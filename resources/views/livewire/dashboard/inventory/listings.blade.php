<div class="flex flex-col gap-6">

  <!-- HEADER -->
  <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b border-slate-200 pb-4">
    <div>
      <div class="flex items-center gap-2 mb-1">
        <span class="text-xs font-extrabold text-pp-600 uppercase tracking-wider">Public Marketplace Listings</span>
      </div>
      <h1 class="text-2xl font-extrabold text-slate-950">Marketplace Listings Management</h1>
      <p class="text-xs text-slate-500 mt-0.5">Monitor and manage public listings, stock allocations, unit pricing, and status controls.</p>
    </div>

    <div class="flex items-center gap-2">
      <button type="button" wire:click="openCreateListingModal()" class="px-5 py-2.5 rounded-xl bg-pp-600 hover:bg-pp-700 text-white font-extrabold text-xs shadow-xs transition flex items-center gap-1.5 cursor-pointer">
        <i class="fas fa-plus"></i> Create New Listing
      </button>
    </div>
  </div>

  <!-- FLASH MESSAGES -->
  @if (session()->has('message'))
    <div class="p-4 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-900 font-bold text-xs flex items-center justify-between shadow-2xs">
      <div class="flex items-center gap-2">
        <i class="fas fa-check-circle text-emerald-600 text-base"></i>
        <span>{{ session('message') }}</span>
      </div>
      <button onclick="this.parentElement.remove()" class="text-emerald-700 hover:text-emerald-900 cursor-pointer"><i class="fas fa-times"></i></button>
    </div>
  @endif

  @if (session()->has('error'))
    <div class="p-4 rounded-2xl bg-rose-50 border border-rose-200 text-rose-900 font-bold text-xs flex items-center justify-between shadow-2xs">
      <div class="flex items-center gap-2">
        <i class="fas fa-exclamation-triangle text-rose-600 text-base"></i>
        <span>{{ session('error') }}</span>
      </div>
      <button onclick="this.parentElement.remove()" class="text-rose-700 hover:text-rose-900 cursor-pointer"><i class="fas fa-times"></i></button>
    </div>
  @endif

  <!-- FILTER CONTROLS BAR -->
  <div class="bg-white rounded-2xl border border-slate-200 p-4 shadow-xs space-y-3">
    <div class="flex items-center justify-between flex-wrap gap-2">
      <h3 class="text-xs font-extrabold text-slate-900 uppercase tracking-wider flex items-center gap-2">
        <i class="fas fa-filter text-pp-600"></i> Filter Marketplace Listings
      </h3>

      @if($search || $selectedCategory || $selectedStatus || $priceSort)
        <button type="button" wire:click="resetFilters" class="text-xs font-bold text-rose-600 hover:underline flex items-center gap-1 cursor-pointer">
          <i class="fas fa-undo text-[10px]"></i> Reset Filters
        </button>
      @endif
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3 text-xs">
      
      <!-- 1. SEARCH ITEM (INPUT) -->
      <div>
        <label class="block font-bold text-slate-700 mb-1">Search Item</label>
        <div class="relative">
          <i class="fas fa-search absolute left-3 top-3 text-slate-400 text-xs"></i>
          <input type="text" wire:model.live.debounce.300ms="search" placeholder="Search title, model..." class="w-full pl-8 p-2.5 rounded-xl border border-slate-200 text-xs text-slate-900 outline-none focus:border-pp-500 bg-slate-50/50 transition" />
        </div>
      </div>

      <!-- 2. CATEGORY (DROPDOWN) -->
      <div>
        <label class="block font-bold text-slate-700 mb-1">Category</label>
        <select wire:model.live="selectedCategory" class="w-full p-2.5 rounded-xl border border-slate-200 text-xs font-semibold text-slate-800 outline-none focus:border-pp-500 bg-slate-50/50 transition">
          <option value="">All Categories</option>
          @foreach($categories as $cat)
            <option value="{{ $cat->id }}">{{ $cat->name }}</option>
          @endforeach
        </select>
      </div>

      <!-- 3. STATUS (DROPDOWN: Live | Inactive | Draft | Rejected | Sold Out) -->
      <div>
        <label class="block font-bold text-slate-700 mb-1">Status</label>
        <select wire:model.live="selectedStatus" class="w-full p-2.5 rounded-xl border border-slate-200 text-xs font-semibold text-slate-800 outline-none focus:border-pp-500 bg-slate-50/50 transition">
          <option value="">All Statuses</option>
          <option value="live">Live (Active)</option>
          <option value="inactive">Inactive</option>
          <option value="draft">Draft</option>
          <option value="rejected">Rejected</option>
          <option value="sold_out">Sold Out</option>
        </select>
      </div>

      <!-- 4. PRICE (HIGH TO LOW, LOW TO HIGH) -->
      <div>
        <label class="block font-bold text-slate-700 mb-1">Sort by Price</label>
        <select wire:model.live="priceSort" class="w-full p-2.5 rounded-xl border border-slate-200 text-xs font-semibold text-slate-800 outline-none focus:border-pp-500 bg-slate-50/50 transition">
          <option value="">Latest Created</option>
          <option value="high_low">Price: High to Low</option>
          <option value="low_high">Price: Low to High</option>
        </select>
      </div>

    </div>
  </div>

  <!-- LISTINGS TABLE CONTAINER -->
  <div class="bg-white rounded-3xl border border-slate-200 p-6 space-y-4 shadow-soft">
    
    <div class="flex items-center justify-between border-b border-slate-100 pb-3 flex-wrap gap-2">
      <h3 class="text-xs font-extrabold text-slate-900 uppercase tracking-wider flex items-center gap-2">
        <i class="fas fa-store text-pp-600"></i> Active Public Listings ({{ $listings->total() }})
      </h3>
      <span class="text-xs text-slate-400 font-semibold">Page {{ $listings->currentPage() }} of {{ $listings->lastPage() ?: 1 }}</span>
    </div>

    <!-- RESPONSIVE TABLE WRAPPER -->
    <div class="overflow-x-auto border border-slate-200 rounded-2xl bg-white shadow-xs">
      <table class="w-full text-left text-xs border-collapse min-w-[700px]">
        <thead>
          <tr class="bg-slate-50 border-b border-slate-200 text-slate-700 font-extrabold uppercase text-[10px] tracking-wider">
            <th class="p-3.5 min-w-[220px]">Item</th>
            <th class="p-3.5 w-28">Stock</th>
            <th class="p-3.5 text-right w-32">Unit Price</th>
            <th class="p-3.5 text-center w-28">Sales Count</th>
            <th class="p-3.5 w-28">Status</th>
            <th class="p-3.5 text-center w-36">Actions</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-slate-100 font-medium text-slate-700">
          @forelse($listings as $lst)
            @php
              $asset = $lst->assetable;
              $isComponent = ($lst->assetable_type === 'App\Models\Component');
              
              if ($isComponent) {
                  $itemTitle = $asset?->name ?? 'Harvested Component';
                  $categoryName = $asset?->item?->deviceModel?->category?->name ?? 'Component';
                  $modelName = $asset?->item?->deviceModel?->name ?? '—';
              } else {
                  $itemTitle = $asset?->name ?: ($asset?->deviceModel?->name ?? 'Device Item');
                  $categoryName = $asset?->deviceModel?->category?->name ?? 'Device';
                  $modelName = $asset?->deviceModel?->name ?? '—';
              }

              // Determine exact listing status badge
              $status = $lst->status;
              if ($lst->quantity <= 0 && $status === 'active') {
                  $status = 'sold_out';
              }
            @endphp

            <tr class="hover:bg-slate-50/80 transition">
              
              <!-- 1. ITEM -->
              <td class="p-3.5">
                <div class="flex flex-col gap-1">
                  <div class="flex items-center gap-2">
                    <span class="px-1.5 py-0.5 rounded text-[9px] font-black uppercase {{ $isComponent ? 'bg-amber-100 text-amber-900' : 'bg-pp-100 text-pp-900' }}">
                      {{ $isComponent ? 'Component' : 'Whole Unit' }}
                    </span>
                    <span class="font-extrabold text-slate-950 text-xs line-clamp-1">
                      {{ $itemTitle }}
                    </span>
                  </div>
                  <div class="text-[10px] text-slate-500 flex items-center gap-2">
                    <span>Category: <strong>{{ $categoryName }}</strong></span>
                    <span>·</span>
                    <span>Model: <strong>{{ $modelName }}</strong></span>
                  </div>
                </div>
              </td>

              <!-- 2. STOCK -->
              <td class="p-3.5">
                @if($lst->quantity > 0)
                  <span class="font-extrabold text-emerald-600 text-xs block">{{ $lst->quantity }} Available</span>
                @else
                  <span class="font-bold text-rose-600 text-xs block">0 Available</span>
                @endif
                @if($lst->reserved_quantity > 0)
                  <span class="text-[10px] text-amber-700 font-semibold block">({{ $lst->reserved_quantity }} in carts)</span>
                @endif
              </td>

              <!-- 3. UNIT PRICE -->
              <td class="p-3.5 text-right">
                <span class="font-extrabold text-slate-950 text-xs block">₦{{ number_format($lst->price, 2) }}</span>
              </td>

              <!-- 4. SALES COUNT -->
              <td class="p-3.5 text-center">
                <span class="font-bold text-slate-700 text-xs">{{ $lst->sold_quantity ?? 0 }} Sold</span>
              </td>

              <!-- 5. STATUS -->
              <td class="p-3.5">
                @if($status === 'active' || $status === 'live')
                  <span class="px-2.5 py-1 rounded-full bg-emerald-100 text-emerald-800 font-extrabold text-[10px] inline-flex items-center gap-1">
                    🟢 Live
                  </span>
                @elseif($status === 'sold_out')
                  <span class="px-2.5 py-1 rounded-full bg-purple-100 text-purple-800 font-extrabold text-[10px] inline-flex items-center gap-1">
                    🟣 Sold Out
                  </span>
                @elseif($status === 'draft')
                  <span class="px-2.5 py-1 rounded-full bg-amber-100 text-amber-900 font-extrabold text-[10px] inline-flex items-center gap-1">
                    🟡 Draft
                  </span>
                @elseif($status === 'rejected')
                  <span class="px-2.5 py-1 rounded-full bg-rose-100 text-rose-800 font-extrabold text-[10px] inline-flex items-center gap-1">
                    🔴 Rejected
                  </span>
                @else
                  <span class="px-2.5 py-1 rounded-full bg-slate-100 text-slate-600 font-bold text-[10px] inline-flex items-center gap-1">
                    ⚪ Inactive
                  </span>
                @endif
              </td>

              <!-- 6. ACTIONS (EDIT, DELETE) -->
              <td class="p-3.5 text-center">
                <div class="flex items-center justify-center gap-1.5">
                  <button type="button" wire:click="editListing({{ $lst->id }})" class="px-2.5 py-1 rounded-lg border border-slate-200 hover:bg-slate-50 font-bold text-slate-700 text-[10px] transition cursor-pointer" title="Edit Listing">
                    <i class="fas fa-edit mr-0.5"></i> Edit
                  </button>

                  <button type="button" wire:click="deleteListing({{ $lst->id }})" wire:confirm="Are you sure you want to delete this marketplace listing?" class="px-2.5 py-1 rounded-lg border border-rose-200 bg-rose-50 hover:bg-rose-100 text-rose-700 font-extrabold text-[10px] transition cursor-pointer" title="Delete Listing">
                    <i class="fas fa-trash-alt mr-0.5"></i> Delete
                  </button>
                </div>
              </td>

            </tr>
          @empty
            <tr>
              <td colspan="6" class="p-8 text-center text-slate-400 font-semibold">
                <div class="max-w-xs mx-auto space-y-2">
                  <i class="fas fa-store-slash text-3xl text-slate-300 block"></i>
                  <p class="text-xs font-bold text-slate-600">No marketplace listings match your search filters.</p>
                  <button type="button" wire:click="resetFilters" class="text-xs font-bold text-pp-600 hover:underline cursor-pointer">
                    Clear all filters
                  </button>
                </div>
              </td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>

    <!-- PAGINATION LINKS -->
    @if($listings->hasPages())
      <div class="pt-3 border-t border-slate-100">
        {{ $listings->links() }}
      </div>
    @endif

  </div>

  <!-- CREATE NEW LISTING MODAL DIALOG -->
  @if($showCreateListingModal)
    <div class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/50 backdrop-blur-xs p-4">
      <div class="bg-white rounded-3xl border border-slate-200 shadow-2xl max-w-lg w-full p-6 space-y-5 relative">
        <div class="flex items-center justify-between border-b border-slate-100 pb-3">
          <div class="flex items-center gap-2">
            <div class="w-8 h-8 rounded-xl bg-pp-50 text-pp-600 grid place-items-center text-sm font-black">
              <i class="fas fa-tag"></i>
            </div>
            <div>
              <h3 class="text-sm font-extrabold text-slate-950">Create New Marketplace Listing</h3>
              <p class="text-[11px] text-slate-500">Select an unlisted item or component to list for sale</p>
            </div>
          </div>
          <button type="button" wire:click="closeCreateListingModal" class="w-8 h-8 rounded-full bg-slate-100 hover:bg-slate-200 text-slate-500 grid place-items-center transition cursor-pointer">
            <i class="fas fa-times text-xs"></i>
          </button>
        </div>

        <div class="space-y-4 text-xs">
          
          <!-- 1. SEARCHABLE DROPDOWN SELECT FOR ITEM / COMPONENT -->
          <div>
            <label class="block font-bold text-slate-700 mb-1">Select Item / Component to List <span class="text-rose-500">*</span></label>
            <select wire:model.live="selectedAssetKey" class="w-full p-3 rounded-xl border border-slate-200 text-xs font-extrabold text-slate-900 outline-none focus:border-pp-500 bg-white transition">
              <option value="">-- Choose Unlisted Item or Component --</option>
              @if(!empty($unlistedAssets['items']) && count($unlistedAssets['items']) > 0)
                <optgroup label="📦 Complete Devices / Items">
                  @foreach($unlistedAssets['items'] as $uItem)
                    <option value="item_{{ $uItem['id'] }}">{{ $uItem['name'] }} ({{ ucfirst($uItem['condition']) }})</option>
                  @endforeach
                </optgroup>
              @endif
              @if(!empty($unlistedAssets['components']) && count($unlistedAssets['components']) > 0)
                <optgroup label="🧩 Harvested Sub-Components">
                  @foreach($unlistedAssets['components'] as $uComp)
                    <option value="component_{{ $uComp['id'] }}">{{ $uComp['name'] }} (Component of {{ $uComp['parent_item'] }})</option>
                  @endforeach
                </optgroup>
              @endif
            </select>
            @error('selectedAssetKey') <span class="text-[10px] text-rose-600 font-bold block mt-1">{{ $message }}</span> @enderror
          </div>

          <!-- 2. PRICE & QUANTITY -->
          <div class="grid grid-cols-2 gap-3">
            <div>
              <label class="block font-bold text-slate-700 mb-1">Listing Price (₦) <span class="text-rose-500">*</span></label>
              <div class="relative">
                <span class="absolute left-3 top-2.5 text-slate-400 font-bold text-xs">₦</span>
                <input type="number" wire:model="price" placeholder="0.00" class="w-full pl-8 p-2.5 rounded-xl border border-slate-200 text-xs font-extrabold text-slate-900 outline-none focus:border-pp-500 transition" />
              </div>
              @error('price') <span class="text-[10px] text-rose-600 font-bold block mt-1">{{ $message }}</span> @enderror
            </div>

            <div>
              <label class="block font-bold text-slate-700 mb-1">Quantity <span class="text-rose-500">*</span></label>
              <input type="number" wire:model="quantity" min="1" {{ $isQuantityDisabled ? 'disabled' : '' }} class="w-full p-2.5 rounded-xl border border-slate-200 text-xs font-extrabold outline-none focus:border-pp-500 transition {{ $isQuantityDisabled ? 'bg-slate-100 text-slate-500 cursor-not-allowed' : 'bg-white text-slate-900' }}" />
              @if($isQuantityDisabled)
                <span class="text-[9px] text-slate-500 font-semibold block mt-0.5">Quantity defaults to 1 for components.</span>
              @endif
              @error('quantity') <span class="text-[10px] text-rose-600 font-bold block mt-1">{{ $message }}</span> @enderror
            </div>
          </div>

          <!-- 3. WARRANTY PERIOD & TERMS -->
          <div class="grid grid-cols-2 gap-3">
            <div>
              <label class="block font-bold text-slate-700 mb-1">Warranty Period (Days)</label>
              <input type="number" wire:model="warranty_period_days" placeholder="0" class="w-full p-2.5 rounded-xl border border-slate-200 text-xs font-bold text-slate-900 outline-none focus:border-pp-500 transition" />
            </div>

            <div>
              <label class="block font-bold text-slate-700 mb-1">Warranty Terms</label>
              <input type="text" wire:model="warranty_terms" placeholder="e.g. 7-day inspection warranty" class="w-full p-2.5 rounded-xl border border-slate-200 text-xs text-slate-900 outline-none focus:border-pp-500 transition" />
            </div>
          </div>

        </div>

        <div class="border-t border-slate-100 pt-4 flex items-center justify-end gap-2.5">
          <button type="button" wire:click="closeCreateListingModal" class="px-4 py-2 rounded-xl border border-slate-300 hover:bg-slate-50 text-xs font-bold text-slate-700 transition cursor-pointer">
            Cancel
          </button>
          <button type="button" wire:click="createListing" class="px-5 py-2 rounded-xl bg-pp-600 hover:bg-pp-700 text-white text-xs font-extrabold shadow-xs transition cursor-pointer flex items-center gap-1.5">
            <i class="fas fa-check text-[10px]"></i> Publish Listing
          </button>
        </div>
      </div>
    </div>
  @endif

  <!-- QUICK EDIT LISTING MODAL DIALOG -->
  @if($showEditModal)
    <div class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/50 backdrop-blur-xs p-4">
      <div class="bg-white rounded-3xl border border-slate-200 shadow-2xl max-w-md w-full p-6 space-y-5 relative">
        <div class="flex items-center justify-between border-b border-slate-100 pb-3">
          <div class="flex items-center gap-2">
            <div class="w-8 h-8 rounded-xl bg-pp-50 text-pp-600 grid place-items-center text-sm font-black">
              <i class="fas fa-edit"></i>
            </div>
            <div>
              <h3 class="text-sm font-extrabold text-slate-950">Edit Marketplace Listing</h3>
              <p class="text-[11px] text-slate-500">Update price, stock, and listing status</p>
            </div>
          </div>
          <button type="button" wire:click="closeEditModal" class="w-8 h-8 rounded-full bg-slate-100 hover:bg-slate-200 text-slate-500 grid place-items-center transition cursor-pointer">
            <i class="fas fa-times text-xs"></i>
          </button>
        </div>

        <div class="space-y-4 text-xs">
          <div>
            <label class="block font-bold text-slate-700 mb-1">Listing Price (₦) <span class="text-rose-500">*</span></label>
            <div class="relative">
              <span class="absolute left-3 top-2.5 text-slate-400 font-bold text-xs">₦</span>
              <input type="number" wire:model="editPrice" placeholder="0.00" class="w-full pl-8 p-2.5 rounded-xl border border-slate-200 text-xs font-extrabold text-slate-900 outline-none focus:border-pp-500 transition" />
            </div>
            @error('editPrice') <span class="text-[10px] text-rose-600 font-bold block mt-1">{{ $message }}</span> @enderror
          </div>

          <div>
            <label class="block font-bold text-slate-700 mb-1">Available Stock Quantity <span class="text-rose-500">*</span></label>
            <input type="number" wire:model="editQuantity" min="0" placeholder="1" class="w-full p-2.5 rounded-xl border border-slate-200 text-xs font-bold text-slate-900 outline-none focus:border-pp-500 transition" />
            @error('editQuantity') <span class="text-[10px] text-rose-600 font-bold block mt-1">{{ $message }}</span> @enderror
          </div>

          <div>
            <label class="block font-bold text-slate-700 mb-1">Listing Status <span class="text-rose-500">*</span></label>
            <select wire:model="editStatus" class="w-full p-2.5 rounded-xl border border-slate-200 text-xs font-semibold text-slate-800 outline-none focus:border-pp-500 transition">
              <option value="active">Live (Active)</option>
              <option value="inactive">Inactive</option>
              <option value="draft">Draft</option>
              <option value="rejected">Rejected</option>
              <option value="sold_out">Sold Out</option>
            </select>
            @error('editStatus') <span class="text-[10px] text-rose-600 font-bold block mt-1">{{ $message }}</span> @enderror
          </div>
        </div>

        <div class="border-t border-slate-100 pt-4 flex items-center justify-end gap-2.5">
          <button type="button" wire:click="closeEditModal" class="px-4 py-2 rounded-xl border border-slate-300 hover:bg-slate-50 text-xs font-bold text-slate-700 transition cursor-pointer">
            Cancel
          </button>
          <button type="button" wire:click="updateListing" class="px-5 py-2 rounded-xl bg-pp-600 hover:bg-pp-700 text-white text-xs font-extrabold shadow-xs transition cursor-pointer flex items-center gap-1.5">
            <i class="fas fa-check text-[10px]"></i> Save Changes
          </button>
        </div>
      </div>
    </div>
  @endif

</div>