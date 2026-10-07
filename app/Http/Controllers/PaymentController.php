<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Services\Commercial\SubscriptionService;
use App\Services\Payment\EscrowService;
use App\Services\Payment\FlutterwaveService;
use App\Services\Payment\PaystackService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    public function callback(
        Request $request,
        EscrowService $escrowService,
        PaystackService $paystackService,
        FlutterwaveService $flutterwaveService
    ): RedirectResponse {
        $provider = $request->query('provider') ?: config('services.payment.default_gateway', 'paystack');
        $reference = $request->query('reference') ?? $request->query('trxref') ?? $request->query('tx_ref');

        if (! $reference) {
            return redirect()->route('dashboard')->with('error', 'Payment reference is missing.');
        }

        $payment = Payment::where('reference', $reference)->first();

        if (! $payment) {
            if (str_starts_with($reference, 'SUB-')) {
                return redirect()->route('subscription-plans')->with('error', 'Subscription payment transaction not found.');
            }
            return redirect()->route('dashboard')->with('error', 'Payment transaction not found.');
        }

        $isSubscription = ($payment->metadata['payment_type'] ?? '') === 'subscription';
        $isPromotion = ($payment->metadata['payment_type'] ?? '') === 'promotion';
        $planId = $payment->metadata['plan_id'] ?? 1;

        if ($payment->status === 'successful') {
            if ($isSubscription) {
                return redirect()->route('dashboard')->with('success', 'Your subscription is already active!');
            }
            if ($isPromotion) {
                $listingId = $payment->metadata['listing_id'] ?? null;
                if ($listingId) {
                    return redirect()->route('mylisting.view', $listingId)->with('success', 'Your promotion payment was successful and campaign is active!');
                }
            }
            return $this->redirectAfterPayment($payment, 'Payment was successful!');
        }

        // Check for local mock verification in development
        $isMockSuccess = app()->isLocal() && (
            $request->query('mock_success') == 1 ||
            (empty(config("services.{$provider}.secret")) && ! $request->has('mock_fail'))
        );

        if ($isMockSuccess) {
            $result = [
                'success' => true,
                'status' => 'successful',
                'amount' => $payment->amount,
                'reference' => $reference,
            ];
        } else {
            // Verify synchronously on return
            $result = ($provider === 'flutterwave')
                ? $flutterwaveService->verify($reference)
                : $paystackService->verify($reference);
        }

        if (! empty($result['success'])) {
            if ($isSubscription) {
                $subscription = app(SubscriptionService::class)->activateSubscription($payment);
                return redirect()->route('dashboard')->with(
                    'success',
                    "Payment confirmed! Welcome to {$subscription->plan->name}. Your subscription is active until {$subscription->ends_at->format('M d, Y')}."
                );
            }

            if ($isPromotion) {
                $listingId = $payment->metadata['listing_id'] ?? null;
                $type = $payment->metadata['type'] ?? 'clicks';
                $quantity = (int) ($payment->metadata['target_count'] ?? $payment->metadata['quantity'] ?? 0);

                // Activate existing pending Promotion record or create new
                $promotionId = $payment->paymentable_id ?? ($payment->metadata['promotion_id'] ?? null);
                $promotion = $promotionId ? \App\Models\Promotion::find($promotionId) : null;

                if ($promotion) {
                    $promotion->update([
                        'status' => 'active',
                        'target_count' => $promotion->target_count ?: $quantity,
                    ]);
                } else {
                    $promotion = \App\Models\Promotion::create([
                        'user_id' => $payment->user_id,
                        'listing_id' => $listingId,
                        'type' => $type,
                        'target_count' => $quantity,
                        'achieved_count' => 0,
                        'status' => 'active',
                    ]);
                }

                $payment->update([
                    'paymentable_id' => $promotion->id,
                    'paymentable_type' => \App\Models\Promotion::class,
                    'status' => 'successful',
                    'paid_at' => now(),
                ]);

                if (! empty($payment->metadata['coupon_code'])) {
                    $coupon = \App\Models\Coupon::where('code', $payment->metadata['coupon_code'])->first();
                    $coupon?->recordUsage();
                }

                if ($listingId) {
                    return redirect()->route('mylisting.view', $listingId)->with(
                        'success',
                        "Promotion payment confirmed! Your campaign for " . number_format($quantity) . " promotional {$type} is now active."
                    );
                }
            }

            $escrowService->handlePaymentSuccessful($payment, $result);
            return $this->redirectAfterPayment($payment, 'Payment successfully confirmed!');
        }

        // Payment failed or verification unsuccessful
        $payment->update(['status' => 'failed']);

        if ($isSubscription) {
            $errorMessage = $result['message'] ?? 'Payment verification failed or transaction was cancelled. Please try again.';
            return redirect()->route('subscription.confirm', ['plan' => $planId])
                ->with('error', $errorMessage);
        }

        if ($isPromotion) {
            $listingId = $payment->metadata['listing_id'] ?? null;
            $errorMessage = $result['message'] ?? 'Payment verification failed or transaction was cancelled. Please try again.';
            if ($listingId) {
                return redirect()->route('mylisting.view', $listingId)->with('error', $errorMessage);
            }
        }

        return $this->redirectAfterPayment($payment, 'Payment verification pending or failed.', false);
    }

    protected function redirectAfterPayment(Payment $payment, string $message, bool $success = true): RedirectResponse
    {
        $statusKey = $success ? 'success' : 'error';

        if (($payment->metadata['payment_type'] ?? '') === 'promotion' && ! empty($payment->metadata['listing_id'])) {
            return redirect()->route('mylisting.view', $payment->metadata['listing_id'])->with($statusKey, $message);
        }

        if ($payment->invoice_id) {
            return redirect()->route('invoices')->with($statusKey, $message);
        }

        if ($payment->subscription_id || ($payment->metadata['payment_type'] ?? '') === 'subscription') {
            return redirect()->route('subscriptions')->with($statusKey, $message);
        }

        return redirect()->route('dashboard')->with($statusKey, $message);
    }
}
