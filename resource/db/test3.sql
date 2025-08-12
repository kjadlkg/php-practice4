-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- 생성 시간: 25-08-12 15:31
-- 서버 버전: 10.4.32-MariaDB
-- PHP 버전: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- 데이터베이스: `test3`
--

-- --------------------------------------------------------

--
-- 테이블 구조 `board`
--

CREATE TABLE `board` (
  `board_id` bigint(20) NOT NULL,
  `board_title` varchar(255) NOT NULL,
  `board_content` text NOT NULL,
  `board_writer` varchar(20) DEFAULT NULL,
  `board_writer_id` varchar(30) DEFAULT NULL,
  `board_pw` varchar(255) DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `board_views` bigint(20) NOT NULL DEFAULT 0,
  `recommend_up` int(11) NOT NULL DEFAULT 0,
  `recommend_down` int(11) NOT NULL DEFAULT 0,
  `ip` varchar(255) DEFAULT NULL,
  `is_deleted` tinyint(1) NOT NULL DEFAULT 0,
  `deleted_at` datetime DEFAULT NULL ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- 테이블의 덤프 데이터 `board`
--

INSERT INTO `board` (`board_id`, `board_title`, `board_content`, `board_writer`, `board_writer_id`, `board_pw`, `created_at`, `board_views`, `recommend_up`, `recommend_down`, `ip`, `is_deleted`, `deleted_at`) VALUES
(3, 'test', 'test', 'test', '', NULL, '2025-03-17 21:37:41', 1, 0, 0, NULL, 0, NULL),
(5, 'ㅎㅇ', 'ㅎㅇ', 'test', '', NULL, '2025-03-19 22:18:50', 12, 0, 0, NULL, 0, NULL),
(6, 'ㅎㅇ', 'ㅎㅇㅎㅇ', 'test', '', NULL, '2025-03-19 22:19:28', 11, 0, 0, NULL, 0, '2025-07-10 03:59:04'),
(7, 'ㅋㅋ', 'ㅋㅋ', 'test', '', NULL, '2025-03-19 22:19:39', 73, 0, 0, NULL, 0, '2025-07-10 03:59:04'),
(9, '댓글 테스트', '댓글 테스트', 'test', '', NULL, '2025-03-29 23:47:44', 9, 0, 0, NULL, 0, '2025-07-10 03:59:04'),
(12, 'zz', 'zz', 'test', '', NULL, '2025-04-03 21:55:49', 1, 0, 0, NULL, 0, NULL),
(18, 'zz', 'zzzz', 'test', '', NULL, '2025-04-03 22:19:36', 3, 0, 0, NULL, 0, NULL),
(20, 'zz', 'zz', 'ㅇㅇ', '', '$2y$10$qAclqJw6RMrKKJWxdngtrOQaW2uCu5f40PlnyW/kd.eBjmQ1vHpyW', '2025-04-03 22:28:30', 1, 0, 0, '::1', 0, '2025-07-10 03:59:58'),
(21, 'zz', 'zz', 'dwdad', '', '$2y$10$4wTHCj0FVLzDbHEBTmOLEOvr0qFdmp2GkpYrqTiXLhVebUBr4Vfjq', '2025-04-03 22:53:37', 0, 0, 0, '::1', 0, '2025-07-10 03:59:58'),
(23, 'ㅌㅅㅌ', 'ㅌㅅㅌㅌㅅㅌ', 'ㅇㅇ', '', '$2y$10$ZPtdtugicUbvyYN5wekDOOCagPhDEuAVp9nXGRuNrSiUbPrMHpyaO', '2025-04-04 23:01:03', 13, 0, 0, '::1', 0, NULL),
(24, 'zz', '쿠쿠', 'ㅇㅇ', '', '$2y$10$rMSZUdpvnHQKDnPvTjDu/u1eEruoijYNrTp.ggPYLc2qyUkskwJeK', '2025-04-04 23:21:14', 13, 0, 0, '::1', 0, NULL),
(25, 'zz', 'zzzz', 'qwer', '', '$2y$10$EpxoK.VRThejW4tnzL/juuTjZoxe2bhxzyHFNnWaCCusmqtLuC94a', '2025-04-05 23:56:45', 15, 0, 0, '::1', 0, '2025-07-10 03:59:04'),
(26, '안녕하세요', 'ㅎㅇㅎㅇ', 'ㅇㅇ', '', '$2y$10$PgEcRKsCRDwuBnKXsj8YdecTiF2lfBC78bDZCWzXrwIWrYtQUOwJ6', '2025-04-06 21:03:24', 8, 0, 0, '::1', 0, NULL),
(37, 'ㅇㅇ', 'ㅇㅇ', 'ㅇㅇ', '', '$2y$10$7Irx2A4MOZ0LXMHs30/8ROxoCx7E.rx4GAFNjRfLEBEvwM90pIzg2', '2025-04-08 20:55:15', 33, 0, 0, '::1', 0, NULL),
(41, 'zz', 'zz', 'test', '', NULL, '2025-04-12 00:13:37', 8, 0, 0, NULL, 0, NULL),
(42, '캡차 테스트', 'ㅌㅅㅌ', 'ㅇㅇ', '', '$2y$10$U06eyz.426CkMq1c7MxzxO7a46T7VSz2q1YD7ptJT6N8L3CPfIvV6', '2025-04-13 17:33:16', 82, 21, 12, '::1', 0, NULL),
(43, 'dddd', 'dddd', 'dddd', '', '$2y$10$1vpxFXcxpg0lt8B9nI/A9uI6B4mTCP1MQqxPHcNOslbSP/rPRAuBS', '2025-04-19 22:24:32', 93, 0, 0, '::1', 0, NULL),
(44, '페이징', 'ㅍㅇㅈ', 'test', '', NULL, '2025-05-08 07:34:45', 0, 0, 0, NULL, 0, NULL),
(45, 'sadads', 'asdasda', 'test', '', NULL, '2025-05-08 16:56:54', 3, 0, 0, NULL, 0, NULL),
(46, 'asdada', 'asdasda', 'test', '', NULL, '2025-05-08 16:56:58', 0, 0, 0, NULL, 0, NULL),
(47, 'asdasda', 'dasdadsad', 'test', '', NULL, '2025-05-08 16:57:03', 0, 0, 0, NULL, 0, NULL),
(48, 'asdasdasd', 'asdasdas', 'test', '', NULL, '2025-05-08 16:57:07', 2, 0, 0, NULL, 0, NULL),
(49, 'asdadadadsa', 'dasasdasda', 'test', '', NULL, '2025-05-08 16:57:13', 9, 0, 0, NULL, 0, NULL),
(50, 'adsasd', 'asdasda', 'ㅇㅇ', '', '$2y$10$lk2k.Zs39mj5L3L7ZzNTAe8gPhVLJ0E/IOe6VO7yqGHrevALbGSQG', '2025-05-09 19:46:05', 157, 1, 0, '::1', 0, NULL),
(51, 'asdadsa', 'asdasd', 'dd', NULL, '$2y$10$fUyUybu7pzGp.DB7z/XsJO60bSrUS/WYvfXNbKAXXlmnqLVfx28Nu', '2025-05-12 15:21:48', 108, 11, 6, '::1', 0, NULL),
(52, 'asdads', 'adsasdadsa', '감자돌이', NULL, NULL, '2025-05-14 09:08:46', 3, 0, 0, NULL, 0, NULL),
(53, 'dd', 'dd', '감자돌이', 'gamja', NULL, '2025-05-14 09:15:19', 6, 0, 0, NULL, 0, NULL),
(54, '안녕하세요', '안녕하세요', 'ㅇㅇ', NULL, '$2y$10$4rVOvt3h7/aIA8VNaXRMR.em4FCRF1cpaAJSPjGGtHVkrkYn151Ga', '2025-05-14 16:10:38', 84, 0, 0, '::1', 0, NULL),
(55, '안녕하세요', '안녕하세요', '감자돌이', 'gamja', NULL, '2025-05-15 21:59:40', 30, 0, 0, NULL, 0, NULL),
(56, 'dadsa', 'asdasda', 'dd', NULL, '$2y$10$l.NF9o23mAfOLpfrNwVDvegm5OzS3lIjQD6WBESgcga5HKCDRgUCy', '2025-05-15 23:07:05', 4, 0, 0, '::1', 0, NULL),
(57, 'adsasdasd', 'asdadsasd', 'ㅇㅇ', NULL, '$2y$10$5B0gsjYhqNFtLhIZ8MJDLeKJGxsdfv7ZeF81VmcyPSP.oI8wgYLc2', '2025-06-10 00:09:07', 3, 0, 0, '::1', 0, NULL),
(60, 'asdasdasdasd12341234', 'asdasdasdasdasdasd12341234', 'test', 'test', NULL, '2025-06-10 18:02:56', 21, 0, 0, NULL, 0, NULL),
(61, 'dd', 'dd', 'test', 'test', NULL, '2025-06-13 00:04:35', 0, 0, 0, NULL, 0, NULL),
(62, 'ddd', 'dd', 'test', 'test', NULL, '2025-06-13 00:04:39', 0, 0, 0, NULL, 0, NULL),
(63, 'dd', 'ddd', 'test', 'test', NULL, '2025-06-13 00:04:43', 0, 0, 0, NULL, 0, NULL),
(64, 'ddd', 'dddd', 'test', 'test', NULL, '2025-06-13 00:04:46', 0, 0, 0, NULL, 0, NULL),
(65, 'ddd', 'dddd', 'test', 'test', NULL, '2025-06-13 00:04:50', 0, 0, 0, NULL, 0, NULL),
(66, 'ddd', 'dddd', 'test', 'test', NULL, '2025-06-13 00:04:54', 0, 0, 0, NULL, 0, NULL),
(67, 'dd', 'dddd', 'test', 'test', NULL, '2025-06-13 00:04:59', 0, 0, 0, NULL, 0, NULL),
(68, 'ddd', 'dddddd', 'test', 'test', NULL, '2025-06-13 00:05:03', 0, 0, 0, NULL, 0, NULL),
(69, 'ddd', 'ddddd', 'test', 'test', NULL, '2025-06-13 00:05:07', 0, 0, 0, NULL, 0, NULL),
(70, 'ddd', 'ddddd', 'test', 'test', NULL, '2025-06-13 00:05:12', 44, 0, 0, NULL, 0, NULL),
(72, 'dd', 'dd', 'test', 'test', NULL, '2025-06-14 17:54:52', 24, 0, 0, NULL, 0, NULL),
(73, 'dd', 'dd', 'test', 'test', NULL, '2025-06-14 17:54:55', 440, 0, 0, NULL, 0, '2025-06-24 13:13:56'),
(74, '삭제된 글 테스트용', '삭제된 글 테스트용', 'ㅇㅇ', '', '$2y$10$4CM36u74VZaQrqkyq0fEGeMzPOGsGE0yS3QdA0iq1hZrGJj/stFM.', '2025-06-24 13:06:36', 8, 0, 0, '::1', 1, '2025-06-24 13:11:33'),
(75, '삭제 글 테스트', '삭제 글 테스트', 'test', 'test', NULL, '2025-06-24 13:13:40', 1, 0, 0, NULL, 1, '2025-06-24 13:13:45'),
(76, '체크', '체크', '자동입력방지코드오류체크', 'captchacheck', NULL, '2025-07-10 11:24:28', 0, 0, 0, NULL, 0, NULL);

-- --------------------------------------------------------

--
-- 테이블 구조 `comment`
--

CREATE TABLE `comment` (
  `comment_id` bigint(20) NOT NULL,
  `board_id` bigint(20) DEFAULT NULL,
  `parent_id` int(11) DEFAULT NULL,
  `comment_writer` varchar(20) DEFAULT NULL,
  `comment_writer_id` varchar(30) DEFAULT NULL,
  `comment_pw` varchar(255) DEFAULT NULL,
  `comment_content` text NOT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `ip` varchar(255) DEFAULT NULL,
  `is_deleted` tinyint(1) NOT NULL DEFAULT 0,
  `deleted_at` datetime DEFAULT NULL ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- 테이블의 덤프 데이터 `comment`
--

INSERT INTO `comment` (`comment_id`, `board_id`, `parent_id`, `comment_writer`, `comment_writer_id`, `comment_pw`, `comment_content`, `created_at`, `ip`, `is_deleted`, `deleted_at`) VALUES
(5, 6, NULL, '감자돌이', '', NULL, 'ㅎㅇㅎㅇ', '0000-00-00 00:00:00', NULL, 0, NULL),
(6, 7, NULL, '감자돌이', '', NULL, 'ㅋㅋ', '0000-00-00 00:00:00', NULL, 0, '2025-07-10 04:26:19'),
(7, 5, NULL, '감자돌이', '', NULL, 'ㅎㅇ', '0000-00-00 00:00:00', NULL, 0, NULL),
(8, 7, NULL, '감자돌이', '', NULL, '시간 체크', '2025-03-28 21:10:16', NULL, 0, NULL),
(9, 7, NULL, 'test', '', NULL, '이제는 되겠지', '2025-03-29 23:35:23', NULL, 0, NULL),
(10, 9, NULL, 'test', '', NULL, 'ㅌㅅㅌ', '2025-03-29 23:47:52', NULL, 0, NULL),
(13, NULL, NULL, 'test', '', NULL, 'test', '2025-04-02 22:28:05', NULL, 0, NULL),
(14, NULL, NULL, '', '', NULL, '', '2025-04-09 23:05:43', NULL, 0, NULL),
(15, NULL, NULL, '', '', NULL, '', '2025-04-09 23:05:45', NULL, 0, NULL),
(22, NULL, NULL, 'test', '', NULL, 'ㅇㅇ', '2025-04-10 00:01:56', NULL, 0, NULL),
(23, NULL, NULL, 'test', '', NULL, 'dd', '2025-04-10 00:04:32', NULL, 0, NULL),
(24, NULL, NULL, 'test', '', NULL, 'dd', '2025-04-10 00:07:27', NULL, 0, NULL),
(27, NULL, NULL, '메롱', '', '$2y$10$4i4Jqu9s15a5JETNOLnKGu.0zE1DFj0KCnc734roXObwzMULLgSMe', 'ㅋㅋㅋ', '2025-04-11 23:49:06', '::1', 0, NULL),
(29, NULL, NULL, '감자', '', '$2y$10$X.WXN8kVIrupLWz7lrkDV.EcKOC.1tJqJOQZRfNn4RKObldsUud02', '메롱', '2025-04-11 23:49:30', '::1', 0, NULL),
(30, NULL, NULL, '메롱롱', '', '$2y$10$Pkioe86gP5KCWz3sQ0CGSuG7XCC6R0gfRFaM8tfG8NCCUHodj/cp.', '1111', '2025-04-11 23:49:44', '::1', 0, NULL),
(31, NULL, NULL, 'ㅇㅇ', '', '$2y$10$GWyorn8rIATfZP3QQ9lj/.c0Y4.CXMW6BIOro6LkC9uVHNNN1HSh6', 'zz', '2025-04-11 23:49:55', '::1', 0, NULL),
(32, NULL, NULL, 'ㅇㅇ', '', '$2y$10$nxJgzVol8ZH7fPwj6F6yIunO7uOMP2GzQ7oO/gbq7qQfpm8dgzw1K', 'zz', '2025-04-11 23:51:25', '::1', 0, NULL),
(33, NULL, NULL, 'ㅇㅇ', '', '$2y$10$fRFnq4Fq82ERqR9P6hvgje7d5S6AdA6KGdyCzBLqYIalQytH3PF.u', '', '2025-04-11 23:51:35', '::1', 0, NULL),
(34, NULL, NULL, 'ㅇㅇ', '', '$2y$10$6h1IlXs3ZN/R7m0it38jK.zmVAZzEj.hhch7EhIIBDWQRsMxTLxOG', '', '2025-04-11 23:51:43', '::1', 0, NULL),
(35, NULL, NULL, '', '', '$2y$10$oNn1eyHR1iMXdkls2yh9HujOYi4uZ.2OJ7QkZrnEF3LY10lL6RM4m', '', '2025-04-11 23:52:44', '::1', 0, NULL),
(36, NULL, NULL, '', '', '$2y$10$637axBuJmQKQou5ge0goHu9AjVdFImSZ/3HpvEi.a4VSqgahOVVS2', '', '2025-04-11 23:52:47', '::1', 0, NULL),
(37, NULL, NULL, '', '', '$2y$10$l4vV20PDWn3tXWtDJzmIyOZbLlS4FEPMgA2Z9e45m.gHIgWYvVKY.', '', '2025-04-11 23:52:52', '::1', 0, NULL),
(38, NULL, NULL, '', '', '$2y$10$1CONU09TDUm20B3ZrdiS9.lr3xS6MdPHIP5cAdxVu2bDaU0UJXXoq', '', '2025-04-11 23:53:03', '::1', 0, NULL),
(39, NULL, NULL, '', '', '$2y$10$0wPMu76x/VUkLI6TJVxKiu8CseEDrmvNHr3keGb111KT9h5igj3mO', '', '2025-04-11 23:53:06', '::1', 0, NULL),
(40, NULL, NULL, 'zz', '', '$2y$10$oEyW1AQpNE1niLh5SM/Zd..RJjbnyXluVNzIhrNuDdnvOSLIw6JrW', 'zz', '2025-04-11 23:53:48', '::1', 0, NULL),
(41, NULL, NULL, 'zz', '', '$2y$10$eSiVQJ6j/rAS7mA6BdIwx.ktuB8Y4nVkTcig7a0AQx1T1dfoUKNNu', '1111', '2025-04-11 23:53:56', '::1', 0, NULL),
(43, 37, NULL, '', '', '$2y$10$tvcOca856ttAx2Hg0ooy5eNA/1xlCRef9OOS0c/OHz.NdNSaodF6C', 'zz', '2025-04-11 23:58:09', '::1', 0, NULL),
(44, 37, NULL, '', '', '$2y$10$QilMvLWkfVO/SfATukYtwuJ0U2Q1M.RJT7JetV8ineQ2YODNPB0kO', 'ㅇㅇ', '2025-04-12 00:00:39', '::1', 0, NULL),
(45, 37, NULL, '', '', '$2y$10$OUUSMupB5OuQbliODGJVNe9a8FKmBNSJ.YxCo8OK8PoQ0En5SiroS', 'ㅇㅇ', '2025-04-12 00:02:42', '::1', 0, NULL),
(46, 37, NULL, '', '', '$2y$10$sEZotlIrURLC.c3Mq6JogOuHAtcMEjlIbOtlsB08iFmpzNKYhmUDi', '', '2025-04-12 00:04:38', '::1', 0, NULL),
(47, 37, NULL, 'test', '', '$2y$10$UrwurH5iuwXfyB3FnYyRE.QY8UEUDpqdcF50o8wKnj/hessy23sYO', 'zz', '2025-04-12 00:05:12', '::1', 0, NULL),
(48, 41, NULL, 'ㅇㅇ', '', '$2y$10$vgHSyYUGpsSpaVpDS/jlEeIjEIPYTr.UJXzU02yWJAbxDqs99kBwS', 'ㅎㅎ', '2025-04-12 00:13:54', '::1', 0, NULL),
(49, 41, NULL, 'ㅇㅇ', '', '$2y$10$L7hC5XHrtMNVMS3zQmMus.y.pBeR4H0Zv.78OZzQusdcdfMknnXHe', 'zz', '2025-04-12 00:15:57', '::1', 0, NULL),
(50, 42, NULL, 'ㅇㅇ', '', '$2y$10$XlH3kadvM7BIgUOLe5U2L.9d7PIrKL4hNIl02xpADr82FIyvCDT2K', 'zz', '2025-04-13 17:40:03', '::1', 0, NULL),
(51, 42, NULL, 'ㅇㅇ', '', '$2y$10$V1K0Vp8sltg//qLqaUyA0u7gSl7hqfyd4s7kaEeEh6mNHLtIjE032', 'zz', '2025-04-15 23:58:34', '::1', 0, NULL),
(52, 43, NULL, 'test', '', '', 'ㅇㅇㅇ', '2025-05-06 14:41:19', '', 0, NULL),
(63, 51, NULL, 'ㅇㅇ', '', '$2y$10$ihbNNhCNfAMJNfa1AKELLuGFffIat94cuFhp1G11uq0XVnPoFyUrS', 'asdadsa', '2025-05-13 01:07:13', '::1', 0, NULL),
(64, 51, NULL, 'test', 'test', '', 'adsasdasd', '2025-05-13 02:16:03', '', 0, NULL),
(65, 51, NULL, 'test', 'test', '', 'dasdasd', '2025-05-13 02:21:08', '', 0, NULL),
(74, 55, NULL, '감자돌이', 'gamja', '', '안녕하세요', '2025-05-15 21:59:49', '', 0, NULL),
(75, 55, NULL, '감자돌이', 'gamja', '', '안녕하세요', '2025-05-15 21:59:51', '', 0, NULL),
(76, 55, NULL, '감자돌이', 'gamja', '', '안녕하세요', '2025-05-15 21:59:54', '', 0, NULL),
(79, 55, NULL, 'ㅇㅇ', '', '$2y$10$0hnukKDJa7iSh8OsLek1aOcuhzCa30s.h3BckefGFKgTkeS99qWZW', '안녕하세요 익명', '2025-05-15 23:02:48', '::1', 0, NULL),
(80, NULL, NULL, 'test', 'test', '', 'adads', '2025-06-13 03:01:47', '', 0, NULL),
(81, 70, NULL, 'test', 'test', '', 'dd', '2025-06-13 04:09:25', '', 0, NULL),
(82, 70, NULL, 'test', 'test', '', 'dd', '2025-06-13 04:09:27', '', 0, NULL),
(83, 70, NULL, 'test', 'test', '', 'ddd', '2025-06-13 04:09:29', '', 0, NULL),
(84, 70, NULL, 'test', 'test', '', 'ddd', '2025-06-13 04:09:30', '', 0, NULL),
(85, 70, NULL, 'test', 'test', '', 'dddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddd', '2025-06-13 04:09:48', '', 0, NULL),
(86, 70, NULL, 'test', 'test', '', 'dddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddd', '2025-06-13 04:09:54', '', 0, NULL),
(87, 70, NULL, 'test', 'test', '', 'dddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddd', '2025-06-13 04:09:56', '', 0, NULL),
(88, 70, NULL, 'test', 'test', '', 'dddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddd', '2025-06-13 04:09:58', '', 0, NULL),
(89, 70, NULL, 'test', 'test', '', 'dddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddd', '2025-06-13 04:10:00', '', 0, NULL),
(90, 70, NULL, 'test', 'test', '', 'dddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddd', '2025-06-13 04:10:01', '', 0, NULL),
(91, 70, NULL, 'test', 'test', '', 'dddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddd', '2025-06-13 04:10:04', '', 0, NULL),
(92, 70, NULL, 'test', 'test', '', 'dddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddd', '2025-06-13 04:10:06', '', 0, NULL),
(93, 70, NULL, 'test', 'test', '', 'dddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddd', '2025-06-13 04:10:08', '', 0, NULL),
(94, 70, NULL, 'test', 'test', '', 'dddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddd', '2025-06-13 04:10:10', '', 0, NULL),
(95, 70, NULL, 'test', 'test', '', 'dddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddd', '2025-06-13 04:10:13', '', 0, NULL),
(96, 70, NULL, 'test', 'test', '', 'dddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddd', '2025-06-13 04:10:15', '', 0, NULL),
(97, 70, NULL, 'test', 'test', '', 'dddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddd', '2025-06-13 04:10:26', '', 0, NULL),
(98, 70, NULL, 'test', 'test', '', 'dddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddd', '2025-06-13 04:10:28', '', 0, NULL),
(99, 70, NULL, 'test', 'test', '', 'dddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddd', '2025-06-13 04:10:32', '', 0, NULL),
(100, 70, NULL, 'test', 'test', '', 'dddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddd', '2025-06-13 04:10:35', '', 0, NULL),
(101, 70, NULL, 'test', 'test', '', 'dddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddd', '2025-06-13 04:10:37', '', 0, NULL),
(102, 70, NULL, 'test', 'test', '', 'dddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddd', '2025-06-13 04:10:40', '', 0, NULL),
(103, 70, NULL, 'test', 'test', '', 'dddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddd', '2025-06-13 04:10:43', '', 0, NULL),
(104, 70, NULL, 'test', 'test', '', 'dddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddd', '2025-06-13 04:11:34', '', 0, NULL),
(105, 70, NULL, 'test', 'test', '', 'dddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddd', '2025-06-13 04:11:36', '', 0, NULL),
(106, 70, NULL, 'test', 'test', '', 'dddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddd', '2025-06-13 04:11:38', '', 0, NULL),
(107, 70, NULL, 'test', 'test', '', 'dddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddd', '2025-06-13 04:11:40', '', 0, NULL),
(108, 70, NULL, 'test', 'test', '', 'dddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddd', '2025-06-13 04:11:42', '', 0, NULL),
(109, 73, NULL, 'ㅇㅇ', '', '$2y$10$QFR63QMVw8W5.P/y4O4mvuAThKMcy5WC6kScn3pNQ9nsGS51c4b3i', 'dd', '2025-06-17 19:23:26', '::1', 0, NULL),
(119, 73, 109, 'ㅇㅇ', '', '$2y$10$7HOjV4pVkj3SuuNRNcoct.m4IGZ0iKl3UWYj6IPqbVvKagL37r0Ke', 'asdasdasdasd', '2025-06-23 01:32:37', '::1', 0, NULL),
(120, 73, 109, 'ㅇㅇ', '', '$2y$10$9507HuAXnk8TnIJDYuOt4Os/pfwY5OE9/rY8CoOtsnYfakeo/wNpK', 'asdasdas', '2025-06-23 01:37:24', '::1', 0, NULL),
(122, 73, 121, 'ㅇㅇ', '', '$2y$10$YO3cByQDx4rmRlYpTt4BCehiQn4LAHB75wCGh7WBTo7rMytFBvsOm', 'asdasdasasd', '2025-06-23 01:47:22', '::1', 1, '2025-06-24 00:29:29'),
(123, 73, 122, 'ㅇㅇ', '', '$2y$10$5OjFEb83Jwfv/e0educGzO4w0JsPrBjri4e9E77La1GrfS.nozK3e', 'adsasdasdad', '2025-06-24 00:29:24', '::1', 0, NULL),
(124, 73, 123, 'ㅇㅇ', '', '$2y$10$teHkcxPqYSeRTBD5Vt0z9eJdnis3xwmXxFOREmFizmXBMB9sV1X.G', 'asdasdasdass', '2025-06-24 00:31:00', '::1', 0, NULL),
(125, 73, NULL, 'ㅇㅇ', '', '$2y$10$Qwwdrzp8WYob0JH0us7tbullt.Ck0r2E3Ay.jhxYvvWIfdnb6xZG6', 'dadsad', '2025-06-24 00:50:30', '::1', 0, NULL),
(126, 73, 125, 'ㅇㅇ', '', '$2y$10$kSI90MmYoZco3xY4kEB/oO0gJstcc5CqC/wCfxGGBWyHTd5V14MfS', 'asdasdasda', '2025-06-24 00:50:40', '::1', 0, NULL),
(127, 73, 126, 'ㅇㅇ', '', '$2y$10$9qw2Md.ELPSMtAo/KGk1qe0cAK7TOqN5GAXE4/McyYAkYPPaZP1GS', 'asdasdadsa', '2025-06-24 00:50:46', '::1', 0, NULL),
(128, 73, 125, 'ㅇㅇ', '', '$2y$10$rtColed3WyOOvB9/TKlb/.Qp7qBwhSOpR0q209i/2N3TKeva/qbw2', 'asdasdasdasdasdasd', '2025-06-24 00:50:54', '::1', 0, NULL),
(129, 73, 125, 'ㅇㅇ', '', '$2y$10$hyMLWjXRrXGwXhGrnmnrq.Whi9xK6ykyMPUTP8DgZFj/sfI3jX9Nq', 'asdasdasdasd', '2025-06-24 00:51:01', '::1', 1, '2025-06-24 01:08:00'),
(130, 73, NULL, 'ㅇㅇ', '', '$2y$10$ur5MzewgOgqnIgrEaiDyTe1UolCaPqVXy14e8YqeyVs4UHH0rSnEW', 'asdasdasd', '2025-06-24 00:55:04', '::1', 0, NULL),
(131, 73, NULL, 'ㅇㅇ', '', '$2y$10$fH7OmgVDx16Ym6y73Y5dKeXFRAOdnjBDs4zLfumz6JCuelNygw0nW', 'asdadsadads', '2025-06-24 01:01:15', '::1', 0, NULL),
(132, 73, NULL, 'ㅇㅇ', '', '$2y$10$Yt3xkU0EFzX.4w.33b4Jtuw7VBTR22jDfniTcajwJ/4VB/fdhFEsi', 'asdasdasd', '2025-06-24 01:03:44', '::1', 0, NULL),
(133, 73, NULL, 'ㅇㅇ', '', '$2y$10$Oj8B9okaOoJZ2MGCQZVe9OJIBgUlQyqQQhfWDdkmcgXB5XRYnhImK', '11111111111111', '2025-06-24 01:04:00', '::1', 1, '2025-06-24 01:07:46'),
(134, 73, 133, 'ㅇㅇ', '', '$2y$10$nlxN2XtTUlJlL64Zn7txYOpMMTrQwWKoy3IatPQ9zkeffE1jGkrHa', 'asdasda', '2025-06-24 11:28:16', '::1', 0, NULL),
(135, 73, NULL, 'ㅇㅇ', '', '$2y$10$XQtincAIV9rmFPQmnr6FMua4SEPjxrGZ/rCiL8MdMLRTxHohan7fS', 'asdasdas', '2025-06-24 11:44:33', '::1', 0, NULL),
(136, 73, 135, 'ㅇㅇ', '', '$2y$10$QKGFNKF5rfZTER706r2I3.3DHOAFNc5ua.5uuUAh.PU1wF3Evlntq', 'asdadasdadsa', '2025-06-24 12:16:06', '::1', 0, NULL),
(137, 73, 136, 'ㅇㅇ', '', '$2y$10$PG66q1XQuxOhBmIQ/Pz0iOgyjBlHMzkENzlrE5oINLydSibKEuBkq', 'asdadsadsad', '2025-06-24 12:16:58', '::1', 0, NULL),
(138, 73, 136, 'ㅇㅇ', '', '$2y$10$Yo3PHz0WdrRXWHOLXwvR.uL/zwhIsRMS52YwLN1QS7C2beVLCF1o6', '111111', '2025-06-24 12:18:08', '::1', 0, NULL),
(139, 73, 136, 'ㅇㅇ', '', '$2y$10$JmV0RgoiJP6FJNXK8hPAJuMxTcdnSv/C3nH6Z6PGOmN66ETn.6V2i', 'asdasdads', '2025-06-24 12:18:56', '::1', 0, NULL),
(140, 73, NULL, 'ㅇㅇ', '', '$2y$10$DF1JORRFzgp90J7gw2DVD.1JjQg3GFkrwqQUYTtRpHSsTQ6..jSnu', 'asdasdasdasdasd', '2025-06-24 12:19:14', '::1', 0, NULL),
(141, 73, NULL, 'test', 'test', '', '댓글 카운트 테스트', '2025-06-24 12:38:30', '', 0, NULL),
(142, 73, NULL, 'test', 'test', '', '댓글', '2025-06-24 12:40:31', '', 1, '2025-06-24 12:42:24'),
(143, 73, NULL, 'test', 'test', '', 'ㅇㅇㅇ', '2025-06-24 12:40:43', '', 1, '2025-06-24 12:41:25'),
(144, 73, NULL, 'test', 'test', '', 'ㅇㅇㅇㅇ', '2025-06-24 12:42:31', '', 1, '2025-06-24 12:42:34'),
(145, 73, NULL, 'test', 'test', '', 'ㅇㅇ', '2025-06-24 12:47:11', '', 1, '2025-06-24 12:47:13'),
(146, 74, NULL, 'test', 'test', '', '테스트', '2025-06-24 13:06:48', '', 0, NULL);

-- --------------------------------------------------------

--
-- 테이블 구조 `notification`
--

CREATE TABLE `notification` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `type` enum('comment','reply','','') NOT NULL,
  `board_id` int(11) DEFAULT NULL,
  `comment_id` int(11) DEFAULT NULL,
  `is_read` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `ip` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- 테이블 구조 `recommend`
--

CREATE TABLE `recommend` (
  `recommend_id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `ip` varchar(45) DEFAULT NULL,
  `board_id` int(11) NOT NULL,
  `type` enum('up','down','','') NOT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- 테이블의 덤프 데이터 `recommend`
--

INSERT INTO `recommend` (`recommend_id`, `user_id`, `ip`, `board_id`, `type`, `created_at`) VALUES
(1, 0, '::1', 51, 'up', '2025-05-13 17:22:12'),
(2, 0, '::1', 51, 'down', '2025-05-13 17:22:15'),
(3, 0, '::1', 71, 'up', '2025-06-13 03:01:53'),
(4, 0, '::1', 71, 'down', '2025-06-13 03:01:54');

-- --------------------------------------------------------

--
-- 테이블 구조 `report`
--

CREATE TABLE `report` (
  `report_id` int(11) NOT NULL,
  `board_id` bigint(20) NOT NULL,
  `report_type` enum('obscene','spam','illegal','') NOT NULL,
  `user_id` varchar(30) DEFAULT NULL,
  `ip` varchar(255) DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- 테이블의 덤프 데이터 `report`
--

INSERT INTO `report` (`report_id`, `board_id`, `report_type`, `user_id`, `ip`, `created_at`) VALUES
(1, 37, 'spam', NULL, '::1', '2025-06-16 23:00:07'),
(2, 37, 'spam', NULL, '::1', '2025-06-16 23:00:20'),
(3, 73, 'illegal', 'test', '::1', '2025-06-16 23:05:19'),
(8, 72, 'spam', 'test', '::1', '2025-06-16 23:06:52'),
(26, 72, 'obscene', 'test', '::1', '2025-06-16 23:23:58'),
(29, 72, 'illegal', 'test', '::1', '2025-06-16 23:30:33'),
(30, 73, 'spam', 'test', '::1', '2025-06-16 23:32:27'),
(31, 73, 'obscene', 'test', '::1', '2025-06-16 23:32:33');

-- --------------------------------------------------------

--
-- 테이블 구조 `user`
--

CREATE TABLE `user` (
  `idx` bigint(20) NOT NULL,
  `user_name` varchar(20) NOT NULL,
  `user_id` varchar(30) NOT NULL,
  `user_pw` varchar(255) NOT NULL,
  `user_email` varchar(50) NOT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `is_deleted` tinyint(1) NOT NULL DEFAULT 0,
  `deleted_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- 테이블의 덤프 데이터 `user`
--

INSERT INTO `user` (`idx`, `user_name`, `user_id`, `user_pw`, `user_email`, `created_at`, `updated_at`, `is_deleted`, `deleted_at`) VALUES
(1, 'admin', 'admin', '$2y$10$KkLbfAUxYheO/NMDJHf/WOyTvUti6w4s28Xt3IDWTTsxb2tLHNV4C', 'admin@admin.com', '2025-03-09 22:21:21', '2025-06-14 18:25:48', 1, '2025-06-14 11:25:48'),
(2, 'qwer', 'qwer', '$2y$10$YQ5ViuJckJ5fDDg00NJ2leS', 'qwer@qwer', '2025-03-10 23:20:59', '2025-03-10 23:20:59', 0, NULL),
(3, 'qwerr', 'qwerr', '$2y$10$6ks3TtniK/2DvenB32CvJ.O', 'qwer@qwer', '2025-03-10 23:24:30', '2025-03-17 21:04:29', 0, NULL),
(4, 'test', 'test', '$2y$10$7JEAyLyQ9Vns9JUNvn3J4.FXmb4pgxfroZQLDsi6qkNPI91zpYSVa', 'test@test', '2025-03-10 23:32:20', '2025-06-14 18:20:43', 0, '0000-00-00 00:00:00'),
(5, 'qwerrr', 'qwerrr', '$2y$10$MrOSHXtQgdjNncfZNLTu..7', 'qwer@qwer', '2025-03-10 23:50:58', '2025-03-17 21:04:29', 0, NULL),
(6, 'qqwer', 'qqwer', '$2y$10$d1808s4QZHIMZlgltBnNh.3gz//w1TCv4N8VyBpNC1lwosqvb6H3C', 'qwer@qwer', '2025-03-12 22:49:07', '2025-03-17 21:04:29', 0, NULL),
(7, 'testtesttest', 'testtesttest', '$2y$10$A9uI/2cHlIEzVs8BxKUjuuoeE5.kPUAkV85Ecf0cFAavA.JK85RCO', 'testtesttest@testtesttest', '2025-03-12 22:54:55', '2025-03-17 21:05:21', 0, NULL),
(8, 'testtesttestt', 'testtesttestt', '$2y$10$UgX3glqAHAKsPFe9IfgMdeZJPUOugqA5eguwY.zHu.hbcEkWyRgDO', 'testtesttestt@testtesttestt', '2025-03-12 22:56:15', '2025-03-17 21:05:21', 0, NULL),
(9, 'testtesttesttest', 'testtesttesttest', '$2y$10$.ewR.Rn.RCEZQ6qkTytSMOiV/JRb4PFrQM2IxBXKLLJc9YmJFMmCy', 'testtesttesttest@testtesttesttest', '2025-03-12 22:56:39', '2025-03-17 21:05:21', 0, NULL),
(10, 'testtesttesttestt', 'testtesttesttestt', '$2y$10$Sr.YTLKXWamLGxugCgTW..GGeczOPFKMXOAgBAO3FOuUWfQVTB4GS', 'testtesttesttestt@testtesttesttestt', '2025-03-12 22:57:05', '2025-03-17 21:05:21', 0, NULL),
(11, 'qwerqwer12', 'qwerqwer12', '$2y$10$5kIkvA.xtsaEJsUvF.h7Mev8vrs1rTVh.PN9dnW2z6Zb6PKjGFO3G', 'qwerqwer12@qwerqwer12.com', '2025-03-13 22:01:06', '2025-03-13 22:01:06', 0, NULL),
(12, 'asdf', 'asdf', '$2y$10$IGUDK2VDhSILqDxtjKcgue1WzVqSWDEgO4ujFkXa38kLgd.BYUAha', 'asdf@asdf.com', '2025-03-13 23:42:04', '2025-03-13 23:42:04', 0, NULL),
(13, '감자돌이', 'gamja', '$2y$10$Rr8T2B/kV5YL88HAoFyXEOh79FM1b2bwlp5U0TLD600gJh2w9Wqw2', 'gamja@gamja.com', '2025-03-16 16:16:50', '2025-04-29 21:24:20', 0, NULL),
(14, '감자감자', 'gamja111', '$2y$10$RbpDmFSGSpr5rsULDAxSK.rTid/ogoKBXhXS2xEAojD97jR0APkei', 'gamja@gam.ja', '2025-03-25 23:42:34', '2025-03-29 23:07:58', 0, NULL),
(15, 'ㅇㅇ', '::1_1743686910', '$2y$10$qAclqJw6RMrKKJWxdngtrOQaW2uCu5f40PlnyW/kd.eBjmQ1vHpyW', '', '2025-04-03 22:28:30', '2025-04-03 22:28:30', 0, NULL),
(16, 'dwdad', '::1_1743688417', '$2y$10$4wTHCj0FVLzDbHEBTmOLEOvr0qFdmp2GkpYrqTiXLhVebUBr4Vfjq', '', '2025-04-03 22:53:37', '2025-04-03 22:53:37', 0, NULL),
(17, 'ㅋㅋ', '::1_1743769133', '$2y$10$75gXkNcB1R.SaoC2Gcwvd.2T0YGQ2waYXzD2iamFZhLJiaoPLo04e', '', '2025-04-04 21:18:53', '2025-04-04 21:18:53', 0, NULL),
(18, '김자', '::1_1743865005', '$2y$10$EpxoK.VRThejW4tnzL/juuTjZoxe2bhxzyHFNnWaCCusmqtLuC94a', '', '2025-04-05 23:56:45', '2025-04-05 23:56:45', 0, NULL),
(19, 'dddd', '::1_1745069072', '$2y$10$1vpxFXcxpg0lt8B9nI/A9uI6B4mTCP1MQqxPHcNOslbSP/rPRAuBS', '', '2025-04-19 22:24:32', '2025-04-19 22:24:32', 0, NULL),
(20, 'dd', '::1_1747030908', '$2y$10$fUyUybu7pzGp.DB7z/XsJO60bSrUS/WYvfXNbKAXXlmnqLVfx28Nu', '', '2025-05-12 15:21:48', '2025-05-12 15:21:48', 0, NULL),
(21, '자동입력방지코드오류체크', 'captchacheck', '$2y$10$v8sCN0Ff3xYB0Sewm5.TjuXt2QUUfEQUFLhcW7qAJsk1zvh8Wv98O', 'captcha@captcha.com', '2025-07-10 11:20:17', '2025-07-10 11:26:04', 1, '2025-07-10 04:26:04');

--
-- 덤프된 테이블의 인덱스
--

--
-- 테이블의 인덱스 `board`
--
ALTER TABLE `board`
  ADD PRIMARY KEY (`board_id`);

--
-- 테이블의 인덱스 `comment`
--
ALTER TABLE `comment`
  ADD PRIMARY KEY (`comment_id`);

--
-- 테이블의 인덱스 `notification`
--
ALTER TABLE `notification`
  ADD PRIMARY KEY (`id`);

--
-- 테이블의 인덱스 `recommend`
--
ALTER TABLE `recommend`
  ADD PRIMARY KEY (`recommend_id`),
  ADD KEY `idx_user_board` (`user_id`,`board_id`,`created_at`),
  ADD KEY `idx_ip_board` (`ip`,`board_id`,`created_at`);

--
-- 테이블의 인덱스 `report`
--
ALTER TABLE `report`
  ADD PRIMARY KEY (`report_id`);

--
-- 테이블의 인덱스 `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`idx`),
  ADD UNIQUE KEY `user_id` (`user_id`),
  ADD UNIQUE KEY `user_name` (`user_name`) USING BTREE;

--
-- 덤프된 테이블의 AUTO_INCREMENT
--

--
-- 테이블의 AUTO_INCREMENT `board`
--
ALTER TABLE `board`
  MODIFY `board_id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=77;

--
-- 테이블의 AUTO_INCREMENT `comment`
--
ALTER TABLE `comment`
  MODIFY `comment_id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=147;

--
-- 테이블의 AUTO_INCREMENT `notification`
--
ALTER TABLE `notification`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- 테이블의 AUTO_INCREMENT `recommend`
--
ALTER TABLE `recommend`
  MODIFY `recommend_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- 테이블의 AUTO_INCREMENT `report`
--
ALTER TABLE `report`
  MODIFY `report_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=32;

--
-- 테이블의 AUTO_INCREMENT `user`
--
ALTER TABLE `user`
  MODIFY `idx` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- 덤프된 테이블의 제약사항
--

--
-- 테이블의 제약사항 `board`
--
ALTER TABLE `board`
  ADD CONSTRAINT `fk_user_board` FOREIGN KEY (`board_writer`) REFERENCES `user` (`user_name`) ON DELETE CASCADE;

--
-- 테이블의 제약사항 `comment`
--
ALTER TABLE `comment`
  ADD CONSTRAINT `fk_comment_board` FOREIGN KEY (`board_id`) REFERENCES `board` (`board_id`) ON DELETE SET NULL;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
