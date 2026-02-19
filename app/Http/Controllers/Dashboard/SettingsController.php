<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class SettingsController extends Controller
{
    public function index()
    {
        return view('dashboard.settings.index');
    }

    public function update(Request $request)
    {
        /** @var User $user */
        $user = auth()->user();

        $validated = $request->validate([
            // Entreprise
            'company_name' => 'nullable|string|max:255',
            'tax_id' => 'nullable|string|max:100',
            'address' => 'nullable|string|max:500',
            'phone' => 'nullable|string|max:50',
            
            // Paiement Mobile
            'wave_number' => 'nullable|string|max:50',
            'orange_money_number' => 'nullable|string|max:50',
            'momo_number' => 'nullable|string|max:50',
            'moov_money_number' => 'nullable|string|max:50',
            
            // Template
            'invoice_template' => ['nullable', 'string', Rule::in($this->getAllowedTemplates($user->plan))],
            
            // Mot de passe
            'current_password' => 'nullable|required_with:new_password|current_password',
            'new_password' => 'nullable|min:8|confirmed',
        ]);

        // Mise à jour des informations de l'entreprise
        $user->company_name = $validated['company_name'] ?? $user->company_name;
        $user->tax_id = $validated['tax_id'] ?? $user->tax_id;
        $user->address = $validated['address'] ?? $user->address;
        $user->phone = $validated['phone'] ?? $user->phone;

        // Mise à jour des numéros de paiement mobile
        $user->wave_number = $validated['wave_number'] ?? $user->wave_number;
        $user->orange_money_number = $validated['orange_money_number'] ?? $user->orange_money_number;
        $user->momo_number = $validated['momo_number'] ?? $user->momo_number;
        $user->moov_money_number = $validated['moov_money_number'] ?? $user->moov_money_number;

        // Mise à jour du template
        if (isset($validated['invoice_template'])) {
            $user->invoice_template = $validated['invoice_template'];
        }

        // Mise à jour du mot de passe
        if (!empty($validated['new_password'])) {
            $user->password = Hash::make($validated['new_password']);
        }

        $user->save();

        return redirect()->route('client.settings.index')
            ->with('success', 'Paramètres mis à jour avec succès !');
    }

    /**
     * Obtenir les templates autorisés pour un plan
     */
    private function getAllowedTemplates(?string $plan): array
    {
        return match($plan) {
            'enterprise' => ['starter', 'pro', 'pro-minimal', 'pro-bold', 'enterprise', 'enterprise-dark', 'enterprise-minimal'],
            'pro' => ['starter', 'pro', 'pro-minimal', 'pro-bold'],
            default => ['starter'],
        };
    }
}
