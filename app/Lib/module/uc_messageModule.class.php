<?php
// +----------------------------------------------------------------------
// | Fanwe 方维p2p借贷系统
// +----------------------------------------------------------------------
// | Copyright (c) 2011 http://www.fanwe.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 云淡风轻(88522820@qq.com)
// +----------------------------------------------------------------------

require APP_ROOT_PATH.'app/Lib/uc.php';
require APP_ROOT_PATH.'app/Lib/message.php';
class uc_messageModule extends SiteBaseModule
{
	public function index()
	{
		 
		$GLOBALS['tmpl']->assign("page_title",$GLOBALS['lang']['UC_MESSAGE']);
		$GLOBALS['tmpl']->assign("post_title",$GLOBALS['lang']['UC_MESSAGE']);
		//以下关于订单留言的输出
		$condition = " user_id = ".intval($GLOBALS['user_info']['id']);
	
		
		//message_form 变量输出
		$GLOBALS['tmpl']->assign('rel_table','feedback');
	
		
		//分页
		$page = intval($_REQUEST['p']);
		if($page==0)
		$page = 1;
		$limit = (($page-1)*app_conf("PAGE_SIZE")).",".app_conf("PAGE_SIZE");
		$message = get_message_list_shop($limit,$condition);
		
		$page = new Page($message['count'],app_conf("PAGE_SIZE"));   //初始化分页对象 		
		$p  =  $page->show();
		$GLOBALS['tmpl']->assign('pages',$p);
		
		$GLOBALS['tmpl']->assign("message_list",$message['list']);
		
	
		//end订单留言
		
		$GLOBALS['tmpl']->assign("inc_file","inc/message_form.html");
		$GLOBALS['tmpl']->display("page/uc.html");	
	}
}
?>