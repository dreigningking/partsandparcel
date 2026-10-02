<?php

namespace App\Livewire\Admin;

use App\Models\Coupon;
use Carbon\Carbon;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.dash')]
#[Title('Edit Coupon — Admin Control Center')]
class AdminCouponEdit extends Component
{
    public Coupon $coupon;

    public string $code = '';

    public string $type = 'percentage';

    public string $value = '10.00';

    public string $min_order_amount = '0.00';

    public ?string $max_discount = null;

    public ?string $expires_at = null;

    public ?int $usage_limit = null;

    public int $used_count = 0;

    public bool $is_active = true;

    public function mount(Coupon $coupon): void
    {
        $this->coupon = $coupon;
        $this->code = $coupon->code;
        $this->type = $coupon->type;
        $this->value = (string) $coupon->value;
        $this->min_order_amount = (string) $coupon->min_order_amount;
        $this->max_discount = $coupon->max_discount !== null ? (string) $coupon->max_discount : null;
        $this->expires_at = $coupon->expires_at?->format('Y-m-d\TH:i');
        $this->usage_limit = $coupon->usage_limit;
        $this->used_count = (int) $coupon->used_count;
        $this->is_active = (bool) $coupon->is_active;
    }

    protected function rules(): array
    {
        return [
            'code' => ['required', 'string', 'max:64', Rule::unique('coupons', 'code')->ignore($this->coupon->id)],
            'type' => ['required', Rule::in(['percentage', 'fixed'])],
            'value' => ['required', 'numeric', 'min:0.01'],
            'min_order_amount' => ['required', 'numeric', 'min:0'],
            'max_discount' => ['nullable', 'numeric', 'min:0'],
            'expires_at' => ['nullable', 'date'],
            'usage_limit' => ['nullable', 'integer', 'min:1'],
            'used_count' => ['required', 'integer', 'min:0'],
            'is_active' => ['boolean'],
        ];
    }

    public function save(): void
    {
        $this->validate();

        $this->coupon->update([
            'code' => strtoupper(trim($this->code)),
            'type' => $this->type,
            'value' => $this->value,
            'min_order_amount' => $this->min_order_amount ?: 0.00,
            'max_discount' => $this->max_discount !== null && $this->max_discount !== '' ? $this->max_discount : null,
            'expires_at' => $this->expires_at ? Carbon::parse($this->expires_at) : null,
            'usage_limit' => $this->usage_limit ?: null,
            'used_count' => $this->used_count,
            'is_active' => $this->is_active,
        ]);

        session()->flash('status', __('Coupon :code updated successfully.', ['code' => $this->coupon->code]));
    }

    public function render()
    {
        return view('livewire.admin.admin-coupon-form', [
            'heading' => __('Edit Coupon :code', ['code' => $this->coupon->code]),
            'subheading' => __('Update discount rules, order eligibility thresholds, and usage caps.'),
            'submitLabel' => __('Save Changes'),
            'isEditing' => true,
            'code' => $this->code,
            'type' => $this->type,
            'value' => $this->value,
            'min_order_amount' => $this->min_order_amount,
            'max_discount' => $this->max_discount,
            'expires_at' => $this->expires_at,
            'usage_limit' => $this->usage_limit,
            'used_count' => $this->used_count,
            'is_active' => $this->is_active,
        ]);
    }
}
