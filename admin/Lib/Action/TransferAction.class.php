<?php
// +----------------------------------------------------------------------
// | Fanwe 方维p2p借贷系统
// +----------------------------------------------------------------------
// | Copyright (c) 2011 http://www.fanwe.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 云淡风轻(88522820@qq.com)
// +----------------------------------------------------------------------

class TransferAction extends CommonAction{
	public function __construct(){
		require APP_ROOT_PATH."app/Lib/common.php";
		require APP_ROOT_PATH."app/Lib/deal.php";
		parent::__construct();
		parent::assign('main_title',L(MODULE_NAME."_".ACTION_NAME));
	}
	function index(){
		
		//创建分页对象
		$listRows = C('PAGE_LISTROWS');
		
		$p = !empty($_GET[C('VAR_PAGE')])?$_GET[C('VAR_PAGE')]:1;
		
		$limit = (($p - 1) * $listRows) . "," .$listRows; 
		
		$transfer_list =  get_transfer_list($limit," and d.deal_status >= 4 ",'',''," d.create_time DESC , dlt.id DESC ");
			
		$this->assign('list',$transfer_list['list']);
		
		$this->assign('csv_set',"index");
		$p = new Page ( $transfer_list['rs_count'], $listRows );
		
		$page = $p->show ();
		
		$this->assign('page',$page);
		
		$this->display();
	}
	
	function ing(){
		
		//创建分页对象
		$listRows = C('PAGE_LISTROWS');
		
		$p = !empty($_GET[C('VAR_PAGE')])?$_GET[C('VAR_PAGE')]:1;
		
		$limit = (($p - 1) * $listRows) . "," .$listRows; 
		
		$transfer_list =  get_transfer_list($limit," and d.deal_status >= 4 and dlt.status=1 and dlt.t_user_id=0 ",'',''," d.create_time DESC , dlt.id DESC ");
			
		$this->assign('list',$transfer_list['list']);
		
		$this->assign('csv_set',"ing");
		
		$p = new Page ( $transfer_list['rs_count'], $listRows );
		
		$page = $p->show ();
		
		$this->assign('page',$page);
		
		$this->display("index");
	}
	
	function success(){
		
		//创建分页对象
		$listRows = C('PAGE_LISTROWS');
		
		$p = !empty($_GET[C('VAR_PAGE')])?$_GET[C('VAR_PAGE')]:1;
		
		$limit = (($p - 1) * $listRows) . "," .$listRows; 
		
		$transfer_list =  get_transfer_list($limit," and d.deal_status >= 4 and dlt.status=1 and dlt.t_user_id>0 ",'',''," d.create_time DESC , dlt.id DESC ");
			
		$this->assign('list',$transfer_list['list']);
		
		$this->assign('csv_set',"success");
		$p = new Page ( $transfer_list['rs_count'], $listRows );
		
		$page = $p->show ();
		
		$this->assign('page',$page);
		
		$this->display("index");
	}
	
	function back(){
		
		//创建分页对象
		$listRows = C('PAGE_LISTROWS');
		
		$p = !empty($_GET[C('VAR_PAGE')])?$_GET[C('VAR_PAGE')]:1;
		
		$limit = (($p - 1) * $listRows) . "," .$listRows; 
		
		$transfer_list =  get_transfer_list($limit," and d.deal_status >= 4 and dlt.status=0 ",'',''," d.create_time DESC , dlt.id DESC ");
			
		$this->assign('list',$transfer_list['list']);
		$this->assign('csv_set',"back");
		
		$p = new Page ( $transfer_list['rs_count'], $listRows );
		
		$page = $p->show ();
		
		$this->assign('page',$page);
		
		$this->display("index");
	}
	
	function reback(){
		$id = intval($_REQUEST['id']);
		if($id==0){
			$this->error("操作失败");
			die();
		}
		
		$id = intval($_REQUEST['id']);
		
		$deal_id = $GLOBALS['db']->getOne("SELECT deal_id FROM ".DB_PREFIX."deal_load_transfer WHERE id=".$id);
		if($deal_id==0){
			
			$this->error("不存在的债权");
			die();
		}
		
		$condition = ' AND dlt.id='.$id.' AND d.deal_status >= 4 and d.is_effect=1 and d.is_delete=0 and d.repay_time_type =1 and  d.publish_wait=0 ';
		$union_sql = " LEFT JOIN ".DB_PREFIX."deal_load_transfer dlt ON dlt.deal_id = dl.deal_id ";
		
		$transfer = get_transfer($union_sql,$condition);
		if($transfer['t_user_id'] > 0){
			$this->error("债权已转让，无法撤销",0);
			die();
		}
		
		$this->assign('transfer',$transfer);
		
		parent::success($this->fetch());
	}
	
	function do_reback(){
		$id = intval($_REQUEST['id']);
		if($id==0){
			$this->error("操作失败",0);
			die();
		}
		
		$id = intval($_REQUEST['id']);
		
		$deal_id = $GLOBALS['db']->getOne("SELECT deal_id FROM ".DB_PREFIX."deal_load_transfer WHERE id=".$id);
		if($deal_id==0){
			
			$this->error("不存在的债权");
			die();
		}
		
		$condition = ' AND dlt.id='.$id.' AND d.deal_status >= 4 and d.is_effect=1 and d.is_delete=0 and d.repay_time_type =1 and  d.publish_wait=0 ';
		$union_sql = " LEFT JOIN ".DB_PREFIX."deal_load_transfer dlt ON dlt.deal_id = dl.deal_id ";
		
		$transfer = get_transfer($union_sql,$condition);
		
		if($transfer['t_user_id'] > 0){
			$this->error("债权已转让，无法撤销",0);
			die();
		}
		
		$msg = strim($_POST['msg']);
		
		if($msg == ""){
			$this->error("请输入撤销原因",0);
			die();
		}
		
		$GLOBALS['db']->query("UPDATE  ".DB_PREFIX."deal_load_transfer SET status=0 WHERE id=".$id);
		if($GLOBALS['db']->affected_rows() > 0){
			
			$notice['shop_title'] = app_conf("SHOP_TITLE");
			$notice['url'] = "“<a href=\"".url("index","transfer#detail",array("id"=>$v['id']))."\">Z-".$v['load_id']."</a>”";
			$notice['msg'] = "因为：“".$msg."”被管理员撤销了";
			$tmpl_content = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."msg_template where name = 'TPL_DEAL_SUCCESS_SITE_SMS'",false);
			$GLOBALS['tmpl']->assign("notice",$notice);
			$content = $GLOBALS['tmpl']->fetch("str:".$tmpl_content['content']);
			
			send_user_msg("",$content,0,$transfer['user_id'],TIME_UTC,0,true,17);
			save_log("撤销编号：$id的债券转让",1);
			parent::success("撤销成功！");
			die();
		}
		else{
			save_log("撤销编号：$id的债券转让",0);
			$this->error("撤销失败！");
			die();
		}
	}
	
	//所以转让
	public function export_csv($page = 1)
	{
		set_time_limit(0);
		$limit = (($page - 1)*intval(app_conf("BATCH_PAGE_SIZE"))).",".(intval(app_conf("BATCH_PAGE_SIZE")));
		
		$csv_set = strim($_REQUEST['csv_set']);
	
		if($csv_set == "index"){
			$transfer_list =  get_transfer_list($limit," and d.deal_status >= 4 ",'',''," d.create_time DESC , dlt.id DESC ");
		
		}elseif ( $csv_set == "ing"){
			$transfer_list =  get_transfer_list($limit," and d.deal_status >= 4 and dlt.status=1 and dlt.t_user_id=0 ",'',''," d.create_time DESC , dlt.id DESC ");
		}elseif ( $csv_set == "success"){
			$transfer_list =  get_transfer_list($limit," and d.deal_status >= 4 and dlt.status=1 and dlt.t_user_id>0 ",'',''," d.create_time DESC , dlt.id DESC ");
		}elseif ( $csv_set == "back"){
			$transfer_list =  get_transfer_list($limit," and d.deal_status >= 4 and dlt.status=0 ",'',''," d.create_time DESC , dlt.id DESC ");
		}
		
		
		$list = $transfer_list['list'];
		foreach($list as $k=>$v)
		{
			$list[$k]['name'] =M("Deal")->where(" id=".$list[$k]['deal_id'])->getField("name");
			$list[$k]['user_name'] =M("User")->where(" id=".$list[$k]['user_id'])->getField("user_name");
			$list[$k]['t_user_name'] =M("User")->where(" id=".$list[$k]['t_user_id'])->getField("user_name");
			$list[$k]['transfer_time'] = to_date($list[$k]['transfer_time'],"Y-m-d");
			$list[$k]['create_time'] = to_date($list[$k]['create_time'],"Y-m-d");

			if($list[$k]['t_user_id'] > 0 ){
				$list[$k]['status'] = "已转让";
			}elseif ($list[$k]['status']==0){
				$list[$k]['status'] = "已撤销";
			}else{
				$list[$k]['status'] = "转让中";
			}
		}
	
		if($list)
		{
			register_shutdown_function(array(&$this, 'export_csv_score'), $page+1);
			$transfer_list = array('id'=>'""','name'=>'""','user_name'=>'""','t_user_name'=>'""','transfer_time'=>'""','transfer_amount'=>'""','transfer_number'=>'""','create_time'=>'""','status'=>'""');
			if($page == 1)
				$content = iconv("utf-8","gbk","编号,原始标,转让者,承接者,承接时间,转让价格,转让期数,发布时间,状态");

			if($page==1) $content = $content . "\n";
	
			foreach($list as $k=>$v)
			{
				$transfer_list = array();
				$transfer_list['id'] = iconv('utf-8','gbk','"' . $list[$k]['id'] . '"');
				$transfer_list['name'] = iconv('utf-8','gbk','"' .  $list[$k]['name'] . '"');
				$transfer_list['user_name'] = iconv('utf-8','gbk','"' . $list[$k]['user_name']. '"');
				$transfer_list['t_user_name'] = iconv('utf-8','gbk','"' . $list[$k]['t_user_name'] . '"');
				$transfer_list['transfer_time'] = iconv('utf-8','gbk','"' . $v['transfer_time'] . '"');
				$transfer_list['transfer_amount'] = iconv('utf-8','gbk','"' . $v['transfer_amount'] . '"');
				$transfer_list['transfer_number'] = iconv('utf-8','gbk','"' . $v['transfer_number'] . '"');
				$transfer_list['create_time'] = iconv('utf-8','gbk','"' . $list[$k]['create_time'] . '"');
				$transfer_list['status'] = iconv('utf-8','gbk','"' . $list[$k]['status'] . '"');
	
				$content .= implode(",", $transfer_list) . "\n";
			}
			 
			header("Content-Disposition: attachment; filename=transfer_list.csv");
			echo $content;
		}
		else
		{
			if($page==1)
				$this->error(L("NO_RESULT"));
		}
	
	}

	
	
}

?>