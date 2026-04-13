<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Peserta extends Model
{
    protected $table = 'peserta';
    protected $primaryKey = 'nim';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'nim',
        'nama',
        'email',
        'prodi',
        'gender',
        'bahasa_jawa',
        'riwayat_penyakit',
        'berkebutuhan_khusus',
        'id_kelompok',
        'id_periode'
    ];

    public function kelompok()
    {
        return $this->belongsTo(Kelompok::class, 'id_kelompok', 'id_kelompok');
    }

    public function periode()
    {
        return $this->belongsTo('App\Models\Periode', 'id_periode', 'id_periode');
    }
}