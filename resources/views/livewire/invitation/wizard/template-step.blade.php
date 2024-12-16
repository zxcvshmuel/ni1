<div>
    <div class="space-y-8">
        {{-- Header Section --}}
        <div>
            <h2 class="text-2xl font-medium text-gray-900">בחר תבנית להזמנה</h2>
            <p class="mt-1 text-sm text-gray-500">הוסיפו מעט קסם להזמנה שלכם</p>
        </div>

        {{-- Search & Filters --}}
        <div class="flex gap-4">
            <div class="flex-1">
                <div class="relative">
                    <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                        <x-heroicon-o-magnifying-glass class="w-5 h-5 text-gray-400" />
                    </div>
                    <input
                        type="search"
                        wire:model.live.debounce.300ms="search"
                        placeholder="חיפוש תבנית..."
                        class="block w-full rounded-md border-0 py-3 pr-10 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-purple-600"
                    >
                </div>
            </div>

            <div class="flex gap-2">
                <select
                    wire:model.live="selectedCategory"
                    class="block rounded-md border-0 py-3 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-inset focus:ring-purple-600"
                >
                    <option value="">כל הסגנונות</option>
                    @foreach($categories as $category)
                        <option value="{{ $category->id }}">{{ $category->name['he'] ?? $category->name['en'] ?? '' }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        {{-- Templates Grid --}}
        <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3">
            @foreach($templates as $template)
                <div 
                    wire:key="template-{{ $template->id }}"
                    @class([
                        'relative group cursor-pointer rounded-lg overflow-hidden transition-all duration-200',
                        'ring-2 ring-purple-600' => $selectedTemplate === $template->id
                    ])
                    wire:click="selectTemplate({{ $template->id }})"
                >
                    {{-- Template Preview Image --}}
                    <div class="aspect-[3/4] bg-gray-100">
                        @if($template->getFirstMediaUrl('thumbnails'))
                            <img 
                                src="{{ $template->getFirstMediaUrl('thumbnails') }}" 
                                alt="{{ $template->name['he'] }}"
                                class="h-full w-full object-cover"
                            >
                        @else
                            <div class="flex h-full items-center justify-center">
                                <x-heroicon-o-photo class="w-12 h-12 text-gray-300" />
                            </div>
                        @endif
                    </div>

                    {{-- Template Info --}}
                    <div class="absolute bottom-0 inset-x-0 bg-gradient-to-t from-black/60 p-4">
                        <h3 class="text-lg font-medium text-white">{{ $template->name['he'] }}</h3>
                        @if($template->category)
                            <p class="text-sm text-gray-200">{{ $template->category->name['he'] }}</p>
                        @endif
                    </div>

                    {{-- Selected Indicator --}}
                    @if($selectedTemplate === $template->id)
                        <div class="absolute top-2 left-2 bg-purple-600 text-white p-1 rounded-full">
                            <x-heroicon-s-check class="w-5 h-5" />
                        </div>
                    @endif
                </div>
            @endforeach
        </div>

        {{-- Pagination --}}
        <div>
            {{ $templates->links() }}
        </div>
    </div>
</div>