<?php

require APP_ROOT_PATH.'app/Lib/uc.php';
class uc_given_recordModule extends SiteBaseModule {

   public function index()
	{
		$GLOBALS['tmpl']->assign("page_title","节日福利");
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
			$where .= " and send_date >='".strim($start_time)."'";
		}
		if(strim($end_time) !="")
		{
			$where .= " and send_date <='".strim($end_time)."'";
		}
		
		$page = intval($_REQUEST['p']);
		if($page==0)
		$page = 1;
		$limit = (($page-1)*app_conf("PAGE_SIZE")).",".app_conf("PAGE_SIZE");

		if($limit)
		{
			$limit = " limit ".$limit;
		}

		$list = $GLOBALS['db']->getAll("select * from ".DB_PREFIX."given_record gr left join ".DB_PREFIX."user u on u.id = gr.user_id left join ".DB_PREFIX."vip_festivals vf on vf.id = gr.gift_id  WHERE u.id='".$GLOBALS['user_info']['id']."' $where order by gr.id desc ".$limit);

		$list_count = $GLOBALS['db']->getOne("select count(*) from ".DB_PREFIX."given_record gr left join ".DB_PREFIX."user u on u.id = gr.user_id left join ".DB_PREFIX."vip_festivals vf on vf.id = gr.gift_id WHERE u.id='".$GLOBALS['user_info']['id']."' $where  ");
		
		$page = new Page($list_count,app_conf("PAGE_SIZE"));   //初始化分页对象 		
		$p  =  $page->show();
		$GLOBALS['tmpl']->assign('pages',$p);
		
		$GLOBALS['tmpl']->assign("list",$list);
		$GLOBALS['tmpl']->assign("start_time",$start_time);
		$GLOBALS['tmpl']->assign("end_time",$end_time);
		
		$GLOBALS['tmpl']->assign("inc_file","inc/uc/uc_given_record.html");
		$GLOBALS['tmpl']->display("page/uc.html");
	}
    
}
?>