<?php

namespace App\Livewire\Admin;

use App\Models\Faq;
use Livewire\Component;
use Livewire\WithPagination;

class AdminFaqs extends Component
{
    use WithPagination;

    public bool $showModal = false;

    public ?int $editingId = null;

    public string $question = '';

    public string $answer = '';

    public bool $is_active = true;

    public bool $show_on_contact_page = false;

    protected function rules(): array
    {
        return [
            'question'              => ['required', 'string', 'max:500'],
            'answer'                => ['required', 'string'],
            'is_active'             => ['boolean'],
            'show_on_contact_page'  => ['boolean'],
        ];
    }

    public function openCreate(): void
    {
        $this->editingId = null;
        $this->question = '';
        $this->answer = '';
        $this->is_active = true;
        $this->show_on_contact_page = false;
        $this->resetErrorBag();
        $this->showModal = true;
    }

    public function openEdit(int $id): void
    {
        $faq = Faq::query()->findOrFail($id);
        $this->editingId = $faq->id;
        $this->question = $faq->question;
        $this->answer = $faq->answer;
        $this->is_active = $faq->is_active;
        $this->show_on_contact_page = $faq->show_on_contact_page;
        $this->resetErrorBag();
        $this->showModal = true;
    }

    public function closeModal(): void
    {
        $this->showModal = false;
        $this->editingId = null;
    }

    public function save(): void
    {
        $this->validate();

        $payload = [
            'question'             => $this->question,
            'answer'               => $this->answer,
            'is_active'            => $this->is_active,
            'show_on_contact_page' => $this->show_on_contact_page,
        ];

        if ($this->editingId) {
            Faq::query()->whereKey($this->editingId)->update($payload);
        } else {
            Faq::query()->create($payload);
        }

        session()->flash('status', __('FAQ saved.'));
        $this->closeModal();
    }

    public function toggleActive(int $id): void
    {
        $faq = Faq::query()->findOrFail($id);
        $faq->update(['is_active' => ! $faq->is_active]);
        session()->flash('status', $faq->is_active ? __('FAQ activated.') : __('FAQ deactivated.'));
    }

    public function delete(int $id): void
    {
        Faq::query()->whereKey($id)->delete();
        session()->flash('status', __('FAQ deleted.'));
    }

    public function render()
    {
        return view('livewire.admin.admin-faqs', [
            'faqs' => Faq::query()->latest()->paginate(15),
        ]);
    }
}
