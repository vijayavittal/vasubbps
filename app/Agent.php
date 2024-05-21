<?php

namespace App;
use Tymon\JWTAuth\Contracts\JWTSubject;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Model;

//An Agent Model to hold all info about agents at AEPS

class Agent extends Authenticatable implements JWTSubject
{
    //Total of 7 fillables
    protected $fillable = ['name','pan','password','balance','phone','address','pincode','outlet_id','company','email','last_settlement'];

    protected $hidden = ['password'];

    public $customClaims = [];

    public function transactions(){
        return $this->hasMany(Transaction::class,'pan','pan');
    }

    public function getJWTIdentifier()
    {
        return $this->getKey();
    }
    

    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     *
     * @return array
     */
    public function getJWTCustomClaims()
    {
        return $this->customClaims;    
    }
    
}
