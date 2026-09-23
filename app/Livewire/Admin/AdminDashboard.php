<?php

namespace App\Livewire\Admin;

use App\Models\Discussion;
use App\Models\Dispute;
use App\Models\Invoice;
use App\Models\Listing;
use App\Models\Offer;
use App\Models\Payment;
use App\Models\Response;
use App\Models\Settlement;
use App\Models\User;
use Carbon\Carbon;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.dash')]
#[Title('Parts & Parcel — Admin Control Center')]
class AdminDashboard extends Component
{
    public function render()
    {
        $startOfMonth = now()->startOfMonth();
        $today = today();

        // 1. KPI Cards
        $gmvThisMonth = Invoice::where('status', 'paid')
            ->where('created_at', '>=', $startOfMonth)
            ->sum('total') ?: 0;

        $platformRevenue = Invoice::where('status', 'paid')
            ->where('created_at', '>=', $startOfMonth)
            ->sum('commission') ?: ($gmvThisMonth * 0.05); // default 5% platform commission

        $activeUsersCount = User::count();
        $newUsersThisWeek = User::where('created_at', '>=', now()->startOfWeek())->count();

        $activeDisputesCount = Dispute::whereIn('status', ['opened', 'pending', 'escalated'])->count();
        $pendingListingsCount = Listing::where('status', 'pending')->count();
        $unverifiedUsersCount = User::where('is_verified', false)->count();

        $needsAttentionCount = $activeDisputesCount + $pendingListingsCount + $unverifiedUsersCount;

        // 2. 7-Day Activity Chart
        $chartDays = collect();
        $maxDailyVolume = 1;
        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i);
            $dayName = $date->format('D');
            $dailyVolume = Invoice::whereDate('created_at', $date->toDateString())
                ->where('status', 'paid')
                ->sum('total') ?: (Invoice::whereDate('created_at', $date->toDateString())->count() * 15000);

            if ($dailyVolume > $maxDailyVolume) {
                $maxDailyVolume = $dailyVolume;
            }

            $chartDays->push([
                'day' => $dayName,
                'date' => $date->format('M d'),
                'volume' => $dailyVolume,
                'isToday' => $i === 0,
            ]);
        }

        $chartDays = $chartDays->map(function ($item) use ($maxDailyVolume) {
            $pct = $maxDailyVolume > 0 ? max(20, min(100, round(($item['volume'] / $maxDailyVolume) * 100))) : 25;
            $item['heightPercent'] = $pct;
            return $item;
        });

        // 3. Action Queue Details
        $oldestDispute = Dispute::whereIn('status', ['opened', 'pending', 'escalated'])->oldest()->first();
        $oldestDisputeDays = $oldestDispute ? $oldestDispute->created_at->diffInDays(now()) : 0;

        $pendingSettlementsCount = Settlement::where('status', 'pending')->count();
        $pendingSettlementsAmount = Settlement::where('status', 'pending')->sum('net_amount') ?: 0;

        // 4. Marketplace Metrics
        $activeListings = Listing::where('status', 'active')->count();
        $newListingsToday = Listing::whereDate('created_at', $today)->count();
        $offersToday = Offer::whereDate('created_at', $today)->count();
        $completedSales = Invoice::where('status', 'paid')->count();

        // 5. Financial Snapshot
        $paymentsCollected = Payment::where('status', 'successful')->sum('amount') ?: Invoice::where('status', 'paid')->sum('total');
        $refundsThisMonth = Payment::where('status', 'refunded')->where('created_at', '>=', $startOfMonth)->sum('amount') ?: 0;

        // 6. Community Health
        $discussionsToday = Discussion::whereDate('created_at', $today)->count();
        $responsesToday = Response::whereDate('created_at', $today)->count();
        $reportsAwaitingReview = 0; // future reported content queue

        // 7. Recent Platform Events
        $recentEvents = collect();

        // Add recent disputes
        foreach (Dispute::with('issue')->latest()->take(3)->get() as $disp) {
            $recentEvents->push([
                'type' => 'dispute',
                'icon' => '⚖',
                'bg' => 'bg-rose-50 text-rose-600',
                'title' => "Dispute opened #DSP-{$disp->id}",
                'subtitle' => $disp->reason ?: ($disp->issue->title ?? 'Buyer/seller issue escalated to arbitration'),
                'time' => $disp->created_at->diffForHumans(),
                'timestamp' => $disp->created_at,
            ]);
        }

        // Add recent successful payments
        foreach (Payment::where('status', 'successful')->latest()->take(3)->get() as $pmt) {
            $recentEvents->push([
                'type' => 'payment',
                'icon' => '₦',
                'bg' => 'bg-emerald-50 text-emerald-600',
                'title' => "Payment of " . number_format($pmt->amount) . " received",
                'subtitle' => "Ref: {$pmt->reference} via " . ucfirst($pmt->provider ?? 'gateway'),
                'time' => $pmt->created_at->diffForHumans(),
                'timestamp' => $pmt->created_at,
            ]);
        }

        // Add recent users
        foreach (User::latest()->take(3)->get() as $u) {
            $recentEvents->push([
                'type' => 'user',
                'icon' => '👤',
                'bg' => 'bg-blue-50 text-blue-600',
                'title' => "New user registered: {$u->name}",
                'subtitle' => $u->is_verified ? 'Verified account' : 'Verification / KYC pending',
                'time' => $u->created_at->diffForHumans(),
                'timestamp' => $u->created_at,
            ]);
        }

        $recentEvents = $recentEvents->sortByDesc('timestamp')->take(5)->values();

        return view('livewire.admin.admin-dashboard', [
            'gmvThisMonth' => $gmvThisMonth,
            'platformRevenue' => $platformRevenue,
            'activeUsersCount' => $activeUsersCount,
            'newUsersThisWeek' => $newUsersThisWeek,
            'needsAttentionCount' => $needsAttentionCount,
            'chartDays' => $chartDays,
            'activeDisputesCount' => $activeDisputesCount,
            'oldestDisputeDays' => $oldestDisputeDays,
            'pendingListingsCount' => $pendingListingsCount,
            'unverifiedUsersCount' => $unverifiedUsersCount,
            'pendingSettlementsCount' => $pendingSettlementsCount,
            'pendingSettlementsAmount' => $pendingSettlementsAmount,
            'activeListings' => $activeListings,
            'newListingsToday' => $newListingsToday,
            'offersToday' => $offersToday,
            'completedSales' => $completedSales,
            'paymentsCollected' => $paymentsCollected,
            'refundsThisMonth' => $refundsThisMonth,
            'discussionsToday' => $discussionsToday,
            'responsesToday' => $responsesToday,
            'reportsAwaitingReview' => $reportsAwaitingReview,
            'recentEvents' => $recentEvents,
        ]);
    }
}
