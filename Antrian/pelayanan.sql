-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jun 30, 2025 at 01:13 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `pelayanan`
--

-- --------------------------------------------------------

--
-- Table structure for table `antrian`
--

CREATE TABLE `antrian` (
  `id` int(11) NOT NULL,
  `nomor` varchar(10) NOT NULL,
  `loket` enum('UMUM','KIA','LANSIA') NOT NULL,
  `status` enum('Pending','Calling','Process','Success','Skip') NOT NULL DEFAULT 'Pending',
  `terdaftar_pada` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `antrian`
--

INSERT INTO `antrian` (`id`, `nomor`, `loket`, `status`, `terdaftar_pada`, `updated_at`) VALUES
(5, 'U001', 'UMUM', 'Success', '2025-06-29 19:11:47', '2025-06-29 22:57:03'),
(6, 'U002', 'UMUM', 'Success', '2025-06-29 22:57:06', '2025-06-29 23:06:07'),
(7, 'U003', 'UMUM', 'Success', '2025-06-29 23:06:10', '2025-06-29 23:14:21'),
(8, 'K001', 'KIA', 'Success', '2025-06-29 23:06:14', '2025-06-29 23:15:07'),
(9, 'L001', 'LANSIA', 'Success', '2025-06-29 23:06:16', '2025-06-29 23:15:16'),
(10, 'U004', 'UMUM', 'Success', '2025-06-29 23:13:42', '2025-06-29 23:14:29'),
(11, 'U005', 'UMUM', 'Success', '2025-06-29 23:13:58', '2025-06-29 23:14:34'),
(12, 'U006', 'UMUM', 'Success', '2025-06-29 23:14:06', '2025-06-29 23:14:38');

-- --------------------------------------------------------

--
-- Table structure for table `user`
--

CREATE TABLE `user` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `username` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` int(11) NOT NULL DEFAULT 2
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user`
--

INSERT INTO `user` (`id`, `name`, `username`, `password`, `role`) VALUES
(3, 'nova', 'nova', '$2y$10$lJ7/b7gPCVymIpCCci5Ncu1QveAkseKNy9x6TT/Utu8eGa/12sqW.', 2);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `antrian`
--
ALTER TABLE `antrian`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_status_tanggal` (`status`,`terdaftar_pada`),
  ADD KEY `idx_loket_tanggal` (`loket`,`terdaftar_pada`);

--
-- Indexes for table `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `antrian`
--
ALTER TABLE `antrian`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `user`
--
ALTER TABLE `user`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
