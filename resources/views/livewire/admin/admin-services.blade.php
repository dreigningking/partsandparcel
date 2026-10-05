<div class="space-y-6">
    <!-- TOP HEADER -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-white dark:bg-slate-900 p-6 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-soft">
        <div>
            <div class="flex items-center gap-2 text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-widest">
                <a href="{{ route('admin.dashboard') }}" class="hover:text-pp-600 transition">Admin Console</a>
                <span>›</span>
                <span class="text-pp-600 dark:text-pp-400">Marketplace</span>
            </div>
            <h1 class="text-2xl font-black text-slate-900 dark:text-white tracking-tight mt-1 flex items-center gap-2.5">
                <span>Services &amp; Repairs</span>
                <span class="text-xs px-2.5 py-0.5 rounded-full font-extrabold bg-pp-100 text-pp-700 dark:bg-pp-900/40 dark:text-pp-300">
                    {{ $totalCount }} Total Jobs
                </span>
            </h1>
            <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">
                Technician bookings, diagnosis jobs, part replacements, and repair warranty tracking.
            </p>
        </div>

        <div class="flex items-center gap-2">
            <span class="px-3 py-1.5 rounded-xl bg-blue-50 text-blue-700 font-extrabold text-xs border border-blue-200">
                In Progress: {{ $activeCount }}
            </span>
            <span class="px-3 py-1.5 rounded-xl bg-emerald-50 text-emerald-700 font-extrabold text-xs border border-emerald-200">
                Completed: {{ $completedCount }}
            </span>
        </div>
    </div>

    @if (session('status'))
        <div class="p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs font-bold flex items-center justify-between">
            <span>{{ session('status') }}</span>
        </div>
    @endif

    <!-- FILTER BAR -->
    <div class="bg-white dark:bg-slate-900 p-4 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-soft flex flex-col sm:flex-row gap-3 items-center justify-between">
        <div class="w-full sm:w-80">
            <input
                type="text"
                wire:model.live.debounce.300ms="search"
                placeholder="Search job title, customer, technician..."
                class="w-full px-3.5 py-2 rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-xs font-bold text-slate-900 dark:text-white placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-pp-500"
            >
        </div>

        <div class="flex items-center gap-2 w-full sm:w-auto">
            <select
                wire:model.live="status"
                class="px-3 py-2 rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-xs font-bold text-slate-700 dark:text-slate-300 focus:outline-none"
            >
                <option value="">All Statuses</option>
                <option value="pending">Pending</option>
                <option value="started">Started / In Progress</option>
                <option value="completed">Completed</option>
                <option value="cancelled">Cancelled</option>
            </select>
        </div>
    </div>

    <!-- TABLE -->
    <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-soft overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-slate-50 dark:bg-slate-800/60 text-slate-500 uppercase tracking-wider font-extrabold text-[10px] border-b border-slate-200 dark:border-slate-700">
                    <tr>
                        <th class="py-3 px-4">Job Title &amp; ID</th>
                        <th class="py-3 px-4">Customer</th>
                        <th class="py-3 px-4">Technician / Provider</th>
                        <th class="py-3 px-4">Device &amp; Model</th>
                        <th class="py-3 px-4">Status</th>
                        <th class="py-3 px-4">Warranty</th>
                        <th class="py-3 px-4 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800 font-medium">
                    @forelse($services as $job)
                        <tr class="hover:bg-slate-50/60 dark:hover:bg-slate-800/40 transition">
                            <td class="py-3.5 px-4">
                                <div class="font-extrabold text-slate-900 dark:text-white">
                                    {{ $job->title }}
                                </div>
                                <div class="text-[10px] text-slate-400 font-mono">
                                    #JOB-{{ $job->id }}
                                </div>
                            </td>
                            <td class="py-3.5 px-4 font-bold text-slate-800 dark:text-slate-200">
                                {{ $job->customer?->name ?: 'N/A' }}
                            </td>
                            <td class="py-3.5 px-4 font-bold text-slate-800 dark:text-slate-200">
                                {{ $job->provider?->name ?: 'Unassigned' }}
                            </td>
                            <td class="py-3.5 px-4 text-slate-600 dark:text-slate-400">
                                {{ $job->brand?->name }} {{ $job->deviceModel?->name ?: $job->external_item_description }}
                            </td>
                            <td class="py-3.5 px-4">
                                @php
                                    $jColor = match($job->status) {
                                        'completed' => 'bg-emerald-100 text-emerald-800 dark:bg-emerald-950/60 dark:text-emerald-300',
                                        'in_progress', 'started' => 'bg-blue-100 text-blue-800 dark:bg-blue-950/60 dark:text-blue-300',
                                        'cancelled' => 'bg-rose-100 text-rose-800 dark:bg-rose-950/60 dark:text-rose-300',
                                        default => 'bg-amber-100 text-amber-800 dark:bg-amber-950/60 dark:text-amber-300',
                                    };
                                @endphp
                                <span class="px-2.5 py-1 rounded-full text-[10px] font-extrabold uppercase tracking-wider {{ $jColor }}">
                                    {{ $job->status }}
                                </span>
                            </td>
                            <td class="py-3.5 px-4 text-slate-500 text-[11px]">
                                {{ $job->warranty_period_days ? "{$job->warranty_period_days} Days" : 'None' }}
                            </td>
                            <td class="py-3.5 px-4 text-right">
                                <button
                                    wire:click="showJob({{ $job->id }})"
                                    class="px-2.5 py-1 rounded-lg bg-pp-50 hover:bg-pp-100 text-pp-700 dark:bg-pp-950 dark:hover:bg-pp-900 text-xs font-bold transition cursor-pointer"
                                >
                                    Inspect
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-8 text-center text-slate-400">
                                No service jobs found matching criteria.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="p-4 border-t border-slate-100 dark:border-slate-800">
            {{ $services->links() }}
        </div>
    </div>

    <!-- DETAIL MODAL -->
    @if($selectedJob)
        <div class="fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4">
            <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-xl max-w-lg w-full p-6 space-y-4">
                <div class="flex items-center justify-between border-b border-slate-100 dark:border-slate-800 pb-3">
                    <h3 class="font-black text-slate-900 dark:text-white text-base">
                        Job #JOB-{{ $selectedJob->id }}: {{ $selectedJob->title }}
                    </h3>
                    <button wire:click="closeJob" class="text-slate-400 hover:text-slate-600 font-bold text-sm cursor-pointer">✕</button>
                </div>

                <div class="space-y-3 text-xs">
                    <div class="grid grid-cols-2 gap-3 p-3 rounded-xl bg-slate-50 dark:bg-slate-800/60">
                        <div>
                            <span class="text-slate-400 font-bold text-[10px] uppercase">Customer</span>
                            <p class="font-extrabold text-slate-800 dark:text-white">{{ $selectedJob->customer?->name }}</p>
                        </div>
                        <div>
                            <span class="text-slate-400 font-bold text-[10px] uppercase">Provider</span>
                            <p class="font-extrabold text-slate-800 dark:text-white">{{ $selectedJob->provider?->name ?: 'Unassigned' }}</p>
                        </div>
                        <div>
                            <span class="text-slate-400 font-bold text-[10px] uppercase">Status</span>
                            <p class="font-extrabold text-pp-600 uppercase">{{ $selectedJob->status }}</p>
                        </div>
                        <div>
                            <span class="text-slate-400 font-bold text-[10px] uppercase">Warranty</span>
                            <p class="font-bold text-slate-700 dark:text-slate-300">{{ $selectedJob->warranty_period_days ? "{$selectedJob->warranty_period_days} Days" : 'None' }}</p>
                        </div>
                    </div>

                    @if($selectedJob->description)
                        <div>
                            <span class="text-slate-400 font-bold text-[10px] uppercase">Description</span>
                            <p class="text-slate-700 dark:text-slate-300 mt-0.5 leading-relaxed">{{ $selectedJob->description }}</p>
                        </div>
                    @endif

                    <div class="flex items-center gap-2 pt-2 border-t border-slate-100 dark:border-slate-800">
                        <span class="text-[11px] font-bold text-slate-500">Update Status:</span>
                        <button wire:click="updateJobStatus({{ $selectedJob->id }}, 'started')" class="px-2.5 py-1 rounded-lg bg-blue-50 text-blue-700 hover:bg-blue-100 text-xs font-bold transition cursor-pointer">
                            Start Job
                        </button>
                        <button wire:click="updateJobStatus({{ $selectedJob->id }}, 'completed')" class="px-2.5 py-1 rounded-lg bg-emerald-50 text-emerald-700 hover:bg-emerald-100 text-xs font-bold transition cursor-pointer">
                            Complete
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
