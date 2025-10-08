# Agent Monitoring Perangkat (Vanilla PHP)

Skrip PHP ringan ini berfungsi sebagai agen untuk mengumpulkan metrik dari perangkat (seperti Raspberry Pi) dan mengirimkannya ke sebuah endpoint RESTful API.

Agen ini dirancang khusus dengan mempertimbangkan kendala lingkungan dan konektivitas, sehingga dibuat sepenuhnya menggunakan **Vanilla PHP (PHP murni)** tanpa dependensi eksternal.

---

## Latar Belakang & Kendala Desain

Pilihan arsitektur "Zero Dependency" dan target PHP 7.0 diambil secara sadar karena kendala spesifik pada lingkungan target:

* **🎯 Target PHP 7.0:** Agen ini dirancang untuk berjalan di perangkat dengan sistem operasi lama (seperti Raspbian 9 "Stretch") yang versi PHP-nya terkunci di **7.0**. Hal ini membuat penggunaan library modern yang umumnya memerlukan PHP 7.2+ menjadi tidak memungkinkan.
* **🌐 Kuota Internet Terbatas:** Perangkat target beroperasi di lokasi dengan koneksi internet modem yang terbatas dan mahal. Menghilangkan `composer` berarti **tidak ada lagi langkah `composer install`** yang memakan kuota data.
* **🚀 Deployment Ringan:** Tujuannya adalah agar proses *deployment* atau *update* agen bisa dilakukan semudah mungkin, yaitu hanya dengan menyalin beberapa file skrip, tanpa perlu proses instalasi yang rumit.

---

## Kebutuhan Sistem

* PHP versi **7.0** atau lebih tinggi
* Ekstensi PHP: `php-curl` (untuk mengirim data ke API)
* Akses ke *command-line tools* Linux: `ping`, `free`, `df`, `uptime`, `ip`

