<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Peserta;
use App\Models\Periode;
use App\Models\LogActivity;
use App\Imports\PesertaImport;
use Maatwebsite\Excel\Facades\Excel;


class ImportController extends Controller
{
    private function getPeriodeId()
    {
        return session('periode_id')
            ?? request('periode_id')
            ?? Periode::where('status', 'aktif')
                ->value('id_periode');
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
        $periode_id = request('periode_id') ?? session('periode_id');

        $periode = \App\Models\Periode::find($periode_id);

        if ($periode && $periode->status_publish == 1) {
            return redirect('/import?periode_id=' . $periode_id)
                ->with('error', 'Periode sudah dipublish, tidak bisa import data!');
        }

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
            'no hp' => 'no_telp',
            'no_hp' => 'no_telp',
            'no telp' => 'no_telp',
            'no_telp' => 'no_telp',
            'nomor telepon' => 'no_telp',
            'telepon' => 'no_telp',
            'hp' => 'no_telp',
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

            // hapus BOM UTF8
            $h = preg_replace('/^\xEF\xBB\xBF/', '', $h);

            // bersihkan karakter aneh
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
                'no_telp' => $rowData['no_telp'] ?? '',
                'prodi' => $rowData['prodi'] ?? '',
                'gender' => $this->convertGender($rowData['gender'] ?? ''),
                'bahasa_jawa' => $this->convertBahasaJawa($rowData['bahasa_jawa'] ?? ''),
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

        // =========================
        // LAKI-LAKI / PRIA
        // =========================
        if (
            in_array($value, [
                'l',
                'laki-laki',
                'laki laki',
                'pria',
                'male',
                'cowok'
            ])
        ) {
            return 'Pria';
        }

        // =========================
        // PEREMPUAN / WANITA
        // =========================
        if (
            in_array($value, [
                'p',
                'perempuan',
                'wanita',
                'female',
                'cewe'
            ])
        ) {
            return 'Wanita';
        }

        return null;
    }

    private function convertBahasaJawa($value)
    {
        $value = strtolower(trim($value));

        if (
            in_array($value, [
                'bisa',
                'ya',
                'y',
                'yes',
                '1',
                'true'
            ])
        ) {
            return 1;
        }

        return 0;
    }

    private function convertKhusus($value)
    {
        $value = strtolower(trim($value));

        if (
            in_array($value, [
                '',
                '-',
                'tidak',
                'tidak ada',
                'no',
                '0',
                'false'
            ])
        ) {
            return 0;
        }

        return 1;
    }

    private function convertPenyakit($value)
    {
        $value = strtolower(trim($value));

        if (
            in_array($value, [
                '',
                '-',
                'tidak',
                'tidak ada',
                'no',
                '0',
                'false'
            ])
        ) {
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
            $errors = [];

            foreach ($data as $i => $row) {

                // ❌ skip jika kosong
                if (empty($row['nama']) || empty($row['nim'])) {
                    continue;
                }

                // 🚫 VALIDASI DUPLIKAT PER PERIODE
                if (
                    \App\Models\Peserta::where('nim', trim($row['nim']))
                        ->where('id_periode', $periode_id)
                        ->exists()
                ) {
                    $errors[] = "Baris " . ($i + 1) . ": NIM {$row['nim']} sudah ada di periode ini";
                    continue;
                }

                // 💾 SIMPAN DATA
                \App\Models\Peserta::create([
                    'nama' => trim($row['nama']),
                    'nim' => trim($row['nim']),
                    'email' => isset($row['email']) ? trim($row['email']) : null,
                    'no_telp' => isset($row['no_telp']) ? trim($row['no_telp']) : null,
                    'prodi' => isset($row['prodi']) ? trim($row['prodi']) : null,
                    'gender' => $row['gender'] ?? null,
                    'bahasa_jawa' => $row['bahasa_jawa'] ?? 0,
                    'riwayat_penyakit' => $row['riwayat_penyakit'],
                    'detail_penyakit' => $row['detail_penyakit'] ?? null,
                    'berkebutuhan_khusus' => $row['berkebutuhan_khusus'],
                    'detail_khusus' => $row['detail_khusus'] ?? null,
                    'id_periode' => $periode_id
                ]);

                $jumlah++;
            }

            // 🔥 LOG ACTIVITY - HANYA JIKA ADA DATA YANG BERHASIL DISIMPAN
            if ($jumlah > 0) {
                LogActivity::create([
                    'username' => session('user')->username ?? 'Admin',
                    'aktivitas' => "Import Peserta - $jumlah peserta berhasil diimport"
                ]);
            }

            return redirect('/import')
                ->with('success', "Data berhasil disimpan ($jumlah data)")
                ->with('warning', $errors);

        } catch (\Exception $e) {

            return back()->withErrors([
                'error' => 'Gagal import data, silakan cek file CSV'
            ])->withInput();
        }
    }
}