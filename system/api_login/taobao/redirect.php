<?php
if(!defined('APP_ROOT_PATH')) 
define('APP_ROOT_PATH', str_replace('system/api_login/taobao/redirect.php', '', str_replace('\\', '/', __FILE__)));
require_once APP_ROOT_PATH.'system/utils/es_session.php';

		es_session::start();
		require_once "alipay_service.class.php";
		$aliapy_config['partner']		= es_session::get('taobao_app_key');
		$aliapy_config['key']			=  es_session::get('taobao_app_secret');
		$aliapy_config['return_url']   = es_session::get('taobao_callback');
		$aliapy_config['sign_type']    = 'MD5';
		$aliapy_config['input_charset']      = 'utf-8';
		$aliapy_config['transport']    = 'http';
		$anti_phishing_key  = '';
		$exter_invoke_ip = '';
		
		$parameter = array(
		        //扩展功能参数——防钓鱼
		        "anti_phishing_key"	=> $anti_phishing_key,
				"exter_invoke_ip"	=> $exter_invoke_ip,
		);
		
		//构造快捷登录接口
		$alipayService = new AlipayService($aliapy_config);
		$html_text = $alipayService->alipay_auth_authorize($parameter);
		echo $html_text;
?>