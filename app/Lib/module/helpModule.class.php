<?php
// +----------------------------------------------------------------------
// | Fanwe 方维p2p借贷系统
// +----------------------------------------------------------------------
// | Copyright (c) 2011 http://www.fanwe.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 云淡风轻(88522820@qq.com)
// +----------------------------------------------------------------------

require APP_ROOT_PATH.'app/Lib/page.php';
class helpModule extends SiteBaseModule
{
	public function index()
	{			
		$GLOBALS['tmpl']->caching = true;
		$cache_id  = md5(MODULE_NAME.ACTION_NAME.trim($_REQUEST['id']).$GLOBALS['deal_city']['id']);		
		if (!$GLOBALS['tmpl']->is_cached('page/help_index.html', $cache_id))	
		{
			$id = intval($_REQUEST['id']);
			$uname = addslashes(trim($_REQUEST['id']));
			
			if($id==0&&$uname=='')
			{
				$id = $GLOBALS['db']->getOne("select a.id from ".DB_PREFIX."article as a left join ".DB_PREFIX."article_cate as ac on a.cate_id = ac.id where ac.type_id = 1 order by a.sort desc");
			}
			elseif($id==0&&$uname!='')
			{
				$id = $GLOBALS['db']->getOne("select id from ".DB_PREFIX."article where uname = '".$uname."'"); 
			}		
			$article = get_article($id);		
	
			if(!$article||$article['type_id']!=1)
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
			$seo_title = $article['seo_title']!=''?$article['seo_title']:$article['title'];
			$GLOBALS['tmpl']->assign("page_title",$seo_title);
			$seo_keyword = $article['seo_keyword']!=''?$article['seo_keyword']:$article['title'];
			$GLOBALS['tmpl']->assign("page_keyword",$seo_keyword.",");
			$seo_description = $article['seo_description']!=''?$article['seo_description']:$article['title'];
			$GLOBALS['tmpl']->assign("page_description",$seo_description.",");
			$GLOBALS['tmpl']->assign("relate_help",$cate_list);		
		}
		$GLOBALS['tmpl']->display("page/help_index.html",$cache_id);
	}
}
?>