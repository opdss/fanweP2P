<?php

require APP_ROOT_PATH.'app/Lib/uc.php';
class uc_vip_settingModule extends SiteBaseModule {

   public function index(){
		$GLOBALS['tmpl']->assign("page_title","VIP等级特权");
		
		$list = load_auto_cache("level");
		
		$GLOBALS['tmpl']->assign("list",$list['list']);
		
		$userinfo=$GLOBALS['db']->getRow("SELECT * FROM ".DB_PREFIX."user WHERE id='".$GLOBALS['user_info']['id']."' and vip_state='1' ");
	
		$GLOBALS['tmpl']->assign('userinfo',$userinfo);
		
		$gradeinfo=$GLOBALS['db']->getRow("SELECT * FROM ".DB_PREFIX."vip_type WHERE id='".$userinfo['vip_id']."' and is_delete='0' ");
		$GLOBALS['tmpl']->assign('gradeinfo',$gradeinfo);
		
		$vip_type=$GLOBALS['db']->getAll("SELECT * FROM ".DB_PREFIX."vip_type WHERE is_effect='1' and is_delete='0' ");
		$GLOBALS['tmpl']->assign('vip_type',$vip_type);
		
		$vip_setting=$GLOBALS['db']->getAll("SELECT * FROM ".DB_PREFIX."vip_setting v LEFT JOIN ".DB_PREFIX."vip_type vt ON v.vip_id=vt.id WHERE v.is_effect='1' and v.is_delete='0' ");
		$GLOBALS['tmpl']->assign('vip_setting',$vip_setting);
		
		
		$customerinfo=$GLOBALS['db']->getRow("SELECT * FROM ".DB_PREFIX."customer WHERE id='".$userinfo['customer_id']."' and is_effect='1'  ");
		$GLOBALS['tmpl']->assign('customerinfo',$customerinfo);
		$today=to_date(TIME_UTC,"Y-m-d");

		$start_time=to_date(next_replay_month(TIME_UTC,-24),"Y-m-d");
		$begin_date=to_date(next_replay_month(TIME_UTC,-3),"Y-m-d");
		
		$user_id=$GLOBALS['user_info']['id'];
		$borrow_total = $GLOBALS['db']->getOne("select sum(borrow_amount) FROM ".DB_PREFIX."deal WHERE start_date>'$start_time' and user_id='$user_id' and deal_status > 3 ");
		$load_total = $GLOBALS['db']->getOne("select sum(dl.money) FROM ".DB_PREFIX."deal_load dl LEFT JOIN ".DB_PREFIX."deal d ON dl.deal_id=d.id WHERE dl.create_date>'$start_time' and dl.user_id='$user_id' and d.deal_status > 3  ");
		$t_u_load = $GLOBALS['db']->getRow("SELECT sum(dlr.repay_money) as load_money  FROM ".DB_PREFIX."deal_load_repay dlr LEFT JOIN ".DB_PREFIX."deal d ON d.id=dlr.deal_id LEFT JOIN ".DB_PREFIX."deal_load dl ON dl.id=dlr.load_id WHERE d.is_effect=1 and dl.is_repay= 0 and  dlr.t_user_id = ".$user_id);
		$load_total = floatval($load_total) + floatval($t_u_load['load_money']);
		
	//	$load_total = $GLOBALS['db']->getOne("select sum(money) FROM ".DB_PREFIX."deal_load WHERE create_date>'$start_time' and user_id='$user_id' ");
		$overdue_total = $GLOBALS['db']->getOne("select sum(repay_money) FROM ".DB_PREFIX."deal_repay WHERE ((has_repay=1 and repay_date<true_repay_date) or (has_repay=0 and repay_date <'$today')) and user_id='$user_id' ");
		
		$bl_total=(int)$borrow_total+(int)$load_total;
		$yx_total= $bl_total-$overdue_total;
		
		$vipgradeinfo = $GLOBALS['db']->getRow("select * FROM ".DB_PREFIX."vip_type WHERE lower_limit<='$yx_total' and upper_limit>='$yx_total' and is_effect='1' and is_delete='0' ");
		$grade_cz=$GLOBALS['db']->getRow("SELECT * FROM ".DB_PREFIX."vip_type where is_effect='1' and is_delete='0'  order by lower_limit asc limit 1   ");

		if(($grade_cz['lower_limit']>$yx_total)||$userinfo['vip_id']==0){
			$chazhi=(int)$grade_cz['lower_limit']-$yx_total;
			if($chazhi>0){
				$chazhi=$chazhi;
			}else{
				$chazhi = 0;
			}
			
			$nextgrade=$grade_cz['vip_grade'];
		}else{
			$chazhi=$vipgradeinfo['upper_limit']-$yx_total+1;
			$nextgradeinfo = $GLOBALS['db']->getRow("select * FROM ".DB_PREFIX."vip_type WHERE  lower_limit >'".$vipgradeinfo['upper_limit']."' and is_effect='1' and is_delete='0' order by lower_limit asc limit 1 ");
			$nextgrade=$nextgradeinfo['vip_grade'];
		}
		
		$chazhi = number_format($chazhi);
		$bl_total = number_format($bl_total);
		$vipgradeinfo = number_format($vipgradeinfo);
		$yx_total = number_format($yx_total);
		$overdue_total = number_format($overdue_total);
		
		$GLOBALS['tmpl']->assign('bl_total',$bl_total);
		$GLOBALS['tmpl']->assign('chazhi',$chazhi);
		$GLOBALS['tmpl']->assign('nextgrade',$nextgrade);
		$GLOBALS['tmpl']->assign('overdue_total',$overdue_total);
		$GLOBALS['tmpl']->assign('yx_total',$yx_total);
		
		$GLOBALS['tmpl']->assign('vipgradeinfo',$vipgradeinfo);
		
		$GLOBALS['tmpl']->assign("inc_file","inc/uc/uc_vip_setting_index.html");
		$GLOBALS['tmpl']->display("page/uc.html");
	}
	
}
?>