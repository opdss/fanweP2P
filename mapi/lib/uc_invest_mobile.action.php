<?php
// +----------------------------------------------------------------------
// | Fanwe 方维p2p借贷系统
// +----------------------------------------------------------------------
// | Copyright (c) 2011 http://www.fanwe.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 云淡风轻(88522820@qq.com)
// +----------------------------------------------------------------------

require APP_ROOT_PATH.'app/Lib/deal.php';
class uc_invest_mobile
{
	public function index(){
		
		$root = array();
		$id = intval($GLOBALS['request']['id']);
		
		$deal = get_deal($id);
		//send_deal_contract_email($id,$deal,$deal['user_id']);  //发送电子协议邮件
		$root['deal']= $deal;
		
		//借款列表
		$load_list = $GLOBALS['db']->getAll("SELECT deal_id,user_id,user_name,money,is_auto,create_time FROM ".DB_PREFIX."deal_load WHERE deal_id = ".$id);
		$u_info = get_user("*",$deal['user_id']);
	
		//可用额度
		$can_use_quota=get_can_use_quota($deal['user_id']);
		$root['can_use_quota']= $can_use_quota;
		
		$credit_file = get_user_credit_file($deal['user_id']);
		$deal['is_faved'] = 0;
		
		/*
		if($GLOBALS['user_info']){
			$deal['is_faved'] = $GLOBALS['db']->getOne("SELECT count(*) FROM ".DB_PREFIX."deal_collect WHERE deal_id = ".$id." AND user_id=".intval($GLOBALS['user_info']['id']));
				
			if($deal['deal_status'] >=4){
				//还款列表
				$loan_repay_list = get_deal_load_list($deal);
				
				$root['loan_repay_list']= $loan_repay_list;
				
				
				foreach($load_list as $k=>$v){
					$load_list[$k]['remain_money'] = $v['money'] - $GLOBALS['db']->getOne("SELECT sum(self_money) FROM ".DB_PREFIX."deal_load_repay WHERE user_id=".$v['user_id']." AND deal_id=".$id);
					if($load_list[$k]['remain_money'] <=0){
						$load_list[$k]['remain_money'] = 0;
						$load_list[$k]['status'] = 1;
					}
				}
			}
			
		}*/
	
		$user_statics = sys_user_status($deal['user_id'],true);
		$root['user_statics']= $user_statics;//借款笔数
		
		$root['load_list']= $load_list;
		$root['credit_file']= $credit_file;
		$root['u_info']= $u_info;
	
		//工作认证是否过期  
		$root['expire']= user_info_expire($u_info);
		//留言
		$message_list = $GLOBALS['db']->getAll("SELECT title,content,a.create_time,rel_id,a.user_id,a.is_effect,b.user_name FROM ".DB_PREFIX."message as a left join ".DB_PREFIX."user as b on  a.user_id = b.id WHERE rel_id = ".$id);
		$root['message']= $message_list;
		
		
		
		//seo
		if($deal['type_match_row'])
			$seo_title = $deal['seo_title']!=''?$deal['seo_title']:$deal['type_match_row'] . " - " . $deal['name'];
		else
			$seo_title = $deal['seo_title']!=''?$deal['seo_title']: $deal['name'];
		$root['page_title']= $seo_title;
		$seo_keyword = $deal['seo_keyword']!=''?$deal['seo_keyword']:$deal['type_match_row'].",".$deal['name'];
		$root['page_keyword']= $seo_keyword;
		$seo_description = $deal['seo_description']!=''?$deal['seo_description']:$deal['name'];
		$root['seo_description']= $seo_description;
	
		output($root);		
	}
}
?>

