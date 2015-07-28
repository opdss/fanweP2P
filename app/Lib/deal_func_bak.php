<?php
// +----------------------------------------------------------------------
// | Fanwe 方维p2p借贷系统
// +----------------------------------------------------------------------
// | Copyright (c) 2011 http://www.fanwe.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 云淡风轻(88522820@qq.com)
// +----------------------------------------------------------------------


/**
 * 获取指定的投标
 */
function get_deal($id=0,$is_effect=1)
{
	$time = TIME_UTC;
	
	if($is_effect == 1)
	{
		$ext = " and is_effect = 1 ";
	}
	if($id==0)  //有ID时不自动获取
	{
		return false;
		/*$sql = "select id from ".DB_PREFIX."deal where is_effect = 1 and is_delete = 0  ";
			if($cate_id>0)
			{

		$ids =load_auto_cache("deal_sub_parent_cate_ids",array("cate_id"=>$cate_id));

		$sql .= " and cate_id in (".implode(",",$ids).")";
		}
			
		$sql.=" order by sort desc";
		$deal = $GLOBALS['db']->getRow($sql);
		*/
			
	}
	else{
		$deal = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."deal where id = ".intval($id)."  and is_delete = 0  $ext");
	}

	if($deal)
	{
		if($deal['deal_status']!=3 && $deal['deal_status']!=5)
		{
			$temp_data =syn_deal_status($deal['id']);
			$deal = array_merge($deal,$temp_data);
		}
		format_deal_item($deal);
			
	}
	return $deal;

}


/**
 * 获取正在进行的投标列表
 */
function get_deal_list($limit="",$cate_id=0, $where='',$orderby = '',$user_name='',$user_pwd='')
{

	$time = TIME_UTC;

	$count_sql = "select count(*) from ".DB_PREFIX."deal where is_effect = 1 and is_delete = 0 ";
	if(es_cookie::get("shop_sort_field")=="ulevel"){
		$extfield = ",(SELECT u.level_id FROM fanwe_user u WHERE u.id=user_id ) as ulevel";
	}

	$sql = "select *,start_time as last_time,(load_money/borrow_amount*100) as progress_point,(start_time + enddate*24*3600 - ".$time.") as remain_time $extfield from ".DB_PREFIX."deal where is_effect = 1 and is_delete = 0 ";
	if($cate_id>0)
	{
		$ids =load_auto_cache("deal_sub_parent_cate_ids",array("cate_id"=>$cate_id));
		$sql .= " and cate_id in (".implode(",",$ids).")";
		$count_sql .= " and cate_id in (".implode(",",$ids).")";
	}

	if($where != '')
	{
		$sql.=" and ".$where;
		$count_sql.=" and ".$where;
	}

	if($orderby=='')
		$sql.=" order by sort desc ";
	else
		$sql.=" order by ".$orderby;
	
	if($limit!=""){
		$sql .=" limit ".$limit;
	}

	$deals_count = $GLOBALS['db']->getOne($count_sql);
	if($deals_count > 0){
		$deals = $GLOBALS['db']->getAll($sql);
		//echo $sql;
		if($deals)
		{
			foreach($deals as $k=>$deal)
			{
				format_deal_item($deal);
				$deals[$k] = $deal;
			}
		}
	}
	else{
		$deals = array();
	}
	return array('list'=>$deals,'count'=>$deals_count);
}

function format_deal_item(&$deal){
	
	//判断是否已经开始
	$deal['is_wait'] = 0;
	if($deal['start_time'] > TIME_UTC){
		$deal['is_wait'] = 1;
		$deal['remain_time'] = $deal['start_time'] - TIME_UTC;
	}
	else{
		$deal['remain_time'] = $deal['start_time'] + $deal['enddate']*24*3600 - TIME_UTC;
	}
		
	//当为天的时候
	if($deal['repay_time_type'] == 0){
		$true_repay_time = 1;
	}
	else{
		$true_repay_time = $deal['repay_time'];
	}
		
	if(trim($deal['titlecolor']) != ''){
		$deal['color_name'] = "<span style='color:#".$deal['titlecolor']."'>".$deal['name']."</span>";
	}
	else{
		$deal['color_name'] = $deal['name'];
	}
	//格式化数据
	$deal['borrow_amount_format'] = format_price($deal['borrow_amount']/10000)."万";format_price($deal['borrow_amount']);
		
	$deal['rate_foramt'] = number_format($deal['rate'],2);
		
	//$deal['borrow_amount_format_w'] = format_price($deal['borrow_amount']/10000)."万";
	$deal['rate_foramt_w'] = number_format($deal['rate'],2)."%";
		
	//本息还款金额
	if($deal['loantype'] == 0){
		$deal['month_repay_money'] = pl_it_formula($deal['borrow_amount'],$deal['rate']/12/100,$true_repay_time);
	}
	//每月付息，到期还本
	elseif($deal['loantype'] == 1)
		$deal['month_repay_money'] = av_it_formula($deal['borrow_amount'],$deal['rate']/12/100) ;
	//到期还本息
	elseif($deal['loantype'] == 2)
		$deal['month_repay_money'] = $deal['borrow_amount'] * $deal['rate']/12/100 * $true_repay_time;


	$deal['month_repay_money_format'] = format_price($deal['month_repay_money']);
		
	//到期还本息管理费
	$deal['month_manage_money'] = $deal['borrow_amount']*app_conf('MANAGE_FEE')/100;

	$deal['month_manage_money_format'] = format_price($deal['month_manage_money']);
	
	$deal['all_manage_money'] = $deal['month_manage_money'] * $deal["repay_time"];
	
	$deal['true_month_repay_money'] = $deal['month_repay_money'] + $deal['month_manage_money'];

	//还需多少钱
	$deal['need_money'] = format_price($deal['borrow_amount'] - $deal['load_money']);
	//百分比
	$deal['progress_point'] = $deal['load_money']/$deal['borrow_amount']*100;
		
	$deal['user'] = get_user("user_name,level_id,province_id,city_id",$deal['user_id']);
		
	if($deal['cate_id'] > 0){
		$deal['cate_info'] = $GLOBALS['db']->getRowCached("select id,name,brief,uname,icon from ".DB_PREFIX."deal_cate where id = ".$deal['cate_id']." and is_effect = 1 and is_delete = 0");
	}
	if($deal['type_id'] > 0){
		$deal['type_info'] = $GLOBALS['db']->getRowCached("select id,name,brief,uname,icon from ".DB_PREFIX."deal_loan_type where id = ".$deal['type_id']." and is_effect = 1 and is_delete = 0");
	}
		
	
	if($deal['deal_status'] <> 1 || $deal['remain_time'] <= 0){
		$deal['remain_time_format'] = "0".$GLOBALS['lang']['DAY']."0".$GLOBALS['lang']['HOUR']."0".$GLOBALS['lang']['MIN'];
	}
	else{
		$deal['remain_time_format'] = remain_time($deal['remain_time']);
	}
		
	$deal['min_loan_money_format'] = format_price($deal['min_loan_money']);
		
		
	if($deal['deal_status']==4){

		if($deal['repay_time_type'] == 0){
			/*
			 $r_y = to_date($deal['repay_start_time'],"Y");
			$r_m = to_date($deal['repay_start_time'],"m");
			$r_d = to_date($deal['repay_start_time'],"d");
			if($r_m-1 <=0){
			$r_m = 12;
			$r_y = $r_y-1;
			}
			else{
			$r_m = $r_m - 1;
			}
			$deal["type_repay_start_time"]  = to_timespan($r_y."-".$r_m."-".$r_d,"Y-m-d") + $deal['repay_time']*24*3600;
			*/
			$deal["type_repay_start_time"]  = next_replay_month($deal['repay_start_time'],"-1") + $deal['repay_time']*24*3600;
			$deal["type_next_repay_time"] = next_replay_month($deal['type_repay_start_time']);
		}

			
		if($deal['last_repay_time'] > 0){
			$deal["next_repay_time"] = next_replay_month($deal['last_repay_time']);
		}
		else{
			$deal["next_repay_time"] = next_replay_month($deal['repay_start_time']);
		}

		$deal["next_repay_time_format"] = to_date($deal['next_repay_time'],'Y-m-d');

		//总的必须还多少本息
		//
		if($deal['loantype'] == 0)
			$deal['remain_repay_money'] = $deal['month_repay_money'] * $true_repay_time;
		elseif($deal['loantype'] == 1)//每月还息，到期还本
			$deal['remain_repay_money'] = $deal['borrow_amount'] + $deal['month_repay_money'] * $true_repay_time;
		elseif($deal['loantype'] == 2)
			$deal['remain_repay_money'] = $deal['borrow_amount'] + $deal['month_repay_money'];
			
		//还有多少需要还
		$deal['need_remain_repay_money'] = $deal['remain_repay_money'] - $deal['repay_money'];
		//还款进度条
		if($deal['remain_repay_money'] > 0)
			$deal['repay_progress_point'] =  $deal['repay_money']/$deal['remain_repay_money']*100;
		else
			$deal['repay_progress_point'] =  0;

		//最后一期还款
		if($deal['loantype'] == 2)
			$deal['last_month_repay_money'] = $deal['remain_repay_money'];
		else
			$deal['last_month_repay_money'] = $deal['remain_repay_money'] - $deal['month_repay_money']*($true_repay_time-1);

		//最后的还款日期
		/*$y=to_date($deal['repay_start_time'],"Y");
			$m=to_date($deal['repay_start_time'],"m");
		$d=to_date($deal['repay_start_time'],"d");
		$y = $y + intval(($m+$true_repay_time)/12);
		$m = ($m+$true_repay_time)%12;

		$deal["end_repay_time"] = to_timespan($y."-".$m."-".$d,"Y-m-d");
		*/
		$deal["end_repay_time"] =  next_replay_month($deal['repay_start_time'],$true_repay_time);
		if(to_date($deal["end_repay_time"],"Ymd") < to_date(TIME_UTC,"Ymd")){
			$deal['exceed_the_time'] = true;
		}

		//罚息
		$is_check_impose = true;
		//到期还本息 只有最后一个月后才算罚息
		if($deal['loantype'] == 2){
			//算出到期还本息的最后一个月是否小于今天
			if($deal['exceed_the_time']){
				$is_check_impose = true;
			}
			else{
				$is_check_impose = false;
			}
		}
		if($deal["next_repay_time"] - TIME_UTC <0 && $is_check_impose){
			//晚多少天
			$time_span = to_timespan(to_date(TIME_UTC,"Y-m-d"),"Y-m-d");
			$next_time_span = to_timespan(to_date($deal['next_repay_time'],"Y-m-d"),"Y-m-d");
			$day  = ceil(($time_span-$next_time_span)/24/3600);
				
			$impose_fee = trim($deal['impose_fee_day1']);
			$manage_impose_fee = trim($deal['manage_impose_fee_day1']);
			//判断是否严重逾期
			if($day >= app_conf('YZ_IMPSE_DAY')){
				$impose_fee = trim($deal['impose_fee_day2']);
				$manage_impose_fee = trim($deal['manage_impose_fee_day2']);
			}
			
			$impose_fee = floatval($impose_fee);
			$manage_impose_fee = floatval($manage_impose_fee);
				
			//罚息
			$deal['impose_money'] = $deal['month_repay_money']*$impose_fee*$day/100;
				
			//罚管理费
				
			$deal['manage_impose_money'] = $deal['month_repay_money']*$manage_impose_fee*$day/100;
				
			$deal['impose_money'] += $deal['manage_impose_money'];
		}
	}
		
	if($deal['publis_wait'] == 1 || $deal['publis_wait'] == 0){
		$deal['publis_time_format'] = to_date($deal['create_time'],'Y-m-d H:i');
	}else{
		$deal['publis_time_format'] = to_date($deal['start_time'],'Y-m-d H:i');
	}
	
	$durl = url("index","deal",array("id"=>$deal['id']));
	$deal['share_url'] = SITE_DOMAIN.$durl;
	if($GLOBALS['user_info'])
	{
		if(app_conf("URL_MODEL")==0)
		{
			$deal['share_url'] .= "&r=".base64_encode(intval($GLOBALS['user_info']['id']));
		}
		else
		{
			$deal['share_url'] .= "?r=".base64_encode(intval($GLOBALS['user_info']['id']));
		}
	}
	
	$deal['url'] = $durl;
	if (!empty($user_name) && !empty($user_pwd)){
		$durl = "/index.php?ctl=uc_deal&act=mrefdetail&id=".$deal['id']."&user_name=".$user_name."&user_pwd=".$user_pwd;
	}else{
		$durl = "/index.php?ctl=deal&act=mobile&id=".$deal['id'];
	}
		
	$deal['app_url'] = str_replace("/mapi", "", SITE_DOMAIN.$durl);
}

/**
 * 还款列表
 */
function get_deal_load_list($deal){
	
	//当为天的时候
	if($deal['repay_time_type'] == 0){
		$true_repay_time = 1;
	}

	
	$deal_repay_list = $GLOBALS['db']->getAll("SELECT * FROM ".DB_PREFIX."deal_repay where deal_id=".$deal['id']." order by l_key ASC ");

	foreach($deal_repay_list as $k=>$v){
		
		$i = $v['l_key'];
		$loan_list[$i]['l_key'] = $v['l_key'];
		$loan_list[$i]['repay_id'] = $v['id'];
		$loan_list[$i]['impose_day'] = 0;
		/**
		 * status 1提前,2准时还款，3逾期还款 4严重逾期 5部分还款
		 */
		if($v['has_repay'] == 1){
			$loan_list[$i]['status'] = ($v['status'] + 1);
		}
		elseif($v['has_repay'] == 2){
			$loan_list[$i]['status'] = 5;
		}
		elseif($v['has_repay'] == 3){
			$loan_list[$i]['status'] = 6;
		}
		elseif($v['has_repay'] == 0){
			$loan_list[$i]['status'] = 0;
		}
		$loan_list[$i]['repay_day'] = $v['repay_time'];
		
		//月还本息
		$loan_list[$i]['month_repay_money'] = $v['repay_money'];
		//判断是否已经还完
		$loan_list[$i]['true_repay_time'] = $v['true_repay_time'];
		//管理费
		$loan_list[$i]['month_manage_money'] = $v['manage_money'];
		
		//has_repay：1：已还款;0:未还款
		$loan_list[$i]['has_repay'] = $v['has_repay'];
		
		//已还多少
		$loan_list[$i]['month_has_repay_money'] = 0;
		if($v['has_repay'] == 1){
			$loan_list[$i]['month_has_repay_money'] = $v['repay_money'];
			$loan_list[$i]['month_manage_money'] = 0;
				
			$loan_list[$i]['status'] = $v['status']+1;
			$loan_list[$i]['month_repay_money'] =0;
			
			//逾期罚息
			$loan_list[$i]['impose_money'] = $v['impose_money'];
				
			//真实还多少
			$loan_list[$i]['month_has_repay_money_all'] = $loan_list[$i]['month_has_repay_money'] + $deal['month_manage_money']+$loan_list[$i]['impose_money'];
				
			//总的必须还多少
			$loan_list[$i]['month_need_all_repay_money'] = 0;
		}
		else{
			
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
				if($deal['loantype'] == 0){
					$loan_list[$i]['impose_money'] = $loan_list[$i]['month_repay_money']*$impose_fee*$day/100;
				}
				elseif($deal['loantype'] == 1)//每月还息，到期还本
				{
					$loan_list[$i]['impose_money'] = $loan_list[$i]['month_repay_money']*$impose_fee*$day/100;
				}
				elseif($deal['loantype'] == 2){
					//到期还款 只有最后一个月超出才罚息
					if($i+1 == $true_repay_time)
						$loan_list[$i]['impose_money'] = $loan_list[$i]['month_repay_money']*$impose_fee*$day/100;
					else{
						$loan_list[$i]['impose_money'] = 0;
					}
				}
				//罚管理费
				$loan_list[$i]['manage_impose_money'] = $loan_list[$i]['month_repay_money']*$manage_impose_fee*$day/100;
				$loan_list[$i]['impose_money'] += $loan_list[$i]['manage_impose_money'];
			}
				
			//真实还多少
			$loan_list[$i]['month_has_repay_money_all'] = 0;
				
			//总的必须还多少
			$loan_list[$i]['month_need_all_repay_money'] =  $loan_list[$i]['month_repay_money'] + $loan_list[$i]['month_manage_money'] + $loan_list[$i]['impose_money'];
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

		//逾期费用
		$loan_list[$i]['impose_money_format'] = format_price($loan_list[$i]['impose_money']);

		//状态
		if($loan_list[$i]['status'] == 0){
			$loan_list[$i]['status_format'] = '待还';
		}elseif($loan_list[$i]['status'] == 1){
			$loan_list[$i]['status_format'] = '提前还款';
		}elseif($loan_list[$i]['status'] == 2){
			$loan_list[$i]['status_format'] = '准时还款';
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


/**
 * 获取某一期的用户还款列表
 * array $deal_info 借款信息 
 * int $user_id 用户ID 为0代表全部
 * int $lkey  第几期 -1 全部
 * int $ukey 第几个投标人 -1 全部
 * int $true_time  真实还款时间
 * int $get_type  0 全部 1代表未还的  2 代表已还的
 * int $r_type = 0; 返回类型; 1:只返回一个数组; $result['item']
 * string $limit; 查询限制数量; 0,20  $result['count']
 */
 
function get_deal_user_load_list($deal_info, $user_id = 0 ,$lkey = -1 , $ukey = -1,$true_time=0,$get_type = 0, $r_type = 0, $limit = ""){
	if(!$deal_info){
		return false;
	}

	$result = array();
	
		if($get_type > 0){
			if($get_type==1)
				$extW = " AND dlr.has_repay = 0 ";
			else
				$extW = " AND dlr.has_repay = 1 ";
		}
		
		if($user_id > 0){
			$extW .= " AND dlr.user_id =  ".$user_id;
		}
		
		if($lkey >= 0){
			$extW .= " AND dlr.l_key =  ".$lkey;
		}
				
		if (!empty($limit)){ 
			$limit = " limit ".$limit;
		
			$sql = "SELECT count(*) FROM ".DB_PREFIX."deal_load_repay dlr ".					
					" WHERE dlr.deal_id=".$deal_info['id']." $extW";
			
			$count = $GLOBALS['db']->getOne($sql);
			$result['count'] = $count;
		}
		$sql = "SELECT dlr.*,dl.pMerBillNo,dl.money,u.ips_acct_no,u.email,u.user_name,tu.ips_acct_no as t_ips_acct_no,tu.user_name as t_user_name,tu.email as t_email  FROM ".DB_PREFIX."deal_load_repay dlr ".
				" LEFT JOIN ".DB_PREFIX."deal_load dl ON dl.id =dlr.load_id  ".
				" LEFT OUTER JOIN ".DB_PREFIX."user u ON u.id = dlr.user_id ".
				" LEFT OUTER JOIN ".DB_PREFIX."user tu ON tu.id = dlr.t_user_id ".
				" WHERE dlr.deal_id=".$deal_info['id']." $extW ORDER BY dlr.l_key ASC,dlr.u_key ASC ".$limit;
		
		
		//echo $sql; exit;
		$load_users = $GLOBALS['db']->getAll($sql);
	
	if($true_time == 0)
		$true_time = TIME_UTC;
	
	
	
	$loan_list = array();
	foreach($load_users as $k=>$v){
			//转出方手续费  ===》收取：借款者 的管理费 + 管理逾期罚息   $item['repay_manage_money']  + $item['repay_manage_impose_money']
			//转入方手续费  ===》收取：投资者 的管理费  $item['manage_money']
			//转入金额 ===》还款金额 + 逾期罚息 $item['month_repay_money'] + $item['impose_money']  
		
			$item = array();
			
			//deal_load_repay 编号
			$item['id'] = $v['id'];
			
			//status 1提前,2准时还款，3逾期还款 4严重逾期 数据库里的参数 + 1
			if($v['has_repay'] == 1){
				$item['status'] = $v['status'] +1;
			}
			else{
				$item['status'] = 0;
			}
			
			//实际投标金额
			$item['money'] = $v['money']; 
			
			//还款日
			$item['repay_day'] = $v['repay_time'];
			
			//实际还款日
			$item['true_repay_time'] = $v['true_repay_time'];
			
			//月还本息
			$item['month_repay_money']= $v['repay_money'];
			
			//当前期本金
			$item['self_money'] = $v['self_money'];
			
			//罚息
			$item['impose_money'] =$v['impose_money'];
			
			//投标者信息
			$item['user_id'] =$v['user_id'];
			$item['user_name'] =$v['user_name'];
			$item['email'] =$v['email'];
			$item['mobile'] =$v['mobile'];
			$item['ips_acct_no'] =$v['ips_acct_no'];
			
			//承接者信息
			$item['t_user_id'] =$v['t_user_id'];
			$item['t_user_name'] =$v['t_user_name'];
			$item['t_ips_acct_no'] =$v['t_ips_acct_no'];
			$item['t_email'] =$v['t_email'];
			$item['t_mobile'] =$v['t_mobile'];
			
			//管理费
			$item['manage_money'] =$v['manage_money'];
			
			//借款者均摊下来的管理费
			$item['repay_manage_money'] =$v['repay_manage_money'];
			
			//是否还款 0未还 1已还
			$item['has_repay'] =$v['has_repay'];
			
			//对应deal_repay的编号
			$item['repay_id'] =$v['repay_id'];
			//投标编号 对应 deal_load 的编号
			$item['load_id'] =$v['load_id'];
			//第几期
			$item['l_key'] =$v['l_key'];
			//对应借款的第几个投标人
			$item['u_key'] =$v['u_key'];
			//登记债权人时提 交的订单号
			$item['pMerBillNo'] =$v['pMerBillNo'];
			//逾期借入者管理费罚息
			$item['repay_manage_impose_money'] = $v['repay_manage_impose_money'];
			
			if($v['has_repay'] == 0){
				$item['month_has_repay_money'] = 0;
				if($true_time > ($v['repay_time'] + 24*3600 -1 ) && $item['month_repay_money'] > 0){
					$time_span = to_timespan(to_date($true_time,"Y-m-d"),"Y-m-d");
					$next_time_span = $v['repay_time'];
					$day  = ceil(($time_span-$next_time_span)/24/3600);
		
					if($day >0){
						//普通逾期
						$item['status'] = 3;
						$impose_fee = trim($deal_info['impose_fee_day1']);
						$manage_impose_fee = trim($deal_info['manage_impose_fee_day1']);
						if($day >= app_conf('YZ_IMPSE_DAY')){//严重逾期
							$impose_fee = trim($deal_info['impose_fee_day2']);
							$manage_impose_fee = trim($deal_info['manage_impose_fee_day2']);
							$item['status'] = 4;
						}
						
						$impose_fee = floatval($impose_fee);
							
						//罚息
						if($deal_info['loantype'] == 0){
							$item['impose_money'] = $item['month_repay_money'] *$impose_fee*$day/100;
						}
						elseif($deal_info['loantype'] == 1){
							$item['impose_money'] = $item['month_repay_money'] *$impose_fee*$day/100;
						}
						elseif($deal_info['loantype'] == 2){
							$item['impose_money'] = $item['month_repay_money'] *$impose_fee*$day/100;
						}
						
						$item['repay_manage_impose_money'] = $item['repay_manage_money']*$manage_impose_fee*$day/100;
					}
					
				}
				$item['month_has_repay_money'] = 0;
				$item['month_has_repay_money_all'] = 0;
			}
			else{
				$item['month_has_repay_money'] = $item['month_repay_money'];
				$item['month_has_repay_money_all'] = $item['month_repay_money'] + $item['month_manage_money']+$item['impose_money'];
			}
			
			$item['repay_day_format'] = to_date($item['repay_day'],"Y-m-d");
			$item['true_repay_time_format'] = to_date($item['true_repay_time']);
			$item['manage_money_format'] = format_price($item['manage_money']);
			$item['impose_money_format'] = format_price($item['impose_money']);
			$item['repay_manage_impose_money_format'] = format_price($item['repay_manage_impose_money']);
			$item['month_repay_money_format'] = format_price($item['month_repay_money']);
			$item['month_has_repay_money_format'] = format_price($item['month_has_repay_money']);
			$item['month_has_repay_money_all_format'] = format_price($item['month_has_repay_money_all']);
			//状态
			if($item['status'] == 0){
				$item['status_format'] = '待还';
			}elseif($item['status'] == 1){
				$item['status_format'] = '提前还款';
			}elseif($item['status'] == 2){
				$item['status_format'] = '准时还款';
			}elseif($item['status'] == 3){
				$item['status_format'] = '逾期还款';
			}elseif($item['status'] == 4){
				$item['status_format'] = '严重逾期';
			}
			
			
			
			if ($r_type == 0){
				if($lkey >= 0){
					if($lkey == $item['l_key']){
						$loan_list[$item['u_key']][$item['l_key']] = $item;
					}
				}
				else
					$loan_list[$item['u_key']][$item['l_key']] = $item;
			}else{
				$loan_list[] = $item;
			}
	}
	
	if ($r_type == 0){	
		if($ukey >= 0)
			return $loan_list[$ukey];
		else{
			return $loan_list;
		}
	}else{
		$result['item'] = $loan_list;
		return $result;
	}
}


/**
 * 动态输出成功案例， 不受缓存限制
 */
function insert_success_deal_list(){
	//输出成功案例
	$GLOBALS['tmpl']->caching = true;
	$GLOBALS['tmpl']->cache_lifetime = 120;  //首页缓存10分钟
	$cache_id  = md5("success_deal_list");	
	if (!$GLOBALS['tmpl']->is_cached("inc/insert/success_deal_list.html", $cache_id))
	{	
		$suc_deal_list =  get_deal_list(11,0,"deal_status in(4,5) "," success_time DESC,sort DESC,id DESC");
		$GLOBALS['tmpl']->assign("succuess_deal_list",$suc_deal_list['list']);
	}
	return $GLOBALS['tmpl']->fetch("inc/insert/success_deal_list.html",$cache_id);
}


//更改过期流标状态
function change_deal_status(){
	//$sql = "select id from ".DB_PREFIX."deal where is_effect = 1 and deal_status = 1 and is_delete = 0 AND load_money/borrow_amount < 1 AND (start_time + enddate*24*3600 - ".TIME_UTC.") <=0  ";
	/*$sql = "select id from ".DB_PREFIX."deal where is_effect = 1 and deal_status = 1 and is_delete = 0 AND load_money/borrow_amount <= 1 ";
	 $deal_ids = $GLOBALS['db']->getAll($sql);

	foreach($deal_ids as $k=>$v)
	{
	syn_deal_status($v['id']);
	}*/
	syn_dealing();
}



function check_dobid2($deal_id,$bid_money,$bid_paypassword){	
	$root = array();
	$root["status"] = 0;//0:出错;1:正确;
	
	$bid_money = floatval($bid_money);
	$bid_paypassword = strim($bid_paypassword);
	
	
	if(!$GLOBALS['user_info']){
		$root["show_err"] = $GLOBALS['lang']['PLEASE_LOGIN_FIRST'];
		return $root;
	}
	
	
	if($bid_paypassword==""){
		$root["show_err"] = $GLOBALS['lang']['PAYPASSWORD_EMPTY'];
		return $root;
	}
	
	if(md5($bid_paypassword)!=$GLOBALS['user_info']['paypassword']){
		$root["show_err"] = $GLOBALS['lang']['PAYPASSWORD_ERROR'];
		return $root;
	}
	
	$deal = get_deal($deal_id);
	if(!$deal){
		$root["show_err"] = $GLOBALS['lang']['PLEASE_SPEC_DEAL'];
		return $root;
	}
	
	if($deal['ips_bill_no']!="" && $GLOBALS['user_info']['ips_acct_no']==""){
		$root["show_err"] = "此标为第三方托管标，请先绑定第三方托管账户,<a href=\"".url("index","uc_center")."\" target='_blank'>点这里设置</a>";
		return $root;
	}
	
	if($deal['is_wait'] == 1){
		$root["show_err"] = $GLOBALS['lang']['DEAL_IS_WAIT'];
		return $root;
	}
	//@file_put_contents("/Public/sqlog.txt",print_r($_REQUEST,1));
	if($bid_money <=0 || $bid_money < $deal['min_loan_money'] || ($bid_money * 100)%100!=0){
		$root["show_err"] = $GLOBALS['lang']['BID_MONEY_NOT_TRUE'];
		//print_r($deal);
		return $root;
	}
	if(floatval($deal['max_loan_money']) >0){
		if($bid_money > floatval($deal['max_loan_money'])){
			$root["show_err"] = $GLOBALS['lang']['BID_MONEY_NOT_TRUE'];
			//print_r($deal);
			/*
			 $root["bid_money"] = $bid_money;
			$root["max_loan_money"] = floatval($deal['max_loan_money']);
			$root["show_err"] = 'ddd2';
			print_r($root);
			die();
			*/
			return $root;
		}
	}
	
	if((int)strim(app_conf('DEAL_BID_MULTIPLE')) > 0){
		if($bid_money%(int)strim(app_conf('DEAL_BID_MULTIPLE'))!=0){
			$root["show_err"] = $GLOBALS['lang']['BID_MONEY_NOT_TRUE'];
			return $root;
		}
	}
	
	
	if(floatval($deal['progress_point']) >= 100){
		$root["show_err"] = $GLOBALS['lang']['DEAL_BID_FULL'];
		return $root;
	}
	
	if(floatval($deal['deal_status']) != 1 ){
		$root["show_err"] = $GLOBALS['lang']['DEAL_FAILD_OPEN'];
		return $root;
	}
	
	
	//判断所投的钱是否超过了剩余投标额度
	if($bid_money > (round($deal['borrow_amount'],2) - round($deal['load_money'],2))){
		$root["show_err"] = sprintf($GLOBALS['lang']['DEAL_LOAN_NOT_ENOUGHT'],format_price($deal['borrow_amount'] - $deal['load_money']));
		return $root;
	}
	
	if($deal['ips_bill_no']==""){
		$root["status"] = 1;//0:出错;1:正确;
	
		return $root;
	}
	else{
		$root["status"] = 2;//第三方托管标 正确
	
		return $root;
	}
	
}

function dobid2_ok($deal_id,$user_id){
	$deal = get_deal($deal_id);
	sys_user_status($user_id);
	//超过一半的时候
	
	if($deal['deal_status']==1 && $deal['progress_point'] >= 50 && $deal['progress_point']<=60 && $deal['is_send_half_msg'] == 0)
	{
		$msg_conf = get_user_msg_conf($deal['user_id']);
		//邮件
		if(app_conf("MAIL_ON")){
			if(!$msg_conf || intval($msg_conf['mail_half'])==1){
				$load_tmpl = $GLOBALS['db']->getRowCached("select * from ".DB_PREFIX."msg_template where name = 'TPL_DEAL_HALF_EMAIL'");
				$user_info = $GLOBALS['db']->getRow("select email,user_name from ".DB_PREFIX."user where id = ".$deal['user_id']);
				$tmpl_content = $load_tmpl['content'];
				$notice['user_name'] = $user_info['user_name'];
				$notice['deal_name'] = $deal['name'];
				$notice['deal_url'] = SITE_DOMAIN.$deal['url'];
				$notice['site_name'] = app_conf("SHOP_TITLE");
				$notice['site_url'] = SITE_DOMAIN.APP_ROOT;
				$notice['help_url'] = SITE_DOMAIN.url("index","helpcenter");
				$notice['msg_cof_setting_url'] = SITE_DOMAIN.url("index","uc_msg#setting");
	
	
				$GLOBALS['tmpl']->assign("notice",$notice);
	
				$msg = $GLOBALS['tmpl']->fetch("str:".$tmpl_content);
				$msg_data['dest'] = $user_info['email'];
				$msg_data['send_type'] = 1;
				$msg_data['title'] = "您的借款列表“".$deal['name']."”招标过半！";
				$msg_data['content'] = addslashes($msg);
				$msg_data['send_time'] = 0;
				$msg_data['is_send'] = 0;
				$msg_data['create_time'] = TIME_UTC;
				$msg_data['user_id'] =  $deal['user_id'];
				$msg_data['is_html'] = $load_tmpl['is_html'];
				$GLOBALS['db']->autoExecute(DB_PREFIX."deal_msg_list",$msg_data); //插入
			}
		}
	
		//站内信
		if(intval($msg_conf['sms_half'])==1){
			$content = "<p>您在".app_conf("SHOP_TITLE")."的借款“<a href=\"".$deal['url']."\">".$deal['name']."</a>”完成度超过50%";
			send_user_msg("",$content,0,$deal['user_id'],TIME_UTC,0,true,15);
		}
	
		//更新
		$GLOBALS['db']->autoExecute(DB_PREFIX."deal",array("is_send_half_msg"=>1),"UPDATE","id=".$deal_id);
	}	
}

function dobid2($deal_id,$bid_money,$bid_paypassword){
	$root = check_dobid2($deal_id,$bid_money,$bid_paypassword);
	if ($root["status"] == 0){
		return $root;
	}
	elseif($root["status"] == 2){
		$root['jump'] = APP_ROOT."/index.php?ctl=collocation&act=RegisterCreditor&deal_id=$deal_id&user_id=".$GLOBALS['user_info']['id']."&bid_money=$bid_money&bid_paypassword=$bid_paypassword";
		return $root;
	}
	$root["status"] = 0;
	$bid_money = floatval($bid_money);
	$bid_paypassword = strim($bid_paypassword);

	if($bid_money > $GLOBALS['user_info']['money']){
		$root["show_err"] = $GLOBALS['lang']['MONEY_NOT_ENOUGHT'];
		return $root;
	}

	$data['user_id'] = $GLOBALS['user_info']['id'];
	$data['user_name'] = $GLOBALS['user_info']['user_name'];
	$data['deal_id'] = $deal_id;
	$data['money'] = $bid_money;
	$data['create_time'] = TIME_UTC;

	$GLOBALS['db']->autoExecute(DB_PREFIX."deal_load",$data,"INSERT");
	$load_id = $GLOBALS['db']->insert_id();
	if($load_id > 0){
		//更改资金记录
		$msg = sprintf('编号%s的投标,付款单号%s',$deal_id,$load_id);
		require_once APP_ROOT_PATH."system/libs/user.php";
		modify_account(array('money'=>-$bid_money,'score'=>0),$GLOBALS['user_info']['id'],$msg);
		
		dobid2_ok($deal_id,$GLOBALS['user_info']['id']);
		
		//$root["show_err"] = $GLOBALS['lang']['ERROR_TITLE'];
		$root["status"] = 1;//0:出错;1:正确;
		return $root;
		//showSuccess($GLOBALS['lang']['DEAL_BID_SUCCESS'],$ajax,url("index","deal",array("id"=>$id)));
	}
	else{
		$root["show_err"] = $GLOBALS['lang']['ERROR_TITLE'];
		return $root;
	}
}



function get_transfer($union_sql,$condition){

	$sql = 'SELECT dlt.id,dlt.transfer_amount,dlt.near_repay_time,dlt.user_id,d.next_repay_time,d.last_repay_time,d.rate,d.repay_start_time,d.repay_time,d.name as deal_name,dlt.load_money,dlt.id as dltid,dlt.status as tras_status,dlt.t_user_id,dlt.transfer_amount,dlt.create_time as tras_create_time,dlt.transfer_time,dlt.load_id FROM '.DB_PREFIX.'deal_load dl LEFT JOIN '.DB_PREFIX.'deal d ON d.id = dl.deal_id '.$union_sql.' WHERE 1=1 '.$condition;

	$transfer = $GLOBALS['db']->getRow($sql);

	if($transfer){
		//下个还款日
		$transfer['next_repay_time_format'] = to_date($transfer['near_repay_time'],"Y-m-d");

		//还款日
		$transfer['final_repay_time'] = next_replay_month($transfer['repay_start_time'],$transfer['repay_time']);
		$transfer['final_repay_time_format'] = to_date($transfer['zz_last_repay_time'],"Y-m-d");
		//剩余期数
		$transfer['how_much_month'] = how_much_month($transfer['near_repay_time'],$transfer['final_repay_time']) +1;

		//本息还款金额
		$transfer['month_repay_money'] = pl_it_formula($transfer['load_money'],$transfer['rate']/12/100,$transfer['repay_time']);
		//剩余多少钱未回
		$transfer['all_must_repay_money'] = $transfer['month_repay_money'] * $transfer['how_much_month'];
		//剩余多少本金未回
		$transfer['left_benjin'] = get_benjin($transfer['repay_time']-$transfer['how_much_month']-1,$transfer['repay_time'],$transfer['load_money'],$transfer['month_repay_money'],$transfer['rate']);
		$transfer['left_benjin_format'] = format_price($transfer['left_benjin']);
		//剩多少利息
		$transfer['left_lixi'] = $transfer['all_must_repay_money'] - $transfer['left_benjin'];
		$transfer['left_lixi_format'] = format_price($transfer['left_lixi']);

		//转让价格
		$transfer['transfer_amount_format'] =  format_price($transfer['transfer_amount']);

		//转让收益
		$transfer['transfer_income_format'] =  format_price($transfer['all_must_repay_money']-$transfer['transfer_amount']);

		if($transfer['tras_create_time'] !=""){
			$transfer['tras_create_time_format'] =  to_date($transfer['tras_create_time'],"Y-m-d");
		}

		if(intval($transfer)>0){
			$transfer['transfer_time_format'] =  to_date($transfer['transfer_time'],"Y-m-d");
		}

		$transfer['user'] = get_user("user_name,level_id",$transfer['user_id']);
		if($transfer['t_user_id'] > 0)
			$transfer['tuser'] = get_user("user_name,level_id",$transfer['t_user_id']);
	}

	return $transfer;

}

function get_transfer_list($limit,$condition='',$extfield,$union_sql,$orderby = ''){
	//获取转让列表
	$count_sql = 'SELECT count(dlt.id) FROM '.DB_PREFIX.'deal_load_transfer dlt LEFT JOIN '.DB_PREFIX.'deal d ON d.id =dlt.deal_id WHERE dlt.status=1 and d.is_effect=1 AND d.is_delete = 0 '.$condition;

	$rs_count = $GLOBALS['db']->getOne($count_sql);

	if($rs_count > 0){
		$list_sql = 'SELECT dlt.*,d.name,d.icon,d.cate_id,d.user_id as duser_id,d.rate,d.repay_time,d.repay_time_type '.$extfield.'  FROM '.DB_PREFIX.'deal_load_transfer dlt LEFT JOIN '.DB_PREFIX.'deal d ON d.id =dlt.deal_id '.$union_sql.' WHERE dlt.status=1 and d.is_effect=1 AND d.is_delete = 0 '.$condition;
		$list_sql .= ' ORDER BY '.$orderby;
		$list_sql .=' LIMIT '.$limit;

		$list = $GLOBALS['db']->getAll($list_sql);
		foreach($list as $k=>$v){
			$list[$k]['duser'] = get_user("user_name,level_id,province_id,city_id",$v['duser_id']);
			$list[$k]['user'] = get_user("user_name,level_id,province_id,city_id",$v['user_id']);
			if($v['t_user_id'] > 0)
				$list[$k]['tuser'] = get_user("user_name,level_id,province_id,city_id",$v['t_user_id']);
			else
				$list[$k]['tuser'] = null;
				
				
			if($list[$k]['tuser'] === false){
				$list[$k]['tuser'] = null;
			}
				
			if($list[$k]['duser'] === false){
				$list[$k]['duser'] = null;
			}
				
			if($list[$k]['user'] === false){
				$list[$k]['user'] = new ArrayObject();
			}
				
				
			$list[$k]['url'] = url("index","transfer#detail",array("id"=>$v['id']));
			//$deal['url'] = $durl;
			$durl = "/index.php?ctl=deal&act=mobile&id=".$v['deal_id'];
			$list[$k]['app_url'] = str_replace("/mapi", "", SITE_DOMAIN.$durl);
				
			//剩余期数
			$list[$k]['how_much_month'] = how_much_month($v['near_repay_time'],$v['last_repay_time'])+1;
				
			if($v['cate_id'] > 0){
				$list[$k]['cate_info'] = $GLOBALS['db']->getRowCached("select id,name,brief,uname,icon from ".DB_PREFIX."deal_cate where id = ".$v['cate_id']." and is_effect = 1 and is_delete = 0");
			}
				
			//本息还款金额
			$list[$k]['month_repay_money'] = pl_it_formula($v['load_money'],$v['rate']/12/100,$v['repay_time']);
			//剩余多少钱未回
			$list[$k]['all_must_repay_money'] = $list[$k]['month_repay_money'] * $list[$k]['how_much_month'];
				
			//剩余多少本金未回
			$list[$k]['left_benjin'] = get_benjin($v['repay_time']-$list[$k]['how_much_month']-1,$v['repay_time'],$v['load_money'],$list[$k]['month_repay_money'],$v['rate']);
			$list[$k]['left_benjin_format'] = format_price($list[$k]['left_benjin']/10000)."万";
				
			//剩多少利息
			$list[$k]['left_lixi'] = $list[$k]['all_must_repay_money'] - $list[$k]['left_benjin'];
			$list[$k]['left_lixi_format'] = format_price($list[$k]['left_lixi']);
				
			$list[$k]['remain_time'] =$v['near_repay_time'] - TIME_UTC + 24*3600 - 1;
			$list[$k]['remain_time_format'] = remain_time($list[$k]['remain_time']);
				

			$list[$k]['near_repay_time_format'] = to_date($v['near_repay_time'],"Y-m-d");
			$list[$k]['transfer_amount_format'] = format_price($v['transfer_amount']/10000)."万";
				
			//转让收益
			$list[$k]['transfer_income'] =  $list[$k]['all_must_repay_money']-$list[$k]['transfer_amount'];
			$list[$k]['transfer_income_format'] =  format_price($list[$k]['transfer_income']);
				
			//
			$list[$k]['transfer_time_format'] = to_date($v['transfer_time'],"Y-m-d");
				
		}
		$result["list"] =  $list;
	}
	$result["rs_count"] =  $rs_count;
	return $result;
}

//提前还款操作界面
function getUcInrepayRefund($id){
	$id = intval($id);
	$root = array();
	$root["status"] = 0;//0:出错;1:正确;

	if($id == 0){
		$root["show_err"] = "操作失败！";
		return $root;
	}

	$deal = get_deal($id);
	if(!$deal)
	{
		$root["show_err"] = "借款不存在！";
		return $root;
	}
	if($deal['user_id']!=$GLOBALS['user_info']['id']){
		$root["show_err"] = "不属于你的借款！";
		return $root;
	}
	if($deal['deal_status']!=4){
		$root["show_err"] = "借款不是还款状态！";
		return $root;
	}

	$root["deal"] = $deal;

	$time = TIME_UTC;
	$impose_money = 0;
	//还了几期了
	$has_repay_count =  $GLOBALS['db']->getOne("SELECT count(*) FROM ".DB_PREFIX."deal_repay WHERE has_repay = 1 and deal_id=".$id);
	//计算罚息
	$loan_list = get_deal_load_list($deal);
	foreach($loan_list as $k=>$v){
		if($v['has_repay'] == 0)
		{
			$impose_money +=$v['impose_money'];
		}
	}

	//月利率
	$rate = $deal['rate']/12/100;

	//计算剩多少本金
	$benjin = $deal['borrow_amount'];
	if($deal['loantype']==0){//等额本息的时候才通过公式计算剩余多少本金
		for($i=1;$i<=$has_repay_count;$i++){
			$benjin = $benjin - $deal['month_repay_money'] + $benjin*$rate;
		}
			
		$impose_money += ($benjin - $deal['month_repay_money'] + $benjin*$rate) * (float)trim($deal['compensate_fee'])/100;
		$total_repay_money = $benjin + $benjin*$rate;
	}
	elseif($deal['loantype']==1){//每月付息，到期还本
		$impose_money += $benjin * (float)trim($deal['compensate_fee'])/100;
		$total_repay_money = $benjin + $deal['month_repay_money'];
	}
	elseif($deal['loantype']==2){//到期还本息
		$impose_money += $benjin * (float)trim($deal['compensate_fee'])/100;
		$total_repay_money = $benjin + $benjin*$rate;
		//计算应缴多少管理费
		$now_ym = to_date($time,"Y-m");
		$i=0;
		foreach($loan_list as $k=>$v){
			++$i;
			if($now_ym==to_date($v['repay_day'],"Y-m")){
				$deal['month_manage_money'] = $benjin * (float)trim($deal['manage_fee'])/100 * $i;
			}
		}
			
		$root["true_all_manage_money"] = $deal['month_manage_money'];

	}

	$root["true_all_manage_money_format"] = format_price($root["true_all_manage_money"]);

	$root["status"] = 1;//0:出错;1:正确;
	$root["impose_money"] = $impose_money;
	$root["impose_money_format"] = format_price($root["impose_money"]);

	$root["total_repay_money"] = $total_repay_money;
	$root["total_repay_money_format"] = format_price($root["total_repay_money"]);

	$true_total_repay_money = $total_repay_money + $impose_money + $deal['month_manage_money'];
	$root["true_total_repay_money"] = $true_total_repay_money;
	$root["true_total_repay_money_format"] = format_price($root["true_total_repay_money"]);

	return $root;
}


//正常还款执行界面
function getUcRepayBorrowMoney($id,$ids){
	$id = intval($id);
	$root = array();
	$root["status"] = 0;//0:出错;1:正确;

	if($id == 0){
		$root["show_err"] = "操作失败！";
		return $root;
	}

	$deal = get_deal($id);
	if(!$deal)
	{
		$root["show_err"] = "借款不存在！";
		return $root;
	}
	if($deal['ips_bill_no']!=""){
		$root["show_err"] = "此借款为第三方托管，不能以系统资金还款";
		return $root;
	}
	if($deal['user_id']!=$GLOBALS['user_info']['id']){
		$root["show_err"] = "不属于你的借款！";
		return $root;
	}
	if($deal['deal_status']!=4){
		$root["show_err"] = "借款不是还款状态！";
		return $root;
	}
	
	$ids = explode(",",$ids);

	//还款列表
	$loan_list = get_deal_load_list($deal);
	

	$first_key = -1;
	$find_first_key = false;

	$repay_data = array();

	//需还多少
	$must_repay = 0;
	//管理费多少
	$must_fee = 0;
	//罚息
	$must_impose = 0;

	$pt_impose = array();
	$yz_impose = array();
	$k_repay_time = 0 ;

	foreach($loan_list as $k=>$v){
		if($v['has_repay']==0){
			if(!$find_first_key){
				$first_key = $k;
				$find_first_key = true;
			}

			if(in_array($k,$ids)){
				
				$repay_data[$k]['id'] = $v['repay_id'];
				$repay_data[$k]['deal_id'] = $id;
				$repay_data[$k]['user_id'] = $GLOBALS['user_info']['id'];
				//月还本息
				$repay_data[$k]['repay_money'] = round($v['month_repay_money'],2);
				$must_repay +=round($v['month_repay_money'],2);
					
				//管理费
				$repay_data[$k]['manage_money'] = round($v['month_manage_money'],2);
				$must_fee += round($v['month_manage_money'],2);
					
				//罚息
				$repay_data[$k]['impose_money'] = round($v['impose_money'],2);
				$must_impose += round($v['impose_money'],2);
					
				$repay_data[$k]['status'] = 0;
				if(to_date($v['repay_day'],"Y-m-d") == to_date(TIME_UTC,"Y-m-d")){
					//准时
					$repay_data[$k]['status'] = 1;
				}
				elseif($v['impose_money'] >0){
					//逾期
					$repay_data[$k]['status'] = 2;
					if($v['impose_day'] < app_conf('YZ_IMPSE_DAY')){
						//普通逾期
						$pt_impose[] = $k;
					}

					else{
						//严重逾期
						$repay_data[$k]['status'] = 3;
						$yz_impose[] = $k;
					}
				}
				$repay_data[$k]['repay_time'] = $v['repay_day'];
				$repay_data[$k]['true_repay_time'] = TIME_UTC;
			}
		}
	}

	//不等于到期还本息时才判断是否按顺序
	if($deal['loantype'] !=2){
		if($first_key!=$ids[0]){
			$root["show_err"] = "还款失败，请按顺序还款！";
			return $root;
		}
	}

	if(($must_repay+$must_fee+$must_impose)>$GLOBALS['user_info']['money']){
		$root["show_err"] = "对不起，您的余额不足！";
		return $root;
	}

	//录入到还款列表
	require APP_ROOT_PATH.'system/libs/user.php';
	foreach($repay_data as $k=>$v){
		$deal_repay_id = $v['id'];
		$v['has_repay'] = 1;
		$GLOBALS['db']->autoExecute(DB_PREFIX."deal_repay",$v,"UPDATE",'id='.$v['id'],'SILENT');
		
		if($GLOBALS['db']->affected_rows() > 0)
		{
			//更新用户账户资金记录
			modify_account(array("money"=>-$v['impose_money']),$GLOBALS['user_info']['id'],"标:".$deal['id'].",期:".($k+1).",逾期罚息");
			modify_account(array("money"=>-$v['manage_money']),$GLOBALS['user_info']['id'],"标:".$deal['id'].",期:".($k+1).",借款管理费");
			modify_account(array("money"=>-$v['repay_money']),$GLOBALS['user_info']['id'],"标:".$deal['id'].",期:".($k+1).",偿还本息");
		}
		else{
			$root["show_err"] = "对不起，处理数据失败请联系客服！";
			return $root;
		}
	}


	//信用额度
	if($pt_impose){
		foreach($pt_impose as $pt_k=>$pt_v){
			modify_account(array("point"=>trim(app_conf('IMPOSE_POINT'))),$GLOBALS['user_info']['id'],"标:".$deal['id'].",期:".($pt_v+1).",逾期还款");
		}
	}
	if($yz_impose){
		foreach($yz_impose as $yz_k=>$yz_v){
			modify_account(array("point"=>trim(app_conf('YZ_IMPOSE_POINT'))),$GLOBALS['user_info']['id'],"标:".$deal['id'].",期:".($yz_v+1).",严重逾期还款");
		}
	}
	$next_loan = $loan_list[$ids[count($ids)-1]+1];
	$content = "您好，您在".app_conf("SHOP_TITLE")."的借款 “<a href=\"".$deal['url']."\">".$deal['name']."</a>”成功还款".number_format(($must_repay+$must_fee+$must_impose),2)."元，";
	if($next_loan){
		$content .= "本笔借款的下个还款日为".to_date($next_loan['repay_day'],"Y年m月d日")."，需要本息".number_format($next_loan['month_repay_money'],2)."元。";
		$GLOBALS['db']->query("UPDATE ".DB_PREFIX."deal SET next_repay_time = '".$next_loan['repay_day']."' WHERE id=".$id);
	}
	else{
		$content .= "本笔借款已还款完毕！";
			
		//判断获取的信用是否超过限制
		if($GLOBALS['db']->getOne("SELECT sum(point) FROM ".DB_PREFIX."user_log WHERE log_info='还清借款' AND user_id=".$GLOBALS['user_info']['id']) < (int)trim(app_conf('CONF_REPAY_SUCCESS_LIMIT'))){
			//获取上一次还款时间
			$befor_repay_time = $GLOBALS['db']->getOne("SELECT MAX(log_time) FROM ".DB_PREFIX."user_log WHERE log_info='还清借款' AND user_id=".$GLOBALS['user_info']['id']);
			$day = ceil((TIME_UTC-$befor_repay_time)/24/3600);
			//当天数大于等于间隔时间 获得信用
			if($day >= (int)trim(app_conf('REPAY_SUCCESS_DAY'))){
				modify_account(array("point"=>trim(app_conf('REPAY_SUCCESS_POINT'))),$GLOBALS['user_info']['id'],"标:".$deal['id'].",还清借款");
			}
		}
			
		//用户获得额度
		modify_account(array("quota"=>trim(app_conf('USER_REPAY_QUOTA'))),$GLOBALS['user_info']['id'],"标:".$deal['id'].",还清借款获得额度");
	}


	send_user_msg("",$content,0,$GLOBALS['user_info']['id'],TIME_UTC,0,true,8);
	//短信通知
	if(app_conf("SMS_ON")==1&&app_conf('SMS_SEND_REPAY')==1){
		$sms_content = "尊敬的".app_conf("SHOP_TITLE")."用户".$GLOBALS['user_info']['user_name']."，您成功还款".number_format(($must_repay+$must_fee+$must_impose),2)."元，感谢您的关注和支持。【".app_conf("SHOP_TITLE")."】";
		$msg_data['dest'] = $GLOBALS['user_info']['mobile'];
		$msg_data['send_type'] = 0;
		$msg_data['title'] = $msg_data['content'] = addslashes($sms_content);
		$msg_data['send_time'] = 0;
		$msg_data['is_send'] = 0;
		$msg_data['create_time'] = TIME_UTC;
		$msg_data['user_id'] = $GLOBALS['user_info']['id'];
		$msg_data['is_html'] = 0;
		$GLOBALS['db']->autoExecute(DB_PREFIX."deal_msg_list",$msg_data); //插入
	}

	syn_deal_status($id);


	//用户回款 get_deal_user_load_list($deal_info, $ukey = -1 , $load_id=0,$true_time=0,$get_type = 0,$user_id){
	$user_loan_list = get_deal_user_load_list($deal);
	
	foreach($user_loan_list as $lllk=>$lllv){
		foreach($lllv as $kk=>$vv){
			if($vv['has_repay']==0 && in_array($vv['l_key'],$ids)){//借入者已还款，但是没打款到借出用户中心
				$user_load_data = array();

				$user_load_data['true_repay_time'] = TIME_UTC;
				$user_load_data['is_site_repay'] = 0;
				$user_load_data['status'] = 0;
					
				$user_load_data['repay_money'] = $vv['month_repay_money'];
				$user_load_data['manage_money'] = $vv['month_manage_money'];
				$user_load_data['impose_money'] = $vv['impose_money'];
				
				if($vv['status']>0)
					$user_load_data['status'] = $vv['status'] - 1;
					
				$user_load_data['has_repay'] = 1;
				$GLOBALS['db']->autoExecute(DB_PREFIX."deal_load_repay",$user_load_data,"UPDATE","id=".$vv['id'],"SILENT");
				
				if($GLOBALS['db']->affected_rows() > 0){
	
					$content = "您好，您在".app_conf("SHOP_TITLE")."的投标 “<a href=\"".$deal['url']."\">".$deal['name']."</a>”成功还款".($vv['month_repay_money']+$vv['impose_money'])."元，";
					$unext_loan = $user_loan_list[$vv['u_key']][$kk+1];
						
					if($unext_loan){
						$content .= "本笔投标的下个还款日为".to_date($unext_loan['repay_day'],"Y年m月d日")."，需还本息".round($unext_loan['month_repay_money'],2)."元。";
					}
					else{
						$all_repay_money= round($GLOBALS['db']->getOne("SELECT (sum(repay_money)-sum(self_money) + sum(impose_money)) as shouyi FROM ".DB_PREFIX."deal_load_repay WHERE deal_id=".$v['deal_id']." AND user_id=".$v['user_id']),2);
						$all_impose_money = round($GLOBALS['db']->getOne("SELECT sum(impose_money) FROM ".DB_PREFIX."deal_load_repay WHERE deal_id=".$v['deal_id']." AND user_id=".$v['user_id']),2);
						$content .= "本次投标共获得收益:".$all_repay_money."元,其中违约金为:".$all_impose_money."元,本次投标已回款完毕！";
	
	
					}
					if($user_load_data['impose_money'] !=0 || $user_load_data['manage_money'] !=0 || $user_load_data['repay_money']!=0){
						$in_user_id  = $vv['user_id'];
						//如果是转让债权那么将回款打入转让者的账户
						if($vv['t_user_id'] > 0){
							$in_user_id = $vv['t_user_id'];
							$loan_user_info['user_name'] = $vv['user_name'];
							$loan_user_info['t_email'] = $vv['email'];
							$loan_user_info['t_mobile'] = $vv['mobile'];
						}
						else{
							$loan_user_info['user_name'] = $vv['t_user_name'];
							$loan_user_info['t_email'] = $vv['t_email'];
							$loan_user_info['t_mobile'] = $vv['t_mobile'];
						}
	
						//更新用户账户资金记录
						modify_account(array("money"=>$user_load_data['impose_money']),$in_user_id,"标:".$deal['id'].",期:".($kk+1).",逾期罚息");
	
						modify_account(array("money"=>-$user_load_data['manage_money']),$in_user_id,"标:".$deal['id'].",期:".($kk+1).",投标管理费");
	
						modify_account(array("money"=>$user_load_data['repay_money']),$in_user_id,"标:".$deal['id'].",期:".($kk+1).",回报本息");
						$msg_conf = get_user_msg_conf($in_user_id);
	
	
						//短信通知
						if(app_conf("SMS_ON")==1&&app_conf('SMS_REPAY_TOUSER_ON')==1){
							
							$tmpl = $GLOBALS['db']->getRowCached("select * from ".DB_PREFIX."msg_template where name = 'TPL_DEAL_LOAD_REPAY_SMS'");
							$tmpl_content = $tmpl['content'];
								
							$notice['user_name'] = $loan_user_info['user_name'];
							$notice['deal_name'] = $deal['sub_name'];
							$notice['deal_url'] = $deal['url'];
							$notice['site_name'] = app_conf("SHOP_TITLE");
							$notice['repay_money'] = number_format(($vv['month_repay_money']+$vv['impose_money']),2);
							if($unext_loan){
								$notice['need_next_repay'] = $unext_loan;
								$notice['next_repay_time'] = to_date($unext_loan['repay_day'],"Y年m月d日");
								$notice['next_repay_money'] = number_format($unext_loan['month_repay_money'],2);
							}
							else{
								$notice['all_repay_money'] = $all_repay_money;
								$notice['impose_money'] = $all_impose_money;
							}
								
							$GLOBALS['tmpl']->assign("notice",$notice);
							$sms_content = $GLOBALS['tmpl']->fetch("str:".$tmpl_content);
								
							$msg_data['dest'] = $loan_user_info['mobile'];
							$msg_data['send_type'] = 0;
							$msg_data['title'] = $msg_data['content'] = addslashes($sms_content);
							$msg_data['send_time'] = 0;
							$msg_data['is_send'] = 0;
							$msg_data['create_time'] = TIME_UTC;
							$msg_data['user_id'] = $in_user_id;
							$msg_data['is_html'] = 0;
							$GLOBALS['db']->autoExecute(DB_PREFIX."deal_msg_list",$msg_data); //插入
						}
	
						//站内信
						if($msg_conf['sms_bidrepaid']==1)
							send_user_msg("",$content,0,$in_user_id,TIME_UTC,0,true,9);
						//邮件
						if($msg_conf['mail_bidrepaid']==1 && app_conf('MAIL_ON')==1){
							
							$tmpl = $GLOBALS['db']->getRowCached("select * from ".DB_PREFIX."msg_template where name = 'TPL_DEAL_LOAD_REPAY_EMAIL'");
							$tmpl_content = $tmpl['content'];
								
							$notice['user_name'] = $loan_user_info['user_name'];
							$notice['deal_name'] = $deal['sub_name'];
							$notice['deal_url'] = $deal['url'];
							$notice['site_name'] = app_conf("SHOP_TITLE");
							$notice['site_url'] = SITE_DOMAIN.APP_ROOT;
							$notice['help_url'] = SITE_DOMAIN.url("index","helpcenter");
							$notice['msg_cof_setting_url'] = SITE_DOMAIN.url("index","uc_msg#setting");
							$notice['repay_money'] = number_format(($vv['month_repay_money']+$vv['impose_money']),2);
							if($unext_loan){
								$notice['need_next_repay'] = $unext_loan;
								$notice['next_repay_time'] = to_date($unext_loan['repay_day'],"Y年m月d日");
								$notice['next_repay_money'] = number_format($unext_loan['month_repay_money'],2);
							}
							else{
								$notice['all_repay_money'] = $all_repay_money;
								$notice['impose_money'] = $all_impose_money;
							}
								
							$GLOBALS['tmpl']->assign("notice",$notice);
								
							$msg = $GLOBALS['tmpl']->fetch("str:".$tmpl_content);
							$msg_data['dest'] = $loan_user_info['email'];
							$msg_data['send_type'] = 1;
							$msg_data['title'] = "“".$deal['name']."”回款通知";
							$msg_data['content'] = addslashes($msg);
							$msg_data['send_time'] = 0;
							$msg_data['is_send'] = 0;
							$msg_data['create_time'] = TIME_UTC;
							$msg_data['user_id'] = $in_user_id;
							$msg_data['is_html'] = $tmpl['is_html'];
							$GLOBALS['db']->autoExecute(DB_PREFIX."deal_msg_list",$msg_data); //插入
						}
	
					}
				}
			}
		}
		
	}
	
	sys_user_status($GLOBALS['user_info']['id'],false,true);
	$root["status"] = 1;//0:出错;1:正确;
	$root["show_err"] = "操作成功!";
	return $root;
}


//提前还款执行程序
function getUCInrepayRepayBorrowMoney($id){
	$id = intval($id);

	$root = array();
	$root["status"] = 0;//0:出错;1:正确;

	if($id == 0){
		$root["show_err"] = "操作失败！";
		return $root;
	}

	$deal = get_deal($id);
	if(!$deal)
	{
		$root["show_err"] = "借款不存在！";
		return $root;
	}
	if($deal['user_id']!=$GLOBALS['user_info']['id']){
		$root["show_err"] = "不属于你的借款！";
		return $root;
	}
	if($deal['deal_status']!=4){
		$root["show_err"] = "借款不是还款状态！";
		return $root;
	}

	$time = TIME_UTC;
	$impose_money = 0;
	//还了几期了
	$has_repay_count =  $GLOBALS['db']->getOne("SELECT count(*) FROM ".DB_PREFIX."deal_repay WHERE deal_id=".$id);
	//计算罚息
	$loan_list = get_deal_load_list($deal);
	$k_repay_time = 0;
	foreach($loan_list as $k=>$v){
		if($k>($has_repay_count-1))
		{
			if($k_repay_time==0)
				$k_repay_time = $v['repay_day'];
			$impose_money +=$v['impose_money'];
		}
	}

	if($impose_money > 0){
		$root["show_err"] = "请将逾期未还的借款还完才可以进行此操作！";
		return $root;
	}

	//月利率
	$rate = $deal['rate']/12/100;

	$impose_money = 0;
	//计算剩多少本金
	$benjin = $deal['borrow_amount'];
	if($deal['loantype']==0){//等额本息的时候才通过公式计算剩余多少本金
		for($i=1;$i<=$has_repay_count;$i++){
			$benjin = $benjin - ($deal['month_repay_money'] - $benjin*$rate);
		}

		$impose_money = ($benjin - $deal['month_repay_money'] + $benjin*$rate) * (int)trim(app_conf('COMPENSATE_FEE'))/100;
		$total_repay_money = $benjin + $benjin*$rate;
	}
	elseif($deal['loantype']==1){//每月付息，到期还本
		$impose_money = $benjin * (int)trim(app_conf('COMPENSATE_FEE'))/100;
		$total_repay_money = $benjin + $deal['month_repay_money'];
	}
	elseif($deal['loantype']==2){//到期还本息
		$impose_money += $benjin * (int)trim(app_conf('COMPENSATE_FEE'))/100;
		$total_repay_money = $benjin + $benjin*$rate;
			
		//计算应缴多罚息 多少管理费
		$now_ym = to_date($time,"Y-m");
		$i=0;
		foreach($loan_list as $k=>$v){
			++$i;
			if($now_ym==to_date($v['repay_day'],"Y-m")){
				$deal['month_manage_money'] = $benjin * trim(app_conf('MANAGE_FEE'))/100 * $i;
			}
		}
	}

	$GLOBALS['tmpl']->assign("impose_money",$impose_money);
	$GLOBALS['tmpl']->assign("total_repay_money",$total_repay_money);

	$true_total_repay_money = $total_repay_money + $impose_money + $deal['month_manage_money'];

	if(($total_repay_money+$impose_money+$deal['month_manage_money'])>$GLOBALS['user_info']['money']){
		$root["show_err"] = "对不起，您的余额不足！";
		return $root;
	}


	//录入到提前还款列表
	$inrepay_data['deal_id'] = $id;
	$inrepay_data['user_id'] = $GLOBALS['user_info']['id'];
	$inrepay_data['repay_money'] = round($total_repay_money);
	$inrepay_data['impose_money'] = round($impose_money,2);
	$inrepay_data['manage_money'] = round($deal['month_manage_money']);
	$inrepay_data['repay_time'] = $k_repay_time;
	$inrepay_data['true_repay_time'] = $time;

	$GLOBALS['db']->autoExecute(DB_PREFIX."deal_inrepay_repay",$inrepay_data,"INSERT");
	$inrepay_id = $GLOBALS['db']->insert_id();
	if($inrepay_id==0){
		$root["show_err"] = "对不起，数据处理失败，请联系客服！";
		return $root;
	}

	//录入还款列表
	$after_time = $GLOBALS['db']->getOne("SELECT repay_time FROM ".DB_PREFIX."deal_repay WHERE deal_id=".$id." ORDER BY repay_time DESC");
	if($after_time==""){
		$after_time = $deal['repay_start_time'];
	}

	$temp_ids[] = array();
	for($i=0;$i<($deal['repay_time']-$has_repay_count);$i++){
		$repay_data['id'] = $v['repay_id'];
		$repay_data['has_repay'] = 1;
		$repay_data['deal_id'] = $id;
		$repay_data['user_id'] = $GLOBALS['user_info']['id'];
		$repay_data['repay_time'] = $after_time = next_replay_month($after_time);
		$repay_data['true_repay_time'] = $time;
		$repay_data['status'] = 0;
		if($i==0){
			$repay_data['repay_money'] = round($deal['month_repay_money'],2);
			$repay_data['impose_money'] = round($impose_money,2);
			$repay_data['manage_money'] = round($deal['month_manage_money']);
		}
		else{
			if($deal['loantype']==0){//等额本息
				$repay_data['repay_money'] = $benjin/($deal['repay_time']-$has_repay_count);
			}
			elseif($deal['loantype']==1){//每月还息
				if($i+1==($deal['repay_time']-$has_repay_count)){
					$repay_data['repay_money'] = $benjin;
				}
				else{
					$repay_data['repay_money'] =0;
				}
			}
			elseif($deal['loantype']==2){//每月还息
				if($i+1==($deal['repay_time']-$has_repay_count)){
					$repay_data['repay_money'] = $benjin;
				}
				else{
					$repay_data['repay_money'] =0;
				}
			}
			$repay_data['impose_money'] = 0;
			$repay_data['manage_money'] = 0;
		}
		$deal_repay_id = $v['repay_id'];
		$GLOBALS['db']->autoExecute(DB_PREFIX."deal_repay",$repay_data,"UPDATE","id=".$deal_repay_id);
		
		//假如出错 删除掉原来的以插入的数据
		if($GLOBALS['db']->affected_rows() == 0)
		{
			if($temp_ids){
				$GLOBALS['db']->query("UPDATE ".DB_PREFIX."deal_repay set has_repay = 0 WHERE id in (".implode(",",$temp_ids).")");
			}
			$root["show_err"] = "对不起，处理数据失败请联系客服！";
			return $root;
		}
		else{
			$temp_ids[] = $deal_repay_id;
		}
	}

	//更新用户账户资金记录
	require APP_ROOT_PATH.'system/libs/user.php';
	modify_account(array("money"=>-round($impose_money)),$GLOBALS['user_info']['id'],"标:".$deal['id'].",提前还款违约金");
	modify_account(array("money"=>-round(($total_repay_money+$deal['month_manage_money']),2)),$GLOBALS['user_info']['id'],"标:".$deal['id'].",提前还款");

	//用户获得额度
	modify_account(array("quota"=>trim(app_conf('USER_REPAY_QUOTA'))),$GLOBALS['user_info']['id'],"标:".$deal['id'].",还清借款获得额度");

	$content = "您好，您在".app_conf("SHOP_TITLE")."的借款 “<a href=\"".$deal['url']."\">".$deal['name']."</a>”成功提前还款".round($true_total_repay_money,2)."元，";
	$content .= "其中违约金为:".round($impose_money,2)."元,本笔借款已还款完毕！";

	send_user_msg("",$content,0,$GLOBALS['user_info']['id'],$time,0,true,8);
	//短信通知
	if(app_conf("SMS_ON")==1&&app_conf('SMS_SEND_REPAY')==1){
		$sms_content = "尊敬的".app_conf("SHOP_TITLE")."用户".$GLOBALS['user_info']['user_name']."，您成功提前还款".round($true_total_repay_money,2)."元，其中违约金为:".round($impose_money,2)."元,感谢您的关注和支持。【".app_conf("SHOP_TITLE")."】";
		$msg_data['dest'] = $GLOBALS['user_info']['mobile'];
		$msg_data['send_type'] = 0;
		$msg_data['title'] = $msg_data['content'] = addslashes($sms_content);
		$msg_data['send_time'] = 0;
		$msg_data['is_send'] = 0;
		$msg_data['create_time'] = $time;
		$msg_data['user_id'] = $GLOBALS['user_info']['id'];
		$msg_data['is_html'] = 0;
		$GLOBALS['db']->autoExecute(DB_PREFIX."deal_msg_list",$msg_data); //插入
	}

	//判断获取的信用是否超过限制
	if($GLOBALS['db']->getOne("SELECT sum(point) FROM ".DB_PREFIX."user_log WHERE (log_info='标:".$deal['id'].",还清借款' or log_info='还清借款') AND user_id=".$GLOBALS['user_info']['id']) < (int)trim(app_conf('CONF_REPAY_SUCCESS_LIMIT'))){
		//获取上一次还款时间
		$befor_repay_time = $GLOBALS['db']->getOne("SELECT MAX(log_time) FROM ".DB_PREFIX."user_log WHERE (log_info='标:".$deal['id'].",还清借款' or log_info='还清借款') AND user_id=".$GLOBALS['user_info']['id']);
		$day = ceil(($time-$befor_repay_time)/24/3600);
		//当天数大于等于间隔时间 获得信用
		if($day >= (int)trim(app_conf('REPAY_SUCCESS_DAY'))){
			modify_account(array("point"=>trim(app_conf('REPAY_SUCCESS_POINT'))),$GLOBALS['user_info']['id'],"标:".$deal['id'].",还清借款");
		}
			
		//用户获得额度
		modify_account(array("quota"=>trim(app_conf('USER_REPAY_QUOTA'))),$GLOBALS['user_info']['id'],"标:".$deal['id'].",还清借款获得额度");
	}

	syn_deal_status($id);
	sys_user_status($GLOBALS['user_info']['id'],false,true);


	//用户回款
	$user_loan_list = get_deal_user_load_list($deal);

	foreach($user_loan_list as $lllk=>$lllv){
		
		foreach($lllv as $kk=>$vv){
			
			//本金
			$user_self_money = 0;
			//本息
			$user_repay_money = 0;
			//违约金
			$user_impose_money = 0;
			//管理费
			$user_manage_money = 0;
			
			$in_user_id = $vv['user_id'];
			//判断是否转让了债权
			if($vv['t_user_id'] > 0){
				$in_user_id = $vv['t_user_id'];
				$loan_user_info['user_name'] = $vv['user_name'];
				$loan_user_info['t_email'] = $vv['email'];
				$loan_user_info['t_mobile'] = $vv['mobile'];
			}
			else{
				$loan_user_info['user_name'] = $vv['t_user_name'];
				$loan_user_info['t_email'] = $vv['t_email'];
				$loan_user_info['t_mobile'] = $vv['t_mobile'];
			}
			
			//借入者已还款，但是没打款到借出用户中心
			if($vv['has_repay']==0){
				$user_load_data['deal_id'] = $v['deal_id'];
				$user_load_data['user_id'] = $v['user_id'];
				$user_load_data['repay_time'] = $vv['repay_day'];
				$user_load_data['true_repay_time'] = $time;
				$user_load_data['is_site_repay'] = 0;
				$user_load_data['status'] = 0;
					
				//小于提前还款按正常还款
				if($vv['repay_day'] < $k_repay_time){

					//等额本息的时候才通过公式计算剩余多少本金
					$user_load_data['self_money'] = $vv['self_money'];
					
					$user_load_data['repay_money'] = $vv['month_repay_money'];
					$user_load_data['manage_money'] = $vv['month_manage_money'];
					$user_load_data['impose_money'] = $vv['impose_money'];
					if($vv['status']>0)
						$user_load_data['status'] = $vv['status'] - 1;

					$content = "您好，您在".app_conf("SHOP_TITLE")."的投标 “<a href=\"".$deal['url']."\">".$deal['name']."</a>”成功还款".number_format(($vv['month_repay_money']+$vv['impose_money']),2)."元，";
					$unext_loan = $user_loan_list[$kk+1];
					if($unext_loan){
						$content .= "本笔投标的下个还款日为".to_date($unext_loan['repay_day'],"Y年m月d日")."，需要本息".number_format($unext_loan['month_repay_money'],2)."元。";
					}
					$user_self_money +=(float)$user_load_data['self_money'];
					if($user_load_data['impose_money']!=0||$user_load_data['manage_money']!=0||$user_load_data['repay_money']!=0){
						
						//更新用户账户资金记录
						modify_account(array("money"=>$user_load_data['impose_money']),$in_user_id,"标:".$deal['id'].",期:".($kk+1).",逾期罚息");
							
						modify_account(array("money"=>-$user_load_data['manage_money']),$in_user_id,"标:".$deal['id'].",期:".($kk+1).",投标管理费");
							
						modify_account(array("money"=>$user_load_data['repay_money']),$in_user_id,"标:".$deal['id'].",期:".($kk+1).",回报本息");
							
						$msg_conf = get_user_msg_conf($in_user_id);
						//站内信
						if($msg_conf['sms_bidrepaid']==1)
							send_user_msg("",$content,0,$in_user_id,$time,0,true,9);
						//邮件
						if($msg_conf['mail_bidrepaid']==1){

						}
					}
				}
				//提前还款
				//当前提前还款的第一个月
				else{

					if($vv['repay_day'] == $k_repay_time){
						if($deal['loantype']==0){//等额本息的时候才通过公式计算剩余多少本金
							$user_load_data['self_money'] = $vv['month_repay_money'] - get_benjin($kk,$deal['repay_time'],$v['money'],$vv['month_repay_money'],$deal['rate'])*$deal['rate']/12/100;
							$user_load_data['impose_money'] = ($user_load_data['self_money'] - $vv['month_repay_money'] + $user_load_data['self_money']*$v['rate']) * (int)trim(app_conf('COMPENSATE_FEE'))/100;
						}
						elseif($deal['loantype']==1){//每月还息，到期还本
							$user_load_data['self_money'] = $vv['money'];
							$user_load_data['impose_money'] = $vv['money'] * floatval(trim($deal['compensate_fee']))/100;
						}
						elseif($deal['loantype']==2){//每月还息，到期还本
							$user_load_data['self_money'] = $vv['money'];
							$user_load_data['impose_money'] = $vv['money'] * floatval(trim($deal['compensate_fee']))/100;
						}

						$user_self_money +=(float)$user_load_data['self_money'];
							
						if($deal['loantype']==0){//等额本息的时候才通过公式计算剩余多少本金
							$user_load_data['repay_money'] = $vv['month_repay_money'];
							$user_load_data['manage_money'] = $vv['month_manage_money'];
						}
						elseif($deal['loantype']==1){
							$user_load_data['repay_money'] = $vv['month_repay_money'] + $v['money'];
							$user_load_data['manage_money'] = $vv['month_manage_money'];
						}
						elseif($deal['loantype']==2){
							$user_load_data['repay_money'] = $vv['money'];
							$user_load_data['manage_money'] = $vv['money'] * floatval(trim($deal['user_loan_manage_fee'])) /100 * ($kk +1) ;
						}
							
							
						$user_repay_k = $kk+1;
					}
					else{
						//其他月份
							
						//等额本息
						if($deal['loantype']==0){
							if($user_self_money == 0){
								$user_load_data['self_money'] = $vv['month_repay_money'] - get_benjin($kk,$deal['repay_time'],$v['money'],$vv['month_repay_money'],$deal['rate'])*$deal['rate']/12/100;
								$user_load_data['impose_money'] = ($user_load_data['self_money'] - $vv['month_repay_money'] + $user_load_data['self_money']*$v['rate']) * (int)trim(app_conf('COMPENSATE_FEE'))/100;
							}
							else{
								$user_load_data['self_money'] = $user_load_data['repay_money'] = ($v['money'] - $user_self_money)/($v['repay_time']-$user_repay_k);
								$user_load_data['manage_money'] = 0;
								$user_load_data['impose_money'] = 0;
							}
						}
						//每月还息，到期还本
						elseif($deal['loantype']==1){
							if($user_self_money == 0){
								$user_self_money = $user_load_data['self_money'] = $v['money'];
								$user_load_data['repay_money'] = $vv['month_repay_money'] + $v['money'];
								$user_load_data['impose_money'] = $vv['money'] * floatval(trim($deal['compensate_fee']))/100;
								$user_load_data['manage_money'] = $vv['month_manage_money'];
							}
							else{
								$user_load_data['self_money'] = $user_load_data['repay_money'] = 0;
								$user_load_data['manage_money'] = 0;
								$user_load_data['impose_money'] = 0;
							}
						}
						//到期还本息
						elseif($deal['loantype']==2){
							if($user_self_money == 0){
								$user_self_money = $user_load_data['self_money'] = $v['money'];
								$user_load_data['repay_money'] = $vv['month_repay_money'] + $v['money'];
								$user_load_data['impose_money'] = $vv['money'] * floatval(trim($deal['compensate_fee']))/100;
								$user_load_data['manage_money'] = $vv['money'] * floatval(trim($deal['user_loan_manage_fee'])) /100 * ($kk +1) ;
							}
							else{
								$user_load_data['self_money'] = $user_load_data['repay_money'] = 0;
								$user_load_data['manage_money'] = 0;
								$user_load_data['impose_money'] = 0;
							}
						}
							
					}

					$user_repay_money += (float)$user_load_data['repay_money'];
					$user_impose_money += (float)$user_load_data['impose_money'];
					$user_manage_money += (float)$user_load_data['manage_money'];
					$user_load_data['l_key'] = $kk;
					$user_load_data['u_key'] = $k;
				}
				$user_load_data['has_repay'] = 1;
				$GLOBALS['db']->autoExecute(DB_PREFIX."deal_load_repay",$user_load_data,"INSERT");
					
			}
			
			if($user_repay_money >0){
				$all_repay_money = round($GLOBALS['db']->getOne("SELECT (sum(repay_money)-sum(self_money) + sum(impose_money)) as shouyi FROM ".DB_PREFIX."deal_load_repay WHERE deal_id=".$v['deal_id']." AND user_id=".$v['user_id']),2);
				$all_impose_money = round($GLOBALS['db']->getOne("SELECT sum(impose_money) FROM ".DB_PREFIX."deal_load_repay WHERE deal_id=".$v['deal_id']." AND user_id=".$v['user_id']),2);
	
				$content = "您好，您在".app_conf("SHOP_TITLE")."的投标 “<a href=\"".$deal['url']."\">".$deal['name']."</a>”提前还款,";
				$content .= "本次投标共获得收益:".$all_repay_money."元,其中违约金为:".$all_impose_money."元,本次投标已回款完毕！";
	
				//更新用户账户资金记录
				modify_account(array("money"=>$user_impose_money),$in_user_id,"标:".$deal['id'].",违约金");
	
				modify_account(array("money"=>-$user_manage_money),$in_user_id,"标:".$deal['id'].",投标管理费");
	
				modify_account(array("money"=>$user_repay_money),$in_user_id,"标:".$deal['id'].",回报本息");
	
				$msg_conf = get_user_msg_conf($in_user_id);
				//短信通知
				if(app_conf("SMS_ON")==1&&app_conf('SMS_REPAY_TOUSER_ON')==1){
						
					$tmpl = $GLOBALS['db']->getRowCached("select * from ".DB_PREFIX."msg_template where name = 'TPL_DEAL_LOAD_REPAY_SMS'");
					$tmpl_content = $tmpl['content'];
						
					$notice['user_name'] = $loan_user_info['user_name'];
					$notice['deal_name'] = $deal['sub_name'];
					$notice['deal_url'] = $deal['url'];
					$notice['site_name'] = app_conf("SHOP_TITLE");
					$notice['repay_money'] = $vv['month_repay_money']+$vv['impose_money'];
						
					$notice['all_repay_money'] = $all_repay_money;
					$notice['impose_money'] = $all_impose_money;
						
					$GLOBALS['tmpl']->assign("notice",$notice);
					$sms_content = $GLOBALS['tmpl']->fetch("str:".$tmpl_content);
						
					$msg_data['dest'] = $loan_user_info['mobile'];
					$msg_data['send_type'] = 0;
					$msg_data['title'] = $msg_data['content'] = addslashes($sms_content);
					$msg_data['send_time'] = 0;
					$msg_data['is_send'] = 0;
					$msg_data['create_time'] = $time;
					$msg_data['user_id'] = $in_user_id;
					$msg_data['is_html'] = 0;
					$GLOBALS['db']->autoExecute(DB_PREFIX."deal_msg_list",$msg_data); //插入
				}
				//站内信
				if($msg_conf['sms_bidrepaid']==1)
					send_user_msg("",$content,0,$in_user_id,$time,0,true,9);
				//邮件
				if($msg_conf['mail_bidrepaid']==1 && app_conf('MAIL_ON')==1){
					
					$tmpl = $GLOBALS['db']->getRowCached("select * from ".DB_PREFIX."msg_template where name = 'TPL_DEAL_LOAD_REPAY_EMAIL'");
					$tmpl_content = $tmpl['content'];
						
					$notice['user_name'] = $loan_user_info['user_name'];
					$notice['deal_name'] = $deal['sub_name'];
					$notice['deal_url'] = $deal['url'];
					$notice['site_name'] = app_conf("SHOP_TITLE");
					$notice['site_url'] = SITE_DOMAIN.APP_ROOT;
					$notice['help_url'] = SITE_DOMAIN.url("index","helpcenter");
					$notice['msg_cof_setting_url'] = SITE_DOMAIN.url("index","uc_msg#setting");
					$notice['repay_money'] = $vv['month_repay_money']+$vv['impose_money'];
						
					$notice['all_repay_money'] = $all_repay_money;
					$notice['impose_money'] = $all_impose_money;
						
					$GLOBALS['tmpl']->assign("notice",$notice);
						
					$msg = $GLOBALS['tmpl']->fetch("str:".$tmpl_content);
					$msg_data['dest'] = $loan_user_info['email'];
					$msg_data['send_type'] = 1;
					$msg_data['title'] = "“".$deal['name']."”回款通知";
					$msg_data['content'] = addslashes($msg);
					$msg_data['send_time'] = 0;
					$msg_data['is_send'] = 0;
					$msg_data['create_time'] = $time;
					$msg_data['user_id'] = $in_user_id;
					$msg_data['is_html'] = $tmpl['is_html'];
					$GLOBALS['db']->autoExecute(DB_PREFIX."deal_msg_list",$msg_data); //插入
				}
			}
		}
			
	}
	$root["status"] = 1;//0:出错;1:正确;
	$root["show_err"] = "操作成功!";
	return $root;
}



//债权转让常规检测;
function check_trans($id,$paypassword){
	$paypassword = strim($paypassword);
	$id = intval($id);

	$root = array();
	$root["status"] = 0;//0:出错;1:正确;

	if(!$GLOBALS['user_info']){
		$root["show_err"] = $GLOBALS['lang']['PLEASE_LOGIN_FIRST'];
		return $root;
	}


	if($paypassword==""){
		$root["show_err"] = $GLOBALS['lang']['PAYPASSWORD_EMPTY'];
		return $root;
	}

	if(md5($paypassword)!=$GLOBALS['user_info']['paypassword']){
		$root["show_err"] = $GLOBALS['lang']['PAYPASSWORD_ERROR'];//.$GLOBALS['user_info']['paypassword'].';'.md5($paypassword).';'.$paypassword;
		return $root;
	}



	$deal_id = $GLOBALS['db']->getOne("SELECT deal_id FROM ".DB_PREFIX."deal_load_transfer WHERE id=".$id);
	if($deal_id==0){
		$root["show_err"] = "不存在的债权";
		return $root;
	}
	else{
		syn_deal_status($deal_id);
	}

	$condition = ' AND dlt.id='.$id.' AND d.deal_status = 4 and d.is_effect=1 and d.is_delete=0 and d.loantype = 0 and d.repay_time_type =1 and  d.publish_wait=0 ';
	$union_sql = " LEFT JOIN ".DB_PREFIX."deal_load_transfer dlt ON dlt.deal_id = dl.deal_id ";

	$sql = 'SELECT dlt.load_id,dlt.id,dlt.t_user_id,dlt.transfer_amount,dlt.user_id,dlt.near_repay_time,d.next_repay_time,d.last_repay_time,d.rate,d.repay_start_time,d.repay_time,dlt.load_money,dlt.id as dltid,dlt.status as tras_status,dlt.t_user_id,dlt.transfer_amount,dlt.create_time as tras_create_time,d.user_id as duser_id,d.ips_bill_no FROM '.DB_PREFIX.'deal_load dl LEFT JOIN '.DB_PREFIX.'deal d ON d.id = dl.deal_id '.$union_sql.' WHERE 1=1 '.$condition;

	$transfer = $GLOBALS['db']->getRow($sql);

	if($transfer){
		if($transfer['user_id']==$GLOBALS['user_info']['id']){
			$root["show_err"] = "不能购买自己转让的债权";
			return $root;
		}
			
		if($transfer['duser_id']==$GLOBALS['user_info']['id']){
			$root["show_err"] = "不能购买自己的的借贷债权";
			return $root;
		}
			
		if($transfer['tras_status']==0){
			$root["show_err"] = "债权已撤销";
			return $root;
		}
			
		if(intval($transfer['t_user_id'])>0){
			$root["show_err"] = "债权已转让";
			return $root;
		}

		//下个还款日
		if(intval($transfer['next_repay_time']) == 0){
			$transfer['next_repay_time'] = next_replay_month($transfer['repay_start_time']);
		}
			
		if($transfer['next_repay_time'] - TIME_UTC  + 24*3600 - 1 <= 0){
			$root["show_err"] = "债权转让已过期";
			return $root;
		}
			
		$root["transfer"] = $transfer;
		$root["deal_id"] = $deal_id;
	}
	else{
		$root["show_err"] = "债权转让不存在";
		return $root;
	}
	if($transfer['ips_bill_no']!="")
		$root["status"] = 2;
	else
		$root["status"] = 1;//0:出错;1:正确;
	return $root;
}

//债权转让;
function dotrans($id,$paypassword){
	$paypassword = strim($paypassword);
	$id = intval($id);

	$root = array();
	$root["status"] = 0;//0:出错;1:正确;
	
	$result = check_trans($id,$paypassword);
	
	if ($result['status'] == 0){
		$root["show_err"] = $result["show_err"];
		return $root;
	}
	
	if ($result['status'] == 2){
		$root["status"] = 2;
		$root["jump"] = APP_ROOT."/index.php?ctl=collocation&act=RegisterCretansfer&id=$id&t_user_id=".$GLOBALS['user_info']['id']."&paypassword=".$paypassword;
		return $root;
	}
	
	$transfer = $result["transfer"];
	$deal_id = $result["deal_id"];
	

	if($transfer){		
		if(floatval($transfer['transfer_amount']) > floatval($GLOBALS['user_info']['money'])){
			$root["show_err"] = "账户余额不足";
			return $root;
		}
			
		$GLOBALS['db']->query("UPDATE ".DB_PREFIX."deal_load_transfer set t_user_id = ".$GLOBALS['user_info']['id'].",transfer_time='".TIME_UTC."' WHERE id=".$id." and t_user_id =0 AND status=1 AND near_repay_time- ".TIME_UTC." + 24*3600 - 1 > 0 ");
		if($GLOBALS['db']->affected_rows()){
			require APP_ROOT_PATH."/system/libs/user.php";
			//承接人扣除转让费
			modify_account(array("money"=>-floatval($transfer['transfer_amount'])),$GLOBALS['user_info']['id'],"债：Z-".$transfer['load_id'].",承接金");
			//转让人接受转让费
			modify_account(array("money"=>floatval($transfer['transfer_amount'])),$transfer['user_id'],"债：Z-".$transfer['load_id'].",转让金");
			
			$user_loan_transfer_fee = $GLOBALS['db']->getOne("SELECT d.user_loan_transfer_fee FROM ".DB_PREFIX."deal d LEFT JOIN ".DB_PREFIX."deal_load_transfer dlt ON dlt.deal_id=d.id where dlt.id=".$id);
			//扣除转让人的手续费
			if(trim($user_loan_transfer_fee)!=""){
				$transfer_fee = $transfer['transfer_amount']*floatval(trim($user_loan_transfer_fee));
				if($transfer_fee!=0){
					$transfer_fee = $transfer_fee / 100;
				}
				modify_account(array("money"=>-floatval($transfer_fee)),$transfer['user_id'],"债:Z-".$transfer['load_id'].",转让管理费");
			}
				
			
			dotrans_ok($id);
			

			$root["status"] = 1;//0:出错;1:正确;
			$root["show_err"] = "转让成功";
			return $root;
		}
		else{
			$root["show_err"] = "转让失败";
			return $root;
		}
	}
	else{
		$root["show_err"] = "债权转让不存在";
		return $root;
	}

}

function dotrans_ok($transfer_id){
	
	$transfer = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."deal_load_transfer where id = ".$transfer_id);
	//发送消息
	$msg_conf = get_user_msg_conf($transfer['user_id']);
	if($msg_conf['sms_transfer']==1 || $msg_conf['mail_transfer']==1){
		$transfer['tuser'] = get_user("user_name,email",$transfer['t_user_id']);
		$transfer['user'] = get_user("user_name,email",$transfer['user_id']);
	}
	
	if($msg_conf['sms_transfer']==1){
			
		$content = "您好，您在".app_conf("SHOP_TITLE")."的债权 “<a href=\"".url("index","transfer#detail",array("id"=>$transfer['id']))."\">Z-".$transfer['load_id']."</a>” 成功转让给：<a href=\"".$transfer['tuser']['url']."\">".$transfer['tuser']['user_name']."</a>";
		send_user_msg("",$content,0,$transfer['user_id'],TIME_UTC,0,true,18);
	}
	//邮件
	if($msg_conf['mail_transfer']==1 && app_conf('MAIL_ON')==1){
		$tmpl = $GLOBALS['db']->getRowCached("select * from ".DB_PREFIX."msg_template where name = 'TPL_MAIL_TRANSFER_SUCCESS'");
		$tmpl_content = $tmpl['content'];
			
		$notice['user_name'] = $transfer['user']['user_name'];
		$notice['transfer_time'] = to_date($transfer['create_time'],"Y年m月d日");
		$notice['transfer_id'] = "Z-".$transfer['load_id'];
		$notice['deal_url'] = SITE_DOMAIN.url("index","transfer#detail",array("id"=>$transfer['id']));
		$notice['site_name'] = app_conf("SHOP_TITLE");
		$notice['site_url'] = SITE_DOMAIN.APP_ROOT;
		$notice['help_url'] = SITE_DOMAIN.url("index","helpcenter");
		$notice['msg_cof_setting_url'] = SITE_DOMAIN.url("index","uc_msg#setting");
			
	
			
		$GLOBALS['tmpl']->assign("notice",$notice);
			
		$msg = $GLOBALS['tmpl']->fetch("str:".$tmpl_content);
		$msg_data['dest'] = $transfer['user']['email'];
		$msg_data['send_type'] = 1;
		$msg_data['title'] = "“债权：Z-".$transfer['load_id']."”转让通知";
		$msg_data['content'] = addslashes($msg);
		$msg_data['send_time'] = 0;
		$msg_data['is_send'] = 0;
		$msg_data['create_time'] = TIME_UTC;
		$msg_data['user_id'] = $transfer['user_id'];
		$msg_data['is_html'] = $tmpl['is_html'];
		$GLOBALS['db']->autoExecute(DB_PREFIX."deal_msg_list",$msg_data); //插入
	}
	
	if(app_conf('SMS_ON')==1){
		$tmpl = $GLOBALS['db']->getRowCached("select * from ".DB_PREFIX."msg_template where name = 'TPL_SMS_TRANSFER_SUCCESS'");
		$tmpl_content = $tmpl['content'];
			
		$notice['user_name'] = $transfer['user']['user_name'];
		$notice['transfer_time'] = to_date($transfer['create_time'],"Y年m月d日");
		$notice['transfer_id'] = "Z-".$transfer['load_id'];
		$notice['site_name'] = app_conf("SHOP_TITLE");
			
			
		$GLOBALS['tmpl']->assign("notice",$notice);
			
		$msg = $GLOBALS['tmpl']->fetch("str:".$tmpl_content);
		$msg_data['dest'] = $transfer['user']['mobile'];
		$msg_data['send_type'] = 0;
		$msg_data['title'] = "“债权：Z-".$transfer['load_id']."”转让通知";
		$msg_data['content'] = addslashes($msg);
		$msg_data['send_time'] = 0;
		$msg_data['is_send'] = 0;
		$msg_data['create_time'] = TIME_UTC;
		$msg_data['user_id'] = $transfer['user_id'];
		$msg_data['is_html'] = $tmpl['is_html'];
		$GLOBALS['db']->autoExecute(DB_PREFIX."deal_msg_list",$msg_data); //插入
	}
	
	//发送债权协议
	send_transfer_contract_email($transfer_id);	
}
	
?>