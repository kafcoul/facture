<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TeamInvitationResource\Pages;
use App\Models\TeamInvitation;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class TeamInvitationResource extends Resource
{
    protected static ?string $model = TeamInvitation::class;

    protected static ?string $navigationIcon = 'heroicon-o-envelope';

    protected static ?string $navigationLabel = 'Invitations';

    protected static ?string $navigationGroup = 'Administration';

    protected static ?int $navigationSort = 3;

    protected static ?string $modelLabel = 'Invitation';

    protected static ?string $pluralModelLabel = 'Invitations';

    public static function getNavigationBadge(): ?string
    {
        $tenantId = auth()->user()?->tenant_id;
        if (!$tenantId) return null;

        return static::getModel()::where('tenant_id', $tenantId)
            ->where('status', 'pending')
            ->count() ?: null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'warning';
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->where('tenant_id', auth()->user()?->tenant_id);
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Invitation')
                    ->schema([
                        Forms\Components\TextInput::make('email')
                            ->required()
                            ->email()
                            ->maxLength(255)
                            ->label('Email'),
                        Forms\Components\TextInput::make('name')
                            ->maxLength(255)
                            ->label('Nom'),
                        Forms\Components\Select::make('role')
                            ->options([
                                'admin' => 'Administrateur',
                                'user' => 'Utilisateur',
                                'accountant' => 'Comptable',
                            ])
                            ->default('user')
                            ->required()
                            ->label('Rôle'),
                        Forms\Components\Select::make('tenant_id')
                            ->relationship('tenant', 'name')
                            ->searchable()
                            ->preload()
                            ->required()
                            ->label('Tenant'),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Statut')
                    ->schema([
                        Forms\Components\Select::make('status')
                            ->options([
                                'pending' => 'En attente',
                                'accepted' => 'Acceptée',
                                'declined' => 'Refusée',
                                'expired' => 'Expirée',
                            ])
                            ->default('pending')
                            ->required()
                            ->label('Statut'),
                        Forms\Components\DateTimePicker::make('expires_at')
                            ->default(now()->addDays(7))
                            ->label('Expire le'),
                        Forms\Components\DateTimePicker::make('accepted_at')
                            ->label('Acceptée le')
                            ->disabled(),
                    ])
                    ->columns(3),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('email')
                    ->searchable()
                    ->sortable()
                    ->copyable()
                    ->icon('heroicon-m-envelope')
                    ->label('Email'),
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->placeholder('—')
                    ->label('Nom'),
                Tables\Columns\TextColumn::make('tenant.name')
                    ->sortable()
                    ->label('Tenant'),
                Tables\Columns\TextColumn::make('role')
                    ->badge()
                    ->color(fn (?string $state): string => match ($state) {
                        'admin' => 'danger',
                        'accountant' => 'warning',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (?string $state): string => match ($state) {
                        'admin' => 'Admin',
                        'user' => 'Utilisateur',
                        'accountant' => 'Comptable',
                        default => $state ?? '—',
                    })
                    ->label('Rôle'),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn (?string $state): string => match ($state) {
                        'pending' => 'warning',
                        'accepted' => 'success',
                        'declined' => 'danger',
                        'expired' => 'gray',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (?string $state): string => match ($state) {
                        'pending' => 'En attente',
                        'accepted' => 'Acceptée',
                        'declined' => 'Refusée',
                        'expired' => 'Expirée',
                        default => $state ?? '—',
                    })
                    ->label('Statut'),
                Tables\Columns\TextColumn::make('expires_at')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->color(fn ($record) => $record->expires_at && $record->expires_at->isPast() ? 'danger' : null)
                    ->label('Expire le'),
                Tables\Columns\TextColumn::make('created_at')
                    ->date('d/m/Y')
                    ->sortable()
                    ->label('Envoyée le'),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'pending' => 'En attente',
                        'accepted' => 'Acceptée',
                        'declined' => 'Refusée',
                        'expired' => 'Expirée',
                    ])
                    ->label('Statut'),
                Tables\Filters\SelectFilter::make('role')
                    ->options([
                        'admin' => 'Administrateur',
                        'user' => 'Utilisateur',
                        'accountant' => 'Comptable',
                    ])
                    ->label('Rôle'),
                Tables\Filters\Filter::make('expired')
                    ->query(fn (Builder $query) => $query->where('expires_at', '<', now())->where('status', 'pending'))
                    ->label('Expirées')
                    ->toggle(),
            ])
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\EditAction::make(),
                    Tables\Actions\Action::make('resend')
                        ->label('Renvoyer')
                        ->icon('heroicon-o-arrow-path')
                        ->color('info')
                        ->requiresConfirmation()
                        ->action(function (TeamInvitation $record) {
                            $record->update(['expires_at' => now()->addDays(7)]);
                            // Could dispatch a mail job here
                        })
                        ->visible(fn (TeamInvitation $record) => $record->status === 'pending'),
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
            'index' => Pages\ListTeamInvitations::route('/'),
            'create' => Pages\CreateTeamInvitation::route('/create'),
            'edit' => Pages\EditTeamInvitation::route('/{record}/edit'),
        ];
    }
}
