<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use PragmaRX\Google2FA\Google2FA;
use BaconQrCode\Renderer\ImageRenderer;
use BaconQrCode\Renderer\Image\SvgImageBackEnd;
use BaconQrCode\Renderer\RendererStyle\RendererStyle;
use BaconQrCode\Writer;

class TwoFactorController extends Controller
{
    /**
     * Afficher la page d'activation du 2FA
     */
    public function enable(Request $request)
    {
        $user = Auth::user();

        // Si déjà activé, rediriger vers les settings
        if ($user->two_factor_secret) {
            return redirect()->route('client.settings.index')->with('info', 'L\'authentification à deux facteurs est déjà activée.');
        }

        // Générer un nouveau secret
        $google2fa = new Google2FA();
        $secret = $google2fa->generateSecretKey();

        // Stocker temporairement dans la session
        session(['two_factor_secret' => $secret]);

        // Générer le QR code
        $qrCodeUrl = $google2fa->getQRCodeUrl(
            config('app.name'),
            $user->email,
            $secret
        );

        $writer = new Writer(
            new ImageRenderer(
                new RendererStyle(200),
                new SvgImageBackEnd()
            )
        );

        $qrCodeSvg = $writer->writeString($qrCodeUrl);

        return view('dashboard.settings.two-factor.enable', [
            'secret' => $secret,
            'qrCodeSvg' => $qrCodeSvg,
        ]);
    }

    /**
     * Confirmer l'activation du 2FA
     */
    public function confirm(Request $request)
    {
        $request->validate([
            'code' => 'required|string|size:6',
        ]);

        /** @var User $user */
        $user = Auth::user();
        $secret = session('two_factor_secret');

        if (!$secret) {
            return redirect()->route('client.settings.index')->with('error', 'Session expirée. Veuillez réessayer.');
        }

        // Vérifier le code
        $google2fa = new Google2FA();
        $valid = $google2fa->verifyKey($secret, $request->code);

        if (!$valid) {
            throw ValidationException::withMessages([
                'code' => 'Le code de vérification est invalide.',
            ]);
        }

        // Sauvegarder le secret
        $user->two_factor_secret = encrypt($secret);
        $user->two_factor_confirmed_at = now();

        // Générer des codes de récupération
        $recoveryCodes = $this->generateRecoveryCodes();
        $user->two_factor_recovery_codes = encrypt(json_encode($recoveryCodes));

        $user->save();

        // Nettoyer la session
        session()->forget('two_factor_secret');

        return view('dashboard.settings.two-factor.recovery-codes', [
            'recoveryCodes' => $recoveryCodes,
        ]);
    }

    /**
     * Désactiver le 2FA
     */
    public function disable(Request $request)
    {
        $request->validate([
            'password' => 'required|string',
        ]);

        /** @var User $user */
        $user = Auth::user();

        // Vérifier le mot de passe
        if (!Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'password' => 'Le mot de passe est incorrect.',
            ]);
        }

        // Désactiver le 2FA
        $user->two_factor_secret = null;
        $user->two_factor_recovery_codes = null;
        $user->two_factor_confirmed_at = null;
        $user->save();

        return redirect()->route('client.settings.index')->with('success', 'L\'authentification à deux facteurs a été désactivée.');
    }

    /**
     * Afficher les codes de récupération
     */
    public function showRecoveryCodes()
    {
        $user = Auth::user();

        if (!$user->two_factor_secret) {
            return redirect()->route('client.settings.index')->with('error', 'L\'authentification à deux facteurs n\'est pas activée.');
        }

        $recoveryCodes = json_decode(decrypt($user->two_factor_recovery_codes), true);

        return view('dashboard.settings.two-factor.recovery-codes', [
            'recoveryCodes' => $recoveryCodes,
        ]);
    }

    /**
     * Régénérer les codes de récupération
     */
    public function regenerateRecoveryCodes(Request $request)
    {
        $request->validate([
            'password' => 'required|string',
        ]);

        /** @var User $user */
        $user = Auth::user();

        // Vérifier le mot de passe
        if (!Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'password' => 'Le mot de passe est incorrect.',
            ]);
        }

        if (!$user->two_factor_secret) {
            return redirect()->route('client.settings.index')->with('error', 'L\'authentification à deux facteurs n\'est pas activée.');
        }

        // Générer de nouveaux codes
        $recoveryCodes = $this->generateRecoveryCodes();
        $user->two_factor_recovery_codes = encrypt(json_encode($recoveryCodes));
        $user->save();

        return view('dashboard.settings.two-factor.recovery-codes', [
            'recoveryCodes' => $recoveryCodes,
            'regenerated' => true,
        ]);
    }

    /**
     * Générer des codes de récupération
     */
    protected function generateRecoveryCodes(): array
    {
        $codes = [];

        for ($i = 0; $i < 8; $i++) {
            $codes[] = strtoupper(bin2hex(random_bytes(5)));
        }

        return $codes;
    }
}
