<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Stripe;

class StripePaymentController extends Controller
{
    public function __construct()
    {
        Stripe\Stripe::setApiKey(env('STRIPE_SECRET'));
    }
     /**
     * success response method.
     *
     * @return \Illuminate\Http\Response
     */
    public function stripePost(Request $request)
    {
        Stripe\Stripe::setApiKey(env('STRIPE_SECRET'));
        Stripe\Charge::create ([
                "amount" => 1,
                "currency" => "usd",
                "source" => $request->stripeToken,
                "description" => "Test payment from itsolutionstuff.com." 
        ]);
  
        Session::flash('success', 'Payment successful!');
          
        return back();
    }

    public function stripeCard(Request $request)
    {
        $user = auth()->user();
        if($user)
        {
            $customer_id = $user->customer_id;

            try {

                $card = \Stripe\Customer::createSource(
                    $customer_id,
                [
                    'source.object' => 'card',
                    'source.number' => '4242 4242 4242 4242',
                    'source.exp_month' => 'card',
                    'source.exp_year' => 'card',
        
                ]
                );
            } catch (\Exception $ex) {
                return $ex->getMessage();
            }
        }
        
       
    }
}
