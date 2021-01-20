--
-- Table structure for table `data_unit`
--
CREATE TABLE `data_unit` (
  `id` int(11) NOT NULL,
  `bidur_1` smallint(6) NOT NULL,
  `bidur_2` smallint(6) NOT NULL,
  `bidur_3` smallint(6) NOT NULL,
  `idinduk` int(11) NOT NULL,
  `ispendapatan` tinyint(4) NOT NULL,
  `isskpd` tinyint(4) NOT NULL,
  `kode_skpd_1` varchar(10) NOT NULL,
  `kode_skpd_2` varchar(10) NOT NULL,
  `kodeunit` varchar(30) NOT NULL,
  `komisi` int(11) DEFAULT NULL,
  `namabendahara` text,
  `status` varchar(50) NOT NULL,
  `namakepala` text NOT NULL,
  `namaunit` text NOT NULL,
  `nipbendahara` varchar(30) DEFAULT NULL,
  `nipkepala` varchar(30) NOT NULL,
  `pangkatkepala` varchar(50) NOT NULL,
  `setupunit` int(11) NOT NULL,
  `statuskepala` varchar(20) NOT NULL,
  `update_at` datetime NOT NULL,
  `tahun_anggaran` year(4) NOT NULL DEFAULT '2021',
  `active` tinyint(4) DEFAULT 1
) ENGINE = InnoDB DEFAULT CHARSET = latin1;
-- 
--
-- Table structure for table `data_akun`
--
CREATE TABLE `data_akun` (
  `id` int(11) NOT NULL,
  `belanja` varchar(10) NOT NULL,
  `id_akun` int(11) NOT NULL,
  `is_bagi_hasil` tinyint(4) NOT NULL,
  `is_bankeu_khusus` tinyint(4) NOT NULL,
  `is_bankeu_umum` tinyint(4) NOT NULL,
  `is_barjas` tinyint(4) NOT NULL,
  `is_bl` tinyint(4) NOT NULL,
  `is_bos` tinyint(4) NOT NULL,
  `is_btt` tinyint(4) NOT NULL,
  `is_bunga` tinyint(4) NOT NULL,
  `is_gaji_asn` tinyint(4) NOT NULL,
  `is_hibah_brg` tinyint(4) NOT NULL,
  `is_hibah_uang` tinyint(4) NOT NULL,
  `is_locked` tinyint(4) NOT NULL,
  `is_modal_tanah` tinyint(4) NOT NULL,
  `is_pembiayaan` tinyint(4) NOT NULL,
  `is_pendapatan` tinyint(4) NOT NULL,
  `is_sosial_brg` tinyint(4) NOT NULL,
  `is_sosial_uang` tinyint(4) NOT NULL,
  `is_subsidi` tinyint(4) NOT NULL,
  `kode_akun` varchar(50) NOT NULL,
  `nama_akun` text NOT NULL,
  `set_input` tinyint(4) NOT NULL,
  `set_lokus` tinyint(4) DEFAULT NULL,
  `status` varchar(20) DEFAULT NULL,
  `update_at` datetime NOT NULL,
  `tahun_anggaran` year(4) NOT NULL DEFAULT '2021'
) ENGINE = InnoDB DEFAULT CHARSET = latin1;
-- 
--
-- Table structure for table `data_rka`
--
CREATE TABLE `data_rka` (
  `id` int(11) NOT NULL,
  `created_user` int(11) DEFAULT NULL,
  `createddate` varchar(10) DEFAULT NULL,
  `createdtime` varchar(10) DEFAULT NULL,
  `harga_satuan` double(20, 0) DEFAULT NULL,
  `id_daerah` int(11) DEFAULT NULL,
  `id_rinci_sub_bl` int(11) DEFAULT NULL,
  `id_standar_nfs` tinyint(4) DEFAULT NULL,
  `is_locked` tinyint(4) DEFAULT NULL,
  `jenis_bl` varchar(50) DEFAULT NULL,
  `ket_bl_teks` text DEFAULT NULL,
  `kode_akun` varchar(50) DEFAULT NULL,
  `koefisien` text DEFAULT NULL,
  `lokus_akun_teks` text DEFAULT NULL,
  `nama_akun` text DEFAULT NULL,
  `nama_komponen` text DEFAULT NULL,
  `spek_komponen` text DEFAULT NULL,
  `satuan` varchar(50) DEFAULT NULL,
  `spek` text DEFAULT NULL,
  `sat1` text DEFAULT NULL,
  `sat2` text DEFAULT NULL,
  `sat3` text DEFAULT NULL,
  `sat4` text DEFAULT NULL,
  `volum1` text DEFAULT NULL,
  `volum2` text DEFAULT NULL,
  `volum3` text DEFAULT NULL,
  `volum4` text DEFAULT NULL,
  `subs_bl_teks` text DEFAULT NULL,
  `total_harga` double(20, 0) DEFAULT NULL,
  `rincian` double(20, 0) DEFAULT NULL,
  `totalpajak` double(20, 0) DEFAULT NULL,
  `updated_user` int(11) DEFAULT NULL,
  `updateddate` varchar(20) DEFAULT NULL,
  `updatedtime` varchar(20) DEFAULT NULL,
  `user1` varchar(50) DEFAULT NULL,
  `user2` varchar(50) DEFAULT NULL,
  `active` tinyint(4) DEFAULT 1,
  `update_at` datetime DEFAULT NULL,
  `tahun_anggaran` year(4) NOT NULL DEFAULT '2021',
  `idbl` int(11) DEFAULT NULL,
  `idsubbl` int(11) DEFAULT NULL,
  `kode_bl` varchar(50) NOT NULL,
  `kode_sbl` varchar(50) NOT NULL,
  `id_prop_penerima` int(11) DEFAULT NULL,
  `id_camat_penerima` int(11) DEFAULT NULL,
  `id_kokab_penerima` int(11) DEFAULT NULL,
  `id_lurah_penerima` int(11) DEFAULT NULL,
  `id_penerima` int(11) DEFAULT NULL,
  `idkomponen` double(20, 0) DEFAULT NULL,
  `idketerangan` int(11) DEFAULT NULL,
  `idsubtitle` int(11) DEFAULT NULL
) ENGINE = InnoDB DEFAULT CHARSET = latin1;
--
-- Table structure for table `data_capaian_prog_sub_keg`
--
CREATE TABLE `data_capaian_prog_sub_keg` (
  `id` int(11) NOT NULL,
  `satuancapaian` varchar(50) DEFAULT NULL,
  `targetcapaianteks` varchar(50) DEFAULT NULL,
  `capaianteks` text,
  `targetcapaian` int(11) DEFAULT NULL,
  `kode_sbl` varchar(50) DEFAULT NULL,
  `idsubbl` int(11) DEFAULT NULL,
  `active` tinyint(4) DEFAULT '1',
  `update_at` datetime NOT NULL,
  `tahun_anggaran` year(4) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
--
-- Table structure for table `data_dana_sub_keg`
--
CREATE TABLE `data_dana_sub_keg` (
  `id` int(11) NOT NULL,
  `namadana` varchar(50) DEFAULT NULL,
  `kodedana` varchar(50) DEFAULT NULL,
  `iddana` int(11) DEFAULT NULL,
  `iddanasubbl` int(11) DEFAULT NULL,
  `pagudana` double(20, 0) DEFAULT NULL,
  `kode_sbl` varchar(50) DEFAULT NULL,
  `idsubbl` int(11) DEFAULT NULL,
  `active` tinyint(4) DEFAULT 1,
  `update_at` datetime NOT NULL,
  `tahun_anggaran` year(4) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
--
-- Table structure for table `data_output_giat_sub_keg`
--
CREATE TABLE `data_output_giat_sub_keg` (
  `id` int(11) NOT NULL,
  `outputteks` text,
  `satuanoutput` varchar(50) DEFAULT NULL,
  `targetoutput` int(11) DEFAULT NULL,
  `targetoutputteks` varchar(50) DEFAULT NULL,
  `kode_sbl` varchar(50) DEFAULT NULL,
  `idsubbl` int(11) DEFAULT NULL,
  `active` tinyint(4) DEFAULT '1',
  `update_at` datetime NOT NULL,
  `tahun_anggaran` year(4) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Table structure for table `data_keg_indikator_hasil`
--

CREATE TABLE `data_keg_indikator_hasil` (
  `id` int(11) NOT NULL,
  `hasilteks` text,
  `satuanhasil` varchar(50) DEFAULT NULL,
  `targethasil` varchar(50) DEFAULT NULL,
  `targethasilteks` varchar(50) DEFAULT NULL,
  `kode_sbl` varchar(50) DEFAULT NULL,
  `idsubbl` varchar(50) DEFAULT NULL,
  `active` tinyint(4) DEFAULT 1,
  `update_at` datetime NOT NULL,
  `tahun_anggaran` year(4) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
-- 
--
-- Table structure for table `data_ssh`
--
CREATE TABLE `data_ssh` (
  `id` int(11) NOT NULL,
  `id_standar_harga` int(11) NOT NULL,
  `kode_standar_harga` varchar(30) NOT NULL,
  `nama_standar_harga` text NOT NULL,
  `satuan` text NOT NULL,
  `spek` text NOT NULL,
  `is_deleted` tinyint(4) NOT NULL,
  `is_locked` tinyint(4) NOT NULL,
  `kelompok` tinyint(4) NOT NULL,
  `harga` double(20, 0) NOT NULL,
  `kode_kel_standar_harga` varchar(30) NOT NULL,
  `nama_kel_standar_harga` text NOT NULL,
  `update_at` datetime NOT NULL,
  `tahun_anggaran` year(4) NOT NULL DEFAULT '2020'
) ENGINE = InnoDB DEFAULT CHARSET = latin1;
-- 
--
-- Table structure for table `data_ssh_rek_belanja`
--
CREATE TABLE `data_ssh_rek_belanja` (
  `id` int(11) NOT NULL,
  `id_akun` int(11) NOT NULL,
  `kode_akun` varchar(50) NOT NULL,
  `nama_akun` text NOT NULL,
  `id_standar_harga` int(11) NOT NULL,
  `tahun_anggaran` year(4) NOT NULL DEFAULT '2021'
) ENGINE = InnoDB DEFAULT CHARSET = latin1;
-- 
--
-- Table structure for table `data_sub_keg_bl`
--
CREATE TABLE `data_sub_keg_bl` (
  `id` int(11) NOT NULL,
  `id_sub_skpd` int(11) NOT NULL,
  `id_lokasi` int(11) DEFAULT NULL,
  `id_label_kokab` int(11) NOT NULL,
  `nama_dana` text,
  `no_sub_giat` varchar(20) NOT NULL,
  `kode_giat` varchar(50) NOT NULL,
  `id_program` int(11) NOT NULL,
  `nama_lokasi` text DEFAULT NULL,
  `waktu_akhir` int(11) NOT NULL,
  `pagu_n_lalu` double(20, 0) DEFAULT NULL,
  `id_urusan` int(11) NOT NULL,
  `id_unik_sub_bl` text NOT NULL,
  `id_sub_giat` int(11) NOT NULL,
  `label_prov` text,
  `kode_program` varchar(50) NOT NULL,
  `kode_sub_giat` varchar(50) NOT NULL,
  `no_program` varchar(20) NOT NULL,
  `kode_urusan` varchar(20) NOT NULL,
  `kode_bidang_urusan` varchar(20) NOT NULL,
  `nama_program` text NOT NULL,
  `target_4` text,
  `target_5` text,
  `id_bidang_urusan` int(11) DEFAULT NULL,
  `nama_bidang_urusan` text,
  `target_3` text,
  `no_giat` varchar(50) NOT NULL,
  `id_label_prov` int(11) NOT NULL,
  `waktu_awal` int(11) NOT NULL,
  `pagu` double(20, 0) NOT NULL,
  `output_sub_giat` text,
  `sasaran` text,
  `indikator` text,
  `id_dana` int(11) DEFAULT NULL,
  `nama_sub_giat` text NOT NULL,
  `pagu_n_depan` double(20, 0) NOT NULL,
  `satuan` text,
  `id_rpjmd` int(11) NOT NULL,
  `id_giat` int(11) NOT NULL,
  `id_label_pusat` int(11) NOT NULL,
  `nama_giat` text NOT NULL,
  `kode_skpd` varchar(50) NOT NULL,
  `nama_skpd` text NOT NULL,
  `kode_sub_skpd` varchar(50) NOT NULL,
  `id_skpd` int(11) NOT NULL,
  `id_sub_bl` int(11) DEFAULT NULL,
  `nama_sub_skpd` text NOT NULL,
  `target_1` text,
  `nama_urusan` text NOT NULL,
  `target_2` text,
  `label_kokab` text,
  `label_pusat` text,
  `pagu_keg` double(20, 0) NOT NULL,
  `id_bl` int(11) DEFAULT NULL,
  `kode_bl` varchar(50) NOT NULL,
  `kode_sbl` varchar(50) NOT NULL,
  `active` tinyint(4) DEFAULT '1',
  `update_at` datetime NOT NULL,
  `tahun_anggaran` year(4) NOT NULL DEFAULT '2021'
) ENGINE = InnoDB DEFAULT CHARSET = latin1;
-- 
--
-- Table structure for table `data_sub_keg_indikator`
--
CREATE TABLE `data_sub_keg_indikator` (
  `id` int(11) NOT NULL,
  `outputteks` text NOT NULL,
  `targetoutput` int(11) NOT NULL,
  `satuanoutput` text NOT NULL,
  `idoutputbl` int(11) NOT NULL,
  `targetoutputteks` text NOT NULL,
  `kode_sbl` varchar(50) NOT NULL,
  `idsubbl` int(11) DEFAULT NULL,
  `active` tinyint(4) DEFAULT '1',
  `update_at` datetime NOT NULL,
  `tahun_anggaran` year(4) NOT NULL
) ENGINE = InnoDB DEFAULT CHARSET = latin1;
-- 
--
-- Table structure for table `data_unit_pagu`
--
CREATE TABLE `data_unit_pagu` (
  `id` int(11) NOT NULL,
  `batasanpagu` double(20, 0) NOT NULL,
  `id_daerah` int(11) NOT NULL,
  `id_level` int(11) NOT NULL,
  `id_skpd` int(11) NOT NULL,
  `id_unit` int(11) NOT NULL,
  `id_user` int(11) NOT NULL,
  `is_anggaran` tinyint(4) NOT NULL,
  `is_deleted` tinyint(4) NOT NULL,
  `is_komponen` tinyint(4) NOT NULL,
  `is_locked` smallint(6) NOT NULL,
  `is_skpd` tinyint(4) NOT NULL,
  `kode_skpd` varchar(50) NOT NULL,
  `kunci_bl` tinyint(4) NOT NULL,
  `kunci_bl_rinci` tinyint(4) NOT NULL,
  `kuncibl` tinyint(4) NOT NULL,
  `kunciblrinci` tinyint(4) NOT NULL,
  `nilaipagu` double(20, 0) NOT NULL,
  `nilaipagumurni` double(20, 0) NOT NULL,
  `nilairincian` double(20, 0) NOT NULL,
  `pagu_giat` double(20, 0) NOT NULL,
  `realisasi` double(20, 0) NOT NULL,
  `rinci_giat` double(20, 0) NOT NULL,
  `set_pagu_giat` double(20, 0) NOT NULL,
  `set_pagu_skpd` double(20, 0) NOT NULL,
  `tahun` double(20, 0) NOT NULL,
  `total_giat` double(20, 0) NOT NULL,
  `totalgiat` double(20, 0) NOT NULL,
  `update_at` datetime NOT NULL,
  `tahun_anggaran` year(4) NOT NULL
) ENGINE = InnoDB DEFAULT CHARSET = latin1;
-- 
--
-- Table structure for table `data_prog_keg`
--
CREATE TABLE `data_prog_keg` (
  `id` int(11) NOT NULL,
  `id_bidang_urusan` int(11) NOT NULL,
  `id_program` int(11) NOT NULL,
  `id_sub_giat` int(11) NOT NULL,
  `id_urusan` int(11) NOT NULL,
  `is_locked` int(11) NOT NULL,
  `kode_bidang_urusan` varchar(50) NOT NULL,
  `kode_giat` varchar(50) NOT NULL,
  `kode_program` varchar(50) NOT NULL,
  `kode_sub_giat` varchar(50) NOT NULL,
  `kode_urusan` varchar(50) NOT NULL,
  `nama_bidang_urusan` text NOT NULL,
  `nama_giat` text NOT NULL,
  `nama_program` text NOT NULL,
  `nama_sub_giat` text NOT NULL,
  `nama_urusan` text NOT NULL,
  `status` varchar(20) DEFAULT NULL,
  `update_at` datetime NOT NULL,
  `tahun_anggaran` year(4) NOT NULL DEFAULT '2021'
) ENGINE = InnoDB DEFAULT CHARSET = latin1;
-- 
--
-- Table structure for table `data_rpjmd`
--
CREATE TABLE `data_rpjmd` (
  `id` int(11) NOT NULL,
  `id_bidang_urusan` int(11) NOT NULL,
  `id_program` int(11) NOT NULL,
  `id_rpjmd` int(11) NOT NULL,
  `indikator` text NOT NULL,
  `kebijakan_teks` text NOT NULL,
  `kode_bidang_urusan` varchar(50) NOT NULL,
  `kode_program` varchar(50) NOT NULL,
  `kode_skpd` varchar(50),
  `misi_teks` text NOT NULL,
  `nama_bidang_urusan` varchar(100) NOT NULL,
  `nama_program` text NOT NULL,
  `nama_skpd` text NOT NULL,
  `pagu_1` varchar(50) NOT NULL,
  `pagu_2` varchar(50) NOT NULL,
  `pagu_3` varchar(50) NOT NULL,
  `pagu_4` varchar(50) NOT NULL,
  `pagu_5` varchar(50) NOT NULL,
  `sasaran_teks` text NOT NULL,
  `satuan` varchar(50) NOT NULL,
  `strategi_teks` text NOT NULL,
  `target_1` varchar(50) NOT NULL,
  `target_2` varchar(50) NOT NULL,
  `target_3` varchar(50) NOT NULL,
  `target_4` varchar(50) NOT NULL,
  `target_5` varchar(50) NOT NULL,
  `tujuan_teks` text NOT NULL,
  `visi_teks` text NOT NULL,
  `update_at` datetime NOT NULL,
  `tahun_anggaran` year(4) NOT NULL DEFAULT '2021'
) ENGINE = InnoDB DEFAULT CHARSET = latin1;

-- Table structure for table `data_alamat` --

CREATE TABLE `data_alamat` (
  `id` int(11) NOT NULL,
  `id_alamat` int(11) NOT NULL,
  `nama` text NOT NULL,
  `id_prov` int(11) NOT NULL,
  `id_kab` int(11) NOT NULL,
  `id_kec` int(11) NOT NULL,
  `is_prov` tinyint(4) NOT NULL,
  `is_kab` tinyint(4) NOT NULL,
  `is_kec` tinyint(4) NOT NULL,
  `is_kel` tinyint(4) NOT NULL,
  `updated_at` datetime NOT NULL,
  `tahun` year(4) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- Table structure for table `data_profile_penerima_bantuan`--

CREATE TABLE `data_profile_penerima_bantuan` (
  `id` int(11) NOT NULL,
  `id_profil` int(11) NOT NULL,
  `nama_teks` text NOT NULL,
  `alamat_teks` text NOT NULL,
  `jenis_penerima` text NOT NULL,
  `updated_at` datetime NOT NULL,
  `tahun` year(4) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1; 

-- Table structure for table `data_desa_kelurahan`--

CREATE TABLE `data_desa_kelurahan` (
  `id` int(11) NOT NULL,
  `camat_teks` varchar(50) DEFAULT NULL,
  `id_camat` int(11) DEFAULT NULL,
  `id_daerah` int(11) DEFAULT NULL,
  `id_level` varchar(50) DEFAULT NULL,
  `id_lurah` int(11) DEFAULT NULL,
  `id_profil` int(11) DEFAULT NULL,
  `id_user` int(11) DEFAULT NULL,
  `is_desa` tinyint(4) DEFAULT NULL,
  `is_locked` tinyint(4) DEFAULT NULL,
  `jabatan` varchar(50) DEFAULT NULL,
  `jenis` int(11) DEFAULT NULL,
  `kab_kota` varchar(50) DEFAULT NULL,
  `kode_lurah` varchar(50) DEFAULT NULL,
  `login_name` varchar(50) DEFAULT NULL,
  `lurah_teks` varchar(50) DEFAULT NULL,
  `nama_daerah` varchar(50) DEFAULT NULL,
  `nama_user` varchar(50) DEFAULT NULL,
  `accasmas` tinyint(4) DEFAULT NULL,
  `accbankeu` tinyint(4) DEFAULT NULL,
  `accdisposisi` tinyint(4) DEFAULT NULL,
  `accgiat` tinyint(4) DEFAULT NULL,
  `acchibah` tinyint(4) DEFAULT NULL,
  `accinput` tinyint(4) DEFAULT NULL,
  `accjadwal` tinyint(4) DEFAULT NULL,
  `acckunci` tinyint(4) DEFAULT NULL,
  `accmaster` tinyint(4) DEFAULT NULL,
  `accspv` tinyint(4) DEFAULT NULL,
  `accunit` tinyint(4) DEFAULT NULL,
  `accusulan` tinyint(4) DEFAULT NULL,
  `alamatteks` text,
  `camatteks` varchar(50) DEFAULT NULL,
  `daerahpengusul` varchar(50) DEFAULT NULL,
  `dapil` varchar(50) DEFAULT NULL,
  `emailteks` varchar(50) DEFAULT NULL,
  `fraksi` varchar(50) DEFAULT NULL,
  `idcamat` int(11) DEFAULT NULL,
  `iddaerahpengusul` int(11) DEFAULT NULL,
  `idkabkota` int(11) DEFAULT NULL,
  `idlevel` int(11) DEFAULT NULL,
  `idlokasidesa` int(11) DEFAULT NULL,
  `idlurah` int(11) DEFAULT NULL,
  `idlurahpengusul` int(11) DEFAULT NULL,
  `idprofil` int(11) DEFAULT NULL,
  `iduser` int(11) DEFAULT NULL,
  `loginname` varchar(50) DEFAULT NULL,
  `lokasidesateks` varchar(50) DEFAULT NULL,
  `lurahteks` varchar(50) DEFAULT NULL,
  `nama` varchar(50) DEFAULT NULL,
  `namapengusul` varchar(50) DEFAULT NULL,
  `nik` varchar(50) DEFAULT NULL,
  `nip` varchar(50) DEFAULT NULL,
  `notelp` varchar(50) DEFAULT NULL,
  `npwp` varchar(50) DEFAULT NULL,
  `update_at` datetime NOT NULL,
  `tahun_anggaran` year(4) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- Table structure for table `data_dewan`--

CREATE TABLE `data_dewan` (
  `id` int(11) NOT NULL,
  `jabatan` varchar(50) DEFAULT NULL,
  `accasmas` tinyint(4) DEFAULT NULL,
  `accbankeu` tinyint(4) DEFAULT NULL,
  `accdisposisi` tinyint(4) DEFAULT NULL,
  `accgiat` tinyint(4) DEFAULT NULL,
  `acchibah` tinyint(4) DEFAULT NULL,
  `accinput` tinyint(4) DEFAULT NULL,
  `accjadwal` tinyint(4) DEFAULT NULL,
  `acckunci` tinyint(4) DEFAULT NULL,
  `accmaster` tinyint(4) DEFAULT NULL,
  `accspv` tinyint(4) DEFAULT NULL,
  `accunit` tinyint(4) DEFAULT NULL,
  `accusulan` tinyint(4) DEFAULT NULL,
  `alamatteks` text,
  `camatteks` varchar(50) DEFAULT NULL,
  `daerahpengusul` varchar(50) DEFAULT NULL,
  `dapil` varchar(50) DEFAULT NULL,
  `emailteks` varchar(50) DEFAULT NULL,
  `fraksi` varchar(50) DEFAULT NULL,
  `idcamat` int(11) DEFAULT NULL,
  `iddaerahpengusul` int(11) DEFAULT NULL,
  `idkabkota` int(11) DEFAULT NULL,
  `idlevel` int(11) DEFAULT NULL,
  `idlokasidesa` int(11) DEFAULT NULL,
  `idlurah` int(11) DEFAULT NULL,
  `idlurahpengusul` int(11) DEFAULT NULL,
  `idprofil` int(11) DEFAULT NULL,
  `iduser` int(11) DEFAULT NULL,
  `loginname` varchar(50) DEFAULT NULL,
  `lokasidesateks` varchar(50) DEFAULT NULL,
  `lurahteks` varchar(50) DEFAULT NULL,
  `nama` varchar(50) DEFAULT NULL,
  `namapengusul` varchar(50) DEFAULT NULL,
  `nik` varchar(50) DEFAULT NULL,
  `nip` varchar(50) DEFAULT NULL,
  `notelp` varchar(50) DEFAULT NULL,
  `npwp` varchar(50) DEFAULT NULL,
  `update_at` datetime NOT NULL,
  `tahun_anggaran` year(4) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Table structure for table `data_tag_sub_keg`
--

CREATE TABLE `data_tag_sub_keg` (
  `id` int(11) NOT NULL,
  `idlabelgiat` int(11) DEFAULT NULL,
  `namalabel` varchar(50) DEFAULT NULL,
  `idtagbl` int(11) DEFAULT NULL,
  `kode_sbl` varchar(50) DEFAULT NULL,
  `idsubbl` int(11) DEFAULT NULL,
  `active` tinyint(4) DEFAULT '1',
  `update_at` datetime NOT NULL,
  `tahun_anggaran` year(4) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Table structure for table `data_tag_sub_keg`
--

CREATE TABLE `data_pengaturan_sipd` (
  `id` int(11) NOT NULL,
  `daerah` text DEFAULT NULL,
  `kepala_daerah` text DEFAULT NULL,
  `wakil_kepala_daerah` text DEFAULT NULL,
  `awal_rpjmd` year(4) DEFAULT NULL,
  `akhir_rpjmd` year(4) DEFAULT NULL,
  `pelaksana_rkpd` tinyint(4) DEFAULT NULL,
  `pelaksana_kua` tinyint(4) DEFAULT NULL,
  `pelaksana_apbd` tinyint(4) DEFAULT NULL,
  `set_kpa_sekda` tinyint(4) DEFAULT NULL,
  `update_at` datetime NOT NULL,
  `tahun_anggaran` year(4) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Table structure for table `data_sumber_dana`
--

CREATE TABLE `data_sumber_dana` (
  `id` int(11) NOT NULL,
  `created_at` datetime NOT NULL,
  `created_user` int(11) NOT NULL,
  `id_daerah` int(11) NOT NULL,
  `id_dana` int(11) NOT NULL,
  `id_unik` text NOT NULL,
  `is_locked` int(11) NOT NULL,
  `kode_dana` varchar(50) NOT NULL,
  `nama_dana` text NOT NULL,
  `set_input` varchar(50) NOT NULL,
  `status` varchar(50) NOT NULL,
  `tahun` year(4) NOT NULL DEFAULT '2021',
  `updated_at` datetime DEFAULT NULL,
  `updated_user` int(11) NOT NULL DEFAULT '0',
  `tahun_anggaran` year(4) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Table structure for table `data_user_penatausahaan`
--

CREATE TABLE `data_user_penatausahaan` (
  `id` int(11) NOT NULL,
  `idSkpd` int(11) DEFAULT NULL,
  `namaSkpd` text NOT NULL,
  `kodeSkpd` int(50) DEFAULT NULL,
  `idDaerah` int(11) DEFAULT NULL,
  `userName` text,
  `nip` varchar(50) DEFAULT NULL,
  `fullName` text,
  `nomorHp` int(50) DEFAULT NULL,
  `rank` varchar(50) DEFAULT NULL,
  `npwp` varchar(50) DEFAULT NULL,
  `idJabatan` int(11) DEFAULT NULL,
  `namaJabatan` varchar(50) DEFAULT NULL,
  `idRole` int(11) DEFAULT NULL,
  `order` int(11) DEFAULT NULL,
  `kpa` varchar(50) DEFAULT NULL,
  `bank` text,
  `group` varchar(50) DEFAULT NULL,
  `password` varchar(50) DEFAULT NULL,
  `konfirmasiPassword` varchar(50) DEFAULT NULL,
  `tahun` year(4) NOT NULL,
  `updated_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Table structure for table `data_renstra`
--

CREATE TABLE `data_renstra` (
  `id` int(11) NOT NULL,
  `id_bidang_urusan` int(11) DEFAULT NULL,
  `id_giat` int(11) DEFAULT NULL,
  `id_program` int(11) DEFAULT NULL,
  `id_renstra` int(11) DEFAULT NULL,
  `id_rpjmd` int(11) DEFAULT NULL,
  `id_sub_giat` int(11) DEFAULT NULL,
  `id_unit` int(11) DEFAULT NULL,
  `indikator` text,
  `indikator_sub` text,
  `is_locked` tinyint(4) DEFAULT NULL,
  `kebijakan_teks` text,
  `kode_bidang_urusan` varchar(50) DEFAULT NULL,
  `kode_giat` varchar(50) DEFAULT NULL,
  `kode_program` varchar(50) DEFAULT NULL,
  `kode_skpd` varchar(50) DEFAULT NULL,
  `kode_sub_giat` varchar(50) DEFAULT NULL,
  `misi_teks` text,
  `nama_bidang_urusan` text,
  `nama_giat` text,
  `nama_program` text,
  `nama_skpd` text,
  `nama_sub_giat` text,
  `outcome` text,
  `pagu_1` double DEFAULT NULL,
  `pagu_2` double DEFAULT NULL,
  `pagu_3` double DEFAULT NULL,
  `pagu_4` double DEFAULT NULL,
  `pagu_5` double DEFAULT NULL,
  `pagu_sub_1` double DEFAULT NULL,
  `pagu_sub_2` double DEFAULT NULL,
  `pagu_sub_3` double DEFAULT NULL,
  `pagu_sub_4` double DEFAULT NULL,
  `pagu_sub_5` double DEFAULT NULL,
  `sasaran_teks` text,
  `satuan` varchar(50) DEFAULT NULL,
  `satuan_sub` varchar(50) DEFAULT NULL,
  `strategi_teks` text,
  `target_1` text,
  `target_2` text,
  `target_3` text,
  `target_4` text,
  `target_5` text,
  `target_sub_1` text,
  `target_sub_2` text,
  `target_sub_3` text,
  `target_sub_4` text,
  `target_sub_5` text,
  `tujuan_teks` text,
  `visi_teks` text,
  `active` tinyint(4) NOT NULL DEFAULT '1',
  `update_at` datetime NOT NULL,
  `tahun_anggaran` year(4) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Table structure for table `data_lokasi_sub_keg`
--

CREATE TABLE `data_lokasi_sub_keg` (
  `id` int(11) NOT NULL,
  `camatteks` text,
  `daerahteks` text,
  `idcamat` int(11) DEFAULT NULL,
  `iddetillokasi` double DEFAULT NULL,
  `idkabkota` int(11) DEFAULT NULL,
  `idlurah` int(11) DEFAULT NULL,
  `lurahteks` text,
  `kode_sbl` varchar(50) DEFAULT NULL,
  `idsubbl` int(11) DEFAULT NULL,
  `active` tinyint(4) DEFAULT NULL,
  `update_at` datetime NOT NULL,
  `tahun_anggaran` year(4) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Table structure for table `data_anggaran_kas`
--

CREATE TABLE `data_anggaran_kas` (
  `id` int(11) NOT NULL,
  `bulan_1` double DEFAULT NULL,
  `bulan_2` double DEFAULT NULL,
  `bulan_3` double DEFAULT NULL,
  `bulan_4` double DEFAULT NULL,
  `bulan_5` double DEFAULT NULL,
  `bulan_6` double DEFAULT NULL,
  `bulan_7` double DEFAULT NULL,
  `bulan_8` double DEFAULT NULL,
  `bulan_9` double DEFAULT NULL,
  `bulan_10` double DEFAULT NULL,
  `bulan_11` double DEFAULT NULL,
  `bulan_12` double DEFAULT NULL,
  `id_akun` int(11) DEFAULT NULL,
  `id_bidang_urusan` int(11) DEFAULT NULL,
  `id_daerah` int(11) DEFAULT NULL,
  `id_giat` int(11) DEFAULT NULL,
  `id_program` int(11) DEFAULT NULL,
  `id_skpd` int(11) DEFAULT NULL,
  `id_sub_giat` int(11) DEFAULT NULL,
  `id_sub_skpd` int(11) DEFAULT NULL,
  `id_unit` int(11) DEFAULT NULL,
  `kode_akun` varchar(50) DEFAULT NULL,
  `nama_akun` text,
  `selisih` double DEFAULT NULL,
  `tahun` year(4) DEFAULT NULL,
  `total_akb` double DEFAULT NULL,
  `total_rincian` double DEFAULT NULL,
  `active` tinyint(4) DEFAULT NULL,
  `kode_sbl` varchar(50) DEFAULT NULL,
  `type` varchar(50) DEFAULT NULL,
  `tahun_anggaran` year(4) NOT NULL,
  `updated_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Table structure for table `data_pembiayaan`
--

CREATE TABLE `data_pembiayaan` (
  `id` int(11) NOT NULL,
  `created_user` int(11) DEFAULT NULL,
  `createddate` varchar(50) DEFAULT NULL,
  `createdtime` varchar(50) DEFAULT NULL,
  `id_pembiayaan` int(11) DEFAULT NULL,
  `keterangan` varchar(50) DEFAULT NULL,
  `kode_akun` varchar(50) DEFAULT NULL,
  `nama_akun` text,
  `nilaimurni` int(11) DEFAULT NULL,
  `program_koordinator` int(11) DEFAULT NULL,
  `rekening` text,
  `skpd_koordinator` int(11) DEFAULT NULL,
  `total` double DEFAULT NULL,
  `updated_user` int(11) DEFAULT NULL,
  `updateddate` varchar(50) DEFAULT NULL,
  `updatedtime` varchar(50) DEFAULT NULL,
  `uraian` text,
  `urusan_koordinator` int(11) DEFAULT NULL,
  `type` varchar(50) DEFAULT NULL,
  `user1` varchar(50) DEFAULT NULL,
  `user2` varchar(50) DEFAULT NULL,
  `id_skpd` int(11) DEFAULT NULL,
  `active` tinyint(4) NOT NULL,
  `update_at` datetime NOT NULL,
  `tahun_anggaran` year(4) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Table structure for table `data_pendapatan`
--

CREATE TABLE `data_pendapatan` (
  `id` int(11) NOT NULL,
  `created_user` int(11) DEFAULT NULL,
  `createddate` varchar(50) DEFAULT NULL,
  `createdtime` varchar(50) DEFAULT NULL,
  `id_pendapatan` int(11) DEFAULT NULL,
  `keterangan` text,
  `kode_akun` varchar(50) DEFAULT NULL,
  `nama_akun` text,
  `nilaimurni` int(11) DEFAULT NULL,
  `program_koordinator` int(11) DEFAULT NULL,
  `rekening` text,
  `skpd_koordinator` int(11) DEFAULT NULL,
  `total` double DEFAULT NULL,
  `updated_user` int(11) DEFAULT NULL,
  `updateddate` varchar(50) DEFAULT NULL,
  `updatedtime` varchar(50) DEFAULT NULL,
  `uraian` text,
  `urusan_koordinator` int(11) DEFAULT NULL,
  `user1` varchar(50) DEFAULT NULL,
  `user2` varchar(50) DEFAULT NULL,
  `id_skpd` int(11) DEFAULT NULL,
  `active` tinyint(4) NOT NULL,
  `update_at` datetime NOT NULL,
  `tahun_anggaran` year(4) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- view untuk data bantuan hibah uang --

CREATE VIEW vw_batuan_hibah_uang AS SELECT
a.kode_akun,
a.nama_akun,
r.subs_bl_teks,
r.ket_bl_teks,
r.lokus_akun_teks,
r.nama_komponen,
r.koefisien,
r.satuan,
r.harga_satuan,
r.rincian,
r.kode_sbl,
r.update_at,
r.tahun_anggaran,
al.nama AS deskel,
(SELECT nama from data_alamat where id_alamat=al.id_kec) AS kecamatan,
(SELECT nama from data_alamat where id_alamat=al.id_kab) AS kabupaten,
(SELECT nama from data_alamat where id_alamat=al.id_prov) AS provinsi,
p.nama_teks AS nama_penerima,
p.alamat_teks AS alamat_penerima,
p.jenis_penerima
FROM data_akun a
INNER JOIN data_rka r ON a.kode_akun=r.kode_akun
LEFT JOIN data_alamat al ON r.id_lurah_penerima=al.id_alamat
LEFT JOIN data_profile_penerima_bantuan p ON r.id_penerima=p.id_profil
WHERE a.is_hibah_uang=1;


--
-- Indexes for dumped tables
--

--
-- Indexes for table `data_pendapatan`
--
ALTER TABLE `data_pendapatan`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `data_pembiayaan`
--
ALTER TABLE `data_pembiayaan`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `data_anggaran_kas`
--
ALTER TABLE `data_anggaran_kas`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `data_lokasi_sub_keg`
--
ALTER TABLE `data_lokasi_sub_keg`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `data_renstra`
--
ALTER TABLE `data_renstra`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `data_keg_indikator_hasil`
--
ALTER TABLE `data_keg_indikator_hasil`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `data_user_penatausahaan`
--
ALTER TABLE `data_user_penatausahaan`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `data_capaian_prog_sub_keg`
--
ALTER TABLE `data_capaian_prog_sub_keg`
  ADD PRIMARY KEY (`id`);
--
-- Indexes for table `data_output_giat_sub_keg`
--
ALTER TABLE `data_output_giat_sub_keg`
  ADD PRIMARY KEY (`id`);
--
-- Indexes for table `data_dana_sub_keg`
--
ALTER TABLE `data_dana_sub_keg`
  ADD PRIMARY KEY (`id`);
--
-- Indexes for table `data_sumber_dana`
--
ALTER TABLE `data_sumber_dana`
  ADD PRIMARY KEY (`id`);
--
-- Indexes for table `data_tag_sub_keg`
--
ALTER TABLE `data_tag_sub_keg`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `data_pengaturan_sipd`
--
ALTER TABLE `data_pengaturan_sipd`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `data_dewan`
--
ALTER TABLE `data_dewan`
  ADD PRIMARY KEY (`id`);
  
--
-- Indexes for table `data_desa_kelurahan`
--
ALTER TABLE `data_desa_kelurahan`
  ADD PRIMARY KEY (`id`);
--
-- Indexes for table `data_alamat`
--
ALTER TABLE `data_alamat`
  ADD PRIMARY KEY (`id`);
--
-- Indexes for table `data_profile_penerima_bantuan`
--
ALTER TABLE `data_profile_penerima_bantuan`
  ADD PRIMARY KEY (`id`);
--
-- Indexes for table `data_prog_keg`
--
ALTER TABLE `data_rpjmd`
ADD PRIMARY KEY (`id`);
--
-- Indexes for table `data_prog_keg`
--
ALTER TABLE `data_prog_keg`
ADD PRIMARY KEY (`id`);
--
-- Indexes for table `data_unit`
--
ALTER TABLE `data_unit`
ADD PRIMARY KEY (`id`);
--
-- Indexes for table `data_akun`
--
ALTER TABLE `data_akun`
ADD PRIMARY KEY (`id`);
--
-- Indexes for table `data_rka`
--
ALTER TABLE `data_rka`
ADD PRIMARY KEY (`id`);
--
-- Indexes for table `data_ssh`
--
ALTER TABLE `data_ssh`
ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `id_standar_harga` (`id_standar_harga`);
--
-- Indexes for table `data_ssh_rek_belanja`
--
ALTER TABLE `data_ssh_rek_belanja`
ADD PRIMARY KEY (`id`),
  ADD KEY `id_standar_harga` (`id_standar_harga`);
--
-- Indexes for table `data_sub_keg_bl`
--
ALTER TABLE `data_sub_keg_bl`
ADD PRIMARY KEY (`id`);
--
-- Indexes for table `data_sub_keg_indikator`
--
ALTER TABLE `data_sub_keg_indikator`
ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `data_desa_kelurahan`
--
ALTER TABLE `data_desa_kelurahan`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `data_unit`
--
ALTER TABLE `data_unit`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `data_akun`
--
ALTER TABLE `data_akun`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `data_rka`
--
ALTER TABLE `data_rka`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `data_ssh`
--
ALTER TABLE `data_ssh`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `data_ssh_rek_belanja`
--
ALTER TABLE `data_ssh_rek_belanja`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `data_sub_keg_bl`
--
ALTER TABLE `data_sub_keg_bl`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `data_sub_keg_indikator`
--
ALTER TABLE `data_sub_keg_indikator`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `data_ssh_rek_belanja`
--
ALTER TABLE `data_ssh_rek_belanja`
ADD CONSTRAINT `data_ssh_rek_belanja_ibfk_1` FOREIGN KEY (`id_standar_harga`) REFERENCES `data_ssh` (`id_standar_harga`);
--
-- Indexes for table `data_unit_pagu`
--
ALTER TABLE `data_unit_pagu`
ADD PRIMARY KEY (`id`);
--
-- AUTO_INCREMENT for table `data_unit_pagu`
--
ALTER TABLE `data_unit_pagu`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `data_prog_keg`
--
ALTER TABLE `data_prog_keg`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `data_prog_keg`
--
ALTER TABLE `data_rpjmd`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `data_profile_penerima_bantuan`
--
ALTER TABLE `data_profile_penerima_bantuan`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `data_alamat`
--
ALTER TABLE `data_alamat`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `data_dewan`
--
ALTER TABLE `data_dewan`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `data_tag_sub_keg`
--
ALTER TABLE `data_tag_sub_keg`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `data_pengaturan_sipd`
--
ALTER TABLE `data_pengaturan_sipd`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `data_sumber_dana`
--
ALTER TABLE `data_sumber_dana`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `data_dana_sub_keg`
--
ALTER TABLE `data_dana_sub_keg`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `data_output_giat_sub_keg`
--
ALTER TABLE `data_output_giat_sub_keg`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `data_capaian_prog_sub_keg`
--
ALTER TABLE `data_capaian_prog_sub_keg`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `data_user_penatausahaan`
--
ALTER TABLE `data_user_penatausahaan`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `data_keg_indikator_hasil`
--
ALTER TABLE `data_keg_indikator_hasil`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `data_renstra`
--
ALTER TABLE `data_renstra`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `data_lokasi_sub_keg`
--
ALTER TABLE `data_lokasi_sub_keg`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `data_anggaran_kas`
--
ALTER TABLE `data_anggaran_kas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `data_pembiayaan`
--
ALTER TABLE `data_pembiayaan`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `data_pendapatan`
--
ALTER TABLE `data_pendapatan`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;