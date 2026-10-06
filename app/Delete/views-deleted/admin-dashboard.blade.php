<div>
    <main class="white-header max-w-7xl mx-auto px-4 mt-[90px] mb-8">
        @include('layouts.profile-header')

        <div class="grid grid-cols-1 lg:grid-cols-[300px_1fr] gap-8">
            @include('layouts.admin-sidebar')

            <div class="space-y-8">
                <div class="bg-white rounded-xl p-8 shadow-md">
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-8 pb-4 border-b border-gray-200">
                        <div>
                            <h2 class="text-2xl font-semibold text-gray-900">Admin overview</h2>
                            <p class="text-gray-500 text-sm mt-1">Monitor users, listings, subscriptions, and platform health in one place.</p>
                        </div>
                        <a href="{{ route('welcome') }}" class="inline-flex items-center justify-center gap-2 px-5 py-2.5 rounded-lg font-medium text-white bg-emerald-600 hover:bg-emerald-700 shadow-md transition whitespace-nowrap">
                            <i class="ri-external-link-line text-lg"></i>
                            View live site
                        </a>
                    </div>

                    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
                        <div class="bg-gray-50 rounded-lg p-4 text-center">
                            <div class="text-3xl font-bold text-emerald-600">2.4k</div>
                            <div class="text-sm text-gray-500">Registered users</div>
                        </div>
                        <div class="bg-gray-50 rounded-lg p-4 text-center">
                            <div class="text-3xl font-bold text-emerald-600">186</div>
                            <div class="text-sm text-gray-500">Active listings</div>
                        </div>
                        <div class="bg-gray-50 rounded-lg p-4 text-center">
                            <div class="text-3xl font-bold text-emerald-600">14</div>
                            <div class="text-sm text-gray-500">Pending moderation</div>
                        </div>
                        <div class="bg-gray-50 rounded-lg p-4 text-center">
                            <div class="text-3xl font-bold text-emerald-600">₦2.1M</div>
                            <div class="text-sm text-gray-500">Payments (30d)</div>
                        </div>
                    </div>

                    <h3 class="text-lg font-semibold mb-4 flex items-center gap-2">
                        <i class="ri-flashlight-line text-emerald-600"></i>
                        Quick actions
                    </h3>
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                        <a href="{{ route('admin.users') }}" class="flex items-center gap-3 p-4 border border-gray-200 rounded-xl hover:border-emerald-500 hover:bg-emerald-50/50 transition">
                            <span class="w-10 h-10 rounded-lg bg-emerald-100 text-emerald-700 flex items-center justify-center"><i class="ri-team-line text-xl"></i></span>
                            <div>
                                <div class="font-medium text-gray-900">Users</div>
                                <div class="text-xs text-gray-500">Accounts &amp; roles</div>
                            </div>
                        </a>
                        <a href="{{ route('admin.listings') }}" class="flex items-center gap-3 p-4 border border-gray-200 rounded-xl hover:border-emerald-500 hover:bg-emerald-50/50 transition">
                            <span class="w-10 h-10 rounded-lg bg-emerald-100 text-emerald-700 flex items-center justify-center"><i class="ri-building-line text-xl"></i></span>
                            <div>
                                <div class="font-medium text-gray-900">Listings</div>
                                <div class="text-xs text-gray-500">Listings &amp; reviews</div>
                            </div>
                        </a>
                        <a href="{{ route('admin.moderations') }}" class="flex items-center gap-3 p-4 border border-gray-200 rounded-xl hover:border-emerald-500 hover:bg-emerald-50/50 transition">
                            <span class="w-10 h-10 rounded-lg bg-emerald-100 text-emerald-700 flex items-center justify-center"><i class="ri-notification-line text-xl"></i></span>
                            <div>
                                <div class="font-medium text-gray-900">Moderations</div>
                                <div class="text-xs text-gray-500">System &amp; user alerts</div>
                            </div>
                        </a>
                        <a href="{{ route('admin.profile') }}" class="flex items-center gap-3 p-4 border border-gray-200 rounded-xl hover:border-emerald-500 hover:bg-emerald-50/50 transition">
                            <span class="w-10 h-10 rounded-lg bg-emerald-100 text-emerald-700 flex items-center justify-center"><i class="ri-user-settings-line text-xl"></i></span>
                            <div>
                                <div class="font-medium text-gray-900">Profile</div>
                                <div class="text-xs text-gray-500">Your admin account</div>
                            </div>
                        </a>
                    </div>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                    <div class="bg-white rounded-xl p-8 shadow-md">
                        <div class="flex items-center justify-between mb-6 pb-4 border-b border-gray-200">
                            <h3 class="text-lg font-semibold flex items-center gap-2">
                                <i class="ri-alarm-warning-line text-emerald-600"></i>
                                Queues needing attention
                            </h3>
                            <a href="{{ route('admin.listings') }}" class="text-sm font-medium text-emerald-600 hover:text-emerald-700">View all</a>
                        </div>
                        <ul class="space-y-4">
                            <li class="flex gap-4 items-start">
                                <span class="w-10 h-10 rounded-lg bg-amber-100 text-amber-700 flex items-center justify-center shrink-0"><i class="ri-home-4-line text-lg"></i></span>
                                <div class="min-w-0 flex-1">
                                    <p class="font-medium text-gray-900">3 listings awaiting approval</p>
                                    <p class="text-xs text-gray-500 mt-0.5">Submitted in the last 48 hours · Listings</p>
                                </div>
                            </li>
                            <li class="flex gap-4 items-start">
                                <span class="w-10 h-10 rounded-lg bg-blue-100 text-blue-700 flex items-center justify-center shrink-0"><i class="ri-calendar-check-line text-lg"></i></span>
                                <div class="min-w-0 flex-1">
                                    <p class="font-medium text-gray-900">5 moderation disputes</p>
                                    <p class="text-xs text-gray-500 mt-0.5">Buyer &amp; seller schedule conflicts · Inspections</p>
                                </div>
                            </li>
                            <li class="flex gap-4 items-start">
                                <span class="w-10 h-10 rounded-lg bg-violet-100 text-violet-700 flex items-center justify-center shrink-0"><i class="ri-bank-card-line text-lg"></i></span>
                                <div class="min-w-0 flex-1">
                                    <p class="font-medium text-gray-900">2 flagged payments</p>
                                    <p class="text-xs text-gray-500 mt-0.5">Manual review required · Payments</p>
                                </div>
                            </li>
                        </ul>
                    </div>

                    <div class="bg-gradient-to-br from-emerald-900 to-emerald-600 rounded-xl p-8 text-white shadow-md">
                        <h3 class="text-lg font-semibold mb-2 flex items-center gap-2">
                            <i class="ri-shield-check-line"></i>
                            Platform checklist
                        </h3>
                        <p class="text-emerald-100 text-sm mb-6 leading-relaxed">
                            Keep subscription plans, promotion rules, and coupon limits aligned so sellers and buyers always see consistent pricing at checkout.
                        </p>
                        <ul class="space-y-3 text-sm text-emerald-50">
                            <li class="flex gap-2"><i class="ri-checkbox-circle-fill text-emerald-300 shrink-0 mt-0.5"></i> Audit roles after staff changes under Settings → Roles &amp; Permissions.</li>
                            <li class="flex gap-2"><i class="ri-checkbox-circle-fill text-emerald-300 shrink-0 mt-0.5"></i> Reconcile payouts weekly from Payments against bank settlements.</li>
                            <li class="flex gap-2"><i class="ri-checkbox-circle-fill text-emerald-300 shrink-0 mt-0.5"></i> Publish or archive draft posts so the blog matches your public messaging.</li>
                        </ul>
                        <a href="{{ route('blog') }}" class="mt-6 inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-white/15 hover:bg-white/25 border border-white/30 text-sm font-medium transition">
                            <i class="ri-article-line"></i>
                            Open blog (public)
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </main>
</div>
