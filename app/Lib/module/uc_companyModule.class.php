<?php
// +----------------------------------------------------------------------
// | Fanwe 方维p2p借贷系统
// +----------------------------------------------------------------------
// | Copyright (c) 2011 http://www.fanwe.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 云淡风轻(88522820@qq.com)
// +----------------------------------------------------------------------

require APP_ROOT_PATH.'app/Lib/uc.php';

class uc_companyModule extends SiteBaseModule
{
	public function index()
	{
		$GLOBALS['tmpl']->assign("page_title",$GLOBALS['lang']['UC_COMPANY']);
		
		$company = $GLOBALS['db']->getRow("SELECT * FROM ".DB_PREFIX."user_company WHERE user_id=".intval($GLOBALS['user_info']['id']));
		
		$GLOBALS['tmpl']->assign("company",$company);
		
		$GLOBALS['tmpl']->assign("inc_file","inc/uc/uc_company.html");
		$GLOBALS['tmpl']->display("page/uc.html");
	}
	
	
	public function save()
	{
		$data =  $_POST;
		foreach($data as $k=>$v){
			if($k=="description"){
				$data[$k] =  replace_public(btrim($v));
			}else{
				$data[$k] =  strim($v);
			}
		}
		$data['user_id'] = intval($GLOBALS['user_info']['id']);
		$mode ="INSERT";
		$where ="";
		if($GLOBALS['db']->getOne("SELECT count(*) FROM ".DB_PREFIX."user_company WHERE user_id=".intval($GLOBALS['user_info']['id'])) > 0){
			$mode = "UPDATE";
			$where ="user_id=".intval($GLOBALS['user_info']['id']);
		}
		
		$GLOBALS['db']->autoExecute(DB_PREFIX."user_company",$data,$mode,$where);
		if($GLOBALS['db']->affected_rows() > 0){
			$user_info_re = array();
			$user_info_re['enterpriseName'] = $data['enterpriseName'];
			$user_info_re['bankLicense'] = $data['bankLicense'];
			$user_info_re['orgNo'] = $data['orgNo'];
			$user_info_re['businessLicense'] = $data['businessLicense'];
			$user_info_re['taxNo'] = $data['enterpriseName'];
			$GLOBALS['db']->autoExecute(DB_PREFIX."user",$user_info_re,"UPDATE","id=".intval($GLOBALS['user_info']['id']));
		}
		app_redirect(url("index","uc_company#index"));
	}
}
?>