<?php
// +----------------------------------------------------------------------
// | Fanwe 方维p2p借贷系统
// +----------------------------------------------------------------------
// | Copyright (c) 2011 http://www.fanwe.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 云淡风轻(88522820@qq.com)
// +----------------------------------------------------------------------

class WeekwinAction extends CommonAction{
	public function index()
	{
		$everwin = M("PeiziWeekwin")->find();
		$this->assign("everwin",$everwin);
		$this->display();
	}
	
	public function update_index()
	{
		
	}
	
}
?>