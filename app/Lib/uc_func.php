<?php
// +----------------------------------------------------------------------
// | Fanwe 方维p2p借贷系统
// +----------------------------------------------------------------------
// | Copyright (c) 2011 http://www.fanwe.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 云淡风轻(88522820@qq.com)
// +----------------------------------------------------------------------
	//查询会员日志
	function get_user_log($limit,$user_id,$t='')
	{
		if(!in_array($t,array("money","score","point")))
		{
			$t = "";
		}
		if($t=='')
		{
			$condition = "";
		}
		else
		{
			$condition = " and ".$t." <> 0 ";
		}
	
		$user_id = intval($user_id);
		$count = $GLOBALS['db']->getOne("select count(*) from ".DB_PREFIX."user_log where user_id = ".$user_id." $condition");
		$list = array();
		if($count > 0)
			$list = $GLOBALS['db']->getAll("select * from ".DB_PREFIX."user_log where user_id = ".$user_id." $condition order by log_time desc limit ".$limit);
		return array("list"=>$list,'count'=>$count);
	}
	
	/**
	 * 会员资金日志
	 * $limit 数量
	 * $user_id 用户id
	 * $status -1全部
	 * $condition 其他条件
	 */
	function get_user_money_log($limit,$user_id,$type=-1,$condition){
		$extWhere = "";
		if($type >= 0){
			$extWhere.=" AND `type`=".$type;
		}
			
		$user_id = intval($user_id);
		$count = $GLOBALS['db']->getOne("select count(*) from ".DB_PREFIX."user_money_log where user_id = ".$user_id." $extWhere $condition");
		$list = array();
		if($count > 0){
			
			$list = $GLOBALS['db']->getAll("select * from ".DB_PREFIX."user_money_log where user_id = ".$user_id." $extWhere $condition order by id desc limit ".$limit);
		}
		return array("list"=>$list,'count'=>$count);
	}
	
	/**
	 * 会员冻结资金日志
	 * $limit 数量
	 * $user_id 用户id
	 * $status -1全部
	 * $condition 其他条件
	 */
	function get_user_lock_money_log($limit,$user_id,$type=-1,$condition){
		$extWhere = "";
		if($type >= 0){
			$extWhere.=" AND `type`=".$type;
		}
			
		$user_id = intval($user_id);
		$count = $GLOBALS['db']->getOne("select count(*) from ".DB_PREFIX."user_lock_money_log where user_id = ".$user_id." $extWhere $condition");
		$list = array();
		if($count > 0){
			
			$list = $GLOBALS['db']->getAll("select * from ".DB_PREFIX."user_lock_money_log where user_id = ".$user_id." $extWhere $condition order by id desc limit ".$limit);
		}
		return array("list"=>$list,'count'=>$count);
	}
	
	/**
	 * 会员信用积分日志
	 * $limit 数量
	 * $user_id 用户id
	 * $status -1全部
	 * $condition 其他条件
	 */
	function get_user_point_log($limit,$user_id,$type=-1,$condition){
		$extWhere = '';
		if($type >= 0){
			$extWhere.=" AND `type`=".$type;
		}
		$user_id = intval($user_id);
		$count = $GLOBALS['db']->getOne("select count(*) from ".DB_PREFIX."user_point_log where user_id = ".$user_id." $extWhere  $condition");
		$list = array();
		if($count > 0)
			$list = $GLOBALS['db']->getAll("select * from ".DB_PREFIX."user_point_log where user_id = ".$user_id." $extWhere  $condition order by id DESC limit ".$limit);
		return array("list"=>$list,'count'=>$count);
	}
	
	/**
	 * 会员积分日志
	 * $limit 数量
	 * $user_id 用户id
	 * $status -1全部
	 * $condition 其他条件
	 */
	function get_user_score_log($limit,$user_id,$type=-1,$condition){
		$extWhere = '';
		if($type >= 0){
			$extWhere.=" AND `type`=".$type;
		}
		$user_id = intval($user_id);
		$count = $GLOBALS['db']->getOne("select count(*) from ".DB_PREFIX."user_score_log where user_id = ".$user_id." $extWhere  $condition");
		$list = array();
		if($count > 0)
			$list = $GLOBALS['db']->getAll("select * from ".DB_PREFIX."user_score_log where user_id = ".$user_id." $extWhere  $condition order by id DESC limit ".$limit);
		return array("list"=>$list,'count'=>$count);
	}
	
	/**
	 * 不可提现资金日志
	 * $limit 数量
	 * $user_id 用户id
	 * $status -1全部
	 * $condition 其他条件
	 */
	function get_user_nmc_amount_log($limit,$user_id,$type=-1,$condition){
		$extWhere = '';
		if($type >= 0){
			$extWhere.=" AND `type`=".$type;
		}
		$user_id = intval($user_id);
		$count = $GLOBALS['db']->getOne("select count(*) from ".DB_PREFIX."user_money_log where user_id = ".$user_id." $extWhere AND (`type`= 22 OR `type`= 28 OR `type`= 29)  $condition");
		$list = array();
		if($count > 0)
			$list = $GLOBALS['db']->getAll("select * from ".DB_PREFIX."user_money_log where user_id = ".$user_id." $extWhere AND (`type`= 22 OR `type`= 28 OR `type`= 29)  $condition order by id DESC limit ".$limit);
		return array("list"=>$list,'count'=>$count);
	}
	
	//查询会员充值订单
	function get_user_incharge($limit,$user_id)
	{
		$user_id = intval($user_id);
		$count = $GLOBALS['db']->getOne("select count(*) from ".DB_PREFIX."deal_order where user_id = ".$user_id." and type = 1 and is_delete = 0");
		$list = array();
		if($count > 0){
			$list = $GLOBALS['db']->getAll("select * from ".DB_PREFIX."deal_order where user_id = ".$user_id." and type = 1 and is_delete = 0 order by create_time desc limit ".$limit);
		
			foreach($list as $k=>$v)
			{
				$list[$k]['payment_notice'] = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."payment_notice where order_id = ".$v['id']);
				$list[$k]['payment'] = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."payment where id = ".$v['payment_id']);
			}
		}
		return array("list"=>$list,'count'=>$count);
	}
	
	//new查询会员充值订单日志
	function get_user_incharge_log($limit,$user_id,$condition)
	{
		$user_id = intval($user_id);
		$count = $GLOBALS['db']->getOne("select count(*) from ".DB_PREFIX."payment_notice pn left join ".DB_PREFIX."payment p on pn.payment_id = p.id where  pn.user_id = ".$user_id."  $condition ");
		
		$list = array();
		if($count > 0){
			$list = $GLOBALS['db']->getAll("select pn.*,name from ".DB_PREFIX."payment_notice pn left join ".DB_PREFIX."payment p on pn.payment_id = p.id  where pn.user_id = ".$user_id."  $condition  order by pay_time desc limit ".$limit);
			foreach($list as $k=>$v)
			{
				if($list[$k]['is_paid'] == 1){
					$list[$k]['is_paid_format']="已支付";
				}else{
					$list[$k]['is_paid_format']="未支付";
				}
				$list[$k]['create_time_format'] = to_date($list[$k]['create_time'],"Y-m-d");
				$list[$k]['pay_time_format'] = to_date($list[$k]['pay_time'],"Y-m-d");
				$list[$k]['money_format'] = format_price($list[$k]['money']);
				
			}
		}
		return array("list"=>$list,'count'=>$count);
	}
	
	//查询会员提现记录
	function get_user_carry($limit,$user_id)
	{
		$user_id = intval($user_id);
		$count = $GLOBALS['db']->getOne("select count(*) from ".DB_PREFIX."user_carry where user_id = ".$user_id." ");
		$list = array();
		if($count > 0){
			$list = $GLOBALS['db']->getAll("select uc.*,b.name as bank_name from ".DB_PREFIX."user_carry uc LEFT JOIN ".DB_PREFIX."bank b ON b.id=uc.bank_id  where user_id = ".$user_id." order by create_time desc limit ".$limit);

			foreach($list as $k=>$v)
			{
				if($v['status']==0){
					$list[$k]['status_format'] = "待处理";
				}
				elseif($v['status']==1){
					$list[$k]['status_format'] = "提现成功";
				}
				elseif($v['status']==2){
					$list[$k]['status_format'] = "提现失败";
				}
				elseif($v['status']==3){
					$list[$k]['status_format'] = "待付款";
				}
				elseif($v['status']==4){
					$list[$k]['status_format'] = "已撤销";
				}
			}
		}
		return array("list"=>$list,'count'=>$count);
	}
	
	//查询会员的团购券
	function get_user_coupon($limit,$user_id,$status=0)
	{
		$user_id = intval($user_id);
		$ext_condition = '';
		if($status==1)
		{
			$ext_condition = " and confirm_time = 0 ";
		}
		if($status==2)
		{
			$ext_condition = " and confirm_time <> 0 ";
		}
		
		$count = $GLOBALS['db']->getOne("select count(*) from ".DB_PREFIX."deal_coupon where user_id = ".$user_id." and is_delete = 0 and is_valid = 1 ".$ext_condition);
		$list = array();
		if($count > 0){
			$list = $GLOBALS['db']->getAll("select * from ".DB_PREFIX."deal_coupon where user_id = ".$user_id." and is_delete = 0 and is_valid = 1 ".$ext_condition." order by order_id desc limit ".$limit);
		
			foreach($list as $k=>$v)
			{
				if($GLOBALS['db']->getOne("select forbid_sms from ".DB_PREFIX."deal where id = ".$v['deal_id'])==1)
				{
					//禁止发券时，将已发数改为上限
					$list[$k]['sms_count'] = app_conf("SMS_COUPON_LIMIT");
				}
				$list[$k]['deal_item'] = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."deal_order_item where id = ".$v['order_deal_id']);
			}
		}
		
		return array("list"=>$list,'count'=>$count);		
	}
	
	
	//查询会员订单
	function get_user_order($limit,$user_id)
	{
		$user_id = intval($user_id);
		$count = $GLOBALS['db']->getOne("select count(*) from ".DB_PREFIX."deal_order where user_id = ".$user_id." and type = 0 and is_delete = 0");
		$list = array();
		if($count >0){
			$list = $GLOBALS['db']->getAll("select * from ".DB_PREFIX."deal_order where user_id = ".$user_id." and type = 0 and is_delete = 0 order by create_time desc limit ".$limit);
			
			foreach($list as $k=>$v)
			{
				$list[$k]['payment_notice'] = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."payment_notice where order_id = ".$v['id']);
			}
		}
		return array("list"=>$list,'count'=>$count);
	}
	
	//查询会员抽奖
	function get_user_lottery($limit,$user_id)
	{
		$user_id = intval($user_id);
		$count = $GLOBALS['db']->getOne("select count(*) from ".DB_PREFIX."lottery where user_id = ".$user_id);
		$list = array();
		if($count > 0){
			$list = $GLOBALS['db']->getAll("select * from ".DB_PREFIX."lottery where user_id = ".$user_id." order by create_time desc limit ".$limit);
			
			foreach($list as $k=>$v)
			{
				$list[$k]['deal_name'] = $GLOBALS['db']->getOne("select name from ".DB_PREFIX."deal where id = ".$v['deal_id']);
				$list[$k]['deal_sub_name'] = $GLOBALS['db']->getOne("select sub_name from ".DB_PREFIX."deal where id = ".$v['deal_id']);
				if($v['buyer_id']==0)
				{
					$buyer = $GLOBALS['db']->getOne("select user_name from ".DB_PREFIX."user where id = ".$v['user_id']);
				}
				else
				{
					$buyer = $GLOBALS['db']->getOne("select user_name from ".DB_PREFIX."user where id = ".$v['buyer_id']);
				}
				$list[$k]['buyer'] = $buyer;
			}	
		}
		
		return array("list"=>$list,'count'=>$count);
	}
	
	/**
	 * 查询会员邀请及返利列表
	 * $type 0有效推荐 1无效推荐
	 */
	function get_invite_list($limit,$user_id,$type=0)
	{
		$user_id = intval($user_id);
		
		$condition = " AND dl.is_has_loans = 1 AND u.user_type in(0,1) ";
		if($type==0){
			if(intval(app_conf("INVITE_REFERRALS_DATE")) > 0){
				$after_year =  next_replay_month(to_timespan(to_date(TIME_UTC,"Y-m-d")),-intval(app_conf("INVITE_REFERRALS_DATE")));
				$condition =" AND u.create_time >= ".$after_year." and dl.create_time  > ".$after_year."  AND dl.user_id > 0 and dl.id > 0";
			}
			else{
				$condition =" AND dl.user_id > 0 and dl.id > 0";
			}
		}
		else
		{
			if(intval(app_conf("INVITE_REFERRALS_DATE")) > 0){
				$after_year =  next_replay_month(to_timespan(to_date(TIME_UTC,"Y-m-d")),-intval(app_conf("INVITE_REFERRALS_DATE")));
				$condition =" AND (u.create_time < ".$after_year." OR dlr.user_id is null ) and (dlr.id is null OR dlr.id = 0) ";
			}
			else{
				$condition =" AND (dlr.user_id is null ) and (dlr.id is null OR dlr.id = 0)";
			}
		}
		
		$sql_count = "select count(DISTINCT u.id) from ".DB_PREFIX."user u " .
				"LEFT JOIN ".DB_PREFIX."deal_load_repay dlr ON (if(dlr.t_user_id > 0,dlr.t_user_id,dlr.user_id))=u.id  " .
				"LEFT JOIN ".DB_PREFIX."deal_load dl ON dl.id=dlr.load_id  " .
				"where u.pid = ".$user_id." $condition ";
		
		$count = $GLOBALS['db']->getOne($sql_count);
		$list = array();
		if($count > 0){
			$sql = "select u.*,dl.user_id as r_user_id,dl.create_time as r_create_time FROM ".DB_PREFIX."user u " .
					"LEFT JOIN ".DB_PREFIX."deal_load_repay dlr ON (if(dlr.t_user_id > 0,dlr.t_user_id,dlr.user_id))=u.id  " .
					"LEFT JOIN ".DB_PREFIX."deal_load dl ON dl.id=dlr.load_id  " .
					"where u.pid = ".$user_id." $condition group by u.id order by id DESC limit ".$limit;
			$list = $GLOBALS['db']->getAll($sql);
			foreach($list as $k=>$v){
				if(intval($v['r_user_id'])==0){
					$list[$k]['result'] = "未投资";
				}
				elseif(intval($v['create_time'])<$after_year){
					$list[$k]['result'] = "过期";
				}
			}
		}
		
		return array("list"=>$list,'count'=>$count);
	}
	
	/**
	 * 授信额度申请
	 */
	function get_deal_quota_list($limit,$user_id,$condition)
	{
		$user_id = intval($user_id);
		$sql = "select * from ".DB_PREFIX."deal_quota_submit  where user_id = ".$user_id." $condition order by id desc limit ".$limit;
		$sql_count = "select count(*) from ".DB_PREFIX."deal_quota_submit  where user_id = ".$user_id." $condition ";
		$count = $GLOBALS['db']->getOne($sql_count);
		$list = array();
		if($count > 0){
			$list = $GLOBALS['db']->getAll($sql);
			foreach($list as $k=>$v){
				if($list[$k]['borrow_amount'] < 100)
					$list[$k]['borrow_amount_format'] = format_price($list[$k]['borrow_amount']);
				else
					$list[$k]['borrow_amount_format'] = format_price($list[$k]['borrow_amount']/10000)."万";
				
				 $list[$k]['create_time_format'] = to_date($v['create_time'],'Y-m-d H:i');
				 $list[$k]['update_time_format'] = to_date($v['update_time'],'Y-m-d H:i');
				 if($v['status']==0)
				 	$list[$k]['status_format'] = "未审核";
				 elseif($v['status']==1)
				 	$list[$k]['status_format'] = "已通过";
				 elseif($v['status']==2)
					$list[$k]['status_format'] = "未通过";
			}
		}
		
		return array("list"=>$list,'count'=>$count);
	}
	//查询信用值申请列表
	function get_quota_list($limit,$user_id,$condition)
	{	
		$user_id = intval($user_id);
		$sql = "select * from ".DB_PREFIX."quota_submit  where user_id = ".$user_id." $condition order by id desc limit ".$limit;
		$sql_count = "select count(*) from ".DB_PREFIX."quota_submit  where user_id = ".$user_id." $condition ";
		$count = $GLOBALS['db']->getOne($sql_count);
		$list = array();
		if($count > 0){
			$list = $GLOBALS['db']->getAll($sql);
			foreach($list as $k=>$v){
				$list[$k]['create_time_format'] = to_date($v['create_time'],'Y-m-d H:i');
				$list[$k]['op_time_format'] = to_date($v['op_time'],'Y-m-d H:i');
				if($v['status']==0)
				 	$list[$k]['status_format'] = "未审核";
				elseif($v['status']==1)
				 	$list[$k]['status_format'] = "已通过";
				elseif($v['status']==2)
					$list[$k]['status_format'] = "未通过";
			}
		}
			
		
		return array("list"=>$list,'count'=>$count);
	}
	
	
	//查询代金券列表
	function get_voucher_list($limit,$user_id)
	{
		$user_id = intval($user_id);
		$sql = "select * from ".DB_PREFIX."ecv as e left join ".DB_PREFIX."ecv_type as et on e.ecv_type_id = et.id where e.user_id = ".$user_id." order by e.id desc limit ".$limit;
		$sql_count = "select count(*) from ".DB_PREFIX."ecv where user_id = ".$user_id;
		$count = $GLOBALS['db']->getOne($sql_count);
		$list = array();
		if($count > 0)
			$list = $GLOBALS['db']->getAll($sql);
		
		return array("list"=>$list,'count'=>$count);
	}
	
	//查询可兑换代金券列表
	function get_exchange_voucher_list($limit)
	{
		$sql = "select * from ".DB_PREFIX."ecv_type where send_type = 1 order by id desc limit ".$limit;
		$sql_count = "select count(*) from ".DB_PREFIX."ecv_type where send_type = 1";
		$count = $GLOBALS['db']->getOne($sql_count);
		$list = array();
		if($count > 0)
			$list = $GLOBALS['db']->getAll($sql);
		
		return array("list"=>$list,'count'=>$count);
	}
	
	function get_collect_list($limit,$user_id)
	{
		$user_id = intval($user_id);
		$sql_count = "select count(*) from ".DB_PREFIX."deal_collect where user_id = ".$user_id;
		$count = $GLOBALS['db']->getOne($sql_count);
		$list = array();
		if($count > 0){
			$sql = "select d.*,c.create_time as add_time ,c.id as cid,u.user_name,u.level_id,u.province_id,u.city_id from ".DB_PREFIX."deal_collect as c left join ".DB_PREFIX."deal as d on d.id = c.deal_id LEFT JOIN ".DB_PREFIX."user u ON u.id= d.user_id where d.is_delete=0 and d.publish_wait = 0 and c.user_id = ".$user_id." order by c.create_time desc limit ".$limit;
			$list = $GLOBALS['db']->getAll($sql);
			foreach($list as $k=>$v){
				$list[$k]['borrow_amount_format'] = format_price($v['borrow_amount']);
				$list[$k]['borrow_amount_format'] = format_price($v['borrow_amount']/10000)."万";//format_price($deal['borrow_amount']);
				$list[$k]['rate_foramt_w'] = number_format($v['rate'],2)."%";
				
				$list[$k]['rate_foramt'] = number_format($v['rate'],2);
				//本息还款金额
				$list[$k]['month_repay_money'] = format_price(pl_it_formula($v['borrow_amount'],$v['rate']/12/100,$v['repay_time']));
				//还需多少钱
				$list[$k]['need_money'] = format_price($v['borrow_amount'] - $v['load_money']);
				//百分比
				if($v['deal_status']==4){
					$list[$k]['month_repay_money'] = pl_it_formula($v['borrow_amount'],$v['rate']/12/100,$v['repay_time']);
					$list[$k]['remain_repay_money'] = $list[$k]['month_repay_money'] * $v['repay_time'];
					$list[$k]['progress_point'] =  round($v['repay_money']/$list[$k]['remain_repay_money']*100,2);
				}else{
					$list[$k]['progress_point'] = $v['load_money']/$v['borrow_amount']*100;
				}
				
				$user_location = $GLOBALS['db']->getOne("select name from ".DB_PREFIX."region_conf where id = ".intval($v['city_id']),false);
				if($user_location=='')
					$user_location = $GLOBALS['db']->getOne("select name from ".DB_PREFIX."region_conf where id = ".intval($v['province_id']),false);
			
				$list[$k]['user_location'] = $user_location;
				$list[$k]['point_level'] = $GLOBALS['db']->getOne("select name from ".DB_PREFIX."user_level where id = ".intval($v['level_id']));
				
				$durl = "/index.php?ctl=deal&act=mobile&is_sj=1&id=".$v['id'];
				$list[$k]['app_url'] = str_replace("/mapi", "", SITE_DOMAIN.$durl);
			}
		}
		
		return array("list"=>$list,'count'=>$count);
	}
	
	function getInvestList($mode = "index", $user_id = 0, $page = 0,$user_name='',$user_pwd='') {
				
		if ($user_id > 0){
			$condtion = "   AND d.deal_status in(1,2,3,4,5)  ";
			switch($mode){
				case "index" :
					$condtion = "   AND d.deal_status in(1,2,3,4,5)  ";
					break;
				case "invite" :
					$condtion = "   AND d.deal_status in(1,2)  ";
					break;
				case "in" :
					$condtion = "   AND d.deal_status =1  ";
					break;
				case "full" :
					$condtion = "   AND d.deal_status =2  ";
					break;
				case "flow" :
					$condtion = "   AND d.deal_status =3  ";
					break;
				case "ing" :
					$condtion = "   AND d.deal_status =4  ";
					break;
				case "over" :
					$condtion = "   AND d.deal_status =5  ";
					break;
				case "bad" :
					$condtion = "   AND d.deal_status = 4 AND (".TIME_UTC." - d.last_repay_time)/24/3600 >=".trim(app_conf("YZ_IMPSE_DAY"))." and d.last_repay_time > 0 ";
					break;
			}
		
			if($page==0)
				$page = 1;
			$limit = (($page-1)*app_conf("PAGE_SIZE")).",".app_conf("PAGE_SIZE");
			 
			//$sql = "select d.*,u.user_name,dl.money as u_load_money,u.level_id,u.province_id,u.city_id,dl.id as load_id from ".DB_PREFIX."deal d left join ".DB_PREFIX."deal_load as dl on d.id = dl.deal_id LEFT JOIN ".DB_PREFIX."user u ON u.id=d.user_id where dl.user_id = ".$user_id." $condtion group by dl.id order by dl.create_time desc limit ".$limit;
			//$sql_count = "select count(DISTINCT dl.id) from ".DB_PREFIX."deal d left join ".DB_PREFIX."deal_load as dl on d.id = dl.deal_id where d.is_delete=0 and d.publish_wait = 0 and dl.user_id = ".$user_id." $condtion ";
			
			$sql = "select dlt.t_user_id,d.*,u.user_name,dl.money as u_load_money,u.level_id,u.province_id,u.city_id,dl.id as load_id,dl.rebate_money from ".DB_PREFIX."deal d left join ".DB_PREFIX."deal_load as dl on d.id = dl.deal_id LEFT JOIN ".DB_PREFIX."deal_load_transfer dlt on dlt.load_id = dl.id LEFT JOIN ".DB_PREFIX."user u ON u.id=d.user_id where (dl.user_id = ".$user_id." or dlt.t_user_id =".$user_id.")  $condtion group by dl.id order by dl.create_time desc limit ".$limit;

			$sql_count = "select count(DISTINCT dl.id) from ".DB_PREFIX."deal d left join ".DB_PREFIX."deal_load as dl on d.id = dl.deal_id LEFT JOIN ".DB_PREFIX."deal_load_transfer dlt on dlt.load_id = dl.id where d.is_delete=0 and d.publish_wait = 0 and (dl.user_id = ".$user_id." or dlt.t_user_id =".$user_id.")  $condtion ";
			
			$count = $GLOBALS['db']->getOne($sql_count);
			$list = array();
			if($count >0){
				$list = $GLOBALS['db']->getAll($sql);
				$load_ids = array();
				foreach($list as $k=>$v){
					$list[$k]['borrow_amount_format'] = format_price($v['borrow_amount']/10000)."万";//format_price($deal['borrow_amount']);
					$list[$k]['rate_foramt_w'] = number_format($v['rate'],2)."%";
					
					//$list[$k]['borrow_amount_format'] = format_price($v['borrow_amount']);					
					$list[$k]['rate_foramt'] = number_format($v['rate'],2);
					
					//本息还款金额
					$list[$k]['month_repay_money'] = pl_it_formula($v['borrow_amount'],$v['rate']/12/100,$v['repay_time']);
					$list[$k]['month_repay_money_format'] =  format_price($list[$k]['month_repay_money']);
					
					if($list[$k]['create_time'] !=""){
						$list[$k]['create_time_format'] =  to_date($list[$k]['create_time'],"Y-m-d");
					}
						
					if($v['deal_status'] == 1){
						//还需多少钱
						$list[$k]['need_money'] = format_price($v['borrow_amount'] - $v['load_money']);
			
						//百分比
						$list[$k]['progress_point'] = $v['load_money']/$v['borrow_amount']*100;
			
					}
					elseif($v['deal_status'] == 2 || $v['deal_status'] == 5)
					{
						$list[$k]['progress_point'] = 100;
					}
					elseif($v['deal_status'] == 4){
						//百分比
						$list[$k]['remain_repay_money'] = $list[$k]['month_repay_money'] * $v['repay_time'];
						//还有多少需要还
						$list[$k]['need_remain_repay_money'] = $list[$k]['remain_repay_money'] - $v['repay_money'];
						//还款进度条
						$list[$k]['progress_point'] =  round($v['repay_money']/$list[$k]['remain_repay_money']*100,2);
					}
						
					$user_location = $GLOBALS['db']->getOne("select name from ".DB_PREFIX."region_conf where id = ".intval($v['city_id']),false);
					if($user_location=='')
						$user_location = $GLOBALS['db']->getOne("select name from ".DB_PREFIX."region_conf where id = ".intval($v['province_id']),false);
			
					$list[$k]['user_location'] = $user_location;
					$list[$k]['point_level'] = $GLOBALS['db']->getOne("select name from ".DB_PREFIX."user_level where id = ".intval($v['level_id']));
					
					
					//$durl = url("index","deal",array("id"=>$list[$k]['id']));
					//$deal['url'] = $durl;
					if($v['deal_status'] == 4 || $v['deal_status'] == 5){
						$durl = "/index.php?ctl=uc_invest&act=mrefdetail&is_sj=1&id=".$v['id']."&load_id=".$v['load_id']."&user_name=".$user_name."&user_pwd=".$user_pwd;					
						$list[$k]['app_url'] = str_replace("/mapi", "", SITE_DOMAIN.$durl);
					}else{
						$durl = "/index.php?ctl=deal&act=mobile&is_sj=1&id=".$v['id'];
						$list[$k]['app_url'] = str_replace("/mapi", "", SITE_DOMAIN.$durl);
					}
					$load_ids[] = $v['load_id'];
				}
				//判断是否已经转让
				if(count($load_ids) > 0){
					$tmptransfer_list  = $GLOBALS['db']->getAll("SELECT * FROM ".DB_PREFIX."deal_load_transfer where load_id in(".implode(",",$load_ids).") and t_user_id > 0 and user_id=".$user_id);
					$transfer_list = array();
					foreach($tmptransfer_list as $k=>$v){
						$transfer_list[$v['load_id']] = $v;
					}
					unset($tmptransfer_list);
					foreach($list as $k=>$v){
						if(isset($transfer_list[$v['load_id']])){
							$list[$k]['has_transfer'] = 1;
						}
					}
				}
				
			}
		
		
			return array('list'=>$list,'count'=>$count);
		}else{
			return array();
		}
	
	}	
	
	function getUcTransferList($page,$status){
		if($page==0)
			$page = 1;
			
		$limit = (($page-1)*app_conf("PAGE_SIZE")).",".app_conf("PAGE_SIZE");
	
		$condition = ' and d.is_effect=1 and d.is_delete=0  and d.repay_time_type =1 and  d.publish_wait=0 and dl.user_id='.$GLOBALS['user_info']['id']."  ";
		$union_sql = " LEFT JOIN ".DB_PREFIX."deal_load_transfer dlt ON dlt.deal_id = dl.deal_id and dlt.load_id=dl.id ";
		switch($status){
			case 1://可转让
				$condition.= " AND d.next_repay_time - ".TIME_UTC." + 24*3600 - 1 > 0 AND d.deal_status = 4 and (isnull(dlt.id) or (dlt.t_user_id =0 and dlt.status = 0) ) ";
				break;
			case 2://转让中
				$condition.=" AND d.deal_status = 4 AND dlt.status = 1 and dlt.user_id >0 and dlt.t_user_id=0 ";
				break;
			case 3://已转让
				$condition.=" AND dlt.t_user_id > 0 ";
				break;
			case 4://已撤销
				$condition.=" AND dlt.status = 0 ";
				break;
			default ://默认
				$condition.=" AND ((d.deal_status = 4 and dlt.id > 0) or (d.deal_status = 4 and isnull(dlt.id) AND d.next_repay_time - ".TIME_UTC." + 24*3600 - 1 > 0)  or (d.deal_status = 5 and dlt.id >0))";
				break;
		}
	
		$count_sql = 'SELECT count(dl.id) FROM '.DB_PREFIX.'deal_load dl LEFT JOIN '.DB_PREFIX.'deal d ON d.id = dl.deal_id '.$union_sql.' WHERE 1=1 '.$condition;
	
		$rs_count = $GLOBALS['db']->getOne($count_sql);
		if($rs_count > 0){
			$list_sql = 'SELECT dl.id as dlid,d.*,dl.money as load_money,dlt.id as dltid,dlt.status as tras_status,dlt.t_user_id,dlt.transfer_amount,dlt.create_time as tras_create_time FROM '.DB_PREFIX.'deal_load dl LEFT JOIN '.DB_PREFIX.'deal d ON d.id = dl.deal_id '.$union_sql.' WHERE 1=1 '.$condition.' ORDER BY d.id DESC';
	
			$list = $GLOBALS['db']->getAll($list_sql." LIMIT $limit ");
			foreach($list as $k => $v){
				//最后还款日
				$list[$k]['final_repay_time'] = next_replay_month($v['repay_start_time'],$v['repay_time']);
					
				$list[$k]['final_repay_time_format'] = to_date($list[$k]['final_repay_time'],"Y-m-d");
				//剩余期数
				if($v['deal_status']==4){
					if(intval($v['last_repay_time']) > 0)
						$list[$k]['how_much_month'] = how_much_month($v['last_repay_time'],$list[$k]['final_repay_time']);
					else{
						$list[$k]['how_much_month'] = how_much_month($v['repay_start_time'],$list[$k]['final_repay_time']);
					}
				}
				else{
					$list[$k]['how_much_month'] = 0;
				}
				
				$transfer_rs = deal_transfer($list[$k]);
				$list[$k]['month_repay_money'] = $transfer_rs['month_repay_money'];
				$list[$k]['all_must_repay_money'] = $transfer_rs['all_must_repay_money'];
				$list[$k]['left_benjin'] = round($transfer_rs['left_benjin'],2);
				
					
				$list[$k]['left_benjin_format'] = format_price($list[$k]['left_benjin']);
				//剩多少利息
				$list[$k]['left_lixi'] = $list[$k]['all_must_repay_money'] - $list[$k]['left_benjin'];
				$list[$k]['left_lixi_format'] = format_price($list[$k]['left_lixi']);
	
				//转让价格
				$list[$k]['transfer_amount_format'] =  format_price($v['transfer_amount']/10000)."万";
	
				if($v['tras_create_time'] !=""){
					$list[$k]['tras_create_time_format'] =  to_date($v['tras_create_time'],"Y-m-d");
				}
				
				$list[$k]['near_repay_time_format'] =  to_date($v['near_repay_time'],"Y-m-d");
				
				if ($list[$k]['tras_status'] == '')
					$list[$k]['tras_status_format'] = '可转让';
				else if ($list[$k]['tras_status'] == 0)
					$list[$k]['tras_status_format'] = '已撤销';
				else if ($list[$k]['tras_status'] == 1){
					if ($list[$k]['t_user_id'] > 0){
						$list[$k]['tras_status_format'] = '已转让';
					}else{
						$list[$k]['tras_status_format'] = '转让中';
					}					
				}
				
				$list[$k]['tras_status_op'] = 0;
				
				if ($list[$k]['tras_status'] == '')
					$list[$k]['tras_status_op'] = 1;//'转让';//<a href="javascript:void(0);" class="J_do_transfer" dataid="{$item.dlid}">转让</a>
				else if ($list[$k]['tras_status'] == 0){
					if ($list[$k]['how_much_month'] == 0)
						$list[$k]['tras_status_op'] = 2;//'还款完毕,无法转让';
					else{
						if ($list[$k]['next_repay_time'] +24*3600-1 - TIME_UTC < 0)
							$list[$k]['tras_status_op'] = 3;//'逾期还款,无法转让';
						else
							$list[$k]['tras_status_op'] = 4;//'重转让';//<a href="javascript:void(0);" class="J_do_transfer" dataid="{$item.dlid}">重转让</a>
					}
				}
				else if ($list[$k]['tras_status'] == 1){
					if ($list[$k]['t_user_id'] > 0){
						$list[$k]['tras_status_op'] = 5;//'查看详情<br>转让协议';
						//<a href="{url x="index" r="transfer#detail" p="id=$item.dltid"}">查看详情</a><br>
						//<a href="javascript:void(0);" onclick="javascript:window.showModalDialog('{url x="index" r="uc_transfer#contact" p="id=$item.dltid"}');">转让协议</a>
					}else
						$list[$k]['tras_status_op'] = 6;//'撤销';//<a href="javascript:void(0);"  class="J_do_reback" dataid="{$item.dltid}">撤销</a>					
				}
				
				$durl = "/index.php?ctl=deal&act=mobile&is_sj=1&id=".$v['id'];				
				$list[$k]['app_url'] = str_replace("/mapi", "", SITE_DOMAIN.$durl);
			}
				
			return array('list'=>$list,'count'=>$rs_count);
		}else{
			return array('list'=>null,'count'=>0);
		}
	}	
	
	//转让;
	function getUcToTransfer($id,$tid){	
			
		$status = array('status'=>0,'show_err'=>'','transfer');
		if($id==0){			
			$status['status'] = 0;
			$status['show_err'] = "不存在的债权";
			return $status;
		}
	
		//先执行更新借贷信息
		$deal_id = $GLOBALS['db']->getOne("SELECT deal_id FROM ".DB_PREFIX."deal_load WHERE id=".$id);
		if($deal_id==0){
			$status['status'] = 0;
			$status['show_err'] = "不存在的债权";
			return $status;
		}
		else{
			syn_deal_status($deal_id);
		}
		
		$condition = ' AND dl.id='.$id.' AND d.deal_status = 4 and d.is_effect=1 and d.is_delete=0  and d.repay_time_type =1 and  d.publish_wait=0 and dl.user_id='.$GLOBALS['user_info']['id']."  and d.next_repay_time - ".TIME_UTC." + 24*3600 - 1 > 0  ";
		if($tid > 0)
		{
			$condition.=" and dlt.id=$tid";
		}
		
		$union_sql = " LEFT JOIN ".DB_PREFIX."deal_load_transfer dlt ON dlt.deal_id = dl.deal_id and dlt.load_id=dl.id ";
	
		$sql = 'SELECT dl.id as dlid,d.*,dl.money as load_money,dlt.id as dltid,dlt.status as tras_status,dlt.t_user_id,dlt.transfer_amount,dlt.create_time as tras_create_time FROM '.DB_PREFIX.'deal_load dl LEFT JOIN '.DB_PREFIX.'deal d ON d.id = dl.deal_id '.$union_sql.' WHERE 1=1 '.$condition;
	
		$transfer = $GLOBALS['db']->getRow($sql);
	
		if($transfer){
			//下个还款日
			if(intval($transfer['next_repay_time']) > 0){
				$transfer['next_repay_time_format'] = to_date($transfer['next_repay_time'],"Y-m-d");
			}
			else{
				$transfer['next_repay_time_format'] = to_date(next_replay_month($transfer['repay_start_time']),"Y-m-d");
			}
				
				
			//还款日
			$transfer['final_repay_time'] = next_replay_month($transfer['repay_start_time'],$transfer['repay_time']);
			$transfer['final_repay_time_format'] = to_date($transfer['final_repay_time'],"Y-m-d");
			//剩余期数
			if(intval($transfer['last_repay_time']) > 0)
				$transfer['how_much_month'] = how_much_month($transfer['last_repay_time'],$transfer['final_repay_time']);
			else{
				$transfer['how_much_month'] = how_much_month($transfer['repay_start_time'],$transfer['final_repay_time']);
			}
			
			
			$transfer_rs = deal_transfer($transfer);
			$transfer['month_repay_money'] = $transfer_rs['month_repay_money'];
			$transfer['all_must_repay_money'] = $transfer_rs['all_must_repay_money'];
			$transfer['left_benjin'] = $transfer_rs['left_benjin'];
			
			$transfer['left_benjin_format'] = format_price($transfer['left_benjin']);
			//剩多少利息
			$transfer['left_lixi'] = $transfer['all_must_repay_money'] - $transfer['left_benjin'];
			$transfer['left_lixi_format'] = format_price($transfer['left_lixi']);
				
			//转让价格
			$transfer['transfer_amount_format'] =  format_price($transfer['all_must_repay_money']);
				
			if($transfer['tras_create_time'] !=""){
				$transfer['tras_create_time_format'] =  to_date($transfer['tras_create_time'],"Y-m-d");
			}
				
			$status['status'] = 1;
			$status['transfer'] = $transfer;
			$status['show_err'] = "";
			return $status;
		}
		else{			
			$status['status'] = 0;
			$status['show_err'] = "不存在的债权转让";
			return $status;
		}
	}
		
	/**
	 * 执行转让
	 */
	function getUcDoTransfer($id,$tid,$paypassword,$transfer_money){
		$paypassword = strim($paypassword);
		$id = intval($id);
		$tid = intval($tid);
		$transfer_money = floatval($transfer_money);
		
		$status = array('status'=>0,'show_err'=>'');
		if($id==0){
			$status['status'] = 0;
			$status['show_err'] = "不存在的债权";
			return $status;
		}
		
		if($transfer_money <= 0){
			$status['status'] = 0;
			$status['show_err'] = "转让金额必须大于0";
			return $status;
		}
				
		$deal_id = $GLOBALS['db']->getOne("SELECT deal_id FROM ".DB_PREFIX."deal_load WHERE id=".$id);
		if($deal_id==0){
			$status['status'] = 0;
			$status['show_err'] = "不存在的债权";
			return $status;
		}
		else{
			syn_deal_status($deal_id);
		}
	
		//判断支付密码是否正确
		if($paypassword ==""){			
			$status['status'] = 0;
			$status['show_err'] = $GLOBALS['lang']['PAYPASSWORD_EMPTY'];
			return $status;
		}
	
		if(md5($paypassword) != $GLOBALS['user_info']['paypassword']){			
			$status['status'] = 0;
			$status['show_err'] = $GLOBALS['lang']['PAYPASSWORD_ERROR'];
			return $status;
		}
	
		$condition = ' AND dl.id='.$id.' AND d.deal_status = 4 and d.is_effect=1 and d.is_delete=0 and d.repay_time_type =1 and  d.publish_wait=0 and dl.user_id='.$GLOBALS['user_info']['id']." and d.next_repay_time - ".TIME_UTC." + 24*3600 - 1 > 0  ";
		$union_sql = " LEFT JOIN ".DB_PREFIX."deal_load_transfer dlt ON dlt.deal_id = dl.deal_id and dlt.load_id=dl.id ";
	
		$sql = 'SELECT dl.id as dlid,d.*,dl.money as load_money,dlt.status as tras_status,dlt.t_user_id,dlt.transfer_amount,dlt.create_time as tras_create_time FROM '.DB_PREFIX.'deal_load dl LEFT JOIN '.DB_PREFIX.'deal d ON d.id = dl.deal_id '.$union_sql.' WHERE 1=1 '.$condition;
	
		$transfer = $GLOBALS['db']->getRow($sql);
	
		if($transfer){
	
			//下个还款日
			if(intval($transfer['next_repay_time']) == 0){
				$transfer['next_repay_time'] = next_replay_month($transfer['repay_start_time']);
			}
				
			if($transfer['next_repay_time'] - TIME_UTC + 24*3600 -1 < 0){				
				$status['status'] = 0;
				$status['show_err'] = "转让操作失败，有逾期未还款存在！";
				return $status;
			}
				
			//还款日
			$transfer['final_repay_time'] = next_replay_month($transfer['repay_start_time'],$transfer['repay_time']);
				
			//剩余期数
			if(intval($transfer['last_repay_time']) > 0)
				$transfer['how_much_month'] = how_much_month($transfer['last_repay_time'],$transfer['final_repay_time']);
			else{
				$transfer['how_much_month'] = how_much_month($transfer['repay_start_time'],$transfer['final_repay_time']);
			}
			
			$transfer_rs = deal_transfer($transfer);
			$transfer['month_repay_money'] = $transfer_rs['month_repay_money'];
			$transfer['all_must_repay_money'] = $transfer_rs['all_must_repay_money'];
			$transfer['left_benjin'] = $transfer_rs['left_benjin'];
				
			//剩多少利息
			$transfer['left_lixi'] = $transfer['all_must_repay_money'] - $transfer['left_benjin'];
	
			//判断转让金额是否超出了可转让的界限
			if(round($transfer_money,2) > round(floatval($transfer['all_must_repay_money']),2)){				
				$status['status'] = 0;
				$status['show_err'] = "转让金额不得大于最大转让金额";
				return $status;				
			}
				
			$transfer_data['create_time'] = TIME_UTC;
			$transfer_data['create_date'] = to_date(TIME_UTC);
			$transfer_data['deal_id'] = $transfer['id'];
			$transfer_data['load_id'] = $id;
			$transfer_data['user_id'] = $GLOBALS['user_info']['id'];
			$transfer_data['transfer_number'] = $transfer['how_much_month'];
			$transfer_data['last_repay_time'] = $transfer['final_repay_time'];
			$transfer_data['load_money'] = $transfer['load_money'];
			$transfer_data['status'] = 1;
			$transfer_data['transfer_amount'] = $transfer_money;
			$transfer_data['near_repay_time'] = $transfer['next_repay_time'];
				
			if($tid > 0){
				$GLOBALS['db']->autoExecute(DB_PREFIX."deal_load_transfer",$transfer_data,"UPDATE","id=".$tid);
			}
			else{
				$GLOBALS['db']->autoExecute(DB_PREFIX."deal_load_transfer",$transfer_data);
			}
				
			if($GLOBALS['db']->affected_rows()){				
				$status['status'] = 1;
				$status['show_err'] = "转让操作成功";
				return $status;
			}
			else{				
				$status['status'] = 0;
				$status['show_err'] = "转让操作失败";
				return $status;
			}
		}
		else{			
			$status['status'] = 0;
			$status['show_err'] = "不存在的债权";
			return $status;			
		}
	}
		
	function getUcTransferBuys($page,$status){
		if($page==0)
			$page = 1;
		
		$limit = (($page-1)*app_conf("PAGE_SIZE")).",".app_conf("PAGE_SIZE");
		$page_args= array();
	
		$condition = ' and d.is_effect=1 and d.is_delete=0  and d.repay_time_type =1 and  d.publish_wait=0 and dlt.t_user_id='.$GLOBALS['user_info']['id']."  ";
		$union_sql = " LEFT JOIN ".DB_PREFIX."deal_load_transfer dlt ON dlt.deal_id = dl.deal_id  and dlt.load_id=dl.id ";
		switch($status){
			case 1://回收中
				$condition.= " AND d.deal_status = 4 ";
				break;
			case 2://已回收
				$condition.=" AND d.deal_status = 5 ";
				break;
			default ://默认
				$condition.=" AND d.deal_status >= 4 ";
				break;
		}
	
		$count_sql = 'SELECT count(dl.id) FROM '.DB_PREFIX.'deal_load dl LEFT JOIN '.DB_PREFIX.'deal d ON d.id = dl.deal_id '.$union_sql.' WHERE 1=1 '.$condition;
	
		$rs_count = $GLOBALS['db']->getOne($count_sql." LIMIT $limit ");
		$list = array();
		if($rs_count > 0){
			$list_sql = 'SELECT dl.id as dlid,d.*,dl.money as load_money,dlt.id as dltid,dlt.status as tras_status,dlt.t_user_id,dlt.transfer_amount,dlt.create_time as tras_create_time,dlt.transfer_time FROM '.DB_PREFIX.'deal_load dl LEFT JOIN '.DB_PREFIX.'deal d ON d.id = dl.deal_id '.$union_sql.' WHERE 1=1 '.$condition." ORDER BY dlid DESC";
	
			$list = $GLOBALS['db']->getAll($list_sql);
			foreach($list as $k => $v){
				//最后还款日
				$list[$k]['final_repay_time'] = next_replay_month($v['repay_start_time'],$v['repay_time']);
				$list[$k]['final_repay_time_format'] = to_date($list[$k]['final_repay_time'],"Y-m-d");
				//剩余期数
				if($v['deal_status']==4){
					if(intval($v['last_repay_time']) > 0)
						$list[$k]['how_much_month'] = how_much_month($v['last_repay_time'],$list[$k]['final_repay_time']);
					else{
						$list[$k]['how_much_month'] = how_much_month($v['repay_start_time'],$list[$k]['final_repay_time']);
					}
				}
				else{
					$list[$k]['how_much_month'] = 0;
				}
	
				$transfer_rs = deal_transfer($list[$k]);
				$list[$k]['month_repay_money'] = $transfer_rs['month_repay_money'];
				
					
	
				if($v['deal_status']==4){
					
					$transfer_rs = deal_transfer($list[$k]);
					//剩余多少钱未回
					$list[$k]['all_must_repay_money'] = $transfer_rs['all_must_repay_money'];
					$list[$k]['left_benjin'] = $transfer_rs['left_benjin'];
					
					$list[$k]['left_benjin_format'] = format_price($list[$k]['left_benjin']/10000)."万";
					//剩多少利息
					$list[$k]['left_lixi'] = $list[$k]['all_must_repay_money'] - $list[$k]['left_benjin'];
					$list[$k]['left_lixi_format'] = format_price($list[$k]['left_lixi']);
	
				}
				else{
					$list[$k]['left_benjin_format'] = format_price(0);
					$list[$k]['left_lixi_format'] = format_price(0);
				}
	
				//转让价格
				$list[$k]['transfer_amount_format'] =  format_price($v['transfer_amount']/10000,3)."万";
	
				if($v['tras_create_time'] !=""){
					$list[$k]['tras_create_time_format'] =  to_date($v['tras_create_time'],"Y-m-d");
				}
	
				if(intval($v['transfer_time'])>0){
					$list[$k]['transfer_time_format'] =  to_date($v['transfer_time'],"Y-m-d");
				}

				$list[$k]['tras_status_op'] = 5;
				if($v['deal_status']==4)
					$list[$k]['tras_status_format'] = '回收中';
				elseif($v['deal_status']==5)
					$list[$k]['tras_status_format'] = '已回收';
				
				$durl = "/index.php?ctl=deal&act=mobile&is_sj=1&id=".$v['id'];							
				$list[$k]['app_url'] = str_replace("/mapi", "", SITE_DOMAIN.$durl);
			}
				
			return array('list'=>$list,'count'=>$rs_count);
		}else{
			return array('list'=>null,'count'=>0);
		}
	}	
	
	
	function getInchargeDone($payment_id,$money,$bank_id,$memo,$pingzheng)
	{
		$status = array('status'=>0,'show_err'=>'');

		if($money<=0)
		{
			$status['status'] = 0;
			$status['show_err'] = $GLOBALS['lang']['PLEASE_INPUT_CORRECT_INCHARGE'];
			return $status;
		}
	
		$payment_info = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."payment where id = ".$payment_id);
		if(!$payment_info)
		{
			$status['status'] = 0;
			$status['show_err'] = $GLOBALS['lang']['PLEASE_SELECT_PAYMENT'];
			return $status;
		}
		
		$order = array();
		$order['payment_id'] = $payment_id;
		$order['bank_id'] = $bank_id;
		$order['memo'] = $memo;
				
		//开始生成订单
		$now = TIME_UTC;	
		
		$order['user_type'] = 0;
		$order['user_id'] = $GLOBALS['user_info']['id'];
		$order['create_time'] = $now;
		$order['create_date'] = to_date(TIME_UTC,"Y-m-d");
		if($payment_info['fee_type'] == 0)
			$order['money'] = $money + $payment_info['fee_amount'];
		else
			$order['money'] = $money + $payment_info['fee_amount']*$money;
			
		//收用户手续费
		if($payment_info['fee_type'] == 0)
			$order['fee_amount'] = $payment_info['fee_amount'];
		else
			$order['fee_amount'] = $payment_info['fee_amount']*$money;

		/*支付手续费
		if($payment_info['pay_fee_type'] == 0)
			$order['pay_fee_amount'] = $payment_info['pay_fee_amount'];
		else
			$order['pay_fee_amount'] = $payment_info['pay_fee_amount']*$money;
		*/				

		if($payment_info['class_name']=='Otherpay' && $order['memo']!=""){
	
			$payment_info['config'] = unserialize($payment_info['config']);
			if($order['memo']==""){
				$status['status'] = 0;
				$status['show_err'] = "请输入银行流水单号";
				return $status;
			}
			
			if($order['bank_id']==""){
				$status['status'] = 0;
				$status['show_err'] = "请选择开户行";
				return $status;
			}
			
			
			$order['outer_notice_sn'] =  $order['memo'];//银行流水号
			
			$order['memo'] = "银行流水单号:".$order['memo'];
			$order['memo'] .= "<br>开户行：".$payment_info['config']['pay_bank'][$order['bank_id']];
			$order['memo'] .= "<br>充值银行：".$payment_info['config']['pay_name'][$order['bank_id']];
			$order['memo'] .= "<br>帐号：".$payment_info['config']['pay_account'][$order['bank_id']];
			$order['memo'] .= "<br>用户：".$payment_info['config']['pay_account_name'][$order['bank_id']];
			if($pingzheng!="")
				$order['memo'] .= "<br>凭证：<a href='".$pingzheng."' target='_blank'>查看</a>";
			
			//$order['bank_id'] = $payment_info['config']['pay_account'][$order['bank_id']];//银行帐户
		}
		do
		{
			$order['notice_sn'] = to_date(TIME_UTC,"Ymdhis").rand(100,999);
			$GLOBALS['db']->autoExecute(DB_PREFIX."payment_notice",$order,'INSERT','','SILENT');
			$order_id = intval($GLOBALS['db']->insert_id());
		}while($order_id==0);
	
		
		$status['payment_info'] = $payment_info;
		$status['status'] = 1;
		$status['payment_notice_id'] = $order_id;
		$status['order_id'] = $order_id;
		$status['pay_status'] = 0;
	
		return $status;
	}	
	
	//用户提现;
	function getUcSaveCarry($amount,$paypassword,$bid){
		$status = array('status'=>0,'show_err'=>'');
	
		if($GLOBALS['user_info']['id'] > 0){
			$paypassword = strim($paypassword);
			$amount = floatval($amount);
			$bid = intval($bid);
				
			if($paypassword==""){
				$status['status'] = 0;
				$status['show_err'] = $GLOBALS['lang']['PAYPASSWORD_EMPTY'];
				return $status;
			}
				
			if(md5($paypassword)!=$GLOBALS['user_info']['paypassword']){
				$status['status'] = 0;
				$status['show_err'] = $GLOBALS['lang']['PAYPASSWORD_ERROR'];
				return $status;
			}
				
			$data['user_id'] = intval($GLOBALS['user_info']['id']);
			$data['money'] = $amount;
			
			if($data['money'] <=0)
			{
				$status['status'] = 0;
				$status['show_err'] = $GLOBALS['lang']['CARRY_MONEY_NOT_TRUE'];
				return $status;
			}
			
			$fee = getCarryFee($data['money'],$GLOBALS['user_info']);
						
			//判断提现金额限制 	
			if(($data['money'] + $fee + floatval($GLOBALS['user_info']['nmc_amount'])) > floatval($GLOBALS['user_info']['money'])){
				$status['status'] = 0;
				$status['show_err'] = $GLOBALS['lang']['CARRY_MONEY_NOT_ENOUGHT'];
				return $status;
			}
			$data['fee'] = $fee;
				
			
				
			if($bid == 0)
			{
				$status['status'] = 0;
				$status['show_err'] = $GLOBALS['lang']['PLASE_ENTER_CARRY_BANK'];
				return $status;
			}
			//更新会员账户信息
			if((floatval($fee) + floatval($data['money'])) <= $GLOBALS['db']->getOne("SELECT money-nmc_amount FROM ".DB_PREFIX."user WHERE id=".intval($GLOBALS['user_info']['id']))){
					
				$user_bank = $GLOBALS['db']->getRow("SELECT * FROM ".DB_PREFIX."user_bank where user_id=".intval($GLOBALS['user_info']['id'])." AND id=$bid ");
					
				$data['bank_id'] = $user_bank['bank_id'];
				$data['real_name'] = $user_bank['real_name'];
				$data['region_lv1'] = intval($user_bank['region_lv1']);
				$data['region_lv2'] = intval($user_bank['region_lv2']);
				$data['region_lv3'] = intval($user_bank['region_lv3']);
				$data['region_lv4'] = intval($user_bank['region_lv4']);
				$data['bankzone'] = trim($user_bank['bankzone']);
				$data['bankcard'] = trim($user_bank['bankcard']);
					
					
				$data['create_time'] = TIME_UTC;
				$data['create_date'] = to_date(TIME_UTC,"Y-m-d");
				$GLOBALS['db']->autoExecute(DB_PREFIX."user_carry",$data,"INSERT");
					
				require APP_ROOT_PATH.'system/libs/user.php';
				modify_account(array('money'=>-$data['money'],'lock_money'=>$data['money']),$data['user_id'],"提现申请",8);
				modify_account(array('money'=>-$fee,'lock_money'=>$fee),$data['user_id'],"提现手续费",9);
				
				//$content = "您于".to_date($data['create_time'],"Y年m月d日 H:i:s")."提交的".format_price($data['money'])."提现申请我们正在处理，如您填写的账户信息正确无误，您的资金将会于3个工作日内到达您的银行账户.";
				
				$notice['time']=to_date($data['create_time'],"Y年m月d日 H:i:s");
				$notice['money']=format_price($data['money']);
					
				$tmpl_content = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."msg_template where name = 'TPL_WITHDRAWS_CASH'",false);
				$GLOBALS['tmpl']->assign("notice",$notice);
				$content = $GLOBALS['tmpl']->fetch("str:".$tmpl_content['content']);
					
				send_user_msg("",$content,0,$data['user_id'],TIME_UTC,0,true,5);
					
				$status['status'] = 1;
				$status['show_err'] = $GLOBALS['lang']['CARRY_SUBMIT_SUCCESS'];
				
				return $status;
				
			}
			else{
				$status['status'] = 0;
				$status['show_err'] = $GLOBALS['lang']['CARRY_MONEY_NOT_ENOUGHT'];
				return $status;
			}
			
		}else{
			$status['show_err'] ="未登录";
		}
		return $status;
	}	
	
	//用户提现;
	function getAuthorizedSaveCarry($amount,$paypassword,$bid){
		
		$status = array('status'=>0,'show_err'=>'');
	
		if($GLOBALS['authorized_info']['id'] > 0){
			$paypassword = strim($paypassword);
			$amount = floatval($amount);
			$bid = intval($bid);
			if($paypassword==""){
				$status['status'] = 0;
				$status['show_err'] = $GLOBALS['lang']['PAYPASSWORD_EMPTY'];
				return $status;
			}
			if(md5($paypassword)!=$GLOBALS['authorized_info']['paypassword']){
				$status['status'] = 0;
				$status['show_err'] = $GLOBALS['lang']['PAYPASSWORD_ERROR'];
				return $status;
			}
				
			$data['user_id'] = intval($GLOBALS['authorized_info']['id']);
			$data['money'] = $amount;
			
			if($data['money'] <=0)
			{
				$status['status'] = 0;
				$status['show_err'] = $GLOBALS['lang']['CARRY_MONEY_NOT_TRUE'];
				return $status;
			}
			$fee = 0;
			$feel_type = 0;
			//获取手续费配置表
			$fee_config = load_auto_cache("user_carry_config");
			//如果手续费大于最大的配置那么取这个手续费
			if($data['money'] >=$fee_config[count($fee_config)-1]['max_price']){
				$fee = $fee_config[count($fee_config)-1]['fee'];
				$feel_type = $fee_config[count($fee_config)-1]['fee_type'];
			}
			else{
				foreach($fee_config as $k=>$v){
					if($data['money'] >= $v['min_price'] &&$data['money'] <= $v['max_price']){
						$fee =  floatval($v['fee']);
						$feel_type = $v['fee_type'];
					}
				}
			}
			
			if($feel_type == 1){
				$fee = $data['money'] * $fee * 0.01;
			}	
			
			//判断提现金额限制	
			if(($data['money'] + $fee + floatval($GLOBALS['user_info']['nmc_amount'])) > floatval($GLOBALS['authorized_info']['money'])){
				$status['status'] = 0;
				$status['show_err'] = $GLOBALS['lang']['CARRY_MONEY_NOT_ENOUGHT'];
				return $status;
			}
			$data['fee'] = $fee;
				
			
				
			if($bid == 0)
			{
				$status['status'] = 0;
				$status['show_err'] = $GLOBALS['lang']['PLASE_ENTER_CARRY_BANK'];
				return $status;
			}
				
			$user_bank = $GLOBALS['db']->getRow("SELECT * FROM ".DB_PREFIX."user_bank where user_id=".intval($GLOBALS['authorized_info']['id'])." AND id=$bid ");
				
			$data['bank_id'] = $user_bank['bank_id'];
			$data['real_name'] = $user_bank['real_name'];
			$data['region_lv1'] = intval($user_bank['region_lv1']);
			$data['region_lv2'] = intval($user_bank['region_lv2']);
			$data['region_lv3'] = intval($user_bank['region_lv3']);
			$data['region_lv4'] = intval($user_bank['region_lv4']);
			$data['bankzone'] = trim($user_bank['bankzone']);
			$data['bankcard'] = trim($user_bank['bankcard']);
				
				
			$data['create_time'] = TIME_UTC;
			$GLOBALS['db']->autoExecute(DB_PREFIX."user_carry",$data,"INSERT");
				
			//更新会员账户信息
			require APP_ROOT_PATH.'system/libs/user.php';
			modify_account(array('money'=>-$data['money'],'lock_money'=>$data['money']),$data['user_id'],"提现申请",8);
			modify_account(array('money'=>-$fee,'lock_money'=>$fee),$data['user_id'],"提现手续费",9);
				
			//$content = "您于".to_date($data['create_time'],"Y年m月d日 H:i:s")."提交的".format_price($data['money'])."提现申请我们正在处理，如您填写的账户信息正确无误，您的资金将会于3个工作日内到达您的银行账户.";
				
				$notice['time']=to_date($data['create_time'],"Y年m月d日 H:i:s");
				$notice['money']=format_price($data['money']);
					
				$tmpl_content = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."msg_template where name = 'TPL_WITHDRAWS_CASH'",false);
				$GLOBALS['tmpl']->assign("notice",$notice);
				$content = $GLOBALS['tmpl']->fetch("str:".$tmpl_content['content']);
					
				send_user_msg("",$content,0,$data['user_id'],TIME_UTC,0,true,5);
				
			$status['status'] = 1;
			$status['show_err'] = $GLOBALS['lang']['CARRY_SUBMIT_SUCCESS'];
		}else{
			$status['show_err'] ="未登录";
		}
		return $status;
	}	
	
	function getUcRepayPlan($user_id,$status,$limit,$condition=""){
		$result = array("rs_count"=>0,"list"=>array());
		$extWhere =" 1=1 ";
		if($user_id > 0){
			$extWhere .=" and ((dlr.user_id = $user_id and dlr.t_user_id=0) or dlr.t_user_id=$user_id) ";
		}
		
		switch($status){
			case "1": //待还款
				$extWhere .=" and dlr.has_repay=0 ";
				break;
			case "2": //已还款
				$extWhere .=" and dlr.has_repay=1 ";
				break;
			case "3": //近期待还款
				$extWhere .=" and dlr.has_repay=0 and dlr.repay_time <=".next_replay_month(TIME_UTC,1)." ";
				break;
		}
		
		$sql_count = "SELECT count(*) FROM ".DB_PREFIX."deal_load_repay dlr LEFT JOIN ".DB_PREFIX."deal d On d.id = dlr.deal_id where $extWhere $condition ";
		
		$result['rs_count'] = $GLOBALS['db']->getOne($sql_count);
		if($result['rs_count'] > 0){
			$sql_list = "SELECT dlr.*,dlr.l_key +1 as l_key_index ,d.name FROM ".DB_PREFIX."deal_load_repay dlr LEFT JOIN ".DB_PREFIX."deal d On d.id = dlr.deal_id where $extWhere $condition ORDER BY dlr.repay_time DESC LIMIT ".$limit;
			
			$result['list'] = $GLOBALS['db']->getAll($sql_list);
			foreach($result['list'] as $k=>$v){
				$result['list'][$k]['l_key_index'] = "第 ".$v['l_key_index']." 期";
				//状态
				if($v['has_repay'] == 0){
					$result['list'][$k]['status_format'] = '待还';
				}elseif($v['status'] == 0){
					$result['list'][$k]['status_format'] = '提前还款';
				}elseif($v['status'] == 1){
					$result['list'][$k]['status_format'] = '准时还款';
				}elseif($v['status'] == 2){
					$result['list'][$k]['status_format'] = '逾期还款';
				}elseif($v['status'] == 3){
					$result['list'][$k]['status_format'] = '严重逾期';
				}
				$result['list'][$k]['interest_money_format'] = format_price($v['interest_money'] + $v['reward_money'] - $v['manage_money'] - $v['manage_interest_money']);
				$result['list'][$k]['shiji_money'] = format_price($v['true_interest_money'] + $v['impose_money'] + $v['true_reward_money'] - $v['true_manage_money'] - $v['true_manage_interest_money']);
				$result['list'][$k]['repay_money_format'] = format_price($v['repay_money']);
				$result['list'][$k]['manage_interest_money_format'] = format_price($v['manage_interest_money']);
			}
		}
		return $result;
	}
	
	
	
	
	
	
	
	function getUcDealRepay($user_id,$limit,$condition=""){
		
		$result = array("rs_count"=>0,"list"=>array());
		$extWhere =" 1=1 ";
		$extWhere .=" and   has_repay=0 and user_id = ".$user_id ." and repay_time <=".next_replay_month(TIME_UTC,1)." ";
		
		$sql_count = "SELECT count(*) FROM ".DB_PREFIX."deal_repay where  $extWhere $condition  order by deal_id";
		$result['rs_count'] = $GLOBALS['db']->getOne($sql_count);
		if($result['rs_count'] > 0){
			$result['list']=$GLOBALS['db']->getAll("select *,l_key+1 as l_key_index from ".DB_PREFIX."deal_repay where  $extWhere $condition order by deal_id limit ".$limit);
			foreach($result['list'] as $k=>$v){
				$result['list'][$k]['name']= $GLOBALS['db']->getOne("select name from ".DB_PREFIX."deal where id = ".$result['list'][$k]['deal_id']);//贷款名称
				$result['list'][$k]['l_key_index'] = "第 ".$v['l_key_index']." 期";
				if($v['has_repay'] == 0){
					$result['list'][$k]['status_format'] = '待还';
				}elseif($v['status'] == 0){
					$result['list'][$k]['status_format'] = '提前还款';
				}elseif($v['status'] == 1){
					$result['list'][$k]['status_format'] = '准时还款';
				}elseif($v['status'] == 2){
					$result['list'][$k]['status_format'] = '逾期还款';
				}elseif($v['status'] == 3){
					$result['list'][$k]['status_format'] = '严重逾期';
				}
				$result['list'][$k]['repay_money_format'] = format_price($v['repay_money']);
				$result['list'][$k]['self_money_format'] = format_price($v['self_money']);
				$result['list'][$k]['interest_money_format'] = format_price($v['interest_money']);
			}
		}
		return $result;
	}
?>