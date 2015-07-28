<?php
// +----------------------------------------------------------------------
// | Fanwe 方维p2p借贷系统
// +----------------------------------------------------------------------
// | Copyright (c) 2011 http://www.fanwe.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 云淡风轻(88522820@qq.com)
// +----------------------------------------------------------------------
//require APP_ROOT_PATH.'app/Lib/uc.php';
class uc_bank
{
	public function index(){
		
		$root = array();
		
		$email = strim($GLOBALS['request']['email']);//用户名或邮箱
		$pwd = strim($GLOBALS['request']['pwd']);//密码
		
		//检查用户,用户密码
		$user = user_check($email,$pwd);
		$user_id  = intval($user['id']);
		$root['user_id'] = $user_id;
		if ($user_id >0){
			require APP_ROOT_PATH.'app/Lib/uc_func.php';
			
			$root['user_login_status'] = 1;
			$root['response_code'] = 1;
			
			$bank_list = $GLOBALS['db']->getAll("SELECT u.id, u.bankcard,u.real_name, b.name as bank_name, b.id as bank_id FROM ".DB_PREFIX."user_bank u left join ".DB_PREFIX."bank b on b.id = u.bank_id where u.user_id=".$user_id." ORDER BY u.id ASC");
			foreach($bank_list as $k=>$v){
				$bank_list[$k]['bankcode'] = str_replace(" ","",$v['bankcard']);
				$bank_list[$k]['img'] = str_replace("/mapi","",SITE_DOMAIN.APP_ROOT.'/public/bank/'.$v['bank_id'].'.jpg');
			}

			$root['item'] = $bank_list;
			
			//手续费
			$fee_config = load_auto_cache("user_carry_config");
			$json_fee = array();
			foreach($fee_config as $k=>$v){
				$json_fee[] = $v;
				$fee_config[$k]['fee_format'] = format_price($v['fee']);
			}
			
			$root['fee_config'] = $fee_config;
			//$root['json_fee'] = json_encode($json_fee);
			$root['open_ips'] = intval(app_conf("OPEN_IPS"));
			$root['ips_acct_no'] = $user['ips_acct_no'];
			
			$root['idno'] = $user['idno'];//身份证号
			$root['real_name'] = $user['real_name'];
						
			//第三方托管标
			if (!empty($user['ips_acct_no']) && intval(app_conf("OPEN_IPS")) > 0){
				$result = GetIpsUserMoney($user_id,0);
					
				$root['ips_money'] = $result['pBalance'];
				
				//提现 http://p2p.fanwe.net/index.php?ctl=collocation&act=DoDwTrade&user_type=0&from=app&user_id=44&pTrdAmt=10
				$app_url = APP_ROOT."/index.php?ctl=collocation&act=DoDwTrade&user_type=0&pTrdAmt=parm_amt&user_id=".$user_id."&from=".$GLOBALS['request']['from'];
				$root['dw_url'] = str_replace("/mapi", "", SITE_DOMAIN.$app_url);
			}else{
				
				//申请
				$app_url = APP_ROOT."/index.php?ctl=collocation&act=CreateNewAcct&user_type=0&user_id=".$user_id."&from=".$GLOBALS['request']['from'];
				$root['acct_url'] = str_replace("/mapi", "", SITE_DOMAIN.$app_url);
				
				$root['ips_money'] = 0;
			}
			
			$root['ips_money_format'] = format_price($root['ips_money']); //预留 提现金额
			$root['ips_money_fee'] = 0;//预留 提现费用
			
			
		}else{
			$root['response_code'] = 0;
			$root['show_err'] ="未登录";
			$root['user_login_status'] = 0;
		}
		$root['program_title'] = "提现";
		output($root);		
	}
}
?>
