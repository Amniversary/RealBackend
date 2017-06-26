/*
Navicat MySQL Data Transfer

Source Server         : 直播最终服务器
Source Server Version : 50616
Source Host           : rm-bp1q1q12hlx580jok.mysql.rds.aliyuncs.com:3306
Source Database       : mblivedb

Target Server Type    : MYSQL
Target Server Version : 50616
File Encoding         : 65001

Date: 2016-06-27 16:35:41
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for `mb_payment`
-- ----------------------------
DROP TABLE IF EXISTS `mb_payment`;
CREATE TABLE `mb_payment` (
  `payment_id` int(11) NOT NULL AUTO_INCREMENT,
  `code` int(11) DEFAULT NULL,
  `status` int(11) DEFAULT NULL,
  `title` varchar(100) DEFAULT NULL,
  `order_no` varchar(10) DEFAULT NULL,
  `icon` varchar(200) DEFAULT NULL,
  `remark1` varchar(100) DEFAULT NULL,
  `remark2` varchar(100) DEFAULT NULL,
  `remark3` varchar(100) DEFAULT NULL,
  `remark4` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`payment_id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4;

-- ----------------------------
-- Records of mb_payment
-- ----------------------------
INSERT INTO `mb_payment` VALUES ('1', '1', '1', '账户余额支付', '005', 'http://image.matewish.cn/pay_icon/balance_pay.png', '1', 'http://image.matewish.cn/pay_icon/balance_pay.png', '', '');
INSERT INTO `mb_payment` VALUES ('3', '3', '1', '支付宝支付', '001', 'http://image.matewish.cn/pay_icon/ali_wallet.png', '1', 'http://image.matewish.cn/pay_icon/ali_wallet.png', '', '');
INSERT INTO `mb_payment` VALUES ('4', '4', '1', '微信支付', '002', 'http://image.matewish.cn/pay_icon/wechat_pay.png', '1', 'http://image.matewish.cn/pay_icon/wechat_pay.png', '', '');
INSERT INTO `mb_payment` VALUES ('5', '5', '1', '连连支付', '003', 'http://image.matewish.cn/pay_icon/lian_pay.png', '1', 'http://image.matewish.cn/pay_icon/lian_pay.png', '', '');
