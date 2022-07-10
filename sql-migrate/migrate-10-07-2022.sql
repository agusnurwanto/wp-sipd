ALTER TABLE `data_rka` ADD `substeks` TEXT NULL AFTER `subs_bl_teks`;
ALTER TABLE `data_rka` ADD `id_dana` int(11) NULL AFTER `subs_bl_teks`;
ALTER TABLE `data_rka` ADD `nama_dana` TEXT NULL AFTER `subs_bl_teks`;
ALTER TABLE `data_rka` ADD `is_paket` tinyint(4) NULL AFTER `subs_bl_teks`;
ALTER TABLE `data_rka` ADD `kode_dana` varchar(30) NULL AFTER `subs_bl_teks`;
ALTER TABLE `data_rka` ADD `subtitle_teks` TEXT NULL AFTER `subs_bl_teks`;