<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Apl extends Model
{
    protected $table = 'apl';
    protected $primaryKey = 'nim';
    public $incrementing = false;
    protected $keyType = 'string';
    protected $fillable = [
        'nim',
        'nama',
        'email',
        'no_telp',
        'id_periode'
    ];
}