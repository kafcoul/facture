<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProductResource\Pages;
use App\Models\Product;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class ProductResource extends Resource
{
    protected static ?string $model = Product::class;

    protected static ?string $navigationIcon = 'heroicon-o-cube';

    protected static ?string $navigationLabel = 'Produits';

    protected static ?string $navigationGroup = 'Gestion Commerciale';

    protected static ?int $navigationSort = 2;

    protected static ?string $modelLabel = 'Produit';

    protected static ?string $pluralModelLabel = 'Produits';

    protected static ?string $recordTitleAttribute = 'name';

    public static function getGloballySearchableAttributes(): array
    {
        return ['name', 'description', 'sku'];
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count() ?: null;
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Informations produit')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->required()
                            ->maxLength(255)
                            ->label('Nom du produit'),
                        Forms\Components\TextInput::make('sku')
                            ->label('SKU / Référence')
                            ->maxLength(255)
                            ->unique(ignoreRecord: true),
                        Forms\Components\Textarea::make('description')
                            ->maxLength(65535)
                            ->columnSpanFull()
                            ->label('Description'),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Tarification')
                    ->schema([
                        Forms\Components\TextInput::make('unit_price')
                            ->required()
                            ->numeric()
                            ->prefix('XOF')
                            ->label('Prix unitaire'),
                        Forms\Components\TextInput::make('price')
                            ->numeric()
                            ->prefix('XOF')
                            ->label('Prix catalogue')
                            ->helperText('Prix affiché (optionnel)'),
                        Forms\Components\TextInput::make('tax_rate')
                            ->numeric()
                            ->default(18)
                            ->suffix('%')
                            ->label('Taux de TVA'),
                        Forms\Components\TextInput::make('unit')
                            ->default('unité')
                            ->maxLength(50)
                            ->label('Unité')
                            ->helperText('Ex: heure, jour, pièce, kg...'),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Options')
                    ->schema([
                        Forms\Components\Toggle::make('is_active')
                            ->default(true)
                            ->label('Produit actif'),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->sortable()
                    ->weight('bold')
                    ->label('Nom'),
                Tables\Columns\TextColumn::make('sku')
                    ->searchable()
                    ->placeholder('—')
                    ->label('SKU'),
                Tables\Columns\TextColumn::make('unit_price')
                    ->money('XOF')
                    ->sortable()
                    ->label('Prix unitaire'),
                Tables\Columns\TextColumn::make('tax_rate')
                    ->suffix('%')
                    ->placeholder('0%')
                    ->sortable()
                    ->label('TVA'),
                Tables\Columns\TextColumn::make('unit')
                    ->placeholder('unité')
                    ->label('Unité'),
                Tables\Columns\IconColumn::make('is_active')
                    ->boolean()
                    ->sortable()
                    ->label('Actif'),
                Tables\Columns\TextColumn::make('created_at')
                    ->date('d/m/Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->label('Créé le'),
            ])
            ->defaultSort('name')
            ->filters([
                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('Statut')
                    ->trueLabel('Actifs')
                    ->falseLabel('Inactifs'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
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
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListProducts::route('/'),
            'create' => Pages\CreateProduct::route('/create'),
            'edit' => Pages\EditProduct::route('/{record}/edit'),
        ];
    }
}
