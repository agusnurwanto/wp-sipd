# WP-SIPD
Optimasi aplikasi SIPD dengan chrome extension dan plugin wordpress
Semoga bermanfaat

### GRUP telegram https://t.me/sipd_chrome_extension

### Chrome extension https://github.com/agusnurwanto/sipd-chrome-extension

Cara pakai plugin:
- Install wordpress
- Install plugin ini dan aktifkan
- Import SQL file tabel.sql untuk membuat tabel tempat menyimpan data yang diambil dari sipd.kemendagri.go.id
- Masuk ke dashboard admin wordpress
	- SIPD Options > API KEY chrome extension
	- Secara default data apikey sudah terisi, bisa diedit sesuai keperluan
	- Klik tombol Save Change / Simpan. Apikey ini harus sama dengan yang ada di configurasi SIPD chrome extension
- Untuk menampilkan SSH menggunakan shortcode [datassh]
- Untuk menampilkan akun belanja menggunakan shortcode [rekbelanja]
- Halaman RKA akan otomatis tergenerate dalam bentuk post yang dikelompokan dalam category perangkat daerah ketika melakukan singkronisasi data
- Theme yang sudah dites astra theme

### HARUS Update php.ini

Optimasi server apache agar proses pengiriman data dari chrome extension ke server wordpress berjalan lancar (edit file php.ini):
- max_input_vars = 300000
- max_execution_time = 300
- max_input_time = 600
- memory_limit = 3556M
- post_max_size = 20M

Permintaan fitur:
- User umum bisa request penambahan fitur dengan membuat issue

### Video Tutorial 

- Progress Pengembangan Aplikasi SIPD Lokal, Export RKA Semua Kegiatan https://youtu.be/t84n2jZUfFo
- Integrasi data SIPD ke SIMDA keuangan https://youtu.be/vFOsAlnxmTo
- Setting Koneksi SIMDA API PHP menggunakan ODBC di Windows https://www.youtube.com/watch?v=ojc6Dr6fZ8I