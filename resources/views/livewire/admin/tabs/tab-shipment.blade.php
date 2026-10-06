<div class="space-y-6">

  <!-- ========================================================================= -->
  <!-- SHIPMENT AUDIT HEADER -->
  <!-- ========================================================================= -->
  <div class="bg-white dark:bg-slate-900 rounded-3xl border border-pp-200 dark:border-pp-900/60 p-6 sm:p-7 shadow-soft space-y-3">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 border-b border-pp-100 dark:border-pp-900/40 pb-4">
      <div class="flex items-center gap-3">
        <div class="w-11 h-11 rounded-2xl bg-pp-50 dark:bg-pp-950/60 text-pp-700 dark:text-pp-300 flex items-center justify-center text-lg shrink-0 border border-pp-200/60 dark:border-pp-800/60">
          <i class="fas fa-truck-fast"></i>
        </div>
        <div>
          <h2 class="text-sm font-black text-slate-950 dark:text-white uppercase tracking-wider flex items-center gap-2">
            <span>Logistics Dispatch &amp; Waybill Tracking</span>
            <span class="px-2.5 py-0.5 rounded-full bg-pp-100 text-pp-800 dark:bg-pp-900/60 dark:text-pp-300 text-[10px] font-black uppercase">
              Admin Inspection
            </span>
          </h2>
          <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">
            Read-only logistics ledger auditing pickups, carrier transit waybills, receiver delivery addresses, and delivery proof.
          </p>
        </div>
      </div>

      <span class="text-xs text-slate-400 font-bold self-start sm:self-auto">
        {{ $allShipments->count() }} {{ Str::plural('consignments', $allShipments->count()) }} linked
      </span>
    </div>
  </div>

  <!-- ========================================================================= -->
  <!-- SHIPMENTS LIST -->
  <!-- ========================================================================= -->
  @if ($allShipments->isEmpty())
    <div class="p-8 rounded-3xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 text-center space-y-2 shadow-soft">
      <div class="w-12 h-12 rounded-2xl bg-slate-100 dark:bg-slate-800 text-slate-400 flex items-center justify-center text-xl mx-auto mb-2">
        <i class="fas fa-boxes-packing"></i>
      </div>
      <h3 class="text-sm font-black text-slate-900 dark:text-white">No Shipments Recorded</h3>
      <p class="text-xs text-slate-500 max-w-md mx-auto">There are no carrier dispatches, waybills, or courier pickups currently linked to this commercial invoice.</p>
    </div>
  @else
    @foreach ($allShipments as $shp)
      @php
        $adminShipmentStatus = match($shp->status) {
          'delivered' => [
            'label' => 'Delivered to Buyer',
            'badge' => 'bg-emerald-100 text-emerald-800 border-emerald-200 dark:bg-emerald-950/60 dark:text-emerald-300',
            'icon' => 'fa-circle-check text-emerald-600',
          ],
          'dispatched', 'in_transit' => [
            'label' => 'In Transit with Carrier',
            'badge' => 'bg-blue-100 text-blue-800 border-blue-200 dark:bg-blue-950/60 dark:text-blue-300',
            'icon' => 'fa-truck-moving text-blue-600',
          ],
          'returned' => [
            'label' => 'Returned to Seller',
            'badge' => 'bg-purple-100 text-purple-800 border-purple-200 dark:bg-purple-950/60 dark:text-purple-300',
            'icon' => 'fa-arrow-turn-down-left text-purple-600',
          ],
          'cancelled' => [
            'label' => 'Shipment Cancelled',
            'badge' => 'bg-rose-100 text-rose-800 border-rose-200 dark:bg-rose-950/60 dark:text-rose-300',
            'icon' => 'fa-ban text-rose-600',
          ],
          default => [
            'label' => 'Waiting for Vendor / Seller to dispatch',
            'badge' => 'bg-amber-100 text-amber-800 border-amber-200 dark:bg-amber-950/60 dark:text-amber-300',
            'icon' => 'fa-clock text-amber-600',
          ],
        };
      @endphp

      <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200 dark:border-slate-800 p-6 shadow-soft space-y-5">
        
        <!-- HEADER WITH ADMIN STATUS -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 border-b border-slate-100 dark:border-slate-800 pb-4">
          <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-2xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 flex items-center justify-center text-base shrink-0">
              <i class="fas {{ $adminShipmentStatus['icon'] }}"></i>
            </div>
            <div>
              <div class="flex items-center gap-2 flex-wrap">
                <span class="text-xs font-black text-slate-400 uppercase">Waybill / Tracking:</span>
                <strong class="font-mono text-xs font-black text-pp-600 dark:text-pp-400">
                  {{ $shp->tracking_number ?: ('#SHP-' . $shp->id) }}
                </strong>
                <span class="px-2.5 py-0.5 rounded-full text-xs font-black uppercase tracking-wider border {{ $adminShipmentStatus['badge'] }}">
                  {{ $adminShipmentStatus['label'] }}
                </span>
              </div>
              <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">
                Carrier: <strong>{{ $shp->provider_name ?: 'Standard Courier' }}</strong>
                @if ($shp->dispatched_at)
                  · Dispatched: {{ $shp->dispatched_at->format('M d, Y · h:i A') }}
                @endif
                @if ($shp->delivered_at)
                  · Delivered: {{ $shp->delivered_at->format('M d, Y · h:i A') }}
                @endif
              </p>
            </div>
          </div>

          <!-- ADMIN STATUS BADGE -->
          <div class="self-start sm:self-auto text-right">
            <span class="text-[10px] font-bold text-slate-400 uppercase block">Logistics Charge</span>
            <strong class="text-sm font-extrabold text-slate-900 dark:text-white block">
              {{ $invoice->currency_symbol }}{{ number_format((float) ($shp->fee ?: 0), 2) }}
            </strong>
          </div>
        </div>

        <!-- SENDER ORIGIN & RECEIVER DESTINATION -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-xs">
          <!-- Origin Location -->
          <div class="p-4 rounded-2xl bg-slate-50 dark:bg-slate-800/40 border border-slate-200/80 dark:border-slate-800 space-y-1.5">
            <span class="text-[10px] font-black uppercase tracking-wider text-slate-400 flex items-center gap-1">
              <i class="fas fa-arrow-up text-emerald-600"></i> Origin (Sender / Pickup)
            </span>
            <strong class="text-slate-900 dark:text-white block">{{ $shp->origin_contact_name ?: ($shp->sender?->name ?? 'Seller') }}</strong>
            @if ($shp->origin_contact_phone)
              <p class="font-mono text-slate-500 text-[11px]">{{ $shp->origin_contact_phone }}</p>
            @endif
            <p class="text-slate-600 dark:text-slate-300 leading-relaxed">{{ $shp->formatted_origin_address }}</p>
          </div>

          <!-- Destination Location -->
          <div class="p-4 rounded-2xl bg-slate-50 dark:bg-slate-800/40 border border-slate-200/80 dark:border-slate-800 space-y-1.5">
            <span class="text-[10px] font-black uppercase tracking-wider text-slate-400 flex items-center gap-1">
              <i class="fas fa-arrow-down text-pp-600"></i> Destination (Receiver / Delivery)
            </span>
            <strong class="text-slate-900 dark:text-white block">{{ $shp->destination_contact_name ?: ($shp->receiver?->name ?? 'Buyer') }}</strong>
            @if ($shp->destination_contact_phone)
              <p class="font-mono text-slate-500 text-[11px]">{{ $shp->destination_contact_phone }}</p>
            @endif
            <p class="text-slate-600 dark:text-slate-300 leading-relaxed">{{ $shp->formatted_destination_address }}</p>
          </div>
        </div>

        <!-- CONSIGNMENT ITEMS -->
        @if ($shp->items->isNotEmpty())
          <div class="space-y-2 pt-2">
            <h4 class="text-[11px] font-extrabold uppercase text-slate-400 tracking-wider">Consignment Items inside Package</h4>
            <div class="divide-y divide-slate-100 dark:divide-slate-800 border border-slate-200/80 dark:border-slate-800 rounded-2xl overflow-hidden text-xs">
              @foreach ($shp->items as $sItem)
                <div class="p-3 bg-white dark:bg-slate-900 flex items-center justify-between">
                  <div>
                    <strong class="font-bold text-slate-900 dark:text-white">{{ $sItem->item_name ?: 'Line item' }}</strong>
                    @if ($sItem->notes)
                      <p class="text-[11px] text-slate-400 mt-0.5">{{ $sItem->notes }}</p>
                    @endif
                  </div>
                  <span class="px-2 py-0.5 rounded bg-slate-100 dark:bg-slate-800 font-extrabold text-[10px]">
                    Qty: {{ $sItem->quantity ?: 1 }}
                  </span>
                </div>
              @endforeach
            </div>
          </div>
        @endif

        <!-- DISPATCH EVIDENCE -->
        @if ($shp->evidence)
          <div class="space-y-1.5 pt-1 text-xs">
            <span class="text-[10px] font-black uppercase tracking-wider text-slate-400">Waybill / Dispatch Evidence Note</span>
            <div class="p-3 rounded-2xl bg-slate-50 dark:bg-slate-800/40 border border-slate-200/80 dark:border-slate-800 text-slate-700 dark:text-slate-300">
              {{ $shp->evidence }}
            </div>
          </div>
        @endif

      </div>
    @endforeach
  @endif

</div>
