<?php
// +----------------------------------------------------------------------
// | Fanwe 方维p2p借贷系统
// +----------------------------------------------------------------------
// | Copyright (c) 2011 http://www.fanwe.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 云淡风轻(88522820@qq.com)
// +----------------------------------------------------------------------
//require APP_ROOT_PATH.'app/Lib/uc.php';
class uc_center
{
	public function index(){
		
		$root = array();
		
		$email = strim($GLOBALS['request']['email']);//用户名或邮箱
		$pwd = strim($GLOBALS['request']['pwd']);//密码
		
		//检查用户,用户密码
		$user = user_check($email,$pwd);
		$user_id  = intval($user['id']);
		if ($user_id >0){
			
			$root['user_login_status'] = 1;									
		
			$user['group_name'] = $GLOBALS['db']->getOne("select name from ".DB_PREFIX."user_group where id = ".$user['group_id']." ");
			
			$province_str = $GLOBALS['db']->getOne("select name from ".DB_PREFIX."region_conf where id = ".$user['province_id']);
			$city_str = $GLOBALS['db']->getOne("select name from ".DB_PREFIX."region_conf where id = ".$user['city_id']);
			if($province_str.$city_str=='')
				$user_location = $GLOBALS['lang']['LOCATION_NULL'];
			else
				$user_location = $province_str." ".$city_str;
			
			$user['user_location'] = $user_location;
			$user['money_format'] = format_price($user['money']);//可用资金			
			$user['lock_money_format'] = format_price($user['lock_money']);//冻结资金			
			$user['total_money'] = $user['money'] + $user['lock_money'];//资金余额
			$user['total_money_format'] = format_price($user['total_money']);//资金余额					
			$user['create_time_format'] = to_date($user['create_time'],'Y-m-d'); //注册时间
			
			
			$root['response_code'] = 1;
			$root['user_location'] = $user['user_location'];
			$root['user_name'] = $user['user_name'];
			$root['group_name'] = $user['group_name'];
			$root['money_format'] = $user['money_format'];
			$root['money'] = $user['money'];
			$root['lock_money_format'] = $user['lock_money_format'];
			$root['lock_money'] = $user['lock_money'];
			$root['total_money'] = $user['total_money'];
			$root['total_money_format'] = $user['total_money_format'];
			$root['create_time_format'] = $user['create_time_format'];
			
			$root['idno'] = $user['idno'];
			$root['real_name'] = $user['real_name'];
			
			if(intval(app_conf("OPEN_IPS")) > 0){
				$app_url = APP_ROOT."/index.php?ctl=collocation&act=CreateNewAcct&user_type=0&user_id=".$user_id."&from=".$GLOBALS['request']['from'];
				//申请
				$root['app_url'] = str_replace("/mapi", "", SITE_DOMAIN.$app_url);
				$root['acct_url'] = $root['app_url'];				
			}		
			
			$root['ips_acct_no'] = $user['ips_acct_no'];
			$root['open_ips'] = intval(app_conf("OPEN_IPS"));
			
			//第三方托管标
			if (!empty($user['ips_acct_no']) && intval(app_conf("OPEN_IPS")) > 0){
				$result = GetIpsUserMoney($user_id,0);
					
				$root['ips_balance'] = $result['pBalance'];//可用余额
				$root['ips_lock'] = $result['pLock'];//冻结余额
				$root['ips_needstl'] = $result['pNeedstl'];//未结算余额
			}else{
				$root['ips_balance'] = 0;//可用余额
				$root['ips_lock'] = 0;//冻结余额
				$root['ips_needstl'] = 0;//未结算余额
			}
			
			$root['ips_balance_format'] = format_price($root['ips_balance']);
			$root['ips_lock_format'] = format_price($root['ips_lock']);
			$root['ips_needstl_format'] = format_price($root['ips_needstl']);
			
		}else{
			$root['response_code'] = 0;
			$root['show_err'] ="未登录";
			$root['user_login_status'] = 0;
		}
		$root['program_title'] = "会员中心";
		output($root);		
	}
}
?>
