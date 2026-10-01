<?php

// use App\Livewire\Admin\AdminAdvertisements;
use App\Livewire\Admin\AdminBlog;
use App\Livewire\Admin\AdminBlogPostCreate;
use App\Livewire\Admin\AdminBlogPostEdit;
use App\Livewire\Admin\AdminBlogPostShow;
use App\Livewire\Admin\AdminCouponCreate;
use App\Livewire\Admin\AdminCouponEdit;
use App\Livewire\Admin\AdminCoupons;
use App\Livewire\Admin\AdminDashboard;
use App\Livewire\Admin\AdminFaqs;
use App\Livewire\Admin\AdminListingDetails;
use App\Livewire\Admin\AdminListings;
use App\Livewire\Admin\AdminModerations;
use App\Livewire\Admin\AdminPayments;
use App\Livewire\Admin\AdminPromotions;
use App\Livewire\Admin\AdminSubscriptions;
use App\Livewire\Admin\AdminUserDetails;
use App\Livewire\Admin\AdminUsers;
use App\Livewire\Admin\Settings\AdminCategories;
use App\Livewire\Admin\Settings\AdminCountries;
use App\Livewire\Admin\Settings\AdminGeneral;
use App\Livewire\Admin\Settings\AdminPromotionPlans;
use App\Livewire\Admin\Settings\AdminRolesPermissions;
use App\Livewire\Admin\Settings\AdminStaff;
use App\Livewire\Admin\Settings\AdminSubscriptionPlans;
use App\Livewire\Auth\ForgotPassword;
use App\Livewire\Auth\Login;
use App\Livewire\Auth\Register;
use App\Livewire\Auth\ResetPassword;
use App\Livewire\Dashboard\Disputes\DisputesList;
use App\Livewire\Dashboard\Disputes\DisputeView;
use App\Livewire\Dashboard\Earnings;
use App\Livewire\Dashboard\Inventory\ItemCreate;
use App\Livewire\Dashboard\Inventory\ItemsList;
use App\Livewire\Dashboard\Inventory\ItemView;
use App\Livewire\Dashboard\Inventory\Listings;
use App\Livewire\Dashboard\Inventory\ListingView;
use App\Livewire\Dashboard\Invoices\InvoicesList;
use App\Livewire\Dashboard\Invoices\InvoiceView;
use App\Livewire\Dashboard\Locations;
use App\Livewire\Dashboard\Messages\MessageConversation;
use App\Livewire\Dashboard\Messages\MessageList;
use App\Livewire\Dashboard\Notifications;
use App\Livewire\Dashboard\Offers\OffersList;
use App\Livewire\Dashboard\Offers\OfferView;
use App\Livewire\Dashboard\Overview;
use App\Livewire\Dashboard\Profile;
use App\Livewire\Dashboard\Requests\MyRequests;
use App\Livewire\Dashboard\Requests\MyRequestView;
use App\Livewire\Dashboard\Responses\MyResponses;
use App\Livewire\Dashboard\Responses\MyResponseView;
use App\Livewire\Dashboard\Shipments\ShipmentsList;
use App\Livewire\Dashboard\Shipments\ShipmentView;
use App\Livewire\Dashboard\SubscriptionConfirmation;
use App\Livewire\Dashboard\SubscriptionPlans;
use App\Livewire\Dashboard\Subscriptions;
use App\Livewire\Dashboard\Wishlists;
use App\Livewire\Marketplace\CartPage;
use App\Livewire\Marketplace\CheckoutPage;
use App\Livewire\Marketplace\Community\CommunityHome;
use App\Livewire\Marketplace\Community\CommunityRequest;
use App\Livewire\Marketplace\Listings\Category;
use App\Livewire\Marketplace\Listings\ListingDetails;
use App\Livewire\Marketplace\Listings\SearchPage;
use App\Livewire\Marketplace\Welcome;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

// Authentication (Guest)
Route::middleware('guest')->group(function () {
    Route::get('login', Login::class)->name('login');
    Route::get('register', Register::class)->name('register');
    Route::get('forgot-password', ForgotPassword::class)->name('password.request');
    Route::get('reset-password/{token}', ResetPassword::class)->name('password.reset');
});

// Logout
Route::post('logout', function () {
    Auth::guard('web')->logout();
    request()->session()->invalidate();
    request()->session()->regenerateToken();
    return redirect()->route('welcome');
})->name('logout');
// Admin Direct Logout
// Route::get('admin/logout', function () {
//     Auth::guard('web')->logout();
//     request()->session()->invalidate();
//     request()->session()->regenerateToken();
//     return redirect()->route('welcome');
// })->name('admin.logout');

Route::get('/', Welcome::class)->name('welcome');
Route::get('category', Category::class)->name('category');
Route::get('search', SearchPage::class)->name('search');
Route::get('listing-details/{listing}', ListingDetails::class)->name('listing-details');
Route::get('community', CommunityHome::class)->name('community');
Route::get('community/request/{id}', CommunityRequest::class)->name('community.request');
Route::get('cart', CartPage::class)->name('cart');
Route::get('checkout', CheckoutPage::class)->name('checkout');
Route::get('help', Overview::class)->name('help');

// Payments & Webhooks
Route::get('payment/callback', [\App\Http\Controllers\PaymentController::class, 'callback'])->name('payment.callback');
Route::post('webhooks/paystack', [\App\Http\Controllers\Webhooks\PaystackWebhookController::class, 'handle'])->name('webhooks.paystack');
Route::post('webhooks/flutterwave', [\App\Http\Controllers\Webhooks\FlutterwaveWebhookController::class, 'handle'])->name('webhooks.flutterwave');

// Invoice Document & Spreadsheet Exports
Route::middleware('auth')->group(function () {
    Route::get('dashboard', Overview::class)->name('dashboard');
    Route::get('subscriptions', Subscriptions::class)->name('subscriptions');
    Route::get('subscription-plans', SubscriptionPlans::class)->name('subscription-plans');
    Route::get('subscription/confirm/{plan}', SubscriptionConfirmation::class)->name('subscription.confirm');
    Route::get('messages', MessageList::class)->name('messages');
    Route::get('messages/conversation', MessageConversation::class)->name('conversation');
    Route::get('offers', OffersList::class)->name('offers');
    Route::get('offers/{offer_id?}', OfferView::class)->name('offers.view');

    Route::get('invoices', InvoicesList::class)->name('invoices');
    Route::get('invoices/{invoice_id?}', InvoiceView::class)->name('invoices.view');
    Route::get('invoices/export/excel', [\App\Http\Controllers\InvoiceExportController::class, 'exportExcel'])->name('invoices.export.excel');
    Route::get('invoices/{invoice}/export/pdf', [\App\Http\Controllers\InvoiceExportController::class, 'exportPdf'])->name('invoices.export.pdf');

    Route::get('shipments', ShipmentsList::class)->name('shipments');
    Route::get('shipments/{shipment_id?}', ShipmentView::class)->name('shipments.view');
    Route::get('locations', Locations::class)->name('locations');
    Route::get('notifications', Notifications::class)->name('notifications');
    Route::get('profile', Profile::class)->name('profile');

    //Buying
    Route::get('wishlists', Wishlists::class)->name('wishlists');
    Route::get('myrequests', MyRequests::class)->name('myrequests');
    Route::get('myrequests/{id?}', MyRequestView::class)->name('myrequest.view');
    //Selling
    Route::get('myresponses', MyResponses::class)->name('myresponses');
    Route::get('myresponses/{id?}', MyResponseView::class)->name('myresponse.view');
    Route::get('myitems', ItemsList::class)->name('myitems');
    Route::get('myitems/create', ItemCreate::class)->name('item.create');
    Route::get('myitems/item_id', ItemView::class)->name('item.view');
    Route::get('mylistings', Listings::class)->name('mylistings');
    Route::get('mylistings/listing_id', ListingView::class)->name('mylisting.view');
    Route::get('myearnings', Earnings::class)->name('earnings');
    Route::get('disputes', DisputesList::class)->name('disputes');
    Route::get('disputes/dispute_id', DisputeView::class)->name('disputes.view');
});

// Admin Control Center (Protected by EnsureUserIsAdmin)
Route::middleware(['auth', 'admin'])->prefix('admin')->as('admin.')->group(function () {
    Route::get('/', AdminDashboard::class)->name('index');
    Route::get('dashboard', AdminDashboard::class)->name('dashboard');
    Route::get('profile', Profile::class)->name('profile');
    Route::get('moderations', AdminModerations::class)->name('moderations');
    Route::get('users', AdminUsers::class)->name('users');
    Route::get('users/{user}', AdminUserDetails::class)->whereNumber('user')->name('users.show');
    Route::get('subscriptions', AdminSubscriptions::class)->name('subscriptions');
    Route::get('promotions', AdminPromotions::class)->name('promotions');
    Route::get('properties', AdminListings::class)->name('properties');
    Route::get('properties/{property}', AdminListingDetails::class)->whereNumber('property')->name('properties.show');
    Route::get('blog', AdminBlog::class)->name('blog');
    Route::get('blog/create', AdminBlogPostCreate::class)->name('blog.create');
    Route::get('blog/{post}', AdminBlogPostShow::class)->whereNumber('post')->name('blog.show');
    Route::get('blog/{post}/edit', AdminBlogPostEdit::class)->whereNumber('post')->name('blog.edit');
    Route::get('payments', AdminPayments::class)->name('payments');
    // Route::get('coupons', AdminCoupons::class)->name('coupons');
    // Route::get('coupons/create', AdminCouponCreate::class)->name('coupons.create');
    // Route::get('coupons/{coupon}/edit', AdminCouponEdit::class)->whereNumber('coupon')->name('coupons.edit');
    Route::get('faqs', AdminFaqs::class)->name('faqs');
    // Route::get('advertisements', AdminAdvertisements::class)->name('advertisements');
    Route::prefix('settings')->name('settings.')->group(function () {
        Route::get('general', AdminGeneral::class)->name('general');
        Route::get('categories', AdminCategories::class)->name('categories');
        Route::get('countries', AdminCountries::class)->name('countries');
        Route::get('staff', AdminStaff::class)->name('staff');
        Route::get('roles', AdminRolesPermissions::class)->name('roles');
        Route::get('promotion-plans', AdminPromotionPlans::class)->name('promotion-plans');
        Route::get('subscription-plans', AdminSubscriptionPlans::class)->name('subscription-plans');
    });
});
    // Route::middleware(['admin', 'role.selected'])->prefix('admin')->name('admin.')->group(function () {
    //     Route::get('dashboard', AdminDashboard::class)->name('dashboard');
    //     
    // });

Route::get('clear-cache', function () {
    Artisan::call('cache:clear');
    Artisan::call('config:clear');
    Artisan::call('view:clear');
    Artisan::call('route:clear');

    return 'Cache cleared!';
});
