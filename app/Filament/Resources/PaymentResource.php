<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PaymentResource\Pages;
use App\Models\Payment;
use App\Services\PaymentService;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class PaymentResource extends Resource
{
    protected static ?string $model = Payment::class;

    protected static ?string $navigationIcon = 'heroicon-o-credit-card';

    protected static ?string $navigationLabel = 'Paiements';

    protected static ?string $navigationGroup = 'Gestion Commerciale';

    protected static ?int $navigationSort = 4;

    protected static ?string $modelLabel = 'Paiement';

    protected static ?string $pluralModelLabel = 'Paiements';

    protected static ?string $recordTitleAttribute = 'transaction_id';

    public static function getGloballySearchableAttributes(): array
    {
        return ['transaction_id', 'invoice.number', 'invoice.client.name'];
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::where('status', 'pending')->count() ?: null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'warning';
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Informations du paiement')
                    ->schema([
                        Forms\Components\Select::make('invoice_id')
                            ->relationship('invoice', 'number')
                            ->required()
                            ->searchable()
                            ->preload()
                            ->label('Facture'),
                        Forms\Components\TextInput::make('amount')
                            ->required()
                            ->numeric()
                            ->prefix('XOF')
                            ->label('Montant'),
                        Forms\Components\Select::make('currency')
                            ->options([
                                'XOF' => 'XOF - Franc CFA',
                                'EUR' => 'EUR - Euro',
                                'USD' => 'USD - Dollar US',
                            ])
                            ->default('XOF')
                            ->label('Devise'),
                        Forms\Components\Select::make('status')
                            ->options([
                                'pending' => 'En attente',
                                'completed' => 'Complété',
                                'failed' => 'Échoué',
                            ])
                            ->default('pending')
                            ->required()
                            ->label('Statut'),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Méthode de paiement')
                    ->schema([
                        Forms\Components\Select::make('gateway')
                            ->options([
                                'stripe' => 'Stripe',
                                'paypal' => 'PayPal',
                                'orange_money' => 'Orange Money',
                                'mtn_money' => 'MTN Money',
                                'wave' => 'Wave',
                                'bank_transfer' => 'Virement bancaire',
                                'cash' => 'Espèces',
                                'check' => 'Chèque',
                                'other' => 'Autre',
                            ])
                            ->label('Passerelle'),
                        Forms\Components\TextInput::make('payment_method')
                            ->maxLength(255)
                            ->label('Méthode')
                            ->placeholder('Ex: Carte Visa, Mobile Money...'),
                        Forms\Components\TextInput::make('transaction_id')
                            ->maxLength(255)
                            ->label('ID Transaction')
                            ->unique(ignoreRecord: true),
                    ])
                    ->columns(3),

                Forms\Components\Section::make('Dates & Détails')
                    ->schema([
                        Forms\Components\DateTimePicker::make('completed_at')
                            ->label('Date de paiement'),
                        Forms\Components\DateTimePicker::make('failed_at')
                            ->label('Date d\'échec')
                            ->visible(fn (Forms\Get $get) => $get('status') === 'failed'),
                        Forms\Components\Textarea::make('failure_reason')
                            ->maxLength(65535)
                            ->label('Raison de l\'échec')
                            ->visible(fn (Forms\Get $get) => $get('status') === 'failed'),
                    ])
                    ->columns(2)
                    ->collapsible(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('invoice.number')
                    ->searchable()
                    ->sortable()
                    ->weight('bold')
                    ->label('Facture'),
                Tables\Columns\TextColumn::make('invoice.client.name')
                    ->searchable()
                    ->sortable()
                    ->label('Client'),
                Tables\Columns\TextColumn::make('amount')
                    ->money('XOF')
                    ->sortable()
                    ->weight('bold')
                    ->label('Montant'),
                Tables\Columns\TextColumn::make('gateway')
                    ->formatStateUsing(fn (?string $state): string => match ($state) {
                        'stripe' => 'Stripe',
                        'paypal' => 'PayPal',
                        'orange_money' => 'Orange Money',
                        'mtn_money' => 'MTN Money',
                        'wave' => 'Wave',
                        'bank_transfer' => 'Virement',
                        'cash' => 'Espèces',
                        'check' => 'Chèque',
                        default => $state ?? '—',
                    })
                    ->label('Passerelle'),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'pending' => 'warning',
                        'completed' => 'success',
                        'failed' => 'danger',
                        'refunded' => 'info',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'pending' => 'En attente',
                        'completed' => 'Complété',
                        'failed' => 'Échoué',
                        'refunded' => 'Remboursé',
                        default => $state,
                    })
                    ->label('Statut'),
                Tables\Columns\TextColumn::make('transaction_id')
                    ->searchable()
                    ->copyable()
                    ->placeholder('—')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->label('ID Transaction'),
                Tables\Columns\TextColumn::make('completed_at')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->placeholder('—')
                    ->label('Payé le'),
                Tables\Columns\TextColumn::make('created_at')
                    ->date('d/m/Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->label('Créé le'),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'pending' => 'En attente',
                        'completed' => 'Complété',
                        'failed' => 'Échoué',
                    ])
                    ->label('Statut'),
                Tables\Filters\SelectFilter::make('gateway')
                    ->options([
                        'stripe' => 'Stripe',
                        'paypal' => 'PayPal',
                        'orange_money' => 'Orange Money',
                        'mtn_money' => 'MTN Money',
                        'wave' => 'Wave',
                        'bank_transfer' => 'Virement',
                        'cash' => 'Espèces',
                        'check' => 'Chèque',
                    ])
                    ->label('Passerelle'),
                Tables\Filters\Filter::make('pending_only')
                    ->query(fn (Builder $query) => $query->where('status', 'pending'))
                    ->label('En attente uniquement')
                    ->toggle(),
            ])
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\ViewAction::make(),
                    Tables\Actions\EditAction::make(),
                    Tables\Actions\Action::make('mark_completed')
                        ->label('Marquer complété')
                        ->icon('heroicon-o-check-circle')
                        ->color('success')
                        ->requiresConfirmation()
                        ->action(fn (Payment $record) => $record->markAsCompleted())
                        ->visible(fn (Payment $record) => $record->status === 'pending'),
                    Tables\Actions\Action::make('mark_failed')
                        ->label('Marquer échoué')
                        ->icon('heroicon-o-x-circle')
                        ->color('danger')
                        ->requiresConfirmation()
                        ->form([
                            Forms\Components\Textarea::make('failure_reason')
                                ->label('Raison de l\'échec')
                                ->required(),
                        ])
                        ->action(fn (Payment $record, array $data) => $record->markAsFailed($data['failure_reason']))
                        ->visible(fn (Payment $record) => $record->status === 'pending'),
                    Tables\Actions\Action::make('refund')
                        ->label('Rembourser')
                        ->icon('heroicon-o-arrow-uturn-left')
                        ->color('warning')
                        ->requiresConfirmation()
                        ->modalHeading('Rembourser le paiement')
                        ->modalDescription('Cette action est irréversible. Le remboursement sera effectué via la passerelle de paiement.')
                        ->form([
                            Forms\Components\TextInput::make('refund_amount')
                                ->label('Montant à rembourser (0 = total)')
                                ->numeric()
                                ->default(0)
                                ->minValue(0)
                                ->prefix('XOF'),
                            Forms\Components\Textarea::make('refund_reason')
                                ->label('Raison du remboursement')
                                ->required(),
                        ])
                        ->action(function (Payment $record, array $data) {
                            try {
                                $service = app(PaymentService::class);
                                $result = $service->refundPayment(
                                    $record,
                                    (float) ($data['refund_amount'] ?? 0),
                                    $data['refund_reason'] ?? '',
                                );

                                if ($result['success']) {
                                    Notification::make()
                                        ->title('Remboursement effectué')
                                        ->body($result['message'])
                                        ->success()
                                        ->send();
                                } else {
                                    Notification::make()
                                        ->title('Échec du remboursement')
                                        ->body($result['message'])
                                        ->danger()
                                        ->send();
                                }
                            } catch (\Exception $e) {
                                Notification::make()
                                    ->title('Erreur')
                                    ->body($e->getMessage())
                                    ->danger()
                                    ->send();
                            }
                        })
                        ->visible(fn (Payment $record) => $record->status === 'completed' && $record->gateway && $record->transaction_id),
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
            'index' => Pages\ListPayments::route('/'),
            'create' => Pages\CreatePayment::route('/create'),
            'edit' => Pages\EditPayment::route('/{record}/edit'),
        ];
    }
}
