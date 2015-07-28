<?php
/**
 * 等额本息接口
 */
if (isset($read_modules) && $read_modules == true)
{
    $module['class_name']    = 'loantype_0';
    //键值跟class_name后面的 _?对应
    $module['key']    = 0;
    //借口名称
    $module['name']    = "等额本息";
    //短名称
    $module['sub_name']    = "等额本息";
    //支持借款期限类型  数组 array  0[,1];  0天标   1月标
    $module['repay_time_type']    = array(1);
    
    return $module;
}

require_once APP_ROOT_PATH."system/libs/loantype.php";
class loantype_0 implements loantype{
	/**
	 * 是否最后一起才还款
	 */
	function is_last_repay(){
		return false;
	}
	/**
	 * 还多少钱 
	 */
	function deal_repay_money($deal){
		//月还本息
		$return['month_repay_money'] = pl_it_formula($deal['borrow_amount'],$deal['rate']/12/100,$deal['repay_time']);
		//实际还多少钱 
		$return['remain_repay_money'] = round($return['month_repay_money'] * $deal['repay_time'],2);
		//最后一期还款本息
		$return['last_month_repay_money'] = $return['remain_repay_money'] - round($return['month_repay_money'],2)*($deal['repay_time']-1);
		
		//是否最后一期才算罚息
		$return['is_check_impose'] = false;
		return $return;
	}
	
	//还款还款计划
	function make_repay_plan($deal){
		$true_repay_time = $deal['repay_time'];
		$repay_day = $deal['repay_start_time'];
		$has_use_self_money = 0;
		$list = array();
		for($i=0;$i<$true_repay_time;$i++){
			$load_repay = array();
			$load_repay['repay_time'] = $repay_day =  next_replay_month($repay_day);
				
			$load_repay['repay_date'] = to_date($load_repay['repay_time']);
			
			if($i+1 == $true_repay_time){
				$load_repay['repay_money'] = $deal['last_month_repay_money'];
				$load_repay['self_money'] = $deal['borrow_amount'] - $has_use_self_money;
			}
			else{
				$load_repay['repay_money'] = $deal['month_repay_money'];
				$load_repay['self_money'] = get_self_money($i,$deal['borrow_amount'],$deal['month_repay_money'],$deal['rate']);
				
				$has_use_self_money += $load_repay['self_money'];
			}
			
			$load_repay['manage_money'] = $deal['month_manage_money'];
			$load_repay['interest_money'] = $load_repay['repay_money'] - $load_repay['self_money'];
			
			//借款者 授权服务机构获取的管理费抽成
			$rebate_rs = get_rebate_fee($deal['user_id'],"borrow");
			$load_repay['manage_money_rebate'] = $load_repay['manage_money']* floatval($rebate_rs['rebate'])/100;
			
			$load_repay['deal_id'] = $deal['id'];
			$load_repay['user_id'] = $deal['user_id'];
			
			$list[] = $load_repay;
		}
		return $list;
	}
	//还款回款计划
	function make_user_repay_plan($deal,$idx,$repay_day,$true_time,$repay_id,$load_users,&$total_money){
				
		$true_repay_time = $deal['repay_time'];
		
		if(intval($true_time) == 0)
			$true_time = TIME_UTC;
			
		$load_ids = array();
		foreach($load_users as $k=>$v){
			$item = array();
			$item = $v;
			$item['load_id'] = $v['id'];
			$item['repay_id'] = $repay_id;
			$item['has_repay'] = 0;
			$item['t_user_id'] = 0;
			
			$month_repay_money = pl_it_formula($item['money'],$deal['rate']/12/100,$true_repay_time);

			//最后一个月还本息
			if($idx+1 == $true_repay_time){
				$item['repay_manage_money'] = $deal['month_manage_money'] - round($deal['month_manage_money'] / $deal['buy_count'],2) * ($deal['buy_count'] - 1);
				$item['repay_money'] = round($month_repay_money*$true_repay_time,2) - round($month_repay_money,2)*($true_repay_time-1);
				$item['self_money'] = $v['money'] - $total_money[$v['id']];
				unset($total_money[$v['id']]);
			}
			else{
				$item['repay_manage_money'] = $deal['month_manage_money'] / $deal['buy_count'];
				$item['repay_money'] = $month_repay_money;
				$item['self_money'] = get_self_money($idx,$v['money'],$month_repay_money,$deal['rate']);
				$total_money[$v['id']] += round((float)$item['self_money'],2);
			}
			
			$item['interest_money'] =  $item['repay_money'] - $item['self_money']; 
			$item['manage_money'] = $item['money']* floatval($deal["user_loan_manage_fee"])/100;
			if($item['is_winning']==1 && (int)$item['income_type']==2 && (float)$item['income_value']!=0){
				$item['reward_money'] = $item['interest_money'] * (float)$item['income_value'] * 0.01;
			}
			
			
			$load_users[$k]= $item;
			
			$load_ids[] = $item['id'];
		}
		//获取已转让的标
		if(count($load_ids) > 0){
			$temp_t_users = $GLOBALS['db']->getAll("SELECT u.ips_acct_no,u.id as user_id,u.user_name,dlt.load_id FROM ".DB_PREFIX."deal_load_transfer dlt LEFT JOIN ".DB_PREFIX."user u ON dlt.t_user_id=u.id WHERE dlt.load_id in(".implode(",",$load_ids).") and deal_id=".$deal['id']." and dlt.t_user_id >0 and dlt.status=1 and dlt.near_repay_time<=".$repay_day);
			if($temp_t_users){
				$transfer_users =array();
				foreach($temp_t_users as $k=>$v){
					$transfer_users[$v['load_id']] = $v;
				}
				unset($temp_t_users);
				foreach($load_users as $k=>$v){
					if(isset($transfer_users[$v['id']])){
						$load_users[$k]['t_user_id'] = $transfer_users[$v['id']]['user_id'];
					}
				}
				
			}
			
		}
		$list = array();
		foreach($load_users as $kk=>$vv){
			$repay_data =array();
			$repay_data['u_key'] = $kk;
			$repay_data['l_key'] = $idx;
			$repay_data['deal_id'] = $vv['deal_id'];
			$repay_data['load_id'] = $vv['id'];
			$repay_data['repay_id'] = $vv['repay_id'];
			$repay_data['t_user_id'] = $vv['t_user_id'];
			$repay_data['user_id'] = $vv['user_id'];
			$repay_data['repay_time'] = $repay_day;
			$repay_data['repay_date'] = to_date($repay_day);
			$repay_data['self_money'] = $vv['self_money'];
			$repay_data['repay_money'] = $vv['repay_money'];
			$repay_data['interest_money'] = $vv['interest_money'];
			$repay_data['repay_manage_money'] = $vv['repay_manage_money'];
			$repay_data['loantype'] = $deal['loantype'];
			$repay_data['has_repay'] = $vv['has_repay'];
			$repay_data['manage_money'] = $vv['manage_money'];
			$repay_data['reward_money'] = $vv['reward_money'];
			
			//VIP利息管理费
			$deal =  get_user_load_fee((int)$vv['t_user_id'] > 0 ? $vv['t_user_id'] : $vv['user_id'],0,$deal);
			$repay_data['manage_interest_money'] = $repay_data['interest_money']*floatval($deal["user_loan_interest_manage_fee"])/100;
			
			//投资者 授权服务机构获取的利息管理费抽成
			$rebate_rs = get_rebate_fee((int)$vv['t_user_id'] > 0 ? $vv['t_user_id'] : $vv['user_id'],"invest");
			$repay_data['manage_interest_money_rebate'] = $repay_data['manage_interest_money']* floatval($rebate_rs['rebate'])/100;
			
			$list[] = $repay_data;
		}
		return $list;
	}
	
	/**
	 * 提前还款
	 */
	function inrepay_repay($loaninfo,$k,$time_utc=0){
		$benjin = $loaninfo['deal']['borrow_amount'];
		
		$rate = $loaninfo['deal']['rate']*0.01/12;
		
		$benjin = get_benjin($k,$loaninfo['deal']['repay_time'],$benjin,$loaninfo['deal']['month_repay_money'],$loaninfo['deal']['rate']);
		
		$return["impose_money"] = $benjin * (float)trim($loaninfo['deal']['compensate_fee'])*0.01;
		$return["true_self_money"] = $benjin;
		$return["true_repay_money"] = $benjin + $benjin*$rate;
		$return["true_manage_money"] = $loaninfo['deal']['month_manage_money'];
		$return["true_manage_money_rebate"] = $return["true_manage_money"] * floatval($loaninfo['deal']['rebate'])/100;
		
		$return["true_manage_interest_money"] = $loaninfo['deal']['manage_interest_money'];
		$return["true_manage_interest_money_rebate"] = $loaninfo['deal']['manage_interest_money_rebate'];
		
		return $return;
	}
	
	/**
	 * 债券转让计算
	 */
	function transfer($transfer){
		
		$return['month_repay_money'] = pl_it_formula($transfer['load_money'],$transfer['rate']/12/100,$transfer['repay_time']);
		//剩余多少钱未回
		$return['all_must_repay_money'] = $return['month_repay_money'] * $transfer['how_much_month'];
		//剩余多少本金未回
		if($transfer['repay_time'] == $transfer['how_much_month'])
			$return['left_benjin'] = $transfer['load_money'];
		else
			$return['left_benjin'] = get_benjin($transfer['repay_time']-$transfer['how_much_month'],$transfer['repay_time'],$transfer['load_money'],$return['month_repay_money'],$transfer['rate']);
			
		return $return;
	}
}
?>
