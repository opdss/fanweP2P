<?php
// +----------------------------------------------------------------------
// | Fanwe 方维p2p借贷系统
// +----------------------------------------------------------------------
// | Copyright (c) 2011 http://www.fanwe.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 云淡风轻(88522820@qq.com)
// +----------------------------------------------------------------------
//require APP_ROOT_PATH.'app/Lib/uc.php';
class uc_account_log
{
	public function index(){
		
		$root = array();
		
		$email = strim($GLOBALS['request']['email']);//用户名或邮箱
		$pwd = strim($GLOBALS['request']['pwd']);//密码
		
		$page = intval($GLOBALS['request']['page']);
		
		
		//检查用户,用户密码
		$user = user_check($email,$pwd);
		$user_id  = intval($user['id']);
		if ($user_id >0){
			require APP_ROOT_PATH.'app/Lib/uc_func.php';
			
			$root['user_login_status'] = 1;
			$root['response_code'] = 1;
			
			if($page==0)
				$page = 1;
			$limit = (($page-1)*app_conf("PAGE_SIZE")).",".app_conf("PAGE_SIZE");
	
			$status = intval($GLOBALS['request']['status']);
			$root['status'] = $status;
		//	$status 1 冻结资金日志,2 信用积分日志,3 积分日志,其他 资金日志
		if($status=="2"){
			$title_arrays = array(
				"100" => "全部",	
				"0" => "结存",	
				"4" => "偿还本息",	
				"5" => "回收本息",	
				"6" => "提前还款",	
				"7" => "提前回收",	
				"8" => "申请认证",	
				"11" => "逾期还款",
				"13" => "人工操作",
				"14" => "借款服务费",
				"18" => "开户奖励",
				//"22" => "兑换",
				"23" => "邀请返利",	
				"24" => "投标返利",
				"25" => "签到成功",
			);
		}
		elseif($status=="1"){
			$title_arrays = array(
				"100" => "全部",
				"0" => "结存",
				"1" => "充值",
				"2" => "投标成功",
				"8" => "申请提现",
				"9" => "提现手续费",
				"13" => "人工操作",
				"18" => "开户奖励",
				"19" => "流标还返",
			);
		}
		elseif($status=="3"){
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
		else{
			$title_arrays = array(
				"100" => "全部",
				"0" => "结存",
				"1" => "充值",
				"2" => "投标成功",
				"3" => "招标成功",
				"4" => "偿还本息",
				"5" => "回收本息",
				"6" => "提前还款",
				"7" => "提前回收",
				"8" => "申请提现",
				"9" => "提现手续费",
				"10" => "借款管理费",
				"11" => "逾期罚息",
				"12" => "逾期管理费",
				"13" => "人工充值",
				"14" => "借款服务费",
				"15" => "出售债权",
				"16" => "购买债权",
				"17" => "债权转让管理费",
				"18" => "开户奖励",
				"19" => "流标还返",
				"20" => "投标管理费",
				"21" => "投标逾期收入",
				"22" => "兑换",
				"23" => "邀请返利",
				"24" => "投标返利",
				"25" => "签到成功",
				"26" => "逾期罚金（垫付后）",
				"27" => "其他费用",
				"28"=>"投资奖励 ",
				"29"=>"红包奖励 "
			);
		}		
			
			
			if(isset($status) && $status=="2"){
				$result = get_user_point_log($limit,$GLOBALS['user_info']['id'],-1); //会员信用积分
			}elseif(isset($status) && $status=="1"){
				$result = get_user_lock_money_log($limit,$GLOBALS['user_info']['id'],-1); //会员冻结资金日志
			}elseif(isset($status) && $status=="3"){
				$result = get_user_score_log($limit,$GLOBALS['user_info']['id'],-1); //会员积分日志
			}else{
				$result = get_user_money_log($limit,$GLOBALS['user_info']['id'],-1); //会员资金日志
			}
			
			foreach($result['list'] as $k=>$v){
				$result['list'][$k]['title'] = $title_arrays[$v['type']];
			}
			
			$list = $result['list'];

			$root['item'] = $list;
			
			$root['page'] = array("page"=>$page,"page_total"=>ceil($result['count']/app_conf("PAGE_SIZE")),"page_size"=>app_conf("PAGE_SIZE"));
			
		}else{
			$root['response_code'] = 0;
			$root['show_err'] ="未登录";
			$root['user_login_status'] = 0;
		}
		$root['program_title'] = "帐户日志";
		output($root);		
	}
}
?>
