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

    public string $value = '';

    public string $min_order_amount = '0.00';

    public ?string $max_discount = null;

    public ?string $expires_at = null;

    public ?int $usage_limit = null;

    public int $used_count = 0;

    public bool $is_active = true;

    public function mount(mixed $coupon = null)
    {
        if ($coupon instanceof Coupon && $coupon->exists) {
            $this->coupon = $coupon;
        } elseif (is_numeric($coupon) || is_string($coupon)) {
            $this->coupon = Coupon::query()->find($coupon) ?? new Coupon();
        } elseif (request()->route('coupon')) {
            $routeVal = request()->route('coupon');
            $this->coupon = $routeVal instanceof Coupon ? $routeVal : (Coupon::query()->find($routeVal) ?? new Coupon());
        } elseif (request()->has('coupon')) {
            $this->coupon = Coupon::query()->find(request('coupon')) ?? new Coupon();
        } elseif (request()->has('id')) {
            $this->coupon = Coupon::query()->find(request('id')) ?? new Coupon();
        } else {
            $this->coupon = new Coupon();
        }

        if (! $this->coupon || ! $this->coupon->exists) {
            session()->flash('error', __('Coupon not found.'));
            return redirect()->route('admin.coupons');
        }

        $this->code = (string) ($this->coupon->code ?? '');
        $this->type = in_array($this->coupon->type, ['percentage', 'fixed'], true) ? $this->coupon->type : 'percentage';
        $this->value = $this->coupon->value !== null ? (string) $this->coupon->value : '';
        $this->min_order_amount = $this->coupon->min_order_amount !== null ? (string) $this->coupon->min_order_amount : '0.00';
        $this->max_discount = $this->coupon->max_discount !== null ? (string) $this->coupon->max_discount : null;
        $this->expires_at = $this->coupon->expires_at?->format('Y-m-d\TH:i');
        $this->usage_limit = $this->coupon->usage_limit;
        $this->used_count = (int) ($this->coupon->used_count ?? 0);
        $this->is_active = (bool) ($this->coupon->is_active ?? true);

        return null;
    }

    protected function rules(): array
    {
        $couponId = $this->coupon?->id;

        return [
            'code' => ['required', 'string', 'max:64', Rule::unique('coupons', 'code')->ignore($couponId)],
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

        if (! $this->coupon || ! $this->coupon->exists) {
            session()->flash('error', __('Coupon record not found.'));
            return;
        }

        $this->coupon->update([
            'code' => strtoupper(trim($this->code)),
            'type' => $this->type,
            'value' => $this->value,
            'min_order_amount' => $this->min_order_amount ?: 0.00,
            'max_discount' => ($this->type === 'percentage' && $this->max_discount !== null && $this->max_discount !== '') ? $this->max_discount : null,
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
            'heading' => __('Edit Coupon :code', ['code' => $this->coupon?->code ?? '']),
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
