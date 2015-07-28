<?php
// +----------------------------------------------------------------------
// | Fanwe 方维p2p借贷系统
// +----------------------------------------------------------------------
// | Copyright (c) 2011 http://www.fanwe.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 云淡风轻(88522820@qq.com)
// +----------------------------------------------------------------------

class PromotionHumanAction extends CommonAction{
	public function index()
	{	
		$user_name  = trim($_REQUEST['user_name']);
		
		if (isset ( $_REQUEST ['_order'] )) {
			$sorder = $_REQUEST ['_order'];
		}
		else{
			$sorder = "id";
		}
		switch($sorder){
			case "user_name":
				$order ="u.".$sorder;
				break;
			case "email ":
				$order ="u.email";
				break;
			case "status":
				$order ="u.pid";
				break;
			case "humans":
				$order ="u.pid";
				break;
			case "referer_memo":
				$order ="u.referer_memo";
				break;
			default :
				$order ="u.".$sorder;
				break;
		}
		
		
		
		//排序方式默认按照倒序排列
		//接受 sost参数 0 表示倒序 非0都 表示正序
		if (isset ( $_REQUEST ['_sort'] )) {
			$sort = $_REQUEST ['_sort'] ? 'asc' : 'desc';
		}
		else{
			$sort = "desc";
		}
		
		$condition = " u.user_type in(0,1) AND r.pid > 0 ";
		
		if($user_name!=="" && $user_name!=0)
			$condition.=" and u.user_name like '%".$user_name."%' ";
		
		
		
		$sql_count = "SELECT count(DISTINCT u.id) FROM ".DB_PREFIX."user u LEFT JOIN ".DB_PREFIX."user r On r.pid =u.id  left join ".DB_PREFIX."user_sta us on u.id = us.user_id  WHERE  $condition ";
		$count = $GLOBALS['db']->getOne($sql_count);

		if (! empty ( $_REQUEST ['listRows'] )) {
			$listRows = $_REQUEST ['listRows'];
		} else {
			$listRows = '';
		}
		
		$p = new Page ( $count, $listRows );
		if($count>0){
			$sql = "SELECT u.*,us.load_money,us.load_count,us.repay_deal_count,us.repay_amount FROM ".DB_PREFIX."user u LEFT JOIN ".DB_PREFIX."user r On r.pid =u.id left join ".DB_PREFIX."user_sta us on u.id = us.user_id  WHERE  $condition GROUP BY u.id ORDER BY $order $sort   LIMIT ".($p->firstRow . ',' . $p->listRows);
			
			$list = $GLOBALS['db']->getAll($sql);
			foreach($list as $k=>$v){
				$list[$k]['pidcount'] = $GLOBALS['db']->getOne("SELECT count(*) FROM ".DB_PREFIX."user where pid = ".$v['id']);
				//提成
				$list[$k]['percentage'] = $GLOBALS['db']->getOne("SELECT sum(money) FROM ".DB_PREFIX."referrals WHERE rel_user_id=".$v['id']."  AND pay_time >0  ");
					
			}
			$this->assign("list",$list);
		}
	
		$page = $p->show();
		
		$sortImg = $sort; //排序图标
		$sortAlt = $sort == 'desc' ? l("ASC_SORT") : l("DESC_SORT"); //排序提示
		$sort = $sort == 'desc' ? 1 : 0; //排序方式
		
		$this->assign ( 'sort', $sort );
		$this->assign ( "page", $page );
		$this->display();
	}
	
	
	
	public function export_csv($page = 1)
	{
		set_time_limit(0);
		$limit = (($page - 1)*intval(app_conf("BATCH_PAGE_SIZE"))).",".(intval(app_conf("BATCH_PAGE_SIZE")));
	
		//定义条件
		$condition = " u.user_type in(0,1) AND r.pid > 0 ";
		if($user_name!=="" && $user_name!=0)
			$condition.=" and u.user_name like '%".$user_name."%' ";
		$sql = "SELECT u.*,us.load_money,us.load_count,us.repay_deal_count,us.repay_amount FROM ".DB_PREFIX."user u LEFT JOIN ".DB_PREFIX."user r On r.pid =u.id left join ".DB_PREFIX."user_sta us on u.id = us.user_id  WHERE  $condition GROUP BY u.id  LIMIT ".$limit;
		$list = $GLOBALS['db']->getAll($sql);
		
		foreach($list as $k=>$v){
			$list[$k]['pidcount'] = $GLOBALS['db']->getOne("SELECT count(*) FROM ".DB_PREFIX."user where pid = ".$v['id']);
			//提成
			$list[$k]['percentage'] = $GLOBALS['db']->getOne("SELECT sum(money) FROM ".DB_PREFIX."referrals WHERE rel_user_id=".$v['id']."  AND pay_time >0  ");
				
		}
		
		if($list)
		{
			register_shutdown_function(array(&$this, 'export_csv'), $page+1);
			$user_value = array('id'=>'""','id'=>'""','pidcount'=>'""','load_count'=>'""','load_money'=>'""','percentage'=>'""','repay_deal_count'=>'""','repay_amount'=>'""');
			if($page == 1)
				$content = iconv("utf-8","gbk","编号,推广人,推广人数,投资次数,投资总额,投资提成,还款次数,还款总额");
			$content = $content . "\n";
			foreach($list as $k=>$v)
			{
				$promotion_human = array();
				$promotion_human['id'] = iconv('utf-8','gbk','"' . $v['id'] . '"');
				$promotion_human['user_name'] = iconv('utf-8','gbk','"' . get_user_name_reals($v['id']) . '"');
				$promotion_human['pidcount'] = iconv('utf-8','gbk','"' . $v['pidcount'] . '"');
				$promotion_human['load_count'] = iconv('utf-8','gbk','"' . $v['load_count'] . '"');
				$promotion_human['load_money'] = iconv('utf-8','gbk','"' . format_price($v['load_money']) . '"');
				$promotion_human['percentage'] = iconv('utf-8','gbk','"' . format_price($v['percentage']) . '"');
				$promotion_human['repay_deal_count'] = iconv('utf-8','gbk','"' . $v['repay_deal_count'] . '"');
				$promotion_human['repay_amount'] = iconv('utf-8','gbk','"' . format_price($v['repay_amount']) . '"');
	
				$content .= implode(",", $promotion_human) . "\n";
			}
			header("Content-Disposition: attachment; filename=promotion_human.csv");
			echo $content;
		}
		else
		{
			if($page==1)
				$this->error(L("NO_RESULT"));
		}
	}
}
?>