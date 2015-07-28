<?php
// +----------------------------------------------------------------------
// | Fanwe 方维p2p借贷系统
// +----------------------------------------------------------------------
// | Copyright (c) 2011 http://www.fanwe.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 云淡风轻(88522820@qq.com)
// +----------------------------------------------------------------------

$lang = array(
	'name'	=>	'phpwind',
	'DB_HOST'	=>	'数据库主机名',
	'DB_USER'	=>	'数据库用户名',
	'DB_PASS'	=>	'数据库密码',
	'DB_NAME'	=>	'数据库名',
	'DB_CHARSET'	=>	'数据库字符集',
	'DB_CHARSET_utf8'	=>	'UTF-8',
	'DB_CHARSET_gbk'	=>	'GBK/gb2312',
	'DB_CHARSET_big5'	=>	'big5',
	'IS_LATIN1'	=>	'是否为latin1编码',
	'IS_LATIN1_1'	=>	'否',
	'IS_LATIN1_2'	=>	'是',
	'PREFIX'	=>	'表前缀',
	'SUFFIX'	=>	'同名用户后缀',
	
);

$config = array(
	'DB_HOST'	=>	'',
	'DB_USER'	=>	'',
	'DB_PASS'	=>	array(
		'INPUT_TYPE'	=>	'2'
	),
	'DB_NAME'	=>	'',
	'DB_CHARSET'	=>	array(
		'INPUT_TYPE'	=>	'1',
		'VALUES'	=> 	array('utf8','gbk','big5')
	),
	'IS_LATIN1'	=>	array(
		'INPUT_TYPE'	=>	'1',
		'VALUES'	=> 	array(1,2)
	),
	'PREFIX'	=>	'',
	'SUFFIX'	=>	''
);

/* 模块的基本信息 */
if (isset($read_modules) && $read_modules == true)
{
    $module['class_name']    = 'Phpwind';

    /* 名称 */
    $module['name']    = $lang['name'];

    $module['lang'] = $lang;
    $module['config'] = $config;
    
    return $module;
}

// phpwind会员整合
require_once(APP_ROOT_PATH.'system/libs/integrate.php');
class Phpwind_integrate implements integrate {
	var $pwdb = NULL;
	/* 论坛加密密钥 */
    var $db_hash = '';
    var $db_sitehash = '';
	public function __construct()
	{
		$config = unserialize(app_conf("INTEGRATE_CFG"));
		if($config['IS_LATIN1']==2)
		$config['DB_CHARSET'] = 'latin1';
		$this->pwdb =  new mysql_db($config['DB_HOST'], $config['DB_USER'], $config['DB_PASS'], $config['DB_NAME'],$config['DB_CHARSET'],0,1);
		
		$db_hash = $this->pwdb->query("SELECT `db_value` FROM ".$config['PREFIX']."config WHERE `db_name` = 'db_hash'",'SILENT');
		$db_hash = mysql_fetch_row($db_hash);
		if($db_hash)
		$this->db_hash = $db_hash[0];
        $db_sitehash = $this->pwdb->query("SELECT `db_value` FROM ".$config['PREFIX']."config WHERE `db_name` = 'db_sitehash'",'SILENT');
        $db_sitehash = mysql_fetch_row($db_sitehash);
        if($db_sitehash)
		$this->db_sitehash = $db_sitehash[0];
	}
	
	//用户登录
	public function login($user_name,$user_pwd)
	{			
		$config = unserialize(app_conf("INTEGRATE_CFG"));
        $cookie_name = substr(md5($this->db_sitehash), 0, 5) . '_winduser';
        
        $es_user = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."user where user_name = '".$user_name."'");
        if($es_user&&$es_user['user_pwd']==md5($user_pwd.$es_user['code']))
        {
        	//易想系统登录成功,开始执行同步登录
        	$sql = "SELECT uid AS user_id, username,password,email,safecv".
                   " FROM " . $config['PREFIX'] .
                   "members WHERE username ='".$user_name.$config['SUFFIX']."'";        	
        	$row = $this->pwdb->getRow($sql);
        	if(!$row)
        	{
        		$sql = "SELECT uid AS user_id, username,password,email,safecv".
	                   " FROM " . $config['PREFIX'] .
	                   "members WHERE username ='".$user_name."'";	
	        	$row = $this->pwdb->getRow($sql);
        	}   

        	$cookie_name = substr(md5($this->db_sitehash), 0, 5) . '_winduser';
			$salt =  md5($_SERVER["HTTP_USER_AGENT"] . $row['password'] . $this->db_hash);						
			$auto_login_key = $this->code_string($row['user_id']."\t".$salt."\t".$row['safecv'], 'ENCODE');						
			setcookie($cookie_name, $auto_login_key, time()+3600*24*30,"/"); 
			return array('status'=>1,'data'=>'','msg'=>''); 	
        }
        else
        {
        	//无会员或验证密码失败
        	if(!$es_user)
        	{
        		//无会员时，查询是否在pw有该会员
        		$sql = "SELECT uid AS user_id, username,password,email,safecv".
	                   " FROM " . $config['PREFIX'] .
	                   "members WHERE username ='".$user_name."'";	
	        	$row = $this->pwdb->getRow($sql);
	        	if($row&&$row['password']==md5($user_pwd))  //pw有该会员并且密码正确
	        	{
	        		//将pw会员同步到易想系统
	        		$ease_user = array();
					$ease_user['is_effect'] = 1;
					if($GLOBALS['db']->getOne("select count(*) from ".DB_PREFIX."user where email = '".$row['email']."'")>0)  //会员邮箱已存在时邮箱留空
					{
							$email = ''; 
					}
											
					$ease_user['email'] = $email;
					$ease_user['user_name'] = $row['username'];
					$ease_user['user_pwd'] = $row['password'];
					$ease_user['integrate_id'] = $row['user_id'];
						
					$ease_user['group_id'] = $GLOBALS['db']->getOne("select id from ".DB_PREFIX."user_group order by score asc limit 1");
					$GLOBALS['db']->autoExecute(DB_PREFIX."user",$ease_user);
					$cookie_name = substr(md5($this->db_sitehash), 0, 5) . '_winduser';
					$salt =  md5($_SERVER["HTTP_USER_AGENT"] . $row['password'] . $this->db_hash);						
					$auto_login_key = $this->code_string($row['user_id']."\t".$salt."\t".$row['safecv'], 'ENCODE');						
					setcookie($cookie_name, $auto_login_key, time()+3600*24*30,"/"); 
					return array('status'=>1,'data'=>'','msg'=>''); 
	        	}
	        	else
	        	{
	        		// pw也不存在该会员，则不作任何处理
	        	}
        	}
        }	
	}
    /* 加密解密函数，自动登录密钥也是用该函数进行加密解密 */
   private function code_string($string, $action='ENCODE')
    {
        $key    = substr(md5($_SERVER["HTTP_USER_AGENT"] . $this->db_hash), 8, 18);

        $string = $action == 'ENCODE' ? $string : base64_decode($string);
        $keylen = strlen($key);
        $strlen = strlen($string);
        $code   = '';
        for ($i = 0; $i < $strlen; $i++)
        {
            $k     = $i % $keylen;
            $code .= $string[$i] ^ $key[$k];
        }
        $code = $action == 'DECODE' ? $code : base64_encode($code);
        return $code;
    }	
	//用户登出
	public function logout()
	{		
		$config = unserialize(app_conf("INTEGRATE_CFG"));
        $cookie_name = substr(md5($this->db_sitehash), 0, 5) . '_winduser';
        $time = time() - 3600;
        setcookie($cookie_name, '', $time, "/");
        return array('status'=>1,'data'=>'','msg'=>''); 		
	}
	
	//用户注册
	public function add_user($user_name,$user_pwd,$email)
	{
		 $config = unserialize(app_conf("INTEGRATE_CFG"));
		 $pw_userinfo = $this->pwdb->getRow("SELECT * FROM ".$config['PREFIX']."members WHERE `username`='".$user_name."'");
		 if(!$pw_userinfo)
		 {
		            $this->pwdb->query("INSERT INTO ".$config['PREFIX']."members SET username='".$user_name."', password='".md5($user_pwd)."', email='".$email."', regdate='".TIME_UTC."'", 'SILENT');
					$uid = intval($this->pwdb->insert_id());
		 }
		 else
		 {
		        	//存在同名会员		        
		        	$user_name = $user_name.$config['SUFFIX'];		        	
		        	$this->pwdb->query("INSERT INTO ".$config['PREFIX']."members SET username='".$user_name."', password='".md5($user_pwd)."', email='".$email."', regdate='".TIME_UTC."'", 'SILENT');
					$uid = intval($this->pwdb->insert_id());
		}
		        
		/* 更新memberdata表 */
		$sql = 'INSERT INTO '. $config['PREFIX'].'memberdata set uid = '.$uid;			               
		$this->pwdb->query($sql);

		return array("status"=>1,'data'=>$uid);
	}
	
	//用户修改,仅用于密码的修改 
	public function edit_user($user_data,$user_new_pwd)
	{
       
	}
	
	//删除用户
	public function delete_user($user_data)
	{
		
	}
	
	public function install($config_seralized)
	{
		$config = unserialize($config_seralized);
		if($config['IS_LATIN1']==2)
		{
			$config['DB_CHARSET']	=	'latin1';
		}
		
		if (!@mysql_pconnect($config['DB_HOST'], $config['DB_USER'], $config['DB_PASS']))
        {
            $result['status'] = 0;
            $result['msg'] = '无法连接数据库';
            return $result;
        }
        else
        {
        	$pwdb = new mysql_db($config['DB_HOST'], $config['DB_USER'], $config['DB_PASS'], $config['DB_NAME'],$config['DB_CHARSET']);
        	//开始将easethink的会员导入到pw中
        	$query = $GLOBALS['db']->query("SELECT * FROM " .DB_PREFIX."user ORDER BY `id` ASC");
		    while($data = $GLOBALS['db']->fetch_array($query))
		    {
		        $pw_userinfo = $pwdb->getRow("SELECT * FROM ".$config['PREFIX']."members WHERE `username`='".$data['user_name']."'");
		        if(!$pw_userinfo)
		        {
		            $pwdb->query("INSERT INTO ".$config['PREFIX']."members SET username='".$data['user_name']."', password='".$data['user_pwd']."', email='".$data['email']."', regdate='".$data['create_time']."'", 'SILENT');
					$integrate_id = intval($pwdb->insert_id());
					$GLOBALS['db']->query("update ".DB_PREFIX."user set integrate_id = ".$integrate_id." where id = ".$data['id']);
					
		        }
		        else
		        {
		        	//存在同名会员		        
		        	$data['user_name'] = $data['user_name'].$config['SUFFIX'];		        	
		        	$pwdb->query("INSERT INTO ".$config['PREFIX']."members SET username='".$data['user_name']."', password='".$data['user_pwd']."', email='".$data['email']."', regdate='".$data['create_time']."'", 'SILENT');
					$integrate_id = intval($pwdb->insert_id());
					$GLOBALS['db']->query("update ".DB_PREFIX."user set integrate_id = ".$integrate_id." where id = ".$data['id']);
		        }
		        
		         /* 更新memberdata表 */
			     $sql = 'INSERT INTO '. $config['PREFIX'].'memberdata set uid = '.$integrate_id;			               
			     $pwdb->query($sql,"SILENT");
		    }
		    unset($query);
        	
        	
        	$result['status'] = 1;
        	return $result;
        }
		
	}
	
	public function uninstall()
	{
		$GLOBALS['db']->query("update ".DB_PREFIX."user set integrate_id = 0");
	}	
	
}


?>