<?php

namespace App\Livewire\Admin;

use App\Models\Coupon;
use Illuminate\Validation\Rule;
use Livewire\Component;

class AdminCouponEdit extends Component
{
    public Coupon $coupon;

    public string $name = '';

    public string $code = '';

    public int $quantity = 1;

    public ?int $limit_per_user = null;

    public ?string $start_at = null;

    public ?string $expires_at = null;

    public string $discount = '0';

    public bool $is_published = false;

    public function mount(Coupon $coupon): void
    {
        $this->coupon = $coupon;
        $this->name = $coupon->name;
        $this->code = $coupon->code;
        $this->quantity = (int) $coupon->quantity;
        $this->limit_per_user = $coupon->limit_per_user;

        $this->start_at = $coupon->start_at?->format('Y-m-d\TH:i');
        $this->expires_at = $coupon->expires_at?->format('Y-m-d\TH:i');

        $this->discount = (string) $coupon->discount;
        $this->is_published = (bool) $coupon->is_published;
    }

    protected function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'code' => ['required', 'string', 'max:64', Rule::unique('coupons', 'code')->ignore($this->coupon->id)],
            'quantity' => ['required', 'integer', 'min:1'],
            'limit_per_user' => ['nullable', 'integer', 'min:1'],

            'start_at' => ['nullable', 'date'],
            'expires_at' => ['nullable', 'date', 'after_or_equal:start_at'],

            'discount' => ['required', 'numeric', 'min:0'],

            'is_published' => ['boolean'],
        ];
    }

    public function save(): void
    {
        $this->validate();

        $this->coupon->update([
            'name' => $this->name,
            'code' => strtoupper(trim($this->code)),
            'quantity' => $this->quantity,
            'limit_per_user' => $this->limit_per_user,

            'start_at' => $this->start_at ?: null,
            'expires_at' => $this->expires_at ?: null,

            'discount' => $this->discount,

            'is_published' => $this->is_published,
        ]);

        session()->flash('status', __('Coupon updated.'));
    }

    public function render()
    {
        return view('livewire.admin.admin-coupon-form', [
            'heading' => __('Edit coupon'),
            'submitLabel' => __('Save'),
        ]);
    }
}
