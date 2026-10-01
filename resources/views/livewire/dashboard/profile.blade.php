<div class="flex flex-col gap-6" wire:init="loadBanks">

  <!-- HEADER -->
  <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
    <div>
      <h1 class="text-2xl font-extrabold text-slate-950">Account &amp; Business Profile</h1>
      <p class="text-xs text-slate-500 mt-0.5">Manage your personal credentials, store branding, and direct seller payout bank account.</p>
    </div>

    <span class="px-3.5 py-1.5 rounded-full bg-emerald-100 text-emerald-800 text-xs font-extrabold flex items-center gap-1.5 self-start sm:self-auto">
      <i class="fas fa-check-circle text-emerald-600"></i> VERIFIED ACCOUNT
    </span>
  </div>

  @if (session()->has('profile_success'))
    <div class="p-4 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs font-bold flex items-center gap-2">
      <i class="fas fa-check-circle text-emerald-600 text-sm"></i>
      {{ session('profile_success') }}
    </div>
  @endif

  @if (session()->has('bank_success'))
    <div class="p-4 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs font-bold flex items-center gap-2">
      <i class="fas fa-shield-alt text-emerald-600 text-sm"></i>
      {{ session('bank_success') }}
    </div>
  @endif

  <!-- PROFILE CARDS GRID -->
  <div class="grid md:grid-cols-12 gap-6">
    
    <!-- LEFT: PERSONAL & STORE INFO -->
    <div class="md:col-span-7 lg:col-span-7 space-y-6">
      
      <div class="bg-white rounded-3xl border border-slate-200 p-6 space-y-5 shadow-soft">
        <h3 class="text-xs font-extrabold text-slate-900 uppercase tracking-wider flex items-center gap-2 border-b border-slate-100 pb-3">
          <i class="fas fa-store text-pp-600"></i> Business &amp; Personal Information
        </h3>

        <form wire:submit.prevent="saveProfile" class="space-y-4 text-xs">
          <div class="grid sm:grid-cols-2 gap-4">
            <div class="space-y-1">
              <label class="font-bold text-slate-700 block">Full Name</label>
              <input type="text" wire:model="name" class="w-full p-2.5 rounded-xl border border-slate-200 font-bold text-slate-900 outline-none focus:border-pp-500 transition" />
              @error('name') <span class="text-red-600 text-[11px] font-semibold">{{ $message }}</span> @enderror
            </div>

            <div class="space-y-1">
              <label class="font-bold text-slate-700 block">Store / Business Name</label>
              <input type="text" wire:model="business_name" placeholder="e.g. Acme Tech Solutions" class="w-full p-2.5 rounded-xl border border-slate-200 font-bold text-slate-900 outline-none focus:border-pp-500 transition" />
              @error('business_name') <span class="text-red-600 text-[11px] font-semibold">{{ $message }}</span> @enderror
            </div>

            <div class="space-y-1">
              <label class="font-bold text-slate-700 block">Email Address (Read-only)</label>
              <input type="email" value="{{ $email }}" disabled class="w-full p-2.5 rounded-xl border border-slate-200 bg-slate-50 font-bold text-slate-500 cursor-not-allowed outline-none" />
            </div>

            <div class="space-y-1">
              <label class="font-bold text-slate-700 block">Phone Number</label>
              <input type="text" wire:model="phone" placeholder="e.g. +234 803 123 4567" class="w-full p-2.5 rounded-xl border border-slate-200 font-bold text-slate-900 outline-none focus:border-pp-500 transition" />
              @error('phone') <span class="text-red-600 text-[11px] font-semibold">{{ $message }}</span> @enderror
            </div>
          </div>

          <div class="pt-3 border-t border-slate-100 flex justify-end">
            <button type="submit" class="px-5 py-2.5 rounded-xl bg-pp-600 hover:bg-pp-700 text-white font-extrabold text-xs shadow-xs transition cursor-pointer flex items-center gap-1.5">
              <i class="fas fa-save"></i> Save Profile Details
            </button>
          </div>
        </form>
      </div>

      <!-- INSTRUCTIONS ON PAYMENTS -->
      <div class="p-5 rounded-3xl bg-slate-50 border border-slate-200 text-xs text-slate-600 space-y-2">
        <h4 class="font-extrabold text-slate-900 flex items-center gap-1.5">
          <i class="fas fa-info-circle text-pp-600"></i> How Bank Accounts Work on Parts &amp; Parcel
        </h4>
        <p class="leading-relaxed">
          When buyers choose <strong>Direct Transfer</strong> at checkout, they pay directly into your saved bank account. For orders placed via <strong>Parts &amp; Parcel Escrow</strong>, payouts are disbursed directly to this account upon warranty satisfaction or customer delivery confirmation.
        </p>
      </div>

    </div>

    <!-- RIGHT: DIRECT SELLER PAYOUT BANK DETAILS -->
    <div class="md:col-span-5 lg:col-span-5 space-y-6">
      
      <div class="bg-white rounded-3xl border border-slate-200 p-6 space-y-4 shadow-soft">
        <div class="flex items-center justify-between border-b border-slate-100 pb-3">
          <h3 class="text-xs font-extrabold text-slate-900 uppercase tracking-wider flex items-center gap-2">
            <i class="fas fa-university text-pp-600"></i> Direct Seller Payout Bank
          </h3>
          @if ($hasSavedBank && ! $showEditBankForm)
            <span class="px-2 py-0.5 rounded-full bg-emerald-100 text-emerald-800 text-[10px] font-extrabold">
              ACTIVE
            </span>
          @endif
        </div>

        @if ($hasSavedBank && ! $showEditBankForm)
          <!-- VIEW CURRENT SAVED BANK -->
          <div class="p-4 rounded-2xl bg-gradient-to-br from-pp-50 to-slate-50 border border-pp-200 space-y-3 text-xs shadow-2xs">
            <div class="flex items-center justify-between">
              <span class="text-slate-500 font-bold text-[10px] uppercase tracking-wider">Connected Bank Account</span>
              <span class="text-emerald-700 font-black text-[11px] flex items-center gap-1">
                <i class="fas fa-check-circle"></i> Ready to Receive Payments
              </span>
            </div>

            <div>
              <span class="text-[11px] text-slate-400 font-bold block">BANK</span>
              <h4 class="text-base font-black text-slate-900">{{ $savedBank->bank_name }}</h4>
            </div>

            <div class="grid grid-cols-2 gap-3 pt-1 border-t border-pp-100">
              <div>
                <span class="text-[10px] text-slate-400 font-bold block">ACCOUNT NUMBER</span>
                <p class="font-mono font-black text-sm text-pp-800 tracking-wider">{{ $savedBank->account_number }}</p>
              </div>
              <div>
                <span class="text-[10px] text-slate-400 font-bold block">ACCOUNT NAME</span>
                <p class="font-bold text-xs text-slate-800 truncate" title="{{ $savedBank->account_name }}">{{ $savedBank->account_name }}</p>
              </div>
            </div>

            @if ($savedBank->verified_at)
              <div class="pt-2 text-[10px] text-slate-500 flex items-center gap-1">
                <i class="fas fa-shield-check text-emerald-600"></i> Verified on {{ $savedBank->verified_at->format('M d, Y') }}
              </div>
            @endif
          </div>

          <button type="button" wire:click="editBank" class="w-full py-2.5 rounded-xl border border-slate-200 text-slate-800 font-bold text-xs hover:bg-slate-50 transition cursor-pointer flex items-center justify-center gap-2">
            <i class="fas fa-pencil-alt text-slate-500"></i> Update Bank Details
          </button>

        @else
          <!-- EDIT / ADD BANK ACCOUNT FORM -->
          <div class="space-y-4 text-xs">
            @if (! $hasSavedBank)
              <div class="p-3 rounded-xl bg-amber-50 border border-amber-200 text-amber-900 text-[11px] leading-relaxed">
                <strong class="block font-extrabold flex items-center gap-1 mb-0.5">
                  <i class="fas fa-exclamation-triangle text-amber-600"></i> Action Needed:
                </strong>
                Add your bank account below so buyers can make direct payments to you, and your funds can be deposited.
              </div>
            @endif

            <!-- 1. BANK NAME (Input text first, transforms into gateway dropdown once loaded) -->
            <div class="space-y-1">
              <label class="font-bold text-slate-700 flex items-center justify-between">
                <span>Select or Enter Bank</span>
                @if ($isLoadingBanks)
                  <span class="text-[10px] text-pp-600 font-bold flex items-center gap-1">
                    <i class="fas fa-circle-notch fa-spin"></i> Loading bank list...
                  </span>
                @endif
              </label>

              @if (! empty($banks))
                <!-- Gateway bank dropdown when loaded -->
                <select wire:model.live="bank_code" class="w-full p-2.5 rounded-xl border border-slate-200 font-bold text-slate-900 outline-none focus:border-pp-500 transition bg-white cursor-pointer">
                  <option value="">-- Choose your bank --</option>
                  @foreach ($banks as $b)
                    <option value="{{ $b['code'] }}">{{ $b['name'] }}</option>
                  @endforeach
                </select>
                <div class="flex items-center justify-between text-[10px] text-slate-400 mt-1">
                  <span>{{ count($banks) }} banks available via payment gateway</span>
                  @if (! empty($bank_name))
                    <span class="text-pp-700 font-bold">{{ $bank_name }}</span>
                  @endif
                </div>
              @else
                <!-- Initial text input before gateway banks load -->
                <div class="relative">
                  <input type="text" wire:model="bank_name" placeholder="Type bank name (e.g. GTBank, Zenith, Access)" class="w-full p-2.5 rounded-xl border border-slate-200 font-bold text-slate-900 outline-none focus:border-pp-500 transition" />
                  <span class="absolute right-3 top-2.5 text-slate-400 text-xs">
                    <i class="fas fa-university"></i>
                  </span>
                </div>
                <p class="text-[10px] text-slate-400">Loading instant gateway bank picker...</p>
              @endif
              @error('bank_name') <span class="text-red-600 text-[11px] font-semibold">{{ $message }}</span> @enderror
            </div>

            <!-- 2. ACCOUNT NUMBER & LIVE RESOLUTION -->
            <div class="space-y-1">
              <label class="font-bold text-slate-700 flex items-center justify-between">
                <span>10-Digit Account Number</span>
                @if ($isResolving)
                  <span class="text-[10px] text-pp-600 font-bold flex items-center gap-1">
                    <i class="fas fa-spinner fa-spin"></i> Resolving account name...
                  </span>
                @endif
              </label>
              <div class="relative">
                <input type="text" wire:model.live.debounce.400ms="account_number" maxlength="10" placeholder="0123456789" class="w-full p-2.5 rounded-xl border border-slate-200 font-mono font-bold text-slate-900 tracking-wider outline-none focus:border-pp-500 transition" />
                @if ($resolveSuccess)
                  <span class="absolute right-3 top-2.5 text-emerald-600 text-xs">
                    <i class="fas fa-check-circle"></i>
                  </span>
                @endif
              </div>
              @error('account_number') <span class="text-red-600 text-[11px] font-semibold">{{ $message }}</span> @enderror
            </div>

            <!-- 3. ACCOUNT NAME (Auto-resolved from gateway or manual fallback) -->
            <div class="space-y-1">
              <label class="font-bold text-slate-700 flex items-center justify-between">
                <span>Account Name</span>
                @if ($resolveSuccess)
                  <span class="text-[10px] px-2 py-0.5 rounded bg-emerald-100 text-emerald-800 font-extrabold flex items-center gap-1">
                    <i class="fas fa-shield-alt"></i> GATEWAY VERIFIED
                  </span>
                @endif
              </label>
              <input type="text" wire:model="account_name" placeholder="Full name registered on bank account" class="w-full p-2.5 rounded-xl border border-slate-200 font-bold text-slate-900 outline-none focus:border-pp-500 transition {{ $resolveSuccess ? 'bg-emerald-50/50 border-emerald-300' : '' }}" />
              @if ($resolveError)
                <p class="text-[10px] text-amber-700 font-semibold">{{ $resolveError }}</p>
              @endif
              @error('account_name') <span class="text-red-600 text-[11px] font-semibold">{{ $message }}</span> @enderror
            </div>

            <!-- 4. SECURITY CHECK: ACCOUNT PASSWORD REQUIREMENT -->
            <div class="space-y-1 pt-2 border-t border-slate-100">
              <label class="font-bold text-slate-800 flex items-center gap-1">
                <i class="fas fa-lock text-slate-500"></i> Account Password Verification
              </label>
              <input type="password" wire:model="password" placeholder="Enter your login password to confirm" class="w-full p-2.5 rounded-xl border border-slate-200 font-bold text-slate-900 outline-none focus:border-pp-500 transition" />
              <p class="text-[10px] text-slate-500">Enter your password to authorize this bank account change.</p>
              @error('password') <span class="text-red-600 text-[11px] font-semibold block">{{ $message }}</span> @enderror
            </div>

            <!-- ACTIONS -->
            <div class="pt-3 border-t border-slate-100 flex items-center gap-2">
              <button type="button" wire:click="saveBankAccount" class="flex-1 py-2.5 rounded-xl bg-pp-600 hover:bg-pp-700 text-white font-extrabold text-xs shadow-xs transition cursor-pointer flex items-center justify-center gap-1.5">
                <i class="fas fa-lock"></i> Save Bank Account
              </button>

              @if ($hasSavedBank)
                <button type="button" wire:click="cancelEditBank" class="py-2.5 px-4 rounded-xl border border-slate-200 text-slate-600 font-bold text-xs hover:bg-slate-50 transition cursor-pointer">
                  Cancel
                </button>
              @endif
            </div>

          </div>
        @endif

      </div>

    </div>

  </div>

</div>