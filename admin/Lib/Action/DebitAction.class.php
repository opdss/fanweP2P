<?php
// +----------------------------------------------------------------------
// | Fanwe 方维p2p借贷系统
// +----------------------------------------------------------------------
// | Copyright (c) 2011 http://www.fanwe.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 云淡风轻(88522820@qq.com)
// +----------------------------------------------------------------------

class DebitAction extends CommonAction{
	public function index()
	{
		$debit_conf = $GLOBALS["db"]->getRow("select * from ".DB_PREFIX."debit_conf");
		
		$debit_conf["borrow_amount_cfg"] = unserialize($debit_conf["borrow_amount_cfg"]);

		$this->assign("debit",$debit_conf);
		$this->assign("main_title","白条设置");
		$this->display();
	}
	
	public function update_debit(){
		
		$borrow_amount_cfg = array();
		foreach($_REQUEST["borrow_amount_cfg"] as $k => $v)
		{
			if($v != "")
				$borrow_amount_cfg[] = htmlspecialchars(addslashes(trim($v)));
		}
		$data = array();
		$data["borrow_amount_cfg"] = serialize($borrow_amount_cfg);
		$data["loantype"] = intval($_REQUEST["loantype"]);
		$data["services_fee"] = floatval($_REQUEST["services_fee"]);
		$data["manage_fee"] = floatval($_REQUEST["manage_fee"]);
		$data["manage_impose_fee_day1"] = strim($_REQUEST["manage_impose_fee_day1"]);
		$data["manage_impose_fee_day2"] = strim($_REQUEST["manage_impose_fee_day2"]);
		$data["impose_fee_day1"] = strim($_REQUEST["impose_fee_day1"]);
		$data["impose_fee_day2"] = strim($_REQUEST["impose_fee_day2"]);
		$data["rate_cfg"] = intval($_REQUEST["rate_cfg"]);
		$data["enddate"] = intval($_REQUEST["enddate"]);
		$data["first_relief"] = floatval($_REQUEST["first_relief"]);
		
		$count = $GLOBALS["db"]->getOne("select count(*) from ".DB_PREFIX."debit_conf");
		if($count==0)
		{
			$GLOBALS['db']->autoExecute(DB_PREFIX."debit_conf",$data);
		}
		else	
		{
			$GLOBALS['db']->autoExecute(DB_PREFIX."debit_conf",$data,"UPDATE"," 1=1 ");
		}
		
		save_log(l("DEBIT_UPDATED"),1);		

		$this->success(L("UPDATE_SUCCESS"));
	}
}
?>