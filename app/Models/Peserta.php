<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Peserta extends Model
{
    protected $table = 'peserta';
    protected $primaryKey = 'id_peserta';
    public $incrementing = true;
    protected $keyType = 'int';
    public $timestamps = true;

    protected $fillable = [
        'id_peserta',
        'nim',
        'nama',
        'email',

        'id_prodi',
        'prodi',

        'gender',
        'no_telp',
        'bahasa_jawa',
        'riwayat_penyakit',
        'berkebutuhan_khusus',
        'detail_penyakit',
        'detail_khusus',
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

    public function prodiRel()
    {
        return $this->belongsTo(
            Prodi::class,
            'id_prodi',
            'id_prodi'
        );
    }
}