--
-- Table structure for table `data_jadwal_lokal`
--

CREATE TABLE `data_jadwal_lokal` (
  `id_jadwal_lokal` int(11) NOT NULL,
  `nama` varchar(64) DEFAULT NULL,
  `waktu_awal` datetime DEFAULT NULL,
  `waktu_akhir` datetime DEFAULT NULL,
  `status` int(11) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Indexes for table `data_jadwal_lokal`
--
ALTER TABLE `data_jadwal_lokal`
  ADD PRIMARY KEY (`id_jadwal_lokal`);

--
-- AUTO_INCREMENT for table `data_jadwal_lokal`
--
ALTER TABLE `data_jadwal_lokal`
  MODIFY `id_jadwal_lokal` int(11) NOT NULL AUTO_INCREMENT;