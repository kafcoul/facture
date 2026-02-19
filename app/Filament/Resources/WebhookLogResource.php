<?php

namespace App\Filament\Resources;

use App\Filament\Resources\WebhookLogResource\Pages;
use App\Models\WebhookLog;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class WebhookLogResource extends Resource
{
    protected static ?string $model = WebhookLog::class;

    protected static ?string $navigationIcon = 'heroicon-o-globe-alt';

    protected static ?string $navigationLabel = 'Webhooks';

    protected static ?string $navigationGroup = 'Administration';

    protected static ?int $navigationSort = 4;

    protected static ?string $modelLabel = 'Webhook';

    protected static ?string $pluralModelLabel = 'Webhooks';

    protected static ?string $recordTitleAttribute = 'event_type';

    public static function getNavigationBadge(): ?string
    {
        $failed = static::getModel()::where('processed', false)->count();
        return $failed > 0 ? (string) $failed : null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return static::getModel()::where('processed', false)->count() > 0 ? 'danger' : 'success';
    }

    public static function canCreate(): bool
    {
        return false;
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Détails du Webhook')
                    ->schema([
                        Forms\Components\TextInput::make('gateway')
                            ->label('Passerelle')
                            ->disabled(),
                        Forms\Components\TextInput::make('event_type')
                            ->label('Type d\'événement')
                            ->disabled(),
                        Forms\Components\TextInput::make('ip_address')
                            ->label('Adresse IP')
                            ->disabled(),
                        Forms\Components\Toggle::make('processed')
                            ->label('Traité')
                            ->disabled(),
                    ])
                    ->columns(2),
                Forms\Components\Section::make('Payload')
                    ->schema([
                        Forms\Components\KeyValue::make('payload')
                            ->label('Données reçues')
                            ->disabled(),
                    ]),
                Forms\Components\Section::make('Réponse')
                    ->schema([
                        Forms\Components\KeyValue::make('response')
                            ->label('Réponse du système')
                            ->disabled(),
                    ])
                    ->visible(fn ($record) => !empty($record?->response)),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\BadgeColumn::make('gateway')
                    ->label('Passerelle')
                    ->colors([
                        'primary' => 'stripe',
                        'success' => 'paystack',
                        'warning' => 'flutterwave',
                        'info' => 'wave',
                        'danger' => 'mpesa',
                    ])
                    ->formatStateUsing(fn (string $state): string => ucfirst($state))
                    ->sortable(),
                Tables\Columns\TextColumn::make('event_type')
                    ->label('Événement')
                    ->searchable()
                    ->sortable()
                    ->wrap()
                    ->weight('bold'),
                Tables\Columns\IconColumn::make('processed')
                    ->label('Traité')
                    ->boolean()
                    ->sortable(),
                Tables\Columns\TextColumn::make('ip_address')
                    ->label('IP')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Reçu le')
                    ->dateTime('d/m/Y H:i:s')
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('gateway')
                    ->label('Passerelle')
                    ->options([
                        'stripe' => 'Stripe',
                        'paystack' => 'Paystack',
                        'flutterwave' => 'Flutterwave',
                        'wave' => 'Wave',
                        'mpesa' => 'M-Pesa',
                        'fedapay' => 'FedaPay',
                        'kkiapay' => 'KKiaPay',
                        'cinetpay' => 'CinetPay',
                    ]),
                Tables\Filters\TernaryFilter::make('processed')
                    ->label('Statut')
                    ->trueLabel('Traités')
                    ->falseLabel('Échoués'),
                Tables\Filters\Filter::make('recent')
                    ->label('Dernières 24h')
                    ->query(fn (Builder $query): Builder =>
                        $query->where('created_at', '>=', now()->subDay())
                    ),
                Tables\Filters\Filter::make('this_week')
                    ->label('Cette semaine')
                    ->query(fn (Builder $query): Builder =>
                        $query->where('created_at', '>=', now()->startOfWeek())
                    ),
            ])
            ->actions([
                Tables\Actions\ViewAction::make()
                    ->label('Voir'),
                Tables\Actions\Action::make('reprocess')
                    ->label('Retraiter')
                    ->icon('heroicon-o-arrow-path')
                    ->color('warning')
                    ->requiresConfirmation()
                    ->visible(fn (WebhookLog $record): bool => !$record->processed)
                    ->action(function (WebhookLog $record): void {
                        $record->update(['processed' => true]);
                        \Filament\Notifications\Notification::make()
                            ->title('Webhook marqué comme traité')
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
            'index' => Pages\ListWebhookLogs::route('/'),
            'view' => Pages\ViewWebhookLog::route('/{record}'),
        ];
    }
}
