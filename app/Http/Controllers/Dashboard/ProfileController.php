<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class ProfileController extends Controller
{
    public function edit()
    {
        /** @var User $user */
        $user = auth()->user();
        return view('dashboard.profile.edit', compact('user'));
    }

    public function update(Request $request)
    {
        /** @var User $user */
        $user = auth()->user();

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email,' . $user->id],
            'phone' => ['nullable', 'string', 'max:30'],
            'address' => ['nullable', 'string', 'max:500'],
            'company_name' => ['nullable', 'string', 'max:255'],
            'tax_id' => ['nullable', 'string', 'max:100'],
            'wave_number' => ['nullable', 'string', 'max:30'],
            'orange_money_number' => ['nullable', 'string', 'max:30'],
            'momo_number' => ['nullable', 'string', 'max:30'],
            'moov_money_number' => ['nullable', 'string', 'max:30'],
            'current_password' => ['nullable', 'required_with:password'],
            'password' => ['nullable', 'confirmed', Password::defaults()],
        ]);

        // Vérifier le mot de passe actuel si un nouveau est fourni
        if ($request->filled('password')) {
            if (!Hash::check($request->current_password, $user->password)) {
                return back()->withErrors(['current_password' => 'Le mot de passe actuel est incorrect.']);
            }
            $user->password = Hash::make($validated['password']);
        }

        $user->name = $validated['name'];
        $user->email = $validated['email'];
        $user->phone = $validated['phone'] ?? $user->phone;
        $user->address = $validated['address'] ?? $user->address;
        $user->company_name = $validated['company_name'] ?? $user->company_name;
        $user->tax_id = $validated['tax_id'] ?? $user->tax_id;
        $user->wave_number = $validated['wave_number'] ?? $user->wave_number;
        $user->orange_money_number = $validated['orange_money_number'] ?? $user->orange_money_number;
        $user->momo_number = $validated['momo_number'] ?? $user->momo_number;
        $user->moov_money_number = $validated['moov_money_number'] ?? $user->moov_money_number;
        $user->save();

        return back()->with('success', 'Votre profil a été mis à jour avec succès.');
    }
}
