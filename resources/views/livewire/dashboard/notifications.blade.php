<div class="flex flex-col gap-6">

  <!-- FLASH MESSAGE -->
  @if(session()->has('message'))
    <div class="p-4 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs font-bold flex items-center justify-between">
      <span>{{ session('message') }}</span>
      <button type="button" onclick="this.parentElement.remove()" class="text-emerald-600 hover:text-emerald-900">&times;</button>
    </div>
  @endif

  <!-- HEADER & TABS -->
  <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
    <div>
      <h1 class="text-2xl font-extrabold text-slate-950">Notifications &amp; Activity Alerts</h1>
      <p class="text-xs text-slate-500 mt-0.5">Stay updated on offer counter-proposals, payment releases, and community discussions.</p>
    </div>

    <div class="flex items-center gap-1 bg-white p-1 rounded-xl border border-slate-200 text-xs font-bold shadow-2xs">
      <button wire:click="setTab('all')" class="px-3 py-1.5 rounded-lg {{ $activeTab === 'all' ? 'bg-pp-600 text-white' : 'text-slate-600 hover:bg-slate-50' }} transition">
        All Alerts ({{ $counts['all'] }})
      </button>
      <button wire:click="setTab('transactions')" class="px-3 py-1.5 rounded-lg {{ $activeTab === 'transactions' ? 'bg-pp-600 text-white' : 'text-slate-600 hover:bg-slate-50' }} transition">
        Transactions ({{ $counts['transactions'] }})
      </button>
      <button wire:click="setTab('offers')" class="px-3 py-1.5 rounded-lg {{ $activeTab === 'offers' ? 'bg-pp-600 text-white' : 'text-slate-600 hover:bg-slate-50' }} transition">
        Offers &amp; Negotiations ({{ $counts['offers'] }})
      </button>
      <button wire:click="setTab('community')" class="px-3 py-1.5 rounded-lg {{ $activeTab === 'community' ? 'bg-pp-600 text-white' : 'text-slate-600 hover:bg-slate-50' }} transition">
        Community ({{ $counts['community'] }})
      </button>
    </div>
  </div>

  <!-- NOTIFICATIONS STREAM -->
  <div class="bg-white rounded-3xl border border-slate-200 p-6 space-y-4 shadow-soft">
    <div class="flex items-center justify-between border-b border-slate-100 pb-3">
      <h3 class="text-xs font-extrabold text-slate-900 uppercase tracking-wider flex items-center gap-2">
        <i class="fas fa-bell text-pp-600"></i> Recent Platform Activity
      </h3>
      @if($counts['all'] > 0)
        <button wire:click="markAllAsRead" class="text-xs font-bold text-pp-600 hover:underline cursor-pointer">
          Mark All as Read
        </button>
      @endif
    </div>

    <div class="divide-y divide-slate-100">
      @forelse($notifications as $notif)
        @php
          $isUnread = $notif->unread();
          $data = $notif->data;
          $icon = $data['icon'] ?? 'fas fa-bell';
          $iconColor = $data['icon_color'] ?? 'text-pp-600 bg-pp-100';
          $title = $data['title'] ?? 'Platform Notification';
          $message = $data['message'] ?? '';
          $actionUrl = $data['action_url'] ?? '#';
        @endphp
        <div class="py-4 flex items-start gap-4 {{ $isUnread ? 'bg-pp-50/40' : 'hover:bg-slate-50/80' }} p-3 rounded-2xl transition">
          <div class="w-10 h-10 rounded-2xl {{ $iconColor }} grid place-items-center text-base shrink-0">
            <i class="{{ $icon }}"></i>
          </div>
          <div class="flex-1 space-y-1">
            <div class="flex items-center justify-between text-xs">
              <div class="flex items-center gap-2">
                <span class="font-extrabold text-slate-900">{{ $title }}</span>
                @if($isUnread)
                  <span class="w-2 h-2 rounded-full bg-pp-600"></span>
                @endif
              </div>
              <div class="flex items-center gap-3">
                <span class="text-slate-400 text-[11px]">{{ $notif->created_at->diffForHumans() }}</span>
                @if($isUnread)
                  <button wire:click="markAsRead('{{ $notif->id }}')" class="text-[11px] text-slate-400 hover:text-slate-600" title="Mark as read">
                    <i class="fas fa-check"></i>
                  </button>
                @endif
              </div>
            </div>
            <p class="text-xs text-slate-600 leading-snug">
              {{ $message }}
            </p>
            @if($actionUrl !== '#')
              <a href="{{ $actionUrl }}" class="inline-block text-xs font-bold text-pp-600 hover:underline mt-1">
                View Details →
              </a>
            @endif
          </div>
        </div>
      @empty
        <div class="py-12 text-center text-slate-400 space-y-2">
          <i class="fas fa-bell-slash text-3xl text-slate-300"></i>
          <p class="text-xs font-semibold">No notifications found in this category.</p>
        </div>
      @endforelse
    </div>

    @if($notifications->hasPages())
      <div class="pt-4 border-t border-slate-100">
        {{ $notifications->links() }}
      </div>
    @endif
  </div>

</div>