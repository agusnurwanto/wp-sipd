ALTER TABLE data_rumus_indikator DROP CONSTRAINT PRIMARY;
ALTER TABLE `data_rumus_indikator` ADD `id_asli` int(11)  AFTER `id`;
ALTER TABLE `data_rumus_indikator` ADD PRIMARY KEY (`id_asli`);
ALTER TABLE `data_rumus_indikator` MODIFY `id_asli` int(11) NOT NULL AUTO_INCREMENT;