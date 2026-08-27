<div class="space-y-8">
  
  <div class="bg-white rounded-3xl border border-slate-200 shadow-soft overflow-hidden flex flex-col lg:flex-row h-[calc(100vh-140px)] min-h-[680px]">
    
    <!-- LEFT PANEL: MESSAGES LIST -->
    <div class="w-full lg:w-[380px] border-r border-slate-200 flex flex-col shrink-0 bg-white {{ $activeConversationId ? 'hidden lg:flex' : 'flex' }}">
      
      <!-- HEADER -->
      <div class="p-4 border-b border-slate-100 space-y-3 shrink-0 bg-white">
        <div class="flex items-center justify-between">
          <div>
            <h1 class="text-xl font-extrabold text-slate-950">Messages</h1>
            <p class="text-[11px] text-slate-400">4 unread conversations</p>
          </div>
          <button class="w-8 h-8 rounded-lg hover:bg-slate-100 grid place-items-center text-slate-500 text-sm font-bold cursor-pointer">
            <i class="fas fa-sliders-h"></i>
          </button>
        </div>

        <!-- SEARCH INPUT -->
        <div class="relative">
          <i class="fas fa-search absolute left-3.5 top-3 text-slate-400 text-xs"></i>
          <input type="text" wire:model.live="searchQuery" placeholder="Search messages, sellers, or parts..." class="w-full pl-9 pr-3 py-2 rounded-xl bg-slate-50 border border-slate-200 text-xs outline-none focus:border-pp-500 focus:bg-white transition" />
        </div>

        <!-- FILTER TABS -->
        <div class="flex items-center gap-1 bg-slate-100 p-1 rounded-xl text-[11px] font-bold">
          <button wire:click="$set('activeTab', 'all')" class="flex-1 py-1 rounded-lg text-center transition {{ $activeTab === 'all' ? 'bg-white text-slate-900 shadow-2xs' : 'text-slate-500 hover:text-slate-800' }}">All (6)</button>
          <button wire:click="$set('activeTab', 'unread')" class="flex-1 py-1 rounded-lg text-center transition {{ $activeTab === 'unread' ? 'bg-white text-slate-900 shadow-2xs' : 'text-slate-500 hover:text-slate-800' }}">Unread (3)</button>
          <button wire:click="$set('activeTab', 'offers')" class="flex-1 py-1 rounded-lg text-center transition {{ $activeTab === 'offers' ? 'bg-white text-slate-900 shadow-2xs' : 'text-slate-500 hover:text-slate-800' }}">Offers (2)</button>
          <button wire:click="$set('activeTab', 'orders')" class="flex-1 py-1 rounded-lg text-center transition {{ $activeTab === 'orders' ? 'bg-white text-slate-900 shadow-2xs' : 'text-slate-500 hover:text-slate-800' }}">Orders (1)</button>
        </div>
      </div>

      <!-- CONVERSATIONS SCROLLABLE LIST -->
      <div class="flex-1 min-h-0 overflow-y-auto divide-y divide-slate-100">
        
        <!-- CONVERSATION 1: ADAM COMPUTERS -->
        <button wire:click="selectConversation('adam')" class="w-full text-left p-4 flex gap-3 transition cursor-pointer {{ $activeConversationId === 'adam' ? 'bg-pp-50/70 border-l-4 border-pp-600' : 'hover:bg-slate-50 bg-pp-50/30' }}">
          <div class="relative shrink-0">
            <span class="w-11 h-11 rounded-full bg-pp-100 text-pp-700 font-extrabold grid place-items-center text-sm shadow-2xs">A</span>
            <span class="w-3 h-3 rounded-full bg-emerald-500 border-2 border-white absolute bottom-0 right-0"></span>
          </div>
          <div class="flex-1 min-w-0">
            <div class="flex items-center justify-between gap-1">
              <span class="font-bold text-xs text-slate-900 truncate">Adam Computers</span>
              <span class="text-[10px] font-semibold text-pp-600">5m ago</span>
            </div>
            <div class="flex items-center gap-1.5 mt-0.5">
              <span class="px-1.5 py-0.2 rounded bg-pp-100 text-pp-800 text-[9px] font-extrabold uppercase">OFFER AGREED</span>
              <span class="text-[11px] text-slate-500 truncate">HP EliteBook 840 G5</span>
            </div>
            <p class="text-xs font-semibold text-slate-800 mt-1 truncate">I can accept ₦260,000 if you can pick up today...</p>
          </div>
          <span class="w-2.5 h-2.5 rounded-full bg-pp-600 mt-1.5 shrink-0"></span>
        </button>

        <!-- CONVERSATION 2: ABEL ELECTRONICS HUB -->
        <button wire:click="selectConversation('abel')" class="w-full text-left p-4 flex gap-3 transition cursor-pointer {{ $activeConversationId === 'abel' ? 'bg-pp-50/70 border-l-4 border-pp-600' : 'hover:bg-slate-50' }}">
          <div class="relative shrink-0">
            <span class="w-11 h-11 rounded-full bg-amber-100 text-amber-800 font-extrabold grid place-items-center text-sm">A</span>
          </div>
          <div class="flex-1 min-w-0">
            <div class="flex items-center justify-between gap-1">
              <span class="font-bold text-xs text-slate-900 truncate">Abel Electronics Hub</span>
              <span class="text-[10px] text-slate-400">24m ago</span>
            </div>
            <div class="flex items-center gap-1.5 mt-0.5">
              <span class="px-1.5 py-0.2 rounded bg-amber-100 text-amber-900 text-[9px] font-extrabold uppercase">INQUIRY</span>
              <span class="text-[11px] text-slate-500 truncate">Dell Latitude 5420 Board</span>
            </div>
            <p class="text-xs text-slate-600 mt-1 truncate">Can you confirm if your model uses the i5 11th Gen processor?</p>
          </div>
          <span class="w-2.5 h-2.5 rounded-full bg-pp-600 mt-1.5 shrink-0"></span>
        </button>

        <!-- CONVERSATION 3: SETH REPAIR YARD -->
        <button wire:click="selectConversation('seth')" class="w-full text-left p-4 flex gap-3 transition cursor-pointer {{ $activeConversationId === 'seth' ? 'bg-pp-50/70 border-l-4 border-pp-600' : 'hover:bg-slate-50' }}">
          <div class="relative shrink-0">
            <span class="w-11 h-11 rounded-full bg-emerald-100 text-emerald-800 font-extrabold grid place-items-center text-sm">S</span>
          </div>
          <div class="flex-1 min-w-0">
            <div class="flex items-center justify-between gap-1">
              <span class="font-bold text-xs text-slate-900 truncate">Seth Repair Yard</span>
              <span class="text-[10px] text-slate-400">1h ago</span>
            </div>
            <div class="flex items-center gap-1.5 mt-0.5">
              <span class="px-1.5 py-0.2 rounded bg-emerald-100 text-emerald-800 text-[9px] font-extrabold uppercase">PART AVAILABILITY</span>
              <span class="text-[11px] text-slate-500 truncate">840 G5 Battery</span>
            </div>
            <p class="text-xs text-slate-600 mt-1 truncate">Yes, I have 3 tested units available for pickup in Ikeja.</p>
          </div>
        </button>

        <!-- CONVERSATION 4: LAPTOPWIZARD -->
        <button wire:click="selectConversation('laptopwizard')" class="w-full text-left p-4 flex gap-3 transition cursor-pointer {{ $activeConversationId === 'laptopwizard' ? 'bg-pp-50/70 border-l-4 border-pp-600' : 'hover:bg-slate-50' }}">
          <div class="relative shrink-0">
            <span class="w-11 h-11 rounded-full bg-purple-100 text-purple-800 font-extrabold grid place-items-center text-sm">L</span>
          </div>
          <div class="flex-1 min-w-0">
            <div class="flex items-center justify-between gap-1">
              <span class="font-bold text-xs text-slate-900 truncate">LaptopWizard</span>
              <span class="text-[10px] text-slate-400">3h ago</span>
            </div>
            <div class="flex items-center gap-1.5 mt-0.5">
              <span class="px-1.5 py-0.2 rounded bg-purple-100 text-purple-800 text-[9px] font-extrabold uppercase">ORDER #PP-89211</span>
              <span class="text-[11px] text-slate-500 truncate">MacBook M1 Screen</span>
            </div>
            <p class="text-xs text-slate-600 mt-1 truncate">Payment confirmed in Escrow! Preparing dispatch now.</p>
          </div>
        </button>

        <!-- CONVERSATION 5: TECHSAM REPAIRS -->
        <button wire:click="selectConversation('techsam')" class="w-full text-left p-4 flex gap-3 transition cursor-pointer {{ $activeConversationId === 'techsam' ? 'bg-pp-50/70 border-l-4 border-pp-600' : 'hover:bg-slate-50' }}">
          <div class="relative shrink-0">
            <span class="w-11 h-11 rounded-full bg-slate-100 text-slate-700 font-extrabold grid place-items-center text-sm">T</span>
          </div>
          <div class="flex-1 min-w-0">
            <div class="flex items-center justify-between gap-1">
              <span class="font-bold text-xs text-slate-900 truncate">TechSam Repairs</span>
              <span class="text-[10px] text-slate-400">Yesterday</span>
            </div>
            <div class="flex items-center gap-1.5 mt-0.5">
              <span class="px-1.5 py-0.2 rounded bg-slate-100 text-slate-700 text-[9px] font-extrabold uppercase">SCRAP SALVAGE</span>
              <span class="text-[11px] text-slate-500 truncate">Dell Inspiron 15 Scrap</span>
            </div>
            <p class="text-xs text-slate-600 mt-1 truncate">Will send rider to collect the scrap casing tomorrow morning.</p>
          </div>
        </button>

        <!-- CONVERSATION 6: MAX EXPRESS COURIER -->
        <button wire:click="selectConversation('max_courier')" class="w-full text-left p-4 flex gap-3 transition cursor-pointer {{ $activeConversationId === 'max_courier' ? 'bg-pp-50/70 border-l-4 border-pp-600' : 'hover:bg-slate-50' }}">
          <div class="relative shrink-0">
            <span class="w-11 h-11 rounded-full bg-blue-100 text-blue-800 font-extrabold grid place-items-center text-sm"><i class="fas fa-motorcycle"></i></span>
          </div>
          <div class="flex-1 min-w-0">
            <div class="flex items-center justify-between gap-1">
              <span class="font-bold text-xs text-slate-900 truncate">Max Express Courier</span>
              <span class="text-[10px] text-slate-400">Aug 24</span>
            </div>
            <div class="flex items-center gap-1.5 mt-0.5">
              <span class="px-1.5 py-0.2 rounded bg-blue-100 text-blue-800 text-[9px] font-extrabold uppercase">DELIVERY JOB</span>
              <span class="text-[11px] text-slate-500 truncate">Dispatcher #402</span>
            </div>
            <p class="text-xs text-slate-600 mt-1 truncate">I have picked up the parcel from Adam's shop. Heading to Lekki.</p>
          </div>
        </button>

      </div>
    </div>

    <!-- RIGHT PANEL: ACTIVE CONVERSATION WINDOW OR EMPTY PROMPT -->
    <div class="flex-1 flex flex-col min-w-0 bg-slate-50/50 {{ $activeConversationId ? 'flex' : 'hidden lg:flex' }}">
      
      @if ($activeConversationId)
        
        <!-- CHAT HEADER BAR -->
        <div class="h-16 px-4 sm:px-6 border-b border-slate-200 bg-white flex items-center justify-between shrink-0 shadow-2xs">
          <div class="flex items-center gap-3 min-w-0">
            <!-- MOBILE BACK BUTTON -->
            <button wire:click="clearConversation" class="lg:hidden p-2 rounded-xl text-slate-600 hover:bg-slate-100 transition cursor-pointer" title="Back to Inbox">
              <i class="fas fa-arrow-left text-sm"></i>
            </button>

            <div class="relative shrink-0">
              <span class="w-10 h-10 rounded-full bg-pp-100 text-pp-700 font-extrabold grid place-items-center text-sm">A</span>
              <span class="w-2.5 h-2.5 rounded-full bg-emerald-500 border-2 border-white absolute bottom-0 right-0"></span>
            </div>

            <div class="min-w-0">
              <div class="flex items-center gap-1.5">
                <h3 class="font-extrabold text-sm text-slate-950 truncate">
                  @if($activeConversationId === 'adam') Adam Computers
                  @elseif($activeConversationId === 'abel') Abel Electronics Hub
                  @elseif($activeConversationId === 'seth') Seth Repair Yard
                  @elseif($activeConversationId === 'laptopwizard') LaptopWizard
                  @elseif($activeConversationId === 'techsam') TechSam Repairs
                  @else Max Express Courier @endif
                </h3>
                <span class="px-1.5 py-0.2 rounded bg-emerald-100 text-emerald-800 text-[9px] font-extrabold">VERIFIED SELLER</span>
              </div>
              <p class="text-[11px] text-slate-500 truncate">Computer Village, Ikeja, Lagos · Active recently</p>
            </div>
          </div>

          <div class="flex items-center gap-2 shrink-0">
            <a href="{{ route('listing-details') }}" class="hidden sm:inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl border border-slate-200 hover:border-pp-300 text-slate-700 font-bold text-xs transition">
              <i class="fas fa-laptop text-pp-600"></i> View Listing
            </a>
            <button class="p-2 rounded-xl hover:bg-slate-100 text-slate-500 text-sm cursor-pointer" title="More options">
              <i class="fas fa-ellipsis-v"></i>
            </button>
          </div>
        </div>

        <!-- ITEM CONTEXT BANNER -->
        <div class="px-4 sm:px-6 py-2.5 bg-pp-50/80 border-b border-pp-100 flex items-center justify-between gap-3 text-xs shrink-0">
          <div class="flex items-center gap-2.5 min-w-0">
            <span class="text-xl">💻</span>
            <div class="truncate">
              <span class="font-extrabold text-slate-900">HP EliteBook 840 G5 Laptop</span>
              <span class="text-slate-500 ml-1">· Intel i5, 8GB RAM, 256GB SSD</span>
            </div>
          </div>
          <div class="flex items-center gap-2 shrink-0">
            <span class="font-black text-slate-950 text-sm">₦280,000</span>
            <a href="{{ route('cart') }}" class="px-3 py-1 rounded-lg bg-pp-600 hover:bg-pp-700 text-white font-bold text-[11px] transition">Make Package Offer</a>
          </div>
        </div>

        <!-- CHAT MESSAGES STREAM -->
        <div class="flex-1 min-h-0 overflow-y-auto p-4 sm:p-6 space-y-4">
          
          <div class="text-center my-2">
            <span class="px-3 py-1 rounded-full bg-slate-200/70 text-slate-600 text-[10px] font-bold uppercase tracking-wider">Today, August 25</span>
          </div>

          <!-- MESSAGE 1: BUYER -->
          <div class="flex justify-start">
            <div class="max-w-[85%] sm:max-w-[70%] space-y-1">
              <div class="bg-white border border-slate-200 rounded-2xl rounded-tl-xs p-3.5 text-xs text-slate-800 shadow-2xs leading-relaxed">
                Hi, is this item still available at your shop? I need a clean unit for client work.
              </div>
              <span class="text-[10px] text-slate-400 block px-1">1:45 PM</span>
            </div>
          </div>

          <!-- MESSAGE 2: SELLER -->
          <div class="flex justify-end">
            <div class="max-w-[85%] sm:max-w-[70%] space-y-1">
              <div class="bg-pp-600 text-white rounded-2xl rounded-tr-xs p-3.5 text-xs shadow-2xs leading-relaxed">
                Hello! Yes, it is fully tested and available at our shop in Computer Village.
              </div>
              <span class="text-[10px] text-slate-400 text-right block px-1">1:48 PM</span>
            </div>
          </div>

          <!-- MESSAGE 3: SELLER -->
          <div class="flex justify-end">
            <div class="max-w-[85%] sm:max-w-[70%] space-y-1">
              <div class="bg-pp-600 text-white rounded-2xl rounded-tr-xs p-3.5 text-xs shadow-2xs leading-relaxed">
                It comes with clean original accessories and 14-day warranty for testing.
              </div>
              <span class="text-[10px] text-slate-400 text-right block px-1">1:49 PM</span>
            </div>
          </div>

          <!-- MESSAGE 4: BUYER -->
          <div class="flex justify-start">
            <div class="max-w-[85%] sm:max-w-[70%] space-y-1">
              <div class="bg-white border border-slate-200 rounded-2xl rounded-tl-xs p-3.5 text-xs text-slate-800 shadow-2xs leading-relaxed">
                Great! Can you accept a discounted price if I come for pickup this afternoon?
              </div>
              <span class="text-[10px] text-slate-400 block px-1">2:10 PM</span>
            </div>
          </div>

          <!-- MESSAGE 5: SELLER (ACCEPTED OFFER) -->
          <div class="flex justify-end">
            <div class="max-w-[85%] sm:max-w-[70%] space-y-1">
              <div class="bg-pp-600 text-white rounded-2xl rounded-tr-xs p-3.5 text-xs shadow-2xs leading-relaxed">
                I can accept your price offer if you can pick up today before 5 PM.
              </div>
              <span class="text-[10px] text-slate-400 text-right block px-1">2:14 PM</span>
            </div>
          </div>

          <!-- SYSTEM CALLOUT NOTICE -->
          <div class="p-3.5 rounded-2xl bg-emerald-50 border border-emerald-200 text-xs text-emerald-900 space-y-1 my-2">
            <div class="flex items-center justify-between font-extrabold text-emerald-800">
              <span class="flex items-center gap-1.5"><i class="fas fa-handshake text-emerald-600"></i> Offer Agreed by Seller</span>
              <span>Active Offer</span>
            </div>
            <p class="text-[11px] text-emerald-700 leading-relaxed">
              Price agreed with seller. Proceed to checkout to reserve item safely in Escrow.
            </p>
          </div>

          <!-- MESSAGE 6: SELLER -->
          <div class="flex justify-end">
            <div class="max-w-[85%] sm:max-w-[70%] space-y-1">
              <div class="bg-pp-600 text-white rounded-2xl rounded-tr-xs p-3.5 text-xs shadow-2xs leading-relaxed">
                Let me know when your rider is outside or if you're coming yourself!
              </div>
              <span class="text-[10px] text-slate-400 text-right block px-1">2:18 PM</span>
            </div>
          </div>

        </div>

        <!-- CHAT INPUT FOOTER WITH ATTACHMENT ICON ON THE LEFT -->
        <div class="p-4 border-t border-slate-200 bg-white shrink-0">
          <form onsubmit="event.preventDefault();" class="flex items-center gap-2">
            
            <!-- ATTACHMENT ICON BUTTON -->
            <label class="p-2.5 rounded-xl text-slate-500 hover:text-pp-600 hover:bg-pp-50 transition cursor-pointer shrink-0" title="Attach file, image, or receipt">
              <i class="fas fa-paperclip text-base"></i>
              <input type="file" class="hidden" />
            </label>

            <!-- MESSAGE TEXT INPUT -->
            <input type="text" placeholder="Write a message..." class="flex-1 bg-slate-50 border border-slate-200 rounded-xl px-4 py-2.5 text-xs outline-none focus:border-pp-500 focus:bg-white transition" />

            <!-- SEND BUTTON -->
            <button type="submit" class="px-5 py-2.5 rounded-xl bg-pp-600 hover:bg-pp-700 text-white font-extrabold text-xs transition cursor-pointer flex items-center gap-1.5 shrink-0 shadow-2xs">
              <i class="fas fa-paper-plane text-xs"></i>
              <span class="hidden sm:inline">Send</span>
            </button>
          </form>
        </div>

      @else
        
        <!-- BLANK DESKTOP PROMPT WHEN NO CONVERSATION IS SELECTED -->
        <div class="flex-1 grid place-items-center p-8 text-center bg-slate-50/50">
          <div class="max-w-sm space-y-3">
            <div class="w-16 h-16 rounded-3xl bg-pp-50 text-pp-600 grid place-items-center text-2xl mx-auto shadow-2xs">
              <i class="fas fa-comments"></i>
            </div>
            <h3 class="text-base font-extrabold text-slate-900">No Conversation</h3>
            <p class="text-xs text-slate-500 leading-relaxed">
              Select a message to resume conversation.
            </p>
          </div>
        </div>

      @endif

    </div>

  </div>

</div>