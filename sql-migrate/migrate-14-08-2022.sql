CREATE TABLE `data_rpjmd_visi_lokal` (
  `id` int(11) NOT NULL,
  `id_visi` int(11) DEFAULT NULL,
  `is_locked` tinyint(2) DEFAULT NULL,
  `status` text DEFAULT NULL,
  `visi_teks` text DEFAULT NULL,
  `update_at` datetime NOT NULL,
  `active` tinyint(4) NOT NULL,
  `tahun_anggaran` year(4) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
ALTER TABLE `data_rpjmd_visi_lokal`
  ADD PRIMARY KEY (`id`);
ALTER TABLE `data_rpjmd_visi_lokal`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

CREATE TABLE `data_rpjmd_misi_lokal` (
  `id` int(11) NOT NULL,
  `id_misi` int(11) DEFAULT NULL,
  `id_misi_old` int(11) DEFAULT NULL,
  `id_visi` int(11) DEFAULT NULL,
  `is_locked` tinyint(2) DEFAULT NULL,
  `misi_teks` text DEFAULT NULL,
  `status` text DEFAULT NULL,
  `urut_misi` tinyint(2) DEFAULT NULL,
  `visi_lock` tinyint(2) DEFAULT NULL,
  `visi_teks` text DEFAULT NULL,
  `update_at` datetime NOT NULL,
  `active` tinyint(4) NOT NULL,
  `tahun_anggaran` year(4) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
ALTER TABLE `data_rpjmd_misi_lokal`
  ADD PRIMARY KEY (`id`);
ALTER TABLE `data_rpjmd_misi_lokal`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

CREATE TABLE `data_rpjmd_tujuan_lokal` (
  `id` int(11) NOT NULL,
  `id_misi` int(11) DEFAULT NULL,
  `id_misi_old` int(11) DEFAULT NULL,
  `id_tujuan` int(11) DEFAULT NULL,
  `id_unik` text DEFAULT NULL,
  `id_unik_indikator` text DEFAULT NULL,
  `id_visi` int(11) DEFAULT NULL,
  `indikator_teks` text DEFAULT NULL,
  `is_locked` tinyint(4) DEFAULT NULL,
  `is_locked_indikator` tinyint(4) DEFAULT NULL,
  `misi_lock` tinyint(4) DEFAULT NULL,
  `misi_teks` text DEFAULT NULL,
  `satuan` text DEFAULT NULL,
  `status` text DEFAULT NULL,
  `target_1` text DEFAULT NULL,
  `target_2` text DEFAULT NULL,
  `target_3` text DEFAULT NULL,
  `target_4` text DEFAULT NULL,
  `target_5` text DEFAULT NULL,
  `target_akhir` text DEFAULT NULL,
  `target_awal` text DEFAULT NULL,
  `tujuan_teks` text DEFAULT NULL,
  `urut_misi` int(11) DEFAULT NULL,
  `urut_tujuan` int(11) DEFAULT NULL,
  `visi_teks` text DEFAULT NULL,
  `active` tinyint(4) NOT NULL,
  `update_at` datetime NOT NULL,
  `tahun_anggaran` year(4) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
ALTER TABLE `data_rpjmd_tujuan_lokal`
  ADD PRIMARY KEY (`id`);
ALTER TABLE `data_rpjmd_tujuan_lokal`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;


CREATE TABLE `data_rpjmd_sasaran_lokal` (
  `id` int(11) NOT NULL,
  `id_misi` int(11) DEFAULT NULL,
  `id_misi_old` int(11) DEFAULT NULL,
  `id_sasaran` int(11) DEFAULT NULL,
  `id_unik` text DEFAULT NULL,
  `id_unik_indikator` text DEFAULT NULL,
  `id_visi` int(11) DEFAULT NULL,
  `indikator_teks` text DEFAULT NULL,
  `is_locked` tinyint(4) DEFAULT NULL,
  `is_locked_indikator` tinyint(4) DEFAULT NULL,
  `kode_tujuan` text DEFAULT NULL,
  `misi_teks` text DEFAULT NULL,
  `sasaran_teks` text DEFAULT NULL,
  `satuan` text DEFAULT NULL,
  `status` text DEFAULT NULL,
  `target_1` text DEFAULT NULL,
  `target_2` text DEFAULT NULL,
  `target_3` text DEFAULT NULL,
  `target_4` text DEFAULT NULL,
  `target_5` text DEFAULT NULL,
  `target_akhir` text DEFAULT NULL,
  `target_awal` text DEFAULT NULL,
  `tujuan_lock` tinyint(4) DEFAULT NULL,
  `tujuan_teks` text DEFAULT NULL,
  `urut_misi` int(11) DEFAULT NULL,
  `urut_sasaran` int(11) DEFAULT NULL,
  `urut_tujuan` int(11) DEFAULT NULL,
  `visi_teks` text DEFAULT NULL,
  `active` tinyint(4) NOT NULL,
  `update_at` datetime NOT NULL,
  `tahun_anggaran` year(4) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
ALTER TABLE `data_rpjmd_sasaran_lokal`
  ADD PRIMARY KEY (`id`);
ALTER TABLE `data_rpjmd_sasaran_lokal`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;


CREATE TABLE `data_rpjmd_program_lokal` (
  `id` int(11) NOT NULL,
  `id_misi` int(11) DEFAULT NULL,
  `id_misi_old` int(11) DEFAULT NULL,
  `id_program` int(11) DEFAULT NULL,
  `id_unik` text DEFAULT NULL,
  `id_unik_indikator` text DEFAULT NULL,
  `id_unit` int(11) DEFAULT NULL,
  `id_visi` int(11) DEFAULT NULL,
  `indikator` text DEFAULT NULL,
  `is_locked` tinyint(4) DEFAULT NULL,
  `is_locked_indikator` tinyint(4) DEFAULT NULL,
  `kode_sasaran` text DEFAULT NULL,
  `kode_skpd` text DEFAULT NULL,
  `kode_tujuan` text DEFAULT NULL,
  `misi_teks` text DEFAULT NULL,
  `nama_program` text DEFAULT NULL,
  `nama_skpd` text DEFAULT NULL,
  `pagu_1` double(20,0) DEFAULT NULL,
  `pagu_2` double(20,0) DEFAULT NULL,
  `pagu_3` double(20,0) DEFAULT NULL,
  `pagu_4` double(20,0) DEFAULT NULL,
  `pagu_5` double(20,0) DEFAULT NULL,
  `program_lock` tinyint(4) DEFAULT NULL,
  `sasaran_lock` tinyint(4) DEFAULT NULL,
  `sasaran_teks` text DEFAULT NULL,
  `satuan` text DEFAULT NULL,
  `status` text DEFAULT NULL,
  `target_1` text DEFAULT NULL,
  `target_2` text DEFAULT NULL,
  `target_3` text DEFAULT NULL,
  `target_4` text DEFAULT NULL,
  `target_5` text DEFAULT NULL,
  `target_akhir` text DEFAULT NULL,
  `target_awal` text DEFAULT NULL,
  `tujuan_lock` tinyint(4) DEFAULT NULL,
  `tujuan_teks` text DEFAULT NULL,
  `urut_misi` int(11) DEFAULT NULL,
  `urut_sasaran` int(11) DEFAULT NULL,
  `urut_tujuan` int(11) DEFAULT NULL,
  `visi_teks` text DEFAULT NULL,
  `active` tinyint(4) NOT NULL,
  `update_at` datetime NOT NULL,
  `tahun_anggaran` year(4) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
ALTER TABLE `data_rpjmd_program_lokal`
  ADD PRIMARY KEY (`id`);
ALTER TABLE `data_rpjmd_program_lokal`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;



CREATE TABLE `data_renstra_tujuan_lokal` (
  `id` int(11) NOT NULL,
  `bidur_lock` int(11) DEFAULT NULL,
  `id_bidang_urusan` int(11) DEFAULT NULL,
  `id_unik` text DEFAULT NULL,
  `id_unik_indikator` text DEFAULT NULL,
  `id_unit` int(11) DEFAULT NULL,
  `indikator_teks` text DEFAULT NULL,
  `is_locked` tinyint(4) DEFAULT NULL,
  `is_locked_indikator` tinyint(4) DEFAULT NULL,
  `kode_bidang_urusan` text DEFAULT NULL,
  `kode_sasaran_rpjm` text DEFAULT NULL,
  `kode_skpd` text DEFAULT NULL,
  `nama_bidang_urusan` text DEFAULT NULL,
  `nama_skpd` text DEFAULT NULL,
  `satuan` text DEFAULT NULL,
  `status` text DEFAULT NULL,
  `target_1` text DEFAULT NULL,
  `target_2` text DEFAULT NULL,
  `target_3` text DEFAULT NULL,
  `target_4` text DEFAULT NULL,
  `target_5` text DEFAULT NULL,
  `target_akhir` text DEFAULT NULL,
  `target_awal` text DEFAULT NULL,
  `tujuan_teks` text DEFAULT NULL,
  `urut_tujuan` int(11) DEFAULT NULL,
  `active` tinyint(4) NOT NULL,
  `update_at` datetime NOT NULL,
  `tahun_anggaran` year(4) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
ALTER TABLE `data_renstra_tujuan_lokal`
  ADD PRIMARY KEY (`id`);
ALTER TABLE `data_renstra_tujuan_lokal`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

CREATE TABLE `data_renstra_sasaran_lokal` (
  `id` int(11) NOT NULL,
  `bidur_lock` int(11) DEFAULT NULL,
  `id_bidang_urusan` int(11) DEFAULT NULL,
  `id_misi` int(11) DEFAULT NULL,
  `id_unik` int(11) DEFAULT NULL,
  `id_unik_indikator` text DEFAULT NULL,
  `id_unit` text DEFAULT NULL,
  `id_visi` int(11) DEFAULT NULL,
  `indikator_teks` text DEFAULT NULL,
  `is_locked` int(11) DEFAULT NULL,
  `is_locked_indikator` int(11) DEFAULT NULL,
  `kode_bidang_urusan` text DEFAULT NULL,
  `kode_skpd` text DEFAULT NULL,
  `kode_tujuan` text DEFAULT NULL,
  `nama_bidang_urusan` text DEFAULT NULL,
  `nama_skpd` text DEFAULT NULL,
  `sasaran_teks` text DEFAULT NULL,
  `satuan` text DEFAULT NULL,
  `status` text DEFAULT NULL,
  `target_1` text DEFAULT NULL,
  `target_2` text DEFAULT NULL,
  `target_3` text DEFAULT NULL,
  `target_4` text DEFAULT NULL,
  `target_5` text DEFAULT NULL,
  `target_akhir` text DEFAULT NULL,
  `target_awal` text DEFAULT NULL,
  `tujuan_lock` tinyint(4) DEFAULT NULL,
  `tujuan_teks` text DEFAULT NULL,
  `urut_sasaran` tinyint(4) DEFAULT NULL,
  `urut_tujuan` tinyint(4) DEFAULT NULL,
  `active` tinyint(4) NOT NULL,
  `update_at` datetime NOT NULL,
  `tahun_anggaran` year(4) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
ALTER TABLE `data_renstra_sasaran_lokal`
  ADD PRIMARY KEY (`id`);
ALTER TABLE `data_renstra_sasaran_lokal`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

CREATE TABLE `data_renstra_program_lokal` (
  `id` int(11) NOT NULL,
  `bidur_lock` int(11) DEFAULT NULL,
  `id_bidang_urusan` int(11) DEFAULT NULL,
  `id_misi` int(11) DEFAULT NULL,
  `id_program` int(11) DEFAULT NULL,
  `id_unik` text DEFAULT NULL,
  `id_unik_indikator` text DEFAULT NULL,
  `id_unit` int(11) DEFAULT NULL,
  `id_visi` int(11) DEFAULT NULL,
  `indikator` text DEFAULT NULL,
  `is_locked` tinyint(4) DEFAULT NULL,
  `is_locked_indikator` tinyint(4) DEFAULT NULL,
  `kode_bidang_urusan` text DEFAULT NULL,
  `kode_program` text DEFAULT NULL,
  `kode_sasaran` text DEFAULT NULL,
  `kode_skpd` text DEFAULT NULL,
  `kode_tujuan` text DEFAULT NULL,
  `nama_bidang_urusan` text DEFAULT NULL,
  `nama_program` text DEFAULT NULL,
  `nama_skpd` text DEFAULT NULL,
  `pagu_1` DECIMAL(20,0) DEFAULT NULL,
  `pagu_2` DECIMAL(20,0) DEFAULT NULL,
  `pagu_3` DECIMAL(20,0) DEFAULT NULL,
  `pagu_4` DECIMAL(20,0) DEFAULT NULL,
  `pagu_5` DECIMAL(20,0) DEFAULT NULL,
  `program_lock` tinyint(4) DEFAULT NULL,
  `sasaran_lock` tinyint(4) DEFAULT NULL,
  `sasaran_teks` text DEFAULT NULL,
  `satuan` text DEFAULT NULL,
  `status` text DEFAULT NULL,
  `target_1` text DEFAULT NULL,
  `target_2` text DEFAULT NULL,
  `target_3` text DEFAULT NULL,
  `target_4` text DEFAULT NULL,
  `target_5` text DEFAULT NULL,
  `target_akhir` text DEFAULT NULL,
  `target_awal` text DEFAULT NULL,
  `tujuan_lock` int(11) DEFAULT NULL,
  `tujuan_teks` text DEFAULT NULL,
  `urut_sasaran` tinyint(4) DEFAULT NULL,
  `urut_tujuan` tinyint(4) DEFAULT NULL,
  `active` tinyint(4) NOT NULL,
  `update_at` datetime NOT NULL,
  `tahun_anggaran` year(4) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
ALTER TABLE `data_renstra_program_lokal`
  ADD PRIMARY KEY (`id`);
ALTER TABLE `data_renstra_program_lokal`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

CREATE TABLE `data_renstra_kegiatan_lokal` (
  `id` int(11) NOT NULL,
  `bidur_lock` tinyint(4) DEFAULT NULL,
  `giat_lock` tinyint(4) DEFAULT NULL,
  `id_bidang_urusan` int(11) DEFAULT NULL,
  `id_giat` int(11) DEFAULT NULL,
  `id_misi` int(11) DEFAULT NULL,
  `id_program` int(11) DEFAULT NULL,
  `id_unik` text DEFAULT NULL,
  `id_unik_indikator` text DEFAULT NULL,
  `id_unit` int(11) DEFAULT NULL,
  `id_visi` int(11) DEFAULT NULL,
  `indikator` text DEFAULT NULL,
  `is_locked` tinyint(4) DEFAULT NULL,
  `is_locked_indikator` tinyint(4) DEFAULT NULL,
  `kode_bidang_urusan` text DEFAULT NULL,
  `kode_giat` text DEFAULT NULL,
  `kode_program` text DEFAULT NULL,
  `kode_sasaran` text DEFAULT NULL,
  `kode_skpd` text DEFAULT NULL,
  `kode_tujuan` text DEFAULT NULL,
  `kode_unik_program` text DEFAULT NULL,
  `nama_bidang_urusan` text DEFAULT NULL,
  `nama_giat` text DEFAULT NULL,
  `nama_program` text DEFAULT NULL,
  `nama_skpd` text DEFAULT NULL,
  `pagu_1` DECIMAL(20,0) DEFAULT NULL,
  `pagu_2` DECIMAL(20,0) DEFAULT NULL,
  `pagu_3` DECIMAL(20,0) DEFAULT NULL,
  `pagu_4` DECIMAL(20,0) DEFAULT NULL,
  `pagu_5` DECIMAL(20,0) DEFAULT NULL,
  `program_lock` tinyint(4) DEFAULT NULL,
  `renstra_prog_lock` tinyint(4) DEFAULT NULL,
  `sasaran_lock` tinyint(4) DEFAULT NULL,
  `sasaran_teks` text DEFAULT NULL,
  `satuan` text DEFAULT NULL,
  `status` text DEFAULT NULL,
  `target_1` text DEFAULT NULL,
  `target_2` text DEFAULT NULL,
  `target_3` text DEFAULT NULL,
  `target_4` text DEFAULT NULL,
  `target_5` text DEFAULT NULL,
  `target_akhir` text DEFAULT NULL,
  `target_awal` text DEFAULT NULL,
  `tujuan_lock` tinyint(4) DEFAULT NULL,
  `tujuan_teks` text DEFAULT NULL,
  `urut_sasaran` tinyint(4) DEFAULT NULL,
  `urut_tujuan` tinyint(4) DEFAULT NULL,
  `active` tinyint(4) NOT NULL,
  `update_at` datetime NOT NULL,
  `tahun_anggaran` year(4) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
ALTER TABLE `data_renstra_kegiatan_lokal`
  ADD PRIMARY KEY (`id`);
ALTER TABLE `data_renstra_kegiatan_lokal`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

-- history lokal --

CREATE TABLE `data_rpjmd_visi_lokal_history` (
  `id` int(11) NOT NULL,
  `id_visi` int(11) DEFAULT NULL,
  `is_locked` tinyint(2) DEFAULT NULL,
  `status` text DEFAULT NULL,
  `visi_teks` text DEFAULT NULL,
  `update_at` datetime NOT NULL,
  `active` tinyint(4) NOT NULL,
  `tahun_anggaran` year(4) NOT NULL,
  `id_jadwal` int(11) NOT NULL,
  `id_asli` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
ALTER TABLE `data_rpjmd_visi_lokal_history`
  ADD PRIMARY KEY (`id`);
ALTER TABLE `data_rpjmd_visi_lokal_history`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

CREATE TABLE `data_rpjmd_misi_lokal_history` (
  `id` int(11) NOT NULL,
  `id_misi` int(11) DEFAULT NULL,
  `id_misi_old` int(11) DEFAULT NULL,
  `id_visi` int(11) DEFAULT NULL,
  `is_locked` tinyint(2) DEFAULT NULL,
  `misi_teks` text DEFAULT NULL,
  `status` text DEFAULT NULL,
  `urut_misi` tinyint(2) DEFAULT NULL,
  `visi_lock` tinyint(2) DEFAULT NULL,
  `visi_teks` text DEFAULT NULL,
  `update_at` datetime NOT NULL,
  `active` tinyint(4) NOT NULL,
  `tahun_anggaran` year(4) NOT NULL,
  `id_jadwal` int(11) NOT NULL,
  `id_asli` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
ALTER TABLE `data_rpjmd_misi_lokal_history`
  ADD PRIMARY KEY (`id`);
ALTER TABLE `data_rpjmd_misi_lokal_history`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

CREATE TABLE `data_rpjmd_tujuan_lokal_history` (
  `id` int(11) NOT NULL,
  `id_misi` int(11) DEFAULT NULL,
  `id_misi_old` int(11) DEFAULT NULL,
  `id_tujuan` int(11) DEFAULT NULL,
  `id_unik` text DEFAULT NULL,
  `id_unik_indikator` text DEFAULT NULL,
  `id_visi` int(11) DEFAULT NULL,
  `indikator_teks` text DEFAULT NULL,
  `is_locked` tinyint(4) DEFAULT NULL,
  `is_locked_indikator` tinyint(4) DEFAULT NULL,
  `misi_lock` tinyint(4) DEFAULT NULL,
  `misi_teks` text DEFAULT NULL,
  `satuan` text DEFAULT NULL,
  `status` text DEFAULT NULL,
  `target_1` text DEFAULT NULL,
  `target_2` text DEFAULT NULL,
  `target_3` text DEFAULT NULL,
  `target_4` text DEFAULT NULL,
  `target_5` text DEFAULT NULL,
  `target_akhir` text DEFAULT NULL,
  `target_awal` text DEFAULT NULL,
  `tujuan_teks` text DEFAULT NULL,
  `urut_misi` int(11) DEFAULT NULL,
  `urut_tujuan` int(11) DEFAULT NULL,
  `visi_teks` text DEFAULT NULL,
  `active` tinyint(4) NOT NULL,
  `update_at` datetime NOT NULL,
  `tahun_anggaran` year(4) NOT NULL,
  `id_jadwal` int(11) NOT NULL,
  `id_asli` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
ALTER TABLE `data_rpjmd_tujuan_lokal_history`
  ADD PRIMARY KEY (`id`);
ALTER TABLE `data_rpjmd_tujuan_lokal_history`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;


CREATE TABLE `data_rpjmd_sasaran_lokal_history` (
  `id` int(11) NOT NULL,
  `id_misi` int(11) DEFAULT NULL,
  `id_misi_old` int(11) DEFAULT NULL,
  `id_sasaran` int(11) DEFAULT NULL,
  `id_unik` text DEFAULT NULL,
  `id_unik_indikator` text DEFAULT NULL,
  `id_visi` int(11) DEFAULT NULL,
  `indikator_teks` text DEFAULT NULL,
  `is_locked` tinyint(4) DEFAULT NULL,
  `is_locked_indikator` tinyint(4) DEFAULT NULL,
  `kode_tujuan` text DEFAULT NULL,
  `misi_teks` text DEFAULT NULL,
  `sasaran_teks` text DEFAULT NULL,
  `satuan` text DEFAULT NULL,
  `status` text DEFAULT NULL,
  `target_1` text DEFAULT NULL,
  `target_2` text DEFAULT NULL,
  `target_3` text DEFAULT NULL,
  `target_4` text DEFAULT NULL,
  `target_5` text DEFAULT NULL,
  `target_akhir` text DEFAULT NULL,
  `target_awal` text DEFAULT NULL,
  `tujuan_lock` tinyint(4) DEFAULT NULL,
  `tujuan_teks` text DEFAULT NULL,
  `urut_misi` int(11) DEFAULT NULL,
  `urut_sasaran` int(11) DEFAULT NULL,
  `urut_tujuan` int(11) DEFAULT NULL,
  `visi_teks` text DEFAULT NULL,
  `active` tinyint(4) NOT NULL,
  `update_at` datetime NOT NULL,
  `tahun_anggaran` year(4) NOT NULL,
  `id_jadwal` int(11) NOT NULL,
  `id_asli` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
ALTER TABLE `data_rpjmd_sasaran_lokal_history`
  ADD PRIMARY KEY (`id`);
ALTER TABLE `data_rpjmd_sasaran_lokal_history`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;


CREATE TABLE `data_rpjmd_program_lokal_history` (
  `id` int(11) NOT NULL,
  `id_misi` int(11) DEFAULT NULL,
  `id_misi_old` int(11) DEFAULT NULL,
  `id_program` int(11) DEFAULT NULL,
  `id_unik` text DEFAULT NULL,
  `id_unik_indikator` text DEFAULT NULL,
  `id_unit` int(11) DEFAULT NULL,
  `id_visi` int(11) DEFAULT NULL,
  `indikator` text DEFAULT NULL,
  `is_locked` tinyint(4) DEFAULT NULL,
  `is_locked_indikator` tinyint(4) DEFAULT NULL,
  `kode_sasaran` text DEFAULT NULL,
  `kode_skpd` text DEFAULT NULL,
  `kode_tujuan` text DEFAULT NULL,
  `misi_teks` text DEFAULT NULL,
  `nama_program` text DEFAULT NULL,
  `nama_skpd` text DEFAULT NULL,
  `pagu_1` double(20,0) DEFAULT NULL,
  `pagu_2` double(20,0) DEFAULT NULL,
  `pagu_3` double(20,0) DEFAULT NULL,
  `pagu_4` double(20,0) DEFAULT NULL,
  `pagu_5` double(20,0) DEFAULT NULL,
  `program_lock` tinyint(4) DEFAULT NULL,
  `sasaran_lock` tinyint(4) DEFAULT NULL,
  `sasaran_teks` text DEFAULT NULL,
  `satuan` text DEFAULT NULL,
  `status` text DEFAULT NULL,
  `target_1` text DEFAULT NULL,
  `target_2` text DEFAULT NULL,
  `target_3` text DEFAULT NULL,
  `target_4` text DEFAULT NULL,
  `target_5` text DEFAULT NULL,
  `target_akhir` text DEFAULT NULL,
  `target_awal` text DEFAULT NULL,
  `tujuan_lock` tinyint(4) DEFAULT NULL,
  `tujuan_teks` text DEFAULT NULL,
  `urut_misi` int(11) DEFAULT NULL,
  `urut_sasaran` int(11) DEFAULT NULL,
  `urut_tujuan` int(11) DEFAULT NULL,
  `visi_teks` text DEFAULT NULL,
  `active` tinyint(4) NOT NULL,
  `update_at` datetime NOT NULL,
  `tahun_anggaran` year(4) NOT NULL,
  `id_jadwal` int(11) NOT NULL,
  `id_asli` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
ALTER TABLE `data_rpjmd_program_lokal_history`
  ADD PRIMARY KEY (`id`);
ALTER TABLE `data_rpjmd_program_lokal_history`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;



CREATE TABLE `data_renstra_tujuan_lokal_history` (
  `id` int(11) NOT NULL,
  `bidur_lock` int(11) DEFAULT NULL,
  `id_bidang_urusan` int(11) DEFAULT NULL,
  `id_unik` text DEFAULT NULL,
  `id_unik_indikator` text DEFAULT NULL,
  `id_unit` int(11) DEFAULT NULL,
  `indikator_teks` text DEFAULT NULL,
  `is_locked` tinyint(4) DEFAULT NULL,
  `is_locked_indikator` tinyint(4) DEFAULT NULL,
  `kode_bidang_urusan` text DEFAULT NULL,
  `kode_sasaran_rpjm` text DEFAULT NULL,
  `kode_skpd` text DEFAULT NULL,
  `nama_bidang_urusan` text DEFAULT NULL,
  `nama_skpd` text DEFAULT NULL,
  `satuan` text DEFAULT NULL,
  `status` text DEFAULT NULL,
  `target_1` text DEFAULT NULL,
  `target_2` text DEFAULT NULL,
  `target_3` text DEFAULT NULL,
  `target_4` text DEFAULT NULL,
  `target_5` text DEFAULT NULL,
  `target_akhir` text DEFAULT NULL,
  `target_awal` text DEFAULT NULL,
  `tujuan_teks` text DEFAULT NULL,
  `urut_tujuan` int(11) DEFAULT NULL,
  `active` tinyint(4) NOT NULL,
  `update_at` datetime NOT NULL,
  `tahun_anggaran` year(4) NOT NULL,
  `id_jadwal` int(11) NOT NULL,
  `id_asli` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
ALTER TABLE `data_renstra_tujuan_lokal_history`
  ADD PRIMARY KEY (`id`);
ALTER TABLE `data_renstra_tujuan_lokal_history`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

CREATE TABLE `data_renstra_sasaran_lokal_history` (
  `id` int(11) NOT NULL,
  `bidur_lock` int(11) DEFAULT NULL,
  `id_bidang_urusan` int(11) DEFAULT NULL,
  `id_misi` int(11) DEFAULT NULL,
  `id_unik` int(11) DEFAULT NULL,
  `id_unik_indikator` text DEFAULT NULL,
  `id_unit` text DEFAULT NULL,
  `id_visi` int(11) DEFAULT NULL,
  `indikator_teks` text DEFAULT NULL,
  `is_locked` int(11) DEFAULT NULL,
  `is_locked_indikator` int(11) DEFAULT NULL,
  `kode_bidang_urusan` text DEFAULT NULL,
  `kode_skpd` text DEFAULT NULL,
  `kode_tujuan` text DEFAULT NULL,
  `nama_bidang_urusan` text DEFAULT NULL,
  `nama_skpd` text DEFAULT NULL,
  `sasaran_teks` text DEFAULT NULL,
  `satuan` text DEFAULT NULL,
  `status` text DEFAULT NULL,
  `target_1` text DEFAULT NULL,
  `target_2` text DEFAULT NULL,
  `target_3` text DEFAULT NULL,
  `target_4` text DEFAULT NULL,
  `target_5` text DEFAULT NULL,
  `target_akhir` text DEFAULT NULL,
  `target_awal` text DEFAULT NULL,
  `tujuan_lock` tinyint(4) DEFAULT NULL,
  `tujuan_teks` text DEFAULT NULL,
  `urut_sasaran` tinyint(4) DEFAULT NULL,
  `urut_tujuan` tinyint(4) DEFAULT NULL,
  `active` tinyint(4) NOT NULL,
  `update_at` datetime NOT NULL,
  `tahun_anggaran` year(4) NOT NULL,
  `id_jadwal` int(11) NOT NULL,
  `id_asli` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
ALTER TABLE `data_renstra_sasaran_lokal_history`
  ADD PRIMARY KEY (`id`);
ALTER TABLE `data_renstra_sasaran_lokal_history`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

CREATE TABLE `data_renstra_program_lokal_history` (
  `id` int(11) NOT NULL,
  `bidur_lock` int(11) DEFAULT NULL,
  `id_bidang_urusan` int(11) DEFAULT NULL,
  `id_misi` int(11) DEFAULT NULL,
  `id_program` int(11) DEFAULT NULL,
  `id_unik` text DEFAULT NULL,
  `id_unik_indikator` text DEFAULT NULL,
  `id_unit` int(11) DEFAULT NULL,
  `id_visi` int(11) DEFAULT NULL,
  `indikator` text DEFAULT NULL,
  `is_locked` tinyint(4) DEFAULT NULL,
  `is_locked_indikator` tinyint(4) DEFAULT NULL,
  `kode_bidang_urusan` text DEFAULT NULL,
  `kode_program` text DEFAULT NULL,
  `kode_sasaran` text DEFAULT NULL,
  `kode_skpd` text DEFAULT NULL,
  `kode_tujuan` text DEFAULT NULL,
  `nama_bidang_urusan` text DEFAULT NULL,
  `nama_program` text DEFAULT NULL,
  `nama_skpd` text DEFAULT NULL,
  `pagu_1` DECIMAL(20,0) DEFAULT NULL,
  `pagu_2` DECIMAL(20,0) DEFAULT NULL,
  `pagu_3` DECIMAL(20,0) DEFAULT NULL,
  `pagu_4` DECIMAL(20,0) DEFAULT NULL,
  `pagu_5` DECIMAL(20,0) DEFAULT NULL,
  `program_lock` tinyint(4) DEFAULT NULL,
  `sasaran_lock` tinyint(4) DEFAULT NULL,
  `sasaran_teks` text DEFAULT NULL,
  `satuan` text DEFAULT NULL,
  `status` text DEFAULT NULL,
  `target_1` text DEFAULT NULL,
  `target_2` text DEFAULT NULL,
  `target_3` text DEFAULT NULL,
  `target_4` text DEFAULT NULL,
  `target_5` text DEFAULT NULL,
  `target_akhir` text DEFAULT NULL,
  `target_awal` text DEFAULT NULL,
  `tujuan_lock` int(11) DEFAULT NULL,
  `tujuan_teks` text DEFAULT NULL,
  `urut_sasaran` tinyint(4) DEFAULT NULL,
  `urut_tujuan` tinyint(4) DEFAULT NULL,
  `active` tinyint(4) NOT NULL,
  `update_at` datetime NOT NULL,
  `tahun_anggaran` year(4) NOT NULL,
  `id_jadwal` int(11) NOT NULL,
  `id_asli` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
ALTER TABLE `data_renstra_program_lokal_history`
  ADD PRIMARY KEY (`id`);
ALTER TABLE `data_renstra_program_lokal_history`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

CREATE TABLE `data_renstra_kegiatan_lokal_history` (
  `id` int(11) NOT NULL,
  `bidur_lock` tinyint(4) DEFAULT NULL,
  `giat_lock` tinyint(4) DEFAULT NULL,
  `id_bidang_urusan` int(11) DEFAULT NULL,
  `id_giat` int(11) DEFAULT NULL,
  `id_misi` int(11) DEFAULT NULL,
  `id_program` int(11) DEFAULT NULL,
  `id_unik` text DEFAULT NULL,
  `id_unik_indikator` text DEFAULT NULL,
  `id_unit` int(11) DEFAULT NULL,
  `id_visi` int(11) DEFAULT NULL,
  `indikator` text DEFAULT NULL,
  `is_locked` tinyint(4) DEFAULT NULL,
  `is_locked_indikator` tinyint(4) DEFAULT NULL,
  `kode_bidang_urusan` text DEFAULT NULL,
  `kode_giat` text DEFAULT NULL,
  `kode_program` text DEFAULT NULL,
  `kode_sasaran` text DEFAULT NULL,
  `kode_skpd` text DEFAULT NULL,
  `kode_tujuan` text DEFAULT NULL,
  `kode_unik_program` text DEFAULT NULL,
  `nama_bidang_urusan` text DEFAULT NULL,
  `nama_giat` text DEFAULT NULL,
  `nama_program` text DEFAULT NULL,
  `nama_skpd` text DEFAULT NULL,
  `pagu_1` DECIMAL(20,0) DEFAULT NULL,
  `pagu_2` DECIMAL(20,0) DEFAULT NULL,
  `pagu_3` DECIMAL(20,0) DEFAULT NULL,
  `pagu_4` DECIMAL(20,0) DEFAULT NULL,
  `pagu_5` DECIMAL(20,0) DEFAULT NULL,
  `program_lock` tinyint(4) DEFAULT NULL,
  `renstra_prog_lock` tinyint(4) DEFAULT NULL,
  `sasaran_lock` tinyint(4) DEFAULT NULL,
  `sasaran_teks` text DEFAULT NULL,
  `satuan` text DEFAULT NULL,
  `status` text DEFAULT NULL,
  `target_1` text DEFAULT NULL,
  `target_2` text DEFAULT NULL,
  `target_3` text DEFAULT NULL,
  `target_4` text DEFAULT NULL,
  `target_5` text DEFAULT NULL,
  `target_akhir` text DEFAULT NULL,
  `target_awal` text DEFAULT NULL,
  `tujuan_lock` tinyint(4) DEFAULT NULL,
  `tujuan_teks` text DEFAULT NULL,
  `urut_sasaran` tinyint(4) DEFAULT NULL,
  `urut_tujuan` tinyint(4) DEFAULT NULL,
  `active` tinyint(4) NOT NULL,
  `update_at` datetime NOT NULL,
  `tahun_anggaran` year(4) NOT NULL,
  `id_jadwal` int(11) NOT NULL,
  `id_asli` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
ALTER TABLE `data_renstra_kegiatan_lokal_history`
  ADD PRIMARY KEY (`id`);
ALTER TABLE `data_renstra_kegiatan_lokal_history`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

-- history SIPD --

CREATE TABLE `data_rpjmd_visi_history` (
  `id` int(11) NOT NULL,
  `id_visi` int(11) DEFAULT NULL,
  `is_locked` tinyint(2) DEFAULT NULL,
  `status` text DEFAULT NULL,
  `visi_teks` text DEFAULT NULL,
  `update_at` datetime NOT NULL,
  `active` tinyint(4) NOT NULL,
  `tahun_anggaran` year(4) NOT NULL,
  `id_jadwal` int(11) NOT NULL,
  `id_asli` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
ALTER TABLE `data_rpjmd_visi_history`
  ADD PRIMARY KEY (`id`);
ALTER TABLE `data_rpjmd_visi_history`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

CREATE TABLE `data_rpjmd_misi_history` (
  `id` int(11) NOT NULL,
  `id_misi` int(11) DEFAULT NULL,
  `id_misi_old` int(11) DEFAULT NULL,
  `id_visi` int(11) DEFAULT NULL,
  `is_locked` tinyint(2) DEFAULT NULL,
  `misi_teks` text DEFAULT NULL,
  `status` text DEFAULT NULL,
  `urut_misi` tinyint(2) DEFAULT NULL,
  `visi_lock` tinyint(2) DEFAULT NULL,
  `visi_teks` text DEFAULT NULL,
  `update_at` datetime NOT NULL,
  `active` tinyint(4) NOT NULL,
  `tahun_anggaran` year(4) NOT NULL,
  `id_jadwal` int(11) NOT NULL,
  `id_asli` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
ALTER TABLE `data_rpjmd_misi_history`
  ADD PRIMARY KEY (`id`);
ALTER TABLE `data_rpjmd_misi_history`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

CREATE TABLE `data_rpjmd_tujuan_history` (
  `id` int(11) NOT NULL,
  `id_misi` int(11) DEFAULT NULL,
  `id_misi_old` int(11) DEFAULT NULL,
  `id_tujuan` int(11) DEFAULT NULL,
  `id_unik` text DEFAULT NULL,
  `id_unik_indikator` text DEFAULT NULL,
  `id_visi` int(11) DEFAULT NULL,
  `indikator_teks` text DEFAULT NULL,
  `is_locked` tinyint(4) DEFAULT NULL,
  `is_locked_indikator` tinyint(4) DEFAULT NULL,
  `misi_lock` tinyint(4) DEFAULT NULL,
  `misi_teks` text DEFAULT NULL,
  `satuan` text DEFAULT NULL,
  `status` text DEFAULT NULL,
  `target_1` text DEFAULT NULL,
  `target_2` text DEFAULT NULL,
  `target_3` text DEFAULT NULL,
  `target_4` text DEFAULT NULL,
  `target_5` text DEFAULT NULL,
  `target_akhir` text DEFAULT NULL,
  `target_awal` text DEFAULT NULL,
  `tujuan_teks` text DEFAULT NULL,
  `urut_misi` int(11) DEFAULT NULL,
  `urut_tujuan` int(11) DEFAULT NULL,
  `visi_teks` text DEFAULT NULL,
  `active` tinyint(4) NOT NULL,
  `update_at` datetime NOT NULL,
  `tahun_anggaran` year(4) NOT NULL,
  `id_jadwal` int(11) NOT NULL,
  `id_asli` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
ALTER TABLE `data_rpjmd_tujuan_history`
  ADD PRIMARY KEY (`id`);
ALTER TABLE `data_rpjmd_tujuan_history`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;


CREATE TABLE `data_rpjmd_sasaran_history` (
  `id` int(11) NOT NULL,
  `id_misi` int(11) DEFAULT NULL,
  `id_misi_old` int(11) DEFAULT NULL,
  `id_sasaran` int(11) DEFAULT NULL,
  `id_unik` text DEFAULT NULL,
  `id_unik_indikator` text DEFAULT NULL,
  `id_visi` int(11) DEFAULT NULL,
  `indikator_teks` text DEFAULT NULL,
  `is_locked` tinyint(4) DEFAULT NULL,
  `is_locked_indikator` tinyint(4) DEFAULT NULL,
  `kode_tujuan` text DEFAULT NULL,
  `misi_teks` text DEFAULT NULL,
  `sasaran_teks` text DEFAULT NULL,
  `satuan` text DEFAULT NULL,
  `status` text DEFAULT NULL,
  `target_1` text DEFAULT NULL,
  `target_2` text DEFAULT NULL,
  `target_3` text DEFAULT NULL,
  `target_4` text DEFAULT NULL,
  `target_5` text DEFAULT NULL,
  `target_akhir` text DEFAULT NULL,
  `target_awal` text DEFAULT NULL,
  `tujuan_lock` tinyint(4) DEFAULT NULL,
  `tujuan_teks` text DEFAULT NULL,
  `urut_misi` int(11) DEFAULT NULL,
  `urut_sasaran` int(11) DEFAULT NULL,
  `urut_tujuan` int(11) DEFAULT NULL,
  `visi_teks` text DEFAULT NULL,
  `active` tinyint(4) NOT NULL,
  `update_at` datetime NOT NULL,
  `tahun_anggaran` year(4) NOT NULL,
  `id_jadwal` int(11) NOT NULL,
  `id_asli` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
ALTER TABLE `data_rpjmd_sasaran_history`
  ADD PRIMARY KEY (`id`);
ALTER TABLE `data_rpjmd_sasaran_history`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;


CREATE TABLE `data_rpjmd_program_history` (
  `id` int(11) NOT NULL,
  `id_misi` int(11) DEFAULT NULL,
  `id_misi_old` int(11) DEFAULT NULL,
  `id_program` int(11) DEFAULT NULL,
  `id_unik` text DEFAULT NULL,
  `id_unik_indikator` text DEFAULT NULL,
  `id_unit` int(11) DEFAULT NULL,
  `id_visi` int(11) DEFAULT NULL,
  `indikator` text DEFAULT NULL,
  `is_locked` tinyint(4) DEFAULT NULL,
  `is_locked_indikator` tinyint(4) DEFAULT NULL,
  `kode_sasaran` text DEFAULT NULL,
  `kode_skpd` text DEFAULT NULL,
  `kode_tujuan` text DEFAULT NULL,
  `misi_teks` text DEFAULT NULL,
  `nama_program` text DEFAULT NULL,
  `nama_skpd` text DEFAULT NULL,
  `pagu_1` double(20,0) DEFAULT NULL,
  `pagu_2` double(20,0) DEFAULT NULL,
  `pagu_3` double(20,0) DEFAULT NULL,
  `pagu_4` double(20,0) DEFAULT NULL,
  `pagu_5` double(20,0) DEFAULT NULL,
  `program_lock` tinyint(4) DEFAULT NULL,
  `sasaran_lock` tinyint(4) DEFAULT NULL,
  `sasaran_teks` text DEFAULT NULL,
  `satuan` text DEFAULT NULL,
  `status` text DEFAULT NULL,
  `target_1` text DEFAULT NULL,
  `target_2` text DEFAULT NULL,
  `target_3` text DEFAULT NULL,
  `target_4` text DEFAULT NULL,
  `target_5` text DEFAULT NULL,
  `target_akhir` text DEFAULT NULL,
  `target_awal` text DEFAULT NULL,
  `tujuan_lock` tinyint(4) DEFAULT NULL,
  `tujuan_teks` text DEFAULT NULL,
  `urut_misi` int(11) DEFAULT NULL,
  `urut_sasaran` int(11) DEFAULT NULL,
  `urut_tujuan` int(11) DEFAULT NULL,
  `visi_teks` text DEFAULT NULL,
  `active` tinyint(4) NOT NULL,
  `update_at` datetime NOT NULL,
  `tahun_anggaran` year(4) NOT NULL,
  `id_jadwal` int(11) NOT NULL,
  `id_asli` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
ALTER TABLE `data_rpjmd_program_history`
  ADD PRIMARY KEY (`id`);
ALTER TABLE `data_rpjmd_program_history`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;



CREATE TABLE `data_renstra_tujuan_history` (
  `id` int(11) NOT NULL,
  `bidur_lock` int(11) DEFAULT NULL,
  `id_bidang_urusan` int(11) DEFAULT NULL,
  `id_unik` text DEFAULT NULL,
  `id_unik_indikator` text DEFAULT NULL,
  `id_unit` int(11) DEFAULT NULL,
  `indikator_teks` text DEFAULT NULL,
  `is_locked` tinyint(4) DEFAULT NULL,
  `is_locked_indikator` tinyint(4) DEFAULT NULL,
  `kode_bidang_urusan` text DEFAULT NULL,
  `kode_sasaran_rpjm` text DEFAULT NULL,
  `kode_skpd` text DEFAULT NULL,
  `nama_bidang_urusan` text DEFAULT NULL,
  `nama_skpd` text DEFAULT NULL,
  `satuan` text DEFAULT NULL,
  `status` text DEFAULT NULL,
  `target_1` text DEFAULT NULL,
  `target_2` text DEFAULT NULL,
  `target_3` text DEFAULT NULL,
  `target_4` text DEFAULT NULL,
  `target_5` text DEFAULT NULL,
  `target_akhir` text DEFAULT NULL,
  `target_awal` text DEFAULT NULL,
  `tujuan_teks` text DEFAULT NULL,
  `urut_tujuan` int(11) DEFAULT NULL,
  `active` tinyint(4) NOT NULL,
  `update_at` datetime NOT NULL,
  `tahun_anggaran` year(4) NOT NULL,
  `id_jadwal` int(11) NOT NULL,
  `id_asli` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
ALTER TABLE `data_renstra_tujuan_history`
  ADD PRIMARY KEY (`id`);
ALTER TABLE `data_renstra_tujuan_history`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

CREATE TABLE `data_renstra_sasaran_history` (
  `id` int(11) NOT NULL,
  `bidur_lock` int(11) DEFAULT NULL,
  `id_bidang_urusan` int(11) DEFAULT NULL,
  `id_misi` int(11) DEFAULT NULL,
  `id_unik` int(11) DEFAULT NULL,
  `id_unik_indikator` text DEFAULT NULL,
  `id_unit` text DEFAULT NULL,
  `id_visi` int(11) DEFAULT NULL,
  `indikator_teks` text DEFAULT NULL,
  `is_locked` int(11) DEFAULT NULL,
  `is_locked_indikator` int(11) DEFAULT NULL,
  `kode_bidang_urusan` text DEFAULT NULL,
  `kode_skpd` text DEFAULT NULL,
  `kode_tujuan` text DEFAULT NULL,
  `nama_bidang_urusan` text DEFAULT NULL,
  `nama_skpd` text DEFAULT NULL,
  `sasaran_teks` text DEFAULT NULL,
  `satuan` text DEFAULT NULL,
  `status` text DEFAULT NULL,
  `target_1` text DEFAULT NULL,
  `target_2` text DEFAULT NULL,
  `target_3` text DEFAULT NULL,
  `target_4` text DEFAULT NULL,
  `target_5` text DEFAULT NULL,
  `target_akhir` text DEFAULT NULL,
  `target_awal` text DEFAULT NULL,
  `tujuan_lock` tinyint(4) DEFAULT NULL,
  `tujuan_teks` text DEFAULT NULL,
  `urut_sasaran` tinyint(4) DEFAULT NULL,
  `urut_tujuan` tinyint(4) DEFAULT NULL,
  `active` tinyint(4) NOT NULL,
  `update_at` datetime NOT NULL,
  `tahun_anggaran` year(4) NOT NULL,
  `id_jadwal` int(11) NOT NULL,
  `id_asli` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
ALTER TABLE `data_renstra_sasaran_history`
  ADD PRIMARY KEY (`id`);
ALTER TABLE `data_renstra_sasaran_history`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

CREATE TABLE `data_renstra_program_history` (
  `id` int(11) NOT NULL,
  `bidur_lock` int(11) DEFAULT NULL,
  `id_bidang_urusan` int(11) DEFAULT NULL,
  `id_misi` int(11) DEFAULT NULL,
  `id_program` int(11) DEFAULT NULL,
  `id_unik` text DEFAULT NULL,
  `id_unik_indikator` text DEFAULT NULL,
  `id_unit` int(11) DEFAULT NULL,
  `id_visi` int(11) DEFAULT NULL,
  `indikator` text DEFAULT NULL,
  `is_locked` tinyint(4) DEFAULT NULL,
  `is_locked_indikator` tinyint(4) DEFAULT NULL,
  `kode_bidang_urusan` text DEFAULT NULL,
  `kode_program` text DEFAULT NULL,
  `kode_sasaran` text DEFAULT NULL,
  `kode_skpd` text DEFAULT NULL,
  `kode_tujuan` text DEFAULT NULL,
  `nama_bidang_urusan` text DEFAULT NULL,
  `nama_program` text DEFAULT NULL,
  `nama_skpd` text DEFAULT NULL,
  `pagu_1` DECIMAL(20,0) DEFAULT NULL,
  `pagu_2` DECIMAL(20,0) DEFAULT NULL,
  `pagu_3` DECIMAL(20,0) DEFAULT NULL,
  `pagu_4` DECIMAL(20,0) DEFAULT NULL,
  `pagu_5` DECIMAL(20,0) DEFAULT NULL,
  `program_lock` tinyint(4) DEFAULT NULL,
  `sasaran_lock` tinyint(4) DEFAULT NULL,
  `sasaran_teks` text DEFAULT NULL,
  `satuan` text DEFAULT NULL,
  `status` text DEFAULT NULL,
  `target_1` text DEFAULT NULL,
  `target_2` text DEFAULT NULL,
  `target_3` text DEFAULT NULL,
  `target_4` text DEFAULT NULL,
  `target_5` text DEFAULT NULL,
  `target_akhir` text DEFAULT NULL,
  `target_awal` text DEFAULT NULL,
  `tujuan_lock` int(11) DEFAULT NULL,
  `tujuan_teks` text DEFAULT NULL,
  `urut_sasaran` tinyint(4) DEFAULT NULL,
  `urut_tujuan` tinyint(4) DEFAULT NULL,
  `active` tinyint(4) NOT NULL,
  `update_at` datetime NOT NULL,
  `tahun_anggaran` year(4) NOT NULL,
  `id_jadwal` int(11) NOT NULL,
  `id_asli` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
ALTER TABLE `data_renstra_program_history`
  ADD PRIMARY KEY (`id`);
ALTER TABLE `data_renstra_program_history`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

CREATE TABLE `data_renstra_kegiatan_history` (
  `id` int(11) NOT NULL,
  `bidur_lock` tinyint(4) DEFAULT NULL,
  `giat_lock` tinyint(4) DEFAULT NULL,
  `id_bidang_urusan` int(11) DEFAULT NULL,
  `id_giat` int(11) DEFAULT NULL,
  `id_misi` int(11) DEFAULT NULL,
  `id_program` int(11) DEFAULT NULL,
  `id_unik` text DEFAULT NULL,
  `id_unik_indikator` text DEFAULT NULL,
  `id_unit` int(11) DEFAULT NULL,
  `id_visi` int(11) DEFAULT NULL,
  `indikator` text DEFAULT NULL,
  `is_locked` tinyint(4) DEFAULT NULL,
  `is_locked_indikator` tinyint(4) DEFAULT NULL,
  `kode_bidang_urusan` text DEFAULT NULL,
  `kode_giat` text DEFAULT NULL,
  `kode_program` text DEFAULT NULL,
  `kode_sasaran` text DEFAULT NULL,
  `kode_skpd` text DEFAULT NULL,
  `kode_tujuan` text DEFAULT NULL,
  `kode_unik_program` text DEFAULT NULL,
  `nama_bidang_urusan` text DEFAULT NULL,
  `nama_giat` text DEFAULT NULL,
  `nama_program` text DEFAULT NULL,
  `nama_skpd` text DEFAULT NULL,
  `pagu_1` DECIMAL(20,0) DEFAULT NULL,
  `pagu_2` DECIMAL(20,0) DEFAULT NULL,
  `pagu_3` DECIMAL(20,0) DEFAULT NULL,
  `pagu_4` DECIMAL(20,0) DEFAULT NULL,
  `pagu_5` DECIMAL(20,0) DEFAULT NULL,
  `program_lock` tinyint(4) DEFAULT NULL,
  `renstra_prog_lock` tinyint(4) DEFAULT NULL,
  `sasaran_lock` tinyint(4) DEFAULT NULL,
  `sasaran_teks` text DEFAULT NULL,
  `satuan` text DEFAULT NULL,
  `status` text DEFAULT NULL,
  `target_1` text DEFAULT NULL,
  `target_2` text DEFAULT NULL,
  `target_3` text DEFAULT NULL,
  `target_4` text DEFAULT NULL,
  `target_5` text DEFAULT NULL,
  `target_akhir` text DEFAULT NULL,
  `target_awal` text DEFAULT NULL,
  `tujuan_lock` tinyint(4) DEFAULT NULL,
  `tujuan_teks` text DEFAULT NULL,
  `urut_sasaran` tinyint(4) DEFAULT NULL,
  `urut_tujuan` tinyint(4) DEFAULT NULL,
  `active` tinyint(4) NOT NULL,
  `update_at` datetime NOT NULL,
  `tahun_anggaran` year(4) NOT NULL,
  `id_jadwal` int(11) NOT NULL,
  `id_asli` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
ALTER TABLE `data_renstra_kegiatan_history`
  ADD PRIMARY KEY (`id`);
ALTER TABLE `data_renstra_kegiatan_history`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;