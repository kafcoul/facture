<x-filament-panels::page>
    <div class="space-y-6">
        <form wire:submit="updateProfile">
            {{ $this->profileForm }}

            <div class="mt-4">
                <x-filament::button type="submit">
                    Sauvegarder le profil
                </x-filament::button>
            </div>
        </form>

        <form wire:submit="updatePassword">
            {{ $this->passwordForm }}

            <div class="mt-4">
                <x-filament::button type="submit" color="warning">
                    Changer le mot de passe
                </x-filament::button>
            </div>
        </form>
    </div>
</x-filament-panels::page>
