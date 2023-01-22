ALTER TABLE `data_rumus_indikator` CHANGE `id` `id` INT(11) NOT NULL;
ALTER TABLE `data_rumus_indikator` DROP PRIMARY KEY;
ALTER TABLE `data_rumus_indikator` ADD `id_asli` int(11)  AFTER `id`;
ALTER TABLE `data_rumus_indikator` CHANGE `id_asli` `id_asli` INT(11) NOT NULL AUTO_INCREMENT, add PRIMARY KEY (`id_asli`);