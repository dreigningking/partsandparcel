<div class="min-h-[85vh] flex flex-col justify-center py-12 sm:px-6 lg:px-8">
    <div class="sm:mx-auto sm:w-full sm:max-w-md text-center">
        <a href="{{ route('welcome') }}" class="inline-flex items-center gap-2">
            <div class="w-10 h-10 rounded-2xl bg-pp-600 text-white font-black text-xl grid place-items-center shadow-lg shadow-pp-500/30">P</div>
            <span class="font-black text-2xl tracking-tight text-slate-900">Parts &amp; Parcel</span>
        </a>
        <h2 class="mt-4 text-2xl font-extrabold text-slate-900 tracking-tight">Create your account</h2>
        <p class="mt-1.5 text-xs text-slate-500">One account to buy, sell, request parts, and offer repair services.</p>
    </div>

    <div class="mt-8 sm:mx-auto sm:w-full sm:max-w-md px-4 sm:px-0">
        <div class="bg-white py-8 px-6 shadow-xl shadow-slate-200/50 sm:rounded-3xl border border-slate-100 sm:px-10">
            <form wire:submit.prevent="submit" class="space-y-4">
                <div>
                    <label for="name" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Full Name</label>
                    <input type="text" id="name" wire:model.defer="name" required placeholder="John Doe"
                           class="w-full px-4 py-2.5 rounded-xl border border-slate-200 focus:outline-hidden focus:border-pp-500 focus:ring-2 focus:ring-pp-100 text-sm transition font-medium text-slate-800 placeholder-slate-400">
                    @error('name') <span class="text-[11px] font-bold text-rose-500 mt-1 block">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label for="email" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Email Address</label>
                    <input type="email" id="email" wire:model.defer="email" required placeholder="you@example.com"
                           class="w-full px-4 py-2.5 rounded-xl border border-slate-200 focus:outline-hidden focus:border-pp-500 focus:ring-2 focus:ring-pp-100 text-sm transition font-medium text-slate-800 placeholder-slate-400">
                    @error('email') <span class="text-[11px] font-bold text-rose-500 mt-1 block">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label for="phone" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Phone Number <span class="text-[10px] text-slate-400 font-normal lowercase">(optional)</span></label>
                    <input type="text" id="phone" wire:model.defer="phone" placeholder="+234 800 000 0000"
                           class="w-full px-4 py-2.5 rounded-xl border border-slate-200 focus:outline-hidden focus:border-pp-500 focus:ring-2 focus:ring-pp-100 text-sm transition font-medium text-slate-800 placeholder-slate-400">
                    @error('phone') <span class="text-[11px] font-bold text-rose-500 mt-1 block">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label for="business_name" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Business / Workshop Name <span class="text-[10px] text-slate-400 font-normal lowercase">(optional)</span></label>
                    <input type="text" id="business_name" wire:model.defer="business_name" placeholder="Apex Salvage / FixLogic"
                           class="w-full px-4 py-2.5 rounded-xl border border-slate-200 focus:outline-hidden focus:border-pp-500 focus:ring-2 focus:ring-pp-100 text-sm transition font-medium text-slate-800 placeholder-slate-400">
                    @error('business_name') <span class="text-[11px] font-bold text-rose-500 mt-1 block">{{ $message }}</span> @enderror
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <label for="password" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Password</label>
                        <input type="password" id="password" wire:model.defer="password" required placeholder="••••••••"
                               class="w-full px-4 py-2.5 rounded-xl border border-slate-200 focus:outline-hidden focus:border-pp-500 focus:ring-2 focus:ring-pp-100 text-sm transition font-medium text-slate-800 placeholder-slate-400">
                        @error('password') <span class="text-[11px] font-bold text-rose-500 mt-1 block">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label for="password_confirmation" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Confirm</label>
                        <input type="password" id="password_confirmation" wire:model.defer="password_confirmation" required placeholder="••••••••"
                               class="w-full px-4 py-2.5 rounded-xl border border-slate-200 focus:outline-hidden focus:border-pp-500 focus:ring-2 focus:ring-pp-100 text-sm transition font-medium text-slate-800 placeholder-slate-400">
                    </div>
                </div>

                <div class="pt-2">
                    <label class="flex items-start gap-2.5 cursor-pointer">
                        <input type="checkbox" wire:model.defer="terms" class="w-4 h-4 mt-0.5 rounded text-pp-600 focus:ring-pp-500 border-slate-300">
                        <span class="text-xs text-slate-600 leading-snug">
                            I agree to the <a href="#" class="font-bold text-pp-600 hover:underline">Terms of Service</a>, <a href="#" class="font-bold text-pp-600 hover:underline">Escrow Policy</a>, and <a href="#" class="font-bold text-pp-600 hover:underline">Privacy Policy</a>.
                        </span>
                    </label>
                    @error('terms') <span class="text-[11px] font-bold text-rose-500 mt-1 block">{{ $message }}</span> @enderror
                </div>

                <div class="pt-2">
                    <button type="submit" wire:loading.attr="disabled"
                            class="w-full py-3.5 px-4 rounded-xl bg-pp-600 hover:bg-pp-700 text-white font-extrabold text-sm transition shadow-lg shadow-pp-600/25 flex items-center justify-center gap-2 cursor-pointer">
                        <span wire:loading.remove>Create Account</span>
                        <span wire:loading class="inline-flex items-center gap-2">
                            <svg class="animate-spin h-4 w-4 text-white" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                            Creating account...
                        </span>
                    </button>
                </div>
            </form>

            <div class="mt-6 pt-6 border-t border-slate-100 text-center">
                <p class="text-xs text-slate-500 font-medium">
                    Already have an account?
                    <a href="{{ route('login') }}" class="font-extrabold text-pp-600 hover:underline">Sign in</a>
                </p>
            </div>
        </div>
    </div>
</div>
