<?php

namespace App\Http\Controllers;

use App\Agent;
use App\Pricing;
use App\Transaction;
use App\Integration;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;

class AgentController extends Controller
{
    

    public function dashboard(Request $request)
    {

        $users = '';
        if (Auth::user()->pan == 'XXXXXXXXXX') {
            if ($request->query('pan')){
                $pan = $request->query('pan');
            }else {
                $pan = '%';
            }
            $users = Agent::select('name','pan')->get();
        }else{
            $pan = Auth::user()->pan;
        }
            $services = Pricing::all();
            $analysis = [];
            $date = Carbon::now()->toDateString();
            foreach ($services as $service){
                $analy = [];
                $analy['name'] = $service->name;

                $analy['amount'] = Transaction::where('pan','like',$pan)->where('sp_key',$service->sp_key)->where('status','SUCCESS')->whereDate('created_at',$date)->sum('amount');
                array_push($analysis,$analy);
            }
            $amount = Transaction::where('pan','like',$pan)->where('status','SUCCESS')->whereDate('created_at',$date)->sum('amount');

            usort($analysis, function ($item1, $item2) {
                return $item2['amount'] <=> $item1['amount'];
            });

            $analysis  = array_slice($analysis,0,4,true);

            $total = [];
            $value = 0;
            for($i=0;$i<10;$i++){
                $date = Carbon::now()->subDays($i)->toDateString();
                $day = Carbon::now()->subDays($i)->toDateTimeString();
                $value = Transaction::where('pan','like',$pan)->whereDate('created_at',$date)->where('status','SUCCESS')->sum('amount') ;
                array_push($total,[$day,$value]);
            }


            return Inertia::render('Agent/Dashboard',[
                'total'=>$total,
                'top'=>$analysis,
                'amount'=>$amount,
                'users'=>$users
            ]);


    }

    public function getBillers(Request $request)
    {
        $request->validate([
            'name'=>'required'
        ]);

        $billers = Pricing::where('type',$request->input('name'))->get();

        return response()->json([
            'status'=>'success',
            'data'=>$billers
        ]);
    }

    public function getPortal(Request $request)
    {
        return Inertia::render('Agent/Portal');
    }

    public function getBalance()
    {
        $agent = Agent::where('id',Auth::id())->first();

        if ($agent != null){
            if ($agent->balance == 0){
                $zero = "0.00";
            }else{
                $zero = "$agent->balance";
            }
//            $balance =  array([ "balance" => $zero]);
            //verify the hash
            return \response()->json([
                "response_code"=> "TXN",
                "response_msg"=> "Transaction Successful",
                "transactions"=> $zero,
            ]) ;
        }else{
            return \response()->json([
                "response_code"=> "ERR",
                "response_msg"=> "Transaction Error",
            ]) ;
        }

    }

   

    public function fetchBill(Request $request)
    {
        
        // $user = Auth::user();
        // if ($user->balance < $request->input('bill_amount')){
        //     return response()->json([
        //         'status'=>'failure',
        //         'message'=>'Insufficient balance. Please recharge via AEPS.'
        //     ]);
        // }

        // $tranactionController = new TransactionController();
        // $transaction = $tranactionController->createTransaction($user->pan,$request->input('amount'),0,$request->input('sp_key'),$user->phone);
        // if($transaction == false){
        //     return response()->json([
        //         'status' => 'failure',
        //         'message'=> 'Transaction ID Failed'
        //     ]);
        // }

        // $paramList=array();
        // for($i=0;$i<count($request->input('params'));$i++){
        //     $paramName="param".($i+1);
        //     $paramList[$paramName] = $request->input('params')[$i]["value"];
        // }

        
        $user = Agent::whereId(Auth::id())->first();
      
     
        // if ($user->balance < $request->input('amount')){
        //     return response()->json([
        //         'status'=>'failure',
        //         'message'=>'Insufficient balance. Please recharge via AEPS.'
        //     ]);
        // }
        
       
        $pricing =  Pricing::where('sp_key',$request->input('sp_key'))->first();
     
       

     

        $paramList=array();
        for($i=0;$i<count($request->input('params'));$i++){
            $paramName="param".($i+1);
            $paramList[$paramName] = $request->input('params')[$i]["value"];
        }
   $amount = $request->input('amount');
        $validateID="";

//       
        // $url = '127.0.0.1:8080/v1/add/fetch';
        // For UAT
        $data =[
            "billDetails"=>[ 
          "billerId" =>$pricing->sp_key,
          "customerParams"=>[
      [
            "name" => $pricing->params,
            "value" => $amount,
      ],
        ]],
        "agentDetails"=>[ 
            "deviceTags"=>[ 
                [
            "name"=> "INITIATING_CHANNEL",
            "value" => "AGT",
        ],
            [
             "name" => "TERMINAL_ID",
            "value" => $user->outlet_id,
        ],
            [
            "name" => "POSTAL_CODE",
            "value" => $user->pincode,
        ],
            [
            "name" => "MOBILE",
            "value" => $user->phone,
        ],
            [
            "name" =>"GEOCODE",
            "value" => number_format($request->input("location")["lat"],4).",".number_format($request->input("location")["long"],4),
        ],
            ],

              "agentId" => "AM01YKS042INTU000001",
         
        ],
       
        "custDetails"=>[ 
            "mobileNo" => $user->phone,
            "customerTags"=>[  
                [
                "name" => "EMAIL",
                "value" => $user->email,
            ]
            ]
            ],
             "chId"=> 1,
    "isRealTimeFetch"=> true

               

           
          
        ];
   
      //For Prod
    //     $data =[
    //         "billDetails"=>[ 
    //       "billerId" =>$pricing->sp_key,
    //       "customerParams"=>[
    //   [
    //         "name" => $pricing->params,
    //         "value" => $amount,
    //   ],
    //     ]],
    //     "agentDetails"=>[ 
    //         "deviceTags"=>[ 
    //             [
    //         "name"=> "INITIATING_CHANNEL",
    //         "value" => "MOB",
    //     ],
    //         [
    //          "name" => "IMEI",
    //         "value" => "448674528976410",
    //     ],
    //         [
    //         "name" => "OS",
    //         "value" => "android",
    //     ],
    //         [
    //         "name" => "APP",
    //         "value" => "NPCIAPP",
    //     ],
    //         [
    //         "name" =>"IP",
    //         "value" => "124.170.23.28",
    //     ],
    //         ],

    //           "agentId" => "AM01AM32MOBU00000001",

    //     ],
    //     "custDetails"=>[ 
    //         "mobileNo" => $user->phone,
    //         "customerTags"=>[  
    //             [
    //             "name" => "EMAIL",
    //             "value" => $user->email,
    //         ]
    //         ]
    //         ],
    //    "chId" => 1,
    //    "isRealTimeFetch" => true,

           
          
    //     ];
   
        // $jsonData = json_encode($data);
        // $response = Http::withHeaders([
        //     'Content-Type' => 'application/json',
        // ])->post('127.0.0.1:8080/v1/add/fetch',$data);

        $usertoken = Integration::where('user_id',Auth::id())->first();
       
        $token = $usertoken->token;

       
        $jsonData = json_encode($data);
        $response = Http::withHeaders(['Authorization'=> $token
            ,'Content-Type' => 'application/json',
        ])->post(env('production_fetch'),$data);
        
       
            $array = json_decode($response,TRUE);
            return response()->json([
                "status"=>"success",
                "data" =>$array
            ]);

            // if($array['status'] === 'success'){
            //     return response()->json([
            //         "status"=>"success",
            //         "data" =>$array
            //     ]);

            // }else{
            //     return response()->json([
            //         "status"=>"failure",
            //         "message" =>"Something Went Wrong",
            //     ]);
            // }
           
           
       

       
    // if($array["status"] != "success"){
    //     return response()->json([
    //         "status"=>"failure",
    //         "message"=>$array["data"]["status"]
    //     ]);
    // }
   
  
        





    }

    public function payBill(Request $request)
    {
  
        

        $user = Agent::whereId(Auth::id())->first();
      
     
        // if ($user->balance < $request->input('amount')){
        //     return response()->json([
        //         'status'=>'failure',
        //         'message'=>'Insufficient balance. Please recharge via AEPS.'
        //     ]);
        // }
        
       
        $pricing =  Pricing::where('sp_key',$request->input('sp_key'))->first();
   

     

        $paramList=array();
        for($i=0;$i<count($request->input('params'));$i++){
            $paramName="param".($i+1);
            $paramList[$paramName] = $request->input('params')[$i]["value"];
        }
   $amount = $request->input('amount');
   $cash = $request->input('cash');
   $amt=$request->input('amt');
   $mobile=$request->input('mobile');
 
        $validateID="";


 $data =[
            "agentDetails"=>[ 
                "deviceTags"=>[ 
                    [
                "name"=> "INITIATING_CHANNEL",
                "value" => "AGT",
            ],
                [
                 "name" => "TERMINAL_ID",
                "value" => $user->outlet_id,
            ],
                [
                "name" => "POSTAL_CODE",
                "value" => $user->pincode,
            ],
                [
                "name" => "MOBILE",
                "value" => $user->phone,
            ],
                [
                "name" =>"GEOCODE",
                "value" => number_format($request->input("location")["lat"],4).",".number_format($request->input("location")["long"],4),
            ],
                ],
    
                  "agentId" => "AM01YKS042INTU000001",
    
            ],
            "amountDetails" => [
                "amount" => $request->cash,
                "currency" => "356",
                "custConvFee" => "0",
                "couCustConvFee" => "0"
            ],
            "billDetails"=>[ 
          "billerId" =>$pricing->sp_key,
          "customerParams"=>[
      [
            "name" => $pricing->params,
            "value" => $amount,
      ]
        ]],
        "chId" => 1,
        "custDetails"=>[ 
            "mobileNo" => $user->phone,
            "customerTags"=>[  
                [
                "name" => "EMAIL",
                "value" => $user->email,
            ]
            ]
            ],
        "paymentDetails" => [
            "quickPay" => "Yes",
            "splitPay" =>"No",
            "offusPay" => "No",
            "paymentMode"=> "Cash",

            "paymentInfo" => [
                [   
                "name" => "Remarks",
                "value" => "BillPayments"
            ]
            ]
            ],
       
     
       "refId"=> $request->refId,
       "clientRequestId" => ""

 ];


   // prod

//     $data =[
//         "agentDetails"=>[ 
//             "deviceTags"=>[ 
//                 [
//             "name"=> "INITIATING_CHANNEL",
//             "value" => "MOB",
//         ],
//             [
//              "name" => "IMEI",
//             "value" => "448674528976410",
//         ],
//             [
//             "name" => "OS",
//             "value" => "android",
//         ],
//             [
//             "name" => "APP",
//             "value" => "NPCIAPP",
//         ],
//         [
//             "name" => "IP",
//             "value" => "124.170.23.28",
//         ]
//             ],
    
//               "agentId" => "AM01AM32MOBU00000001",
    
//         ],
//         "amountDetails"=>[ 
//            "amount" => $request->cash,
//            "currency" => "356",
//            "custConvFee" => "0",
//            "couCustConvFee"=> "0"

//             ],
//         "billDetails"=>[ 
//       "billerId" =>$pricing->sp_key,
//       "customerParams"=>[
//   [
//         "name" => $pricing->params,
//         "value" => $amount,
//   ],
//     ]],
//     "chId" => 1,
//     "custDetails"=>[ 
//         "mobileNo" => $user->phone,
//         "customerTags"=>[  
//             [
//             "name" => "EMAIL",
//             "value" => $user->email,
//         ]
//         ]
//         ],
//         "paymentDetails" =>[
//             "quickPay" =>"No",
//         "splitPay"=> "No",
//         "offusPay"=> "Yes",
//         "paymentMode"=> "Credit Card",
//         "paymentInfo" =>[
//             [
//             "name"=> "CardNum|AuthCode",
//             // "value"=>"4336620020624963|123456"
//             "value"=>"4336620020624936|123456"
//             ]
//         ]
//         ],
   
//         "refId" => $request->refId,
//         "clientRequestId" => ""

       
      
//     ];

$usertoken = Integration::where('user_id',Auth::id())->first();
$token = $usertoken->token;

        //Convert data array to JSON format
         $jsonData = json_encode($data);
   
        $response = Http::withHeaders([
            'Content-Type' => 'application/json','Authorization' => $token
        ])->post(env('production_pay') ,$data);
        $array = json_decode($response,TRUE);

       
        // if($array["status"] != "failure"){
        //     return response()->json([
        //            'status' => 'failure',
        //           'message'=> $array['Msg']
        //     ]);
        // }

   
  

    // if ($array['status_code'] !== 200){
    //     return response()->json([
    //         'status'=>'failure',
    //         'message'=>$res['message']
    //     ]);
    // }
    
         
  
    $txnReferenceId = $array['clientResponse']['response']['txnReferenceId'];
    $txn_reference_id = $array['paymentResponse']['txn_reference_id'];
   
    

 $tranactionController = new TransactionController();
        $transaction = $tranactionController->createTransaction($user->pan,$cash,0,$request->input('sp_key'),$user->phone,$txnReferenceId,$txn_reference_id);
        if($transaction == false){
            return response()->json([
                'status' => 'failure',
                'message'=> 'Transaction ID Failed'
            ]);
        }
        return response()->json([
            "status"=>"success",
            "data"=>$array
          ]);
    
   
   
        // $Agent_balance =   Agent::whereId(Auth::id())->first();
        // $Agentid = Agent::whereId(Auth::id())->first();
        // dd($Agentid);
        // $balance = $Agent_balance->balance;
        // $net_balance = $balance - $cash;
       
        //    Agent::where('id',$Agentid)->update([
        //      'balance' =>$net_balance
        //    ]);
 
  if($array["status"] != "success"){
    return response()->json([
        "status"=>"failure",
        "message"=>$array["data"]["status"]
    ]);
}
  }   

  public function payBillDouble(Request $request)
    {
        $user = Agent::whereId(Auth::id())->first();
      
     
        // if ($user->balance < $request->input('amount')){
        //     return response()->json([
        //         'status'=>'failure',
        //         'message'=>'Insufficient balance. Please recharge via AEPS.'
        //     ]);
        // }
        
       
        $pricing =  Pricing::where('sp_key',$request->input('sp_key'))->first();
       

     

        $paramList=array();
        for($i=0;$i<count($request->input('params'));$i++){
            $paramName="param".($i+1);
            $paramList[$paramName] = $request->input('params')[$i]["value"];
        }
   $amount = $request->input('amount');
   $cash = $request->input('cash');
   $amt=$request->input('amt');
   $mobile=$request->input('mobile');
 
        $validateID="";

if($pricing->params1 == NULL &&  $mobile == NULL)
{
    $data =[
        "agentDetails"=>[ 
            "deviceTags"=>[ 
                [
            "name"=> "INITIATING_CHANNEL",
            "value" => "AGT",
        ],
            [
             "name" => "TERMINAL_ID",
            "value" => $user->outlet_id,
        ],
            [
            "name" => "POSTAL_CODE",
            "value" => $user->pincode,
        ],
            [
            "name" => "MOBILE",
            "value" => $user->phone,
        ],
            [
            "name" =>"GEOCODE",
            "value" => number_format($request->input("location")["lat"],4).",".number_format($request->input("location")["long"],4),
        ],
            ],

              "agentId" => "AM01YKS042INTU000001",

        ],
        "amountDetails" => [
            "amount" => $request->cash,
            "currency" => "356",
            "custConvFee" => "0",
            "couCustConvFee" => "0"
        ],
        "billDetails"=>[ 
      "billerId" =>$pricing->sp_key,
      "customerParams"=>[
  [
        "name" => $pricing->params,
        "value" => $amount,
  ]
    ]],
    "chId" => 1,
    "custDetails"=>[ 
        "mobileNo" => $user->phone,
        "customerTags"=>[  
            [
            "name" => "EMAIL",
            "value" => $user->email,
        ]
        ]
        ],
    "paymentDetails" => [
        "quickPay" => "Yes",
        "splitPay" =>"No",
        "offusPay" => "No",
        "paymentMode"=> "Cash",

        "paymentInfo" => [
            [   
            "name" => "Remarks",
            "value" => "BillPayments"
        ]
        ]
        ],
   
 
   "refId"=> $request->refId,
   "clientRequestId" => ""

];


//prod

//     $data =[
//         "agentDetails"=>[ 
//             "deviceTags"=>[ 
//                 [
//             "name"=> "INITIATING_CHANNEL",
//             "value" => "MOB",
//         ],
//             [
//              "name" => "IMEI",
//             "value" => "448674528976410",
//         ],
//             [
//             "name" => "OS",
//             "value" => "android",
//         ],
//             [
//             "name" => "APP",
//             "value" => "NPCIAPP",
//         ],
//         [
//             "name" => "IP",
//             "value" => "124.170.23.28",
//         ]
//             ],

//               "agentId" => "AM01AM32MOBU00000001",

//         ],
//         "amountDetails"=>[ 
//            "amount" => $request->cash,
//            "currency" => "356",
//            "custConvFee" => "0",
//            "couCustConvFee"=> "0"

//             ],
//         "billDetails"=>[ 
//       "billerId" =>$pricing->sp_key,
//       "customerParams"=>[
//   [
//         "name" => $pricing->params,
//         "value" => $amount,
//   ],
//     ]],
//     "chId" => 1,
//     "custDetails"=>[ 
//         "mobileNo" => $user->phone,
//         "customerTags"=>[  
//             [
//             "name" => "EMAIL",
//             "value" => $user->email,
//         ]
//         ]
//         ],
//         "paymentDetails" =>[
//             "quickPay" =>"Yes",
//         "splitPay"=> "No",
//         "offusPay"=> "No",
//         "paymentMode"=> "Credit Card",
//         "paymentInfo" =>[
//             [
//             "name"=> "CardNum|AuthCode",
//             "value"=>"4336620020624963|123456"
//             ]
//         ]
//         ],

//         "refId" => $request->refId,
//         "clientRequestId" => ""

   
  
//     ];
$usertoken = Integration::where('user_id',Auth::id())->first();
$token = $usertoken->token;
    //Convert data array to JSON format
    // $jsonData = json_encode($datas);
    $response = Http::withHeaders([
        'Content-Type' => 'application/json','Authorization' => $token
    ])->post(env('production_pay'),$data);

   
        // return response()->json([
           
        //     'message'=> $response['error'],
        // ]);
   
        


  

//     $response = Http::withHeaders(['Authorization'=> 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJodHRwOlwvXC8xMjcuMC4wLjE6ODAwMFwvY3JlYXRlLWp3dCIsImlhdCI6MTcxNDAyNjMxNSwiZXhwIjoyMDI5NTU5MTE1LCJuYmYiOjE3MTQwMjYzMTUsImp0aSI6IjAxMnpoVm5mdkxzMWl4M0EiLCJzdWIiOjEsInBydiI6ImM5MDcyMDRlNWZkZDM5OTZkMThjMzg3ZjYzNDRlNTVkNmQwMmVmZGQiLCJ1c2VyX2lkIjoxLCJpbnRlZ3JhdGlvbl9pZCI6ODEsInByb2R1Y3Rpb24iOnRydWV9.27zbpqIEg2C5Iamw42rjb4ZPVn-B8uHWXqkLYm9ha0k'
//     ,'Content-Type' => 'application/json',
// ])->asForm()->post('http://127.0.0.1:8080/v1/v2/billpayment', $datas);


$array = json_decode($response,TRUE);
      



$txnReferenceId = $array['clientResponse']['response']['txnReferenceId'];
$txn_reference_id = $array['paymentResponse']['txn_reference_id'];


$tranactionController = new TransactionController();
    $transaction = $tranactionController->createTransaction($user->pan,$cash,0,$request->input('sp_key'),$user->phone,$txnReferenceId,$txn_reference_id);
    if($transaction == false){
        return response()->json([
            'status' => 'failure',
            'message'=> 'Transaction ID Failed'
        ]);
    }
    // $Agent_balance =   Agent::whereId(Auth::id())->first();
    // $Agentid = Agent::whereId(Auth::id())->first();
    // dd($Agentid);
    // $balance = $Agent_balance->balance;
    // $net_balance = $balance - $cash;
   
    //    Agent::where('id',$Agentid)->update([
    //      'balance' =>$net_balance
    //    ]);
    return response()->json([
        "status"=>"success",
        "data"=>$array
      ]);
}else {
    $data =[
        "agentDetails"=>[ 
            "deviceTags"=>[ 
                [
            "name"=> "INITIATING_CHANNEL",
            "value" => "AGT",
        ],
            [
             "name" => "TERMINAL_ID",
            "value" => $user->outlet_id,
        ],
            [
            "name" => "POSTAL_CODE",
            "value" => $user->pincode,
        ],
            [
            "name" => "MOBILE",
            "value" => $user->phone,
        ],
            [
            "name" =>"GEOCODE",
            "value" => number_format($request->input("location")["lat"],4).",".number_format($request->input("location")["long"],4),
        ],
            ],

              "agentId" => "AM01YKS042INTU000001",

        ],
        "amountDetails" => [
            "amount" => $request->cash,
            "currency" => "356",
            "custConvFee" => "0",
            "couCustConvFee" => "0"
        ],
        "billDetails"=>[ 
      "billerId" =>$pricing->sp_key,
      "customerParams"=>[
  [
        "name" => $pricing->params,
        "value" => $amount,
  ],
        [
        "name" => $pricing->params1,
        "value" => $mobile,
      ]
    ]],
    "chId" => 1,
    "custDetails"=>[ 
        "mobileNo" => $user->phone,
        "customerTags"=>[  
            [
            "name" => "EMAIL",
            "value" => $user->email,
        ]
        ]
        ],
    "paymentDetails" => [
        "quickPay" => "Yes",
        "splitPay" =>"No",
        "offusPay" => "No",
        "paymentMode"=> "Cash",

        "paymentInfo" => [
            [   
            "name" => "Remarks",
            "value" => "BillPayments"
        ]
        ]
        ],
   
 
   "refId"=> $request->refId,
   "clientRequestId" => ""

];


//prod

//     $data =[
//         "agentDetails"=>[ 
//             "deviceTags"=>[ 
//                 [
//             "name"=> "INITIATING_CHANNEL",
//             "value" => "MOB",
//         ],
//             [
//              "name" => "IMEI",
//             "value" => "448674528976410",
//         ],
//             [
//             "name" => "OS",
//             "value" => "android",
//         ],
//             [
//             "name" => "APP",
//             "value" => "NPCIAPP",
//         ],
//         [
//             "name" => "IP",
//             "value" => "124.170.23.28",
//         ]
//             ],

//               "agentId" => "AM01AM32MOBU00000001",

//         ],
//         "amountDetails"=>[ 
//            "amount" => $request->cash,
//            "currency" => "356",
//            "custConvFee" => "0",
//            "couCustConvFee"=> "0"

//             ],
//         "billDetails"=>[ 
//       "billerId" =>$pricing->sp_key,
//       "customerParams"=>[
//   [
//         "name" => $pricing->params,
//         "value" => $amount,
//   ],
//     ]],
//     "chId" => 1,
//     "custDetails"=>[ 
//         "mobileNo" => $user->phone,
//         "customerTags"=>[  
//             [
//             "name" => "EMAIL",
//             "value" => $user->email,
//         ]
//         ]
//         ],
//         "paymentDetails" =>[
//             "quickPay" =>"Yes",
//         "splitPay"=> "No",
//         "offusPay"=> "No",
//         "paymentMode"=> "Credit Card",
//         "paymentInfo" =>[
//             [
//             "name"=> "CardNum|AuthCode",
//             "value"=>"4336620020624963|123456"
//             ]
//         ]
//         ],

//         "refId" => $request->refId,
//         "clientRequestId" => ""

   
  
//     ];

$usertoken = Integration::where('user_id',Auth::id())->first();
$token = $usertoken->token;
    //Convert data array to JSON format
    // $jsonData = json_encode($datas);
    $response = Http::withHeaders([
        'Content-Type' => 'application/json','Authorization' => $token
    ])->post(env('production_pay'),$data);

   
        // return response()->json([
           
        //     'message'=> $response['error'],
        // ]);
   
        


  

//     $response = Http::withHeaders(['Authorization'=> 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJodHRwOlwvXC8xMjcuMC4wLjE6ODAwMFwvY3JlYXRlLWp3dCIsImlhdCI6MTcxNDAyNjMxNSwiZXhwIjoyMDI5NTU5MTE1LCJuYmYiOjE3MTQwMjYzMTUsImp0aSI6IjAxMnpoVm5mdkxzMWl4M0EiLCJzdWIiOjEsInBydiI6ImM5MDcyMDRlNWZkZDM5OTZkMThjMzg3ZjYzNDRlNTVkNmQwMmVmZGQiLCJ1c2VyX2lkIjoxLCJpbnRlZ3JhdGlvbl9pZCI6ODEsInByb2R1Y3Rpb24iOnRydWV9.27zbpqIEg2C5Iamw42rjb4ZPVn-B8uHWXqkLYm9ha0k'
//     ,'Content-Type' => 'application/json',
// ])->asForm()->post('http://127.0.0.1:8080/v1/v2/billpayment', $datas);


$array = json_decode($response,TRUE);


// return response()->json([
//     'status' => 'failure',
//     'message'=> $array['Msg']
// ]);




$txnReferenceId = $array['response']['txnReferenceId'];
$txn_reference_id = $array['response']['txn_reference_id'];


$tranactionController = new TransactionController();
    $transaction = $tranactionController->createTransaction($user->pan,$cash,0,$request->input('sp_key'),$user->phone,$txnReferenceId,$txn_reference_id);
    if($transaction == false){
        return response()->json([
            'status' => 'failure',
            'message'=> 'Transaction ID Failed'
        ]);
    }
    // $Agent_balance =   Agent::whereId(Auth::id())->first();
    // $Agentid = Agent::whereId(Auth::id())->first();
    // dd($Agentid);
    // $balance = $Agent_balance->balance;
    // $net_balance = $balance - $cash;
   
    //    Agent::where('id',$Agentid)->update([
    //      'balance' =>$net_balance
    //    ]);
    return response()->json([
        "status"=>"success",
        "data"=>$array
      ]);
}
    
      
  }   
  public function payPrepaid(Request $request){

   
    $user = Agent::whereId(Auth::id())->first();
      
     
    // if ($user->balance < $request->input('amount')){
    //     return response()->json([
    //         'status'=>'failure',
    //         'message'=>'Insufficient balance. Please recharge via AEPS.'
    //     ]);
    // }
    
   
    $pricing =  Pricing::where('sp_key',$request->input('sp_key'))->first();
   

 

    $paramList=array();
    for($i=0;$i<count($request->input('params'));$i++){
        $paramName="param".($i+1);
        $paramList[$paramName] = $request->input('params')[$i]["value"];
    }
$amount = $request->input('amount');
$cash = $request->input('cash');
$amt=$request->input('amt');
$mobile=$request->input('mobile');

    $validateID="";
//     $data =[
//         "agentDetails"=>[ 
//             "deviceTags"=>[ 
//                 [
//             "name"=> "INITIATING_CHANNEL",
//             "value" => "AGT",
//         ],
//             [
//              "name" => "TERMINAL_ID",
//             "value" => $user->outlet_id,
//         ],
//             [
//             "name" => "POSTAL_CODE",
//             "value" => $user->pincode,
//         ],
//             [
//             "name" => "MOBILE",
//             "value" => $user->phone,
//         ],
//             [
//             "name" =>"GEOCODE",
//             "value" => number_format($request->input("location")["lat"],4).",".number_format($request->input("location")["long"],4),
//         ],
//             ],

//               "agentId" => "AM01YKS042INTU000001",

//         ],
//         "amountDetails" => [
//             "amount" => $request->cash,
//             "currency" => "356",
//             "custConvFee" => "0",
//             "couCustConvFee" => "0"
//         ],
//         "billDetails"=>[ 
//       "billerId" =>$pricing->sp_key,
//       "customerParams"=>[
//   [
//         "name" => $pricing->params,
//         "value" => $amount,
//   ],
//         [
//         "name" => $pricing->params1,
//         "value" => $mobile,
//         ],
//         [
//             "name" =>"Id",
//             "value" =>"4"
//         ],
//     ]],
//     "planDetails"=> [
//         "planDetail"=> [
//             "id"=> "4",
//             "type"=> "RECOMMENDED"
//         ]
//     ],
//     "chId" => 1,
//     "custDetails"=>[ 
//         "mobileNo" => $user->phone,
//         "customerTags"=>[  
//             [
//             "name" => "EMAIL",
//             "value" => $user->email,
//         ]
//         ]
//         ],
//     "paymentDetails" => [
//         "quickPay" => "Yes",
//         "splitPay" =>"No",
//         "offusPay" => "No",
//         "paymentMode"=> "Cash",

//         "paymentInfo" => [
//             [   
//             "name" => "Remarks",
//             "value" => "BillPayments"
//         ]
//         ]
//         ],
   
 
// //    "refId"=> $request->refId,
//    "refId"=> "",
//    "clientRequestId" => ""

// ];


//prod

    $data =[
        "agentDetails"=>[ 
            "deviceTags"=>[ 
                [
            "name"=> "INITIATING_CHANNEL",
            "value" => "MOB",
        ],
            [
             "name" => "IMEI",
            "value" => "448674528976410",
        ],
            [
            "name" => "OS",
            "value" => "android",
        ],
            [
            "name" => "APP",
            "value" => "NPCIAPP",
        ],
        [
            "name" => "IP",
            "value" => "124.170.23.28",
        ]
            ],

              "agentId" => "AM01AM32MOBU00000001",

        ],
        "amountDetails"=>[ 
           "amount" =>strval($request->cash),
       
           "currency" => "356",
           "custConvFee" => "0",
           "couCustConvFee"=> "0"

            ],
        "billDetails"=>[ 
      "billerId" =>$pricing->sp_key,
      "customerParams"=>[
        [
                    "name" => $pricing->params,
                    "value" => $amount,
        ],
        [
              "name" => $pricing->params1,
                    "value" => $mobile,
         ],
        [
                        "name" =>"Id",
                       "value" =>$request->selectedPlanId
        ],
    ]],
    "planDetails"=> [
        "planDetail"=>[
            "id"=> $request->selectedPlanId,
            "type"=> "RECOMMENDED"
        ]
    ],

    "chId" => 1,
    "custDetails"=>[ 
        "mobileNo" => $user->phone,
        "customerTags"=>[  
            [
            "name" => "EMAIL",
            "value" => $user->email,
        ]
        ]
        ],
        "paymentDetails" =>[
            "quickPay" =>"No",
        "splitPay"=> "No",
        "offusPay"=> "Yes",
        "paymentMode"=> "Credit Card",
        "paymentInfo" =>[
            [
            "name"=> "CardNum|AuthCode",
            "value"=>"4336620020624963|123456"
            ]
        ]
        ],

        "refId" => "",
        "clientRequestId" => ""

   
  
    ];


    


    $usertoken = Integration::where('user_id',Auth::id())->first();
    $token = $usertoken->token;
    
            //Convert data array to JSON format
             $jsonData = json_encode($data);
       
            $response = Http::withHeaders([
                'Content-Type' => 'application/json','Authorization' => $token
            ])->post(env('production_pay') ,$data);
    
    
               
           
        $array = json_decode($response,TRUE);
  
    


        // return response()->json([
        //     'status' => 'failure',
        //     'message'=> $array['Msg']
        // ]);
    
    

 

$txnReferenceId = $array['clientResponse']['response']['txnReferenceId'];
$txn_reference_id = $array['paymentResponse']['txn_reference_id'];


$tranactionController = new TransactionController();
    $transaction = $tranactionController->createTransaction($user->pan,$cash,0,$request->input('sp_key'),$user->phone,$txnReferenceId,$txn_reference_id);
    if($transaction == false){
        return response()->json([
            'status' => 'failure',
            'message'=> 'Transaction ID Failed'
        ]);
    }
    return response()->json([
        "status"=>"success",
        "data"=>$array
      ]);



  }
  


      
       // Check if the request was successful
    //    if ($response->successful()) {
    //     // Get JSON response as an array
    //     // Process the response data
    // } else {
    //     // Handle unsuccessful response
    //     $statusCode = $response->status();
    //     $errorMessage = $response->body();
    //     // Handle error
    // }
     


    

//    public function getBillers(Request $request): \Illuminate\Http\JsonResponse
//    {
//        $response = Http::asJson()->post(env('INSTANTPAY_GET_BILLER_URL'),[
//            "token" => env('INSTANTPAY_AEPS_TOKEN'),
//            "request" =>[
//                "sp_key"=>$request->input('sp_key'),
//                "page"=>1
//            ]
//        ]);
//
//
//        $xml = simplexml_load_string($response);
//        $json = json_encode($xml);
//        $array = json_decode($json,TRUE);
//
//
//        if($array["statuscode"] != "TXN"){
//            return response()->json([
//                "status"=>"failure",
//                "message"=>$array["status"]
//            ]);
//        }
//
//        return response()->json([
//           "status"=>"success",
//           "data"=>$array["data"]["biller"]["item"]
//        ]);
//    }

//    public function getBillerDetails()
//    {
//
//
//        if($array["statuscode"] != "TXN"){
//            return response()->json([
//                "status"=>"failure",
//                "message"=>$array["status"]
//            ]);
//        }
//
//
//        return response()->json([
//            "status"=>"success",
//            "data"=>$array["data"]["item"]
//        ]);
//    }

    public function aepsTransaction(Request $request)
    {
        $user=Auth::user();
        $tranactionController = new TransactionController();
        $transaction = $tranactionController->createTransaction($user->pan,$request->input('amount'),0,$request->input('sp_key'),$request->input('mobile'));
        if($transaction == false){
            return response()->json([
                'status' => 'failure',
                'message'=> 'Transaction ID Failed'
            ]);
        }

        $request = [
            "token" => env('INSTANTPAY_AEPS_TOKEN'),
            "request"=>[
                "outlet_id"=>Auth::user()->outlet_id,
                "amount"=>$request->input('amount'),
                "aadhaar_uid"=>$request->input('aadhar_uuid'),
                "bankiin"=>$request->input("bankiin"),
                "latitude"=>$request->input('latitude'),
                "longitude"=>$request->input('longitude'),
                "mobile"=>$request->input('mobile'),
                "sp_key"=>$request->input('sp_key'),
                "agent_id"=>$transaction->id,
                "pidDataType"=>$request->input('pidDataType'),
                "pidData"=>$request->input('pidData'),
                "ci"=>$request->input('ci'),
                "dc"=>$request->input('dc'),
                "dpId"=>$request->input('dpid'),
                "errCode"=>$request->input('errCode'),
                "errInfo"=>$request->input('errInfo'),
                "fCount"=>$request->input('fCount'),
                "tType"=>null,
                "hmac"=>$request->input('hmac'),
                "iCount"=>$request->input('iCount'),
                "mc"=>$request->input('mc'),
                "mi"=>$request->input('mi'),
                "nmPoints"=>$request->input('nmPoints'),
                "pCount"=>$request->input('pCount'),
                "pType"=>$request->input('pType'),
                "qScore"=>$request->input('qScore'),
                "rdsId"=>$request->input('rdsId'),
                "rdsVer"=>$request->input('rdsVer'),
                "sessionKey"=>$request->input('sessionKey'),
                "srno"=>$request->input('srno')
            ],
            "user-agent"=>$request->input('userAgent')
        ];
        $response = Http::asJson()->post(env('INSTANTPAY_GET_AEPS_URL'),$request);

        $xml = simplexml_load_string($response);
        $json = json_encode($xml);
        $array = json_decode($json,TRUE);

        if($array["statuscode"] != "TXN"){
            $transaction->status="FAILED";
            $transaction->request_id = $array["ipay_uuid"];
            $transaction->save();

            Log::info('Request => '.json_encode($request));
            Log::info('Response => '.json_encode($xml));

            return response()->json([
                "status"=>"failure",
                "message"=>$array["status"]
            ]);
        }

        $agent = Agent::whereId(Auth::id())->first();
        if ($transaction->sp_key == "WAP"){
            $this->calculateAepsCommision($transaction,$agent);
        }elseif ($transaction->sp_key == "SAP"){
            $this->calculateStatementCommission($transaction,$agent);
        }

        $transaction->request_id = $array["ipay_uuid"];
        $transaction->save();


        return response()->json([
            'status' => 'success',
            'data'=> $array
        ]);

    }

    public function calculateCommission(Transaction $transaction,Agent $agent,Pricing $pricing)
    {
        $kCommision = $pricing->commission_type == "F" ? $pricing->k_commission : round($transaction->amount * ($pricing->k_commission/100),2);
        $aCommision = $pricing->commission_type == "F" ? $pricing->a_commission : round($transaction->amount * ($pricing->a_commission/100),2);
        $total = $kCommision + $aCommision;
        $transaction->total_comm = $total_commision = round($total,2);
        $transaction->total_tds = $tds = round($total_commision*(env("TDS")/100),2);
        $transaction->agent_tds = $atds = round($aCommision*(env("TDS")/100),2);
        $transaction->agent_comm = round($aCommision - $atds,2 );
        $transaction->kadamba_comm = round($kCommision - $tds,2);
        $transaction->status="SUCCESS";
        $transaction->save();
        $agent->balance = round($agent->balance - round($transaction->amount - $transaction->agent_comm,2 ),2);
        $agent->save();
    }

    public function calculateAepsCommision(Transaction $transaction,Agent $agent)
    {
        if($transaction->amount >= 100.00 && $transaction->amount <= 10000.00){
            $commission = round($transaction->amount*(0.38/100),2);
            $commission = $commission > 11.40 ? 11.40 : $commission;
            $agent_tds = round(($commission*.6)*(env("TDS")/100),2);
            $agent_comm = round(($commission*.6) - $agent_tds,2);
            $tds = round(($commission)*(env("TDS")/100) ,2);
            $transaction->total_comm = $commission;
            $transaction->total_tds = $tds;
            $transaction->agent_tds = $agent_tds;
            $transaction->agent_comm = $agent_comm;
            $transaction->kadamba_comm = round(($commission*.4) - $tds,2);
            $transaction->status="SUCCESS";
            $transaction->save();
            $agent->balance += $transaction->amount + $agent_comm;
            $agent->save();
        }elseif ($transaction->amount >= 1.00 && $transaction->amount <= 99.99){
            $transaction->status="SUCCESS";
            $transaction->save();
            $agent->balance += $transaction->amount;
            $agent->save();
        }

    }

    public function calculateStatementCommission(Transaction $transaction,Agent $agent)
    {
        $transaction->total_comm = 1;
        $transaction->total_tds = round((1)*(env("TDS")/100) ,2);
        $transaction->agent_tds = round((1)*(env("TDS")/100) ,2);
        $transaction->agent_comm = round(1-((1)*(env("TDS")/100)) ,2);
        $transaction->status="SUCCESS";
        $transaction->save();
        $agent->balance += $transaction->agent_comm;
        $agent->save();
    }
   

    public function showLoadBalance(Request $request)
    {
       
        $agents = Agent::all();
      
        return Inertia::render('Agent/LoadBalance',[
            'agents' => $agents
        ]);

    }
public function updateBalance(Request $request){
    $request->validate([
        'Agentid'=>'required',
        'Amount'=>'required'

    ]);
 $Agent =   Agent::where('id',Auth::id())->first();
 $balance = $Agent->balance;
 $net_balance = $balance + $request->Amount;
    Agent::where('pan',$request->Agentid)->update([
      'balance' =>$net_balance
    ]);
    Transaction::create([
      
        'pan'=>$Agent->pan,
        'status'=>"Credited",
        'amount'=>$request->Amount,
        'customer_params'=>$Agent->phone,
        'description'=>'Amount Loaded'
        
    ]);
    return Inertia::render('Agent/dashboard');
}
public function ViewDTH(Request $request)
    {
        return Inertia::render('Shared/DTH');
    }
    public function fetchData(Request $request)
    {
        $mobile = $request->input('mobile');
    
  
        $usertoken = Integration::where('user_id',Auth::id())->first();
        $token = $usertoken->token;
        // Assuming you want to pass the selectedValue as a query parameter
        // $response = Http::get("127.0.0.1:8080/v1/v3/billerwiseplan/BSNL00000NATHL/Circle/", [
        //     'selectedValue' => $selectedValue
        // ]);

        $response = Http::withHeaders([
            'Content-Type' => 'application/json','Authorization' => $token
        ])->get('127.0.0.1:8080/v1/v3/billerwiseplan/BSNL00000NATHL/Circle/'.$mobile);

        return $response->json();
    }
}
