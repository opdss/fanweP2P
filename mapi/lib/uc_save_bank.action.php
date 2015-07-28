<?php
// +----------------------------------------------------------------------
// | Fanwe 方维p2p借贷系统
// +----------------------------------------------------------------------
// | Copyright (c) 2011 http://www.fanwe.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 云淡风轻(88522820@qq.com)
// +----------------------------------------------------------------------
//require APP_ROOT_PATH.'app/Lib/uc.php';
class uc_save_bank
{
	public function index(){
		
		$root = array();
		
		$email = strim($GLOBALS['request']['email']);//用户名或邮箱
		$pwd = strim($GLOBALS['request']['pwd']);//密码
		
		//检查用户,用户密码
		$user = user_check($email,$pwd);
		$user_id  = intval($user['id']);
		if ($user_id >0){

			$root['response_code'] = 1;
			$root['user_login_status'] = 1;
			
			$data['bank_id'] = intval($GLOBALS['request']['bank_id']);
			$data['user_id'] = $user_id;			
			
			if($root['response_code'] == 1 && $data['bank_id'] == 0)
			{
				$root['response_code'] = 0;
				$root['show_err'] = $GLOBALS['lang']['PLASE_ENTER_CARRY_BANK'];
			}
			
			$data['real_name'] = $user['real_name'];
			
			
			$data['region_lv1'] = 0;
			$data['region_lv2'] = 0;
			$data['region_lv3'] = 0;
			$data['region_lv4'] = 0;
			
			$data['bankzone'] = trim($GLOBALS['request']['bankzone']);
			if($root['response_code'] == 1 && $data['bankzone'] == ""){
				$root['response_code'] = 0;
				$root['show_err'] = "请输入开户行网点";
			}
			
			$data['bankcard'] = trim($GLOBALS['request']['bankcard']);
			if($root['response_code'] == 1 && $data['bankcard'] == ""){
				$root['response_code'] = 0;
				$root['show_err'] = $GLOBALS['lang']['PLASE_ENTER_CARRY_BANK_CODE'];
			}					
			
			if($root['response_code'] == 1 && $GLOBALS['db']->getOne("SELECT count(*) FROM ".DB_PREFIX."user_bank WHERE bankcard='".$data['bankcard']."'  AND user_id=".$user_id) > 0){
				$root['response_code'] = 0;
				$root['show_err'] = "该银行卡已存在";
			}
			
			if($root['response_code'] == 1){
				$GLOBALS['db']->autoExecute(DB_PREFIX."user_bank",$data,"INSERT");
				
				if($GLOBALS['db']->affected_rows()){
					$root['response_code'] = 1;
					$root['show_err'] = "保存成功";
				}else{
					$root['response_code'] = 0;
					$root['show_err'] = "保存失败";
				}
			}				
		}else{
			$root['response_code'] = 0;
			$root['show_err'] ="未登录";
			$root['user_login_status'] = 0;
		}
		output($root);		
	}
}
?>
