<div>
    @if ($isOpen)
        <!-- BACKDROP -->
        <div wire:click="closeModal" class="fixed inset-0 z-[120] bg-slate-950/60 backdrop-blur-xs transition"></div>

        <!-- REPORT MODAL DIALOG -->
        <div class="fixed inset-0 z-[130] flex items-center justify-center p-4 overflow-y-auto">
            <div class="bg-white rounded-3xl max-w-lg w-full p-6 sm:p-7 shadow-2xl relative border border-slate-200 my-8 space-y-5 animate-in fade-in zoom-in-95 duration-150">
                
                <!-- CLOSE BUTTON -->
                <button wire:click="closeModal" class="absolute top-5 right-5 w-8 h-8 rounded-full bg-slate-100 hover:bg-slate-200 text-slate-500 hover:text-slate-800 grid place-items-center text-sm font-bold transition cursor-pointer" aria-label="Close modal">
                    <i class="fas fa-times"></i>
                </button>

                <!-- HEADER -->
                <div class="flex items-center gap-3 border-b border-slate-100 pb-4">
                    <div class="w-10 h-10 rounded-2xl {{ $hasAlreadyReported ? 'bg-amber-50 text-amber-600 border-amber-200' : 'bg-rose-50 text-rose-600 border-rose-200' }} border grid place-items-center text-base shrink-0 shadow-2xs">
                        <i class="fas {{ $hasAlreadyReported ? 'fa-flag-checkered' : 'fa-flag' }}"></i>
                    </div>
                    <div>
                        <h2 class="text-base sm:text-lg font-extrabold text-slate-950 leading-tight">
                            {{ $hasAlreadyReported ? 'Report Details & Status' : 'Report ' . $targetTypeLabel }}
                        </h2>
                        <p class="text-xs text-slate-500">
                            {{ $hasAlreadyReported ? 'You have previously reported this item.' : 'Help us keep the Parts & Parcel marketplace safe and honest.' }}
                        </p>
                    </div>
                </div>

                <!-- PREVIEW OF REPORTED ITEM -->
                <div class="p-3.5 rounded-2xl bg-slate-50 border border-slate-200/80 space-y-2 text-xs">
                    <div class="flex items-start gap-3">
                        @if($targetImageUrl)
                            <img src="{{ $targetImageUrl }}" alt="{{ $targetTitle }}" class="w-12 h-12 rounded-xl object-cover border border-slate-200 shrink-0" />
                        @else
                            <div class="w-12 h-12 rounded-xl bg-pp-50 text-pp-600 border border-pp-100 grid place-items-center font-extrabold text-xs shrink-0">
                                {{ strtoupper(substr($targetAuthor ?: 'P&P', 0, 2)) }}
                            </div>
                        @endif

                        <div class="flex-1 min-w-0">
                            <span class="px-2 py-0.5 rounded-md bg-white border border-slate-200 text-[10px] font-extrabold uppercase text-slate-600 mb-1 inline-block">
                                {{ $targetTypeLabel }}
                            </span>
                            <h4 class="font-extrabold text-slate-900 truncate text-sm leading-snug">{{ $targetTitle }}</h4>
                            <div class="flex items-center gap-2 text-[11px] text-slate-400 mt-0.5 flex-wrap">
                                <span>By: <strong class="text-slate-700">{{ $targetAuthor }}</strong></span>
                                @if($targetSubtitle)
                                    <span>·</span>
                                    <span class="font-bold text-pp-700">{{ $targetSubtitle }}</span>
                                @endif
                            </div>
                        </div>
                    </div>

                    @if($targetExcerpt)
                        <div class="text-[11px] text-slate-600 line-clamp-2 italic pt-1 border-t border-slate-100">
                            "{{ $targetExcerpt }}"
                        </div>
                    @endif
                </div>

                <!-- STATE A: ALREADY REPORTED (VIEW DETAILS & ADMIN RESOLUTION) -->
                @if ($hasAlreadyReported && $existingReport)
                    <div class="space-y-4 pt-1 text-xs">
                        
                        <!-- REPORT STATUS BANNER -->
                        <div class="p-4 rounded-2xl border {{ $existingReport['status'] === 'resolved' ? 'bg-emerald-50 border-emerald-200 text-emerald-900' : ($existingReport['status'] === 'dismissed' ? 'bg-slate-100 border-slate-200 text-slate-800' : 'bg-amber-50 border-amber-200 text-amber-900') }} space-y-1.5 shadow-2xs">
                            <div class="flex items-center justify-between">
                                <span class="font-extrabold text-xs uppercase tracking-wider flex items-center gap-1.5">
                                    @if ($existingReport['status'] === 'resolved')
                                        <i class="fas fa-check-circle text-emerald-600"></i> Report Status: Resolved
                                    @elseif ($existingReport['status'] === 'dismissed')
                                        <i class="fas fa-times-circle text-slate-500"></i> Report Status: Dismissed
                                    @else
                                        <i class="fas fa-hourglass-half text-amber-600"></i> Report Status: Pending Admin Review
                                    @endif
                                </span>
                                <span class="text-[10px] font-semibold opacity-75">{{ $existingReport['created_at'] }}</span>
                            </div>
                            <p class="text-[11px] leading-relaxed">
                                @if ($existingReport['status'] === 'resolved')
                                    Our moderation team has investigated and taken action on this report.
                                @elseif ($existingReport['status'] === 'dismissed')
                                    Our moderation team reviewed this report and determined it does not violate community guidelines.
                                @else
                                    Our safety and moderation team is actively reviewing this content. You will receive an email once resolved.
                                @endif
                            </p>
                        </div>

                        <!-- YOUR SUBMITTED DETAILS -->
                        <div class="p-4 rounded-2xl bg-white border border-slate-200 space-y-2 shadow-2xs">
                            <div class="flex justify-between items-center border-b border-slate-100 pb-2">
                                <span class="text-slate-400 font-bold uppercase text-[10px]">Reason Selected:</span>
                                <span class="font-extrabold text-slate-900">{{ $existingReport['title'] }}</span>
                            </div>

                            @if (!empty($existingReport['description']))
                                <div class="space-y-1 pt-1">
                                    <span class="text-slate-400 font-bold uppercase text-[10px] block">Your Comments:</span>
                                    <p class="text-slate-700 bg-slate-50 p-2.5 rounded-xl border border-slate-100 text-xs italic">
                                        "{{ $existingReport['description'] }}"
                                    </p>
                                </div>
                            @endif
                        </div>

                        <!-- ADMIN RESOLUTION NOTES (IF RESPONDED) -->
                        @if (!empty($existingReport['resolution_notes']))
                            <div class="p-4 rounded-2xl bg-emerald-50/70 border border-emerald-200 space-y-1.5 shadow-2xs">
                                <div class="flex items-center justify-between text-emerald-950 font-extrabold text-xs">
                                    <span class="flex items-center gap-1.5">
                                        <i class="fas fa-shield-alt text-emerald-600"></i> Admin Resolution Notes
                                    </span>
                                    <span class="text-[10px] text-emerald-700 font-semibold">{{ $existingReport['updated_at'] }}</span>
                                </div>
                                <div class="p-3 rounded-xl bg-white border border-emerald-100 text-slate-800 text-xs leading-relaxed">
                                    {{ $existingReport['resolution_notes'] }}
                                </div>
                                <span class="text-[10px] text-emerald-700 block">Reviewed by: {{ $existingReport['resolved_by'] }}</span>
                            </div>
                        @else
                            <div class="p-3 rounded-xl bg-slate-50 border border-slate-100 text-center text-[11px] text-slate-400 italic">
                                Admin response is currently pending.
                            </div>
                        @endif

                        <div class="pt-2">
                            <button wire:click="closeModal" class="w-full py-2.5 rounded-xl bg-slate-900 hover:bg-slate-800 text-white font-extrabold text-xs shadow-xs transition cursor-pointer">
                                Close
                            </button>
                        </div>

                    </div>

                <!-- STATE B: SUBMIT NEW REPORT FORM -->
                @else
                    <form wire:submit="submitReport" class="space-y-4 pt-1 text-xs">
                        
                        <!-- REASON DROPDOWN -->
                        <div>
                            <label class="block font-bold text-slate-700 mb-1.5">
                                Reason for Reporting <span class="text-rose-500">*</span>
                            </label>
                            <select wire:model="reportTitle" required class="w-full p-2.5 rounded-xl border border-slate-200 bg-slate-50 text-xs font-semibold text-slate-800 focus:bg-white focus:border-pp-600 outline-none transition">
                                @foreach($reasonOptions as $reason => $hint)
                                    <option value="{{ $reason }}">{{ $reason }} — {{ $hint }}</option>
                                @endforeach
                            </select>
                            @error('reportTitle') <span class="text-rose-500 text-xs font-semibold mt-1 block">{{ $message }}</span> @enderror
                        </div>

                        <!-- DESCRIPTION TEXTAREA -->
                        <div>
                            <label class="block font-bold text-slate-700 mb-1.5">
                                Additional Details / Evidence <span class="text-slate-400 font-normal">(Optional)</span>
                            </label>
                            <textarea wire:model="reportDescription" rows="3" placeholder="Please provide any context or details to help moderators investigate quickly..." class="w-full p-3 rounded-xl border border-slate-200 bg-slate-50 text-xs text-slate-800 focus:bg-white focus:border-pp-600 outline-none transition"></textarea>
                            @error('reportDescription') <span class="text-rose-500 text-xs font-semibold mt-1 block">{{ $message }}</span> @enderror
                        </div>

                        <div class="p-3 rounded-xl bg-slate-50 border border-slate-100 text-[11px] text-slate-500 leading-relaxed">
                            <i class="fas fa-info-circle text-pp-600 mr-1"></i> Reports are confidential and reviewed within 24 hours. You will receive an email update with any admin resolution notes.
                        </div>

                        <!-- ACTION BUTTONS -->
                        <div class="flex items-center justify-end gap-2.5 pt-2">
                            <button type="button" wire:click="closeModal" class="px-4 py-2.5 rounded-xl border border-slate-200 hover:bg-slate-50 text-slate-700 font-bold text-xs transition cursor-pointer">
                                Cancel
                            </button>
                            <button type="submit" class="px-5 py-2.5 rounded-xl bg-rose-600 hover:bg-rose-700 text-white font-extrabold text-xs shadow-xs transition flex items-center gap-1.5 cursor-pointer">
                                <i class="fas fa-paper-plane text-[11px]"></i>
                                <span>Submit Report</span>
                            </button>
                        </div>
                    </form>
                @endif

            </div>
        </div>
    @endif
</div>
