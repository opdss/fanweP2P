<?php
require '../system/common.php';
require '../app/Lib/deal.php';
require '../app/Lib/common.php';
//获取还款中的标
$page = intval($_REQUEST['p']);
if($page==0)
	$page = 1;
$limit = (($page-1)*app_conf("DEAL_PAGE_SIZE")).",".app_conf("DEAL_PAGE_SIZE");

$id = intval($_REQUEST['id']);
if($id > 0)
	$extW = " id=".$id;
else
	$extW = " deal_status = 4 ";
$result = get_deal_list($limit,0,$extW);

foreach($result['list'] as $k=>$v){
	make_repay_plan($v);
}

$pages_all = ceil($result['count']/app_conf("DEAL_PAGE_SIZE"));

if($page < $pages_all){
	app_redirect("update.php?p=".($page+1));
}
else{
	echo "数据更新完成";
}