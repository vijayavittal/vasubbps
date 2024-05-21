<?php


namespace App;



use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;

class InstantPay
{
    private $token,$outletId;

    /**
     * InstantPay constructor.
     * @return void
     * @param void
     */
    public function __construct()
    {
        $this->token =  env('INSTANTPAY_TOKEN');
    }

    /**
     *
     * @param $phoneNumber
     * @return bool
     */
    public function registrationOTP($phoneNumber)
    {
        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => "https://www.instantpay.in/ws/outlet/registrationOTP",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS =>"{\n  \"token\" : \"".$this->token."\",\n  \"request\" : \n  {\n    \"mobile\" : \"".$phoneNumber."\"\n  }\n}",
            CURLOPT_HTTPHEADER => array(
                "Content-Type: application/json"
            ),
        ));

        $response = curl_exec($curl);

        curl_close($curl);

        $xml = simplexml_load_string($response);

        try {
            if($xml->status == "OTP Sent successfully"){
                return true;
            }else{
                return false;
            }
        }catch (\ErrorException $e){
            return false;
        }
    }

    /**
     * @param array $request
     * @return array $response
     */
    public function registration(array $request)
    {
        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => "https://www.instantpay.in/ws/outlet/registration",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS =>"{\t\"token\" : \"".$request['token']."\",\r\n\t\"request\" : {\r\n\t\t\"mobile\" : \"".$request['phone']."\",\r\n\t\t\"email\" : \"".$request['email']."\",\r\n\t\t\"company\" : \"".$request['company']."\",\r\n\t\t\"name\" : \"".$request['name']."\",\r\n\t\t\"pan\" : \"".$request['pan']."\",\r\n\t\t\"pincode\" : \"".$request['pincode']."\",\r\n\t\t\"address\" : \"".$request['address']."\",\r\n\t\t\"otp\" : \"".$request['otp']."\"\r\n}\r\n}",
            CURLOPT_HTTPHEADER => array(
                "Content-Type: application/json"
            ),
        ));

        $response = curl_exec($curl);

        curl_close($curl);

        $xml = simplexml_load_string($response);

        try {
            if ($xml->status == "Outlet Successfully Registered" || $xml->status == "Outlet already exists") {
                return array('status'=>true,'data'=>$xml->data[0]->outlet_id);
            } else {
                if($xml->statuscode == "ERR" || $xml->statuscode == "EOP"){
                    return array('status'=>false,'data'=>$xml->status);
                }
            }
        }catch (\ErrorException $e){
            return array('status'=>false,'data'=>$e->getMessage());
        }
    }
}
