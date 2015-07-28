<?php
require APP_ROOT_PATH.'app/Lib/uc.php';
class uc_gift_recordModule extends SiteBaseModule {

    public function index()
	{
		
		$GLOBALS['tmpl']->assign("page_title","投资奖励");
		
		$start_time = strim($_REQUEST['start_time']);
		$end_time = strim($_REQUEST['end_time']);
			
		$d = explode('-',$start_time);
		if (isset($_REQUEST['start_time']) && $start_time !="" && checkdate($d[1], $d[2], $d[0]) == false){
			$this->error("开始时间不是有效的时间格式:{$start_time}(yyyy-mm-dd)");
			exit;
		}
		
		$d = explode('-',$end_time);
		if ( isset($_REQUEST['end_time']) && strim($end_time) !="" &&  checkdate($d[1], $d[2], $d[0]) == false){
			$this->error("结束时间不是有效的时间格式:{$end_time}(yyyy-mm-dd)");
			exit;
		}
		
		if ($start_time!="" && strim($end_time) !="" && to_timespan($start_time) > to_timespan($end_time)){
			$this->error('开始时间不能大于结束时间:'.$start_time.'至'.$end_time);
			exit;
		}

		if(strim($start_time)!="")
		{
			$where .= " and gr.release_date >='".strim($start_time)."'";
		}
		if(strim($end_time) !="")
		{
			$where .= " and gr.release_date <='".strim($end_time)."'";
		}
		
		if(isset($_REQUEST['gift_type'])){
			$gift_type=$_REQUEST['gift_type'];
			if($gift_type!=5){
				$where .= " and gr.gift_type ='".$gift_type."'";
				$GLOBALS['tmpl']->assign("gift_type",$gift_type);
			}
			
		}
		
		$page = intval($_REQUEST['p']);
		if($page==0)
		$page = 1;
		$limit = (($page-1)*app_conf("PAGE_SIZE")).",".app_conf("PAGE_SIZE");

		if($limit)
		{
			$limit = " limit ".$limit;
		}
		
		$list_count = $GLOBALS['db']->getOne("select count(*) from ".DB_PREFIX."gift_record gr left join ".DB_PREFIX."deal d on d.id = gr.deal_id  WHERE gr.user_id='".$GLOBALS['user_info']['id']."' $where ");
		$list = array();
		if($list_count > 0)
		{
			$sql = "select gr.*,d.name,vg.name as gift_name,sum(dlr.true_reward_money) as reward_money  from ".DB_PREFIX."gift_record gr " .
					"left join ".DB_PREFIX."deal d on d.id = gr.deal_id  " .
					"left join ".DB_PREFIX."vip_gift vg on vg.id = gr.gift_value AND gr.gift_type='4' " .
					"left join ".DB_PREFIX."deal_load_repay dlr on dlr.load_id = gr.load_id  " .
					"WHERE gr.user_id='".$GLOBALS['user_info']['id']."' AND ((gr.gift_type = 2 AND dlr.true_reward_money >0 ) OR (gr.gift_type <> 2)) $where GROUP BY gr.load_id order by gr.id desc ".$limit;
					
			$list = $GLOBALS['db']->getAll($sql);	
			foreach($list as $k=>$v){
				$list[$k]['url'] = url("index","deal#index",array("id"=>$v['deal_id']));
			}
		}
		
		
		$page = new Page($list_count,app_conf("PAGE_SIZE"));   //初始化分页对象 		
		$p  =  $page->show();
		$GLOBALS['tmpl']->assign('pages',$p);
		
		$GLOBALS['tmpl']->assign("list",$list);
		$GLOBALS['tmpl']->assign("start_time",$start_time);
		$GLOBALS['tmpl']->assign("end_time",$end_time);
		
		
		
		$GLOBALS['tmpl']->assign("inc_file","inc/uc/uc_gift_record.html");
		$GLOBALS['tmpl']->display("page/uc.html");
	}
}
?>