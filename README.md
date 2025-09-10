# Human Resources Information System (HRIS) Berbasis Web

Repositori ini berisi kode sumber untuk aplikasi **Human Resources Information System (HRIS)** berbasis web yang dirancang untuk PT Infico Alumindo Indonesia. [cite_start]Sistem ini dikembangkan untuk mengatasi berbagai kendala yang timbul dari pengelolaan sumber daya manusia (SDM) yang masih manual, seperti keterlambatan rekap absensi, data cuti yang tidak teratur, dan proses penggajian yang memakan waktu[cite: 11, 24].

[cite_start]Proyek ini dikembangkan menggunakan **metode Waterfall** [cite: 13, 26] [cite_start]dan dibangun dengan **framework Laravel**[cite: 12, 25].

---

## Latar Belakang

[cite_start]Pengelolaan SDM manual di PT Infico Alumindo Indonesia menyebabkan beberapa masalah, termasuk pencatatan absensi yang tidak efisien, pengajuan cuti yang tidak terdokumentasi, serta proses penggajian yang rentan kesalahan dan memakan waktu[cite: 48]. [cite_start]HRIS ini dirancang untuk mengotomatisasi proses-proses tersebut, meningkatkan efisiensi operasional, dan memastikan akurasi data administrasi karyawan[cite: 68, 69].

---

## Fitur Utama

HRIS ini dilengkapi dengan berbagai fitur untuk menyederhanakan manajemen SDM:

* **Manajemen Absensi Lanjutan**
    * [cite_start]**Absensi Reguler**: Menggunakan teknologi pengenalan wajah (*face recognition*) dan pelacakan lokasi (*real-time location*) untuk absensi masuk dan keluar[cite: 15, 28].
    * [cite_start]**Absensi Sales**: Dilengkapi fitur foto *geotagging* yang menampilkan jam dan lokasi secara *real-time* pada foto, memungkinkan pemantauan aktivitas kerja setiap jam[cite: 16, 29].
* [cite_start]**Manajemen Cuti Terintegrasi**: Karyawan dapat mengajukan cuti melalui sistem, dan atasan dapat memberikan persetujuan secara cepat dan terdokumentasi[cite: 17, 30].
* [cite_start]**Otomatisasi Penggajian**: Modul penggajian terhubung langsung dengan data absensi (reguler dan sales), gaji bulanan/harian, dan data lembur untuk menghasilkan perhitungan gaji yang akurat dan efisien[cite: 18, 31].
* [cite_start]**People Development**: Memungkinkan pencatatan dan pelacakan perkembangan karyawan secara berkala, termasuk evaluasi kinerja dan mutasi jabatan (rotasi, demosi, dan promosi)[cite: 19, 32].

---

## Peran Pengguna (Aktor)

[cite_start]Sistem ini memiliki lima hak akses yang berbeda untuk setiap peran[cite: 106]:

1.  [cite_start]**Admin**: Memiliki akses penuh ke semua fitur dan modul dalam sistem[cite: 112].
2.  [cite_start]**Director**: Dapat menyetujui cuti, melihat laporan absensi, melihat rapor kinerja, dan mengelola *people development*[cite: 113].
3.  [cite_start]**Manager**: Dapat melakukan absensi, melihat dan mencetak laporan absensi, mengajukan dan menyetujui cuti, serta melihat rapor kinerja[cite: 115].
4.  [cite_start]**Finance**: Memiliki hak utama untuk mengelola penggajian dan mewarisi hak akses dari Manajer[cite: 116].
5.  [cite_start]**Karyawan**: Dapat melakukan absensi, melihat laporan absensi, mengajukan cuti, serta melihat rapor kinerja dan detail penggajian mereka[cite: 117].

---

## Teknologi yang Digunakan

* [cite_start]**Framework**: Laravel [cite: 100]
* [cite_start]**Bahasa Pemrograman**: PHP [cite: 61]
* [cite_start]**Desain Tampilan**: Tailwind CSS [cite: 100]
* [cite_start]**Database**: MySQL [cite: 100]
* [cite_start]**Lingkungan Pengembangan Lokal**: Laragon [cite: 100]

---

## Metode Pengembangan

[cite_start]Pengembangan sistem ini menggunakan **metode Waterfall**, yang terdiri dari lima tahapan berurutan[cite: 13, 85]:

1.  [cite_start]**Requirement Analysis**: Analisis kebutuhan sistem melalui observasi, wawancara, dan studi literatur[cite: 94].
2.  [cite_start]**System Design**: Perancangan sistem menggunakan *Use Case Diagram* dan *Entity Relationship Diagram (ERD)*[cite: 97, 98].
3.  [cite_start]**Implementation**: Proses *coding* mengubah desain menjadi aplikasi fungsional menggunakan Laravel[cite: 99].
4.  [cite_start]**Testing**: Pengujian sistem menggunakan *Alpha Testing* untuk memastikan fungsionalitas berjalan sesuai harapan[cite: 101, 102].
5.  [cite_start]**Maintenance**: Pemeliharaan sistem untuk perbaikan dan menjaga kinerja tetap optimal setelah implementasi[cite: 103].

---

## Hasil Pengujian

[cite_start]Sistem telah melalui tahap pengujian internal (*Alpha Testing*) yang dilakukan oleh tim pengembang[cite: 202, 203].

* [cite_start]**Total Skenario Uji**: 97 skenario[cite: 219].
* [cite_start]**Hasil**: **100% berhasil** tanpa ada kegagalan[cite: 33, 220].
* [cite_start]**Kesimpulan**: Semua modul yang dikembangkan telah memenuhi persyaratan fungsional yang ditetapkan dan siap untuk mendukung operasional harian perusahaan[cite: 242, 243].

---

## Artikel Ilmiah
Project ini juga dibuat dalam bentuk tulisan ilmiah yang dapat diakses menggunakan link: [Artikel](https://publikasi.teknokrat.ac.id/index.php/teknoinfo/article/view/368/90)
