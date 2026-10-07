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
  <!-- 3. BUYER REPORT ISSUE MODAL (ITEMIZED REJECTION) -->
  <!-- ========================================================================= -->
  @if ($showIssueModal)
    <div class="fixed inset-0 z-50 flex items-center justify-center bg-slate-950/60 backdrop-blur-xs p-4 overflow-y-auto">
      <div class="bg-white rounded-3xl max-w-xl w-full p-6 space-y-4 shadow-xl border border-slate-200 my-8">
        <div class="flex items-center justify-between border-b border-slate-100 pb-3">
          <h3 class="text-sm font-extrabold text-rose-700 flex items-center gap-2">
            <i class="fas fa-triangle-exclamation"></i> Report Delivery Issue &amp; Reject Items
          </h3>
          <button wire:click="closeModals" type="button" class="text-slate-400 hover:text-slate-600 text-lg cursor-pointer">×</button>
        </div>

        <div class="space-y-4 text-xs">
          <!-- Primary Issue Category -->
          <div>
            <label class="font-bold text-slate-700 block mb-1">Issue Category *</label>
            <select wire:model="issueType" class="w-full p-2.5 rounded-xl border border-slate-200 text-xs focus:border-pp-500 outline-none">
              <option value="defective">Defective / Malfunctioning Unit</option>
              <option value="damaged">Damaged in Transit / Physical Crack</option>
              <option value="wrong_item">Wrong Item Delivered</option>
              <option value="incompatibility">Incompatibility with Equipment</option>
              <option value="not_as_described">Not as Described in Listing</option>
              <option value="lost_or_missing">Lost or Missing Components / Package</option>
            </select>
            @error('issueType') <span class="text-rose-500 text-[11px]">{{ $message }}</span> @enderror
          </div>

          <!-- Itemized Selection -->
          <div class="space-y-2">
            <label class="font-bold text-slate-700 block">
              Select Rejected Line Items * <span class="text-slate-400 font-normal">(Specify items you are rejecting)</span>
            </label>
            <div class="space-y-2 max-h-56 overflow-y-auto p-2 rounded-2xl bg-slate-50 border border-slate-200">
              @foreach ($invoice->items as $iItem)
                <div class="p-3 rounded-xl bg-white border border-slate-200 space-y-2">
                  <label class="flex items-start gap-2.5 cursor-pointer">
                    <input type="checkbox" wire:model="selectedIssueItemIds" value="{{ $iItem->id }}" class="mt-0.5 accent-rose-600 w-4 h-4 rounded" />
                    <div class="flex-1">
                      <strong class="text-slate-900 block font-bold text-xs">{{ $iItem->description }}</strong>
                      <span class="text-[11px] text-slate-500">Qty: {{ $iItem->quantity }} · {{ $invoice->currency_symbol }}{{ number_format($iItem->amount) }}</span>
                    </div>
                  </label>

                  @if (in_array($iItem->id, $selectedIssueItemIds))
                    <div class="pt-2 pl-6 border-t border-slate-100 grid grid-cols-1 sm:grid-cols-2 gap-2 text-[11px]">
                      <div>
                        <span class="text-slate-500 block mb-0.5 font-medium">Specific Item Reason:</span>
                        <input type="text" wire:model="issueItemReasons.{{ $iItem->id }}" placeholder="e.g. Scratched screen, dead battery" class="w-full p-1.5 rounded-lg border border-slate-200 text-xs" />
                      </div>
                      <div>
                        <span class="text-slate-500 block mb-0.5 font-medium">Item Photo/Evidence URL:</span>
                        <input type="text" wire:model="issueItemEvidences.{{ $iItem->id }}" placeholder="Photo URL or screenshot link" class="w-full p-1.5 rounded-lg border border-slate-200 text-xs" />
                      </div>
                    </div>
                  @endif
                </div>
              @endforeach
            </div>
            @error('selectedIssueItemIds') <span class="text-rose-500 text-[11px] block">{{ $message }}</span> @enderror
          </div>

          <!-- Overall Explanation -->
          <div>
            <label class="font-bold text-slate-700 block mb-1">Issue Explanation &amp; Rejection Reason *</label>
            <textarea wire:model="issueDescription" rows="3" placeholder="Provide detailed information explaining why the item(s) are being rejected..." class="w-full p-2.5 rounded-xl border border-slate-200 text-xs focus:border-pp-500 outline-none"></textarea>
            @error('issueDescription') <span class="text-rose-500 text-[11px]">{{ $message }}</span> @enderror
          </div>

          <!-- Overall Evidence -->
          <div>
            <label class="font-bold text-slate-700 block mb-1">General Evidence URL (Photo / Video link)</label>
            <input type="text" wire:model="issueEvidence" placeholder="Upload or photo URL showing the defect..." class="w-full p-2.5 rounded-xl border border-slate-200 text-xs focus:border-pp-500 outline-none" />
          </div>
        </div>

        <div class="flex items-center justify-end gap-2 pt-2 border-t border-slate-100">
          <button wire:click="closeModals" type="button" class="px-4 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold cursor-pointer">Cancel</button>
          <button wire:click="submitReportIssue" type="button" class="px-5 py-2 rounded-xl bg-rose-600 hover:bg-rose-700 text-white text-xs font-extrabold shadow-xs cursor-pointer">Submit Rejection &amp; Freeze Escrow</button>
        </div>
      </div>
    </div>
  @endif

  <!-- ========================================================================= -->
  <!-- 4. SELLER ISSUE RESOLUTION PROPOSAL MODAL -->
  <!-- ========================================================================= -->
  @if ($showSellerIssueResponseModal)
    <div class="fixed inset-0 z-50 flex items-center justify-center bg-slate-950/60 backdrop-blur-xs p-4 overflow-y-auto">
      <div class="bg-white rounded-3xl max-w-lg w-full p-6 space-y-4 shadow-xl border border-slate-200">
        <div class="flex items-center justify-between border-b border-slate-100 pb-3">
          <h3 class="text-sm font-extrabold text-slate-900 flex items-center gap-2">
            <i class="fas fa-handshake-angle text-pp-600"></i> Propose Resolution Remedy
          </h3>
          <button wire:click="closeModals" type="button" class="text-slate-400 hover:text-slate-600 text-lg cursor-pointer">×</button>
        </div>

        <div class="space-y-3 text-xs">
          <div>
            <label class="font-bold text-slate-700 block mb-1">Resolution Remedy *</label>
            <select wire:model="sellerResolutionType" class="w-full p-2.5 rounded-xl border border-slate-200 text-xs focus:border-pp-500 outline-none">
              <option value="replacement">Send Replacement Unit</option>
              <option value="refund">Issue Escrow Refund</option>
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
  <!-- 5. SELLER CONTEST ISSUE MODAL (DISAGREEMENT -> ESCALATES TO DISPUTE) -->
  <!-- ========================================================================= -->
  @if ($showSellerContestModal)
    <div class="fixed inset-0 z-50 flex items-center justify-center bg-slate-950/60 backdrop-blur-xs p-4 overflow-y-auto">
      <div class="bg-white rounded-3xl max-w-lg w-full p-6 space-y-4 shadow-xl border border-rose-200">
        <div class="flex items-center justify-between border-b border-rose-100 pb-3">
          <h3 class="text-sm font-extrabold text-rose-700 flex items-center gap-2">
            <i class="fas fa-scale-balanced"></i> Contest Issue &amp; Escalate to Dispute
          </h3>
          <button wire:click="closeModals" type="button" class="text-slate-400 hover:text-slate-600 text-lg cursor-pointer">×</button>
        </div>

        <div class="p-3.5 rounded-2xl bg-amber-50 border border-amber-200 text-xs text-amber-900 space-y-1">
          <strong class="font-extrabold block">Notice on Disputing Claims:</strong>
          <span>Contesting this issue indicates you disagree with the buyer's rejection. A formal arbitration case will be created for platform mediation, and escrow funds will remain locked.</span>
        </div>

        <div class="space-y-3 text-xs">
          <div>
            <label class="font-bold text-slate-700 block mb-1">Contestation Statement / Reason *</label>
            <textarea wire:model="contestReason" rows="3" placeholder="Explain why the buyer's claim is unjustified (e.g. tested before dispatch, buyer mishandled, wrong diagnosis)..." class="w-full p-2.5 rounded-xl border border-slate-200 text-xs focus:border-rose-500 outline-none"></textarea>
            @error('contestReason') <span class="text-rose-500 text-[11px]">{{ $message }}</span> @enderror
          </div>

          <div>
            <label class="font-bold text-slate-700 block mb-1">Testing / Quality Assurance Evidence (Link or Photo URL)</label>
            <input type="text" wire:model="contestEvidence" placeholder="Link to testing video, pre-dispatch photos, packaging proof..." class="w-full p-2.5 rounded-xl border border-slate-200 text-xs focus:border-rose-500 outline-none" />
          </div>
        </div>

        <div class="flex items-center justify-end gap-2 pt-2 border-t border-slate-100">
          <button wire:click="closeModals" type="button" class="px-4 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold cursor-pointer">Cancel</button>
          <button wire:click="sellerContestIssue" type="button" class="px-5 py-2 rounded-xl bg-rose-600 hover:bg-rose-700 text-white text-xs font-extrabold shadow-xs cursor-pointer">Open Formal Dispute</button>
        </div>
      </div>
    </div>
  @endif

  <!-- ========================================================================= -->
  <!-- 6. BUYER RETURN DISPATCH MODAL -->
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
            <textarea wire:model="returnNotes" rows="2" placeholder="e.g. Package sent in original box with all accessories..." class="w-full p-2.5 rounded-xl border border-slate-200 text-xs focus:border-pp-500 outline-none"></textarea>
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
  <!-- 7. SELLER RETURN REJECTION MODAL (CONDITION DISPUTE) -->
  <!-- ========================================================================= -->
  @if ($showSellerReturnRejectModal)
    <div class="fixed inset-0 z-50 flex items-center justify-center bg-slate-950/60 backdrop-blur-xs p-4 overflow-y-auto">
      <div class="bg-white rounded-3xl max-w-lg w-full p-6 space-y-4 shadow-xl border border-rose-200">
        <div class="flex items-center justify-between border-b border-rose-100 pb-3">
          <h3 class="text-sm font-extrabold text-rose-700 flex items-center gap-2">
            <i class="fas fa-box-tissue"></i> Reject Returned Goods Condition
          </h3>
          <button wire:click="closeModals" type="button" class="text-slate-400 hover:text-slate-600 text-lg cursor-pointer">×</button>
        </div>

        <div class="p-3.5 rounded-2xl bg-rose-50 border border-rose-200 text-xs text-rose-900 space-y-1">
          <strong class="font-extrabold block">Return Fraud or Abuse Escalation:</strong>
          <span>If the buyer returned a different item, empty parcel, tampered unit, or broken serial number seal, opening this dispute stops the refund or replacement and submits the return to mediation.</span>
        </div>

        <div class="space-y-3 text-xs">
          <div>
            <label class="font-bold text-slate-700 block mb-1">Rejection Reason &amp; Inspection Findings *</label>
            <textarea wire:model="returnRejectReason" rows="3" placeholder="Describe the discrepancy: damaged on return, serial number mismatch, missing parts..." class="w-full p-2.5 rounded-xl border border-slate-200 text-xs focus:border-rose-500 outline-none"></textarea>
            @error('returnRejectReason') <span class="text-rose-500 text-[11px]">{{ $message }}</span> @enderror
          </div>

          <div>
            <label class="font-bold text-slate-700 block mb-1">Unboxing / Inspection Evidence Link</label>
            <input type="text" wire:model="returnRejectEvidence" placeholder="Photo/Video unboxing URL proving return discrepancy..." class="w-full p-2.5 rounded-xl border border-slate-200 text-xs focus:border-rose-500 outline-none" />
          </div>
        </div>

        <div class="flex items-center justify-end gap-2 pt-2 border-t border-slate-100">
          <button wire:click="closeModals" type="button" class="px-4 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold cursor-pointer">Cancel</button>
          <button wire:click="sellerRejectReturn" type="button" class="px-5 py-2 rounded-xl bg-rose-600 hover:bg-rose-700 text-white text-xs font-extrabold shadow-xs cursor-pointer">Reject Return &amp; Dispute</button>
        </div>
      </div>
    </div>
  @endif

  <!-- ========================================================================= -->
  <!-- 8. BUYER REPLACEMENT REJECTION MODAL (DEFECTIVE REPLACEMENT) -->
  <!-- ========================================================================= -->
  @if ($showBuyerReplacementRejectModal)
    <div class="fixed inset-0 z-50 flex items-center justify-center bg-slate-950/60 backdrop-blur-xs p-4 overflow-y-auto">
      <div class="bg-white rounded-3xl max-w-lg w-full p-6 space-y-4 shadow-xl border border-rose-200">
        <div class="flex items-center justify-between border-b border-rose-100 pb-3">
          <h3 class="text-sm font-extrabold text-rose-700 flex items-center gap-2">
            <i class="fas fa-repeat"></i> Reject Replacement Unit
          </h3>
          <button wire:click="closeModals" type="button" class="text-slate-400 hover:text-slate-600 text-lg cursor-pointer">×</button>
        </div>

        <div class="p-3.5 rounded-2xl bg-amber-50 border border-amber-200 text-xs text-amber-900 space-y-1">
          <strong class="font-extrabold block">Replacement Issue Notice:</strong>
          <span>If the replacement unit sent by the seller is also defective or the wrong part, rejecting it will immediately escalate this invoice to platform dispute arbitration.</span>
        </div>

        <div class="space-y-3 text-xs">
          <div>
            <label class="font-bold text-slate-700 block mb-1">Reason for Rejecting Replacement *</label>
            <textarea wire:model="replacementRejectReason" rows="3" placeholder="Explain what is wrong with the replacement unit..." class="w-full p-2.5 rounded-xl border border-slate-200 text-xs focus:border-rose-500 outline-none"></textarea>
            @error('replacementRejectReason') <span class="text-rose-500 text-[11px]">{{ $message }}</span> @enderror
          </div>

          <div>
            <label class="font-bold text-slate-700 block mb-1">Evidence (Photo / Video URL)</label>
            <input type="text" wire:model="replacementRejectEvidence" placeholder="URL showing failure or damage on replacement..." class="w-full p-2.5 rounded-xl border border-slate-200 text-xs focus:border-rose-500 outline-none" />
          </div>
        </div>

        <div class="flex items-center justify-end gap-2 pt-2 border-t border-slate-100">
          <button wire:click="closeModals" type="button" class="px-4 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold cursor-pointer">Cancel</button>
          <button wire:click="buyerRejectReplacement" type="button" class="px-5 py-2 rounded-xl bg-rose-600 hover:bg-rose-700 text-white text-xs font-extrabold shadow-xs cursor-pointer">Reject Unit &amp; Escalate Dispute</button>
        </div>
      </div>
    </div>
  @endif

  <!-- ========================================================================= -->
  <!-- 9. BUYER WARRANTY CLAIM MODAL -->
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
            <label class="font-bold text-slate-700 block mb-1">Target Warranted Item</label>
            <select wire:model="warrantyItemId" class="w-full p-2.5 rounded-xl border border-slate-200 text-xs focus:border-pp-500 outline-none">
              <option value="">All Warranted Items in Order</option>
              @foreach ($invoice->items->where('warranty_period_days', '>', 0) as $wItem)
                <option value="{{ $wItem->id }}">{{ $wItem->description }} ({{ $wItem->warranty_period_days }} Days Coverage)</option>
              @endforeach
            </select>
          </div>

          <div>
            <label class="font-bold text-slate-700 block mb-1">Claim Type *</label>
            <select wire:model="warrantyClaimType" class="w-full p-2.5 rounded-xl border border-slate-200 text-xs focus:border-pp-500 outline-none">
              <option value="defect">Factory Defect / Hardware Malfunction</option>
              <option value="hardware_failure">Complete Hardware Failure / No Power</option>
              <option value="malfunction">Intermittent Glitch / Partial Failure</option>
              <option value="wear_tear">Premature Breakdown under Normal Use</option>
            </select>
            @error('warrantyClaimType') <span class="text-rose-500 text-[11px]">{{ $message }}</span> @enderror
          </div>

          <div>
            <label class="font-bold text-slate-700 block mb-1">Fault Description *</label>
            <textarea wire:model="warrantyReason" rows="3" placeholder="Describe what failed or stopped working under warranty terms..." class="w-full p-2.5 rounded-xl border border-slate-200 text-xs focus:border-pp-500 outline-none"></textarea>
            @error('warrantyReason') <span class="text-rose-500 text-[11px]">{{ $message }}</span> @enderror
          </div>

          <div>
            <label class="font-bold text-slate-700 block mb-1">Diagnostic Evidence / Photo or Video Link</label>
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
  <!-- 10. SELLER WARRANTY RESPONSE MODAL -->
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
              <option value="accept">Accept Claim (Authorize Replacement / Repair)</option>
              <option value="reject">Refuse Claim (Outside Warranty / Misuse) — Will Open Dispute</option>
            </select>
          </div>

          @if ($warrantyResolutionDecision === 'accept')
            <div>
              <label class="font-bold text-slate-700 block mb-1">Proposed Remedy *</label>
              <select wire:model="warrantyRemedy" class="w-full p-2.5 rounded-xl border border-slate-200 text-xs focus:border-pp-500 outline-none">
                <option value="replacement">Dispatch Replacement Unit</option>
                <option value="repair">Free Repair Service</option>
              </select>
            </div>
          @endif

          <div>
            <label class="font-bold text-slate-700 block mb-1">Notes / Diagnostic Explanation *</label>
            <textarea wire:model="warrantyResolutionNotes" rows="3" placeholder="{{ $warrantyResolutionDecision === 'accept' ? 'Provide dispatch instructions or repair schedule...' : 'Explain in detail why this claim is being denied...' }}" class="w-full p-2.5 rounded-xl border border-slate-200 text-xs focus:border-pp-500 outline-none"></textarea>
            @error('warrantyResolutionNotes') <span class="text-rose-500 text-[11px]">{{ $message }}</span> @enderror
          </div>

          @if ($warrantyResolutionDecision === 'reject')
            <div>
              <label class="font-bold text-slate-700 block mb-1">Diagnostic Evidence URL (Optional)</label>
              <input type="text" wire:model="warrantyRejectEvidence" placeholder="Photo or link showing user damage, tampered seal, or excluded condition..." class="w-full p-2.5 rounded-xl border border-slate-200 text-xs focus:border-pp-500 outline-none" />
            </div>
          @endif
        </div>

        <div class="flex items-center justify-end gap-2 pt-2 border-t border-slate-100">
          <button wire:click="closeModals" type="button" class="px-4 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold cursor-pointer">Cancel</button>
          <button wire:click="sellerRespondToWarrantyClaim" type="button" class="px-5 py-2 rounded-xl {{ $warrantyResolutionDecision === 'reject' ? 'bg-rose-600 hover:bg-rose-700' : 'bg-slate-900 hover:bg-slate-800' }} text-white text-xs font-extrabold shadow-xs cursor-pointer">
            {{ $warrantyResolutionDecision === 'reject' ? 'Deny Claim & Escalate to Dispute' : 'Submit Acceptance' }}
          </button>
        </div>
      </div>
    </div>
  @endif

  <!-- ========================================================================= -->
  <!-- 7. UNILATERAL CANCELLATION MODAL -->
  <!-- ========================================================================= -->
  @if ($showCancelModal)
    <div class="fixed inset-0 z-50 flex items-center justify-center bg-slate-950/60 backdrop-blur-xs p-4">
      <div class="bg-white rounded-3xl max-w-md w-full p-6 space-y-4 shadow-xl border border-slate-200">
        <div class="flex items-center justify-between border-b border-slate-100 pb-3">
          <h3 class="text-sm font-extrabold text-rose-700 flex items-center gap-2">
            <i class="fas fa-ban"></i> Cancel Order
          </h3>
          <button wire:click="closeModals" type="button" class="text-slate-400 hover:text-slate-600 text-lg cursor-pointer">×</button>
        </div>

        <p class="text-xs text-slate-600 leading-relaxed">
          Are you sure you want to cancel this order?
          @if ($invoice->status === 'paid')
            Since payment was already completed in escrow, an automatic refund will be queued for the buyer.
          @endif
        </p>

        <div>
          <label class="font-bold text-slate-700 block mb-1 text-xs">Reason for Cancellation *</label>
          <textarea wire:model="cancelReason" rows="3" placeholder="Please explain why you are cancelling this order..." class="w-full p-2.5 rounded-xl border border-slate-200 text-xs focus:border-rose-500 outline-none"></textarea>
          @error('cancelReason') <span class="text-rose-500 text-[11px]">{{ $message }}</span> @enderror
        </div>

        <div class="flex items-center justify-end gap-2 pt-2 border-t border-slate-100">
          <button wire:click="closeModals" type="button" class="px-4 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold cursor-pointer">Back</button>
          <button wire:click="confirmCancelOrder" type="button" class="px-5 py-2 rounded-xl bg-rose-600 hover:bg-rose-700 text-white text-xs font-extrabold shadow-xs cursor-pointer">Confirm Cancellation</button>
        </div>
      </div>
    </div>
  @endif

</div>
