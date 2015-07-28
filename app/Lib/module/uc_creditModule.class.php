<?php
// +----------------------------------------------------------------------
// | Fanwe 方维p2p借贷系统
// +----------------------------------------------------------------------
// | Copyright (c) 2011 http://www.fanwe.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 云淡风轻(88522820@qq.com)
// +----------------------------------------------------------------------

require APP_ROOT_PATH.'app/Lib/uc.php';

class uc_creditModule extends SiteBaseModule
{
	public function index(){
		$GLOBALS['tmpl']->assign("page_title",$GLOBALS['lang']['UC_CREDIT']);
		
		$list = load_auto_cache("level");
		
		$GLOBALS['tmpl']->assign("list",$list['list']);
		
		foreach($list["list"] as $k=>$v){
			if($v['id'] ==  $GLOBALS['user_info']['level_id'])
				$user_point_level = $v['name'];
		}
		
		//可用额度
		$can_use_quota=get_can_use_quota($GLOBALS['user_info']['id']);
		$GLOBALS['tmpl']->assign('can_use_quota',$can_use_quota);
		
		
		$credit_file = get_user_credit_file($GLOBALS['user_info']['id'],$GLOBALS['user_info']);
		
		//必要信用认证
    	$level_point['need_other_point'] = 0;
    	$must_credit_count = 0;
    	$other_credit_count = 0;
    	foreach($credit_file as $k=>$v){
    		if($v['passed']== 1)
	    		$level_point['need_other_point'] += (int)trim($v['point']);
	    	if($v['must'] == 1)
	    		$must_credit_count ++;
	    	else
	    		$other_credit_count ++;
    	}
    	
    	
    	//还清
    	$level_point['repay_success'] = $GLOBALS['db']->getRow("SELECT sum(point) as total_point,count(*) AS total_count FROM ".DB_PREFIX."user_log WHERE log_info like '%还清借款%' AND user_id=".$GLOBALS['user_info']['id']);
    	//逾期
    	$level_point['impose_repay'] = $GLOBALS['db']->getRow("SELECT sum(point) as total_point,count(*) AS total_count FROM ".DB_PREFIX."user_log WHERE log_info like '%逾期还款%' and log_info not like '%严重逾期还款%' AND user_id=".$GLOBALS['user_info']['id']);
    	//严重逾期
    	$level_point['yz_impose_repay'] = $GLOBALS['db']->getRow("SELECT sum(point) as total_point,count(*) AS total_count FROM ".DB_PREFIX."user_log WHERE log_info like '%严重逾期还款%' AND user_id=".$GLOBALS['user_info']['id']);
    	
    	$GLOBALS['tmpl']->assign('level_point',$level_point);
		
		$GLOBALS['tmpl']->assign("credit_file",$credit_file);
		$GLOBALS['tmpl']->assign("must_credit_count",$must_credit_count);
		$GLOBALS['tmpl']->assign("other_credit_count",$other_credit_count);
		
		$GLOBALS['tmpl']->assign("user_point_level",$user_point_level);
		
		$GLOBALS['tmpl']->assign("inc_file","inc/uc/uc_credit.html");
		$GLOBALS['tmpl']->display("page/uc.html");
	}
}
?>