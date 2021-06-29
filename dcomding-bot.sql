-- phpMyAdmin SQL Dump
-- version 4.6.6deb5ubuntu0.5
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- 생성 시간: 21-06-30 04:14
-- 서버 버전: 5.7.34-0ubuntu0.18.04.1
-- PHP 버전: 7.2.24-0ubuntu0.18.04.7

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- 데이터베이스: `dcomding`
--

-- --------------------------------------------------------

--
-- 테이블 구조 `member`
--

CREATE TABLE `member` (
  `mb_id` int(21) NOT NULL,
  `mb_name` varchar(255) NOT NULL,
  `mb_slack_id` varchar(255) NOT NULL,
  `mb_github_id` varchar(255) NOT NULL,
  `mb_is_hidden` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- 테이블 구조 `member_point`
--

CREATE TABLE `member_point` (
  `mp_id` int(21) NOT NULL,
  `mb_id` int(21) NOT NULL,
  `te_id` int(21) NOT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- 테이블 구조 `slack_log`
--

CREATE TABLE `slack_log` (
  `sl_id` int(21) NOT NULL,
  `sl_channel` varchar(255) NOT NULL,
  `sl_blocks` json NOT NULL,
  `sl_created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- 테이블 구조 `test`
--

CREATE TABLE `test` (
  `te_id` int(21) NOT NULL,
  `tg_id` int(21) NOT NULL,
  `te_seq` tinyint(1) NOT NULL,
  `te_name` varchar(255) NOT NULL,
  `te_dirname` varchar(255) NOT NULL,
  `te_hard` tinyint(1) NOT NULL,
  `te_point` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- 테이블 구조 `test_case`
--

CREATE TABLE `test_case` (
  `tc_id` int(21) NOT NULL,
  `te_id` int(21) NOT NULL,
  `tc_seq` tinyint(1) NOT NULL,
  `tc_name` varchar(255) DEFAULT NULL,
  `tc_input` mediumtext NOT NULL,
  `tc_output` mediumtext NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- 테이블 구조 `test_group`
--

CREATE TABLE `test_group` (
  `tg_id` int(21) NOT NULL,
  `tg_name` varchar(255) NOT NULL,
  `tg_dirname` varchar(255) NOT NULL,
  `tg_start` datetime NOT NULL,
  `tg_end` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- 테이블 구조 `test_result`
--

CREATE TABLE `test_result` (
  `tr_id` int(21) NOT NULL,
  `te_id` int(21) NOT NULL,
  `mb_id` int(21) NOT NULL,
  `tr_language` enum('cpp','c','js','py') NOT NULL,
  `tr_code` text NOT NULL,
  `tr_result` enum('success','failed','compile_error','runtime_error','timeout','archiving') NOT NULL,
  `tr_time` int(21) DEFAULT NULL,
  `tc_id` int(21) DEFAULT NULL,
  `tr_created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- 덤프된 테이블의 인덱스
--

--
-- 테이블의 인덱스 `member`
--
ALTER TABLE `member`
  ADD PRIMARY KEY (`mb_id`),
  ADD UNIQUE KEY `mb_github_id` (`mb_github_id`);

--
-- 테이블의 인덱스 `member_point`
--
ALTER TABLE `member_point`
  ADD PRIMARY KEY (`mp_id`),
  ADD UNIQUE KEY `mb_id` (`mb_id`,`te_id`),
  ADD KEY `te_id` (`te_id`);

--
-- 테이블의 인덱스 `slack_log`
--
ALTER TABLE `slack_log`
  ADD PRIMARY KEY (`sl_id`);

--
-- 테이블의 인덱스 `test`
--
ALTER TABLE `test`
  ADD PRIMARY KEY (`te_id`),
  ADD KEY `tg_id` (`tg_id`),
  ADD KEY `te_dirname` (`te_dirname`) USING BTREE;

--
-- 테이블의 인덱스 `test_case`
--
ALTER TABLE `test_case`
  ADD PRIMARY KEY (`tc_id`),
  ADD UNIQUE KEY `tc_seq` (`te_id`,`tc_seq`) USING BTREE,
  ADD KEY `te_id` (`te_id`);

--
-- 테이블의 인덱스 `test_group`
--
ALTER TABLE `test_group`
  ADD PRIMARY KEY (`tg_id`),
  ADD UNIQUE KEY `tg_dirname` (`tg_dirname`);

--
-- 테이블의 인덱스 `test_result`
--
ALTER TABLE `test_result`
  ADD PRIMARY KEY (`tr_id`),
  ADD KEY `test_result_ibfk_1` (`tc_id`),
  ADD KEY `mb_id` (`mb_id`),
  ADD KEY `te_id` (`te_id`);

--
-- 덤프된 테이블의 AUTO_INCREMENT
--

--
-- 테이블의 AUTO_INCREMENT `member`
--
ALTER TABLE `member`
  MODIFY `mb_id` int(21) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;
--
-- 테이블의 AUTO_INCREMENT `member_point`
--
ALTER TABLE `member_point`
  MODIFY `mp_id` int(21) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=84;
--
-- 테이블의 AUTO_INCREMENT `slack_log`
--
ALTER TABLE `slack_log`
  MODIFY `sl_id` int(21) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=215;
--
-- 테이블의 AUTO_INCREMENT `test`
--
ALTER TABLE `test`
  MODIFY `te_id` int(21) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;
--
-- 테이블의 AUTO_INCREMENT `test_case`
--
ALTER TABLE `test_case`
  MODIFY `tc_id` int(21) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=249;
--
-- 테이블의 AUTO_INCREMENT `test_group`
--
ALTER TABLE `test_group`
  MODIFY `tg_id` int(21) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;
--
-- 테이블의 AUTO_INCREMENT `test_result`
--
ALTER TABLE `test_result`
  MODIFY `tr_id` int(21) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=216;
--
-- 덤프된 테이블의 제약사항
--

--
-- 테이블의 제약사항 `member_point`
--
ALTER TABLE `member_point`
  ADD CONSTRAINT `member_point_ibfk_1` FOREIGN KEY (`mb_id`) REFERENCES `member` (`mb_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `member_point_ibfk_2` FOREIGN KEY (`te_id`) REFERENCES `test` (`te_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- 테이블의 제약사항 `test`
--
ALTER TABLE `test`
  ADD CONSTRAINT `test_ibfk_1` FOREIGN KEY (`tg_id`) REFERENCES `test_group` (`tg_id`);

--
-- 테이블의 제약사항 `test_case`
--
ALTER TABLE `test_case`
  ADD CONSTRAINT `test_case_ibfk_1` FOREIGN KEY (`te_id`) REFERENCES `test` (`te_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- 테이블의 제약사항 `test_result`
--
ALTER TABLE `test_result`
  ADD CONSTRAINT `test_result_ibfk_1` FOREIGN KEY (`tc_id`) REFERENCES `test_case` (`tc_id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `test_result_ibfk_2` FOREIGN KEY (`mb_id`) REFERENCES `member` (`mb_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `test_result_ibfk_3` FOREIGN KEY (`te_id`) REFERENCES `test` (`te_id`) ON DELETE CASCADE ON UPDATE CASCADE;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
