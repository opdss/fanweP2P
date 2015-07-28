<?php
// +----------------------------------------------------------------------
// | Fanwe 方维p2p借贷系统
// +----------------------------------------------------------------------
// | Copyright (c) 2011 http://www.fanwe.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 云淡风轻(88522820@qq.com)
// +----------------------------------------------------------------------

require APP_ROOT_PATH.'app/Lib/uc.php';

class uc_transferModule extends SiteBaseModule
{
	public function __construct(){
		parent::__construct();
		
		if(ACTION_NAME =="index" || ACTION_NAME =="buys"){
			//可以转让
			$count_sql = 'SELECT count(dl.id) FROM '.DB_PREFIX.'deal_load dl LEFT JOIN '.DB_PREFIX.'deal d ON d.id = dl.deal_id LEFT JOIN '.DB_PREFIX.'deal_load_transfer dlt ON dlt.deal_id = dl.deal_id and dlt.load_id=dl.id WHERE 1=1 and d.is_effect=1 and d.is_delete=0 and d.loantype = 0 and d.repay_time_type =1 and d.publish_wait=0 and dl.user_id='.intval($GLOBALS['user_info']['id']).' AND d.deal_status = 4 and isnull(dlt.id) and d.next_repay_time - '.TIME_UTC.' + 24*3600 - 1 > 0 ';
			
			$rs_count = $GLOBALS['db']->getOne($count_sql);
			$statics['need_transfer']= $rs_count;
			//待回收本金
			$statics['need_transfer_benjin']= 0;
			//待回收利息
			$statics['need_transfer_lixi']= 0;
			
			if($statics['need_transfer'] > 0){
				$list_sql = 'SELECT dl.id as dlid,d.*,dl.money as load_money,dlt.id as dltid,dlt.status as tras_status,dlt.t_user_id,dlt.transfer_amount,dlt.create_time as tras_create_time FROM '.DB_PREFIX.'deal_load dl LEFT JOIN '.DB_PREFIX.'deal d ON d.id = dl.deal_id LEFT JOIN '.DB_PREFIX.'deal_load_transfer dlt ON dlt.deal_id = dl.deal_id and dlt.load_id=dl.id WHERE 1=1 and d.is_effect=1 and d.is_delete=0 and d.loantype = 0 and d.repay_time_type =1 and d.publish_wait=0 and dl.user_id='.intval($GLOBALS['user_info']['id']).' AND d.deal_status = 4 and isnull(dlt.id) and d.next_repay_time - '.TIME_UTC.' + 24*3600 - 1 > 0 ';
				$list = $GLOBALS['db']->getAll($list_sql);
				foreach($list as $k => $v){
					//最后还款日
					$list[$k]['final_repay_time'] = next_replay_month($v['repay_start_time'],$v['repay_time']);
					//剩余期数
					if(intval($v['last_repay_time']) > 0)
						$list[$k]['how_much_month'] = how_much_month($v['last_repay_time'],$list[$k]['final_repay_time']);
					else{
						$list[$k]['how_much_month'] = how_much_month($v['repay_start_time'],$list[$k]['final_repay_time']);
					}
					
					//本息还款金额
					$list[$k]['month_repay_money'] = pl_it_formula($v['load_money'],$v['rate']/12/100,$v['repay_time']);
					//剩余多少钱未回
					$list[$k]['all_must_repay_money'] = $list[$k]['month_repay_money'] * $list[$k]['how_much_month'];
					//剩余多少本金未回
					$list[$k]['left_benjin'] = get_benjin($v['repay_time']-$list[$k]['how_much_month']-1,$v['repay_time'],$v['load_money'],$list[$k]['month_repay_money'],$v['rate']);
					$statics['need_transfer_benjin'] += $list[$k]['left_benjin'];
					//剩多少利息
					$list[$k]['left_lixi'] = $list[$k]['all_must_repay_money'] - $list[$k]['left_benjin'];
					$statics['need_transfer_lixi'] += $list[$k]['left_lixi'];
				}
				unset($list);
				
			}
			
			//转让中
			$count_sql = 'SELECT count(dl.id) FROM '.DB_PREFIX.'deal_load dl LEFT JOIN '.DB_PREFIX.'deal d ON d.id = dl.deal_id LEFT JOIN '.DB_PREFIX.'deal_load_transfer dlt ON dlt.deal_id = dl.deal_id and dlt.load_id=dl.id WHERE 1=1 and d.is_effect=1 and d.is_delete=0 and d.loantype = 0 and d.repay_time_type =1 and d.publish_wait=0 and dl.user_id='.intval($GLOBALS['user_info']['id']).' AND d.deal_status = 4  AND dlt.status = 1 and dlt.user_id >0 and dlt.t_user_id=0';
			$rs_count = $GLOBALS['db']->getOne($count_sql);
			$statics['in_transfer']= $rs_count;
			//待回收本金
			$statics['in_transfer_benjin']= 0;
			//待回收利息
			$statics['in_transfer_lixi']= 0;
			if($statics['in_transfer'] > 0){
				$list_sql = 'SELECT dl.id as dlid,d.*,dl.money as load_money,dlt.id as dltid,dlt.status as tras_status,dlt.t_user_id,dlt.transfer_amount,dlt.create_time as tras_create_time FROM '.DB_PREFIX.'deal_load dl LEFT JOIN '.DB_PREFIX.'deal d ON d.id = dl.deal_id LEFT JOIN '.DB_PREFIX.'deal_load_transfer dlt ON dlt.deal_id = dl.deal_id and dlt.load_id=dl.id WHERE 1=1 and d.is_effect=1 and d.is_delete=0 and d.loantype = 0 and d.repay_time_type =1 and d.publish_wait=0 and dl.user_id='.intval($GLOBALS['user_info']['id']).' AND d.deal_status = 4 AND dlt.status = 1 and dlt.user_id >0 and dlt.t_user_id=0';
				$list = $GLOBALS['db']->getAll($list_sql);
				foreach($list as $k => $v){
					//最后还款日
					$list[$k]['final_repay_time'] = next_replay_month($v['repay_start_time'],$v['repay_time']);
					//剩余期数
					if(intval($v['last_repay_time']) > 0)
						$list[$k]['how_much_month'] = how_much_month($v['last_repay_time'],$list[$k]['final_repay_time']);
					else{
						$list[$k]['how_much_month'] = how_much_month($v['repay_start_time'],$list[$k]['final_repay_time']);
					}
					
					//本息还款金额
					$list[$k]['month_repay_money'] = pl_it_formula($v['load_money'],$v['rate']/12/100,$v['repay_time']);
					//剩余多少钱未回
					$list[$k]['all_must_repay_money'] = $list[$k]['month_repay_money'] * $list[$k]['how_much_month'];
					//剩余多少本金未回
					$list[$k]['left_benjin'] = get_benjin($v['repay_time']-$list[$k]['how_much_month']-1,$v['repay_time'],$v['load_money'],$list[$k]['month_repay_money'],$v['rate']);
					$statics['in_transfer_benjin'] += $list[$k]['left_benjin'];
					//剩多少利息
					$list[$k]['left_lixi'] = $list[$k]['all_must_repay_money'] - $list[$k]['left_benjin'];
					$statics['in_transfer_lixi'] += $list[$k]['left_lixi'];
				}
				unset($list);
				
			}
			
			//回收中
			$count_sql ='SELECT count(DISTINCT dlt.id) FROM '.DB_PREFIX.'deal_load_transfer dlt LEFT JOIN '.DB_PREFIX.'deal_load dl ON dlt.deal_id = dl.deal_id LEFT JOIN '.DB_PREFIX.'deal d ON d.id = dl.deal_id  WHERE 1=1 and d.is_effect=1 and d.is_delete=0 and d.loantype = 0 and d.repay_time_type =1 and d.publish_wait=0 and dlt.t_user_id='.intval($GLOBALS['user_info']['id']).' AND d.deal_status = 4  and dlt.status = 1 ';
			$rs_count = $GLOBALS['db']->getOne($count_sql);
			$statics['inback_transfer']= $rs_count;
			$statics['inback_transfer_benjin']= 0;
			$statics['inback_transfer_lixi']= 0;
			if($statics['inback_transfer'] > 0){
				$list_sql = 'SELECT dl.id as dlid,d.*,dl.money as load_money,dlt.id as dltid,dlt.status as tras_status,dlt.t_user_id,dlt.transfer_amount,dlt.create_time as tras_create_time,dlt.transfer_time FROM '.DB_PREFIX.'deal_load dl LEFT JOIN '.DB_PREFIX.'deal d ON d.id = dl.deal_id LEFT JOIN '.DB_PREFIX.'deal_load_transfer dlt ON dlt.deal_id = dl.deal_id WHERE 1=1 and d.is_effect=1 and d.is_delete=0 and d.loantype = 0 and d.repay_time_type =1 and d.publish_wait=0 and dlt.t_user_id='.intval($GLOBALS['user_info']['id']).' AND d.deal_status = 4';
				$list = $GLOBALS['db']->getAll($list_sql);
				foreach($list as $k => $v){
					//最后还款日
					$list[$k]['final_repay_time'] = next_replay_month($v['repay_start_time'],$v['repay_time']);
					//剩余期数
					if(intval($v['last_repay_time']) > 0)
						$list[$k]['how_much_month'] = how_much_month($v['last_repay_time'],$list[$k]['final_repay_time']);
					else{
						$list[$k]['how_much_month'] = how_much_month($v['repay_start_time'],$list[$k]['final_repay_time']);
					}
					
					//本息还款金额
					$list[$k]['month_repay_money'] = pl_it_formula($v['load_money'],$v['rate']/12/100,$v['repay_time']);
					//剩余多少钱未回
					$list[$k]['all_must_repay_money'] = $list[$k]['month_repay_money'] * $list[$k]['how_much_month'];
					//剩余多少本金未回
					$list[$k]['left_benjin'] = get_benjin($v['repay_time']-$list[$k]['how_much_month']-1,$v['repay_time'],$v['load_money'],$list[$k]['month_repay_money'],$v['rate']);
					$statics['inback_transfer_benjin'] += $list[$k]['left_benjin'];
					
					//剩多少利息
					$list[$k]['left_lixi'] = $list[$k]['all_must_repay_money'] - $list[$k]['left_benjin'];
					$statics['inback_transfer_lixi'] += $list[$k]['left_lixi'];
					
				}
				unset($list);
			}
			
			//已回收
			$count_sql ='SELECT count(dl.id) FROM '.DB_PREFIX.'deal_load dl LEFT JOIN '.DB_PREFIX.'deal d ON d.id = dl.deal_id LEFT JOIN '.DB_PREFIX.'deal_load_transfer dlt ON dlt.deal_id = dl.deal_id WHERE 1=1 and d.is_effect=1 and d.is_delete=0 and d.loantype = 0 and d.repay_time_type =1 and d.publish_wait=0 and dlt.t_user_id='.intval($GLOBALS['user_info']['id']).' AND d.deal_status = 5';
			$rs_count = $GLOBALS['db']->getOne($count_sql);
			$statics['hasback_transfer']= $rs_count;
			$statics['hasback_transfer_all']= 0;
			if($statics['hasback_transfer'] > 0){
				$list_sql = 'SELECT dl.id as dlid,d.*,dl.money as load_money,dlt.id as dltid,dlt.status as tras_status,dlt.t_user_id,dlt.transfer_amount,dlt.create_time as tras_create_time,dlt.transfer_time,dlt.near_repay_time FROM '.DB_PREFIX.'deal_load dl LEFT JOIN '.DB_PREFIX.'deal d ON d.id = dl.deal_id LEFT JOIN '.DB_PREFIX.'deal_load_transfer dlt ON dlt.deal_id = dl.deal_id WHERE 1=1 and d.is_effect=1 and d.is_delete=0 and d.loantype = 0 and d.repay_time_type =1 and d.publish_wait=0 and dlt.t_user_id='.intval($GLOBALS['user_info']['id']).' AND d.deal_status = 5';
				$list = $GLOBALS['db']->getAll($list_sql);
				foreach($list as $k => $v){
					//最后还款日
					$list[$k]['final_repay_time'] = next_replay_month($v['repay_start_time'],$v['repay_time']);
					//剩余期数
					
					$list[$k]['how_much_month'] = how_much_month($v['near_repay_time'],$list[$k]['final_repay_time']) + 1;
					
					//本息还款金额
					$list[$k]['month_repay_money'] = pl_it_formula($v['load_money'],$v['rate']/12/100,$v['repay_time']);
					//剩余多少钱未回
					$list[$k]['all_must_repay_money'] = $list[$k]['month_repay_money'] * $list[$k]['how_much_month'];
					$statics['hasback_transfer_all'] += $list[$k]['all_must_repay_money'];
					//剩余多少本金未回
					//$list[$k]['left_benjin'] = get_benjin($v['repay_time']-$list[$k]['how_much_month']-1,$v['repay_time'],$v['load_money'],$list[$k]['month_repay_money'],$v['rate']);
					
					//剩多少利息
					//$list[$k]['left_lixi'] = $list[$k]['all_must_repay_money'] - $list[$k]['left_benjin'];
					
					
				}
				unset($list);
			}
			
			
			$GLOBALS['tmpl']->assign("statics",$statics);
		}
	}
	
	
	
	public function index()
	{
		$page = intval($_REQUEST['p']);
		$status = intval($_REQUEST['status']);
		$result = getUcTransferList($page,$status);
		
		if ($result['count'] > 0){
			$page = new Page($result['count'],app_conf("PAGE_SIZE"));   //初始化分页对象
			$p  =  $page->show();
			$GLOBALS['tmpl']->assign('pages',$p);
			$GLOBALS['tmpl']->assign('list',$result['list']);
		}
		
		$GLOBALS['tmpl']->assign("page_title",$GLOBALS['lang']['UC_TRANSFER']);
		$GLOBALS['tmpl']->assign("inc_file","inc/uc/uc_transfer_index.html");
		$GLOBALS['tmpl']->assign('status',$status);
				
		$GLOBALS['tmpl']->display("page/uc.html");
	}
	
	
	
	public function buys()
	{
		$page = intval($_REQUEST['p']);
		$status = intval($_REQUEST['status']);
		
		if($page==0)
			$page = 1;
			
		$result = getUcTransferBuys($page,$status);
		
		if ($result['count'] > 0){
			$page = new Page($result['count'],app_conf("PAGE_SIZE"));   //初始化分页对象
			$p  =  $page->show();
			$GLOBALS['tmpl']->assign('pages',$p);
			$GLOBALS['tmpl']->assign('list',$result['list']);
		}
		
		$GLOBALS['tmpl']->assign("page_title",$GLOBALS['lang']['UC_TRANSFER']);
		$GLOBALS['tmpl']->assign("inc_file","inc/uc/uc_transfer_index.html");
		$GLOBALS['tmpl']->assign('status',$status);
		
		$GLOBALS['tmpl']->display("page/uc.html");
	}
	
	
	public function to_transfer(){
		$id = intval($_REQUEST['id']);
		$tid = intval($_REQUEST['tid']);
		
		
		$status = getUcToTransfer($id);
		
		if($status['status'] ==0){
			echo $status['show_err']; die();
		}else{
			$GLOBALS['tmpl']->assign('transfer',$status['transfer']);
				
			$GLOBALS['tmpl']->display("inc/uc/ajax_to_transfer.html");
		}		
	}
	
	/**
	 * 执行转让
	 */
	public function do_transfer(){
		$id = intval($_REQUEST['dlid']);
		$tid = intval($_REQUEST['dltid']);
		$paypassword = strim($_REQUEST['paypassword']);
		$transfer_money = floatval($_REQUEST['transfer_money']);
		
		$status = getUcDoTransfer($id,$tid,$paypassword,$transfer_money);
		
		if($status['status']==0){
			showErr($status['show_err'],1);
		}else{
			showSuccess($status['show_err'],1);
		}		
	}
	
	/**
	 * 撤销转让
	 */
	public function do_reback(){
		$dltid = intval($_REQUEST['dltid']);
		if($dltid==0){
			showErr("不存在的转让债权",1);
		}
		
		$GLOBALS['db']->query("UPDATE ".DB_PREFIX."deal_load_transfer SET status=0,callback_count = callback_count+1 where id=".$dltid." AND t_user_id=0 and user_id = ".intval($GLOBALS['user_info']['id']));
		
		if($GLOBALS['db']->affected_rows()){
			showSuccess("撤销操作成功",1);
		}
		else{
			showSuccess("撤销操作失败",1);
		}
	}
	
	public function contact(){
		$id = intval($_REQUEST['id']);
		if($id==0){
			echo "不存在的债权"; die();
		}
		
		//先执行更新借贷信息
		$deal_id = $GLOBALS['db']->getOne("SELECT deal_id FROM ".DB_PREFIX."deal_load WHERE id=".$id);
		if($deal_id==0){
			echo "不存在的债权"; die();
		}
		else{
			syn_deal_status($deal_id);
		}
		
		$condition = ' AND dlt.id='.$id.' AND d.deal_status >= 4 and d.is_effect=1 and d.is_delete=0 and d.loantype = 0 and d.repay_time_type =1 and  d.publish_wait=0 and (dlt.user_id='.$GLOBALS['user_info']['id'].' or dlt.t_user_id = '.$GLOBALS['user_info']['id'].') ';
		$union_sql = " LEFT JOIN ".DB_PREFIX."deal_load_transfer dlt ON dlt.deal_id = dl.deal_id ";
		
		$sql = 'SELECT dlt.id,dlt.deal_id,dlt.load_id,dlt.transfer_amount,dlt.near_repay_time,dlt.user_id,d.next_repay_time,d.last_repay_time,d.rate,d.repay_start_time,d.repay_time,dlt.load_money,dlt.id as dltid,dlt.status as tras_status,dlt.t_user_id,dlt.transfer_amount,dlt.create_time as tras_create_time,dlt.transfer_time,d.user_id as duser_id FROM '.DB_PREFIX.'deal_load dl LEFT JOIN '.DB_PREFIX.'deal d ON d.id = dl.deal_id '.$union_sql.' WHERE 1=1 '.$condition;
		
		$transfer = $GLOBALS['db']->getRow($sql);
		
		if($transfer){
			//下个还款日
			$transfer['next_repay_time_format'] = to_date($transfer['near_repay_time'],"Y 年 m 月 d 日");
			$transfer['near_repay_time_format'] = to_date(next_replay_month($transfer['near_repay_time']," -1 "),"Y 年 m 月 d 日");
			
			//什么时候开始借
			$transfer['repay_start_time_format']  = to_date($transfer['repay_start_time'],"Y 年 m 月 d 日");
			
			//还款日
			$transfer['final_repay_time'] = next_replay_month($transfer['repay_start_time'],$transfer['repay_time']);
			$transfer['final_repay_time_format'] = to_date($transfer['final_repay_time'],"Y 年 m 月 d 日");
			
			//剩余期数
			$transfer['how_much_month'] = how_much_month($transfer['near_repay_time'],$transfer['final_repay_time']) +1;
			
			//本息还款金额
			$transfer['month_repay_money'] = pl_it_formula($transfer['load_money'],$transfer['rate']/12/100,$transfer['repay_time']);
			$transfer['month_repay_money_format'] = format_price($transfer['month_repay_money']);
			
			//剩余多少钱未回
			$transfer['all_must_repay_money'] = $transfer['month_repay_money'] * $transfer['how_much_month'];
			$transfer['all_must_repay_money_format'] = format_price($transfer['all_must_repay_money']);
			
			//剩余多少本金未回
			$transfer['left_benjin'] = get_benjin($transfer['repay_time']-$transfer['how_much_month']-1,$transfer['repay_time'],$transfer['load_money'],$transfer['month_repay_money'],$transfer['rate']);
			$transfer['left_benjin_format'] = format_price($transfer['left_benjin']);
			//剩多少利息
			$transfer['left_lixi'] = $transfer['all_must_repay_money'] - $transfer['left_benjin'];
			$transfer['left_lixi_format'] = format_price($transfer['left_lixi']);
			
			//投标价格
			$transfer['load_money_format'] =  format_price($transfer['load_money']);
			
			//转让价格
			$transfer['transfer_amount_format'] =  format_price($transfer['transfer_amount']);
			
			//转让管理费
			$transfer_fee = $transfer['transfer_amount']*(float)app_conf("USER_LOAD_TRANSFER_FEE");
			if($transfer_fee!=0)
				$transfer_fee = $transfer_fee / 100;
			$transfer['transfer_fee_format'] = format_price($transfer_fee);
			
			//转让收益
			$transfer['transfer_income_format'] =  format_price($transfer['all_must_repay_money']-$transfer['transfer_amount']);
			
			if($transfer['tras_create_time'] !=""){
				$transfer['tras_create_time_format'] =  to_date($transfer['tras_create_time'],"Y 年 m 月 d 日");
			}
			
			$transfer['transfer_time_format'] =  to_date($transfer['transfer_time'],"Y 年 m 月 d 日");
			
			$transfer['user'] = get_user("user_name,real_name,idno,level_id",$transfer['user_id']);
			$transfer['tuser'] = get_user("user_name,real_name,idno,level_id",$transfer['t_user_id']);
			$transfer['duser'] = get_user("user_name,real_name,idno,level_id",$transfer['duser_id']);
			$GLOBALS['tmpl']->assign('transfer',$transfer);
		}
		else{
			echo "不存在的债权"; die();	
		}
		
		$contract = $GLOBALS['tmpl']->fetch("str:".app_conf("TCONTRACT"));
		
		$GLOBALS['tmpl']->assign('contract',$contract);
		
		$GLOBALS['tmpl']->display("inc/uc/transfer_contact.html");
	}
}
?>