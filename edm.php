<?php
// +----------------------------------------------------------------------
// | Fanwe 方维p2p借贷系统
// +----------------------------------------------------------------------
// | Copyright (c) 2011 http://www.fanwe.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 云淡风轻(88522820@qq.com)
// +----------------------------------------------------------------------

require_once 'XML/RPC2/Client.php';
if(!defined('APP_ROOT_PATH')) 
define('APP_ROOT_PATH', str_replace('edm.php', '', str_replace('\\', '/', __FILE__)));

if(!function_exists("app_conf"))
{
	//引入数据库的系统配置及定义配置函数
	$sys_config = require APP_ROOT_PATH.'system/config.php';
	function app_conf($name)
	{
		return stripslashes($GLOBALS['sys_config'][$name]);
	}
}

$options = array(
    'prefix' => '1.5.',  //版本，不需要更改
	'encoding'=>'utf-8',
	'debug'=>false			//是否打印信息
);
$api_url='http://api.lian-wo.com/';		# api地址，不需要更改
$username= app_conf("EDM_USERNAME");			# 您的账号
$password= app_conf("EDM_PASSWORD");				# 您的密码

$client = XML_RPC2_Client::create($api_url, $options);
$token = $client->login($username,$password);

//邮件地址 多个邮件地址用英文逗号隔开
//$email='test@gmail.com,test1@gmail.com';
function create_email($email,$client,$token)
{	
	try 
	{		    
		$group_id = $client->createGroup($token,"easethink_".time());	    
		//指定所属组
		$group=array($group_id);	
		$email_id=$client->createEmail($token,$email,$group);
		return $group_id;
	
	} catch (XML_RPC2_FaultException $e) {	
	    // The XMLRPC server returns a XMLRPC error
	    return 'Exception #' . $e->getFaultCode() . ' : ' . $e->getFaultString();
	
	} catch (Exception $e) {	
	    // Other errors (HTTP or networking problems...)
	   return 'Exception : ' . $e->getMessage();	
	}
}

//创建群发的内容
function create_template($subject,$send_email,$send_name,$content,$client,$token)
{
	try {
		
		//邮件标题
		$array['subject']=$subject;
	
		$array['send_email']=$send_email;
		$array['send_name']=$send_name;
	
		$array['reply_email']=$send_email;
		$array['reply_name']=$send_name;
	
		$array['content']=$content;
	
		$template_id=$client->createTemplate($token,"easethink_".time(),$array);
		return $template_id;
	
	} catch (XML_RPC2_FaultException $e) {
	
	    // The XMLRPC server returns a XMLRPC error
	    return 'Exception #' . $e->getFaultCode() . ' : ' . $e->getFaultString();
	
	} catch (Exception $e) {
	
	    // Other errors (HTTP or networking problems...)
	    return 'Exception : ' . $e->getMessage();	
	}
}

function send_mail($email,$subject,$send_email,$send_name,$content,$send_time,$client,$token)
{
	
	$send_group = intval(create_email($email,$client,$token));	
	$template_id = intval(create_template($subject,$send_email,$send_name,$content,$client,$token));	
	$send_group = $send_group.'';


	try {
		//禁止发送的组，多个组用英文逗号隔开
		$forbidden_group='-1';		
		$client->createPlan($token,$template_id,$send_time,$send_group,$forbidden_group);
		return "success";
	
	} catch (XML_RPC2_FaultException $e) {
	
	    // The XMLRPC server returns a XMLRPC error
	    return 'Exception #' . $e->getFaultCode() . ' : ' . $e->getFaultString();
	
	} catch (Exception $e) {
	
	    // Other errors (HTTP or networking problems...)
	    return 'Exception : ' . $e->getMessage();
	
	}
}
//
//send_mail("fzmatthew@qq.com","测试邮件","fzmatthew@qq.com","易想","测试内容","2010-01-01 23:50:12",$client,$token);
//exit;
?>