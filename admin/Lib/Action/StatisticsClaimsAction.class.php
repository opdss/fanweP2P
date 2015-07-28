<?php
// +----------------------------------------------------------------------
// | Fanwe 方维p2p借贷系统
// +----------------------------------------------------------------------
// | Copyright (c) 2011 http://www.fanwe.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 云淡风轻(88522820@qq.com)
// +----------------------------------------------------------------------

class StatisticsClaimsAction extends CommonAction{

    public function index() {
    	if(trim($_REQUEST['search'])=="do"){
	    	$start_time = trim($_REQUEST['start_time']);
	    	if($start_time==""){
	    		$this->error("请选择开始时间");	
	    		die();
	    	}
	    	$this->assign("start_time",$start_time);
	    	$end_time = trim($_REQUEST['end_time']);
	    	
	    	if($end_time==""){
	    		$this->error("请选择结束时间");
	    		die();
	    	}
	    	
	    	$this->assign("end_time",$end_time);
	    	
	    	$start_time = to_timespan($start_time,"Y-m-d");
	    	$end_time = to_timespan($end_time,"Y-m-d");
	    	
	    	if($end_time < $start_time){
	    		$this->error("结束时间必须大于开始时间");
	    		die();
	    	}
	    	
	    	$now_time = to_timespan(to_date(TIME_UTC,"Y-m-d"),"Y-m-d");
	    	
	    	if($end_time > $now_time){
	    		$end_time = $now_time;
	    	}
	    	
	    	//开始时间跟结束时间差多少天
	    	$day = ($end_time - $start_time) /24 /3600;
	    	
	    	
	    	$list = array();
	    	$day_time = $start_time;
	    	
	    	//标分类
	    	$deal_cate = load_auto_cache("cache_deal_cate");
	    	
	    	/*foreach($deal_cate as $kk=>$vv){
	    		if(strpos($vv['name'],"智能")!==false){
	    			unset($deal_cate[$kk]);
	    		}
	    	}*/
	    	$this->assign("deal_cate",$deal_cate);
	    	
	    	//获取改时间段内所有的 还款中和 已还清的贷款
	    	//$deals = $GLOBALS['db']->getAll("SELECT id FROM ".DB_PREFIX."deal where deal_status in(4,5) and is_effect=1 and is_delete=0 and publish_wait=0 AND success_time >= $start_time and  ((loantype=1 and (success_time + repay_time*31*24*3600) >=$end_time) or (loantype=0 and (success_time + (repay_time+1)*24*3600)>=$end_time))");
	    	$deals = $GLOBALS['db']->getAll("SELECT id FROM ".DB_PREFIX."deal where deal_status in(4,5) and is_effect=1 and is_delete=0 and publish_wait=0 AND success_time >= $start_time");
	    	
	    	$temp_deals = array();
	    	
	    	require_once APP_ROOT_PATH."app/Lib/common.php";
    		require_once APP_ROOT_PATH."app/Lib/deal.php";
    		require_once APP_ROOT_PATH."system/libs/user.php";
	    	
	    	for($i = 0 ; $i<=$day; $i++){
	    		$list[$i]['day'] = $day_time;
	    		//线上充值金额
	    		$list[$i]['online_pay'] = floatval($GLOBALS['db']->getOne("SELECT sum(deal_total_price) FROM ".DB_PREFIX."deal_order where pay_status=2 and type = 1 and is_delete = 0 and create_time between $day_time and $day_time+24*3600 and payment_id not in (SELECT id from ".DB_PREFIX."payment where class_name='Otherpay') "));
	    		//线下充值金额
	    		$list[$i]['below_pay'] = floatval($GLOBALS['db']->getOne("SELECT sum(deal_total_price) FROM ".DB_PREFIX."deal_order where pay_status=2 and type = 1 and is_delete = 0 and create_time between $day_time and $day_time+24*3600 and payment_id in (SELECT id from ".DB_PREFIX."payment where class_name='Otherpay') "));
	    		
	    		foreach($deal_cate as $kk=>$vv){
	    			//if(strpos($vv['name'],"智能")===false)
	    				$list[$i][$vv['id']]['borrow_amount'] = floatval($GLOBALS['db']->getOne("SELECT sum(borrow_amount) FROM ".DB_PREFIX."deal where deal_status=4 and is_delete = 0 and publish_wait = 0 and success_time between $day_time and $day_time+24*3600 and cate_id=".$vv['id']));
	    		}
	    		
	    		//投资总额[投标者]
	    		$list[$i]['load_amount'] = floatval($GLOBALS['db']->getOne("SELECT sum(money) FROM ".DB_PREFIX."deal_load where is_repay=0 and create_time between $day_time and $day_time+24*3600"));
	    		
	    		//已获利息总额[投标者]
	    		$list[$i]['load_lixi_amount'] = floatval($GLOBALS['db']->getOne("SELECT (sum(repay_money) - sum(self_money)) FROM ".DB_PREFIX."deal_load_repay where true_repay_time between $day_time and $day_time+24*3600"));
	    		
	    		//应付本金
	    		$list[$i]['benjin_amount'] = 0;
	    		//应付利息 
	    		$list[$i]['pay_lxi_amount'] = 0;
	    		//应付罚息
	    		$list[$i]['impose_amount'] = 0;
	    		
	    		//已付本金
	    		$list[$i]['has_repay_benjin_amount']=0;
	    		//已付利息
	    		$list[$i]['has_repay_lxi_amount']=0;
	    		//已付罚息
	    		$list[$i]['has_repay_impose_amount'] = 0;
	    		
	    		foreach($deals as $kk=>$vv){
	    			
	    			if(!isset($temp_deals[$vv['id']])){
	    				$temp_deals[$vv['id']]['deal'] = get_deal($vv['id']);
	    				$temp_deals[$vv['id']]['loan'] = get_deal_load_list($temp_deals[$vv['id']]['deal']);
	    			}
	    			
	    			foreach($temp_deals[$vv['id']]['loan'] as $kkk=>$vvv){
	    				
	    				//如果刚好等于传入的时间就开始计算
	    				if($vvv['true_repay_time'] >= $day_time && $vvv['true_repay_time'] <= ($day_time+24*3600-1) || $vvv['repay_day'] == $day_time){
	    					
	    						if($temp_deals[$vv['id']]['deal']['month_repay_money'] > 0 || $temp_deals[$vv['id']]['deal']['last_month_repay_money'] > 0){
	    							if($temp_deals[$vv['id']]['deal']['loantype'] ==0){
	    								$benj = get_benjin($kkk,count($temp_deals[$vv['id']]['loan']),$temp_deals[$vv['id']]['deal']['borrow_amount'],$temp_deals[$vv['id']]['deal']['month_repay_money'],$temp_deals[$vv['id']]['deal']['rate']);
			    						
			    						$list[$i]['benjin_amount'] += $benj;
	    								$list[$i]['pay_lxi_amount'] += $temp_deals[$vv['id']]['deal']['month_repay_money'] - $benj;
	    							}
			    					elseif($temp_deals[$vv['id']]['deal']['loantype'] ==1)
			    					{
			    						if($kkk+1==count($temp_deals[$vv['id']]['loan'])){
			    							$list[$i]['benjin_amount'] += $temp_deals[$vv['id']]['deal']['borrow_amount'];
			    							$list[$i]['pay_lxi_amount'] += $temp_deals[$vv['id']]['deal']['last_month_repay_money'] - $list[$i]['benjin_amount'];
			    						}
			    						else{
			    							$list[$i]['pay_lxi_amount'] += $temp_deals[$vv['id']]['deal']['month_repay_money'];
			    						}
			    					}
			    					elseif($temp_deals[$vv['id']]['deal']['loantype'] ==2){
			    						if($kkk+1==count($temp_deals[$vv['id']]['loan'])){
			    							$list[$i]['benjin_amount'] += $temp_deals[$vv['id']]['deal']['borrow_amount'];
			    							$list[$i]['pay_lxi_amount'] += $temp_deals[$vv['id']]['deal']['last_month_repay_money'] -  $temp_deals[$vv['id']]['deal']['borrow_amount'];
			    						}
			    					}
			    					
		    						$list[$i]['impose_amount'] += $vvv['impose_money'];
		    						
			    					if($vvv['has_repay']==1){
			    						if($temp_deals[$vv['id']]['deal']['loantype'] ==0){
			    							$benj = get_benjin($kkk,count($temp_deals[$vv['id']]['loan']),$temp_deals[$vv['id']]['deal']['borrow_amount'],$temp_deals[$vv['id']]['deal']['month_repay_money'],$temp_deals[$vv['id']]['deal']['rate']);
			    							$list[$i]['has_repay_benjin_amount'] +=$benj;
			    							
			    							$list[$i]['has_repay_lxi_amount'] += $temp_deals[$vv['id']]['deal']['month_repay_money'] - $benj;
			    						}
			    						elseif($temp_deals[$vv['id']]['deal']['loantype'] ==1)
			    						{
			    							if($kkk+1==count($temp_deals[$vv['id']]['loan'])){
			    									$list[$i]['has_repay_benjin_amount'] +=$temp_deals[$vv['id']]['deal']['borrow_amount'];
			    									$list[$i]['has_repay_lxi_amount'] += $temp_deals[$vv['id']]['deal']['last_month_repay_money'] - $list[$i]['benjin_amount'];
			    							}
			    							else{
			    								$list[$i]['has_repay_lxi_amount'] += $temp_deals[$vv['id']]['deal']['month_repay_money'];
			    							}
			    						}
			    						elseif($temp_deals[$vv['id']]['deal']['loantype'] ==2){
			    							if($kkk+1==count($temp_deals[$vv['id']]['loan'])){
				    							$list[$i]['has_repay_benjin_amount'] += $temp_deals[$vv['id']]['deal']['borrow_amount'];
				    							$list[$i]['has_repay_lxi_amount'] += $temp_deals[$vv['id']]['deal']['last_month_repay_money'] -  $temp_deals[$vv['id']]['deal']['borrow_amount'];
				    						}
			    						}
			    						$list[$i]['has_repay_impose_amount'] +=$vvv['impose_money']; 
			    						
			    					}
			    					
	    						}
	    				}
	    			}
	    		}
	    		
	    		//待还本金
	    		$list[$i]['wait_repay_benjin_amount'] = $list[$i]['benjin_amount']-$list[$i]['has_repay_benjin_amount'];
	    		//待还利息
	    		$list[$i]['wait_repay_lxi_amount'] = $list[$i]['pay_lxi_amount']-$list[$i]['has_repay_lxi_amount'];
	    		//待还罚息
	    		$list[$i]['wait_repay_impose_amount'] = $list[$i]['impose_amount'] - $list[$i]['has_repay_impose_amount'] ;
	    		
	    		
	    		//申请提现总额
	    		$list[$i]['carry'] = floatval($GLOBALS['db']->getOne("SELECT sum(money) FROM ".DB_PREFIX."user_carry where create_time between $day_time and $day_time+24*3600 "));
	    		
	    		//成功提现金额
	    		$list[$i]['suc_carry'] = floatval($GLOBALS['db']->getOne("SELECT sum(money) FROM ".DB_PREFIX."user_carry where status=1 and update_time between $day_time and $day_time+24*3600 "));
	    		
	    		
	    		$day_time +=24 * 3600;
	    	}
	    	
	    	//待投资资金
	    	$user_amount = M("User")->where("is_delete=0 AND is_effect=1")->sum("money");
			$this->assign("user_amount",$user_amount);
	    	
	    	$this->assign("list",$list);
    	}
    	$this->display();
    }
    
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
			$this->error('开始时间不能大于结束时间');
			exit;
		}
	
		$q_date_diff = 70;
		$this->assign("q_date_diff",$q_date_diff);
		//echo abs(to_timespan($map['end_time']) - to_timespan($map['start_time'])) / 86400 + 1;
		if ($q_date_diff > 0 && (abs(to_timespan($map['end_time']) - to_timespan($map['start_time'])) / 86400  + 1 > $q_date_diff)){
			$this->error("查询时间间隔不能大于  {$q_date_diff} 天");
			exit;
		}
	
		return $map;
	}	
	
    //债权转让
	public function change_account_total(){
		
		$map =  $this->com_search();
		
		foreach ( $map as $key => $val ) {
			//dump($key);
			if ((!is_array($val)) && ($val <> '')){
				$parameter .= "$key=" . urlencode ( $val ) . "&";
			}
		}
					
		$date_list = explode(",",date_in($map['start_time'],$map['end_time'],false));
		
		$sql = "select create_date as time,count(*) as transfernum,sum(transfer_amount) as transfermoney from ".DB_PREFIX."deal_load_transfer where create_date in (".date_in($map['start_time'],$map['end_time']).")  group by create_date ";
		$transfer_list = $GLOBALS['db']->getAll($sql);
		
		$sql = "select transfer_date as time,count(*) as successnum,sum(transfer_amount) as successmoney from ".DB_PREFIX."deal_load_transfer where transfer_date in (".date_in($map['start_time'],$map['end_time']).") group by transfer_date";
		$success_list = $GLOBALS['db']->getAll($sql);
		
		$sql = "select create_time_ymd as time,sum(money) as transferfeemoney from ".DB_PREFIX."site_money_log where type='17' and create_time_ymd in (".date_in($map['start_time'],$map['end_time']).") group by create_time_ymd";
		$transferfee_list = $GLOBALS['db']->getAll($sql);
		//echo $sql;
			
		$list = array();
		foreach($date_list as $k=>$v){
			$row = array();
			$row['date'] = $v;
			$row['债权转让笔数'] = 0;
			$row['债权转让金额'] = 0;
			$row['债权转让管理费'] = 0;
			$row['成功转让笔数'] = 0;
			$row['成功转让金额'] = 0;	
							
			foreach($transfer_list as $tk=>$tv){
				if ($tv['time'] == $v){
					$row['债权转让笔数'] = $tv['transfernum'];
					$row['债权转让金额'] = $tv['transfermoney'];
					break;
				}				
			}
			foreach($transferfee_list as $tfk=>$tfv){
				if ($tfv['time'] == $v){
					$row['债权转让管理费'] = $tfv['transferfeemoney'];
					break;
				}				
			}
							
			foreach($success_list as $sk=>$sv){
				if ($sv['time'] == $v){
					$row['成功转让笔数'] = $sv['successnum'];
					$row['成功转让金额'] = $sv['successmoney'];
					break;
				}				
			}
			
			$list[] = $row;
			
		}
		$this->assign("list",$list);
		//print_r($list);
		require('./admin/Tpl/default/Common/js/flash/php-ofc-library/open-flash-chart.php');
		
		$total_array=array(
				array(
					array('债权转让笔数','date','债权转让笔数'),
					array('债权转让金额','date','债权转让金额'),
					array('债权转让管理费','date','债权转让管理费'),
					array('成功转让笔数','date','成功转让笔数'),
					array('成功转让金额','date','成功转让金额')
				),
		);
		
		$chart_list=$this->get_jx_json_all($list,$total_array);
		$this->assign("chart_list",$chart_list);
		//dump($chart_list);
		
		$this->display();		
	}
	
}
?>