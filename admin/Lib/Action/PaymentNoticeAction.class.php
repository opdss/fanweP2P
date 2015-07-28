<?php
// +----------------------------------------------------------------------
// | Fanwe 方维p2p借贷系统
// +----------------------------------------------------------------------
// | Copyright (c) 2011 http://www.fanwe.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 云淡风轻(88522820@qq.com)
// +----------------------------------------------------------------------

class PaymentNoticeAction extends CommonAction{
	public function com_search(){
		$map = array ();
	
		if (!isset($_REQUEST['end_time']) || $_REQUEST['end_time'] == '') {
			$_REQUEST['end_time'] = to_date(get_gmtime(), 'Y-m-d');
		}
	
	
		if (!isset($_REQUEST['start_time']) || $_REQUEST['start_time'] == '') {
			$_REQUEST['start_time'] = dec_date($_REQUEST['end_time'], 7);// $_SESSION['q_start_time_7'];
		}
	
		$map['start_time'] = trim($_REQUEST['start_time']);
		$map['end_time'] = trim($_REQUEST['end_time']);
	
		$this->assign("start_time",$map['start_time']);
		$this->assign("end_time",$map['end_time']);
	
	
		if ($map['start_time'] == ''){
			$this->error('开始时间 不能为空');
			exit;
		}
	
		if ($map['end_time'] == ''){
			$this->error('结束时间 不能为空');
			exit;
		}
	
		$d = explode('-',$map['start_time']);
		if (checkdate($d[1], $d[2], $d[0]) == false){
			$this->error("开始时间不是有效的时间格式:{$map['start_time']}(yyyy-mm-dd)");
			exit;
		}
	
		$d = explode('-',$map['end_time']);
		if (checkdate($d[1], $d[2], $d[0]) == false){
			$this->error("结束时间不是有效的时间格式:{$map['end_time']}(yyyy-mm-dd)");
			exit;
		}
	
		if (to_timespan($map['start_time']) > to_timespan($map['end_time'])){
			$this->error('开始时间不能大于结束时间:'.$map['start_time'].'至'.$map['end_time']);
			exit;
		}
	
		$q_date_diff = 31;
		$this->assign("q_date_diff",$q_date_diff);
		//echo abs(to_timespan($map['end_time']) - to_timespan($map['start_time'])) / 86400 + 1;
		if ($q_date_diff > 0 && (abs(to_timespan($map['end_time']) - to_timespan($map['start_time'])) / 86400  + 1 > $q_date_diff)){
			$this->error("查询时间间隔不能大于  {$q_date_diff} 天");
			exit;
		}
		
		return $map;
	}
	
	public function index()
	{
		
		$map = $this->com_search();
		
		
		if(trim($_REQUEST['order_sn'])!='')
		{
			$condition['order_id'] = M("DealOrder")->where("order_sn='".trim($_REQUEST['order_sn'])."'")->getField("id");
		}
		if(intval($_REQUEST['no_payment_id']) > 0){
			$condition['payment_id'] = array("neq",intval($_REQUEST['no_payment_id']));
		}
		if(trim($_REQUEST['notice_sn'])!='')
		{
			$condition['notice_sn'] = $_REQUEST['notice_sn'];
		}	
		
		if($map['start_time'] != '' && $map['end_time'] && ( !isset($_REQUEST['is_paid']) || intval($_REQUEST['is_paid'])==-1 || intval($_REQUEST['is_paid'])==1 ) ){
			if(intval($_REQUEST['is_paid'])==1)
				$condition['pay_date']= array("between",array($map['start_time'],$map['end_time']));
			else
				$condition['create_date']= array("between",array($map['start_time'],$map['end_time']));
		}
		
		if(intval($_REQUEST['is_paid'])==0)
		{
			//$condition['create_time']= array("between",array(to_timespan($map['start_time'],"Y-m-d"),to_timespan(dec_date($map['end_time'],-1),"Y-m-d")));
			$condition['create_date']= array("between",array($map['start_time'],$map['end_time']));
		}
	
		
		$payment_id = M("Payment")->where("class_name = 'Otherpay' ")->getField("id");
		$condition['payment_id'] = array("neq",intval($payment_id));
		
		if(intval($_REQUEST['payment_id'])==0){
			unset($_REQUEST['payment_id']);
		}
		else{
			$condition['payment_id'] = array("eq",intval($_REQUEST['payment_id']));
		}
		if(intval($_REQUEST['is_paid'])==-1 || !isset($_REQUEST['is_paid']))unset($_REQUEST['is_paid']);
		
		//dump($condition);
		
		$this->assign("default_map",$condition);
		$this->assign("payment_list",M("Payment")->findAll());
		parent::index();
	}
	
	public function online()
	{
		$map = $this->com_search();
		if(trim($_REQUEST['order_sn'])!='')
		{
			$condition['order_id'] = M("DealOrder")->where("order_sn='".trim($_REQUEST['order_sn'])."'")->getField("id");
		}
		if(intval($_REQUEST['no_payment_id']) > 0){
			$condition['payment_id'] = array("neq",intval($_REQUEST['no_payment_id']));
		}
		if(trim($_REQUEST['notice_sn'])!='')
		{
			$condition['notice_sn'] = $_REQUEST['notice_sn'];
		}		
		$payment_id = M("Payment")->where("class_name = 'Otherpay'")->getField("id");
		$condition['payment_id'] = $payment_id;

		if($map['start_time'] != '' && $map['end_time']){
			$condition['create_time']= array("between",array(to_timespan($map['start_time'],"Y-m-d"),to_timespan(dec_date($map['end_time'],-1),"Y-m-d")));
		}
		
		if(intval($_REQUEST['is_paid'])==-1 || !isset($_REQUEST['is_paid']))unset($_REQUEST['is_paid']);
		$this->assign("default_map",$condition);
		parent::index();
	
	}
	
	//管理员收款
	public function update(){   
		
		$notice_id = intval($_REQUEST['id']);
		$outer_notice_sn = strim($_REQUEST['outer_notice_sn']);
		$bank_id = strim($_REQUEST['bank_id']);
		
		//开始由管理员手动收款
		require_once APP_ROOT_PATH."system/libs/cart.php";
		$payment_notice = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."payment_notice where id = ".$notice_id);
		
		if($payment_notice['is_paid'] == 0 )
		{
			if($bank_id)
			{
				$GLOBALS['db']->query("update ".DB_PREFIX."payment_notice set  bank_id = ".$bank_id." where id = ".$notice_id." and is_paid = 0");
			}else{
				$this->error ("请输入直联银行编号");
			}
			payment_paid($notice_id,"银行流水号 ".':'.$outer_notice_sn);	//对其中一条款支付的付款单付款
			$msg = sprintf(l("ADMIN_PAYMENT_PAID"),$payment_notice['notice_sn']);
			save_log($msg,1);
			$this->success(l("ORDER_PAID_SUCCESS"));
		}
		else {
			$this->error (l("INVALID_OPERATION"));
		}
	}
	
	public function gathering(){
		
		$id = intval($_REQUEST['id']);
		$this->assign("id",$id);
		$this->display();
	}
	

	
}
?>