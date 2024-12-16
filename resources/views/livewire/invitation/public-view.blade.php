<div class="min-h-screen bg-white" x-data="{ showRsvp: @entangle('showRsvpModal') }">
    {{-- Cover Image/Header Section --}}
    <div class="relative h-96 bg-gradient-to-b from-purple-50 to-white">
        <div class="absolute inset-0">
            @if($invitation->template->thumbnail)
                <img 
                    src="{{ $invitation->template->getFirstMediaUrl('thumbnails') }}" 
                    alt="{{ $invitation->title }}"
                    class="w-full h-full object-cover"
                >
            @endif
            <div class="absolute inset-0 bg-gradient-to-t from-white"></div>
        </div>

        <div class="relative max-w-7xl mx-auto px-4 py-16 sm:px-6 sm:py-24 lg:px-8 text-center">
            <h1 class="text-4xl font-bold tracking-tight text-gray-900 sm:text-5xl lg:text-6xl">
                {{ $invitation->title }}
            </h1>
            <p class="mt-6 text-xl text-gray-500">
                {{ $invitation->event_date->format('d.m.Y') }}
            </p>
        </div>
    </div>

    {{-- Timeline Section --}}
    <div class="max-w-2xl mx-auto px-4 py-16 sm:px-6 lg:max-w-7xl lg:px-8">
        <div class="relative">
            @foreach($invitation->content['timeline'] ?? [] as $event)
                <x-invitation.timeline-event 
                    :time="$event['time']"
                    :title="$event['title']"
                    :description="$event['description'] ?? null"
                    :icon="$event['icon'] ?? 'clock'"
                />
            @endforeach
        </div>
    </div>

    {{-- Action Buttons --}}
    <div class="fixed bottom-0 inset-x-0 pb-2 sm:pb-5">
        <div class="max-w-xl mx-auto px-2 sm:px-6">
            <div class="p-2 rounded-lg bg-white shadow-lg sm:p-3">
                <div class="flex items-center justify-around gap-x-3">
                    <button
                        type="button"
                        class="flex-1 flex items-center justify-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-purple-600 hover:bg-purple-700"
                        x-on:click="showRsvp = true"
                    >
                        <x-heroicon-s-check class="h-5 w-5 ml-2" />
                        אישור הגעה
                    </button>

                    <button
                        type="button"
                        class="flex-1 flex items-center justify-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50"
                        wire:click="addToCalendar"
                    >
                        <x-heroicon-s-calendar class="h-5 w-5 ml-2" />
                        הוסף ליומן{{-- resources/views/livewire/invitation/public-view.blade.php --}}
                        <div>
                            @if($invitation)
                                <div class="min-h-screen bg-white">
                                    {{-- Cover Image/Header Section --}}
                                    <div class="relative h-96 bg-gradient-to-b from-purple-50 to-white">
                                        <div class="absolute inset-0">
                                            <div class="absolute inset-0 bg-gradient-to-t from-white"></div>
                                        </div>
                        
                                        <div class="relative max-w-7xl mx-auto px-4 py-16 sm:px-6 sm:py-24 lg:px-8 text-center">
                                            <h1 class="text-4xl font-bold tracking-tight text-gray-900 sm:text-5xl lg:text-6xl">
                                                {{ $invitation->title }}
                                            </h1>
                                            <p class="mt-6 text-xl text-gray-500">
                                                {{ $invitation->event_date ? $invitation->event_date->format('d.m.Y') : '' }}
                                            </p>
                                        </div>
                                    </div>
                        
                                    {{-- Basic details for testing --}}
                                    <div class="max-w-2xl mx-auto px-4 py-16 sm:px-6 lg:max-w-7xl lg:px-8">
                                        <div class="text-center">
                                            <p class="text-lg">
                                                מיקום: {{ $invitation->venue_name }}
                                            </p>
                                            <p class="text-lg mt-2">
                                                כתובת: {{ $invitation->venue_address }}
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            @else
                                <div class="p-4 text-center">
                                    No invitation data available
                                </div>
                            @endif
                        </div>
                    </button>

                    <button
                        type="button"
                        class="flex-1 flex items-center justify-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50"
                        wire:click="shareWhatsApp"
                    >
                        <x-heroicon-s-share class="h-5 w-5 ml-2" />
                        שתף
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- RSVP Modal --}}
    <livewire:invitation.rsvp-form 
        :invitation="$invitation" 
        show="showRsvp"
    />
</div>