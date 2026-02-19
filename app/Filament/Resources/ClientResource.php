<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ClientResource\Pages;
use App\Models\Client;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class ClientResource extends Resource
{
    protected static ?string $model = Client::class;

    protected static ?string $navigationIcon = 'heroicon-o-user-group';

    protected static ?string $navigationLabel = 'Clients';

    protected static ?string $navigationGroup = 'Gestion Commerciale';

    protected static ?int $navigationSort = 1;

    protected static ?string $modelLabel = 'Client';

    protected static ?string $pluralModelLabel = 'Clients';

    protected static ?string $recordTitleAttribute = 'name';

    public static function getGloballySearchableAttributes(): array
    {
        return ['name', 'email', 'company', 'phone'];
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count() ?: null;
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Informations principales')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->required()
                            ->maxLength(255)
                            ->label('Nom complet'),
                        Forms\Components\TextInput::make('company')
                            ->maxLength(255)
                            ->label('Entreprise'),
                        Forms\Components\TextInput::make('email')
                            ->email()
                            ->maxLength(255)
                            ->label('Email'),
                        Forms\Components\TextInput::make('phone')
                            ->tel()
                            ->maxLength(255)
                            ->label('Téléphone'),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Adresse')
                    ->schema([
                        Forms\Components\TextInput::make('address')
                            ->maxLength(255)
                            ->label('Adresse')
                            ->columnSpanFull(),
                        Forms\Components\TextInput::make('city')
                            ->maxLength(255)
                            ->label('Ville'),
                        Forms\Components\TextInput::make('state')
                            ->maxLength(255)
                            ->label('Région / État'),
                        Forms\Components\TextInput::make('postal_code')
                            ->maxLength(20)
                            ->label('Code postal'),
                        Forms\Components\TextInput::make('country')
                            ->maxLength(255)
                            ->default('Sénégal')
                            ->label('Pays'),
                    ])
                    ->columns(2)
                    ->collapsible(),

                Forms\Components\Section::make('Paramètres')
                    ->schema([
                        Forms\Components\TextInput::make('tax_id')
                            ->maxLength(255)
                            ->label('N° Fiscal / NINEA'),
                        Forms\Components\Select::make('currency')
                            ->options([
                                'XOF' => 'XOF - Franc CFA',
                                'XAF' => 'XAF - Franc CFA (CEMAC)',
                                'EUR' => 'EUR - Euro',
                                'USD' => 'USD - Dollar US',
                                'GNF' => 'GNF - Franc Guinéen',
                            ])
                            ->default('XOF')
                            ->label('Devise'),
                        Forms\Components\Select::make('language')
                            ->options([
                                'fr' => 'Français',
                                'en' => 'Anglais',
                            ])
                            ->default('fr')
                            ->label('Langue'),
                        Forms\Components\Toggle::make('is_active')
                            ->default(true)
                            ->label('Actif'),
                    ])
                    ->columns(2)
                    ->collapsible(),

                Forms\Components\Section::make('Notes')
                    ->schema([
                        Forms\Components\Textarea::make('notes')
                            ->maxLength(65535)
                            ->columnSpanFull()
                            ->label('Notes internes'),
                    ])
                    ->collapsible()
                    ->collapsed(),
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
                Tables\Columns\TextColumn::make('company')
                    ->searchable()
                    ->placeholder('—')
                    ->label('Entreprise'),
                Tables\Columns\TextColumn::make('email')
                    ->searchable()
                    ->copyable()
                    ->icon('heroicon-o-envelope')
                    ->label('Email'),
                Tables\Columns\TextColumn::make('phone')
                    ->searchable()
                    ->icon('heroicon-o-phone')
                    ->label('Téléphone'),
                Tables\Columns\TextColumn::make('city')
                    ->sortable()
                    ->placeholder('—')
                    ->toggleable()
                    ->label('Ville'),
                Tables\Columns\TextColumn::make('invoices_count')
                    ->counts('invoices')
                    ->sortable()
                    ->label('Factures')
                    ->badge()
                    ->color('primary'),
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
            ->defaultSort('created_at', 'desc')
            ->filters([
                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('Statut')
                    ->trueLabel('Actifs')
                    ->falseLabel('Inactifs'),
                Tables\Filters\SelectFilter::make('country')
                    ->label('Pays')
                    ->options(fn () =>
                        Client::whereNotNull('country')
                            ->distinct()
                            ->pluck('country', 'country')
                            ->toArray()
                    ),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
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
            'index' => Pages\ListClients::route('/'),
            'create' => Pages\CreateClient::route('/create'),
            'edit' => Pages\EditClient::route('/{record}/edit'),
        ];
    }
}
