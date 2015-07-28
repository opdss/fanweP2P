<?php
// +----------------------------------------------------------------------
// | Fanwe 方维p2p借贷系统
// +----------------------------------------------------------------------
// | Copyright (c) 2011 http://www.fanwe.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 云淡风轻(88522820@qq.com)
// +----------------------------------------------------------------------

require APP_ROOT_PATH.'app/Lib/uc.php';

class uc_centerModule extends SiteBaseModule
{
	private $space_user;
	public function init_main()
	{
//		$user_info = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."user where id = ".intval($GLOBALS['user_info']['id']));		
//		require_once APP_ROOT_PATH."system/extend/ip.php";		
//		$iplocation = new iplocate();
//		$address=$iplocation->getaddress($user_info['login_ip']);
//		$user_info['from'] = $address['area1'].$address['area2'];
		$GLOBALS['tmpl']->assign('user_auth',get_user_auth());
	}
	
	public function init_user(){
		$this->user_data = $GLOBALS['user_info'];
		
		$province_str = $GLOBALS['db']->getOne("select name from ".DB_PREFIX."region_conf where id = ".$this->user_data['province_id']);
		$city_str = $GLOBALS['db']->getOne("select name from ".DB_PREFIX."region_conf where id = ".$this->user_data['city_id']);
		if($province_str.$city_str=='')
			$user_location = $GLOBALS['lang']['LOCATION_NULL'];
		else 
			$user_location = $province_str." ".$city_str;
		
		$this->user_data['fav_count'] = $GLOBALS['db']->getOne("select count(*) from ".DB_PREFIX."topic where user_id = ".$this->user_data['id']." and fav_id <> 0");
		$this->user_data['user_location'] = $user_location;
		$this->user_data['group_name'] = $GLOBALS['db']->getOne("select name from ".DB_PREFIX."user_group where id = ".$this->user_data['group_id']." ");
		
		$this->user_data['user_statics'] =sys_user_status($GLOBALS['user_info']['id'],false);
		$GLOBALS['tmpl']->assign('user_statics',$this->user_data['user_statics']);
	}
	
	public function index()
	{	
		$this->init_user();
		$user_info = $this->user_data;
		
			 
		$ajax =intval($_REQUEST['ajax']);
		if($ajax==0)
		{
			$this->init_main();			
		}
		$user_id = intval($GLOBALS['user_info']['id']);	
		
		
		/***统计***/
		$user_statics = $user_info['user_statics'];
		
		
		//投资收益
		$user_statics["load_earnings"] = number_format($user_statics['load_earnings'] + $user_statics['reward_money'] + $user_statics['load_tq_impose'] + $user_statics['load_yq_impose'] + $user_statics['rebate_money'] + $user_statics['referrals_money'] - $user_statics['carry_fee_money']- $user_statics['incharge_fee_money'], 2);
		
		
		//已赚收益
		$user_statics["need_repay_amount"] = floatval($user_statics["need_repay_amount"])+floatval($user_statics["need_manage_amount"]);
		
		//待收本金
		$user_statics["load_wait_self_money"] = floatval($user_statics["load_wait_self_money"]);
		
		$user_statics["clear_total_money"] = number_format((round($user_statics["load_wait_self_money"],2) + round($user_info["money"],2) + round($user_info["lock_money"],2) - round($user_statics["need_repay_amount"],2)),2);
		
		$user_statics["load_wait_self_money"] = number_format($user_statics["load_wait_self_money"],2);
		
		//待收收益
		$user_statics["load_wait_earnings"] = number_format(floatval($user_statics["load_wait_earnings"]), 2);
		
		$user_statics["ltotal_money"] = number_format(floatval($user_statics["load_wait_repay_money"]) + floatval($user_statics["load_repay_money"]),2);
		
		$user_info["total_money"] = number_format(floatval($user_info["money"]) + floatval($user_info["lock_money"]), 2);
		
		$user_info["lock_money"] = number_format(floatval($user_info["lock_money"]), 2);
		$user_statics["money"] = number_format(floatval($user_info["money"]), 2);
		
		$user_statics["need_repay_amount"]= number_format(floatval($user_statics["need_repay_amount"]), 2);
		
		//投标中的
		$invest_sql = "SELECT count(*) as l_count,sum(money) as l_money FROM ".DB_PREFIX."deal_load dl LEFT JOIN ".DB_PREFIX."deal d ON dl.deal_id = d.id WHERE dl.user_id=".$user_id." and d.deal_status in(1,2) group by dl.user_id";

		$invest = $GLOBALS['db']->getRow($invest_sql);
		$user_statics["invest_count"] = $invest["l_count"];
		$user_statics["invest_money"] = number_format($invest["l_money"],2);
		$user_statics["total_money"] = number_format(round($invest_sql["money"],2)+ round($user_statics["load_wait_repay_money"],2)+round($user_statics["load_repay_money"],2),2);
		
		//待回收本息
		$user_statics["load_wait_repay_money"] = number_format(floatval($user_statics["load_wait_repay_money"]), 2);
		//已回收本息
		$user_statics["load_repay_money"] = number_format(floatval($user_statics["load_repay_money"]), 2);
		
		//本月
		$this_wait_deals = $this->get_loadlist($user_id,"  AND DATE_FORMAT(FROM_UNIXTIME(repay_time),'%Y年%m月')  = date_format(curdate(),'%Y年%m月') ");
		$user_statics["this_month_money"] = 0.00;
		$user_statics["this_month_count"] = 0;
		
		foreach($this_wait_deals as $k=>$v)
		{
			$user_statics["this_month_money"] += $v["repay_money"];
			$user_statics["this_month_count"] ++;
		}
		//下月
		$next_wait_deals = $this->get_loadlist($user_id," AND DATE_FORMAT(FROM_UNIXTIME(repay_time),'%Y年%m月')  = date_format(DATE_ADD(curdate(), INTERVAL 1 MONTH),'%Y年%m月')");
		$user_statics["next_month_money"] = 0.00;
		$user_statics["next_month_count"] = 0;
		
		foreach($next_wait_deals as $k=>$v)
		{
			$user_statics["next_month_money"] += $v["repay_money"];
			$user_statics["next_month_count"] ++;
		}
		
		//本年
		$year_wait_deals = $this->get_loadlist($user_id," AND DATE_FORMAT(FROM_UNIXTIME(repay_time),'%Y')  =  DATE_FORMAT(curdate(),'%Y')");
		
		$user_statics["year_money"] = 0.00;
		$user_statics["year_count"] = 0;
		
		foreach($year_wait_deals as $k=>$v)
		{
			$user_statics["year_money"] += $v["repay_money"];
			$user_statics["year_count"] ++;
		}

		$user_statics["year_money"] = number_format(round($user_statics["year_money"],2),2);
		$user_statics["this_month_money"] = number_format(round($user_statics["this_month_money"],2),2);
		$user_statics["next_month_money"] = number_format(round($user_statics["next_month_money"],2),2);
		
		//总计
		$all_wait_deals = $this->get_loadlist($user_id,'');
		$user_statics["total_invest_money"] = 0.00;
		$user_statics["total_invest_count"] = 0;
		
		foreach($all_wait_deals as $k=>$v)
		{
			$user_statics["total_invest_money"] += $v["repay_money"];
			$user_statics["total_invest_count"] ++;
		}
		$user_statics["total_invest_money"] = number_format($user_statics["total_invest_money"],2);
		//$user_statics["total_invest_count"] = $user_statics["this_month_count"]+$user_statics["next_month_count"]+$user_statics["year_count"];
		
		
		$load_list_sql = "SELECT * FROM ".DB_PREFIX."deal_load WHERE user_id = ".$GLOBALS['user_info']['id']." ORDER BY id DESC limit 0,4";
		//最近交易
		$load_list = $GLOBALS['db']->getAll($load_list_sql,false);
		$GLOBALS['tmpl']->assign("load_list",$load_list);


		//$user_statics["total_money"] =  number_format(floatval($user_info["load_wait_repay_money"]) - floatval($user_info["need_repay_amount"]));
		$GLOBALS['tmpl']->assign("user_statics",$user_statics);
		
		//最近六个月投资记录
		$month = array();
		
		//select month(FROM_UNIXTIME(time)) from table_name group by month(FROM_UNIXTIME(time))
		$result['lend'] = $GLOBALS['db']->getAll("SELECT count(*) as l_count,sum(money) as l_money,DATE_FORMAT(FROM_UNIXTIME(dl.create_time),'%Y年%m月') as l_month FROM ".DB_PREFIX."deal_load dl LEFT JOIN ".DB_PREFIX."deal d ON dl.deal_id = d.id WHERE dl.is_repay = 0 AND dl.user_id=".$user_id." and d.deal_status in(1,2,4,5) group by DATE_FORMAT(FROM_UNIXTIME(dl.create_time),'%Y年%m月')",false);
		$months[0]["time"] = to_date(next_replay_month(TIME_UTC,-5),'Y年m月');
		$months[1]["time"] = to_date(next_replay_month(TIME_UTC,-4),'Y年m月');
		$months[2]["time"] = to_date(next_replay_month(TIME_UTC,-3),'Y年m月');
		$months[3]["time"] = to_date(next_replay_month(TIME_UTC,-2),'Y年m月');
		$months[4]["time"] = to_date(next_replay_month(TIME_UTC,-1),'Y年m月');
		$months[5]["time"] = to_date(TIME_UTC,'Y年m月');
		
		$max_money = 100;
		foreach($result['lend']  as $k=>$v)
		{
			if(round($max_money)<round($v["l_money"]))
			{
				$max_money = $v["l_money"];
			}
			foreach($months as $kk => $vv)
			{
				if($vv["time"] == $v["l_month"])
				{
					$months[$kk]["l_money"] = $v["l_money"];
					$months[$kk]["show_money"] = number_format(floatval($v["l_money"]), 2); 
				}
			}
		}
		foreach($months as $k => $v)
		{
			$months[$k]["height"] = $v["l_money"]/$max_money*325;
			$months[$k]["bottom"] = $v["l_money"]/$max_money*325+35;
		}
		$GLOBALS['tmpl']->assign("max_money",$max_money);
		$GLOBALS['tmpl']->assign("months",$months);
		
		/***右侧统计结束***/
		
		$GLOBALS['tmpl']->assign("user_data",$user_info);
		if($ajax==0)
		{
			//近期待还款
			$day_deal_repay = getUcDealRepay($user_id,10,"");
			//近期待收款
			$day_repay_list = getUcRepayPlan($user_id,3,10,"");

			//推荐的标
			require APP_ROOT_PATH."app/Lib/deal_func.php";
			$where = " is_recommend = 1 and deal_status in (0,1,2)";
			$deals_list = get_deal_list(10,0,$where);
			
			foreach($deals_list['list'] as $k=>$v){
				$deals_list['list'][$k]['repay_time_format'] = $v['repay_time']."个月";
				$deals_list['list'][$k]['start_time_format'] = to_date($v['start_time'],"Y-m-d");
				
				if($v['is_delete'] == 2)
					$deals_list['list'][$k]['deal_status_format'] = "待发布";
				elseif($v['is_wait'] == 1)
					$deals_list['list'][$k]['deal_status_format'] = "未开始";
				elseif ($v['deal_status'] == 5)
					$deals_list['list'][$k]['deal_status_format'] = "还款完毕";
				elseif($v['deal_status'] == 4)
					$deals_list['list'][$k]['deal_status_format'] = "还款中";
				elseif($v['deal_status'] == 0)
					$deals_list['list'][$k]['deal_status_format'] = $v['need_credit']==0 ? "等待审核" : "等待材料";
				elseif($v['deal_status'] == 1 && $v['remain_time'] > 0)
					$deals_list['list'][$k]['deal_status_format'] ="筹款中";
				elseif($v['deal_status'] == 2)
					$deals_list['list'][$k]['deal_status_format'] ="满标";
				elseif($v['deal_status'] ==3 || $v['remain_time'] <= 0)
					$deals_list['list'][$k]['deal_status_format'] ="流标";
					
			}
			$GLOBALS['tmpl']->assign('day_deal_repay',$day_deal_repay['list']);
			$GLOBALS['tmpl']->assign('day_repay_list',$day_repay_list['list']);
			$GLOBALS['tmpl']->assign('deals_list',$deals_list['list']);
			
			$GLOBALS['tmpl']->assign("page_title",$GLOBALS['lang']['UC_CENTER_INDEX']);
			$GLOBALS['tmpl']->assign("post_title",$GLOBALS['lang']['UC_CENTER_INDEX']);			
			$GLOBALS['tmpl']->assign("inc_file","inc/uc/uc_center_index.html");
			$GLOBALS['tmpl']->display("page/uc.html");	
		}
		else
		{
			header("Content-Type:text/html; charset=utf-8");
			echo $GLOBALS['tmpl']->fetch("inc/topic_col_list.html");
		}
	}
	
	public function focustopic()
	{	
		$this->init_user();
		$user_info = $this->user_data;
		$ajax =intval($_REQUEST['ajax']);
		if($ajax==0)
		{ 
			$this->init_main();	
		}
		$user_id = intval($GLOBALS['user_info']['id']);
		//输出发言列表
		$page = intval($_REQUEST['p']);
		if($page==0)
		$page = 1;
		$limit = (($page-1)*app_conf("PAGE_SIZE")).",".app_conf("PAGE_SIZE");
					
		//开始输出相关的用户日志
		$uids = $GLOBALS['db']->getOne("select group_concat(focused_user_id) from ".DB_PREFIX."user_focus where focus_user_id = ".$user_info['id']." ");

		if($uids)
		{
			$uids = trim($uids,",");	
			$result = get_topic_list($limit," user_id in (".$uids.") ");
		}
		
		$GLOBALS['tmpl']->assign("topic_list",$result['list']);
		$page = new Page($result['total'],app_conf("PAGE_SIZE"));   //初始化分页对象 		
		$p  =  $page->show();
		$GLOBALS['tmpl']->assign('pages',$p);
		$GLOBALS['tmpl']->assign('user_data',$user_info);
		if($ajax==0)
		{	
			$list_html = $GLOBALS['tmpl']->fetch("inc/topic_col_list.html");
			$GLOBALS['tmpl']->assign("list_html",$list_html);
			$GLOBALS['tmpl']->assign("page_title",$GLOBALS['lang']['UC_CENTER_MYFAV']);
			$GLOBALS['tmpl']->assign("post_title",$GLOBALS['lang']['UC_CENTER_MYFAV']);			
			$GLOBALS['tmpl']->assign("inc_file","inc/uc/uc_center_index.html");
			$GLOBALS['tmpl']->display("page/uc.html");	
		}
		else
		{
			header("Content-Type:text/html; charset=utf-8");
			echo $GLOBALS['tmpl']->fetch("inc/topic_col_list.html");
		}
	}
	
	
	public function lend()
	{
		$this->init_user();
		$user_info = $this->user_data;
		$ajax =intval($_REQUEST['ajax']);
		if($ajax==0)
		{ 
			$this->init_main();
		}
		$user_id = intval($user_info['id']);	
		//输出发言列表
		$page = intval($_REQUEST['p']);
		if($page==0)
		$page = 1;
		$limit = (($page-1)*app_conf("PAGE_SIZE")).",".app_conf("PAGE_SIZE");
		
		$result['total'] = $GLOBALS['db']->getOne("SELECT count(*) FROM ".DB_PREFIX."deal_load WHERE user_id=".$user_id);
		if($result['total'] >0)
			$result['list'] = $GLOBALS['db']->getAll("SELECT dl.*,d.rate,d.repay_time,d.repay_time_type,d.deal_status,d.name FROM ".DB_PREFIX."deal_load dl LEFT JOIN ".DB_PREFIX."deal d ON dl.deal_id = d.id WHERE dl.user_id=".$user_id." LIMIT ".$limit);
		
		$page = new Page($result['total'],app_conf("PAGE_SIZE"));   //初始化分页对象 		
		$p  =  $page->show();
		$GLOBALS['tmpl']->assign('pages',$p);
		
		$GLOBALS['tmpl']->assign("lend_list",$result['list']);
		$GLOBALS['tmpl']->assign("user_data",$user_info);
		
		if($ajax==0)
		{	
			$list_html = $GLOBALS['tmpl']->fetch("inc/uc/uc_center_lend.html");
			$GLOBALS['tmpl']->assign("list_html",$list_html);
			$GLOBALS['tmpl']->assign("page_title",$GLOBALS['lang']['UC_CENTER_LEND']);
			$GLOBALS['tmpl']->assign("post_title",$GLOBALS['lang']['UC_CENTER_LEND']);			
			$GLOBALS['tmpl']->assign("inc_file","inc/uc/uc_center_index.html");
			$GLOBALS['tmpl']->display("page/uc.html");	
		}
		else
		{
			header("Content-Type:text/html; charset=utf-8");
			echo $GLOBALS['tmpl']->fetch("inc/uc_center_lend.html");
		}
	}
	
	
	public function deal()
	{	
		$this->init_user();
		$user_info = $this->user_data;	
		$ajax =intval($_REQUEST['ajax']);
		if($ajax==0)
		{ 
			$this->init_main();	
		}
		$user_id = intval($user_info['id']);
		
		//输出借款记录
		$page = intval($_REQUEST['p']);
		if($page==0)
		$page = 1;
		$limit = (($page-1)*app_conf("PAGE_SIZE")).",".app_conf("PAGE_SIZE");
			
		require_once (APP_ROOT_PATH."app/Lib/deal.php");
		
		$result = get_deal_list($limit,0,"user_id=".$user_id,"id DESC");

		$GLOBALS['tmpl']->assign("deal_list",$result['list']);
		
		$page = new Page($result['count'],app_conf("PAGE_SIZE"));   //初始化分页对象 		
		$p  =  $page->show();
		$GLOBALS['tmpl']->assign('pages',$p);
		
		$GLOBALS['tmpl']->assign('user_data',$user_info);
		if($ajax==0)
		{	
			$list_html = $GLOBALS['tmpl']->fetch("inc/uc/uc_center_deals.html");
			$GLOBALS['tmpl']->assign("list_html",$list_html);
			$GLOBALS['tmpl']->assign("page_title",$GLOBALS['lang']['UC_CENTER_MYDEAL']);
			$GLOBALS['tmpl']->assign("post_title",$GLOBALS['lang']['UC_CENTER_MYDEAL']);			
			$GLOBALS['tmpl']->assign("inc_file","inc/uc/uc_center_index.html");
			$GLOBALS['tmpl']->display("page/uc.html");	
		}
		else
		{
			header("Content-Type:text/html; charset=utf-8");
			echo $GLOBALS['tmpl']->fetch("inc/uc/uc_center_deals.html");
		}
	}
	
	
	
	public function mayfocus()
	{
		$user_info = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."user where id = ".intval($GLOBALS['user_info']['id']));		
		$GLOBALS['tmpl']->assign("user_data",$user_info);
		$GLOBALS['tmpl']->assign("page_title",$GLOBALS['lang']['YOU_MAY_FOCUS']);		
		$GLOBALS['tmpl']->assign("inc_file","inc/uc/uc_center_mayfocus.html");
		$GLOBALS['tmpl']->display("page/uc.html");
	}
	public function fans()
	{
		$user_info = $this->user_data;
				
		$page_size = 24;
		
		$page = intval($_REQUEST['p']);
		if($page==0)
		$page = 1;
		$limit = (($page-1)*$page_size).",".$page_size;
	
		$user_id = intval($GLOBALS['user_info']['id']);
		
		//输出粉丝
		$total = $GLOBALS['db']->getOne("select count(*) from ".DB_PREFIX."user_focus where focused_user_id = ".$user_id);
		$fans_list = array();
		if($total > 0){
			$fans_list = $GLOBALS['db']->getAll("select focus_user_id as id,focus_user_name as user_name from ".DB_PREFIX."user_focus where focused_user_id = ".$user_id." order by id desc limit ".$limit);
					
			foreach($fans_list as $k=>$v)
			{			
				$focus_uid = intval($v['id']);
				$focus_data = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."user_focus where focus_user_id = ".$user_id." and focused_user_id = ".$focus_uid);
				if($focus_data)
				$fans_list[$k]['focused'] = 1;
			}
		}
		$GLOBALS['tmpl']->assign("fans_list",$fans_list);	

		$page = new Page($total,$page_size);   //初始化分页对象 		
		$p  =  $page->show();
		$GLOBALS['tmpl']->assign('pages',$p);
		
		
		$GLOBALS['tmpl']->assign("user_data",$user_info);
		$GLOBALS['tmpl']->assign("page_title",$GLOBALS['lang']['MY_FANS']);		
		$GLOBALS['tmpl']->assign("inc_file","inc/uc/uc_center_fans.html");
		$GLOBALS['tmpl']->display("page/uc.html");
	}
	
	
	public function focus()
	{
		$this->init_user();
		$user_info = $this->user_data;
				
		$page_size = 24;
		
		$page = intval($_REQUEST['p']);
		if($page==0)
		$page = 1;
		$limit = (($page-1)*$page_size).",".$page_size;
	
		$user_id = intval($GLOBALS['user_info']['id']);
		
		//输出粉丝
		$total = $GLOBALS['db']->getOne("select count(*) from ".DB_PREFIX."user_focus where focus_user_id = ".$user_id);
		$focus_list = array();
		if($total > 0){
			$focus_list = $GLOBALS['db']->getAll("select focused_user_id as id,focused_user_name as user_name from ".DB_PREFIX."user_focus where focus_user_id = ".$user_id." order by id desc limit ".$limit);
			
			foreach($focus_list as $k=>$v)
			{			
				$focus_uid = intval($v['id']);
				$focus_data = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."user_focus where focus_user_id = ".$user_id." and focused_user_id = ".$focus_uid);
				if($focus_data)
				$focus_list[$k]['focused'] = 1;
			}
		}
		$GLOBALS['tmpl']->assign("focus_list",$focus_list);	

		$page = new Page($total,$page_size);   //初始化分页对象 		
		$p  =  $page->show();
		$GLOBALS['tmpl']->assign('pages',$p);
		
		
		$list_html = $GLOBALS['tmpl']->fetch("inc/uc/uc_center_focus.html");
		$GLOBALS['tmpl']->assign("list_html",$list_html);
		$GLOBALS['tmpl']->assign("user_data",$user_info);
		$GLOBALS['tmpl']->assign("user_id",$user_id);
		$GLOBALS['tmpl']->assign("page_title",$GLOBALS['lang']['MY_FOCUS']);	
		
			
		$GLOBALS['tmpl']->assign("inc_file","inc/uc/uc_center_index.html");
		$GLOBALS['tmpl']->display("page/uc.html");
	}
	
	
	public function setweibo()
	{
		$user_info = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."user where id = ".intval($GLOBALS['user_info']['id']));
				
		$apis = $GLOBALS['db']->getAll("select * from ".DB_PREFIX."api_login ");
		
		foreach($apis as $k=>$v)
		{
			if($user_info[strtolower($v['class_name'])."_id"])
			{
				$apis[$k]['is_bind'] = 1;
				if($user_info["is_syn_".strtolower($v['class_name'])]==1)
				{
					$apis[$k]['is_syn'] = 1;
				}
				else
				{
					$apis[$k]['is_syn'] = 0;
				}
			}
			else
			{
				$apis[$k]['is_bind'] = 0;
			}
			
//			if(file_exists(APP_ROOT_PATH."system/api_login/".$v['class_name']."_api.php"))
//			{
//				require_once APP_ROOT_PATH."system/api_login/".$v['class_name']."_api.php";
//				$api_class = $v['class_name']."_api";
//				$api_obj = new $api_class($v);
//				$url = $api_obj->get_bind_api_url();
//				$apis[$k]['url'] = $url;
//			}
		}		
		$GLOBALS['tmpl']->assign("apis",$apis);
		$GLOBALS['tmpl']->assign("user_data",$user_info);
		$GLOBALS['tmpl']->assign("page_title",$GLOBALS['lang']['SETWEIBO']);		
		$GLOBALS['tmpl']->assign("inc_file","inc/uc/uc_center_setweibo.html");
		$GLOBALS['tmpl']->display("page/uc.html");
	}
	
	private function get_loadlist($user_id,$where) {
		$condtion = "   AND dlr.has_repay = 0  ".$where." ";
    	$sql = "select dlr.*,u.user_name,u.level_id,u.province_id,u.city_id from ".DB_PREFIX."deal_load_repay dlr LEFT JOIN ".DB_PREFIX."user u ON u.id=dlr.user_id  where ((dlr.user_id = ".$user_id." and dlr.t_user_id = 0) or dlr.t_user_id = ".$user_id.") $condtion order by dlr.repay_time desc ";
		$list = $GLOBALS['db']->getAll($sql);
		
		return $list;
    }
}
?>