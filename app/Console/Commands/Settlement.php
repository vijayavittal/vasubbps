<?php

namespace App\Console\Commands;

use App\Agent;
use App\Pricing;
use Carbon\Carbon;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use Mpdf\Mpdf;

class Settlement extends Command
{
    private $product_array = array(
             array("service name"=>"AEPS Withdrawal","service key"=>"WAP"),
             array("service name"=>"AEPS Statement","service key"=>"SAP"),
             array("service name"=>"AEPS Balance","service key"=>"BAP"),
            array("service name"=>"ACT Fibernet","service key"=>"AFB"),
            array("service name"=>"Airtel","service key"=>"ATC"),
            array("service name"=>"Airtel","service key"=>"ATL"),
            array("service name"=>"Airtel","service key"=>"ATP"),
            array("service name"=>"Airtel Broadband","service key"=>"ABB"),
            array("service name"=>"Airtel Digital TV","service key"=>"ATK"),
            array("service name"=>"Airtel Digital TV","service key"=>"ATV"),
            array("service name"=>"Asianet Broadband","service key"=>"ANB"),
            array("service name"=>"Bajaj Finance","service key"=>"BRL"),
            array("service name"=>"Bank of Baroda","service key"=>"BBT"),
            array("service name"=>"Bharat Gas (BPCL)","service key"=>"PGC"),
            array("service name"=>"BSNL","service key"=>"BPC"),
            array("service name"=>"BSNL - Corporate","service key"=>"BCL"),
            array("service name"=>"BSNL - Individual","service key"=>"BGL"),
            array("service name"=>"BSNL - Special Tariff","service key"=>"BVP"),
            array("service name"=>"BSNL - Talktime","service key"=>"BGP"),
            array("service name"=>"CESCOM - KARNATAKA","service key"=>"CKE"),
            array("service name"=>"Comway Broadband","service key"=>"CWB"),
            array("service name"=>"Connect Broadband","service key"=>"CBB"),
            array("service name"=>"D VoiS Communications","service key"=>"DBB"),
            array("service name"=>"d2h","service key"=>"VTV"),
            array("service name"=>"DEN Broadband","service key"=>"DEB"),
            array("service name"=>"Dish TV","service key"=>"DTV"),
            array("service name"=>"Dish TV","service key"=>"DTK"),
            array("service name"=>"FlexSalary","service key"=>"FSL"),
            array("service name"=>"Fusionnet Web Services","service key"=>"FBB"),
            array("service name"=>"GESCOM - KARNATAKA","service key"=>"GKE"),
            array("service name"=>"Hathway Broadband","service key"=>"HBB"),
            array("service name"=>"HESCOM - KARNATAKA","service key"=>"HKE"),
            array("service name"=>"HP Gas (HPCL)","service key"=>"HGC"),
            array("service name"=>"I-ON Broadband","service key"=>"ONB"),
            array("service name"=>"ICICI Bank","service key"=>"ICT"),
            array("service name"=>"Idea","service key"=>"IDC"),
            array("service name"=>"Idea","service key"=>"IDP"),
            array("service name"=>"IDFC FIRST Bank","service key"=>"IFL"),
            array("service name"=>"Indian Highways Management Company","service key"=>"AFT"),
            array("service name"=>"IndusInd Bank","service key"=>"BFT"),
            array("service name"=>"Instanet Broadband","service key"=>"INB"),
            array("service name"=>"L&T Financial Services","service key"=>"LTL"),
            array("service name"=>"LokSuvidha","service key"=>"LSL"),
            array("service name"=>"Motilal Oswal Home Finance","service key"=>"MOL"),
            array("service name"=>"Netplus Broadband","service key"=>"NPB"),
            array("service name"=>"Nextra Broadband","service key"=>"NBB"),
            array("service name"=>"Paisa Dukan","service key"=>"PDL"),
            array("service name"=>"PAN Application","service key"=>"PAN"),
            array("service name"=>"Paytm Payments Bank","service key"=>"PBT"),
            array("service name"=>"Reliance Jio","service key"=>"RJP"),
            array("service name"=>"Reliance Jio","service key"=>"RJC"),
            array("service name"=>"Snapmint","service key"=>"SML"),
            array("service name"=>"Spectranet Broadband","service key"=>"SBB"),
            array("service name"=>"Sun Direct","service key"=>"STV"),
            array("service name"=>"Tata Capital","service key"=>"TAL"),
            array("service name"=>"Tata Sky","service key"=>"TTK"),
            array("service name"=>"Tata Sky","service key"=>"TTV"),
            array("service name"=>"Tata Sky - ONLINE","service key"=>"OTV"),
            array("service name"=>"Tikona Broadband","service key"=>"TBB"),
            array("service name"=>"Timbl Broadband","service key"=>"TIB"),
            array("service name"=>"TTN BroadBand","service key"=>"TTB"),
            array("service name"=>"V-FiberNet Broadband","service key"=>"VFB"),
            array("service name"=>"Videocon d2h","service key"=>"VTK"),
            array("service name"=>"Vodafone","service key"=>"VFP"),
            array("service name"=>"Vodafone","service key"=>"VFC")
            );


    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'aeps:settlement';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Daily settlement to agents';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $agents = Agent::all();
        foreach ($agents as $agent)
        {

            if ($agent->balance > 50.00){

            $fromDate = Carbon::createFromFormat('Y-m-d',$agent->last_settlement)->addDay()->toDateString();
            $toDate = Carbon::now()->subDay()->toDateString();
            $transactions = $agent->transactions()
                                ->whereBetween('created_at',[$fromDate.' 00:00:00',$toDate.' 23:59:59'])->where('status','SUCCESS')->get();

            $mpdf = new Mpdf(['mode' => 'utf-8', 'format' => 'A4-L']);
            $mpdf1 = new Mpdf(['mode' => 'utf-8', 'format' => 'A4-L']);

            $header = '<header>
                            <table width="100%">
                            <tr>
                                <td width="100%" style="text-align: center;font-weight: bold"><img style="width: 50px;height: 50px" src="../../../resources/images/reduces/logo.jpg"></td>
                            </tr>
                            </table>
                            <table width="100%" style="margin-bottom: 20px">
                            <tr>
                                  <td width="100%" style="text-align: center;font-weight: bold">KADAMBA AEPS SETTLEMENT STATEMENT</td>
                            </tr>
                            </table>
                            </header>';

            $loanhtml1 = '<html>

                            <body>

                            <table width="100%">
                            <tr>
                                <td width="100%" style="text-align: center;font-weight: bold">'.ucwords(strtolower($agent->name)).' - '.ucwords(strtolower($agent->company)).'</td>

                            </tr>
                            </table>

                            <table width="100%">
                            <tr>
                              <td width="100%" style="text-align: center;font-weight: bold">Settlement from '.$fromDate.' to '.$toDate.'</td>

                            </tr>
                            </table>


                            <table width="100%" style="margin-top: 20px;text-align: center">
                            <tr>
                                <td width="13%" style="text-align: center;font-weight: bold;">Transaction ID</td>
                                <td width="12%" style="text-align: center;font-weight: bold;">Product</td>
                                <td width="13%" style="text-align: center;font-weight: bold;">Customer</td>
                                <td width="13%" style="text-align: center;font-weight: bold;">Amount</td>
                                <td width="12%" style="text-align: center;font-weight: bold;">Total Commision</td>
                                <td width="13%" style="text-align: center;font-weight: bold;">Kadamba Commission</td>
                                <td width="12%" style="text-align: center;font-weight: bold;">Kadamba TDS</td>
                                <td width="13%" style="text-align: center;font-weight: bold;">Agent Commission</td>
                                <td width="12%" style="text-align: center;font-weight: bold;">Agent TDS</td>
                                <td width="12%" style="text-align: center;font-weight: bold;">Settled Amount</td>
                                <td width="12%" style="text-align: center;font-weight: bold;">Time</td>
                            </tr>
                            <tbody>';

            $loanhtml = '<html>

                        <body>
                        <table width="100%">
                            <tr>
                                <td width="100%" style="text-align: center;font-weight: bold">'.ucwords(strtolower($agent->name)).' - '.ucwords(strtolower($agent->company)).'</td>

                            </tr>
                            </table>

                            <table width="100%">
                            <tr>
                              <td width="100%" style="text-align: center;font-weight: bold">Settlement from '.$fromDate.' to '.$toDate.'</td>

                            </tr>
                            </table>

                            <table width="100%" style="margin-top: 20px;text-align: center">
                            <tr>
                                <td width="20%" style="text-align: center;font-weight: bold;">Transaction ID</td>
                                <td width="20%" style="text-align: center;font-weight: bold;">Product</td>
                                <td width="20%" style="text-align: center;font-weight: bold;">Customer</td>
                                <td width="20%" style="text-align: center;font-weight: bold;">Amount</td>
                                <td width="20%" style="text-align: center;font-weight: bold;">Agent Commission</td>
                                <td width="20%" style="text-align: center;font-weight: bold;">Agent TDS</td>
                                <td width="12%" style="text-align: center;font-weight: bold;">Settled Amount</td>
                                <td width="20%" style="text-align: center;font-weight: bold;">Time</td>
                            </tr>
                            <tbody>';

            $kadamba = $agent_comm  = $agent_tds = $kadamba_tds = 0;

            foreach ($transactions as $transaction){
                $kadamba += $transaction->kadamba_comm;
                $agent_comm += $transaction->agent_comm;
                $agent_tds += $transaction->agent_tds;
                $kadamba_tds += $transaction->total_tds;

                $product_name = is_null(Pricing::where('sp_key',$transaction->sp_key)->first()) ? $this->product_array[array_search($transaction->sp_key, array_column($this->product_array, 'service key'))]["service name"] : Pricing::where('sp_key',$transaction->sp_key)->first()->name;

                $loanhtml1 .= '<tr>
                        <td>'.$transaction->id.'</td>
                         <td>'.$product_name.'</td>
                        <td>'.$transaction->customer_params.'</td>';
                $loanhtml1 .=  $transaction->sp_key == "WAP" ? '<td>+'.$transaction->amount.'</td>' : '<td>-'.$transaction->amount.'</td>';
                $loanhtml1 .=  '<td>'.$transaction->total_comm.'</td>
                        <td>'.($transaction->kadamba_comm + $transaction->total_tds) .'</td>
                        <td>'.($transaction->total_tds) .'</td>
                         <td>'.($transaction->agent_comm + $transaction->agent_tds).'</td>
                        <td>'.$transaction->agent_tds.'</td>';
                $loanhtml1 .=  $transaction->sp_key == "WAP" ? '<td>+'.($transaction->amount + ($transaction->total_comm)).'</td>' : '<td>-'.($transaction->amount - ($transaction->total_comm )).'</td>';
                $loanhtml1 .='
                        <td>'.Carbon::createFromFormat('Y-m-d H:i:s',$transaction->created_at)->toTimeString().'</td>
                        </tr>';

                    $loanhtml .= '<tr>
                        <td>'.$transaction->id.'</td>
                        <td>'.$product_name.'</td>
                        <td>'.$transaction->customer_params.'</td>';
                $loanhtml .=  $transaction->sp_key == "WAP" ? '<td>+'.$transaction->amount.'</td>' : '<td>-'.$transaction->amount.'</td>';
                $loanhtml .= '<td>'.($transaction->agent_comm + $transaction->agent_tds).'</td>
                        <td>'.$transaction->agent_tds.'</td>';
                $loanhtml .=  $transaction->sp_key == "WAP" ? '<td>+'.($transaction->amount + ($transaction->agent_comm)).'</td>' : '<td>-'.($transaction->amount - ($transaction->agent_comm)).'</td>';
                $loanhtml .=
                        '<td>'.Carbon::createFromFormat('Y-m-d H:i:s',$transaction->created_at)->toTimeString().'</td>
                          </tr>';
            }

            $loanhtml .= '</tbody></table>';
            $loanhtml1 .= '</tbody></table>';

            $loanhtml1 .= '<div><p style="text-align: center;font-weight: bold;">Total Kadamba Commmission = '.($kadamba + $kadamba_tds).'</p></div>';
            $loanhtml1 .= '<div><p style="text-align: center;font-weight: bold;">Total Kadamba TDS = '.$kadamba_tds.'</p></div>';
            $loanhtml1 .= '<div><p style="text-align: center;font-weight: bold;">Total Kadamba NET Commission = '.$kadamba.'</p></div>';

            $loanhtml1 .= '<div><p style="text-align: center;font-weight: bold;">Total Agent Commmission = '.($agent_comm + $agent_tds).'</p></div>';
            $loanhtml1 .= '<div><p style="text-align: center;font-weight: bold;">Total Agent TDS = '.$agent_tds.'</p></div>';
            $loanhtml1 .= '<div><p style="text-align: center;font-weight: bold;">Total Agent NET Commission = '.$agent_comm.'</p></div>';

            $loanhtml .= '<div><p style="text-align: center;font-weight: bold;">Total Agent Commmission = '.($agent_comm + $agent_tds).'</p></div>';
            $loanhtml .= '<div><p style="text-align: center;font-weight: bold;">Total Agent TDS = '.$agent_tds.'</p></div>';
            $loanhtml .= '<div><p style="text-align: center;font-weight: bold;">Total Agent NET Commission = '.$agent_comm.'</p></div>';
            $loanhtml .= '<div><p style="text-align: center;font-weight: bold;">Total Settlement Amount = '.$agent->balance.'</p></div>';
            $loanhtml1 .= '<div><p style="text-align: center;font-weight: bold;">Total Settlement Amount = '.$agent->balance.'</p></div>';


            $loanhtml .= '</body></html>';

            $loanhtml1 .= '</body></html>';


            $mpdf->WriteHTML($header);
            $mpdf1->WriteHTML($header);
            $mpdf->WriteHTML($loanhtml);
            $mpdf1->WriteHTML($loanhtml1);

            try {
                Mail::send('email_template', [], function ($message) use ($agent, $mpdf) {
                    $message->to($agent->email)
                        ->subject('Settlement of AEPS')
                        ->from('no-reply@kadamba.biz', 'Kadamba AEPS')
                        ->attachData($mpdf->Output($agent->outlet_id, 'S'), $agent->outlet_id . '.pdf');
                });
                Mail::send('email_template', [], function ($message) use ($agent, $mpdf1) {
                    $message->to("coo@kadamba.co.in")
                        ->subject('Settlement of AEPS for '.$agent->name)
                        ->from('no-reply@kadamba.biz', 'Kadamba AEPS')
                        ->attachData($mpdf1->Output($agent->outlet_id, 'S'), $agent->outlet_id . '.pdf');
                });
                Mail::send('email_template', [], function ($message) use ($agent, $mpdf1) {
                    $message->to("ho@kadambasociety.com")
                        ->subject('Settlement of AEPS for '.$agent->name)
                        ->from('no-reply@kadamba.biz', 'Kadamba AEPS')
                        ->attachData($mpdf1->Output($agent->outlet_id, 'S'), $agent->outlet_id . '.pdf');
                });

                $agent->balance = 0.00;
                $agent->last_settlement = $toDate;
                $agent->save();
            }catch (Exception $exception){
                info($exception);
            }

            }else{
                info("agent below transactions $agent->id");
            }
        }
    }
}
