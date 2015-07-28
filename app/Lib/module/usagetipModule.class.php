<?php

class usagetipModule  extends SiteBaseModule {
    function index() {
		$id = intval($_REQUEST['id']);
    	if($id==0){
    		$this->cate();
    	}
    	else{
    		$this->view();
    	}
    }
    private function cate(){
    	require APP_ROOT_PATH.'app/Lib/page.php';
    	$cate_id = 6;
    	$GLOBALS['tmpl']->caching = true;
		$cache_id  = md5(MODULE_NAME.ACTION_NAME."cate".$cate_id.intval($_REQUEST['p']));
		if (!$GLOBALS['tmpl']->is_cached('page/usagetip_index.html', $cache_id))	
		{	
			$cate_item = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."article_cate where id = $cate_id and is_effect = 1 and is_delete = 0");
			if($cate_id>0&&!$cate_item)
			{
				app_redirect(APP_ROOT."/");
			}
			
			$cate_tree = get_acate_tree();		
			$GLOBALS['tmpl']->assign("acate_tree",$cate_tree);			
	
			//分页
			$page = intval($_REQUEST['p']);
			if($page==0)
			$page = 1;
			$limit = (($page-1)*app_conf("PAGE_SIZE")).",".app_conf("PAGE_SIZE");		
			$result = get_article_list($limit,$cate_id,'','');
			
			$GLOBALS['tmpl']->assign("list",$result['list']);
			$page = new Page($result['count'],app_conf("PAGE_SIZE"));   //初始化分页对象 		
			$p  =  $page->show();
			$GLOBALS['tmpl']->assign('pages',$p);
				
			//使用技巧
			$use_tech_list  = get_article_list(6,6);
			$GLOBALS['tmpl']->assign("use_tech_list",$use_tech_list);	
			
			$GLOBALS['tmpl']->assign("page_title",$cate_item['title']);
			$GLOBALS['tmpl']->assign("page_keyword",$cate_item['title'].",");
			$GLOBALS['tmpl']->assign("page_description",$cate_item['title'].",");
		}
		$GLOBALS['tmpl']->display("page/usagetip_index.html",$cache_id);
    }
    
    private function view(){
    	$id = intval($_REQUEST['id']);
    	if($id==0){
    		app_redirect(url("index","usagetip"));
    	}
    	$GLOBALS['tmpl']->caching = true;
		$GLOBALS['tmpl']->cache_lifetime = 6000;  //首页缓存10分钟
		$cache_id  = md5(MODULE_NAME.ACTION_NAME."view".$id);
		if (!$GLOBALS['tmpl']->is_cached("page/usagetip_view.html", $cache_id))
		{	
			$article = get_article($id);
			$GLOBALS['tmpl']->assign("article",$article);
			
			$seo_title = $article['seo_title']!=''?$article['seo_title']:$article['title'];
			$GLOBALS['tmpl']->assign("page_title",$seo_title);
			$seo_keyword = $article['seo_keyword']!=''?$article['seo_keyword']:$article['title'];
			$GLOBALS['tmpl']->assign("page_keyword",$seo_keyword.",");
			$seo_description = $article['seo_description']!=''?$article['seo_description']:$article['title'];
			$GLOBALS['tmpl']->assign("page_description",$seo_description.",");
			
			//使用技巧
			$use_tech_list  = get_article_list(6,6);
			$GLOBALS['tmpl']->assign("use_tech_list",$use_tech_list);	
		}
		$GLOBALS['tmpl']->display("page/usagetip_view.html",$cache_id);
    }
}
?>