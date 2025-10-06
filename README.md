# Pi Monitor Agent (v1 - Native cURL) 

![PHP](https://img.shields.io/badge/PHP-7.0%2B-blue.svg) ![License](https://img.shields.io/badge/License-MIT-green.svg)

Agen monitoring berbasis PHP OOP yang ringan, dirancang untuk berjalan di perangkat Raspberry Pi. Skrip ini secara periodik mengumpulkan metrik sistem dan status layanan, lalu mengirimkannya ke backend terpusat untuk dianalisis.

## ✨ Fitur

- **Monitoring Kesehatan Sistem:** Mengumpulkan metrik vital seperti Latency, Uptime, Ketersediaan Memori & Disk.
- **Pengecekan Status Layanan:** Memeriksa status koneksi ke server target dan interface jaringan (PPTP/OpenVPN).
- **Data Kontekstual:** Mengirimkan data identitas perangkat seperti Device Name, Wilayah, dan Provinsi.
- **Struktur OOP:** Dibuat dengan prinsip Object-Oriented untuk kemudahan perawatan.
- **Konfigurasi Terpusat:** Semua kredensial dan parameter diatur melalui file `.env` untuk keamanan.

## ⚙️ Persyaratan

- PHP >= 7.0
- Ekstensi PHP: `php-curl`, `php-json`
- **Composer**

