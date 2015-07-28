<?php
// +----------------------------------------------------------------------
// | Fanwe 方维p2p借贷系统
// +----------------------------------------------------------------------
// | Copyright (c) 2011 http://www.fanwe.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 云淡风轻(88522820@qq.com)
// +----------------------------------------------------------------------

$medal_lang = array(
	'name'	=>	'忠实网友勋章',
	'description'	=>	'点亮为忠实的网友会员',
	'route'	=>	'连续签到10天以上将获得该勋章',
	'day_count'	=>	'连续登录签到天数',
	
);
$config = array(
	'day_count'	=>	array(
		'INPUT_TYPE'	=>	'0'
	),
);
/* 模块的基本信息 */
if (isset($read_modules) && $read_modules == true)
{
    $module['class_name']    = 'Keepsign';
    $module['name']    = $medal_lang['name'];
	$module['description'] = $medal_lang['description'];
	$module['route'] = $medal_lang['route'];	
    $module['config'] = $config;    
    $module['lang'] = $medal_lang;
    $module['allow_check'] = 1;  //到期回收
    return $module;
}

// 忠实网友勋章
require_once(APP_ROOT_PATH.'system/libs/medal.php');
class Keepsign_medal implements medal {

	public function get_medal()
	{
		$user_id = intval($GLOBALS['user_info']['id']);
		$medal = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."medal where class_name = 'Keepsign'");
		$medal['config'] = unserialize($medal['config']);
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
			$sign_count = $GLOBALS['db']->getOne("select count(*) from ".DB_PREFIX."user_sign_log where user_id = ".$user_id);		
			if($sign_count>=$medal['config']['day_count'])
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
				$result['info'] = "领取该勋章需要您连续".$medal['config']['day_count']."天登录网站并进行签到";
				$result['jump'] = url("shop","uc_medal");
			}
		}
		return $result;
	}
	
	public function check_medal()
	{
		//30天后回收
		$user_id = intval($GLOBALS['user_info']['id']);
		$sign_count = $GLOBALS['db']->getOne("select count(*) from ".DB_PREFIX."user_sign_log where user_id = ".$user_id);	
		$medal = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."medal where class_name = 'Keepsign'");
		$medal['config'] = unserialize($medal['config']);	
		
		if($sign_count>=$medal['config']['day_count'])
		{
			return array("status"=>1,"info"=>"");  //不会回收该勋章
		}
		else
		{
			
			$medal_log = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."user_medal where user_id = ".$user_id." and medal_id = ".$medal['id']);
			
			if($medal_log&&TIME_UTC>=($medal_log['create_time']+intval($medal['config']['day_count'])*24*3600))
			{
				//超过三十天
				$GLOBALS['db']->query("delete from ".DB_PREFIX."user_medal where user_id = ".$user_id." and medal_id = ".$medal['id']);
				//删除表示可以再次领取
				return array("status"=>0,"info"=>"您的".$medal['name']."被回收");  //不会回收该勋章
			}
			else
			{
				return array("status"=>1,"info"=>"");  //不会回收该勋章
			}
		}
		
	}
}
?>