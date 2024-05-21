<?php

namespace App\Console\Commands;

use App\Pricing;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class SyncBillerDetailsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'SyncBillerCommand';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync the details of the biller established in the portal';

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
        $billers = Pricing::all();

        foreach ($billers as $biller){

            if ($biller->id == 9 || $biller->id == 10 || $biller->id == 11){
                continue;
            }
            $response = Http::asJson()->post(env('INSTANTPAY_GET_BILLER_DETAIL_URL'),[
                "token" => env('INSTANTPAY_AEPS_TOKEN'),
                "request"=>[
                    "biller_id"=>$biller->sp_key
                ]
            ]);

            $xml = simplexml_load_string($response);
            $json = json_encode($xml);
            $array = json_decode($json,TRUE);

            $biller->params = json_encode($array["data"]["biller"]["item"]["params"]);
            $biller->bill_fetch = $array["data"]["biller"]["item"]["fetch_requirement"] == "MANDATORY" ? "Y" : "N";
            $biller->save();
        }
    }
}
