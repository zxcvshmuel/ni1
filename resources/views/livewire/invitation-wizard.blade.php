<div class="min-h-screen bg-gray-50" x-data>
    {{-- Progress Steps --}}
    <div class="bg-white border-b">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <nav class="flex justify-between items-center py-4" aria-label="Progress">
                <ol class="flex items-center w-full">
                    @foreach($steps as $index => $step)
                        <li @class([
                            'relative flex-1',
                            'pl-8' => !$loop->first,
                            'pl-4' => $loop->first,
                        ])>
                            <div class="flex items-center">
                                <div @class([
                                    'relative flex h-8 w-8 items-center justify-center rounded-full',
                                    'bg-purple-600 text-white' => $currentStep === $index,
                                    'bg-purple-100 text-purple-600' => $currentStep > $index,
                                    'bg-gray-100 text-gray-400' => $currentStep < $index,
                                ])>
                                    <x-dynamic-component 
                                        :component="'heroicon-s-'.$step['icon']" 
                                        class="w-5 h-5"
                                    />
                                </div>
                                <div class="mr-4 text-sm font-medium">
                                    {{ $step['title'] }}
                                </div>
                            </div>
                        </li>
                    @endforeach
                </ol>
            </nav>
        </div>
    </div>

    {{-- Step Content --}}
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        @switch($currentStep)
            @case(1)
                <livewire:invitation-wizard.template-step />
                @break
            @case(2)
                <livewire:invitation-wizard.music-step />
                @break
            @case(3)
                <livewire:invitation-wizard.effects-step />
                @break
            @case(4)
                <livewire:invitation-wizard.details-step />
                @break
            @case(5)
                <livewire:invitation-wizard.preview-step />
                @break
            @case(6)
                <livewire:invitation-wizard.payment-step />
                @break
        @endswitch
    </div>
</div>