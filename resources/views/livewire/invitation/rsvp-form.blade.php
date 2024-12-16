<div
    x-show="showRsvp"
    class="relative z-50"
    x-transition:enter="ease-out duration-300"
    x-transition:enter-start="opacity-0"
    x-transition:enter-end="opacity-100"
    x-transition:leave="ease-in duration-200"
    x-transition:leave-start="opacity-100"
    x-transition:leave-end="opacity-0"
>
    <div class="fixed inset-0 bg-gray-500 bg-opacity-75"></div>

    <div class="fixed inset-0 z-50 overflow-y-auto">
        <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
            <div
                x-show="showRsvp"
                x-transition:enter="ease-out duration-300"
                x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                x-transition:leave="ease-in duration-200"
                x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                class="relative transform overflow-hidden rounded-lg bg-white px-4 pb-4 pt-5 text-right shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-lg sm:p-6"
            >
                <div class="absolute left-0 top-0 hidden pl-4 pt-4 sm:block">
                    <button 
                        type="button" 
                        class="rounded-md bg-white text-gray-400 hover:text-gray-500"
                        x-on:click="showRsvp = false"
                    >
                        <span class="sr-only">סגור</span>
                        <x-heroicon-s-x-mark class="h-6 w-6" />
                    </button>
                </div>

                <form wire:submit.prevent="submit">
                    <div class="space-y-6">
                        {{-- Form Fields --}}
                        <div>
                            <x-input-label for="name" required>שם מלא</x-input-label>
                            <x-text-input 
                                id="name"
                                type="text"
                                wire:model.defer="name"
                                class="mt-1 block w-full"
                                required
                            />
                            <x-input-error :messages="$errors->get('name')" class="mt-2" />
                        </div>

                        {{-- More fields... --}}

                        <div class="mt-5 sm:mt-6 flex flex-row-reverse gap-3">
                            <x-primary-button type="submit" wire:loading.attr="disabled">
                                <span wire:loading.remove>שלח אישור</span>
                                <span wire:loading>שולח...</span>
                            </x-primary-button>
                            
                            <x-secondary-button type="button" x-on:click="showRsvp = false">
                                ביטול
                            </x-secondary-button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>