<div class="p-6">
    @if(empty($leaderboard))
        <div class="text-center py-12">
            <p class="text-gray-500 text-lg">Nog geen scores beschikbaar voor dit jaar</p>
        </div>
    @else
        <!-- Summary Stats -->
        <div class="mb-6 bg-gradient-to-r from-blue-50 to-indigo-50 dark:from-blue-900/20 dark:to-indigo-900/20 rounded-lg p-6 border-2 border-blue-200 dark:border-blue-800">
            <h4 class="text-lg font-semibold text-blue-900 dark:text-blue-100 mb-3 flex items-center gap-2">
                <span class="text-2xl">📊</span>
                <span>Overzicht {{ date('Y') }}</span>
            </h4>
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4 text-sm">
                <div class="bg-white dark:bg-gray-800 rounded-lg p-4 shadow">
                    <div class="text-gray-500 dark:text-gray-400 text-xs uppercase mb-1">Totaal Deelnemers</div>
                    <div class="text-2xl font-bold text-blue-600 dark:text-blue-400">
                        {{ collect($leaderboard)->pluck('user_id')->unique()->count() }}
                    </div>
                </div>
                <div class="bg-white dark:bg-gray-800 rounded-lg p-4 shadow">
                    <div class="text-gray-500 dark:text-gray-400 text-xs uppercase mb-1">KKP Schutters</div>
                    <div class="text-2xl font-bold text-green-600 dark:text-green-400">
                        {{ collect($leaderboard)->where('kaliber', 'KKP')->count() }}
                    </div>
                </div>
                <div class="bg-white dark:bg-gray-800 rounded-lg p-4 shadow">
                    <div class="text-gray-500 dark:text-gray-400 text-xs uppercase mb-1">GKP Schutters</div>
                    <div class="text-2xl font-bold text-purple-600 dark:text-purple-400">
                        {{ collect($leaderboard)->where('kaliber', 'GKP')->count() }}
                    </div>
                </div>
                <div class="bg-white dark:bg-gray-800 rounded-lg p-4 shadow">
                    <div class="text-gray-500 dark:text-gray-400 text-xs uppercase mb-1">Totaal Series</div>
                    <div class="text-2xl font-bold text-orange-600 dark:text-orange-400">
                        {{ collect($leaderboard)->sum('series_count') }}
                    </div>
                </div>
            </div>
        </div>

        <!-- Leaderboards per Kaliber -->
        <div class="space-y-8">
            @foreach(['KKP', 'GKP'] as $kaliber)
                @php
                    $kaliberData = collect($leaderboard)->where('kaliber', $kaliber)->values();
                @endphp
                
                @if($kaliberData->count() > 0)
                    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg overflow-hidden border-2 border-gray-200 dark:border-gray-700">
                        <!-- Header -->
                        <div class="bg-gradient-to-r 
                            {{ $kaliber === 'KKP' ? 'from-green-600 to-green-700 dark:from-green-700 dark:to-green-800' : 'from-purple-600 to-purple-700 dark:from-purple-700 dark:to-purple-800' }} 
                            px-6 py-4">
                            <h3 class="text-xl font-bold text-white flex items-center gap-3">
                                <span class="text-2xl">{{ $kaliber === 'KKP' ? '🎯' : '🔫' }}</span>
                                <span>{{ $kaliber }} - {{ $kaliber === 'KKP' ? 'Klein Kaliber Pistool' : 'Groot Kaliber Pistool' }}</span>
                            </h3>
                        </div>
                        
                        <!-- Leaderboard Table -->
                        <div class="overflow-x-auto">
                            <table class="w-full">
                                <thead class="bg-gray-50 dark:bg-gray-900">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider w-24">
                                            Positie
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                            Naam
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider w-32">
                                            Totaal Punten
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider w-32">
                                            Gemiddeld
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider w-32">
                                            Beste Score
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider w-28">
                                            Series
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                    @foreach($kaliberData as $entry)
                                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors
                                            {{ $entry['position'] === 1 ? 'bg-yellow-50 dark:bg-yellow-900/20 border-l-4 border-yellow-500' : '' }}
                                            {{ $entry['position'] === 2 ? 'bg-slate-50 dark:bg-slate-700/50 border-l-4 border-slate-400' : '' }}
                                            {{ $entry['position'] === 3 ? 'bg-orange-50 dark:bg-orange-900/20 border-l-4 border-orange-500' : '' }}
                                        ">
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="flex items-center gap-2">
                                                    @if($entry['position'] === 1)
                                                        <span class="text-3xl">🥇</span>
                                                        <span class="text-xs font-bold text-yellow-700 dark:text-yellow-400">1e</span>
                                                    @elseif($entry['position'] === 2)
                                                        <span class="text-3xl">🥈</span>
                                                        <span class="text-xs font-bold text-slate-700 dark:text-slate-300">2e</span>
                                                    @elseif($entry['position'] === 3)
                                                        <span class="text-3xl">🥉</span>
                                                        <span class="text-xs font-bold text-orange-700 dark:text-orange-400">3e</span>
                                                    @else
                                                        <span class="text-lg font-semibold text-gray-600 dark:text-gray-300">
                                                            {{ $entry['position'] }}
                                                        </span>
                                                    @endif
                                                </div>
                                            </td>
                                            <td class="px-6 py-4">
                                                <div class="flex items-center gap-2">
                                                    <span class="text-sm font-medium text-gray-900 dark:text-gray-100">
                                                        {{ $entry['name'] }}
                                                    </span>
                                                    @if($entry['position'] === 1)
                                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-bold bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200">
                                                            Kampioen {{ date('Y') }}
                                                        </span>
                                                    @endif
                                                </div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="flex items-center gap-2">
                                                    <span class="text-xl font-bold 
                                                        {{ $entry['position'] === 1 ? 'text-yellow-600 dark:text-yellow-400' : '' }}
                                                        {{ $entry['position'] === 2 ? 'text-slate-600 dark:text-slate-300' : '' }}
                                                        {{ $entry['position'] === 3 ? 'text-orange-600 dark:text-orange-400' : '' }}
                                                        {{ $entry['position'] > 3 ? 'text-gray-900 dark:text-gray-100' : '' }}
                                                    ">
                                                        {{ number_format($entry['total_points'], 0, ',', '.') }}
                                                    </span>
                                                    <span class="text-xs text-gray-500 dark:text-gray-400">pt</span>
                                                </div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <span class="text-sm font-medium text-gray-700 dark:text-gray-300">
                                                    {{ number_format($entry['average_points'], 1, ',', '.') }}
                                                </span>
                                                <span class="text-xs text-gray-500 dark:text-gray-400">pt</span>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200">
                                                    🎯 {{ $entry['best_score'] }} pt
                                                </span>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <span class="text-sm text-gray-600 dark:text-gray-400">
                                                    {{ $entry['series_count'] }} {{ $entry['series_count'] === 1 ? 'serie' : 'series' }}
                                                </span>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                @endif
            @endforeach
        </div>

        <!-- Footer Note -->
        <div class="mt-6 text-center text-sm text-gray-500 dark:text-gray-400">
            <p>📅 Leaderboard van {{ date('Y') }} • Alleen officiële scores tellen mee</p>
        </div>
    @endif
</div>
