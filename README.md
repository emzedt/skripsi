#  Human Resources Information System (HRIS) Berbasis Web 💻

Repositori ini berisi kode sumber untuk aplikasi **Human Resources Information System (HRIS)** berbasis web yang dirancang untuk PT Infico Alumindo Indonesia. Sistem ini dikembangkan untuk mengatasi berbagai kendala yang timbul dari pengelolaan sumber daya manusia (SDM) yang masih manual, seperti keterlambatan rekap absensi, data cuti yang tidak teratur, dan proses penggajian yang memakan waktu.

Proyek ini dikembangkan menggunakan metode **Waterfall** 🌊 dan dibangun dengan framework **Laravel**.

---

## 🤔 Latar Belakang

Pengelolaan SDM manual di PT Infico Alumindo Indonesia menyebabkan beberapa masalah, termasuk pencatatan absensi yang tidak efisien, pengajuan cuti yang tidak terdokumentasi, serta proses penggajian yang rentan kesalahan dan memakan waktu. HRIS ini dirancang untuk mengotomatisasi proses-proses tersebut, meningkatkan efisiensi operasional, dan memastikan akurasi data administrasi karyawan.

---

## ✨ Fitur Utama

HRIS ini dilengkapi dengan berbagai fitur untuk menyederhanakan manajemen SDM:

* **Manajemen Absensi Lanjutan** ⏰
    * **Absensi Reguler**: Menggunakan teknologi pengenalan wajah (📸 *face recognition*) dan pelacakan lokasi (*real-time location* 📍) untuk absensi masuk dan keluar.
    * **Absensi Sales**: Dilengkapi fitur foto *geotagging* yang menampilkan jam dan lokasi secara *real-time* pada foto, memungkinkan pemantauan aktivitas kerja setiap jam.
* **Manajemen Cuti Terintegrasi** 🗓️: Karyawan dapat mengajukan cuti melalui sistem, dan atasan dapat memberikan persetujuan secara cepat dan terdokumentasi.
* **Otomatisasi Penggajian** 💰: Modul penggajian terhubung langsung dengan data absensi (reguler dan sales), gaji bulanan/harian, dan data lembur untuk menghasilkan perhitungan gaji yang akurat dan efisien.
* **People Development** 📈: Memungkinkan pencatatan dan pelacakan perkembangan karyawan secara berkala, termasuk evaluasi kinerja dan mutasi jabatan (rotasi, demosi, dan promosi).

---

## 👥 Peran Pengguna (Aktor)

Sistem ini memiliki lima hak akses yang berbeda untuk setiap peran:

1.  👑 **Admin**: Memiliki akses penuh ke semua fitur dan modul dalam sistem.
2.  👔 **Director**: Dapat menyetujui cuti, melihat laporan absensi, melihat rapor kinerja, dan mengelola *people development*.
3.  👨‍💼 **Manager**: Dapat melakukan absensi, melihat dan mencetak laporan absensi, mengajukan dan menyetujui cuti, serta melihat rapor kinerja.
4.  💳 **Finance**: Memiliki hak utama untuk mengelola penggajian dan mewarisi hak akses dari Manajer.
5.  🧑‍💻 **Karyawan**: Dapat melakukan absensi, melihat laporan absensi, mengajukan cuti, serta melihat rapor kinerja dan detail penggajian mereka.

---

## 🛠️ Teknologi yang Digunakan

* **Framework**: Laravel
* **Bahasa Pemrograman**: PHP
* **Desain Tampilan**: Tailwind CSS
* **Database**: MySQL
* **Lingkungan Pengembangan Lokal**: Laragon

---

## 🔄 Metode Pengembangan

Pengembangan sistem ini menggunakan **metode Waterfall**, yang terdiri dari lima tahapan berurutan:

1.  **Requirement Analysis**: Analisis kebutuhan sistem melalui observasi, wawancara, dan studi literatur.
2.  **System Design**: Perancangan sistem menggunakan *Use Case Diagram* dan *Entity Relationship Diagram (ERD)*.
3.  **Implementation**: Proses *coding* mengubah desain menjadi aplikasi fungsional menggunakan Laravel.
4.  **Testing**: Pengujian sistem menggunakan *Alpha Testing* untuk memastikan fungsionalitas berjalan sesuai harapan.
5.  **Maintenance**: Pemeliharaan sistem untuk perbaikan dan menjaga kinerja tetap optimal setelah implementasi.

---

## ✅ Hasil Pengujian

Sistem telah melalui tahap pengujian internal (*Alpha Testing*) yang dilakukan oleh tim pengembang.

* **Total Skenario Uji**: 97 skenario.
* **Hasil**: **100% berhasil** tanpa ada kegagalan.
* **Kesimpulan**: Semua modul yang dikembangkan telah memenuhi persyaratan fungsional yang ditetapkan dan siap untuk mendukung operasional harian perusahaan.

---

## 📖 Artikel Ilmiah

Proyek ini juga dibuat dalam bentuk tulisan ilmiah yang dapat diakses menggunakan tautan berikut: [Artikel](https://publikasi.teknokrat.ac.id/index.php/teknoinfo/article/view/368/90)
