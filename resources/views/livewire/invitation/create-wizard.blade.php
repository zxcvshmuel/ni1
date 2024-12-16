<div class="min-h-screen bg-gray-50">
    {{-- Header עם הצעדים --}}
    <div class="bg-white shadow">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <nav class="flex justify-between items-center py-4" aria-label="Progress">
                <ol class="flex items-center w-full">
                    @foreach ($steps as $index => $step)
                        <li @class([
                            'relative flex-1',
                            'pl-4' => !$loop->first,
                        ])>
                            <div class="flex items-center">
                                <div @class([
                                    'relative flex h-8 w-8 items-center justify-center rounded-full',
                                    'bg-purple-600 text-white' => $currentStep === $index,
                                    'bg-purple-100 text-purple-600' => $currentStep > $index,
                                    'bg-gray-100 text-gray-400' => $currentStep < $index,
                                ])>
                                    <x-heroicon-s-{{ $step['icon'] }} class="w-5 h-5" />
                                </div>
                                <span @class([
                                    'mr-4 text-sm font-medium',
                                    'text-purple-600' => $currentStep === $index,
                                    'text-gray-900' => $currentStep > $index,
                                    'text-gray-400' => $currentStep < $index,
                                ])>
                                    {{ $step['title'] }}
                                </span>
                            </div>
                        </li>
                    @endforeach
                </ol>
            </nav>
        </div>
    </div>

    {{-- תוכן הצעד הנוכחי --}}
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        @switch($currentStep)
            @case(1)
                <livewire:invitation.wizard.template-step />
                @break
            @case(2)
                <livewire:invitation.wizard.music-step />
                @break
            @case(3)
                <livewire:invitation.wizard.effects-step />
                @break
            @case(4)
                <livewire:invitation.wizard.details-step />
                @break
            @case(5)
                <livewire:invitation.wizard.preview-step />
                @break
            @case(6)
                <livewire:invitation.wizard.payment-step />
                @break
        @endswitch
    </div>

    {{-- כפתורי ניווט --}}
    <div class="fixed bottom-0 inset-x-0 bg-white border-t">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4 flex justify-between">
            <button 
                type="button"
                @class([
                    'px-4 py-2 rounded-md text-sm font-medium',
                    'text-gray-700 bg-white border border-gray-300 hover:bg-gray-50' => $currentStep > 1,
                    'text-gray-300 cursor-not-allowed' => $currentStep === 1,
                ])
                wire:click="previousStep"
                @if($currentStep === 1) disabled @endif
            >
                הקודם
            </button>

            <button 
                type="button"
                @class([
                    'px-4 py-2 rounded-md text-sm font-medium',
                    'bg-purple-600 text-white hover:bg-purple-700' => $currentStep < count($steps),
                    'text-gray-300 cursor-not-allowed' => $currentStep === count($steps),
                ])
                wire:click="nextStep"
                @if($currentStep === count($steps)) disabled @endif
            >
                {{ $currentStep === count($steps) ? 'סיום' : 'הבא' }}
            </button>
        </div>
    </div>
</div>