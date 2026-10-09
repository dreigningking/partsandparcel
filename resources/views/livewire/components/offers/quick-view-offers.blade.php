<div x-data="{
    open: @entangle('isOpen'),
    init() {
        window.addEventListener('open-quick-view-offer', () => { this.open = true; });
        window.addEventListener('close-quick-view-offer', () => { this.open = false; });
    }
}">
    <!-- BACKDROP OVERLAY -->
    <div class="overlay fixed inset-0 z-[98] bg-slate-950/50 backdrop-blur-xs transition"
         :class="{ 'open': open }"
         @click="open = false; $wire.closeDrawer()"></div>

    <!-- QUICK-VIEW OFFER DRAWER (DESKTOP SLIDE-OVER) -->
    <div class="drawer fixed top-0 right-0 bottom-0 z-[99] w-full sm:w-[480px] bg-white shadow-2xl flex flex-col"
         :class="{ 'open': open }">
        
        <!-- LOADING STATE -->
        <div wire:loading.flex wire:target="loadOffers" class="absolute inset-0 bg-white/90 z-20 flex-col items-center justify-center p-8 text-center gap-3">
            <div class="w-8 h-8 rounded-full border-2 border-pp-600 border-t-transparent animate-spin"></div>
            <span class="text-xs font-semibold text-slate-500">Loading offer details...</span>
        </div>

        <!-- DRAWER HEADER -->
        <div class="h-16 px-6 border-b border-slate-100 flex items-center justify-between shrink-0 bg-white">
            <div>
                <h3 class="font-extrabold text-slate-900 text-base flex items-center gap-2">
                    <i class="fas fa-handshake text-pp-600"></i>
                    <span>Offer Negotiation Quick View</span>
                </h3>
                <p class="text-[11px] text-slate-400">Response by: <strong class="text-slate-700">{{ $authorName }}</strong></p>
            </div>
            <button type="button" @click="open = false; $wire.closeDrawer()" class="w-8 h-8 rounded-lg hover:bg-slate-100 text-slate-500 font-bold text-lg grid place-items-center cursor-pointer">
                <i class="fas fa-times"></i>
            </button>
        </div>

            <!-- NON-BINDING & AVAILABILITY DISCLAIMER BANNER -->
            <div class="px-6 py-2.5 bg-amber-50 border-b border-amber-200 text-amber-900 text-[11px] flex items-center gap-2 shrink-0">
                <i class="fas fa-info-circle text-amber-600 shrink-0"></i>
                <span>Offers are subject to item availability and non-binding until payment is completed.</span>
            </div>

            <!-- FLASH NOTIFICATIONS -->
            @if (session()->has('message'))
                <div class="mx-6 mt-3 p-3 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs font-semibold flex items-center gap-2">
                    <i class="fas fa-check-circle text-emerald-600"></i>
                    <span>{{ session('message') }}</span>
                </div>
            @endif

            @if (session()->has('error'))
                <div class="mx-6 mt-3 p-3 rounded-xl bg-rose-50 border border-rose-200 text-rose-800 text-xs font-semibold flex items-center gap-2">
                    <i class="fas fa-exclamation-circle text-rose-600"></i>
                    <span>{{ session('error') }}</span>
                </div>
            @endif

            <!-- ROUNDS SLIDER & ITEM STEPPER -->
            @if (count($rounds) > 0)
                @php
                    $currentRound = $rounds[$currentRoundIndex];
                    $totalRounds = count($rounds);
                    $offerItems = $currentRound['items'] ?? [];
                    $totalItems = count($offerItems);
                @endphp

                <!-- ROUND CONTROLS -->
                <div class="px-6 py-2.5 bg-pp-50/70 border-b border-pp-100 flex items-center justify-between text-xs shrink-0">
                    <button wire:click="previousRound" @disabled($currentRoundIndex === 0) class="px-2.5 py-1 rounded-lg border border-slate-200 bg-white font-bold text-slate-700 hover:bg-slate-50 transition disabled:opacity-40 disabled:cursor-not-allowed flex items-center gap-1 cursor-pointer">
                        <i class="fas fa-chevron-left text-[10px]"></i> Prev Round
                    </button>

                    <div class="text-center font-extrabold text-pp-800 text-xs">
                        Round {{ $currentRoundIndex + 1 }} of {{ $totalRounds }}
                    </div>

                    <button wire:click="nextRound" @disabled($currentRoundIndex === $totalRounds - 1) class="px-2.5 py-1 rounded-lg border border-slate-200 bg-white font-bold text-slate-700 hover:bg-slate-50 transition disabled:opacity-40 disabled:cursor-not-allowed flex items-center gap-1 cursor-pointer">
                        Next Round <i class="fas fa-chevron-right text-[10px]"></i>
                    </button>
                </div>

                <!-- ITEM-BY-ITEM WIZARD STEPPER (IF MULTIPLE ITEMS) -->
                @if ($totalItems > 1)
                    <div class="px-6 py-2 bg-slate-50 border-b border-slate-200 flex items-center justify-between text-xs shrink-0">
                        <button wire:click="prevItemStep" @disabled($itemStep <= 1) class="text-[11px] font-bold text-slate-600 hover:text-slate-900 disabled:opacity-30 cursor-pointer">
                            <i class="fas fa-arrow-left text-[9px] mr-1"></i> Prev Item
                        </button>
                        <span class="text-[11px] font-extrabold text-slate-800">
                            Item {{ min($itemStep, $totalItems) }} of {{ $totalItems }}
                        </span>
                        <button wire:click="nextItemStep" @disabled($itemStep >= $totalItems) class="text-[11px] font-bold text-slate-600 hover:text-slate-900 disabled:opacity-30 cursor-pointer">
                            Next Item <i class="fas fa-arrow-right text-[9px] ml-1"></i>
                        </button>
                    </div>
                @endif

                <!-- DRAWER BODY - ACTIVE CONTENT -->
                <div class="flex-1 min-h-0 overflow-y-auto p-6 space-y-4 text-xs">
                    
                    <div class="p-4 rounded-2xl bg-white border-2 border-pp-500 space-y-3 shadow-2xs">
                        <div class="flex items-center justify-between border-b border-slate-100 pb-2">
                            <div>
                                <span class="font-extrabold text-slate-900 text-xs">{{ $currentRound['from'] }}</span>
                                @if(!empty($currentRound['to']))
                                    <span class="text-slate-400 text-[10px] block">to {{ $currentRound['to'] }}</span>
                                @endif
                            </div>
                            <span class="px-2.5 py-0.5 rounded-full {{ strtolower($currentRound['status']) === 'accepted' ? 'bg-emerald-100 text-emerald-800' : 'bg-pp-100 text-pp-800' }} font-extrabold text-[10px] uppercase">
                                {{ $currentRound['status'] }}
                            </span>
                        </div>

                        <!-- ITEM FOCUS BREAKDOWN -->
                        @if ($totalItems > 0 && isset($offerItems[$itemStep - 1]))
                            @php $activeItem = $offerItems[$itemStep - 1]; @endphp
                            <div class="p-3 rounded-xl bg-pp-50/60 border border-pp-100 space-y-1.5">
                                <span class="text-[9px] uppercase font-black text-pp-700 tracking-wider">Item Details</span>
                                <h4 class="font-extrabold text-slate-900 text-xs">{{ $activeItem['description'] }}</h4>
                                <div class="flex items-center gap-3 text-[11px] text-slate-600 font-semibold">
                                    <span>Quantity: <strong class="text-slate-900">{{ $activeItem['quantity'] }}</strong></span>
                                    <span>·</span>
                                    <span>Unit Price: <strong class="text-pp-700">₦{{ number_format($activeItem['unit_price']) }}</strong></span>
                                </div>
                                @if ($activeItem['warranty_period_days'])
                                    <div class="text-[10px] text-slate-500 mt-1">
                                        <i class="fas fa-shield-alt text-emerald-600 mr-1"></i> {{ $activeItem['warranty_period_days'] }} Days ({{ $activeItem['warranty_terms'] ?: 'Inspection warranty' }})
                                    </div>
                                @endif
                            </div>
                        @endif

                        <div class="space-y-1">
                            <span class="text-slate-400 font-medium block">Total Proposed Price:</span>
                            <span class="text-2xl font-black text-slate-950">{{ $currentRound['price'] }}</span>
                        </div>

                        <div class="grid grid-cols-2 gap-2 text-slate-600">
                            <div class="p-2.5 rounded-xl bg-slate-50 border border-slate-100">
                                <span class="text-[10px] text-slate-400 font-bold uppercase block">Overall Warranty</span>
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

                        <div class="text-[10px] text-slate-400 flex items-center justify-between">
                            <span>Negotiation Ref: <strong class="text-slate-600">#OFF-{{ $currentRound['id'] }}</strong></span>
                            <span><i class="fas fa-clock mr-1"></i> {{ $currentRound['time'] }}</span>
                        </div>
                    </div>

                </div>

                <!-- DRAWER FOOTER ACTIONS -->
                <div class="p-4 border-t border-slate-100 bg-white flex flex-col gap-2 shrink-0">
                    @if (!empty($currentRound['can_accept']))
                        <button wire:click="acceptCurrentOffer({{ $currentRound['id'] }})" class="w-full py-3 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-extrabold text-xs text-center shadow-xs transition flex items-center justify-center gap-1.5 cursor-pointer">
                            <i class="fas fa-check-circle"></i>
                            <span>Accept Offer ({{ $currentRound['price'] }})</span>
                        </button>
                    @endif

                    @if (!empty($currentRound['can_edit']))
                        <button wire:click="launchCounterDrawer({{ $currentRound['id'] }}, true)" class="w-full py-2.5 rounded-xl bg-amber-500 hover:bg-amber-600 text-white font-extrabold text-xs text-center shadow-xs transition flex items-center justify-center gap-1.5 cursor-pointer">
                            <i class="fas fa-edit"></i>
                            <span>Edit My Offer</span>
                        </button>
                    @endif

                    @if (!empty($currentRound['can_counter']))
                        <button wire:click="launchCounterDrawer({{ $currentRound['id'] }}, false)" class="w-full py-2.5 rounded-xl bg-pp-600 hover:bg-pp-700 text-white font-extrabold text-xs text-center shadow-xs transition flex items-center justify-center gap-1.5 cursor-pointer">
                            <i class="fas fa-exchange-alt"></i>
                            <span>Make Counter Offer</span>
                        </button>
                    @endif

                    <a href="{{ route('offers.view', ['offer_id' => 'OFF-' . $currentRound['id']]) }}" class="w-full py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs text-center transition block">
                        Open Full Offer Thread →
                    </a>
                </div>
            @else
                <div class="p-8 text-center text-slate-500 text-xs">
                    <i class="fas fa-inbox text-3xl text-slate-300 mb-2 block"></i>
                    <span>No active offers recorded for this request yet.</span>
                </div>
            @endif

        </div>
</div>