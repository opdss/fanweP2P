ALTER TABLE `fanwe_user`
ADD COLUMN `ips_acct_no`  varchar(30) NULL COMMENT 'pIpsAcctNo 30 IPS托管平台账 户号';

ALTER TABLE `fanwe_deal_load`
ADD COLUMN `pP2PBillNo`  varchar(30) NULL COMMENT 'IPS P2P订单号 否 由IPS系统生成的唯一流水号',
ADD COLUMN `pContractNo`  varchar(30) NULL COMMENT '合同号',
ADD COLUMN `pMerBillNo`  varchar(30) NULL COMMENT '登记债权人时提 交的订单号',
ADD COLUMN `is_has_loans`  tinyint(1) NOT NULL DEFAULT 0 COMMENT '是否已经放款给招标人',
ADD COLUMN `msg`  varchar(100) NULL COMMENT '转账备注  转账失败的原因' ;

//将旧的满标数据,更新为：4还款中，5已还清
update fanwe_deal_load set is_has_loans = 1 where deal_id in (select id from fanwe_deal where deal_status in (4,5));

ALTER TABLE `fanwe_deal`


ADD COLUMN `guarantees_amt`  decimal(20,2) NOT NULL DEFAULT 0.0000 COMMENT '借款保证金（冻结借款人的金额，需要提前存钱）',
ADD COLUMN `real_freezen_amt`  decimal(20,2) NULL DEFAULT 0 COMMENT '借款方 实际冻结金额 = 保证金',
ADD COLUMN `un_real_freezen_amt`  decimal(11,2) NULL DEFAULT 0 COMMENT '已经解冻的担保保证金（借款方）<=real_freezen_amt',

ADD COLUMN `guarantor_amt`  decimal(11,2) NULL DEFAULT 0 COMMENT '担保方，担保金额(代偿金额累计不能大于担保金额)',

ADD COLUMN `guarantor_margin_amt`  decimal(11,2) NULL DEFAULT 0 COMMENT '担保方，担保保证金额(需要冻结担保方的金额）',
ADD COLUMN `guarantor_real_freezen_amt`  decimal(20,2) NULL DEFAULT 0 COMMENT '担保方 实际冻结金额 = 担保保证金额',
ADD COLUMN `un_guarantor_real_freezen_amt`  decimal(20,2) NULL DEFAULT 0 COMMENT '已经解冻的担保保证金（担保方）<=guarantor_real_freezen_amt',

ADD COLUMN `guarantor_pro_fit_amt`  decimal(11,2) NULL DEFAULT 0 COMMENT '担保收益',
ADD COLUMN `guarantor_real_fit_amt`  decimal(11,2) NULL DEFAULT 0 COMMENT '实际担保收益，转帐后更新<=guarantor_pro_fit_amt',


ADD COLUMN `mer_bill_no`  varchar(30) NULL COMMENT '标的登记时提交的订单单号',
ADD COLUMN `ips_bill_no`  varchar(30) NULL COMMENT '由IPS系统生成的唯一流水号',
ADD COLUMN `ips_guarantor_bill_no`  varchar(30) NULL COMMENT '担保编号ips返回的',
ADD COLUMN `mer_guarantor_bill_no`  varchar(30) NULL COMMENT '提交的担保单号';


ALTER TABLE `fanwe_deal_load_transfer`
ADD COLUMN `lock_user_id`  int(11) NOT NULL DEFAULT 0 COMMENT '锁定用户id,给用户支付时间,主要用于资金托管' AFTER `callback_count`,
ADD COLUMN `lock_time`  int(11) NOT NULL DEFAULT 0 COMMENT '锁定时间,10分钟后,自动解锁;给用户支付时间,主要用于资金托管' AFTER `lock_user_id`,
ADD COLUMN `ips_status`  tinyint(1) NOT NULL DEFAULT 0 COMMENT 'ips处理状态;0:未处理;1:已登记债权转让;2:已转让',
ADD COLUMN `ips_bill_no`  varchar(30) NULL COMMENT 'IPS P2P订单号 否 由IPS系统生成的唯一流水号',
ADD COLUMN `pMerBillNo`  varchar(30) NULL COMMENT '商户订单号 商户系统唯一不重复';


ALTER TABLE `fanwe_deal_agency`
ADD COLUMN `acct_type`  tinyint(1) NOT NULL DEFAULT 1 COMMENT '担保方类型 否 0#机构；1#个人' AFTER `address`,
ADD COLUMN `ips_mer_code`  varchar(10) NULL COMMENT '由IPS颁发的商户号 acct_type = 0' AFTER `acct_type`,
ADD COLUMN `idno`  varchar(20) NULL COMMENT '真实身份证 acct_type =1',
ADD COLUMN `real_name`  varchar(30) NULL COMMENT 'acct_type = 1真实姓名' AFTER `idno`,
ADD COLUMN `mobile`  varchar(11) NULL AFTER `real_name`,
ADD COLUMN `email`  varchar(30) NULL AFTER `mobile`,
ADD COLUMN `ips_acct_no`  varchar(30) NULL COMMENT 'ips个人帐户' AFTER `email`;