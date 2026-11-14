# Audit foto produk O’Clock — Tahap 4.2

Audit 13 November 2026 berdasarkan PRD v2.1 dan pembacaan langsung MySQL.

**29 produk aktif, 20 ILLUSTRATION, 9 MISMATCH, 0 VERIFIED, 0 MISSING/UNVERIFIED untuk aset yang sedang dipakai.** Semua produk membutuhkan foto yang memenuhi syarat. Status model dan status aset dinilai secara terpisah. Nama dan spesifikasi dari MySQL adalah klaim katalog yang diperiksa. Material case tidak memiliki kolom tersendiri pada produk dan perlu dikonfirmasi dari dokumen model atau unit fisik bila belum tercantum.

## Matriks 29 produk

| SKU | Brand | Model | File Sekarang | Jenis Gambar | Kesesuaian Foto | Sumber/Izin | Rekomendasi |
| --- | --- | --- | --- | --- | --- | --- | --- |
| AC-AUT-9601MAR | Alexandre Christie | AC 9601 MAR | `products/ac-aut-9601mar.svg` | Ilustrasi SVG | **ILLUSTRATION**. Kode dasar tercantum. Varian rose gold/dial hitam dan strap belum terverifikasi. | [Referensi model](https://alexandrechristie.com/collections/men-collection). Izin aset katalog belum dikonfirmasi. | Memerlukan foto nyata sesuai varian. Konfirmasi model dahulu. |
| AC-CLS-6647MCB | Alexandre Christie | AC 6647 MCB | `products/ac-cls-6647mcb.svg` | Ilustrasi SVG | **ILLUSTRATION**. Kode dasar tercantum. Varian gunmetal dan bracelet mesh belum terverifikasi. | [Referensi model](https://alexandrechristie.com/collections/men-collection). Izin aset katalog belum dikonfirmasi. | Memerlukan foto nyata sesuai varian. Konfirmasi model dahulu. |
| AC-CLS-8645MD | Alexandre Christie | AC 8645 MD | `products/ac-cls-8645md.svg` | Ilustrasi SVG | **ILLUSTRATION**. MODEL BELUM TERVERIFIKASI. Minta referensi resmi untuk dial putih dan strap kulit cokelat. | Izin aset katalog belum dikonfirmasi. | Memerlukan foto nyata sesuai varian. Konfirmasi model dahulu. |
| AC-SPT-6295MPR | Alexandre Christie | AC 6295 MPR | `products/ac-spt-6295mpr.svg` | Ilustrasi SVG | **ILLUSTRATION**. MODEL BELUM TERVERIFIKASI untuk konfigurasi chronograph rose gold/hitam. Minta nomor varian dan spesifikasi resmi. | Izin aset katalog belum dikonfirmasi. | Memerlukan foto nyata sesuai varian. Konfirmasi model dahulu. |
| AC-WMN-2A40LDB | Alexandre Christie | AC 2A40 LDB | `products/ac-wmn-2a40ldb.svg` | Ilustrasi SVG | **ILLUSTRATION**. MODEL BELUM TERVERIFIKASI untuk dial mother-of-pearl dan bracelet champagne. Minta kode varian lengkap. | Izin aset katalog belum dikonfirmasi. | Memerlukan foto nyata sesuai varian. Konfirmasi model dahulu. |
| CAS-A168-GLD | Casio | A168WG-9WDF | `products/cas-a168-gld.jpg` | Foto JPG | **MISMATCH**. Foto menunjukkan F-91W hitam dengan strap resin. Target seri A168WG-9 berwarna emas dan berband baja. | [Sumber terindikasi dari skrip dan isi foto](https://commons.wikimedia.org/wiki/File:Casio_F-91W_5051.jpg). [Referensi model](https://www.casio.com/intl/watches/casio/product.A168WG-9W/). Izin aset katalog belum dikonfirmasi. | Ganti dengan foto varian yang sesuai. |
| CAS-EDF-S108 | Casio | EFR-S108D-1AV | `products/cas-edf-s108.jpg` | Foto JPG | **MISMATCH**. Foto Edifice memiliki tiga subdial dan dua pusher. Referensi target dial hitam memakai tiga jarum tanpa subdial. | [Referensi model](https://www.casio.com/intl/watches/edifice/product.EFR-S108D-1AV/). Izin aset katalog belum dikonfirmasi. | Ganti dengan foto varian yang sesuai. |
| CAS-GA2100-BLK | Casio | GA-2100-1A1DR | `products/cas-ga2100-blk.jpg` | Foto JPG | **MISMATCH**. Foto menunjukkan bezel logam perak. Target GA-2100-1A1 memiliki tampilan serba hitam. | [Referensi model](https://www.casio.com/intl/watches/gshock/product.GA-2100-1A1/). Izin aset katalog belum dikonfirmasi. | Ganti dengan foto varian yang sesuai. |
| CTZ-PRM-BN0150 | Citizen | BN0150-28E | `products/ctz-prm-bn0150.jpg` | Foto JPG | **MISMATCH**. Dial foto bertuliskan AUTOMATIC dengan day-date dan crown di kiri bawah. Target BN0150 hitam adalah Eco-Drive. | [Referensi model](https://www.citizenwatch.com/us/en/product/BN0150-28E). Izin aset katalog belum dikonfirmasi. | Ganti dengan foto varian yang sesuai. |
| CTZ-TSU-NJ0150 | Citizen | NJ0150-56Z | `products/ctz-tsu-nj0150.svg` | Ilustrasi SVG | **ILLUSTRATION**. Referensi cocok dengan dial kuning dan bracelet baja terintegrasi. | [Referensi model](https://www.citizenwatch.com/us/en/product/NJ0150-56Z). Izin aset katalog belum dikonfirmasi. | Memerlukan foto nyata sesuai varian. |
| EGR-CAS-SHA02 | EIGER | Shasta Dual-Time Chrono | `products/egr-cas-sha02.svg` | Ilustrasi SVG | **ILLUSTRATION**. MODEL BELUM TERVERIFIKASI. EIGER mencantumkan Shasta Women. Konfirmasi konfigurasi khaki/cokelat pada data toko. | [Referensi model](https://www.eigeradventure.com/d/shasta-ws?color=black). Izin aset katalog belum dikonfirmasi. | Memerlukan foto nyata sesuai varian. Konfirmasi model dahulu. |
| EGR-OUT-HEL01 | EIGER | Heliconia Digital Altimeter | `products/egr-out-hel01.svg` | Ilustrasi SVG | **ILLUSTRATION**. MODEL BELUM TERVERIFIKASI. Katalog toko EIGER mencantumkan Helicon. Konfirmasi nama, sensor dan kode varian. | [Referensi model](https://store18609.store.link/category/watches). Izin aset katalog belum dikonfirmasi. | Memerlukan foto nyata sesuai varian. Konfirmasi model dahulu. |
| OC-CS-001 | O'Clock Signature | Aethel Minimalist Ivory 40mm | `products/oc-cs-001.svg` | Ilustrasi SVG | **ILLUSTRATION**. MODEL BELUM TERVERIFIKASI. Minta bukti model fisik dan referensi pemasok. | Izin aset katalog belum dikonfirmasi. | Memerlukan foto nyata sesuai varian. Konfirmasi model dahulu. |
| OC-DV-003 | Nautilus | OceanMaster Deep Diver 200M | `products/oc-dv-003.svg` | Ilustrasi SVG | **ILLUSTRATION**. MODEL BELUM TERVERIFIKASI. Minta bukti model fisik dan referensi pemasok. | Izin aset katalog belum dikonfirmasi. | Memerlukan foto nyata sesuai varian. Konfirmasi model dahulu. |
| OC-FM-002 | Vanguard | Chrono Sovereign Gold Black | `products/oc-fm-002.svg` | Ilustrasi SVG | **ILLUSTRATION**. MODEL BELUM TERVERIFIKASI. Minta bukti model fisik dan referensi pemasok. | Izin aset katalog belum dikonfirmasi. | Memerlukan foto nyata sesuai varian. Konfirmasi model dahulu. |
| OC-PL-005 | Horologix | Aviator Heritage 1944 Type-B | `products/oc-pl-005.svg` | Ilustrasi SVG | **ILLUSTRATION**. MODEL BELUM TERVERIFIKASI. Minta bukti model fisik dan referensi pemasok. | Izin aset katalog belum dikonfirmasi. | Memerlukan foto nyata sesuai varian. Konfirmasi model dahulu. |
| OC-SP-004 | Apex Sport | AeroTrack Stealth Chrono | `products/oc-sp-004.svg` | Ilustrasi SVG | **ILLUSTRATION**. MODEL BELUM TERVERIFIKASI. Minta bukti model fisik dan referensi pemasok. | Izin aset katalog belum dikonfirmasi. | Memerlukan foto nyata sesuai varian. Konfirmasi model dahulu. |
| OC-SW-006 | Nexus Chrono | Pulse Luxe Hybrid OLED | `products/oc-sw-006.svg` | Ilustrasi SVG | **ILLUSTRATION**. MODEL BELUM TERVERIFIKASI. Minta bukti model fisik dan referensi pemasok. | Izin aset katalog belum dikonfirmasi. | Memerlukan foto nyata sesuai varian. Konfirmasi model dahulu. |
| ORN-BAM-AC0009 | Orient | Bambino FAC00009N0 | `products/orn-bam-ac0009.jpg` | Foto JPG | **MISMATCH**. Foto Orient Capital menampilkan day-date, indeks batang dan strap hitam. Referensi Bambino memiliki dial champagne dengan jarum biru. | [Sumber terindikasi dari skrip dan isi foto](https://commons.wikimedia.org/wiki/File:Orient_Capital_FUG1R003W9_wrist_watch_face.jpg). [Referensi model](https://www.orientwatchusa.com/pages/the-orient-watch-usa-holiday-2023-gift-guide). Izin aset katalog belum dikonfirmasi. | Ganti dengan foto varian yang sesuai. |
| ORN-KAM-RAAA00 | Orient | Kamasu RA-AA0001B | `products/orn-kam-raaa00.jpg` | Foto JPG | **MISMATCH**. Foto Orient diver memiliki tombol tambahan dekat jam 2 dan bentuk indeks berbeda dari referensi Kamasu. | [Referensi model](https://www.orientwatchusa.com/products/ra-aa0001b39b). Izin aset katalog belum dikonfirmasi. | Ganti dengan foto varian yang sesuai. |
| PAL-WOD-COK02 | PALA Nusantara | Cokelat Nusantara | `products/pal-wod-cok02.svg` | Ilustrasi SVG | **ILLUSTRATION**. MODEL BELUM TERVERIFIKASI. Minta kode resmi dan spesifikasi kayu, case serta strap dari PALA. | Izin aset katalog belum dikonfirmasi. | Memerlukan foto nyata sesuai varian. Konfirmasi model dahulu. |
| PAL-WOD-MER01 | PALA Nusantara | Merah Nusantara | `products/pal-wod-mer01.svg` | Ilustrasi SVG | **ILLUSTRATION**. MODEL BELUM TERVERIFIKASI. Minta kode resmi dan spesifikasi kayu, case serta strap dari PALA. | Izin aset katalog belum dikonfirmasi. | Memerlukan foto nyata sesuai varian. Konfirmasi model dahulu. |
| SEI-5SP-SRPD55 | Seiko | SRPD55K1 | `products/sei-5sp-srpd55.jpg` | Foto JPG | **MISMATCH**. Foto menunjukkan Seiko 5 berbentuk membulat persegi tanpa bezel putar. Target SRPD55 memiliki case bulat dan bezel bernomor. | [Referensi model](https://www.seikowatches.com/us-en/products/5sports/srpd55). Izin aset katalog belum dikonfirmasi. | Ganti dengan foto varian yang sesuai. |
| SEI-PRS-SRPD37 | Seiko | SRPD37J1 | `products/sei-prs-srpd37.svg` | Ilustrasi SVG | **ILLUSTRATION**. Seiko mencantumkan Presage SRPD37J1. Cocokkan kembali warna jarum dan strap dengan unit fisik. | [Referensi model](https://www.seikowatches.com/id-id/products/presage/srpd37j1). Izin aset katalog belum dikonfirmasi. | Memerlukan foto nyata sesuai varian. |
| SEI-PRX-SRPE93 | Seiko | SRPE93K1 | `products/sei-prx-srpe93.jpg` | Foto JPG | **MISMATCH**. Foto menunjukkan SKX007 dengan tulisan DIVER’S 200m oranye dan tanpa logo Prospex. Target SRPE93 memakai case berbentuk bantalan. | [Sumber terindikasi dari skrip dan isi foto](https://commons.wikimedia.org/wiki/File:Seiko_SKX007.jpg). [Referensi model](https://www.seikowatches.com/us-en/products/prospex/srpe93). Izin aset katalog belum dikonfirmasi. | Ganti dengan foto varian yang sesuai. |
| TMX-EXP-T49961 | Timex | T49961 | `products/tmx-exp-t49961.jpg` | Foto JPG | **MISMATCH**. Foto menunjukkan dial terang, bezel warna perunggu dan strap kulit cokelat. Target memakai strap kain hijau dan case gelap. | [Referensi model](https://timex.com/products/expedition-scout-40mm-fabric-strap-watch-t49961). Izin aset katalog belum dikonfirmasi. | Ganti dengan foto varian yang sesuai. |
| TMX-Q1979-TW2T | Timex | TW2T80700 | `products/tmx-q1979-tw2t.svg` | Ilustrasi SVG | **ILLUSTRATION**. Kode tercatat di data toko. Konfirmasi kode lengkap dan varian bezel merah/biru dari Timex atau label unit. | Izin aset katalog belum dikonfirmasi. | Memerlukan foto nyata sesuai varian. Konfirmasi model dahulu. |
| WDK-KIN-WL02 | Woodka | Kinu Walnut Beige | `products/wdk-kin-wl02.svg` | Ilustrasi SVG | **ILLUSTRATION**. MODEL BELUM TERVERIFIKASI. Minta kode resmi Woodka untuk case walnut dan strap beige. | Izin aset katalog belum dikonfirmasi. | Memerlukan foto nyata sesuai varian. Konfirmasi model dahulu. |
| WDK-LCO-TK01 | Woodka | Loco Teak Navy Ikat | `products/wdk-lco-tk01.svg` | Ilustrasi SVG | **ILLUSTRATION**. MODEL BELUM TERVERIFIKASI. Minta kode resmi Woodka serta bukti varian case jati dan strap ikat navy. | Izin aset katalog belum dikonfirmasi. | Memerlukan foto nyata sesuai varian. Konfirmasi model dahulu. |

## Spesifikasi pembanding dari MySQL

| SKU | Warna dial/case menurut katalog | Strap menurut katalog | Ukuran case menurut katalog |
| --- | --- | --- | --- |
| AC-AUT-9601MAR | Rose Gold PVD / Textured Black Dial | Genuine Leather Alligator Pattern | 43mm |
| AC-CLS-6647MCB | Gunmetal Charcoal / Rose Gold Highlights | Solid Milanese Mesh Stainless Steel | 44mm |
| AC-CLS-8645MD | White Dial / Warm Brown Leather | Genuine Soft Calfskin Leather Strap | 40mm |
| AC-SPT-6295MPR | Rose Gold / Stealth Black | High-Grade FKM Rubber Strap | 46mm |
| AC-WMN-2A40LDB | Champagne Gold / Natural Mother of Pearl | Stainless Steel Jewellery Link Bracelet | 34mm |
| CAS-A168-GLD | Metallic Gold / Champagne | Stainless Steel Gold-Tone Band | 36.3mm |
| CAS-EDF-S108 | Silver / Sunray Black Dial | Solid Stainless Steel Link | 39.9mm |
| CAS-GA2100-BLK | Matte All-Black | Resin Band | 45.4mm |
| CTZ-PRM-BN0150 | Matte Black / Orange Accent Hand | Black Polyurethane Diver Strap | 44mm |
| CTZ-TSU-NJ0150 | Vibrant Yellow / Stainless Steel | Integrated Stainless Steel Bracelet | 40mm |
| EGR-CAS-SHA02 | Desert Khaki / Tactical Dark Brown | Silicone Ergonomic Strap | 46mm |
| EGR-OUT-HEL01 | Tactical Charcoal / Olive Green | High-Tension Polyurethane (PU) | 48mm |
| OC-CS-001 | Ivory / Tan Brown | Genuine Leather | 40mm |
| OC-DV-003 | Deep Sea Navy | FKM Rubber Strap | 42mm |
| OC-FM-002 | Muted Gold / Charcoal | Stainless Steel Milanese | 41mm |
| OC-PL-005 | Vintage Brown / Cream | Vintage Brown Leather | 42mm |
| OC-SP-004 | Matte Black | Silicone Hybrid | 43mm |
| OC-SW-006 | Silver Slate | Quick-Release Leather | 41mm |
| ORN-BAM-AC0009 | Antique Cream / Blued Hands | Genuine Leather Dark Brown | 40.5mm |
| ORN-KAM-RAAA00 | Deep Black / Red Tip Second Hand | Solid Stainless Steel Bracelet | 41.8mm |
| PAL-WOD-COK02 | Natural Teak Brown / Warm Camel | Natural Buffalo Leather Strap | 35mm x 35mm (Square) |
| PAL-WOD-MER01 | Sonokeling Rosewood / Terracotta Red | Vegetable Tanned Leather (Kulit Nabati Asli) | 35mm x 35mm (Square) |
| SEI-5SP-SRPD55 | Brushed Silver / Matte Black Dial | Stainless Steel Three-fold Clasp | 42.5mm |
| SEI-PRS-SRPD37 | Emerald Green Sunray / Rose Gold Hands | Espresso Calfskin Leather with Deployment Clasp | 40.5mm |
| SEI-PRX-SRPE93 | Charcoal Black / Stainless Steel | High-Grade Accordion Silicone | 45mm |
| TMX-EXP-T49961 | Olive Green / Matte Black Case | Green Nylon Slip-thru Strap | 40mm |
| TMX-Q1979-TW2T | Pepsi Bezel (Red/Navy) / Silver | Woven Stainless Steel Mesh Bracelet | 38mm |
| WDK-KIN-WL02 | Dark Walnut / Sand Beige | Genuine Soft Leather Beige | 38mm |
| WDK-LCO-TK01 | Teak Wood / Navy Ethnic Pattern | Interchangeable Tenun Ikat Woven Fabric | 40mm |

## Bukti audit

- Query SELECT MySQL mencakup semua baris produk termasuk status aktif dan soft delete. Seluruh 29 baris aktif dan tanpa soft delete.
- Storage memiliki 38 aset produk, terdiri dari 29 SVG ilustrasi vektor dan 9 JPG. Sembilan SVG cadangan tidak dirujuk oleh produk saat ini. Semua 9 JPG dibuka secara visual. Isi seluruh SVG diperiksa sebagai bentuk vektor tanpa elemen gambar raster tertanam. Seluruh path produk ada di disk.
- `public/storage` adalah junction Windows ke `storage/app/public`. Inventaris melalui junction juga berisi 38 file. Pemeriksaan ini memvalidasi akses filesystem. HTTP 200 dan browser belum diuji pada sesi audit.
- Homepage dan katalog memakai komponen kartu yang sama. Komponen kartu, detail publik, serta daftar dan form edit produk Admin membangun URL dari `image_path` yang sama. Form tambah Admin menyediakan input unggah.
- `ProductSeeder.php` memetakan 29 SKU ke SVG. Skrip gambar yang ditemukan di sumber proyek adalah `fix_images.php`. Skrip ini memakai ambang ukuran untuk menerima JPG yang sudah ada dan memiliki pemetaan F-91W, SKX007, dan Orient Capital ke SKU berbeda. Enam JPG lain hanya dicantumkan sebagai hasil sesi sebelumnya tanpa asal file yang dapat diaudit. Skrip diperiksa secara statis.
- Git berada pada branch `master` tanpa commit dan tanpa entri index pada awal audit. File proyek muncul sebagai untracked. Pemeriksaan riwayat Git tidak dapat mengaitkan perubahan aset dengan commit tertentu.

## Usulan visibilitas katalog final

Daftar kandidat untuk disembunyikan sementara adalah **seluruh 29 SKU pada matriks**, sampai tiap SKU memiliki foto nyata yang tepat dengan izin yang jelas. Jika katalog sedang dipakai untuk demonstrasi, menerapkan usulan ini sekaligus akan mengosongkan katalog. Seluruh SKU masih aktif. Tunggu persetujuan daftar spesifik sebelum mengubah visibilitas.

Sumber foto yang perlu diminta adalah foto unit milik pengguna, aset berizin dari pemegang hak, atau foto berlisensi dengan identitas varian yang dapat dibuktikan. Tautan produsen di matriks membantu mencocokkan model. Untuk baris tanpa referensi yang pasti, minta kode model lengkap atau foto label unit dari produsen/pemasok sebelum memilih aset.
