<div class="relative flex-1 min-w-0 max-w-[680px] mx-2 lg:mx-3 xl:mx-4" x-data="{ open: true }" @click.outside="open = false">
  <form wire:submit.prevent="submitSearch" class="hidden md:flex items-center border border-slate-200 rounded-xl overflow-hidden h-11 w-full shadow-sm focus-within:border-pp-500 focus-within:ring-2 focus-within:ring-pp-500/20 transition bg-white">
    <div class="h-full px-3.5 bg-slate-50 border-r border-slate-200 text-xs font-semibold text-slate-600 flex items-center gap-1.5 shrink-0">
      <svg class="w-3.5 h-3.5 text-slate-400" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M9.69 18.933l.003.001C9.89 19.02 10 19 10 19s.11.02.307-.066l.003-.001.006-.003.018-.008a5.741 5.741 0 00.281-.14c.186-.096.446-.24.757-.433.62-.384 1.445-.966 2.274-1.765C15.302 14.988 17 12.493 17 9A7 7 0 103 9c0 3.492 1.698 5.988 3.355 7.584a13.731 13.731 0 003.03 2.198l.019.009.006.003zM10 11.25a2.25 2.25 0 100-4.5 2.25 2.25 0 000 4.5z" clip-rule="evenodd"/></svg>
      Nigeria
    </div>
    
    <input 
      type="text" 
      wire:model.live.debounce.200ms="query"
      @focus="open = true"
      class="flex-1 min-w-0 h-full px-3 lg:px-4 outline-none text-sm text-slate-800 placeholder:text-slate-400" 
      placeholder="Search device, model (e.g. iPhone 12, Corolla, CAT Excavator) or part..." 
      autocomplete="off"
    />

    <button type="submit" class="h-full px-5 bg-pp-600 hover:bg-pp-700 text-white font-semibold text-sm flex items-center gap-2 transition cursor-pointer">
      <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="11" cy="11" r="7"/><path d="m20 20-4-4"/></svg>
      <span>Search</span>
    </button>
  </form>

  <!-- LIVE SUGGESTIONS DROPDOWN MENU -->
  @if(strlen(trim($query)) >= 2)
    <div x-show="open" class="absolute left-0 right-0 top-full mt-1 bg-white rounded-2xl border border-slate-200 shadow-2xl overflow-hidden z-50 animate-in fade-in duration-150">
      @if(count($suggestions) > 0)
        <div class="px-4 py-2 bg-slate-50 border-b border-slate-100 flex items-center justify-between text-[11px] font-extrabold uppercase tracking-wider text-slate-500">
          <span>Suggestions</span>
          <span class="text-pp-600 font-bold">Click to filter category</span>
        </div>
        <div class="max-h-[360px] overflow-y-auto divide-y divide-slate-100">
          @foreach($suggestions as $item)
            <a href="{{ $item['url'] }}" class="flex items-center justify-between px-4 py-3 hover:bg-pp-50/70 transition group">
              <div class="flex items-center gap-3">
                <span class="w-7 h-7 rounded-lg bg-slate-100 group-hover:bg-pp-100 text-slate-600 group-hover:text-pp-600 grid place-items-center text-xs shrink-0 font-bold">
                  @if($item['type'] === 'model') 📱 
                  @elseif($item['type'] === 'category') 📁 
                  @elseif($item['type'] === 'brand') 🏷️ 
                  @else 💻 
                  @endif
                </span>
                <div>
                  <div class="text-xs font-bold text-slate-900 group-hover:text-pp-700">
                    {{ $item['subtitle'] }}
                  </div>
                  <div class="text-[10px] text-slate-400 capitalize font-medium">
                    {{ $item['type'] }} suggestion
                  </div>
                </div>
              </div>
              <svg class="w-4 h-4 text-slate-300 group-hover:text-pp-600 transition-transform group-hover:translate-x-0.5" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M7.21 14.77a.75.75 0 01.02-1.06L11.168 10 7.23 6.03a.75.75 0 111.06-1.06l4.5 4.5a.75.75 0 010 1.06l-4.5 4.5a.75.75 0 01-1.08.02z" clip-rule="evenodd"/>
              </svg>
            </a>
          @endforeach
        </div>
      @else
        <div class="p-4 text-center">
          <p class="text-xs font-semibold text-slate-600">No suggestions found for "<span class="font-bold text-slate-900">{{ $query }}</span>"</p>
          <p class="text-[11px] text-slate-400 mt-0.5">Click Search button to search device items and community discussions.</p>
        </div>
      @endif

      <!-- FULL SEARCH FOOTER BUTTON -->
      <button type="button" wire:click="submitSearch" class="w-full p-3 bg-slate-50 hover:bg-slate-100 text-center text-xs font-bold text-pp-600 border-t border-slate-100 flex items-center justify-center gap-1.5 transition cursor-pointer">
        <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="11" cy="11" r="7"/><path d="m20 20-4-4"/></svg>
        <span>Search all items &amp; community discussions for "{{ $query }}" →</span>
      </button>
    </div>
  @endif
</div>
