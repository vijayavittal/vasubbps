<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    protected $fillable = ['amount','pan','status','request_id','customer_params','kadamba_comm','total_comm','total_tds','agent_comm','agent_tds','sp_key','txnReferenceId','txn_reference_id'];
}
