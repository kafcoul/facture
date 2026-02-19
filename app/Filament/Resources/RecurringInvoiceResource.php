<?php

namespace App\Filament\Resources;

use App\Filament\Resources\RecurringInvoiceResource\Pages;
use App\Domain\Invoice\Models\RecurringInvoice;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Notifications\Notification;
use Filament\Tables\Actions\Action;
use Illuminate\Database\Eloquent\Builder;

class RecurringInvoiceResource extends Resource
{
    protected static ?string $model = RecurringInvoice::class;

    protected static ?string $navigationIcon = 'heroicon-o-arrow-path';

    protected static ?string $navigationLabel = 'Factures Récurrentes';

    protected static ?string $navigationGroup = 'Gestion Commerciale';

    protected static ?int $navigationSort = 5;

    protected static ?string $modelLabel = 'Facture Récurrente';

    protected static ?string $pluralModelLabel = 'Factures Récurrentes';

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::active()->count() ?: null;
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Configuration de la récurrence')
                    ->schema([
                        Forms\Components\Select::make('client_id')
                            ->relationship('client', 'name')
                            ->required()
                            ->searchable()
                            ->preload()
                            ->label('Client'),
                        Forms\Components\Select::make('frequency')
                            ->label('Fréquence')
                            ->options(RecurringInvoice::FREQUENCIES)
                            ->required()
                            ->default('monthly'),
                        Forms\Components\DatePicker::make('start_date')
                            ->label('Date de début')
                            ->required()
                            ->default(now()),
                        Forms\Components\DatePicker::make('end_date')
                            ->label('Date de fin')
                            ->nullable()
                            ->afterOrEqual('start_date')
                            ->placeholder('Indéfinie'),
                        Forms\Components\DatePicker::make('next_due_date')
                            ->label('Prochaine échéance')
                            ->required()
                            ->default(now()->addMonth()),
                        Forms\Components\TextInput::make('occurrences_limit')
                            ->label('Nombre max de factures')
                            ->numeric()
                            ->nullable()
                            ->placeholder('Illimité')
                            ->minValue(1),
                    ])
                    ->columns(3),

                Forms\Components\Section::make('Montants')
                    ->schema([
                        Forms\Components\TextInput::make('subtotal')
                            ->label('Sous-total HT')
                            ->numeric()
                            ->required()
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
                            ->required()
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

                Forms\Components\Section::make('Lignes de facture (template)')
                    ->schema([
                        Forms\Components\Repeater::make('items')
                            ->label('Produits/Services')
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

                Forms\Components\Section::make('Options')
                    ->schema([
                        Forms\Components\Toggle::make('is_active')
                            ->label('Active')
                            ->default(true),
                        Forms\Components\Toggle::make('auto_send')
                            ->label('Envoi automatique par email')
                            ->default(false)
                            ->helperText('Envoyer automatiquement la facture au client par email'),
                        Forms\Components\Textarea::make('notes')
                            ->label('Notes')
                            ->rows(2),
                        Forms\Components\Textarea::make('terms')
                            ->label('Conditions')
                            ->rows(2),
                    ])
                    ->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('client.name')
                    ->label('Client')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),
                Tables\Columns\BadgeColumn::make('frequency')
                    ->label('Fréquence')
                    ->formatStateUsing(fn (string $state): string => RecurringInvoice::FREQUENCIES[$state] ?? $state)
                    ->colors([
                        'info' => 'weekly',
                        'primary' => 'monthly',
                        'warning' => 'quarterly',
                        'success' => 'yearly',
                    ]),
                Tables\Columns\TextColumn::make('total')
                    ->label('Montant')
                    ->money(fn ($record) => strtolower($record->currency))
                    ->sortable(),
                Tables\Columns\TextColumn::make('next_due_date')
                    ->label('Prochaine facture')
                    ->date('d/m/Y')
                    ->sortable()
                    ->color(fn ($record): string =>
                        $record->next_due_date->isPast() ? 'danger' :
                        ($record->next_due_date->diffInDays(now()) <= 7 ? 'warning' : 'success')
                    ),
                Tables\Columns\TextColumn::make('occurrences_count')
                    ->label('Générées')
                    ->formatStateUsing(fn ($state, $record): string =>
                        $record->occurrences_limit
                            ? "{$state}/{$record->occurrences_limit}"
                            : (string) $state
                    )
                    ->sortable(),
                Tables\Columns\IconColumn::make('auto_send')
                    ->label('Auto-envoi')
                    ->boolean(),
                Tables\Columns\IconColumn::make('is_active')
                    ->label('Active')
                    ->boolean()
                    ->sortable(),
                Tables\Columns\TextColumn::make('last_generated_at')
                    ->label('Dernière génération')
                    ->dateTime('d/m/Y H:i')
                    ->placeholder('Jamais')
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('next_due_date', 'asc')
            ->filters([
                Tables\Filters\SelectFilter::make('frequency')
                    ->label('Fréquence')
                    ->options(RecurringInvoice::FREQUENCIES),
                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('Statut')
                    ->trueLabel('Actives')
                    ->falseLabel('Inactives'),
                Tables\Filters\Filter::make('due_soon')
                    ->label('À générer bientôt')
                    ->query(fn (Builder $query): Builder =>
                        $query->where('next_due_date', '<=', now()->addDays(7))
                            ->where('is_active', true)
                    ),
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->label('Modifier'),
                Action::make('generate_now')
                    ->label('Générer maintenant')
                    ->icon('heroicon-o-document-plus')
                    ->color('success')
                    ->requiresConfirmation()
                    ->modalHeading('Générer une facture maintenant ?')
                    ->modalDescription('Une facture sera créée immédiatement à partir de ce modèle récurrent.')
                    ->visible(fn (RecurringInvoice $record): bool => $record->canGenerate())
                    ->action(function (RecurringInvoice $record): void {
                        \Illuminate\Support\Facades\Artisan::call('invoices:generate-recurring');
                        Notification::make()
                            ->title('Facture générée')
                            ->body('La facture a été créée avec succès.')
                            ->success()
                            ->send();
                    }),
                Action::make('toggle_active')
                    ->label(fn (RecurringInvoice $record): string =>
                        $record->is_active ? 'Suspendre' : 'Réactiver')
                    ->icon(fn (RecurringInvoice $record): string =>
                        $record->is_active ? 'heroicon-o-pause' : 'heroicon-o-play')
                    ->color(fn (RecurringInvoice $record): string =>
                        $record->is_active ? 'warning' : 'success')
                    ->requiresConfirmation()
                    ->action(function (RecurringInvoice $record): void {
                        $record->update(['is_active' => !$record->is_active]);
                        $status = $record->is_active ? 'réactivée' : 'suspendue';
                        Notification::make()
                            ->title("Récurrence {$status}")
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
            'index' => Pages\ListRecurringInvoices::route('/'),
            'create' => Pages\CreateRecurringInvoice::route('/create'),
            'edit' => Pages\EditRecurringInvoice::route('/{record}/edit'),
        ];
    }
}
