<?php

namespace App\Filament\Client\Resources;

use App\Filament\Client\Resources\InvoiceResource\Pages;
use App\Domain\Invoice\Models\Invoice;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class InvoiceResource extends Resource
{
    protected static ?string $model = Invoice::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static ?string $navigationLabel = 'Mes Factures';

    protected static ?string $navigationGroup = 'Mes Documents';

    protected static ?int $navigationSort = 1;

    protected static ?string $modelLabel = 'Facture';

    protected static ?string $pluralModelLabel = 'Factures';

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
                Forms\Components\Section::make('Détails de la facture')
                    ->schema([
                        Forms\Components\TextInput::make('invoice_number')
                            ->label('N° Facture')
                            ->disabled(),
                        Forms\Components\TextInput::make('status')
                            ->label('Statut')
                            ->disabled(),
                        Forms\Components\TextInput::make('total')
                            ->label('Montant Total')
                            ->disabled(),
                        Forms\Components\TextInput::make('due_date')
                            ->label('Date d\'échéance')
                            ->disabled(),
                    ])
                    ->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('invoice_number')
                    ->label('N° Facture')
                    ->searchable()
                    ->sortable()
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
                Tables\Columns\TextColumn::make('issue_date')
                    ->label('Date émission')
                    ->date('d/m/Y')
                    ->sortable(),
                Tables\Columns\TextColumn::make('due_date')
                    ->label('Échéance')
                    ->date('d/m/Y')
                    ->sortable()
                    ->color(fn (Invoice $record): string =>
                        $record->status === 'overdue' ? 'danger' : 'gray'
                    ),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label('Statut')
                    ->options([
                        'draft' => 'Brouillon',
                        'sent' => 'Envoyée',
                        'viewed' => 'Vue',
                        'paid' => 'Payée',
                        'overdue' => 'En retard',
                        'partially_paid' => 'Partiel',
                        'cancelled' => 'Annulée',
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
            'index' => Pages\ListInvoices::route('/'),
            'view' => Pages\ViewInvoice::route('/{record}'),
        ];
    }
}
