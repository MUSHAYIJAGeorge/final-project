<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factory\HasFactory;

class payment extends Model
{
    //

public function usersserach()
    {
        return $this->belongsToMany('App\User::class','User_id');
    }

}

