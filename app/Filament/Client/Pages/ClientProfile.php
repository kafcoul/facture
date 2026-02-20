<?php

namespace App\Filament\Client\Pages;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Pages\Page;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Hash;

class ClientProfile extends Page implements Forms\Contracts\HasForms
{
    use Forms\Concerns\InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-user-circle';

    protected static ?string $navigationLabel = 'Mon Profil';

    protected static ?string $navigationGroup = 'Mon Compte';

    protected static ?int $navigationSort = 1;

    protected static ?string $title = 'Mon Profil';

    protected static ?string $slug = 'profile';

    protected static string $view = 'filament.client.pages.profile';

    public ?array $profileData = [];
    public ?array $passwordData = [];

    public function mount(): void
    {
        $user = auth()->user();

        $this->profileForm->fill([
            'name' => $user->name,
            'email' => $user->email,
            'phone' => $user->phone,
            'company_name' => $user->company_name,
            'address' => $user->address,
        ]);
    }

    protected function getForms(): array
    {
        return [
            'profileForm',
            'passwordForm',
        ];
    }

    public function profileForm(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Mes Informations')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->required()
                            ->maxLength(255)
                            ->label('Nom complet'),
                        Forms\Components\TextInput::make('email')
                            ->disabled()
                            ->label('Email'),
                        Forms\Components\TextInput::make('phone')
                            ->tel()
                            ->maxLength(255)
                            ->label('Téléphone'),
                        Forms\Components\TextInput::make('company_name')
                            ->maxLength(255)
                            ->label('Entreprise'),
                        Forms\Components\Textarea::make('address')
                            ->maxLength(500)
                            ->label('Adresse'),
                    ])
                    ->columns(2),
            ])
            ->statePath('profileData');
    }

    public function passwordForm(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Changer le mot de passe')
                    ->schema([
                        Forms\Components\TextInput::make('current_password')
                            ->password()
                            ->required()
                            ->label('Mot de passe actuel'),
                        Forms\Components\TextInput::make('new_password')
                            ->password()
                            ->required()
                            ->minLength(8)
                            ->label('Nouveau mot de passe'),
                        Forms\Components\TextInput::make('new_password_confirmation')
                            ->password()
                            ->required()
                            ->same('new_password')
                            ->label('Confirmer le mot de passe'),
                    ])
                    ->columns(1),
            ])
            ->statePath('passwordData');
    }

    public function updateProfile(): void
    {
        $data = $this->profileForm->getState();

        auth()->user()->update([
            'name' => $data['name'],
            'phone' => $data['phone'],
            'company_name' => $data['company_name'],
            'address' => $data['address'],
        ]);

        Notification::make()
            ->title('Profil mis à jour')
            ->success()
            ->send();
    }

    public function updatePassword(): void
    {
        $data = $this->passwordForm->getState();

        if (!Hash::check($data['current_password'], auth()->user()->password)) {
            Notification::make()
                ->title('Mot de passe actuel incorrect')
                ->danger()
                ->send();
            return;
        }

        auth()->user()->update([
            'password' => Hash::make($data['new_password']),
        ]);

        $this->passwordForm->fill();

        Notification::make()
            ->title('Mot de passe mis à jour')
            ->success()
            ->send();
    }
}
