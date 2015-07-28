<?php
// +----------------------------------------------------------------------
// | Fanwe 方维p2p借贷系统
// +----------------------------------------------------------------------
// | Copyright (c) 2011 http://www.fanwe.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 云淡风轻(88522820@qq.com)
// +----------------------------------------------------------------------

require APP_ROOT_PATH.'app/Lib/uc.php';
class uc_topicModule extends SiteBaseModule
{
	public function add()
	{
		$ajax = intval($_REQUEST['ajax']);
		
		if(!$GLOBALS['user_info'])
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
		
		$forum_title = htmlspecialchars(addslashes(trim(valid_str($_REQUEST['forum_title']))));
		$group_id = intval($_REQUEST['group_id']);
		
		if($group_id>0)
		{
			if($forum_title=='')
			showErr("请输出发表的主题",$ajax);
			
			$user_id = intval($GLOBALS['user_info']['id']);
			$group_info = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."topic_group where id = ".$group_id);
			if($group_info['user_id']!=$user_id) //不是组长进行验证
			{
				if($GLOBALS['db']->getOne("select count(*) from ".DB_PREFIX."user_topic_group where group_id=".$group_id." and user_id = ".$user_id)==0)
				{
					showErr("不是本组会员, 不能发表主题",$ajax);
				}
			}
		}
		
		
		$title = htmlspecialchars(addslashes(trim(valid_str($_REQUEST['title']))));
		$content = htmlspecialchars(addslashes(trim(valid_str($_REQUEST['content']))));
		$group = htmlspecialchars(addslashes(trim($_REQUEST['group'])));
		$group_data = addslashes(trim($_REQUEST['group_data']));
		$type = addslashes(trim($_REQUEST['type']));
		$tags_data = $_REQUEST['tag'];
		$tags = array();
		foreach($tags_data as $tag_row)
		{
			$tag_row_arr = explode(" ",$tag_row);
			foreach($tag_row_arr as $tag_item)
			{
				$tag_item = trim($tag_item);
				if(!in_array($tag_item,$tags))
				{
					$tags[] = $tag_item;
				}
			}
		}		
		$attach_list=get_topic_attach_list(); 		
		$id = insert_topic($content,$title,$type,$group, $relay_id = 0, $fav_id = 0,$group_data,$attach_list,$url_route=array(),$tags,'','',$forum_title,$group_id);	
		if($id)
		{
			$GLOBALS['db']->query("update ".DB_PREFIX."topic set source_name = '网站' where id = ".intval($id));
			increase_user_active(intval($GLOBALS['user_info']['id']),"发表了一则分享");
		}		
		//验证码
		if(app_conf("VERIFY_IMAGE")==1)
		{
			es_session::delete('verify');
		}
		if($ajax==1)
		{
			$result['info'] = $GLOBALS['lang']['MESSAGE_POST_SUCCESS'];
			$result['data'] = intval($id);
			$result['status'] = 1;
			ajax_return($result);
		}
		else
		{
			if($group_id>0)
			$url = url("shop","group#forum",array("id"=>$group_id));
			showSuccess($GLOBALS['lang']['MESSAGE_POST_SUCCESS'],$ajax,$url);	
		}
//		showSuccess($GLOBALS['lang']['MESSAGE_POST_SUCCESS'],$ajax);	
		
	}
}
?>