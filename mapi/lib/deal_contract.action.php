<?php
// +----------------------------------------------------------------------
// | Fanwe 方维p2p借贷系统
// +----------------------------------------------------------------------
// | Copyright (c) 2011 http://www.fanwe.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 云淡风轻(88522820@qq.com)
// +----------------------------------------------------------------------

class deal_contract
{
	public function index(){
		$root = array();
		
		$email = strim($GLOBALS['request']['email']);//用户名或邮箱
		$pwd = strim($GLOBALS['request']['pwd']);//密码
		$id = intval($GLOBALS['request']['id']);
		
		//检查用户,用户密码
		$user = user_check($email,$pwd);
		$user_id  = intval($user['id']);
		if ($user_id >0){
			require APP_ROOT_PATH.'app/Lib/uc.php';
			require APP_ROOT_PATH.'app/Lib/deal.php';
		//	require_once APP_ROOT_PATH.'system/common.php';
			$root['user_login_status'] = 1;
				
			$root['response_code'] = 1;
			if($id == 0){
				showErr("操作失败！");
			}
			$deal = get_deal($id);
			if(!$deal){
				showErr("操作失败！");
			}
			$load_user_id = $GLOBALS['db']->getOne("select user_id FROM ".DB_PREFIX."deal_load WHERE deal_id=".$id." and user_id=".$GLOBALS['user_info']['id']." ORDER BY create_time ASC");
			if($load_user_id == 0  && $deal['user_id']!=$GLOBALS['user_info']['id'] ){
				showErr("操作失败！");
			}
					
			//$root['deal'] = $deal;
			
			
			$loan_list = $GLOBALS['db']->getAll("select * FROM ".DB_PREFIX."deal_load WHERE deal_id=".$id." ORDER BY create_time ASC");
			foreach($loan_list as $k=>$v){
				$vv_deal['borrow_amount'] = $v['money'];
				$vv_deal['rate'] = $deal['rate'];
				$vv_deal['repay_time'] = $deal['repay_time'];
				$vv_deal['loantype'] = $deal['loantype'];
				$vv_deal['repay_time_type'] = $deal['repay_time_type'];
				
				$deal_rs =  deal_repay_money($vv_deal);
				$loan_list[$k]['get_repay_money'] = $deal_rs['month_repay_money'];
				if(is_last_repay($deal['loantype']))
					$loan_list[$k]['get_repay_money'] = $deal_rs['remain_repay_money'];
			}
			
			//$root['loan_list'] = $loan_list;
			
			$u_info = get_user("*",$deal['user_id']);
			//$root['user_info'] = $u_info;
			
			if($u_info['sealpassed'] == 1){
				$credit_file = get_user_credit_file($deal['user_id']);
				//$GLOBALS['tmpl']->assign('user_seal_url',$credit_file['credit_seal']['file_list'][0]);
				$root['user_seal_url'] = $credit_file['credit_seal']['file_list'][0];
				
			}
			
//			$GLOBALS['tmpl']->assign('SITE_URL',str_replace(array("https://","http://"),"",SITE_DOMAIN));
//			$GLOBALS['tmpl']->assign('SITE_TITLE',app_conf("SITE_TITLE"));
//			$GLOBALS['tmpl']->assign('CURRENCY_UNIT',app_conf("CURRENCY_UNIT"));

//			$root['SITE_URL'] = str_replace(array("https://","http://"),"",SITE_DOMAIN);
//			$root['SITE_TITLE'] = app_conf("SITE_TITLE");
//			$root['CURRENCY_UNIT'] = app_conf("CURRENCY_UNIT");
			
			$contract = $GLOBALS['tmpl']->fetch("str:".get_contract($deal['contract_id']));
			$root['contract'] = $contract;
			
			
		}else{
			$root['response_code'] = 0;
			$root['show_err'] ="未登录";
			$root['user_login_status'] = 0;
		}
		
		output($root);		
	}
}
?>
