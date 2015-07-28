<?php
// +----------------------------------------------------------------------
// | Fanwe 方维p2p借贷系统
// +----------------------------------------------------------------------
// | Copyright (c) 2011 http://www.fanwe.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 云淡风轻(88522820@qq.com)
// +----------------------------------------------------------------------

require APP_ROOT_PATH.'app/Lib/uc.php';
class uc_medalModule extends SiteBaseModule
{
	public function index()
	{		 
		$GLOBALS['tmpl']->assign("page_title",$GLOBALS['lang']['UC_MEDAL']);
		$GLOBALS['tmpl']->assign("post_title",$GLOBALS['lang']['UC_MEDAL']);	
		
	
		$user_id = intval($GLOBALS['user_info']['id']);
		
		$list = $GLOBALS['db']->getAll("select * from ".DB_PREFIX."medal where is_effect = 1 ");
		$GLOBALS['tmpl']->assign('list',$list);		
		
		$my_list = $GLOBALS['db']->getAll("select * from ".DB_PREFIX."user_medal where user_id = ".$user_id." and is_delete = 0 order by create_time desc");
		$GLOBALS['tmpl']->assign('my_list',$my_list);	
		
		$GLOBALS['tmpl']->assign("inc_file","inc/uc/uc_medal_index.html");
		$GLOBALS['tmpl']->display("page/uc.html");	
	}
	
	
	public function load_medal()
	{
		$id = intval($_REQUEST['id']);
		$medal = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."medal where id = ".$id);
		$GLOBALS['tmpl']->assign("medal",$medal);
		$GLOBALS['tmpl']->display("inc/load_medal.html");
	}
	
	public function get_medal()
	{
		$id = intval($_REQUEST['id']);
		$medal = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."medal where id = ".$id);
		$file = APP_ROOT_PATH."system/medal/".$medal['class_name']."_medal.php";
		$cls = $medal['class_name']."_medal";
		$result['status'] = 0;
		$result['info'] = "勋章不存在";
		
		if(file_exists($file))
		{
			require_once $file;
			if(class_exists($cls))
			{
				$o = new $cls;
				$result = $o->get_medal();
			}
		}
		ajax_return($result);
	}
	
}
?>