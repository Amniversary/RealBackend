ALTER TABLE `my_city` CHANGE `city_id` `city_id` INT(11) NOT NULL AUTO_INCREMENT;


ALTER TABLE `my_account_info` ADD `create_time` DATETIME NULL COMMENT '创建时间' AFTER `status`;


ALTER TABLE `my_account_info` ADD `school_name` VARCHAR(100) CHARACTER SET utf8 COLLATE utf8_general_ci NULL COMMENT '学校名称' , ADD `school_area` VARCHAR(100) CHARACTER SET utf8 COLLATE utf8_general_ci NULL COMMENT '学校所在地' , ADD `hometown` VARCHAR(100) CHARACTER SET utf8 COLLATE utf8_general_ci NULL COMMENT '故乡' ;


ALTER TABLE `my_wish` CHANGE `reward_money` `wish_money` DECIMAL(12,2) NULL DEFAULT NULL COMMENT '愿望总额';

ALTER TABLE `my_wish` ADD `is_official` INT NULL COMMENT '是否官方' ;

ALTER TABLE `my_reward_list`  ADD `reward_money_except_packets` DECIMAL(12,2) NULL COMMENT '除了红包外的金额' ,  ADD `red_packets_id` INT NULL COMMENT '红包id' ,  ADD `red_packets_money` DECIMAL(12,2) NULL COMMENT '红包金额' ;




INSERT INTO  `meiyuan`.`my_system_params` (
`system_params_id` ,
`code` ,
`discribtion` ,
`value1` ,
`value2` ,
`value3` ,
`remark1` ,
`remark2` ,
`remark3` ,
`remark4` ,
`title`
)
VALUES (
NULL ,  'system_reward_for_wish_public_packets',  '设置打赏人第一次打赏时愿望发布者所领取的红包',  '第一次打赏，愿望发布者红包', NULL , NULL , NULL , NULL , NULL , NULL , NULL
), (
NULL ,  'system_reward_for_userself_packets',  '第一次打赏时打赏人所领红包', NULL , NULL , NULL , NULL , NULL , NULL , NULL , NULL
);

ALTER TABLE `my_shortmessage_list` CHANGE `id` `id` INT(11) NOT NULL AUTO_INCREMENT COMMENT '自增主键';


ALTER TABLE  `my_shortmessage_list` ADD  `status` INT NULL COMMENT  '发送状态 0 失败  1成功';

ALTER TABLE  `my_user_intermediate` ADD  `status` INT NULL COMMENT  '审核状态 1未审核 2已审核';

ALTER TABLE  `my_user_intermediate` ADD  `verify_time` DATETIME NULL COMMENT  '认证通过时间';

ALTER TABLE  `my_base_centification` ADD  `verify_time` DATETIME NULL COMMENT  '初级认证通过时间';

ALTER TABLE `my_borrow_fund_record` ADD `stage_money` DECIMAL(12,2) NULL COMMENT '每期还款金额' AFTER `borrow_money`;

ALTER TABLE `my_borrow_fund_record`  ADD `user_name` VARCHAR(100) CHARACTER SET utf8 COLLATE utf8_general_ci NULL COMMENT '姓名' AFTER `finance_remark`,  ADD `card_no` VARCHAR(100) CHARACTER SET utf8 COLLATE utf8_general_ci NULL COMMENT '卡号' AFTER `user_name`,  ADD `identity_no` VARCHAR(100) CHARACTER SET utf8 COLLATE utf8_general_ci NULL COMMENT '身份证号' AFTER `card_no`,  ADD `borrow_type` INT NULL COMMENT '借款类别 1打赏  2提现' AFTER `identity_no`;

ALTER TABLE  `my_borrow_fund_record` ADD  `bank_name` VARCHAR( 100 ) CHARACTER SET utf8 COLLATE utf8_general_ci NULL COMMENT  '银行名称' AFTER  `borrow_type`;

ALTER TABLE `my_borrow_fund_record` CHANGE `status_result` `status_result` INT(11) NULL DEFAULT NULL COMMENT '状态 1 受理 2 审核通过 4 财务打款（已完成） 8 拒绝';

ALTER TABLE `my_bill` ADD `real_back_fee` DECIMAL(12,2) NULL COMMENT '实际还款总额 还款金额 + 违约金额 + 持续违约金额' AFTER `is_cur_stage`, ADD `breach_fee` DECIMAL(12,2) NULL COMMENT '违约金额' AFTER `real_back_fee`, ADD `last_breach_fee` DECIMAL(12,2) NULL COMMENT '持续违约金额' AFTER `breach_fee`;

ALTER TABLE `my_bill` ADD `breach_days` INT NULL COMMENT '违约天数' AFTER `last_breach_fee`;

ALTER TABLE  `my_personal_red_packets` ADD  `is_base_verify` INT NULL COMMENT  '打赏人是否初级认证，如果打赏人不初级认证，愿望发起人无法收到钱' AFTER  `status`;

ALTER TABLE `my_user_bank_card` ADD `card_type` INT NULL COMMENT '卡类型， 1储蓄卡 2银行卡' AFTER `user_id`;

ALTER TABLE `my_personal_red_packets` ADD `create_time` DATETIME NULL COMMENT '创建时间' AFTER `is_base_verify`, ADD `record_id` INT NULL COMMENT '记录id，愿望类别或某一个愿望' AFTER `create_time`, ADD `over_money_for_us` DECIMAL(12,2) NULL COMMENT '满多少才能用' AFTER `record_id`;


ALTER TABLE `my_wish` ADD `back_dis` TEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL COMMENT '回报描述' AFTER `back_type`;

INSERT INTO `meiyuan`.`my_system_params` (`system_params_id`, `code`, `discribtion`, `value1`, `value2`, `value3`, `remark1`, `remark2`, `remark3`, `remark4`, `title`) VALUES (24, 'sys_cash_rate', '提现手续费，默认3，单位%', '提现手续费', '3', NULL, NULL, NULL, NULL, NULL, NULL);

ALTER TABLE `my_red_packets` CHANGE `open_type` `is_rand` INT(11) NULL DEFAULT NULL COMMENT '是否随机发放金额 1 是 0否';

ALTER TABLE `my_personal_red_packets` CHANGE `open_type` `is_rand` INT(11) NULL DEFAULT NULL COMMENT '是否随机发放金额 1是 0否';

CREATE TABLE IF NOT EXISTS `my_get_cash` (
  `get_cash_id` int(11) NOT NULL AUTO_INCREMENT COMMENT '主键自增',
  `user_id` int(11) DEFAULT NULL COMMENT '用户id',
  `nick_name` varchar(100) DEFAULT NULL COMMENT '用户名称',
  `cash_money` decimal(12,2) DEFAULT NULL COMMENT '提现金额',
  `cash_rate` int(11) DEFAULT NULL COMMENT '提现费率',
  `cash_fees` decimal(12,2) DEFAULT NULL COMMENT '提现手续费',
  `real_cash_money` decimal(12,2) DEFAULT NULL COMMENT '除手续费后的提现金额',
  `status` int(11) DEFAULT NULL COMMENT '状态 1、已受理、2已审核、3打款、4拒绝',
  `refuesd_reason` varchar(100) DEFAULT NULL COMMENT '拒绝原因',
  `finance_remark` varchar(100) DEFAULT NULL COMMENT '打款备注',
  `identity_no` varchar(100) DEFAULT NULL COMMENT '身份证号',
  `real_name` varchar(100) DEFAULT NULL COMMENT '姓名',
  `card_no` varchar(100) DEFAULT NULL COMMENT '银行卡号',
  `bank_name` varchar(100) DEFAULT NULL COMMENT '银行名称',
  `create_time` datetime DEFAULT NULL COMMENT '创建时间',
  `remark1` varchar(100) DEFAULT NULL,
  `remark2` varchar(100) DEFAULT NULL,
  `remark3` varchar(100) DEFAULT NULL,
  `remark4` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`get_cash_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='提现表' AUTO_INCREMENT=1 ;

ALTER TABLE  `my_user_collection` ADD  `create_time` DATETIME NULL COMMENT  '创建时间' AFTER  `other_id`;


CREATE TABLE IF NOT EXISTS `my_payment` (
`payment_id` int(11) NOT NULL,
  `code` int(11) DEFAULT NULL,
  `title` varchar(100) DEFAULT NULL,
  `order_no` varchar(10) DEFAULT NULL COMMENT '排序号',
  `status` int(11) DEFAULT NULL COMMENT '状态 1 开通  0 未开通',
  `remark1` varchar(100) DEFAULT NULL,
  `remark2` varchar(100) DEFAULT NULL,
  `remark3` varchar(100) DEFAULT NULL,
  `remark4` varchar(100) DEFAULT NULL
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='支付方式表' AUTO_INCREMENT=6 ;


INSERT INTO `my_payment` (`payment_id`, `code`, `title`, `order_no`, `status`, `remark1`, `remark2`, `remark3`, `remark4`) VALUES
(1, 1, '账户余额支付', '001', NULL, NULL, NULL, NULL, NULL),
(2, 2, '美愿基金支付', '002', NULL, NULL, NULL, NULL, NULL),
(3, 3, '支付宝支付', '003', NULL, NULL, NULL, NULL, NULL),
(4, 4, '微信支付', '004', NULL, NULL, NULL, NULL, NULL),
(5, 5, '连连支付', '005', NULL, NULL, NULL, NULL, NULL);

ALTER TABLE `my_payment`
 ADD PRIMARY KEY (`payment_id`);

ALTER TABLE `my_payment`
MODIFY `payment_id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=6;

CREATE TABLE IF NOT EXISTS `my_school_info` (
`school_id` int(11) NOT NULL,
  `code` varchar(50) DEFAULT NULL COMMENT '代号',
  `school_name` varchar(100) DEFAULT NULL COMMENT '学校名称',
  `order_no` varchar(10) DEFAULT NULL COMMENT '排序号',
  `status` int(11) DEFAULT NULL COMMENT '状态',
  `province` varchar(100) DEFAULT NULL COMMENT '省',
  `city` varchar(100) DEFAULT NULL COMMENT '市',
  `county` varchar(100) DEFAULT NULL COMMENT '县',
  `student_credit` int(11) DEFAULT NULL COMMENT '学生信用增额',
  `social_credit` int(11) DEFAULT NULL COMMENT '社会人员信用增额',
  `remark1` varchar(100) DEFAULT NULL,
  `remark2` varchar(100) DEFAULT NULL,
  `remark3` varchar(100) DEFAULT NULL,
  `remark4` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='学校信息' AUTO_INCREMENT=1 ;

ALTER TABLE `my_school_info`
 ADD PRIMARY KEY (`school_id`);

ALTER TABLE `my_school_info`
MODIFY `school_id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `my_school_info` ADD `degree` VARCHAR(50) NULL COMMENT '学历 本科、专科' AFTER `school_name`;

CREATE TABLE IF NOT EXISTS `my_user` (
`backend_user_id` int(11) NOT NULL,
  `username` varchar(100) DEFAULT NULL COMMENT '用户名',
  `password_hash` varchar(100) DEFAULT NULL COMMENT '密码hash值',
  `password_reset_token` varchar(100) DEFAULT NULL COMMENT '密码重置token',
  `email` varchar(100) DEFAULT NULL COMMENT '邮箱',
  `auth_key` varchar(100) DEFAULT NULL,
  `status` int(11) DEFAULT NULL,
  `created_at` int(11) DEFAULT NULL COMMENT '创建时间',
  `updated_at` int(11) DEFAULT NULL COMMENT '更新时间',
  `password` varchar(100) DEFAULT NULL COMMENT '密码',
  `pic` varchar(100) DEFAULT NULL COMMENT '图标',
  `remark1` varchar(100) DEFAULT NULL,
  `remark2` varchar(100) DEFAULT NULL,
  `remark3` varchar(100) DEFAULT NULL,
  `remark4` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='后台用户表' AUTO_INCREMENT=1 ;

ALTER TABLE `my_user`
 ADD PRIMARY KEY (`backend_user_id`);

ALTER TABLE `my_user`
MODIFY `backend_user_id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `my_bill` ADD `other_pay_bill` VARCHAR(100) NULL COMMENT '第三方支付账单号' AFTER `pay_bill`;

ALTER TABLE `my_borrow_fund_record` ADD `reward_id` INT NULL COMMENT '打赏记录id，提现时无用' AFTER `borrow_type`;

ALTER TABLE `my_get_cash`  ADD `check_time` DATETIME NULL COMMENT '审核时间'  AFTER `create_time`,  ADD `finace_ok_time` DATETIME NULL COMMENT '打款时间'  AFTER `check_time`;

ALTER TABLE `my_business_check` ADD `create_user_id` INT NULL COMMENT '发起人id' AFTER `check_user_name`, ADD `create_user_name` VARCHAR(100) NULL COMMENT '发起人姓名' AFTER `create_user_id`;

CREATE TABLE IF NOT EXISTS `my_recharge_list` (
`recharge_id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL COMMENT '用户id',
  `charge_money` decimal(12,2) DEFAULT NULL COMMENT '充值金额',
  `status_result` int(11) DEFAULT NULL COMMENT '状态 1 支付中 2 已支付',
  `fail_reason` varchar(100) DEFAULT NULL COMMENT '支付失败原因',
  `pay_type` int(11) DEFAULT NULL COMMENT '支付方式 3支付宝  4微信支付 5连连支付',
  `pay_bill` varchar(100) DEFAULT NULL COMMENT '账单号',
  `other_pay_bill` varchar(100) DEFAULT NULL COMMENT '第三方账单号',
  `pay_times` int(11) DEFAULT NULL COMMENT '支付次数',
  `create_time` datetime DEFAULT NULL COMMENT '创建时间',
  `remark1` varchar(100) DEFAULT NULL COMMENT '备用字段',
  `remark2` varchar(100) DEFAULT NULL COMMENT '备用字段',
  `remark3` varchar(100) DEFAULT NULL COMMENT '备用字段',
  `remark4` varchar(100) DEFAULT NULL COMMENT '备用字段'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='充值表' AUTO_INCREMENT=1 ;


ALTER TABLE `my_recharge_list`
 ADD PRIMARY KEY (`recharge_id`);

ALTER TABLE `my_recharge_list`
MODIFY `recharge_id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `my_wish` ADD `publish_user_phone` VARCHAR(100) NULL COMMENT '是否完成 1未完成 2完成' AFTER `hot_num`, ADD `is_finish` INT NULL COMMENT '是否转到余额 1 否 2是' AFTER `publish_user_phone`, ADD `to_balance` INT NULL COMMENT '退还状态 1 未退还 2 进行中 3 完成 4失败' AFTER `is_finish`, ADD `back_status` INT NULL COMMENT '退还人数' AFTER `to_balance`, ADD `back_count` INT NULL COMMENT '退还人数' AFTER `back_status`, ADD `back_money` DECIMAL(12,2) NULL COMMENT '退还金额 退款总金额，红包不退' AFTER `back_count`, ADD INDEX (`publish_user_phone`) ;

CREATE TABLE IF NOT EXISTS `my_report_list` (
`my_report_id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL COMMENT '举报发起人id',
  `nick_name` varchar(100) DEFAULT NULL COMMENT '举报发起人昵称',
  `report_type` int(11) DEFAULT NULL COMMENT '举报类型',
  `wish_id` int(11) DEFAULT NULL COMMENT '愿望id',
  `wish_name` varchar(100) DEFAULT NULL COMMENT '愿望名称',
  `report_user_id` int(11) DEFAULT NULL COMMENT '被举报的用户id',
  `report_user_name` varchar(100) DEFAULT NULL COMMENT '被举报人昵称',
  `report_content` varchar(200) DEFAULT NULL COMMENT '举报内容',
  `create_time` datetime DEFAULT NULL COMMENT '创建时间',
  `status` int(11) DEFAULT NULL COMMENT '状态',
  `check_time` datetime DEFAULT NULL COMMENT '审核时间',
  `remark1` varchar(100) DEFAULT NULL COMMENT '审核备注',
  `remark2` varchar(100) DEFAULT NULL,
  `remark3` varchar(100) DEFAULT NULL,
  `remark4` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='举报列表' AUTO_INCREMENT=1 ;

ALTER TABLE `my_report_list`
 ADD PRIMARY KEY (`my_report_id`);

ALTER TABLE `my_report_list`
MODIFY `my_report_id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `my_bill` CHANGE `pay_bill` `pay_bill` VARCHAR(100) NULL DEFAULT NULL COMMENT '支付账单号';

CREATE TABLE IF NOT EXISTS `my_hot_words` (
`hot_words_id` int(11) NOT NULL,
  `words_type` int(11) DEFAULT NULL COMMENT '类型 1 人 2 愿望  3 城市',
  `content` varchar(100) DEFAULT NULL COMMENT '单词内容',
  `order_no` varchar(10) DEFAULT NULL COMMENT '排序号',
  `status` int(11) DEFAULT NULL COMMENT '状态 1正常，默认  0 禁止',
  `remark1` varchar(100) DEFAULT NULL,
  `remark2` varchar(100) DEFAULT NULL,
  `remark3` varchar(100) DEFAULT NULL,
  `remark4` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='热门关键字表' AUTO_INCREMENT=1 ;

ALTER TABLE `my_hot_words`
 ADD PRIMARY KEY (`hot_words_id`);

ALTER TABLE `my_hot_words`
MODIFY `hot_words_id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `my_bill` ADD `is_check_delay` INT NULL COMMENT '是否检查逾期 1未 2 已检测' AFTER `breach_days`, ADD `is_delay` INT NULL COMMENT '是否逾期 1 未 2 逾期' AFTER `is_check_delay`, ADD `bad_bill_remark` VARCHAR(100) NULL COMMENT '坏账备注' AFTER `is_delay`, ADD `bad_mark_user_id` VARCHAR(100) NULL COMMENT '坏账标记人' AFTER `bad_bill_remark`, ADD `bad_mark_user_name` VARCHAR(100) NULL COMMENT '坏账标记人名称' AFTER `bad_mark_user_id`;


ALTER TABLE `my_user_active` ADD `fund_cash_count` INT NULL COMMENT '美愿基金借款次数' AFTER `sign_sum_count`, ADD `fund_cash_money` DECIMAL(12,2) NULL COMMENT '美愿基金借款金额' AFTER `fund_cash_count`, ADD `fund_back_count` INT NULL COMMENT '美愿基金还款次数' AFTER `fund_cash_money`, ADD `fund_back_money` DECIMAL(12,2) NULL COMMENT '美愿基金还款金额' AFTER `fund_back_count`, ADD `check_refused_count` INT NULL COMMENT '审核被拒次数' AFTER `fund_back_money`, ADD `check_refused_content` TEXT NULL COMMENT '审核被拒内容 只保留最近10条' AFTER `check_refused_count`;

ALTER TABLE `my_business_check` ADD `check_no` INT NULL COMMENT '审核号' AFTER `create_user_name`;


ALTER TABLE `my_user_active` ADD `balance_cash_count` INT NULL COMMENT '余额提现次数' AFTER `sign_sum_count`, ADD `balance_cash_money` DECIMAL(12,2) NULL COMMENT '余额提现总额' AFTER `balance_cash_count`;

ALTER TABLE `my_business_check` ADD `refused_reason` VARCHAR(100) NULL COMMENT '拒绝原因' AFTER `check_no`;

INSERT INTO `meiyuan`.`my_system_params` (`system_params_id`, `code`, `discribtion`, `value1`, `value2`, `value3`, `remark1`, `remark2`, `remark3`, `remark4`, `title`) VALUES (NULL, 'system_share_wish_title', '愿望分享标题', '愿望分享标题', '新年新气象，和我一起来美愿实现愿望吧！', NULL, NULL, NULL, NULL, NULL, NULL), (NULL, 'system_share_wish_content', '愿望分享内容', '愿望分享内容', '下载美愿，快来支持我的愿望，您可获得3倍奖金，同时为我赢取1倍奖金！', NULL, NULL, NULL, NULL, NULL, NULL);

INSERT INTO `meiyuan`.`my_system_params` (`system_params_id`, `code`, `discribtion`, `value1`, `value2`, `value3`, `remark1`, `remark2`, `remark3`, `remark4`, `title`) VALUES (NULL, 'system_share_invite_title', '邀请朋友标题', '邀请朋友标题', '美愿，一个帮你实现愿望的app！', NULL, NULL, NULL, NULL, NULL, NULL), (NULL, 'system_share_invite_content', '邀请朋友内容', '邀请朋友内容', '美愿是一个共享社交金融平台，通过共享愿望的方式，实现每一个愿望。', NULL, NULL, NULL, NULL, NULL, NULL);

ALTER TABLE `my_system_params` ADD `group_id` INT NULL COMMENT '分组id' AFTER `system_params_id`;

ALTER TABLE `my_reward_list` ADD INDEX(`wish_id`);

ALTER TABLE `my_wish_comment` ADD INDEX(`wish_id`);

ALTER TABLE `my_reward_list` ADD INDEX(`pay_status`);

ALTER TABLE `my_get_cash` ADD INDEX(`status`);

ALTER TABLE `my_message` ADD INDEX(`user_id`);

ALTER TABLE `my_bill` ADD INDEX(`back_date`);

ALTER TABLE `my_bill` ADD INDEX(`status`);

ALTER TABLE `my_reward_list` ADD `first_red_packet_id` INT NULL COMMENT '第一次打赏红包id' AFTER `reward_money`, ADD `first_red_packet_money` DECIMAL(12,2) NULL COMMENT '第一次打赏红包金额' AFTER `first_red_packet_id`;

ALTER TABLE `my_friends_list` ADD INDEX(`user_id`);

ALTER TABLE `my_fund_info` ADD UNIQUE(`user_id`);

ALTER TABLE `my_personal_red_packets` ADD INDEX(`user_id`);

ALTER TABLE `my_sign_login` ADD INDEX(`user_id`);

ALTER TABLE `my_user` ADD UNIQUE(`username`);

ALTER TABLE `my_user_account_info` ADD UNIQUE(`user_id`);

ALTER TABLE `my_user_active` ADD UNIQUE(`user_id`);

ALTER TABLE `my_user_address` ADD INDEX(`user_id`);

ALTER TABLE `my_user_bank_card` ADD INDEX(`user_id`);

ALTER TABLE `my_user_collection` ADD INDEX(`user_id`);

ALTER TABLE `my_user_intermediate` ADD UNIQUE(`user_id`);

ALTER TABLE `my_user_weixin` ADD INDEX(`user_id`);

ALTER TABLE `my_user_weixin` ADD UNIQUE(`open_id`);

ALTER TABLE `my_reward_list` ADD INDEX(`reward_user_id`);

ALTER TABLE `my_account_info` ADD `is_inner` INT NULL COMMENT '是否内部 1 是 2 否' AFTER `create_time`;

ALTER TABLE `my_business_check` ADD INDEX(`check_no`);

CREATE TABLE IF NOT EXISTS `my_set_user_check_no` (
`set_check_no_id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL COMMENT '后台用户id',
  `start_no` int(11) DEFAULT NULL COMMENT '开始审核号，审核号必须链接',
  `end_no` int(11) DEFAULT NULL COMMENT '结束审核号',
  `remark1` varchar(100) DEFAULT NULL,
  `remark2` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='设置人员审核表' AUTO_INCREMENT=1 ;


ALTER TABLE `my_set_user_check_no`
 ADD PRIMARY KEY (`set_check_no_id`), ADD UNIQUE KEY `user_id` (`user_id`);

ALTER TABLE `my_set_user_check_no`
MODIFY `set_check_no_id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `my_borrow_fund_record` ADD `protocal_url` TEXT NULL COMMENT '协议url' AFTER `bank_name`;

ALTER TABLE `my_bill` ADD `source_fee` DECIMAL(12,2) NULL COMMENT '本金' AFTER `back_fee`, ADD `borrow_fee` DECIMAL(12,2) NULL COMMENT '手续费' AFTER `source_fee`;

ALTER TABLE `my_wish` ADD INDEX(`publish_user_id`);

ALTER TABLE `my_wish` ADD INDEX(`hot_num`);

ALTER TABLE `my_red_packets` ADD `over_pic` VARCHAR(200) NULL COMMENT '过期图片' AFTER `pic`;

ALTER TABLE `my_personal_red_packets` ADD `over_pic` VARCHAR(200) NULL COMMENT '过期图片' AFTER `pic`;

ALTER TABLE `my_personal_red_packets` CHANGE `pic` `pic` VARCHAR(200) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '红包图标';

ALTER TABLE `my_wish` ADD INDEX(`wish_name`);

ALTER TABLE `my_account_info` ADD INDEX(`nick_name`);

ALTER TABLE `my_update_content` CHANGE `remark4` `remark4` TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL;

ALTER TABLE `my_update_content` ADD UNIQUE(`module_id`);

ALTER TABLE `my_report_list` ADD INDEX(`status`);


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
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='权限表' AUTO_INCREMENT=1 ;


ALTER TABLE `my_menu`
 ADD PRIMARY KEY (`menu_id`), ADD UNIQUE KEY `url` (`url`), ADD KEY `parent_id` (`parent_id`,`order_no`);

ALTER TABLE `my_menu`
MODIFY `menu_id` int(11) NOT NULL AUTO_INCREMENT;

CREATE TABLE IF NOT EXISTS `my_user_menu` (
`user_menu_id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL COMMENT '用户id',
  `menu_id` int(11) DEFAULT NULL COMMENT '权限id',
  `remark1` varchar(100) DEFAULT NULL,
  `remark2` varchar(100) DEFAULT NULL,
  `remark3` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='用户权限表' AUTO_INCREMENT=1 ;


ALTER TABLE `my_user_menu`
 ADD PRIMARY KEY (`user_menu_id`), ADD KEY `user_id` (`user_id`);

ALTER TABLE `my_user_menu`
MODIFY `user_menu_id` int(11) NOT NULL AUTO_INCREMENT;

delimiter //
CREATE FUNCTION `getChildLst`(rootId INT)
RETURNS varchar(3000)
BEGIN
DECLARE sTemp VARCHAR(3000);
DECLARE sTempChd VARCHAR(3000);
SET sTemp = '';
SET sTempChd =cast(rootId as CHAR);
WHILE sTempChd is not null DO
SET sTemp = concat(sTemp,',',sTempChd);
SELECT group_concat(menu_id) INTO sTempChd FROM my_menu where FIND_IN_SET(parent_id,sTempChd)>0;
END WHILE;
RETURN SUBSTR(sTemp,2);
END//

ALTER TABLE `my_user_weixin` CHANGE `nick_name` `nick_name` VARCHAR(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '微信昵称';


CREATE TABLE IF NOT EXISTS `my_chat_group` (
`chat_group_id` int(11) NOT NULL,
  `group_name` varchar(100) DEFAULT NULL COMMENT '群名称',
  `group_master_id` int(11) DEFAULT NULL COMMENT '群组id',
  `group_member_count` int(11) DEFAULT NULL COMMENT '组员数量',
  `icon` varchar(200) DEFAULT NULL COMMENT '群图标',
  `create_time` datetime DEFAULT NULL COMMENT '创建时间',
  `status` int(11) DEFAULT NULL COMMENT '状态',
  `remark1` varchar(100) DEFAULT NULL,
  `remark2` varchar(100) DEFAULT NULL,
  `remark3` varchar(100) DEFAULT NULL,
  `remark4` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='群组信息' AUTO_INCREMENT=1 ;


ALTER TABLE `my_chat_group`
 ADD PRIMARY KEY (`chat_group_id`), ADD KEY `group_name` (`group_name`,`group_master_id`);

ALTER TABLE `my_chat_group`
MODIFY `chat_group_id` int(11) NOT NULL AUTO_INCREMENT;

CREATE TABLE IF NOT EXISTS `my_chat_personal_group` (
`chat_personal_group_id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `group_id` int(11) DEFAULT NULL,
  `remark1` varchar(100) DEFAULT NULL,
  `remark2` varchar(100) DEFAULT NULL,
  `remark3` varchar(100) DEFAULT NULL,
  `remark4` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='群组个人关系表' AUTO_INCREMENT=1 ;


ALTER TABLE `my_chat_personal_group`
 ADD PRIMARY KEY (`chat_personal_group_id`), ADD KEY `group_id` (`group_id`);

ALTER TABLE `my_chat_personal_group`
MODIFY `chat_personal_group_id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `my_chat_group` ADD `public` INT NULL COMMENT '是否公开' AFTER `status`, ADD `approval` INT NULL COMMENT '加入是否要批准' AFTER `public`;

ALTER TABLE `my_chat_group` ADD `describtion` TEXT NULL COMMENT '群描述' AFTER `approval`;

INSERT INTO `my_system_params` (`system_params_id`, `group_id`, `code`, `discribtion`, `value1`, `value2`, `value3`, `remark1`, `remark2`, `remark3`, `remark4`, `title`) VALUES
(32, 5, 'statistic_day_users_date', '统计日新增人数开始统计日期，统计到当天前一天结束', '2016-02-01', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(33, 5, 'statistic_day_users_month', '统计月份开始月份，2016-02；统计到当前月前一个月为止', '2016-02', NULL, NULL, NULL, NULL, NULL, NULL, NULL);

ALTER TABLE `my_chat_group` ADD `other_id` VARCHAR(100) NULL COMMENT '第三方群id' AFTER `describtion`, ADD UNIQUE (`other_id`) ;


ALTER TABLE `my_chat_personal_group` ADD `is_owner` INT NULL COMMENT '是否群主 1 是 2 否' AFTER `group_id`;

ALTER TABLE `my_chat_group` ADD `group_type` INT NULL COMMENT '1 愿望群 2 普通群' AFTER `other_id`;

ALTER TABLE `my_chat_group` ADD `wish_id` INT NULL COMMENT '愿望id' AFTER `group_type`, ADD INDEX (`wish_id`) ;

INSERT INTO `meiyuan`.`my_user_menu` (`user_menu_id`, `user_id`, `menu_id`, `remark1`, `remark2`, `remark3`) VALUES (NULL, '1', '23', NULL, NULL, NULL);
INSERT INTO `meiyuan`.`my_user_menu` (`user_menu_id`, `user_id`, `menu_id`, `remark1`, `remark2`, `remark3`) VALUES (NULL, '1', '24', NULL, NULL, NULL);
INSERT INTO `meiyuan`.`my_user_menu` (`user_menu_id`, `user_id`, `menu_id`, `remark1`, `remark2`, `remark3`) VALUES (NULL, '1', '25', NULL, NULL, NULL);


CREATE TABLE IF NOT EXISTS `my_user_daily_statistic` (
`daily_statistic_id` int(11) NOT NULL COMMENT '主键自增',
  `user_day_num` varchar(100) DEFAULT NULL COMMENT '用户统计',
  `data_time` date DEFAULT NULL COMMENT '统计日期',
  `remark1` varchar(100) DEFAULT NULL COMMENT '备用字段',
  `remark2` varchar(100) DEFAULT NULL COMMENT '备用字段',
  `remark3` varchar(100) DEFAULT NULL COMMENT '备用字段',
  `remark4` varchar(100) DEFAULT NULL COMMENT '备用字段'
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='日用户数量统计表' AUTO_INCREMENT=33 ;

ALTER TABLE `my_user_daily_statistic`
 ADD PRIMARY KEY (`daily_statistic_id`);

ALTER TABLE `my_user_daily_statistic`
MODIFY `daily_statistic_id` int(11) NOT NULL AUTO_INCREMENT COMMENT '主键自增',AUTO_INCREMENT=33;


CREATE TABLE IF NOT EXISTS `my_user_month_statistic` (
`month_statistic_id` int(11) NOT NULL COMMENT '主键自增',
  `user_month_num` varchar(100) DEFAULT NULL COMMENT '用户统计',
  `data_time` varchar(100) DEFAULT NULL COMMENT '统计日期',
  `remark1` varchar(100) DEFAULT NULL COMMENT '备用字段',
  `remark2` varchar(100) DEFAULT NULL COMMENT '备用字段',
  `remark3` varchar(100) DEFAULT NULL COMMENT '备用字段',
  `remark4` varchar(100) DEFAULT NULL COMMENT '备用字段'
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='月用户数量统计表' AUTO_INCREMENT=2 ;


ALTER TABLE `my_user_month_statistic`
 ADD PRIMARY KEY (`month_statistic_id`);

ALTER TABLE `my_user_month_statistic`
MODIFY `month_statistic_id` int(11) NOT NULL AUTO_INCREMENT COMMENT '主键自增',AUTO_INCREMENT=2;

CREATE TABLE IF NOT EXISTS `my_wish_new_statistic` (
`wish_new_id` int(11) NOT NULL,
  `wish_id` int(11) DEFAULT NULL COMMENT '愿望id',
  `modify_time` datetime DEFAULT NULL COMMENT '修改时间',
  `order_no` int(11) DEFAULT NULL COMMENT '人为干预排序号，默认1000',
  `remark1` varchar(100) DEFAULT NULL,
  `remark2` varchar(100) DEFAULT NULL,
  `remark3` varchar(100) DEFAULT NULL,
  `remark4` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='愿望动态信息表' AUTO_INCREMENT=1 ;


ALTER TABLE `my_wish_new_statistic`
 ADD PRIMARY KEY (`wish_new_id`), ADD UNIQUE KEY `wish_id` (`wish_id`), ADD KEY `modify_time` (`modify_time`,`order_no`);

ALTER TABLE `my_wish_new_statistic`
MODIFY `wish_new_id` int(11) NOT NULL AUTO_INCREMENT;

CREATE TABLE IF NOT EXISTS `my_personal_new_statistic` (
`personal_new_id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `modify_time` datetime DEFAULT NULL,
  `content` text,
  `relate_user_id` int(11) DEFAULT NULL,
  `remark1` varchar(100) DEFAULT NULL,
  `remark2` varchar(100) DEFAULT NULL,
  `remark3` varchar(100) DEFAULT NULL,
  `remark4` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='个人动态信息表' AUTO_INCREMENT=1 ;


ALTER TABLE `my_personal_new_statistic`
 ADD PRIMARY KEY (`personal_new_id`), ADD UNIQUE KEY `user_id` (`user_id`), ADD KEY `modify_time` (`modify_time`);


ALTER TABLE `my_personal_new_statistic`
MODIFY `personal_new_id` int(11) NOT NULL AUTO_INCREMENT;

INSERT INTO `my_menu` ( `title`, `icon`, `url`, `visible`, `parent_id`, `order_no`, `status`, `remark1`, `remark2`, `remrak3`, `remark4`, `remark5`, `remark6`) VALUES
('最新愿望排序管理', 'fa fa-outdent', 'wishmanage/wishnewestorder', 1, 0, '0006', 1, NULL, NULL, NULL, NULL, NULL, NULL);

UPDATE `my_menu` SET `url` = '4#' WHERE `menu_id` = 2;

INSERT INTO `my_menu`(`title`,`icon`,`url`,`visible`,`parent_id`,`order_no`,`status`,`remark1`,`remark2`,`remark3`,`remark4`,`remark5`,`remark6`) VALUES
('其他审核管理','fa fa-check-circle-o','auditmanage/index',1,2,'0021',1,NULL,NULL,NULL,NULL,NULL,NULL);

INSERT INTO `my_menu`(`title`,`icon`,`url`,`visible`,`parent_id`,`order_no`,`status`,`remark1`,`remark2`,`remark3`,`remark4`,`remark5`,`remark6`) VALUES
('余额提现审核管理','fa fa-check-circle-o','auditmanage/management',1,2,'0022',1,NULL,NULL,NULL,NULL,NULL,NULL);



insert into `my_wish_new_statistic`(wish_id,modify_time,order_no) select wish_id,create_time,1000 from my_wish

insert into `my_personal_new_statistic` (user_id) select account_id from my_account_info

ALTER TABLE `my_personal_new_statistic` ADD `new_type` INT NULL COMMENT '消息类型 1 打赏 2 评论 3 发布愿望' AFTER `relate_user_id`;

ALTER TABLE `my_personal_new_statistic` ADD `talk_to_user_id` INT NULL COMMENT '回复人id' AFTER `new_type`;

CREATE TABLE IF NOT EXISTS `my_user_visitors_statistic` (
`visitors_statistic_id` int(11) NOT NULL COMMENT '主键自增',
  `visitors_num` int(11) DEFAULT NULL COMMENT '认证统计',
  `visitors_date` varchar(100) DEFAULT NULL COMMENT '认证日期',
  `remark1` varchar(100) DEFAULT NULL COMMENT '备用字段',
  `remark2` varchar(100) DEFAULT NULL COMMENT '备用字段',
  `remark3` varchar(100) DEFAULT NULL COMMENT '备用字段',
  `remark4` varchar(100) DEFAULT NULL COMMENT '备用字段'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='认证用户统计表' AUTO_INCREMENT=1 ;

ALTER TABLE `my_user_visitors_statistic`
 ADD PRIMARY KEY (`visitors_statistic_id`);

ALTER TABLE `my_user_visitors_statistic`
MODIFY `visitors_statistic_id` int(11) NOT NULL AUTO_INCREMENT COMMENT '主键自增';

INSERT INTO `meiyuan`.`my_system_params` ( `group_id`, `code`, `discribtion`, `value1`, `value2`, `value3`, `remark1`, `remark2`, `remark3`, `remark4`, `title`) VALUES
('5','statistic_day_users_Verify', '统计认证人数开始统计日期，统计到当天前一天结束', '2016-02-01 00:00:00','2016-02-01 00:00:00', NULL, NULL, NULL, NULL, NULL, NULL, NULL);

INSERT INTO `my_system_params` (`group_id`, `code`, `discribtion`, `value1`, `value2`, `value3`, `remark1`, `remark2`, `remark3`, `remark4`, `title`) VALUES
(5, 'statistic_month_users_verify', '统计认证开始月份，2016-02；统计到当前月前一个月为止', '2016-02', '2016-02', NULL, NULL, NULL, NULL, NULL, NULL);

INSERT INTO `mydb`.`my_system_params` (`system_params_id`, `group_id`, `code`, `discribtion`, `value1`, `value2`, `value3`, `remark1`, `remark2`, `remark3`, `remark4`, `title`) VALUES (NULL, '1', 'system_reward_note', '可获得1倍奖金，5元封顶', '可获得1倍奖金，5元封顶', NULL, NULL, NULL, NULL, NULL, NULL, NULL);

INSERT INTO `meiyuan`.`my_system_params` (`group_id`, `code`, `discribtion`, `value1`, `value2`, `value3`, `remark1`, `remark2`, `remark3`, `remark4`, `title`) VALUES ('5', 'statistic_day_users_Verify', '统计认证人数开始统计日期，统计到当天前一天结束', '2016-02-01 00:00:00', NULL, NULL, NULL, NULL, NULL, NULL, NULL);

ALTER TABLE `my_chat_personal_group` ADD `chat_pic` VARCHAR(200) NULL COMMENT '聊天背景图链接' , ADD `hide_msg` INT NULL COMMENT '免打扰 1 否 2 是 免打扰后不会提示消息' ;

ALTER TABLE `my_chat_personal_group` ADD `nick_name` VARCHAR(100) NULL COMMENT '群中的昵称' ;

ALTER TABLE `my_friends_list` ADD `chat_pic` VARCHAR(100) NOT NULL COMMENT '聊天背景图链接' , ADD `hide_msg` INT NOT NULL COMMENT '免打扰 1 否 2 是 免打扰后不会提示消息' , ADD `nick_name` VARCHAR(100) NOT NULL COMMENT '好友备注昵称' ;

ALTER TABLE `my_friends_list` CHANGE `chat_pic` `chat_pic` VARCHAR(100) CHARACTER SET utf8 COLLATE utf8_general_ci NULL COMMENT '聊天背景图链接', CHANGE `hide_msg` `hide_msg` INT(11) NULL COMMENT '免打扰 1 否 2 是 免打扰后不会提示消息', CHANGE `nick_name` `nick_name` VARCHAR(100) CHARACTER SET utf8 COLLATE utf8_general_ci NULL COMMENT '好友备注昵称';

update `my_friends_list` set chat_pic = null,hide_msg=1,nick_name=null;

ALTER TABLE `my_account_info` ADD INDEX(`device_no`);

ALTER TABLE `my_account_info` ADD INDEX(`phone_no`);

CREATE TABLE IF NOT EXISTS `my_month_visitors_statistic` (
`month_visitors_id` int(11) NOT NULL COMMENT '自增主键',
  `month_visitors_num` varchar(100) DEFAULT NULL COMMENT '月认证用户数',
  `month_visitors_time` varchar(100) DEFAULT NULL COMMENT '月认证时间',
  `remark1` varchar(100) DEFAULT NULL,
  `remark2` varchar(100) DEFAULT NULL,
  `remark3` varchar(100) DEFAULT NULL,
  `remark4` varchar(100) DEFAULT NULL
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='每月认证用户统计表';

ALTER TABLE `my_month_visitors_statistic`
 ADD PRIMARY KEY (`month_visitors_id`);

ALTER TABLE `my_month_visitors_statistic`
MODIFY `month_visitors_id` int(11) NOT NULL AUTO_INCREMENT COMMENT '自增主键';

ALTER TABLE `my_get_cash` ADD `first_get_money` VARCHAR(100) NULL COMMENT '是否首次提现 默认为 1 提现为 2 ' ;

ALTER TABLE `my_chat_group` ADD `chat_pic` VARCHAR(100)  NULL COMMENT '群默认聊天背景图' AFTER `wish_id`;

insert into my_hot_order_extend(wish_id,order_no) select wish_id,1000 from my_wish;

INSERT INTO `my_menu` (`title`, `icon`, `url`, `visible`, `parent_id`, `order_no`, `status`, `remark1`, `remark2`, `remark3`, `remark4`, `remark5`, `remark6`) VALUES
('排行版愿望管理', 'fa fa-outdent', 'wishmanage/wishhotorder', 1, 0, '0006', 1, NULL, NULL, NULL, NULL, NULL, NULL);

INSERT INTO `my_user_menu` (`user_id`, `menu_id`, `remark1`, `remark2`, `remark3`) VALUES ('1', '26', NULL, NULL, NULL);

INSERT INTO `my_menu` (`title`, `icon`, `url`, `visible`, `parent_id`, `order_no`, `status`, `remark1`, `remark2`, `remark3`, `remark4`, `remark5`, `remark6`) VALUES
('愿望提现审核管理', 'fa fa-check-circle-o', 'auditmanage/wishmoneytobalance', 1, 2, '0023', 1, NULL, NULL, NULL, NULL, NULL, NULL);

insert into my_user_menu (user_id,menu_id)values(1,27)

ALTER TABLE my_wish DROP INDEX publish_user_phone;

ALTER TABLE `my_wish` ADD INDEX(`ready_reward_money`);
ALTER TABLE `my_wish` ADD INDEX(`red_packets_money`);

ALTER TABLE `my_wish` ADD INDEX(`finish_status`);
ALTER TABLE `my_wish` ADD INDEX(`status`);

ALTER TABLE `my_base_centification` ADD UNIQUE(`user_id`);

CREATE TABLE IF NOT EXISTS `my_wish_for_first_reward` (
  `first_reward_id` int(11) NOT NULL,
  `wish_id` int(11) DEFAULT NULL COMMENT '愿望id',
  `packet_for_wish` int(11) DEFAULT NULL COMMENT '首次打赏，被打赏愿望获取的红包',
  `packet_for_reward` int(11) DEFAULT NULL COMMENT '首次打赏，打赏人奖励红包',
  `remark1` varchar(100) DEFAULT NULL,
  `remark2` varchar(100) DEFAULT NULL,
  `remark3` varchar(100) DEFAULT NULL,
  `remark4` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='愿望首次打赏扩展表';


ALTER TABLE `my_wish_for_first_reward`
 ADD PRIMARY KEY (`first_reward_id`), ADD UNIQUE KEY `wish_id` (`wish_id`);

insert into my_wish_for_first_reward(`wish_id`, `packet_for_wish`, `packet_for_reward`)values(-1,null,null);


ALTER TABLE `my_report_list` ADD `scene` INT NULL COMMENT '1 愿望 2 群 3 好友' AFTER `nick_name`;

update `my_report_list` set scene = 1 where scene is null

CREATE TABLE IF NOT EXISTS `my_user_balance_log` (
`balance_log_id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL COMMENT '用户id',
  `user_account_id` int(11) DEFAULT NULL COMMENT '用户账号id',
  `operate_type` int(11) DEFAULT NULL COMMENT '操作类型 1 充值  2 打赏  3 提现  4修改金额',
  `op_money` decimal(12,2) DEFAULT NULL COMMENT '交易金额',
  `before_balance` decimal(12,2) DEFAULT NULL COMMENT '操作前余额',
  `after_balance` decimal(12,2) DEFAULT NULL COMMENT '操作后金额',
  `create_time` datetime DEFAULT NULL COMMENT '操作时间',
  `unique_op_id` varchar(100) DEFAULT NULL COMMENT '唯一操作号',
  `remark1` varchar(100) DEFAULT NULL COMMENT '备用字段',
  `remark2` varchar(100) DEFAULT NULL,
  `remark3` varchar(100) DEFAULT NULL,
  `remark4` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='用户账户日志表' AUTO_INCREMENT=1 ;


ALTER TABLE `my_user_balance_log` ADD INDEX(`user_id`);

ALTER TABLE `my_user_balance_log` ADD INDEX(`user_account_id`);

ALTER TABLE `my_user_balance_log` ADD INDEX(`operate_type`);

ALTER TABLE `my_user_balance_log` ADD INDEX(`create_time`);

ALTER TABLE `my_user_balance_log` ADD UNIQUE(`unique_op_id`);

INSERT INTO `meiyuan`.`my_system_params` ( `group_id`, `code`, `discribtion`, `value1`, `value2`, `value3`, `title`) VALUES ( '1', 'system_pay', '支付方式图标', '支付方式图标', 'http://image.matewish.cn/pay_icon/pay.png', NULL, NULL);


