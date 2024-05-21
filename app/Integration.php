<?php


namespace App;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use App\Agent;
use JWTAuth;
use Carbon\Carbon;



class Integration extends Model
{



    protected $fillable = ['name','expires','token','user_id'];

    public function generateToken(Integration $integration)
    {
        $user = $integration->user;
       
   //     $customClaims = [];
        $user->customClaims["user_id"]=$user->id;
        $user->customClaims["integration_id"]=$integration->id;
        $user->customClaims["exp"]=Carbon::now()->addYears(10)->unix();
        $user->customClaims["production"] = true;
      

        $token = JWTAuth::fromUser($user, $user->customClaims);

        return $token;
    }

    public function user() {
        return $this->belongsTo(Agent::class);
    }
}
