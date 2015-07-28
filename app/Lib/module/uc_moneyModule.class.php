<?php
// +----------------------------------------------------------------------
// | Fanwe 方维p2p借贷系统
// +----------------------------------------------------------------------
// | Copyright (c) 2011 http://www.fanwe.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 云淡风轻(88522820@qq.com)
// +----------------------------------------------------------------------

require APP_ROOT_PATH.'app/Lib/uc.php';

class uc_moneyModule extends SiteBaseModule
{
	private $creditsettings;
	private $allow_exchange = false;

	public function __construct()
	{
		if(in_array(ACTION_NAME,array("carry","savecarry"))){
			$is_ajax = intval($_REQUEST['is_ajax']);
			//判断是否是黑名单会员
	    	if($GLOBALS['user_info']['is_black']==1){
	    		showErr("您当前无权限提现，具体联系网站客服",$is_ajax,url("index","uc_center"));
	    	}
		}
		if(file_exists(APP_ROOT_PATH."public/uc_config.php"))
		{
			require_once APP_ROOT_PATH."public/uc_config.php";
		}
		if(app_conf("INTEGRATE_CODE")=='Ucenter'&&UC_CONNECT=='mysql')
		{
			if(file_exists(APP_ROOT_PATH."public/uc_data/creditsettings.php"))
			{
				require_once APP_ROOT_PATH."public/uc_data/creditsettings.php";
				$this->creditsettings = $_CACHE['creditsettings'];
				if(count($this->creditsettings)>0)
				{
					foreach($this->creditsettings as $k=>$v)
					{
						$this->creditsettings[$k]['srctitle'] = $this->credits_CFG[$v['creditsrc']]['title'];
					}
					$this->allow_exchange = true;
					$GLOBALS['tmpl']->assign("allow_exchange",$this->allow_exchange);
				}
			}
		}
		parent::__construct();
	}
	
	
	public function exchange()
	{		
		$user_info = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."user where id = ".intval($GLOBALS['user_info']['id']));		
		$GLOBALS['tmpl']->assign("user_info",$user_info);
		$GLOBALS['tmpl']->assign("page_title",$GLOBALS['lang']['UC_EXCHANGE']);
		$GLOBALS['tmpl']->assign("inc_file","inc/uc/uc_money_exchange.html");
		
		$GLOBALS['tmpl']->assign("exchange_data",$this->creditsettings);
		$GLOBALS['tmpl']->assign("exchange_json_data",json_encode($this->creditsettings));
		
		$GLOBALS['tmpl']->display("page/uc.html");
	}
	
	public function doexchange()
	{		
		if($this->allow_exchange)
		{
			$user_pwd = md5(addslashes(trim($_REQUEST['password'])));
			$user_info = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."user where id = ".intval($GLOBALS['user_info']['id']));		
			
			if($user_info['user_pwd']=="")
			{
				//判断是否为初次整合
				//载入会员整合
				$integrate_code = trim(app_conf("INTEGRATE_CODE"));
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
				if($integrate_obj)
				{			
					$result = $integrate_obj->login($user_info['user_name'],$user_pwd);						
					if($result['status'])
					{
						$GLOBALS['db']->query("update ".DB_PREFIX."user set user_pwd = '".$user_pwd."' where id = ".$user_info['id']);
						$user_info['user_pwd'] = $user_pwd;
					}								
				}
			}
			if($user_info['user_pwd']==$user_pwd)
			{
				$cfg = $this->creditsettings[addslashes(trim($_REQUEST['key']))];
				if($cfg)
				{	
					$amount = floor($_REQUEST['amountdesc']);
					$use_amount = floor($amount*$cfg['ratio']); //消耗的本系统积分
				    $field = $this->credits_CFG[$cfg['creditsrc']]['field'];
					
				    if($user_info[$field]<$use_amount)
				    {
				    	$data = array("status"=>false,"message"=>$cfg['srctitle']."不足，不能兑换");
						ajax_return($data);
				    }				    
					
					include_once(APP_ROOT_PATH . 'uc_client/client.php');	
				    $res = call_user_func_array("uc_credit_exchange_request", array(
				    	$user_info['integrate_id'],  //uid(整合的UID)
				    	$cfg['creditsrc'],  //原积分ID
				    	$cfg['creditdesc'],  //目标积分ID
				    	$cfg['appiddesc'],  //toappid目标应用ID
				    	$amount,  //amount额度(计算过的目标应用的额度)
				    ));
				    if($res)
				    {
				    	//兑换成功
				    	$use_amount = 0 - $use_amount;				    	
				    	$credit_data = array($field=>$use_amount);
				    	require_once APP_ROOT_PATH."system/libs/user.php";
				    	modify_account($credit_data,$user_info['id'],"ucenter兑换支出",22);
				    	$data = array("status"=>true,"message"=>"兑换成功");
						ajax_return($data);
				    }
				    else
				    {
				    	$data = array("status"=>false,"message"=>"兑换失败");
						ajax_return($data);
				    }
				}
				else
				{
					$data = array("status"=>false,"message"=>"非法的兑换请求");
					ajax_return($data);
				}
			}
			else
			{
				$data = array("status"=>false,"message"=>"登录密码不正确");
				ajax_return($data);
			}
		}
		else
		{
			$data = array("status"=>false,"message"=>"未开启兑换功能");
			ajax_return($data);
		}
	}
	
	public function index()
	{
		
		$user_info = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."user where id = ".intval($GLOBALS['user_info']['id']));		
		$level_info = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."user_group where id = ".intval($user_info['group_id']));
		$point_level = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."user_level where id = ".intval($user_info['level_id']));
		$user_info['user_level'] = $level_info['name'];
		$user_info['point_level'] = $point_level['name'];
		$user_info['discount'] = $level_info['discount']*10;
		$GLOBALS['tmpl']->assign("user_data",$user_info);
		
		$type_title = isset($_REQUEST['type_title']) ? intval($_REQUEST['type_title']) : 100;
		
		$times = intval($_REQUEST['times']);
		$time_status = intval($_REQUEST['time_status']);
		
		$t = strim($_REQUEST['t']); //point 积分  为空为资金
		$GLOBALS['tmpl']->assign("t",$t);
		
		$page = intval($_REQUEST['p']);
		if($page==0)
			$page = 1;
		$limit = (($page-1)*app_conf("PAGE_SIZE")).",".app_conf("PAGE_SIZE");
		
		if($t=="point"){
			$title_arrays = array(
				"100" => "全部",	
				"0" => "结存",	
				"4" => "偿还本息",	
				"5" => "回收本息",	
				"6" => "提前还款",	
				"7" => "提前回收",	
				"8" => "申请认证",	
				"11" => "逾期还款",
				"13" => "人工操作",
				"14" => "借款服务费",
				"18" => "开户奖励",
				//"22" => "兑换",
				"23" => "邀请返利",	
				"24" => "投标返利",
				"25" => "签到成功",
			);
		}
		elseif($t=="lock_money"){
			$title_arrays = array(
				"100" => "全部",
				"0" => "结存",
				"1" => "充值",
				"2" => "投标成功",
				"8" => "申请提现",
				"9" => "提现手续费",
				"13" => "人工操作",
				"18" => "开户奖励",
				"19" => "流标还返",
			);
		}
		elseif($t=="score"){
			$title_arrays = array(
				"100" => "全部",
				"0" => "结存",
				"1" => "充值",
				"2" => "投标成功",
				"3" => "招标成功",
				"8" => "申请提现",
				"9" => "提现手续费",
				"13" => "人工操作",
				"18" => "开户奖励",
				"22" => "兑换",
				"25" => "签到成功",
				"28"=>"投资奖励 ",
				"29"=>"红包奖励 "
			);
		}elseif($t=="nmc_amount"){
			$title_arrays = array(
				"100" => "全部",
				"22" => "兑换",
				"28"=>"投资奖励 ",
				"29"=>"红包奖励 "
			);
		}
		else{
			$title_arrays = array(
				"100" => "全部",
				"0" => "结存",
				"1" => "充值",
				"2" => "投标成功",
				"3" => "招标成功",
				"4" => "偿还本息",
				"5" => "回收本息",
				"6" => "提前还款",
				"7" => "提前回收",
				"8" => "申请提现",
				"9" => "提现手续费",
				"10" => "借款管理费",
				"11" => "逾期罚息",
				"12" => "逾期管理费",
				"13" => "人工充值",
				"14" => "借款服务费",
				"15" => "出售债权",
				"16" => "购买债权",
				"17" => "债权转让管理费",
				"18" => "开户奖励",
				"19" => "流标还返",
				"20" => "投标管理费",
				"21" => "投标逾期收入",
				"22" => "兑换",
				"23" => "邀请返利",
				"24" => "投标返利",
				"25" => "签到成功",
				"26" => "逾期罚金（垫付后）",
				"27" => "其他费用",
				"28"=>"投资奖励 ",
				"29"=>"红包奖励 "
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
		$user_id = intval($GLOBALS['user_info']['id']);
		
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
		
		if(isset($t) && $t=="point"){
			$result = get_user_point_log($limit,$user_id,$type,$condition); //会员信用积分
		}
		elseif(isset($t) && $t=="lock_money"){
			$result = get_user_lock_money_log($limit,$user_id,$type,$condition); //会员信用积分
		}
		elseif(isset($t) && $t=="score"){
			$result = get_user_score_log($limit,$user_id,$type,$condition); //会员积分
		}
		elseif(isset($t) && $t=="nmc_amount"){
			$result = get_user_nmc_amount_log($limit,$user_id,$type,$condition); //不可提现资金日志
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
		
		$GLOBALS['tmpl']->assign("page_title",$GLOBALS['lang']['UC_MONEY']);
		$GLOBALS['tmpl']->assign("inc_file","inc/uc/uc_money_index.html");
		$GLOBALS['tmpl']->display("page/uc.html");
	}
	
	
	public function incharge()
	{
		 
		$GLOBALS['tmpl']->assign("page_title",$GLOBALS['lang']['UC_MONEY_INCHARGE']);
	
		//输出支付方式
		$payment_list = $GLOBALS['db']->getAll("select * from ".DB_PREFIX."payment where is_effect = 1 and class_name <> 'Account' and class_name <> 'Voucher' and class_name <> 'tenpayc2c' and online_pay = 1 order by sort desc");			
		foreach($payment_list as $k=>$v)
		{
			if($v['class_name']=='Alipay')
			{
				$cfg = unserialize($v['config']);
				if($cfg['alipay_service']!=2)
				{
					unset($payment_list[$k]);
					continue;
				}
			}
			$directory = APP_ROOT_PATH."system/payment/";
			$file = $directory. $v['class_name']."_payment.php";
			if(file_exists($file))
			{
				require_once($file);
				$payment_class = $v['class_name']."_payment";
				$payment_object = new $payment_class();
				$payment_list[$k]['display_code'] = $payment_object->get_display_code();						
			}
			else
			{
				unset($payment_list[$k]);
			}
		}
		$GLOBALS['tmpl']->assign("payment_list",$payment_list);
		
		//判断是否有线下支付
		$below_payment = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."payment where is_effect = 1 and class_name = 'Otherpay'");
		if($below_payment){
			$directory = APP_ROOT_PATH."system/payment/";
			$file = $directory. $below_payment['class_name']."_payment.php";	
			if(file_exists($file))
			{
				require_once($file);
				$payment_class = $below_payment['class_name']."_payment";
				
				$payment_object = new $payment_class();
				$below_payment['display_code'] = $payment_object->get_display_code();						
			}
			
			$GLOBALS['tmpl']->assign("below_payment",$below_payment);
		}
		
		$GLOBALS['tmpl']->assign("inc_file","inc/uc/uc_money_incharge.html");
		$GLOBALS['tmpl']->display("page/uc.html");
	}
	
	public function incharge_log()
	{
		 
		$GLOBALS['tmpl']->assign("page_title",$GLOBALS['lang']['UC_MONEY_INCHARGE_LOG']);
				
		//输出充值订单
		$page = intval($_REQUEST['p']);
		if($page==0)
		$page = 1;
		$limit = (($page-1)*app_conf("PAGE_SIZE")).",".app_conf("PAGE_SIZE");
	
		$condition = "";
		
		$is_paid = isset($_REQUEST['is_paid']) ? intval($_REQUEST['is_paid']) : 0;
		if($is_paid == 0 )
		{
			$condition.=" and pn.is_paid = 0"; 
		}else{
			$condition.=" and pn.is_paid = 1";
		}
		$GLOBALS['tmpl']->assign('is_paid',$is_paid);
		
		$result = get_user_incharge_log($limit,$GLOBALS['user_info']['id'],$condition);
		
		$GLOBALS['tmpl']->assign("list",$result['list']);
		$page = new Page($result['count'],app_conf("PAGE_SIZE"));   //初始化分页对象 		
		$p  =  $page->show();
		$GLOBALS['tmpl']->assign('pages',$p);
		
		
		$GLOBALS['tmpl']->assign("inc_file","inc/uc/uc_money_incharge_log.html");
		$GLOBALS['tmpl']->display("page/uc.html");
	}
	
	public function incharge_done()
	{
		/*
		$payment_id = intval($_REQUEST['payment']);
		$money = floatval($_REQUEST['money']);
		$bank_id = addslashes(htmlspecialchars(trim($_REQUEST['bank_id'])));
		$memo = addslashes(htmlspecialchars(trim($_REQUEST['memo'])));
		
		
		if($money<=0)
		{
			showErr($GLOBALS['lang']['PLEASE_INPUT_CORRECT_INCHARGE']);
		}
		
		$payment_info = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."payment where id = ".$payment_id);
		if(!$payment_info)
		{
			showErr($GLOBALS['lang']['PLEASE_SELECT_PAYMENT']);
		}
		//开始生成订单
		$now = TIME_UTC;
		$order['type'] = 1; //充值单
		$order['user_id'] = $GLOBALS['user_info']['id'];
		$order['create_time'] = $now;
		if($payment_info['fee_type'] == 0)
			$order['total_price'] = $money + $payment_info['fee_amount'];
		else
			$order['total_price'] = $money + $payment_info['fee_amount']*$money;
			
		$order['deal_total_price'] = $money;
		$order['pay_amount'] = 0;  
		$order['pay_status'] = 0;  
		$order['delivery_status'] = 5;  
		$order['order_status'] = 0; 
		$order['payment_id'] = $payment_id;
		if($payment_info['fee_type'] == 0)
			$order['payment_fee'] = $payment_info['fee_amount'];
		else
			$order['payment_fee'] = $payment_info['fee_amount']*$money;
			
		$order['bank_id'] = $bank_id;
		$order['memo'] = $bank_id;
		if($payment_info['class_name']=='Otherpay' && $order['memo']!=""){
			
			$payment_info['config'] = unserialize($payment_info['config']);
			$order['memo'] = "银行流水单号:".$order['memo'];
			$order['memo'] .= "<br>开户行：".$payment_info['config']['pay_bank'][$order['bank_id']];
			$order['memo'] .= "<br>充值银行：".$payment_info['config']['pay_name'][$order['bank_id']];
			$order['memo'] .= "<br>帐号：".$payment_info['config']['pay_account'][$order['bank_id']];
			$order['memo'] .= "<br>用户：".$payment_info['config']['pay_account_name'][$order['bank_id']];
		}
		do
		{
			$order['order_sn'] = to_date(TIME_UTC,"Ymdhis").rand(100,999);
			$GLOBALS['db']->autoExecute(DB_PREFIX."deal_order",$order,'INSERT','','SILENT'); 
			$order_id = intval($GLOBALS['db']->insert_id());
		}while($order_id==0);
		
		require_once APP_ROOT_PATH."system/libs/cart.php";
		$payment_notice_id = make_payment_notice($order['total_price'],$order_id,$payment_info['id'],$order['memo']);
		//创建支付接口的付款单
	*/
		$payment_id = intval($_REQUEST['payment']);
		$money = floatval($_REQUEST['money']);
		$bank_id = addslashes(htmlspecialchars(trim($_REQUEST['bank_id'])));
		$memo = addslashes(htmlspecialchars(trim($_REQUEST['memo'])));
		$pingzheng = replace_public(trim($_REQUEST['pingzheng']));
		
		$status = getInchargeDone($payment_id,$money,$bank_id,$memo,$pingzheng);
		if($status['status'] == 0){			
			showErr($status['show_err']);
		}
		else{
			if($status['pay_status'])
			{
				app_redirect(url("index","payment#incharge_done",array("id"=>$status['order_id']))); //充值支付成功
			}
			else
			{
				app_redirect(url("index","payment#pay",array("id"=>$status['payment_notice_id'])));
			}
		}		
		
	}
	

	
	public function bank(){
		$bank_list = $GLOBALS['db']->getAll("SELECT ub.*,b.icon FROM ".DB_PREFIX."user_bank ub left join ".DB_PREFIX."bank b on ub.bank_id=b.id  where user_id=".intval($GLOBALS['user_info']['id'])." ORDER BY id ASC");
		foreach($bank_list as $k=>$v){
			$bank_list[$k]['bankcode'] = str_replace(" ","",$v['bankcard']);
		}
		$GLOBALS['tmpl']->assign("bank_list",$bank_list);
		
		make_delivery_region_js();
		
		if(app_conf("OPEN_IPS") > 0){
			if(strtolower(getCollName()) == "yeepay")
			{
				$yee_bank = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."yeepay_bind_bank_card where is_callback =1 and code =1 and platformUserNo = ".intval($GLOBALS['user_info']['id']));
				$GLOBALS['tmpl']->assign("yee_bank",$yee_bank);
				$GLOBALS['tmpl']->assign("is_yee",1);
			}
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
		
		$GLOBALS['tmpl']->assign("page_title",$GLOBALS['lang']['UC_CARRY']);
		$GLOBALS['tmpl']->assign("inc_file","inc/uc/uc_money_carry_bank.html");
		$GLOBALS['tmpl']->display("page/uc.html");
	}
	
	public function addbank(){
		//判断是否验证过身份证
		if($GLOBALS['user_info']['real_name']==""){
			showErr("<div style='font-size:18px'>您的实名信息尚未填写！</div>为保护您的账户安全，请先填写实名信息。",1,url("index","uc_account#security"));
			die();
		}
		
		
		$bank_list = $GLOBALS['db']->getAll("SELECT * FROM ".DB_PREFIX."bank ORDER BY is_rec DESC,sort DESC,id ASC");
		
		$GLOBALS['tmpl']->assign("bank_list",$bank_list);
		
		//地区列表
		
		$region_lv1 = $GLOBALS['db']->getAll("select * from ".DB_PREFIX."delivery_region where region_level = 1");  //二级地址
		$GLOBALS['tmpl']->assign("region_lv1",$region_lv1);
		
		$info =  $GLOBALS["tmpl"]->fetch("inc/uc/uc_money_carry_addbank.html");
		
		showSuccess($info,1);
	}
	
	public function delbank(){
		$id = intval($_REQUEST['id']);
		if($id==0){
			showErr("数据不存在",1);
		}
		$GLOBALS['db']->query("DELETE FROM ".DB_PREFIX."user_bank where user_id=".intval($GLOBALS['user_info']['id'])." and id=".$id);
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
		
		$data['user_id'] = $GLOBALS['user_info']['id'];
		
		if($GLOBALS['db']->getOne("SELECT count(*) FROM ".DB_PREFIX."user_bank WHERE bankcard='".$data['bankcard']."'  AND user_id=".$GLOBALS['user_info']['id']) > 0){
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
		$bid = intval($_REQUEST['bid']);
		if($bid==0){
			app_redirect(url("index","uc_money#bank"));
		}
		
		$user_bank = $GLOBALS['db']->getRow("SELECT ub.*,b.name as bankname FROM ".DB_PREFIX."user_bank ub LEFT JOIN ".DB_PREFIX."bank b on ub.bank_id=b.id where ub.user_id=".intval($GLOBALS['user_info']['id'])." AND ub.id=$bid ");
		if(!$user_bank){
			app_redirect(url("index","uc_money#bank"));
		}
		
		$user_bank['bankcode'] = str_replace(" ","",$user_bank['bankcard']);
		$GLOBALS['tmpl']->assign("user_bank",$user_bank);
		
		$red_envelope = $GLOBALS['db']->getOne("select sum(money) from ".DB_PREFIX."user_money_log where user_id = ".intval($GLOBALS['user_info']['id'])." and type = 28 or type = 29  ");
		$exchange = $GLOBALS['db']->getOne("select sum(money) from ".DB_PREFIX."user_money_log where user_id = ".intval($GLOBALS['user_info']['id'])." and type = 22 ");
		
		$red_envelope = format_price($red_envelope);
		$exchange = format_price($exchange);
		$GLOBALS['tmpl']->assign("red_envelope",$red_envelope);
		$GLOBALS['tmpl']->assign("exchange",$exchange);
		
		$carry_total_money = $GLOBALS['db']->getOne("SELECT sum(money) FROM ".DB_PREFIX."user_carry WHERE user_id=".intval($GLOBALS['user_info']['id'])." AND status=1");
	
		$GLOBALS['tmpl']->assign("carry_total_money",$carry_total_money);
		$GLOBALS['tmpl']->assign("bid",$bid);
		
		$tmoney=$GLOBALS['user_info']['money']-$GLOBALS['user_info']['nmc_amount'];
		
		$vip_id = 0;
		if($GLOBALS['user_info']['vip_id'] > 0 && $GLOBALS['user_info']['vip_state'] == 1){
			$vip_id = $GLOBALS['user_info']['vip_id'];
		}
		
		
		//手续费
		$fee_config = load_auto_cache("user_carry_config",array("vip_id"=>$vip_id));
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
		
		$GLOBALS['tmpl']->assign("page_title",$GLOBALS['lang']['UC_CARRY']);
		$GLOBALS['tmpl']->assign("inc_file","inc/uc/uc_money_carry.html");
		$GLOBALS['tmpl']->display("page/uc.html");
	}
	
	

	function savecarry(){
		
		if($GLOBALS['user_info']['id'] > 0){
			require_once APP_ROOT_PATH.'app/Lib/uc_func.php';
			
			$paypassword = strim(FW_DESPWD($_REQUEST['paypassword']));
			$amount = floatval($_REQUEST['amount']);
			$bid = floatval($_REQUEST['bid']);
			
			$status = getUcSaveCarry($amount,$paypassword,$bid);
			if($status['status'] == 0){
				showErr($status['show_err']);
			}
			else{
				showSuccess($status['show_err']);
			}
		}else{
			app_redirect(url("index","user#login"));
		}
		
	}	
	
	function carry_log(){
		$GLOBALS['tmpl']->assign("page_title","提现日志");
				
		//输出充值订单
		$page = intval($_REQUEST['p']);
		if($page==0)
		$page = 1;
		$limit = (($page-1)*app_conf("PAGE_SIZE")).",".app_conf("PAGE_SIZE");
	
		$result = get_user_carry($limit,$GLOBALS['user_info']['id']);
		
		$GLOBALS['tmpl']->assign("list",$result['list']);
		$page = new Page($result['count'],app_conf("PAGE_SIZE"));   //初始化分页对象 		
		$p  =  $page->show();
		$GLOBALS['tmpl']->assign('pages',$p);
		
		
		$GLOBALS['tmpl']->assign("inc_file","inc/uc/uc_money_carry_log.html");
		$GLOBALS['tmpl']->display("page/uc.html");
	}
	/**
	 * 撤销提现
	 */
	public function do_reback(){
		$dltid = intval($_REQUEST['dltid']);
		$GLOBALS['db']->query("UPDATE ".DB_PREFIX."user_carry SET status=4 where id=".$dltid." and status=0  and user_id = ".intval($GLOBALS['user_info']['id']));
		if($GLOBALS['db']->affected_rows()){
			require_once APP_ROOT_PATH."system/libs/user.php";
			$data = $GLOBALS['db']->getRow("SELECT * FROM ".DB_PREFIX."user_carry where id=".$dltid." and status=4 and user_id = ".intval($GLOBALS['user_info']['id']));
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
		$dltid = intval($_REQUEST['dltid']);
		$data = $GLOBALS['db']->getRow("SELECT user_id,money,fee FROM ".DB_PREFIX."user_carry where id=".$dltid." and status=4 and user_id = ".intval($GLOBALS['user_info']['id']));
		if(((float)$data['money'] + (float)$data['fee'] + (float)$GLOBALS['user_info']['nmc_amount']) > (float)$GLOBALS['user_info']['money']){
			showErr("继续申请提现失败,金额不足",1);
		}
		
		$GLOBALS['db']->query("UPDATE ".DB_PREFIX."user_carry SET status=0 where id=".$dltid." and (money + fee + ".$GLOBALS['user_info']['nmc_amount'].") <= ".(float)$GLOBALS['user_info']['money']." and status=4 and user_id = ".intval($GLOBALS['user_info']['id']) );
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
}
?>