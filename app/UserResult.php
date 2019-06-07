<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class UserResult extends Model
{
    //
    protected $fillable = ['idUser',
    'idSubject',
    'result'
    ];
}
