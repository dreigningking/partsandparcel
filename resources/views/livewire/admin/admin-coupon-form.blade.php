<x-admin.page :title="$heading" description="Define discount rules and publication status.">
    @if (session('status'))
        <div class="mb-4 rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800">{{ session('status') }}</div>
    @endif

    <form wire:submit="save" class="max-w-2xl space-y-4">
        <div>
            <label class="block text-sm font-medium text-gray-700">Name</label>
            <input type="text" wire:model="name" class="mt-1 w-full rounded-lg border border-gray-200 px-3 py-2 text-sm" />
            @error('name') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700">Code</label>
            <input type="text" wire:model.blur="code" class="mt-1 w-full rounded-lg border border-gray-200 px-3 py-2 font-mono text-sm uppercase" />
            @error('code') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
        </div>
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
            <div>
                <label class="block text-sm font-medium text-gray-700">Total quantity</label>
                <input type="number" wire:model="quantity" min="1" class="mt-1 w-full rounded-lg border border-gray-200 px-3 py-2 text-sm" />
                @error('quantity') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
            </div>
            <div class="flex items-end gap-3 pb-1">
                <label class="inline-flex items-center gap-2 text-sm text-gray-700">
                    <input type="checkbox" wire:model="is_published" class="rounded border-gray-300" />
                    Published
                </label>
            </div>
        </div>
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
            <div>
                <label class="block text-sm font-medium text-gray-700">Limit per user</label>
                <input type="number" wire:model="limit_per_user" class="mt-1 w-full rounded-lg border border-gray-200 px-3 py-2 text-sm" />
                @error('limit_per_user') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
            </div>
            
        </div>
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
            <div>
                <label class="block text-sm font-medium text-gray-700">Start at</label>
                <input type="datetime-local" wire:model="start_at" class="mt-1 w-full rounded-lg border border-gray-200 px-3 py-2 text-sm" />
                @error('start_at') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700">Expires at</label>
                <input type="datetime-local" wire:model="expires_at" class="mt-1 w-full rounded-lg border border-gray-200 px-3 py-2 text-sm" />
                @error('expires_at') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
            </div>
        </div>
        
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
            <div>
                <label class="block text-sm font-medium text-gray-700">Percent</label>
                <input type="text" wire:model="discount" class="mt-1 w-full rounded-lg border border-gray-200 px-3 py-2 text-sm" />
                @error('discount') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
            </div>
            
        </div>
        <div class="flex flex-wrap gap-3 pt-2">
            <button type="submit" class="rounded-lg bg-emerald-600 px-4 py-2 text-sm font-medium text-white hover:bg-emerald-700">{{ $submitLabel }}</button>
            <a href="{{ route('admin.coupons') }}" class="rounded-lg border border-gray-200 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50">Back to list</a>
        </div>
    </form>
</x-admin.page>
