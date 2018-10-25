-- phpMyAdmin SQL Dump
-- version 4.4.14
-- http://www.phpmyadmin.net
--
-- Host: 127.0.0.1
-- Generation Time: Oct 25, 2018 at 06:55 AM
-- Server version: 5.6.26
-- PHP Version: 5.6.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `ugmpress`
--

-- --------------------------------------------------------

--
-- Table structure for table `author`
--

CREATE TABLE IF NOT EXISTS `author` (
  `author_id` mediumint(9) NOT NULL,
  `work_unit_id` mediumint(9) DEFAULT NULL,
  `institute_id` mediumint(9) DEFAULT NULL,
  `author_nip` varchar(256) NOT NULL,
  `author_name` varchar(256) NOT NULL,
  `author_degree_front` varchar(256) NOT NULL,
  `author_degree_back` varchar(256) NOT NULL,
  `author_latest_education` enum('s1','s2','s3','other') NOT NULL,
  `author_address` text NOT NULL,
  `author_contact` varchar(20) NOT NULL,
  `author_email` varchar(256) NOT NULL,
  `bank_id` varchar(4) DEFAULT NULL,
  `author_saving_num` varchar(30) NOT NULL,
  `heir_name` varchar(256) NOT NULL,
  `user_id` mediumint(9) DEFAULT NULL,
  `author_ktp` varchar(256) NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `author`
--

INSERT INTO `author` (`author_id`, `work_unit_id`, `institute_id`, `author_nip`, `author_name`, `author_degree_front`, `author_degree_back`, `author_latest_education`, `author_address`, `author_contact`, `author_email`, `bank_id`, `author_saving_num`, `heir_name`, `user_id`, `author_ktp`) VALUES
(1, 4, 7, '123451', 'Penulis Pertama', '', 'S.T., M.T., Ph.D.', 's1', 'Yogyakarta', '088825497878', 'penulis1@gmail.com', '014', '5228452154', 'Anaknya Penulis 1', 7, 'KTP_Penulis_Pertama_20181023032311.jpg'),
(2, 4, 5, '123452', 'Penulis Kedua', 'Drs.', '', 's1', 'Jakarta', '0815799965251', 'penulis2@gmail.com', '009', '7541242124', 'Anaknya Penulis 2', 8, 'KTP_Penulis_Kedua_20181023032951.jpg'),
(8, 4, 3, '123453', 'Penulis Ketiga', '', 'S.H.', 's1', 'Bandung', '08754541212', 'penulis3@gmail.com', '061', '55521001212', 'Anaknya Penulis 3', 9, 'KTP_Penulis_Ketiga_20181023043740.jpg'),
(9, 4, 2, '123454', 'Penulis Reviewer Pertama', '', '', 'other', 'Tegal', '0811445454545', 'penulisreviewer1@gmail.com', '121', '666894613332', 'Anaknya Penulis Reviewer 1', 15, ''),
(10, 3, 6, '123455', 'Penulis Reviewer Kedua', 'Ir.', '', 's1', 'Yogyakarta', '087855512345', 'penulisreviewer2@gmail.com', '167', '7877854212', 'Anaknya Penulis Reviewer 2', 16, '');

-- --------------------------------------------------------

--
-- Table structure for table `bank`
--

CREATE TABLE IF NOT EXISTS `bank` (
  `bank_name` varchar(256) NOT NULL,
  `bank_id` varchar(4) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `bank`
--

INSERT INTO `bank` (`bank_name`, `bank_id`) VALUES
('BANK BRI', '002'),
('BANK EKSPOR INDONESIA', '003'),
('BANK MANDIRI', '008'),
('BANK BNI', '009'),
('BANK DANAMON', '011'),
('PERMATA BANK', '013'),
('BANK BCA', '014'),
('BANK BII', '016'),
('BANK PANIN', '019'),
('BANK ARTA NIAGA KENCANA', '020'),
('BANK NIAGA', '022'),
('BANK BUANA IND', '023'),
('BANK LIPPO', '026'),
('BANK NISP', '028'),
('AMERICAN EXPRESS BANK LTD', '030'),
('CITIBANK N.A.', '031'),
('JP. MORGAN CHASE BANK, N.A.', '032'),
('BANK OF AMERICA, N.A', '033'),
('ING INDONESIA BANK', '034'),
('BANK MULTICOR TBK.', '036'),
('BANK ARTHA GRAHA', '037'),
('BANK CREDIT AGRICOLE INDOSUEZ', '039'),
('THE BANGKOK BANK COMP. LTD', '040'),
('THE HONGKONG & SHANGHAI B.C.', '041'),
('THE BANK OF TOKYO MITSUBISHI UFJ LTD', '042'),
('BANK SUMITOMO MITSUI INDONESIA', '045'),
('BANK DBS INDONESIA', '046'),
('BANK RESONA PERDANIA', '047'),
('BANK MIZUHO INDONESIA', '048'),
('STANDARD CHARTERED BANK', '050'),
('BANK ABN AMRO', '052'),
('BANK KEPPEL TATLEE BUANA', '053'),
('BANK CAPITAL INDONESIA, TBK.', '054'),
('BANK BNP PARIBAS INDONESIA', '057'),
('BANK UOB INDONESIA', '058'),
('KOREA EXCHANGE BANK DANAMON', '059'),
('RABOBANK INTERNASIONAL INDONESIA', '060'),
('ANZ PANIN BANK', '061'),
('DEUTSCHE BANK AG.', '067'),
('BANK WOORI INDONESIA', '068'),
('BANK OF CHINA LIMITED', '069'),
('BANK BUMI ARTA', '076'),
('BANK EKONOMI', '087'),
('BANK ANTARDAERAH', '088'),
('BANK HAGA', '089'),
('BANK IFI', '093'),
('BANK CENTURY, TBK.', '095'),
('BANK MAYAPADA', '097'),
('BANK JABAR', '110'),
('BANK DKI', '111'),
('BPD DIY', '112'),
('BANK JATENG', '113'),
('BANK JATIM', '114'),
('BPD JAMBI', '115'),
('BPD ACEH', '116'),
('BANK SUMUT', '117'),
('BANK NAGARI', '118'),
('BANK RIAU', '119'),
('BANK SUMSEL', '120'),
('BANK LAMPUNG', '121'),
('BPD KALSEL', '122'),
('BPD KALIMANTAN BARAT', '123'),
('BPD KALTIM', '124'),
('BPD KALTENG', '125'),
('BPD SULSEL', '126'),
('BANK SULUT', '127'),
('BPD NTB', '128'),
('BPD BALI', '129'),
('BANK NTT', '130'),
('BANK MALUKU', '131'),
('BPD PAPUA', '132'),
('BANK BENGKULU', '133'),
('BPD SULAWESI TENGAH', '134'),
('BANK SULTRA', '135'),
('BANK NUSANTARA PARAHYANGAN', '145'),
('BANK SWADESI', '146'),
('BANK MUAMALAT', '147'),
('BANK MESTIKA', '151'),
('BANK METRO EXPRESS', '152'),
('BANK SHINTA INDONESIA', '153'),
('BANK MASPION', '157'),
('BANK HAGAKITA', '159'),
('BANK GANESHA', '161'),
('BANK WINDU KENTJANA', '162'),
('HALIM INDONESIA BANK', '164'),
('BANK HARMONI INTERNATIONAL', '166'),
('BANK KESAWAN', '167'),
('BANK TABUNGAN NEGARA (PERSERO)', '200'),
('BANK HIMPUNAN SAUDARA 1906, TBK .', '212'),
('BANK TABUNGAN PENSIUNAN NASIONAL', '213'),
('BANK SWAGUNA', '405'),
('BANK JASA ARTA', '422'),
('BANK MEGA', '426'),
('BANK JASA JAKARTA', '427'),
('BANK BUKOPIN', '441'),
('BANK SYARIAH MANDIRI', '451'),
('BANK BISNIS INTERNASIONAL', '459'),
('BANK SRI PARTHA', '466'),
('BANK JASA JAKARTA', '472'),
('BANK BINTANG MANUNGGAL', '484'),
('BANK BUMIPUTERA', '485'),
('BANK YUDHA BHAKTI', '490'),
('BANK MITRANIAGA', '491'),
('BANK AGRO NIAGA', '494'),
('BANK INDOMONEX', '498'),
('BANK ROYAL INDONESIA', '501'),
('BANK ALFINDO', '503'),
('BANK SYARIAH MEGA', '506'),
('BANK INA PERDANA', '513'),
('BANK HARFA', '517'),
('PRIMA MASTER BANK', '520'),
('BANK PERSYARIKATAN INDONESIA', '521'),
('BANK DIPO INTERNATIONAL', '523'),
('BANK AKITA', '525'),
('LIMAN INTERNATIONAL BANK', '526'),
('ANGLOMAS INTERNASIONAL BANK', '531'),
('BANK KESEJAHTERAAN EKONOMI', '535'),
('BANK UIB', '536'),
('BANK ARTOS IND', '542'),
('BANK PURBA DANARTA', '547'),
('BANK MULTI ARTA SENTOSA', '548'),
('BANK MAYORA', '553'),
('BANK INDEX SELINDO', '555'),
('BANK EKSEKUTIF', '558'),
('CENTRATAMA NASIONAL BANK', '559'),
('BANK FAMA INTERNASIONAL', '562'),
('BANK SINAR HARAPAN BALI', '564'),
('BANK VICTORIA INTERNATIONAL', '566'),
('BANK HARDA', '567'),
('BANK FINCONESIA', '945'),
('BANK MERINCORP', '946'),
('BANK MAYBANK INDOCORP', '947'),
('BANK OCBC – INDONESIA', '948'),
('BANK CHINA TRUST INDONESIA', '949'),
('BANK COMMONWEALTH', '950');

-- --------------------------------------------------------

--
-- Table structure for table `book`
--

CREATE TABLE IF NOT EXISTS `book` (
  `book_id` mediumint(9) NOT NULL,
  `draft_id` mediumint(9) DEFAULT NULL,
  `book_code` varchar(256) NOT NULL,
  `book_title` varchar(256) NOT NULL,
  `book_edition` varchar(256) NOT NULL,
  `isbn` varchar(256) NOT NULL,
  `book_file` varchar(256) DEFAULT NULL,
  `published_date` date NOT NULL,
  `printing_type` enum('p','o') NOT NULL DEFAULT 'o',
  `serial_num` mediumint(9) NOT NULL,
  `serial_num_per_year` mediumint(9) NOT NULL,
  `copies_num` varchar(256) NOT NULL,
  `book_notes` text NOT NULL,
  `is_reprint` enum('y','n') NOT NULL DEFAULT 'n'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `category`
--

CREATE TABLE IF NOT EXISTS `category` (
  `category_id` mediumint(9) NOT NULL,
  `category_name` varchar(256) NOT NULL,
  `category_year` year(4) NOT NULL,
  `category_note` text NOT NULL,
  `date_open` date NOT NULL,
  `date_close` date NOT NULL,
  `category_status` enum('y','n') NOT NULL DEFAULT 'y'
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `category`
--

INSERT INTO `category` (`category_id`, `category_name`, `category_year`, `category_note`, `date_open`, `date_close`, `category_status`) VALUES
(1, 'Hibah UGM', 2018, 'Hibah untuk UGM 2018', '2018-10-22', '2018-11-10', 'y'),
(2, 'Hibah UGM Press', 2018, 'Hibah untuk staff UGM Press', '2018-10-24', '2018-11-10', 'y'),
(3, 'Hibah Umum', 2018, 'Hibah untuk umum', '2018-10-30', '2018-12-13', 'y');

-- --------------------------------------------------------

--
-- Table structure for table `draft`
--

CREATE TABLE IF NOT EXISTS `draft` (
  `draft_id` mediumint(9) NOT NULL,
  `category_id` mediumint(9) DEFAULT NULL,
  `theme_id` mediumint(9) DEFAULT NULL,
  `draft_title` varchar(256) NOT NULL,
  `draft_file` varchar(256) DEFAULT NULL,
  `entry_date` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `finish_date` timestamp NULL DEFAULT NULL,
  `print_date` timestamp NULL DEFAULT NULL,
  `is_review` enum('y','n') NOT NULL DEFAULT 'n',
  `review_start_date` datetime NOT NULL,
  `review_end_date` datetime NOT NULL,
  `review1_file` varchar(255) NOT NULL,
  `review1_upload_date` timestamp NULL DEFAULT NULL,
  `review1_last_upload` varchar(255) NOT NULL,
  `review1_notes` text NOT NULL,
  `review1_notes_author` text NOT NULL,
  `review1_deadline` datetime NOT NULL,
  `review1_flag` enum('y','n') DEFAULT NULL,
  `review2_file` varchar(255) NOT NULL,
  `review2_upload_date` timestamp NULL DEFAULT NULL,
  `review2_notes` text NOT NULL,
  `review2_notes_author` text NOT NULL,
  `review2_deadline` datetime NOT NULL,
  `review2_flag` enum('y','n') DEFAULT NULL,
  `review_status` text NOT NULL,
  `is_edit` enum('y','n') NOT NULL DEFAULT 'n',
  `edit_start_date` datetime NOT NULL,
  `edit_end_date` datetime NOT NULL,
  `edit_file` varchar(255) NOT NULL,
  `edit_upload_date` timestamp NULL DEFAULT NULL,
  `edit_last_upload` varchar(255) NOT NULL,
  `edit_notes` text NOT NULL,
  `edit_notes_author` text NOT NULL,
  `edit_deadline` datetime NOT NULL,
  `edit_status` text NOT NULL,
  `is_layout` enum('y','n') NOT NULL DEFAULT 'n',
  `layout_start_date` datetime NOT NULL,
  `layout_end_date` datetime NOT NULL,
  `layout_file` varchar(255) NOT NULL,
  `layout_upload_date` timestamp NULL DEFAULT NULL,
  `layout_last_upload` varchar(255) NOT NULL,
  `layout_notes` text NOT NULL,
  `layout_notes_author` text NOT NULL,
  `layout_deadline` datetime NOT NULL,
  `cover_file` varchar(255) NOT NULL,
  `cover_upload_date` timestamp NULL DEFAULT NULL,
  `cover_last_upload` varchar(255) NOT NULL,
  `cover_notes` text NOT NULL,
  `cover_notes_author` text NOT NULL,
  `layout_status` text NOT NULL,
  `is_proofread` enum('y','n') NOT NULL DEFAULT 'n',
  `proofread_start_date` datetime NOT NULL,
  `proofread_end_date` datetime NOT NULL,
  `proofread_file` varchar(255) NOT NULL,
  `proofread_upload_date` datetime DEFAULT NULL,
  `proofread_last_upload` varchar(255) NOT NULL,
  `proofread_notes` text NOT NULL,
  `proofread_notes_author` text NOT NULL,
  `proofread_status` text NOT NULL,
  `draft_status` int(11) NOT NULL,
  `draft_notes` text NOT NULL,
  `kriteria1_reviewer1` text NOT NULL,
  `kriteria2_reviewer1` text NOT NULL,
  `kriteria3_reviewer1` text NOT NULL,
  `kriteria4_reviewer1` text NOT NULL,
  `nilai_reviewer1` text NOT NULL,
  `kriteria1_reviewer2` text NOT NULL,
  `kriteria2_reviewer2` text NOT NULL,
  `kriteria3_reviewer2` text NOT NULL,
  `kriteria4_reviewer2` text NOT NULL,
  `nilai_reviewer2` text NOT NULL,
  `review2_last_upload` varchar(255) NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=16 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `draft`
--

INSERT INTO `draft` (`draft_id`, `category_id`, `theme_id`, `draft_title`, `draft_file`, `entry_date`, `finish_date`, `print_date`, `is_review`, `review_start_date`, `review_end_date`, `review1_file`, `review1_upload_date`, `review1_last_upload`, `review1_notes`, `review1_notes_author`, `review1_deadline`, `review1_flag`, `review2_file`, `review2_upload_date`, `review2_notes`, `review2_notes_author`, `review2_deadline`, `review2_flag`, `review_status`, `is_edit`, `edit_start_date`, `edit_end_date`, `edit_file`, `edit_upload_date`, `edit_last_upload`, `edit_notes`, `edit_notes_author`, `edit_deadline`, `edit_status`, `is_layout`, `layout_start_date`, `layout_end_date`, `layout_file`, `layout_upload_date`, `layout_last_upload`, `layout_notes`, `layout_notes_author`, `layout_deadline`, `cover_file`, `cover_upload_date`, `cover_last_upload`, `cover_notes`, `cover_notes_author`, `layout_status`, `is_proofread`, `proofread_start_date`, `proofread_end_date`, `proofread_file`, `proofread_upload_date`, `proofread_last_upload`, `proofread_notes`, `proofread_notes_author`, `proofread_status`, `draft_status`, `draft_notes`, `kriteria1_reviewer1`, `kriteria2_reviewer1`, `kriteria3_reviewer1`, `kriteria4_reviewer1`, `nilai_reviewer1`, `kriteria1_reviewer2`, `kriteria2_reviewer2`, `kriteria3_reviewer2`, `kriteria4_reviewer2`, `nilai_reviewer2`, `review2_last_upload`) VALUES
(1, 1, 3, 'Contoh Lembar Kerja Ditolak', 'Draft_Lembar_Kerja_20181023232733.docx', '2018-10-23 16:27:33', NULL, NULL, 'n', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '', NULL, '', '', '', '0000-00-00 00:00:00', NULL, '', NULL, '', '', '0000-00-00 00:00:00', NULL, '', 'n', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '', NULL, '', '', '', '0000-00-00 00:00:00', '', 'n', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '', NULL, '', '', '', '0000-00-00 00:00:00', '', NULL, '', '', '', '', 'n', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '', NULL, '', '', '', '', 2, '', '', '', '', '', '', '', '', '', '', '', ''),
(2, 1, 4, 'Contoh Lembar Kerja Diterima', 'Contoh_Lembar_Kerja_Diterima_20181023141212.docx', '2018-10-23 07:12:12', NULL, NULL, 'n', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '', NULL, '', '', '<p>nice<br></p>', '0000-00-00 00:00:00', NULL, '', NULL, '', '', '0000-00-00 00:00:00', NULL, '', 'n', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '', NULL, '', '', '', '0000-00-00 00:00:00', '', 'n', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '', NULL, '', '', '', '0000-00-00 00:00:00', '', NULL, '', '', '', '', 'n', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '', NULL, '', '', '', '', 1, '', '', '', '', '', '', '', '', '', '', '', ''),
(3, 2, 6, 'Review Ditolak', 'Reviewer_Ditolak_20181024002020.docx', '2018-10-23 17:20:20', NULL, NULL, 'n', '2018-10-24 00:47:27', '2018-10-24 01:14:21', 'Review_Ditolak_review1_file_20181024005632.docx', '2018-10-23 17:56:32', 'author', '<p>Sudah baik</p>', '<p>Baik<br></p>', '2018-12-23 00:47:28', 'y', 'Review_Ditolak_review2_file_20181024011341.docx', '2018-10-23 18:13:41', '<p>Kurang baik<br></p>', '<p>Aduh<br></p>', '2018-12-23 00:47:28', 'n', '<p>Draft ditolak<br></p>', 'n', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '', NULL, '', '', '', '0000-00-00 00:00:00', '', 'n', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '', NULL, '', '', '', '0000-00-00 00:00:00', '', NULL, '', '', '', '', 'n', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '', NULL, '', '', '', '', 99, '', '<p>Sudah baik<br></p>', '<p>Sudah baik<br></p>', '<p>Sudah baik</p>', '<p>Sudah baik</p>', '4,5,4,5', '<p>Kurang baik<br></p>', '<p>Kurang baik<br></p>', '<p>Kurang baik<br></p>', '<p>Kurang baik<br></p>', '1,1,1,1', 'author'),
(7, 1, 1, 'Proses Lembar Kerja', 'Proses_Lembar_Kerja_20181024182451.docx', '2018-10-24 11:24:51', NULL, NULL, 'n', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '', NULL, '', '', '', '0000-00-00 00:00:00', NULL, '', NULL, '', '', '0000-00-00 00:00:00', NULL, '', 'n', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '', NULL, '', '', '', '0000-00-00 00:00:00', '', 'n', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '', NULL, '', '', '', '0000-00-00 00:00:00', '', NULL, '', '', '', '', 'n', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '', NULL, '', '', '', '', 0, '', '', '', '', '', '', '', '', '', '', '', ''),
(8, 1, 1, 'Proses Review', 'Proses_Review_20181024182833.docx', '2018-10-24 11:28:33', NULL, NULL, 'n', '2018-10-24 18:49:41', '0000-00-00 00:00:00', '', NULL, '', '', '<p>baik<br></p>', '2018-12-23 18:49:41', 'y', '', NULL, '<p>baik<br></p>', '', '2018-12-23 18:49:41', 'y', '', 'n', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '', NULL, '', '', '', '0000-00-00 00:00:00', '', 'n', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '', NULL, '', '', '', '0000-00-00 00:00:00', '', NULL, '', '', '', '', 'n', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '', NULL, '', '', '', '', 4, '', '', '', '', '', '5,4,4,4', '', '', '', '', '4,4,4,5', ''),
(9, 1, 3, 'Review Diterima', 'Review_Diterima_20181024190256.docx', '2018-10-24 12:02:56', NULL, NULL, 'y', '2018-10-24 19:05:43', '2018-10-24 19:05:25', '', NULL, '', '', '', '2018-12-23 19:05:43', NULL, '', NULL, '', '', '2018-12-23 19:05:43', NULL, '<p>Review Disetujui<br></p>', 'n', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '', NULL, '', '', '', '0000-00-00 00:00:00', '', 'n', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '', NULL, '', '', '', '0000-00-00 00:00:00', '', NULL, '', '', '', '', 'n', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '', NULL, '', '', '', '', 5, '', '', '', '', '', '', '', '', '', '', '', ''),
(10, 1, 4, 'Proses Edit', 'Proses_Edit_20181024192100.docx', '2018-10-24 12:21:01', NULL, NULL, 'y', '2018-10-24 19:26:07', '2018-10-24 19:25:58', '', NULL, '', '', '', '2018-12-23 19:26:07', NULL, '', NULL, '', '', '2018-12-23 19:26:07', NULL, '<p>Review Disetujui<br></p>', 'n', '2018-10-24 19:26:39', '0000-00-00 00:00:00', 'Proses_Edit_edit_file_20181024192805.docx', '2018-10-24 12:28:05', 'editor', '<p>Mantab<br></p>', '<p>Oke<br></p>', '2018-12-23 19:26:39', '', 'n', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '', NULL, '', '', '', '0000-00-00 00:00:00', '', NULL, '', '', '', '', 'n', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '', NULL, '', '', '', '', 6, '', '', '', '', '', '', '', '', '', '', '', ''),
(11, 3, 6, 'Edit Ditolak', 'Edit_Ditolak_20181024203743.docx', '2018-10-24 13:37:43', NULL, NULL, 'y', '2018-10-24 20:42:25', '2018-10-24 20:40:39', '', NULL, '', '', '', '2018-12-23 20:42:25', NULL, '', NULL, '', '', '2018-12-23 20:42:25', NULL, '<p>Review lanjut<br></p>', 'n', '2018-10-24 20:42:43', '2018-10-24 20:42:36', '', NULL, '', '', '', '2018-12-23 20:42:43', '<p>edit ditolak<br></p>', 'n', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '', NULL, '', '', '', '0000-00-00 00:00:00', '', NULL, '', '', '', '', 'n', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '', NULL, '', '', '', '', 99, '', '', '', '', '', '', '', '', '', '', '', ''),
(12, 1, 6, 'Edit Diterima', 'Edit_Diterima_20181024204356.docx', '2018-10-24 13:43:56', NULL, NULL, 'y', '2018-10-24 20:44:28', '2018-10-24 20:44:12', '', NULL, '', '', '', '2018-12-23 20:44:28', NULL, '', NULL, '', '', '2018-12-23 20:44:28', NULL, '<p>Review diterima<br></p>', 'y', '2018-10-24 20:44:48', '2018-10-24 20:51:31', 'Edit_Diterima_edit_file_20181024205100.docx', '2018-10-24 13:51:00', 'editor', '<p>mantab<br></p>', '<p>Baik<br></p>', '2018-12-23 20:44:48', '<p>Edit Diterima<br></p>', 'n', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '', NULL, '', '', '', '0000-00-00 00:00:00', '', NULL, '', '', '', '', 'n', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '', NULL, '', '', '', '', 7, '', '', '', '', '', '', '', '', '', '', '', ''),
(13, 1, 3, 'Proses Layout', 'Proses_Layout_20181024211202.docx', '2018-10-24 14:12:02', NULL, NULL, 'y', '2018-10-24 21:12:31', '2018-10-24 21:12:15', '', NULL, '', '', '', '2018-12-23 21:12:31', NULL, '', NULL, '', '', '2018-12-23 21:12:31', NULL, '', 'y', '2018-10-24 21:12:45', '2018-10-24 21:12:39', '', NULL, '', '<p>ok<br></p>', '', '2018-12-23 21:12:45', '<p>Edit Disetujui<br></p>', 'n', '2018-10-24 21:14:26', '0000-00-00 00:00:00', 'Proses_Layout_layout_file_20181024211537.docx', '2018-10-24 14:15:37', 'layouter', '<p>Baik<br></p>', '<p>Baik<br></p>', '2018-12-23 21:14:26', 'Proses_Layout_cover_file_20181024211754.pdf', '2018-10-24 14:19:06', 'layouter', '<p>Baik<br></p>', '<p>Baik<br></p>', '', 'n', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '', NULL, '', '', '', '', 10, '', '', '', '', '', '', '', '', '', '', '', ''),
(15, 1, 5, 'Layout Ditolak', 'Layout_Ditolak_20181025115140.docx', '2018-10-25 04:51:40', NULL, NULL, 'n', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '', NULL, '', '', '', '0000-00-00 00:00:00', NULL, '', NULL, '', '', '0000-00-00 00:00:00', NULL, '', 'n', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '', NULL, '', '', '', '0000-00-00 00:00:00', '', 'n', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '', NULL, '', '', '', '0000-00-00 00:00:00', '', NULL, '', '', '', '', 'n', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '', NULL, '', '', '', '', 0, '', '', '', '', '', '', '', '', '', '', '', '');

-- --------------------------------------------------------

--
-- Table structure for table `draft_author`
--

CREATE TABLE IF NOT EXISTS `draft_author` (
  `draft_author_id` mediumint(9) NOT NULL,
  `draft_id` mediumint(9) DEFAULT NULL,
  `author_id` mediumint(9) DEFAULT NULL,
  `draft_author_status` int(1) NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=20 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `draft_author`
--

INSERT INTO `draft_author` (`draft_author_id`, `draft_id`, `author_id`, `draft_author_status`) VALUES
(1, 1, 1, 1),
(2, 1, 2, 0),
(3, 2, 2, 1),
(4, 3, 1, 1),
(8, 7, 1, 1),
(9, 8, 1, 1),
(10, 8, 2, 0),
(11, 8, 8, 0),
(12, 9, 1, 1),
(13, 9, 2, 0),
(14, 10, 1, 1),
(15, 11, 8, 1),
(16, 12, 8, 1),
(17, 13, 1, 1),
(19, 15, 1, 1);

-- --------------------------------------------------------

--
-- Table structure for table `draft_reviewer`
--

CREATE TABLE IF NOT EXISTS `draft_reviewer` (
  `draft_reviewer_id` mediumint(9) NOT NULL,
  `draft_id` mediumint(9) DEFAULT NULL,
  `reviewer_id` mediumint(9) DEFAULT NULL,
  `status` int(11) NOT NULL DEFAULT '0' COMMENT '1 = Accept, 2 = Reject'
) ENGINE=InnoDB AUTO_INCREMENT=18 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `draft_reviewer`
--

INSERT INTO `draft_reviewer` (`draft_reviewer_id`, `draft_id`, `reviewer_id`, `status`) VALUES
(1, 3, 1, 0),
(3, 3, 2, 0),
(4, 8, 1, 0),
(5, 8, 2, 0),
(6, 9, 1, 0),
(7, 9, 2, 0),
(8, 10, 1, 0),
(9, 10, 2, 0),
(10, 11, 2, 0),
(11, 11, 5, 0),
(12, 12, 1, 0),
(13, 12, 2, 0),
(14, 13, 1, 0),
(15, 13, 2, 0);

-- --------------------------------------------------------

--
-- Table structure for table `faculty`
--

CREATE TABLE IF NOT EXISTS `faculty` (
  `faculty_id` mediumint(9) NOT NULL,
  `faculty_name` varchar(256) NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `faculty`
--

INSERT INTO `faculty` (`faculty_id`, `faculty_name`) VALUES
(2, 'Vokasi'),
(3, 'Hukum'),
(4, 'Kehutanan'),
(6, 'Filsafat'),
(7, 'Teknik');

-- --------------------------------------------------------

--
-- Table structure for table `institute`
--

CREATE TABLE IF NOT EXISTS `institute` (
  `institute_id` mediumint(9) NOT NULL,
  `institute_name` varchar(256) NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `institute`
--

INSERT INTO `institute` (`institute_id`, `institute_name`) VALUES
(2, 'UNY'),
(3, 'ITB'),
(5, 'UI'),
(6, 'UII'),
(7, 'UGM');

-- --------------------------------------------------------

--
-- Table structure for table `responsibility`
--

CREATE TABLE IF NOT EXISTS `responsibility` (
  `responsibility_id` mediumint(9) NOT NULL,
  `user_id` mediumint(9) DEFAULT NULL,
  `draft_id` mediumint(9) DEFAULT NULL
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `responsibility`
--

INSERT INTO `responsibility` (`responsibility_id`, `user_id`, `draft_id`) VALUES
(1, 2, 10),
(2, 2, 11),
(3, 2, 12),
(4, 2, 13),
(5, 4, 13),
(6, 6, 13);

-- --------------------------------------------------------

--
-- Table structure for table `reviewer`
--

CREATE TABLE IF NOT EXISTS `reviewer` (
  `reviewer_id` mediumint(9) NOT NULL,
  `reviewer_nip` varchar(256) NOT NULL,
  `reviewer_name` varchar(256) NOT NULL,
  `faculty_id` mediumint(9) DEFAULT NULL,
  `user_id` mediumint(9) DEFAULT NULL,
  `expert` text NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `reviewer`
--

INSERT INTO `reviewer` (`reviewer_id`, `reviewer_nip`, `reviewer_name`, `faculty_id`, `user_id`, `expert`) VALUES
(1, '10001', 'Reviewer Pertama', 7, 14, 'teknik,olahraga'),
(2, '10002', 'Reviewer Kedua', 3, 11, 'olahraga,hukum,sejarah'),
(3, '10003', 'Penulis Reviewer Pertama', 6, 15, 'geologi,geodesi'),
(4, '10004', 'Penulis Reviewer Kedua', 2, 16, 'vokasi,kedokteran'),
(5, '10005', 'Reviewer Ketiga', 2, 17, 'teknik,hukum');

-- --------------------------------------------------------

--
-- Table structure for table `theme`
--

CREATE TABLE IF NOT EXISTS `theme` (
  `theme_id` mediumint(9) NOT NULL,
  `theme_name` varchar(256) NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `theme`
--

INSERT INTO `theme` (`theme_id`, `theme_name`) VALUES
(1, 'Teknologi Informasi'),
(3, 'Astronomi'),
(4, 'Bahasa Indonesia'),
(5, 'Bahasa Inggris'),
(6, 'Olahraga');

-- --------------------------------------------------------

--
-- Table structure for table `user`
--

CREATE TABLE IF NOT EXISTS `user` (
  `user_id` mediumint(9) NOT NULL,
  `username` varchar(256) NOT NULL,
  `password` varchar(256) NOT NULL,
  `level` enum('superadmin','admin_penerbitan','staff_penerbitan','editor','layouter','admin_pemasaran','admin_percetakan','admin_gudang','author','reviewer','author_reviewer') NOT NULL,
  `is_blocked` enum('y','n') NOT NULL DEFAULT 'n'
) ENGINE=InnoDB AUTO_INCREMENT=18 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `user`
--

INSERT INTO `user` (`user_id`, `username`, `password`, `level`, `is_blocked`) VALUES
(1, 'superadmin', '17c4520f6cfd1ab53d8745e84681eb49', 'superadmin', 'n'),
(2, 'editor1', 'c9330587565205a5b8345f60c620ecc6', 'editor', 'n'),
(3, 'admin1', 'e00cf25ad42683b3df678c61f42c6bda', 'admin_penerbitan', 'n'),
(4, 'layouter1', 'ddfca9e47eec6493f18290dcea4e90bd', 'layouter', 'n'),
(6, 'layouter2', 'd09153bee3b1f4cbe8cc800128cedd68', 'layouter', 'n'),
(7, 'author1', 'b312ba4ffd5245fa2a1ab819ec0d0347', 'author', 'n'),
(8, 'author2', '9bd97baef2b853ec00cc3cffd269f679', 'author', 'n'),
(9, 'author3', 'c59a474d5ade296a15ebc40d6c4e8e11', 'author', 'n'),
(11, 'reviewer2', '2693b57f0f59df94caacefb811e99851', 'reviewer', 'n'),
(12, 'editor2', '0a96c5e164b4f259b4b8f6f565b55fe2', 'editor', 'n'),
(14, 'reviewer1', '6ce19528a40dde9521d97cf7ba264eca', 'reviewer', 'n'),
(15, 'authorreviewer1', 'f45b8b331a6cb91bd5b9a08058f552c1', 'author_reviewer', 'n'),
(16, 'authorreviewer2', '7055e5f72015748c9c8f4ee9d4795826', 'author_reviewer', 'n'),
(17, 'reviewer3', '315d31e7c8f3a136610aafa220d689be', 'reviewer', 'n');

-- --------------------------------------------------------

--
-- Table structure for table `worksheet`
--

CREATE TABLE IF NOT EXISTS `worksheet` (
  `worksheet_id` mediumint(9) NOT NULL,
  `draft_id` mediumint(9) DEFAULT NULL,
  `worksheet_num` varchar(256) NOT NULL,
  `is_reprint` enum('y','n') NOT NULL DEFAULT 'n',
  `worksheet_status` int(1) NOT NULL DEFAULT '0' COMMENT '1 = Approve, 2 = Reject',
  `worksheet_notes` text NOT NULL,
  `worksheet_pic` varchar(256) NOT NULL,
  `worksheet_ts` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB AUTO_INCREMENT=17 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `worksheet`
--

INSERT INTO `worksheet` (`worksheet_id`, `draft_id`, `worksheet_num`, `is_reprint`, `worksheet_status`, `worksheet_notes`, `worksheet_pic`, `worksheet_ts`) VALUES
(1, 1, '2018-10-01', 'n', 2, 'Contoh persetujuan editor', 'editor1', '2018-10-23 04:39:09'),
(2, 2, '2018-10-02', 'n', 1, 'Lembar kerja disetujui', 'superadmin', '2018-10-23 17:31:54'),
(3, 3, '2018-10-03', 'n', 1, 'Lembar Kerja Diterima', 'superadmin', '2018-10-23 17:32:16'),
(7, 7, '2018-10-04', 'n', 0, '', '', '2018-10-24 11:24:51'),
(9, 8, '2018-10-05', 'n', 1, '', 'superadmin', '2018-10-24 11:49:01'),
(10, 9, '2018-10-06', 'n', 1, '', 'superadmin', '2018-10-24 12:03:04'),
(11, 10, '2018-10-07', 'n', 1, '', 'superadmin', '2018-10-24 12:21:08'),
(12, 11, '2018-10-08', 'n', 1, '', 'superadmin', '2018-10-24 13:37:57'),
(13, 12, '2018-10-09', 'n', 1, '', 'superadmin', '2018-10-24 13:44:05'),
(14, 13, '2018-10-10', 'n', 1, '', 'superadmin', '2018-10-24 14:12:12'),
(16, 15, '2018-10-11', 'n', 0, '', '', '2018-10-25 04:51:40');

-- --------------------------------------------------------

--
-- Table structure for table `work_unit`
--

CREATE TABLE IF NOT EXISTS `work_unit` (
  `work_unit_id` mediumint(9) NOT NULL,
  `work_unit_name` varchar(256) NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `work_unit`
--

INSERT INTO `work_unit` (`work_unit_id`, `work_unit_name`) VALUES
(2, 'Mahasiswa'),
(3, 'Umum'),
(4, 'Dosen');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `author`
--
ALTER TABLE `author`
  ADD PRIMARY KEY (`author_id`),
  ADD KEY `work_unit_id` (`work_unit_id`),
  ADD KEY `institute_id` (`institute_id`),
  ADD KEY `bank_id` (`bank_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `bank`
--
ALTER TABLE `bank`
  ADD PRIMARY KEY (`bank_id`);

--
-- Indexes for table `book`
--
ALTER TABLE `book`
  ADD PRIMARY KEY (`book_id`),
  ADD KEY `id_proposal` (`draft_id`),
  ADD KEY `code_id` (`book_code`);

--
-- Indexes for table `category`
--
ALTER TABLE `category`
  ADD PRIMARY KEY (`category_id`);

--
-- Indexes for table `draft`
--
ALTER TABLE `draft`
  ADD PRIMARY KEY (`draft_id`),
  ADD KEY `id_kategori` (`category_id`),
  ADD KEY `id_penulis` (`theme_id`);

--
-- Indexes for table `draft_author`
--
ALTER TABLE `draft_author`
  ADD PRIMARY KEY (`draft_author_id`),
  ADD KEY `draft_id` (`draft_id`),
  ADD KEY `author_id` (`author_id`);

--
-- Indexes for table `draft_reviewer`
--
ALTER TABLE `draft_reviewer`
  ADD PRIMARY KEY (`draft_reviewer_id`),
  ADD KEY `draft_id` (`draft_id`),
  ADD KEY `author_id` (`reviewer_id`),
  ADD KEY `reviewer_id` (`reviewer_id`),
  ADD KEY `draft_id_2` (`draft_id`);

--
-- Indexes for table `faculty`
--
ALTER TABLE `faculty`
  ADD PRIMARY KEY (`faculty_id`);

--
-- Indexes for table `institute`
--
ALTER TABLE `institute`
  ADD PRIMARY KEY (`institute_id`);

--
-- Indexes for table `responsibility`
--
ALTER TABLE `responsibility`
  ADD PRIMARY KEY (`responsibility_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `draft_id` (`draft_id`);

--
-- Indexes for table `reviewer`
--
ALTER TABLE `reviewer`
  ADD PRIMARY KEY (`reviewer_id`),
  ADD KEY `faculty_id` (`faculty_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `theme`
--
ALTER TABLE `theme`
  ADD PRIMARY KEY (`theme_id`);

--
-- Indexes for table `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`user_id`);

--
-- Indexes for table `worksheet`
--
ALTER TABLE `worksheet`
  ADD PRIMARY KEY (`worksheet_id`),
  ADD KEY `id_proposal` (`draft_id`);

--
-- Indexes for table `work_unit`
--
ALTER TABLE `work_unit`
  ADD PRIMARY KEY (`work_unit_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `author`
--
ALTER TABLE `author`
  MODIFY `author_id` mediumint(9) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=11;
--
-- AUTO_INCREMENT for table `book`
--
ALTER TABLE `book`
  MODIFY `book_id` mediumint(9) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `category`
--
ALTER TABLE `category`
  MODIFY `category_id` mediumint(9) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=5;
--
-- AUTO_INCREMENT for table `draft`
--
ALTER TABLE `draft`
  MODIFY `draft_id` mediumint(9) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=16;
--
-- AUTO_INCREMENT for table `draft_author`
--
ALTER TABLE `draft_author`
  MODIFY `draft_author_id` mediumint(9) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=20;
--
-- AUTO_INCREMENT for table `draft_reviewer`
--
ALTER TABLE `draft_reviewer`
  MODIFY `draft_reviewer_id` mediumint(9) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=18;
--
-- AUTO_INCREMENT for table `faculty`
--
ALTER TABLE `faculty`
  MODIFY `faculty_id` mediumint(9) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=8;
--
-- AUTO_INCREMENT for table `institute`
--
ALTER TABLE `institute`
  MODIFY `institute_id` mediumint(9) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=8;
--
-- AUTO_INCREMENT for table `responsibility`
--
ALTER TABLE `responsibility`
  MODIFY `responsibility_id` mediumint(9) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=10;
--
-- AUTO_INCREMENT for table `reviewer`
--
ALTER TABLE `reviewer`
  MODIFY `reviewer_id` mediumint(9) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=6;
--
-- AUTO_INCREMENT for table `theme`
--
ALTER TABLE `theme`
  MODIFY `theme_id` mediumint(9) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=7;
--
-- AUTO_INCREMENT for table `user`
--
ALTER TABLE `user`
  MODIFY `user_id` mediumint(9) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=18;
--
-- AUTO_INCREMENT for table `worksheet`
--
ALTER TABLE `worksheet`
  MODIFY `worksheet_id` mediumint(9) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=17;
--
-- AUTO_INCREMENT for table `work_unit`
--
ALTER TABLE `work_unit`
  MODIFY `work_unit_id` mediumint(9) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=5;
--
-- Constraints for dumped tables
--

--
-- Constraints for table `author`
--
ALTER TABLE `author`
  ADD CONSTRAINT `author_ibfk_1` FOREIGN KEY (`work_unit_id`) REFERENCES `work_unit` (`work_unit_id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `author_ibfk_2` FOREIGN KEY (`institute_id`) REFERENCES `institute` (`institute_id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `author_ibfk_3` FOREIGN KEY (`bank_id`) REFERENCES `bank` (`bank_id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `author_ibfk_4` FOREIGN KEY (`user_id`) REFERENCES `user` (`user_id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `book`
--
ALTER TABLE `book`
  ADD CONSTRAINT `book_ibfk_2` FOREIGN KEY (`draft_id`) REFERENCES `draft` (`draft_id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `draft`
--
ALTER TABLE `draft`
  ADD CONSTRAINT `draft_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `category` (`category_id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `draft_ibfk_2` FOREIGN KEY (`theme_id`) REFERENCES `theme` (`theme_id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `draft_author`
--
ALTER TABLE `draft_author`
  ADD CONSTRAINT `draft_author_ibfk_1` FOREIGN KEY (`author_id`) REFERENCES `author` (`author_id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `draft_author_ibfk_3` FOREIGN KEY (`draft_id`) REFERENCES `draft` (`draft_id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `draft_reviewer`
--
ALTER TABLE `draft_reviewer`
  ADD CONSTRAINT `draft_reviewer_ibfk_1` FOREIGN KEY (`draft_id`) REFERENCES `draft` (`draft_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `draft_reviewer_ibfk_2` FOREIGN KEY (`reviewer_id`) REFERENCES `reviewer` (`reviewer_id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `responsibility`
--
ALTER TABLE `responsibility`
  ADD CONSTRAINT `responsibility_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user` (`user_id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `responsibility_ibfk_2` FOREIGN KEY (`draft_id`) REFERENCES `draft` (`draft_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `reviewer`
--
ALTER TABLE `reviewer`
  ADD CONSTRAINT `reviewer_ibfk_1` FOREIGN KEY (`faculty_id`) REFERENCES `faculty` (`faculty_id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `reviewer_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `user` (`user_id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `worksheet`
--
ALTER TABLE `worksheet`
  ADD CONSTRAINT `worksheet_ibfk_1` FOREIGN KEY (`draft_id`) REFERENCES `draft` (`draft_id`) ON DELETE CASCADE ON UPDATE CASCADE;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
