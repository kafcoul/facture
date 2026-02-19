<?php

namespace App\Filament\Pages;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Pages\Page;
use Filament\Actions\Action;
use Filament\Notifications\Notification;

class Settings extends Page implements Forms\Contracts\HasForms
{
    use Forms\Concerns\InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-cog-6-tooth';

    protected static ?string $navigationLabel = 'Paramètres';

    protected static ?string $navigationGroup = 'Configuration';

    protected static ?int $navigationSort = 1;

    protected static ?string $title = 'Paramètres Généraux';

    protected static ?string $slug = 'settings';

    protected static string $view = 'filament.pages.settings';

    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill([
            'app_name' => config('app.name', 'InvoiceSaaS'),
            'app_url' => config('app.url', ''),
            'app_timezone' => config('app.timezone', 'Africa/Dakar'),
            'app_locale' => config('app.locale', 'fr'),
            'default_currency' => 'XOF',
            'default_tax_rate' => 18,
            'invoice_prefix' => 'INV',
            'invoice_due_days' => 30,
            'company_name' => auth()->user()?->company_name ?? '',
            'company_email' => auth()->user()?->email ?? '',
            'company_phone' => auth()->user()?->phone ?? '',
            'company_address' => auth()->user()?->address ?? '',
            'company_tax_id' => auth()->user()?->tax_id ?? '',
        ]);
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Application')
                    ->schema([
                        Forms\Components\TextInput::make('app_name')
                            ->label('Nom de l\'application')
                            ->required(),
                        Forms\Components\TextInput::make('app_url')
                            ->label('URL de l\'application')
                            ->url(),
                        Forms\Components\Select::make('app_timezone')
                            ->label('Fuseau horaire')
                            ->options([
                                'Africa/Dakar' => 'Dakar (GMT+0)',
                                'Africa/Abidjan' => 'Abidjan (GMT+0)',
                                'Africa/Lagos' => 'Lagos (GMT+1)',
                                'Africa/Douala' => 'Douala (GMT+1)',
                                'Europe/Paris' => 'Paris (GMT+1/+2)',
                                'UTC' => 'UTC',
                            ]),
                        Forms\Components\Select::make('app_locale')
                            ->label('Langue')
                            ->options([
                                'fr' => 'Français',
                                'en' => 'English',
                            ]),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Facturation')
                    ->schema([
                        Forms\Components\Select::make('default_currency')
                            ->label('Devise par défaut')
                            ->options([
                                'XOF' => 'XOF - Franc CFA',
                                'EUR' => 'EUR - Euro',
                                'USD' => 'USD - Dollar US',
                            ]),
                        Forms\Components\TextInput::make('default_tax_rate')
                            ->label('Taux de TVA par défaut')
                            ->numeric()
                            ->suffix('%'),
                        Forms\Components\TextInput::make('invoice_prefix')
                            ->label('Préfixe factures')
                            ->maxLength(10),
                        Forms\Components\TextInput::make('invoice_due_days')
                            ->label('Délai de paiement (jours)')
                            ->numeric()
                            ->minValue(1),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Informations entreprise')
                    ->schema([
                        Forms\Components\TextInput::make('company_name')
                            ->label('Nom de l\'entreprise'),
                        Forms\Components\TextInput::make('company_email')
                            ->label('Email')
                            ->email(),
                        Forms\Components\TextInput::make('company_phone')
                            ->label('Téléphone')
                            ->tel(),
                        Forms\Components\TextInput::make('company_tax_id')
                            ->label('Numéro fiscal'),
                        Forms\Components\Textarea::make('company_address')
                            ->label('Adresse')
                            ->columnSpanFull(),
                    ])
                    ->columns(2),
            ])
            ->statePath('data');
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('save')
                ->label('Enregistrer')
                ->action('save')
                ->icon('heroicon-o-check'),
        ];
    }

    public function save(): void
    {
        $data = $this->form->getState();

        // Update user profile info
        $user = auth()->user();
        if ($user) {
            $user->update([
                'company_name' => $data['company_name'] ?? null,
                'phone' => $data['company_phone'] ?? null,
                'address' => $data['company_address'] ?? null,
            ]);
        }

        // Store tenant settings if available
        if ($user && $user->tenant) {
            $user->tenant->setSetting('default_currency', $data['default_currency'] ?? 'XOF');
            $user->tenant->setSetting('default_tax_rate', $data['default_tax_rate'] ?? 18);
            $user->tenant->setSetting('invoice_prefix', $data['invoice_prefix'] ?? 'INV');
            $user->tenant->setSetting('invoice_due_days', $data['invoice_due_days'] ?? 30);
        }

        Notification::make()
            ->title('Paramètres enregistrés')
            ->success()
            ->send();
    }
}
