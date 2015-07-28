<?php
// +----------------------------------------------------------------------
// | Fanwe 方维p2p借贷系统
// +----------------------------------------------------------------------
// | Copyright (c) 2011 http://www.fanwe.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 云淡风轻(88522820@qq.com)
// +----------------------------------------------------------------------

require APP_ROOT_PATH.'app/Lib/deal.php';
class transfer_mobile
{
	public function index(){
		$root = array();
		//set_gopreview();
		
		$deal_id = intval($GLOBALS['request']['id']);
		$root['deal_id'] = $deal_id;
		
		if($deal_id==0){
			echo "不存在的债权"; die();
		}
		$deal = get_deal($deal_id);
		//syn_transfer_status($id);
		$deal['yq_count'] =  $GLOBALS['db']->getOne("SELECT count(*) FROM ".DB_PREFIX."deal_repay WHERE has_repay = 1 and deal_id=".$deal_id." AND status >= 2");
		$root['deal'] = $deal;
		//借款列表
		$load_list = $GLOBALS['db']->getAll("SELECT deal_id,user_id,user_name,money,is_auto,create_time FROM ".DB_PREFIX."deal_load WHERE deal_id = ".$deal_id);
	
		$u_info = get_user("*",$deal['user_id']);
		
		//可用额度
		$can_use_quota=get_can_use_quota($deal['user_id']);
		$root['can_use_quota'] = $can_use_quota;
		
		$credit_file = get_user_credit_file($deal['user_id']);
		$deal['is_faved'] = 0;

		$user_statics = sys_user_status($deal['user_id'],true);
		$root['user_statics'] = $user_statics;
		
		$root['load_list']= $load_list;
		$root['credit_file']= $credit_file;
		$root['u_info']= $u_info;
		
		//工作认证是否过期
		$root['expire']= user_info_expire($u_info);
		
		//留言
		$message_list = $GLOBALS['db']->getAll("SELECT title,content,a.create_time,rel_id,a.user_id,a.is_effect,b.user_name FROM ".DB_PREFIX."message as a left join ".DB_PREFIX."user as b on  a.user_id = b.id WHERE rel_id = ".$id);
		$root['message']= $message_list;
		
		/*
		//分页
		$page = intval($_REQUEST['p']);
		if($page==0)
			$page = 1;
		$limit = (($page-1)*app_conf("PAGE_SIZE")).",".app_conf("PAGE_SIZE");
		$msg_condition = $condition." AND is_effect = 1 ";
		$message = get_message_list($limit,$msg_condition);
		$page = new Page($message['count'],app_conf("PAGE_SIZE"));   //初始化分页对象
		$p  =  $page->show();
		$root['pages']= $p;
		*/
		
		foreach($message['list'] as $k=>$v){
			$msg_sub = get_message_list("","pid=".$v['id'],false);
			$message['list'][$k]["sub"] = $msg_sub["list"];
		}
		
		$root['message_list']= $message['list'];
		
		$condition = ' AND dlt.id='.$id.' AND d.deal_status >= 4 and d.is_effect=1 and d.is_delete=0  and d.repay_time_type =1 and  d.publish_wait=0 ';
		$union_sql = " LEFT JOIN ".DB_PREFIX."deal_load_transfer dlt ON dlt.deal_id = dl.deal_id ";
		
		$transfer = get_transfer($union_sql,$condition);
		$root['transfer']= $transfer;
		
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

