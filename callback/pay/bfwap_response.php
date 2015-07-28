<?php
if(!defined('ROOT_PATH'))
define('ROOT_PATH', str_replace('callback/pay/bfwap_response.php', '', str_replace('\\', '/', __FILE__)));

global $pay_req;
$pay_req['ctl'] = "payment";
$pay_req['act'] = "response";
$pay_req['class_name'] = "Bfwap";
$pay_req['is_sj'] = 1;

include ROOT_PATH."index.php";


?>