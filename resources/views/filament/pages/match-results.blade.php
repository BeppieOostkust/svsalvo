<div class="p-6 space-y-8">
    @forelse($results as $result)
        <div>
            <h3 class="text-lg font-bold text-gray-900 dark:text-gray-100 mb-3">🏆 {{ $result['kaliber'] }}</h3>
            <table class="w-full text-sm divide-y divide-gray-200 dark:divide-gray-700">
                <thead>
                    <tr class="text-left text-xs font-semibold uppercase text-gray-500 dark:text-gray-400">
                        <th class="pb-2 w-12">#</th>
                        <th class="pb-2">Naam</th>
                        <th class="pb-2 text-right">Punten</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                    @foreach($result['scores'] as $score)
                        <tr class="{{ $score['position'] === 1 ? 'font-semibold' : '' }}">
                            <td class="py-3 pr-4 text-gray-500 dark:text-gray-400">
                                @if($score['position'] === 1) 🥇
                                @elseif($score['position'] === 2) 🥈
                                @elseif($score['position'] === 3) 🥉
                                @else {{ $score['position'] }}
                                @endif
                            </td>
                            <td class="py-3 text-gray-900 dark:text-gray-100">{{ $score['name'] }}</td>
                            <td class="py-3 text-right font-mono text-gray-900 dark:text-gray-100">{{ $score['points'] }} pt</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @empty
        <p class="text-gray-500 text-center py-8">Geen resultaten beschikbaar</p>
    @endforelse
</div>
