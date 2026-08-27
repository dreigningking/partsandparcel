<div class="flex flex-col gap-6">

  <!-- HEADER & TABS -->
  <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
    <div>
      <h1 class="text-2xl font-extrabold text-slate-950">Trust &amp; Resolution Center</h1>
      <p class="text-xs text-slate-500 mt-0.5">Manage claims, dispute resolution, and Escrow protection mediation cases.</p>
    </div>

    <div class="flex items-center gap-1 bg-white p-1 rounded-xl border border-slate-200 text-xs font-bold shadow-2xs">
      <button class="px-3 py-1.5 rounded-lg bg-pp-600 text-white">Open Cases (1)</button>
      <button class="px-3 py-1.5 rounded-lg text-emerald-700 hover:bg-emerald-50">Resolved (3)</button>
      <button class="px-3 py-1.5 rounded-lg text-slate-600 hover:bg-slate-50">Refunded</button>
    </div>
  </div>

  <!-- DISPUTES TABLE -->
  <div class="bg-white rounded-3xl border border-slate-200 p-6 space-y-4 shadow-soft">
    <div class="flex items-center justify-between border-b border-slate-100 pb-3">
      <h3 class="text-xs font-extrabold text-slate-900 uppercase tracking-wider flex items-center gap-2">
        <i class="fas fa-gavel text-pp-600"></i> Active Dispute Cases Stream
      </h3>
      <span class="text-xs text-slate-500 font-medium">Sorted by creation date</span>
    </div>

    <div class="overflow-x-auto">
      <table class="w-full text-left text-xs">
        <thead class="bg-slate-50 text-slate-500 uppercase font-extrabold border-b border-slate-200">
          <tr>
            <th class="p-3.5">Dispute Case Ref</th>
            <th class="p-3.5">Related Invoice</th>
            <th class="p-3.5">Complainant / Respondent</th>
            <th class="p-3.5">Issue Classification</th>
            <th class="p-3.5">Escrow Status</th>
            <th class="p-3.5 text-right">Action</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-slate-100 font-medium text-slate-700">
          <tr class="hover:bg-slate-50/80 transition">
            <td class="p-3.5 font-bold text-slate-900">
              <a href="{{ route('disputes.view', ['id' => 'DSP-7012']) }}" class="text-pp-600 hover:underline">#DSP-7012</a>
            </td>
            <td class="p-3.5 text-slate-600">#INV-9079</td>
            <td class="p-3.5 text-slate-900 font-bold">TechSam (Buyer) vs Abel Electronics</td>
            <td class="p-3.5"><span class="px-2 py-0.5 rounded bg-rose-100 text-rose-800 text-[10px] font-extrabold">DEFECTIVE BOARD ON ARRIVAL</span></td>
            <td class="p-3.5"><span class="px-2.5 py-0.5 rounded-full bg-amber-100 text-amber-900 text-[10px] font-extrabold">FUNDS HELD IN ESCROW</span></td>
            <td class="p-3.5 text-right">
              <a href="{{ route('disputes.view', ['id' => 'DSP-7012']) }}" class="px-3 py-1.5 rounded-lg bg-pp-600 hover:bg-pp-700 text-white font-extrabold text-[11px] transition">View Case &amp; Evidence →</a>
            </td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>

</div>