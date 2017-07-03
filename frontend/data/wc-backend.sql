SET NAMES utf8;
SET FOREIGN_KEY_CHECKS = 0;

-- ----------------------------
--  Table structure for `wc_attention_event`
-- ----------------------------
DROP TABLE IF EXISTS `wc_attention_event`;
CREATE TABLE `wc_attention_event` (
  `record_id` int(11) NOT NULL AUTO_INCREMENT COMMENT '记录id ',
  `app_id` int(11) NOT NULL COMMENT '对应公众号id',
  `content` varchar(100) DEFAULT NULL COMMENT '消息内容',
  `msg_type` int(11) DEFAULT NULL COMMENT '消息类型 0文本消息 1图文消息',
  `create_time` datetime DEFAULT NULL COMMENT '创建时间',
  `flag` int(11) DEFAULT NULL,
  `remark1` varchar(100) DEFAULT NULL COMMENT '备用字段1',
  `remark2` varchar(100) DEFAULT NULL COMMENT '备用字段2',
  `remark3` varchar(100) DEFAULT NULL COMMENT '备用字段3',
  `remark4` varchar(100) DEFAULT NULL COMMENT '备用字段4',
  PRIMARY KEY (`record_id`),
  KEY `appid` (`app_id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=0 DEFAULT CHARSET=utf8mb4 COMMENT='关注回复消息表';


-- ----------------------------
--  Table structure for `wc_authorization`
-- ----------------------------
DROP TABLE IF EXISTS `wc_authorization`;
CREATE TABLE `wc_authorization` (
  `record_id` int(11) NOT NULL AUTO_INCREMENT COMMENT '记录id',
  `app_id` varchar(100) DEFAULT NULL COMMENT 'appid',
  `create_time` int(11) DEFAULT NULL COMMENT '时间戳',
  `verify_ticket` varchar(300) DEFAULT NULL COMMENT '用于授权获取token',
  `access_token` varchar(300) DEFAULT NULL COMMENT '授权token',
  `pre_auth_code` varchar(300) DEFAULT NULL COMMENT '预授权码',
  `remark1` varchar(100) DEFAULT NULL COMMENT '备用字段1',
  `remark2` varchar(100) DEFAULT NULL COMMENT '备用字段2',
  `remark3` varchar(100) DEFAULT NULL COMMENT '备用字段3',
  `remark4` varchar(100) DEFAULT NULL COMMENT '备用字段4',
  PRIMARY KEY (`record_id`),
  UNIQUE KEY `appid` (`app_id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=0 DEFAULT CHARSET=utf8mb4 COMMENT='微信授权信息表';

-- ----------------------------
--  Table structure for `wc_authorization_list`
-- ----------------------------
DROP TABLE IF EXISTS `wc_authorization_list`;
CREATE TABLE `wc_authorization_list` (
  `record_id` int(11) NOT NULL AUTO_INCREMENT COMMENT '主键自增',
  `authorizer_appid` varchar(100) NOT NULL COMMENT '授权公众号appid',
  `authorizer_access_token` varchar(300) NOT NULL COMMENT '授权令牌',
  `authorizer_refresh_token` varchar(300) NOT NULL COMMENT '授权刷新令牌凭证',
  `func_info` varchar(1000) DEFAULT NULL COMMENT '授权权限集json格式',
  `status` int(11) DEFAULT NULL COMMENT '状态',
  `user_id` int(11) DEFAULT NULL COMMENT '操作人id',
  `nick_name` varchar(100) DEFAULT NULL COMMENT '授权方昵称',
  `head_img` varchar(300) DEFAULT NULL COMMENT '授权方头像',
  `service_type_info` int(11) DEFAULT NULL COMMENT '公众号类型：0订阅号 1升级后的订阅号 2服务号',
  `verify_type_info` int(11) DEFAULT NULL COMMENT '认证类型：-1未认证 0微信认证 1新浪微博认证 2腾讯微博认证 3资质认证，未名称认证 4资质认证，未名称认证，新浪微博认证 5资质认证，未名称认证，腾讯微博认证',
  `user_name` varchar(100) DEFAULT NULL COMMENT '授权公众号原始ID',
  `alias` varchar(100) DEFAULT NULL COMMENT '授权公众号的微信号',
  `qrcode_url` varchar(300) DEFAULT NULL COMMENT '二维码Url',
  `business_info` varchar(300) DEFAULT NULL COMMENT '功能开通信息',
  `idc` int(11) DEFAULT NULL,
  `principal_name` varchar(100) DEFAULT NULL COMMENT '公众号主体类型例如：个人/企业',
  `signature` varchar(1000) DEFAULT NULL COMMENT '功能签名',
  `authorization_info` varchar(2000) NOT NULL COMMENT '授权信息主体',
  `create_time` datetime DEFAULT NULL COMMENT '授权时间',
  `update_time` datetime DEFAULT NULL COMMENT '更新时间',
  `remark1` varchar(100) DEFAULT NULL COMMENT '备用字段1',
  `remark2` varchar(100) DEFAULT NULL COMMENT '备用字段2',
  `remark3` varchar(100) DEFAULT NULL COMMENT '备用字段3',
  `remark4` varchar(100) DEFAULT NULL COMMENT '备用字段4',
  PRIMARY KEY (`record_id`),
  UNIQUE KEY `auth_appid` (`authorizer_appid`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=0 DEFAULT CHARSET=utf8mb4 COMMENT='授权公众号信息表';



-- ----------------------------
--  Table structure for `wc_client`
-- ----------------------------
DROP TABLE IF EXISTS `wc_client`;
CREATE TABLE `wc_client` (
  `client_id` int(11) NOT NULL AUTO_INCREMENT COMMENT '用户id ',
  `open_id` varchar(200) DEFAULT NULL COMMENT '对公众号的openid',
  `nick_name` varchar(100) DEFAULT NULL COMMENT '用户昵称',
  `subscribe` int(11) DEFAULT NULL COMMENT '是否关注 0 未关注 ',
  `sex` int(11) DEFAULT NULL COMMENT '性别 1 男 2 女 0未知',
  `city` varchar(100) DEFAULT NULL COMMENT '城市',
  `language` varchar(100) DEFAULT NULL COMMENT '语言',
  `province` varchar(100) DEFAULT NULL COMMENT '省份',
  `country` varchar(100) DEFAULT NULL COMMENT '国家',
  `headimgurl` varchar(500) DEFAULT NULL COMMENT '用户头像 ',
  `subscribe_time` int(11) DEFAULT NULL COMMENT '关注时间',
  `unionid` varchar(200) DEFAULT NULL COMMENT '对开发账号唯一id',
  `groupid` varchar(100) DEFAULT NULL COMMENT '群组id',
  `app_id` varchar(200) DEFAULT NULL COMMENT 'AppId',
  `create_time` datetime DEFAULT NULL COMMENT '创建时间',
  `update_time` datetime DEFAULT NULL COMMENT '更新时间',
  `remark` varchar(100) DEFAULT NULL COMMENT '备注',
  `remark1` varchar(100) DEFAULT NULL COMMENT '备用字段1',
  `remark2` varchar(100) DEFAULT NULL COMMENT '备用字段2',
  `remark3` varchar(100) DEFAULT NULL COMMENT '备用字段3',
  `remark4` varchar(100) DEFAULT NULL COMMENT '备用字段4',
  PRIMARY KEY (`client_id`)
) ENGINE=InnoDB AUTO_INCREMENT=0 DEFAULT CHARSET=utf8mb4 COMMENT='微信用户信息表';


-- ----------------------------
--  Table structure for `wc_menu`
-- ----------------------------
DROP TABLE IF EXISTS `wc_menu`;
CREATE TABLE `wc_menu` (
  `menu_id` int(11) NOT NULL AUTO_INCREMENT COMMENT '权限id',
  `title` varchar(100) NOT NULL COMMENT '权限名称',
  `icon` varchar(100) DEFAULT NULL COMMENT '权限图标',
  `url` varchar(100) NOT NULL COMMENT '路由路径',
  `visible` int(11) NOT NULL COMMENT '是否显示在菜单栏',
  `parent_id` varchar(100) NOT NULL COMMENT '上级id',
  `order_no` int(11) DEFAULT NULL COMMENT '排序号',
  `status` int(11) NOT NULL COMMENT '状态',
  `remark1` varchar(100) DEFAULT NULL COMMENT '备用字段1',
  `remark2` varchar(100) DEFAULT NULL COMMENT '备用字段2',
  `remark3` varchar(100) DEFAULT NULL COMMENT '备用字段3',
  `remark4` varchar(100) DEFAULT NULL COMMENT '备用字段4',
  PRIMARY KEY (`menu_id`)
) ENGINE=InnoDB AUTO_INCREMENT=0 DEFAULT CHARSET=utf8mb4 COMMENT='权限菜单表';



-- ----------------------------
--  Table structure for `wc_system_params`
-- ----------------------------
DROP TABLE IF EXISTS `wc_system_params`;
CREATE TABLE `wc_system_params` (
  `recode_id` int(11) NOT NULL,
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ----------------------------
--  Table structure for `wc_user`
-- ----------------------------
DROP TABLE IF EXISTS `wc_user`;
CREATE TABLE `wc_user` (
  `backend_user_id` int(11) NOT NULL AUTO_INCREMENT COMMENT '管理员id',
  `username` varchar(100) NOT NULL COMMENT '管理员昵称',
  `pwd_hash` varchar(300) NOT NULL,
  `pwd_reset_token` varchar(100) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL COMMENT '管理员邮箱',
  `auth_key` varchar(100) DEFAULT NULL,
  `status` int(11) NOT NULL COMMENT '管理员状态',
  `pic` varchar(200) DEFAULT NULL COMMENT '头像',
  `create_at` int(11) NOT NULL COMMENT '创建时间',
  `update_at` int(11) NOT NULL COMMENT '更新时间',
  `remark1` varchar(100) DEFAULT NULL COMMENT '备用字段1',
  `remark2` varchar(100) DEFAULT NULL COMMENT '备用字段2',
  `password` varchar(100) NOT NULL,
  PRIMARY KEY (`backend_user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=0 DEFAULT CHARSET=utf8mb4 COMMENT='管理员信息表';


-- ----------------------------
--  Table structure for `wc_user_menu`
-- ----------------------------
DROP TABLE IF EXISTS `wc_user_menu`;
CREATE TABLE `wc_user_menu` (
  `record_id` int(11) NOT NULL AUTO_INCREMENT COMMENT '记录id',
  `user_id` int(11) NOT NULL COMMENT '管理员id',
  `menu_id` int(11) NOT NULL COMMENT '菜单id',
  `remark1` varchar(100) DEFAULT NULL,
  `remark2` varchar(100) DEFAULT NULL,
  `remark3` varchar(100) DEFAULT NULL,
  `remark4` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`record_id`)
) ENGINE=InnoDB AUTO_INCREMENT=0 DEFAULT CHARSET=utf8mb4 COMMENT='管理员权限表';



SET FOREIGN_KEY_CHECKS = 1;
