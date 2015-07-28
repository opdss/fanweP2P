<?php
if(!defined('ROOT_PATH'))
define('ROOT_PATH', str_replace('jfb_response.php', '', str_replace('\\', '/', __FILE__)));

global $pay_req;
$pay_req['ctl'] = "payment";
$pay_req['act'] = "response";
$pay_req['class_name'] = "Jfb";
include ROOT_PATH."index.php";
?>