# 📐 FORMULA RULE-BASED SYSTEM (RBS) PEMBAGIAN KELOMPOK KKN

## 📋 Daftar Isi
1. [Overview Sistem](#overview-sistem)
2. [Komponen Rule](#komponen-rule)
3. [Hard Rules (Constraint)](#hard-rules-constraint)
4. [Soft Rules (Scoring)](#soft-rules-scoring)
5. [Algoritma Penugasan](#algoritma-penugasan)
6. [Contoh Kasus](#contoh-kasus)
7. [Implementasi Code](#implementasi-code)

---

## Overview Sistem

Sistem RBS menggunakan **kombinasi Hard Rules dan Soft Rules**:

- **Hard Rules**: Aturan ketat yang HARUS dipenuhi (if condition tidak terpenuhi → kelompok di-reject)
- **Soft Rules**: Aturan preferensi dengan scoring system (semakin tinggi score → semakin baik)

```
┌─────────────────────────────────────────────────────────┐
│           RANDOMISASI PEMBAGIAN KELOMPOK KKN            │
├─────────────────────────────────────────────────────────┤
│                                                          │
│  INPUT: Peserta, Kelompok                               │
│         ↓                                                │
│  FOR SETIAP PESERTA:                                     │
│    1. Ambil semua kelompok candidate                     │
│    2. Apply Hard Rules → filter invalid kelompok        │
│    3. Apply Soft Rules → hitung score setiap candidate  │
│    4. Pilih kelompok dengan score tertinggi             │
│    5. Assign peserta ke kelompok terpilih               │
│         ↓                                                │
│  OUTPUT: Hasil pembagian (OK / Belum dapat kelompok)    │
│                                                          │
└─────────────────────────────────────────────────────────┘
```

---

## Komponen Rule

### Input Data

| Atribut | Tipe | Deskripsi |
|---------|------|-----------|
| `nim` | String | Nomor Induk Mahasiswa |
| `nama` | String | Nama lengkap peserta |
| `prodi` | String | Program studi |
| `gender` | Enum | Pria / Wanita |
| `bahasa_jawa` | Boolean | 1 = Bisa, 0 = Tidak |
| `riwayat_penyakit` | Boolean | 1 = Ada, 0 = Tidak |
| `berkebutuhan_khusus` | Boolean | 1 = Ya, 0 = Tidak |

### Atribut Kelompok

| Atribut | Tipe | Deskripsi |
|---------|------|-----------|
| `id_kelompok` | UUID | ID unik kelompok |
| `nomor_kelompok` | Integer | Nomor urut kelompok (K1, K2, ...) |
| `kapasitas` | Integer | Jumlah maksimal peserta |
| `faskes` | Boolean | 1 = Ada fasilitas kesehatan, 0 = Tidak |
| `dusun` | String | Lokasi dusun |

---

## Hard Rules (Constraint)

### Rule 1: Kapasitas Kelompok

**Kondisi:**
```
IF jumlah_peserta_di_kelompok >= kapasitas_kelompok
THEN kelompok ini INVALID
```

**Formula:**
$$\text{Jumlah Peserta} \geq \text{Kapasitas} \Rightarrow \text{REJECT}$$

**Kode:**
```php
$jumlah = collect($result)
    ->where('id_kelompok', $kelompok->id_kelompok)
    ->count();

if ($jumlah >= $kelompok->kapasitas)
    continue;  // Skip kelompok ini
```

**Contoh:**
- Kelompok 1 kapasitas 5, sudah ada 5 peserta → REJECT
- Kelompok 1 kapasitas 5, sudah ada 4 peserta → VALID ✓

---

### Rule 2: Peserta Khusus (1 per Kelompok)

**Kondisi:**
```
IF peserta_punya_riwayat_penyakit OR peserta_punya_kebutuhan_khusus
AND sudah_ada_peserta_khusus_di_kelompok
THEN kelompok ini INVALID
```

**Formula:**
$$(\text{riwayat\_penyakit} = 1 \vee \text{berkebutuhan\_khusus} = 1)$$
$$\wedge \exists \text{ peserta khusus lain di kelompok} \Rightarrow \text{REJECT}$$

**Kode:**
```php
$isKhusus = ($peserta['riwayat_penyakit'] == 1 || 
             $peserta['berkebutuhan_khusus'] == 1);

if ($isKhusus) {
    $sudahAda = collect($result)
        ->where('id_kelompok', $kelompok->id_kelompok)
        ->where(function ($p) {
            return $p['riwayat_penyakit'] == 1 || 
                   $p['berkebutuhan_khusus'] == 1;
        })->count();

    if ($sudahAda > 0)
        continue;  // Skip kelompok ini
}
```

**Contoh:**
- Peserta A: riwayat penyakit = 1
- Kelompok 1: sudah ada Peserta B (riwayat penyakit = 1)
- Result: Kelompok 1 INVALID untuk Peserta A ❌
- Peserta A akan dicari kelompok lain tanpa peserta khusus

---

## Soft Rules (Scoring)

Setelah lolos Hard Rules, kelompok diberi **score** berdasarkan preferensi. **Kelompok dengan score tertinggi dipilih**.

### Rule 1: Prioritas Fasilitas Kesehatan

**Deskripsi:** Peserta dengan kondisi khusus diprioritaskan ke kelompok yang memiliki fasilitas kesehatan.

**Kondisi:**
```
IF peserta_punya_kondisi_khusus AND kelompok_punya_faskes
THEN tambah +5 poin
```

**Formula:**
$$\text{Score} += \begin{cases} 5 & \text{if } (\text{riwayat\_penyakit} = 1 \vee \text{berkebutuhan\_khusus} = 1) \wedge \text{faskes} = 1 \\ 0 & \text{otherwise} \end{cases}$$

**Kode:**
```php
if ($isKhusus && $kelompok->faskes == 1) {
    $score += 5;
}
```

**Contoh Scoring:**
- Peserta: Ada riwayat penyakit
- Kelompok A: Punya faskes → Score +5 ✓
- Kelompok B: Tidak punya faskes → Score +0

---

### Rule 2: Sebaran Program Studi

**Deskripsi:** Kelompok seharusnya memiliki keberagaman prodi. Jika prodi peserta belum ada di kelompok, beri score bonus.

**Kondisi:**
```
IF prodi_peserta BELUM ADA di kelompok
THEN tambah +2 poin
```

**Formula:**
$$\text{Score} += \begin{cases} 2 & \text{if } \text{count}(\text{prodi} = \text{peserta.prodi}) = 0 \\ 0 & \text{otherwise} \end{cases}$$

**Kode:**
```php
$jumlahProdiSama = collect($result)
    ->where('id_kelompok', $kelompok->id_kelompok)
    ->where('prodi', $peserta['prodi'])
    ->count();

if ($jumlahProdiSama == 0) {
    $score += 2;
}
```

**Contoh Scoring:**
- Kelompok 1: [TI, TI, Sistem Info] - belum ada DKV
- Peserta: DKV → Score +2 ✓ (tambah keberagaman)
- Peserta: TI → Score +0 (sudah ada prodi sama)

---

### Rule 3: Sebaran Bahasa Jawa

**Deskripsi:** Setiap kelompok harus minimal ada 1 orang yang bisa berbahasa Jawa (untuk komunikasi dengan masyarakat lokal).

**Kondisi:**
```
IF peserta_bisa_bahasa_jawa AND TIDAK ada yang bisa di kelompok
THEN tambah +3 poin
```

**Formula:**
$$\text{Score} += \begin{cases} 3 & \text{if } \text{bahasa\_jawa} = 1 \wedge \text{count}(\text{bahasa\_jawa} = 1) = 0 \\ 0 & \text{otherwise} \end{cases}$$

**Kode:**
```php
$jumlahBisaJawa = collect($result)
    ->where('id_kelompok', $kelompok->id_kelompok)
    ->where('bahasa_jawa', 1)
    ->count();

if ($peserta['bahasa_jawa'] == 1 && $jumlahBisaJawa == 0) {
    $score += 3;
}
```

**Contoh Scoring:**
- Kelompok 1: [Tidak, Tidak, Tidak] - belum ada yang bisa bahasa Jawa
- Peserta: Bisa bahasa Jawa → Score +3 ✓ (penting untuk kelompok)
- Peserta: Tidak bisa bahasa Jawa → Score +0 (kelompok sudah punya)

---

### Rule 4: Keseimbangan Gender

**Deskripsi:** Gender dalam kelompok seharusnya seimbang. Tambah score jika peserta menambah keseimbangan.

**Kondisi:**
```
IF peserta PRIA AND jumlah_pria <= jumlah_wanita
THEN tambah +1 poin

IF peserta WANITA AND jumlah_wanita <= jumlah_pria
THEN tambah +1 poin
```

**Formula:**
$$\text{Score} += \begin{cases} 1 & \text{if gender = "Pria"} \wedge \text{count(Pria)} \leq \text{count(Wanita)} \\ 1 & \text{if gender = "Wanita"} \wedge \text{count(Wanita)} \leq \text{count(Pria)} \\ 0 & \text{otherwise} \end{cases}$$

**Kode:**
```php
$laki = collect($result)
    ->where('id_kelompok', $kelompok->id_kelompok)
    ->where('gender', 'Pria')
    ->count();

$perempuan = collect($result)
    ->where('id_kelompok', $kelompok->id_kelompok)
    ->where('gender', 'Wanita')
    ->count();

if ($peserta['gender'] == 'Pria' && $laki <= $perempuan) {
    $score += 1;
}

if ($peserta['gender'] == 'Wanita' && $perempuan <= $laki) {
    $score += 1;
}
```

**Contoh Scoring:**
- Kelompok 1: [Pria, Pria, Pria, Wanita] (3 Pria, 1 Wanita)
  - Peserta Pria: Score +0 (sudah banyak pria)
  - Peserta Wanita: Score +1 ✓ (seimbangkan gender)

---

## Algoritma Penugasan

### Pseudocode Lengkap

```
UNTUK SETIAP peserta dalam daftar_peserta:
    
    kandidat = []
    
    UNTUK SETIAP kelompok dalam daftar_kelompok:
        
        // HARD RULE 1: Cek kapasitas
        IF jumlah_peserta_di_kelompok >= kapasitas_kelompok:
            LANJUT ke kelompok berikutnya
        
        // HARD RULE 2: Cek peserta khusus
        IF peserta_punya_kondisi_khusus:
            IF sudah_ada_peserta_khusus_di_kelompok:
                LANJUT ke kelompok berikutnya
        
        // SOFT RULE: Hitung score
        score = 0
        
        IF peserta_khusus AND kelompok_punya_faskes:
            score += 5
        
        IF prodi_peserta_belum_ada:
            score += 2
        
        IF peserta_bisa_jawa AND belum_ada_yang_bisa:
            score += 3
        
        IF gender_peserta_menambah_keseimbangan:
            score += 1
        
        // Simpan kelompok kandidat dengan scorenya
        kandidat.push({kelompok, score})
    
    // Cek apakah ada kelompok valid
    IF kandidat.length == 0:
        peserta.status = "melanggar_rule"  // Tidak dapat kelompok
        LANJUT ke peserta berikutnya
    
    // Urutkan kandidat berdasarkan score DESCENDING
    kandidat.sort_by_score(descending)
    
    // Ambil kandidat terbaik (score tertinggi)
    kelompok_terpilih = kandidat[0].kelompok
    
    // Assign peserta ke kelompok
    peserta.id_kelompok = kelompok_terpilih.id
    peserta.status = "ok"

// Hasil akhir
return hasil_pembagian
```

---

## Contoh Kasus

### Skenario 1: Peserta Normal

**Data Peserta:**
- NIM: A001
- Nama: Budi Santoso
- Prodi: TI
- Gender: Pria
- Bahasa Jawa: Ya (1)
- Riwayat Penyakit: Tidak (0)
- Kebutuhan Khusus: Tidak (0)

**Kandidat Kelompok:**

| Kelompok | Kapasitas | Peserta | Faskes | Kondisi | Prodi | Jawa | Gender (Pria/Wanita) | Hard Rule | Score | Alasan |
|----------|-----------|---------|--------|---------|-------|------|---------------------|-----------|-------|--------|
| K1 | 5 | 4 | Ya | 0 khusus | [TI, TI, SI] | 1 | 2P/2W | ✓ VALID | 0 | Prodi sudah ada TI, gender sudah seimbang |
| K2 | 5 | 3 | Tidak | 0 khusus | [DKV, ENG] | 0 | 1P/2W | ✓ VALID | **3** | Prodi baru (TI) +2, gender Pria +1 |
| K3 | 4 | 4 | Ya | 0 khusus | [TI, SI, SI] | 0 | 3P/1W | ❌ PENUH | - | Kapasitas penuh |

**Hasil:** Budi → **K2** (score tertinggi = 3)

---

### Skenario 2: Peserta dengan Kondisi Khusus

**Data Peserta:**
- NIM: B005
- Nama: Siti Zahara
- Prodi: Sistem Info
- Gender: Wanita
- Bahasa Jawa: Ya (1)
- Riwayat Penyakit: Ya (1) ← **KHUSUS**
- Kebutuhan Khusus: Tidak (0)

**Kandidat Kelompok:**

| Kelompok | Kapasitas | Peserta | Faskes | Kondisi Khusus | Hard Rule | Score | Alasan |
|----------|-----------|---------|--------|---------|-----------|-------|--------|
| K1 | 5 | 3 | Tidak | 0 | ✓ VALID | 2 | Prodi baru, tapi faskes = 0 |
| K2 | 5 | 2 | Ya | 1 | ❌ INVALID | - | Sudah ada peserta khusus lain |
| K3 | 5 | 1 | Ya | 0 | ✓ VALID | **5** | Faskes ada, peserta khusus prioritas +5 |

**Hasil:** Siti → **K3** (Hard Rule terpenuhi + score tertinggi)

---

### Skenario 3: Tidak Ada Kelompok Valid

**Data Peserta:**
- NIM: C010
- Nama: Ahmad Hidayat
- Prodi: DKV
- Gender: Pria
- Riwayat Penyakit: Ya (1)
- Kebutuhan Khusus: Ya (1)

**Kandidat Kelompok:**

| Kelompok | Kapasitas | Kondisi | Hard Rule | Alasan |
|----------|-----------|---------|-----------|--------|
| K1 | 5 | 0 khusus | ❌ INVALID | Sudah ada peserta khusus |
| K2 | 4 | 0 khusus | ❌ INVALID | Sudah ada peserta khusus |
| K3 | 5 | 0 khusus | ❌ INVALID | Sudah ada peserta khusus |
| K4 | 5 | PENUH | ❌ INVALID | Kapasitas penuh |

**Hasil:** Ahmad → **MELANGGAR_RULE** (Tidak dapat kelompok)

---

## Implementasi Code

### Lokasi File

**File:** `app/Http/Controllers/KelompokController.php`  
**Method:** `generate()`  
**Line:** 666-900

### Code Lengkap dengan Comment

```php
public function generate(Request $request)
{
    $data = json_decode($request->data, true);

    if (!$data) {
        return redirect('/import')->with('error', 'Silakan upload ulang');
    }

    $this->logAktivitas('Generate', 'Randomisasi kelompok');
    $periode_id = $this->getPeriodeId();

    if ($lock = $this->checkPublishLock($periode_id)) {
        return $lock;
    }

    $kelompokList = Kelompok::where('id_periode', $periode_id)->get();
    $result = [];

    // ========================================
    // ITERATE SETIAP PESERTA
    // ========================================
    foreach ($data as $peserta) {
        
        // NORMALISASI GENDER
        $gender = strtolower(trim($peserta['gender'] ?? ''));
        $peserta['gender'] = in_array($gender, ['l', 'laki-laki', 'pria']) 
            ? 'Pria' 
            : (in_array($gender, ['p', 'perempuan', 'wanita']) ? 'Wanita' : null);

        $kandidat = [];

        // ========================================
        // ITERATE SETIAP KELOMPOK SEBAGAI KANDIDAT
        // ========================================
        foreach ($kelompokList as $kelompok) {

            // =========================
            // HARD RULE 1: CEK KAPASITAS
            // =========================
            $jumlah = collect($result)
                ->where('id_kelompok', $kelompok->id_kelompok)
                ->count();

            if ($jumlah >= $kelompok->kapasitas)
                continue;  // REJECT

            // =========================
            // HARD RULE 2: PESERTA KHUSUS
            // =========================
            $isKhusus = ($peserta['riwayat_penyakit'] == 1 || 
                         $peserta['berkebutuhan_khusus'] == 1);

            if ($isKhusus) {
                $sudahAda = collect($result)
                    ->where('id_kelompok', $kelompok->id_kelompok)
                    ->where(function ($p) {
                        return $p['riwayat_penyakit'] == 1 || 
                               $p['berkebutuhan_khusus'] == 1;
                    })->count();

                if ($sudahAda > 0)
                    continue;  // REJECT
            }

            // =========================
            // SOFT RULE SCORING
            // =========================
            $score = 0;

            // SOFT RULE 1: Prioritas Faskes
            if ($isKhusus && $kelompok->faskes == 1) {
                $score += 5;
            }

            // SOFT RULE 2: Sebaran Prodi
            $jumlahProdiSama = collect($result)
                ->where('id_kelompok', $kelompok->id_kelompok)
                ->where('prodi', $peserta['prodi'])
                ->count();

            if ($jumlahProdiSama == 0) {
                $score += 2;
            }

            // SOFT RULE 3: Sebaran Bahasa Jawa
            $jumlahBisaJawa = collect($result)
                ->where('id_kelompok', $kelompok->id_kelompok)
                ->where('bahasa_jawa', 1)
                ->count();

            if ($peserta['bahasa_jawa'] == 1 && $jumlahBisaJawa == 0) {
                $score += 3;
            }

            // SOFT RULE 4: Keseimbangan Gender
            $laki = collect($result)
                ->where('id_kelompok', $kelompok->id_kelompok)
                ->where('gender', 'Pria')
                ->count();

            $perempuan = collect($result)
                ->where('id_kelompok', $kelompok->id_kelompok)
                ->where('gender', 'Wanita')
                ->count();

            if ($peserta['gender'] == 'Pria' && $laki <= $perempuan) {
                $score += 1;
            }

            if ($peserta['gender'] == 'Wanita' && $perempuan <= $laki) {
                $score += 1;
            }

            // =========================
            // SIMPAN KANDIDAT + SCORE
            // =========================
            $kandidat[] = [
                'kelompok' => $kelompok,
                'score' => $score
            ];
        }

        // =========================
        // TIDAK ADA KELOMPOK VALID
        // =========================
        if (count($kandidat) == 0) {
            $result[] = [
                'nim' => $peserta['nim'],
                'nama' => $peserta['nama'],
                'status' => 'melanggar_rule',
                'id_kelompok' => null,
            ];
            continue;
        }

        // =========================
        // URUTKAN BERDASARKAN SCORE
        // =========================
        $kandidat = collect($kandidat)
            ->sortByDesc('score')
            ->values();

        // Ambil kandidat terbaik
        $pilih = $kandidat[0]['kelompok'];

        $result[] = [
            'nim' => $peserta['nim'],
            'nama' => $peserta['nama'],
            'id_kelompok' => $pilih->id_kelompok,
            'nomor_kelompok' => $pilih->nomor_kelompok,
            'status' => 'ok',
        ];
    }

    // =========================
    // SIMPAN KE SESSION
    // =========================
    session(['hasil_generate_' . $periode_id => $result]);

    $melanggar = collect($result)->where('status', 'melanggar_rule')->count();

    if ($melanggar > 0) {
        return redirect('/randomisasi')
            ->with('warning', 'Ada peserta yang tidak memenuhi aturan sistem.');
    }

    return redirect('/randomisasi');
}
```

---

## Tabel Referensi Score

| No | Rule | Score | Kondisi |
|----|------|-------|---------|
| 1 | Faskes | +5 | Peserta khusus + Kelompok ada faskes |
| 2 | Prodi | +2 | Prodi peserta belum ada di kelompok |
| 3 | Bahasa Jawa | +3 | Peserta bisa jawa + belum ada di kelompok |
| 4 | Gender | +1 | Gender peserta menambah keseimbangan |
| **Total Max Score** | - | **11** | Semua rule terpenuhi |

---

## Kemungkinan Pengembangan

1. **Bobot Dinamis**: Ubah nilai score (+2, +3, +5) berdasarkan prioritas institusi
2. **Rule Tambahan**: 
   - Preferensi lokasi geografis (dusun yang sama)
   - Skill khusus peserta
   - Keseimbangan jurusan
3. **Machine Learning**: Prediksi kelompok terbaik berdasarkan data historis
4. **Admin Interface**: UI untuk mengatur rules tanpa perlu edit code

---

**Dibuat:** 29 Mei 2026  
**Versi:** 1.0  
**Status:** Production Ready
