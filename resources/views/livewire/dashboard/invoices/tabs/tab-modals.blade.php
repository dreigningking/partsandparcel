<div>

  <!-- ========================================================================= -->
  <!-- 1. SELLER SHIPMENT DISPATCH MODAL -->
  <!-- ========================================================================= -->
  @if ($showShipmentModal)
    <div class="fixed inset-0 z-50 flex items-center justify-center bg-slate-950/60 backdrop-blur-xs p-4 overflow-y-auto">
      <div class="bg-white rounded-3xl max-w-lg w-full p-6 space-y-4 shadow-xl border border-slate-200">
        <div class="flex items-center justify-between border-b border-slate-100 pb-3">
          <h3 class="text-sm font-extrabold text-slate-900 flex items-center gap-2">
            <i class="fas fa-truck text-pp-600"></i> Mark Package as Shipped
          </h3>
          <button wire:click="closeModals" type="button" class="text-slate-400 hover:text-slate-600 text-lg cursor-pointer">×</button>
        </div>

        <div class="space-y-3 text-xs">
          <div>
            <label class="font-bold text-slate-700 block mb-1">Carrier / Courier Service *</label>
            <input type="text" wire:model="carrierName" placeholder="e.g. GIGM, DHL, Speedaf, In-house Rider" class="w-full p-2.5 rounded-xl border border-slate-200 text-xs font-medium focus:border-pp-500 outline-none" />
            @error('carrierName') <span class="text-rose-500 text-[11px]">{{ $message }}</span> @enderror
          </div>

          <div>
            <label class="font-bold text-slate-700 block mb-1">Tracking / Waybill Number *</label>
            <input type="text" wire:model="trackingNumber" placeholder="Waybill or tracking code" class="w-full p-2.5 rounded-xl border border-slate-200 text-xs font-mono font-bold focus:border-pp-500 outline-none uppercase" />
            @error('trackingNumber') <span class="text-rose-500 text-[11px]">{{ $message }}</span> @enderror
          </div>

          <div>
            <label class="font-bold text-slate-700 block mb-1">Dispatch Evidence / Receipt Link</label>
            <textarea wire:model="dispatchEvidence" rows="2" placeholder="Image link, receipt reference, or tracking URL..." class="w-full p-2.5 rounded-xl border border-slate-200 text-xs focus:border-pp-500 outline-none"></textarea>
          </div>

          <div>
            <label class="font-bold text-slate-700 block mb-1">Notes to Buyer (Optional)</label>
            <textarea wire:model="dispatchNotes" rows="2" placeholder="e.g. Package packed with bubble wrap, driver phone: 080..." class="w-full p-2.5 rounded-xl border border-slate-200 text-xs focus:border-pp-500 outline-none"></textarea>
          </div>
        </div>

        <div class="flex items-center justify-end gap-2 pt-2 border-t border-slate-100">
          <button wire:click="closeModals" type="button" class="px-4 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold cursor-pointer">Cancel</button>
          <button wire:click="markAsShipped" type="button" class="px-5 py-2 rounded-xl bg-pp-600 hover:bg-pp-700 text-white text-xs font-extrabold shadow-xs cursor-pointer">Confirm Dispatch</button>
        </div>
      </div>
    </div>
  @endif

  <!-- ========================================================================= -->
  <!-- 2. BUYER CONFIRM RECEPTION MODAL -->
  <!-- ========================================================================= -->
  @if ($showReceiveConfirmModal)
    <div class="fixed inset-0 z-50 flex items-center justify-center bg-slate-950/60 backdrop-blur-xs p-4">
      <div class="bg-white rounded-3xl max-w-md w-full p-6 space-y-4 shadow-xl border border-slate-200 text-center">
        <div class="w-12 h-12 rounded-2xl bg-emerald-100 text-emerald-700 flex items-center justify-center text-xl mx-auto">
          <i class="fas fa-box-open"></i>
        </div>
        <h3 class="text-base font-extrabold text-slate-950">Confirm Package Reception</h3>
        <p class="text-xs text-slate-600 leading-relaxed">
          Are you sure you have physically received this package from <strong>{{ $outboundShipment?->provider_name ?? 'the courier' }}</strong>?
        </p>
        <div class="flex items-center justify-center gap-2 pt-2">
          <button wire:click="closeModals" type="button" class="px-4 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold cursor-pointer">No, Not Yet</button>
          <button wire:click="confirmPackageReceived" type="button" class="px-5 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-extrabold shadow-xs cursor-pointer">Yes, I Have It!</button>
        </div>
      </div>
    </div>
  @endif

  <!-- ========================================================================= -->
  <!-- 3. BUYER REPORT ISSUE MODAL -->
  <!-- ========================================================================= -->
  @if ($showIssueModal)
    <div class="fixed inset-0 z-50 flex items-center justify-center bg-slate-950/60 backdrop-blur-xs p-4 overflow-y-auto">
      <div class="bg-white rounded-3xl max-w-lg w-full p-6 space-y-4 shadow-xl border border-slate-200">
        <div class="flex items-center justify-between border-b border-slate-100 pb-3">
          <h3 class="text-sm font-extrabold text-rose-700 flex items-center gap-2">
            <i class="fas fa-exclamation-triangle"></i> Report an Issue on this Order
          </h3>
          <button wire:click="closeModals" type="button" class="text-slate-400 hover:text-slate-600 text-lg cursor-pointer">×</button>
        </div>

        <div class="space-y-3 text-xs">
          <div>
            <label class="font-bold text-slate-700 block mb-1">Issue Category *</label>
            <select wire:model="issueType" class="w-full p-2.5 rounded-xl border border-slate-200 text-xs focus:border-pp-500 outline-none">
              <option value="damaged">Damaged in Transit</option>
              <option value="defective">Defective / Not Working</option>
              <option value="wrong_item">Wrong Item Delivered</option>
              <option value="missing">Incomplete / Missing Components</option>
            </select>
          </div>

          <div>
            <label class="font-bold text-slate-700 block mb-1">Affected Line Item</label>
            <select wire:model="issueItemId" class="w-full p-2.5 rounded-xl border border-slate-200 text-xs focus:border-pp-500 outline-none">
              <option value="">All Items in Package</option>
              @foreach ($invoice->items as $iItem)
                <option value="{{ $iItem->id }}">{{ $iItem->description }} (Qty: {{ $iItem->quantity }})</option>
              @endforeach
            </select>
          </div>

          <div>
            <label class="font-bold text-slate-700 block mb-1">Issue Explanation *</label>
            <textarea wire:model="issueDescription" rows="3" placeholder="Provide detailed information on what is wrong with the package..." class="w-full p-2.5 rounded-xl border border-slate-200 text-xs focus:border-pp-500 outline-none"></textarea>
            @error('issueDescription') <span class="text-rose-500 text-[11px]">{{ $message }}</span> @enderror
          </div>

          <div>
            <label class="font-bold text-slate-700 block mb-1">Evidence (Photos / Video Links)</label>
            <input type="text" wire:model="issueEvidence" placeholder="Upload or photo URL showing the defect..." class="w-full p-2.5 rounded-xl border border-slate-200 text-xs focus:border-pp-500 outline-none" />
          </div>
        </div>

        <div class="flex items-center justify-end gap-2 pt-2 border-t border-slate-100">
          <button wire:click="closeModals" type="button" class="px-4 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold cursor-pointer">Cancel</button>
          <button wire:click="submitReportIssue" type="button" class="px-5 py-2 rounded-xl bg-rose-600 hover:bg-rose-700 text-white text-xs font-extrabold shadow-xs cursor-pointer">Submit Issue &amp; Freeze Escrow</button>
        </div>
      </div>
    </div>
  @endif

  <!-- ========================================================================= -->
  <!-- 4. SELLER ISSUE RESOLUTION MODAL -->
  <!-- ========================================================================= -->
  @if ($showSellerIssueResponseModal)
    <div class="fixed inset-0 z-50 flex items-center justify-center bg-slate-950/60 backdrop-blur-xs p-4 overflow-y-auto">
      <div class="bg-white rounded-3xl max-w-lg w-full p-6 space-y-4 shadow-xl border border-slate-200">
        <div class="flex items-center justify-between border-b border-slate-100 pb-3">
          <h3 class="text-sm font-extrabold text-slate-900 flex items-center gap-2">
            <i class="fas fa-handshake-angle text-pp-600"></i> Propose Resolution
          </h3>
          <button wire:click="closeModals" type="button" class="text-slate-400 hover:text-slate-600 text-lg cursor-pointer">×</button>
        </div>

        <div class="space-y-3 text-xs">
          <div>
            <label class="font-bold text-slate-700 block mb-1">Resolution Remedy *</label>
            <select wire:model="sellerResolutionType" class="w-full p-2.5 rounded-xl border border-slate-200 text-xs focus:border-pp-500 outline-none">
              <option value="replacement">Send Replacement Unit</option>
              <option value="refund">Issue Refund</option>
            </select>
          </div>

          <div class="p-3 rounded-xl bg-slate-50 border border-slate-200 space-y-2">
            <label class="flex items-center gap-2 cursor-pointer font-bold text-slate-900">
              <input type="checkbox" wire:model="sellerRequiresReturn" class="accent-pp-600 w-4 h-4 rounded" />
              <span>Require buyer to return the defective/wrong item first</span>
            </label>

            @if ($sellerRequiresReturn)
              <div class="pt-2">
                <label class="font-bold text-slate-700 block mb-1">Return Method</label>
                <select wire:model="sellerReturnMethod" class="w-full p-2 rounded-lg border border-slate-200 text-xs">
                  <option value="shipment">Return by Courier / Shipment</option>
                  <option value="dropoff">Buyer Drops Off at Seller Location</option>
                </select>
              </div>
            @endif
          </div>

          <div>
            <label class="font-bold text-slate-700 block mb-1">Notes / Instructions to Buyer</label>
            <textarea wire:model="sellerResponseNotes" rows="2" placeholder="Instructions on return packaging or replacement timeframe..." class="w-full p-2.5 rounded-xl border border-slate-200 text-xs focus:border-pp-500 outline-none"></textarea>
          </div>
        </div>

        <div class="flex items-center justify-end gap-2 pt-2 border-t border-slate-100">
          <button wire:click="closeModals" type="button" class="px-4 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold cursor-pointer">Cancel</button>
          <button wire:click="sellerRespondToIssue" type="button" class="px-5 py-2 rounded-xl bg-slate-900 hover:bg-slate-800 text-white text-xs font-extrabold shadow-xs cursor-pointer">Confirm Resolution Proposal</button>
        </div>
      </div>
    </div>
  @endif

  <!-- ========================================================================= -->
  <!-- 5. BUYER RETURN DISPATCH MODAL -->
  <!-- ========================================================================= -->
  @if ($showBuyerReturnModal)
    <div class="fixed inset-0 z-50 flex items-center justify-center bg-slate-950/60 backdrop-blur-xs p-4 overflow-y-auto">
      <div class="bg-white rounded-3xl max-w-lg w-full p-6 space-y-4 shadow-xl border border-slate-200">
        <div class="flex items-center justify-between border-b border-slate-100 pb-3">
          <h3 class="text-sm font-extrabold text-indigo-700 flex items-center gap-2">
            <i class="fas fa-paper-plane"></i> Confirm Return Package Dispatched
          </h3>
          <button wire:click="closeModals" type="button" class="text-slate-400 hover:text-slate-600 text-lg cursor-pointer">×</button>
        </div>

        <div class="space-y-3 text-xs">
          <div>
            <label class="font-bold text-slate-700 block mb-1">Return Carrier / Service</label>
            <input type="text" wire:model="returnCarrier" placeholder="e.g. GIGM, Speedaf, Personal Dropoff" class="w-full p-2.5 rounded-xl border border-slate-200 text-xs focus:border-pp-500 outline-none" />
          </div>

          <div>
            <label class="font-bold text-slate-700 block mb-1">Return Waybill / Tracking Number</label>
            <input type="text" wire:model="returnTrackingNumber" placeholder="Waybill code" class="w-full p-2.5 rounded-xl border border-slate-200 text-xs font-mono font-bold uppercase focus:border-pp-500 outline-none" />
          </div>

          <div>
            <label class="font-bold text-slate-700 block mb-1">Waybill Receipt / Evidence URL</label>
            <input type="text" wire:model="returnEvidence" placeholder="Photo link of waybill slip or receipt" class="w-full p-2.5 rounded-xl border border-slate-200 text-xs focus:border-pp-500 outline-none" />
          </div>

          <div>
            <label class="font-bold text-slate-700 block mb-1">Return Notes (Optional)</label>
            <textarea wire:model="returnNotes" rows="2" placeholder="e.g. Package sent in original box..." class="w-full p-2.5 rounded-xl border border-slate-200 text-xs focus:border-pp-500 outline-none"></textarea>
          </div>
        </div>

        <div class="flex items-center justify-end gap-2 pt-2 border-t border-slate-100">
          <button wire:click="closeModals" type="button" class="px-4 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold cursor-pointer">Cancel</button>
          <button wire:click="buyerConfirmReturnShipped" type="button" class="px-5 py-2 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-extrabold shadow-xs cursor-pointer">Save Return Dispatch</button>
        </div>
      </div>
    </div>
  @endif

  <!-- ========================================================================= -->
  <!-- 6. BUYER WARRANTY CLAIM MODAL -->
  <!-- ========================================================================= -->
  @if ($showWarrantyModal)
    <div class="fixed inset-0 z-50 flex items-center justify-center bg-slate-950/60 backdrop-blur-xs p-4 overflow-y-auto">
      <div class="bg-white rounded-3xl max-w-lg w-full p-6 space-y-4 shadow-xl border border-slate-200">
        <div class="flex items-center justify-between border-b border-slate-100 pb-3">
          <h3 class="text-sm font-extrabold text-emerald-800 flex items-center gap-2">
            <i class="fas fa-screwdriver-wrench"></i> File Warranty Claim
          </h3>
          <button wire:click="closeModals" type="button" class="text-slate-400 hover:text-slate-600 text-lg cursor-pointer">×</button>
        </div>

        <div class="space-y-3 text-xs">
          <div>
            <label class="font-bold text-slate-700 block mb-1">Target Item</label>
            <select wire:model="warrantyItemId" class="w-full p-2.5 rounded-xl border border-slate-200 text-xs focus:border-pp-500 outline-none">
              <option value="">All Warranted Items</option>
              @foreach ($invoice->items->where('warranty_period_days', '>', 0) as $wItem)
                <option value="{{ $wItem->id }}">{{ $wItem->description }} ({{ $wItem->warranty_period_days }} Days)</option>
              @endforeach
            </select>
          </div>

          <div>
            <label class="font-bold text-slate-700 block mb-1">Fault Description *</label>
            <textarea wire:model="warrantyReason" rows="3" placeholder="Describe what failed or stopped working under warranty terms..." class="w-full p-2.5 rounded-xl border border-slate-200 text-xs focus:border-pp-500 outline-none"></textarea>
            @error('warrantyReason') <span class="text-rose-500 text-[11px]">{{ $message }}</span> @enderror
          </div>

          <div>
            <label class="font-bold text-slate-700 block mb-1">Diagnostic Evidence / Photos</label>
            <input type="text" wire:model="warrantyEvidence" placeholder="Link to photo or video showing the fault" class="w-full p-2.5 rounded-xl border border-slate-200 text-xs focus:border-pp-500 outline-none" />
          </div>
        </div>

        <div class="flex items-center justify-end gap-2 pt-2 border-t border-slate-100">
          <button wire:click="closeModals" type="button" class="px-4 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold cursor-pointer">Cancel</button>
          <button wire:click="submitWarrantyClaim" type="button" class="px-5 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-extrabold shadow-xs cursor-pointer">Submit Warranty Claim</button>
        </div>
      </div>
    </div>
  @endif

  <!-- ========================================================================= -->
  <!-- 7. SELLER WARRANTY RESPONSE MODAL -->
  <!-- ========================================================================= -->
  @if ($showSellerWarrantyModal)
    <div class="fixed inset-0 z-50 flex items-center justify-center bg-slate-950/60 backdrop-blur-xs p-4 overflow-y-auto">
      <div class="bg-white rounded-3xl max-w-lg w-full p-6 space-y-4 shadow-xl border border-slate-200">
        <div class="flex items-center justify-between border-b border-slate-100 pb-3">
          <h3 class="text-sm font-extrabold text-slate-900 flex items-center gap-2">
            <i class="fas fa-shield-halved text-emerald-600"></i> Respond to Warranty Claim
          </h3>
          <button wire:click="closeModals" type="button" class="text-slate-400 hover:text-slate-600 text-lg cursor-pointer">×</button>
        </div>

        <div class="space-y-3 text-xs">
          <div>
            <label class="font-bold text-slate-700 block mb-1">Warranty Decision *</label>
            <select wire:model="warrantyResolutionDecision" class="w-full p-2.5 rounded-xl border border-slate-200 text-xs focus:border-pp-500 outline-none">
              <option value="accept">Accept Claim &amp; Send Replacement / Repair</option>
              <option value="reject">Reject Claim (Outside Terms / User Damage)</option>
            </select>
          </div>

          <div>
            <label class="font-bold text-slate-700 block mb-1">Notes / Diagnostic Explanation</label>
            <textarea wire:model="warrantyResolutionNotes" rows="3" placeholder="Provide instructions for replacement dispatch or explanation for rejection..." class="w-full p-2.5 rounded-xl border border-slate-200 text-xs focus:border-pp-500 outline-none"></textarea>
          </div>
        </div>

        <div class="flex items-center justify-end gap-2 pt-2 border-t border-slate-100">
          <button wire:click="closeModals" type="button" class="px-4 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold cursor-pointer">Cancel</button>
          <button wire:click="sellerRespondToWarrantyClaim" type="button" class="px-5 py-2 rounded-xl bg-slate-900 hover:bg-slate-800 text-white text-xs font-extrabold shadow-xs cursor-pointer">Submit Response</button>
        </div>
      </div>
    </div>
  @endif

</div>
