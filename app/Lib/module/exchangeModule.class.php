<?php
// +----------------------------------------------------------------------
// | Fanwe 方维p2p借贷系统
// +----------------------------------------------------------------------
// | Copyright (c) 2011 http://www.fanwe.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 云淡风轻(88522820@qq.com)
// +----------------------------------------------------------------------

class exchangeModule extends SiteBaseModule
{
	public function index()
	{		
		$GLOBALS['tmpl']->caching = true;
		$cache_id  = md5(MODULE_NAME.ACTION_NAME.trim($_REQUEST['id']).$GLOBALS['deal_city']['id']);		
		if (!$GLOBALS['tmpl']->is_cached('score_info.html', $cache_id))	
		{		
			
			
			//获取当前页的团购商品
			$id = intval($_REQUEST['id']);
			$uname = addslashes(trim($_REQUEST['id']));
			
			if($id==0&&$uname=='')
			{
				app_redirect(APP_ROOT."/");
			}
			elseif($id==0&&$uname!='')
			{
				$id = $GLOBALS['db']->getOne("select id from ".DB_PREFIX."deal where uname = '".$uname."'"); 
			}
			//获取当前页的团购商品
			
			$goods = get_goods($id);
			//输出商城分类
			$cate_tree = get_cate_tree($goods['shop_cate_id']);
			$GLOBALS['tmpl']->assign("cate_id",$goods['shop_cate_id']);
			$GLOBALS['tmpl']->assign("cate_tree",$cate_tree);
			jump_deal($goods,MODULE_NAME);
			if(!$goods||$goods['buy_type']!=1)
			{
				app_redirect(APP_ROOT."/");
			}
						
			
			$GLOBALS['tmpl']->assign("goods",$goods);
			
			//开始输出当前的site_nav
			$cates = array();
			$cate = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."shop_cate where id = ".$goods['shop_cate_id']);
			do
			{
				$cates[] = $cate;
				$pid = intval($cate['pid']);
				$cate = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."shop_cate where is_effect =1 and is_delete =0 and id = ".$pid);			
				
			}while($pid!=0);
	
			$page_title = substr($page_title,0,-3);
			krsort($cates);
			
			$site_nav[] = array('name'=>$GLOBALS['lang']['HOME_PAGE'],'url'=>APP_ROOT."/");
			
			if($cates)
			{
				foreach($cates as $cate_row)
				{
					if($cate_row['uname']!="")
					$curl = url("shop","score#index",array("id"=>$cate_row['uname']));
					else
					$curl = url("shop","score#index",array("id"=>$cate_row['id']));
					$site_nav[] = array('name'=>$cate_row['name'],'url'=>$curl);
				}
			}	

			if($goods['uname']!="")
					$gurl = url("shop","exchange#index",array("id"=>$goods['uname']));
					else
					$gurl = url("shop","exchange#index",array("id"=>$goods['id']));
			$site_nav[] = array('name'=>$goods['name'],'url'=>$gurl);
			$GLOBALS['tmpl']->assign("site_nav",$site_nav);
			//输出当前的site_nav
			
			$seo_title = $goods['seo_title']!=''?$goods['seo_title']:$goods['name'];
			$GLOBALS['tmpl']->assign("page_title",$seo_title);
			$seo_keyword = $goods['seo_keyword']!=''?$goods['seo_keyword']:$goods['name'];
			$GLOBALS['tmpl']->assign("page_keyword",$seo_keyword.",");
			$seo_description = $goods['seo_description']!=''?$goods['seo_description']:$goods['name'];
			$GLOBALS['tmpl']->assign("page_description",$seo_description.",");
			
			if(!$GLOBALS['user_info'])
			{
				$GLOBALS['tmpl']->assign("message_login_tip",sprintf($GLOBALS['lang']['MESSAGE_LOGIN_TIP'],url("shop","user#login"),url("shop","user#register")));
			}
		}
		$GLOBALS['tmpl']->display("score_info.html",$cache_id);
	}
}
?>