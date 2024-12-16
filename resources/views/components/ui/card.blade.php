<div {{ $attributes->merge(['class' => 'rounded-lg bg-white shadow-sm p-4']) }} dir="{{ $rtl ? 'rtl' : 'ltr' }}">
    @if($title)
        <div class="mb-4">
            <h3 class="text-lg font-medium text-gray-900">{{ $title }}</h3>
            @if($subtitle)
                <p class="text-sm text-gray-500">{{ $subtitle }}</p>
            @endif
        </div>
    @endif
    {{ $slot }}
</div>