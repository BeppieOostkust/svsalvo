<div class="p-6">
    @if(empty($results))
        <div class="text-center py-12">
            <p class="text-gray-500 text-lg">Geen resultaten beschikbaar</p>
        </div>
    @else
        <div class="space-y-8">
            @foreach($results as $result)
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg overflow-hidden border-2 border-gray-200 dark:border-gray-700">
                    <!-- Header -->
                    <div class="bg-gradient-to-r from-blue-600 to-blue-700 dark:from-blue-700 dark:to-blue-800 px-6 py-4">
                        <h3 class="text-xl font-bold text-white flex items-center gap-3">
                            <span class="text-2xl">🎯</span>
                            <span>Serie {{ $result['round_number'] }} - {{ $result['kaliber'] }}</span>
                        </h3>
                    </div>
                    
                    <!-- Results Table -->
                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead class="bg-gray-50 dark:bg-gray-900">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider w-20">
                                        Positie
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                        Naam
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider w-24">
                                        Baan
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider w-32">
                                        Punten
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                @foreach($result['scores'] as $score)
                                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors
                                        {{ $score['position'] === 1 ? 'bg-yellow-50 dark:bg-yellow-900/20 border-l-4 border-yellow-500' : '' }}
                                        {{ $score['position'] === 2 ? 'bg-slate-50 dark:bg-slate-700/50 border-l-4 border-slate-400' : '' }}
                                        {{ $score['position'] === 3 ? 'bg-orange-50 dark:bg-orange-900/20 border-l-4 border-orange-500' : '' }}
                                    ">
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="flex items-center gap-2">
                                                @if($score['position'] === 1)
                                                    <span class="text-3xl">🥇</span>
                                                    <span class="text-xs font-bold text-yellow-700 dark:text-yellow-400">1e</span>
                                                @elseif($score['position'] === 2)
                                                    <span class="text-3xl">🥈</span>
                                                    <span class="text-xs font-bold text-slate-700 dark:text-slate-300">2e</span>
                                                @elseif($score['position'] === 3)
                                                    <span class="text-3xl">🥉</span>
                                                    <span class="text-xs font-bold text-orange-700 dark:text-orange-400">3e</span>
                                                @else
                                                    <span class="text-lg font-semibold text-gray-600 dark:text-gray-300">
                                                        {{ $score['position'] }}
                                                    </span>
                                                @endif
                                            </div>
                                        </td>
                                        <td class="px-6 py-4">
                                            <div class="flex items-center gap-2">
                                                <span class="text-sm font-medium text-gray-900 dark:text-gray-100">
                                                    {{ $score['name'] }}
                                                </span>
                                                @if($score['position'] === 1)
                                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-bold bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200">
                                                        Winnaar
                                                    </span>
                                                @endif
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            @if($score['baan'])
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200">
                                                    Baan {{ $score['baan'] }}
                                                </span>
                                            @else
                                                <span class="text-gray-400 dark:text-gray-600 text-sm">-</span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="flex items-center gap-2">
                                                <span class="text-2xl font-bold 
                                                    {{ $score['position'] === 1 ? 'text-yellow-600 dark:text-yellow-400' : '' }}
                                                    {{ $score['position'] === 2 ? 'text-slate-600 dark:text-slate-300' : '' }}
                                                    {{ $score['position'] === 3 ? 'text-orange-600 dark:text-orange-400' : '' }}
                                                    {{ $score['position'] > 3 ? 'text-gray-900 dark:text-gray-100' : '' }}
                                                ">
                                                    {{ $score['points'] }}
                                                </span>
                                                <span class="text-sm text-gray-500 dark:text-gray-400">pt</span>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @endforeach
        </div>
        
        <!-- Summary Stats -->
        <div class="mt-8 bg-blue-50 dark:bg-blue-900/20 rounded-lg p-6 border-2 border-blue-200 dark:border-blue-800">
            <h4 class="text-lg font-semibold text-blue-900 dark:text-blue-100 mb-2">
                📊 Overzicht
            </h4>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm text-blue-800 dark:text-blue-200">
                <div>
                    <span class="font-medium">Totaal aantal series:</span>
                    <span class="ml-2">{{ collect($results)->pluck('round_number')->unique()->count() }}</span>
                </div>
                <div>
                    <span class="font-medium">Totaal aantal deelnemers:</span>
                    <span class="ml-2">{{ collect($results)->pluck('scores')->flatten(1)->count() }}</span>
                </div>
                <div>
                    <span class="font-medium">Kalibers:</span>
                    <span class="ml-2">{{ collect($results)->pluck('kaliber')->unique()->implode(', ') }}</span>
                </div>
            </div>
        </div>
    @endif
</div>
