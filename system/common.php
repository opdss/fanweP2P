<?php
// +----------------------------------------------------------------------
// | Fanwe 方维p2p借贷系统
// +----------------------------------------------------------------------
// | Copyright (c) 2011 http://www.fanwe.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 云淡风轻(88522820@qq.com)
// +----------------------------------------------------------------------

//前后台加载的函数库
require_once 'system_init.php';

//获取真实路径
function get_real_path()
{
	return APP_ROOT_PATH;
}

//获取GMTime
function get_gmtime()
{
	return (time() - date('Z'));
}

function to_date($utc_time, $format = 'Y-m-d H:i:s') {
	if (empty ( $utc_time )) {
		return '';
	}
	$timezone = intval(app_conf('TIME_ZONE'));
	$time = $utc_time + $timezone * 3600; 
	return date ($format, $time );
}

function to_timespan($str, $format = 'Y-m-d H:i:s')
{
	$timezone = intval(app_conf('TIME_ZONE'));
	//$timezone = 8; 
	$time = intval(strtotime($str));
	if($time!=0)
	$time = $time - $timezone * 3600;
    return $time;
}


function valid_tag($str)
{
	return preg_replace("/<(?!div|ol|ul|li|sup|sub|span|br|img|p|h1|h2|h3|h4|h5|h6|\/div|\/ol|\/ul|\/li|\/sup|\/sub|\/span|\/br|\/img|\/p|\/h1|\/h2|\/h3|\/h4|\/h5|\/h6|blockquote|\/blockquote|strike|\/strike|b|\/b|i|\/i|u|\/u)[^>]*>/i","",$str);
}

/**
 * 获取指定时间与当前时间的时间间隔
 *
 * @access  public
 * @param   integer      $time
 *
 * @return  string
 */
function getBeforeTimelag($time)
{
	if($time == 0)
		return "";
	
	static $today_time = NULL,
			$before_lang = NULL,
			$beforeday_lang = NULL,
			$today_lang = NULL,
			$yesterday_lang = NULL,
			$hours_lang = NULL,
			$minutes_lang = NULL,
			$months_lang = NULL,
			$date_lang = NULL,
			$sdate = 86400;
	
	if($today_time === NULL)
	{
		$today_time = TIME_UTC;
		$before_lang = '前';
		$beforeday_lang = '前天';
		$today_lang = '今天';
		$yesterday_lang = '昨天';
		$hours_lang = '小时';
		$minutes_lang = '分钟';
		$months_lang =  '月';
		$date_lang =  '日';
	}
	
	$now_day = to_timespan(to_date($today_time,"Y-m-d")); //今天零点时间 
	$pub_day = to_timespan(to_date($time,"Y-m-d")); //发布期零点时间

	$timelag = $now_day - $pub_day;
	
	$year_time = to_date($time,'Y');
	$today_year = to_date($today_time,'Y');
	
	if($year_time < $today_year)
		return to_date($time,'Y:m:d H:i');
		
	$timelag_str = to_date($time,' H:i');
	
	$day_time = 0;
	if($timelag / $sdate >= 1)
	{
		$day_time = floor($timelag / $sdate);
		$timelag = $timelag % $sdate;
	}
	
	switch($day_time)
	{
		case '0':
			$timelag_str = $today_lang.$timelag_str;
		break;
		
		case '1':
			$timelag_str = $yesterday_lang.$timelag_str;
		break;
		
		case '2':
			$timelag_str = $beforeday_lang.$timelag_str;
		break;
		
		default:
			$timelag_str = to_date($time,'m'.$months_lang.'d'.$date_lang.' H:i');
		break;
	}
	return $timelag_str;
}

//获取客户端IP
function get_client_ip() {
	//使用wap时，是通过中转方式，所以要在wap/index.php获取客户ip,转入到:sjmapi上 chenfq by add 2014-12-17
	if (isset($GLOBALS['request']['client_ip']) && !empty($GLOBALS['request']['client_ip']))
		$ip = $GLOBALS['request']['client_ip'];
	else if (getenv ( "HTTP_CLIENT_IP" ) && strcasecmp ( getenv ( "HTTP_CLIENT_IP" ), "unknown" ))
		$ip = getenv ( "HTTP_CLIENT_IP" );
	else if (getenv ( "HTTP_X_FORWARDED_FOR" ) && strcasecmp ( getenv ( "HTTP_X_FORWARDED_FOR" ), "unknown" ))
		$ip = getenv ( "HTTP_X_FORWARDED_FOR" );
	else if (getenv ( "REMOTE_ADDR" ) && strcasecmp ( getenv ( "REMOTE_ADDR" ), "unknown" ))
		$ip = getenv ( "REMOTE_ADDR" );
	else if (isset ( $_SERVER ['REMOTE_ADDR'] ) && $_SERVER ['REMOTE_ADDR'] && strcasecmp ( $_SERVER ['REMOTE_ADDR'], "unknown" ))
		$ip = $_SERVER ['REMOTE_ADDR'];
	else
		$ip = "unknown";
	return strim($ip);
}

//过滤注入
function filter_injection(&$request)
{
	$pattern = "/(select[\s])|(insert[\s])|(update[\s])|(delete[\s])|(from[\s])|(where[\s])/i";
	foreach($request as $k=>$v)
	{
				if(preg_match($pattern,$k,$match))
				{
						die("SQL Injection denied!");
				}
		
				if(is_array($v))
				{					
					filter_injection($v);
				}
				else
				{					
					
					if(preg_match($pattern,$v,$match))
					{
						die("SQL Injection denied!");
					}					
				}
	}
	
}

//过滤请求
function filter_request(&$request)
{
		if(MAGIC_QUOTES_GPC)
		{
			foreach($request as $k=>$v)
			{
				if(is_array($v))
				{
					filter_request($request[$k]);
				}
				else
				{
					$request[$k] = stripslashes(trim($v));
				}
			}
		}
		
}

function adddeepslashes(&$request)
{

			foreach($request as $k=>$v)
			{
				if(is_array($v))
				{
					adddeepslashes($request[$k]);
				}
				else
				{
					$request[$k] = addslashes(trim($v));
				}
			}		
}

//request转码
function convert_req(&$req)
{
	foreach($req as $k=>$v)
	{
		if(is_array($v))
		{
			convert_req($req[$k]);
		}
		else
		{
			if(!is_u8($v))
			{
				$req[$k] = iconv("gbk","utf-8",$v);
			}
		}
	}
}

function stripdeepslashes(&$request)
{

	if(is_array($request))
	{
			foreach($request as $k=>$v)
			{
				if(is_array($v))
				{
					stripdeepslashes($request[$k]);
				}
				else
				{
					$request[$k] = stripslashes(trim($v));
				}
			}
	}
	else
	$request = stripslashes($request);
}

function quotes($content)
{
	//if $content is an array
	if (is_array($content))
	{
		foreach ($content as $key=>$value)
		{
			//$content[$key] = mysql_real_escape_string($value);
			$content[$key] = addslashes($value);
		}
	} else
	{
		//if $content is not an array
		$content = addslashes($content);
		//mysql_real_escape_string($content);
	}
	return $content;
}

function strim($str)
{
	return quotes(htmlspecialchars(trim($str)));
}
function btrim($str)
{
	return quotes(trim($str));
}

function is_u8($string)
{
	return preg_match('%^(?:
		 [\x09\x0A\x0D\x20-\x7E]            # ASCII
	   | [\xC2-\xDF][\x80-\xBF]             # non-overlong 2-byte
	   |  \xE0[\xA0-\xBF][\x80-\xBF]        # excluding overlongs
	   | [\xE1-\xEC\xEE\xEF][\x80-\xBF]{2}  # straight 3-byte
	   |  \xED[\x80-\x9F][\x80-\xBF]        # excluding surrogates
	   |  \xF0[\x90-\xBF][\x80-\xBF]{2}     # planes 1-3
	   | [\xF1-\xF3][\x80-\xBF]{3}          # planes 4-15
	   |  \xF4[\x80-\x8F][\x80-\xBF]{2}     # plane 16
   )*$%xs', $string);
}

//清除缓存
function clear_cache()
{
		//系统后台缓存
		syn_dealing();
		clear_dir_file(get_real_path()."public/runtime/admin/Cache/");	
		clear_dir_file(get_real_path()."public/runtime/admin/Data/_fields/");		
		clear_dir_file(get_real_path()."public/runtime/admin/Temp/");	
		clear_dir_file(get_real_path()."public/runtime/admin/Logs/");	
		@unlink(get_real_path()."public/runtime/admin/~app.php");
		@unlink(get_real_path()."public/runtime/admin/~runtime.php");
		@unlink(get_real_path()."public/runtime/admin/lang.js");
		@unlink(get_real_path()."public/runtime/app/config_cache.php");	
		
		
		//数据缓存
		clear_dir_file(get_real_path()."public/runtime/app/data_caches/");				
		clear_dir_file(get_real_path()."public/runtime/app/db_caches/");
		$GLOBALS['cache']->clear();
		clear_dir_file(get_real_path()."public/runtime/data/");

		//模板页面缓存
		clear_dir_file(get_real_path()."public/runtime/app/tpl_caches/");		
		clear_dir_file(get_real_path()."public/runtime/app/tpl_compiled/");
		@unlink(get_real_path()."public/runtime/app/lang.js");	
		
		//脚本缓存
		clear_dir_file(get_real_path()."public/runtime/statics/");		
			
				
		
}
function clear_dir_file($path)
{
   if ( $dir = opendir( $path ) )
   {
            while ( $file = readdir( $dir ) )
            {
                $check = is_dir( $path. $file );
                if ( !$check )
                {
                    @unlink( $path . $file );                       
                }
                else 
                {
                 	if($file!='.'&&$file!='..')
                 	{
                 		clear_dir_file($path.$file."/");              			       		
                 	} 
                 }           
            }
            closedir( $dir );
            rmdir($path);
            return true;
   }
}

//同步未过期团购的状态
function syn_dealing()
{
	$deals = $GLOBALS['db']->getAll("select id from ".DB_PREFIX."deal where is_effect = 1 and deal_status not in (3,5) and is_delete = 0 AND load_money/borrow_amount <= 1");
	foreach($deals as $v)
	{
		syn_deal_status($v['id']);
	}
}

function check_install()
{
	if(!file_exists(get_real_path()."public/install.lock"))
	{
	    clear_cache();
		header('Location:'.APP_ROOT.'/install');
		exit;
	}
}

//同步XXID的团购商品的状态,time_status,buy_status
function syn_deal_status($id)
{
	$deals_time = TIME_UTC;
	$deal_info = $GLOBALS['db']->getRow("select *,(start_time + enddate*24*3600 - ".$deals_time.") as remain_time,(load_money/borrow_amount*100) as progress_point from ".DB_PREFIX."deal where id = ".$id);
	
	if($deal_info['deal_status'] == 5){
		return true;
	}
	
	if($deal_info['deal_status']!=3){
		if($deal_info['progress_point'] <100){
			$data['load_money'] = $GLOBALS['db']->getOne("SELECT sum(money) FROM ".DB_PREFIX."deal_load  WHERE deal_id=$id ");
			$data['progress_point'] = $deal_info['progress_point'] = round($data['load_money']/$deal_info['borrow_amount']*100,2);
		}
		
		if(($deal_info['progress_point'] >=100 || $data['progress_point'] >=100) && floatval($deal_info['load_money']) >=floatval($deal_info['borrow_amount'])){
			
			if($GLOBALS['db']->getOne("SELECT count(*) FROM ".DB_PREFIX."deal_inrepay_repay WHERE deal_id=$id") >0){
				$data['deal_status'] = 5;
				$repay_info =  $GLOBALS['db']->getRow("SELECT sum(true_repay_money) As all_repay_money  FROM ".DB_PREFIX."deal_load_repay WHERE has_repay = 1 AND deal_id=$id");
				if($repay_info){
					$data['repay_money'] = $repay_info['all_repay_money'];
				}
				$data['last_repay_time'] = $GLOBALS['db']->getOne("SELECT true_repay_time FROM ".DB_PREFIX."deal_inrepay_repay WHERE deal_id=$id"); 
			}
			//判断是否是借款状态还是已还款完毕
			elseif(($deal_info['deal_status']==4&&$deal_info['repay_start_time']>0) || ($deal_info['deal_status']==2 && $deal_info['repay_start_time']>0 && $deal_info['repay_start_time'] <= $deals_time)){
				$repay_info =  $GLOBALS['db']->getRow("SELECT sum(true_repay_money) As all_repay_money  FROM ".DB_PREFIX."deal_load_repay WHERE has_repay=1 AND deal_id=$id");
				if(!empty($repay_info['all_repay_money'])){
					$data['repay_money'] = $repay_info['all_repay_money'];
					$last_repay_time = $GLOBALS['db']->getOne("SELECT MAX(repay_time)  FROM ".DB_PREFIX."deal_repay WHERE has_repay=1 AND deal_id=$id");;
					$data['last_repay_time'] =  $last_repay_time;
					$data['next_repay_time'] = next_replay_month($last_repay_time); 
				
				}
				elseif($deal_info['deal_status']==4){
					if($deal_info['repay_time_type'] == 0){
						$data['next_repay_time'] = $deal_info['repay_start_time'] + $deal_info['repay_time']*24*3600; 
					}
					else{
						if(is_last_repay($deal_info['loantype']))
							$data['next_repay_time'] = next_replay_month($deal_info['repay_start_time'],$deal_info['repay_time']); 
						else
							$data['next_repay_time'] = next_replay_month($deal_info['repay_start_time']); 
					}
				}
				
				//判断是否完成还款
				if($GLOBALS['db']->getOne("SELECT count(*) FROM ".DB_PREFIX."deal_repay WHERE has_repay<>1 AND deal_id=$id ") == 0){
					$data['deal_status'] = 5;
				}
				else{
					$data['deal_status'] = 4;
				}
			}
			else{
				//获取最后一次的投标记录
				if($deal_info['success_time'] == 0){
					$data['success_time'] = $deal_info['success_time'] = $GLOBALS['db']->getOne("SELECT max(create_time) FROM ".DB_PREFIX."deal_load WHERE deal_id=$id ");
				}
				
				$data['deal_status'] = 2;
			}
		}
		elseif($deal_info['remain_time'] <= 0 && $deal_info['deal_status']==1){
			//投标时间超出 更新为流标
			$data['deal_status'] = 1;
			//$data['bad_time'] = $deal_info['start_time'] + $deal_info['enddate']*24*3600;
			$data['bad_time'] = TIME_UTC;
			$data['bad_date'] = to_date(TIME_UTC,"Y-m-d");
		}
		/*elseif($deal_info['remain_time'] > 0 && $deal_info['deal_status']==0){
			$data['deal_status'] = 1;
		}*/
	}
	
	//投标人数
	$sdata = $GLOBALS['db']->getRow("SELECT count(*) as buy_count,sum(money) as load_money FROM ".DB_PREFIX."deal_load WHERE deal_id=$id");
	$data['buy_count'] = $sdata['buy_count'];
	$data['load_money'] = floatval($sdata['load_money']);
	
	//流标 移入后台手动操作
	/*if($deal_info['deal_status'] ==3 || $data['deal_status']==3){
		//流标时返还
		require_once APP_ROOT_PATH."system/libs/user.php";
		$r_load_list = $GLOBALS['db']->getAll("SELECT id,user_id,money FROM ".DB_PREFIX."deal_load WHERE is_repay=0 AND deal_id=$id");
		foreach($r_load_list as $k=>$v){
			modify_account(array("money"=>$v['money']),$v['user_id'],"标:".$deal_info['id'].",流标返还");
			$GLOBALS['db']->query("UPDATE ".DB_PREFIX."deal_load SET is_repay=1 WHERE id=".$v['id']);
		}
		//发送流标通知
		if($deal_info['is_send_bad_msg']==0){
			$data['is_send_bad_msg'] = 1;
			//发邮件
			send_deal_faild_mail_sms($id,$deal_info,$deal_info['user_id']);
			//站内信
			send_deal_faild_site_sms($id,$deal_info,$deal_info['user_id']);
			
			//添加到动态
			insert_topic("deal_bad",$id,$deal_info['user_id'],get_user_name($deal_info['user_id'],false),0);
		}
	}*/
	
	//发送流标通知
	if(($deal_info['deal_status'] ==3 || $data['deal_status']==3) && $deal_info['is_send_bad_msg']==0){
		$data['is_send_bad_msg'] = 1;
		//发邮件
		send_deal_faild_mail_sms($id,$deal_info,$deal_info['user_id']);
		//站内信
		send_deal_faild_site_sms($id,$deal_info,$deal_info['user_id']);
		
		//添加到动态
		insert_topic("deal_bad",$id,$deal_info['user_id'],get_user_name($deal_info['user_id'],false),0);
	}
	
	//放款给用户 移入后台手动操作
	/*if($deal_info['is_has_loans']==0 && $data['deal_status']==4){
		$data['is_has_loans'] = 1;
		require_once APP_ROOT_PATH."system/libs/user.php";
		modify_account(array("money"=>$deal_info['borrow_amount']),$deal_info['user_id'],"标:".$deal_info['id'].",招标成功");
		//扣除服务费
		$services_fee = $deal_info['borrow_amount']*floatval(trim($deal_info['services_fee']))/100;
		modify_account(array("money"=>-$services_fee),$deal_info['user_id'],"服务费");
		
		
		//发借款成功邮件
		send_deal_success_mail_sms($id,$deal_info);
		//发借款成功站内信
		send_deal_success_site_sms($id,$deal_info);
		
		//返利给用户
		if(floatval(app_conf("USER_BID_REBATE"))!=0){
			$load_list = $GLOBALS['db']->getAll("SELECT id,user_id,`money` FROM ".DB_PREFIX."deal_load where deal_id=".$id." and is_rebate = 0 ");
			foreach($load_list as $lk=>$lv){
				$GLOBALS['db']->query("UPDATE ".DB_PREFIX."deal_load SET is_rebate =1 WHERE id=".$lv['id']." AND is_rebate = 0 AND user_id=".$lv['user_id']);
				if($GLOBALS['db']->affected_rows()){
					modify_account(array("money"=>$lv['money']*floatval(app_conf("USER_BID_REBATE"))/100),$lv['user_id'],"标:".$id.",返利");
				}
			}
		}
	}*/
	
	$GLOBALS['db']->autoExecute(DB_PREFIX."deal",$data,"UPDATE","id=".$id);
	
	//自动投标功能
	//2013-8-15 排除了   借贷者在外 and usa.user_id <>'".$deal_info['user_id']."'
	if(app_conf("OPEN_AUTOBID") == 1 && $deal_info['ips_bill_no'] ==""){
		if(($deal_info['deal_status'] == 1 || $data['deal_status']==1) && $deal_info['remain_time'] >0 && $deals_time - $deal_info['start_time'] >=1800 && $deal_info['progress_point'] <=95 && $deal_info['repay_time_type'] == 1){
			$sql = "SELECT u.id,u.user_name,usa.fixed_amount FROM ".DB_PREFIX."user_autobid usa " .
					"LEFT JOIN ".DB_PREFIX."user u ON u.id=usa.user_id  " .
					"LEFT JOIN ".DB_PREFIX."deal d ON d.id=".$deal_info['id']." ".
					"LEFT JOIN ".DB_PREFIX."user du ON du.id = d.user_id ".
					"WHERE (usa.fixed_amount >=d.min_loan_money or d.min_loan_money = 0) AND (usa.fixed_amount <= d.max_loan_money or d.max_loan_money = 0) " .
					"AND usa.fixed_amount <= d.borrow_amount*0.2 AND usa.is_effect = 1 ".
					"AND (d.rate between usa.min_rate AND usa.max_rate) " .
					"AND (d.repay_time between usa.min_period AND usa.max_period) " .
					"AND usa.user_id not in (SELECT user_id FROM ".DB_PREFIX."deal_load WHERE deal_id=$id) and usa.user_id <>d.user_id " .
					"AND (du.level_id between usa.min_level AND usa.max_level) " .
					"AND usa.fixed_amount < (d.borrow_amount - d.load_money) ".
					"AND u.money-usa.retain_amount >= usa.fixed_amount ".
					"AND FIND_IN_SET(d.cate_id,usa.deal_cates) ".
					"GROUP BY usa.user_id ORDER BY usa.last_bid_time ASC";
			
			$autobid_user = $GLOBALS['db']->getRow($sql);
			//开始投标
			if($autobid_user && $deal_info['borrow_amount'] > $GLOBALS['db']->getOne("SELECT sum(money) FROM ".DB_PREFIX."deal_load WHERE deal_id=".$deal_info['id']) + $autobid_user['fixed_amount'])
			{
				$biddata['deal_id'] = $id;
				$biddata['money'] = $autobid_user['fixed_amount'];
				$biddata['is_auto'] = 1;
				$insertbiddata = return_deal_load_data($biddata,$autobid_user,$deal_info);
				
				$GLOBALS['db']->autoExecute(DB_PREFIX."deal_load",$insertbiddata,"INSERT");
				$load_id = $GLOBALS['db']->insert_id();
				if($load_id > 0){
					$msg = '[<a href="'.url("index","deal",array("id"=>$deal_info['id'])).'" target="_blank">'.$deal_info['name'].'</a>]的投标,付款单号'.$load_id;
					require_once APP_ROOT_PATH."system/libs/user.php";
					modify_account(array("money"=>-$autobid_user['fixed_amount'],'lock_money'=>$autobid_user['fixed_amount']),$autobid_user['id'],$msg,2);
					$GLOBALS['db']->query("UPDATE ".DB_PREFIX."user_autobid SET last_bid_time=".TIME_UTC." WHERE user_id=".$autobid_user['id']);
				}
			}
		}
	}
	
	return $data;
}

function LoadLoanModule($loantype){
	static $make_module = array();
	if(!isset($make_module[$loantype])){
		require APP_ROOT_PATH."system/loantype/loantype_".$loantype.".class.php";
		$obj_class = "loantype_".$loantype;
		$make_module[$loantype] = new $obj_class;
	}
	return $make_module[$loantype];
}

/**
 * 生成还款计划和回款计划
 */
function make_repay_plan($deal){
	$loantype = intval($deal['loantype']);
	$LoanModule = LoadLoanModule($loantype);
	$list = $LoanModule->make_repay_plan($deal);
	$total_money = array();
	foreach($list as $i=>$load_repay){
		if($old_info = $GLOBALS['db']->getRow("SELECT * FROM ".DB_PREFIX."deal_repay WHERE deal_id=".$deal['id']." AND repay_time=".$load_repay['repay_time']."  ") ){
			$repay_id = $old_info['id'];
			if($old_info['has_repay']==0){
				$load_repay['l_key'] = $i;
				$load_repay['status'] = 0;
				
				$GLOBALS['db']->autoExecute(DB_PREFIX."deal_repay",$load_repay,"UPDATE","deal_id=".$deal['id']." AND repay_time=".$load_repay['repay_time']."");
			}
			else{
				unset($load_repay['self_money']);
				unset($load_repay['repay_money']);
				unset($load_repay['has_repay']);
				unset($load_repay['manage_money']);
				unset($load_repay['manage_money_rebate']);
				$GLOBALS['db']->query("UPDATE FROM ".DB_PREFIX."deal_repay SET l_key='".$i."' WHERE deal_id=".$deal['id']." AND repay_time=".$load_repay['repay_day']."");
			}
				
		}
		else{
			
			$load_repay['l_key'] = $i;
			$load_repay['status'] = 0;
			$load_repay['has_repay'] = 0;
			
			$GLOBALS['db']->autoExecute(DB_PREFIX."deal_repay",$load_repay,"INSERT");
			$repay_id = $GLOBALS['db']->insert_id();
		}
		
		make_user_repay_plan($deal,$i,$load_repay['repay_time'],$old_info['true_repay_time'],$repay_id,$total_money);
	}
}

/**
 * 生成投标者的回款计划
 */
function make_user_repay_plan($deal,$idx,$repay_day,$true_time,$repay_id,&$total_money){
	static $fload_users;
	if(!isset($fload_users[$deal['id']])){
		$fload_users[$deal['id']] = $GLOBALS['db']->getAll("SELECT * FROM ".DB_PREFIX."deal_load  WHERE deal_id=".$deal['id']." ORDER BY id ASC ");
	}
	
	$loantype = intval($deal['loantype']);
	$LoanModule = LoadLoanModule($loantype);
	
	$load_users = $LoanModule->make_user_repay_plan($deal,$idx,$repay_day,$true_time,$repay_id,$fload_users[$deal['id']],$total_money);

	foreach($load_users as $kk=>$vv){
		$repay_data =array();
		$repay_data = $vv;
		
		if($old_info = $GLOBALS['db']->getRow("SELECT id,has_repay FROM ".DB_PREFIX."deal_load_repay WHERE deal_id=".$vv['deal_id']." AND u_key=$kk and l_key= $idx ")){
			if($old_info['has_repay']==1)
			{
				unset($repay_data['self_money']);
				unset($repay_data['repay_money']);
				unset($repay_data['interest_money']);
				unset($repay_data['manage_money']);
				unset($repay_data['repay_manage_money']);
				unset($repay_data['manage_interest_money']);
				unset($repay_data['manage_interest_money_rebate']);
				unset($repay_data['has_repay']);
			}
			$GLOBALS['db']->autoExecute(DB_PREFIX."deal_load_repay",$repay_data,"UPDATE","id=".$old_info['id']);
		}
		else{
			$GLOBALS['db']->autoExecute(DB_PREFIX."deal_load_repay",$repay_data,"INSERT");
		}
	}
	
	
	$all_money = $GLOBALS['db']->getOne("SELECT sum(repay_money) FROM ".DB_PREFIX."deal_load_repay WHERE deal_id=".$vv['deal_id']." AND l_key= $idx ");
	if($all_money != 0)
		$GLOBALS['db']->query("UPDATE ".DB_PREFIX."deal_repay SET repay_money = $all_money WHERE deal_id=".$vv['deal_id']." AND l_key= $idx");
	
}

/** 
 * 每月还款
 */
function deal_repay_money($deal){
	$loantype = intval($deal['loantype']);
	$LoanModule = LoadLoanModule($loantype);
	return $LoanModule->deal_repay_money($deal);
}

/**
 * 提前还款
 */
function inrepay_repay($loaninfo,$k,$time_utc = 0){
	$loantype = intval($loaninfo['deal']['loantype']);
	$LoanModule = LoadLoanModule($loantype);
	
	return $LoanModule->inrepay_repay($loaninfo,$k,$time_utc);
}

/**
 * 判断是否最后一起才还款
 */
function is_last_repay($loantype){
	$loantype = intval($loantype);
	$LoanModule = LoadLoanModule($loantype);
	return $LoanModule->is_last_repay();
}

/**
 * 债券转让计算 天标不支持
 * $transfer array  必要参数  load_money[投标金额] how_much_month[剩余期数] rate[费率]
 */
function deal_transfer($transfer){
	
	$loantype = intval($transfer['loantype']);
	$LoanModule = LoadLoanModule($loantype);
	return $LoanModule->transfer($transfer);
}

/**
 * 获取借款方式名称
 */
function loantypename($loantype,$type=1){
	static $loantype_info = array();
	$loantype = (int)$loantype;
	if(!isset($loantype_info[$loantype])){
		$read_modules = true;
		$loantype_info[$loantype] = require(APP_ROOT_PATH."system/loantype/loantype_".$loantype.".class.php");
	}
	if($type==1)
		return $loantype_info[$loantype]['sub_name'];
	else
		return $loantype_info[$loantype]['name'];
		
}


/**
 * 更新债权转让状态
 */
function syn_transfer_status($id=0,$deal_id=0){
	$ids = array();
	$extw = "";
	if($id > 0){
		$extw .= " AND dlt.id = ".intval($id);
	}
	
	if($deal_id > 0){
		$extw.= " AND d.id = ".intval($deal_id);
	}
	
	$tids = $GLOBALS['db']->getAll("SELECT dlt.id,dlt.load_id,dlt.create_time,dlt.user_id,dlt.t_user_id from ".DB_PREFIX."deal_load_transfer dlt LEFT join ".DB_PREFIX."deal d on d.id=dlt.deal_id WHERE dlt.status=1 and dlt.t_user_id=0 and (dlt.near_repay_time <> d.next_repay_time or dlt.near_repay_time < ".TIME_UTC." or d.deal_status = 5) ".$extw);
	if(app_conf('MAIL_ON')==1)
		$tmpl_mail = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."msg_template where name = 'TPL_MAIL_TRANSFER_FAILED'",false);
	if(app_conf('SMS_ON')==1)
		$tmpl_sms = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."msg_template where name = 'TPL_SMS_TRANSFER_FAILED'",false);
	foreach($tids as $k=>$v)
	{
		$ids[] = $v['id'];
		
		//发送消息
		$msg_conf = get_user_msg_conf($v['user_id']);
		if($msg_conf['sms_transferfail']==1 || $msg_conf['mail_transferfail']==1){
			$v['tuser'] = get_user("user_name,mobile,email",$v['t_user_id']);
			$v['user'] = get_user("user_name,mobile,email",$v['user_id']);
		}
		
		if($msg_conf['sms_transferfail']==1){
			
			$notice['shop_title']=app_conf("SHOP_TITLE");
			$notice['url']="“<a href=\"".url("index","transfer#detail",array("id"=>$v['id']))."\">Z-".$v['load_id']."</a>”";
			$notice['msg'] = "因为：“借款人还款,或借款人逾期还款”自动撤销了";
			$tmpl_content = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."msg_template where name = 'TPL_DEAL_SUCCESS_SITE_SMS'",false);
			$GLOBALS['tmpl']->assign("notice",$notice);
			$content = $GLOBALS['tmpl']->fetch("str:".$tmpl_content['content']);
			send_user_msg("",$content,0,$v['user_id'],TIME_UTC,0,true,17);
		}
		//邮件
		if($msg_conf['mail_transferfail']==1 && app_conf('MAIL_ON')==1){
			$tmpl_content = $tmpl_mail['content'];
			
			$notice['user_name'] = $v['user']['user_name'];
			$notice['transfer_time'] = to_date($v['create_time'],"Y年m月d日");
			$notice['transfer_id'] = "Z-".$v['load_id'];
			$notice['bad_msg'] = "借款人还款,或借款人逾期还款";
			$notice['deal_url'] = SITE_DOMAIN.url("index","transfer#detail",array("id"=>$v['id']));
			$notice['site_name'] = app_conf("SHOP_TITLE");
			$notice['site_url'] = SITE_DOMAIN.APP_ROOT;
			$notice['help_url'] = SITE_DOMAIN.url("index","helpcenter");
			$notice['msg_cof_setting_url'] = SITE_DOMAIN.url("index","uc_msg#setting");
			
		
			
			$GLOBALS['tmpl']->assign("notice",$notice);
			
			$msg = $GLOBALS['tmpl']->fetch("str:".$tmpl_content);
			$msg_data['dest'] = $v['user']['email'];
			$msg_data['send_type'] = 1;
			$msg_data['title'] = "债权：“Z-".$v['load_id']."”撤销通知";
			$msg_data['content'] = addslashes($msg);
			$msg_data['send_time'] = 0;
			$msg_data['is_send'] = 0;
			$msg_data['create_time'] = TIME_UTC;
			$msg_data['user_id'] = $v['user_id'];
			$msg_data['is_html'] = $tmpl_mail['is_html'];
			$GLOBALS['db']->autoExecute(DB_PREFIX."deal_msg_list",$msg_data); //插入
		}
		//短信
		if(app_conf('SMS_ON')==1){
			$tmpl_content = $tmpl_sms['content'];
			
			$notice['user_name'] = $v['user']['user_name'];
			$notice['transfer_time'] = to_date($v['create_time'],"Y年m月d日");
			$notice['transfer_id'] = "Z-".$v['load_id'];
			$notice['site_name'] = app_conf("SHOP_TITLE");
			$notice['bad_msg'] = "借款人还款,或借款人逾期还款";
			
			
			$GLOBALS['tmpl']->assign("notice",$notice);
			
			$msg = $GLOBALS['tmpl']->fetch("str:".$tmpl_content);
			$msg_data['dest'] = $v['user']['mobile'];
			$msg_data['send_type'] = 0;
			$msg_data['title'] = "债权：“Z-".$v['load_id']."”撤销通知";
			$msg_data['content'] = addslashes($msg);
			$msg_data['send_time'] = 0;
			$msg_data['is_send'] = 0;
			$msg_data['create_time'] = TIME_UTC;
			$msg_data['user_id'] = $v['user_id'];
			$msg_data['is_html'] = $tmpl_sms['is_html'];
			$GLOBALS['db']->autoExecute(DB_PREFIX."deal_msg_list",$msg_data); //插入
		}
	}
	if(count($ids) > 0)
		$GLOBALS['db']->query("UPDATE ".DB_PREFIX."deal_load_transfer SET status=0 WHERE t_user_id=0 and id in (".implode(",",$ids).") ");
}

//更新用户统计
function sys_user_status($user_id,$is_cache = false,$make_cache=false){
	if($user_id == 0)
		return ;
	$data = false;
	if($make_cache == false){
		if($is_cache == true){
			$key = md5("USER_STATICS_".$user_id);
			$data = load_dynamic_cache($key);
		}
	}
	if($data==false){
		//留言数
		$data['dp_count'] = $GLOBALS['db']->getOne("SELECT count(*) FROM ".DB_PREFIX."message use index(idx_0)  WHERE user_id=$user_id AND is_effect = 1");
		//总借款额
		$data['borrow_amount'] = $GLOBALS['db']->getOne("SELECT sum(borrow_amount) FROM ".DB_PREFIX."deal  use index(idx_0) WHERE deal_status in(4,5) AND user_id=$user_id AND publish_wait = 0 and is_delete = 0 and is_effect=1 ");
		
		$has_repay_rs = $GLOBALS['db']->getRow("SELECT sum(true_repay_money) as total_repay_money,sum(true_manage_money) as total_manage_money FROM ".DB_PREFIX."deal_repay WHERE has_repay=1 AND user_id=$user_id");
		//已还本息
		$data['repay_amount'] = $has_repay_rs['total_repay_money'];
		
		//已还管理费
		$data['repay_manage_amount'] = $has_repay_rs['total_manage_money'];
		
		//投资返利
		$data['rebate_money'] = $GLOBALS['db']->getOne("SELECT sum(rebate_money) FROM ".DB_PREFIX."deal_load WHERE is_has_loans=1 AND user_id=$user_id");
				
		//发布借款笔数
		$data['deal_count'] = $GLOBALS['db']->getOne("SELECT count(*) FROM ".DB_PREFIX."deal use index(idx_1) WHERE user_id=$user_id AND publish_wait = 0 and is_delete = 0 and is_effect=1 ");
		//成功借款笔数
		$data['success_deal_count'] = $GLOBALS['db']->getOne("SELECT count(*) FROM ".DB_PREFIX."deal use index(idx_0) WHERE deal_status in (4,5) AND user_id=$user_id AND publish_wait = 0 and is_delete = 0 and is_effect=1 ");
		//还清笔数
		$data['repay_deal_count'] = $GLOBALS['db']->getOne("SELECT count(*) FROM ".DB_PREFIX."deal use index(idx_0) WHERE deal_status = 5 AND user_id=$user_id AND publish_wait = 0 and is_delete = 0 and is_effect=1 ");
		//未还清笔数
		$data['wh_repay_deal_count'] = $data['success_deal_count'] - $data['repay_deal_count'];
		//提前还清笔数
		$data['tq_repay_deal_count'] = $GLOBALS['db']->getOne("SELECT count(*) FROM ".DB_PREFIX."deal_inrepay_repay WHERE user_id=$user_id");
		//正常还清笔数
		$data['zc_repay_deal_count'] = $GLOBALS['db']->getOne("SELECT count(*) FROM ".DB_PREFIX."deal WHERE deal_status = 5 AND user_id=$user_id AND publish_wait = 0 and is_delete = 0 and is_effect=1  AND id not in (SELECT deal_id FROM ".DB_PREFIX."deal_inrepay_repay WHERE user_id=$user_id)");
		//加权平均借款利率
		//$data['avg_rate'] = $GLOBALS['db']->getOne("SELECT sum(rate)/count(*) FROM ".DB_PREFIX."deal use index(idx_0) WHERE deal_status in (4,5) AND user_id=$user_id AND publish_wait = 0 and is_delete = 0 and is_effect=1  ");
		$avg_rate = $GLOBALS['db']->getRow("SELECT sum(rate) as sum_rate ,count(*) as sum_count FROM ".DB_PREFIX."deal use index(idx_0) WHERE deal_status in(4,5) AND user_id=$user_id AND publish_wait = 0 and is_delete = 0 and is_effect=1  ");
		
		//加权平均借款利率【家债权】
		$repay_avg_rate = $GLOBALS['db']->getRow("SELECT sum(d.rate) as sum_rate ,count(*) as sum_count FROM ".DB_PREFIX."deal_load_repay dlr LEFT JOIN ".DB_PREFIX."deal d ON d.id=dlr.deal_id WHERE (dlr.user_id = ".$user_id." and dlr.t_user_id = 0) or dlr.t_user_id = ".$user_id);
		
		$data["avg_rate"] =(floatval($avg_rate["sum_rate"])+floatval($repay_avg_rate["sum_rate"]))/(floatval($avg_rate["sum_count"])+floatval($repay_avg_rate["sum_count"]));

		//平均每笔借款金额
		$data['avg_borrow_amount'] = $data['borrow_amount'] / $data['success_deal_count'];
		
		//逾期本息
		$data['yuqi_amount'] = $GLOBALS['db']->getOne("SELECT (sum(repay_money) + sum(impose_money)) as new_amount FROM ".DB_PREFIX."deal_repay WHERE user_id=$user_id AND ((has_repay=1 and status in(2,3)) or (has_repay=0 AND (".TIME_UTC."- repay_time -24*3600 + 1 ) > 0) )");
		//逾期费用
		$data['yuqi_impose'] = $GLOBALS['db']->getOne("SELECT sum(repay_money) FROM ".DB_PREFIX."deal_repay WHERE has_repay=1 AND user_id=$user_id AND ((has_repay=1 and status in(2,3)) or (has_repay=0 AND (".TIME_UTC."- repay_time -24*3600 + 1 ) > 0))");
		
		//逾期次数
		$data['yuqi_count'] = $GLOBALS['db']->getOne("SELECT count(*) FROM ".DB_PREFIX."deal_repay WHERE user_id=".$user_id."  AND ((has_repay=1 AND status = 2) or (has_repay=0  AND (".TIME_UTC."- repay_time -24*3600 + 1 )/24/3600 between 1 and ".(intval(app_conf('YZ_IMPSE_DAY')) - 1)." ))");
		//严重逾期次数
		$data['yz_yuqi_count'] = $GLOBALS['db']->getOne("SELECT count(*) FROM ".DB_PREFIX."deal_repay WHERE user_id=$user_id AND ((has_repay=1 AND status = 3) or (has_repay=0  AND (".TIME_UTC."- repay_time -24*3600 + 1 )/24/3600 >= ".intval(app_conf('YZ_IMPSE_DAY')) ." ))");
		
		$load_info = $GLOBALS['db']->getRow("SELECT sum(true_interest_money - true_manage_money - true_manage_interest_money) as load_earnings,sum(true_repay_money - true_manage_money - true_manage_interest_money) AS load_repay_money,sum(true_reward_money) as load_reward_money FROM ".DB_PREFIX."deal_load_repay  WHERE  ((user_id = ".$user_id." and t_user_id = 0) or t_user_id = ".$user_id.") AND has_repay=1 ");
		//已赚利息
		$data['load_earnings'] = $load_info['load_earnings'];
		//已回收本息
		$data['load_repay_money'] = $load_info['load_repay_money'];
		//奖励收益
		$data['reward_money'] = $load_info['load_reward_money'];
		
		//坏账 
		$data['bad_count'] = $GLOBALS['db']->getOne("SELECT count(*) FROM ".DB_PREFIX."deal_repay  WHERE user_id=".$user_id."  AND is_site_bad = 1 ");
		
		
		$need_load_info = $GLOBALS['db']->getRow("SELECT sum(repay_money) AS load_repay_money,sum(manage_money) as total_manage_money FROM ".DB_PREFIX."deal_repay  WHERE has_repay=0 AND user_id=$user_id ");
		//待还本息【债权】
		//$t_need_load_info = $GLOBALS['db']->getRow("SELECT sum(repay_money) AS load_repay_money,sum(repay_manage_money) as total_manage_money FROM ".DB_PREFIX."deal_load_repay WHERE has_repay=0 AND t_user_id=$user_id");
		//print_r($t_need_load_info);die;
		//待还本息
		//$data['need_repay_amount'] = $need_load_info['load_repay_money'] + $t_need_load_info['load_repay_money'];
		$data['need_repay_amount'] = $need_load_info['load_repay_money'];
		//待还管理费
		//$data['need_manage_amount'] = $need_load_info['total_manage_money'] + $t_need_load_info['total_manage_money'];
		$data['need_manage_amount'] = $need_load_info['total_manage_money'];
		
		
		
		//邀请返利
		$data['referrals_money'] = $GLOBALS['db']->getOne("SELECT sum(money) FROM ".DB_PREFIX."referrals WHERE rel_user_id=".$user_id."  AND pay_time >0  ");
		
		//提现手续费
		$data['carry_fee_money'] = $GLOBALS['db']->getOne("SELECT sum(fee) FROM ".DB_PREFIX."user_carry WHERE user_id=".$user_id."  AND status =1  ");
		
		//充值手续费
		$data['incharge_fee_money'] = $GLOBALS['db']->getOne("SELECT sum(fee_amount) FROM ".DB_PREFIX."payment_notice WHERE user_id=".$user_id."  AND is_paid =1  ");
		
		//已赚提前还款违约金
		$data['load_tq_impose'] = $GLOBALS['db']->getOne("SELECT sum(impose_money) FROM ".DB_PREFIX."deal_load_repay WHERE has_repay=1 AND status = 0 AND ((user_id = ".$user_id." and t_user_id = 0) or t_user_id = ".$user_id.")");
		//已赚逾期罚息
		$data['load_yq_impose'] = $GLOBALS['db']->getOne("SELECT sum(impose_money) FROM ".DB_PREFIX."deal_load_repay WHERE has_repay=1 AND status in (2,3) AND ((user_id = ".$user_id." and t_user_id = 0) or t_user_id = ".$user_id.")");
		
		//借出加权平均收益率
		//$data['load_avg_rate'] = $GLOBALS['db']->getOne("SELECT sum(rate)/count(*) FROM ".DB_PREFIX."deal_load dl LEFT JOIN ".DB_PREFIX."deal d ON d.id=dl.deal_id WHERE d.deal_status in(4,5) AND dl.user_id=$user_id");
		
		
		$load_avg_rate = $GLOBALS['db']->getRow("SELECT sum(rate) as sum_rate ,count(*) as sum_count FROM ".DB_PREFIX."deal_load dl LEFT JOIN ".DB_PREFIX."deal d ON d.id=dl.deal_id WHERE d.deal_status in(4,5) AND dl.user_id=$user_id ");
		//借出加权平均收益率【加入债权】
		$load_repay_avg_rate = $GLOBALS['db']->getRow("SELECT sum(d.rate) as sum_rate ,count(*) as sum_count FROM ".DB_PREFIX."deal_load_repay dlr LEFT JOIN ".DB_PREFIX."deal d ON d.id=dlr.deal_id WHERE (dlr.user_id = ".$user_id." and dlr.t_user_id = 0) or dlr.t_user_id = ".$user_id);
		//print_r($load_repay_avg_rate);die;
		$data["load_avg_rate"] =(floatval($load_avg_rate["sum_rate"])+floatval($load_repay_avg_rate["sum_rate"]))/(floatval($load_avg_rate["sum_count"])+floatval($load_repay_avg_rate["sum_count"]));

		/**/
		
		
		//总借出笔数
		$u_load = $GLOBALS['db']->getRow("SELECT count(*) as load_count,sum(money) as load_money FROM ".DB_PREFIX."deal_load WHERE user_id=$user_id and is_repay= 0 ");
		
		$data['load_count'] = $u_load['load_count'];
		//总借出金额
		$data['load_money'] = $u_load['load_money'];
		
		//总债权笔数【含债权转入】
		$t_u_load = $GLOBALS['db']->getRow("SELECT count(*) as load_count,sum(dlr.repay_money) as load_money  FROM ".DB_PREFIX."deal_load_repay dlr LEFT JOIN ".DB_PREFIX."deal d ON d.id=dlr.deal_id LEFT JOIN ".DB_PREFIX."deal_load dl ON dl.id=dlr.load_id WHERE d.is_effect=1 and dl.is_repay= 0 and  dlr.t_user_id = ".$user_id);
		
		$data['load_count'] = $data['load_count'] + $t_u_load['load_count'];

		//总借出金额【含债权转入】
		$data['load_money'] = floatval($data['load_money']) + floatval($t_u_load['load_money']);
		//已回收笔数
		$data['reback_load_count'] = $GLOBALS['db']->getOne("SELECT count(*)  FROM ".DB_PREFIX."deal_load dl LEFT JOIN ".DB_PREFIX."deal d ON d.id=dl.deal_id WHERE d.deal_status =5 AND dl.user_id=$user_id and d.is_delete = 0 and d.is_effect=1");
		
		//已回收笔数【含债权转入】
		$data['t_reback_load_count'] = $GLOBALS['db']->getOne("SELECT count(*)  FROM ".DB_PREFIX."deal_load_repay dlr LEFT JOIN ".DB_PREFIX."deal d ON d.id=dlr.deal_id WHERE d.deal_status =5 AND d.is_delete = 0 and d.is_effect=1 and  dlr.t_user_id = ".$user_id." ");
		
		//总已回收笔数
		$data['reback_load_count'] = $data['reback_load_count'] + $data['t_reback_load_count'];
		
		//待回收笔数【含债权转入】
		$data['wait_reback_load_count'] = $GLOBALS['db']->getOne("SELECT count(*)  FROM ".DB_PREFIX."deal_load dl LEFT JOIN ".DB_PREFIX."deal d ON d.id=dl.deal_id WHERE d.deal_status =4 AND dl.user_id=$user_id and d.is_delete = 0 and d.is_effect=1");
		
		$load_wait = $GLOBALS['db']->getRow("SELECT sum(self_money) as total_self_money,sum(repay_money - manage_money - manage_interest_money) as total_repay_money,sum(interest_money) as load_wait_earnings FROM ".DB_PREFIX."deal_load_repay where ((user_id = ".$user_id." and t_user_id = 0) or t_user_id = ".$user_id.") and has_repay = 0  ");
		
		//待回收本金
		$data['load_wait_self_money'] = $load_wait['total_self_money'];
		//待回收本息
		$data['load_wait_repay_money'] = $load_wait['total_repay_money'];
		//待回收利息
		$data['load_wait_earnings'] = $load_wait['load_wait_earnings'];
		
		
		
		
		
		if($GLOBALS['db']->getOne("SELECT count(*) FROM ".DB_PREFIX."user_sta WHERE user_id=".$user_id) > 0)
			$GLOBALS['db']->autoExecute(DB_PREFIX."user_sta",$data,"UPDATE","user_id=".$user_id);
		else{
			$data['user_id'] = $user_id;
			$GLOBALS['db']->autoExecute(DB_PREFIX."user_sta",$data,"INSERT");
		}
		
		if($data['deal_count'] > 0 || $data['load_count']){
			if($data['deal_count'] > 0)
				$u_data['is_borrow_in'] = 1;
			if($data['load_count'] > 0)
				$u_data['is_borrow_out'] = 1;
			$GLOBALS['db']->autoExecute(DB_PREFIX."user",$u_data,"UPDATE","id=".$user_id);
		}
		if($is_cache == true || $make_cache == true){
			set_dynamic_cache($key,$data);
		}
	}
	return $data;
}


//发送流标通知邮件
function send_deal_faild_mail_sms($deal_id,$deal_info=false,$user_id){
	if(!$deal_info && $deal_id ==0)
		return false;
	
	if(app_conf('MAIL_ON')==0 || app_conf('SMS_ON')==0)
		return false;
		
	if(!$deal_info)
		$deal_info = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."deal where id = ".$deal_id);
	
	
	if(intval($deal_info['is_send_bad_msg'])==1)
		return false;
			
	$msg_conf = get_user_msg_conf($user_id);
	
	//借入者的邮件通知
	if(($msg_conf['mail_myfail'] == 1 || !$msg_conf) && app_conf('MAIL_ON')==1){
		$user_info = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."user where id = ".$deal_info['user_id']);
		$tmpl = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."msg_template where name = 'TPL_MAIL_DEAL_FAILED'",false);
		$tmpl_content = $tmpl['content'];
		
		$notice['user_name'] = $user_info['user_name'];
		$notice['deal_name'] = $deal_info['name'];
		$notice['deal_publish_time'] = to_date($deal_info['create_time'],"Y年m月d日");
		$notice['site_name'] = app_conf("SHOP_TITLE");
		$notice['site_url'] = SITE_DOMAIN.APP_ROOT;
		$notice['send_deal_url'] = SITE_DOMAIN.url("index","borrow");
		$notice['help_url'] = SITE_DOMAIN.url("index","helpcenter");
		$notice['msg_cof_setting_url'] = SITE_DOMAIN.url("index","uc_msg#setting");
		$notice['bad_msg'] = $deal_info['bad_msg'];
		
		$GLOBALS['tmpl']->assign("notice",$notice);
		
		$msg = $GLOBALS['tmpl']->fetch("str:".$tmpl_content);
		$msg_data['dest'] = $user_info['email'];
		$msg_data['send_type'] = 1;
		$msg_data['title'] = "您的借款“".$deal_info['name']."”已流标！";
		$msg_data['content'] = addslashes($msg);
		$msg_data['send_time'] = 0;
		$msg_data['is_send'] = 0;
		$msg_data['create_time'] = TIME_UTC;
		$msg_data['user_id'] = $user_info['id'];
		$msg_data['is_html'] = $tmpl['is_html'];
		$GLOBALS['db']->autoExecute(DB_PREFIX."deal_msg_list",$msg_data); //插入
	}
	//借入者的短信通知
	if(app_conf('SMS_ON')==1){
		if(!$user_info)
			$user_info = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."user where id = ".$deal_info['user_id']);
		$tmpl = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."msg_template where name = 'TPL_SMS_DEAL_FAILED'",false);
		$tmpl_content = $tmpl['content'];
		
		$notice['user_name'] = $user_info['user_name'];
		$notice['deal_name'] = $deal_info['name'];
		$notice['deal_publish_time'] = to_date($deal_info['create_time'],"Y年m月d日");
		$notice['site_name'] = app_conf("SHOP_TITLE");
		$notice['bad_msg'] = $deal_info['bad_msg'];
		
		$GLOBALS['tmpl']->assign("notice",$notice);
		
		$msg = $GLOBALS['tmpl']->fetch("str:".$tmpl_content);
		$msg_data['dest'] = $user_info['mobile'];
		$msg_data['send_type'] = 0;
		$msg_data['title'] = $user_info['user_name']."的借款“".$deal_info['name']."”流标通知";
		$msg_data['content'] = addslashes($msg);
		$msg_data['send_time'] = 0;
		$msg_data['is_send'] = 0;
		$msg_data['create_time'] = TIME_UTC;
		$msg_data['user_id'] = $user_info['id'];
		$msg_data['is_html'] = $tmpl['is_html'];
		$GLOBALS['db']->autoExecute(DB_PREFIX."deal_msg_list",$msg_data); //插入
	}
	
	
	
	//获取投标列表
	$load_user_list = $GLOBALS['db']->getAll("SELECT user_name,user_id,create_time FROM ".DB_PREFIX."deal_load WHERE deal_id=".$deal_info['id']);
	if($load_user_list){
		$load_mail_tmpl = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."msg_template where name = 'TPL_MAIL_LOAD_FAILED'",false);
		$load_sms_tmpl = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."msg_template where name = 'TPL_SMS_LOAD_FAILED'",false);
		foreach($load_user_list as $k=>$v){
			$user_info = $GLOBALS['db']->getRow("select email,mobile from ".DB_PREFIX."user where id = ".$v['user_id']);
			$load_msg_conf = get_user_msg_conf($v['user_id']);
			//邮件通知
			if($load_msg_conf['mail_myfail'] == 1 && app_conf('MAIL_ON')==1){
				$tmpl_content = $load_mail_tmpl['content'];
				$notice['user_name'] = $v['user_name'];
				$notice['deal_name'] = $deal_info['name'];
				$notice['money'] = number_format($v['money']);
				$notice['deal_url'] = SITE_DOMAIN.$deal_info['url'];
				$notice['deal_load_time'] = to_date($v['create_time'],"Y年m月d日");
				$notice['site_name'] = app_conf("SHOP_TITLE");
				$notice['site_url'] = SITE_DOMAIN.APP_ROOT;
				$notice['help_url'] = SITE_DOMAIN.url("index","helpcenter");
				$notice['msg_cof_setting_url'] = SITE_DOMAIN.url("index","uc_msg#setting");
				$notice['bad_msg'] = $deal_info['bad_msg'];
				
				$GLOBALS['tmpl']->assign("notice",$notice);
				
				$msg = $GLOBALS['tmpl']->fetch("str:".$tmpl_content);
				$msg_data['dest'] = $user_info['email'];
				$msg_data['send_type'] = 1;
				$msg_data['title'] = "您的所投的借款“".$deal_info['name']."”已流标！";
				$msg_data['content'] = addslashes($msg);
				$msg_data['send_time'] = 0;
				$msg_data['is_send'] = 0;
				$msg_data['create_time'] = TIME_UTC;
				$msg_data['user_id'] =  $v['user_id'];
				$msg_data['is_html'] = $load_mail_tmpl['is_html'];
				$GLOBALS['db']->autoExecute(DB_PREFIX."deal_msg_list",$msg_data); //插入
			}
			//短信通知
			if(app_conf('SMS_ON')==1){
				$tmpl_content = $load_sms_tmpl['content'];
				$notice['user_name'] = $v['user_name'];
				$notice['deal_name'] = $deal_info['name'];
				$notice['money'] = number_format($v['money']);
				$notice['deal_load_time'] = to_date($v['create_time'],"Y年m月d日");
				$notice['site_name'] = app_conf("SHOP_TITLE");
				$notice['bad_msg'] = $deal_info['bad_msg'];
				
				$GLOBALS['tmpl']->assign("notice",$notice);
				
				$msg = $GLOBALS['tmpl']->fetch("str:".$tmpl_content);
				$msg_data['dest'] = $user_info['mobile'];
				$msg_data['send_type'] = 0;
				$msg_data['title'] = $v['user_name']."的投标“".$deal_info['name']."”流标通知";
				$msg_data['content'] = addslashes($msg);
				$msg_data['send_time'] = 0;
				$msg_data['is_send'] = 0;
				$msg_data['create_time'] = TIME_UTC;
				$msg_data['user_id'] =  $v['user_id'];
				$msg_data['is_html'] = $load_sms_tmpl['is_html'];
				$GLOBALS['db']->autoExecute(DB_PREFIX."deal_msg_list",$msg_data); //插入
			}
		}
	}
}

//发送满标通知邮件
function send_deal_success_mail_sms($deal_id,$deal_info=false){
	if(!$deal_info && $deal_id ==0)
		return false;
	
	if(app_conf('MAIL_ON')==0 && app_conf('SMS_ON')==0)
		return false;
	
	
	if(!$deal_info)
		$deal_info = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."deal where id = ".$deal_id);
	
	
	if(intval($deal_info['is_send_success_msg'])==1)
		return false;
		
	
	//借入者的邮件通知
	if(app_conf('MAIL_ON')==1){
		$user_info = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."user where id = ".$deal_info['user_id']);
		$tmpl = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."msg_template where name = 'TPL_MAIL_DEAL_SUCCESS'",false);
		$tmpl_content = $tmpl['content'];
		
		$notice['user_name'] = $user_info['user_name'];
		$notice['deal_name'] = $deal_info['name'];
		$notice['deal_publish_time'] = to_date($deal_info['create_time'],"Y年m月d日");
		$notice['site_name'] = app_conf("SHOP_TITLE");
		$notice['site_url'] = SITE_DOMAIN.APP_ROOT;
		$notice['send_deal_url'] = SITE_DOMAIN.url("index","borrow");
		$notice['help_url'] = SITE_DOMAIN.url("index","helpcenter");
		$notice['msg_cof_setting_url'] = SITE_DOMAIN.url("index","uc_msg#setting");
		$notice['bad_msg'] = $deal_info['bad_msg'];
		
		$GLOBALS['tmpl']->assign("notice",$notice);
		
		$msg = $GLOBALS['tmpl']->fetch("str:".$tmpl_content);
		$msg_data['dest'] = $user_info['email'];
		$msg_data['send_type'] = 1;
		$msg_data['title'] = "您的借款“".$deal_info['name']."”已满标！";
		$msg_data['content'] = addslashes($msg);
		$msg_data['send_time'] = 0;
		$msg_data['is_send'] = 0;
		$msg_data['create_time'] = TIME_UTC;
		$msg_data['user_id'] = $user_info['id'];
		$msg_data['is_html'] = $tmpl['is_html'];
		$GLOBALS['db']->autoExecute(DB_PREFIX."deal_msg_list",$msg_data); //插入
	}
	//借入者的短信通知
	if(app_conf('SMS_ON')==1){
		if(!$user_info)
			$user_info = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."user where id = ".$deal_info['user_id']);
		$tmpl = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."msg_template where name = 'TPL_SMS_DEAL_SUCCESS'",false);
		$tmpl_content = $tmpl['content'];
		
		$notice['user_name'] = $user_info['user_name'];
		$notice['deal_name'] = $deal_info['name'];
		$notice['deal_publish_time'] = to_date($deal_info['create_time'],"Y年m月d日");
		$notice['site_name'] = app_conf("SHOP_TITLE");
		$notice['bad_msg'] = $deal_info['bad_msg'];
		
		$GLOBALS['tmpl']->assign("notice",$notice);
		
		$msg = $GLOBALS['tmpl']->fetch("str:".$tmpl_content);
		$msg_data['dest'] = $user_info['mobile'];
		$msg_data['send_type'] = 0;
		$msg_data['title'] = $user_info['user_name']."的借款“".$deal_info['name']."”满标通知";
		$msg_data['content'] = addslashes($msg);
		$msg_data['send_time'] = 0;
		$msg_data['is_send'] = 0;
		$msg_data['create_time'] = TIME_UTC;
		$msg_data['user_id'] = $user_info['id'];
		$msg_data['is_html'] = $tmpl['is_html'];
		$GLOBALS['db']->autoExecute(DB_PREFIX."deal_msg_list",$msg_data); //插入
	}
		
	//获取投标列表
	$load_user_list = $GLOBALS['db']->getAll("SELECT user_name,user_id,create_time FROM ".DB_PREFIX."deal_load WHERE deal_id=".$deal_info['id']);
	if($load_user_list){
		$load_mail_tmpl = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."msg_template where name = 'TPL_MAIL_LOAD_SUCCESS'",false);
		$load_sms_tmpl = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."msg_template where name = 'TPL_SMS_LOAD_SUCCESS'",false);
		foreach($load_user_list as $k=>$v){
			$user_info = $GLOBALS['db']->getRow("select email,mobile from ".DB_PREFIX."user where id = ".$v['user_id']);
			$load_msg_conf = get_user_msg_conf($v['user_id']);
			if($load_msg_conf['mail_bidsuccess'] == 1 && app_conf('MAIL_ON')==1){
				$tmpl_content = $load_mail_tmpl['content'];
				$notice['user_name'] = $v['user_name'];
				$notice['deal_name'] = $deal_info['name'];
				$notice['deal_url'] = SITE_DOMAIN.$deal_info['url'];
				$notice['deal_load_time'] = to_date($v['create_time'],"Y年m月d日");
				$notice['site_name'] = app_conf("SHOP_TITLE");
				$notice['site_url'] = SITE_DOMAIN.APP_ROOT;
				$notice['help_url'] = SITE_DOMAIN.url("index","helpcenter");
				$notice['msg_cof_setting_url'] = SITE_DOMAIN.url("index","uc_msg#setting");
				
				$GLOBALS['tmpl']->assign("notice",$notice);
				
				$msg = $GLOBALS['tmpl']->fetch("str:".$tmpl_content);
				$msg_data['dest'] = $user_info['email'];
				$msg_data['send_type'] = 1;
				$msg_data['title'] = "您的所投的借款“".$deal_info['name']."”已满标！";
				$msg_data['content'] = addslashes($msg);
				$msg_data['send_time'] = 0;
				$msg_data['is_send'] = 0;
				$msg_data['create_time'] = TIME_UTC;
				$msg_data['user_id'] =  $v['user_id'];
				$msg_data['is_html'] = $load_mail_tmpl['is_html'];
				$GLOBALS['db']->autoExecute(DB_PREFIX."deal_msg_list",$msg_data); //插入
			}
			
			//短信通知
			if(app_conf('SMS_ON')==1){
				$tmpl_content = $load_sms_tmpl['content'];
				$notice['user_name'] = $v['user_name'];
				$notice['deal_name'] = $deal_info['name'];
				$notice['deal_load_time'] = to_date($v['create_time'],"Y年m月d日");
				$notice['site_name'] = app_conf("SHOP_TITLE");
				
				$GLOBALS['tmpl']->assign("notice",$notice);
				
				$msg = $GLOBALS['tmpl']->fetch("str:".$tmpl_content);
				$msg_data['dest'] = $user_info['mobile'];
				$msg_data['send_type'] = 0;
				$msg_data['title'] = $v['user_name']."的投标“".$deal_info['name']."”满标通知";
				$msg_data['content'] = addslashes($msg);
				$msg_data['send_time'] = 0;
				$msg_data['is_send'] = 0;
				$msg_data['create_time'] = TIME_UTC;
				$msg_data['user_id'] =  $v['user_id'];
				$msg_data['is_html'] = $load_sms_tmpl['is_html'];
				$GLOBALS['db']->autoExecute(DB_PREFIX."deal_msg_list",$msg_data); //插入
			}
		}
	}
}

//发送流标站内信
function send_deal_faild_site_sms($deal_id,$deal_info=false,$user_id){
	if(!$deal_info && $deal_id ==0)
		return false;
	
	if(!$deal_info)
		$deal_info = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."deal where id = ".$deal_id);
	
	
	if(intval($deal_info['is_send_bad_msg'])==1)
		return false;
		
	$msg_conf = get_user_msg_conf($user_id);
	
	if($msg_conf['sms_myfail'] == 1){
		
		$notice['shop_title'] = app_conf("SHOP_TITLE");
		$notice['time'] = to_date($v['create_time'],"Y年m月d日");
		$notice['deal_name'] = "<a href=\"".url("index","deal",array("id"=>$deal_info['id']))."\">".$deal_info['name']."</a>";
		$notice['bad_msg'] = $deal_info['bad_msg'];
		$tmpl_content = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."msg_template where name = 'TPL_DEAL_FAILD_SITE_SMS'",false);
		$GLOBALS['tmpl']->assign("notice",$notice);
		$content = $GLOBALS['tmpl']->fetch("str:".$tmpl_content['content']);
		send_user_msg("",$content,0,$user_id,TIME_UTC,0,true,10);
	}
	
	//获取投标列表
	$load_user_list = $GLOBALS['db']->getAll("SELECT user_name,user_id,create_time FROM ".DB_PREFIX."deal_load WHERE deal_id=".$deal_info['id']);
	if($load_user_list){
		foreach($load_user_list as $k=>$v){
			$user_info = $GLOBALS['db']->getRow("select email from ".DB_PREFIX."user where id = ".$v['user_id']);
			$load_msg_conf = get_user_msg_conf($v['user_id']);
			if($load_msg_conf['sms_myfail'] == 1 || !$load_msg_conf){
				
				$notice['shop_title']=app_conf("SHOP_TITLE");
				$notice['time']=to_date($v['create_time'],"Y年m月d日");
				$notice['deal_name']="<a href=\"".url("index","deal",array("id"=>$deal_info['id']))."\">".$deal_info['name']."</a>";
				$notice['bad_msg'] = "1. 借款者没能按时提交四项必要信用认证的材料。<br>2. 借款者在招标期间没有筹集到足够的借款。";
				$tmpl_content = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."msg_template where name = 'TPL_DEAL_FAILD_SITE_SMS'",false);
				$GLOBALS['tmpl']->assign("notice",$notice);
				$content = $GLOBALS['tmpl']->fetch("str:".$tmpl_content['content']);
				send_user_msg("",$content,0,$user_id,TIME_UTC,0,true,10);
				
			}
		}
	}
}

//发送满标站内信
function send_deal_success_site_sms($deal_id,$deal_info=false){
	if(!$deal_info && $deal_id ==0)
		return false;
	
	if(!$deal_info)
		$deal_info = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."deal where id = ".$deal_id);
	
	
	if(intval($deal_info['is_send_success_msg'])==1)
		return false;
	
	//获取投标列表
	$load_user_list = $GLOBALS['db']->getAll("SELECT user_name,user_id,create_time FROM ".DB_PREFIX."deal_load WHERE deal_id=".$deal_info['id']);
	if($load_user_list){
		foreach($load_user_list as $k=>$v){
			$user_info = $GLOBALS['db']->getRow("select email from ".DB_PREFIX."user where id = ".$v['user_id']);
			$load_msg_conf = get_user_msg_conf($v['user_id']);
			if($load_msg_conf['sms_bidsuccess'] == 1 || !$load_msg_conf){
				
				$notice['shop_title']=app_conf("SHOP_TITLE");
				$notice['time']=to_date($v['create_time'],"Y年m月d日");
				$notice['deal_name']="“<a href=\"".url("index","deal",array("id"=>$deal_info['id']))."\">".$deal_info['name']."</a>”";
				
				$tmpl_content = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."msg_template where name = 'TPL_DEAL_SUCCESS_SITE_SMS'",false);
				$GLOBALS['tmpl']->assign("notice",$notice);
				$content = $GLOBALS['tmpl']->fetch("str:".$tmpl_content['content']);
				send_user_msg("",$content,0,$v['user_id'],TIME_UTC,0,true,16);
			}
		}
	}
}

//发送电子协议邮件
function send_deal_contract_email($deal_id,$deal,$user_id){
	
	if($deal_id ==0 && !$deal)
		return false;
		
	if(app_conf('MAIL_ON')==0 || app_conf('MAIL_SEND_CONTRACT_ON')==0)
		return false;
	
	if(!$deal)
		$deal = get_deal($deal_id);
	
	if(intval($deal['is_send_contract'])==1 || $deal['deal_status'] <> 4)
		return false;
		
	
	$GLOBALS['tmpl']->assign('deal',$deal);
	
	$loan_list = $GLOBALS['db']->getAll("select * FROM ".DB_PREFIX."deal_load WHERE deal_id=".$deal_id." ORDER BY create_time ASC");
	foreach($loan_list as $k=>$v){
		
		$vv_deal['borrow_amount'] = $v['money'];
		$vv_deal['rate'] = $deal['rate'];
		$vv_deal['repay_time'] = $deal['repay_time'];
		$vv_deal['loantype'] = $deal['loantype'];
		$vv_deal['repay_time_type'] = $deal['repay_time_type'];
		
		$deal_rs =  deal_repay_money($vv_deal);
		$loan_list[$k]['get_repay_money'] = $deal_rs['month_repay_money'];
		if(is_last_repay($deal['loantype']))
			$loan_list[$k]['get_repay_money'] = $deal_rs['remain_repay_money'];
	}
	
	$GLOBALS['tmpl']->assign('loan_list',$loan_list);
		
	$u_info = get_user("*",$deal['user_id']);
	$GLOBALS['tmpl']->assign('user_info',$u_info);
	if($u_info['sealpassed'] == 1){
		$credit_file = get_user_credit_file($deal['user_id']);
		$GLOBALS['tmpl']->assign('user_seal_url',$credit_file['credit_seal']['file_list'][0]);
	}
	
	$GLOBALS['tmpl']->assign('SITE_URL',str_replace(array("https://","http://"),"",SITE_DOMAIN));
	$GLOBALS['tmpl']->assign('SITE_TITLE',app_conf("SITE_TITLE"));
	$GLOBALS['tmpl']->assign('CURRENCY_UNIT',app_conf("CURRENCY_UNIT"));
	
	$msg = $GLOBALS['tmpl']->fetch("str:".get_contract($deal['contract_id']));
	
	$msg = "<div style=\"font-size:12px\"><div style=\"background-color: #FFFFFF; border:#dfe6ea solid 1px; padding: 5px 12px;\">" .$msg;
	$msg .= "</div></div>"; 
	
	$msg_data['dest'] = $u_info['email'];
	$msg_data['send_type'] = 1;
	$msg_data['title'] = "借款“".$deal['name']."”的电子协议！";
	$msg_data['content'] = addslashes($msg);
	$msg_data['send_time'] = 0;
	$msg_data['is_send'] = 0;
	$msg_data['create_time'] = TIME_UTC;
	$msg_data['user_id'] =  $deal['user_id'];
	$msg_data['is_html'] = 1;
	//插入数据库
	if($GLOBALS['db']->autoExecute(DB_PREFIX."deal_msg_list",$msg_data)){
		$GLOBALS['db']->query("UPDATE ".DB_PREFIX."deal set is_send_contract =1 where id =".$deal['id']);
	}
	
	//获取投资者的邮件
	$load_user_list = $GLOBALS['db']->getAll("SELECT u.user_name,u.email,u.id FROM ".DB_PREFIX."user u LEFT JOIN  ".DB_PREFIX."deal_load dl ON u.id=dl.user_id WHERE dl.deal_id=".$deal['id']);
	foreach($load_user_list as $k=>$v){
		$msg_data['dest'] = $v['email'];
		$msg_data['send_type'] = 1;
		$msg_data['title'] = "借款“".$deal['name']."”的电子协议！";
		$msg_data['content'] = addslashes($msg);
		$msg_data['send_time'] = 0;
		$msg_data['is_send'] = 0;
		$msg_data['create_time'] = TIME_UTC;
		$msg_data['user_id'] =  $v['id'];
		$msg_data['is_html'] = 1;
		//插入数据库
		$GLOBALS['db']->autoExecute(DB_PREFIX."deal_msg_list",$msg_data);
	}
}

function send_transfer_contract_email($id,$transfer){
	if($id ==0 && !$transfer)
		return false;
		
	if(app_conf('MAIL_ON')==0 || app_conf('MAIL_SEND_CONTRACT_ON')==0)
		return false;
	
	if(!$transfer){
		$condition = ' AND dlt.id='.$id.' AND d.deal_status >= 4 and d.is_effect=1 and d.is_delete=0 and d.repay_time_type =1 and  d.publish_wait=0 and (dlt.user_id='.$GLOBALS['user_info']['id'].' or dlt.t_user_id = '.$GLOBALS['user_info']['id'].') ';
		$union_sql = " LEFT JOIN ".DB_PREFIX."deal_load_transfer dlt ON dlt.deal_id = dl.deal_id ";
		/*
		$sql = 'SELECT dlt.id,dlt.deal_id,dlt.load_id,dlt.transfer_amount,dlt.near_repay_time,dlt.user_id,d.next_repay_time,d.last_repay_time,d.rate,d.repay_start_time,d.repay_time,dlt.load_money,dlt.id as dltid,dlt.status as tras_status,dlt.t_user_id,dlt.transfer_amount,dlt.create_time as tras_create_time,dlt.transfer_time,d.user_id as duser_id,d.tcontract_id FROM '.DB_PREFIX.'deal_load dl LEFT JOIN '.DB_PREFIX.'deal d ON d.id = dl.deal_id '.$union_sql.' WHERE 1=1 '.$condition;
		
		$transfer = $GLOBALS['db']->getRow($sql);*/
		require_once APP_ROOT_PATH."/app/Lib/deal_func.php";
		$transfer = get_transfer($union_sql,$condition);
	}
	
	if($transfer){
		/*
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
		
		$transfer_rs = deal_transfer($transfer);
		//本息还款金额
		$transfer['month_repay_money'] = $transfer_rs['month_repay_money'];
		//剩余多少钱未回
		$transfer['all_must_repay_money'] = $transfer_rs['all_must_repay_money'];
		//剩余多少本金未回
		$transfer['left_benjin'] = $transfer_rs['left_benjin'];
		
		
		$transfer['month_repay_money_format'] = format_price($transfer['month_repay_money']);
		
		$transfer['all_must_repay_money_format'] = format_price($transfer['all_must_repay_money']);
		
		$transfer['left_benjin_format'] = format_price($transfer['left_benjin']);
		//剩多少利息
		$transfer['left_lixi'] = $transfer['all_must_repay_money'] - $transfer['left_benjin'];
		$transfer['left_lixi_format'] = format_price($transfer['left_lixi']);
		
		//投标价格
		$transfer['load_money_format'] =  format_price($transfer['load_money']);
		
		//转让价格
		$transfer['transfer_amount_format'] =  format_price($transfer['transfer_amount']);
		
		//转让管理费
		$transfer['transfer_fee_format'] = format_price($transfer['transfer_amount']*(float)app_conf("USER_LOAD_TRANSFER_FEE"));
		
		//转让收益
		$transfer['transfer_income_format'] =  format_price($transfer['all_must_repay_money']-$transfer['transfer_amount']);
		
		if($transfer['tras_create_time'] !=""){
			$transfer['tras_create_time_format'] =  to_date($transfer['tras_create_time'],"Y 年 m 月 d 日");
		}
		
		$transfer['transfer_time_format'] =  to_date($transfer['transfer_time'],"Y 年 m 月 d 日");
		
		$transfer['user'] = get_user("user_name,email,real_name,idno,level_id",$transfer['user_id']);
		$transfer['tuser'] = get_user("user_name,email,real_name,idno,level_id",$transfer['t_user_id']);
		$transfer['duser'] = get_user("user_name,email,real_name,idno,level_id",$transfer['duser_id']);
		$GLOBALS['tmpl']->assign('transfer',$transfer);
		*/
		
		$GLOBALS['tmpl']->assign('SITE_URL',str_replace(array("https://","http://"),"",SITE_DOMAIN));
		$GLOBALS['tmpl']->assign('SITE_TITLE',app_conf("SITE_TITLE"));
		$GLOBALS['tmpl']->assign('CURRENCY_UNIT',app_conf("CURRENCY_UNIT"));
		
		$contract = $GLOBALS['tmpl']->fetch("str:".get_contract($transfer['tcontract_id']));
		
		$msg_data['dest'] = $transfer['user']['email'];
		$msg_data['send_type'] = 1;
		$msg_data['title'] = "债权“Z-".$transfer['load_id']."”的电子协议！";
		$msg_data['content'] = addslashes($contract);
		$msg_data['send_time'] = 0;
		$msg_data['is_send'] = 0;
		$msg_data['create_time'] = TIME_UTC;
		$msg_data['user_id'] =  $transfer['user_id'];
		$msg_data['is_html'] = 1;
		//发送给转让者
		$GLOBALS['db']->autoExecute(DB_PREFIX."deal_msg_list",$msg_data);
		//发送给承接者
		$msg_data['dest'] = $transfer['tuser']['email'];
		$msg_data['user_id'] =  $transfer['t_user_id'];
		$GLOBALS['db']->autoExecute(DB_PREFIX."deal_msg_list",$msg_data);
			
	}
}

function get_contract($id){
	static $contract_array =array();
	if(!isset($contract_array[$id])){
		$contract = load_auto_cache("contract_cache");
		$contract_array[$id] = $contract[$id]['content'];
	}
	return $contract_array[$id];
}


//发注册验证邮件
function send_user_verify_mail($email,$code,$user_info,$immediately)
{
	$re = array('msg_id'=>0, 'status'=>0,'msg'=>'');

	if(app_conf("MAIL_ON")==1)
	{
		$tmpl = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."msg_template where name = 'TPL_MAIL_USER_VERIFY'");
		$tmpl_content = $tmpl['content'];
		$verify['email'] = $email;
		$verify['code'] = $code;
		$GLOBALS['tmpl']->assign("verify",$verify);
		$msg = $GLOBALS['tmpl']->fetch("str:".$tmpl_content);
		$msg_data['dest'] = $email;
		$msg_data['send_type'] = 1;
		$msg_data['title'] = "邮箱验证内容";
		$msg_data['content'] = addslashes($msg);
		$msg_data['send_time'] = 0;
			
		if ($immediately){
			$msg_data['is_send'] = 1;
		}else{
			$msg_data['is_send'] = 0;
		}
		$msg_data['create_time'] = TIME_UTC;
		$msg_data['user_id'] = $user_info['id'];
		$msg_data['is_html'] = $tmpl['is_html'];
		$GLOBALS['db']->autoExecute(DB_PREFIX."deal_msg_list",$msg_data); //插入

		$msg_id = $GLOBALS['db']->insert_id();
			
		$re['msg_id'] = $msg_id;
			
		if ($immediately && $msg_id > 0){
			$result = send_sms_email($msg_data);

			$re['status'] = intval($result['status']);
			$re['msg'] = trim($result['msg']);

			//发送结束，更新当前消息状态
			$GLOBALS['db']->query("update ".DB_PREFIX."deal_msg_list set is_success = ".intval($result['status']).",result='".$result['msg']."',send_time='".TIME_UTC."' where id =".$msg_id);
		}else{
			if ($msg_id == 0){
				$re['status'] = 0;
			}else{
				$re['status'] = 1;
			}
		}
		return $re;
	}else{
		return $re;
	}
}


//密码取回验证邮件
function send_user_verify_mails($email,$code,$user_info,$immediately)
{
	$re = array('msg_id'=>0, 'status'=>0,'msg'=>'');
	if(app_conf("MAIL_ON")==1)
	{
		$tmpl = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."msg_template where name = 'TPL_MAIL_USER_PASSWORD'");
		$tmpl_content = $tmpl['content'];
		$verify['email'] = $email;
		$verify['code'] = $code;
		$GLOBALS['tmpl']->assign("verify",$verify);
		$msg = $GLOBALS['tmpl']->fetch("str:".$tmpl_content);
		//$msg_data['user_id'] = $user_id;
		$msg_data['dest'] = $email;
		$msg_data['send_type'] = 1;
		$msg_data['title'] = "邮箱验证内容";
		$msg_data['content'] = addslashes($msg);
		$msg_data['send_time'] = 0;
			
		if ($immediately){
			$msg_data['is_send'] = 1;
		}else{
			$msg_data['is_send'] = 0;
		}
		$msg_data['create_time'] = TIME_UTC;
		
		$msg_data['user_id'] = $user_info['user_id'];
		$msg_data['is_html'] = $tmpl['is_html'];
		$GLOBALS['db']->autoExecute(DB_PREFIX."deal_msg_list",$msg_data); //插入

		$msg_id = $GLOBALS['db']->insert_id();
			
		$re['msg_id'] = $msg_id;
			
		if ($immediately && $msg_id > 0){
			$result = send_sms_email($msg_data);

			$re['status'] = intval($result['status']);
			$re['msg'] = trim($result['msg']);

			//发送结束，更新当前消息状态
			$GLOBALS['db']->query("update ".DB_PREFIX."deal_msg_list set is_success = ".intval($result['status']).",result='".$result['msg']."',send_time='".TIME_UTC."' where id =".$msg_id);
		}else{
			if ($msg_id == 0){
				$re['status'] = 0;
			}else{
				$re['status'] = 1;
			}
		}
		return $re;
	}else{
		return $re;
	}
}


//发密码验证邮件
function send_user_password_mail($user_id)
{
	if(app_conf("MAIL_ON")==1)
	{
		$verify_code = rand(111111,999999);
		$GLOBALS['db']->query("update ".DB_PREFIX."user set password_verify = '".$verify_code."' where id = ".$user_id);
		$user_info = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."user where id = ".$user_id);			
		if($user_info)
		{
			$tmpl = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."msg_template where name = 'TPL_MAIL_USER_PASSWORD'");
			$tmpl_content=  $tmpl['content'];
			$user_info['password_url'] = SITE_DOMAIN.url("index","user#modify_password", array("code"=>$user_info['password_verify'],"id"=>$user_info['id']));			
			$GLOBALS['tmpl']->assign("user",$user_info);
			$msg = $GLOBALS['tmpl']->fetch("str:".$tmpl_content);
			$msg_data['dest'] = $user_info['email'];
			$msg_data['send_type'] = 1;
			$msg_data['title'] = $GLOBALS['lang']['RESET_PASSWORD'];
			$msg_data['content'] = addslashes($msg);
			$msg_data['send_time'] = 0;
			$msg_data['is_send'] = 0;
			$msg_data['create_time'] = TIME_UTC;
			$msg_data['user_id'] = $user_info['id'];
			$msg_data['is_html'] = $tmpl['is_html'];
			$GLOBALS['db']->autoExecute(DB_PREFIX."deal_msg_list",$msg_data); //插入
		}
	}
}

//发密码验证邮件--担保机构
function send_unit_user_password_mail($user_id)
{
	//if(app_conf("MAIL_ON")==1)
	//{
		$verify_code = rand(111111,999999);
		$GLOBALS['db']->query("update ".DB_PREFIX."user set code = '".$verify_code."' where id = ".$user_id);
		$user_info = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."user where id = ".$user_id);			
		if($user_info)
		{
			$tmpl = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."msg_template where name = 'TPL_MAIL_USER_PASSWORD'");
			$tmpl_content=  $tmpl['content'];
			$user_info['password_url'] = SITE_DOMAIN.url("index","manageagency#modify_password", array("code"=>$user_info['password_verify'],"id"=>$user_info['id']));			
			$GLOBALS['tmpl']->assign("user",$user_info);
			$msg = $GLOBALS['tmpl']->fetch("str:".$tmpl_content);
			$msg_data['dest'] = $user_info['email'];
			$msg_data['send_type'] = 1;
			$msg_data['title'] = $GLOBALS['lang']['RESET_PASSWORD'];
			$msg_data['content'] = addslashes($msg);
			$msg_data['send_time'] = 0;
			$msg_data['is_send'] = 0;
			$msg_data['create_time'] = TIME_UTC;
			$msg_data['user_id'] = $user_info['id'];
			$msg_data['is_html'] = $tmpl['is_html'];
			$GLOBALS['db']->autoExecute(DB_PREFIX."deal_msg_list",$msg_data); //插入
		}
	//}
}


//发短信收款单
function send_payment_sms($notice_id)
{
	if(app_conf("SMS_ON")==1&&app_conf("SMS_SEND_PAYMENT")==1)
	{
		$notice_data = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."payment_notice where id = ".$notice_id);				
		if($notice_data)
		{
			$user_info = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."user where id = ".$notice_data['user_id']);
			$order_info = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."deal_order where id = ".$notice_data['order_id']);
			if($user_info['mobile']!=''||$order_info['mobile']!='')
			{
				$tmpl = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."msg_template where name = 'TPL_SMS_PAYMENT'");				
				$tmpl_content = $tmpl['content'];
				$notice_data['user_name'] = $user_info['user_name'];
				$notice_data['order_sn'] = $GLOBALS['db']->getOne("select order_sn from ".DB_PREFIX."deal_order where id = ".$notice_data['order_id']);			
				$notice_data['pay_time_format'] = to_date($notice_data['pay_time']);
				$notice_data['money_format'] = format_price($notice_data['money']);
				$GLOBALS['tmpl']->assign("payment_notice",$notice_data);
				$msg = $GLOBALS['tmpl']->fetch("str:".$tmpl_content);
				if($user_info['mobile']!='')
				{
					$msg_data['dest'] = $user_info['mobile'];
				}
				else
				{
					$msg_data['dest'] = $order_info['mobile'];
				}
				$msg_data['send_type'] = 0;
				$msg_data['content'] = addslashes($msg);;
				$msg_data['send_time'] = 0;
				$msg_data['is_send'] = 0;
				$msg_data['create_time'] = TIME_UTC;
				$msg_data['user_id'] = $user_info['id'];
				$msg_data['is_html'] = $tmpl['is_html'];
				$GLOBALS['db']->autoExecute(DB_PREFIX."deal_msg_list",$msg_data); //插入
				
			}
		}		
	}
}

//发邮件收款单
function send_payment_mail($notice_id)
{
	if(app_conf("MAIL_ON")==1&&app_conf("MAIL_SEND_PAYMENT")==1)
	{
		$notice_data = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."payment_notice where id = ".$notice_id);				
		if($notice_data)
		{
			$user_info = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."user where id = ".$notice_data['user_id']);
			if($user_info['email']!='')
			{
				$tmpl = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."msg_template where name = 'TPL_MAIL_PAYMENT'");				
				$tmpl_content = $tmpl['content'];
				$notice_data['user_name'] = $user_info['user_name'];
				$notice_data['order_sn'] = $GLOBALS['db']->getOne("select order_sn from ".DB_PREFIX."deal_order where id = ".$notice_data['order_id']);			
				$notice_data['pay_time_format'] = to_date($notice_data['pay_time']);
				$notice_data['money_format'] = format_price($notice_data['money']);
				$GLOBALS['tmpl']->assign("payment_notice",$notice_data);
				$msg = $GLOBALS['tmpl']->fetch("str:".$tmpl_content);
				$msg_data['dest'] = $user_info['email'];
				$msg_data['send_type'] = 1;
				$msg_data['title'] = $GLOBALS['lang']['PAYMENT_NOTICE'];
				$msg_data['content'] = addslashes($msg);;
				$msg_data['send_time'] = 0;
				$msg_data['is_send'] = 0;
				$msg_data['create_time'] = TIME_UTC;
				$msg_data['user_id'] = $user_info['id'];
				$msg_data['is_html'] = $tmpl['is_html'];
				$GLOBALS['db']->autoExecute(DB_PREFIX."deal_msg_list",$msg_data); //插入
			}
		}		
	}
}



//发邮件发货单
function send_delivery_mail($notice_sn,$deal_names = '',$order_id)
{
	if(app_conf("MAIL_ON")==1&&app_conf("MAIL_SEND_DELIVERY")==1)
	{
		$notice_data = $GLOBALS['db']->getRow("select dn.* from ".DB_PREFIX."delivery_notice as dn left join ".DB_PREFIX."deal_order_item as doi on dn.order_item_id = doi.id where dn.notice_sn = '".$notice_sn."' and doi.order_id = ".$order_id);				
		if($notice_data)
		{
			$user_info = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."user where id = ".$notice_data['user_id']);
			if($user_info['email']!='')
			{
				$tmpl = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."msg_template where name = 'TPL_MAIL_DELIVERY'");				
				$tmpl_content = $tmpl['content'];
				$notice_data['user_name'] = $user_info['user_name'];
				$notice_data['order_sn'] = $GLOBALS['db']->getOne("select do.order_sn from ".DB_PREFIX."deal_order_item as doi left join ".DB_PREFIX."deal_order as do on doi.order_id = do.id where doi.id = ".$notice_data['order_item_id']);			
				$notice_data['delivery_time_format'] = to_date($notice_data['delivery_time']);
				$notice_data['deal_names'] = $deal_names;
				$GLOBALS['tmpl']->assign("delivery_notice",$notice_data);
				$msg = $GLOBALS['tmpl']->fetch("str:".$tmpl_content);
				$msg_data['dest'] = $user_info['email'];
				$msg_data['send_type'] = 1;
				$msg_data['title'] = $GLOBALS['lang']['DELIVERY_NOTICE'];
				$msg_data['content'] = addslashes($msg);;
				$msg_data['send_time'] = 0;
				$msg_data['is_send'] = 0;
				$msg_data['create_time'] = TIME_UTC;
				$msg_data['user_id'] = $user_info['id'];
				$msg_data['is_html'] = $tmpl['is_html'];
				$GLOBALS['db']->autoExecute(DB_PREFIX."deal_msg_list",$msg_data); //插入
			}
		}		
	}
}

//发短信发货单
function send_delivery_sms($notice_sn,$deal_names = '',$order_id)
{
	if(app_conf("SMS_ON")==1&&app_conf("SMS_SEND_DELIVERY")==1)
	{
		$notice_data = $GLOBALS['db']->getRow("select dn.* from ".DB_PREFIX."delivery_notice as dn left join ".DB_PREFIX."deal_order_item as doi on dn.order_item_id = doi.id where dn.notice_sn = '".$notice_sn."' and doi.order_id = ".$order_id);						
		if($notice_data)
		{
			$order_info = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."deal_order where id = ".$order_id);
			$user_info = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."user where id = ".$notice_data['user_id']);
			if($user_info['mobile']!=''||$order_info['mobile']!='')
			{
				$tmpl = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."msg_template where name = 'TPL_SMS_DELIVERY'");				
				$tmpl_content = $tmpl['content'];
				$notice_data['user_name'] = $user_info['user_name'];
				$notice_data['order_sn'] = $GLOBALS['db']->getOne("select do.order_sn from ".DB_PREFIX."deal_order_item as doi left join ".DB_PREFIX."deal_order as do on doi.order_id = do.id where doi.id = ".$notice_data['order_item_id']);			
				$notice_data['delivery_time_format'] = to_date($notice_data['delivery_time']);
				$notice_data['deal_names'] = $deal_names;
				$GLOBALS['tmpl']->assign("delivery_notice",$notice_data);
				$msg = $GLOBALS['tmpl']->fetch("str:".$tmpl_content);
				if($user_info['mobile']!='')
				{
					$msg_data['dest'] = $user_info['mobile'];
					$msg_data['send_type'] = 0;
					$msg_data['content'] = addslashes($msg);;
					$msg_data['send_time'] = 0;
					$msg_data['is_send'] = 0;
					$msg_data['create_time'] = TIME_UTC;
					$msg_data['user_id'] = $user_info['id'];
					$msg_data['is_html'] = $tmpl['is_html'];
					$GLOBALS['db']->autoExecute(DB_PREFIX."deal_msg_list",$msg_data); //插入
				}
				
				if($order_info['mobile']!=''&&$order_info['mobile']!=$user_info['mobile'])
				{
					$msg_data['dest'] = $order_info['mobile'];
					$msg_data['send_type'] = 0;
					$msg_data['content'] = addslashes($msg);;
					$msg_data['send_time'] = 0;
					$msg_data['is_send'] = 0;
					$msg_data['create_time'] = TIME_UTC;
					$msg_data['user_id'] = $user_info['id'];
					$msg_data['is_html'] = $tmpl['is_html'];
					$GLOBALS['db']->autoExecute(DB_PREFIX."deal_msg_list",$msg_data); //插入
				}
			}
		}		
	}
}


//发短信验证码
//$immediately: true，立即即行发送操作;
function send_verify_sms($mobile,$code,$user_info,$immediately)
{
	$re = array('msg_id'=>0, 'status'=>0,'msg'=>'');
	
	if(app_conf("SMS_ON")==1)
	{
				$tmpl = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."msg_template where name = 'TPL_SMS_VERIFY_CODE'");				
				$tmpl_content = $tmpl['content'];
				$verify['mobile'] = $mobile;
				$verify['code'] = $code;
				$GLOBALS['tmpl']->assign("verify",$verify);
				$msg = $GLOBALS['tmpl']->fetch("str:".$tmpl_content);
				$msg_data['dest'] = $mobile;
				$msg_data['send_type'] = 0;
				$msg_data['title'] = addslashes($msg);
				$msg_data['content'] = $msg_data['title'];
				$msg_data['send_time'] = 0;
				
				if ($immediately){
					$msg_data['is_send'] = 1;
				}else{
					$msg_data['is_send'] = 0;
				}
				$msg_data['create_time'] = TIME_UTC;
				$msg_data['user_id'] = $user_info['id'];
				$msg_data['is_html'] = $tmpl['is_html'];
				$GLOBALS['db']->autoExecute(DB_PREFIX."deal_msg_list",$msg_data); //插入	

				$msg_id = $GLOBALS['db']->insert_id();
				
				$re['msg_id'] = $msg_id;
				
				if ($immediately && $msg_id > 0){
					$result = send_sms_email($msg_data);
					
					$re['status'] = intval($result['status']);
					$re['msg'] = trim($result['msg']);
					
					//发送结束，更新当前消息状态
					$GLOBALS['db']->query("update ".DB_PREFIX."deal_msg_list set is_success = ".intval($result['status']).",result='".$result['msg']."',send_time='".TIME_UTC."' where id =".$msg_id);
				}else{
					if ($msg_id == 0){
						$re['status'] = 0;
					}else{
						$re['status'] = 1;
					}
				}
				return $re;
	}else{
		return $re;
	}
}



function send_sms_email($msg_item){
	
	$re = array('status'=>0,'msg'=>'');
	
	if($msg_item['send_type']==0)
	{
		//短信
		require_once APP_ROOT_PATH."system/utils/es_sms.php";
		$sms = new sms_sender();
		$result = $sms->sendSms($msg_item['dest'],$msg_item['content']);
		//发送结束，更新当前消息状态
		//$GLOBALS['db']->query("update ".DB_PREFIX."deal_msg_list set is_success = ".intval($result['status']).",result='".$result['msg']."',send_time='".TIME_UTC."' where id =".intval($msg_item['id']));
		
		$re['status'] = intval($result['status']);
		$re['msg'] = $result['msg'];
	}
	
	if($msg_item['send_type']==1)
	{
		//邮件
		require_once APP_ROOT_PATH."system/utils/es_mail.php";
		$mail = new mail_sender();
			
		$mail->AddAddress($msg_item['dest']);
		$mail->IsHTML($msg_item['is_html']); 				  // 设置邮件格式为 HTML
		$mail->Subject = $msg_item['title'];   // 标题
		$mail->Body = $msg_item['content'];  // 内容
			
		$is_success = $mail->Send();
		$result = $mail->ErrorInfo;
	
		//发送结束，更新当前消息状态
		//$GLOBALS['db']->query("update ".DB_PREFIX."deal_msg_list set is_success = ".intval($is_success).",result='".$result."',send_time='".TIME_UTC."' where id =".intval($msg_item['id']));
		
		$re['status'] = intval($is_success);
		$re['msg'] = $result;
	}
	
	return $re;
}

//发邮件退订验证
function send_unsubscribe_mail($email)
{
	if(app_conf("MAIL_ON")==1)
	{
		if($email)
		{
			$GLOBALS['db']->query("update ".DB_PREFIX."mail_list set code = '".rand(1111,9999)."' where mail_address='".$email."' and code = ''");
			$email_item = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."mail_list where mail_address = '".$email."' and code <> ''");
			if($email_item)
			{
				$tmpl = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."msg_template where name = 'TPL_MAIL_UNSUBSCRIBE'");				
				$tmpl_content = $tmpl['content'];
				$mail = $email_item;
				$mail['url'] = SITE_DOMAIN.url("index","subscribe#dounsubscribe", array("code"=>base64_encode($mail['code']."|".$mail['mail_address'])));
				$GLOBALS['tmpl']->assign("mail",$mail);
				$msg = $GLOBALS['tmpl']->fetch("str:".$tmpl_content);
				$msg_data['dest'] = $mail['mail_address'];
				$msg_data['send_type'] = 1;
				$msg_data['title'] = $GLOBALS['lang']['MAIL_UNSUBSCRIBE'];
				$msg_data['content'] = addslashes($msg);;
				$msg_data['send_time'] = 0;
				$msg_data['is_send'] = 0;
				$msg_data['create_time'] = TIME_UTC;
				$msg_data['user_id'] = 0;
				$msg_data['is_html'] = $tmpl['is_html'];
				$GLOBALS['db']->autoExecute(DB_PREFIX."deal_msg_list",$msg_data); //插入
			}
		}		
	}
}

function get_deal_cate_name($cate_id)
{
	return $GLOBALS['db']->getOne("select name from ".DB_PREFIX."deal_cate where id =".$cate_id);
}

function get_loan_type_name($type_id){
	return $GLOBALS['db']->getOne("select name from ".DB_PREFIX."deal_loan_type where id =".$type_id);
}
	
function format_price($price,$decimals=2)
{
	return app_conf("CURRENCY_UNIT")."".number_format($price,2);
}
function format_score($score)
{
	return intval($score)."".app_conf("SCORE_UNIT");	
}

//utf8 字符串截取
function msubstr($str, $start=0, $length=15, $charset="utf-8", $suffix=true)
{
	if(function_exists("mb_substr"))
    {
        $slice =  mb_substr($str, $start, $length, $charset);
        if($suffix&$slice!=$str) return $slice."…";
    	return $slice;
    }
    elseif(function_exists('iconv_substr')) {
        return iconv_substr($str,$start,$length,$charset);
    }
    $re['utf-8']   = "/[\x01-\x7f]|[\xc2-\xdf][\x80-\xbf]|[\xe0-\xef][\x80-\xbf]{2}|[\xf0-\xff][\x80-\xbf]{3}/";
    $re['gb2312'] = "/[\x01-\x7f]|[\xb0-\xf7][\xa0-\xfe]/";
    $re['gbk']    = "/[\x01-\x7f]|[\x81-\xfe][\x40-\xfe]/";
    $re['big5']   = "/[\x01-\x7f]|[\x81-\xfe]([\x40-\x7e]|\xa1-\xfe])/";
    preg_match_all($re[$charset], $str, $match);
    $slice = join("",array_slice($match[0], $start, $length));
    if($suffix&&$slice!=$str) return $slice."…";
    return $slice;
}

/**
*截取替换
*/
function utf_substr($str){
	$pa = "/[\x01-\x7f]|[\xc2-\xdf][\x80-\xbf]|\xe0[\xa0-\xbf][\x80-\xbf]|[\xe1-\xef][\x80-\xbf][\x80-\xbf]|\xf0[\x90-\xbf][\x80-\xbf][\x80-\xbf]|[\xf1-\xf7][\x80-\xbf][\x80-\xbf][\x80-\xbf]/";
    preg_match_all($pa, $str, $t_string);
	if(count($t_string[0])==1)
		return $str;
	if(count($t_string[0])==2)
		return cut_str($str, 1, 0).'***';
	else
		return cut_str($str, 1, 0).'***'.cut_str($str, 1, -1);
}

function cut_str($string, $sublen, $start = 0, $code = 'UTF-8')
{
    if($code == 'UTF-8')
    {
        $pa = "/[\x01-\x7f]|[\xc2-\xdf][\x80-\xbf]|\xe0[\xa0-\xbf][\x80-\xbf]|[\xe1-\xef][\x80-\xbf][\x80-\xbf]|\xf0[\x90-\xbf][\x80-\xbf][\x80-\xbf]|[\xf1-\xf7][\x80-\xbf][\x80-\xbf][\x80-\xbf]/";
        preg_match_all($pa, $string, $t_string);

        if(count($t_string[0]) - $start > $sublen) return join('', array_slice($t_string[0], $start, $sublen));
        return join('', array_slice($t_string[0], $start, $sublen));
    }
    else
    {
        $start = $start*2;
        $sublen = $sublen*2;
        $strlen = strlen($string);
        $tmpstr = '';

        for($i=0; $i< $strlen; $i++)
        {
            if($i>=$start && $i< ($start+$sublen))
            {
                if(ord(substr($string, $i, 1))>129)
                {
                    $tmpstr.= substr($string, $i, 2);
                }
                else
                {
                    $tmpstr.= substr($string, $i, 1);
                }
            }
            if(ord(substr($string, $i, 1))>129) $i++;
        }
        return $tmpstr;
    }
}

 /**
  * PHP获取字符串中英文混合长度 
  * @param $str string 字符串
  * @param $$charset string 编码
  * @return 返回长度，1中文=1位，2英文=1位
  */
  function strLength($str,$charset='utf-8'){
  	if($charset=='utf-8') $str = iconv('utf-8','gb2312',$str);
    $num = strlen($str);
    $cnNum = 0;
    for($i=0;$i<$num;$i++){
        if(ord(substr($str,$i+1,1))>127){
            $cnNum++;
            $i++;
        }
    }
    $enNum = $num-($cnNum*2);
    $number = ($enNum/2)+$cnNum;
    return ceil($number);
 }


//字符编码转换
if(!function_exists("iconv"))
{	
	function iconv($in_charset,$out_charset,$str)
	{
		require 'libs/iconv.php';
		$chinese = new Chinese();
		return $chinese->Convert($in_charset,$out_charset,$str);
	}
}

//JSON兼容
if(!function_exists("json_encode"))
{	
	function json_encode($data)
	{
		require_once 'libs/json.php';
		$JSON = new JSON();
		return $JSON->encode($data);
	}
}
if(!function_exists("json_decode"))
{	
	function json_decode($data)
	{
		require_once 'libs/json.php';
		$JSON = new JSON();
		return $JSON->decode($data,1);
	}
}

//邮件格式验证的函数
function check_email($email)
{
	if(!preg_match("/^\w+((-\w+)|(\.\w+))*\@[A-Za-z0-9]+((\.|-)[A-Za-z0-9]+)*\.[A-Za-z0-9]+$/",$email))
	{
		return false;
	}
	else
	return true;
}

//验证手机号码
function check_mobile($mobile)
{
	if(!empty($mobile) && !preg_match("/^\d{6,}$/",$mobile))
	{
		return false;
	}
	else
	return true;
}

/**
 * 检查身份证
 * 0失败1成功
 */
function getIDCardInfo($IDCard) {
	if (!preg_match("/^[1-9]([0-9a-zA-Z]{17}|[0-9a-zA-Z]{14})$/", $IDCard)) {
		$flag = 0;
	} else {
		if (strlen($IDCard) == 18) {
			$tyear = intval(substr($IDCard, 6, 4));
			$tmonth = intval(substr($IDCard, 10, 2));
			$tday = intval(substr($IDCard, 12, 2));
			if ($tyear > date("Y") || $tyear < (date("Y") - 100)) {
				$flag = 0;
			}
			elseif ($tmonth < 0 || $tmonth > 12) {
				$flag = 0;
			}
			elseif ($tday < 0 || $tday > 31) {
				$flag = 0;

			} else {
				$tdate = $tyear . "-" . $tmonth . "-" . $tday . " 00:00:00";
				if ((time() - mktime(0, 0, 0, $tmonth, $tday, $tyear)) < 18 * 365 * 24 * 60 * 60) {
					$flag = 0;
				} else {
					$flag = 1;
				}
			}

		}
		elseif (strlen($IDCard) == 15) {
			$tyear = intval("19" . substr($IDCard, 6, 2));
			$tmonth = intval(substr($IDCard, 8, 2));
			$tday = intval(substr($IDCard, 10, 2));
			if ($tyear > date("Y") || $tyear < (date("Y") - 100)) {
				$flag = 0;
			}
			elseif ($tmonth < 0 || $tmonth > 12) {
				$flag = 0;
			}
			elseif ($tday < 0 || $tday > 31) {
				$flag = 0;
			} else {
				$tdate = $tyear . "-" . $tmonth . "-" . $tday . " 00:00:00";
				if ((time() - mktime(0, 0, 0, $tmonth, $tday, $tyear)) < 18 * 365 * 24 * 60 * 60) {
					$flag = 0;
				} else {
					$flag = 1;
				}
			}
		}
	}
	return $flag;
}

//跳转
function app_redirect($url,$time=0,$msg='')
{
	if(!defined("SITE_DOMAIN"))
		define("SITE_DOMAIN",get_domain());
    //多行URL地址支持
    $url = str_replace(array("\n", "\r"), '', $url);    
    if(empty($msg))
        $msg    =   "系统将在{$time}秒之后自动跳转到{$url}！";
    if (!headers_sent()) {
        // redirect
        if(0===$time) {
        	if(substr($url,0,1)=="/")
        	{        		
        		header("Location:".SITE_DOMAIN.$url);
        	}
        	else
        	{
        		header("Location:".$url);
        	}
            
        }else {
            header("refresh:{$time};url={$url}");
            echo($msg);
        }
        exit();
    }else {
        $str    = "<meta http-equiv='Refresh' content='{$time};URL={$url}'>";
        if($time!=0)
            $str   .=   $msg;
        exit($str);
    }
}



/**
 * 验证访问IP的有效性
 * @param ip地址 $ip_str
 * @param 访问页面 $module
 * @param 时间间隔 $time_span
 * @param 数据ID $id
 */
function check_ipop_limit($ip_str,$module,$time_span=0,$id=0)
{
		$op = es_session::get($module."_".$id."_ip");
    	if(empty($op))
    	{
    		$check['ip']	=	 CLIENT_IP;
    		$check['time']	=	TIME_UTC;
    		es_session::set($module."_".$id."_ip",$check);    		
    		return true;  //不存在session时验证通过
    	}
    	else 
    	{   
    		$check['ip']	=	 CLIENT_IP;
    		$check['time']	=	TIME_UTC;    
    		$origin	=	es_session::get($module."_".$id."_ip");
    		
    		if($check['ip']==$origin['ip'])
    		{
    			if($check['time'] - $origin['time'] < $time_span)
    			{
    				return false;
    			}
    			else 
    			{
    				es_session::set($module."_".$id."_ip",$check);
    				return true;  //不存在session时验证通过    				
    			}
    		}
    		else 
    		{
    			es_session::set($module."_".$id."_ip",$check);
    			return true;  //不存在session时验证通过
    		}
    	}
    }

/**
 * 还款返利操作
 * $dlr_id  表deal_load_repay id
 */
function get_referrals($dlr_id){
	if(intval(app_conf("INVITE_REFERRALS_DATE")) > 0){
		$after_year =  next_replay_month(to_timespan(to_date(TIME_UTC,"Y-m-d")),-intval(app_conf("INVITE_REFERRALS_DATE")));
		$condition =" AND u.create_time >= ".$after_year." and dl.create_time  > ".$after_year."  ";
	}
	
	$sql = "select dlr.*,(IF (dlr.t_user_id > 0 ,dlr.t_user_id,dlr.user_id)) as f_user_id,dl.create_time f_create_time,u.create_time,(dlr.true_interest_money * u.referral_rate * 0.01 ) as smoney,(dlr.true_self_money * u.referral_rate * 0.01 ) as ssmoney,u.user_name,u.referral_rate,u.pid " .
			"from ".DB_PREFIX."deal_load_repay dlr " .
			"LEFT JOIN ".DB_PREFIX."user u ON u.id=(IF (dlr.t_user_id > 0 ,dlr.t_user_id,dlr.user_id)) " .
			"LEFT JOIN ".DB_PREFIX."deal_load dl ON dl.id=dlr.load_id " .
			"where u.pid > 0 AND dlr.id = ".$dlr_id." AND u.referral_rate > 0 and u.user_type in (0,1) $condition ";
	$referrals_data = $GLOBALS['db']->getRow($sql);
	if(floatval($referrals_data['smoney']) != 0 )
	{
		$data['deal_id'] = $referrals_data['deal_id'];
		$data['load_id'] = $referrals_data['load_id'];
		$data['l_key'] = $referrals_data['l_key'];
		$data['money'] = intval(app_conf("INVITE_REFERRALS_TYPE")) ==0 ? $referrals_data['smoney'] : $referrals_data['ssmoney'];
		$data['user_id'] = $referrals_data['f_user_id'];
		$data['rel_user_id'] = $referrals_data['pid'];
		$data['referral_type'] = intval(app_conf("INVITE_REFERRALS_TYPE")) ==0 ? 0 : 1;
		$data['repay_time'] = $referrals_data['repay_time'];
		$data['create_time'] = TIME_UTC;
		$GLOBALS['db']->autoExecute(DB_PREFIX."referrals",$data,"INSERT");
		$id = $GLOBALS['db']->insert_id();
		if($id > 0)
		{
			if(intval(app_conf("INVITE_REFERRALS_AUTO")) == 1){
				//开始发放返利
				require_once APP_ROOT_PATH."system/libs/user.php";
				
				$lkey = $referrals_data['l_key'] + 1;
				$referral_amount = $referrals_data['smoney']>0?format_price($referrals_data['smoney']):0;
				require_once APP_ROOT_PATH."app/Lib/deal.php";
				$deal = get_deal($id);
				$msg = '[<a href="'.$deal['url'].'" target="_blank">'.$deal['name'].'</a>],第'.$lkey.'期,还款获取邀请返利';
				modify_account(array('money'=>$referrals_data['smoney']),$referrals_data['pid'],$msg,23);
				$GLOBALS['db']->query("UPDATE ".DB_PREFIX."referrals  SET pay_time=".TIME_UTC." WHERE id=".$id);
			}
			
			return true;
		}
		else
		{
			return false;
		}
	}
	else
	{
		return false;
	}
}
/**
 * 手动返利操作
 * $id  标 referrals id
 */
function pay_referrals($id)
{
	$sql = "select * from ".DB_PREFIX."referrals where id = ".$id;
	$referrals_data = $GLOBALS['db']->getRow($sql);
	
	if(floatval($referrals_data['money']) != 0)
	{
		//开始发放返利
		require_once APP_ROOT_PATH."system/libs/user.php";
		$lkey = $referrals_data['l_key'] + 1;
		$referral_amount = format_price($referrals_data['money']);
		require_once(APP_ROOT_PATH."app/Lib/common.php");
		require_once APP_ROOT_PATH."app/Lib/deal.php";
		$deal = get_deal($referrals_data['deal_id']);
		
		$msg = '[<a href="'.$deal['url'].'" target="_blank">'.$deal['name'].'</a>],第'.$lkey.'期,还款获取邀请返利';
		modify_account(array('money'=>$referrals_data['money']),$referrals_data['rel_user_id'],$msg,23);
		$GLOBALS['db']->query("UPDATE ".DB_PREFIX."referrals  SET pay_time=".TIME_UTC." WHERE id=".$id);
		return true;
		
	}
	else
	{
		return false;
	}
}


function get_deal_mail_content($deal_rs)
{
	$tmpl_content = file_get_contents(APP_ROOT_PATH."app/Tpl/".app_conf("TEMPLATE")."/deal_mail.html");
	$GLOBALS['tmpl']->assign("APP_ROOT",APP_ROOT);
	$tmpl_path = app_conf("TMPL_DOMAIN_ROOT")==''?SITE_DOMAIN.APP_ROOT:app_conf("TMPL_DOMAIN_ROOT")."/";
	if($deal_rs)
	{
		foreach($deal_rs as $k=>$deal)
		{
			$send_date = to_date(TIME_UTC,'Y年m月d日');
			$weekarray = array("日","一","二","三","四","五","六");
			$send_date .= " 星期".$weekarray[to_date(TIME_UTC,"w")];
			$deal['send_date'] = $send_date;
			$deal['url'] = $tmpl_path.url("index","deal",array("id"=>$deal['id']));
			$deal['borrow_amount_format'] = $deal_rs[$k]['borrow_amount'];
			$deal['rate_foramt'] = $deal_rs[$k]['rate'];
			$deal['rate_foramt'] = $deal_rs[$k]['type_match_row'];
			$deal_rs[$k] = $deal;
		}
		$GLOBALS['tmpl']->assign("deal_rs",$deal_rs);
		$content = $GLOBALS['tmpl']->fetch("str:".$tmpl_content);	
		//$tmpl_path = app_conf("TMPL_DOMAIN_ROOT")==''?SITE_DOMAIN.APP_ROOT."/app/Tpl/":app_conf("TMPL_DOMAIN_ROOT")."/";
		$content = str_replace("deal_mail/",$tmpl_path.app_conf("TEMPLATE")."/deal_mail/",$content);
		//print_r($deal_rs);die;
		return $content;
	}
	else
	return '';
}

	

/**
 * $notice.site_name
 * $notice.deal_name
 * $notice.site_url
 * @param $deal_id
 */
function get_deal_sms_content($deal_id)
{
	$tmpl_content = $GLOBALS['db']->getOne("select content from ".DB_PREFIX."msg_template where name = 'TPL_DEAL_NOTICE_SMS'");
	$deal = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."deal where id = ".$deal_id);
	if($deal)
	{
		$notice['site_name'] = app_conf("SHOP_TITLE");
		$notice['deal_name'] = $deal['sub_name'];
		$notice['site_url'] = SITE_DOMAIN.APP_ROOT;
		$GLOBALS['tmpl']->assign("notice",$notice);
		$content = $GLOBALS['tmpl']->fetch("str:".$tmpl_content);
		return $content;
	}
	else
	return '';
}

/**
 * $bond.sn
 * $bond.password
 * $bond.name
 * $bond.user_name
 * $bond.begin_time_format
 * $bond.end_time_format
 * $bond.tel
 * $bond.address
 * $bond.route
 * $bond.open_time
 * @param $coupon_id
 * @param $location_id
 */


function gzip_out($content)
{
	header("Content-type: text/html; charset=utf-8");
    header("Cache-control: private");  //支持页面回跳
	$gzip = app_conf("GZIP_ON");
	if( intval($gzip)==1 )
	{
		if(!headers_sent()&&extension_loaded("zlib")&&preg_match("/gzip/i",$_SERVER["HTTP_ACCEPT_ENCODING"]))
		{
			$content = gzencode($content,9);	
			header("Content-Encoding: gzip");
			header("Content-Length: ".strlen($content));
			echo $content;
		}
		else
		echo $content;
	}else{
		echo $content;
	}
	
}

function order_log($log_info,$order_id)
{
	$data['id'] = 0;
	$data['log_info'] = $log_info;
	$data['log_time'] = TIME_UTC;
	$data['order_id'] = $order_id;
	$GLOBALS['db']->autoExecute(DB_PREFIX."deal_order_log", $data);
}


/**
	 * 保存图片
	 * @param array $upd_file  即上传的$_FILES数组
	 * @param array $key $_FILES 中的键名 为空则保存 $_FILES 中的所有图片
	 * @param string $dir 保存到的目录
	 * @param array $whs
	 	可生成多个缩略图
		数组 参数1 为宽度，
			 参数2为高度，
			 参数3为处理方式:0(缩放,默认)，1(剪裁)，
			 参数4为是否水印 默认为 0(不生成水印)
	 	array(
			'thumb1'=>array(300,300,0,0),
			'thumb2'=>array(100,100,0,0),
			'origin'=>array(0,0,0,0),  宽与高为0为直接上传
			...
		)，
	 * @param array $is_water 原图是否水印
	 * @return array
	 	array(
			'key'=>array(
				'name'=>图片名称，
				'url'=>原图web路径，
				'path'=>原图物理路径，
				有略图时
				'thumb'=>array(
					'thumb1'=>array('url'=>web路径,'path'=>物理路径),
					'thumb2'=>array('url'=>web路径,'path'=>物理路径),
					...
				)
			)
			....
		)
	 */
//$img = save_image_upload($_FILES,'avatar','temp',array('avatar'=>array(300,300,1,1)),1);
function save_image_upload($upd_file, $key='',$dir='temp', $whs=array(),$is_water=false,$need_return = false)
{
		require_once APP_ROOT_PATH."system/utils/es_imagecls.php";
		$image = new es_imagecls();
		$image->max_size = intval(app_conf("MAX_IMAGE_SIZE"));
		
		$list = array();

		if(empty($key))
		{
			foreach($upd_file as $fkey=>$file)
			{
				$list[$fkey] = false;
				$image->init($file,$dir);
				if($image->save())
				{
					$list[$fkey] = array();
					$list[$fkey]['url'] = $image->file['target'];
					$list[$fkey]['path'] = $image->file['local_target'];
					$list[$fkey]['name'] = $image->file['prefix'];
				}
				else
				{
					if($image->error_code==-105)
					{
						if($need_return)
						{
							return array('error'=>1,'message'=>'上传的图片太大');
						}
						else
						echo "上传的图片太大";
					}
					elseif($image->error_code==-104||$image->error_code==-103||$image->error_code==-102||$image->error_code==-101)
					{
						if($need_return)
						{
							return array('error'=>1,'message'=>'非法图像');
						}
						else
						echo "非法图像";
					}
					exit;
				}
			}
		}
		else
		{
			$list[$key] = false;
			$image->init($upd_file[$key],$dir);
			if($image->save())
			{
				$list[$key] = array();
				$list[$key]['url'] = $image->file['target'];
				$list[$key]['path'] = $image->file['local_target'];
				$list[$key]['name'] = $image->file['prefix'];
			}
			else
				{
					if($image->error_code==-105)
					{
						if($need_return)
						{
							return array('error'=>1,'message'=>'上传的图片太大');
						}
						else
						echo "上传的图片太大";
					}
					elseif($image->error_code==-104||$image->error_code==-103||$image->error_code==-102||$image->error_code==-101)
					{
						if($need_return)
						{
							return array('error'=>1,'message'=>'非法图像');
						}
						else
						echo "非法图像";
					}
					exit;
				}
		}

		$water_image = APP_ROOT_PATH.app_conf("WATER_MARK");
		$alpha = app_conf("WATER_ALPHA");
		$place = app_conf("WATER_POSITION");
		
		foreach($list as $lkey=>$item)
		{
				//循环生成规格图
				foreach($whs as $tkey=>$wh)
				{
					$list[$lkey]['thumb'][$tkey]['url'] = false;
					$list[$lkey]['thumb'][$tkey]['path'] = false;
					if($wh[0] > 0 || $wh[1] > 0)  //有宽高度
					{
						$thumb_type = isset($wh[2]) ? intval($wh[2]) : 0;  //剪裁还是缩放， 0缩放 1剪裁
						if($thumb = $image->thumb($item['path'],$wh[0],$wh[1],$thumb_type))
						{
							$list[$lkey]['thumb'][$tkey]['url'] = $thumb['url'];
							$list[$lkey]['thumb'][$tkey]['path'] = $thumb['path'];
							if(isset($wh[3]) && intval($wh[3]) > 0)//需要水印
							{
								$paths = pathinfo($list[$lkey]['thumb'][$tkey]['path']);
								$path = $paths['dirname'];
				        		$path = $path."/origin/";
				        		if (!is_dir($path)) { 
						             @mkdir($path);
						             @chmod($path, 0777);
					   			}   	    
				        		$filename = $paths['basename'];
								@file_put_contents($path.$filename,@file_get_contents($list[$lkey]['thumb'][$tkey]['path']));      
								$image->water($list[$lkey]['thumb'][$tkey]['path'],$water_image,$alpha, $place);
							}
						}
					}
				}
			if($is_water)
			{
				$paths = pathinfo($item['path']);
				$path = $paths['dirname'];
        		$path = $path."/origin/";
        		if (!is_dir($path)) { 
		             @mkdir($path);
		             @chmod($path, 0777);
	   			}   	    
        		$filename = $paths['basename'];
				@file_put_contents($path.$filename,@file_get_contents($item['path']));        		
				$image->water($item['path'],$water_image,$alpha, $place);
			}
		}			
		return $list;
}

function empty_tag($string)
{	
	$string = preg_replace(array("/\[img\]\d+\[\/img\]/","/\[[^\]]+\]/"),array("",""),$string);
	if(trim($string)=='')
	return $GLOBALS['lang']['ONLY_IMG'];
	else 
	return $string;
	//$string = str_replace(array("[img]","[/img]"),array("",""),$string);
}

//验证是否有非法字汇，未完成
function valid_str($string)
{
	$string = msubstr($string,0,5000);
	if(app_conf("FILTER_WORD")!='')
	$string = preg_replace("/".app_conf("FILTER_WORD")."/","*",$string);
	return $string;
}


/**
 * utf8字符转Unicode字符
 * @param string $char 要转换的单字符
 * @return void
 */
function utf8_to_unicode($char)
{
	switch(strlen($char))
	{
		case 1:
			return ord($char);
		case 2:
			$n = (ord($char[0]) & 0x3f) << 6;
			$n += ord($char[1]) & 0x3f;
			return $n;
		case 3:
			$n = (ord($char[0]) & 0x1f) << 12;
			$n += (ord($char[1]) & 0x3f) << 6;
			$n += ord($char[2]) & 0x3f;
			return $n;
		case 4:
			$n = (ord($char[0]) & 0x0f) << 18;
			$n += (ord($char[1]) & 0x3f) << 12;
			$n += (ord($char[2]) & 0x3f) << 6;
			$n += ord($char[3]) & 0x3f;
			return $n;
	}
}

/**
 * utf8字符串分隔为unicode字符串
 * @param string $str 要转换的字符串
 * @param string $depart 分隔,默认为空格为单字
 * @return string
 */
function str_to_unicode_word($str,$depart=' ')
{
	$arr = array();
	$str_len = mb_strlen($str,'utf-8');
	for($i = 0;$i < $str_len;$i++)
	{
		$s = mb_substr($str,$i,1,'utf-8');
		if($s != ' ' && $s != '　')
		{
			$arr[] = 'ux'.utf8_to_unicode($s);
		}
	}
	return implode($depart,$arr);
}


/**
 * utf8字符串分隔为unicode字符串
 * @param string $str 要转换的字符串
 * @return string
 */
function str_to_unicode_string($str)
{
	$string = str_to_unicode_word($str,'');
	return $string;
}

//分词
function div_str($str)
{
	require_once APP_ROOT_PATH."system/libs/words.php";
	$words = words::segment($str);
	$words[] = $str;	
	return $words;
}


/**
 * 
 * @param $tag  //要插入的关键词
 * @param $table  //表名
 * @param $id  //数据ID
 * @param $field		// tag_match/name_match/cate_match/locate_match
 */
function insert_match_item($tag,$table,$id,$field)
{
	if($tag=='')
	return;
	
	$unicode_tag = str_to_unicode_string($tag);
	$sql = "select count(*) from ".DB_PREFIX.$table." where match(".$field.") against ('".$unicode_tag."' IN BOOLEAN MODE) and id = ".$id;
	$rs = $GLOBALS['db']->getOne($sql);
	if(intval($rs) == 0)
	{
		$match_row = $GLOBALS['db']->getRow("select * from ".DB_PREFIX.$table." where id = ".$id);
		if($match_row[$field]=="")
		{
				$match_row[$field] = $unicode_tag;
				$match_row[$field."_row"] = $tag;
		}
		else
		{
				$match_row[$field] = $match_row[$field].",".$unicode_tag;
				$match_row[$field."_row"] = $match_row[$field."_row"].",".$tag;
		}
		$GLOBALS['db']->autoExecute(DB_PREFIX.$table, $match_row, $mode = 'UPDATE', "id=".$id, $querymode = 'SILENT');	
		
	}	
}

function get_all_parent_id($id,$table,&$arr = array())
{
	if(intval($id)>0)
	{
		$arr[] = $id;
		$pid = $GLOBALS['db']->getOne("select pid from ".$table." where id = ".$id);
		if($pid>0)
		{
			get_all_parent_id($pid,$table,$arr);
		}
	}
}

function syn_deal_match($deal_id)
{
	$deal = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."deal where id = ".$deal_id);
	if($deal)
	{
		$deal['name_match'] = "";
		$deal['name_match_row'] = "";
		$deal['deal_cate_match'] = "";
		$deal['deal_cate_match_row'] = "";
		$deal['type_match'] = "";
		$deal['type_match_row'] = "";
		$deal['tag_match'] = "";
		$deal['tag_match_row'] = "";
		$GLOBALS['db']->autoExecute(DB_PREFIX."deal", $deal, $mode = 'UPDATE', "id=".$deal_id, $querymode = 'SILENT');	
	
		//同步名称
		$name_arr = div_str(trim($deal['name'])); 
		foreach($name_arr as $name_item)
		{
			insert_match_item($name_item,"deal",$deal_id,"name_match");
		}
		
		//分类类别
		$deal_cate =array();		
		get_all_parent_id(intval($deal['cate_id']),DB_PREFIX."deal_cate",$deal_cate);
		if(count($deal_cate)>0)
		{
			$deal_cates = $GLOBALS['db']->getAll("select name from ".DB_PREFIX."deal_cate where id in (".implode(",",$deal_cate).")");
			foreach ($deal_cates as $row)
			{
				insert_match_item(trim($row['name']),"deal",$deal_id,"deal_cate_match");
			}
		}
		$goods_cate =array();
		get_all_parent_id(intval($deal['type_id']),DB_PREFIX."deal_loan_type",$goods_cate);
		if(count($goods_cate)>0)
		{
			$goods_cates = $GLOBALS['db']->getAll("select name from ".DB_PREFIX."deal_loan_type where id in (".implode(",",$goods_cate).")");
			foreach ($goods_cates as $row)
			{
				insert_match_item(trim($row['name']),"deal",$deal_id,"type_match");
			}
		}
		
		
	}	
}


//封装url

function url($app_index,$route="index",$param=array())
{
	$key = md5("URL_KEY_".$app_index.$route.serialize($param));
	if ($GLOBALS['request']['from']!="wap"){
		if(isset($GLOBALS[$key]))
		{
			$url = $GLOBALS[$key];
			return $url;
		}
		
		$url = load_dynamic_cache($key);
		if($url!==false)
		{
			$GLOBALS[$key] = $url;
			return $url;
		}
	}
	
	$route_array = explode("#",$route);
	
	if(isset($param)&&$param!=''&&!is_array($param))
	{
		$param['id'] = $param;
	}

	$module = strtolower(trim($route_array[0]));
	$action = strtolower(trim($route_array[1]));

	if(!$module||$module=='index')$module="";
	if(!$action||$action=='index')$action="";
	
	if(app_conf("URL_MODEL")==0 || $GLOBALS['request']['from']=="wap")
	{
		//过滤主要的应用url
		if($app_index==app_conf("MAIN_APP"))
			$app_index = "index";
		if(substr($module,0,3)=="uc_"){
			$app_index = "member";
		}
		
		//原始模式
		$url = APP_ROOT."/".$app_index.".php";
		if($module!=''||$action!=''||count($param)>0) //有后缀参数
		{
			$url.="?";
		}

			
		if($module&&$module!='')
		$url .= "ctl=".$module."&";
		if($action&&$action!='')
		$url .= "act=".$action."&";
		if(count($param)>0)
		{
			foreach($param as $k=>$v)
			{
				if($k&&$v)
				$url =$url.$k."=".urlencode($v)."&";
			}
		}
		if(substr($url,-1,1)=='&'||substr($url,-1,1)=='?') $url = substr($url,0,-1);
		if ($GLOBALS['request']['from']!="wap"){
			$GLOBALS[$key] = $url;
			set_dynamic_cache($key,$url);
		}
		return $url;
	}
	else
	{
		//重写的默认
		$url = APP_ROOT;
	
		if($app_index!='index')
		$url .= "/".$app_index;

		if($module&&$module!='')
		$url .= "/".$module;
		if($action&&$action!='')
		$url .= "-".$action;
		
		if(count($param)>0)
		{
			$url.="/";
			foreach($param as $k=>$v)
			{
				$url =$url.$k."-".urlencode($v)."-";
			}
		}
		
		//过滤主要的应用url
		if($app_index==app_conf("MAIN_APP"))
		$url = str_replace("/".app_conf("MAIN_APP"),"",$url);
		
		$route = $module."#".$action;
		switch ($route)
		{
				case "xxx":
					break;
				default:
					break;
		}
				
		if(substr($url,-1,1)=='/'||substr($url,-1,1)=='-') $url = substr($url,0,-1);
		
		
		if($url=='')$url="/";
		$GLOBALS[$key] = $url;
		set_dynamic_cache($key,$url);
		return $url;
	}
	
	
}


function unicode_encode($name) {//to Unicode
    $name = iconv('UTF-8', 'UCS-2', $name);
    $len = strlen($name);
    $str = '';
    for($i = 0; $i < $len - 1; $i = $i + 2) {
        $c = $name[$i];
        $c2 = $name[$i + 1];
        if (ord($c) > 0) {// 两个字节的字
            $cn_word = '\\'.base_convert(ord($c), 10, 16).base_convert(ord($c2), 10, 16);
            $str .= strtoupper($cn_word);
        } else {
            $str .= $c2;
        }
    }
    return $str;
}

function unicode_decode($name) {//Unicode to
    $pattern = '/([\w]+)|(\\\u([\w]{4}))/i';
    preg_match_all($pattern, $name, $matches);
    if (!empty($matches)) {
        $name = '';
        for ($j = 0; $j < count($matches[0]); $j++) {
            $str = $matches[0][$j];
            if (strpos($str, '\\u') === 0) {
                $code = base_convert(substr($str, 2, 2), 16, 10);
                $code2 = base_convert(substr($str, 4), 16, 10);
                $c = chr($code).chr($code2);
                $c = iconv('UCS-2', 'UTF-8', $c);
                $name .= $c;
            } else {
                $name .= $str;
            }
        }
    }
    return $name;
}


//载入动态缓存数据
function load_dynamic_cache($name)
{
	if(isset($GLOBALS['dynamic_cache'][$name]))
	{
		return $GLOBALS['dynamic_cache'][$name];
	}
	else
	{
		return false;
	}
}

function set_dynamic_cache($name,$value)
{
	if(!isset($GLOBALS['dynamic_cache'][$name]))
	{
		if(count($GLOBALS['dynamic_cache'])>MAX_DYNAMIC_CACHE_SIZE)
		{
			array_shift($GLOBALS['dynamic_cache']);
		}
		$GLOBALS['dynamic_cache'][$name] = $value;		
	}
}


function load_auto_cache($key,$param=array())
{
	require_once APP_ROOT_PATH."system/libs/auto_cache.php";
	$file =  APP_ROOT_PATH."system/auto_cache/".$key.".auto_cache.php";
	if(file_exists($file))
	{
		require_once $file;
		$class = $key."_auto_cache";
		$obj = new $class;
		$result = $obj->load($param);
	}
	else
	$result = false;
	return $result;
}

function rm_auto_cache($key,$param=array())
{
	require_once APP_ROOT_PATH."system/libs/auto_cache.php";
	$file =  APP_ROOT_PATH."system/auto_cache/".$key.".auto_cache.php";
	if(file_exists($file))
	{
		require_once $file;
		$class = $key."_auto_cache";
		$obj = new $class;
		$obj->rm($param);
	}
}


function clear_auto_cache($key)
{
	require_once APP_ROOT_PATH."system/libs/auto_cache.php";
	$file =  APP_ROOT_PATH."system/auto_cache/".$key.".auto_cache.php";
	if(file_exists($file))
	{
		require_once $file;
		$class = $key."_auto_cache";
		$obj = new $class;
		$obj->clear_all();
	}
}

//获取随机会员提供关注
function get_rand_user($count,$is_daren=0,$uid=0)
{
	/*//第0阶梯达人，10个会员
	$danren_result_0 = $GLOBALS['cache']->get("RAND_USER_CACHE_DAREN_0");
	if($danren_result_0===false)
	{
		$sql = "select id,user_name,province_id,city_id from ".DB_PREFIX."user where is_daren = 1 order by is_merchant desc,is_daren desc,topic_count desc limit 10";	
		$danren_result_0 = $GLOBALS['db']->getAll($sql);
		if($danren_result_0)
		$GLOBALS['cache']->set("RAND_USER_CACHE_DAREN_0",$danren_result_0,3600);
		else
		$GLOBALS['cache']->set("RAND_USER_CACHE_DAREN_0",array(),3600);
	}	
	
	//第1阶梯达人，50个会员
	$danren_result_1 = $GLOBALS['cache']->get("RAND_USER_CACHE_DAREN_1");
	if($danren_result_1===false)
	{
		$sql = "select id,user_name,province_id,city_id from ".DB_PREFIX."user where is_daren = 1 order by is_merchant desc,is_daren desc,topic_count desc limit 10,50";	
		$danren_result_1 = $GLOBALS['db']->getAll($sql);
		if($danren_result_1)
		$GLOBALS['cache']->set("RAND_USER_CACHE_DAREN_1",$danren_result_1,3600);
		else
		$GLOBALS['cache']->set("RAND_USER_CACHE_DAREN_1",array(),3600);
	}
	
	//第2阶梯达人，2000个会员
	$danren_result_2 = $GLOBALS['cache']->get("RAND_USER_CACHE_DAREN_2");
	if($danren_result_2===false)
	{
		$sql = "select id,user_name,province_id,city_id from ".DB_PREFIX."user where is_daren = 1 order by is_merchant desc,is_daren desc,topic_count desc limit 50,2000";	
		$danren_result_2 = $GLOBALS['db']->getAll($sql);
		if($danren_result_2)
		$GLOBALS['cache']->set("RAND_USER_CACHE_DAREN_2",$danren_result_2,3600);
		else
		$GLOBALS['cache']->set("RAND_USER_CACHE_DAREN_2",array(),3600);
	}
	
	$danren_list[] = $danren_result_0;
	$danren_list[] = $danren_result_1;
	$danren_list[] = $danren_result_2;
	
	//非达人 , 2000个活跃会员
	$nodanren_result = $GLOBALS['cache']->get("RAND_USER_CACHE_NODAREN");
	if($nodanren_result===false)
	{
		$sql = "select id,user_name,province_id,city_id from ".DB_PREFIX."user where is_daren = 0 order by is_merchant desc,is_daren desc,topic_count desc limit 2000";	
		$nodanren_result = $GLOBALS['db']->getAll($sql);
		if($nodanren_result)
		$GLOBALS['cache']->set("RAND_USER_CACHE_NODAREN",$nodanren_result,3600);
		else
		$GLOBALS['cache']->set("RAND_USER_CACHE_NODAREN",array(),3600);
	}	
	
	$user_list = array();
	if($uid==0)
	{
		$user_group = 0; //阶梯数		
		while(count($user_list)<$count&&$user_group<3)
		{
			$current_count = count($user_list);
			for($loop=0;$loop<$count-$current_count;$loop++)
			{				
				$i = rand(0,count($danren_list[$user_group])-1);				
				$user_item = $danren_list[$user_group][$i];
				unset($danren_list[$user_group][$i]);
				sort($danren_list[$user_group]);
				if($user_item)
				$user_list[] = $user_item;
			}
			$user_group++;			
		}
		
		if(count($user_list)<$count&&$is_daren==0)
		{
			//人数还不足，并允许非达人
			$current_count = count($user_list);
			for($loop=0;$loop<$count-$current_count;$loop++)
			{				
				$i = rand(0,count($nodanren_result)-1);				
				$user_item = $nodanren_result[$i];
				unset($nodanren_result[$i]);
				sort($nodanren_result);
				if($user_item)
				$user_list[] = $user_item;
			}
		}

	}
	else
	{
		
		
		$user_group = 0; //阶梯数		
		while(count($user_list)<$count&&$user_group<3)
		{
			$current_count = count($user_list);
			//$loop_count 用于限制循环上限, $c用于计算个数, $i标识当前位置
			for($loop_count=0,$c=0;$c<$count-$current_count&&$loop_count<100;$loop_count++,$c++)
			{				
				$i = rand(0,count($danren_list[$user_group])-1);				
				$user_item = $danren_list[$user_group][$i];
				unset($danren_list[$user_group][$i]);
				sort($danren_list[$user_group]);
				if($user_item)
				{
					if($user_item['id']!=$uid&&$GLOBALS['db']->getOne("select count(*) from ".DB_PREFIX."user_focus where focus_user_id=".$uid." and focused_user_id = ".intval($user_item['id']))==0)
					$user_list[] = $user_item;							
					else					
					$c--;									
				}
							
			}
			$user_group++;			
		}
		
		if(count($user_list)<$count&&$is_daren==0)
		{
			//人数还不足，并允许非达人
			
			$current_count = count($user_list);
			for($loop_count=0,$c=0;$c<$count-$current_count&&$loop_count<100;$loop_count++,$c++)
			{
				$i = rand(0,count($nodanren_result)-1);				
				$user_item = $nodanren_result[$i];
				unset($nodanren_result[$i]);
				sort($nodanren_result);
				if($user_item)
				{
					if($user_item['id']!=$uid&&$GLOBALS['db']->getOne("select count(*) from ".DB_PREFIX."user_focus where focus_user_id=".$uid." and focused_user_id = ".intval($user_item['id']))==0)
					$user_list[] = $user_item;							
					else					
					$c--;									
				}		
			}
		}		
		
	}
	return $user_list;
	*/
}

/*ajax返回*/
function ajax_return($data)
{
		header("Content-Type:text/html; charset=utf-8");
        echo(json_encode($data));
        exit;	
}


//增加会员活跃度
function increase_user_active($user_id,$log)
{
	$t_begin_time = to_timespan(to_date(TIME_UTC,"Y-m-d"));  //今天开始
	$t_end_time = to_timespan(to_date(TIME_UTC,"Y-m-d"))+ (24*3600 - 1);  //今天结束
	$y_begin_time = $t_begin_time - (24*3600); //昨天开始
	$y_end_time = $t_end_time - (24*3600);  //昨天结束
	
	$point = intval(app_conf("USER_ACTIVE_POINT"));
	$score = intval(app_conf("USER_ACTIVE_SCORE"));
	$money = doubleval(app_conf("USER_ACTIVE_MONEY"));
	$point_max = intval(app_conf("USER_ACTIVE_POINT_MAX"));
	$score_max = intval(app_conf("USER_ACTIVE_SCORE_MAX"));
	$money_max = doubleval(app_conf("USER_ACTIVE_MONEY_MAX"));
	
	$sum_money = doubleval($GLOBALS['db']->getOne("select sum(money) from ".DB_PREFIX."user_active_log where user_id = ".$user_id." and create_time between ".$t_begin_time." and ".$t_end_time));
	$sum_score = intval($GLOBALS['db']->getOne("select sum(score) from ".DB_PREFIX."user_active_log where user_id = ".$user_id." and create_time between ".$t_begin_time." and ".$t_end_time));
	$sum_point = intval($GLOBALS['db']->getOne("select sum(point) from ".DB_PREFIX."user_active_log where user_id = ".$user_id." and create_time between ".$t_begin_time." and ".$t_end_time));
	
	if($sum_money>=$money_max)$money = 0;
	if($sum_score>=$score_max)$score = 0;
	if($sum_point>=$point_max)$point = 0;
	
	if($money>0||$score>0||$point>0)
	{
		require_once  APP_ROOT_PATH."system/libs/user.php";
		modify_account(array("money"=>$money,"score"=>$score,"point"=>$point),$user_id,$log);
		$data['user_id'] = $user_id;
		$data['create_time'] = TIME_UTC;
		$data['money'] = $money;
		$data['score'] = $score;
		$data['point'] = $point;
		$GLOBALS['db']->autoExecute(DB_PREFIX."user_active_log",$data);
	}
}

/**
 * 组装借款搜索条件
 */
function build_deal_filter_condition($param,$is_store=false)
{
	$cate_id = intval($param['cid']);
	$deal_type_id = intval($param['tid']);
	$city_id = intval($GLOBALS['deal_city']['id']);
	if($is_store){
		$deal_type = intval($param['deal_type']);
		$condition = " and deal_type = $deal_type ";
	}
	else{
		$condition="";
	}
	if($city_id>0)
	{			
		$ids = load_auto_cache("deal_city_belone_ids",array("city_id"=>$city_id));
		if($ids)
		$condition .= " and city_id in (".implode(",",$ids).")";
	}
	
	if($cate_id>0)
	{			
			$cate_name = $GLOBALS['db']->getOne("select name from ".DB_PREFIX."deal_cate where id = ".$cate_id);
			$cate_name_unicode = str_to_unicode_string($cate_name);
					
			if($deal_type_id>0)
			{
				$deal_type_name = $GLOBALS['db']->getOne("select name from ".DB_PREFIX."deal_cate_type where id = ".$deal_type_id);
				$deal_type_name_unicode = str_to_unicode_string($deal_type_name);
				$condition .= " and (match(deal_cate_match) against('+".$cate_name_unicode." +".$deal_type_name_unicode."' IN BOOLEAN MODE)) ";
			}
			else
			{
				$condition .= " and (match(deal_cate_match) against('".$cate_name_unicode."' IN BOOLEAN MODE)) ";
			}
	}
	
	return $condition;
}

function is_animated_gif($filename){
 $fp=fopen($filename, 'rb');
 $filecontent=fread($fp, filesize($filename));
 fclose($fp);
 return strpos($filecontent,chr(0x21).chr(0xff).chr(0x0b).'NETSCAPE2.0')===FALSE?0:1;
}


function make_deal_cate_js()
{
	$js_file = APP_ROOT_PATH."public/runtime/app/deal_cate_conf.js";
	if(!file_exists($js_file))
	{
		$js_str = "var deal_cate_conf = [";
		$deal_cates = $GLOBALS['db']->getAll("select id,name from ".DB_PREFIX."deal_cate where is_delete = 0 and is_effect = 1 order by sort desc");
		foreach($deal_cates as $k=>$v)
		{
			$js_str.='{"n":"'.$v['name'].'","i":"'.$v['id'].'","s":[';
			$js_str .= ']},';
		}
		if($deal_cates)
		$js_str = substr($js_str,0,-1);
		$js_str.="];";
		@file_put_contents($js_file,$js_str);
	}
}

function make_deal_region_js()
{
	$dir = APP_ROOT_PATH."public/runtime/app/deal_region_conf/";
	if (!is_dir($dir))
    {
             @mkdir($dir);
             @chmod($dir, 0777);
    }  
	$js_file = $dir.intval($GLOBALS['deal_city']['id']).".js";
	if(!file_exists($js_file))
	{
		$js_str = "var deal_region_conf = [";
		$js_str.="];";
		@file_put_contents($js_file,$js_str);
	}
}


function make_delivery_region_js()
{
	$path = APP_ROOT_PATH."public/runtime/app/region.js"; 
	if(!file_exists($path))
	{
		$jsStr = "var regionConf = ".get_delivery_region_js();		
		@file_put_contents($path,$jsStr);
	}
}
function get_delivery_region_js($pid = 0)
{

		$jsStr = "";
		$childRegionList = $GLOBALS['db']->getAll("select * from ".DB_PREFIX."delivery_region where pid = ".$pid." order by id asc");
		foreach($childRegionList as $childRegion)
		{
			if(empty($jsStr))
				$jsStr .= "{";
			else
				$jsStr .= ",";
				
			$childStr = get_delivery_region_js($childRegion['id']);
			$jsStr .= "\"r$childRegion[id]\":{\"i\":$childRegion[id],\"n\":\"$childRegion[name]\",\"c\":$childStr}";
		}
		
		if(!empty($jsStr))
			$jsStr .= "}";
		else
			$jsStr .= "\"\"";
				
		return $jsStr;
 
}

function update_sys_config()
{
	$filename = APP_ROOT_PATH."public/sys_config.php";
	if(!file_exists($filename))
	{
		//定义DB
		require APP_ROOT_PATH.'system/db/db.php';
		$dbcfg = require APP_ROOT_PATH."public/db_config.php";
		define('DB_PREFIX', $dbcfg['DB_PREFIX']); 
		if(!file_exists(APP_ROOT_PATH.'public/runtime/app/db_caches/'))
			mkdir(APP_ROOT_PATH.'public/runtime/app/db_caches/',0777);
		$pconnect = false;
		$db = new mysql_db($dbcfg['DB_HOST'].":".$dbcfg['DB_PORT'], $dbcfg['DB_USER'],$dbcfg['DB_PWD'],$dbcfg['DB_NAME'],'utf8',$pconnect);
		//end 定义DB

		$sys_configs = $db->getAll("select * from ".DB_PREFIX."conf");
		$config_str = "<?php\n";
		$config_str .= "return array(\n";
		foreach($sys_configs as $k=>$v)
		{
			$config_str.="'".$v['name']."'=>'".addslashes($v['value'])."',\n";
		}
		$config_str.=");\n ?>";	
		file_put_contents($filename,$config_str);
		$url = APP_ROOT."/";
		app_redirect($url);
	}
}

/**
 * 等额本息还款计算方式
 * $money 贷款金额
 * $rate 月利率
 * $remoth 还几个月
 * 返回  每月还款额
 */
function pl_it_formula($money,$rate,$remoth){
	if((pow(1+$rate,$remoth)-1) > 0)
		return $money * ($rate*pow(1+$rate,$remoth)/(pow(1+$rate,$remoth)-1));
	else
		return 0;
}

/**
 * 按月还款计算方式
 * $total_money 贷款金额
 * $rate 年利率
 * 返回月应该还多少利息
 */
function av_it_formula($total_money,$rate){
	return $total_money * $rate;
}

/**
 * 获取该期剩余本金
 * int $Idx  第几期
 * int $all_idx 总的是几期
 * floatval $amount_money 总的借款多少
 * floatval $month_repay_money 月还本息
 * floatval $rate 费率
 */
function get_benjin($idx,$all_idx,$amount_money,$month_repay_money,$rate){
	//计算剩多少本金
	$benjin = $amount_money;
	for($i=1;$i<$idx+1;$i++){
		$benjin = $benjin - ($month_repay_money - $benjin*$rate/12/100);
	}
	return $benjin;
}
/**
 * 获取该期本金
 * int $Idx  第几期
 * floatval $amount_money 总的借款多少
 * floatval $month_repay_money 月还本息
 * floatval $rate 费率
 */
function get_self_money($idx,$amount_money,$month_repay_money,$rate){
	return $month_repay_money - get_benjin($idx,$idx,$amount_money,$month_repay_money,$rate)*$rate/12/100;
}

function getXmlNodeValue($xmlStr ,$nodeName){
	preg_match_all( "/\<".$nodeName."\>(.*?)\<\/".$nodeName."\>/s", $xmlStr, $nodeValue );
	return $nodeValue[1][0];
}

/**
 * 满标放款
 * $type 0 普通 1代表 第三方
 * $is_loan 0 不返款， 1 返款
 */
 function do_loans($id,$repay_start_time,$type = 0){
 	$return = array("status"=>0,"info"=>"");
	if($id==0){
		$return['info'] = "放款失败，借款不存在";
		return $return;
	}
	require_once(APP_ROOT_PATH."app/Lib/deal.php");
	
	$deal_info = get_deal($id);
	
	if(!$deal_info){
		$return['info'] = "放款失败，借款不存在";
		return $return;
	}
	if(!in_array($deal_info['deal_status'],array(2,4,5))){
		$return['info'] = "放款失败，借款不是满标状态";
		return $return;
	}
	if($type==0)
		$loan_data['repay_start_time'] = $repay_start_time==''?0:to_timespan(to_date(to_timespan($repay_start_time),"Y-m-d"),"Y-m-d");
	else
		$loan_data['repay_start_time'] = $repay_start_time;
		
	if($loan_data['repay_start_time']==0){
		$return['info'] = "放款失败，时间没选择";
		return $return;
	}
	
	if($type==0 && $deal_info['ips_bill_no']!=""){
		$return['status'] = 2;
		$return['info'] = "";
		$return['jump'] = APP_ROOT."/index.php?ctl=collocation&act=Transfer&pTransferType=1&deal_id=".$id."&ref_data=".$loan_data['repay_start_time']; 
		return $return;
	}
	
	if($loan_data['repay_start_time'] > 0){
		$deal_info['next_repay_time'] = $loan_data['next_repay_time'] = next_replay_month($loan_data['repay_start_time']);
	}
	$deal_info['deal_status'] = $loan_data['deal_status'] = 4;
	$deal_info['is_has_loans'] = $loan_data['is_has_loans'] = 1;
	$loan_data['repay_start_date'] = to_date($loan_data['repay_start_time'],"Y-m-d");
	
	//放款给用户
	$GLOBALS['db']->autoExecute(DB_PREFIX."deal",$loan_data,"UPDATE","id=".$id." AND is_has_loans=0 ");
	
	$deal_info['repay_start_time'] = $loan_data['repay_start_time'];
	
	if($GLOBALS['db']->affected_rows() > 0){
		format_deal_item($deal_info);
		require_once APP_ROOT_PATH."system/libs/user.php";
		if($type == 0){
			modify_account(array("money"=>$deal_info['borrow_amount']),$deal_info['user_id'],"[<a href='".$deal_info['url']."' target='_blank'>".$deal_info['name']."</a>],招标成功",3);
			//扣除服务费
			$services_fee = $deal_info['borrow_amount']*floatval(trim($deal_info['services_fee']))/100;
			modify_account(array("money"=>-$services_fee),$deal_info['user_id'],"[<a href='".$deal_info['url']."' target='_blank'>".$deal_info['name']."</a>],服务费",14);
			
		}
		
		//积分
		if($deal_info['score']!=0)
			modify_account(array("score"=>$deal_info['score']),$deal_info['user_id'],"[<a href='".$deal_info['url']."' target='_blank'>".$deal_info['name']."</a>],招标成功",3);
		
		$load_list = $GLOBALS['db']->getAll("SELECT id,user_id,`money`,`is_old_loan`,`rebate_money`,`bid_score`,`is_winning`,`income_type`,`income_value` FROM ".DB_PREFIX."deal_load where deal_id=".$id." and is_rebate = 0 ");
		foreach($load_list as $lk=>$lv){
			//扣除冻结资金
			if($type == 0){
				$GLOBALS['db']->query("UPDATE ".DB_PREFIX."deal_load SET is_has_loans =1 WHERE id=".$lv['id']." AND is_has_loans = 0 AND user_id=".$lv['user_id']);
				if($GLOBALS['db']->affected_rows()){
					if($lv['is_old_loan']==0)
						modify_account(array("lock_money"=>-$lv['money']),$lv['user_id'],"[<a href='".$deal_info['url']."' target='_blank'>".$deal_info['name']."</a>],投标成功",2);
				}
			}
			//返利给用户
			if(floatval($lv["rebate_money"])!=0 || intval($lv["bid_score"])!=0){
				$GLOBALS['db']->query("UPDATE ".DB_PREFIX."deal_load SET is_rebate =1 WHERE id=".$lv['id']." AND is_rebate = 0 AND user_id=".$lv['user_id']);
				if($GLOBALS['db']->affected_rows()){
					//返利
					if(floatval($lv["rebate_money"])!=0)
						modify_account(array("money"=>$lv['rebate_money']),$lv['user_id'],"[<a href='".$deal_info['url']."' target='_blank'>".$deal_info['name']."</a>],投资返利",24);
					//积分
					if(intval($lv["bid_score"])!=0)
						modify_account(array("score"=>$lv['bid_score']),$lv['user_id'],"[<a href='".$deal_info['url']."' target='_blank'>".$deal_info['name']."</a>],投资返积分",2);
					//VIP奖励 
					if(((int)$lv['income_type']==1 || (int)$lv['income_type']==2 || (int)$lv['income_type']==3 || (int)$lv['income_type']==4) && $lv['is_winning']==1){
						
						$user_msg_conf = get_user_msg_conf($lv['user_id']);
						//发放奖励
						if($lv['income_type']==1){
							//红包记录 增加用户金额与不可提现金额
							$red_envelope_date['user_id'] = $lv['user_id'];
							$red_envelope_date['deal_id'] = $id;
							$red_envelope_date['load_id'] = $lv['id'];
							$red_envelope_date['reward_name'] = "投标收益奖励";
							$red_envelope_date['gift_type'] = 1;
							$redmoney= $GLOBALS['db']->getOne("SELECT money FROM ".DB_PREFIX."vip_red_envelope WHERE id='".(int)$lv['income_value']."'");
							$red_envelope_date['gift_value']=$redmoney;
							$red_envelope_date['status'] = 1;
							$red_envelope_date['generation_date'] = to_date(TIME_UTC,"Y-m-d");
							$red_envelope_date['release_date'] = to_date(TIME_UTC,"Y-m-d");
							
							$is_send_mail = $user_msg_conf['mail_redenvelope'];
							$is_send_sms = $user_msg_conf['sms_redenvelope'];
							$TPL_MAIL_NAME = "TPL_MAIL_RED_ENVELOPE";
							$TPL_SMS_NAME = "TPL_SMS_RED_ENVELOPE";
							$gift_value = $redmoney;
							
							if($redmoney!=0&&$redmoney!=""){
								$GLOBALS['db']->autoExecute(DB_PREFIX."gift_record",$red_envelope_date); //插入
								modify_account(array('money'=>$redmoney,'nmc_amount'=>$redmoney),$lv['user_id'],"[<a href='".$deal_info['url']."' target='_blank'>".$deal_info['name']."</a>],的投标收益奖励  红包现金",28);
							}
							
						}elseif($lv['income_type']==2){
							//收益率
							$rate_date['user_id'] = $lv['user_id'];
							$rate_date['deal_id'] = $id;
							$rate_date['load_id'] = $lv['id'];
							$rate_date['reward_name'] = "投标收益奖励";
							$rate_date['gift_type'] = 2;
							$loadinfo = $GLOBALS['db']->getRow("SELECT * FROM ".DB_PREFIX."deal_load_repay WHERE load_id='".$lv['id']."'");
							$interest_money = $loadinfo['repay_money']-$loadinfo['self_money'];
							$gift_value = $interest_money * (float)$lv['income_value'] * 0.01;
							$rate_date['reward_money']=$gift_value;
							$rate_date['gift_value']=$lv['income_value'];
							$rate_date['status'] = 1;
							$rate_date['generation_date'] = to_date(TIME_UTC,"Y-m-d");
							$rate_date['release_date'] = to_date(TIME_UTC,"Y-m-d");
							$GLOBALS['db']->autoExecute(DB_PREFIX."gift_record",$rate_date); //插入
							
						}elseif($lv['income_type']==3){
							//积分
							$score=(int)$lv['income_value'];
							$score_date['user_id'] = $lv['user_id'];
							$score_date['deal_id'] = $id;
							$score_date['load_id'] = $lv['id'];
							$score_date['reward_name'] = "投标收益奖励";
							$score_date['gift_type'] = 3;
							$score_date['gift_value']=(int)$lv['income_value'];
							$score_date['status'] = 1;
							$score_date['generation_date'] = to_date(TIME_UTC,"Y-m-d");
							$score_date['release_date'] = to_date(TIME_UTC,"Y-m-d");
							$GLOBALS['db']->autoExecute(DB_PREFIX."gift_record",$score_date); //插入
							
							$is_send_mail = $user_msg_conf['mail_integral'];
							$is_send_sms = $user_msg_conf['sms_integral'];
							$TPL_MAIL_NAME = "TPL_MAIL_INTEGRAL";
							$TPL_SMS_NAME = "TPL_SMS_INTEGRAL";
							$gift_value = (int)$lv['income_value'];
							
							if($score!=0){
								modify_account(array("score"=>$score),$lv['user_id'],"[<a href='".$deal_info['url']."' target='_blank'>".$deal_info['name']."</a>],的投标收益奖励 积分",28);
							}
							
						}elseif($lv['income_type']==4){
							//礼品记录
							$gift_date['user_id'] = $lv['user_id'];
							$gift_date['deal_id'] = $id;
							$gift_date['load_id'] = $lv['id'];
							$gift_date['reward_name'] = "投标收益奖励";
							$gift_date['gift_type'] = 4;
							$gift_date['gift_value']=(int)$lv['income_value'];
							$gift_date['status'] = 1;
							$gift_date['generation_date'] = to_date(TIME_UTC,"Y-m-d");
							$gift_date['release_date'] = to_date(TIME_UTC,"Y-m-d");
							$GLOBALS['db']->autoExecute(DB_PREFIX."gift_record",$gift_date); //插入
							
							$is_send_mail = $user_msg_conf['mail_gift'];
							$is_send_sms = $user_msg_conf['sms_gift'];
							$TPL_MAIL_NAME = "TPL_MAIL_GIFT";
							$TPL_SMS_NAME = "TPL_SMS_GIFT";
							
							$gift_value = $GLOBALS['db']->getOne("select name from ".DB_PREFIX."vip_gift where id = ".$gift_date['gift_value']);
							
							
						}
						
						$user_info = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."user where id = ".$lv['user_id']);
						$deal_name = $GLOBALS['db']->getOne("select name from ".DB_PREFIX."deal where id = ".$id);
						
			
						//邮件
						if($is_send_mail == 1 && app_conf("MAIL_ON")==1)
						{
							$tmpl = $GLOBALS['db']->getRowCached("select * from ".DB_PREFIX."msg_template where name = '".$TPL_MAIL_NAME."'");
							$tmpl_content = $tmpl['content'];
							
							
							$notice['user_name'] = $user_info['user_name'];
							$notice['deal_name'] = $deal_name;
							$notice['release_date'] = to_date(TIME_UTC,"Y-m-d");
							$notice['site_name'] = app_conf("SHOP_TITLE");
							$notice['gift_value'] = $gift_value;
							
							$GLOBALS['tmpl']->assign("notice",$notice);
								
							$msg = $GLOBALS['tmpl']->fetch("str:".$tmpl_content);
							$msg_data['dest'] = $user_info['email'];
							$msg_data['send_type'] = 1;
							$msg_data['title'] = "投标奖励邮件通知";
							$msg_data['content'] = addslashes($msg);
							$msg_data['send_time'] = 0;
							$msg_data['is_send'] = 0;
							$msg_data['create_time'] = TIME_UTC;
							$msg_data['user_id'] = $user_info['id'];
							$msg_data['is_html'] = $tmpl['is_html'];
							$GLOBALS['db']->autoExecute(DB_PREFIX."deal_msg_list",$msg_data); //插入
						}
						
						//短信
						if($is_send_sms == 1 && app_conf("SMS_ON")==1)
						{
							$tmpl = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."msg_template where name = '".$TPL_SMS_NAME."'");				
							$tmpl_content = $tmpl['content'];
							
							$notice['user_name'] = $user_info['user_name'];
							$notice['deal_name'] = $deal_name;
							$notice['release_date'] = to_date(TIME_UTC,"Y-m-d");
							$notice['site_name'] = app_conf("SHOP_TITLE");
							$notice['gift_value'] = $gift_value;
							
							$GLOBALS['tmpl']->assign("notice",$notice);
								
							$msg = $GLOBALS['tmpl']->fetch("str:".$tmpl_content);
							
							$msg_data['dest'] = $user_info['mobile'];
							$msg_data['send_type'] = 0;
							$msg_data['title'] = "投标奖励短信通知";
							$msg_data['content'] = addslashes($msg);;
							$msg_data['send_time'] = 0;
							$msg_data['is_send'] = 0;
							$msg_data['create_time'] = TIME_UTC;
							$msg_data['user_id'] = $user_info['id'];
							$msg_data['is_html'] = $tmpl['is_html'];
							$GLOBALS['db']->autoExecute(DB_PREFIX."deal_msg_list",$msg_data); //插入				
						}
						
					}
				}
			}
			
		}
		
		//$GLOBALS['db']->autoExecute(DB_PREFIX."deal_load",array("is_has_loans"=>1),"UPDATE","deal_id=".$id);
		
		
		make_repay_plan($deal_info);
		
		//发借款成功邮件
		send_deal_success_mail_sms($id,$deal_info);
		//发借款成功站内信
		send_deal_success_site_sms($id,$deal_info);
		
		$return['status'] = 1;
		$return['info'] = "放款成功";
		return $return;
	}
	else{
		$return['info'] = "放款失败";
		return $return;
	}
 }

/**
 * 流标还返
 * $id deal_id
 * $type 0 普通返回，  1资金托管
 */

function do_received($id,$type = 0,$bad_msg = ""){
	
	$return = array("status"=>0,"info"=>"");
	if($id==0){
		$return['info'] = "返还失败，借款不存在";
		return $return;
	}
	require_once(APP_ROOT_PATH."app/Lib/deal.php");
	syn_deal_status($id);
	$deal_info = get_deal($id);
	
	if(!$deal_info){
		$return['info'] = "返还失败，借款不存在";
		return $return;
	}
	if(intval($deal_info['deal_status'])>=4){
		$return['info'] = "返还失败，借款状态为还款状态";
		return $return;
	}
	
	if($type==0 && $deal_info['ips_bill_no']!=""){
		$return['status'] = 2;
		$return['info'] = "";
		$return['jump'] = APP_ROOT."/index.php?ctl=collocation&act=RegisterSubject&pOperationType=2&status=2&status_msg=$bad_msg&deal_id=".$id; 
		return $return;
	}
	
	if($type == 0){
		//流标时返还
		require_once APP_ROOT_PATH."system/libs/user.php";
		$r_load_list = $GLOBALS['db']->getAll("SELECT id,user_id,money,is_old_loan FROM ".DB_PREFIX."deal_load WHERE is_repay=0 AND deal_id=$id");
		foreach($r_load_list as $k=>$v){
			if($v['is_old_loan']==0)
				modify_account(array("money"=>$v['money'],"lock_money"=>-$v['money']),$v['user_id'],"[<a href='".$deal_info['url']."' target='_blank'>".$deal_info['name']."</a>],流标返还",19);
			else
				modify_account(array("money"=>$v['money']),$v['user_id'],"[<a href='".$deal_info['url']."' target='_blank'>".$deal_info['name']."</a>],流标返还",19);
				
			$GLOBALS['db']->query("UPDATE ".DB_PREFIX."deal_load SET is_repay=1 WHERE id=".$v['id']);
		}
	}
	else{
		$GLOBALS['db']->query("UPDATE ".DB_PREFIX."deal_load SET is_repay=1 WHERE deal_id=$id");
	}
	
	if($GLOBALS['db']->getOne("SELECT count(*) FROM ".DB_PREFIX."deal_load WHERE is_repay=0 AND deal_id=$id") > 0){
		$bad_data['bad_msg'] = $bad_msg;
		$GLOBALS['db']->autoExecute(DB_PREFIX."deal",$bad_data,"UPDATE","id=".$id);
		$return['status'] = 1;
		$return['info'] = "部分返还";
		return $return;
	}
	else{
		$bad_data['is_has_received'] = 1;
		$bad_data['bad_msg'] = $bad_msg;
		$bad_data['bad_time'] = TIME_UTC;
		$bad_data['bad_date'] = to_date(TIME_UTC,"Y-m-d");
		$bad_data['deal_status'] = 3;
		$GLOBALS['db']->autoExecute(DB_PREFIX."deal",$bad_data,"UPDATE","id=".$id);
		$return['status'] = 1;
		$return['info'] = "返还成功";
		return $return;
	}
}

function get_site_email($user_id){
	$domian_attr =  explode(".",str_replace(array("https://","http://","/"),"",SITE_DOMAIN));
	$len = count($domian_attr);
	
	if($len==1)
		return $user_id."@".$domian_attr[$len-1].".com";
	else
		return $user_id."@".$domian_attr[$len-2].".".$domian_attr[$len-1];
	
}


function isMobile() {
	// 如果有HTTP_X_WAP_PROFILE则一定是移动设备
	if (isset ($_SERVER['HTTP_X_WAP_PROFILE'])){
		return true;
	}
	//如果via信息含有wap则一定是移动设备,部分服务商会屏蔽该信息
	if (isset ($_SERVER['HTTP_VIA'])) {
		//找不到为flase,否则为true
		return stristr($_SERVER['HTTP_VIA'], "wap") ? true : false;
	}
	//判断手机发送的客户端标志,兼容性有待提高
	if (isset ($_SERVER['HTTP_USER_AGENT'])) {
		$clientkeywords = array (
				'nokia',
				'sony',
				'ericsson',
				'mot',
				'samsung',
				'htc',
				'sgh',
				'lg',
				'sharp',
				'sie-',
				'philips',
				'panasonic',
				'alcatel',
				'lenovo',
				'iphone',
				'ipod',
				'blackberry',
				'meizu',
				'android',
				'netfront',
				'symbian',
				'ucweb',
				'windowsce',
				'palm',
				'operamini',
				'operamobi',
				'openwave',
				'nexusone',
				'cldc',
				'midp',
				'wap',
				'mobile'
		);
		// 从HTTP_USER_AGENT中查找手机浏览器的关键字
		if (preg_match("/(" . implode('|', $clientkeywords) . ")/i", strtolower($_SERVER['HTTP_USER_AGENT']))) {
			return true;
		}
	}
	//协议法，因为有可能不准确，放到最后判断
	if (isset ($_SERVER['HTTP_ACCEPT'])) {
		// 如果只支持wml并且不支持html那一定是移动设备
		// 如果支持wml和html但是wml在html之前则是移动设备
		if ((strpos($_SERVER['HTTP_ACCEPT'], 'vnd.wap.wml') !== false) && (strpos($_SERVER['HTTP_ACCEPT'], 'text/html') === false || (strpos($_SERVER['HTTP_ACCEPT'], 'vnd.wap.wml') < strpos($_SERVER['HTTP_ACCEPT'], 'text/html')))) {
			return true;
		}
	}
}


//给字符串添加引号
function str_quot($str,$separator=","){
	//echo $str."<br>";
	$str_arr = explode($separator,$str);
	//print_r($str_arr); exit;
	$str_new = "";
	foreach ($str_arr AS $key => $val){
		if ($str_new == ""){
			$str_new = "'".addslashes($val)."'";
		}else{
			$str_new .= ",'".addslashes($val)."'";
		}
	}

	return $str_new;
}

function date_in($start_date,$end_date,$add_quotation = true){
	$sysc_start_time = to_timespan(to_date(to_timespan($start_date),'Y-m-d'));
	$sysc_end_time = to_timespan(to_date(to_timespan($end_date),'Y-m-d'));
	if ($sysc_end_time == 0)
		$sysc_end_time = $sysc_start_time;

	if ($sysc_start_time == 0)
		$sysc_start_time = $sysc_end_time;

	$str_in = '';
	for($s_date = $sysc_start_time; $s_date <= $sysc_end_time; $s_date += 86400){
		if ($add_quotation){
			if ($str_in == ""){
				$str_in = "'".to_date($s_date,'Y-m-d')."'";
			}else{
				$str_in .= ",'".to_date($s_date,'Y-m-d')."'";
			}
		}else{
			if ($str_in == ""){
				$str_in = to_date($s_date,'Y-m-d');
			}else{
				$str_in .= ",".to_date($s_date,'Y-m-d');
			}
		}
	}
	return $str_in;
}

//日期加减
function dec_date($date,$dec){
	//$sysc_start_time = to_timespan(to_date(to_timespan($date),'Y-m-d')) - $dec * 86400;

	return to_date(to_timespan($date)  - $dec * 86400,'Y-m-d');
}

/**
 * 账单操作日志
 */
function repay_log($id,$msg,$user_id,$adm_id){
	$data['repay_id'] = $id;
	$data['log'] = $msg;
	$data['user_id'] = intval($user_id);
	$data['adm_id'] = intval($adm_id);
	$data['create_time'] = TIME_UTC;
	
	$GLOBALS['db']->autoExecute(DB_PREFIX."deal_repay_log",$data);
	if($GLOBALS['db']->insert_id() > 0){
		return true;
	}
	else{
		return false;
	}
}

function hideMobile($mobile){
	 if($mobile!="")
	 	return preg_replace('#(\d{3})\d{5}(\d{3})#', '${1}*****${2}',$mobile);
	 else
	 	return "";
}

function hideIdCard($idcard){
	 if($idcard!="")
	 	return preg_replace('#(\d{14})\d{4}|(\w+)#', '${1}****',$idcard);
	 else
	 	return "";
	 	
}
function hideEmail($email){
	if($email!="")
		return preg_replace('#(\w{2})\w+\@+#', '${1}****@${3}', $email);
	else
		return "";
}


function FW_DESPWD($pwd){
	$pwd = unescape(base64_decode($pwd));
	$pwd = substr($pwd,strlen(LOGIN_DES_KEY()."方维"),strlen($pwd));
	$pwd = substr($pwd,0,strlen($pwd) - strlen("软件"));
	return $pwd;
}

function  unescape($str){
   $str = rawurldecode ($str);
	preg_match_all ( "/%u.{4}|&#x.{4};|&#\d+;|.+/U", $str, $r );
	$ar = $r [0];
	foreach ( $ar as $k => $v ) {
		if (substr ( $v, 0, 2 ) == "%u")
			$ar [$k] = iconv ( "UCS-2", "UTF-8", pack ( "H4", substr ( $v, - 4 ) ) );
		elseif (substr ( $v, 0, 3 ) == "&#x")
			$ar [$k] = iconv ( "UCS-2", "UTF-8", pack ( "H4", substr ( $v, 3, - 1 ) ) );
		elseif (substr ( $v, 0, 2 ) == "&#") {
			$ar [$k] = iconv ( "UCS-2", "UTF-8", pack ( "n", substr ( $v, 2, - 1 ) ) );
		}
	}
	return join ( "", $ar );
} 

function LOGIN_DES_KEY(){
	if(!es_session::is_set("DES_KEY")){
		require_once APP_ROOT_PATH."system/utils/es_string.php";
		es_session::set("DES_KEY",es_string::rand_string(50));
	}
	return es_session::get("DES_KEY");
}

function HASH_KEY(){
	if(!es_session::is_set("HASH_KEY")){
		require_once APP_ROOT_PATH."system/utils/es_string.php";
		es_session::set("HASH_KEY",es_string::rand_string(50));
	}
	return es_session::get("HASH_KEY");
}

function check_hash_key(){
	if(strim($_REQUEST['fhash'])!="" && md5(HASH_KEY())==md5($_REQUEST['fhash'])){
		return true;
	}
	else
		return false;
}


///$type   invest:投资者,borrow:借款者
function get_rebate_fee($user_id,$type)
{
	$return['rebate'] = 0;
	$return['pid'] =0;
	if($user_id)
	{
		$return['pid'] = $GLOBALS['db']->getRow("SELECT p.id FROM ".DB_PREFIX."user u left join ".DB_PREFIX."user as p on p.id=u.pid WHERE u.id = ".$user_id." and p.user_type=3 ",false) ;
		if(intval($return['pid']) > 0)
		{
			if($type == 'invest')
			{
				$return['rebate'] = (float)app_conf('INVESTORS_COMMISSION_RATIO') ;
			}
			else if($type == 'borrow')
			{
				$return['rebate'] = (float)app_conf('BORROWER_COMMISSION_RATIO') ;
			}
		}
	}
	return $return;
}

///$type   invest:投资者,borrow:借款者
function reback_rebate_money($user_id,$money,$type,$memo){

	$pid = $GLOBALS['db']->getOne("select pid from ".DB_PREFIX."user where id = ".$user_id);
	if($pid == 0)
	{
		return;
	}
	require_once APP_ROOT_PATH."system/libs/user.php";
	modify_account(array("money"=>$money),$pid,$memo,23);
	
}

//费用信息
function get_user_load_fee($user_id,$deal_id=0,$deal_info = array()){
  	 if(!$deal_info && $deal_id > 0){
      	$deal_info = get_deal($deal_id);
     }
     if(intval($user_id)==0){
     	return false;
     }
     
	  $user_vipinfo = $GLOBALS['db']->getRow("select v.load_mfee,v.interest from ".DB_PREFIX."vip_setting v LEFT JOIN ".DB_PREFIX."user u ON u.vip_id=v.vip_id where u.id= ".$user_id." and u.vip_state='1' and v.is_effect='1' ",false);
	  
	  if($user_vipinfo['load_mfee']!="")
	  {
	  	$deal_info['manage_fee'] = $user_vipinfo['load_mfee'];
	  }
	 
	  if($user_vipinfo['interest']!="")
	  {
	  	$deal_info['user_loan_interest_manage_fee']  = $user_vipinfo['interest'];
	  }
	
	 
	 return $deal_info;
	   
}

/**
 * 投标数据格式化
 */
function return_deal_load_data($data,$user_info,$deal){
	$formatdata = array();
	if(isset($data['pMerBillNo']))
		$formatdata['pMerBillNo'] = $data['pMerBillNo'];
	if(isset($data['pContractNo']))
		$formatdata['pContractNo'] = $data["pContractNo"];
	if(isset($data['pP2PBillNo']))
		$formatdata['pP2PBillNo'] = $data['pP2PBillNo'];
		
	$formatdata['user_id'] = $user_info['id'];
	$formatdata['user_name'] = $user_info['user_name'];
	$formatdata['deal_id'] = $data['deal_id'];
	$formatdata['money'] = $data['money'];
	$formatdata['create_time'] = TIME_UTC;
	$formatdata['create_date'] = to_date(TIME_UTC);
	$formatdata['is_auto'] = intval($data['is_auto']);
	$formatdata['rebate_money'] = $formatdata['money'] * floatval(trim($deal['user_bid_rebate'])) * 0.01;
	
	$vip_state = $user_info['vip_state'];
	$income_value = "";
	if($vip_state==1){	
		$settinginfo= $GLOBALS['db']->getRow("SELECT * FROM ".DB_PREFIX."vip_setting WHERE vip_id='".$user_info['vip_id']."' ");
		
		$deal['user_bid_score_fee'] = $settinginfo['multiple'];
		
		$jilu=rand(1,100);
		$income_type = 0;
		
		if($jilu<=$settinginfo['probability']){
			
			//随机奖励类型
			$rand_gift = array();
			//奖励红包
			if($settinginfo['red_envelope'] !=""){
				$rand_gift[1] = $settinginfo['red_envelope'];
			}
			//奖励利率
			if($settinginfo['rate'] > 0){
				$rand_gift[2] = $settinginfo['rate'];
			}
			//奖励积分
			if($settinginfo['integral'] > 0){
				$rand_gift[3] = $settinginfo['integral'];
			}
			
			//奖励礼品
			if($settinginfo['gift'] !=""){
				$rand_gift[4] = $settinginfo['gift'];
			}
			
			if(count($rand_gift)){
				$income_type =  array_rand($rand_gift);
				$retrun_gift = $rand_gift[$income_type];
				switch(intval($income_type)){
					case 1:
							$ids =  explode(",",$rand_gift[1]);
							$k= array_rand($ids);
							$income_value = $ids[$k];
						break;
					case 2:
							$income_value=$rand_gift[2];
						break;
					case 3:
							$income_value=$rand_gift[3];
						break;
					case 4:
							$ids =  explode(",",$rand_gift[4]);
							$k= array_rand($ids);
							$income_value = $ids[$k];
						break;
					default : 
						$income_value="";
						break;
				}
			}
			else{
				$income_value="";
			}
			
		}
		$formatdata['is_winning'] = $income_value== "" ? 0 : 1;
		$formatdata['income_type'] = $income_type;
		$formatdata['income_value'] = $income_value;
	}
	
	$formatdata['bid_score'] = $formatdata['money'] * floatval(trim($deal['user_bid_score_fee'])) * 0.01;
	
	
	return $formatdata;
}


/**
 * 获得当前使用的，资金管理名称
 * @return string
 */
function getCollName(){
	
	return $GLOBALS['db']->getOne("select class_name from ".DB_PREFIX."collocation where is_effect = 1 limit 1");
	/*if(intval(app_conf("OPEN_IPS"))==1){
		return 'Ips';
	}else if(intval(app_conf("OPEN_IPS"))==2){
		return 'Yeepay';
	}else if(intval(app_conf("OPEN_IPS"))==3){
		return 'Baofoo';
	}else {
		return '';
	}*/
}

/**
 * VIP 升降级
 */
 function syn_user_vip($user_id,$type,$type_info){
 	$vipdata = array();
 	//$type_info 1借款升级、2投标升级、3正常还款升级、4提前还款升级、5.逾期还款降级、6.代还款降级、7.坏账降级
	switch($type_info){
		case "1":
			$vipdata['causes']="借款升级";
			break;
		case "2":
			$vipdata['causes']="投标升级";
			break;
		case "3":
			$vipdata['causes']="正常还款升级";
			break;
		case "4":
			$vipdata['causes']="提前还款升级";
			break;
		case "5":
			$vipdata['causes']="逾期还款降级";
			break;
		case "6":
			$vipdata['causes']="代还款降级";
			break;
		case "7":
			$vipdata['causes']="坏账降级";
			break;
		default : 
			$vipdata['causes']="";
			break;
	}
 	$vipdata['user_id'] = $user_id;
	$today=to_date(TIME_UTC,"Y-m-d");
	$start_time=to_date(next_replay_month(TIME_UTC,-24),"Y-m-d");
	$begin_date=to_date(next_replay_month(TIME_UTC,-3),"Y-m-d");
	
	$user_info= $GLOBALS['db']->getRow("select * FROM ".DB_PREFIX."user WHERE id='$user_id' ");
	$borrow_total = $GLOBALS['db']->getOne("select sum(borrow_amount) FROM ".DB_PREFIX."deal WHERE start_date>'$start_time' and user_id='$user_id' and deal_status > 3 ");
	$overdue_total = $GLOBALS['db']->getOne("select sum(repay_money) FROM ".DB_PREFIX."deal_repay WHERE ((has_repay=1 and repay_date<true_repay_date) or (has_repay=0 and repay_date <'$today')) and user_id='$user_id' ");
	$load_total = $GLOBALS['db']->getOne("select sum(dl.money) FROM ".DB_PREFIX."deal_load dl LEFT JOIN ".DB_PREFIX."deal d ON dl.deal_id=d.id WHERE dl.create_date>'$start_time' and dl.user_id='$user_id' and d.deal_status > 3  ");
	$t_u_load = $GLOBALS['db']->getRow("SELECT sum(dlr.repay_money) as load_money  FROM ".DB_PREFIX."deal_load_repay dlr LEFT JOIN ".DB_PREFIX."deal d ON d.id=dlr.deal_id LEFT JOIN ".DB_PREFIX."deal_load dl ON dl.id=dlr.load_id WHERE d.is_effect=1 and dl.is_repay= 0 and  dlr.t_user_id = ".$user_id);
	$load_total = floatval($load_total) + floatval($t_u_load['load_money']);
	
	$bl_total=$borrow_total+$load_total-$overdue_total;
	$gradeinfo = $GLOBALS['db']->getRow("select * FROM ".DB_PREFIX."vip_type WHERE lower_limit<='$bl_total' and upper_limit>='$bl_total' and is_effect='1' and is_delete='0' ");
 	if($type==0){
 		
 	}elseif($type==1){
 		//升级
 		$overdue_num = $GLOBALS['db']->getOne("select count(*) FROM ".DB_PREFIX."deal_repay WHERE ((has_repay=1 and repay_date<true_repay_date and repay_date>'$begin_date') or (has_repay=0 and repay_date <'$today'))  and user_id='$user_id' ");
		$demotion_num = $GLOBALS['db']->getOne("select count(*) FROM ".DB_PREFIX."demotion_record  WHERE status=0 and user_id='$user_id' and eresume_date>'$today' ");
		$rdemotion_num = $GLOBALS['db']->getOne("select count(*) FROM ".DB_PREFIX."demotion_record  WHERE status=0 and user_id='$user_id' and eresume_date<='$today' ");
 		//三个月内无逾期还款且 超过了预计降级还原期		
		if($overdue_num==0 && $demotion_num==0){
			$vipdata['original_vip_id'] = $user_info['vip_id'];
			$vipdata['now_vip_id'] = $gradeinfo['id'];
			$vipdata['upgrade_date'] = to_date(TIME_UTC,"Y-m-d");
			if(($gradeinfo['id']!=$user_info['vip_id'])&&($gradeinfo['id']!=0)){
				$GLOBALS['db']->autoExecute(DB_PREFIX."vip_upgrade_record",$vipdata,"INSERT");
			}
			$GLOBALS['db']->query("update ".DB_PREFIX."user SET vip_id='".$gradeinfo['id']."',vip_state='1' WHERE id=".$user_id);
			if($rdemotion_num>0){
				$GLOBALS['db']->query("update ".DB_PREFIX."demotion_record SET status='1',arecovery_date='$today' WHERE status=0 and user_id='$user_id' and eresume_date<='$today' ");
			}
		}
 	}elseif($type==2){
			//降级	
			$now_grade = $GLOBALS['db']->getOne("select id FROM ".DB_PREFIX."vip_type WHERE sort<'".$gradeinfo['sort']."' and is_effect='1' and is_delete='0' order by sort desc limit 1 ");
			if($gradeinfo['vip_grade']!="普通VIP会员"&&$user_info['vip_id']!=0){
				$GLOBALS['db']->query("update ".DB_PREFIX."user SET vip_id='".$now_grade."' WHERE id=".$user_id);
			}
			$vipdata['original_vip_id'] = $user_info['vip_id'];
			$vipdata['now_vip_id'] = $now_grade;
			$vipdata['start_date'] = to_date(TIME_UTC,"Y-m-d");
			$vipdata['eresume_date'] = to_date(next_replay_month(TIME_UTC,3),"Y-m-d");
			$vipdata['status'] = 0;
				
			if($demotion_num==0&&$gradeinfo['vip_grade']!="普通VIP会员"&&$user_info['vip_id']!=0){
				$GLOBALS['db']->autoExecute(DB_PREFIX."demotion_record",$vipdata,"INSERT");
			}
 	}
 	
 	return $vipdata;
 }
 
 
 function get_loantype_info($id){
 	if($id == 0){
 		return ;
 	}
 	$type_info = load_auto_cache("deal_loan_type_list",array("id"=>$id));
 	return $type_info[$id];
 }
 
?>