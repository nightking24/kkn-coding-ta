# 🔄 ALUR SISTEM SKORING: CONTOH STEP-BY-STEP RANDOMISASI

## 📌 Contoh Data Input

### Peserta (5 orang)
```
P1: Budi (TI, Pria, Bahasa Jawa ✓, Sehat, Normal)
P2: Siti (SI, Wanita, Bahasa Jawa ✗, Penyakit, Normal)
P3: Ahmad (DKV, Pria, Bahasa Jawa ✓, Sehat, Normal)
P4: Rina (TI, Wanita, Bahasa Jawa ✗, Sehat, Normal)
P5: Citra (ENG, Wanita, Bahasa Jawa ✓, Sehat, Khusus)
```

### Kelompok (3 kelompok)
```
K1: Kapasitas 2, Faskes ✗, Sudah ada: -
K2: Kapasitas 2, Faskes ✓, Sudah ada: -
K3: Kapasitas 2, Faskes ✓, Sudah ada: -
```

---

## 🚀 PROSES RANDOMISASI: PESERTA SATU PER SATU

### ▶️ PESERTA 1: BUDI (TI, Pria, Bahasa Jawa ✓, Sehat)

```
╔════════════════════════════════════════════════════════════════════════╗
║ PESERTA: Budi (TI | Pria | Bahasa Jawa ✓ | Sehat | Normal)            ║
╚════════════════════════════════════════════════════════════════════════╝
```

#### Step 1: Iterasi Setiap Kelompok

**🔍 Kelompok K1:**
```
┌─ HARD RULE 1: Kapasitas?
│  Peserta di K1: 0
│  Kapasitas: 2
│  Status: 0 < 2 ✅ LOLOS
│
├─ HARD RULE 2: Peserta Khusus?
│  Budi khusus? ❌ Tidak
│  Status: ✅ LOLOS (tidak perlu cek)
│
├─ KESIMPULAN: ✅ K1 VALID (masuk kandidat)
│
└─ SOFT RULE SCORING:
   └─ Faskes: Budi khusus? ❌ → Score = 0
   └─ Bahasa Jawa: Budi bisa Jawa ✓ + belum ada Jawa di K1 ✓ → Score += 3
   └─ Prodi: TI belum ada di K1 ✓ → Score += 2
   └─ Gender: Pria, K1 sudah 0 Pria (0 < 0?) ❌ → Score = 0
   
   TOTAL SCORE K1 = 0 + 3 + 2 + 0 = 5️⃣
```

**🔍 Kelompok K2:**
```
┌─ HARD RULE 1: Kapasitas?
│  Peserta di K2: 0
│  Kapasitas: 2
│  Status: 0 < 2 ✅ LOLOS
│
├─ HARD RULE 2: Peserta Khusus?
│  Budi khusus? ❌ Tidak
│  Status: ✅ LOLOS
│
├─ KESIMPULAN: ✅ K2 VALID
│
└─ SOFT RULE SCORING:
   └─ Faskes: Budi khusus? ❌ → Score = 0
   └─ Bahasa Jawa: Budi bisa ✓ + belum ada ✓ → Score += 3
   └─ Prodi: TI belum ada ✓ → Score += 2
   └─ Gender: Pria, K2 sudah 0 Pria (0 ≤ 0) → Score += 1
   
   TOTAL SCORE K2 = 0 + 3 + 2 + 1 = 6️⃣
```

**🔍 Kelompok K3:**
```
┌─ HARD RULE 1: Kapasitas?
│  Peserta di K3: 0
│  Kapasitas: 2
│  Status: 0 < 2 ✅ LOLOS
│
├─ HARD RULE 2: Peserta Khusus?
│  Budi khusus? ❌ Tidak
│  Status: ✅ LOLOS
│
├─ KESIMPULAN: ✅ K3 VALID
│
└─ SOFT RULE SCORING:
   └─ Faskes: Budi khusus? ❌ → Score = 0
   └─ Bahasa Jawa: Budi bisa ✓ + belum ada ✓ → Score += 3
   └─ Prodi: TI belum ada ✓ → Score += 2
   └─ Gender: Pria, K3 sudah 0 Pria (0 ≤ 0) → Score += 1
   
   TOTAL SCORE K3 = 0 + 3 + 2 + 1 = 6️⃣
```

#### Step 2: Bandingkan Score

```
KANDIDAT RANKING:
1️⃣ K2 = 6 poin ← DIPILIH (score tertinggi)
1️⃣ K3 = 6 poin ← sama, ambil yang pertama di list
3️⃣ K1 = 5 poin
```

#### Step 3: Assign Peserta

```
✅ BUDI → K2
   Status: OK ✓
```

#### State Kelompok Setelah P1:
```
K1: Peserta = 0/2, [          ], Jawa: 0, Prodi: []
K2: Peserta = 1/2, [Budi (TI) ], Jawa: 1, Prodi: [TI]  ← Budi ada di sini
K3: Peserta = 0/2, [          ], Jawa: 0, Prodi: []
```

---

### ▶️ PESERTA 2: SITI (SI, Wanita, Bahasa Jawa ✗, Penyakit 🚨)

```
╔════════════════════════════════════════════════════════════════════════╗
║ PESERTA: Siti (SI | Wanita | Bahasa Jawa ✗ | Penyakit 🚨 | Normal)    ║
║ STATUS: KHUSUS (riwayat penyakit = 1)                                  ║
╚════════════════════════════════════════════════════════════════════════╝
```

#### Step 1: Iterasi Setiap Kelompok

**🔍 Kelompok K1:**
```
┌─ HARD RULE 1: Kapasitas?
│  Peserta di K1: 0
│  Kapasitas: 2
│  Status: 0 < 2 ✅ LOLOS
│
├─ HARD RULE 2: Peserta Khusus?
│  Siti khusus? ✅ YA (penyakit)
│  Ada peserta khusus lain di K1? ❌ Tidak
│  Status: ✅ LOLOS
│
├─ KESIMPULAN: ✅ K1 VALID
│
└─ SOFT RULE SCORING:
   └─ Faskes: Siti khusus ✓ + K1 faskes? ❌ → Score = 0
   └─ Bahasa Jawa: Siti bisa? ❌ → Score = 0
   └─ Prodi: SI belum ada di K1 ✓ → Score += 2
   └─ Gender: Wanita, K1 sudah 0 Wanita (0 ≤ 0) → Score += 1
   
   TOTAL SCORE K1 = 0 + 0 + 2 + 1 = 3️⃣
```

**🔍 Kelompok K2:**
```
┌─ HARD RULE 1: Kapasitas?
│  Peserta di K2: 1 (Budi)
│  Kapasitas: 2
│  Status: 1 < 2 ✅ LOLOS
│
├─ HARD RULE 2: Peserta Khusus?
│  Siti khusus? ✅ YA (penyakit)
│  Ada peserta khusus lain di K2? 
│    → Cek: Budi penyakit? ❌ Kebutuhan khusus? ❌
│    → Tidak ada peserta khusus di K2 ✓
│  Status: ✅ LOLOS
│
├─ KESIMPULAN: ✅ K2 VALID
│
└─ SOFT RULE SCORING:
   └─ Faskes: Siti khusus ✓ + K2 faskes? ✅ → Score += 5 ⭐
   └─ Bahasa Jawa: Siti bisa? ❌ → Score = 0
   └─ Prodi: SI belum ada di K2 ✓ → Score += 2
   └─ Gender: Wanita, K2 pria: 1 (Budi), wanita: 0 (0 ≤ 1) → Score += 1
   
   TOTAL SCORE K2 = 5 + 0 + 2 + 1 = 8️⃣
```

**🔍 Kelompok K3:**
```
┌─ HARD RULE 1: Kapasitas?
│  Peserta di K3: 0
│  Kapasitas: 2
│  Status: 0 < 2 ✅ LOLOS
│
├─ HARD RULE 2: Peserta Khusus?
│  Siti khusus? ✅ YA
│  Ada peserta khusus lain di K3? ❌ Tidak
│  Status: ✅ LOLOS
│
├─ KESIMPULAN: ✅ K3 VALID
│
└─ SOFT RULE SCORING:
   └─ Faskes: Siti khusus ✓ + K3 faskes? ✅ → Score += 5 ⭐
   └─ Bahasa Jawa: Siti bisa? ❌ → Score = 0
   └─ Prodi: SI belum ada di K3 ✓ → Score += 2
   └─ Gender: Wanita, K3 pria: 0, wanita: 0 (0 ≤ 0) → Score += 1
   
   TOTAL SCORE K3 = 5 + 0 + 2 + 1 = 8️⃣
```

#### Step 2: Bandingkan Score

```
KANDIDAT RANKING:
1️⃣ K2 = 8 poin ← DIPILIH (score tertinggi, posisi pertama)
1️⃣ K3 = 8 poin ← sama
3️⃣ K1 = 3 poin
```

#### Step 3: Assign Peserta

```
✅ SITI → K2
   Status: OK ✓
   (Diprioritaskan ke K2 karena ada faskes untuk peserta khusus)
```

#### State Kelompok Setelah P2:
```
K1: Peserta = 0/2, [          ], Jawa: 0, Prodi: []
K2: Peserta = 2/2, [Budi, Siti], Jawa: 1, Prodi: [TI, SI]  ← PENUH ❌
K3: Peserta = 0/2, [          ], Jawa: 0, Prodi: []
```

---

### ▶️ PESERTA 3: AHMAD (DKV, Pria, Bahasa Jawa ✓, Sehat)

```
╔════════════════════════════════════════════════════════════════════════╗
║ PESERTA: Ahmad (DKV | Pria | Bahasa Jawa ✓ | Sehat | Normal)          ║
╚════════════════════════════════════════════════════════════════════════╝
```

#### Step 1: Iterasi Setiap Kelompok

**🔍 Kelompok K1:**
```
┌─ HARD RULE 1: Kapasitas?
│  Peserta di K1: 0
│  Kapasitas: 2
│  Status: 0 < 2 ✅ LOLOS
│
├─ HARD RULE 2: Peserta Khusus?
│  Ahmad khusus? ❌ Tidak
│  Status: ✅ LOLOS
│
└─ SOFT RULE SCORING:
   └─ Faskes: Ahmad khusus? ❌ → Score = 0
   └─ Bahasa Jawa: Ahmad bisa ✓ + belum ada ✓ → Score += 3
   └─ Prodi: DKV belum ada ✓ → Score += 2
   └─ Gender: Pria, K1 pria: 0 (0 ≤ 0) → Score += 1
   
   TOTAL SCORE K1 = 0 + 3 + 2 + 1 = 6️⃣
```

**🔍 Kelompok K2:**
```
┌─ HARD RULE 1: Kapasitas?
│  Peserta di K2: 2 (Budi, Siti)
│  Kapasitas: 2
│  Status: 2 < 2? ❌ TIDAK LOLOS → PENUH
│
├─ KESIMPULAN: ❌ K2 INVALID (DITOLAK)
│
└─ TIDAK MASUK KANDIDAT ✗
```

**🔍 Kelompok K3:**
```
┌─ HARD RULE 1: Kapasitas?
│  Peserta di K3: 0
│  Kapasitas: 2
│  Status: 0 < 2 ✅ LOLOS
│
├─ HARD RULE 2: Peserta Khusus?
│  Ahmad khusus? ❌ Tidak
│  Status: ✅ LOLOS
│
└─ SOFT RULE SCORING:
   └─ Faskes: Ahmad khusus? ❌ → Score = 0
   └─ Bahasa Jawa: Ahmad bisa ✓ + belum ada ✓ → Score += 3
   └─ Prodi: DKV belum ada ✓ → Score += 2
   └─ Gender: Pria, K3 pria: 0 (0 ≤ 0) → Score += 1
   
   TOTAL SCORE K3 = 0 + 3 + 2 + 1 = 6️⃣
```

#### Step 2: Bandingkan Score

```
KANDIDAT RANKING:
1️⃣ K1 = 6 poin ← DIPILIH (ada di urutan pertama)
1️⃣ K3 = 6 poin ← sama
X  K2 = INVALID (tidak masuk kandidat)
```

#### Step 3: Assign Peserta

```
✅ AHMAD → K1
   Status: OK ✓
```

#### State Kelompok Setelah P3:
```
K1: Peserta = 1/2, [Ahmad (DKV)], Jawa: 1, Prodi: [DKV]
K2: Peserta = 2/2, [Budi, Siti  ], Jawa: 1, Prodi: [TI, SI]  ← PENUH
K3: Peserta = 0/2, [             ], Jawa: 0, Prodi: []
```

---

### ▶️ PESERTA 4: RINA (TI, Wanita, Bahasa Jawa ✗, Sehat)

```
╔════════════════════════════════════════════════════════════════════════╗
║ PESERTA: Rina (TI | Wanita | Bahasa Jawa ✗ | Sehat | Normal)          ║
╚════════════════════════════════════════════════════════════════════════╝
```

#### Step 1: Iterasi Setiap Kelompok

**🔍 Kelompok K1:**
```
┌─ HARD RULE 1: Kapasitas?
│  Peserta di K1: 1 (Ahmad)
│  Kapasitas: 2
│  Status: 1 < 2 ✅ LOLOS
│
├─ HARD RULE 2: Peserta Khusus?
│  Rina khusus? ❌ Tidak
│  Status: ✅ LOLOS
│
└─ SOFT RULE SCORING:
   └─ Faskes: Rina khusus? ❌ → Score = 0
   └─ Bahasa Jawa: Rina bisa? ❌ → Score = 0
   └─ Prodi: TI belum ada di K1 ✓ → Score += 2
   └─ Gender: Wanita, K1 pria: 1, wanita: 0 (0 ≤ 1) → Score += 1
   
   TOTAL SCORE K1 = 0 + 0 + 2 + 1 = 3️⃣
```

**🔍 Kelompok K2:**
```
┌─ HARD RULE 1: Kapasitas?
│  Peserta di K2: 2 (Budi, Siti)
│  Kapasitas: 2
│  Status: 2 < 2? ❌ PENUH → INVALID
│
└─ ❌ K2 INVALID (DITOLAK)
```

**🔍 Kelompok K3:**
```
┌─ HARD RULE 1: Kapasitas?
│  Peserta di K3: 0
│  Kapasitas: 2
│  Status: 0 < 2 ✅ LOLOS
│
├─ HARD RULE 2: Peserta Khusus?
│  Rina khusus? ❌ Tidak
│  Status: ✅ LOLOS
│
└─ SOFT RULE SCORING:
   └─ Faskes: Rina khusus? ❌ → Score = 0
   └─ Bahasa Jawa: Rina bisa? ❌ → Score = 0
   └─ Prodi: TI belum ada di K3 ✓ → Score += 2
   └─ Gender: Wanita, K3 pria: 0, wanita: 0 (0 ≤ 0) → Score += 1
   
   TOTAL SCORE K3 = 0 + 0 + 2 + 1 = 3️⃣
```

#### Step 2: Bandingkan Score

```
KANDIDAT RANKING:
1️⃣ K1 = 3 poin ← DIPILIH (urutan pertama)
1️⃣ K3 = 3 poin ← sama
X  K2 = INVALID
```

#### Step 3: Assign Peserta

```
✅ RINA → K1
   Status: OK ✓
```

#### State Kelompok Setelah P4:
```
K1: Peserta = 2/2, [Ahmad, Rina ], Jawa: 1, Prodi: [DKV, TI]  ← PENUH
K2: Peserta = 2/2, [Budi, Siti  ], Jawa: 1, Prodi: [TI, SI]   ← PENUH
K3: Peserta = 0/2, [             ], Jawa: 0, Prodi: []
```

---

### ▶️ PESERTA 5: CITRA (ENG, Wanita, Bahasa Jawa ✓, Kebutuhan Khusus 🚨)

```
╔════════════════════════════════════════════════════════════════════════╗
║ PESERTA: Citra (ENG | Wanita | Bahasa Jawa ✓ | Sehat | Khusus 🚨)     ║
║ STATUS: KHUSUS (berkebutuhan_khusus = 1)                               ║
╚════════════════════════════════════════════════════════════════════════╝
```

#### Step 1: Iterasi Setiap Kelompok

**🔍 Kelompok K1:**
```
┌─ HARD RULE 1: Kapasitas?
│  Peserta di K1: 2 (Ahmad, Rina)
│  Kapasitas: 2
│  Status: 2 < 2? ❌ PENUH → INVALID
│
└─ ❌ K1 INVALID
```

**🔍 Kelompok K2:**
```
┌─ HARD RULE 1: Kapasitas?
│  Peserta di K2: 2 (Budi, Siti)
│  Kapasitas: 2
│  Status: 2 < 2? ❌ PENUH → INVALID
│
└─ ❌ K2 INVALID
```

**🔍 Kelompok K3:**
```
┌─ HARD RULE 1: Kapasitas?
│  Peserta di K3: 0
│  Kapasitas: 2
│  Status: 0 < 2 ✅ LOLOS
│
├─ HARD RULE 2: Peserta Khusus?
│  Citra khusus? ✅ YA (kebutuhan khusus)
│  Ada peserta khusus lain di K3? ❌ Tidak
│  Status: ✅ LOLOS
│
├─ KESIMPULAN: ✅ K3 VALID
│
└─ SOFT RULE SCORING:
   └─ Faskes: Citra khusus ✓ + K3 faskes? ✅ → Score += 5 ⭐
   └─ Bahasa Jawa: Citra bisa ✓ + belum ada ✓ → Score += 3
   └─ Prodi: ENG belum ada ✓ → Score += 2
   └─ Gender: Wanita, K3 pria: 0, wanita: 0 (0 ≤ 0) → Score += 1
   
   TOTAL SCORE K3 = 5 + 3 + 2 + 1 = 11️⃣ (SCORE MAKSIMAL)
```

#### Step 2: Bandingkan Score

```
KANDIDAT RANKING:
1️⃣ K3 = 11 poin ← DIPILIH (SCORE MAKSIMAL!)
X  K1 = INVALID (PENUH)
X  K2 = INVALID (PENUH)
```

#### Step 3: Assign Peserta

```
✅ CITRA → K3
   Status: OK ✓
   (Mendapat score maksimal karena faskes ada untuk peserta khusus,
    bisa bahasa jawa, prodi baru, gender seimbang)
```

#### State Kelompok Setelah P5:
```
K1: Peserta = 2/2, [Ahmad, Rina ], Jawa: 1, Prodi: [DKV, TI]
K2: Peserta = 2/2, [Budi, Siti  ], Jawa: 1, Prodi: [TI, SI]
K3: Peserta = 1/2, [Citra (ENG) ], Jawa: 1, Prodi: [ENG]
```

---

## 📊 HASIL AKHIR RANDOMISASI

```
KELOMPOK K1:
├─ Ahmad (DKV, Pria, Bahasa Jawa ✓)
└─ Rina (TI, Wanita, Bahasa Jawa ✗)
   Status: ✅ OK

KELOMPOK K2:
├─ Budi (TI, Pria, Bahasa Jawa ✓)
└─ Siti (SI, Wanita, Bahasa Jawa ✗, Penyakit 🏥)
   Status: ✅ OK

KELOMPOK K3:
└─ Citra (ENG, Wanita, Bahasa Jawa ✓, Kebutuhan Khusus ♿)
   Status: ✅ OK
   (Masih ada slot 1 kosong)
```

---

## 🎯 KEY INSIGHTS

### 1. Hard Rule Langsung Eliminasi
- **K2 ditolak untuk Ahmad** karena kapasitas penuh → tidak masuk kandidat
- Meskipun K2 bisa dapat score tinggi, tetap REJECT ❌

### 2. Prioritas Peserta Khusus
- **Siti** (penyakit) → dipilih K2 karena ada faskes (+5 poin)
- **Citra** (kebutuhan khusus) → dipilih K3, dapat score maksimal (11 poin)

### 3. Hard Rule 2 Bekerja
- Peserta khusus maksimal 1 per kelompok
- K1 dan K2 tidak bisa terima peserta khusus lagi setelah menerima Siti di K2

### 4. Urutan Iterasi Penting
- Peserta yang datang duluan memiliki lebih banyak pilihan
- Peserta terakhir terbatas (dalam kasus ini Citra cuma punya K3)

### 5. Scoring Membantu Optimasi
- Ketika ada multiple candidates valid, score memilih yang terbaik
- Budi dipilih K2 (score 6) daripada K1 (score 5)

---

## 🔢 Tabel Scoring Ringkasan

| Peserta | Kelompok | Hard Rule | Score | Status |
|---------|----------|-----------|-------|--------|
| Budi | K1 | ✅ | 5 | Kandidat |
| Budi | K2 | ✅ | 6 | **DIPILIH** ⭐ |
| Budi | K3 | ✅ | 6 | Kandidat |
| Siti | K1 | ✅ | 3 | Kandidat |
| Siti | K2 | ✅ | 8 | **DIPILIH** ⭐ |
| Siti | K3 | ✅ | 8 | Kandidat |
| Ahmad | K1 | ✅ | 6 | **DIPILIH** ⭐ |
| Ahmad | K2 | ❌ | - | INVALID (PENUH) |
| Ahmad | K3 | ✅ | 6 | Kandidat |
| Rina | K1 | ✅ | 3 | **DIPILIH** ⭐ |
| Rina | K2 | ❌ | - | INVALID (PENUH) |
| Rina | K3 | ✅ | 3 | Kandidat |
| Citra | K1 | ❌ | - | INVALID (PENUH) |
| Citra | K2 | ❌ | - | INVALID (PENUH) |
| Citra | K3 | ✅ | 11 | **DIPILIH** ⭐ (MAX) |

---

## 💡 Kesimpulan

**Alur Sistem Skoring:**
1. **Hard Rule** = Filter VALID/INVALID (mesti lolos semua)
2. **Soft Rule** = Scoring untuk pilihan terbaik (ambil score tertinggi)
3. **Iterasi** = Setiap peserta diproses satu per satu, kelompok menyusut kapasitasnya
4. **Adaptif** = Score berubah seiring peserta baru masuk ke kelompok

Sistemnya cerdas karena:
- ✅ Prioritas peserta khusus dengan faskes
- ✅ Optimasi keberagaman prodi
- ✅ Pastikan ada bahasa Jawa di setiap kelompok
- ✅ Keseimbangan gender yang baik
