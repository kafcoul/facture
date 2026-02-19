<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CreditNoteResource\Pages;
use App\Domain\Invoice\Models\CreditNote;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Notifications\Notification;
use Filament\Tables\Actions\Action;
use Illuminate\Database\Eloquent\Builder;

class CreditNoteResource extends Resource
{
    protected static ?string $model = CreditNote::class;

    protected static ?string $navigationIcon = 'heroicon-o-receipt-refund';

    protected static ?string $navigationLabel = 'Avoirs';

    protected static ?string $navigationGroup = 'Gestion Commerciale';

    protected static ?int $navigationSort = 7;

    protected static ?string $modelLabel = 'Avoir';

    protected static ?string $pluralModelLabel = 'Avoirs';

    protected static ?string $recordTitleAttribute = 'number';

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count() ?: null;
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Informations de l\'avoir')
                    ->schema([
                        Forms\Components\Select::make('client_id')
                            ->relationship('client', 'name')
                            ->required()
                            ->searchable()
                            ->preload()
                            ->label('Client')
                            ->reactive()
                            ->afterStateUpdated(fn (Forms\Set $set) => $set('invoice_id', null)),
                        Forms\Components\Select::make('invoice_id')
                            ->relationship(
                                'invoice',
                                'number',
                                fn (Builder $query, Forms\Get $get) =>
                                    $query->when($get('client_id'), fn ($q, $clientId) =>
                                        $q->where('client_id', $clientId)
                                    )->where('type', 'invoice')
                            )
                            ->searchable()
                            ->preload()
                            ->label('Facture associée')
                            ->nullable()
                            ->placeholder('Aucune (avoir libre)'),
                        Forms\Components\TextInput::make('number')
                            ->label('Numéro')
                            ->placeholder('Auto-généré')
                            ->disabled()
                            ->dehydrated(),
                        Forms\Components\Select::make('reason')
                            ->label('Motif')
                            ->options(CreditNote::REASONS)
                            ->required(),
                        Forms\Components\Select::make('status')
                            ->label('Statut')
                            ->options(CreditNote::STATUSES)
                            ->default('draft')
                            ->required(),
                        Forms\Components\DateTimePicker::make('issued_at')
                            ->label('Date d\'émission')
                            ->default(now()),
                    ])
                    ->columns(3),

                Forms\Components\Section::make('Lignes de l\'avoir')
                    ->schema([
                        Forms\Components\Repeater::make('items')
                            ->label('Lignes')
                            ->schema([
                                Forms\Components\TextInput::make('description')
                                    ->label('Description')
                                    ->required()
                                    ->columnSpan(2),
                                Forms\Components\TextInput::make('quantity')
                                    ->label('Qté')
                                    ->numeric()
                                    ->default(1)
                                    ->minValue(0.01),
                                Forms\Components\TextInput::make('unit_price')
                                    ->label('Prix unitaire')
                                    ->numeric()
                                    ->default(0)
                                    ->suffix('XOF'),
                                Forms\Components\TextInput::make('tax_rate')
                                    ->label('TVA %')
                                    ->numeric()
                                    ->default(18)
                                    ->suffix('%'),
                                Forms\Components\TextInput::make('total')
                                    ->label('Total')
                                    ->numeric()
                                    ->default(0)
                                    ->suffix('XOF'),
                            ])
                            ->columns(6)
                            ->defaultItems(1)
                            ->collapsible()
                            ->reorderable(),
                    ]),

                Forms\Components\Section::make('Montants')
                    ->schema([
                        Forms\Components\TextInput::make('subtotal')
                            ->label('Sous-total HT')
                            ->numeric()
                            ->default(0)
                            ->suffix('XOF'),
                        Forms\Components\TextInput::make('tax')
                            ->label('TVA')
                            ->numeric()
                            ->default(0)
                            ->suffix('XOF'),
                        Forms\Components\TextInput::make('total')
                            ->label('Total TTC')
                            ->numeric()
                            ->default(0)
                            ->suffix('XOF'),
                        Forms\Components\Select::make('currency')
                            ->label('Devise')
                            ->options([
                                'XOF' => 'XOF - Franc CFA',
                                'EUR' => 'EUR - Euro',
                                'USD' => 'USD - Dollar US',
                            ])
                            ->default('XOF'),
                    ])
                    ->columns(4),

                Forms\Components\Section::make('Notes')
                    ->schema([
                        Forms\Components\Textarea::make('notes')
                            ->label('Notes internes')
                            ->rows(2),
                    ])
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
                Tables\Columns\TextColumn::make('invoice.number')
                    ->label('Facture')
                    ->placeholder('Avoir libre')
                    ->sortable(),
                Tables\Columns\BadgeColumn::make('reason')
                    ->label('Motif')
                    ->formatStateUsing(fn (string $state): string => CreditNote::REASONS[$state] ?? $state)
                    ->colors([
                        'danger' => 'error',
                        'warning' => 'return',
                        'info' => 'discount',
                        'secondary' => 'cancellation',
                        'primary' => 'duplicate',
                    ]),
                Tables\Columns\BadgeColumn::make('status')
                    ->label('Statut')
                    ->formatStateUsing(fn (string $state): string => CreditNote::STATUSES[$state] ?? $state)
                    ->colors([
                        'secondary' => 'draft',
                        'info' => 'issued',
                        'success' => 'applied',
                        'danger' => 'cancelled',
                    ]),
                Tables\Columns\TextColumn::make('total')
                    ->label('Montant')
                    ->money(fn ($record) => strtolower($record->currency))
                    ->sortable()
                    ->color('danger'),
                Tables\Columns\TextColumn::make('issued_at')
                    ->label('Émis le')
                    ->dateTime('d/m/Y')
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label('Statut')
                    ->options(CreditNote::STATUSES),
                Tables\Filters\SelectFilter::make('reason')
                    ->label('Motif')
                    ->options(CreditNote::REASONS),
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->label('Modifier'),
                Action::make('issue')
                    ->label('Émettre')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->requiresConfirmation()
                    ->visible(fn (CreditNote $record): bool => $record->status === 'draft')
                    ->action(function (CreditNote $record): void {
                        $record->update([
                            'status' => 'issued',
                            'issued_at' => now(),
                        ]);
                        Notification::make()
                            ->title('Avoir émis')
                            ->success()
                            ->send();
                    }),
                Action::make('apply')
                    ->label('Appliquer')
                    ->icon('heroicon-o-arrow-down-on-square')
                    ->color('info')
                    ->requiresConfirmation()
                    ->modalHeading('Appliquer cet avoir ?')
                    ->modalDescription('Le montant sera déduit de la facture associée.')
                    ->visible(fn (CreditNote $record): bool => $record->status === 'issued' && $record->invoice_id !== null)
                    ->action(function (CreditNote $record): void {
                        $record->update(['status' => 'applied']);
                        Notification::make()
                            ->title('Avoir appliqué')
                            ->success()
                            ->send();
                    }),
                Action::make('cancel')
                    ->label('Annuler')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->visible(fn (CreditNote $record): bool => in_array($record->status, ['draft', 'issued']))
                    ->action(function (CreditNote $record): void {
                        $record->update(['status' => 'cancelled']);
                        Notification::make()
                            ->title('Avoir annulé')
                            ->warning()
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
            'index' => Pages\ListCreditNotes::route('/'),
            'create' => Pages\CreateCreditNote::route('/create'),
            'edit' => Pages\EditCreditNote::route('/{record}/edit'),
        ];
    }
}
