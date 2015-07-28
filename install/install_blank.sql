/*
Navicat MySQL Data Transfer

Source Server         : localhost_3306
Source Server Version : 50045
Source Host           : localhost:3306
Source Database       : v33

Target Server Type    : MYSQL
Target Server Version : 50045
File Encoding         : 65001

Date: 2015-04-03 18:20:19
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for `%DB_PREFIX%admin`
-- ----------------------------
DROP TABLE IF EXISTS `%DB_PREFIX%admin`;
CREATE TABLE `%DB_PREFIX%admin` (
  `id` int(11) NOT NULL auto_increment,
  `adm_name` varchar(255) NOT NULL COMMENT '管理员用户名',
  `adm_password` varchar(255) NOT NULL COMMENT '管理员密码',
  `is_effect` tinyint(1) NOT NULL COMMENT '有效性控制',
  `is_delete` tinyint(1) NOT NULL COMMENT '删除标识',
  `role_id` int(11) NOT NULL COMMENT '角色ID(权限控制用)',
  `login_time` int(11) NOT NULL COMMENT '最后登录时间',
  `login_ip` varchar(255) NOT NULL COMMENT '最后登录IP',
  `is_department` tinyint(1) NOT NULL COMMENT '用户类型   0：管理员，1：部门',
  `pid` int(11) NOT NULL COMMENT '所属部门编号',
  `work_id` varchar(255) default NULL COMMENT '员工编号',
  PRIMARY KEY  (`id`),
  UNIQUE KEY `unique_adm_name` (`adm_name`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of fanwe_admin
-- ----------------------------
INSERT INTO `%DB_PREFIX%admin` VALUES ('1', 'admin', '21232f297a57a5a743894a0e4a801fc3', '1', '0', '4', '1415812947', '127.0.0.1', '0', '0', null);

-- ----------------------------
-- Table structure for `%DB_PREFIX%admin_carry`
-- ----------------------------
DROP TABLE IF EXISTS `%DB_PREFIX%admin_carry`;
CREATE TABLE `%DB_PREFIX%admin_carry` (
  `id` int(11) NOT NULL auto_increment,
  `admin_id` int(11) NOT NULL COMMENT '管理ID',
  `admin_name` varchar(50) NOT NULL COMMENT '管理员名称',
  `money` decimal(20,2) NOT NULL COMMENT '提现金额',
  `memo` text NOT NULL COMMENT '操作备注',
  `create_time` int(11) NOT NULL COMMENT '操作时间',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of fanwe_admin_carry
-- ----------------------------

-- ----------------------------
-- Table structure for `%DB_PREFIX%adv`
-- ----------------------------
DROP TABLE IF EXISTS `%DB_PREFIX%adv`;
CREATE TABLE `%DB_PREFIX%adv` (
  `id` int(11) NOT NULL auto_increment,
  `tmpl` varchar(255) NOT NULL COMMENT '前台使用模板名称',
  `adv_id` varchar(255) NOT NULL COMMENT '定义在模板文件里的广告位的ID名称，用于动态在模板上调用相应的广告位内容',
  `code` text NOT NULL COMMENT '用于前台展示显示的html广告内容',
  `is_effect` tinyint(1) NOT NULL COMMENT '有效性控制标识',
  `name` varchar(255) NOT NULL COMMENT '广告位名称，用于后台管理查询用',
  `city_ids` varchar(255) NOT NULL COMMENT '用于控制广告显示在哪些城市，填入城市ID,用半角逗号分隔',
  `rel_id` int(11) NOT NULL COMMENT '用于动态关联的广告定义，例如首页显示多个商品分类模块，每个分类模块下需要定义一个独立的广告，这种广告一般在商品分类，生活服务分类中单独设置，这里的rel_id指向相关的分类ID',
  `rel_table` varchar(255) NOT NULL COMMENT '同rel_id，这里填的是相关的表名，例如商城分类的推荐广告，这里填入shop_cate',
  PRIMARY KEY  (`id`),
  KEY `tmpl` (`tmpl`),
  KEY `adv_id` (`adv_id`),
  KEY `city_ids` (`city_ids`),
  KEY `rel_id` (`rel_id`),
  KEY `rel_table` (`rel_table`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of fanwe_adv
-- ----------------------------
INSERT INTO `%DB_PREFIX%adv` VALUES ('1', 'blue', '首页广告位1', '<img src=\"./public/attachment/201410/10/16/54379e5b77e58.jpg\" alt=\"\" />', '1', '广告位1', '', '0', '');
INSERT INTO `%DB_PREFIX%adv` VALUES ('2', 'blue', '首页广告位2', '<img src=\"./public/attachment/201410/10/16/54379eb932938.jpg\" alt=\"\" />', '1', '广告位2', '', '0', '');

-- ----------------------------
-- Table structure for `%DB_PREFIX%api_login`
-- ----------------------------
DROP TABLE IF EXISTS `%DB_PREFIX%api_login`;
CREATE TABLE `%DB_PREFIX%api_login` (
  `id` int(11) NOT NULL auto_increment,
  `name` varchar(255) NOT NULL COMMENT '第三方登录名称',
  `config` text NOT NULL COMMENT ' 序列化后的配置信息',
  `class_name` varchar(255) NOT NULL COMMENT '接口类名',
  `icon` varchar(255) NOT NULL COMMENT '登录用小图标显示',
  `bicon` varchar(255) NOT NULL COMMENT '登录用大图标显示',
  `is_weibo` tinyint(1) NOT NULL COMMENT '是否微博接口，该接口标识可以同步信息到第三方的微博平台',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=13 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of fanwe_api_login
-- ----------------------------
INSERT INTO `%DB_PREFIX%api_login` VALUES ('9', '新浪api登录接口', 'a:2:{s:7:\"app_key\";s:9:\"929562715\";s:10:\"app_secret\";s:32:\"0f59b4e39c900c6f7d827c201db12349\";}', 'Sina', './public/attachment/201302/04/14/510f59403870b.gif', './public/attachment/201203/17/15/4f64396822524.png', '1');
INSERT INTO `%DB_PREFIX%api_login` VALUES ('10', '腾讯微博登录插件', 'a:2:{s:7:\"app_key\";s:0:\"\";s:10:\"app_secret\";s:0:\"\";}', 'Tencent', './public/attachment/201302/04/14/510f590f950a7.gif', './public/attachment/201203/17/15/4f643977758ee.png', '1');

-- ----------------------------
-- Table structure for `%DB_PREFIX%article`
-- ----------------------------
DROP TABLE IF EXISTS `%DB_PREFIX%article`;
CREATE TABLE `%DB_PREFIX%article` (
  `id` int(11) NOT NULL auto_increment,
  `title` varchar(255) NOT NULL COMMENT '文章标题',
  `icon` varchar(255) NOT NULL COMMENT '图标',
  `content` longtext NOT NULL COMMENT ' 文章内容',
  `cate_id` int(11) NOT NULL COMMENT '文章分类ID',
  `create_time` int(11) NOT NULL COMMENT '发表时间',
  `update_time` int(11) NOT NULL COMMENT '更新时间',
  `add_admin_id` int(11) NOT NULL COMMENT '发布人(管理员ID)',
  `is_effect` tinyint(4) NOT NULL COMMENT '有效性标识',
  `rel_url` varchar(255) NOT NULL COMMENT '自动跳转的外链',
  `update_admin_id` int(11) NOT NULL COMMENT '更新人(管理员ID)',
  `is_delete` tinyint(4) NOT NULL COMMENT '删除标识',
  `click_count` int(11) NOT NULL COMMENT '点击数',
  `sort` int(11) NOT NULL COMMENT '排序 由大到小',
  `seo_title` text NOT NULL COMMENT '自定义seo页面标题',
  `seo_keyword` text NOT NULL COMMENT '自定义seo页面keyword',
  `seo_description` text NOT NULL COMMENT '自定义seo页面标述',
  `uname` varchar(255) NOT NULL,
  `sub_title` varchar(255) NOT NULL,
  `brief` text NOT NULL,
  PRIMARY KEY  (`id`),
  KEY `cate_id` (`cate_id`),
  KEY `create_time` (`create_time`),
  KEY `update_time` (`update_time`),
  KEY `click_count` (`click_count`),
  KEY `sort` (`sort`)
) ENGINE=MyISAM AUTO_INCREMENT=70 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of fanwe_article
-- ----------------------------
INSERT INTO `%DB_PREFIX%article` VALUES ('1', '公司简介', '', '<p><span style=\"widows:2;text-transform:none;background-color:#ffffff;text-indent:0px;display:inline !important;font:12px/19px arial, tahoma, helvetica, 宋体;white-space:normal;orphans:2;float:none;letter-spacing:normal;color:#444444;word-spacing:0px;-webkit-text-size-adjust:auto;-webkit-text-stroke-width:0px;\">p2p信贷(fanwe.com)为有资金需求和理财需求的个人搭建了一个公平、透明、稳定、高效的网络互动平台。用户可以在p2p信贷上获得信用评级、发布借款请求满足个人的资金需要；也可以把自己的闲余资金通过人人贷出借给信用良好有资金需求的个人，在获得良好的资金回报率的同时帮助了他人。</span></p>\r\n<div style=\"padding-bottom:10px;widows:2;text-transform:none;background-color:#ffffff;text-indent:0px;padding-left:0px;padding-right:0px;font:12px/19px arial, tahoma, helvetica, 宋体;word-wrap:break-word;white-space:normal;orphans:2;letter-spacing:normal;color:#444444;overflow:hidden;word-break:break-all;word-spacing:0px;padding-top:10px;-webkit-text-size-adjust:auto;-webkit-text-stroke-width:0px;\" class=\"h20\"></div>\r\n<div style=\"widows:2;text-transform:none;background-color:#ffffff;text-indent:0px;font:600 12px/19px arial, tahoma, helvetica, 宋体;word-wrap:break-word;white-space:normal;orphans:2;letter-spacing:normal;color:#8e8e8e;word-break:break-all;word-spacing:0px;-webkit-text-size-adjust:auto;-webkit-text-stroke-width:0px;\" class=\"f_dgray b f_12\">p2p信贷缘起</div>\r\n<div style=\"padding-bottom:10px;widows:2;text-transform:none;background-color:#ffffff;text-indent:0px;padding-left:0px;padding-right:0px;font:12px/19px arial, tahoma, helvetica, 宋体;word-wrap:break-word;white-space:normal;orphans:2;letter-spacing:normal;color:#444444;overflow:hidden;word-break:break-all;word-spacing:0px;padding-top:10px;-webkit-text-size-adjust:auto;-webkit-text-stroke-width:0px;\" class=\"h20\"></div>\r\n<p><span style=\"widows:2;text-transform:none;background-color:#ffffff;text-indent:0px;display:inline !important;font:12px/19px arial, tahoma, helvetica, 宋体;white-space:normal;orphans:2;float:none;letter-spacing:normal;color:#444444;word-spacing:0px;-webkit-text-size-adjust:auto;-webkit-text-stroke-width:0px;\">随着互联网用户的普及、技术的进步与货币数字化的迅速发展，2005年在英国首次出现个人对个人（P2P）的网络信贷服务平台。这种模式由于使借贷双方互惠双赢，加上其高效便捷的操作方式、个性化的利率定价机制，推出后得到广泛的认可和关注，迅速在其他国家复制。我们团队看到了这种模式将对中国民间信贷及小额贷款行业带来深远积极的影响。我们决定结合中国的社会信用状况，利用可靠的信用审核模型和先进的技术，创建了适合中国的P2P小额信贷网络平台——p2p信贷(fanwe.com)。</span></p>\r\n<div style=\"padding-bottom:10px;widows:2;text-transform:none;background-color:#ffffff;text-indent:0px;padding-left:0px;padding-right:0px;font:12px/19px arial, tahoma, helvetica, 宋体;word-wrap:break-word;white-space:normal;orphans:2;letter-spacing:normal;color:#444444;overflow:hidden;word-break:break-all;word-spacing:0px;padding-top:10px;-webkit-text-size-adjust:auto;-webkit-text-stroke-width:0px;\" class=\"h20\"></div>\r\n<div style=\"widows:2;text-transform:none;background-color:#ffffff;text-indent:0px;font:600 12px/19px arial, tahoma, helvetica, 宋体;word-wrap:break-word;white-space:normal;orphans:2;letter-spacing:normal;color:#8e8e8e;word-break:break-all;word-spacing:0px;-webkit-text-size-adjust:auto;-webkit-text-stroke-width:0px;\" class=\"f_dgray b f_12\">社会意义</div>\r\n<div style=\"padding-bottom:10px;widows:2;text-transform:none;background-color:#ffffff;text-indent:0px;padding-left:0px;padding-right:0px;font:12px/19px arial, tahoma, helvetica, 宋体;word-wrap:break-word;white-space:normal;orphans:2;letter-spacing:normal;color:#444444;overflow:hidden;word-break:break-all;word-spacing:0px;padding-top:10px;-webkit-text-size-adjust:auto;-webkit-text-stroke-width:0px;\" class=\"h20\"></div>\r\n<p><span style=\"widows:2;text-transform:none;background-color:#ffffff;text-indent:0px;display:inline !important;font:12px/19px arial, tahoma, helvetica, 宋体;white-space:normal;orphans:2;float:none;letter-spacing:normal;color:#444444;word-spacing:0px;-webkit-text-size-adjust:auto;-webkit-text-stroke-width:0px;\">P2P小额贷款是一种将非常小额度的贷款聚集起来借贷给有资金需求人群的一种商业模型。它的社会价值主要体现在满足个人资金需求、发展个人信用体系和提高社会闲散资金利用率三个方面：</span><br style=\"widows:2;text-transform:none;background-color:#ffffff;text-indent:0px;font:12px/19px arial, tahoma, helvetica, 宋体;white-space:normal;orphans:2;letter-spacing:normal;color:#444444;word-spacing:0px;-webkit-text-size-adjust:auto;-webkit-text-stroke-width:0px;\" />\r\n<span style=\"widows:2;text-transform:none;background-color:#ffffff;text-indent:0px;display:inline !important;font:12px/19px arial, tahoma, helvetica, 宋体;white-space:normal;orphans:2;float:none;letter-spacing:normal;color:#444444;word-spacing:0px;-webkit-text-size-adjust:auto;-webkit-text-stroke-width:0px;\">1）在中国，银行对个人信用贷款的条件要求很高，个人从银行系统融资面临很多困难,P2P小额贷款为需要资金的人提供了新的融资渠道。</span><br style=\"widows:2;text-transform:none;background-color:#ffffff;text-indent:0px;font:12px/19px arial, tahoma, helvetica, 宋体;white-space:normal;orphans:2;letter-spacing:normal;color:#444444;word-spacing:0px;-webkit-text-size-adjust:auto;-webkit-text-stroke-width:0px;\" />\r\n<span style=\"widows:2;text-transform:none;background-color:#ffffff;text-indent:0px;display:inline !important;font:12px/19px arial, tahoma, helvetica, 宋体;white-space:normal;orphans:2;float:none;letter-spacing:normal;color:#444444;word-spacing:0px;-webkit-text-size-adjust:auto;-webkit-text-stroke-width:0px;\">2）P2P小额贷款主要是以个人信用评价为基础的贷款，它的发展有助于个人体现自身的信用价值，提高社会个人信用体系的建设。</span><br style=\"widows:2;text-transform:none;background-color:#ffffff;text-indent:0px;font:12px/19px arial, tahoma, helvetica, 宋体;white-space:normal;orphans:2;letter-spacing:normal;color:#444444;word-spacing:0px;-webkit-text-size-adjust:auto;-webkit-text-stroke-width:0px;\" />\r\n<span style=\"widows:2;text-transform:none;background-color:#ffffff;text-indent:0px;display:inline !important;font:12px/19px arial, tahoma, helvetica, 宋体;white-space:normal;orphans:2;float:none;letter-spacing:normal;color:#444444;word-spacing:0px;-webkit-text-size-adjust:auto;-webkit-text-stroke-width:0px;\">3）P2P小额贷款扩宽了个人投资的渠道，加大了资金的流动，提高了社会闲散资金的使用率，促进了经济的发展。</span><br style=\"widows:2;text-transform:none;background-color:#ffffff;text-indent:0px;font:12px/19px arial, tahoma, helvetica, 宋体;white-space:normal;orphans:2;letter-spacing:normal;color:#444444;word-spacing:0px;-webkit-text-size-adjust:auto;-webkit-text-stroke-width:0px;\" />\r\n<span style=\"widows:2;text-transform:none;background-color:#ffffff;text-indent:0px;display:inline !important;font:12px/19px arial, tahoma, helvetica, 宋体;white-space:normal;orphans:2;float:none;letter-spacing:normal;color:#444444;word-spacing:0px;-webkit-text-size-adjust:auto;-webkit-text-stroke-width:0px;\">P2P网络借贷平台的出现，不仅仅是一个创新的商业模式, 它更为缩小社会贫富差距、创造就业、实现经济长期发展、社会和谐作出了重大贡献。</span></p>\r\n<div style=\"padding-bottom:10px;widows:2;text-transform:none;background-color:#ffffff;text-indent:0px;padding-left:0px;padding-right:0px;font:12px/19px arial, tahoma, helvetica, 宋体;word-wrap:break-word;white-space:normal;orphans:2;letter-spacing:normal;color:#444444;overflow:hidden;word-break:break-all;word-spacing:0px;padding-top:10px;-webkit-text-size-adjust:auto;-webkit-text-stroke-width:0px;\" class=\"h20\"></div>\r\n<div style=\"widows:2;text-transform:none;background-color:#ffffff;text-indent:0px;font:600 12px/19px arial, tahoma, helvetica, 宋体;word-wrap:break-word;white-space:normal;orphans:2;letter-spacing:normal;color:#8e8e8e;word-break:break-all;word-spacing:0px;-webkit-text-size-adjust:auto;-webkit-text-stroke-width:0px;\" class=\"f_dgray b f_12\">p2p信贷的愿景</div>\r\n<div style=\"padding-bottom:10px;widows:2;text-transform:none;background-color:#ffffff;text-indent:0px;padding-left:0px;padding-right:0px;font:12px/19px arial, tahoma, helvetica, 宋体;word-wrap:break-word;white-space:normal;orphans:2;letter-spacing:normal;color:#444444;overflow:hidden;word-break:break-all;word-spacing:0px;padding-top:10px;-webkit-text-size-adjust:auto;-webkit-text-stroke-width:0px;\" class=\"h20\"></div>\r\n<p><span style=\"widows:2;text-transform:none;background-color:#ffffff;text-indent:0px;display:inline !important;font:12px/19px arial, tahoma, helvetica, 宋体;white-space:normal;orphans:2;float:none;letter-spacing:normal;color:#444444;word-spacing:0px;-webkit-text-size-adjust:auto;-webkit-text-stroke-width:0px;\">我们坚信，随着时代的进步，中国社会的信用体系必将逐步完善，而技术的革新，也必将使民间借贷的模式发生革命性的变化。我们期待在这次进步的浪潮中，走在时代的前端，打造出中国最诚信可靠的P2P网络借贷平台，成为一家卓越的、实现巨大社会价值的企业。</span></p>', '2', '1428084904', '1428084904', '0', '1', '', '0', '0', '192', '30', '', '', '', '', '', '');
INSERT INTO `%DB_PREFIX%article` VALUES ('2', '免责条款', '', '免责条款', '2', '1428084904', '1428084904', '0', '1', '', '0', '0', '55', '18', '', '', '', '', '', '');
INSERT INTO `%DB_PREFIX%article` VALUES ('3', '隐私保护', '', '<div class=\"lh26\"><b><span style=\"font-size:14px;\">隐私保护我们有哪些措施保障您的隐私安全</span></b><div><ul><li>　· p2p贷款严格遵守国家相关法律法规，对用户的隐私信息进行严格的保护。</li>\r\n<li>　· 我们采用业界最先进的加密技术，用户的注册信息、账户收支信息都已进行高强度的加密处理，不会被不法分子窃取到。</li>\r\n<li>　· p2p贷款设有严格的安全系统，未经允许的员工不可获取您的相关信息。</li>\r\n<li>　· p2p贷款绝不会将您的账户信息、银行信息以任何形式透露给第三方。</li>\r\n</ul>\r\n</div>\r\n<div><b><span style=\"font-size:14px;\">个人信息安全：</span></b></div>\r\n<div>p2p贷款是一个实名认证平台，p2p贷款会保证用户信息隐私的安全，用户在平台上交流的过程中，也要时刻注意保护个人隐私，截图注意覆盖个人信息，不要透露真实姓名与住址等，以防个人信息被盗取造成损失。</div>\r\n</div>', '3', '1428084904', '1428084904', '0', '1', '', '0', '0', '31', '19', '', '', '', '', '', '');
INSERT INTO `%DB_PREFIX%article` VALUES ('4', '咨询热点', '', '咨询热点', '4', '1428084904', '1428084904', '0', '1', '', '0', '0', '60', '20', '', '', '', '', '', '');
INSERT INTO `%DB_PREFIX%article` VALUES ('5', '联系我们', '', '<div style=\"line-height:19px;font-family:arial, tahoma, helvetica, 宋体;word-wrap:break-word;color:#8e8e8e;word-break:break-all;font-weight:600;\" class=\"f_dgray b f_12\">福建p2p信贷金融信息服务有限公司<div style=\"padding-bottom:10px;padding-left:0px;padding-right:0px;word-wrap:break-word;overflow:hidden;word-break:break-all;padding-top:10px;\" class=\"h20\"></div>\r\n客户服务</div>\r\n<div style=\"padding-bottom:10px;line-height:19px;padding-left:0px;padding-right:0px;font-family:arial, tahoma, helvetica, 宋体;word-wrap:break-word;color:#444444;overflow:hidden;word-break:break-all;padding-top:10px;\" class=\"h20\"></div>\r\n<span style=\"line-height:19px;font-family:arial, tahoma, helvetica, 宋体;color:#444444;\">如果您在使用p2p信贷（fanwe.com）的过程中有任何疑问请您与人人贷客服人员联系。</span><br style=\"line-height:19px;font-family:arial, tahoma, helvetica, 宋体;color:#444444;\" />\r\n<span style=\"line-height:19px;font-family:arial, tahoma, helvetica, 宋体;color:#444444;\">在线帮助：</span><font color=\"#0000ee\" face=\"Arial, Tahoma, Helvetica, 宋体\"><span style=\"line-height:19px;\"><u><a href=\"./index.php?ctl=helpcenter\" target=\"_blank\">http://www.fanwe.com/index.php?ctl=helpcenter</a></u></span></font><br style=\"line-height:19px;font-family:arial, tahoma, helvetica, 宋体;color:#444444;\" />\r\n<span style=\"line-height:19px;font-family:arial, tahoma, helvetica, 宋体;color:#444444;\">客服电话：0591-88138230</span><div style=\"padding-bottom:10px;line-height:19px;padding-left:0px;padding-right:0px;font-family:arial, tahoma, helvetica, 宋体;word-wrap:break-word;color:#444444;overflow:hidden;word-break:break-all;padding-top:10px;\" class=\"h20\"></div>\r\n<div style=\"line-height:19px;font-family:arial, tahoma, helvetica, 宋体;word-wrap:break-word;color:#8e8e8e;word-break:break-all;font-weight:600;\" class=\"f_dgray b f_12\">媒体采访</div>\r\n<div style=\"padding-bottom:10px;line-height:19px;padding-left:0px;padding-right:0px;font-family:arial, tahoma, helvetica, 宋体;word-wrap:break-word;color:#444444;overflow:hidden;word-break:break-all;padding-top:10px;\" class=\"h20\"></div>\r\n<span style=\"line-height:19px;font-family:arial, tahoma, helvetica, 宋体;color:#444444;\">p2p信贷作为一个年轻、阳光的创新型公司，乐于展示我们的理念和价值观。如果您有媒体采访需求请将媒体名称、采访提纲、联系方式发送到info@fanwe.com，我们的工作人员会尽快与您联系。</span><div style=\"padding-bottom:10px;line-height:19px;padding-left:0px;padding-right:0px;font-family:arial, tahoma, helvetica, 宋体;word-wrap:break-word;color:#444444;overflow:hidden;word-break:break-all;padding-top:10px;\" class=\"h20\"></div>\r\n<div style=\"line-height:19px;font-family:arial, tahoma, helvetica, 宋体;word-wrap:break-word;color:#8e8e8e;word-break:break-all;font-weight:600;\" class=\"f_dgray b f_12\">商务合作<br />\r\n</div>\r\n<div style=\"padding-bottom:10px;line-height:19px;padding-left:0px;padding-right:0px;font-family:arial, tahoma, helvetica, 宋体;word-wrap:break-word;color:#444444;overflow:hidden;word-break:break-all;padding-top:10px;\" class=\"h20\"></div>\r\n<span style=\"line-height:19px;font-family:arial, tahoma, helvetica, 宋体;color:#444444;\">如果贵公司与p2p信贷优势互补，并有合作意向，请简要描述合作意向并发送到<a href=\"mailto:info@fanwe.com\">info@fanwe.com</a>，工作人员会尽快与您联系。</span><div style=\"padding-bottom:10px;line-height:19px;padding-left:0px;padding-right:0px;font-family:arial, tahoma, helvetica, 宋体;word-wrap:break-word;color:#444444;overflow:hidden;word-break:break-all;padding-top:10px;\" class=\"h20\"></div>\r\n<div style=\"line-height:19px;font-family:arial, tahoma, helvetica, 宋体;word-wrap:break-word;color:#8e8e8e;word-break:break-all;font-weight:600;\" class=\"f_dgray b f_12\">最新进展</div>\r\n<div style=\"padding-bottom:10px;line-height:19px;padding-left:0px;padding-right:0px;font-family:arial, tahoma, helvetica, 宋体;word-wrap:break-word;color:#444444;overflow:hidden;word-break:break-all;padding-top:10px;\" class=\"h20\"></div>\r\n<span style=\"line-height:19px;font-family:arial, tahoma, helvetica, 宋体;color:#444444;\">微博：</span><font color=\"#425f9d\" face=\"Arial, Tahoma, Helvetica, 宋体\"><span style=\"line-height:19px;cursor:pointer;\"><a href=\"http://t.sina.com.cn/fanwe\" target=\"_self\">http://t.sina.com.cn/fanwe</a></span></font><br style=\"line-height:19px;font-family:arial, tahoma, helvetica, 宋体;color:#444444;\" />\r\n<span style=\"line-height:19px;font-family:arial, tahoma, helvetica, 宋体;color:#444444;\">博客：</span><font color=\"#425f9d\" face=\"Arial, Tahoma, Helvetica, 宋体\"><span style=\"line-height:19px;cursor:pointer;\"><a href=\"http://blog.sina.com.cn/fanwe\" target=\"_self\">http://blog.sina.com.cn/fanwe</a></span></font><div style=\"padding-bottom:10px;line-height:19px;padding-left:0px;padding-right:0px;font-family:arial, tahoma, helvetica, 宋体;word-wrap:break-word;color:#444444;overflow:hidden;word-break:break-all;padding-top:10px;\" class=\"h20\"></div>\r\n<div style=\"line-height:19px;font-family:arial, tahoma, helvetica, 宋体;word-wrap:break-word;color:#8e8e8e;word-break:break-all;font-weight:600;\" class=\"f_dgray b f_12\">公司地址</div>\r\n<div style=\"padding-bottom:10px;line-height:19px;padding-left:0px;padding-right:0px;font-family:arial, tahoma, helvetica, 宋体;word-wrap:break-word;color:#444444;overflow:hidden;word-break:break-all;padding-top:10px;\" class=\"h20\"></div>\r\n<span style=\"line-height:19px;font-family:arial, tahoma, helvetica, 宋体;color:#444444;\">福建福州鼓楼区杨桥路19号尚林苑北802 &nbsp; &nbsp;邮编：350001</span><br style=\"line-height:19px;font-family:arial, tahoma, helvetica, 宋体;color:#444444;\" />\r\n<br class=\"Apple-interchange-newline\" />', '2', '1428084904', '1428084904', '0', '1', '', '0', '0', '77', '21', '', '', '', '', '', '');
INSERT INTO `%DB_PREFIX%article` VALUES ('6', '公司简介', '', '公司简介', '6', '1428084904', '1428084904', '0', '1', '', '0', '0', '92', '22', '', '', '', '', '', '');
INSERT INTO `%DB_PREFIX%article` VALUES ('7', 'p2p信贷即将上线', '', '<div><span style=\"font-size:14px;line-height:21px;\">尊敬的p2p信贷用户：</span></div>\r\n<div><span style=\"font-size:14px;line-height:21px;\"><br />\r\n</span></div>\r\n<div><span style=\"font-size:14px;line-height:21px;\">&nbsp; &nbsp; &nbsp; &nbsp; 您好！</span></div>\r\n<div><span style=\"font-size:14px;line-height:21px;\"><br />\r\n</span></div>\r\n<div style=\"text-align:left;\"><span style=\"font-size:14px;line-height:21px;\">&nbsp; &nbsp; &nbsp; &nbsp;&nbsp;</span><span style=\"font-size:14px;line-height:21px;\">p2p信贷正在测试中，</span><span style=\"font-size:14px;line-height:21px;\">预计2013年3月正式上线。具体上线日期确定后我们会及时予以公布。</span></div>\r\n<div><span style=\"font-size:14px;line-height:21px;\"><br />\r\n</span></div>\r\n<div><span style=\"font-size:14px;line-height:21px;\">&nbsp; &nbsp; &nbsp; &nbsp; 非常感谢各位用户的支持和信任！我们会不断完善、为您提供更加优质的服务！</span></div>\r\n<div><span style=\"font-size:14px;line-height:21px;\"><br />\r\n</span></div>\r\n<div style=\"text-align:right;\"><span style=\"font-size:14px;line-height:21px;\">p2p信贷团队</span></div>\r\n<div style=\"text-align:right;\"><span style=\"font-size:14px;line-height:21px;\"><br />\r\n</span></div>\r\n<div style=\"text-align:right;\"><span style=\"font-size:14px;line-height:21px;\">2013年2月4日</span></div>', '5', '1428084904', '1428084904', '0', '1', '', '0', '0', '74', '23', '', '', '', '', '', '');
INSERT INTO `%DB_PREFIX%article` VALUES ('66', '联系我们', '', '客户服务<div style=\"padding-bottom:10px;line-height:19px;padding-left:0px;padding-right:0px;font-family:arial, tahoma, helvetica, 宋体;word-wrap:break-word;color:#444444;overflow:hidden;word-break:break-all;padding-top:10px;\" class=\"h20\"></div>\r\n<span style=\"line-height:19px;font-family:arial, tahoma, helvetica, 宋体;color:#444444;\">如果您在使用p2p信贷（fanwe.com）的过程中有任何疑问请您与人人贷客服人员联系。</span><br style=\"line-height:19px;font-family:arial, tahoma, helvetica, 宋体;color:#444444;\" />\r\n<span style=\"line-height:19px;font-family:arial, tahoma, helvetica, 宋体;color:#444444;\">在线帮助：</span><font color=\"#910100\" face=\"Arial, Tahoma, Helvetica, 宋体\"><span style=\"line-height:19px;\"><u><a href=\"./index.php?ctl=helpcenter\" target=\"_blank\">http://www.fanwe.com/index.php?ctl=helpcenter</a></u></span></font><br style=\"line-height:19px;font-family:arial, tahoma, helvetica, 宋体;color:#444444;\" />\r\n<span style=\"line-height:19px;font-family:arial, tahoma, helvetica, 宋体;color:#444444;\">客服电话：0591-88138230</span> <div style=\"padding-bottom:10px;line-height:19px;padding-left:0px;padding-right:0px;font-family:arial, tahoma, helvetica, 宋体;word-wrap:break-word;color:#444444;overflow:hidden;word-break:break-all;padding-top:10px;\" class=\"h20\"></div>\r\n<div style=\"line-height:19px;font-family:arial, tahoma, helvetica, 宋体;word-wrap:break-word;color:#8e8e8e;word-break:break-all;font-weight:600;\" class=\"f_dgray b f_12\">媒体采访</div>\r\n<div style=\"padding-bottom:10px;line-height:19px;padding-left:0px;padding-right:0px;font-family:arial, tahoma, helvetica, 宋体;word-wrap:break-word;color:#444444;overflow:hidden;word-break:break-all;padding-top:10px;\" class=\"h20\"></div>\r\n<span style=\"line-height:19px;font-family:arial, tahoma, helvetica, 宋体;color:#444444;\">p2p信贷作为一个年轻、阳光的创新型公司，乐于展示我们的理念和价值观。如果您有媒体采访需求请将媒体名称、采访提纲、联系方式发送到info@fanwe.com，我们的工作人员会尽快与您联系。</span> <div style=\"padding-bottom:10px;line-height:19px;padding-left:0px;padding-right:0px;font-family:arial, tahoma, helvetica, 宋体;word-wrap:break-word;color:#444444;overflow:hidden;word-break:break-all;padding-top:10px;\" class=\"h20\"></div>\r\n<div style=\"line-height:19px;font-family:arial, tahoma, helvetica, 宋体;word-wrap:break-word;color:#8e8e8e;word-break:break-all;font-weight:600;\" class=\"f_dgray b f_12\">商务合作<br />\r\n</div>\r\n<div style=\"padding-bottom:10px;line-height:19px;padding-left:0px;padding-right:0px;font-family:arial, tahoma, helvetica, 宋体;word-wrap:break-word;color:#444444;overflow:hidden;word-break:break-all;padding-top:10px;\" class=\"h20\"></div>\r\n<span style=\"line-height:19px;font-family:arial, tahoma, helvetica, 宋体;color:#444444;\">如果贵公司与p2p信贷优势互补，并有合作意向，请简要描述合作意向并发送到<a href=\"mailto:info@fanwe.com\">info@fanwe.com</a>，工作人员会尽快与您联系。</span> <div style=\"padding-bottom:10px;line-height:19px;padding-left:0px;padding-right:0px;font-family:arial, tahoma, helvetica, 宋体;word-wrap:break-word;color:#444444;overflow:hidden;word-break:break-all;padding-top:10px;\" class=\"h20\"></div>\r\n<div style=\"line-height:19px;font-family:arial, tahoma, helvetica, 宋体;word-wrap:break-word;color:#8e8e8e;word-break:break-all;font-weight:600;\" class=\"f_dgray b f_12\">最新进展</div>\r\n<div style=\"padding-bottom:10px;line-height:19px;padding-left:0px;padding-right:0px;font-family:arial, tahoma, helvetica, 宋体;word-wrap:break-word;color:#444444;overflow:hidden;word-break:break-all;padding-top:10px;\" class=\"h20\"></div>\r\n<span style=\"line-height:19px;font-family:arial, tahoma, helvetica, 宋体;color:#444444;\">微博：</span><font color=\"#910100\" face=\"Arial, Tahoma, Helvetica, 宋体\"><span style=\"line-height:19px;cursor:pointer;\"><a href=\"http://t.sina.com.cn/fanwe\" target=\"_self\">http://t.sina.com.cn/fanwe</a></span></font><br style=\"line-height:19px;font-family:arial, tahoma, helvetica, 宋体;color:#444444;\" />\r\n<span style=\"line-height:19px;font-family:arial, tahoma, helvetica, 宋体;color:#444444;\">博客：</span><font color=\"#910100\" face=\"Arial, Tahoma, Helvetica, 宋体\"><span style=\"line-height:19px;cursor:pointer;\"><a href=\"http://blog.sina.com.cn/fanwe\" target=\"_self\">http://blog.sina.com.cn/fanwe</a></span></font> <div style=\"padding-bottom:10px;line-height:19px;padding-left:0px;padding-right:0px;font-family:arial, tahoma, helvetica, 宋体;word-wrap:break-word;color:#444444;overflow:hidden;word-break:break-all;padding-top:10px;\" class=\"h20\"></div>\r\n<div style=\"line-height:19px;font-family:arial, tahoma, helvetica, 宋体;word-wrap:break-word;color:#8e8e8e;word-break:break-all;font-weight:600;\" class=\"f_dgray b f_12\">公司地址</div>\r\n<div style=\"padding-bottom:10px;line-height:19px;padding-left:0px;padding-right:0px;font-family:arial, tahoma, helvetica, 宋体;word-wrap:break-word;color:#444444;overflow:hidden;word-break:break-all;padding-top:10px;\" class=\"h20\"></div>\r\n<span style=\"line-height:19px;font-family:arial, tahoma, helvetica, 宋体;color:#444444;\">福建福州鼓楼区杨桥路19号尚林苑北802 &nbsp; &nbsp;邮编：350001</span><br style=\"line-height:19px;font-family:arial, tahoma, helvetica, 宋体;color:#444444;\" />', '2', '1428084904', '1428084904', '0', '1', '', '0', '1', '1', '55', '', '', '', '', '', '');
INSERT INTO `%DB_PREFIX%article` VALUES ('8', '常见问题', '', '常见问题', '1', '1428084904', '1428084904', '0', '1', '', '0', '0', '20', '27', '', '', '', '', '', '');
INSERT INTO `%DB_PREFIX%article` VALUES ('9', '平台原理', '', '<div class=\"blank20\"></div>\r\n<div class=\"tc\"><img src=\"./public/attachment/201301/14/17/aboutP2P.jpg\" alt=\"\" border=\"0\" /><br />\r\n</div>\r\n<div class=\"blank20\"></div>\r\n<div class=\"b f14\" style=\"color:#2e5f9b;\">		p2p信贷统平台机制	</div>\r\n<div class=\"blank10\"></div>\r\n<div style=\"color:#585858;\">p2p信贷(fanwe.com)为有资金需求和理财需求的个人搭建了一个公平、透明、稳定、高效的网络互动平台。用户可以在p2p信贷上获得信用评级、发布借款请求满足个人的资金需要；也可以把自己的闲余资金通过p2p信贷出借给信用良好有资金需求的个人，在获得良好的资金回报率的同时帮助了他人。</div>\r\n<div class=\"blank20\"></div>\r\n<div class=\"b f14\" style=\"color:#2e5f9b;\">p2p信贷借贷流程	</div>\r\n<div class=\"blank10\"></div>\r\n<div class=\"tc\"><img src=\"./public/attachment/201301/14/17/liucheng.jpg\" /></div>\r\n<div class=\"blank20\"></div>', '1', '1428084904', '1428084904', '0', '1', 'u:index|aboutp2p', '0', '0', '2', '26', '', '', '', 'aboutp2p', '', '');
INSERT INTO `%DB_PREFIX%article` VALUES ('10', '如何借款', '', '<div class=\"border\">        	<div class=\"tit\"><h3>贷款方式</h3>\r\n<a href=\"./index.php?ctl=borrow\">申请贷款</a></div>\r\n            <table cellspacing=\"0\" border=\"0\">            	<tbody><tr class=\"even\"><td style=\"border-right:1px solid #dce7fa;\">借款额度</td>\r\n<td> 3,000 至 1,000,000人民币</td>\r\n</tr>\r\n                <tr class=\"odd\"><td style=\"border-right:1px solid #dce7fa;\">借款利率</td>\r\n<td> 6% — 24%（取决于借贷双方以及借款人信用等级）</td>\r\n</tr>\r\n                <tr class=\"even\"><td style=\"border-right:1px solid #dce7fa;\">借款期限</td>\r\n<td> 3 / 6 / 9 / 12 / 18 / 24个月</td>\r\n</tr>\r\n                <tr class=\"odd\"><td style=\"border-right:1px solid #dce7fa;\">还款方式</td>\r\n<td> 每月还款（等额本息还款法）</td>\r\n</tr>\r\n                <tr class=\"even\"><td style=\"border-right:1px solid #dce7fa;\">服务费用</td>\r\n<td> 根据您的当前等级而定，等级越高，费用越低</td>\r\n</tr>\r\n                <tr class=\"odd\"><td style=\"border-right:1px solid #dce7fa;\">管理费用</td>\r\n<td> 将根据借款期限，每月向借入者收取借款本金的0.3%作为借款管理费</td>\r\n</tr>\r\n            </tbody>\r\n</table>\r\n        </div>\r\n				<div class=\"border\">        	<div class=\"tit\"><h3>贷款资格</h3>\r\n</div>\r\n           	<div class=\"titcen\">     	                 <p style=\"background:#ecf7ff;\"><span style=\"color:#ff8500;\">年龄：</span>22—65周岁</p>\r\n                       <p><span style=\"color:#ff8500;\">工作与收入：</span>请先确认您的身份</p>\r\n	                 <ul>                     <li style=\"background:#ecf7ff;list-style:square;padding-left:20px;line-height:34px;list-style-position:inside;\"><span style=\"color:#ff8500;\">私营业主(包含网店商家)：</span>有一年以上的营业执照;近半年可体现月均流水(对公或对私)不低于2万元。</li>\r\n                     <li style=\"background:#ecf7ff;list-style:square;padding-left:20px;line-height:34px;list-style-position:inside;\"><span style=\"color:#ff8500;\">企业员工：</span>在央企,国企及注册资本高于100万元人民币的一般民营企业,有半年以上的工作合同或相关证明,<br />\r\n可体现月收入不低于2000元,。</li>\r\n					 <li style=\"background:#ecf7ff;list-style:square;padding-left:20px;line-height:34px;list-style-position:inside;\"><span style=\"color:#ff8500;\">政府机关及事业单位工作人员：</span>正式工作人员,可体现月收入不低于1200元。</li>\r\n                 </ul>\r\n           	</div>\r\n        </div>\r\n<div class=\"border\">			<div class=\"tit\">            	  <h3>快速问答</h3>\r\n                  <a href=\"./index.php?ctl=help\">了解更多</a>            </div>\r\n			<div class=\"titcen\">				<h4>Q：如何判断我是否有申请资格？</h4>\r\n				<p>A：只要有您有相对稳定的收入，并能出示相关的证明文件就拥有申请贷款的资格。</p>\r\n			 				<h4>Q：什么是信用等级？较高的信用等级有什么优势？</h4>\r\n				<p>A：信用等级是p2p信贷用来评估借款人信用所使用的一套体系,目前p2p信贷信用等级共分7级，从最高的AA				级到最低的HR级。您的信用等级越高，能得到的借款额度越高，提高信用等级，可以增加借出者对您的信任度、并能以更低的成本成功借款、</p>\r\n							<h4>Q：我上传的隐私会不会被泄露？</h4>\r\n				<p>A：不会，保护用户的隐私和数据安全是p2p信贷&nbsp;最重要的责任之一。在传输层中,采用加密数据进行传输以				防止网络分包被截获造成数据泄露。同时p2p信贷还启用了侵扰监测系统来监测那些内部和外部易受侵扰的路径，并运用其他一些人工措施来加强保护用户数据的安全。 </p>\r\n								<h4>Q：能否提前还贷？</h4>\r\n				<p>A：您可以随时还清整笔贷款，您仅需向借出者支付一期本息以及剩余本金的1%的违约金。</p>\r\n			</div>\r\n        </div>', '1', '1428084904', '1428084904', '0', '1', 'u:index|borrow#aboutborrow', '0', '0', '0', '25', '', '', '', 'aboutborrow', '', '');
INSERT INTO `%DB_PREFIX%article` VALUES ('11', '如何理财', '', '<div style=\"margin-top:10px;padding-left:50px;\">		            <div id=\"step\">		                <img style=\"vertical-align:middle;\" alt=\"\" src=\"./public/images/aboutfinacing/reg_fwd.png\" />&nbsp;只需30秒,您即可注册成为p2p信贷用户<br />\r\n		                <img style=\"margin:2px 0px 0px 46px;\" alt=\"\" class=\"logo_hack\" src=\"./public/images/aboutfinacing/blue_sign.png\" />		            </div>\r\n		            <div id=\"step\">		                <img style=\"vertical-align:middle;\" alt=\"\" src=\"./public/images/aboutfinacing/lender.png\" />&nbsp;为了保证您的资金安全，您需先用手机验证、身份证验证2项实名验证，方可开始投资<br />\r\n		                <img style=\"margin-left:46px;\" alt=\"\" class=\"logo_hack\" src=\"./public/images/aboutfinacing/blue_sign.png\" />		            </div>\r\n		            <div id=\"step\">		                <img style=\"vertical-align:middle;\" alt=\"\" src=\"./public/images/aboutfinacing/choose.png\" />&nbsp;您可以根据自己的风险偏好，以及回报率要求，来选择最适合您的借款机会<br />\r\n		                <img style=\"margin-left:46px;\" alt=\"\" class=\"logo_hack\" src=\"./public/images/aboutfinacing/blue_sign.png\" />		            </div>\r\n		            <div id=\"step\">		                <img style=\"vertical-align:middle;\" alt=\"\" src=\"./public/images/aboutfinacing/lender_money.png\" />&nbsp;在您充值之后，您便可以点对点的将您的资金进行出借给您意向的借款人<br />\r\n		                <img style=\"margin-left:46px;\" alt=\"\" class=\"logo_hack\" src=\"./public/images/aboutfinacing/blue_sign.png\" />		            </div>\r\n		            <div id=\"step\">		                <img style=\"vertical-align:middle;\" alt=\"\" src=\"./public/images/aboutfinacing/interest.png\" />&nbsp;经p2p信贷审核的借款成功后，借入者将每月按时向您及其他借出者归还本息<br />\r\n		            </div>\r\n		        </div>\r\n		        <div style=\"margin-top:20px;padding:10px 50px 10px 50px;\">		            <div class=\"about_title2\">p2p信贷投资原理</div>\r\n		            <div style=\"border-bottom:1px solid #9c9c9c;margin:8px 0px 8px 0px;\"></div>\r\n		            <div style=\"font-size:13px;line-height:20px;\">		                <p>		                    p2p信贷(fanwe.com)为有资金需求和理财需求的个人搭建了一个公平、透明、稳定、高效的网络互动平台。		                    用户可以在p2p信贷上获得信用评级、发布借款请求满足个人的资金需要；也可以把自己的闲余资金通过p2p信贷出借给信用良好有资金需求的个人，		                    在获得良好的资金回报率的同时帮助了他人。		                </p>\r\n		            </div>\r\n		        </div>\r\n		        <div>		            <img alt=\"p2p借贷系统平台原理\" class=\"logo_hack\" src=\"./public/images/aboutfinacing/principle.jpg\" />		        </div>', '7', '1428084904', '1428084904', '0', '1', 'u:index|deals&act=about&u=financing', '0', '0', '1', '24', '', '', '', 'financing', '', '');
INSERT INTO `%DB_PREFIX%article` VALUES ('33', '本金保障计划', '', '<h3 style=\"border-bottom:medium none;border-left:medium none;border-top:medium none;border-right:medium none;\">什么是本金保障计划？</h3>\r\n<p>本金保障指当理财人（借出者）投资的借款出现严重逾期时（即逾期超过30天），p2p信贷系统将向理财人垫付此笔借款未归还的剩余出借本金或本息（具体情况视投资标的类型的具体垫付规则为准），从而为理财人营造一个安全的投资环境，保证投资人的本金安全。</p>\r\n<h3 style=\"border-bottom:medium none;border-left:medium none;border-top:medium none;border-right:medium none;\">理财人（出借者）需要向p2p信贷系统支付本金保障计划服务的费用吗？</h3>\r\n<p>当前理财人无需支付任何费用，通过身份认证成为借出者即可享受此服务。</p>\r\n<h3 style=\"border-bottom:medium none;border-left:medium none;border-top:medium none;border-right:medium none;\">如果我投资的借款发生逾期了，p2p信贷系统如何赔付？</h3>\r\n<p>当您投资的借款发生逾期30天后，p2p信贷风险备用金账户会在一个工作日内将此笔借款的应赔付金额自动充入您的p2p信贷账户中（具体情况视投资标的类型的具体垫付规则为准）。</p>\r\n<h3 style=\"border-bottom:medium none;border-left:medium none;border-top:medium none;border-right:medium none;\">我在p2p信贷上的所有理财投资是否都适用p2p信贷本金保障计划？</h3>\r\n<p>是的，每项投资的具体垫付规则视投资标的具体类型而定。</p>\r\n<div class=\"bztitle\">风险备用金账户规则</div>\r\n<p>“风险备用金账户”是指为p2p信贷系统所服务的所有理财人的共同利益考虑、以p2p信贷名义单独开设并由其管理的一个专款专用账户。</p>\r\n<h3 style=\"border-bottom:medium none;border-left:medium none;border-top:medium none;border-right:medium none;\">“风险备用金账户”资金来源：</h3>\r\n<p>“风险备用金账户”资金当前全部来源于p2p信贷系统根据其与借款人签署的协议向其所服务的借款人所收取的服务费（以下简称“服务费”），p2p信贷系统在依协议向借款人收取服务费的同时，将在收取的服务费中按照贷款产品类型及借款人的信用等级等信息计提风险备用金（详见《p2p信贷系统风险备用金账户—产品垫付规则明细表》）。计提的风险备用金将存放入“风险备用金账户”进行专户管理。各产品类型及信用等级等所对应的风险备用金的计提标准和方式由p2p信贷制定并解释，p2p信贷有权根据实际业务需要对相关内容进行调整，如作修改，p2p信贷将及时进行披露。 </p>\r\n<h3 style=\"border-bottom:medium none;border-left:medium none;border-top:medium none;border-right:medium none;\">“风险备用金账户”资金用途：</h3>\r\n<p>“风险备用金账户”资金将专门用于在一定限额内弥补p2p信贷所服务的理财人（债权人）由于借款人（债务人）的违约所遭受的本金或本息的损失（具体赔付金额以所投资的产品类型的垫付规则为准），即当借款人（债务人）逾期还款超过30日时，p2p信贷将按照“风险备用金账户”资金使用规则从该账户中提取相应资金用于偿付理财人（债权人）应收取的本金或本息金额（不同产品的垫付范围请参考《p2p信贷系统风险备用金账户—产品垫付规则明细表》）（以下统一简称“逾期应收赔付金额”）。</p>\r\n<h3 style=\"border-bottom:medium none;border-left:medium none;border-top:medium none;border-right:medium none;\">“风险备用金账户”资金使用规则：</h3>\r\n<p>“风险备用金账户”资金使用遵循以下规则：<br />\r\n1、违约偿付规则，即当借款人（债务人）逾期还款超过30 日时，方才从“风险备用金账户”资金中抽取相应资金偿付理财人（债权人）逾期应收赔付金额。<br />\r\n2、时间顺序规则，即“风险备用金账户”资金对理财人（债权人）逾期应收赔付金额的偿付按照该债权发生的时间顺序进行偿付分配，先偿付时间发生在前的债权，后偿付时间发生在后的债权。<br />\r\n3、债权比例规则， “风险备用金账户”资金对同一借款协议下的不同理财人（债权人）逾期应收赔付金额的偿付按照各债权金额占同协议内发生的债权总额的比例进行偿付分配；或，当“风险备用金账户”资金当期余额不足以支付当期（每月为一期）所有应享受该账户的理财人所对应的逾期赔付金额时，则当期应享受该账户的理财人按照各自对应的逾期应收赔付金额占当期所有理财人对应的逾期应收赔付总额的比例进行偿付分配。<br />\r\n4、有限偿付规则，即“风险备用金账户”资金对理财人（债权人）逾期应收赔付的偿付以该账户的资金总额为限，当该账户余额为零时，自动停止对理财人逾期应收赔付金额的偿付，直到该账户获得新的风险备用金。<br />\r\n5、收益转移规则，即当理财人享有了“风险备用金帐户”对某笔逾期债权赔付金额的足额偿付后，该债权对应的借款人其后为该笔债权所偿还的本金、利息及罚息归属“风险备用金账户”；如债权有抵押、质押及其他担保的，则平台代借款人处置抵押质押物的所得等也归属“风险备用金账户”。<br />\r\n6、金额上限规则，即当“风险备用金帐户”内金额超过当时平台上所有债权本金金额的10%时，p2p借贷系统有权将超出部分转出该账户，转出部分归p2p借贷系统所有。<br />\r\n</p>\r\n<h3 style=\"border-bottom:medium none;border-left:medium none;border-top:medium none;border-right:medium none;\">“风险备用金账户”资金管理原则：</h3>\r\n<p>p2p信贷将审慎管理“风险备用金账户”资金，并就账户及资金使用情况对理财人进行定期（按季度/月）披露。具体披露方式及解释权归p2p信贷所有。</p>\r\n<h3 style=\"border-bottom:medium none;border-left:medium none;border-top:medium none;border-right:medium none;\">附表：《p2p信贷风险备用金账户—产品垫付规则明细表》</h3>\r\n<p style=\"position:relative;text-align:center;\"><img border=\"0\" alt=\"\" src=\"./public/images/aq/benjinhelp.jpg\" /></p>\r\n<p class=\"b\">注：信用认证标中与部分渠道合作的产品风险金计提标准不适用上表规则，将根据合作渠道的具体情形单独设定。</p>', '8', '1428084904', '1428084904', '0', '1', '', '0', '0', '12', '48', '', '', '', '', '', '');
INSERT INTO `%DB_PREFIX%article` VALUES ('12', 'p2p信贷费率', '', '<div class=\"blank20\"></div>\r\n<div class=\"b f12\">一、理财费用</div>\r\n<p class=\"f_red\">p2p信贷系统不收取理财人任何费用，充值提现费用为第三方支付公司收取</p>\r\n<div class=\"blank20\"></div>\r\n<div class=\"b f12\">二、贷款费用</div>\r\n1、服务费<br />\r\np2p信贷系统收取的服务费将全部存于风险准备金账户用于p2p信贷系统的本金保障计划。服务费将按照借款人的信用等级来收取： <div class=\"blank20\"></div>\r\n<table style=\"text-align:center;background-color:#ccc;width:100%;\" border=\"0\" cellspacing=\"1\" align=\"center\"><tbody><tr style=\"background:#eff5fe;height:30px;\"><td class=\"tc\">信用等级 </td>\r\n<td class=\"tc\" width=\"10%\">AA </td>\r\n<td width=\"10%\" tc?=\"\">A </td>\r\n<td class=\"tc\" width=\"10%\">B </td>\r\n<td class=\"tc\" width=\"10%\">C </td>\r\n<td class=\"tc\" width=\"10%\">D </td>\r\n<td class=\"tc\" width=\"10%\">E </td>\r\n<td class=\"tc\" width=\"10%\">HR </td>\r\n</tr>\r\n<tr style=\"background:#fff;height:30px;\"><td class=\"tc\">费率</td>\r\n<td class=\"tc\">0%</td>\r\n<td class=\"tc\">1%</td>\r\n<td class=\"tc\">1.5%</td>\r\n<td class=\"tc\">2%</td>\r\n<td class=\"tc\">2.5%</td>\r\n<td class=\"tc\">3%</td>\r\n<td class=\"tc\">5%</td>\r\n</tr>\r\n</tbody>\r\n</table>\r\n2、账户管理费<br />\r\np2p信贷系统将按照借款人的借款期限，每月向借款人收取其借款本金的0.3%作为借款管理费。<div class=\"blank20\"></div>\r\n<div class=\"b f12\">三、充值提现费用</div>\r\n1、充值费用（由第三方平台收取）<br />\r\n第三方支付平台将在您充入资金时扣除0.5%作为转账费用。充值费用上限为100元。超过100元的部分由p2p信贷承担。<br />\r\n<p style=\"color:#8e8e8e;\">注：充值时可以使用一张充值免费券，抵去该笔充值费用。（每位新注册理财用户（完成绑定身份证和手机号）将自动获得一张充值免费券，并可通过参与活动的方式获得更多充值免费券。）</p>\r\n<div class=\"blank20\"></div>\r\n2、提现费用（由第三方平台收取）<br />\r\n当借款人要求将借款资金转至指定银行账户时，会发生转账费用，第三方支付平台将按以下标准收取相关费用。<table style=\"text-align:center;background-color:#ccc;width:100%;\" border=\"0\" cellspacing=\"1\" align=\"center\"><tbody><tr style=\"background:#eff5fe;height:30px;\"><td width=\"25%\"><div align=\"center\">金额 </div>\r\n</td>\r\n<td width=\"25%\"><div align=\"center\">2万元以下 </div>\r\n</td>\r\n<td width=\"25%\"><div align=\"center\">2万元（含）－5万元 </div>\r\n</td>\r\n<td width=\"25%\"><div align=\"center\">5万元（含）－100万元 </div>\r\n</td>\r\n</tr>\r\n<tr style=\"background:#fff;height:30px;\"><td><div align=\"center\">收费 </div>\r\n</td>\r\n<td><div align=\"center\">1元/笔 </div>\r\n</td>\r\n<td><div align=\"center\">3元/笔 </div>\r\n</td>\r\n<td><div align=\"center\">5元/笔 </div>\r\n</td>\r\n</tr>\r\n</tbody>\r\n</table>\r\n<div class=\"blank20\"></div>', '1', '1428084904', '1428084904', '0', '1', 'u:index|aboutfee', '0', '0', '0', '23', '', '', '', 'aboutfee', '', '');
INSERT INTO `%DB_PREFIX%article` VALUES ('67', '庆祝上线，免收成交费用', '', '<p><span style=\"font-size:14px;\">&nbsp;</p>\r\n<div><span style=\"line-height:21px;font-size:14px;\">尊敬的p2p信贷用户：</span></div>\r\n</span><p><span style=\"font-size:14px;\">&nbsp;&nbsp;&nbsp;&nbsp; 经过团队为期两个月的筹备、测试，p2p信贷终于和各位朋友见面了！</span></p>\r\n<p>&nbsp;</p>\r\n<p><span style=\"font-size:14px;\">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;您在p2p信贷上能够接触到全新的借款和理财模式，体验到技术进步带来的便捷与实惠。也欢迎各位邀请自己的亲戚、朋友、同事来使用p2p信贷，让更多的人能够感受到网络互助平台带来的前所未有的借贷体验。</span></p>\r\n<p>&nbsp;</p>\r\n<p><span style=\"font-size:14px;\">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;为庆祝网站上线，网站目前免收成交费用。请大家把握机会，多多尝试借款~~</span></p>\r\n<p>&nbsp;</p>\r\n<p>&nbsp;</p>\r\n<p>&nbsp;</p>\r\n<p align=\"right\"><span style=\"font-size:14px;\">p2p信贷团队</span></p>\r\n<p align=\"right\"><span style=\"font-size:14px;\">2013年3月4日</span></p>', '5', '1428084904', '1428084904', '0', '1', '', '0', '0', '49', '55', 'p2p信贷', '网络贷款', '网贷', '', '', '');
INSERT INTO `%DB_PREFIX%article` VALUES ('13', '审核之借出者篇', '', '在p2p信贷上成为借出者十分简便，用户免费注册后通过手机绑定，身份证验证和借出学习即可成为p2p信贷借出者，成为借出者后用户可以选择符合自己标准的借款列表借出。关于借款列表的选择，我们有专门的建议，这里主要介绍一下信用审核对于借出者选择的影响。p2p信贷的信用审核系统包括了基本信用审核（目前包括身份证明、工作证明、学历证明、社区网认证）和附加信用审核（目前包括购车证明、微博认证、信用报告认证、收入认证、结婚证明）两个方面。要通过信用审核要求借入者上传自己的相关资料，p2p信贷的审核人员对这些资料进行严格审查，尽可能确定资料的真实性。基本信用审核表明借入者的真实性和基本信用情况（经验表明工作、学历在很大程度上说明了借入者的信用状况），而附加信用审核的材料获得有一定的难度，且具有一定门槛（购车证明、完税证明、个人信用报告等审核对借出者的经济基础有较高要求），借入者能够通过附加信用审核说明他对借款更加重视，也具备更强的还款能力。通过审核的资料在借入者发布借款请求时会部分披露给借出者，从而让借出者更加了解借入者的情况，以便作出正确的判断。当借入者发布借款时，为保护借出者的利益，我们要求借入者至少要通过基本信用审核。 在p2p信贷上选择借款列表时，除了要看借入者的借款描述和用途外，更要看借入者披露的信息是否足以证明自己的还款意愿和能力（一般来说，著名高校毕业、就职于知名企业或政府部门、社区网使用活跃、已购车、已婚、收入较高等都能直接或间接说明借入者还款意愿和能力更强）。为保护借出者的利益，借入者要在p2p信贷上披露信用信息必须通过相应的认证，也就是说借入者通过越多的审核借出者对借入者进行判断的依据也就越多。所以，借出者应尽量选择基本信用状况优秀或是基本资料并不出众但是通过了较多附加信用审核的借入者。特别地，当借入者发布较高额的借款请求时，除非通过其它渠道对借入者有所了解，否则我们建议借出者要主动要求借入者通过更多的附加信用审核（可以通过借款列表内的留言板进行留言）。', '6', '1428084904', '1428084904', '0', '1', '', '0', '0', '0', '28', '', '', '', '', '', '');
INSERT INTO `%DB_PREFIX%article` VALUES ('14', '审核之借入者篇', '', '在p2p信贷上申请成为借入者，完成基本信用审核后才可以发布借款，基本信用审核是p2p信贷审核的门槛，提交基本信用审核申请的难度较低，借入者通过基本信用审核后即可发布借款请求。附加信用审核作为基本信用审核的补充，能够提高借入者的信用等级，增加借出者对借入者的信任，提高借款成功率。 p2p信贷的基本信用审核目前包括身份证认证，学历认证，工作认证和社区网认证。要通过基本信用审核需要提供身份证原件的照片或扫描件，中国高等教育学生信息网学历认证在线验证码（登录学信网注册后即可方便获得），工作证明原件照片或扫描件及常使用的社区网账号（目前仅限于开心网和人人网），这些材料获取容易，且包括了用户的基本信息（借入者在发布借款请求时，我们会在保护用户隐私的前提下有选择的披露部分借入者信息以便借出者作出判断），能够让借出者对借入者有初步了解。 附加信用审核目前包括新浪微博认证，收入证明，个人信用报告认证，购车证明，结婚证明。通过附加信用审核可以提高信用等级，等级越高，借款成功的可能性越大，借款利率也会相应降低。因而我们提倡借款前尽可能多地完成附加信用审核。某些附加信用审核材料的获得较基本信用审核困难一些，比如个人信用报告，完税证明，等都需要到专门的机构获得。虽然材料的获取有一定难度，但是只要您提供的材料真实，通过这些审核后借出者对您的了解和信任度会有显著提高，借款也更易成功。', '6', '1428084904', '1428084904', '0', '1', '', '0', '0', '0', '29', '', '', '', '', '', '');
INSERT INTO `%DB_PREFIX%article` VALUES ('15', '选择借款列表', '', '借款列表是p2p信贷借入者发出的借款请求。借款列表中包括借款数额、借款目的、借款期限和借款人对如何使用这笔借款的描述等信息。通过借款列表您还可以了解借款人已上传哪些信用审核资料及该借款人的信用等级。p2p信贷上有许多借款列表可供选择。点击“我要借出”标签，您就可以浏览所有的借款列表。“正在进行中的列表”显示的是目前可以投标的一些借款，“等待复审的列表”显示的是已经完成招标，正在审核中的借款。您也可以查看所有借款列表，并通过招标剩余时间、借款数额、借款进度、借款人信用等级等条件，进一步详细搜索。 您可以根据p2p信贷为您提供的信息选择适合您投资的借款列表。如果您是风险爱好者，您可以选择信用等级较低、借款期限较长但利息率高的借款列表。如果您是风险规避者，您可以选择信用等级高、借款期限短但利息率较低的借款列表。p2p信贷保证您完全享有真实、有效的借款人信息，以便您做出明智的选择，这样您的投资就会有稳定、丰厚的回报。 想了解更多投资策略，请参考“投资策略”与“分散投资降低风险”。', '6', '1428084904', '1428084904', '0', '1', '', '0', '0', '0', '30', '', '', '', '', '', '');
INSERT INTO `%DB_PREFIX%article` VALUES ('16', '借款失败原因', '', '<p>1．信息太少，有的借款者发布的借款列表内容很少，甚至只有一个图标或者一张没有真实头像的图片，没有明确的借款原因。这样的借款列表不能让人产生信任感，借出者会因为真实信息太少又不能保证按时还款而不会投标。</p>\r\n<p>&nbsp;2．借款的用途不可靠，有的借款者借款用途风险太大或者不合法，有的甚至带有赌博性质，比如要去股市再赌一把等，这种风险太大的借款项目一般出借人也不会选择。&nbsp;</p>\r\n<p>3．借款描述存在明显问题，比如有的借款列表描述中，每月收入除去每月应还款数额后剩余金额不足以抵消每月开支，没有足够的还款能力。</p>\r\n<p>4．低级的骗子，有的借入者直接抄袭别人的借款列表信息，但是一些基本信息存在明显的矛盾之处。&nbsp;</p>\r\n<p>5．信誉不好者，在p2p信贷有逾期行为的借款者会被记录到黑名单中，随着逾期时间的增长，我们会逐步公布逾期借款者的信息，有了逾期记录的借款者再次借款会有一定难度。&nbsp;</p>\r\n<p>6．发布借款列表后即消失，对于借出者的合理的提问要求不予回应。&nbsp;</p>\r\n<p>7．个人基本情况并不优秀，而且通过的附加信用审核很少或是没有通过附加信用审核。</p>', '6', '1428084904', '1428084904', '0', '1', '', '0', '0', '0', '31', '', '', '', '', '', '');
INSERT INTO `%DB_PREFIX%article` VALUES ('17', '其他借出技巧', '', '<p>1.不要盲目跟投 盲目跟投是初级借出者常见的错误之一。不管借款人的信用级别，不看借款列表详情，就亦步亦趋的跟着其他出借人投标，这是危险且对自有资金不负责的行为。一些明显存在较大风险的借款列表很可能由于一些借出者的盲目跟投而满标，进而产生潜在风险。&nbsp;</p>\r\n<p>2.不要在投资中夹杂过多感情因素 p2p借贷系统建立的初衷是让大家帮助大家，但归根结蒂对于出借人来说这里是一个投资的平台是一个理财的途径，如果出借人承担了与回报不相符的风险那么出借人对于借入者的帮助也是无力和不可持续的。所以，借出者不要完全基于感情因素（喜欢，同情等）去投标，在帮助人的同时一定要考虑自己的风险与回报是否匹配。&nbsp;</p>\r\n<p>3.关注借入者的网络行为 我们生活在网络互动日益频繁，网络生活占日常生活比重日益加大对时代，p2p借贷系统也正是在这样的大环境下应运而生。所以，在判断借款人信用的时候除了以参考p2p借贷系统给出的信用级别为主外，还可以关注该用户的网络行为。比如他/她的网络日志，他/她的社交网站，微博等等。通常来讲，一个用户可考证的网络行为可以从一个侧面在某种程度上反映出他/她的信用水平。&nbsp;</p>\r\n<p>4.主动向借入者提问 经验表明优秀的贷款审核员对借入者的人工判断在某些时候甚至会优于最好的信用模型。所以，p2p借贷系统鼓励借出者对借入者提出与借款相关的问题，或是要求借入者提供一些附加的信用材料。在借款列表中的留言板上对借入者提问，借入者会在第一时间得到通知。当借入者回答您的提问时，您也会收到邮件提醒。&nbsp;</p>\r\n<p>5.关注借款人信用等级以外的其他信息 毋庸置疑，借款人的信用等级在很大程度上说明了借款人的信用情况，但是有时通过借入人是哪个院校毕业的、他/她做过哪些认证、他/她之前的还款情况怎样、他/她的借款用途是什么、他/她准备如何还款等其他信息也能在一定程度上判断出借款人是否有还款能力和还款意愿。</p>', '6', '1428084904', '1428084904', '0', '1', '', '0', '0', '0', '32', '', '', '', '', '', '');
INSERT INTO `%DB_PREFIX%article` VALUES ('18', '提高借款成功率', '', '<p>提高借款成功率 借款能否成功主要看出借人对借入者的看法，如果借入者有良好的还款意愿和还款能力，且能够提供真实的证明材料，则出借人更愿意对其投资。具体来说，一笔成功的借款需要具备的条件有真实的借款用途，真诚的借款意愿，充足的还款能力，可靠的证明材料。简而言之就是，提供真实的资料，增加信用等级，以真诚打动出借人。&nbsp;</p>\r\n<p>分开来讲要注意的地方有：</p>\r\n<p>&nbsp;　　1．尽量通过更多的附加信用审核。附加信用审核的通过有一定的难度，通过附加信用审核可以证明借入者的还款意愿和还款能力，而且每通过一项审核，审核人员会根据您提供的材料进行评分，信用分数越高，信用等级相应地也会提高，不喜欢高风险的借出者也比较愿意对您的信用列表投标。&nbsp;</p>\r\n<p>　　2．借款时在不泄露个人隐私前提下提供尽可能多的披露个人信用信息，目前可披露的信息包括毕业院校，工作单位，社区网使用状况，婚姻状况，购车状况，信用报告情况，收入状况等，其中有些内容借款时必须披露，有些则可以在通过相应的认证后选择性的披露。通常著名高校毕业，知名企业或政府机关就业，社区网站活跃，已婚，购车等情况比较吸引借出者。同时，借款描述中详细的借款用途，可靠的还款来源，合理认真的收支计划等也能反映借入者的认真态度，更容易筹集到借款。&nbsp;</p>\r\n<p>　　3．可以将自己的借款分享到人人网，开心网或新浪微博，吸引有意愿和能力的好友来投标，同时好友的关注，投标及评论也可以吸引其他借出者。</p>', '6', '1428084904', '1428084904', '0', '1', '', '0', '0', '0', '33', '', '', '', '', '', '');
INSERT INTO `%DB_PREFIX%article` VALUES ('19', '如何判断借款列表', '', '在p2p借贷系统上借出闲置资金，在帮助别人的同时获取稳定的收入， 实现社会资本的再分配，是一种双赢的选择。但是平台上发布借款的人数众多且参差不齐，必然存在借款者不能按时还款的情况。为了保证自己的资金能够收回，如何选择可靠的借款列表进行投资就需要慎重考虑。对于大多数投资人来说，一个大原则就是做熟不做生，投稳不投险。 对于朋友发布的借款列表，可以通过私下亲自接触来了解，这种情况能够获取第一手资料，稳定性更高。对于朋友的朋友发布的借款列表，可以通过朋友间接了解，或经朋友介绍直接了解。 对于陌生人发布的借款列表，首先要看他/她的信用等级。等级可以从很大程度上反映借入者的信用水平，一般而言信用等级越高违约的可能性就越低。其次要看他通过的信用审核，基本信用审核（目前包括身份证明、工作证明、学历证明、社区网认证）可以表明借入者身份的真实性和基本信用情况，成为借入者发布借款至少要通过基本信用审核；附加信用审核（目前包括购车证明、微博认证、信用报告认证、收入认证、结婚证明）能反映其对借款的重视程度，也能表明其还款能力。所以借出者可以选择基本情况优秀或是通过较多附加信用审核的借出者。最后要看一下他/她的借款描述，以及借款条件。借款用途和还款计划描述详尽可靠，且借款数额和借款利率与借款描述和借款人信用等级相符的借款人较为可靠。如果经过以上判断还有疑问的可以通过借款列表的留言板给借款者留言，通过借款者的回答来更进一步了解借款者。', '6', '1428084904', '1428084904', '0', '1', '', '0', '0', '0', '34', '', '', '', '', '', '');
INSERT INTO `%DB_PREFIX%article` VALUES ('20', '借入者注意事项', '', '<p>用户到p2p信贷发布借款，基本上都是近期有资金需求想要尽快筹到所需资金的，有的借入者对于借贷流程不熟悉，没有成功借到钱。借款失败的原因可能有很多，p2p信贷给出的建议是：作为借入者当有所为有所不为。&nbsp;</p>\r\n<p>有所为：</p>\r\n<p>　　1．尽可能详尽地完善个人资料，完成信用审核，资料要真实可靠，投资者都会偏向于比较可靠的借入者，真实的照片，可查询的社区网站，通过较多的附加信用审核等等都可以获得更多人的关注。&nbsp;</p>\r\n<p>　　2．邀请好友加入，熟悉的人对彼此都很了解，会有更强的投资意愿。而且好友的评价也可以成为投资者的参考意见。&nbsp;</p>\r\n<p>　　3．尽可能详细耐心地回答借出者的提问，通过沟通加深彼此的了解，对各种可能出现的情况提前进行说明，借出者在获得自己想要的信息之余还会对您的耐心细心留下深刻的印象。&nbsp;</p>\r\n<p>有所不为：&nbsp;</p>\r\n<p>　　1．不提供任何虚假信息。p2p信贷的后台审核人员对于用户资料的甄别非常认真严格，能够避免大部分欺诈借款的通过，而且p2p信贷用户之间的交流也可以让一些骗子无所遁形，不要存在侥幸心理，一旦被识别出虚假信息，用户信用会受到非常大的影响，信用恢复也非常困难，没有人愿意将钱投给有诈骗前科的人。&nbsp;</p>\r\n<p>　　2．在不了解平台原理借贷流程的情况下发布借款，屡次流标，有了多次流标记录会让借出者对用户产生怀疑，再次发布借款的成功率也不会太高。&nbsp;</p>\r\n<p>　　3．不要将自己的一些隐私如身份证号，详细住址等公布在公共区域，警惕骗子利用您的信息行骗。 希望您能够在p2p信贷成功借到资金。</p>', '6', '1428084904', '1428084904', '0', '1', '', '0', '0', '0', '35', '', '', '', '', '', '');
INSERT INTO `%DB_PREFIX%article` VALUES ('21', '投资策略', '', '<font color=\"#444444\" face=\"Arial, Tahoma, Helvetica, 宋体\"><span style=\"line-height:19px;\">一旦您决定通过p2p信贷进行投资，您就可以利用我们为您提供的工具、数据和p2p信贷独一无二的优势——透明性，开始构建专属于您的投资组合。 在您构建投资组合时先要明确的是您的风险偏好和承受损失的能力。通常来讲较高的收益会对应较高的风险，低风险也只会带来较低的收益。具体到p2p信贷来讲，信用等级高、信用信息优秀（毕业于著名高校，在有实力的公司任职等）且信用资料完善的借入者违约几率极低，所以支付给您的利息也会相对较低（还是高于一般理财产品），较低信用级别或是信用信息并不出众的借入者则会支付更多的利息，但是相对而言违约率也会有所增加。通过浏览借款人的详细资料（包括毕业院校，婚姻状况，工作情况，p2p信贷借款记录，借款描述，财务状况等）、信用等级，p2p信贷审核记录以及亲身与之交流，您可以挑选出更适合您风险偏好的贷款。如果您承受风险的能力较强，为了追求更高的收益您可以制定较为激进的投资策略并在您的投资组合中更多的加入信用级别相对较低的借入者，反之，如果您厌恶风险，追求更加稳定的回报，您可以制定更加稳健的投资策略在您的投资组合中更多的考虑高信用级别的借款人。同时，您的投资策略还应随着您的财务状况的变化做出相应的调整。</span></font>', '6', '1428084904', '1428084904', '0', '1', '', '0', '0', '0', '36', '', '', '', '', '', '');
INSERT INTO `%DB_PREFIX%article` VALUES ('22', '无抵押贷款骗局', '', '<p>目前全国很多地方出现了小额无抵押贷款信息，这些公司均是不限地区，也不需要任何抵押，只需身份证就可以放贷款。事实证明这些信息基本都是欺诈信息。 无抵押贷款，正规金融机构只有个别银行推出了公务员担保的小额无抵押贷款及农村信用社的多户联保，而且需要按照正规流程:到银行面签合同，提供工资卡的银行对帐单等。事实上这些业务在实践中也不断出现问题，甚至多数已经名存实亡，或者不断提高要求导致贷款量逐渐萎缩。至于民间机构（包括新成立的小额贷款公司），则多数不敢涉足，或者欲尝辄止，难成气候。 纵观目前出现的骗局，他们都有一定的行为模式，共同之处可能包括：&nbsp;</p>\r\n<p>　　1．公司名头比较吓人，所谓的“xx贷款集团”等，甚至有个别还提供营业执照复印件，造假痕迹十分明显。事实上，所谓的“xx贷款集团”，根本不可能在工商部门登记注册，即使提供的营业执照复印件，也是仿造的；&nbsp;</p>\r\n<p>　　2．一般只提供手机号、联系人和QQ，无固定电话，无办公地址，通过网上搜索该号码可以看到在全国不同城市的业务都是用同一号码，非常有问题；&nbsp;</p>\r\n<p>　　3．一般都说明公司在全国各大中城市均设有分支机构，各地都可以方便的办理业务；&nbsp;</p>\r\n<p>　　4．放贷条件十分宽松，手续简便，只需提供基本资料如身份证，户口本或营业执照，无需抵押；&nbsp;</p>\r\n<p>　　5．利率看似合理，月息1%—3%不等；&nbsp;</p>\r\n<p>　　6．当借款人联系后，骗子们会找出各种理由要求先收取前期费用（如预付利息，律师费，核实费，保险费，手续费，保证金等）；&nbsp;</p>\r\n<p>　　7．当借款人付费后，发现骗子再也无法联系上；&nbsp;</p>\r\n<p>p2p借贷系统在这里真诚提醒您：&nbsp;</p>\r\n<p>　　1．请到对方公司进行实地查看，并签订正式合同。&nbsp;</p>\r\n<p>　　2．请仔细辨别公司是否为正当经营的企业。&nbsp;</p>\r\n<p>　　3．请不要在贷款未到账之前支付任何前期费用。 p2p信贷提供一个借入借出者自由交流的平台，借入借出者的准入都要求通过实名身份验证，发布借款需要通过各项审核，借款完成放标前需要通过视频验证，电话验证各项验证，尽可能的保证借款的安全，但是我们的措施不能做到万无一失，我们通过审核认证等行为尽可能详细地了解用户信息，对用户的投资理财给出专业的建议，您可以基于我们的建议作出自己的决定。</p>', '6', '1428084904', '1428084904', '0', '1', '', '0', '0', '0', '37', '', '', '', '', '', '');
INSERT INTO `%DB_PREFIX%article` VALUES ('23', '新手上路', '', '如果您是一个投资新手，不知道从何开始，p2p信贷是您不二的选择，我们为您提供流水化的、简单的投资过程，帮您实现轻松理财。 信息的透明度是我们一贯坚持的理念。p2p信贷为您提供完全透明、真实的信息，使您能够做出正确的投资选择。通过借款人的信用记录、投标进程的即时更新，您可以全程了解您投资的去向。另外，很重要的一点是您可以了解您把钱借给了谁，用做什么用途。我们的借款人是真实存在的个体，而不是像一些公司那样，让您无从得知投资的用途。 作为一个投资新手，您可以阅读一些有关投资方面的书，也可以向身边的朋友寻求一些建议。p2p信贷也为您提供了一个简单易行的理财方法。但是这并不意味着在p2p信贷投资没有任何风险，您也有可能遇到不按时还款的借款人、甚至坏账。这就是我们为什么建议您分散投资，使您的投资组合多样化。详情请参考“分散投资降低风险”。', '6', '1428084904', '1428084904', '0', '1', '', '0', '0', '0', '38', '', '', '', '', '', '');
INSERT INTO `%DB_PREFIX%article` VALUES ('24', '分散投资降低风险', '', 'p2p借贷系统网络平台的一大优势在于丰富、透明、全面的信息，您可以轻松接触到所有借入者的必要资料，加之平台最低允许单笔50元的投资，您可以轻松投资多个借入者。 投资多个借入者的一大好处在于有效降低风险。虽然不管投资多么分散单笔投资的违约率是不会改变的，但是由于资金分散，单笔投资规模较小，一笔投资不能收回不会严重影响收益率。反之，如果投资过于集中，一旦出现违约会对投资收益造成严重影响甚至给您带来损失。因而我们建议您在投资时尽量将资金分成多笔投出，不要集中在一笔或者几笔上。具体应该分散到什么程度跟您的投资策略息息相关，当您更多的选择低信用级别的借入者以追求高收益时，您应适当加大分散程度。反之，当您更多的选择高信用级别的借款人以追求稳定的回报时，可以适当降低分散程度从而减少您的工作量。原则上讲，我们建议您将手中的资金分成至少20份投出。 另外，分散投资可以让您帮助更多的借入者，在投资的同时享受帮助他人的乐趣。分散投资还可以让您有效的建立投资组合，具体请参考“投资策略”。', '6', '1428084904', '1428084904', '0', '1', '', '0', '0', '0', '39', '', '', '', '', '', '');
INSERT INTO `%DB_PREFIX%article` VALUES ('25', '风险与收益', '', '在任何投资中，收益和风险都形影相随，收益以风险为代价，风险用收益来补偿。投资者投资的目的是为了得到收益，与此同时，又不可避免地面临着风险，在p2p信贷投资也是如此。 通过p2p信贷，您可以注册成为借出者并投资购买一个或多个借款列表中的部分或者全部债权。当一个借入者加入p2p信贷并提交了相应的资料后（包括工作证明、社区网证明、学历证明、身份验证、微博验证、购车证明、结婚证明、个人信用报告、收入证明等），我们会严格审查资料，尽可能确定资料的真实性，并利用我们的信用评估系统授予该借入者一个p2p信贷信用等级。通过审核的资料会在借入者发布借款时部分的披露给借出者，以便借出者做出正确的判断（一般来说，著名高校毕业、就职于知名企业或政府部门、社区网使用活跃、已购车、已婚、收入较高等都能直接或间接说明借入者还款意愿和能力更强）。而借入者的信用等级则由其通过审核的数量和其之前在网站上的借贷行为决定。通常，信用等级越高披露的信用资料越优秀出现违约的几率也就越小，反之则违约几率加大。 更小的违约几率代表更加确定的收益，但是由于借入者信用级别较高，或是他的信用资料很优秀时，她/他所接受的利息通常较低。因而投资这类借入者通常会带来风险小、稳定但是较低的收益。反之，较低的信用级别代表相对较大的风险，但当借入者信用级别较低，且信用资料并不出众时，她/他所接受的利息会相对较高。于是，投资低信用等级借入者通常会带来具有一定风险但是较高的收益。 了解了风险与回报的关系您就可以根据您的风险偏好来选择借入者进行投资了。p2p信贷网络平台的优势让您可以将您手中的资金分散投给多个借入者，于是您可以制定自己的投资策略，构建自己的投资组合。具体请参考“投资策略”于“分散投资降低风险”。', '6', '1428084904', '1428084904', '0', '1', '', '0', '0', '0', '40', '', '', '', '', '', '');
INSERT INTO `%DB_PREFIX%article` VALUES ('26', '信用等级的解读', '', '最近，部分用户反映不是很理解我们的信用等级，我在这里做一下说明。如果大家读过本栏目中的“风险与收益”、“投资策略”以及“如何判断借款列表” 那么一定知道借入者信用水平有高有低，潜在的违约率也不尽相同。目前，p2p借贷系统根据借入者提交的信用审核资料将借入者分成7个信用等级，由高到低分别是 AA、A、B、C、D、E、HR。其中，AA为信用水平最高级，代表极低的违约率，同时，这类借款人通常接受的利率也较低。反过来，HR则代表低信用水平，潜在的违约风险也较其他信用等级为高，所以需要提供更高的利息来弥补出借人承担的风险。具体分数线以及等级的划分可以参考“我的帐户”内的“信用审核”页面。 p2p借贷系统信用审核', '6', '1428084904', '1428084904', '0', '1', '', '0', '0', '0', '41', '', '', '', '', '', '');
INSERT INTO `%DB_PREFIX%article` VALUES ('27', '政策法规', '', '<div class=\"blank20\"></div>\r\n<div class=\"f_dgray b f12\">    一、民间借贷的定义</div>\r\n<div class=\"blank20\"></div>\r\n民间借贷是指自然人之间、自然人与法人之间、自然人与其它组织之间借贷。民间借贷是《民法通则》中一种民事法律行为，行为人在具有完全民事行为能力（即年满18周岁，且不存在足以影响自身行为的精神疾病的情形）、意思表示真实，借款合同符合法律、行政法规规定，则该借款合同完全受到《合同法》等法律的保护。民间借贷是民间资本的一种投资渠道，作为银行金融的有效补充已逐渐成为民间金融的一种重要形式。<div class=\"blank20\"></div>\r\n<div class=\"f_dgray b f12\">    二、民间借贷的法律环境</div>\r\n<div class=\"blank20\"></div>\r\n《国务院关于鼓励和引导民间投资健康发展的若干意见》：新36条出台，鼓励民间资本进入金融领域，发起设立金融中介服务机构。<div class=\"blank20\"></div>\r\n《中华人民共和国合同法》从法律上肯定了民间借贷行为的合法性，并从法律层面保护出借人收回借贷资金和利息的权利。<div class=\"blank20\"></div>\r\n《合同法》第211条：“自然人之间的借款合同对支付利息没有约定或约定不明确的，视为不支付利息。自然人之间的借款合同约定支付利息的，借款的利率不得违反国家有关限制借款利率的规定”。<div class=\"blank20\"></div>\r\n最高人民法院《关于人民法院审理借贷案件的若干意见》第6条：“民间借贷的利率可以适当高于银行的利率，各地人民法院可以根据本地区的实际情况具体掌握，但最高不得超过银行同类贷款利率的四倍，（包含利率本数）。超出此限度的，超出部分的利息不予保护”。<div class=\"blank20\"></div>\r\n<div class=\"f_dgray b f12\">    三、p2p信贷的合法性</div>\r\n<div class=\"blank20\"></div>\r\n《合同法》第二十三章“居间合同”中明确规定，居间人提供贷款合同订立的媒介服务，可依法向委托方收取相应的报酬。因此贷款服务机构的存在和服务费的收取都是符合法律规定并受法律保护的。<div class=\"blank20\"></div>\r\np2p信贷既不吸储，也不放贷，作为一个网络信用管理及借贷服务中介机构，其业务范围和经营活动完全符合相关法律和国家的政策规定。p2p信贷将在国家法律和相关政策的指引下，为广大借款人、出借人提供优质、高效的服务。<div class=\"blank20\"></div>\r\n<div class=\"f_dgray b f12\">    四、政策倾向</div>\r\n<div class=\"blank20\"></div>\r\n<a href=\"http://finance.qq.com/a/20111110/006098.htm?qq=0&amp;ADUIN=1053718065&amp;ADSESSION=1320928475&amp;ADTAG=CLIENT.QQ.3847_.0\" target=\"_blank\"> “央行：“民间借贷有合法性 利率不得高于银行4倍”</a><div class=\"blank20\"></div>\r\n央行有关负责人表示，在遵守相关法律前提下，自然人、法人及其他组织间有自由借贷的权利。只要不违反法律的强制性规定，民间借贷关系都受法律保护。民间借贷是正规金融的有益补充。', '3', '1428084904', '1428084904', '0', '1', 'u:index|aboutlaws', '0', '0', '3', '42', '', '', '', 'aboutlaws', '', '');
INSERT INTO `%DB_PREFIX%article` VALUES ('28', '为什么选择p2p信贷', '', '<div style=\"margin-top:10px;padding:10px;\">		<div class=\"about_title2\">稳定的回报</div>\r\n		<div style=\"border-bottom:1px solid #9c9c9c;margin:8px 0px 8px 0px;\"></div>\r\n		<div style=\"font-size:13px;line-height:20px;\">			<p>在股票、债券、银行存款等传统投资方式之外，p2p信贷为您提供了一种新的投资途径，您可以将钱5.9%—23.4%不等的年利率，出借给哪些有需要的借入者，				然后每月回收本金和利息，从而获取稳定的投资回报。			</p>\r\n		</div>\r\n	</div>\r\n	<div style=\"margin-top:20px;padding:10px;\">		<div class=\"about_title2\">帮助他人</div>\r\n		<div style=\"border-bottom:1px solid #9c9c9c;margin:8px 0px 8px 0px;\"></div>\r\n		<div style=\"font-size:13px;line-height:20px;\">			<p>				在p2p信贷上，您可以自由的选择将钱出借给您希望帮助的群体，如医生、警察、消防员，或者创业人群等。				您也可以通过借款目的来选择您出借的对象，如教育培训借款，婚礼筹备借款等。这一切都将是您在获取稳定回报的同时，实现了帮助他人的人生价值。			</p>\r\n		</div>\r\n	</div>\r\n	<div style=\"margin-top:20px;padding:10px;\">		<div class=\"about_title2\">详尽客观的借入者信息</div>\r\n		<div style=\"border-bottom:1px solid #9c9c9c;margin:8px 0px 8px 0px;\"></div>\r\n		<div style=\"font-size:13px;line-height:20px;\">			<p>p2p信贷以客观公正的态度，尽最大的努力去审核借入者的每一项信用信息。包括借入者的工作情况，家庭情况，收入情况，信用情况等。				p2p信贷将这些信用信息分析整理后，最大限度地提供给出借者，以帮助出借者做出客观正确的出借抉择。			</p>\r\n		</div>\r\n	</div>\r\n	<div style=\"margin-top:20px;padding:10px;\">		<div class=\"about_title2\">分散投资</div>\r\n		<div style=\"border-bottom:1px solid #9c9c9c;margin:8px 0px 8px 0px;\"></div>\r\n		<div style=\"font-size:13px;line-height:20px;\">			<p>				我们同时也要提醒您，在p2p信贷的出借并不是100%安全的，借出的金额也存在一定的违约风险。然而通过p2p信贷，您可以将您的总出借金额，				以最低50元一笔的方式分散出借给很多借入者，这样的分散投资将大大降低您的整体投资风险。			</p>\r\n		</div>\r\n	</div>\r\n	<div style=\"margin-top:20px;padding:10px;\">		<div class=\"about_title2\">灵活的投资周期</div>\r\n		<div style=\"border-bottom:1px solid #9c9c9c;margin:8px 0px 8px 0px;\"></div>\r\n		<div style=\"font-size:13px;line-height:20px;\">			<p>				p2p信贷提供3个月、6个月、9个月、12个月、18个月、24个月、和36个月7种借款期限选择。因此出借者可以根据自己的需要，将钱投资给不一样周期的借款人。				同时，由于借入者是每月还款，因此出借者很快就可以开始回收投资，并非要等到借款到期时才可拿回本金。在回收本息的过程中，				出借者可以将回收的钱出借给其他借款人来获取更多的回报，也可将钱自由支配，用于其他用途。			</p>\r\n		</div>\r\n	</div>', '7', '1428084904', '1428084904', '0', '1', 'u:index|deals&act=about&u=choose', '0', '0', '0', '43', '', '', '', 'choose', '', '');
INSERT INTO `%DB_PREFIX%article` VALUES ('29', '投资回报', '', '<div style=\"margin-top:10px;padding:10px;\">	<div style=\"font-size:13px;line-height:20px;\">		<p>			通过p2p信贷，您可以将手中的富余资金出借给信用良好但缺少资金的大学生、工薪阶层、微小企业主、农民，帮助他们实现教育培训、电脑或家电购买、			装修、兼职创业、脱贫致富等理想，同时还能通过利息收入为您带来较高的稳定收益，			实现精神和物质的双重回报。根据p2p信贷的历史经验数据，资金年收益始终保持在10%以上。		</p>\r\n	</div>\r\n</div>\r\n<div style=\"margin-top:20px;padding:10px;\">	<div class=\"about_title2\">各类型投资产品收益及风险对比分析</div>\r\n	<div style=\"border-bottom:1px solid #9c9c9c;margin:8px 0px 8px 0px;\"></div>\r\n	<div style=\"font-size:13px;\" class=\"dunDetail\">		<table width=\"480\" style=\"text-align:center;background:#d3d4d8;\" border=\"0\" cellspacing=\"1\">			<tbody><tr bgcolor=\"#e1f0ff\" style=\"font-weight:bold;height:37px;\">				<td>产品</td>\r\n				<td>年收益</td>\r\n				<td>风险级别</td>\r\n				<td>流动性</td>\r\n			</tr>\r\n			<tr style=\"height:37px;\" bgcolor=\"#ffffff\">				<td>一年期定存</td>\r\n				<td>2.6%</td>\r\n				<td>★</td>\r\n				<td>★</td>\r\n			</tr>\r\n			<tr style=\"height:37px;\" bgcolor=\"#ffffff\">				<td>一年期国债</td>\r\n				<td>2.6%</td>\r\n				<td>★</td>\r\n				<td>★</td>\r\n			</tr>\r\n			<tr style=\"height:37px;\" bgcolor=\"#ffffff\">				<td>银行理财产品</td>\r\n				<td>3%—6%</td>\r\n				<td>★★</td>\r\n				<td>★</td>\r\n			</tr>\r\n			<tr style=\"height:37px;\" bgcolor=\"#ffffff\">				<td>开放式基金</td>\r\n				<td>不确定</td>\r\n				<td>★★★</td>\r\n				<td>★★★★★</td>\r\n			</tr>\r\n			<tr style=\"height:37px;\" bgcolor=\"#ffffff\">				<td>股票</td>\r\n				<td>不确定</td>\r\n				<td>★★★★★</td>\r\n				<td>★★★★</td>\r\n			</tr>\r\n			<tr style=\"height:37px;\" bgcolor=\"#ffffff\">				<td>p2p信贷</td>\r\n				<td>10%以上</td>\r\n				<td>★</td>\r\n				<td>★★</td>\r\n			</tr>\r\n		</tbody>\r\n</table>\r\n	</div>\r\n</div>\r\n<div style=\"margin-top:20px;padding:10px;\">	<div class=\"about_title2\">一年期定期存款、国债</div>\r\n	<div style=\"border-bottom:1px solid #9c9c9c;margin:8px 0px 8px 0px;\"></div>\r\n	<div style=\"font-size:13px;line-height:20px;\">		<p>			定期存款与国债的风险，在众多理财途径里面无疑最低的，然而锁定期长，2％左右的回报率，在通过膨胀日益高涨，CPI高于3％的今天，			却让资金难以抗衡通胀，从长期来看购买力将越来越低。		</p>\r\n	</div>\r\n</div>\r\n<div style=\"margin-top:20px;padding:10px;\">	<div class=\"about_title2\">银行理财产品</div>\r\n	<div style=\"border-bottom:1px solid #9c9c9c;margin:8px 0px 8px 0px;\"></div>\r\n	<div style=\"font-size:13px;line-height:20px;\">		<p>			在银行里，锁定期1年以上的理财产品，年化回报率一般在在3－6％左右，但是动辄几百万的投资门槛，将很多理财者拒之门外。相比起来，			通过p2p借贷系统借出，资金的回收情况则要灵活的多。此外，理财产品背后的还款来源则是政府的财政贷款，房地产信托等。这些对象的财务情况并非完全透明，			对于普通用户来说，往往只能看到表面的情况，难以真正理解其中的风险。		</p>\r\n	</div>\r\n</div>\r\n<div style=\"margin-top:20px;padding:10px;\">	<div class=\"about_title2\">股票、基金</div>\r\n	<div style=\"border-bottom:1px solid #9c9c9c;margin:8px 0px 8px 0px;\"></div>\r\n	<div style=\"font-size:13px;line-height:20px;\">		<p>			随着中国金融行业的发展，股票和基金已经慢慢地成为了很多人投资理财的选择。然而，股票和激励里那些复杂的概念，创业版，物联网，			有色板块，明星基金经理，内部消息等等词汇，我们已慢慢耳熟能详，我们也一次次地期望那个暴富的故事发生在我们身上，然而在现实生活中，			却往往发现并不那么给力，我们面临的更多的总是长期套牢。相比起来，通过分散投资，通过借钱还款这样简单明了的赚钱模式，获得稳定的10%以上收益，			也许更适合普通的投资者。		</p>\r\n	</div>\r\n</div>', '7', '1428084904', '1428084904', '0', '1', 'u:index|deals&act=about&u=respond', '0', '0', '0', '44', '', '', '', 'respond', '', '');
INSERT INTO `%DB_PREFIX%article` VALUES ('30', '投资风险', '', '<div style=\"margin-top:10px;padding:10px;\">	<div class=\"about_title2\">p2p信贷的审核机制</div>\r\n	<div style=\"border-bottom:1px solid #9c9c9c;margin:8px 0px 8px 0px;\"></div>\r\n	<div style=\"font-size:13px;line-height:20px;\">		<p>			p2p信贷结合了国内外先进的信用审查及风险控制机制，形成了一整套贯穿于前期咨询、贷前审核、贷后跟进、回款管理的全面而严密的信用服务流程。			截至目前为止，p2p信贷系统上半年以来，坏账率仅为0.8%，远低于同行业1.7%的平均水平。<br />\r\n			前期审核阶段，p2p信贷系统将通过对用户的各种材料，以及电话核实的手段，来考查用户的偿还能力，信用习惯，和稳定性三方面。审核时依据的材料包括用户的工作证明，			银行流水单，个人信用报告，房产证明，结婚证明等。		</p>\r\n	</div>\r\n</div>\r\n<div style=\"margin-top:20px;padding:10px;\">	<div class=\"about_title2\">p2p信贷系统的催收机制</div>\r\n	<div style=\"border-bottom:1px solid #9c9c9c;margin:8px 0px 8px 0px;\"></div>\r\n	<div style=\"font-size:13px;line-height:20px;\">		<p>			除了前期严格的审核手段之外，若借款人出现逾期还款情况，p2p信贷将在不同的阶段进行不同程度的催收。<br />\r\n			逾期初期，p2p信贷客服将通过电话与借款人进行沟通，做出善意的还款提醒，同时了解用户的逾期原因，并提醒保持良好信用的重要性。<br />\r\n			如果p2p信贷客服沟通期间，借款人不积极配合，或者在15天以后依然不进行还款，则p2p信贷将尝试与借款人的联系人等其他联系方式进行沟通。<br />\r\n			如果借款用户恶意逃避，或者逾期超过30天，p2p信贷将把借款人的催收委托给第三方全国性催收机构协助催收，同时逐步在借款人的借款详情里公布其逾期信息。		</p>\r\n	</div>\r\n</div>\r\n<div style=\"margin-top:20px;padding:10px;\">	<div class=\"about_title2\">分散投资，降低风险</div>\r\n	<div style=\"border-bottom:1px solid #9c9c9c;margin:8px 0px 8px 0px;\"></div>\r\n	<div style=\"font-size:13px;line-height:20px;\">		<p>			p2p信贷平台，为小额资金的分散投资提供了可能，根据经济学原理，每位借款人的还款是独立性极强的事件，这样风险就被大大分散。 <br />\r\n			例如，您将10万元借给一个借入者，假设违约率为1％，年利率为12％，一旦出现那1％的机率，该借款人违约不还，那么您所承受的风险将是100%。			但是，如果您能将10万元分成100元一笔，借出给1000个违约率为1%，			年利率为12%的借款人由于不同的借款人之间有极强的独立性，在这种投资方式下损失本金的概率将会变得非常低，约等于零。		</p>\r\n	</div>\r\n</div>\r\n<div style=\"margin-top:20px;padding:10px;\">	<div class=\"about_title2\">风险保证金体系，逾期本金垫付</div>\r\n	<div style=\"border-bottom:1px solid #9c9c9c;margin:8px 0px 8px 0px;\"></div>\r\n	<div style=\"font-size:13px;line-height:20px;\">		<p>			p2p信贷的风险保证金。风险保证金在理财者的出借资金遇到回收问题时，用以补偿其本金的损失，这将在很大程度上保证理财者的资金安全。<br />\r\n			目前p2p信贷的保证金处于试运行阶段，并免费将所有E级以上用户申请的借款纳入风险保证体系。当纳入风险保证体系的借款人逾期后，风险金将向理财者垫付本金，			同时该笔借款未来的本息、罚息等收益权，将归风险保证金所有。<br />\r\n			风险保证金具体细则，将在正式上线后公布。		</p>\r\n	</div>\r\n</div>', '7', '1428084904', '1428084904', '0', '1', 'u:index|deals&act=about&u=risk', '0', '0', '0', '45', '', '', '', 'risk', '', '');
INSERT INTO `%DB_PREFIX%article` VALUES ('31', '政策法规', '', '<div style=\"margin-top:10px;padding:10px;\">	<div class=\"about_title2\">民间借贷的定义</div>\r\n	<div style=\"border-bottom:1px solid #9c9c9c;margin:8px 0px 8px 0px;\"></div>\r\n	<div style=\"font-size:13px;line-height:20px;\">		<p>			民间借贷是指自然人之间、自然人与法人之间、自然人与其它组织之间借贷。民间借贷是《民法通则》中一种民事法律行为，			行为人在具有完全民事行为（即年满18周岁，且不存在足以影响自身行为的精神疾病的情形）、意思表示真实，借款合同符合法律、			行政法规规定，则该借款合同完全受到《合同法》等法律的保护。民间借贷是民间资本的一种投资渠道，			作为银行金融的有效补充已逐渐成为民间金融的一种重要形式。		</p>\r\n	</div>\r\n</div>\r\n<div style=\"margin-top:20px;padding:10px;\">	<div class=\"about_title2\">P2P民间借贷的法律环境</div>\r\n	<div style=\"border-bottom:1px solid #9c9c9c;margin:8px 0px 8px 0px;\"></div>\r\n	<div style=\"font-size:13px;line-height:20px;\">		<p>			《国务院关于鼓励和引导民间投资健康发展的若干意见》：新36条出台，鼓励民间资本进入金融领域，发起设立金融中介服务机构。<br />\r\n			《中华人民共和国合同法》从法律上肯定了民间借贷行为的合法性，并从法律层面保护出借人收回借贷资金和利息的权利。<br />\r\n			《合同法》第211条：“自然人之间的借款合同对支付利息没有约定或约定不明确的，视为不支付利息。自然人之间的借款合同约定支付利息的，			借款的利率不得违反国家有关限制借款利率的规定”<br />\r\n			最高人民法院《关于人民法院审理借贷案件的若干意见》第6条：“民间借贷的利率可以适当高于银行的利率，			各地人民法院可以根据本地区的实际情况具体掌握，但最高不得超过银行同类贷款利率的四倍，（包含利率本款）。超出此限度的，超出部分的利息不予保护”。		</p>\r\n	</div>\r\n</div>\r\n<div style=\"margin-top:20px;padding:10px;\">	<div class=\"about_title2\">p2p信贷的合法性</div>\r\n	<div style=\"border-bottom:1px solid #9c9c9c;margin:8px 0px 8px 0px;\"></div>\r\n	<div style=\"font-size:13px;line-height:20px;\">		<p>			《合同法》第二十三章“居间合同”中明确规定，居间人提供贷款合同订立的媒介服务，可依法向委托方收取相应的报酬。			因此贷款服务机构的存在和服务费的收取都是符合法律规定并受法律保护的。<br />\r\n			p2p信贷既不吸储，也不放贷，作为一个网络信用管理及借贷服务中介机构，其业务范围和经营活动完全符合相关法律和国家的政策规定。			p2p信贷将在国家法律和相关政策的指引下，为广大借款人、出借人提供优质、高效的服务。		</p>\r\n	</div>\r\n</div>', '7', '1428084904', '1428084904', '0', '1', 'u:index|deals&act=about&u=rule', '0', '0', '0', '46', '', '', '', 'rule', '', '');
INSERT INTO `%DB_PREFIX%article` VALUES ('32', '投资小提示', '', '<div style=\"margin-top:10px;padding:10px;\">	<div class=\"about_title2\">不要盲目跟投</div>\r\n	<div style=\"border-bottom:1px solid #9c9c9c;margin:8px 0px 8px 0px;\"></div>\r\n	<div style=\"font-size:13px;line-height:20px;\">		<p>p2p信贷结合了国内外先进的信用审查及风险控制机制，形成了一整套贯穿于前期咨询、贷前审核、贷后跟进、回款管理的全面而严密的信用服务流程。截至目前为止，p2p信贷上线半年以来，坏账率仅为0.8%，远低于同行业1.7%的平均水平。<br />\r\n			前期审核阶段，p2p信贷将通过对用户的各种材料，以及电话核实的手段，来考查用户的偿还能力，信用习惯和稳定性三方面。审核时依据的材料包括用户的工作证明、银行流水单、个人信用报告、房产证明、结婚证明等。		</p>\r\n	</div>\r\n</div>\r\n<div style=\"margin-top:20px;padding:10px;\">	<div class=\"about_title2\">不要在投资中夹杂过多感情因素</div>\r\n	<div style=\"border-bottom:1px solid #9c9c9c;margin:8px 0px 8px 0px;\"></div>\r\n	<div style=\"font-size:13px;line-height:20px;\">		<p>			除了前期严格的审核手段之外，若借款人出现逾期还款情况，p2p信贷将在不同的阶段进行不同程度的催收。<br />\r\n			逾期初期，p2p信贷客服将通过电话与借款人进行沟通，做出善意的还款提醒，同时了解用户的逾期原因，并提醒保持良好信用的重要性。  <br />\r\n			如果p2p信贷客服沟通期间，借款人不积极配合，或者在15天以后依然不进行还款，则p2p信贷将尝试与借款人的联系人等其他联系方式进行沟通。<br />\r\n			如果借款用户恶意逃避，或者逾期超过30天，p2p信贷将把借款人的催收委托给第三方全国性催收机构协助催收，同时逐步在借款人的借款详情里公布其逾期信息。		</p>\r\n	</div>\r\n</div>\r\n<div style=\"margin-top:20px;padding:10px;\">	<div class=\"about_title2\">关注借入者的网络行为</div>\r\n	<div style=\"border-bottom:1px solid #9c9c9c;margin:8px 0px 8px 0px;\"></div>\r\n	<div style=\"font-size:13px;line-height:20px;\">		<p>			p2p信贷平台，为小额资金的分散投资提供了可能，根据经济学原理，每位借款人的还款是独立性极强的事件，这样风险就被大大分散。<br />\r\n			例如，您将10万元借给一个借入者，假设违约率为1％，年利率为12％，一旦出现那1％的机率，该借款人违约不还，那么您所承受的风险将是100%损失。			但是，如果您将10万元分成100元一笔，借出给1000个违约率为1%，年利率为12%的借款人，由于不同的借款人之间有极强的独立性，在这种投资方式下损失本金的概率将会变得非常低，约等于零。		</p>\r\n	</div>\r\n</div>\r\n<div style=\"margin-top:20px;padding:10px;\">	<div class=\"about_title2\">主动向借入者提问</div>\r\n	<div style=\"border-bottom:1px solid #9c9c9c;margin:8px 0px 8px 0px;\"></div>\r\n	<div style=\"font-size:13px;line-height:20px;\">		<p>			p2p信贷即将推出风险保证金。风险保证金在理财者的出借资金遇到回收问题时，用以补偿其本金的损失，这将在很大程度上保证理财者的资金安全。<br />\r\n			目前p2p信贷的保证金处于试运行阶段，并免费将所有E级以上用户申请的借款纳入风险保证体系。当纳入风险保证体系的借款人逾期后，风险金将向理财者垫付本金，			同时该笔借款本来的本息、罚息等收益权，将规风险保证金所有。			<br />\r\n			风险保证金具体细则，将在正式上线后公布。		</p>\r\n	</div>\r\n</div>\r\n<div style=\"margin-top:20px;padding:10px;\">	<div class=\"about_title2\">关注借款人信用等级以外的其他信息</div>\r\n	<div style=\"border-bottom:1px solid #9c9c9c;margin:8px 0px 8px 0px;\"></div>\r\n	<div style=\"font-size:13px;line-height:20px;\">		<p>			毋庸置疑，借款人的信用等级在很大程度上说明了借款人的信用情况，但是有时通过借入人是哪个院校毕业的、他/她做过哪些认证、他/她之前的还款情况怎样、			他/她的借款用途是什么、他/她准备如何还款等其他信息也能在一定程度上判断出借款人是否有还款能力和还款意愿。		</p>\r\n	</div>\r\n</div>', '7', '1428084904', '1428084904', '0', '1', 'u:index|deals&act=about&u=clew', '0', '0', '15', '47', '', '', '', 'clew', '', '');
INSERT INTO `%DB_PREFIX%article` VALUES ('35', '隐私保护', '', '<h3>我们有哪些措施保障您的隐私安全</h3>\r\n<ul id=\"anquanli\">   <li>p2p信贷系统严格遵守国家相关法律法规，对用户的隐私信息进行严格的保护。 </li>\r\n   <li>我们采用业界最先进的加密技术，用户的注册信息、账户收支信息都已进行高强度的加密处理，不会被不法分子窃取到。 </li>\r\n   <li>p2p信贷系统设有严格的安全系统，未经允许的员工不可获取您的相关信息。</li>\r\n   <li>p2p信贷系统绝不会将您的账户信息、银行信息以任何形式透露给第三方。 </li>\r\n</ul>\r\n<h3>个人信息安全：</h3>\r\n<p>p2p信贷系统是一个实名认证平台，p2p信贷系统会保证用户信息隐私的安全，用户在平台上交流的过程中，也要时刻注意保护个人隐私，截图注意覆盖个人信息，不要透露真实姓名与住址等，以防个人信息被盗取造成损失。 </p>', '9', '1428084904', '1428084904', '0', '1', '', '0', '0', '51', '50', '', '', '', '', '', '');
INSERT INTO `%DB_PREFIX%article` VALUES ('34', '账户安全', '', '<h3>第三方资金托管</h3>\r\n<p>p2p信贷目前对客户交易资金的保管完全按照\"专户专款专用\"的标准模式进行运作，客户在p2p信贷的交易资金是可以完全放心的。</p>\r\n<ul id=\"anquan_step\" class=\"clearfix\"><li><a href=\"./#zha_stpe1\">第 1 步：牢记p2p信贷系统官方网址，警惕欺诈网站</a></li>\r\n<li><a href=\"./#zha_stpe2\">第 2 步：用邮箱注册一个p2p信贷系统账号</a></li>\r\n<li><a href=\"./#zha_stpe3\">第 3 步：设置高强度的\"登录密码\"</a></li>\r\n<li><a href=\"./#zha_stpe4\">第 4 步：多给电脑杀毒</a></li>\r\n</ul>\r\n<h3 id=\"zha_stpe1\">第 1 步：牢记p2p信贷官方网址，警惕欺诈网站</h3>\r\n<p>请牢记p2p信贷的官方网址是http://www.fanwe.com，不要点击来历不明的链接访问p2p信贷。您可将p2p信贷的官网链接添加到浏览器的收藏夹中，以便您的下次登录。</p>\r\n<h3 id=\"zha_stpe2\">第 2 步：用邮箱注册一个p2p信贷系统账号</h3>\r\n<p>您可以用一个常用的电子邮箱注册一个p2p信贷账号，您将用该邮箱接收来自p2p信贷的验证、提醒等重要通知邮件。</p>\r\n<h3 id=\"zha_stpe3\">第 3 步：设置高强度的\"登录密码\"</h3>\r\n<p>什么是高强度的登录密码，怎么设置会更安全？您在密码设置时，最好使用数字和字母混合，不要使用纯数字或纯字母。强烈建议不要把\"登录密码\"设置成生日、姓名拼音或是邮箱名，这些都是容易被他人猜到的形式。</p>\r\n<h3 id=\"zha_stpe4\">第 4 步：多给电脑杀毒</h3>\r\n<p>及时更新操作系统补丁，升级新版浏览器，安装反病毒软件和防火墙并保持更新；避免在网吧等公共场所使用网上银行，不要打开来历不明的电子邮件等。</p>\r\n<p>电脑中毒会怎么样？ 如果电脑一直中毒，病毒会容易获取您的账户信息。</p>\r\n<p>电脑怎么杀毒？ 我们推荐支付宝安全联盟，联盟提供了很多免费的杀毒软件下载。</p>', '9', '1428084904', '1428084904', '0', '1', '', '0', '0', '13', '49', '', '', '', '', '', '');
INSERT INTO `%DB_PREFIX%article` VALUES ('38', '五大重要守则', '', '<h3>安全补丁更新：</h3>\r\n<p>些常用的计算机软件的保安漏洞可被病毒作者和黑客利用，来进入那些未安装安全补丁程序的计算机，盗取资料。一旦发现这种情况，软件出版商便会推出安全补丁程序来堵塞这些漏洞。您可定期浏览软件出版商的网站，对操作系统和应用软件进行安全补丁更新。 </p>\r\n<h3>防病毒软件：</h3>\r\n<p>安装防病毒软件，定期更新软件及安装最新的病毒定义文件，有效保障计算机免受病毒侵袭。 </p>\r\n<h3>个人防火墙：</h3>\r\n<p>安装防火墙，帮助保护您的计算机系统不会在连接互联网时受到侵袭，可阻止资料在未经您授权下传入或传出您的计算机。 </p>\r\n<h3>反间谍程序：</h3>\r\n<p>间谍软件可运行在用户计算机上用以监测及收集用户上网信息的程序，能够监测和搜集用户的上网信息，比如获取您输入的个人信息，包括密码、电话号码、信用卡帐号及身份证号码。间谍软件往往作为某些服务的「免费」下载程序的一部分下载到个人计算机中，或在未经您同意或知晓的情况下被下载到您的计算机中。我们强烈建议您安装并使用较有信誉的反间谍软件产品以保护您的计算机免受间谍软件的侵害。 </p>\r\n<h3>密码设置注意事项：</h3>\r\n<p>密码是取得您的网上户口数据的钥匙，因此您必须紧记将密码妥为保密。密码可以是任何字符，包括数字、字母、特殊字符等。长度在6～16位之间，区分英文字母大小写。密码最好是包含字母、数字、特殊字符的组合，不要设置成常用数字，如：生日、电话号码等，也不要设为一个单词。密码的位数应该超过六位，经常修改密码，并为重要服务例如网上理财服务设置独立的密码。 </p>', '11', '1428084904', '1428084904', '0', '1', '', '0', '0', '49', '53', '', '', '', '', '', '');
INSERT INTO `%DB_PREFIX%article` VALUES ('36', '严格的贷前审核', '', '<div class=\"lca_coninfo\">贷前审核：<br />\r\n在客户提出借款申请后，p2p信贷系统会对客户的基本资料进行分析。通过网络、电话及其他可以掌握的有效渠道进行详实、仔细的调查。避免不良客户的欺诈风险。在资料信息核实完成后，根据个人信用风险分析系统进行评估，由经验丰富的贷款审核人员进行双重审核确认后最终决定批核结果。</div>', '10', '1428084904', '1428084904', '0', '1', '', '0', '0', '50', '51', '', '', '', '', '', '');
INSERT INTO `%DB_PREFIX%article` VALUES ('37', '完善的贷中贷后管理', '', '<div class=\"lca_coninfo\">贷中审查：<br />\r\n贷中审核人员会对借款中客户资料的有效期、资料属性及客户的还款状态进行实时监控，对客户信息变动进行更新。保持与客户的畅通联系，避免失去联系导致借款产生风险。对异常客户转入贷后管理系统。</div>\r\n    <div class=\"lca_coninfo\">贷后管理：<br />\r\n如果用户逾期未归还贷款，贷后管理部门将第一时间通过短信、电话等方式提醒用户进行还款，如果用户在5天内还未归还当期借款，p2p信贷将会联系该用户的紧急联系人、直系亲属、单位等督促用户尽快还款。如果用户仍未还款，交由专业的高级催收团队与第三方专业机构合作进行包括上门等一系列的催收工作，直至采取法律手段。</div>', '10', '1428084904', '1428084904', '0', '1', '', '0', '0', '52', '52', '', '', '', '', '', '');
INSERT INTO `%DB_PREFIX%article` VALUES ('40', '关于p2p信贷', '', '<div class=\"b\">	关于p2p信贷</div>\r\n<div>   p2p信贷(fanwe.com)是中国领先的个人对个人借贷平台(P2P lending)。p2p借贷为有资金需求和理财需求的个人搭建了一个安全、透明、稳定、高效的网络平台，以个人对个人（P2P）的方式解决借贷双方的资金需求及理财需求。用户可以在p2p借贷上获得信用评级、发布贷款请求，满足个人的资金需要；也可以把自己的闲余资金通过p2p借贷出借给信用良好的贷款者，在帮助他人的同时获得良好的资金回报率。 </div>\r\n<div class=\"b\">	p2p信贷的起源</div>\r\n<div>	p2p信贷即p2p借贷，p2p借贷的另一种解释是P2P小额贷款，是一种将小额的资金聚集起来借贷给有资金需求人群的一种商业模式。由2006年“诺贝尔和平奖”得主尤努斯教授（孟加拉国）首创。20世纪90年代以来，小额信贷在世界上很多国家兴起，形成了众多发展模式，并取得了很大成功，目前，世界上比较著名的网络小额贷款服务平台有Prosper、Zopa、Lending CLub等机构。</div>\r\n<div class=\"b\">	p2p信贷如何收费</div>\r\n<div>	<a href=\"./index.php?ctl=aboutfee\" target=\"_blank\">见费用</a>（其它页面）</div>\r\n<div class=\"b\">	在p2p信贷&nbsp;平台上的借贷受法律保护么</div>\r\n<div>	<a href=\"./index.php?ctl=aboutlaws\" target=\"_blank\">见政策法规</a>（其它页面）</div>\r\n<div class=\"b\">p2p信贷的年利率范围是怎么规定的</div>\r\n<div>p2p信贷目前的利率范围为6 %-24%。在p2p信贷平台上借贷的最高年利率设定为同期银行贷款年利率的4倍。且随着银行贷款利率的调整，p2p信贷&nbsp;的利率上限也将随之调整。</div>\r\n<div>	注：</div>\r\n<div>	1.&nbsp;p2p信贷&nbsp;的利率的调整会在商业银行贷款年利率调整后1个月内进行调整。</div>\r\n<div>	2. 在利率调整之前的成功的借款不受调整的影响。</div>\r\n<div class=\"b\">	用户资料的隐私和安全如何保证</div>\r\n<div>   保护用户的隐私和数据安全是p2p信贷&nbsp;最重要的责任之一。在传输层中,我们采用加密数据进行传输以防止网络分包被截获造成数据泄露。用户的所有的信息都储存在由防火墙保护的服务器里，这些安全防护措施都是7*24小时不间断运转的。数据根据敏感性的不同，以128位或256位的形式用SSL加密。另外，p2p信贷&nbsp;还启用了侵扰监测系统来监测那些内部和外部易受侵扰的路径，并运用其他一些人工措施来加强保护用户数据的安全。</div>\r\n<div class=\"b\">	我的借款会不会受p2p信贷&nbsp;运营变动而变动</div>\r\n<div>p2p信贷及其运营公司的任何变动，均不影响已通过p2p信贷&nbsp;所达成的任何贷款协议的合法性、有效性。借款人有义务继续按照贷款协议的约定按时、足额偿还理财人的贷款本金和利息。否则理财人有权依据贷款合同的约定采取必要的法律手段追究借款人的法律责任。</div>', '14', '1428084904', '1428084904', '0', '1', '', '0', '0', '0', '2', '', '', '', '', '', '');
INSERT INTO `%DB_PREFIX%article` VALUES ('41', '常用名词解释', '', '<div class=\"b\">	借款人：</div>\r\n<div>	也称贷款人，是指已经或准备在网站上进行贷款活动的用户。</div>\r\n<div>   凡22周岁以上的中国大陆地区公民，都可以成为借款人。</div>\r\n<div class=\"b\">	理财人：</div>\r\n<div>	也称投资人，是指已经或准备在网站上进行出借活动的用户。</div>\r\n<div>   凡18周岁以上的中国大陆地区公民，都可以成为理财人。 </div>\r\n<div class=\"b\">	借款列表：</div>\r\n<div>	当借款人成功发布一个借款请求时，我们将包含各种贷款信息的该请求称为一个借款列表。 </div>\r\n<div class=\"b\">	招标：</div>\r\n<div>	是指一个借款人发出一个贷款请求，即创建一个借款列表的行为。 </div>\r\n<div class=\"b\">	投标：</div>\r\n<div>   是指理财人将其账户内指定的金额出借给其指定的借款列表的行为。 </div>\r\n<div class=\"b\" id=\"b_2\">	满标：</div>\r\n<div>	是指一个借款列表在投标期限内足额筹集到所需资金。 </div>\r\n<div class=\"b\">	放款：</div>\r\n<div>	指一个借款列表满标后且已符合放款标准，p2p借贷将把所筹资金打入借款人在p2p借贷的账户中。即成功贷款。</div>\r\n<div class=\"b\">	流标：</div>\r\n<div>	是指一个借款列表的投标期限已过，但是贷款没有足额筹集齐，即贷款失败。 </div>\r\n<div class=\"b\">	投标金额：</div>\r\n<div>	是指理财人对借款列表进行投标的金额。</div>\r\n<div class=\"b\">	帐户总额：</div>\r\n<div>	指用户账户的所有金额（含冻结金额和可用金额）。</div>\r\n<div class=\"b\">	可用金额：</div>\r\n<div>	是指用户可自由支配的金额。</div>\r\n<div class=\"b\">	冻结金额：</div>\r\n<div>	用户对某借款列表进行投标后，在此列表的招标期结束之前，这部分投标金额将被锁定，“冻结金额”是指因这类型锁定金额的总额。如果列表放款，这部分金额将转给该列表的借款人；如果该列表流标，这部分金额将立即变为用户的可用金额。 </div>\r\n<div class=\"b\" id=\"b_1\">	等额本息：</div>\r\n<div>	定义：等额本息还款法是一种被广泛采用的还款方式，在还款期内，每月偿还同等数额的贷款(包括本金和利息)。借款人每月还款额中的本金比重逐月递增、利息比重逐月递减。 </div>\r\n<div>	其计算公式如下：</div>\r\n<div>	每月还款额=[贷款本金×月利率×（1+月利率）^还款总期数]÷[（1+月利率）^还款总期数-1]<br />\r\n因计算中存在四舍五入，最后一期还款金额与之前略有不同。</div>\r\n<div class=\"b\">	什么是加权收益率</div>\r\n<div>	由于借款时间不定，p2p借贷采用加权平均的方式计算借出者的收益率，具体公式如下：</div>\r\n<div>	加权收益率 = (本金1*收益率1+本金2*收益率2+本金3*收益率3 + ......本金n*收益率n-坏账计提+罚息)/(本金1+本金2+本金3 + ......本金n)</div>\r\n<div>	注：</div>\r\n<div>	1. 此公式未计算可能的资金闲置期</div>', '14', '1428084904', '1428084904', '0', '1', '', '0', '0', '0', '1', '', '', '', '', '', '');
INSERT INTO `%DB_PREFIX%article` VALUES ('39', '避免私下交易', '', '<p>p2p信贷系统建议用户避免尝试私下交易。私下交易的约束力极低，不受《合同法》的保护，造成逾期的风险非常高，同时您的个人信息将有可能被泄漏，存在遭遇诈骗甚至受到严重犯罪侵害的隐患。 </p>', '11', '1428084904', '1428084904', '0', '1', '', '0', '0', '55', '54', '', '', '', '', '', '');
INSERT INTO `%DB_PREFIX%article` VALUES ('42', '贷款利率、期限、方式', '', '<div class=\"b\">	贷款金额有限制吗</div>\r\n<div>	信用贷款金额最高为人民币100万元，信用审核员会根据您提交的p2p借贷认证材料情况给予您信用额度（即您可以申请贷款金额的上限）。<br />\r\n机构担保贷款与智能理财标贷款视合作机构资质及实际情况设定贷款金额。</div>\r\n<div class=\"b\">	p2p信贷的贷款期限和还款方式 </div>\r\n<div>	目前信用贷款您可以选择的借款期限3个月、6个月、9个月、12个月、18个月、24个月 。还款方式是按月<a href=\"./help.html#b_1\">等额本息</a>还贷。</div>\r\n<div class=\"b\">	在p2p信贷贷款是否需要抵押、担保</div>\r\n<div>   不需要，在p2p信贷上的贷款都是以借款人的信用评级为基础的，不涉及到抵押物及其他人的担保，免去了繁杂的手续和可能由抵押物引起的纠纷。 </div>\r\n<div class=\"b\">	作为借款人，我的贷款利率是如何确定的</div>\r\n<div>	借款用户可跟据自身情况，参考网站对各信用等级借款人设定的相应贷款利率下限及网站近期的成交贷款利率自行设定可接受的利率。在同等情况下，贷款利率越高的贷款，成功（<a href=\"./help.html#q2\">满标</a>）的概率越大。</div>\r\n<div class=\"b\">	如何查询我的贷款协议</div>\r\n<div>	1.	如果您已在网上成功的贷款贷款，您可以在网站上访问“我的p2p信贷”-“偿还贷款”-“贷款协议”；</div>\r\n<div>	2.	如果您已在网上成功的投标理财，您可以在网站上访问“我的p2p信贷”-“我的投资”-“回收中的贷款”-“贷款协议”；</div>\r\n<div>	3.	您也可以访问“我要贷款”-“工具箱”-查看“<a href=\"./borrow/toolsPage.action?type=tools_contact\" target=\"_blank\">贷款协议范本</a>”。</div>', '15', '1428084904', '1428084904', '0', '1', '', '0', '0', '0', '10', '', '', '', '', '', '');
INSERT INTO `%DB_PREFIX%article` VALUES ('43', '如何申请贷款', '', '<div class=\"b\">	如何在p2p信贷上申请贷款</div>\r\n<div>	第一步：免费注册为p2p信贷用户<br />\r\n	第二步：填写个人信息、发布借款列表<br />\r\n	第三步：上传信用认证材料<br />\r\n	第四步：筹集并获得贷款<br />\r\n</div>', '15', '1428084904', '1428084904', '0', '1', '', '0', '0', '0', '9', '', '', '', '', '', '');
INSERT INTO `%DB_PREFIX%article` VALUES ('44', '发布借款列表', '', '<div class=\"b\">	如何发布借款列表</div>\r\n<div>	借款人访问“我要贷款”，选择“申请贷款”，根据提示填写贷款信息，在确认无误后点击发布即可创建一条<a href=\"./lend/lendPage.action\" target=\"_blank\">借款列表</a>。</div>\r\n<div>	注：您通过p2p信贷认证后发布的相关信息越完整，资料越丰富，获得贷款的机会就会越高。</div>\r\n  <div class=\"b\">	借款列表状态</div>\r\n<div>	借款人发布借款列表后，在未将合格的认证资料上传完毕并获得p2p信贷给出的信用等级和信用额度（全部通过四项必要信用认证）前，借款列表将处于“提交资料”状态，不能被任何理财人投标。在借款人全部通过四项必要信用认证且得到信用等级和信用额度后，借款列表将进入“马上投标”状态，即开始筹集贷款。</div>\r\n<div class=\"b\">	是否可以修改已发布的借款列表</div>\r\n<div>	不可以。在用户发布借款列表之后，不可以对已发布的贷款进行修改。请用户在发布贷款时务必认真填写各项信息。</div>', '15', '1428084904', '1428084904', '0', '1', '', '0', '0', '0', '8', '', '', '', '', '', '');
INSERT INTO `%DB_PREFIX%article` VALUES ('45', '申请信用额度', '', '<div class=\"b\">	什么是信用额度 </div>\r\n<div>	借款人在全部通过四项“必要信用认证”后，可以继续上传材料进行各项“可选信用认证”，在人人贷审核员完成对所提供材料的审核工作后，借款人会从人人贷获得一个相应的信用等级和信用额度。信用额度既是借款人单笔贷款的上限也是借款者累积尚未还清贷款的上限。例如，如果一个借款人信用额度为5万元，则在没有其他贷款的情况下，用户可以发布总额最高为5万元的贷款请求，也可以分多次发布贷款请求，但尚未还清贷款（以整笔贷款金额计算）的总额不得超过5万元。</div>\r\n<div class=\"b\">	什么时候可以申请信用额度、如何申请</div>\r\n<div>	成为借款人后，您在上传认证资料进行各项信用认证后可以先得到一个信用额度暂不发布贷款列表，在您需要贷款的时候，用相应额度发布贷款需求。</div>', '15', '1428084904', '1428084904', '0', '1', '', '0', '0', '0', '7', '', '', '', '', '', '');
INSERT INTO `%DB_PREFIX%article` VALUES ('46', 'p2p信贷认证', '', '<div class=\"b\">	借款人必要信用认证</div>\r\n<div>	贷款必须全部通过的认证项目，不可缺少。（为了保证审核效率，确保贷款用户尽快通过审核，我们将优先为四项必要认证材料上传齐全的用户提供审核服务。）。</div>\r\n<div style=\"padding-left:0px;\"> <div class=\"b\">	身份证认证</div>\r\n<div>	您上传的身份证扫描件需和您绑定的身份证一致，否则将无法通过认证。认证说明：<br />\r\n 1、请您上传您本人第二代身份证正、反两面照片。<br />\r\n 2、请确认您上传的资料是清晰的、未经修改的数码照片（可以是彩色扫描图片）。<br />\r\n<p style=\"font-weight:bold;\">每张图片的尺寸不大于3M。</p>\r\n认证有效期： 永久</div>\r\n <div class=\"b\">	工作认证 </div>\r\n<div>	您的工作状况是p2p信贷评估您信用状况的主要依据。请您填写真实可靠的工作信息。认证说明：<br />\r\n 上传资料说明：<br />\r\n如果您满足以下 1种以上的身份，例如：您有稳定工作，且兼职开淘宝店。我们建议您同时上传两份资料，这将有助于提高您的借款额度和信用等级 <p style=\"font-weight:bold;\">1、 工薪阶层：</p>\r\n请上传以下资料的照片或扫描件：（建议两项或两项以上）<br />\r\na) 劳动合同。 <br />\r\nb) 加盖单位公章的在职证明。<br />\r\n c) 带有姓名及照片的工作证。<br />\r\n <p style=\"font-weight:bold;\">2、 私营企业主:</p>\r\n请上传以下全部三项资料的照片或扫描件：<br />\r\n a) 企业的营业执照。 <br />\r\nb) 企业的税务登记证。 <br />\r\nc) 店面照片（照片内需能看见营业执照）。<br />\r\n <p style=\"font-weight:bold;\">3、网商：</p>\r\n请上传以下资料的照片或扫描件：<br />\r\n a) 请上传网店主页和后台的截屏(需要看清网址）。<br />\r\n b) 支付宝（或其他第三方支付工具）的至少3张最近3个月的商户版成功交易记录的截屏图片。 <br />\r\nc) 营业执照（如果有的话提供，不是必须的）。 <br />\r\nd) 备注：如果是淘宝专职卖家，店铺等级必须为3钻以上（含3钻）。<br />\r\n 认证条件： 工薪阶层需入职满6个月，私营企业主和淘宝商家需经营满一年<br />\r\n 认证有效期： 6个月 <br />\r\n</div>\r\n<div class=\"b\">	个人信用报告认证 </div>\r\n<div>个人信用报告是由中国人民银行出具，全面记录个人信用活动，反映个人信用基本状况的文件。本报告是p2p信贷了解您信用状况的一个重要参考资料。 您信用报告内体现的信用记录，和信用卡额度等数据，将在您发布借款时经p2p信贷工作人员整理，在充分保护您隐私的前提下披露给p2p信贷借出者，作为借出者投标的依据。<br />\r\n        认证说明： <br />\r\n1、上传您的个人信用报告原件的照片，每页信用报告需独立照相，并将整份信用报告按页码先后顺序完整上传。<br />\r\n 2、请确认您上传的资料是清晰的、未经修改的数码照片或扫描图片。 <br />\r\n认证条件： 信用记录良好 <br />\r\n认证有效期： 6个月 <br />\r\n<p style=\"font-weight:bold;\">如何办理个人信用报告？</p>\r\n 办理个人信用报告，可由本人或委托他人到中国人民银行征信中心办理本人的信用报告，其中被委托人称为代理人。 <br />\r\n（一）本人查询信用报告 <br />\r\n携带本人有效身份证件原件及一份复印件（其中复印件留给征信分中心备查），到中国人民银行各地征信分中心查询。 有效身份证件包括：身份证（第二代身份证须复印正反两面）、军官证、士兵证、护照、港澳居民来往内地通行证、 台湾同胞来往内地通行证、外国人居留证等。 <br />\r\n（二）委托他人查询信用报告 <br />\r\n需要携带的材料有委托人及代理人双方的身份证件原件及复印件、授权委托书。其中身份证件复印件和授权委托书留给 征信分中心备查。委托书格式请与当地征信分中心联系取得。<br />\r\n 全国各地征信中心联系方式查询 <br />\r\n<a href=\"http://www.pbccrc.org.cn/kefuzhongxin_303.html\" target=\"_blank\">http://www.pbccrc.org.cn/kefuzhongxin_303.html</a><br />\r\n 认证有效期： 6个月 <br />\r\n</div>\r\n<div class=\"b\">	收入认证 </div>\r\n您的银行流水单以及完税证明，是证明您收入情况的主要文件，也是p2p信贷评估您还款能力的主要依据之一。<br />\r\n认证说明： <br />\r\n上传资料说明：<br />\r\n如果您满足以下 1种以上的身份，例如：您有稳定工作，且兼职开淘宝店。<br />\r\n 我们建议您同时上传两份资料，这将有助于提高您的借款额度和信用等级。<br />\r\n <p style=\"font-weight:bold;\">1、 工薪阶层：</p>\r\n<p>请上传以下一项或多项资料：<br />\r\n a) 最近连续六个月工资卡银行流水单的照片或扫描件，须有银行盖章，或工资卡网银的电脑截屏。<br />\r\n b) 最近连续六个月的个人所得税完税凭证。 <br />\r\nc) 社保卡正反面原件的照片以及最近连续六个月缴费记录（注：可在社保网站查询到为准）。<br />\r\n d) 如果工资用现金形式发放，请提供近半年的常用银行储蓄账户流水单，须有银行盖章，或工资卡网银的电脑截屏。 <br />\r\n</p>\r\n<p style=\"font-weight:bold;\">2、 私营企业主:</p>\r\n请上传以下一项或多项资料的照片或扫描件：<br />\r\n a) 最近连续六个月个人银行卡流水单，须有银行盖章，或网银的电脑截屏。 <br />\r\nb) 最近连续六个月企业银行流水单，须有银行盖章；或近半年企业的纳税证明。<br />\r\n <p style=\"font-weight:bold;\">3、 网商：</p>\r\n请上传以下全部两项资料的照片或扫描件：<br />\r\n a) 最近连续六个月个人银行卡流水单，须有银行盖章，或网银的电脑截屏。<br />\r\nb) 如果是淘宝商家请上传近半年淘宝店支付宝账户明细的网银截图。 <br />\r\n认证条件： 收入需较稳定，如果是私营企业主及淘宝商家月均流水需在20000以上 <br />\r\n认证有效期： 6个月 <br />\r\n如何办理银行流水单？ <br />\r\n银行流水单是银行卡最近一段时间内的资金交易记录，是确认借入者收入状况的重要证明，银行流水单 是p2p借贷用以评价借入者还款能力的重要凭证之一。 <br />\r\n<p style=\"font-weight:bold;\">办理流程</p>\r\n 前往最近的工资卡所在银行，提供工资卡及本人身份证原件，要求银行打印工资卡最近半年的流水单。<br />\r\n 如何办理个人所得税完税证明？<br />\r\n一、委托公司会计代办 <br />\r\n联系所在公司（负责申报个人所得税）会计，委托会计办理需提交纳税人有效身份证件原件及复印件、受托人有效身份证件原件及 复印件及委托协议原件，并填写《开具个人所得税完税证明申请表》即可。<br />\r\n 二、本人办理 <br />\r\n1、向所在公司会计咨询公司进行缴税的税务所。<br />\r\n 2、本人携带有效身份证件原件及复印件，前往公司缴税的税务所，填写《开具个人所得税完税证明申请表》即可领取完税证明。 <br />\r\n<br />\r\n                              </div>\r\n    <div class=\"b\">	可选信用认证 </div>\r\n您可以选择上传的认证项目，只要通过了这些可选信用认证，将会提高您的“信用等级”和“信用额度” 。 <br />\r\n<div style=\"padding-left:0px;\"> <div class=\"b\">房产证明 </div>\r\n房产证明是证明借入者资产及还款能力的重要凭证,p2p信贷会根据借款者提供的房产证明给与借入者一定的信用加分。 <br />\r\n 认证说明： 1、请上传以下任意一项或多项资料。  <br />\r\na) 购房合同。  <br />\r\nb) 银行按揭贷款合同。 <br />\r\n c) 房产局产调单及收据。 <br />\r\n 2、请确认您上传的资料是清晰的、未经修改的数码照片或扫描图片。 <br />\r\n认证条件： 必须是商品房  <br />\r\n每张图片的尺寸不大于1.5M。 <br />\r\n 认证有效期： 永久  <br />\r\n<div class=\"b\">购车证明 </div>\r\n购车证明是证明借入者资产及还款能力的重要凭证之一，p2p信贷会根据借入者提供的购车证明给与借入者一定的信用加分。         <br />\r\n认证说明：  <br />\r\n1、请上传您所购买车辆行驶证原件的照片。  <br />\r\n2、请上传您和您购买车辆的合影（照片须体现车牌号码）。  <br />\r\n3、请确认您上传的资料是清晰的、未经修改的数码照片或扫描图片。  <br />\r\n认证有效期： 永久  <br />\r\n<div class=\"b\">结婚证明</div>\r\n 借入者的婚姻状况的稳定性，是p2p信贷考核借款人信用的评估因素之一，通过p2p信贷结婚认证，您将获得一定的信用加分。  <br />\r\n认证说明： 1、请您上传以下资料  <br />\r\n1) 您结婚证书原件的照片 <br />\r\n 2) 您配偶的身份证原件的照片。如果持有第二代身份证，请上传正反两面  <br />\r\n照片。如果持有第一代身份证，仅需上传正面照片。  <br />\r\n3) 您和配偶的近照合影一张  <br />\r\n2、请确认您上传的资料是清晰的、未经修改的数码照片或扫描图片。  <br />\r\n认证条件： 您的配偶同意您将其个人资料提供给p2p借贷  <br />\r\n认证有效期： 永久  <br />\r\n<div class=\"b\">学历认证 </div>\r\n借出者在选择借款列表投标时，借入者的学历也是一个重要的参考因素。为了让借出者更好、更快地相信您的学历是真实的，强烈建议您对学历进行在线验证。  <br />\r\n学历认证方法：  <br />\r\n一、2001年至今获得学历，需学历证书编号 <br />\r\n1、点击 <a href=\"http://www.chsi.com.cn/xlcx/\" target=\"_blank\">网上学历查询。</a>  <br />\r\n2、选择“零散查询”。  <br />\r\n3、输入证书编号、查询码（通过手机短信获得，为12位学历查询码）、姓名、以及验证码进行查询。  <br />\r\n4、查询成功后，您将查获得《教育部学历证书电子注册备案表》。  <br />\r\n5、将该表右下角的12位在线验证码<a href=\"./public/images/xueli_1.jpg\" target=\"_blank\">（见样本图01）</a>，输入下面的文本框。  <br />\r\n6、点击提交审核。  <br />\r\n二、1991年至今获得学历，无需学历证书编号 <br />\r\n1、点击 <a href=\"http://www.chsi.com.cn/xlcx/\" target=\"_blank\"> 网上学历查询</a>。  <br />\r\n2、选择“本人查询”。  <br />\r\n3、注册学信网账号。  <br />\r\n4、登录学信网，点击“学历信息”。  <br />\r\n5、选择您的最高学历，并点击“申请验证报告”（申请过程中，您需通过手机短信获得12位学历查询码，此查询码与p2p借贷所需验证码不同）。  <br />\r\n6、申请成功后，您将获得12位在线验证码<a href=\"./public/images/xueli_2.jpg\" target=\"_blank\">（见样本图02）</a>  <br />\r\n7、将12位在线验证码输入下面的文本框  <br />\r\n8、点击提交审核  <br />\r\n认证条件： 大专或以上学历（普通全日制）  <br />\r\n认证有效期： 永久  <br />\r\n技术职称认证  <br />\r\n技术职称是经专家评审、反映一个人专业技术水平并作为聘任专业技术职务依据的一种资格，不与工资挂钩，是p2p信贷考核借款人信用的评估因素之一，通过p2p信贷技术职称认证证明，您将获得一定的信用加分。  <br />\r\n认证说明：  <br />\r\n1、请上传您的技术职称证书原件照片。  <br />\r\n2、请确认您上传的资料是清晰的、未经修改的数码照片或扫描图片。 <br />\r\n认证条件： 国家承认的二级及以上等级证书。例如律师证、会计证、工程师证等  <br />\r\n每张图片的尺寸不大于1.5M。  <br />\r\n认证有效期： 永久  <br />\r\n<div class=\"b\">视频认证 </div>\r\n什么是视频认证？只有通过视频认证您才能获得贷款，您只需要在视频认证页面上传您本人的视频，并提交，即可申请视频认证。您也可以选择与p2p信贷客服在线上进行视频认证。&nbsp;</div>\r\n<div style=\"padding-left:0px;\">认证说明：  <br />\r\n1、视频中诵读内容请按如下文字诵读： 我是 ***，我在p2p信贷的用户名是***，我的身份证号是 ***********************，现在我正在做p2p信贷的视频确认。我在此做出以下承诺：我愿意接受p2p信贷的使用条款和借款协议；我提供给p2p信贷的信息及资料均是真实有效的；我愿意对我在p2p信贷上的行为承担全部法律责任；在我未能按时归还借款时，我同意p2p信贷采取法律诉讼、资料曝光等一切必要措施。&nbsp;</div>\r\n<div style=\"padding-left:0px;\"> <span style=\"color:red;\">2、读完声明后，请您将身份证正面(有身份证号)对准摄像头，并保持5秒</span><br />\r\n3、您可以联系右侧的在线客服进行视频确认。如果您选择在线进行视频确认，在做完以上声明后，我们的视频审核员会与您进行几分钟简单的交流。<br />\r\n4、请按上述内容要求录制视频，如不符合要求均不能通过视频认证 <br />\r\n(1)上传视频认证文件大小 不得超过50M<br />\r\n(2)请上传真实有效的本人的视频。<br />\r\n(3)视频文件格式可以是RMVB、WMV 或 AVI类型的文件。<br />\r\n(4).视频认证必须图像清晰，声音清楚<br />\r\n(5).视频认证必须衣冠整洁，禁止出现抽烟，赤裸等形象<br />\r\n只有通过视频认证您才能获得贷款，您只需要在视频认证页面将视频文件发送至与您对应客服的邮箱，并勾选\'使用邮件发送\'后提交,即可进行视频认证  <br />\r\n认证有效期： 永久  <br />\r\n<div class=\"b\">手机详单认证 </div>\r\n手机流水单是最近一段时间内的详细通话记录，是p2p借贷用以验证借入者真实性的重要凭证之一。您的手机详单不会以任何形式被泄露。   <br />\r\n认证说明：  <br />\r\n1、请您上传您绑定的手机号码最近3个月的手机详单原件的照片。如详单数量较多可分月打印并上传  <br />\r\n每月前5日部分（每月详单均需清晰显示机主手机号码）。  <br />\r\n2、请确认您上传的资料是清晰的、未经修改的数码照片（不可以是扫描图片）。  <br />\r\n每张图片的尺寸不大于1.5M。  <br />\r\n认证有效期： 永久  <br />\r\n如何办理手机详单  <br />\r\n前往最近的手机运营商营业厅，提供手机服务密码或机主身份证明即可打印。 <br />\r\n<div class=\"b\">居住地证明</div>\r\n 居住地的稳定性，是p2p信贷考核借款人的主要评估因素之一，通过p2p信贷居住地证明，您将获得一定的信用加分。&nbsp;</div>\r\n<div style=\"padding-left:0px;\">认证说明：  <br />\r\n1、请上传以下任何一项可证明现居住地址的证明文件原件的照片。  <br />\r\n1) 用您姓名登记的水、电、气最近三期缴费单；  <br />\r\n2) 用您姓名登记固定电话最近三期缴费单；  <br />\r\n3) 您的信用卡最近两期的月结单；  <br />\r\n4) 您的自有房产证明；  <br />\r\n5) 您父母的房产证明，及证明您和父母关系的证明材料。  <br />\r\n2、请确认您上传的资料是清晰的、未经修改的数码照片（不可以是扫描图片）。  <br />\r\n每张图片的尺寸不大于1.5M。  <br />\r\n认证有效期： 6个月  <br />\r\n<!-- <div class=\"b\">实地审核申请</div>\r\n 实地审核申请是p2p借贷了解您信用状况的一个重要参考资料。您的实地审核申请通过审核后，将在您发布借款时经p2p借贷工作人员整理，在充分保护您隐私的前提下披露给p2p借贷借出者，作为借出者投标的依据。  <br/>\r\n申请资格：  <br/>\r\n1、目前实地审核申请仅对经营类客户开放。  <br/>\r\n2、借款申请额在3万以上。  <br/>\r\n3、申请人必须已经提交四项必要信用认证资料。  <br/>\r\n4、申请人名下有固定资产，经营时间满一年。 <br/>\r\n注：目前仅对北京六环以内地区开放  <br/>\r\n借款人认证资料说明  <br/>\r\np2p借贷\"对资料的认证仅限于如下方式：  <br/>\r\n1. 对用户提交的书面资料的照片文件进行真实性审查。  <br/>\r\n2. 对用户上传的照片资料的内容与其申报的信息的一致性审查。  <br/>\r\n3. p2p借贷承诺在认证中保持中立、审慎的态度，但p2p借贷不对借款人的行为提供任何保证。  <br/>\r\n4. p2p借贷的认证不能避免出贷款项出现违约的风险，亦不免除借款人应当承担的各种责任。 <br/>\r\n --></div>', '15', '1428084904', '1428084904', '0', '1', '', '0', '0', '0', '6', '', '', '', '', '', '');
INSERT INTO `%DB_PREFIX%article` VALUES ('47', '认证项目说明', '', '<div class=\"b\">p2p信贷对资料的认证仅限于如下方式：</div>\r\n<div>   1. 对用户提交的书面资料的照片文件进行真实性审查。</div>\r\n<div>	2. 对用户上传的照片资料的内容与其申报的信息的一致性审查。 </div>\r\n<div>   3. p2p信贷贷承诺在认证中保持中立、审慎的态度，但P2P借贷不对借款人的行为提供任何保证。</div>\r\n<div>   4. p2p信贷的认证不能避免出贷款项出现违约的风险，亦不免除借款人应当承担的各种责任。</div>', '15', '1428084904', '1428084904', '0', '1', '', '0', '0', '0', '5', '', '', '', '', '', '');
INSERT INTO `%DB_PREFIX%article` VALUES ('48', '信用等级和信用额度', '', '<div class=\"b\">	什么是信用等级 ：</div>\r\n<div>p2p信贷认证体系包括信用等级和信用额度。信用等级是借款人的信用属性，也是理财人判断借款人违约风险的重要依据之一。通常来讲借款人信用等级越高，其违约率越低，相应的其贷款成功率越高。信用等级由认证分数转化而来，具体请参考信用等级的分数区间。目前认证等级的等级由高到低分为：AA、A、B、C、D、E、HR。</div>\r\n<div class=\"b\">	信用等级的分数区间：</div>\r\n<div>	每个信用等级都有一个信用分数的范围，具体可以见下表：</div>\r\n<div>	<table width=\"692\" border=\"0\" cellspacing=\"1\" style=\"text-align:center;background:#000;\">		<tbody><tr style=\"background:#fff;\">			<td width=\"15%\">信用等级</td>\r\n 			<td width=\"10%\">HR</td>\r\n			<td width=\"10%\">E</td>\r\n			<td width=\"10%\">D</td>\r\n			<td width=\"10%\">C</td>\r\n			<td width=\"10%\">B</td>\r\n			<td width=\"10%\">A</td>\r\n			<td width=\"10%\">AA</td>\r\n		</tr>\r\n		<tr style=\"background:#fff;\">			<td>分数区间</td>\r\n			<td>0-99</td>\r\n			<td>100-109</td>\r\n			<td>110-119</td>\r\n			<td>120-129</td>\r\n			<td>130-144</td>\r\n			<td>145-159</td>\r\n			<td>160+</td>\r\n		</tr>\r\n	</tbody>\r\n</table>\r\n</div>\r\n<div class=\"b\">	什么是信用额度</div>\r\n<div>	借款人在全部通过四项“必要信用认证”后，可以继续上传材料进行各项“可选信用认证”，在p2p信贷审核员完成对所提供材料的审核工作后，借款人会从p2p信贷获得一个相应的信用等级和信用额度。信用额度既是借款人单笔贷款的上限也是借款者累积尚未还清贷款的上限。例如，如果一个借款人信用额度为5万元，则在没有其他贷款的情况下，用户可以发布总额最高为5万元的贷款请求，也可以分多次发布贷款请求，但尚未还清贷款（以整笔贷款金额计算）的总额不得超过5万元。</div>\r\n<div class=\"b\">	如何获得信用等级及信用额度</div>\r\n<div>	访问“我要贷款”—“p2p信贷认证”，根据提示完成四项“必要信用认证”及用户根据自身情况可自己可完成的“可选信用认证”后，即可获得信用等级及信用额度。</div>\r\n<div class=\"b\">	如何查看我的信用等级</div>\r\n<div>	访问“我的p2p信贷”——“我的p2p信贷”——“p2p信贷认证”，您就可以看到您的信用等级，及您进行的各种信用认证的情况。</div>\r\n<div class=\"b\">	如何提高我的信用等级（分数）</div>\r\n<div>   您可以通过申请“可选信用认证“来提高您的信用分数。另外您在p2p信贷网站上良好的借贷记录也会增加您的信用分数。</div>\r\n<div style=\"font-style:italic;\">	注：提高信用等级可以增加理财人贷款成功的概率，提高贷款成功率，我们建议借款人在贷款前根据实际情况尽量多完成信用认证。</div>\r\n<div class=\"b\">	为什么我获得了信用等级与信用额度，但我无法申请贷款</div>\r\n<div>	您无法申请贷款的原因可能是由于没有全部通过四项“必要信用认证”。<br />\r\n	如果还有问题请在工作时间联系在线客服或拨打网站服务热线：400-888-8888</div>\r\n<div class=\"b\">	我某一项信用认证被驳回，是否可以进行重新认证？</div>\r\n<div>   如果您在某项信用认证中，资料提交发生错误或资料需要更新，您可以重新进行该项信用认证。</div>\r\n   <div class=\"b\">   为什么我提交的附加信用认证，3个工作日后还没有收到结果？</div>\r\n<div>  只有当您通过全部的必要信用认证后，p2p信贷才会认证您的可选信用认证信息。所以您没有收到结果的原因可能是您没有通过全部的必要信用认证。</div>', '15', '1428084904', '1428084904', '0', '1', '', '0', '0', '0', '4', '', '', '', '', '', '');
INSERT INTO `%DB_PREFIX%article` VALUES ('49', '视频确认、贷款成功', '', '<div class=\"b\">	什么是视频确认</div>\r\n<div>	为了保证贷款的真实有效，借款人在发布借款需求后需及时进行视频确认。视频确认的内容包括：借款人对自我身份的认证及宣读贷款承诺书。</div>\r\n<div class=\"b\">	视频确认的时间及形式</div>\r\n<div>	形式1：在贷款列表出于资料审核状态（“等待材料”状态），在借款列表进入筹款阶段（“马上投标”状态），借款人可与对应信用审核员约定视频确认时间，进行时时视频通话进行确认。</div>\r\n<div>	形式2：在借款列表进入筹款阶段（“马上投标”状态），借款人可按网站规定格式、大小及内容自行录制确认视频后上传至该地址，上传后等待信用审核工作人员查看并予以最终确认。</div>\r\n<div class=\"b\">	什么是贷款成功</div>\r\n<div>	理财人发布借款列表后，须至少通过四项必要信用认证并，此后借款人会得到相应的信用等级和信用额度，此时借款人的借款列表可被理财人投标，即开始筹集贷款。当借款列表满标且借款人通过视频确认后，p2p信贷信审工作组会对借款列表做最后的判定，当您的借款列表得到放款批准时即为贷款成功，您所筹集的资金将划入您的p2p信贷账户中。</div>\r\n <div class=\"b\">	贷款成功后如何提取现金</div>\r\n<div>	当一个借款列表得到放款批准即为贷款成功后，p2p信贷将会将您筹集到的资金划入您的p2p借贷账户，您可在网站上点击“我的p2p信贷”—“充值提现”，填写提现信息。资金将在申请提现后1~2个工作日内到达您指定的银行账户。为了保障您的资金尽快到账，请您使用提现页面推荐的全国性银行申请提现。</div>', '15', '1428084904', '1428084904', '0', '1', '', '0', '0', '0', '3', '', '', '', '', '', '');
INSERT INTO `%DB_PREFIX%article` VALUES ('50', '还款', '', '<div class=\"b\">	如何还款</div>\r\n<div>	若账户中余额大于当期还款在还款日系统会代贷款者自动扣缴当期还款，若借款人需要手动还款，请访问“我的p2p信贷”——“贷款管理”——“偿还贷款”。</div>\r\n<div>	若账户余额不足支付当期还款，可以通过网银为<a href=\"./#q18\" target=\"_self\">账户充值</a>。</div>\r\n<div class=\"b\">	借入者能否提前还款</div>\r\n<div>	1.	借款人可以随时进行提前还款。具体的操作方法是访问“我的p2p信贷”——“贷款管理”——“偿还贷款”，点击右上角的“还清此笔贷款”。</div>\r\n<div>	2.	提提前还款的用户需要支付给理财人贷款剩余本金的1%作为违约金，不用再支付后续的利息及管理费用。公式如下： </div>\r\n<div>	提前还款应还金额= 剩余本金+ 当期本息及账户管理费+提前还款违约金</div>\r\n<div>	请用户注意提前归还部分贷款不视为提前还款，仍需支付全部贷款利息及账户管理费。 </div>', '15', '1428084904', '1428084904', '0', '1', '', '0', '0', '0', '2', '', '', '', '', '', '');
INSERT INTO `%DB_PREFIX%article` VALUES ('51', '逾期还款', '', '<div class=\"b\">	如果逾期还款，会有什么惩罚</div>\r\n<div>	如果逾期还款，借款人要承担罚息与逾期后的管理费用，并会被扣去相应的信用分数。所以p2p信贷强烈建议用户避免逾期还款，一旦发生逾期请尽快还清贷款。</div>\r\n<div class=\"b\">	逾期还款超过30天，会造成什么样的后果</div>\r\n<div>	若用户违约逾期还款超过30天，p2p信贷有权将该用户的有关资料正式备案在“不良信用记录”，列入全国个人信用评级体系的黑名单（“不良信用记录”数据将为银行、电信、担保公司、人才中心等有关机构提供个人不良信用信息）此不良记录将保存7年。同时保留对该用户采取法律措施的权利，由此所产生的所有法律后果和费用（包括但不限于律师费）将由该用户来承担。</div>\r\n<div class=\"b\">	贷款逾期后的罚息如何计算</div>\r\n<div>	自逾期开始之后，正常利息停止计算。按照下面公式计算罚息：</div>\r\n<div>	罚息总额 = 逾期本息×对应罚息利率×逾期天数；</div>\r\n<div>	<table width=\"692\" border=\"0\" cellspacing=\"1\" style=\"text-align:center;background:#000;\">		<tbody><tr style=\"background:#fff;\">			<td>逾期天数</td>\r\n			<td>1—30天</td>\r\n			<td>31天以上</td>\r\n		</tr>\r\n		<tr style=\"background:#fff;\">			<td>罚息利率</td>\r\n			<td>0.05%</td>\r\n			<td>0.1%</td>\r\n		</tr>\r\n	</tbody>\r\n</table>\r\n</div>\r\n<div class=\"b\">	贷款逾期后的管理费用如何计算</div>\r\n<div>	自逾期开始之后，正常管理费用停止计算。按照下面公式计算：</div>\r\n<div>	逾期后管理费总额=逾期本息×对应逾期管理费率×逾期天数；</div>\r\n<div>	<table width=\"692\" border=\"0\" cellspacing=\"1\" style=\"text-align:center;background:#000;\">		<tbody><tr style=\"background:#fff;\">			<td>逾期天数</td>\r\n			<td>1—30天</td>\r\n			<td>31天以上</td>\r\n		</tr>\r\n		<tr style=\"background:#fff;\">			<td>逾期管理费率</td>\r\n			<td>0.1%</td>\r\n			<td>0.5%</td>\r\n		</tr>\r\n	</tbody>\r\n</table>\r\n</div>', '15', '1428084904', '1428084904', '0', '1', '', '0', '0', '0', '1', '', '', '', '', '', '');
INSERT INTO `%DB_PREFIX%article` VALUES ('52', '费用、合同、理财金额限', '', '<div class=\"b\">	理财人在p2p信贷上投资需要交哪些费用</div>\r\n<div>	理财人仅需向第三方支付平台（快钱、支付宝等）缴纳账户充值与提现的费用，p2p借贷对理财人不收取任何费用。详情请见“<a href=\"./index.php?ctl=aboutfee\" target=\"_blank\">费用</a>”。</div>\r\n<div class=\"b\">	电子合同有效吗</div>\r\n<div>	我国新合同法第十一条规定: 书面形式合同是指合同书、信件和数据电文(包括电报、电传、传真、电子数据交换和电子邮件)等可以有形地表现所载内容的形式。因此电子合同属于书面形式的合同一种，是受到法律保护的。另外p2p信贷平台中的合同文本都由经济法律师起草的，保证了经过这个平台的交易是具备法律效力的。</div>\r\n<div class=\"b\">	理财人的理财金额是否有限制 </div>\r\n<div>	理财人的理财金额没有上限。</div>\r\n<div class=\"b\">p2p信贷现在是否提供线下借贷服务</div>\r\n<div>	目前p2p信贷暂不提供线下借贷业务，用户需在网上完成交易。</div>', '16', '1428084904', '1428084904', '0', '1', '', '0', '0', '0', '5', '', '', '', '', '', '');
INSERT INTO `%DB_PREFIX%article` VALUES ('53', '开始理财', '', '<div class=\"b\">	如何开始理财</div>\r\n<div>	访问“我要理财”— 根据提示，填写相应的信息。通过“身份证绑定”、“手机绑定”，即可成为理财人。</div>\r\n<div class=\"b\">	理财人为什么要做身份绑定？有什么好处</div>\r\n<div>	为了保障理财人资金的安全性，将p2p信贷账号实名绑定，以及保证理财人是年满18岁的具有完全民事行为能力的中国公民，从而保证借贷合同的有效性，p2p信贷要求所有理财人必须通过身份证认证。身份认证过程简便，且认证信息只用于核实用户身份，P2P借贷对于所有资料将严格保密。</div>', '16', '1428084904', '1428084904', '0', '1', '', '0', '0', '0', '4', '', '', '', '', '', '');
INSERT INTO `%DB_PREFIX%article` VALUES ('54', '投标', '', '<div class=\"b\">	投标前我需要注意的事项：</div>\r\n<div>	1、认真阅读该借款列表的详细信息（包括贷款金额，利息率，贷款期限、贷款者信用等级等），以确定您所要投的标符合您的风险承受能力和所要求的投资回报率。</div>\r\n<div>	2、应知道若您所投标的借款人发生违约，违约损失需要由该标的所有理财人共同承担。</div>\r\n<div>	3、投标前请您核实自己将要理财的金额，确认无误后再进行操作。</div>\r\n<div>	4、您也可以参考网站的“<a href=\"./index.php?ctl=usagetip\" target=\"_blank\">使用技巧</a>”。</div>\r\n<div class=\"b\">	如何分散投资风险</div>\r\n<div>	1.	请尽量分散您的投资，这样可以降低单一借款人违约对您的投资收益的影响。例如您可以把5000元借给10个借款人。</div>\r\n<div>	2.	请在同等收益的情况下投标给<a href=\"./index.php?ctl=helpcenter#q48\">信用等级</a>较高的借款人。</div>\r\n<div>	3.	浏览网站关于<a href=\"./index.php?ctl=usagetip\" target=\"_blank\">使用技巧</a>的文章。</div>\r\n<div class=\"b\">	信用等级的高低代表什么</div>\r\n<div>	一般情况下，用户信用等级越高，表明个人信用越好，借款违约的可能性越小。</div>\r\n<div class=\"b\">	投标后是否可以取消</div>\r\n<div>	您在投标后不允许取消，若此标<a href=\"./index.php?ctl=helpcenter#q41\">满标</a>并放款后，您账户上的冻结金额自动将转入该标借款人账户。若此标<a href=\"./index.php?ctl=helpcenter#q41\">流标</a>，则您账户上的<a href=\"./index.php?ctl=helpcenter#q41\">冻结金额</a>自动转为用户可用金额。</div>', '16', '1428084904', '1428084904', '0', '1', '', '0', '0', '0', '3', '', '', '', '', '', '');
INSERT INTO `%DB_PREFIX%article` VALUES ('55', '收取还款', '', '<div class=\"b\">	如何收取还款</div>\r\n<div>	借款人在规定的还款时间内将钱充值到与p2p信贷合作的第三方支付平台上，p2p信贷系统会发送邮件通知所有理财人。理财人可按个人需求选择<a href=\"./index.php?ctl=helpcenter#q51\">提现</a>，或继续投资（出借）。</div>\r\n<div>	注：借款人也可能提前偿还全部贷款或者在到期日前手动提前偿还贷款，请理财人注意查收p2p信贷发出的通知。</div>\r\n<div class=\"b\">	借款人能否提前还款 </div>\r\n<div>	借款人可以提前偿还贷款，如果借款人提前偿还全部贷款，需要额外支付给理财人贷款剩余本金的1%作为违约金，不用再支付后续的利息及管理费用。公式如下：</div>\r\n<div>	提前还款应还金额= 剩余本金+ 当期本息及账户管理费+提前还款违约金</div>', '16', '1428084904', '1428084904', '0', '1', '', '0', '0', '0', '2', '', '', '', '', '', '');
INSERT INTO `%DB_PREFIX%article` VALUES ('56', '坏账', '', '<div class=\"b\">	借款人逾期后再还款，理财人可以收到罚息吗</div>\r\n<div>	是的，请参考 “<a href=\"./index.php?ctl=helpcenter#q51\">贷款逾期后的罚息如何计算</a>”。</div>\r\n<div class=\"b\">	借款人逾期还款，p2p信贷将如何帮助理财人催收贷款</div>\r\n<div>p2p信贷有一套完善的催收机制，通过电话短信提醒、上门拜访、法律诉讼等多种方式，有效的保证了平台上绝大部分借款人的及时还款。延迟还款的借款人需按约定交纳罚息和违约金。</div>', '16', '1428084904', '1428084904', '0', '1', '', '0', '0', '0', '1', '', '', '', '', '', '');
INSERT INTO `%DB_PREFIX%article` VALUES ('57', '智能理财标', '', '<div class=\"b\">	什么是智能理财标？</div>\r\n<div>	智能理财标是p2p信贷推出的全新理财产品系列。产品基于高保障的战略合作，精选大量质押、抵押贷款需求和供应链金融服务相关贷款需求供理财人选择。投资智能理财标，理财者的本金与利息将受到保障。回款方式与投资期限均更为灵活，当前智能理财标为每月还息，到期还本，之后还会逐步开发更具灵活性的系列化产品。在安全稳定、灵活便捷的前提下，保证了较高的理财收益。</div>\r\n<div class=\"b\">	为什么智能理财标的额度为百万级别？</div>\r\n<div>	1、精挑渠道，细选借款用户，此类用户信用更优，还款能力更强；2、质押物、抵押物、供应链金融各环节等大幅增进借款人的信用级别，提高违约代价，同时p2p信贷与渠道合作有更强的处理此类贷款逾期的能力；3、日常实时监控更具保障力度。</div>\r\n<div class=\"b\">	为什么说可以提高资金使用率？</div>\r\n<div>   因为智能理财标前期已经通过严格的审核流程，所以智能理财标满标当日即可放款，无需更多等待时间。并且智能理财标的回款方式更为灵活，本金回款更为集中（当前为每月还息，到期还本），省去了循环投标的操作，投资期间无需操心，坐享收益。</div>\r\n<div class=\"b\">	如果智能理财标逾期，p2p信贷如何处理？</div>\r\n<div>	如果智能理财标发生逾期，但借款人在逾期的30天内进行了还款，借款人在偿还理财人应得利息的基础上，将额外支付罚息； 如果逾期30天后贷款人没有进行还款，在第31天时，p2p信贷风险准备金将赔付理财人应得的所有本金及利息，并额外赔偿30天（等待期）的利息予理财人，相应的，该标债权将由理财人转移至p2p信贷风险准备金。之后该债权收回的全部资金将归p2p信贷风险准备金所有。对于智能理财标，p2p信贷将收取年化1%-2%的服务费（不同合作渠道会有所不同）全部存于风险准备金账户用于p2p信贷智能理财本息保障计划。<br />\r\n	例：用户在5月1日获得贷款100万，年化利息为9%，借款期限3个月。理财人全体应在6月1日、7月1日得到利息7,500元，并在8月1日获得7,500+100万元（每月付息，到期还本）。如果贷款人在6月1日未按时还款，并一直没有偿还应还款项，理财人手中债权会在7月1日（逾期后第31天）转移到p2p借贷手中，并获得当月应得利息（7,500元）、额外赔偿的30天利息（7,500元）以及本金（100万元）。获赔的1,015,000元将根据理财人投资比例立即打到理财人的p2p借贷账户中。</div>\r\n<div class=\"b\">	借款期限都有哪些选择？</div>\r\n<div>	经过科学的分析并进一步贴近贷款者及理财人的需求，智能理财标的期限目前最低至1个月，以自然月为单位，最高12个月，具有极强的流动性。后续的，更具灵活性的产品也会针对具体的实际贷款需求和理财人需求相应开发。</div>\r\n<div class=\"b\">   什么是供应链金融？</div>\r\n<div>   供应链金融是指基于对供应链管理程度和核心企业信用资质的掌握，对供应链核心企业和上下游多个环节的企业提供的金融产品和服务。该种灵活的融资模式会根据特定产品供应链上的历史交易数据和贸易特性，以企业间贸易行为所产生的确定未来现金流为直接还款来源。</div>\r\n<div class=\"b\">   应收账款保障产品是什么？</div>\r\n<div>   应收账款保障产品是p2p信贷智能理财标系列的供应链金融产品之一。该产品以供应链上下游企业间历史交易数据为审核依据，以借款企业特定账户中的应收账款作为保障。p2p信贷风险管理部门会严格定期监控该特定应收账款账户的资金流动情况，一旦借款用户逾期，该账户中的届时所有资金及未来待收资金将全部用于偿还所有未清偿本息。</div>\r\n<div class=\"b\">   仓单保障产品是什么？</div>\r\n<div>   仓单保障产品是p2p信贷智能理财标系列的供应链金融产品之一。该产品以供应链上下游企业间物流环节的流通数据为审核依据，以借款企业在p2p信贷委托仓库内的在仓货物作为保障。p2p借贷风险管理部门会严格定期监控委托仓库内在仓货物流通及价格变动情况。一旦借款用户逾期，p2p信贷会在委托仓储方的协助下，对于在仓货物进行处置变卖，变卖后的资金将全部用于偿还所有未清偿本息。</div>', '17', '1428084904', '1428084904', '0', '1', '', '0', '0', '0', '4', '', '', '', '', '', '');
INSERT INTO `%DB_PREFIX%article` VALUES ('58', '机构担保标', '', '<div class=\"b\">	什么是机构担保标？</div>\r\n<div>   是指p2p信贷的合作伙伴为相应的借款提供连带保证，并负有连带保证责任。所谓连带保证责任即连带保证人对债务人负连带责任，无论主债务人的财产是否能够清偿债务，债权人均可向保证人要求其履行保证义务。</div>\r\n<div class=\"b\">p2p信贷如何选择合作或机构？</div>\r\n<div>	通过严格的考察，p2p信贷会选择具备丰富的个人信用审核经验、较长经营历史及雄厚的资金实力的机构合作展开该项业务。</div>\r\n<div class=\"b\">p2p信贷有什么手段防范合作伙伴带来的风险？</div>\r\n<div>p2p信贷与合作伙伴签订了合作协议，一旦合作伙伴违背其应承担的连带保证责任，p2p信贷有权通过法律手段进行追偿，同时p2p信贷会通过自有的严格的审核系统对合作伙伴的相关业务进行双重审核，严控风险。</div>\r\n<div class=\"b\">	机构担保标与网站个人发布的借款的区别是什么？</div>\r\n<div>   由于有合作伙伴为借款人提供担保，机构担保标能够为理财人提供本息保障；筹标时间为四天，缩短借款、理财人双方的资金流转周期；满标后立即放款，可提高审核效率和放款速度。</div>\r\n<div class=\"b\">	如果机构担保标逾期，p2p信贷如何处理？</div>\r\n<div>	合作机构承担借款的连带保证责任，若此类借款逾期，合作机构将代借款人偿付此笔借款的本息及相关费用。</div>\r\n<div class=\"b\">	借款人的资质如何审核？</div>\r\n<div>	借款人通过合作伙伴在全国各地的网点进行借款申请，由合作伙伴对其借款资质进行全面审核，审核内容包括但不限于借款人信用报告、收入、财产的资料审核和实地考察等，p2p信贷负责通过自己的风控手段进行复审。</div>', '17', '1428084904', '1428084904', '0', '1', '', '0', '0', '0', '3', '', '', '', '', '', '');
INSERT INTO `%DB_PREFIX%article` VALUES ('59', '实地认证标', '', '<div class=\"b\">什么是“实地认证标”？</div>\r\n<div>   “实地认证标”是p2p信贷推出的一款全新产品。该产品延续了p2p信贷借贷求真务实的经营理念，在原有严格审核的基础上，增加了p2p信贷前端工作人员对借款人情况的实地走访、审核调查以及后续的贷中、贷后服务环节，进一步加强风险管理控制，达到了双重保障的效果。</div>\r\n<div class=\"b\">“实地认证标”与“信用认证标”的区别是什么？</div>\r\n<div>“实地认证标”相对“信用认证标”增添了实地认证审核，进一步保障了理财用户资金安全；同时采用本息保障的赔付方式，使得您理财更加省心、放心。</div>\r\n<div class=\"b\">   “实地认证标”如果产生坏账，将如何垫付？</div>\r\n<div>p2p信贷风险备用金将对此类产品的本金和利息提供双重保障。一旦该产品借款发生逾期，p2p信贷风险备用金将优先代借款人偿付此笔借款的本息及相关费用。</div>', '17', '1428084904', '1428084904', '0', '1', '', '0', '0', '0', '2', '', '', '', '', '', '');
INSERT INTO `%DB_PREFIX%article` VALUES ('60', '充值方式', '', '<div class=\"b\">	如何给我的账户充值</div>\r\n<div>	<div>1.进入“我的p2p信贷-充值提现页面”，输入要充值的金额，点击充值按钮，将弹出国通付或快钱付款界面。</div>\r\n	<div>2.选择付款银行，点击确认无误按钮，按提示完成付款。</div>\r\n	<div>3.	付款后，显示成功付款后，跳转到p2p信贷充值页面，显示充值成功。</div>\r\n	<div>4.	您可以通过资金记录查看充值的金额及历史记录。</div>\r\n</div>', '18', '1428084904', '1428084904', '0', '1', '', '0', '0', '0', '3', '', '', '', '', '', '');
INSERT INTO `%DB_PREFIX%article` VALUES ('61', '如何充值', '', '<div class=\"b\">	如何给我的账户充值</div>\r\n<div>	<div>1.进入“我的p2p信贷-充值提现页面”，输入要充值的金额，点击充值按钮，将弹出国付通或快钱付款界面。</div>\r\n	<div>2.选择付款银行，点击确认无误按钮，按提示完成付款。</div>\r\n	<div>3.	付款后，显示成功付款后，跳转到p2p信贷充值页面，显示充值成功。</div>\r\n	<div>4.	您可以通过资金记录查看充值的金额及历史记录。</div>\r\n</div>', '18', '1428084904', '1428084904', '0', '1', '', '0', '0', '0', '2', '', '', '', '', '', '');
INSERT INTO `%DB_PREFIX%article` VALUES ('62', '提现', '', '<div class=\"b\">	如何提取现金</div>\r\n<div>	<div>您可以随时将您在“p2p信贷”账号中的可用余额申请提现到您现有的任何一家银行的账号上。 </div>\r\n	<div>注意：请确保您填写的银行账号的开户人姓名和您在p2p信贷上提供的身份证上的真实姓名一致，并提供一张可以申请提现的银行账号，否则提现无法成功。 </div>\r\n</div>\r\n<div class=\"b\">	在申请取现时，如何填写正确的开户行支行名称</div>\r\n<div>	<div>用户在申请取现时，一定要确保自己填写的开户行支行名称正确。 </div>\r\n	<div>一般的开户行支行名称都是由 xx银行+xx分行+xx支行 组成的。 </div>\r\n	<div>如：</div>\r\n	<div>招商银行上海分行新客站支行 </div>\r\n	<div>工商银行四川省攀枝花市分行 </div>\r\n	<div>农业银行深圳分行长城支行 </div>\r\n	<div>交通银行北京崇文门支行 </div>\r\n	<div>如果用户不清楚开户行支行名称，可以打电话到当地所在银行的营业网点询问或者登录该银行网站查找。 </div>\r\n</div>\r\n<div class=\"b\">	为什么我申请取现失败？ </div>\r\n<div>	<div>造成您取现失败的原因可能有以下几种： </div>\r\n	<div> ·&nbsp;p2p信贷账号未绑定本人身份证 </div>\r\n	<div> · 银行开户行信息错误 </div>\r\n	<div> · 银行账号/户名错误，或是账号和户名不符 </div>\r\n	<div> · 使用信用卡提现 </div>\r\n	<div> · 银行账户冻结或正在办理挂失 </div>\r\n	<div>如果遇到以上情况，我们会在收到支付机构转账失败的的通知后解除您的资金冻结（手续费不退还），请您不必担心资金安全。 </div>\r\n</div>', '18', '1428084904', '1428084904', '0', '1', '', '0', '0', '0', '1', '', '', '', '', '', '');
INSERT INTO `%DB_PREFIX%article` VALUES ('63', '注册登录', '', '<div class=\"b\">	如何成为p2p信贷注册用户</div>\r\n<div>	<div>1.	进入p2p信贷首页，点击右上角“免费注册”按钮。</div>\r\n	<div>2.	根据提示信息，填写您的注册邮箱、密码，点击“免费注册”。</div>\r\n	<div>3.	注册邮箱为用户的用户名，为后续接收站内消息及账单的唯一渠道，注册后此邮箱不可修改。</div>\r\n</div>\r\n<div class=\"b\">	如何登录p2p信贷</div>\r\n<div>	<div>注册成为p2p信贷用户后,您就可以使用账号登录p2p借贷了：</div>\r\n	<div>1.	进入p2p信贷首页，点击右上角“登录”按钮。</div>\r\n	<div>2.	输入您的账号(电子邮件)及密码，点击“登录”按钮。</div>\r\n</div>\r\n<div class=\"b\">	如何找回我的密码</div>\r\n<div>	<div>如果您遗忘了密码，可以在网站用户登录页面，点击“忘记密码了”按钮，填写自己的Email地址信息找回密码。</div>\r\n	<div>1.	点击首页“登录”---点击“忘记密码”。</div>\r\n	<div>2.	填写您当时注册的邮箱，点击“发送到我的邮箱”。</div>\r\n	<div>3.	提示邮件发送成功。</div>\r\n	<div>4.	根据提示到您的邮箱中将会收到一封标题为“重设密码”的信件,点击信中的链接。</div>\r\n	<div>5.	根据提示密码重置成功。</div>\r\n</div>\r\n<div class=\"b\">	设置密码</div>\r\n<div>	您注册时输入的密码可以是数字或者英文字母，对于英文字母我们同时设置了大小写的区分。注册后您可以随时在“我的p2p借贷”--“个人设置”—“修改密码”中修改您所设定的密码。</div>\r\n<div class=\"b\">	账户密码安全常识</div>\r\n<div>	<div>请仔细阅读以下常见的安全措施：</div>\r\n	<div>1.	密码要保密，且定期更改。</div>\r\n	<div>2.	请使用较长、复杂的字母数字组合以提高密码强度，不要用生日等容易被别人猜中的密码。</div>\r\n	<div>3.	如果使用了密码取回功能，请您及时删除我们发送给您的载有密码的邮件。</div>\r\n	<div>4.	请记住不要与任何人共享您的密码。如果您已经与他人共享了密码，我们强烈建议您尽快更改。</div>\r\n	<div>5.	您可能在不经意间使计算机受到病毒、木马、间谍软件、广告软件的感染，这些软件也可能记录并盗走您的密码！所以，请安装和使用杀毒软件。</div>\r\n</div>', '19', '1428084904', '1428084904', '0', '1', '', '0', '0', '0', '3', '', '', '', '', '', '');
INSERT INTO `%DB_PREFIX%article` VALUES ('64', '我的p2p信贷', '', '<div class=\"b\">	如何修改个人资料</div>\r\n<div>	访问“我的p2p信贷”— “个人设置”—“修改个人信息”-，即可编辑个人资料。</div>\r\n<div class=\"b\">	如何取消p2p信贷的邮件和站内信的提醒</div>\r\n<div>	访问“我的p2p信贷”— “个人设置”—“通知设置”-，即可修改。</div>', '19', '1428084904', '1428084904', '0', '1', '', '0', '0', '0', '2', '', '', '', '', '', '');
INSERT INTO `%DB_PREFIX%article` VALUES ('65', '意见反馈、举报', '', '<div class=\"b\">	对网站有任何建议或意见，如何反馈</div>\r\n<div>	<div>您可以发站内信给用户“p2p信贷客服”，我们会及时给您回馈。</div>\r\n	<div>发站内信请访问网站上“我的p2p信贷”-“我的p2p信贷”-“站内信”。</div>\r\n</div>\r\n<div class=\"b\">	遇到其他用户恶意骚扰怎么办</div>\r\n<div>	如遇到情况严重的恶意骚扰，请举报该用户给p2p信贷工作人员。我们核实后会尽快采取处理措施。</div>\r\n<div class=\"b\">	遇到别人盗用我的资料假冒我怎么办</div>\r\n<div>	如遇到此类情况，您可以联系p2p借贷的在线客服或拨打我们的客服热线400-888-8888来向我们说明情况，我们会尽快采取处理措施，以确保您信息和资金的安全。</div>\r\n<div class=\"b\">	如何截屏并保存图片</div>\r\n<div>	<div>一、使用PrintScreen键</div>\r\n	<div>1、首先，在出现想要截的画面的时候，按下键盘上的PrintScreen键。</div>\r\n	<div>2、在\"开始\"→\"所有程序\"→\"附件\"选\"画图\"，这会打开Windows系统自带的画图程序(mspaint.exe)选择\"编辑\"→\"粘贴\"(或者直接用Ctrl+V热键)，将截下来的图粘贴到画图程序中。</div>\r\n	<div>3、将图片进行保存，选择\"文件\"→\"另存为\"，在出现的另存为对话框中选择保存类型为 JPEG（*.JPG;*.JPEG;*.JPE;*.JFIF），然后输入文件名，点击保存。</div>\r\n	<div>二、使用QQ截屏</div>\r\n	<div>1、打开QQ对话框。</div>\r\n	<div>2、点击Ctrl+Alt+A键，用鼠标选取想截屏的画面。</div>\r\n	<div>3、点击“保存”，在出现的对话框中选择保存类型为 JPEG（*.JPG;*.JPEG;），然后输入文件名，点击保存。</div>\r\n	<div>三、使用其他截屏工具</div>\r\n	<div>说明： 请不要用任何其他图片编辑软件编辑。如果发现用户存在用其他图片编辑软件的情况，您将会被加入系统黑名单。</div>\r\n</div>', '19', '1428084904', '1428084904', '0', '1', '', '0', '0', '0', '1', '', '', '', '', '', '');
INSERT INTO `%DB_PREFIX%article` VALUES ('68', '担保机构登录', '', '', '2', '1423102102', '1423102195', '0', '1', 'index.php?ctl=manageagency&act=login', '0', '0', '0', '59', '', '', '', '担保机构登录', '', '');
INSERT INTO `%DB_PREFIX%article` VALUES ('69', '授权服务机构登录', '', '', '2', '1423102163', '1423102163', '0', '1', 'index.php?ctl=authorized&act=login', '0', '0', '0', '60', '', '', '', '授权服务机构登录', '', '');

-- ----------------------------
-- Table structure for `%DB_PREFIX%article_cate`
-- ----------------------------
DROP TABLE IF EXISTS `%DB_PREFIX%article_cate`;
CREATE TABLE `%DB_PREFIX%article_cate` (
  `id` int(11) NOT NULL auto_increment,
  `title` varchar(255) NOT NULL COMMENT '分类名称',
  `brief` varchar(255) NOT NULL COMMENT '分类简介(备用字段)',
  `pid` int(11) NOT NULL COMMENT '父ID，程序分类可分二级',
  `is_effect` tinyint(4) NOT NULL COMMENT '有效性标识',
  `is_delete` tinyint(4) NOT NULL COMMENT '删除标识',
  `type_id` tinyint(1) NOT NULL COMMENT '型 0:普通文章（可通前台分类列表查找到） 1.帮助文章（用于前台页面底部的站点帮助） 2.公告文章（用于前台页面公告模块的调用） 3.系统文章（自定义的一些文章，需要前台自定义一些入口链接到该文章） 所属该分类的所有文章类型与分类一致',
  `sort` int(11) NOT NULL,
  PRIMARY KEY  (`id`),
  KEY `pid` (`pid`),
  KEY `type_id` (`type_id`),
  KEY `sort` (`sort`)
) ENGINE=MyISAM AUTO_INCREMENT=21 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of fanwe_article_cate
-- ----------------------------
INSERT INTO `%DB_PREFIX%article_cate` VALUES ('1', '使用帮助', '', '0', '1', '0', '1', '4');
INSERT INTO `%DB_PREFIX%article_cate` VALUES ('2', '关于我们', '', '0', '1', '0', '1', '3');
INSERT INTO `%DB_PREFIX%article_cate` VALUES ('3', '安全保护', '', '0', '1', '0', '1', '2');
INSERT INTO `%DB_PREFIX%article_cate` VALUES ('4', '了解更多', '', '0', '1', '0', '1', '1');
INSERT INTO `%DB_PREFIX%article_cate` VALUES ('5', '网站公告', '网站公告', '0', '1', '0', '2', '5');
INSERT INTO `%DB_PREFIX%article_cate` VALUES ('6', '使用技巧', '', '0', '1', '0', '3', '6');
INSERT INTO `%DB_PREFIX%article_cate` VALUES ('7', '关于理财', '', '0', '1', '0', '3', '7');
INSERT INTO `%DB_PREFIX%article_cate` VALUES ('8', '本金保障', '<img src=\"./public/attachment/201303/02/17/5131c1ed03587.jpg\"/>', '12', '1', '0', '3', '8');
INSERT INTO `%DB_PREFIX%article_cate` VALUES ('9', '交易安全保障', '<img src=\"./public/attachment/201303/02/17/5131c20d5db34.jpg\"/>', '12', '1', '0', '3', '9');
INSERT INTO `%DB_PREFIX%article_cate` VALUES ('10', '贷款审核与保障', '<img src=\"./public/attachment/201303/02/17/5131c2460b952.jpg\"/>\r\np2p信贷拥有一套科学有效的信用审核标准和方法，对借款用户进行信用风险分析及信用等级分级。同时p2p信贷建立了完整严谨的风险管理体系包括贷前审核、贷中审查和贷后管理以控制借款逾期违约的风险。从创立开始，p2p信贷借款逾期率一直保持在0.9%以内，为业内最优水平。', '12', '1', '0', '3', '10');
INSERT INTO `%DB_PREFIX%article_cate` VALUES ('11', '网上理财安全建议', '<img src=\"./public/attachment/201303/02/17/5131c25681025.jpg\"/>', '12', '1', '0', '3', '11');
INSERT INTO `%DB_PREFIX%article_cate` VALUES ('12', '安全保障', '', '0', '1', '0', '3', '12');
INSERT INTO `%DB_PREFIX%article_cate` VALUES ('13', '帮助中心', '', '0', '1', '0', '3', '13');
INSERT INTO `%DB_PREFIX%article_cate` VALUES ('14', '基本介绍', '', '13', '1', '0', '3', '19');
INSERT INTO `%DB_PREFIX%article_cate` VALUES ('15', '借款人常见问题', '', '13', '1', '0', '3', '18');
INSERT INTO `%DB_PREFIX%article_cate` VALUES ('16', '理财人常见问题', '', '13', '1', '0', '3', '17');
INSERT INTO `%DB_PREFIX%article_cate` VALUES ('17', '产品及计划介绍', '', '13', '1', '0', '3', '16');
INSERT INTO `%DB_PREFIX%article_cate` VALUES ('18', '账户充值、提现', '', '13', '1', '0', '3', '15');
INSERT INTO `%DB_PREFIX%article_cate` VALUES ('19', '其他', '', '13', '1', '0', '3', '14');
INSERT INTO `%DB_PREFIX%article_cate` VALUES ('20', '联系我们', '', '2', '1', '1', '1', '20');

-- ----------------------------
-- Table structure for `%DB_PREFIX%auto_cache`
-- ----------------------------
DROP TABLE IF EXISTS `%DB_PREFIX%auto_cache`;
CREATE TABLE `%DB_PREFIX%auto_cache` (
  `cache_key` varchar(100) NOT NULL default '' COMMENT '程序中识别的缓存唯一ID',
  `cache_type` varchar(100) NOT NULL COMMENT '缓存接口类型',
  `cache_data` text NOT NULL COMMENT '缓存值',
  `cache_time` int(11) NOT NULL COMMENT '缓存时间',
  PRIMARY KEY  (`cache_key`,`cache_type`),
  KEY `cache_type` (`cache_type`),
  KEY `cache_key` (`cache_key`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of fanwe_auto_cache
-- ----------------------------

-- ----------------------------
-- Table structure for `%DB_PREFIX%bank`
-- ----------------------------
DROP TABLE IF EXISTS `%DB_PREFIX%bank`;
CREATE TABLE `%DB_PREFIX%bank` (
  `id` int(11) NOT NULL auto_increment,
  `name` varchar(255) NOT NULL COMMENT '银行名称',
  `is_rec` tinyint(1) NOT NULL COMMENT '是否推荐',
  `day` int(11) NOT NULL COMMENT '处理时间',
  `sort` int(11) NOT NULL COMMENT '银行排序',
  `icon` varchar(255) default NULL COMMENT '图标',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=41 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of fanwe_bank
-- ----------------------------
INSERT INTO `%DB_PREFIX%bank` VALUES ('1', '中国工商银行', '1', '3', '0', './public/bank/1.jpg');
INSERT INTO `%DB_PREFIX%bank` VALUES ('2', '中国农业银行', '1', '3', '0', './public/bank/2.jpg');
INSERT INTO `%DB_PREFIX%bank` VALUES ('3', '中国建设银行', '1', '3', '0', './public/bank/3.jpg');
INSERT INTO `%DB_PREFIX%bank` VALUES ('4', '招商银行', '1', '3', '0', './public/bank/4.jpg');
INSERT INTO `%DB_PREFIX%bank` VALUES ('5', '中国光大银行', '1', '3', '0', './public/bank/5.jpg');
INSERT INTO `%DB_PREFIX%bank` VALUES ('6', '中国邮政储蓄银行', '1', '3', '0', './public/bank/6.jpg');
INSERT INTO `%DB_PREFIX%bank` VALUES ('7', '兴业银行', '1', '3', '0', './public/bank/7.jpg');
INSERT INTO `%DB_PREFIX%bank` VALUES ('8', '中国银行', '0', '3', '0', './public/bank/8.jpg');
INSERT INTO `%DB_PREFIX%bank` VALUES ('9', '交通银行', '0', '3', '3', './public/bank/9.jpg');
INSERT INTO `%DB_PREFIX%bank` VALUES ('10', '中信银行', '0', '3', '0', './public/bank/10.jpg');
INSERT INTO `%DB_PREFIX%bank` VALUES ('11', '华夏银行', '0', '3', '0', './public/bank/11.jpg');
INSERT INTO `%DB_PREFIX%bank` VALUES ('12', '上海浦东发展银行', '0', '3', '1', './public/bank/12.jpg');
INSERT INTO `%DB_PREFIX%bank` VALUES ('13', '城市信用社', '0', '3', '0', './public/bank/13.jpg');
INSERT INTO `%DB_PREFIX%bank` VALUES ('14', '恒丰银行', '0', '3', '0', './public/bank/14.jpg');
INSERT INTO `%DB_PREFIX%bank` VALUES ('15', '广东发展银行', '0', '3', '0', './public/bank/15.jpg');
INSERT INTO `%DB_PREFIX%bank` VALUES ('16', '深圳发展银行', '0', '3', '2', './public/bank/16.jpg');
INSERT INTO `%DB_PREFIX%bank` VALUES ('17', '中国民生银行', '0', '3', '0', './public/bank/17.jpg');
INSERT INTO `%DB_PREFIX%bank` VALUES ('18', '中国农业发展银行', '0', '3', '0', './public/bank/18.jpg');
INSERT INTO `%DB_PREFIX%bank` VALUES ('19', '农村商业银行', '0', '3', '0', './public/bank/19.jpg');
INSERT INTO `%DB_PREFIX%bank` VALUES ('20', '农村信用社', '0', '3', '0', './public/bank/20.jpg');
INSERT INTO `%DB_PREFIX%bank` VALUES ('21', '城市商业银行', '0', '3', '0', './public/bank/21.jpg');
INSERT INTO `%DB_PREFIX%bank` VALUES ('22', '农村合作银行', '0', '3', '0', './public/bank/22.jpg');
INSERT INTO `%DB_PREFIX%bank` VALUES ('23', '浙商银行', '0', '3', '0', './public/bank/23.jpg');
INSERT INTO `%DB_PREFIX%bank` VALUES ('24', '上海农商银行', '0', '3', '0', './public/bank/24.jpg');
INSERT INTO `%DB_PREFIX%bank` VALUES ('25', '中国进出口银行', '0', '3', '0', './public/bank/25.jpg');
INSERT INTO `%DB_PREFIX%bank` VALUES ('26', '渤海银行', '0', '3', '0', './public/bank/26.jpg');
INSERT INTO `%DB_PREFIX%bank` VALUES ('27', '国家开发银行', '0', '3', '0', './public/bank/27.jpg');
INSERT INTO `%DB_PREFIX%bank` VALUES ('28', '村镇银行', '0', '3', '0', './public/bank/28.jpg');
INSERT INTO `%DB_PREFIX%bank` VALUES ('29', '徽商银行股份有限公司', '0', '3', '0', './public/bank/29.jpg');
INSERT INTO `%DB_PREFIX%bank` VALUES ('30', '南洋商业银行', '0', '3', '0', './public/bank/30.jpg');
INSERT INTO `%DB_PREFIX%bank` VALUES ('31', '韩亚银行', '0', '3', '0', './public/bank/31.jpg');
INSERT INTO `%DB_PREFIX%bank` VALUES ('32', '花旗银行', '0', '3', '0', './public/bank/32.jpg');
INSERT INTO `%DB_PREFIX%bank` VALUES ('33', '渣打银行', '0', '3', '0', './public/bank/33.jpg');
INSERT INTO `%DB_PREFIX%bank` VALUES ('34', '华一银行', '0', '3', '0', './public/bank/34.jpg');
INSERT INTO `%DB_PREFIX%bank` VALUES ('35', '东亚银行', '0', '3', '0', './public/bank/35.jpg');
INSERT INTO `%DB_PREFIX%bank` VALUES ('36', '苏格兰皇家银行', '1', '1', '26', './public/bank/36.jpg');

-- ----------------------------
-- Table structure for `%DB_PREFIX%baofoo_acct_trans`
-- ----------------------------
DROP TABLE IF EXISTS `%DB_PREFIX%baofoo_acct_trans`;
CREATE TABLE `%DB_PREFIX%baofoo_acct_trans` (
  `id` int(11) NOT NULL auto_increment,
  `merchant_id` varchar(20) NOT NULL COMMENT '商户号',
  `terminal_id` varchar(20) NOT NULL COMMENT '终端号',
  `order_id` int(11) NOT NULL COMMENT '订单号 (唯一不可重复)',
  `ref_id` int(11) NOT NULL default '0' COMMENT '转发类型关联的id;ref_type=1时fanwe_deal_load_transfer.id',
  `ref_type` tinyint(1) NOT NULL default '0' COMMENT '转帐类型;1:债权转让',
  `payer_user_id` int(11) NOT NULL default '0' COMMENT '付款方帐号',
  `payee_user_id` int(11) NOT NULL default '0' COMMENT '收款方帐号',
  `payer_type` tinyint(1) NOT NULL default '0' COMMENT '付款方帐号类型;0为普通用户(平台的user_id) 1为商户号',
  `payee_type` tinyint(1) NOT NULL default '0' COMMENT '收款方帐号类型 0为普通用户(平台的user_id) 1为商户号',
  `amount` decimal(20,2) NOT NULL default '0.00' COMMENT '充值金额，单位：元',
  `fee_taken_on` tinyint(1) default '1' COMMENT '费用承担方(平台收取的费用),0付款方1收款方',
  `fee` decimal(20,2) default '0.00' COMMENT '该费用将会从指定费用方账户收取到平台可用账户',
  `req_time` varchar(20) default '0' COMMENT '请求时间',
  `code` varchar(20) default NULL COMMENT '结果代码',
  `msg` varchar(255) default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of fanwe_baofoo_acct_trans
-- ----------------------------

-- ----------------------------
-- Table structure for `%DB_PREFIX%baofoo_bind_state`
-- ----------------------------
DROP TABLE IF EXISTS `%DB_PREFIX%baofoo_bind_state`;
CREATE TABLE `%DB_PREFIX%baofoo_bind_state` (
  `id` int(11) NOT NULL auto_increment,
  `merchant_id` varchar(20) NOT NULL COMMENT '商户号',
  `terminal_id` varchar(20) NOT NULL COMMENT '终端号',
  `bf_account` varchar(20) NOT NULL COMMENT '手机号，快速注册时候的宝付帐号',
  `name` varchar(50) NOT NULL COMMENT '用户真实姓名',
  `id_card` varchar(50) NOT NULL COMMENT '用户身份证号',
  `user_id` int(11) NOT NULL COMMENT '用户编号(唯一做)',
  `sign` varchar(50) default NULL,
  `code` varchar(20) default NULL COMMENT '结果代码',
  `msg` varchar(255) default NULL COMMENT '绑定结果信息',
  `is_callback` tinyint(1) NOT NULL default '0',
  `create_time` int(11) default NULL,
  PRIMARY KEY  (`id`,`user_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of fanwe_baofoo_bind_state
-- ----------------------------

-- ----------------------------
-- Table structure for `%DB_PREFIX%baofoo_business`
-- ----------------------------
DROP TABLE IF EXISTS `%DB_PREFIX%baofoo_business`;
CREATE TABLE `%DB_PREFIX%baofoo_business` (
  `id` int(11) NOT NULL auto_increment,
  `merchant_id` varchar(20) NOT NULL COMMENT '商户号',
  `terminal_id` varchar(20) NOT NULL COMMENT '终端号',
  `action_type` tinyint(1) NOT NULL default '0' COMMENT '请求类型，投标为1，满标为2，流标为3，还标为4',
  `order_id` int(11) NOT NULL default '0' COMMENT '订单号 （唯一不允许重复）',
  `cus_id` int(11) NOT NULL default '0' COMMENT '标id',
  `cus_name` varchar(255) NOT NULL COMMENT '标名称',
  `brw_id` int(11) NOT NULL default '0' COMMENT '借款人user_id用户编号(唯一)',
  `req_time` varchar(20) NOT NULL default '0' COMMENT '请求时间	例如 1405668253874  （当前时间转换毫秒）',
  `user_id` int(11) NOT NULL default '0' COMMENT '用户编号',
  `amount` decimal(20,2) default '0.00' COMMENT '金额，单位：元',
  `special` int(11) default '0' COMMENT '特殊标识(还标专用), 该编号对应的用户，为还款人。如20140374',
  `fee` decimal(20,2) default '0.00' COMMENT '手续费(涉及到满标、还款接口)',
  `load_user_id` int(11) NOT NULL default '0' COMMENT '记录：投标人',
  `load_amount` decimal(20,2) NOT NULL default '0.00' COMMENT '投标金额；投标时使用',
  `repay_start_time` int(11) NOT NULL default '0' COMMENT '开始还款日期;满标放款时使用',
  `bids_msg` varchar(255) default NULL COMMENT '流标原因',
  `deal_repay_id` int(11) NOT NULL default '0' COMMENT '还款时使用,还款计划id',
  `code` varchar(20) default NULL COMMENT '结果代码',
  `msg` varchar(255) default NULL COMMENT '绑定结果信息',
  `is_callback` tinyint(1) NOT NULL default '0',
  PRIMARY KEY  (`id`,`cus_name`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of fanwe_baofoo_business
-- ----------------------------

-- ----------------------------
-- Table structure for `%DB_PREFIX%baofoo_business_detail`
-- ----------------------------
DROP TABLE IF EXISTS `%DB_PREFIX%baofoo_business_detail`;
CREATE TABLE `%DB_PREFIX%baofoo_business_detail` (
  `id` int(11) NOT NULL auto_increment,
  `pid` int(11) NOT NULL default '0',
  `user_id` int(11) NOT NULL default '0' COMMENT '用户编号',
  `amount` decimal(20,2) default '0.00' COMMENT '金额，单位：元',
  `fee` decimal(20,2) default '0.00' COMMENT '手续费(涉及到满标、还款接口)',
  `deal_load_id` int(11) NOT NULL default '0' COMMENT '投标ID',
  `deal_load_repay_id` int(11) NOT NULL default '0' COMMENT '回款计划id',
  `repay_manage_impose_money` decimal(20,2) NOT NULL default '0.00' COMMENT '平台收取 借款者 的管理费逾期罚息',
  `impose_money` decimal(20,2) NOT NULL COMMENT '投资者收取 借款者 的逾期罚息',
  `repay_status` tinyint(1) default NULL COMMENT '还款状态',
  `true_repay_time` int(11) default NULL COMMENT '还款时间',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=137 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of fanwe_baofoo_business_detail
-- ----------------------------

-- ----------------------------
-- Table structure for `%DB_PREFIX%baofoo_fo_charge`
-- ----------------------------
DROP TABLE IF EXISTS `%DB_PREFIX%baofoo_fo_charge`;
CREATE TABLE `%DB_PREFIX%baofoo_fo_charge` (
  `id` int(11) NOT NULL auto_increment,
  `merchant_id` varchar(20) NOT NULL COMMENT '商户号',
  `terminal_id` varchar(20) NOT NULL COMMENT '终端号',
  `order_id` int(11) NOT NULL COMMENT '订单号 (唯一不可重复)',
  `amount` decimal(20,2) NOT NULL default '0.00' COMMENT '充值金额，单位：元',
  `amount_fee` decimal(20,0) default '0' COMMENT '平台收取用户的手续费',
  `mer_fee` decimal(20,2) default '0.00' COMMENT '商户收取的手续费',
  `user_id` int(11) NOT NULL COMMENT '用户编号',
  `fee_taken_on` tinyint(1) default NULL COMMENT '费用承担方(宝付收取的费用),1平台2用户自担 ',
  `fee` decimal(20,2) default '0.00' COMMENT '宝付收取的手续费',
  `code` varchar(20) default NULL COMMENT '结果代码',
  `succ_time` varchar(20) default NULL COMMENT '成功时间   年月日十分秒',
  `is_callback` tinyint(1) NOT NULL default '0',
  `create_time` int(11) default NULL,
  PRIMARY KEY  (`id`,`user_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of fanwe_baofoo_fo_charge
-- ----------------------------

-- ----------------------------
-- Table structure for `%DB_PREFIX%baofoo_log`
-- ----------------------------
DROP TABLE IF EXISTS `%DB_PREFIX%baofoo_log`;
CREATE TABLE `%DB_PREFIX%baofoo_log` (
  `id` int(10) NOT NULL auto_increment,
  `code` varchar(50) NOT NULL,
  `create_date` datetime NOT NULL,
  `strxml` text,
  `html` text,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=269 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of fanwe_baofoo_log
-- ----------------------------

-- ----------------------------
-- Table structure for `%DB_PREFIX%baofoo_recharge`
-- ----------------------------
DROP TABLE IF EXISTS `%DB_PREFIX%baofoo_recharge`;
CREATE TABLE `%DB_PREFIX%baofoo_recharge` (
  `id` int(11) NOT NULL auto_increment,
  `merchant_id` varchar(20) NOT NULL COMMENT '商户号',
  `terminal_id` varchar(20) NOT NULL COMMENT '终端号',
  `order_id` int(11) NOT NULL COMMENT '订单号 (唯一不可重复)',
  `amount` decimal(20,2) NOT NULL default '0.00' COMMENT '充值金额，单位：元',
  `mer_fee` decimal(20,2) default '0.00' COMMENT '商户收取的手续费',
  `user_id` int(11) NOT NULL COMMENT '用户编号',
  `fee_taken_on` tinyint(1) default NULL COMMENT '费用承担方(宝付收取的费用),1平台2用户自担 ',
  `additional_info` varchar(255) default NULL COMMENT '其它信息',
  `incash_money` decimal(20,2) default NULL,
  `fee` decimal(20,2) default NULL,
  `code` varchar(20) default NULL COMMENT '结果代码',
  `succ_time` varchar(20) default NULL COMMENT '成功时间   年月日十分秒',
  `is_callback` tinyint(1) NOT NULL default '0',
  `create_time` int(11) default NULL,
  PRIMARY KEY  (`id`,`user_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of fanwe_baofoo_recharge
-- ----------------------------

-- ----------------------------
-- Table structure for `%DB_PREFIX%collocation`
-- ----------------------------
DROP TABLE IF EXISTS `%DB_PREFIX%collocation`;
CREATE TABLE `%DB_PREFIX%collocation` (
  `id` int(11) NOT NULL auto_increment,
  `class_name` varchar(255) NOT NULL,
  `is_effect` tinyint(1) NOT NULL,
  `name` varchar(255) NOT NULL,
  `config` text NOT NULL,
  `fee_type` tinyint(1) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=30 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of fanwe_collocation
-- ----------------------------

-- ----------------------------
-- Table structure for `%DB_PREFIX%conf`
-- ----------------------------
DROP TABLE IF EXISTS `%DB_PREFIX%conf`;
CREATE TABLE `%DB_PREFIX%conf` (
  `id` int(11) NOT NULL auto_increment,
  `name` varchar(255) NOT NULL,
  `value` text NOT NULL,
  `group_id` int(11) NOT NULL,
  `input_type` tinyint(1) NOT NULL COMMENT '配置输入的类型 0:文本输入 1:下拉框输入 2:图片上传 3:编辑器',
  `value_scope` text NOT NULL COMMENT '取值范围',
  `is_effect` tinyint(1) NOT NULL,
  `is_conf` tinyint(1) NOT NULL COMMENT '是否可配置 0: 可配置  1:不可配置',
  `sort` int(11) NOT NULL,
  `tip` text NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=234 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of fanwe_conf
-- ----------------------------
INSERT INTO `%DB_PREFIX%conf` VALUES ('1', 'DEFAULT_ADMIN', 'admin', '1', '0', '', '1', '0', '0', '');
INSERT INTO `%DB_PREFIX%conf` VALUES ('2', 'URL_MODEL', '0', '1', '1', '0,1', '1', '1', '3', '');
INSERT INTO `%DB_PREFIX%conf` VALUES ('3', 'AUTH_KEY', 'fanwe', '1', '0', '', '1', '1', '4', '');
INSERT INTO `%DB_PREFIX%conf` VALUES ('4', 'TIME_ZONE', '8', '1', '1', '0,8', '1', '1', '1', '');
INSERT INTO `%DB_PREFIX%conf` VALUES ('5', 'ADMIN_LOG', '1', '1', '1', '0,1', '0', '1', '0', '');
INSERT INTO `%DB_PREFIX%conf` VALUES ('6', 'DB_VERSION', '3.3', '0', '0', '', '1', '0', '0', '');
INSERT INTO `%DB_PREFIX%conf` VALUES ('7', 'DB_VOL_MAXSIZE', '8000000', '1', '0', '', '1', '1', '11', '');
INSERT INTO `%DB_PREFIX%conf` VALUES ('8', 'WATER_MARK', './public/attachment/201302/05/13/51109c2f2966f.png', '2', '2', '', '1', '1', '48', '');
INSERT INTO `%DB_PREFIX%conf` VALUES ('24', 'CURRENCY_UNIT', '￥', '3', '0', '', '1', '1', '21', '');
INSERT INTO `%DB_PREFIX%conf` VALUES ('10', 'BIG_WIDTH', '500', '2', '0', '', '0', '0', '49', '');
INSERT INTO `%DB_PREFIX%conf` VALUES ('11', 'BIG_HEIGHT', '500', '2', '0', '', '0', '0', '50', '');
INSERT INTO `%DB_PREFIX%conf` VALUES ('12', 'SMALL_WIDTH', '200', '2', '0', '', '0', '0', '51', '');
INSERT INTO `%DB_PREFIX%conf` VALUES ('13', 'SMALL_HEIGHT', '200', '2', '0', '', '0', '0', '52', '');
INSERT INTO `%DB_PREFIX%conf` VALUES ('14', 'WATER_ALPHA', '75', '2', '0', '', '1', '1', '53', '');
INSERT INTO `%DB_PREFIX%conf` VALUES ('15', 'WATER_POSITION', '4', '2', '1', '1,2,3,4,5', '1', '1', '54', '');
INSERT INTO `%DB_PREFIX%conf` VALUES ('16', 'MAX_IMAGE_SIZE', '3000000', '2', '0', '', '1', '1', '55', '');
INSERT INTO `%DB_PREFIX%conf` VALUES ('17', 'ALLOW_IMAGE_EXT', 'jpg,gif,png', '2', '0', '', '1', '1', '56', '');
INSERT INTO `%DB_PREFIX%conf` VALUES ('18', 'MAX_FILE_SIZE', '1', '1', '0', '', '0', '1', '0', '');
INSERT INTO `%DB_PREFIX%conf` VALUES ('19', 'ALLOW_FILE_EXT', '1', '1', '0', '', '0', '1', '0', '');
INSERT INTO `%DB_PREFIX%conf` VALUES ('20', 'BG_COLOR', '#ffffff', '2', '0', '', '0', '0', '57', '');
INSERT INTO `%DB_PREFIX%conf` VALUES ('21', 'IS_WATER_MARK', '1', '2', '1', '0,1', '1', '1', '58', '');
INSERT INTO `%DB_PREFIX%conf` VALUES ('22', 'TEMPLATE', 'blue', '3', '0', '', '1', '1', '17', '');
INSERT INTO `%DB_PREFIX%conf` VALUES ('25', 'SCORE_UNIT', '积分', '3', '0', '', '1', '1', '22', '');
INSERT INTO `%DB_PREFIX%conf` VALUES ('26', 'USER_VERIFY', '1', '4', '1', '0,1,2,3,4', '1', '1', '63', '');
INSERT INTO `%DB_PREFIX%conf` VALUES ('27', 'SHOP_LOGO', './public/attachment/201011/4cdd501dc023b.png', '3', '2', '', '1', '1', '19', '');
INSERT INTO `%DB_PREFIX%conf` VALUES ('28', 'SHOP_LANG', 'zh-cn', '3', '1', 'zh-cn,zh-tw,en-us', '1', '1', '18', '');
INSERT INTO `%DB_PREFIX%conf` VALUES ('29', 'SHOP_TITLE', 'p2p信贷', '3', '0', '', '1', '1', '13', '');
INSERT INTO `%DB_PREFIX%conf` VALUES ('30', 'SHOP_KEYWORD', 'p2p信贷—最大、最安全的网络借贷平台', '3', '0', '', '1', '1', '15', '');
INSERT INTO `%DB_PREFIX%conf` VALUES ('31', 'SHOP_DESCRIPTION', 'p2p信贷—最大、最安全的网络借贷平台', '3', '0', '', '1', '1', '15', '');
INSERT INTO `%DB_PREFIX%conf` VALUES ('32', 'SHOP_TEL', '0591-87119192', '3', '0', '', '1', '1', '23', '');
INSERT INTO `%DB_PREFIX%conf` VALUES ('35', 'INVITE_REFERRALS', '0', '0', '1', '0,1', '1', '0', '1', '');
INSERT INTO `%DB_PREFIX%conf` VALUES ('38', 'ONLINE_QQ', '', '0', '0', '', '1', '0', '25', '');
INSERT INTO `%DB_PREFIX%conf` VALUES ('39', 'ONLINE_TIME', '周一至周六 9:00-18:00', '3', '0', '', '1', '1', '26', '');
INSERT INTO `%DB_PREFIX%conf` VALUES ('40', 'DEAL_PAGE_SIZE', '10', '3', '0', '', '1', '1', '31', '');
INSERT INTO `%DB_PREFIX%conf` VALUES ('41', 'PAGE_SIZE', '10', '3', '0', '', '1', '1', '32', '');
INSERT INTO `%DB_PREFIX%conf` VALUES ('42', 'HELP_CATE_LIMIT', '4', '3', '0', '', '1', '1', '34', '');
INSERT INTO `%DB_PREFIX%conf` VALUES ('43', 'HELP_ITEM_LIMIT', '4', '3', '0', '', '1', '1', '35', '');
INSERT INTO `%DB_PREFIX%conf` VALUES ('44', 'SHOP_FOOTER', '<div style=\"text-align:center;\">\r\n	联系我们：info@fanwe.com &nbsp; 福州方维信息科技有限公司\r\n</div>\r\n<div style=\"text-align:center;\">\r\n	&copy; 2013 p2p信贷 All rights reserved\r\n</div>', '3', '3', '', '1', '1', '37', '');
INSERT INTO `%DB_PREFIX%conf` VALUES ('193', 'CUSTOM_SERVICE', ',', '6', '0', '', '1', '1', '1', '客服会员ID，多个用英文逗号（,）隔开，前台自动回复时使用！');
INSERT INTO `%DB_PREFIX%conf` VALUES ('194', 'SMS_SEND_REPAY', '1', '5', '1', '0,1', '1', '1', '80', '');
INSERT INTO `%DB_PREFIX%conf` VALUES ('45', 'USER_MESSAGE_AUTO_EFFECT', '1', '4', '1', '0,1', '1', '1', '64', '');
INSERT INTO `%DB_PREFIX%conf` VALUES ('50', 'MAIL_SEND_PAYMENT', '1', '5', '1', '0,1', '1', '1', '75', '');
INSERT INTO `%DB_PREFIX%conf` VALUES ('51', 'SMS_SEND_PAYMENT', '0', '5', '1', '0,1', '1', '1', '81', '');
INSERT INTO `%DB_PREFIX%conf` VALUES ('62', 'REPLY_ADDRESS', 'info@fanwe.com', '5', '0', '', '1', '1', '77', '');
INSERT INTO `%DB_PREFIX%conf` VALUES ('56', 'MAIL_ON', '0', '5', '1', '0,1', '1', '1', '72', '');
INSERT INTO `%DB_PREFIX%conf` VALUES ('57', 'SMS_ON', '1', '5', '1', '0,1', '1', '1', '78', '');
INSERT INTO `%DB_PREFIX%conf` VALUES ('63', 'BATCH_PAGE_SIZE', '500', '3', '0', '', '1', '1', '33', '');
INSERT INTO `%DB_PREFIX%conf` VALUES ('65', 'PUBLIC_DOMAIN_ROOT', '', '2', '0', '', '1', '1', '59', '没有架设请留空');
INSERT INTO `%DB_PREFIX%conf` VALUES ('70', 'REFERRALS_DELAY', '1', '4', '0', '', '1', '0', '70', '');
INSERT INTO `%DB_PREFIX%conf` VALUES ('71', 'SUBMIT_DELAY', '5', '1', '0', '', '1', '1', '13', '');
INSERT INTO `%DB_PREFIX%conf` VALUES ('72', 'APP_MSG_SENDER_OPEN', '1', '1', '1', '0,1', '1', '1', '9', '');
INSERT INTO `%DB_PREFIX%conf` VALUES ('73', 'ADMIN_MSG_SENDER_OPEN', '1', '1', '1', '0,1', '1', '1', '10', '');
INSERT INTO `%DB_PREFIX%conf` VALUES ('74', 'SHOP_OPEN', '1', '3', '1', '0,1', '1', '1', '46', '');
INSERT INTO `%DB_PREFIX%conf` VALUES ('75', 'SHOP_CLOSE_HTML', '', '3', '3', '', '1', '1', '47', '');
INSERT INTO `%DB_PREFIX%conf` VALUES ('76', 'FOOTER_LOGO', './public/attachment/201302/28/10/512eba9616a56.png', '3', '2', '', '1', '1', '20', '');
INSERT INTO `%DB_PREFIX%conf` VALUES ('77', 'GZIP_ON', '0', '1', '1', '0,1', '1', '1', '2', '');
INSERT INTO `%DB_PREFIX%conf` VALUES ('78', 'INTEGRATE_CODE', '', '0', '0', '', '1', '0', '0', '');
INSERT INTO `%DB_PREFIX%conf` VALUES ('79', 'INTEGRATE_CFG', '', '0', '0', '', '1', '0', '0', '');
INSERT INTO `%DB_PREFIX%conf` VALUES ('80', 'SHOP_SEO_TITLE', 'p2p信贷—最大、最安全的网络借贷平台', '3', '0', '', '1', '1', '14', '');
INSERT INTO `%DB_PREFIX%conf` VALUES ('81', 'CACHE_ON', '1', '1', '1', '0,1', '1', '1', '7', '');
INSERT INTO `%DB_PREFIX%conf` VALUES ('82', 'EXPIRED_TIME', '0', '1', '0', '', '1', '1', '5', '');
INSERT INTO `%DB_PREFIX%conf` VALUES ('120', 'FILTER_WORD', '', '1', '0', '', '1', '1', '100', '');
INSERT INTO `%DB_PREFIX%conf` VALUES ('84', 'STYLE_OPEN', '0', '3', '1', '0,1', '0', '0', '44', '');
INSERT INTO `%DB_PREFIX%conf` VALUES ('85', 'STYLE_DEFAULT', '1', '3', '1', '0,1', '0', '0', '45', '');
INSERT INTO `%DB_PREFIX%conf` VALUES ('86', 'TMPL_DOMAIN_ROOT', '', '2', '0', '0', '0', '0', '62', '');
INSERT INTO `%DB_PREFIX%conf` VALUES ('232', 'OPEN_QUOTA', '1', '6', '1', '0,1', '1', '1', '29', '开启授信额度申请');
INSERT INTO `%DB_PREFIX%conf` VALUES ('88', 'MEMCACHE_HOST', '127.0.0.1:11211', '1', '0', '', '1', '1', '8', '');
INSERT INTO `%DB_PREFIX%conf` VALUES ('90', 'IMAGE_USERNAME', '', '2', '0', '', '1', '1', '60', '');
INSERT INTO `%DB_PREFIX%conf` VALUES ('91', 'IMAGE_PASSWORD', '', '2', '4', '', '1', '1', '61', '');
INSERT INTO `%DB_PREFIX%conf` VALUES ('92', 'REGISTER_TYPE', '1', '4', '1', '0,1,2', '1', '1', '66', '如果系统整合UC，或开启第三方托管，建议使用 手机和邮箱');
INSERT INTO `%DB_PREFIX%conf` VALUES ('93', 'ATTR_SELECT', '0', '3', '1', '0,1', '0', '0', '43', '');
INSERT INTO `%DB_PREFIX%conf` VALUES ('94', 'ICP_LICENSE', '', '3', '0', '', '1', '1', '27', '');
INSERT INTO `%DB_PREFIX%conf` VALUES ('95', 'COUNT_CODE', '', '3', '0', '', '1', '1', '28', '');
INSERT INTO `%DB_PREFIX%conf` VALUES ('96', 'DEAL_MSG_LOCK', '0', '0', '0', '', '0', '0', '0', '');
INSERT INTO `%DB_PREFIX%conf` VALUES ('97', 'PROMOTE_MSG_LOCK', '0', '0', '0', '', '0', '0', '0', '');
INSERT INTO `%DB_PREFIX%conf` VALUES ('103', 'SEND_SPAN', '2', '1', '0', '', '1', '1', '85', '');
INSERT INTO `%DB_PREFIX%conf` VALUES ('111', 'SHOP_SEARCH_KEYWORD', '贷款,借贷，网贷', '3', '0', '', '1', '1', '15', '');
INSERT INTO `%DB_PREFIX%conf` VALUES ('117', 'INDEX_NOTICE_COUNT', '5', '3', '0', '', '1', '1', '35', '');
INSERT INTO `%DB_PREFIX%conf` VALUES ('119', 'TMPL_CACHE_ON', '1', '1', '1', '0,1', '1', '1', '6', '');
INSERT INTO `%DB_PREFIX%conf` VALUES ('125', 'DOMAIN_ROOT', '', '1', '0', '', '1', '1', '10', '');
INSERT INTO `%DB_PREFIX%conf` VALUES ('127', 'MAIN_APP', 'shop', '1', '1', 'shop,tuan,youhui', '1', '0', '10', '');
INSERT INTO `%DB_PREFIX%conf` VALUES ('128', 'VERIFY_IMAGE', '0', '1', '1', '0,1', '1', '1', '10', '');
INSERT INTO `%DB_PREFIX%conf` VALUES ('131', 'APNS_MSG_LOCK', '1', '0', '0', '', '0', '0', '0', '');
INSERT INTO `%DB_PREFIX%conf` VALUES ('132', 'PROMOTE_MSG_PAGE', '0', '0', '0', '', '0', '0', '0', '');
INSERT INTO `%DB_PREFIX%conf` VALUES ('133', 'APNS_MSG_PAGE', '0', '0', '0', '', '0', '0', '0', '');
INSERT INTO `%DB_PREFIX%conf` VALUES ('167', 'COMPANY', '福建p2p信贷金融信息服务有限公司', '6', '0', '', '1', '1', '1', '');
INSERT INTO `%DB_PREFIX%conf` VALUES ('168', 'COMPANY_ADDRESS', '福州台江区', '6', '0', '', '1', '1', '2', '');
INSERT INTO `%DB_PREFIX%conf` VALUES ('169', 'COMPANY_REG_ADDRESS', '福州市', '6', '0', '', '1', '1', '3', '');
INSERT INTO `%DB_PREFIX%conf` VALUES ('175', 'MANAGE_FEE', '0.3', '6', '0', '', '1', '1', '21', '管理费 = 本金总额×管理费率');
INSERT INTO `%DB_PREFIX%conf` VALUES ('176', 'MANAGE_IMPOSE_FEE_DAY1', '0.1', '6', '0', '', '1', '1', '22', '');
INSERT INTO `%DB_PREFIX%conf` VALUES ('177', 'MANAGE_IMPOSE_FEE_DAY2', '0.5', '6', '0', '', '1', '1', '23', '逾期管理费总额 = 逾期本息总额×对应逾期管理费率×逾期天数');
INSERT INTO `%DB_PREFIX%conf` VALUES ('178', 'IMPOSE_FEE_DAY1', '0.05', '6', '0', '', '1', '1', '24', '');
INSERT INTO `%DB_PREFIX%conf` VALUES ('179', 'IMPOSE_FEE_DAY2', '0.1', '6', '0', '', '1', '1', '25', '罚息总额 = 逾期本息总额×对应罚息利率×逾期天数');
INSERT INTO `%DB_PREFIX%conf` VALUES ('180', 'COMPENSATE_FEE', '1.0', '6', '0', '', '1', '1', '25', '补偿金额 = 剩余本金×补偿利率');
INSERT INTO `%DB_PREFIX%conf` VALUES ('181', 'IMPOSE_POINT', '-1', '7', '0', '', '1', '1', '14', '');
INSERT INTO `%DB_PREFIX%conf` VALUES ('182', 'YZ_IMPOSE_POINT', '-30', '7', '0', '', '1', '1', '15', '');
INSERT INTO `%DB_PREFIX%conf` VALUES ('183', 'YZ_IMPSE_DAY', '31', '6', '0', '', '1', '1', '15', '超过该天数为严重逾期');
INSERT INTO `%DB_PREFIX%conf` VALUES ('184', 'REPAY_SUCCESS_POINT', '1', '7', '0', '', '1', '1', '13', '');
INSERT INTO `%DB_PREFIX%conf` VALUES ('185', 'REPAY_SUCCESS_DAY', '28', '7', '0', '', '1', '1', '13', '');
INSERT INTO `%DB_PREFIX%conf` VALUES ('186', 'REPAY_SUCCESS_LIMIT', '20', '7', '0', '', '1', '1', '13', '');
INSERT INTO `%DB_PREFIX%conf` VALUES ('187', 'USER_REGISTER_POINT', '20', '7', '0', '', '1', '1', '0', '');
INSERT INTO `%DB_PREFIX%conf` VALUES ('188', 'USER_REGISTER_MONEY', '0', '7', '0', '', '1', '1', '0', '');
INSERT INTO `%DB_PREFIX%conf` VALUES ('189', 'USER_REGISTER_SCORE', '0', '7', '0', '', '1', '1', '0', '');
INSERT INTO `%DB_PREFIX%conf` VALUES ('192', 'MAX_BORROW_QUOTA', '1000000', '6', '0', '', '1', '1', '27', '');
INSERT INTO `%DB_PREFIX%conf` VALUES ('191', 'MIN_BORROW_QUOTA', '3000', '6', '0', '', '1', '1', '26', '');
INSERT INTO `%DB_PREFIX%conf` VALUES ('195', 'USER_REPAY_QUOTA', '500', '7', '0', '', '1', '1', '26', '');
INSERT INTO `%DB_PREFIX%conf` VALUES ('196', 'USER_LOAN_MANAGE_FEE', '0', '6', '0', '', '1', '1', '24', '管理费 = 投资总额×管理费率 0即不收取');
INSERT INTO `%DB_PREFIX%conf` VALUES ('197', 'SMS_REPAY_TOUSER_ON', '0', '5', '1', '0,1', '1', '1', '80', '');
INSERT INTO `%DB_PREFIX%conf` VALUES ('200', 'MAIL_SEND_CONTRACT_ON', '1', '5', '1', '0,1', '1', '1', '78', '');
INSERT INTO `%DB_PREFIX%conf` VALUES ('201', 'DEAL_BID_MULTIPLE', '0', '6', '0', '', '1', '1', '12', '0为不限制');
INSERT INTO `%DB_PREFIX%conf` VALUES ('202', 'USER_LOCK_MONEY', '0', '7', '0', '', '1', '1', '0', '需管理员手动解冻');
INSERT INTO `%DB_PREFIX%conf` VALUES ('203', 'USER_BID_REBATE', '0', '7', '0', '', '1', '1', '65', '返利金额=投标金额×返利百分比【需满标】');
INSERT INTO `%DB_PREFIX%conf` VALUES ('204', 'AGREEMENT', '', '4', '0', '', '1', '1', '66', '请输入帮助类文章编号');
INSERT INTO `%DB_PREFIX%conf` VALUES ('205', 'PRIVACY', '', '4', '0', '', '1', '1', '67', '请输入帮助类文章编号');
INSERT INTO `%DB_PREFIX%conf` VALUES ('206', 'USER_LOAD_TRANSFER_FEE', '0', '6', '0', '', '1', '1', '24', '管理费 = 转让金额×管理费率');
INSERT INTO `%DB_PREFIX%conf` VALUES ('224', 'USER_LOAN_INTEREST_MANAGE_FEE', '0', '6', '0', '', '1', '1', '24', '管理费 = 实际得到的利息×管理费率 0即不收取');
INSERT INTO `%DB_PREFIX%conf` VALUES ('225', 'GENERATION_REPAY_FEE', '0.1', '6', '0', '', '1', '1', '28', '垫付后的罚息 =  垫付总额×垫付罚息费率×垫付天数  0即不收取');
INSERT INTO `%DB_PREFIX%conf` VALUES ('226', 'USER_BID_SCORE_FEE', '1', '7', '0', '', '1', '1', '71', '投标返还积分 = 投标金额 ×返还比率【需满标，非信用积分】');
INSERT INTO `%DB_PREFIX%conf` VALUES ('227', 'INVESTORS_COMMISSION_RATIO', '', '4', '0', '', '1', '0', '72', '投资者佣金比例');
INSERT INTO `%DB_PREFIX%conf` VALUES ('228', 'BORROWER_COMMISSION_RATIO', '', '4', '0', '', '1', '0', '72', '借款者佣金比例');
INSERT INTO `%DB_PREFIX%conf` VALUES ('229', 'INVITE_REFERRALS_AUTO', '0', '0', '1', '0,1', '1', '0', '5', '');
INSERT INTO `%DB_PREFIX%conf` VALUES ('230', 'INVITE_REFERRALS_DATE', '12', '0', '0', '', '1', '0', '6', '0表示一直有效');
INSERT INTO `%DB_PREFIX%conf` VALUES ('121', 'USER_LOGIN_SCORE', '100', '7', '0', '', '1', '1', '2', '');
INSERT INTO `%DB_PREFIX%conf` VALUES ('122', 'USER_LOGIN_MONEY', '0', '7', '0', '', '1', '1', '1', '');
INSERT INTO `%DB_PREFIX%conf` VALUES ('123', 'USER_LOGIN_POINT', '0', '7', '0', '', '1', '1', '8', '');
INSERT INTO `%DB_PREFIX%conf` VALUES ('231', 'INVITE_REFERRALS_TYPE', '0', '0', '1', '0,1', '1', '0', '5', '');
INSERT INTO `%DB_PREFIX%conf` VALUES ('233', 'OPEN_POINT', '1', '6', '1', '0,1', '1', '1', '30', '开启信用额度申请');
INSERT INTO `%DB_PREFIX%conf` VALUES ('208', 'VIRTUAL_MONEY_1', '11102.88', '1', '0', '', '1', '1', '41', '虚拟的累计成交额');
INSERT INTO `%DB_PREFIX%conf` VALUES ('209', 'VIRTUAL_MONEY_2', '66788.32', '1', '0', '', '1', '1', '42', '虚拟的累计创造收益');
INSERT INTO `%DB_PREFIX%conf` VALUES ('210', 'VIRTUAL_MONEY_3', '56788.23', '1', '0', '', '1', '1', '43', '虚拟的本息保障金');
INSERT INTO `%DB_PREFIX%conf` VALUES ('211', 'OPEN_AUTOBID', '1', '4', '1', '0,1', '1', '1', '68', '开启关闭前台自动投标功能');
INSERT INTO `%DB_PREFIX%conf` VALUES ('212', 'OPEN_IPS', '0', '4', '1', '0,1', '1', '0', '69', '开启关闭资金托管功能');
INSERT INTO `%DB_PREFIX%conf` VALUES ('213', 'IPS_MERCODE', '', '4', '0', '', '1', '0', '71', '');
INSERT INTO `%DB_PREFIX%conf` VALUES ('214', 'IPS_KEY', '', '4', '0', '', '1', '0', '71', '');
INSERT INTO `%DB_PREFIX%conf` VALUES ('215', 'BORROW_AGREEMENT', '', '6', '0', '0', '1', '1', '1', '帮助类文章编号，在我要借款填写资料处显示');
INSERT INTO `%DB_PREFIX%conf` VALUES ('216', 'APPLE_DOWLOAD_URL', '', '3', '0', '0', '1', '1', '35', '');
INSERT INTO `%DB_PREFIX%conf` VALUES ('217', 'ANDROID_DOWLOAD_URL', '', '3', '0', '0', '1', '1', '35', '');
INSERT INTO `%DB_PREFIX%conf` VALUES ('67', 'REFERRAL_IP_LIMIT', '0', '0', '1', '0,1', '1', '0', '71', '');
INSERT INTO `%DB_PREFIX%conf` VALUES ('218', 'IPS_3DES_IV', '', '4', '0', '', '1', '0', '71', '');
INSERT INTO `%DB_PREFIX%conf` VALUES ('219', 'IPS_3DES_KEY', '', '4', '0', '', '1', '0', '71', '');
INSERT INTO `%DB_PREFIX%conf` VALUES ('220', 'IPS_FEE_TYPE', '', '4', '1', '1,2', '1', '0', '71', '谁付IPS手续费');
INSERT INTO `%DB_PREFIX%conf` VALUES ('221', 'INVITE_REFERRALS_MIN', '10', '0', '0', '', '1', '0', '2', '');
INSERT INTO `%DB_PREFIX%conf` VALUES ('222', 'INVITE_REFERRALS_MAX', '20', '0', '0', '', '1', '0', '3', '');
INSERT INTO `%DB_PREFIX%conf` VALUES ('223', 'INVITE_REFERRALS_RATE', '1', '0', '0', '', '1', '0', '4', '');

-- ----------------------------
-- Table structure for `%DB_PREFIX%contract`
-- ----------------------------
DROP TABLE IF EXISTS `%DB_PREFIX%contract`;
CREATE TABLE `%DB_PREFIX%contract` (
  `id` int(11) NOT NULL auto_increment,
  `title` varchar(255) NOT NULL COMMENT '范本标题',
  `content` text NOT NULL COMMENT '范本内容',
  `is_effect` tinyint(1) NOT NULL COMMENT '0无效 1有效',
  `is_delete` tinyint(1) NOT NULL COMMENT '0正常 1删除',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=8 DEFAULT CHARSET=utf8 COMMENT='合同范本';

-- ----------------------------
-- Records of fanwe_contract
-- ----------------------------
INSERT INTO `%DB_PREFIX%contract` VALUES ('1', '等额本息合同范本【普通】', '<div style=\"width: 98%;text-align: right;\">编号：<span>{$deal.deal_sn}</span></div>\r\n<h2 align=\"center\">借款协议</h2>\r\n<br/>\r\n<div style=\"text-align: left;font-weight: 600;\">甲方（出借人）：</div>\r\n<table border=\"1\" style=\"margin: 0px auto; border-collapse: collapse; border: 1px solid rgb(0, 0, 0); width: 70%; \">\r\n    <tr>\r\n	<td style=\"text-align:center;\"> {$SITE_TITLE}用户名</td>\r\n	<td style=\"text-align:center;\"> 借出金额</td>\r\n	<td style=\"text-align:center;\">借款期限</td>\r\n	<td style=\"text-align:center;\"> 每月应收本息</td>\r\n    </tr>\r\n    {foreach from=\"$loan_list\" item=\"loan\"}\r\n    <tr>\r\n	<td style=\"text-align:center;\">{$loan.user_name}</td>\r\n	<td style=\"text-align:right;padding-right:10px\">{$CURRENCY_UNIT}{function name=\"number_format\" v=$loan.money f=2}</td>\r\n	<td style=\"text-align:center;\">{$deal.repay_time}个月</td>\r\n	<td style=\"text-align:right;padding-right:10px\">\r\n	{$CURRENCY_UNIT}{function name=\"number_format\" v=$loan.get_repay_money f=2}\r\n	</td>\r\n    </tr>\r\n    {/foreach}\r\n    <tr>\r\n	<td style=\"text-align:center;\">总计</td>\r\n	<td style=\"text-align:right;padding-right:10px\">{$CURRENCY_UNIT}{function name=\"number_format\" v=$deal.load_money f=2}</td>\r\n	<td> </td>\r\n	<td> </td>\r\n    </tr>\r\n</table>\r\n<p>注：因计算中存在四舍五入，最后一期应收本息与之前略有不同</p>\r\n<br/>\r\n<div>\r\n	<p style=\"text-align: left;font-weight: 600;\">乙方（借款人）：</p>\r\n	<p style=\"text-align: left;font-weight: 600;\">{$SITE_TITLE}用户名：<span>{$user_info.user_name}</span></p>\r\n	<br/>\r\n	<p style=\"text-align: left;font-weight: 600;\"> 丙方（见证人）：{function name=\"app_conf\" v=\"COMPANY\"} </p>\r\n	<p style=\"text-align: left;font-weight: 600;\">联系方式：{function name=\"app_conf\" v=\"COMPANY_ADDRESS\"}</p>\r\n</div>\r\n<br/>\r\n<p><strong>鉴于：</strong></p>\r\n<p>1、丙方是一家在{function name=\"app_conf\" v=\"COMPANY_REG_ADDRESS\"}合法成立并有效存续的有限责任公司，拥有{$SITE_URL} 网站（以下简称“该网站”）的经营权，提供信用咨询，为交易提供信息服务；</p>\r\n<p>2、乙方已在该网站注册，并承诺其提供给丙方的信息是完全真实的；</p>\r\n<p>3、甲方承诺对本协议涉及的借款具有完全的支配能力，是其自有闲散资金，为其合法所得；并承诺其提供给丙方的信息是完全真实的；</p>\r\n<p>4、乙方有借款需求，甲方亦同意借款，双方有意成立借贷关系；</p>\r\n<br/>\r\n<p style=\"text-align: left;font-weight: 600;\">各方经协商一致，于<span> {function name=\"to_date\" v=\"$deal.repay_start_time\" f=\"Y年m月d日\"}</span>签订如下协议，共同遵照履行：</p>\r\n<br/>\r\n<p style=\"text-align: left;font-weight: 600;\"> 第一条 借款基本信息</p>\r\n<br/>\r\n<table border=\"1\" style=\"margin: 0px auto; border-collapse: collapse; border: 1px solid rgb(0, 0, 0); width: 70%; \">\r\n	<tr>\r\n		<td width=\"20%\" style=\"padding-left:10px\"> 借款详细用途</td>\r\n		<td style=\"padding-left:10px\"> {$deal.type_info.name}</td>\r\n	</tr>\r\n	<tr>\r\n		<td style=\"padding-left:10px\">借款本金数额</td>\r\n		<td style=\"padding-left:10px\">\r\n			{$CURRENCY_UNIT}{function name=\"number_format\" v=$deal.borrow_amount f=2}（各出借人借款本金数额详见本协议文首表格）\r\n		</td>\r\n	</tr>\r\n	<tr>\r\n		<td style=\"padding-left:10px\"> 月偿还本息数额\r\n		</td>\r\n		<td style=\"padding-left:10px\">\r\n			{$deal.month_repay_money_format}（因计算中存在四舍五入，最后一期应还金额与之前可能有所不同，为{$CURRENCY_UNIT}{function name=\"number_format\" v=$deal.last_month_repay_money f=2}）\r\n		</td>\r\n	</tr>\r\n	\r\n	<tr>\r\n		<td style=\"padding-left:10px\"> 还款分期月数\r\n		</td>\r\n		<td style=\"padding-left:10px\">\r\n			{$deal.repay_time}个月\r\n		</td>\r\n	</tr>\r\n	\r\n	<tr>\r\n		<td style=\"padding-left:10px\">\r\n			还款日\r\n		</td>\r\n		<td style=\"padding-left:10px\">\r\n			 自{function name=\"to_date\" v=\"$deal.repay_start_time\" f=\"Y年m月d日\"}起，每月 {function name=\"to_date\" v=\"$deal.repay_start_time\" f=\"d\"}日（24:00前，节假日不顺延）\r\n		</td>\r\n	</tr>\r\n	<tr>\r\n		<td style=\"padding-left:10px\"> 借款期限\r\n		</td>\r\n		<td style=\"padding-left:10px\">\r\n			{$deal.repay_time}个月，{function name=\"to_date\" v=\"$deal.repay_start_time\" f=\"Y年m月d日\"}起，至  {function name=\"to_date\" v=\"$deal.end_repay_time\" f=\"Y年m月d日\"}日止\r\n		</td>\r\n	</tr>\r\n</table>\r\n<br/>\r\n<div>\r\n	<p style=\"text-align: left;font-weight: 600;\">\r\n		第二条 各方权利和义务\r\n	</p>\r\n	<p style=\"text-align: left;font-weight: 600;\">\r\n		<u>甲方的权利和义务</u>\r\n	</p>\r\n	<p> 1、	甲方应按合同约定的借款期限起始日期将足额的借款本金支付给乙方。\r\n	</p>\r\n	<p> 2、	甲方享有其所出借款项所带来的利息收益。\r\n	</p>\r\n	<p>3、	如乙方违约，甲方有权要求丙方提供其已获得的乙方信息，丙方应当提供。\r\n	</p>\r\n	<p>4、	无须通知乙方，甲方可以根据自己的意愿进行本协议下其对乙方债权的转让。在甲方的债权转让后，乙方需对债权受让人继续履行本协议下其对甲方的还款义务，不得以未接到债权转让通知为由拒绝履行还款义务。\r\n	</p>\r\n	<p> 5、	甲方应主动缴纳由利息所得带来的可能的税费。\r\n	</p>\r\n	<p>6、	如乙方实际还款金额少于本协议约定的本金、利息及违约金的，甲方各出借人同意各自按照其于本协议文首约定的借款比例收取还款。\r\n	</p>\r\n	<p> 7、	甲方应确保其提供信息和资料的真实性，不得提供虚假信息或隐瞒重要事实。\r\n	</p>\r\n	<p style=\"text-align: left;font-weight: 600;\">\r\n		<u>乙方权利和义务</u>\r\n	</p>\r\n	<p> 1、	乙方必须按期足额向甲方偿还本金和利息。\r\n	</p>\r\n	<p> 2、    乙方必须按期足额向丙方支付借款管理费用。\r\n	</p>\r\n	<p>3、	乙方承诺所借款项不用于任何违法用途。\r\n	</p>\r\n	<p>4、	乙方应确保其提供的信息和资料的真实性，不得提供虚假信息或隐瞒重要事实。\r\n	</p>\r\n	<p>5、	乙方有权了解其在丙方的信用评审进度及结果。\r\n	</p>\r\n	<p> 6、	乙方不得将本协议项下的任何权利义务转让给任何其他方。\r\n	</p>\r\n	<p style=\"text-align: left;font-weight: 600;\">\r\n		<u>丙方的权利和义务</u>\r\n	</p>\r\n	<p>1、甲方授权并委托丙方代其收取本协议文首所约定的出借人每月应收本息，代收后按照甲方的要求进行处置，乙方对此表示认可。\r\n	</p>\r\n	<p>2、甲方授权并委托丙方将其支付的出借本金直接划付至乙方账户，乙方对此表示认可。\r\n	</p>\r\n	<p> 3、甲、乙双方一致同意，在有必要时，丙方有权代甲方对乙方进行关于本协议借款的违约提醒及催收工作，包括但不限于：电话通知、上门催收提醒、发律师函、对乙方提起诉讼等。甲方在此确认委托丙方为其进行以上工作，并授权丙方可以将此工作委托给本协议外的其他方进行。乙方对前述委托的提醒、催收事项已明确知晓并应积极配合。\r\n	</p>\r\n	<p>4、丙方有权按月向乙方收取双方约定的借款管理费，并在有必要时对乙方进行违约提醒及催收工作，包括但不限于电话通知、发律师函、对乙方提起诉讼等。丙方有权将此违约提醒及催收工作委托给本协议外的其他方进行。\r\n	</p>\r\n	<p> 5、丙方接受甲乙双方的委托行为所产生的法律后果由相应委托方承担。如因乙方或甲方或其他方（包括但不限于技术问题）造成的延误或错误，丙方不承担任何责任。\r\n	</p>\r\n	<p> 6、丙方应对甲方和乙方的信息及本协议内容保密；如任何一方违约，或因相关权力部门要求（包括但不限于法院、仲裁机构、金融监管机构等），丙方有权披露。\r\n	</p>\r\n	<p>7、丙方根据本协议对乙方进行违约提醒及催收工作时，可在其认为必要时进行上门催收提醒，即丙方派出人员（至少2名）至乙方披露的住所地或经常居住地（联系地址）处催收和进行违约提醒，同时向乙方发送催收通知单，乙方应当签收，乙方不签收的，不影响上门催收提醒的进行。丙方采取上门催收提醒的，乙方应当向丙方支付上门提醒费用，收费标准为每次人民币1000.00元，此外，乙方还应向丙方支付进行上门催收提醒服务的差旅费（包括但不限于交通费、食宿费等）。\r\n	</p>\r\n	<br/>\r\n	<p style=\"text-align: left;font-weight: 600;\">\r\n		第三条 借款管理费及居间服务费\r\n	</p>\r\n	<p>1、	在本协议中，“借款管理费”和“居间服务费”是指因丙方为乙方提供信用咨询、评估、还款提醒、账户管理、还款特殊情况沟通、本金保障等系列信用相关服务（统称“信用服务”）而由乙方支付给丙方的报酬。\r\n	</p>\r\n	<p>2、	对于丙方向乙方提供的一系列信用服务，乙方同意在借款成功时向丙方支付本协议第一条约定借款本金总额的{function name=\"number_format\" v=$deal.services_fee f=1}%(即人民币<?php echo number_format(floatval($this->_var[\'deal\'][\'services_fee\'])*$this->_var[\'deal\'][\'borrow_amount\']/100,2); ?>元)作为居间服务费，该“居间服务费”由乙方授权并委托丙方在丙方根据本协议规定的“丙方的权利和义务”第2款规定向乙方划付出借本金时从本金中予以扣除，即视为乙方已缴纳。在本协议约定的借款期限内，乙方应每月向丙方支付本协议第一条约定借款本金总额的{function name=\"app_conf\" v=\"MANAGE_FEE\"}% (即人民币{function name=\"number_format\" v=\"$deal.month_manage_money\" f=2}元)，作为借款管理费用，共需支付{$deal.repay_time}期，共计人民币{function name=\"number_format\" v=\"$deal.all_manage_money\" f=2} 元，借款管理费的缴纳时间与本协议第一条约定的还款日一致。</p>\r\n	<p> 本条所称的“借款成功时”系指本协议签署日。</p>\r\n	<p> 3、    如乙方和丙方协商一致调整借款管理费和居间服务费时，无需经过甲方同意。 </p>\r\n	<br/>\r\n	<p style=\"text-align: left;font-weight: 600;\">\r\n		第四条 违约责任\r\n	</p>\r\n	<p> 1、协议各方均应严格履行合同义务，非经各方协商一致或依照本协议约定，任何一方不得解除本协议。\r\n	</p>\r\n	<p>2、任何一方违约，违约方应承担因违约使得其他各方产生的费用和损失，包括但不限于调查、诉讼费、律师费等，应由违约方承担。如违约方为乙方的，甲方有权立即解除本协议，并要求乙方立即偿还未偿还的本金、利息、罚息、违约金。此时，乙方还应向丙方支付所有应付的借款管理费。如本协议提前解除时，乙方在{$SITE_URL}网站的账户里有任何余款的，丙方有权按照本协议第四条第3项的清偿顺序将乙方的余款用于清偿，并要求乙方支付因此产生的相关费用。</p>\r\n	<p>3、乙方的每期还款均应按照如下顺序清偿：</p>\r\n	<p>（1）根据本协议产生的其他全部费用；</p>\r\n	<p>（2）本协议第四条第4款约定的罚息； </p>\r\n	<p>（3）本协议第四条第5款约定的逾期管理费；</p>\r\n	<p>（4）拖欠的利息； </p>\r\n	<p>（5）拖欠的本金； </p>\r\n	<p>（6）拖欠丙方的借款管理费；\r\n	</p>\r\n	<p>（7）正常的利息； </p>\r\n	<p>（8）正常的本金； </p>\r\n	<p>（9）丙方的借款管理费；</p>\r\n	<p> 4、乙方应严格履行还款义务，如乙方逾期还款，则应按照下述条款向甲方支付逾期罚息，自逾期开始之后，逾期本金的正常利息停止计算。 </p>\r\n	<p>罚息总额 = 逾期本息总额×对应罚息利率×逾期天数；</p>\r\n</div>\r\n<div>\r\n	<br/>\r\n	<table border=\"1\" style=\"margin: 0px auto; border-collapse: collapse; border: 1px solid rgb(0, 0, 0); width: 70%; \">\r\n		<tr>\r\n			<td style=\"padding-left:10px\">\r\n				逾期天数\r\n			</td>\r\n			<td style=\"padding-left:10px\">\r\n				1—<?php echo (int)app_conf(\"YZ_IMPSE_DAY\")-1; ?>天\r\n			</td>\r\n			<td style=\"padding-left:10px\">\r\n				{function name=\"app_conf\" v=\"YZ_IMPSE_DAY\"}天及以上\r\n			</td>\r\n		</tr>\r\n		<tr>\r\n			<td style=\"padding-left:10px\">\r\n				罚息利率\r\n			</td>\r\n			<td style=\"padding-left:10px\">\r\n				{$deal.impose_fee_day1}%\r\n			</td>\r\n			<td style=\"padding-left:10px\">\r\n				{$deal.impose_fee_day2}%\r\n			</td>\r\n		</tr>\r\n	</table>\r\n	<br/>\r\n	<p>\r\n		5、乙方应严格履行还款义务，如乙方逾期还款，则应按照下述条款向丙方支付逾期管理费：\r\n	</p>\r\n	<p>\r\n		逾期管理费总额 = 逾期本息总额×对应逾期管理费率×逾期天数；\r\n	</p>\r\n</div>\r\n<br/>\r\n<table border=\"1\" style=\"margin: 0px auto; border-collapse: collapse; border: 1px solid rgb(0, 0, 0); width: 70%; \">\r\n	<tr>\r\n		<td style=\"padding-left:10px\">\r\n			逾期天数\r\n		</td>\r\n		<td style=\"padding-left:10px\">\r\n			1—<?php echo (int)app_conf(\"YZ_IMPSE_DAY\")-1; ?>天\r\n		</td>\r\n		<td style=\"padding-left:10px\">\r\n			{function name=\"app_conf\" v=\"YZ_IMPSE_DAY\"}天及以上\r\n		</td>\r\n	</tr>\r\n	<tr>\r\n		<td style=\"padding-left:10px\">\r\n			逾期管理费费率\r\n		</td>\r\n		<td style=\"padding-left:10px\">\r\n			{$deal.manage_impose_fee_day1}%\r\n		</td>\r\n		<td style=\"padding-left:10px\">\r\n			{$deal.manage_impose_fee_day2}%\r\n		</td>\r\n	</tr>\r\n</table>\r\n<br/>\r\n<div>\r\n	<p>\r\n		6、如果乙方逾期支付任何一期还款超过30天，或乙方在逾期后出现逃避、拒绝沟通或拒绝承认欠款事实等恶意行为，本协议项下的全部借款本息及借款管理费均提前到期，乙方应立即清偿本协议下尚未偿付的全部本金、利息、罚息、借款管理费及根据本协议产生的其他全部费用。\r\n	</p>\r\n	<p>\r\n		7、如果乙方逾期支付任何一期还款超过30天，或乙方在逾期后出现逃避、拒绝沟通或拒绝承认欠款事实等恶意行为，丙方有权将乙方的“逾期记录”记入人民银行公民征信系统，丙方不承担任何法律责任。\r\n	</p>\r\n	<p>\r\n		8、如果乙方逾期支付任何一期还款超过30天，或乙方在逾期后出现逃避、拒绝沟通或拒绝承认欠款事实等恶意行为，丙方有权将乙方违约失信的相关信息及乙方其他信息向媒体、用人单位、公安机关、检查机关、法律机关披露，丙方不承担任何责任。\r\n	</p>\r\n	<p>\r\n		9、在乙方还清全部本金、利息、借款管理费、罚息、逾期管理费之前，罚息及逾期管理费的计算不停止。\r\n	</p>\r\n	<p>\r\n		10、本协议中的所有甲方与乙方之间的借款均是相互独立的，一旦乙方逾期未归还借款本息，甲方中的任何一个出借人均有权单独向乙方追索或者提起诉讼。如乙方逾期支付借款管理费或提供虚假信息的，丙方亦可单独向乙方追索或者提起诉讼。\r\n	</p>\r\n	<br/>\r\n	<p style=\"text-align: left;font-weight: 600;\">\r\n		第五条 提前还款\r\n	</p>\r\n	<p>\r\n		1、乙方可在借款期间任何时候提前偿还剩余借款。\r\n	</p>\r\n	<p>\r\n		2、提前偿还全部剩余借款\r\n	</p>\r\n	<p style=\"padding-left: 15px\">\r\n		1）乙方提前清偿全部剩余借款时，应向甲方支付当期应还本息，剩余本金及提前还款补偿（补偿金额为剩余本金的{$deal.compensate_fee}%）。\r\n	</p>\r\n	<p style=\"padding-left: 15px\">\r\n		2）乙方提前清偿全部剩余借款时，应向丙方支付当期借款管理费，乙方无需支付剩余还款期的借款管理费。\r\n	</p>\r\n	<p>\r\n		3、提前偿还部分借款\r\n	</p>\r\n	<p style=\"padding-left: 15px\">\r\n		1）乙方提前偿还部分借款，仍应向甲方支付全部借款利息。\r\n	</p>\r\n	<p style=\"padding-left: 15px\">\r\n		2）乙方提前偿还部分借款，仍应向丙方支付全部应付的借款管理费。\r\n	</p>\r\n	<p>\r\n		4、任何形式的提前还款不影响丙方向乙方收取在本协议第三条中说明的居间服务费。\r\n	</p>\r\n	<br/>\r\n	<p style=\"text-align: left;font-weight: 600;\">\r\n		第六条	法律及争议解决\r\n	</p>\r\n	 <p>\r\n		 本协议的签订、履行、终止、解释均适用中华人民共和国法律，并由丙方所在地{function name=\"app_conf\" v=\'COMPANY_REG_ADDRESS\'}人民法院管辖。\r\n	</p>\r\n	<br/>\r\n	<p style=\"text-align: left;font-weight: 600;\">\r\n		第七条	附则\r\n	</p>\r\n	<p>\r\n		1、本协议采用电子文本形式制成，并永久保存在丙方为此设立的专用服务器上备查，各方均认可该形式的协议效力。\r\n	</p>\r\n	<p>\r\n		2、本协议自文本最终生成之日生效。\r\n	</p>\r\n	<p>\r\n		3、本协议签订之日起至借款全部清偿之日止，乙方或甲方有义务在下列信息变更三日内提供更新后的信息给丙方：本人、本人的家庭联系人及紧急联系人、工作单位、居住地址、住所电话、手机号码、电子邮箱、银行账户的变更。若因任何一方不及时提供上述变更信息而带来的损失或额外费用应由该方承担。\r\n	</p>\r\n	<p>\r\n		4、如果本协议中的任何一条或多条违反适用的法律法规，则该条将被视为无效，但该无效条款并不影响本协议其他条款的效力。\r\n	</p>\r\n        <br>\r\n	{if $deal.attachment}\r\n	<p style=\"text-align: left;font-weight: 600;\">\r\n		第八条	附件\r\n	</p>\r\n	<p>\r\n		{$deal.attachment}\r\n	</p>\r\n	{/if}\r\n</div>\r\n<br/>\r\n<div style=\"width: 98%;text-align: right;\">\r\n	<p>\r\n		{function name=\"to_date\" v=\"$deal.repay_start_time\" f=\"Y年m月d日\"}\r\n	</p>\r\n</div>', '1', '0');
INSERT INTO `%DB_PREFIX%contract` VALUES ('2', '等额本息合同范本【担保】', '<div style=\"width: 98%;text-align: right;\">编号：<span>{$deal.deal_sn}</span></div>\r\n<h2 align=\"center\">借款协议</h2>\r\n<br/>\r\n<div style=\"text-align: left;font-weight: 600;\">甲方（出借人）：</div>\r\n<table border=\"1\" style=\"margin: 0px auto; border-collapse: collapse; border: 1px solid rgb(0, 0, 0); width: 70%; \">\r\n    <tr>\r\n	<td style=\"text-align:center;\"> {$SITE_TITLE}用户名</td>\r\n	<td style=\"text-align:center;\"> 借出金额</td>\r\n	<td style=\"text-align:center;\">借款期限</td>\r\n	<td style=\"text-align:center;\"> 每月应收本息</td>\r\n    </tr>\r\n    {foreach from=\"$loan_list\" item=\"loan\"}\r\n    <tr>\r\n	<td style=\"text-align:center;\">{$loan.user_name}</td>\r\n	<td style=\"text-align:right;padding-right:10px\">{$CURRENCY_UNIT}{function name=\"number_format\" v=$loan.money f=2}</td>\r\n	<td style=\"text-align:center;\">{$deal.repay_time}个月</td>\r\n	<td style=\"text-align:right;padding-right:10px\">\r\n	{$CURRENCY_UNIT}{function name=\"number_format\" v=$loan.get_repay_money f=2}\r\n	</td>\r\n    </tr>\r\n    {/foreach}\r\n    <tr>\r\n	<td style=\"text-align:center;\">总计</td>\r\n	<td style=\"text-align:right;padding-right:10px\">{$CURRENCY_UNIT}{function name=\"number_format\" v=$deal.load_money f=2}</td>\r\n	<td> </td>\r\n	<td> </td>\r\n    </tr>\r\n</table>\r\n<p>注：因计算中存在四舍五入，最后一期应收本息与之前略有不同</p>\r\n<br/>\r\n<div>\r\n	<p style=\"text-align: left;font-weight: 600;\">乙方（借款人）：</p>\r\n	<p style=\"text-align: left;font-weight: 600;\">{$SITE_TITLE}用户名：<span>{$user_info.user_name}</span></p>\r\n	<br/>\r\n	<p style=\"text-align: left;font-weight: 600;\"> 丙方（见证人）：{function name=\"app_conf\" v=\"COMPANY\"} </p>\r\n	<p style=\"text-align: left;font-weight: 600;\">联系方式：{function name=\"app_conf\" v=\"COMPANY_ADDRESS\"}</p>\r\n	<br/>\r\n	<p style=\"text-align: left;font-weight: 600;\"> 丁方（担保方）：{$deal.agency_info.name} </p>\r\n	<p style=\"text-align: left;font-weight: 600;\">联系方式：{$deal.agency_info.address}</p>\r\n</div>\r\n<br/>\r\n<p><strong>鉴于：</strong></p>\r\n<p>1、丙方是一家在{function name=\"app_conf\" v=\"COMPANY_REG_ADDRESS\"}合法成立并有效存续的有限责任公司，拥有{$SITE_URL} 网站（以下简称“该网站”）的经营权，提供信用咨询，为交易提供信息服务；</p>\r\n<p>2、乙方已在该网站注册，并承诺其提供给丙方的信息是完全真实的；</p>\r\n<p>3、甲方承诺对本协议涉及的借款具有完全的支配能力，是其自有闲散资金，为其合法所得；并承诺其提供给丙方的信息是完全真实的；</p>\r\n<p>4、乙方有借款需求，甲方亦同意借款，双方有意成立借贷关系；</p>\r\n<p>5、丁方愿意作为甲方借款提供保障。当乙方逾期3天仍未清偿借款本息，则甲方本协议项下的债权（逾期本息）自动转让给丁方，丁方将在第4天垫付该标的的未清偿借款本息给甲方；</p>\r\n<br/>\r\n<p style=\"text-align: left;font-weight: 600;\">各方经协商一致，于<span> {function name=\"to_date\" v=\"$deal.repay_start_time\" f=\"Y年m月d日\"}</span>签订如下协议，共同遵照履行：</p>\r\n<br/>\r\n<p style=\"text-align: left;font-weight: 600;\"> 第一条 借款基本信息</p>\r\n<br/>\r\n<table border=\"1\" style=\"margin: 0px auto; border-collapse: collapse; border: 1px solid rgb(0, 0, 0); width: 70%; \">\r\n	<tr>\r\n		<td width=\"20%\" style=\"padding-left:10px\"> 借款详细用途</td>\r\n		<td style=\"padding-left:10px\"> {$deal.type_info.name}</td>\r\n	</tr>\r\n	<tr>\r\n		<td style=\"padding-left:10px\">借款本金数额</td>\r\n		<td style=\"padding-left:10px\">\r\n			{$CURRENCY_UNIT}{function name=\"number_format\" v=$deal.borrow_amount f=2}（各出借人借款本金数额详见本协议文首表格）\r\n		</td>\r\n	</tr>\r\n	<tr>\r\n		<td style=\"padding-left:10px\">月偿还本息数额\r\n		</td>\r\n		<td style=\"padding-left:10px\">\r\n			{$deal.month_repay_money_format}（因计算中存在四舍五入，最后一期应还金额与之前可能有所不同，为{$CURRENCY_UNIT}{function name=\"number_format\" v=$deal.last_month_repay_money f=2}）\r\n		</td>\r\n	</tr>\r\n	\r\n	<tr>\r\n		<td style=\"padding-left:10px\"> 还款分期月数\r\n		</td>\r\n		<td style=\"padding-left:10px\">\r\n			{$deal.repay_time}个月\r\n		</td>\r\n	</tr>\r\n	\r\n	<tr>\r\n		<td style=\"padding-left:10px\">\r\n			还款日\r\n		</td>\r\n		<td style=\"padding-left:10px\">\r\n			 自{function name=\"to_date\" v=\"$deal.repay_start_time\" f=\"Y年m月d日\"}起，每月 {function name=\"to_date\" v=\"$deal.end_repay_time\" f=\"Y年m月d日\"}日（24:00前，节假日不顺延）\r\n		</td>\r\n	</tr>\r\n	<tr>\r\n		<td style=\"padding-left:10px\"> 借款期限\r\n		</td>\r\n		<td style=\"padding-left:10px\">\r\n			{$deal.repay_time}个月，{function name=\"to_date\" v=\"$deal.repay_start_time\" f=\"Y年m月d日\"}起，至  {function name=\"to_date\" v=\"$deal.end_repay_time\" f=\"Y年m月d日\"}日止\r\n		</td>\r\n	</tr>\r\n</table>\r\n<br/>\r\n<div>\r\n	<p style=\"text-align: left;font-weight: 600;\">\r\n		第二条 各方权利和义务\r\n	</p>\r\n	<p style=\"text-align: left;font-weight: 600;\">\r\n		<u>甲方的权利和义务</u>\r\n	</p>\r\n	<p> 1、	甲方应按合同约定的借款期限起始日期将足额的借款本金支付给乙方。\r\n	</p>\r\n	<p> 2、	甲方享有其所出借款项所带来的利息收益。\r\n	</p>\r\n	<p>3、	如乙方违约，甲方有权要求丙方提供其已获得的乙方信息，丙方应当提供。\r\n	</p>\r\n	<p>4、	无须通知乙方，甲方可以根据自己的意愿进行本协议下其对乙方债权的转让。在甲方的债权转让后，乙方需对债权受让人继续履行本协议下其对甲方的还款义务，不得以未接到债权转让通知为由拒绝履行还款义务。\r\n	</p>\r\n	<p> 5、	甲方应主动缴纳由利息所得带来的可能的税费。\r\n	</p>\r\n	<p>6、	如乙方实际还款金额少于本协议约定的本金、利息及违约金的，甲方各出借人同意各自按照其于本协议文首约定的借款比例收取还款。\r\n	</p>\r\n	<p> 7、	甲方应确保其提供信息和资料的真实性，不得提供虚假信息或隐瞒重要事实。\r\n	</p>\r\n	<p style=\"text-align: left;font-weight: 600;\">\r\n		<u>乙方权利和义务</u>\r\n	</p>\r\n	<p> 1、	乙方必须按期足额向甲方偿还本金和利息。\r\n	</p>\r\n	<p> 2、    乙方必须按期足额向丙方支付借款管理费用。\r\n	</p>\r\n	<p>3、	乙方承诺所借款项不用于任何违法用途。\r\n	</p>\r\n	<p>4、	乙方应确保其提供的信息和资料的真实性，不得提供虚假信息或隐瞒重要事实。\r\n	</p>\r\n	<p>5、	乙方有权了解其在丙方的信用评审进度及结果。\r\n	</p>\r\n	<p> 6、	乙方不得将本协议项下的任何权利义务转让给任何其他方。\r\n	</p>\r\n	<p style=\"text-align: left;font-weight: 600;\">\r\n		<u>丙方的权利和义务</u>\r\n	</p>\r\n	<p>1、甲方授权并委托丙方代其收取本协议文首所约定的出借人每月应收本息，代收后按照甲方的要求进行处置，乙方对此表示认可。\r\n	</p>\r\n	<p>2、甲方授权并委托丙方将其支付的出借本金直接划付至乙方账户，乙方对此表示认可。\r\n	</p>\r\n	<p> 3、甲、乙双方一致同意，在有必要时，丙方有权代甲方对乙方进行关于本协议借款的违约提醒及催收工作，包括但不限于：电话通知、上门催收提醒、发律师函、对乙方提起诉讼等。甲方在此确认委托丙方为其进行以上工作，并授权丙方可以将此工作委托给本协议外的其他方进行。乙方对前述委托的提醒、催收事项已明确知晓并应积极配合。\r\n	</p>\r\n	<p>4、丙方有权按月向乙方收取双方约定的借款管理费，并在有必要时对乙方进行违约提醒及催收工作，包括但不限于电话通知、发律师函、对乙方提起诉讼等。丙方有权将此违约提醒及催收工作委托给本协议外的其他方进行。\r\n	</p>\r\n	<p> 5、丙方接受甲乙双方的委托行为所产生的法律后果由相应委托方承担。如因乙方或甲方或其他方（包括但不限于技术问题）造成的延误或错误，丙方不承担任何责任。\r\n	</p>\r\n	<p> 6、丙方应对甲方和乙方的信息及本协议内容保密；如任何一方违约，或因相关权力部门要求（包括但不限于法院、仲裁机构、金融监管机构等），丙方有权披露。\r\n	</p>\r\n	<p>7、丙方根据本协议对乙方进行违约提醒及催收工作时，可在其认为必要时进行上门催收提醒，即丙方派出人员（至少2名）至乙方披露的住所地或经常居住地（联系地址）处催收和进行违约提醒，同时向乙方发送催收通知单，乙方应当签收，乙方不签收的，不影响上门催收提醒的进行。丙方采取上门催收提醒的，乙方应当向丙方支付上门提醒费用，收费标准为每次人民币1000.00元，此外，乙方还应向丙方支付进行上门催收提醒服务的差旅费（包括但不限于交通费、食宿费等）。\r\n	</p>\r\n	<p style=\"text-align: left;font-weight: 600;\">\r\n		<u>丁方的权利和义务</u>\r\n	</p>\r\n	<p>1、出借人和借款人均同意，丁方取得出借人在本协议项下的债权后，可依法向借款人追收借款本金、利息、逾期罚息等，清收费用，坏账风险均由丁方承担。\r\n	</p>\r\n	<p>2、款人逾期本网站对逾期仍未还款的借款人收取逾期罚息作为催收费用、采取多种方式进行催收、将借款人的相关信息对外公开或列入“不良信用记录”或采取法律措施等各项行为，该等服务的法律后果均由借款人自行承担，如债权转让给丁方后，对借款人造成的一切责任与本网站无关，均由借款人自行承担。\r\n	</p>\r\n	<p> 3、若借款人的任何一期还款不足以偿还应还本金、利息和违约金，且出借人为多人时，则出借人同意按照各自出借金额在出借金额总额中的比例收取还款，不足偿还本金时直接由丁方垫付后债权直接转让给丁方再进行追讨。\r\n	</p>\r\n	\r\n	<p style=\"text-align: left;font-weight: 600;\">\r\n		第三条 借款管理费及居间服务费\r\n	</p>\r\n	<p>1、	在本协议中，“借款管理费”和“居间服务费”是指因丙方为乙方提供信用咨询、评估、还款提醒、账户管理、还款特殊情况沟通、本金保障等系列信用相关服务（统称“信用服务”）而由乙方支付给丙方的报酬。\r\n	</p>\r\n	<p>2、	对于丙方向乙方提供的一系列信用服务，乙方同意在借款成功时向丙方支付本协议第一条约定借款本金总额的{function name=\"number_format\" v=$deal.services_fee f=1}%(即人民币<?php echo number_format(floatval($this->_var[\'deal\'][\'services_fee\'])*$this->_var[\'deal\'][\'borrow_amount\']/100,2); ?>元)作为居间服务费，该“居间服务费”由乙方授权并委托丙方在丙方根据本协议规定的“丙方的权利和义务”第2款规定向乙方划付出借本金时从本金中予以扣除，即视为乙方已缴纳。在本协议约定的借款期限内，乙方应每月向丙方支付本协议第一条约定借款本金总额的{function name=\"app_conf\" v=\"MANAGE_FEE\"}% (即人民币{function name=\"number_format\" v=\"$deal.month_manage_money\" f=2}元)，作为借款管理费用，共需支付{$deal.repay_time}期，共计人民币{function name=\"number_format\" v=\"$deal.all_manage_money\" f=2} 元，借款管理费的缴纳时间与本协议第一条约定的还款日一致。</p>\r\n	<p> 本条所称的“借款成功时”系指本协议签署日。</p>\r\n	<p> 3、    如乙方和丙方协商一致调整借款管理费和居间服务费时，无需经过甲方同意。 </p>\r\n	<br/>\r\n	<p style=\"text-align: left;font-weight: 600;\">\r\n		第四条 违约责任\r\n	</p>\r\n	<p> 1、协议各方均应严格履行合同义务，非经各方协商一致或依照本协议约定，任何一方不得解除本协议。\r\n	</p>\r\n	<p>2、任何一方违约，违约方应承担因违约使得其他各方产生的费用和损失，包括但不限于调查、诉讼费、律师费等，应由违约方承担。如违约方为乙方的，甲方有权立即解除本协议，并要求乙方立即偿还未偿还的本金、利息、罚息、违约金。此时，乙方还应向丙方支付所有应付的借款管理费。如本协议提前解除时，乙方在{$SITE_URL}网站的账户里有任何余款的，丙方有权按照本协议第四条第3项的清偿顺序将乙方的余款用于清偿，并要求乙方支付因此产生的相关费用。</p>\r\n	<p>3、乙方的每期还款均应按照如下顺序清偿：</p>\r\n	<p>（1）根据本协议产生的其他全部费用；</p>\r\n	<p>（2）本协议第四条第4款约定的罚息； </p>\r\n	<p>（3）本协议第四条第5款约定的逾期管理费；</p>\r\n	<p>（4）拖欠的利息； </p>\r\n	<p>（5）拖欠的本金； </p>\r\n	<p>（6）拖欠丙方的借款管理费；\r\n	</p>\r\n	<p>（7）正常的利息； </p>\r\n	<p>（8）正常的本金； </p>\r\n	<p>（9）丙方的借款管理费；</p>\r\n	<p> 4、乙方应严格履行还款义务，如乙方逾期还款，则应按照下述条款向甲方支付逾期罚息，自逾期开始之后，逾期本金的正常利息停止计算。 </p>\r\n	<p>罚息总额 = 逾期本息总额×对应罚息利率×逾期天数；</p>\r\n</div>\r\n<div>\r\n	<br/>\r\n<table border=\"1\" style=\"margin: 0px auto; border-collapse: collapse; border: 1px solid rgb(0, 0, 0); width: 70%; \">\r\n		<tr>\r\n			<td style=\"padding-left:10px\">\r\n				逾期天数\r\n			</td>\r\n			<td style=\"padding-left:10px\">\r\n				1—<?php echo (int)app_conf(\"YZ_IMPSE_DAY\")-1; ?>天\r\n			</td>\r\n			<td style=\"padding-left:10px\">\r\n				{function name=\"app_conf\" v=\"YZ_IMPSE_DAY\"}天及以上\r\n			</td>\r\n		</tr>\r\n		<tr>\r\n			<td style=\"padding-left:10px\">\r\n				罚息利率\r\n			</td>\r\n			<td style=\"padding-left:10px\">\r\n				{$deal.impose_fee_day1}%\r\n			</td>\r\n			<td style=\"padding-left:10px\">\r\n				{$deal.impose_fee_day2}%\r\n			</td>\r\n		</tr>\r\n	</table>\r\n	<br/>\r\n	<p>\r\n		5、乙方应严格履行还款义务，如乙方逾期还款，则应按照下述条款向丙方支付逾期管理费：\r\n	</p>\r\n	<p>\r\n		逾期管理费总额 = 逾期本息总额×对应逾期管理费率×逾期天数；\r\n	</p>\r\n</div>\r\n<br/>\r\n<table border=\"1\" style=\"margin: 0px auto; border-collapse: collapse; border: 1px solid rgb(0, 0, 0); width: 70%; \">\r\n	<tr>\r\n		<td style=\"padding-left:10px\">\r\n			逾期天数\r\n		</td>\r\n		<td style=\"padding-left:10px\">\r\n			1—<?php echo (int)app_conf(\"YZ_IMPSE_DAY\")-1; ?>天\r\n		</td>\r\n		<td style=\"padding-left:10px\">\r\n			{function name=\"app_conf\" v=\"YZ_IMPSE_DAY\"}天及以上\r\n		</td>\r\n	</tr>\r\n	<tr>\r\n		<td style=\"padding-left:10px\">\r\n			逾期管理费费率\r\n		</td>\r\n		<td style=\"padding-left:10px\">\r\n			{$deal.manage_impose_fee_day1}%\r\n		</td>\r\n		<td style=\"padding-left:10px\">\r\n			{$deal.manage_impose_fee_day2}%\r\n		</td>\r\n	</tr>\r\n</table>\r\n<br/>\r\n<div>\r\n	<p>\r\n		6、如果乙方逾期支付任何一期还款超过30天，或乙方在逾期后出现逃避、拒绝沟通或拒绝承认欠款事实等恶意行为，本协议项下的全部借款本息及借款管理费均提前到期，乙方应立即清偿本协议下尚未偿付的全部本金、利息、罚息、借款管理费及根据本协议产生的其他全部费用。\r\n	</p>\r\n	<p>\r\n		7、如果乙方逾期支付任何一期还款超过30天，或乙方在逾期后出现逃避、拒绝沟通或拒绝承认欠款事实等恶意行为，丙方有权将乙方的“逾期记录”记入人民银行公民征信系统，丙方不承担任何法律责任。\r\n	</p>\r\n	<p>\r\n		8、如果乙方逾期支付任何一期还款超过30天，或乙方在逾期后出现逃避、拒绝沟通或拒绝承认欠款事实等恶意行为，丙方有权将乙方违约失信的相关信息及乙方其他信息向媒体、用人单位、公安机关、检查机关、法律机关披露，丙方不承担任何责任。\r\n	</p>\r\n	<p>\r\n		9、在乙方还清全部本金、利息、借款管理费、罚息、逾期管理费之前，罚息及逾期管理费的计算不停止。\r\n	</p>\r\n	<p>\r\n		10、本协议中的所有甲方与乙方之间的借款均是相互独立的，一旦乙方逾期未归还借款本息，甲方中的任何一个出借人均有权单独向乙方追索或者提起诉讼。如乙方逾期支付借款管理费或提供虚假信息的，丙方亦可单独向乙方追索或者提起诉讼。\r\n	</p>\r\n	<br/>\r\n	<p style=\"text-align: left;font-weight: 600;\">\r\n		第五条 提前还款\r\n	</p>\r\n	<p>\r\n		1、乙方可在借款期间任何时候提前偿还剩余借款。\r\n	</p>\r\n	<p>\r\n		2、提前偿还全部剩余借款\r\n	</p>\r\n	<p style=\"padding-left: 15px\">\r\n		1）乙方提前清偿全部剩余借款时，应向甲方支付当期应还本息，剩余本金及提前还款补偿（补偿金额为剩余本金的{$deal.compensate_fee}%）。\r\n	</p>\r\n	<p style=\"padding-left: 15px\">\r\n		2）乙方提前清偿全部剩余借款时，应向丙方支付当期借款管理费，乙方无需支付剩余还款期的借款管理费。\r\n	</p>\r\n	<p>\r\n		3、提前偿还部分借款\r\n	</p>\r\n	<p style=\"padding-left: 15px\">\r\n		1）乙方提前偿还部分借款，仍应向甲方支付全部借款利息。\r\n	</p>\r\n	<p style=\"padding-left: 15px\">\r\n		2）乙方提前偿还部分借款，仍应向丙方支付全部应付的借款管理费。\r\n	</p>\r\n	<p>\r\n		4、任何形式的提前还款不影响丙方向乙方收取在本协议第三条中说明的居间服务费。\r\n	</p>\r\n	<br/>\r\n	<p style=\"text-align: left;font-weight: 600;\">\r\n		第六条	法律及争议解决\r\n	</p>\r\n	 <p>\r\n		 本协议的签订、履行、终止、解释均适用中华人民共和国法律，并由丙方所在地{function name=\"app_conf\" v=\'COMPANY_REG_ADDRESS\'}人民法院管辖。\r\n	</p>\r\n	<br/>\r\n	<p style=\"text-align: left;font-weight: 600;\">\r\n		第七条	附则\r\n	</p>\r\n	<p>\r\n		1、本协议采用电子文本形式制成，并永久保存在丙方为此设立的专用服务器上备查，各方均认可该形式的协议效力。\r\n	</p>\r\n	<p>\r\n		2、本协议自文本最终生成之日生效。\r\n	</p>\r\n	<p>\r\n		3、本协议签订之日起至借款全部清偿之日止，乙方或甲方有义务在下列信息变更三日内提供更新后的信息给丙方：本人、本人的家庭联系人及紧急联系人、工作单位、居住地址、住所电话、手机号码、电子邮箱、银行账户的变更。若因任何一方不及时提供上述变更信息而带来的损失或额外费用应由该方承担。\r\n	</p>\r\n	<p>\r\n		4、如果本协议中的任何一条或多条违反适用的法律法规，则该条将被视为无效，但该无效条款并不影响本协议其他条款的效力。\r\n	</p>\r\n        <br>\r\n	{if $deal.attachment}\r\n	<p style=\"text-align: left;font-weight: 600;\">\r\n		第八条	附件\r\n	</p>\r\n	<p>\r\n		{$deal.attachment}\r\n	</p>\r\n	{/if}\r\n</div>\r\n<br/>\r\n<div style=\"width: 98%;text-align: right;\">\r\n	<p>\r\n		{function name=\"to_date\" v=\"$deal.repay_start_time\" f=\"Y年m月d日\"}\r\n	</p>\r\n</div>', '1', '0');
INSERT INTO `%DB_PREFIX%contract` VALUES ('3', '付息还本合同范本【普通】', '<div style=\"width: 98%;text-align: right;\">编号：<span>{$deal.deal_sn}</span></div>\r\n<h2 align=\"center\">借款协议</h2>\r\n<br/>\r\n<div style=\"text-align: left;font-weight: 600;\">甲方（出借人）：</div>\r\n<table border=\"1\" style=\"margin: 0px auto; border-collapse: collapse; border: 1px solid rgb(0, 0, 0); width: 70%; \">\r\n    <tr>\r\n	<td style=\"text-align:center;\"> {$SITE_TITLE}用户名</td>\r\n	<td style=\"text-align:center;\"> 借出金额</td>\r\n	<td style=\"text-align:center;\">借款期限</td>\r\n	<td style=\"text-align:center;\"> 每月应收利息</td>\r\n	<td style=\"text-align:center;\"> 到期还本金</td>\r\n    </tr>\r\n    {foreach from=\"$loan_list\" item=\"loan\"}\r\n    <tr>\r\n	<td style=\"text-align:center;\">{$loan.user_name}</td>\r\n	<td style=\"text-align:right;padding-right:10px\">{$CURRENCY_UNIT}{function name=\"number_format\" v=$loan.money f=2}</td>\r\n	<td style=\"text-align:center;\">{$deal.repay_time}个月</td>\r\n	<td style=\"text-align:right;padding-right:10px\">\r\n	{$CURRENCY_UNIT}{function name=\"number_format\" v=$loan.get_repay_money f=2}\r\n	</td>\r\n	<td style=\"text-align:right;padding-right:10px\">{$CURRENCY_UNIT}{function name=\"number_format\" v=$loan.money f=2}</td>\r\n    </tr>\r\n    {/foreach}\r\n    <tr>\r\n	<td style=\"text-align:center;\">总计</td>\r\n	<td style=\"text-align:right;padding-right:10px\">{$CURRENCY_UNIT}{function name=\"number_format\" v=$deal.load_money f=2}</td>\r\n	<td> </td>\r\n	<td> </td>\r\n	<td> </td>\r\n    </tr>\r\n</table>\r\n<p>注：因计算中存在四舍五入，最后一期应收本息与之前略有不同</p>\r\n<br/>\r\n<div>\r\n	<p style=\"text-align: left;font-weight: 600;\">乙方（借款人）：</p>\r\n	<p style=\"text-align: left;font-weight: 600;\">{$SITE_TITLE}用户名：<span>{$user_info.user_name}</span></p>\r\n	<br/>\r\n	<p style=\"text-align: left;font-weight: 600;\"> 丙方（见证人）：{function name=\"app_conf\" v=\"COMPANY\"} </p>\r\n	<p style=\"text-align: left;font-weight: 600;\">联系方式：{function name=\"app_conf\" v=\"COMPANY_ADDRESS\"}</p>\r\n</div>\r\n<br/>\r\n<p><strong>鉴于：</strong></p>\r\n<p>1、丙方是一家在{function name=\"app_conf\" v=\"COMPANY_REG_ADDRESS\"}合法成立并有效存续的有限责任公司，拥有{$SITE_URL} 网站（以下简称“该网站”）的经营权，提供信用咨询，为交易提供信息服务；</p>\r\n<p>2、乙方已在该网站注册，并承诺其提供给丙方的信息是完全真实的；</p>\r\n<p>3、甲方承诺对本协议涉及的借款具有完全的支配能力，是其自有闲散资金，为其合法所得；并承诺其提供给丙方的信息是完全真实的；</p>\r\n<p>4、乙方有借款需求，甲方亦同意借款，双方有意成立借贷关系；</p>\r\n<br/>\r\n<p style=\"text-align: left;font-weight: 600;\">各方经协商一致，于<span> {function name=\"to_date\" v=\"$deal.repay_start_time\" f=\"Y年m月d日\"}</span>签订如下协议，共同遵照履行：</p>\r\n<br/>\r\n<p style=\"text-align: left;font-weight: 600;\"> 第一条 借款基本信息</p>\r\n<br/>\r\n<table border=\"1\" style=\"margin: 0px auto; border-collapse: collapse; border: 1px solid rgb(0, 0, 0); width: 70%; \">\r\n	<tr>\r\n		<td width=\"20%\" style=\"padding-left:10px\"> 借款详细用途</td>\r\n		<td style=\"padding-left:10px\"> {$deal.type_info.name}</td>\r\n	</tr>\r\n	<tr>\r\n		<td style=\"padding-left:10px\">借款本金数额</td>\r\n		<td style=\"padding-left:10px\">\r\n			{$CURRENCY_UNIT}{function name=\"number_format\" v=$deal.borrow_amount f=2}（各出借人借款本金数额详见本协议文首表格）\r\n		</td>\r\n	</tr>\r\n	<tr>\r\n		<td style=\"padding-left:10px\"> 月偿还利息数额\r\n		</td>\r\n		<td style=\"padding-left:10px\">\r\n			{$deal.month_repay_money_format}（因计算中存在四舍五入，最后一期应还金额与之前可能有所不同，为{$CURRENCY_UNIT}<?php echo number_format($this->_var[\'deal\'][\'last_month_repay_money\'] -  $this->_var[\'deal\'][\'borrow_amount\'],2);?>）\r\n		</td>\r\n	</tr>\r\n\r\n       <tr>\r\n		<td style=\"padding-left:10px\"> 到期本金\r\n		</td>\r\n		<td style=\"padding-left:10px\">\r\n			{$CURRENCY_UNIT}{function name=\"number_format\" v=$deal.borrow_amount f=2}\r\n		</td>\r\n	</tr>\r\n	\r\n	<tr>\r\n		<td style=\"padding-left:10px\"> 还款分期月数\r\n		</td>\r\n		<td style=\"padding-left:10px\">\r\n			{$deal.repay_time}个月\r\n		</td>\r\n	</tr>\r\n	\r\n	<tr>\r\n		<td style=\"padding-left:10px\">\r\n			还款日\r\n		</td>\r\n		<td style=\"padding-left:10px\">\r\n			 自{function name=\"to_date\" v=\"$deal.repay_start_time\" f=\"Y年m月d日\"}起，每月{function name=\"to_date\" v=\"$deal.repay_start_time\" f=\"d\"}日（24:00前，节假日不顺延）\r\n		</td>\r\n	</tr>\r\n	<tr>\r\n		<td style=\"padding-left:10px\"> 借款期限\r\n		</td>\r\n		<td style=\"padding-left:10px\">\r\n			{$deal.repay_time}个月，{function name=\"to_date\" v=\"$deal.repay_start_time\" f=\"Y年m月d日\"}起，至  {function name=\"to_date\" v=\"$deal.end_repay_time\" f=\"Y年m月d日\"}日止\r\n		</td>\r\n	</tr>\r\n</table>\r\n<br/>\r\n<div>\r\n	<p style=\"text-align: left;font-weight: 600;\">\r\n		第二条 各方权利和义务\r\n	</p>\r\n	<p style=\"text-align: left;font-weight: 600;\">\r\n		<u>甲方的权利和义务</u>\r\n	</p>\r\n	<p> 1、	甲方应按合同约定的借款期限起始日期将足额的借款本金支付给乙方。\r\n	</p>\r\n	<p> 2、	甲方享有其所出借款项所带来的利息收益。\r\n	</p>\r\n	<p>3、	如乙方违约，甲方有权要求丙方提供其已获得的乙方信息，丙方应当提供。\r\n	</p>\r\n	<p>4、	无须通知乙方，甲方可以根据自己的意愿进行本协议下其对乙方债权的转让。在甲方的债权转让后，乙方需对债权受让人继续履行本协议下其对甲方的还款义务，不得以未接到债权转让通知为由拒绝履行还款义务。\r\n	</p>\r\n	<p> 5、	甲方应主动缴纳由利息所得带来的可能的税费。\r\n	</p>\r\n	<p>6、	如乙方实际还款金额少于本协议约定的本金、利息及违约金的，甲方各出借人同意各自按照其于本协议文首约定的借款比例收取还款。\r\n	</p>\r\n	<p> 7、	甲方应确保其提供信息和资料的真实性，不得提供虚假信息或隐瞒重要事实。\r\n	</p>\r\n	<p style=\"text-align: left;font-weight: 600;\">\r\n		<u>乙方权利和义务</u>\r\n	</p>\r\n	<p> 1、	乙方必须按期足额向甲方偿还本金和利息。\r\n	</p>\r\n	<p> 2、    乙方必须按期足额向丙方支付借款管理费用。\r\n	</p>\r\n	<p>3、	乙方承诺所借款项不用于任何违法用途。\r\n	</p>\r\n	<p>4、	乙方应确保其提供的信息和资料的真实性，不得提供虚假信息或隐瞒重要事实。\r\n	</p>\r\n	<p>5、	乙方有权了解其在丙方的信用评审进度及结果。\r\n	</p>\r\n	<p> 6、	乙方不得将本协议项下的任何权利义务转让给任何其他方。\r\n	</p>\r\n	<p style=\"text-align: left;font-weight: 600;\">\r\n		<u>丙方的权利和义务</u>\r\n	</p>\r\n	<p>1、甲方授权并委托丙方代其收取本协议文首所约定的出借人每月应收本息，代收后按照甲方的要求进行处置，乙方对此表示认可。\r\n	</p>\r\n	<p>2、甲方授权并委托丙方将其支付的出借本金直接划付至乙方账户，乙方对此表示认可。\r\n	</p>\r\n	<p> 3、甲、乙双方一致同意，在有必要时，丙方有权代甲方对乙方进行关于本协议借款的违约提醒及催收工作，包括但不限于：电话通知、上门催收提醒、发律师函、对乙方提起诉讼等。甲方在此确认委托丙方为其进行以上工作，并授权丙方可以将此工作委托给本协议外的其他方进行。乙方对前述委托的提醒、催收事项已明确知晓并应积极配合。\r\n	</p>\r\n	<p>4、丙方有权按月向乙方收取双方约定的借款管理费，并在有必要时对乙方进行违约提醒及催收工作，包括但不限于电话通知、发律师函、对乙方提起诉讼等。丙方有权将此违约提醒及催收工作委托给本协议外的其他方进行。\r\n	</p>\r\n	<p> 5、丙方接受甲乙双方的委托行为所产生的法律后果由相应委托方承担。如因乙方或甲方或其他方（包括但不限于技术问题）造成的延误或错误，丙方不承担任何责任。\r\n	</p>\r\n	<p> 6、丙方应对甲方和乙方的信息及本协议内容保密；如任何一方违约，或因相关权力部门要求（包括但不限于法院、仲裁机构、金融监管机构等），丙方有权披露。\r\n	</p>\r\n	<p>7、丙方根据本协议对乙方进行违约提醒及催收工作时，可在其认为必要时进行上门催收提醒，即丙方派出人员（至少2名）至乙方披露的住所地或经常居住地（联系地址）处催收和进行违约提醒，同时向乙方发送催收通知单，乙方应当签收，乙方不签收的，不影响上门催收提醒的进行。丙方采取上门催收提醒的，乙方应当向丙方支付上门提醒费用，收费标准为每次人民币1000.00元，此外，乙方还应向丙方支付进行上门催收提醒服务的差旅费（包括但不限于交通费、食宿费等）。\r\n	</p>\r\n	<br/>\r\n	<p style=\"text-align: left;font-weight: 600;\">\r\n		第三条 借款管理费及居间服务费\r\n	</p>\r\n	<p>1、	在本协议中，“借款管理费”和“居间服务费”是指因丙方为乙方提供信用咨询、评估、还款提醒、账户管理、还款特殊情况沟通、本金保障等系列信用相关服务（统称“信用服务”）而由乙方支付给丙方的报酬。\r\n	</p>\r\n	<p>2、	对于丙方向乙方提供的一系列信用服务，乙方同意在借款成功时向丙方支付本协议第一条约定借款本金总额的{function name=\"number_format\" v=$deal.services_fee f=1}%(即人民币<?php echo number_format(floatval($this->_var[\'deal\'][\'services_fee\'])*$this->_var[\'deal\'][\'borrow_amount\']/100,2); ?>元)作为居间服务费，该“居间服务费”由乙方授权并委托丙方在丙方根据本协议规定的“丙方的权利和义务”第2款规定向乙方划付出借本金时从本金中予以扣除，即视为乙方已缴纳。在本协议约定的借款期限内，乙方应每月向丙方支付本协议第一条约定借款本金总额的{function name=\"app_conf\" v=\"MANAGE_FEE\"}% (即人民币{function name=\"number_format\" v=\"$deal.month_manage_money\" f=2}元)，作为借款管理费用，共需支付{$deal.repay_time}期，共计人民币{function name=\"number_format\" v=\"$deal.all_manage_money\" f=2} 元，借款管理费的缴纳时间与本协议第一条约定的还款日一致。</p>\r\n	<p> 本条所称的“借款成功时”系指本协议签署日。</p>\r\n	<p> 3、    如乙方和丙方协商一致调整借款管理费和居间服务费时，无需经过甲方同意。 </p>\r\n	<br/>\r\n	<p style=\"text-align: left;font-weight: 600;\">\r\n		第四条 违约责任\r\n	</p>\r\n	<p> 1、协议各方均应严格履行合同义务，非经各方协商一致或依照本协议约定，任何一方不得解除本协议。\r\n	</p>\r\n	<p>2、任何一方违约，违约方应承担因违约使得其他各方产生的费用和损失，包括但不限于调查、诉讼费、律师费等，应由违约方承担。如违约方为乙方的，甲方有权立即解除本协议，并要求乙方立即偿还未偿还的本金、利息、罚息、违约金。此时，乙方还应向丙方支付所有应付的借款管理费。如本协议提前解除时，乙方在{$SITE_URL}网站的账户里有任何余款的，丙方有权按照本协议第四条第3项的清偿顺序将乙方的余款用于清偿，并要求乙方支付因此产生的相关费用。</p>\r\n	<p>3、乙方的每期还款均应按照如下顺序清偿：</p>\r\n	<p>（1）根据本协议产生的其他全部费用；</p>\r\n	<p>（2）本协议第四条第4款约定的罚息； </p>\r\n	<p>（3）本协议第四条第5款约定的逾期管理费；</p>\r\n	<p>（4）拖欠的利息； </p>\r\n	<p>（5）拖欠的本金； </p>\r\n	<p>（6）拖欠丙方的借款管理费；\r\n	</p>\r\n	<p>（7）正常的利息； </p>\r\n	<p>（8）正常的本金； </p>\r\n	<p>（9）丙方的借款管理费；</p>\r\n	<p> 4、乙方应严格履行还款义务，如乙方逾期还款，则应按照下述条款向甲方支付逾期罚息，自逾期开始之后，逾期本金的正常利息停止计算。 </p>\r\n	<p>罚息总额 = 逾期本息总额×对应罚息利率×逾期天数；</p>\r\n</div>\r\n<div>\r\n	<br/>\r\n	<table border=\"1\" style=\"margin: 0px auto; border-collapse: collapse; border: 1px solid rgb(0, 0, 0); width: 70%; \">\r\n		<tr>\r\n			<td style=\"padding-left:10px\">\r\n				逾期天数\r\n			</td>\r\n			<td style=\"padding-left:10px\">\r\n				1—<?php echo (int)app_conf(\"YZ_IMPSE_DAY\")-1; ?>天\r\n			</td>\r\n			<td style=\"padding-left:10px\">\r\n				{function name=\"app_conf\" v=\"YZ_IMPSE_DAY\"}天及以上\r\n			</td>\r\n		</tr>\r\n		<tr>\r\n			<td style=\"padding-left:10px\">\r\n				罚息利率\r\n			</td>\r\n			<td style=\"padding-left:10px\">\r\n				{$deal.impose_fee_day1}%\r\n			</td>\r\n			<td style=\"padding-left:10px\">\r\n				{$deal.impose_fee_day2}%\r\n			</td>\r\n		</tr>\r\n	</table>\r\n	<br/>\r\n	<p>\r\n		5、乙方应严格履行还款义务，如乙方逾期还款，则应按照下述条款向丙方支付逾期管理费：\r\n	</p>\r\n	<p>\r\n		逾期管理费总额 = 逾期本息总额×对应逾期管理费率×逾期天数；\r\n	</p>\r\n</div>\r\n<br/>\r\n<table border=\"1\" style=\"margin: 0px auto; border-collapse: collapse; border: 1px solid rgb(0, 0, 0); width: 70%; \">\r\n	<tr>\r\n		<td style=\"padding-left:10px\">\r\n			逾期天数\r\n		</td>\r\n		<td style=\"padding-left:10px\">\r\n			1—<?php echo (int)app_conf(\"YZ_IMPSE_DAY\")-1; ?>天\r\n		</td>\r\n		<td style=\"padding-left:10px\">\r\n			{function name=\"app_conf\" v=\"YZ_IMPSE_DAY\"}天及以上\r\n		</td>\r\n	</tr>\r\n	<tr>\r\n		<td style=\"padding-left:10px\">\r\n			逾期管理费费率\r\n		</td>\r\n		<td style=\"padding-left:10px\">\r\n			{$deal.manage_impose_fee_day1}%\r\n		</td>\r\n		<td style=\"padding-left:10px\">\r\n			{$deal.manage_impose_fee_day2}%\r\n		</td>\r\n	</tr>\r\n</table>\r\n<br/>\r\n<div>\r\n	<p>\r\n		6、如果乙方逾期支付任何一期还款超过30天，或乙方在逾期后出现逃避、拒绝沟通或拒绝承认欠款事实等恶意行为，本协议项下的全部借款本息及借款管理费均提前到期，乙方应立即清偿本协议下尚未偿付的全部本金、利息、罚息、借款管理费及根据本协议产生的其他全部费用。\r\n	</p>\r\n	<p>\r\n		7、如果乙方逾期支付任何一期还款超过30天，或乙方在逾期后出现逃避、拒绝沟通或拒绝承认欠款事实等恶意行为，丙方有权将乙方的“逾期记录”记入人民银行公民征信系统，丙方不承担任何法律责任。\r\n	</p>\r\n	<p>\r\n		8、如果乙方逾期支付任何一期还款超过30天，或乙方在逾期后出现逃避、拒绝沟通或拒绝承认欠款事实等恶意行为，丙方有权将乙方违约失信的相关信息及乙方其他信息向媒体、用人单位、公安机关、检查机关、法律机关披露，丙方不承担任何责任。\r\n	</p>\r\n	<p>\r\n		9、在乙方还清全部本金、利息、借款管理费、罚息、逾期管理费之前，罚息及逾期管理费的计算不停止。\r\n	</p>\r\n	<p>\r\n		10、本协议中的所有甲方与乙方之间的借款均是相互独立的，一旦乙方逾期未归还借款本息，甲方中的任何一个出借人均有权单独向乙方追索或者提起诉讼。如乙方逾期支付借款管理费或提供虚假信息的，丙方亦可单独向乙方追索或者提起诉讼。\r\n	</p>\r\n	<br/>\r\n	<p style=\"text-align: left;font-weight: 600;\">\r\n		第五条 提前还款\r\n	</p>\r\n	<p>\r\n		1、乙方可在借款期间任何时候提前偿还剩余借款。\r\n	</p>\r\n	<p>\r\n		2、提前偿还全部剩余借款\r\n	</p>\r\n	<p style=\"padding-left: 15px\">\r\n		1）乙方提前清偿全部剩余借款时，应向甲方支付当期应还本息，剩余本金及提前还款补偿（补偿金额为剩余本金的{$deal.compensate_fee}%）。\r\n	</p>\r\n	<p style=\"padding-left: 15px\">\r\n		2）乙方提前清偿全部剩余借款时，应向丙方支付当期借款管理费，乙方无需支付剩余还款期的借款管理费。\r\n	</p>\r\n	<p>\r\n		3、提前偿还部分借款\r\n	</p>\r\n	<p style=\"padding-left: 15px\">\r\n		1）乙方提前偿还部分借款，仍应向甲方支付全部借款利息。\r\n	</p>\r\n	<p style=\"padding-left: 15px\">\r\n		2）乙方提前偿还部分借款，仍应向丙方支付全部应付的借款管理费。\r\n	</p>\r\n	<p>\r\n		4、任何形式的提前还款不影响丙方向乙方收取在本协议第三条中说明的居间服务费。\r\n	</p>\r\n	<br/>\r\n	<p style=\"text-align: left;font-weight: 600;\">\r\n		第六条	法律及争议解决\r\n	</p>\r\n	 <p>\r\n		 本协议的签订、履行、终止、解释均适用中华人民共和国法律，并由丙方所在地{function name=\"app_conf\" v=\'COMPANY_REG_ADDRESS\'}人民法院管辖。\r\n	</p>\r\n	<br/>\r\n	<p style=\"text-align: left;font-weight: 600;\">\r\n		第七条	附则\r\n	</p>\r\n	<p>\r\n		1、本协议采用电子文本形式制成，并永久保存在丙方为此设立的专用服务器上备查，各方均认可该形式的协议效力。\r\n	</p>\r\n	<p>\r\n		2、本协议自文本最终生成之日生效。\r\n	</p>\r\n	<p>\r\n		3、本协议签订之日起至借款全部清偿之日止，乙方或甲方有义务在下列信息变更三日内提供更新后的信息给丙方：本人、本人的家庭联系人及紧急联系人、工作单位、居住地址、住所电话、手机号码、电子邮箱、银行账户的变更。若因任何一方不及时提供上述变更信息而带来的损失或额外费用应由该方承担。\r\n	</p>\r\n	<p>\r\n		4、如果本协议中的任何一条或多条违反适用的法律法规，则该条将被视为无效，但该无效条款并不影响本协议其他条款的效力。\r\n	</p>\r\n        <br>\r\n	{if $deal.attachment}\r\n	<p style=\"text-align: left;font-weight: 600;\">\r\n		第八条	附件\r\n	</p>\r\n	<p>\r\n		{$deal.attachment}\r\n	</p>\r\n	{/if}\r\n</div>\r\n<br/>\r\n<div style=\"width: 98%;text-align: right;\">\r\n	<p>\r\n		{function name=\"to_date\" v=\"$deal.repay_start_time\" f=\"Y年m月d日\"}\r\n	</p>\r\n</div>', '1', '0');
INSERT INTO `%DB_PREFIX%contract` VALUES ('4', '付息还本合同范本【担保】', '<div style=\"width: 98%;text-align: right;\">编号：<span>{$deal.deal_sn}</span></div>\r\n<h2 align=\"center\">借款协议</h2>\r\n<br/>\r\n<div style=\"text-align: left;font-weight: 600;\">甲方（出借人）：</div>\r\n<table border=\"1\" style=\"margin: 0px auto; border-collapse: collapse; border: 1px solid rgb(0, 0, 0); width: 70%; \">\r\n    <tr>\r\n	<td style=\"text-align:center;\"> {$SITE_TITLE}用户名</td>\r\n	<td style=\"text-align:center;\"> 借出金额</td>\r\n	<td style=\"text-align:center;\">借款期限</td>\r\n	<td style=\"text-align:center;\"> 每月应收利息</td>\r\n	<td style=\"text-align:center;\"> 到期还本金</td>\r\n    </tr>\r\n    {foreach from=\"$loan_list\" item=\"loan\"}\r\n    <tr>\r\n	<td style=\"text-align:center;\">{$loan.user_name}</td>\r\n	<td style=\"text-align:right;padding-right:10px\">{$CURRENCY_UNIT}{function name=\"number_format\" v=$loan.money f=2}</td>\r\n	<td style=\"text-align:center;\">{$deal.repay_time}个月</td>\r\n	<td style=\"text-align:right;padding-right:10px\">\r\n	{$CURRENCY_UNIT}{function name=\"number_format\" v=$loan.get_repay_money f=2}\r\n	</td>\r\n	<td style=\"text-align:right;padding-right:10px\">{$CURRENCY_UNIT}{function name=\"number_format\" v=$loan.money f=2}</td>\r\n	</tr>\r\n    {/foreach}\r\n    <tr>\r\n	<td style=\"text-align:center;\">总计</td>\r\n	<td style=\"text-align:right;padding-right:10px\">{$CURRENCY_UNIT}{function name=\"number_format\" v=$deal.load_money f=2}</td>\r\n	<td> </td>\r\n	<td> </td>\r\n	<td> </td>\r\n    </tr>\r\n</table>\r\n<p>注：因计算中存在四舍五入，最后一期应收本息与之前略有不同</p>\r\n<br/>\r\n<div>\r\n	<p style=\"text-align: left;font-weight: 600;\">乙方（借款人）：</p>\r\n	<p style=\"text-align: left;font-weight: 600;\">{$SITE_TITLE}用户名：<span>{$user_info.user_name}</span></p>\r\n	<br/>\r\n	<p style=\"text-align: left;font-weight: 600;\"> 丙方（见证人）：{function name=\"app_conf\" v=\"COMPANY\"} </p>\r\n	<p style=\"text-align: left;font-weight: 600;\">联系方式：{function name=\"app_conf\" v=\"COMPANY_ADDRESS\"}</p>\r\n	<br/>\r\n	<p style=\"text-align: left;font-weight: 600;\"> 丁方（担保方）：{$deal.agency_info.name} </p>\r\n	<p style=\"text-align: left;font-weight: 600;\">联系方式：{$deal.agency_info.address}</p>\r\n</div>\r\n<br/>\r\n<p><strong>鉴于：</strong></p>\r\n<p>1、丙方是一家在{function name=\"app_conf\" v=\"COMPANY_REG_ADDRESS\"}合法成立并有效存续的有限责任公司，拥有{$SITE_URL} 网站（以下简称“该网站”）的经营权，提供信用咨询，为交易提供信息服务；</p>\r\n<p>2、乙方已在该网站注册，并承诺其提供给丙方的信息是完全真实的；</p>\r\n<p>3、甲方承诺对本协议涉及的借款具有完全的支配能力，是其自有闲散资金，为其合法所得；并承诺其提供给丙方的信息是完全真实的；</p>\r\n<p>4、乙方有借款需求，甲方亦同意借款，双方有意成立借贷关系；</p>\r\n<p>5、丁方愿意作为甲方借款提供保障。当乙方逾期3天仍未清偿借款本息，则甲方本协议项下的债权（逾期本息）自动转让给丁方，丁方将在第4天垫付该标的的未清偿借款本息给甲方；</p>\r\n<br/>\r\n<p style=\"text-align: left;font-weight: 600;\">各方经协商一致，于<span> {function name=\"to_date\" v=\"$deal.repay_start_time\" f=\"Y年m月d日\"}</span>签订如下协议，共同遵照履行：</p>\r\n<br/>\r\n<p style=\"text-align: left;font-weight: 600;\"> 第一条 借款基本信息</p>\r\n<br/>\r\n<table border=\"1\" style=\"margin: 0px auto; border-collapse: collapse; border: 1px solid rgb(0, 0, 0); width: 70%; \">\r\n	<tr>\r\n		<td width=\"20%\" style=\"padding-left:10px\"> 借款详细用途</td>\r\n		<td style=\"padding-left:10px\"> {$deal.type_info.name}</td>\r\n	</tr>\r\n	<tr>\r\n		<td style=\"padding-left:10px\">借款本金数额</td>\r\n		<td style=\"padding-left:10px\">\r\n			{$CURRENCY_UNIT}{function name=\"number_format\" v=$deal.borrow_amount f=2}（各出借人借款本金数额详见本协议文首表格）\r\n		</td>\r\n	</tr>\r\n	<tr>\r\n		<td style=\"padding-left:10px\">月偿还利息数额\r\n		</td>\r\n		<td style=\"padding-left:10px\">\r\n			{$deal.month_repay_money_format}（因计算中存在四舍五入，最后一期应还金额与之前可能有所不同，为{$CURRENCY_UNIT}<?php echo number_format($this->_var[\'deal\'][\'last_month_repay_money\'] -  $this->_var[\'deal\'][\'borrow_amount\'],2);?>）\r\n		</td>\r\n	</tr>\r\n	 <tr>\r\n		<td style=\"padding-left:10px\"> 到期本金\r\n		</td>\r\n		<td style=\"padding-left:10px\">\r\n			{$CURRENCY_UNIT}{function name=\"number_format\" v=$deal.borrow_amount f=2}\r\n		</td>\r\n	</tr>\r\n	<tr>\r\n		<td style=\"padding-left:10px\"> 还款分期月数\r\n		</td>\r\n		<td style=\"padding-left:10px\">\r\n			{$deal.repay_time}个月\r\n		</td>\r\n	</tr>\r\n	<tr>\r\n		<td style=\"padding-left:10px\">\r\n			还款日\r\n		</td>\r\n		<td style=\"padding-left:10px\">\r\n			 自{function name=\"to_date\" v=\"$deal.repay_start_time\" f=\"Y年m月d日\"}起，每月{function name=\"to_date\" v=\"$deal.repay_start_time\" f=\"d\"}日（24:00前，节假日不顺延）\r\n		</td>\r\n	</tr>\r\n	<tr>\r\n		<td style=\"padding-left:10px\"> 借款期限\r\n		</td>\r\n		<td style=\"padding-left:10px\">\r\n			{$deal.repay_time}个月，{function name=\"to_date\" v=\"$deal.repay_start_time\" f=\"Y年m月d日\"}起，至  {function name=\"to_date\" v=\"$deal.end_repay_time\" f=\"Y年m月d日\"}日止\r\n		</td>\r\n	</tr>\r\n</table>\r\n<br/>\r\n<div>\r\n	<p style=\"text-align: left;font-weight: 600;\">\r\n		第二条 各方权利和义务\r\n	</p>\r\n	<p style=\"text-align: left;font-weight: 600;\">\r\n		<u>甲方的权利和义务</u>\r\n	</p>\r\n	<p> 1、	甲方应按合同约定的借款期限起始日期将足额的借款本金支付给乙方。\r\n	</p>\r\n	<p> 2、	甲方享有其所出借款项所带来的利息收益。\r\n	</p>\r\n	<p>3、	如乙方违约，甲方有权要求丙方提供其已获得的乙方信息，丙方应当提供。\r\n	</p>\r\n	<p>4、	无须通知乙方，甲方可以根据自己的意愿进行本协议下其对乙方债权的转让。在甲方的债权转让后，乙方需对债权受让人继续履行本协议下其对甲方的还款义务，不得以未接到债权转让通知为由拒绝履行还款义务。\r\n	</p>\r\n	<p> 5、	甲方应主动缴纳由利息所得带来的可能的税费。\r\n	</p>\r\n	<p>6、	如乙方实际还款金额少于本协议约定的本金、利息及违约金的，甲方各出借人同意各自按照其于本协议文首约定的借款比例收取还款。\r\n	</p>\r\n	<p> 7、	甲方应确保其提供信息和资料的真实性，不得提供虚假信息或隐瞒重要事实。\r\n	</p>\r\n	<p style=\"text-align: left;font-weight: 600;\">\r\n		<u>乙方权利和义务</u>\r\n	</p>\r\n	<p> 1、	乙方必须按期足额向甲方偿还本金和利息。\r\n	</p>\r\n	<p> 2、    乙方必须按期足额向丙方支付借款管理费用。\r\n	</p>\r\n	<p>3、	乙方承诺所借款项不用于任何违法用途。\r\n	</p>\r\n	<p>4、	乙方应确保其提供的信息和资料的真实性，不得提供虚假信息或隐瞒重要事实。\r\n	</p>\r\n	<p>5、	乙方有权了解其在丙方的信用评审进度及结果。\r\n	</p>\r\n	<p> 6、	乙方不得将本协议项下的任何权利义务转让给任何其他方。\r\n	</p>\r\n	<p style=\"text-align: left;font-weight: 600;\">\r\n		<u>丙方的权利和义务</u>\r\n	</p>\r\n	<p>1、甲方授权并委托丙方代其收取本协议文首所约定的出借人每月应收本息，代收后按照甲方的要求进行处置，乙方对此表示认可。\r\n	</p>\r\n	<p>2、甲方授权并委托丙方将其支付的出借本金直接划付至乙方账户，乙方对此表示认可。\r\n	</p>\r\n	<p> 3、甲、乙双方一致同意，在有必要时，丙方有权代甲方对乙方进行关于本协议借款的违约提醒及催收工作，包括但不限于：电话通知、上门催收提醒、发律师函、对乙方提起诉讼等。甲方在此确认委托丙方为其进行以上工作，并授权丙方可以将此工作委托给本协议外的其他方进行。乙方对前述委托的提醒、催收事项已明确知晓并应积极配合。\r\n	</p>\r\n	<p>4、丙方有权按月向乙方收取双方约定的借款管理费，并在有必要时对乙方进行违约提醒及催收工作，包括但不限于电话通知、发律师函、对乙方提起诉讼等。丙方有权将此违约提醒及催收工作委托给本协议外的其他方进行。\r\n	</p>\r\n	<p> 5、丙方接受甲乙双方的委托行为所产生的法律后果由相应委托方承担。如因乙方或甲方或其他方（包括但不限于技术问题）造成的延误或错误，丙方不承担任何责任。\r\n	</p>\r\n	<p> 6、丙方应对甲方和乙方的信息及本协议内容保密；如任何一方违约，或因相关权力部门要求（包括但不限于法院、仲裁机构、金融监管机构等），丙方有权披露。\r\n	</p>\r\n	<p>7、丙方根据本协议对乙方进行违约提醒及催收工作时，可在其认为必要时进行上门催收提醒，即丙方派出人员（至少2名）至乙方披露的住所地或经常居住地（联系地址）处催收和进行违约提醒，同时向乙方发送催收通知单，乙方应当签收，乙方不签收的，不影响上门催收提醒的进行。丙方采取上门催收提醒的，乙方应当向丙方支付上门提醒费用，收费标准为每次人民币1000.00元，此外，乙方还应向丙方支付进行上门催收提醒服务的差旅费（包括但不限于交通费、食宿费等）。\r\n	</p>\r\n	<p style=\"text-align: left;font-weight: 600;\">\r\n		<u>丁方的权利和义务</u>\r\n	</p>\r\n	<p>1、出借人和借款人均同意，丁方取得出借人在本协议项下的债权后，可依法向借款人追收借款本金、利息、逾期罚息等，清收费用，坏账风险均由丁方承担。\r\n	</p>\r\n	<p>2、款人逾期本网站对逾期仍未还款的借款人收取逾期罚息作为催收费用、采取多种方式进行催收、将借款人的相关信息对外公开或列入“不良信用记录”或采取法律措施等各项行为，该等服务的法律后果均由借款人自行承担，如债权转让给丁方后，对借款人造成的一切责任与本网站无关，均由借款人自行承担。\r\n	</p>\r\n	<p> 3、若借款人的任何一期还款不足以偿还应还本金、利息和违约金，且出借人为多人时，则出借人同意按照各自出借金额在出借金额总额中的比例收取还款，不足偿还本金时直接由丁方垫付后债权直接转让给丁方再进行追讨。\r\n	</p>\r\n	\r\n	<p style=\"text-align: left;font-weight: 600;\">\r\n		第三条 借款管理费及居间服务费\r\n	</p>\r\n	<p>1、	在本协议中，“借款管理费”和“居间服务费”是指因丙方为乙方提供信用咨询、评估、还款提醒、账户管理、还款特殊情况沟通、本金保障等系列信用相关服务（统称“信用服务”）而由乙方支付给丙方的报酬。\r\n	</p>\r\n	<p>2、	对于丙方向乙方提供的一系列信用服务，乙方同意在借款成功时向丙方支付本协议第一条约定借款本金总额的{function name=\"number_format\" v=$deal.services_fee f=1}%(即人民币<?php echo number_format(floatval($this->_var[\'deal\'][\'services_fee\'])*$this->_var[\'deal\'][\'borrow_amount\']/100,2); ?>元)作为居间服务费，该“居间服务费”由乙方授权并委托丙方在丙方根据本协议规定的“丙方的权利和义务”第2款规定向乙方划付出借本金时从本金中予以扣除，即视为乙方已缴纳。在本协议约定的借款期限内，乙方应每月向丙方支付本协议第一条约定借款本金总额的{function name=\"app_conf\" v=\"MANAGE_FEE\"}% (即人民币{function name=\"number_format\" v=\"$deal.month_manage_money\" f=2}元)，作为借款管理费用，共需支付{$deal.repay_time}期，共计人民币{function name=\"number_format\" v=\"$deal.all_manage_money\" f=2} 元，借款管理费的缴纳时间与本协议第一条约定的还款日一致。</p>\r\n	<p> 本条所称的“借款成功时”系指本协议签署日。</p>\r\n	<p> 3、    如乙方和丙方协商一致调整借款管理费和居间服务费时，无需经过甲方同意。 </p>\r\n	<br/>\r\n	<p style=\"text-align: left;font-weight: 600;\">\r\n		第四条 违约责任\r\n	</p>\r\n	<p> 1、协议各方均应严格履行合同义务，非经各方协商一致或依照本协议约定，任何一方不得解除本协议。\r\n	</p>\r\n	<p>2、任何一方违约，违约方应承担因违约使得其他各方产生的费用和损失，包括但不限于调查、诉讼费、律师费等，应由违约方承担。如违约方为乙方的，甲方有权立即解除本协议，并要求乙方立即偿还未偿还的本金、利息、罚息、违约金。此时，乙方还应向丙方支付所有应付的借款管理费。如本协议提前解除时，乙方在{$SITE_URL}网站的账户里有任何余款的，丙方有权按照本协议第四条第3项的清偿顺序将乙方的余款用于清偿，并要求乙方支付因此产生的相关费用。</p>\r\n	<p>3、乙方的每期还款均应按照如下顺序清偿：</p>\r\n	<p>（1）根据本协议产生的其他全部费用；</p>\r\n	<p>（2）本协议第四条第4款约定的罚息； </p>\r\n	<p>（3）本协议第四条第5款约定的逾期管理费；</p>\r\n	<p>（4）拖欠的利息； </p>\r\n	<p>（5）拖欠的本金； </p>\r\n	<p>（6）拖欠丙方的借款管理费；\r\n	</p>\r\n	<p>（7）正常的利息； </p>\r\n	<p>（8）正常的本金； </p>\r\n	<p>（9）丙方的借款管理费；</p>\r\n	<p> 4、乙方应严格履行还款义务，如乙方逾期还款，则应按照下述条款向甲方支付逾期罚息，自逾期开始之后，逾期本金的正常利息停止计算。 </p>\r\n	<p>罚息总额 = 逾期本息总额×对应罚息利率×逾期天数；</p>\r\n</div>\r\n<div>\r\n	<br/>\r\n	<table border=\"1\" style=\"margin: 0px auto; border-collapse: collapse; border: 1px solid rgb(0, 0, 0); width: 70%; \">\r\n		<tr>\r\n			<td style=\"padding-left:10px\">\r\n				逾期天数\r\n			</td>\r\n			<td style=\"padding-left:10px\">\r\n				1—<?php echo (int)app_conf(\"YZ_IMPSE_DAY\")-1; ?>天\r\n			</td>\r\n			<td style=\"padding-left:10px\">\r\n				{function name=\"app_conf\" v=\"YZ_IMPSE_DAY\"}天及以上\r\n			</td>\r\n		</tr>\r\n		<tr>\r\n			<td style=\"padding-left:10px\">\r\n				罚息利率\r\n			</td>\r\n			<td style=\"padding-left:10px\">\r\n				{$deal.impose_fee_day1}%\r\n			</td>\r\n			<td style=\"padding-left:10px\">\r\n				{$deal.impose_fee_day2}%\r\n			</td>\r\n		</tr>\r\n	</table>\r\n	<br/>\r\n	<p>\r\n		5、乙方应严格履行还款义务，如乙方逾期还款，则应按照下述条款向丙方支付逾期管理费：\r\n	</p>\r\n	<p>\r\n		逾期管理费总额 = 逾期本息总额×对应逾期管理费率×逾期天数；\r\n	</p>\r\n</div>\r\n<br/>\r\n<table border=\"1\" style=\"margin: 0px auto; border-collapse: collapse; border: 1px solid rgb(0, 0, 0); width: 70%; \">\r\n	<tr>\r\n		<td style=\"padding-left:10px\">\r\n			逾期天数\r\n		</td>\r\n		<td style=\"padding-left:10px\">\r\n			1—<?php echo (int)app_conf(\"YZ_IMPSE_DAY\")-1; ?>天\r\n		</td>\r\n		<td style=\"padding-left:10px\">\r\n			{function name=\"app_conf\" v=\"YZ_IMPSE_DAY\"}天及以上\r\n		</td>\r\n	</tr>\r\n	<tr>\r\n		<td style=\"padding-left:10px\">\r\n			逾期管理费费率\r\n		</td>\r\n		<td style=\"padding-left:10px\">\r\n			{$deal.manage_impose_fee_day1}%\r\n		</td>\r\n		<td style=\"padding-left:10px\">\r\n			{$deal.manage_impose_fee_day2}%\r\n		</td>\r\n	</tr>\r\n</table>\r\n<br/>\r\n<div>\r\n	<p>\r\n		6、如果乙方逾期支付任何一期还款超过30天，或乙方在逾期后出现逃避、拒绝沟通或拒绝承认欠款事实等恶意行为，本协议项下的全部借款本息及借款管理费均提前到期，乙方应立即清偿本协议下尚未偿付的全部本金、利息、罚息、借款管理费及根据本协议产生的其他全部费用。\r\n	</p>\r\n	<p>\r\n		7、如果乙方逾期支付任何一期还款超过30天，或乙方在逾期后出现逃避、拒绝沟通或拒绝承认欠款事实等恶意行为，丙方有权将乙方的“逾期记录”记入人民银行公民征信系统，丙方不承担任何法律责任。\r\n	</p>\r\n	<p>\r\n		8、如果乙方逾期支付任何一期还款超过30天，或乙方在逾期后出现逃避、拒绝沟通或拒绝承认欠款事实等恶意行为，丙方有权将乙方违约失信的相关信息及乙方其他信息向媒体、用人单位、公安机关、检查机关、法律机关披露，丙方不承担任何责任。\r\n	</p>\r\n	<p>\r\n		9、在乙方还清全部本金、利息、借款管理费、罚息、逾期管理费之前，罚息及逾期管理费的计算不停止。\r\n	</p>\r\n	<p>\r\n		10、本协议中的所有甲方与乙方之间的借款均是相互独立的，一旦乙方逾期未归还借款本息，甲方中的任何一个出借人均有权单独向乙方追索或者提起诉讼。如乙方逾期支付借款管理费或提供虚假信息的，丙方亦可单独向乙方追索或者提起诉讼。\r\n	</p>\r\n	<br/>\r\n	<p style=\"text-align: left;font-weight: 600;\">\r\n		第五条 提前还款\r\n	</p>\r\n	<p>\r\n		1、乙方可在借款期间任何时候提前偿还剩余借款。\r\n	</p>\r\n	<p>\r\n		2、提前偿还全部剩余借款\r\n	</p>\r\n	<p style=\"padding-left: 15px\">\r\n		1）乙方提前清偿全部剩余借款时，应向甲方支付当期应还本息，剩余本金及提前还款补偿（补偿金额为剩余本金的{function name=\"app_conf\" v=\"COMPENSATE_FEE\"}%）。\r\n	</p>\r\n	<p style=\"padding-left: 15px\">\r\n		2）乙方提前清偿全部剩余借款时，应向丙方支付当期借款管理费，乙方无需支付剩余还款期的借款管理费。\r\n	</p>\r\n	<p>\r\n		3、提前偿还部分借款\r\n	</p>\r\n	<p style=\"padding-left: 15px\">\r\n		1）乙方提前偿还部分借款，仍应向甲方支付全部借款利息。\r\n	</p>\r\n	<p style=\"padding-left: 15px\">\r\n		2）乙方提前偿还部分借款，仍应向丙方支付全部应付的借款管理费。\r\n	</p>\r\n	<p>\r\n		4、任何形式的提前还款不影响丙方向乙方收取在本协议第三条中说明的居间服务费。\r\n	</p>\r\n	<br/>\r\n	<p style=\"text-align: left;font-weight: 600;\">\r\n		第六条	法律及争议解决\r\n	</p>\r\n	 <p>\r\n		 本协议的签订、履行、终止、解释均适用中华人民共和国法律，并由丙方所在地{function name=\"app_conf\" v=\'COMPANY_REG_ADDRESS\'}人民法院管辖。\r\n	</p>\r\n	<br/>\r\n	<p style=\"text-align: left;font-weight: 600;\">\r\n		第七条	附则\r\n	</p>\r\n	<p>\r\n		1、本协议采用电子文本形式制成，并永久保存在丙方为此设立的专用服务器上备查，各方均认可该形式的协议效力。\r\n	</p>\r\n	<p>\r\n		2、本协议自文本最终生成之日生效。\r\n	</p>\r\n	<p>\r\n		3、本协议签订之日起至借款全部清偿之日止，乙方或甲方有义务在下列信息变更三日内提供更新后的信息给丙方：本人、本人的家庭联系人及紧急联系人、工作单位、居住地址、住所电话、手机号码、电子邮箱、银行账户的变更。若因任何一方不及时提供上述变更信息而带来的损失或额外费用应由该方承担。\r\n	</p>\r\n	<p>\r\n		4、如果本协议中的任何一条或多条违反适用的法律法规，则该条将被视为无效，但该无效条款并不影响本协议其他条款的效力。\r\n	</p>\r\n        <br>\r\n	{if $deal.attachment}\r\n	<p style=\"text-align: left;font-weight: 600;\">\r\n		第八条	附件\r\n	</p>\r\n	<p>\r\n		{$deal.attachment}\r\n	</p>\r\n	{/if}\r\n</div>\r\n<br/>\r\n<div style=\"width: 98%;text-align: right;\">\r\n	<p>\r\n		{function name=\"to_date\" v=\"$deal.repay_start_time\" f=\"Y年m月d日\"}\r\n	</p>\r\n</div>', '1', '0');
INSERT INTO `%DB_PREFIX%contract` VALUES ('5', '到期本息合同范本【普通】', '<div style=\"width: 98%;text-align: right;\">编号：<span>{$deal.deal_sn}</span></div>\r\n<h2 align=\"center\">借款协议</h2>\r\n<br/>\r\n<div style=\"text-align: left;font-weight: 600;\">甲方（出借人）：</div>\r\n<table border=\"1\" style=\"margin: 0px auto; border-collapse: collapse; border: 1px solid rgb(0, 0, 0); width: 70%; \">\r\n    <tr>\r\n	<td style=\"text-align:center;\"> {$SITE_TITLE}用户名</td>\r\n	<td style=\"text-align:center;\"> 借出金额</td>\r\n	<td style=\"text-align:center;\">借款期限</td>\r\n	<td style=\"text-align:center;\"> 到期还本息</td>\r\n    </tr>\r\n    {foreach from=\"$loan_list\" item=\"loan\"}\r\n    <tr>\r\n	<td style=\"text-align:center;\">{$loan.user_name}</td>\r\n	<td style=\"text-align:right;padding-right:10px\">{$CURRENCY_UNIT}{function name=\"number_format\" v=$loan.money f=2}</td>\r\n	<td style=\"text-align:center;\">{$deal.repay_time}{if $deal.repay_time_type eq 0}天{else}个月{/if}</td>\r\n	<td style=\"text-align:right;padding-right:10px\">\r\n	{$CURRENCY_UNIT}{function name=\"number_format\" v=$loan.get_repay_money f=2}\r\n	</td>\r\n    </tr>\r\n    {/foreach}\r\n    <tr>\r\n	<td style=\"text-align:center;\">总计</td>\r\n	<td style=\"text-align:right;padding-right:10px\">{$CURRENCY_UNIT}{function name=\"number_format\" v=$deal.load_money f=2}</td>\r\n	<td> </td>\r\n	<td> </td>\r\n    </tr>\r\n</table>\r\n<p>注：因计算中存在四舍五入，最后一期应收本息与之前略有不同</p>\r\n<br/>\r\n<div>\r\n	<p style=\"text-align: left;font-weight: 600;\">乙方（借款人）：</p>\r\n	<p style=\"text-align: left;font-weight: 600;\">{$SITE_TITLE}用户名：<span>{$user_info.user_name}</span></p>\r\n	<br/>\r\n	<p style=\"text-align: left;font-weight: 600;\"> 丙方（见证人）：{function name=\"app_conf\" v=\"COMPANY\"} </p>\r\n	<p style=\"text-align: left;font-weight: 600;\">联系方式：{function name=\"app_conf\" v=\"COMPANY_ADDRESS\"}</p>\r\n</div>\r\n<br/>\r\n<p><strong>鉴于：</strong></p>\r\n<p>1、丙方是一家在{function name=\"app_conf\" v=\"COMPANY_REG_ADDRESS\"}合法成立并有效存续的有限责任公司，拥有{$SITE_URL} 网站（以下简称“该网站”）的经营权，提供信用咨询，为交易提供信息服务；</p>\r\n<p>2、乙方已在该网站注册，并承诺其提供给丙方的信息是完全真实的；</p>\r\n<p>3、甲方承诺对本协议涉及的借款具有完全的支配能力，是其自有闲散资金，为其合法所得；并承诺其提供给丙方的信息是完全真实的；</p>\r\n<p>4、乙方有借款需求，甲方亦同意借款，双方有意成立借贷关系；</p>\r\n<br/>\r\n<p style=\"text-align: left;font-weight: 600;\">各方经协商一致，于<span> {function name=\"to_date\" v=\"$deal.repay_start_time\" f=\"Y年m月d日\"}</span>签订如下协议，共同遵照履行：</p>\r\n<br/>\r\n<p style=\"text-align: left;font-weight: 600;\"> 第一条 借款基本信息</p>\r\n<br/>\r\n<table border=\"1\" style=\"margin: 0px auto; border-collapse: collapse; border: 1px solid rgb(0, 0, 0); width: 70%; \">\r\n	<tr>\r\n		<td width=\"20%\" style=\"padding-left:10px\"> 借款详细用途</td>\r\n		<td style=\"padding-left:10px\"> {$deal.type_info.name}</td>\r\n	</tr>\r\n	<tr>\r\n		<td style=\"padding-left:10px\">借款本金数额</td>\r\n		<td style=\"padding-left:10px\">\r\n			{$CURRENCY_UNIT}{function name=\"number_format\" v=$deal.borrow_amount f=2}（各出借人借款本金数额详见本协议文首表格）\r\n		</td>\r\n	</tr>\r\n	<tr>\r\n		<td style=\"padding-left:10px\">到期还本息\r\n		</td>\r\n		<td style=\"padding-left:10px\">\r\n			{$CURRENCY_UNIT}{function name=\"number_format\" v=$deal.last_month_repay_money f=2}\r\n		</td>\r\n	</tr>\r\n	\r\n	<tr>\r\n		<td style=\"padding-left:10px\">\r\n			还款日\r\n		</td>\r\n		<td style=\"padding-left:10px\">\r\n			 自{function name=\"to_date\" v=\"$deal.repay_start_time\" f=\"Y年m月d日\"}起，{function name=\"to_date\" v=\"$deal.end_repay_time\" f=\"Y年m月d日\"}（24:00前，节假日不顺延）\r\n		</td>\r\n	</tr>\r\n	<tr>\r\n		<td style=\"padding-left:10px\"> 借款期限\r\n		</td>\r\n		<td style=\"padding-left:10px\">\r\n			{$deal.repay_time}{if $deal.repay_time_type eq 0}天{else}个月{/if}，{function name=\"to_date\" v=\"$deal.repay_start_time\" f=\"Y年m月d日\"}起，至  {function name=\"to_date\" v=\"$deal.end_repay_time\" f=\"Y年m月d日\"}日止\r\n		</td>\r\n	</tr>\r\n</table>\r\n<br/>\r\n<div>\r\n	<p style=\"text-align: left;font-weight: 600;\">\r\n		第二条 各方权利和义务\r\n	</p>\r\n	<p style=\"text-align: left;font-weight: 600;\">\r\n		<u>甲方的权利和义务</u>\r\n	</p>\r\n	<p> 1、	甲方应按合同约定的借款期限起始日期将足额的借款本金支付给乙方。\r\n	</p>\r\n	<p> 2、	甲方享有其所出借款项所带来的利息收益。\r\n	</p>\r\n	<p>3、	如乙方违约，甲方有权要求丙方提供其已获得的乙方信息，丙方应当提供。\r\n	</p>\r\n	<p>4、	无须通知乙方，甲方可以根据自己的意愿进行本协议下其对乙方债权的转让。在甲方的债权转让后，乙方需对债权受让人继续履行本协议下其对甲方的还款义务，不得以未接到债权转让通知为由拒绝履行还款义务。\r\n	</p>\r\n	<p> 5、	甲方应主动缴纳由利息所得带来的可能的税费。\r\n	</p>\r\n	<p>6、	如乙方实际还款金额少于本协议约定的本金、利息及违约金的，甲方各出借人同意各自按照其于本协议文首约定的借款比例收取还款。\r\n	</p>\r\n	<p> 7、	甲方应确保其提供信息和资料的真实性，不得提供虚假信息或隐瞒重要事实。\r\n	</p>\r\n	<p style=\"text-align: left;font-weight: 600;\">\r\n		<u>乙方权利和义务</u>\r\n	</p>\r\n	<p> 1、	乙方必须按期足额向甲方偿还本金和利息。\r\n	</p>\r\n	<p> 2、    乙方必须按期足额向丙方支付借款管理费用。\r\n	</p>\r\n	<p>3、	乙方承诺所借款项不用于任何违法用途。\r\n	</p>\r\n	<p>4、	乙方应确保其提供的信息和资料的真实性，不得提供虚假信息或隐瞒重要事实。\r\n	</p>\r\n	<p>5、	乙方有权了解其在丙方的信用评审进度及结果。\r\n	</p>\r\n	<p> 6、	乙方不得将本协议项下的任何权利义务转让给任何其他方。\r\n	</p>\r\n	<p style=\"text-align: left;font-weight: 600;\">\r\n		<u>丙方的权利和义务</u>\r\n	</p>\r\n	<p>1、甲方授权并委托丙方代其收取本协议文首所约定的出借人每月应收本息，代收后按照甲方的要求进行处置，乙方对此表示认可。\r\n	</p>\r\n	<p>2、甲方授权并委托丙方将其支付的出借本金直接划付至乙方账户，乙方对此表示认可。\r\n	</p>\r\n	<p> 3、甲、乙双方一致同意，在有必要时，丙方有权代甲方对乙方进行关于本协议借款的违约提醒及催收工作，包括但不限于：电话通知、上门催收提醒、发律师函、对乙方提起诉讼等。甲方在此确认委托丙方为其进行以上工作，并授权丙方可以将此工作委托给本协议外的其他方进行。乙方对前述委托的提醒、催收事项已明确知晓并应积极配合。\r\n	</p>\r\n	<p>4、丙方有权按月向乙方收取双方约定的借款管理费，并在有必要时对乙方进行违约提醒及催收工作，包括但不限于电话通知、发律师函、对乙方提起诉讼等。丙方有权将此违约提醒及催收工作委托给本协议外的其他方进行。\r\n	</p>\r\n	<p> 5、丙方接受甲乙双方的委托行为所产生的法律后果由相应委托方承担。如因乙方或甲方或其他方（包括但不限于技术问题）造成的延误或错误，丙方不承担任何责任。\r\n	</p>\r\n	<p> 6、丙方应对甲方和乙方的信息及本协议内容保密；如任何一方违约，或因相关权力部门要求（包括但不限于法院、仲裁机构、金融监管机构等），丙方有权披露。\r\n	</p>\r\n	<p>7、丙方根据本协议对乙方进行违约提醒及催收工作时，可在其认为必要时进行上门催收提醒，即丙方派出人员（至少2名）至乙方披露的住所地或经常居住地（联系地址）处催收和进行违约提醒，同时向乙方发送催收通知单，乙方应当签收，乙方不签收的，不影响上门催收提醒的进行。丙方采取上门催收提醒的，乙方应当向丙方支付上门提醒费用，收费标准为每次人民币1000.00元，此外，乙方还应向丙方支付进行上门催收提醒服务的差旅费（包括但不限于交通费、食宿费等）。\r\n	</p>\r\n	<br/>\r\n	<p style=\"text-align: left;font-weight: 600;\">\r\n		第三条 借款管理费及居间服务费\r\n	</p>\r\n	<p>1、	在本协议中，“借款管理费”和“居间服务费”是指因丙方为乙方提供信用咨询、评估、还款提醒、账户管理、还款特殊情况沟通、本金保障等系列信用相关服务（统称“信用服务”）而由乙方支付给丙方的报酬。\r\n	</p>\r\n	<p>2、	对于丙方向乙方提供的一系列信用服务，乙方同意在借款成功时向丙方支付本协议第一条约定借款本金总额的{function name=\"number_format\" v=$deal.services_fee f=1}%(即人民币<?php echo number_format(floatval($this->_var[\'deal\'][\'services_fee\'])*$this->_var[\'deal\'][\'borrow_amount\']/100,2); ?>元)作为居间服务费，该“居间服务费”由乙方授权并委托丙方在丙方根据本协议规定的“丙方的权利和义务”第2款规定向乙方划付出借本金时从本金中予以扣除，即视为乙方已缴纳。在本协议约定的借款期限内，乙方应每月向丙方支付本协议第一条约定借款本金总额的{function name=\"app_conf\" v=\"MANAGE_FEE\"}% (即人民币{function name=\"number_format\" v=\"$deal.month_manage_money\" f=2}元)，作为借款管理费用，共需支付{if $deal.repay_time_type eq 0}1{else}{$deal.repay_time}{/if}期，共计人民币{function name=\"number_format\" v=\"$deal.all_manage_money\" f=2} 元，借款管理费的缴纳时间与本协议第一条约定的还款日一致。</p>\r\n	<p> 本条所称的“借款成功时”系指本协议签署日。</p>\r\n	<p> 3、    如乙方和丙方协商一致调整借款管理费和居间服务费时，无需经过甲方同意。 </p>\r\n	<br/>\r\n	<p style=\"text-align: left;font-weight: 600;\">\r\n		第四条 违约责任\r\n	</p>\r\n	<p> 1、协议各方均应严格履行合同义务，非经各方协商一致或依照本协议约定，任何一方不得解除本协议。\r\n	</p>\r\n	<p>2、任何一方违约，违约方应承担因违约使得其他各方产生的费用和损失，包括但不限于调查、诉讼费、律师费等，应由违约方承担。如违约方为乙方的，甲方有权立即解除本协议，并要求乙方立即偿还未偿还的本金、利息、罚息、违约金。此时，乙方还应向丙方支付所有应付的借款管理费。如本协议提前解除时，乙方在{$SITE_URL}网站的账户里有任何余款的，丙方有权按照本协议第四条第3项的清偿顺序将乙方的余款用于清偿，并要求乙方支付因此产生的相关费用。</p>\r\n	<p>3、乙方的每期还款均应按照如下顺序清偿：</p>\r\n	<p>（1）根据本协议产生的其他全部费用；</p>\r\n	<p>（2）本协议第四条第4款约定的罚息； </p>\r\n	<p>（3）本协议第四条第5款约定的逾期管理费；</p>\r\n	<p>（4）拖欠的利息； </p>\r\n	<p>（5）拖欠的本金； </p>\r\n	<p>（6）拖欠丙方的借款管理费；\r\n	</p>\r\n	<p>（7）正常的利息； </p>\r\n	<p>（8）正常的本金； </p>\r\n	<p>（9）丙方的借款管理费；</p>\r\n	<p> 4、乙方应严格履行还款义务，如乙方逾期还款，则应按照下述条款向甲方支付逾期罚息，自逾期开始之后，逾期本金的正常利息停止计算。 </p>\r\n	<p>罚息总额 = 逾期本息总额×对应罚息利率×逾期天数；</p>\r\n</div>\r\n<div>\r\n	<br/>\r\n	<table border=\"1\" style=\"margin: 0px auto; border-collapse: collapse; border: 1px solid rgb(0, 0, 0); width: 70%; \">\r\n		<tr>\r\n			<td style=\"padding-left:10px\">\r\n				逾期天数\r\n			</td>\r\n			<td style=\"padding-left:10px\">\r\n				1—<?php echo (int)app_conf(\"YZ_IMPSE_DAY\")-1; ?>天\r\n			</td>\r\n			<td style=\"padding-left:10px\">\r\n				{function name=\"app_conf\" v=\"YZ_IMPSE_DAY\"}天及以上\r\n			</td>\r\n		</tr>\r\n		<tr>\r\n			<td style=\"padding-left:10px\">\r\n				罚息利率\r\n			</td>\r\n			<td style=\"padding-left:10px\">\r\n				{$deal.impose_fee_day1}%\r\n			</td>\r\n			<td style=\"padding-left:10px\">\r\n				{$deal.impose_fee_day2}%\r\n			</td>\r\n		</tr>\r\n	</table>\r\n	<br/>\r\n	<p>\r\n		5、乙方应严格履行还款义务，如乙方逾期还款，则应按照下述条款向丙方支付逾期管理费：\r\n	</p>\r\n	<p>\r\n		逾期管理费总额 = 逾期本息总额×对应逾期管理费率×逾期天数；\r\n	</p>\r\n</div>\r\n<br/>\r\n<table border=\"1\" style=\"margin: 0px auto; border-collapse: collapse; border: 1px solid rgb(0, 0, 0); width: 70%; \">\r\n	<tr>\r\n		<td style=\"padding-left:10px\">\r\n			逾期天数\r\n		</td>\r\n		<td style=\"padding-left:10px\">\r\n			1—<?php echo (int)app_conf(\"YZ_IMPSE_DAY\")-1; ?>天\r\n		</td>\r\n		<td style=\"padding-left:10px\">\r\n			{function name=\"app_conf\" v=\"YZ_IMPSE_DAY\"}天及以上\r\n		</td>\r\n	</tr>\r\n	<tr>\r\n		<td style=\"padding-left:10px\">\r\n			逾期管理费费率\r\n		</td>\r\n		<td style=\"padding-left:10px\">\r\n			{$deal.manage_impose_fee_day1}%\r\n		</td>\r\n		<td style=\"padding-left:10px\">\r\n			{$deal.manage_impose_fee_day2}%\r\n		</td>\r\n	</tr>\r\n</table>\r\n<br/>\r\n<div>\r\n	<p>\r\n		6、如果乙方逾期支付任何一期还款超过30天，或乙方在逾期后出现逃避、拒绝沟通或拒绝承认欠款事实等恶意行为，本协议项下的全部借款本息及借款管理费均提前到期，乙方应立即清偿本协议下尚未偿付的全部本金、利息、罚息、借款管理费及根据本协议产生的其他全部费用。\r\n	</p>\r\n	<p>\r\n		7、如果乙方逾期支付任何一期还款超过30天，或乙方在逾期后出现逃避、拒绝沟通或拒绝承认欠款事实等恶意行为，丙方有权将乙方的“逾期记录”记入人民银行公民征信系统，丙方不承担任何法律责任。\r\n	</p>\r\n	<p>\r\n		8、如果乙方逾期支付任何一期还款超过30天，或乙方在逾期后出现逃避、拒绝沟通或拒绝承认欠款事实等恶意行为，丙方有权将乙方违约失信的相关信息及乙方其他信息向媒体、用人单位、公安机关、检查机关、法律机关披露，丙方不承担任何责任。\r\n	</p>\r\n	<p>\r\n		9、在乙方还清全部本金、利息、借款管理费、罚息、逾期管理费之前，罚息及逾期管理费的计算不停止。\r\n	</p>\r\n	<p>\r\n		10、本协议中的所有甲方与乙方之间的借款均是相互独立的，一旦乙方逾期未归还借款本息，甲方中的任何一个出借人均有权单独向乙方追索或者提起诉讼。如乙方逾期支付借款管理费或提供虚假信息的，丙方亦可单独向乙方追索或者提起诉讼。\r\n	</p>\r\n	<br/>\r\n	<p style=\"text-align: left;font-weight: 600;\">\r\n		第五条 提前还款\r\n	</p>\r\n	<p>\r\n		1、乙方可在借款期间任何时候提前偿还剩余借款。\r\n	</p>\r\n	<p>\r\n		2、提前偿还全部剩余借款\r\n	</p>\r\n	<p style=\"padding-left: 15px\">\r\n		1）乙方提前清偿全部剩余借款时，应向甲方支付当期应还本息，剩余本金及提前还款补偿（补偿金额为剩余本金的{function name=\"app_conf\" v=\"COMPENSATE_FEE\"}%）。\r\n	</p>\r\n	<p style=\"padding-left: 15px\">\r\n		2）乙方提前清偿全部剩余借款时，应向丙方支付当期借款管理费，乙方无需支付剩余还款期的借款管理费。\r\n	</p>\r\n	<p>\r\n		3、提前偿还部分借款\r\n	</p>\r\n	<p style=\"padding-left: 15px\">\r\n		1）乙方提前偿还部分借款，仍应向甲方支付全部借款利息。\r\n	</p>\r\n	<p style=\"padding-left: 15px\">\r\n		2）乙方提前偿还部分借款，仍应向丙方支付全部应付的借款管理费。\r\n	</p>\r\n	<p>\r\n		4、任何形式的提前还款不影响丙方向乙方收取在本协议第三条中说明的居间服务费。\r\n	</p>\r\n	<br/>\r\n	<p style=\"text-align: left;font-weight: 600;\">\r\n		第六条	法律及争议解决\r\n	</p>\r\n	 <p>\r\n		 本协议的签订、履行、终止、解释均适用中华人民共和国法律，并由丙方所在地{function name=\"app_conf\" v=\'COMPANY_REG_ADDRESS\'}人民法院管辖。\r\n	</p>\r\n	<br/>\r\n	<p style=\"text-align: left;font-weight: 600;\">\r\n		第七条	附则\r\n	</p>\r\n	<p>\r\n		1、本协议采用电子文本形式制成，并永久保存在丙方为此设立的专用服务器上备查，各方均认可该形式的协议效力。\r\n	</p>\r\n	<p>\r\n		2、本协议自文本最终生成之日生效。\r\n	</p>\r\n	<p>\r\n		3、本协议签订之日起至借款全部清偿之日止，乙方或甲方有义务在下列信息变更三日内提供更新后的信息给丙方：本人、本人的家庭联系人及紧急联系人、工作单位、居住地址、住所电话、手机号码、电子邮箱、银行账户的变更。若因任何一方不及时提供上述变更信息而带来的损失或额外费用应由该方承担。\r\n	</p>\r\n	<p>\r\n		4、如果本协议中的任何一条或多条违反适用的法律法规，则该条将被视为无效，但该无效条款并不影响本协议其他条款的效力。\r\n	</p>\r\n        <br>\r\n	{if $deal.attachment}\r\n	<p style=\"text-align: left;font-weight: 600;\">\r\n		第八条	附件\r\n	</p>\r\n	<p>\r\n		{$deal.attachment}\r\n	</p>\r\n	{/if}\r\n</div>\r\n<br/>\r\n<div style=\"width: 98%;text-align: right;\">\r\n	<p>\r\n		{function name=\"to_date\" v=\"$deal.repay_start_time\" f=\"Y年m月d日\"}\r\n	</p>\r\n</div>', '1', '0');
INSERT INTO `%DB_PREFIX%contract` VALUES ('6', '到期本息合同范本【担保】', '<div style=\"width: 98%;text-align: right;\">编号：<span>{$deal.deal_sn}</span></div>\r\n<h2 align=\"center\">借款协议</h2>\r\n<br/>\r\n<div style=\"text-align: left;font-weight: 600;\">甲方（出借人）：</div>\r\n<table border=\"1\" style=\"margin: 0px auto; border-collapse: collapse; border: 1px solid rgb(0, 0, 0); width: 70%; \">\r\n    <tr>\r\n	<td style=\"text-align:center;\"> {$SITE_TITLE}用户名</td>\r\n	<td style=\"text-align:center;\"> 借出金额</td>\r\n	<td style=\"text-align:center;\">借款期限</td>\r\n	<td style=\"text-align:center;\"> 到期还本息</td>\r\n    </tr>\r\n    {foreach from=\"$loan_list\" item=\"loan\"}\r\n    <tr>\r\n	<td style=\"text-align:center;\">{$loan.user_name}</td>\r\n	<td style=\"text-align:right;padding-right:10px\">{$CURRENCY_UNIT}{function name=\"number_format\" v=$loan.money f=2}</td>\r\n	<td style=\"text-align:center;\">{$deal.repay_time}{if $deal.repay_time_type eq 0}天{else}个月{/if}</td>\r\n	<td style=\"text-align:right;padding-right:10px\">\r\n	{$CURRENCY_UNIT}{function name=\"number_format\" v=$loan.get_repay_money f=2}\r\n	</td>\r\n    </tr>\r\n    {/foreach}\r\n    <tr>\r\n	<td style=\"text-align:center;\">总计</td>\r\n	<td style=\"text-align:right;padding-right:10px\">{$CURRENCY_UNIT}{function name=\"number_format\" v=$deal.load_money f=2}</td>\r\n	<td> </td>\r\n	<td> </td>\r\n    </tr>\r\n</table>\r\n<p>注：因计算中存在四舍五入，最后一期应收本息与之前略有不同</p>\r\n<br/>\r\n<div>\r\n	<p style=\"text-align: left;font-weight: 600;\">乙方（借款人）：</p>\r\n	<p style=\"text-align: left;font-weight: 600;\">{$SITE_TITLE}用户名：<span>{$user_info.user_name}</span></p>\r\n	<br/>\r\n	<p style=\"text-align: left;font-weight: 600;\"> 丙方（见证人）：{function name=\"app_conf\" v=\"COMPANY\"} </p>\r\n	<p style=\"text-align: left;font-weight: 600;\">联系方式：{function name=\"app_conf\" v=\"COMPANY_ADDRESS\"}</p>\r\n	<br/>\r\n	<p style=\"text-align: left;font-weight: 600;\"> 丁方（担保方）：{$deal.agency_info.name} </p>\r\n	<p style=\"text-align: left;font-weight: 600;\">联系方式：{$deal.agency_info.address}</p>\r\n</div>\r\n<br/>\r\n<p><strong>鉴于：</strong></p>\r\n<p>1、丙方是一家在{function name=\"app_conf\" v=\"COMPANY_REG_ADDRESS\"}合法成立并有效存续的有限责任公司，拥有{$SITE_URL} 网站（以下简称“该网站”）的经营权，提供信用咨询，为交易提供信息服务；</p>\r\n<p>2、乙方已在该网站注册，并承诺其提供给丙方的信息是完全真实的；</p>\r\n<p>3、甲方承诺对本协议涉及的借款具有完全的支配能力，是其自有闲散资金，为其合法所得；并承诺其提供给丙方的信息是完全真实的；</p>\r\n<p>4、乙方有借款需求，甲方亦同意借款，双方有意成立借贷关系；</p>\r\n<p>5、丁方愿意作为甲方借款提供保障。当乙方逾期3天仍未清偿借款本息，则甲方本协议项下的债权（逾期本息）自动转让给丁方，丁方将在第4天垫付该标的的未清偿借款本息给甲方；</p>\r\n<br/>\r\n<p style=\"text-align: left;font-weight: 600;\">各方经协商一致，于<span> {function name=\"to_date\" v=\"$deal.repay_start_time\" f=\"Y年m月d日\"}</span>签订如下协议，共同遵照履行：</p>\r\n<br/>\r\n<p style=\"text-align: left;font-weight: 600;\"> 第一条 借款基本信息</p>\r\n<br/>\r\n<table border=\"1\" style=\"margin: 0px auto; border-collapse: collapse; border: 1px solid rgb(0, 0, 0); width: 70%; \">\r\n	<tr>\r\n		<td width=\"20%\" style=\"padding-left:10px\"> 借款详细用途</td>\r\n		<td style=\"padding-left:10px\"> {$deal.type_info.name}</td>\r\n	</tr>\r\n	<tr>\r\n		<td style=\"padding-left:10px\">借款本金数额</td>\r\n		<td style=\"padding-left:10px\">\r\n			{$CURRENCY_UNIT}{function name=\"number_format\" v=$deal.borrow_amount f=2}（各出借人借款本金数额详见本协议文首表格）\r\n		</td>\r\n	</tr>\r\n	<tr>\r\n		<td style=\"padding-left:10px\">到期还本息\r\n		</td>\r\n		<td style=\"padding-left:10px\">\r\n			{$CURRENCY_UNIT}{function name=\"number_format\" v=$deal.last_month_repay_money f=2}\r\n		</td>\r\n	</tr>\r\n	\r\n	<tr>\r\n		<td style=\"padding-left:10px\">\r\n			还款日\r\n		</td>\r\n		<td style=\"padding-left:10px\">\r\n			 自{function name=\"to_date\" v=\"$deal.repay_start_time\" f=\"Y年m月d日\"}起，{function name=\"to_date\" v=\"$deal.end_repay_time\" f=\"Y年m月d日\"}（24:00前，节假日不顺延）\r\n		</td>\r\n	</tr>\r\n	<tr>\r\n		<td style=\"padding-left:10px\"> 借款期限\r\n		</td>\r\n		<td style=\"padding-left:10px\">\r\n			{$deal.repay_time}{if $deal.repay_time_type eq 0}天{else}个月{/if}，{function name=\"to_date\" v=\"$deal.repay_start_time\" f=\"Y年m月d日\"}起，至  {function name=\"to_date\" v=\"$deal.end_repay_time\" f=\"Y年m月d日\"}日止\r\n		</td>\r\n	</tr>\r\n</table>\r\n<br/>\r\n<div>\r\n	<p style=\"text-align: left;font-weight: 600;\">\r\n		第二条 各方权利和义务\r\n	</p>\r\n	<p style=\"text-align: left;font-weight: 600;\">\r\n		<u>甲方的权利和义务</u>\r\n	</p>\r\n	<p> 1、	甲方应按合同约定的借款期限起始日期将足额的借款本金支付给乙方。\r\n	</p>\r\n	<p> 2、	甲方享有其所出借款项所带来的利息收益。\r\n	</p>\r\n	<p>3、	如乙方违约，甲方有权要求丙方提供其已获得的乙方信息，丙方应当提供。\r\n	</p>\r\n	<p>4、	无须通知乙方，甲方可以根据自己的意愿进行本协议下其对乙方债权的转让。在甲方的债权转让后，乙方需对债权受让人继续履行本协议下其对甲方的还款义务，不得以未接到债权转让通知为由拒绝履行还款义务。\r\n	</p>\r\n	<p> 5、	甲方应主动缴纳由利息所得带来的可能的税费。\r\n	</p>\r\n	<p>6、	如乙方实际还款金额少于本协议约定的本金、利息及违约金的，甲方各出借人同意各自按照其于本协议文首约定的借款比例收取还款。\r\n	</p>\r\n	<p> 7、	甲方应确保其提供信息和资料的真实性，不得提供虚假信息或隐瞒重要事实。\r\n	</p>\r\n	<p style=\"text-align: left;font-weight: 600;\">\r\n		<u>乙方权利和义务</u>\r\n	</p>\r\n	<p> 1、	乙方必须按期足额向甲方偿还本金和利息。\r\n	</p>\r\n	<p> 2、    乙方必须按期足额向丙方支付借款管理费用。\r\n	</p>\r\n	<p>3、	乙方承诺所借款项不用于任何违法用途。\r\n	</p>\r\n	<p>4、	乙方应确保其提供的信息和资料的真实性，不得提供虚假信息或隐瞒重要事实。\r\n	</p>\r\n	<p>5、	乙方有权了解其在丙方的信用评审进度及结果。\r\n	</p>\r\n	<p> 6、	乙方不得将本协议项下的任何权利义务转让给任何其他方。\r\n	</p>\r\n	<p style=\"text-align: left;font-weight: 600;\">\r\n		<u>丙方的权利和义务</u>\r\n	</p>\r\n	<p>1、甲方授权并委托丙方代其收取本协议文首所约定的出借人每月应收本息，代收后按照甲方的要求进行处置，乙方对此表示认可。\r\n	</p>\r\n	<p>2、甲方授权并委托丙方将其支付的出借本金直接划付至乙方账户，乙方对此表示认可。\r\n	</p>\r\n	<p> 3、甲、乙双方一致同意，在有必要时，丙方有权代甲方对乙方进行关于本协议借款的违约提醒及催收工作，包括但不限于：电话通知、上门催收提醒、发律师函、对乙方提起诉讼等。甲方在此确认委托丙方为其进行以上工作，并授权丙方可以将此工作委托给本协议外的其他方进行。乙方对前述委托的提醒、催收事项已明确知晓并应积极配合。\r\n	</p>\r\n	<p>4、丙方有权按月向乙方收取双方约定的借款管理费，并在有必要时对乙方进行违约提醒及催收工作，包括但不限于电话通知、发律师函、对乙方提起诉讼等。丙方有权将此违约提醒及催收工作委托给本协议外的其他方进行。\r\n	</p>\r\n	<p> 5、丙方接受甲乙双方的委托行为所产生的法律后果由相应委托方承担。如因乙方或甲方或其他方（包括但不限于技术问题）造成的延误或错误，丙方不承担任何责任。\r\n	</p>\r\n	<p> 6、丙方应对甲方和乙方的信息及本协议内容保密；如任何一方违约，或因相关权力部门要求（包括但不限于法院、仲裁机构、金融监管机构等），丙方有权披露。\r\n	</p>\r\n	<p>7、丙方根据本协议对乙方进行违约提醒及催收工作时，可在其认为必要时进行上门催收提醒，即丙方派出人员（至少2名）至乙方披露的住所地或经常居住地（联系地址）处催收和进行违约提醒，同时向乙方发送催收通知单，乙方应当签收，乙方不签收的，不影响上门催收提醒的进行。丙方采取上门催收提醒的，乙方应当向丙方支付上门提醒费用，收费标准为每次人民币1000.00元，此外，乙方还应向丙方支付进行上门催收提醒服务的差旅费（包括但不限于交通费、食宿费等）。\r\n	</p>\r\n	<p style=\"text-align: left;font-weight: 600;\">\r\n		<u>丁方的权利和义务</u>\r\n	</p>\r\n	<p>1、出借人和借款人均同意，丁方取得出借人在本协议项下的债权后，可依法向借款人追收借款本金、利息、逾期罚息等，清收费用，坏账风险均由丁方承担。\r\n	</p>\r\n	<p>2、款人逾期本网站对逾期仍未还款的借款人收取逾期罚息作为催收费用、采取多种方式进行催收、将借款人的相关信息对外公开或列入“不良信用记录”或采取法律措施等各项行为，该等服务的法律后果均由借款人自行承担，如债权转让给丁方后，对借款人造成的一切责任与本网站无关，均由借款人自行承担。\r\n	</p>\r\n	<p> 3、若借款人的任何一期还款不足以偿还应还本金、利息和违约金，且出借人为多人时，则出借人同意按照各自出借金额在出借金额总额中的比例收取还款，不足偿还本金时直接由丁方垫付后债权直接转让给丁方再进行追讨。\r\n	</p>\r\n	\r\n	<p style=\"text-align: left;font-weight: 600;\">\r\n		第三条 借款管理费及居间服务费\r\n	</p>\r\n	<p>1、	在本协议中，“借款管理费”和“居间服务费”是指因丙方为乙方提供信用咨询、评估、还款提醒、账户管理、还款特殊情况沟通、本金保障等系列信用相关服务（统称“信用服务”）而由乙方支付给丙方的报酬。\r\n	</p>\r\n	<p>2、	对于丙方向乙方提供的一系列信用服务，乙方同意在借款成功时向丙方支付本协议第一条约定借款本金总额的{function name=\"number_format\" v=$deal.services_fee f=1}%(即人民币<?php echo number_format(floatval($this->_var[\'deal\'][\'services_fee\'])*$this->_var[\'deal\'][\'borrow_amount\']/100,2); ?>元)作为居间服务费，该“居间服务费”由乙方授权并委托丙方在丙方根据本协议规定的“丙方的权利和义务”第2款规定向乙方划付出借本金时从本金中予以扣除，即视为乙方已缴纳。在本协议约定的借款期限内，乙方应每月向丙方支付本协议第一条约定借款本金总额的{function name=\"app_conf\" v=\"MANAGE_FEE\"}% (即人民币{function name=\"number_format\" v=\"$deal.month_manage_money\" f=2}元)，作为借款管理费用，共需支付{if $deal.repay_time_type eq 0}1{else}{$deal.repay_time}{/if}期，共计人民币{function name=\"number_format\" v=\"$deal.all_manage_money\" f=2} 元，借款管理费的缴纳时间与本协议第一条约定的还款日一致。</p>\r\n	<p> 本条所称的“借款成功时”系指本协议签署日。</p>\r\n	<p> 3、    如乙方和丙方协商一致调整借款管理费和居间服务费时，无需经过甲方同意。 </p>\r\n	<br/>\r\n	<p style=\"text-align: left;font-weight: 600;\">\r\n		第四条 违约责任\r\n	</p>\r\n	<p> 1、协议各方均应严格履行合同义务，非经各方协商一致或依照本协议约定，任何一方不得解除本协议。\r\n	</p>\r\n	<p>2、任何一方违约，违约方应承担因违约使得其他各方产生的费用和损失，包括但不限于调查、诉讼费、律师费等，应由违约方承担。如违约方为乙方的，甲方有权立即解除本协议，并要求乙方立即偿还未偿还的本金、利息、罚息、违约金。此时，乙方还应向丙方支付所有应付的借款管理费。如本协议提前解除时，乙方在{$SITE_URL}网站的账户里有任何余款的，丙方有权按照本协议第四条第3项的清偿顺序将乙方的余款用于清偿，并要求乙方支付因此产生的相关费用。</p>\r\n	<p>3、乙方的每期还款均应按照如下顺序清偿：</p>\r\n	<p>（1）根据本协议产生的其他全部费用；</p>\r\n	<p>（2）本协议第四条第4款约定的罚息； </p>\r\n	<p>（3）本协议第四条第5款约定的逾期管理费；</p>\r\n	<p>（4）拖欠的利息； </p>\r\n	<p>（5）拖欠的本金； </p>\r\n	<p>（6）拖欠丙方的借款管理费；\r\n	</p>\r\n	<p>（7）正常的利息； </p>\r\n	<p>（8）正常的本金； </p>\r\n	<p>（9）丙方的借款管理费；</p>\r\n	<p> 4、乙方应严格履行还款义务，如乙方逾期还款，则应按照下述条款向甲方支付逾期罚息，自逾期开始之后，逾期本金的正常利息停止计算。 </p>\r\n	<p>罚息总额 = 逾期本息总额×对应罚息利率×逾期天数；</p>\r\n</div>\r\n<div>\r\n	<br/>\r\n	<table border=\"1\" style=\"margin: 0px auto; border-collapse: collapse; border: 1px solid rgb(0, 0, 0); width: 70%; \">\r\n		<tr>\r\n			<td style=\"padding-left:10px\">\r\n				逾期天数\r\n			</td>\r\n			<td style=\"padding-left:10px\">\r\n				1—<?php echo (int)app_conf(\"YZ_IMPSE_DAY\")-1; ?>天\r\n			</td>\r\n			<td style=\"padding-left:10px\">\r\n				{function name=\"app_conf\" v=\"YZ_IMPSE_DAY\"}天及以上\r\n			</td>\r\n		</tr>\r\n		<tr>\r\n			<td style=\"padding-left:10px\">\r\n				罚息利率\r\n			</td>\r\n			<td style=\"padding-left:10px\">\r\n				{$deal.impose_fee_day1}%\r\n			</td>\r\n			<td style=\"padding-left:10px\">\r\n				{$deal.impose_fee_day2}%\r\n			</td>\r\n		</tr>\r\n	</table>\r\n	<br/>\r\n	<p>\r\n		5、乙方应严格履行还款义务，如乙方逾期还款，则应按照下述条款向丙方支付逾期管理费：\r\n	</p>\r\n	<p>\r\n		逾期管理费总额 = 逾期本息总额×对应逾期管理费率×逾期天数；\r\n	</p>\r\n</div>\r\n<br/>\r\n<table border=\"1\" style=\"margin: 0px auto; border-collapse: collapse; border: 1px solid rgb(0, 0, 0); width: 70%; \">\r\n	<tr>\r\n		<td style=\"padding-left:10px\">\r\n			逾期天数\r\n		</td>\r\n		<td style=\"padding-left:10px\">\r\n			1—<?php echo (int)app_conf(\"YZ_IMPSE_DAY\")-1; ?>天\r\n		</td>\r\n		<td style=\"padding-left:10px\">\r\n			{function name=\"app_conf\" v=\"YZ_IMPSE_DAY\"}天及以上\r\n		</td>\r\n	</tr>\r\n	<tr>\r\n		<td style=\"padding-left:10px\">\r\n			逾期管理费费率\r\n		</td>\r\n		<td style=\"padding-left:10px\">\r\n			{$deal.manage_impose_fee_day1}%\r\n		</td>\r\n		<td style=\"padding-left:10px\">\r\n			{$deal.manage_impose_fee_day2}%\r\n		</td>\r\n	</tr>\r\n</table>\r\n<br/>\r\n<div>\r\n	<p>\r\n		6、如果乙方逾期支付任何一期还款超过30天，或乙方在逾期后出现逃避、拒绝沟通或拒绝承认欠款事实等恶意行为，本协议项下的全部借款本息及借款管理费均提前到期，乙方应立即清偿本协议下尚未偿付的全部本金、利息、罚息、借款管理费及根据本协议产生的其他全部费用。\r\n	</p>\r\n	<p>\r\n		7、如果乙方逾期支付任何一期还款超过30天，或乙方在逾期后出现逃避、拒绝沟通或拒绝承认欠款事实等恶意行为，丙方有权将乙方的“逾期记录”记入人民银行公民征信系统，丙方不承担任何法律责任。\r\n	</p>\r\n	<p>\r\n		8、如果乙方逾期支付任何一期还款超过30天，或乙方在逾期后出现逃避、拒绝沟通或拒绝承认欠款事实等恶意行为，丙方有权将乙方违约失信的相关信息及乙方其他信息向媒体、用人单位、公安机关、检查机关、法律机关披露，丙方不承担任何责任。\r\n	</p>\r\n	<p>\r\n		9、在乙方还清全部本金、利息、借款管理费、罚息、逾期管理费之前，罚息及逾期管理费的计算不停止。\r\n	</p>\r\n	<p>\r\n		10、本协议中的所有甲方与乙方之间的借款均是相互独立的，一旦乙方逾期未归还借款本息，甲方中的任何一个出借人均有权单独向乙方追索或者提起诉讼。如乙方逾期支付借款管理费或提供虚假信息的，丙方亦可单独向乙方追索或者提起诉讼。\r\n	</p>\r\n	<br/>\r\n	<p style=\"text-align: left;font-weight: 600;\">\r\n		第五条 提前还款\r\n	</p>\r\n	<p>\r\n		1、乙方可在借款期间任何时候提前偿还剩余借款。\r\n	</p>\r\n	<p>\r\n		2、提前偿还全部剩余借款\r\n	</p>\r\n	<p style=\"padding-left: 15px\">\r\n		1）乙方提前清偿全部剩余借款时，应向甲方支付当期应还本息，剩余本金及提前还款补偿（补偿金额为剩余本金的{function name=\"app_conf\" v=\"COMPENSATE_FEE\"}%）。\r\n	</p>\r\n	<p style=\"padding-left: 15px\">\r\n		2）乙方提前清偿全部剩余借款时，应向丙方支付当期借款管理费，乙方无需支付剩余还款期的借款管理费。\r\n	</p>\r\n	<p>\r\n		3、提前偿还部分借款\r\n	</p>\r\n	<p style=\"padding-left: 15px\">\r\n		1）乙方提前偿还部分借款，仍应向甲方支付全部借款利息。\r\n	</p>\r\n	<p style=\"padding-left: 15px\">\r\n		2）乙方提前偿还部分借款，仍应向丙方支付全部应付的借款管理费。\r\n	</p>\r\n	<p>\r\n		4、任何形式的提前还款不影响丙方向乙方收取在本协议第三条中说明的居间服务费。\r\n	</p>\r\n	<br/>\r\n	<p style=\"text-align: left;font-weight: 600;\">\r\n		第六条	法律及争议解决\r\n	</p>\r\n	 <p>\r\n		 本协议的签订、履行、终止、解释均适用中华人民共和国法律，并由丙方所在地{function name=\"app_conf\" v=\'COMPANY_REG_ADDRESS\'}人民法院管辖。\r\n	</p>\r\n	<br/>\r\n	<p style=\"text-align: left;font-weight: 600;\">\r\n		第七条	附则\r\n	</p>\r\n	<p>\r\n		1、本协议采用电子文本形式制成，并永久保存在丙方为此设立的专用服务器上备查，各方均认可该形式的协议效力。\r\n	</p>\r\n	<p>\r\n		2、本协议自文本最终生成之日生效。\r\n	</p>\r\n	<p>\r\n		3、本协议签订之日起至借款全部清偿之日止，乙方或甲方有义务在下列信息变更三日内提供更新后的信息给丙方：本人、本人的家庭联系人及紧急联系人、工作单位、居住地址、住所电话、手机号码、电子邮箱、银行账户的变更。若因任何一方不及时提供上述变更信息而带来的损失或额外费用应由该方承担。\r\n	</p>\r\n	<p>\r\n		4、如果本协议中的任何一条或多条违反适用的法律法规，则该条将被视为无效，但该无效条款并不影响本协议其他条款的效力。\r\n	</p>\r\n        <br>\r\n	{if $deal.attachment}\r\n	<p style=\"text-align: left;font-weight: 600;\">\r\n		第八条	附件\r\n	</p>\r\n	<p>\r\n		{$deal.attachment}\r\n	</p>\r\n	{/if}\r\n</div>\r\n<br/>\r\n<div style=\"width: 98%;text-align: right;\">\r\n	<p>\r\n		{function name=\"to_date\" v=\"$deal.repay_start_time\" f=\"Y年m月d日\"}\r\n	</p>\r\n</div>', '1', '0');
INSERT INTO `%DB_PREFIX%contract` VALUES ('7', '债权转让合同范本', '<div style=\"width: 98%;text-align: right;\">\r\n编号：<span>Z-{$transfer.load_id}</span>\r\n</div>\r\n <h2 align=\"center\">债权转让及受让协议</h2>\r\n\r\n<br/>\r\n<div> \r\n\r\n　　　本债权转让及受让协议（下称“本协议”）由以下双方于签署：\r\n</p>\r\n</div>\r\n<br/>\r\n<div> \r\n<p style=\"text-align: left;font-weight: 600;\">甲方（转让人）：{$transfer.user.real_name}</p>\r\n<p>身份证号：{$transfer.user.idno}</p>\r\n<p>{$SITE_TITLE}用户名：{$transfer.user.user_name}</p>\r\n</div>\r\n <br/>\r\n<div> \r\n<p style=\"text-align: left;font-weight: 600;\">乙方（受让人）：{$transfer.tuser.real_name}</p>\r\n<p>身份证号：{$transfer.tuser.idno}</p>\r\n<p>{$SITE_TITLE}用户名：{$transfer.tuser.user_name}</p>\r\n</div>\r\n <br/>\r\n <p>就甲方通过{$SITE_TITLE}商务顾问（北京）有限公司（以下“{$SITE_TITLE}”系指{$SITE_TITLE}商务顾问（北京）有限公司和下述{$SITE_TITLE}网站的统称）运营管理的<?php echo str_replace(\"http://\",\"\",get_domain()); ?> 网站（下称“{$SITE_TITLE}网站”）向乙方转让债权事宜，双方经协商一致，达成如下协议：</p>       \r\n<br/>\r\n<p style=\"text-align: left;font-weight: 600;\">1.  债权转让</p>\r\n<p>1.1  标的债权信息及转让</p>     <p>甲方同意将其通过{$SITE_TITLE}的居间协助而形成的有关债权（下称“标的债权”）转让给乙方，乙方同意受让该等债权。标的债权具体信息如下：<p>\r\n<p style=\"text-align: left;font-weight: 600;\">标的债权信息：</p>\r\n<br/>\r\n<table border=\"1\" style=\"margin: 0px auto; border-collapse: collapse; border: 1px solid rgb(0, 0, 0); width: 70%; \">\r\n<tr>\r\n  <td width=\"20%\" style=\"padding-left:10px\">借款ID</td>\r\n <td style=\"padding-left:10px\">{$transfer.load_id}</td>\r\n</tr>\r\n<tr>\r\n  <td style=\"padding-left:10px\">借款人姓名</td>\r\n  <td style=\"padding-left:10px\">{$transfer.user.real_name}</td>\r\n</tr>\r\n<tr>\r\n  <td style=\"padding-left:10px\">借款本金数额</td>\r\n <td style=\"padding-left:10px\">\r\n    {$transfer.load_money_format}                                                                      \r\n </td>\r\n</tr>\r\n<tr>\r\n  <td style=\"padding-left:10px\">借款年利率</td>\r\n  <td style=\"padding-left:10px\">{$transfer.rate}%</td>\r\n</tr>\r\n<tr>\r\n  <td style=\"padding-left:10px\">原借款期限</td>\r\n  <td style=\"padding-left:10px\">\r\n	{$transfer.repay_time} 个月，{$transfer.repay_start_time_format} 起，至 {$transfer.final_repay_time_format}止</td>\r\n</tr>\r\n<tr>\r\n  <td style=\"padding-left:10px\">月偿还本息数额</td>\r\n  <td style=\"padding-left:10px\">\r\n  {$transfer.month_repay_money_format}\r\n  </td>\r\n</tr>\r\n<tr>\r\n  <td style=\"padding-left:10px\">还款日</td>\r\n  <td style=\"padding-left:10px\">\r\n    {$transfer.repay_start_time_format} 自起，每月 {function name=\"to_date\" v=$transfer.repay_start_time f=\"d\"} 日（24:00前，节假日不顺延）\r\n </td>\r\n</tr>\r\n</table>\r\n<p style=\"text-align: left;font-weight: 600;\">标的债权转让信息</p>\r\n<br/>\r\n<table border=\"1\" style=\"margin: 0px auto; border-collapse: collapse; border: 1px solid rgb(0, 0, 0); width: 70%; \">\r\n<tr>\r\n  <td width=\"20%\" style=\"padding-left:10px\">标的债权价值</td>\r\n <td style=\"padding-left:10px\">\r\n	{$transfer.all_must_repay_money_format}                                             \r\n   </td>\r\n</tr>\r\n<tr>\r\n  <td style=\"padding-left:10px\">转让价款</td>\r\n <td style=\"padding-left:10px\">\r\n	{$transfer.transfer_amount_format}                                                        \r\n  </td>\r\n</tr>\r\n<tr>\r\n  <td style=\"padding-left:10px\">转让管理费</td>\r\n  <td style=\"padding-left:10px\">\r\n		{$transfer.transfer_fee_format}\r\n  </td>\r\n</tr>\r\n<tr>\r\n  <td style=\"padding-left:10px\">转让日期</td>\r\n <td style=\"padding-left:10px\">{$transfer.transfer_time_format}</td>\r\n</tr>\r\n<tr>\r\n  <td style=\"padding-left:10px\">剩余还款分期月数</td>\r\n <td style=\"padding-left:10px\">\r\n    {$transfer.how_much_month} 个月，{$transfer.near_repay_time_format} 起，至  {$transfer.final_repay_time_format} 止 \r\n </td>\r\n</tr>\r\n</table>\r\n<br/>\r\n<p>1.2  债权转让流程</p>\r\n<p>1.2.1  双方同意并确认，双方通过自行或授权有关方根据{$SITE_TITLE}网站有关规则和说明，在{$SITE_TITLE}网站进行债权转让和受让购买操作等方式确认签署本协议。</p>\r\n<p>1.2.2  双方接受本协议且{$SITE_TITLE}审核通过时，本协议立即成立,并待转让价款支付完成时生效。协议成立的同时甲方不可撤销地授权{$SITE_TITLE}自行或委托第三方支付机构或合作的金融机构，将转让价款在扣除甲方应支付给{$SITE_TITLE}的转让管理费之后划转、支付给乙方，上述转让价款划转完成即视为本协议生效且标的债权转让成功；同时甲方不可撤销地授权{$SITE_TITLE}将其代为保管的甲方与标的债权借款人签署的电子文本形式的《借款协议》（下称“借款协议”）及借款人相关信息在{$SITE_TITLE}网站有关系统板块向乙方进行展示。</p>\r\n<p>1.2.3  本协议生效且标的债权转让成功后，双方特此委托{$SITE_TITLE}将标的债权的转让事项及有关信息通过站内信等形式通知与标的债权对应的借款人。</p>\r\n<p>1.3  自标的债权转让成功之日起，乙方成为标的债权的债权人，承继借款协议项下出借人的权利并承担出借人的义务。</p>\r\n<br/>\r\n<p style=\"text-align: left;font-weight: 600;\">2.  保证与承诺</p>\r\n<p>2.1  甲方保证其转让的债权系其合法、有效的债权，不存在转让的限制。甲方同意并承诺按有关协议及{$SITE_TITLE}网站的相关规则和说明向{$SITE_TITLE}支付债权转让管理费。</p>\r\n<p>2.2  乙方保证其所用于受让标的债权的资金来源合法，乙方是该资金的合法所有人。如果第三方对资金归属、合法性问题发生争议，乙方应自行负责解决并承担相关责任。</p><br/>\r\n<p style=\"text-align: left;font-weight: 600;\">3.  违约</p>\r\n<p>3.1  双方同意，如果一方违反其在本协议中所作的保证、承诺或任何其他义务，致使其他方遭受或发生损害、损失等责任，违约方须向守约方赔偿守约方因此遭受的一切经济损失。</p>\r\n<p>3.2  双方均有过错的，应根据双方实际过错程度，分别承担各自的违约责任。</p><br/>\r\n<p style=\"text-align: left;font-weight: 600;\">4.  适用法律和争议解决</p>\r\n<p>4.1  本协议的订立、效力、解释、履行、修改和终止以及争议的解决适用中国的法律。</p>\r\n<p>4.2  本协议在履行过程中，如发生任何争执或纠纷，双方应友好协商解决；若协商不成，任何一方均有权向有管辖权的人民法院提起诉讼。</p><br/>\r\n<p style=\"text-align: left;font-weight: 600;\">5.  其他</p>\r\n<p>5.1  双方可以书面协议方式对本协议作出修改和补充。经过双方签署的有关本协议的修改协议和补充协议是本协议组成部分，具有与本协议同等的法律效力。</p>\r\n<p>5.2  本协议及其修改或补充均通过{$SITE_TITLE}网站以电子文本形式制成，可以有一份或者多份并且每一份具有同等法律效力；同时双方委托{$SITE_TITLE}代为保管并永久保存在{$SITE_TITLE}为此设立的专用服务器上备查。双方均认可该形式的协议效力。</p>\r\n<p>5.3  甲乙双方均确认，本协议的签订、生效和履行以不违反中国的法律法规为前提。如果本协议中的任何一条或多条违反适用的法律法规，则该条将被视为无效，但该无效条款并不影响本协议其他条款的效力。</p>\r\n<p>5.4  除本协议上下文另有定义外，本协议项下的用语和定义应具有{$SITE_TITLE}网站服务协议及其有关规则中定义的含义。若有冲突，则以本协议为准。</p>\r\n        <br>	\r\n        {if $transfer.tattachment}\r\n	<p style=\"text-align: left;font-weight: 600;\">\r\n		第八条	附件\r\n	</p>\r\n	<p>\r\n		{$transfer.tattachment}\r\n	</p>\r\n	{/if}\r\n</div>\r\n  <br>\r\n<div style=\"width: 98%;text-align: right;\">\r\n	<p>\r\n		{$transfer.transfer_time_format}\r\n	</p>\r\n</div>', '1', '0');

-- ----------------------------
-- Table structure for `%DB_PREFIX%customer`
-- ----------------------------
DROP TABLE IF EXISTS `%DB_PREFIX%customer`;
CREATE TABLE `%DB_PREFIX%customer` (
  `id` int(11) NOT NULL auto_increment COMMENT '客服编号',
  `name` varchar(255) NOT NULL COMMENT '客服名称',
  `telphone` varchar(20) NOT NULL COMMENT '客服电话',
  `qq` varchar(20) NOT NULL COMMENT '客服qq',
  `is_effect` tinyint(1) NOT NULL default '1' COMMENT '客服状态【有效性',
  `is_delete` tinyint(1) NOT NULL default '0',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='公司信息标';

-- ----------------------------
-- Records of fanwe_customer
-- ----------------------------

-- ----------------------------
-- Table structure for `%DB_PREFIX%deal`
-- ----------------------------
DROP TABLE IF EXISTS `%DB_PREFIX%deal`;
CREATE TABLE `%DB_PREFIX%deal` (
  `id` int(11) NOT NULL auto_increment,
  `name` text NOT NULL COMMENT '借款名称',
  `sub_name` varchar(255) NOT NULL COMMENT '缩略么',
  `cate_id` int(11) NOT NULL COMMENT '借款分类',
  `agency_id` int(11) NOT NULL COMMENT '担保机构（标识ID）',
  `agency_status` tinyint(1) NOT NULL COMMENT '应邀状态 0 应邀 1邀约中 2 拒绝',
  `user_id` int(11) NOT NULL COMMENT '借款人（标识ID）',
  `description` text NOT NULL COMMENT '简介',
  `is_effect` tinyint(1) NOT NULL COMMENT ' 有效性控制',
  `is_delete` tinyint(1) NOT NULL COMMENT '删除标识',
  `sort` int(11) NOT NULL COMMENT '排序  大->小',
  `type_id` int(11) NOT NULL COMMENT '借款用途（标识id）',
  `icon_type` tinyint(1) NOT NULL COMMENT '0自己上传，2用户头像，3类型图',
  `icon` varchar(255) NOT NULL COMMENT '贷款缩略图',
  `seo_title` text NOT NULL COMMENT 'SEO标题',
  `seo_keyword` text NOT NULL COMMENT 'SEO关键词',
  `seo_description` text NOT NULL COMMENT 'SEO说明',
  `is_hot` tinyint(1) NOT NULL COMMENT '是否热门',
  `is_new` tinyint(1) NOT NULL COMMENT '是否最新',
  `is_best` tinyint(1) NOT NULL COMMENT '是否最佳',
  `borrow_amount` decimal(20,2) NOT NULL COMMENT '借款总额',
  `apart_borrow_amount` decimal(10,0) NOT NULL COMMENT '拆标后的原借款标金额',
  `min_loan_money` decimal(20,2) NOT NULL default '50.00' COMMENT '最底投标额度',
  `max_loan_money` decimal(20,2) NOT NULL default '0.00' COMMENT '最高投标额度',
  `repay_time` int(11) NOT NULL COMMENT '接口时间',
  `rate` decimal(10,2) NOT NULL COMMENT '年利率',
  `day` int(1) NOT NULL COMMENT '招标时间',
  `create_time` int(11) NOT NULL COMMENT '添加时间',
  `update_time` int(11) NOT NULL COMMENT '更新时间',
  `name_match` text NOT NULL,
  `name_match_row` text NOT NULL,
  `deal_cate_match` text NOT NULL,
  `deal_cate_match_row` text NOT NULL,
  `tag_match` text NOT NULL,
  `tag_match_row` text NOT NULL,
  `type_match` text NOT NULL,
  `type_match_row` text NOT NULL,
  `is_recommend` tinyint(1) NOT NULL COMMENT '是否推荐',
  `buy_count` int(11) NOT NULL COMMENT '投标人数',
  `load_money` decimal(20,2) NOT NULL COMMENT '已投标多少',
  `repay_money` decimal(20,2) NOT NULL COMMENT '还了多少！',
  `start_time` int(11) NOT NULL COMMENT '开始招标日期',
  `success_time` int(11) NOT NULL COMMENT '成功日期',
  `repay_start_time` int(11) NOT NULL COMMENT '开始还款日',
  `last_repay_time` int(11) NOT NULL COMMENT '最后一次还款日',
  `next_repay_time` int(11) NOT NULL COMMENT '下次还款日',
  `bad_time` int(11) NOT NULL COMMENT '流标时间',
  `deal_status` tinyint(4) NOT NULL COMMENT '0待等材料，1进行中，2满标，3流标，4还款中，5已还清',
  `enddate` int(11) NOT NULL COMMENT '筹标期限',
  `voffice` tinyint(1) NOT NULL COMMENT '是否显示公司名称',
  `vposition` tinyint(1) NOT NULL COMMENT '是否显示职位',
  `services_fee` varchar(20) NOT NULL COMMENT '服务费率',
  `publish_wait` tinyint(1) NOT NULL COMMENT '是否发布 1：待发布 0已发布, 2初审通过，3复审失败',
  `is_send_bad_msg` tinyint(1) NOT NULL COMMENT '是否已发送流标通知',
  `is_send_success_msg` tinyint(1) NOT NULL COMMENT '是否已经发送成功通知',
  `bad_msg` text NOT NULL COMMENT '流标通知内容',
  `send_half_msg_time` int(11) NOT NULL COMMENT '发送投标过半的通知时间',
  `send_three_msg_time` int(11) NOT NULL COMMENT '发送三天内需还款的通知时间',
  `is_send_half_msg` tinyint(1) NOT NULL COMMENT '是否已发送招标过半通知',
  `is_has_loans` tinyint(11) NOT NULL COMMENT '是否已经放款给招标人',
  `loantype` tinyint(1) NOT NULL COMMENT '还款方式  0:等额本息 1:付息还本 2:到期本息',
  `warrant` tinyint(1) NOT NULL COMMENT '担保范围 0:无  1:本金 2:本金及利息',
  `titlecolor` varchar(20) NOT NULL COMMENT '标题颜色',
  `is_send_contract` tinyint(1) NOT NULL COMMENT '是否已经发送电子协议书',
  `repay_time_type` tinyint(1) NOT NULL default '1' COMMENT '借款期限类型 0:按天还款  1:按月还款',
  `risk_rank` tinyint(1) NOT NULL default '0' COMMENT '风险等级',
  `risk_security` text NOT NULL COMMENT '风险保障',
  `deal_sn` varchar(50) NOT NULL COMMENT '借款编号',
  `is_has_received` tinyint(1) NOT NULL COMMENT '流标是否返已还',
  `manage_fee` varchar(30) NOT NULL COMMENT '借款者管理费',
  `user_loan_manage_fee` varchar(30) NOT NULL COMMENT '投资者管理费',
  `manage_impose_fee_day1` varchar(30) NOT NULL COMMENT '普通逾期管理费',
  `manage_impose_fee_day2` varchar(30) NOT NULL COMMENT '严重逾期管理费',
  `impose_fee_day1` varchar(30) NOT NULL COMMENT '普通逾期费率',
  `impose_fee_day2` varchar(30) NOT NULL COMMENT '严重逾期费率',
  `user_load_transfer_fee` varchar(30) NOT NULL COMMENT '债权转让管理费',
  `compensate_fee` varchar(30) NOT NULL COMMENT '提前还款补偿',
  `ips_do_transfer` tinyint(1) NOT NULL default '0' COMMENT '放款处理中',
  `ips_over` tinyint(1) NOT NULL default '0' COMMENT 'IPS 是否完成还款 0 未完成 1完成',
  `delete_msg` text NOT NULL COMMENT '审核失败提示',
  `user_bid_rebate` varchar(30) NOT NULL COMMENT '投资返利%',
  `guarantees_amt` decimal(20,2) NOT NULL default '0.00' COMMENT '借款保证金（冻结借款人的金额，需要提前存钱）',
  `real_freezen_amt` decimal(20,2) default '0.00' COMMENT '借款方 实际冻结金额 = 保证金',
  `un_real_freezen_amt` decimal(20,2) default '0.00' COMMENT '已经解冻的担保保证金（借款方）<=real_freezen_amt',
  `guarantor_amt` decimal(20,2) default '0.00' COMMENT '担保方，担保金额(代偿金额累计不能大于担保金额)',
  `guarantor_margin_amt` decimal(20,2) default '0.00' COMMENT '担保方，担保保证金额(需要冻结担保方的金额）',
  `guarantor_real_freezen_amt` decimal(20,2) default '0.00' COMMENT '担保方 实际冻结金额 = 担保保证金额',
  `un_guarantor_real_freezen_amt` decimal(20,2) default '0.00' COMMENT '已经解冻的担保保证金（担保方）<=guarantor_real_freezen_amt',
  `guarantor_pro_fit_amt` decimal(20,2) default '0.00' COMMENT '担保收益',
  `guarantor_real_fit_amt` decimal(20,2) default '0.00' COMMENT '实际担保收益，转帐后更新<=guarantor_pro_fit_amt',
  `mer_bill_no` varchar(30) default NULL COMMENT '标的登记时提交的订单单号',
  `ips_bill_no` varchar(30) default NULL COMMENT '由IPS系统生成的唯一流水号',
  `ips_guarantor_bill_no` varchar(30) default NULL COMMENT '担保编号ips返回的',
  `mer_guarantor_bill_no` varchar(30) default NULL COMMENT '提交的担保单号',
  `view_info` text NOT NULL,
  `generation_position` decimal(20,2) NOT NULL COMMENT '申请延期的额度',
  `uloadtype` tinyint(1) NOT NULL COMMENT '用户投标类型 0按金额，1 按份数',
  `portion` int(11) NOT NULL COMMENT '分成多少份',
  `max_portion` int(11) NOT NULL COMMENT '最多买多少份',
  `start_date` date NOT NULL COMMENT '开始投标时间，日期格式，方便统计',
  `repay_start_date` date NOT NULL COMMENT '满标放款,支出奖励时间,日期格式,方便统计',
  `bad_date` date NOT NULL COMMENT '流标时间,日期格式,方便统计',
  `contract_id` int(11) NOT NULL COMMENT '借款合同模板',
  `tcontract_id` int(11) NOT NULL COMMENT '转让合同模板',
  `is_advance` tinyint(1) NOT NULL COMMENT '是否预告',
  `is_hidden` tinyint(1) default '0' COMMENT '是否在投资列表隐藏 0：不隐藏，1：隐藏',
  `score` int(11) NOT NULL COMMENT '借款者获得积分',
  `user_bid_score_fee` varchar(20) NOT NULL COMMENT '投资返还积分比率',
  `user_loan_interest_manage_fee` varchar(30) NOT NULL,
  `guarantees_money` decimal(20,2) NOT NULL default '0.00' COMMENT '借款保证金',
  `attachment` text NOT NULL COMMENT '合同附件',
  `tattachment` text NOT NULL COMMENT '转让合同附件',
  `publish_memo` text NOT NULL,
  `is_index_show` tinyint(1) default '0' COMMENT '是否首页显示',
  `loans_pic` varchar(255) default NULL COMMENT '白条满标放款凭证',
  `admin_id` int(11) NOT NULL COMMENT '管理员id',
  PRIMARY KEY  (`id`),
  KEY `cate_id` (`cate_id`),
  KEY `sort` (`sort`),
  KEY `create_time` (`create_time`),
  KEY `update_time` (`update_time`),
  KEY `user_id` USING BTREE (`user_id`),
  KEY `idx_1` (`user_id`,`publish_wait`),
  KEY `idx_0` (`deal_status`,`user_id`,`publish_wait`),
  KEY `idx_d_001` (`start_date`),
  KEY `idx_d_002` (`repay_start_date`),
  KEY `idx_d_003` (`bad_date`),
  FULLTEXT KEY `name_match` (`name_match`),
  FULLTEXT KEY `tag_match` (`tag_match`),
  FULLTEXT KEY `deal_cate_match` (`deal_cate_match`),
  FULLTEXT KEY `all_match` (`name_match`,`deal_cate_match`,`tag_match`,`type_match`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of fanwe_deal
-- ----------------------------

-- ----------------------------
-- Table structure for `%DB_PREFIX%deal_agency`
-- ----------------------------
DROP TABLE IF EXISTS `%DB_PREFIX%deal_agency`;
CREATE TABLE `%DB_PREFIX%deal_agency` (
  `id` int(11) NOT NULL auto_increment,
  `header` text NOT NULL COMMENT '头部代码',
  `name` varchar(100) NOT NULL COMMENT '机构名称',
  `brief` text NOT NULL COMMENT '担保方介绍',
  `company_brief` text NOT NULL COMMENT '公司概况',
  `history` text NOT NULL COMMENT '发展史',
  `content` text NOT NULL COMMENT '内容',
  `is_effect` tinyint(1) NOT NULL COMMENT '有效性标识',
  `sort` int(11) NOT NULL,
  `short_name` varchar(100) NOT NULL COMMENT '缩略名',
  `address` varchar(255) NOT NULL COMMENT '地址',
  `acct_type` tinyint(1) NOT NULL default '1' COMMENT '担保方类型 否 0#机构；1#个人',
  `ips_mer_code` varchar(10) default NULL COMMENT '由IPS颁发的商户号 acct_type = 0',
  `idno` varchar(20) default NULL COMMENT '真实身份证 acct_type =1',
  `real_name` varchar(30) default NULL COMMENT 'acct_type = 1真实姓名',
  `mobile` varchar(11) default NULL,
  `email` varchar(30) default NULL,
  `ips_acct_no` varchar(30) default NULL COMMENT 'ips个人帐户',
  `password` varchar(32) NOT NULL COMMENT '密码',
  `code` varchar(30) default NULL COMMENT '找回密码验证码',
  `update_time` int(11) NOT NULL COMMENT '修改时间',
  `create_time` int(11) NOT NULL COMMENT '注册时间',
  `login_ip` varchar(30) default NULL COMMENT '登陆ip',
  `login_time` int(11) default NULL COMMENT '登陆时间',
  `emailpassed` tinyint(1) NOT NULL default '0' COMMENT '邮箱验证标识符',
  `verify` varchar(10) default NULL COMMENT '邮件验证码',
  `verify_create_time` int(11) default NULL COMMENT '邮件验证码生成时间',
  `mobilepassed` tinyint(1) default '0' COMMENT '手机验证标识',
  `bind_verify` varchar(10) default NULL COMMENT '手机绑定验证码',
  `view_info` text NOT NULL COMMENT '资料展示',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of fanwe_deal_agency
-- ----------------------------
INSERT INTO `%DB_PREFIX%deal_agency` VALUES ('1', '<a href=\"http://www.fanwe.cn/\" title=\"担保机构参考\" target=\"_blank\"><img src=\"http://www.renrendai.com/event/zaccn/logo.jpg\" width=\"179\" height=\"41\" alt=\"\" border=\"0\" /></a>', '担保机构参考', '深圳市中安信业创业投资有限公司是一家专门为个体工商户、小企业主和低收入家庭提供快速简便、无抵押无担保小额个人贷款服务的企业。公司自2004年开始探索无抵押无担保贷款， 至今累计放款全国最多，小额贷款服务的客户最多。在广东省（深圳市、佛山市），北京市，天津市，上海市，河北省，福建省，山东省，江苏省，湖南省，广西， 四川省，浙江省，河南省，湖北省，安徽省与辽宁省等五十多家便利的网点，逾千名员工专门从事小额贷款业务。中安信业是国内探索无抵押无担保商业化 可持续小额贷款最早的、累计放款量和贷款余额最多的、全国网点最多的、信贷质量最好的、运作最为规范的专业小额贷款机构。', '深圳市中安信业创业投资有限公司（中安信业）成立于2003年10月，是一家专门协助银行金融机构为微小企业、个体户及中低收入人群提供快速简便、免抵押、免担保小额贷款服务的专业现代化小额信贷技术服务公司；累计协助银行发放贷款逾数十亿元，一直保持良好的资产质量，对合作机构的还款从未发生过一分钱的逾期；全国60个营业网点，覆盖了17个省，26个城市，专业的信贷从业人员逾1500人。', '<p><span>2003年</span> – 中安信业成立；</p>\r\n 				<p><span>2006年</span> – 深圳首家获政府批准的小额贷款试点企业；</p>\r\n 			 	<p><span>2008年</span> – 国际金融公司（IFC）入股中安信业，并派其全球小额贷款业务负责人参加中安信业的董事会；</p>\r\n			 	<p><span>2009年</span> – 首创 “贷款银行  + 代理机构” 的助贷模式，荣获深圳市市政府和国家开发银行的金融创新奖； </p>\r\n			 	<p><span>2010年 </span> – 深圳市小额贷款行业协会首届会长单位；</p>\r\n			 	<p><span>2011年</span> – 中国小额信贷机构联席会副会长单位； </p>\r\n			 	<p><span>2011年</span> – 中安信业携手国家开发银行、德国复兴信贷银行和国际金融公司共同创办了深圳龙岗国安村镇银行。</p>', '<div style=\"margin-bottom:20px;\">			<h3>高管团队</h3>\r\n			<p style=\"text-indent:20px;\"> 	中安信业拥有业内经验最丰富的管理团队，包括了多名在国内外银行业和小额贷款领域的资深高级管理人员。他们分别来自汇丰银行，大众银行，摩根士丹利，花旗银行，Procredit，工商银行，建设银行，深圳发展银行，安信信贷，平安集团，中国人民银行等。 </p>\r\n		</div>\r\n<img src=\"http://www.renrendai.com/event/zaccn/thinLine.jpg\" width=\"818\" height=\"6\" alt=\"\" border=\"0\" /><div>			<h3>经济效益和社会效益</h3>\r\n			<div id=\"xiaoyi\">				<ul>					<li>中安信业的标志代表着我们的两大目标：经济效益和社会效益，经济回报和社会回报，二者相辅相成。</li>\r\n					<li>通过协助银行金融机构为小微企业提供贷款，帮助其发展壮大，推动了经济发展，促进了就业和社会稳定。<br />\r\n						-累计至今，至少为超过10万名小微企业主提供了贷款，支持了超过几十万个就业机会。<br />\r\n						-为处于创业阶段的小企业提供了发展资金，免抵押，免担保，无需财务报表					</li>\r\n						<li>创造了贷款客户、银行机构和社会的多赢局面。</li>\r\n						</ul>\r\n			</div>\r\n			<img src=\"http://www.renrendai.com/event/zaccn/photo.jpg\" alt=\"\" border=\"0\" />		</div>', '1', '0', '担保机构参考', '', '1', null, null, null, null, null, null, '', null, '0', '0', null, null, '0', null, null, '0', null, '');

-- ----------------------------
-- Table structure for `%DB_PREFIX%deal_cate`
-- ----------------------------
DROP TABLE IF EXISTS `%DB_PREFIX%deal_cate`;
CREATE TABLE `%DB_PREFIX%deal_cate` (
  `id` int(11) NOT NULL auto_increment,
  `name` varchar(255) NOT NULL COMMENT '分类名称',
  `brief` text NOT NULL COMMENT '分类简介',
  `pid` int(11) NOT NULL COMMENT '父类ID',
  `is_delete` tinyint(1) NOT NULL COMMENT '删除标识',
  `is_effect` tinyint(1) NOT NULL COMMENT '有效性标识',
  `sort` int(11) NOT NULL,
  `uname` varchar(255) NOT NULL,
  `icon` varchar(255) NOT NULL default '' COMMENT '分类icon',
  PRIMARY KEY  (`id`),
  KEY `pid` (`pid`),
  KEY `sort` (`sort`)
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of fanwe_deal_cate
-- ----------------------------
INSERT INTO `%DB_PREFIX%deal_cate` VALUES ('1', '信用认证标', '', '0', '0', '1', '10', '', '');
INSERT INTO `%DB_PREFIX%deal_cate` VALUES ('2', '实地认证标', '', '0', '0', '1', '9', '', './public/images/dealcate/sdrzb.png');
INSERT INTO `%DB_PREFIX%deal_cate` VALUES ('3', '机构担保标', '', '0', '0', '1', '3', '', './public/images/dealcate/jgdbb.png');
INSERT INTO `%DB_PREFIX%deal_cate` VALUES ('4', '智能理财标', '', '0', '0', '1', '4', '', './public/images/dealcate/zhibt.png');

-- ----------------------------
-- Table structure for `%DB_PREFIX%deal_city`
-- ----------------------------
DROP TABLE IF EXISTS `%DB_PREFIX%deal_city`;
CREATE TABLE `%DB_PREFIX%deal_city` (
  `id` int(11) NOT NULL auto_increment,
  `name` varchar(255) NOT NULL,
  `uname` varchar(255) NOT NULL,
  `is_effect` tinyint(1) NOT NULL,
  `is_delete` tinyint(1) NOT NULL,
  `pid` int(11) NOT NULL,
  `is_default` tinyint(1) NOT NULL,
  `seo_title` text NOT NULL,
  `seo_keyword` text NOT NULL,
  `seo_description` text NOT NULL,
  `sort` int(11) NOT NULL,
  PRIMARY KEY  (`id`),
  KEY `pid` (`pid`),
  KEY `sort` (`sort`)
) ENGINE=MyISAM AUTO_INCREMENT=18 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of fanwe_deal_city
-- ----------------------------
INSERT INTO `%DB_PREFIX%deal_city` VALUES ('1', '全国', 'quanguo', '1', '0', '0', '1', '', '', '', '0');

-- ----------------------------
-- Table structure for `%DB_PREFIX%deal_city_link`
-- ----------------------------
DROP TABLE IF EXISTS `%DB_PREFIX%deal_city_link`;
CREATE TABLE `%DB_PREFIX%deal_city_link` (
  `deal_id` int(11) NOT NULL,
  `city_id` int(11) NOT NULL,
  KEY `idx0` (`deal_id`,`city_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of fanwe_deal_city_link
-- ----------------------------

-- ----------------------------
-- Table structure for `%DB_PREFIX%deal_collect`
-- ----------------------------
DROP TABLE IF EXISTS `%DB_PREFIX%deal_collect`;
CREATE TABLE `%DB_PREFIX%deal_collect` (
  `id` int(11) NOT NULL auto_increment,
  `deal_id` int(11) NOT NULL COMMENT '借款（标识ID）',
  `user_id` int(11) NOT NULL COMMENT '收藏者（标识性ID）',
  `create_time` int(11) NOT NULL COMMENT '收藏时间',
  PRIMARY KEY  (`id`),
  KEY `deal_id` (`deal_id`),
  KEY `user_id` (`user_id`),
  KEY `create_time` (`create_time`)
) ENGINE=MyISAM AUTO_INCREMENT=18 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of fanwe_deal_collect
-- ----------------------------
INSERT INTO `%DB_PREFIX%deal_collect` VALUES ('17', '1', '44', '1355877403');

-- ----------------------------
-- Table structure for `%DB_PREFIX%deal_inrepay_repay`
-- ----------------------------
DROP TABLE IF EXISTS `%DB_PREFIX%deal_inrepay_repay`;
CREATE TABLE `%DB_PREFIX%deal_inrepay_repay` (
  `id` int(11) NOT NULL auto_increment,
  `deal_id` int(11) NOT NULL COMMENT '借款（标识性ID）',
  `user_id` int(11) NOT NULL COMMENT '借款人（标识性ID）',
  `repay_money` decimal(20,2) NOT NULL COMMENT '提前还款多少',
  `manage_money` decimal(20,2) NOT NULL COMMENT '提前还款管理费',
  `impose` decimal(20,2) NOT NULL COMMENT '提前还款罚息',
  `repay_time` int(11) NOT NULL COMMENT '在哪一期提前还款',
  `true_repay_time` int(11) NOT NULL COMMENT '还款时间',
  `self_money` decimal(20,2) NOT NULL COMMENT '提前还款本金',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of fanwe_deal_inrepay_repay
-- ----------------------------

-- ----------------------------
-- Table structure for `%DB_PREFIX%deal_load`
-- ----------------------------
DROP TABLE IF EXISTS `%DB_PREFIX%deal_load`;
CREATE TABLE `%DB_PREFIX%deal_load` (
  `id` int(11) NOT NULL auto_increment,
  `deal_id` int(11) NOT NULL COMMENT '借款ID',
  `user_id` int(11) NOT NULL COMMENT '投标人ID',
  `user_name` varchar(50) NOT NULL COMMENT '用户名',
  `money` decimal(20,2) NOT NULL COMMENT '投标金额',
  `create_time` int(11) NOT NULL COMMENT '投标时间',
  `is_repay` tinyint(1) NOT NULL COMMENT '流标是否已返还',
  `is_rebate` tinyint(1) NOT NULL COMMENT '是否已返利',
  `is_auto` tinyint(1) NOT NULL COMMENT '是否为自动投标 0:收到 1:自动',
  `pP2PBillNo` varchar(30) default NULL COMMENT 'IPS P2P订单号 否 由IPS系统生成的唯一流水号',
  `pContractNo` varchar(30) default NULL COMMENT '合同号',
  `pMerBillNo` varchar(30) default NULL COMMENT '登记债权人时提 交的订单号',
  `is_has_loans` tinyint(1) NOT NULL default '0' COMMENT '是否已经放款给招标人',
  `msg` varchar(100) default NULL COMMENT '转账备注  转账失败的原因',
  `is_old_loan` tinyint(1) NOT NULL COMMENT '历史投标 0 不是  1 是 ',
  `create_date` date NOT NULL COMMENT '记录投资日期,方便统计使用',
  `rebate_money` decimal(20,2) NOT NULL default '0.00' COMMENT '返利金额',
  `is_winning` tinyint(1) NOT NULL COMMENT '是否中奖 0未中奖 1中奖',
  `income_type` tinyint(1) NOT NULL COMMENT '收益类型 1红包 2收益率 3积分 4礼品',
  `income_value` varchar(100) NOT NULL COMMENT '收益值',
  `bid_score` int(11) NOT NULL COMMENT '投标获得的积分',
  PRIMARY KEY  (`id`),
  KEY `deal_id` (`deal_id`),
  KEY `user_id` (`user_id`),
  KEY `idx_dl_001` (`create_date`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of fanwe_deal_load
-- ----------------------------

-- ----------------------------
-- Table structure for `%DB_PREFIX%deal_load_repay`
-- ----------------------------
DROP TABLE IF EXISTS `%DB_PREFIX%deal_load_repay`;
CREATE TABLE `%DB_PREFIX%deal_load_repay` (
  `id` int(11) NOT NULL auto_increment,
  `deal_id` int(11) NOT NULL COMMENT '借款（标识ID）',
  `user_id` int(11) NOT NULL COMMENT '投标人（标识ID）',
  `self_money` decimal(20,2) NOT NULL COMMENT '本金',
  `repay_money` decimal(20,2) NOT NULL COMMENT '还款金额',
  `manage_money` decimal(20,2) NOT NULL COMMENT '管理费',
  `impose_money` decimal(20,2) NOT NULL COMMENT '罚息',
  `repay_time` int(11) NOT NULL COMMENT '预计回款时间',
  `repay_date` date NOT NULL COMMENT '预计回款时间,方便统计',
  `true_repay_time` int(11) NOT NULL COMMENT '实际回款时间',
  `true_repay_date` date NOT NULL COMMENT '实际回款时间,方便统计使用',
  `true_repay_money` decimal(20,2) NOT NULL COMMENT '真实还款本息',
  `true_self_money` decimal(20,2) NOT NULL COMMENT '真实还款本金',
  `interest_money` decimal(20,2) NOT NULL COMMENT '利息   repay_money - self_money',
  `true_interest_money` decimal(20,2) NOT NULL COMMENT '实际利息',
  `true_manage_money` decimal(20,2) NOT NULL COMMENT '实际管理费',
  `true_repay_manage_money` decimal(20,2) NOT NULL,
  `status` int(11) NOT NULL COMMENT '0提前，1准时，2逾期，3严重逾期',
  `is_site_repay` tinyint(1) NOT NULL COMMENT '0自付，1网站垫付 2担保机构垫付',
  `l_key` int(11) NOT NULL default '0' COMMENT '还的是第几期',
  `u_key` int(11) NOT NULL default '0' COMMENT '还的是第几个投标人',
  `repay_id` int(11) NOT NULL COMMENT '还款计划ID',
  `load_id` int(11) NOT NULL COMMENT '投标记录ID',
  `has_repay` tinyint(11) NOT NULL default '0' COMMENT '0未收到还款，1已收到还款',
  `t_user_id` int(11) NOT NULL COMMENT '承接着会员ID',
  `repay_manage_money` decimal(20,2) NOT NULL COMMENT '从借款者均摊下来的管理费',
  `repay_manage_impose_money` decimal(20,2) NOT NULL COMMENT '借款者均摊下来的逾期管理费',
  `loantype` int(11) NOT NULL COMMENT '还款方式',
  `manage_interest_money` decimal(11,2) NOT NULL default '0.00' COMMENT '预计能收到：利息管理费,是在满标放款时生成',
  `true_manage_interest_money` decimal(11,2) NOT NULL default '0.00' COMMENT '实际收到：利息管理费,是在还款时生成',
  `manage_interest_money_rebate` decimal(11,2) NOT NULL default '0.00' COMMENT '预计返佣金额(返给授权机构)',
  `true_manage_interest_money_rebate` decimal(11,2) NOT NULL default '0.00' COMMENT '实际返佣金额(返给授权机构)',
  `t_pMerBillNo` varchar(255) default NULL COMMENT 'ips债权转让后新的ips流水号',
  `reward_money` decimal(20,2) NOT NULL COMMENT '预计奖励收益',
  `true_reward_money` decimal(20,2) NOT NULL COMMENT '实际奖励收益',
  PRIMARY KEY  (`id`),
  KEY `idx_0` (`deal_id`,`user_id`,`l_key`,`u_key`),
  KEY `idx_1` (`user_id`,`status`),
  KEY `idx_2` (`deal_id`,`user_id`,`repay_time`,`l_key`,`u_key`),
  KEY `idx_dl_001` (`repay_date`),
  KEY `idx_dl_002` (`true_repay_date`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of fanwe_deal_load_repay
-- ----------------------------

-- ----------------------------
-- Table structure for `%DB_PREFIX%deal_load_transfer`
-- ----------------------------
DROP TABLE IF EXISTS `%DB_PREFIX%deal_load_transfer`;
CREATE TABLE `%DB_PREFIX%deal_load_transfer` (
  `id` int(11) NOT NULL auto_increment,
  `deal_id` int(11) NOT NULL COMMENT '所投的标',
  `load_id` int(11) NOT NULL COMMENT '债权ID',
  `user_id` int(11) NOT NULL COMMENT '债权人ID',
  `transfer_amount` decimal(20,2) NOT NULL COMMENT '转让价格',
  `load_money` decimal(20,2) NOT NULL COMMENT '投标金额',
  `last_repay_time` int(11) NOT NULL COMMENT '最后还款日期',
  `near_repay_time` int(11) NOT NULL COMMENT '下次还款日',
  `transfer_number` int(11) NOT NULL COMMENT '转让期数',
  `t_user_id` int(11) NOT NULL COMMENT '承接人',
  `transfer_time` int(11) NOT NULL COMMENT '承接时间',
  `create_time` int(11) NOT NULL COMMENT '发布时间',
  `status` tinyint(1) NOT NULL COMMENT '转让状态，0取消 1开始',
  `callback_count` int(11) NOT NULL COMMENT '撤回次数',
  `lock_user_id` int(11) NOT NULL default '0' COMMENT '锁定用户id,给用户支付时间,主要用于资金托管',
  `lock_time` int(11) NOT NULL default '0' COMMENT '锁定时间,10分钟后,自动解锁;给用户支付时间,主要用于资金托管',
  `ips_status` tinyint(1) NOT NULL default '0' COMMENT 'ips处理状态;0:未处理;1:已登记债权转让;2:已转让',
  `ips_bill_no` varchar(30) default NULL COMMENT 'IPS P2P订单号 否 由IPS系统生成的唯一流水号',
  `pMerBillNo` varchar(30) default NULL COMMENT '商户订单号 商户系统唯一不重复',
  `create_date` date NOT NULL COMMENT '发布时间,日期格式,方便统计',
  `transfer_date` date NOT NULL COMMENT '承接时间,日期格式,方便统计',
  PRIMARY KEY  (`id`),
  KEY `idx_0` USING BTREE (`deal_id`,`user_id`),
  KEY `idx_1` (`deal_id`,`user_id`,`status`),
  KEY `idx_2` (`id`,`transfer_amount`,`create_time`),
  KEY `idx_3` USING BTREE (`deal_id`,`status`,`t_user_id`),
  KEY `idx_dlt_001` (`create_date`),
  KEY `idx_dlt_002` (`transfer_date`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='债权转让';

-- ----------------------------
-- Records of fanwe_deal_load_transfer
-- ----------------------------

-- ----------------------------
-- Table structure for `%DB_PREFIX%deal_loan_type`
-- ----------------------------
DROP TABLE IF EXISTS `%DB_PREFIX%deal_loan_type`;
CREATE TABLE `%DB_PREFIX%deal_loan_type` (
  `id` int(11) NOT NULL auto_increment,
  `name` varchar(255) NOT NULL COMMENT '借款用途',
  `brief` text NOT NULL COMMENT '简介',
  `pid` int(11) NOT NULL COMMENT '父类ID',
  `is_delete` tinyint(1) NOT NULL COMMENT '是否删除',
  `is_effect` tinyint(1) NOT NULL COMMENT '是否有效',
  `sort` int(11) NOT NULL,
  `uname` varchar(255) NOT NULL,
  `icon` varchar(255) NOT NULL default '' COMMENT '分类icon',
  `applyto` varchar(255) NOT NULL COMMENT '适用人群',
  `condition` text NOT NULL COMMENT '申请条件',
  `credits` text NOT NULL COMMENT '必要申请资料',
  `is_quota` tinyint(1) NOT NULL COMMENT '额度限制  0否 1是',
  `content` text NOT NULL COMMENT '类型简介',
  PRIMARY KEY  (`id`),
  KEY `pid` (`pid`),
  KEY `sort` (`sort`)
) ENGINE=MyISAM AUTO_INCREMENT=11 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of fanwe_deal_loan_type
-- ----------------------------
INSERT INTO `%DB_PREFIX%deal_loan_type` VALUES ('1', '短期周转', '', '0', '0', '1', '10', '', './public/images/dealtype/dqzz.png', '', '', '', '1', '');
INSERT INTO `%DB_PREFIX%deal_loan_type` VALUES ('2', '购房借款', '', '0', '0', '1', '9', '', './public/images/dealtype/gf.png', '', '', '', '1', '');
INSERT INTO `%DB_PREFIX%deal_loan_type` VALUES ('3', '装修借款', '', '0', '0', '1', '8', '', './public/images/dealtype/zx.png', '', '', '', '1', '');
INSERT INTO `%DB_PREFIX%deal_loan_type` VALUES ('4', '个人消费', '', '0', '0', '1', '7', '', './public/images/dealtype/grxf.png', '', '', '', '1', '');
INSERT INTO `%DB_PREFIX%deal_loan_type` VALUES ('5', '婚礼筹备', '', '0', '0', '1', '6', '', './public/images/dealtype/hlcb.png', '', '', '', '1', '');
INSERT INTO `%DB_PREFIX%deal_loan_type` VALUES ('6', '教育培训', '', '0', '0', '1', '5', '', './public/images/dealtype/jypx.png', '', '', '', '1', '');
INSERT INTO `%DB_PREFIX%deal_loan_type` VALUES ('7', '汽车消费', '', '0', '0', '1', '4', '', './public/images/dealtype/qcxf.png', '', '', '', '1', '');
INSERT INTO `%DB_PREFIX%deal_loan_type` VALUES ('8', '投资创业', '', '0', '0', '1', '3', '', './public/images/dealtype/tzcy.png', '', '', '', '1', '');
INSERT INTO `%DB_PREFIX%deal_loan_type` VALUES ('9', '医疗支出', '', '0', '0', '1', '2', '', './public/images/dealtype/ylzc.png', '', '', '', '1', '');
INSERT INTO `%DB_PREFIX%deal_loan_type` VALUES ('10', '其他借款', '', '0', '0', '1', '1', '', './public/images/dealtype/other.png', '', '', '', '1', '');

-- ----------------------------
-- Table structure for `%DB_PREFIX%deal_msg_list`
-- ----------------------------
DROP TABLE IF EXISTS `%DB_PREFIX%deal_msg_list`;
CREATE TABLE `%DB_PREFIX%deal_msg_list` (
  `id` int(11) NOT NULL auto_increment,
  `dest` varchar(255) NOT NULL COMMENT '发送目标（邮件/手机号',
  `send_type` tinyint(1) NOT NULL COMMENT '发送类型 0:短信 1:邮件',
  `content` text NOT NULL COMMENT '发送的内容',
  `send_time` int(11) NOT NULL COMMENT '发出的时间',
  `is_send` tinyint(1) NOT NULL COMMENT '是否已发送 0:否 1:等待队列发送',
  `create_time` int(11) NOT NULL COMMENT '生成的时间',
  `user_id` int(11) NOT NULL COMMENT '会员ID',
  `result` text NOT NULL COMMENT '发送结果（如出错存放服务器或接口返回的错误信息）',
  `is_success` tinyint(1) NOT NULL COMMENT '是否发送成功',
  `is_html` tinyint(1) NOT NULL COMMENT '只针对邮件使用，是否为超文本邮件 0:否 1:是',
  `title` text NOT NULL COMMENT '只针对邮件使用 邮件的标题',
  `is_youhui` tinyint(1) NOT NULL,
  `youhui_id` int(11) NOT NULL,
  PRIMARY KEY  (`id`),
  KEY `idx0` (`id`,`is_send`,`send_type`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of fanwe_deal_msg_list
-- ----------------------------

-- ----------------------------
-- Table structure for `%DB_PREFIX%deal_order`
-- ----------------------------
DROP TABLE IF EXISTS `%DB_PREFIX%deal_order`;
CREATE TABLE `%DB_PREFIX%deal_order` (
  `id` int(11) NOT NULL auto_increment,
  `order_sn` varchar(255) NOT NULL COMMENT '订单编号',
  `type` tinyint(1) NOT NULL COMMENT '订单类型(0:借款订单 1:用户充值单)',
  `user_id` int(11) NOT NULL COMMENT '会员ID',
  `create_time` int(11) NOT NULL COMMENT '下单时间',
  `update_time` int(11) NOT NULL COMMENT '更新时间',
  `pay_status` tinyint(1) NOT NULL COMMENT '支付状态 0:未支付 1:部份付款 2:全部付款',
  `total_price` decimal(20,2) NOT NULL,
  `pay_amount` decimal(20,2) NOT NULL,
  `delivery_status` tinyint(1) NOT NULL COMMENT '发货状态 0:未发货 1:部份发货 2:全部发货 5:无需发货的订单',
  `order_status` tinyint(1) NOT NULL COMMENT '订单状态 0:开放状态（可操作不可删除） 1:结单（不可操作可删除）',
  `is_delete` tinyint(1) NOT NULL COMMENT '删除标识',
  `return_total_score` int(11) NOT NULL COMMENT '返给用户的总积分',
  `refund_amount` decimal(20,2) NOT NULL COMMENT '已退款总额',
  `admin_memo` text NOT NULL COMMENT '管理员的备注',
  `memo` text NOT NULL COMMENT '用户下单的备注',
  `region_lv1` int(11) NOT NULL COMMENT '配送地址一级地区ID',
  `region_lv2` int(11) NOT NULL COMMENT '配送地址二级地区ID',
  `region_lv3` int(11) NOT NULL COMMENT '配送地址三级地区ID',
  `region_lv4` int(11) NOT NULL COMMENT '配送地址四级地区ID',
  `address` text NOT NULL COMMENT '配送地址',
  `mobile` varchar(255) NOT NULL COMMENT '联系人手机',
  `zip` varchar(255) NOT NULL COMMENT '联系人邮编',
  `consignee` varchar(255) NOT NULL COMMENT '收货人姓名',
  `deal_total_price` decimal(20,2) NOT NULL,
  `discount_price` decimal(20,2) NOT NULL,
  `delivery_fee` decimal(20,2) NOT NULL,
  `ecv_money` decimal(20,2) NOT NULL,
  `account_money` decimal(20,2) NOT NULL,
  `delivery_id` int(11) NOT NULL COMMENT '配送方式',
  `payment_id` int(11) NOT NULL COMMENT '支付方式',
  `payment_fee` decimal(20,2) NOT NULL,
  `return_total_money` decimal(20,2) NOT NULL,
  `extra_status` tinyint(1) NOT NULL COMMENT '额外的订单标识 0:正常的订单 1.金额超额产生退款的订单（多次支付，重付通知） 2.发货失败退款（下单时库存足够，支付成功后库存不足，自动退款到用户的订单）',
  `after_sale` tinyint(1) NOT NULL COMMENT '售后处理标识 0:正常订单 1:退款处理的订单',
  `refund_money` decimal(20,2) NOT NULL COMMENT '弃用',
  `bank_id` varchar(255) NOT NULL COMMENT '银行直连支付的银行编号',
  `referer` varchar(255) NOT NULL COMMENT '订单的来路 url',
  `deal_ids` varchar(255) NOT NULL COMMENT '所投的借款ID，逗号分隔',
  `user_name` varchar(255) NOT NULL COMMENT '下单用户名',
  `refund_status` tinyint(1) NOT NULL COMMENT '0:不需退款 1:有退款申请 2:已处理',
  `retake_status` tinyint(1) NOT NULL COMMENT '弃用',
  PRIMARY KEY  (`id`),
  UNIQUE KEY `unique_sn` (`order_sn`),
  FULLTEXT KEY `deal_ids` (`deal_ids`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of fanwe_deal_order
-- ----------------------------

-- ----------------------------
-- Table structure for `%DB_PREFIX%deal_order_item`
-- ----------------------------
DROP TABLE IF EXISTS `%DB_PREFIX%deal_order_item`;
CREATE TABLE `%DB_PREFIX%deal_order_item` (
  `id` int(11) NOT NULL auto_increment,
  `deal_id` int(11) NOT NULL COMMENT '借款ID',
  `number` int(11) NOT NULL COMMENT '购买的数量',
  `unit_price` decimal(20,2) NOT NULL COMMENT '单价',
  `total_price` decimal(20,2) NOT NULL COMMENT '总价',
  `delivery_status` tinyint(1) NOT NULL COMMENT '发货状态 0:未发货 1:已发货',
  `name` text NOT NULL COMMENT '产品名称',
  `return_score` int(11) NOT NULL COMMENT '返积分单价',
  `return_total_score` int(11) NOT NULL COMMENT '返积分总价',
  `attr` varchar(255) NOT NULL COMMENT '属性ID，逗号分开',
  `verify_code` varchar(255) NOT NULL COMMENT '唯一标识码（产品ID+属性ID加密）',
  `order_id` int(11) NOT NULL COMMENT '所属的订单ID',
  `return_money` decimal(20,2) NOT NULL COMMENT '返现的单价',
  `return_total_money` decimal(20,2) NOT NULL,
  `buy_type` tinyint(1) NOT NULL COMMENT '购买的类型',
  `sub_name` varchar(255) NOT NULL COMMENT '短名称',
  `attr_str` text NOT NULL COMMENT '属性配置的字符串',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of fanwe_deal_order_item
-- ----------------------------

-- ----------------------------
-- Table structure for `%DB_PREFIX%deal_order_log`
-- ----------------------------
DROP TABLE IF EXISTS `%DB_PREFIX%deal_order_log`;
CREATE TABLE `%DB_PREFIX%deal_order_log` (
  `id` int(11) NOT NULL auto_increment,
  `log_info` text NOT NULL,
  `log_time` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of fanwe_deal_order_log
-- ----------------------------

-- ----------------------------
-- Table structure for `%DB_PREFIX%deal_payment`
-- ----------------------------
DROP TABLE IF EXISTS `%DB_PREFIX%deal_payment`;
CREATE TABLE `%DB_PREFIX%deal_payment` (
  `id` int(11) NOT NULL auto_increment,
  `deal_id` int(11) NOT NULL,
  `payment_id` int(11) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of fanwe_deal_payment
-- ----------------------------

-- ----------------------------
-- Table structure for `%DB_PREFIX%deal_quota_submit`
-- ----------------------------
DROP TABLE IF EXISTS `%DB_PREFIX%deal_quota_submit`;
CREATE TABLE `%DB_PREFIX%deal_quota_submit` (
  `id` int(11) NOT NULL auto_increment,
  `user_id` int(11) NOT NULL,
  `titlecolor` varchar(10) NOT NULL COMMENT '标题颜色',
  `name` varchar(255) NOT NULL COMMENT '贷款标题',
  `sub_name` varchar(255) NOT NULL COMMENT '短名称',
  `view_info` text NOT NULL COMMENT '资料展示',
  `citys` text NOT NULL COMMENT '城市（序列化）',
  `cate_id` int(11) NOT NULL COMMENT '贷款分类',
  `agency_id` int(11) NOT NULL COMMENT '担保机构',
  `warrant` tinyint(4) NOT NULL,
  `guarantor_margin_amt` decimal(20,2) NOT NULL COMMENT '担保保证金',
  `guarantor_amt` decimal(20,2) NOT NULL COMMENT '担保金额',
  `guarantor_pro_fit_amt` decimal(20,2) NOT NULL COMMENT '担保收益',
  `icon` varchar(255) NOT NULL COMMENT '图标',
  `type_id` int(11) NOT NULL COMMENT '借款用途',
  `borrow_amount` decimal(20,2) NOT NULL COMMENT '申请额度',
  `guarantees_amt` decimal(20,2) NOT NULL COMMENT '借款保证金',
  `services_fee` varchar(20) NOT NULL COMMENT '成交服务费',
  `manage_fee` varchar(20) NOT NULL COMMENT '借款者管理费',
  `user_loan_manage_fee` varchar(20) NOT NULL COMMENT '投资者管理费',
  `manage_impose_fee_day1` varchar(20) NOT NULL COMMENT '普通逾期管理费',
  `manage_impose_fee_day2` varchar(20) NOT NULL COMMENT '逾期管理费总额',
  `impose_fee_day1` varchar(20) NOT NULL COMMENT '普通逾期罚息',
  `impose_fee_day2` varchar(20) NOT NULL COMMENT '严重逾期罚息',
  `user_load_transfer_fee` varchar(20) NOT NULL COMMENT '债权转让管理费',
  `user_bid_rebate` varchar(20) NOT NULL COMMENT '投资人返利',
  `compensate_fee` varchar(20) NOT NULL COMMENT '提前还款补偿',
  `generation_position` varchar(20) NOT NULL COMMENT '申请延期比率',
  `description` text NOT NULL COMMENT '借款内容',
  `risk_rank` tinyint(4) NOT NULL COMMENT '风险等级',
  `risk_security` text NOT NULL COMMENT '风险控制',
  `is_effect` tinyint(1) NOT NULL COMMENT '默认是否有效',
  `status` tinyint(4) NOT NULL COMMENT '0待处理，1审核通过，2审核失败',
  `op_memo` text NOT NULL COMMENT '操作备注',
  `bad_msg` text NOT NULL COMMENT '失败原因',
  `create_time` int(11) NOT NULL COMMENT '添加时间',
  `update_time` int(11) NOT NULL COMMENT '处理时间',
  `contract_id` int(11) NOT NULL COMMENT '借款合同',
  `tcontract_id` int(11) NOT NULL COMMENT '转让合同',
  `user_loan_interest_manage_fee` varchar(20) NOT NULL COMMENT '投标者利息管理费',
  `score` int(11) NOT NULL COMMENT '借款者获得积分',
  `user_bid_score_fee` varchar(20) NOT NULL COMMENT '投资返还积分比率',
  `attachment` text NOT NULL COMMENT '合同附件',
  `tattachment` text NOT NULL COMMENT '转让合同附件',
  PRIMARY KEY  (`id`),
  KEY `idx0` (`user_id`,`status`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='授信额度申请';

-- ----------------------------
-- Records of fanwe_deal_quota_submit
-- ----------------------------

-- ----------------------------
-- Table structure for `%DB_PREFIX%deal_repay`
-- ----------------------------
DROP TABLE IF EXISTS `%DB_PREFIX%deal_repay`;
CREATE TABLE `%DB_PREFIX%deal_repay` (
  `id` int(11) NOT NULL auto_increment,
  `deal_id` int(11) NOT NULL COMMENT '借款ID',
  `user_id` int(11) NOT NULL COMMENT '借款人',
  `repay_money` decimal(20,2) NOT NULL COMMENT '还款金额',
  `manage_money` decimal(20,2) NOT NULL COMMENT '管理费',
  `impose_money` decimal(20,2) NOT NULL COMMENT '罚息',
  `repay_time` int(11) NOT NULL COMMENT '还的是第几期的',
  `true_repay_time` int(11) NOT NULL COMMENT '还款时间',
  `status` tinyint(1) NOT NULL COMMENT '0提前,1准时还款，2逾期还款 3严重逾期  前台在这基础上+1',
  `l_key` int(11) NOT NULL default '0' COMMENT '还款顺序 0 开始',
  `has_repay` tinyint(1) NOT NULL default '0' COMMENT '0未还,1已还 2部分还款',
  `manage_impose_money` decimal(20,2) NOT NULL COMMENT '逾期管理费',
  `is_site_bad` tinyint(1) NOT NULL COMMENT '是否坏账  0不是，1坏账 管理员看到的',
  `repay_date` date NOT NULL COMMENT '预期还款日期,日期格式方便统计',
  `true_repay_date` date NOT NULL COMMENT '实际还款日期,日期格式方便统计',
  `true_repay_money` double(20,2) NOT NULL default '0.00' COMMENT '实还金额',
  `true_self_money` decimal(20,2) NOT NULL COMMENT '实际还款本金',
  `interest_money` decimal(20,2) NOT NULL COMMENT '待还利息   repay_money - self_money',
  `true_interest_money` decimal(20,2) NOT NULL COMMENT '实际还利息',
  `true_manage_money` decimal(20,2) NOT NULL COMMENT '实际管理费',
  `self_money` decimal(20,2) NOT NULL default '0.00' COMMENT '需还本金',
  `loantype` int(11) NOT NULL COMMENT '还款方式',
  `manage_money_rebate` decimal(20,2) NOT NULL default '0.00' COMMENT '预计收到的：管理费返佣,满标放款时生成',
  `true_manage_money_rebate` decimal(20,2) NOT NULL default '0.00' COMMENT '实际收到的：管理费返佣,每期还款时生成',
  `get_manage` tinyint(1) NOT NULL COMMENT '是否已收取管理费',
  PRIMARY KEY  (`id`),
  KEY `idx_0` (`user_id`,`status`),
  KEY `l_key` (`l_key`),
  KEY `idx_1` (`deal_id`,`l_key`),
  KEY `idx_dr_001` (`true_repay_date`),
  KEY `idx_dr_002` (`repay_date`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of fanwe_deal_repay
-- ----------------------------

-- ----------------------------
-- Table structure for `%DB_PREFIX%deal_repay_log`
-- ----------------------------
DROP TABLE IF EXISTS `%DB_PREFIX%deal_repay_log`;
CREATE TABLE `%DB_PREFIX%deal_repay_log` (
  `id` int(11) NOT NULL auto_increment,
  `repay_id` int(11) NOT NULL COMMENT '账单ID',
  `log` text NOT NULL COMMENT '日志',
  `adm_id` int(11) NOT NULL COMMENT '操作管理员',
  `user_id` int(11) NOT NULL COMMENT '操作用户',
  `create_time` int(11) NOT NULL COMMENT '操作时间',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of fanwe_deal_repay_log
-- ----------------------------

-- ----------------------------
-- Table structure for `%DB_PREFIX%deal_show`
-- ----------------------------
DROP TABLE IF EXISTS `%DB_PREFIX%deal_show`;
CREATE TABLE `%DB_PREFIX%deal_show` (
  `id` int(11) NOT NULL auto_increment,
  `school` varchar(255) default NULL COMMENT '学校',
  `user_name` varchar(255) default NULL COMMENT '姓名',
  `amount` decimal(20,2) default NULL COMMENT '借款金额',
  `sort` int(11) default NULL COMMENT '排序',
  `deal_name` varchar(255) default NULL COMMENT '标名',
  `create_time` int(11) default NULL COMMENT '时间戳',
  `img` varchar(255) default NULL COMMENT '显示图片',
  `show_left` tinyint(1) default NULL COMMENT '左边显示 1：是 0:否',
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of fanwe_deal_show
-- ----------------------------

-- ----------------------------
-- Table structure for `%DB_PREFIX%debit_conf`
-- ----------------------------
DROP TABLE IF EXISTS `%DB_PREFIX%debit_conf`;
CREATE TABLE `%DB_PREFIX%debit_conf` (
  `borrow_amount_cfg` text NOT NULL COMMENT '借款金额 序列化',
  `loantype` int(11) NOT NULL COMMENT '还款类型',
  `services_fee` varchar(20) NOT NULL COMMENT '服务费率',
  `manage_fee` varchar(20) NOT NULL COMMENT '借款者管理费',
  `manage_impose_fee_day1` varchar(20) NOT NULL COMMENT '普通逾期管理费',
  `manage_impose_fee_day2` varchar(20) NOT NULL COMMENT '严重逾期管理费',
  `impose_fee_day1` varchar(20) NOT NULL COMMENT '普通逾期费率',
  `impose_fee_day2` varchar(20) NOT NULL COMMENT '严重逾期费率',
  `rate_cfg` tinyint(1) NOT NULL COMMENT '利率 0取最低 1取中间值 2取最高',
  `enddate` int(11) NOT NULL COMMENT '筹标日期',
  `first_relief` decimal(20,2) default NULL COMMENT '第一次减免费用'
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='白条配置表';

-- ----------------------------
-- Records of fanwe_debit_conf
-- ----------------------------

-- ----------------------------
-- Table structure for `%DB_PREFIX%delivery_region`
-- ----------------------------
DROP TABLE IF EXISTS `%DB_PREFIX%delivery_region`;
CREATE TABLE `%DB_PREFIX%delivery_region` (
  `id` int(11) NOT NULL auto_increment,
  `pid` int(11) NOT NULL,
  `name` varchar(50) NOT NULL COMMENT '地区名称',
  `region_level` tinyint(4) NOT NULL COMMENT '1:国 2:省 3:市(县) 4:区(镇)',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=3418 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of fanwe_delivery_region
-- ----------------------------
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2', '1', '北京', '2');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3', '1', '安徽', '2');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('4', '1', '福建', '2');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('5', '1', '甘肃', '2');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('6', '1', '广东', '2');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('7', '1', '广西', '2');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('8', '1', '贵州', '2');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('9', '1', '海南', '2');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('10', '1', '河北', '2');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('11', '1', '河南', '2');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('12', '1', '黑龙江', '2');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('13', '1', '湖北', '2');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('14', '1', '湖南', '2');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('15', '1', '吉林', '2');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('16', '1', '江苏', '2');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('17', '1', '江西', '2');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('18', '1', '辽宁', '2');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('19', '1', '内蒙古', '2');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('20', '1', '宁夏', '2');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('21', '1', '青海', '2');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('22', '1', '山东', '2');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('23', '1', '山西', '2');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('24', '1', '陕西', '2');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('25', '1', '上海', '2');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('26', '1', '四川', '2');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('27', '1', '天津', '2');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('28', '1', '西藏', '2');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('29', '1', '新疆', '2');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('30', '1', '云南', '2');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('31', '1', '浙江', '2');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('32', '1', '重庆', '2');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('33', '1', '香港', '2');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('34', '1', '澳门', '2');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('35', '1', '台湾', '2');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('36', '3', '安庆', '3');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('37', '3', '蚌埠', '3');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('38', '3', '巢湖', '3');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('39', '3', '池州', '3');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('40', '3', '滁州', '3');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('41', '3', '阜阳', '3');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('42', '3', '淮北', '3');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('43', '3', '淮南', '3');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('44', '3', '黄山', '3');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('45', '3', '六安', '3');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('46', '3', '马鞍山', '3');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('47', '3', '宿州', '3');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('48', '3', '铜陵', '3');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('49', '3', '芜湖', '3');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('50', '3', '宣城', '3');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('51', '3', '亳州', '3');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('52', '2', '北京', '3');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('53', '4', '福州', '3');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('54', '4', '龙岩', '3');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('55', '4', '南平', '3');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('56', '4', '宁德', '3');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('57', '4', '莆田', '3');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('58', '4', '泉州', '3');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('59', '4', '三明', '3');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('60', '4', '厦门', '3');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('61', '4', '漳州', '3');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('62', '5', '兰州', '3');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('63', '5', '白银', '3');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('64', '5', '定西', '3');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('65', '5', '甘南', '3');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('66', '5', '嘉峪关', '3');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('67', '5', '金昌', '3');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('68', '5', '酒泉', '3');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('69', '5', '临夏', '3');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('70', '5', '陇南', '3');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('71', '5', '平凉', '3');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('72', '5', '庆阳', '3');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('73', '5', '天水', '3');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('74', '5', '武威', '3');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('75', '5', '张掖', '3');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('76', '6', '广州', '3');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('77', '6', '深圳', '3');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('78', '6', '潮州', '3');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('79', '6', '东莞', '3');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('80', '6', '佛山', '3');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('81', '6', '河源', '3');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('82', '6', '惠州', '3');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('83', '6', '江门', '3');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('84', '6', '揭阳', '3');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('85', '6', '茂名', '3');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('86', '6', '梅州', '3');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('87', '6', '清远', '3');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('88', '6', '汕头', '3');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('89', '6', '汕尾', '3');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('90', '6', '韶关', '3');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('91', '6', '阳江', '3');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('92', '6', '云浮', '3');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('93', '6', '湛江', '3');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('94', '6', '肇庆', '3');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('95', '6', '中山', '3');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('96', '6', '珠海', '3');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('97', '7', '南宁', '3');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('98', '7', '桂林', '3');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('99', '7', '百色', '3');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('100', '7', '北海', '3');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('101', '7', '崇左', '3');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('102', '7', '防城港', '3');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('103', '7', '贵港', '3');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('104', '7', '河池', '3');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('105', '7', '贺州', '3');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('106', '7', '来宾', '3');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('107', '7', '柳州', '3');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('108', '7', '钦州', '3');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('109', '7', '梧州', '3');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('110', '7', '玉林', '3');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('111', '8', '贵阳', '3');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('112', '8', '安顺', '3');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('113', '8', '毕节', '3');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('114', '8', '六盘水', '3');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('115', '8', '黔东南', '3');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('116', '8', '黔南', '3');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('117', '8', '黔西南', '3');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('118', '8', '铜仁', '3');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('119', '8', '遵义', '3');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('120', '9', '海口', '3');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('121', '9', '三亚', '3');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('122', '9', '白沙', '3');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('123', '9', '保亭', '3');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('124', '9', '昌江', '3');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('125', '9', '澄迈县', '3');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('126', '9', '定安县', '3');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('127', '9', '东方', '3');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('128', '9', '乐东', '3');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('129', '9', '临高县', '3');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('130', '9', '陵水', '3');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('131', '9', '琼海', '3');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('132', '9', '琼中', '3');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('133', '9', '屯昌县', '3');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('134', '9', '万宁', '3');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('135', '9', '文昌', '3');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('136', '9', '五指山', '3');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('137', '9', '儋州', '3');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('138', '10', '石家庄', '3');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('139', '10', '保定', '3');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('140', '10', '沧州', '3');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('141', '10', '承德', '3');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('142', '10', '邯郸', '3');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('143', '10', '衡水', '3');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('144', '10', '廊坊', '3');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('145', '10', '秦皇岛', '3');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('146', '10', '唐山', '3');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('147', '10', '邢台', '3');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('148', '10', '张家口', '3');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('149', '11', '郑州', '3');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('150', '11', '洛阳', '3');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('151', '11', '开封', '3');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('152', '11', '安阳', '3');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('153', '11', '鹤壁', '3');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('154', '11', '济源', '3');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('155', '11', '焦作', '3');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('156', '11', '南阳', '3');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('157', '11', '平顶山', '3');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('158', '11', '三门峡', '3');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('159', '11', '商丘', '3');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('160', '11', '新乡', '3');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('161', '11', '信阳', '3');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('162', '11', '许昌', '3');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('163', '11', '周口', '3');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('164', '11', '驻马店', '3');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('165', '11', '漯河', '3');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('166', '11', '濮阳', '3');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('167', '12', '哈尔滨', '3');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('168', '12', '大庆', '3');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('169', '12', '大兴安岭', '3');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('170', '12', '鹤岗', '3');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('171', '12', '黑河', '3');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('172', '12', '鸡西', '3');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('173', '12', '佳木斯', '3');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('174', '12', '牡丹江', '3');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('175', '12', '七台河', '3');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('176', '12', '齐齐哈尔', '3');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('177', '12', '双鸭山', '3');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('178', '12', '绥化', '3');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('179', '12', '伊春', '3');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('180', '13', '武汉', '3');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('181', '13', '仙桃', '3');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('182', '13', '鄂州', '3');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('183', '13', '黄冈', '3');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('184', '13', '黄石', '3');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('185', '13', '荆门', '3');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('186', '13', '荆州', '3');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('187', '13', '潜江', '3');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('188', '13', '神农架林区', '3');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('189', '13', '十堰', '3');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('190', '13', '随州', '3');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('191', '13', '天门', '3');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('192', '13', '咸宁', '3');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('193', '13', '襄樊', '3');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('194', '13', '孝感', '3');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('195', '13', '宜昌', '3');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('196', '13', '恩施', '3');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('197', '14', '长沙', '3');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('198', '14', '张家界', '3');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('199', '14', '常德', '3');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('200', '14', '郴州', '3');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('201', '14', '衡阳', '3');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('202', '14', '怀化', '3');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('203', '14', '娄底', '3');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('204', '14', '邵阳', '3');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('205', '14', '湘潭', '3');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('206', '14', '湘西', '3');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('207', '14', '益阳', '3');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('208', '14', '永州', '3');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('209', '14', '岳阳', '3');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('210', '14', '株洲', '3');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('211', '15', '长春', '3');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('212', '15', '吉林', '3');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('213', '15', '白城', '3');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('214', '15', '白山', '3');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('215', '15', '辽源', '3');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('216', '15', '四平', '3');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('217', '15', '松原', '3');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('218', '15', '通化', '3');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('219', '15', '延边', '3');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('220', '16', '南京', '3');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('221', '16', '苏州', '3');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('222', '16', '无锡', '3');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('223', '16', '常州', '3');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('224', '16', '淮安', '3');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('225', '16', '连云港', '3');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('226', '16', '南通', '3');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('227', '16', '宿迁', '3');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('228', '16', '泰州', '3');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('229', '16', '徐州', '3');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('230', '16', '盐城', '3');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('231', '16', '扬州', '3');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('232', '16', '镇江', '3');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('233', '17', '南昌', '3');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('234', '17', '抚州', '3');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('235', '17', '赣州', '3');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('236', '17', '吉安', '3');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('237', '17', '景德镇', '3');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('238', '17', '九江', '3');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('239', '17', '萍乡', '3');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('240', '17', '上饶', '3');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('241', '17', '新余', '3');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('242', '17', '宜春', '3');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('243', '17', '鹰潭', '3');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('244', '18', '沈阳', '3');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('245', '18', '大连', '3');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('246', '18', '鞍山', '3');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('247', '18', '本溪', '3');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('248', '18', '朝阳', '3');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('249', '18', '丹东', '3');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('250', '18', '抚顺', '3');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('251', '18', '阜新', '3');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('252', '18', '葫芦岛', '3');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('253', '18', '锦州', '3');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('254', '18', '辽阳', '3');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('255', '18', '盘锦', '3');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('256', '18', '铁岭', '3');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('257', '18', '营口', '3');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('258', '19', '呼和浩特', '3');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('259', '19', '阿拉善盟', '3');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('260', '19', '巴彦淖尔盟', '3');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('261', '19', '包头', '3');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('262', '19', '赤峰', '3');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('263', '19', '鄂尔多斯', '3');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('264', '19', '呼伦贝尔', '3');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('265', '19', '通辽', '3');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('266', '19', '乌海', '3');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('267', '19', '乌兰察布市', '3');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('268', '19', '锡林郭勒盟', '3');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('269', '19', '兴安盟', '3');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('270', '20', '银川', '3');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('271', '20', '固原', '3');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('272', '20', '石嘴山', '3');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('273', '20', '吴忠', '3');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('274', '20', '中卫', '3');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('275', '21', '西宁', '3');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('276', '21', '果洛', '3');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('277', '21', '海北', '3');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('278', '21', '海东', '3');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('279', '21', '海南', '3');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('280', '21', '海西', '3');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('281', '21', '黄南', '3');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('282', '21', '玉树', '3');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('283', '22', '济南', '3');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('284', '22', '青岛', '3');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('285', '22', '滨州', '3');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('286', '22', '德州', '3');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('287', '22', '东营', '3');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('288', '22', '菏泽', '3');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('289', '22', '济宁', '3');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('290', '22', '莱芜', '3');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('291', '22', '聊城', '3');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('292', '22', '临沂', '3');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('293', '22', '日照', '3');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('294', '22', '泰安', '3');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('295', '22', '威海', '3');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('296', '22', '潍坊', '3');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('297', '22', '烟台', '3');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('298', '22', '枣庄', '3');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('299', '22', '淄博', '3');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('300', '23', '太原', '3');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('301', '23', '长治', '3');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('302', '23', '大同', '3');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('303', '23', '晋城', '3');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('304', '23', '晋中', '3');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('305', '23', '临汾', '3');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('306', '23', '吕梁', '3');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('307', '23', '朔州', '3');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('308', '23', '忻州', '3');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('309', '23', '阳泉', '3');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('310', '23', '运城', '3');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('311', '24', '西安', '3');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('312', '24', '安康', '3');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('313', '24', '宝鸡', '3');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('314', '24', '汉中', '3');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('315', '24', '商洛', '3');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('316', '24', '铜川', '3');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('317', '24', '渭南', '3');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('318', '24', '咸阳', '3');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('319', '24', '延安', '3');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('320', '24', '榆林', '3');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('321', '25', '上海', '3');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('322', '26', '成都', '3');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('323', '26', '绵阳', '3');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('324', '26', '阿坝', '3');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('325', '26', '巴中', '3');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('326', '26', '达州', '3');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('327', '26', '德阳', '3');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('328', '26', '甘孜', '3');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('329', '26', '广安', '3');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('330', '26', '广元', '3');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('331', '26', '乐山', '3');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('332', '26', '凉山', '3');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('333', '26', '眉山', '3');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('334', '26', '南充', '3');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('335', '26', '内江', '3');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('336', '26', '攀枝花', '3');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('337', '26', '遂宁', '3');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('338', '26', '雅安', '3');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('339', '26', '宜宾', '3');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('340', '26', '资阳', '3');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('341', '26', '自贡', '3');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('342', '26', '泸州', '3');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('343', '27', '天津', '3');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('344', '28', '拉萨', '3');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('345', '28', '阿里', '3');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('346', '28', '昌都', '3');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('347', '28', '林芝', '3');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('348', '28', '那曲', '3');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('349', '28', '日喀则', '3');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('350', '28', '山南', '3');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('351', '29', '乌鲁木齐', '3');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('352', '29', '阿克苏', '3');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('353', '29', '阿拉尔', '3');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('354', '29', '巴音郭楞', '3');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('355', '29', '博尔塔拉', '3');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('356', '29', '昌吉', '3');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('357', '29', '哈密', '3');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('358', '29', '和田', '3');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('359', '29', '喀什', '3');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('360', '29', '克拉玛依', '3');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('361', '29', '克孜勒苏', '3');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('362', '29', '石河子', '3');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('363', '29', '图木舒克', '3');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('364', '29', '吐鲁番', '3');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('365', '29', '五家渠', '3');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('366', '29', '伊犁', '3');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('367', '30', '昆明', '3');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('368', '30', '怒江', '3');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('369', '30', '普洱', '3');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('370', '30', '丽江', '3');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('371', '30', '保山', '3');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('372', '30', '楚雄', '3');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('373', '30', '大理', '3');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('374', '30', '德宏', '3');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('375', '30', '迪庆', '3');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('376', '30', '红河', '3');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('377', '30', '临沧', '3');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('378', '30', '曲靖', '3');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('379', '30', '文山', '3');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('380', '30', '西双版纳', '3');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('381', '30', '玉溪', '3');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('382', '30', '昭通', '3');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('383', '31', '杭州', '3');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('384', '31', '湖州', '3');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('385', '31', '嘉兴', '3');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('386', '31', '金华', '3');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('387', '31', '丽水', '3');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('388', '31', '宁波', '3');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('389', '31', '绍兴', '3');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('390', '31', '台州', '3');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('391', '31', '温州', '3');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('392', '31', '舟山', '3');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('393', '31', '衢州', '3');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('394', '32', '重庆', '3');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('395', '33', '香港', '3');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('396', '34', '澳门', '3');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('397', '35', '台湾', '3');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('398', '36', '迎江区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('399', '36', '大观区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('400', '36', '宜秀区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('401', '36', '桐城市', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('402', '36', '怀宁县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('403', '36', '枞阳县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('404', '36', '潜山县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('405', '36', '太湖县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('406', '36', '宿松县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('407', '36', '望江县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('408', '36', '岳西县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('409', '37', '中市区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('410', '37', '东市区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('411', '37', '西市区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('412', '37', '郊区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('413', '37', '怀远县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('414', '37', '五河县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('415', '37', '固镇县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('416', '38', '居巢区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('417', '38', '庐江县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('418', '38', '无为县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('419', '38', '含山县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('420', '38', '和县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('421', '39', '贵池区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('422', '39', '东至县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('423', '39', '石台县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('424', '39', '青阳县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('425', '40', '琅琊区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('426', '40', '南谯区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('427', '40', '天长市', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('428', '40', '明光市', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('429', '40', '来安县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('430', '40', '全椒县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('431', '40', '定远县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('432', '40', '凤阳县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('433', '41', '蚌山区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('434', '41', '龙子湖区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('435', '41', '禹会区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('436', '41', '淮上区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('437', '41', '颍州区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('438', '41', '颍东区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('439', '41', '颍泉区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('440', '41', '界首市', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('441', '41', '临泉县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('442', '41', '太和县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('443', '41', '阜南县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('444', '41', '颖上县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('445', '42', '相山区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('446', '42', '杜集区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('447', '42', '烈山区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('448', '42', '濉溪县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('449', '43', '田家庵区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('450', '43', '大通区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('451', '43', '谢家集区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('452', '43', '八公山区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('453', '43', '潘集区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('454', '43', '凤台县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('455', '44', '屯溪区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('456', '44', '黄山区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('457', '44', '徽州区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('458', '44', '歙县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('459', '44', '休宁县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('460', '44', '黟县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('461', '44', '祁门县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('462', '45', '金安区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('463', '45', '裕安区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('464', '45', '寿县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('465', '45', '霍邱县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('466', '45', '舒城县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('467', '45', '金寨县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('468', '45', '霍山县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('469', '46', '雨山区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('470', '46', '花山区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('471', '46', '金家庄区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('472', '46', '当涂县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('473', '47', '埇桥区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('474', '47', '砀山县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('475', '47', '萧县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('476', '47', '灵璧县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('477', '47', '泗县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('478', '48', '铜官山区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('479', '48', '狮子山区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('480', '48', '郊区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('481', '48', '铜陵县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('482', '49', '镜湖区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('483', '49', '弋江区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('484', '49', '鸠江区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('485', '49', '三山区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('486', '49', '芜湖县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('487', '49', '繁昌县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('488', '49', '南陵县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('489', '50', '宣州区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('490', '50', '宁国市', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('491', '50', '郎溪县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('492', '50', '广德县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('493', '50', '泾县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('494', '50', '绩溪县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('495', '50', '旌德县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('496', '51', '涡阳县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('497', '51', '蒙城县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('498', '51', '利辛县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('499', '51', '谯城区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('500', '52', '东城区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('501', '52', '西城区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('502', '52', '海淀区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('503', '52', '朝阳区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('504', '52', '崇文区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('505', '52', '宣武区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('506', '52', '丰台区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('507', '52', '石景山区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('508', '52', '房山区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('509', '52', '门头沟区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('510', '52', '通州区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('511', '52', '顺义区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('512', '52', '昌平区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('513', '52', '怀柔区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('514', '52', '平谷区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('515', '52', '大兴区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('516', '52', '密云县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('517', '52', '延庆县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('518', '53', '鼓楼区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('519', '53', '台江区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('520', '53', '仓山区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('521', '53', '马尾区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('522', '53', '晋安区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('523', '53', '福清市', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('524', '53', '长乐市', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('525', '53', '闽侯县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('526', '53', '连江县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('527', '53', '罗源县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('528', '53', '闽清县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('529', '53', '永泰县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('530', '53', '平潭县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('531', '54', '新罗区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('532', '54', '漳平市', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('533', '54', '长汀县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('534', '54', '永定县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('535', '54', '上杭县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('536', '54', '武平县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('537', '54', '连城县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('538', '55', '延平区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('539', '55', '邵武市', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('540', '55', '武夷山市', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('541', '55', '建瓯市', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('542', '55', '建阳市', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('543', '55', '顺昌县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('544', '55', '浦城县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('545', '55', '光泽县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('546', '55', '松溪县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('547', '55', '政和县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('548', '56', '蕉城区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('549', '56', '福安市', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('550', '56', '福鼎市', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('551', '56', '霞浦县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('552', '56', '古田县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('553', '56', '屏南县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('554', '56', '寿宁县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('555', '56', '周宁县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('556', '56', '柘荣县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('557', '57', '城厢区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('558', '57', '涵江区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('559', '57', '荔城区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('560', '57', '秀屿区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('561', '57', '仙游县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('562', '58', '鲤城区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('563', '58', '丰泽区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('564', '58', '洛江区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('565', '58', '清濛开发区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('566', '58', '泉港区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('567', '58', '石狮市', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('568', '58', '晋江市', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('569', '58', '南安市', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('570', '58', '惠安县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('571', '58', '安溪县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('572', '58', '永春县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('573', '58', '德化县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('574', '58', '金门县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('575', '59', '梅列区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('576', '59', '三元区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('577', '59', '永安市', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('578', '59', '明溪县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('579', '59', '清流县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('580', '59', '宁化县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('581', '59', '大田县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('582', '59', '尤溪县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('583', '59', '沙县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('584', '59', '将乐县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('585', '59', '泰宁县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('586', '59', '建宁县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('587', '60', '思明区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('588', '60', '海沧区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('589', '60', '湖里区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('590', '60', '集美区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('591', '60', '同安区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('592', '60', '翔安区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('593', '61', '芗城区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('594', '61', '龙文区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('595', '61', '龙海市', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('596', '61', '云霄县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('597', '61', '漳浦县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('598', '61', '诏安县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('599', '61', '长泰县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('600', '61', '东山县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('601', '61', '南靖县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('602', '61', '平和县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('603', '61', '华安县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('604', '62', '皋兰县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('605', '62', '城关区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('606', '62', '七里河区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('607', '62', '西固区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('608', '62', '安宁区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('609', '62', '红古区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('610', '62', '永登县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('611', '62', '榆中县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('612', '63', '白银区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('613', '63', '平川区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('614', '63', '会宁县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('615', '63', '景泰县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('616', '63', '靖远县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('617', '64', '临洮县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('618', '64', '陇西县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('619', '64', '通渭县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('620', '64', '渭源县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('621', '64', '漳县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('622', '64', '岷县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('623', '64', '安定区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('624', '64', '安定区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('625', '65', '合作市', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('626', '65', '临潭县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('627', '65', '卓尼县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('628', '65', '舟曲县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('629', '65', '迭部县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('630', '65', '玛曲县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('631', '65', '碌曲县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('632', '65', '夏河县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('633', '66', '嘉峪关市', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('634', '67', '金川区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('635', '67', '永昌县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('636', '68', '肃州区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('637', '68', '玉门市', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('638', '68', '敦煌市', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('639', '68', '金塔县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('640', '68', '瓜州县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('641', '68', '肃北', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('642', '68', '阿克塞', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('643', '69', '临夏市', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('644', '69', '临夏县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('645', '69', '康乐县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('646', '69', '永靖县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('647', '69', '广河县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('648', '69', '和政县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('649', '69', '东乡族自治县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('650', '69', '积石山', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('651', '70', '成县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('652', '70', '徽县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('653', '70', '康县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('654', '70', '礼县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('655', '70', '两当县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('656', '70', '文县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('657', '70', '西和县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('658', '70', '宕昌县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('659', '70', '武都区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('660', '71', '崇信县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('661', '71', '华亭县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('662', '71', '静宁县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('663', '71', '灵台县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('664', '71', '崆峒区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('665', '71', '庄浪县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('666', '71', '泾川县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('667', '72', '合水县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('668', '72', '华池县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('669', '72', '环县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('670', '72', '宁县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('671', '72', '庆城县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('672', '72', '西峰区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('673', '72', '镇原县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('674', '72', '正宁县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('675', '73', '甘谷县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('676', '73', '秦安县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('677', '73', '清水县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('678', '73', '秦州区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('679', '73', '麦积区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('680', '73', '武山县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('681', '73', '张家川', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('682', '74', '古浪县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('683', '74', '民勤县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('684', '74', '天祝', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('685', '74', '凉州区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('686', '75', '高台县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('687', '75', '临泽县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('688', '75', '民乐县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('689', '75', '山丹县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('690', '75', '肃南', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('691', '75', '甘州区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('692', '76', '从化市', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('693', '76', '天河区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('694', '76', '东山区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('695', '76', '白云区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('696', '76', '海珠区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('697', '76', '荔湾区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('698', '76', '越秀区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('699', '76', '黄埔区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('700', '76', '番禺区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('701', '76', '花都区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('702', '76', '增城区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('703', '76', '从化区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('704', '76', '市郊', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('705', '77', '福田区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('706', '77', '罗湖区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('707', '77', '南山区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('708', '77', '宝安区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('709', '77', '龙岗区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('710', '77', '盐田区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('711', '78', '湘桥区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('712', '78', '潮安县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('713', '78', '饶平县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('714', '79', '南城区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('715', '79', '东城区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('716', '79', '万江区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('717', '79', '莞城区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('718', '79', '石龙镇', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('719', '79', '虎门镇', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('720', '79', '麻涌镇', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('721', '79', '道滘镇', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('722', '79', '石碣镇', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('723', '79', '沙田镇', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('724', '79', '望牛墩镇', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('725', '79', '洪梅镇', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('726', '79', '茶山镇', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('727', '79', '寮步镇', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('728', '79', '大岭山镇', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('729', '79', '大朗镇', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('730', '79', '黄江镇', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('731', '79', '樟木头', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('732', '79', '凤岗镇', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('733', '79', '塘厦镇', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('734', '79', '谢岗镇', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('735', '79', '厚街镇', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('736', '79', '清溪镇', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('737', '79', '常平镇', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('738', '79', '桥头镇', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('739', '79', '横沥镇', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('740', '79', '东坑镇', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('741', '79', '企石镇', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('742', '79', '石排镇', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('743', '79', '长安镇', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('744', '79', '中堂镇', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('745', '79', '高埗镇', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('746', '80', '禅城区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('747', '80', '南海区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('748', '80', '顺德区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('749', '80', '三水区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('750', '80', '高明区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('751', '81', '东源县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('752', '81', '和平县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('753', '81', '源城区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('754', '81', '连平县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('755', '81', '龙川县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('756', '81', '紫金县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('757', '82', '惠阳区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('758', '82', '惠城区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('759', '82', '大亚湾', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('760', '82', '博罗县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('761', '82', '惠东县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('762', '82', '龙门县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('763', '83', '江海区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('764', '83', '蓬江区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('765', '83', '新会区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('766', '83', '台山市', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('767', '83', '开平市', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('768', '83', '鹤山市', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('769', '83', '恩平市', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('770', '84', '榕城区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('771', '84', '普宁市', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('772', '84', '揭东县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('773', '84', '揭西县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('774', '84', '惠来县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('775', '85', '茂南区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('776', '85', '茂港区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('777', '85', '高州市', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('778', '85', '化州市', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('779', '85', '信宜市', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('780', '85', '电白县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('781', '86', '梅县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('782', '86', '梅江区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('783', '86', '兴宁市', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('784', '86', '大埔县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('785', '86', '丰顺县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('786', '86', '五华县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('787', '86', '平远县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('788', '86', '蕉岭县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('789', '87', '清城区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('790', '87', '英德市', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('791', '87', '连州市', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('792', '87', '佛冈县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('793', '87', '阳山县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('794', '87', '清新县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('795', '87', '连山', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('796', '87', '连南', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('797', '88', '南澳县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('798', '88', '潮阳区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('799', '88', '澄海区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('800', '88', '龙湖区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('801', '88', '金平区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('802', '88', '濠江区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('803', '88', '潮南区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('804', '89', '城区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('805', '89', '陆丰市', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('806', '89', '海丰县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('807', '89', '陆河县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('808', '90', '曲江县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('809', '90', '浈江区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('810', '90', '武江区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('811', '90', '曲江区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('812', '90', '乐昌市', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('813', '90', '南雄市', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('814', '90', '始兴县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('815', '90', '仁化县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('816', '90', '翁源县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('817', '90', '新丰县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('818', '90', '乳源', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('819', '91', '江城区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('820', '91', '阳春市', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('821', '91', '阳西县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('822', '91', '阳东县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('823', '92', '云城区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('824', '92', '罗定市', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('825', '92', '新兴县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('826', '92', '郁南县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('827', '92', '云安县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('828', '93', '赤坎区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('829', '93', '霞山区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('830', '93', '坡头区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('831', '93', '麻章区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('832', '93', '廉江市', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('833', '93', '雷州市', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('834', '93', '吴川市', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('835', '93', '遂溪县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('836', '93', '徐闻县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('837', '94', '肇庆市', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('838', '94', '高要市', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('839', '94', '四会市', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('840', '94', '广宁县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('841', '94', '怀集县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('842', '94', '封开县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('843', '94', '德庆县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('844', '95', '石岐街道', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('845', '95', '东区街道', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('846', '95', '西区街道', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('847', '95', '环城街道', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('848', '95', '中山港街道', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('849', '95', '五桂山街道', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('850', '96', '香洲区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('851', '96', '斗门区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('852', '96', '金湾区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('853', '97', '邕宁区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('854', '97', '青秀区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('855', '97', '兴宁区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('856', '97', '良庆区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('857', '97', '西乡塘区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('858', '97', '江南区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('859', '97', '武鸣县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('860', '97', '隆安县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('861', '97', '马山县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('862', '97', '上林县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('863', '97', '宾阳县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('864', '97', '横县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('865', '98', '秀峰区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('866', '98', '叠彩区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('867', '98', '象山区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('868', '98', '七星区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('869', '98', '雁山区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('870', '98', '阳朔县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('871', '98', '临桂县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('872', '98', '灵川县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('873', '98', '全州县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('874', '98', '平乐县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('875', '98', '兴安县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('876', '98', '灌阳县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('877', '98', '荔浦县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('878', '98', '资源县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('879', '98', '永福县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('880', '98', '龙胜', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('881', '98', '恭城', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('882', '99', '右江区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('883', '99', '凌云县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('884', '99', '平果县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('885', '99', '西林县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('886', '99', '乐业县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('887', '99', '德保县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('888', '99', '田林县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('889', '99', '田阳县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('890', '99', '靖西县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('891', '99', '田东县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('892', '99', '那坡县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('893', '99', '隆林', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('894', '100', '海城区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('895', '100', '银海区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('896', '100', '铁山港区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('897', '100', '合浦县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('898', '101', '江州区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('899', '101', '凭祥市', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('900', '101', '宁明县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('901', '101', '扶绥县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('902', '101', '龙州县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('903', '101', '大新县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('904', '101', '天等县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('905', '102', '港口区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('906', '102', '防城区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('907', '102', '东兴市', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('908', '102', '上思县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('909', '103', '港北区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('910', '103', '港南区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('911', '103', '覃塘区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('912', '103', '桂平市', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('913', '103', '平南县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('914', '104', '金城江区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('915', '104', '宜州市', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('916', '104', '天峨县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('917', '104', '凤山县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('918', '104', '南丹县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('919', '104', '东兰县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('920', '104', '都安', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('921', '104', '罗城', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('922', '104', '巴马', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('923', '104', '环江', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('924', '104', '大化', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('925', '105', '八步区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('926', '105', '钟山县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('927', '105', '昭平县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('928', '105', '富川', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('929', '106', '兴宾区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('930', '106', '合山市', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('931', '106', '象州县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('932', '106', '武宣县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('933', '106', '忻城县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('934', '106', '金秀', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('935', '107', '城中区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('936', '107', '鱼峰区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('937', '107', '柳北区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('938', '107', '柳南区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('939', '107', '柳江县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('940', '107', '柳城县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('941', '107', '鹿寨县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('942', '107', '融安县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('943', '107', '融水', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('944', '107', '三江', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('945', '108', '钦南区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('946', '108', '钦北区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('947', '108', '灵山县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('948', '108', '浦北县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('949', '109', '万秀区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('950', '109', '蝶山区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('951', '109', '长洲区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('952', '109', '岑溪市', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('953', '109', '苍梧县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('954', '109', '藤县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('955', '109', '蒙山县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('956', '110', '玉州区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('957', '110', '北流市', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('958', '110', '容县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('959', '110', '陆川县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('960', '110', '博白县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('961', '110', '兴业县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('962', '111', '南明区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('963', '111', '云岩区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('964', '111', '花溪区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('965', '111', '乌当区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('966', '111', '白云区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('967', '111', '小河区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('968', '111', '金阳新区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('969', '111', '新天园区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('970', '111', '清镇市', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('971', '111', '开阳县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('972', '111', '修文县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('973', '111', '息烽县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('974', '112', '西秀区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('975', '112', '关岭', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('976', '112', '镇宁', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('977', '112', '紫云', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('978', '112', '平坝县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('979', '112', '普定县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('980', '113', '毕节市', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('981', '113', '大方县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('982', '113', '黔西县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('983', '113', '金沙县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('984', '113', '织金县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('985', '113', '纳雍县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('986', '113', '赫章县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('987', '113', '威宁', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('988', '114', '钟山区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('989', '114', '六枝特区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('990', '114', '水城县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('991', '114', '盘县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('992', '115', '凯里市', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('993', '115', '黄平县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('994', '115', '施秉县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('995', '115', '三穗县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('996', '115', '镇远县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('997', '115', '岑巩县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('998', '115', '天柱县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('999', '115', '锦屏县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1000', '115', '剑河县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1001', '115', '台江县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1002', '115', '黎平县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1003', '115', '榕江县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1004', '115', '从江县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1005', '115', '雷山县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1006', '115', '麻江县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1007', '115', '丹寨县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1008', '116', '都匀市', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1009', '116', '福泉市', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1010', '116', '荔波县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1011', '116', '贵定县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1012', '116', '瓮安县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1013', '116', '独山县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1014', '116', '平塘县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1015', '116', '罗甸县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1016', '116', '长顺县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1017', '116', '龙里县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1018', '116', '惠水县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1019', '116', '三都', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1020', '117', '兴义市', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1021', '117', '兴仁县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1022', '117', '普安县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1023', '117', '晴隆县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1024', '117', '贞丰县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1025', '117', '望谟县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1026', '117', '册亨县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1027', '117', '安龙县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1028', '118', '铜仁市', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1029', '118', '江口县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1030', '118', '石阡县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1031', '118', '思南县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1032', '118', '德江县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1033', '118', '玉屏', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1034', '118', '印江', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1035', '118', '沿河', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1036', '118', '松桃', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1037', '118', '万山特区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1038', '119', '红花岗区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1039', '119', '务川县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1040', '119', '道真县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1041', '119', '汇川区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1042', '119', '赤水市', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1043', '119', '仁怀市', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1044', '119', '遵义县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1045', '119', '桐梓县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1046', '119', '绥阳县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1047', '119', '正安县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1048', '119', '凤冈县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1049', '119', '湄潭县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1050', '119', '余庆县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1051', '119', '习水县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1052', '119', '道真', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1053', '119', '务川', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1054', '120', '秀英区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1055', '120', '龙华区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1056', '120', '琼山区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1057', '120', '美兰区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1058', '137', '市区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1059', '137', '洋浦开发区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1060', '137', '那大镇', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1061', '137', '王五镇', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1062', '137', '雅星镇', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1063', '137', '大成镇', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1064', '137', '中和镇', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1065', '137', '峨蔓镇', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1066', '137', '南丰镇', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1067', '137', '白马井镇', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1068', '137', '兰洋镇', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1069', '137', '和庆镇', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1070', '137', '海头镇', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1071', '137', '排浦镇', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1072', '137', '东成镇', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1073', '137', '光村镇', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1074', '137', '木棠镇', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1075', '137', '新州镇', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1076', '137', '三都镇', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1077', '137', '其他', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1078', '138', '长安区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1079', '138', '桥东区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1080', '138', '桥西区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1081', '138', '新华区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1082', '138', '裕华区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1083', '138', '井陉矿区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1084', '138', '高新区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1085', '138', '辛集市', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1086', '138', '藁城市', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1087', '138', '晋州市', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1088', '138', '新乐市', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1089', '138', '鹿泉市', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1090', '138', '井陉县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1091', '138', '正定县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1092', '138', '栾城县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1093', '138', '行唐县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1094', '138', '灵寿县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1095', '138', '高邑县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1096', '138', '深泽县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1097', '138', '赞皇县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1098', '138', '无极县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1099', '138', '平山县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1100', '138', '元氏县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1101', '138', '赵县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1102', '139', '新市区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1103', '139', '南市区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1104', '139', '北市区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1105', '139', '涿州市', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1106', '139', '定州市', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1107', '139', '安国市', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1108', '139', '高碑店市', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1109', '139', '满城县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1110', '139', '清苑县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1111', '139', '涞水县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1112', '139', '阜平县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1113', '139', '徐水县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1114', '139', '定兴县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1115', '139', '唐县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1116', '139', '高阳县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1117', '139', '容城县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1118', '139', '涞源县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1119', '139', '望都县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1120', '139', '安新县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1121', '139', '易县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1122', '139', '曲阳县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1123', '139', '蠡县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1124', '139', '顺平县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1125', '139', '博野县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1126', '139', '雄县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1127', '140', '运河区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1128', '140', '新华区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1129', '140', '泊头市', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1130', '140', '任丘市', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1131', '140', '黄骅市', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1132', '140', '河间市', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1133', '140', '沧县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1134', '140', '青县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1135', '140', '东光县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1136', '140', '海兴县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1137', '140', '盐山县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1138', '140', '肃宁县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1139', '140', '南皮县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1140', '140', '吴桥县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1141', '140', '献县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1142', '140', '孟村', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1143', '141', '双桥区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1144', '141', '双滦区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1145', '141', '鹰手营子矿区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1146', '141', '承德县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1147', '141', '兴隆县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1148', '141', '平泉县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1149', '141', '滦平县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1150', '141', '隆化县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1151', '141', '丰宁', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1152', '141', '宽城', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1153', '141', '围场', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1154', '142', '从台区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1155', '142', '复兴区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1156', '142', '邯山区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1157', '142', '峰峰矿区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1158', '142', '武安市', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1159', '142', '邯郸县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1160', '142', '临漳县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1161', '142', '成安县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1162', '142', '大名县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1163', '142', '涉县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1164', '142', '磁县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1165', '142', '肥乡县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1166', '142', '永年县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1167', '142', '邱县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1168', '142', '鸡泽县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1169', '142', '广平县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1170', '142', '馆陶县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1171', '142', '魏县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1172', '142', '曲周县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1173', '143', '桃城区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1174', '143', '冀州市', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1175', '143', '深州市', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1176', '143', '枣强县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1177', '143', '武邑县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1178', '143', '武强县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1179', '143', '饶阳县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1180', '143', '安平县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1181', '143', '故城县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1182', '143', '景县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1183', '143', '阜城县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1184', '144', '安次区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1185', '144', '广阳区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1186', '144', '霸州市', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1187', '144', '三河市', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1188', '144', '固安县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1189', '144', '永清县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1190', '144', '香河县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1191', '144', '大城县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1192', '144', '文安县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1193', '144', '大厂', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1194', '145', '海港区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1195', '145', '山海关区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1196', '145', '北戴河区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1197', '145', '昌黎县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1198', '145', '抚宁县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1199', '145', '卢龙县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1200', '145', '青龙', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1201', '146', '路北区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1202', '146', '路南区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1203', '146', '古冶区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1204', '146', '开平区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1205', '146', '丰南区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1206', '146', '丰润区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1207', '146', '遵化市', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1208', '146', '迁安市', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1209', '146', '滦县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1210', '146', '滦南县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1211', '146', '乐亭县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1212', '146', '迁西县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1213', '146', '玉田县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1214', '146', '唐海县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1215', '147', '桥东区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1216', '147', '桥西区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1217', '147', '南宫市', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1218', '147', '沙河市', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1219', '147', '邢台县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1220', '147', '临城县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1221', '147', '内丘县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1222', '147', '柏乡县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1223', '147', '隆尧县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1224', '147', '任县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1225', '147', '南和县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1226', '147', '宁晋县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1227', '147', '巨鹿县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1228', '147', '新河县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1229', '147', '广宗县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1230', '147', '平乡县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1231', '147', '威县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1232', '147', '清河县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1233', '147', '临西县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1234', '148', '桥西区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1235', '148', '桥东区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1236', '148', '宣化区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1237', '148', '下花园区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1238', '148', '宣化县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1239', '148', '张北县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1240', '148', '康保县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1241', '148', '沽源县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1242', '148', '尚义县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1243', '148', '蔚县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1244', '148', '阳原县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1245', '148', '怀安县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1246', '148', '万全县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1247', '148', '怀来县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1248', '148', '涿鹿县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1249', '148', '赤城县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1250', '148', '崇礼县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1251', '149', '金水区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1252', '149', '邙山区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1253', '149', '二七区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1254', '149', '管城区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1255', '149', '中原区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1256', '149', '上街区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1257', '149', '惠济区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1258', '149', '郑东新区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1259', '149', '经济技术开发区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1260', '149', '高新开发区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1261', '149', '出口加工区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1262', '149', '巩义市', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1263', '149', '荥阳市', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1264', '149', '新密市', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1265', '149', '新郑市', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1266', '149', '登封市', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1267', '149', '中牟县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1268', '150', '西工区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1269', '150', '老城区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1270', '150', '涧西区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1271', '150', '瀍河回族区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1272', '150', '洛龙区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1273', '150', '吉利区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1274', '150', '偃师市', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1275', '150', '孟津县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1276', '150', '新安县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1277', '150', '栾川县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1278', '150', '嵩县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1279', '150', '汝阳县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1280', '150', '宜阳县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1281', '150', '洛宁县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1282', '150', '伊川县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1283', '151', '鼓楼区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1284', '151', '龙亭区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1285', '151', '顺河回族区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1286', '151', '金明区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1287', '151', '禹王台区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1288', '151', '杞县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1289', '151', '通许县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1290', '151', '尉氏县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1291', '151', '开封县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1292', '151', '兰考县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1293', '152', '北关区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1294', '152', '文峰区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1295', '152', '殷都区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1296', '152', '龙安区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1297', '152', '林州市', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1298', '152', '安阳县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1299', '152', '汤阴县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1300', '152', '滑县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1301', '152', '内黄县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1302', '153', '淇滨区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1303', '153', '山城区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1304', '153', '鹤山区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1305', '153', '浚县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1306', '153', '淇县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1307', '154', '济源市', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1308', '155', '解放区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1309', '155', '中站区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1310', '155', '马村区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1311', '155', '山阳区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1312', '155', '沁阳市', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1313', '155', '孟州市', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1314', '155', '修武县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1315', '155', '博爱县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1316', '155', '武陟县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1317', '155', '温县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1318', '156', '卧龙区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1319', '156', '宛城区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1320', '156', '邓州市', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1321', '156', '南召县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1322', '156', '方城县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1323', '156', '西峡县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1324', '156', '镇平县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1325', '156', '内乡县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1326', '156', '淅川县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1327', '156', '社旗县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1328', '156', '唐河县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1329', '156', '新野县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1330', '156', '桐柏县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1331', '157', '新华区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1332', '157', '卫东区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1333', '157', '湛河区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1334', '157', '石龙区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1335', '157', '舞钢市', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1336', '157', '汝州市', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1337', '157', '宝丰县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1338', '157', '叶县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1339', '157', '鲁山县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1340', '157', '郏县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1341', '158', '湖滨区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1342', '158', '义马市', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1343', '158', '灵宝市', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1344', '158', '渑池县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1345', '158', '陕县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1346', '158', '卢氏县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1347', '159', '梁园区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1348', '159', '睢阳区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1349', '159', '永城市', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1350', '159', '民权县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1351', '159', '睢县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1352', '159', '宁陵县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1353', '159', '虞城县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1354', '159', '柘城县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1355', '159', '夏邑县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1356', '160', '卫滨区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1357', '160', '红旗区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1358', '160', '凤泉区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1359', '160', '牧野区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1360', '160', '卫辉市', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1361', '160', '辉县市', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1362', '160', '新乡县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1363', '160', '获嘉县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1364', '160', '原阳县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1365', '160', '延津县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1366', '160', '封丘县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1367', '160', '长垣县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1368', '161', '浉河区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1369', '161', '平桥区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1370', '161', '罗山县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1371', '161', '光山县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1372', '161', '新县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1373', '161', '商城县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1374', '161', '固始县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1375', '161', '潢川县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1376', '161', '淮滨县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1377', '161', '息县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1378', '162', '魏都区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1379', '162', '禹州市', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1380', '162', '长葛市', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1381', '162', '许昌县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1382', '162', '鄢陵县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1383', '162', '襄城县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1384', '163', '川汇区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1385', '163', '项城市', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1386', '163', '扶沟县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1387', '163', '西华县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1388', '163', '商水县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1389', '163', '沈丘县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1390', '163', '郸城县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1391', '163', '淮阳县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1392', '163', '太康县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1393', '163', '鹿邑县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1394', '164', '驿城区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1395', '164', '西平县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1396', '164', '上蔡县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1397', '164', '平舆县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1398', '164', '正阳县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1399', '164', '确山县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1400', '164', '泌阳县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1401', '164', '汝南县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1402', '164', '遂平县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1403', '164', '新蔡县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1404', '165', '郾城区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1405', '165', '源汇区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1406', '165', '召陵区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1407', '165', '舞阳县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1408', '165', '临颍县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1409', '166', '华龙区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1410', '166', '清丰县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1411', '166', '南乐县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1412', '166', '范县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1413', '166', '台前县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1414', '166', '濮阳县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1415', '167', '道里区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1416', '167', '南岗区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1417', '167', '动力区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1418', '167', '平房区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1419', '167', '香坊区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1420', '167', '太平区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1421', '167', '道外区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1422', '167', '阿城区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1423', '167', '呼兰区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1424', '167', '松北区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1425', '167', '尚志市', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1426', '167', '双城市', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1427', '167', '五常市', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1428', '167', '方正县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1429', '167', '宾县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1430', '167', '依兰县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1431', '167', '巴彦县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1432', '167', '通河县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1433', '167', '木兰县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1434', '167', '延寿县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1435', '168', '萨尔图区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1436', '168', '红岗区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1437', '168', '龙凤区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1438', '168', '让胡路区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1439', '168', '大同区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1440', '168', '肇州县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1441', '168', '肇源县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1442', '168', '林甸县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1443', '168', '杜尔伯特', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1444', '169', '呼玛县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1445', '169', '漠河县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1446', '169', '塔河县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1447', '170', '兴山区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1448', '170', '工农区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1449', '170', '南山区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1450', '170', '兴安区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1451', '170', '向阳区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1452', '170', '东山区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1453', '170', '萝北县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1454', '170', '绥滨县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1455', '171', '爱辉区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1456', '171', '五大连池市', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1457', '171', '北安市', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1458', '171', '嫩江县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1459', '171', '逊克县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1460', '171', '孙吴县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1461', '172', '鸡冠区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1462', '172', '恒山区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1463', '172', '城子河区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1464', '172', '滴道区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1465', '172', '梨树区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1466', '172', '虎林市', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1467', '172', '密山市', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1468', '172', '鸡东县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1469', '173', '前进区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1470', '173', '郊区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1471', '173', '向阳区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1472', '173', '东风区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1473', '173', '同江市', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1474', '173', '富锦市', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1475', '173', '桦南县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1476', '173', '桦川县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1477', '173', '汤原县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1478', '173', '抚远县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1479', '174', '爱民区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1480', '174', '东安区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1481', '174', '阳明区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1482', '174', '西安区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1483', '174', '绥芬河市', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1484', '174', '海林市', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1485', '174', '宁安市', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1486', '174', '穆棱市', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1487', '174', '东宁县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1488', '174', '林口县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1489', '175', '桃山区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1490', '175', '新兴区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1491', '175', '茄子河区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1492', '175', '勃利县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1493', '176', '龙沙区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1494', '176', '昂昂溪区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1495', '176', '铁峰区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1496', '176', '建华区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1497', '176', '富拉尔基区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1498', '176', '碾子山区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1499', '176', '梅里斯达斡尔区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1500', '176', '讷河市', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1501', '176', '龙江县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1502', '176', '依安县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1503', '176', '泰来县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1504', '176', '甘南县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1505', '176', '富裕县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1506', '176', '克山县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1507', '176', '克东县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1508', '176', '拜泉县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1509', '177', '尖山区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1510', '177', '岭东区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1511', '177', '四方台区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1512', '177', '宝山区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1513', '177', '集贤县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1514', '177', '友谊县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1515', '177', '宝清县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1516', '177', '饶河县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1517', '178', '北林区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1518', '178', '安达市', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1519', '178', '肇东市', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1520', '178', '海伦市', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1521', '178', '望奎县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1522', '178', '兰西县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1523', '178', '青冈县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1524', '178', '庆安县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1525', '178', '明水县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1526', '178', '绥棱县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1527', '179', '伊春区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1528', '179', '带岭区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1529', '179', '南岔区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1530', '179', '金山屯区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1531', '179', '西林区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1532', '179', '美溪区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1533', '179', '乌马河区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1534', '179', '翠峦区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1535', '179', '友好区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1536', '179', '上甘岭区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1537', '179', '五营区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1538', '179', '红星区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1539', '179', '新青区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1540', '179', '汤旺河区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1541', '179', '乌伊岭区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1542', '179', '铁力市', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1543', '179', '嘉荫县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1544', '180', '江岸区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1545', '180', '武昌区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1546', '180', '江汉区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1547', '180', '硚口区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1548', '180', '汉阳区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1549', '180', '青山区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1550', '180', '洪山区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1551', '180', '东西湖区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1552', '180', '汉南区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1553', '180', '蔡甸区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1554', '180', '江夏区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1555', '180', '黄陂区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1556', '180', '新洲区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1557', '180', '经济开发区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1558', '181', '仙桃市', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1559', '182', '鄂城区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1560', '182', '华容区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1561', '182', '梁子湖区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1562', '183', '黄州区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1563', '183', '麻城市', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1564', '183', '武穴市', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1565', '183', '团风县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1566', '183', '红安县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1567', '183', '罗田县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1568', '183', '英山县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1569', '183', '浠水县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1570', '183', '蕲春县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1571', '183', '黄梅县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1572', '184', '黄石港区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1573', '184', '西塞山区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1574', '184', '下陆区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1575', '184', '铁山区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1576', '184', '大冶市', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1577', '184', '阳新县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1578', '185', '东宝区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1579', '185', '掇刀区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1580', '185', '钟祥市', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1581', '185', '京山县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1582', '185', '沙洋县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1583', '186', '沙市区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1584', '186', '荆州区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1585', '186', '石首市', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1586', '186', '洪湖市', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1587', '186', '松滋市', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1588', '186', '公安县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1589', '186', '监利县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1590', '186', '江陵县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1591', '187', '潜江市', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1592', '188', '神农架林区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1593', '189', '张湾区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1594', '189', '茅箭区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1595', '189', '丹江口市', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1596', '189', '郧县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1597', '189', '郧西县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1598', '189', '竹山县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1599', '189', '竹溪县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1600', '189', '房县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1601', '190', '曾都区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1602', '190', '广水市', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1603', '191', '天门市', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1604', '192', '咸安区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1605', '192', '赤壁市', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1606', '192', '嘉鱼县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1607', '192', '通城县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1608', '192', '崇阳县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1609', '192', '通山县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1610', '193', '襄城区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1611', '193', '樊城区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1612', '193', '襄阳区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1613', '193', '老河口市', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1614', '193', '枣阳市', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1615', '193', '宜城市', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1616', '193', '南漳县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1617', '193', '谷城县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1618', '193', '保康县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1619', '194', '孝南区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1620', '194', '应城市', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1621', '194', '安陆市', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1622', '194', '汉川市', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1623', '194', '孝昌县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1624', '194', '大悟县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1625', '194', '云梦县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1626', '195', '长阳', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1627', '195', '五峰', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1628', '195', '西陵区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1629', '195', '伍家岗区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1630', '195', '点军区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1631', '195', '猇亭区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1632', '195', '夷陵区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1633', '195', '宜都市', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1634', '195', '当阳市', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1635', '195', '枝江市', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1636', '195', '远安县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1637', '195', '兴山县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1638', '195', '秭归县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1639', '196', '恩施市', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1640', '196', '利川市', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1641', '196', '建始县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1642', '196', '巴东县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1643', '196', '宣恩县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1644', '196', '咸丰县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1645', '196', '来凤县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1646', '196', '鹤峰县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1647', '197', '岳麓区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1648', '197', '芙蓉区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1649', '197', '天心区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1650', '197', '开福区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1651', '197', '雨花区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1652', '197', '开发区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1653', '197', '浏阳市', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1654', '197', '长沙县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1655', '197', '望城县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1656', '197', '宁乡县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1657', '198', '永定区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1658', '198', '武陵源区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1659', '198', '慈利县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1660', '198', '桑植县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1661', '199', '武陵区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1662', '199', '鼎城区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1663', '199', '津市市', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1664', '199', '安乡县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1665', '199', '汉寿县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1666', '199', '澧县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1667', '199', '临澧县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1668', '199', '桃源县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1669', '199', '石门县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1670', '200', '北湖区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1671', '200', '苏仙区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1672', '200', '资兴市', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1673', '200', '桂阳县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1674', '200', '宜章县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1675', '200', '永兴县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1676', '200', '嘉禾县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1677', '200', '临武县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1678', '200', '汝城县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1679', '200', '桂东县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1680', '200', '安仁县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1681', '201', '雁峰区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1682', '201', '珠晖区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1683', '201', '石鼓区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1684', '201', '蒸湘区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1685', '201', '南岳区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1686', '201', '耒阳市', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1687', '201', '常宁市', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1688', '201', '衡阳县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1689', '201', '衡南县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1690', '201', '衡山县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1691', '201', '衡东县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1692', '201', '祁东县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1693', '202', '鹤城区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1694', '202', '靖州', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1695', '202', '麻阳', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1696', '202', '通道', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1697', '202', '新晃', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1698', '202', '芷江', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1699', '202', '沅陵县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1700', '202', '辰溪县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1701', '202', '溆浦县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1702', '202', '中方县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1703', '202', '会同县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1704', '202', '洪江市', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1705', '203', '娄星区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1706', '203', '冷水江市', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1707', '203', '涟源市', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1708', '203', '双峰县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1709', '203', '新化县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1710', '204', '城步', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1711', '204', '双清区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1712', '204', '大祥区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1713', '204', '北塔区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1714', '204', '武冈市', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1715', '204', '邵东县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1716', '204', '新邵县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1717', '204', '邵阳县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1718', '204', '隆回县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1719', '204', '洞口县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1720', '204', '绥宁县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1721', '204', '新宁县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1722', '205', '岳塘区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1723', '205', '雨湖区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1724', '205', '湘乡市', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1725', '205', '韶山市', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1726', '205', '湘潭县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1727', '206', '吉首市', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1728', '206', '泸溪县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1729', '206', '凤凰县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1730', '206', '花垣县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1731', '206', '保靖县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1732', '206', '古丈县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1733', '206', '永顺县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1734', '206', '龙山县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1735', '207', '赫山区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1736', '207', '资阳区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1737', '207', '沅江市', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1738', '207', '南县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1739', '207', '桃江县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1740', '207', '安化县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1741', '208', '江华', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1742', '208', '冷水滩区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1743', '208', '零陵区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1744', '208', '祁阳县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1745', '208', '东安县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1746', '208', '双牌县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1747', '208', '道县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1748', '208', '江永县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1749', '208', '宁远县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1750', '208', '蓝山县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1751', '208', '新田县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1752', '209', '岳阳楼区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1753', '209', '君山区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1754', '209', '云溪区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1755', '209', '汨罗市', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1756', '209', '临湘市', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1757', '209', '岳阳县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1758', '209', '华容县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1759', '209', '湘阴县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1760', '209', '平江县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1761', '210', '天元区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1762', '210', '荷塘区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1763', '210', '芦淞区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1764', '210', '石峰区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1765', '210', '醴陵市', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1766', '210', '株洲县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1767', '210', '攸县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1768', '210', '茶陵县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1769', '210', '炎陵县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1770', '211', '朝阳区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1771', '211', '宽城区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1772', '211', '二道区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1773', '211', '南关区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1774', '211', '绿园区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1775', '211', '双阳区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1776', '211', '净月潭开发区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1777', '211', '高新技术开发区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1778', '211', '经济技术开发区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1779', '211', '汽车产业开发区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1780', '211', '德惠市', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1781', '211', '九台市', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1782', '211', '榆树市', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1783', '211', '农安县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1784', '212', '船营区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1785', '212', '昌邑区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1786', '212', '龙潭区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1787', '212', '丰满区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1788', '212', '蛟河市', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1789', '212', '桦甸市', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1790', '212', '舒兰市', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1791', '212', '磐石市', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1792', '212', '永吉县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1793', '213', '洮北区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1794', '213', '洮南市', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1795', '213', '大安市', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1796', '213', '镇赉县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1797', '213', '通榆县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1798', '214', '江源区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1799', '214', '八道江区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1800', '214', '长白', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1801', '214', '临江市', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1802', '214', '抚松县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1803', '214', '靖宇县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1804', '215', '龙山区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1805', '215', '西安区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1806', '215', '东丰县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1807', '215', '东辽县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1808', '216', '铁西区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1809', '216', '铁东区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1810', '216', '伊通', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1811', '216', '公主岭市', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1812', '216', '双辽市', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1813', '216', '梨树县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1814', '217', '前郭尔罗斯', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1815', '217', '宁江区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1816', '217', '长岭县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1817', '217', '乾安县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1818', '217', '扶余县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1819', '218', '东昌区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1820', '218', '二道江区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1821', '218', '梅河口市', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1822', '218', '集安市', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1823', '218', '通化县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1824', '218', '辉南县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1825', '218', '柳河县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1826', '219', '延吉市', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1827', '219', '图们市', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1828', '219', '敦化市', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1829', '219', '珲春市', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1830', '219', '龙井市', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1831', '219', '和龙市', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1832', '219', '安图县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1833', '219', '汪清县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1834', '220', '玄武区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1835', '220', '鼓楼区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1836', '220', '白下区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1837', '220', '建邺区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1838', '220', '秦淮区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1839', '220', '雨花台区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1840', '220', '下关区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1841', '220', '栖霞区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1842', '220', '浦口区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1843', '220', '江宁区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1844', '220', '六合区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1845', '220', '溧水县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1846', '220', '高淳县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1847', '221', '沧浪区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1848', '221', '金阊区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1849', '221', '平江区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1850', '221', '虎丘区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1851', '221', '吴中区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1852', '221', '相城区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1853', '221', '园区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1854', '221', '新区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1855', '221', '常熟市', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1856', '221', '张家港市', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1857', '221', '玉山镇', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1858', '221', '巴城镇', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1859', '221', '周市镇', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1860', '221', '陆家镇', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1861', '221', '花桥镇', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1862', '221', '淀山湖镇', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1863', '221', '张浦镇', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1864', '221', '周庄镇', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1865', '221', '千灯镇', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1866', '221', '锦溪镇', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1867', '221', '开发区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1868', '221', '吴江市', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1869', '221', '太仓市', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1870', '222', '崇安区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1871', '222', '北塘区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1872', '222', '南长区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1873', '222', '锡山区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1874', '222', '惠山区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1875', '222', '滨湖区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1876', '222', '新区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1877', '222', '江阴市', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1878', '222', '宜兴市', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1879', '223', '天宁区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1880', '223', '钟楼区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1881', '223', '戚墅堰区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1882', '223', '郊区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1883', '223', '新北区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1884', '223', '武进区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1885', '223', '溧阳市', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1886', '223', '金坛市', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1887', '224', '清河区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1888', '224', '清浦区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1889', '224', '楚州区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1890', '224', '淮阴区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1891', '224', '涟水县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1892', '224', '洪泽县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1893', '224', '盱眙县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1894', '224', '金湖县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1895', '225', '新浦区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1896', '225', '连云区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1897', '225', '海州区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1898', '225', '赣榆县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1899', '225', '东海县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1900', '225', '灌云县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1901', '225', '灌南县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1902', '226', '崇川区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1903', '226', '港闸区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1904', '226', '经济开发区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1905', '226', '启东市', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1906', '226', '如皋市', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1907', '226', '通州市', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1908', '226', '海门市', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1909', '226', '海安县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1910', '226', '如东县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1911', '227', '宿城区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1912', '227', '宿豫区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1913', '227', '宿豫县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1914', '227', '沭阳县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1915', '227', '泗阳县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1916', '227', '泗洪县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1917', '228', '海陵区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1918', '228', '高港区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1919', '228', '兴化市', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1920', '228', '靖江市', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1921', '228', '泰兴市', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1922', '228', '姜堰市', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1923', '229', '云龙区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1924', '229', '鼓楼区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1925', '229', '九里区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1926', '229', '贾汪区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1927', '229', '泉山区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1928', '229', '新沂市', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1929', '229', '邳州市', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1930', '229', '丰县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1931', '229', '沛县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1932', '229', '铜山县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1933', '229', '睢宁县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1934', '230', '城区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1935', '230', '亭湖区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1936', '230', '盐都区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1937', '230', '盐都县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1938', '230', '东台市', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1939', '230', '大丰市', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1940', '230', '响水县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1941', '230', '滨海县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1942', '230', '阜宁县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1943', '230', '射阳县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1944', '230', '建湖县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1945', '231', '广陵区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1946', '231', '维扬区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1947', '231', '邗江区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1948', '231', '仪征市', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1949', '231', '高邮市', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1950', '231', '江都市', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1951', '231', '宝应县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1952', '232', '京口区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1953', '232', '润州区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1954', '232', '丹徒区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1955', '232', '丹阳市', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1956', '232', '扬中市', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1957', '232', '句容市', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1958', '233', '东湖区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1959', '233', '西湖区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1960', '233', '青云谱区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1961', '233', '湾里区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1962', '233', '青山湖区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1963', '233', '红谷滩新区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1964', '233', '昌北区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1965', '233', '高新区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1966', '233', '南昌县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1967', '233', '新建县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1968', '233', '安义县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1969', '233', '进贤县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1970', '234', '临川区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1971', '234', '南城县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1972', '234', '黎川县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1973', '234', '南丰县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1974', '234', '崇仁县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1975', '234', '乐安县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1976', '234', '宜黄县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1977', '234', '金溪县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1978', '234', '资溪县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1979', '234', '东乡县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1980', '234', '广昌县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1981', '235', '章贡区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1982', '235', '于都县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1983', '235', '瑞金市', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1984', '235', '南康市', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1985', '235', '赣县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1986', '235', '信丰县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1987', '235', '大余县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1988', '235', '上犹县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1989', '235', '崇义县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1990', '235', '安远县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1991', '235', '龙南县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1992', '235', '定南县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1993', '235', '全南县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1994', '235', '宁都县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1995', '235', '兴国县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1996', '235', '会昌县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1997', '235', '寻乌县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1998', '235', '石城县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1999', '236', '安福县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2000', '236', '吉州区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2001', '236', '青原区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2002', '236', '井冈山市', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2003', '236', '吉安县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2004', '236', '吉水县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2005', '236', '峡江县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2006', '236', '新干县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2007', '236', '永丰县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2008', '236', '泰和县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2009', '236', '遂川县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2010', '236', '万安县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2011', '236', '永新县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2012', '237', '珠山区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2013', '237', '昌江区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2014', '237', '乐平市', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2015', '237', '浮梁县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2016', '238', '浔阳区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2017', '238', '庐山区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2018', '238', '瑞昌市', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2019', '238', '九江县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2020', '238', '武宁县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2021', '238', '修水县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2022', '238', '永修县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2023', '238', '德安县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2024', '238', '星子县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2025', '238', '都昌县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2026', '238', '湖口县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2027', '238', '彭泽县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2028', '239', '安源区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2029', '239', '湘东区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2030', '239', '莲花县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2031', '239', '芦溪县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2032', '239', '上栗县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2033', '240', '信州区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2034', '240', '德兴市', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2035', '240', '上饶县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2036', '240', '广丰县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2037', '240', '玉山县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2038', '240', '铅山县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2039', '240', '横峰县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2040', '240', '弋阳县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2041', '240', '余干县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2042', '240', '波阳县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2043', '240', '万年县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2044', '240', '婺源县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2045', '241', '渝水区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2046', '241', '分宜县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2047', '242', '袁州区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2048', '242', '丰城市', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2049', '242', '樟树市', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2050', '242', '高安市', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2051', '242', '奉新县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2052', '242', '万载县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2053', '242', '上高县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2054', '242', '宜丰县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2055', '242', '靖安县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2056', '242', '铜鼓县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2057', '243', '月湖区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2058', '243', '贵溪市', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2059', '243', '余江县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2060', '244', '沈河区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2061', '244', '皇姑区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2062', '244', '和平区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2063', '244', '大东区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2064', '244', '铁西区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2065', '244', '苏家屯区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2066', '244', '东陵区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2067', '244', '沈北新区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2068', '244', '于洪区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2069', '244', '浑南新区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2070', '244', '新民市', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2071', '244', '辽中县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2072', '244', '康平县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2073', '244', '法库县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2074', '245', '西岗区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2075', '245', '中山区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2076', '245', '沙河口区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2077', '245', '甘井子区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2078', '245', '旅顺口区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2079', '245', '金州区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2080', '245', '开发区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2081', '245', '瓦房店市', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2082', '245', '普兰店市', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2083', '245', '庄河市', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2084', '245', '长海县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2085', '246', '铁东区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2086', '246', '铁西区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2087', '246', '立山区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2088', '246', '千山区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2089', '246', '岫岩', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2090', '246', '海城市', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2091', '246', '台安县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2092', '247', '本溪', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2093', '247', '平山区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2094', '247', '明山区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2095', '247', '溪湖区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2096', '247', '南芬区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2097', '247', '桓仁', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2098', '248', '双塔区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2099', '248', '龙城区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2100', '248', '喀喇沁左翼蒙古族自治', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2101', '248', '北票市', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2102', '248', '凌源市', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2103', '248', '朝阳县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2104', '248', '建平县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2105', '249', '振兴区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2106', '249', '元宝区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2107', '249', '振安区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2108', '249', '宽甸', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2109', '249', '东港市', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2110', '249', '凤城市', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2111', '250', '顺城区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2112', '250', '新抚区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2113', '250', '东洲区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2114', '250', '望花区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2115', '250', '清原', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2116', '250', '新宾', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2117', '250', '抚顺县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2118', '251', '阜新', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2119', '251', '海州区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2120', '251', '新邱区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2121', '251', '太平区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2122', '251', '清河门区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2123', '251', '细河区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2124', '251', '彰武县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2125', '252', '龙港区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2126', '252', '南票区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2127', '252', '连山区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2128', '252', '兴城市', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2129', '252', '绥中县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2130', '252', '建昌县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2131', '253', '太和区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2132', '253', '古塔区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2133', '253', '凌河区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2134', '253', '凌海市', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2135', '253', '北镇市', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2136', '253', '黑山县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2137', '253', '义县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2138', '254', '白塔区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2139', '254', '文圣区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2140', '254', '宏伟区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2141', '254', '太子河区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2142', '254', '弓长岭区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2143', '254', '灯塔市', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2144', '254', '辽阳县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2145', '255', '双台子区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2146', '255', '兴隆台区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2147', '255', '大洼县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2148', '255', '盘山县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2149', '256', '银州区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2150', '256', '清河区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2151', '256', '调兵山市', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2152', '256', '开原市', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2153', '256', '铁岭县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2154', '256', '西丰县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2155', '256', '昌图县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2156', '257', '站前区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2157', '257', '西市区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2158', '257', '鲅鱼圈区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2159', '257', '老边区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2160', '257', '盖州市', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2161', '257', '大石桥市', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2162', '258', '回民区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2163', '258', '玉泉区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2164', '258', '新城区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2165', '258', '赛罕区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2166', '258', '清水河县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2167', '258', '土默特左旗', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2168', '258', '托克托县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2169', '258', '和林格尔县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2170', '258', '武川县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2171', '259', '阿拉善左旗', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2172', '259', '阿拉善右旗', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2173', '259', '额济纳旗', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2174', '260', '临河区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2175', '260', '五原县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2176', '260', '磴口县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2177', '260', '乌拉特前旗', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2178', '260', '乌拉特中旗', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2179', '260', '乌拉特后旗', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2180', '260', '杭锦后旗', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2181', '261', '昆都仑区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2182', '261', '青山区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2183', '261', '东河区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2184', '261', '九原区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2185', '261', '石拐区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2186', '261', '白云矿区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2187', '261', '土默特右旗', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2188', '261', '固阳县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2189', '261', '达尔罕茂明安联合旗', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2190', '262', '红山区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2191', '262', '元宝山区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2192', '262', '松山区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2193', '262', '阿鲁科尔沁旗', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2194', '262', '巴林左旗', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2195', '262', '巴林右旗', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2196', '262', '林西县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2197', '262', '克什克腾旗', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2198', '262', '翁牛特旗', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2199', '262', '喀喇沁旗', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2200', '262', '宁城县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2201', '262', '敖汉旗', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2202', '263', '东胜区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2203', '263', '达拉特旗', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2204', '263', '准格尔旗', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2205', '263', '鄂托克前旗', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2206', '263', '鄂托克旗', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2207', '263', '杭锦旗', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2208', '263', '乌审旗', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2209', '263', '伊金霍洛旗', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2210', '264', '海拉尔区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2211', '264', '莫力达瓦', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2212', '264', '满洲里市', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2213', '264', '牙克石市', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2214', '264', '扎兰屯市', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2215', '264', '额尔古纳市', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2216', '264', '根河市', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2217', '264', '阿荣旗', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2218', '264', '鄂伦春自治旗', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2219', '264', '鄂温克族自治旗', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2220', '264', '陈巴尔虎旗', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2221', '264', '新巴尔虎左旗', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2222', '264', '新巴尔虎右旗', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2223', '265', '科尔沁区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2224', '265', '霍林郭勒市', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2225', '265', '科尔沁左翼中旗', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2226', '265', '科尔沁左翼后旗', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2227', '265', '开鲁县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2228', '265', '库伦旗', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2229', '265', '奈曼旗', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2230', '265', '扎鲁特旗', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2231', '266', '海勃湾区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2232', '266', '乌达区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2233', '266', '海南区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2234', '267', '化德县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2235', '267', '集宁区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2236', '267', '丰镇市', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2237', '267', '卓资县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2238', '267', '商都县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2239', '267', '兴和县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2240', '267', '凉城县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2241', '267', '察哈尔右翼前旗', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2242', '267', '察哈尔右翼中旗', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2243', '267', '察哈尔右翼后旗', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2244', '267', '四子王旗', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2245', '268', '二连浩特市', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2246', '268', '锡林浩特市', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2247', '268', '阿巴嘎旗', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2248', '268', '苏尼特左旗', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2249', '268', '苏尼特右旗', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2250', '268', '东乌珠穆沁旗', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2251', '268', '西乌珠穆沁旗', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2252', '268', '太仆寺旗', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2253', '268', '镶黄旗', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2254', '268', '正镶白旗', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2255', '268', '正蓝旗', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2256', '268', '多伦县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2257', '269', '乌兰浩特市', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2258', '269', '阿尔山市', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2259', '269', '科尔沁右翼前旗', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2260', '269', '科尔沁右翼中旗', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2261', '269', '扎赉特旗', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2262', '269', '突泉县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2263', '270', '西夏区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2264', '270', '金凤区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2265', '270', '兴庆区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2266', '270', '灵武市', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2267', '270', '永宁县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2268', '270', '贺兰县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2269', '271', '原州区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2270', '271', '海原县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2271', '271', '西吉县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2272', '271', '隆德县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2273', '271', '泾源县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2274', '271', '彭阳县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2275', '272', '惠农县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2276', '272', '大武口区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2277', '272', '惠农区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2278', '272', '陶乐县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2279', '272', '平罗县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2280', '273', '利通区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2281', '273', '中卫县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2282', '273', '青铜峡市', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2283', '273', '中宁县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2284', '273', '盐池县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2285', '273', '同心县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2286', '274', '沙坡头区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2287', '274', '海原县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2288', '274', '中宁县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2289', '275', '城中区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2290', '275', '城东区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2291', '275', '城西区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2292', '275', '城北区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2293', '275', '湟中县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2294', '275', '湟源县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2295', '275', '大通', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2296', '276', '玛沁县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2297', '276', '班玛县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2298', '276', '甘德县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2299', '276', '达日县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2300', '276', '久治县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2301', '276', '玛多县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2302', '277', '海晏县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2303', '277', '祁连县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2304', '277', '刚察县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2305', '277', '门源', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2306', '278', '平安县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2307', '278', '乐都县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2308', '278', '民和', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2309', '278', '互助', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2310', '278', '化隆', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2311', '278', '循化', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2312', '279', '共和县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2313', '279', '同德县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2314', '279', '贵德县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2315', '279', '兴海县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2316', '279', '贵南县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2317', '280', '德令哈市', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2318', '280', '格尔木市', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2319', '280', '乌兰县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2320', '280', '都兰县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2321', '280', '天峻县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2322', '281', '同仁县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2323', '281', '尖扎县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2324', '281', '泽库县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2325', '281', '河南蒙古族自治县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2326', '282', '玉树县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2327', '282', '杂多县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2328', '282', '称多县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2329', '282', '治多县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2330', '282', '囊谦县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2331', '282', '曲麻莱县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2332', '283', '市中区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2333', '283', '历下区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2334', '283', '天桥区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2335', '283', '槐荫区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2336', '283', '历城区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2337', '283', '长清区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2338', '283', '章丘市', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2339', '283', '平阴县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2340', '283', '济阳县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2341', '283', '商河县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2342', '284', '市南区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2343', '284', '市北区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2344', '284', '城阳区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2345', '284', '四方区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2346', '284', '李沧区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2347', '284', '黄岛区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2348', '284', '崂山区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2349', '284', '胶州市', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2350', '284', '即墨市', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2351', '284', '平度市', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2352', '284', '胶南市', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2353', '284', '莱西市', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2354', '285', '滨城区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2355', '285', '惠民县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2356', '285', '阳信县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2357', '285', '无棣县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2358', '285', '沾化县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2359', '285', '博兴县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2360', '285', '邹平县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2361', '286', '德城区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2362', '286', '陵县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2363', '286', '乐陵市', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2364', '286', '禹城市', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2365', '286', '宁津县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2366', '286', '庆云县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2367', '286', '临邑县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2368', '286', '齐河县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2369', '286', '平原县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2370', '286', '夏津县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2371', '286', '武城县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2372', '287', '东营区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2373', '287', '河口区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2374', '287', '垦利县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2375', '287', '利津县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2376', '287', '广饶县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2377', '288', '牡丹区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2378', '288', '曹县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2379', '288', '单县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2380', '288', '成武县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2381', '288', '巨野县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2382', '288', '郓城县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2383', '288', '鄄城县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2384', '288', '定陶县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2385', '288', '东明县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2386', '289', '市中区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2387', '289', '任城区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2388', '289', '曲阜市', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2389', '289', '兖州市', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2390', '289', '邹城市', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2391', '289', '微山县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2392', '289', '鱼台县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2393', '289', '金乡县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2394', '289', '嘉祥县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2395', '289', '汶上县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2396', '289', '泗水县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2397', '289', '梁山县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2398', '290', '莱城区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2399', '290', '钢城区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2400', '291', '东昌府区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2401', '291', '临清市', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2402', '291', '阳谷县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2403', '291', '莘县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2404', '291', '茌平县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2405', '291', '东阿县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2406', '291', '冠县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2407', '291', '高唐县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2408', '292', '兰山区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2409', '292', '罗庄区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2410', '292', '河东区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2411', '292', '沂南县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2412', '292', '郯城县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2413', '292', '沂水县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2414', '292', '苍山县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2415', '292', '费县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2416', '292', '平邑县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2417', '292', '莒南县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2418', '292', '蒙阴县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2419', '292', '临沭县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2420', '293', '东港区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2421', '293', '岚山区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2422', '293', '五莲县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2423', '293', '莒县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2424', '294', '泰山区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2425', '294', '岱岳区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2426', '294', '新泰市', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2427', '294', '肥城市', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2428', '294', '宁阳县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2429', '294', '东平县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2430', '295', '荣成市', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2431', '295', '乳山市', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2432', '295', '环翠区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2433', '295', '文登市', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2434', '296', '潍城区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2435', '296', '寒亭区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2436', '296', '坊子区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2437', '296', '奎文区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2438', '296', '青州市', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2439', '296', '诸城市', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2440', '296', '寿光市', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2441', '296', '安丘市', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2442', '296', '高密市', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2443', '296', '昌邑市', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2444', '296', '临朐县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2445', '296', '昌乐县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2446', '297', '芝罘区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2447', '297', '福山区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2448', '297', '牟平区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2449', '297', '莱山区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2450', '297', '开发区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2451', '297', '龙口市', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2452', '297', '莱阳市', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2453', '297', '莱州市', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2454', '297', '蓬莱市', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2455', '297', '招远市', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2456', '297', '栖霞市', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2457', '297', '海阳市', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2458', '297', '长岛县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2459', '298', '市中区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2460', '298', '山亭区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2461', '298', '峄城区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2462', '298', '台儿庄区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2463', '298', '薛城区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2464', '298', '滕州市', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2465', '299', '张店区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2466', '299', '临淄区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2467', '299', '淄川区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2468', '299', '博山区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2469', '299', '周村区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2470', '299', '桓台县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2471', '299', '高青县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2472', '299', '沂源县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2473', '300', '杏花岭区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2474', '300', '小店区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2475', '300', '迎泽区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2476', '300', '尖草坪区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2477', '300', '万柏林区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2478', '300', '晋源区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2479', '300', '高新开发区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2480', '300', '民营经济开发区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2481', '300', '经济技术开发区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2482', '300', '清徐县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2483', '300', '阳曲县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2484', '300', '娄烦县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2485', '300', '古交市', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2486', '301', '城区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2487', '301', '郊区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2488', '301', '沁县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2489', '301', '潞城市', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2490', '301', '长治县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2491', '301', '襄垣县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2492', '301', '屯留县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2493', '301', '平顺县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2494', '301', '黎城县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2495', '301', '壶关县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2496', '301', '长子县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2497', '301', '武乡县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2498', '301', '沁源县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2499', '302', '城区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2500', '302', '矿区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2501', '302', '南郊区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2502', '302', '新荣区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2503', '302', '阳高县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2504', '302', '天镇县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2505', '302', '广灵县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2506', '302', '灵丘县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2507', '302', '浑源县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2508', '302', '左云县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2509', '302', '大同县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2510', '303', '城区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2511', '303', '高平市', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2512', '303', '沁水县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2513', '303', '阳城县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2514', '303', '陵川县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2515', '303', '泽州县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2516', '304', '榆次区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2517', '304', '介休市', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2518', '304', '榆社县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2519', '304', '左权县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2520', '304', '和顺县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2521', '304', '昔阳县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2522', '304', '寿阳县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2523', '304', '太谷县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2524', '304', '祁县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2525', '304', '平遥县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2526', '304', '灵石县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2527', '305', '尧都区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2528', '305', '侯马市', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2529', '305', '霍州市', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2530', '305', '曲沃县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2531', '305', '翼城县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2532', '305', '襄汾县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2533', '305', '洪洞县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2534', '305', '吉县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2535', '305', '安泽县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2536', '305', '浮山县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2537', '305', '古县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2538', '305', '乡宁县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2539', '305', '大宁县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2540', '305', '隰县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2541', '305', '永和县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2542', '305', '蒲县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2543', '305', '汾西县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2544', '306', '离石市', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2545', '306', '离石区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2546', '306', '孝义市', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2547', '306', '汾阳市', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2548', '306', '文水县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2549', '306', '交城县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2550', '306', '兴县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2551', '306', '临县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2552', '306', '柳林县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2553', '306', '石楼县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2554', '306', '岚县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2555', '306', '方山县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2556', '306', '中阳县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2557', '306', '交口县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2558', '307', '朔城区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2559', '307', '平鲁区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2560', '307', '山阴县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2561', '307', '应县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2562', '307', '右玉县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2563', '307', '怀仁县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2564', '308', '忻府区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2565', '308', '原平市', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2566', '308', '定襄县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2567', '308', '五台县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2568', '308', '代县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2569', '308', '繁峙县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2570', '308', '宁武县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2571', '308', '静乐县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2572', '308', '神池县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2573', '308', '五寨县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2574', '308', '岢岚县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2575', '308', '河曲县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2576', '308', '保德县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2577', '308', '偏关县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2578', '309', '城区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2579', '309', '矿区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2580', '309', '郊区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2581', '309', '平定县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2582', '309', '盂县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2583', '310', '盐湖区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2584', '310', '永济市', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2585', '310', '河津市', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2586', '310', '临猗县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2587', '310', '万荣县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2588', '310', '闻喜县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2589', '310', '稷山县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2590', '310', '新绛县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2591', '310', '绛县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2592', '310', '垣曲县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2593', '310', '夏县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2594', '310', '平陆县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2595', '310', '芮城县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2596', '311', '莲湖区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2597', '311', '新城区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2598', '311', '碑林区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2599', '311', '雁塔区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2600', '311', '灞桥区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2601', '311', '未央区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2602', '311', '阎良区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2603', '311', '临潼区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2604', '311', '长安区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2605', '311', '蓝田县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2606', '311', '周至县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2607', '311', '户县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2608', '311', '高陵县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2609', '312', '汉滨区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2610', '312', '汉阴县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2611', '312', '石泉县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2612', '312', '宁陕县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2613', '312', '紫阳县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2614', '312', '岚皋县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2615', '312', '平利县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2616', '312', '镇坪县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2617', '312', '旬阳县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2618', '312', '白河县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2619', '313', '陈仓区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2620', '313', '渭滨区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2621', '313', '金台区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2622', '313', '凤翔县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2623', '313', '岐山县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2624', '313', '扶风县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2625', '313', '眉县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2626', '313', '陇县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2627', '313', '千阳县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2628', '313', '麟游县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2629', '313', '凤县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2630', '313', '太白县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2631', '314', '汉台区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2632', '314', '南郑县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2633', '314', '城固县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2634', '314', '洋县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2635', '314', '西乡县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2636', '314', '勉县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2637', '314', '宁强县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2638', '314', '略阳县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2639', '314', '镇巴县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2640', '314', '留坝县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2641', '314', '佛坪县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2642', '315', '商州区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2643', '315', '洛南县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2644', '315', '丹凤县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2645', '315', '商南县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2646', '315', '山阳县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2647', '315', '镇安县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2648', '315', '柞水县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2649', '316', '耀州区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2650', '316', '王益区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2651', '316', '印台区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2652', '316', '宜君县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2653', '317', '临渭区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2654', '317', '韩城市', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2655', '317', '华阴市', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2656', '317', '华县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2657', '317', '潼关县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2658', '317', '大荔县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2659', '317', '合阳县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2660', '317', '澄城县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2661', '317', '蒲城县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2662', '317', '白水县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2663', '317', '富平县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2664', '318', '秦都区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2665', '318', '渭城区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2666', '318', '杨陵区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2667', '318', '兴平市', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2668', '318', '三原县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2669', '318', '泾阳县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2670', '318', '乾县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2671', '318', '礼泉县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2672', '318', '永寿县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2673', '318', '彬县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2674', '318', '长武县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2675', '318', '旬邑县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2676', '318', '淳化县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2677', '318', '武功县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2678', '319', '吴起县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2679', '319', '宝塔区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2680', '319', '延长县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2681', '319', '延川县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2682', '319', '子长县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2683', '319', '安塞县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2684', '319', '志丹县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2685', '319', '甘泉县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2686', '319', '富县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2687', '319', '洛川县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2688', '319', '宜川县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2689', '319', '黄龙县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2690', '319', '黄陵县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2691', '320', '榆阳区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2692', '320', '神木县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2693', '320', '府谷县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2694', '320', '横山县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2695', '320', '靖边县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2696', '320', '定边县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2697', '320', '绥德县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2698', '320', '米脂县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2699', '320', '佳县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2700', '320', '吴堡县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2701', '320', '清涧县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2702', '320', '子洲县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2703', '321', '长宁区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2704', '321', '闸北区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2705', '321', '闵行区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2706', '321', '徐汇区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2707', '321', '浦东新区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2708', '321', '杨浦区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2709', '321', '普陀区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2710', '321', '静安区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2711', '321', '卢湾区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2712', '321', '虹口区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2713', '321', '黄浦区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2714', '321', '南汇区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2715', '321', '松江区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2716', '321', '嘉定区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2717', '321', '宝山区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2718', '321', '青浦区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2719', '321', '金山区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2720', '321', '奉贤区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2721', '321', '崇明县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2722', '322', '青羊区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2723', '322', '锦江区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2724', '322', '金牛区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2725', '322', '武侯区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2726', '322', '成华区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2727', '322', '龙泉驿区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2728', '322', '青白江区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2729', '322', '新都区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2730', '322', '温江区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2731', '322', '高新区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2732', '322', '高新西区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2733', '322', '都江堰市', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2734', '322', '彭州市', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2735', '322', '邛崃市', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2736', '322', '崇州市', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2737', '322', '金堂县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2738', '322', '双流县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2739', '322', '郫县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2740', '322', '大邑县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2741', '322', '蒲江县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2742', '322', '新津县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2743', '322', '都江堰市', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2744', '322', '彭州市', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2745', '322', '邛崃市', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2746', '322', '崇州市', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2747', '322', '金堂县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2748', '322', '双流县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2749', '322', '郫县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2750', '322', '大邑县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2751', '322', '蒲江县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2752', '322', '新津县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2753', '323', '涪城区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2754', '323', '游仙区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2755', '323', '江油市', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2756', '323', '盐亭县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2757', '323', '三台县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2758', '323', '平武县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2759', '323', '安县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2760', '323', '梓潼县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2761', '323', '北川县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2762', '324', '马尔康县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2763', '324', '汶川县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2764', '324', '理县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2765', '324', '茂县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2766', '324', '松潘县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2767', '324', '九寨沟县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2768', '324', '金川县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2769', '324', '小金县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2770', '324', '黑水县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2771', '324', '壤塘县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2772', '324', '阿坝县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2773', '324', '若尔盖县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2774', '324', '红原县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2775', '325', '巴州区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2776', '325', '通江县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2777', '325', '南江县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2778', '325', '平昌县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2779', '326', '通川区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2780', '326', '万源市', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2781', '326', '达县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2782', '326', '宣汉县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2783', '326', '开江县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2784', '326', '大竹县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2785', '326', '渠县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2786', '327', '旌阳区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2787', '327', '广汉市', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2788', '327', '什邡市', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2789', '327', '绵竹市', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2790', '327', '罗江县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2791', '327', '中江县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2792', '328', '康定县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2793', '328', '丹巴县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2794', '328', '泸定县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2795', '328', '炉霍县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2796', '328', '九龙县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2797', '328', '甘孜县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2798', '328', '雅江县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2799', '328', '新龙县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2800', '328', '道孚县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2801', '328', '白玉县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2802', '328', '理塘县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2803', '328', '德格县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2804', '328', '乡城县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2805', '328', '石渠县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2806', '328', '稻城县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2807', '328', '色达县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2808', '328', '巴塘县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2809', '328', '得荣县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2810', '329', '广安区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2811', '329', '华蓥市', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2812', '329', '岳池县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2813', '329', '武胜县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2814', '329', '邻水县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2815', '330', '利州区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2816', '330', '元坝区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2817', '330', '朝天区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2818', '330', '旺苍县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2819', '330', '青川县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2820', '330', '剑阁县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2821', '330', '苍溪县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2822', '331', '峨眉山市', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2823', '331', '乐山市', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2824', '331', '犍为县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2825', '331', '井研县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2826', '331', '夹江县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2827', '331', '沐川县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2828', '331', '峨边', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2829', '331', '马边', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2830', '332', '西昌市', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2831', '332', '盐源县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2832', '332', '德昌县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2833', '332', '会理县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2834', '332', '会东县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2835', '332', '宁南县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2836', '332', '普格县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2837', '332', '布拖县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2838', '332', '金阳县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2839', '332', '昭觉县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2840', '332', '喜德县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2841', '332', '冕宁县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2842', '332', '越西县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2843', '332', '甘洛县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2844', '332', '美姑县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2845', '332', '雷波县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2846', '332', '木里', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2847', '333', '东坡区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2848', '333', '仁寿县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2849', '333', '彭山县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2850', '333', '洪雅县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2851', '333', '丹棱县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2852', '333', '青神县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2853', '334', '阆中市', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2854', '334', '南部县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2855', '334', '营山县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2856', '334', '蓬安县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2857', '334', '仪陇县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2858', '334', '顺庆区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2859', '334', '高坪区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2860', '334', '嘉陵区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2861', '334', '西充县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2862', '335', '市中区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2863', '335', '东兴区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2864', '335', '威远县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2865', '335', '资中县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2866', '335', '隆昌县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2867', '336', '东  区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2868', '336', '西  区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2869', '336', '仁和区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2870', '336', '米易县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2871', '336', '盐边县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2872', '337', '船山区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2873', '337', '安居区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2874', '337', '蓬溪县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2875', '337', '射洪县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2876', '337', '大英县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2877', '338', '雨城区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2878', '338', '名山县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2879', '338', '荥经县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2880', '338', '汉源县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2881', '338', '石棉县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2882', '338', '天全县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2883', '338', '芦山县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2884', '338', '宝兴县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2885', '339', '翠屏区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2886', '339', '宜宾县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2887', '339', '南溪县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2888', '339', '江安县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2889', '339', '长宁县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2890', '339', '高县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2891', '339', '珙县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2892', '339', '筠连县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2893', '339', '兴文县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2894', '339', '屏山县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2895', '340', '雁江区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2896', '340', '简阳市', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2897', '340', '安岳县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2898', '340', '乐至县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2899', '341', '大安区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2900', '341', '自流井区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2901', '341', '贡井区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2902', '341', '沿滩区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2903', '341', '荣县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2904', '341', '富顺县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2905', '342', '江阳区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2906', '342', '纳溪区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2907', '342', '龙马潭区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2908', '342', '泸县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2909', '342', '合江县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2910', '342', '叙永县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2911', '342', '古蔺县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2912', '343', '和平区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2913', '343', '河西区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2914', '343', '南开区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2915', '343', '河北区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2916', '343', '河东区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2917', '343', '红桥区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2918', '343', '东丽区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2919', '343', '津南区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2920', '343', '西青区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2921', '343', '北辰区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2922', '343', '塘沽区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2923', '343', '汉沽区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2924', '343', '大港区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2925', '343', '武清区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2926', '343', '宝坻区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2927', '343', '经济开发区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2928', '343', '宁河县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2929', '343', '静海县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2930', '343', '蓟县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2931', '344', '城关区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2932', '344', '林周县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2933', '344', '当雄县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2934', '344', '尼木县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2935', '344', '曲水县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2936', '344', '堆龙德庆县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2937', '344', '达孜县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2938', '344', '墨竹工卡县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2939', '345', '噶尔县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2940', '345', '普兰县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2941', '345', '札达县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2942', '345', '日土县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2943', '345', '革吉县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2944', '345', '改则县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2945', '345', '措勤县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2946', '346', '昌都县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2947', '346', '江达县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2948', '346', '贡觉县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2949', '346', '类乌齐县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2950', '346', '丁青县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2951', '346', '察雅县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2952', '346', '八宿县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2953', '346', '左贡县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2954', '346', '芒康县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2955', '346', '洛隆县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2956', '346', '边坝县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2957', '347', '林芝县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2958', '347', '工布江达县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2959', '347', '米林县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2960', '347', '墨脱县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2961', '347', '波密县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2962', '347', '察隅县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2963', '347', '朗县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2964', '348', '那曲县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2965', '348', '嘉黎县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2966', '348', '比如县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2967', '348', '聂荣县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2968', '348', '安多县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2969', '348', '申扎县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2970', '348', '索县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2971', '348', '班戈县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2972', '348', '巴青县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2973', '348', '尼玛县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2974', '349', '日喀则市', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2975', '349', '南木林县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2976', '349', '江孜县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2977', '349', '定日县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2978', '349', '萨迦县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2979', '349', '拉孜县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2980', '349', '昂仁县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2981', '349', '谢通门县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2982', '349', '白朗县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2983', '349', '仁布县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2984', '349', '康马县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2985', '349', '定结县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2986', '349', '仲巴县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2987', '349', '亚东县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2988', '349', '吉隆县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2989', '349', '聂拉木县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2990', '349', '萨嘎县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2991', '349', '岗巴县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2992', '350', '乃东县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2993', '350', '扎囊县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2994', '350', '贡嘎县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2995', '350', '桑日县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2996', '350', '琼结县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2997', '350', '曲松县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2998', '350', '措美县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('2999', '350', '洛扎县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3000', '350', '加查县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3001', '350', '隆子县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3002', '350', '错那县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3003', '350', '浪卡子县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3004', '351', '天山区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3005', '351', '沙依巴克区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3006', '351', '新市区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3007', '351', '水磨沟区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3008', '351', '头屯河区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3009', '351', '达坂城区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3010', '351', '米东区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3011', '351', '乌鲁木齐县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3012', '352', '阿克苏市', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3013', '352', '温宿县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3014', '352', '库车县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3015', '352', '沙雅县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3016', '352', '新和县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3017', '352', '拜城县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3018', '352', '乌什县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3019', '352', '阿瓦提县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3020', '352', '柯坪县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3021', '353', '阿拉尔市', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3022', '354', '库尔勒市', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3023', '354', '轮台县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3024', '354', '尉犁县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3025', '354', '若羌县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3026', '354', '且末县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3027', '354', '焉耆', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3028', '354', '和静县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3029', '354', '和硕县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3030', '354', '博湖县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3031', '355', '博乐市', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3032', '355', '精河县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3033', '355', '温泉县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3034', '356', '呼图壁县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3035', '356', '米泉市', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3036', '356', '昌吉市', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3037', '356', '阜康市', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3038', '356', '玛纳斯县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3039', '356', '奇台县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3040', '356', '吉木萨尔县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3041', '356', '木垒', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3042', '357', '哈密市', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3043', '357', '伊吾县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3044', '357', '巴里坤', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3045', '358', '和田市', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3046', '358', '和田县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3047', '358', '墨玉县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3048', '358', '皮山县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3049', '358', '洛浦县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3050', '358', '策勒县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3051', '358', '于田县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3052', '358', '民丰县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3053', '359', '喀什市', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3054', '359', '疏附县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3055', '359', '疏勒县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3056', '359', '英吉沙县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3057', '359', '泽普县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3058', '359', '莎车县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3059', '359', '叶城县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3060', '359', '麦盖提县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3061', '359', '岳普湖县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3062', '359', '伽师县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3063', '359', '巴楚县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3064', '359', '塔什库尔干', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3065', '360', '克拉玛依市', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3066', '361', '阿图什市', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3067', '361', '阿克陶县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3068', '361', '阿合奇县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3069', '361', '乌恰县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3070', '362', '石河子市', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3071', '363', '图木舒克市', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3072', '364', '吐鲁番市', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3073', '364', '鄯善县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3074', '364', '托克逊县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3075', '365', '五家渠市', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3076', '366', '阿勒泰市', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3077', '366', '布克赛尔', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3078', '366', '伊宁市', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3079', '366', '布尔津县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3080', '366', '奎屯市', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3081', '366', '乌苏市', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3082', '366', '额敏县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3083', '366', '富蕴县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3084', '366', '伊宁县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3085', '366', '福海县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3086', '366', '霍城县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3087', '366', '沙湾县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3088', '366', '巩留县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3089', '366', '哈巴河县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3090', '366', '托里县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3091', '366', '青河县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3092', '366', '新源县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3093', '366', '裕民县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3094', '366', '和布克赛尔', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3095', '366', '吉木乃县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3096', '366', '昭苏县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3097', '366', '特克斯县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3098', '366', '尼勒克县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3099', '366', '察布查尔', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3100', '367', '盘龙区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3101', '367', '五华区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3102', '367', '官渡区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3103', '367', '西山区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3104', '367', '东川区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3105', '367', '安宁市', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3106', '367', '呈贡县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3107', '367', '晋宁县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3108', '367', '富民县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3109', '367', '宜良县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3110', '367', '嵩明县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3111', '367', '石林县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3112', '367', '禄劝', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3113', '367', '寻甸', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3114', '368', '兰坪', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3115', '368', '泸水县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3116', '368', '福贡县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3117', '368', '贡山', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3118', '369', '宁洱', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3119', '369', '思茅区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3120', '369', '墨江', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3121', '369', '景东', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3122', '369', '景谷', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3123', '369', '镇沅', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3124', '369', '江城', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3125', '369', '孟连', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3126', '369', '澜沧', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3127', '369', '西盟', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3128', '370', '古城区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3129', '370', '宁蒗', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3130', '370', '玉龙', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3131', '370', '永胜县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3132', '370', '华坪县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3133', '371', '隆阳区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3134', '371', '施甸县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3135', '371', '腾冲县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3136', '371', '龙陵县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3137', '371', '昌宁县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3138', '372', '楚雄市', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3139', '372', '双柏县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3140', '372', '牟定县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3141', '372', '南华县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3142', '372', '姚安县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3143', '372', '大姚县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3144', '372', '永仁县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3145', '372', '元谋县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3146', '372', '武定县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3147', '372', '禄丰县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3148', '373', '大理市', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3149', '373', '祥云县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3150', '373', '宾川县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3151', '373', '弥渡县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3152', '373', '永平县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3153', '373', '云龙县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3154', '373', '洱源县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3155', '373', '剑川县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3156', '373', '鹤庆县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3157', '373', '漾濞', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3158', '373', '南涧', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3159', '373', '巍山', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3160', '374', '潞西市', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3161', '374', '瑞丽市', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3162', '374', '梁河县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3163', '374', '盈江县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3164', '374', '陇川县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3165', '375', '香格里拉县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3166', '375', '德钦县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3167', '375', '维西', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3168', '376', '泸西县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3169', '376', '蒙自县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3170', '376', '个旧市', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3171', '376', '开远市', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3172', '376', '绿春县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3173', '376', '建水县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3174', '376', '石屏县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3175', '376', '弥勒县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3176', '376', '元阳县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3177', '376', '红河县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3178', '376', '金平', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3179', '376', '河口', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3180', '376', '屏边', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3181', '377', '临翔区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3182', '377', '凤庆县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3183', '377', '云县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3184', '377', '永德县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3185', '377', '镇康县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3186', '377', '双江', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3187', '377', '耿马', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3188', '377', '沧源', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3189', '378', '麒麟区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3190', '378', '宣威市', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3191', '378', '马龙县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3192', '378', '陆良县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3193', '378', '师宗县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3194', '378', '罗平县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3195', '378', '富源县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3196', '378', '会泽县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3197', '378', '沾益县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3198', '379', '文山县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3199', '379', '砚山县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3200', '379', '西畴县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3201', '379', '麻栗坡县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3202', '379', '马关县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3203', '379', '丘北县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3204', '379', '广南县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3205', '379', '富宁县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3206', '380', '景洪市', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3207', '380', '勐海县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3208', '380', '勐腊县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3209', '381', '红塔区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3210', '381', '江川县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3211', '381', '澄江县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3212', '381', '通海县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3213', '381', '华宁县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3214', '381', '易门县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3215', '381', '峨山', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3216', '381', '新平', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3217', '381', '元江', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3218', '382', '昭阳区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3219', '382', '鲁甸县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3220', '382', '巧家县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3221', '382', '盐津县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3222', '382', '大关县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3223', '382', '永善县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3224', '382', '绥江县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3225', '382', '镇雄县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3226', '382', '彝良县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3227', '382', '威信县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3228', '382', '水富县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3229', '383', '西湖区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3230', '383', '上城区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3231', '383', '下城区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3232', '383', '拱墅区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3233', '383', '滨江区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3234', '383', '江干区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3235', '383', '萧山区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3236', '383', '余杭区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3237', '383', '市郊', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3238', '383', '建德市', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3239', '383', '富阳市', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3240', '383', '临安市', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3241', '383', '桐庐县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3242', '383', '淳安县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3243', '384', '吴兴区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3244', '384', '南浔区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3245', '384', '德清县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3246', '384', '长兴县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3247', '384', '安吉县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3248', '385', '南湖区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3249', '385', '秀洲区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3250', '385', '海宁市', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3251', '385', '嘉善县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3252', '385', '平湖市', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3253', '385', '桐乡市', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3254', '385', '海盐县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3255', '386', '婺城区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3256', '386', '金东区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3257', '386', '兰溪市', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3258', '386', '市区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3259', '386', '佛堂镇', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3260', '386', '上溪镇', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3261', '386', '义亭镇', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3262', '386', '大陈镇', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3263', '386', '苏溪镇', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3264', '386', '赤岸镇', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3265', '386', '东阳市', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3266', '386', '永康市', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3267', '386', '武义县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3268', '386', '浦江县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3269', '386', '磐安县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3270', '387', '莲都区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3271', '387', '龙泉市', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3272', '387', '青田县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3273', '387', '缙云县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3274', '387', '遂昌县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3275', '387', '松阳县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3276', '387', '云和县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3277', '387', '庆元县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3278', '387', '景宁', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3279', '388', '海曙区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3280', '388', '江东区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3281', '388', '江北区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3282', '388', '镇海区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3283', '388', '北仑区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3284', '388', '鄞州区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3285', '388', '余姚市', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3286', '388', '慈溪市', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3287', '388', '奉化市', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3288', '388', '象山县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3289', '388', '宁海县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3290', '389', '越城区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3291', '389', '上虞市', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3292', '389', '嵊州市', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3293', '389', '绍兴县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3294', '389', '新昌县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3295', '389', '诸暨市', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3296', '390', '椒江区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3297', '390', '黄岩区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3298', '390', '路桥区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3299', '390', '温岭市', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3300', '390', '临海市', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3301', '390', '玉环县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3302', '390', '三门县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3303', '390', '天台县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3304', '390', '仙居县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3305', '391', '鹿城区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3306', '391', '龙湾区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3307', '391', '瓯海区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3308', '391', '瑞安市', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3309', '391', '乐清市', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3310', '391', '洞头县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3311', '391', '永嘉县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3312', '391', '平阳县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3313', '391', '苍南县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3314', '391', '文成县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3315', '391', '泰顺县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3316', '392', '定海区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3317', '392', '普陀区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3318', '392', '岱山县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3319', '392', '嵊泗县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3320', '393', '衢州市', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3321', '393', '江山市', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3322', '393', '常山县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3323', '393', '开化县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3324', '393', '龙游县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3325', '394', '合川区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3326', '394', '江津区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3327', '394', '南川区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3328', '394', '永川区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3329', '394', '南岸区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3330', '394', '渝北区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3331', '394', '万盛区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3332', '394', '大渡口区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3333', '394', '万州区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3334', '394', '北碚区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3335', '394', '沙坪坝区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3336', '394', '巴南区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3337', '394', '涪陵区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3338', '394', '江北区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3339', '394', '九龙坡区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3340', '394', '渝中区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3341', '394', '黔江开发区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3342', '394', '长寿区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3343', '394', '双桥区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3344', '394', '綦江县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3345', '394', '潼南县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3346', '394', '铜梁县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3347', '394', '大足县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3348', '394', '荣昌县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3349', '394', '璧山县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3350', '394', '垫江县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3351', '394', '武隆县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3352', '394', '丰都县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3353', '394', '城口县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3354', '394', '梁平县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3355', '394', '开县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3356', '394', '巫溪县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3357', '394', '巫山县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3358', '394', '奉节县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3359', '394', '云阳县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3360', '394', '忠县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3361', '394', '石柱', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3362', '394', '彭水', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3363', '394', '酉阳', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3364', '394', '秀山', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3365', '395', '沙田区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3366', '395', '东区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3367', '395', '观塘区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3368', '395', '黄大仙区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3369', '395', '九龙城区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3370', '395', '屯门区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3371', '395', '葵青区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3372', '395', '元朗区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3373', '395', '深水埗区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3374', '395', '西贡区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3375', '395', '大埔区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3376', '395', '湾仔区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3377', '395', '油尖旺区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3378', '395', '北区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3379', '395', '南区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3380', '395', '荃湾区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3381', '395', '中西区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3382', '395', '离岛区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3383', '396', '澳门', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3384', '397', '台北', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3385', '397', '高雄', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3386', '397', '基隆', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3387', '397', '台中', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3388', '397', '台南', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3389', '397', '新竹', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3390', '397', '嘉义', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3391', '397', '宜兰县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3392', '397', '桃园县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3393', '397', '苗栗县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3394', '397', '彰化县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3395', '397', '南投县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3396', '397', '云林县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3397', '397', '屏东县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3398', '397', '台东县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3399', '397', '花莲县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3400', '397', '澎湖县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('1', '0', '中国', '1');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3401', '3', '合肥', '3');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3402', '3401', '肥东县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3403', '3401', '庐江县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3404', '3401', '包河区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3405', '3401', '蜀山区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3406', '3401', '瑶海区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3407', '3401', '庐阳区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3408', '3401', '经济技术开发区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3409', '3401', '高新技术开发区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3410', '3401', '北城新区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3411', '3401', '滨湖新区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3412', '3401', '政务文化新区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3413', '3401', '新站综合开发试验区', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3414', '3401', '肥西县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3415', '3401', '巢湖市', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3416', '3401', '长丰县', '4');
INSERT INTO `%DB_PREFIX%delivery_region` VALUES ('3417', '386', '义乌', '4');

-- ----------------------------
-- Table structure for `%DB_PREFIX%demotion_record`
-- ----------------------------
DROP TABLE IF EXISTS `%DB_PREFIX%demotion_record`;
CREATE TABLE `%DB_PREFIX%demotion_record` (
  `id` int(11) NOT NULL auto_increment,
  `user_id` int(11) NOT NULL COMMENT '会员ID',
  `original_vip_id` varchar(11) NOT NULL COMMENT 'VIP原等级',
  `now_vip_id` int(11) NOT NULL COMMENT '降级后等级',
  `causes` varchar(255) NOT NULL COMMENT '降级原因',
  `start_date` date NOT NULL COMMENT '降级开始日期',
  `eresume_date` date NOT NULL COMMENT '预计恢复日期',
  `arecovery_date` date NOT NULL COMMENT '实际恢复日期',
  `status` tinyint(1) NOT NULL COMMENT '状态(0降级、1已恢复等级)',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='VIP降级记录表';

-- ----------------------------
-- Records of fanwe_demotion_record
-- ----------------------------

-- ----------------------------
-- Table structure for `%DB_PREFIX%ecv`
-- ----------------------------
DROP TABLE IF EXISTS `%DB_PREFIX%ecv`;
CREATE TABLE `%DB_PREFIX%ecv` (
  `id` int(11) NOT NULL auto_increment,
  `sn` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `use_limit` int(11) NOT NULL,
  `use_count` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `begin_time` int(11) NOT NULL,
  `end_time` int(11) NOT NULL,
  `money` decimal(20,4) NOT NULL,
  `ecv_type_id` int(11) NOT NULL,
  PRIMARY KEY  (`id`),
  UNIQUE KEY `unk_sn` (`sn`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='优惠券列表';

-- ----------------------------
-- Records of fanwe_ecv
-- ----------------------------

-- ----------------------------
-- Table structure for `%DB_PREFIX%ecv_type`
-- ----------------------------
DROP TABLE IF EXISTS `%DB_PREFIX%ecv_type`;
CREATE TABLE `%DB_PREFIX%ecv_type` (
  `id` int(11) NOT NULL auto_increment,
  `name` varchar(255) NOT NULL,
  `money` decimal(20,4) NOT NULL,
  `use_limit` int(11) NOT NULL,
  `begin_time` int(11) NOT NULL,
  `end_time` int(11) NOT NULL,
  `gen_count` int(11) NOT NULL,
  `send_type` tinyint(1) NOT NULL,
  `exchange_score` int(11) NOT NULL,
  `exchange_limit` int(11) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='优惠券类型';

-- ----------------------------
-- Records of fanwe_ecv_type
-- ----------------------------

-- ----------------------------
-- Table structure for `%DB_PREFIX%email_verify_code`
-- ----------------------------
DROP TABLE IF EXISTS `%DB_PREFIX%email_verify_code`;
CREATE TABLE `%DB_PREFIX%email_verify_code` (
  `id` int(11) NOT NULL auto_increment,
  `verify_code` varchar(10) NOT NULL,
  `email` varchar(100) NOT NULL,
  `create_time` int(11) NOT NULL,
  `client_ip` varchar(30) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of fanwe_email_verify_code
-- ----------------------------

-- ----------------------------
-- Table structure for `%DB_PREFIX%expression`
-- ----------------------------
DROP TABLE IF EXISTS `%DB_PREFIX%expression`;
CREATE TABLE `%DB_PREFIX%expression` (
  `id` int(11) NOT NULL auto_increment,
  `title` varchar(255) NOT NULL,
  `type` varchar(255) NOT NULL default 'tusiji',
  `emotion` varchar(255) NOT NULL,
  `filename` varchar(255) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=135 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of fanwe_expression
-- ----------------------------
INSERT INTO `%DB_PREFIX%expression` VALUES ('19', '傲慢', 'qq', '[傲慢]', 'aoman.gif');
INSERT INTO `%DB_PREFIX%expression` VALUES ('20', '白眼', 'qq', '[白眼]', 'baiyan.gif');
INSERT INTO `%DB_PREFIX%expression` VALUES ('21', '鄙视', 'qq', '[鄙视]', 'bishi.gif');
INSERT INTO `%DB_PREFIX%expression` VALUES ('22', '闭嘴', 'qq', '[闭嘴]', 'bizui.gif');
INSERT INTO `%DB_PREFIX%expression` VALUES ('23', '擦汗', 'qq', '[擦汗]', 'cahan.gif');
INSERT INTO `%DB_PREFIX%expression` VALUES ('24', '菜刀', 'qq', '[菜刀]', 'caidao.gif');
INSERT INTO `%DB_PREFIX%expression` VALUES ('25', '差劲', 'qq', '[差劲]', 'chajin.gif');
INSERT INTO `%DB_PREFIX%expression` VALUES ('26', '欢庆', 'qq', '[欢庆]', 'cheer.gif');
INSERT INTO `%DB_PREFIX%expression` VALUES ('27', '虫子', 'qq', '[虫子]', 'chong.gif');
INSERT INTO `%DB_PREFIX%expression` VALUES ('28', '呲牙', 'qq', '[呲牙]', 'ciya.gif');
INSERT INTO `%DB_PREFIX%expression` VALUES ('29', '捶打', 'qq', '[捶打]', 'da.gif');
INSERT INTO `%DB_PREFIX%expression` VALUES ('30', '大便', 'qq', '[大便]', 'dabian.gif');
INSERT INTO `%DB_PREFIX%expression` VALUES ('31', '大兵', 'qq', '[大兵]', 'dabing.gif');
INSERT INTO `%DB_PREFIX%expression` VALUES ('32', '大叫', 'qq', '[大叫]', 'dajiao.gif');
INSERT INTO `%DB_PREFIX%expression` VALUES ('33', '大哭', 'qq', '[大哭]', 'daku.gif');
INSERT INTO `%DB_PREFIX%expression` VALUES ('34', '蛋糕', 'qq', '[蛋糕]', 'dangao.gif');
INSERT INTO `%DB_PREFIX%expression` VALUES ('35', '发怒', 'qq', '[发怒]', 'fanu.gif');
INSERT INTO `%DB_PREFIX%expression` VALUES ('36', '刀', 'qq', '[刀]', 'dao.gif');
INSERT INTO `%DB_PREFIX%expression` VALUES ('37', '得意', 'qq', '[得意]', 'deyi.gif');
INSERT INTO `%DB_PREFIX%expression` VALUES ('38', '凋谢', 'qq', '[凋谢]', 'diaoxie.gif');
INSERT INTO `%DB_PREFIX%expression` VALUES ('39', '饿', 'qq', '[饿]', 'er.gif');
INSERT INTO `%DB_PREFIX%expression` VALUES ('40', '发呆', 'qq', '[发呆]', 'fadai.gif');
INSERT INTO `%DB_PREFIX%expression` VALUES ('41', '发抖', 'qq', '[发抖]', 'fadou.gif');
INSERT INTO `%DB_PREFIX%expression` VALUES ('42', '饭', 'qq', '[饭]', 'fan.gif');
INSERT INTO `%DB_PREFIX%expression` VALUES ('43', '飞吻', 'qq', '[飞吻]', 'feiwen.gif');
INSERT INTO `%DB_PREFIX%expression` VALUES ('44', '奋斗', 'qq', '[奋斗]', 'fendou.gif');
INSERT INTO `%DB_PREFIX%expression` VALUES ('45', '尴尬', 'qq', '[尴尬]', 'gangga.gif');
INSERT INTO `%DB_PREFIX%expression` VALUES ('46', '给力', 'qq', '[给力]', 'geili.gif');
INSERT INTO `%DB_PREFIX%expression` VALUES ('47', '勾引', 'qq', '[勾引]', 'gouyin.gif');
INSERT INTO `%DB_PREFIX%expression` VALUES ('48', '鼓掌', 'qq', '[鼓掌]', 'guzhang.gif');
INSERT INTO `%DB_PREFIX%expression` VALUES ('49', '哈哈', 'qq', '[哈哈]', 'haha.gif');
INSERT INTO `%DB_PREFIX%expression` VALUES ('50', '害羞', 'qq', '[害羞]', 'haixiu.gif');
INSERT INTO `%DB_PREFIX%expression` VALUES ('51', '哈欠', 'qq', '[哈欠]', 'haqian.gif');
INSERT INTO `%DB_PREFIX%expression` VALUES ('52', '花', 'qq', '[花]', 'hua.gif');
INSERT INTO `%DB_PREFIX%expression` VALUES ('53', '坏笑', 'qq', '[坏笑]', 'huaixiao.gif');
INSERT INTO `%DB_PREFIX%expression` VALUES ('54', '挥手', 'qq', '[挥手]', 'huishou.gif');
INSERT INTO `%DB_PREFIX%expression` VALUES ('55', '回头', 'qq', '[回头]', 'huitou.gif');
INSERT INTO `%DB_PREFIX%expression` VALUES ('56', '激动', 'qq', '[激动]', 'jidong.gif');
INSERT INTO `%DB_PREFIX%expression` VALUES ('57', '惊恐', 'qq', '[惊恐]', 'jingkong.gif');
INSERT INTO `%DB_PREFIX%expression` VALUES ('58', '惊讶', 'qq', '[惊讶]', 'jingya.gif');
INSERT INTO `%DB_PREFIX%expression` VALUES ('59', '咖啡', 'qq', '[咖啡]', 'kafei.gif');
INSERT INTO `%DB_PREFIX%expression` VALUES ('60', '可爱', 'qq', '[可爱]', 'keai.gif');
INSERT INTO `%DB_PREFIX%expression` VALUES ('61', '可怜', 'qq', '[可怜]', 'kelian.gif');
INSERT INTO `%DB_PREFIX%expression` VALUES ('62', '磕头', 'qq', '[磕头]', 'ketou.gif');
INSERT INTO `%DB_PREFIX%expression` VALUES ('63', '示爱', 'qq', '[示爱]', 'kiss.gif');
INSERT INTO `%DB_PREFIX%expression` VALUES ('64', '酷', 'qq', '[酷]', 'ku.gif');
INSERT INTO `%DB_PREFIX%expression` VALUES ('65', '难过', 'qq', '[难过]', 'kuaikule.gif');
INSERT INTO `%DB_PREFIX%expression` VALUES ('66', '骷髅', 'qq', '[骷髅]', 'kulou.gif');
INSERT INTO `%DB_PREFIX%expression` VALUES ('67', '困', 'qq', '[困]', 'kun.gif');
INSERT INTO `%DB_PREFIX%expression` VALUES ('68', '篮球', 'qq', '[篮球]', 'lanqiu.gif');
INSERT INTO `%DB_PREFIX%expression` VALUES ('69', '冷汗', 'qq', '[冷汗]', 'lenghan.gif');
INSERT INTO `%DB_PREFIX%expression` VALUES ('70', '流汗', 'qq', '[流汗]', 'liuhan.gif');
INSERT INTO `%DB_PREFIX%expression` VALUES ('71', '流泪', 'qq', '[流泪]', 'liulei.gif');
INSERT INTO `%DB_PREFIX%expression` VALUES ('72', '礼物', 'qq', '[礼物]', 'liwu.gif');
INSERT INTO `%DB_PREFIX%expression` VALUES ('73', '爱心', 'qq', '[爱心]', 'love.gif');
INSERT INTO `%DB_PREFIX%expression` VALUES ('74', '骂人', 'qq', '[骂人]', 'ma.gif');
INSERT INTO `%DB_PREFIX%expression` VALUES ('75', '不开心', 'qq', '[不开心]', 'nanguo.gif');
INSERT INTO `%DB_PREFIX%expression` VALUES ('76', '不好', 'qq', '[不好]', 'no.gif');
INSERT INTO `%DB_PREFIX%expression` VALUES ('77', '很好', 'qq', '[很好]', 'ok.gif');
INSERT INTO `%DB_PREFIX%expression` VALUES ('78', '佩服', 'qq', '[佩服]', 'peifu.gif');
INSERT INTO `%DB_PREFIX%expression` VALUES ('79', '啤酒', 'qq', '[啤酒]', 'pijiu.gif');
INSERT INTO `%DB_PREFIX%expression` VALUES ('80', '乒乓', 'qq', '[乒乓]', 'pingpang.gif');
INSERT INTO `%DB_PREFIX%expression` VALUES ('81', '撇嘴', 'qq', '[撇嘴]', 'pizui.gif');
INSERT INTO `%DB_PREFIX%expression` VALUES ('82', '强', 'qq', '[强]', 'qiang.gif');
INSERT INTO `%DB_PREFIX%expression` VALUES ('83', '亲亲', 'qq', '[亲亲]', 'qinqin.gif');
INSERT INTO `%DB_PREFIX%expression` VALUES ('84', '出丑', 'qq', '[出丑]', 'qioudale.gif');
INSERT INTO `%DB_PREFIX%expression` VALUES ('85', '足球', 'qq', '[足球]', 'qiu.gif');
INSERT INTO `%DB_PREFIX%expression` VALUES ('86', '拳头', 'qq', '[拳头]', 'quantou.gif');
INSERT INTO `%DB_PREFIX%expression` VALUES ('87', '弱', 'qq', '[弱]', 'ruo.gif');
INSERT INTO `%DB_PREFIX%expression` VALUES ('88', '色', 'qq', '[色]', 'se.gif');
INSERT INTO `%DB_PREFIX%expression` VALUES ('89', '闪电', 'qq', '[闪电]', 'shandian.gif');
INSERT INTO `%DB_PREFIX%expression` VALUES ('90', '胜利', 'qq', '[胜利]', 'shengli.gif');
INSERT INTO `%DB_PREFIX%expression` VALUES ('91', '衰', 'qq', '[衰]', 'shuai.gif');
INSERT INTO `%DB_PREFIX%expression` VALUES ('92', '睡觉', 'qq', '[睡觉]', 'shuijiao.gif');
INSERT INTO `%DB_PREFIX%expression` VALUES ('93', '太阳', 'qq', '[太阳]', 'taiyang.gif');
INSERT INTO `%DB_PREFIX%expression` VALUES ('96', '啊', 'tusiji', '[啊]', 'aa.gif');
INSERT INTO `%DB_PREFIX%expression` VALUES ('97', '暗爽', 'tusiji', '[暗爽]', 'anshuang.gif');
INSERT INTO `%DB_PREFIX%expression` VALUES ('98', 'byebye', 'tusiji', '[byebye]', 'baibai.gif');
INSERT INTO `%DB_PREFIX%expression` VALUES ('99', '不行', 'tusiji', '[不行]', 'buxing.gif');
INSERT INTO `%DB_PREFIX%expression` VALUES ('100', '戳眼', 'tusiji', '[戳眼]', 'chuoyan.gif');
INSERT INTO `%DB_PREFIX%expression` VALUES ('101', '很得意', 'tusiji', '[很得意]', 'deyi.gif');
INSERT INTO `%DB_PREFIX%expression` VALUES ('102', '顶', 'tusiji', '[顶]', 'ding.gif');
INSERT INTO `%DB_PREFIX%expression` VALUES ('103', '抖抖', 'tusiji', '[抖抖]', 'douxiong.gif');
INSERT INTO `%DB_PREFIX%expression` VALUES ('104', '哼', 'tusiji', '[哼]', 'heng.gif');
INSERT INTO `%DB_PREFIX%expression` VALUES ('105', '挥汗', 'tusiji', '[挥汗]', 'huihan.gif');
INSERT INTO `%DB_PREFIX%expression` VALUES ('106', '昏迷', 'tusiji', '[昏迷]', 'hunmi.gif');
INSERT INTO `%DB_PREFIX%expression` VALUES ('107', '互拍', 'tusiji', '[互拍]', 'hupai.gif');
INSERT INTO `%DB_PREFIX%expression` VALUES ('108', '瞌睡', 'tusiji', '[瞌睡]', 'keshui.gif');
INSERT INTO `%DB_PREFIX%expression` VALUES ('109', '笼子', 'tusiji', '[笼子]', 'longzi.gif');
INSERT INTO `%DB_PREFIX%expression` VALUES ('110', '听歌', 'tusiji', '[听歌]', 'music.gif');
INSERT INTO `%DB_PREFIX%expression` VALUES ('111', '奶瓶', 'tusiji', '[奶瓶]', 'naiping.gif');
INSERT INTO `%DB_PREFIX%expression` VALUES ('112', '扭背', 'tusiji', '[扭背]', 'niubei.gif');
INSERT INTO `%DB_PREFIX%expression` VALUES ('113', '拍砖', 'tusiji', '[拍砖]', 'paizhuan.gif');
INSERT INTO `%DB_PREFIX%expression` VALUES ('114', '飘过', 'tusiji', '[飘过]', 'piaoguo.gif');
INSERT INTO `%DB_PREFIX%expression` VALUES ('115', '揉脸', 'tusiji', '[揉脸]', 'roulian.gif');
INSERT INTO `%DB_PREFIX%expression` VALUES ('116', '闪闪', 'tusiji', '[闪闪]', 'shanshan.gif');
INSERT INTO `%DB_PREFIX%expression` VALUES ('117', '生日', 'tusiji', '[生日]', 'shengri.gif');
INSERT INTO `%DB_PREFIX%expression` VALUES ('118', '摊手', 'tusiji', '[摊手]', 'tanshou.gif');
INSERT INTO `%DB_PREFIX%expression` VALUES ('119', '躺坐', 'tusiji', '[躺坐]', 'tanzuo.gif');
INSERT INTO `%DB_PREFIX%expression` VALUES ('120', '歪头', 'tusiji', '[歪头]', 'waitou.gif');
INSERT INTO `%DB_PREFIX%expression` VALUES ('121', '我踢', 'tusiji', '[我踢]', 'woti.gif');
INSERT INTO `%DB_PREFIX%expression` VALUES ('122', '无聊', 'tusiji', '[无聊]', 'wuliao.gif');
INSERT INTO `%DB_PREFIX%expression` VALUES ('123', '醒醒', 'tusiji', '[醒醒]', 'xingxing.gif');
INSERT INTO `%DB_PREFIX%expression` VALUES ('124', '睡了', 'tusiji', '[睡了]', 'xixishui.gif');
INSERT INTO `%DB_PREFIX%expression` VALUES ('125', '旋转', 'tusiji', '[旋转]', 'xuanzhuan.gif');
INSERT INTO `%DB_PREFIX%expression` VALUES ('126', '摇晃', 'tusiji', '[摇晃]', 'yaohuang.gif');
INSERT INTO `%DB_PREFIX%expression` VALUES ('127', '耶', 'tusiji', '[耶]', 'yeah.gif');
INSERT INTO `%DB_PREFIX%expression` VALUES ('128', '郁闷', 'tusiji', '[郁闷]', 'yumen.gif');
INSERT INTO `%DB_PREFIX%expression` VALUES ('129', '晕厥', 'tusiji', '[晕厥]', 'yunjue.gif');
INSERT INTO `%DB_PREFIX%expression` VALUES ('130', '砸', 'tusiji', '[砸]', 'za.gif');
INSERT INTO `%DB_PREFIX%expression` VALUES ('131', '震荡', 'tusiji', '[震荡]', 'zhendang.gif');
INSERT INTO `%DB_PREFIX%expression` VALUES ('132', '撞墙', 'tusiji', '[撞墙]', 'zhuangqiang.gif');
INSERT INTO `%DB_PREFIX%expression` VALUES ('133', '转头', 'tusiji', '[转头]', 'zhuantou.gif');
INSERT INTO `%DB_PREFIX%expression` VALUES ('134', '抓墙', 'tusiji', '[抓墙]', 'zhuaqiang.gif');

-- ----------------------------
-- Table structure for `%DB_PREFIX%generation_repay`
-- ----------------------------
DROP TABLE IF EXISTS `%DB_PREFIX%generation_repay`;
CREATE TABLE `%DB_PREFIX%generation_repay` (
  `id` int(11) NOT NULL auto_increment,
  `deal_id` int(11) NOT NULL,
  `repay_id` int(11) NOT NULL COMMENT '第几期',
  `admin_id` int(11) NOT NULL COMMENT '管理员ID',
  `agency_id` int(11) NOT NULL COMMENT '担保机构ID',
  `self_money` decimal(20,2) NOT NULL,
  `interest_money` decimal(20,2) NOT NULL,
  `repay_money` decimal(20,2) NOT NULL COMMENT '代还多少本息',
  `manage_money` decimal(20,2) NOT NULL COMMENT '代换多少管理费',
  `impose_money` decimal(20,2) NOT NULL COMMENT '代还多少罚息',
  `manage_impose_money` decimal(20,2) NOT NULL COMMENT '代换多少逾期管理费',
  `create_time` int(11) NOT NULL COMMENT '代还时间',
  `create_date` date NOT NULL,
  `status` tinyint(4) NOT NULL COMMENT '0待收款 1已收款 ',
  `memo` text NOT NULL COMMENT '操作备注',
  `total_money_fee` decimal(20,2) NOT NULL COMMENT '垫付罚息',
  `fee_day` int(11) NOT NULL COMMENT '垫付天数',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='代还款记录';

-- ----------------------------
-- Records of fanwe_generation_repay
-- ----------------------------

-- ----------------------------
-- Table structure for `%DB_PREFIX%generation_repay_submit`
-- ----------------------------
DROP TABLE IF EXISTS `%DB_PREFIX%generation_repay_submit`;
CREATE TABLE `%DB_PREFIX%generation_repay_submit` (
  `id` int(11) NOT NULL auto_increment,
  `deal_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL COMMENT '代还多少本息',
  `money` decimal(20,2) NOT NULL,
  `status` tinyint(1) NOT NULL COMMENT '0 未处理 1续约成功 2续约失败',
  `memo` text NOT NULL,
  `op_memo` text NOT NULL COMMENT '操作备注',
  `create_time` int(11) NOT NULL COMMENT '代还时间',
  `update_time` int(11) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='代还款申请';

-- ----------------------------
-- Records of fanwe_generation_repay_submit
-- ----------------------------

-- ----------------------------
-- Table structure for `%DB_PREFIX%gift_record`
-- ----------------------------
DROP TABLE IF EXISTS `%DB_PREFIX%gift_record`;
CREATE TABLE `%DB_PREFIX%gift_record` (
  `id` int(11) NOT NULL auto_increment,
  `deal_id` int(11) NOT NULL COMMENT '借款ID',
  `load_id` int(11) NOT NULL COMMENT '投标记录ID',
  `reward_name` varchar(100) NOT NULL COMMENT '奖励名称',
  `user_id` int(11) NOT NULL COMMENT '所属人ID',
  `gift_type` tinyint(1) NOT NULL COMMENT '收益类型1.红包、2.收益率、3.积分、4.礼品',
  `status` tinyint(1) NOT NULL COMMENT '发放状态 0未发放，1已发放',
  `generation_date` date NOT NULL COMMENT '生成时间',
  `release_date` date NOT NULL COMMENT '发放时间',
  `gift_value` varchar(100) NOT NULL COMMENT '收益值 红包为金额、收益率为百分比、积分为数值、礼品为礼品ID ',
  `reward_money` decimal(20,2) NOT NULL COMMENT '实际获得金额',
  PRIMARY KEY  (`id`),
  KEY `idx_gr_001` USING BTREE (`user_id`,`status`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='投资奖励记录表';

-- ----------------------------
-- Records of fanwe_gift_record
-- ----------------------------

-- ----------------------------
-- Table structure for `%DB_PREFIX%given_record`
-- ----------------------------
DROP TABLE IF EXISTS `%DB_PREFIX%given_record`;
CREATE TABLE `%DB_PREFIX%given_record` (
  `id` int(11) NOT NULL auto_increment,
  `user_id` int(11) NOT NULL COMMENT '会员ID',
  `vip_id` int(11) NOT NULL COMMENT 'VIP等级',
  `given_name_type` tinyint(1) NOT NULL COMMENT '礼品名称类型 1.生日礼品 2.节日礼品',
  `given_type` tinyint(1) NOT NULL COMMENT '礼品类型 1.红包 2.积分',
  `given_value` int(11) NOT NULL COMMENT '礼品值',
  `given_num` int(11) NOT NULL COMMENT '数量',
  `brief` text NOT NULL COMMENT '备注',
  `send_date` date NOT NULL COMMENT '发送日期',
  `send_state` tinyint(1) NOT NULL COMMENT '发送状态 0未发送 1已发送',
  `gift_id` int(11) NOT NULL COMMENT '节日礼品ID',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='礼金记录列表';

-- ----------------------------
-- Records of fanwe_given_record
-- ----------------------------

-- ----------------------------
-- Table structure for `%DB_PREFIX%goods`
-- ----------------------------
DROP TABLE IF EXISTS `%DB_PREFIX%goods`;
CREATE TABLE `%DB_PREFIX%goods` (
  `id` int(11) NOT NULL auto_increment,
  `name` varchar(255) NOT NULL COMMENT '商品名称',
  `sub_name` varchar(255) NOT NULL COMMENT '商品简称',
  `cate_id` int(11) NOT NULL COMMENT '分类ID',
  `img` text NOT NULL COMMENT '商品主图',
  `brief` text NOT NULL COMMENT '商品简介',
  `description` text NOT NULL COMMENT '商品描述',
  `sort` int(11) NOT NULL COMMENT '排序',
  `max_bought` int(11) NOT NULL COMMENT '库存数',
  `user_max_bought` int(11) NOT NULL COMMENT '会员最大购买量按件',
  `score` int(11) NOT NULL COMMENT '购买所需积分',
  `is_delivery` tinyint(1) NOT NULL COMMENT '	是否需要配送；0：否; 1：是',
  `is_hot` tinyint(1) NOT NULL COMMENT '热卖',
  `is_new` tinyint(1) NOT NULL COMMENT '最新',
  `is_recommend` tinyint(1) NOT NULL COMMENT '是否推荐',
  `seo_title` text NOT NULL COMMENT 'SEO自定义标题',
  `seo_keyword` text NOT NULL COMMENT 'SEO自定义关键词',
  `seo_description` text NOT NULL COMMENT 'SEO自定义描述',
  `goods_type_id` int(11) NOT NULL COMMENT '商品属性',
  `invented_number` int(11) NOT NULL COMMENT '虚拟购买数',
  `buy_number` int(11) NOT NULL COMMENT '购买人数',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of fanwe_goods
-- ----------------------------

-- ----------------------------
-- Table structure for `%DB_PREFIX%goods_attr`
-- ----------------------------
DROP TABLE IF EXISTS `%DB_PREFIX%goods_attr`;
CREATE TABLE `%DB_PREFIX%goods_attr` (
  `id` int(11) NOT NULL auto_increment,
  `name` varchar(255) NOT NULL COMMENT '属性名称',
  `goods_type_attr_id` int(11) NOT NULL COMMENT '属性ID',
  `score` int(11) NOT NULL COMMENT '所需积分',
  `goods_id` int(11) NOT NULL COMMENT '商品id',
  `is_checked` tinyint(1) NOT NULL COMMENT '是否有独立库存',
  PRIMARY KEY  (`id`),
  KEY `goods_type_attr_id` USING BTREE (`goods_type_attr_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of fanwe_goods_attr
-- ----------------------------

-- ----------------------------
-- Table structure for `%DB_PREFIX%goods_attr_stock`
-- ----------------------------
DROP TABLE IF EXISTS `%DB_PREFIX%goods_attr_stock`;
CREATE TABLE `%DB_PREFIX%goods_attr_stock` (
  `id` int(11) NOT NULL auto_increment,
  `goods_id` int(11) NOT NULL,
  `attr_cfg` text NOT NULL,
  `stock_cfg` int(11) NOT NULL,
  `attr_str` text NOT NULL,
  `buy_count` int(11) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of fanwe_goods_attr_stock
-- ----------------------------

-- ----------------------------
-- Table structure for `%DB_PREFIX%goods_cate`
-- ----------------------------
DROP TABLE IF EXISTS `%DB_PREFIX%goods_cate`;
CREATE TABLE `%DB_PREFIX%goods_cate` (
  `id` int(11) NOT NULL auto_increment,
  `name` varchar(255) NOT NULL COMMENT '商品分类名称',
  `is_effect` tinyint(1) NOT NULL default '1' COMMENT '是否有效',
  `is_delete` tinyint(1) NOT NULL default '0',
  `pid` int(11) NOT NULL,
  `sort` int(11) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=8 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of fanwe_goods_cate
-- ----------------------------
INSERT INTO `%DB_PREFIX%goods_cate` VALUES ('1', '服装', '1', '0', '0', '0');
INSERT INTO `%DB_PREFIX%goods_cate` VALUES ('2', '电气', '1', '0', '0', '1');
INSERT INTO `%DB_PREFIX%goods_cate` VALUES ('3', '羽绒服', '1', '0', '1', '5');
INSERT INTO `%DB_PREFIX%goods_cate` VALUES ('5', '毛衣', '1', '0', '1', '4');
INSERT INTO `%DB_PREFIX%goods_cate` VALUES ('6', '食品', '1', '0', '0', '3');
INSERT INTO `%DB_PREFIX%goods_cate` VALUES ('7', '水果', '1', '0', '6', '0');

-- ----------------------------
-- Table structure for `%DB_PREFIX%goods_order`
-- ----------------------------
DROP TABLE IF EXISTS `%DB_PREFIX%goods_order`;
CREATE TABLE `%DB_PREFIX%goods_order` (
  `id` int(11) NOT NULL auto_increment,
  `order_sn` varchar(255) NOT NULL COMMENT '订单号',
  `goods_id` int(11) NOT NULL COMMENT '商品id',
  `goods_name` varchar(255) NOT NULL COMMENT '商品名称',
  `score` int(11) NOT NULL COMMENT ' 所需积分',
  `total_score` int(11) NOT NULL COMMENT ' 所需积分',
  `number` int(11) NOT NULL default '1' COMMENT '数量',
  `delivery_sn` varchar(255) NOT NULL COMMENT '快递单号',
  `order_status` tinyint(1) NOT NULL COMMENT '订单状态 0未发货 1已发货 2无效订单 3用户删除',
  `user_id` int(11) NOT NULL COMMENT ' 会员ID',
  `ex_time` int(11) NOT NULL COMMENT '兑换时间',
  `ex_date` date NOT NULL COMMENT '兑换时间Ymd',
  `delivery_time` int(11) NOT NULL COMMENT '发货时间',
  `delivery_date` date NOT NULL COMMENT '发货时间Ymd',
  `delivery_addr` varchar(255) NOT NULL COMMENT '收货地址',
  `delivery_tel` varchar(255) NOT NULL COMMENT '收货电话',
  `delivery_name` varchar(255) NOT NULL COMMENT '收货名称',
  `is_delivery` tinyint(1) NOT NULL COMMENT '是否配送',
  `attr_stock_id` int(11) NOT NULL COMMENT '库存编号',
  `attr` text NOT NULL COMMENT '所选属性',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of fanwe_goods_order
-- ----------------------------

-- ----------------------------
-- Table structure for `%DB_PREFIX%goods_type`
-- ----------------------------
DROP TABLE IF EXISTS `%DB_PREFIX%goods_type`;
CREATE TABLE `%DB_PREFIX%goods_type` (
  `id` int(11) NOT NULL auto_increment,
  `name` varchar(255) NOT NULL COMMENT '商品类型名',
  `is_effect` tinyint(1) NOT NULL default '1',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=8 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of fanwe_goods_type
-- ----------------------------
INSERT INTO `%DB_PREFIX%goods_type` VALUES ('7', '美食', '1');
INSERT INTO `%DB_PREFIX%goods_type` VALUES ('2', '电子', '1');
INSERT INTO `%DB_PREFIX%goods_type` VALUES ('3', '旅游', '1');
INSERT INTO `%DB_PREFIX%goods_type` VALUES ('4', '保险', '1');
INSERT INTO `%DB_PREFIX%goods_type` VALUES ('5', '彩票', '1');

-- ----------------------------
-- Table structure for `%DB_PREFIX%goods_type_attr`
-- ----------------------------
DROP TABLE IF EXISTS `%DB_PREFIX%goods_type_attr`;
CREATE TABLE `%DB_PREFIX%goods_type_attr` (
  `id` int(11) NOT NULL auto_increment,
  `name` varchar(255) NOT NULL COMMENT '商品属性名',
  `input_type` tinyint(1) NOT NULL,
  `preset_value` text NOT NULL,
  `goods_type_id` int(11) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=17 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of fanwe_goods_type_attr
-- ----------------------------
INSERT INTO `%DB_PREFIX%goods_type_attr` VALUES ('1', '颜色 ', '0', '', '2');
INSERT INTO `%DB_PREFIX%goods_type_attr` VALUES ('2', '规格', '0', '', '2');
INSERT INTO `%DB_PREFIX%goods_type_attr` VALUES ('10', '黄金', '0', '', '2');
INSERT INTO `%DB_PREFIX%goods_type_attr` VALUES ('4', '尺寸', '0', '', '2');
INSERT INTO `%DB_PREFIX%goods_type_attr` VALUES ('11', '车险', '0', '', '4');
INSERT INTO `%DB_PREFIX%goods_type_attr` VALUES ('13', '房贷险', '0', '', '4');
INSERT INTO `%DB_PREFIX%goods_type_attr` VALUES ('14', '工伤意外险', '0', '', '4');
INSERT INTO `%DB_PREFIX%goods_type_attr` VALUES ('15', '养老保险', '0', '', '4');
INSERT INTO `%DB_PREFIX%goods_type_attr` VALUES ('16', '偷吃果冻险', '0', '', '4');

-- ----------------------------
-- Table structure for `%DB_PREFIX%ips_create_new_acct`
-- ----------------------------
DROP TABLE IF EXISTS `%DB_PREFIX%ips_create_new_acct`;
CREATE TABLE `%DB_PREFIX%ips_create_new_acct` (
  `id` int(10) NOT NULL auto_increment,
  `user_id` int(11) NOT NULL default '0',
  `user_type` tinyint(1) NOT NULL default '0' COMMENT '0:普通用户fanwe_user.id;1:担保用户fanwe_deal_agency.id',
  `argMerCode` varchar(6) NOT NULL COMMENT '“平台”账号 否 由IPS颁发的商户号 ',
  `pMerBillNo` varchar(30) NOT NULL default '0' COMMENT 'pMerBillNo商户开户流水号 否 商户系统唯一丌重复 针对用户在开户中途中断（开户未完成，但关闭了IPS开 户界面）时，必须重新以相同的商户订单号发起再次开户 ',
  `pIdentType` tinyint(2) default '1' COMMENT '证件类型 否 1#身份证，默认：1',
  `pIdentNo` varchar(20) default NULL COMMENT '证件号码 否 真实身份证 ',
  `pRealName` varchar(30) default NULL COMMENT '姓名 否 真实姓名（中文） ',
  `pMobileNo` varchar(11) default NULL COMMENT '手机号 否 用户发送短信 ',
  `pEmail` varchar(50) default NULL COMMENT '注册邮箱 否 用于登录账号，IPS系统内唯一丌能重复',
  `pSmDate` date default NULL COMMENT '提交日期 否 时间格式“yyyyMMdd”,商户提交日期,。如：20140323 ',
  `pMemo1` varchar(100) default NULL COMMENT '备注 是/否',
  `pMemo2` varchar(100) default NULL COMMENT '备注 是/否',
  `pMemo3` varchar(100) default NULL COMMENT '备注 是/否',
  `pStatus` tinyint(2) default NULL COMMENT '开户状态 否 状态：10#开户成功，5#注册超时，9#开户失败',
  `pBankName` varchar(64) default NULL COMMENT '银行名称 是/否 pErrCode 返回状态为 MG00000F 时返回，用 户在IPS登记的信息 ',
  `pBkAccName` varchar(50) default NULL COMMENT '户名 是/否 pErrCode 返回状态为 MG00000F 时返回，用 户在IPS登记的信息不姓名一致。',
  `pBkAccNo` varchar(4) default NULL COMMENT '银行卡账号 是/否 pErrCode 返回状态为 MG00000F 时返回，用 户在IPS登记的信息。返回卡号后4位。 ',
  `pCardStatus` tinyint(1) default NULL COMMENT '身份证状态 是/否 pErrCode 返回状态为MG00000F时返回。 是否验证成功 F 未验证，Y 验证通过 验证丌 通过 ',
  `pPhStatus` tinyint(1) default NULL COMMENT '手机状态 是/否 pErrCode 返回状态为MG00000F时返回 是否验证成功： F未验 ，Y 验证通过，N验证 丌通过 ',
  `pIpsAcctNo` varchar(30) default NULL COMMENT 'IPS托管平台账 户号 是/否 pErrCode 返回状态为 MG00000F 时返回，由 IPS生成颁发的资金账号。 ',
  `pIpsAcctDate` date default NULL COMMENT 'IPS开户日期 否 pErrCode 返回状态为 MG00000F 时返回，格 式：yyyymmdd ',
  `pMerCode` varchar(6) default NULL COMMENT '“平台”账号 否 由IPS颁发的商户号 ',
  `pErrCode` varchar(8) default NULL COMMENT '处理返回状态 否 MG00000F 操作成功； 其他错误：参考自定义错误码 ',
  `pErrMsg` varchar(255) default NULL COMMENT '返回信息 是/否 MG00000F 操作成功； 其他错误信息：参考自定义错误码 ',
  `is_callback` tinyint(1) NOT NULL default '0' COMMENT '0:未回调处理;1:已回调处理',
  PRIMARY KEY  (`id`,`pMerBillNo`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of fanwe_ips_create_new_acct
-- ----------------------------

-- ----------------------------
-- Table structure for `%DB_PREFIX%ips_do_dp_trade`
-- ----------------------------
DROP TABLE IF EXISTS `%DB_PREFIX%ips_do_dp_trade`;
CREATE TABLE `%DB_PREFIX%ips_do_dp_trade` (
  `id` int(11) NOT NULL auto_increment,
  `user_type` tinyint(1) NOT NULL COMMENT '0:普通用户fanwe_user.id;1:担保用户fanwe_deal_agency.id',
  `user_id` int(11) NOT NULL,
  `pMerCode` varchar(6) default NULL COMMENT '“平台”账号 否 由IPS颁发的商户号 ',
  `pMerBillNo` varchar(30) default NULL COMMENT '商户充值订单号 否 商户系统唯一不重复',
  `pAcctType` tinyint(1) NOT NULL default '1' COMMENT '账户类型 否 固定值为 1，表示为类型为IPS个人账户',
  `pIdentNo` varchar(20) default NULL COMMENT '证件号码 否 真实身份证（个人）/IPS颁发的商户号（商户） 本期考虑个人，商户充值预留，下期增加 ',
  `pRealName` varchar(30) default NULL COMMENT '姓名 否 真实姓名（中文） pIpsAcctNo 30 IPS托管账户号 否 账户类型为1时，IPS托管账户号（个人） ',
  `pIpsAcctNo` varchar(30) default NULL COMMENT 'IPS托管账户号 账户类型为1时，IPS托管账户号（个人）',
  `pTrdDate` date default NULL COMMENT '充值日期 否 格式：YYYYMMDD ',
  `pTrdAmt` decimal(20,2) default '0.00' COMMENT '充值金额 否 金额单位：元，丌能为负，丌允许为0，保留2位小数； 格式：12.00 ',
  `pChannelType` tinyint(1) default '1' COMMENT '充值渠道种类 否 1#网银充值；2#代扣充值 ',
  `pTrdBnkCode` varchar(5) default NULL COMMENT '充值银行 是/否 网银充值的银行列表由IPS提供，对应充值银行的CODE， 具体使用见接口 <<商户端获取银行列表查询(WS)>>， 获取pBankList内容项中“银行卡编号”字段； 代扣充值这里传空； ',
  `pMerFee` decimal(20,2) default '0.00' COMMENT '平台手续费 否 这里是平台向用户收取的费用 金额单位：元，丌能为负，允许为0，保留2位小数； 格式：12.00 ',
  `pIpsFeeType` tinyint(1) default NULL COMMENT '谁付IPS手续费 否 这里是IPS向平台收取的费用 1：平台支付 2：用户支付 ',
  `pMemo1` varchar(100) default NULL,
  `pMemo2` varchar(100) default NULL,
  `pMemo3` varchar(100) default NULL,
  `pIpsBillNo` varchar(30) default NULL COMMENT 'IPS充值订单号 否 由IPS系统生成的唯一流水号',
  `pErrCode` varchar(8) default NULL COMMENT '充值状态 否 MG00000F 操作成功； MG00008F IPS受理中; 其他错误信息：参考自定义错误码',
  `pErrMsg` varchar(100) default NULL COMMENT '返回信息 是/否 MG00000F 操作成功； MG00008F IPS受理中; 其他错误信息：参考自定义错误码',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of fanwe_ips_do_dp_trade
-- ----------------------------

-- ----------------------------
-- Table structure for `%DB_PREFIX%ips_do_dw_trade`
-- ----------------------------
DROP TABLE IF EXISTS `%DB_PREFIX%ips_do_dw_trade`;
CREATE TABLE `%DB_PREFIX%ips_do_dw_trade` (
  `id` int(11) NOT NULL auto_increment,
  `user_type` tinyint(1) NOT NULL default '0' COMMENT '0:普通用户fanwe_user.id;1:担保用户fanwe_deal_agency.id',
  `user_id` int(11) NOT NULL default '0',
  `pMerCode` varchar(6) NOT NULL COMMENT '“平台”账号 否 由IPS颁发的商户号',
  `pMerBillNo` varchar(30) NOT NULL COMMENT '商户提现订单号商户系统唯一不重复',
  `pAcctType` tinyint(1) default '1' COMMENT '账户类型 否 0#机构（暂未开放） ；1#个人',
  `pOutType` tinyint(1) default NULL COMMENT '提现模式 否 1#普通提现；2#定向提现<暂不开放> ',
  `pBidNo` varchar(30) default NULL COMMENT '标号 是/否 提现模式为2时，此字段生效 内容是投标时的标号',
  `pContractNo` varchar(30) default NULL COMMENT '合同号 是/否 提现模式为2时，此字段生效 内容是投标时的合同号',
  `pDwTo` varchar(30) default NULL COMMENT '提现去向 是/否 提现模式为2时，此字段生效 上送IPS托管账户号（个人/商户号）',
  `pIdentNo` varchar(20) default NULL COMMENT '证件号码 否 真实身份证（个人）/由IPS颁发的商户号（商户）',
  `pRealName` varchar(30) default NULL COMMENT '姓名 否 真实姓名（中文） ',
  `pIpsAcctNo` varchar(30) default NULL COMMENT 'IPS账户号 否 账户类型为1时，IPS个人托管账户号 账户类型为0时，由IPS颁发的商户号',
  `pDwDate` date default NULL COMMENT '提现日期 否 格式：YYYYMMDD ',
  `pTrdAmt` decimal(20,2) NOT NULL default '0.00' COMMENT '提现金额 否 金额单位，不能为负，不允许为0 ',
  `pMerFee` decimal(20,2) NOT NULL default '0.00' COMMENT '平台手续费 否 金额单位，不能为负，允许为0 这里是平台向用户收取的费用 ',
  `pIpsFeeType` tinyint(1) NOT NULL default '0' COMMENT 'IPS手续费收取方 否 这里是IPS收取的费用 1：平台支付 2：提现方支付',
  `pIpsBillNo` varchar(30) default NULL,
  `is_callback` tinyint(1) NOT NULL default '0' COMMENT '0:未回调处理;1:已回调处理',
  `pErrCode` varchar(8) default NULL COMMENT 'MG00000F ?操作成功',
  `pErrMsg` varchar(100) default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of fanwe_ips_do_dw_trade
-- ----------------------------

-- ----------------------------
-- Table structure for `%DB_PREFIX%ips_guarantee_unfreeze`
-- ----------------------------
DROP TABLE IF EXISTS `%DB_PREFIX%ips_guarantee_unfreeze`;
CREATE TABLE `%DB_PREFIX%ips_guarantee_unfreeze` (
  `id` int(10) NOT NULL auto_increment,
  `deal_id` int(11) NOT NULL default '0',
  `pMerCode` varchar(6) NOT NULL COMMENT '“平台”账号 否 由IPS颁发的商户号 ',
  `pMerBillNo` varchar(30) NOT NULL default '0' COMMENT '商户系统唯一丌重复',
  `pBidNo` varchar(30) NOT NULL default '0' COMMENT '标的号，商户系统唯一丌重复',
  `pUnfreezeDate` date default NULL COMMENT '解冻日期格 式：yyyymmdd ',
  `pUnfreezeAmt` decimal(20,2) default '0.00' COMMENT '解冻金额 金额单位，丌能为负，丌允许为0 累计解冻金额  <= 当时冻结时的保证金',
  `pUnfreezenType` tinyint(1) default '1' COMMENT '解冻类型 否 1#解冻借款方；2#解冻担保方',
  `pAcctType` tinyint(1) default '1' COMMENT '解冻者账户类型 否 0#机构；1#个人',
  `pIdentNo` varchar(20) default NULL COMMENT '解冻者证件号码 是/否 解冻者账户类型1时：真实身份证（个人），必填 解冻账户类型0时：为空处理',
  `pRealName` varchar(30) default NULL COMMENT '解冻者姓名 否 账户类型为1时，真实姓名（中文） 账户类型为0时，开户时在IPS登记的商户名称 ',
  `pIpsAcctNo` varchar(30) default NULL COMMENT '解冻者IPS账号 否 账户类型为1时，IPS个人托管账户号 账户类型为0时，由IPS颁发的商户号 ',
  `pIpsBillNo` varchar(30) default NULL COMMENT '由IPS系统生成的唯一流水号',
  `pIpsTime` datetime default NULL COMMENT 'IPS处理时间 否 格式为：yyyyMMddHHmmss',
  `pErrCode` varchar(8) default NULL COMMENT '处理返回状态 否 MG00000F 操作成功； 其他错误：参考自定义错误码 ',
  `pErrMsg` varchar(255) default NULL COMMENT '返回信息 是/否 MG00000F 操作成功； 其他错误信息：参考自定义错误码 ',
  `is_callback` tinyint(1) NOT NULL default '0' COMMENT '0:未回调处理;1:已回调处理',
  PRIMARY KEY  (`id`,`pMerBillNo`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of fanwe_ips_guarantee_unfreeze
-- ----------------------------

-- ----------------------------
-- Table structure for `%DB_PREFIX%ips_log`
-- ----------------------------
DROP TABLE IF EXISTS `%DB_PREFIX%ips_log`;
CREATE TABLE `%DB_PREFIX%ips_log` (
  `id` int(10) NOT NULL auto_increment,
  `code` varchar(50) NOT NULL,
  `create_date` datetime NOT NULL,
  `strxml` text,
  `html` text,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=13 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of fanwe_ips_log
-- ----------------------------

-- ----------------------------
-- Table structure for `%DB_PREFIX%ips_register_creditor`
-- ----------------------------
DROP TABLE IF EXISTS `%DB_PREFIX%ips_register_creditor`;
CREATE TABLE `%DB_PREFIX%ips_register_creditor` (
  `id` int(10) NOT NULL auto_increment,
  `deal_id` int(11) NOT NULL default '0',
  `user_id` int(11) NOT NULL default '0',
  `pMerCode` int(6) NOT NULL,
  `pMerBillNo` varchar(30) NOT NULL COMMENT '商户订单号 否 商户系统唯一不重复 ',
  `pMerDate` date default NULL COMMENT '商户日期 否 格式：YYYYMMDD ',
  `pBidNo` varchar(30) default NULL COMMENT '标的号 否 字母和数字，如a~z,A~Z,0~9',
  `pContractNo` varchar(30) default NULL COMMENT '合同号 否 字母和数字，如a~z,A~Z,0~9',
  `pRegType` tinyint(1) default NULL COMMENT '登记方式 否 1：手劢投标  2：自劢投标',
  `pAuthNo` varchar(30) default NULL COMMENT '授权号 是/否  字母和数字，如a~z,A~Z,0~9 登记方式为1时，为空 登记方式为2时，填写该投资人自劢投标签约时IPS向平 台接口返回的“pIpsAuthNo 授权号” （详见自劢投标签 约） ',
  `pAuthAmt` decimal(20,2) default '0.00' COMMENT '债权面额 否 金额单位元，不能为负，不允许为0 ',
  `pTrdAmt` decimal(20,2) default '0.00' COMMENT '交易金额 否 金额单位元，不能为负，不允许为0 债权面额等于交易金额 ',
  `pFee` decimal(20,2) default '0.00' COMMENT '投资人手续费 否 金额单位元，不能为负，允许为0 ',
  `pAcctType` tinyint(1) default '1' COMMENT '账户类型 否 0#机构（暂未开放） ；1#个人 ',
  `pIdentNo` varchar(20) default NULL COMMENT '证件号码 否 真实身份证（个人）/由IPS颁发的商户号',
  `pRealName` varchar(30) default NULL COMMENT '姓名 否 真实姓名（中文）',
  `pAccount` varchar(30) default NULL COMMENT '投资人账户 否 账户类型为1时，IPS托管账户号（个人） 账户类型为0时，由IPS颁发的商户号',
  `pUse` varchar(100) default NULL COMMENT '借款用途 否 借款用途 ',
  `pMemo1` varchar(100) default NULL COMMENT '备注',
  `pMemo2` varchar(100) default NULL COMMENT '备注',
  `pMemo3` varchar(100) default NULL COMMENT '备注',
  `pAccountDealNo` varchar(20) default NULL COMMENT '投资人编号 否 IPS返回的投资人编号 ',
  `pBidDealNo` varchar(30) default NULL COMMENT '标的编号 否 IPS返回的标的编号',
  `pBusiType` tinyint(1) default NULL COMMENT '业务类型 否 返回1，代表投标',
  `pTransferAmt` decimal(20,2) default NULL COMMENT '实际冻结金额 否 实际冻结金额',
  `pStatus` tinyint(2) default '0' COMMENT '债权人状态 否 0：新增 1：?行中 10：结束',
  `pP2PBillNo` varchar(30) default NULL COMMENT 'IPS P2P订单号 否 由IPS系统生成的唯一流水号',
  `pIpsTime` datetime default NULL COMMENT 'IPS处理时间 否 格式为：yyyyMMddHHmmss',
  `is_callback` tinyint(1) NOT NULL default '0' COMMENT '0:未回调处理;1:已回调处理',
  `pErrCode` varchar(8) default NULL,
  `pErrMsg` varchar(100) default NULL,
  PRIMARY KEY  (`id`,`pMerBillNo`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of fanwe_ips_register_creditor
-- ----------------------------

-- ----------------------------
-- Table structure for `%DB_PREFIX%ips_register_cretansfer`
-- ----------------------------
DROP TABLE IF EXISTS `%DB_PREFIX%ips_register_cretansfer`;
CREATE TABLE `%DB_PREFIX%ips_register_cretansfer` (
  `id` int(10) NOT NULL auto_increment,
  `is_callback` tinyint(1) NOT NULL default '0' COMMENT '0:未回调处理;1:已回调处理',
  `t_user_id` int(11) NOT NULL default '0' COMMENT '受让用户ID',
  `transfer_id` int(11) NOT NULL default '0',
  `pMerCode` varchar(30) NOT NULL COMMENT '“平台”账 号 由IPS颁发的商户号',
  `pMerBillNo` varchar(30) NOT NULL default '' COMMENT '商户订单号 否 商户系统唯一不重复',
  `pMerDate` date default NULL COMMENT '商户日期 否 格式：YYYYMMDD ',
  `pBidNo` varchar(30) default NULL COMMENT '标的号 否 原投资交易的标的号，字母和数字，如a~z,A~Z,0~9 ',
  `pContractNo` varchar(30) default NULL COMMENT '合同号 否 原投资交易的合同号， 字母和数字，如a~z,A~Z,0~9 ',
  `pFromAccountType` tinyint(1) default NULL COMMENT '出让方账户类型 否 0：机构（暂不支持） 1：个人 ',
  `pFromName` varchar(30) default NULL COMMENT '出让方账户姓名 否 出让方账户真实姓名',
  `pFromAccount` varchar(50) default NULL COMMENT '出让方账户 否 出让方账户类型为1时，IPS托管账户号（个人） 出让方账户类型为0时，由IPS颁发的商户号 ',
  `pFromIdentType` tinyint(2) default '1' COMMENT '出让方证件类型 否 1#身份证，默认：1 ',
  `pFromIdentNo` varchar(20) default NULL COMMENT '出让方证件号码 否 真实身份证（个人）/由IPS颁发的商户号（机构）',
  `pToAccountType` tinyint(1) default NULL COMMENT '受让方账户类型 否 1：个人  0：机构（暂不支持）',
  `pToAccountName` varchar(30) default NULL COMMENT '受让方账户姓名 否 受让方账户真实姓名 ',
  `pToAccount` varchar(30) default NULL COMMENT '受让方账户 否 受让方账户类型为1时，IPS托管账户号（个人）',
  `pToIdentType` tinyint(2) default '1' COMMENT '受让方证件类型 否 1#身份证，默讣：1 ',
  `pToIdentNo` varchar(20) default NULL COMMENT '受让方证件号码 否 真实身份证（个人）/由IPS颁发的商户号（机构）',
  `pCreMerBillNo` varchar(30) default NULL COMMENT '登记债权人时提 交的订单号 否 字母和数字，如a~z,A~Z,0~9 登记债权人时提交的订单号，见<登记债权人接口>请求 参数中的“pMerBillNo” ',
  `pCretAmt` decimal(20,2) default '0.00' COMMENT '债权面额 否 金额单位元，不能为负，不允许为0 ',
  `pPayAmt` decimal(20,2) default '0.00' COMMENT '支付金额 否 金额单位元，不能为负，不允许为0 债权面额（1-30%）<=支付金额<= 债权面额（1+30%） ',
  `pFromFee` decimal(20,2) default '0.00' COMMENT '出让方手续费 否 金额单位元，不能为负，允许为0 ',
  `pToFee` decimal(20,2) default '0.00' COMMENT '受让方手续费 否 金额单位元，不能为负，允许为0 ',
  `pCretType` tinyint(1) default '1' COMMENT '转让类型 否 1：全部转让 2：部分转让',
  `pMemo1` varchar(100) default NULL COMMENT '备注',
  `pMemo2` varchar(100) default NULL COMMENT '备注',
  `pMemo3` varchar(100) default NULL COMMENT '备注',
  `pErrCode` varchar(8) default NULL COMMENT '处理返回状态 否 MG00000F 操作成功； 其他错误信息：参考自定义错误码',
  `pErrMsg` varchar(100) default NULL COMMENT '返回信息 是/否 MG00000F 操作成功； 其他错误信息：参考自定义错误码',
  `pP2PBillNo` varchar(30) default NULL COMMENT '债权转让编号 否 IPS返回的债权转让编号',
  `pIpsTime` datetime default NULL COMMENT 'IPS处理时间 否 格式为：yyyyMMddHHmmss ',
  `pBussType` tinyint(1) default '1' COMMENT '业务类型 否 1：债权转让',
  `pStatus` tinyint(1) default '1' COMMENT '转让状态 否 0：新建 1：?行中 10：成功  9： 失败 ',
  PRIMARY KEY  (`id`,`pMerBillNo`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of fanwe_ips_register_cretansfer
-- ----------------------------

-- ----------------------------
-- Table structure for `%DB_PREFIX%ips_register_guarantor`
-- ----------------------------
DROP TABLE IF EXISTS `%DB_PREFIX%ips_register_guarantor`;
CREATE TABLE `%DB_PREFIX%ips_register_guarantor` (
  `id` int(10) NOT NULL auto_increment,
  `deal_id` int(11) NOT NULL,
  `agency_id` int(11) NOT NULL,
  `pMerCode` varchar(6) default NULL COMMENT '“平台”账号 否 由IPS颁发的商户号',
  `pMerBillNo` varchar(30) NOT NULL default '' COMMENT '商户订单号 否 商户系统唯一不重复',
  `pMerDate` date default NULL COMMENT '商户日期 否 格式：yyyyMMdd ',
  `pBidNo` varchar(30) default NULL COMMENT '标的号 否 字母和数字，如a~z,A~Z,0~9',
  `pAmount` decimal(20,2) default '0.00' COMMENT '担保金额 否 金额单位元，不能为负，不允许为0 担保人针对该合同标的承诺的最高赔付金额 ',
  `pMarginAmt` decimal(20,2) default '0.00' COMMENT '担保保证金 否 金额单位元，不能为负，允许为0 担保人针对该合同标的被冻结的金额',
  `pProFitAmt` decimal(20,2) default '0.00' COMMENT '担保收益 否 金额单位元，不能为负，允许为0 ',
  `pAcctType` tinyint(1) default '0' COMMENT '担保方类型 否 0#机构；1#个人 ',
  `pFromIdentNo` varchar(20) default NULL COMMENT '担保方证件号码 否 针对担保方类型为1时：真实身份证（个人） 针对担保方类型为0时：由IPS颁发的商户号 ',
  `pAccountName` varchar(30) default NULL COMMENT '担保方账户姓名 否 针对担保方类型为1时：担保方账户真实姓名 针对担保方类型为0时：在IPS开户时登记的商户名称',
  `pAccount` varchar(30) default NULL COMMENT '担保方账户 否 担保方类型为1时，IPS托管账户号（个人） 担保方类型为0时，由IPS颁发的商户号 ',
  `pMemo1` varchar(100) default NULL,
  `pMemo2` varchar(100) default NULL,
  `pMemo3` varchar(100) default NULL,
  `pP2PBillNo` varchar(30) default NULL COMMENT '担保方编号 否 IPS返回的担保人编号 ',
  `pRealFreezeAmt` decimal(20,2) default '0.00' COMMENT '实际冻结金额  IPS返回的担保保证金 ',
  `pCompenAmt` decimal(20,2) default '0.00' COMMENT '已代偿金额  IPS返回的担保金额 ',
  `pIpsTime` datetime default NULL COMMENT 'IPS处理时间  格式为：yyyyMMddHHmmss ',
  `pStatus` tinyint(2) default NULL COMMENT '担保状态 否 0：新增  1：?行中  10：结束  9：失败',
  `pErrCode` varchar(8) default NULL COMMENT '处理返回状态 否 MG00000F 操作成功； 其他错误信息：参考自定义错误码 ',
  `pErrMsg` varchar(100) default NULL COMMENT '返回信息 是/否 MG00000F 操作成功； 其他错误信',
  `is_callback` tinyint(1) NOT NULL default '0' COMMENT '0:未回调处理;1:已回调处理',
  PRIMARY KEY  (`id`,`pMerBillNo`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of fanwe_ips_register_guarantor
-- ----------------------------

-- ----------------------------
-- Table structure for `%DB_PREFIX%ips_register_subject`
-- ----------------------------
DROP TABLE IF EXISTS `%DB_PREFIX%ips_register_subject`;
CREATE TABLE `%DB_PREFIX%ips_register_subject` (
  `id` int(10) NOT NULL auto_increment,
  `pMerCode` int(6) NOT NULL default '0' COMMENT '“平台”账号 否 由IPS颁发的商户号 ',
  `deal_id` int(11) NOT NULL default '0',
  `status` tinyint(1) NOT NULL default '0' COMMENT '0:新增; 1:标的正常结束; 2:流标结束',
  `pMerBillNo` varchar(30) NOT NULL COMMENT '商户订单号 否 商户系统唯一不重复',
  `pBidNo` varchar(30) NOT NULL COMMENT '标的号，商户系统唯一不重复 ',
  `pRegDate` date default NULL COMMENT '商户日期 否 格式：YYYYMMDD ',
  `pLendAmt` decimal(20,2) default '0.00' COMMENT '借款金额 否 金额单位，丌能为负，丌允许为0； 借款金额  <= 10000.00万 关于N(9,2)见4.1补充说明 ',
  `pGuaranteesAmt` decimal(20,2) default '0.00' COMMENT '借款保证金，允许冻结的金额，金额单位，丌能为负，允 许为0； 借款保证金  <= 10000.00万 ',
  `pTrdLendRate` decimal(20,2) default '0.00' COMMENT '借款利率 否 金额单位，丌能为负，允许为0； 借款利率  < 48%，例如：45.12%传入 45.12 ',
  `pTrdCycleType` tinyint(2) default NULL COMMENT '借款周期类型 否 借款周期类型，1：天；3：月； 借款周期 <= 5年',
  `pTrdCycleValue` int(5) default '0' COMMENT '借款周期值 否 借款周期值 借款周期 <= 5年。 如果借款周期类型为天，则借款周期值<= 1800(360 * 5)；如果借款周期类型为月，则借款周期值<= 60(12 * 5) ',
  `pLendPurpose` varchar(100) default NULL COMMENT '借款用途',
  `pRepayMode` tinyint(2) default NULL COMMENT '还款方式，1：等额本息，2：按月还息到期还本；3：等 额本金；99：其他； ',
  `pOperationType` tinyint(2) default NULL COMMENT '标的操作类型，1：新增，2：结束 “新增”代表新增标的，“结束”代表标的正常还清、丌 需要再还款戒者标的流标等情况。标的“结束”后，投资 人投标冻结金额、担保方保证金、借款人保证金均自劢解 冻。 ',
  `pLendFee` decimal(20,2) default '0.00' COMMENT '借款人手续费 否 金额单位，丌能为负，允许为0 这里是平台向借款人收取的费用 ',
  `pAcctType` tinyint(1) default '1' COMMENT '账户类型 否 0#机构（暂未开放） ；1#个人 ',
  `pIdentNo` varchar(20) default NULL COMMENT '证件号码 否 真实身份证（个人）/由IPS颁发的商户号 ',
  `pRealName` varchar(30) default NULL COMMENT '姓名 否 真实姓名（中文）',
  `pIpsAcctNo` varchar(30) default NULL COMMENT 'IPS账户号 否 账户类型为1时，IPS托管账户号（个人） 账户类型为0时，由IPS颁发的商户号 ',
  `pMemo1` varchar(100) default NULL COMMENT '备注 是/否  ',
  `pMemo2` varchar(100) default NULL,
  `pMemo3` varchar(100) default NULL,
  `pIpsBillNo` varchar(30) default NULL,
  `pIpsTime` datetime default NULL COMMENT 'IPS处理时间 否 格式为：yyyyMMddHHmmss ',
  `pBidStatus` tinyint(2) default NULL COMMENT '标的状态，1：新增；2：募集中；3：? 行中；8：结束处理中；9：失败；10：结 束；',
  `pRealFreezenAmt` decimal(20,2) default '0.00' COMMENT '实际冻结金额，金额单位，不能为负，不允许为0； 实际冻结金额 = 保证金+手续费',
  `pErrCode` varchar(255) default NULL COMMENT 'MG02500F标的新增；（登记标的时同步返回） ? MG02501F标的募集中；（登记标的成功后异步返回） ? MG02503F 标的结束处理中；（登记结束标的时同步返 回） ? MG02504F标的失败； ? MG02505F标的结束(登记结束标的成功后异步返回)',
  `pErrMsg` varchar(100) default NULL COMMENT '返回信息 是/否 MG00000F 操作成功； MG02500F标的新增； MG02501F标的募集中； MG02503F标的结束处理中； MG02504F标的失败； MG02505F标的结束 其他错误信息：参考自定义错误码',
  `is_callback` tinyint(1) NOT NULL default '0' COMMENT '0:未回调处理;1:已回调处理',
  `status_msg` varchar(255) default NULL COMMENT '主要是status_msg=2时记录的，流标原因',
  PRIMARY KEY  (`id`,`pMerBillNo`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of fanwe_ips_register_subject
-- ----------------------------

-- ----------------------------
-- Table structure for `%DB_PREFIX%ips_repayment_new_trade`
-- ----------------------------
DROP TABLE IF EXISTS `%DB_PREFIX%ips_repayment_new_trade`;
CREATE TABLE `%DB_PREFIX%ips_repayment_new_trade` (
  `id` int(10) NOT NULL auto_increment,
  `deal_id` int(11) NOT NULL,
  `deal_repay_id` int(11) NOT NULL default '0' COMMENT '还款列表ID',
  `pMerCode` varchar(30) NOT NULL,
  `pMerBillNo` varchar(30) NOT NULL,
  `pBidNo` varchar(30) NOT NULL COMMENT '标号  ? 字母和数字，如a~z,A~Z,0~9',
  `pRepaymentDate` date NOT NULL COMMENT '还款日期 ? 格式：YYYYMMDD ',
  `pRepayType` tinyint(1) NOT NULL default '1' COMMENT '还款类型，1#手动还款，2#自动还款',
  `pIpsAuthNo` varchar(30) NOT NULL COMMENT '授权号 ? 是/否 ? 当还款类型为自动还款时不为空，为手动还款时为空',
  `pOutAcctNo` varchar(30) NOT NULL COMMENT '转出方IPS账号 ? 否 ? 借款人在IPS注册的资金托管账号',
  `pOutAmt` decimal(20,2) NOT NULL default '0.00' COMMENT '转出金额 ? 否 ? 表示此次还款总金额。 ? 转出金额=Sum(pInAmt) ? Sum(pInAmt)代表转入金额的合计，一个或多个 投资人时的还款金额的累加。 ? 金额单位：元，不能为负，不允许为 0，保留 2 位小 数； ? 格式：12.00 ',
  `pOutFee` decimal(20,2) NOT NULL default '0.00' COMMENT '转出方总手续费 ? 否 ? 表示此次借款人或担保人所承担的还款手续费，此手 续费由商户平台向用户收取。 ? 金额单位：元，不能为负，允许为0，保留 2位小数； ? 格式：12.00 ?pOutFee ?= ?Sum(pOutInfoFee) ? Sum(pOutInfoFee)代表转出方手续费的合计 ? ',
  `pMessage` varchar(100) default NULL COMMENT '转入结果说明 成功与失败的说明',
  `pMemo1` varchar(100) default NULL,
  `pMemo2` varchar(100) default NULL,
  `pMemo3` varchar(100) default NULL,
  `pIpsBillNo` varchar(30) default NULL COMMENT 'IPS还款订单号  否  由 IPS 系统生成的唯一流水号， 此次还款的批次号',
  `pOutIpsFee` decimal(20,2) default '0.00' COMMENT '收取转出方手 续费  此手续费由平台商户垫付给 IPS 的手续费',
  `pIpsDate` date default NULL COMMENT 'IPS受理日期  否  yyyyMMdd',
  `pErrCode` varchar(8) default NULL COMMENT '返回状态  否 MG00000F操作成功 MG00008F IPS受理中；  待处理状态。（并非此次还款成功，还款成功返回详见 4.11.4）  除此之外：参考自定义错误码',
  `pErrMsg` varchar(100) default NULL COMMENT '接口返回信息  否  状态非 MG00000F时，反馈实际原因',
  `is_callback` tinyint(1) default '0',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of fanwe_ips_repayment_new_trade
-- ----------------------------

-- ----------------------------
-- Table structure for `%DB_PREFIX%ips_repayment_new_trade_detail`
-- ----------------------------
DROP TABLE IF EXISTS `%DB_PREFIX%ips_repayment_new_trade_detail`;
CREATE TABLE `%DB_PREFIX%ips_repayment_new_trade_detail` (
  `id` int(10) NOT NULL auto_increment,
  `pid` int(11) NOT NULL,
  `pCreMerBillNo` varchar(30) NOT NULL COMMENT '登记债权人时提 交的订单号 ? 否 ? 登记债权人时提交的订单号，见<登记债权人接口>请求 参数中的“pMerBillNo” ',
  `pInAcctNo` varchar(30) default NULL COMMENT '转入方 IPS 托管 账户号 ? 否 ? 债权人在IPS注册的资金托管账号',
  `pInFee` decimal(20,2) default '0.00' COMMENT '转入方手续费 ? 否 ? 表示此次还款债权人所承担的还款手续费，此手续费由商 户平台向用户收取。金额单位：元，不能为负，允许为0，保留2位小数； ? 格式：12.00 ?',
  `pOutInfoFee` decimal(20,2) default '0.00' COMMENT '转出方手续费 ? 否 ? 表示此次借款人或担保人所承担的还款明细手续费，此手 续费由商户平台收取。',
  `pInAmt` decimal(20,2) default '0.00' COMMENT '转入金额 ? 否 ? 格式：0.00 ? ?必须大于0 ?且大于转入方手续费',
  `pStatus` varchar(2) NOT NULL default '0' COMMENT '转入状态 ? 否 ? Y#还款成功；N#还款失败',
  `pMessage` varchar(100) default NULL COMMENT '转入结果说明 成功与失败的说明',
  `deal_load_repay_id` int(10) NOT NULL default '0' COMMENT '对应的还款列表ID',
  `impose_money` decimal(20,2) default '0.00',
  `repay_manage_impose_money` decimal(20,2) default '0.00',
  `self_money` decimal(20,2) NOT NULL,
  `repay_status` tinyint(1) NOT NULL,
  `true_repay_time` int(11) NOT NULL,
  `manage_interest_money` decimal(11,2) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of fanwe_ips_repayment_new_trade_detail
-- ----------------------------

-- ----------------------------
-- Table structure for `%DB_PREFIX%ips_transfer`
-- ----------------------------
DROP TABLE IF EXISTS `%DB_PREFIX%ips_transfer`;
CREATE TABLE `%DB_PREFIX%ips_transfer` (
  `id` int(10) NOT NULL auto_increment,
  `deal_id` int(10) NOT NULL,
  `ref_data` varchar(200) default NULL,
  `pMerCode` varchar(8) NOT NULL,
  `pMerBillNo` varchar(30) default NULL COMMENT '商户订单号  否  商户系统唯一不重复',
  `pBidNo` varchar(30) default NULL COMMENT '标的号  否  标的号，商户系统唯一不重复 ',
  `pDate` date default NULL COMMENT '商户日期  否  格式：YYYYMMDD  ',
  `pTransferType` tinyint(2) default NULL COMMENT '转账类型  否  转账类型  1：投资（报文提交关系，转出方：转入方=N：1），  2：代偿（报文提交关系，转出方：转入方=1：N），  3：代偿还款（报文提交关系，转出方：转入方=1：1），  4：债权转让（报文提交关系，转出方：转入方=1：1），  5：结算担保收益（报文提交关系，转出方：转入方=1： 1） ',
  `pTransferMode` tinyint(2) default NULL COMMENT '转账方式  是  转账方式，1：逐笔入账；2：批量入账  逐笔入账：不将转账款项汇总，而是按明细交易一笔一 笔计入账户  批量入帐：针对投资，将明细交易按 1 笔汇总本金和 1 笔汇总手续费记入借款人帐户  当转账类型为“1：投资”时，可选择 1 或 2。其余交 易只能选1',
  `pErrCode` varchar(8) default NULL COMMENT '返回状态 ? 否 ? 一、转账类型为“代偿”，“投 资”时同步返回 MG00008F ?IPS 受理中；异步再返回 MG00000F ? 操作成功； ? 二、其他转账类型 MG00000F ?操作成功； ? 其他错误信息：参考自定义错误码 ? ',
  `pErrMsg` varchar(100) default NULL COMMENT '接口返回信息 ? 否 ? MG00000F ?操作成功； ? 其他错误信息：参考自定义错误码',
  `pIpsBillNo` varchar(30) default NULL COMMENT 'IPS订单号  否  由 IPS系统生成的唯一流水号',
  `pIpsTime` datetime default NULL COMMENT 'IPS处理时间  否  格式为：yyyyMMddHHmmss',
  `is_callback` tinyint(1) NOT NULL default '0',
  `pMemo1` varchar(100) default NULL,
  `pMemo2` varchar(100) default NULL,
  `pMemo3` varchar(100) default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of fanwe_ips_transfer
-- ----------------------------

-- ----------------------------
-- Table structure for `%DB_PREFIX%ips_transfer_detail`
-- ----------------------------
DROP TABLE IF EXISTS `%DB_PREFIX%ips_transfer_detail`;
CREATE TABLE `%DB_PREFIX%ips_transfer_detail` (
  `id` int(10) NOT NULL auto_increment,
  `pid` int(11) NOT NULL default '0',
  `pOriMerBillNo` varchar(30) default NULL COMMENT '原商户订单号  否  商户系统唯一不重复  当转账类型为投资时，为登记债权人时提交的商户订单号  当转账类型为代偿时，为登记债权人时提交的商户订单号  当转账类型为代偿还款时，为代偿时提交的商户订单号  当转账类型为债权转让时，为登记债权转让时提交的商户 订单号  当转账类型为结算担保收益时，为登记担保人时提交的商 户订单号  ',
  `pTrdAmt` decimal(20,2) default '0.00' COMMENT '转账金额  否  金额单位：元，不能为负，不允许为0，保留2位小数；  格式：12.00  转账类型，1：投资，转账金额=债权面额；  转账类型，2：代偿，转账金额=代偿金额；  转账类型，3：代偿还款，转账金额=代偿还款金额；  转账类型，4：债权转让，转账金额=登记债权转让时的 支付金额； 转账类型，5：结算担保收益，累计转账金额<=登记担保 方时的担保收益；  ',
  `pFAcctType` tinyint(1) default '1' COMMENT '转出方账户类型  否  0#机构；1#个人',
  `pFIpsAcctNo` varchar(30) default NULL COMMENT '转出方 IPS 托管 账户号  否  账户类型为1时，IPS个人托管账户号  账户类型为0时，由 IPS颁发的商户号  转账类型，1：投资，此为转出方（投资人）；  转账类型，2：代偿，此为转出方（担保方）；  转账类型，3：代偿还款，此为转出方（借款人）；  转账类型，4：债权转让，此为转出方（受让方）；  转账类型，5：结算担保收益，此为转出方（借款人）；  ',
  `pFTrdFee` decimal(20,2) default NULL COMMENT '转出方明细手续 费  否  金额单位：元，不能为负，允许为0，保留2位小数；  格式：12.00  转账类型，1：投资，此为转出方（投资人）手续费；  转账类型，2：代偿，此为转出方（担保方）手续费；  转账类型，3：代偿还款，此为转出方（借款人）手续费；  转账类型，4：债权转让，此为转出方（受让方）手续费；  转账类型，5：结算担保收益，此为转出方（借款人）手 续费；  ',
  `pTAcctType` tinyint(1) default '1' COMMENT '转入方账户类型  否  0#机构；1#个人',
  `pTIpsAcctNo` varchar(30) default NULL COMMENT '转入方 IPS 托管 账户号  否  账户类型为1时，IPS个人托管账户号  账户类型为0时，由 IPS颁发的商户号  转账类型，1：投资，此为转入方（借款人）；  转账类型，2：代偿，此为转入方（投资人）；  转账类型，3：代偿还款，此为转入方（担保方）；  转账类型，4：债权转让，此为转入方（出让方）；  转账类型，5：结算担保收益，此为转入方（担保方）；  ',
  `pTTrdFee` decimal(20,2) default NULL COMMENT '转入方明细手续 费  否  金额单位：元，不能为负，允许为0，保留2位小数；  格式：12.00  转账类型，1：投资，此为转入方（借款人）手续费；  转账类型，2：代偿，此为转入方（投资人）手续费；  转账类型，3：代偿还款，此为转入方（担保方）手续费；  转账类型，4：债权转让，此为转入方（出让方）手续费；  转账类型，5：结算担保收益，此为转入方（担保方）手 续费； ',
  `pIpsDetailBillNo` varchar(255) default NULL COMMENT 'IPS明细订单号  否  IPS明细订单号',
  `pIpsDetailTime` datetime default NULL COMMENT 'IPS明细处理时间  否  格式为：yyyyMMddHHmmss ',
  `pIpsFee` decimal(20,2) default '0.00' COMMENT 'IPS手续费  否  IPS手续费',
  `pStatus` varchar(1) default NULL,
  `pMessage` varchar(100) default NULL COMMENT '转账备注  否  转账失败的原因 ',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of fanwe_ips_transfer_detail
-- ----------------------------

-- ----------------------------
-- Table structure for `%DB_PREFIX%link`
-- ----------------------------
DROP TABLE IF EXISTS `%DB_PREFIX%link`;
CREATE TABLE `%DB_PREFIX%link` (
  `id` int(11) NOT NULL auto_increment,
  `name` varchar(255) NOT NULL COMMENT '友情链接显示名称',
  `group_id` int(11) NOT NULL COMMENT '友情链接分组ID',
  `url` varchar(255) NOT NULL COMMENT '链接地址',
  `is_effect` tinyint(1) NOT NULL COMMENT '有效性标识',
  `sort` int(11) NOT NULL COMMENT '排序 大到小',
  `img` varchar(255) NOT NULL COMMENT '链接图片',
  `description` text NOT NULL COMMENT '描述说明',
  `count` int(11) NOT NULL COMMENT '点击量',
  `show_index` tinyint(1) NOT NULL COMMENT '是否显示到首页底部 0:否 1:是',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of fanwe_link
-- ----------------------------

-- ----------------------------
-- Table structure for `%DB_PREFIX%link_group`
-- ----------------------------
DROP TABLE IF EXISTS `%DB_PREFIX%link_group`;
CREATE TABLE `%DB_PREFIX%link_group` (
  `id` int(11) NOT NULL auto_increment,
  `name` varchar(255) NOT NULL COMMENT '友情链接分组名称',
  `sort` tinyint(1) NOT NULL,
  `is_effect` tinyint(1) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=7 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of fanwe_link_group
-- ----------------------------
INSERT INTO `%DB_PREFIX%link_group` VALUES ('6', '友情链接', '1', '1');

-- ----------------------------
-- Table structure for `%DB_PREFIX%log`
-- ----------------------------
DROP TABLE IF EXISTS `%DB_PREFIX%log`;
CREATE TABLE `%DB_PREFIX%log` (
  `id` int(11) NOT NULL auto_increment,
  `log_info` text NOT NULL COMMENT '日志描述内容',
  `log_time` int(11) NOT NULL COMMENT '发生时间',
  `log_admin` int(11) NOT NULL COMMENT ' 操作的管理员ID',
  `log_ip` varchar(255) NOT NULL COMMENT '操作者IP',
  `log_status` tinyint(1) NOT NULL COMMENT '操作结果 1:操作成功 0:操作失败',
  `module` varchar(255) NOT NULL COMMENT '操作的模块module',
  `action` varchar(255) NOT NULL COMMENT '操作的命令action',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of fanwe_log
-- ----------------------------

-- ----------------------------
-- Table structure for `%DB_PREFIX%mail_list`
-- ----------------------------
DROP TABLE IF EXISTS `%DB_PREFIX%mail_list`;
CREATE TABLE `%DB_PREFIX%mail_list` (
  `id` int(11) NOT NULL auto_increment,
  `mail_address` varchar(255) NOT NULL COMMENT '邮件的地址',
  `city_id` int(11) NOT NULL COMMENT '订阅的城市ID，用于按地区群发时匹配',
  `code` varchar(255) NOT NULL COMMENT '弃用',
  `is_effect` tinyint(1) NOT NULL,
  PRIMARY KEY  (`id`),
  KEY `mail_address_idx` (`mail_address`)
) ENGINE=MyISAM AUTO_INCREMENT=10 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of fanwe_mail_list
-- ----------------------------

-- ----------------------------
-- Table structure for `%DB_PREFIX%mail_server`
-- ----------------------------
DROP TABLE IF EXISTS `%DB_PREFIX%mail_server`;
CREATE TABLE `%DB_PREFIX%mail_server` (
  `id` int(11) NOT NULL auto_increment,
  `smtp_server` varchar(255) NOT NULL COMMENT 'smtp服务器地址IP或域名',
  `smtp_name` varchar(255) NOT NULL COMMENT 'smtp发件帐号名',
  `smtp_pwd` varchar(255) NOT NULL COMMENT 'smtp密码',
  `is_ssl` tinyint(1) NOT NULL COMMENT '是否ssl加密连接（参考具体smtp服务商的要求，如gmail要求ssl连接）',
  `smtp_port` varchar(255) NOT NULL COMMENT 'smtp端口',
  `use_limit` int(11) NOT NULL COMMENT '可用次数为0时表示无限次数使用, 次数满后轮到下一个配置的邮件服务器发件，直到没有可发的邮件服务器为止',
  `is_reset` tinyint(1) NOT NULL COMMENT '是否自动清零，1:次数达到上限后自动清零，等待下一个轮回继续使用该邮箱发送',
  `is_effect` tinyint(1) NOT NULL,
  `total_use` int(11) NOT NULL COMMENT '当前已用次数',
  `is_verify` tinyint(1) NOT NULL COMMENT '是否需要身份验证,通常为1',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of fanwe_mail_server
-- ----------------------------

-- ----------------------------
-- Table structure for `%DB_PREFIX%medal`
-- ----------------------------
DROP TABLE IF EXISTS `%DB_PREFIX%medal`;
CREATE TABLE `%DB_PREFIX%medal` (
  `id` int(11) NOT NULL auto_increment,
  `class_name` varchar(255) NOT NULL COMMENT '勋章接口名',
  `name` varchar(255) NOT NULL COMMENT '显示名称',
  `description` text NOT NULL COMMENT '勋章的描述',
  `is_effect` tinyint(1) NOT NULL COMMENT '有效性',
  `config` text NOT NULL COMMENT '不同勋章接口功能的配置信息',
  `icon` varchar(255) NOT NULL COMMENT '勋章图片',
  `image` varchar(255) NOT NULL COMMENT '备用',
  `route` text NOT NULL COMMENT '勋章获取规则的描述文字',
  `allow_check` tinyint(1) NOT NULL COMMENT '是否会被系统回收 0:不会 1:会',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=6 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of fanwe_medal
-- ----------------------------

-- ----------------------------
-- Table structure for `%DB_PREFIX%message`
-- ----------------------------
DROP TABLE IF EXISTS `%DB_PREFIX%message`;
CREATE TABLE `%DB_PREFIX%message` (
  `id` int(11) NOT NULL auto_increment,
  `title` varchar(255) NOT NULL COMMENT '留言标题',
  `content` text NOT NULL COMMENT '留言内容',
  `create_time` int(11) NOT NULL COMMENT '留言时间',
  `update_time` int(11) NOT NULL COMMENT '回复时间',
  `admin_reply` text NOT NULL COMMENT '管理员回复内容',
  `admin_id` int(11) NOT NULL COMMENT '回复管理员ID',
  `rel_table` varchar(255) NOT NULL COMMENT '相关的数据表/模块（如借款留言deal）',
  `rel_id` int(11) NOT NULL COMMENT '相关留言的数据ID',
  `user_id` int(11) NOT NULL COMMENT '留言会员ID',
  `pid` int(11) NOT NULL COMMENT '弃用',
  `is_effect` tinyint(1) NOT NULL COMMENT '有效性标识（自动生效的留言自动为1），审核生效的留言为0',
  PRIMARY KEY  (`id`),
  KEY `idx_0` (`user_id`,`is_effect`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of fanwe_message
-- ----------------------------

-- ----------------------------
-- Table structure for `%DB_PREFIX%message_type`
-- ----------------------------
DROP TABLE IF EXISTS `%DB_PREFIX%message_type`;
CREATE TABLE `%DB_PREFIX%message_type` (
  `id` int(11) NOT NULL auto_increment,
  `type_name` varchar(255) NOT NULL COMMENT '预设的代码用于留言表中的rel_table',
  `is_fix` tinyint(1) NOT NULL COMMENT '系统内置类型，1:不可删除该类型 0:可删除',
  `show_name` varchar(255) NOT NULL COMMENT '类型显示名称 主要在留言板页面显示',
  `is_effect` tinyint(1) NOT NULL,
  `sort` int(11) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of fanwe_message_type
-- ----------------------------
INSERT INTO `%DB_PREFIX%message_type` VALUES ('1', 'deal', '1', '普通贷款', '1', '0');
INSERT INTO `%DB_PREFIX%message_type` VALUES ('2', 'transfer', '1', '债券转让', '1', '0');

-- ----------------------------
-- Table structure for `%DB_PREFIX%mobile_list`
-- ----------------------------
DROP TABLE IF EXISTS `%DB_PREFIX%mobile_list`;
CREATE TABLE `%DB_PREFIX%mobile_list` (
  `id` int(11) NOT NULL auto_increment,
  `mobile` varchar(255) NOT NULL COMMENT '订阅手机号',
  `city_id` int(11) NOT NULL COMMENT '城市ID',
  `verify_code` varchar(255) NOT NULL COMMENT '验证码',
  `is_effect` tinyint(1) NOT NULL COMMENT '有效性标识',
  PRIMARY KEY  (`id`),
  KEY `mobile_idx` (`mobile`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of fanwe_mobile_list
-- ----------------------------

-- ----------------------------
-- Table structure for `%DB_PREFIX%mobile_verify_code`
-- ----------------------------
DROP TABLE IF EXISTS `%DB_PREFIX%mobile_verify_code`;
CREATE TABLE `%DB_PREFIX%mobile_verify_code` (
  `id` int(11) NOT NULL auto_increment,
  `verify_code` varchar(10) NOT NULL,
  `mobile` varchar(20) NOT NULL,
  `create_time` int(11) NOT NULL,
  `client_ip` varchar(30) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of fanwe_mobile_verify_code
-- ----------------------------

-- ----------------------------
-- Table structure for `%DB_PREFIX%msg_box`
-- ----------------------------
DROP TABLE IF EXISTS `%DB_PREFIX%msg_box`;
CREATE TABLE `%DB_PREFIX%msg_box` (
  `id` int(11) NOT NULL auto_increment,
  `title` varchar(255) NOT NULL COMMENT '标题',
  `content` text NOT NULL COMMENT '内容',
  `from_user_id` int(11) NOT NULL COMMENT '发件人ID 0表示系统自动发送的信息',
  `to_user_id` int(11) NOT NULL COMMENT '收信人ID',
  `create_time` int(11) NOT NULL COMMENT '发信时间',
  `is_read` tinyint(1) NOT NULL COMMENT '是否已读 0:未读 1:已读',
  `is_delete` tinyint(1) NOT NULL COMMENT '是否被用户删除',
  `system_msg_id` int(11) NOT NULL COMMENT '系统群发的系统通知关联的群发数据ID',
  `type` tinyint(1) NOT NULL,
  `group_key` varchar(200) NOT NULL,
  `is_notice` tinyint(1) NOT NULL COMMENT '1系统通知 2材料通过 3审核失败 4额度更新 5提现申请 6提现成功 7提现失败 8还款成功 9回款成功 10借款流标 11投标流标 12三日内还款 13标被留言 14标留言被回复 15借款投标过半 16投标满标 17债权转让失败，18债权转让成功 19续约成功 20续约失败 0用户信息',
  `fav_id` int(11) NOT NULL default '0' COMMENT '关联数据id',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of fanwe_msg_box
-- ----------------------------

-- ----------------------------
-- Table structure for `%DB_PREFIX%msg_conf`
-- ----------------------------
DROP TABLE IF EXISTS `%DB_PREFIX%msg_conf`;
CREATE TABLE `%DB_PREFIX%msg_conf` (
  `user_id` int(11) NOT NULL,
  `mail_asked` tinyint(1) NOT NULL COMMENT '有人对我的借款列表提问（邮件）',
  `sms_asked` tinyint(1) NOT NULL COMMENT '有人对我的借款列表提问（邮件）',
  `mail_bid` tinyint(1) NOT NULL COMMENT '有人向我的借款列表投标（邮件）',
  `sms_bid` tinyint(1) NOT NULL COMMENT '有人向我的借款列表投标（短信）',
  `mail_myfail` tinyint(1) NOT NULL COMMENT '我的借款列表流标（邮件）',
  `sms_myfail` tinyint(1) NOT NULL COMMENT '我的借款列表流标（短信）',
  `mail_half` tinyint(1) NOT NULL COMMENT '我的借款列表完成度超过50%',
  `sms_half` tinyint(1) NOT NULL COMMENT '我的借款列表完成度超过50%',
  `mail_bidsuccess` tinyint(1) NOT NULL COMMENT '我的投标成功',
  `sms_bidsuccess` tinyint(1) NOT NULL COMMENT '我的投标成功',
  `mail_fail` tinyint(1) NOT NULL COMMENT '我的投标流标',
  `sms_fail` tinyint(1) NOT NULL COMMENT '我的投标流标',
  `mail_bidrepaid` tinyint(1) NOT NULL COMMENT '我收到一笔还款',
  `sms_bidrepaid` tinyint(1) NOT NULL COMMENT '我收到一笔还款',
  `mail_answer` tinyint(1) NOT NULL COMMENT '借入者回答了我对借款列表的提问',
  `sms_answer` tinyint(1) NOT NULL COMMENT '借入者回答了我对借款列表的提问',
  `mail_transferfail` tinyint(1) NOT NULL COMMENT '债权转让失败提醒',
  `sms_transferfail` tinyint(1) NOT NULL COMMENT '债权转让失败提醒',
  `mail_transfer` tinyint(1) NOT NULL COMMENT '债权转让成功提醒',
  `sms_transfer` tinyint(1) NOT NULL COMMENT '债权转让成功提醒',
  `mail_redenvelope` tinyint(1) NOT NULL COMMENT '红包奖励提醒',
  `sms_redenvelope` tinyint(1) NOT NULL COMMENT '红包奖励提醒',
  `mail_rate` tinyint(1) NOT NULL COMMENT '收益率奖励提醒',
  `sms_rate` tinyint(1) NOT NULL COMMENT '收益率奖励提醒',
  `mail_integral` tinyint(1) NOT NULL COMMENT '积分奖励提醒',
  `sms_integral` tinyint(1) NOT NULL COMMENT '积分奖励提醒',
  `mail_gift` tinyint(1) NOT NULL COMMENT '礼品奖励提醒',
  `sms_gift` tinyint(1) NOT NULL COMMENT '礼品奖励提醒',
  PRIMARY KEY  (`user_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of fanwe_msg_conf
-- ----------------------------

-- ----------------------------
-- Table structure for `%DB_PREFIX%msg_system`
-- ----------------------------
DROP TABLE IF EXISTS `%DB_PREFIX%msg_system`;
CREATE TABLE `%DB_PREFIX%msg_system` (
  `id` int(11) NOT NULL auto_increment,
  `title` varchar(255) NOT NULL COMMENT '标题',
  `content` text NOT NULL COMMENT '内容',
  `create_time` int(11) NOT NULL COMMENT '发布时间',
  `user_names` text NOT NULL COMMENT '群发的用户名列表，逗号分隔(为空表示发给所有人)',
  `user_ids` text NOT NULL COMMENT '群发的用户ID |号分隔(为空表示发给所有人)',
  `end_time` int(11) NOT NULL COMMENT '过期时间点',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of fanwe_msg_system
-- ----------------------------

-- ----------------------------
-- Table structure for `%DB_PREFIX%msg_template`
-- ----------------------------
DROP TABLE IF EXISTS `%DB_PREFIX%msg_template`;
CREATE TABLE `%DB_PREFIX%msg_template` (
  `id` int(11) NOT NULL auto_increment,
  `name` varchar(255) NOT NULL COMMENT '名称标识',
  `content` text NOT NULL COMMENT '模板内容',
  `type` tinyint(1) NOT NULL COMMENT '类型 0短信 1邮件',
  `is_html` tinyint(1) NOT NULL COMMENT '针对邮件设置的是否超文本标识',
  PRIMARY KEY  (`id`),
  KEY `name` (`name`)
) ENGINE=MyISAM AUTO_INCREMENT=79 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of fanwe_msg_template
-- ----------------------------
INSERT INTO `%DB_PREFIX%msg_template` VALUES ('1', 'TPL_MAIL_USER_VERIFY', '尊敬的用户您的验证码是【{$verify.code}】，此验证码只能用来注册。', '1', '1');
INSERT INTO `%DB_PREFIX%msg_template` VALUES ('2', 'TPL_MAIL_USER_PASSWORD', '尊敬的用户您的验证码是【{$verify.code}】。', '1', '1');
INSERT INTO `%DB_PREFIX%msg_template` VALUES ('38', 'TPL_GEN_SUCCESS_SMS', '尊敬的{$notice.site_name}用户{$notice.user_name}，您的“{$notice.deal_name}”续约已成功通过，感谢您的关注和支持。', '0', '0');
INSERT INTO `%DB_PREFIX%msg_template` VALUES ('3', 'TPL_SMS_PAYMENT', '{$payment_notice.user_name}你好,你所下订单{$payment_notice.order_sn}的收款单{$payment_notice.notice_sn}金额{$payment_notice.money_format}于{$payment_notice.pay_time_format}支付成功', '0', '0');
INSERT INTO `%DB_PREFIX%msg_template` VALUES ('4', 'TPL_MAIL_PAYMENT', '{$payment_notice.user_name}你好,你所下订单{$payment_notice.order_sn}的收款单{$payment_notice.notice_sn}金额{$payment_notice.money_format}于{$payment_notice.pay_time_format}支付成功', '1', '0');
INSERT INTO `%DB_PREFIX%msg_template` VALUES ('5', 'TPL_SMS_VERIFY_CODE', '你的手机号为{$verify.mobile},验证码为{$verify.code}', '0', '0');
INSERT INTO `%DB_PREFIX%msg_template` VALUES ('6', 'TPL_DEAL_NOTICE_SMS', '{$notice.site_name}又有新借款啦!{$notice.deal_name},欢迎来投标{$notice.site_url}', '0', '0');
INSERT INTO `%DB_PREFIX%msg_template` VALUES ('7', 'TPL_MAIL_DEAL_FAILED', '<p>尊敬的用户{$notice.user_name}：&nbsp; </p>\r\n<p>遗憾的通知您，您于{$notice.deal_publish_time}发布的借款“{$notice.deal_name}”流标，您的本次借款行为失败。&nbsp;</p><p>您借款失败的可能原因为：&nbsp; </p>\r\n<br>\r\n<br>\r\n1. 您没能按时提交四项必要信用认证的材料。\r\n<br>\r\n<br>\r\n2. 您在招标期间没有筹集到足够的借款。&nbsp;&nbsp; \r\n<p>如果您属于认证未通过流标，为了您能够成功贷款，请凑齐申请贷款所需要的材料。您可以点击<a href=\"{$notice.help_url}\" target=\"_blank\">需要提供哪些材料？</a>来了解更多所需材料的详情。进行更多的信用认证将有助您获得更高的贷款额度。</p>\r\n<p>如果您属于招标到期流标，为了您能够成功贷款，请适度提高贷款利率，将有助您更快的获得贷款。&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; </p>\r\n<p>点击 <a href=\"{$notice.send_deal_url}\">这里</a>重新发布借款。&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; </p>\r\n<p>感谢您对我们的支持与关注。&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; </p>\r\n<p>{$notice.site_name}&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; </p>\r\n<p>注：此邮件由系统自动发送，请勿回复！&nbsp; </p>\r\n<p>如果您有任何疑问，请您查看 <a href=\"{$notice.help_url}\" target=\"_blank\">帮助</a>，或访问 <a href=\"{$notice.site_url}\" target=\"_blank\">客服中心</a></p>\r\n<p>如果您觉得收到过多邮件，可以点击 <a href=\"{$notice.msg_cof_setting_url}\" target=\"_blank\">这里</a>进行设置\r\n&nbsp; </p>', '1', '1');
INSERT INTO `%DB_PREFIX%msg_template` VALUES ('8', 'TPL_MAIL_LOAD_FAILED', '<p>尊敬的用户{$notice.user_name}：&nbsp; </p>\r\n<p>遗憾的通知您，您于{$notice.deal_load_time}所投的借款“{$notice.deal_name}”流标，您的本次投标行为失败。&nbsp;</p><p>您所投的借款失败的可能原因为：&nbsp; </p>\r\n<br>\r\n<br>\r\n1. 借款者没能按时提交四项必要信用认证的材料。\r\n<br>\r\n<br>\r\n2. 借款者在招标期间没有筹集到足够的借款。&nbsp;&nbsp; \r\n\r\n<p>感谢您对我们的支持与关注。&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; </p>\r\n<p>{$notice.site_name}&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; </p>\r\n<p>注：此邮件由系统自动发送，请勿回复！&nbsp; </p>\r\n<p>如果您有任何疑问，请您查看 <a href=\"{$notice.help_url}\" target=\"_blank\">帮助</a>，或访问 <a href=\"{$notice.site_url}\" target=\"_blank\">客服中心</a></p>\r\n<p>如果您觉得收到过多邮件，可以点击 <a href=\"{$notice.msg_cof_setting_url}\" target=\"_blank\">这里</a>进行设置\r\n&nbsp; </p>', '1', '1');
INSERT INTO `%DB_PREFIX%msg_template` VALUES ('9', 'TPL_DEAL_THREE_SMS', '尊敬的{$notice.site_name}用户 {$notice.user_name} ，您本期贷款的还款日是{$notice.repay_time_d}日，还款金额{$notice.repay_money}元，请按时还款。', '0', '0');
INSERT INTO `%DB_PREFIX%msg_template` VALUES ('10', 'TPL_MAIL_DEAL_MSG', '<p>尊敬的用户{$notice.user_name}：</p>\r\n<p>您好，用户{$notice.msg_user_name}对您发布的借款列表“{$notice.deal_name}”进行了以下留言：</p>\r\n<p>“{$notice.message}”</p>\r\n<p>请您登录{$notice.site_name}借款详情页面查看答复。</p>\r\n<p>点击 <a href=\"{$notice.deal_url}\" target=\"_blank\">这里</a>进行答复。</p>\r\n<p>感谢您对我们的支持与关注！</p>\r\n<p>{$notice.site_name}</p>\r\n<p>注：此邮件由系统自动发送，请勿回复！</p>\r\n<p>如果您有任何疑问，请您查看 <a href=\"{$notice.help_url}\" target=\"_blank\">帮助</a>，或访问 <a href=\"#\" target=\"_blank\">客服中心</a></p>', '1', '1');
INSERT INTO `%DB_PREFIX%msg_template` VALUES ('11', 'TPL_MAIL_DEAL_REPLY_MSG', '<p>尊敬的用户{$notice.user_name}：</p>\r\n<p>您好，用户{$notice.msg_user_name}回复了您对借款列表“{$notice.deal_name}”的留言。具体回复如下：</p>\r\n<p>“{$notice.message}”</p>\r\n<p>点击 <a href=\"{$notice.deal_url}\" target=\"_blank\">这里</a>查看借款列表详情或进行投标。</p>\r\n<p>感谢您对我们的支持与关注！</p>\r\n<p>{$notice.site_name}</p>\r\n<p>注：此邮件由系统自动发送，请勿回复！</p>\r\n<p>如果您有任何疑问，请您查看 <a href=\"{$notice.help_url}\" target=\"_blank\">帮助</a>，或访问 <a href=\"#\" target=\"_blank\">客服中心</a></p>', '1', '1');
INSERT INTO `%DB_PREFIX%msg_template` VALUES ('12', 'TPL_DEAL_THREE_EMAIL', '<p>尊敬的用户{$notice.user_name}：&nbsp; </p>\r\n<p>您的借款“<a href=\"{$notice.deal_url}\">{$notice.deal_name}</a>”本期还款日是{$notice.repay_time_d}日，还款金额{$notice.repay_money}元，请按时还款。【{$notice.site_name}】 </p>\r\n<p>点击 <a href=\"{$notice.repay_url}\">这里</a>进行还款。&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; </p>\r\n<p>感谢您对我们的支持与关注。&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; </p>\r\n<p>{$notice.site_name}&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; </p>\r\n<p>注：此邮件由系统自动发送，请勿回复！&nbsp; </p>\r\n<p>如果您有任何疑问，请您查看 <a href=\"{$notice.help_url}\" target=\"_blank\">帮助</a>，或访问 <a href=\"{$notice.site_url}\" target=\"_blank\">客服中心</a></p>\r\n<p>如果您觉得收到过多邮件，可以点击 <a href=\"{$notice.msg_cof_setting_url}\" target=\"_blank\">这里</a>进行设置rn&nbsp; </p>', '1', '1');
INSERT INTO `%DB_PREFIX%msg_template` VALUES ('13', 'TPL_DEAL_HALF_EMAIL', '<p>尊敬的用户{$notice.user_name}：&nbsp; </p>\r\n<p>您的借款“<a href=\"{$notice.deal_url}\">{$notice.deal_name}</a>”招标完成度超过50%【{$notice.site_name}】 </p>\r\n<p>感谢您对我们的支持与关注。&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; </p>\r\n<p>{$notice.site_name}&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; </p>\r\n<p>注：此邮件由系统自动发送，请勿回复！&nbsp; </p>\r\n<p>如果您有任何疑问，请您查看 <a href=\"{$notice.help_url}\" target=\"_blank\">帮助</a>，或访问 <a href=\"{$notice.site_url}\" target=\"_blank\">客服中心</a></p>\r\n<p>如果您觉得收到过多邮件，可以点击 <a href=\"{$notice.msg_cof_setting_url}\" target=\"_blank\">这里</a>进行设置\r\n&nbsp; </p>', '1', '1');
INSERT INTO `%DB_PREFIX%msg_template` VALUES ('14', 'TPL_DEAL_LOAD_REPAY_EMAIL', '<p>尊敬的用户{$notice.user_name}：&nbsp; </p>\r\n<p>您好，您在{$notice.site_name}所投的的投标“<a href=\"{$notice.deal_url}\">{$notice.deal_name}</a>”成功还款{$notice.repay_money}元 </p>\r\n{if $notice.need_next_repay}\r\n<p>本笔投标的下个还款日为{$notice.next_repay_time}，需还本息{$notice.next_repay_money}元。</p>\r\n{else}\r\n<p>本次投标共获得收益:{$notice.all_repay_money}元,{if $notice.impose_money}其中违约金为:{$notice.impose_money}元,{/if}本次投标已回款完毕！</p>\r\n{/if}\r\n<p>感谢您对我们的支持与关注。&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; </p>\r\n<p>{$notice.site_name}&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; </p>\r\n<p>注：此邮件由系统自动发送，请勿回复！&nbsp; </p>\r\n<p>如果您有任何疑问，请您查看 <a href=\"{$notice.help_url}\" target=\"_blank\">帮助</a>，或访问 <a href=\"{$notice.site_url}\" target=\"_blank\">客服中心</a></p>\r\n<p>如果您觉得收到过多邮件，可以点击 <a href=\"{$notice.msg_cof_setting_url}\" target=\"_blank\">这里</a>进行设置\r\n&nbsp; </p>', '1', '1');
INSERT INTO `%DB_PREFIX%msg_template` VALUES ('24', 'TPL_DEAL_LOAD_REPAY_SMS', '尊敬的{$notice.site_name}用户{$notice.user_name}，您所投的标“{$notice.deal_name}”回款{$notice.repay_money}元，感谢您的关注和支持。', '0', '0');
INSERT INTO `%DB_PREFIX%msg_template` VALUES ('25', 'TPL_CARYY_SUCCESS_SMS', '尊敬的{$notice.site_name}用户{$notice.user_name}，您的{$notice.carry_money}元提现已成功转入您的银行账户，请注意查收，感谢您的关注和支持。', '0', '0');
INSERT INTO `%DB_PREFIX%msg_template` VALUES ('26', 'TPL_MAIL_LOAD_SUCCESS', '<p>尊敬的用户{$notice.user_name}：&nbsp; </p>\r\n<p>很高兴的通知您，您于{$notice.deal_load_time}所投的借款“{$notice.deal_name}”满标，您的本次投标行为成功。&nbsp;</p>\r\n<br>\r\n<br>\r\n<p>点击 <a href=\"{$notice.send_deal_url}\">这里</a>查看您所发布借款。&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; </p>\r\n<p>感谢您对我们的支持与关注。&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; </p>\r\n<p>{$notice.site_name}&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; </p>\r\n<p>注：此邮件由系统自动发送，请勿回复！&nbsp; </p>\r\n<p>如果您有任何疑问，请您查看 <a href=\"{$notice.help_url}\" target=\"_blank\">帮助</a>，或访问 <a href=\"{$notice.site_url}\" target=\"_blank\">客服中心</a></p>\r\n<p>如果您觉得收到过多邮件，可以点击 <a href=\"{$notice.msg_cof_setting_url}\" target=\"_blank\">这里</a>进行设置\r\n&nbsp; </p>', '1', '1');
INSERT INTO `%DB_PREFIX%msg_template` VALUES ('27', 'TPL_MAIL_TRANSFER_FAILED', '<p>尊敬的用户{$notice.user_name}：&nbsp; </p>\r\n<p>很遗憾的通知您，您于{$notice.transfer_time}转让的债权，编号：“{$notice.transfer_id}”因为“{$notice.bad_msg}”自动撤销了。&nbsp;</p>\r\n<br>\r\n<br>\r\n<p>点击 <a href=\"{$notice.send_transfer_url}\">这里</a>查看您所发布的转让信息。&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; </p>\r\n<p>感谢您对我们的支持与关注。&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; </p>\r\n<p>{$notice.site_name}&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; </p>\r\n<p>注：此邮件由系统自动发送，请勿回复！&nbsp; </p>\r\n<p>如果您有任何疑问，请您查看 <a href=\"{$notice.help_url}\" target=\"_blank\">帮助</a>，或访问 <a href=\"{$notice.site_url}\" target=\"_blank\">客服中心</a></p>\r\n<p>如果您觉得收到过多邮件，可以点击 <a href=\"{$notice.msg_cof_setting_url}\" target=\"_blank\">这里</a>进行设置\r\n&nbsp; </p>', '1', '1');
INSERT INTO `%DB_PREFIX%msg_template` VALUES ('28', 'TPL_MAIL_TRANSFER_SUCCESS', '<p>尊敬的用户{$notice.user_name}：&nbsp; </p>\r\n<p>很高兴的通知您，您于{$notice.transfer_time}转让的债权，编号：“{$notice.transfer_id}”已成功转让。&nbsp;</p>\r\n<br>\r\n<br>\r\n<p>点击 <a href=\"{$notice.send_deal_url}\">这里</a>查看您所发布的转让信息。&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; </p>\r\n<p>感谢您对我们的支持与关注。&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; </p>\r\n<p>{$notice.site_name}&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; </p>\r\n<p>注：此邮件由系统自动发送，请勿回复！&nbsp; </p>\r\n<p>如果您有任何疑问，请您查看 <a href=\"{$notice.help_url}\" target=\"_blank\">帮助</a>，或访问 <a href=\"{$notice.site_url}\" target=\"_blank\">客服中心</a></p>\r\n<p>如果您觉得收到过多邮件，可以点击 <a href=\"{$notice.msg_cof_setting_url}\" target=\"_blank\">这里</a>进行设置\r\n&nbsp; </p>', '1', '1');
INSERT INTO `%DB_PREFIX%msg_template` VALUES ('30', 'TPL_SMS_DEAL_FAILED', '尊敬的用户{$notice.user_name}，遗憾的通知您，您于{$notice.deal_publish_time}发布的借款“{$notice.deal_name}”流标。', '0', '0');
INSERT INTO `%DB_PREFIX%msg_template` VALUES ('31', 'TPL_SMS_LOAD_FAILED', '尊敬的用户{$notice.user_name}，遗憾的通知您，您于{$notice.deal_load_time}所投的借款“{$notice.deal_name}”流标。', '0', '0');
INSERT INTO `%DB_PREFIX%msg_template` VALUES ('32', 'TPL_MAIL_DEAL_SUCCESS', '<p>尊敬的用户{$notice.user_name}：&nbsp; </p>\r\n<p>很高兴的通知您，您于{$notice.deal_publish_time}发布的借款“{$notice.deal_name}”满标，您的本次借款行为成功。&nbsp;</p>\r\n<br>\r\n<br>\r\n<p>点击 <a href=\"{$notice.send_deal_url}\">这里</a>查看您所发布借款。&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; </p>\r\n<p>感谢您对我们的支持与关注。&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; </p>\r\n<p>{$notice.site_name}&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; </p>\r\n<p>注：此邮件由系统自动发送，请勿回复！&nbsp; </p>\r\n<p>如果您有任何疑问，请您查看 <a href=\"{$notice.help_url}\" target=\"_blank\">帮助</a>，或访问 <a href=\"{$notice.site_url}\" target=\"_blank\">客服中心</a></p>\r\n<p>如果您觉得收到过多邮件，可以点击 <a href=\"{$notice.msg_cof_setting_url}\" target=\"_blank\">这里</a>进行设置\r\n&nbsp; </p>', '1', '1');
INSERT INTO `%DB_PREFIX%msg_template` VALUES ('33', 'TPL_SMS_DEAL_SUCCESS', '尊敬的用户{$notice.user_name}，很高兴的通知您，您于{$notice.deal_publish_time}发布的借款“{$notice.deal_name}”满标。', '0', '0');
INSERT INTO `%DB_PREFIX%msg_template` VALUES ('34', 'TPL_SMS_LOAD_SUCCESS', '尊敬的用户{$notice.user_name}，很高兴的通知您，您于{$notice.deal_load_time}所投的借款“{$notice.deal_name}”满标。', '0', '0');
INSERT INTO `%DB_PREFIX%msg_template` VALUES ('35', 'TPL_SMS_TRANSFER_FAILED', '尊敬的用户{$notice.user_name}，很遗憾的通知您，您于{$notice.transfer_time}转让的债权编号：“{$notice.transfer_id}”撤销了。', '0', '0');
INSERT INTO `%DB_PREFIX%msg_template` VALUES ('36', 'TPL_SMS_TRANSFER_SUCCESS', '尊敬的用户{$notice.user_name}，很高兴的通知您，您于{$notice.transfer_time}转让的债权，编号：“{$notice.transfer_id}”已成功转让。', '0', '0');
INSERT INTO `%DB_PREFIX%msg_template` VALUES ('37', 'TPL_SMS_DEAL_DELETE', '尊敬的用户{$notice.user_name}，遗憾的通知您，您于{$notice.deal_publish_time}发布的借款“{$notice.deal_name}”因“{$notice.delete_msg}”审核失败了。', '0', '0');
INSERT INTO `%DB_PREFIX%msg_template` VALUES ('39', 'TPL_QUOTA_SUCCESS_SMS', '尊敬的{$notice.site_name}用户{$notice.user_name}，您的{$notice.quota_money}元信用额度申请已成功，感谢您的关注和支持。', '0', '0');
INSERT INTO `%DB_PREFIX%msg_template` VALUES ('40', 'TPL_QUOTA_FAILED_SMS', '尊敬的{$notice.site_name}用户{$notice.user_name}，您的{$notice.quota_money}元信用额度申请 因 “{$notice.msg}” 审核失败了。', '0', '0');
INSERT INTO `%DB_PREFIX%msg_template` VALUES ('41', 'TPL_SMS_REPAY_SUCCESS_MSG', '尊敬的{$notice.site_name}用户{$notice.user_name}，您的借款“{$notice.deal_name}”在第{$notice.index}期{$notice.status}还款{$notice.all_money}元，感谢您的关注和支持。', '0', '0');
INSERT INTO `%DB_PREFIX%msg_template` VALUES ('42', 'TPL_MAIL_RED_ENVELOPE', '尊敬的{$notice.site_name}用户{$notice.user_name}，您的投标“{$notice.deal_name}”获得红包奖励，红包金额{$notice.gift_value}，奖励已于{$notice.release_date}发送。', '1', '0');
INSERT INTO `%DB_PREFIX%msg_template` VALUES ('43', 'TPL_SMS_RED_ENVELOPE', '尊敬的{$notice.site_name}用户{$notice.user_name}，您的投标“{$notice.deal_name}”获得红包奖励，红包金额{$notice.gift_value}，奖励已于{$notice.release_date}发送。', '0', '0');
INSERT INTO `%DB_PREFIX%msg_template` VALUES ('44', 'TPL_MAIL_RATE', '尊敬的{$notice.site_name}用户{$notice.user_name}，您的投标“{$notice.deal_name}”获得收益率奖励，收益率{$notice.gift_value}，奖励已于{$notice.release_date}发送。', '1', '0');
INSERT INTO `%DB_PREFIX%msg_template` VALUES ('45', 'TPL_SMS_RATE', '尊敬的{$notice.site_name}用户{$notice.user_name}，您的投标“{$notice.deal_name}”获得收益率奖励，收益率{$notice.gift_value}，奖励已于{$notice.release_date}发送。', '0', '0');
INSERT INTO `%DB_PREFIX%msg_template` VALUES ('46', 'TPL_MAIL_INTEGRAL', '尊敬的{$notice.site_name}用户{$notice.user_name}，您的投标“{$notice.deal_name}”获得积分奖励，积分值{$notice.gift_value}，奖励已于{$notice.release_date}发送。', '1', '0');
INSERT INTO `%DB_PREFIX%msg_template` VALUES ('47', 'TPL_SMS_INTEGRAL', '尊敬的{$notice.site_name}用户{$notice.user_name}，您的投标“{$notice.deal_name}”获得积分奖励，积分值{$notice.gift_value}，奖励已于{$notice.release_date}发送。', '0', '0');
INSERT INTO `%DB_PREFIX%msg_template` VALUES ('48', 'TPL_MAIL_GIFT', '尊敬的{$notice.site_name}用户{$notice.user_name}，您的投标“{$notice.deal_name}”获得礼品奖励，礼品{$notice.gift_value}，奖励已于{$notice.release_date}发送。', '1', '0');
INSERT INTO `%DB_PREFIX%msg_template` VALUES ('49', 'TPL_SMS_GIFT', '尊敬的{$notice.site_name}用户{$notice.user_name}，您的投标“{$notice.deal_name}”获得礼品奖励，礼品{$notice.gift_value}，奖励已于{$notice.release_date}发送。', '0', '0');
INSERT INTO `%DB_PREFIX%msg_template` VALUES ('50', 'TPL_DEAL_FAILD_SITE_SMS', '<p>感谢您使用{$notice.shop_title}贷款融资，但有一些遗憾的通知您，您于{$notice.time}投标的借款列表{$notice.deal_name}流标，导致您本次所投的贷款列表流标的原因可能包括的原因：</p>{$notice.bad_msg}', '2', '0');
INSERT INTO `%DB_PREFIX%msg_template` VALUES ('51', 'TPL_DEAL_SUCCESS_SITE_SMS', '<p>感谢您使用{$notice.shop_title}贷款融资，很高兴的通知您，您于{$notice.time}投标的借款列表{$notice.deal_name}满标', '2', '0');
INSERT INTO `%DB_PREFIX%msg_template` VALUES ('52', 'TPL_SYN_TRANSFER_REVOKE', '您好，您在{$notice.shop_title}转让的债权 {$notice.url}  {$notice.msg}', '2', '0');
INSERT INTO `%DB_PREFIX%msg_template` VALUES ('53', 'TPL_LEVEL_ADD', '恭喜您，您已经成为{$notice.level_name}', '2', '0');
INSERT INTO `%DB_PREFIX%msg_template` VALUES ('54', 'TPL_LEVEL_DEL', '很报歉，您已经降为{$notice.level_name}', '2', '0');
INSERT INTO `%DB_PREFIX%msg_template` VALUES ('56', 'TPL_XY_LEVEL_ADD', '恭喜您，您的信用等级升级到{$notice.level_name}.', '2', '0');
INSERT INTO `%DB_PREFIX%msg_template` VALUES ('57', 'TPL_XY_LEVEL_DEL', '很报歉，您的信用等级降为{$notice.level_name}.', '2', '0');
INSERT INTO `%DB_PREFIX%msg_template` VALUES ('58', 'TPL_DEAL_OVER_FIVE', '<p>您在{$notice.shop_title}的借款{$notice.url}完成度超过50%', '2', '0');
INSERT INTO `%DB_PREFIX%msg_template` VALUES ('59', 'TPL_REPAY_MONEY_MSG', '您好，您在{$notice.shop_title}的借款{$notice.url}的借款第{$notice.key}期还款{$notice.money}元{$notice.content1}{$notice.content2}', '2', '0');
INSERT INTO `%DB_PREFIX%msg_template` VALUES ('60', 'TPL_REPAY_MONEY_TQTB', '您好，您在{$notice.shop_title}的投标{$notice.url}提前还款,本次投标共获得收益:{$notice.repay_money}元,其中违约金为:{$notice.impose_money}元,本次投标已回款完毕！', '2', '0');
INSERT INTO `%DB_PREFIX%msg_template` VALUES ('61', 'TPL_REPAY_MONEY_TQJK', '您好，您在{$notice.shop_title}的借款{$notice.url}成功提前还款{$notice.repay_money}元，其中违约金为:{$notice.impose_money}元,本笔借款已还款完毕！', '2', '0');
INSERT INTO `%DB_PREFIX%msg_template` VALUES ('62', 'TPL_TRANSFER_REVOKE_USER', '您好，您在{$notice.shop_title}的债权{$notice.url}成功转让给：{$notice.url_name}', '2', '0');
INSERT INTO `%DB_PREFIX%msg_template` VALUES ('63', 'TPL_WITHDRAWS_CASH', '您于{$notice.time}提交的{$notice.money}提现申请我们正在处理，如您填写的账户信息正确无误，您的资金将会于3个工作日内到达您的银行账户.', '2', '0');
INSERT INTO `%DB_PREFIX%msg_template` VALUES ('64', 'TPL_SITE_REPAY', '您好，您在{$notice.shop_title}的投标{$notice.url}成功还款{$notice.money}元{$notice.content}', '2', '0');
INSERT INTO `%DB_PREFIX%msg_template` VALUES ('65', 'TPL_REPLY_MSG', '<p>您好，用户{$notice.user_name}回复了您对借款列表 {$notice.url}的留言。具体回复如下：</p><p>{$notice.msg}</p>', '2', '0');
INSERT INTO `%DB_PREFIX%msg_template` VALUES ('66', 'TPL_WORDS_MSG', '<p>您好，用户{$notice.user_name}对您发布的借款列表{$notice.url}进行了以下留言：</p><p>“{$notice.msg}”</p>', '2', '0');
INSERT INTO `%DB_PREFIX%msg_template` VALUES ('67', 'TPL_INS_SUCCESS_SHEN_HE', '您好，您于 {$sh_notice.time}在{$sh_notice.shop_title}提交的{$sh_notice.type_name}信息已经成功通过审核。<br>您目前的信用分数为{$sh_notice.point}分({$sh_notice.dengji}级),信用额度为{$sh_notice.quota}', '2', '0');
INSERT INTO `%DB_PREFIX%msg_template` VALUES ('68', 'TPL_INS_DEAL_REPAY_SMS', '您在{$sh_notice.time}的借款{$sh_notice.deal_name}，最近一期还款将于{$sh_notice.time}日到期，需还金额{$sh_notice.repay_money}元。', '2', '0');
INSERT INTO `%DB_PREFIX%msg_template` VALUES ('69', 'TPL_INS_FAILED_SHEN_HE', '您好，您于 {$sh_notice.time}在{$sh_notice.shop_title}提交的{$sh_notice.type_name}信息未能通过审核。未能通过的原因是{$sh_notice.msg}.', '2', '0');
INSERT INTO `%DB_PREFIX%msg_template` VALUES ('70', 'TPL_INS_SXQUORA_SUCCESS_SMS', '您于{$sh_notice.time}提交的{$sh_notice.quota}授信额度申请成功，请查看您的申请记录。', '2', '0');
INSERT INTO `%DB_PREFIX%msg_template` VALUES ('71', 'TPL_INS_SXQUORA_FAILED_SMS', '您于{$sh_notice.time}提交的{$sh_notice.quota}授信额度申请申请被我们驳回，驳回原因{$sh_notice.msg};', '2', '0');
INSERT INTO `%DB_PREFIX%msg_template` VALUES ('72', 'TPL_INS_XY_SUCCESS_SMS', '您于{$sh_notice.time}提交的{$sh_notice.deal_name}续约申请通过。', '2', '0');
INSERT INTO `%DB_PREFIX%msg_template` VALUES ('73', 'TPL_INS_XY_FAILED_SMS', '您于{$sh_notice.time}提交的{$sh_notice.deal_name}续约申请被我们驳回，驳回原因{$sh_notice.msg}.', '2', '0');
INSERT INTO `%DB_PREFIX%msg_template` VALUES ('74', 'TPL_INS_QUORA_SUCCESS', '您于{$sh_notice.time}提交的{$sh_notice.quota}信用额度申请成功，请查看您的申请记录。', '2', '0');
INSERT INTO `%DB_PREFIX%msg_template` VALUES ('75', 'TPL_INS_QUOTA_TZ', '您好，{$sh_notice.shop_title}审核部门经过综合评估您的信用资料及网站还款记录，将您的信用额度调整为：{$sh_notice.quota}元.', '2', '0');
INSERT INTO `%DB_PREFIX%msg_template` VALUES ('76', 'TPL_INS_QUORA_FAILED', '您于{$sh_notice.time}信用额度申请申请被我们驳回，驳回原因{$sh_notice.msg}.', '2', '0');
INSERT INTO `%DB_PREFIX%msg_template` VALUES ('77', 'TPL_INS_WITHDRAWS_SUCCESS', '您于{$sh_notice.time}提交的{$sh_notice.money}提现申请汇款成功，请查看您的资金记录。', '2', '0');
INSERT INTO `%DB_PREFIX%msg_template` VALUES ('78', 'TPL_INS_WITHDRAWS_FAILED', '您于{$sh_notice.time}提交的{$sh_notice.money}提现申请提现申请被我们驳回，驳回原因{$sh_notice.msg}.', '2', '0');

-- ----------------------------
-- Table structure for `%DB_PREFIX%m_adv`
-- ----------------------------
DROP TABLE IF EXISTS `%DB_PREFIX%m_adv`;
CREATE TABLE `%DB_PREFIX%m_adv` (
  `id` smallint(6) NOT NULL auto_increment,
  `name` varchar(100) default '',
  `img` varchar(255) default '',
  `page` varchar(20) default '',
  `type` tinyint(1) default '0' COMMENT '1.标签集,2.url地址,3.分类排行,4.最亮达人,5.搜索发现,6.一起拍,7.热门单品排行,8.直接显示某个分享',
  `data` text,
  `sort` smallint(5) default '10',
  `status` tinyint(1) default '1',
  `open_url_type` int(11) default '0' COMMENT '0:使用内置浏览器打开url;1:使用外置浏览器打开',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of fanwe_m_adv
-- ----------------------------

-- ----------------------------
-- Table structure for `%DB_PREFIX%m_config`
-- ----------------------------
DROP TABLE IF EXISTS `%DB_PREFIX%m_config`;
CREATE TABLE `%DB_PREFIX%m_config` (
  `id` int(10) NOT NULL auto_increment,
  `code` varchar(255) default NULL,
  `title` varchar(255) default NULL,
  `val` text,
  `type` tinyint(1) NOT NULL,
  `sort` int(11) default '0',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=31 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of fanwe_m_config
-- ----------------------------
INSERT INTO `%DB_PREFIX%m_config` VALUES ('10', 'kf_phone', '客服电话', '400-000-0000', '0', '1');
INSERT INTO `%DB_PREFIX%m_config` VALUES ('11', 'kf_email', '客服邮箱', 'qq@fanwe.com', '0', '2');
INSERT INTO `%DB_PREFIX%m_config` VALUES ('29', 'ios_upgrade', 'ios版本升级内容', '', '3', '9');
INSERT INTO `%DB_PREFIX%m_config` VALUES ('16', 'page_size', '分页大小', '10', '0', '10');
INSERT INTO `%DB_PREFIX%m_config` VALUES ('17', 'about_info', '关于我们(填文章ID)', '66', '0', '3');
INSERT INTO `%DB_PREFIX%m_config` VALUES ('18', 'program_title', '程序标题名称', '方维P2P信贷系统', '0', '0');
INSERT INTO `%DB_PREFIX%m_config` VALUES ('22', 'android_version', 'android版本号', '2014070802', '0', '4');
INSERT INTO `%DB_PREFIX%m_config` VALUES ('23', 'android_filename', 'android下载包名', 'p2p.apk', '0', '5');
INSERT INTO `%DB_PREFIX%m_config` VALUES ('24', 'ios_version', 'ios版本号', '0', '0', '7');
INSERT INTO `%DB_PREFIX%m_config` VALUES ('25', 'ios_down_url', 'ios下载地址', '', '0', '8');
INSERT INTO `%DB_PREFIX%m_config` VALUES ('28', 'android_upgrade', 'android版本升级内容', '更新版本号0，更新内容：', '3', '6');
INSERT INTO `%DB_PREFIX%m_config` VALUES ('30', 'article_cate_id', '文章分类ID', '15', '0', '11');

-- ----------------------------
-- Table structure for `%DB_PREFIX%nav`
-- ----------------------------
DROP TABLE IF EXISTS `%DB_PREFIX%nav`;
CREATE TABLE `%DB_PREFIX%nav` (
  `id` int(11) NOT NULL auto_increment,
  `name` varchar(255) NOT NULL COMMENT '菜单名称',
  `url` varchar(255) NOT NULL COMMENT '跳转的外链URL',
  `blank` tinyint(1) NOT NULL COMMENT '是否在新窗口打开',
  `sort` int(11) NOT NULL COMMENT '排序 大到小',
  `is_effect` tinyint(1) NOT NULL COMMENT '有效性标识',
  `u_module` varchar(255) NOT NULL COMMENT '指向的前台module',
  `u_action` varchar(255) NOT NULL COMMENT '指向的前台action',
  `u_id` int(11) NOT NULL COMMENT '弃用',
  `u_param` varchar(255) NOT NULL COMMENT 'url的参数，以原始的url传参方式填入 如：id=1&cid=2&pid=3',
  `is_shop` tinyint(1) NOT NULL COMMENT '菜单显示的频道 保留',
  `app_index` varchar(255) NOT NULL COMMENT '指向的前台app应用入口',
  `pid` int(11) NOT NULL COMMENT '父级ID',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=27 DEFAULT CHARSET=utf8 COMMENT='前台导航菜单配置表';

-- ----------------------------
-- Records of fanwe_nav
-- ----------------------------
INSERT INTO `%DB_PREFIX%nav` VALUES ('1', '首页', '', '0', '5', '1', 'index', '', '0', '', '0', 'index', '0');
INSERT INTO `%DB_PREFIX%nav` VALUES ('2', '我要理财', '', '0', '0', '1', 'deals', 'index', '0', '', '0', 'index', '0');
INSERT INTO `%DB_PREFIX%nav` VALUES ('3', '我要贷款', '', '0', '0', '1', 'borrow', '', '0', '', '0', 'index', '0');
INSERT INTO `%DB_PREFIX%nav` VALUES ('4', '我的p2p信贷', '', '0', '0', '1', 'uc_center', '', '0', '', '0', 'index', '0');
INSERT INTO `%DB_PREFIX%nav` VALUES ('5', '安全保障', '', '0', '0', '1', 'guarantee', 'index', '0', '', '0', 'index', '0');
INSERT INTO `%DB_PREFIX%nav` VALUES ('6', '积分商城', '', '0', '0', '1', 'score', '', '0', '', '0', 'index', '0');
INSERT INTO `%DB_PREFIX%nav` VALUES ('7', '平台原理', '', '0', '0', '1', 'aboutp2p', '', '0', '', '0', 'index', '1');
INSERT INTO `%DB_PREFIX%nav` VALUES ('8', '政策法规', '', '0', '0', '1', 'aboutlaws', '', '0', '', '0', 'index', '1');
INSERT INTO `%DB_PREFIX%nav` VALUES ('9', '费用', '', '0', '0', '1', 'aboutfee', '', '0', '', '0', 'index', '1');
INSERT INTO `%DB_PREFIX%nav` VALUES ('10', '关于我们', '', '0', '0', '1', 'article', '', '0', 'id=1', '0', 'index', '1');
INSERT INTO `%DB_PREFIX%nav` VALUES ('11', '个人贷款', '', '0', '5', '1', 'deals', 'index', '0', '', '0', 'index', '2');
INSERT INTO `%DB_PREFIX%nav` VALUES ('12', '工具箱', '', '0', '3', '1', 'tool', '', '0', '', '0', 'index', '2');
INSERT INTO `%DB_PREFIX%nav` VALUES ('13', '关于理财', '', '0', '2', '1', 'deals', 'about', '0', '', '0', 'index', '2');
INSERT INTO `%DB_PREFIX%nav` VALUES ('14', '成为理财人', '', '0', '1', '1', 'belender', '', '0', '', '0', 'index', '2');
INSERT INTO `%DB_PREFIX%nav` VALUES ('15', '贷款说明', '', '0', '0', '1', 'borrow', 'aboutborrow', '0', '', '0', 'index', '3');
INSERT INTO `%DB_PREFIX%nav` VALUES ('16', '信用认证', '', '0', '0', '1', 'borrow', 'creditswitch', '0', '', '0', 'index', '3');
INSERT INTO `%DB_PREFIX%nav` VALUES ('17', '申请贷款', '', '0', '0', '1', 'borrow', 'index', '0', '', '0', 'index', '3');
INSERT INTO `%DB_PREFIX%nav` VALUES ('18', '我的主页', '', '0', '0', '1', 'uc_center', '', '0', '', '0', 'index', '4');
INSERT INTO `%DB_PREFIX%nav` VALUES ('19', '贷款管理', '', '0', '0', '1', 'uc_deal', 'refund', '0', '', '0', 'index', '4');
INSERT INTO `%DB_PREFIX%nav` VALUES ('20', '投标管理', '', '0', '0', '1', 'uc_invest', '', '0', '', '0', 'index', '4');
INSERT INTO `%DB_PREFIX%nav` VALUES ('21', '个人设置', '', '0', '0', '1', 'uc_account', '', '0', '', '0', 'index', '4');
INSERT INTO `%DB_PREFIX%nav` VALUES ('22', '本金保障', '', '0', '0', '1', 'guarantee', 'detail', '0', 'id=8', '0', 'index', '5');
INSERT INTO `%DB_PREFIX%nav` VALUES ('23', '交易安全保障', '', '0', '0', '1', 'guarantee', 'detail', '0', 'id=9', '0', 'index', '5');
INSERT INTO `%DB_PREFIX%nav` VALUES ('24', '贷款审核与保障', '', '0', '0', '1', 'guarantee', 'detail', '0', 'id=10', '0', 'index', '5');
INSERT INTO `%DB_PREFIX%nav` VALUES ('25', '网上理财安全建议', '', '0', '0', '1', 'guarantee', 'detail', '0', 'id=11', '0', 'index', '5');
INSERT INTO `%DB_PREFIX%nav` VALUES ('26', '债权转让', '', '0', '4', '1', 'transfer', 'index', '0', '', '0', 'index', '2');

-- ----------------------------
-- Table structure for `%DB_PREFIX%payment`
-- ----------------------------
DROP TABLE IF EXISTS `%DB_PREFIX%payment`;
CREATE TABLE `%DB_PREFIX%payment` (
  `id` int(11) NOT NULL auto_increment,
  `class_name` varchar(255) NOT NULL COMMENT '支付接口类名',
  `is_effect` tinyint(1) NOT NULL COMMENT '有效性标识',
  `online_pay` tinyint(1) NOT NULL COMMENT '是否为在线支付的接口',
  `fee_amount` decimal(20,4) NOT NULL COMMENT '手续费用的计费值',
  `name` varchar(255) NOT NULL COMMENT '接口名称',
  `description` text NOT NULL COMMENT '描述',
  `total_amount` decimal(20,2) NOT NULL COMMENT '总操作金额',
  `config` text NOT NULL COMMENT '序列号后的配置信息',
  `logo` varchar(255) NOT NULL COMMENT '显示的图标',
  `sort` int(11) NOT NULL,
  `fee_type` tinyint(1) NOT NULL COMMENT '手续费的计费标准 0:定额 1:支付总额的比率',
  `pay_fee_type` tinyint(1) NOT NULL default '0' COMMENT '支付公司收费类型',
  `pay_fee_amount` decimal(20,4) NOT NULL default '0.0000' COMMENT '支付公司收费收费',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of fanwe_payment
-- ----------------------------
INSERT INTO `%DB_PREFIX%payment` VALUES ('1', 'Account', '1', '1', '0.0000', '余额支付', '', '0.00', 'N;', '', '1', '0', '0', '0.0000');
INSERT INTO `%DB_PREFIX%payment` VALUES ('2', 'Voucher', '1', '1', '0.0000', '代金券支付', '', '0.00', 'N;', '', '4', '0', '0', '0.0000');

-- ----------------------------
-- Table structure for `%DB_PREFIX%payment_notice`
-- ----------------------------
DROP TABLE IF EXISTS `%DB_PREFIX%payment_notice`;
CREATE TABLE `%DB_PREFIX%payment_notice` (
  `id` int(11) NOT NULL auto_increment,
  `notice_sn` varchar(255) NOT NULL COMMENT '支付单号',
  `create_time` int(11) NOT NULL COMMENT '下单时间',
  `pay_time` int(11) NOT NULL COMMENT '付款时间',
  `order_id` int(11) NOT NULL COMMENT '关联的订单号ID',
  `is_paid` tinyint(1) NOT NULL COMMENT '是否已支付',
  `user_id` int(11) NOT NULL COMMENT '会员ID',
  `payment_id` int(11) NOT NULL COMMENT '支付接口ID',
  `memo` text NOT NULL COMMENT '付款单备注',
  `money` decimal(20,2) NOT NULL COMMENT '应付金额',
  `outer_notice_sn` varchar(255) NOT NULL COMMENT '第三方支付平台的对帐号',
  `pay_date` date default NULL COMMENT '收款日期',
  `create_date` date NOT NULL COMMENT '记录充值下单时间,方便统计使用',
  `bank_id` varchar(50) default NULL COMMENT '直联银行编号',
  `fee_amount` decimal(20,2) NOT NULL default '0.00' COMMENT '收用户手续费',
  `pay_fee_amount` decimal(20,2) NOT NULL default '0.00' COMMENT '平台付支付公司手续费',
  PRIMARY KEY  (`id`),
  UNIQUE KEY `notice_sn_unk` (`notice_sn`),
  KEY `idx_pn_001` (`pay_date`),
  KEY `idx_pn_002` USING BTREE (`create_date`)
) ENGINE=MyISAM AUTO_INCREMENT=129 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of fanwe_payment_notice
-- ----------------------------

-- ----------------------------
-- Table structure for `%DB_PREFIX%point_group`
-- ----------------------------
DROP TABLE IF EXISTS `%DB_PREFIX%point_group`;
CREATE TABLE `%DB_PREFIX%point_group` (
  `id` int(11) NOT NULL auto_increment,
  `name` varchar(255) NOT NULL COMMENT '分组名称',
  `sort` int(11) NOT NULL COMMENT '排序 大到小',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of fanwe_point_group
-- ----------------------------
INSERT INTO `%DB_PREFIX%point_group` VALUES ('1', '卫生', '100');
INSERT INTO `%DB_PREFIX%point_group` VALUES ('2', '服务', '100');

-- ----------------------------
-- Table structure for `%DB_PREFIX%point_group_link`
-- ----------------------------
DROP TABLE IF EXISTS `%DB_PREFIX%point_group_link`;
CREATE TABLE `%DB_PREFIX%point_group_link` (
  `point_group_id` int(11) NOT NULL COMMENT '商户子类点评评分分组ID fanwe_merchant_type_point_group',
  `category_id` int(11) NOT NULL,
  KEY `group_id` USING BTREE (`point_group_id`),
  KEY `type_id` USING BTREE (`category_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of fanwe_point_group_link
-- ----------------------------
INSERT INTO `%DB_PREFIX%point_group_link` VALUES ('2', '12');
INSERT INTO `%DB_PREFIX%point_group_link` VALUES ('2', '11');
INSERT INTO `%DB_PREFIX%point_group_link` VALUES ('1', '10');
INSERT INTO `%DB_PREFIX%point_group_link` VALUES ('2', '10');
INSERT INTO `%DB_PREFIX%point_group_link` VALUES ('2', '9');
INSERT INTO `%DB_PREFIX%point_group_link` VALUES ('1', '8');
INSERT INTO `%DB_PREFIX%point_group_link` VALUES ('2', '8');
INSERT INTO `%DB_PREFIX%point_group_link` VALUES ('1', '14');
INSERT INTO `%DB_PREFIX%point_group_link` VALUES ('2', '14');
INSERT INTO `%DB_PREFIX%point_group_link` VALUES ('1', '15');
INSERT INTO `%DB_PREFIX%point_group_link` VALUES ('1', '16');
INSERT INTO `%DB_PREFIX%point_group_link` VALUES ('2', '16');
INSERT INTO `%DB_PREFIX%point_group_link` VALUES ('1', '17');
INSERT INTO `%DB_PREFIX%point_group_link` VALUES ('2', '17');

-- ----------------------------
-- Table structure for `%DB_PREFIX%promote_msg`
-- ----------------------------
DROP TABLE IF EXISTS `%DB_PREFIX%promote_msg`;
CREATE TABLE `%DB_PREFIX%promote_msg` (
  `id` int(11) NOT NULL auto_increment,
  `type` tinyint(1) NOT NULL COMMENT '群发推广信息类型(0:短信 1:邮件)',
  `title` varchar(255) NOT NULL COMMENT '群发信息（邮件标题）',
  `content` text NOT NULL COMMENT '群发的内容',
  `send_time` int(11) NOT NULL COMMENT '设置的自动发送的时间',
  `send_status` tinyint(1) NOT NULL COMMENT '发送状态 0:未发送 1:发送中 2:已发送',
  `deal_id` int(11) NOT NULL COMMENT '针对某个借款发送的推广信息',
  `send_type` tinyint(1) NOT NULL COMMENT '发送方式（0:按会员组 1:按订阅地区发送 2:自定义发送，即指定邮箱、手机发送）',
  `send_type_id` int(11) NOT NULL COMMENT '发送类型为按会员组时：会员组ID，发送类型为按地区时：城市ID',
  `send_define_data` text NOT NULL COMMENT '自定义发送时存放指定的邮箱地址、手机号，用半角逗号分隔',
  `is_html` tinyint(1) NOT NULL COMMENT '群发为邮件时的邮件类型，是否为超文本邮件',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=11 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of fanwe_promote_msg
-- ----------------------------

-- ----------------------------
-- Table structure for `%DB_PREFIX%promote_msg_list`
-- ----------------------------
DROP TABLE IF EXISTS `%DB_PREFIX%promote_msg_list`;
CREATE TABLE `%DB_PREFIX%promote_msg_list` (
  `id` int(11) NOT NULL auto_increment,
  `dest` varchar(255) NOT NULL COMMENT '发送的目标(邮件地址/手机号)',
  `send_type` tinyint(1) NOT NULL COMMENT '发送类型 0:短信 1:邮件',
  `content` text NOT NULL COMMENT '信息内容',
  `title` varchar(255) NOT NULL COMMENT '邮件的标题',
  `send_time` int(11) NOT NULL COMMENT '发送的时间',
  `is_send` tinyint(1) NOT NULL COMMENT '是否已发送 0:否 1:等待队列发送',
  `create_time` int(11) NOT NULL COMMENT '生成的时间',
  `user_id` int(11) NOT NULL COMMENT '会员ID',
  `result` text NOT NULL COMMENT '发送结果（如出错存放服务器或接口返回的错误信息）',
  `is_success` tinyint(1) NOT NULL COMMENT '是否发送成功',
  `is_html` tinyint(1) NOT NULL COMMENT '只针对邮件使用，是否为超文本邮件 0:否 1:是',
  `msg_id` int(11) NOT NULL COMMENT '群发信息的原消息ID promote_msg表的数据ID',
  PRIMARY KEY  (`id`),
  KEY `dest_idx` (`dest`)
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of fanwe_promote_msg_list
-- ----------------------------

-- ----------------------------
-- Table structure for `%DB_PREFIX%provisions_risk`
-- ----------------------------
DROP TABLE IF EXISTS `%DB_PREFIX%provisions_risk`;
CREATE TABLE `%DB_PREFIX%provisions_risk` (
  `id` int(11) NOT NULL auto_increment,
  `admin_id` int(11) NOT NULL COMMENT '操作员',
  `amount` decimal(20,4) NOT NULL COMMENT '金额',
  `money` decimal(20,4) NOT NULL COMMENT '余额',
  `optime` int(11) NOT NULL COMMENT '操作时间',
  `memo` text NOT NULL COMMENT '备注',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of fanwe_provisions_risk
-- ----------------------------

-- ----------------------------
-- Table structure for `%DB_PREFIX%quota_submit`
-- ----------------------------
DROP TABLE IF EXISTS `%DB_PREFIX%quota_submit`;
CREATE TABLE `%DB_PREFIX%quota_submit` (
  `id` int(11) NOT NULL auto_increment,
  `money` int(11) NOT NULL COMMENT '申请金额',
  `referraler` varchar(100) NOT NULL COMMENT '推荐人',
  `memo` text NOT NULL COMMENT '详细说明',
  `other_memo` text NOT NULL COMMENT ' 其他地方借款详细说明',
  `create_time` int(11) NOT NULL COMMENT '申请时间',
  `status` tinyint(1) NOT NULL COMMENT '0未处理 1申请通过  2申请失败',
  `op_time` int(11) NOT NULL COMMENT '审核时间',
  `bad_msg` text NOT NULL COMMENT '失败原因',
  `user_id` int(11) NOT NULL,
  `msg` text NOT NULL COMMENT '备注',
  `note` text COMMENT '操作备注',
  PRIMARY KEY  (`id`),
  KEY `idx0` (`status`,`user_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='额度申请表';

-- ----------------------------
-- Records of fanwe_quota_submit
-- ----------------------------

-- ----------------------------
-- Table structure for `%DB_PREFIX%red_envelope_record`
-- ----------------------------
DROP TABLE IF EXISTS `%DB_PREFIX%red_envelope_record`;
CREATE TABLE `%DB_PREFIX%red_envelope_record` (
  `id` int(11) NOT NULL auto_increment,
  `deal_id` int(11) NOT NULL COMMENT '借款ID',
  `deal_load_id` int(11) NOT NULL COMMENT '投标ID',
  `reward_name` varchar(100) NOT NULL COMMENT '奖励名称',
  `money` decimal(20,0) NOT NULL COMMENT '红包金额',
  `user_id` int(11) NOT NULL COMMENT '所属人ID',
  `status` tinyint(1) NOT NULL COMMENT '发放状态 0未发放，1已发放',
  `generation_date` date NOT NULL COMMENT '生成时间',
  `release_date` date NOT NULL COMMENT '发放时间',
  PRIMARY KEY  (`id`),
  KEY `idx_rer_001` USING BTREE (`user_id`,`status`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='红包记录列表';

-- ----------------------------
-- Records of fanwe_red_envelope_record
-- ----------------------------

-- ----------------------------
-- Table structure for `%DB_PREFIX%referrals`
-- ----------------------------
DROP TABLE IF EXISTS `%DB_PREFIX%referrals`;
CREATE TABLE `%DB_PREFIX%referrals` (
  `id` int(11) NOT NULL auto_increment,
  `user_id` int(11) NOT NULL COMMENT ' 被邀请人ID，即返利生成的用户ID',
  `rel_user_id` int(11) NOT NULL COMMENT '邀请人ID（即需要返利的会员ID）',
  `money` decimal(20,2) NOT NULL COMMENT '返利的现金',
  `create_time` int(11) NOT NULL COMMENT '返利生成的时间',
  `repay_time` int(11) NOT NULL COMMENT '返利时间',
  `pay_time` int(11) NOT NULL COMMENT '返利发放的时间',
  `deal_id` int(11) NOT NULL COMMENT '关联的借款id',
  `load_id` int(11) NOT NULL COMMENT '关联的投标id',
  `l_key` int(11) NOT NULL COMMENT '关联的投标第几期还款',
  `score` int(11) NOT NULL COMMENT '返利的积分',
  `point` int(11) NOT NULL COMMENT '返利的信用',
  `referral_type` tinyint(1) NOT NULL default '0' COMMENT '返利方式 0利息 1本金',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='邀请返利记录表';

-- ----------------------------
-- Records of fanwe_referrals
-- ----------------------------

-- ----------------------------
-- Table structure for `%DB_PREFIX%region_conf`
-- ----------------------------
DROP TABLE IF EXISTS `%DB_PREFIX%region_conf`;
CREATE TABLE `%DB_PREFIX%region_conf` (
  `id` int(11) NOT NULL auto_increment,
  `pid` int(11) NOT NULL COMMENT '父级地区ID',
  `name` varchar(50) NOT NULL COMMENT '地区名称',
  `region_level` tinyint(4) NOT NULL COMMENT '1:国 2:省 3:市(县) 4:区(镇)',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=3402 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of fanwe_region_conf
-- ----------------------------
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('3', '1', '安徽', '2');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('4', '1', '福建', '2');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('5', '1', '甘肃', '2');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('6', '1', '广东', '2');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('7', '1', '广西', '2');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('8', '1', '贵州', '2');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('9', '1', '海南', '2');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('10', '1', '河北', '2');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('11', '1', '河南', '2');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('12', '1', '黑龙江', '2');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('13', '1', '湖北', '2');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('14', '1', '湖南', '2');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('15', '1', '吉林', '2');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('16', '1', '江苏', '2');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('17', '1', '江西', '2');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('18', '1', '辽宁', '2');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('19', '1', '内蒙古', '2');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('20', '1', '宁夏', '2');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('21', '1', '青海', '2');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('22', '1', '山东', '2');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('23', '1', '山西', '2');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('24', '1', '陕西', '2');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('26', '1', '四川', '2');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('28', '1', '西藏', '2');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('29', '1', '新疆', '2');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('30', '1', '云南', '2');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('31', '1', '浙江', '2');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('36', '3', '安庆', '3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('37', '3', '蚌埠', '3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('38', '3', '巢湖', '3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('39', '3', '池州', '3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('40', '3', '滁州', '3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('41', '3', '阜阳', '3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('42', '3', '淮北', '3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('43', '3', '淮南', '3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('44', '3', '黄山', '3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('45', '3', '六安', '3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('46', '3', '马鞍山', '3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('47', '3', '宿州', '3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('48', '3', '铜陵', '3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('49', '3', '芜湖', '3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('50', '3', '宣城', '3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('51', '3', '亳州', '3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('52', '2', '北京', '2');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('53', '4', '福州', '3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('54', '4', '龙岩', '3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('55', '4', '南平', '3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('56', '4', '宁德', '3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('57', '4', '莆田', '3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('58', '4', '泉州', '3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('59', '4', '三明', '3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('60', '4', '厦门', '3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('61', '4', '漳州', '3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('62', '5', '兰州', '3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('63', '5', '白银', '3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('64', '5', '定西', '3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('65', '5', '甘南', '3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('66', '5', '嘉峪关', '3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('67', '5', '金昌', '3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('68', '5', '酒泉', '3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('69', '5', '临夏', '3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('70', '5', '陇南', '3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('71', '5', '平凉', '3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('72', '5', '庆阳', '3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('73', '5', '天水', '3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('74', '5', '武威', '3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('75', '5', '张掖', '3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('76', '6', '广州', '3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('77', '6', '深圳', '3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('78', '6', '潮州', '3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('79', '6', '东莞', '3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('80', '6', '佛山', '3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('81', '6', '河源', '3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('82', '6', '惠州', '3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('83', '6', '江门', '3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('84', '6', '揭阳', '3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('85', '6', '茂名', '3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('86', '6', '梅州', '3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('87', '6', '清远', '3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('88', '6', '汕头', '3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('89', '6', '汕尾', '3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('90', '6', '韶关', '3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('91', '6', '阳江', '3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('92', '6', '云浮', '3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('93', '6', '湛江', '3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('94', '6', '肇庆', '3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('95', '6', '中山', '3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('96', '6', '珠海', '3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('97', '7', '南宁', '3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('98', '7', '桂林', '3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('99', '7', '百色', '3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('100', '7', '北海', '3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('101', '7', '崇左', '3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('102', '7', '防城港', '3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('103', '7', '贵港', '3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('104', '7', '河池', '3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('105', '7', '贺州', '3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('106', '7', '来宾', '3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('107', '7', '柳州', '3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('108', '7', '钦州', '3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('109', '7', '梧州', '3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('110', '7', '玉林', '3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('111', '8', '贵阳', '3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('112', '8', '安顺', '3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('113', '8', '毕节', '3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('114', '8', '六盘水', '3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('115', '8', '黔东南', '3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('116', '8', '黔南', '3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('117', '8', '黔西南', '3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('118', '8', '铜仁', '3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('119', '8', '遵义', '3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('120', '9', '海口', '3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('121', '9', '三亚', '3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('122', '9', '白沙', '3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('123', '9', '保亭', '3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('124', '9', '昌江', '3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('125', '9', '澄迈县', '3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('126', '9', '定安县', '3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('127', '9', '东方', '3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('128', '9', '乐东', '3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('129', '9', '临高县', '3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('130', '9', '陵水', '3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('131', '9', '琼海', '3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('132', '9', '琼中', '3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('133', '9', '屯昌县', '3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('134', '9', '万宁', '3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('135', '9', '文昌', '3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('136', '9', '五指山', '3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('137', '9', '儋州', '3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('138', '10', '石家庄', '3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('139', '10', '保定', '3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('140', '10', '沧州', '3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('141', '10', '承德', '3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('142', '10', '邯郸', '3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('143', '10', '衡水', '3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('144', '10', '廊坊', '3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('145', '10', '秦皇岛', '3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('146', '10', '唐山', '3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('147', '10', '邢台', '3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('148', '10', '张家口', '3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('149', '11', '郑州', '3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('150', '11', '洛阳', '3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('151', '11', '开封', '3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('152', '11', '安阳', '3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('153', '11', '鹤壁', '3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('154', '11', '济源', '3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('155', '11', '焦作', '3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('156', '11', '南阳', '3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('157', '11', '平顶山', '3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('158', '11', '三门峡', '3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('159', '11', '商丘', '3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('160', '11', '新乡', '3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('161', '11', '信阳', '3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('162', '11', '许昌', '3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('163', '11', '周口', '3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('164', '11', '驻马店', '3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('165', '11', '漯河', '3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('166', '11', '濮阳', '3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('167', '12', '哈尔滨', '3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('168', '12', '大庆', '3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('169', '12', '大兴安岭', '3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('170', '12', '鹤岗', '3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('171', '12', '黑河', '3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('172', '12', '鸡西', '3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('173', '12', '佳木斯', '3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('174', '12', '牡丹江', '3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('175', '12', '七台河', '3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('176', '12', '齐齐哈尔', '3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('177', '12', '双鸭山', '3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('178', '12', '绥化', '3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('179', '12', '伊春', '3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('180', '13', '武汉', '3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('181', '13', '仙桃', '3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('182', '13', '鄂州', '3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('183', '13', '黄冈', '3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('184', '13', '黄石', '3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('185', '13', '荆门', '3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('186', '13', '荆州', '3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('187', '13', '潜江', '3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('188', '13', '神农架林区', '3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('189', '13', '十堰', '3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('190', '13', '随州', '3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('191', '13', '天门', '3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('192', '13', '咸宁', '3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('193', '13', '襄樊', '3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('194', '13', '孝感', '3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('195', '13', '宜昌', '3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('196', '13', '恩施', '3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('197', '14', '长沙', '3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('198', '14', '张家界', '3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('199', '14', '常德', '3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('200', '14', '郴州', '3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('201', '14', '衡阳', '3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('202', '14', '怀化', '3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('203', '14', '娄底', '3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('204', '14', '邵阳', '3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('205', '14', '湘潭', '3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('206', '14', '湘西', '3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('207', '14', '益阳', '3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('208', '14', '永州', '3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('209', '14', '岳阳', '3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('210', '14', '株洲', '3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('211', '15', '长春', '3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('212', '15', '吉林', '3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('213', '15', '白城', '3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('214', '15', '白山', '3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('215', '15', '辽源', '3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('216', '15', '四平', '3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('217', '15', '松原', '3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('218', '15', '通化', '3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('219', '15', '延边', '3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('220', '16', '南京', '3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('221', '16', '苏州', '3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('222', '16', '无锡', '3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('223', '16', '常州', '3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('224', '16', '淮安', '3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('225', '16', '连云港', '3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('226', '16', '南通', '3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('227', '16', '宿迁', '3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('228', '16', '泰州', '3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('229', '16', '徐州', '3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('230', '16', '盐城', '3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('231', '16', '扬州', '3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('232', '16', '镇江', '3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('233', '17', '南昌', '3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('234', '17', '抚州', '3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('235', '17', '赣州', '3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('236', '17', '吉安', '3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('237', '17', '景德镇', '3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('238', '17', '九江', '3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('239', '17', '萍乡', '3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('240', '17', '上饶', '3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('241', '17', '新余', '3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('242', '17', '宜春', '3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('243', '17', '鹰潭', '3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('244', '18', '沈阳', '3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('245', '18', '大连', '3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('246', '18', '鞍山', '3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('247', '18', '本溪', '3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('248', '18', '朝阳', '3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('249', '18', '丹东', '3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('250', '18', '抚顺', '3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('251', '18', '阜新', '3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('252', '18', '葫芦岛', '3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('253', '18', '锦州', '3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('254', '18', '辽阳', '3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('255', '18', '盘锦', '3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('256', '18', '铁岭', '3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('257', '18', '营口', '3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('258', '19', '呼和浩特', '3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('259', '19', '阿拉善盟', '3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('260', '19', '巴彦淖尔盟', '3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('261', '19', '包头', '3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('262', '19', '赤峰', '3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('263', '19', '鄂尔多斯', '3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('264', '19', '呼伦贝尔', '3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('265', '19', '通辽', '3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('266', '19', '乌海', '3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('267', '19', '乌兰察布市', '3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('268', '19', '锡林郭勒盟', '3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('269', '19', '兴安盟', '3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('270', '20', '银川', '3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('271', '20', '固原', '3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('272', '20', '石嘴山', '3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('273', '20', '吴忠', '3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('274', '20', '中卫', '3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('275', '21', '西宁', '3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('276', '21', '果洛', '3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('277', '21', '海北', '3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('278', '21', '海东', '3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('279', '21', '海南', '3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('280', '21', '海西', '3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('281', '21', '黄南', '3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('282', '21', '玉树', '3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('283', '22', '济南', '3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('284', '22', '青岛', '3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('285', '22', '滨州', '3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('286', '22', '德州', '3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('287', '22', '东营', '3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('288', '22', '菏泽', '3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('289', '22', '济宁', '3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('290', '22', '莱芜', '3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('291', '22', '聊城', '3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('292', '22', '临沂', '3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('293', '22', '日照', '3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('294', '22', '泰安', '3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('295', '22', '威海', '3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('296', '22', '潍坊', '3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('297', '22', '烟台', '3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('298', '22', '枣庄', '3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('299', '22', '淄博', '3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('300', '23', '太原', '3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('301', '23', '长治', '3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('302', '23', '大同', '3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('303', '23', '晋城', '3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('304', '23', '晋中', '3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('305', '23', '临汾', '3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('306', '23', '吕梁', '3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('307', '23', '朔州', '3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('308', '23', '忻州', '3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('309', '23', '阳泉', '3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('310', '23', '运城', '3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('311', '24', '西安', '3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('312', '24', '安康', '3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('313', '24', '宝鸡', '3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('314', '24', '汉中', '3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('315', '24', '商洛', '3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('316', '24', '铜川', '3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('317', '24', '渭南', '3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('318', '24', '咸阳', '3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('319', '24', '延安', '3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('320', '24', '榆林', '3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('321', '25', '上海', '2');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('322', '26', '成都', '3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('323', '26', '绵阳', '3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('324', '26', '阿坝', '3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('325', '26', '巴中', '3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('326', '26', '达州', '3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('327', '26', '德阳', '3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('328', '26', '甘孜', '3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('329', '26', '广安', '3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('330', '26', '广元', '3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('331', '26', '乐山', '3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('332', '26', '凉山', '3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('333', '26', '眉山', '3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('334', '26', '南充', '3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('335', '26', '内江', '3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('336', '26', '攀枝花', '3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('337', '26', '遂宁', '3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('338', '26', '雅安', '3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('339', '26', '宜宾', '3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('340', '26', '资阳', '3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('341', '26', '自贡', '3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('342', '26', '泸州', '3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('343', '27', '天津', '2');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('344', '28', '拉萨', '3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('345', '28', '阿里', '3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('346', '28', '昌都', '3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('347', '28', '林芝', '3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('348', '28', '那曲', '3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('349', '28', '日喀则', '3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('350', '28', '山南', '3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('351', '29', '乌鲁木齐', '3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('352', '29', '阿克苏', '3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('353', '29', '阿拉尔', '3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('354', '29', '巴音郭楞', '3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('355', '29', '博尔塔拉', '3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('356', '29', '昌吉', '3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('357', '29', '哈密', '3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('358', '29', '和田', '3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('359', '29', '喀什', '3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('360', '29', '克拉玛依', '3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('361', '29', '克孜勒苏', '3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('362', '29', '石河子', '3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('363', '29', '图木舒克', '3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('364', '29', '吐鲁番', '3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('365', '29', '五家渠', '3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('366', '29', '伊犁', '3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('367', '30', '昆明', '3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('368', '30', '怒江', '3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('369', '30', '普洱', '3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('370', '30', '丽江', '3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('371', '30', '保山', '3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('372', '30', '楚雄', '3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('373', '30', '大理', '3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('374', '30', '德宏', '3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('375', '30', '迪庆', '3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('376', '30', '红河', '3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('377', '30', '临沧', '3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('378', '30', '曲靖', '3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('379', '30', '文山', '3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('380', '30', '西双版纳', '3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('381', '30', '玉溪', '3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('382', '30', '昭通', '3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('383', '31', '杭州', '3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('384', '31', '湖州', '3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('385', '31', '嘉兴', '3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('386', '31', '金华', '3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('387', '31', '丽水', '3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('388', '31', '宁波', '3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('389', '31', '绍兴', '3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('390', '31', '台州', '3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('391', '31', '温州', '3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('392', '31', '舟山', '3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('393', '31', '衢州', '3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('394', '32', '重庆', '2');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('395', '33', '香港', '2');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('396', '34', '澳门', '2');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('397', '35', '台湾', '2');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('500', '52', '东城区', '3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('501', '52', '西城区', '3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('502', '52', '海淀区', '3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('503', '52', '朝阳区', '3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('504', '52', '崇文区', '3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('505', '52', '宣武区', '3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('506', '52', '丰台区', '3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('507', '52', '石景山区', '3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('508', '52', '房山区', '3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('509', '52', '门头沟区', '3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('510', '52', '通州区', '3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('511', '52', '顺义区', '3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('512', '52', '昌平区', '3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('513', '52', '怀柔区', '3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('514', '52', '平谷区', '3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('515', '52', '大兴区', '3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('516', '52', '密云县', '3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('517', '52', '延庆县', '3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('2703', '321', '长宁区', '3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('2704', '321', '闸北区', '3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('2705', '321', '闵行区', '3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('2706', '321', '徐汇区', '3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('2707', '321', '浦东新区', '3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('2708', '321', '杨浦区', '3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('2709', '321', '普陀区', '3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('2710', '321', '静安区', '3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('2711', '321', '卢湾区', '3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('2712', '321', '虹口区', '3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('2713', '321', '黄浦区', '3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('2714', '321', '南汇区', '3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('2715', '321', '松江区', '3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('2716', '321', '嘉定区', '3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('2717', '321', '宝山区', '3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('2718', '321', '青浦区', '3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('2719', '321', '金山区', '3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('2720', '321', '奉贤区', '3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('2721', '321', '崇明县', '3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('2912', '343', '和平区', '3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('2913', '343', '河西区', '3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('2914', '343', '南开区', '3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('2915', '343', '河北区', '3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('2916', '343', '河东区', '3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('2917', '343', '红桥区', '3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('2918', '343', '东丽区', '3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('2919', '343', '津南区', '3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('2920', '343', '西青区', '3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('2921', '343', '北辰区', '3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('2922', '343', '塘沽区', '3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('2923', '343', '汉沽区', '3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('2924', '343', '大港区', '3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('2925', '343', '武清区', '3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('2926', '343', '宝坻区', '3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('2927', '343', '经济开发区', '3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('2928', '343', '宁河县', '3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('2929', '343', '静海县', '3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('2930', '343', '蓟县', '3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('3325', '394', '合川区', '3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('3326', '394', '江津区', '3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('3327', '394', '南川区', '3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('3328', '394', '永川区', '3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('3329', '394', '南岸区', '3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('3330', '394', '渝北区', '3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('3331', '394', '万盛区', '3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('3332', '394', '大渡口区', '3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('3333', '394', '万州区', '3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('3334', '394', '北碚区', '3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('3335', '394', '沙坪坝区', '3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('3336', '394', '巴南区', '3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('3337', '394', '涪陵区', '3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('3338', '394', '江北区', '3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('3339', '394', '九龙坡区', '3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('3340', '394', '渝中区', '3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('3341', '394', '黔江开发区', '3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('3342', '394', '长寿区', '3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('3343', '394', '双桥区', '3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('3344', '394', '綦江县', '3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('3345', '394', '潼南县', '3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('3346', '394', '铜梁县', '3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('3347', '394', '大足县', '3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('3348', '394', '荣昌县', '3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('3349', '394', '璧山县', '3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('3350', '394', '垫江县', '3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('3351', '394', '武隆县', '3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('3352', '394', '丰都县', '3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('3353', '394', '城口县', '3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('3354', '394', '梁平县', '3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('3355', '394', '开县', '3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('3356', '394', '巫溪县', '3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('3357', '394', '巫山县', '3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('3358', '394', '奉节县', '3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('3359', '394', '云阳县', '3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('3360', '394', '忠县', '3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('3361', '394', '石柱', '3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('3362', '394', '彭水', '3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('3363', '394', '酉阳', '3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('3364', '394', '秀山', '3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('3365', '395', '沙田区', '3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('3366', '395', '东区', '3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('3367', '395', '观塘区', '3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('3368', '395', '黄大仙区', '3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('3369', '395', '九龙城区', '3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('3370', '395', '屯门区', '3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('3371', '395', '葵青区', '3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('3372', '395', '元朗区', '3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('3373', '395', '深水埗区', '3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('3374', '395', '西贡区', '3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('3375', '395', '大埔区', '3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('3376', '395', '湾仔区', '3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('3377', '395', '油尖旺区', '3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('3378', '395', '北区', '3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('3379', '395', '南区', '3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('3380', '395', '荃湾区', '3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('3381', '395', '中西区', '3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('3382', '395', '离岛区', '3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('3383', '396', '澳门', '3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('3384', '397', '台北', '3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('3385', '397', '高雄', '3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('3386', '397', '基隆', '3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('3387', '397', '台中', '3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('3388', '397', '台南', '3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('3389', '397', '新竹', '3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('3390', '397', '嘉义', '3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('3391', '397', '宜兰县', '3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('3392', '397', '桃园县', '3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('3393', '397', '苗栗县', '3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('3394', '397', '彰化县', '3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('3395', '397', '南投县', '3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('3396', '397', '云林县', '3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('3397', '397', '屏东县', '3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('3398', '397', '台东县', '3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('3399', '397', '花莲县', '3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('3400', '397', '澎湖县', '3');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('3401', '3', '合肥', '3');

-- ----------------------------
-- Table structure for `%DB_PREFIX%remind_count`
-- ----------------------------
DROP TABLE IF EXISTS `%DB_PREFIX%remind_count`;
CREATE TABLE `%DB_PREFIX%remind_count` (
  `id` int(11) NOT NULL auto_increment,
  `topic_count` int(11) NOT NULL COMMENT '分享数',
  `topic_count_time` int(11) NOT NULL,
  `dp_count` int(11) NOT NULL COMMENT '点评统计',
  `dp_count_time` int(11) NOT NULL,
  `msg_count` int(11) NOT NULL COMMENT '会员留言',
  `msg_count_time` int(11) NOT NULL,
  `buy_msg_count` int(11) NOT NULL COMMENT '弃用',
  `buy_msg_count_time` int(11) NOT NULL,
  `order_count` int(11) NOT NULL COMMENT '订单统计',
  `order_count_time` int(11) NOT NULL,
  `refund_count` int(11) NOT NULL COMMENT '退款统计',
  `refund_count_time` int(11) NOT NULL,
  `retake_count` int(11) NOT NULL COMMENT '弃用',
  `retake_count_time` int(11) NOT NULL,
  `incharge_count` int(11) NOT NULL COMMENT '充值统计',
  `incharge_count_time` int(11) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of fanwe_remind_count
-- ----------------------------

-- ----------------------------
-- Table structure for `%DB_PREFIX%reportguy`
-- ----------------------------
DROP TABLE IF EXISTS `%DB_PREFIX%reportguy`;
CREATE TABLE `%DB_PREFIX%reportguy` (
  `id` int(11) NOT NULL auto_increment,
  `user_id` int(11) NOT NULL COMMENT '举报人',
  `r_user_id` int(11) NOT NULL COMMENT '被举报人',
  `reason` varchar(50) NOT NULL COMMENT '举报原因',
  `content` text NOT NULL COMMENT '举报内容',
  `status` tinyint(1) NOT NULL COMMENT '是否处理 0未处理 1已处理',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of fanwe_reportguy
-- ----------------------------

-- ----------------------------
-- Table structure for `%DB_PREFIX%role`
-- ----------------------------
DROP TABLE IF EXISTS `%DB_PREFIX%role`;
CREATE TABLE `%DB_PREFIX%role` (
  `id` int(11) NOT NULL auto_increment,
  `name` varchar(255) NOT NULL,
  `is_effect` tinyint(1) NOT NULL,
  `is_delete` tinyint(1) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of fanwe_role
-- ----------------------------
INSERT INTO `%DB_PREFIX%role` VALUES ('4', '测试管理员', '1', '0');

-- ----------------------------
-- Table structure for `%DB_PREFIX%role_access`
-- ----------------------------
DROP TABLE IF EXISTS `%DB_PREFIX%role_access`;
CREATE TABLE `%DB_PREFIX%role_access` (
  `id` int(11) NOT NULL auto_increment,
  `role_id` int(11) NOT NULL COMMENT '角色ID',
  `node` varchar(255) NOT NULL COMMENT '节点action名',
  `module` varchar(255) NOT NULL COMMENT '模块名',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=87 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of fanwe_role_access
-- ----------------------------

-- ----------------------------
-- Table structure for `%DB_PREFIX%score_exchange_record`
-- ----------------------------
DROP TABLE IF EXISTS `%DB_PREFIX%score_exchange_record`;
CREATE TABLE `%DB_PREFIX%score_exchange_record` (
  `id` int(11) NOT NULL auto_increment COMMENT '会员ID',
  `user_id` int(11) NOT NULL,
  `integral` int(11) NOT NULL COMMENT '兑换积分',
  `vip_id` int(11) NOT NULL COMMENT 'VIP等级',
  `exchange_date` date NOT NULL COMMENT '兑换日期',
  `number` int(11) default NULL COMMENT '笔数',
  `cash` decimal(20,2) NOT NULL COMMENT '兑现金额',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='积分兑换记录表';

-- ----------------------------
-- Records of fanwe_score_exchange_record
-- ----------------------------

-- ----------------------------
-- Table structure for `%DB_PREFIX%sc_service`
-- ----------------------------
DROP TABLE IF EXISTS `%DB_PREFIX%sc_service`;
CREATE TABLE `%DB_PREFIX%sc_service` (
  `id` int(11) NOT NULL auto_increment,
  `user_id` int(11) NOT NULL COMMENT '会员ID',
  `vip_id` int(11) NOT NULL COMMENT 'VIP等级',
  `sc_name` varchar(100) NOT NULL COMMENT 'VIP 客服专员',
  `tel` decimal(20,0) NOT NULL COMMENT '电话',
  `qq` varchar(100) NOT NULL COMMENT 'QQ',
  `email` varchar(150) NOT NULL COMMENT '电子邮箱',
  `note` text NOT NULL COMMENT '备注',
  `is_delete` tinyint(1) NOT NULL COMMENT '删除标识',
  `is_effect` tinyint(1) NOT NULL COMMENT '有效性标识',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='专员客服表';

-- ----------------------------
-- Records of fanwe_sc_service
-- ----------------------------

-- ----------------------------
-- Table structure for `%DB_PREFIX%security`
-- ----------------------------
DROP TABLE IF EXISTS `%DB_PREFIX%security`;
CREATE TABLE `%DB_PREFIX%security` (
  `id` int(11) NOT NULL auto_increment,
  `admin_id` int(11) NOT NULL COMMENT '操作员',
  `amount` decimal(20,4) NOT NULL COMMENT '金额',
  `money` decimal(20,4) NOT NULL default '0.0000' COMMENT '余额',
  `optime` int(11) NOT NULL COMMENT '操作时间',
  `memo` text NOT NULL COMMENT '备注',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of fanwe_security
-- ----------------------------

-- ----------------------------
-- Table structure for `%DB_PREFIX%session`
-- ----------------------------
DROP TABLE IF EXISTS `%DB_PREFIX%session`;
CREATE TABLE `%DB_PREFIX%session` (
  `session_id` varchar(255) NOT NULL,
  `session_data` text NOT NULL,
  `session_time` int(11) NOT NULL,
  PRIMARY KEY  (`session_id`),
  KEY `session_id` (`session_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of fanwe_session
-- ----------------------------

-- ----------------------------
-- Table structure for `%DB_PREFIX%site_money_log`
-- ----------------------------
DROP TABLE IF EXISTS `%DB_PREFIX%site_money_log`;
CREATE TABLE `%DB_PREFIX%site_money_log` (
  `id` int(11) NOT NULL auto_increment,
  `user_id` int(11) NOT NULL COMMENT '关联用户',
  `money` decimal(20,2) NOT NULL COMMENT '操作金额',
  `memo` text NOT NULL COMMENT '操作备注',
  `type` tinyint(2) NOT NULL COMMENT '7提前回收，9提现手续费，10借款管理费，12逾期管理费，13人工充值，14借款服务费，17债权转让管理费，18开户奖励，20投标管理费，22兑换，23邀请返利，24投标返利，25签到成功，26逾期罚金（垫付后），27其他费用',
  `create_time` int(11) NOT NULL COMMENT '操作时间',
  `create_time_ymd` date NOT NULL COMMENT '操作时间 ymd',
  `create_time_ym` int(6) NOT NULL COMMENT '操作时间 ym',
  `create_time_y` int(4) NOT NULL COMMENT '操作时间 y',
  PRIMARY KEY  (`id`),
  KEY `idx0` (`user_id`,`type`,`create_time`),
  KEY `idx1` (`user_id`,`type`,`create_time_ymd`),
  KEY `idx2` (`user_id`,`type`),
  KEY `create_time_ymd` (`create_time_ymd`),
  KEY `idx3` (`type`,`create_time`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='网站收益日志表';

-- ----------------------------
-- Records of fanwe_site_money_log
-- ----------------------------

-- ----------------------------
-- Table structure for `%DB_PREFIX%sms`
-- ----------------------------
DROP TABLE IF EXISTS `%DB_PREFIX%sms`;
CREATE TABLE `%DB_PREFIX%sms` (
  `id` int(11) NOT NULL auto_increment,
  `name` varchar(255) NOT NULL COMMENT '短信接口显示名称',
  `description` text NOT NULL COMMENT '描述',
  `class_name` varchar(255) NOT NULL COMMENT '类名',
  `server_url` text NOT NULL COMMENT '接口的服务器通讯地址',
  `user_name` varchar(255) NOT NULL COMMENT '接口商验证用用户名',
  `password` varchar(255) NOT NULL COMMENT '接口商验证用密码',
  `config` text NOT NULL COMMENT '额外的配置信息',
  `is_effect` tinyint(1) NOT NULL COMMENT '有效性',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of fanwe_sms
-- ----------------------------

-- ----------------------------
-- Table structure for `%DB_PREFIX%topic`
-- ----------------------------
DROP TABLE IF EXISTS `%DB_PREFIX%topic`;
CREATE TABLE `%DB_PREFIX%topic` (
  `id` int(11) NOT NULL auto_increment,
  `fav_id` varchar(255) NOT NULL COMMENT '收藏的id',
  `type` varchar(255) NOT NULL COMMENT 'focus关注，1',
  `user_id` int(11) NOT NULL COMMENT '会员ID',
  `user_name` varchar(255) NOT NULL COMMENT '会员名',
  `l_user_id` int(11) NOT NULL COMMENT '借款人的ID',
  `is_effect` tinyint(1) NOT NULL COMMENT '有效性',
  `create_time` int(11) NOT NULL COMMENT '触发时间',
  PRIMARY KEY  (`id`),
  KEY `create_time` (`create_time`),
  KEY `user_id` (`user_id`),
  KEY `type` (`type`),
  KEY `is_effect` (`is_effect`),
  KEY `ordery_sort` (`create_time`),
  KEY `multi_key` (`is_effect`,`create_time`),
  KEY `index_01` (`fav_id`,`user_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of fanwe_topic
-- ----------------------------

-- ----------------------------
-- Table structure for `%DB_PREFIX%urls`
-- ----------------------------
DROP TABLE IF EXISTS `%DB_PREFIX%urls`;
CREATE TABLE `%DB_PREFIX%urls` (
  `id` int(11) NOT NULL auto_increment,
  `url` text NOT NULL,
  `count` int(11) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of fanwe_urls
-- ----------------------------

-- ----------------------------
-- Table structure for `%DB_PREFIX%user`
-- ----------------------------
DROP TABLE IF EXISTS `%DB_PREFIX%user`;
CREATE TABLE `%DB_PREFIX%user` (
  `id` int(11) NOT NULL auto_increment,
  `user_name` varchar(255) NOT NULL COMMENT '会员名',
  `short_name` varchar(255) default NULL COMMENT '缩略名',
  `brief` text COMMENT '担保方介绍',
  `header` text COMMENT '头部',
  `company_brief` text,
  `history` text COMMENT '发展史',
  `content` text COMMENT '内容',
  `sort` int(11) default NULL COMMENT '排序',
  `acct_type` int(11) default NULL COMMENT '担保账户类型(0:机构，1:个人)',
  `user_pwd` varchar(255) NOT NULL COMMENT '会员密码',
  `create_time` int(11) NOT NULL COMMENT '注册时间',
  `update_time` int(11) NOT NULL COMMENT '修改时间',
  `login_ip` varchar(255) NOT NULL COMMENT '最后登录IP',
  `group_id` int(11) NOT NULL COMMENT '会员组ID',
  `is_effect` tinyint(1) NOT NULL COMMENT '是否被禁用（未验证）',
  `is_delete` tinyint(1) NOT NULL COMMENT '删除',
  `email` varchar(255) NOT NULL COMMENT '会员邮件',
  `idno` varchar(20) NOT NULL COMMENT '身份证号',
  `idcardpassed` tinyint(1) NOT NULL COMMENT '身份证是否审核通过',
  `idcardpassed_time` int(11) NOT NULL COMMENT '通过时间',
  `real_name` varchar(50) NOT NULL COMMENT '真实姓名',
  `mobile` varchar(255) NOT NULL COMMENT '会员手机号',
  `mobilepassed` tinyint(1) NOT NULL COMMENT '手机是否验证通过',
  `score` int(11) NOT NULL COMMENT '积分',
  `money` decimal(20,2) NOT NULL COMMENT '资金',
  `quota` decimal(20,0) NOT NULL default '0' COMMENT '额度',
  `lock_money` decimal(20,2) NOT NULL COMMENT '冻结资金',
  `verify` varchar(255) NOT NULL COMMENT '验证码',
  `code` varchar(255) NOT NULL COMMENT '登录用的标识码',
  `pid` int(11) NOT NULL COMMENT '推荐人ID',
  `referer_memo` varchar(255) NOT NULL COMMENT '邀请备注',
  `login_time` int(11) NOT NULL COMMENT '最后登录时间',
  `referral_count` int(11) NOT NULL COMMENT ' 返利数量',
  `password_verify` varchar(255) NOT NULL COMMENT '取回密码的验证码',
  `integrate_id` int(11) NOT NULL COMMENT '会员整合的用户ID（如uc中的会员ID）',
  `sina_id` int(11) NOT NULL COMMENT '新浪同步的会员ID',
  `renren_id` int(11) NOT NULL COMMENT '预留',
  `kaixin_id` int(11) NOT NULL COMMENT '预留',
  `sohu_id` int(11) NOT NULL COMMENT '预留',
  `bind_verify` varchar(255) NOT NULL COMMENT '绑定验证码',
  `verify_create_time` int(11) NOT NULL COMMENT '绑定验证码发送时间',
  `tencent_id` varchar(255) NOT NULL COMMENT '腾讯微博ID',
  `referer` varchar(255) NOT NULL COMMENT '会员来路',
  `login_pay_time` int(11) NOT NULL COMMENT '弃用',
  `focus_count` int(11) NOT NULL COMMENT '关注别人的数量',
  `focused_count` int(11) NOT NULL COMMENT '粉丝数',
  `n_province_id` int(11) NOT NULL COMMENT '户籍-省',
  `n_city_id` int(11) NOT NULL COMMENT '户籍-市',
  `province_id` int(11) NOT NULL COMMENT '户口-省',
  `city_id` int(11) NOT NULL COMMENT '户口-市',
  `sex` tinyint(1) NOT NULL default '-1' COMMENT '性别 0女 1 男',
  `step` tinyint(1) NOT NULL COMMENT ' 新手已完成步骤',
  `byear` int(4) NOT NULL COMMENT '出生年',
  `bmonth` int(2) NOT NULL COMMENT '出生月',
  `bday` int(2) NOT NULL COMMENT '出生日',
  `graduation` varchar(15) NOT NULL COMMENT '学历',
  `graduatedyear` int(5) NOT NULL COMMENT '入学年份',
  `university` varchar(100) NOT NULL COMMENT '毕业院校',
  `edu_validcode` varchar(20) NOT NULL COMMENT '学历认证码',
  `has_send_video` tinyint(1) NOT NULL COMMENT '是否已经上传视频',
  `marriage` varchar(15) NOT NULL COMMENT '婚姻状况',
  `haschild` tinyint(1) NOT NULL COMMENT '有子女 0无 1有',
  `hashouse` tinyint(1) NOT NULL COMMENT '是否有房 0无 1有',
  `houseloan` tinyint(1) NOT NULL COMMENT '是否又房贷',
  `hascar` tinyint(1) NOT NULL COMMENT '是否有车',
  `carloan` tinyint(4) NOT NULL COMMENT '是否又车贷',
  `car_brand` varchar(50) NOT NULL COMMENT '汽车品牌',
  `car_year` int(4) NOT NULL COMMENT '购车时间',
  `car_number` varchar(50) NOT NULL COMMENT '汽车数量',
  `address` varchar(150) NOT NULL COMMENT '住址',
  `phone` varchar(50) NOT NULL COMMENT '电话',
  `postcode` varchar(20) NOT NULL COMMENT '邮编',
  `locate_time` int(11) NOT NULL default '0' COMMENT '用户最后登陆时间',
  `xpoint` float(10,6) NOT NULL default '0.000000' COMMENT '用户最后登陆x座标',
  `ypoint` float(10,6) NOT NULL default '0.000000' COMMENT '用户最后登陆y座标',
  `topic_count` int(11) NOT NULL COMMENT '主题数',
  `fav_count` int(11) NOT NULL COMMENT '喜欢数',
  `faved_count` int(11) NOT NULL COMMENT '被喜欢数',
  `insite_count` int(11) NOT NULL COMMENT '弃用',
  `outsite_count` int(11) NOT NULL COMMENT '弃用',
  `level_id` int(11) NOT NULL COMMENT '等级ID',
  `point` int(11) NOT NULL COMMENT '经验值',
  `sina_app_key` varchar(255) NOT NULL COMMENT '新浪的同步验证key',
  `sina_app_secret` varchar(255) NOT NULL COMMENT '新浪的同步验证密码',
  `is_syn_sina` tinyint(1) NOT NULL COMMENT '是否同步发微博到新浪',
  `tencent_app_key` varchar(255) NOT NULL COMMENT '腾讯的同步验证key',
  `tencent_app_secret` varchar(255) NOT NULL COMMENT '腾讯的同步验证密码',
  `is_syn_tencent` tinyint(1) NOT NULL COMMENT '是否同步发微博到腾讯',
  `t_access_token` varchar(250) NOT NULL COMMENT '腾讯微博授权码',
  `t_openkey` varchar(250) NOT NULL COMMENT '腾讯微博的openkey',
  `t_openid` varchar(250) NOT NULL COMMENT '腾讯微博OPENID',
  `sina_token` varchar(250) NOT NULL COMMENT '新浪的授权码',
  `is_borrow_out` tinyint(1) NOT NULL COMMENT '是否是投标者',
  `is_borrow_int` tinyint(1) NOT NULL COMMENT '是否融资人',
  `creditpassed` tinyint(1) NOT NULL COMMENT '信用认证是否通过',
  `creditpassed_time` int(11) NOT NULL COMMENT '信用认证通过时间',
  `workpassed` tinyint(1) NOT NULL COMMENT '工作认证是否通过',
  `workpassed_time` int(11) NOT NULL COMMENT '工作认证通过时间',
  `incomepassed` tinyint(1) NOT NULL COMMENT '收入认证是否通过',
  `incomepassed_time` int(11) NOT NULL COMMENT '收入认证通过时间',
  `housepassed` tinyint(1) NOT NULL COMMENT '房产认证是否通过',
  `housepassed_time` int(11) NOT NULL COMMENT '房产认证通过时间',
  `carpassed` tinyint(1) NOT NULL COMMENT '汽车认证是否通过',
  `carpassed_time` int(11) NOT NULL COMMENT '汽车认证通过时间',
  `marrypassed` tinyint(1) NOT NULL COMMENT '结婚认证是否通过',
  `marrypassed_time` int(11) NOT NULL COMMENT '结婚认证通过时间',
  `edupassed` tinyint(1) NOT NULL COMMENT '教育认证是否通过',
  `edupassed_time` int(11) NOT NULL COMMENT '教育认证通过时间',
  `skillpassed` tinyint(1) NOT NULL COMMENT '技术职称是否通过',
  `skillpassed_time` int(11) NOT NULL COMMENT '技术职称认证通过时间',
  `videopassed` tinyint(1) NOT NULL COMMENT '视频认证是否通',
  `videopassed_time` int(11) NOT NULL COMMENT '视频认证通过时间',
  `mobiletruepassed` tinyint(1) NOT NULL COMMENT '手机实名认证是否通过',
  `mobiletruepassed_time` int(11) NOT NULL COMMENT '手机实名认证认证通过时间',
  `residencepassed` tinyint(1) NOT NULL,
  `residencepassed_time` int(11) NOT NULL,
  `alipay_id` varchar(255) NOT NULL,
  `qq_id` varchar(255) NOT NULL,
  `info_down` varchar(255) NOT NULL COMMENT '资料下载地址',
  `sealpassed` tinyint(1) NOT NULL COMMENT '电子印章是否通过',
  `paypassword` varchar(50) NOT NULL COMMENT '支付密码',
  `apns_code` varchar(255) default NULL COMMENT '推送设备号',
  `emailpassed` tinyint(1) NOT NULL,
  `tmp_email` varchar(255) NOT NULL,
  `view_info` text NOT NULL,
  `ips_acct_no` varchar(30) default NULL COMMENT 'pIpsAcctNo 30 IPS托管平台账 户号',
  `referral_rate` decimal(10,2) NOT NULL COMMENT '返利抽成比',
  `user_type` tinyint(4) NOT NULL COMMENT '用户类型 0普通用户 1 企业用户',
  `create_date` date NOT NULL COMMENT '记录注册日期，方便统计使用',
  `register_ip` varchar(50) NOT NULL COMMENT '注册IP',
  `admin_id` int(11) NOT NULL COMMENT '所属管理员',
  `customer_id` int(11) NOT NULL COMMENT '所属客服',
  `is_black` tinyint(1) NOT NULL COMMENT '是否黑名单',
  `vip_id` int(11) NOT NULL COMMENT 'VIP等级id',
  `vip_state` tinyint(1) NOT NULL COMMENT 'VIP状态 0关闭 1开启',
  `nmc_amount` decimal(20,2) NOT NULL COMMENT '不可提现金额',
  `ips_mer_code` varchar(10) default NULL COMMENT '由IPS颁发的商户号 acct_type = 0',
  `enterpriseName` varchar(50) NOT NULL COMMENT '企业名称',
  `bankLicense` varchar(50) NOT NULL COMMENT '开户银行许可证',
  `orgNo` varchar(50) NOT NULL COMMENT '组织机构代码',
  `businessLicense` varchar(50) NOT NULL COMMENT '营业执照编号',
  `taxNo` varchar(20) NOT NULL COMMENT '税务登记号',
  `u_year` varchar(255) default NULL COMMENT '入学年份',
  `u_special` varchar(255) default NULL COMMENT '专业',
  `u_alipay` varchar(255) default NULL COMMENT '支付宝账号',
  PRIMARY KEY  (`id`),
  UNIQUE KEY `unk_user_name` (`user_name`),
  KEY `idx_u_001` USING BTREE (`create_date`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of fanwe_user
-- ----------------------------
INSERT INTO `%DB_PREFIX%user` VALUES ('1', 'fanwe', null, null, null, null, null, null, null, null, '6714ccb93be0fda4e51f206b91b46358', '1362094066', '1362094066', '127.0.0.1', '1', '1', '0', 'fanwe@fanwe.com', '1', '1', '1358551878', '1', '13696857677', '1', '3', '68900.00', '288893', '0.00', '', '', '0', '', '1407173232', '0', '', '0', '0', '0', '0', '0', '7648', '1356635011', '', '', '0', '3', '3', '4', '53', '4', '55', '1', '1', '1914', '5', '7', '大专', '2013', '', '2147483647', '1', '未婚', '0', '0', '0', '0', '0', '别克', '2010', '闽A.123123', '2222', '', '35001', '1330712776', '119.306938', '26.069746', '30', '1', '3', '3', '0', '7', '757', '', '', '0', '', '', '0', '', '', '', '', '1', '0', '1', '1358551878', '1', '1358551878', '1', '1359089534', '0', '0', '0', '0', '0', '0', '1', '127', '0', '0', '0', '0', '0', '0', '0', '0', '', '', '', '0', '', null, '0', '', '', null, '0.00', '0', '2013-03-01', '', '0', '0', '0', '0', '0', '0.00', null, '', '', '', '', '', null, null, null);
INSERT INTO `%DB_PREFIX%user` VALUES ('2', '担保机构参考', '担保机构参考', '深圳市中安信业创业投资有限公司是一家专门为个体工商户、小企业主和低收入家庭提供快速简便、无抵押无担保小额个人贷款服务的企业。公司自2004年开始探索无抵押无担保贷款， 至今累计放款全国最多，小额贷款服务的客户最多。在广东省（深圳市、佛山市），北京市，天津市，上海市，河北省，福建省，山东省，江苏省，湖南省，广西， 四川省，浙江省，河南省，湖北省，安徽省与辽宁省等五十多家便利的网点，逾千名员工专门从事小额贷款业务。中安信业是国内探索无抵押无担保商业化 可持续小额贷款最早的、累计放款量和贷款余额最多的、全国网点最多的、信贷质量最好的、运作最为规范的专业小额贷款机构。', '<a href=\"http://www.fanwe.cn/\" title=\"担保机构参考\" target=\"_blank\"><img src=\"http://www.renrendai.com/event/zaccn/logo.jpg\" width=\"179\" height=\"41\" alt=\"\" border=\"0\" /></a>', '深圳市中安信业创业投资有限公司（中安信业）成立于2003年10月，是一家专门协助银行金融机构为微小企业、个体户及中低收入人群提供快速简便、免抵押、免担保小额贷款服务的专业现代化小额信贷技术服务公司；累计协助银行发放贷款逾数十亿元，一直保持良好的资产质量，对合作机构的还款从未发生过一分钱的逾期；全国60个营业网点，覆盖了17个省，26个城市，专业的信贷从业人员逾1500人。', '<p><span>2003年</span> – 中安信业成立；</p>\r\n 				<p><span>2006年</span> – 深圳首家获政府批准的小额贷款试点企业；</p>\r\n 			 	<p><span>2008年</span> – 国际金融公司（IFC）入股中安信业，并派其全球小额贷款业务负责人参加中安信业的董事会；</p>\r\n			 	<p><span>2009年</span> – 首创 “贷款银行  + 代理机构” 的助贷模式，荣获深圳市市政府和国家开发银行的金融创新奖； </p>\r\n			 	<p><span>2010年 </span> – 深圳市小额贷款行业协会首届会长单位；</p>\r\n			 	<p><span>2011年</span> – 中国小额信贷机构联席会副会长单位； </p>\r\n			 	<p><span>2011年</span> – 中安信业携手国家开发银行、德国复兴信贷银行和国际金融公司共同创办了深圳龙岗国安村镇银行。</p>', '<div style=\"margin-bottom:20px;\">			<h3>高管团队</h3>\r\n			<p style=\"text-indent:20px;\"> 	中安信业拥有业内经验最丰富的管理团队，包括了多名在国内外银行业和小额贷款领域的资深高级管理人员。他们分别来自汇丰银行，大众银行，摩根士丹利，花旗银行，Procredit，工商银行，建设银行，深圳发展银行，安信信贷，平安集团，中国人民银行等。 </p>\r\n		</div>\r\n<img src=\"http://www.renrendai.com/event/zaccn/thinLine.jpg\" width=\"818\" height=\"6\" alt=\"\" border=\"0\" /><div>			<h3>经济效益和社会效益</h3>\r\n			<div id=\"xiaoyi\">				<ul>					<li>中安信业的标志代表着我们的两大目标：经济效益和社会效益，经济回报和社会回报，二者相辅相成。</li>\r\n					<li>通过协助银行金融机构为小微企业提供贷款，帮助其发展壮大，推动了经济发展，促进了就业和社会稳定。<br />\r\n						-累计至今，至少为超过10万名小微企业主提供了贷款，支持了超过几十万个就业机会。<br />\r\n						-为处于创业阶段的小企业提供了发展资金，免抵押，免担保，无需财务报表					</li>\r\n						<li>创造了贷款客户、银行机构和社会的多赢局面。</li>\r\n						</ul>\r\n			</div>\r\n			<img src=\"http://www.renrendai.com/event/zaccn/photo.jpg\" alt=\"\" border=\"0\" />		</div>', '0', '1', '', '0', '0', '', '0', '1', '0', '', '', '0', '0', '', '', '0', '0', '0.00', '0', '0.00', '', '', '0', '', '0', '0', '', '0', '0', '0', '0', '0', '', '0', '', '', '0', '0', '0', '0', '0', '0', '0', '-1', '0', '0', '0', '0', '', '0', '', '', '0', '', '0', '0', '0', '0', '0', '', '0', '', '', '', '', '0', '0.000000', '0.000000', '0', '0', '0', '0', '0', '0', '0', '', '', '0', '', '', '0', '', '', '', '', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '', '', '', '0', '', null, '0', '', '', null, '0.00', '2', '0000-00-00', '', '0', '0', '0', '0', '0', '0.00', null, '', '', '', '', '', null, null, null);

-- ----------------------------
-- Table structure for `%DB_PREFIX%user_active_log`
-- ----------------------------
DROP TABLE IF EXISTS `%DB_PREFIX%user_active_log`;
CREATE TABLE `%DB_PREFIX%user_active_log` (
  `id` int(11) NOT NULL auto_increment,
  `user_id` int(11) NOT NULL COMMENT '会员ID',
  `create_time` int(11) NOT NULL COMMENT '发生时间',
  `point` int(11) NOT NULL COMMENT '经验',
  `score` int(11) NOT NULL COMMENT '积分',
  `money` decimal(11,4) NOT NULL COMMENT '钱',
  PRIMARY KEY  (`id`),
  KEY `user_id` (`user_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of fanwe_user_active_log
-- ----------------------------

-- ----------------------------
-- Table structure for `%DB_PREFIX%user_address`
-- ----------------------------
DROP TABLE IF EXISTS `%DB_PREFIX%user_address`;
CREATE TABLE `%DB_PREFIX%user_address` (
  `id` int(11) NOT NULL auto_increment,
  `user_id` int(11) NOT NULL COMMENT '用户id',
  `name` varchar(50) NOT NULL COMMENT '收货人姓名',
  `address` varchar(255) NOT NULL COMMENT '用户地址',
  `phone` varchar(20) NOT NULL COMMENT '用户电话',
  `is_default` tinyint(1) NOT NULL default '0' COMMENT '是否默认地址',
  `provinces_cities` varchar(100) NOT NULL COMMENT '省市',
  `zip_code` varchar(20) NOT NULL COMMENT '邮编',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of fanwe_user_address
-- ----------------------------

-- ----------------------------
-- Table structure for `%DB_PREFIX%user_auth`
-- ----------------------------
DROP TABLE IF EXISTS `%DB_PREFIX%user_auth`;
CREATE TABLE `%DB_PREFIX%user_auth` (
  `id` int(11) NOT NULL auto_increment,
  `user_id` int(11) NOT NULL,
  `m_name` varchar(255) NOT NULL,
  `a_name` varchar(255) NOT NULL,
  `rel_id` int(11) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of fanwe_user_auth
-- ----------------------------

-- ----------------------------
-- Table structure for `%DB_PREFIX%user_autobid`
-- ----------------------------
DROP TABLE IF EXISTS `%DB_PREFIX%user_autobid`;
CREATE TABLE `%DB_PREFIX%user_autobid` (
  `user_id` int(11) NOT NULL,
  `fixed_amount` decimal(20,0) NOT NULL COMMENT '每次投标金额',
  `min_rate` decimal(20,0) NOT NULL COMMENT '最小利息',
  `max_rate` decimal(20,0) NOT NULL COMMENT '最大利息',
  `min_period` int(11) NOT NULL COMMENT '最低借款期限',
  `max_period` int(11) NOT NULL COMMENT '最高借款期限',
  `min_level` int(11) NOT NULL COMMENT '最低信用等级',
  `max_level` int(11) NOT NULL COMMENT '最高信用等级',
  `retain_amount` decimal(20,0) NOT NULL COMMENT '账户保留金额',
  `is_effect` tinyint(4) NOT NULL COMMENT '是否开启  0关闭 1开启',
  `last_bid_time` int(11) NOT NULL COMMENT '最后一次投标时间',
  `deal_cates` text NOT NULL,
  PRIMARY KEY  (`user_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of fanwe_user_autobid
-- ----------------------------
INSERT INTO `%DB_PREFIX%user_autobid` VALUES ('1', '1100', '15', '25', '3', '36', '5', '7', '0', '1', '1362944420', '');

-- ----------------------------
-- Table structure for `%DB_PREFIX%user_bank`
-- ----------------------------
DROP TABLE IF EXISTS `%DB_PREFIX%user_bank`;
CREATE TABLE `%DB_PREFIX%user_bank` (
  `id` int(11) NOT NULL auto_increment,
  `user_id` int(11) NOT NULL COMMENT '体现人（标识ID）',
  `bank_id` int(11) NOT NULL COMMENT '银行(标识ID)',
  `bankcard` varchar(30) NOT NULL COMMENT '卡号',
  `real_name` varchar(20) NOT NULL COMMENT '姓名',
  `region_lv1` int(11) NOT NULL,
  `region_lv2` int(11) NOT NULL,
  `region_lv3` int(11) NOT NULL,
  `region_lv4` int(11) NOT NULL,
  `bankzone` varchar(255) NOT NULL COMMENT '开户网点',
  PRIMARY KEY  (`id`),
  KEY `user_id` (`user_id`),
  KEY `bank_id` (`bank_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of fanwe_user_bank
-- ----------------------------

-- ----------------------------
-- Table structure for `%DB_PREFIX%user_carry`
-- ----------------------------
DROP TABLE IF EXISTS `%DB_PREFIX%user_carry`;
CREATE TABLE `%DB_PREFIX%user_carry` (
  `id` int(11) NOT NULL auto_increment,
  `user_id` int(11) NOT NULL COMMENT '提现人（标识ID）',
  `money` decimal(20,2) NOT NULL COMMENT '提现金额',
  `fee` decimal(20,2) NOT NULL COMMENT '手续费',
  `bank_id` int(11) NOT NULL COMMENT '银行ID',
  `bankcard` varchar(30) NOT NULL COMMENT '开好',
  `create_time` int(11) NOT NULL COMMENT '提交日期',
  `status` tinyint(1) NOT NULL COMMENT '0待审核，1已付款，2未通过，3待付款',
  `update_time` int(11) NOT NULL COMMENT '处理时间',
  `msg` text NOT NULL COMMENT '操作通知内容',
  `desc` text NOT NULL COMMENT '备注',
  `real_name` varchar(30) NOT NULL COMMENT '姓名',
  `region_lv1` int(11) NOT NULL COMMENT '国ID',
  `region_lv2` int(11) NOT NULL COMMENT '省ID',
  `region_lv3` int(11) NOT NULL COMMENT '市ID',
  `region_lv4` int(11) NOT NULL COMMENT '区ID',
  `create_date` date NOT NULL COMMENT '记录提现提交日期，方便统计使用',
  `bankzone` varchar(255) NOT NULL COMMENT '开户网点',
  PRIMARY KEY  (`id`),
  KEY `idx_uc_001` USING BTREE (`create_date`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of fanwe_user_carry
-- ----------------------------

-- ----------------------------
-- Table structure for `%DB_PREFIX%user_carry_config`
-- ----------------------------
DROP TABLE IF EXISTS `%DB_PREFIX%user_carry_config`;
CREATE TABLE `%DB_PREFIX%user_carry_config` (
  `id` int(11) NOT NULL auto_increment,
  `name` varchar(50) NOT NULL COMMENT '简称',
  `min_price` decimal(20,0) NOT NULL COMMENT '最低额度',
  `max_price` decimal(20,0) NOT NULL COMMENT '最高额度',
  `fee` decimal(20,2) NOT NULL COMMENT '费率',
  `fee_type` tinyint(1) NOT NULL COMMENT '费率类型 0 是固定值 1是百分比',
  `vip_id` int(11) NOT NULL COMMENT 'VIP等级     0默认配置  否则就是对应VIP等级设置',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of fanwe_user_carry_config
-- ----------------------------
INSERT INTO `%DB_PREFIX%user_carry_config` VALUES ('1', '2万以下', '0', '19999', '1.00', '0', '0');
INSERT INTO `%DB_PREFIX%user_carry_config` VALUES ('2', '2万元(含)-5万元', '20000', '49999', '3.00', '0', '0');
INSERT INTO `%DB_PREFIX%user_carry_config` VALUES ('3', '5万元(含)-100万元', '50000', '999999', '5.00', '0', '0');

-- ----------------------------
-- Table structure for `%DB_PREFIX%user_company`
-- ----------------------------
DROP TABLE IF EXISTS `%DB_PREFIX%user_company`;
CREATE TABLE `%DB_PREFIX%user_company` (
  `user_id` int(11) NOT NULL,
  `company_name` varchar(150) NOT NULL COMMENT '公司名称',
  `contact` varchar(50) NOT NULL default '' COMMENT '法人代表',
  `officetype` varchar(50) NOT NULL COMMENT '公司类别',
  `officedomain` varchar(50) NOT NULL COMMENT '公司行业',
  `officecale` varchar(50) NOT NULL COMMENT '公司规模',
  `register_capital` varchar(50) NOT NULL COMMENT '注册资金',
  `asset_value` varchar(100) NOT NULL COMMENT '资产净值',
  `officeaddress` varchar(255) NOT NULL COMMENT '公司地址',
  `description` text NOT NULL COMMENT '公司简介',
  `bankLicense` varchar(50) NOT NULL COMMENT '开户银行许可证',
  `orgNo` varchar(50) NOT NULL COMMENT '组织机构代码',
  `businessLicense` varchar(50) NOT NULL COMMENT '营业执照编号',
  `taxNo` varchar(20) NOT NULL COMMENT '税务登记号'
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='公司信息标';

-- ----------------------------
-- Records of fanwe_user_company
-- ----------------------------

-- ----------------------------
-- Table structure for `%DB_PREFIX%user_credit_file`
-- ----------------------------
DROP TABLE IF EXISTS `%DB_PREFIX%user_credit_file`;
CREATE TABLE `%DB_PREFIX%user_credit_file` (
  `id` int(11) NOT NULL auto_increment,
  `user_id` int(11) NOT NULL COMMENT '会员ID',
  `type` varchar(50) NOT NULL COMMENT '审核类型',
  `file` text NOT NULL COMMENT '序列化后的审核资料地址',
  `create_time` int(11) NOT NULL COMMENT '上传时间',
  `status` tinyint(1) NOT NULL COMMENT '0未处理，1已处理',
  `passed` tinyint(1) NOT NULL COMMENT '是否认证通过',
  `passed_time` int(1) NOT NULL COMMENT '认证日期',
  `msg` text NOT NULL COMMENT '失败原因',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=9 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of fanwe_user_credit_file
-- ----------------------------
INSERT INTO `%DB_PREFIX%user_credit_file` VALUES ('1', '1', 'credit_identificationscanning', 'a:1:{i:0;s:50:\"./public/attachment/201301/14/17/50f3ce845a18a.jpg\";}', '0', '1', '0', '0', '');
INSERT INTO `%DB_PREFIX%user_credit_file` VALUES ('2', '1', 'credit_house', 'a:1:{i:0;s:50:\"./public/attachment/201301/14/17/50f3ce845a18a.jpg\";}', '0', '1', '0', '0', '');
INSERT INTO `%DB_PREFIX%user_credit_file` VALUES ('3', '1', 'credit_contact', 'a:2:{i:0;s:50:\"./public/attachment/201301/15/09/50f4b1a92ccd5.jpg\";i:1;s:50:\"./public/attachment/201301/15/09/50f4b18e93f79.jpg\";}', '0', '1', '0', '0', '');
INSERT INTO `%DB_PREFIX%user_credit_file` VALUES ('4', '1', 'credit_incomeduty', 'a:1:{i:0;s:50:\"./public/attachment/201301/15/11/50f4cfbbb09f3.jpg\";}', '0', '1', '0', '0', '');
INSERT INTO `%DB_PREFIX%user_credit_file` VALUES ('5', '1', 'credit_credit', 'a:1:{i:0;s:50:\"./public/attachment/201301/15/11/50f4cfd10b869.jpg\";}', '0', '1', '0', '0', '');
INSERT INTO `%DB_PREFIX%user_credit_file` VALUES ('6', '1', 'credit_car', 'a:3:{i:0;s:50:\"./public/attachment/201301/15/14/50f4f3769c76c.jpg\";i:1;s:50:\"./public/attachment/201301/15/14/50f4f3769c76c.jpg\";i:2;s:50:\"./public/attachment/201301/15/14/50f4f3769c76c.jpg\";}', '0', '1', '0', '0', '');
INSERT INTO `%DB_PREFIX%user_credit_file` VALUES ('7', '1', 'credit_residence', 'a:2:{i:0;s:50:\"./public/attachment/201301/19/10/50fa070599761.jpg\";i:1;s:50:\"./public/attachment/201301/19/10/50fa0638d3575.jpg\";}', '1358534280', '1', '0', '0', '');
INSERT INTO `%DB_PREFIX%user_credit_file` VALUES ('8', '1', 'credit_mobilereceipt', 'a:1:{i:0;s:50:\"./public/attachment/201301/19/11/50fa192493730.jpg\";}', '1358538917', '1', '0', '0', '');

-- ----------------------------
-- Table structure for `%DB_PREFIX%user_credit_type`
-- ----------------------------
DROP TABLE IF EXISTS `%DB_PREFIX%user_credit_type`;
CREATE TABLE `%DB_PREFIX%user_credit_type` (
  `id` int(11) NOT NULL auto_increment,
  `type_name` varchar(255) NOT NULL COMMENT '类型名称',
  `type` varchar(100) NOT NULL COMMENT '审核类型',
  `icon` varchar(255) NOT NULL COMMENT '图标',
  `brief` text NOT NULL COMMENT '简介',
  `description` text NOT NULL COMMENT '认证说明',
  `role` varchar(255) NOT NULL COMMENT '认证条件',
  `file_tip` varchar(255) NOT NULL COMMENT '上传框说明',
  `file_count` int(11) NOT NULL,
  `expire` int(11) NOT NULL COMMENT '过期时间',
  `status` tinyint(1) NOT NULL COMMENT '0系统，1管理员新加',
  `is_effect` tinyint(1) NOT NULL COMMENT '0无效，1有效',
  `sort` int(11) NOT NULL COMMENT '排序',
  `point` int(11) NOT NULL COMMENT '信用积分',
  `must` tinyint(1) NOT NULL COMMENT '是否必须',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=14 DEFAULT CHARSET=utf8 COMMENT='会员认证审核资料';

-- ----------------------------
-- Records of fanwe_user_credit_type
-- ----------------------------
INSERT INTO `%DB_PREFIX%user_credit_type` VALUES ('1', '实名认证', 'credit_identificationscanning', './public/credit/1.jpg', '您上传的身份证扫描件需和您绑定的身份证一致，否则将无法通过认证。', '<div class=\"lh22\">\r\n	1、请您上传您<span class=\"f_red\">本人身份证原件</span>的照片。如果您持有第二代身份证，请上传正、反两面照片。\r\n</div>\r\n<div class=\"lh22\">\r\n	如果您持有第一代身份证，仅需上传正面照片。\r\n</div>\r\n<div class=\"lh22\">\r\n	2、请确认您上传的资料是清晰的、未经修改的数码照片（不可以是扫描图片）。\r\n</div>\r\n<div class=\"lh22\">\r\n	每张图片的尺寸<span class=\"f_red\">不大于1.5M</span>。\r\n</div>', '', '', '2', '0', '1', '1', '1', '10', '1');
INSERT INTO `%DB_PREFIX%user_credit_type` VALUES ('2', '工作认证', 'credit_contact', './public/credit/2.jpg', '您的工作状况是评估您信用状况的主要依据。请您填写真实可靠的工作信息。', '上传资料说明：<br />\r\n如果您满足以下 1种以上的身份，例如：您有稳定工作，且兼职开淘宝店。<br />\r\n我们建议您同时上传两份资料，这将有助于提高您的借款额度和信用等级 <br />\r\n<br />\r\n<table class=\"f12\" cellspacing=\"1\" style=\"background:#ccc;\">\r\n	<tbody>\r\n		<tr>\r\n			<td class=\"pl10 pr10 wb\">\r\n				<h4>\r\n					工薪阶层：\r\n				</h4>\r\n请上传以下<span class=\"f_red\">至少两项</span>资料的照片或扫描件：\r\n			</td>\r\n			<td class=\"pl10 pr10 wb\">\r\n				<div class=\"lh22\">\r\n					a) 劳动合同。\r\n				</div>\r\n				<div class=\"lh22\">\r\n					b) 加盖单位公章的在职证明。\r\n				</div>\r\n				<div class=\"lh22\">\r\n					c) 带有姓名及照片的工作证。\r\n				</div>\r\n			</td>\r\n		</tr>\r\n		<tr>\r\n			<td class=\"pl10 pr10 wb\">\r\n				<h4>\r\n					私营企业主:\r\n				</h4>\r\n请上传以下<span class=\"f_red\">全部三项</span>资料的照片或扫描件：\r\n			</td>\r\n			<td class=\"pl10 pr10 wb\">\r\n				<div class=\"lh22\">\r\n					a) 企业的营业执照。\r\n				</div>\r\n				<div class=\"lh22\">\r\n					b) 企业的税务登记证。\r\n				</div>\r\n				<div class=\"lh22\">\r\n					c) 店面照片（照片内需能看见营业执照）。\r\n				</div>\r\n			</td>\r\n		</tr>\r\n		<tr>\r\n			<td class=\"pl10 pr10 wb\">\r\n				<h4>\r\n					网商：\r\n				</h4>\r\n请上传以下资料的照片或扫描件：\r\n			</td>\r\n			<td class=\"pl10 pr10 wb\">\r\n				<div class=\"lh22\">\r\n					a) 请上传网店主页和后台的截屏(需要看清网址）。\r\n				</div>\r\n				<div class=\"lh22\">\r\n					b) 支付宝（或其他第三方支付工具）的至少3张最近3个月的商户版成功交易记录的截屏图片。\r\n				</div>\r\n				<div class=\"lh22\">\r\n					c) 营业执照（如果有的话提供，不是必须的）。\r\n				</div>\r\n				<div class=\"lh22\">\r\n					d) 备注：如果是淘宝专职卖家，店铺等级必须为3钻以上（含3钻）。\r\n				</div>\r\n			</td>\r\n		</tr>\r\n	</tbody>\r\n</table>', '工薪阶层需入职满6个月，私营企业主和淘宝商家需经营满一年', '', '4', '6', '1', '1', '2', '10', '1');
INSERT INTO `%DB_PREFIX%user_credit_type` VALUES ('3', '信用报告', 'credit_credit', './public/credit/3.jpg', '个人信用报告是由中国人民银行出具，全面记录个人信用活动，反映个人信用基本状况的文件。本报告是p2p信贷了解您信用状况的一个重要参考资料。 您信用报告内体现的信用记录，和信用卡额度等数据，将在您发布借款时经网站工作人员整理，在充分保护您隐私的前提下披露给借出者，作为借出者投标的依据。', '<div>\r\n	<div class=\"lh22\">\r\n		1、个人信用报告需<span class=\"f_red\">15日内</span>开具。\r\n	</div>\r\n	<div class=\"lh22\">\r\n		2、上传您的<span class=\"f_red\">个人信用报告原件</span>的照片，每页信用报告需独立照相，并将整份信用报告按页码先后顺序完整上传。 <br />\r\n<a href=\"#creditDiv\" id=\"creditGuy\" class=\"f_blue\">如何办理个人信用报告？</a> <br />\r\n<a href=\"http://www.pbccrc.org.cn/zxzx/lxfs/lxfs.shtml\" target=\"_blank\" class=\"f_blue\">全国各地征信中心联系方式查询</a> \r\n	</div>\r\n	<div class=\"lh22\">\r\n		3、请确认您上传的资料是清晰的、未经修改的数码照片或扫描图片。\r\n	</div>\r\n</div>', '', '上传央行信用报告', '2', '6', '1', '1', '3', '10', '1');
INSERT INTO `%DB_PREFIX%user_credit_type` VALUES ('4', '收入认证', 'credit_incomeduty', './public/credit/4.jpg', '您的银行流水单以及完税证明，是证明您收入情况的主要文件，也是评估您还款能力的主要依据之一。', '上传资料说明：<br />\r\n如果您满意以下 1种以上的身份，例如：您有稳定工作，且兼职开淘宝店。 <br />\r\n我们建议您同时上传两份资料，这将有助于提高您的借款额度和信用等级。 <br />\r\n<table class=\"f12\" cellspacing=\"1\" style=\"background:#ccc;\">\r\n	<tbody>\r\n		<tr>\r\n			<td class=\"pl10 pr10 wb\">\r\n				<h4>\r\n					工薪阶层：\r\n				</h4>\r\n请上传右侧<span class=\"f_red\">一项或多项</span>资料：\r\n			</td>\r\n			<td class=\"wb\">\r\n				<div class=\"lh22\" style=\"padding:0 10px;\">\r\n					a) 最近连续六个月工资卡银行流水单的照片或扫描件，须有银行盖章，或工资卡网银的电脑截屏。<br />\r\n<a href=\"#bankDiv\" id=\"bankGuy\" class=\"f_blue\">如何办理银行流水单？</a> \r\n				</div>\r\n				<div class=\"lh22\" style=\"padding:0 10px;\">\r\n					b) 最近连续六个月的个人所得税完税凭证。<br />\r\n<a href=\"#dutyDiv\" id=\"dutyGuy\" class=\"f_blue\">如何办理个人所得税完税证明？</a> \r\n				</div>\r\n				<div class=\"lh22\" style=\"padding:0 10px;\">\r\n					c) 社保卡正反面原件的照片以及最近连续六个月缴费记录。\r\n				</div>\r\n				<div class=\"lh22\" style=\"padding:0 10px;\">\r\n					d) 如果工资用现金形式发放，请提供近半年的常用银行储蓄账户流水单，须有银行盖章，或工资卡网银的电脑截屏。。\r\n				</div>\r\n			</td>\r\n		</tr>\r\n		<tr>\r\n			<td class=\"pl10 pr10 wb\">\r\n				<h4>\r\n					私营企业主:\r\n				</h4>\r\n请上传右侧<span class=\"f_red\">一项或多项</span>资料的照片或扫描件：\r\n			</td>\r\n			<td class=\"wb\">\r\n				<div class=\"lh22\" style=\"padding:0 10px;\">\r\n					a) 最近连续六个月个人银行卡流水单，须有银行盖章，或网银的电脑截屏。<br />\r\n<a href=\"#bankDiv\" id=\"bankGuy2\" class=\"f_blue\">如何办理银行流水单？</a> \r\n				</div>\r\n				<div class=\"lh22\" style=\"padding:0 10px;\">\r\n					b) 最近连续六个月企业银行流水单，须有银行盖章；或近半年企业的纳税证明。\r\n				</div>\r\n			</td>\r\n		</tr>\r\n		<tr>\r\n			<td class=\"pl10 pr10 wb\">\r\n				<h4>\r\n					网商：\r\n				</h4>\r\n请上传右侧<span class=\"f_red\">全部两项</span>资料的照片或扫描件：\r\n			</td>\r\n			<td class=\"wb\">\r\n				<div class=\"lh22\" style=\"padding:0 10px;\">\r\n					a) 最近连续六个月个人银行卡流水单，须有银行盖章，或网银的电脑截屏。<br />\r\n<a href=\"#bankDiv\" id=\"bankGuy2\" class=\"f_blue\">如何办理银行流水单？</a> \r\n				</div>\r\n				<div class=\"lh22\" style=\"padding:0 10px;\">\r\n					b) 如果是淘宝商家请上传近半年淘宝店支付宝账户明细的网银截图。\r\n				</div>\r\n			</td>\r\n		</tr>\r\n	</tbody>\r\n</table>', '收入需较稳定，私营企业主及淘宝商家月均流水需在20000以上', '上传完税证明', '6', '6', '1', '1', '4', '10', '1');
INSERT INTO `%DB_PREFIX%user_credit_type` VALUES ('5', '电子印章', 'credit_seal', './public/credit/6.jpg', '电子印章将会在借款协议那边使用。', '<div class=\"lh22\">\r\n                        	电子印章认证必须为<span class=\"f_red\">GIF</span>或者<span class=\"f_red\">PNG</span>的<span class=\"f_red\">背景透明</span>图片。\r\n                        </div>', '', '电子印章', '1', '0', '1', '1', '5', '2', '0');
INSERT INTO `%DB_PREFIX%user_credit_type` VALUES ('6', '房产认证', 'credit_house', './public/credit/15.jpg', '房产证明是证明借入者资产及还款能力的重要凭证,根据借款者提供的房产证明给与借入者一定的信用加分。', '1、 请上传以下任意一项或多项资料。\r\n<div class=\"pl15\">\r\n	<div class=\"lh22\">\r\n		a) <span class=\"f_red\">购房合同以及发票。</span> \r\n	</div>\r\n	<div class=\"lh22\">\r\n		b) <span class=\"f_red\">银行按揭贷款合同。</span> \r\n	</div>\r\n	<div class=\"lh22\">\r\n		c) <span class=\"f_red\">房产局产调单及收据。</span> \r\n	</div>\r\n</div>\r\n2、 请确认您上传的资料是清晰的、未经修改的数码照片或<span class=\"f_red\">彩色扫描</span>图片。 每张图片的尺寸<span class=\"f_red\">不大于3M</span>。', '必须是商品房', '上传房产证明', '4', '0', '1', '1', '6', '3', '0');
INSERT INTO `%DB_PREFIX%user_credit_type` VALUES ('7', '购车认证', 'credit_car', './public/credit/12.jpg', '购车证明是证明借入者资产及还款能力的重要凭证之一，根据借入者提供的购车证明给与借入者一定的信用加分。', '<div class=\"lh22\">\r\n	1、请上传您所购买<span class=\"f_red\">车辆行驶证</span>原件的照片。\r\n</div>\r\n<div class=\"lh22\">\r\n	2、请上传您和您购买车辆的<span class=\"f_red\">合影（照片须露出车牌号码）</span>。\r\n</div>\r\n<div class=\"lh22\">\r\n	3、请确认您上传的资料是清晰的、未经修改的数码照片或扫描图片。\r\n</div>', '', '上传汽车证明', '4', '0', '1', '1', '7', '3', '0');
INSERT INTO `%DB_PREFIX%user_credit_type` VALUES ('8', '学历认证', 'credit_graducation', './public/credit/10.jpg', '出者在选择借款列表投标时，借入者的学历也是一个重要的参考因素。为了让借出者更好、更快地相信您的学历是真实的，强烈建议您对学历进行在线验证。', '<div class=\"f14 f_red\">一、2001年至今获得学历，需学历证书编号</div>\r\n<div class=\"pl15\">\r\n<div class=\"lh22\">\r\n	1、点击 <a href=\"http://www.chsi.com.cn/xlcx/\" target=\"_blank\" class=\"f_blue\">网上学历查询</a>。\r\n</div>\r\n<div class=\"lh22\">\r\n	2、选择“零散查询”。\r\n</div>\r\n<div class=\"lh22\">\r\n	3、输入证书编号、查询码（通过手机短信获得，为12位学历查询码）、姓名、以及验证码进行查询。\r\n</div>\r\n<div class=\"lh22\">\r\n	4、查询成功后，您将查获得《教育部学历证书电子注册备案表》。\r\n</div>\r\n<div class=\"lh22\">\r\n	5、将该表<span class=\"f_red\">右下角的12位在线验证码</span><a href=\"./public/images/xueli_1.jpg\" target=\"_blank\" class=\"f_blue\">（见样本图01）</a>，输入下面的文本框。\r\n</div>\r\n<div class=\"lh22\">\r\n	6、点击提交审核。\r\n</div>\r\n</div>\r\n<div class=\"f14 f_red\">\r\n	二、1991年至今获得学历，无需学历证书编号\r\n</div>\r\n<div class=\"pl15\">\r\n<div class=\"lh22\">\r\n	1、点击 <a href=\"http://www.chsi.com.cn/xlcx/\" target=\"_blank\" class=\"f_blue\">网上学历查询</a>。\r\n</div>\r\n<div class=\"lh22\">\r\n	2、选择“本人查询”。\r\n</div>\r\n<div class=\"lh22\">\r\n	3、注册学信网账号。\r\n</div>\r\n<div class=\"lh22\">\r\n	4、登录学信网，点击“学历信息”。\r\n</div>\r\n<div class=\"lh22\">\r\n	5、选择您的最高学历，并点击“申请验证报告”（申请过程中，您需通过手机短信获得12位学历查询码，此查询码与{function name=\"app_conf\" v=\"SHOP_TITLE\"}所需验证码不同）。\r\n</div>\r\n<div class=\"lh22\">\r\n	6、申请成功后，您将获得<span class=\"f_red\">12位在线验证码</span><a href=\"./public/images/xueli_2.jpg\" target=\"_blank\" class=\"f_blue\">（见样本图02）</a> \r\n</div>\r\n<div class=\"lh22\">\r\n	7、将12位在线验证码输入下面的文本框\r\n</div>\r\n<div class=\"lh22\">\r\n	8、点击提交审核\r\n</div>\r\n</div>', '大专或以上学历（普通全日制）', '', '0', '0', '1', '1', '8', '10', '0');
INSERT INTO `%DB_PREFIX%user_credit_type` VALUES ('9', '技术职称认证', 'credit_titles', './public/credit/9.jpg', '技术职称是经专家评审、反映一个人专业技术水平并作为聘任专业技术职务依据的一种资格，不与工资挂钩，是考核借款人信用的评估因素之一，通过技术职称认证证明，您将获得一定的信用加分。', '<div class=\"lh22\">\r\n	1、请上传您的技术职称证书原件照片。\r\n</div>\r\n<div class=\"lh22\">\r\n	2、 请确认您上传的资料是清晰的、未经修改的数码照片或扫描图片。 每张图片的尺寸<span class=\"f_red\">不大于1.5M</span>。\r\n</div>', '国家承认的二级及以上等级证书。例如律师证、会计证、工程师证等', '上传技术职称认证', '2', '0', '1', '1', '9', '2', '0');
INSERT INTO `%DB_PREFIX%user_credit_type` VALUES ('10', '视频认证', 'credit_videoauth', './public/credit/8.jpg', '什么是视频认证？只有通过视频认证您才能获得贷款，您只需要在视频认证页面上传您本人的视频，并提交，即可申请视频认证。您也可以选择与p2p信贷客服在线上进行视频认证。', '<div class=\"lh22\">\r\n	1、视频录制要求：\r\n	<div>\r\n		（1）视频认证文件大小<span class=\"f_red\">不得超过50M</span><br />\r\n（2）	请上传真实有效的本人的视频<br />\r\n（3）	视频文件格式可以是RMVB、WMV、mp4 、 AVI等类型的文件<br />\r\n（4）	视频认证必须图像清晰，声音清楚<br />\r\n（5）	视频认证必须衣冠整洁，禁止出现抽烟，赤裸等形象\r\n	</div>\r\n</div>\r\n<div class=\"lh22\">\r\n	2、视频录制内容。请针对本次借款录制视频，视频中需包括以下内容：\r\n	<div>\r\n		<span class=\"b\">（1）：首先，请朗读以下文字：</span>我是 ***，我在{function name=\"app_conf\" v=\"SHOP_TITLE\"}的用户名是***，我的身份证号是 ***********************，现在我正在做{function name=\"app_conf\" v=\"SHOP_TITLE\"}的视频确认。我在此做出以下承诺：我愿意接受{function name=\"app_conf\" v=\"SHOP_TITLE\"}的使用条款和借款协议；我提供给{function name=\"app_conf\" v=\"SHOP_TITLE\"}的信息及资料均是真实有效的；我愿意对我在{function name=\"app_conf\" v=\"SHOP_TITLE\"}上的行为承担全部法律责任；在我未能按时归还借款时，我同意{function name=\"app_conf\" v=\"SHOP_TITLE\"}采取法律诉讼、资料曝光等一切必要措施。\r\n	</div>\r\n	<div>\r\n		<span class=\"b\">（2）：读完声明后，请您将身份证正面(有身份证号)对准摄像头，并保持5秒，需要保证画面中能同时看到您和您的身份证，并且身份证内容清晰可见。</span>\r\n	</div>\r\n</div>\r\n<div class=\"lh22\">\r\n	3、视频提交办法：您可以选择下列方法之一进行视频认证的提交：\r\n	<div>\r\n		（1）您可以联系右侧的在线QQ客服进行视频文件的提交。\r\n	</div>\r\n	<div>\r\n		（2）您可以将视频文件发送至<a name=\"app_conf\"></a>{function name=\"app_conf\" v=\"REPLY_ADDRESS\"}，请在邮件中注明您的{function name=\"app_conf\" v=\"SHOP_TITLE\"}用户名及真实姓名。\r\n	</div>\r\n	<div>\r\n		（3）当您通过上述两种方式之一提交过视频认证文件之后，请选择下面的选项并点击“提交审核”。\r\n	</div>\r\n</div>', '', '', '0', '0', '1', '1', '10', '2', '0');
INSERT INTO `%DB_PREFIX%user_credit_type` VALUES ('11', '手机实名认证', 'credit_mobilereceipt', './public/credit/7.jpg', '手机流水单是最近一段时间内的详细通话记录，是验证借入者真实性的重要凭证之一。您的手机详单不会以任何形式被泄露。', '<div class=\"div22\">\r\n	1、请您上传您绑定的手机号码<span class=\"f_red\">最近3个月的手机详单</span>原件的照片。如详单数量较多可分月打印并上传\r\n</div>\r\n<div class=\"lh22\">\r\n	<span class=\"f_red\">每月前5日部分</span>（每月详单均需清晰显示机主手机号码）。\r\n</div>\r\n<div class=\"lh22\">\r\n	2、请确认您上传的资料是清晰的、未经修改的数码照片（不可以是扫描图片）。\r\n</div>\r\n<div class=\"lh22\">\r\n	每张图片的尺寸<span class=\"f_red\">不大于1.5M</span>。\r\n</div>', '', '上传手机流水单', '4', '0', '1', '1', '11', '10', '0');
INSERT INTO `%DB_PREFIX%user_credit_type` VALUES ('12', '居住地认证', 'credit_residence', './public/credit/5.jpg', '居住地的稳定性，是考核借款人的主要评估因素之一，通过居住地证明，您将获得一定的信用加分。', '<div class=\"lh22\">\r\n	1、请上传以下任何一项可证明<span class=\"f_red\">现居住地址</span>的证明文件原件的照片。\r\n</div>\r\n<div class=\"lh22\">\r\n	1) 用您姓名登记的水、电、气最近三期缴费单；\r\n</div>\r\n<div class=\"lh22\">\r\n	2) 用您姓名登记固定电话最近三期缴费单；\r\n</div>\r\n<div class=\"lh22\">\r\n	3) 您的信用卡最近两期的月结单；\r\n</div>\r\n<div class=\"lh22\">\r\n	4) 您的自有房产证明；\r\n</div>\r\n<div class=\"lh22\">\r\n	5) 您父母的房产证明，及证明您和父母关系的证明材料。\r\n</div>\r\n<div class=\"lh22\">\r\n	2、请确认您上传的资料是清晰的、未经修改的数码照片（不可以是扫描图片）。\r\n</div>\r\n<div class=\"lh22\">\r\n	每张图片的尺寸<span class=\"f_red\">不大于1.5M</span>。\r\n</div>', '', '上传居住地证明', '4', '6', '1', '1', '12', '2', '0');
INSERT INTO `%DB_PREFIX%user_credit_type` VALUES ('13', '结婚认证', 'credit_marriage', './public/credit/11.jpg', '借入者的婚姻状况的稳定性，是考核借款人信用的评估因素之一，通过结婚认证，您将获得一定的信用加分。', '<div class=\"lh22\">\r\n	1、请您上传以下资料\r\n</div>\r\n<div class=\"lh22\">\r\n	1) 您<span class=\"f_red\">结婚证书</span>原件的照片\r\n</div>\r\n<div class=\"lh22\">\r\n	2) 您配偶的身份证原件的照片。如果持有第二代身份证，请上传正反两面\r\n</div>\r\n<div class=\"lh22\">\r\n	照片。如果持有第一代身份证，仅需上传正面照片。\r\n</div>\r\n<div class=\"lh22\">\r\n	3) 您和配偶的<span class=\"f_red\">近照合影</span>一张\r\n</div>\r\n<div class=\"lh22\">\r\n	2、请确认您上传的资料是清晰的、未经修改的数码照片或扫描图片。\r\n</div>', '您的配偶同意您将其个人资料提供给本站', '上传结婚证书', '4', '0', '1', '1', '13', '2', '0');

-- ----------------------------
-- Table structure for `%DB_PREFIX%user_extend`
-- ----------------------------
DROP TABLE IF EXISTS `%DB_PREFIX%user_extend`;
CREATE TABLE `%DB_PREFIX%user_extend` (
  `id` int(11) NOT NULL auto_increment,
  `field_id` int(11) NOT NULL COMMENT '扩展字段ID',
  `user_id` int(11) NOT NULL COMMENT '会员ID',
  `value` varchar(255) NOT NULL COMMENT '值',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=88 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of fanwe_user_extend
-- ----------------------------
INSERT INTO `%DB_PREFIX%user_extend` VALUES ('85', '1', '1', '大专');
INSERT INTO `%DB_PREFIX%user_extend` VALUES ('86', '2', '1', '2004');
INSERT INTO `%DB_PREFIX%user_extend` VALUES ('87', '3', '1', '');

-- ----------------------------
-- Table structure for `%DB_PREFIX%user_field`
-- ----------------------------
DROP TABLE IF EXISTS `%DB_PREFIX%user_field`;
CREATE TABLE `%DB_PREFIX%user_field` (
  `id` int(11) NOT NULL auto_increment,
  `field_name` varchar(255) NOT NULL COMMENT ' 字段名（代码）',
  `field_show_name` varchar(255) NOT NULL COMMENT '字段显示用的名称',
  `input_type` tinyint(1) NOT NULL COMMENT '0:手动输入 1：预选下拉',
  `value_scope` text NOT NULL COMMENT '预选下拉的预选值,以逗号分隔',
  `is_must` tinyint(1) NOT NULL COMMENT '是否必填',
  `sort` int(11) NOT NULL COMMENT '排序大到小',
  PRIMARY KEY  (`id`),
  UNIQUE KEY `unk_field_name` (`field_name`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of fanwe_user_field
-- ----------------------------
INSERT INTO `%DB_PREFIX%user_field` VALUES ('1', 'weibo', '腾讯微博', '0', '', '0', '1');

-- ----------------------------
-- Table structure for `%DB_PREFIX%user_focus`
-- ----------------------------
DROP TABLE IF EXISTS `%DB_PREFIX%user_focus`;
CREATE TABLE `%DB_PREFIX%user_focus` (
  `focus_user_id` int(11) NOT NULL COMMENT '关注人ID',
  `focused_user_id` int(11) NOT NULL COMMENT '被关注人ID',
  `focus_user_name` varchar(255) NOT NULL COMMENT '关注人用户名',
  `focused_user_name` varchar(255) NOT NULL COMMENT '被关注人用户名',
  PRIMARY KEY  (`focus_user_id`,`focused_user_id`),
  KEY `focus_user_id` (`focus_user_id`),
  KEY `focused_user_id` (`focused_user_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of fanwe_user_focus
-- ----------------------------

-- ----------------------------
-- Table structure for `%DB_PREFIX%user_frequented`
-- ----------------------------
DROP TABLE IF EXISTS `%DB_PREFIX%user_frequented`;
CREATE TABLE `%DB_PREFIX%user_frequented` (
  `id` int(11) NOT NULL auto_increment,
  `uid` int(11) default '0' COMMENT '员会ID',
  `title` varchar(50) default NULL,
  `addr` varchar(255) default NULL,
  `xpoint` float(12,6) default '0.000000' COMMENT 'longitude',
  `ypoint` float(12,6) default '0.000000' COMMENT 'latitude',
  `latitude_top` float(12,6) default NULL,
  `latitude_bottom` float(12,6) default NULL,
  `longitude_left` float(12,6) default NULL,
  `longitude_right` float(12,6) default NULL,
  `zoom_level` int(5) default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of fanwe_user_frequented
-- ----------------------------

-- ----------------------------
-- Table structure for `%DB_PREFIX%user_group`
-- ----------------------------
DROP TABLE IF EXISTS `%DB_PREFIX%user_group`;
CREATE TABLE `%DB_PREFIX%user_group` (
  `id` int(11) NOT NULL auto_increment,
  `name` varchar(255) NOT NULL COMMENT '组名',
  `score` int(11) NOT NULL COMMENT '所需积分',
  `discount` decimal(20,2) NOT NULL COMMENT '折扣',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of fanwe_user_group
-- ----------------------------
INSERT INTO `%DB_PREFIX%user_group` VALUES ('1', '普通会员', '0', '1.00');

-- ----------------------------
-- Table structure for `%DB_PREFIX%user_level`
-- ----------------------------
DROP TABLE IF EXISTS `%DB_PREFIX%user_level`;
CREATE TABLE `%DB_PREFIX%user_level` (
  `id` int(11) NOT NULL auto_increment,
  `name` varchar(255) NOT NULL COMMENT '等级名称',
  `point` int(11) NOT NULL COMMENT '所需经验',
  `services_fee` varchar(20) NOT NULL COMMENT '服务费率',
  `enddate` varchar(255) NOT NULL COMMENT '贷款时间',
  `repaytime` text NOT NULL COMMENT '借款期限和借款利率【一行一配置】',
  PRIMARY KEY  (`id`),
  UNIQUE KEY `unk` (`point`)
) ENGINE=MyISAM AUTO_INCREMENT=8 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of fanwe_user_level
-- ----------------------------
INSERT INTO `%DB_PREFIX%user_level` VALUES ('1', 'HR', '0', '5', '7', '3|1|10|24\r\n6|1|11|24\r\n9|1|12|24\r\n12|1|15|24\r\n18|1|15|24\r\n24|1|15|24');
INSERT INTO `%DB_PREFIX%user_level` VALUES ('2', 'E', '100', '3', '7', '3|1|10|24\r\n6|1|11|24\r\n9|1|12|24\r\n12|1|15|24\r\n18|1|15|24\r\n24|1|15|24');
INSERT INTO `%DB_PREFIX%user_level` VALUES ('3', 'D', '110', '2.5', '7', '3|1|10|24\r\n6|1|11|24\r\n9|1|12|24\r\n12|1|15|24\r\n18|1|15|24\r\n24|1|15|24');
INSERT INTO `%DB_PREFIX%user_level` VALUES ('4', 'C', '120', '2', '7', '3|1|10|24\r\n6|1|11|24\r\n9|1|12|24\r\n12|1|15|24\r\n18|1|15|24\r\n24|1|15|24');
INSERT INTO `%DB_PREFIX%user_level` VALUES ('5', 'B', '130', '1.5', '7', '3|1|10|24\r\n6|1|11|24\r\n9|1|12|24\r\n12|1|15|24\r\n18|1|15|24\r\n24|1|15|24');
INSERT INTO `%DB_PREFIX%user_level` VALUES ('6', 'A', '145', '1', '7', '3|1|10|24\r\n6|1|11|24\r\n9|1|12|24\r\n12|1|15|24\r\n18|1|15|24\r\n24|1|15|24');
INSERT INTO `%DB_PREFIX%user_level` VALUES ('7', 'AA', '160', '0', '7', '3|1|10|24\r\n6|1|11|24\r\n9|1|12|24\r\n12|1|15|24\r\n18|1|15|24\r\n24|1|15|24');

-- ----------------------------
-- Table structure for `%DB_PREFIX%user_lock_money_log`
-- ----------------------------
DROP TABLE IF EXISTS `%DB_PREFIX%user_lock_money_log`;
CREATE TABLE `%DB_PREFIX%user_lock_money_log` (
  `id` int(11) NOT NULL auto_increment,
  `user_id` int(11) NOT NULL COMMENT '关联用户',
  `lock_money` decimal(20,2) NOT NULL COMMENT '操作金额',
  `account_lock_money` decimal(20,2) NOT NULL COMMENT '当前账户余额',
  `memo` text NOT NULL COMMENT '操作备注',
  `type` tinyint(2) NOT NULL COMMENT '0结存，1充值，2投标成功，8申请提现，9提现手续费，10借款管理费，18开户奖励，19流标还返',
  `create_time` int(11) NOT NULL COMMENT '操作时间',
  `create_time_ymd` date NOT NULL COMMENT '操作时间 ymd',
  `create_time_ym` int(6) NOT NULL COMMENT '操作时间 ym',
  `create_time_y` int(4) NOT NULL COMMENT '操作时间 y',
  PRIMARY KEY  (`id`),
  KEY `idx0` (`user_id`,`type`,`create_time`),
  KEY `idx1` (`user_id`,`type`,`create_time_ymd`),
  KEY `idx2` (`user_id`,`type`),
  KEY `create_time_ymd` (`create_time_ymd`),
  KEY `idx3` (`type`,`create_time`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COMMENT='会员资金日志表';

-- ----------------------------
-- Records of fanwe_user_lock_money_log
-- ----------------------------
INSERT INTO `%DB_PREFIX%user_lock_money_log` VALUES ('1', '1', '0.00', '0.00', '升级3.2', '0', '1420826398', '2015-01-10', '201501', '2015');

-- ----------------------------
-- Table structure for `%DB_PREFIX%user_log`
-- ----------------------------
DROP TABLE IF EXISTS `%DB_PREFIX%user_log`;
CREATE TABLE `%DB_PREFIX%user_log` (
  `id` int(11) NOT NULL auto_increment,
  `log_info` text NOT NULL COMMENT '日志内容',
  `log_time` int(11) NOT NULL COMMENT '发生时间',
  `log_admin_id` int(11) NOT NULL COMMENT '操作管理员的ID',
  `log_user_id` int(11) NOT NULL COMMENT '操作的前台会员ID',
  `money` decimal(20,2) NOT NULL COMMENT '相关的钱',
  `score` int(11) NOT NULL COMMENT '相关的积分',
  `point` int(11) NOT NULL COMMENT '相关的经验',
  `quota` decimal(20,2) NOT NULL COMMENT '相关的额度',
  `lock_money` decimal(20,2) NOT NULL COMMENT '相关的冻结资金',
  `user_id` int(11) NOT NULL COMMENT '会员ID',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of fanwe_user_log
-- ----------------------------

-- ----------------------------
-- Table structure for `%DB_PREFIX%user_medal`
-- ----------------------------
DROP TABLE IF EXISTS `%DB_PREFIX%user_medal`;
CREATE TABLE `%DB_PREFIX%user_medal` (
  `id` int(11) NOT NULL auto_increment,
  `user_id` int(11) NOT NULL,
  `medal_id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `create_time` int(11) NOT NULL,
  `is_delete` tinyint(1) NOT NULL,
  `icon` varchar(255) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of fanwe_user_medal
-- ----------------------------

-- ----------------------------
-- Table structure for `%DB_PREFIX%user_money_log`
-- ----------------------------
DROP TABLE IF EXISTS `%DB_PREFIX%user_money_log`;
CREATE TABLE `%DB_PREFIX%user_money_log` (
  `id` int(11) NOT NULL auto_increment,
  `user_id` int(11) NOT NULL COMMENT '关联用户',
  `money` decimal(20,2) NOT NULL COMMENT '操作金额',
  `account_money` decimal(20,2) NOT NULL COMMENT '当前账户余额',
  `memo` text NOT NULL COMMENT '操作备注',
  `type` tinyint(2) NOT NULL COMMENT '0结存，1充值，2投标成功，3招标成功，4偿还本息，5回收本息，6提前还款，7提前回收，8申请提现，9提现手续费，10借款管理费，11逾期罚息，12逾期管理费，13人工充值，14借款服务费，15出售债权，16购买债权，17债权转让管理费，18开户奖励，19流标还返，20投标管理费，21投标逾期收入，22兑换，23邀请返利，24投标返利，25签到成功，26逾期罚金（垫付后），27其他费用',
  `create_time` int(11) NOT NULL COMMENT '操作时间',
  `create_time_ymd` date NOT NULL COMMENT '操作时间 ymd',
  `create_time_ym` int(6) NOT NULL COMMENT '操作时间 ym',
  `create_time_y` int(4) NOT NULL COMMENT '操作时间 y',
  PRIMARY KEY  (`id`),
  KEY `idx0` (`user_id`,`type`,`create_time`),
  KEY `idx1` (`user_id`,`type`,`create_time_ymd`),
  KEY `idx2` (`user_id`,`type`),
  KEY `create_time_ymd` (`create_time_ymd`),
  KEY `idx3` (`type`,`create_time`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COMMENT='会员资金日志表';

-- ----------------------------
-- Records of fanwe_user_money_log
-- ----------------------------
INSERT INTO `%DB_PREFIX%user_money_log` VALUES ('1', '1', '0.00', '68900.00', '升级3.2', '0', '1420826397', '2015-01-10', '201501', '2015');

-- ----------------------------
-- Table structure for `%DB_PREFIX%user_point_log`
-- ----------------------------
DROP TABLE IF EXISTS `%DB_PREFIX%user_point_log`;
CREATE TABLE `%DB_PREFIX%user_point_log` (
  `id` int(11) NOT NULL auto_increment,
  `user_id` int(11) NOT NULL COMMENT '关联用户',
  `point` decimal(20,2) NOT NULL COMMENT '操作信用积分',
  `account_point` decimal(20,2) NOT NULL COMMENT '当前账户信用积分',
  `memo` text NOT NULL COMMENT '操作备注',
  `type` tinyint(2) NOT NULL COMMENT '0结存，4偿还本息，5回收本息，6提前还款，7提前回收，8申请认证，11逾期还款，13人工充值，14借款服务费，18开户奖励，22兑换，23邀请返利，24投标返利，25签到成功',
  `create_time` int(11) NOT NULL COMMENT '操作时间',
  `create_time_ymd` date NOT NULL COMMENT '操作时间 ymd',
  `create_time_ym` int(6) NOT NULL COMMENT '操作时间 ym',
  `create_time_y` int(4) NOT NULL COMMENT '操作时间 y',
  PRIMARY KEY  (`id`),
  KEY `idx0` (`user_id`,`type`,`create_time`),
  KEY `idx1` (`user_id`,`type`,`create_time_ymd`),
  KEY `idx2` (`user_id`,`type`),
  KEY `create_time_ymd` (`create_time_ymd`),
  KEY `idx3` (`type`,`create_time`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COMMENT='会员信用积分日志表';

-- ----------------------------
-- Records of fanwe_user_point_log
-- ----------------------------
INSERT INTO `%DB_PREFIX%user_point_log` VALUES ('1', '1', '0.00', '757.00', '升级3.2', '0', '1420826397', '2015-01-10', '201501', '2015');

-- ----------------------------
-- Table structure for `%DB_PREFIX%user_score_log`
-- ----------------------------
DROP TABLE IF EXISTS `%DB_PREFIX%user_score_log`;
CREATE TABLE `%DB_PREFIX%user_score_log` (
  `id` int(11) NOT NULL auto_increment,
  `user_id` int(11) NOT NULL COMMENT '关联用户',
  `score` decimal(20,2) NOT NULL COMMENT '操作积分',
  `account_score` decimal(20,2) NOT NULL COMMENT '当前账户剩余积分',
  `memo` varchar(255) NOT NULL COMMENT '操作备注',
  `type` tinyint(2) NOT NULL COMMENT '0结存，1充值，2投标成功，3招标成功，4偿还本息，5回收本息，6提前还款，13人工充值，14借款服务费，15出售债权，16购买债权，17债权转让管理费，18开户奖励，19流标还返，20投标管理费，21投标逾期收入，22兑换，23邀请返利，24投标返利，25签到成功 ',
  `create_time` int(11) NOT NULL COMMENT '操作时间',
  `create_time_ymd` date NOT NULL COMMENT '操作时间 ymd',
  `create_time_ym` int(11) NOT NULL COMMENT '操作时间 ym',
  `create_time_y` int(11) NOT NULL COMMENT '操作时间 y',
  PRIMARY KEY  (`id`),
  KEY `idx0` (`user_id`,`type`,`create_time`),
  KEY `idx1` (`user_id`,`type`,`create_time_ymd`),
  KEY `idx2` (`user_id`,`type`),
  KEY `create_time_ymd` (`create_time_ymd`),
  KEY `idx3` (`type`,`create_time`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of fanwe_user_score_log
-- ----------------------------

-- ----------------------------
-- Table structure for `%DB_PREFIX%user_sign_log`
-- ----------------------------
DROP TABLE IF EXISTS `%DB_PREFIX%user_sign_log`;
CREATE TABLE `%DB_PREFIX%user_sign_log` (
  `id` int(11) NOT NULL auto_increment,
  `user_id` int(11) NOT NULL,
  `sign_date` int(11) NOT NULL,
  PRIMARY KEY  (`id`),
  KEY `user_id` (`user_id`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of fanwe_user_sign_log
-- ----------------------------

-- ----------------------------
-- Table structure for `%DB_PREFIX%user_sta`
-- ----------------------------
DROP TABLE IF EXISTS `%DB_PREFIX%user_sta`;
CREATE TABLE `%DB_PREFIX%user_sta` (
  `user_id` int(11) NOT NULL,
  `dp_count` int(11) NOT NULL COMMENT '留言数',
  `borrow_amount` decimal(20,2) NOT NULL COMMENT '总的借款数',
  `repay_amount` decimal(20,2) NOT NULL COMMENT '已还本息',
  `need_repay_amount` decimal(20,2) NOT NULL COMMENT '待还本息',
  `need_manage_amount` decimal(20,2) NOT NULL COMMENT '待还管理费',
  `avg_rate` float(10,2) NOT NULL COMMENT '平均借款利率',
  `avg_borrow_amount` decimal(20,2) NOT NULL COMMENT '平均每笔借款金额',
  `deal_count` int(11) NOT NULL COMMENT '总借入笔数',
  `success_deal_count` int(11) NOT NULL COMMENT '成功借款',
  `repay_deal_count` int(11) NOT NULL COMMENT '还清笔数',
  `tq_repay_deal_count` int(11) NOT NULL COMMENT '提前还清',
  `zc_repay_deal_count` int(11) NOT NULL COMMENT '正常还清',
  `wh_repay_deal_count` int(11) NOT NULL COMMENT '未还清',
  `yuqi_count` int(11) NOT NULL COMMENT '逾期次数',
  `yz_yuqi_count` int(11) NOT NULL COMMENT '严重逾期次数',
  `yuqi_amount` decimal(20,2) NOT NULL COMMENT '逾期本息',
  `yuqi_impose` decimal(20,2) NOT NULL COMMENT '逾期费用',
  `load_earnings` decimal(20,2) NOT NULL COMMENT '已赚利息',
  `load_tq_impose` decimal(20,2) NOT NULL COMMENT '提前还款违约金',
  `load_yq_impose` decimal(20,2) NOT NULL COMMENT '逾期还款违约金',
  `load_avg_rate` float(10,2) NOT NULL COMMENT '借出加权平均收益率',
  `load_count` int(11) NOT NULL COMMENT '总借出笔数',
  `load_money` decimal(20,2) NOT NULL COMMENT '总的借出金额',
  `load_repay_money` decimal(20,2) NOT NULL COMMENT '已回收本息',
  `load_wait_repay_money` decimal(20,2) default NULL COMMENT '待回收本息',
  `reback_load_count` int(11) NOT NULL COMMENT '收回的借出笔数',
  `wait_reback_load_count` int(11) NOT NULL COMMENT '未收回的借出笔数',
  `load_wait_earnings` decimal(20,2) NOT NULL COMMENT '待回收利息',
  `bad_count` int(11) NOT NULL COMMENT '坏账数量',
  `rebate_money` int(11) NOT NULL COMMENT '返利金额',
  PRIMARY KEY  (`user_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of fanwe_user_sta
-- ----------------------------
INSERT INTO `%DB_PREFIX%user_sta` VALUES ('1', '0', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0', '0', '0', '0', '0', '0', '0', '0', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0', '0.00', '0.00', '0.00', '0', '0', '0.00', '0', '0');

-- ----------------------------
-- Table structure for `%DB_PREFIX%user_work`
-- ----------------------------
DROP TABLE IF EXISTS `%DB_PREFIX%user_work`;
CREATE TABLE `%DB_PREFIX%user_work` (
  `user_id` int(11) NOT NULL,
  `office` varchar(100) NOT NULL COMMENT '单位名称',
  `jobtype` varchar(50) NOT NULL COMMENT '职业状态',
  `province_id` int(11) NOT NULL COMMENT '工作城市-省',
  `city_id` int(11) NOT NULL COMMENT '工作城市-市',
  `officetype` varchar(50) NOT NULL COMMENT '公司类别',
  `officedomain` varchar(50) NOT NULL COMMENT '公司行业',
  `officecale` varchar(50) NOT NULL COMMENT '公司规模',
  `position` varchar(50) NOT NULL COMMENT '职位',
  `salary` varchar(50) NOT NULL COMMENT '月收入',
  `workyears` varchar(50) NOT NULL COMMENT '在现单位工作年限',
  `workphone` varchar(20) NOT NULL COMMENT '公司电话',
  `workemail` varchar(50) NOT NULL COMMENT '公司邮箱',
  `officeaddress` varchar(100) NOT NULL COMMENT '公司地址',
  `urgentcontact` varchar(20) NOT NULL COMMENT '直系联系人-姓名',
  `urgentrelation` varchar(20) NOT NULL COMMENT '直系联系人-关系',
  `urgentmobile` varchar(20) NOT NULL COMMENT '直系联系人-电话',
  `urgentcontact2` varchar(20) NOT NULL COMMENT '其他联系人-姓名',
  `urgentrelation2` varchar(20) NOT NULL COMMENT '其他联系人-关系',
  `urgentmobile2` varchar(20) NOT NULL COMMENT '其他联系人-电话',
  PRIMARY KEY  (`user_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of fanwe_user_work
-- ----------------------------

-- ----------------------------
-- Table structure for `%DB_PREFIX%user_x_y_point`
-- ----------------------------
DROP TABLE IF EXISTS `%DB_PREFIX%user_x_y_point`;
CREATE TABLE `%DB_PREFIX%user_x_y_point` (
  `id` int(11) NOT NULL auto_increment,
  `uid` int(11) NOT NULL,
  `xpoint` float(14,6) NOT NULL default '0.000000',
  `ypoint` float(14,6) NOT NULL default '0.000000',
  `locate_time` int(11) default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of fanwe_user_x_y_point
-- ----------------------------

-- ----------------------------
-- Table structure for `%DB_PREFIX%vip_festivals`
-- ----------------------------
DROP TABLE IF EXISTS `%DB_PREFIX%vip_festivals`;
CREATE TABLE `%DB_PREFIX%vip_festivals` (
  `id` int(11) NOT NULL auto_increment,
  `name` varchar(100) NOT NULL COMMENT '节日名称',
  `holiday_date` date NOT NULL COMMENT '节日时间',
  `number` int(11) NOT NULL COMMENT '数量',
  `brief` text NOT NULL COMMENT '简介',
  `is_delete` tinyint(1) NOT NULL COMMENT '删除标识',
  `is_effect` tinyint(1) NOT NULL COMMENT '有效性标识',
  `sort` int(11) NOT NULL COMMENT '节日礼品排序',
  `is_send` tinyint(1) NOT NULL COMMENT '发送标识',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=10 DEFAULT CHARSET=utf8 COMMENT='节日礼品表';

-- ----------------------------
-- Records of fanwe_vip_festivals
-- ----------------------------

-- ----------------------------
-- Table structure for `%DB_PREFIX%vip_gift`
-- ----------------------------
DROP TABLE IF EXISTS `%DB_PREFIX%vip_gift`;
CREATE TABLE `%DB_PREFIX%vip_gift` (
  `id` int(11) NOT NULL auto_increment,
  `name` varchar(100) NOT NULL COMMENT '礼品名称',
  `brief` text NOT NULL COMMENT '礼品简介',
  `is_delete` tinyint(1) NOT NULL COMMENT '删除标识',
  `is_effect` tinyint(1) NOT NULL COMMENT '有效性标识',
  `sort` int(11) NOT NULL COMMENT '礼品排序',
  PRIMARY KEY  (`id`),
  KEY `sort` USING BTREE (`sort`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='礼品列表';

-- ----------------------------
-- Records of fanwe_vip_gift
-- ----------------------------

-- ----------------------------
-- Table structure for `%DB_PREFIX%vip_red_envelope`
-- ----------------------------
DROP TABLE IF EXISTS `%DB_PREFIX%vip_red_envelope`;
CREATE TABLE `%DB_PREFIX%vip_red_envelope` (
  `id` int(11) NOT NULL auto_increment,
  `money` decimal(20,0) NOT NULL COMMENT '红包金额',
  `brief` text NOT NULL COMMENT '红包简介',
  `is_delete` tinyint(1) NOT NULL COMMENT '删除标识',
  `is_effect` tinyint(1) NOT NULL COMMENT '有效性标识',
  `sort` int(11) NOT NULL COMMENT '红包排序',
  PRIMARY KEY  (`id`),
  KEY `sort` USING BTREE (`sort`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='红包列表';

-- ----------------------------
-- Records of fanwe_vip_red_envelope
-- ----------------------------

-- ----------------------------
-- Table structure for `%DB_PREFIX%vip_setting`
-- ----------------------------
DROP TABLE IF EXISTS `%DB_PREFIX%vip_setting`;
CREATE TABLE `%DB_PREFIX%vip_setting` (
  `id` int(11) NOT NULL auto_increment,
  `vip_id` int(11) NOT NULL COMMENT 'VIP等级',
  `probability` int(11) NOT NULL COMMENT '收益奖励几率',
  `load_mfee` decimal(20,2) NOT NULL COMMENT '借款管理费(每月)',
  `interest` int(11) NOT NULL COMMENT '投资利息费',
  `charges` int(11) NOT NULL COMMENT '提现手续费(每笔)',
  `coefficient` decimal(20,2) NOT NULL COMMENT '积分折现系数',
  `multiple` decimal(20,1) NOT NULL COMMENT '积分获取倍数',
  `bgift` int(11) NOT NULL COMMENT '生日礼品',
  `btype` tinyint(1) NOT NULL COMMENT '生日礼品类别 1积分 2现金红包',
  `holiday_score` int(11) NOT NULL COMMENT '节日积分',
  `is_delete` tinyint(1) NOT NULL COMMENT '删除标识',
  `is_effect` tinyint(1) NOT NULL COMMENT '有效性标识',
  `sort` int(11) NOT NULL COMMENT 'VIP配置排序',
  `rate` decimal(20,2) NOT NULL COMMENT '增加的收益率',
  `integral` int(11) NOT NULL COMMENT '收益积分值',
  `red_envelope` varchar(100) NOT NULL COMMENT '多种类型红包金额',
  `gift` varchar(100) NOT NULL COMMENT '多种礼品ID',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=6 DEFAULT CHARSET=utf8 COMMENT='VIP配置表';

-- ----------------------------
-- Records of fanwe_vip_setting
-- ----------------------------
INSERT INTO `%DB_PREFIX%vip_setting` VALUES ('1', '1', '5', '0.30', '10', '2', '0.50', '1.0', '1000', '1', '500', '0', '1', '1', '0.00', '0', '', '');
INSERT INTO `%DB_PREFIX%vip_setting` VALUES ('2', '2', '10', '0.29', '8', '1', '0.55', '1.1', '50', '2', '1000', '0', '1', '2', '0.00', '0', '', '');
INSERT INTO `%DB_PREFIX%vip_setting` VALUES ('3', '3', '15', '0.28', '6', '0', '0.60', '1.2', '100', '2', '2000', '0', '1', '3', '0.00', '0', '', '');
INSERT INTO `%DB_PREFIX%vip_setting` VALUES ('4', '4', '20', '0.27', '4', '0', '0.65', '1.3', '200', '2', '5000', '0', '1', '4', '0.00', '0', '', '');
INSERT INTO `%DB_PREFIX%vip_setting` VALUES ('5', '5', '25', '0.25', '2', '0', '0.70', '1.4', '500', '2', '10000', '0', '1', '5', '0.00', '0', '', '');

-- ----------------------------
-- Table structure for `%DB_PREFIX%vip_type`
-- ----------------------------
DROP TABLE IF EXISTS `%DB_PREFIX%vip_type`;
CREATE TABLE `%DB_PREFIX%vip_type` (
  `id` int(11) NOT NULL auto_increment,
  `vip_grade` varchar(100) NOT NULL COMMENT 'VIP等级',
  `lower_limit` decimal(20,0) NOT NULL COMMENT '金额下限',
  `upper_limit` decimal(20,0) NOT NULL COMMENT '金额上限',
  `content` varchar(255) NOT NULL COMMENT 'VIP升级条件',
  `is_delete` tinyint(1) NOT NULL COMMENT '删除标识',
  `is_effect` tinyint(1) NOT NULL COMMENT '有效性标识',
  `sort` int(11) NOT NULL COMMENT 'VIP排序',
  PRIMARY KEY  (`id`),
  KEY `sort` USING BTREE (`sort`)
) ENGINE=MyISAM AUTO_INCREMENT=6 DEFAULT CHARSET=utf8 COMMENT='VIP类别表';

-- ----------------------------
-- Records of fanwe_vip_type
-- ----------------------------
INSERT INTO `%DB_PREFIX%vip_type` VALUES ('1', '普通VIP会员', '0', '49999', '注册完成后为普通VIP会员；', '0', '1', '1');
INSERT INTO `%DB_PREFIX%vip_type` VALUES ('2', '白银VIP会员', '50000', '499999', '总额度达到50000元，且没有迟还款和逾期还款的用户，系统自动为其升级成为白银VIP会员。', '0', '1', '2');
INSERT INTO `%DB_PREFIX%vip_type` VALUES ('3', '黄金VIP会员', '500000', '4999999', '总额度达到500000元，且没有迟还款和逾期还款记录的用户，系统自动为其升级成为黄金VIP会员。', '0', '1', '3');
INSERT INTO `%DB_PREFIX%vip_type` VALUES ('4', '铂金VIP会员', '5000000', '49999999', '总额度达到5000000元，且没有迟还款和逾期还款记录的用户，系统自动为其升级成为白金VIP会员。', '0', '1', '4');
INSERT INTO `%DB_PREFIX%vip_type` VALUES ('5', '钻石VIP会员', '50000000', '999999999999999', '总额度达到50000000元，且没有还款和逾期还款记录的用户，系统自动为其升级成为钻石VIP会员。', '0', '1', '5');

-- ----------------------------
-- Table structure for `%DB_PREFIX%vip_upgrade_record`
-- ----------------------------
DROP TABLE IF EXISTS `%DB_PREFIX%vip_upgrade_record`;
CREATE TABLE `%DB_PREFIX%vip_upgrade_record` (
  `id` int(11) NOT NULL auto_increment,
  `user_id` int(11) NOT NULL COMMENT '会员ID',
  `original_vip_id` int(11) NOT NULL COMMENT 'VIP原等级ID',
  `now_vip_id` int(11) NOT NULL COMMENT 'VIP现有等级ID',
  `causes` varchar(255) NOT NULL COMMENT 'VIP升级原因',
  `upgrade_date` date NOT NULL COMMENT 'VIP升级日期',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=6 DEFAULT CHARSET=utf8 COMMENT='VIP升级记录表';

-- ----------------------------
-- Records of fanwe_vip_upgrade_record
-- ----------------------------

-- ----------------------------
-- Table structure for `%DB_PREFIX%vote`
-- ----------------------------
DROP TABLE IF EXISTS `%DB_PREFIX%vote`;
CREATE TABLE `%DB_PREFIX%vote` (
  `id` int(11) NOT NULL auto_increment,
  `name` varchar(255) NOT NULL COMMENT '调查的项目名称',
  `begin_time` int(11) NOT NULL COMMENT '开始时间',
  `end_time` int(11) NOT NULL COMMENT '结束时间',
  `is_effect` tinyint(1) NOT NULL COMMENT '有效性',
  `sort` int(11) NOT NULL,
  `description` text NOT NULL COMMENT '描述',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of fanwe_vote
-- ----------------------------

-- ----------------------------
-- Table structure for `%DB_PREFIX%vote_ask`
-- ----------------------------
DROP TABLE IF EXISTS `%DB_PREFIX%vote_ask`;
CREATE TABLE `%DB_PREFIX%vote_ask` (
  `id` int(11) NOT NULL auto_increment,
  `name` varchar(255) NOT NULL COMMENT '投票项名称',
  `type` tinyint(1) NOT NULL COMMENT '投票类型，单选多选/自定义可叠加 1:单选 2:多选 3:自定义',
  `sort` int(11) NOT NULL COMMENT ' 排序 大到小',
  `vote_id` int(11) NOT NULL COMMENT '调查ID',
  `val_scope` text NOT NULL COMMENT '预选范围 逗号分开',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of fanwe_vote_ask
-- ----------------------------

-- ----------------------------
-- Table structure for `%DB_PREFIX%vote_result`
-- ----------------------------
DROP TABLE IF EXISTS `%DB_PREFIX%vote_result`;
CREATE TABLE `%DB_PREFIX%vote_result` (
  `id` int(11) NOT NULL auto_increment,
  `name` varchar(255) NOT NULL COMMENT '投票的名称',
  `count` int(11) NOT NULL COMMENT '计数',
  `vote_id` int(11) NOT NULL COMMENT '调查项ID',
  `vote_ask_id` int(11) NOT NULL COMMENT '投票项（问题）ID',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of fanwe_vote_result
-- ----------------------------

-- ----------------------------
-- Table structure for `%DB_PREFIX%yeepay_bind_bank_card`
-- ----------------------------
DROP TABLE IF EXISTS `%DB_PREFIX%yeepay_bind_bank_card`;
CREATE TABLE `%DB_PREFIX%yeepay_bind_bank_card` (
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

-- ----------------------------
-- Records of fanwe_yeepay_bind_bank_card
-- ----------------------------

-- ----------------------------
-- Table structure for `%DB_PREFIX%yeepay_cp_transaction`
-- ----------------------------
DROP TABLE IF EXISTS `%DB_PREFIX%yeepay_cp_transaction`;
CREATE TABLE `%DB_PREFIX%yeepay_cp_transaction` (
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
  `deal_repay_id` int(11) default NULL COMMENT '还款计划ID',
  `fee` decimal(20,2) default NULL,
  `repay_start_time` varchar(50) default NULL COMMENT '记录还款时间',
  `create_time` int(11) default NULL,
  `update_time` int(11) default NULL COMMENT '易宝处理时间',
  PRIMARY KEY  (`id`,`requestNo`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of fanwe_yeepay_cp_transaction
-- ----------------------------

-- ----------------------------
-- Table structure for `%DB_PREFIX%yeepay_cp_transaction_detail`
-- ----------------------------
DROP TABLE IF EXISTS `%DB_PREFIX%yeepay_cp_transaction_detail`;
CREATE TABLE `%DB_PREFIX%yeepay_cp_transaction_detail` (
  `id` int(10) NOT NULL auto_increment,
  `pid` int(10) NOT NULL default '0' COMMENT 'fanwe_yeepay_repayment.id',
  `deal_load_repay_id` int(11) NOT NULL default '0' COMMENT '用户回款计划表',
  `targetUserType` int(11) NOT NULL default '0' COMMENT '用户类型',
  `targetPlatformUserNo` int(11) NOT NULL default '0' COMMENT '平台用户编号',
  `amount` decimal(20,2) NOT NULL default '0.00' COMMENT '转入金额',
  `bizType` varchar(20) NOT NULL default '' COMMENT '资金明细业务类型。根据业务的不同，需要传入不同的值，见【业务类型',
  `repay_manage_impose_money` decimal(20,2) default NULL COMMENT '平台收取借款者的管理费逾期罚息',
  `impose_money` decimal(20,2) default NULL COMMENT '投资者收取借款者的逾期罚息',
  `repay_status` int(11) default NULL COMMENT '还款状态',
  `true_repay_time` int(11) default NULL COMMENT '还款时间',
  `fee` decimal(20,2) default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=29 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of fanwe_yeepay_cp_transaction_detail
-- ----------------------------

-- ----------------------------
-- Table structure for `%DB_PREFIX%yeepay_enterprise_register`
-- ----------------------------
DROP TABLE IF EXISTS `%DB_PREFIX%yeepay_enterprise_register`;
CREATE TABLE `%DB_PREFIX%yeepay_enterprise_register` (
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

-- ----------------------------
-- Records of fanwe_yeepay_enterprise_register
-- ----------------------------

-- ----------------------------
-- Table structure for `%DB_PREFIX%yeepay_log`
-- ----------------------------
DROP TABLE IF EXISTS `%DB_PREFIX%yeepay_log`;
CREATE TABLE `%DB_PREFIX%yeepay_log` (
  `id` int(10) NOT NULL auto_increment,
  `code` varchar(50) NOT NULL,
  `create_date` datetime NOT NULL,
  `strxml` text,
  `html` text,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=71034 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of fanwe_yeepay_log
-- ----------------------------

-- ----------------------------
-- Table structure for `%DB_PREFIX%yeepay_recharge`
-- ----------------------------
DROP TABLE IF EXISTS `%DB_PREFIX%yeepay_recharge`;
CREATE TABLE `%DB_PREFIX%yeepay_recharge` (
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
  `create_time` int(11) default NULL,
  `fee` decimal(20,2) NOT NULL default '0.00' COMMENT '手续费',
  PRIMARY KEY  (`id`,`requestNo`)
) ENGINE=MyISAM AUTO_INCREMENT=29 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of fanwe_yeepay_recharge
-- ----------------------------

-- ----------------------------
-- Table structure for `%DB_PREFIX%yeepay_register`
-- ----------------------------
DROP TABLE IF EXISTS `%DB_PREFIX%yeepay_register`;
CREATE TABLE `%DB_PREFIX%yeepay_register` (
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
  `create_time` int(11) default NULL,
  PRIMARY KEY  (`id`,`requestNo`)
) ENGINE=MyISAM AUTO_INCREMENT=26 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of fanwe_yeepay_register
-- ----------------------------

-- ----------------------------
-- Table structure for `%DB_PREFIX%yeepay_withdraw`
-- ----------------------------
DROP TABLE IF EXISTS `%DB_PREFIX%yeepay_withdraw`;
CREATE TABLE `%DB_PREFIX%yeepay_withdraw` (
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
  `create_time` int(11) default NULL,
  `fee` decimal(20,2) default NULL COMMENT '手续费',
  PRIMARY KEY  (`id`,`requestNo`)
) ENGINE=MyISAM AUTO_INCREMENT=36 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of fanwe_yeepay_withdraw
-- ----------------------------
