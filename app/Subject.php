<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Subject extends Model
{
    //
    protected $fillable = ['name',
                            'idAuthor',
                            'noQuestions',
                            'multipleChoice',
                            'separatePage',
                            'canBack',
                            'limitedTime',
                            'time',
                            'course',
                            'description',
                            'shared',
                            'categorysed',
                            'randomize',
                            'subject'];
}
