<?php

namespace App;
use Eloquent;

use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    public function customer(){
        return $this->hasOne('App\Customer','id','customerId');
    }
}
