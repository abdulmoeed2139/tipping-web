<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FrontentController extends Controller
{
    public function homePage()
    {
        return view('innerpages.home');
    }

    public function onetimePayment()
    {
        $lastSuccess = Order::where('user_id', Auth::id())
                    ->where('status', 'paid')
                    ->latest('created_at')
                    ->first();

        $nextAutoAmount = null;

        if ($lastSuccess) {
            // Agar last success mila hai to +1
            $nextAutoAmount = $lastSuccess->amount + 1;
        }

        return view('innerpages.one-time-payment', compact('nextAutoAmount'));
    }

    public function selectMethod()
    {
        return view('innerpages.method-selection');
    }

    public function invoice()
    {
        return view('innerpages.invoice');
    }

    public function unsuccessfulPayment()
    {
        return view('innerpages.unsuccessful-payment');
    }

    public function paymentSuccessful()
    {
        return view('innerpages.paymentSuccessful');
    }




}
