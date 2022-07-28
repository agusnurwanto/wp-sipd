--
-- Table structure for table `data_rka_history`
--

CREATE TABLE `data_rka_history` (
  `id` int(11) NOT NULL,
  `created_user` int(11) DEFAULT NULL,
  `createddate` varchar(10) CHARACTER SET latin1 DEFAULT NULL,
  `createdtime` varchar(10) CHARACTER SET latin1 DEFAULT NULL,
  `harga_satuan` double(20,0) DEFAULT NULL,
  `harga_satuan_murni` double(20,0) DEFAULT NULL,
  `id_daerah` int(11) DEFAULT NULL,
  `id_rinci_sub_bl` int(11) DEFAULT NULL,
  `id_standar_nfs` tinyint(4) DEFAULT NULL,
  `is_locked` tinyint(4) DEFAULT NULL,
  `jenis_bl` varchar(50) CHARACTER SET latin1 DEFAULT NULL,
  `ket_bl_teks` text CHARACTER SET latin1,
  `kode_akun` varchar(50) CHARACTER SET latin1 DEFAULT NULL,
  `koefisien` text CHARACTER SET latin1,
  `koefisien_murni` text CHARACTER SET latin1,
  `lokus_akun_teks` text CHARACTER SET latin1,
  `nama_akun` text CHARACTER SET latin1,
  `nama_komponen` text CHARACTER SET latin1,
  `spek_komponen` text CHARACTER SET latin1,
  `satuan` varchar(50) CHARACTER SET latin1 DEFAULT NULL,
  `spek` text CHARACTER SET latin1,
  `sat1` text CHARACTER SET latin1,
  `sat2` text CHARACTER SET latin1,
  `sat3` text CHARACTER SET latin1,
  `sat4` text CHARACTER SET latin1,
  `volum1` text CHARACTER SET latin1,
  `volum2` text CHARACTER SET latin1,
  `volum3` text CHARACTER SET latin1,
  `volum4` text CHARACTER SET latin1,
  `volume` text CHARACTER SET latin1,
  `volume_murni` text CHARACTER SET latin1,
  `subs_bl_teks` text CHARACTER SET latin1,
  `subtitle_teks` text CHARACTER SET latin1,
  `kode_dana` varchar(30) CHARACTER SET latin1 DEFAULT NULL,
  `is_paket` tinyint(4) DEFAULT NULL,
  `nama_dana` text CHARACTER SET latin1,
  `id_dana` int(11) DEFAULT NULL,
  `substeks` text CHARACTER SET latin1,
  `total_harga` double(20,0) DEFAULT NULL,
  `rincian` double(20,0) DEFAULT NULL,
  `rincian_murni` double(20,0) DEFAULT NULL,
  `totalpajak` double(20,0) DEFAULT NULL,
  `pajak` double(20,0) DEFAULT NULL,
  `pajak_murni` double(20,0) DEFAULT NULL,
  `updated_user` int(11) DEFAULT NULL,
  `updateddate` varchar(20) CHARACTER SET latin1 DEFAULT NULL,
  `updatedtime` varchar(20) CHARACTER SET latin1 DEFAULT NULL,
  `user1` text CHARACTER SET latin1,
  `user2` text CHARACTER SET latin1,
  `active` tinyint(4) DEFAULT '1',
  `update_at` datetime DEFAULT NULL,
  `tahun_anggaran` year(4) NOT NULL DEFAULT '2021',
  `idbl` int(11) DEFAULT NULL,
  `idsubbl` int(11) DEFAULT NULL,
  `kode_bl` varchar(50) CHARACTER SET latin1 NOT NULL,
  `kode_sbl` varchar(50) CHARACTER SET latin1 NOT NULL,
  `id_prop_penerima` int(11) DEFAULT NULL,
  `id_camat_penerima` int(11) DEFAULT NULL,
  `id_kokab_penerima` int(11) DEFAULT NULL,
  `id_lurah_penerima` int(11) DEFAULT NULL,
  `id_penerima` int(11) DEFAULT NULL,
  `idkomponen` double(20,0) DEFAULT NULL,
  `idketerangan` int(11) DEFAULT NULL,
  `idsubtitle` int(11) DEFAULT NULL,
  `id_data_rka` int(11) NOT NULL,
  `id_local_schedule` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Indexes for table `data_rka_history`
--
ALTER TABLE `data_rka_history`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for table `data_rka_history`
--
ALTER TABLE `data_rka_history`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;