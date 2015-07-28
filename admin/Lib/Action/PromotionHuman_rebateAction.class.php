<?php
// +----------------------------------------------------------------------
// | Fanwe 方维p2p借贷系统
// +----------------------------------------------------------------------
// | Copyright (c) 2011 http://www.fanwe.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 云淡风轻(88522820@qq.com)
// +----------------------------------------------------------------------

class PromotionHuman_rebateAction extends CommonAction{
	public function index()
	{	
		$user_name  = trim($_REQUEST['user_name']);
		$condition = "1=1";
		
		if($user_name!=="" && $user_name!=0)
		$condition.=" and user_name like '%".$user_name."%' ";
		//获取所有pid
		$pid= $GLOBALS['db']->getAll("SELECT DISTINCT u.pid from ".DB_PREFIX."user u left join ".DB_PREFIX."user u_p on u.pid = u_p.id where u_p.user_type = 3 group by u.pid");

		$flatmap = array_map("array_pop",$pid);
		$pid=implode(',',$flatmap);
		
		if($pid)
			$condition.=" and id in (" .$pid. " )" ;
		
		$count = count($pid);
	
		if (! empty ( $_REQUEST ['listRows'] )) {
			$listRows = $_REQUEST ['listRows'];
		} else {
			$listRows = '';
		}
		
		$p = new Page ( $count, $listRows );
		if($count>0){
			$sql = "SELECT * FROM ".DB_PREFIX."user u WHERE  $condition order by id desc LIMIT ".($p->firstRow . ',' . $p->listRows);

			$list = $GLOBALS['db']->getAll($sql);
			
			foreach($list as $k=>$v){
				//邀请人数
				$list[$k]['p_count'] = $GLOBALS['db']->getOne("SELECT count(*) FROM ".DB_PREFIX."user where pid = ".$list[$k]['id']);
				//投资借款
				$list[$k]['sta'] = $GLOBALS['db']->getRow("SELECT sum(load_count) as invest_count,sum(load_money) as invest_money,sum(deal_count) as borrow_count,sum(borrow_amount) as borrow_money FROM ".DB_PREFIX."user u left join ".DB_PREFIX."user_sta us on u.id = us.user_id  WHERE  u.pid = ".$list[$k]['id']);
				//投资返佣
				$list[$k]['invest_rebate'] = $GLOBALS['db']->getRow("select sum(manage_interest_money_rebate) as rebate_fee,sum(true_manage_interest_money_rebate) as true_rebate_fee from ".DB_PREFIX."deal_load_repay dlr left join ".DB_PREFIX."user u on dlr.user_id = u.id where has_repay =1 and u.pid = ".$list[$k]['id']);
				$list[$k]['borrow_rebate'] = $GLOBALS['db']->getRow("select sum(manage_money_rebate) as rebate_fee,sum(true_manage_money_rebate) as true_rebate_fee from ".DB_PREFIX."deal_repay dl left join ".DB_PREFIX."user u on dl.user_id = u.id where has_repay =1 and u.pid = ".$list[$k]['id']);
				//借款
				//$list[$k]['borrow'] = $GLOBALS['db']->getOne("select sum(repay_money-self_money+impose_money) from ".DB_PREFIX."deal_load_repay where user_id = ".$list[$k]['id']);	
			}
			$this->assign("list",$list);
		}
	
		$page = $p->show();
		$this->assign ( "page", $page );
		$this->display();
	}
	
}
?>