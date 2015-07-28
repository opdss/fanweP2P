<?php
// +----------------------------------------------------------------------
// | Fanwe 方维p2p借贷系统
// +----------------------------------------------------------------------
// | Copyright (c) 2011 http://www.fanwe.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 云淡风轻(88522820@qq.com)
// +----------------------------------------------------------------------

require APP_ROOT_PATH.'app/Lib/page.php';
class debit_articleModule extends SiteBaseModule
{
	public function index()
	{			
		$ajax = intval($_REQUEST["is_ajax"]);
		$GLOBALS['tmpl']->caching = true;
		$cache_id  = md5(MODULE_NAME.ACTION_NAME.trim($_REQUEST['title']).$GLOBALS['deal_city']['id']);		
		if (!$GLOBALS['tmpl']->is_cached('page/article_index.html', $cache_id))	
		{
			$article_name = urldecode($_REQUEST['title']);
			$article = get_article_buy_uname($article_name);
			if($article)
			{
				$article_list = get_article_list(12,$article["cate_id"],"","");
			}
			if($article["title"] =="")
			{
				$article["title"] = $article_name;
			}
			$GLOBALS['tmpl']->assign("article",$article);
			$GLOBALS['tmpl']->assign("article_list",$article_list["list"]);
			$seo_title = $article['seo_title']!=''?$article['seo_title']:$article['title'];
			$GLOBALS['tmpl']->assign("page_title",$seo_title);
			$seo_keyword = $article['seo_keyword']!=''?$article['seo_keyword']:$article['title'];
			$GLOBALS['tmpl']->assign("page_keyword",$seo_keyword.",");
			$seo_description = $article['seo_description']!=''?$article['seo_description']:$article['title'];
			$GLOBALS['tmpl']->assign("page_description",$seo_description.",");
		}
		$GLOBALS['tmpl']->assign("ajax",$ajax);
		$GLOBALS['tmpl']->display("debit/debit_article.html",$cache_id);
	}
	// 	http://localhost/daikuang/debit.php?ctl=debit_article&act=help_center
	public function help_center()
	{
		$GLOBALS['tmpl']->caching = true;
		$cache_id  = md5(MODULE_NAME.ACTION_NAME.trim("debit_help_center").$GLOBALS['deal_city']['id']);		
		if (!$GLOBALS['tmpl']->is_cached('page/article_index.html', $cache_id))	
		{
			$art_list = array();
			$art_list[] = get_article_buy_uname("理财人常见问题");
			$art_list[] = get_article_buy_uname("借款人常见问题");
			$art_list[] = get_article_buy_uname("产品及计划介绍");
			$art_list[] = get_article_buy_uname("账户充值、提现");
			$art_list[] = get_article_buy_uname("其他");
			
			$GLOBALS['tmpl']->assign("art_list",$art_list);
			
			$article = $art_list[0];
			
			
			$seo_title = $article['seo_title']!=''?$article['seo_title']:$article['title'];
			$GLOBALS['tmpl']->assign("page_title",$seo_title);
			$seo_keyword = $article['seo_keyword']!=''?$article['seo_keyword']:$article['title'];
			$GLOBALS['tmpl']->assign("page_keyword",$seo_keyword.",");
			$seo_description = $article['seo_description']!=''?$article['seo_description']:$article['title'];
			$GLOBALS['tmpl']->assign("page_description",$seo_description.",");
		}
		$GLOBALS['tmpl']->display("debit/debit_help_center.html",$cache_id);
	}
}
?>