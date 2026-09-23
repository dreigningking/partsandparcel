<div class="flex flex-col gap-6">

  <!-- TOP NAV & HEADER -->
  <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
    <div>
      <div class="flex items-center gap-2 mb-1">
        <a href="{{ route('myrequests') }}" class="text-xs font-bold text-pp-600 hover:underline flex items-center gap-1">
          <i class="fas fa-arrow-left text-[10px]"></i> Back to My Requests
        </a>
        <span class="text-slate-300">·</span>
        <span class="text-xs font-extrabold text-slate-500">Request Ref: #REQ-{{ $discussion?->id ?? '8402' }}</span>
      </div>
      <h1 class="text-2xl font-extrabold text-slate-950 flex items-center gap-3 flex-wrap">
        <span>{{ $discussion?->title ?? 'Looking for HP EliteBook 840 G5 Motherboard' }}</span>
        <span class="px-2.5 py-0.5 rounded-full bg-emerald-50 text-emerald-700 font-bold text-[10px] uppercase border border-emerald-100">
          {{ strtoupper($discussion?->status ?? 'OPEN') }} FOR PROPOSALS
        </span>
      </h1>
      <p class="text-xs text-slate-500 mt-0.5">
        Posted {{ $discussion ? $discussion->created_at->diffForHumans() : '2 hours ago' }} · Target Budget: <strong class="text-pp-700">{{ $discussion?->attachments['budget'] ?? '₦70,000 - ₦90,000' }}</strong> · Location: {{ $discussion?->attachments['location'] ?? 'Ikeja, Lagos' }}
      </p>
    </div>

    <div class="flex items-center gap-2">
      <a href="{{ route('community.request', ['id' => $discussion?->id ?? 1]) }}" class="px-4 py-2 rounded-xl bg-pp-600 hover:bg-pp-700 text-white font-bold text-xs shadow-2xs transition">
        View Public Request Page →
      </a>
    </div>
  </div>

  <!-- RECEIVED VENDOR OFFERS TABLE -->
  <div class="bg-white rounded-3xl border border-slate-200 p-6 space-y-4 shadow-soft">
    <div class="flex items-center justify-between border-b border-slate-100 pb-3">
      <div>
        <h3 class="text-xs font-extrabold text-slate-900 uppercase tracking-wider flex items-center gap-2">
          <i class="fas fa-handshake text-pp-600"></i> Received Private Vendor Offers ({{ count($offers) }} Offers)
        </h3>
        <p class="text-xs text-slate-500 mt-0.5">Review and accept vendor quotes or submit counter-offers.</p>
      </div>
    </div>

    <div class="overflow-x-auto">
      <table class="w-full text-left text-xs">
        <thead class="bg-slate-50 text-slate-500 uppercase font-extrabold border-b border-slate-200">
          <tr>
            <th class="p-3.5">Vendor Store</th>
            <th class="p-3.5">Proposed Price</th>
            <th class="p-3.5">Warranty Term</th>
            <th class="p-3.5">Fulfillment Method</th>
            <th class="p-3.5">Status</th>
            <th class="p-3.5 text-right">Actions</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-slate-100 font-medium text-slate-700">
          @forelse ($offers as $off)
            <tr class="hover:bg-slate-50/80 transition">
              <td class="p-3.5 font-bold text-slate-900">
                <div class="flex items-center gap-2">
                  <span class="w-8 h-8 rounded-full bg-pp-100 text-pp-700 font-bold grid place-items-center text-xs">
                    {{ strtoupper(substr($off['vendor_name'], 0, 1)) }}
                  </span>
                  <div>
                    <span>{{ $off['vendor_name'] }}</span>
                    <span class="text-[10px] text-slate-400 block">{{ $off['vendor_city'] }}</span>
                  </div>
                </div>
              </td>
              <td class="p-3.5 font-black text-slate-950 text-sm">₦{{ number_format($off['price']) }}</td>
              <td class="p-3.5"><span class="px-2 py-0.5 rounded bg-emerald-100 text-emerald-800 text-[10px] font-extrabold">{{ $off['warranty'] }}</span></td>
              <td class="p-3.5">{{ $off['delivery'] }}</td>
              <td class="p-3.5">
                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-extrabold uppercase {{ $off['status'] === 'accepted' ? 'bg-emerald-100 text-emerald-800' : ($off['status'] === 'pending' ? 'bg-amber-100 text-amber-900' : 'bg-slate-100 text-slate-700') }}">
                  {{ $off['status'] }}
                </span>
              </td>
              <td class="p-3.5 text-right space-x-2">
                <a href="{{ route('offers.view', ['offer_id' => 'OFF-' . $off['id']]) }}" class="px-3.5 py-1.5 rounded-lg bg-pp-600 hover:bg-pp-700 text-white font-extrabold text-[11px] transition inline-block">
                  View Thread &amp; Accept →
                </a>
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="6" class="p-8 text-center text-slate-500">
                No private offers have been submitted for this request yet.
              </td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>

</div>