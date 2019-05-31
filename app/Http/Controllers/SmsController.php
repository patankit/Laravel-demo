<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Twilio\Jwt\ClientToken;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Client;

class SmsController extends Controller
{
    protected $code, $smsVerifcation;

    function __construct()
    {
        $this->smsVerifcation = new \App\SmsVerification();
    }

    public function index(){
        echo "tesat";die;
    }

    public function store(Request $request)
    {
        $code = rand(1000, 9999);
        echo $code;die;
        $request['code'] = $code;
        $this->smsVerifcation->store($request);
        $this->sendSms($request);
    }

    public function sendSms($request)
    {
        $accountId = config('app.twilio')['TWILIO_ACCOUNT_SID'];
        $authToken = config('app.twilio')['TWILIO_AUTH_TOKEN'];

        try{
            $client = new client(['auth' => [$accountId, @$authToken]]);
//            $result = $client->post('https://api.twilio.com/2010-04-01/Accounts/'.$accountId.'/Messages.json',[
//                'form_params' => [
//                    'Body' => 'CODE: '.$request->code,
//                    'to' => $request->contact_number,
//                    'from' => '+919428304149'
//                ]
//            ]);
            $result =  $client->messages->create(
            // Where to send a text message (your cell phone?)
                '+919428304149',
                array(
                    'from' => '+919428304149',
                    'body' => 'I sent this message in under 10 minutes!'
                )
            );
            return $result;
        }catch (\Exception $e){
            echo "Error: ".$e->getMessage();
        }

    }
    public function verifyContact(Request $request){
        $smsVerifcation = $this->smsVerifcation::where('contact_number','=', $request->contact_number)->latest()->first();
        if($request->code == $smsVerifcation->code)
        {
            return $smsVerifcation->updateModel($request);
            $msg["message"] = "verified";
            return msg;
        }
        else{
            $msg["message"] = "not verified";
            return $msg;
        }
    }

}
