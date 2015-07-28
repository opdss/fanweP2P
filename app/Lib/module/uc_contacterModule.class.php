<?php
// +----------------------------------------------------------------------
// | Fanwe 方维p2p借贷系统
// +----------------------------------------------------------------------
// | Copyright (c) 2011 http://www.fanwe.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 云淡风轻(88522820@qq.com)
// +----------------------------------------------------------------------

require APP_ROOT_PATH.'app/Lib/uc.php';

class uc_contacterModule extends SiteBaseModule
{
	public function index()
	{
		
		$page = intval($_REQUEST['p']);
		if($page==0)
		$page = 1;
		$limit = (($page-1)*app_conf("PAGE_SIZE")).",".app_conf("PAGE_SIZE");
		$list = $GLOBALS['db']->getAll("SELECT * FROM ".DB_PREFIX."user_contacter where user_id=".$GLOBALS['user_info']['id']." LIMIT ".$limit);
		$count = $GLOBALS['db']->getOne("SELECT count(*) FROM ".DB_PREFIX."user_contacter where user_id=".$GLOBALS['user_info']['id']);
		$GLOBALS['tmpl']->assign("list",$list);
		$page = new Page($count,app_conf("PAGE_SIZE"));   //初始化分页对象 		
		$p  =  $page->show();
		$GLOBALS['tmpl']->assign('pages',$p);
		
		$GLOBALS['tmpl']->assign("page_title",$GLOBALS['lang']['UC_CONTACTER']);
		$GLOBALS['tmpl']->assign("inc_file","inc/uc/uc_contacter_index.html");
	
				
		$GLOBALS['tmpl']->display("page/uc.html");
	}
	
	public function del()
	{
		$id = intval($_REQUEST['id']);
		$is_ajax = intval($_REQUEST['is_ajax']);
		$GLOBALS['db']->query("delete from ".DB_PREFIX."user_contacter where id = ".$id." and user_id = ".intval($GLOBALS['user_info']['id']));
		if($GLOBALS['db']->affected_rows())
		{
			if($is_ajax){
				
				ajax_return(array("status"=>1,"html"=>$html));
			}
			else{
				showSuccess($GLOBALS['lang']['DELETE_SUCCESS']);
			}
		}
		else
		{
			if($is_ajax){
				ajax_return(array("status"=>0,"message"=>$GLOBALS['lang']['INVALID_COLLECT']));
			}
			else{
				showErr($GLOBALS['lang']['INVALID_COLLECT']);
			}
		}
	}
	
	public function modify(){
		$id = intval($_REQUEST['id']);
		$is_ajax = intval($_REQUEST['is_ajax']);
		$info = $GLOBALS['db']->getRow("SELECT id,user_name,user_id,tel,email FROM ".DB_PREFIX."user_contacter where user_id=".$GLOBALS['user_info']['id']." AND id= ".$id);
		if($info){
			if($is_ajax){
				ajax_return(array("status"=>1,"info"=>$info));
			}
		}
		else{
			if($is_ajax){
				ajax_return(array("status"=>0,"message"=>"数据不存在!"));
			}
		}
	}
	
	public function do_contacter(){
		$id = intval($_REQUEST['id']);
		$is_ajax = intval($_REQUEST['is_ajax']);
		$data['user_name'] = trim($_REQUEST['user_name']);
		$data['tel'] = trim($_REQUEST['tel']);
		$data['email'] = trim($_REQUEST['email']);
		if(empty($data['user_name']))
		{
			if($is_ajax==1)
				ajax_return(array("status"=>0,"message"=>"请输入姓名!"));
			else
				showErr("请输入姓名!");
		}
		if(empty($data['tel']) && empty($_REQUEST['email']))
		{
			if($is_ajax==1)
				ajax_return(array("status"=>0,"message"=>"请输入手机号或邮箱!"));
			else
				showErr("请输入手机号或邮箱!");
		}
		elseif(!empty($data['tel'])){
			if(!check_mobile($data['tel'])){
				if($is_ajax==1)
					ajax_return(array("status"=>0,"message"=>"您好，您的手机号格式不正确!"));
				else
					showErr("您好，您的手机号格式不正确!");
			}
		}
		elseif(!empty($data['email'])){
			if(!check_email($data['email'])){
				if($is_ajax==1)
					ajax_return(array("status"=>0,"message"=>"您好，您的邮箱格式不正确!"));
				else
					showErr("您好，您的邮箱格式不正确!");
			}
		}
		if($id > 0){
			$info = $GLOBALS['db']->getRow("SELECT count(*) FROM ".DB_PREFIX."user_contacter WHERE id=".$id." AND user_id=".intval($GLOBALS['user_info']['id']));
			if($info==0){
				if($is_ajax==1)
					ajax_return(array("status"=>0,"message"=>"编辑失败!"));
				else
					showErr("编辑失败!");
			}
			$rs = $GLOBALS['db']->autoExecute(DB_PREFIX."user_contacter",$data,"UPDATE","id=".$id);
			if($rs){
				if($is_ajax==1)
					ajax_return(array("status"=>1,"message"=>"编辑成功!"));
				else
					showErr("编辑成功!");
			}
			else{
				if($is_ajax==1)
					ajax_return(array("status"=>0,"message"=>"编辑失败!"));
				else
					showErr("编辑失败!");
			}
		}
		else{
			$data['user_id'] = intval($GLOBALS['user_info']['id']);
			$GLOBALS['db']->autoExecute(DB_PREFIX."user_contacter",$data,"INSERT");
			$insert_id = $GLOBALS['db']->insert_id();
			if($insert_id){
				if($is_ajax==1)
					ajax_return(array("status"=>1,"message"=>"添加成功!"));
				else
					showErr("添加成功!");
			}
			else{
				if($is_ajax==1)
					ajax_return(array("status"=>0,"message"=>"添加失败!"));
				else
					showErr("添加失败!");
			}
		}
	}
}
?>