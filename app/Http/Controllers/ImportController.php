<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Peserta;
use App\Imports\PesertaImport;
use Maatwebsite\Excel\Facades\Excel;


class ImportController extends Controller
{
    private function getPeriodeId()
    {
        return session('periode_id')
            ?? request('periode_id')
            ?? \App\Models\Periode::where('status_publish', 1)->value('id_periode');
    }

    private function checkPublishLock($periode_id)
    {
        $status = \App\Models\Periode::where('id_periode', $periode_id)
            ->value('status_publish');

        if ($status == 1) {
            return back()->with('error', 'Periode sudah dipublish, data tidak bisa diubah!');
        }

        return null;
    }

    public function index()
    {
        return view('import.index');
    }

    public function preview(Request $request)
    {
        if (!$request->hasFile('file')) {
            return back()->withErrors(['error' => 'File harus diupload']);
        }

        $file = $request->file('file');

        if (!in_array($file->getClientOriginalExtension(), ['csv', 'txt'])) {
            return back()->withErrors(['error' => 'File harus berupa CSV']);
        }

        $content = file_get_contents($file);

        $delimiter = strpos($content, ';') !== false ? ';' : ',';

        $data = array_map(function ($line) use ($delimiter) {
            return str_getcsv($line, $delimiter);
        }, file($file));

        $preview = [];
        $errors = [];

        $mapping = [
            'nama lengkap' => 'nama',
            'nama' => 'nama',
            'nim' => 'nim',
            'email' => 'email',
            'program studi' => 'prodi',
            'prodi' => 'prodi',
            'jenis kelamin' => 'gender',
            'jenis_kelamin' => 'gender',
            'gender' => 'gender',
            'bisa bahasa jawa?' => 'bahasa_jawa',
            'bahasa jawa' => 'bahasa_jawa',
            'riwayat penyakit' => 'riwayat_penyakit',
            'berkebutuhan khusus' => 'berkebutuhan_khusus',
            'kebutuhan khusus' => 'berkebutuhan_khusus'
        ];

        $header = array_map(function ($h) use ($mapping) {
            $h = preg_replace('/[\x00-\x1F\x80-\xFF]/', '', $h);
            $h = strtolower(trim($h));
            return $mapping[$h] ?? $h;
        }, $data[0]);

        $required = ['nama', 'nim'];

        foreach ($required as $col) {
            if (!in_array($col, $header)) {
                return back()->withErrors([
                    'error' => "Kolom '$col' tidak ditemukan di file CSV"
                ]);
            }
        }

        foreach ($data as $i => $row) {

            if ($i == 0)
                continue;

            if (empty(array_filter($row)))
                continue;

            if (count($header) != count($row)) {
                $errors[] = "Baris " . ($i + 1) . ": jumlah kolom tidak sesuai";
                continue;
            }

            $rowData = @array_combine($header, $row);

            if (!$rowData) {
                $errors[] = "Baris " . ($i + 1) . ": format tidak valid";
                continue;
            }

            $nama = trim($rowData['nama'] ?? '');
            $nim = trim($rowData['nim'] ?? '');

            $rowError = [];

            if (!$nama) {
                $rowError[] = 'Nama kosong';
            }

            if (!$nim) {
                $rowError[] = 'NIM kosong';
            } elseif (!is_numeric($nim)) {
                $rowError[] = 'NIM harus angka';
            }

            if (!empty($rowError)) {
                $errors[] = "Baris " . ($i + 1) . ": " . implode(', ', $rowError);
            }

            $preview[] = [
                'nama' => $nama,
                'nim' => $nim,
                'email' => $rowData['email'] ?? '',
                'prodi' => $rowData['prodi'] ?? '',
                'gender' => $this->convertGender($rowData['gender'] ?? ''),
                'bahasa_jawa' => $rowData['bahasa_jawa'] ?? '',
                'riwayat_penyakit' => $this->convertPenyakit($rowData['riwayat_penyakit'] ?? ''),
                'detail_penyakit' => trim($rowData['riwayat_penyakit'] ?? ''),
                'berkebutuhan_khusus' => $this->convertKhusus($rowData['berkebutuhan_khusus'] ?? ''),
                'detail_khusus' => trim($rowData['berkebutuhan_khusus'] ?? ''),
            ];
        }

        return view('import.preview', compact('preview', 'errors'));
    }

    private function convertGender($value)
    {
        $value = strtolower(trim($value));

        if (in_array($value, ['l', 'laki-laki', 'laki laki', 'male'])) {
            return 'L';
        }

        if (in_array($value, ['p', 'perempuan', 'female'])) {
            return 'P';
        }

        return null;
    }

    private function convertKhusus($value)
    {
        $value = strtolower(trim($value));

        if ($value == '' || $value == '-' || $value == 'tidak') {
            return 0;
        }

        return 1; // apapun isi dianggap "ya"
    }

    private function convertPenyakit($value)
    {
        $value = strtolower(trim($value));

        if ($value == '' || $value == '-' || $value == 'tidak') {
            return 0;
        }

        return 1;
    }


    public function store(Request $request)
    {
        $periode_id = $this->getPeriodeId();
        if ($lock = $this->checkPublishLock($periode_id)) {
            return $lock;
        }

        $data = json_decode($request->data, true);

        if (!$data || !is_array($data)) {
            return back()->withErrors([
                'error' => 'Data tidak valid atau kosong'
            ]);
        }

        try {
            $jumlah = 0;
            foreach ($data as $row) {

                if (empty($row['nama']) || empty($row['nim'])) {
                    continue;
                }

                if (
                    Peserta::where('nim', trim($row['nim']))
                        ->where('id_periode', $periode_id)
                        ->exists()
                ) {
                    continue;
                }

                Peserta::create([
                    'nama' => trim($row['nama']),
                    'nim' => trim($row['nim']),
                    'email' => isset($row['email']) ? trim($row['email']) : null,
                    'prodi' => isset($row['prodi']) ? trim($row['prodi']) : null,
                    'gender' => strtoupper($row['gender']) == 'L' ? 'L' : 'P',
                    'bahasa_jawa' => isset($row['bahasa_jawa']) ? trim($row['bahasa_jawa']) : null,
                    'riwayat_penyakit' => $row['riwayat_penyakit'],
                    'detail_penyakit' => $row['detail_penyakit'] ?? null,
                    'berkebutuhan_khusus' => $row['berkebutuhan_khusus'],
                    'detail_khusus' => $row['detail_khusus'] ?? null,

                    'id_periode' => $periode_id
                ]);
                $jumlah++;
            }

            return redirect('/import')->with('success', 'Data berhasil disimpan');

        } catch (\Exception $e) {

            return back()->withErrors([
                'error' => 'Gagal import data, silakan cek file CSV'
            ])->withInput();
        }
    }
}