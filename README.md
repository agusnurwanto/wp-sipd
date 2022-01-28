# WP-SIPD
Optimasi aplikasi SIPD dengan chrome extension dan plugin wordpress
Semoga bermanfaat

### DONASI
- Donasi untuk pengembang aplikasi, klik di link ini https://smkasiyahhomeschooling.blogspot.com/p/donasi-pengembangan-smk-asiyah.html
- Untuk pemda yang kesulitan dalam penerapan wp-sipd atau belum memiliki tenaga IT, kami menawarkan jasa setting dan penerapan wp-sipd. Informasi lebih detail di https://smkasiyahhomeschooling.blogspot.com/p/produk-dan-layanan-kami.html

### GRUP telegram https://t.me/sipd_chrome_extension

### Chrome extension https://github.com/agusnurwanto/sipd-chrome-extension

### Cara pakai plugin
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
- `[apbdpenjabaran tahun_anggaran="2021" lampiran="5"]` short code untuk menampilkan APBD penjabaran lampiran 5
- `[apbdpenjabaran tahun_anggaran="2021" lampiran="4"]` short code untuk menampilkan APBD penjabaran lampiran 4
- `[apbdpenjabaran tahun_anggaran="2021" lampiran="3"]` short code untuk menampilkan APBD penjabaran lampiran 3
- `[apbdpenjabaran tahun_anggaran="2021" lampiran="2" id_skpd="xxxx"]` short code untuk menampilkan APBD penjabaran lampiran 2
- `[apbdpenjabaran tahun_anggaran="2021" lampiran="1"]` short code untuk menampilkan APBD penjabaran lampiran 1
- `[monitor_sipd tahun_anggaran="2021" kode_rek="4,5,6.1,6.2"]` short code untuk halaman monitoring update pagu per SKPD yang diurutkan berdasarkan waktu updatenya
- Install plugin Ultimate Member untuk halaman profil user PA/KPA
- Tambahkan shortcode `[menu_monev]` pada form user profile Ultimate Member untuk menampilkan url halaman MONEV sesuai user yang login
- Jika terjadi koneksi database sql server error karena integrasi simda setting diaktifkan, tambahkan paramter GET **no_simda=1** pada url. Parameter ini untuk memutuskan koneksi ke DB SQL SERVER, selanjutnya kita bisa memperbaikan settingan koneksi di menu **SIMDA Setting**.

### HARUS Update php.ini
Optimasi server apache agar proses pengiriman data dari chrome extension ke server wordpress berjalan lancar (edit file php.ini):
- max_input_vars = 1000000
- max_execution_time = 300
- max_input_time = 600
- memory_limit = 3556M
- post_max_size = 20M
- **pastikan untuk menghapus kode ; sebagai tanda komentar pada file php.ini, terutama pada configurasi ;max_input_vars. Jika tidak dilakukan maka settingan yang sudah dilakukan akan dianggap sebagai komentar dan tidak dijalankan oleh server apache.**

### Cara singkron tahun anggaran baru 2022 ke database SQL Server SIMDA
- Sesuaikan tahun anggaran baru di form **SIPD Options > Tahun Anggaran SIPD**
- Ubah database SIMDA sesuai database tahun yang baru di halaman **SIMDA Options > Database SIMDA**
- Tambahkan database SIMDA yang baru di database_simda dan lakukan testing koneksi di aplikasi https://github.com/agusnurwanto/SIMDA-API-PHP
- Lakukan singkroniasi data perangkat daerah menggunakan akun admin daerah atau TAPD pemda di SIPD kemendagri
- Lakukan cek kembali mapping SKPD di halaman **SIMDA Options**. Jika di tabel ta_sub_unit belum dibuat data SKPD-nya, maka perlu dibuat dulu manual atau bisa digenerate dengan mengaktifkan opsi di form **Integrasi Sub Unit SIMDA sesuai SIPD**
- Setelah dipastikan data mapping SKPD sudah sesuai antara di SIPD dan SIMDA, baru melakukan singkronisasi RKA

### Permintaan fitur baru
- User umum bisa request penambahan fitur dengan membuat issue

### Video Tutorial 
- Progress Pengembangan Aplikasi SIPD Lokal, Export RKA Semua Kegiatan https://youtu.be/t84n2jZUfFo
- Integrasi data SIPD ke SIMDA keuangan https://youtu.be/vFOsAlnxmTo
- Setting Koneksi SIMDA API PHP menggunakan ODBC di Windows https://www.youtube.com/watch?v=ojc6Dr6fZ8I
