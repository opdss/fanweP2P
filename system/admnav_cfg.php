<?php 
return array( 
	"index"	=>	array(
		"name"	=>	"系统首页", 
		"key"	=>	"index", 
		"groups"	=>	array( 
			"index"	=>	array(
				"name"	=>	"系统首页", 
				"key"	=>	"index", 
				"nodes"	=>	array( 
					array("name"=>"待办事务","module"=>"Index","action"=>"main"),
					array("name"=>"网站数据统计","module"=>"Index","action"=>"statistics"),
					array("name"=>"借款统计","module"=>"Statistics","action"=>"index"),
				),
			),
			"syslog"	=>	array(
				"name"	=>	"系统日志", 
				"key"	=>	"syslog", 
				"nodes"	=>	array( 
					array("name"=>"系统日志列表","module"=>"Log","action"=>"index"),
				),
			),
		),
	),
	"deal"	=>	array(
		"name"	=>	"贷款管理", 
		"key"	=>	"deal", 
		"groups"	=>	array( 			
			"deal"	=>	array(
				"name"	=>	"贷款管理", 
				"key"	=>	"deal", 
				"nodes"	=>	array( 
					array("name"=>"全部贷款","module"=>"Deal","action"=>"index"),
					array("name"=>"预告中贷款","module"=>"Deal","action"=>"advance"),
					array("name"=>"贷款回收站","module"=>"Deal","action"=>"trash"),
				),
			),
			
			"deal_c"	=>	array(
			"name"	=>	"审核管理", 
			"key"	=>	"deal_s", 
			"nodes"	=>	array( 
					array("name"=>"待审核列表","module"=>"Deal","action"=>"publish"),
					array("name"=>"复审核列表","module"=>"Deal","action"=>"true_publish"),
					array("name"=>"等材料贷款","module"=>"Deal","action"=>"wait"),
					array("name"=>"未满标贷款","module"=>"Deal","action"=>"ing"),
					array("name"=>"过期的贷款","module"=>"Deal","action"=>"expire"),
					array("name"=>"流标的贷款","module"=>"Deal","action"=>"flow"),
				),
			),
			
			"deal_s"	=>	array(
			"name"	=>	"满标管理", 
			"key"	=>	"deal_s", 
			"nodes"	=>	array( 
					array("name"=>"满标待放款","module"=>"Deal","action"=>"full"),
					array("name"=>"还款中贷款","module"=>"Deal","action"=>"inrepay"),
					array("name"=>"已完成贷款","module"=>"Deal","action"=>"over"),
					array("name"=>"提前还贷款","module"=>"Deal","action"=>"penalty"),
				),
			),
			
			"deal_money"	=>	array(
			"name"	=>	"借贷记录", 
			"key"	=>	"deal_money", 
			"nodes"	=>	array(
					array("name"=>"待还款账单","module"=>"Deal","action"=>"three"),
					array("name"=>"逾期待收款","module"=>"Deal","action"=>"yuqi"),
					array("name"=>"网站垫付款","module"=>"Deal","action"=>"generation_repay"),
					array("name"=>"收款信息","module"=>"Deal","action"=>"user_loads_repay"),
				),
			),
			
			"loads"	=>	array(
			"name"	=>	"投标信息", 
			"key"	=>	"loads", 
			"nodes"	=>	array( 
					array("name"=>"所有投标","module"=>"Loads","action"=>"index"),
					array("name"=>"手动投标","module"=>"Loads","action"=>"hand"),
					array("name"=>"自动投标","module"=>"Loads","action"=>"auto"),
					array("name"=>"成功的投标","module"=>"Loads","action"=>"success"),
					array("name"=>"失败的投标","module"=>"Loads","action"=>"failed"),
				),
			),
			
			"transfer"	=>	array(
					"name"	=>	"债权转让",
					"key"	=>	"transfer",
					"nodes"	=>	array(
						array("name"=>"所有转让","module"=>"Transfer","action"=>"index"),
						array("name"=>"正在转让","module"=>"Transfer","action"=>"ing"),
						array("name"=>"成功转让","module"=>"Transfer","action"=>"success"),
						array("name"=>"撤销转让","module"=>"Transfer","action"=>"back"),
					),
			),
			
	
			"message"	=>	array(
					"name"	=>	"留言管理",
					"key"	=>	"message",
					"nodes"	=>	array(
							array("name"=>"留言列表","module"=>"Message","action"=>"index"),
					),
			),
			
			
				
			/*"uplan"	=>	array(
					"name"	=>	"U-计划",
					"key"	=>	"uplan",
					"nodes"	=>	array(
							array("name"=>"计划分类列表","module"=>"PlanCate","action"=>"index"),
							array("name"=>"计划列表","module"=>"Plan","action"=>"index"),
							//array("name"=>"计划参与列表","module"=>"PlanJoin","action"=>"index"),
					),
			),
				
			"peizi"	=>	array(
					"name"	=>	"股票配资",
					"key"	=>	"peizi",
					"nodes"	=>	array(
							array("name"=>"股票配资","module"=>"PeiziConf","action"=>"index"),
							array("name"=>"天利率配置","module"=>"Everwin","action"=>"rate"),
							array("name"=>"按周操盘","module"=>"Weekwin","action"=>"index"),
							array("name"=>"按月操盘","module"=>"Scheme","action"=>"index"),
							array("name"=>"月利率配置","module"=>"Scheme","action"=>"rate"),
							array("name"=>"期货配资","module"=>"Futures","action"=>"index"),
							array("name"=>"期货配资利率","module"=>"Futures","action"=>"rate"),
					),
			),
			*/	
		),
	),
	"user"	=>	array(
			"name"	=>	"会员管理",
			"key"	=>	"user",
			"groups"	=>	array(
					"user"	=>	array(
							"name"	=>	"普通会员",
							"key"	=>	"user",
							"nodes"	=>	array(
									array("name"=>"普通会员","module"=>"User","action"=>"index"),
									array("name"=>"会员黑名单","module"=>"User","action"=>"black"),
									array("name"=>"待审核会员","module"=>"User","action"=>"register"),
									array("name"=>"会员信息","module"=>"User","action"=>"info"),
									array("name"=>"会员回收站","module"=>"User","action"=>"trash"),
							),
					),
					"company"	=>	array(
							"name"	=>	"企业会员",
							"key"	=>	"company",
							"nodes"	=>	array(
									array("name"=>"企业会员","module"=>"User","action"=>"company_index"),
									array("name"=>"会员黑名单","module"=>"User","action"=>"company_black"),
									array("name"=>"待审核会员","module"=>"User","action"=>"company_register"),
									array("name"=>"会员信息","module"=>"User","action"=>"company_info"),
									array("name"=>"会员回收站","module"=>"User","action"=>"company_trash"),
							),
					),
					"agencies"	=>	array(
							"name"	=>	"授权服务机构",
							"key"	=>	"agencies",
							"nodes"	=>	array(
									array("name"=>"授权服务机构","module"=>"User","action"=>"agencies_index"),
									array("name"=>"授权服务机构回收站","module"=>"User","action"=>"agencies_trash"),
							),
					),
					
					"agency"	=>	array(
							"name"	=>	"担保机构",
							"key"	=>	"agency",
							"nodes"	=>	array(
									array("name"=>"担保机构","module"=>"DealAgency","action"=>"index"),
									array("name"=>"担保机构回收站","module"=>"DealAgency","action"=>"trash"),
							),
					),
					
					
					"other"	=>	array(
							"name"	=>	"其他信息",
							"key"	=>	"other",
							"nodes"	=>	array(
									array("name"=>"公司列表","module"=>"User","action"=>"company_manage"),
									array("name"=>"工作信息","module"=>"User","action"=>"work_manage"),
									array("name"=>"银行卡列表","module"=>"User","action"=>"bank_manage"),
							),
					),
					
					/*"ecvtype"	=>	array(
							"name"	=>	"优惠券管理",
							"key"	=>	"ecvtype",
							"nodes"	=>	array(
									array("name"=>"优惠券类型","module"=>"EcvType","action"=>"index"),
							),
					),*/
					"userconfig"	=>	array(
							"name"	=>	"相关配置",
							"key"	=>	"userconfig",
							"nodes"	=>	array(
									array("name"=>"会员字段列表","module"=>"UserField","action"=>"index"),
									//array("name"=>"会员组别列表","module"=>"UserGroup","action"=>"index"),
									array("name"=>"信用等级列表","module"=>"UserLevel","action"=>"index"),
							),
					),
					"notice"	=>	array(
							"name"	=>	"站内消息",
							"key"	=>	"notice",
							"nodes"	=>	array(
									array("name"=>"消息群发","module"=>"MsgSystem","action"=>"index"),
									array("name"=>"消息列表","module"=>"MsgBox","action"=>"index"),
							),
					),
					
					"privilege"	=>	array(
								"name"	=>	"VIP特权",
								"key"	=>	"privilege",
								"nodes"	=>	array(
										array("name"=>"VIP会员表","module"=>"VipPrivilege","action"=>"vip_user"),
										array("name"=>"VIP等级","module"=>"VipType","action"=>"index"),
//										array("name"=>"VIP等级回收站","module"=>"VipType","action"=>"vip_type_trash"),
										array("name"=>"VIP配置列表","module"=>"VipSetting","action"=>"index"),
										array("name"=>"VIP配置回收站","module"=>"VipSetting","action"=>"setting_trash"),
										array("name"=>"VIP升级记录","module"=>"VipPrivilege","action"=>"vip_upgrade_record"),
										array("name"=>"VIP降级记录","module"=>"VipPrivilege","action"=>"vip_demotion_record"),
										array("name"=>"客服列表","module"=>"Customers","action"=>"index"),
										array("name"=>"客服回收站","module"=>"Customers","action"=>"trash"),
								),
						),
		
						"reward"	=>	array(
								"name"	=>	"投资奖励",
								"key"	=>	"gift",
								"nodes"	=>	array(
										array("name"=>"奖励发放列表","module"=>"VipGift","action"=>"vip_gift_record"),
										array("name"=>"礼品管理","module"=>"VipGift","action"=>"index"),
										array("name"=>"红包管理","module"=>"VipRedEnvelope","action"=>"index"),

								),
						),
		
						"welfare"	=>	array(
								"name"	=>	"节日福利",
								"key"	=>	"welfare",
								"nodes"	=>	array(
										array("name"=>"节日积分表","module"=>"VipFestivals","action"=>"index"),
//										array("name"=>"节日积分回收站","module"=>"VipFestivals","action"=>"festivals_trash"),
										array("name"=>"福利发放列表","module"=>"VipWelfare","action"=>"given_record"),
										array("name"=>"积分兑现","module"=>"VipWelfare","action"=>"score_exchange"),
										
								),
						),
					
			),
	),	
	
	
	"order"	=>	array(
			"name"	=>	"资金管理",
			"key"	=>	"order",
			"groups"	=>	array(
					"order"	=>	array(
							"name"	=>	"充值管理",
							"key"	=>	"order",
							"nodes"	=>	array(
									array("name"=>"在线充值单","module"=>"PaymentNotice","action"=>"index"),									
									array("name"=>"在线充值日账单","module"=>"BankReconciliation","action"=>"index"),
									array("name"=>"线下充值单","module"=>"PaymentNotice","action"=>"online"),
							),
					),
					
					"usercarry"	=>	array(
							"name"	=>	"提现申请管理",
							"key"	=>	"usercarry",
							"nodes"	=>	array(
									array("name"=>"所有申请","module"=>"UserCarry","action"=>"index"),
									array("name"=>"待审申请","module"=>"UserCarry","action"=>"wait"),
									array("name"=>"待付申请","module"=>"UserCarry","action"=>"waitpay"),
									array("name"=>"成功申请","module"=>"UserCarry","action"=>"success"),
									array("name"=>"失败申请","module"=>"UserCarry","action"=>"failed"),
									array("name"=>"会员撤销","module"=>"UserCarry","action"=>"reback"),
							),
					),
					
					"moneylog"=>array(
							"name"	=>	"资金日志",
							"key"	=>	"moneylog",
							"nodes"	=>	array(
									array("name"=>"会员资金日志","module"=>"User","action"=>"fund_management"),
									array("name"=>"网站收支","module"=>"Deal","action"=>"site_money"),
									array("name"=>"保障金","module"=>"Security","action"=>"index"),
									array("name"=>"风险准备金","module"=>"ProvisionsRisk","action"=>"index"),
							),
					),
					
					"hand_operated"	=>	array(
							"name"	=>	"手动操作",
							"key"	=>	"hand_operated",
							"nodes"	=>	array(
									array("name"=>"快速充值","module"=>"User","action"=>"hand_recharge"),
									array("name"=>"快速扣款","module"=>"User","action"=>"hand_overdue"),
									array("name"=>"冻结资金","module"=>"User","action"=>"hand_freeze"),
									array("name"=>"变更信用积分","module"=>"User","action"=>"hand_integral"),
									array("name"=>"变更积分","module"=>"User","action"=>"hand_integrals"),
									array("name"=>"变更额度","module"=>"User","action"=>"hand_quota"),
							),
					),
					
					"ipslog"	=>	array(
							"name"	=>	"第三方托管对账",
							"key"	=>	"ipslog",
							"nodes"	=>	array(
									array("name"=>"开户","module"=>"Ipslog","action"=>"create"),
									array("name"=>"标的登记","module"=>"Ipslog","action"=>"trade"),
									array("name"=>"投标记录","module"=>"Ipslog","action"=>"creditor"),
									//array("name"=>"担保方","module"=>"Ipslog","action"=>"guarantor"),
									array("name"=>"充值","module"=>"Ipslog","action"=>"recharge"),
									array("name"=>"提现","module"=>"Ipslog","action"=>"transfer"),
									array("name"=>"还款单","module"=>"IpsRelation","action"=>"repayment"),
									array("name"=>"回款单","module"=>"IpsRelation","action"=>"back_repayment"),
									array("name"=>"满标放款","module"=>"IpsFullscale","action"=>"index"),
									array("name"=>"债权转让","module"=>"IpsTransfer","action"=>"index"),
									//array("name"=>"担保收益","module"=>"IpsProfit","action"=>"index"),
							),				
					),
			),
	),
	
	"routine" => array(
		"name" => "待办事务",
		"key"  => "routine",
		"groups" => array(
			
			"generationrepay"	=>	array(
				"name"	=>	"续约申请",
					"key"	=>	"generationrepay",
					"nodes"	=>	array(
						array("name"=>"续约申请","module"=>"GenerationRepaySubmit","action"=>"index"),
					),
			),
			"dealquotasubmit"	=>	array(
					"name"	=>	"授信额度申请",
					"key"	=>	"dealquotasubmit",
					"nodes"	=>	array(
							array("name"=>"申请列表","module"=>"DealQuotaSubmit","action"=>"index"),
					),
			),
			"quotasubmit"	=>	array(
					"name"	=>	"信用额度申请",
					"key"	=>	"quotasubmit",
					"nodes"	=>	array(
							array("name"=>"申请列表","module"=>"QuotaSubmit","action"=>"index"),
					),
			),
			"reportguy"	=>	array(
					"name"	=>	"举报管理",
					"key"	=>	"reportguy",
					"nodes"	=>	array(
							array("name"=>"举报列表","module"=>"Reportguy","action"=>"index"),
					),
			),
			"credit"	=>	array(
					"name"	=>	"认证管理",
					"key"	=>	"credit",
					"nodes"	=>	array(
							array("name"=>"所有认证","module"=>"Credit","action"=>"user"),
							array("name"=>"待审的认证","module"=>"Credit","action"=>"user_wait"),
							array("name"=>"通过的认证","module"=>"Credit","action"=>"user_success"),
							array("name"=>"失败的认证","module"=>"Credit","action"=>"user_bad"),
					),
			),
			
			"referral"	=>	array(
					"name"	=>	"会员返利",
					"key"	=>	"referral",
					"nodes"	=>	array(
							array("name"=>"邀请返利列表","module"=>"Referrals","action"=>"index"),
							array("name"=>"建立关联","module"=>"CreateRelevance","action"=>"index"),
							array("name"=>"推广人列表","module"=>"PromotionHuman","action"=>"index"),
					),
			),
			
			"rebate"	=>	array(
					"name"	=>	"返佣列表",
					"key"	=>	"referral",
					"nodes"	=>	array(
							array("name"=>"投资返佣列表","module"=>"Referrals_rebate","action"=>"index"),
							array("name"=>"借款返佣列表","module"=>"Referrals_rebate","action"=>"borrow_index"),
							array("name"=>"建立关联","module"=>"CreateRelevance_rebate","action"=>"index"),
							array("name"=>"授权服务机构统计","module"=>"PromotionHuman_rebate","action"=>"index"),
					),
			)
		)
	),
	
	"statistics"	=>	array(
				"name"	=>	"统计模块",
				"key"	=>	"statistics",
				"groups"	=>	array(
						"borrow_statistics"	=>	array(
								"name"	=>	"借出统计",
								"key"	=>	"borrow_statistics",
								"nodes"	=>	array(
										array("name"=>"借出总统计","module"=>"StatisticsBorrow","action"=>"tender_total"),
										array("name"=>"投资人数","module"=>"StatisticsBorrow","action"=>"tender_usernum_total"),
										array("name"=>"投资金额","module"=>"StatisticsBorrow","action"=>"tender_account_total"),
										array("name"=>"标种投资","module"=>"StatisticsBorrow","action"=>"tender_borrow_type"),
										array("name"=>"已回款","module"=>"StatisticsBorrow","action"=>"tender_hasback_total"),
										array("name"=>"待收款","module"=>"StatisticsBorrow","action"=>"tender_tobe_receivables"),
										array("name"=>"投资排名","module"=>"StatisticsBorrow","action"=>"tender_rank_list"),
										array("name"=>"投资额比例","module"=>"StatisticsBorrow","action"=>"tender_account_ratio"),
								),
						),
		
						"loan_statistics"	=>	array(
								"name"	=>	"借入统计",
								"key"	=>	"loan_statistics",
								"nodes"	=>	array(
										array("name"=>"借入总统计","module"=>"StatisticsLoan","action"=>"loan_total"),
										array("name"=>"借款人数","module"=>"StatisticsLoan","action"=>"loan_usernum_total"),
										array("name"=>"借款金额","module"=>"StatisticsLoan","action"=>"loan_account_total"),
										array("name"=>"标种借款","module"=>"StatisticsLoan","action"=>"loan_borrow_type"),
										array("name"=>"已还款","module"=>"StatisticsLoan","action"=>"loan_hasback_total"),
										array("name"=>"待还款","module"=>"StatisticsLoan","action"=>"loan_tobe_receivables"),
										array("name"=>"逾期还款","module"=>"StatisticsLoan","action"=>"loan_repay_late_total"),

								),
						),
		
		
						"claims_statistics"	=>	array(
								"name"	=>	"债权统计",
								"key"	=>	"claims_statistics",
								"nodes"	=>	array(
										array("name"=>"债权转让","module"=>"StatisticsClaims","action"=>"change_account_total"),
								),
						),
							
						"website_statistics"	=>	array(
								"name"	=>	"平台统计",
								"key"	=>	"website_statistics",
								"nodes"	=>	array(
										array("name"=>"充值统计","module"=>"WebsiteStatistics","action"=>"website_recharge_total"),
										array("name"=>"提现统计","module"=>"WebsiteStatistics","action"=>"website_extraction_cash"),
										array("name"=>"用户统计","module"=>"WebsiteStatistics","action"=>"website_users_total"),
										array("name"=>"网站垫付统计","module"=>"WebsiteStatistics","action"=>"website_advance_total"),
										array("name"=>"网站费用统计","module"=>"WebsiteStatistics","action"=>"website_cost_total"),
								),
						),
							
				),
		),
		
	
		
	"department"	=>	array(
		"name"	=>	"部门管理",
		"key"	=>	"department",
		"groups"	=>	array(
			"admin_manage"	=>	array(
				"name"	=>	"管理员管理",
				"key"	=>	"admin_manage",
				"nodes"	=>	array(
					array("name"=>"部门列表","module"=>"Departments","action"=>"index"),
					array("name"=>"部门回收站","module"=>"Departments","action"=>"trash"),
					array("name"=>"部门成员","module"=>"MyManager","action"=>"index"),
					array("name"=>"待分配会员","module"=>"MyCustomer","action"=>"index"),
					array("name"=>"待分配借款标","module"=>"OverdueBillMonth","action"=>"unallocated_standard"),
					array("name"=>"我的会员","module"=>"MyMembership","action"=>"index"),
					array("name"=>"所有借款标","module"=>"OverdueBillMonth","action"=>"all_loan"),
				),
			),
				
			"member_bill"	=>	array(
					"name"	=>	"我的会员账单",
					"key"	=>	"member_bill",
					"nodes"	=>	array(
							array("name"=>"本月到期账单","module"=>"OverdueBillMonth","action"=>"index"),
							array("name"=>"逾期账单","module"=>"OverdueBillMonth","action"=>"overdue_bill"),
							array("name"=>"已还款账单","module"=>"OverdueBillMonth","action"=>"repayment_bill"),
							array("name"=>"还款中借款标","module"=>"OverdueBillMonth","action"=>"repayloan_scale"),
							array("name"=>"已完成借款标","module"=>"OverdueBillMonth","action"=>"completedloan_scale"),
							array("name"=>"已坏账借款标","module"=>"OverdueBillMonth","action"=>"badloan_scale"),
							array("name"=>"借款会员列表","module"=>"User","action"=>"borrowing_member"),
							array("name"=>"坏账会员列表","module"=>"User","action"=>"bad_member"),
						
					),
			),
				
				
		),
	),
		
	"Integral"	=>	array(
			"name"	=>	"积分商城",
			"key"	=>	"Integral",
			"groups"	=>	array(
					"integral_mall"	=>	array(
							"name"	=>	"积分商城",
							"key"	=>	"integral_mall",
							"nodes"	=>	array(
									array("name"=>"商品列表","module"=>"Goods","action"=>"index"),
									array("name"=>"商品类型","module"=>"GoodsType","action"=>"index"),
									array("name"=>"商品分类","module"=>"GoodsCate","action"=>"index"),
									array("name"=>"兑换商品","module"=>"GoodsOrder","action"=>"index"),
							),
					),
	
			),
	),
		
	
	"promote"	=>	array(
		"name"	=>	"短信邮件", 
		"key"	=>	"promote", 
		"groups"	=>	array( 
			"msg"	=>	array(
				"name"	=>	"消息模板管理", 
				"key"	=>	"msg", 
				"nodes"	=>	array( 
					array("name"=>"消息模板管理","module"=>"MsgTemplate","action"=>"index"),
				),
			),
			"mail"	=>	array(
				"name"	=>	"邮件管理", 
				"key"	=>	"mail", 
				"nodes"	=>	array( 
					array("name"=>"邮件服务器列表","module"=>"MailServer","action"=>"index"),
					array("name"=>"邮件列表","module"=>"PromoteMsg","action"=>"mail_index"),
				),
			),
			"sms"	=>	array(
				"name"	=>	"短信管理", 
				"key"	=>	"sms", 
				"nodes"	=>	array( 
					array("name"=>"短信接口列表","module"=>"Sms","action"=>"index"),
					array("name"=>"短信列表","module"=>"PromoteMsg","action"=>"sms_index"),
				),
			),
			"msglist"	=>	array(
				"name"	=>	"队列管理", 
				"key"	=>	"msglist", 
				"nodes"	=>	array( 
					array("name"=>"业务队列列表","module"=>"DealMsgList","action"=>"index"),
					array("name"=>"推广队列列表","module"=>"PromoteMsgList","action"=>"index"),
				),
			),
		),
	),
	"front"	=>	array(
			"name"	=>	"前端设置",
			"key"	=>	"front",
			"groups"	=>	array(
				"article"	=>	array(
						"name"	=>	"文章管理",
						"key"	=>	"article",
						"nodes"	=>	array(
								array("name"=>"文章列表","module"=>"Article","action"=>"index"),
								array("name"=>"文章回收站","module"=>"Article","action"=>"trash"),
						),
				),					
				"articlecate"	=>	array(
						"name"	=>	"文章分类",
						"key"	=>	"articlecate",
						"nodes"	=>	array(
								array("name"=>"分类列表","module"=>"ArticleCate","action"=>"index"),
								array("name"=>"分类回收站","module"=>"ArticleCate","action"=>"trash"),
						),
				),
				"frontconfig"	=>	array(
						"name"	=>	"前端设置",
						"key"	=>	"frontconfig",
						"nodes"	=>	array(
								array("name"=>"导航菜单列表","module"=>"Nav","action"=>"index"),
								array("name"=>"投票调查列表","module"=>"Vote","action"=>"index"),
								array("name"=>"前端广告列表","module"=>"Adv","action"=>"index"),
						),
				),
				
				"link"	=>	array(
						"name"	=>	"友情链接",
						"key"	=>	"link",
						"nodes"	=>	array(
								array("name"=>"友情链接分组","module"=>"LinkGroup","action"=>"index"),
								array("name"=>"友情链接列表","module"=>"Link","action"=>"index"),
						),
				),
			),
	),
	"system"	=>	array(
		"name"	=>	"系统设置", 
		"key"	=>	"system", 
		"groups"	=>	array( 
			"sysconf"	=>	array(
				"name"	=>	"系统设置", 
				"key"	=>	"sysconf", 
				"nodes"	=>	array( 
					array("name"=>"系统配置","module"=>"Conf","action"=>"index"),
					array("name"=>"邀请返利配置","module"=>"Conf","action"=>"referrals"),
					array("name"=>"授权服务机构返佣设置","module"=>"Conf","action"=>"commossion"),
					array("name"=>"QQ客服配置","module"=>"Conf","action"=>"qq"),
					array("name"=>"提现手续费","module"=>"UserCarry","action"=>"config"),
					array("name"=>"提现银行设置","module"=>"Bank","action"=>"index"),
					array("name"=>"认证类型设置","module"=>"Credit","action"=>"index"),
				),
			),
			
			"dealconfig"	=>	array(
				"name"	=>	"贷款设置", 
				"key"	=>	"dealconfig", 
				"nodes"	=>	array( 
					array("name"=>"贷款分类设置","module"=>"DealCate","action"=>"index"),
					array("name"=>"分类回收站","module"=>"DealCate","action"=>"trash"),
					array("name"=>"贷款类型设置","module"=>"DealLoanType","action"=>"index"),
					array("name"=>"类型回收站","module"=>"DealLoanType","action"=>"trash"),
					array("name"=>"贷款城市设置","module"=>"City","action"=>"index"),
					array("name"=>"城市回收站","module"=>"City","action"=>"trash"),
					array("name"=>"合同范本设置","module"=>"Contract","action"=>"index"),
					array("name"=>"范本回收站","module"=>"Contract","action"=>"trash"),
				),
			),
			
			/*"debitconfig"	=>	array(
				"name"	=>	"白条设置", 
				"key"	=>	"debitconfig", 
				"nodes"	=>	array( 
					array("name"=>"白条设置","module"=>"Debit","action"=>"index"),
				),
			),
			"dealshow"	=>	array(
					"name"	=>	"展示订单",
					"key"	=>	"message",
					"nodes"	=>	array(
							array("name"=>"首页展示订单","module"=>"DealShow","action"=>"index"),
					),
			),
			*/
			"interface"	=>	array(
					"name"	=>	"接口设置",
					"key"	=>	"interface",
					"nodes"	=>	array(
							//array("name"=>"资金托管配置","module"=>"Conf","action"=>"money_index"),
							array("name"=>"资金托管","module"=>"Collocation","action"=>"index"),
							array("name"=>"支付接口设置","module"=>"Payment","action"=>"index"),
							array("name"=>"会员第三方登录","module"=>"ApiLogin","action"=>"index"),
							array("name"=>"会员整合插件","module"=>"Integrate","action"=>"index"),
					),
			),
			
			"mobile"	=>	array(
				"name"	=>	"移动平台设置", 
				"key"	=>	"mobile", 
				"nodes"	=>	array( 
					array("name"=>"手机端配置","module"=>"Conf","action"=>"mobile"),
					array("name"=>"手机端广告列表","module"=>"MAdv","action"=>"index"),
				),
			),		
			"admin"	=>	array(
				"name"	=>	"系统管理员", 
				"key"	=>	"admin", 
				"nodes"	=>	array( 
					array("name"=>"角色管理","module"=>"Role","action"=>"index"),
					array("name"=>"角色回收站","module"=>"Role","action"=>"trash"),
					array("name"=>"管理员管理","module"=>"Admin","action"=>"index"),
				),
			),
			"datebase"	=>	array(
				"name"	=>	"数据库", 
				"key"	=>	"datebase", 
				"nodes"	=>	array( 
					array("name"=>"数据库备份","module"=>"Database","action"=>"index"),
					array("name"=>"SQL操作","module"=>"Database","action"=>"sql"),
				),
			),
			
		),
	),
	
	/*
	"Crowd"	=>	array(
			"name"	=>	"众筹管理",
			"key"	=>	"Crowd",
			"groups"	=>	array(
					"Crowd_manage"	=>	array(
							"name"	=>	"项目管理",
							"key"	=>	"Crowd_manage",
							"nodes"	=>	array(
									array("name"=>"分类列表","module"=>"CrowdCate","action"=>"index"),
									array("name"=>"上线项目列表","module"=>"Crowd","action"=>"online_index"),	
									array("name"=>"未审核项目","module"=>"Crowd","action"=>"submit_index"),						
									),
					),
					"Crowd_order"	=>	array(
							"name"	=>	"项目支持",
							"key"	=>	"Crowd_order",
							"nodes"	=>	array(
									array("name"=>"项目支持","module"=>"CrowdOrder","action"=>"index"),
 									),
					),
	
			),
			
	),	
*/
		
);
?>