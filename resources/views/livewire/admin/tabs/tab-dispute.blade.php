<div class="space-y-6">

  <!-- ========================================================================= -->
  <!-- DISPUTE RESOLUTION AUDIT HEADER -->
  <!-- ========================================================================= -->
  <div class="bg-white dark:bg-slate-900 rounded-3xl border border-purple-200 dark:border-purple-900/60 p-6 sm:p-7 shadow-soft space-y-4">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 border-b border-purple-100 dark:border-purple-900/40 pb-4">
      <div class="flex items-center gap-3">
        <div class="w-11 h-11 rounded-2xl bg-purple-50 dark:bg-purple-950/60 text-purple-600 dark:text-purple-400 flex items-center justify-center text-lg shrink-0 border border-purple-200 dark:border-purple-800">
          <i class="fas fa-scale-balanced"></i>
        </div>
        <div>
          <h2 class="text-sm font-black text-purple-950 dark:text-purple-300 uppercase tracking-wider flex items-center gap-2">
            <span>Formal Dispute Mediation Cases</span>
            <span class="px-2.5 py-0.5 rounded-full bg-purple-100 text-purple-900 text-[10px] font-black uppercase">
              Admin Arbitration
            </span>
          </h2>
          <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">
            Escalated commercial disputes arbitrated by platform staff when buyer and seller reach an impasse.
          </p>
        </div>
      </div>

      <span class="text-xs text-slate-400 font-bold self-start sm:self-auto">
        {{ $disputes->count() }} {{ Str::plural('dispute', $disputes->count()) }} linked
      </span>
    </div>

    <!-- ESCROW LOCK NOTICE -->
    <div class="p-4 rounded-2xl bg-purple-50/70 dark:bg-purple-950/30 border border-purple-200 dark:border-purple-800/60 flex items-center gap-3 text-xs text-purple-950 dark:text-purple-300">
      <i class="fas fa-gavel text-purple-600 text-base shrink-0"></i>
      <div>
        <strong class="font-black block">Dispute Escrow Freeze: All Payouts Frozen</strong>
        <span class="text-purple-900 dark:text-purple-400 text-[11px]">
          Funds remain strictly frozen in platform escrow until mediator arbitrates claims and delivers binding decision.
        </span>
      </div>
    </div>
  </div>

  <!-- ========================================================================= -->
  <!-- DISPUTE CASES LIST -->
  <!-- ========================================================================= -->
  @if ($disputes->isEmpty())
    <div class="p-8 rounded-3xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 text-center space-y-2 shadow-soft">
      <div class="w-12 h-12 rounded-2xl bg-slate-100 dark:bg-slate-800 text-slate-400 flex items-center justify-center text-xl mx-auto mb-2">
        <i class="fas fa-handshake"></i>
      </div>
      <h3 class="text-sm font-black text-slate-900 dark:text-white">No Disputes Filed</h3>
      <p class="text-xs text-slate-500 max-w-md mx-auto">There are no escalated dispute arbitration cases for this invoice.</p>
    </div>
  @else
    @foreach ($disputes as $dispute)
      @php
        $adminDisputeStatus = match($dispute->status) {
          'resolved' => [
            'label' => 'Mediator Resolved (Dispute Settled)',
            'badge' => 'bg-emerald-100 text-emerald-800 border-emerald-200 dark:bg-emerald-950/60 dark:text-emerald-300',
            'icon' => 'fa-circle-check text-emerald-600',
          ],
          'pending_evidence', 'evidence_required' => [
            'label' => 'Awaiting Evidence from Parties',
            'badge' => 'bg-amber-100 text-amber-800 border-amber-200 dark:bg-amber-950/60 dark:text-amber-300',
            'icon' => 'fa-file-circle-question text-amber-600',
          ],
          'dismissed', 'closed' => [
            'label' => 'Dispute Dismissed / Closed',
            'badge' => 'bg-slate-100 text-slate-700 border-slate-200 dark:bg-slate-800 dark:text-slate-300',
            'icon' => 'fa-folder-closed text-slate-500',
          ],
          default => [
            'label' => 'Dispute in progress (Under Mediation Review)',
            'badge' => 'bg-purple-100 text-purple-800 border-purple-200 dark:bg-purple-950/60 dark:text-purple-300',
            'icon' => 'fa-gavel text-purple-600',
          ],
        };
      @endphp

      <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200 dark:border-slate-800 p-6 shadow-soft space-y-4">
        
        <!-- HEADER WITH ADMIN STATUS -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 border-b border-slate-100 dark:border-slate-800 pb-3">
          <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-2xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 flex items-center justify-center text-base shrink-0">
              <i class="fas {{ $adminDisputeStatus['icon'] }}"></i>
            </div>
            <div>
              <div class="flex items-center gap-2 flex-wrap">
                <span class="px-2.5 py-0.5 rounded-xl bg-purple-100 dark:bg-purple-950/60 text-purple-900 dark:text-purple-300 font-black text-xs uppercase flex items-center gap-1.5">
                  <i class="fas fa-scale-balanced text-purple-600"></i>
                  <span>Case #DSP-{{ str_pad($dispute->id, 4, '0', STR_PAD_LEFT) }}</span>
                </span>
                <span class="px-2.5 py-0.5 rounded-full text-xs font-black uppercase tracking-wider border {{ $adminDisputeStatus['badge'] }}">
                  {{ $adminDisputeStatus['label'] }}
                </span>
              </div>
              <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">
                Opened by <strong>{{ $dispute->opener?->name ?: 'User' }}</strong> on {{ $dispute->created_at->format('M d, Y · h:i A') }}
              </p>
            </div>
          </div>

          <a
            href="{{ route('admin.disputes') }}"
            class="px-3.5 py-1.5 rounded-xl bg-slate-900 dark:bg-white text-white dark:text-slate-900 hover:bg-slate-800 font-extrabold text-xs shadow-2xs transition flex items-center gap-1.5 self-start sm:self-auto cursor-pointer"
          >
            <span>Mediation Desk</span>
            <i class="fas fa-arrow-up-right-from-square text-[10px]"></i>
          </a>
        </div>

        <!-- DISPUTE REASON -->
        <div class="space-y-1 text-xs">
          <span class="font-black uppercase tracking-wider text-slate-400 text-[10px]">Arbitration Reason / Grounds:</span>
          <p class="p-4 rounded-2xl bg-slate-50 dark:bg-slate-800/40 border border-slate-200/80 dark:border-slate-800 text-slate-800 dark:text-slate-200 leading-relaxed font-medium">
            "{{ $dispute->reason ?: ($dispute->issue?->description ?? 'Dispute filed over commercial fulfillment terms.') }}"
          </p>
        </div>

        <!-- RESOLUTION (IF RESOLVED) -->
        @if ($dispute->resolution)
          <div class="p-4 rounded-2xl bg-emerald-50/70 dark:bg-emerald-950/30 border border-emerald-200 dark:border-emerald-800/60 space-y-1 text-xs">
            <div class="flex items-center gap-2 text-emerald-900 dark:text-emerald-300 font-black">
              <i class="fas fa-check-circle text-emerald-600"></i>
              <span>Mediator Final Ruling:</span>
            </div>
            <p class="text-emerald-950 dark:text-emerald-200 leading-relaxed font-medium">
              {{ $dispute->resolution }}
            </p>
            @if ($dispute->resolved_at)
              <span class="text-[10px] text-emerald-700 dark:text-emerald-400 block pt-1">
                Resolved on {{ $dispute->resolved_at->format('M d, Y · h:i A') }} by {{ $dispute->resolver?->name ?? 'Staff Mediator' }}
              </span>
            @endif
          </div>
        @endif

      </div>
    @endforeach
  @endif

</div>
