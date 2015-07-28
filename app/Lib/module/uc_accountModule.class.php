<?php
// +----------------------------------------------------------------------
// | Fanwe 方维p2p借贷系统
// +----------------------------------------------------------------------
// | Copyright (c) 2011 http://www.fanwe.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 云淡风轻(88522820@qq.com)
// +----------------------------------------------------------------------

require APP_ROOT_PATH.'app/Lib/uc.php';

class uc_accountModule extends SiteBaseModule
{
	public function index()
	{

		$GLOBALS['tmpl']->assign("page_title",$GLOBALS['lang']['UC_ACCOUNT']);
		
		//扩展字段
		$field_list = load_auto_cache("user_field_list");
		
		foreach($field_list as $k=>$v)
		{
			$field_list[$k]['value'] = $GLOBALS['db']->getOne("select value from ".DB_PREFIX."user_extend where user_id=".$GLOBALS['user_info']['id']." and field_id=".$v['id']);
		}
		
		$GLOBALS['tmpl']->assign("field_list",$field_list);
		
		
		//地区列表
		$region_lv2_name ="";
		$region_lv2 = $GLOBALS['db']->getAll("select * from ".DB_PREFIX."region_conf where region_level = 2");  //二级地址
		foreach($region_lv2 as $k=>$v)
		{
			if($v['id'] == intval($GLOBALS['user_info']['province_id']))
			{
				$region_lv2[$k]['selected'] = 1;
				$region_lv2_name = $v['name'];
			}
			
			if($v['id'] == intval($GLOBALS['user_info']['n_province_id']))
			{
				$region_lv2[$k]['nselected'] = 1;
				$n_region_lv2_name = $v['name'];
			}
		}
		$GLOBALS['tmpl']->assign("region_lv2",$region_lv2);
		$GLOBALS['tmpl']->assign("region_lv2_name",$region_lv2_name);
		$GLOBALS['tmpl']->assign("n_region_lv2_name",$n_region_lv2_name);
			
		$region_lv3_name = "";
		$region_lv3 = $GLOBALS['db']->getAll("select * from ".DB_PREFIX."region_conf where pid = ".intval($GLOBALS['user_info']['province_id']));  //三级地址
		foreach($region_lv3 as $k=>$v)
		{
			if($v['id'] == intval($GLOBALS['user_info']['city_id']))
			{
				$region_lv3[$k]['selected'] = 1;
				$region_lv3_name = $v['name'];
				break;
			}
		}
		$GLOBALS['tmpl']->assign("region_lv3",$region_lv3);
		$GLOBALS['tmpl']->assign("region_lv3_name",$region_lv3_name);
			
		$n_region_lv3_name = "";
		$n_region_lv3 = $GLOBALS['db']->getAll("select * from ".DB_PREFIX."region_conf where pid = ".intval($GLOBALS['user_info']['n_province_id']));  //三级地址
		foreach($n_region_lv3 as $k=>$v)
		{
			if($v['id'] == intval($GLOBALS['user_info']['n_city_id']))
			{
				$n_region_lv3[$k]['selected'] = 1;
				$n_region_lv3_name = $v['name'];
				break;
			}
		}
		$GLOBALS['tmpl']->assign("n_region_lv3",$n_region_lv3);
		$GLOBALS['tmpl']->assign("n_region_lv3_name",$n_region_lv3_name);
			
			
		
		$GLOBALS['tmpl']->assign("inc_file","inc/uc/uc_account_index.html");
		$GLOBALS['tmpl']->display("page/uc.html");
	}
	
	public function work(){
		$GLOBALS['tmpl']->assign("page_title",$GLOBALS['lang']['UC_WORK_AUTH']);
		//地区列表
		$work =  $GLOBALS['db']->getRow("SELECT * FROM ".DB_PREFIX."user_work where user_id =".$GLOBALS['user_info']['id']);
		$GLOBALS['tmpl']->assign("work",$work);
		
		$region_lv2 = $GLOBALS['db']->getAll("select * from ".DB_PREFIX."region_conf where region_level = 2");  //二级地址
		foreach($region_lv2 as $k=>$v)
		{
			if($v['id'] == intval($work['province_id']))
			{
				$region_lv2[$k]['selected'] = 1;
				break;
			}
		}
		$GLOBALS['tmpl']->assign("region_lv2",$region_lv2);
		
		$region_lv3 = $GLOBALS['db']->getAll("select * from ".DB_PREFIX."region_conf where pid = ".intval($work['province_id']));  //三级地址
		foreach($region_lv3 as $k=>$v)
		{
			if($v['id'] == intval($work['city_id']))
			{
				$region_lv3[$k]['selected'] = 1;
				break;
			}
		}
		$GLOBALS['tmpl']->assign("region_lv3",$region_lv3);
		
		$GLOBALS['tmpl']->assign("inc_file","inc/uc/uc_work_index.html");
		$GLOBALS['tmpl']->display("page/uc.html");
	}
	
	public function mobile(){
		$is_ajax = intval($_REQUEST['is_ajax']);
		$form = strim($_REQUEST['from']);
		
		$GLOBALS['tmpl']->assign("is_ajax",$is_ajax);
		if($is_ajax==0){
			$GLOBALS['tmpl']->assign("page_title",$GLOBALS['lang']['UC_MOBILE']);
			$GLOBALS['tmpl']->assign("inc_file","inc/uc/uc_mobile_index.html");
			$GLOBALS['tmpl']->display("page/uc.html");
		}
		else{
			if($form == "debit")
			{
				$GLOBALS['tmpl']->display("debit/debit_uc_mobile_index.html");
			}
			else
			{
				$GLOBALS['tmpl']->display("inc/uc/uc_mobile_index.html");
			}
		}
	}
	
	public function email(){
		if($GLOBALS['user_info']['emailpassed']==1){
			exit();
		}
		
		$remail = get_site_email($GLOBALS['user_info']['id']);
		
		if($remail ==$GLOBALS['user_info']['email']){
			$email="";
		}
		else
			$email=$GLOBALS['user_info']['email'];
		
		$GLOBALS['tmpl']->assign("email",$email);
		$step = intval($_REQUEST['step']);
		$GLOBALS['tmpl']->assign("step",$step);
		$form = strim($_REQUEST["from"]);
		if($form == "debit")
		{
			$GLOBALS['tmpl']->display("debit/debit_uc_email_step.html");
		}
		else
		{
			$GLOBALS['tmpl']->display("inc/uc/uc_email_step.html");
		}
	}
	
	public function saveemail(){
		$oemail =  strim($_REQUEST['oemail']);
		$email =  strim($_REQUEST['email']);
		$code = $_REQUEST['code'];
		
		$remail = get_site_email($GLOBALS['user_info']['id']);
		
		if($GLOBALS['user_info']['email']!="" && $remail !=$GLOBALS['user_info']['email']){
			if($oemail!=$GLOBALS['user_info']['email']){
				$result['info'] = "旧邮箱确认失败";
				ajax_return($result);
			}
		}
		if($email!="" && !check_email($email)){
			$result['info'] = "新邮箱格式错误";
			ajax_return($result);
		}
		if($GLOBALS['user_info']['emailpassed']==1){
			$result['info'] = "该账户已绑定认证过邮箱，无法进行此操作";
			ajax_return($result);
		}
		if($code != $GLOBALS['user_info']['verify']){
			$result['info'] = "验证码错误";
			ajax_return($result);
		}
		
		if($email==""){
			$email = $oemail;
		}
	
		$GLOBALS['db']->query("update ".DB_PREFIX."user set email = '".$email."',verify = '',emailpassed = 1 where id = ".$GLOBALS['user_info']['id']);
		
		$result['status'] = 1;
		$result['info'] = "邮箱绑定成功";
		ajax_return($result);
	}
	
	public function save()
	{
		require_once APP_ROOT_PATH.'system/libs/user.php';
		foreach($_REQUEST as $k=>$v)
		{
			$_REQUEST[$k] = htmlspecialchars(addslashes(trim($v)));
		}
		
		if ($_REQUEST['sta'] == 1){
			if(md5(strim($_REQUEST['old_password']).$GLOBALS['user_info']['code'])!=$GLOBALS['user_info']['user_pwd'])
			{
				showErr("旧密码错误！",intval($_REQUEST['is_ajax']));
			}
		}
		
		$_REQUEST['id'] = intval($GLOBALS['user_info']['id']);
		
		if($GLOBALS['user_info']['user_name']!="")
			$_REQUEST['user_name'] =  $_REQUEST['old_user_name'] = $GLOBALS['user_info']['user_name'];
		if($GLOBALS['user_info']['email']!="")
			$_REQUEST['email'] = $_REQUEST['old_email'] = $GLOBALS['user_info']['email'];
		if($GLOBALS['user_info']['mobile']!="")
			$_REQUEST['mobile'] = $_REQUEST['old_mobile'] = $GLOBALS['user_info']['mobile'];
			
		$_REQUEST['old_password'] = strim($_REQUEST['old_password']);
		
		$res = save_user($_REQUEST,'UPDATE');
		if($res['status'] == 1)
		{
			$s_user_info = es_session::get("user_info");
			$user_info = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."user where id = '".intval($s_user_info['id'])."'");
			es_session::set("user_info",$user_info);
			if(intval($_REQUEST['is_ajax'])==1)
				showSuccess($GLOBALS['lang']['SUCCESS_TITLE'],1);
			else{
				app_redirect(url("index","uc_account#index"));
			}		
		}
		else
		{
			$error = $res['data'];		
			if(!$error['field_show_name'])
			{
					$error['field_show_name'] = $GLOBALS['lang']['USER_TITLE_'.strtoupper($error['field_name'])];
			}
			if($error['error']==EMPTY_ERROR)
			{
				$error_msg = sprintf($GLOBALS['lang']['EMPTY_ERROR_TIP'],$error['field_show_name']);
			}
			if($error['error']==FORMAT_ERROR)
			{
				$error_msg = sprintf($GLOBALS['lang']['FORMAT_ERROR_TIP'],$error['field_show_name']);
			}
			if($error['error']==EXIST_ERROR)
			{
				$error_msg = sprintf($GLOBALS['lang']['EXIST_ERROR_TIP'],$error['field_show_name']);
			}
			showErr($error_msg,intval($_REQUEST['is_ajax']));
		}
	}
	
	public function savework(){
		foreach($_REQUEST as $k=>$v)
		{
			$_REQUEST[$k] = htmlspecialchars(addslashes(trim($v)));
		}
		if(isset($_REQUEST['office']))
			$data['office'] = trim($_REQUEST['office']);
		if(isset($_REQUEST['jobtype']))
			$data['jobtype'] = trim($_REQUEST['jobtype']);
		if(isset($_REQUEST['province_id']))
			$data['province_id'] = intval($_REQUEST['province_id']);
		if(isset($_REQUEST['city_id']))
			$data['city_id'] = intval($_REQUEST['city_id']);
		if(isset($_REQUEST['officetype']))
			$data['officetype'] = trim($_REQUEST['officetype']);
		if(isset($_REQUEST['officedomain']))
			$data['officedomain'] = trim($_REQUEST['officedomain']);
		if(isset($_REQUEST['officecale']))
			$data['officecale'] = trim($_REQUEST['officecale']);
		if(isset($_REQUEST['position']))
			$data['position'] = trim($_REQUEST['position']);
		if(isset($_REQUEST['salary']))
			$data['salary'] = trim($_REQUEST['salary']);
		if(isset($_REQUEST['workyears']))
			$data['workyears'] = trim($_REQUEST['workyears']);
		if(isset($_REQUEST['workphone']))
			$data['workphone'] = trim($_REQUEST['workphone']);
		if(isset($_REQUEST['workemail']))
			$data['workemail'] = trim($_REQUEST['workemail']);
		if(isset($_REQUEST['officeaddress']))
			$data['officeaddress'] = trim($_REQUEST['officeaddress']);
		
		if(isset($_REQUEST['urgentcontact']))
			$data['urgentcontact'] = trim($_REQUEST['urgentcontact']);
		if(isset($_REQUEST['urgentrelation']))
			$data['urgentrelation'] = trim($_REQUEST['urgentrelation']);
		if(isset($_REQUEST['urgentmobile']))
			$data['urgentmobile'] = trim($_REQUEST['urgentmobile']);
		if(isset($_REQUEST['urgentcontact2']))
			$data['urgentcontact2'] = trim($_REQUEST['urgentcontact2']);
		if(isset($_REQUEST['urgentrelation2']))
			$data['urgentrelation2'] = trim($_REQUEST['urgentrelation2']);
		if(isset($_REQUEST['urgentmobile2']))
			$data['urgentmobile2'] = trim($_REQUEST['urgentmobile2']);
			
		$data['user_id'] = intval($GLOBALS['user_info']['id']);
			
		if($GLOBALS['db']->getOne("SELECT count(*) FROM ".DB_PREFIX."user_work WHERE user_id=".$data['user_id'])==0){
			//添加
			$GLOBALS['db']->autoExecute(DB_PREFIX."user_work",$data,"INSERT");
		}
		else{
			//编辑
			$GLOBALS['db']->autoExecute(DB_PREFIX."user_work",$data,"UPDATE","user_id=".$data['user_id']);
		}
		
		showSuccess($GLOBALS['lang']['SAVE_USER_SUCCESS'],intval($_REQUEST['is_ajax']));
	}
	
	public function security(){
		
		if($GLOBALS['user_info']['idcardpassed'] == 0)
		{
			$idcard_credit = $GLOBALS['db']->getRow("SELECT * FROM ".DB_PREFIX."user_credit_file WHERE type='credit_identificationscanning' and user_id=".$GLOBALS['user_info']['id']." ");
	    	
	    	if($idcard_credit){
	    		$file_list = array();
	    		if($idcard_credit['file'])
	    			$file_list = unserialize($idcard_credit['file']);
	    		
	    		if(is_array($file_list)) 
	    			$idcard_credit['file_list']= $file_list;
	    		
	    	}
	    	
	    	$GLOBALS['tmpl']->assign('idcard_credit',$idcard_credit);
		}
		
		$user_type = $GLOBALS['db']->getOne("SELECT user_type FROM ".DB_PREFIX."user WHERE id=".$GLOBALS['user_info']['id']);
		$GLOBALS['tmpl']->assign('user_type',$user_type);
		
		$GLOBALS['tmpl']->assign("inc_file","inc/uc/uc_security.html");
		$GLOBALS['tmpl']->display("page/uc.html");
		
		
	}
	
	public function paypassword(){
		$is_ajax = intval($_REQUEST['is_ajax']);
		$from = strim($_REQUEST["from"]);
		
		if(intval($GLOBALS['user_info']['mobilepassed']) == 0){
			showErr('手机号码未绑定',$is_ajax);
		}
		$GLOBALS['tmpl']->assign("is_ajax",$is_ajax);
		
		if($is_ajax==1){
			if($from=="debit")
			{
				showSuccess($GLOBALS['tmpl']->fetch("debit/debit_uc_paypassword_index.html"),$is_ajax);
			}
			else
			{
				showSuccess($GLOBALS['tmpl']->fetch("inc/uc/uc_paypassword_index.html"),$is_ajax);
			}
		}
		else
		{
			$GLOBALS['tmpl']->assign("page_title",$GLOBALS['lang']['UC_PAYPASSWORD']);
			$GLOBALS['tmpl']->assign("inc_file","inc/uc/uc_paypassword_index.html");
			$GLOBALS['tmpl']->display("page/uc.html");
		}
	}
	
	
}
?>