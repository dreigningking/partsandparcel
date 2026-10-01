<x-admin.page title="User details" description="Profile, account controls, and related resources.">
    @if (session('status'))
        <div class="mb-4 rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-2 text-sm text-emerald-700">{{ session('status') }}</div>
    @endif

    <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
        <div class="rounded-lg border border-gray-200 p-4">
            <h3 class="mb-2 font-semibold text-gray-900">Profile</h3>
            <p class="text-sm text-gray-600"><span class="font-medium">Name:</span> {{ $user->name }}</p>
            <p class="text-sm text-gray-600"><span class="font-medium">Email:</span> {{ $user->email }}</p>
            <p class="text-sm text-gray-600"><span class="font-medium">Phone:</span> {{ $user->phone ?: '—' }}</p>
            <p class="text-sm text-gray-600"><span class="font-medium">Country:</span> {{ $user->country?->name ?? '—' }}</p>
            <p class="text-sm text-gray-600"><span class="font-medium">Verified:</span> {{ $user->verified_at ? 'Yes' : 'No' }}</p>
        </div>
        <div class="rounded-lg border border-gray-200 p-4">
            <h3 class="mb-2 font-semibold text-gray-900">Activity summary</h3>
            <p class="text-sm text-gray-600">Listings: {{ $user->listings_count }}</p>
            <p class="text-sm text-gray-600">Subscriptions: {{ $user->subscriptions_count }}</p>
            <p class="text-sm text-gray-600">Status: {{ $user->suspended_at ? 'Suspended' : 'Active' }}</p>
        </div>
    </div>

    <div class="mt-6 flex flex-wrap gap-3">
        @if(auth()->id() !== $user->id && !$user->verified_at)
            <button wire:click="approve" type="button" class="rounded-lg bg-emerald-600 px-4 py-2 text-sm font-medium text-white">Verify user</button>
            <div class="w-full sm:w-auto">
                <input type="text" wire:model="rejectReason" placeholder="Rejection reason" class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm" />
                <button wire:click="reject" type="button" class="mt-1 sm:mt-2 rounded-lg bg-red-600 px-4 py-2 text-sm font-medium text-white">Reject</button>
            </div>
        @endif
        @if(auth()->id() !== $user->id)
            @if (!$user->suspended_at)
                <button wire:click="suspend" type="button" class="rounded-lg bg-amber-600 px-4 py-2 text-sm font-medium text-white">Suspend</button>
            @else
                <button wire:click="unsuspend" type="button" class="rounded-lg bg-emerald-600 px-4 py-2 text-sm font-medium text-white">Unsuspend</button>
            @endif
            <button wire:click="delete" wire:confirm="Delete this user?" type="button" class="rounded-lg bg-red-600 px-4 py-2 text-sm font-medium text-white">Delete</button>
        @endif
        <a href="{{ route('admin.listings', ['q' => $user->name]) }}" class="rounded-lg border border-gray-300 px-4 py-2 text-sm font-medium text-gray-700">View Listings</a>
        <a href="{{ route('admin.subscriptions', ['q' => $user->name]) }}" class="rounded-lg border border-gray-300 px-4 py-2 text-sm font-medium text-gray-700">View Subscriptions</a>
    </div>

    <a href="{{ route('admin.users') }}" class="mt-6 inline-flex text-sm font-medium text-emerald-600 hover:text-emerald-700">← Back to users</a>
</x-admin.page>