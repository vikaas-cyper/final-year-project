@if (config('filament.layout.footer.should_show_logo'))
    <div class="flex items-center justify-center filament-footer">
        <a
            href="https://www.nestpharmaceuticals.in/"
            target="_blank"
            rel="noopener noreferrer"
            class="text-gray-300 hover:text-primary-500 transition"
        >
        <span class="px-1"></span>&copy; {{ date('Y') }} - Nestpharmaceuticals - Salem
        </a>
    </div>
@endif
