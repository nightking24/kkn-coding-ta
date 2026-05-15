# METODE PENGUJIAN BLACKBOX TESTING
## Aplikasi Pembagian Kelompok KKN Reguler Berbasis Web

---

## 1. PENGUJIAN SISTEM - ROLE ADMIN LPPM

| No | Use Case | Skenario Pengujian | Data Input | Output yang Diharapkan | Hasil |
|---|---|---|---|---|---|
| 1 | Login Admin | Admin memasukkan username dan password yang benar | Username: admin, Password: admin123 | Sistem menampilkan dashboard admin dan session user tersimpan | ✓ Berhasil / ✗ Gagal |
| 2 | Login Admin | Admin memasukkan username salah | Username: salah, Password: admin123 | Sistem menampilkan pesan error "Login gagal" | ✓ Berhasil / ✗ Gagal |
| 3 | Login Admin | Admin memasukkan password salah | Username: admin, Password: salah123 | Sistem menampilkan pesan error "Login gagal" | ✓ Berhasil / ✗ Gagal |
| 4 | Dashboard Admin | Admin membuka halaman dashboard | - | Sistem menampilkan ringkasan data (jumlah peserta, kelompok, periode, dll) | ✓ Berhasil / ✗ Gagal |
| 5 | Dashboard Admin | Admin melihat statistik dan data real-time | - | Dashboard menampilkan data terkini sesuai periode aktif | ✓ Berhasil / ✗ Gagal |
| 6 | Tambah Periode KKN | Admin menambah periode KKN baru | Nama Periode: KKN Reguler 2026, Tahun: 2026, Status: Aktif | Periode berhasil ditambahkan dan tampil di daftar periode | ✓ Berhasil / ✗ Gagal |
| 7 | Tambah Periode KKN | Admin memasukkan data periode dengan field kosong | Nama Periode: (kosong) | Sistem menampilkan validasi error "Nama periode harus diisi" | ✓ Berhasil / ✗ Gagal |
| 8 | Edit Periode KKN | Admin mengubah data periode yang sudah ada | Nama Periode diubah menjadi "KKN Reguler 2026 (Update)" | Periode berhasil diupdate dan perubahan tersimpan | ✓ Berhasil / ✗ Gagal |
| 9 | Hapus Periode KKN | Admin menghapus periode yang tidak digunakan | Pilih periode lalu klik tombol delete | Periode berhasil dihapus dan hilang dari daftar | ✓ Berhasil / ✗ Gagal |
| 10 | Tambah DPL | Admin menambah data Dosen Pembimbing Lapangan baru | NIK: 123456789, Nama: Dr. Budi, Email: budi@ukdw.ac.id, No Telp: 081234567890 | DPL berhasil ditambahkan dan tampil di daftar DPL | ✓ Berhasil / ✗ Gagal |
| 11 | Tambah DPL | Admin memasukkan nomor telepon dengan format salah | No Telp: 123 (kurang dari 10 digit) | Sistem menampilkan error validasi | ✓ Berhasil / ✗ Gagal |
| 12 | Edit DPL | Admin mengubah data DPL yang sudah ada | Nama: Dr. Budi Santoso, Email: budi.santoso@ukdw.ac.id | DPL berhasil diupdate | ✓ Berhasil / ✗ Gagal |
| 13 | Hapus DPL | Admin menghapus data DPL | Pilih DPL lalu klik delete | DPL berhasil dihapus | ✓ Berhasil / ✗ Gagal |
| 14 | Tambah APL | Admin menambah data Asisten Pendamping Lapangan | NIM: 72210001, Nama: Andi, Email: andi@student.ukdw.ac.id, No Telp: 082345678901 | APL berhasil ditambahkan | ✓ Berhasil / ✗ Gagal |
| 15 | Edit APL | Admin mengubah data APL | Nama: Andi Wijaya | APL berhasil diupdate | ✓ Berhasil / ✗ Gagal |
| 16 | Hapus APL | Admin menghapus data APL | Pilih APL lalu klik delete | APL berhasil dihapus | ✓ Berhasil / ✗ Gagal |
| 17 | Tambah Kelompok | Admin menambah kelompok KKN baru | Nomor K: 1, Desa: Karangasem, Dusun: Karanganyar, Nama Dukuh: Ketua Rumah, Kapasitas: 10, Semester: Gasal, Tahun KKN: 2026 | Kelompok berhasil ditambahkan | ✓ Berhasil / ✗ Gagal |
| 18 | Tambah Kelompok | Admin memasukkan kapasitas dengan value invalid | Kapasitas: -5 | Sistem menampilkan error validasi | ✓ Berhasil / ✗ Gagal |
| 19 | Tambah Kelompok | Admin memasukkan koordinat GPS yang salah | Latitude: 150, Longitude: 200 | Sistem menampilkan error "Latitude harus antara -90 sampai 90, Longitude antara -180 sampai 180" | ✓ Berhasil / ✗ Gagal |
| 20 | Edit Kelompok | Admin mengubah data kelompok | Kapasitas diubah dari 10 menjadi 12 | Kelompok berhasil diupdate | ✓ Berhasil / ✗ Gagal |
| 21 | Hapus Kelompok | Admin menghapus kelompok | Pilih kelompok lalu klik delete | Kelompok berhasil dihapus | ✓ Berhasil / ✗ Gagal |
| 22 | Import Data CSV Peserta | Admin upload file CSV dengan format benar | File: peserta.csv (NIM, Nama, Prodi, Gender, Bahasa Jawa, Riwayat Penyakit, Berkebutuhan Khusus) | Sistem menampilkan preview data peserta dan tombol "Process" aktif | ✓ Berhasil / ✗ Gagal |
| 23 | Import Data CSV Peserta | Admin upload file dengan format tidak sesuai | File: peserta_salah.csv (kolom tidak lengkap) | Sistem menampilkan pesan error tentang format file | ✓ Berhasil / ✗ Gagal |
| 24 | Import Data CSV Peserta | Admin upload file CSV kosong | File: kosong.csv | Sistem menampilkan pesan error "File tidak boleh kosong" | ✓ Berhasil / ✗ Gagal |
| 25 | Preview Data Peserta | Admin melihat preview data peserta sebelum diproses | - | Sistem menampilkan tabel dengan semua data peserta dari CSV | ✓ Berhasil / ✗ Gagal |
| 26 | Preview Data Peserta | Admin mengecek validasi data peserta | - | Sistem menampilkan warning untuk data yang tidak valid | ✓ Berhasil / ✗ Gagal |
| 27 | Generate/Randomisasi Pembagian Kelompok | Admin menjalankan proses randomisasi | Klik tombol "Generate" | Sistem menampilkan hasil pembagian kelompok sesuai rule | ✓ Berhasil / ✗ Gagal |
| 28 | Generate/Randomisasi Pembagian Kelompok | Sistem memeriksa rule pembagian | - | Peserta dengan riwayat penyakit/kebutuhan khusus hanya 1 per kelompok, gender seimbang | ✓ Berhasil / ✗ Gagal |
| 29 | Generate/Randomisasi Pembagian Kelompok | Admin melakukan generate ulang | Klik "Generate" kedua kalinya | Sistem menampilkan hasil pembagian baru (berbeda dari sebelumnya) | ✓ Berhasil / ✗ Gagal |
| 30 | Lihat Hasil Pembagian Kelompok | Admin melihat hasil pembagian kelompok | - | Sistem menampilkan tabel pembagian peserta per kelompok dengan detail (nama, prodi, kelompok) | ✓ Berhasil / ✗ Gagal |
| 31 | Tempatkan Peserta | Admin menempatkan peserta belum terbaagi ke kelompok | Pilih peserta, pilih kelompok tujuan, klik "Tempatkan" | Peserta berhasil ditempatkan ke kelompok pilihan | ✓ Berhasil / ✗ Gagal |
| 32 | Pindah Peserta | Admin memindahkan peserta dari satu kelompok ke kelompok lain | Pilih peserta di K1, pilih K2, klik "Pindah" | Peserta berhasil pindah dari K1 ke K2 | ✓ Berhasil / ✗ Gagal |
| 33 | Tukar Peserta | Admin menukar 2 peserta antar kelompok | Pilih peserta A dari K1 dan peserta B dari K2, klik "Tukar" | Peserta A pindah ke K2 dan peserta B pindah ke K1 | ✓ Berhasil / ✗ Gagal |
| 34 | Reset Pembagian | Admin mereset pembagian (peserta dikosongkan dari kelompok) | Klik tombol "Reset Pembagian" | Semua peserta dikosongkan dari kelompok, status kembali ke "belum terbagi" | ✓ Berhasil / ✗ Gagal |
| 35 | Reset Total | Admin menghapus semua data peserta | Klik tombol "Reset Total" | Semua data peserta dihapus dari sistem | ✓ Berhasil / ✗ Gagal |
| 36 | Export Hasil ke Excel | Admin mengekspor hasil pembagian ke file Excel | Klik tombol "Export Excel" | File Excel berhasil didownload dengan nama "hasil_pembagian_kkn_reguler_2026.xlsx" berisi data semua kelompok dan peserta | ✓ Berhasil / ✗ Gagal |
| 37 | Export Hasil ke PDF | Admin mengekspor hasil pembagian ke file PDF | Klik tombol "Export PDF" | File PDF berhasil didownload dengan nama "hasil_pembagian_kkn_reguler_2026.pdf" dalam format landscape | ✓ Berhasil / ✗ Gagal |
| 38 | Publish Hasil Pembagian | Admin mempublikasikan hasil pembagian | Klik tombol "Publish" setelah semua peserta terbagi | Periode status berubah menjadi "Published", peserta, DPL, APL dapat melihat hasil | ✓ Berhasil / ✗ Gagal |
| 39 | Publish Hasil Pembagian | Admin mencoba publish dengan peserta belum terbagi lengkap | Klik "Publish" saat masih ada peserta tanpa kelompok | Sistem menampilkan error "Masih ada peserta yang belum mendapat kelompok" | ✓ Berhasil / ✗ Gagal |
| 40 | Publish Hasil Pembagian | Admin publish data dengan kelompok melebihi kapasitas | Klik "Publish" saat K1 melebihi kapasitas | Sistem menampilkan warning "Kelompok K1 melebihi kapasitas" namun tetap bisa publish | ✓ Berhasil / ✗ Gagal |
| 41 | Lihat Log Aktivitas | Admin melihat riwayat aktivitas sistem | Klik menu "Log Aktivitas" | Sistem menampilkan tabel log dengan kolom: Username, Aktivitas, Waktu | ✓ Berhasil / ✗ Gagal |
| 42 | Lihat Log Aktivitas | Admin memverifikasi pencatatan aktivitas | - | Setiap aksi (tambah, edit, delete, publish, export) tercatat dalam log | ✓ Berhasil / ✗ Gagal |
| 43 | Cegah Edit Setelah Publish | Admin mencoba edit data setelah publish | Klik edit pada periode yang sudah publish | Sistem menampilkan pesan "Periode sudah dipublish, data tidak bisa diubah!" | ✓ Berhasil / ✗ Gagal |
| 44 | Cegah Publish Ganda | Admin mencoba publish periode yang sudah publish | Klik "Publish" kedua kalinya | Sistem menampilkan error "Data sudah dipublish sebelumnya!" | ✓ Berhasil / ✗ Gagal |
| 45 | Logout Admin | Admin melakukan logout | Klik menu "Logout" | Session user dihapus, redirect ke halaman login | ✓ Berhasil / ✗ Gagal |

---

## 2. PENGUJIAN SISTEM - ROLE PESERTA

| No | Use Case | Skenario Pengujian | Data Input | Output yang Diharapkan | Hasil |
|---|---|---|---|---|---|
| 1 | Login Peserta | Peserta memasukkan NIM dan password benar | NIM: 72210001, Password: password123 | Sistem menampilkan dashboard peserta dengan informasi kelompok | ✓ Berhasil / ✗ Gagal |
| 2 | Login Peserta | Peserta memasukkan NIM salah | NIM: 12345678, Password: password123 | Sistem menampilkan pesan error "Login gagal" | ✓ Berhasil / ✗ Gagal |
| 3 | Login Peserta | Peserta memasukkan password salah | NIM: 72210001, Password: salahh123 | Sistem menampilkan pesan error "Login gagal" | ✓ Berhasil / ✗ Gagal |
| 4 | Lihat Hasil Pembagian Kelompok | Peserta melihat hasil pembagian setelah dipublish | - | Sistem menampilkan informasi kelompok peserta: nomor kelompok, nama kelompok, lokasi (desa, dusun) | ✓ Berhasil / ✗ Gagal |
| 5 | Lihat Informasi Kelompok | Peserta melihat detail anggota kelompok | Buka menu "Detail Kelompok" | Sistem menampilkan tabel anggota kelompok: NIM, Nama, Prodi, Gender | ✓ Berhasil / ✗ Gagal |
| 6 | Lihat Informasi DPL | Peserta melihat informasi Dosen Pembimbing Lapangan | - | Sistem menampilkan nama DPL, email, dan nomor telepon | ✓ Berhasil / ✗ Gagal |
| 7 | Lihat Informasi APL | Peserta melihat informasi Asisten Pendamping Lapangan | - | Sistem menampilkan nama APL, email, dan nomor telepon | ✓ Berhasil / ✗ Gagal |
| 8 | Lihat Lokasi Kelompok | Peserta melihat detail lokasi kelompok | Klik tab "Lokasi" | Sistem menampilkan: desa, dusun, dusun tuan rumah, nama tuan rumah, alamat, nomor telepon | ✓ Berhasil / ✗ Gagal |
| 9 | Lihat Peta Lokasi | Peserta melihat peta lokasi kelompok | Klik "Tampilkan Peta" | Sistem menampilkan peta interaktif dengan marker lokasi kelompok | ✓ Berhasil / ✗ Gagal |
| 10 | Akses Sebelum Publish | Peserta mencoba akses hasil sebelum dipublish | Masuk tanpa ada periode publish | Sistem menampilkan pesan "Data belum tersedia" atau redirect ke halaman tunggu | ✓ Berhasil / ✗ Gagal |
| 11 | Cegah Edit Data Peserta | Peserta mencoba edit data pribadi | - | Peserta hanya dapat melihat data (read-only), tidak ada tombol edit/delete | ✓ Berhasil / ✗ Gagal |
| 12 | Logout Peserta | Peserta melakukan logout | Klik "Logout" | Session peserta dihapus, redirect ke halaman login | ✓ Berhasil / ✗ Gagal |

---

## 3. PENGUJIAN SISTEM - ROLE DOSEN PEMBIMBING LAPANGAN (DPL)

| No | Use Case | Skenario Pengujian | Data Input | Output yang Diharapkan | Hasil |
|---|---|---|---|---|---|
| 1 | Login DPL | DPL memasukkan NIK dan password benar | NIK: 123456789, Password: password123 | Sistem menampilkan dashboard DPL dengan informasi kelompok yang dibimbing | ✓ Berhasil / ✗ Gagal |
| 2 | Login DPL | DPL memasukkan NIK salah | NIK: 999999999, Password: password123 | Sistem menampilkan pesan error "Login gagal" | ✓ Berhasil / ✗ Gagal |
| 3 | Login DPL | DPL memasukkan password salah | NIK: 123456789, Password: salah123 | Sistem menampilkan pesan error "Login gagal" | ✓ Berhasil / ✗ Gagal |
| 4 | Lihat Data Kelompok Dibimbing | DPL melihat kelompok yang menjadi tanggung jawabnya | - | Sistem menampilkan tabel kelompok: nomor kelompok, nama dukuh, lokasi, kapasitas | ✓ Berhasil / ✗ Gagal |
| 5 | Lihat Detail Anggota Kelompok | DPL melihat daftar anggota dalam kelompok yang dibimbing | Klik kelompok tertentu | Sistem menampilkan tabel anggota: NIM, Nama, Prodi, Gender, Asal Prodi | ✓ Berhasil / ✗ Gagal |
| 6 | Lihat Informasi Lokasi | DPL melihat detail lokasi kelompok yang dibimbing | Buka tab "Detail Lokasi" | Sistem menampilkan: desa, dusun, alamat, nama tuan rumah, nomor telepon tuan rumah | ✓ Berhasil / ✗ Gagal |
| 7 | Lihat Peta Lokasi | DPL melihat peta lokasi kelompok | Klik "Tampilkan Peta" | Sistem menampilkan peta dengan marker lokasi kelompok | ✓ Berhasil / ✗ Gagal |
| 8 | Lihat Informasi APL | DPL melihat informasi APL yang mendampingi kelompoknya | - | Sistem menampilkan: nama APL, NIM, email, nomor telepon | ✓ Berhasil / ✗ Gagal |
| 9 | Lihat Kontak Peserta | DPL melihat nomor telepon/email peserta di kelompoknya | - | Sistem menampilkan data kontak peserta dalam kelompok | ✓ Berhasil / ✗ Gagal |
| 10 | Filter Kelompok Berdasarkan Periode | DPL memilih periode untuk melihat kelompok | Pilih periode di dropdown | Sistem menampilkan kelompok yang relevan dengan periode terpilih | ✓ Berhasil / ✗ Gagal |
| 11 | Cegah Edit Data | DPL mencoba mengubah data kelompok | - | DPL hanya dapat melihat data (read-only), tidak ada tombol edit | ✓ Berhasil / ✗ Gagal |
| 12 | Logout DPL | DPL melakukan logout | Klik "Logout" | Session DPL dihapus, redirect ke halaman login | ✓ Berhasil / ✗ Gagal |

---

## 4. PENGUJIAN SISTEM - ROLE ASISTEN PENDAMPING LAPANGAN (APL)

| No | Use Case | Skenario Pengujian | Data Input | Output yang Diharapkan | Hasil |
|---|---|---|---|---|---|
| 1 | Login APL | APL memasukkan NIM dan password benar | NIM: 72210002, Password: password123 | Sistem menampilkan dashboard APL dengan informasi kelompok yang didampingi | ✓ Berhasil / ✗ Gagal |
| 2 | Login APL | APL memasukkan NIM salah | NIM: 12345678, Password: password123 | Sistem menampilkan pesan error "Login gagal" | ✓ Berhasil / ✗ Gagal |
| 3 | Login APL | APL memasukkan password salah | NIM: 72210002, Password: salah123 | Sistem menampilkan pesan error "Login gagal" | ✓ Berhasil / ✗ Gagal |
| 4 | Lihat Data Kelompok Didampingi | APL melihat kelompok yang menjadi tanggung jawabnya | - | Sistem menampilkan tabel kelompok: nomor kelompok, nama dukuh, lokasi, kapasitas | ✓ Berhasil / ✗ Gagal |
| 5 | Lihat Detail Anggota Kelompok | APL melihat daftar anggota dalam kelompok yang didampingi | Klik kelompok tertentu | Sistem menampilkan tabel anggota: NIM, Nama, Prodi, Gender | ✓ Berhasil / ✗ Gagal |
| 6 | Lihat Informasi Lokasi | APL melihat detail lokasi kelompok yang didampingi | Buka tab "Detail Lokasi" | Sistem menampilkan: desa, dusun, alamat, nama tuan rumah, nomor telepon | ✓ Berhasil / ✗ Gagal |
| 7 | Lihat Peta Lokasi | APL melihat peta lokasi kelompok | Klik "Tampilkan Peta" | Sistem menampilkan peta dengan marker lokasi kelompok | ✓ Berhasil / ✗ Gagal |
| 8 | Lihat Informasi DPL | APL melihat informasi DPL pembimbing kelompoknya | - | Sistem menampilkan: nama DPL, NIK, email, nomor telepon | ✓ Berhasil / ✗ Gagal |
| 9 | Lihat Kontak Peserta | APL melihat nomor telepon/email peserta di kelompoknya | - | Sistem menampilkan data kontak peserta dalam kelompok | ✓ Berhasil / ✗ Gagal |
| 10 | Filter Kelompok Berdasarkan Periode | APL memilih periode untuk melihat kelompok | Pilih periode di dropdown | Sistem menampilkan kelompok yang relevan dengan periode terpilih | ✓ Berhasil / ✗ Gagal |
| 11 | Cegah Edit Data | APL mencoba mengubah data kelompok | - | APL hanya dapat melihat data (read-only), tidak ada tombol edit | ✓ Berhasil / ✗ Gagal |
| 12 | Logout APL | APL melakukan logout | Klik "Logout" | Session APL dihapus, redirect ke halaman login | ✓ Berhasil / ✗ Gagal |

---

## 5. KESIMPULAN PENGUJIAN

**Keterangan Hasil:**
- ✓ Berhasil: Sistem berjalan sesuai ekspektasi
- ✗ Gagal: Sistem tidak sesuai atau terdapat error
- ⚠ Perbaikan: Diperlukan perbaikan untuk fitur tertentu

**Total Test Cases:** 77 skenario pengujian
- Admin LPPM: 45 test cases
- Peserta: 12 test cases
- DPL: 12 test cases
- APL: 12 test cases

**Catatan Penting:**
1. Pengujian dilakukan pada setiap role secara terpisah
2. Validasi input data dilakukan pada setiap form
3. Pengujian rule-based system untuk pembagian kelompok
4. Verifikasi lock mechanism setelah publish
5. Pengujian akses control untuk setiap role
6. Pengujian export functionality (Excel & PDF)
7. Pengujian log aktivitas untuk audit trail
