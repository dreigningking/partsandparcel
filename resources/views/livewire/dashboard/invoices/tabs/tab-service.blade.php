<div class="flex flex-col gap-6">

  <!-- SERVICE JOBS HEADER -->
  <div class="bg-white rounded-3xl border border-slate-200 p-6 sm:p-7 shadow-soft space-y-4">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 border-b border-slate-100 pb-4">
      <div class="flex items-center gap-3">
        <div class="w-11 h-11 rounded-2xl bg-sky-50 text-sky-600 flex items-center justify-center text-lg shrink-0 border border-sky-200/60 shadow-2xs">
          <i class="fas fa-screwdriver-wrench"></i>
        </div>
        <div>
          <h2 class="text-sm font-black text-slate-950 uppercase tracking-wider flex items-center gap-2">
            <span>Repair &amp; Service Job Performance</span>
            <span class="px-2.5 py-0.5 rounded-full bg-sky-100 text-sky-800 text-[10px] font-black uppercase">
              Service Order
            </span>
          </h2>
          <p class="text-xs text-slate-500 mt-0.5">
            Craftsmanship tracking, scheduled appointments, and technician reviews for services on this invoice.
          </p>
        </div>
      </div>
    </div>

    <!-- SERVICE JOBS LIST -->
    @forelse ($serviceJobs as $job)
      <div class="p-5 rounded-2xl bg-slate-50/80 border border-slate-200 space-y-4">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 border-b border-slate-200/60 pb-3">
          <div>
            <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Job Reference #SRV-{{ str_pad($job->id, 4, '0', STR_PAD_LEFT) }}</span>
            <h3 class="text-sm font-extrabold text-slate-900">{{ $job->title ?: 'Commercial Service Job' }}</h3>
          </div>
          <span class="px-3 py-1 rounded-full text-xs font-black uppercase self-start sm:self-auto {{ $job->status === 'completed' ? 'bg-emerald-100 text-emerald-800' : ($job->status === 'in_progress' ? 'bg-sky-100 text-sky-800' : 'bg-amber-100 text-amber-800') }}">
            {{ Str::headline($job->status) }}
          </span>
        </div>

        @if ($job->description)
          <p class="text-xs text-slate-700 leading-relaxed bg-white p-3 rounded-xl border border-slate-200/80">
            {{ $job->description }}
          </p>
        @endif

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3 text-xs">
          <!-- TECHNICIAN -->
          <div class="bg-white p-3 rounded-xl border border-slate-200/80 space-y-1">
            <span class="text-[10px] font-bold text-slate-400 uppercase block">Technician / Provider</span>
            <strong class="text-slate-950 font-extrabold block truncate">
              {{ $job->provider?->business_name ?: ($job->provider?->name ?? 'Assigned Technician') }}
            </strong>
            <span class="text-[11px] text-slate-500 block">
              <i class="fas fa-phone text-slate-400"></i> {{ $job->provider?->phone ?: 'Phone on file' }}
            </span>
          </div>

          <!-- DEVICE MODEL -->
          <div class="bg-white p-3 rounded-xl border border-slate-200/80 space-y-1">
            <span class="text-[10px] font-bold text-slate-400 uppercase block">Device / Hardware</span>
            <strong class="text-slate-950 font-extrabold block truncate">
              {{ $job->brand?->name }} {{ $job->deviceModel?->name ?: ($job->external_item_description ?? 'Equipment Service') }}
            </strong>
            <span class="text-[11px] text-slate-500 block truncate">
              Category: {{ $job->category?->name ?? 'General' }}
            </span>
          </div>

          <!-- SCHEDULE -->
          <div class="bg-white p-3 rounded-xl border border-slate-200/80 space-y-1">
            <span class="text-[10px] font-bold text-slate-400 uppercase block">Appointment / Schedule</span>
            <strong class="text-slate-950 font-extrabold block">
              {{ $job->scheduled_at ? $job->scheduled_at->format('M d, Y · h:i A') : 'Flexible / Arranged' }}
            </strong>
            <span class="text-[11px] text-slate-500 block">
              Started: {{ $job->started_at ? $job->started_at->format('M d, Y') : 'Pending' }}
            </span>
          </div>

          <!-- WARRANTY -->
          <div class="bg-white p-3 rounded-xl border border-slate-200/80 space-y-1">
            <span class="text-[10px] font-bold text-slate-400 uppercase block">Craftsmanship Warranty</span>
            <strong class="text-emerald-700 font-extrabold block">
              {{ $job->warranty_period_days ? $job->warranty_period_days . ' Days Protection' : 'Standard Guarantee' }}
            </strong>
            <span class="text-[11px] text-slate-500 block truncate">
              {{ $job->warranty_terms ?: 'Covers labor and parts' }}
            </span>
          </div>
        </div>

        @if ($job->notes)
          <div class="text-[11px] text-slate-500 bg-amber-50/50 p-2.5 rounded-xl border border-amber-200/60">
            <strong class="text-amber-900 font-bold">Service Notes:</strong> {{ $job->notes }}
          </div>
        @endif
      </div>
    @empty
      <!-- FALLBACK IF SERVICES ON INVOICE BUT NO FORMAL SERVICE JOB ENTITY -->
      <div class="p-6 rounded-2xl bg-slate-50/80 border border-slate-200 text-center text-xs space-y-2">
        <div class="w-10 h-10 rounded-xl bg-sky-100 text-sky-600 grid place-items-center mx-auto text-base">
          <i class="fas fa-screwdriver-wrench"></i>
        </div>
        <h4 class="font-extrabold text-slate-900">Service Items on Commercial Invoice</h4>
        <p class="text-slate-500 max-w-md mx-auto">
          This invoice includes repair and installation services fulfilled by <strong>{{ $invoice->seller->business_name ?: $invoice->seller->name }}</strong>.
        </p>
      </div>
    @endforelse
  </div>

  <!-- ========================================================================= -->
  <!-- ADD SERVICE REVIEW SECTION -->
  <!-- ========================================================================= -->
  <div class="p-6 sm:p-7 rounded-3xl bg-white border border-slate-200 shadow-soft space-y-4">
    <div class="border-b border-slate-100 pb-3 flex items-center justify-between">
      <div>
        <h3 class="text-xs font-extrabold text-slate-900 uppercase tracking-wider flex items-center gap-2">
          <i class="fas fa-star text-amber-500"></i> Review the Service Technician
        </h3>
        <p class="text-xs text-slate-500 mt-0.5">Share feedback on technician responsiveness, repair quality, and craftsmanship.</p>
      </div>
      <span class="px-2.5 py-0.5 rounded-full bg-blue-100 text-blue-800 text-[10px] font-extrabold uppercase">
        Verified Review
      </span>
    </div>

    @if ($isBuyer && in_array($invoice->status, ['paid', 'accepted']))
      @if (! $serviceReviewSubmitted)
        <div class="p-5 rounded-2xl bg-sky-50/60 border border-sky-200 space-y-4 text-xs">
          <div>
            <label class="font-bold text-slate-700 block mb-1">Technician Craftsmanship Rating:</label>
            <div class="flex items-center gap-1.5">
              @for ($i = 1; $i <= 5; $i++)
                <button type="button" wire:click="$set('serviceRating', {{ $i }})" class="text-2xl transition cursor-pointer {{ $serviceRating >= $i ? 'text-amber-400 hover:text-amber-500' : 'text-slate-200 hover:text-slate-300' }}">
                  <i class="fas fa-star"></i>
                </button>
              @endfor
              <span class="ml-2 text-xs font-black text-slate-800">{{ $serviceRating }} / 5 Stars</span>
            </div>
          </div>

          <div>
            <label class="font-bold text-slate-700 block mb-1">Workmanship Feedback:</label>
            <textarea wire:model="serviceReviewComment" rows="3" placeholder="How was the technician's repair speed, communication, punctuality, and repair quality?" class="w-full p-3 rounded-xl border border-slate-200 text-xs bg-white text-slate-900 outline-none focus:border-pp-500 transition shadow-2xs"></textarea>
          </div>

          <button wire:click="submitServiceReview" type="button" class="px-5 py-2.5 rounded-xl bg-sky-600 hover:bg-sky-700 text-white font-extrabold text-xs shadow-xs transition flex items-center gap-2 cursor-pointer">
            <i class="fas fa-paper-plane"></i>
            <span>Submit Technician Review</span>
          </button>
        </div>
      @else
        <div class="p-4 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-900 text-xs font-bold flex items-center gap-3">
          <div class="w-8 h-8 rounded-xl bg-emerald-600 text-white flex items-center justify-center shrink-0">
            <i class="fas fa-check"></i>
          </div>
          <div>
            <strong class="block">Your technician review has been recorded!</strong>
            <span class="text-emerald-700 font-normal text-[11px]">Thank you for supporting verified craftsmanship on Parts &amp; Parcel.</span>
          </div>
        </div>
      @endif
    @else
      <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200 text-xs text-slate-500 text-center">
        Service reviews unlock once the invoice is paid and the repair job is completed.
      </div>
    @endif
  </div>

</div>
