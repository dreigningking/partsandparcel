<div class="flex flex-col gap-6">

  <!-- HEADER -->
  <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
    <div>
      <h1 class="text-2xl font-extrabold text-slate-950">Account &amp; Business Profile</h1>
      <p class="text-xs text-slate-500 mt-0.5">Manage your personal information, technician credentials, and payment payout settings.</p>
    </div>

    <span class="px-3.5 py-1.5 rounded-full bg-emerald-100 text-emerald-800 text-xs font-extrabold flex items-center gap-1.5 self-start sm:self-auto">
      <i class="fas fa-check-circle text-emerald-600"></i> VERIFIED SELLER &amp; TECHNICIAN
    </span>
  </div>

  <!-- PROFILE CARDS GRID -->
  <div class="grid md:grid-cols-12 gap-6">
    
    <!-- LEFT: PERSONAL & STORE INFO -->
    <div class="md:col-span-8 space-y-6">
      
      <div class="bg-white rounded-3xl border border-slate-200 p-6 space-y-5 shadow-soft">
        <h3 class="text-xs font-extrabold text-slate-900 uppercase tracking-wider flex items-center gap-2 border-b border-slate-100 pb-3">
          <i class="fas fa-store text-pp-600"></i> Business &amp; Personal Information
        </h3>

        <div class="grid sm:grid-cols-2 gap-4 text-xs">
          <div class="space-y-1">
            <label class="font-bold text-slate-700 block">Full Name</label>
            <input type="text" value="Emmanuel Reign" class="w-full p-2.5 rounded-xl border border-slate-200 font-bold text-slate-900 outline-none focus:border-pp-500" />
          </div>

          <div class="space-y-1">
            <label class="font-bold text-slate-700 block">Store / Business Name</label>
            <input type="text" value="Adam Computers Ltd" class="w-full p-2.5 rounded-xl border border-slate-200 font-bold text-slate-900 outline-none focus:border-pp-500" />
          </div>

          <div class="space-y-1">
            <label class="font-bold text-slate-700 block">Email Address</label>
            <input type="email" value="reign@partsandparcel.com" class="w-full p-2.5 rounded-xl border border-slate-200 font-bold text-slate-900 outline-none focus:border-pp-500" />
          </div>

          <div class="space-y-1">
            <label class="font-bold text-slate-700 block">Phone Number</label>
            <input type="text" value="+234 803 123 4567" class="w-full p-2.5 rounded-xl border border-slate-200 font-bold text-slate-900 outline-none focus:border-pp-500" />
          </div>
        </div>

        <div class="pt-3 border-t border-slate-100 flex justify-end">
          <button class="px-5 py-2.5 rounded-xl bg-pp-600 hover:bg-pp-700 text-white font-extrabold text-xs shadow-xs transition cursor-pointer">
            Save Profile Changes
          </button>
        </div>
      </div>

    </div>

    <!-- RIGHT: PAYOUT BANK ACCOUNT DETAILS -->
    <div class="md:col-span-4 space-y-6">
      
      <div class="bg-white rounded-3xl border border-slate-200 p-6 space-y-4 shadow-soft">
        <h3 class="text-xs font-extrabold text-slate-900 uppercase tracking-wider border-b border-slate-100 pb-3 flex items-center gap-2">
          <i class="fas fa-university text-pp-600"></i> Direct Seller Payout Bank
        </h3>

        <div class="p-4 rounded-2xl bg-pp-50 border border-pp-200 space-y-2 text-xs">
          <span class="text-slate-400 font-bold text-[10px] uppercase block">Connected Payout Account</span>
          <h4 class="text-base font-extrabold text-slate-900">Guaranty Trust Bank (GTBank)</h4>
          <p class="font-bold text-slate-800">Account #: 0123456789</p>
          <p class="text-slate-600">Account Name: Adam Computers Ltd</p>
        </div>

        <button class="w-full py-2.5 rounded-xl border border-slate-200 text-slate-700 font-bold text-xs hover:bg-slate-50 transition cursor-pointer">
          Update Bank Details
        </button>
      </div>

    </div>

  </div>

</div>