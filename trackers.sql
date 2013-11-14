-- phpMyAdmin SQL Dump
-- version 3.5.8
-- http://www.phpmyadmin.net
--
-- Host: localhost:3306
-- Generation Time: Oct 28, 2013 at 05:04 PM
-- Server version: 5.0.51b-community-nt-log
-- PHP Version: 5.4.9

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `trackers`
--

-- --------------------------------------------------------

--
-- Table structure for table `stock`
--

CREATE TABLE IF NOT EXISTS `stock` (
  `id` int(11) NOT NULL auto_increment COMMENT '自增id',
  `code` char(6) NOT NULL COMMENT '股票代码',
  `name` varchar(100) NOT NULL COMMENT '股票名称',
  `exchange` tinyint(4) NOT NULL COMMENT '挂牌交易所，1为上海，2为深圳',
  `status` tinyint(1) NOT NULL default '1' COMMENT '股票状态，1为挂牌中，2为下市',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='股票代码表';

-- --------------------------------------------------------

--
-- Table structure for table `transaction_log`
--

CREATE TABLE IF NOT EXISTS `transaction_log` (
  `id` bigint(20) NOT NULL auto_increment,
  `stockCode` char(6) NOT NULL COMMENT '股票代码',
  `dateTime` char(10) NOT NULL COMMENT '日期',
  `openPrice` decimal(7,2) NOT NULL COMMENT '开盘价',
  `highPrice` decimal(7,2) NOT NULL COMMENT '最高价',
  `lowPrice` decimal(7,2) NOT NULL COMMENT '最低价',
  `closePrice` decimal(7,2) NOT NULL COMMENT '收盘价',
  `adjClosePrice` decimal(7,2) NOT NULL COMMENT '调整收盘价',
  `volume` bigint(20) NOT NULL COMMENT '成交量',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='股票交易记录表';

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

CREATE TABLE IF NOT EXISTS `company_info` (
  `id` int(11) NOT NULL auto_increment,
  `stockCode` char(6) NOT NULL COMMENT '证券代码',
  `email` varchar(30) default NULL COMMENT '电子信箱',
  `publishDate` char(10) default NULL COMMENT '发行日期',
  `publishPrice` decimal(7,2) default NULL COMMENT '发行价格',
  `inMarketDate` char(10) default NULL COMMENT '上市日期',
  `dealer` varchar(100) default NULL COMMENT '主承销商',
  `inMarketRecommendPerson` varchar(100) default NULL COMMENT '上市推荐人',
  `webSite` varchar(50) default NULL COMMENT '公司网址',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='股票公司信息表';

ALTER TABLE `company_info` ADD `lowestPrice` DECIMAL( 7, 2 ) NOT NULL COMMENT '历史最低价格',
ADD `highestPrice` DECIMAL( 7, 2 ) NOT NULL COMMENT '历史最高价格',
ADD `lowestDate` CHAR( 10 ) NOT NULL COMMENT '历史最低日期',
ADD `highestDate` CHAR( 10 ) NOT NULL COMMENT '历史最高日期',
ADD `diffPrice` DECIMAL( 7, 2 ) NOT NULL COMMENT '最高最低价格差';
ALTER TABLE `company_info` ADD `nowPrice` DECIMAL( 7, 2 ) NOT NULL COMMENT '当前股价' AFTER `webSite` ;

UPDATE `company_info` SET `publishDate`=unix_timestamp(`publishDate`) WHERE 1;
ALTER TABLE `company_info` CHANGE `publishDate` `publishDate` INT(11) NOT NULL DEFAULT 0 COMMENT '发行日期';

--
-- 转换日期格式为时间戳
--
UPDATE `transaction_log` SET `dateTime`=unix_timestamp(`dateTime`) WHERE 1;

ALTER TABLE `transaction_log` CHANGE `dateTime` `dateTime` INT( 11 ) NOT NULL COMMENT '日期';

ALTER TABLE `transaction_log` ADD INDEX ( `stockCode` ) ;

CREATE TABLE IF NOT EXISTS `lszjlx` (
  `id` int(11) NOT NULL auto_increment,
  `stockCode` char(6) NOT NULL COMMENT '股票代码',
  `dateTime` int(11) NOT NULL,
  `closePrice` decimal(7,2) NOT NULL COMMENT '收盘价',
  `changePrice` decimal(7,2) NOT NULL COMMENT '涨跌幅',
  `percentChg` decimal(4,2) NOT NULL COMMENT '换手率',
  `income` int(10) NOT NULL COMMENT '资金流入（万元）',
  `expenditure` int(10) NOT NULL COMMENT '资金流出（万元）',
  `netIncome` int(10) NOT NULL COMMENT '净资金流入（万元）',
  `mainIncome` int(10) NOT NULL COMMENT '主力资金流入（万元）',
  `mainExpenditure` int(10) NOT NULL COMMENT '主力资金流出（万元）',
  `netMainIncome` int(10) NOT NULL COMMENT '净主力资金流入（万元）',
  PRIMARY KEY  (`id`),
  KEY `stockCode` (`stockCode`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='股票历史资金流向表';