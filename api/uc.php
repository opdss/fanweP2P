<?php
// +----------------------------------------------------------------------
// | Fanwe 方维p2p借贷系统
// +----------------------------------------------------------------------
// | Copyright (c) 2011 http://www.fanwe.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 云淡风轻(88522820@qq.com)
// +----------------------------------------------------------------------

if(!file_exists("../public/uc_config.php")){
	exit();
}

require '../system/common.php';
require '../public/uc_config.php';

//积分的配置
$credits_CFG = array(
	'1' => array('title'=>'经验', 'unit'=>'' ,'field'=>'point'),
	'2' => array('title'=>'积分', 'unit'=>'' ,'field'=>'score'),
	'3' => array('title'=>'资金', 'unit'=>'' ,'field'=>'money'),
);

//常量定义
define('UC_CLIENT_VERSION', '1.5.0');	//note UCenter 版本标识
define('UC_CLIENT_RELEASE', '20081031');

define('API_DELETEUSER', 1);		//note 用户删除 API 接口开关
define('API_RENAMEUSER', 1);		//note 用户改名 API 接口开关
define('API_GETTAG', 1);		//note 获取标签 API 接口开关
define('API_SYNLOGIN', 1);		//note 同步登录 API 接口开关
define('API_SYNLOGOUT', 1);		//note 同步登出 API 接口开关
define('API_UPDATEPW', 1);		//note 更改用户密码 开关
define('API_UPDATEBADWORDS', 1);	//note 更新关键字列表 开关
define('API_UPDATEHOSTS', 1);		//note 更新域名解析缓存 开关
define('API_UPDATEAPPS', 1);		//note 更新应用列表 开关
define('API_UPDATECLIENT', 1);		//note 更新客户端缓存 开关
define('API_UPDATECREDIT', 1);		//note 更新用户积分 开关
define('API_GETCREDITSETTINGS', 1);	//note 向 UCenter 提供积分设置 开关
define('API_GETCREDIT', 1);		//note 获取用户的某项积分 开关
define('API_UPDATECREDITSETTINGS', 1);	//note 更新应用积分设置 开关

define('API_RETURN_SUCCEED', '1');
define('API_RETURN_FAILED', '-1');
define('API_RETURN_FORBIDDEN', '-2');

$GLOBALS['cookie_path'] = str_replace("api/","",$GLOBALS['cookie_path']);

//数据验证
if(!defined('IN_UC'))
{
    error_reporting(0);
    set_magic_quotes_runtime(0);
    defined('MAGIC_QUOTES_GPC') || define('MAGIC_QUOTES_GPC', get_magic_quotes_gpc());

    $_DCACHE = $get = $post = array();

    $code = @$_GET['code'];
   
    parse_str(_authcode($code, 'DECODE', UC_KEY), $get);

    if(MAGIC_QUOTES_GPC)
    {
        $get = _stripslashes($get);
    }

    $timestamp = time();
    if($timestamp - $get['time'] > 3600)
    {
        exit('Authracation has expiried');
    }
    if(empty($get))
    {
        exit('Invalid Request');
    }
}



$action = $get['action'];
include(APP_ROOT_PATH . 'uc_client/lib/xml.class.php');
$post = xml_unserialize(file_get_contents('php://input'));

if(in_array($get['action'], array('test', 'deleteuser', 'renameuser', 'gettag', 'synlogin', 'synlogout', 'updatepw', 'updatebadwords', 'updatehosts', 'updateapps', 'updateclient', 'updatecredit', 'getcreditsettings', 'updatecreditsettings')))
{
    $uc_note = new uc_note();
    exit($uc_note->$get['action']($get, $post));
}
else
{
    exit(API_RETURN_FAILED);
}


class uc_note {

	var $dbconfig = '';
	//var $db = '';
	var $appdir = '';

	function _serialize($arr, $htmlon = 0) {
		if(!function_exists('xml_serialize')) {
			include_once APP_ROOT_PATH.'uc_client/lib/xml.class.php';
		}
		return xml_serialize($arr, $htmlon);
	}

	function uc_note() {
		$this->appdir = APP_ROOT_PATH;
		$this->dbconfig = $GLOBALS['db']; 
	}

	function test($get, $post) {
		return API_RETURN_SUCCEED;
	}

	function deleteuser($get, $post) {
		/*代码省略*/
		return API_RETURN_SUCCEED;
	}
	
	/* 更多接口项目 */
    function synlogin($get, $post)
    {
        $uid = intval($get['uid']);
        $username = $get['username'];
        if(!API_SYNLOGIN)
        {
            return API_RETURN_FORBIDDEN;
        }
        //开始同步o2o会员登录
        $user_info = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."user where integrate_id = ".$uid);
        if(!$user_info)
        {
        	//无会员开始自动注册
        	include_once(APP_ROOT_PATH . 'uc_client/client.php');
        	if($uc_data = uc_get_user($username))
			{
			    list($uid, $uname, $email) = $uc_data;
				if(UC_CHARSET!='utf-8')
				{				
					$uname = iconv(UC_CHARSET,"utf-8",$uname);
					$email = iconv(UC_CHARSET,"utf-8",$email);
				}
		       	if(!$GLOBALS['db']->getOne("select count(*) from ".DB_PREFIX."user where user_name = '".$uname."'")>0) 
				{
					$user_info = array();
					$user_info['is_effect'] = intval(app_conf("USER_VERIFY"));
					if($GLOBALS['db']->getOne("select count(*) from ".DB_PREFIX."user where email = '".$email."'")>0)  //会员邮箱已存在时邮箱留空
					{
						$email = '';
						$user_info['is_effect'] = 1;
					}
					
					$user_info['email'] = $email;
					$user_info['user_name'] = $uname;
					$user_info['user_pwd'] = '';
					$user_info['integrate_id'] = $uid;					
					$user_info['group_id'] = $GLOBALS['db']->getOne("select id from ".DB_PREFIX."user_group order by score asc limit 1");
			
							
					$GLOBALS['db']->autoExecute(DB_PREFIX."user",$user_info);
					$user_info['id'] = $GLOBALS['db']->insert_id();
				}
				
			}        	
        }
        if(intval($user_info['is_effect'])==1)
        {
        	es_session::set("user_info",$user_info);
        }
    }
    
	function synlogout($get, $post)
    {
    	if(!API_SYNLOGOUT)
        {
            return API_RETURN_FORBIDDEN;
        }
        es_cookie::delete("user_name");
		es_cookie::delete("user_pwd");
        es_session::delete("user_info");        
    }
    
    function updatepw($get, $post)
    {
        if(!API_UPDATEPW)
        {
            return API_RETURN_FORBIDDEN;
        }
        $username = $get['username'];
        $newpw = md5($get['password']);
        $GLOBALS['db']->query("UPDATE " .DB_PREFIX."user SET user_pwd='$newpw' WHERE user_name='$username'");
        return API_RETURN_SUCCEED;
    }
    
	function updatebadwords($get, $post)
    {
        if(!API_UPDATEBADWORDS)
        {
            return API_RETURN_FORBIDDEN;
        }
        $cachefile = $this->appdir.'public/uc_data/badwords.php';
        $fp = fopen($cachefile, 'w');
        $data = array();
        if(is_array($post)) {
            foreach($post as $k => $v) {
                $data['findpattern'][$k] = $v['findpattern'];
                $data['replace'][$k] = $v['replacement'];
            }
        }
        $s = "<?php\r\n";
        $s .= '$_CACHE[\'badwords\'] = '.var_export($data, TRUE).";\r\n";
        fwrite($fp, $s);
        fclose($fp);
        return API_RETURN_SUCCEED;
    }

    function updatehosts($get, $post)
    {
        if(!API_UPDATEHOSTS)
        {
            return API_RETURN_FORBIDDEN;
        }
        $cachefile = $this->appdir.'public/uc_data/hosts.php';
        $fp = fopen($cachefile, 'w');
        $s = "<?php\r\n";
        $s .= '$_CACHE[\'hosts\'] = '.var_export($post, TRUE).";\r\n";
        fwrite($fp, $s);
        fclose($fp);
        return API_RETURN_SUCCEED;
    }

    function updateapps($get, $post)
    {
        if(!API_UPDATEAPPS)
        {
            return API_RETURN_FORBIDDEN;
        }
        $UC_API = $post['UC_API'];

        if($post)
        {
        $cachefile = $this->appdir.'public/uc_data/apps.php';
        $fp = fopen($cachefile, 'w');
        $s = "<?php\r\n";
        $s .= '$_CACHE[\'apps\'] = '.var_export($post, TRUE).";\r\n";
        fwrite($fp, $s);
        fclose($fp);
        #clear_cache_files();
        }
        return API_RETURN_SUCCEED;
    }

    function updateclient($get, $post)
    {
        if(!API_UPDATECLIENT)
        {
            return API_RETURN_FORBIDDEN;
        }
        $cachefile = $this->appdir.'public/uc_data/settings.php';
        $fp = fopen($cachefile, 'w');
        $s = "<?php\r\n";
        $s .= '$_CACHE[\'settings\'] = '.var_export($post, TRUE).";\r\n";
        fwrite($fp, $s);
        fclose($fp);
        return API_RETURN_SUCCEED;
    }
    
    
	function getcreditsettings($get, $post) {
		
		global $credits_CFG;
		if(!API_GETCREDITSETTINGS) {
			return API_RETURN_FORBIDDEN;
		}

		$credits = array();
		foreach($credits_CFG as $id => $extcredits) {
			$credits[$id] = array(strip_tags($extcredits['title']), $extcredits['unit']);
		}

		return $this->_serialize($credits);
	}
	
	
	function updatecreditsettings($get, $post) {
		global $credits_CFG;

		if(!API_UPDATECREDITSETTINGS) {
			return API_RETURN_FORBIDDEN;
		}

		$outextcredits = array();
		foreach($get['credit'] as $appid => $credititems) {
			if($appid == UC_APPID) {
				foreach($credititems as $value) {
					$outextcredits[$value['appiddesc'].'_'.$value['creditdesc']] = array(
						'appiddesc' => $value['appiddesc'],
						'creditdesc' => $value['creditdesc'],
						'creditsrc' => $value['creditsrc'],
						'title' => $value['title'],
						'unit' => $value['unit'],
						'ratiosrc' => $value['ratiosrc'],
						'ratiodesc' => $value['ratiodesc'],
						'ratio' => $value['ratio']
					);
				}
			}
		}


		$cachefile = $this->appdir.'public/uc_data/creditsettings.php';
		$fp = fopen($cachefile, 'w');
		$s = "<?php\r\n";
		$s .= '$_CACHE[\'creditsettings\'] = '.var_export($outextcredits, TRUE).";\r\n";
		fwrite($fp, $s);
		fclose($fp);

		return API_RETURN_SUCCEED;
	}
	
	//获取指定用户积分
	function getcredit($get, $post) {
		global $credits_CFG;
		if(!API_GETCREDIT) {
			return API_RETURN_FORBIDDEN;
		}
		$uid = intval($get['uid']);
		$credit = intval($get['credit']);
		$field = $credits_CFG[$credit]['field'];
		return $GLOBALS['db']->getOne("select ".$field." from ".DB_PREFIX."user where is_delete = 0 and is_effect = 1 and integrate_id = ".$uid);
	}
	
	//api更新用户积分
	function updatecredit($get, $post) {
		global $credits_CFG;
		if(!API_UPDATECREDIT) {
			return API_RETURN_FORBIDDEN;
		}

		$credit = $get['credit'];  //积分配置id
		$amount = $get['amount'];  //值
		$uid = $get['uid'];
		$user = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."user where is_delete = 0 and is_effect = 1 and integrate_id = ".$uid);
		if(!$user) {
			return API_RETURN_SUCCEED;
		}
		else
		{		
			$field = $credits_CFG[$credit]['field'];
			require_once APP_ROOT_PATH."system/libs/user.php";
			modify_account(array($field=>$amount),$user['id'],"ucenter兑换收入",22);
		}

		return API_RETURN_SUCCEED;
	}

}





/**
 *  uc自带函数1
 *
 * @access  public
 * @param   string  $string
 *
 * @return  string  $string
 */
function _setcookie($var, $value, $life = 0, $prefix = 1)
{
    global $cookiepre, $cookiedomain, $cookiepath, $timestamp, $_SERVER;
    setcookie(($prefix ? $cookiepre : '').$var, $value,
        $life ? $timestamp + $life : 0, $cookiepath,
        $cookiedomain, $_SERVER['SERVER_PORT'] == 443 ? 1 : 0);
}

/**
 *  uc自带函数2
 *
 * @access  public
 *
 * @return  string  $string
 */
function _authcode($string, $operation = 'DECODE', $key = '', $expiry = 0)
{
    $ckey_length = 4;
    $key = md5($key ? $key : UC_KEY);
    $keya = md5(substr($key, 0, 16));
    $keyb = md5(substr($key, 16, 16));
    $keyc = $ckey_length ? ($operation == 'DECODE' ? substr($string, 0, $ckey_length): substr(md5(microtime()), -$ckey_length)) : '';

    $cryptkey = $keya.md5($keya.$keyc);
    $key_length = strlen($cryptkey);

    $string = $operation == 'DECODE' ? base64_decode(substr($string, $ckey_length)) : sprintf('%010d', $expiry ? $expiry + time() : 0).substr(md5($string.$keyb), 0, 16).$string;
    $string_length = strlen($string);

    $result = '';
    $box = range(0, 255);

    $rndkey = array();
    for($i = 0; $i <= 255; $i++)
    {
        $rndkey[$i] = ord($cryptkey[$i % $key_length]);
    }

    for($j = $i = 0; $i < 256; $i++)
    {
        $j = ($j + $box[$i] + $rndkey[$i]) % 256;
        $tmp = $box[$i];
        $box[$i] = $box[$j];
        $box[$j] = $tmp;
    }

    for($a = $j = $i = 0; $i < $string_length; $i++)
    {
        $a = ($a + 1) % 256;
        $j = ($j + $box[$a]) % 256;
        $tmp = $box[$a];
        $box[$a] = $box[$j];
        $box[$j] = $tmp;
        $result .= chr(ord($string[$i]) ^ ($box[($box[$a] + $box[$j]) % 256]));
    }

    if($operation == 'DECODE')
    {
        if((substr($result, 0, 10) == 0 || substr($result, 0, 10) - time() > 0) && substr($result, 10, 16) == substr(md5(substr($result, 26).$keyb), 0, 16))
        {
            return substr($result, 26);
        }
        else
        {
            return '';
        }
    }
    else
    {
        return $keyc.str_replace('=', '', base64_encode($result));
    }
}

/**
 *  uc自带函数3
 *
 * @access  public
 * @param   string  $string
 *
 * @return  string  $string
 */
function _stripslashes($string)
{
    if(is_array($string))
    {
        foreach($string as $key => $val)
        {
            $string[$key] = _stripslashes($val);
        }
    }
    else
    {
        $string = stripslashes($string);
    }
    return $string;
}
?>