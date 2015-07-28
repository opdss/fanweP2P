<?php

require APP_ROOT_PATH.'app/Lib/uc.php';
class uc_score_exchangeModule extends SiteBaseModule {

     public function index(){
		$GLOBALS['tmpl']->assign("page_title","积分兑现");
		
		$list = load_auto_cache("level");
		
		$GLOBALS['tmpl']->assign("list",$list['list']);
		
		$userinfo=$GLOBALS['db']->getRow("SELECT * FROM ".DB_PREFIX."user WHERE id='".$GLOBALS['user_info']['id']."' and vip_state='1' ");
		$GLOBALS['tmpl']->assign('userinfo',$userinfo);
		
		$gradeinfo=$GLOBALS['db']->getRow("SELECT * FROM ".DB_PREFIX."vip_type WHERE id='".$userinfo['vip_id']."' and is_delete='0' ");
		$GLOBALS['tmpl']->assign('gradeinfo',$gradeinfo);
		
		$vip_type=$GLOBALS['db']->getAll("SELECT * FROM ".DB_PREFIX."vip_type WHERE is_effect='1' and is_delete='0'  ");
		$GLOBALS['tmpl']->assign('vip_type',$vip_type);
		
		$vip_setting=$GLOBALS['db']->getAll("SELECT * FROM ".DB_PREFIX."vip_setting v LEFT JOIN ".DB_PREFIX."vip_type vt ON v.vip_id=vt.id WHERE v.is_effect='1' and v.is_delete='0' ");
		$GLOBALS['tmpl']->assign('vip_setting',$vip_setting);
		
		$json_vipinfo=$GLOBALS['db']->getRow("SELECT * FROM ".DB_PREFIX."vip_setting WHERE vip_id='".$userinfo['vip_id']."' ");
		$GLOBALS['tmpl']->assign('json_vipinfo',$json_vipinfo);
		
		$customerinfo=$GLOBALS['db']->getRow("SELECT * FROM ".DB_PREFIX."customer WHERE id='".$userinfo['customer_id']."' and is_effect='1'  ");
		$GLOBALS['tmpl']->assign('customerinfo',$customerinfo);
		
		$today=to_date(TIME_UTC,"Y-m-d");
		$start_time=to_date(next_replay_month(TIME_UTC,-24),"Y-m-d");
		$begin_date=to_date(next_replay_month(TIME_UTC,-3),"Y-m-d");
		
		$user_id=$GLOBALS['user_info']['id'];
		$borrow_total = $GLOBALS['db']->getOne("select sum(borrow_amount) FROM ".DB_PREFIX."deal WHERE start_date>'$start_time' and user_id='$user_id' and deal_status > 3 ");
		$load_total = $GLOBALS['db']->getOne("select sum(dl.money) FROM ".DB_PREFIX."deal_load dl LEFT JOIN ".DB_PREFIX."deal d ON dl.deal_id=d.id WHERE dl.create_date>'$start_time' and dl.user_id='$user_id' and d.deal_status > 3  ");
		$t_u_load = $GLOBALS['db']->getRow("SELECT sum(dlr.repay_money) as load_money  FROM ".DB_PREFIX."deal_load_repay dlr LEFT JOIN ".DB_PREFIX."deal d ON d.id=dlr.deal_id LEFT JOIN ".DB_PREFIX."deal_load dl ON dl.id=dlr.load_id WHERE d.is_effect=1 and dl.is_repay= 0 and  dlr.t_user_id = ".$user_id);
		$load_total = floatval($load_total) + floatval($t_u_load['load_money']);
//		$load_total = $GLOBALS['db']->getOne("select sum(money) FROM ".DB_PREFIX."deal_load WHERE create_date>'$start_time' and user_id='$user_id' ");
		$overdue_total = $GLOBALS['db']->getOne("select sum(repay_money) FROM ".DB_PREFIX."deal_repay WHERE ((has_repay=1 and repay_date<true_repay_date) or (has_repay=0 and repay_date <'$today')) and user_id='$user_id' ");
		
		$bl_total=(int)$borrow_total+(int)$load_total;
		$yx_total= $bl_total-$overdue_total;
		
		$vipgradeinfo = $GLOBALS['db']->getRow("select * FROM ".DB_PREFIX."vip_type WHERE lower_limit<='$yx_total' and upper_limit>='$yx_total' and is_effect='1' and is_delete='0' ");
		$grade_cz=$GLOBALS['db']->getRow("SELECT * FROM ".DB_PREFIX."vip_type where is_effect='1' and is_delete='0' order by lower_limit asc limit 1  ");

		if(($grade_cz['lower_limit']>$yx_total)||$userinfo['vip_id']==0){
			$chazhi=(int)$grade_cz['lower_limit']-$yx_total;
			if($chazhi>0){
				$chazhi = $chazhi;
			}else{
				$chazhi = 0;
			}
			$nextgrade=$grade_cz['vip_grade'];
		}else{
			$chazhi=$vipgradeinfo['upper_limit']-$yx_total+1;
			$nextgradeinfo = $GLOBALS['db']->getRow("select * FROM ".DB_PREFIX."vip_type WHERE  lower_limit >'".$vipgradeinfo['upper_limit']."' and is_effect='1' and is_delete='0' order by lower_limit asc limit 1 ");
			$nextgrade=$nextgradeinfo['vip_grade'];
		}
		$chazhi = number_format($chazhi);
		$bl_total = number_format($bl_total);
		$vipgradeinfo = number_format($vipgradeinfo);
		$yx_total = number_format($yx_total);
		$overdue_total = number_format($overdue_total);
		
		$GLOBALS['tmpl']->assign('bl_total',$bl_total);
		$GLOBALS['tmpl']->assign('chazhi',$chazhi);
		$GLOBALS['tmpl']->assign('nextgrade',$nextgrade);
		$GLOBALS['tmpl']->assign('overdue_total',$overdue_total);
		$GLOBALS['tmpl']->assign('yx_total',$yx_total);
		
		$GLOBALS['tmpl']->assign('vipgradeinfo',$vipgradeinfo);
		
		$GLOBALS['tmpl']->assign("inc_file","inc/uc/uc_score_exchange.html");
		$GLOBALS['tmpl']->display("page/uc.html");
	}
	
	public function cash_save(){
		
		$data['integral'] = intval($_REQUEST['integral']);
		$data['user_id'] = $GLOBALS['user_info']['id'];
		require APP_ROOT_PATH.'system/libs/user.php';
		$userinfo=$GLOBALS['db']->getRow("SELECT * FROM ".DB_PREFIX."user WHERE id='".$GLOBALS['user_info']['id']."' ");
		if($userinfo['vip_id']==0){
			showErr("您还不是VIP会员，不可兑换积分！",0);
		}
		if($data['integral']==0||$data['integral']==""){
			showErr("请输入正确的兑换积分！",0);
		}
		
		if($data['integral']>$userinfo['score']){
			showErr("该积分超过了用户积分",0);
		}else{
			if($data['integral']%1000!=0){
				showErr("该积分不是1000的倍数积分",0);
			}else{
				$vininfo=$GLOBALS['db']->getRow("SELECT * FROM ".DB_PREFIX."vip_setting WHERE vip_id='".$userinfo['vip_id']."' ");
				$excmoney=($data['integral']*$vininfo['coefficient'])/100;
				modify_account(array('money'=>$excmoney,'nmc_amount'=>$excmoney,'score'=>-$data['integral']),$GLOBALS['user_info']['id'],'积分兑现',22);
			}
		}
		
		$data['vip_id'] = $userinfo['vip_id'];
		$data['exchange_date']=to_date(TIME_UTC,"Y-m-d");
		$data['cash'] = $excmoney;
		
		$GLOBALS['db']->autoExecute(DB_PREFIX."score_exchange_record",$data,"INSERT");
		
		if($GLOBALS['db']->affected_rows()){
			showSuccess("兑现成功",0);
		}
		else{
			showErr("兑现失败",0);
		}
		
	}
	
	public function record(){
		
		$user_info = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."user where id = ".intval($GLOBALS['user_info']['id']));		
		$level_info = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."user_group where id = ".intval($user_info['group_id']));
		$point_level = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."user_level where id = ".intval($user_info['level_id']));
		$user_info['user_level'] = $level_info['name'];
		$user_info['point_level'] = $point_level['name'];
		$user_info['discount'] = $level_info['discount']*10;
		$GLOBALS['tmpl']->assign("user_data",$user_info);
		
		$type_title = isset($_REQUEST['type_title']) ? intval($_REQUEST['type_title']) : 100;
		
		$times = intval($_REQUEST['times']);
		$time_status = intval($_REQUEST['time_status']);
		
		$t = "score";
		$GLOBALS['tmpl']->assign("t",$t);
		
		$page = intval($_REQUEST['p']);
		if($page==0)
			$page = 1;
		$limit = (($page-1)*app_conf("PAGE_SIZE")).",".app_conf("PAGE_SIZE");
		
		if($t=="score"){
			$title_arrays = array(
				"100" => "全部",
				"0" => "结存",
				"1" => "充值",
				"2" => "投标成功",
				"3" => "招标成功",
				"8" => "申请提现",
				"9" => "提现手续费",
				"13" => "人工操作",
				"18" => "开户奖励",
				"22" => "兑换",
				"25" => "签到成功",
				"28"=>"投资奖励 ",
				"29"=>"红包奖励 "
			);
		}
			
			
		$GLOBALS['tmpl']->assign('title_array',$title_arrays);
	
		$times_array = array(
				"0" => "全部",
				"1" => "三天以内",
				"2" => "一周以内",
				"3" => "一月以内",
				"4" => "三月以内",
				"5" => "一年以内",
		);
		$GLOBALS['tmpl']->assign('times_array',$times_array);
		$user_id = intval($GLOBALS['user_info']['id']);
		
		$condition = "";
		
		if ($times== 1){
			$condition.=" and create_time_ymd >= '".to_date(TIME_UTC-3600*24*3,"Y-m-d")."' "; //三天以内
		}elseif ($times==2){
			$condition.="and create_time_ymd >= '".to_date(TIME_UTC - to_date(TIME_UTC,"w") * 24*3600 ,"Y-m-d")."'"; //一周以内
		}elseif ($times==3){
			$condition.=" and create_time_ym  = '".to_date(TIME_UTC,"Ym")."'";//一月以内
		}elseif ($times==4){
			$condition.=" and create_time_ym  >= '".to_date(next_replay_month(TIME_UTC , -2 ),"Ym" )."'";//三月以内
		}elseif ($times==5){
			$condition.=" and create_time_y  = '".to_date(TIME_UTC,"Y")."'";//一年以内
		}
		
		if ($type_title==100)
		{
			$type= -1;
		}
		else{
			$type = $type_title;
		}
		
		if($time_status==1){
			$time = isset($_REQUEST['time']) ? strim($_REQUEST['time']) : "";
			
			$time_f = to_date(to_timespan($time,"Ymd"),"Y-m-d");
			$condition.=" and create_time_ymd = '".$time_f."'";
			$GLOBALS['tmpl']->assign('time_normal',$time_f);
			$GLOBALS['tmpl']->assign('time',$time);
		}
		
		if(isset($t) && $t=="score"){
			$result = get_user_score_log($limit,$user_id,$type,$condition); //会员积分
		}
		
		foreach($result['list'] as $k=>$v){
			$result['list'][$k]['title'] = $title_arrays[$v['type']];
		}
		
		$GLOBALS['tmpl']->assign("type_title",$type_title);
		$GLOBALS['tmpl']->assign("times",$times);
		
		$GLOBALS['tmpl']->assign('time_status',$time_status);
		
		$GLOBALS['tmpl']->assign("list",$result['list']);
		$page = new Page($result['count'],app_conf("PAGE_SIZE"));   //初始化分页对象 		
		$p  =  $page->show();
		$GLOBALS['tmpl']->assign('pages',$p);
		
		$GLOBALS['tmpl']->assign("page_title","积分详情");
		
		$GLOBALS['tmpl']->assign("inc_file","inc/uc/uc_score_exchange_record.html");
		$GLOBALS['tmpl']->display("page/uc.html");
		
	}
	
}
?>