<?php
if(!defined('ROOT_PATH'))
define('ROOT_PATH', str_replace('baofoo_callback.php', '', str_replace('\\', '/', __FILE__)));

global $pay_req;
$pay_req['ctl'] = "payment";
$pay_req['act'] = isset($_REQUEST['act']) ? $_REQUEST['act'] : "response";
if($pay_req['act'] ==""){
	$pay_req['act'] = "response";
}
$pay_req['class_name'] = "Baofoo";
include ROOT_PATH."index.php";
?>