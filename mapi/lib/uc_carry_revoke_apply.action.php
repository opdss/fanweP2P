<?php
// +----------------------------------------------------------------------
// | Fanwe 方维p2p借贷系统
// +----------------------------------------------------------------------
// | Copyright (c) 2011 http://www.fanwe.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 云淡风轻(88522820@qq.com)
// +----------------------------------------------------------------------
//require APP_ROOT_PATH.'app/Lib/uc.php';
class uc_carry_revoke_apply
{
	public function index(){
		
		$root = array();
		
		$email = strim($GLOBALS['request']['email']);//用户名或邮箱
		$pwd = strim($GLOBALS['request']['pwd']);//密码
		
		$page = intval($GLOBALS['request']['page']);
		$dltid = intval($GLOBALS['request']['dltid']);	
		$status = intval($GLOBALS['request']['status']);	
			
		//检查用户,用户密码
		$user = user_check($email,$pwd);
		$user_id  = intval($user['id']);
		if ($user_id >0){
			require APP_ROOT_PATH.'app/Lib/uc_func.php';
			require APP_ROOT_PATH.'system/libs/user.php';
			$root['user_login_status'] = 1;
			$root['response_code'] = 1;
			
			$nmc_amount = $GLOBALS['db']->getOne("SELECT nmc_amount FROM ".DB_PREFIX."user where id = ".intval($GLOBALS['user_info']['id']));
			
			if($status == 0){
				$GLOBALS['db']->query("UPDATE ".DB_PREFIX."user_carry SET status=4 where id=".$dltid." and status=0  and user_id = ".intval($GLOBALS['user_info']['id']));
				if($GLOBALS['db']->affected_rows()){
					$data = $GLOBALS['db']->getRow("SELECT * FROM ".DB_PREFIX."user_carry where id=".$dltid." and status=4 and user_id = ".intval($GLOBALS['user_info']['id']));
					modify_account(array('money'=>$data['money'],'lock_money'=>-$data['money']),$data['user_id'],"撤销提现,提现金额",8);
					modify_account(array('money'=>$data['fee'],'lock_money'=>-$data['fee']),$data['user_id'],"撤销提现，提现手续费",9);
					$root['show_err'] = "撤销操作成功";
				}
				else{
					$root['show_err'] = "撤销操作失败";
				}
				output($root);
			}elseif($status == 4){
				$data = $GLOBALS['db']->getRow("SELECT user_id,money,fee FROM ".DB_PREFIX."user_carry where id=".$dltid." and status=4 and user_id = ".intval($GLOBALS['user_info']['id']));
				if(((float)$data['money'] + (float)$data['fee'] + (float)$GLOBALS['user_info']['nmc_amount']) > (float)$GLOBALS['user_info']['money']){
					$root['show_err'] = "继续申请提现失败,金额不足";
				}
				
				$sql = "UPDATE ".DB_PREFIX."user_carry SET status=0 where id=".$dltid." and (money + fee + $nmc_amount) <= ".(float)$GLOBALS['user_info']['money']." and status=4 and user_id = ".intval($GLOBALS['user_info']['id'])."  ";
				$root['sql'] = $sql;
				$GLOBALS['db']->query($sql);
				if($GLOBALS['db']->affected_rows()){
					modify_account(array('money'=>-$data['money'],'lock_money'=>$data['money']),$data['user_id'],"提现申请",8);
					modify_account(array('money'=>-$data['fee'],'lock_money'=>$data['fee']),$data['user_id'],"提现手续费",9);
					$root['show_err'] = "继续申请提现成功";
				}
				else{
					$root['show_err'] = "继续申请提现失败";
				}
				output($root);
			}else{
				$root['show_err'] = "操作失败";
				output($root);
			}
			
		}else{
			$root['response_code'] = 0;
			$root['show_err'] ="未登录";
			$root['user_login_status'] = 0;
		}
		$root['program_title'] = "提现操作";
		output($root);		
	}
}
?>
