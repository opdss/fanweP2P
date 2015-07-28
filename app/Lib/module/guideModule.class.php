<?php

class guideModule  extends SiteBaseModule {
    function index() {
		$GLOBALS['tmpl']->caching = true;
		$GLOBALS['tmpl']->cache_lifetime = 6000;  //首页缓存10分钟
		$cache_id  = md5(MODULE_NAME.ACTION_NAME);	
		if (!$GLOBALS['tmpl']->is_cached("page/guide.html", $cache_id))
		{	
			
			
			
			$cate_id =  $GLOBALS['db']->getOne("select id from ".DB_PREFIX."article_cate where is_effect = 1 and title = '关于理财'");//新手指引
			//print_r("select id,title,content from ".DB_PREFIX."article where is_effect = 1 and is_delete = 0 and cate_id =".$cate_id." limit 6");
			$article_list = $GLOBALS['db']->getAll("select id,title,content from ".DB_PREFIX."article where is_effect = 1 and is_delete = 0 and cate_id =".$cate_id." limit 6");
			
			$GLOBALS['tmpl']->assign("article_list",$article_list);
			//print_r($article_list);
			
			$GLOBALS['tmpl']->assign("page_title","新手指引");
			$GLOBALS['tmpl']->assign("page_keyword","新手指引");
			$GLOBALS['tmpl']->assign("page_description","新手指引");
			
		}
		$GLOBALS['tmpl']->display("page/guide.html",$cache_id);
    }
}
?>