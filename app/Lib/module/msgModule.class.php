<?php
// +----------------------------------------------------------------------
// | Fanwe 方维p2p借贷系统
// +----------------------------------------------------------------------
// | Copyright (c) 2011 http://www.fanwe.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 云淡风轻(88522820@qq.com)
// +----------------------------------------------------------------------

require APP_ROOT_PATH.'app/Lib/page.php';
require APP_ROOT_PATH.'app/Lib/message.php';

class msgModule extends SiteBaseModule
{
	public function index()
	{
		$GLOBALS['tmpl']->caching = true;
		$cache_id  = md5(MODULE_NAME.trim($_REQUEST['act']).$GLOBALS['deal_city']['id']);	
		if($GLOBALS['tmpl']->is_cached("msg_index.html",$cache_id))
		{
			
		}	
		$GLOBALS['tmpl']->display("msg_index.html",$cache_id);
	}
	
	//不可接收购买评论
	public function add()
	{				
		$user_info = $GLOBALS['user_info'];
		$ajax = intval($_REQUEST['ajax']);
		if(!$user_info)
		{
			showErr($GLOBALS['lang']['PLEASE_LOGIN_FIRST'],$ajax);
		}
		if($_REQUEST['content']=='')
		{
			showErr($GLOBALS['lang']['MESSAGE_CONTENT_EMPTY'],$ajax);
		}
		
		//验证码
		if(app_conf("VERIFY_IMAGE")==1)
		{
			$verify = md5(trim($_REQUEST['verify']));
			$session_verify = es_session::get('verify');
			if($verify!=$session_verify)
			{				
				showErr($GLOBALS['lang']['VERIFY_CODE_ERROR'],$ajax);
			}
		}
		
		if(!check_ipop_limit(CLIENT_IP,"message",intval(app_conf("SUBMIT_DELAY")),0))
		{
			showErr($GLOBALS['lang']['MESSAGE_SUBMIT_FAST'],$ajax);
		}
		
		$rel_table = strim($_REQUEST['rel_table']);
		$message_type = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."message_type where type_name='".$rel_table."'");
		if(!$message_type)
		{
			showErr($GLOBALS['lang']['INVALID_MESSAGE_TYPE'],$ajax);
		}			
		//添加留言
		$message['title'] = $_REQUEST['title']?strim($_REQUEST['title']):btrim(valid_str($_REQUEST['content']));
		$message['content'] = btrim(valid_str($_REQUEST['content']));
		$message['title'] = valid_str($message['title']);
			
		$message['create_time'] = TIME_UTC;
		$message['rel_table'] = $rel_table;
		$message['rel_id'] = intval($_REQUEST['rel_id']);
		$message['user_id'] = intval($GLOBALS['user_info']['id']);
		
		if(app_conf("USER_MESSAGE_AUTO_EFFECT")==0)
		{
			$message_effect = 0;
		}
		else
		{
			$message_effect = $message_type['is_effect'];
		}
		$message['is_effect'] = $message_effect;		
		$GLOBALS['db']->autoExecute(DB_PREFIX."message",$message);
		$l_user_id =  $GLOBALS['db']->getOne("SELECT user_id FROM ".DB_PREFIX."deal WHERE id=".$message['rel_id']);
		
		//添加到动态
		insert_topic($rel_table."_message",$message['rel_id'],$message['user_id'],$GLOBALS['user_info']['user_name'],$l_user_id);
		
		if($rel_table == "deal"){
			require_once APP_ROOT_PATH.'app/Lib/deal.php';
			$deal = get_deal($message['rel_id']);
			//自己给自己留言不执行操作
			if($deal['user_id']!=$message['user_id']){
				$msg_conf = get_user_msg_conf($deal['user_id']);
				//站内信
				if($msg_conf['sms_asked']==1){
					$notices['user_name'] = get_user_name($message['user_id']);
					$notices['url'] = " “<a href=\"".$deal['url']."\">".$deal['name']."</a>”";
					$notices['msg'] = $message['content'];
						
					$tmpl_content = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."msg_template where name = 'TPL_WORDS_MSG'",false);
					$GLOBALS['tmpl']->assign("notice",$notices);
					$contents = $GLOBALS['tmpl']->fetch("str:".$tmpl_content['content']);
						
					send_user_msg("",$contents,0,$deal['user_id'],TIME_UTC,0,true,13,$message['rel_id']);
				}
				//邮件
				if($msg_conf['mail_asked']==1 && app_conf('MAIL_ON')==1){
					$user_info = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."user where id = ".$deal['user_id']);
					$tmpl = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."msg_template where name = 'TPL_MAIL_DEAL_MSG'",false);
					$tmpl_content = $tmpl['content'];
					
					$notice['user_name'] = $user_info['user_name'];
					$notice['msg_user_name'] = get_user_name($message['user_id'],false);
					$notice['deal_name'] = $deal['name'];
					$notice['deal_url'] = SITE_DOMAIN.url("index","deal",array("id"=>$deal['id']));
					$notice['message'] = $message['content'];
					$notice['site_name'] = app_conf("SHOP_TITLE");
					$notice['site_url'] = SITE_DOMAIN.APP_ROOT;
					$notice['help_url'] = SITE_DOMAIN.url("index","helpcenter");
					
					
					$GLOBALS['tmpl']->assign("notice",$notice);
					
					$msg = $GLOBALS['tmpl']->fetch("str:".$tmpl_content);
					$msg_data['dest'] = $user_info['email'];
					$msg_data['send_type'] = 1;
					$msg_data['title'] = get_user_name($message['user_id'],false)."给您的标留言！";
					$msg_data['content'] = addslashes($msg);
					$msg_data['send_time'] = 0;
					$msg_data['is_send'] = 0;
					$msg_data['create_time'] = TIME_UTC;
					$msg_data['user_id'] = $user_info['id'];
					$msg_data['is_html'] = $tmpl['is_html'];
					$GLOBALS['db']->autoExecute(DB_PREFIX."deal_msg_list",$msg_data); //插入
				}
			}
		}
		
		showSuccess($GLOBALS['lang']['MESSAGE_POST_SUCCESS'],$ajax);
	}
}
?>