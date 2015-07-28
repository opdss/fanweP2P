CREATE TABLE `fanwe_yeepay_log` (
  `id` int(10) NOT NULL auto_increment,
  `code` varchar(50) NOT NULL,
  `create_date` datetime NOT NULL,
  `strxml` text,
  `html` text,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=71034 DEFAULT CHARSET=utf8;

CREATE TABLE `fanwe_yeepay_recharge` (
  `id` int(10) NOT NULL auto_increment,
  `requestNo` int(10) NOT NULL default '0' COMMENT 'yeepay_log.id',
  `platformUserNo` int(11) NOT NULL default '0' COMMENT 'fanwe_user.id',
  `platformNo` varchar(20) NOT NULL,
  `amount` decimal(20,2) NOT NULL default '0.00' COMMENT '充值金额',
  `feeMode` varchar(50) NOT NULL default 'PLATFORM' COMMENT '费率模式PLATFORM',
  `is_callback` tinyint(1) NOT NULL default '0',
  `bizType` varchar(50) default NULL COMMENT '业务名称',
  `code` varchar(50) default NULL COMMENT '返回码;1 成功 0 失败 2 xml参数格式错误 3 签名验证失败 101 引用了不存在的对象（例如错误的订单号） 102 业务状态不正确 103 由于业务限制导致业务不能执行 104 实名认证失败',
  `message` varchar(255) default NULL COMMENT '描述异常信息',
  `description` varchar(255) default NULL,
  PRIMARY KEY  (`id`,`requestNo`)
) ENGINE=MyISAM AUTO_INCREMENT=29 DEFAULT CHARSET=utf8;

CREATE TABLE `fanwe_yeepay_register` (
  `id` int(10) NOT NULL auto_increment,
  `requestNo` int(10) NOT NULL default '0' COMMENT 'yeepay_log.id',
  `platformUserNo` int(11) default '0' COMMENT 'fanwe_user.id',
  `platformNo` varchar(20) default NULL,
  `nickName` varchar(50) default NULL,
  `realName` varchar(50) default NULL,
  `idCardNo` varchar(50) default NULL,
  `idCardType` varchar(50) default NULL,
  `mobile` varchar(20) default NULL,
  `email` varchar(50) default NULL,
  `is_callback` tinyint(1) NOT NULL default '0',
  `bizType` varchar(50) default NULL COMMENT '业务名称',
  `code` varchar(50) default NULL COMMENT '返回码;1 成功 0 失败 2 xml参数格式错误 3 签名验证失败 101 引用了不存在的对象（例如错误的订单号） 102 业务状态不正确 103 由于业务限制导致业务不能执行 104 实名认证失败',
  `message` varchar(255) default NULL COMMENT '描述异常信息',
  `description` varchar(255) default NULL,
  PRIMARY KEY  (`id`,`requestNo`)
) ENGINE=MyISAM AUTO_INCREMENT=26 DEFAULT CHARSET=utf8;

CREATE TABLE `fanwe_yeepay_withdraw` (
  `id` int(10) NOT NULL auto_increment,
  `requestNo` int(10) NOT NULL default '0' COMMENT 'yeepay_log.id',
  `platformUserNo` int(11) NOT NULL default '0' COMMENT 'fanwe_user.id',
  `platformNo` varchar(20) NOT NULL,
  `amount` decimal(20,2) NOT NULL default '0.00' COMMENT '充值金额',
  `feeMode` varchar(50) NOT NULL default '' COMMENT 'PLATFORM 收取商户手续费 USER 收取用户手续费',
  `is_callback` tinyint(1) NOT NULL default '0',
  `bizType` varchar(50) default NULL COMMENT '业务名称',
  `code` varchar(50) default NULL COMMENT '返回码;1 成功 0 失败 2 xml参数格式错误 3 签名验证失败 101 引用了不存在的对象（例如错误的订单号） 102 业务状态不正确 103 由于业务限制导致业务不能执行 104 实名认证失败',
  `message` varchar(255) default NULL COMMENT '描述异常信息',
  `description` varchar(255) default NULL,
  `cardNo` varchar(50) default NULL COMMENT '绑定的卡号',
  `bank` varchar(20) default NULL COMMENT '卡的开户行',
  PRIMARY KEY  (`id`,`requestNo`)
) ENGINE=MyISAM AUTO_INCREMENT=36 DEFAULT CHARSET=utf8;

CREATE TABLE `fanwe_yeepay_cp_transaction` (
  `id` int(10) NOT NULL auto_increment,
  `requestNo` int(10) NOT NULL default '0' COMMENT 'yeepay_log.id',
  `platformNo` varchar(20) NOT NULL,
  `platformUserNo` int(11) NOT NULL default '0' COMMENT 'fanwe_user.id',
  `userType` varchar(20) NOT NULL default 'MEMBER' COMMENT '出款人用户类型，目前只支持传入 MEMBER\r\nMEMBER 个人会员 MERCHANT 商户 ',
  `bizType` varchar(50) NOT NULL COMMENT 'TENDER 投标 REPAYMENT 还款 CREDIT_ASSIGNMENT 债权转让 TRANSFER 转账 COMMISSION 分润，仅在资金转账明细中使用',
  `expired` datetime NOT NULL default '0000-00-00 00:00:00' COMMENT '超过此时间即不允许提交订单',
  `tenderOrderNo` int(11) default '0' COMMENT '项目编号',
  `tenderName` varchar(255) default NULL COMMENT '项目名称 ',
  `tenderAmount` decimal(20,2) default NULL COMMENT '项目金额',
  `tenderDescription` varchar(255) default NULL COMMENT '项目描述信息',
  `borrowerPlatformUserNo` int(11) default NULL COMMENT '项目的借款人平台用户编号',
  `originalRequestNo` int(11) default NULL COMMENT '需要转让的投资记录流水号',
  `details` text COMMENT '资金明细记录',
  `extend` text COMMENT '业务扩展属性，根据业务类型的不同，需要传入不同的参数。',
  `transfer_id` int(11) NOT NULL default '0' COMMENT '债权转让id fanwe_deal_load_transfer.id',
  `is_callback` tinyint(1) NOT NULL default '0',
  `is_complete_transaction` tinyint(1) NOT NULL default '0' COMMENT 'is_callback=1时，才生效;判断是否已经完成转帐',
  `code` varchar(50) default NULL COMMENT '返回码;1 成功 0 失败 2 xml参数格式错误 3 签名验证失败 101 引用了不存在的对象（例如错误的订单号） 102 业务状态不正确 103 由于业务限制导致业务不能执行 104 实名认证失败',
  `message` varchar(255) default NULL COMMENT '描述异常信息',
  `description` varchar(255) default NULL,
  PRIMARY KEY  (`id`,`requestNo`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `fanwe_yeepay_cp_transaction_detail` (
  `id` int(10) NOT NULL auto_increment,
  `pid` int(10) NOT NULL default '0' COMMENT 'fanwe_yeepay_repayment.id',
  `deal_load_repay_id` int(11) NOT NULL default '0' COMMENT '用户回款计划表',
  `targetUserType` int(11) NOT NULL default '0' COMMENT '用户类型',
  `targetPlatformUserNo` int(11) NOT NULL default '0' COMMENT '平台用户编号',
  `amount` decimal(20,2) NOT NULL default '0.00' COMMENT '转入金额',
  `bizType` varchar(20) NOT NULL default '' COMMENT '资金明细业务类型。根据业务的不同，需要传入不同的值，见【业务类型',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=29 DEFAULT CHARSET=utf8;

CREATE TABLE `fanwe_yeepay_enterprise_register` (
  `id` int(10) NOT NULL auto_increment,
  `requestNo` int(10) NOT NULL default '0' COMMENT 'yeepay_log.id',
  `platformUserNo` int(11) default '0' COMMENT 'fanwe_user.id',
  `platformNo` varchar(20) default NULL,
  `enterpriseName` varchar(50) default NULL COMMENT '企业名称',
  `bankLicense` varchar(50) default NULL COMMENT '开户银行许可证',
  `orgNo` varchar(50) default NULL COMMENT '组织机构代码',
  `businessLicense` varchar(50) default NULL COMMENT '营业执照编号',
  `taxNo` varchar(20) default NULL COMMENT '税务登记号',
  `legal` varchar(50) default NULL COMMENT '法人姓名',
  `legalIdNo` varchar(20) default NULL COMMENT '法人身份证号',
  `contact` varchar(20) default NULL COMMENT '企业联系人',
  `contactPhone` varchar(20) default NULL COMMENT '联系人手机号',
  `email` varchar(50) default NULL COMMENT '联系人邮箱',
  `memberClassType` varchar(255) default NULL COMMENT '会员类型ENTERPRISE：企业借款人;GUARANTEE_CORP：担保公司',
  `is_callback` tinyint(1) NOT NULL default '0',
  `bizType` varchar(50) default NULL COMMENT '业务名称',
  `code` varchar(50) default NULL COMMENT '返回码;1 成功 0 失败 2 xml参数格式错误 3 签名验证失败 101 引用了不存在的对象（例如错误的订单号） 102 业务状态不正确 103 由于业务限制导致业务不能执行 104 实名认证失败',
  `message` varchar(255) default NULL COMMENT '描述异常信息',
  `description` varchar(255) default NULL,
  PRIMARY KEY  (`id`,`requestNo`)
) ENGINE=MyISAM AUTO_INCREMENT=26 DEFAULT CHARSET=utf8;

CREATE TABLE `fanwe_yeepay_bind_bank_card` (
  `id` int(10) NOT NULL auto_increment,
  `requestNo` int(10) NOT NULL default '0' COMMENT 'yeepay_log.id',
  `platformUserNo` int(11) NOT NULL default '0' COMMENT 'fanwe_user.id',
  `platformNo` varchar(20) NOT NULL,
  `bankCardNo` varchar(50) NOT NULL default '' COMMENT '绑定的卡号',
  `bank` varchar(20) NOT NULL default '' COMMENT '卡的开户行',
  `cardStatus` varchar(20) NOT NULL COMMENT '卡的状态VERIFYING 认证中 VERIFIED 已认证',
  `is_callback` tinyint(1) NOT NULL default '0',
  `bizType` varchar(50) default NULL COMMENT '业务名称',
  `code` varchar(50) default NULL COMMENT '返回码;1 成功 0 失败 2 xml参数格式错误 3 签名验证失败 101 引用了不存在的对象（例如错误的订单号） 102 业务状态不正确 103 由于业务限制导致业务不能执行 104 实名认证失败',
  `message` varchar(255) default NULL COMMENT '描述异常信息',
  `description` varchar(255) default NULL,
  PRIMARY KEY  (`id`,`requestNo`)
) ENGINE=MyISAM AUTO_INCREMENT=30 DEFAULT CHARSET=utf8;

