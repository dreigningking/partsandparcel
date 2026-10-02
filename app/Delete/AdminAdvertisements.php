<?php

namespace App\Livewire\Admin;

use App\Models\Advertisement;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;

class AdminAdvertisements extends Component
{
    use WithPagination, WithFileUploads;

    public string $search = '';
    public string $placementFilter = '';
    public string $statusFilter = '';

    public ?int $selectedAdId = null;
    
    // For creating new ad
    public bool $showCreateModal = false;
    public string $new_company = '';
    public string $new_email = '';
    public string $new_placement = 'homepage_banner';
    public string $new_amount = '';
    public string $new_description = '';
    public string $new_start_date = '';
    public string $new_end_date = '';
    public string $new_payment_status = 'completed';
    public $new_image;
    
    // For editing dates
    public string $edit_start_date = '';
    public string $edit_end_date = '';

    public function openCreateAd(): void
    {
        $this->reset([
            'new_company', 'new_email', 'new_amount', 'new_description',
            'new_start_date', 'new_end_date', 'new_image'
        ]);
        $this->new_placement = 'homepage_banner';
        $this->new_payment_status = 'completed';
        $this->showCreateModal = true;
        $this->resetErrorBag();
    }

    public function closeCreateAd(): void
    {
        $this->showCreateModal = false;
    }

    public function createAd(): void
    {
        $this->validate([
            'new_company' => ['required', 'string', 'max:255'],
            'new_email' => ['required', 'email', 'max:255'],
            'new_placement' => ['required', 'in:homepage_banner,blog_sidebar,blog_post'],
            'new_amount' => ['required', 'numeric', 'min:0'],
            'new_description' => ['nullable', 'string'],
            'new_start_date' => ['nullable', 'date'],
            'new_end_date' => ['nullable', 'date', 'after_or_equal:new_start_date'],
            'new_payment_status' => ['required', 'in:pending,completed'],
            'new_image' => ['required', 'image', 'max:2048'],
        ]);

        $imagePath = $this->new_image->store('advertisements', 'public');

        Advertisement::create([
            'company' => $this->new_company,
            'email' => $this->new_email,
            'placement' => $this->new_placement,
            'amount' => $this->new_amount,
            'description' => $this->new_description,
            'start_date' => $this->new_start_date ?: null,
            'end_date' => $this->new_end_date ?: null,
            'payment_status' => $this->new_payment_status,
            'payment_method' => 'manual',
            'image' => $imagePath,
        ]);

        session()->flash('status', __('Advertisement created successfully.'));
        $this->closeCreateAd();
    }

    public function showAd(int $id): void
    {
        $ad = Advertisement::query()->findOrFail($id);
        $this->selectedAdId = $ad->id;
        $this->edit_start_date = $ad->start_date ? $ad->start_date->format('Y-m-d') : '';
        $this->edit_end_date = $ad->end_date ? $ad->end_date->format('Y-m-d') : '';
        $this->resetErrorBag();
    }

    public function closeAd(): void
    {
        $this->selectedAdId = null;
    }

    public function confirmPayment(int $id): void
    {
        $ad = Advertisement::query()->findOrFail($id);
        $ad->payment_status = 'completed';
        $ad->save();

        session()->flash('status', __('Advertisement payment verified and completed.'));
        $this->selectedAdId = $ad->id; // Refresh view
    }

    public function saveDates(): void
    {
        $this->validate([
            'edit_start_date' => ['required', 'date'],
            'edit_end_date' => ['required', 'date', 'after_or_equal:edit_start_date'],
        ]);

        $ad = Advertisement::query()->findOrFail($this->selectedAdId);
        $ad->start_date = $this->edit_start_date;
        $ad->end_date = $this->edit_end_date;
        $ad->save();

        session()->flash('status', __('Advertisement dates updated.'));
    }

    public function deleteAd(int $id): void
    {
        $ad = Advertisement::query()->findOrFail($id);
        $ad->delete();

        session()->flash('status', __('Advertisement deleted.'));
        $this->closeAd();
    }

    public function render()
    {
        $ads = Advertisement::query()
            ->when($this->search !== '', function ($query) {
                $query->where(function ($inner) {
                    $inner->where('company', 'like', '%' . $this->search . '%')
                        ->orWhere('email', 'like', '%' . $this->search . '%')
                        ->orWhere('description', 'like', '%' . $this->search . '%');
                });
            })
            ->when($this->placementFilter !== '', fn ($query) => $query->where('placement', $this->placementFilter))
            ->when($this->statusFilter !== '', fn ($query) => $query->where('payment_status', $this->statusFilter))
            ->latest()
            ->paginate(10);

        $selectedAd = $this->selectedAdId ? Advertisement::query()->find($this->selectedAdId) : null;

        return view('livewire.admin.admin-advertisements', [
            'ads' => $ads,
            'selectedAd' => $selectedAd,
            'placements' => ['homepage_banner', 'blog_sidebar', 'blog_post'],
        ]);
    }
}
