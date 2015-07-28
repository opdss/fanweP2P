<?php
// +----------------------------------------------------------------------
// | Fanwe 方维p2p借贷系统
// +----------------------------------------------------------------------
// | Copyright (c) 2011 http://www.fanwe.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 云淡风轻(88522820@qq.com)
// +----------------------------------------------------------------------

class SchemeAction extends CommonAction{
	public function index()
	{
		$everwin = M("PeiziScheme")->find();
		$this->assign("everwin",$everwin);
		$this->display();
	}
	
	public function rate()
	{
		$everwin_rate = M("PeiziSchemeRateList")->find();
		$this->assign("everwin_rate",$everwin_rate);
		$this->display();
	}
	
	public function update_index()
	{
		
	}
	
	public function update_rate()
	{
	
	}
}
?>