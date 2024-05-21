<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Auth\ResetsPasswords;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Redirect;
use App\Agent;

class ResetPasswordController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Password Reset Controller
    |--------------------------------------------------------------------------
    |
    | This controller is responsible for handling password reset requests
    | and uses a simple trait to include this behavior. You're free to
    | explore this trait and override any methods you wish to tweak.
    |
    */

    use ResetsPasswords;

    /**
     * Where to redirect users after resetting their password.
     *
     * @var string
     */
    protected $redirectTo = RouteServiceProvider::HOME;


    public function index(Request $request)
    {
        $request->validate([
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        if(DB::table('password_resets')->where('token',\request()->input('token'))->whereDate('created_at',\Carbon\Carbon::now()->toDateString())->count() == 1){
            $email = DB::table('password_resets')->where('token',\request()->input('token'))->whereDate('created_at',\Carbon\Carbon::now()->toDateString())->first()->email;
            $agent = Agent::where('email',$email)->first();
            $agent->update([
                'password'=>Hash::make($request->input('password'))
            ]);
            DB::table('password_resets')->where('token',\request()->input('token'))->whereDate('created_at',\Carbon\Carbon::now()->toDateString())->delete();
            return Redirect::to('/')->withErrors(['message'=>'Password successfully reset']);
        }else{
            abort(404);
        }
    }
}
