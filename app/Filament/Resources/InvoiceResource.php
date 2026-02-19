<?php

namespace App\Filament\Resources;

use App\Filament\Resources\InvoiceResource\Pages;
use App\Models\Invoice;
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

    protected static ?string $navigationLabel = 'Factures';

    protected static ?string $navigationGroup = 'Gestion Commerciale';

    protected static ?int $navigationSort = 3;

    protected static ?string $modelLabel = 'Facture';

    protected static ?string $pluralModelLabel = 'Factures';

    protected static ?string $recordTitleAttribute = 'number';

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->where('type', 'invoice');
    }

    public static function getGloballySearchableAttributes(): array
    {
        return ['number', 'client.name', 'notes'];
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getEloquentQuery()->count() ?: null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        $overdue = static::getEloquentQuery()->where('status', 'overdue')->count();
        return $overdue > 0 ? 'danger' : 'primary';
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Informations Facture')
                    ->schema([
                        Forms\Components\Select::make('client_id')
                            ->relationship('client', 'name')
                            ->required()
                            ->searchable()
                            ->preload()
                            ->label('Client')
                            ->createOptionForm([
                                Forms\Components\TextInput::make('name')->required()->label('Nom'),
                                Forms\Components\TextInput::make('email')->email()->required()->label('Email'),
                                Forms\Components\TextInput::make('phone')->label('Téléphone'),
                            ]),
                        Forms\Components\TextInput::make('number')
                            ->maxLength(255)
                            ->label('Numéro de facture')
                            ->placeholder('Auto-généré')
                            ->disabled()
                            ->dehydrated(),
                        Forms\Components\DatePicker::make('issued_at')
                            ->required()
                            ->default(now())
                            ->label('Date d\'émission'),
                        Forms\Components\DatePicker::make('due_date')
                            ->required()
                            ->default(now()->addDays(30))
                            ->afterOrEqual('issued_at')
                            ->label('Date d\'échéance'),
                        Forms\Components\Select::make('status')
                            ->options([
                                'draft' => 'Brouillon',
                                'sent' => 'Envoyée',
                                'viewed' => 'Vue',
                                'paid' => 'Payée',
                                'overdue' => 'En retard',
                                'cancelled' => 'Annulée',
                                'partially_paid' => 'Partiellement payée',
                            ])
                            ->default('draft')
                            ->required()
                            ->label('Statut'),
                        Forms\Components\Select::make('currency')
                            ->options([
                                'XOF' => 'XOF - Franc CFA',
                                'EUR' => 'EUR - Euro',
                                'USD' => 'USD - Dollar US',
                            ])
                            ->default('XOF')
                            ->label('Devise'),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Lignes de Facture')
                    ->schema([
                        Forms\Components\Repeater::make('items')
                            ->relationship('items')
                            ->schema([
                                Forms\Components\Select::make('product_id')
                                    ->relationship('product', 'name')
                                    ->searchable()
                                    ->preload()
                                    ->label('Produit')
                                    ->reactive()
                                    ->afterStateUpdated(function ($state, Forms\Set $set) {
                                        if ($state) {
                                            $product = \App\Models\Product::find($state);
                                            if ($product) {
                                                $set('description', $product->name);
                                                $set('unit_price', $product->unit_price);
                                                $set('tax_rate', $product->tax_rate ?? 18);
                                            }
                                        }
                                    }),
                                Forms\Components\TextInput::make('description')
                                    ->required()
                                    ->maxLength(255)
                                    ->label('Description')
                                    ->columnSpan(2),
                                Forms\Components\TextInput::make('quantity')
                                    ->required()
                                    ->numeric()
                                    ->default(1)
                                    ->minValue(0.01)
                                    ->label('Qté'),
                                Forms\Components\TextInput::make('unit_price')
                                    ->required()
                                    ->numeric()
                                    ->prefix('XOF')
                                    ->label('Prix unit.'),
                                Forms\Components\TextInput::make('tax_rate')
                                    ->numeric()
                                    ->default(18)
                                    ->suffix('%')
                                    ->label('TVA'),
                                Forms\Components\TextInput::make('discount')
                                    ->numeric()
                                    ->default(0)
                                    ->suffix('%')
                                    ->label('Remise'),
                            ])
                            ->columns(7)
                            ->defaultItems(1)
                            ->addActionLabel('Ajouter une ligne')
                            ->reorderable()
                            ->collapsible()
                            ->cloneable(),
                    ]),

                Forms\Components\Section::make('Récapitulatif')
                    ->schema([
                        Forms\Components\TextInput::make('subtotal')
                            ->numeric()
                            ->prefix('XOF')
                            ->disabled()
                            ->dehydrated()
                            ->label('Sous-total HT'),
                        Forms\Components\TextInput::make('tax')
                            ->numeric()
                            ->prefix('XOF')
                            ->disabled()
                            ->dehydrated()
                            ->label('TVA'),
                        Forms\Components\TextInput::make('discount')
                            ->numeric()
                            ->prefix('XOF')
                            ->default(0)
                            ->label('Remise globale'),
                        Forms\Components\TextInput::make('total')
                            ->numeric()
                            ->prefix('XOF')
                            ->disabled()
                            ->dehydrated()
                            ->label('Total TTC')
                            ->extraAttributes(['class' => 'font-bold']),
                    ])
                    ->columns(4)
                    ->collapsible(),

                Forms\Components\Section::make('Notes & Conditions')
                    ->schema([
                        Forms\Components\Textarea::make('notes')
                            ->maxLength(65535)
                            ->label('Notes'),
                        Forms\Components\Textarea::make('terms')
                            ->maxLength(65535)
                            ->label('Conditions de paiement'),
                    ])
                    ->columns(2)
                    ->collapsed(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('number')
                    ->searchable()
                    ->sortable()
                    ->weight('bold')
                    ->copyable()
                    ->label('Numéro'),
                Tables\Columns\TextColumn::make('client.name')
                    ->searchable()
                    ->sortable()
                    ->label('Client'),
                Tables\Columns\TextColumn::make('issued_at')
                    ->date('d/m/Y')
                    ->sortable()
                    ->label('Émission'),
                Tables\Columns\TextColumn::make('due_date')
                    ->date('d/m/Y')
                    ->sortable()
                    ->color(fn ($record) => $record->due_date && $record->due_date->isPast() && $record->status !== 'paid' ? 'danger' : null)
                    ->label('Échéance'),
                Tables\Columns\TextColumn::make('total')
                    ->money('XOF')
                    ->sortable()
                    ->weight('bold')
                    ->label('Total'),
                Tables\Columns\TextColumn::make('status')
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
                    })
                    ->label('Statut'),
                Tables\Columns\TextColumn::make('items_count')
                    ->counts('items')
                    ->label('Lignes')
                    ->badge()
                    ->color('gray')
                    ->toggleable(isToggledHiddenByDefault: true),
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
                        'draft' => 'Brouillon',
                        'sent' => 'Envoyée',
                        'viewed' => 'Vue',
                        'paid' => 'Payée',
                        'overdue' => 'En retard',
                        'cancelled' => 'Annulée',
                        'partially_paid' => 'Partiellement payée',
                    ])
                    ->multiple()
                    ->label('Statut'),
                Tables\Filters\SelectFilter::make('client')
                    ->relationship('client', 'name')
                    ->searchable()
                    ->preload()
                    ->label('Client'),
                Tables\Filters\Filter::make('overdue')
                    ->query(fn (Builder $query) => $query->where('status', 'overdue'))
                    ->label('En retard uniquement')
                    ->toggle(),
                Tables\Filters\Filter::make('due_this_month')
                    ->query(fn (Builder $query) => $query
                        ->whereBetween('due_date', [now()->startOfMonth(), now()->endOfMonth()])
                        ->whereNotIn('status', ['paid', 'cancelled']))
                    ->label('Échéance ce mois')
                    ->toggle(),
            ])
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\ViewAction::make(),
                    Tables\Actions\EditAction::make(),
                    Tables\Actions\Action::make('mark_sent')
                        ->label('Marquer envoyée')
                        ->icon('heroicon-o-paper-airplane')
                        ->color('info')
                        ->requiresConfirmation()
                        ->action(fn (Invoice $record) => $record->update(['status' => 'sent']))
                        ->visible(fn (Invoice $record) => $record->status === 'draft'),
                    Tables\Actions\Action::make('mark_paid')
                        ->label('Marquer payée')
                        ->icon('heroicon-o-check-circle')
                        ->color('success')
                        ->requiresConfirmation()
                        ->action(fn (Invoice $record) => $record->markAsPaid())
                        ->visible(fn (Invoice $record) => in_array($record->status, ['sent', 'viewed', 'overdue', 'partially_paid'])),
                    Tables\Actions\Action::make('download_pdf')
                        ->label('Télécharger PDF')
                        ->icon('heroicon-o-arrow-down-tray')
                        ->color('gray')
                        ->url(fn (Invoice $record) => route('invoices.download', $record->uuid ?? $record->id))
                        ->openUrlInNewTab(),
                    Tables\Actions\Action::make('duplicate')
                        ->label('Dupliquer')
                        ->icon('heroicon-o-document-duplicate')
                        ->color('gray')
                        ->requiresConfirmation()
                        ->action(function (Invoice $record) {
                            $newInvoice = $record->replicate(['number', 'uuid', 'public_hash', 'paid_at']);
                            $newInvoice->status = 'draft';
                            $newInvoice->issued_at = now();
                            $newInvoice->due_date = now()->addDays(30);
                            $newInvoice->save();

                            foreach ($record->items as $item) {
                                $newItem = $item->replicate(['invoice_id']);
                                $newItem->invoice_id = $newInvoice->id;
                                $newItem->save();
                            }
                        }),
                    Tables\Actions\Action::make('cancel')
                        ->label('Annuler')
                        ->icon('heroicon-o-x-circle')
                        ->color('danger')
                        ->requiresConfirmation()
                        ->action(fn (Invoice $record) => $record->update(['status' => 'cancelled']))
                        ->visible(fn (Invoice $record) => !in_array($record->status, ['paid', 'cancelled'])),
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
            'index' => Pages\ListInvoices::route('/'),
            'create' => Pages\CreateInvoice::route('/create'),
            'edit' => Pages\EditInvoice::route('/{record}/edit'),
        ];
    }
}
