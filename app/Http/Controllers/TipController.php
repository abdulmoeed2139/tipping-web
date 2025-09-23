<?php

namespace App\Http\Controllers;

use QrCode;
use App\Models\Order;
use App\Models\LinkAmount;
use Illuminate\Support\Str;
use Illuminate\Http\Request;

class TipController extends Controller
{
    public function generateLink(Request $request)
    {
        $amount = $request->amount;
        $token = Str::random(32);
        $link = LinkAmount::create([
            'token' => $token,
            'amount' => $amount,
        ]);

        $url = url('/paylink/' . $token);
        $qr = QrCode::size(200)->generate($url);

        return response()->json([
            'success' => true,
            'url' => $url,
            'qr' => $qr
        ]);
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
            ->where('created_at', '>=', now()->subMinutes(30))
            ->first();
        if (!$link) {
            return redirect('/tip')->with('error', '❌ This payment link has expired. Please generate a new one.');
        }
        
        $order = Order::create([
            'amount' => $link->amount,
            'status' => 'pending',
        ]);

        return redirect('/pay/' . $order->id)->with([
            'expired' => false,
            'amount' => $link->amount,
        ]);
    }


    public function createOrder(Request $request)
    {
        $request->validate([
            'amount' => 'required|numeric|min:5|max:9999',
        ]);

        $order = Order::create([
            'amount' => $request->amount,
            'status' => 'pending',
        ]);

        // Ab direct pay/{order_id} pe bhej do
        return redirect('/pay/' . $order->id);
    }

    public function pay(Order $order)
    {
        // Is page pe user ko merchant selection / payment method show karoge
        return view('innerpages.method-selection', compact('order'));
    }

    public function checkout(Order $order)
    {
        // Yahan tum invoice ya checkout page dikha sakte ho
        return view('innerpages.invoice', compact('order'));
    }

    public function paymentSuccessful(Order $order)
    {
        return view('innerpages.paymentSuccessful', compact('order'));
    }

    public function unsuccessfulPayment()
    {
        return view('innerpages.unsuccessful-payment');
    }
}
