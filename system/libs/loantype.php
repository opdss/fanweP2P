<?php
interface loantype{
	/**
	 * 是否最后一起才还款
	 */
	 function is_last_repay();
	/**
	 * 还多少钱 
	 */
	function deal_repay_money($deal);
	/**
	 * 会员还款计划
	 */
	function make_repay_plan($deal);
	/**
	 * 会员回款计划
	 */
	function make_user_repay_plan($deal,$idx,$repay_day,$true_time,$repay_id,$load_users,&$total_money);
	
	/**
	 * 提前还款
	 */
	function inrepay_repay($loaninfo,$k,$time_utc=0);
	
	/**
	 * 债券转让计算
	 */
	function transfer($transfer);
}
?>
