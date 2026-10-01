<?php

namespace App\Livewire\Admin;

use App\Models\Moderation;
use Livewire\Component;

class AdminModerations extends Component
{
    public string $filter = 'pending';

    public function render()
    {
        $query = Moderation::query()
            ->with('moderator')
            ->with('moderatable')
            ->orderBy('created_at');

        if ($this->filter === 'pending') {
            $query->where('status', 'pending');
        } elseif ($this->filter === 'processed') {
            $query->whereIn('status', ['approved', 'rejected']);
        }

        $moderations = $query->get();

        return view('livewire.admin.admin-moderations', [
            'moderations' => $moderations,
        ]);
    }
}
