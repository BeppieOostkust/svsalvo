{{-- Extend the default Filament EditRecord page --}}
<x-filament-panels::page>
    {{-- Include the default edit record form --}}
    <x-filament-panels::form wire:submit="save">
        {{ $this->form }}
        
        <x-filament-panels::form.actions
            :actions="$this->getCachedFormActions()"
            :full-width="$this->hasFullWidthFormActions()"
        />
    </x-filament-panels::form>
    
    {{-- Include the audio player for sound functionality --}}
    @include('filament.pages.matches.audio-player-footer')
</x-filament-panels::page>
