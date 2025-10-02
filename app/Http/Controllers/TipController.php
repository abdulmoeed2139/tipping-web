<?php

namespace App\Http\Controllers;

use QrCode;
use App\Models\Order;
use App\Models\LinkAmount;
use App\Services\NowPaymentsService;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TipController extends Controller
{

    private function getPaymentLimits()
    {
        return [
            'minimum' => config('app.minimum_order', 5),
            'maximum' => config('app.maximum_order', 9999),
        ];
    }
    public function generateLink(Request $request)
    {
        $limits = $this->getPaymentLimits();
        
        $request->validate([
            'amount' => "required|numeric|min:{$limits['minimum']}|max:{$limits['maximum']}",
        ]);
        
        $amount = $request->amount;
        $token = Str::random(32);

        try {
            $order = Order::create([
                'amount' => $amount,
                'user_id' => Auth::id(),
                'status' => 'pending',
            ]);

            $link = LinkAmount::create([
                'token' => $token,
                'amount' => $amount,
                'user_id' => Auth::id(),
                'order_id' => $order->id,
            ]);

            $url = url('/paylink/' . $token);
            $qr = QrCode::size(200)->generate($url);

            return response()->json([
                'success' => true,
                'url' => $url,
                'qr' => $qr
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to generate payment link: ' . $e->getMessage(),
                'error_type' => 'database_error'
            ], 500);
        }
    }

    // // TipController.php
    // public function validateLink($token)
    // {
    //     $link = LinkAmount::where('token', $token)
    //         ->where('created_at', '>=', now()->subMinutes(30))
    //         ->first();

    //     if (!$link) {
    //         return redirect('/tip')->with('error', '❌ This payment link has expired. Please generate a new one.');
    //     }

    //     return redirect('/select-merchant')->with([
    //         'expired' => false,
    //         'amount' => $link->amount,
    //     ]);
    // }

    public function validateLink($token)
    {
        $link = LinkAmount::where('token', $token)
            ->where('created_at', '>=', now()->subHours(24)) // Extended to 24 hours
            ->first();
            
        if (!$link) {
            return redirect('/tip')->with('error', '❌ This payment link has expired or is invalid. Please generate a new one.');
        }

        // Check if the associated order exists and is still pending
        if (!$link->order || $link->order->status !== 'pending') {
            return redirect('/tip')->with('error', '❌ This payment link is no longer valid. The order may have been completed or cancelled.');
        }

        return redirect('/pay/' . $link->order->id)->with([
            'expired' => false,
            'amount' => $link->amount,
        ]);
    }


    public function createOrder(Request $request)
    {
        $limits = $this->getPaymentLimits();
        
        $request->validate([
            'amount' => "required|numeric|min:{$limits['minimum']}|max:{$limits['maximum']}",
        ]);

        if($request->amount < $request->previousAmount){
            return redirect()->back()->with('error', 'Amount previous amount se kam nahi ho sakta');
        }

        $order = Order::create([
            'amount' => $request->amount,
            'user_id' => Auth::id(),
            'status' => 'pending',
        ]);

        return redirect('/pay/' . $order->id);
    }

    public function pay(Order $order)
    {
        return view('innerpages.method-selection', compact('order'));
    }

    public function checkout(Order $order)
    {
        return view('innerpages.invoice', compact('order'));
    }

    public function paymentSuccessful(Order $order)
    {
        $order->update(['status' => 'paid']);
        return view('innerpages.paymentSuccessful', compact('order'));
    }

    /**
     * Generate PDF invoice for the order
     */
    public function generateInvoice(Order $order)
    {
        $order->load('user');
        
        $data = [
            'order' => $order,
            'invoice_number' => 'INV-' . str_pad($order->id, 6, '0', STR_PAD_LEFT),
            'generated_at' => now()->format('M d, Y H:i'),
        ];
        
        return view('innerpages.invoice-pdf', compact('data'));
    }

    public function unsuccessfulPayment()
    {
        return view('innerpages.unsuccessful-payment');
    }

    /**
     * Create crypto payment
     */
    public function createCryptoPayment(Request $request)
    {
        $limits = $this->getPaymentLimits();
        
        $request->validate([
            'amount' => "required|numeric|min:{$limits['minimum']}|max:{$limits['maximum']}",
            'crypto_currency' => 'required|string|in:btc,eth,usdt,usdc,bnb,xrp,ada,sol,doge,ltc',
        ]);

        $nowPayments = new NowPaymentsService();
        
        // Get the correct NOWPayments currency code
        $supportedCrypto = $nowPayments->getSupportedCrypto();
        $cryptoCode = $supportedCrypto[$request->crypto_currency]['nowpayments_code'] ?? strtoupper($request->crypto_currency);
        
        // Get estimated crypto amount
        $estimate = $nowPayments->getEstimatedAmount(
            $request->amount, 
            'usd', 
            $cryptoCode
        );

        // Check if estimate failed
        if (!$estimate || isset($estimate['error'])) {
            $errorMessage = isset($estimate['error']) ? $estimate['error'] : 'Unable to get crypto conversion rate. Please try again.';
            
            // Add more specific error information
            if (isset($estimate['currency_from']) && isset($estimate['currency_to'])) {
                $errorMessage .= " (From: {$estimate['currency_from']} to {$estimate['currency_to']})";
            }
            
            return response()->json([
                'success' => false,
                'message' => $errorMessage,
            ], 400);
        }

        $order = Order::create([
            'amount' => $request->amount,
            'user_id' => Auth::id(),
            'status' => 'pending',
            'payment_method' => 'crypto',
            'crypto_currency' => $request->crypto_currency,
            'crypto_amount' => $estimate['estimated_amount'],
            'payment_expires_at' => now()->addMinutes(30),
        ]);

        // Create NOWPayments payment
        $paymentData = [
            'price_amount' => $request->amount,
            'price_currency' => 'usd',
            'pay_currency' => $cryptoCode, // Use the correct NOWPayments currency code
            'order_id' => $order->id,
            'order_description' => 'Tip payment for order #' . $order->id,
            'ipn_callback_url' => url('/api/nowpayments/callback'),
        ];

        $payment = $nowPayments->createPayment($paymentData);
        
        if (!$payment) {
            $order->update(['status' => 'failed']);
            return response()->json([
                'success' => false,
                'message' => 'Failed to create crypto payment.',
            ], 400);
        }

        // Check for NOWPayments API errors
        if (isset($payment['error'])) {
            $order->update(['status' => 'failed']);
            return response()->json([
                'success' => false,
                'message' => $payment['error'],
            ], 400);
        }

        // Update order with NOWPayments data
        $order->update([
            'nowpayments_payment_id' => $payment['payment_id'],
            'payment_address' => $payment['pay_address'],
            'payment_qr_code' => $payment['pay_url'] ?? null,
            'pay_amount' => $payment['pay_amount'] ?? null,
            'amount_received' => $payment['amount_received'] ?? 0,
            'pay_currency' => $payment['pay_currency'] ?? null,
            'price_currency' => $payment['price_currency'] ?? null,
            'network' => $payment['network'] ?? null,
            'payment_status' => $payment['payment_status'] ?? 'waiting',
            'valid_until' => isset($payment['valid_until']) ? \Carbon\Carbon::parse($payment['valid_until']) : null,
            'expiration_estimate_date' => isset($payment['expiration_estimate_date']) ? \Carbon\Carbon::parse($payment['expiration_estimate_date']) : null,
            'is_fixed_rate' => $payment['is_fixed_rate'] ?? false,
            'is_fee_paid_by_user' => $payment['is_fee_paid_by_user'] ?? false,
            'type' => $payment['type'] ?? null,
            'product' => $payment['product'] ?? null,
        ]);

        return response()->json([
            'success' => true,
            'redirect_url' => url('/crypto-pay/' . $order->id)
        ]);
    }

    /**
     * Show crypto payment page
     */
    public function cryptoPay(Order $order)
    {
        if ($order->isPaymentExpired()) {
            return redirect('/tip')->with('error', 'Payment has expired. Please create a new payment.');
        }

        return view('innerpages.crypto-payment', compact('order'));
    }

    /**
     * Check crypto payment status
     */
    public function checkCryptoPaymentStatus(Order $order)
    {
        $nowPayments = new NowPaymentsService();
        $status = $nowPayments->getPaymentStatus($order->nowpayments_payment_id);

        if ($status && isset($status['payment_status'])) {
            if ($status['payment_status'] === 'finished') {
                $order->update(['status' => 'paid']);
                return response()->json(['status' => 'paid']);
            }
            return response()->json(['status' => $status['payment_status']]);
        }

        return response()->json(['status' => 'pending']);
    }

    /**
     * NOWPayments IPN callback
     */
    public function nowPaymentsCallback(Request $request)
    {
        $signature = $request->header('x-nowpayments-sig');
        $payload = $request->getContent();

        $nowPayments = new NowPaymentsService();
        
        if (!$nowPayments->verifyIpnSignature($payload, $signature)) {
            return response()->json(['error' => 'Invalid signature'], 400);
        }

        $data = json_decode($payload, true);
        
        $order = Order::where('nowpayments_payment_id', $data['payment_id'])->first();
        
        if ($order) {
            // Update order with latest NOWPayments data
            $updateData = [
                'payment_status' => $data['payment_status'] ?? $order->payment_status,
                'amount_received' => $data['amount_received'] ?? $order->amount_received,
            ];
            
            // Update order status if payment is finished
            if (isset($data['payment_status']) && $data['payment_status'] === 'finished') {
                $updateData['status'] = 'paid';
            }
            
            $order->update($updateData);
            
            \Log::info('NOWPayments IPN Callback', [
                'payment_id' => $data['payment_id'],
                'payment_status' => $data['payment_status'] ?? 'unknown',
                'amount_received' => $data['amount_received'] ?? 0,
                'order_id' => $order->id,
                'order_status' => $order->status
            ]);
        }

        return response()->json(['status' => 'ok']);
    }

    /**
     * Create fiat payment (Visa/Mastercard/Apple Pay)
     */
    public function createFiatPayment(Request $request)
    {
        $limits = $this->getPaymentLimits();
        
        $request->validate([
            'amount' => "required|numeric|min:{$limits['minimum']}|max:{$limits['maximum']}",
            'fiat_method' => 'required|string|in:visa,mastercard,apple_pay',
        ]);

        $nowPayments = new NowPaymentsService();

        $order = Order::create([
            'amount' => $request->amount,
            'user_id' => Auth::id(),
            'status' => 'pending',
            'payment_method' => 'fiat',
            'payment_expires_at' => now()->addMinutes(30),
        ]);

        // Create NOWPayments fiat payment
        $paymentData = [
            'price_amount' => $request->amount,
            'price_currency' => 'USD',
            'pay_currency' => 'usd', // This enables fiat payments
            'order_id' => $order->id,
            'order_description' => 'Tip payment for order #' . $order->id,
            'success_url' => url('/fiat-payment-success/' . $order->id),
            'cancel_url' => url('/fiat-payment-cancel/' . $order->id),
            'ipn_callback_url' => url('/api/nowpayments/callback'),
        ];

        $payment = $nowPayments->createFiatPayment($paymentData);

        if (!$payment) {
            $order->update(['status' => 'failed']);
            return response()->json([
                'success' => false,
                'message' => 'Failed to create fiat payment',
            ], 400);
        }

        // Check for NOWPayments API errors
        if (isset($payment['error'])) {
            $order->update(['status' => 'failed']);
            return response()->json([
                'success' => false,
                'message' => $payment['error'],
            ], 400);
        }

        // Update order with NOWPayments data
        $order->update([
            'nowpayments_payment_id' => $payment['payment_id'],
            'payment_address' => $payment['pay_address'] ?? null,
            'payment_qr_code' => $payment['pay_url'] ?? null,
        ]);

        return response()->json([
            'success' => true,
            'redirect_url' => $payment['pay_url'] ?? url('/fiat-pay/' . $order->id)
        ]);
    }

    /**
     * Show fiat payment page
     */
    public function fiatPay(Order $order)
    {
        if ($order->isPaymentExpired()) {
            return redirect('/tip')->with('error', 'Payment has expired. Please create a new payment.');
        }

        return view('innerpages.fiat-payment', compact('order'));
    }

    /**
     * Fiat payment success page
     */
    public function fiatPaymentSuccess(Order $order)
    {
        $order->update(['status' => 'paid']);
        return view('innerpages.paymentSuccessful', compact('order'));
    }

    /**
     * Fiat payment cancel page
     */
    public function fiatPaymentCancel(Order $order)
    {
        $order->update(['status' => 'cancelled']);
        return view('innerpages.unsuccessful-payment', compact('order'));
    }

    /**
     * Regenerate payment link for an existing order
     */
    public function regenerateLink(Request $request)
    {
        $request->validate([
            'order_id' => 'required|exists:orders,id',
        ]);

        $order = Order::findOrFail($request->order_id);
        
        // Check if user owns this order
        if ($order->user_id !== Auth::id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        // Check if order is still pending
        if ($order->status !== 'pending') {
            return response()->json(['error' => 'Order is no longer pending'], 400);
        }

        // Generate new token
        $newToken = Str::random(32);
        
        // Update or create new link
        $link = LinkAmount::updateOrCreate(
            ['order_id' => $order->id],
            [
                'token' => $newToken,
                'amount' => $order->amount,
                'user_id' => Auth::id(),
            ]
        );

        $url = url('/paylink/' . $newToken);
        $qr = QrCode::size(200)->generate($url);

        return response()->json([
            'success' => true,
            'url' => $url,
            'qr' => $qr,
            'message' => 'Payment link regenerated successfully'
        ]);
    }

    /**
     * Get supported cryptocurrencies
     */
    public function getSupportedCrypto()
    {
        $nowPayments = new NowPaymentsService();
        return response()->json($nowPayments->getSupportedCrypto());
    }

    /**
     * Get supported fiat payment methods
     */
    public function getSupportedFiatMethods()
    {
        $nowPayments = new NowPaymentsService();
        return response()->json($nowPayments->getSupportedFiatMethods());
    }
}
