# Sistem Pendukung Keputusan Pemilihan Mentor Program Magang Internal Menggunakan Metode AHP dan SAW Berbasis Web

Repositori ini dikembangkan untuk membangun aplikasi Sistem Pendukung Keputusan (SPK) dalam menentukan mentor terbaik untuk program magang internal dengan menggabungkan dua metode pengambilan keputusan multi-kriteria: **Analytic Hierarchy Process (AHP)** dan **Simple Additive Weighting (SAW)**.

## Anggota Kelompok (Kelompok 5)
* **DEWI ANDRAYANI**
* **HAYKAL AZIZI**
* **HELMI AZIZ**
* **SHEVA AL NASCUTHA**

---

## Metodologi SPK AHP-SAW

1. **AHP (Analytic Hierarchy Process)**:
   * Digunakan untuk menentukan bobot prioritas relatif dari setiap kriteria melalui matriks perbandingan berpasangan menggunakan skala Saaty (1-9).
   * Dilengkapi dengan pengujian konsistensi rasio (Consistency Ratio / CR &le; 0.1) untuk menjamin objektivitas pembobotan.
2. **SAW (Simple Additive Weighting)**:
   * Digunakan untuk proses penormalan matriks kecocokan (R) berdasarkan kriteria benefit dan cost.
   * Melakukan penjumlahan terbobot dari nilai normalisasi dengan bobot AHP untuk menghasilkan nilai preferensi (P) akhir sebagai acuan peringkat alternatif terbaik.

### Studi Kasus: Pemilihan Mentor Magang Internal
* **Alternatif (Calon Mentor & Data Pembanding)**:
  1. **Dewi Andrayani** (A1) — *Peringkat 2*
  2. **Haykal Azizi** (A2) — *Peringkat 3*
  3. **Helmi Aziz** (A3) — *Peringkat 1 (Rekomendasi Utama)* 🏆
  4. **Sheva Al Nascutha** (A4) — *Peringkat 4*
  5. Mahasiswa 5 (A5)
  6. Mahasiswa 6 (A6)
  7. Mahasiswa 7 (A7)
  8. Mahasiswa 8 (A8)
* **Kriteria Penilaian**:
  * **C1: English Proficiency Test (EPT)** (Benefit)
  * **C2: Indeks Prestasi Kumulatif (IPK)** (Benefit)
  * **C3: Jurusan** (Cost)

---

## Panduan Instalasi & Penggunaan

### Prasyarat
* XAMPP (dengan PHP versi 8.x dan MySQL berjalan di port `3307`).

### Langkah-langkah
1. Pindahkan folder proyek `spkAHP-SAW` ke direktori htdocs XAMPP Anda (`C:\xampp\htdocs\spkAHP-SAW`).
2. Aktifkan modul Apache dan MySQL pada XAMPP Control Panel.
3. Import database:
   * Buka phpMyAdmin, buat database baru dengan nama `db_dss`.
   * Import berkas SQL `db/db_dss.sql` ke dalam database `db_dss` tersebut.
4. Akses sistem melalui peramban web di alamat:
   * `http://localhost/spkAHP-SAW/`
5. Login menggunakan akun administrator default:
   * **Username**: `admin`
   * **Password**: `admin`
