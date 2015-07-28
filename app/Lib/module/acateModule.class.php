<?php
// +----------------------------------------------------------------------
// | Fanwe 方维p2p借贷系统
// +----------------------------------------------------------------------
// | Copyright (c) 2011 http://www.fanwe.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 云淡风轻(88522820@qq.com)
// +----------------------------------------------------------------------

require APP_ROOT_PATH.'app/Lib/page.php';
class acateModule extends SiteBaseModule
{
	public function index()
	{
		$GLOBALS['tmpl']->caching = true;
		$cache_id  = md5(MODULE_NAME.ACTION_NAME.trim($_REQUEST['id']).intval($_REQUEST['p']));		
		if (!$GLOBALS['tmpl']->is_cached('page/acate_index.html', $cache_id))	
		{		
			$id = intval($_REQUEST['id']);
			$cate_item = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."article_cate where id = ".$id." and is_effect = 1 and is_delete = 0");
			
			if($id>0&&!$cate_item)
			{
				app_redirect(APP_ROOT."/");
			}
			elseif($cate_item['type_id']!=0)
			{
				if($cate_item['type_id']==1)
				app_redirect(url("index","help#index"));
				if($cate_item['type_id']==2)
				app_redirect(url("index","notice#list"));
				if($cate_item['type_id']==3)
				app_redirect(url("index","sys#alist"));
			}
			
			$cate_id = intval($cate_item['id']);
			$cate_tree = get_acate_tree();		
			$GLOBALS['tmpl']->assign("acate_tree",$cate_tree);	
			$cate = null;
			foreach($cate_tree as $k=>$v){
				if($id == $v['id']){
					$cate = $v;
				}
			}		
			
			$GLOBALS['tmpl']->assign("cate",$cate);
	
			//分页
			$page = intval($_REQUEST['p']);
			if($page==0)
			$page = 1;
			$limit = (($page-1)*app_conf("PAGE_SIZE")).",".app_conf("PAGE_SIZE");		
			$result = get_article_list($limit,$cate_id,'ac.type_id = 0','');
			
			$GLOBALS['tmpl']->assign("list",$result['list']);
			$page = new Page($result['count'],app_conf("PAGE_SIZE"));   //初始化分页对象 		
			$p  =  $page->show();
			$GLOBALS['tmpl']->assign('pages',$p);
			
			
			//开始输出当前的site_nav
			$cates = array();
			$cate = $cate_item;
			do
			{
				$cates[] = $cate;
				$pid = intval($cate['pid']);
				$cate = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."article_cate where is_effect =1 and is_delete =0 and id = ".$pid);			
				
			}while($pid!=0);
			
			foreach($cates as $cate_row)
			{
				$page_title .= $cate_row['title']." - "; 
				$page_kd .= $cate_row['title'].",";
			}
			$page_title = substr($page_title,0,-3);
			krsort($cates);
			
			$site_nav[] = array('name'=>$GLOBALS['lang']['HOME_PAGE'],'url'=>APP_ROOT."/");
			if($cate_item)
			{
				foreach($cates as $cate_row)
				{
					$site_nav[] = array('name'=>$cate_row['title'],'url'=>url("index","acate#index",array("id"=>$cate_row['id'])));
				}
			}		
			else
			{
				$site_nav[] = array('name'=>$GLOBALS['lang']['ARTICLE_CATE'],'url'=>url("index","acate#index"));
			}
			$GLOBALS['tmpl']->assign("site_nav",$site_nav);
			//输出当前的site_nav
			
			
			$GLOBALS['tmpl']->assign("page_title",$cate_item['title']);
			$GLOBALS['tmpl']->assign("page_keyword",$cate_item['title'].",");
			$GLOBALS['tmpl']->assign("page_description",$cate_item['title'].",");
		}
		$GLOBALS['tmpl']->display("page/acate_index.html",$cache_id);
	}
}
?>