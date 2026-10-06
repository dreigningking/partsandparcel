<div class="space-y-6">

  <!-- ========================================================================= -->
  <!-- ISSUES AUDIT HEADER -->
  <!-- ========================================================================= -->
  <div class="bg-white dark:bg-slate-900 rounded-3xl border border-rose-200 dark:border-rose-900/60 p-6 sm:p-7 shadow-soft space-y-4">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 border-b border-rose-100 dark:border-rose-900/40 pb-4">
      <div class="flex items-center gap-3">
        <div class="w-11 h-11 rounded-2xl bg-rose-50 dark:bg-rose-950/60 text-rose-600 dark:text-rose-400 flex items-center justify-center text-lg shrink-0 border border-rose-200 dark:border-rose-800">
          <i class="fas fa-triangle-exclamation"></i>
        </div>
        <div>
          <h2 class="text-sm font-black text-rose-950 dark:text-rose-300 uppercase tracking-wider flex items-center gap-2">
            <span>Reported Issues &amp; Inspection Claims</span>
            <span class="px-2.5 py-0.5 rounded-full bg-rose-100 text-rose-900 text-[10px] font-black uppercase">
              Admin Inspection
            </span>
          </h2>
          <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">
            Audit buyer-reported friction, damaged parcels, component faults, photo evidence, and seller response proposals.
          </p>
        </div>
      </div>

      <span class="text-xs text-slate-400 font-bold self-start sm:self-auto">
        {{ $issues->count() }} {{ Str::plural('issue', $issues->count()) }} recorded
      </span>
    </div>

    <!-- ESCROW FREEZE AUDIT NOTICE -->
    <div class="p-4 rounded-2xl bg-rose-50/70 dark:bg-rose-950/30 border border-rose-200 dark:border-rose-800/60 flex items-center gap-3 text-xs text-rose-950 dark:text-rose-300">
      <i class="fas fa-shield-halved text-rose-600 text-base shrink-0"></i>
      <div>
        <strong class="font-black block">Escrow Payout Protection Locked</strong>
        <span class="text-rose-900 dark:text-rose-400 text-[11px]">
          Seller settlement payout remains frozen while reported issues are undergoing mutual resolution or dispute escalation.
        </span>
      </div>
    </div>
  </div>

  <!-- ========================================================================= -->
  <!-- ISSUES LIST -->
  <!-- ========================================================================= -->
  @if ($issues->isEmpty())
    <div class="p-8 rounded-3xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 text-center space-y-2 shadow-soft">
      <div class="w-12 h-12 rounded-2xl bg-slate-100 dark:bg-slate-800 text-slate-400 flex items-center justify-center text-xl mx-auto mb-2">
        <i class="fas fa-check-double"></i>
      </div>
      <h3 class="text-sm font-black text-slate-900 dark:text-white">No Issues Reported</h3>
      <p class="text-xs text-slate-500 max-w-md mx-auto">The buyer has not raised any claims or defects against this commercial invoice.</p>
    </div>
  @else
    @foreach ($issues as $issue)
      @php
        $adminIssueStatus = match(true) {
          $issue->dispute !== null => [
            'label' => 'Escalated to Dispute Mediation',
            'badge' => 'bg-purple-100 text-purple-800 border-purple-200 dark:bg-purple-950/60 dark:text-purple-300',
            'icon' => 'fa-scale-balanced text-purple-600',
          ],
          $issue->status === 'resolved' => [
            'label' => 'Issue Resolved Amicably',
            'badge' => 'bg-emerald-100 text-emerald-800 border-emerald-200 dark:bg-emerald-950/60 dark:text-emerald-300',
            'icon' => 'fa-circle-check text-emerald-600',
          ],
          $issue->status === 'rejected' => [
            'label' => 'Issue Rejected by Seller',
            'badge' => 'bg-rose-100 text-rose-800 border-rose-200 dark:bg-rose-950/60 dark:text-rose-300',
            'icon' => 'fa-times-circle text-rose-600',
          ],
          ! empty($issue->resolution_action) => [
            'label' => 'Seller Proposal Submitted (Waiting for Buyer)',
            'badge' => 'bg-blue-100 text-blue-800 border-blue-200 dark:bg-blue-950/60 dark:text-blue-300',
            'icon' => 'fa-paper-plane text-blue-600',
          ],
          default => [
            'label' => 'Waiting for Seller Response',
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
              <i class="fas {{ $adminIssueStatus['icon'] }}"></i>
            </div>
            <div>
              <div class="flex items-center gap-2 flex-wrap">
                <span class="px-2.5 py-0.5 rounded-xl bg-rose-100 dark:bg-rose-950/60 text-rose-900 dark:text-rose-300 font-black text-xs uppercase flex items-center gap-1.5">
                  <i class="fas fa-circle-exclamation text-rose-600"></i>
                  <span>{{ Str::headline($issue->type) }}</span>
                </span>
                <span class="text-xs font-mono font-bold text-slate-400">#ISS-{{ str_pad($issue->id, 4, '0', STR_PAD_LEFT) }}</span>
                <span class="px-2.5 py-0.5 rounded-full text-xs font-black uppercase tracking-wider border {{ $adminIssueStatus['badge'] }}">
                  {{ $adminIssueStatus['label'] }}
                </span>
              </div>
              <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">
                Reported by <strong>{{ $issue->reporter?->name ?? 'Buyer' }}</strong> on {{ $issue->created_at->format('M d, Y · h:i A') }}
              </p>
            </div>
          </div>
        </div>

        <!-- BUYER STATEMENT -->
        <div class="space-y-1 text-xs">
          <span class="font-black uppercase tracking-wider text-slate-400 text-[10px]">Buyer Statement / Complaint:</span>
          <p class="p-4 rounded-2xl bg-slate-50 dark:bg-slate-800/40 border border-slate-200/80 dark:border-slate-800 text-slate-800 dark:text-slate-200 leading-relaxed font-medium">
            "{{ $issue->description }}"
          </p>
        </div>

        <!-- AFFECTED LINE ITEMS & EVIDENCE -->
        @if ($issue->items->isNotEmpty())
          <div class="space-y-2 text-xs">
            <span class="font-black uppercase tracking-wider text-slate-400 text-[10px]">Affected Line Items &amp; Defect Reason:</span>
            <div class="space-y-2">
              @foreach ($issue->items as $issueItem)
                <div class="p-3.5 rounded-2xl bg-slate-50 dark:bg-slate-800/40 border border-slate-200/80 dark:border-slate-800 flex flex-col sm:flex-row sm:items-center justify-between gap-2">
                  <div>
                    <strong class="text-slate-900 dark:text-white block font-extrabold">{{ $issueItem->invoiceItem?->description ?: 'Line Item' }}</strong>
                    <span class="text-[11px] text-slate-500">Claim Reason: <strong class="text-slate-700 dark:text-slate-300">{{ Str::headline($issueItem->reason) }}</strong></span>
                  </div>
                  @if ($issueItem->evidence)
                    <a href="{{ $issueItem->evidence }}" target="_blank" rel="noopener noreferrer" class="px-3 py-1.5 rounded-xl bg-white dark:bg-slate-800 hover:bg-slate-100 border border-slate-200 dark:border-slate-700 text-pp-600 dark:text-pp-400 font-bold text-xs transition flex items-center gap-1.5 self-start sm:self-auto shrink-0 shadow-2xs">
                      <i class="fas fa-paperclip text-[10px]"></i>
                      <span>Inspect Photo Evidence</span>
                    </a>
                  @endif
                </div>
              @endforeach
            </div>
          </div>
        @endif

        <!-- SELLER RESPONSE / PROPOSAL -->
        @if ($issue->resolution_action)
          <div class="p-4 rounded-2xl bg-blue-50/70 dark:bg-blue-950/30 border border-blue-200 dark:border-blue-800/60 space-y-1.5 text-xs">
            <div class="flex items-center justify-between">
              <span class="font-black uppercase tracking-wider text-blue-900 dark:text-blue-300 text-[10px] flex items-center gap-1.5">
                <i class="fas fa-handshake text-blue-600"></i> Seller Proposal Submitted
              </span>
              <span class="px-2 py-0.5 rounded-full text-[10px] font-black uppercase bg-blue-100 text-blue-800">
                Remedy: {{ Str::headline($issue->resolution_action) }}
              </span>
            </div>
            @if ($issue->resolution_notes)
              <p class="text-blue-950 dark:text-blue-200 leading-relaxed font-medium">
                {{ $issue->resolution_notes }}
              </p>
            @endif
          </div>
        @endif

      </div>
    @endforeach
  @endif

</div>
