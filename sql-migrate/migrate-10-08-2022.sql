--
-- Add New Column for table `data_jadwal_lokal`
--
ALTER TABLE `data_jadwal_lokal`
  ADD `id_tipe` int(11) NOT NULL;

--
-- Table structure for table `data_tipe_perencanaan`
--

CREATE TABLE `data_tipe_perencanaan` (
  `id` int(11) NOT NULL,
  `nama_tipe` varchar(64) NOT NULL,
  `keterangan_tipe` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `data_tipe_perencanaan`
--

INSERT INTO `data_tipe_perencanaan` (`id`, `nama_tipe`, `keterangan_tipe`) VALUES
(1, 'rpjpd\r\n', ''),
(2, 'rpjm', ''),
(3, 'rpd', ''),
(4, 'renstra', ''),
(5, 'renja', ''),
(6, 'penganggaran', '');

--
-- Indexes for table `data_tipe_perencanaan`
--
ALTER TABLE `data_tipe_perencanaan`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for table `data_tipe_perencanaan`
--
ALTER TABLE `data_tipe_perencanaan`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;