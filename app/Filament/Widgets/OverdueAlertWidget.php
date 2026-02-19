<?php

namespace App\Filament\Widgets;

use App\Domain\Invoice\Models\Invoice;
use Filament\Widgets\Widget;
use Illuminate\Contracts\View\View;

class OverdueAlertWidget extends Widget
{
    protected static ?int $sort = 0;

    protected static string $view = 'filament.widgets.overdue-alert-widget';

    protected int|string|array $columnSpan = 'full';

    public function getOverdueCount(): int
    {
        return Invoice::where('status', 'overdue')->count();
    }

    public function getOverdueTotal(): float
    {
        return Invoice::where('status', 'overdue')->sum('total');
    }

    public static function canView(): bool
    {
        return Invoice::where('status', 'overdue')->exists();
    }
}
