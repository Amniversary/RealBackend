-- phpMyAdmin SQL Dump
-- version 4.2.7.1
-- http://www.phpmyadmin.net
--
-- Host: 127.0.0.1
-- Generation Time: 2016-04-21 03:15:19
-- 服务器版本： 5.6.20
-- PHP Version: 5.5.15

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

--
-- Database: `meibo`
--

-- --------------------------------------------------------

--
-- 表的结构 `mb_api_log`
--

CREATE TABLE IF NOT EXISTS `mb_api_log` (
`api_id` int(11) NOT NULL,
  `device_type` int(11) DEFAULT NULL,
  `fun_id` varchar(100) DEFAULT NULL,
  `unique_no` varchar(100) DEFAULT NULL,
  `device_no` varchar(100) DEFAULT NULL,
  `create_time` datetime DEFAULT NULL,
  `remark1` varchar(100) DEFAULT NULL,
  `remark2` varchar(100) DEFAULT NULL,
  `remark3` varchar(100) DEFAULT NULL,
  `remark4` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- 表的结构 `mb_attention`
--

CREATE TABLE IF NOT EXISTS `mb_attention` (
`record_id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `friend_user_id` int(11) DEFAULT NULL,
  `chat_pic` varchar(100) DEFAULT NULL,
  `nick_name` varchar(100) DEFAULT NULL,
  `hide_msg` int(11) DEFAULT NULL,
  `remark1` varchar(100) DEFAULT NULL,
  `remark2` varchar(100) DEFAULT NULL,
  `remark3` varchar(100) DEFAULT NULL,
  `remark4` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- 表的结构 `mb_balance`
--

CREATE TABLE IF NOT EXISTS `mb_balance` (
`balance_id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `bean_balance` int(11) DEFAULT NULL,
  `pay_pwd` varchar(200) DEFAULT NULL,
  `ticket_count` int(11) DEFAULT NULL,
  `ticket_real_sum` int(11) DEFAULT NULL,
  `ticket_count_sum` int(11) DEFAULT NULL,
  `virtual_ticket_count` int(11) DEFAULT NULL,
  `send_ticket_count` int(11) DEFAULT NULL,
  `rand_str` varchar(100) DEFAULT NULL,
  `sign` int(11) DEFAULT NULL,
  `remark1` varchar(100) DEFAULT NULL,
  `remark2` varchar(100) DEFAULT NULL,
  `remark3` varchar(100) DEFAULT NULL,
  `remark4` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- 表的结构 `mb_business_check`
--

CREATE TABLE IF NOT EXISTS `mb_business_check` (
`business_check_id` int(11) NOT NULL,
  `relate_id` int(11) DEFAULT NULL,
  `business_type` int(11) DEFAULT NULL,
  `status` int(11) DEFAULT NULL,
  `check_result_status` int(11) DEFAULT NULL,
  `create_time` datetime DEFAULT NULL,
  `check_time` datetime DEFAULT NULL,
  `check_user_id` int(11) DEFAULT NULL,
  `check_user_name` varchar(100) DEFAULT NULL,
  `create_user_id` int(11) DEFAULT NULL,
  `create_user_name` varchar(100) DEFAULT NULL,
  `check_no` int(11) DEFAULT NULL,
  `refused_reason` varchar(100) DEFAULT NULL,
  `remark1` varchar(100) DEFAULT NULL,
  `remark2` varchar(100) DEFAULT NULL,
  `remark3` varchar(100) DEFAULT NULL,
  `remark4` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- 表的结构 `mb_carousel`
--

CREATE TABLE IF NOT EXISTS `mb_carousel` (
`carousel_id` int(11) NOT NULL,
  `title` varchar(100) DEFAULT NULL,
  `discribtion` varchar(100) DEFAULT NULL,
  `pic_url` varchar(100) DEFAULT NULL,
  `action_type` varchar(100) DEFAULT NULL,
  `action_content` text,
  `order_no` varchar(10) DEFAULT NULL,
  `status` int(11) DEFAULT NULL,
  `remark1` varchar(100) DEFAULT NULL,
  `remark2` varchar(100) DEFAULT NULL,
  `remark3` varchar(100) DEFAULT NULL,
  `remark4` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- 表的结构 `mb_cashing_type`
--

CREATE TABLE IF NOT EXISTS `mb_cashing_type` (
  `payment_id` int(11) NOT NULL,
  `code` int(11) NOT NULL,
  `status` int(11) NOT NULL,
  `title` varchar(100) NOT NULL,
  `order_no` varchar(10) NOT NULL,
  `remark1` varchar(100) NOT NULL,
  `remark2` varchar(100) NOT NULL,
  `remark3` varchar(100) NOT NULL,
  `remark4` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- 表的结构 `mb_chat_room`
--

CREATE TABLE IF NOT EXISTS `mb_chat_room` (
`room_id` int(11) NOT NULL,
  `living_id` int(11) DEFAULT NULL,
  `room_name` varchar(100) DEFAULT NULL,
  `room_master_id` int(11) DEFAULT NULL,
  `manager_num` int(11) DEFAULT NULL,
  `icon` int(11) DEFAULT NULL,
  `create_time` datetime DEFAULT NULL,
  `status` int(11) DEFAULT NULL,
  `public` int(11) DEFAULT NULL,
  `approval` int(11) DEFAULT NULL,
  `describtion` text,
  `other_id` int(11) DEFAULT NULL,
  `back_pic` varchar(100) DEFAULT NULL,
  `remark1` varchar(100) DEFAULT NULL,
  `remark2` varchar(100) DEFAULT NULL,
  `remark3` varchar(100) DEFAULT NULL,
  `remark4` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- 表的结构 `mb_chat_room_member`
--

CREATE TABLE IF NOT EXISTS `mb_chat_room_member` (
`record_id` int(11) NOT NULL,
  `owner` int(11) DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL,
  `group_id` int(11) DEFAULT NULL,
  `chat_pic` varchar(100) DEFAULT NULL,
  `nick_name` varchar(100) DEFAULT NULL,
  `hide_msg` int(11) DEFAULT NULL,
  `create_time` datetime DEFAULT NULL,
  `leaving_time` datetime DEFAULT NULL,
  `status` int(11) DEFAULT NULL,
  `remark1` varchar(100) DEFAULT NULL,
  `remark2` varchar(100) DEFAULT NULL,
  `remark3` varchar(100) DEFAULT NULL,
  `remark4` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- 表的结构 `mb_city`
--

CREATE TABLE IF NOT EXISTS `mb_city` (
`city_id` int(11) NOT NULL,
  `city_name` varchar(100) DEFAULT NULL,
  `pid` varchar(100) DEFAULT NULL,
  `help_code` varchar(100) DEFAULT NULL,
  `all_code` varchar(100) DEFAULT NULL,
  `city_type` int(11) DEFAULT NULL,
  `longitude` decimal(20,10) DEFAULT NULL,
  `latitude` decimal(20,10) DEFAULT NULL,
  `remark1` varchar(100) DEFAULT NULL,
  `remark2` varchar(100) DEFAULT NULL,
  `remark3` varchar(100) DEFAULT NULL,
  `remark4` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- 表的结构 `mb_client`
--

CREATE TABLE IF NOT EXISTS `mb_client` (
`client_id` int(11) NOT NULL,
  `unique_no` varchar(100) DEFAULT NULL,
  `register_type` int(11) DEFAULT NULL COMMENT '类型 1 手机 2 微信 3 微博 4 QQ',
  `city` int(11) DEFAULT NULL,
  `age` int(11) DEFAULT NULL,
  `nick_name` varchar(100) DEFAULT NULL,
  `pic` varchar(100) DEFAULT NULL,
  `main_pic` varchar(100) DEFAULT NULL,
  `sign_name` varchar(100) DEFAULT NULL,
  `phone_no` varchar(20) DEFAULT NULL,
  `device_no` varchar(100) DEFAULT NULL,
  `device_type` int(11) DEFAULT NULL,
  `sex` int(11) DEFAULT NULL,
  `status` int(11) DEFAULT NULL,
  `create_time` datetime DEFAULT NULL,
  `is_bind_weixin` int(11) DEFAULT NULL COMMENT '绑定微信 默认1 否  2是',
  `is_bind_alipay` int(11) DEFAULT NULL COMMENT '绑定支付宝 默认1 否  2是',
  `is_inner` int(11) DEFAULT NULL,
  `remark1` varchar(100) DEFAULT NULL,
  `remark2` varchar(100) DEFAULT NULL,
  `remark3` varchar(100) DEFAULT NULL,
  `remark4` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- 表的结构 `mb_client_active`
--

CREATE TABLE IF NOT EXISTS `mb_client_active` (
`active_id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `attention_num` int(11) DEFAULT NULL,
  `funs_num` int(11) DEFAULT NULL,
  `experience` int(11) DEFAULT NULL,
  `level_no` int(11) DEFAULT NULL,
  `level_pic` varchar(100) DEFAULT NULL,
  `remark1` varchar(100) DEFAULT NULL,
  `remark2` varchar(100) DEFAULT NULL,
  `remark3` varchar(100) DEFAULT NULL,
  `remark4` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- 表的结构 `mb_client_balance_log`
--

CREATE TABLE IF NOT EXISTS `mb_client_balance_log` (
`log_id` int(11) NOT NULL,
  `device_type` int(11) DEFAULT NULL COMMENT '设备类型',
  `user_id` int(11) DEFAULT NULL COMMENT '用户id',
  `user_account_id` int(11) DEFAULT NULL COMMENT '用户账号id',
  `log_type` int(11) DEFAULT NULL COMMENT '日志类型',
  `operate_type` int(11) DEFAULT NULL COMMENT '操作类型',
  `operate_value` decimal(12,2) DEFAULT NULL COMMENT '交易数',
  `change_rate` int(11) DEFAULT NULL COMMENT '转化率',
  `result_value` decimal(12,2) DEFAULT NULL COMMENT '结果数',
  `before_balance` decimal(12,2) DEFAULT NULL COMMENT '操作前余额',
  `after_balance` decimal(12,2) DEFAULT NULL COMMENT '操作后金额',
  `create_time` datetime DEFAULT NULL COMMENT '操作时间',
  `unique_op_id` varchar(100) DEFAULT NULL COMMENT '唯一操作号',
  `remark1` varchar(100) DEFAULT NULL,
  `remark2` varchar(100) DEFAULT NULL,
  `remark3` varchar(100) DEFAULT NULL,
  `remark4` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='mb_client_balance_log' AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- 表的结构 `mb_client_cash_type_detail`
--

CREATE TABLE IF NOT EXISTS `mb_client_cash_type_detail` (
`record_id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `alipay_no` varchar(100) DEFAULT NULL,
  `identity_no` varchar(100) DEFAULT NULL,
  `real_name` varchar(100) DEFAULT NULL,
  `card_no` varchar(100) DEFAULT NULL,
  `bank_name` varchar(100) DEFAULT NULL,
  `create_time` datetime DEFAULT NULL,
  `remark1` varchar(100) DEFAULT NULL,
  `remark2` varchar(100) DEFAULT NULL,
  `remark3` varchar(100) DEFAULT NULL,
  `remark4` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- 表的结构 `mb_client_other`
--

CREATE TABLE IF NOT EXISTS `mb_client_other` (
`record_id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `other_id` int(11) DEFAULT NULL,
  `register_type` int(11) DEFAULT NULL,
  `remark1` varchar(100) DEFAULT NULL,
  `remark2` varchar(100) DEFAULT NULL,
  `remark3` varchar(100) DEFAULT NULL,
  `remark4` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- 表的结构 `mb_experience_log`
--

CREATE TABLE IF NOT EXISTS `mb_experience_log` (
`log_id` int(11) NOT NULL,
  `device_type` int(11) DEFAULT NULL COMMENT '设备类型',
  `source_type` int(11) DEFAULT NULL COMMENT '来源类型',
  `reward_user_id` int(11) DEFAULT NULL COMMENT '打赏人id',
  `to_user_id` int(11) DEFAULT NULL COMMENT '被打赏人id',
  `living_id` int(11) DEFAULT NULL COMMENT '直播id',
  `room_id` int(11) DEFAULT NULL COMMENT '直播聊天室id',
  `living_time` decimal(12,2) DEFAULT NULL COMMENT '直播时间或观看时间',
  `gift_id` int(11) DEFAULT NULL COMMENT '礼物id',
  `gift_type` int(11) DEFAULT NULL COMMENT '礼物类型',
  `gift_name` varchar(100) DEFAULT NULL COMMENT '礼物名称',
  `gift_value` decimal(12,2) DEFAULT NULL COMMENT '礼物豆值',
  `change_rate` int(11) DEFAULT NULL COMMENT '经验转化率',
  `experience` int(11) DEFAULT NULL COMMENT '经验证',
  `create_time` datetime DEFAULT NULL COMMENT '创建时间',
  `remark1` varchar(100) DEFAULT NULL,
  `remark2` varchar(100) DEFAULT NULL,
  `remark3` varchar(100) DEFAULT NULL,
  `remark4` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='经验获取日志表' AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- 表的结构 `mb_gift`
--

CREATE TABLE IF NOT EXISTS `mb_gift` (
`gift_id` int(11) NOT NULL,
  `gift_name` varchar(100) DEFAULT NULL,
  `pic` varchar(100) DEFAULT NULL,
  `gift_value` decimal(12,2) DEFAULT NULL,
  `remark1` varchar(100) DEFAULT NULL,
  `remark2` varchar(100) DEFAULT NULL,
  `remark3` varchar(100) DEFAULT NULL,
  `remark4` varchar(100) DEFAULT NULL,
  `order_no` varchar(10) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- 表的结构 `mb_goods`
--

CREATE TABLE IF NOT EXISTS `mb_goods` (
`goods_id` int(11) NOT NULL,
  `goods_name` int(11) DEFAULT NULL,
  `pic` varchar(100) DEFAULT NULL,
  `goods_price` int(11) DEFAULT NULL,
  `sale_type` int(11) DEFAULT NULL,
  `status` int(11) DEFAULT NULL,
  `goods_type` int(11) DEFAULT NULL,
  `num` int(11) DEFAULT NULL,
  `order_no` varchar(10) DEFAULT NULL,
  `remark1` varchar(100) DEFAULT NULL,
  `remark2` varchar(100) DEFAULT NULL,
  `remark3` varchar(100) DEFAULT NULL,
  `remark4` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- 表的结构 `mb_hot_words`
--

CREATE TABLE IF NOT EXISTS `mb_hot_words` (
  `hot_words_id` int(11) NOT NULL,
  `words_type` int(11) DEFAULT NULL,
  `content` varchar(100) DEFAULT NULL,
  `order_no` varchar(100) DEFAULT NULL,
  `status` int(11) DEFAULT NULL,
  `remark1` varchar(100) DEFAULT NULL,
  `remark2` varchar(100) DEFAULT NULL,
  `remark3` varchar(100) DEFAULT NULL,
  `remark4` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- 表的结构 `mb_level`
--

CREATE TABLE IF NOT EXISTS `mb_level` (
`level_id` int(11) NOT NULL,
  `level_name` varchar(100) DEFAULT NULL,
  `experience` int(11) DEFAULT NULL,
  `level_pic` varchar(100) DEFAULT NULL,
  `order_no` int(11) DEFAULT NULL,
  `remark1` varchar(100) DEFAULT NULL,
  `remark2` varchar(100) DEFAULT NULL,
  `remark3` varchar(100) DEFAULT NULL,
  `remark4` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- 表的结构 `mb_living`
--

CREATE TABLE IF NOT EXISTS `mb_living` (
`living_id` int(11) NOT NULL,
  `device_type` int(11) DEFAULT NULL,
  `living_title` varchar(100) DEFAULT NULL,
  `living_master_id` int(11) DEFAULT NULL,
  `create_time` datetime DEFAULT NULL,
  `order_no` varchar(10) DEFAULT NULL,
  `push_type` int(11) DEFAULT NULL,
  `push_url` varchar(100) DEFAULT NULL,
  `pull_http_url` varchar(100) DEFAULT NULL,
  `pull_rtmp_url` varchar(100) DEFAULT NULL,
  `pull_hls_url` varchar(100) DEFAULT NULL,
  `city` varchar(100) DEFAULT NULL,
  `longitude` decimal(20,10) DEFAULT NULL,
  `latitude` decimal(20,10) DEFAULT NULL,
  `is_official` int(11) DEFAULT NULL,
  `status` int(11) DEFAULT NULL,
  `finish_time` datetime DEFAULT NULL,
  `living_time` decimal(12,2) DEFAULT NULL,
  `is_to_expirence` int(11) DEFAULT NULL,
  `remark1` varchar(100) DEFAULT NULL,
  `remark2` varchar(100) DEFAULT NULL,
  `remark3` varchar(100) DEFAULT NULL,
  `remark4` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- 表的结构 `mb_livingmaster_hot`
--

CREATE TABLE IF NOT EXISTS `mb_livingmaster_hot` (
`hot_id` int(11) NOT NULL,
  `livingmaster_id` int(11) NOT NULL,
  `hot_type` int(11) NOT NULL,
  `hot_num` int(11) DEFAULT NULL,
  `order_no` varchar(10) DEFAULT NULL,
  `remark1` varchar(100) DEFAULT NULL,
  `remark2` varchar(100) DEFAULT NULL,
  `remark3` varchar(100) DEFAULT NULL,
  `remark4` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- 表的结构 `mb_living_goods`
--

CREATE TABLE IF NOT EXISTS `mb_living_goods` (
`living_good_id` int(11) NOT NULL,
  `living_id` int(11) DEFAULT NULL,
  `goods_num` int(11) DEFAULT NULL,
  `remark1` varchar(100) DEFAULT NULL,
  `remark2` varchar(100) DEFAULT NULL,
  `remark3` varchar(100) DEFAULT NULL,
  `remark4` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- 表的结构 `mb_living_hot`
--

CREATE TABLE IF NOT EXISTS `mb_living_hot` (
`hot_id` int(11) NOT NULL,
  `living_id` int(11) DEFAULT NULL,
  `hot_num` int(11) DEFAULT NULL,
  `order_no` varchar(10) DEFAULT NULL,
  `remark1` varchar(100) DEFAULT NULL,
  `remark2` varchar(100) DEFAULT NULL,
  `remark3` varchar(100) DEFAULT NULL,
  `remark4` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- 表的结构 `mb_living_personnum`
--

CREATE TABLE IF NOT EXISTS `mb_living_personnum` (
`living_personnum_id` int(11) NOT NULL,
  `living_id` int(11) DEFAULT NULL,
  `person_count` int(11) DEFAULT NULL,
  `person_count_total` int(11) DEFAULT NULL,
  `remark1` varchar(100) DEFAULT NULL,
  `remark2` varchar(100) DEFAULT NULL,
  `remark3` varchar(100) DEFAULT NULL,
  `remark4` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- 表的结构 `mb_living_tickets`
--

CREATE TABLE IF NOT EXISTS `mb_living_tickets` (
`living_tickets_id` int(11) NOT NULL,
  `living_id` int(11) DEFAULT NULL,
  `tickets_num` int(11) DEFAULT NULL,
  `tickets_real_num` int(11) DEFAULT NULL,
  `remark1` varchar(100) DEFAULT NULL,
  `remark2` varchar(100) DEFAULT NULL,
  `remark3` varchar(100) DEFAULT NULL,
  `remark4` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

-- ----------------------------
-- Table structure for `mb_menu`
-- ----------------------------
CREATE TABLE IF NOT EXISTS `mb_menu` (
`menu_id` int(11) NOT NULL,
  `title` varchar(100) DEFAULT NULL,
  `icon` varchar(100) DEFAULT NULL,
  `url` varchar(100) DEFAULT NULL,
  `visible` int(11) DEFAULT NULL,
  `parent_id` int(11) DEFAULT NULL,
  `order_no` varchar(10) DEFAULT NULL,
  `status` int(11) DEFAULT NULL,
  `remark1` varchar(100) DEFAULT NULL,
  `remark2` varchar(100) DEFAULT NULL,
  `remrak3` varchar(100) DEFAULT NULL,
  `remark4` varchar(100) DEFAULT NULL
) ENGINE=InnoDB  DEFAULT CHARSET=utf8mb4 AUTO_INCREMENT=127 ;

--
-- 转存表中的数据 `mb_menu`
--

INSERT INTO `mb_menu` (`menu_id`, `title`, `icon`, `url`, `visible`, `parent_id`, `order_no`, `status`, `remark1`, `remark2`, `remrak3`, `remark4`) VALUES
(1, '首页统计', NULL, 'site/index', 0, 0, '0000', 1, NULL, NULL, NULL, NULL),
(2, '客户信息管理', 'fa fa-group', '1#', 1, 0, '0001', 1, NULL, NULL, NULL, NULL),
(3, '客户信息', 'fa fa-user', 'client/index', 1, 2, '0085', 1, NULL, NULL, NULL, NULL),
(4, '设置超管', NULL, 'client/set_client_type', 0, 3, '0198', 1, NULL, NULL, NULL, NULL),
(5, '客户状态', NULL, 'client/setstatus', 0, 3, NULL, 1, NULL, NULL, NULL, NULL),
(6, '客户内部', NULL, 'client/setinner', 0, 3, NULL, 1, NULL, NULL, NULL, NULL),
(7, '客户签约', NULL, 'client/contract', 0, 3, NULL, 1, NULL, NULL, NULL, NULL),
(8, '客户签约率', NULL, 'client/cash_rite', 0, 3, NULL, 1, NULL, NULL, NULL, NULL),
(9, '客户账号交换', NULL, 'client/set_client_id', 0, 3, NULL, 1, NULL, NULL, NULL, NULL),
(10, '客户微信解绑', NULL, 'client/unbindwecat', 0, 3, NULL, 1, NULL, NULL, NULL, NULL),
(11, '客户财务信息', 'fa fa-money', 'client/client_finance_index', 1, 2, '0090', 1, NULL, NULL, NULL, NULL),
(12, '客户财务修改豆', NULL, 'client/update_bean', 0, 11, NULL, 1, NULL, NULL, NULL, NULL),
(13, '客户财务余额详情', NULL, 'client/moneydetail', 0, 11, NULL, 1, NULL, NULL, NULL, NULL),
(14, '客户财务票详情', NULL, 'client/ticket_detail', 0, 11, NULL, 1, NULL, NULL, NULL, NULL),
(15, '直播管理', 'fa fa-play-circle', '2#', 1, 0, '0005', 1, NULL, NULL, NULL, NULL),
(16, '热门直播管理', 'fa fa-caret-square-o-right', 'living/hot_living', 1, 15, '0075', 1, NULL, NULL, NULL, NULL),
(17, '热门用户状态', NULL, 'living/set_status', 0, 16, NULL, 1, NULL, NULL, NULL, NULL),
(18, '热门观看直播', NULL, 'living/look_living', 0, 16, NULL, 1, NULL, NULL, NULL, NULL),
(19, '热门排序号', NULL, 'living/set_order', 0, 16, NULL, 1, NULL, NULL, NULL, NULL),
(20, '热门场次', NULL, 'living/living_hot', 0, 16, NULL, 1, NULL, NULL, NULL, NULL),
(21, '支付方式管理', 'fa fa-cart-plus', 'paymentmanage/index', 1, 0, '0019', 1, NULL, NULL, NULL, NULL),
(22, '支付方式状态', NULL, 'paymentmanage/setstatus', 0, 21, NULL, 1, NULL, NULL, NULL, NULL),
(23, '支付方式编辑', NULL, 'paymentmanage/update', 0, 21, NULL, 1, NULL, NULL, NULL, NULL),
(24, '支付方式新增', NULL, 'paymentmanage/create', 0, 21, NULL, 1, NULL, NULL, NULL, NULL),
(25, '支付方式删除', NULL, 'paymentmanage/delete', 0, 21, NULL, 1, NULL, NULL, NULL, NULL),
(26, '等级管理', 'fa fa-moon-o', 'level/index', 1, 0, '0030', 1, NULL, NULL, NULL, NULL),
(27, '编辑等级', NULL, 'level/update', 0, 26, '0300', 1, NULL, NULL, NULL, NULL),
(28, '等级新增', NULL, 'level/create', 0, 26, NULL, 1, NULL, NULL, NULL, NULL),
(29, '等级删除', NULL, 'level/delete', 0, 26, '0', 1, NULL, NULL, NULL, NULL),
(30, '轮播图管理', 'fa  fa-file-image-o', 'carouselmanage/index', 1, 0, '0035', 1, NULL, NULL, NULL, NULL),
(31, '轮播图新增', NULL, 'carouselmanage/create', 0, 30, '0200', 1, NULL, NULL, NULL, NULL),
(32, '轮播图状态', NULL, 'carouselmanage/setstatus', 0, 30, '0210', 1, NULL, NULL, NULL, NULL),
(33, '轮播图编辑', NULL, 'carouselmanage/update', 0, 30, '0215', 1, NULL, NULL, NULL, NULL),
(34, '轮播图删除', NULL, 'carouselmanage/delete', 0, 30, '0220', 1, NULL, NULL, NULL, NULL),
(35, '举报管理', 'fa fa-phone', 'checkreport/index', 1, 0, '0040', 1, NULL, NULL, NULL, NULL),
(36, '举报状态', NULL, 'checkreport/set_status', 0, 35, NULL, 1, NULL, NULL, NULL, NULL),
(37, '举报查看详情', NULL, 'checkreport/detail', 0, 35, NULL, 1, NULL, NULL, NULL, NULL),
(38, '举报已审核页', NULL, 'checkreport/indexaudited', 0, 35, NULL, 1, NULL, NULL, NULL, NULL),
(39, '举报审核', NULL, 'checkreport/checkrefuse', 0, 35, NULL, 1, NULL, NULL, NULL, NULL),
(40, '人员管理', 'fa fa-user', 'usermanage/index', 1, 0, '0045', 1, NULL, NULL, NULL, NULL),
(41, '人员权限', NULL, 'usermanage/setprivilige', 0, 40, '0195', 1, NULL, NULL, NULL, NULL),
(42, '人员新增', NULL, 'usermanage/create', 0, 40, '0225', 1, NULL, NULL, NULL, NULL),
(43, '人员状态', NULL, 'usermanage/setstatus', 0, 40, '0230', 1, NULL, NULL, NULL, NULL),
(44, '人员编辑', NULL, 'usermanage/update', 0, 40, '0235', 1, NULL, NULL, NULL, NULL),
(45, '人员删除', NULL, 'usermanage/delete', 0, 40, '0240', 1, NULL, NULL, NULL, NULL),
(46, '人员密码', NULL, 'usermanage/resetpwd', 0, 40, '0245', 1, NULL, NULL, NULL, NULL),
(47, '人员审核号', NULL, 'usermanage/setcheckno', 0, 40, '0250', 1, NULL, NULL, NULL, NULL),
(48, '系统管理', 'fa fa-wrench', '3#', 1, 0, '0050', 1, NULL, NULL, NULL, NULL),
(49, '版本管理', 'fa fa-dashboard', 'versionmanage/index', 1, 48, '0095', 1, NULL, NULL, NULL, NULL),
(50, '版本管理新增', NULL, 'versionmanage/create', 0, 49, NULL, 1, NULL, NULL, NULL, NULL),
(51, '版本状态', NULL, 'versionmanage/setstatus', 0, 49, NULL, 1, NULL, NULL, NULL, NULL),
(52, '版本编辑', NULL, 'versionmanage/update', 0, 49, NULL, 1, NULL, NULL, NULL, NULL),
(53, '版本子版本', NULL, 'versionmanage/indexson', 0, 49, NULL, 1, NULL, NULL, NULL, NULL),
(54, '子版本强制更新', NULL, 'versionmanage/setstatusson', 0, 49, NULL, 1, NULL, NULL, NULL, NULL),
(55, '内部版本号', NULL, 'versionmanage/set_version_inner', 0, 49, NULL, 1, NULL, NULL, NULL, NULL),
(56, '子版本编辑', NULL, 'versionmanage/updateson', 0, 49, NULL, 1, NULL, NULL, NULL, NULL),
(57, '子版本详情', NULL, 'versionmanage/detailson', 0, 49, NULL, 1, NULL, NULL, NULL, NULL),
(58, '子版本删除', NULL, 'versionmanage/deleteson', 0, 49, NULL, 1, NULL, NULL, NULL, NULL),
(59, '子版本新增', NULL, 'versionmanage/createson', 0, 49, NULL, 1, NULL, NULL, NULL, NULL),
(60, '子版本是否登录', NULL, 'versionmanage/set_register', 0, 49, NULL, 1, NULL, NULL, NULL, NULL),
(61, '推送活动', 'fa fa-cog', 'system/pushactive', 1, 48, '0100', 1, NULL, NULL, NULL, NULL),
(62, '系统参数设置', 'fa fa-cogs', '8#', 1, 48, '0105', 1, NULL, NULL, NULL, NULL),
(63, '用户设备参数', 'fa fa-television', 'updatemanage/user_device_params', 1, 62, '0110', 1, NULL, NULL, NULL, NULL),
(64, '分享信息参数', 'fa fa-reply', 'updatemanage/share_info_params', 1, 62, '0115', 1, NULL, NULL, NULL, NULL),
(65, '签到打赏信息', 'fa fa-calendar', 'updatemanage/sign_reward_params', 1, 62, '0120', 1, NULL, NULL, NULL, NULL),
(66, '费率信息参数', 'fa fa-cny', 'updatemanage/rate_info_params', 1, 62, '0125', 1, NULL, NULL, NULL, NULL),
(67, '统计信息参数', 'fa fa-commenting', 'updatemanage/statistics_info_params', 1, 62, '0130', 1, NULL, NULL, NULL, NULL),
(68, '直播参数设置', 'fa fa-toggle-right', 'updatemanage/living_params', 1, 62, '0135', 1, NULL, NULL, NULL, NULL),
(69, '心跳参数设置', 'fa fa-heartbeat', 'updatemanage/heartbeat_params', 1, 62, '0140', 1, NULL, NULL, NULL, NULL),
(70, '消息发送', 'fa fa-commenting', 'systemmessage/index', 1, 62, '0145', 1, NULL, NULL, NULL, NULL),
(71, '系统参数标题', NULL, 'updatemanage/set_system_title', 0, 0, '0270', 1, NULL, NULL, NULL, NULL),
(72, '系统参数描述', NULL, 'updatemanage/set_description', 0, 0, '0275', 1, NULL, NULL, NULL, NULL),
(73, '参数内容1', NULL, 'updatemanage/set_value1', 0, 0, '0280', 1, NULL, NULL, NULL, NULL),
(74, '参数内容2', NULL, 'updatemanage/set_value2', 0, 0, '0285', 1, NULL, NULL, NULL, NULL),
(75, '参数内容3', NULL, 'updatemanage/set_value3', 0, 0, '0290', 1, NULL, NULL, NULL, NULL),
(76, '新增参数', NULL, 'updatemanage/create', 0, 0, '0295', 1, NULL, NULL, NULL, NULL),
(77, '消息发送编辑', NULL, 'systemmessage/update', 0, 70, NULL, 1, NULL, NULL, NULL, NULL),
(78, '消息发送状态', NULL, 'systemmessage/setstatus', 0, 70, NULL, 1, NULL, NULL, NULL, NULL),
(79, '消息删除', NULL, 'systemmessage/delete', 0, 70, NULL, 1, NULL, NULL, NULL, NULL),
(80, '系统消息新增', NULL, 'systemmessage/create', 0, 0, NULL, 1, NULL, NULL, NULL, NULL),
(81, '欢迎词设置', 'fa fa-commenting', 'enterroomnote/index', 1, 0, '0055', 1, NULL, NULL, NULL, NULL),
(82, '欢迎词编辑', NULL, 'enterroomnote/update', 0, 81, NULL, 1, NULL, NULL, NULL, NULL),
(83, '欢迎词新增', NULL, 'enterroomnote/create', 0, 81, NULL, 1, NULL, NULL, NULL, NULL),
(84, '欢迎词删除', NULL, 'enterroomnote/delete', 0, 81, NULL, 1, NULL, NULL, NULL, NULL),
(85, '直播认证审核管理', 'fa fa-user', '4#', 1, 0, '0060', 1, NULL, NULL, NULL, NULL),
(86, '审核', 'fa fa-user', 'approvebusinesscheck/index', 1, 85, '0150', 1, NULL, NULL, NULL, NULL),
(87, '直播认证查看详情', NULL, 'approvebusinesscheck/detail', 0, 86, NULL, 1, NULL, NULL, NULL, NULL),
(88, '直播认证已审核', NULL, 'approvebusinesscheck/indexaudited', 0, 86, NULL, 1, NULL, NULL, NULL, NULL),
(89, '直播认证审核', NULL, 'approvebusinesscheck/checkrefuse', 0, 86, NULL, 1, NULL, NULL, NULL, NULL),
(90, '票提现管理', 'fa fa-cart-plus', '6#', 1, 0, '0070', 1, NULL, NULL, NULL, NULL),
(91, '运营审核', 'fa fa-cart-plus', 'checkmoneygoods/index', 1, 90, '0170', 1, NULL, NULL, NULL, NULL),
(92, '财务打款', 'fa fa-cart-plus', 'checkmoneygoods/indexcash', 1, 90, '0175', 1, NULL, NULL, NULL, NULL),
(93, '运营已审核', NULL, 'checkmoneygoods/indexaudited', 0, 91, NULL, 1, NULL, NULL, NULL, NULL),
(94, '运营查看详情', NULL, 'checkmoneygoods/detail', 0, 91, NULL, 1, NULL, NULL, NULL, NULL),
(95, '票提现详情', NULL, 'checkmoneygoods/detailcash', 0, 92, NULL, 1, NULL, NULL, NULL, NULL),
(96, '数据统计', 'fa fa-calculator', '7#', 1, 0, '0080', 1, NULL, NULL, NULL, NULL),
(97, '主播直播时间', 'fa fa-circle-o', 'datastatistic/livingtime', 1, 96, '0180', 1, NULL, NULL, NULL, NULL),
(98, '主播收入', 'fa fa-circle-o', 'datastatistic/masterprofit', 1, 96, '0185', 1, NULL, NULL, NULL, NULL),
(99, '城市直播数统计', 'fa fa-calculator', '5#', 1, 96, '0190', 1, NULL, NULL, NULL, NULL),
(100, '日统计', 'fa fa-circle-o', 'statisticarea/index', 1, 99, '0155', 1, NULL, NULL, NULL, NULL),
(101, '周统计', 'fa fa-circle-o', 'statisticarea/week', 1, 99, '0160', 1, NULL, NULL, NULL, NULL),
(102, '月统计', 'fa fa-circle-o', 'statisticarea/month', 1, 99, '0165', 1, NULL, NULL, NULL, NULL),
(103, '充值记录管理', 'fa fa-fire', '10#', 1, 0, '0107', 1, NULL, NULL, NULL, NULL),
(104, '个人充值管理', 'fa fa-level-up', 'checkmoneygoods/userrecharge', 1, 103, '0176', 1, NULL, NULL, NULL, NULL),
(105, '个人充值检验', NULL, 'checkmoneygoods/check_recharge_recode', 0, 104, NULL, 1, NULL, NULL, NULL, NULL),
(106, '商品管理', 'fa fa-github-alt', '11#', 1, 0, '0111', 1, NULL, NULL, NULL, NULL),
(107, '豆商品管理', 'fa fa-cart-plus', 'goods/index', 1, 106, '0010', 1, NULL, NULL, NULL, NULL),
(108, '豆商品销售类型', NULL, 'goods/sale_type', 0, 106, NULL, 1, NULL, NULL, NULL, NULL),
(109, '豆商品销售状态', NULL, 'goods/status', 0, 106, NULL, 1, NULL, NULL, NULL, NULL),
(110, '豆商品类型', NULL, 'goods/goods_type', 0, 106, NULL, 1, NULL, NULL, NULL, NULL),
(111, '豆商品编辑', NULL, 'goods/update', 0, 106, NULL, 1, NULL, NULL, NULL, NULL),
(112, '豆商品删除', NULL, 'goods/delete', 0, 106, NULL, 1, NULL, NULL, NULL, NULL),
(113, '豆商品新增', NULL, 'goods/create', 0, 106, '0260', 1, NULL, NULL, NULL, NULL),
(114, '票转豆商品管理', 'fa fa-cart-plus', 'tobeangoods/index', 1, 106, '0015', 1, NULL, NULL, NULL, NULL),
(115, '票转豆编辑', NULL, 'tobeangoods/update', 0, 114, NULL, 1, NULL, NULL, NULL, NULL),
(116, '票转豆删除', NULL, 'tobeangoods/delete', 0, 114, NULL, 1, NULL, NULL, NULL, NULL),
(117, '票转豆新增', NULL, 'tobeangoods/create', 0, 114, NULL, 1, NULL, NULL, NULL, NULL),
(118, '票转豆状态', NULL, 'tobeangoods/setstatus', 0, 114, NULL, 1, NULL, NULL, NULL, NULL),
(119, '票提现商品管理', 'fa fa-cart-plus', 'goodstickettocash/index', 1, 106, '0020', 1, NULL, NULL, NULL, NULL),
(120, '票提现新增', NULL, 'goodstickettocash/create', 0, 119, '', 1, NULL, NULL, NULL, NULL),
(121, '票提现编辑', NULL, 'goodstickettocash/update', 0, 119, NULL, 1, NULL, NULL, NULL, NULL),
(122, '票提现状态', NULL, 'goodstickettocash/setstatus', 0, 119, NULL, 1, NULL, NULL, NULL, NULL),
(123, '票提现删除', NULL, 'goodstickettocash/delete', 0, 119, NULL, 1, NULL, NULL, NULL, NULL),
(124, '礼物管理', 'fa fa-gift', 'gift/index', 1, 106, '0025', 1, NULL, NULL, NULL, NULL),
(125, '礼物新增', NULL, 'gift/create', 0, 124, NULL, 1, NULL, NULL, NULL, NULL),
(126, '礼物编辑', NULL, 'gift/update', 0, 124, NULL, 1, NULL, NULL, NULL, NULL),
(127, '礼物状态', NULL, 'gift/black', 0, 124, NULL, 1, NULL, NULL, NULL, NULL),
(128, '礼物删除', NULL, 'gift/delete', 0, 124, NULL, 1, NULL, NULL, NULL, NULL),
(129, '上传图片', NULL, 'mypic/upload_pic', 0, 0, '0255', 1, NULL, NULL, NULL, NULL),
(130, '票提现运营通过/拒绝', NULL, 'checkmoneygoods/checkrefuse', 0, 91, NULL, 1, NULL, NULL, NULL, NULL),
(131, '票提现账务打款通过', NULL, 'checkmoneygoods/playmoney', 0, 92, NULL, 1, NULL, NULL, NULL, NULL),
(132, '票提现运营通过/拒绝', NULL, 'checkmoneygoods/checkrefuse', 0, 91, NULL, 1, NULL, NULL, NULL, NULL),
(133, '票提现账务打款通过', NULL, 'checkmoneygoods/playmoney', 0, 92, NULL, 1, NULL, NULL, NULL, NULL),
(134, '直播认证批量审核', NULL, 'approvebusinesscheck/checkbatch', 0, 85, NULL, 1, NULL, NULL, NULL, NULL),
(135, '运营批量审核', NULL, 'checkmoneygoods/checkbatch', 0, 91, NULL, 1, NULL, NULL, NULL, NULL);
--
-- Indexes for table `mb_menu`
--
ALTER TABLE `mb_menu`
 ADD PRIMARY KEY (`menu_id`);

--
-- AUTO_INCREMENT for table `mb_menu`
--
ALTER TABLE `mb_menu`
MODIFY `menu_id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=127;
-- --------------------------------------------------------

--
-- 表的结构 `mb_payment`
--

CREATE TABLE IF NOT EXISTS `mb_payment` (
`payment_id` int(11) NOT NULL,
  `code` int(11) DEFAULT NULL,
  `status` int(11) DEFAULT NULL,
  `title` varchar(100) DEFAULT NULL,
  `order_no` varchar(10) DEFAULT NULL,
  `remark1` varchar(100) DEFAULT NULL,
  `remark2` varchar(100) DEFAULT NULL,
  `remark3` varchar(100) DEFAULT NULL,
  `remark4` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- 表的结构 `mb_recharge`
--

CREATE TABLE IF NOT EXISTS `mb_recharge` (
`recharge_id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `goods_id` int(11) DEFAULT NULL,
  `goods_name` varchar(100) DEFAULT NULL,
  `goods_price` decimal(12,2) DEFAULT NULL,
  `goods_num` int(11) DEFAULT NULL,
  `bean_num` decimal(12,2) DEFAULT NULL,
  `pay_money` decimal(12,2) DEFAULT NULL,
  `status_result` int(11) DEFAULT NULL,
  `fail_reason` varchar(300) DEFAULT NULL,
  `pay_type` int(11) DEFAULT NULL,
  `pay_bill` varchar(100) DEFAULT NULL,
  `other_pay_bill` varchar(100) DEFAULT NULL,
  `pay_times` int(11) DEFAULT NULL,
  `create_time` datetime DEFAULT NULL,
  `remark1` varchar(100) DEFAULT NULL,
  `remark2` varchar(100) DEFAULT NULL,
  `remark3` varchar(100) DEFAULT NULL,
  `remark4` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- 表的结构 `mb_report`
--

CREATE TABLE IF NOT EXISTS `mb_report` (
`report_id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `nick_name` varchar(100) DEFAULT NULL,
  `scene` int(11) DEFAULT NULL,
  `report_type` int(11) DEFAULT NULL,
  `living_id` int(11) DEFAULT NULL,
  `report_user_id` int(11) DEFAULT NULL,
  `report_content` text,
  `create_time` datetime DEFAULT NULL,
  `status` int(11) DEFAULT NULL,
  `check_time` datetime DEFAULT NULL,
  `remark1` varchar(100) DEFAULT NULL,
  `remark2` varchar(100) DEFAULT NULL,
  `remark3` varchar(100) DEFAULT NULL,
  `remark4` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- 表的结构 `mb_reward`
--

CREATE TABLE IF NOT EXISTS `mb_reward` (
`reward_id` int(11) NOT NULL,
  `living_id` int(11) DEFAULT NULL,
  `reward_user_id` int(11) DEFAULT NULL,
  `living_master_id` int(11) DEFAULT NULL,
  `gift_id` int(11) DEFAULT NULL,
  `gift_name` varchar(100) DEFAULT NULL,
  `gift_type` int(11) DEFAULT NULL,
  `gift_value` decimal(21,2) DEFAULT NULL,
  `create_time` datetime DEFAULT NULL,
  `status` int(11) DEFAULT NULL,
  `remark1` varchar(100) DEFAULT NULL,
  `remark2` varchar(100) DEFAULT NULL,
  `remark3` varchar(100) DEFAULT NULL,
  `remark4` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- 表的结构 `mb_reward_log`
--

CREATE TABLE IF NOT EXISTS `mb_reward_log` (
`log_id` int(11) NOT NULL,
  `device_type` int(11) DEFAULT NULL COMMENT '设备类型',
  `reward_user_id` int(11) DEFAULT NULL COMMENT '打赏人id',
  `to_user_id` int(11) DEFAULT NULL COMMENT '被打赏人id',
  `living_id` int(11) DEFAULT NULL COMMENT '直播id',
  `room_id` int(11) DEFAULT NULL COMMENT '直播聊天室id',
  `gift_id` int(11) DEFAULT NULL COMMENT '礼物id',
  `gift_name` varchar(100) DEFAULT NULL COMMENT '礼物名称',
  `gift_value` decimal(12,2) DEFAULT NULL COMMENT '礼物豆值',
  `remark1` varchar(100) DEFAULT NULL,
  `remark2` varchar(100) DEFAULT NULL,
  `remark3` varchar(100) DEFAULT NULL,
  `remark4` varchar(1) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='打赏日志表' AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- 表的结构 `mb_set_user_check_no`
--

CREATE TABLE IF NOT EXISTS `mb_set_user_check_no` (
`set_check_no_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `start_no` int(11) DEFAULT NULL,
  `end_no` int(11) DEFAULT NULL,
  `remark1` varchar(100) DEFAULT NULL,
  `remark2` varchar(100) DEFAULT NULL,
  `remark3` varchar(100) DEFAULT NULL,
  `remark4` varchar(100) DEFAULT NULL
) ENGINE=InnoDB  DEFAULT CHARSET=utf8mb4 AUTO_INCREMENT=2 ;

--
-- 转存表中的数据 `mb_set_user_check_no`
--

INSERT INTO `mb_set_user_check_no` (`set_check_no_id`, `user_id`, `start_no`, `end_no`, `remark1`, `remark2`, `remark3`, `remark4`) VALUES
(1, 15, 0, 19, NULL, NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- 表的结构 `mb_sum_reward_tickets`
--

CREATE TABLE IF NOT EXISTS `mb_sum_reward_tickets` (
`reward_id` int(11) NOT NULL,
  `reward_user_id` int(11) DEFAULT NULL,
  `living_master_id` int(11) DEFAULT NULL,
  `ticket_num` int(11) DEFAULT NULL,
  `create_time` datetime DEFAULT NULL,
  `remark1` varchar(100) DEFAULT NULL,
  `remark2` varchar(100) DEFAULT NULL,
  `remark3` varchar(100) DEFAULT NULL,
  `remark4` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- 表的结构 `mb_system_params`
--

CREATE TABLE IF NOT EXISTS `mb_system_params` (
`params_id` int(11) NOT NULL,
  `group_id` int(11) DEFAULT NULL COMMENT '分组id',
  `code` varchar(100) DEFAULT NULL COMMENT '系统code参数',
  `title` varchar(100) DEFAULT NULL COMMENT '标题',
  `discribtion` varchar(100) DEFAULT NULL COMMENT '信息描述',
  `value1` varchar(100) DEFAULT NULL COMMENT '内容值1',
  `value2` varchar(100) DEFAULT NULL COMMENT '内容值2',
  `value3` varchar(100) DEFAULT NULL COMMENT '内容值3',
  `remark1` varchar(100) DEFAULT NULL COMMENT '备用字段1',
  `remark2` varchar(100) DEFAULT NULL COMMENT '备用字段2',
  `remark3` varchar(100) DEFAULT NULL COMMENT '备用字段3',
  `remark4` varchar(100) DEFAULT NULL COMMENT '备用字段4'
) ENGINE=InnoDB  DEFAULT CHARSET=utf8mb4 AUTO_INCREMENT=0 ;

--
-- 转存表中的数据 `mb_system_params`
--

INSERT INTO `mb_system_params` (`params_id`, `group_id`, `code`, `title`, `discribtion`, `value1`, `value2`, `value3`, `remark1`, `remark2`, `remark3`, `remark4`) VALUES
(1, 1, 'system_help', NULL, '系统帮助html在次编辑', '帮助', '访问链接', NULL, NULL, NULL, NULL, NULL),
(2, 1, 'system_join_to_us', NULL, '加入我们html编辑', '加入我们', '访问链接', NULL, NULL, NULL, NULL, NULL),
(3, 1, 'system_about_us', NULL, '关于我们的html编辑', '关于我们', '访问链接', NULL, NULL, NULL, NULL, NULL),
(4, 1, 'system_customer_call', NULL, NULL, '客服电话', '0571-28819117', NULL, NULL, NULL, NULL, NULL),
(5, 2, 'system_share_weixin_pic', NULL, NULL, '微信共享图片', 'http://image.matewish.cn/system/icon.png', NULL, NULL, NULL, NULL, NULL),
(7, 3, 'system_sign_circle', NULL, '默认7天', '签到周期', '7', NULL, NULL, NULL, NULL, NULL),
(9, 3, 'system_sign_red_packets', NULL, '设置一个红包，存放红包id', '签到红包', '1', NULL, NULL, NULL, NULL, NULL),
(13, 4, 'system_stu_borrow_by_stages_rate', NULL, '暂时3期，一起 1期 2期 3期，单位%', '学生借款费率', '6-5.5-5', NULL, NULL, NULL, NULL, NULL),
(14, 4, 'system_social_borrow_by_stages_rate', NULL, '针对社会人员的费率，单位%，依次1期 2期 3期', '社会人员借款费率', '7-6.5-6', NULL, NULL, NULL, NULL, NULL),
(15, 4, 'system_breach_rate', NULL, '默认5，单位%；借款金额的5%', '违约手续费率', '5', NULL, NULL, NULL, NULL, NULL),
(16, 4, 'system_last_breach_rate', NULL, '默认1 单位%；借款金额的1%', '后续违约费率', '1', NULL, NULL, NULL, NULL, NULL),
(17, 4, 'system_fund_init_money', NULL, '3000美愿基金初始金额', '美愿基金初始额度', '3000', NULL, NULL, NULL, NULL, NULL),
(18, 4, 'system_fund_by_stages_count', NULL, '默认3期', '美愿基金最大还款期数', '3', NULL, NULL, NULL, NULL, NULL),
(19, 1, 'system_nearby_distance', '', '附近的距离，单位米', '附近的距离', '2000', '', '', '', '', ''),
(20, 4, 'system_fund_rate_for_halfdelaytimes', NULL, '美愿基金半年内违约次数对应的还款手续费，从1次开始递增', '半年内违约次数对应还款手续费', '2-2-2', NULL, NULL, NULL, NULL, NULL),
(21, 4, 'system_fund_credite_value_for_halfdelaytimes', NULL, '美愿基金半年内违约次数对应的信用额度减少的百分比', '半年内逾期次数对应的减少信用额度', '0-10-20', NULL, NULL, NULL, NULL, NULL),
(22, 4, 'system_fund_time_unit', NULL, '美愿基金借款每期时间单位，默认月', '每期时间单位', '月', NULL, NULL, NULL, NULL, NULL),
(23, 4, 'system_days_breach_to_lastbreach', NULL, '违约后到持续违约的天数，默认10天', '持续违约开始天数', '10', NULL, NULL, NULL, NULL, NULL),
(24, 4, 'sys_cash_rate', NULL, '提现手续费率，默认3，单位%', '提现手续费率', '3', NULL, NULL, NULL, NULL, NULL),
(26, 3, 'system_reward_for_userself_packets', NULL, '打赏红包，给打赏的人的红包，直接返利', '打赏红包', '2', NULL, NULL, NULL, NULL, NULL),
(27, 3, 'system_reward_for_wish_public_packets', NULL, '打赏对应愿望的红包', '打赏愿望红包', '3', NULL, NULL, NULL, NULL, NULL),
(28, 2, 'system_share_wish_title', NULL, '愿望分享标题', '愿望分享标题', '新年新气象，和我一起来美愿实现愿望吧！', NULL, NULL, NULL, NULL, NULL),
(29, 2, 'system_share_wish_content', NULL, '愿望分享内容', '愿望分享内容', '下载美愿，快来支持我的愿望，您可获得3倍奖金，同时为我赢取1倍奖金！', NULL, NULL, NULL, NULL, NULL),
(30, 2, 'system_share_invite_title', NULL, '邀请朋友标题', '邀请朋友标题', '美愿，一个帮你实现愿望的app！', NULL, NULL, NULL, NULL, NULL),
(31, 2, 'system_share_invite_content', NULL, '邀请朋友内容', '邀请朋友内容', '美愿是一个共享社交金融平台，通过共享愿望的方式，实现每一个愿望。', NULL, NULL, NULL, NULL, NULL),
(32, 5, 'statistic_day_users_date', NULL, '统计日新增人数开始统计日期，统计到当天前一天结束', '2016-02-01 00:00:00', '2016-03-31 00:00:00', NULL, NULL, NULL, NULL, NULL),
(33, 5, 'statistic_day_users_month', NULL, '统计月份开始月份，2016-02；统计到当前月前一个月为止', '2016-02', '2016-03', NULL, NULL, NULL, NULL, NULL),
(34, 5, 'statistic_day_users_verify', NULL, '统计认证人数开始统计日期，统计到当天前一天结束', '2016-02-01 00:00:00', '2016-03-31 00:00:00', NULL, NULL, NULL, NULL, NULL),
(35, 1, 'system_reward_note', NULL, '可获得1倍奖金，5元封顶', '可获得1倍奖金，5元封顶', NULL, NULL, NULL, NULL, NULL, NULL),
(36, 5, 'statistic_month_users_verify', NULL, '统计认证开始月份，2016-02；统计到当前月前一个月为止', '2016-02', '2016-03', NULL, NULL, NULL, NULL, NULL),
(37, 1, 'system_pay', NULL, '支付方式图标', '支付方式图标', 'http://image.matewish.cn/pay_icon/pay.png', NULL, NULL, NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- 表的结构 `mb_ticket_to_bean`
--

CREATE TABLE IF NOT EXISTS `mb_ticket_to_bean` (
`record_id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `ticket_num` decimal(12,2) DEFAULT NULL,
  `bean_rate` int(11) DEFAULT NULL,
  `bean_num` decimal(12,2) DEFAULT NULL,
  `status` int(11) DEFAULT NULL,
  `refuesd_reason` varchar(100) DEFAULT NULL,
  `create_time` datetime DEFAULT NULL,
  `check_time` datetime DEFAULT NULL,
  `remark1` varchar(100) DEFAULT NULL,
  `remark2` varchar(100) DEFAULT NULL,
  `remark3` varchar(100) DEFAULT NULL,
  `remark4` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- 表的结构 `mb_ticket_to_cash`
--

CREATE TABLE IF NOT EXISTS `mb_ticket_to_cash` (
`record_id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `ticket_num` decimal(12,2) DEFAULT NULL,
  `cash_rate` int(11) DEFAULT NULL,
  `cash_fees` decimal(12,2) DEFAULT NULL,
  `real_cash_money` decimal(12,2) DEFAULT NULL,
  `status` int(11) DEFAULT NULL,
  `refuesd_reason` varchar(100) DEFAULT NULL,
  `finance_remark` varchar(100) DEFAULT NULL,
  `cash_type_id` int(11) DEFAULT NULL,
  `create_time` datetime DEFAULT NULL,
  `check_time` datetime DEFAULT NULL,
  `finace_ok_time` datetime DEFAULT NULL,
  `remark1` varchar(100) DEFAULT NULL,
  `remark2` varchar(100) DEFAULT NULL,
  `remark3` varchar(100) DEFAULT NULL,
  `remark4` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- 表的结构 `mb_time_livingmaster_ticketcount`
--

CREATE TABLE IF NOT EXISTS `mb_time_livingmaster_ticketcount` (
`record_id` int(11) NOT NULL,
  `livingmaster_id` int(11) NOT NULL,
  `hot_type` int(11) NOT NULL,
  `statistic_date` varchar(100) NOT NULL,
  `real_ticket_num` int(11) DEFAULT NULL,
  `ticket_num` int(11) DEFAULT NULL,
  `order_no` varchar(10) DEFAULT NULL,
  `remark1` varchar(100) DEFAULT NULL,
  `remark2` varchar(100) DEFAULT NULL,
  `remark3` varchar(100) DEFAULT NULL,
  `remark4` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- 表的结构 `mb_to_bean_goods`
--

CREATE TABLE IF NOT EXISTS `mb_to_bean_goods` (
`record_id` int(11) NOT NULL,
  `ticket_num` int(11) DEFAULT NULL,
  `bean_num` int(11) DEFAULT NULL,
  `pic` varchar(100) DEFAULT NULL,
  `status` int(11) DEFAULT NULL,
  `order_no` int(11) DEFAULT NULL,
  `remark1` varchar(100) DEFAULT NULL,
  `remark2` varchar(100) DEFAULT NULL,
  `remark3` varchar(100) DEFAULT NULL,
  `remark4` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- 表的结构 `mb_update_content`
--

CREATE TABLE IF NOT EXISTS `mb_update_content` (
`update_id` int(11) NOT NULL,
  `module_id` varchar(100) DEFAULT NULL,
  `discribtion` varchar(100) DEFAULT NULL,
  `version` varchar(100) DEFAULT NULL,
  `inner_version` varchar(100) DEFAULT NULL,
  `link` varchar(100) DEFAULT NULL,
  `update_content` text,
  `remark1` varchar(100) DEFAULT NULL,
  `remark2` varchar(100) DEFAULT NULL,
  `remark3` varchar(100) DEFAULT NULL,
  `remark4` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- 表的结构 `mb_user`
--

CREATE TABLE IF NOT EXISTS `mb_user` (
`backend_user_id` int(11) NOT NULL,
  `username` varchar(100) DEFAULT NULL,
  `password_hash` varchar(200) DEFAULT NULL,
  `password_reset_token` varchar(200) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `auth_key` varchar(100) DEFAULT NULL,
  `status` int(11) DEFAULT NULL,
  `created_at` int(11) DEFAULT NULL,
  `updated_at` int(11) DEFAULT NULL,
  `password` varchar(100) DEFAULT NULL,
  `pic` varchar(100) DEFAULT NULL,
  `remark1` varchar(100) DEFAULT NULL,
  `remark2` varchar(100) DEFAULT NULL,
  `remark3` varchar(100) DEFAULT NULL,
  `remark4` varchar(100) DEFAULT NULL
) ENGINE=InnoDB  DEFAULT CHARSET=utf8mb4 AUTO_INCREMENT=15 ;

--
-- 转存表中的数据 `mb_user`
--

INSERT INTO `mb_user` (`backend_user_id`, `username`, `password_hash`, `password_reset_token`, `email`, `auth_key`, `status`, `created_at`, `updated_at`, `password`, `pic`, `remark1`, `remark2`, `remark3`, `remark4`) VALUES
(1, 'admin', '$2y$13$DCUX4huYtAv/wcO6ssKzDOFV0V4PfR.bIlgJYGF2pbjCNjzJtFpTK', NULL, 'mibo@admin.com', NULL, 1, 1461136630, 1461149484, 'S57mq2WZ8lO2t+2WYvQLDmdPktWXQcjC', 'http://image.matewish.cn/wish_web/person-1.png', NULL, NULL, NULL, NULL),
(14, 'Cherish', '$2y$13$30Q00DBNLB/y3wA9QaN3heSWZtFEJ44cQAtoWuzZm8IUkmXnou1xW', '', '623006297@qq.com', '', 1, 1461120640, 1461120640, 'cgfLBcUVgIJQkjr7kF9FH0MQs0MeId4S', 'http://image.matewish.cn/back_user/05c629439cea09a999e1906b0bfa2d40f49d770a.jpg', '', '', '', '');

-- --------------------------------------------------------

--
-- 表的结构 `mb_user_daily_statistic`
--

CREATE TABLE IF NOT EXISTS `mb_user_daily_statistic` (
`daily_statistic_id` int(11) NOT NULL COMMENT '主键自增',
  `user_day_num` varchar(100) DEFAULT '0' COMMENT '用户统计',
  `data_time` date DEFAULT NULL COMMENT '统计日期',
  `remark1` varchar(100) DEFAULT NULL COMMENT '备用字段',
  `remark2` varchar(100) DEFAULT NULL COMMENT '备用字段',
  `remark3` varchar(100) DEFAULT NULL COMMENT '备用字段',
  `remark4` varchar(100) DEFAULT NULL COMMENT '备用字段'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='日用户数量统计表' AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- 表的结构 `mb_user_menu`
--

CREATE TABLE IF NOT EXISTS `mb_user_menu` (
`user_menu_id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `menu_id` int(11) DEFAULT NULL,
  `remark1` varchar(100) DEFAULT NULL,
  `remark2` varchar(100) DEFAULT NULL,
  `remark3` varchar(100) DEFAULT NULL,
  `remark4` varchar(100) DEFAULT NULL
) ENGINE=InnoDB  DEFAULT CHARSET=utf8mb4 AUTO_INCREMENT=62 ;

--
-- 转存表中的数据 `mb_user_menu`
--

INSERT INTO `mb_user_menu` (`user_menu_id`, `user_id`, `menu_id`, `remark1`, `remark2`, `remark3`, `remark4`) VALUES
(1, 1, 18, NULL, NULL, NULL, NULL),
(2, 1, 27, NULL, NULL, NULL, NULL),
(3, 14, 18, NULL, NULL, NULL, NULL),
(27, 1, 27, NULL, NULL, NULL, NULL),
(29, 1, 27, NULL, NULL, NULL, NULL),
(30, 14, 27, NULL, NULL, NULL, NULL),
(38, 1, 27, NULL, NULL, NULL, NULL),
(41, 14, 27, NULL, NULL, NULL, NULL),
(42, 14, 28, NULL, NULL, NULL, NULL),
(43, 14, 29, NULL, NULL, NULL, NULL),
(44, 14, 58, NULL, NULL, NULL, NULL),
(45, 14, 59, NULL, NULL, NULL, NULL),
(46, 14, 60, NULL, NULL, NULL, NULL),
(47, 14, 61, NULL, NULL, NULL, NULL),
(48, 14, 62, NULL, NULL, NULL, NULL),
(49, 14, 63, NULL, NULL, NULL, NULL),
(50, 14, 64, NULL, NULL, NULL, NULL),
(51, 14, 65, NULL, NULL, NULL, NULL),
(52, 1, 26, NULL, NULL, NULL, NULL),
(53, 1, 27, NULL, NULL, NULL, NULL),
(54, 1, 28, NULL, NULL, NULL, NULL),
(55, 1, 29, NULL, NULL, NULL, NULL),
(56, 1, 60, NULL, NULL, NULL, NULL),
(57, 1, 61, NULL, NULL, NULL, NULL),
(58, 1, 62, NULL, NULL, NULL, NULL),
(59, 1, 63, NULL, NULL, NULL, NULL),
(60, 1, 64, NULL, NULL, NULL, NULL),
(61, 1, 65, NULL, NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- 表的结构 `mb_user_month_statistic`
--

CREATE TABLE IF NOT EXISTS `mb_user_month_statistic` (
`month_statistic_id` int(11) NOT NULL COMMENT '主键自增',
  `user_month_num` varchar(100) DEFAULT NULL COMMENT '用户统计',
  `data_time` varchar(100) DEFAULT NULL COMMENT '统计日期',
  `remark1` varchar(100) DEFAULT NULL COMMENT '备用字段',
  `remark2` varchar(100) DEFAULT NULL COMMENT '备用字段',
  `remark3` varchar(100) DEFAULT NULL COMMENT '备用字段',
  `remark4` varchar(100) DEFAULT NULL COMMENT '备用字段'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='月用户数量统计表' AUTO_INCREMENT=1 ;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `mb_api_log`
--
ALTER TABLE `mb_api_log`
 ADD PRIMARY KEY (`api_id`);

--
-- Indexes for table `mb_attention`
--
ALTER TABLE `mb_attention`
 ADD PRIMARY KEY (`record_id`);

--
-- Indexes for table `mb_balance`
--
ALTER TABLE `mb_balance`
 ADD PRIMARY KEY (`balance_id`);

--
-- Indexes for table `mb_business_check`
--
ALTER TABLE `mb_business_check`
 ADD KEY `business_check_id` (`business_check_id`);

--
-- Indexes for table `mb_carousel`
--
ALTER TABLE `mb_carousel`
 ADD PRIMARY KEY (`carousel_id`);

--
-- Indexes for table `mb_chat_room`
--
ALTER TABLE `mb_chat_room`
 ADD PRIMARY KEY (`room_id`);

--
-- Indexes for table `mb_chat_room_member`
--
ALTER TABLE `mb_chat_room_member`
 ADD PRIMARY KEY (`record_id`);

--
-- Indexes for table `mb_city`
--
ALTER TABLE `mb_city`
 ADD PRIMARY KEY (`city_id`);

--
-- Indexes for table `mb_client`
--
ALTER TABLE `mb_client`
 ADD PRIMARY KEY (`client_id`);

--
-- Indexes for table `mb_client_active`
--
ALTER TABLE `mb_client_active`
 ADD PRIMARY KEY (`active_id`);

--
-- Indexes for table `mb_client_balance_log`
--
ALTER TABLE `mb_client_balance_log`
 ADD PRIMARY KEY (`log_id`);

--
-- Indexes for table `mb_client_cash_type_detail`
--
ALTER TABLE `mb_client_cash_type_detail`
 ADD PRIMARY KEY (`record_id`), ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `mb_client_other`
--
ALTER TABLE `mb_client_other`
 ADD PRIMARY KEY (`record_id`);

--
-- Indexes for table `mb_experience_log`
--
ALTER TABLE `mb_experience_log`
 ADD PRIMARY KEY (`log_id`), ADD KEY `source_type` (`source_type`,`reward_user_id`);

--
-- Indexes for table `mb_gift`
--
ALTER TABLE `mb_gift`
 ADD PRIMARY KEY (`gift_id`);

--
-- Indexes for table `mb_goods`
--
ALTER TABLE `mb_goods`
 ADD PRIMARY KEY (`goods_id`);

--
-- Indexes for table `mb_hot_words`
--
ALTER TABLE `mb_hot_words`
 ADD PRIMARY KEY (`hot_words_id`), ADD KEY `order_no` (`order_no`);

--
-- Indexes for table `mb_level`
--
ALTER TABLE `mb_level`
 ADD PRIMARY KEY (`level_id`), ADD KEY `order_no` (`order_no`);

--
-- Indexes for table `mb_living`
--
ALTER TABLE `mb_living`
 ADD PRIMARY KEY (`living_id`), ADD KEY `create_time` (`create_time`,`order_no`);

--
-- Indexes for table `mb_livingmaster_hot`
--
ALTER TABLE `mb_livingmaster_hot`
 ADD PRIMARY KEY (`livingmaster_id`,`hot_type`,`hot_id`), ADD UNIQUE KEY `hot_id` (`hot_id`);

--
-- Indexes for table `mb_living_goods`
--
ALTER TABLE `mb_living_goods`
 ADD PRIMARY KEY (`living_good_id`), ADD UNIQUE KEY `living_id` (`living_id`);

--
-- Indexes for table `mb_living_hot`
--
ALTER TABLE `mb_living_hot`
 ADD PRIMARY KEY (`hot_id`), ADD UNIQUE KEY `living_id` (`living_id`);

--
-- Indexes for table `mb_living_personnum`
--
ALTER TABLE `mb_living_personnum`
 ADD PRIMARY KEY (`living_personnum_id`), ADD UNIQUE KEY `living_id` (`living_id`);

--
-- Indexes for table `mb_living_tickets`
--
ALTER TABLE `mb_living_tickets`
 ADD PRIMARY KEY (`living_tickets_id`), ADD UNIQUE KEY `living_id` (`living_id`);

--
-- Indexes for table `mb_menu`
--
ALTER TABLE `mb_menu`
 ADD PRIMARY KEY (`menu_id`);

--
-- Indexes for table `mb_payment`
--
ALTER TABLE `mb_payment`
 ADD PRIMARY KEY (`payment_id`);

--
-- Indexes for table `mb_recharge`
--
ALTER TABLE `mb_recharge`
 ADD PRIMARY KEY (`recharge_id`);

--
-- Indexes for table `mb_report`
--
ALTER TABLE `mb_report`
 ADD PRIMARY KEY (`report_id`);

--
-- Indexes for table `mb_reward`
--
ALTER TABLE `mb_reward`
 ADD PRIMARY KEY (`reward_id`), ADD KEY `reward_user_id` (`reward_user_id`);

--
-- Indexes for table `mb_reward_log`
--
ALTER TABLE `mb_reward_log`
 ADD PRIMARY KEY (`log_id`), ADD KEY `reward_user_id` (`reward_user_id`);

--
-- Indexes for table `mb_set_user_check_no`
--
ALTER TABLE `mb_set_user_check_no`
 ADD PRIMARY KEY (`set_check_no_id`), ADD UNIQUE KEY `user_id` (`user_id`);

--
-- Indexes for table `mb_sum_reward_tickets`
--
ALTER TABLE `mb_sum_reward_tickets`
 ADD PRIMARY KEY (`reward_id`), ADD KEY `reward_user_id` (`reward_user_id`);

--
-- Indexes for table `mb_system_params`
--
ALTER TABLE `mb_system_params`
 ADD PRIMARY KEY (`params_id`), ADD UNIQUE KEY `code` (`code`);

--
-- Indexes for table `mb_ticket_to_bean`
--
ALTER TABLE `mb_ticket_to_bean`
 ADD PRIMARY KEY (`record_id`), ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `mb_ticket_to_cash`
--
ALTER TABLE `mb_ticket_to_cash`
 ADD PRIMARY KEY (`record_id`);

--
-- Indexes for table `mb_time_livingmaster_ticketcount`
--
ALTER TABLE `mb_time_livingmaster_ticketcount`
 ADD PRIMARY KEY (`livingmaster_id`,`hot_type`,`statistic_date`,`record_id`), ADD UNIQUE KEY `record_id` (`record_id`);

--
-- Indexes for table `mb_to_bean_goods`
--
ALTER TABLE `mb_to_bean_goods`
 ADD PRIMARY KEY (`record_id`), ADD KEY `order_no` (`order_no`);

--
-- Indexes for table `mb_update_content`
--
ALTER TABLE `mb_update_content`
 ADD PRIMARY KEY (`update_id`), ADD KEY `module_id` (`module_id`);

--
-- Indexes for table `mb_user`
--
ALTER TABLE `mb_user`
 ADD PRIMARY KEY (`backend_user_id`), ADD UNIQUE KEY `username` (`username`);

--
-- Indexes for table `mb_user_daily_statistic`
--
ALTER TABLE `mb_user_daily_statistic`
 ADD PRIMARY KEY (`daily_statistic_id`);

--
-- Indexes for table `mb_user_menu`
--
ALTER TABLE `mb_user_menu`
 ADD PRIMARY KEY (`user_menu_id`), ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `mb_user_month_statistic`
--
ALTER TABLE `mb_user_month_statistic`
 ADD PRIMARY KEY (`month_statistic_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `mb_api_log`
--
ALTER TABLE `mb_api_log`
MODIFY `api_id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `mb_attention`
--
ALTER TABLE `mb_attention`
MODIFY `record_id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `mb_balance`
--
ALTER TABLE `mb_balance`
MODIFY `balance_id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `mb_business_check`
--
ALTER TABLE `mb_business_check`
MODIFY `business_check_id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `mb_carousel`
--
ALTER TABLE `mb_carousel`
MODIFY `carousel_id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `mb_chat_room`
--
ALTER TABLE `mb_chat_room`
MODIFY `room_id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `mb_chat_room_member`
--
ALTER TABLE `mb_chat_room_member`
MODIFY `record_id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `mb_city`
--
ALTER TABLE `mb_city`
MODIFY `city_id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `mb_client`
--
ALTER TABLE `mb_client`
MODIFY `client_id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `mb_client_active`
--
ALTER TABLE `mb_client_active`
MODIFY `active_id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `mb_client_balance_log`
--
ALTER TABLE `mb_client_balance_log`
MODIFY `log_id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `mb_client_cash_type_detail`
--
ALTER TABLE `mb_client_cash_type_detail`
MODIFY `record_id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `mb_client_other`
--
ALTER TABLE `mb_client_other`
MODIFY `record_id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `mb_experience_log`
--
ALTER TABLE `mb_experience_log`
MODIFY `log_id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `mb_gift`
--
ALTER TABLE `mb_gift`
MODIFY `gift_id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `mb_goods`
--
ALTER TABLE `mb_goods`
MODIFY `goods_id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `mb_level`
--
ALTER TABLE `mb_level`
MODIFY `level_id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `mb_living`
--
ALTER TABLE `mb_living`
MODIFY `living_id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `mb_livingmaster_hot`
--
ALTER TABLE `mb_livingmaster_hot`
MODIFY `hot_id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `mb_living_goods`
--
ALTER TABLE `mb_living_goods`
MODIFY `living_good_id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `mb_living_hot`
--
ALTER TABLE `mb_living_hot`
MODIFY `hot_id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `mb_living_personnum`
--
ALTER TABLE `mb_living_personnum`
MODIFY `living_personnum_id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `mb_living_tickets`
--
ALTER TABLE `mb_living_tickets`
MODIFY `living_tickets_id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `mb_menu`
--
ALTER TABLE `mb_menu`
MODIFY `menu_id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=90;
--
-- AUTO_INCREMENT for table `mb_payment`
--
ALTER TABLE `mb_payment`
MODIFY `payment_id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `mb_recharge`
--
ALTER TABLE `mb_recharge`
MODIFY `recharge_id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `mb_report`
--
ALTER TABLE `mb_report`
MODIFY `report_id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `mb_reward`
--
ALTER TABLE `mb_reward`
MODIFY `reward_id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `mb_reward_log`
--
ALTER TABLE `mb_reward_log`
MODIFY `log_id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `mb_set_user_check_no`
--
ALTER TABLE `mb_set_user_check_no`
MODIFY `set_check_no_id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=2;
--
-- AUTO_INCREMENT for table `mb_sum_reward_tickets`
--
ALTER TABLE `mb_sum_reward_tickets`
MODIFY `reward_id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `mb_system_params`
--
ALTER TABLE `mb_system_params`
MODIFY `params_id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=38;
--
-- AUTO_INCREMENT for table `mb_ticket_to_bean`
--
ALTER TABLE `mb_ticket_to_bean`
MODIFY `record_id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `mb_ticket_to_cash`
--
ALTER TABLE `mb_ticket_to_cash`
MODIFY `record_id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `mb_time_livingmaster_ticketcount`
--
ALTER TABLE `mb_time_livingmaster_ticketcount`
MODIFY `record_id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `mb_to_bean_goods`
--
ALTER TABLE `mb_to_bean_goods`
MODIFY `record_id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `mb_update_content`
--
ALTER TABLE `mb_update_content`
MODIFY `update_id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `mb_user`
--
ALTER TABLE `mb_user`
MODIFY `backend_user_id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=15;
--
-- AUTO_INCREMENT for table `mb_user_daily_statistic`
--
ALTER TABLE `mb_user_daily_statistic`
MODIFY `daily_statistic_id` int(11) NOT NULL AUTO_INCREMENT COMMENT '主键自增';
--
-- AUTO_INCREMENT for table `mb_user_menu`
--
ALTER TABLE `mb_user_menu`
MODIFY `user_menu_id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=62;
--
-- AUTO_INCREMENT for table `mb_user_month_statistic`
--
ALTER TABLE `mb_user_month_statistic`
MODIFY `month_statistic_id` int(11) NOT NULL AUTO_INCREMENT COMMENT '主键自增';


--
-- 表的结构 `mb_gift_score`
--

CREATE TABLE IF NOT EXISTS `mb_gift_score` (
`record_id` int(11) NOT NULL COMMENT '主键自增',
  `gift_id` int(11) DEFAULT NULL COMMENT '礼物id',
  `score` int(11) DEFAULT NULL COMMENT '活动积分',
  `remark1` varchar(100) DEFAULT NULL COMMENT '备用字段1',
  `remark2` varchar(100) DEFAULT NULL COMMENT '备用字段2',
  `remark3` varchar(100) DEFAULT NULL COMMENT '备用字段3',
  `remark4` varchar(100) DEFAULT NULL COMMENT '备用字段4'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='礼物积分表' AUTO_INCREMENT=1 ;

ALTER TABLE `mb_gift_score`
 ADD PRIMARY KEY (`record_id`), ADD KEY `gift_id` (`gift_id`);


ALTER TABLE `mb_gift_score`
MODIFY `record_id` int(11) NOT NULL AUTO_INCREMENT COMMENT '主键自增';