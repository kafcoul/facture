<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SubscriptionResource\Pages;
use App\Models\User;
use App\Services\PlanService;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Notifications\Notification;
use Filament\Tables\Actions\Action;
use Illuminate\Database\Eloquent\Builder;

class SubscriptionResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-credit-card';

    protected static ?string $navigationLabel = 'Abonnements';

    protected static ?string $navigationGroup = 'Abonnements';

    protected static ?int $navigationSort = 1;

    protected static ?string $modelLabel = 'Abonnement';

    protected static ?string $pluralModelLabel = 'Abonnements';

    protected static ?string $slug = 'subscriptions';

    public static function getNavigationBadge(): ?string
    {
        $tenantId = auth()->user()?->tenant_id;
        if (!$tenantId) return null;

        return static::getModel()::where('tenant_id', $tenantId)
            ->where('role', 'client')
            ->count() ?: null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        $tenantId = auth()->user()?->tenant_id;
        if (!$tenantId) return 'primary';

        $expiring = static::getModel()::where('tenant_id', $tenantId)
            ->where('role', 'client')
            ->whereNotNull('trial_ends_at')
            ->where('trial_ends_at', '<=', now()->addDays(7))
            ->where('trial_ends_at', '>', now())
            ->count();

        return $expiring > 0 ? 'warning' : 'primary';
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->where('tenant_id', auth()->user()?->tenant_id)
            ->where('role', 'client')
            ->whereNotNull('plan');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Informations utilisateur')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label('Nom')
                            ->disabled(),
                        Forms\Components\TextInput::make('email')
                            ->label('Email')
                            ->disabled(),
                        Forms\Components\TextInput::make('company_name')
                            ->label('Entreprise')
                            ->disabled(),
                    ])
                    ->columns(3),

                Forms\Components\Section::make('Plan & Abonnement')
                    ->schema([
                        Forms\Components\Select::make('plan')
                            ->label('Plan')
                            ->options([
                                'starter' => 'Starter — Gratuit',
                                'pro' => 'Pro — 19 000 XOF/mois',
                                'enterprise' => 'Enterprise — 65 000 XOF/mois',
                            ])
                            ->required(),
                        Forms\Components\DateTimePicker::make('trial_ends_at')
                            ->label('Fin de la période d\'essai')
                            ->nullable(),
                        Forms\Components\Toggle::make('is_active')
                            ->label('Compte actif')
                            ->default(true),
                    ])
                    ->columns(3),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Utilisateur')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),
                Tables\Columns\TextColumn::make('email')
                    ->label('Email')
                    ->searchable()
                    ->copyable(),
                Tables\Columns\TextColumn::make('company_name')
                    ->label('Entreprise')
                    ->searchable()
                    ->placeholder('—'),
                Tables\Columns\BadgeColumn::make('plan')
                    ->label('Plan')
                    ->colors([
                        'gray' => 'starter',
                        'info' => 'pro',
                        'success' => 'enterprise',
                    ])
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'starter' => '🆓 Starter',
                        'pro' => '⭐ Pro',
                        'enterprise' => '🏢 Enterprise',
                        default => $state,
                    })
                    ->sortable(),
                Tables\Columns\TextColumn::make('plan_price')
                    ->label('Prix')
                    ->getStateUsing(function (User $record): string {
                        $plans = PlanService::PLANS;
                        $plan = $plans[$record->plan] ?? null;
                        if (!$plan) return '—';
                        return $plan['price'] === 0
                            ? 'Gratuit'
                            : number_format($plan['price'], 0, ',', ' ') . ' ' . $plan['currency'] . '/mois';
                    }),
                Tables\Columns\TextColumn::make('trial_ends_at')
                    ->label('Fin essai')
                    ->dateTime('d/m/Y H:i')
                    ->placeholder('—')
                    ->color(fn (User $record): string =>
                        $record->trial_ends_at && $record->trial_ends_at->isPast()
                            ? 'danger'
                            : ($record->trial_ends_at && $record->trial_ends_at->diffInDays(now()) <= 7
                                ? 'warning'
                                : 'success')
                    ),
                Tables\Columns\TextColumn::make('invoices_count')
                    ->label('Factures')
                    ->counts('invoices')
                    ->sortable(),
                Tables\Columns\IconColumn::make('is_active')
                    ->label('Actif')
                    ->boolean()
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Inscrit le')
                    ->date('d/m/Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('plan')
                    ->label('Plan')
                    ->options([
                        'starter' => 'Starter',
                        'pro' => 'Pro',
                        'enterprise' => 'Enterprise',
                    ]),
                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('Statut')
                    ->trueLabel('Actifs')
                    ->falseLabel('Inactifs'),
                Tables\Filters\Filter::make('trial_expiring')
                    ->label('Essai expirant bientôt')
                    ->query(fn (Builder $query): Builder =>
                        $query->whereNotNull('trial_ends_at')
                            ->where('trial_ends_at', '<=', now()->addDays(7))
                            ->where('trial_ends_at', '>', now())
                    ),
                Tables\Filters\Filter::make('trial_expired')
                    ->label('Essai expiré')
                    ->query(fn (Builder $query): Builder =>
                        $query->whereNotNull('trial_ends_at')
                            ->where('trial_ends_at', '<=', now())
                    ),
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->label('Modifier'),
                Action::make('upgrade_pro')
                    ->label('→ Pro')
                    ->icon('heroicon-o-arrow-up-circle')
                    ->color('info')
                    ->requiresConfirmation()
                    ->modalHeading('Passer au plan Pro')
                    ->modalDescription('Êtes-vous sûr de vouloir passer cet utilisateur au plan Pro ?')
                    ->visible(fn (User $record): bool => $record->plan !== 'pro' && $record->plan !== 'enterprise')
                    ->action(function (User $record): void {
                        $record->update(['plan' => 'pro']);
                        Notification::make()
                            ->title('Plan mis à jour')
                            ->body("{$record->name} est maintenant sur le plan Pro.")
                            ->success()
                            ->send();
                    }),
                Action::make('upgrade_enterprise')
                    ->label('→ Enterprise')
                    ->icon('heroicon-o-building-office')
                    ->color('success')
                    ->requiresConfirmation()
                    ->modalHeading('Passer au plan Enterprise')
                    ->modalDescription('Êtes-vous sûr de vouloir passer cet utilisateur au plan Enterprise ?')
                    ->visible(fn (User $record): bool => $record->plan !== 'enterprise')
                    ->action(function (User $record): void {
                        $record->update(['plan' => 'enterprise']);
                        Notification::make()
                            ->title('Plan mis à jour')
                            ->body("{$record->name} est maintenant sur le plan Enterprise.")
                            ->success()
                            ->send();
                    }),
                Action::make('downgrade_starter')
                    ->label('→ Starter')
                    ->icon('heroicon-o-arrow-down-circle')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->modalHeading('Rétrograder au plan Starter')
                    ->modalDescription('Êtes-vous sûr de vouloir rétrograder cet utilisateur au plan Starter ?')
                    ->visible(fn (User $record): bool => $record->plan !== 'starter')
                    ->action(function (User $record): void {
                        $record->update(['plan' => 'starter']);
                        Notification::make()
                            ->title('Plan rétrogradé')
                            ->body("{$record->name} est maintenant sur le plan Starter.")
                            ->warning()
                            ->send();
                    }),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\BulkAction::make('bulk_upgrade_pro')
                        ->label('Passer au Pro')
                        ->icon('heroicon-o-arrow-up-circle')
                        ->color('info')
                        ->requiresConfirmation()
                        ->action(function ($records): void {
                            $records->each(fn ($r) => $r->update(['plan' => 'pro']));
                            Notification::make()
                                ->title('Plans mis à jour')
                                ->body($records->count() . ' utilisateurs passés au plan Pro.')
                                ->success()
                                ->send();
                        }),
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
            'index' => Pages\ListSubscriptions::route('/'),
            'edit' => Pages\EditSubscription::route('/{record}/edit'),
        ];
    }
}
