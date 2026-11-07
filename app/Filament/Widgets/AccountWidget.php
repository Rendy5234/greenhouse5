<?php

namespace App\Filament\Widgets;

use Filament\Facades\Filament;
use Filament\Widgets\AccountWidget as BaseAccountWidget;

class AccountWidget extends BaseAccountWidget
{
    protected ?string $maxWidth = 'full';

    protected int|string|array $columnSpan = 'full';

    protected string $view = 'filament.widgets.account-widget';

    protected function getViewData(): array
    {
        $user = Filament::auth()->user();
        $roles = $user?->getRoleNames()->implode(', ') ?? '';

        return [
            'user' => $user,
            'roles' => $roles,
        ];
    }
}
