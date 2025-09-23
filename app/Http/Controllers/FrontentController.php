<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class FrontentController extends Controller
{
    public function homePage()
    {
        return view('innerpages.home');
    }

    public function onetimePayment()
    {
        return view('innerpages.one-time-payment');
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
