--
-- Table structure for table `data_sub_keg_bl_history`
--

CREATE TABLE `data_sub_keg_bl_history` (
  `id` int(11) NOT NULL,
  `id_sub_skpd` int(11) NOT NULL,
  `id_lokasi` int(11) DEFAULT NULL,
  `id_label_kokab` int(11) NOT NULL,
  `nama_dana` text CHARACTER SET latin1,
  `no_sub_giat` varchar(20) CHARACTER SET latin1 NOT NULL,
  `kode_giat` varchar(50) CHARACTER SET latin1 NOT NULL,
  `id_program` int(11) NOT NULL,
  `nama_lokasi` text CHARACTER SET latin1,
  `waktu_akhir` int(11) NOT NULL,
  `pagu_n_lalu` double(20,0) DEFAULT NULL,
  `id_urusan` int(11) NOT NULL,
  `id_unik_sub_bl` text CHARACTER SET latin1 NOT NULL,
  `id_sub_giat` int(11) NOT NULL,
  `label_prov` text CHARACTER SET latin1,
  `kode_program` varchar(50) CHARACTER SET latin1 NOT NULL,
  `kode_sub_giat` varchar(50) CHARACTER SET latin1 NOT NULL,
  `no_program` varchar(20) CHARACTER SET latin1 NOT NULL,
  `kode_urusan` varchar(20) CHARACTER SET latin1 NOT NULL,
  `kode_bidang_urusan` varchar(20) CHARACTER SET latin1 NOT NULL,
  `nama_program` text CHARACTER SET latin1 NOT NULL,
  `target_4` text CHARACTER SET latin1,
  `target_5` text CHARACTER SET latin1,
  `id_bidang_urusan` int(11) DEFAULT NULL,
  `nama_bidang_urusan` text CHARACTER SET latin1,
  `target_3` text CHARACTER SET latin1,
  `no_giat` varchar(50) CHARACTER SET latin1 NOT NULL,
  `id_label_prov` int(11) NOT NULL,
  `waktu_awal` int(11) NOT NULL,
  `pagumurni` double(20,0) DEFAULT NULL,
  `pagu` double(20,0) NOT NULL,
  `pagu_simda` double(20,0) DEFAULT NULL,
  `output_sub_giat` text CHARACTER SET latin1,
  `sasaran` text CHARACTER SET latin1,
  `indikator` text CHARACTER SET latin1,
  `id_dana` int(11) DEFAULT NULL,
  `nama_sub_giat` text CHARACTER SET latin1 NOT NULL,
  `pagu_n_depan` double(20,0) NOT NULL,
  `satuan` text CHARACTER SET latin1,
  `id_rpjmd` int(11) NOT NULL,
  `id_giat` int(11) NOT NULL,
  `id_label_pusat` int(11) NOT NULL,
  `nama_giat` text CHARACTER SET latin1 NOT NULL,
  `kode_skpd` varchar(50) CHARACTER SET latin1 NOT NULL,
  `nama_skpd` text CHARACTER SET latin1 NOT NULL,
  `kode_sub_skpd` varchar(50) CHARACTER SET latin1 NOT NULL,
  `id_skpd` int(11) NOT NULL,
  `id_sub_bl` int(11) DEFAULT NULL,
  `nama_sub_skpd` text CHARACTER SET latin1 NOT NULL,
  `target_1` text CHARACTER SET latin1,
  `nama_urusan` text CHARACTER SET latin1 NOT NULL,
  `target_2` text CHARACTER SET latin1,
  `label_kokab` text CHARACTER SET latin1,
  `label_pusat` text CHARACTER SET latin1,
  `pagu_keg` double(20,0) NOT NULL,
  `id_bl` int(11) DEFAULT NULL,
  `kode_bl` varchar(50) CHARACTER SET latin1 NOT NULL,
  `kode_sbl` varchar(50) CHARACTER SET latin1 NOT NULL,
  `active` tinyint(4) DEFAULT '1',
  `update_at` datetime NOT NULL,
  `tahun_anggaran` year(4) NOT NULL DEFAULT '2021',
  `id_data_sub_keg_bl` int(11) NOT NULL,
  `id_local_schedule` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf32;


--
-- Indexes for table `data_sub_keg_bl_history`
--
ALTER TABLE `data_sub_keg_bl_history`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for table `data_sub_keg_bl_history`
--
ALTER TABLE `data_sub_keg_bl_history`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Table structure for table `data_sub_keg_indikator_history`
--

CREATE TABLE `data_sub_keg_indikator_history` (
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
  `tahun_anggaran` year(4) NOT NULL,
  `id_data_sub_keg_indikator` int(11) NOT NULL,
  `id_local_schedule` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Indexes for table `data_sub_keg_indikator_history`
--
ALTER TABLE `data_sub_keg_indikator_history`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for table `data_sub_keg_indikator_history`
--
ALTER TABLE `data_sub_keg_indikator_history`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Table structure for table `data_keg_indikator_hasil_history`
--

CREATE TABLE `data_keg_indikator_hasil_history` (
  `id` int(11) NOT NULL,
  `hasilteks` text,
  `satuanhasil` varchar(50) DEFAULT NULL,
  `targethasil` varchar(50) DEFAULT NULL,
  `targethasilteks` varchar(50) DEFAULT NULL,
  `kode_sbl` varchar(50) DEFAULT NULL,
  `idsubbl` varchar(50) DEFAULT NULL,
  `active` tinyint(4) DEFAULT '1',
  `update_at` datetime NOT NULL,
  `tahun_anggaran` year(4) NOT NULL,
  `id_data_keg_indikator_hasil` int(11) NOT NULL,
  `id_local_schedule` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Indexes for table `data_keg_indikator_hasil_history`
--
ALTER TABLE `data_keg_indikator_hasil_history`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for table `data_keg_indikator_hasil_history`
--
ALTER TABLE `data_keg_indikator_hasil_history`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Table structure for table `data_tag_sub_keg_history`
--

CREATE TABLE `data_tag_sub_keg_history` (
  `id` int(11) NOT NULL,
  `idlabelgiat` int(11) DEFAULT NULL,
  `namalabel` varchar(50) DEFAULT NULL,
  `idtagbl` int(11) DEFAULT NULL,
  `kode_sbl` varchar(50) DEFAULT NULL,
  `idsubbl` int(11) DEFAULT NULL,
  `active` tinyint(4) DEFAULT '1',
  `update_at` datetime NOT NULL,
  `tahun_anggaran` year(4) NOT NULL,
  `id_data_tag_sub_keg` int(11) NOT NULL,
  `id_local_schedule` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Indexes for table `data_tag_sub_keg_history`
--
ALTER TABLE `data_tag_sub_keg_history`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for table `data_tag_sub_keg_history`
--
ALTER TABLE `data_tag_sub_keg_history`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Table structure for table `data_capaian_prog_sub_keg_history`
--

CREATE TABLE `data_capaian_prog_sub_keg_history` (
  `id` int(11) NOT NULL,
  `satuancapaian` varchar(50) DEFAULT NULL,
  `targetcapaianteks` varchar(50) DEFAULT NULL,
  `capaianteks` text,
  `targetcapaian` int(11) DEFAULT NULL,
  `kode_sbl` varchar(50) DEFAULT NULL,
  `idsubbl` int(11) DEFAULT NULL,
  `active` tinyint(4) DEFAULT '1',
  `update_at` datetime NOT NULL,
  `tahun_anggaran` year(4) NOT NULL,
  `id_data_capaian_prog_sub_keg` int(11) NOT NULL,
  `id_local_schedule` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Indexes for table `data_capaian_prog_sub_keg_history`
--
ALTER TABLE `data_capaian_prog_sub_keg_history`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for table `data_capaian_prog_sub_keg_history`
--
ALTER TABLE `data_capaian_prog_sub_keg_history`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Table structure for table `data_output_giat_sub_keg_history`
--

CREATE TABLE `data_output_giat_sub_keg_history` (
  `id` int(11) NOT NULL,
  `outputteks` text,
  `satuanoutput` varchar(50) DEFAULT NULL,
  `targetoutput` int(11) DEFAULT NULL,
  `targetoutputteks` varchar(50) DEFAULT NULL,
  `kode_sbl` varchar(50) DEFAULT NULL,
  `idsubbl` int(11) DEFAULT NULL,
  `active` tinyint(4) DEFAULT '1',
  `update_at` datetime NOT NULL,
  `tahun_anggaran` year(4) NOT NULL,
  `id_data_output_giat_sub_keg` int(11) NOT NULL,
  `id_local_schedule` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Indexes for table `data_output_giat_sub_keg_history`
--
ALTER TABLE `data_output_giat_sub_keg_history`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for table `data_output_giat_sub_keg_history`
--
ALTER TABLE `data_output_giat_sub_keg_history`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Table structure for table `data_dana_sub_keg_history`
--

CREATE TABLE `data_dana_sub_keg_history` (
  `id` int(11) NOT NULL,
  `namadana` text,
  `kodedana` varchar(50) DEFAULT NULL,
  `iddana` int(11) DEFAULT NULL,
  `iddanasubbl` int(11) DEFAULT NULL,
  `pagudana` double(20,0) DEFAULT NULL,
  `kode_sbl` varchar(50) DEFAULT NULL,
  `idsubbl` int(11) DEFAULT NULL,
  `active` tinyint(4) DEFAULT '1',
  `update_at` datetime NOT NULL,
  `tahun_anggaran` year(4) NOT NULL,
  `id_data_dana_sub_keg` int(11) NOT NULL,
  `id_local_schedule` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Indexes for table `data_dana_sub_keg_history`
--
ALTER TABLE `data_dana_sub_keg_history`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for table `data_dana_sub_keg_history`
--
ALTER TABLE `data_dana_sub_keg_history`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Table structure for table `data_lokasi_sub_keg_history`
--

CREATE TABLE `data_lokasi_sub_keg_history` (
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
  `tahun_anggaran` year(4) NOT NULL,
  `id_data_lokasi_sub_keg` int(11) NOT NULL,
  `id_local_schedule` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Indexes for table `data_lokasi_sub_keg_history`
--
ALTER TABLE `data_lokasi_sub_keg_history`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for table `data_lokasi_sub_keg_history`
--
ALTER TABLE `data_lokasi_sub_keg_history`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Table structure for table `data_mapping_sumberdana_history`
--

CREATE TABLE `data_mapping_sumberdana_history` (
  `id` int(11) NOT NULL,
  `id_rinci_sub_bl` int(11) NOT NULL,
  `id_sumber_dana` int(11) NOT NULL,
  `user` text,
  `active` tinyint(4) NOT NULL,
  `update_at` datetime NOT NULL,
  `tahun_anggaran` year(4) NOT NULL,
  `id_data_mapping_sumberdana` int(11) NOT NULL,
  `id_local_schedule` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Indexes for table `data_mapping_sumberdana_history`
--
ALTER TABLE `data_mapping_sumberdana_history`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for table `data_mapping_sumberdana_history`
--
ALTER TABLE `data_mapping_sumberdana_history`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
