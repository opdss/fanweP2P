<?php 
// +----------------------------------------------------------------------
// | Fanwe 方维p2p借贷系统
// +----------------------------------------------------------------------
// | Copyright (c) 2011 http://www.fanwe.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 云淡风轻(88522820@qq.com)
// +----------------------------------------------------------------------

define("EMPTY_ERROR",1);  //未填写的错误
define("FORMAT_ERROR",2); //格式错误
define("EXIST_ERROR",3); //已存在的错误

define("ACCOUNT_NO_EXIST_ERROR",1); //帐户不存在
define("ACCOUNT_PASSWORD_ERROR",2); //帐户密码错误
define("ACCOUNT_NO_VERIFY_ERROR",3); //帐户未激活


	/**
	 * 生成会员数据
	 * @param $user_data  提交[post或get]的会员数据
	 * @param $mode  处理的方式，注册或保存
	 * 返回：data中返回出错的字段信息，包括field_name, 可能存在的field_show_name 以及 error 错误常量
	 * 不会更新保存的字段为：score,money,verify,pid
	 */
	function save_user($user_data,$mode='INSERT')
	{		
		//开始数据验证
		$res = array('status'=>1,'info'=>'','data'=>''); //用于返回的数据
		if($mode=="INSERT" || isset($user_data['user_name'])){
			if(trim($user_data['user_name'])=='')
			{
				$field_item['field_name'] = 'unit_name';
				$field_item['error']	=	EMPTY_ERROR;
				$res['status'] = 0;
				$res['data'] = $field_item;
				return $res;
			}
			
			if(!preg_match("/^[\x{4e00}-\x{9fa5}_\-]*[0-9a-zA-Z_\-]*[\x{201c}\x{201d}\x{3001}\x{uff1a}\x{300a}\x{300b\x{ff0c}\x{ff1b}\x{3002}_\-]*$/u",$user_data['user_name'])){
				$field_item['field_name'] = 'unit_name';
				$field_item['error']	=	FORMAT_ERROR;
				$res['status'] = 0;
				$res['data'] = $field_item;
				return $res;
			}
		
		}
		
		
		
		if( $mode=="INSERT" || isset($user_data['email'])){
			if($GLOBALS['db']->getOne("select count(*) from ".DB_PREFIX."deal_agency where email = '".trim($user_data['email'])."'")>0)
			{
				$field_item['field_name'] = 'email';
				$field_item['error']	=	EXIST_ERROR;
				$res['status'] = 0;
				$res['data'] = $field_item;
				return $res;
			}
			if(trim($user_data['email'])=='')
			{
				$field_item['field_name'] = 'email';
				$field_item['error']	=	EMPTY_ERROR;
				$res['status'] = 0;
				$res['data'] = $field_item;
				return $res;
			}
			if(!check_email(trim($user_data['email'])))
			{
				$field_item['field_name'] = 'email';
				$field_item['error']	=	FORMAT_ERROR;
				$res['status'] = 0;
				$res['data'] = $field_item;
				return $res;
			}
		}
		
		if( $mode=="INSERT" || isset($user_data['mobile'])){
			
			if((trim($user_data['mobile'])==''))
			{
				$field_item['field_name'] = 'mobile';
				$field_item['error']	=	EMPTY_ERROR;
				$res['status'] = 0;
				$res['data'] = $field_item;
				return $res;
			}
			
			if(!check_mobile(trim($user_data['mobile'])))
			{
				$field_item['field_name'] = 'mobile';
				$field_item['error']	=	FORMAT_ERROR;
				$res['status'] = 0;
				$res['data'] = $field_item;
				return $res;
			}
			
			if($user_data['mobile']!=''&&$GLOBALS['db']->getOne("select count(*) from ".DB_PREFIX."deal_agency where mobile = '".trim($user_data['mobile'])."'")>0)
			{
				$field_item['field_name'] = 'mobile';
				$field_item['error']	=	EXIST_ERROR;
				$res['status'] = 0;
				$res['data'] = $field_item;
				return $res;
			}
		}
		
		//验证结束开始插入数据
		if($mode=="INSERT" || $user_data['user_name'])
			$user['name'] = $user_data['user_name'];
		
		//密码
		if($mode=="INSERT" || $user_data['password'])
			$user['password'] = md5($user_data['password']);
		
		$user['update_time'] = TIME_UTC;
		
		
		if($mode=="INSERT" || $user_data['email'])
			$user['email'] = $user_data['email'];
			
		if($mode=="INSERT" || $user_data['mobile'])
			$user['mobile'] = $user_data['mobile'];
		
		
		if($mode == 'INSERT')
		{
			$user['create_time'] = TIME_UTC;
			$user['code'] = ''; //默认不使用code, 该值用于其他系统导入时的初次认证
		}
		else
		{
			$user['code'] = $GLOBALS['db']->getOne("select code from ".DB_PREFIX."deal_agency where id =".$user_data['id']);
		}
		
		if(isset($user_data['user_pwd'])&&$user_data['user_pwd']!='')
			$user['password'] = md5($user_data['user_pwd'].$user['code']);
		
		$date_time = to_date(TIME_UTC);
		
		
		if($mode == 'INSERT')
		{
			$s_api_user_info = es_session::get("api_manageagency_info");
			$user[$s_api_user_info['field']] = $s_api_user_info['id'];
			es_session::delete("api_manageagency_info");
			$where = '';
		}
		else
		{			
			unset($user['pid']);
			$where = "id=".intval($user_data['id']);
		}
		if($GLOBALS['db']->autoExecute(DB_PREFIX."deal_agency",$user,$mode,$where))
		{
			if($mode == 'INSERT')
			{
				$user_id = $GLOBALS['db']->insert_id();	
				
			}
			else
			{
				$user_id = $user_data['id'];
			}
		}
		$res['data'] = $user_id;
		
		
		
		return $res;
	}

	
	/**
	 * 处理会员登录
	 * @param $user_name_or_email 用户名或邮箱地址
	 * @param $user_pwd 密码
	 * 
	 */
	function do_login_user($user_name_or_email,$user_pwd)
	{
		$user_data = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."deal_agency where name='".$user_name_or_email."'");
			
		if(!$user_data)
		{			
			$result['status'] = 0;
			$result['data'] = ACCOUNT_NO_EXIST_ERROR;
			return $result;
		}
		else
		{
			$result['user'] = $user_data;
			
			$is_use_pass = false;
			
			if(strlen($user_pwd) == 32 && $user_data['password'] == $user_pwd)
			{		
				$is_use_pass = true;								
			}else if($user_data['password'] == md5($user_pwd.$user_data['code']))
			{
				$is_use_pass = true;
			}
						
			if($is_use_pass) //未整合，则直接成功
			{
				$result['status'] = 1;
				
				
				
				es_session::set("manageagency_info",$user_data);
				$GLOBALS['manageagency_info'] = $user_data;
				$GLOBALS['db']->query("update ".DB_PREFIX."deal_agency set login_ip = '".get_client_ip()."',login_time= ".TIME_UTC." where id =".$user_data['id']);				
				
				
				$result['step'] = intval($user_data["step"]);
				
				return $result;
			}else{
				$result['status'] = 0;
				$result['data'] = ACCOUNT_PASSWORD_ERROR;
				return $result;				
			}
		}
	}
	
	/**
	 * 登出,返回 array('status'=>'',data=>'',msg=>'') msg存放整合接口返回的字符串
	 */
	function loginout_user()
	{
		$user_info = es_session::get("manageagency_info");
		if(!$user_info)
		{
			$result['status'] = 0;
		}
		else
		{
			es_session::delete("manageagency_info");
			$result['status'] = 1;
		}
		return $result;
	}
	
	
	
	
	
	/**
	 * 验证会员数据
	 */
	function check_user($field_name,$field_data)
	{		
		//开始数据验证
		$user_data[$field_name] = $field_data;
		$res = array('status'=>1,'info'=>'','data'=>''); //用于返回的数据
		if(trim($user_data['user_name'])==''&&$field_name=='user_name')
		{
			$field_item['field_name'] = 'user_name';
			$field_item['error']	=	EMPTY_ERROR;
			$res['status'] = 0;
			$res['data'] = $field_item;
			return $res;
		}
		if($field_name=='user_name'&&!preg_match("/^[\x{4e00}-\x{9fa5}_\-]*[0-9a-zA-Z_\-]*[\x{201c}\x{201d}\x{3001}\x{uff1a}\x{300a}\x{300b\x{ff0c}\x{ff1b}\x{3002}_\-]*$/u",trim($user_data['user_name'])))
		{
			$field_item['field_name'] = 'user_name';
			$field_item['error']	=	FORMAT_ERROR;
			$res['status'] = 0;
			$res['data'] = $field_item;
			return $res;
		}
		if($field_name=='user_name'&&$GLOBALS['db']->getOne("select count(*) from ".DB_PREFIX."user where user_name = '".trim($user_data['user_name'])."' and id <> ".intval($user_data['id']))>0)
		{
			$field_item['field_name'] = 'user_name';
			$field_item['error']	=	EXIST_ERROR;
			$res['status'] = 0;
			$res['data'] = $field_item;
			return $res;
		}
		
		
		if($field_name=='email'&&$GLOBALS['db']->getOne("select count(*) from ".DB_PREFIX."user where email = '".trim($user_data['email'])."' and id <> ".intval($user_data['id']))>0)
		{
			$field_item['field_name'] = 'email';
			$field_item['error']	=	EXIST_ERROR;
			$res['status'] = 0;
			$res['data'] = $field_item;
			return $res;
		}
		if($field_name=='email'&&trim($user_data['email'])=='')
		{
			$field_item['field_name'] = 'email';
			$field_item['error']	=	EMPTY_ERROR;
			$res['status'] = 0;
			$res['data'] = $field_item;
			return $res;
		}
		if($field_name=='email'&&!check_email(trim($user_data['email'])))
		{
			$field_item['field_name'] = 'email';
			$field_item['error']	=	FORMAT_ERROR;
			$res['status'] = 0;
			$res['data'] = $field_item;
			return $res;
		}
		if($field_name=='mobile'&&intval(app_conf("MOBILE_MUST"))==1&&trim($user_data['mobile'])=='')
		{
			$field_item['field_name'] = 'mobile';
			$field_item['error']	=	EMPTY_ERROR;
			$res['status'] = 0;
			$res['data'] = $field_item;
			return $res;
		}
		
		if($field_name=='mobile'&&!check_mobile(trim($user_data['mobile'])))
		{
			$field_item['field_name'] = 'mobile';
			$field_item['error']	=	FORMAT_ERROR;
			$res['status'] = 0;
			$res['data'] = $field_item;
			return $res;
		}
		if($field_name=='mobile'&&$user_data['mobile']!=''&&$GLOBALS['db']->getOne("select count(*) from ".DB_PREFIX."user where mobile = '".trim($user_data['mobile'])."' and id <> ".intval($user_data['id']))>0)
		{
			$field_item['field_name'] = 'mobile';
			$field_item['error']	=	EXIST_ERROR;
			$res['status'] = 0;
			$res['data'] = $field_item;
			return $res;
		}
		//验证扩展字段
		$field_item = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."user_field where field_name = '".$field_name."'");
	
		if($field_item['is_must']==1&&trim($user_data[$field_item['field_name']])=='')
		{
				$field_item['error']	=	EMPTY_ERROR;
				$res['status'] = 0;
				$res['data'] = $field_item;
				return $res;
		}
		
		
		
		return $res;
	}

?>