<x-filament::widget>
    <x-filament::card>
        {{-- Widget content --}}
        <div class="h-12 flex items-center space-x-4 rtl:space-x-reverse">
            <div class="w-10 h-10 rounded-full bg-gray-200 bg-cover bg-center" style="background-image: url( 'https://cdn-icons-png.flaticon.com/512/4768/4768700.png')">

            </div>
            <div>

                <h2 class="text-lg sm:text-xl font-bold tracking-tight">
                    Download User Manual

                </h2>


                <a type="submit" href="{{ route('manual') }}" @class([ 'text-gray-600 hover:text-primary-500 focus:outline-none focus:underline' , 'dark:text-gray-300 dark:hover:text-primary-500'=> config('filament.dark_mode'),
                    ])
                    >
                    click here!
                </a>
                </form>
            </div>
        </div>
    </x-filament::card>
</x-filament::widget>
