<?php

namespace App\Filament\Client\Resources;

use App\Filament\Client\Resources\PaymentResource\Pages;
use App\Domain\Payment\Models\Payment;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class PaymentResource extends Resource
{
    protected static ?string $model = Payment::class;

    protected static ?string $navigationIcon = 'heroicon-o-banknotes';

    protected static ?string $navigationLabel = 'Mes Paiements';

    protected static ?string $navigationGroup = 'Mes Documents';

    protected static ?int $navigationSort = 2;

    protected static ?string $modelLabel = 'Paiement';

    protected static ?string $pluralModelLabel = 'Paiements';

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->where('tenant_id', auth()->user()?->tenant_id);
    }

    public static function canCreate(): bool
    {
        return false;
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Détails du paiement')
                    ->schema([
                        Forms\Components\TextInput::make('amount')
                            ->label('Montant')
                            ->disabled(),
                        Forms\Components\TextInput::make('status')
                            ->label('Statut')
                            ->disabled(),
                        Forms\Components\TextInput::make('gateway')
                            ->label('Passerelle')
                            ->disabled(),
                        Forms\Components\TextInput::make('transaction_id')
                            ->label('ID Transaction')
                            ->disabled(),
                    ])
                    ->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('invoice.invoice_number')
                    ->label('N° Facture')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('amount')
                    ->label('Montant')
                    ->money('XOF')
                    ->sortable(),
                Tables\Columns\BadgeColumn::make('status')
                    ->label('Statut')
                    ->colors([
                        'warning' => 'pending',
                        'success' => 'completed',
                        'danger' => 'failed',
                        'gray' => 'refunded',
                    ])
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'pending' => 'En attente',
                        'completed' => 'Complété',
                        'failed' => 'Échoué',
                        'refunded' => 'Remboursé',
                        default => $state,
                    }),
                Tables\Columns\TextColumn::make('gateway')
                    ->label('Passerelle')
                    ->formatStateUsing(fn (?string $state): string => match ($state) {
                        'stripe' => 'Stripe',
                        'wave' => 'Wave',
                        'orange_money' => 'Orange Money',
                        'momo' => 'MTN MoMo',
                        'bank_transfer' => 'Virement',
                        'cash' => 'Espèces',
                        default => $state ?? '—',
                    }),
                Tables\Columns\TextColumn::make('completed_at')
                    ->label('Date')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label('Statut')
                    ->options([
                        'pending' => 'En attente',
                        'completed' => 'Complété',
                        'failed' => 'Échoué',
                        'refunded' => 'Remboursé',
                    ]),
            ])
            ->actions([
                Tables\Actions\ViewAction::make()
                    ->label('Voir'),
            ])
            ->bulkActions([]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPayments::route('/'),
        ];
    }
}
