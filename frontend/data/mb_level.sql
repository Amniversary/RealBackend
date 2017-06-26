/*
Navicat MySQL Data Transfer

Source Server         : 本地
Source Server Version : 50620
Source Host           : localhost:3306
Source Database       : meibo

Target Server Type    : MYSQL
Target Server Version : 50620
File Encoding         : 65001

Date: 2016-04-22 17:41:00
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for `mb_level`
-- ----------------------------
DROP TABLE IF EXISTS `mb_level`;
CREATE TABLE `mb_level` (
  `level_id` int(11) NOT NULL AUTO_INCREMENT,
  `level_name` varchar(100) DEFAULT NULL,
  `experience` int(11) DEFAULT NULL,
  `level_pic` varchar(100) DEFAULT NULL,
  `order_no` int(11) DEFAULT NULL,
  `remark1` varchar(100) DEFAULT NULL,
  `remark2` varchar(100) DEFAULT NULL,
  `remark3` varchar(100) DEFAULT NULL,
  `remark4` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`level_id`),
  KEY `order_no` (`order_no`)
) ENGINE=InnoDB AUTO_INCREMENT=201 DEFAULT CHARSET=utf8mb4;

-- ----------------------------
-- Records of mb_level
-- ----------------------------
INSERT INTO `mb_level` VALUES ('1', '1', '0', 'http://image.matewish.cn/pay_icon/grade1_star.png', null, null, null, null, null);
INSERT INTO `mb_level` VALUES ('2', '2', '600', 'http://image.matewish.cn/pay_icon/grade1_star.png', null, null, null, null, null);
INSERT INTO `mb_level` VALUES ('3', '3', '1201', 'http://image.matewish.cn/pay_icon/grade1_star.png', null, null, null, null, null);
INSERT INTO `mb_level` VALUES ('4', '4', '1809', 'http://image.matewish.cn/pay_icon/grade1_star.png', null, null, null, null, null);
INSERT INTO `mb_level` VALUES ('5', '5', '2436', 'http://image.matewish.cn/pay_icon/grade1_star.png', null, null, null, null, null);
INSERT INTO `mb_level` VALUES ('6', '6', '3100', 'http://image.matewish.cn/pay_icon/grade1_star.png', null, null, null, null, null);
INSERT INTO `mb_level` VALUES ('7', '7', '3825', 'http://image.matewish.cn/pay_icon/grade1_star.png', null, null, null, null, null);
INSERT INTO `mb_level` VALUES ('8', '8', '4641', 'http://image.matewish.cn/pay_icon/grade1_star.png', null, null, null, null, null);
INSERT INTO `mb_level` VALUES ('9', '9', '5584', 'http://image.matewish.cn/pay_icon/grade1_star.png', null, null, null, null, null);
INSERT INTO `mb_level` VALUES ('10', '10', '6696', 'http://image.matewish.cn/pay_icon/grade1_star.png', null, null, null, null, null);
INSERT INTO `mb_level` VALUES ('11', '11', '8025', 'http://image.matewish.cn/pay_icon/grade1_star.png', null, null, null, null, null);
INSERT INTO `mb_level` VALUES ('12', '12', '9625', 'http://image.matewish.cn/pay_icon/grade1_star.png', null, null, null, null, null);
INSERT INTO `mb_level` VALUES ('13', '13', '11556', 'http://image.matewish.cn/pay_icon/grade2_moon.png', null, null, null, null, null);
INSERT INTO `mb_level` VALUES ('14', '14', '13884', 'http://image.matewish.cn/pay_icon/grade2_moon.png', null, null, null, null, null);
INSERT INTO `mb_level` VALUES ('15', '15', '16681', 'http://image.matewish.cn/pay_icon/grade2_moon.png', null, null, null, null, null);
INSERT INTO `mb_level` VALUES ('16', '16', '20025', 'http://image.matewish.cn/pay_icon/grade2_moon.png', null, null, null, null, null);
INSERT INTO `mb_level` VALUES ('17', '17', '24000', 'http://image.matewish.cn/pay_icon/grade2_moon.png', null, null, null, null, null);
INSERT INTO `mb_level` VALUES ('18', '18', '28696', 'http://image.matewish.cn/pay_icon/grade2_moon.png', null, null, null, null, null);
INSERT INTO `mb_level` VALUES ('19', '19', '34209', 'http://image.matewish.cn/pay_icon/grade2_moon.png', null, null, null, null, null);
INSERT INTO `mb_level` VALUES ('20', '20', '40641', 'http://image.matewish.cn/pay_icon/grade3_sun.png', null, null, null, null, null);
INSERT INTO `mb_level` VALUES ('21', '21', '48100', 'http://image.matewish.cn/pay_icon/grade3_sun.png', null, null, null, null, null);
INSERT INTO `mb_level` VALUES ('22', '22', '56700', 'http://image.matewish.cn/pay_icon/grade3_sun.png', null, null, null, null, null);
INSERT INTO `mb_level` VALUES ('23', '23', '66561', 'http://image.matewish.cn/pay_icon/grade3_sun.png', null, null, null, null, null);
INSERT INTO `mb_level` VALUES ('24', '24', '77809', 'http://image.matewish.cn/pay_icon/grade3_sun.png', null, null, null, null, null);
INSERT INTO `mb_level` VALUES ('25', '25', '90576', 'http://image.matewish.cn/pay_icon/grade4_diamond.png', null, null, null, null, null);
INSERT INTO `mb_level` VALUES ('26', '26', '105000', 'http://image.matewish.cn/pay_icon/grade4_diamond.png', null, null, null, null, null);
INSERT INTO `mb_level` VALUES ('27', '27', '121225', 'http://image.matewish.cn/pay_icon/grade4_diamond.png', null, null, null, null, null);
INSERT INTO `mb_level` VALUES ('28', '28', '139401', 'http://image.matewish.cn/pay_icon/grade4_diamond.png', null, null, null, null, null);
INSERT INTO `mb_level` VALUES ('29', '29', '159684', 'http://image.matewish.cn/pay_icon/grade4_diamond.png', null, null, null, null, null);
INSERT INTO `mb_level` VALUES ('30', '30', '182236', 'http://image.matewish.cn/pay_icon/grade5_crown1.png', null, null, null, null, null);
INSERT INTO `mb_level` VALUES ('31', '31', '207225', 'http://image.matewish.cn/pay_icon/grade5_crown1.png', null, null, null, null, null);
INSERT INTO `mb_level` VALUES ('32', '32', '234825', 'http://image.matewish.cn/pay_icon/grade5_crown1.png', null, null, null, null, null);
INSERT INTO `mb_level` VALUES ('33', '33', '265216', 'http://image.matewish.cn/pay_icon/grade5_crown1.png', null, null, null, null, null);
INSERT INTO `mb_level` VALUES ('34', '34', '298584', 'http://image.matewish.cn/pay_icon/grade5_crown1.png', null, null, null, null, null);
INSERT INTO `mb_level` VALUES ('35', '35', '335121', 'http://image.matewish.cn/pay_icon/grade5_crown1.png', null, null, null, null, null);
INSERT INTO `mb_level` VALUES ('36', '36', '375025', 'http://image.matewish.cn/pay_icon/grade5_crown1.png', null, null, null, null, null);
INSERT INTO `mb_level` VALUES ('37', '37', '418500', 'http://image.matewish.cn/pay_icon/grade5_crown1.png', null, null, null, null, null);
INSERT INTO `mb_level` VALUES ('38', '38', '465756', 'http://image.matewish.cn/pay_icon/grade5_crown1.png', null, null, null, null, null);
INSERT INTO `mb_level` VALUES ('39', '39', '517009', 'http://image.matewish.cn/pay_icon/grade5_crown1.png', null, null, null, null, null);
INSERT INTO `mb_level` VALUES ('40', '40', '572481', 'http://image.matewish.cn/pay_icon/grade6_crown2.png', null, null, null, null, null);
INSERT INTO `mb_level` VALUES ('41', '41', '632400', 'http://image.matewish.cn/pay_icon/grade6_crown2.png', null, null, null, null, null);
INSERT INTO `mb_level` VALUES ('42', '42', '697000', 'http://image.matewish.cn/pay_icon/grade6_crown2.png', null, null, null, null, null);
INSERT INTO `mb_level` VALUES ('43', '43', '766521', 'http://image.matewish.cn/pay_icon/grade6_crown2.png', null, null, null, null, null);
INSERT INTO `mb_level` VALUES ('44', '44', '841209', 'http://image.matewish.cn/pay_icon/grade6_crown2.png', null, null, null, null, null);
INSERT INTO `mb_level` VALUES ('45', '45', '921316', 'http://image.matewish.cn/pay_icon/grade6_crown2.png', null, null, null, null, null);
INSERT INTO `mb_level` VALUES ('46', '46', '1007100', 'http://image.matewish.cn/pay_icon/grade6_crown2.png', null, null, null, null, null);
INSERT INTO `mb_level` VALUES ('47', '47', '1098825', 'http://image.matewish.cn/pay_icon/grade6_crown2.png', null, null, null, null, null);
INSERT INTO `mb_level` VALUES ('48', '48', '1196761', 'http://image.matewish.cn/pay_icon/grade6_crown2.png', null, null, null, null, null);
INSERT INTO `mb_level` VALUES ('49', '49', '1301184', 'http://image.matewish.cn/pay_icon/grade6_crown2.png', null, null, null, null, null);
INSERT INTO `mb_level` VALUES ('50', '50', '1412376', 'http://image.matewish.cn/pay_icon/grade4_crown3.png', null, null, null, null, null);
INSERT INTO `mb_level` VALUES ('51', '51', '1530625', 'http://image.matewish.cn/pay_icon/grade4_crown3.png', null, null, null, null, null);
INSERT INTO `mb_level` VALUES ('52', '52', '1656225', 'http://image.matewish.cn/pay_icon/grade4_crown3.png', null, null, null, null, null);
INSERT INTO `mb_level` VALUES ('53', '53', '1789476', 'http://image.matewish.cn/pay_icon/grade4_crown3.png', null, null, null, null, null);
INSERT INTO `mb_level` VALUES ('54', '54', '1930684', 'http://image.matewish.cn/pay_icon/grade4_crown3.png', null, null, null, null, null);
INSERT INTO `mb_level` VALUES ('55', '55', '2080161', 'http://image.matewish.cn/pay_icon/grade4_crown3.png', null, null, null, null, null);
INSERT INTO `mb_level` VALUES ('56', '56', '2238225', 'http://image.matewish.cn/pay_icon/grade4_crown3.png', null, null, null, null, null);
INSERT INTO `mb_level` VALUES ('57', '57', '2405200', 'http://image.matewish.cn/pay_icon/grade4_crown3.png', null, null, null, null, null);
INSERT INTO `mb_level` VALUES ('58', '58', '2581416', 'http://image.matewish.cn/pay_icon/grade4_crown3.png', null, null, null, null, null);
INSERT INTO `mb_level` VALUES ('59', '59', '2767209', 'http://image.matewish.cn/pay_icon/grade4_crown3.png', null, null, null, null, null);
INSERT INTO `mb_level` VALUES ('60', '60', '2962921', 'http://image.matewish.cn/pay_icon/grade4_crown3.png', null, null, null, null, null);
INSERT INTO `mb_level` VALUES ('61', '61', '3168900', 'http://image.matewish.cn/pay_icon/grade4_crown3.png', null, null, null, null, null);
INSERT INTO `mb_level` VALUES ('62', '62', '3385500', 'http://image.matewish.cn/pay_icon/grade4_crown3.png', null, null, null, null, null);
INSERT INTO `mb_level` VALUES ('63', '63', '3613081', 'http://image.matewish.cn/pay_icon/grade4_crown3.png', null, null, null, null, null);
INSERT INTO `mb_level` VALUES ('64', '64', '3852009', 'http://image.matewish.cn/pay_icon/grade4_crown3.png', null, null, null, null, null);
INSERT INTO `mb_level` VALUES ('65', '65', '4102656', 'http://image.matewish.cn/pay_icon/grade4_crown3.png', null, null, null, null, null);
INSERT INTO `mb_level` VALUES ('66', '66', '4365400', 'http://image.matewish.cn/pay_icon/grade4_crown3.png', null, null, null, null, null);
INSERT INTO `mb_level` VALUES ('67', '67', '4640625', 'http://image.matewish.cn/pay_icon/grade4_crown3.png', null, null, null, null, null);
INSERT INTO `mb_level` VALUES ('68', '68', '4928721', 'http://image.matewish.cn/pay_icon/grade4_crown3.png', null, null, null, null, null);
INSERT INTO `mb_level` VALUES ('69', '69', '5230084', 'http://image.matewish.cn/pay_icon/grade4_crown3.png', null, null, null, null, null);
INSERT INTO `mb_level` VALUES ('70', '70', '5545116', 'http://image.matewish.cn/pay_icon/grade4_crown3.png', null, null, null, null, null);
INSERT INTO `mb_level` VALUES ('71', '71', '5874225', 'http://image.matewish.cn/pay_icon/grade4_crown3.png', null, null, null, null, null);
INSERT INTO `mb_level` VALUES ('72', '72', '6217825', 'http://image.matewish.cn/pay_icon/grade4_crown3.png', null, null, null, null, null);
INSERT INTO `mb_level` VALUES ('73', '73', '6576336', 'http://image.matewish.cn/pay_icon/grade4_crown3.png', null, null, null, null, null);
INSERT INTO `mb_level` VALUES ('74', '74', '6950184', 'http://image.matewish.cn/pay_icon/grade4_crown3.png', null, null, null, null, null);
INSERT INTO `mb_level` VALUES ('75', '75', '7339801', 'http://image.matewish.cn/pay_icon/grade4_crown3.png', null, null, null, null, null);
INSERT INTO `mb_level` VALUES ('76', '76', '7745625', 'http://image.matewish.cn/pay_icon/grade4_crown3.png', null, null, null, null, null);
INSERT INTO `mb_level` VALUES ('77', '77', '8168100', 'http://image.matewish.cn/pay_icon/grade4_crown3.png', null, null, null, null, null);
INSERT INTO `mb_level` VALUES ('78', '78', '8607676', 'http://image.matewish.cn/pay_icon/grade4_crown3.png', null, null, null, null, null);
INSERT INTO `mb_level` VALUES ('79', '79', '9064809', 'http://image.matewish.cn/pay_icon/grade4_crown3.png', null, null, null, null, null);
INSERT INTO `mb_level` VALUES ('80', '80', '9539961', 'http://image.matewish.cn/pay_icon/grade8_crown4.png', null, null, null, null, null);
INSERT INTO `mb_level` VALUES ('81', '81', '10033600', 'http://image.matewish.cn/pay_icon/grade8_crown4.png', null, null, null, null, null);
INSERT INTO `mb_level` VALUES ('82', '82', '10546200', 'http://image.matewish.cn/pay_icon/grade8_crown4.png', null, null, null, null, null);
INSERT INTO `mb_level` VALUES ('83', '83', '11078241', 'http://image.matewish.cn/pay_icon/grade8_crown4.png', null, null, null, null, null);
INSERT INTO `mb_level` VALUES ('84', '84', '11630209', 'http://image.matewish.cn/pay_icon/grade8_crown4.png', null, null, null, null, null);
INSERT INTO `mb_level` VALUES ('85', '85', '12202596', 'http://image.matewish.cn/pay_icon/grade8_crown4.png', null, null, null, null, null);
INSERT INTO `mb_level` VALUES ('86', '86', '12795900', 'http://image.matewish.cn/pay_icon/grade8_crown4.png', null, null, null, null, null);
INSERT INTO `mb_level` VALUES ('87', '87', '13410625', 'http://image.matewish.cn/pay_icon/grade8_crown4.png', null, null, null, null, null);
INSERT INTO `mb_level` VALUES ('88', '88', '14047281', 'http://image.matewish.cn/pay_icon/grade8_crown4.png', null, null, null, null, null);
INSERT INTO `mb_level` VALUES ('89', '89', '14706384', 'http://image.matewish.cn/pay_icon/grade8_crown4.png', null, null, null, null, null);
INSERT INTO `mb_level` VALUES ('90', '90', '15388456', 'http://image.matewish.cn/pay_icon/grade8_crown4.png', null, null, null, null, null);
INSERT INTO `mb_level` VALUES ('91', '91', '16094025', 'http://image.matewish.cn/pay_icon/grade8_crown4.png', null, null, null, null, null);
INSERT INTO `mb_level` VALUES ('92', '92', '16823625', 'http://image.matewish.cn/pay_icon/grade8_crown4.png', null, null, null, null, null);
INSERT INTO `mb_level` VALUES ('93', '93', '17577796', 'http://image.matewish.cn/pay_icon/grade8_crown4.png', null, null, null, null, null);
INSERT INTO `mb_level` VALUES ('94', '94', '18357084', 'http://image.matewish.cn/pay_icon/grade8_crown4.png', null, null, null, null, null);
INSERT INTO `mb_level` VALUES ('95', '95', '19162041', 'http://image.matewish.cn/pay_icon/grade8_crown4.png', null, null, null, null, null);
INSERT INTO `mb_level` VALUES ('96', '96', '19993225', 'http://image.matewish.cn/pay_icon/grade8_crown4.png', null, null, null, null, null);
INSERT INTO `mb_level` VALUES ('97', '97', '20851200', 'http://image.matewish.cn/pay_icon/grade8_crown4.png', null, null, null, null, null);
INSERT INTO `mb_level` VALUES ('98', '98', '21736536', 'http://image.matewish.cn/pay_icon/grade8_crown4.png', null, null, null, null, null);
INSERT INTO `mb_level` VALUES ('99', '99', '22649809', 'http://image.matewish.cn/pay_icon/grade8_crown4.png', null, null, null, null, null);
INSERT INTO `mb_level` VALUES ('100', '100', '23591601', 'http://image.matewish.cn/pay_icon/grade8_crown4.png', null, null, null, null, null);
INSERT INTO `mb_level` VALUES ('101', '101', '24562500', 'http://image.matewish.cn/pay_icon/grade8_crown4.png', null, null, null, null, null);
INSERT INTO `mb_level` VALUES ('102', '102', '25563100', 'http://image.matewish.cn/pay_icon/grade8_crown4.png', null, null, null, null, null);
INSERT INTO `mb_level` VALUES ('103', '103', '26594001', 'http://image.matewish.cn/pay_icon/grade8_crown4.png', null, null, null, null, null);
INSERT INTO `mb_level` VALUES ('104', '104', '27655809', 'http://image.matewish.cn/pay_icon/grade8_crown4.png', null, null, null, null, null);
INSERT INTO `mb_level` VALUES ('105', '105', '28749136', 'http://image.matewish.cn/pay_icon/grade8_crown4.png', null, null, null, null, null);
INSERT INTO `mb_level` VALUES ('106', '106', '29874600', 'http://image.matewish.cn/pay_icon/grade8_crown4.png', null, null, null, null, null);
INSERT INTO `mb_level` VALUES ('107', '107', '31032825', 'http://image.matewish.cn/pay_icon/grade8_crown4.png', null, null, null, null, null);
INSERT INTO `mb_level` VALUES ('108', '108', '32224441', 'http://image.matewish.cn/pay_icon/grade8_crown4.png', null, null, null, null, null);
INSERT INTO `mb_level` VALUES ('109', '109', '33450084', 'http://image.matewish.cn/pay_icon/grade8_crown4.png', null, null, null, null, null);
INSERT INTO `mb_level` VALUES ('110', '110', '34710396', 'http://image.matewish.cn/pay_icon/grade8_crown4.png', null, null, null, null, null);
INSERT INTO `mb_level` VALUES ('111', '111', '36006025', 'http://image.matewish.cn/pay_icon/grade8_crown4.png', null, null, null, null, null);
INSERT INTO `mb_level` VALUES ('112', '112', '37337625', 'http://image.matewish.cn/pay_icon/grade8_crown4.png', null, null, null, null, null);
INSERT INTO `mb_level` VALUES ('113', '113', '38705856', 'http://image.matewish.cn/pay_icon/grade8_crown4.png', null, null, null, null, null);
INSERT INTO `mb_level` VALUES ('114', '114', '40111384', 'http://image.matewish.cn/pay_icon/grade8_crown4.png', null, null, null, null, null);
INSERT INTO `mb_level` VALUES ('115', '115', '41554881', 'http://image.matewish.cn/pay_icon/grade8_crown4.png', null, null, null, null, null);
INSERT INTO `mb_level` VALUES ('116', '116', '43037025', 'http://image.matewish.cn/pay_icon/grade8_crown4.png', null, null, null, null, null);
INSERT INTO `mb_level` VALUES ('117', '117', '44558500', 'http://image.matewish.cn/pay_icon/grade8_crown4.png', null, null, null, null, null);
INSERT INTO `mb_level` VALUES ('118', '118', '46119996', 'http://image.matewish.cn/pay_icon/grade8_crown4.png', null, null, null, null, null);
INSERT INTO `mb_level` VALUES ('119', '119', '47722209', 'http://image.matewish.cn/pay_icon/grade8_crown4.png', null, null, null, null, null);
INSERT INTO `mb_level` VALUES ('120', '120', '49365841', 'http://image.matewish.cn/pay_icon/grade8_crown4.png', null, null, null, null, null);
INSERT INTO `mb_level` VALUES ('121', '121', '51051600', 'http://image.matewish.cn/pay_icon/grade8_crown4.png', null, null, null, null, null);
INSERT INTO `mb_level` VALUES ('122', '122', '52780200', 'http://image.matewish.cn/pay_icon/grade8_crown4.png', null, null, null, null, null);
INSERT INTO `mb_level` VALUES ('123', '123', '54552361', 'http://image.matewish.cn/pay_icon/grade8_crown4.png', null, null, null, null, null);
INSERT INTO `mb_level` VALUES ('124', '124', '56368809', 'http://image.matewish.cn/pay_icon/grade8_crown4.png', null, null, null, null, null);
INSERT INTO `mb_level` VALUES ('125', '125', '58230276', 'http://image.matewish.cn/pay_icon/grade8_crown4.png', null, null, null, null, null);
INSERT INTO `mb_level` VALUES ('126', '126', '60137500', 'http://image.matewish.cn/pay_icon/grade8_crown4.png', null, null, null, null, null);
INSERT INTO `mb_level` VALUES ('127', '127', '62091225', 'http://image.matewish.cn/pay_icon/grade8_crown4.png', null, null, null, null, null);
INSERT INTO `mb_level` VALUES ('128', '128', '64092201', 'http://image.matewish.cn/pay_icon/grade8_crown4.png', null, null, null, null, null);
INSERT INTO `mb_level` VALUES ('129', '129', '66141184', 'http://image.matewish.cn/pay_icon/grade8_crown4.png', null, null, null, null, null);
INSERT INTO `mb_level` VALUES ('130', '130', '68238936', 'http://image.matewish.cn/pay_icon/grade8_crown4.png', null, null, null, null, null);
INSERT INTO `mb_level` VALUES ('131', '131', '70386225', 'http://image.matewish.cn/pay_icon/grade8_crown4.png', null, null, null, null, null);
INSERT INTO `mb_level` VALUES ('132', '132', '72583825', 'http://image.matewish.cn/pay_icon/grade8_crown4.png', null, null, null, null, null);
INSERT INTO `mb_level` VALUES ('133', '133', '74832516', 'http://image.matewish.cn/pay_icon/grade8_crown4.png', null, null, null, null, null);
INSERT INTO `mb_level` VALUES ('134', '134', '77133084', 'http://image.matewish.cn/pay_icon/grade8_crown4.png', null, null, null, null, null);
INSERT INTO `mb_level` VALUES ('135', '135', '79486321', 'http://image.matewish.cn/pay_icon/grade8_crown4.png', null, null, null, null, null);
INSERT INTO `mb_level` VALUES ('136', '136', '81893025', 'http://image.matewish.cn/pay_icon/grade8_crown4.png', null, null, null, null, null);
INSERT INTO `mb_level` VALUES ('137', '137', '84354000', 'http://image.matewish.cn/pay_icon/grade8_crown4.png', null, null, null, null, null);
INSERT INTO `mb_level` VALUES ('138', '138', '86870056', 'http://image.matewish.cn/pay_icon/grade8_crown4.png', null, null, null, null, null);
INSERT INTO `mb_level` VALUES ('139', '139', '89442009', 'http://image.matewish.cn/pay_icon/grade8_crown4.png', null, null, null, null, null);
INSERT INTO `mb_level` VALUES ('140', '140', '92070681', 'http://image.matewish.cn/pay_icon/grade8_crown4.png', null, null, null, null, null);
INSERT INTO `mb_level` VALUES ('141', '141', '94756900', 'http://image.matewish.cn/pay_icon/grade8_crown4.png', null, null, null, null, null);
INSERT INTO `mb_level` VALUES ('142', '142', '97501500', 'http://image.matewish.cn/pay_icon/grade8_crown4.png', null, null, null, null, null);
INSERT INTO `mb_level` VALUES ('143', '143', '100305321', 'http://image.matewish.cn/pay_icon/grade8_crown4.png', null, null, null, null, null);
INSERT INTO `mb_level` VALUES ('144', '144', '103169209', 'http://image.matewish.cn/pay_icon/grade8_crown4.png', null, null, null, null, null);
INSERT INTO `mb_level` VALUES ('145', '145', '106094016', 'http://image.matewish.cn/pay_icon/grade8_crown4.png', null, null, null, null, null);
INSERT INTO `mb_level` VALUES ('146', '146', '109080600', 'http://image.matewish.cn/pay_icon/grade8_crown4.png', null, null, null, null, null);
INSERT INTO `mb_level` VALUES ('147', '147', '112129825', 'http://image.matewish.cn/pay_icon/grade8_crown4.png', null, null, null, null, null);
INSERT INTO `mb_level` VALUES ('148', '148', '115242561', 'http://image.matewish.cn/pay_icon/grade8_crown4.png', null, null, null, null, null);
INSERT INTO `mb_level` VALUES ('149', '149', '118419684', 'http://image.matewish.cn/pay_icon/grade8_crown4.png', null, null, null, null, null);
INSERT INTO `mb_level` VALUES ('150', '150', '121662076', 'http://image.matewish.cn/pay_icon/grade8_crown4.png', null, null, null, null, null);
INSERT INTO `mb_level` VALUES ('151', '151', '124970625', 'http://image.matewish.cn/pay_icon/grade8_crown4.png', null, null, null, null, null);
INSERT INTO `mb_level` VALUES ('152', '152', '128346225', 'http://image.matewish.cn/pay_icon/grade8_crown4.png', null, null, null, null, null);
INSERT INTO `mb_level` VALUES ('153', '153', '131789776', 'http://image.matewish.cn/pay_icon/grade8_crown4.png', null, null, null, null, null);
INSERT INTO `mb_level` VALUES ('154', '154', '135302184', 'http://image.matewish.cn/pay_icon/grade8_crown4.png', null, null, null, null, null);
INSERT INTO `mb_level` VALUES ('155', '155', '138884361', 'http://image.matewish.cn/pay_icon/grade8_crown4.png', null, null, null, null, null);
INSERT INTO `mb_level` VALUES ('156', '156', '142537225', 'http://image.matewish.cn/pay_icon/grade8_crown4.png', null, null, null, null, null);
INSERT INTO `mb_level` VALUES ('157', '157', '146261700', 'http://image.matewish.cn/pay_icon/grade8_crown4.png', null, null, null, null, null);
INSERT INTO `mb_level` VALUES ('158', '158', '150058716', 'http://image.matewish.cn/pay_icon/grade8_crown4.png', null, null, null, null, null);
INSERT INTO `mb_level` VALUES ('159', '159', '153929209', 'http://image.matewish.cn/pay_icon/grade8_crown4.png', null, null, null, null, null);
INSERT INTO `mb_level` VALUES ('160', '160', '157874121', 'http://image.matewish.cn/pay_icon/grade8_crown4.png', null, null, null, null, null);
INSERT INTO `mb_level` VALUES ('161', '161', '161894400', 'http://image.matewish.cn/pay_icon/grade8_crown4.png', null, null, null, null, null);
INSERT INTO `mb_level` VALUES ('162', '162', '165991000', 'http://image.matewish.cn/pay_icon/grade8_crown4.png', null, null, null, null, null);
INSERT INTO `mb_level` VALUES ('163', '163', '170164881', 'http://image.matewish.cn/pay_icon/grade8_crown4.png', null, null, null, null, null);
INSERT INTO `mb_level` VALUES ('164', '164', '174417009', 'http://image.matewish.cn/pay_icon/grade8_crown4.png', null, null, null, null, null);
INSERT INTO `mb_level` VALUES ('165', '165', '178748356', 'http://image.matewish.cn/pay_icon/grade8_crown4.png', null, null, null, null, null);
INSERT INTO `mb_level` VALUES ('166', '166', '183159900', 'http://image.matewish.cn/pay_icon/grade8_crown4.png', null, null, null, null, null);
INSERT INTO `mb_level` VALUES ('167', '167', '187652625', 'http://image.matewish.cn/pay_icon/grade8_crown4.png', null, null, null, null, null);
INSERT INTO `mb_level` VALUES ('168', '168', '192227521', 'http://image.matewish.cn/pay_icon/grade8_crown4.png', null, null, null, null, null);
INSERT INTO `mb_level` VALUES ('169', '169', '196885584', 'http://image.matewish.cn/pay_icon/grade8_crown4.png', null, null, null, null, null);
INSERT INTO `mb_level` VALUES ('170', '170', '201627816', 'http://image.matewish.cn/pay_icon/grade8_crown4.png', null, null, null, null, null);
INSERT INTO `mb_level` VALUES ('171', '171', '206455225', 'http://image.matewish.cn/pay_icon/grade8_crown4.png', null, null, null, null, null);
INSERT INTO `mb_level` VALUES ('172', '172', '211368825', 'http://image.matewish.cn/pay_icon/grade8_crown4.png', null, null, null, null, null);
INSERT INTO `mb_level` VALUES ('173', '173', '216369636', 'http://image.matewish.cn/pay_icon/grade8_crown4.png', null, null, null, null, null);
INSERT INTO `mb_level` VALUES ('174', '174', '221458684', 'http://image.matewish.cn/pay_icon/grade8_crown4.png', null, null, null, null, null);
INSERT INTO `mb_level` VALUES ('175', '175', '226637001', 'http://image.matewish.cn/pay_icon/grade8_crown4.png', null, null, null, null, null);
INSERT INTO `mb_level` VALUES ('176', '176', '231905625', 'http://image.matewish.cn/pay_icon/grade8_crown4.png', null, null, null, null, null);
INSERT INTO `mb_level` VALUES ('177', '177', '237265600', 'http://image.matewish.cn/pay_icon/grade8_crown4.png', null, null, null, null, null);
INSERT INTO `mb_level` VALUES ('178', '178', '242717976', 'http://image.matewish.cn/pay_icon/grade8_crown4.png', null, null, null, null, null);
INSERT INTO `mb_level` VALUES ('179', '179', '248263809', 'http://image.matewish.cn/pay_icon/grade8_crown4.png', null, null, null, null, null);
INSERT INTO `mb_level` VALUES ('180', '180', '253904161', 'http://image.matewish.cn/pay_icon/grade8_crown4.png', null, null, null, null, null);
INSERT INTO `mb_level` VALUES ('181', '181', '259640100', 'http://image.matewish.cn/pay_icon/grade8_crown4.png', null, null, null, null, null);
INSERT INTO `mb_level` VALUES ('182', '182', '265472700', 'http://image.matewish.cn/pay_icon/grade8_crown4.png', null, null, null, null, null);
INSERT INTO `mb_level` VALUES ('183', '183', '271403041', 'http://image.matewish.cn/pay_icon/grade8_crown4.png', null, null, null, null, null);
INSERT INTO `mb_level` VALUES ('184', '184', '277432209', 'http://image.matewish.cn/pay_icon/grade8_crown4.png', null, null, null, null, null);
INSERT INTO `mb_level` VALUES ('185', '185', '283561296', 'http://image.matewish.cn/pay_icon/grade8_crown4.png', null, null, null, null, null);
INSERT INTO `mb_level` VALUES ('186', '186', '289791400', 'http://image.matewish.cn/pay_icon/grade8_crown4.png', null, null, null, null, null);
INSERT INTO `mb_level` VALUES ('187', '187', '296123625', 'http://image.matewish.cn/pay_icon/grade8_crown4.png', null, null, null, null, null);
INSERT INTO `mb_level` VALUES ('188', '188', '302559081', 'http://image.matewish.cn/pay_icon/grade8_crown4.png', null, null, null, null, null);
INSERT INTO `mb_level` VALUES ('189', '189', '309098884', 'http://image.matewish.cn/pay_icon/grade8_crown4.png', null, null, null, null, null);
INSERT INTO `mb_level` VALUES ('190', '190', '315744156', 'http://image.matewish.cn/pay_icon/grade8_crown4.png', null, null, null, null, null);
INSERT INTO `mb_level` VALUES ('191', '191', '322496025', 'http://image.matewish.cn/pay_icon/grade8_crown4.png', null, null, null, null, null);
INSERT INTO `mb_level` VALUES ('192', '192', '329355625', 'http://image.matewish.cn/pay_icon/grade8_crown4.png', null, null, null, null, null);
INSERT INTO `mb_level` VALUES ('193', '193', '336324096', 'http://image.matewish.cn/pay_icon/grade8_crown4.png', null, null, null, null, null);
INSERT INTO `mb_level` VALUES ('194', '194', '343402584', 'http://image.matewish.cn/pay_icon/grade8_crown4.png', null, null, null, null, null);
INSERT INTO `mb_level` VALUES ('195', '195', '350592241', 'http://image.matewish.cn/pay_icon/grade8_crown4.png', null, null, null, null, null);
INSERT INTO `mb_level` VALUES ('196', '196', '357894225', 'http://image.matewish.cn/pay_icon/grade8_crown4.png', null, null, null, null, null);
INSERT INTO `mb_level` VALUES ('197', '197', '365309700', 'http://image.matewish.cn/pay_icon/grade8_crown4.png', null, null, null, null, null);
INSERT INTO `mb_level` VALUES ('198', '198', '372839836', 'http://image.matewish.cn/pay_icon/grade8_crown4.png', null, null, null, null, null);
INSERT INTO `mb_level` VALUES ('199', '199', '380485809', 'http://image.matewish.cn/pay_icon/grade8_crown4.png', null, null, null, null, null);
INSERT INTO `mb_level` VALUES ('200', '200', '388248801', 'http://image.matewish.cn/pay_icon/grade8_crown4.png', null, null, null, null, null);
