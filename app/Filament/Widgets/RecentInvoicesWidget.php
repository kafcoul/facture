<?php

namespace App\Filament\Widgets;

use App\Domain\Invoice\Models\Invoice;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class RecentInvoicesWidget extends BaseWidget
{
    protected static ?string $heading = 'Factures récentes';

    protected static ?int $sort = 4;

    protected int|string|array $columnSpan = 'full';

    protected static ?string $pollingInterval = '30s';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Invoice::with('client')->latest()->limit(10)
            )
            ->columns([
                Tables\Columns\TextColumn::make('number')
                    ->label('N°')
                    ->searchable()
                    ->weight('bold')
                    ->color('primary'),
                Tables\Columns\TextColumn::make('client.name')
                    ->label('Client')
                    ->searchable(),
                Tables\Columns\TextColumn::make('total')
                    ->label('Montant')
                    ->money('XOF')
                    ->sortable(),
                Tables\Columns\TextColumn::make('status')
                    ->label('Statut')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'draft' => 'gray',
                        'sent' => 'info',
                        'viewed' => 'purple',
                        'paid' => 'success',
                        'overdue' => 'danger',
                        'cancelled' => 'gray',
                        'partially_paid' => 'warning',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'draft' => 'Brouillon',
                        'sent' => 'Envoyée',
                        'viewed' => 'Vue',
                        'paid' => 'Payée',
                        'overdue' => 'En retard',
                        'cancelled' => 'Annulée',
                        'partially_paid' => 'Partiel',
                        default => $state,
                    }),
                Tables\Columns\TextColumn::make('due_date')
                    ->label('Échéance')
                    ->date('d/m/Y')
                    ->color(fn (Invoice $record): string =>
                        $record->due_date && $record->due_date->isPast() && $record->status !== 'paid'
                            ? 'danger' : 'gray'
                    ),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Créée le')
                    ->date('d/m/Y')
                    ->sortable(),
            ])
            ->actions([
                Tables\Actions\Action::make('view')
                    ->label('Voir')
                    ->icon('heroicon-o-eye')
                    ->url(fn (Invoice $record): string =>
                        \App\Filament\Resources\InvoiceResource::getUrl('edit', ['record' => $record])
                    ),
            ])
            ->paginated(false);
    }
}
