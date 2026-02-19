<x-filament-panels::page>
    <div class="space-y-6">
        {{-- Profile Form --}}
        <form wire:submit="saveProfile">
            {{ $this->profileForm }}
            <div class="mt-4 flex justify-end">
                <x-filament::button type="submit">
                    Enregistrer le profil
                </x-filament::button>
            </div>
        </form>

        {{-- Password Form --}}
        <form wire:submit="savePassword">
            {{ $this->passwordForm }}
            <div class="mt-4 flex justify-end">
                <x-filament::button type="submit" color="warning">
                    Changer le mot de passe
                </x-filament::button>
            </div>
        </form>

        {{-- Payment Numbers Form --}}
        <form wire:submit="savePayment">
            {{ $this->paymentForm }}
            <div class="mt-4 flex justify-end">
                <x-filament::button type="submit">
                    Enregistrer les numéros
                </x-filament::button>
            </div>
        </form>
    </div>
</x-filament-panels::page>
