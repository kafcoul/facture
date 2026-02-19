<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TenantResource\Pages;
use App\Domain\Tenant\Models\Tenant;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class TenantResource extends Resource
{
    protected static ?string $model = Tenant::class;

    protected static ?string $navigationIcon = 'heroicon-o-building-office-2';

    protected static ?string $navigationLabel = 'Tenants';

    protected static ?string $navigationGroup = 'Administration';

    protected static ?int $navigationSort = 1;

    protected static ?string $modelLabel = 'Tenant';

    protected static ?string $pluralModelLabel = 'Tenants';

    protected static ?string $recordTitleAttribute = 'name';

    public static function getGloballySearchableAttributes(): array
    {
        return ['name', 'slug', 'domain'];
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count() ?: null;
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Informations du tenant')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->required()
                            ->maxLength(255)
                            ->label('Nom'),
                        Forms\Components\TextInput::make('slug')
                            ->required()
                            ->maxLength(255)
                            ->unique(ignoreRecord: true)
                            ->label('Slug'),
                        Forms\Components\TextInput::make('domain')
                            ->maxLength(255)
                            ->label('Domaine'),
                    ])
                    ->columns(3),

                Forms\Components\Section::make('Abonnement')
                    ->schema([
                        Forms\Components\Select::make('plan')
                            ->options([
                                'starter' => 'Starter',
                                'pro' => 'Pro',
                                'enterprise' => 'Enterprise',
                            ])
                            ->default('starter')
                            ->label('Plan'),
                        Forms\Components\DateTimePicker::make('trial_ends_at')
                            ->label('Fin de l\'essai'),
                        Forms\Components\DateTimePicker::make('expires_at')
                            ->label('Expiration'),
                    ])
                    ->columns(3),

                Forms\Components\Section::make('État')
                    ->schema([
                        Forms\Components\Toggle::make('is_active')
                            ->default(true)
                            ->label('Actif'),
                        Forms\Components\KeyValue::make('settings')
                            ->label('Paramètres')
                            ->columnSpanFull(),
                    ]),
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
                Tables\Columns\TextColumn::make('slug')
                    ->searchable()
                    ->color('gray')
                    ->label('Slug'),
                Tables\Columns\TextColumn::make('plan')
                    ->badge()
                    ->color(fn (?string $state): string => match ($state) {
                        'enterprise' => 'success',
                        'pro' => 'info',
                        default => 'gray',
                    })
                    ->label('Plan'),
                Tables\Columns\TextColumn::make('users_count')
                    ->counts('users')
                    ->badge()
                    ->color('gray')
                    ->label('Utilisateurs'),
                Tables\Columns\TextColumn::make('invoices_count')
                    ->counts('invoices')
                    ->badge()
                    ->color('gray')
                    ->label('Factures'),
                Tables\Columns\IconColumn::make('is_active')
                    ->boolean()
                    ->sortable()
                    ->label('Actif'),
                Tables\Columns\TextColumn::make('trial_ends_at')
                    ->date('d/m/Y')
                    ->placeholder('—')
                    ->sortable()
                    ->label('Fin essai'),
                Tables\Columns\TextColumn::make('created_at')
                    ->date('d/m/Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->label('Créé le'),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
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
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
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
            'index' => Pages\ListTenants::route('/'),
            'create' => Pages\CreateTenant::route('/create'),
            'edit' => Pages\EditTenant::route('/{record}/edit'),
        ];
    }
}
