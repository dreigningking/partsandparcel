<div class="min-h-[80vh] flex flex-col justify-center py-12 sm:px-6 lg:px-8">
    <div class="sm:mx-auto sm:w-full sm:max-w-md text-center">
        <a href="{{ route('welcome') }}" class="inline-flex items-center gap-2">
            <div class="w-10 h-10 rounded-2xl bg-pp-600 text-white font-black text-xl grid place-items-center shadow-lg shadow-pp-500/30">P</div>
            <span class="font-black text-2xl tracking-tight text-slate-900">Parts &amp; Parcel</span>
        </a>
        <h2 class="mt-4 text-2xl font-extrabold text-slate-900 tracking-tight">Reset your password</h2>
        <p class="mt-1.5 text-xs text-slate-500">Enter your email and we will send you a password reset link.</p>
    </div>

    <div class="mt-8 sm:mx-auto sm:w-full sm:max-w-md px-4 sm:px-0">
        <div class="bg-white py-8 px-6 shadow-xl shadow-slate-200/50 sm:rounded-3xl border border-slate-100 sm:px-10">
            @if ($statusMessage)
                <div class="mb-5 p-3.5 rounded-xl bg-emerald-50 border border-emerald-200 text-xs font-semibold text-emerald-700 flex items-center gap-2">
                    <svg class="w-4 h-4 text-emerald-500 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    <span>{{ $statusMessage }}</span>
                </div>
            @endif

            @if ($errorMessage)
                <div class="mb-5 p-3.5 rounded-xl bg-rose-50 border border-rose-200 text-xs font-semibold text-rose-700 flex items-center gap-2">
                    <svg class="w-4 h-4 text-rose-500 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <span>{{ $errorMessage }}</span>
                </div>
            @endif

            <form wire:submit.prevent="submit" class="space-y-5">
                <div>
                    <label for="email" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Email Address</label>
                    <input type="email" id="email" wire:model.defer="email" required placeholder="you@example.com"
                           class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:outline-hidden focus:border-pp-500 focus:ring-2 focus:ring-pp-100 text-sm transition font-medium text-slate-800 placeholder-slate-400">
                    @error('email') <span class="text-[11px] font-bold text-rose-500 mt-1 block">{{ $message }}</span> @enderror
                </div>

                <div>
                    <button type="submit" wire:loading.attr="disabled"
                            class="w-full py-3.5 px-4 rounded-xl bg-pp-600 hover:bg-pp-700 text-white font-extrabold text-sm transition shadow-lg shadow-pp-600/25 flex items-center justify-center gap-2 cursor-pointer">
                        <span wire:loading.remove>Send Reset Link</span>
                        <span wire:loading class="inline-flex items-center gap-2">
                            <svg class="animate-spin h-4 w-4 text-white" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                            Sending...
                        </span>
                    </button>
                </div>
            </form>

            <div class="mt-6 pt-6 border-t border-slate-100 text-center">
                <a href="{{ route('login') }}" class="text-xs font-extrabold text-pp-600 hover:underline">← Back to sign in</a>
            </div>
        </div>
    </div>
</div>
