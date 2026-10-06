<div class="flex flex-col gap-6">

  <!-- ========================================================================= -->
  <!-- 1. SHIPMENT LINE ITEMS BREAKDOWN (PICKUP / DELIVERY / RETURNS) -->
  <!-- ========================================================================= -->
  @php
    $logisticsItems = $invoice->items->whereIn('type', ['pickup', 'delivery', 'return']);
  @endphp
  @if ($logisticsItems->isNotEmpty())
    <div class="bg-white rounded-3xl border border-slate-200 p-6 shadow-soft space-y-3">
      <h3 class="text-xs font-black text-slate-900 uppercase tracking-wider flex items-center gap-2">
        <i class="fas fa-boxes-packing text-pp-600"></i> Agreed Logistics &amp; Transport Services
      </h3>
      <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3 text-xs">
        @foreach ($logisticsItems as $lItem)
          <div class="p-3.5 rounded-2xl bg-slate-50 border border-slate-200/80 space-y-1">
            <div class="flex items-center justify-between">
              <span class="px-2 py-0.5 rounded bg-slate-900 text-white text-[9px] font-black uppercase">
                {{ $lItem->type }}
              </span>
              <strong class="text-slate-950 font-black">{{ $invoice->currency_symbol }}{{ number_format($lItem->amount) }}</strong>
            </div>
            <strong class="text-slate-900 block font-bold truncate">{{ $lItem->description }}</strong>
            <span class="text-[11px] text-slate-500 block">Qty: {{ $lItem->quantity }}</span>
          </div>
        @endforeach
      </div>
    </div>
  @endif

  <!-- ========================================================================= -->
  <!-- 2. OUTBOUND SHIPMENT CARD -->
  <!-- ========================================================================= -->
  <div class="bg-white rounded-3xl border border-slate-200 p-6 sm:p-7 shadow-soft space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 border-b border-slate-100 pb-4">
      <div class="flex items-center gap-3">
        <div class="w-11 h-11 rounded-2xl bg-pp-50 text-pp-600 flex items-center justify-center text-lg shrink-0 border border-pp-200/60 shadow-2xs">
          <i class="fas fa-truck-fast"></i>
        </div>
        <div>
          <h2 class="text-sm font-black text-slate-950 uppercase tracking-wider flex items-center gap-2">
            <span>Outbound Waybill &amp; Dispatch Tracking</span>
            @if ($outboundShipment)
              <span class="px-2.5 py-0.5 rounded-full text-[10px] font-black uppercase {{ $outboundShipment->status === 'delivered' ? 'bg-emerald-100 text-emerald-800' : ($outboundShipment->status === 'dispatched' ? 'bg-pp-100 text-pp-800' : 'bg-amber-100 text-amber-800') }}">
                {{ $outboundShipment->status }}
              </span>
            @else
              <span class="px-2.5 py-0.5 rounded-full bg-slate-100 text-slate-600 text-[10px] font-black uppercase">
                Awaiting Dispatch
              </span>
            @endif
          </h2>
          <p class="text-xs text-slate-500 mt-0.5">
            Seller dispatch to buyer delivery address with live progress and reception confirmation.
          </p>
        </div>
      </div>

      <!-- SELLER / BUYER ACTION BUTTONS -->
      <div class="flex items-center gap-2 self-start sm:self-auto flex-wrap">
        @if ($isSeller)
          @if (! $outboundShipment || ! in_array($outboundShipment->status, ['dispatched', 'delivered']))
            <button wire:click="openShipmentModal" type="button" class="px-4 py-2 rounded-xl bg-pp-600 hover:bg-pp-700 text-white font-extrabold text-xs shadow-xs transition flex items-center gap-2 cursor-pointer">
              <i class="fas fa-truck"></i>
              <span>Mark Shipped &amp; Add Tracking</span>
            </button>
          @else
            <button wire:click="openShipmentModal" type="button" class="px-3.5 py-2 rounded-xl border border-slate-200 hover:bg-slate-50 text-slate-700 font-bold text-xs transition flex items-center gap-1.5 cursor-pointer">
              <i class="fas fa-pen text-[10px]"></i>
              <span>Update Tracking</span>
            </button>
          @endif
        @endif

        @if ($isBuyer && $outboundShipment && $outboundShipment->status === 'dispatched')
          <button wire:click="openReceiveConfirmModal" type="button" class="px-4 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-extrabold text-xs shadow-xs transition flex items-center gap-2 cursor-pointer">
            <i class="fas fa-box-open"></i>
            <span>Confirm Package Received</span>
          </button>
        @endif
      </div>
    </div>

    @if ($outboundShipment)
      <!-- PROGRESS TIMELINE STEPPER -->
      <div class="p-5 rounded-2xl bg-slate-50/80 border border-slate-200">
        <h4 class="text-[11px] font-black uppercase tracking-wider text-slate-400 mb-4 flex items-center gap-1.5">
          <i class="fas fa-route text-pp-600"></i> Dispatch Progress Timeline
        </h4>

        <div class="grid grid-cols-3 gap-2 text-center text-xs">
          <!-- STAGE 1: ORDER PAID -->
          <div class="space-y-1">
            <div class="w-8 h-8 rounded-full bg-emerald-600 text-white font-extrabold grid place-items-center mx-auto shadow-2xs">✓</div>
            <strong class="text-slate-900 block text-[11px]">Paid &amp; Ready</strong>
            <span class="text-[10px] text-slate-400">Escrow Secured</span>
          </div>

          <!-- STAGE 2: DISPATCHED -->
          @php $isDispatched = in_array($outboundShipment->status, ['dispatched', 'delivered']); @endphp
          <div class="space-y-1 {{ ! $isDispatched ? 'opacity-50' : '' }}">
            <div class="w-8 h-8 rounded-full {{ $isDispatched ? 'bg-emerald-600 text-white' : 'bg-slate-200 text-slate-600' }} font-extrabold grid place-items-center mx-auto shadow-2xs">
              {{ $isDispatched ? '✓' : '2' }}
            </div>
            <strong class="text-slate-900 block text-[11px]">In Transit</strong>
            <span class="text-[10px] text-slate-400">
              {{ $outboundShipment->dispatched_at ? $outboundShipment->dispatched_at->format('M d, H:i') : 'Pending Seller' }}
            </span>
          </div>

          <!-- STAGE 3: DELIVERED -->
          @php $isDelivered = $outboundShipment->status === 'delivered'; @endphp
          <div class="space-y-1 {{ ! $isDelivered ? 'opacity-50' : '' }}">
            <div class="w-8 h-8 rounded-full {{ $isDelivered ? 'bg-emerald-600 text-white' : 'bg-slate-200 text-slate-600' }} font-extrabold grid place-items-center mx-auto shadow-2xs">
              {{ $isDelivered ? '✓' : '3' }}
            </div>
            <strong class="text-slate-900 block text-[11px]">Delivered</strong>
            <span class="text-[10px] text-slate-400">
              {{ $outboundShipment->delivered_at ? $outboundShipment->delivered_at->format('M d, H:i') : 'Pending Delivery' }}
            </span>
          </div>
        </div>
      </div>

      <!-- KEY SHIPMENT DETAILS GRID -->
      <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3.5 text-xs">
        <div class="p-4 rounded-2xl bg-white border border-slate-200 space-y-1">
          <span class="text-[10px] font-bold uppercase text-slate-400 block">Waybill / Tracking #</span>
          <strong class="text-sm font-black text-pp-700 block tracking-wider font-mono">
            {{ $outboundShipment->tracking_number }}
          </strong>
          <span class="text-[11px] text-slate-500 block">Provider: {{ $outboundShipment->provider_name }}</span>
        </div>

        <div class="p-4 rounded-2xl bg-white border border-slate-200 space-y-1">
          <span class="text-[10px] font-bold uppercase text-slate-400 block">Origin (Pickup Location)</span>
          <strong class="text-slate-950 font-extrabold block truncate">
            {{ $outboundShipment->origin_city ?: 'Seller Point' }}, {{ $outboundShipment->origin_state ?: 'Lagos' }}
          </strong>
          <span class="text-[11px] text-slate-500 block truncate">
            Contact: {{ $outboundShipment->origin_contact_name ?: $invoice->seller->name }}
          </span>
        </div>

        <div class="p-4 rounded-2xl bg-white border border-slate-200 space-y-1">
          <span class="text-[10px] font-bold uppercase text-slate-400 block">Destination (Delivery Point)</span>
          <strong class="text-slate-950 font-extrabold block truncate">
            {{ $outboundShipment->destination_city ?: 'Customer Address' }}, {{ $outboundShipment->destination_state ?: 'Lagos' }}
          </strong>
          <span class="text-[11px] text-slate-500 block truncate">
            Recipient: {{ $outboundShipment->destination_contact_name ?: $invoice->buyer->name }}
          </span>
        </div>

        <div class="p-4 rounded-2xl bg-white border border-slate-200 space-y-1">
          <span class="text-[10px] font-bold uppercase text-slate-400 block">Dispatched Timestamp</span>
          <strong class="text-slate-950 font-extrabold block">
            {{ $outboundShipment->dispatched_at ? $outboundShipment->dispatched_at->format('M d, Y · h:i A') : 'Awaiting Dispatch' }}
          </strong>
          <span class="text-[11px] text-slate-500 block">
            Status: {{ Str::headline($outboundShipment->status) }}
          </span>
        </div>
      </div>

      <!-- DISPATCH EVIDENCE & NOTES -->
      @if ($outboundShipment->notes || $outboundShipment->evidence)
        <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200 space-y-2 text-xs">
          @if ($outboundShipment->notes)
            <div>
              <strong class="font-black text-slate-900 block text-[11px] uppercase tracking-wider">Seller Dispatch Notes:</strong>
              <p class="text-slate-700 mt-0.5 leading-relaxed">{{ $outboundShipment->notes }}</p>
            </div>
          @endif
          @if ($outboundShipment->evidence)
            <div class="pt-2 border-t border-slate-200/60 flex items-center gap-2">
              <span class="text-slate-500 font-bold text-[11px]">Waybill Evidence Link:</span>
              <a href="{{ $outboundShipment->evidence }}" target="_blank" rel="noopener noreferrer" class="text-pp-600 font-extrabold hover:underline truncate">
                {{ $outboundShipment->evidence }} <i class="fas fa-external-link-alt text-[9px]"></i>
              </a>
            </div>
          @endif
        </div>
      @endif

    @else
      <div class="p-8 rounded-2xl bg-slate-50 border border-slate-200 text-center text-xs space-y-2">
        <div class="w-12 h-12 rounded-2xl bg-slate-200 text-slate-500 grid place-items-center mx-auto text-lg mb-2">
          <i class="fas fa-box"></i>
        </div>
        <h4 class="text-sm font-extrabold text-slate-900">No outbound shipment recorded yet</h4>
        <p class="text-slate-500 max-w-md mx-auto">
          @if ($isSeller)
            The package has not yet been dispatched. Once you send the parcel via courier or dispatch rider, click "Mark Shipped &amp; Add Tracking" above to record tracking details.
          @else
            Your seller is preparing your order for dispatch. Tracking information will appear here once the seller ships your package.
          @endif
        </p>
      </div>
    @endif
  </div>

  <!-- ========================================================================= -->
  <!-- 3. REVERSE RETURN SHIPMENT (IF RETURN INVOLVED) -->
  <!-- ========================================================================= -->
  @if ($returnShipment || $latestReturn)
    <div class="bg-white rounded-3xl border border-indigo-200 p-6 sm:p-7 shadow-soft space-y-5">
      <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 border-b border-indigo-100 pb-4">
        <div class="flex items-center gap-3">
          <div class="w-10 h-10 rounded-2xl bg-indigo-50 text-indigo-700 flex items-center justify-center text-base shrink-0 border border-indigo-200">
            <i class="fas fa-arrow-rotate-left"></i>
          </div>
          <div>
            <h3 class="text-sm font-black text-indigo-950 uppercase tracking-wider flex items-center gap-2">
              <span>Return Shipment (Reverse Logistics)</span>
              @if ($latestReturn)
                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-black uppercase bg-indigo-100 text-indigo-900">
                  {{ $latestReturn->status }}
                </span>
              @endif
            </h3>
            <p class="text-xs text-slate-500 mt-0.5">
              Customer return package back to the seller for verified inspection, replacement, or refund.
            </p>
          </div>
        </div>

        <!-- RETURN ACTIONS -->
        <div class="flex items-center gap-2 self-start sm:self-auto">
          @if ($isBuyer && $latestReturn && in_array($latestReturn->status, ['pending', 'approved']))
            <button wire:click="openBuyerReturnModal({{ $latestReturn->id }})" type="button" class="px-4 py-2 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white font-extrabold text-xs shadow-xs transition flex items-center gap-1.5 cursor-pointer">
              <i class="fas fa-paper-plane"></i>
              <span>Confirm Return Dispatched</span>
            </button>
          @elseif ($isSeller && $latestReturn && in_array($latestReturn->status, ['in_transit', 'shipped']))
            <button wire:click="sellerConfirmReturnReceived({{ $latestReturn->id }})" type="button" class="px-4 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-extrabold text-xs shadow-xs transition flex items-center gap-1.5 cursor-pointer">
              <i class="fas fa-check-circle"></i>
              <span>Confirm Return Received</span>
            </button>
          @endif
        </div>
      </div>

      @if ($returnShipment)
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 text-xs">
          <div class="p-3.5 rounded-xl bg-indigo-50/50 border border-indigo-100 space-y-1">
            <span class="text-[10px] font-bold text-slate-400 uppercase block">Return Tracking #</span>
            <strong class="text-sm font-black text-indigo-900 block font-mono">{{ $returnShipment->tracking_number }}</strong>
            <span class="text-[11px] text-slate-500 block">Courier: {{ $returnShipment->provider_name }}</span>
          </div>

          <div class="p-3.5 rounded-xl bg-indigo-50/50 border border-indigo-100 space-y-1">
            <span class="text-[10px] font-bold text-slate-400 uppercase block">Return Route</span>
            <strong class="text-slate-900 font-extrabold block truncate">
              {{ $returnShipment->origin_city ?: 'Buyer' }} → {{ $returnShipment->destination_city ?: 'Seller' }}
            </strong>
            <span class="text-[11px] text-slate-500 block">Delivery: {{ strtoupper($latestReturn->delivery_method ?? 'courier') }}</span>
          </div>

          <div class="p-3.5 rounded-xl bg-indigo-50/50 border border-indigo-100 space-y-1">
            <span class="text-[10px] font-bold text-slate-400 uppercase block">Return Status</span>
            <strong class="text-slate-900 font-extrabold block">
              {{ Str::headline($returnShipment->status) }}
            </strong>
            <span class="text-[11px] text-slate-500 block">
              {{ $latestReturn->received_at ? 'Received ' . $latestReturn->received_at->format('M d') : 'In progress' }}
            </span>
          </div>
        </div>
      @endif
    </div>
  @endif

</div>
