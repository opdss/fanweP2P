<?php
if(!defined('ROOT_PATH'))
define('ROOT_PATH', str_replace('guofubao_callback.php', '', str_replace('\\', '/', __FILE__)));

global $pay_req;
$pay_req['ctl'] = "payment";
$pay_req['act'] = $_REQUEST['act'];
$pay_req['class_name'] = "Guofubao";
include ROOT_PATH."index.php";
?>