<x-filament::page>
    <x-filament::button wire:click="download">
        ⏬ Template for Import File
    </x-filament::button>

    {{$this->form}}

    <x-filament::button wire:click="import">
        Import
    </x-filament::button>

</x-filament::page>
