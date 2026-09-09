-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jun 30, 2025
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

-- Database: `db_dss`
-- --------------------------------------------------------
-- SPK Pemilihan Mentor Program Magang Internal
-- Metode: AHP (Pembobotan) + SAW (Perankingan)
-- --------------------------------------------------------

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

-- --------------------------------------------------------
-- Drop existing tables (in dependency order)
-- --------------------------------------------------------

DROP TABLE IF EXISTS `ahp_comparisons`;
DROP TABLE IF EXISTS `saw_evaluations`;
DROP TABLE IF EXISTS `saw_criterias`;
DROP TABLE IF EXISTS `saw_alternatives`;
DROP TABLE IF EXISTS `saw_users`;

-- --------------------------------------------------------
-- Table structure for table `saw_alternatives`
-- Menyimpan data alternatif (calon mentor)
-- --------------------------------------------------------

CREATE TABLE `saw_alternatives` (
  `id_alternative` smallint(5) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  PRIMARY KEY (`id_alternative`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `saw_alternatives`
--

INSERT INTO `saw_alternatives` (`id_alternative`, `name`) VALUES
(1, 'Dewi Andrayani'),
(2, 'Haykal Azizi'),
(3, 'Helmi Aziz'),
(4, 'Sheva Al Nascutha'),
(5, 'Mahasiswa 5'),
(6, 'Mahasiswa 6'),
(7, 'Mahasiswa 7'),
(8, 'Mahasiswa 8');

-- --------------------------------------------------------
-- Table structure for table `saw_criterias`
-- Menyimpan kriteria beserta bobot hasil AHP dan atribut
-- --------------------------------------------------------

CREATE TABLE `saw_criterias` (
  `id_criteria` tinyint(3) UNSIGNED NOT NULL AUTO_INCREMENT,
  `criteria` varchar(100) NOT NULL,
  `weight` float NOT NULL,
  `attribute` set('benefit','cost') DEFAULT NULL,
  PRIMARY KEY (`id_criteria`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `saw_criterias`
-- Bobot diperoleh dari perhitungan AHP (CR = 0.019, Konsisten)
-- Ref: Jurnal Penerapan AHP dan SAW dalam Menentukan Penerima Beasiswa
--

INSERT INTO `saw_criterias` (`id_criteria`, `criteria`, `weight`, `attribute`) VALUES
(1, 'English Proficiency Test (EPT)', 0.7231, 'benefit'),
(2, 'Indeks Prestasi Kumulatif (IPK)', 0.2157, 'benefit'),
(3, 'Jurusan', 0.0612, 'cost');

-- --------------------------------------------------------
-- Table structure for table `ahp_comparisons`
-- Menyimpan matriks perbandingan berpasangan AHP
-- --------------------------------------------------------

CREATE TABLE `ahp_comparisons` (
  `id_criteria_row` tinyint(3) UNSIGNED NOT NULL,
  `id_criteria_col` tinyint(3) UNSIGNED NOT NULL,
  `value` float NOT NULL,
  PRIMARY KEY (`id_criteria_row`, `id_criteria_col`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `ahp_comparisons`
-- Matriks perbandingan berpasangan menggunakan Skala Saaty 1-9
--

INSERT INTO `ahp_comparisons` (`id_criteria_row`, `id_criteria_col`, `value`) VALUES
(1, 1, 1),      (1, 2, 5),      (1, 3, 9),
(2, 1, 0.2),    (2, 2, 1),      (2, 3, 5),
(3, 1, 0.1111), (3, 2, 0.2),    (3, 3, 1);

-- --------------------------------------------------------
-- Table structure for table `saw_evaluations`
-- Menyimpan nilai penilaian setiap alternatif terhadap kriteria
-- --------------------------------------------------------

CREATE TABLE `saw_evaluations` (
  `id_alternative` smallint(5) UNSIGNED NOT NULL,
  `id_criteria` tinyint(3) UNSIGNED NOT NULL,
  `value` float NOT NULL,
  PRIMARY KEY (`id_alternative`, `id_criteria`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `saw_evaluations`
-- Format: (id_alternative, id_criteria, value)
--

INSERT INTO `saw_evaluations` (`id_alternative`, `id_criteria`, `value`) VALUES
-- A1: Dewi Andrayani (Mahasiswa 8)
(1, 1, 4),  (1, 2, 3),  (1, 3, 3),
-- A2: Haykal Azizi (Mahasiswa 2)
(2, 1, 4),  (2, 2, 3),  (2, 3, 5),
-- A3: Helmi Aziz (Mahasiswa 4)
(3, 1, 5),  (3, 2, 5),  (3, 3, 3),
-- A4: Sheva Al Nascutha (Mahasiswa 5 di Jurnal)
(4, 1, 3),  (4, 2, 3),  (4, 3, 3),
-- A5: Mahasiswa 5 (Mahasiswa 1 di Jurnal)
(5, 1, 1),  (5, 2, 3),  (5, 3, 2),
-- A6: Mahasiswa 6
(6, 1, 2),  (6, 2, 2),  (6, 3, 1),
-- A7: Mahasiswa 7
(7, 1, 1),  (7, 2, 4),  (7, 3, 2),
-- A8: Mahasiswa 8 (Mahasiswa 3 di Jurnal)
(8, 1, 2),  (8, 2, 3),  (8, 3, 4);

-- --------------------------------------------------------
-- Table structure for table `saw_users`
-- Menyimpan data pengguna aplikasi
-- --------------------------------------------------------

CREATE TABLE `saw_users` (
  `id_user` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(50) DEFAULT NULL,
  `password` varchar(150) DEFAULT NULL,
  PRIMARY KEY (`id_user`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `saw_users`
-- Default: admin / admin (MD5)
--

INSERT INTO `saw_users` (`id_user`, `username`, `password`) VALUES
(1, 'admin', '21232f297a57a5a743894a0e4a801fc3');

-- --------------------------------------------------------

COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
