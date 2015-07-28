<?php
// +----------------------------------------------------------------------
// | Fanwe 方维p2p借贷系统
// +----------------------------------------------------------------------
// | Copyright (c) 2011 http://www.fanwe.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 云淡风轻(88522820@qq.com)
// +----------------------------------------------------------------------

require APP_ROOT_PATH.'app/Lib/page.php';
class sysModule extends SiteBaseModule
{
	public function index()
	{	
		$id = intval($_REQUEST['id']);
			$uname = addslashes(trim($_REQUEST['id']));	
			if($uname=='list')
			{
				$this->list_notice();
				exit;
			}	
		$GLOBALS['tmpl']->caching = true;
		$cache_id  = md5(MODULE_NAME.ACTION_NAME.trim($_REQUEST['id']).$GLOBALS['deal_city']['id']);		
		if (!$GLOBALS['tmpl']->is_cached('sys_index.html', $cache_id))	
		{				
			if($id==0&&$uname=='')
			{
				app_redirect(APP_ROOT."/");
			}
			elseif($id==0&&$uname!='')
			{
				$id = $GLOBALS['db']->getOne("select id from ".DB_PREFIX."article where uname = '".$uname."'"); 
			}		
			$article = get_article($id);		
	
			if(!$article||$article['type_id']!=3)
			{
				app_redirect(APP_ROOT."/");
			}			
			else
			{
				if(check_ipop_limit(CLIENT_IP,"article",60,$article['id']))
				{
					//每一分钟访问更新一次点击数
					$GLOBALS['db']->query("update ".DB_PREFIX."article set click_count = click_count + 1 where id =".$article['id']);
				}
				
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
			$article = get_article($id);
			$GLOBALS['tmpl']->assign("article",$article);
			
			//开始输出当前的site_nav			
			$site_nav[] = array('name'=>$GLOBALS['lang']['HOME_PAGE'],'url'=>APP_ROOT."/");
			$site_nav[] = array('name'=>$GLOBALS['lang']['SHOP_SYSTEM'],'url'=>url("shop","sys#list"));
			if($article['type_id']==1)
			{
				$module = "help";
			}
			elseif($article['type_id']==2)
			{
				$module = "notice";
			}
			elseif($article['type_id']==3)
			{
				$module = "sys";
			}
			else 
			{
				$module = 'article';
			}
			if($article['uname']!='')
			$aurl = url("index",$module,array("id"=>$article['uname']));
			else 
			$aurl = url("index",$module,array("id"=>$article['id']));
			
			$site_nav[] = array('name'=>$article['title'],'url'=>$aurl);
			$GLOBALS['tmpl']->assign("site_nav",$site_nav);
			//输出当前的site_nav
					
			$seo_title = $article['seo_title']!=''?$article['seo_title']:$article['title'];
			$GLOBALS['tmpl']->assign("page_title",$seo_title);
			$seo_keyword = $article['seo_keyword']!=''?$article['seo_keyword']:$article['title'];
			$GLOBALS['tmpl']->assign("page_keyword",$seo_keyword.",");
			$seo_description = $article['seo_description']!=''?$article['seo_description']:$article['title'];
			$GLOBALS['tmpl']->assign("page_description",$seo_description.",");
			
		}
		$GLOBALS['tmpl']->display("sys_index.html",$cache_id);
	}
	
	
	public function list_notice()
	{
		$GLOBALS['tmpl']->caching = true;
		$cache_id  = md5(MODULE_NAME."list_notice".trim($_REQUEST['id']).intval($_REQUEST['p']).$GLOBALS['deal_city']['id']);		
		if (!$GLOBALS['tmpl']->is_cached('page/notice_list.html', $cache_id))	
		{
			$cate_list = get_acate_tree();
			
			$GLOBALS['tmpl']->assign('cate_list',$cate_list);
			//分页
			$page = intval($_REQUEST['p']);
			if($page==0)
			$page = 1;
			$limit = (($page-1)*app_conf("PAGE_SIZE")).",".app_conf("PAGE_SIZE");
			
			$result = get_article_list($limit,0,'ac.type_id = 3','',true);
			
			$GLOBALS['tmpl']->assign("list",$result['list']);
			$page = new Page($result['count'],app_conf("PAGE_SIZE"));   //初始化分页对象 		
			$p  =  $page->show();
			$GLOBALS['tmpl']->assign('pages',$p);
			
			//开始输出当前的site_nav			
			$site_nav[] = array('name'=>$GLOBALS['lang']['HOME_PAGE'],'url'=>APP_ROOT."/");
			$site_nav[] = array('name'=>$GLOBALS['lang']['SHOP_SYSTEM'],'url'=>url("shop","sys#list"));
			$GLOBALS['tmpl']->assign("site_nav",$site_nav);
			//输出当前的site_nav
			
			$cate = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."article_cate where id = ".intval($result['list'][0]['cate_id']));
			$GLOBALS['tmpl']->assign('cate',$cate);
			
			$GLOBALS['tmpl']->assign('page_title',$GLOBALS['lang']['SHOP_SYSTEM']);
			$GLOBALS['tmpl']->assign('page_keyword',$GLOBALS['lang']['SHOP_SYSTEM']);
			$GLOBALS['tmpl']->assign('page_description',$GLOBALS['lang']['SHOP_SYSTEM']);
		}
		$GLOBALS['tmpl']->display("page/notice_list.html",$cache_id);
	}
}
?>