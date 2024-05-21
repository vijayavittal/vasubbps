<?php

namespace App\Http\Controllers\Auth;

use App\Agent;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\SendsPasswordResetEmails;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Str;

class ForgotPasswordController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Password Reset Controller
    |--------------------------------------------------------------------------
    |
    | This controller is responsible for handling password reset emails and
    | includes a trait which assists in sending these notifications from
    | your application to your users. Feel free to explore this trait.
    |
    */

    use SendsPasswordResetEmails;

    public function index(Request $request)
    {
        $request->validate([
            'email' => ['required', 'string', 'email', 'max:255'],
        ]);

        $agent = Agent::where('email',$request->input('email'))->first();

        if (is_null($agent)){
           return Redirect::back()->withErrors(['message'=>'Your Email Does not exist']);
        }else{
            if(DB::table('password_resets')->where('email',$agent->email)->whereDate('created_at',Carbon::now()->toDateString())->count() == 0) {
                $this->sendResetLinkEmail($agent);
                return Redirect::back()->withErrors(['message' => 'Reset link sent to your E-mail']);
            }else{
                return Redirect::back()->withErrors(['message' => 'Already reset link sent to your E-mail']);
            }
        }
    }

    public function sendResetLinkEmail(Agent $agent)
    {


        do{
            $str = Str::random(32);
        }while( DB::table('password_resets')->where('token', $str)->count());

        DB::table('password_resets')->insert([
            'email' => $agent->email,
            'token' => $str,
            'created_at'=>Carbon::now()
        ]);

        $link = env('APP_URL').'?link='.$str;

        Mail::send('emails.passwordReset', ['link'=>$link], function ($message) use ($agent) {
            $message->to($agent->email)
                ->subject('Kadamba AEPS Password Reset')
                ->from('no-reply@aeps.kadamba.biz', 'Kadamba AEPS');
        });

    }

}
