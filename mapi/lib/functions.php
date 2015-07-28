<?php
//输出接口数据
function output($data)
{
	header("Content-Type:text/html; charset=utf-8");
	$r_type = intval($_REQUEST['r_type']);//返回数据格式类型; 0:base64;1;json_encode;2:array
	$data['act'] = ACT;
	$data['act_2'] = ACT_2;
	if ($r_type == 0)
	{
		echo base64_encode(json_encode($data));
	}else if ($r_type == 1)
	{
		print_r(json_encode($data));
	}else if ($r_type == 2)
	{
		print_r($data);
	};
	exit;
}



function getMConfig(){

	$m_config = $GLOBALS['cache']->get("m_config");
	if(true || $m_config===false)
	{
		$m_config = array();
		$sql = "select code,val from ".DB_PREFIX."m_config";
		$list = $GLOBALS['db']->getAll($sql);
		foreach($list as $item){
			$m_config[$item['code']] = $item['val'];
		}

		$GLOBALS['cache']->set("m_config",$m_config);
	}
	return $m_config;
}


/**
* 过滤SQL查询串中的注释。该方法只过滤SQL文件中独占一行或一块的那些注释。
*
* @access  public
* @param   string      $sql        SQL查询串
* @return  string      返回已过滤掉注释的SQL查询串。
*/
function remove_comment($sql)
{
	/* 删除SQL行注释，行注释不匹配换行符 */
	$sql = preg_replace('/^\s*(?:--|#).*/m', '', $sql);

	/* 删除SQL块注释，匹配换行符，且为非贪婪匹配 */
	//$sql = preg_replace('/^\s*\/\*(?:.|\n)*\*\//m', '', $sql);
	$sql = preg_replace('/^\s*\/\*.*?\*\//ms', '', $sql);

	return $sql;
}

function emptyTag($string)
{
		if(empty($string))
			return "";

		$string = strip_tags(trim($string));
		$string = preg_replace("|&.+?;|",'',$string);

		return $string;
}

function get_abs_img_root($content)
{	

	return str_replace("./public/",SITE_DOMAIN.APP_ROOT."/../public/",$content);
	//return str_replace('/mapi/','/',$str);
}
function get_abs_url_root($content)
{
	$content = str_replace("./",SITE_DOMAIN.APP_ROOT."/../",$content);	
	return $content;
}


function user_check($username_email,$pwd)
{
	//$username_email = addslashes($username_email);
	//$pwd = addslashes($pwd);
	if($username_email&&$pwd)
	{
		//$sql = "select *,id as uid from ".DB_PREFIX."user where (user_name='".$username_email."' or email = '".$username_email."') and is_delete = 0";
		$sql = "select *,id as uid from ".DB_PREFIX."user where (user_name='".$username_email."' or email = '".$username_email."' or mobile = '".$username_email."') and is_delete = 0";
		$user_info = $GLOBALS['db']->getRow($sql);

		$is_use_pass = false;
		if (strlen($pwd) != 32){					
			if($user_info['user_pwd']==md5($pwd.$user_info['code']) || $user_info['user_pwd']==md5($pwd)){
				$is_use_pass = true;
				
			}
		}
		else{
			if($user_info['user_pwd']==$pwd){
				$is_use_pass = true;
			}
		}
		if($is_use_pass)
		{
			es_session::set("user_info",$user_info);
			$GLOBALS['user_info'] = $user_info;
			return $user_info;
		}
		else
			return null;
	}
	else
	{
		return null;
	}
}

function user_login($username_email,$pwd)
{	
	require_once APP_ROOT_PATH."system/libs/user.php";
	if(check_ipop_limit(CLIENT_IP,"user_dologin",intval(app_conf("SUBMIT_DELAY")))){
		$result = do_login_user($username_email,$pwd);
	}
	else{
		//showErr($GLOBALS['lang']['SUBMIT_TOO_FAST'],$ajax,url("shop","user#login"));
		$result['status'] = 0;
		$result['msg'] = $GLOBALS['lang']['SUBMIT_TOO_FAST'];
		return $result;
	}
	
	if($result['status'])
	{
		//$GLOBALS['user_info'] = $result["user"];
		return $result;	
	}
	else
	{
		$GLOBALS['user_info'] = null;
		unset($GLOBALS['user_info']);
		
		if($result['data'] == ACCOUNT_NO_EXIST_ERROR)
		{
			$err = $GLOBALS['lang']['USER_NOT_EXIST'];
		}
		if($result['data'] == ACCOUNT_PASSWORD_ERROR)
		{
			$err = $GLOBALS['lang']['PASSWORD_ERROR'];
		}
		if($result['data'] == ACCOUNT_NO_VERIFY_ERROR)
		{
			$err = $GLOBALS['lang']['USER_NOT_VERIFY'];			
		}
		
		$result['msg'] = $err;
		return $result;
	}	
}

?>