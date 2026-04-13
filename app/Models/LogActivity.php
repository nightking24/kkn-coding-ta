<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LogActivity extends Model
{
    protected $table = 'log_activities';

    protected $primaryKey = 'id_log';

    public $incrementing = true;

    protected $fillable = [
        'username',
        'aktivitas'
    ];
}