# Assets Management System (AMS) Berbasis RFID
AMS merupakan sistem informasi berbasis web yang digunakan untuk mengelola aset-aset yang telah diberi stiker berupa Tag RFID yang telah terintegrasi dengan aplikasi mobile sebagai reader dari tag tersebut.

## Spesifikasi
Sistem ini dibangun menggunakan Laravel versi 12 yang menggunakan PHP versi 8.2 dan menggunakan composer, serta memerlukan beberapa package berikut agar setiap fungsi yang ada pada sistem berjalan sepenuhnya.
- laravel/dompdf versi 3.1
- intervention/image-laravel versi 1.5
- maatwebsite/excel versi 3.1
- phpoffice/phpspreadsheet versi 1.29
- yajra/laravel-datatables versi 12

Untuk memastikan kelancaran, ubah beberapa bagian nilai dalam file `php.ini` (jika nilainya bisa lebih tinggi, maka lebih baik) berikut:
- upload_max_filesize=128MB
- max_execution_time=3600
- max_input_time=3600

## Instalasi untuk Development
+ pastikan PHP dan Composer telah tersedia pada perangkat yang Anda gunakan
+ git clone atau unduh langsung project ini
+ copy atau salin isi `.env.example` ke file baru bernama `.env`
+ jalankan perintah `composer install` pada terminal
+ jalankan perintah `php artisan key:generate`
+ buka `.env` dan pastikan semua bagian yang ada di bawah komentar `# INITIAL PASSWORD ...` terisi, karena digunakan untuk generate akun user pertama kali di file `UserSeeder`
+ pastikan konfigurasi database Anda telah tersimpan dengan benar di `.env`
+ jalankan perintah `php artisan migrate --seed` pada terminal
+ jalankan perintah `php artisan serve`
