-- phpMyAdmin SQL Dump
-- version 4.2.7.1
-- http://www.phpmyadmin.net
--
-- Host: 127.0.0.1
-- Generation Time: 2016-02-19 08:41:15
-- 服务器版本： 5.6.20
-- PHP Version: 5.5.15

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `meiyuan`
--

-- --------------------------------------------------------

--
-- 表的结构 `my_menu`
--

CREATE TABLE IF NOT EXISTS `my_menu` (
`menu_id` int(11) NOT NULL,
  `title` varchar(100) DEFAULT NULL COMMENT '权限名称',
  `icon` varchar(100) DEFAULT NULL COMMENT '权限图标',
  `url` varchar(100) DEFAULT NULL COMMENT '权限的url',
  `visible` int(11) DEFAULT NULL COMMENT '是否可见 1 可见 0 不可见',
  `parent_id` int(11) DEFAULT NULL COMMENT '父节点id ，0为顶级节点',
  `order_no` varchar(4) DEFAULT NULL COMMENT '排序号',
  `status` int(11) DEFAULT NULL COMMENT '状态 1 正常 0 禁用',
  `remark1` varchar(100) DEFAULT NULL,
  `remark2` varchar(100) DEFAULT NULL,
  `remrak3` varchar(100) DEFAULT NULL,
  `remark4` varchar(100) DEFAULT NULL,
  `remark5` varchar(100) DEFAULT NULL,
  `remark6` varchar(100) DEFAULT NULL
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='权限表' AUTO_INCREMENT=23 ;

--
-- 转存表中的数据 `my_menu`
--

INSERT INTO `my_menu` (`menu_id`, `title`, `icon`, `url`, `visible`, `parent_id`, `order_no`, `status`, `remark1`, `remark2`, `remrak3`, `remark4`, `remark5`, `remark6`) VALUES
(1, '红包管理', 'fa fa-file-code-o', 'redpacket/index', 1, 0, '0002', 1, NULL, NULL, NULL, NULL, NULL, NULL),
(2, '审核管理', 'fa  fa-check-square-o', 'auditmanage/index', 1, 0, '0001', 1, NULL, NULL, NULL, NULL, NULL, NULL),
(3, '愿望管理', 'fa fa-list', '1#', 1, 0, '0003', 1, NULL, NULL, NULL, NULL, NULL, NULL),
(4, '进行中愿望', 'fa fa-circle-o', 'wishmanage/index', 1, 3, '0004', 1, NULL, NULL, NULL, NULL, NULL, NULL),
(5, '历史愿望', 'fa fa-circle-o', 'wishmanage/indexhis', 1, 3, '0005', 1, NULL, NULL, NULL, NULL, NULL, NULL),
(6, '推荐管理', 'fa fa-outdent', 'wishrecommend/index', 1, 0, '0006', 1, NULL, NULL, NULL, NULL, NULL, NULL),
(7, '热词管理', 'fa fa-eye', 'hotwords/index', 1, 0, '0007', 1, NULL, NULL, NULL, NULL, NULL, NULL),
(10, '举报管理', 'fa fa-phone', 'reportmanage/index', 1, 0, '0008', 1, NULL, NULL, NULL, NULL, NULL, NULL),
(11, '轮播图管理', 'fa  fa-file-image-o', 'carouselmanage/index', 1, 0, '0009', 1, NULL, NULL, NULL, NULL, NULL, NULL),
(12, '客户管理', 'fa fa-users', 'clientmanage/index', 1, 0, '0010', 1, NULL, NULL, NULL, NULL, NULL, NULL),
(13, '评论管理', 'fa fa-commenting', 'commentmanage/index', 1, 0, '0011', 1, NULL, NULL, NULL, NULL, NULL, NULL),
(14, '财务管理', 'fa fa-money', '2#', 1, 0, '0012', 1, NULL, NULL, NULL, NULL, NULL, NULL),
(15, '提现打款管理', 'fa fa-file-code-o', 'getcash/index', 1, 14, '0013', 1, NULL, NULL, NULL, NULL, NULL, NULL),
(16, '美愿基金借款管理', 'fa fa-file-code-o', 'fundborrow/index', 1, 14, '0014', 1, NULL, NULL, NULL, NULL, NULL, NULL),
(17, '美愿基金账单管理', 'fa fa-file-code-o', 'mybill/index', 1, 14, '0015', 1, NULL, NULL, NULL, NULL, NULL, NULL),
(18, '人员管理', 'fa fa-user', 'usermanage/index', 1, 0, '0016', 1, NULL, NULL, NULL, NULL, NULL, NULL),
(19, '系统管理', 'fa fa-wrench', '3#', 1, 0, '0017', 1, NULL, NULL, NULL, NULL, NULL, NULL),
(20, '签到打赏红包设置', 'fa fa-file-code-o', 'system/redpacketset', 1, 19, '0018', 1, NULL, NULL, NULL, NULL, NULL, NULL),
(21, '安卓版本管理', 'fa fa-dashboard', 'updatemanage/updateandroid', 1, 19, '0019', 1, NULL, NULL, NULL, NULL, NULL, NULL),
(22, '菜单管理', 'fa fa-dashboard', 'system/menu', 1, 19, '0020', 1, NULL, NULL, NULL, NULL, NULL, NULL);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `my_menu`
--
ALTER TABLE `my_menu`
 ADD PRIMARY KEY (`menu_id`), ADD UNIQUE KEY `url` (`url`), ADD KEY `parent_id` (`parent_id`,`order_no`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `my_menu`
--
ALTER TABLE `my_menu`
MODIFY `menu_id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=23;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
