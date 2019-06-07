<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SubjectQuestion extends Model
{
    //
    protected $fillable = ['idSubject',
                            'category',
                            'number'];

}
