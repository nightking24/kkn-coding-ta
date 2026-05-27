<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Dpl extends Model
{
    protected $table = 'dpl';
    protected $primaryKey = 'nik';
    public $incrementing = false;
    protected $keyType = 'string';
    protected $fillable = [
        'nik',
        'nidn',
        'nama',
        'email',
        'no_telp',
        'prodi',
        'fakultas',
        'id_periode'
    ];

    public function kelompok()
    {
        return $this->hasMany(Kelompok::class, 'nik', 'nik');
    }
}