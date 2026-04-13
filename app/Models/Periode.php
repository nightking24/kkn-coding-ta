<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Periode extends Model
{
    protected $table = 'periode';
    protected $primaryKey = 'id_periode';
    protected $fillable = [
        'nama_kkn',
        'tahun_kkn',
        'status',
        'tanggal_mulai',
        'tanggal_selesai',
        'lokasi',
        'status_publish'
    ];

    public $timestamps = true;

    public function kelompok()
    {
        return $this->hasMany('App\Models\Kelompok', 'id_periode', 'id_periode');
    }

    public function peserta()
    {
        return $this->hasMany('App\Models\Peserta', 'id_periode', 'id_periode');
    }
}