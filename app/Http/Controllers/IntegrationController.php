<?php

namespace App\Http\Controllers;
use App\Agent;
use App\Pricing;
use App\Integration;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;


class IntegrationController extends Controller 
{
    public function show(Request $request)
    {
        $token = Integration::where('user_id',Auth::id())->first();

        return Inertia::render('Agent/Integration',[
            'token'=>$token
        ]);
    }
    public function create(Request $request)
{
    $data = $request->validate([
       'name'=>'required'
      
    ]);


     $user = Agent::where('id',Auth::id())->first();

    $token_name = Integration::where('name',$request->name)->get();
 
    if($token_name !== $request->name){
        $integration = Integration::create([
            'user_id'=>Auth::id(),
             'name'=>$data["name"],
             'expires'=>Carbon::now()->addYears(10)->format('Y-m-d'),
          
             
         ]);

    $token = $integration->generateToken($integration);

    Integration::where('user_id',$user->id)->update(['token'=>$token]);
    
    }

    return Inertia::render('Agent/Integration',[
        'token'=>$token
    ]);
}
public function ShowTokens(Request $request)
    {
       
        
        return Inertia::render('Agent/ViewIntegration',[
            'token'=>$token
        ]);

    }
}
