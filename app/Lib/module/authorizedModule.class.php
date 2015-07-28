<?php
// +----------------------------------------------------------------------
// | Fanwe 方维p2p借贷系统
// +----------------------------------------------------------------------
// | Copyright (c) 2011 http://www.fanwe.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 云淡风轻(88522820@qq.com)
// +----------------------------------------------------------------------

class authorizedModule extends SiteBaseModule
{
	static private function checkLogin(){
		$info  = es_session::get("authorized_info");
		
		$authorized_info = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."user where id='".$info["id"]."'");
		
		if(!$authorized_info){
			app_redirect(url("index","authorized#login"));
			die();
		}
		
		return $authorized_info;
	}
	public function login()
	{
		$login_info = es_session::get("authorized_info");
		if($login_info)
		{
			app_redirect(url("index","authorized#account"));		
		}
				
		$GLOBALS['tmpl']->caching = true;
		$cache_id  = md5(MODULE_NAME.ACTION_NAME.$GLOBALS['deal_city']['id']);		
		if (!$GLOBALS['tmpl']->is_cached('authorized/authorized_login.html', $cache_id))	
		{
			$GLOBALS['tmpl']->assign("page_title","授权担保机构登陆");
			$GLOBALS['tmpl']->assign("CREATE_TIP",$GLOBALS['lang']['REGISTER']);
			 
		}
		$GLOBALS['tmpl']->display("authorized/authorized_login.html",$cache_id);
	}
	public function dologin()
	{
		if(!$_POST)
		{
			 app_redirect("404.html");
			 exit();
		}
		if(!check_hash_key()){
			showErr("非法请求!",$ajax);
		}
		foreach($_POST as $k=>$v)
		{
			$_POST[$k] = htmlspecialchars(addslashes($v));
		}
		$ajax = intval($_REQUEST['ajax']);
		
		
		$_POST['user_pwd'] = strim(FW_DESPWD($_POST['user_pwd']));
		
		require_once APP_ROOT_PATH."system/libs/user.php";
		
		if(check_ipop_limit(CLIENT_IP,"user_dologin",intval(app_conf("SUBMIT_DELAY")))){
			$result = do_login_user($_POST['email'],$_POST['user_pwd']);
			
		}
		else
			showErr($GLOBALS['lang']['SUBMIT_TOO_FAST'],$ajax,url("shop","authorized#login"));
			
		if($result['status'])
		{	
			$s_user_info = es_session::get("authorized_info");

			$jump_url = url("index","authorized#account");
			$s_user_info = es_session::get("authorized_info");
			
			if($ajax==1)
			{
				$return['status'] = 1;
				$return['info'] = $GLOBALS['lang']['LOGIN_SUCCESS'];
				$return['data'] = $result['msg'];
				$return['jump'] = $jump_url;
				ajax_return($return);
			}
			else
			{
				$GLOBALS['tmpl']->assign('integrate_result',$result['msg']);					
				showSuccess($GLOBALS['lang']['LOGIN_SUCCESS'],$ajax,$jump_url);
			}
			
		}
		else
		{
			if($result['data'] == ACCOUNT_NO_EXIST_ERROR)
			{
				$err = $GLOBALS['lang']['USER_NOT_EXIST'];
			}
			if($result['data'] == ACCOUNT_PASSWORD_ERROR)
			{
				$err = $GLOBALS['lang']['PASSWORD_ERROR'];
			}
			if($result['data'] == ACCOUNT_NO_VERIFY_ERROR)
			{
				$err = $GLOBALS['lang']['USER_NOT_VERIFY'];
				if(app_conf("MAIL_ON")==1&&$ajax==0)
				{				
					$GLOBALS['tmpl']->assign("page_title",$err);
					$GLOBALS['tmpl']->assign("user_info",$result['user']);
					$GLOBALS['tmpl']->display("verify_user.html");
					exit;
				}
				
			}
			showErr($err,$ajax);
		}
	}
	
	
	public function loginout()
	{
		require_once APP_ROOT_PATH."system/libs/user.php";
		$result = loginout_authorized();
		if($result['status'])
		{
			$s_user_info = es_session::get("authorized_info");
			es_cookie::delete("user_name");
			es_cookie::delete("user_pwd");
			$GLOBALS['tmpl']->assign('integrate_result',$result['msg']);
			$before_loginout = $_SERVER['HTTP_REFERER']?$_SERVER['HTTP_REFERER']:url("index");
			if(trim(app_conf("INTEGRATE_CODE"))=='')
			{
				app_redirect($before_loginout);
			}
			else
			showSuccess($GLOBALS['lang']['LOGINOUT_SUCCESS'],0,$before_loginout);
		}
		else
		{
			app_redirect(url("index"));		
		}
	}
	
	public function getpassword()
	{
		$GLOBALS['tmpl']->caching = true;
		$cache_id  = md5(MODULE_NAME.ACTION_NAME.$GLOBALS['deal_city']['id']);		
		if (!$GLOBALS['tmpl']->is_cached('authorized/authorized_get_password.html', $cache_id))	
		{
			 
			$GLOBALS['tmpl']->assign("page_title",$GLOBALS['lang']['GET_PASSWORD_BACK']);
		}
		$GLOBALS['tmpl']->display("authorized/authorized_get_password.html",$cache_id);
	}
	
	public function send_password()
	{
		$email = addslashes(trim($_REQUEST['email']));
		$user_pwd = strim($_REQUEST['pwd_m']);
		$sms_code =strim($_REQUEST['sms_codes']);
		
		if(!check_email($email))
		{
			showErr($GLOBALS['lang']['MAIL_FORMAT_ERROR']); //没输入邮件
		}

		if($GLOBALS['db']->getAll("select count(*) from ".DB_PREFIX."user where email ='".$email."'") == 0)
		{
			showErr($GLOBALS['lang']['NO_THIS_MAIL']);  //无此邮箱用户
		}
		if($sms_code==""){
			showErr("请输入邮箱验证码",1);
		}
		
		if($user_pwd==""){
			showErr("请输入密码",1);
		}
		
		$yanzheng = $GLOBALS['db']->getOne("SELECT count(*) FROM ".DB_PREFIX."email_verify_code WHERE email='".$email."' AND verify_code='".$sms_code."'");

		if(!$yanzheng){
			showErr("邮箱验证码出错",1);
		}
		
		if($user_info = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."user where email='".$email."'"))
		{
			$result = 1;  
			if($result>0)
			{
				$user_info_m['password'] = md5($user_pwd);
				$GLOBALS['db']->autoExecute(DB_PREFIX."user",$user_info_m,"UPDATE","id=".$user_info['id']);
			}
				
			showSuccess($GLOBALS['lang']['MOBILE_SUCCESS'],1);//密码修改成功
				
		}
		else{
			showErr("邮箱账户不存在",1);  //没有该手机账户
		}
		
	}
	public function email(){
		$GLOBALS['authorized_info'] = es_session::get("authorized_info");
		if($GLOBALS['authorized_info']['emailpassed']==1){
			exit();
		}
		
		$remail = get_site_email($GLOBALS['authorized_info']['id']);
		
		if($remail ==$GLOBALS['authorized_info']['email']){
			$email="";
		}
		else
			$email=$GLOBALS['authorized_info']['email'];
		
		$GLOBALS['tmpl']->assign("email",$email);
		$step = intval($_REQUEST['step']);
		$GLOBALS['tmpl']->assign("step",$step);
		$GLOBALS['tmpl']->assign("authorized_info",$GLOBALS['authorized_info']);
		$GLOBALS['tmpl']->display("authorized/authorized_email_step.html");
	}
	
	public function saveemail(){
		if(!check_hash_key()){
			showErr("非法请求!",$ajax);
		}
		$authorized_info = es_session::get("authorized_info");
		$authorized_info = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."user where id =".$authorized_info["id"]);
		$oemail =  strim($_REQUEST['oemail']);
		$email =  strim($_REQUEST['email']);
		$code = $_REQUEST['code'];
		
		$remail = get_site_email($GLOBALS['authorized_info']['id']);
		
		if($GLOBALS['authorized_info']['email']!="" && $remail !=$authorized_info['email']){
			if($oemail!=$authorized_info['email']){
				$result['info'] = "旧邮箱确认失败";
				ajax_return($result);
			}
		}
		if($email!="" && !check_email($email)){
			$result['info'] = "新邮箱格式错误";
			ajax_return($result);
		}
		if($authorized_info['emailpassed']==1){
			$result['info'] = "该账户已绑定认证过邮箱，无法进行此操作";
			ajax_return($result);
		}
		if($code != $authorized_info['verify']){
			$result['info'] = "验证码错误";
			ajax_return($result);
		}
		
		if($email==""){
			$email = $oemail;
		}
	
		$GLOBALS['db']->query("update ".DB_PREFIX."user set email = '".$email."',verify = '',emailpassed = 1 where id = ".$GLOBALS['authorized_info']['id']);
		
		$result['status'] = 1;
		$result['info'] = "邮箱绑定成功";
		ajax_return($result);
	}
	
	public function phone_send_password()
	{	
		$mobile = strim($_REQUEST['mobile']);
		$user_pwd = strim($_REQUEST['pwd_m']);
		$sms_code=strim($_POST['sms_code']);
		
		if(!$mobile)
		{
			showErr($GLOBALS['lang']['MOBILE_FORMAT_ERROR']); //手机格式错误
		}
		
		if($sms_code==""){
			showErr("请输入手机验证码",1);
		}
		
		if($user_pwd==""){
			showErr("请输入密码",1);
		}
		
		
		//判断验证码是否正确
		if($GLOBALS['db']->getOne("SELECT count(*) FROM ".DB_PREFIX."mobile_verify_code WHERE mobile='".$mobile."' AND verify_code='".$sms_code."'")==0){
			showErr("手机验证码出错",1);
		}
			
	
		if($user_info = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."user where mobile ='".$mobile."'"))
		{
			$result = 1;  
			if($result>0)
			{
				$user_info_m['password'] = md5($user_pwd);
				$GLOBALS['db']->autoExecute(DB_PREFIX."user",$user_info_m,"UPDATE","id=".$user_info['id']);
			}
			
			showSuccess($GLOBALS['lang']['MOBILE_SUCCESS'],1);//密码修改成功
			
		}
		else{
			showErr($GLOBALS['lang']['NO_THIS_MOBILE'],1);  //没有该手机账户
		}
		
		
		
	}
	
	public function modify_password()
	{
		 
		$id = intval($_REQUEST['id']);
		$user_info  = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."user where id = ".$id);
		if(!$user_info)
		{
			showErr($GLOBALS['lang']['NO_THIS_USER']);
		}
		$verify = $_REQUEST['code'];
		if($user_info['code'] == $verify&&$user_info['code']!='')
		{
			//成功	
			$GLOBALS['tmpl']->assign("page_title",$GLOBALS['lang']['SET_NEW_PASSWORD']);				
			$GLOBALS['tmpl']->assign("user_info",$user_info);
			$GLOBALS['tmpl']->display("authorized/authorized_modify_password.html");
		}
		else
		{
			showErr($GLOBALS['lang']['VERIFY_FAILED'],0,APP_ROOT."/");
		}	
	}
	
	public function do_modify_password()
	{
		$id = intval($_REQUEST['id']);
		$user_info  = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."user where id = ".$id);
		if(!$user_info)
		{
			showErr($GLOBALS['lang']['NO_THIS_USER']);
		}
		$verify = $_REQUEST['code'];
		if($user_info['code'] == $verify&&$user_info['code']!='')
		{
			if(trim($_REQUEST['user_pwd'])!=trim($_REQUEST['user_pwd_confirm']) || trim($_REQUEST['user_pwd'])=="")
			{
				showErr($GLOBALS['lang']['PASSWORD_VERIFY_FAILED']);
			}
			else
			{			
				$password = addslashes(trim($_REQUEST['user_pwd']));
				$user_info['user_pwd'] = $password;
				$password = md5($password.$user_info['code']);
				$result = 1;  //初始为1
				//载入会员整合
				/*$integrate_code = trim(app_conf("INTEGRATE_CODE"));
				if($integrate_code!='')
				{
					$integrate_file = APP_ROOT_PATH."system/integrate/".$integrate_code."_integrate.php";
					if(file_exists($integrate_file))
					{
						require_once $integrate_file;
						$integrate_class = $integrate_code."_integrate";
						$integrate_obj = new $integrate_class;
					}	
				}
				*/
				if($integrate_obj)
				{
					$result = $integrate_obj->edit_user($user_info,$user_info['user_pwd']);				
				}
				if($result>0)
				{
					$GLOBALS['db']->query("update ".DB_PREFIX."user set user_pwd = '".$password."',password_verify='' where id = ".$user_info['id'] );
					showSuccess($GLOBALS['lang']['NEW_PWD_SET_SUCCESS'],0,APP_ROOT."/");
				}
				else
				{
					showErr($GLOBALS['lang']['NEW_PWD_SET_FAILED']);
				}
			}
			//成功	
			$GLOBALS['tmpl']->assign("page_title",$GLOBALS['lang']['SET_NEW_PASSWORD']);				
			$GLOBALS['tmpl']->assign("user_info",$user_info);
			$GLOBALS['tmpl']->display("user_modify_password.html");
		}
		else
		{
			showErr($GLOBALS['lang']['VERIFY_FAILED'],0,APP_ROOT."/");
		}	
	}
	
	
	/**
	 * 账户资料
	 */
	function account(){
		$authorized_info = $this->checkLogin();
		$authorized_info = $GLOBALS['db']->getRow("SELECT * FROM ".DB_PREFIX."user WHERE user_type = 3 and id=".$authorized_info['id']);
		$GLOBALS['tmpl']->assign('authorized_info',$authorized_info);
		$GLOBALS['tmpl']->assign("inc_file","authorized/authorized_account.html");
		$GLOBALS['tmpl']->display("authorized/a_m.html");
	}
	
	/**
	 * 保存账户资料
	 */
	function accountsave(){
		$authorized_info = $this->checkLogin();
		
		$data['brief'] = strim($_REQUEST["brief"]);
		$data['address'] = strim($_REQUEST["address"]);
		$data['header'] = replace_public(btrim($_REQUEST["header"]));
		$data['company_brief'] = strim($_REQUEST["company_brief"]);
		$data['history'] = replace_public(btrim($_REQUEST["history"]));
		$data['content'] = replace_public(btrim($_REQUEST["content"]));
		
		$GLOBALS['db']->autoExecute(DB_PREFIX."user",$data,"UPDATE","id=".$authorized_info['id']);
		
		$agency_info = $GLOBALS['db']->getRow("SELECT * FROM ".DB_PREFIX."user  where id=".$authorized_info['id']);
		
		es_session::set("authorized_info",$agency_info);
		
	 	showSuccess("操作成功",url("index","authorized#account"));
	}
	
	
	 public function security(){
		$authorized_info = $this->checkLogin();

		$GLOBALS['tmpl']->assign('authorized_info',$authorized_info);

		$GLOBALS['tmpl']->assign("inc_file","authorized/authorized_security.html");
		$GLOBALS['tmpl']->display("authorized/a_m.html");
		
	}
	
	public function save_idsno()
	{
		$authorized_info = $this->checkLogin();
		$id= $authorized_info['id'];
		
		$acct_type = intval($_REQUEST['acct_type']);

		$real_name = strim($_REQUEST['real_name']);
		$idno = strim($_REQUEST['idno']);

		$ajax = intval($_REQUEST['ajax']);

		if(!$id)
		{
			$data['status'] = 0;
			$data['info'] = "该用户尚未登陆";
			ajax_return($data);
		}
		
		if(!$real_name&&$acct_type==1)
		{
			$data['status'] = 0;
			$data['info'] = "请输入真实姓名";
			ajax_return($data);
		}
		
		if(strlen($idno) != 15 && strlen($idno) !=18 ){
			$data['status'] = 0;
			$data['info'] = "身份证格式错误";
			ajax_return($data);
		}
		
		if($idno=="" ){
			$data['status'] = 0;
			$data['info'] = "请输入身份证号";
			ajax_return($data);
		}	
	
	
		//判断该实名是否存在
		if($GLOBALS['db']->getOne("SELECT count(*) FROM ".DB_PREFIX."user where idno = '.$idno.' and id<> $id ") > 0 ){
			$data['status'] = 0;
			$data['info'] = "该实名已被其他用户认证，非本人请联系客服";
			ajax_return($data);
		}
		
		
		if($GLOBALS['db']->getRow("select * from ".DB_PREFIX."user where id =".$id))
		{	
			$user_info_re = array();

			$user_info_re['real_name'] = $real_name;
			$user_info_re['idno'] = $idno;

			$GLOBALS['db']->autoExecute(DB_PREFIX."user",$user_info_re,"UPDATE","id=".$id);
			
			$return['status'] = 1;

			$return['info'] = "提交成功";

			ajax_return($return);
		}
		else{
			$data['status'] = 0;
			$data['info'] = "该用户尚未注册";
			ajax_return($data);
		}
		
	}
	
	public function paypassword(){
		$authorized_info = $this->checkLogin();
		$is_ajax = intval($_REQUEST['is_ajax']);
		if(intval($authorized_info['mobilepassed']) == 0){
			showErr('手机号码未绑定',$is_ajax);
		}
		$GLOBALS['tmpl']->assign("is_ajax",$is_ajax);
		$GLOBALS['tmpl']->assign("authorized_info",$authorized_info);
		if($is_ajax==1){
			showSuccess($GLOBALS['tmpl']->fetch("authorized/authorized_paypassword_index.html"),$is_ajax);
		}
		else
		{
			$GLOBALS['tmpl']->assign("page_title",$GLOBALS['lang']['UC_PAYPASSWORD']);
			$GLOBALS['tmpl']->assign("inc_file","authorized/authorized_paypassword_index.html");
			$GLOBALS['tmpl']->display("authorized/a_m.html");
		}
	}
	
	
	public function customer()
	{
		$authorized_info = $this->checkLogin();
		$page = intval($_REQUEST['p']);
		if($page==0)
			$page = 1;
		$limit = (($page-1)*app_conf("PAGE_SIZE")).",".app_conf("PAGE_SIZE");
		if($limit)
		{
			$limit = " limit ".$limit;
		}
		
		$result = $GLOBALS['db']->getAll("select * from ".DB_PREFIX."user where pid = ".$authorized_info['id']." order by id desc".$limit);
		$count  = $GLOBALS['db']->getOne(" select count(*) from ".DB_PREFIX."user where pid = ".$authorized_info['id']);
		
		foreach($result as $k=>$v)
		{
			$result[$k]["invest_money"] = $GLOBALS['db']->getOne("select sum(dlr.true_manage_interest_money_rebate) from ".DB_PREFIX."deal_load_repay dlr left join ".DB_PREFIX."user u on dlr.user_id = u.id left join ".DB_PREFIX."user t_u on dlr.t_user_id = t_u.id where ((dlr.user_id = ".$v["id"]." and dlr.t_user_id = 0) or dlr.t_user_id = ".$v["id"].") and ((u.pid = ".$authorized_info['id']." and dlr.t_user_id = 0) or t_u.pid = ".$authorized_info['id'].")");
			$result[$k]["borrow_money"] = $GLOBALS['db']->getOne("select sum(dr.true_manage_money_rebate) from ".DB_PREFIX."deal_repay dr left join ".DB_PREFIX."user u on dr.user_id = u.id where dr.user_id = ".$v['id']." and u.pid = ".$authorized_info['id']);
			$result[$k]["totalmoney"] = floatval($result[$k]["invest_money"]) + floatval($result[$k]["borrow_money"]);
		}
		$GLOBALS['tmpl']->assign("list",$result);
		
		//require APP_ROOT_PATH."app/Lib/deal_func.php";
		require APP_ROOT_PATH."app/Lib/page.php";
		
		$page = new Page($count,app_conf("PAGE_SIZE"));    //初始化分页对象 		
		$p  =  $page->show();
		
		$GLOBALS['tmpl']->assign('pages',$p);
		$GLOBALS['tmpl']->assign("inc_file","authorized/authorized_customer.html");
		$GLOBALS['tmpl']->display("authorized/a_m.html");
		
	}
	public function customer_not()
	{
		$authorized_info = $this->checkLogin();
		$page = intval($_REQUEST['p']);
		if($page==0)
			$page = 1;
		$limit = (($page-1)*app_conf("PAGE_SIZE")).",".app_conf("PAGE_SIZE");
		if($limit)
		{
			$limit = " limit ".$limit;
		}
		
		$result = $GLOBALS['db']->getAll("select * from ".DB_PREFIX."user where referer_memo = ".$authorized_info['id']." and pid = 0 order by id desc".$limit);
		$count  = $GLOBALS['db']->getOne(" select count(*) from ".DB_PREFIX."user where referer_memo = ".$authorized_info['id']." and pid = 0 ");
		
		//foreach($result as $k=>$v)
		//{
			//$result[$k]["totalmoney"] = $GLOBALS['db']->getOne("select sum(money) from ".DB_PREFIX."user_money_log where user_id = ".$authorized_info['id']." and type in (28,29) and from_user_id = ".$v["id"]);
		//}
		$GLOBALS['tmpl']->assign("list",$result);
		
		//require APP_ROOT_PATH."app/Lib/deal_func.php";
		require APP_ROOT_PATH."app/Lib/page.php";
		
		$page = new Page($count,app_conf("PAGE_SIZE"));    //初始化分页对象 		
		$p  =  $page->show();
		
		$GLOBALS['tmpl']->assign('pages',$p);
		$GLOBALS['tmpl']->assign("inc_file","authorized/authorized_customer_not.html");
		$GLOBALS['tmpl']->display("authorized/a_m.html");
		
	}
	public function invest()
	{
		$authorized_info = $this->checkLogin();
		
		$page = intval($_REQUEST['p']);
		if($page==0)
			$page = 1;
		$limit = (($page-1)*app_conf("PAGE_SIZE")).",".app_conf("PAGE_SIZE");
		if($limit)
		{
			$limit = " limit ".$limit;
		}
		
		$user_id = intval($_REQUEST['u_id']);
		
		if($user_id)
		{
			$sql = "select f_u.id as f_u_id,t_u.id as t_u_id,dlt.t_user_id,d.*,f_u.user_name as f_user_name,t_u.user_name as t_user_name,dl.money as u_load_money,u.level_id,u.province_id,u.city_id,dl.id as load_id from ".DB_PREFIX."deal d left join ".DB_PREFIX."deal_load as dl on d.id = dl.deal_id LEFT JOIN ".DB_PREFIX."deal_load_transfer dlt on dlt.load_id = dl.id LEFT JOIN ".DB_PREFIX."user u ON u.id=d.user_id 
LEFT JOIN ".DB_PREFIX."user t_u ON t_u.id = t_user_id LEFT JOIN ".DB_PREFIX."user f_u ON f_u.id = dl.user_id where d.deal_status in(4,5) and (f_u.pid='".$authorized_info["id"]."' or t_u.pid = '".$authorized_info["id"]."') and ((f_u.id = '".$user_id."' and (t_u.id is null or t_u.id = 0)) or t_u.id = '".$user_id."') group by dl.id order by dl.create_time desc ".$limit;
			
			$sql_count = "select count(DISTINCT dl.id) from ".DB_PREFIX."deal d left join ".DB_PREFIX."deal_load as dl on d.id = dl.deal_id LEFT JOIN ".DB_PREFIX."deal_load_transfer dlt on dlt.load_id = dl.id LEFT JOIN ".DB_PREFIX."user u ON u.id=d.user_id 
LEFT JOIN ".DB_PREFIX."user t_u ON t_u.id = t_user_id LEFT JOIN ".DB_PREFIX."user f_u ON f_u.id = dl.user_id where d.is_delete=0 and d.publish_wait = 0 AND d.deal_status in(4,5) and (f_u.pid='".$authorized_info["id"]."' or t_u.pid = '".$authorized_info["id"]."') and ((f_u.id = '".$user_id."' and (t_u.id is null or t_u.id = 0)) or t_u.id = '".$user_id."')";
			
		}
		else
		{
			$sql = "select t_u.id as t_u_id,f_u.id as f_u_id,dlt.t_user_id,d.*,f_u.user_name as f_user_name,t_u.user_name as t_user_name,dl.money as u_load_money,u.level_id,u.province_id,u.city_id,dl.id as load_id from ".DB_PREFIX."deal d left join ".DB_PREFIX."deal_load as dl on d.id = dl.deal_id LEFT JOIN ".DB_PREFIX."deal_load_transfer dlt on dlt.load_id = dl.id LEFT JOIN ".DB_PREFIX."user u ON u.id=d.user_id 
LEFT JOIN ".DB_PREFIX."user t_u ON t_u.id = t_user_id LEFT JOIN ".DB_PREFIX."user f_u ON f_u.id = dl.user_id where d.deal_status in(4,5) and (f_u.pid='".$authorized_info["id"]."' or t_u.pid = '".$authorized_info["id"]."') group by dl.id order by dl.create_time desc ".$limit;
			
			$sql_count = "select count(DISTINCT dl.id) from ".DB_PREFIX."deal d left join ".DB_PREFIX."deal_load as dl on d.id = dl.deal_id LEFT JOIN ".DB_PREFIX."deal_load_transfer dlt on dlt.load_id = dl.id LEFT JOIN ".DB_PREFIX."user u ON u.id=d.user_id 
LEFT JOIN ".DB_PREFIX."user t_u ON t_u.id = t_user_id LEFT JOIN ".DB_PREFIX."user f_u ON f_u.id = dl.user_id where d.is_delete=0 and d.publish_wait = 0 AND d.deal_status in(4,5) and (f_u.pid='".$authorized_info["id"]."' or t_u.pid = '".$authorized_info["id"]."')";
		}

		$count = $GLOBALS['db']->getOne($sql_count);

		$list = array();
		if($count >0){
			$list = $GLOBALS['db']->getAll($sql);
			$load_ids = array();
			foreach($list as $k=>$v){
				$list[$k]["u_id"] = $v["t_u_id"]?$v["t_u_id"]:$v["f_u_id"];
				$list[$k]['borrow_amount_format'] = format_price($v['borrow_amount']/10000)."万";//format_price($deal['borrow_amount']);
				$list[$k]['rate_foramt_w'] = number_format($v['rate'],2)."%";
				$list[$k]['user_name'] = $v["t_user_name"] != "" ? $v["t_user_name"] : $v["f_user_name"];
				//$list[$k]['borrow_amount_format'] = format_price($v['borrow_amount']);					
				$list[$k]['rate_foramt'] = number_format($v['rate'],2);
				
				//本息还款金额
				$list[$k]['month_repay_money'] = pl_it_formula($v['borrow_amount'],$v['rate']/12/100,$v['repay_time']);
				$list[$k]['month_repay_money_format'] =  format_price($list[$k]['month_repay_money']);
					
				switch($v['deal_status']){
					//case "1":$list[$k]['status_format'] = ; break;
					//case "2":$list[$k]['status_format'] = ; break;
					//case "3":$list[$k]['status_format'] = ""; break;
					case "4":$list[$k]['status_format'] = "还款中"; break;
					case "5":$list[$k]['status_format'] = "已还清"; break;
					default:break;
				}
				
				/*if($v['deal_status'] == 1){
					//还需多少钱
					$list[$k]['need_money'] = format_price($v['borrow_amount'] - $v['load_money']);
		
					//百分比
					$list[$k]['progress_point'] = $v['load_money']/$v['borrow_amount']*100;
		
				}
				/*elseif($v['deal_status'] == 2 || $v['deal_status'] == 5)
				{
					$list[$k]['progress_point'] = 100;
				}
				elseif($v['deal_status'] == 4){
					//百分比
					$list[$k]['remain_repay_money'] = $list[$k]['month_repay_money'] * $v['repay_time'];
					//还有多少需要还
					$list[$k]['need_remain_repay_money'] = $list[$k]['remain_repay_money'] - $v['repay_money'];
					//还款进度条
					$list[$k]['progress_point'] =  round($v['repay_money']/$list[$k]['remain_repay_money']*100,2);
				}*/
					
				$user_location = $GLOBALS['db']->getOne("select name from ".DB_PREFIX."region_conf where id = ".intval($v['city_id']));
				if($user_location=='')
					$user_location = $GLOBALS['db']->getOne("select name from ".DB_PREFIX."region_conf where id = ".intval($v['province_id']));
		
				$list[$k]['user_location'] = $user_location;
				$list[$k]['point_level'] = $GLOBALS['db']->getOne("select name from ".DB_PREFIX."user_level where id = ".intval($v['level_id']));
				
				
				//$durl = url("index","deal",array("id"=>$list[$k]['id']));
				//$deal['url'] = $durl;
				/*if($v['deal_status'] == 4 || $v['deal_status'] == 5){
					$durl = "/index.php?ctl=uc_invest&act=mrefdetail&is_sj=1&id=".$v['id']."&load_id=".$v['load_id']."&user_name=".$user_name."&user_pwd=".$user_pwd;					
					$list[$k]['app_url'] = str_replace("/mapi", "", SITE_DOMAIN.$durl);
				}else{
					$durl = "/index.php?ctl=deal&act=mobile&is_sj=1&id=".$v['id'];
					$list[$k]['app_url'] = str_replace("/mapi", "", SITE_DOMAIN.$durl);
				}*/
				$load_ids[] = $v['load_id'];
			}
			//判断是否已经转让
			if(count($load_ids) > 0){
				$tmptransfer_list  = $GLOBALS['db']->getAll("SELECT * FROM ".DB_PREFIX."deal_load_transfer where load_id in(".implode(",",$load_ids).") and t_user_id > 0 and user_id=".$user_id);
				$transfer_list = array();
				foreach($tmptransfer_list as $k=>$v){
					$transfer_list[$v['load_id']] = $v;
				}
				unset($tmptransfer_list);
				foreach($list as $k=>$v){
					if(isset($transfer_list[$v['load_id']])){
						$list[$k]['has_transfer'] = 1;
					}
				}
			}
		}

    	$GLOBALS['tmpl']->assign("list",$list);
    	
		require APP_ROOT_PATH."app/Lib/deal_func.php";
		require APP_ROOT_PATH."app/Lib/page.php";
		
    	$page = new Page($count,app_conf("PAGE_SIZE"));   //初始化分页对象
    	$p  =  $page->show();
    	$GLOBALS['tmpl']->assign('pages',$p);
		
    	$GLOBALS['tmpl']->assign("page_title","客户投资列表");
    	
		$GLOBALS['tmpl']->assign("inc_file","authorized/authorized_invest.html");
		$GLOBALS['tmpl']->display("authorized/a_m.html");	
	}
	public function refdetail(){
		//277
		$authorized_info = $this->checkLogin();
    	$user_id = intval($_REQUEST['u_id']);
		$auth = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."user where id = $user_id and pid = ".$authorized_info["id"]);
		if(!$auth)
		{
			showErr("非法访问",0);
		}
		$id = intval($_REQUEST['id']);
		$load_id = intval($_REQUEST['load_id']);
		
		//require APP_ROOT_PATH."app/Lib/deal_func.php";
		require APP_ROOT_PATH."app/Lib/page.php";
		
		//$deal = get_deal($id);
		//print_r($deal);die;
		//if(!$deal || $deal['deal_status']<4){
		//	showErr("无法查看，可能有以下原因！<br>1。借款不存在<br>2。借款被删除<br>3。借款未成功");
		//}
		
		$list = $GLOBALS['db']->getAll("SELECT dlr.*,d.name,u.user_name FROM ".DB_PREFIX."deal_load_repay dlr left join ".DB_PREFIX."deal d on dlr.deal_id = d.id left join ".DB_PREFIX."user u on dlr.user_id = u.id WHERE deal_id=$id and load_id = $load_id and ((dlr.user_id = $user_id and dlr.t_user_id = 0) or dlr.t_user_id = $user_id)");
		//print_r("SELECT dlr.*,d.name,u.user_name FROM ".DB_PREFIX."deal_load_repay dlr left join ".DB_PREFIX."deal d on dlr.deal_id = d.id left join ".DB_PREFIX."user u on dlr.user_id = u.id WHERE deal_id=$id and load_id = $load_id and ((dlr.user_id = $user_id and dlr.t_user_id = 0) or dlr.t_user_id = $user_id)");die;
		
		foreach($list as $k => $v)
		{
			$list[$k]["ll_key"] = intval($v["l_key"])+1;
			$list[$k]["manage_interest_money_format"] = floatval($v["true_manage_interest_money"])>0 ?$v["true_manage_interest_money"] :$v["manage_interest_money"];
			
			if($v["t_user_id"] != 0)
			{
				$list[$k]["status_format"] = "已转" ;
			}
			elseif($v["has_repay"] == 1)
			{
				$list[$k]["status_format"] = "已还" ;
				
			}
			elseif ($v["status"] == 0 and $v["repay_time"] < TIME_UTC)
			{
				$list[$k]["status_format"] = "坏账" ;
			}
			else
			{
				$list[$k]["status_format"] = "待还" ;
			}
			
			$list[$k]["repay_day"] = $v["true_repay_time"]?$v["true_repay_time"]:$v["repay_time"];
		}
		
		$GLOBALS['tmpl']->assign('list',$list);
				
		
		
		$inrepay_info = $GLOBALS['db']->getRow("SELECT * FROM ".DB_PREFIX."deal_inrepay_repay WHERE deal_id=$id");
		$GLOBALS['tmpl']->assign("inrepay_info",$inrepay_info);
		
		$GLOBALS['tmpl']->assign("page_title","回款详情");
		$GLOBALS['tmpl']->assign("inc_file","authorized/authorized_refdetail.html");
		$GLOBALS['tmpl']->display("authorized/a_m.html");	
    }
    
	public function loan()
	{
		$authorized_info = $this->checkLogin();
		
    	$user_id = intval($_REQUEST['u_id']);
		
		$auth = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."user where id = $user_id and pid = ".$authorized_info["id"]);
		if($user_id && !$auth)
		{
			showErr("非法访问",0);
		}
		
		
		require APP_ROOT_PATH."app/Lib/deal_func.php";
		require APP_ROOT_PATH."app/Lib/page.php";
		
		$status = intval($_REQUEST['status']);
		
		$GLOBALS['tmpl']->assign("status",$status);
		
		//输出借款记录
		$page = intval($_REQUEST['p']);
		if($page==0)
			$page = 1;
		$limit = (($page-1)*app_conf("PAGE_SIZE")).",".app_conf("PAGE_SIZE");
			
		$deal_status = 4;
		if($status == 1){
			$deal_status = 5;
		}
		
		$time = TIME_UTC;

		$count_sql = "select count(*) from ".DB_PREFIX."deal d LEFT JOIN ".DB_PREFIX."user u on d.user_id = u.id where deal_status in (4,5) and u.pid ='".$authorized_info["id"]."' ";
		if($is_all==false)
			$count_sql.=" and d.is_effect = 1 and d.is_delete = 0 ";

		$sql = "select *,d.id as did,start_time as last_time,(load_money/borrow_amount*100) as progress_point,(start_time + enddate*24*3600 - ".$time.") as remain_time $extfield from ".DB_PREFIX."deal d left join ".DB_PREFIX."user u on d.user_id = u.id where deal_status in (2,4,5) and u.pid ='".$authorized_info["id"]."' ";

		if($is_all==false)
			$sql.=" and d.is_effect = 1 and d.is_delete = 0 ";
	
		if($where != '')
		{
			$sql.=" and ".$where;
			$count_sql.=" and ".$where;
		}
		
		if($user_id){
			$sql .=" and user_id = ".$user_id;
			$count_sql.=" and user_id = ".$user_id;
		}
	
		if($orderby=='')
			$sql.=" order by d.id desc ";
		else
			$sql.=" order by ".$orderby;
		
		if($limit!=""){
			$sql .=" limit ".$limit;
		}
		

		$result["count"] = $GLOBALS['db']->getOne($count_sql);
		if($result["count"]> 0){
			$result["list"] = $GLOBALS['db']->getAll($sql);
			
			if($result["list"])
			{
				foreach($result["list"] as $k=>$deal)
				{
					format_deal_item($deal,$user_name,$user_pwd);
					$result["list"][$k] = $deal;
				}
			}
		}
		else{
			$result["list"] = array();
		}
		
		$deal_ids = array();
		foreach($result['list'] as $k=>$v){
			if($v['repay_progress_point'] >= $v['generation_position'])
				$result['list'][$k]["can_generation"] = 1;
			
			$deal_ids[] = $v['id'];
		}
		if($deal_ids){
			$temp_ids = $GLOBALS['db']->getAll("SELECT `deal_id`,`status` FROM ".DB_PREFIX."generation_repay_submit WHERE deal_id in(".implode(",",$deal_ids).") ");
			$deal_g_ids = array();
			foreach($temp_ids as $k=>$v){
				$deal_g_ids[$v['deal_id']] = $v;
			}
		
		
			foreach($result['list'] as $k=>$v){
				$result['list'][$k]["ll_key"] = $GLOBALS['db']->getOne("select count(*) from ".DB_PREFIX."deal_repay where user_id = ".$v["user_id"]." and deal_id = ".$v["did"]);
				$result['list'][$k]["status_format"] = $v["deal_status"] == 5 ?"已还清":"还款中";
			}
		}
		$GLOBALS['tmpl']->assign("deal_list",$result['list']);
		
		$page = new Page($result['count'],app_conf("PAGE_SIZE"));   //初始化分页对象 		
		$p  =  $page->show();
		$GLOBALS['tmpl']->assign('pages',$p);
		
		$GLOBALS['tmpl']->assign("page_title","客户借款列表");

		$GLOBALS['tmpl']->assign("inc_file","authorized/authorized_borrowed.html");
		$GLOBALS['tmpl']->display("authorized/a_m.html");	
	}
	public function deal_refdetail(){
		//277
		$authorized_info = $this->checkLogin();
    	$user_id = intval($_REQUEST['u_id']);
		$auth = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."user where id = $user_id and pid = ".$authorized_info["id"]);
		if(!$auth)
		{
			showErr("非法访问",0);
		}
		require APP_ROOT_PATH."app/Lib/deal_func.php";
		require APP_ROOT_PATH."app/Lib/page.php";
		
		$id = intval($_REQUEST['id']);
		
		$deal = get_deal($id);
		if(!$deal)
		{
			showErr("借款不存在！");
		}
		if($deal['user_id']!=$user_id){
			showErr("不属于你的借款！");
		}
		/*if($deal['deal_status']!=5){
			showErr("借款状态不正确！");
		}*/
		$GLOBALS['tmpl']->assign('deal',$deal);
		//还款列表
		$loan_list = $GLOBALS['db']->getAll("SELECT dr.*,u.user_name FROM ".DB_PREFIX."deal_repay dr left join ".DB_PREFIX."user u on dr.user_id = u.id where deal_id=$id ORDER BY repay_time ASC");
		
		$manage_fee = 0;
		$impose_money = 0;
		$repay_money = 0;
		foreach($loan_list as $k=>$v){
			$manage_fee += $v['manage_money'];
			$impose_money += $v['impose_money'];
			$repay_money += $v['repay_money'];
			
			//还款日
			$loan_list[$k]['repay_time_format'] = to_date($v['true_repay_time']?$v['true_repay_time']:$v['repay_time'],'Y-m-d');
			//$loan_list[$k]['repay_time_format'] = to_date($v['repay_time'],'Y-m-d');
			//$loan_list[$k]['true_repay_time_format'] = to_date($v['true_repay_time'],'Y-m-d');
	
			//待还本息
			$loan_list[$k]['repay_money_format'] = format_price($v['repay_money']);
			//借款管理费
			$loan_list[$k]['manage_money_format'] = format_price($v['manage_money']);
	
			//逾期费用
			$loan_list[$k]['impose_money_format'] = format_price($v['impose_money']);
			//返佣
			$loan_list[$k]['manage_money_rebate_format'] = format_price($v['manage_money_rebate']);
			$loan_list[$k]['true_manage_money_rebate_format'] = format_price($v['true_manage_money_rebate']);
			//状态
			if($v["has_repay"] == 1)
			{
				if($v['status'] == 0){
					$loan_list[$k]['status_format'] = '提前还款';
				}elseif($v['status'] == 1){
					$loan_list[$k]['status_format'] = '准时还款';
				}elseif($v['status'] == 2){
					$loan_list[$k]['status_format'] = '逾期还款';
				}elseif($v['status'] == 3){
					$loan_list[$k]['status_format'] = '严重逾期';
				}
			}
			else
			{
				$loan_list[$k]['status_format'] = '待还';
			}
			$loan_list[$k]["ll_key"] = intval($v["l_key"])+1;
			
		}
		$GLOBALS['tmpl']->assign("manage_fee",$manage_fee);
		$GLOBALS['tmpl']->assign("impose_money",$impose_money);
		$GLOBALS['tmpl']->assign("repay_money",$repay_money);
		$GLOBALS['tmpl']->assign("loan_list",$loan_list);
		
		$inrepay_info = $GLOBALS['db']->getRow("SELECT * FROM ".DB_PREFIX."deal_inrepay_repay WHERE deal_id=$id");
		$GLOBALS['tmpl']->assign("inrepay_info",$inrepay_info);
		
		$GLOBALS['tmpl']->assign("page_title",$GLOBALS['lang']['UC_DEAL_REFUND']);
		
		$GLOBALS['tmpl']->assign("inc_file","authorized/authorized_deal_quick_refdetail.html");
		$GLOBALS['tmpl']->display("authorized/a_m.html");	
    }
	public function cash()
	{
		$user_info = $GLOBALS['authorized_info'] = $this->checkLogin();
		
		//$level_info = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."user_group where id = ".intval($authorized_info['group_id']));
		//$point_level = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."user_level where id = ".intval($authorized_info['level_id']));
		//$authorized_info['user_level'] = $level_info['name'];
		//$authorized_info['point_level'] = $point_level['name'];
		//$authorized_info['discount'] = $level_info['discount']*10;
		$GLOBALS['tmpl']->assign("user_data",$user_info);
		
		require APP_ROOT_PATH."app/Lib/uc_func.php";
		require APP_ROOT_PATH."app/Lib/page.php";
		
		
		$type_title = isset($_REQUEST['type_title']) ? intval($_REQUEST['type_title']) : 100;
		
		$times = intval($_REQUEST['times']);
		$time_status = intval($_REQUEST['time_status']);
		
		$t = strim($_REQUEST['t']); //point 积分  为空为资金
		$GLOBALS['tmpl']->assign("t",$t);
		
		$page = intval($_REQUEST['p']);
		if($page==0)
			$page = 1;
		$limit = (($page-1)*app_conf("PAGE_SIZE")).",".app_conf("PAGE_SIZE");
		
		if($t=="lock_money"){
			$title_arrays = array(
				"100" => "全部",
				"8" => "申请提现",
				"9" => "提现手续费",
				"13" => "人工充值",
				"18" => "开户奖励",
				"23" => "邀请返利",
				"27" => "其他费用",
			);
		}
		else{
			$title_arrays = array(
				"100" => "全部",
				"8" => "申请提现",
				"9" => "提现手续费",
				"13" => "人工充值",
				"18" => "开户奖励",
				"23" => "邀请返利",
				"27" => "其他费用",
			);
		}		
			
		$GLOBALS['tmpl']->assign('title_array',$title_arrays);
	
		$times_array = array(
				"0" => "全部",
				"1" => "三天以内",
				"2" => "一周以内",
				"3" => "一月以内",
				"4" => "三月以内",
				"5" => "一年以内",
		);
		$GLOBALS['tmpl']->assign('times_array',$times_array);
		$user_id = intval($GLOBALS['authorized_info']['id']);
		
		$condition = "";
		
		if ($times== 1){
			$condition.=" and create_time_ymd >= '".to_date(TIME_UTC-3600*24*3,"Y-m-d")."' "; //三天以内
		}elseif ($times==2){
			$condition.="and create_time_ymd >= '".to_date(TIME_UTC - to_date(TIME_UTC,"w") * 24*3600 ,"Y-m-d")."'"; //一周以内
		}elseif ($times==3){
			$condition.=" and create_time_ym  = '".to_date(TIME_UTC,"Ym")."'";//一月以内
		}elseif ($times==4){
			$condition.=" and create_time_ym  >= '".to_date(next_replay_month(TIME_UTC , -2 ),"Ym" )."'";//三月以内
		}elseif ($times==5){
			$condition.=" and create_time_y  = '".to_date(TIME_UTC,"Y")."'";//一年以内
		}
		
		if ($type_title==100)
		{
			$type= -1;
		}
		else{
			$type = $type_title;
		}
		
		if($time_status==1){
			$time = isset($_REQUEST['time']) ? strim($_REQUEST['time']) : "";
			
			$time_f = to_date(to_timespan($time,"Ymd"),"Y-m-d");
			$condition.=" and create_time_ymd = '".$time_f."'";
			$GLOBALS['tmpl']->assign('time_normal',$time_f);
			$GLOBALS['tmpl']->assign('time',$time);
		}
		
		if(isset($t) && $t=="lock_money"){
			$result = get_user_lock_money_log($limit,$user_id,$type,$condition); //会员信用积分
		}
		else{
			$result = get_user_money_log($limit,$user_id,$type,$condition); //会员资金日志
		}
		
		foreach($result['list'] as $k=>$v){
			$result['list'][$k]['title'] = $title_arrays[$v['type']];
		}
		
		$GLOBALS['tmpl']->assign("type_title",$type_title);
		$GLOBALS['tmpl']->assign("times",$times);
		
		$GLOBALS['tmpl']->assign("carry_money",$GLOBALS['db']->getOne("SELECT sum(money) FROM ".DB_PREFIX."user_carry WHERE user_id=".$user_id." AND `status`=1"));
		$GLOBALS['tmpl']->assign("incharge_money",$GLOBALS['db']->getOne("SELECT sum(money) FROM ".DB_PREFIX."payment_notice WHERE user_id=".$user_id." AND `is_paid`=1"));
		$GLOBALS['tmpl']->assign('time_status',$time_status);
		
		$GLOBALS['tmpl']->assign("list",$result['list']);
		$page = new Page($result['count'],app_conf("PAGE_SIZE"));   //初始化分页对象 		
		$p  =  $page->show();
		$GLOBALS['tmpl']->assign('pages',$p);
		
		$GLOBALS['tmpl']->assign("user_info",$GLOBALS['authorized_info']);
		$GLOBALS['tmpl']->assign("page_title",$GLOBALS['lang']['UC_MONEY']);
		$GLOBALS['tmpl']->assign("inc_file","authorized/authorized_money_index.html");
		$GLOBALS['tmpl']->display("authorized/a_m.html");
	}
	public function bank(){
		$GLOBALS['authorized_info'] = $this->checkLogin();
		$bank_list = $GLOBALS['db']->getAll("SELECT ub.*,b.icon FROM ".DB_PREFIX."user_bank ub left join ".DB_PREFIX."bank b on ub.bank_id=b.id  where user_id=".intval($GLOBALS['authorized_info']['id'])." ORDER BY id ASC");
		foreach($bank_list as $k=>$v){
			$bank_list[$k]['bankcode'] = str_replace(" ","",$v['bankcard']);
		}
		$GLOBALS['tmpl']->assign("bank_list",$bank_list);
		
		make_delivery_region_js();
		
		if(app_conf("OPEN_IPS")==1){
			//手续费
			$fee_config = load_auto_cache("user_carry_config");
			$json_fee = array();
			foreach($fee_config as $k=>$v){
				$json_fee[] = $v;
				if($v['fee_type']==1)
					$fee_config[$k]['fee_format'] = $v['fee']."%";
				else
					$fee_config[$k]['fee_format'] = format_price($v['fee']);
			}
			$GLOBALS['tmpl']->assign("fee_config",$fee_config);
			$GLOBALS['tmpl']->assign("json_fee",json_encode($json_fee));
		}
		
		$GLOBALS['tmpl']->assign("user_info",$GLOBALS['authorized_info']);
		$GLOBALS['tmpl']->assign("page_title",$GLOBALS['lang']['UC_CARRY']);
		$GLOBALS['tmpl']->assign("inc_file","authorized/authorized_money_carry_bank.html");
		$GLOBALS['tmpl']->display("authorized/a_m.html");
	}
	
	public function addbank(){
		//判断是否验证过身份证
		$GLOBALS['authorized_info'] = $this->checkLogin();
		if($GLOBALS['authorized_info']['real_name']==""){
			showErr("<div style='font-size:18px'>您的实名信息尚未填写！</div>为保护您的账户安全，请先填写实名信息。",1,url("index","authorized#security"));
			die();
		}
		
		
		$bank_list = $GLOBALS['db']->getAll("SELECT * FROM ".DB_PREFIX."bank ORDER BY is_rec DESC,sort DESC,id ASC");
		
		$GLOBALS['tmpl']->assign("bank_list",$bank_list);
		
		//地区列表
		
		$region_lv1 = $GLOBALS['db']->getAll("select * from ".DB_PREFIX."delivery_region where region_level = 1");  //二级地址
		$GLOBALS['tmpl']->assign("user_info",$GLOBALS['authorized_info']);
		$GLOBALS['tmpl']->assign("region_lv1",$region_lv1);
		
		$info =  $GLOBALS["tmpl"]->fetch("authorized/authorized_money_carry_addbank.html");
		showSuccess($info,1);
	}
	
	public function delbank(){
		$GLOBALS['authorized_info'] = $this->checkLogin();
		$id = intval($_REQUEST['id']);
		if($id==0){
			showErr("数据不存在",1);
		}
		$GLOBALS['db']->query("DELETE FROM ".DB_PREFIX."user_bank where user_id=".intval($GLOBALS['authorized_info']['id'])." and id=".$id);
		if($GLOBALS['db']->affected_rows()){
			showSuccess("删除成功",1);
		}
		else{
			showErr("删除失败",1);
		}
	}
	
	/**
	 * 保存
	 */
	public function savebank(){
		if(!check_hash_key()){
			showErr("非法请求!",$ajax);
		}
		$GLOBALS['authorized_info'] = $this->checkLogin();
		$data['bank_id'] = intval($_REQUEST['bank_id']);
		if($data['bank_id'] == 0)
		{
			$data['bank_id'] = intval($_REQUEST['otherbank']);
		}
		
		if($data['bank_id'] == 0)
		{
			showErr($GLOBALS['lang']['PLASE_ENTER_CARRY_BANK'],1);
		}
		
		$data['real_name'] = trim($_REQUEST['real_name']);
		if($data['real_name'] == ""){
			showErr("请输入开户名",1);
		}
		
		$data['region_lv1'] = intval($_REQUEST['region_lv1']);
		$data['region_lv2'] = intval($_REQUEST['region_lv2']);
		$data['region_lv3'] = intval($_REQUEST['region_lv3']);
		$data['region_lv4'] = intval($_REQUEST['region_lv4']);
		if($data['region_lv3'] == 0){
			showErr("请选择开户行所在地",1);
		}
		
		$data['bankzone'] = trim($_REQUEST['bankzone']);
		if($data['bankzone'] == ""){
			showErr("请输入开户行网点",1);
		}
		
		$data['bankcard'] = trim($_REQUEST['bankcard']);
		if($data['bankcard'] == ""){
			showErr($GLOBALS['lang']['PLASE_ENTER_CARRY_BANK_CODE'],1);
		}
		
		$data['user_id'] = $GLOBALS['authorized_info']['id'];
		
		if($GLOBALS['db']->getOne("SELECT count(*) FROM ".DB_PREFIX."user_bank WHERE bankcard='".$data['bankcard']."'  AND user_id=".$GLOBALS['authorized_info']['id']) > 0){
			showErr("该银行卡已存在",1);
		}
		
		$GLOBALS['db']->autoExecute(DB_PREFIX."user_bank",$data,"INSERT");
		
		if($GLOBALS['db']->affected_rows()){
			showSuccess("保存成功",1);
		}
		else{
			showErr("保存失败",1);
		}
	}
	public function carry()
	{
		$GLOBALS['authorized_info'] = $this->checkLogin();
		$bid = intval($_REQUEST['bid']);
		if($bid==0){
			app_redirect(url("index","authorized#bank"));
		}
		
		$user_bank = $GLOBALS['db']->getRow("SELECT ub.*,b.name as bankname FROM ".DB_PREFIX."user_bank ub LEFT JOIN ".DB_PREFIX."bank b on ub.bank_id=b.id where ub.user_id=".intval($GLOBALS['authorized_info']['id'])." AND ub.id=$bid ");
		if(!$user_bank){
			app_redirect(url("index","authorized#bank"));
		}
		
		$user_bank['bankcode'] = str_replace(" ","",$user_bank['bankcard']);
		$GLOBALS['tmpl']->assign("user_bank",$user_bank);
		
		
		$carry_total_money = $GLOBALS['db']->getOne("SELECT sum(money) FROM ".DB_PREFIX."user_carry WHERE user_id=".intval($GLOBALS['authorized_info']['id'])." AND status=1");
	
		$GLOBALS['tmpl']->assign("carry_total_money",$carry_total_money);
		$GLOBALS['tmpl']->assign("bid",$bid);
		
		//手续费
		$fee_config = load_auto_cache("user_carry_config");
		$json_fee = array();
		foreach($fee_config as $k=>$v){
			$json_fee[] = $v;
			if($v['fee_type']==1)
				$fee_config[$k]['fee_format'] = $v['fee']."%";
			else
				$fee_config[$k]['fee_format'] = format_price($v['fee']);
		}
		$GLOBALS['tmpl']->assign("fee_config",$fee_config);
		$GLOBALS['tmpl']->assign("json_fee",json_encode($json_fee));
		unset($fee_config);
		unset($json_fee);
		
		$GLOBALS['tmpl']->assign("user_info",$GLOBALS['authorized_info']);
		$GLOBALS['tmpl']->assign("page_title",$GLOBALS['lang']['UC_CARRY']);
		$GLOBALS['tmpl']->assign("inc_file","authorized/authorized_money_carry.html");
		$GLOBALS['tmpl']->display("authorized/a_m.html");
	}
	
	

	public function savecarry(){
		$GLOBALS['authorized_info'] = $this->checkLogin();
		if($GLOBALS['authorized_info']['id'] > 0){
			require_once APP_ROOT_PATH.'app/Lib/uc_func.php';
			$paypassword = strim(FW_DESPWD($_REQUEST['paypassword']));
			$amount = floatval($_REQUEST['amount']);
			$bid = floatval($_REQUEST['bid']);
			$status = getAuthorizedSaveCarry($amount,$paypassword,$bid);
			if($status['status'] == 0){
				showErr($status['show_err']);
			}
			else{
				showSuccess($status['show_err']);
			}
		}else{
			app_redirect(url("index","authorized#login"));
		}
		
	}	
	
	public function carry_log(){
		$GLOBALS['authorized_info'] = $this->checkLogin();
		$GLOBALS['tmpl']->assign("page_title","提现日志");
				
		//输出充值订单
		$page = intval($_REQUEST['p']);
		if($page==0)
		$page = 1;
		$limit = (($page-1)*app_conf("PAGE_SIZE")).",".app_conf("PAGE_SIZE");
		
		require APP_ROOT_PATH."app/Lib/uc_func.php";
		require APP_ROOT_PATH."app/Lib/page.php";
		
		$result = get_user_carry($limit,$GLOBALS['authorized_info']['id']);
		
		$GLOBALS['tmpl']->assign("list",$result['list']);
		$page = new Page($result['count'],app_conf("PAGE_SIZE"));   //初始化分页对象 		
		$p  =  $page->show();
		$GLOBALS['tmpl']->assign('pages',$p);
		
		
		$GLOBALS['tmpl']->assign("inc_file","authorized/authorized_money_carry_log.html");
		$GLOBALS['tmpl']->display("authorized/a_m.html");
	}
	/**
	 * 撤销提现
	 */
	public function do_reback(){
		$GLOBALS['authorized_info'] = $this->checkLogin();
		$dltid = intval($_REQUEST['dltid']);
		$GLOBALS['db']->query("UPDATE ".DB_PREFIX."user_carry SET status=4 where id=".$dltid." and status=0  and user_id = ".intval($GLOBALS['authorized_info']['id']));
		if($GLOBALS['db']->affected_rows()){
			require_once APP_ROOT_PATH."system/libs/user.php";
			$data = $GLOBALS['db']->getRow("SELECT * FROM ".DB_PREFIX."user_carry where id=".$dltid." and status=4 and user_id = ".intval($GLOBALS['authorized_info']['id']));
			modify_account(array('money'=>$data['money'],'lock_money'=>-$data['money']),$data['user_id'],"撤销提现,提现金额",8);
			modify_account(array('money'=>$data['fee'],'lock_money'=>-$data['fee']),$data['user_id'],"撤销提现，提现手续费",9);
			showSuccess("撤销操作成功",1);
		}
		else{
			showErr("撤销操作失败",1);
		}
	}
	/**
	 * 继续申请提现
	 */
	public function do_apply(){
		$GLOBALS['authorized_info'] = $this->checkLogin();
		$dltid = intval($_REQUEST['dltid']);
		$data = $GLOBALS['db']->getRow("SELECT user_id,money,fee FROM ".DB_PREFIX."user_carry where id=".$dltid." and status=4 and user_id = ".intval($GLOBALS['authorized_info']['id']));
		
		if(((float)$data['money'] + (float)$data['fee']) > (float)$GLOBALS['authorized_info']['money']){
			showErr("继续申请提现失败,金额不足",1);
		}
		
		$GLOBALS['db']->query("UPDATE ".DB_PREFIX."user_carry SET status=0 where id=".$dltid." and (money + fee) <= ".(float)$GLOBALS['authorized_info']['money']." and status=4 and user_id = ".intval($GLOBALS['authorized_info']['id']) );
		
		if($GLOBALS['db']->affected_rows()){
			require_once APP_ROOT_PATH."system/libs/user.php";
			modify_account(array('money'=>-$data['money'],'lock_money'=>$data['money']),$data['user_id'],"提现申请",8);
			modify_account(array('money'=>-$data['fee'],'lock_money'=>$data['fee']),$data['user_id'],"提现手续费",9);
			showSuccess("继续申请提现成功",1);
		}
		else{
			showErr("继续申请提现失败",1);
		}
	}
	
	public function save_pwd()
	{
		if(!check_hash_key()){
			showErr("非法请求!",$ajax);
		}
		$GLOBALS['authorized_info']  = $this->checkLogin();
		require_once APP_ROOT_PATH.'system/libs/user.php';
		foreach($_REQUEST as $k=>$v)
		{
			$_REQUEST[$k] = htmlspecialchars(addslashes(trim($v)));
		}
		
		if ($_REQUEST['sta'] == 1){
			$sms_code =trim($_REQUEST['sms_code']);
			$phone = $GLOBALS['authorized_info']['mobile'];
			//print_r($GLOBALS['authorized_info']);die;
			$code = $GLOBALS['db']->getOne("SELECT verify_code FROM ".DB_PREFIX."mobile_verify_code where mobile='".$phone."'");
			if($sms_code != $code)
			{
				showErr("验证码输出错误！",intval($_REQUEST['is_ajax']));
			}
		}
		
		$_REQUEST['id'] = intval($GLOBALS['authorized_info']['id']);
		$_REQUEST["user_type"] = 3;
		$res = save_user($_REQUEST,'UPDATE');
		if($res['status'] == 1)
		{
			$s_user_info = es_session::get("authorized_info");
			$user_info = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."user where id = '".intval($s_user_info['id'])."'");
			es_session::set("authorized_info",$user_info);
			if(intval($_REQUEST['is_ajax'])==1)
				showSuccess($GLOBALS['lang']['SUCCESS_TITLE'],1);
			else{
				app_redirect(url("index","authorized#index"));
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
	
	public function mobile(){
		$authorized_info = $this->checkLogin();
		$is_ajax = intval($_REQUEST['is_ajax']);
		
		$GLOBALS['tmpl']->assign("is_ajax",$is_ajax);
		$GLOBALS['tmpl']->assign("authorized_info",$authorized_info);
		if($is_ajax==0){
			$GLOBALS['tmpl']->assign("page_title",$GLOBALS['lang']['UC_MOBILE']);
			$GLOBALS['tmpl']->assign("inc_file","authorized/authorized_mobile_index.html");
			$GLOBALS['tmpl']->display("authorized/a_m.html");
		}
		else{
			$GLOBALS['tmpl']->display("authorized/authorized_mobile_index.html");
		}
	}
	public function invite()
	{
		$GLOBALS['authorized_info']  = $this->checkLogin();
		//$total_referral_money = $GLOBALS['db']->getOne("select sum(money) from ".DB_PREFIX."referrals where user_id = ".$GLOBALS['user_info']['id']." and pay_time > 0");
		
		//$GLOBALS['tmpl']->assign("total_referral_money",$total_referral_money);
		
		$referral_user = $GLOBALS['db']->getOne("select count(*) from ".DB_PREFIX."user where pid = ".$GLOBALS['authorized_info']['id']." and is_effect=1 and is_delete=0");
		$GLOBALS['tmpl']->assign("referral_user",$referral_user);
		
		$GLOBALS['tmpl']->assign("page_title",$GLOBALS['lang']['UC_INVITE']);
		$GLOBALS['tmpl']->assign("inc_file","authorized/authorized_invite_index.html");
		
		if(intval(app_conf("URL_MODEL")) == 0)
			$depart="&";
		else
			$depart="?";	
		$share_url = SITE_DOMAIN.url("index","user#register");
		if($GLOBALS['authorized_info']){
			$share_url_mobile = $share_url.$depart."r=".base64_encode($GLOBALS['authorized_info']['mobile']);
			$share_url_username = $share_url.$depart."r=".base64_encode($GLOBALS['authorized_info']['user_name']);
		}
			
		$GLOBALS['tmpl']->assign("share_url_mobile",$share_url_mobile);
		$GLOBALS['tmpl']->assign("share_url_username",$share_url_username);		
				
		$GLOBALS['tmpl']->display("authorized/a_m.html");
	}
	function commission(){
		
		$authorized_info  = $this->checkLogin();
		require APP_ROOT_PATH."app/Lib/deal_func.php";
		require APP_ROOT_PATH."app/Lib/page.php";
		
		$page = intval($_REQUEST['p']);
		if($page==0)
			$page = 1;
		$limit = (($page-1)*app_conf("PAGE_SIZE")).",".app_conf("PAGE_SIZE");
		$result = array();
		$result['count'] = $GLOBALS['db']->getOne("SELECT count(*) FROM ".DB_PREFIX."user_money_log WHERE user_id=".$authorized_info['id']." and type = 23 ORDER BY id DESC");

		if($result['count'] > 0){
			
			$result['list'] = $GLOBALS['db']->getAll("SELECT uml.* FROM ".DB_PREFIX."user_money_log uml WHERE uml.user_id=".$authorized_info['id']." and type = 23 ORDER BY uml.id DESC");
		}
		
		$GLOBALS['tmpl']->assign("list",$result['list']);
		$page = new Page($result['count'],app_conf("PAGE_SIZE"));   //初始化分页对象 		
		$p  =  $page->show();
		$GLOBALS['tmpl']->assign('pages',$p);
		$GLOBALS['tmpl']->assign("inc_file","authorized/authorized_commission.html");
		$GLOBALS['tmpl']->display("authorized/a_m.html");
	}
}
?>