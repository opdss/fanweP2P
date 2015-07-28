<?php
/**
 * 到期还本息接口
 */
if (isset($read_modules) && $read_modules == true)
{
    $module['class_name']    = 'loantype_2';
    //键值跟class_name后面的 _?对应
    $module['key']    = 2;
    //借口名称
    $module['name']    = "到期还本息";
    //短名称
    $module['sub_name']    = "到期还本息";
    //支持借款期限类型  数组 array  0[,1];  0天标   1月标
    $module['repay_time_type']    = array(0,1);
    return $module;
}
require_once APP_ROOT_PATH."system/libs/loantype.php";
class loantype_2 implements loantype{
	/**
	 * 是否最后一起才还款
	 */
	function is_last_repay(){
		return true;
	}
	/**
	 * 还多少钱 
	 */
	function deal_repay_money($deal){
		$true_repay_time = $deal['repay_time'];
		//当为天的时候
		if($deal['repay_time_type'] == 0){
			$deal['rate'] = $deal['rate']/30;
		}
		
		//月还本息
		$return['month_repay_money'] = $deal['borrow_amount'] * $deal['rate']/12/100 * $true_repay_time;
		//实际还多少钱 
		$return['remain_repay_money'] = $deal['borrow_amount'] + $return['month_repay_money'] ;
		//最后一期还款本息
		$return['last_month_repay_money'] = $return['remain_repay_money'];
		
		//是否最后一期才算罚息
		$return['is_check_impose'] = true;
		return $return;
	}
	
	//生成还款计划
	function make_repay_plan($deal){
		$all_repay_time = $deal['repay_time'];
		//当为天的时候
		if($deal['repay_time_type'] == 0){
			$deal['rate'] = $deal['rate']/30;
		}
		
		$true_repay_time = 1;
			
		$repay_day = $deal['repay_start_time'];
		$has_use_self_money = 0;
		$list = array();
		for($i=0;$i<$true_repay_time;$i++){
			$load_repay = array();
			if($deal['repay_time_type']==0)
				$repay_day = $load_repay['repay_time'] = $repay_day + $deal['repay_time']*24*3600;
			else
				$repay_day = $load_repay['repay_time'] = next_replay_month($repay_day,$all_repay_time);
				
			$load_repay['repay_date'] = to_date($load_repay['repay_time']);
			
			if($i+1 == $true_repay_time){
				$load_repay['repay_money'] = $deal['last_month_repay_money'];
				$load_repay['self_money'] = $deal['borrow_amount'] ;
				$load_repay['manage_money'] = $deal['all_manage_money'];
			}
			else{
				$load_repay['repay_money'] = 0;
				$load_repay['self_money'] = 0;
				$load_repay['manage_money'] = 0;
			}
			
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
	//生成回款计划
	function make_user_repay_plan($deal,$idx,$repay_day,$true_time,$repay_id,$load_users,&$total_money){
		$all_repay_time = $all_month = $deal['repay_time'];
		//当为天的时候
		if($deal['repay_time_type'] == 0){
			$deal['rate'] = $deal['rate']/30;
			$all_month = 1;
		}
		
		$true_repay_time = 1;
		
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
			
			//最后一个月还本息
			if($idx+1 == $true_repay_time){
				$item['repay_money'] = $item['money'] + $item['money']*$deal['rate']/12/100*$all_repay_time;
				$item['self_money'] = $item['money'];
				$item['repay_manage_money'] = $deal['all_manage_money'] / $deal['buy_count'];
			}
			
			$item['interest_money'] =  $item['repay_money'] - $item['self_money']; 
			$item['manage_money'] = $item['money']* floatval($deal["user_loan_manage_fee"])*0.01 * $all_month;
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
			$repay_data['manage_interest_money'] = $repay_data['interest_money']*floatval($deal["user_loan_interest_manage_fee"])*0.01;
			
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
		
		$all_repay_time = $loaninfo['deal']['repay_time'];
		$true_m = $true_k = 0;
		if($time_utc == 0)
			$time_utc = TIME_UTC;
		//当为天的时候
		if($loaninfo['deal']['repay_time_type'] == 0){
			$rate = $rate/30;
			$left_time = intval(to_timespan(to_date($time_utc,"Y-m-d"),"Y-m-d")) - intval($loaninfo['deal']['repay_start_time']);
			if($left_time > 0)
				$true_k = ceil($left_time/24/3600);
			else
				$true_k = 1 ;
				
			$true_m = 1;
			$all_repay_time = 1;
		}
		else{
			for($i=0;$i<$all_repay_time;$i++){
				if($time_utc >= next_replay_month($loaninfo['deal']['repay_start_time'],$i)+24*3600-1 ){
					$true_k +=1;
				}
			}
			$true_m = $true_k;
		}

		$return["impose_money"] = $benjin * (float)trim($loaninfo['deal']['compensate_fee'])*0.01;
		$return["true_self_money"] = $benjin;
		$return["true_repay_money"] = $benjin + $benjin*$rate*$true_k;
		$return["true_manage_money"] = ($loaninfo['deal']['all_manage_money']/$all_repay_time) * $true_m;
		$return["true_manage_money_rebate"] = $return["true_manage_money"] * floatval($loaninfo['deal']['rebate'])/100;
		
		$return["true_manage_interest_money"] = $benjin*$rate*$true_k*floatval($loaninfo['deal']["user_loan_interest_manage_fee"])/100;
		$return["true_manage_interest_money_rebate"] = $return["true_manage_interest_money"]* floatval($loaninfo['deal']['rebate_fee'])/100;
		
		return $return;
	}
	
	/**
	 * 债券转让计算
	 */
	function transfer($transfer){
		$return['month_repay_money'] = 0 ;
		//剩余多少钱未回
		$return['all_must_repay_money'] = ($transfer['load_money'] * $transfer['rate']/12/100) * $transfer['repay_time'] + $transfer['load_money'];
		//剩余多少本金未回
		$return['left_benjin'] = $transfer['load_money'];
		
		return $return;
	}
}
?>
