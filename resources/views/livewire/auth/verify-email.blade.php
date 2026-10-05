<div class="min-h-[80vh] flex flex-col justify-center py-12 sm:px-6 lg:px-8">
    <div class="sm:mx-auto sm:w-full sm:max-w-md text-center">
        <a href="{{ route('welcome') }}" class="inline-flex items-center gap-2">
            <div class="w-10 h-10 rounded-2xl bg-pp-600 text-white font-black text-xl grid place-items-center shadow-lg shadow-pp-500/30">P</div>
            <span class="font-black text-2xl tracking-tight text-slate-900">Parts &amp; Parcel</span>
        </a>
        <div class="mt-4 inline-flex items-center justify-center w-12 h-12 rounded-2xl bg-pp-50 text-pp-600 border border-pp-200">
            <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
            </svg>
        </div>
        <h2 class="mt-3 text-2xl font-extrabold text-slate-900 tracking-tight">Verify Your Email Address</h2>
        <p class="mt-1.5 text-xs text-slate-500">We require verified email addresses to keep the marketplace safe and protect your account.</p>
    </div>

    <div class="mt-8 sm:mx-auto sm:w-full sm:max-w-md px-4 sm:px-0">
        <div class="bg-white py-8 px-6 shadow-xl shadow-slate-200/50 sm:rounded-3xl border border-slate-100 sm:px-10">
            
            {{-- STATUS NOTIFICATION --}}
            @if ($statusMessage)
                <div class="mb-5 p-3.5 rounded-2xl bg-emerald-50 border border-emerald-200 text-xs font-semibold text-emerald-800 flex items-start gap-2.5">
                    <svg class="w-4 h-4 text-emerald-600 shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <span>{{ $statusMessage }}</span>
                </div>
            @endif

            {{-- ERROR NOTIFICATION --}}
            @if ($errorMessage)
                <div class="mb-5 p-3.5 rounded-2xl bg-rose-50 border border-rose-200 text-xs font-semibold text-rose-700 flex items-start gap-2.5">
                    <svg class="w-4 h-4 text-rose-500 shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <span>{{ $errorMessage }}</span>
                </div>
            @endif

            {{-- EMAIL DISPLAY / EDIT BOX --}}
            <div class="mb-6 p-4 rounded-2xl bg-slate-50 border border-slate-200/80">
                <div class="flex items-center justify-between text-xs mb-1">
                    <span class="font-bold text-slate-500 uppercase tracking-wider text-[10px]">Verification Target</span>
                    <button type="button" wire:click="toggleEditEmail" class="text-[11px] font-bold text-pp-600 hover:text-pp-700 hover:underline">
                        {{ $isEditingEmail ? 'Cancel' : 'Change email' }}
                    </button>
                </div>

                @if ($isEditingEmail)
                    <form wire:submit.prevent="updateEmail" class="mt-2 space-y-2">
                        <input type="email" wire:model="newEmail" required placeholder="name@example.com"
                               class="w-full px-3.5 py-2.5 text-xs font-semibold rounded-xl border border-slate-300 focus:outline-hidden focus:border-pp-500 focus:ring-2 focus:ring-pp-100 bg-white text-slate-800">
                        @error('newEmail') <span class="text-[11px] font-bold text-rose-500 block">{{ $message }}</span> @enderror
                        <div class="flex items-center gap-2">
                            <button type="submit" class="px-3 py-1.5 bg-pp-600 hover:bg-pp-700 text-white rounded-lg text-xs font-bold transition shadow-xs">
                                Save &amp; Send OTP
                            </button>
                            <button type="button" wire:click="toggleEditEmail" class="px-3 py-1.5 bg-slate-200 hover:bg-slate-300 text-slate-700 rounded-lg text-xs font-semibold transition">
                                Cancel
                            </button>
                        </div>
                    </form>
                @else
                    <div class="flex items-center justify-between">
                        <span class="text-sm font-extrabold text-slate-900 tracking-tight">{{ $email }}</span>
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-amber-100 text-amber-800 border border-amber-200">
                            Unverified
                        </span>
                    </div>
                @endif
            </div>

            {{-- OTP VERIFICATION FORM --}}
            <form wire:submit.prevent="verify" class="space-y-5">
                <div>
                    <label for="otp" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2 text-center">
                        Enter 6-Digit OTP Code
                    </label>
                    <div class="relative">
                        <input type="text" id="otp" wire:model.defer="otp" maxlength="6" autofocus placeholder="123456" inputmode="numeric" pattern="[0-9]*" autocomplete="one-time-code"
                               class="w-full text-center tracking-[0.5em] text-2xl font-black font-mono py-3.5 px-4 rounded-2xl border border-slate-300 focus:outline-hidden focus:border-pp-500 focus:ring-4 focus:ring-pp-100 text-slate-900 placeholder-slate-300 transition">
                    </div>
                    @error('otp') <span class="text-[11px] font-bold text-rose-500 mt-2 text-center block">{{ $message }}</span> @enderror
                    <p class="text-[11px] text-slate-400 text-center mt-2">Check your inbox and spam folder. Code expires in 10 minutes.</p>
                </div>

                <div>
                    <button type="submit" wire:loading.attr="disabled"
                            class="w-full py-3.5 px-4 rounded-xl bg-pp-600 hover:bg-pp-700 text-white font-extrabold text-sm transition shadow-lg shadow-pp-600/25 flex items-center justify-center gap-2 cursor-pointer">
                        <span wire:loading.remove>Verify Email</span>
                        <span wire:loading class="flex items-center gap-2">
                            <svg class="animate-spin h-4 w-4 text-white" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                            Verifying Code...
                        </span>
                    </button>
                </div>
            </form>

            {{-- RESEND CODE & ACTIONS --}}
            <div class="mt-6 pt-5 border-t border-slate-100 flex flex-col items-center gap-3 text-center"
                 x-data="{
                     timer: {{ $resendCooldown }},
                     init() {
                         if (this.timer > 0) {
                             let interval = setInterval(() => {
                                 if (this.timer > 0) {
                                     this.timer--;
                                 } else {
                                     clearInterval(interval);
                                 }
                             }, 1000);
                         }
                     }
                 }">
                <div class="text-xs text-slate-500 font-medium">
                    <span>Didn't receive the email code?</span>
                    <template x-if="timer > 0">
                        <span class="font-bold text-slate-400 ml-1">Resend in <span x-text="timer"></span>s</span>
                    </template>
                    <template x-if="timer <= 0">
                        <button type="button" wire:click="resendOtp" wire:loading.attr="disabled"
                                class="font-extrabold text-pp-600 hover:text-pp-700 hover:underline ml-1 cursor-pointer">
                            Resend OTP Code
                        </button>
                    </template>
                </div>

                <div class="pt-2">
                    <button type="button" wire:click="logout" class="text-xs font-semibold text-slate-400 hover:text-slate-600 flex items-center gap-1.5 transition">
                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                        </svg>
                        <span>Sign out of this account</span>
                    </button>
                </div>
            </div>

        </div>
    </div>
</div>
