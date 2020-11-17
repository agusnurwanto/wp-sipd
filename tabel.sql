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
  `namakepala` text NOT NULL,
  `namaunit` text NOT NULL,
  `nipbendahara` varchar(30) DEFAULT NULL,
  `nipkepala` varchar(30) NOT NULL,
  `pangkatkepala` varchar(50) NOT NULL,
  `setupunit` int(11) NOT NULL,
  `statuskepala` varchar(20) NOT NULL,
  `update_at` datetime NOT NULL,
  `tahun_anggaran` year(4) NOT NULL DEFAULT '2021'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

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
  `update_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `tahun_anggaran` year(4) NOT NULL DEFAULT '2021'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `data_rka`
--

CREATE TABLE `data_rka` (
  `id` int(11) NOT NULL,
  `created_user` int(11) DEFAULT NULL,
  `createddate` varchar(10) DEFAULT NULL,
  `createdtime` varchar(10) DEFAULT NULL,
  `harga_satuan` int(11) NOT NULL,
  `id_daerah` int(11) NOT NULL,
  `id_rinci_sub_bl` int(11) NOT NULL,
  `id_standar_nfs` tinyint(4) DEFAULT NULL,
  `is_locked` tinyint(4) DEFAULT NULL,
  `jenis_bl` varchar(50) NOT NULL,
  `ket_bl_teks` text NOT NULL,
  `kode_akun` varchar(50) NOT NULL,
  `koefisien` text NOT NULL,
  `lokus_akun_teks` text NOT NULL,
  `nama_akun` text NOT NULL,
  `nama_komponen` text NOT NULL,
  `spek_komponen` text NOT NULL,
  `satuan` varchar(50) NOT NULL,
  `spek` text NOT NULL,
  `subs_bl_teks` text NOT NULL,
  `total_harga` int(11) DEFAULT NULL,
  `totalpajak` int(11) NOT NULL,
  `updated_user` int(11) DEFAULT NULL,
  `updateddate` varchar(20) DEFAULT NULL,
  `updatedtime` varchar(20) DEFAULT NULL,
  `user1` varchar(50) DEFAULT NULL,
  `user2` varchar(50) DEFAULT NULL,
  `update_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `tahun_anggaran` year(4) NOT NULL DEFAULT '2021',
  `idbl` int(11) DEFAULT NULL,
  `idsubbl` int(11) DEFAULT NULL,
  `kode_bl` varchar(50) NOT NULL,
  `kode_sbl` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

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
  `harga` int(11) NOT NULL,
  `kode_kel_standar_harga` varchar(30) NOT NULL,
  `nama_kel_standar_harga` text NOT NULL,
  `update_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `tahun_anggaran` year(4) NOT NULL DEFAULT '2020'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `data_ssh_rek_belanja`
--

CREATE TABLE `data_ssh_rek_belanja` (
  `id` int(11) NOT NULL,
  `id_akun` int(11) NOT NULL,
  `kode_akun` varchar(50) NOT NULL,
  `nama_akun` text NOT NULL,
  `id_standar_harga` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

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
  `pagu_n_lalu` int(11) DEFAULT NULL,
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
  `pagu` int(11) NOT NULL,
  `output_sub_giat` text,
  `sasaran` text,
  `indikator` text,
  `id_dana` int(11) DEFAULT NULL,
  `nama_sub_giat` text NOT NULL,
  `pagu_n_depan` int(11) NOT NULL,
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
  `pagu_keg` int(11) NOT NULL,
  `id_bl` int(11) DEFAULT NULL,
  `kode_bl` varchar(50) NOT NULL,
  `kode_sbl` varchar(50) NOT NULL,
  `update_at` datetime NOT NULL,
  `tahun_anggaran` year(4) NOT NULL DEFAULT '2021'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

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
  `update_at` datetime NOT NULL,
  `tahun_anggaran` year(4) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `data_unit_pagu`
--

CREATE TABLE `data_unit_pagu` (
  `id` int(11) NOT NULL,
  `batasanpagu` int(11) NOT NULL,
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
  `nilaipagu` int(11) NOT NULL,
  `nilaipagumurni` int(11) NOT NULL,
  `nilairincian` int(11) NOT NULL,
  `pagu_giat` int(11) NOT NULL,
  `realisasi` int(11) NOT NULL,
  `rinci_giat` int(11) NOT NULL,
  `set_pagu_giat` int(11) NOT NULL,
  `set_pagu_skpd` int(11) NOT NULL,
  `tahun` int(11) NOT NULL,
  `total_giat` int(11) NOT NULL,
  `totalgiat` int(11) NOT NULL,
  `update_at` datetime NOT NULL,
  `tahun_anggaran` year(4) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

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
  `update_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `tahun_anggaran` year(4) NOT NULL DEFAULT '2021'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Indexes for dumped tables
--

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