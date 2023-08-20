-- phpMyAdmin SQL Dump
-- version 4.8.3
-- https://www.phpmyadmin.net/
--
-- 主機: 127.0.0.1:3306
-- 產生時間： 2019 年 01 月 01 日 08:01
-- 伺服器版本: 5.7.23
-- PHP 版本： 7.2.10

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- 資料庫： `contactbook`
--

-- --------------------------------------------------------

--
-- 資料表結構 `bothcheck`
--

DROP TABLE IF EXISTS `bothcheck`;
CREATE TABLE IF NOT EXISTS `bothcheck` (
  `sid` char(9) COLLATE utf8mb4_unicode_ci NOT NULL,
  `date` date NOT NULL,
  `parcheck` tinyint(1) NOT NULL,
  `parcomment` text COLLATE utf8mb4_unicode_ci,
  `teacheck` tinyint(1) NOT NULL,
  `teacomment` text COLLATE utf8mb4_unicode_ci,
  KEY `sid` (`sid`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- 資料表的匯出資料 `bothcheck`
--

INSERT INTO `bothcheck` (`sid`, `date`, `parcheck`, `parcomment`, `teacheck`, `teacomment`) VALUES
('108000001', '2018-12-25', 0, '看到了！', 1, '請家長看！！'),
('108000002', '2018-12-25', 0, '', 1, 'rrwr wrwervsfcsrerc'),
('108000003', '2018-12-18', 0, '', 0, 'wqqwqe'),
('108000001', '2018-12-24', 0, '', 1, '12315');

-- --------------------------------------------------------

--
-- 資料表結構 `diary`
--

DROP TABLE IF EXISTS `diary`;
CREATE TABLE IF NOT EXISTS `diary` (
  `sid` char(9) COLLATE utf8mb4_unicode_ci NOT NULL,
  `date` date NOT NULL,
  `diary` text COLLATE utf8mb4_unicode_ci,
  KEY `sid` (`sid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- 資料表的匯出資料 `diary`
--

INSERT INTO `diary` (`sid`, `date`, `diary`) VALUES
('108000001', '2018-12-25', '好山好水好空氣'),
('108000001', '2018-12-26', '聖誕老人還是沒來......');

-- --------------------------------------------------------

--
-- 資料表結構 `homework`
--

DROP TABLE IF EXISTS `homework`;
CREATE TABLE IF NOT EXISTS `homework` (
  `date` date NOT NULL,
  `homework` text COLLATE utf8mb4_unicode_ci,
  `quiz` text COLLATE utf8mb4_unicode_ci,
  PRIMARY KEY (`date`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- 資料表的匯出資料 `homework`
--

INSERT INTO `homework` (`date`, `homework`, `quiz`) VALUES
('2018-12-24', 'wqdwr', 'qedrwf'),
('2018-12-25', '', ''),
('2018-12-27', '1. 數卷*2', '1. 考數學C4'),
('2018-12-31', 'www', 'qwwqda');

-- --------------------------------------------------------

--
-- 資料表結構 `note`
--

DROP TABLE IF EXISTS `note`;
CREATE TABLE IF NOT EXISTS `note` (
  `sid` char(9) COLLATE utf8mb4_unicode_ci NOT NULL,
  `date` date NOT NULL,
  `note` text COLLATE utf8mb4_unicode_ci NOT NULL,
  KEY `sid` (`sid`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- 資料表的匯出資料 `note`
--

INSERT INTO `note` (`sid`, `date`, `note`) VALUES
('108000001', '2018-12-24', '大家記得帶禮物喔!~'),
('108000001', '2018-12-26', '國卷訂正60分以下抄10遍');

-- --------------------------------------------------------

--
-- 資料表結構 `parent`
--

DROP TABLE IF EXISTS `parent`;
CREATE TABLE IF NOT EXISTS `parent` (
  `idnumber` char(10) COLLATE utf8mb4_unicode_ci NOT NULL,
  `id` char(9) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `phone` varchar(12) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `password` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE KEY `idnumber` (`idnumber`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- 資料表的匯出資料 `parent`
--

INSERT INTO `parent` (`idnumber`, `id`, `name`, `phone`, `password`, `email`) VALUES
('A215981917', '110001006', '陳玉婷', '0970643398', 'ps944', '105111212@mail.oit.edu.tw'),
('G293214146', '120001201', '張皓東', '0935742376', 'ps146', 'CsJ2V@gmail.com'),
('C111111122', 'sb', '許耗', '', 'pt001', '');

-- --------------------------------------------------------

--
-- 資料表結構 `student`
--

DROP TABLE IF EXISTS `student`;
CREATE TABLE IF NOT EXISTS `student` (
  `idnumber` char(10) COLLATE utf8mb4_unicode_ci NOT NULL,
  `id` char(9) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `phone` varchar(12) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `password` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `tid` char(9) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `pid` char(9) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE KEY `idnumber` (`idnumber`),
  KEY `tid` (`tid`),
  KEY `pid` (`pid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- 資料表的匯出資料 `student`
--

INSERT INTO `student` (`idnumber`, `id`, `name`, `phone`, `password`, `email`, `tid`, `pid`) VALUES
('A145646492', '108000001', '王偉倫', '0932132377', 'st001', '105111232@mail.oit.edu.tw', '123456789', '110001006'),
('B111326975', '108000002', '張允惟', '0963493807', 'st002', 'JsLt2@gmail.com', '123456789', '120001201'),
('E136551760', '108000003', '蕭世昌', '0938948452', 'st003', 'f2xXQ@gmail.com', '123456789', NULL),
('H292336671', '108000004', '孫珮如', '0972801345', 'st004', 'aVp2a@gmail.com', '123456789', NULL),
('L250791772', '108000005', '翁惠雯', '0929321179', 'st005', 'scv4n5@gmail.com', '123456789', NULL),
('C197058693', '108000006', '林海宏', '0923372717', 'st006', 'm7ast@gmail.com', '123456789', NULL);

-- --------------------------------------------------------

--
-- 資料表結構 `teacher`
--

DROP TABLE IF EXISTS `teacher`;
CREATE TABLE IF NOT EXISTS `teacher` (
  `idnumber` char(10) COLLATE utf8mb4_unicode_ci NOT NULL,
  `id` char(9) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `phone` varchar(12) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `password` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE KEY `idnumber` (`idnumber`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- 資料表的匯出資料 `teacher`
--

INSERT INTO `teacher` (`idnumber`, `id`, `name`, `phone`, `password`, `email`) VALUES
('A123456789', '123456789', '吳郭魚', '012345678912', 't1234', '105111207@mail.oit.edu.tw'),
('S123456789', '156478', 'AAA', '', 'st008', ''),
('C111111111', 'aaa', '王大頭', '09123456777', 'a1234', 'awwasf@gmail.com');

--
-- 已匯出資料表的限制(Constraint)
--

--
-- 資料表的 Constraints `bothcheck`
--
ALTER TABLE `bothcheck`
  ADD CONSTRAINT `sidbothcheck` FOREIGN KEY (`sid`) REFERENCES `student` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- 資料表的 Constraints `diary`
--
ALTER TABLE `diary`
  ADD CONSTRAINT `sidiary` FOREIGN KEY (`sid`) REFERENCES `student` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- 資料表的 Constraints `note`
--
ALTER TABLE `note`
  ADD CONSTRAINT `sidnote` FOREIGN KEY (`sid`) REFERENCES `student` (`id`) ON DELETE NO ACTION ON UPDATE CASCADE;

--
-- 資料表的 Constraints `student`
--
ALTER TABLE `student`
  ADD CONSTRAINT `pid` FOREIGN KEY (`pid`) REFERENCES `parent` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `tid` FOREIGN KEY (`tid`) REFERENCES `teacher` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
