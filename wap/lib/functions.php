<?php

//解析URL标签
// $str = u:shop|acate#index|id=10&name=abc
function parse_url_tag($str)
{
	$key = md5("URL_TAG_".$str);
	if(isset($GLOBALS[$key]))
	{
		return $GLOBALS[$key];
	}

	$url = load_dynamic_cache($key);
	$url=false;
	if($url!==false)
	{
		$GLOBALS[$key] = $url;
		return $url;
	}
	$str = substr($str,2);
	$str_array = explode("|",$str);
	$app_index = $str_array[0];
	$route = $str_array[1];
	$param_tmp = explode("&",$str_array[2]);
	$param = array();

	foreach($param_tmp as $item)
	{
		if($item!='')
			$item_arr = explode("=",$item);
		if($item_arr[0]&&$item_arr[1])
			$param[$item_arr[0]] = $item_arr[1];
	}
	$GLOBALS[$key]= url($app_index,$route,$param);
	set_dynamic_cache($key,$GLOBALS[$key]);
		
	return $GLOBALS[$key];
}


//显示错误
function showErr($msg,$ajax=0,$jump='',$stay=0)
{	
	echo "<script>alert('".$msg."');location.href='".$jump."';</script>";exit;	
}

//显示成功
function showSuccess($msg,$ajax=0,$jump='',$stay=0)
{
	echo "<script>alert('".$msg."');location.href='".$jump."';</script>";exit;
}


//编译生成css文件
function parse_css($urls)
{
	static $color_cfg;
	if(empty($color_cfg))
		$color_cfg = include_once APP_ROOT_PATH."wap/tpl/".TMPL_NAME."/color_cfg.php";
	
	
	$url = md5(implode(',',$urls));
	$css_url = 'public/runtime/wap/statics/'.$url.'.css';
	$url_path = APP_ROOT_PATH.$css_url;
	if(!file_exists($url_path)||IS_DEBUG)
	{
		$tmpl_path = $GLOBALS['tmpl']->_var['TMPL'];

		$css_content = '';
		foreach($urls as $url)
		{
			$css_content .= @file_get_contents($url);
		}
		$css_content = preg_replace("/[\r\n]/",'',$css_content);
		$css_content = str_replace("../images/",$tmpl_path."/images/",$css_content);
		if (is_array($color_cfg)){
			foreach($color_cfg as $k=>$v)
			{
				$css_content = str_replace($k,$v,$css_content);
			}
		}
		//		@file_put_contents($url_path, unicode_encode($css_content));
		@file_put_contents($url_path, $css_content);
	}
	return SITE_DOMAIN."/".APP_ROOT.'/../'.$css_url;
}


//解析URL标签
// $str = u:shop|acate#index|id=10&name=abc
function parse_wap_url_tag($str)
{
	$key = md5("WAP_URL_TAG_".$str);
	if(isset($GLOBALS[$key]))
	{
		return $GLOBALS[$key];
	}

	$url = load_dynamic_cache($key);
	$url=false;
	if($url!==false)
	{
		$GLOBALS[$key] = $url;
		return $url;
	}
	$str = substr($str,2);
	$str_array = explode("|",$str);
	$app_index = $str_array[0];
	$route = $str_array[1];
	$param_tmp = explode("&",$str_array[2]);
	$param = array();

	foreach($param_tmp as $item)
	{
		if($item!='')
			$item_arr = explode("=",$item);
		if($item_arr[0]&&$item_arr[1])
			$param[$item_arr[0]] = $item_arr[1];
	}
	$GLOBALS[$key]= wap_url($app_index,$route,$param);
	set_dynamic_cache($key,$GLOBALS[$key]);
	return $GLOBALS[$key];
}

//wap重写下使用原始链接
function wap_url($app_index,$route="index",$param=array())
{
	$GLOBALS['request']['from']="wap";
	return url($app_index,$route,$param);
}
?>