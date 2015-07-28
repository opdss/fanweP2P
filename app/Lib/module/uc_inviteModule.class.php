<?php
// +----------------------------------------------------------------------
// | Fanwe 方维p2p借贷系统
// +----------------------------------------------------------------------
// | Copyright (c) 2011 http://www.fanwe.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 云淡风轻(88522820@qq.com)
// +----------------------------------------------------------------------

require APP_ROOT_PATH.'app/Lib/uc.php';

class uc_inviteModule extends SiteBaseModule
{
	public function index()
	{
		
		$total_referral_money = $GLOBALS['db']->getOne("select sum(money) from ".DB_PREFIX."referrals where rel_user_id = ".$GLOBALS['user_info']['id']." and pay_time > 0");
		
		$GLOBALS['tmpl']->assign("total_referral_money",$total_referral_money);
		
		$referral_user = $GLOBALS['db']->getOne("select count(*) from ".DB_PREFIX."user where pid = ".$GLOBALS['user_info']['id']." and is_effect=1 and is_delete=0 AND user_type in(0,1) ");
		$GLOBALS['tmpl']->assign("referral_user",$referral_user);
		
		$GLOBALS['tmpl']->assign("page_title",$GLOBALS['lang']['UC_INVITE']);
		$GLOBALS['tmpl']->assign("inc_file","inc/uc/uc_invite_index.html");
		
		if(intval(app_conf("URL_MODEL")) == 0)
			$depart="&";
		else
			$depart="?";	
		$share_url = SITE_DOMAIN.url("index","user#register");
		if($GLOBALS['user_info']){
			$share_url_mobile = $share_url.$depart."r=".base64_encode($GLOBALS['user_info']['mobile']);
			$share_url_username = $share_url.$depart."r=".base64_encode($GLOBALS['user_info']['user_name']);
		}
			
		$GLOBALS['tmpl']->assign("share_url_mobile",$share_url_mobile);
		$GLOBALS['tmpl']->assign("share_url_username",$share_url_username);		
				
		$GLOBALS['tmpl']->display("page/uc.html");
	}
	
	function invite(){
		$type = intval($_REQUEST['type']);
		$page = intval($_REQUEST['p']);
		if($page==0)
			$page = 1;
		
		$limit = (($page-1)*app_conf("PAGE_SIZE")).",".app_conf("PAGE_SIZE");
		$result = get_invite_list($limit,$GLOBALS['user_info']['id'],$type);
		
		$GLOBALS['tmpl']->assign("list",$result['list']);
		$page = new Page($result['count'],app_conf("PAGE_SIZE"));   //初始化分页对象 		
		$p  =  $page->show();
		$GLOBALS['tmpl']->assign('pages',$p);
		$GLOBALS['tmpl']->assign("type",$type);
		$GLOBALS['tmpl']->assign("inc_file","inc/uc/uc_invite_list.html");
		$GLOBALS['tmpl']->display("page/uc.html");
	}
	
	function reward(){
		$page = intval($_REQUEST['p']);
		if($page==0)
			$page = 1;
		$limit = (($page-1)*app_conf("PAGE_SIZE")).",".app_conf("PAGE_SIZE");
		
		$result['count'] = $GLOBALS['db']->getOne("SELECT count(*) FROM ".DB_PREFIX."referrals WHERE rel_user_id=".$GLOBALS['user_info']['id']." ORDER BY id DESC");
		if($result['count'] > 0){
			$result['list'] = $GLOBALS['db']->getAll("SELECT r.*,u.user_name as re_user_name,u.referral_rate FROM ".DB_PREFIX."referrals r LEFT JOIN ".DB_PREFIX."user u ON u.id = r.user_id WHERE r.rel_user_id=".$GLOBALS['user_info']['id']." ORDER BY id DESC");
		}
		
		$GLOBALS['tmpl']->assign("list",$result['list']);
		$page = new Page($result['count'],app_conf("PAGE_SIZE"));   //初始化分页对象 		
		$p  =  $page->show();
		$GLOBALS['tmpl']->assign('pages',$p);
		$GLOBALS['tmpl']->assign("inc_file","inc/uc/uc_invite_reward.html");
		$GLOBALS['tmpl']->display("page/uc.html");
	}
}
?>