<div class="flex flex-col gap-6">

  <!-- ========================================================================= -->
  <!-- REPLACEMENTS HEADER -->
  <!-- ========================================================================= -->
  <div class="bg-white rounded-3xl border border-amber-200 p-6 sm:p-7 shadow-soft space-y-4">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 border-b border-amber-100 pb-4">
      <div class="flex items-center gap-3">
        <div class="w-11 h-11 rounded-2xl bg-amber-50 text-amber-600 flex items-center justify-center text-lg shrink-0 border border-amber-200">
          <i class="fas fa-repeat"></i>
        </div>
        <div>
          <h2 class="text-sm font-black text-amber-950 uppercase tracking-wider flex items-center gap-2">
            <span>Agreed Item Replacements</span>
            <span class="px-2.5 py-0.5 rounded-full bg-amber-100 text-amber-800 text-[10px] font-black uppercase">
              Replacement Pipeline
            </span>
          </h2>
          <p class="text-xs text-slate-500 mt-0.5">
            Seller-agreed replacement units for defective or damaged items, tracking fulfillment from return to delivery.
          </p>
        </div>
      </div>
    </div>
  </div>

  <!-- ========================================================================= -->
  <!-- REPLACEMENTS LIST -->
  <!-- ========================================================================= -->
  <div class="space-y-4">
    @forelse ($replacements as $replacement)
      <div class="bg-white rounded-3xl border border-slate-200 p-6 shadow-soft space-y-5">
        
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 border-b border-slate-100 pb-3">
          <div class="flex items-center gap-2 flex-wrap">
            <span class="px-2.5 py-1 rounded-xl bg-amber-100 text-amber-900 font-black text-xs uppercase flex items-center gap-1.5">
              <i class="fas fa-box-archive text-amber-600"></i>
              <span>Replacement #REP-{{ str_pad($replacement->id, 4, '0', STR_PAD_LEFT) }}</span>
            </span>
            <span class="text-xs text-slate-400">·</span>
            <span class="text-xs text-slate-500">Agreed {{ $replacement->created_at->format('M d, Y') }}</span>
          </div>

          <span class="px-3 py-1 rounded-full text-xs font-black uppercase self-start sm:self-auto {{ $replacement->status === 'delivered' ? 'bg-emerald-100 text-emerald-800' : ($replacement->status === 'dispatched' ? 'bg-pp-100 text-pp-800' : 'bg-amber-100 text-amber-800') }}">
            {{ Str::headline($replacement->status) }}
          </span>
        </div>

        <!-- PROGRESS STEPPER -->
        <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200">
          <div class="grid grid-cols-4 gap-2 text-center text-xs">
            <div class="space-y-1">
              <div class="w-7 h-7 rounded-full bg-emerald-600 text-white font-extrabold grid place-items-center mx-auto text-xs">✓</div>
              <strong class="text-slate-900 block text-[10px]">Agreed</strong>
            </div>

            @php $isReturnReceived = in_array($replacement->status, ['ready_for_dispatch', 'dispatched', 'delivered']); @endphp
            <div class="space-y-1 {{ ! $isReturnReceived ? 'opacity-50' : '' }}">
              <div class="w-7 h-7 rounded-full {{ $isReturnReceived ? 'bg-emerald-600 text-white' : 'bg-slate-200 text-slate-600' }} font-extrabold grid place-items-center mx-auto text-xs">
                {{ $isReturnReceived ? '✓' : '2' }}
              </div>
              <strong class="text-slate-900 block text-[10px]">Return Verified</strong>
            </div>

            @php $isDispatched = in_array($replacement->status, ['dispatched', 'delivered']); @endphp
            <div class="space-y-1 {{ ! $isDispatched ? 'opacity-50' : '' }}">
              <div class="w-7 h-7 rounded-full {{ $isDispatched ? 'bg-emerald-600 text-white' : 'bg-slate-200 text-slate-600' }} font-extrabold grid place-items-center mx-auto text-xs">
                {{ $isDispatched ? '✓' : '3' }}
              </div>
              <strong class="text-slate-900 block text-[10px]">Replacement Sent</strong>
            </div>

            @php $isDelivered = $replacement->status === 'delivered'; @endphp
            <div class="space-y-1 {{ ! $isDelivered ? 'opacity-50' : '' }}">
              <div class="w-7 h-7 rounded-full {{ $isDelivered ? 'bg-emerald-600 text-white' : 'bg-slate-200 text-slate-600' }} font-extrabold grid place-items-center mx-auto text-xs">
                {{ $isDelivered ? '✓' : '4' }}
              </div>
              <strong class="text-slate-900 block text-[10px]">Delivered &amp; Done</strong>
            </div>
          </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 text-xs">
          <div class="p-3.5 rounded-xl bg-slate-50 border border-slate-200 space-y-1">
            <span class="text-[10px] font-bold text-slate-400 uppercase block">Delivery Method</span>
            <strong class="text-slate-900 font-extrabold block uppercase">{{ $replacement->delivery_method ?: 'Courier Shipment' }}</strong>
            <span class="text-[11px] text-slate-500 block">Assigned by seller</span>
          </div>

          <div class="p-3.5 rounded-xl bg-slate-50 border border-slate-200 space-y-1">
            <span class="text-[10px] font-bold text-slate-400 uppercase block">Replacement Tracking</span>
            <strong class="text-slate-900 font-extrabold block font-mono">
              {{ $replacement->shipment?->tracking_number ?: 'Pending Dispatch' }}
            </strong>
            <span class="text-[11px] text-slate-500 block">{{ $replacement->shipment?->provider_name ?: 'Courier Logistics' }}</span>
          </div>

          <div class="p-3.5 rounded-xl bg-slate-50 border border-slate-200 space-y-1">
            <span class="text-[10px] font-bold text-slate-400 uppercase block">Related Issue</span>
            <strong class="text-slate-900 font-extrabold block">#ISS-{{ str_pad($replacement->issue_id, 4, '0', STR_PAD_LEFT) }}</strong>
            <button wire:click="switchTab('issue')" type="button" class="text-pp-600 hover:underline font-bold text-[11px] block">
              View Issue Statement →
            </button>
          </div>
        </div>

        @if ($replacement->notes)
          <div class="p-3.5 rounded-xl bg-amber-50/50 border border-amber-200 text-xs">
            <strong class="font-bold text-amber-950 block text-[11px] uppercase tracking-wider">Seller Instructions:</strong>
            <p class="text-amber-900 mt-0.5 leading-relaxed">{{ $replacement->notes }}</p>
          </div>
        @endif

      </div>
    @empty
      <div class="p-8 rounded-3xl bg-white border border-slate-200 text-center text-xs space-y-2 shadow-soft">
        <div class="w-12 h-12 rounded-2xl bg-amber-50 text-amber-600 grid place-items-center mx-auto text-lg mb-2">
          <i class="fas fa-repeat"></i>
        </div>
        <h4 class="text-sm font-extrabold text-slate-900">No replacements requested</h4>
        <p class="text-slate-500 max-w-md mx-auto">
          If items in this order are found defective and the seller agrees to provide a replacement, tracking and delivery updates will appear here.
        </p>
      </div>
    @endforelse
  </div>

</div>
