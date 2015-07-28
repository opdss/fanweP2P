<?php

/**
 * 获取指定的投标
 */
function get_plan($id=0)
{
	$time = TIME_UTC;

	if($id==0)  //有ID时不自动获取
	{
		return false;
	}
	else{
		$plan = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."plan where id = ".intval($id));
	}
	/*
	if($plan)
	{
		format_plan_item($plan);
	}*/
	return $plan;

}

//U计划回款
function get_plan_load_list($plan){
	
	$plan_repay_list = $GLOBALS['db']->getAll("SELECT * FROM ".DB_PREFIX."plan_load_repay where plan_id=".$plan['id']." order by l_key ASC ");
	foreach($plan_repay_list as $k=>$v){
		$i = $v['l_key'];  //第几期
		$u = $u['u_key'];  //第几个用户
		$loan_list[$i][$u]['l_key'] = $v['l_key'];  //还的是第几期
		$loan_list[$i][$u]['l_key'] = $v['u_key'];  //还的是第几个投标人
		$loan_list[$i][$u]['self_money'] = $v['self_money'];  //本金
		$loan_list[$i][$u]['repay_money'] = $v['repay_money'];  //还款金额
		$loan_list[$i][$u]['manage_money'] = $v['manage_money'];  //管理费
		$loan_list[$i][$u]['repay_manage_money'] = $v['repay_manage_money'];  //从借款者均摊下来的管理费
		$loan_list[$i][$u]['repay_manage_impose_money'] = $v['repay_manage_impose_money'];  //借款者均摊下来的逾期管理费
		$loan_list[$i][$u]['reward_money'] = $v['reward_money'];  //预计奖励收益
		$loan_list[$i][$u]['true_reward_money'] = $v['true_reward_money'];  //实际奖励收益
		$loan_list[$i][$u]['impose_money'] = $v['impose_money'];  //罚息
		$loan_list[$i][$u]['repay_time'] = $v['repay_time'];  //预计回款时间
		$loan_list[$i][$u]['repay_date'] = $v['repay_date'];  //预计回款时间,方便统计
		$loan_list[$i][$u]['true_repay_time'] = $v['true_repay_time'];  //实际回款时间
		$loan_list[$i][$u]['true_repay_date'] = $v['true_repay_date'];  //实际回款时间,方便统计使用
		$loan_list[$i][$u]['true_repay_money'] = $v['true_repay_money'];  //真实还款本息
		$loan_list[$i][$u]['true_self_money'] = $v['true_self_money'];  //真实还款本金
		$loan_list[$i][$u]['interest_money'] = $v['interest_money'];  //利息   repay_money - self_money
		$loan_list[$i][$u]['true_interest_money'] = $v['true_interest_money'];  //实际利息
		$loan_list[$i][$u]['true_manage_money'] = $v['true_manage_money'];  //实际管理费
		$loan_list[$i][$u]['true_repay_manage_money'] = $v['true_repay_manage_money'];  
		$loan_list[$i][$u]['status'] = $v['status'];  //0提前，1准时，2逾期，3严重逾期
		$loan_list[$i][$u]['is_site_repay'] = $v['is_site_repay'];  //0自付，1网站垫付 2担保机构垫付
		$loan_list[$i][$u]['has_repay'] = $v['has_repay'];  //0未收到还款，1已收到还款
		$loan_list[$i][$u]['loantype'] = $v['loantype'];  //还款方式
		$loan_list[$i][$u]['manage_interest_money'] = $v['manage_interest_money'];  //预计能收到：利息管理费,是在满标放款时生成
		$loan_list[$i][$u]['true_manage_interest_money'] = $v['true_manage_interest_money'];  //实际收到：利息管理费,是在还款时生成
		$loan_list[$i][$u]['manage_interest_money_rebate'] = $v['manage_interest_money_rebate'];  //预计返佣金额(返给授权机构)
		$loan_list[$i][$u]['true_manage_interest_money_rebate'] = $v['true_manage_interest_money_rebate'];  //实际返佣金额(返给授权机构)
	
		
		
		/**
		 * status 1提前,2准时还款，3逾期还款 4严重逾期 5部分还款 6还款中
		 */
		if($v['has_repay'] == 2){
			$loan_list[$i]['status'] = 5;
		}
		elseif($v['has_repay'] == 3){
			$loan_list[$i]['status'] = 6;
		}
		$loan_list[$i]['repay_day'] = $v['repay_time'];
		//月还本息
		$loan_list[$i]['month_repay_money'] = $v['repay_money'];
		//判断是否已经还完
		$loan_list[$i]['true_repay_time'] = $v['true_repay_time'];
		//管理费
		$loan_list[$i]['month_manage_money'] = $v['manage_money'] - $v['true_manage_money'];
		//返佣
		$loan_list[$i]['manage_money_rebate'] = (float)$v['manage_money_rebate'];
		
		//has_repay：1：已还款;0:未还款
		$loan_list[$i]['has_repay'] = $v['has_repay'];
		
		//已还多少
		$loan_list[$i]['month_has_repay_money'] = 0;
		
		//总罚息 =  罚息管理费 + 逾期管理费
		$loan_list[$i]['impose_all_money'] = 0;
		if($v['has_repay'] == 1){
			$loan_list[$i]['month_has_repay_money'] = $v['true_repay_money'];
			$loan_list[$i]['month_manage_money'] = $v['true_manage_money'];
			//返佣
			$loan_list[$i]['manage_money_rebate'] = (float)$v['true_manage_money_rebate'];
				
			$loan_list[$i]['status'] = $v['status']+1;
			
			$loan_list[$i]['month_repay_money'] =0;
			
			//逾期罚息
			$loan_list[$i]['impose_money'] = $v['impose_money'];
			
			//逾期管理费
			$loan_list[$i]['manage_impose_money'] = $v['manage_impose_money'];
				
			//真实还多少
			$loan_list[$i]['month_has_repay_money_all'] = $loan_list[$i]['month_has_repay_money'] + $loan_list[$i]['month_manage_money']+$loan_list[$i]['impose_money']+$loan_list[$i]['manage_impose_money'];
			
			//总的必须还多少
			$loan_list[$i]['month_need_all_repay_money'] = 0;
			
			$loan_list[$i]['impose_all_money'] = $loan_list[$i]['impose_money'] + $loan_list[$i]['manage_impose_money'];
			
		}
		elseif($v['has_repay'] == 0){
			//判断是否罚息
			if(TIME_UTC > ($v['repay_time']+ 24*3600 -1)&& $loan_list[$i]['month_repay_money'] > 0){
				//晚多少天
				$loan_list[$i]['status'] = 3;
				$time_span = to_timespan(to_date(TIME_UTC,"Y-m-d"),"Y-m-d");
				$next_time_span = $v['repay_time'];
				$day  = ceil(($time_span-$next_time_span)/24/3600);

				$loan_list[$i]['impose_day'] = $day;

				$impose_fee = trim($deal['impose_fee_day1']);
				$manage_impose_fee = trim($deal['manage_impose_fee_day1']);
				//严重逾期费率
				if($day >= app_conf('YZ_IMPSE_DAY')){
					$loan_list[$i]['status'] = 4;
					$impose_fee = trim($deal['impose_fee_day2']);
					$manage_impose_fee = trim($deal['manage_impose_fee_day2']);
				}
				
				$impose_fee = floatval($impose_fee);
				$manage_impose_fee = floatval($manage_impose_fee);

				//罚息
				$loan_list[$i]['impose_money'] = $loan_list[$i]['month_repay_money']*$impose_fee*$day/100;
				
				//罚管理费
				$loan_list[$i]['manage_impose_money'] = $loan_list[$i]['month_repay_money']*$manage_impose_fee*$day/100;
				$loan_list[$i]['impose_all_money'] = $loan_list[$i]['impose_money'] + $loan_list[$i]['manage_impose_money'];
			}
			/*elseif(to_date(TIME_UTC,"Y-m-d") == to_date($v['repay_time'],"Y-m-d") || (((int)$v['repay_time'] - TIME_UTC)/24/3600 <=3 && ((int)$v['repay_time'] - TIME_UTC)/24/3600 >=0)){
				$loan_list[$i]['status'] =  2;
			}
			else{
				$loan_list[$i]['status'] =  1;
			}*/
			else{
				$loan_list[$i]['status'] =  2;
			}
				
			//真实还多少
			$loan_list[$i]['month_has_repay_money_all'] = $has_repay_money[$v['id']];
				
			//总的必须还多少
			$loan_list[$i]['month_need_all_repay_money'] =  $loan_list[$i]['month_repay_money'] + $loan_list[$i]['month_manage_money'] + $loan_list[$i]['impose_money'] + $loan_list[$i]['manage_impose_money'];
		}
		elseif($v['has_repay'] == 2){
			//判断是否罚息
			$ss_repay_info = $GLOBALS['db']->getRow("SELECT sum(repay_money) as month_repay_money,sum(repay_manage_money) as month_manage_money FROM ".DB_PREFIX."deal_load_repay WHERE l_key =".$i." and deal_id=".$deal['id']." and has_repay=0 ");
			
			$tmp_month_repay_money = $loan_list[$i]['month_repay_money'];
			$loan_list[$i]['month_repay_money'] = $ss_repay_info['month_repay_money'];
			$loan_list[$i]['month_manage_money']= $ss_repay_info['month_manage_money'];
			if(TIME_UTC > ($v['repay_time']+ 24*3600 -1)&& $loan_list[$i]['month_repay_money'] > 0){
				$loan_list[$i]['status'] = 3;
				//晚多少天
				$time_span = to_timespan(to_date(TIME_UTC,"Y-m-d"),"Y-m-d");
				$next_time_span = $v['repay_time'];
				$day  = ceil(($time_span-$next_time_span)/24/3600);

				$loan_list[$i]['impose_day'] = $day;

				$impose_fee = trim($deal['impose_fee_day1']);
				$manage_impose_fee = trim($deal['manage_impose_fee_day1']);
				//严重逾期费率
				if($day >= app_conf('YZ_IMPSE_DAY')){
					$loan_list[$i]['status'] = 4;
					$impose_fee = trim($deal['impose_fee_day2']);
					$manage_impose_fee = trim($deal['manage_impose_fee_day2']);
				}
				
				$impose_fee = floatval($impose_fee);
				$manage_impose_fee = floatval($manage_impose_fee);

				//罚息
				$loan_list[$i]['impose_money'] = $loan_list[$i]['month_repay_money']*$impose_fee*$day/100;
				
				
				//罚管理费
				$loan_list[$i]['manage_impose_money'] = $loan_list[$i]['month_repay_money']*$manage_impose_fee*$day/100;
				$loan_list[$i]['impose_all_money'] = $loan_list[$i]['impose_money'] + $loan_list[$i]['manage_impose_money'];
			}
			/*elseif(to_date(TIME_UTC,"Y-m-d") == to_date($v['repay_time'],"Y-m-d") || (((int)$v['repay_time'] - TIME_UTC)/24/3600 <=3 && ((int)$v['repay_time'] - TIME_UTC)/24/3600 >=0)){
				
				$loan_list[$i]['status'] =  2;
			}
			elseif(round($tmp_month_repay_money) <= $loan_list[$i]['month_repay_money']){
				$loan_list[$i]['has_repay'] =  0;
			}
			else{
				$loan_list[$i]['status'] =  1;
			}*/
			else{
				$loan_list[$i]['status'] =  2;
			}
			$loan_list[$i]['has_repay'] =  0;
				
			//真实还多少
			$loan_list[$i]['month_has_repay_money_all'] = $has_repay_money[$v['id']];
				
			//总的必须还多少
			$loan_list[$i]['month_need_all_repay_money'] =  $loan_list[$i]['month_repay_money'] + $loan_list[$i]['month_manage_money'] + $loan_list[$i]['impose_money'] + $loan_list[$i]['manage_impose_money'];
		}

		//还款日
		$loan_list[$i]['repay_day_format'] = to_date($loan_list[$i]['repay_day'],'Y-m-d');
		//已还金额
		$loan_list[$i]['month_has_repay_money_all_format'] = format_price($loan_list[$i]['month_has_repay_money_all']);
		//待还金额
		$loan_list[$i]['month_need_all_repay_money_format'] = format_price($loan_list[$i]['month_need_all_repay_money']);

		//待还本息
		$loan_list[$i]['month_repay_money_format'] = format_price($loan_list[$i]['month_repay_money']);
		//借款管理费
		$loan_list[$i]['month_manage_money_format'] = format_price($loan_list[$i]['month_manage_money']);
		//返佣
		$loan_list[$i]['manage_money_rebate_format'] = format_price($loan_list[$i]['manage_money_rebate']);
		
		//借款管理费
		$loan_list[$i]['manage_money_impose_format'] = format_price($loan_list[$i]['manage_impose_money']);

		//逾期费用
		$loan_list[$i]['impose_money_format'] = format_price($loan_list[$i]['impose_money']);
		
		//逾期、违约金
		$loan_list[$i]['impose_all_money_format'] = format_price($loan_list[$i]['impose_all_money']);
		
		//状态
		if($loan_list[$i]['has_repay'] == 0){
			$loan_list[$i]['status_format'] = '待还';
		}
		elseif($loan_list[$i]['status'] == 1){
			$loan_list[$i]['status_format'] = '提前还款';
		}elseif($loan_list[$i]['status'] == 2){
			$loan_list[$i]['status_format'] = '正常还款';
		}elseif($loan_list[$i]['status'] == 3){
			$loan_list[$i]['status_format'] = '逾期还款';
		}elseif($loan_list[$i]['status'] == 4){
			$loan_list[$i]['status_format'] = '严重逾期';
		}elseif($loan_list[$i]['status'] == 5){
			$loan_list[$i]['status_format'] = '部分还款';
		}
		elseif($loan_list[$i]['status'] == 6){
			$loan_list[$i]['status_format'] = '还款中';
		}
		
		
	}
	

	return $loan_list;
}



?>