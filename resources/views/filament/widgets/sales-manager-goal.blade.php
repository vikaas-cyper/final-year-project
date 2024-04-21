    <x-filament::widget>
        <x-filament::card>
            @php
                $user = \Filament\Facades\Filament::auth()->user();
            @endphp

            <div class="h-12 flex items-center space-x-4 rtl:space-x-reverse">

                <div>
                    <div class="flex items-center space-x-2 rtl:space-x-reverse text-sm font-medium text-gray-500 dark:text-gray-200 ">
                        <span>
                                This Month Target
                        </span>
                    </div>

                    <div class="text-3xl">

                        {{ $this->getTarget() }}

                    </div>

                </div>
            </div>
        </x-filament::card>

    </x-filament::widget>
