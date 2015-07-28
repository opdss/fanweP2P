<?php
// +----------------------------------------------------------------------
// | Fanwe 方维p2p借贷系统
// +----------------------------------------------------------------------
// | Copyright (c) 2011 http://www.fanwe.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 云淡风轻(88522820@qq.com)
// +----------------------------------------------------------------------

require APP_ROOT_PATH.'app/Lib/uc.php';
class uc_investModule extends SiteBaseModule
{
	public function index(){
		$this->getlist("index");
	}
	public function invite(){
		$this->getlist("invite");
	}
	public function flow(){
		$this->getlist("flow");
	}
	public function ing(){
		$this->getlist("ing");
	}
	public function over(){
		$this->getlist("over");
	}
	public function bad(){
		$this->getlist("bad");
		
	}
	
    private function getlist($mode = "index") {
    	
    	$result = getInvestList($mode,intval($GLOBALS['user_info']['id']),intval($_REQUEST['p']));
    	
    	$list = $result['list'];
    	foreach($list as $k=>$v){
    		//当为天的时候
			if($v['repay_time_type'] == 0){
				$true_repay_time = 1;
			}
			else{
				$true_repay_time = $v['repay_time'];
			}
			
			$deal['borrow_amount'] = $v['u_load_money'];
			$deal['rate'] = $v['rate'];
			$deal['loantype'] = $v['loantype'];
			$deal['repay_time'] = $v['repay_time'];
	    	$deal['repay_time_type'] = $v['repay_time_type'];
	    	$deal['repay_start_time'] = $v['repay_start_time'];
			
			$deal_repay_rs = deal_repay_money($deal);
			
    		$v['interest_amount'] = $deal_repay_rs['month_repay_money'];
				
    		$list[$k] = $v;
    	}
    	$count = $result['count'];
    	
    	$GLOBALS['tmpl']->assign("list",$list);
    	
    	$page = new Page($count,app_conf("PAGE_SIZE"));   //初始化分页对象
    	$p  =  $page->show();
    	$GLOBALS['tmpl']->assign('pages',$p);
    	
		$GLOBALS['tmpl']->assign('user_id', $GLOBALS['user_info']['id']);
		
    	$GLOBALS['tmpl']->assign("page_title",$GLOBALS['lang']['UC_INVEST']);
    		
    	$GLOBALS['tmpl']->assign("inc_file","inc/uc/uc_invest.html");
    	$GLOBALS['tmpl']->display("page/uc.html");
    }
    
public function refdetail(){
		//277
    	$user_id = $GLOBALS['user_info']['id'];
		$id = intval($_REQUEST['id']);
		$load_id = intval($_REQUEST['load_id']);
		require APP_ROOT_PATH."app/Lib/deal.php";
		$deal = get_deal($id);
		if(!$deal || $deal['deal_status']<4){
			showErr("无法查看，可能有以下原因！<br>1。借款不存在<br>2。借款被删除<br>3。借款未成功");
		}
		$GLOBALS['tmpl']->assign('deal',$deal);
				
		//获取本期的投标记录
		$temp_user_load = $GLOBALS['db']->getRow("SELECT dl.id,dl.deal_id,dl.user_id,dl.money,dlt.t_user_id FROM ".DB_PREFIX."deal_load dl left join ".DB_PREFIX."deal_load_transfer dlt on dlt.load_id = dl.id WHERE dl.deal_id=".$id." and dl.id=".$load_id);
		
		//print_r("SELECT id,deal_id,user_id,money FROM ".DB_PREFIX."deal_load  WHERE deal_id=".$id." and id=".$load_id);die;
		
		$user_load_ids = array();
		if($temp_user_load){
			$u_key = $GLOBALS['db']->getOne("SELECT u_key FROM ".DB_PREFIX."deal_load_repay WHERE load_id=".$load_id." and (user_id=".$user_id." or t_user_id = ".$user_id.")");
			if(($temp_user_load["user_id"] == $user_id && intval($temp_user_load['t_user_id']) == 0 )|| $temp_user_load['t_user_id'] == $user_id){
				$temp_user_load['repay_start_time'] = $deal['repay_start_time'];
				$temp_user_load['repay_time'] = $deal['repay_time'];
				$temp_user_load['rate'] = $deal['rate'];
				$temp_user_load['u_key'] = $u_key;
				$temp_user_load['load'] = get_deal_user_load_list($deal, $user_id, -1 ,$u_key);
				$temp_user_load['impose_money'] =0;
				$temp_user_load['manage_fee'] = 0;
				$temp_user_load['repay_money'] = 0;
				$temp_user_load['manage_interest_money'] = 0;
				foreach($temp_user_load['load'] as $kk=>$vv){
					$temp_user_load['impose_money'] += $vv['impose_money'];
					$temp_user_load['manage_fee'] += $vv['manage_money'];
					$temp_user_load['repay_money'] += $vv['month_has_repay_money'];
					$temp_user_load['manage_interest_money'] += floatval($vv['manage_interest_money']);
					
					//预期收益
					$temp_user_load['load'][$kk]['yuqi_money']=format_price($vv['month_repay_money']-$vv['self_money'] - $vv['manage_money'] - $vv['manage_interest_money']);
					//实际收益
					if($vv['has_repay']==1){
						$temp_user_load['load'][$kk]['real_money']=format_price($vv['month_repay_money']- $vv['self_money']+$vv['impose_money'] - $vv['manage_money']- $vv['manage_interest_money']);
						
					}
				}
				$user_load_ids[] = $temp_user_load;
			}
		}
		
		$GLOBALS['tmpl']->assign('user_load_ids',$user_load_ids);
		
		$inrepay_info = $GLOBALS['db']->getRow("SELECT * FROM ".DB_PREFIX."deal_inrepay_repay WHERE deal_id=$id");
		$GLOBALS['tmpl']->assign("inrepay_info",$inrepay_info);
		
		$GLOBALS['tmpl']->assign("load_id",$load_id);
		$GLOBALS['tmpl']->assign("page_title","我的回款");
		$GLOBALS['tmpl']->assign("inc_file","inc/uc/uc_invest_refdetail.html");
		$GLOBALS['tmpl']->display("page/uc.html");	
    }
    
    public function mrefdetail(){
    	$user_id = $GLOBALS['user_info']['id'];
    	$id = intval($_REQUEST['id']);
    	$load_id = intval($_REQUEST['load_id']);
    	require APP_ROOT_PATH."app/Lib/deal.php";
    	$deal = get_deal($id);
    	if(!$deal || $deal['deal_status']<4){
    		showErr("无法查看，可能有以下原因！<br>1。借款不存在<br>2。借款被删除<br>3。借款未成功");
    	}
    	$GLOBALS['tmpl']->assign('deal',$deal);
    
    	$deal_load_list = get_deal_load_list($deal);
    
    	//获取本期的投标记录
		$temp_user_load = $GLOBALS['db']->getRow("SELECT id,deal_id,user_id,money FROM ".DB_PREFIX."deal_load WHERE deal_id=".$id." and id=".$load_id." and user_id=".$user_id);
		
		$user_load_ids = array();
		if($temp_user_load){
			$u_key = $GLOBALS['db']->getOne("SELECT u_key FROM ".DB_PREFIX."deal_load_repay WHERE load_id=".$load_id." and user_id=".$user_id);
			if($temp_user_load['user_id'] == $user_id){
				$temp_user_load['repay_start_time'] = $deal['repay_start_time'];
				$temp_user_load['repay_time'] = $deal['repay_time'];
				$temp_user_load['rate'] = $deal['rate'];
				$temp_user_load['u_key'] = $u_key;
				$temp_user_load['load'] = get_deal_user_load_list($deal, $user_id, -1 ,$u_key);
				$temp_user_load['impose_money'] =0;
				$temp_user_load['manage_fee'] = 0;
				$temp_user_load['repay_money'] = 0;
				$temp_user_load['manage_interest_money'] = 0;
				foreach($temp_user_load['load'] as $kk=>$vv){
					$temp_user_load['impose_money'] += $vv['impose_money'];
					$temp_user_load['manage_fee'] += $vv['manage_money'];
					$temp_user_load['repay_money'] += $vv['month_has_repay_money'];
					$temp_user_load['manage_interest_money'] += $vv['manage_interest_money'];
					
					//预期收益
					$temp_user_load['load'][$kk]['yuqi_money']=format_price($vv['month_repay_money']-$vv['self_money'] - $vv['manage_money'] - $vv['manage_interest_money']);
					//实际收益
					if($vv['has_repay']==1){
						$temp_user_load['load'][$kk]['real_money']=format_price($vv['month_repay_money']- $vv['self_money']+$vv['impose_money'] - $vv['manage_money']- $vv['manage_interest_money']);
						
					}
				}
				$user_load_ids[] = $temp_user_load;
			}
		}
    
    	$GLOBALS['tmpl']->assign('user_load_ids',$user_load_ids);
    
    	$inrepay_info = $GLOBALS['db']->getRow("SELECT * FROM ".DB_PREFIX."deal_inrepay_repay WHERE deal_id=$id");
    	$GLOBALS['tmpl']->assign("inrepay_info",$inrepay_info);
    	
    	$GLOBALS['tmpl']->assign("load_id",$load_id);
    	$GLOBALS['tmpl']->assign("page_title","我的回款");
    	$GLOBALS['tmpl']->assign("inc_file","inc/uc/uc_invest_refdetail.html");
    	$GLOBALS['tmpl']->display("uc_invest_mrefdetail.html");
    }
}
?>