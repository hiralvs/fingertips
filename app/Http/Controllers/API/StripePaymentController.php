<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Stripe;
use App\User;

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
    public function stripeCharge(Request $request)
    {
        Stripe\Stripe::setApiKey(env('STRIPE_SECRET'));
        $user = User::find($request->user_id);
        if($user)
        {
            $customer_id = $user->customer_id;

            try 
            {
                $charge =  \Stripe\Charge::create ([
                        "amount" => $request->amount,
                        "currency" => "SGD",
                        "source" => $request->card_id,
                        "description" =>$request->description, 
                        "customer" => $customer_id
                ]);

                if($charge)
                {
                    $response = ['success' => true,'message' => 'Payment successful','status' => 200,"data"=>$charge];  
                }
                else
                {
                    $response = ['success' => false,'message' => 'Payment failed','status' => 404,"data"=>[]];  
                }
            }
            catch (\Exception $ex) {
                $response = ['success' => false,'message' => $ex->getMessage(),'status' => 404,"data"=>[]];
            }
        }
        return response()->json($response);    
    }

    /* Function used to get card token and then create card from token and customer id*/
    public function stripeCard(Request $request)
    {
        $user = User::find($request->user_id);
        if($user)
        {
            $customer_id = $user->customer_id;

            try {

                $cardToken = \Stripe\Token::create([
                  'card' => [
                    'number' => $request->number,
                    'exp_month' => $request->exp_month,
                    'exp_year' => $request->exp_year,
                    'cvc' => $request->cvc,
                  ],
                ]);

                if($cardToken->id)
                {
                    try
                    {
                        $card = \Stripe\Customer::createSource(
                        $customer_id,
                        ['source' => $cardToken->id]
                        );
                        
                        if($card)
                        {
                            $response = ['success' => true,'message' => 'Card created successfuly','status' => 200,"data"=>$card];  
                        }
                        else
                        {
                            $response = ['success' => false,'message' => 'Card not created successfuly','status' => 404,"data"=>[]];  
                        }
                    }
                    catch (\Exception $ex) {
                        $response = ['success' => false,'message' => $ex->getMessage(),'status' => 404,"data"=>[]];
                    }
                    
                }
            } catch (\Exception $ex) {
                $response = ['success' => false,'message' => $ex->getMessage(),'status' => 404,"data"=>[]];
            }
        }
        else
        {
            $response = ['success' => false,'message' => 'User not found','status' => 404,"data"=>[]];
        }
        return response()->json($response);    
    }


    /* Function used to retreive cutomer card details*/
    public function retrieveStripeCustomer(Request $request)
    {
        $user = User::find($request->user_id);
        if($user)
        {
            $customer_id = $user->customer_id;

            try {

                $getCustomer = \Stripe\Customer::retrieve($customer_id);

                if($getCustomer)
                {
                    $response = ['success' => true,'message' => 'Customer data found successfuly','status' => 200,"data"=>$getCustomer];  
                }
                else
                {
                    $response = ['success' => false,'message' => 'Customer data not found','status' => 404,"data"=>[]];  
                }

            } catch (\Exception $ex) {
                $response = ['success' => false,'message' => $ex->getMessage(),'status' => 404,"data"=>[]];
            }
        }
        else
        {
            $response = ['success' => false,'message' => 'User not found','status' => 404,"data"=>[]];
        }
        return response()->json($response);    
    }

    /* Function used to delete card*/
    public function deleteCard(Request $request)
    {
        $user = User::find($request->user_id);
        if($user)
        {
            $customer_id = $user->customer_id;
            $card_id =  $request->card_id;

            try {

                $deleteCard = \Stripe\Customer::deleteSource($customer_id,$card_id);

                if($deleteCard)
                {
                    $response = ['success' => true,'message' => 'Card deleted successfully','status' => 200,"data"=>$deleteCard];  
                }
                else
                {
                    $response = ['success' => false,'message' => 'Card not deleted','status' => 404,"data"=>[]];  
                }

            } catch (\Exception $ex) {
                $response = ['success' => false,'message' => $ex->getMessage(),'status' => 404,"data"=>[]];
            }
        }
        else
        {
            $response = ['success' => false,'message' => 'User not found','status' => 404,"data"=>[]];
        }
        return response()->json($response);    
    }
    
}
