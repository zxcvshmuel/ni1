<?php

namespace App\Livewire\Invitation\Wizard;

use App\Models\Template;
use App\Models\TemplateCategory;
use Livewire\Component;
use Livewire\WithPagination;

class TemplateStep extends Component
{
    use WithPagination;

    public ?string $search = '';
    public ?int $selectedCategory = null;
    public ?int $selectedTemplate = null;

    protected $queryString = [
        'search' => ['except' => ''],
        'selectedCategory' => ['except' => ''],
    ];

    public function selectTemplate($templateId)
    {
        $this->selectedTemplate = $templateId;
        $this->dispatch('template-selected', templateId: $templateId);
    }

    public function getTemplatesProperty()
    {
        return Template::query()
            ->where('is_active', true)
            ->when($this->search, function ($query) {
                $query->whereJsonContains('name->he', $this->search)
                    ->orWhereJsonContains('name->en', $this->search);
            })
            ->when($this->selectedCategory, function ($query) {
                $query->where('category_id', $this->selectedCategory);
            })
            ->paginate(6);
    }

    public function render()
    {
        return view('livewire.invitation.wizard.template-step', [
            'templates' => $this->templates,
            'categories' => TemplateCategory::whereNull('parent_id')->get()
        ]);
    }
}