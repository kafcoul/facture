<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-users';

    protected static ?string $navigationLabel = 'Utilisateurs';

    protected static ?string $navigationGroup = 'Administration';

    protected static ?int $navigationSort = 2;

    protected static ?string $modelLabel = 'Utilisateur';

    protected static ?string $pluralModelLabel = 'Utilisateurs';

    protected static ?string $recordTitleAttribute = 'name';

    public static function getGloballySearchableAttributes(): array
    {
        return ['name', 'email', 'company_name', 'phone'];
    }

    public static function getNavigationBadge(): ?string
    {
        $tenantId = auth()->user()?->tenant_id;
        if (!$tenantId) return null;

        return static::getModel()::where('tenant_id', $tenantId)->count() ?: null;
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->where('tenant_id', auth()->user()?->tenant_id);
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Identité')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->required()
                            ->maxLength(255)
                            ->label('Nom complet'),
                        Forms\Components\TextInput::make('email')
                            ->required()
                            ->email()
                            ->maxLength(255)
                            ->unique(ignoreRecord: true)
                            ->label('Email'),
                        Forms\Components\TextInput::make('phone')
                            ->tel()
                            ->maxLength(255)
                            ->label('Téléphone'),
                        Forms\Components\TextInput::make('company_name')
                            ->maxLength(255)
                            ->label('Entreprise'),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Accès & Rôle')
                    ->schema([
                        Forms\Components\TextInput::make('password')
                            ->password()
                            ->maxLength(255)
                            ->dehydrateStateUsing(fn (?string $state) => $state ? bcrypt($state) : null)
                            ->dehydrated(fn (?string $state) => filled($state))
                            ->required(fn (string $operation): bool => $operation === 'create')
                            ->label('Mot de passe')
                            ->helperText('Laisser vide pour ne pas modifier'),
                        Forms\Components\Select::make('role')
                            ->options([
                                'admin' => 'Administrateur',
                                'user' => 'Utilisateur',
                                'accountant' => 'Comptable',
                            ])
                            ->default('user')
                            ->required()
                            ->label('Rôle'),
                        Forms\Components\Select::make('tenant_id')
                            ->relationship('tenant', 'name')
                            ->searchable()
                            ->preload()
                            ->label('Tenant'),
                        Forms\Components\Toggle::make('is_active')
                            ->default(true)
                            ->label('Compte actif'),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Abonnement')
                    ->schema([
                        Forms\Components\Select::make('plan')
                            ->options([
                                'starter' => 'Starter',
                                'pro' => 'Pro',
                                'enterprise' => 'Enterprise',
                            ])
                            ->label('Plan'),
                        Forms\Components\DateTimePicker::make('trial_ends_at')
                            ->label('Fin de l\'essai'),
                        Forms\Components\Select::make('invoice_template')
                            ->options([
                                'default' => 'Par défaut',
                                'modern' => 'Moderne',
                                'classic' => 'Classique',
                                'minimal' => 'Minimaliste',
                            ])
                            ->label('Template facture'),
                    ])
                    ->columns(3)
                    ->collapsible(),

                Forms\Components\Section::make('Paiements Mobiles')
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
                    ->columns(2)
                    ->collapsed(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->sortable()
                    ->weight('bold')
                    ->label('Nom'),
                Tables\Columns\TextColumn::make('email')
                    ->searchable()
                    ->sortable()
                    ->copyable()
                    ->icon('heroicon-m-envelope')
                    ->label('Email'),
                Tables\Columns\TextColumn::make('tenant.name')
                    ->sortable()
                    ->placeholder('—')
                    ->label('Tenant'),
                Tables\Columns\TextColumn::make('role')
                    ->badge()
                    ->color(fn (?string $state): string => match ($state) {
                        'admin' => 'danger',
                        'accountant' => 'warning',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (?string $state): string => match ($state) {
                        'admin' => 'Admin',
                        'user' => 'Utilisateur',
                        'accountant' => 'Comptable',
                        default => $state ?? '—',
                    })
                    ->label('Rôle'),
                Tables\Columns\TextColumn::make('plan')
                    ->badge()
                    ->color(fn (?string $state): string => match ($state) {
                        'enterprise' => 'success',
                        'pro' => 'info',
                        default => 'gray',
                    })
                    ->placeholder('—')
                    ->label('Plan'),
                Tables\Columns\IconColumn::make('is_active')
                    ->boolean()
                    ->sortable()
                    ->label('Actif'),
                Tables\Columns\TextColumn::make('last_login_at')
                    ->dateTime('d/m/Y H:i')
                    ->placeholder('Jamais')
                    ->sortable()
                    ->label('Dernière connexion'),
                Tables\Columns\TextColumn::make('created_at')
                    ->date('d/m/Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->label('Inscrit le'),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('role')
                    ->options([
                        'admin' => 'Administrateur',
                        'user' => 'Utilisateur',
                        'accountant' => 'Comptable',
                    ])
                    ->label('Rôle'),
                Tables\Filters\SelectFilter::make('plan')
                    ->options([
                        'starter' => 'Starter',
                        'pro' => 'Pro',
                        'enterprise' => 'Enterprise',
                    ])
                    ->label('Plan'),
                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('Statut')
                    ->trueLabel('Actifs')
                    ->falseLabel('Inactifs'),
                Tables\Filters\SelectFilter::make('tenant')
                    ->relationship('tenant', 'name')
                    ->searchable()
                    ->preload()
                    ->label('Tenant'),
            ])
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\ViewAction::make(),
                    Tables\Actions\EditAction::make(),
                    Tables\Actions\Action::make('toggle_active')
                        ->label(fn (User $record) => $record->is_active ? 'Désactiver' : 'Activer')
                        ->icon(fn (User $record) => $record->is_active ? 'heroicon-o-no-symbol' : 'heroicon-o-check-circle')
                        ->color(fn (User $record) => $record->is_active ? 'danger' : 'success')
                        ->requiresConfirmation()
                        ->action(fn (User $record) => $record->update(['is_active' => !$record->is_active])),
                    Tables\Actions\DeleteAction::make(),
                ]),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }
}
