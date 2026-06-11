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
    // Mengambil ID periode aktif dari session, request, atau periode yang berstatus berjalan
    private function getPeriodeId()
    {
        return
            // Prioritas 1: periode yang tersimpan di session
            session('periode_id')

            // Prioritas 2: periode yang dikirim dari request
            ?? request('periode_id')

            // Prioritas 3: periode yang berstatus berjalan di database
            ?? Periode::where('status', 'berjalan')
                ->value('id_periode');
    }

    // Memeriksa apakah periode sudah dipublish sehingga data peserta tidak dapat diubah
    private function checkPublishLock($periode_id)
    {
        // Mengambil status publish dari periode yang dipilih
        $status = \App\Models\Periode::where('id_periode', $periode_id)
            ->value('status_publish');

        // Jika periode sudah dipublish
        if ($status == 1) {
            // Batalkan proses perubahan data
            return back()->with('error', 'Periode sudah dipublish, data tidak bisa diubah!');
        }

        // Jika belum publish lanjutkan proses
        return null;
    }

    // Menampilkan halaman import data peserta
    public function index()
    {
        return view('import.index');
    }

    // Menampilkan preview data CSV sebelum disimpan ke database
    public function preview(Request $request)
    {
        // Mengambil ID periode aktif
        $periode_id = request('periode_id') ?? session('periode_id');

        // Mengambil data periode
        $periode = \App\Models\Periode::find($periode_id);

        // Mencegah import jika periode sudah dipublish
        if ($periode && $periode->status_publish == 1) {
            return redirect('/import?periode_id=' . $periode_id)
                ->with('error', 'Periode sudah dipublish, tidak bisa import data!');
        }

        // Memastikan file sudah dipilih
        if (!$request->hasFile('file')) {
            return back()->withErrors(['error' => 'File harus diupload']);
        }

        // Mengambil file yang diupload
        $file = $request->file('file');

        // Memastikan file berformat CSV atau TXT
        if (!in_array($file->getClientOriginalExtension(), ['csv', 'txt'])) {
            return back()->withErrors(['error' => 'File harus berupa CSV']);
        }

        // Membaca seluruh isi file
        $content = file_get_contents($file);

        // Menentukan delimiter otomatis (; atau ,)
        $delimiter = strpos($content, ';') !== false ? ';' : ',';

        // Membaca setiap baris CSV dan mengubahnya menjadi array
        $data = array_map(function ($line) use ($delimiter) {
            return str_getcsv($line, $delimiter);
        }, file($file));

        // Menyimpan data preview yang akan ditampilkan ke user
        $preview = [];

        // Menyimpan daftar error yang ditemukan saat validasi CSV
        $errors = [];

        // Mapping berbagai variasi nama header CSV ke nama field sistem
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

        // Membersihkan dan menyesuaikan nama header CSV
        $header = array_map(function ($h) use ($mapping) {

            // Menghapus karakter BOM UTF-8 yang sering muncul dari Excel
            $h = preg_replace('/^\xEF\xBB\xBF/', '', $h);

            // Menghapus karakter aneh yang tidak diperlukan
            $h = preg_replace('/[\x00-\x1F\x80-\xFF]/', '', $h);

            // Mengubah menjadi huruf kecil dan menghapus spasi depan/belakang
            $h = strtolower(trim($h));

            // Mengubah nama header sesuai mapping sistem
            return $mapping[$h] ?? $h;

        }, $data[0]);

        // Menentukan kolom yang wajib ada dalam file CSV
        $required = ['nama', 'nim'];

        foreach ($required as $col) {
            // Jika kolom wajib tidak ditemukan
            if (!in_array($col, $header)) {
                return back()->withErrors([
                    'error' => "Kolom '$col' tidak ditemukan di file CSV"
                ]);
            }
        }

        // Memproses seluruh baris CSV
        foreach ($data as $i => $row) {

            // Lewati baris pertama karena merupakan header
            if ($i == 0)
                continue;

            // Lewati baris kosong
            if (empty(array_filter($row)))
                continue;

            // Memastikan jumlah kolom sesuai dengan header
            if (count($header) != count($row)) {
                $errors[] = "Baris " . ($i + 1) . ": jumlah kolom tidak sesuai";
                continue;
            }

            // Menggabungkan header dengan isi data
            $rowData = @array_combine($header, $row);

            // Jika format data gagal dibaca
            if (!$rowData) {
                $errors[] = "Baris " . ($i + 1) . ": format tidak valid";
                continue;
            }

            // Mengambil nama peserta
            $nama = trim($rowData['nama'] ?? '');

            // Membersihkan NIM dari karakter petik
            $nim = str_replace("'", '', trim($rowData['nim'] ?? ''));

            // Menampung error pada baris ini
            $rowError = [];

            // Validasi nama
            if (!$nama) {
                $rowError[] = 'Nama kosong';
            }

            // Validasi NIM
            if (!$nim) {
                $rowError[] = 'NIM kosong';
            } elseif (!is_numeric($nim)) {
                $rowError[] = 'NIM harus angka';
            }

            // Menyimpan error jika ditemukan
            if (!empty($rowError)) {
                $errors[] = "Baris " . ($i + 1) . ": " . implode(', ', $rowError);
            }

            $prodi = \App\Models\Prodi::whereRaw(
                'LOWER(TRIM(nama_prodi)) = ?',
                [strtolower(trim($rowData['prodi'] ?? ''))]
            )->first();

            // Menyimpan data untuk ditampilkan pada halaman preview
            $preview[] = [
                'nama' => $nama,
                'nim' => $nim,
                'email' => $rowData['email'] ?? '',
                'no_telp' => $rowData['no_telp'] ?? '',
                'id_prodi' => $prodi?->id_prodi,
                'prodi' => $rowData['prodi'] ?? '',

                // Konversi data agar sesuai format sistem
                'gender' => $this->convertGender($rowData['gender'] ?? ''),
                'bahasa_jawa' => $this->convertBahasaJawa($rowData['bahasa_jawa'] ?? ''),
                'riwayat_penyakit' => $this->convertPenyakit($rowData['riwayat_penyakit'] ?? ''),
                'detail_penyakit' => trim($rowData['riwayat_penyakit'] ?? ''),
                'berkebutuhan_khusus' => $this->convertKhusus($rowData['berkebutuhan_khusus'] ?? ''),
                'detail_khusus' => trim($rowData['berkebutuhan_khusus'] ?? ''),
            ];
        }

        // Menampilkan halaman preview sebelum data disimpan
        return view('import.preview', compact('preview', 'errors'));
    }

    // Mengubah berbagai format jenis kelamin menjadi Pria atau Wanita
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

    // Mengubah data kemampuan bahasa Jawa menjadi nilai 1 atau 0
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

    // Mengubah data kebutuhan khusus menjadi nilai 1 atau 0
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

    // Mengubah data riwayat penyakit menjadi nilai 1 atau 0
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

    // Menyimpan data hasil preview ke database peserta
    public function store(Request $request)
    {
        // Mengambil periode aktif
        $periode_id = $this->getPeriodeId();

        // Mencegah perubahan jika periode sudah dipublish
        if ($lock = $this->checkPublishLock($periode_id)) {
            return $lock;
        }

        // Mengubah data JSON dari preview menjadi array PHP
        $data = json_decode($request->data, true);

        // Memastikan data valid
        if (!$data || !is_array($data)) {
            return back()->withErrors([
                'error' => 'Data tidak valid atau kosong'
            ]);
        }

        try {
            // Menghitung jumlah data peserta yang berhasil disimpan
            $jumlah = 0;

            // Menyimpan daftar pesan error selama proses import
            $errors = [];

            // Memproses setiap data peserta hasil preview
            foreach ($data as $i => $row) {

                // Lewati data jika nama atau NIM kosong
                if (empty($row['nama']) || empty($row['nim'])) {
                    continue;
                }

                // Memastikan NIM belum pernah diimport pada periode yang sama
                if (
                    \App\Models\Peserta::where('nim', trim($row['nim']))
                        ->where('id_periode', $periode_id)
                        ->exists()
                ) {
                    // Menyimpan pesan error jika ditemukan NIM duplikat
                    $errors[] = "Baris " . ($i + 1) . ": NIM {$row['nim']} sudah ada di periode ini";
                    continue;
                }

                // Menyimpan data peserta ke database
                \App\Models\Peserta::create([
                    'nama' => trim($row['nama']),
                    'nim' => str_replace("'", '', trim($row['nim'])),
                    'email' => isset($row['email']) ? trim($row['email']) : null,
                    'no_telp' => isset($row['no_telp']) ? trim($row['no_telp']) : null,
                    'id_prodi' => $row['id_prodi'] ?? null,
                    'gender' => $row['gender'] ?? null,
                    'bahasa_jawa' => $row['bahasa_jawa'] ?? 0,
                    'riwayat_penyakit' => $row['riwayat_penyakit'] ?? 0,
                    'detail_penyakit' => $row['detail_penyakit'] ?? null,
                    'berkebutuhan_khusus' => $row['berkebutuhan_khusus'] ?? 0,
                    'detail_khusus' => $row['detail_khusus'] ?? null,
                    'id_periode' => $periode_id
                ]);

                // Menambah jumlah data yang berhasil diimport
                $jumlah++;
            }

            // Mencatat log aktivitas jika ada data yang berhasil disimpan
            if ($jumlah > 0) {
                LogActivity::create([
                    // Menyimpan username pengguna yang melakukan import
                    'username' => session('user')->username ?? 'Admin',

                    // Menyimpan aktivitas import ke log sistem
                    'aktivitas' => "Import Peserta - $jumlah peserta berhasil diimport"
                ]);
            }

            // Kembali ke halaman import dengan pesan sukses
            return redirect('/import')

                // Menampilkan jumlah data yang berhasil disimpan
                ->with('success', "Data berhasil disimpan ($jumlah data)")

                // Menampilkan daftar data yang gagal diimport
                ->with('warning', $errors);

        } catch (\Exception $e) {

            // Menampilkan pesan error jika proses import gagal
            return back()->withErrors([
                'error' => 'Gagal import data, silakan cek file CSV'
            ])->withInput();
        }
    }

    // Mengunduh template CSV yang dapat digunakan untuk import peserta
    public function downloadTemplate()
    {
        // Menentukan nama file template CSV
        $filename = 'template_import_peserta.csv';

        // Menentukan header HTTP agar browser mengunduh file CSV
        $headers = [
            // Menentukan tipe file CSV
            'Content-Type' => 'text/csv; charset=UTF-8',

            // Menentukan nama file yang akan diunduh
            'Content-Disposition' => "attachment; filename=\"$filename\"",
        ];

        // Membuat callback untuk menghasilkan isi file CSV
        $callback = function () {

            // Membuka output stream untuk file CSV
            $file = fopen('php://output', 'w');

            // Menambahkan BOM UTF-8 agar karakter terbaca dengan benar di Excel
            fprintf($file, chr(0xEF) . chr(0xBB) . chr(0xBF));

            // Menulis header kolom sesuai format sistem
            fputcsv($file, [
                'nim',
                'nama',
                'email',
                'prodi',
                'no telp',
                'jenis kelamin',
                'bahasa jawa',
                'riwayat penyakit',
                'kebutuhan khusus'
            ], ';');

            // CONTOH DATA BARIS 1
            fputcsv($file, [
                '11190401',
                'Bima Wijaya',
                'bimawijaya@gmail.com',
                'Manajemen',
                '081234567883',
                'Pria',
                'Bisa',
                '-',
                '-'
            ], ';');

            // CONTOH DATA BARIS 2
            fputcsv($file, [
                '11190402',
                'Pandu Kusuma',
                'pandukusuma@gmail.com',
                'Manajemen',
                '081234567873',
                'Pria',
                'Tidak',
                'Asma',
                '-'
            ], ';');

            // CONTOH DATA BARIS 3
            fputcsv($file, [
                '61190403',
                'Mikaela Cyntia',
                'mikaela@gmail.com',
                'Arsitektur',
                '081234567842',
                'Wanita',
                'Bisa',
                '-',
                'Disabilitas Ringan'
            ], ';');
            // Menutup file setelah selesai ditulis
            fclose($file);
        };

        // Mengirim file CSV ke browser untuk diunduh
        return response()->stream($callback, 200, $headers);
    }
}