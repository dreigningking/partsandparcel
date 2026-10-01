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
        $planId = $payment->metadata['plan_id'] ?? 1;

        if ($payment->status === 'successful') {
            if ($isSubscription) {
                return redirect()->route('dashboard')->with('success', 'Your subscription is already active!');
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

        return $this->redirectAfterPayment($payment, 'Payment verification pending or failed.', false);
    }

    protected function redirectAfterPayment(Payment $payment, string $message, bool $success = true): RedirectResponse
    {
        $statusKey = $success ? 'success' : 'error';

        if ($payment->invoice_id) {
            return redirect()->route('invoices')->with($statusKey, $message);
        }

        if ($payment->subscription_id || ($payment->metadata['payment_type'] ?? '') === 'subscription') {
            return redirect()->route('subscriptions')->with($statusKey, $message);
        }

        return redirect()->route('dashboard')->with($statusKey, $message);
    }
}
