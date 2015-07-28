<?php
// +----------------------------------------------------------------------
// | Fanwe 方维p2p借贷系统
// +----------------------------------------------------------------------
// | Copyright (c) 2011 http://www.fanwe.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 云淡风轻(88522820@qq.com)
// +----------------------------------------------------------------------

class PeiziConfAction extends CommonAction{
	public function index()
	{
		$peiziconf = M("PeiziConf")->findAll();
		foreach($peiziconf as $k=>$v){
			if($peiziconf[$k]['type']==2){
				$money_list = $peiziconf[$k]['money_list'];
				list($beishu,$yue) = split ('[;]', $money_list);
				$peiziconf[$k]['beishu'] = $beishu ;
				$peiziconf[$k]['yue'] = $yue;
			}
		}
		$contract = M("Contract")->where("is_effect=1 and is_delete=0")->findAll();
		$this->assign("contract",$contract);
		$this->assign("peiziconf",$peiziconf);
		$this->display();
	}

	public function update()
	{
		B('FilterString');
		$data = M(MODULE_NAME)->create ();
		//开始验证有效性
		//$this->assign("jumpUrl",u(MODULE_NAME."/edit",array("id"=>$data['id'])));
		print_r($data);die;
		// 更新数据
		$list=M(MODULE_NAME)->save ($data);
		if (false !== $list) {
			//成功提示
			save_log($log_info.L("UPDATE_SUCCESS"),1);
			$this->success(L("UPDATE_SUCCESS"));
		} else {
			//错误提示
			save_log($log_info.L("UPDATE_FAILED"),0);
			$this->error(L("UPDATE_FAILED"),0,$log_info.L("UPDATE_FAILED"));
		}
	}
	
	
	public function rate()
	{
		$everwin_rate = M("PeiziEverwinRateList")->find();
		$this->assign("everwin_rate",$everwin_rate);
		$this->display();
	}
	
	
	public function update_rate()
	{
	
	}
}
?>