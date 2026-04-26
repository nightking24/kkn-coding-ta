<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Peserta;
use App\Models\Apl;
use App\Models\Dpl;
use App\Models\TuanRumah;

class Kelompok extends Model
{
    protected $table = 'kelompok';
    protected $primaryKey = 'id_kelompok';

    protected $fillable = [
        'nomor_kelompok',
        'desa',
        'dusun',
        'nama_dukuh',
        'id_tuan_rumah',
        'nomor_telepon',
        'alamat',
        'faskes',
        'kapasitas',
        'semester',
        'tahun_kkn',
        'latitude',
        'longitude',
        'nama_kecamatan',
        'nik',
        'nim',
        'id_periode'
    ];

    // Relasi ke Periode
    public function periode()
    {
        return $this->belongsTo('App\Models\Periode', 'id_periode', 'id_periode');
    }

    public function dpl()
    {
        return $this->belongsTo(Dpl::class, 'nik', 'nik');
    }

    public function apl()
    {
        return $this->belongsTo(Apl::class, 'nim', 'nim');
    }

    public function peserta()
    {
        return $this->hasMany(\App\Models\Peserta::class, 'id_kelompok', 'id_kelompok');
    }

    public function tuanRumah()
    {
        return $this->belongsTo(TuanRumah::class, 'id_tuan_rumah');
    }
}