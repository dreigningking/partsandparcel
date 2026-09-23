<div class="flex flex-col gap-6">

  <!-- HEADER & TABS -->
  <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
    <div>
      <h1 class="text-2xl font-extrabold text-slate-950">My Community Requests</h1>
      <p class="text-xs text-slate-500 mt-0.5">Track your open product &amp; service requests posted to the community hub.</p>
    </div>

    <div class="flex items-center gap-1 bg-white p-1 rounded-xl border border-slate-200 text-xs font-bold shadow-2xs flex-wrap">
      <button wire:click="$set('activeTab', 'open')" class="px-3 py-1.5 rounded-lg cursor-pointer transition {{ $activeTab === 'open' ? 'bg-pp-600 text-white' : 'text-slate-600 hover:bg-slate-50' }}">
        Open ({{ $counts['open'] }})
      </button>
      <button wire:click="$set('activeTab', 'fulfilled')" class="px-3 py-1.5 rounded-lg cursor-pointer transition {{ $activeTab === 'fulfilled' ? 'bg-emerald-600 text-white' : 'text-emerald-700 hover:bg-emerald-50' }}">
        Fulfilled ({{ $counts['fulfilled'] }})
      </button>
      <button wire:click="$set('activeTab', 'closed')" class="px-3 py-1.5 rounded-lg cursor-pointer transition {{ $activeTab === 'closed' ? 'bg-slate-900 text-white' : 'text-slate-600 hover:bg-slate-50' }}">
        Closed ({{ $counts['closed'] }})
      </button>
      <button wire:click="$set('activeTab', 'all')" class="px-3 py-1.5 rounded-lg cursor-pointer transition {{ $activeTab === 'all' ? 'bg-slate-900 text-white' : 'text-slate-600 hover:bg-slate-50' }}">
        All ({{ $counts['all'] }})
      </button>
    </div>
  </div>

  <!-- REQUEST CARDS STREAM -->
  <div class="space-y-4">
    @forelse ($userRequests as $req)
      @php
        $offersCount = $req->offers->count();
        $responsesCount = $req->responses->count();
        $budget = $req->attachments['budget'] ?? 'Flexible';
        $location = $req->attachments['location'] ?? 'Nigeria';
        $isOpen = ($req->status === 'open');
      @endphp

      <div class="bg-white rounded-3xl border-2 {{ $offersCount > 0 ? 'border-pp-500' : 'border-slate-200' }} p-6 space-y-4 shadow-soft">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 text-xs border-b border-slate-100 pb-3">
          <div class="flex items-center gap-2 flex-wrap">
            @if ($isOpen)
              <span class="px-2.5 py-0.5 rounded-full bg-emerald-50 text-emerald-700 font-bold text-[10px] border border-emerald-100">
                OPEN FOR PROPOSALS
              </span>
            @else
              <span class="px-2.5 py-0.5 rounded-full bg-slate-100 text-slate-700 font-bold text-[10px]">
                {{ strtoupper($req->status) }}
              </span>
            @endif
            <span class="text-slate-300">·</span>
            <span class="font-bold text-slate-900">Posted {{ $req->created_at->diffForHumans() }}</span>
            <span class="text-slate-300">·</span>
            <span class="text-slate-500">Ref: #REQ-{{ $req->id }}</span>
          </div>

          @if ($offersCount > 0)
            <span class="px-2.5 py-0.5 rounded-full bg-pp-100 text-pp-800 font-extrabold text-[11px]">
              {{ $offersCount }} PRIVATE {{ Str::plural('OFFER', $offersCount) }} RECEIVED
            </span>
          @endif
        </div>

        <div class="grid sm:grid-cols-12 gap-4 items-center">
          <div class="sm:col-span-6 space-y-1">
            <h3 class="text-base font-extrabold text-slate-900">{{ $req->title }}</h3>
            <p class="text-xs text-slate-500">
              Target Budget: <strong class="text-pp-700">{{ $budget }}</strong> · Location: {{ $location }}
            </p>
            <p class="text-xs text-slate-600 italic">"{{ Str::limit($req->body, 120) }}"</p>
          </div>

          <div class="sm:col-span-3 text-left sm:text-right space-y-0.5">
            <span class="text-xs text-slate-400 font-bold block uppercase">Activity Stats</span>
            <span class="text-lg font-black text-slate-950">{{ $responsesCount }} {{ Str::plural('Response', $responsesCount) }}</span>
            <span class="text-[10px] text-emerald-600 font-bold block">{{ $offersCount }} Vendor Offers Submitted</span>
          </div>

          <div class="sm:col-span-3 flex flex-col gap-2">
            <a href="{{ route('community.request', ['id' => $req->id]) }}" class="w-full py-2.5 px-4 rounded-xl bg-pp-600 hover:bg-pp-700 text-white font-extrabold text-xs text-center shadow-xs transition block">
              Manage Request &amp; View Offers →
            </a>
          </div>
        </div>
      </div>
    @empty
      <!-- EMPTY STATE -->
      <div class="bg-white rounded-3xl border border-slate-200 p-12 text-center space-y-4 shadow-soft">
        <div class="w-16 h-16 rounded-3xl bg-pp-50 text-pp-600 grid place-items-center text-2xl mx-auto">
          <i class="fas fa-bullhorn"></i>
        </div>
        <div class="space-y-1">
          <h3 class="font-extrabold text-slate-900 text-base">No Requests Found</h3>
          <p class="text-xs text-slate-500 max-w-md mx-auto">
            You haven't posted any community requests in this tab yet. Post what spare parts, machines, or repair services you are looking for!
          </p>
        </div>
        <div class="pt-2">
          <a href="{{ route('community') }}" class="py-2.5 px-5 rounded-xl bg-pp-600 text-white font-bold text-xs hover:bg-pp-700 transition inline-flex items-center gap-2">
            <i class="fas fa-plus"></i>
            <span>Post a Community Request</span>
          </a>
        </div>
      </div>
    @endforelse
  </div>

</div>