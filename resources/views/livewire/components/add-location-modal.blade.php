<div>
    @if($isOpen)
        <!-- BACKDROP -->
        <div wire:click="closeModal" class="fixed inset-0 z-[170] bg-slate-950/50 backdrop-blur-xs transition"></div>

        <!-- MODAL DIALOG -->
        <div class="fixed inset-0 z-[175] flex items-center justify-center p-4 pointer-events-none">
            <div class="bg-white rounded-3xl border border-slate-200 shadow-2xl max-w-lg w-full p-6 space-y-5 pointer-events-auto relative">
                
                <!-- HEADER -->
                <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                    <div class="flex items-center gap-2.5">
                        <div class="w-9 h-9 rounded-xl bg-pp-50 text-pp-600 grid place-items-center text-sm font-black border border-pp-100">
                            <i class="fas fa-map-marker-alt"></i>
                        </div>
                        <div>
                            <h3 class="text-sm font-extrabold text-slate-950">Add Referenced Location</h3>
                            <p class="text-[11px] text-slate-500">Save your shop, warehouse, or pickup location</p>
                        </div>
                    </div>
                    <button type="button" wire:click="closeModal" class="w-8 h-8 rounded-full bg-slate-100 hover:bg-slate-200 text-slate-500 grid place-items-center transition cursor-pointer">
                        <i class="fas fa-times text-xs"></i>
                    </button>
                </div>

                <!-- FORM BODY -->
                <form wire:submit.prevent="createLocation" class="space-y-3.5 text-xs">
                    <div>
                        <label class="block font-bold text-slate-700 mb-1">Location Label <span class="text-rose-500">*</span></label>
                        <input type="text" wire:model.defer="label" placeholder="e.g. Main Workshop, Ikeja Store, Office" class="w-full px-3 py-2.5 rounded-xl border border-slate-200 text-xs font-bold text-slate-900 outline-none focus:border-pp-500 transition" />
                        @error('label') <span class="text-[10px] text-rose-600 font-bold block mt-1">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block font-bold text-slate-700 mb-1">Street Address <span class="text-rose-500">*</span></label>
                        <input type="text" wire:model.defer="address_line_1" placeholder="e.g. Shop B12, Computer Village, Pepple Street" class="w-full px-3 py-2.5 rounded-xl border border-slate-200 text-xs text-slate-900 outline-none focus:border-pp-500 transition" />
                        @error('address_line_1') <span class="text-[10px] text-rose-600 font-bold block mt-1">{{ $message }}</span> @enderror
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <div>
                            <label class="block font-bold text-slate-700 mb-1">City <span class="text-rose-500">*</span></label>
                            <input type="text" wire:model.defer="city" placeholder="e.g. Ikeja" class="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs font-semibold text-slate-900 outline-none focus:border-pp-500 transition" />
                            @error('city') <span class="text-[10px] text-rose-600 font-bold block mt-1">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="block font-bold text-slate-700 mb-1">State <span class="text-rose-500">*</span></label>
                            <select wire:model.defer="state_id" class="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs font-semibold text-slate-900 outline-none focus:border-pp-500 transition bg-white">
                                <option value="">Select State</option>
                                @foreach($states as $st)
                                    <option value="{{ $st->id }}">{{ $st->name }}</option>
                                @endforeach
                            </select>
                            @error('state_id') <span class="text-[10px] text-rose-600 font-bold block mt-1">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <div>
                            <label class="block font-bold text-slate-700 mb-1">Contact Name</label>
                            <input type="text" wire:model.defer="contact_name" placeholder="Contact person" class="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs text-slate-900 outline-none focus:border-pp-500 transition" />
                        </div>
                        <div>
                            <label class="block font-bold text-slate-700 mb-1">Contact Phone</label>
                            <input type="text" wire:model.defer="phone" placeholder="+234 800 000 0000" class="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs text-slate-900 outline-none focus:border-pp-500 transition" />
                        </div>
                    </div>

                    <!-- FOOTER BUTTONS -->
                    <div class="border-t border-slate-100 pt-4 flex items-center justify-end gap-2.5">
                        <button type="button" wire:click="closeModal" class="px-4 py-2 rounded-xl border border-slate-300 hover:bg-slate-50 text-xs font-bold text-slate-700 transition cursor-pointer">
                            Cancel
                        </button>
                        <button type="submit" class="px-5 py-2 rounded-xl bg-pp-600 hover:bg-pp-700 text-white text-xs font-extrabold shadow-xs transition cursor-pointer flex items-center gap-1.5">
                            <i class="fas fa-check text-[10px]"></i> Save Location
                        </button>
                    </div>
                </form>

            </div>
        </div>
    @endif
</div>
