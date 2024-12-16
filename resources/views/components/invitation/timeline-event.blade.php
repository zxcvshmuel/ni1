<div class="relative flex items-center gap-4 pb-8" x-data>
    <div class="absolute h-full w-0.5 bg-gray-200 left-6 top-8"></div>
    <div @class([
        'relative z-10 flex items-center justify-center w-12 h-12 rounded-full',
        'bg-purple-100 text-purple-600' => $color === 'primary',
        'bg-blue-100 text-blue-600' => $color === 'secondary',
    ])>
        <x-dynamic-component :component="'heroicon-o-'.$icon" class="w-6 h-6"/>
    </div>
    <div class="flex-1">
        <div class="text-sm text-gray-500">{{ $time }}</div>
        <div class="font-medium">{{ $title }}</div>
        @if($description)
            <div class="text-sm text-gray-600">{{ $description }}</div>
        @endif
    </div>
</div>