<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;

class EsewaController extends Controller
{
    public function initiate(Request $request)
    {
        $plan = $request->plan ?? 'annually';

        $plans = [
            'monthly'         => ['label' => 'Monthly',       'amount' => 999],
            'semi-annually'   => ['label' => 'Semi-Annually',  'amount' => 5094],
            'annually'        => ['label' => 'Annually',       'amount' => 7188],
        ];

        $selected = $plans[$plan];
        $transactionId = 'TXN_' . Auth::id() . '_' . time();
        session([
            'plan'         => $plan,
            'esewa_amount' => $selected['amount'],
            'esewa_txn_id' => $transactionId,
        ]);
        $message = $this->generateSignature($selected['amount'], $transactionId);

        return view('pages.dashboard.payment.esewa', [
            'amount'         => $selected['amount'],
            'transactionId'  => $transactionId,
            'signature'      => $message,
            'plan'           => $plan,
        ]);
    }

    public function verify(Request $request)
    {
        $data = base64_decode($request->data);
        $data = json_decode($data, true);

        if ($data['status'] !== 'COMPLETE') {
            return redirect()->route('subscription')->with('error', 'Payment failed or cancelled.');
        }

        // Fix #25: verify eSewa's response signature before trusting any field in $data
        $signedFields = explode(',', $data['signed_field_names'] ?? '');
        $signatureMessage = implode(',', array_map(
            fn($field) => "{$field}={$data[$field]}",
            $signedFields
        ));
        $expectedSignature = base64_encode(
            hash_hmac('sha256', $signatureMessage, config('services.esewa.secret_key'), true)
        );
        if (!hash_equals($expectedSignature, $data['signature'] ?? '')) {
            return redirect()->route('subscription')->with('error', 'Payment verification failed.');
        }

        // Fix #26: cross-check returned amount against what was set at initiation
        $expectedAmount = session('esewa_amount');
        if (!$expectedAmount || (float) $data['total_amount'] !== (float) $expectedAmount) {
            return redirect()->route('subscription')->with('error', 'Payment amount mismatch.');
        }

        // Verify with eSewa
        $response = Http::get(config('services.esewa.verify_url'), [
            'product_code'     => config('services.esewa.product_code'),
            'total_amount'     => $data['total_amount'],
            'transaction_uuid' => $data['transaction_uuid'],
        ]);

        if ($response->successful() && $response->json('status') === 'COMPLETE') {
            $user = Auth::user();

            $ends = match (session('plan', 'annually')) {
                'monthly'       => now()->addMonth(),
                'semi-annually' => now()->addMonths(6),
                'annually'      => now()->addYear(),
            };

            // If user already has an active subscription, extend from existing renewal date
            $latestTransaction = $user->transactions()->where('status', 'completed')->latest()->first();
            if ($latestTransaction && $latestTransaction->renewal_at > now()) {
                $ends = match (session('plan', 'annually')) {
                    'monthly'       => $latestTransaction->renewal_at->copy()->addMonth(), //this prevents Carbon from mutating the original date object when calling addMonth() on it.
                    'semi-annually' => $latestTransaction->renewal_at->copy()->addMonths(6),
                    'annually'      => $latestTransaction->renewal_at->copy()->addYear(),
                };
            }

            $transaction = \App\Models\Transaction::firstOrCreate(
                ['transaction_id' => $data['transaction_uuid']],
                [
                    'user_id'        => $user->id,
                    'plan'           => session('plan', 'annually'),
                    'amount'         => $data['total_amount'],
                    'payment_method' => 'esewa',
                    'status'         => 'completed',
                    'renewal_at'     => $ends,
                ]
            );

            if ($transaction->wasRecentlyCreated) {
                $user->update(['subscription_type' => 'premium']);
            }

            return redirect()->route('subscription')->with('success', 'Payment successful! You are now a premium member.');
        }

        return redirect()->route('subscription')->with('error', 'Payment verification failed.');
    }

    private function generateSignature($amount, $transactionId)
    {
        $message = "total_amount={$amount},transaction_uuid={$transactionId},product_code=" . config('services.esewa.product_code');
        $secret  = config('services.esewa.secret_key');
        $hash    = hash_hmac('sha256', $message, $secret, true);
        return base64_encode($hash);
    }
}
