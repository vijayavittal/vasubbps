<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Session;


/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {

    \Illuminate\Support\Facades\Auth::logout();
   if(!\Illuminate\Support\Facades\Auth::guest()){

       return redirect()->to("/dashboard");
   }

   if (\request()->input('link') == null){
       return \Inertia\Inertia::render('Welcome');
   }else{
       if(\Illuminate\Support\Facades\DB::table('password_resets')->where('token',\request()->input('link'))->whereDate('created_at',\Carbon\Carbon::now()->toDateString())->count() == 1){
           return view('auth.passwords.reset')->with(['token'=>\request()->input('link')]);
       }else{
           abort(404);
       }
   }

});

Route::post('/password','Auth\ResetPasswordController@index');

Route::post('/forgot','Auth\ForgotPasswordController@index');

Route::get('/terms',function (){
    return view('terms');
});

Route::get('/privacy',function (){
    return view('privacy');
});



Route::post('/web/transaction', 'TransactionController@validateTransaction');

Route::get('/phone', 'Auth\RegisterController@viewPhone')->name('viewPhone');

Route::post('/phone', 'Auth\RegisterController@phone')->name('phone');

Route::get('/web/callback', 'TransactionController@commitTransaction');

Route::post('/web/confirmation', function () {
    return view('welcome');
});

Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');



Route::get('/dashboard', 'AgentController@dashboard')->middleware(['auth']);

Route::post('/web/balance','AgentController@getBalance')->middleware(['auth']);

Route::get('/transactions', 'TransactionController@show')->middleware(['auth']);

Route::get('/pricings', 'TransactionController@pricings')->middleware(['auth']);

Route::get('/portal','AgentController@getPortal')->middleware(['auth']);

Route::post('/billers','AgentController@getBillers')->middleware(['auth']);

Route::post('/fetch-bill','AgentController@fetchBill')->middleware(['auth']);

Route::post('/pay-bill','AgentController@payBill')->middleware(['auth']);

Route::post('/pay-Bill-Double','AgentController@payBillDouble')->middleware(['auth']);

Route::post('/pay-Bill-Prepaid','AgentController@payPrepaid')->middleware(['auth']);

Route::get('/get-banks','AgentController@getBankList')->middleware(['auth']);

Route::post('/aeps-transaction','AgentController@aepsTransaction')->middleware(['auth']);

Route::get('/integration', 'IntegrationController@show')->middleware(['auth']);

Route::post('/create-jwt','IntegrationController@create')->middleware(['auth']);

Route::get('/ShowTokens', 'IntegrationController@ShowTokens')->middleware(['auth']);

Route::get('/LoadBalance', 'AgentController@showLoadBalance')->middleware(['auth']);

Route::post('/Agents','AgentController@getAgent')->middleware(['auth']);

Route::post('/post-balance','AgentController@updateBalance')->middleware(['auth']);

Route::get('/Get-DTH', 'AgentController@ViewDTH')->middleware(['auth']);

Route::post('/fetch-data', 'AgentController@fetchData');

