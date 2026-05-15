<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ApiLog extends Model
{
    protected $fillable = [
        'city',
        'endpoint',
        'status_code',
        'status',
        'response',
    ];
}