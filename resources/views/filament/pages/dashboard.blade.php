<x-filament-panels::page>
    <div class="space-y-6">
        <!-- Welcome Section -->
        <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-2xl font-bold text-gray-900 dark:text-white">
                        Welkom, {{ auth()->user()->name }}!
                    </h2>
                    <p class="text-gray-600 dark:text-gray-400 mt-1">
                        Hier is een overzicht van de gebruikersstatus
                    </p>
                </div>
                <div class="text-right">
                    <p class="text-sm text-gray-500 dark:text-gray-400">
                        {{ now()->format('l, d F Y') }}
                    </p>
                    <p class="text-sm text-gray-500 dark:text-gray-400">
                        {{ now()->format('H:i') }}
                    </p>
                </div>
            </div>
        </div>

        <!-- Stats Widgets -->
        <x-filament-widgets::widgets
            :widgets="$this->getWidgets()"
            :columns="$this->getColumns()"
        />

        <!-- Quick Actions -->
        <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">
                Snelle Acties
            </h3>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <a href="{{ route('filament.admin.resources.users.index') }}" 
                   class="block p-4 bg-blue-50 dark:bg-blue-900/20 rounded-lg hover:bg-blue-100 dark:hover:bg-blue-900/30 transition">
                    <div class="flex items-center">
                        <svg class="w-8 h-8 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-1a.5.5 0 01.5.5v1a.5.5 0 01-.5.5h-6a.5.5 0 01-.5-.5v-1a.5.5 0 01.5-.5h6z"></path>
                        </svg>
                        <div class="ml-3">
                            <h4 class="font-semibold text-blue-900 dark:text-blue-100">Beheer Gebruikers</h4>
                            <p class="text-sm text-blue-700 dark:text-blue-300">Bekijk en bewerk gebruikers</p>
                        </div>
                    </div>
                </a>

                <a href="{{ route('filament.admin.resources.users.index', ['tableFilters[is_blocked][value]' => 1]) }}" 
                   class="block p-4 bg-red-50 dark:bg-red-900/20 rounded-lg hover:bg-red-100 dark:hover:bg-red-900/30 transition">
                    <div class="flex items-center">
                        <svg class="w-8 h-8 text-red-600 dark:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                        </svg>
                        <div class="ml-3">
                            <h4 class="font-semibold text-red-900 dark:text-red-100">Geblokkeerde Gebruikers</h4>
                            <p class="text-sm text-red-700 dark:text-red-300">Bekijk geblokkeerde accounts</p>
                        </div>
                    </div>
                </a>

                <a href="{{ route('filament.admin.resources.users.index', ['tableFilters[is_admin][value]' => 1]) }}" 
                   class="block p-4 bg-yellow-50 dark:bg-yellow-900/20 rounded-lg hover:bg-yellow-100 dark:hover:bg-yellow-900/30 transition">
                    <div class="flex items-center">
                        <svg class="w-8 h-8 text-yellow-600 dark:text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                        </svg>
                        <div class="ml-3">
                            <h4 class="font-semibold text-yellow-900 dark:text-yellow-100">Administrators</h4>
                            <p class="text-sm text-yellow-700 dark:text-yellow-300">Beheer admin accounts</p>
                        </div>
                    </div>
                </a>
            </div>
        </div>
    </div>
</x-filament-panels::page>
