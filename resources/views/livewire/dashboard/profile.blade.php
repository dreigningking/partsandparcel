<div class="flex flex-col gap-6" wire:init="loadBanks">

  <!-- HEADER -->
  <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
    <div>
      <h1 class="text-2xl font-extrabold text-slate-950">Account &amp; Profile Settings</h1>
      <p class="text-xs text-slate-500 mt-0.5">Manage your personal credentials, store profile, push notifications, and payout bank account.</p>
    </div>

    <div class="flex flex-wrap items-center gap-2 self-start sm:self-auto">
      @if ($user->is_verified)
        <span class="px-3.5 py-1.5 rounded-full bg-emerald-100 text-emerald-800 text-xs font-extrabold flex items-center gap-1.5 shadow-2xs">
          <i class="fas fa-check-circle text-emerald-600"></i> VERIFIED ACCOUNT
        </span>
      @else
        <span class="px-3.5 py-1.5 rounded-full bg-slate-100 text-slate-600 text-xs font-bold flex items-center gap-1.5">
          <i class="fas fa-user-clock text-slate-400"></i> STANDARD ACCOUNT
        </span>
      @endif

      <a href="{{ route('user.profile', $user->id) }}" target="_blank" class="px-3.5 py-1.5 rounded-full bg-pp-50 hover:bg-pp-100 text-pp-700 border border-pp-200 text-xs font-extrabold flex items-center gap-1.5 transition shadow-2xs" title="View Public Profile">
        <i class="fas fa-external-link-alt text-pp-600"></i> Public Page ↗
      </a>
    </div>
  </div>

  <!-- SESSION NOTIFICATIONS -->
  @if (session()->has('profile_success'))
    <div class="p-4 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs font-bold flex items-center justify-between gap-2 shadow-2xs animate-in fade-in">
      <div class="flex items-center gap-2">
        <i class="fas fa-check-circle text-emerald-600 text-sm"></i>
        <span>{{ session('profile_success') }}</span>
      </div>
      <button type="button" onclick="this.parentElement.remove()" class="text-emerald-600 hover:text-emerald-800 cursor-pointer"><i class="fas fa-times"></i></button>
    </div>
  @endif

  @if (session()->has('password_success'))
    <div class="p-4 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs font-bold flex items-center justify-between gap-2 shadow-2xs animate-in fade-in">
      <div class="flex items-center gap-2">
        <i class="fas fa-shield-alt text-emerald-600 text-sm"></i>
        <span>{{ session('password_success') }}</span>
      </div>
      <button type="button" onclick="this.parentElement.remove()" class="text-emerald-600 hover:text-emerald-800 cursor-pointer"><i class="fas fa-times"></i></button>
    </div>
  @endif

  @if (session()->has('notification_success'))
    <div class="p-4 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs font-bold flex items-center justify-between gap-2 shadow-2xs animate-in fade-in">
      <div class="flex items-center gap-2">
        <i class="fas fa-bell text-emerald-600 text-sm"></i>
        <span>{{ session('notification_success') }}</span>
      </div>
      <button type="button" onclick="this.parentElement.remove()" class="text-emerald-600 hover:text-emerald-800 cursor-pointer"><i class="fas fa-times"></i></button>
    </div>
  @endif

  @if (session()->has('bank_success'))
    <div class="p-4 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs font-bold flex items-center justify-between gap-2 shadow-2xs animate-in fade-in">
      <div class="flex items-center gap-2">
        <i class="fas fa-university text-emerald-600 text-sm"></i>
        <span>{{ session('bank_success') }}</span>
      </div>
      <button type="button" onclick="this.parentElement.remove()" class="text-emerald-600 hover:text-emerald-800 cursor-pointer"><i class="fas fa-times"></i></button>
    </div>
  @endif

  @if (session()->has('device_success'))
    <div class="p-4 rounded-2xl bg-blue-50 border border-blue-200 text-blue-800 text-xs font-bold flex items-center justify-between gap-2 shadow-2xs animate-in fade-in">
      <div class="flex items-center gap-2">
        <i class="fas fa-mobile-alt text-blue-600 text-sm"></i>
        <span>{{ session('device_success') }}</span>
      </div>
      <button type="button" onclick="this.parentElement.remove()" class="text-blue-600 hover:text-blue-800 cursor-pointer"><i class="fas fa-times"></i></button>
    </div>
  @endif

  @if (session()->has('device_error'))
    <div class="p-4 rounded-2xl bg-amber-50 border border-amber-200 text-amber-900 text-xs font-bold flex items-center justify-between gap-2 shadow-2xs animate-in fade-in">
      <div class="flex items-center gap-2">
        <i class="fas fa-exclamation-triangle text-amber-600 text-sm"></i>
        <span>{{ session('device_error') }}</span>
      </div>
      <button type="button" onclick="this.parentElement.remove()" class="text-amber-600 hover:text-amber-800 cursor-pointer"><i class="fas fa-times"></i></button>
    </div>
  @endif

  <!-- HORIZONTAL SUB-NAVIGATION TABS -->
  <div class="flex items-center gap-2 border-b border-slate-200 overflow-x-auto pb-px">
    <button type="button" wire:click="setSection('profile')" class="px-4 py-2.5 text-xs font-extrabold flex items-center gap-2 whitespace-nowrap transition border-b-2 cursor-pointer {{ $activeSection === 'profile' ? 'border-pp-600 text-pp-700 bg-white rounded-t-xl' : 'border-transparent text-slate-500 hover:text-slate-900 hover:border-slate-300' }}">
      <i class="fas fa-user-circle {{ $activeSection === 'profile' ? 'text-pp-600' : 'text-slate-400' }}"></i>
      <span>Profile Details</span>
    </button>

    <button type="button" wire:click="setSection('security')" class="px-4 py-2.5 text-xs font-extrabold flex items-center gap-2 whitespace-nowrap transition border-b-2 cursor-pointer {{ $activeSection === 'security' ? 'border-pp-600 text-pp-700 bg-white rounded-t-xl' : 'border-transparent text-slate-500 hover:text-slate-900 hover:border-slate-300' }}">
      <i class="fas fa-shield-alt {{ $activeSection === 'security' ? 'text-pp-600' : 'text-slate-400' }}"></i>
      <span>Security &amp; Password</span>
    </button>

    <button type="button" wire:click="setSection('notifications')" class="px-4 py-2.5 text-xs font-extrabold flex items-center gap-2 whitespace-nowrap transition border-b-2 cursor-pointer {{ $activeSection === 'notifications' ? 'border-pp-600 text-pp-700 bg-white rounded-t-xl' : 'border-transparent text-slate-500 hover:text-slate-900 hover:border-slate-300' }}">
      <i class="fas fa-bell {{ $activeSection === 'notifications' ? 'text-pp-600' : 'text-slate-400' }}"></i>
      <span>Notifications &amp; Devices</span>
      @if($deviceTokens->count() > 0)
        <span class="px-1.5 py-0.2 rounded-full text-[10px] bg-pp-100 text-pp-800 font-black">{{ $deviceTokens->count() }}</span>
      @endif
    </button>

    <button type="button" wire:click="setSection('banking')" class="px-4 py-2.5 text-xs font-extrabold flex items-center gap-2 whitespace-nowrap transition border-b-2 cursor-pointer {{ $activeSection === 'banking' ? 'border-pp-600 text-pp-700 bg-white rounded-t-xl' : 'border-transparent text-slate-500 hover:text-slate-900 hover:border-slate-300' }}">
      <i class="fas fa-university {{ $activeSection === 'banking' ? 'text-pp-600' : 'text-slate-400' }}"></i>
      <span>Seller Payout Bank</span>
      @if($hasSavedBank)
        <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
      @endif
    </button>
  </div>

  <!-- SECTION 1: PROFILE DETAILS -->
  @if ($activeSection === 'profile')
    <div class="grid md:grid-cols-12 gap-6">
      
      <!-- LEFT: AVATAR & QUICK STATS -->
      <div class="md:col-span-4 space-y-6">
        <div class="bg-white rounded-3xl border border-slate-200 p-6 space-y-5 shadow-soft">
          <h3 class="text-xs font-extrabold text-slate-900 uppercase tracking-wider flex items-center gap-2 border-b border-slate-100 pb-3">
            <i class="fas fa-camera text-pp-600"></i> Profile Photo
          </h3>

          <div class="flex flex-col items-center text-center gap-3">
            <div class="relative w-28 h-28 rounded-3xl overflow-hidden bg-slate-100 border-2 border-slate-200 flex items-center justify-center shadow-inner group">
              @if ($avatarFile)
                <img src="{{ $avatarFile->temporaryUrl() }}" class="w-full h-full object-cover">
              @elseif ($currentAvatar)
                <img src="{{ asset('storage/' . $currentAvatar) }}" class="w-full h-full object-cover">
              @else
                @php
                  $initials = collect(explode(' ', $user->name))->map(fn($seg) => strtoupper(substr($seg, 0, 1)))->take(2)->join('');
                @endphp
                <span class="text-3xl font-black text-pp-600">{{ $initials ?: 'U' }}</span>
              @endif

              <div wire:loading wire:target="avatarFile" class="absolute inset-0 bg-slate-900/60 flex items-center justify-center text-white text-xs font-bold">
                <i class="fas fa-circle-notch fa-spin text-lg"></i>
              </div>
            </div>

            <div>
              <p class="font-extrabold text-sm text-slate-900">{{ $user->name }}</p>
              <p class="text-xs text-slate-500">{{ $user->business_name ?: 'Individual Merchant' }}</p>
              <span class="inline-block mt-1 text-[11px] font-bold text-slate-400">Member since {{ $user->created_at->format('M Y') }}</span>
            </div>

            <!-- UPLOAD ACTIONS -->
            <div class="w-full space-y-2 pt-2">
              <label class="w-full py-2.5 px-3 rounded-xl border border-pp-200 bg-pp-50 hover:bg-pp-100 text-pp-700 font-extrabold text-xs transition cursor-pointer flex items-center justify-center gap-1.5 shadow-2xs">
                <i class="fas fa-upload text-pp-600"></i>
                <span>Upload New Avatar</span>
                <input type="file" wire:model="avatarFile" accept="image/png,image/jpeg,image/webp" class="hidden">
              </label>

              @if ($currentAvatar || $avatarFile)
                <button type="button" wire:click="removeAvatar" class="w-full py-2 px-3 rounded-xl border border-slate-200 hover:bg-rose-50 hover:border-rose-200 text-rose-600 font-bold text-xs transition cursor-pointer flex items-center justify-center gap-1.5">
                  <i class="fas fa-trash-alt"></i>
                  <span>Remove Avatar</span>
                </button>
              @endif
              @error('avatarFile') <span class="text-red-600 text-[11px] font-semibold block">{{ $message }}</span> @enderror
              <p class="text-[10px] text-slate-400">Supported formats: JPG, PNG, WEBP. Max file size: 2MB.</p>
            </div>
          </div>

          <!-- VERIFICATION STATUS DETAILS -->
          <div class="pt-4 border-t border-slate-100 space-y-2.5 text-xs">
            <div class="flex items-center justify-between">
              <span class="text-slate-500 font-semibold">Email Status:</span>
              @if ($user->email_verified_at)
                <span class="text-emerald-700 font-extrabold text-[11px] flex items-center gap-1">
                  <i class="fas fa-check-circle"></i> Verified
                </span>
              @else
                <span class="text-amber-700 font-extrabold text-[11px] flex items-center gap-1">
                  <i class="fas fa-clock"></i> Unverified
                </span>
              @endif
            </div>

            <div class="flex items-center justify-between">
              <span class="text-slate-500 font-semibold">Seller Trust:</span>
              @if ($user->is_verified)
                <span class="text-emerald-700 font-extrabold text-[11px] flex items-center gap-1">
                  <i class="fas fa-badge-check"></i> Identity Verified
                </span>
              @else
                <span class="text-slate-500 font-extrabold text-[11px]">Standard Account</span>
              @endif
            </div>

            <div class="flex items-center justify-between">
              <span class="text-slate-500 font-semibold">Country:</span>
              <span class="font-bold text-slate-800">{{ $user->country?->name ?? 'Not Set' }}</span>
            </div>
          </div>
        </div>
      </div>

      <!-- RIGHT: PROFILE EDIT FORM -->
      <div class="md:col-span-8 space-y-6">
        <div class="bg-white rounded-3xl border border-slate-200 p-6 space-y-5 shadow-soft">
          <h3 class="text-xs font-extrabold text-slate-900 uppercase tracking-wider flex items-center gap-2 border-b border-slate-100 pb-3">
            <i class="fas fa-edit text-pp-600"></i> Personal &amp; Business Information
          </h3>

          <form wire:submit.prevent="saveProfile" class="space-y-4 text-xs">
            <div class="grid sm:grid-cols-2 gap-4">
              <!-- FULL NAME -->
              <div class="space-y-1">
                <label class="font-bold text-slate-700 block">Full Name <span class="text-rose-500">*</span></label>
                <input type="text" wire:model="name" class="w-full p-2.5 rounded-xl border border-slate-200 font-bold text-slate-900 outline-none focus:border-pp-500 transition" />
                @error('name') <span class="text-red-600 text-[11px] font-semibold block">{{ $message }}</span> @enderror
              </div>

              <!-- BUSINESS NAME -->
              <div class="space-y-1">
                <label class="font-bold text-slate-700 block">Store / Business Name</label>
                <input type="text" wire:model="business_name" placeholder="e.g. Acme Micro Systems" class="w-full p-2.5 rounded-xl border border-slate-200 font-bold text-slate-900 outline-none focus:border-pp-500 transition" />
                @error('business_name') <span class="text-red-600 text-[11px] font-semibold block">{{ $message }}</span> @enderror
              </div>

              <!-- EMAIL -->
              <div class="space-y-1">
                <label class="font-bold text-slate-700 flex items-center justify-between">
                  <span>Email Address <span class="text-rose-500">*</span></span>
                  @if ($user->email_verified_at)
                    <span class="text-[10px] text-emerald-600 font-extrabold flex items-center gap-0.5"><i class="fas fa-check"></i> Verified</span>
                  @endif
                </label>
                <input type="email" wire:model="email" class="w-full p-2.5 rounded-xl border border-slate-200 font-bold text-slate-900 outline-none focus:border-pp-500 transition" />
                @error('email') <span class="text-red-600 text-[11px] font-semibold block">{{ $message }}</span> @enderror
              </div>

              <!-- PHONE -->
              <div class="space-y-1">
                <label class="font-bold text-slate-700 block">Phone Number</label>
                <input type="text" wire:model="phone" placeholder="e.g. +234 803 123 4567" class="w-full p-2.5 rounded-xl border border-slate-200 font-bold text-slate-900 outline-none focus:border-pp-500 transition" />
                @error('phone') <span class="text-red-600 text-[11px] font-semibold block">{{ $message }}</span> @enderror
              </div>

              <!-- COUNTRY -->
              <div class="space-y-1">
                <label class="font-bold text-slate-700 block">Operating Country</label>
                <x-searchable-select
                  wire:model="country_id"
                  :options="$countries->map(fn($c) => ['value' => $c->id, 'label' => $c->name . ' (' . $c->currency . ')'])"
                  placeholder="-- Select Country --"
                  search-placeholder="Search countries..."
                />
                @error('country_id') <span class="text-red-600 text-[11px] font-semibold block">{{ $message }}</span> @enderror
              </div>

              <!-- THEME PREFERENCE -->
              <div class="space-y-1">
                <label class="font-bold text-slate-700 block">Theme Preference</label>
                <select wire:model="theme_preference" class="w-full p-2.5 rounded-xl border border-slate-200 font-bold text-slate-900 outline-none focus:border-pp-500 transition bg-white cursor-pointer">
                  <option value="system">System Default</option>
                  <option value="light">Light Mode</option>
                  <option value="dark">Dark Mode</option>
                </select>
                @error('theme_preference') <span class="text-red-600 text-[11px] font-semibold block">{{ $message }}</span> @enderror
              </div>
            </div>

            <!-- BIO -->
            <div class="space-y-1">
              <label class="font-bold text-slate-700 flex items-center justify-between">
                <span>About Me / Store Bio</span>
                <span class="text-[10px] text-slate-400">Visible on your public seller page</span>
              </label>
              <textarea wire:model="bio" rows="4" placeholder="Describe your store expertise, years in business, services provided, or component testing standards..." class="w-full p-3 rounded-xl border border-slate-200 font-medium text-slate-900 outline-none focus:border-pp-500 transition resize-none"></textarea>
              @error('bio') <span class="text-red-600 text-[11px] font-semibold block">{{ $message }}</span> @enderror
            </div>

            <div class="pt-3 border-t border-slate-100 flex items-center justify-end">
              <button type="submit" class="px-5 py-2.5 rounded-xl bg-pp-600 hover:bg-pp-700 text-white font-extrabold text-xs shadow-xs transition cursor-pointer flex items-center gap-1.5">
                <i class="fas fa-save"></i> Save Profile Details
              </button>
            </div>
          </form>
        </div>
      </div>

    </div>
  @endif

  <!-- SECTION 2: SECURITY & PASSWORD -->
  @if ($activeSection === 'security')
    <div class="grid md:grid-cols-12 gap-6">
      <div class="md:col-span-8 space-y-6">
        <div class="bg-white rounded-3xl border border-slate-200 p-6 space-y-5 shadow-soft">
          <h3 class="text-xs font-extrabold text-slate-900 uppercase tracking-wider flex items-center gap-2 border-b border-slate-100 pb-3">
            <i class="fas fa-key text-pp-600"></i> Change Password
          </h3>
          <p class="text-xs text-slate-500">Ensure your account uses a strong and distinct password to protect your transactions and direct seller payouts.</p>

          <form wire:submit.prevent="changePassword" class="space-y-4 text-xs max-w-xl">
            <!-- CURRENT PASSWORD -->
            <div class="space-y-1">
              <label class="font-bold text-slate-700 block">Current Password <span class="text-rose-500">*</span></label>
              <div class="relative">
                <input type="password" wire:model="current_password" placeholder="Enter your current password" class="w-full p-2.5 rounded-xl border border-slate-200 font-bold text-slate-900 outline-none focus:border-pp-500 transition" />
              </div>
              @error('current_password') <span class="text-red-600 text-[11px] font-semibold block">{{ $message }}</span> @enderror
            </div>

            <!-- NEW PASSWORD -->
            <div class="space-y-1">
              <label class="font-bold text-slate-700 block">New Password <span class="text-rose-500">*</span></label>
              <input type="password" wire:model="new_password" placeholder="Enter new password (min. 8 characters)" class="w-full p-2.5 rounded-xl border border-slate-200 font-bold text-slate-900 outline-none focus:border-pp-500 transition" />
              @error('new_password') <span class="text-red-600 text-[11px] font-semibold block">{{ $message }}</span> @enderror
            </div>

            <!-- CONFIRM NEW PASSWORD -->
            <div class="space-y-1">
              <label class="font-bold text-slate-700 block">Confirm New Password <span class="text-rose-500">*</span></label>
              <input type="password" wire:model="new_password_confirmation" placeholder="Re-enter new password" class="w-full p-2.5 rounded-xl border border-slate-200 font-bold text-slate-900 outline-none focus:border-pp-500 transition" />
              @error('new_password_confirmation') <span class="text-red-600 text-[11px] font-semibold block">{{ $message }}</span> @enderror
            </div>

            <div class="pt-3 border-t border-slate-100 flex items-center justify-end">
              <button type="submit" class="px-5 py-2.5 rounded-xl bg-pp-600 hover:bg-pp-700 text-white font-extrabold text-xs shadow-xs transition cursor-pointer flex items-center gap-1.5">
                <i class="fas fa-lock"></i> Update Password
              </button>
            </div>
          </form>
        </div>
      </div>

      <div class="md:col-span-4 space-y-6">
        <div class="p-5 rounded-3xl bg-slate-50 border border-slate-200 text-xs text-slate-600 space-y-3">
          <h4 class="font-extrabold text-slate-900 flex items-center gap-1.5">
            <i class="fas fa-shield-alt text-pp-600"></i> Password Security Tips
          </h4>
          <ul class="space-y-2 list-disc list-inside text-slate-500 text-[11px] leading-relaxed">
            <li>Use at least 8 characters with a mix of letters, numbers, and symbols.</li>
            <li>Do not reuse passwords across multiple trading platforms.</li>
            <li>Changing your password helps protect your payout balance and saved bank information.</li>
          </ul>
        </div>
      </div>
    </div>
  @endif

  <!-- SECTION 3: NOTIFICATIONS & REGISTERED DEVICES -->
  @if ($activeSection === 'notifications')
    <div class="space-y-6">
      
      <!-- NOTIFICATION CHANNELS (IN-APP, PUSH, EMAIL) -->
      <div class="bg-white rounded-3xl border border-slate-200 p-6 space-y-6 shadow-soft">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 border-b border-slate-100 pb-4">
          <div>
            <h3 class="text-xs font-extrabold text-slate-900 uppercase tracking-wider flex items-center gap-2">
              <i class="fas fa-sliders-h text-pp-600"></i> Notification Channels &amp; Preferences
            </h3>
            <p class="text-xs text-slate-500 mt-0.5">Control how and where Parts &amp; Parcel alerts you about orders, offers, messages, and payouts.</p>
          </div>

          <button type="button" wire:click="saveNotificationPreferences" class="px-4 py-2 rounded-xl bg-pp-600 hover:bg-pp-700 text-white font-extrabold text-xs transition cursor-pointer flex items-center gap-1.5 shadow-xs self-start sm:self-auto">
            <i class="fas fa-check"></i> Save Preferences
          </button>
        </div>

        <div class="grid md:grid-cols-3 gap-5">
          
          <!-- CHANNEL 1: IN-APP NOTIFICATIONS -->
          <div class="p-5 rounded-2xl border {{ $notify_in_app ? 'border-pp-200 bg-gradient-to-br from-pp-50/50 to-white' : 'border-slate-200 bg-slate-50/50' }} space-y-3 flex flex-col justify-between">
            <div class="space-y-2">
              <div class="flex items-center justify-between">
                <span class="w-9 h-9 rounded-xl bg-pp-100 text-pp-700 grid place-items-center font-bold text-sm">
                  <i class="fas fa-bell"></i>
                </span>
                <label class="relative inline-flex items-center cursor-pointer">
                  <input type="checkbox" wire:model.live="notify_in_app" class="sr-only peer">
                  <div class="w-11 h-6 bg-slate-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-pp-600"></div>
                </label>
              </div>

              <div>
                <h4 class="font-extrabold text-slate-900 text-sm">In-App Notifications</h4>
                <span class="text-[10px] font-bold {{ $notify_in_app ? 'text-emerald-700' : 'text-slate-400' }}">
                  {{ $notify_in_app ? 'ACTIVE & DELIVERING' : 'MUTED' }}
                </span>
              </div>

              <p class="text-xs text-slate-600 leading-relaxed">
                Receive badge counters, unread message alerts, counter-offers, and live system notifications directly inside your dashboard top header bell.
              </p>
            </div>

            <div class="pt-2 text-[10px] text-slate-400 flex items-center gap-1 border-t border-slate-100">
              <i class="fas fa-info-circle text-slate-400"></i> Displayed while browsing Parts &amp; Parcel
            </div>
          </div>

          <!-- CHANNEL 2: PUSH NOTIFICATIONS (FCM) -->
          <div class="p-5 rounded-2xl border {{ $notify_push ? 'border-pp-200 bg-gradient-to-br from-pp-50/50 to-white' : 'border-slate-200 bg-slate-50/50' }} space-y-3 flex flex-col justify-between">
            <div class="space-y-2">
              <div class="flex items-center justify-between">
                <span class="w-9 h-9 rounded-xl bg-blue-100 text-blue-700 grid place-items-center font-bold text-sm">
                  <i class="fas fa-paper-plane"></i>
                </span>
                <label class="relative inline-flex items-center cursor-pointer">
                  <input type="checkbox" wire:model.live="notify_push" class="sr-only peer">
                  <div class="w-11 h-6 bg-slate-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-pp-600"></div>
                </label>
              </div>

              <div>
                <h4 class="font-extrabold text-slate-900 text-sm">Push Notifications</h4>
                <span class="text-[10px] font-bold {{ $notify_push ? 'text-blue-700' : 'text-slate-400' }}">
                  {{ $notify_push ? 'POWERED BY FIREBASE FCM' : 'MUTED' }}
                </span>
              </div>

              <p class="text-xs text-slate-600 leading-relaxed">
                Instant real-time device push notifications sent directly to your registered web browsers and mobile apps even when the website is closed.
              </p>
            </div>

            <div class="pt-2 text-[10px] text-slate-400 flex items-center gap-1 border-t border-slate-100">
              <i class="fas fa-bolt text-blue-500"></i> Instant delivery to registered devices
            </div>
          </div>

          <!-- CHANNEL 3: EMAIL NOTIFICATIONS -->
          <div class="p-5 rounded-2xl border {{ $notify_email ? 'border-pp-200 bg-gradient-to-br from-pp-50/50 to-white' : 'border-slate-200 bg-slate-50/50' }} space-y-3 flex flex-col justify-between">
            <div class="space-y-2">
              <div class="flex items-center justify-between">
                <span class="w-9 h-9 rounded-xl bg-purple-100 text-purple-700 grid place-items-center font-bold text-sm">
                  <i class="fas fa-envelope-open-text"></i>
                </span>
                <label class="relative inline-flex items-center cursor-pointer">
                  <input type="checkbox" wire:model.live="notify_email" class="sr-only peer">
                  <div class="w-11 h-6 bg-slate-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-pp-600"></div>
                </label>
              </div>

              <div>
                <h4 class="font-extrabold text-slate-900 text-sm">Email Notifications</h4>
                <span class="text-[10px] font-bold {{ $notify_email ? 'text-purple-700' : 'text-slate-400' }}">
                  {{ $notify_email ? 'SENT TO ' . Str::limit($user->email, 16) : 'MUTED' }}
                </span>
              </div>

              <p class="text-xs text-slate-600 leading-relaxed">
                Official transaction invoices, escrow release confirmation receipts, customer dispute filings, and security verification links sent to your inbox.
              </p>
            </div>

            <div class="pt-2 text-[10px] text-slate-400 flex items-center gap-1 border-t border-slate-100">
              <i class="fas fa-shield-alt text-purple-500"></i> Essential for transaction receipts
            </div>
          </div>

        </div>
      </div>

      <!-- DEVICE TOKEN / REGISTERED PUSH DEVICES MANAGEMENT -->
      <div class="bg-white rounded-3xl border border-slate-200 p-6 space-y-5 shadow-soft">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 border-b border-slate-100 pb-4">
          <div>
            <h3 class="text-xs font-extrabold text-slate-900 uppercase tracking-wider flex items-center gap-2">
              <i class="fas fa-mobile-alt text-pp-600"></i> Registered Devices &amp; FCM Push Tokens
            </h3>
            <p class="text-xs text-slate-500 mt-0.5">These devices are authorized to receive real-time push alerts via Firebase Cloud Messaging.</p>
          </div>

          <div class="flex items-center gap-2">
            <button type="button" wire:click="registerCurrentDevice" class="px-3.5 py-2 rounded-xl border border-pp-200 bg-pp-50 hover:bg-pp-100 text-pp-700 font-extrabold text-xs transition cursor-pointer flex items-center gap-1.5 shadow-2xs">
              <i class="fas fa-plus-circle text-pp-600"></i> Register This Browser
            </button>

            @if($deviceTokens->count() > 0)
              <button type="button" wire:click="testPushNotification" class="px-3.5 py-2 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-extrabold text-xs transition cursor-pointer flex items-center gap-1.5 shadow-xs">
                <i class="fas fa-paper-plane"></i> Send Test Push
              </button>
            @endif
          </div>
        </div>

        @if($deviceTokens->count() > 0)
          <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-slate-600">
              <thead>
                <tr class="border-b border-slate-100 text-[11px] font-extrabold uppercase text-slate-400">
                  <th class="py-3 px-3">Device &amp; Platform</th>
                  <th class="py-3 px-3">Token Fingerprint</th>
                  <th class="py-3 px-3">Last Active</th>
                  <th class="py-3 px-3 text-right">Action</th>
                </tr>
              </thead>
              <tbody class="divide-y divide-slate-100">
                @foreach ($deviceTokens as $token)
                  @php
                    $isWeb = $token->platform === 'web';
                    $isAndroid = $token->platform === 'android';
                    $isIos = $token->platform === 'ios';
                  @endphp
                  <tr class="hover:bg-slate-50/70 transition">
                    <td class="py-3 px-3">
                      <div class="flex items-center gap-2.5">
                        <span class="w-8 h-8 rounded-xl bg-slate-100 grid place-items-center text-sm font-bold {{ $isAndroid ? 'text-emerald-600' : ($isIos ? 'text-slate-800' : 'text-blue-600') }}">
                          @if ($isAndroid)
                            <i class="fab fa-android"></i>
                          @elseif ($isIos)
                            <i class="fab fa-apple"></i>
                          @else
                            <i class="fab fa-chrome"></i>
                          @endif
                        </span>
                        <div>
                          <p class="font-extrabold text-slate-900">{{ $token->device_name ?: 'Web Browser Session' }}</p>
                          <span class="px-1.5 py-0.2 rounded text-[10px] font-extrabold uppercase bg-slate-100 text-slate-600">
                            {{ $token->platform }}
                          </span>
                        </div>
                      </div>
                    </td>

                    <td class="py-3 px-3 font-mono text-[11px] text-slate-500">
                      {{ Str::limit($token->token, 20) }}...
                    </td>

                    <td class="py-3 px-3 text-[11px] text-slate-500">
                      {{ $token->last_used_at ? $token->last_used_at->diffForHumans() : $token->created_at->diffForHumans() }}
                    </td>

                    <td class="py-3 px-3 text-right">
                      <button type="button" wire:click="removeDeviceToken({{ $token->id }})" class="p-1.5 px-2 rounded-lg text-rose-600 hover:bg-rose-50 font-extrabold text-xs transition cursor-pointer" title="Revoke Device">
                        <i class="fas fa-trash-alt mr-1"></i> Revoke
                      </button>
                    </td>
                  </tr>
                @endforeach
              </tbody>
            </table>
          </div>
        @else
          <div class="p-8 text-center bg-slate-50 rounded-2xl border border-dashed border-slate-200 space-y-3">
            <div class="w-12 h-12 mx-auto rounded-2xl bg-blue-50 text-blue-600 grid place-items-center text-xl">
              <i class="fas fa-mobile-alt"></i>
            </div>
            <div>
              <h4 class="font-extrabold text-slate-900 text-xs">No Push Devices Registered</h4>
              <p class="text-[11px] text-slate-500 max-w-sm mx-auto mt-0.5">
                Register this browser to enable instant push notifications for offers, transactions, and customer inquiries.
              </p>
            </div>
            <button type="button" wire:click="registerCurrentDevice" class="px-4 py-2 rounded-xl bg-pp-600 hover:bg-pp-700 text-white font-extrabold text-xs shadow-xs transition cursor-pointer inline-flex items-center gap-1.5">
              <i class="fas fa-plus-circle"></i> Register This Browser Now
            </button>
          </div>
        @endif

      </div>

    </div>
  @endif

  <!-- SECTION 4: SELLER PAYOUT BANK -->
  @if ($activeSection === 'banking')
    <div class="grid md:grid-cols-12 gap-6">
      
      <!-- LEFT: HOW PAYMENTS WORK -->
      <div class="md:col-span-5 space-y-6">
        <div class="p-6 rounded-3xl bg-slate-50 border border-slate-200 text-xs text-slate-600 space-y-3 shadow-2xs">
          <h4 class="font-extrabold text-slate-900 flex items-center gap-1.5 text-sm">
            <i class="fas fa-info-circle text-pp-600"></i> How Bank Accounts Work on Parts &amp; Parcel
          </h4>
          <p class="leading-relaxed">
            When buyers choose <strong>Direct Transfer</strong> at checkout, they pay directly into your verified bank account.
          </p>
          <p class="leading-relaxed">
            For orders placed via <strong>Parts &amp; Parcel Escrow</strong>, payouts are automatically released to this account upon courier delivery confirmation or buyer inspection completion.
          </p>

          <div class="pt-3 border-t border-slate-200 space-y-2 text-[11px]">
            <div class="flex items-start gap-2">
              <i class="fas fa-shield-alt text-emerald-600 mt-0.5"></i>
              <span>Account names are automatically verified with Nigerian banking gateways.</span>
            </div>
            <div class="flex items-start gap-2">
              <i class="fas fa-lock text-pp-600 mt-0.5"></i>
              <span>Authorization requires entering your current account password before any changes are committed.</span>
            </div>
          </div>
        </div>
      </div>

      <!-- RIGHT: DIRECT SELLER PAYOUT BANK FORM & DETAILS -->
      <div class="md:col-span-7 space-y-6">
        <div class="bg-white rounded-3xl border border-slate-200 p-6 space-y-5 shadow-soft">
          <div class="flex items-center justify-between border-b border-slate-100 pb-3">
            <h3 class="text-xs font-extrabold text-slate-900 uppercase tracking-wider flex items-center gap-2">
              <i class="fas fa-university text-pp-600"></i> Direct Seller Payout Bank
            </h3>
            @if ($hasSavedBank && ! $showEditBankForm)
              <span class="px-2.5 py-1 rounded-full bg-emerald-100 text-emerald-800 text-[10px] font-black">
                ACTIVE
              </span>
            @endif
          </div>

          @if ($hasSavedBank && ! $showEditBankForm)
            <!-- VIEW CURRENT SAVED BANK -->
            <div class="p-5 rounded-2xl bg-gradient-to-br from-pp-50 to-slate-50 border border-pp-200 space-y-4 text-xs shadow-2xs">
              <div class="flex items-center justify-between">
                <span class="text-slate-500 font-bold text-[10px] uppercase tracking-wider">Connected Bank Account</span>
                <span class="text-emerald-700 font-black text-[11px] flex items-center gap-1">
                  <i class="fas fa-check-circle"></i> Ready to Receive Payments
                </span>
              </div>

              <div>
                <span class="text-[11px] text-slate-400 font-bold block">BANK</span>
                <h4 class="text-base font-black text-slate-900">{{ $savedBank->bank_name }}</h4>
              </div>

              <div class="grid grid-cols-2 gap-3 pt-2 border-t border-pp-100">
                <div>
                  <span class="text-[10px] text-slate-400 font-bold block">ACCOUNT NUMBER</span>
                  <p class="font-mono font-black text-sm text-pp-800 tracking-wider">{{ $savedBank->account_number }}</p>
                </div>
                <div>
                  <span class="text-[10px] text-slate-400 font-bold block">ACCOUNT NAME</span>
                  <p class="font-bold text-xs text-slate-800 truncate" title="{{ $savedBank->account_name }}">{{ $savedBank->account_name }}</p>
                </div>
              </div>

              @if ($savedBank->verified_at)
                <div class="pt-2 text-[10px] text-slate-500 flex items-center gap-1 border-t border-pp-100/60">
                  <i class="fas fa-shield-check text-emerald-600"></i> Verified on {{ $savedBank->verified_at->format('M d, Y') }}
                </div>
              @endif
            </div>

            <button type="button" wire:click="editBank" class="w-full py-2.5 rounded-xl border border-slate-200 text-slate-800 font-bold text-xs hover:bg-slate-50 transition cursor-pointer flex items-center justify-center gap-2">
              <i class="fas fa-pencil-alt text-slate-500"></i> Update Bank Details
            </button>

          @else
            <!-- EDIT / ADD BANK ACCOUNT FORM -->
            <div class="space-y-4 text-xs">
              @if (! $hasSavedBank)
                <div class="p-3 rounded-xl bg-amber-50 border border-amber-200 text-amber-900 text-[11px] leading-relaxed">
                  <strong class="block font-extrabold flex items-center gap-1 mb-0.5">
                    <i class="fas fa-exclamation-triangle text-amber-600"></i> Action Needed:
                  </strong>
                  Add your bank account below so buyers can make direct payments to you, and your escrow funds can be deposited.
                </div>
              @endif

              <!-- 1. BANK NAME -->
              <div class="space-y-1">
                <label class="font-bold text-slate-700 flex items-center justify-between">
                  <span>Select or Enter Bank</span>
                  @if ($isLoadingBanks)
                    <span class="text-[10px] text-pp-600 font-bold flex items-center gap-1">
                      <i class="fas fa-circle-notch fa-spin"></i> Loading bank list...
                    </span>
                  @endif
                </label>

                @if (! empty($banks))
                  <x-searchable-select
                    wire:model.live="bank_code"
                    :options="collect($banks)->map(fn($b) => ['value' => $b['code'], 'label' => $b['name']])"
                    placeholder="-- Choose your bank --"
                    search-placeholder="Search bank name..."
                  />
                  <div class="flex items-center justify-between text-[10px] text-slate-400 mt-1">
                    <span>{{ count($banks) }} banks available via payment gateway</span>
                    @if (! empty($bank_name))
                      <span class="text-pp-700 font-bold">{{ $bank_name }}</span>
                    @endif
                  </div>
                @else
                  <div class="relative">
                    <input type="text" wire:model="bank_name" placeholder="Type bank name (e.g. GTBank, Zenith, Access)" class="w-full p-2.5 rounded-xl border border-slate-200 font-bold text-slate-900 outline-none focus:border-pp-500 transition" />
                    <span class="absolute right-3 top-2.5 text-slate-400 text-xs">
                      <i class="fas fa-university"></i>
                    </span>
                  </div>
                @endif
                @error('bank_name') <span class="text-red-600 text-[11px] font-semibold block">{{ $message }}</span> @enderror
              </div>

              <!-- 2. ACCOUNT NUMBER & RESOLUTION -->
              <div class="space-y-1">
                <label class="font-bold text-slate-700 flex items-center justify-between">
                  <span>10-Digit Account Number</span>
                  @if ($isResolving)
                    <span class="text-[10px] text-pp-600 font-bold flex items-center gap-1">
                      <i class="fas fa-spinner fa-spin"></i> Resolving account name...
                    </span>
                  @endif
                </label>
                <div class="relative">
                  <input type="text" wire:model.live.debounce.400ms="account_number" maxlength="10" placeholder="0123456789" class="w-full p-2.5 rounded-xl border border-slate-200 font-mono font-bold text-slate-900 tracking-wider outline-none focus:border-pp-500 transition" />
                  @if ($resolveSuccess)
                    <span class="absolute right-3 top-2.5 text-emerald-600 text-xs">
                      <i class="fas fa-check-circle"></i>
                    </span>
                  @endif
                </div>
                @error('account_number') <span class="text-red-600 text-[11px] font-semibold block">{{ $message }}</span> @enderror
              </div>

              <!-- 3. ACCOUNT NAME -->
              <div class="space-y-1">
                <label class="font-bold text-slate-700 flex items-center justify-between">
                  <span>Account Name</span>
                  @if ($resolveSuccess)
                    <span class="text-[10px] px-2 py-0.5 rounded bg-emerald-100 text-emerald-800 font-extrabold flex items-center gap-1">
                      <i class="fas fa-shield-alt"></i> GATEWAY VERIFIED
                    </span>
                  @endif
                </label>
                <input type="text" wire:model="account_name" placeholder="Full name registered on bank account" class="w-full p-2.5 rounded-xl border border-slate-200 font-bold text-slate-900 outline-none focus:border-pp-500 transition {{ $resolveSuccess ? 'bg-emerald-50/50 border-emerald-300' : '' }}" />
                @if ($resolveError)
                  <p class="text-[10px] text-amber-700 font-semibold">{{ $resolveError }}</p>
                @endif
                @error('account_name') <span class="text-red-600 text-[11px] font-semibold block">{{ $message }}</span> @enderror
              </div>

              <!-- 4. SECURITY CHECK: ACCOUNT PASSWORD REQUIREMENT -->
              <div class="space-y-1 pt-2 border-t border-slate-100">
                <label class="font-bold text-slate-800 flex items-center gap-1">
                  <i class="fas fa-lock text-slate-500"></i> Account Password Verification
                </label>
                <input type="password" wire:model="bank_password" placeholder="Enter your login password to confirm" class="w-full p-2.5 rounded-xl border border-slate-200 font-bold text-slate-900 outline-none focus:border-pp-500 transition" />
                <p class="text-[10px] text-slate-500">Enter your password to authorize this bank account change.</p>
                @error('bank_password') <span class="text-red-600 text-[11px] font-semibold block">{{ $message }}</span> @enderror
              </div>

              <!-- ACTIONS -->
              <div class="pt-3 border-t border-slate-100 flex items-center gap-2">
                <button type="button" wire:click="saveBankAccount" class="flex-1 py-2.5 rounded-xl bg-pp-600 hover:bg-pp-700 text-white font-extrabold text-xs shadow-xs transition cursor-pointer flex items-center justify-center gap-1.5">
                  <i class="fas fa-lock"></i> Save Bank Account
                </button>

                @if ($hasSavedBank)
                  <button type="button" wire:click="cancelEditBank" class="py-2.5 px-4 rounded-xl border border-slate-200 text-slate-600 font-bold text-xs hover:bg-slate-50 transition cursor-pointer">
                    Cancel
                  </button>
                @endif
              </div>

            </div>
          @endif

        </div>
      </div>

    </div>
  @endif

</div>