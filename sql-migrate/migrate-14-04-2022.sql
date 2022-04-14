CREATE TABLE `data_spd` (
  `id` int(11) NOT NULL,
  `no_spd` text NOT NULL,
  `uraian` varchar(64) DEFAULT NULL,
  `id_skpd_sipd` int(11) DEFAULT NULL,
  `kode_skpd_simda` varchar(50) DEFAULT NULL,
  `id_skpd_fmis` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `active` tinyint(2) NOT NULL DEFAULT '1',
  `tahun_anggaran` year(4) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Indexes for table `data_spd`
--
ALTER TABLE `data_spd`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for table `data_spd`
--
ALTER TABLE `data_spd`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

CREATE TABLE `data_spd_rinci` (
  `id` int(11) NOT NULL,
  `no_spd` text NOT NULL,
  `idrefaktivitas` int(11) DEFAULT NULL,
  `idsubunit` int(11) DEFAULT NULL,
  `kdrek1` tinyint(5) DEFAULT NULL,
  `kdrek2` tinyint(5) DEFAULT NULL,
  `kdrek3` tinyint(5) DEFAULT NULL,
  `kdrek4` tinyint(5) DEFAULT NULL,
  `kdrek5` tinyint(5) DEFAULT NULL,
  `kdrek6` tinyint(5) DEFAULT NULL,
  `nilai` double (20, 0) DEFAULT NULL,
  `rekening` text DEFAULT NULL,
  `aktivitas_uraian` text DEFAULT NULL,
  `subkegiatan` text DEFAULT NULL,
  `kode_akun` text DEFAULT NULL,
  `nama_sub_giat` text DEFAULT NULL,
  `nama_giat` text DEFAULT NULL,
  `nama_program` text DEFAULT NULL,
  `kd_urusan` tinyint(5) DEFAULT NULL,
  `kd_unit` tinyint(5) DEFAULT NULL,
  `kd_bidang` tinyint(5) DEFAULT NULL,
  `kd_prog` tinyint(5) DEFAULT NULL,
  `kd_keg` tinyint(5) DEFAULT NULL,
  `id_prog` tinyint(5) DEFAULT NULL,
  `active` tinyint(2) NOT NULL DEFAULT '1',
  `tahun_anggaran` year(4) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Indexes for table `data_spd_rinci`
--
ALTER TABLE `data_spd_rinci`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for table `data_spd_rinci`
--
ALTER TABLE `data_spd_rinci`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;