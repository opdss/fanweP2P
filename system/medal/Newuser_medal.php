<?php
// +----------------------------------------------------------------------
// | Fanwe 方维p2p借贷系统
// +----------------------------------------------------------------------
// | Copyright (c) 2011 http://www.fanwe.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 云淡风轻(88522820@qq.com)
// +----------------------------------------------------------------------

$medal_lang = array(
	'name'	=>	'新手勋章',
	'description'	=>	'点亮您为新手，让更多的朋友找到你',
	'route'	=>	'完善用户的所有资料，即可获取该勋章',
	
);
$config = array(

);
/* 模块的基本信息 */
if (isset($read_modules) && $read_modules == true)
{
    $module['class_name']    = 'Newuser';
    $module['name']    = $medal_lang['name'];
	$module['description'] = $medal_lang['description'];
	$module['route'] = $medal_lang['route'];	
    $module['config'] = $config;    
    $module['lang'] = $medal_lang;
    $module['allow_check'] = 1;  //允许回收
    return $module;
}

// 新手勋章
require_once(APP_ROOT_PATH.'system/libs/medal.php');
class Newuser_medal implements medal {

	public function get_medal()
	{
		$user_id = intval($GLOBALS['user_info']['id']);
		$medal = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."medal where class_name = 'Newuser'");
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
			if($user_info['mobile']!=""&&$user_info['province_id']!=0&&$user_info['city_id']!=0&&$user_info['byear']!=0&&$user_info['bmonth']!=0&&$user_info['bday']!=0&&$user_info['sex']!=-1&&trim($user_info['my_intro'])!=''&&get_user_avatar($user_id,"small")!=APP_ROOT."/public/avatar/noavatar_small.gif")
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
				$result['info'] = "您的个人资料未完善以及头像";
				$result['jump'] = url("shop","uc_account");
			}
		}
		return $result;
	}
	
	public function check_medal()
	{
		//30天后回收
		$user_id = intval($GLOBALS['user_info']['id']);		
		$medal = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."medal where class_name = 'Newuser'");		
		$medal_log = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."user_medal where is_delete = 0 and user_id = ".$user_id." and medal_id = ".$medal['id']);
		if($medal_log&&TIME_UTC>=($medal_log['create_time']+30*24*3600))
		{
				//超过三十天
				$GLOBALS['db']->query("update ".DB_PREFIX."user_medal set is_delete = 1 where user_id = ".$user_id." and medal_id = ".$medal['id']);
				//更新为删除状态，表示该勋章只会发放一次
				return array("status"=>0,"info"=>"您的".$medal['name']."已过期了，被系统回收");  //不会回收该勋章
		}
		else
		{
				return array("status"=>1,"info"=>"");  //不会回收该勋章
		}
	
	}
}
?>