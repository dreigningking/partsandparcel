<div class="space-y-8">
  
  <div class="bg-white rounded-3xl border border-slate-200 shadow-soft overflow-hidden flex flex-col h-[calc(100vh-140px)] min-h-[680px]">
    
    <!-- CHAT HEADER BAR -->
    <div class="h-16 px-4 sm:px-6 border-b border-slate-200 bg-white flex items-center justify-between shrink-0 shadow-2xs">
      <div class="flex items-center gap-3 min-w-0">
        <!-- BACK TO MESSAGES INBOX BUTTON -->
        <a href="{{ route('messages') }}" class="p-2 rounded-xl text-slate-600 hover:bg-slate-100 transition flex items-center gap-1.5 font-extrabold text-xs">
          <i class="fas fa-arrow-left"></i>
          <span>Inbox</span>
        </a>

        <div class="relative shrink-0">
          <span class="w-10 h-10 rounded-full bg-pp-100 text-pp-700 font-extrabold grid place-items-center text-sm">A</span>
          <span class="w-2.5 h-2.5 rounded-full bg-emerald-500 border-2 border-white absolute bottom-0 right-0"></span>
        </div>

        <div class="min-w-0">
          <div class="flex items-center gap-1.5">
            <h3 class="font-extrabold text-sm text-slate-950 truncate">Adam Computers</h3>
            <span class="px-1.5 py-0.2 rounded bg-emerald-100 text-emerald-800 text-[9px] font-extrabold">VERIFIED SELLER</span>
          </div>
          <p class="text-[11px] text-slate-500 truncate">Computer Village, Ikeja, Lagos · Active 5m ago</p>
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
    <div class="flex-1 min-h-0 overflow-y-auto p-4 sm:p-6 space-y-4 bg-slate-50/50">
      
      <div class="text-center my-2">
        <span class="px-3 py-1 rounded-full bg-slate-200/70 text-slate-600 text-[10px] font-bold uppercase tracking-wider">Today, August 25</span>
      </div>

      <!-- MESSAGE 1: BUYER -->
      <div class="flex justify-start">
        <div class="max-w-[85%] sm:max-w-[70%] space-y-1">
          <div class="bg-white border border-slate-200 rounded-2xl rounded-tl-xs p-3.5 text-xs text-slate-800 shadow-2xs leading-relaxed">
            Hi Adam, is this EliteBook 840 G5 still available at your Computer Village shop? I need a clean unit for client work.
          </div>
          <span class="text-[10px] text-slate-400 block px-1">1:45 PM</span>
        </div>
      </div>

      <!-- MESSAGE 2: SELLER -->
      <div class="flex justify-end">
        <div class="max-w-[85%] sm:max-w-[70%] space-y-1">
          <div class="bg-pp-600 text-white rounded-2xl rounded-tr-xs p-3.5 text-xs shadow-2xs leading-relaxed">
            Hello! Yes, it is fully tested and available at Stall 14, Otigba Street, opposite Slot.
          </div>
          <span class="text-[10px] text-slate-400 text-right block px-1">1:48 PM</span>
        </div>
      </div>

      <!-- MESSAGE 3: SELLER -->
      <div class="flex justify-end">
        <div class="max-w-[85%] sm:max-w-[70%] space-y-1">
          <div class="bg-pp-600 text-white rounded-2xl rounded-tr-xs p-3.5 text-xs shadow-2xs leading-relaxed">
            It comes with clean original HP charger and 14-day warranty for testing.
          </div>
          <span class="text-[10px] text-slate-400 text-right block px-1">1:49 PM</span>
        </div>
      </div>

      <!-- MESSAGE 4: BUYER -->
      <div class="flex justify-start">
        <div class="max-w-[85%] sm:max-w-[70%] space-y-1">
          <div class="bg-white border border-slate-200 rounded-2xl rounded-tl-xs p-3.5 text-xs text-slate-800 shadow-2xs leading-relaxed">
            Great! Can you accept ₦260,000 if I come for pickup this afternoon?
          </div>
          <span class="text-[10px] text-slate-400 block px-1">2:10 PM</span>
        </div>
      </div>

      <!-- MESSAGE 5: SELLER (ACCEPTED OFFER) -->
      <div class="flex justify-end">
        <div class="max-w-[85%] sm:max-w-[70%] space-y-1">
          <div class="bg-pp-600 text-white rounded-2xl rounded-tr-xs p-3.5 text-xs shadow-2xs leading-relaxed">
            I can accept ₦260,000 if you can pick up today at Stall 14 before 5 PM.
          </div>
          <span class="text-[10px] text-slate-400 text-right block px-1">2:14 PM</span>
        </div>
      </div>

      <!-- SYSTEM CALLOUT NOTICE -->
      <div class="p-3.5 rounded-2xl bg-emerald-50 border border-emerald-200 text-xs text-emerald-900 space-y-1 my-2">
        <div class="flex items-center justify-between font-extrabold text-emerald-800">
          <span class="flex items-center gap-1.5"><i class="fas fa-handshake text-emerald-600"></i> Offer Agreed by Seller</span>
          <span>₦260,000</span>
        </div>
        <p class="text-[11px] text-emerald-700 leading-relaxed">
          Adam Computers agreed to sell HP EliteBook 840 G5 for ₦260,000. Proceed to checkout to reserve item in Escrow.
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
        
        <!-- ATTACHMENT ICON BUTTON (ATTACHMENT ICON TO LEFT OF MESSAGE INPUT) -->
        <label class="p-2.5 rounded-xl text-slate-500 hover:text-pp-600 hover:bg-pp-50 transition cursor-pointer shrink-0" title="Attach file, image, or receipt">
          <i class="fas fa-paperclip text-base"></i>
          <input type="file" class="hidden" />
        </label>

        <!-- MESSAGE TEXT INPUT -->
        <input type="text" placeholder="Write a message to Adam Computers..." class="flex-1 bg-slate-50 border border-slate-200 rounded-xl px-4 py-2.5 text-xs outline-none focus:border-pp-500 focus:bg-white transition" />

        <!-- SEND BUTTON -->
        <button type="submit" class="px-5 py-2.5 rounded-xl bg-pp-600 hover:bg-pp-700 text-white font-extrabold text-xs transition cursor-pointer flex items-center gap-1.5 shrink-0 shadow-2xs">
          <i class="fas fa-paper-plane text-xs"></i>
          <span class="hidden sm:inline">Send</span>
        </button>
      </form>
    </div>

  </div>

</div>