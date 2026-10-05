<?php

namespace App\Livewire\Admin;

use App\Models\Invoice;
use App\Models\Listing;
use App\Models\Payment;
use App\Models\Revenue;
use App\Models\Subscription;
use App\Models\User;
use Carbon\Carbon;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;

#[Layout('layouts.dash')]
#[Title('Analytics & Reports — Admin Control Center')]
class AdminAnalytics extends Component
{
    #[Url(as: 'period')]
    public string $period = '30d'; // 7d, 30d, 90d, year, all

    public function setPeriod(string $period): void
    {
        if (in_array($period, ['7d', '30d', '90d', 'year', 'all'])) {
            $this->period = $period;
        }
    }

    public function render()
    {
        $startDate = match ($this->period) {
            '7d' => now()->subDays(7)->startOfDay(),
            '30d' => now()->subDays(30)->startOfDay(),
            '90d' => now()->subDays(90)->startOfDay(),
            'year' => now()->subYear()->startOfDay(),
            default => Carbon::create(2020, 1, 1),
        };

        // 1. High Level Financials
        $gmv = Invoice::where('status', 'paid')
            ->where('created_at', '>=', $startDate)
            ->sum('total') ?: 0;

        $platformRevenue = Revenue::where('created_at', '>=', $startDate)->sum('amount') 
            ?: (Invoice::where('status', 'paid')->where('created_at', '>=', $startDate)->sum('commission') ?: 0);

        $paymentsVolume = Payment::where('status', 'successful')
            ->where('created_at', '>=', $startDate)
            ->sum('amount') ?: $gmv;

        $paidInvoicesCount = Invoice::where('status', 'paid')->where('created_at', '>=', $startDate)->count();
        $averageOrderValue = $paidInvoicesCount > 0 ? round($gmv / $paidInvoicesCount, 2) : 0;

        // 2. Marketplace Growth
        $newUsersCount = User::where('created_at', '>=', $startDate)->count();
        $newListingsCount = Listing::where('created_at', '>=', $startDate)->count();
        $newSubscriptionsCount = Subscription::where('created_at', '>=', $startDate)->count();

        // 3. Daily / Weekly Chart Trend (last 14 buckets)
        $numBuckets = 14;
        $chartData = collect();
        $maxDailyGmv = 1;

        for ($i = $numBuckets - 1; $i >= 0; $i--) {
            $date = now()->subDays($i);
            $dayGmv = Invoice::where('status', 'paid')
                ->whereDate('created_at', $date->toDateString())
                ->sum('total') ?: 0;

            if ($dayGmv > $maxDailyGmv) {
                $maxDailyGmv = $dayGmv;
            }

            $chartData->push([
                'label' => $date->format('M d'),
                'day' => $date->format('D'),
                'amount' => $dayGmv,
                'isToday' => $i === 0,
            ]);
        }

        $chartData = $chartData->map(function ($item) use ($maxDailyGmv) {
            $pct = $maxDailyGmv > 0 ? max(10, min(100, round(($item['amount'] / $maxDailyGmv) * 100))) : 15;
            $item['heightPercent'] = $pct;
            return $item;
        });

        // 4. Revenue Breakdown by Type
        $revenueByType = Revenue::where('created_at', '>=', $startDate)
            ->selectRaw('type, sum(amount) as total_amount')
            ->groupBy('type')
            ->pluck('total_amount', 'type')
            ->toArray();

        // 5. Top Performing Sellers
        $topSellers = Invoice::where('status', 'paid')
            ->where('created_at', '>=', $startDate)
            ->whereNotNull('seller_id')
            ->selectRaw('seller_id, count(*) as sales_count, sum(total) as sales_sum_total')
            ->groupBy('seller_id')
            ->orderByDesc('sales_sum_total')
            ->take(5)
            ->with('seller')
            ->get();

        return view('livewire.admin.admin-analytics', [
            'period' => $this->period,
            'gmv' => $gmv,
            'platformRevenue' => $platformRevenue,
            'paymentsVolume' => $paymentsVolume,
            'paidInvoicesCount' => $paidInvoicesCount,
            'averageOrderValue' => $averageOrderValue,
            'newUsersCount' => $newUsersCount,
            'newListingsCount' => $newListingsCount,
            'newSubscriptionsCount' => $newSubscriptionsCount,
            'chartData' => $chartData,
            'revenueByType' => $revenueByType,
            'topSellers' => $topSellers,
        ]);
    }
}
