<?php
// +----------------------------------------------------------------------
// | Fanwe 方维p2p借贷系统
// +----------------------------------------------------------------------
// | Copyright (c) 2011 http://www.fanwe.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 云淡风轻(88522820@qq.com)
// +----------------------------------------------------------------------
//require APP_ROOT_PATH.'app/Lib/uc.php';
class uc_lend
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
			
			$count = $GLOBALS['db']->getOne("SELECT count(*) FROM ".DB_PREFIX."deal_load WHERE user_id=".$user_id);
			$list = $GLOBALS['db']->getAll("SELECT dl.*,d.rate,d.repay_time,d.repay_time_type,d.deal_status,d.name FROM ".DB_PREFIX."deal_load dl LEFT JOIN ".DB_PREFIX."deal d ON dl.deal_id = d.id WHERE dl.user_id=".$user_id);
	
			foreach($list as $k=>$v){
				
			
				//年利率
				$list[$k]['rate_format'] =  round($v['rate'],2);
				//投标金额
				//$list[$k]['money_format'] =  format_price($list[$k]['money']);
				
				$list[$k]['money_format'] = format_price($v['money']);//format_price($deal['borrow_amount']);
				$list[$k]['rate_foramt_w'] = number_format($v['rate'],2)."%";
				
				
				//借款期限				
				if ($v['repay_time_type'] == 0){
					$list[$k]['repay_time_format'] =  $v['repay_time'].'天';
				}else{
					$list[$k]['repay_time_format'] =  $v['repay_time'].'个月';
				}				
				//投标时间
				$list[$k]['create_time_format'] =  to_date($v['create_time'],'Y-m-d H:i');
				
				//状态
				if ($v['deal_status'] == 2 || $v['deal_status'] == 4 || $v['deal_status'] == 5){
					$list[$k]['deal_status_format'] =  '成功';
				}else if ($v['deal_status'] == 2){
					$list[$k]['deal_status_format'] =  '流标';
				}else{
					$list[$k]['deal_status_format'] =  '进行中';
				}
				
				//$durl = url("index","deal",array("id"=>$list[$k]['deal_id']));
				//$deal['url'] = $durl;
				$durl = "/index.php?ctl=deal&act=mobile&id=".$v['deal_id'];
				$list[$k]['app_url'] = str_replace("/mapi", "", SITE_DOMAIN.$durl);				
			}			
			 
			$root['item'] = $list;
			$root['page'] = array("page"=>$page,"page_total"=>ceil($count/app_conf("PAGE_SIZE")),"page_size"=>app_conf("PAGE_SIZE"));
			
		}else{
			$root['response_code'] = 0;
			$root['show_err'] ="未登录";
			$root['user_login_status'] = 0;
		}
		$root['program_title'] = "投资记录";
		output($root);		
	}
}
?>
