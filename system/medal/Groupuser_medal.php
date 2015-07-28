<?php
// +----------------------------------------------------------------------
// | Fanwe 方维p2p借贷系统
// +----------------------------------------------------------------------
// | Copyright (c) 2011 http://www.fanwe.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 云淡风轻(88522820@qq.com)
// +----------------------------------------------------------------------

$medal_lang = array(
	'name'	=>	'组长勋章',
	'description'	=>	'点亮表示您为组长',
	'route'	=>	'申请成为小组组长即可点亮该勋章',
	
);
$config = array(

);
/* 模块的基本信息 */
if (isset($read_modules) && $read_modules == true)
{
    $module['class_name']    = 'Groupuser';
    $module['name']    = $medal_lang['name'];
	$module['description'] = $medal_lang['description'];
	$module['route'] = $medal_lang['route'];	
    $module['config'] = $config;    
    $module['lang'] = $medal_lang;
    $module['allow_check'] = 1;  //允许回收
    return $module;
}

// 组长勋章
require_once(APP_ROOT_PATH.'system/libs/medal.php');
class Groupuser_medal implements medal {

	public function get_medal()
	{
		$user_id = intval($GLOBALS['user_info']['id']);
		$medal = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."medal where class_name = 'Groupuser'");
		$user_info = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."user where id = ".$user_id);
		$data = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."user_medal where medal_id = ".$medal['id']." and user_id = ".$user_id);
		if($data)
		{
			//已经领取
			$result['status'] = 2;
			$result['info'] = "您已经领取过".$medal['name'];
		}
		else
		{	
			$group_info = $GLOBALS['db']->getOne("select count(*) from ".DB_PREFIX."topic_group where user_id = ".$user_id);		
			if($group_info>0)
//			if(1)
			{
				$link_data['user_id'] = $user_id;
				$link_data['medal_id'] = $medal['id'];
				$link_data['name'] = $medal['name'];
				$link_data['icon'] = $medal['icon'];
				$link_data['create_time'] = TIME_UTC;
				$GLOBALS['db']->autoExecute(DB_PREFIX."user_medal",$link_data);				
				$result['status'] = 1; //领取成功
				$result['info'] = "您已经成功领取".$medal['name'];
			}
			else
			{
				$result['status'] = 0;
				$result['info'] = "您还不是小组组长，去申请一个小组";
				$result['jump'] = url("shop","group#create");
			}
		}
		return $result;
	}
	
	public function check_medal()
	{
		//如果不再是组长则回收
		$user_id = intval($GLOBALS['user_info']['id']);		
		$medal = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."medal where class_name = 'Groupuser'");		
		$group_info = $GLOBALS['db']->getOne("select count(*) from ".DB_PREFIX."topic_group where user_id = ".$user_id);	
		$medal_log = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."user_medal where user_id = ".$user_id." and medal_id = ".$medal['id']);
				
		if($medal_log&&intval($group_info)==0)
		{				
				$GLOBALS['db']->query("delete from ".DB_PREFIX."user_medal where user_id = ".$user_id." and medal_id = ".$medal['id']);
				return array("status"=>0,"info"=>"您的".$medal['name']."被系统回收");
		}
		else
		{
				return array("status"=>1,"info"=>"");  //不会回收该勋章
		}
	
	}
}
?>