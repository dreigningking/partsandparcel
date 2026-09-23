<div>
    @if ($isOpen)
        <!-- BACKDROP OVERLAY -->
        <div wire:click="closeDrawer" class="fixed inset-0 z-[98] bg-slate-950/50 backdrop-blur-xs transition"></div>

        <!-- QUICK-VIEW OFFER DRAWER (DESKTOP SLIDE-OVER) -->
        <div class="fixed top-0 right-0 bottom-0 z-[99] w-full sm:w-[480px] bg-white shadow-2xl flex flex-col transition">
            
            <!-- DRAWER HEADER -->
            <div class="h-16 px-6 border-b border-slate-100 flex items-center justify-between shrink-0 bg-white">
                <div>
                    <h3 class="font-extrabold text-slate-900 text-base flex items-center gap-2">
                        <i class="fas fa-handshake text-pp-600"></i>
                        <span>Offer Negotiation Quick View</span>
                    </h3>
                    <p class="text-[11px] text-slate-400">Response by: <strong class="text-slate-700">{{ $authorName }}</strong></p>
                </div>
                <button wire:click="closeDrawer" class="w-8 h-8 rounded-lg hover:bg-slate-100 text-slate-500 font-bold text-lg grid place-items-center cursor-pointer">
                    <i class="fas fa-times"></i>
                </button>
            </div>

            <!-- PAGINATION / SLIDER HEADER -->
            @if (count($rounds) > 0)
                @php
                    $currentRound = $rounds[$currentRoundIndex];
                    $totalRounds = count($rounds);
                @endphp

                <div class="px-6 py-3 bg-pp-50/70 border-b border-pp-100 flex items-center justify-between text-xs">
                    <button wire:click="previousRound" @disabled($currentRoundIndex === 0) class="px-3 py-1.5 rounded-lg border border-slate-200 bg-white font-bold text-slate-700 hover:bg-slate-50 transition disabled:opacity-40 disabled:cursor-not-allowed flex items-center gap-1 cursor-pointer">
                        <i class="fas fa-chevron-left text-[10px]"></i> Previous Round
                    </button>

                    <div class="text-center font-extrabold text-pp-800">
                        Round {{ $currentRoundIndex + 1 }} of {{ $totalRounds }}
                    </div>

                    <button wire:click="nextRound" @disabled($currentRoundIndex === $totalRounds - 1) class="px-3 py-1.5 rounded-lg border border-slate-200 bg-white font-bold text-slate-700 hover:bg-slate-50 transition disabled:opacity-40 disabled:cursor-not-allowed flex items-center gap-1 cursor-pointer">
                        Next Round <i class="fas fa-chevron-right text-[10px]"></i>
                    </button>
                </div>

                <!-- DRAWER BODY - ACTIVE SLIDE CONTENT -->
                <div class="flex-1 min-h-0 overflow-y-auto p-6 space-y-5 text-xs">
                    
                    <div class="p-4 rounded-2xl bg-white border-2 border-pp-500 space-y-3 shadow-2xs">
                        <div class="flex items-center justify-between border-b border-slate-100 pb-2">
                            <div>
                                <span class="font-extrabold text-slate-900 text-xs">{{ $currentRound['from'] }}</span>
                                @if(!empty($currentRound['to']))
                                    <span class="text-slate-400 text-[10px] block">to {{ $currentRound['to'] }}</span>
                                @endif
                            </div>
                            <span class="px-2.5 py-0.5 rounded-full bg-pp-100 text-pp-800 font-extrabold text-[10px] uppercase">
                                {{ $currentRound['status'] }}
                            </span>
                        </div>

                        <div class="space-y-1">
                            <span class="text-slate-400 font-medium block">Proposed Price:</span>
                            <span class="text-2xl font-black text-slate-950">{{ $currentRound['price'] }}</span>
                        </div>

                        <div class="grid grid-cols-2 gap-2 text-slate-600">
                            <div class="p-2.5 rounded-xl bg-slate-50 border border-slate-100">
                                <span class="text-[10px] text-slate-400 font-bold uppercase block">Warranty</span>
                                <span class="font-bold text-slate-800">{{ $currentRound['warranty'] }}</span>
                            </div>

                            <div class="p-2.5 rounded-xl bg-slate-50 border border-slate-100">
                                <span class="text-[10px] text-slate-400 font-bold uppercase block">Fulfillment</span>
                                <span class="font-bold text-slate-800">{{ $currentRound['delivery'] }}</span>
                            </div>
                        </div>

                        <div class="p-3 rounded-xl bg-slate-50 border border-slate-100 italic text-slate-700 leading-relaxed">
                            "{{ $currentRound['message'] }}"
                        </div>

                        <div class="text-[10px] text-slate-400 text-right">
                            <i class="fas fa-clock mr-1"></i> {{ $currentRound['time'] }}
                        </div>
                    </div>

                </div>

                <!-- DRAWER FOOTER ACTIONS -->
                <div class="p-4 border-t border-slate-100 bg-white flex flex-col gap-2 shrink-0">
                    @if (!empty($currentRound['can_accept']))
                        <button wire:click="acceptCurrentOffer({{ $currentRound['id'] }})" class="w-full py-3 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-extrabold text-xs text-center shadow-xs transition flex items-center justify-center gap-1.5 cursor-pointer">
                            <i class="fas fa-check-circle"></i>
                            <span>Accept Round {{ $currentRoundIndex + 1 }} Offer ({{ $currentRound['price'] }})</span>
                        </button>
                    @endif

                    <a href="{{ route('offers.view', ['offer_id' => 'OFF-' . $currentRound['id']]) }}" class="w-full py-2.5 rounded-xl bg-slate-900 hover:bg-slate-800 text-white font-extrabold text-xs text-center shadow-xs transition block">
                        Open Full Offer Thread &amp; Counter →
                    </a>
                </div>
            @endif

        </div>
    @endif
</div>