<?php

namespace App\Filament\Pages;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Pages\Page;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Hash;

class Profile extends Page implements Forms\Contracts\HasForms
{
    use Forms\Concerns\InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-user-circle';

    protected static ?string $navigationLabel = 'Mon Profil';

    protected static ?string $navigationGroup = 'Configuration';

    protected static ?int $navigationSort = 2;

    protected static ?string $title = 'Mon Profil';

    protected static ?string $slug = 'profile';

    protected static string $view = 'filament.pages.profile';

    public ?array $profileData = [];
    public ?array $passwordData = [];
    public ?array $paymentData = [];

    public function mount(): void
    {
        $user = auth()->user();

        $this->profileForm->fill([
            'name' => $user->name,
            'email' => $user->email,
            'phone' => $user->phone,
            'company_name' => $user->company_name,
            'address' => $user->address,
            'tax_id' => $user->tax_id,
            'invoice_template' => $user->invoice_template ?? 'default',
        ]);

        $this->paymentForm->fill([
            'wave_number' => $user->wave_number,
            'orange_money_number' => $user->orange_money_number,
            'momo_number' => $user->momo_number,
            'moov_money_number' => $user->moov_money_number,
        ]);
    }

    protected function getForms(): array
    {
        return [
            'profileForm',
            'passwordForm',
            'paymentForm',
        ];
    }

    public function profileForm(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Informations personnelles')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->required()
                            ->maxLength(255)
                            ->label('Nom complet'),
                        Forms\Components\TextInput::make('email')
                            ->required()
                            ->email()
                            ->maxLength(255)
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
                            ->maxLength(65535)
                            ->label('Adresse')
                            ->columnSpanFull(),
                        Forms\Components\TextInput::make('tax_id')
                            ->maxLength(255)
                            ->label('Numéro fiscal'),
                        Forms\Components\Select::make('invoice_template')
                            ->options([
                                'default' => 'Par défaut',
                                'modern' => 'Moderne',
                                'classic' => 'Classique',
                                'minimal' => 'Minimaliste',
                            ])
                            ->label('Template de facture'),
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
                            ->label('Mot de passe actuel')
                            ->currentPassword(),
                        Forms\Components\TextInput::make('new_password')
                            ->password()
                            ->required()
                            ->minLength(8)
                            ->label('Nouveau mot de passe')
                            ->confirmed(),
                        Forms\Components\TextInput::make('new_password_confirmation')
                            ->password()
                            ->required()
                            ->label('Confirmer le mot de passe'),
                    ])
                    ->columns(1),
            ])
            ->statePath('passwordData');
    }

    public function paymentForm(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Numéros de paiement mobile')
                    ->schema([
                        Forms\Components\TextInput::make('wave_number')
                            ->label('Numéro Wave'),
                        Forms\Components\TextInput::make('orange_money_number')
                            ->label('Numéro Orange Money'),
                        Forms\Components\TextInput::make('momo_number')
                            ->label('Numéro MoMo'),
                        Forms\Components\TextInput::make('moov_money_number')
                            ->label('Numéro Moov Money'),
                    ])
                    ->columns(2),
            ])
            ->statePath('paymentData');
    }

    public function saveProfile(): void
    {
        $data = $this->profileForm->getState();

        auth()->user()->update([
            'name' => $data['name'],
            'phone' => $data['phone'] ?? null,
            'company_name' => $data['company_name'] ?? null,
            'address' => $data['address'] ?? null,
            'tax_id' => $data['tax_id'] ?? null,
            'invoice_template' => $data['invoice_template'] ?? 'default',
        ]);

        Notification::make()
            ->title('Profil mis à jour')
            ->success()
            ->send();
    }

    public function savePassword(): void
    {
        $data = $this->passwordForm->getState();

        auth()->user()->update([
            'password' => Hash::make($data['new_password']),
        ]);

        $this->passwordData = [];

        Notification::make()
            ->title('Mot de passe changé')
            ->success()
            ->send();
    }

    public function savePayment(): void
    {
        $data = $this->paymentForm->getState();

        auth()->user()->update([
            'wave_number' => $data['wave_number'] ?? null,
            'orange_money_number' => $data['orange_money_number'] ?? null,
            'momo_number' => $data['momo_number'] ?? null,
            'moov_money_number' => $data['moov_money_number'] ?? null,
        ]);

        Notification::make()
            ->title('Numéros de paiement mis à jour')
            ->success()
            ->send();
    }
}
