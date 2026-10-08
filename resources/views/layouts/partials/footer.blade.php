<!-- FOOTER TRUST & LINKS -->
<footer class="bg-white border-t border-slate-200 mt-16 text-slate-700">

  <!-- TOP TRUST & ESCROW ASSURANCE BAR -->
  <div class="border-b border-slate-100 bg-slate-50/70">
    <div class="max-w-[1440px] mx-auto px-4 sm:px-6 lg:px-8 py-5">
      <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 text-xs">
        <div class="flex items-center gap-3">
          <div class="w-9 h-9 rounded-xl bg-pp-50 text-pp-600 flex items-center justify-center text-sm font-extrabold shrink-0 shadow-2xs">
            <i class="fas fa-shield-halved"></i>
          </div>
          <div>
            <h5 class="font-extrabold text-slate-900">Parts &amp; Parcel Escrow</h5>
            <p class="text-[11px] text-slate-500">Payments held securely until delivery inspection</p>
          </div>
        </div>

        <div class="flex items-center gap-3">
          <div class="w-9 h-9 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-sm font-extrabold shrink-0 shadow-2xs">
            <i class="fas fa-id-card-clip"></i>
          </div>
          <div>
            <h5 class="font-extrabold text-slate-900">Verified Technicians &amp; Dealers</h5>
            <p class="text-[11px] text-slate-500">Identity and physical store locations checked</p>
          </div>
        </div>

        <div class="flex items-center gap-3">
          <div class="w-9 h-9 rounded-xl bg-amber-50 text-amber-700 flex items-center justify-center text-sm font-extrabold shrink-0 shadow-2xs">
            <i class="fas fa-screwdriver-wrench"></i>
          </div>
          <div>
            <h5 class="font-extrabold text-slate-900">Component Salvage Hub</h5>
            <p class="text-[11px] text-slate-500">Harvest hard-to-find parts from scrap units</p>
          </div>
        </div>

        <div class="flex items-center gap-3">
          <div class="w-9 h-9 rounded-xl bg-sky-50 text-sky-600 flex items-center justify-center text-sm font-extrabold shrink-0 shadow-2xs">
            <i class="fas fa-scale-balanced"></i>
          </div>
          <div>
            <h5 class="font-extrabold text-slate-900">Fair Dispute Mediation</h5>
            <p class="text-[11px] text-slate-500">Prompt evidence review and buyer protection</p>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- MAIN SITEMAP NAVIGATION (5 COLUMNS) -->
  <div class="max-w-[1440px] mx-auto px-4 sm:px-6 lg:px-8 pt-12 pb-8">
    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-12 gap-8 lg:gap-8">
      
      <!-- COLUMN 1: BRAND IDENTITY & TRUST (4 COLS ON DESKTOP) -->
      <div class="col-span-2 md:col-span-3 lg:col-span-4 space-y-4">
        <a href="{{ route('welcome') }}" class="flex items-center gap-2.5 shrink-0">
          <div class="w-9 h-9 rounded-xl bg-pp-600 text-white grid place-items-center shadow-xs">
            <svg viewBox="0 0 32 32" class="w-5 h-5" fill="none">
              <path d="M16 3 27 9.2v13.6L16 29 5 22.8V9.2L16 3Z" stroke="currentColor" stroke-width="2.2" stroke-linejoin="round"/>
              <path d="M16 3v13m11-6.8-11 6.8L5 9.2M16 16v13" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
            </svg>
          </div>
          <span class="text-xl font-extrabold tracking-tight text-slate-950">Parts &amp; Parcel</span>
        </a>

        <p class="text-xs text-slate-500 leading-relaxed max-w-sm">
          Nigeria's trusted circular marketplace for complete devices, machinery, genuine spare parts, and damaged units for component harvesting and salvage.
        </p>

        <!-- PAYMENT METHOD TRUST LOGOS -->
        <div class="pt-1">
          <span class="text-[10px] font-black uppercase tracking-wider text-slate-400 block mb-2">Secure Escrow Gateways</span>
          <div class="flex items-center gap-2 flex-wrap text-slate-600">
            <span class="px-2.5 py-1 rounded-lg bg-slate-100 font-extrabold text-[10px] text-slate-700">Paystack</span>
            <span class="px-2.5 py-1 rounded-lg bg-slate-100 font-extrabold text-[10px] text-slate-700">Flutterwave</span>
            <span class="px-2.5 py-1 rounded-lg bg-slate-100 font-extrabold text-[10px] text-slate-700">Verve</span>
            <span class="px-2.5 py-1 rounded-lg bg-slate-100 font-extrabold text-[10px] text-slate-700">Mastercard</span>
            <span class="px-2.5 py-1 rounded-lg bg-slate-100 font-extrabold text-[10px] text-slate-700">Transfers</span>
          </div>
        </div>

        
      </div>

      <!-- COLUMN 2: MARKETPLACE (2 COLS ON DESKTOP) -->
      <div class="col-span-1 lg:col-span-2 space-y-3">
        <h4 class="text-xs font-black text-slate-900 uppercase tracking-wider">Marketplace</h4>
        <ul class="space-y-2 text-xs text-slate-600">
          <li>
            <a href="{{ route('category') }}?tab=complete" class="hover:text-pp-600 transition flex items-center gap-1.5">
              <span>Complete Devices</span>
            </a>
          </li>
          <li>
            <a href="{{ route('category') }}?tab=parts" class="hover:text-pp-600 transition flex items-center gap-1.5">
              <span>Spare Parts</span>
            </a>
          </li>
          <li>
            <a href="{{ route('category') }}?tab=scrap" class="text-amber-700 font-bold transition flex items-center gap-1.5">
              {{-- <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span> --}}
              <span>Scrap &amp; Salvage</span>
            </a>
          </li>
          <li>
            <a href="{{ route('community') }}" class="hover:text-pp-600 transition">
              <span>Community</span>
            </a>
          </li>
          <li>
            <a href="{{ route('cart') }}" class="hover:text-pp-600 transition">
              <span>Shopping Cart</span>
            </a>
          </li>
        </ul>
      </div>

      <!-- COLUMN 3: DASHBOARD (2 COLS ON DESKTOP) -->
      <div class="col-span-1 lg:col-span-2 space-y-3">
        <h4 class="text-xs font-black text-slate-900 uppercase tracking-wider">Dashboard</h4>
        <ul class="space-y-2 text-xs text-slate-600">
          <li>
            <a href="{{ route('offers') }}" class="hover:text-pp-600 transition">
              <span>Offers</span>
            </a>
          </li>
          <li>
            <a href="{{ route('myitems') }}" class="hover:text-pp-600 transition">
              <span>My Items</span>
            </a>
          </li>
          <li>
            <a href="{{ route('mylistings') }}" class="hover:text-pp-600 transition">
              <span>My Listings</span>
            </a>
          </li>
          <li>
            <a href="{{ route('locations') }}" class="hover:text-pp-600 transition">
              <span>My Locations</span>
            </a>
          </li>
          
          <li>
            <a href="{{ route('invoices') }}" class="hover:text-pp-600 transition">
              <span>Invoices &amp; Escrow</span>
            </a>
          </li>
        </ul>
      </div>

      <!-- COLUMN 4: COMPANY & ACCOUNT (2 COLS ON DESKTOP) -->
      <div class="col-span-1 lg:col-span-2 space-y-3">
        <h4 class="text-xs font-black text-slate-900 uppercase tracking-wider">Company &amp; Account</h4>
        <ul class="space-y-2 text-xs text-slate-600">
          <li>
            <a href="{{ route('blog.index') }}" class="hover:text-pp-600 transition">
              <span>Blog</span>
            </a>
          </li>
          <li>
            <a href="{{ auth()->check() ? route('subscription-plans') : route('pricing') }}" class="hover:text-pp-600 transition">
              <span>Subscription Plans &amp; Pricing</span>
            </a>
          </li>
          <li>
            <a href="{{ route('help') }}" class="hover:text-pp-600 transition">
              <span>Help</span>
            </a>
          </li>
          <li>
            <a href="{{ route('contact') }}" class="hover:text-pp-600 transition">
              <span>Contact</span>
            </a>
          </li>
          
          <!-- LOGIN / REGISTER OR ACCOUNT PROFILE -->
          @guest
            <li class="pt-2 border-t border-slate-100">
              <div class="flex items-center gap-1.5 text-xs">
                <a href="{{ route('login') }}" class="font-bold text-pp-600 hover:text-pp-700 hover:underline">Log in</a>
                <span class="text-slate-300">/</span>
                <a href="{{ route('register') }}" class="font-bold text-slate-800 hover:text-pp-600 hover:underline">Register</a>
              </div>
            </li>
          @else
            <li class="pt-2 border-t border-slate-100">
              <a href="{{ route('profile') }}" class="font-bold text-slate-800 hover:text-pp-600 transition flex items-center gap-1.5">
                <i class="fas fa-user-circle text-pp-600"></i>
                <span>My Profile</span>
              </a>
            </li>
            @if(auth()->user()->isAdmin())
              <li>
                <a href="{{ route('admin.dashboard') }}" class="text-pp-700 font-bold hover:underline flex items-center gap-1 text-[11px]">
                  <i class="fas fa-shield text-[9px]"></i>
                  <span>Admin Console</span>
                </a>
              </li>
            @endif
          @endguest
        </ul>
      </div>

      <!-- COLUMN 5: SOCIAL MEDIA (2 COLS ON DESKTOP) -->
      <div class="col-span-1 lg:col-span-2 space-y-3">
        <h4 class="text-xs font-black text-slate-900 uppercase tracking-wider">Follow Us</h4>
        <ul class="space-y-2 text-xs text-slate-600">
          <li>
            <a href="https://facebook.com" target="_blank" rel="noopener noreferrer" class="hover:text-[#1877F2] transition flex items-center gap-2 group">
              <span class="w-6 h-6 rounded-lg bg-slate-100 text-slate-600 flex items-center justify-center text-[11px] group-hover:bg-[#1877F2] group-hover:text-white transition shadow-2xs">
                <i class="fa-brands fa-facebook-f"></i>
              </span>
              <span>Facebook</span>
            </a>
          </li>
          <li>
            <a href="https://x.com" target="_blank" rel="noopener noreferrer" class="hover:text-slate-950 transition flex items-center gap-2 group">
              <span class="w-6 h-6 rounded-lg bg-slate-100 text-slate-600 flex items-center justify-center text-[11px] group-hover:bg-slate-950 group-hover:text-white transition shadow-2xs">
                <i class="fa-brands fa-x-twitter"></i>
              </span>
              <span>X (Twitter)</span>
            </a>
          </li>
          <li>
            <a href="https://instagram.com" target="_blank" rel="noopener noreferrer" class="hover:text-[#E4405F] transition flex items-center gap-2 group">
              <span class="w-6 h-6 rounded-lg bg-slate-100 text-slate-600 flex items-center justify-center text-[11px] group-hover:bg-[#E4405F] group-hover:text-white transition shadow-2xs">
                <i class="fa-brands fa-instagram"></i>
              </span>
              <span>Instagram</span>
            </a>
          </li>
          <li>
            <a href="https://linkedin.com" target="_blank" rel="noopener noreferrer" class="hover:text-[#0A66C2] transition flex items-center gap-2 group">
              <span class="w-6 h-6 rounded-lg bg-slate-100 text-slate-600 flex items-center justify-center text-[11px] group-hover:bg-[#0A66C2] group-hover:text-white transition shadow-2xs">
                <i class="fa-brands fa-linkedin-in"></i>
              </span>
              <span>LinkedIn</span>
            </a>
          </li>
          <li>
            <a href="https://youtube.com" target="_blank" rel="noopener noreferrer" class="hover:text-[#FF0000] transition flex items-center gap-2 group">
              <span class="w-6 h-6 rounded-lg bg-slate-100 text-slate-600 flex items-center justify-center text-[11px] group-hover:bg-[#FF0000] group-hover:text-white transition shadow-2xs">
                <i class="fa-brands fa-youtube"></i>
              </span>
              <span>YouTube</span>
            </a>
          </li>
          
        </ul>
      </div>

    </div>

    <!-- BOTTOM COPYRIGHT & LEGAL ROW -->
    <div class="mt-12 pt-6 border-t border-slate-100 flex flex-col sm:flex-row items-center justify-between text-xs text-slate-400 gap-4">
      <p>© {{ date('Y') }} Parts &amp; Parcel Ltd. Built for Nigeria &amp; Africa.</p>
      <div class="flex items-center gap-4 flex-wrap">
        <a href="{{ route('help') }}" class="hover:text-slate-600 transition">Buyer Protection Escrow</a>
        <span>·</span>
        <a href="{{ route('help') }}" class="hover:text-slate-600 transition">Terms of Service</a>
        <span>·</span>
        <a href="{{ route('help') }}" class="hover:text-slate-600 transition">Dispute Policies</a>
        <span>·</span>
        <a href="{{ route('help') }}" class="hover:text-slate-600 transition">Privacy Policy</a>
      </div>
    </div>
  </div>
</footer>