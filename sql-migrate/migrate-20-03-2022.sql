ALTER TABLE `data_pembiayaan`  ADD `pagu_fmis` DOUBLE NULL  AFTER `total`;
ALTER TABLE `data_pendapatan`  ADD `pagu_fmis` DOUBLE NULL  AFTER `total`;
ALTER TABLE `data_sub_keg_bl`  ADD `pagu_fmis` DOUBLE NULL  AFTER `pagu_keg`;

CREATE TABLE `data_rincian_fmis` (
    `id` int(11) NOT NULL,
    `id_mapping` text DEFAULT NULL,
    `id_sub_skpd` int(11) DEFAULT NULL,
    `idaktivitas` int(11) DEFAULT NULL,
    `aktivitas` text DEFAULT NULL,
    `dt_rowid` text DEFAULT NULL,
    `dt_rowindex` int(11) DEFAULT NULL,
    `created_at` datetime DEFAULT NULL,
    `created_id` int(11) DEFAULT NULL,
    `harga` double(20,0) DEFAULT NULL,
    `idrkpdrenjabelanja` int(11) DEFAULT NULL,
    `idsatuan1` int(11) DEFAULT NULL,
    `idsatuan2` int(11) DEFAULT NULL,
    `idsatuan3` int(11) DEFAULT NULL,
    `idssh_4` int(11) DEFAULT NULL,
    `idsumberdana` int(11) DEFAULT NULL,
    `jml_volume` TEXT DEFAULT NULL,
    `jml_volume_renja` TEXT DEFAULT NULL,
    `jumlah` double(20,0) DEFAULT NULL,
    `jumlah_renja` TEXT DEFAULT NULL,
    `kdrek1` tinyint(4) DEFAULT NULL,
    `kdrek2` tinyint(4) DEFAULT NULL,
    `kdrek3` tinyint(4) DEFAULT NULL,
    `kdrek4` tinyint(4) DEFAULT NULL,
    `kdrek5` tinyint(4) DEFAULT NULL,
    `kdrek6` tinyint(4) DEFAULT NULL,
    `kdurut` tinyint(4) DEFAULT NULL,
    `kode_rekening` TEXT DEFAULT NULL,
    `nmrek6` TEXT DEFAULT NULL,
    `rekening_display` TEXT DEFAULT NULL,
    `satuan123` TEXT DEFAULT NULL,
    `singkat_sat1` TEXT DEFAULT NULL,
    `singkat_sat2` TEXT DEFAULT NULL,
    `singkat_sat3` TEXT DEFAULT NULL,
    `status_data` TEXT DEFAULT NULL,
    `status_dokumen` TEXT DEFAULT NULL,
    `status_pelaksanaan` TEXT DEFAULT NULL,
    `tahun` TEXT DEFAULT NULL,
    `uraian_belanja` TEXT DEFAULT NULL,
    `uraian_ssh` TEXT DEFAULT NULL,
    `uraian_sumberdana` TEXT DEFAULT NULL,
    `volume_1` TEXT DEFAULT NULL,
    `volume_2` TEXT DEFAULT NULL,
    `volume_3` TEXT DEFAULT NULL,
    `volume_renja1` TEXT DEFAULT NULL,
    `volume_renja2` TEXT DEFAULT NULL,
    `volume_renja3` TEXT DEFAULT NULL,
    `tahun_anggaran` year(4) NOT NULL,
    `active` tinyint(4) NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
ALTER TABLE `data_rincian_fmis`
  ADD PRIMARY KEY (`id`);
ALTER TABLE `data_rincian_fmis`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;