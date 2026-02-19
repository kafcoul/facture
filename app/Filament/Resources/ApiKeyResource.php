<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ApiKeyResource\Pages;
use App\Models\ApiKey;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Notifications\Notification;
use Filament\Tables\Actions\Action;
use Illuminate\Database\Eloquent\Builder;

class ApiKeyResource extends Resource
{
    protected static ?string $model = ApiKey::class;

    protected static ?string $navigationIcon = 'heroicon-o-key';

    protected static ?string $navigationLabel = 'Clés API';

    protected static ?string $navigationGroup = 'Administration';

    protected static ?int $navigationSort = 5;

    protected static ?string $modelLabel = 'Clé API';

    protected static ?string $pluralModelLabel = 'Clés API';

    protected static ?string $recordTitleAttribute = 'name';

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::active()->count() ?: null;
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Informations de la clé')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label('Nom')
                            ->required()
                            ->maxLength(255)
                            ->placeholder('Ex: Application Mobile'),
                        Forms\Components\Select::make('user_id')
                            ->relationship('user', 'name')
                            ->required()
                            ->searchable()
                            ->preload()
                            ->label('Utilisateur'),
                        Forms\Components\TextInput::make('key')
                            ->label('Clé API')
                            ->disabled()
                            ->placeholder('Générée automatiquement')
                            ->dehydrated(false)
                            ->visibleOn('edit'),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Configuration')
                    ->schema([
                        Forms\Components\CheckboxList::make('permissions')
                            ->label('Permissions')
                            ->options([
                                '*' => 'Toutes les permissions',
                                'invoices.*' => 'Factures (CRUD)',
                                'invoices.view' => 'Factures (lecture seule)',
                                'clients.*' => 'Clients (CRUD)',
                                'clients.view' => 'Clients (lecture seule)',
                                'products.*' => 'Produits (CRUD)',
                                'products.view' => 'Produits (lecture seule)',
                                'payments.view' => 'Paiements (lecture seule)',
                            ])
                            ->columns(2),
                        Forms\Components\TextInput::make('rate_limit_per_minute')
                            ->label('Limite de requêtes/min')
                            ->numeric()
                            ->default(60)
                            ->minValue(1)
                            ->maxValue(1000),
                        Forms\Components\DateTimePicker::make('expires_at')
                            ->label('Date d\'expiration')
                            ->nullable()
                            ->placeholder('Jamais'),
                        Forms\Components\Toggle::make('is_active')
                            ->label('Active')
                            ->default(true),
                    ])
                    ->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Nom')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),
                Tables\Columns\TextColumn::make('user.name')
                    ->label('Utilisateur')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('masked_key')
                    ->label('Clé')
                    ->getStateUsing(fn (ApiKey $record): string => $record->masked_key)
                    ->fontFamily('mono')
                    ->copyable()
                    ->copyableState(fn (ApiKey $record): string => $record->key),
                Tables\Columns\TextColumn::make('usage_count')
                    ->label('Utilisations')
                    ->sortable()
                    ->formatStateUsing(fn (int $state): string => number_format($state, 0, ',', ' ')),
                Tables\Columns\TextColumn::make('rate_limit_per_minute')
                    ->label('Limite/min')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('last_used_at')
                    ->label('Dernière utilisation')
                    ->dateTime('d/m/Y H:i')
                    ->placeholder('Jamais')
                    ->sortable(),
                Tables\Columns\IconColumn::make('is_active')
                    ->label('Active')
                    ->boolean()
                    ->sortable(),
                Tables\Columns\TextColumn::make('expires_at')
                    ->label('Expire le')
                    ->dateTime('d/m/Y')
                    ->placeholder('Jamais')
                    ->color(fn (ApiKey $record): string =>
                        $record->expires_at && $record->expires_at->isPast()
                            ? 'danger'
                            : 'success'
                    )
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Créée le')
                    ->date('d/m/Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('Statut')
                    ->trueLabel('Actives')
                    ->falseLabel('Révoquées'),
                Tables\Filters\Filter::make('expired')
                    ->label('Expirées')
                    ->query(fn (Builder $query): Builder =>
                        $query->whereNotNull('expires_at')
                            ->where('expires_at', '<=', now())
                    ),
                Tables\Filters\Filter::make('never_used')
                    ->label('Jamais utilisées')
                    ->query(fn (Builder $query): Builder =>
                        $query->whereNull('last_used_at')
                    ),
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->label('Modifier'),
                Action::make('revoke')
                    ->label('Révoquer')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->modalHeading('Révoquer cette clé API ?')
                    ->modalDescription('La clé ne pourra plus être utilisée pour accéder à l\'API.')
                    ->visible(fn (ApiKey $record): bool => $record->is_active)
                    ->action(function (ApiKey $record): void {
                        $record->revoke();
                        Notification::make()
                            ->title('Clé API révoquée')
                            ->body("La clé \"{$record->name}\" a été révoquée.")
                            ->warning()
                            ->send();
                    }),
                Action::make('reactivate')
                    ->label('Réactiver')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->requiresConfirmation()
                    ->visible(fn (ApiKey $record): bool => !$record->is_active)
                    ->action(function (ApiKey $record): void {
                        $record->update(['is_active' => true]);
                        Notification::make()
                            ->title('Clé API réactivée')
                            ->success()
                            ->send();
                    }),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListApiKeys::route('/'),
            'create' => Pages\CreateApiKey::route('/create'),
            'edit' => Pages\EditApiKey::route('/{record}/edit'),
        ];
    }
}
