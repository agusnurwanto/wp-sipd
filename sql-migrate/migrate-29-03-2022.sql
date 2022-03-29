--
-- Table structure for table `data_ssh_usulan`
--

CREATE TABLE `data_ssh_usulan` (
  `id` int(11) NOT NULL,
  `id_standar_harga` int(11) DEFAULT NULL,
  `kode_standar_harga` varchar(30) DEFAULT NULL,
  `nama_standar_harga` text,
  `satuan` text,
  `spek` text,
  `ket_teks` text,
  `created_at` varchar(25) DEFAULT NULL,
  `created_user` int(11) DEFAULT NULL,
  `updated_user` int(11) DEFAULT NULL,
  `is_deleted` tinyint(4) DEFAULT NULL,
  `is_locked` tinyint(4) DEFAULT NULL,
  `kelompok` tinyint(4) DEFAULT NULL,
  `harga` double(20,0) DEFAULT NULL,
  `harga_2` double(20,0) DEFAULT NULL,
  `harga_3` double(20,0) DEFAULT NULL,
  `kode_kel_standar_harga` varchar(30) DEFAULT NULL,
  `nama_kel_standar_harga` text,
  `update_at` datetime DEFAULT NULL,
  `tahun_anggaran` year(4) NOT NULL DEFAULT '2022',
  `status` varchar(20) DEFAULT NULL,
  `keterangan_status` text,
  `status_upload_sipd` varchar(20) DEFAULT NULL,
  `keterangan_lampiran` text
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Indexes for table `data_ssh_usulan`
--
ALTER TABLE `data_ssh_usulan`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for table `data_ssh_usulan`
--
ALTER TABLE `data_ssh_usulan`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Table structure for table `data_ssh_rek_belanja_usulan`
--

CREATE TABLE `data_ssh_rek_belanja_usulan` (
  `id` int(11) NOT NULL,
  `id_akun` int(11) DEFAULT NULL,
  `kode_akun` varchar(50) DEFAULT NULL,
  `nama_akun` text,
  `id_standar_harga` int(11) DEFAULT NULL,
  `tahun_anggaran` year(4) NOT NULL DEFAULT '2022'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Indexes for table `data_ssh_rek_belanja_usulan`
--
ALTER TABLE `data_ssh_rek_belanja_usulan`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for table `data_ssh_rek_belanja_usulan`
--
ALTER TABLE `data_ssh_rek_belanja_usulan`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Table structure for table `data_satuan`
--

CREATE TABLE `data_satuan` (
  `id` int(11) NOT NULL,
  `id_satuan` int(11) DEFAULT NULL,
  `nama_satuan` varchar(32) DEFAULT NULL,
  `tahun_anggaran` year(4) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Indexes for table `data_satuan`
--
ALTER TABLE `data_satuan`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for table `data_satuan`
--
ALTER TABLE `data_satuan`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Table structure for table `data_kelompok_satuan_harga`
--

CREATE TABLE `data_kelompok_satuan_harga` (
  `id` int(11) NOT NULL,
  `id_kategori` int(11) DEFAULT NULL,
  `kode_kategori` varchar(64) DEFAULT NULL,
  `uraian_kategori` text,
  `tipe_kelompok` varchar(20) DEFAULT NULL,
  `tahun_anggaran` year(4) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Indexes for table `data_kelompok_satuan_harga`
--
ALTER TABLE `data_kelompok_satuan_harga`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for table `data_kelompok_satuan_harga`
--
ALTER TABLE `data_kelompok_satuan_harga`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;