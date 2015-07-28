<?php
// +----------------------------------------------------------------------
// | Fanwe 方维p2p借贷系统
// +----------------------------------------------------------------------
// | Copyright (c) 2011 http://www.fanwe.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 云淡风轻(88522820@qq.com)
// +----------------------------------------------------------------------

	require_once APP_ROOT_PATH."app/Lib/page.php";
	
	if(!$GLOBALS['user_info'] && isset($_REQUEST['user_name']) && $_REQUEST['user_pwd'])
	{
		require_once APP_ROOT_PATH."system/libs/user.php";		
		do_login_user(strim($_REQUEST['user_name']),strim($_REQUEST['user_pwd']));
	}
	
	if($GLOBALS['user_info'])
	{
		$user_id = intval($GLOBALS['user_info']['id']);
		$c_user_info = $GLOBALS['user_info'];
		//$c_user_info = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."user where id = ".intval($GLOBALS['user_info']['id']));
		$c_user_info['user_group'] = $GLOBALS['db']->getOne("select name from ".DB_PREFIX."user_group where id = ".intval($GLOBALS['user_info']['group_id']));
		//$c_user_info['referral_total'] = $GLOBALS['db']->getOne("select referral_count from ".DB_PREFIX."user where id = ".intval($GLOBALS['user_info']['id']));
		$GLOBALS['tmpl']->assign("user_info",$c_user_info);
		
		//签到数据
		$t_begin_time = to_timespan(to_date(TIME_UTC,"Y-m-d"));  //今天开始
		$t_end_time = to_timespan(to_date(TIME_UTC,"Y-m-d"))+ (24*3600 - 1);  //今天结束
		$y_begin_time = $t_begin_time - (24*3600); //昨天开始
		$y_end_time = $t_end_time - (24*3600);  //昨天结束
		
		$t_sign_data = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."user_sign_log where user_id = ".$user_id." and sign_date between ".$t_begin_time." and ".$t_end_time);
		if($t_sign_data)
		{			
			$GLOBALS['tmpl']->assign("t_sign_data",$t_sign_data);
		}
		else
		{
			$y_sign_data = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."user_sign_log where user_id = ".$user_id." and sign_date between ".$y_begin_time." and ".$y_end_time);
			$total_signcount = $GLOBALS['db']->getOne("select count(*) from ".DB_PREFIX."user_sign_log where user_id = ".$user_id);
			if($y_sign_data&&$total_signcount>=3)
			{				
				$tip = "";
				if(doubleval(app_conf("USER_LOGIN_KEEP_MONEY"))>0)
				$tip .= "资金+".format_price(app_conf("USER_LOGIN_KEEP_MONEY"));
				if(intval(app_conf("USER_LOGIN_KEEP_SCORE"))>0)
				$tip .= "积分+".format_score(app_conf("USER_LOGIN_KEEP_SCORE"));
				if(intval(app_conf("USER_LOGIN_KEEP_POINT"))>0)
				$tip .= "经验+".(app_conf("USER_LOGIN_KEEP_POINT"));
				$GLOBALS['tmpl']->assign("sign_tip",$tip);					
			}
			else
			{
				if(!$y_sign_data)
				$GLOBALS['db']->query("delete from ".DB_PREFIX."user_sign_log where user_id = ".$user_id);
				$tip = "";
				if(doubleval(app_conf("USER_LOGIN_MONEY"))>0)
				$tip .= "资金+".format_price(app_conf("USER_LOGIN_MONEY"));
				if(intval(app_conf("USER_LOGIN_SCORE"))>0)
				$tip .= "积分+".format_score(app_conf("USER_LOGIN_SCORE"));
				if(intval(app_conf("USER_LOGIN_POINT"))>0)
				$tip .= "信用+".(app_conf("USER_LOGIN_POINT"));
				$GLOBALS['tmpl']->assign("sign_tip",$tip);
			}
			$GLOBALS['tmpl']->assign("sign_day",$total_signcount);
			$GLOBALS['tmpl']->assign("y_sign_data",$y_sign_data);
		}
	}
	else
	{
		if($_REQUEST['ajax']==1)
		{
			ajax_return(array("status"=>0,"info"=>"请先登录"));
		}
		else
		{
			if(strstr($_REQUEST[CTL],"debit"))
			{
				es_session::set('before_login',$_SERVER['REQUEST_URI']);
				app_redirect(url("debit","debit_user#login"));
			}
			else{
				es_session::set('before_login',$_SERVER['REQUEST_URI']);
				app_redirect(url("index","user#login"));
			}
		}
	}
	
	require_once APP_ROOT_PATH."app/Lib/uc_func.php";
?>