<?php

class helpcenterModule extends SiteBaseModule{
     public function index() {
     	$GLOBALS['tmpl']->caching = true;
		$cache_id  = md5(MODULE_NAME.ACTION_NAME);		
		if (!$GLOBALS['tmpl']->is_cached('page/helpcenter.html', $cache_id))	
		{
	    	$article_cate = get_acate_tree(13);
	    	$info = null;
	    	foreach($article_cate as $k=>$v){
	    		$article_cate[$k]['article'] = get_article_list(100,$v['id'],"","",true);
	    	}
	    	$GLOBALS['tmpl']->assign("article_cate",$article_cate);
	    	
	    	$GLOBALS['tmpl']->assign("page_title","帮助中心");
	    	
			$GLOBALS['tmpl']->assign("page_keyword","帮助中心");
			
			$GLOBALS['tmpl']->assign("page_description","帮助中心");
		}
    	$GLOBALS['tmpl']->display("page/helpcenter.html",$cache_id);
    }
}
?>