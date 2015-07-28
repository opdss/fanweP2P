<?php
// +----------------------------------------------------------------------
// | Fanwe 方维p2p借贷系统
// +----------------------------------------------------------------------
// | Copyright (c) 2011 http://www.fanwe.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 云淡风轻(88522820@qq.com)
// +----------------------------------------------------------------------

require APP_ROOT_PATH.'app/Lib/uc.php';
class uc_deal_quotaModule extends SiteBaseModule
{
	public function index()
	{	
		$user_id = $GLOBALS['user_info']['id'];
		$page = intval($_REQUEST['p']);
		if($page==0)
			$page = 1;
		$limit = (($page-1)*app_conf("PAGE_SIZE")).",".app_conf("PAGE_SIZE");
		
		$status = $_REQUEST['status'];
		
		$kaiqi_if = $GLOBALS['db']->getOne("select value from ".DB_PREFIX."conf where is_effect=1 AND name='OPEN_QUOTA' "); 
		if(!$kaiqi_if)
		showErr('未开启授信额度申请',0,url("index","uc_center"));
		
		if(!$status)
		{
			$condition = "";
		}
		elseif($status==3)
		{
			$condition = " and status = 0";//未审核
		}
		elseif($status==1)
		{
			$condition = " and status = 1";//已通过
		}
		elseif($status==2)
		{
			$condition = " and status = 2";//未通过
		}
		$GLOBALS['tmpl']->assign('status',$status);
		
		
		$result = get_deal_quota_list($limit, $user_id,$condition);	
		
		$GLOBALS['tmpl']->assign("list",$result['list']);
		$page = new Page($result['count'],app_conf("PAGE_SIZE"));   //初始化分页对象
		$p  =  $page->show();
		$GLOBALS['tmpl']->assign('pages',$p);
		
		$GLOBALS['tmpl']->assign("page_title","授信额度申请");
		$GLOBALS['tmpl']->assign("inc_file","inc/uc/uc_deal_quota.html");
		$GLOBALS['tmpl']->display("page/uc.html");
	}
	
	public function add_quota()
	{
		$GLOBALS['tmpl']->display("inc/uc/uc_deal_quota_add.html");
	}
	
	public function do_add_quota()
	{
		$data['user_id'] = $GLOBALS['user_info']['id'];
		
		$data['name'] = strim($_REQUEST['name']);
		$data['borrow_amount'] = floatval($_REQUEST['borrow_amount']);
		$data['description'] = replace_public(btrim($_REQUEST['description']));
    	$data['description'] = valid_tag($data['description']);
    	$data['is_effect'] = 1;
    	
    	$user_view_info = $GLOBALS['user_info']['view_info'];
    	$user_view_info = unserialize($user_view_info);
    	
    	$new_view_info_arr = array();	
    	for($i=1;$i<=intval($_REQUEST['file_upload_count']);$i++){
    		$img_info = array();
    		$img = replace_public(strim($_REQUEST['file_'.$i]));
    		if($img!=""){
    			$img_info['name'] = strim($_REQUEST['file_name_'.$i]);
    			$img_info['img'] = $img;
    			$img_info['is_user'] = 1;
    			
    			$user_view_info[] = $img_info;
    			$ss = $user_view_info;
				end($ss);
				$key = key($ss);
    			$new_view_info_arr[$key] = $img_info;
    		}
    	}
    	    	
    	$datas['view_info'] = serialize($user_view_info);
    	
    	$GLOBALS['db']->autoExecute(DB_PREFIX."user",$datas,"UPDATE","id=".$GLOBALS['user_info']['id']);

    	
    	$data['view_info'] = array();
    	foreach($_REQUEST['file_key'] as $k=>$v){
    		if(isset($user_view_info[$v])){
    			$data['view_info'][$v] = $user_view_info[$v];
    		}
    	}
    	
    	foreach($new_view_info_arr as $k=>$v){
    		$data['view_info'][$k] = $v;
    	}
    	
    	$data['view_info'] = serialize($data['view_info']);
		
		$data['create_time'] = TIME_UTC;//申请时间
		
		$GLOBALS['db']->autoExecute(DB_PREFIX."deal_quota_submit",$data);
		
		if($GLOBALS['db']->insert_id()){
			showSuccess('提交成功，等待管理员审核',0,url("index","uc_deal_quota"));
		}
		else{
			showErr('提交失败',0,url("index","uc_deal_quota"));
		}
	}
	
	public function do_delete_quota()
	{
		$result['status'] = 0;
		$quota_id = intval($_REQUEST['quota_id']);
		$GLOBALS['db']->query("delete from ".DB_PREFIX."deal_quota_submit where id = ".$quota_id." and status=0 ");
		if($GLOBALS['db']->affected_rows())
		{
			$result['status'] = 1;
			$error_msg="删除成功";
		}else{
			$error_msg="删除失败";
		}
		
		$result['info'] = $error_msg;
		ajax_return($result);
	}
}
?>