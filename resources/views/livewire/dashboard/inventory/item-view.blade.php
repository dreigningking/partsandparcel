<div class="space-y-6">

  <!-- TOP NAV & HEADER -->
  <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
    <div>
      <div class="flex items-center gap-2 mb-1">
        <a href="{{ route('myitems') }}" class="text-xs font-bold text-pp-600 hover:underline flex items-center gap-1">
          <i class="fas fa-arrow-left text-[10px]"></i> Back to Inventory
        </a>
        <span class="text-slate-300">·</span>
        <span class="text-xs font-extrabold text-slate-500">Asset Ref: #ITM-{{ str_pad($item->id, 4, '0', STR_PAD_LEFT) }}</span>
      </div>
      <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-950 flex items-center gap-3 flex-wrap">
        <span>{{ $item->name ?: ($item->deviceModel?->name ?? "Inventory Asset #{$item->id}") }}</span>
        @if($item->item_type === 'scrap')
          <span class="px-2.5 py-0.5 rounded-full bg-amber-100 text-amber-900 font-extrabold text-[10px] uppercase">
            Scrap / Disassembly Asset
          </span>
        @elseif($item->item_type === 'part')
          <span class="px-2.5 py-0.5 rounded-full bg-indigo-100 text-indigo-900 font-extrabold text-[10px] uppercase">
            Component Part
          </span>
        @else
          <span class="px-2.5 py-0.5 rounded-full bg-pp-100 text-pp-900 font-extrabold text-[10px] uppercase">
            Whole Device Unit
          </span>
        @endif
      </h1>
      <p class="text-xs text-slate-500 mt-1">
        {{ $item->deviceModel?->brand?->name ?? 'Unspecified Brand' }} · {{ $item->deviceModel?->name ?? 'Unspecified Model' }} · Added {{ $item->created_at->format('M d, Y') }}
      </p>
    </div>

    <div class="flex items-center gap-2.5 self-start sm:self-auto">
      @if($item->item_type === 'scrap')
        <button
          type="button"
          wire:click="openAddComponentModal"
          class="px-4 py-2.5 rounded-xl bg-pp-600 hover:bg-pp-700 text-white font-extrabold text-xs shadow-xs transition flex items-center gap-2"
        >
          <i class="fas fa-plus text-xs"></i>
          <span>Add Harvested Part</span>
        </button>
      @endif

      @if($item->listing)
        <a
          href="{{ route('mylisting.view', $item->listing->id) }}"
          class="px-4 py-2.5 rounded-xl border border-slate-200 hover:bg-slate-50 text-slate-700 font-extrabold text-xs shadow-2xs transition flex items-center gap-2"
        >
          <i class="fas fa-store text-pp-600 text-xs"></i>
          <span>View Marketplace Listing</span>
        </a>
      @endif
    </div>
  </div>

  <!-- FLASH MESSAGES -->
  @if (session('message'))
    <div class="p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs font-bold flex items-center gap-2">
      <i class="fas fa-check-circle text-emerald-600 text-sm"></i>
      <span>{{ session('message') }}</span>
    </div>
  @endif

  <!-- SPECIFICATION & ASSET METRICS -->
  <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 sm:gap-4">
    <div class="bg-white rounded-2xl border border-slate-200 p-4 shadow-2xs">
      <span class="text-[10px] font-bold text-slate-400 uppercase block">Asset Condition</span>
      <span class="text-base font-black text-slate-900 capitalize mt-1 block">
        {{ str_replace('_', ' ', $item->condition_status ?? 'Used') }}
      </span>
      <span class="text-[11px] text-slate-500 mt-0.5 block truncate">{{ $item->condition_notes ?: 'Standard operational wear' }}</span>
    </div>

    <div class="bg-white rounded-2xl border border-slate-200 p-4 shadow-2xs">
      <span class="text-[10px] font-bold text-slate-400 uppercase block">Physical Location</span>
      <span class="text-base font-black text-slate-900 mt-1 block truncate">
        {{ $item->location?->city ?? 'Main Warehouse' }}
      </span>
      <span class="text-[11px] text-slate-500 mt-0.5 block truncate">{{ $item->location?->address_line_1 ?: ($item->location?->state->name ?? 'Nigeria') }}</span>
    </div>

    <div class="bg-white rounded-2xl border border-slate-200 p-4 shadow-2xs">
      <span class="text-[10px] font-bold text-slate-400 uppercase block">Associated Category</span>
      <span class="text-base font-black text-slate-900 mt-1 block truncate">
        {{ $item->deviceModel?->category?->name ?? 'General' }}
      </span>
      <span class="text-[11px] text-slate-500 mt-0.5 block">Marketplace Category</span>
    </div>

    <div class="bg-white rounded-2xl border border-slate-200 p-4 shadow-2xs">
      <span class="text-[10px] font-bold text-slate-400 uppercase block">Listing Status</span>
      @if($item->listing)
        <span class="text-base font-black text-emerald-600 mt-1 block">
          ₦{{ number_format((float) $item->listing->price, 2) }}
        </span>
        <span class="text-[11px] text-slate-500 mt-0.5 block">
          {{ $item->listing->is_published ? 'Published to buyers' : 'Draft / Unlisted' }}
        </span>
      @else
        <span class="text-base font-bold text-slate-500 mt-1 block">Unlisted</span>
        <span class="text-[11px] text-slate-400 mt-0.5 block">In private inventory</span>
      @endif
    </div>
  </div>

  <!-- MAIN ASSET DETAILS & DESCRIPTION -->
  <div class="bg-white rounded-2xl border border-slate-200 p-6 shadow-2xs space-y-4">
    <h3 class="text-xs font-extrabold text-slate-900 uppercase tracking-wider border-b border-slate-100 pb-3 flex items-center justify-between">
      <span>Asset Overview &amp; Specifications</span>
      @if($item->parent)
        <span class="text-[11px] text-indigo-600 lowercase font-bold">
          Harvested from: <a href="{{ route('item.view', $item->parent->id) }}" class="underline">{{ $item->parent->name ?: "Scrap #{$item->parent->id}" }}</a>
        </span>
      @endif
    </h3>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 text-xs">
      <div class="space-y-3">
        <div>
          <span class="font-bold text-slate-500 block">Item Description</span>
          <p class="mt-1 text-slate-700 leading-relaxed bg-slate-50 p-3.5 rounded-xl border border-slate-100">
            {{ $item->description ?: 'No detailed written description provided for this inventory record.' }}
          </p>
        </div>

        @if($item->condition_notes)
          <div>
            <span class="font-bold text-slate-500 block">Condition Notes / Technical Assessment</span>
            <p class="mt-1 text-amber-900 leading-relaxed bg-amber-50/60 p-3.5 rounded-xl border border-amber-100">
              {{ $item->condition_notes }}
            </p>
          </div>
        @endif
      </div>

      <!-- Media & Photos Gallery -->
      <div>
        <span class="font-bold text-slate-500 block mb-2">Item Media &amp; Inspection Photos</span>
        @if($item->media && $item->media->count() > 0)
          <div class="grid grid-cols-3 gap-2.5">
            @foreach($item->media as $media)
              <div class="aspect-square rounded-xl overflow-hidden border border-slate-200 bg-slate-50">
                <img src="{{ $media->file_url ?? $media->url }}" alt="Item Photo" class="w-full h-full object-cover">
              </div>
            @endforeach
          </div>
        @else
          <div class="p-6 rounded-xl bg-slate-50 border border-slate-200/80 text-center text-slate-400">
            <i class="fas fa-camera text-2xl mb-1 block"></i>
            <span>No inspection media attached to this asset record</span>
          </div>
        @endif
      </div>
    </div>
  </div>

  <!-- DISASSEMBLY MATRIX (FOR SCRAP UNITS) -->
  @if($item->item_type === 'scrap' || $item->children->count() > 0)
    <div class="bg-white rounded-2xl border border-slate-200 p-6 space-y-4 shadow-2xs">
      <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 border-b border-slate-100 pb-3">
        <div>
          <h3 class="text-xs font-extrabold text-slate-900 uppercase tracking-wider flex items-center gap-2">
            <i class="fas fa-microchip text-pp-600"></i> Disassembly &amp; Harvested Components Matrix
          </h3>
          <p class="text-[11px] text-slate-500 mt-0.5">
            Manage tested sub-components extracted from this scrap unit for individual marketplace sales.
          </p>
        </div>

        <button
          type="button"
          wire:click="openAddComponentModal"
          class="px-3 py-1.5 rounded-lg bg-pp-50 hover:bg-pp-100 text-pp-700 font-bold text-xs border border-pp-200 transition flex items-center gap-1.5 self-start sm:self-auto"
        >
          <i class="fas fa-plus text-[10px]"></i>
          <span>Add Component</span>
        </button>
      </div>

      <div class="overflow-x-auto">
        <table class="w-full text-left text-xs">
          <thead class="bg-slate-50 text-slate-500 uppercase font-extrabold border-b border-slate-200">
            <tr>
              <th class="p-3.5">Component Part Name</th>
              <th class="p-3.5">Condition Status</th>
              <th class="p-3.5">Marketplace Listing</th>
              <th class="p-3.5">Unit Price</th>
              <th class="p-3.5 text-right">Actions</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-slate-100 font-medium text-slate-700">
            @forelse($item->children as $child)
              <tr class="hover:bg-slate-50/60 transition">
                <td class="p-3.5">
                  <div class="font-bold text-slate-900">{{ $child->name }}</div>
                  @if($child->condition_notes)
                    <div class="text-[11px] text-slate-400 mt-0.5">{{ $child->condition_notes }}</div>
                  @endif
                </td>
                <td class="p-3.5">
                  <span class="px-2 py-0.5 rounded bg-emerald-100 text-emerald-800 text-[10px] font-extrabold">
                    {{ strtoupper($child->condition_status ?? 'TESTED WORKING') }}
                  </span>
                </td>
                <td class="p-3.5">
                  @if($child->listing)
                    <a href="{{ route('mylisting.view', $child->listing->id) }}" class="inline-flex items-center gap-1 text-pp-600 font-bold hover:underline">
                      <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                      <span>Listed (#LST-{{ $child->listing->id }})</span>
                    </a>
                  @else
                    <span class="text-slate-400 font-medium">Unlisted</span>
                  @endif
                </td>
                <td class="p-3.5 font-black text-slate-950">
                  @if($child->listing)
                    ₦{{ number_format((float) $child->listing->price, 2) }}
                  @else
                    —
                  @endif
                </td>
                <td class="p-3.5 text-right">
                  <div class="flex items-center justify-end gap-1.5">
                    @if($child->listing)
                      <a href="{{ route('mylisting.view', $child->listing->id) }}" class="p-1.5 text-slate-500 hover:text-pp-600 transition" title="View Listing Details">
                        <i class="fas fa-eye text-xs"></i>
                      </a>
                    @endif
                    <button
                      type="button"
                      wire:click="deleteComponent({{ $child->id }})"
                      wire:confirm="Are you sure you want to remove this harvested component and its listing?"
                      class="p-1.5 text-slate-400 hover:text-rose-600 transition"
                      title="Delete Component"
                    >
                      <i class="fas fa-trash-alt text-xs"></i>
                    </button>
                  </div>
                </td>
              </tr>
            @empty
              <tr>
                <td colspan="5" class="p-6 text-center text-slate-400">
                  No individual components have been harvested or logged from this scrap unit yet.
                </td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>
  @endif

  <!-- ADD HARVESTED COMPONENT MODAL -->
  @if($showAddComponentModal)
    <div class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
      <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <!-- Backdrop -->
        <div class="fixed inset-0 bg-slate-950/70 backdrop-blur-xs transition-opacity" wire:click="closeAddComponentModal"></div>
        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

        <!-- Dialog -->
        <div class="relative inline-block align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-md sm:w-full border border-slate-200">
          <div class="p-6 space-y-4">
            <div class="flex items-center justify-between border-b border-slate-100 pb-3">
              <h3 class="text-base font-bold text-slate-900" id="modal-title">
                Add Harvested Component Part
              </h3>
              <button type="button" wire:click="closeAddComponentModal" class="text-slate-400 hover:text-slate-600">
                <i class="fas fa-times"></i>
              </button>
            </div>

            <!-- Form Fields -->
            <div>
              <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Component Name <span class="text-rose-500">*</span></label>
              <input
                type="text"
                wire:model="newComponentName"
                placeholder="e.g. Logic Board, Display Screen, Fan Module"
                class="w-full px-3.5 py-2 rounded-xl border border-slate-200 text-xs focus:ring-2 focus:ring-pp-500/30 focus:outline-hidden"
              >
              @error('newComponentName') <span class="text-xs text-rose-500 font-medium">{{ $message }}</span> @enderror
            </div>

            <div>
              <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Testing / Condition Status</label>
              <select
                wire:model="newComponentCondition"
                class="w-full px-3.5 py-2 rounded-xl border border-slate-200 text-xs focus:ring-2 focus:ring-pp-500/30 focus:outline-hidden"
              >
                <option value="Testing working">Testing Working (Tested Functional)</option>
                <option value="Untested">Untested / As Harvested</option>
                <option value="Refurbished">Refurbished / Repaired</option>
                <option value="Faulty">Faulty (For ICs / Donor Chips)</option>
              </select>
            </div>

            <div>
              <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Technical Notes</label>
              <input
                type="text"
                wire:model="newComponentNotes"
                placeholder="e.g. Clean serial, boots to BIOS, no burn marks"
                class="w-full px-3.5 py-2 rounded-xl border border-slate-200 text-xs focus:ring-2 focus:ring-pp-500/30 focus:outline-hidden"
              >
            </div>

            <div class="pt-2 border-t border-slate-100">
              <label class="flex items-center gap-2 cursor-pointer mb-3">
                <input type="checkbox" wire:model.live="newComponentListNow" class="rounded text-pp-600 focus:ring-pp-500">
                <span class="text-xs font-bold text-slate-800">Publish immediately to Marketplace</span>
              </label>

              @if($newComponentListNow)
                <div>
                  <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Listing Price (₦) <span class="text-rose-500">*</span></label>
                  <input
                    type="number"
                    step="0.01"
                    wire:model="newComponentPrice"
                    placeholder="Enter selling price in Naira"
                    class="w-full px-3.5 py-2 rounded-xl border border-slate-200 text-xs focus:ring-2 focus:ring-pp-500/30 focus:outline-hidden"
                  >
                  @error('newComponentPrice') <span class="text-xs text-rose-500 font-medium">{{ $message }}</span> @enderror
                </div>
              @endif
            </div>
          </div>

          <div class="px-6 py-4 bg-slate-50 border-t border-slate-100 flex items-center justify-end gap-2.5">
            <button
              type="button"
              wire:click="closeAddComponentModal"
              class="px-4 py-2 rounded-xl border border-slate-200 text-slate-600 hover:bg-slate-100 text-xs font-bold transition"
            >
              Cancel
            </button>
            <button
              type="button"
              wire:click="saveComponent"
              class="px-4 py-2 rounded-xl bg-pp-600 hover:bg-pp-700 text-white text-xs font-bold shadow-xs transition"
            >
              Save Component
            </button>
          </div>
        </div>
      </div>
    </div>
  @endif

</div>