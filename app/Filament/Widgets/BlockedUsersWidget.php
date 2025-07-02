<?php

namespace App\Filament\Widgets;

use App\Models\User;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class BlockedUsersWidget extends BaseWidget
{
    protected static ?int $sort = 2;
    
    protected function getStats(): array
    {
        $totalUsers = User::count();
        $blockedUsers = User::where('is_blocked', true)->count();
        $activeUsers = User::where('is_active_member', true)->where('is_blocked', false)->count();
        $admins = User::where('is_admin', true)->count();
        
        return [
            Stat::make('Totaal Gebruikers', $totalUsers)
                ->description('Alle geregistreerde gebruikers')
                ->descriptionIcon('heroicon-m-users')
                ->color('primary'),
                
            Stat::make('Actieve Leden', $activeUsers)
                ->description('Actieve, niet-geblokkeerde leden')
                ->descriptionIcon('heroicon-m-check-circle')
                ->color('success'),
                
            Stat::make('Geblokkeerde Gebruikers', $blockedUsers)
                ->description($blockedUsers > 0 ? 'Gebruikers die zijn geblokkeerd' : 'Geen geblokkeerde gebruikers')
                ->descriptionIcon('heroicon-m-lock-closed')
                ->color($blockedUsers > 0 ? 'danger' : 'success')
                ->url(route('filament.admin.resources.users.index', ['tableFilters[is_blocked][value]' => 1])),
                
            Stat::make('Administrators', $admins)
                ->description('Gebruikers met admin rechten')
                ->descriptionIcon('heroicon-m-shield-check')
                ->color('warning')
                ->url(route('filament.admin.resources.users.index', ['tableFilters[is_admin][value]' => 1])),
        ];
    }
}
