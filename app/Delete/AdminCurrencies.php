<?php

namespace App\Livewire\Admin\Settings;

use App\Models\Currency;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Livewire\Component;

class AdminCurrencies extends Component
{
    public bool $showModal = false;

    public ?int $editingId = null;

    public string $name = '';

    public string $code = '';

    public string $slug = '';

    public string $symbol = '';

    public string $symbol_position = 'before';

    public string $thousands_separator = ',';

    public string $decimal_separator = '.';

    public int $decimal_multiplier = 100;

    public bool $is_default = false;

    public bool $is_active = true;

    public ?string $payment_gateway = null;

    // Manual payment / bank details
    public bool $show_manual_payment = false;
    public string $bank_name = '';
    public string $bank_account_number = '';
    public string $account_name = '';
    public string $swift_code = '';
    public string $bank_instructions = '';

    public function openCreate(): void
    {
        $this->editingId = null;
        $this->name = '';
        $this->code = '';
        $this->slug = '';
        $this->symbol = '';
        $this->symbol_position = 'before';
        $this->thousands_separator = ',';
        $this->decimal_separator = '.';
        $this->decimal_multiplier = 100;
        $this->is_default = false;
        $this->is_active = true;
        $this->payment_gateway = null;
        $this->show_manual_payment = false;
        $this->bank_name = '';
        $this->bank_account_number = '';
        $this->account_name = '';
        $this->swift_code = '';
        $this->bank_instructions = '';
        $this->resetErrorBag();
        $this->showModal = true;
    }

    public function openEdit(int $id): void
    {
        $c = Currency::query()->findOrFail($id);
        $this->editingId = $c->id;
        $this->name = $c->name;
        $this->code = $c->code;
        $this->slug = (string) ($c->slug ?? '');
        $this->symbol = (string) ($c->symbol ?? '');
        $this->symbol_position = $c->symbol_position ?? 'before';
        $this->thousands_separator = (string) ($c->thousands_separator ?? ',');
        $this->decimal_separator = (string) ($c->decimal_separator ?? '.');
        $this->decimal_multiplier = (int) ($c->decimal_multiplier ?? 100);
        $this->is_default = (bool) $c->is_default;
        $this->is_active = (bool) $c->is_active;
        $this->payment_gateway = $c->payment_gateway;

        $bankDetails = $c->bank_details ?? [];
        $this->show_manual_payment = ! empty($bankDetails);
        $this->bank_name = $bankDetails['bank_name'] ?? '';
        $this->bank_account_number = $bankDetails['bank_account_number'] ?? '';
        $this->account_name = $bankDetails['account_name'] ?? '';
        $this->swift_code = $bankDetails['swift_code'] ?? '';
        $this->bank_instructions = $bankDetails['instructions'] ?? '';

        $this->resetErrorBag();
        $this->showModal = true;
    }

    public function closeModal(): void
    {
        $this->showModal = false;
        $this->editingId = null;
    }

    public function saveCurrency(): void
    {
        $codeRule = Rule::unique('currencies', 'code');
        $slugRule = Rule::unique('currencies', 'slug');
        if ($this->editingId) {
            $codeRule = $codeRule->ignore($this->editingId);
            $slugRule = $slugRule->ignore($this->editingId);
        }

        $this->validate([
            'name' => ['required', 'string', 'max:255'],
            'code' => ['required', 'string', 'max:8', $codeRule],
            'slug' => ['nullable', 'string', 'max:255', $slugRule],
            'symbol' => ['nullable', 'string', 'max:8'],
            'symbol_position' => ['required', Rule::in(['before', 'after'])],
            'thousands_separator' => ['required', 'string', 'max:4'],
            'decimal_separator' => ['required', 'string', 'max:4'],
            'decimal_multiplier' => ['required', 'integer', 'min:1'],
            'is_default' => ['boolean'],
            'is_active' => ['boolean'],
            'payment_gateway' => ['nullable', 'string', Rule::in(['paystack', 'flutterwave'])],
            'bank_name' => [$this->show_manual_payment ? 'required' : 'nullable', 'string', 'max:255'],
            'bank_account_number' => [$this->show_manual_payment ? 'required' : 'nullable', 'string', 'max:255'],
            'account_name' => [$this->show_manual_payment ? 'required' : 'nullable', 'string', 'max:255'],
            'swift_code' => ['nullable', 'string', 'max:255'],
            'bank_instructions' => ['nullable', 'string'],
        ]);

        $slug = $this->slug !== '' ? Str::slug($this->slug) : Str::slug($this->code);

        $payload = [
            'name' => $this->name,
            'code' => strtoupper($this->code),
            'slug' => $slug,
            'symbol' => $this->symbol,
            'symbol_position' => $this->symbol_position,
            'thousands_separator' => $this->thousands_separator,
            'decimal_separator' => $this->decimal_separator,
            'decimal_multiplier' => $this->decimal_multiplier,
            'is_active' => $this->is_active,
            'payment_gateway' => $this->payment_gateway !== '' ? $this->payment_gateway : null,
            'bank_details' => $this->show_manual_payment ? [
                'bank_name' => $this->bank_name,
                'bank_account_number' => $this->bank_account_number,
                'account_name' => $this->account_name,
                'swift_code' => $this->swift_code,
                'instructions' => $this->bank_instructions,
            ] : null,
        ];

        if ($this->is_default) {
            Currency::query()->update(['is_default' => false]);
            $payload['is_default'] = true;
        } else {
            $payload['is_default'] = false;
        }

        if ($this->editingId) {
            Currency::query()->whereKey($this->editingId)->update($payload);
        } else {
            Currency::query()->create($payload);
        }

        session()->flash('status', __('Currency saved.'));
        $this->closeModal();
    }

    public function setDefault(int $id): void
    {
        Currency::query()->update(['is_default' => false]);
        Currency::query()->whereKey($id)->update(['is_default' => true]);
        session()->flash('status', __('Default currency updated.'));
    }

    public function deleteCurrency(int $id): void
    {
        $c = Currency::query()->findOrFail($id);
        if ($c->prices()->exists() || $c->payments()->exists()) {
            session()->flash('error', __('Cannot delete a currency referenced by prices or payments.'));

            return;
        }
        if ($c->is_default) {
            session()->flash('error', __('Unset default before deleting.'));

            return;
        }
        $c->delete();
        session()->flash('status', __('Currency deleted.'));
    }

    public function render()
    {
        $currencies = Currency::query()->orderByDesc('is_default')->orderBy('code')->get();

        return view('livewire.admin.settings.admin-currencies', [
            'currencies' => $currencies,
        ]);
    }
}
