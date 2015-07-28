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
			$list_sql = 'SELECT count(DISTINCT (CONCAT(d.id ,"_",dr.u_key))) as rs_count,sum(dr.self_money) as need_transfer_benjin,sum(dr.repay_money - dr.self_money) as need_transfer_lixi FROM '.DB_PREFIX.'deal_load_repay dr LEFT JOIN '.DB_PREFIX.'deal d ON d.id = dr.deal_id LEFT JOIN '.DB_PREFIX.'deal_load_transfer dlt ON dlt.deal_id = dr.deal_id and dlt.load_id=dr.load_id WHERE dr.has_repay =0 and d.repay_time_type =1 and dr.user_id='.intval($GLOBALS['user_info']['id']).' AND d.deal_status = 4 and (isnull(dlt.user_id) or dlt.status=0)  and (d.next_repay_time + + 24*3600 - 1) >'.TIME_UTC;

			$can_rs = $GLOBALS['db']->getRow($list_sql);
			$statics['need_transfer']= $can_rs['rs_count'];
			//待回收本金
			$statics['need_transfer_benjin']=$can_rs['need_transfer_benjin'];
			//待回收利息
			$statics['need_transfer_lixi']=$can_rs['need_transfer_lixi'];
			
			
			//转让中
			$list_sql = 'SELECT count(DISTINCT (CONCAT(d.id ,"_",dr.u_key))) as rs_count,sum(dr.self_money) as in_transfer_benjin,sum(dr.repay_money - dr.self_money) as in_transfer_lixi FROM '.DB_PREFIX.'deal_load_repay dr LEFT JOIN '.DB_PREFIX.'deal d ON d.id = dr.deal_id LEFT JOIN '.DB_PREFIX.'deal_load_transfer dlt ON dlt.deal_id = dr.deal_id and dlt.load_id=dr.load_id WHERE dr.has_repay =0 and d.repay_time_type =1 and dr.user_id='.intval($GLOBALS['user_info']['id']).' AND d.deal_status = 4 and dr.t_user_id = 0 and  dlt.status=1  and (d.next_repay_time + + 24*3600 - 1) >'.TIME_UTC;
			$in_rs = $GLOBALS['db']->getRow($list_sql);
			$statics['in_transfer']= $in_rs['rs_count'];
			//待回收本金
			$statics['in_transfer_benjin']= $in_rs['in_transfer_benjin'];
			//待回收利息
			$statics['in_transfer_lixi']= $in_rs['in_transfer_lixi'];
			
			//回收中
			$list_sql = 'SELECT count(DISTINCT (CONCAT(d.id ,"_",dr.u_key))) as rs_count,sum(dr.self_money) as inback_transfer_benjin,sum(dr.repay_money - dr.self_money) as inback_transfer_lixi FROM '.DB_PREFIX.'deal_load_repay dr LEFT JOIN '.DB_PREFIX.'deal d ON d.id = dr.deal_id LEFT JOIN '.DB_PREFIX.'deal_load_transfer dlt ON dlt.deal_id = dr.deal_id and dlt.load_id=dr.load_id WHERE dr.has_repay =0 and d.repay_time_type =1 AND d.deal_status = 4 and dlt.t_user_id = '.intval($GLOBALS['user_info']['id']).'  ';
			
			$inback_rs = $GLOBALS['db']->getRow($list_sql);
			$statics['inback_transfer']= $inback_rs['rs_count'];
			$statics['inback_transfer_benjin']= $inback_rs['inback_transfer_benjin'];
			$statics['inback_transfer_lixi']= $inback_rs['inback_transfer_lixi'];
			
			//已回收
			$list_sql = 'SELECT count(DISTINCT (CONCAT(d.id ,"_",dr.u_key))) as rs_count,sum(dr.repay_money) as hasback_transfer_all FROM '.DB_PREFIX.'deal_load_repay dr LEFT JOIN '.DB_PREFIX.'deal d ON d.id = dr.deal_id LEFT JOIN '.DB_PREFIX.'deal_load_transfer dlt ON dlt.deal_id = dr.deal_id and dlt.load_id=dr.load_id WHERE dr.has_repay =1 and d.repay_time_type =1 AND d.deal_status = 5 and dr.repay_time >=dlt.near_repay_time AND dlt.t_user_id = '.intval($GLOBALS['user_info']['id']).'  ';
			$hasback_rs = $GLOBALS['db']->getRow($list_sql);
			$statics['hasback_transfer']= $hasback_rs['rs_count'];
			$statics['hasback_transfer_all']= $hasback_rs['hasback_transfer_all'];
			
			
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
		
		$status = getUcToTransfer($id,$tid);
		
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
		$paypassword = strim(FW_DESPWD($_REQUEST['paypassword']));
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
		$deal_id = $GLOBALS['db']->getOne("SELECT deal_id FROM ".DB_PREFIX."deal_load_transfer WHERE id=".$id);
		if($deal_id==0){
			echo "不存在的债权"; die();
		}
		else{
			syn_deal_status($deal_id);
		}
		
		$condition = ' AND dlt.id='.$id.' AND d.deal_status >= 4 and d.is_effect=1 and d.is_delete=0  and d.repay_time_type =1 and  d.publish_wait=0 and (dlt.user_id='.$GLOBALS['user_info']['id'].' or dlt.t_user_id = '.$GLOBALS['user_info']['id'].') ';
		$union_sql = " LEFT JOIN ".DB_PREFIX."deal_load_transfer dlt ON dlt.deal_id = dl.deal_id ";
		
		/*$sql = 'SELECT dlt.id,dlt.deal_id,dlt.load_id,dlt.transfer_amount,dlt.near_repay_time,dlt.user_id,d.next_repay_time,d.last_repay_time,d.rate,d.repay_start_time,d.repay_time,dlt.load_money,dlt.id as dltid,dlt.status as tras_status,dlt.t_user_id,dlt.transfer_amount,dlt.create_time as tras_create_time,dlt.transfer_time,d.user_id as duser_id FROM '.DB_PREFIX.'deal_load dl LEFT JOIN '.DB_PREFIX.'deal d ON d.id = dl.deal_id '.$union_sql.' WHERE 1=1 '.$condition;
		
		$transfer = $GLOBALS['db']->getRow($sql);*/
		require APP_ROOT_PATH."/app/Lib/deal_func.php";
		$transfer = get_transfer($union_sql,$condition);
		
		if($transfer){
			/*//下个还款日
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
			$transfer['duser'] = get_user("user_name,real_name,idno,level_id",$transfer['duser_id']);*/
			$GLOBALS['tmpl']->assign('transfer',$transfer);
		}
		else{
			echo "不存在的债权"; die();	
		}
		
		$GLOBALS['tmpl']->assign('SITE_URL',str_replace(array("https://","http://"),"",SITE_DOMAIN));
		$GLOBALS['tmpl']->assign('SITE_TITLE',app_conf("SITE_TITLE"));
		$GLOBALS['tmpl']->assign('CURRENCY_UNIT',app_conf("CURRENCY_UNIT"));
		
		$contract = $GLOBALS['tmpl']->fetch("str:".get_contract($transfer['tcontract_id']));
		
		$GLOBALS['tmpl']->assign('contract',$contract);
		
		$GLOBALS['tmpl']->display("inc/uc/transfer_contact.html");
	}
}
?>