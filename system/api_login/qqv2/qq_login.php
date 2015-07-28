<?php
define('APP_ROOT_PATH', str_replace('system/api_login/qqv2/qq_login.php', '', str_replace('\\', '/', __FILE__)));
require_once(APP_ROOT_PATH."system/api_login/qqv2/qqConnectAPI.php");
$qc = new QC();
$qc->qq_login();
