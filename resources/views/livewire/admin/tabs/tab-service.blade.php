<div class="space-y-6">

  <!-- ========================================================================= -->
  <!-- SERVICES AUDIT HEADER -->
  <!-- ========================================================================= -->
  <div class="bg-white dark:bg-slate-900 rounded-3xl border border-sky-200 dark:border-sky-900/60 p-6 sm:p-7 shadow-soft space-y-3">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 border-b border-sky-100 dark:border-sky-900/40 pb-4">
      <div class="flex items-center gap-3">
        <div class="w-11 h-11 rounded-2xl bg-sky-50 dark:bg-sky-950/60 text-sky-600 dark:text-sky-300 flex items-center justify-center text-lg shrink-0 border border-sky-200/60 dark:border-sky-800/60">
          <i class="fas fa-screwdriver-wrench"></i>
        </div>
        <div>
          <h2 class="text-sm font-black text-slate-950 dark:text-white uppercase tracking-wider flex items-center gap-2">
            <span>Commercial Service Jobs &amp; Technician Work</span>
            <span class="px-2.5 py-0.5 rounded-full bg-sky-100 text-sky-800 dark:bg-sky-900/60 dark:text-sky-300 text-[10px] font-black uppercase">
              Admin Oversight
            </span>
          </h2>
          <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">
            Audit assigned service providers, equipment diagnostics, scheduled appointments, craftsmanship terms, and buyer feedback.
          </p>
        </div>
      </div>

      <span class="text-xs text-slate-400 font-bold self-start sm:self-auto">
        {{ $serviceJobs->count() }} {{ Str::plural('service job', $serviceJobs->count()) }} linked
      </span>
    </div>
  </div>

  <!-- ========================================================================= -->
  <!-- SERVICE JOBS LIST -->
  <!-- ========================================================================= -->
  @if ($serviceJobs->isEmpty())
    <div class="p-8 rounded-3xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 text-center space-y-2 shadow-soft">
      <div class="w-12 h-12 rounded-2xl bg-slate-100 dark:bg-slate-800 text-slate-400 flex items-center justify-center text-xl mx-auto mb-2">
        <i class="fas fa-screwdriver-wrench"></i>
      </div>
      <h3 class="text-sm font-black text-slate-900 dark:text-white">No Service Jobs on Invoice</h3>
      <p class="text-xs text-slate-500 max-w-md mx-auto">There are no diagnostic, installation, or repair jobs attached to this commercial invoice.</p>
    </div>
  @else
    @foreach ($serviceJobs as $job)
      @php
        $adminServiceStatus = match(true) {
          $job->status === 'cancelled' => [
            'label' => 'Service Cancelled',
            'badge' => 'bg-rose-100 text-rose-800 border-rose-200 dark:bg-rose-950/60 dark:text-rose-300',
            'icon' => 'fa-ban text-rose-600',
          ],
          $job->status === 'completed' && $job->review => [
            'label' => 'Service Completed & Verified by Customer',
            'badge' => 'bg-emerald-100 text-emerald-800 border-emerald-200 dark:bg-emerald-950/60 dark:text-emerald-300',
            'icon' => 'fa-circle-check text-emerald-600',
          ],
          $job->status === 'completed' => [
            'label' => 'Service Completed - Awaiting Customer Inspection',
            'badge' => 'bg-emerald-100 text-emerald-800 border-emerald-200 dark:bg-emerald-950/60 dark:text-emerald-300',
            'icon' => 'fa-check text-emerald-600',
          ],
          in_array($job->status, ['in_progress', 'started']) => [
            'label' => 'Service in progress by Technician',
            'badge' => 'bg-sky-100 text-sky-800 border-sky-200 dark:bg-sky-950/60 dark:text-sky-300',
            'icon' => 'fa-gear fa-spin text-sky-600',
          ],
          default => [
            'label' => 'Waiting for Vendor / Technician Appointment',
            'badge' => 'bg-amber-100 text-amber-800 border-amber-200 dark:bg-amber-950/60 dark:text-amber-300',
            'icon' => 'fa-clock text-amber-600',
          ],
        };
      @endphp

      <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200 dark:border-slate-800 p-6 shadow-soft space-y-4">
        
        <!-- HEADER WITH ADMIN STATUS -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 border-b border-slate-100 dark:border-slate-800 pb-3">
          <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-2xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 flex items-center justify-center text-base shrink-0">
              <i class="fas {{ $adminServiceStatus['icon'] }}"></i>
            </div>
            <div>
              <div class="flex items-center gap-2 flex-wrap">
                <span class="text-xs font-mono font-bold text-slate-400">#SRV-{{ str_pad($job->id, 4, '0', STR_PAD_LEFT) }}</span>
                <strong class="text-sm font-extrabold text-slate-900 dark:text-white">{{ $job->title ?: 'Commercial Service Order' }}</strong>
                <span class="px-2.5 py-0.5 rounded-full text-xs font-black uppercase tracking-wider border {{ $adminServiceStatus['badge'] }}">
                  {{ $adminServiceStatus['label'] }}
                </span>
              </div>
              <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">
                Technician: <strong>{{ $job->provider?->business_name ?: ($job->provider?->name ?? 'Assigned Vendor') }}</strong>
                @if ($job->scheduled_at)
                  · Scheduled: {{ $job->scheduled_at->format('M d, Y · h:i A') }}
                @endif
              </p>
            </div>
          </div>
        </div>

        @if ($job->description)
          <div class="text-xs text-slate-700 dark:text-slate-300 bg-slate-50 dark:bg-slate-800/40 p-3.5 rounded-2xl border border-slate-200/80 dark:border-slate-800 leading-relaxed">
            <span class="text-[10px] font-black uppercase tracking-wider text-slate-400 block mb-1">Job Specifications</span>
            {{ $job->description }}
          </div>
        @endif

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3 text-xs">
          <!-- TECHNICIAN -->
          <div class="bg-slate-50 dark:bg-slate-800/40 p-3.5 rounded-2xl border border-slate-200/80 dark:border-slate-800 space-y-1">
            <span class="text-[10px] font-bold text-slate-400 uppercase block">Technician / Vendor</span>
            <strong class="text-slate-950 dark:text-white font-extrabold block truncate">
              {{ $job->provider?->business_name ?: ($job->provider?->name ?? 'Platform Tech') }}
            </strong>
            <span class="text-[11px] text-slate-500 block">
              <i class="fas fa-phone text-slate-400 text-[10px]"></i> {{ $job->provider?->phone ?: 'No phone' }}
            </span>
          </div>

          <!-- HARDWARE SPEC -->
          <div class="bg-slate-50 dark:bg-slate-800/40 p-3.5 rounded-2xl border border-slate-200/80 dark:border-slate-800 space-y-1">
            <span class="text-[10px] font-bold text-slate-400 uppercase block">Equipment / Hardware</span>
            <strong class="text-slate-950 dark:text-white font-extrabold block truncate">
              {{ $job->brand?->name }} {{ $job->deviceModel?->name ?: ($job->external_item_description ?? 'Equipment') }}
            </strong>
            <span class="text-[11px] text-slate-500 block truncate">
              Category: {{ $job->category?->name ?? 'General' }}
            </span>
          </div>

          <!-- SCHEDULE -->
          <div class="bg-slate-50 dark:bg-slate-800/40 p-3.5 rounded-2xl border border-slate-200/80 dark:border-slate-800 space-y-1">
            <span class="text-[10px] font-bold text-slate-400 uppercase block">Appointment Schedule</span>
            <strong class="text-slate-950 dark:text-white font-extrabold block">
              {{ $job->scheduled_at ? $job->scheduled_at->format('M d, Y · h:i A') : 'Arranged with buyer' }}
            </strong>
            <span class="text-[11px] text-slate-500 block">
              Status: {{ Str::headline($job->status) }}
            </span>
          </div>

          <!-- CRAFTSMANSHIP TERMS -->
          <div class="bg-slate-50 dark:bg-slate-800/40 p-3.5 rounded-2xl border border-slate-200/80 dark:border-slate-800 space-y-1">
            <span class="text-[10px] font-bold text-slate-400 uppercase block">Craftsmanship Warranty</span>
            <strong class="text-emerald-700 dark:text-emerald-400 font-extrabold block">
              {{ $job->warranty_period_days ? $job->warranty_period_days . ' Days Guarantee' : 'Standard Warranty' }}
            </strong>
            <span class="text-[11px] text-slate-500 block truncate">
              {{ $job->warranty_terms ?: 'Covers workmanship' }}
            </span>
          </div>
        </div>

        <!-- BUYER REVIEW (IF RECORDED) -->
        @if ($job->review)
          <div class="p-3.5 rounded-2xl bg-emerald-50/70 dark:bg-emerald-950/30 border border-emerald-200 dark:border-emerald-800/60 space-y-1 text-xs">
            <div class="flex items-center justify-between">
              <span class="font-black uppercase tracking-wider text-emerald-900 dark:text-emerald-300 text-[10px] flex items-center gap-1.5">
                <i class="fas fa-star text-amber-500"></i> Customer Service Rating &amp; Review
              </span>
              <span class="font-bold text-emerald-800 text-[11px]">
                {{ $job->review->rating }} / 5 Stars
              </span>
            </div>
            @if ($job->review->comment)
              <p class="text-emerald-950 dark:text-emerald-200 leading-relaxed font-medium mt-1">
                "{{ $job->review->comment }}"
              </p>
            @endif
          </div>
        @endif

      </div>
    @endforeach
  @endif

</div>
