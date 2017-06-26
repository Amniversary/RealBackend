/*
Navicat MySQL Data Transfer

Source Server         : 114.215.189.7
Source Server Version : 50541
Source Host           : 114.215.189.7:3306
Source Database       : meibo

Target Server Type    : MYSQL
Target Server Version : 50541
File Encoding         : 65001

Date: 2016-06-14 17:50:38
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for `mb_system_params`
-- ----------------------------
DROP TABLE IF EXISTS `mb_system_params`;
CREATE TABLE `mb_system_params` (
  `params_id` int(11) NOT NULL AUTO_INCREMENT,
  `group_id` int(11) DEFAULT NULL COMMENT '分组id',
  `code` varchar(100) DEFAULT NULL,
  `title` varchar(100) DEFAULT NULL,
  `description` varchar(100) DEFAULT NULL,
  `value1` varchar(100) DEFAULT NULL,
  `value2` varchar(100) DEFAULT NULL,
  `value3` varchar(100) DEFAULT NULL,
  `remark1` varchar(100) DEFAULT NULL,
  `remark2` varchar(100) DEFAULT NULL,
  `remark3` varchar(100) DEFAULT NULL,
  `remark4` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`params_id`),
  UNIQUE KEY `code` (`code`)
) ENGINE=InnoDB AUTO_INCREMENT=49 DEFAULT CHARSET=utf8mb4;

-- ----------------------------
-- Records of mb_system_params
-- ----------------------------
INSERT INTO `mb_system_params` VALUES ('4', '1', 'system_customer_call', '客服电话', '客服电话', '0571-28819117', '0571-28819117', null, null, null, null, null);
INSERT INTO `mb_system_params` VALUES ('5', '2', 'system_share_weixin_pic', '微信共享图片', '微信共享图片', 'http://image.matewish.cn/system/icon.png', 'http://image.matewish.cn/system/icon.png', null, null, null, null, null);
INSERT INTO `mb_system_params` VALUES ('19', '1', 'system_nearby_distance', '', '附近的距离，单位米', '附近的距离', '2000', '', '', '', '', '');
INSERT INTO `mb_system_params` VALUES ('28', '2', 'system_share_wish_title', '愿望分享标题', '愿望分享标题', '新年新气象，和我一起来美愿实现愿望吧！', '新年新气象，和我一起来美愿实现愿望吧！', null, null, null, null, null);
INSERT INTO `mb_system_params` VALUES ('29', '2', 'system_share_wish_content', '愿望分享内容', '愿望分享内容', '下载美愿，快来支持我的愿望，您可获得3倍奖金，同时为我赢取1倍奖金！', '下载美愿，快来支持我的愿望，您可获得3倍奖金，同时为我赢取1倍奖金！', null, null, null, null, null);
INSERT INTO `mb_system_params` VALUES ('30', '2', 'system_share_invite_title', '邀请朋友标题', '邀请朋友标题', '美愿，一个帮你实现愿望的app！', '美愿，一个帮你实现愿望的app！', null, null, null, null, null);
INSERT INTO `mb_system_params` VALUES ('31', '2', 'system_share_invite_content', '邀请朋友内容', '邀请朋友内容', '美愿是一个共享社交金融平台，通过共享愿望的方式，实现每一个愿望。', '美愿是一个共享社交金融平台，通过共享愿望的方式，实现每一个愿望。', null, null, null, null, null);
INSERT INTO `mb_system_params` VALUES ('37', '1', 'system_pay', '支付方式图标', '支付方式图标', 'http://image.matewish.cn/pay_icon/pay.png', 'http://image.matewish.cn/pay_icon/pay.png', null, null, null, null, null);
INSERT INTO `mb_system_params` VALUES ('38', '1', 'system_device_message_no', '用户设备短信数量限制', '用户设备短信数量限制', '999', '999', null, null, null, null, null);
INSERT INTO `mb_system_params` VALUES ('39', '1', 'system_device_register_no', '用户设备注册数量限制', '用户设备注册数量限制', '99', '99', null, null, null, null, null);
INSERT INTO `mb_system_params` VALUES ('40', '1', 'system_phone_message_no', '用户手机短信数量限制', '用户手机短信数量限制', '999', '999', null, null, null, null, null);
INSERT INTO `mb_system_params` VALUES ('41', '6', 'living_master_min_experience', '主播每分钟获取的经验值', '主播每分钟获取的经验值', '30', '30', null, null, null, null, null);
INSERT INTO `mb_system_params` VALUES ('42', '6', 'living_visitor_min_experience', '观看直播人员每分钟经验值', '观看直播人员每分钟经验值', '15', '15', null, null, null, null, null);
INSERT INTO `mb_system_params` VALUES ('43', '6', 'living_bean_to_experience', '直播送礼物中的豆与经验值转化比例', '直播送礼物中的豆与经验值转化比例，1豆对应的经验值', '10', '10', null, null, null, null, null);
INSERT INTO `mb_system_params` VALUES ('44', '6', 'bean_num_for_danmaku', '弹幕需要的豆数', '弹幕需要的豆数', '1', '1', null, null, null, null, null);
INSERT INTO `mb_system_params` VALUES ('45', '7', 'heart_dis_time', 'app端心跳时间间隔', 'app端心跳时间间隔', '15', '15', '15', null, null, null, null);
INSERT INTO `mb_system_params` VALUES ('46', '7', 'unable_heart_dis_time', '与上次心跳时间差大于多少的心跳视为无效', '与上次心跳时间差大于多少的心跳视为无效，单位秒', '25', '25', null, null, null, null, null);
INSERT INTO `mb_system_params` VALUES ('47', '1', 'system_recharge_info', '微信充值关注信息', '微信充值关注信息说明', '微信充值关注信息说明', ' 微信充值关注信息说明', null, null, null, null, null);
INSERT INTO `mb_system_params` VALUES ('48', '1', 'mb_login_types', '登录方式设置', '登录方式设置，多位字符串，每一位标识一种登录方式，分别：微博、微信、QQ，默认值101', '101', '101', null, null, null, null, null);
