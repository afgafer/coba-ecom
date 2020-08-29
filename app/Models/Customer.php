<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticable;
use Illuminate\Notifications\Notifiable;

class Customer extends Authenticable
{
    use Notifiable;
    protected $guarded=[];

    public function setPasswordAttribute($value){
        $this->attributes['password']=bcrypt($value);
    }
}
