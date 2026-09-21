<?php

namespace App\Http\Controllers;

use App\Jobs\ConfirmPaymentJob;
use App\Models\Payment;
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
        $provider = $request->query('provider', 'paystack');
        $reference = $request->query('reference') ?? $request->query('trxref') ?? $request->query('tx_ref');

        if (! $reference) {
            return redirect()->route('dashboard')->with('error', 'Payment reference missing.');
        }

        $payment = Payment::where('reference', $reference)->first();

        if (! $payment) {
            return redirect()->route('dashboard')->with('error', 'Payment transaction not found.');
        }

        if ($payment->status === 'successful') {
            return $this->redirectAfterPayment($payment, 'Payment was successful!');
        }

        // Verify synchronously on return
        $result = ($provider === 'flutterwave')
            ? $flutterwaveService->verify($reference)
            : $paystackService->verify($reference);

        if (! empty($result['success'])) {
            $escrowService->handlePaymentSuccessful($payment, $result);
            return $this->redirectAfterPayment($payment, 'Payment successfully confirmed!');
        }

        return $this->redirectAfterPayment($payment, 'Payment verification pending or failed.', false);
    }

    protected function redirectAfterPayment(Payment $payment, string $message, bool $success = true): RedirectResponse
    {
        $statusKey = $success ? 'success' : 'error';

        if ($payment->invoice_id) {
            return redirect()->route('invoices')->with($statusKey, $message);
        }

        if ($payment->subscription_id) {
            return redirect()->route('subscriptions')->with($statusKey, $message);
        }

        return redirect()->route('dashboard')->with($statusKey, $message);
    }
}
