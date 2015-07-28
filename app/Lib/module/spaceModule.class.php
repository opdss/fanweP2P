<?php
// +----------------------------------------------------------------------
// | Fanwe 方维p2p借贷系统
// +----------------------------------------------------------------------
// | Copyright (c) 2011 http://www.fanwe.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 云淡风轻(88522820@qq.com)
// +----------------------------------------------------------------------

require APP_ROOT_PATH.'app/Lib/page.php';
class spaceModule extends SiteBaseModule
{
	private $space_user;
	public function init()
	{
		if(!$GLOBALS['user_info']){
			if($_REQUEST['ajax']==1)
			{
				ajax_return(array("status"=>0,"info"=>"请先登录"));
			}
			else
			{
				es_session::set('before_login',$_SERVER['REQUEST_URI']);
				app_redirect(url("index","user#login"));
			}
		}
		$id = intval($_REQUEST['id']);
		$this->space_user = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."user where id = ".$id." and is_effect=  1 and is_delete = 0");
		
		$user_id = intval($GLOBALS['user_info']['id']);
		if(!($this->space_user))
		{
			showErr($GLOBALS['lang']['USER_NOT_EXISTS']);
		}
		$focus_data = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."user_focus where focus_user_id = ".$user_id." and focused_user_id = ".$this->space_user['id']);
		if($focus_data)
		$this->space_user['focused'] = 1;
		
		$province_str = $GLOBALS['db']->getOne("select name from ".DB_PREFIX."region_conf where id = ".$this->space_user['province_id']);
		$city_str = $GLOBALS['db']->getOne("select name from ".DB_PREFIX."region_conf where id = ".$this->space_user['city_id']);
		if($province_str.$city_str=='')
		$user_location = $GLOBALS['lang']['LOCATION_NULL'];
		else 
		$user_location = $province_str." ".$city_str;
		
		$this->space_user['fav_count'] = $GLOBALS['db']->getOne("select count(*) from ".DB_PREFIX."topic where user_id = ".$this->space_user['id']." and fav_id <> 0");
		$this->space_user['user_location'] = $user_location;
		$this->space_user['group_name'] = $GLOBALS['db']->getOne("select name from ".DB_PREFIX."user_group where id = ".$this->space_user['group_id']." ");
		
		$GLOBALS['tmpl']->assign("space_user",$this->space_user);
		
		
		$GLOBALS['tmpl']->assign('user_statics',sys_user_status($id,true));
	}
	public function index()
	{			
		$GLOBALS['tmpl']->caching = true;
		$GLOBALS['tmpl']->cache_lifetime = 600; 
		$cache_id  = md5(MODULE_NAME.ACTION_NAME.trim($_REQUEST['id']).intval($_REQUEST['p']));		
		if (!$GLOBALS['tmpl']->is_cached('page/space_index.html', $cache_id))	
		{
			$this->init();
			$title = sprintf($GLOBALS['lang']['WHOS_SPACE'],$this->space_user['user_name']);
			$site_nav[] = array('name'=>$GLOBALS['lang']['HOME_PAGE'],'url'=>APP_ROOT."/");
			$site_nav[] = array('name'=>$title,'url'=>url("index","space", array("id" => $this->space_user['id'])));	
			$site_nav[] = array('name'=>$GLOBALS['lang']['SPACE_HOME'],'url'=>url("index","space", array("id"=>$this->space_user['id'])));			
			$GLOBALS['tmpl']->assign("site_nav",$site_nav);
			
			$GLOBALS['tmpl']->assign("page_title",$title);
			$GLOBALS['tmpl']->assign("page_keyword",$title.",");
			$GLOBALS['tmpl']->assign("page_description",$title.",");
			
			
			//输出发言列表
			$page = intval($_REQUEST['p']);
			if($page==0)
			$page = 1;
			$limit = (($page-1)*app_conf("PAGE_SIZE")).",".app_conf("PAGE_SIZE");
						
			$result = get_topic_list($limit," (user_id = ".$this->space_user['id']." OR (fav_id = ".$this->space_user['id']." AND type='focus') OR (l_user_id =".$this->space_user['id']." AND type in ('message','deal_message','transfer_message') )) ");
			
			$GLOBALS['tmpl']->assign("topic_list",$result['list']);
			$page = new Page($result['total'],app_conf("PAGE_SIZE"));   //初始化分页对象 		
			$p  =  $page->show();
			$GLOBALS['tmpl']->assign('pages',$p);
			
			$list_html = $GLOBALS['tmpl']->fetch("inc/topic_col_list.html");
			
			$GLOBALS['tmpl']->assign("list_html",$list_html);
			
			$GLOBALS['tmpl']->assign('user_auth',get_user_auth());
		}
		$GLOBALS['tmpl']->display("page/space_index.html",$cache_id);
	}
	
	
	
	public function focus()
	{
		$GLOBALS['tmpl']->caching = true;
		$GLOBALS['tmpl']->cache_lifetime = 600; 
		$cache_id  = md5(MODULE_NAME.ACTION_NAME.trim($_REQUEST['id']).intval($_REQUEST['p']));		
		if (!$GLOBALS['tmpl']->is_cached('page/space_index.html', $cache_id))	
		{
			$this->init();
			$title = sprintf($GLOBALS['lang']['WHOS_SPACE'],$this->space_user['user_name']);
			$site_nav[] = array('name'=>$GLOBALS['lang']['HOME_PAGE'],'url'=>APP_ROOT."/");
			$site_nav[] = array('name'=>$title,'url'=>url("index","space#focus", array("id"=> $this->space_user['id'])));	
			$site_nav[] = array('name'=>$GLOBALS['lang']['SPACE_FOCUS'],'url'=>url("index","space#focus",array("id"=> $this->space_user['id'])));		
			$GLOBALS['tmpl']->assign("site_nav",$site_nav);
			
			$GLOBALS['tmpl']->assign("page_title",$title);
			$GLOBALS['tmpl']->assign("page_keyword",$title.",");
			$GLOBALS['tmpl']->assign("page_description",$title.",");
				
				
			$page_size = 24;
			
			$page = intval($_REQUEST['p']);
			if($page==0)
			$page = 1;
			$limit = (($page-1)*$page_size).",".$page_size;
			
			//输出粉丝
			$total = $GLOBALS['db']->getOne("select count(*) from ".DB_PREFIX."user_focus where focus_user_id = ".$this->space_user['id']);
			$focus_list = array();
			if($total > 0){
				$focus_list = $GLOBALS['db']->getAll("select focused_user_id as id,focused_user_name as user_name from ".DB_PREFIX."user_focus where focus_user_id = ".$this->space_user['id']." order by id desc limit ".$limit);
			
				foreach($focus_list as $k=>$v)
				{			
					$focus_uid = intval($v['id']);
					$focus_data = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."user_focus where focus_user_id = ".$this->space_user['id']." and focused_user_id = ".$focus_uid);
					if($focus_data)
					$focus_list[$k]['focused'] = 1;
				}
			}
			
			$GLOBALS['tmpl']->assign("focus_list",$focus_list);	
	
			$page = new Page($total,$page_size);   //初始化分页对象 		
			$p  =  $page->show();
			$GLOBALS['tmpl']->assign('pages',$p);
			
			$list_html = $GLOBALS['tmpl']->fetch("inc/uc/uc_center_focus.html");
			$GLOBALS['tmpl']->assign("list_html",$list_html);
			$GLOBALS['tmpl']->assign("user_id",$this->space_user['id']);
		}
		$GLOBALS['tmpl']->display("page/space_index.html",$cache_id);
	}
	
	public function focustopic()
	{	
		$GLOBALS['tmpl']->caching = true;
		$GLOBALS['tmpl']->cache_lifetime = 600; 
		$cache_id  = md5(MODULE_NAME.ACTION_NAME.trim($_REQUEST['id']).intval($_REQUEST['p']));		
		if (!$GLOBALS['tmpl']->is_cached('page/space_index.html', $cache_id))	
		{
			$this->init();
			$title = sprintf($GLOBALS['lang']['WHOS_SPACE'],$this->space_user['user_name']);
			$site_nav[] = array('name'=>$GLOBALS['lang']['HOME_PAGE'],'url'=>APP_ROOT."/");
			$site_nav[] = array('name'=>$title,'url'=>url("index","space#focustopic", array("id"=> $this->space_user['id'])));	
			$site_nav[] = array('name'=>$GLOBALS['lang']['SPACE_FOCUSTOPIC'],'url'=>url("index","space#focustopic",array("id"=> $this->space_user['id'])));		
			$GLOBALS['tmpl']->assign("site_nav",$site_nav);
			
			$GLOBALS['tmpl']->assign("page_title",$title);
			$GLOBALS['tmpl']->assign("page_keyword",$title.",");
			$GLOBALS['tmpl']->assign("page_description",$title.",");
			
			//输出发言列表
			$page = intval($_REQUEST['p']);
			if($page==0)
			$page = 1;
			$limit = (($page-1)*app_conf("PAGE_SIZE")).",".app_conf("PAGE_SIZE");
						
			//开始输出相关的用户日志
			$uids = $GLOBALS['db']->getOne("select group_concat(focused_user_id) from ".DB_PREFIX."user_focus where focus_user_id = ".$this->space_user['id']." ");
	
			if($uids)
			{
				$uids = trim($uids,",");	
				$result = get_topic_list($limit," user_id in (".$uids.") ");
			}
			
			$GLOBALS['tmpl']->assign("topic_list",$result['list']);
			$page = new Page($result['total'],app_conf("PAGE_SIZE"));   //初始化分页对象 		
			$p  =  $page->show();
			$GLOBALS['tmpl']->assign('pages',$p);
			
			$list_html = $GLOBALS['tmpl']->fetch("inc/topic_col_list.html");
			$GLOBALS['tmpl']->assign("list_html",$list_html);
		}
		$GLOBALS['tmpl']->display("page/space_index.html",$cache_id);
	}
	
	
	public function lend()
	{
		$GLOBALS['tmpl']->caching = true;
		$GLOBALS['tmpl']->cache_lifetime = 600; 
		$cache_id  = md5(MODULE_NAME.ACTION_NAME.trim($_REQUEST['id']).intval($_REQUEST['p']));		
		if (!$GLOBALS['tmpl']->is_cached('page/space_index.html', $cache_id))	
		{
			$this->init();
			$title = sprintf($GLOBALS['lang']['WHOS_SPACE'],$this->space_user['user_name']);
			$site_nav[] = array('name'=>$GLOBALS['lang']['HOME_PAGE'],'url'=>APP_ROOT."/");
			$site_nav[] = array('name'=>$title,'url'=>url("index","space#lend", array("id"=> $this->space_user['id'])));	
			$site_nav[] = array('name'=>$GLOBALS['lang']['SPACE_LEND'],'url'=>url("index","space#lend",array("id"=> $this->space_user['id'])));		
			$GLOBALS['tmpl']->assign("site_nav",$site_nav);
			
			$GLOBALS['tmpl']->assign("page_title",$title);
			$GLOBALS['tmpl']->assign("page_keyword",$title.",");
			$GLOBALS['tmpl']->assign("page_description",$title.",");
				
			//输出发言列表
			$page = intval($_REQUEST['p']);
			if($page==0)
			$page = 1;
			$limit = (($page-1)*app_conf("PAGE_SIZE")).",".app_conf("PAGE_SIZE");
			
			$result['total'] = $GLOBALS['db']->getOne("SELECT count(*) FROM ".DB_PREFIX."deal_load WHERE user_id=".$this->space_user['id']);
			if($result['total'] > 0)
				$result['list'] = $GLOBALS['db']->getAll("SELECT dl.*,d.rate,d.repay_time,d.repay_time_type,d.deal_status,d.name FROM ".DB_PREFIX."deal_load dl LEFT JOIN ".DB_PREFIX."deal d ON dl.deal_id = d.id WHERE dl.user_id=".$this->space_user['id']." LIMIT $limit ");
			
			$page = new Page($result['total'],app_conf("PAGE_SIZE"));   //初始化分页对象 		
			$p  =  $page->show();
			$GLOBALS['tmpl']->assign('pages',$p);
			
			$GLOBALS['tmpl']->assign("lend_list",$result['list']);
			
			$list_html = $GLOBALS['tmpl']->fetch("inc/uc/uc_center_lend.html");
			$GLOBALS['tmpl']->assign("list_html",$list_html);
		}
		$GLOBALS['tmpl']->display("page/space_index.html",$cache_id);
	}
	
	public function deal()
	{	
		$GLOBALS['tmpl']->caching = true;
		$GLOBALS['tmpl']->cache_lifetime = 600; 
		$cache_id  = md5(MODULE_NAME.ACTION_NAME.trim($_REQUEST['id']).intval($_REQUEST['p']));		
		if (!$GLOBALS['tmpl']->is_cached('page/space_index.html', $cache_id))	
		{
			
			$this->init();
			$title = sprintf($GLOBALS['lang']['WHOS_SPACE'],$this->space_user['user_name']);
			$site_nav[] = array('name'=>$GLOBALS['lang']['HOME_PAGE'],'url'=>APP_ROOT."/");
			$site_nav[] = array('name'=>$title,'url'=>url("index","space#deal", array("id"=> $this->space_user['id'])));	
			$site_nav[] = array('name'=>$GLOBALS['lang']['SPACE_DEAL'],'url'=>url("index","space#deal",array("id"=> $this->space_user['id'])));		
			$GLOBALS['tmpl']->assign("site_nav",$site_nav);
			
			$GLOBALS['tmpl']->assign("page_title",$title);
			$GLOBALS['tmpl']->assign("page_keyword",$title.",");
			$GLOBALS['tmpl']->assign("page_description",$title.",");
			
			//输出借款记录
			$page = intval($_REQUEST['p']);
			if($page==0)
			$page = 1;
			$limit = (($page-1)*app_conf("PAGE_SIZE")).",".app_conf("PAGE_SIZE");
				
			require_once (APP_ROOT_PATH."app/Lib/deal.php");
			
			$result = get_deal_list($limit,0,"user_id=".$this->space_user['id'],"id DESC");
	
			$GLOBALS['tmpl']->assign("deal_list",$result['list']);
			
			$page = new Page($result['count'],app_conf("PAGE_SIZE"));   //初始化分页对象 		
			$p  =  $page->show();
			$GLOBALS['tmpl']->assign('pages',$p);
			
			
			$list_html = $GLOBALS['tmpl']->fetch("inc/uc/uc_center_deals.html");
			$GLOBALS['tmpl']->assign("list_html",$list_html);
		}
		$GLOBALS['tmpl']->display("page/space_index.html",$cache_id);
	}
	
	public function level(){
		$GLOBALS['tmpl']->caching = true;
		$GLOBALS['tmpl']->cache_lifetime = 600; 
		$cache_id  = md5(MODULE_NAME.ACTION_NAME.trim($_REQUEST['id']));		
		if (!$GLOBALS['tmpl']->is_cached('page/space_credit.html', $cache_id))	
		{
			
			$this->init();
			$title = sprintf($GLOBALS['lang']['WHOS_SPACE'],$this->space_user['user_name']);
			$site_nav[] = array('name'=>$GLOBALS['lang']['HOME_PAGE'],'url'=>APP_ROOT."/");
			$site_nav[] = array('name'=>$title,'url'=>url("index","space#deal", array("id"=> $this->space_user['id'])));	
			$site_nav[] = array('name'=>$GLOBALS['lang']['SPACE_DEAL'],'url'=>url("index","space#deal",array("id"=> $this->space_user['id'])));		
			$GLOBALS['tmpl']->assign("site_nav",$site_nav);
			
			$list = load_auto_cache("level");
			
			$GLOBALS['tmpl']->assign("list",$list['list']);
			
			foreach($list["list"] as $k=>$v){
				if($v['id'] ==  $this->space_user['level_id'])
					$user_point_level = $v['name'];
			}
			
			
			$GLOBALS['tmpl']->assign("user_point_level",$user_point_level);
			
			
			$credit_file = get_user_credit_file($GLOBALS['user_info']['id'],$this->space_user);
		
			//必要信用认证
	    	$level_point['need_other_point'] = 0;
	    	$must_credit_count = 0;
	    	$other_credit_count = 0;
	    	foreach($credit_file as $k=>$v){
	    		if($v['passed']== 1)
		    		$level_point['need_other_point'] += (int)trim($v['point']);
		    	if($v['must'] == 1)
		    		$must_credit_count ++;
		    	else
		    		$other_credit_count ++;
	    	}
	    	
	    	//工作认证是否过期
	    	$GLOBALS['tmpl']->assign('expire',$this->space_user);
	    	
	    	//还清
	    	$level_point['repay_success'] = $GLOBALS['db']->getRow("SELECT sum(point) as total_point,count(*) AS total_count FROM ".DB_PREFIX."user_log WHERE log_info like '%还清借款%' AND user_id=".$this->space_user['id']);
	    	//逾期
	    	$level_point['impose_repay'] = $GLOBALS['db']->getRow("SELECT sum(point) as total_point,count(*) AS total_count FROM ".DB_PREFIX."user_log WHERE log_info like '%逾期还款%' and log_info not like '%严重逾期还款%' AND user_id=".$this->space_user['id']);
	    	//严重逾期
	    	$level_point['yz_impose_repay'] = $GLOBALS['db']->getRow("SELECT sum(point) as total_point,count(*) AS total_count FROM ".DB_PREFIX."user_log WHERE log_info like '%严重逾期还款%' AND user_id=".$this->space_user['id']);
	    	
	    	$GLOBALS['tmpl']->assign('level_point',$level_point);
	    	
	    	$GLOBALS['tmpl']->assign("credit_file",$credit_file);
			$GLOBALS['tmpl']->assign("must_credit_count",$must_credit_count);
			$GLOBALS['tmpl']->assign("other_credit_count",$other_credit_count);
	    	
	    	//可用额度
	    	$can_use_quota = get_can_use_quota($this->space_user['id']);
	    	$GLOBALS['tmpl']->assign('can_use_quota',$can_use_quota);
			
			$GLOBALS['tmpl']->assign("page_title",$title);
			$GLOBALS['tmpl']->assign("page_keyword",$title.",");
			$GLOBALS['tmpl']->assign("page_description",$title.",");
		}	
		$GLOBALS['tmpl']->display("page/space_credit.html",$cache_id);
	}

}

function insert_space_right(){
		$user_id =  intval($_REQUEST['id']);
		//输出我的关注
		$focus_list = $GLOBALS['db']->getAll("select focused_user_id as id,focused_user_name as user_name from ".DB_PREFIX."user_focus where focus_user_id = ".$user_id." order by rand() limit 6");

		$GLOBALS['tmpl']->assign("r_focus_list",$focus_list);
		
		return $GLOBALS['tmpl']->fetch("inc/space/space_right.html");
}
?>