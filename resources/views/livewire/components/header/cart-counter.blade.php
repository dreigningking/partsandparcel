@if($variant === 'mobile')
    <a href="{{ route('cart') }}" class="flex flex-col items-center text-[10px] font-semibold {{ request()->routeIs('cart*') ? 'text-pp-600 font-bold' : 'text-slate-600' }} relative group">
        <div class="relative inline-flex items-center justify-center">
            <span class="text-base leading-none">🛒</span>
            @if($cartCount > 0)
                <span class="absolute -top-1.5 -right-2.5 min-w-3.5 h-3.5 px-1 bg-pp-600 text-white rounded-full text-[9px] font-bold grid place-items-center leading-none animate-in zoom-in-50 duration-200">
                    {{ $cartCount > 99 ? '99+' : $cartCount }}
                </span>
            @endif
        </div>
        <span>Cart</span>
    </a>
@else
    <a href="{{ route('cart') }}" aria-label="Cart" class="hidden lg:flex relative p-2.5 rounded-xl hover:bg-slate-100 text-slate-700 transition" title="Shopping Cart">
        <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <path d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 1 0 0 4 2 2 0 0 0 0-4zm-8 0a2 2 0 1 0 0 4 2 2 0 0 0 0-4z"/>
        </svg>
        @if($cartCount > 0)
            <span class="absolute top-1 right-1 min-w-4 h-4 px-1 rounded-full bg-pp-600 text-white text-[10px] font-bold grid place-items-center animate-in zoom-in-50 duration-200">
                {{ $cartCount > 99 ? '99+' : $cartCount }}
            </span>
        @endif
    </a>
@endif
