<?php

class agencyModule extends SiteBaseModule {

    function index() {
    	$id = intval($_REQUEST['id']);
    	
    	$authorizedagency = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."user where id = ".$id." and user_type = 3 and is_effect = 1",false);
    	if(!$authorizedagency)
			app_redirect(url("index")); 
    	
    	$seo_title = $authorizedagency['short_name']!=''?$authorizedagency['short_name']: $authorizedagency['name'];
			
		$GLOBALS['tmpl']->assign("page_title",$seo_title);
		
		$seo_keyword = $seo_title;
		$GLOBALS['tmpl']->assign("page_keyword",$seo_keyword.",");
		
		$seo_description = $authorizedagency['brief'];
		$GLOBALS['tmpl']->assign("seo_description",$seo_description.",");
		
		$GLOBALS['tmpl']->assign("agency",$authorizedagency);
		$GLOBALS['tmpl']->display("page/agency.html");
    }
}
?>