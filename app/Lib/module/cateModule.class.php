<?php
// +----------------------------------------------------------------------
// | Fanwe 方维p2p借贷系统
// +----------------------------------------------------------------------
// | Copyright (c) 2011 http://www.fanwe.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 云淡风轻(88522820@qq.com)
// +----------------------------------------------------------------------

require APP_ROOT_PATH.'app/Lib/page.php';

class cateModule extends SiteBaseModule
{
	public function index()
	{
		$GLOBALS['tmpl']->caching = true;
		$cache_id  = md5(MODULE_NAME.ACTION_NAME.trim($_REQUEST['id']).$GLOBALS['deal_city']['id']);		
		if (!$GLOBALS['tmpl']->is_cached('goods_list.html', $cache_id))		
		{
			$id = intval($_REQUEST['id']);
			if($id==0)
			$uname = addslashes(trim($_REQUEST['id']));
			$cate_item = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."deal_cate where id = ".$id." or (uname = '".$uname."' and uname <> '')");
			$GLOBALS['tmpl']->assign("cate_id",$cate_item['id']);					
			
			//输出商城分类
			$cate_tree = get_cate_tree($cate_item['id']);		
			$GLOBALS['tmpl']->assign("cate_tree",$cate_tree);
			
			//开始输出当前的site_nav
			$cates = array();
			$cate = $cate_item;
			do
			{
				$cates[] = $cate;
				$pid = intval($cate['pid']);
				$cate = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."deal_cate where is_effect =1 and is_delete =0 and id = ".$pid);			
				
			}while($pid!=0);
			
			foreach($cates as $cate_row)
			{
				$page_title .= $cate_row['name']." - "; 
				$page_kd .= $cate_row['name'].",";
			}
			$page_title = substr($page_title,0,-3);
			krsort($cates);
			
			$site_nav[] = array('name'=>$GLOBALS['lang']['HOME_PAGE'],'url'=>APP_ROOT."/");
			if($cate_item)
			{
				foreach($cates as $cate_row)
				{
					if($cate_row['uname']!="")
					$curl = url("shop","cate#index",array("id"=>$cate_row['uname']));
					else
					$curl = url("shop","cate#index",array("id"=>$cate_row['id']));
					$site_nav[] = array('name'=>$cate_row['name'],'url'=>$curl);
				}
			}		
			else
			{
				$site_nav[] = array('name'=>$GLOBALS['lang']['GOODS_CATE'],'url'=>url("shop","cate#index"));
			}
			$GLOBALS['tmpl']->assign("site_nav",$site_nav);
			//输出当前的site_nav
			
			$GLOBALS['tmpl']->assign("page_title",$page_title);
			$GLOBALS['tmpl']->assign("page_keyword",$page_kd);
			$GLOBALS['tmpl']->assign("page_description",$page_kd);					
		}
		$GLOBALS['tmpl']->display("goods_list.html",$cache_id);
	}
}
?>