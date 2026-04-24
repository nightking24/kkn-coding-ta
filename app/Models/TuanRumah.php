<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TuanRumah extends Model
{
    protected $table = 'tuan_rumah';
    protected $primaryKey = 'id_tuan_rumah';

    protected $fillable = [
        'nama_tuan_rumah',
        'dusun',
        'desa',
        'nomor_telepon',
        'alamat',
        'latitude',
        'longitude'
    ];
}