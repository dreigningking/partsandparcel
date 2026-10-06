<div class="space-y-6">

  <!-- ========================================================================= -->
  <!-- REPLACEMENTS AUDIT HEADER -->
  <!-- ========================================================================= -->
  <div class="bg-white dark:bg-slate-900 rounded-3xl border border-teal-200 dark:border-teal-900/60 p-6 sm:p-7 shadow-soft space-y-4">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 border-b border-teal-100 dark:border-teal-900/40 pb-4">
      <div class="flex items-center gap-3">
        <div class="w-11 h-11 rounded-2xl bg-teal-50 dark:bg-teal-950/60 text-teal-600 dark:text-teal-400 flex items-center justify-center text-lg shrink-0 border border-teal-200 dark:border-teal-800">
          <i class="fas fa-boxes-packing"></i>
        </div>
        <div>
          <h2 class="text-sm font-black text-teal-950 dark:text-teal-300 uppercase tracking-wider flex items-center gap-2">
            <span>Replacement Units &amp; Exchange Pipeline</span>
            <span class="px-2.5 py-0.5 rounded-full bg-teal-100 text-teal-900 text-[10px] font-black uppercase">
              Admin Inspection
            </span>
          </h2>
          <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">
            Audit seller-agreed replacement hardware consignments, return verification milestones, and replacement courier tracking.
          </p>
        </div>
      </div>

      <span class="text-xs text-slate-400 font-bold self-start sm:self-auto">
        {{ $replacements->count() }} {{ Str::plural('replacement', $replacements->count()) }} linked
      </span>
    </div>
  </div>

  <!-- ========================================================================= -->
  <!-- REPLACEMENTS LIST -->
  <!-- ========================================================================= -->
  @if ($replacements->isEmpty())
    <div class="p-8 rounded-3xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 text-center space-y-2 shadow-soft">
      <div class="w-12 h-12 rounded-2xl bg-slate-100 dark:bg-slate-800 text-slate-400 flex items-center justify-center text-xl mx-auto mb-2">
        <i class="fas fa-rotate"></i>
      </div>
      <h3 class="text-sm font-black text-slate-900 dark:text-white">No Replacement Orders</h3>
      <p class="text-xs text-slate-500 max-w-md mx-auto">There are no replacement parcels or hardware swaps arranged for this invoice.</p>
    </div>
  @else
    @foreach ($replacements as $replacement)
      @php
        $adminReplacementStatus = match($replacement->status) {
          'delivered', 'accepted' => [
            'label' => 'Replacement Delivered to Buyer',
            'badge' => 'bg-emerald-100 text-emerald-800 border-emerald-200 dark:bg-emerald-950/60 dark:text-emerald-300',
            'icon' => 'fa-circle-check text-emerald-600',
          ],
          'dispatched' => [
            'label' => 'Replacement in Transit with Carrier',
            'badge' => 'bg-blue-100 text-blue-800 border-blue-200 dark:bg-blue-950/60 dark:text-blue-300',
            'icon' => 'fa-truck-fast text-blue-600',
          ],
          'ready_for_dispatch' => [
            'label' => 'Return Verified (Ready for Replacement Dispatch)',
            'badge' => 'bg-teal-100 text-teal-800 border-teal-200 dark:bg-teal-950/60 dark:text-teal-300',
            'icon' => 'fa-box text-teal-600',
          ],
          default => [
            'label' => 'Waiting for Seller to Dispatch Replacement Unit',
            'badge' => 'bg-amber-100 text-amber-800 border-amber-200 dark:bg-amber-950/60 dark:text-amber-300',
            'icon' => 'fa-clock text-amber-600',
          ],
        };
      @endphp

      <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200 dark:border-slate-800 p-6 shadow-soft space-y-4">
        
        <!-- HEADER WITH ADMIN STATUS -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 border-b border-slate-100 dark:border-slate-800 pb-3">
          <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-2xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 flex items-center justify-center text-base shrink-0">
              <i class="fas {{ $adminReplacementStatus['icon'] }}"></i>
            </div>
            <div>
              <div class="flex items-center gap-2 flex-wrap">
                <span class="px-2.5 py-0.5 rounded-xl bg-teal-100 dark:bg-teal-950/60 text-teal-900 dark:text-teal-300 font-black text-xs uppercase flex items-center gap-1.5">
                  <i class="fas fa-boxes-packing text-teal-600"></i>
                  <span>#REP-{{ str_pad($replacement->id, 4, '0', STR_PAD_LEFT) }}</span>
                </span>
                <span class="px-2.5 py-0.5 rounded-full text-xs font-black uppercase tracking-wider border {{ $adminReplacementStatus['badge'] }}">
                  {{ $adminReplacementStatus['label'] }}
                </span>
              </div>
              <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">
                Arranged on {{ $replacement->created_at->format('M d, Y · h:i A') }}
              </p>
            </div>
          </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 text-xs">
          <div class="p-4 rounded-2xl bg-slate-50 dark:bg-slate-800/40 border border-slate-200/80 dark:border-slate-800 space-y-1">
            <span class="text-[10px] font-bold text-slate-400 uppercase block">Delivery Method</span>
            <strong class="text-slate-900 dark:text-white font-extrabold block uppercase">
              {{ $replacement->delivery_method ?: 'Courier Consignment' }}
            </strong>
            <span class="text-[11px] text-slate-500 block">Assigned by merchant</span>
          </div>

          <div class="p-4 rounded-2xl bg-slate-50 dark:bg-slate-800/40 border border-slate-200/80 dark:border-slate-800 space-y-1">
            <span class="text-[10px] font-bold text-slate-400 uppercase block">Waybill / Tracking</span>
            <strong class="text-slate-900 dark:text-white font-mono font-extrabold block truncate">
              {{ $replacement->shipment?->tracking_number ?: 'Pending Dispatch' }}
            </strong>
            <span class="text-[11px] text-slate-500 block truncate">{{ $replacement->shipment?->provider_name ?: 'Standard Carrier' }}</span>
          </div>

          <div class="p-4 rounded-2xl bg-slate-50 dark:bg-slate-800/40 border border-slate-200/80 dark:border-slate-800 space-y-1">
            <span class="text-[10px] font-bold text-slate-400 uppercase block">Linked Issue</span>
            <strong class="text-slate-900 dark:text-white font-extrabold block">
              #ISS-{{ str_pad($replacement->issue_id, 4, '0', STR_PAD_LEFT) }}
            </strong>
            <span class="text-[11px] text-slate-500 block truncate">{{ $replacement->issue?->type ? Str::headline($replacement->issue->type) : 'Customer Issue' }}</span>
          </div>
        </div>

        @if ($replacement->notes)
          <div class="text-xs text-slate-600 dark:text-slate-400 bg-slate-50 dark:bg-slate-800/40 p-3 rounded-xl border border-slate-200/80 dark:border-slate-800">
            <strong class="text-slate-800 dark:text-slate-200 font-bold">Notes:</strong> {{ $replacement->notes }}
          </div>
        @endif

      </div>
    @endforeach
  @endif

</div>
