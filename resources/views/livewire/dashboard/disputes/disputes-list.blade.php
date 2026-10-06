<div class="flex flex-col gap-6">

  <!-- HEADER & TABS -->
  <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
    <div>
      <h1 class="text-2xl font-extrabold text-slate-950 flex items-center gap-2">
        <span>Trust &amp; Resolution Center</span>
        <span class="px-2.5 py-0.5 rounded-full bg-purple-100 text-purple-900 text-xs font-black">
          Arbitration
        </span>
      </h1>
      <p class="text-xs text-slate-500 mt-0.5">Manage claims, dispute resolution, and Escrow protection mediation cases.</p>
    </div>

    <div class="flex items-center gap-1.5 bg-white p-1 rounded-2xl border border-slate-200 text-xs font-bold shadow-2xs self-start sm:self-auto">
      <button wire:click="setFilter('open')" type="button" class="px-3 py-1.5 rounded-xl transition cursor-pointer {{ $statusFilter === 'open' ? 'bg-purple-600 text-white font-extrabold shadow-xs' : 'text-slate-600 hover:bg-slate-50' }}">
        Open Cases ({{ $openCount }})
      </button>
      <button wire:click="setFilter('resolved')" type="button" class="px-3 py-1.5 rounded-xl transition cursor-pointer {{ $statusFilter === 'resolved' ? 'bg-emerald-600 text-white font-extrabold shadow-xs' : 'text-emerald-700 hover:bg-emerald-50' }}">
        Resolved ({{ $resolvedCount }})
      </button>
      <button wire:click="setFilter('all')" type="button" class="px-3 py-1.5 rounded-xl transition cursor-pointer {{ $statusFilter === 'all' ? 'bg-slate-900 text-white font-extrabold shadow-xs' : 'text-slate-600 hover:bg-slate-50' }}">
        All ({{ $totalCount }})
      </button>
    </div>
  </div>

  <!-- SEARCH & FILTER BAR -->
  <div class="bg-white rounded-2xl border border-slate-200 p-3 shadow-2xs flex items-center gap-3">
    <div class="relative flex-1">
      <i class="fas fa-search absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
      <input type="text" wire:model.live.debounce.300ms="search" placeholder="Search by Case Ref (#DSP), Invoice #, reason, or counterparty name..." class="w-full pl-9 pr-4 py-2 rounded-xl text-xs font-medium border border-slate-200 focus:border-purple-500 outline-none" />
    </div>
    @if ($search !== '')
      <button wire:click="$set('search', '')" type="button" class="px-3 py-2 text-xs font-bold text-slate-500 hover:text-slate-700 cursor-pointer">
        Clear
      </button>
    @endif
  </div>

  <!-- DISPUTES TABLE -->
  <div class="bg-white rounded-3xl border border-slate-200 p-6 space-y-4 shadow-soft">
    <div class="flex items-center justify-between border-b border-slate-100 pb-3">
      <h3 class="text-xs font-extrabold text-slate-900 uppercase tracking-wider flex items-center gap-2">
        <i class="fas fa-gavel text-purple-600"></i> Active Dispute Cases Stream
      </h3>
      <span class="text-xs text-slate-500 font-medium">{{ $disputes->total() }} total {{ Str::plural('case', $disputes->total()) }} found</span>
    </div>

    <div class="overflow-x-auto">
      <table class="w-full text-left text-xs">
        <thead class="bg-slate-50 text-slate-500 uppercase font-extrabold border-b border-slate-200">
          <tr>
            <th class="p-3.5">Dispute Case Ref</th>
            <th class="p-3.5">Related Invoice</th>
            <th class="p-3.5">Complainant / Respondent</th>
            <th class="p-3.5">Origin &amp; Classification</th>
            <th class="p-3.5">Escrow Status</th>
            <th class="p-3.5 text-right">Action</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-slate-100 font-medium text-slate-700">
          @forelse ($disputes as $dispute)
            <tr class="hover:bg-slate-50/80 transition">
              <td class="p-3.5 font-bold text-slate-900">
                <a href="{{ route('disputes.view', $dispute->id) }}" class="text-purple-600 hover:underline font-mono">
                  #DSP-{{ str_pad($dispute->id, 4, '0', STR_PAD_LEFT) }}
                </a>
                <span class="text-[11px] text-slate-400 block font-normal">{{ $dispute->created_at->format('M d, Y') }}</span>
              </td>

              <td class="p-3.5">
                @if ($dispute->invoice)
                  <a href="{{ route('invoices.view', $dispute->invoice->id) }}?tab=dispute" class="font-bold text-slate-900 hover:text-pp-600 hover:underline">
                    #INV-{{ $dispute->invoice->invoice_number }}
                  </a>
                  <span class="text-[11px] text-slate-500 block">
                    {{ $dispute->invoice->currency_symbol }}{{ number_format($dispute->invoice->total) }}
                  </span>
                @else
                  <span class="text-slate-400">N/A</span>
                @endif
              </td>

              <td class="p-3.5">
                <strong class="text-slate-900 block font-bold">
                  {{ $dispute->opener?->name ?: 'User' }}
                  @if ($dispute->opened_by === Auth::id())
                    <span class="text-[10px] text-purple-700 font-extrabold">(You)</span>
                  @endif
                </strong>
                <span class="text-[11px] text-slate-500">
                  vs {{ $dispute->respondent?->name ?: 'Counterparty' }}
                  @if ($dispute->respondent_id === Auth::id())
                    <span class="text-[10px] text-purple-700 font-extrabold">(You)</span>
                  @endif
                </span>
              </td>

              <td class="p-3.5 space-y-1">
                <div class="flex items-center gap-1.5 flex-wrap">
                  <span class="px-2 py-0.5 rounded bg-slate-100 text-slate-700 text-[10px] font-black uppercase">
                    {{ $dispute->originCategory() }}
                  </span>
                  <span class="px-2 py-0.5 rounded bg-purple-50 text-purple-800 text-[10px] font-bold">
                    {{ $dispute->typeLabel() }}
                  </span>
                </div>
                <p class="text-[11px] text-slate-500 truncate max-w-xs" title="{{ $dispute->reason }}">
                  "{{ $dispute->reason }}"
                </p>
              </td>

              <td class="p-3.5">
                @if ($dispute->status === 'resolved')
                  <span class="px-2.5 py-0.5 rounded-full bg-emerald-100 text-emerald-800 text-[10px] font-extrabold uppercase">
                    RESOLVED
                  </span>
                @else
                  <span class="px-2.5 py-0.5 rounded-full bg-amber-100 text-amber-900 text-[10px] font-extrabold uppercase flex items-center gap-1 w-max">
                    <i class="fas fa-lock text-[9px]"></i> ESCROW FROZEN
                  </span>
                @endif
              </td>

              <td class="p-3.5 text-right">
                <a href="{{ route('disputes.view', $dispute->id) }}" class="px-3.5 py-1.5 rounded-xl bg-slate-900 hover:bg-slate-800 text-white font-extrabold text-[11px] transition shadow-2xs inline-flex items-center gap-1">
                  <span>View Case</span>
                  <i class="fas fa-chevron-right text-[9px]"></i>
                </a>
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="6" class="p-8 text-center text-slate-500">
                <div class="w-12 h-12 rounded-2xl bg-purple-50 text-purple-600 grid place-items-center mx-auto text-lg mb-2">
                  <i class="fas fa-scale-balanced"></i>
                </div>
                <strong class="text-sm font-extrabold text-slate-900 block">No dispute cases found</strong>
                <p class="text-xs text-slate-500 max-w-sm mx-auto mt-0.5">
                  @if ($search !== '')
                    No disputes matched your search query "{{ $search }}".
                  @else
                    You currently have no active or historical dispute cases.
                  @endif
                </p>
              </td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>

    @if ($disputes->hasPages())
      <div class="pt-3 border-t border-slate-100">
        {{ $disputes->links() }}
      </div>
    @endif
  </div>

</div>