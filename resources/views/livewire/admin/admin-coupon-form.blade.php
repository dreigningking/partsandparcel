<div class="space-y-6 max-w-4xl">

    <!-- PAGE HEADER -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-2 text-xs font-bold text-slate-400 uppercase tracking-wider mb-1">
                <a href="{{ route('admin.dashboard') }}" class="hover:text-pp-600 transition">Admin Console</a>
                <span>/</span>
                <a href="{{ route('admin.coupons') }}" class="hover:text-pp-600 transition">Coupons</a>
                <span>/</span>
                <span class="text-slate-600 dark:text-slate-300">{{ $isEditing ? 'Edit Coupon' : 'Create Coupon' }}</span>
            </div>
            <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-950 dark:text-white tracking-tight">
                {{ $heading }}
            </h1>
            <p class="text-xs sm:text-sm text-slate-500 dark:text-slate-400 mt-1">
                {{ $subheading }}
            </p>
        </div>

        <div class="flex items-center gap-3 self-start sm:self-auto">
            <a
                href="{{ route('admin.coupons') }}"
                class="px-4 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 hover:bg-slate-50 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-200 font-bold text-xs shadow-2xs transition flex items-center gap-2"
            >
                <i class="fas fa-arrow-left text-slate-400"></i>
                <span>Back to Coupons</span>
            </a>
        </div>
    </div>

    <!-- FLASH NOTIFICATIONS -->
    @if (session('status'))
        <div class="rounded-xl border border-emerald-200 bg-emerald-50 dark:border-emerald-800/60 dark:bg-emerald-950/40 p-4 text-sm font-semibold text-emerald-800 dark:text-emerald-300 flex items-center gap-3">
            <i class="fas fa-check-circle text-emerald-500 text-base"></i>
            <span>{{ session('status') }}</span>
        </div>
    @endif

    <!-- FORM CARD -->
    <div class="rounded-2xl border border-slate-200/80 dark:border-slate-800 bg-white dark:bg-slate-900 p-6 shadow-2xs">
        <form wire:submit="save" class="space-y-6">

            <!-- CODE & STATUS SECTION -->
            <div class="space-y-4">
                <h3 class="text-xs font-black uppercase tracking-wider text-slate-400 pb-2 border-b border-slate-100 dark:border-slate-800">
                    Voucher Code &amp; Status
                </h3>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 items-start">
                    <!-- COUPON CODE -->
                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">
                            Coupon Code <span class="text-rose-500">*</span>
                        </label>
                        <div class="flex gap-2">
                            <input
                                type="text"
                                wire:model.blur="code"
                                placeholder="e.g. WELCOME10"
                                class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-xs font-mono font-black uppercase tracking-wider text-slate-900 dark:text-white focus:outline-hidden focus:ring-2 focus:ring-pp-500"
                            />
                            @if (! $isEditing)
                                <button
                                    type="button"
                                    wire:click="generateCode"
                                    class="px-3 py-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 text-xs font-bold hover:bg-slate-200 transition shrink-0"
                                    title="Generate random voucher code"
                                >
                                    <i class="fas fa-magic text-slate-400"></i>
                                    <span>Random</span>
                                </button>
                            @endif
                        </div>
                        @error('code') <p class="text-[11px] text-rose-500 font-semibold mt-1">{{ $message }}</p> @enderror
                        <p class="text-[10px] text-slate-400 mt-1">Buyers will enter this code during checkout.</p>
                    </div>

                    <!-- ACTIVE STATUS TOGGLE -->
                    <div class="sm:pt-6">
                        <label class="flex items-center gap-3 p-3 rounded-xl bg-slate-50 dark:bg-slate-800/60 border border-slate-200 dark:border-slate-700 cursor-pointer">
                            <input
                                type="checkbox"
                                wire:model="is_active"
                                class="w-4 h-4 rounded text-pp-600 focus:ring-pp-500 border-slate-300 dark:border-slate-700"
                            />
                            <div>
                                <span class="text-xs font-bold text-slate-800 dark:text-white block">Active &amp; Redeemable</span>
                                <span class="text-[10px] text-slate-400 block">Uncheck to immediately suspend redemption without deleting.</span>
                            </div>
                        </label>
                    </div>
                </div>
            </div>

            <!-- DISCOUNT CONFIGURATION -->
            <div class="space-y-4">
                <h3 class="text-xs font-black uppercase tracking-wider text-slate-400 pb-2 border-b border-slate-100 dark:border-slate-800">
                    Discount Rate &amp; Deduction Type
                </h3>

                <!-- TYPE SELECTOR -->
                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-2">
                        Deduction Type <span class="text-rose-500">*</span>
                    </label>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <label class="relative flex items-center p-3.5 rounded-xl border {{ $type === 'percentage' ? 'border-pp-500 bg-pp-50/50 dark:bg-pp-950/40 ring-1 ring-pp-500' : 'border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800' }} cursor-pointer transition">
                            <input type="radio" wire:model.live="type" value="percentage" class="sr-only" />
                            <div class="flex items-center gap-3">
                                <span class="w-8 h-8 rounded-lg {{ $type === 'percentage' ? 'bg-pp-600 text-white' : 'bg-slate-200 dark:bg-slate-700 text-slate-600 dark:text-slate-300' }} flex items-center justify-center font-bold text-xs shrink-0">
                                    <i class="fas fa-percent"></i>
                                </span>
                                <div>
                                    <div class="font-extrabold text-xs text-slate-900 dark:text-white">Percentage Discount</div>
                                    <div class="text-[10px] text-slate-400">e.g. 10% off the total eligible order value</div>
                                </div>
                            </div>
                        </label>

                        <label class="relative flex items-center p-3.5 rounded-xl border {{ $type === 'fixed' ? 'border-pp-500 bg-pp-50/50 dark:bg-pp-950/40 ring-1 ring-pp-500' : 'border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800' }} cursor-pointer transition">
                            <input type="radio" wire:model.live="type" value="fixed" class="sr-only" />
                            <div class="flex items-center gap-3">
                                <span class="w-8 h-8 rounded-lg {{ $type === 'fixed' ? 'bg-pp-600 text-white' : 'bg-slate-200 dark:bg-slate-700 text-slate-600 dark:text-slate-300' }} flex items-center justify-center font-bold text-xs shrink-0">
                                    ₦
                                </span>
                                <div>
                                    <div class="font-extrabold text-xs text-slate-900 dark:text-white">Fixed Amount Deduction</div>
                                    <div class="text-[10px] text-slate-400">e.g. Flat ₦5,000 deduction from cart total</div>
                                </div>
                            </div>
                        </label>
                    </div>
                </div>

                <!-- VALUE & MAX DISCOUNT -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <!-- VALUE -->
                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">
                            Discount Value <span class="text-rose-500">*</span>
                        </label>
                        <div class="relative">
                            <span class="absolute left-3.5 top-1/2 -translate-y-1/2 font-bold text-xs text-slate-400">
                                {{ $type === 'percentage' ? '%' : '₦' }}
                            </span>
                            <input
                                type="number"
                                step="0.01"
                                min="0.01"
                                wire:model="value"
                                placeholder="{{ $type === 'percentage' ? 'e.g. 10.00' : 'e.g. 5000.00' }}"
                                class="w-full pl-9 pr-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-xs font-extrabold text-slate-900 dark:text-white focus:outline-hidden focus:ring-2 focus:ring-pp-500"
                            />
                        </div>
                        @error('value') <p class="text-[11px] text-rose-500 font-semibold mt-1">{{ $message }}</p> @enderror
                        <p class="text-[10px] text-slate-400 mt-1">
                            {{ $type === 'percentage' ? 'Percentage to deduct from subtotal.' : 'Exact Naira amount deducted.' }}
                        </p>
                    </div>

                    <!-- MAX DISCOUNT CAP -->
                    @if ($type === 'percentage')
                        <div>
                            <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">
                                Maximum Discount Cap <span class="text-slate-400 font-normal">(Optional)</span>
                            </label>
                            <div class="relative">
                                <span class="absolute left-3.5 top-1/2 -translate-y-1/2 font-bold text-xs text-slate-400">₦</span>
                                <input
                                    type="number"
                                    step="0.01"
                                    min="0"
                                    wire:model="max_discount"
                                    placeholder="e.g. 5000.00"
                                    class="w-full pl-8 pr-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-xs font-bold text-slate-900 dark:text-white focus:outline-hidden focus:ring-2 focus:ring-pp-500"
                                />
                            </div>
                            @error('max_discount') <p class="text-[11px] text-rose-500 font-semibold mt-1">{{ $message }}</p> @enderror
                            <p class="text-[10px] text-slate-400 mt-1">Ceiling discount limit regardless of cart total size.</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- USAGE LIMITS & ELIGIBILITY -->
            <div class="space-y-4">
                <h3 class="text-xs font-black uppercase tracking-wider text-slate-400 pb-2 border-b border-slate-100 dark:border-slate-800">
                    Order Eligibility &amp; Usage Restrictions
                </h3>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <!-- MIN ORDER AMOUNT -->
                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">
                            Minimum Order Cart Subtotal
                        </label>
                        <div class="relative">
                            <span class="absolute left-3.5 top-1/2 -translate-y-1/2 font-bold text-xs text-slate-400">₦</span>
                            <input
                                type="number"
                                step="0.01"
                                min="0"
                                wire:model="min_order_amount"
                                placeholder="0.00"
                                class="w-full pl-8 pr-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-xs font-bold text-slate-900 dark:text-white focus:outline-hidden focus:ring-2 focus:ring-pp-500"
                            />
                        </div>
                        @error('min_order_amount') <p class="text-[11px] text-rose-500 font-semibold mt-1">{{ $message }}</p> @enderror
                        <p class="text-[10px] text-slate-400 mt-1">Leave 0.00 to allow on any purchase amount.</p>
                    </div>

                    <!-- TOTAL USAGE LIMIT -->
                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">
                            Total Redemption Usage Cap <span class="text-slate-400 font-normal">(Optional)</span>
                        </label>
                        <input
                            type="number"
                            min="1"
                            wire:model="usage_limit"
                            placeholder="e.g. 1000"
                            class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-xs font-bold text-slate-900 dark:text-white focus:outline-hidden focus:ring-2 focus:ring-pp-500"
                        />
                        @error('usage_limit') <p class="text-[11px] text-rose-500 font-semibold mt-1">{{ $message }}</p> @enderror
                        <p class="text-[10px] text-slate-400 mt-1">Maximum number of times this coupon can be redeemed across the platform.</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <!-- EXPIRATION DATE -->
                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">
                            Expiration Date &amp; Time <span class="text-slate-400 font-normal">(Optional)</span>
                        </label>
                        <input
                            type="datetime-local"
                            wire:model="expires_at"
                            class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-xs font-bold text-slate-900 dark:text-white focus:outline-hidden focus:ring-2 focus:ring-pp-500"
                        />
                        @error('expires_at') <p class="text-[11px] text-rose-500 font-semibold mt-1">{{ $message }}</p> @enderror
                        <p class="text-[10px] text-slate-400 mt-1">Leave blank if the voucher should never expire.</p>
                    </div>

                    <!-- USED COUNT (IF EDITING) -->
                    @if ($isEditing)
                        <div>
                            <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">
                                Times Already Redeemed
                            </label>
                            <input
                                type="number"
                                min="0"
                                wire:model="used_count"
                                class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-xs font-bold text-slate-900 dark:text-white focus:outline-hidden"
                            />
                            @error('used_count') <p class="text-[11px] text-rose-500 font-semibold mt-1">{{ $message }}</p> @enderror
                        </div>
                    @endif
                </div>
            </div>

            <!-- BUTTONS -->
            <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-100 dark:border-slate-800">
                <a
                    href="{{ route('admin.coupons') }}"
                    class="px-4 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 text-xs font-bold text-slate-600 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-800 transition"
                >
                    Cancel
                </a>

                <button
                    type="submit"
                    class="px-5 py-2.5 rounded-xl bg-pp-600 hover:bg-pp-700 text-white font-extrabold text-xs shadow-soft transition flex items-center gap-2 cursor-pointer"
                >
                    <i class="fas fa-check"></i>
                    <span wire:loading.remove wire:target="save">{{ $submitLabel }}</span>
                    <span wire:loading wire:target="save">Saving...</span>
                </button>
            </div>
        </form>
    </div>

</div>
