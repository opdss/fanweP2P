<?php
// +----------------------------------------------------------------------
// | Fanwe 方维p2p借贷系统
// +----------------------------------------------------------------------
// | Copyright (c) 2011 http://www.fanwe.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 云淡风轻(88522820@qq.com)
// +----------------------------------------------------------------------

require APP_ROOT_PATH.'app/Lib/uc.php';

class uc_autobidModule extends SiteBaseModule
{
    function index() {
    	$GLOBALS['tmpl']->assign("page_title",$GLOBALS['lang']['UC_AUTO_BID']);
    	
    	$deal_cates  = load_auto_cache("cache_deal_cate");
    	$GLOBALS['tmpl']->assign("deal_cates",$deal_cates);
    	
    	$level_list=load_auto_cache("level");
    	$range_date = array();
    	$rate_list = array();
    	foreach($level_list['repaytime_list'] as $k=>$v){
    		foreach($v as $kk=>$vv){
    			if($vv[1]==1)
    				$range_date[(int)$vv[0]] = $vv[0];
    		
    			$rate_list[(int)$vv[2]] = $vv[2];
    			$rate_list[(int)$vv[3]] = $vv[3];
    		}
    	}
    	
    	
    	asort($range_date);
    	sort($range_date);
    	    	
    	$GLOBALS['tmpl']->assign("min_date",$range_date);
    	
    	rsort($range_date);
    	$GLOBALS['tmpl']->assign("max_date",$range_date);
    	
    	
    	asort($rate_list);
    	sort($rate_list);
    	
    	
    	$rateinfo = $GLOBALS['db']->getRow("SELECT max(rate) as max_rate,min(rate) as min_rate FROM ".DB_PREFIX."deal ",false);
    	if((float)$rateinfo['max_rate'] > (int)max($rate_list)){
    		$rate_list[] = intval($rateinfo['max_rate']);
    	}
    	
    	if((float)$rateinfo['min_rate'] < (int)min($rate_list)){
    		$rate_list[0] = intval($rateinfo['min_rate']);
    	}
    	
    	$GLOBALS['tmpl']->assign("min_rate",$rate_list);
    	
    	rsort($rate_list);
    	$GLOBALS['tmpl']->assign("max_rate",$rate_list);
    	
    	
    	$autobid = $GLOBALS['db']->getRow("SELECT * FROM ".DB_PREFIX."user_autobid WHERE user_id = '".$GLOBALS['user_info']['id']."'");
    	
    	$autobid['deal_cates'] = explode(",",$autobid['deal_cates']);
    	
    	$GLOBALS['tmpl']->assign("autobid",$autobid);
    	
    	$level = $level_list['list'];
    	
    	$GLOBALS['tmpl']->assign("max_level",$level);
    	krsort($level);
    	$GLOBALS['tmpl']->assign("min_level",$level);
    	
    	$GLOBALS['tmpl']->assign("inc_file","inc/uc/uc_autobid.html");
		$GLOBALS['tmpl']->display("page/uc.html");
    }
    function save(){
    	filter_request($_POST);
    	
    	$data['user_id'] = intval($GLOBALS['user_info']['id']);
    	//每次投标金额
    	$data['fixed_amount'] = intval($_POST['fixedamount']);
    	if($data['fixed_amount']<50 || $data['fixed_amount']%50!=0){
    		showErr($GLOBALS['lang']['PLASE_ENTER_TRUE_FIXED_AMOUNT']);
    	}
    	//利息
    	$data['min_rate'] = floatval($_POST['min_rate']);
    	$data['max_rate'] = floatval($_POST['max_rate']);
    	if($data['min_rate'] < 0){
    		showErr($GLOBALS['lang']['PLASE_ENTER_TRUE_MIN_RATE']);
    	}
    	if($data['max_rate'] < 0){
    		showErr($GLOBALS['lang']['PLASE_ENTER_TRUE_MAX_RATE']);
    	}
    	if($data['min_rate'] > $data['max_rate']){
    		showErr($GLOBALS['lang']['MIN_RATE_NOMORE_MAX_RATE']);
    	}
    	//借款期限
    	$data['min_period'] = intval($_POST['min_period']);
    	$data['max_period'] = intval($_POST['max_period']);
    	if($data['min_period'] > $data['max_period']){
    		showErr($GLOBALS['lang']['MIN_PERIOD_NOMORE_MAX_PERIOD']);
    	}
    	//等级
    	$data['min_level'] = intval($_POST['min_level']);
    	$data['max_level'] = intval($_POST['max_level']);
    	if($data['min_level'] > $data['max_level']){
    		showErr($GLOBALS['lang']['MIN_LEVEL_NOMORE_MAX_LEVEL']);
    	}
    	//保留金额
    	$data['retain_amount'] = floatval($_POST['retain_amount']);
    	if($data['retain_amount'] < 0){
    		showErr($GLOBALS['lang']['PLASE_ENTER_TRUE_RETAIN_AMOUNT']);
    	}
    	
    	$data['deal_cates'] = implode(",",$_POST['deal_cate']);
    	
    	if($GLOBALS['db']->getOne("SELECT count(*) FROM ".DB_PREFIX."user_autobid WHERE user_id=".$data['user_id']) > 0){
    		//编辑
    		$GLOBALS['db']->autoExecute(DB_PREFIX."user_autobid",$data,"UPDATE","user_id=".$data['user_id']);
    	}
    	else{
    		//添加
    		$data['last_bid_time'] = TIME_UTC;
    		$GLOBALS['db']->autoExecute(DB_PREFIX."user_autobid",$data,"INSERT");
    	}
    	
    	showSuccess($GLOBALS['lang']['SAVE_AUTOBID_SUCCESS']);
    }
    
    function autoopen(){
    	if(intval($GLOBALS['user_info']['id']) == 0){
    		showErr($GLOBALS['lang']['PLEASE_LOGIN_FIRST'],1);
    	}
    	if(!isset($_REQUEST['is_effect'])){
    		showErr($GLOBALS['lang']['ERROR_TITLE'],1);
    	}
    	
    	if($GLOBALS['user_info']['money'] < 50 &&  intval($_REQUEST['is_effect']) == 1){
    		showErr("余额不足50，无法开启",1);
    	}
    	
    	$is_effect =  intval($_REQUEST['is_effect']);
    	$GLOBALS['db']->autoExecute(DB_PREFIX."user_autobid",array("is_effect"=>$is_effect,"last_bid_time"=>TIME_UTC),"UPDATE","user_id=".intval($GLOBALS['user_info']['id']));
    	showSuccess($GLOBALS['lang']['SUCCESS_TITLE'],1);
    }
}
?>