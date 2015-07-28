<?php 
// +----------------------------------------------------------------------
// | Fanwe 方维p2p借贷系统
// +----------------------------------------------------------------------
// | Copyright (c) 2011 http://www.fanwe.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 云淡风轻(88522820@qq.com)
// +----------------------------------------------------------------------

if (isset($read_api) && $read_api == true)
{
    return false;
}
if(!defined('IS_CGI'))
define('IS_CGI',substr(PHP_SAPI, 0,3)=='cgi' ? 1 : 0 );
 if(!defined('_PHP_FILE_')) {
        if(IS_CGI) {
            //CGI/FASTCGI模式下
            $_temp  = explode('.php',$_SERVER["PHP_SELF"]);
            define('_PHP_FILE_',  rtrim(str_replace($_SERVER["HTTP_HOST"],'',$_temp[0].'.php'),'/'));
        }else {
            define('_PHP_FILE_',  rtrim($_SERVER["SCRIPT_NAME"],'/'));
        }
    }
if(!defined('APP_ROOT')) {
        // 网站URL根目录
        $_root = dirname(_PHP_FILE_);
        $_root = (($_root=='/' || $_root=='\\')?'':$_root);
        $_root = str_replace("/dh","",$_root);
        define('APP_ROOT', $_root  );
}


require '../system/common.php';
require '../app/Lib/tuan_init.php';


function emptyTag($string)
{
		if(empty($string))
			return "";
			
		$string = strip_tags(trim($string));
		$string = preg_replace("|&.+?;|",'',$string);
		
		return $string;
}
function convertUrl($url)
{
		$url = str_replace("&","&amp;",$url);
		return $url;
}
?>