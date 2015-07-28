<?php
// +----------------------------------------------------------------------
// | Fanwe 方维p2p借贷系统
// +----------------------------------------------------------------------
// | Copyright (c) 2011 http://www.fanwe.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 云淡风轻(88522820@qq.com)
// +----------------------------------------------------------------------

require APP_ROOT_PATH.'app/Lib/page.php';
class articleModule extends SiteBaseModule
{
	public function index()
	{			
		$GLOBALS['tmpl']->caching = true;
		$cache_id  = md5(MODULE_NAME.ACTION_NAME.trim($_REQUEST['id']).$GLOBALS['deal_city']['id']);		
		if (!$GLOBALS['tmpl']->is_cached('page/article_index.html', $cache_id))	
		{
			$cate_tree = get_acate_tree();		
			$GLOBALS['tmpl']->assign("acate_tree",$cate_tree);	
			
			//获取当前页的团购商品
			$id = intval($_REQUEST['id']);
			$uname = addslashes(trim($_REQUEST['id']));
			
			if($id==0&&$uname=='')
			{
				app_redirect(APP_ROOT."/");
			}
			elseif($id==0&&$uname!='')
			{
				$id = $GLOBALS['db']->getOne("select id from ".DB_PREFIX."article where is_delete = 0 and is_effect = 1 and uname = '".$uname."'"); 
			}		
			$article = get_article($id);	
	
			if(!$article||$article['type_id']!=0)
			{
				app_redirect(APP_ROOT."/");
			}	
			else
			{				
				if($article['rel_url']!='')
				{
					if(!preg_match ("/http:\/\//i", $article['rel_url']))
					{
						if(substr($article['rel_url'],0,2)=='u:')
						{					
									
							app_redirect(parse_url_tag($article['rel_url']));
						}
						else
						app_redirect(APP_ROOT."/".$article['rel_url']);
					}
					else
					app_redirect($article['rel_url']);
				}
			}		
			
			//开始输出当前的site_nav
			$cates = array();
			$cate = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."article_cate where id = ".$article['cate_id']);
			do
			{
				$cates[] = $cate;
				$pid = intval($cate['pid']);
				$cate = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."article_cate where is_effect =1 and is_delete =0 and id = ".$pid);			
				
			}while($pid!=0);
	
			$page_title = substr($page_title,0,-3);
			krsort($cates);
			
			$site_nav[] = array('name'=>$GLOBALS['lang']['HOME_PAGE'],'url'=>APP_ROOT."/");
			
			if($cates)
			{
				foreach($cates as $cate_row)
				{
					$site_nav[] = array('name'=>$cate_row['title'],'url'=>url("shop","acate#index",array("id"=>$cate_row['id'])));
					
				}
			}		
			if($article['uname']!='')
			{
				$aurl = url("shop","article#index",array("id"=>$article['uname']));
			}
			else
			{
				$aurl = url("shop","article#index",array("id"=>$article['id']));
			}
			$site_nav[] = array('name'=>$article['title'],'url'=>$aurl);
			$GLOBALS['tmpl']->assign("site_nav",$site_nav);
			//输出当前的site_nav
			
			$article = get_article($id);
			$GLOBALS['tmpl']->assign("article",$article);
			$seo_title = $article['seo_title']!=''?$article['seo_title']:$article['title'];
			$GLOBALS['tmpl']->assign("page_title",$seo_title);
			$seo_keyword = $article['seo_keyword']!=''?$article['seo_keyword']:$article['title'];
			$GLOBALS['tmpl']->assign("page_keyword",$seo_keyword.",");
			$seo_description = $article['seo_description']!=''?$article['seo_description']:$article['title'];
			$GLOBALS['tmpl']->assign("page_description",$seo_description.",");
		}
		$GLOBALS['tmpl']->display("page/article_index.html",$cache_id);
	}
	
	public function alist()
	{
		$GLOBALS['tmpl']->caching = true;
		$cache_id  = md5(MODULE_NAME."list".strim($_REQUEST['id']).intval($_REQUEST['p']).$GLOBALS['deal_city']['id']);		
		if (!$GLOBALS['tmpl']->is_cached('page/article_list.html', $cache_id))	
		{
			$cate_list = get_acate_tree();
			
			$GLOBALS['tmpl']->assign('cate_list',$cate_list);
			//分页
			$page = intval($_REQUEST['p']);
			if($page==0)
			$page = 1;
			$limit = (($page-1)*app_conf("PAGE_SIZE")).",".app_conf("PAGE_SIZE");
			
			$cate = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."article_cate where id = ".intval($_REQUEST['id']));
			$GLOBALS['tmpl']->assign('cate',$cate);
			
			$result = get_article_list($limit,intval($_REQUEST['id']),'','',true);
			
			$GLOBALS['tmpl']->assign("list",$result['list']);
			$page = new Page($result['count'],app_conf("PAGE_SIZE"));   //初始化分页对象 		
			$p  =  $page->show();
			$GLOBALS['tmpl']->assign('pages',$p);
			
			//开始输出当前的site_nav			
			$site_nav[] = array('name'=>$GLOBALS['lang']['HOME_PAGE'],'url'=>APP_ROOT."/");
			$site_nav[] = array('name'=>$GLOBALS['lang']['SHOP_SYSTEM'],'url'=>url("shop","sys#list"));
			$GLOBALS['tmpl']->assign("site_nav",$site_nav);
			//输出当前的site_nav
			
			$GLOBALS['tmpl']->assign('page_title',$cate['title']);
			$GLOBALS['tmpl']->assign('page_keyword',$GLOBALS['lang']['SHOP_SYSTEM']);
			$GLOBALS['tmpl']->assign('page_description',$GLOBALS['lang']['SHOP_SYSTEM']);
		}
		$GLOBALS['tmpl']->display("page/article_list.html",$cache_id);
	}
}
?>