<?php

namespace App\Livewire\Admin;

use App\Models\Coupon;
use Carbon\Carbon;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.dash')]
#[Title('Create Coupon — Admin Control Center')]
class AdminCouponCreate extends Component
{
    public string $code = '';

    public string $type = 'percentage';

    public string $value = '10.00';

    public string $min_order_amount = '0.00';

    public ?string $max_discount = null;

    public ?string $expires_at = null;

    public ?int $usage_limit = null;

    public bool $is_active = true;

    public function generateCode(): void
    {
        $this->code = 'PP-' . strtoupper(Str::random(6));
    }

    protected function rules(): array
    {
        return [
            'code' => ['required', 'string', 'max:64', 'unique:coupons,code'],
            'type' => ['required', Rule::in(['percentage', 'fixed'])],
            'value' => ['required', 'numeric', 'min:0.01'],
            'min_order_amount' => ['required', 'numeric', 'min:0'],
            'max_discount' => ['nullable', 'numeric', 'min:0'],
            'expires_at' => ['nullable', 'date'],
            'usage_limit' => ['nullable', 'integer', 'min:1'],
            'is_active' => ['boolean'],
        ];
    }

    public function save(): mixed
    {
        $this->validate();

        $coupon = Coupon::query()->create([
            'code' => strtoupper(trim($this->code)),
            'type' => $this->type,
            'value' => $this->value,
            'min_order_amount' => $this->min_order_amount ?: 0.00,
            'max_discount' => $this->max_discount !== null && $this->max_discount !== '' ? $this->max_discount : null,
            'expires_at' => $this->expires_at ? Carbon::parse($this->expires_at) : null,
            'usage_limit' => $this->usage_limit ?: null,
            'used_count' => 0,
            'is_active' => $this->is_active,
        ]);

        session()->flash('status', __('Coupon :code created successfully.', ['code' => $coupon->code]));

        return redirect()->route('admin.coupons');
    }

    public function render()
    {
        return view('livewire.admin.admin-coupon-form', [
            'heading' => __('Create Coupon'),
            'subheading' => __('Define discount percentage or fixed deduction, order minimums, and usage quotas.'),
            'submitLabel' => __('Create Coupon'),
            'isEditing' => false,
            'code' => $this->code,
            'type' => $this->type,
            'value' => $this->value,
            'min_order_amount' => $this->min_order_amount,
            'max_discount' => $this->max_discount,
            'expires_at' => $this->expires_at,
            'usage_limit' => $this->usage_limit,
            'is_active' => $this->is_active,
        ]);
    }
}
