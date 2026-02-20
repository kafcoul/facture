<?php

namespace App\Filament\Client\Widgets;

use App\Domain\Invoice\Models\Invoice;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class RecentInvoicesWidget extends BaseWidget
{
    protected static ?int $sort = 2;

    protected int|string|array $columnSpan = 'full';

    protected static ?string $heading = 'Mes Dernières Factures';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Invoice::where('tenant_id', auth()->user()?->tenant_id)
                    ->latest()
                    ->limit(10)
            )
            ->columns([
                Tables\Columns\TextColumn::make('invoice_number')
                    ->label('N° Facture')
                    ->searchable()
                    ->weight('bold'),
                Tables\Columns\TextColumn::make('client.name')
                    ->label('Client')
                    ->searchable(),
                Tables\Columns\TextColumn::make('total')
                    ->label('Montant')
                    ->money('XOF')
                    ->sortable(),
                Tables\Columns\BadgeColumn::make('status')
                    ->label('Statut')
                    ->colors([
                        'gray' => 'draft',
                        'info' => 'sent',
                        'warning' => 'viewed',
                        'success' => 'paid',
                        'danger' => 'overdue',
                        'primary' => 'partially_paid',
                    ])
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'draft' => 'Brouillon',
                        'sent' => 'Envoyée',
                        'viewed' => 'Vue',
                        'paid' => 'Payée',
                        'overdue' => 'En retard',
                        'partially_paid' => 'Partiel',
                        'cancelled' => 'Annulée',
                        default => $state,
                    }),
                Tables\Columns\TextColumn::make('due_date')
                    ->label('Échéance')
                    ->date('d/m/Y')
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Créée le')
                    ->date('d/m/Y')
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->paginated(false);
    }
}
