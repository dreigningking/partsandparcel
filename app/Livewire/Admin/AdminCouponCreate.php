<?php

namespace App\Livewire\Admin;

use App\Models\Coupon;
use Livewire\Component;

class AdminCouponCreate extends Component
{
    public string $name = '';

    public string $code = '';

    public int $quantity = 1;

    public ?int $limit_per_user = null;

    public ?string $start_at = null;

    public ?string $expires_at = null;

    public string $discount = '0';

    public bool $is_published = false;

    protected function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'code' => ['required', 'string', 'max:64', 'unique:coupons,code'],
            'quantity' => ['required', 'integer', 'min:1'],
            'limit_per_user' => ['nullable', 'integer', 'min:1'],
            'start_at' => ['nullable', 'date'],
            'expires_at' => ['nullable', 'date', 'after_or_equal:start_at'],
            'discount' => ['required', 'numeric', 'min:0'],
            'is_published' => ['boolean'],
        ];
    }

    public function save(): mixed
    {
        $this->validate();

        $coupon = Coupon::query()->create([
            'name' => $this->name,
            'code' => strtoupper(trim($this->code)),
            'quantity' => $this->quantity,
            'limit_per_user' => $this->limit_per_user,
            'start_at' => $this->start_at ?: null,
            'expires_at' => $this->expires_at ?: null,
            'discount' => $this->discount,
            'eligible_items' => null,
            'is_published' => $this->is_published,
        ]);

        session()->flash('status', __('Coupon created.'));

        return redirect()->route('admin.coupons.edit', ['coupon' => $coupon->id]);
    }

    public function render()
    {
        return view('livewire.admin.admin-coupon-form', [
            'heading' => __('Create coupon'),
            'submitLabel' => __('Create'),
        ]);
    }
}
