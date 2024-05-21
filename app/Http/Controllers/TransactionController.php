<?php

namespace App\Http\Controllers;

use App\Agent;
use App\Pricing;
use App\Transaction;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class TransactionController extends Controller
{
    private $product_array = array(
        array("service name" => "Amount Loaded", "service key" => "WAP"),
        array("service name" => "AEPS Statement", "service key" => "SAP"),
        array("service name" => "AEPS Balance", "service key" => "BAP"),
        array("service name" => "ACT Fibernet", "service key" => "AFB"),
        array("service name" => "Airtel", "service key" => "ATC"),
        array("service name" => "Airtel", "service key" => "ATL"),
        array("service name" => "Airtel", "service key" => "ATP"),
        array("service name" => "Airtel Broadband", "service key" => "ABB"),
        array("service name" => "Airtel Digital TV", "service key" => "ATK"),
        array("service name" => "Airtel Digital TV", "service key" => "ATV"),
        array("service name" => "Asianet Broadband", "service key" => "ANB"),
        array("service name" => "Bajaj Finance", "service key" => "BRL"),
        array("service name" => "Bank of Baroda", "service key" => "BBT"),
        array("service name" => "Bharat Gas (BPCL)", "service key" => "PGC"),
        array("service name" => "BSNL", "service key" => "BPC"),
        array("service name" => "BSNL - Corporate", "service key" => "BCL"),
        array("service name" => "BSNL - Individual", "service key" => "BGL"),
        array("service name" => "BSNL - Special Tariff", "service key" => "BVP"),
        array("service name" => "BSNL - Talktime", "service key" => "BGP"),
        array("service name" => "CESCOM - KARNATAKA", "service key" => "CKE"),
        array("service name" => "Comway Broadband", "service key" => "CWB"),
        array("service name" => "Connect Broadband", "service key" => "CBB"),
        array("service name" => "D VoiS Communications", "service key" => "DBB"),
        array("service name" => "d2h", "service key" => "VTV"),
        array("service name" => "DEN Broadband", "service key" => "DEB"),
        array("service name" => "Dish TV", "service key" => "DTV"),
        array("service name" => "Dish TV", "service key" => "DTK"),
        array("service name" => "FlexSalary", "service key" => "FSL"),
        array("service name" => "Fusionnet Web Services", "service key" => "FBB"),
        array("service name" => "GESCOM - KARNATAKA", "service key" => "GKE"),
        array("service name" => "Hathway Broadband", "service key" => "HBB"),
        array("service name" => "HESCOM - KARNATAKA", "service key" => "HKE"),
        array("service name" => "HP Gas (HPCL)", "service key" => "HGC"),
        array("service name" => "I-ON Broadband", "service key" => "ONB"),
        array("service name" => "ICICI Bank", "service key" => "ICT"),
        array("service name" => "Idea", "service key" => "IDC"),
        array("service name" => "Idea", "service key" => "IDP"),
        array("service name" => "IDFC FIRST Bank", "service key" => "IFL"),
        array("service name" => "Indian Highways Management Company", "service key" => "AFT"),
        array("service name" => "IndusInd Bank", "service key" => "BFT"),
        array("service name" => "Instanet Broadband", "service key" => "INB"),
        array("service name" => "L&T Financial Services", "service key" => "LTL"),
        array("service name" => "LokSuvidha", "service key" => "LSL"),
        array("service name" => "Motilal Oswal Home Finance", "service key" => "MOL"),
        array("service name" => "Netplus Broadband", "service key" => "NPB"),
        array("service name" => "Nextra Broadband", "service key" => "NBB"),
        array("service name" => "Paisa Dukan", "service key" => "PDL"),
        array("service name" => "PAN Application", "service key" => "PAN"),
        array("service name" => "Paytm Payments Bank", "service key" => "PBT"),
        array("service name" => "Reliance Jio", "service key" => "RJP"),
        array("service name" => "Reliance Jio", "service key" => "RJC"),
        array("service name" => "Snapmint", "service key" => "SML"),
        array("service name" => "Spectranet Broadband", "service key" => "SBB"),
        array("service name" => "Sun Direct", "service key" => "STV"),
        array("service name" => "Tata Capital", "service key" => "TAL"),
        array("service name" => "Tata Sky", "service key" => "TTK"),
        array("service name" => "Tata Sky", "service key" => "TTV"),
        array("service name" => "Tata Sky - ONLINE", "service key" => "OTV"),
        array("service name" => "Tikona Broadband", "service key" => "TBB"),
        array("service name" => "Timbl Broadband", "service key" => "TIB"),
        array("service name" => "TTN BroadBand", "service key" => "TTB"),
        array("service name" => "V-FiberNet Broadband", "service key" => "VFB"),
        array("service name" => "Videocon d2h", "service key" => "VTK"),
        array("service name" => "Vodafone", "service key" => "VFP"),
        array("service name" => "Vodafone", "service key" => "VFC")
    );

    private $aeps = array("WAP");
    private $percent_comm = array(
        "ATV"=>array("k"=>.2,"a"=>.8,"p"=>"Airtel TV"),
        "VTV"=>array("k"=>.6,"a"=>2.5,"p"=>"d2h TV"),
        "DTV"=>array("k"=>.87,"a"=>1.5),
        "STV"=>array("k"=>.9,"a"=>2.5),
        "TTV"=>array("k"=>.8,"a"=>2.5),
        "OTV"=>array("k"=>.25,"a"=>1),
        "BBT"=>array("k"=>.1,"a"=>.1),
        "ICT"=>array("k"=>.1,"a"=>.1),
        "AFT"=>array("k"=>.1,"a"=>.1),
        "BFT"=>array("k"=>.1,"a"=>.1),
        "PBT"=>array("k"=>.1,"a"=>.1),
        "ATP"=>array("k"=>.15,"a"=>.5),
        "BVP"=>array("k"=>.5,"a"=>1.9),
        "BGP"=>array("k"=>.5,"a"=>1.9),
        "IDP"=>array("k"=>.25,"a"=>.75),
        "RJP"=>array("k"=>.25,"a"=>.75),
        "VFP"=>array("k"=>.15,"a"=>.5),
    );
    private $fixed_comm = array(
        "PAN"=>array("k"=>3,"a"=>5),
        "CKE"=>array("k"=>0.5,"a"=>1),
        "GKE"=>array("k"=>0.5,"a"=>1),
        "HKE"=>array("k"=>0.5,"a"=>1),
        "PGC"=>array("k"=>0.5,"a"=>1),
        "HGC"=>array("k"=>0.5,"a"=>1),
        "ATK"=>array("k"=>50,"a"=>400),
        "DTK"=>array("k"=>50,"a"=>400),
        "TTK"=>array("k"=>50,"a"=>400),
        "VTK"=>array("k"=>50,"a"=>400),
        "AFB"=>array("k"=>1.5,"a"=>2),
        "ABB"=>array("k"=>1.5,"a"=>2),
        "ANB"=>array("k"=>1.5,"a"=>2),
        "CWB"=>array("k"=>1.5,"a"=>2),
        "CBB"=>array("k"=>1.5,"a"=>2),
        "DBB"=>array("k"=>1.5,"a"=>2),
        "DEB"=>array("k"=>1.5,"a"=>2),
        "FBB"=>array("k"=>1.5,"a"=>2),
        "HBB"=>array("k"=>1.5,"a"=>2),
        "ONB"=>array("k"=>1.5,"a"=>2),
        "INB"=>array("k"=>1.5,"a"=>2),
        "MNP"=>array("k"=>1.5,"a"=>2),
        "NPB"=>array("k"=>1.5,"a"=>2),
        "NBB"=>array("k"=>1.5,"a"=>2),
        "SBB"=>array("k"=>1.5,"a"=>2),
        "TBB"=>array("k"=>1.5,"a"=>2),
        "TIB"=>array("k"=>1.5,"a"=>2),
        "TTB"=>array("k"=>1.5,"a"=>2),
        "VFB"=>array("k"=>1.5,"a"=>2),
        "ATL"=>array("k"=>1.5,"a"=>2),
        "BCL"=>array("k"=>1.5,"a"=>2),
        "BGL"=>array("k"=>1.5,"a"=>2),
        "BRL"=>array("k"=>1.5,"a"=>2),
        "FSL"=>array("k"=>1.5,"a"=>2),
        "IFL"=>array("k"=>1.5,"a"=>2),
        "LTL"=>array("k"=>1.5,"a"=>2),
        "LSL"=>array("k"=>1.5,"a"=>2),
        "MOL"=>array("k"=>1.5,"a"=>2),
        "PDL"=>array("k"=>1.5,"a"=>2),
        "SML"=>array("k"=>1.5,"a"=>2),
        "TAL"=>array("k"=>1.5,"a"=>2),
        "ATC"=>array("k"=>1.5,"a"=>2),
        "BPC"=>array("k"=>1.5,"a"=>2),
        "IDC"=>array("k"=>1.5,"a"=>2),
        "RJC"=>array("k"=>1.5,"a"=>2),
        "VFC"=>array("k"=>1.5,"a"=>2)
    );

    /***
     * Validate the transaction based on agent balance
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function validateTransaction(Request $request)
    {

        $agent = Agent::where('pan',$request->json('outlet_pan'))->first();

        $txn = $request->json('transactions');

        if ($txn[0]['sp_key'] == "SAP"  | $txn[0]['sp_key'] == "BAP" )
        {
            $id = $this->createTransaction($agent->pan,0,$request->json('request_id'),$txn[0]['sp_key'],$txn[0]['customer_params'][2]);

            $balance =  array(["agent_id"=> $id]);

            return \response()->json([
                "response_code"=> "TXN",
                "response_msg"=> "Transaction Successful",
                "transactions"=>$balance
            ]);
        }else{
            if ($txn[0]['sp_key'] == "WAP"){

                $id = $this->createTransaction($agent->pan,$txn[0]['amount'],$request->json('request_id'),$txn[0]['sp_key'],$txn[0]['customer_params'][2]);

                $balance =  array(["agent_id"=> $id]);

                return \response()->json([
                    "response_code"=> "TXN",
                    "response_msg"=> "Transaction Successful",
                    "transactions"=>$balance
                ]);
            }else{

                if ($agent->balance < $txn[0]['amount']){
                    return \response()->json([
                        "response_code"=> "ERR",
                        "response_msg"=> "No Sufficient Balance",
                    ]);
                }else{
                    $id = $this->createTransaction($agent->pan,$txn[0]['amount'],$request->json('request_id'),$txn[0]['sp_key'],$txn[0]['customer_params'][0]);
                    $balance =  array(["agent_id"=> $id]);
                    return \response()->json([
                        "response_code"=> "TXN",
                        "response_msg"=> "Transaction Successful",
                        "transactions"=>$balance
                    ]);
                }
            }
        }
    }


    /***
     * create a transaction with unique id
     * @param $pan
     * @param $amount
     * @param $requestId
     * @param $sp_key
     * @param $customer_params
     * @return Transaction $id||false
     */
    public function createTransaction($pan, $cash, $requestId, $sp_key, $customer_params,$txnReferenceId,$txn_reference_id)
    {
        try {
            return Transaction::create([
                'request_id'=>$requestId,
                'pan'=>$pan,
                'status'=>"Debited",
                'amount'=>$cash,
                'customer_params'=>$customer_params,
                'sp_key'=>$sp_key,
                'txnReferenceId'=>$txnReferenceId,
                'txn_reference_id'=>$txn_reference_id
            ]);
        }catch (\Exception $exception){
            return false;
        }

    }

    public function commitTransaction(Request $request)
    {
        $transaction = Transaction::where('id', $request->query('agent_id'))->first();
        $agent = Agent::where('pan',$transaction->pan)->first();

        if ($request->query('status') == "SUCCESS") {
            if (array_key_exists($transaction->sp_key, $this->fixed_comm)) {
                $this->calculateFixedCommission($transaction,$agent);
            } elseif ($transaction->sp_key == "WAP"){
                $this->calculateAepsCommision($transaction,$agent);
            }elseif (array_key_exists($transaction->sp_key,$this->percent_comm)){
                $this->calculatePercentCommission($transaction,$agent);
            }elseif ($transaction->sp_key == "SAP"){
                $this->calculateStatementCommission($transaction,$agent);
            }
        }

        return \response()->json(['success'=>'success'],200);
    }

    public function calculateFixedCommission(Transaction $transaction,Agent $agent)
    {
        $total = $this->fixed_comm[$transaction->sp_key]["k"] + $this->fixed_comm[$transaction->sp_key]["a"];
        $transaction->total_comm = $total_commision = round($total,2);
        $transaction->total_tds = $tds = round($total_commision*(env("TDS")/100),2);
        $transaction->agent_tds = $atds = round($this->fixed_comm[$transaction->sp_key]["a"]*(env("TDS")/100),2);
        $transaction->agent_comm = round($this->fixed_comm[$transaction->sp_key]["a"] - $atds,2 );
        $transaction->kadamba_comm = round($this->fixed_comm[$transaction->sp_key]["k"]-$tds,2);
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

    public function calculatePercentCommission(Transaction $transaction,Agent $agent)
    {
        $total = round($this->percent_comm[$transaction->sp_key]["k"] + $this->percent_comm[$transaction->sp_key]["a"],2);
        $transaction->total_comm = $total_commision = round($transaction->amount * ($total/100),2);
        $transaction->total_tds = $tds = round(($total_commision*(env("TDS")/100)),2);
        $agent1 = round(($this->percent_comm[$transaction->sp_key]["a"]/100)*$transaction->amount,2);
        $transaction->agent_tds = $atds = round($agent1*(env("TDS")/100),2);
        $transaction->agent_comm = round($agent1 - $atds,2 );
        $transaction->kadamba_comm = round(round($transaction->amount*($this->percent_comm[$transaction->sp_key]["k"]/100),2)-$tds,2);
        $transaction->status="SUCCESS";
        $transaction->save();
        $agent->balance = round($agent->balance - round($transaction->amount - $transaction->agent_comm,2 ),2);
        $agent->save();
    }

    public function show(Request $request)
    {
        $fromDate = Carbon::now()->setTime(0,0,0)->format('Y-m-d');
        $toDate = Carbon::now()->addDay()->format('Y-m-d');
        if ($request->query('from') && $request->query('to')){
            $fromDate = Carbon::createFromFormat('Y/m/d',$request->query('from'))->format('Y-m-d');
            $toDate = Carbon::createFromFormat('Y/m/d',$request->query('to'))->addDay()->format('Y-m-d');
        }
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

        $kadambaCommision = Transaction::where('pan','like',$pan)->where('status','SUCCESS')->whereBetween('created_at',[$fromDate,$toDate])->sum('kadamba_comm');
        $agentCommision =  Transaction::where('pan','like',$pan)->where('status','SUCCESS')->whereBetween('created_at',[$fromDate,$toDate])->sum('agent_comm');
        $transactions = Transaction::where('pan','like',$pan)->whereBetween('created_at',[$fromDate,$toDate])->paginate(10);

        foreach ($transactions as $key=>$transaction){

            $transactions[$key]->type = is_null(Pricing::where('sp_key',$transaction->sp_key)->first()) ? $this->product_array[array_search($transaction->sp_key, array_column($this->product_array, 'service key'))]["service name"] : Pricing::where('sp_key',$transaction->sp_key)->first()->name;
            $transactions[$key]->user = Agent::where('pan',$transaction->pan)->first()->name;
            $transactions[$key]->create = Carbon::parse($transaction->created_at)->format('d M Y');
        }

        return Inertia::render('Agent/Transactions',[
           'transactions'=>$transactions,
           'users'=>$users,
            'agentTotal'=>round($agentCommision,2),
            'kadambaTotal'=>round($kadambaCommision,2)
        ]);
//        return view('transactions')->with('transactions',$transactions)->with('users',$users)->with('kadambaTotal',$kadambaCommision)->with('agentTotal',$agentCommision);
    }

    public function pricings(Request $request)
    {
        $transactions = $request->query('type') ? Pricing::where('type',$request->query('type'))->paginate(10) : Pricing::paginate(10);
        $billers = Pricing::select('type')->distinct()->get();
        foreach ($transactions as $key=>$transaction){
            $transactions[$key]->create = Carbon::parse($transaction->created_at)->format('d M Y');
        }

        return Inertia::render('Agent/Pricings',[
            'transactions'=>$transactions,
            'billers'=>$billers
        ]);
    }


}
