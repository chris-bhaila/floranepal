<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;

class EsewaController extends Controller
{
    public function initiate(Request $request)
    {
        $plans = [
            'monthly'         => ['label' => 'Monthly',       'amount' => 999],
            'semi-annually'   => ['label' => 'Semi-Annually',  'amount' => 5094],
            'annually'        => ['label' => 'Annually',       'amount' => 7188],
        ];

        $plan = array_key_exists($request->plan, $plans) ? $request->plan : 'annually';

        $selected = $plans[$plan];
        // Plan is encoded in the UUID so verify() never needs session — eliminates race across tabs
        $transactionId = 'TXN_' . Auth::id() . '_' . time() . '_' . $plan;
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
        $plans = [
            'monthly'         => ['label' => 'Monthly',       'amount' => 999],
            'semi-annually'   => ['label' => 'Semi-Annually',  'amount' => 5094],
            'annually'        => ['label' => 'Annually',       'amount' => 7188],
        ];

        $raw = base64_decode($request->data ?? '', true);
        if ($raw === false) {
            return redirect()->route('subscription')->with('error', 'Invalid payment response.');
        }

        $data = json_decode($raw, true);
        if (!is_array($data)) {
            return redirect()->route('subscription')->with('error', 'Invalid payment response.');
        }

        if ($data['status'] !== 'COMPLETE') {
            return redirect()->route('subscription')->with('error', 'Payment failed or cancelled.');
        }

        // Verify eSewa's response signature before trusting any field in $data
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

        // Parse plan from the signed transaction_uuid (format: TXN_{userId}_{time}_{plan})
        $txnParts = explode('_', $data['transaction_uuid'] ?? '');
        $plan = $txnParts[3] ?? 'annually';
        if (!array_key_exists($plan, $plans)) {
            return redirect()->route('subscription')->with('error', 'Invalid payment plan.');
        }

        // Cross-check returned amount against the canonical plan price
        $expectedAmount = $plans[$plan]['amount'];
        if ((float) $data['total_amount'] !== (float) $expectedAmount) {
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

            $ends = match ($plan) {
                'monthly'       => now()->addMonth(),
                'semi-annually' => now()->addMonths(6),
                'annually'      => now()->addYear(),
            };

            // If user already has an active subscription, extend from the existing renewal date
            $latestTransaction = $user->transactions()->where('status', 'completed')->latest()->first();
            if ($latestTransaction && $latestTransaction->renewal_at > now()) {
                $ends = match ($plan) {
                    'monthly'       => $latestTransaction->renewal_at->copy()->addMonth(),
                    'semi-annually' => $latestTransaction->renewal_at->copy()->addMonths(6),
                    'annually'      => $latestTransaction->renewal_at->copy()->addYear(),
                };
            }

            $transaction = \App\Models\Transaction::firstOrCreate(
                ['transaction_id' => $data['transaction_uuid']],
                [
                    'user_id'        => $user->id,
                    'plan'           => $plan,
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
