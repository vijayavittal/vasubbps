<?php

namespace App\Http\Controllers\Auth;

use App\Agent;
use App\Http\Controllers\Controller;
use App\InstantPay;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Illuminate\View\View;

class RegisterController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users as well as their
    | validation and creation. By default this controller uses a trait to
    | provide this functionality without requiring any additional code.
    |
    */

    use RegistersUsers;

    /**
     * Where to redirect users after registration.
     *
     * @var string
     */
    protected $redirectTo = RouteServiceProvider::HOME;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest');
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        return Validator::make($data, [
            'name' => ['required', 'string', 'max:255'],
            'company' => ['required', 'string', 'max:255'],
            'address'=>['required','string','max:255'],
            'phone'=>['required','digits:10'],
            'email' => ['required', 'string', 'email', 'max:255'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'pan'=>['required','string','regex:/^([a-zA-Z]){5}([0-9]){4}([a-zA-Z]){1}?$/'],
            'pincode'=>['required','digits:6'],
            'otp'=>['required','digits:6']
        ]);
    }

    /**
     * Create a new agent instance after a valid registration from InstantPay.
     *
     * @param  array  $data
     *
     */
    protected function create(array $data)
    {

        $instantPay = new InstantPay();

        $data['pan'] = strtoupper($data['pan']);

        $data['token'] = env('INSTANTPAY_TOKEN');

        $outletid = $instantPay->registration($data);

        if (!$outletid['status']){
            return Redirect::back()->withErrors(['message'=>$outletid['data']]);
        }

        return Agent::create([
            'name' => $data['name'],
            'company'=>$data['company'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'phone'=>$data['phone'],
            'address'=>$data['address'],
            'pan'=>$data['pan'],
            'pincode'=>$data['pincode'],
            'outlet_id'=>$outletid['data'][0],
        ]);
    }

    /**
     * Handle a registration request for the application.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return RedirectResponse
     */
    public function register(Request $request)
    {
        $this->validator($request->all())->validate();

        if($this->create($request->all())){
            return Redirect::to('/')->with(['status'=>true,'message'=>'Successfully Registered']);
        }else{
            return Redirect::to('/')->with(['status'=>true,'message'=>'Please try after sometime']);
        }
    }

    public function viewPhone()
    {
        return view('auth.phone')->with(['status'=>false]);
    }

    public function phone(Request $request)
    {
        $validator = Validator::make($request->all(),[
            'phone'=>['required','digits:10'],
        ]);

        if ($validator->fails()){
            return \response()->json([
                'status'=>'failure',
                'message'=>$validator->errors()->first("phone")
            ]);
        }
        $data = $validator->validated();


        $instantPay = new InstantPay();

        if ($instantPay->registrationOTP($data['phone'])){
            return \response()->json([
               'status' => 'success',
               'phone' => $data['phone']
            ]);
        }else{
           return \response()->json([
               'status'=>'failure',
               'message'=>'User might have already registered on InstantPay'
           ]);
        }
    }

    /**
     * Show the application registration form.
     *
     * @return View
     */
    public function showRegistrationForm()
    {

        if (Session::has('phone')) {
            $phone = Session::get('phone');
            Session::put('phone',$phone);
            return view('auth.register')->with(['phone' => $phone]);
        }else{
            return Redirect::to('/phone');
        }
    }
}
