<aside id="sidebar" class="sidebar {{ $sidebarClass ?? '' }} fixed left-0 top-0 bottom-0 z-[80] w-[280px] bg-white border-r border-slate-200/80 flex flex-col shadow-soft">
        
    <!-- BRAND LOGO & RETRACT TOGGLE (HIDDEN ON MOBILE VIEW) -->
    <div class="h-[72px] px-5 hidden lg:flex items-center justify-between border-b border-slate-100 shrink-0">
        <a class="flex items-center gap-2.5" href="/">
            <div class="w-10 h-10 rounded-xl bg-pp-600 text-white grid place-items-center shadow-sm">
                <svg viewBox="0 0 32 32" class="w-6 h-6" fill="none">
                    <path d="M16 3 27 9.2v13.6L16 29 5 22.8V9.2L16 3Z" stroke="currentColor" stroke-width="2.2" stroke-linejoin="round"/>
                    <path d="M16 3v13m11-6.8-11 6.8L5 9.2M16 16v13" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                </svg>
            </div>
            <span class="brand-text text-xl font-extrabold text-slate-900 tracking-tight">Parts &amp; Parcel</span>
        </a>
        <button onclick="toggleSidebar()"
            class="logo-toggle hidden lg:grid w-8 h-8 place-items-center rounded-lg hover:bg-slate-100 text-slate-600 font-bold transition" title="Toggle Retract Sidebar">‹</button>
    </div>
    

    <!-- NAVIGATION ACCORDIONS -->
    @if(Route::is('admin.*'))
        @include('layouts.partials.nav-admin')
    @else
        @include('layouts.partials.nav-user')
    @endif


</aside>