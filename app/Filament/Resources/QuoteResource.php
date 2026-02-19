<?php

namespace App\Filament\Resources;

use App\Filament\Resources\QuoteResource\Pages;
use App\Models\Invoice;
use App\Domain\Invoice\Models\InvoiceItem;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Notifications\Notification;
use Filament\Tables\Actions\Action;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;

class QuoteResource extends Resource
{
    protected static ?string $model = Invoice::class;

    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-list';

    protected static ?string $navigationLabel = 'Devis';

    protected static ?string $navigationGroup = 'Gestion Commerciale';

    protected static ?int $navigationSort = 6;

    protected static ?string $modelLabel = 'Devis';

    protected static ?string $pluralModelLabel = 'Devis';

    protected static ?string $slug = 'quotes';

    public static function getNavigationBadge(): ?string
    {
        return static::getEloquentQuery()->count() ?: null;
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->where('type', 'quote');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Informations du Devis')
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
                            ->label('Numéro du devis')
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
                            ->label('Valide jusqu\'au'),
                        Forms\Components\Select::make('status')
                            ->options([
                                'draft' => 'Brouillon',
                                'sent' => 'Envoyé',
                                'viewed' => 'Vu',
                                'paid' => 'Accepté',
                                'cancelled' => 'Refusé',
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
                    ->columns(3),

                Forms\Components\Section::make('Lignes du devis')
                    ->schema([
                        Forms\Components\Repeater::make('items')
                            ->relationship()
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
                                                $set('unit_price', $product->unit_price ?? $product->price);
                                                $set('tax_rate', $product->tax_rate ?? 18);
                                            }
                                        }
                                    }),
                                Forms\Components\TextInput::make('description')
                                    ->required()
                                    ->label('Description')
                                    ->columnSpan(2),
                                Forms\Components\TextInput::make('quantity')
                                    ->numeric()
                                    ->default(1)
                                    ->required()
                                    ->label('Quantité')
                                    ->minValue(0.01),
                                Forms\Components\TextInput::make('unit_price')
                                    ->numeric()
                                    ->required()
                                    ->label('Prix unitaire')
                                    ->suffix('XOF'),
                                Forms\Components\TextInput::make('tax_rate')
                                    ->numeric()
                                    ->default(18)
                                    ->label('TVA %')
                                    ->suffix('%'),
                            ])
                            ->columns(6)
                            ->label('Lignes')
                            ->defaultItems(1)
                            ->collapsible()
                            ->reorderable(),
                    ]),

                Forms\Components\Section::make('Totaux')
                    ->schema([
                        Forms\Components\TextInput::make('subtotal')
                            ->numeric()
                            ->default(0)
                            ->label('Sous-total HT')
                            ->suffix('XOF'),
                        Forms\Components\TextInput::make('tax')
                            ->numeric()
                            ->default(0)
                            ->label('TVA')
                            ->suffix('XOF'),
                        Forms\Components\TextInput::make('discount')
                            ->numeric()
                            ->default(0)
                            ->label('Remise')
                            ->suffix('XOF'),
                        Forms\Components\TextInput::make('total')
                            ->numeric()
                            ->default(0)
                            ->label('Total TTC')
                            ->suffix('XOF'),
                    ])
                    ->columns(4),

                Forms\Components\Section::make('Notes & Conditions')
                    ->schema([
                        Forms\Components\Textarea::make('notes')
                            ->label('Notes')
                            ->rows(2),
                        Forms\Components\Textarea::make('terms')
                            ->label('Conditions')
                            ->rows(2),
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
                    ->label('Numéro')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),
                Tables\Columns\TextColumn::make('client.name')
                    ->label('Client')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\BadgeColumn::make('status')
                    ->label('Statut')
                    ->colors([
                        'secondary' => 'draft',
                        'info' => 'sent',
                        'warning' => 'viewed',
                        'success' => 'paid',
                        'danger' => 'cancelled',
                    ])
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'draft' => '📝 Brouillon',
                        'sent' => '📤 Envoyé',
                        'viewed' => '👁️ Vu',
                        'paid' => '✅ Accepté',
                        'cancelled' => '❌ Refusé',
                        default => $state,
                    })
                    ->sortable(),
                Tables\Columns\TextColumn::make('total')
                    ->label('Montant')
                    ->money(fn ($record) => strtolower($record->currency))
                    ->sortable(),
                Tables\Columns\TextColumn::make('issued_at')
                    ->label('Date')
                    ->date('d/m/Y')
                    ->sortable(),
                Tables\Columns\TextColumn::make('due_date')
                    ->label('Valide jusqu\'au')
                    ->date('d/m/Y')
                    ->sortable()
                    ->color(fn ($record): string =>
                        $record->due_date && $record->due_date->isPast() ? 'danger' : 'success'
                    ),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label('Statut')
                    ->options([
                        'draft' => 'Brouillon',
                        'sent' => 'Envoyé',
                        'viewed' => 'Vu',
                        'paid' => 'Accepté',
                        'cancelled' => 'Refusé',
                    ]),
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->label('Modifier'),
                Action::make('convert_to_invoice')
                    ->label('Convertir en facture')
                    ->icon('heroicon-o-document-arrow-down')
                    ->color('success')
                    ->requiresConfirmation()
                    ->modalHeading('Convertir ce devis en facture ?')
                    ->modalDescription('Une nouvelle facture sera créée à partir de ce devis. Le devis sera marqué comme "Accepté".')
                    ->visible(fn (Invoice $record): bool => in_array($record->status, ['draft', 'sent', 'viewed']))
                    ->action(function (Invoice $record): void {
                        // Créer la facture depuis le devis
                        $invoice = $record->replicate();
                        $invoice->type = 'invoice';
                        $invoice->status = 'draft';
                        $invoice->uuid = (string) Str::uuid();
                        $invoice->public_hash = Str::random(32);
                        $invoice->number = null; // sera auto-généré
                        $invoice->issued_at = now();
                        $invoice->due_date = now()->addDays(30);
                        $invoice->metadata = array_merge($record->metadata ?? [], [
                            'converted_from_quote' => $record->id,
                            'original_quote_number' => $record->number,
                        ]);
                        $invoice->save();

                        // Copier les lignes
                        foreach ($record->items as $item) {
                            $newItem = $item->replicate();
                            $newItem->invoice_id = $invoice->id;
                            $newItem->save();
                        }

                        // Marquer le devis comme accepté
                        $record->update(['status' => 'paid']);

                        Notification::make()
                            ->title('Devis converti en facture')
                            ->body("Facture {$invoice->number} créée avec succès.")
                            ->success()
                            ->send();
                    }),
                Action::make('send_quote')
                    ->label('Envoyer')
                    ->icon('heroicon-o-paper-airplane')
                    ->color('info')
                    ->visible(fn (Invoice $record): bool => $record->status === 'draft')
                    ->action(function (Invoice $record): void {
                        $record->update(['status' => 'sent']);
                        Notification::make()
                            ->title('Devis envoyé')
                            ->success()
                            ->send();
                    }),
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
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListQuotes::route('/'),
            'create' => Pages\CreateQuote::route('/create'),
            'edit' => Pages\EditQuote::route('/{record}/edit'),
        ];
    }
}
