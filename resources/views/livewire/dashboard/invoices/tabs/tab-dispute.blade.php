<div class="flex flex-col gap-6">

  <!-- ========================================================================= -->
  <!-- DISPUTE RESOLUTION HEADER -->
  <!-- ========================================================================= -->
  <div class="bg-white rounded-3xl border border-purple-200 p-6 sm:p-7 shadow-soft space-y-4">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 border-b border-purple-100 pb-4">
      <div class="flex items-center gap-3">
        <div class="w-11 h-11 rounded-2xl bg-purple-50 text-purple-600 flex items-center justify-center text-lg shrink-0 border border-purple-200">
          <i class="fas fa-scale-balanced"></i>
        </div>
        <div>
          <h2 class="text-sm font-black text-purple-950 uppercase tracking-wider flex items-center gap-2">
            <span>Dispute &amp; Mediation Resolution Center</span>
            <span class="px-2.5 py-0.5 rounded-full bg-purple-100 text-purple-900 text-[10px] font-black uppercase">
              Official Platform Case
            </span>
          </h2>
          <p class="text-xs text-slate-500 mt-0.5">
            When buyer and seller are unable to resolve issues directly, platform mediation arbitrates escrow funds.
          </p>
        </div>
      </div>
    </div>

    <!-- ESCROW LOCK NOTICE -->
    <div class="p-4 rounded-2xl bg-purple-50/70 border border-purple-200 flex items-center gap-3 text-xs text-purple-950">
      <i class="fas fa-gavel text-purple-600 text-base shrink-0"></i>
      <div>
        <strong class="font-black block">Dispute Escrow Freeze</strong>
        <span class="text-purple-900 text-[11px]">
          All payouts and settlement disbursements are frozen while dispute mediators review evidence, communication logs, and tracking waybills.
        </span>
      </div>
    </div>
  </div>

  <!-- ========================================================================= -->
  <!-- DISPUTE CASES LIST -->
  <!-- ========================================================================= -->
  <div class="space-y-4">
    @forelse ($disputes as $dispute)
      <div class="bg-white rounded-3xl border border-slate-200 p-6 shadow-soft space-y-5">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 border-b border-slate-100 pb-4">
          <div>
            <div class="flex items-center gap-2 flex-wrap">
              <span class="px-2.5 py-1 rounded-xl bg-purple-100 text-purple-900 font-black text-xs uppercase flex items-center gap-1.5">
                <i class="fas fa-scale-balanced text-purple-600"></i>
                <span>Case #DSP-{{ str_pad($dispute->id, 4, '0', STR_PAD_LEFT) }}</span>
              </span>
              <span class="px-2 py-0.5 rounded-lg bg-slate-100 text-slate-700 text-[10px] font-extrabold uppercase">
                Origin: {{ $dispute->originCategory() }}
              </span>
              <span class="px-2 py-0.5 rounded-lg bg-indigo-50 text-indigo-700 text-[10px] font-extrabold">
                {{ $dispute->typeLabel() }}
              </span>
              <span class="text-xs text-slate-400">·</span>
              <span class="text-xs text-slate-500 font-medium">Opened {{ $dispute->created_at->format('M d, Y · h:i A') }}</span>
            </div>
            <div class="flex items-center gap-2 text-xs text-slate-600 mt-1.5 flex-wrap">
              <span>Complainant: <strong class="text-slate-900">{{ $dispute->opener?->name ?: 'User' }}</strong></span>
              <span class="text-slate-300">vs</span>
              <span>Respondent: <strong class="text-slate-900">{{ $dispute->respondent?->name ?: 'Counterparty' }}</strong></span>
            </div>
          </div>

          <div class="flex items-center gap-2 self-start sm:self-auto flex-wrap">
            <span class="px-3 py-1 rounded-full text-xs font-black uppercase {{ $dispute->status === 'resolved' ? 'bg-emerald-100 text-emerald-800' : 'bg-purple-100 text-purple-900' }}">
              {{ Str::headline($dispute->status) }}
            </span>
            <a href="{{ route('disputes.view', $dispute->id) }}" class="px-4 py-2 rounded-xl bg-slate-900 hover:bg-slate-800 text-white font-extrabold text-xs transition flex items-center gap-1.5 shadow-2xs">
              <span>Open Dispute Center</span>
              <i class="fas fa-arrow-up-right-from-square text-[10px]"></i>
            </a>
          </div>
        </div>

        <div class="space-y-2 text-xs">
          <span class="font-black uppercase tracking-wider text-slate-400 text-[10px]">Dispute Reason / Claim Statement:</span>
          <p class="p-4 rounded-2xl bg-slate-50 border border-slate-200/80 text-slate-800 leading-relaxed font-medium">
            "{{ $dispute->reason ?: ($dispute->issue?->description ?? 'Disputed commercial terms.') }}"
          </p>
        </div>

        @if ($dispute->evidence)
          <div class="flex items-center gap-2 text-xs">
            <span class="font-bold text-slate-500 text-[11px]">Submitted Evidence:</span>
            <a href="{{ $dispute->evidence }}" target="_blank" rel="noopener noreferrer" class="text-pp-600 font-bold hover:underline flex items-center gap-1 text-[11px]">
              <i class="fas fa-paperclip text-[10px]"></i> View Evidence Link
            </a>
          </div>
        @endif

        @if ($dispute->resolution)
          <div class="p-4 rounded-2xl bg-emerald-50 border border-emerald-200 space-y-1 text-xs">
            <div class="flex items-center gap-2 text-emerald-900 font-black">
              <i class="fas fa-check-circle text-emerald-600"></i>
              <span>Mediator Final Resolution:</span>
            </div>
            <p class="text-emerald-950 leading-relaxed">{{ $dispute->resolution }}</p>
            @if ($dispute->resolved_at)
              <span class="text-[10px] text-emerald-700 block pt-1">Resolved on {{ $dispute->resolved_at->format('M d, Y') }}</span>
            @endif
          </div>
        @endif

        <div class="flex items-center justify-between pt-2 border-t border-slate-100 text-xs">
          <span class="text-slate-500 text-[11px]">Clicking the button opens dedicated dispute messaging and evidence submission.</span>
          <a href="{{ route('disputes.view', $dispute->id) }}" class="text-purple-700 hover:text-purple-900 font-extrabold hover:underline flex items-center gap-1">
            <span>View Full Case File</span>
            <i class="fas fa-chevron-right text-[9px]"></i>
          </a>
        </div>
      </div>
    @empty
      <div class="p-8 rounded-3xl bg-white border border-slate-200 text-center text-xs space-y-2 shadow-soft">
        <div class="w-12 h-12 rounded-2xl bg-slate-100 text-slate-400 grid place-items-center mx-auto text-lg mb-2">
          <i class="fas fa-scale-balanced"></i>
        </div>
        <h4 class="text-sm font-extrabold text-slate-900">No active disputes on this invoice</h4>
        <p class="text-slate-500 max-w-md mx-auto">
          Disputes are initiated when either party requests platform mediation to resolve an unsettled issue or return disagreement.
        </p>
      </div>
    @endforelse
  </div>

</div>
