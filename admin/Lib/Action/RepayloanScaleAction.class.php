<?php
// +----------------------------------------------------------------------
// | easethink 易想商城系统
// +----------------------------------------------------------------------
// | Copyright (c) 2011 http://www.fanwe.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 云淡风轻(88522820@qq.com)
// +----------------------------------------------------------------------

class RepayloanScaleAction extends CommonAction{
	public function index()
	{
		
		//开始加载搜索条件
		$map['is_delete'] = 0;
		$map['publish_wait'] = 0;
		
		if(trim($_REQUEST['name'])!='')
		{
			$map['name'] = array('like','%'.trim($_REQUEST['name']).'%');		
		}

		$deal_status = isset($_REQUEST['deal_status']) ? trim($_REQUEST['deal_status']) : 'all';

		if(isset($_REQUEST['is_has_received']) && trim($_REQUEST['is_has_received']) != 'all'){
			$map['is_has_received'] = array("eq",intval($_REQUEST['is_has_received']));
			$map['buy_count'] = array("gt",0);
		}
		
		
		$this->getDeallist($map,intval($_REQUEST['cate_id']),$_REQUEST['user_name'],$deal_status);
		
		$this->display ();
		return;
	}
	
	function ing(){
		$map['deal_status'] = array("eq",1);
		$map['is_delete'] = 0;
		if(trim($_REQUEST['name'])!='')
		{
			$map['name'] = array('like','%'.trim($_REQUEST['name']).'%');		
		}

		$map['publish_wait'] = 0;
		$this->getDeallist($map);
		$this->display ("index");
		return;
	}
	function wait(){
		$map['deal_status'] = array("eq",0);
		$map['is_delete'] = 0;
		if(trim($_REQUEST['name'])!='')
		{
			$map['name'] = array('like','%'.trim($_REQUEST['name']).'%');		
		}

		$map['publish_wait'] = 0;
		$this->getDeallist($map,intval($_REQUEST['cate_id']),$_REQUEST['user_name']);
		$this->display ("index");
		return;
	}
	function full(){
		$map['deal_status'] = array("eq",2);
		$map['is_delete'] = 0;
		if(trim($_REQUEST['name'])!='')
		{
			$map['name'] = array('like','%'.trim($_REQUEST['name']).'%');		
		}

		$map['publish_wait'] = 0;
		$this->getDeallist($map,intval($_REQUEST['cate_id']),$_REQUEST['user_name']);
		$this->display ("index");
		return;
	}
	
	function inrepay(){
		$map['deal_status'] = array("eq",4);
		$map['is_delete'] = 0;
		if(trim($_REQUEST['name'])!='')
		{
			$map['name'] = array('like','%'.trim($_REQUEST['name']).'%');		
		}

		$map['publish_wait'] = 0;
		$this->getDeallist($map,intval($_REQUEST['cate_id']),$_REQUEST['user_name']);
		$this->display ("index");
		return;
	}
	
	function flow(){
		$map['deal_status'] = array("eq",3);
		$map['is_delete'] = 0;
		if(trim($_REQUEST['name'])!='')
		{
			$map['name'] = array('like','%'.trim($_REQUEST['name']).'%');		
		}

		$map['publish_wait'] = 0;
		$this->getDeallist($map,intval($_REQUEST['cate_id']),$_REQUEST['user_name']);
		$this->display ("index");
		return;
	}
	
	function over(){
		$map['deal_status'] = array("eq",5);
		$map['is_delete'] = 0;
		if(trim($_REQUEST['name'])!='')
		{
			$map['name'] = array('like','%'.trim($_REQUEST['name']).'%');		
		}

		$map['publish_wait'] = 0;
		$this->getDeallist($map,intval($_REQUEST['cate_id']),$_REQUEST['user_name']);
		$this->display ("index");
		return;
	}
	
	public function publish()
	{
		$map['publish_wait'] = 1;
		$map['is_delete'] = 0;
		if (method_exists ( $this, '_filter' )) {
			$this->_filter ( $map );
		}
		$name=$this->getActionName();
		$model = D ($name);
		if (! empty ( $model )) {
			$this->_list ( $model, $map );
		}
		$this->display ();
		return;
	}
	
	private function getDeallist($map,$cate_id=0,$user_name="",$deal_status="all"){
		//分类
		$cate_tree = M("DealCate")->where('is_delete = 0')->findAll();
		$cate_tree = D("DealCate")->toFormatTree($cate_tree,'name');
		$this->assign("cate_tree",$cate_tree);
		
		if($cate_id>0)
		{
			require_once APP_ROOT_PATH."system/utils/child.php";
			$child = new Child("deal_cate");
			$cate_ids = $child->getChildIds($cate_id);
			$cate_ids[] = $cate_id;
			$map['cate_id'] = array("in",$cate_ids);
		}
		
		if(trim($user_name)!='')
		{
			$sql  ="select group_concat(id) from ".DB_PREFIX."user where user_name like '%".trim($user_name)."%'";
			
			$ids = $GLOBALS['db']->getOne($sql);
			$map['user_id'] = array("in",$ids);
		}
		
		if($deal_status != 'all'){
			if(intval($deal_status)==3){
				$sw['deal_status'] = array("eq",3);
				
				$sws['deal_status'] = array("eq",1);
				$sws['start_time'] = array("exp"," < ".TIME_UTC." - enddate *24 *3600 ");
				$sws['_logic'] = 'and';
				
				$sw['_complex'] = $sws;
				
				$sw['_logic'] = 'or';
				
				$map['_complex'] = $sw;
			}
			else
				$map['deal_status'] = array("eq",intval($deal_status));
		}
		
		if (method_exists ( $this, '_filter' )) {
			$this->_filter ( $map );
		}
		$name=$this->getActionName();
		$model = D ($name);
		if (! empty ( $model )) {
			$this->_list ( $model, $map );
		}
		
	}
	
	public function three()
	{
		$this->assign("main_title",L("DEAL_THREE"));
		
		
		if (isset ( $_REQUEST ['_order'] )) {
			$sorder = $_REQUEST ['_order'];
		}
		else{
			$sorder = "id";
		}
		
		switch($sorder){
			case "name":
			case "cate_id":
					$order ="d.".$sorder;
				break;
			case "has_repay_status":
					$order ="dl.status";
				break;
			case "site_bad_status":
					$order ="dl.is_site_bad";
				break;
			case "is_has_send":
					$order ="d.send_three_msg_time";
				break;
			case "l_key_index":
					$order ="dl.l_key";
				break;
			default : 
				$order ="dl.".$sorder;
				break;
		}
		
	
		
		//排序方式默认按照倒序排列
		//接受 sost参数 0 表示倒序 非0都 表示正序
		if (isset ( $_REQUEST ['_sort'] )) {
			$sort = $_REQUEST ['_sort'] ? 'asc' : 'desc';
		}
		else{
			$sort = "ASC";
		}
		
		
		//分类
		$cate_tree = M("DealCate")->where('is_delete = 0')->findAll();
		$cate_tree = D("DealCate")->toFormatTree($cate_tree,'name');
		$this->assign("cate_tree",$cate_tree);
		
		//开始加载搜索条件
		$condition =" 1=1 ";
		
		$status = intval($_REQUEST['status']);
		if($status >0){
			if(($status-1)==0)
				$condition .= " AND dl.has_repay=0 ";
			else
				$condition .= " AND dl.has_repay=1 and dl.status=".($status-2);
		}
		
		$deal_status = intval($_REQUEST['deal_status']);
		if($deal_status >0){
			$condition .= " AND dl.is_site_bad=".($deal_status-1);
		}
		
		$begin_time  = !isset($_REQUEST['begin_time'])? to_timespan(to_date(TIME_UTC ,"Y-m-d"),"Y-m-d")  : (trim($_REQUEST['begin_time']) =="" ? 0 : to_timespan($_REQUEST['begin_time'],"Y-m-d"));
		$end_time  = !isset($_REQUEST['end_time'])?to_timespan(to_date(TIME_UTC ,"Y-m-d"),"Y-m-d") + 3*24*3600: (trim($_REQUEST['end_time']) =="" ? 0 : to_timespan($_REQUEST['end_time'],"Y-m-d"));
		if($begin_time > 0 || $end_time > 0){
			if($end_time==0)
			{
				$condition .= " and dl.repay_time >= $begin_time ";	
			}
			else
				$condition .= " and dl.repay_time between  $begin_time and $end_time ";	
		}
		
		$_REQUEST['begin_time'] = to_date($begin_time ,"Y-m-d");
		$_REQUEST['end_time'] = to_date($end_time ,"Y-m-d");
		
		if(trim($_REQUEST['name'])!='')
		{
			$condition .= " and d.name like '%".trim($_REQUEST['name'])."%'";
		}
		
		if(trim($_REQUEST['user_name'])!='')
		{
			$condition .= " and dl.user_id in (select id from  ".DB_PREFIX."user WHERE user_name='".trim($_REQUEST['user_name'])."')";			
		}

		if(intval($_REQUEST['cate_id'])>0)
		{
			require_once APP_ROOT_PATH."system/utils/child.php";
			$child = new Child("deal_cate");
			$cate_ids = $child->getChildIds(intval($_REQUEST['cate_id']));
			$cate_ids[] = intval($_REQUEST['cate_id']);
			$condition .=" and d.cate_id in (".implode(",",$cate_ids).") ";
		}
		
		$sql_count = " SELECT count(*) FROM ".DB_PREFIX."deal_repay dl LEFT JOIN ".DB_PREFIX."deal d ON d.id=dl.deal_id WHERE $condition ";
	
		$rs_count = $GLOBALS['db']->getOne($sql_count);
		$list = array();
		
		if($rs_count > 0){
			
			if (! empty ( $_REQUEST ['listRows'] )) {
				$listRows = $_REQUEST ['listRows'];
			} else {
				$listRows = '';
			}
			$p = new Page ( $rs_count, $listRows );
			
			$sql_list =  " SELECT dl.*,dl.l_key + 1 as l_key_index,d.name,d.cate_id,d.send_three_msg_time,u.user_name,u.mobile FROM ".DB_PREFIX."deal_repay dl LEFT JOIN ".DB_PREFIX."deal d  ON d.id=dl.deal_id left join ".DB_PREFIX."user u on u.id=dl.user_id WHERE $condition ORDER BY $order $sort LIMIT ".$p->firstRow . ',' . $p->listRows;
			
			$list = $GLOBALS['db']->getAll($sql_list);
			
			foreach($list as $k=>$v){
				$list[$k]['l_key_index'] = "第 ".$v['l_key_index']." 期";
				if($v['send_three_msg_time'] == $v['repay_time']){
					$list[$k]['is_has_send'] = 1;
				}
				else{
					$list[$k]['is_has_send'] = 0;
				}
				if($v['has_repay']==1){
					switch($v['status']){
						case 0;
							$list[$k]['has_repay_status'] = "提前还款";
							break;
						case 1;
							$list[$k]['has_repay_status'] = "准时还款";
							break;
						case 2;
							$list[$k]['has_repay_status'] = "逾期还款";
							break;
						case 3;
							$list[$k]['has_repay_status'] = "严重逾期";
							break;
					}
				}
				else{
					$list[$k]['has_repay_status'] = "<span style='color:red'>未还</span>";
				}
				
				if($v['is_site_bad'] == 1){
					$list[$k]['site_bad_status'] = "<span style='color:red'>坏账</span>";
				}
				else{
					$list[$k]['site_bad_status'] = "正常";
				}
			}
			
			$page = $p->show();
			$this->assign ( "page", $page );
			
		}
		
		$sortImg = $sort; //排序图标
		$sortAlt = $sort == 'desc' ? l("ASC_SORT") : l("DESC_SORT"); //排序提示
		$sort = $sort == 'desc' ? 1 : 0; //排序方式
		
		$this->assign ( 'sort', $sort );
		$this->assign ( 'order', $sorder );
		$this->assign ( 'sortImg', $sortImg );
		$this->assign ( 'sortType', $sortAlt );
		
		$this->assign("list",$list);
		$this->display ();
		return;
	}
	
	public function three_msg(){
		//开始加载搜索条件
		//开始加载搜索条件
		$condition =" 1=1 ";
		
		$status = intval($_REQUEST['status']);
		if($status >0){
			if(($status-1)==0)
				$condition .= " AND dl.has_repay=0 ";
			else
				$condition .= " AND dl.has_repay=1 and dl.status=".($status-2);
		}
		
		$deal_status = intval($_REQUEST['deal_status']);
		if($deal_status >0){
			$condition .= " AND dl.is_site_bad=".($deal_status-1);
		}
		
		$begin_time  = !isset($_REQUEST['begin_time'])? to_timespan(to_date(TIME_UTC ,"Y-m-d"),"Y-m-d")  : (trim($_REQUEST['begin_time']) =="" ? 0 : to_timespan($_REQUEST['begin_time'],"Y-m-d"));
		$end_time  = !isset($_REQUEST['end_time'])?to_timespan(to_date(TIME_UTC ,"Y-m-d"),"Y-m-d") + 3*24*3600 : (trim($_REQUEST['end_time']) =="" ? 0 : to_timespan($_REQUEST['end_time'],"Y-m-d"));
		if($begin_time > 0 || $end_time > 0){
			if($end_time==0)
			{
				$condition .= " and dl.repay_time >= $begin_time ";	
			}
			else
				$condition .= " and dl.repay_time between  $begin_time and $end_time ";	
		}
		
		
		if(trim($_REQUEST['name'])!='')
		{
			$condition .= " and d.name like '%".trim($_REQUEST['name'])."%'";
		}
		
		if(trim($_REQUEST['user_name'])!='')
		{
			$condition .= " and dl.user_id in (select id from  ".DB_PREFIX."user WHERE user_name='".trim($_REQUEST['user_name'])."')";			
		}

		if(intval($_REQUEST['cate_id'])>0)
		{
			require_once APP_ROOT_PATH."system/utils/child.php";
			$child = new Child("deal_cate");
			$cate_ids = $child->getChildIds(intval($_REQUEST['cate_id']));
			$cate_ids[] = intval($_REQUEST['cate_id']);
			$condition .=" and d.cate_id in (".implode(",",$cate_ids).") ";
		}
		
		$sql_count = " SELECT count(*) FROM ".DB_PREFIX."deal_repay dl LEFT JOIN ".DB_PREFIX."deal d ON d.id=dl.deal_id WHERE $condition ";
	
		$rs_count = $GLOBALS['db']->getOne($sql_count);
		$list = array();
		if($rs_count > 0){
			
			if (! empty ( $_REQUEST ['listRows'] )) {
				$listRows = $_REQUEST ['listRows'];
			} else {
				$listRows = '';
			}
			$p = new Page ( $rs_count, $listRows );
			
			$sql_list =  " SELECT dl.*,dl.l_key + 1 as l_key_index,d.name,d.cate_id,d.send_three_msg_time FROM ".DB_PREFIX."deal_repay dl LEFT JOIN ".DB_PREFIX."deal d  ON d.id=dl.deal_id WHERE $condition ORDER BY dl.repay_time ASC LIMIT ".$p->firstRow . ',' . $p->listRows;
			$list = $GLOBALS['db']->getAll($sql_list);			
		}
		$deal_ids = array();
		//发送信息
		foreach($list as $k=>$v){
			if($v['send_three_msg_time'] != $v['repay_time']){
				
				$deal_ids[]=$v['id'];
				
				$repay_money = $v['repay_money'] + $v['manage_money'] + $v['manage_impose_money'] + $v['impose_money'];
				//站内信
				//$content = "您在".app_conf("SHOP_TITLE")."的借款 “<a href=\"".url("index","deal",array("id"=>$v['id']))."\">".$v['name']."</a>”，最近一期还款将于".to_date($v['repay_time'],"d")."日到期，需还金额".round($repay_money,2)."元。";
				
				$group_arr = array(0,$v['user_id']);
				sort($group_arr);
				$group_arr[] =  4;
				
				$sh_notice['shop_title'] = app_conf("SHOP_TITLE");																		//站点名称
				$sh_notice['deal_name'] = "“<a href=\"".url("index","deal",array("id"=>$v['id']))."\">".$v['name']."</a>”";				//借款名称
				$sh_notice['repay_time'] = to_date($v['repay_time'],"d");																//还款时间
				$sh_notice['money'] = round($repay_money,2);	
				$GLOBALS['tmpl']->assign("sh_notice",$sh_notice);
				$tmpl_sz_failed_content = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."msg_template where name = 'TPL_INS_DEAL_REPAY_SMS'",false);
				$sh_content = $GLOBALS['tmpl']->fetch("str:".$tmpl_sz_failed_content['content']);
				
				$msg_data['content'] = $sh_content;
				$msg_data['to_user_id'] = $v['user_id'];
				$msg_data['create_time'] = TIME_UTC;
				$msg_data['type'] = 0;
				$msg_data['group_key'] = implode("_",$group_arr);
				$msg_data['is_notice'] = 12;
				$msg_data['fav_id'] = $v['id'];
				
				$GLOBALS['db']->autoExecute(DB_PREFIX."msg_box",$msg_data);
				$id = $GLOBALS['db']->insert_id();
				$GLOBALS['db']->query("update ".DB_PREFIX."msg_box set group_key = '".$msg_data['group_key']."_".$id."' where id = ".$id);
				
				$user_info  = D("User")->where("id=".$v['user_id'])->find();
				
				//邮件
				if(app_conf("MAIL_ON")==1)
				{
					$tmpl = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."msg_template where name = 'TPL_DEAL_THREE_EMAIL'",false);
					$tmpl_content = $tmpl['content'];
					
					$notice['user_name'] = $user_info['user_name'];
					$notice['deal_name'] = $v['name'];
					$notice['deal_url'] = SITE_DOMAIN.url("index","deal",array("id"=>$v['id']));
					$notice['repay_url'] = SITE_DOMAIN.url("index","uc_deal#refund");
					$notice['repay_time_y'] = to_date($v['repay_time'],"Y");
					$notice['repay_time_m'] = to_date($v['repay_time'],"m");
					$notice['repay_time_d'] = to_date($v['repay_time'],"d");
					$notice['site_name'] = app_conf("SHOP_TITLE");
					$notice['repay_money'] = round($repay_money,2);
					$notice['help_url'] = SITE_DOMAIN.url("index","helpcenter");
					$notice['msg_cof_setting_url'] = SITE_DOMAIN.url("index","uc_msg#setting");
					
					$GLOBALS['tmpl']->assign("notice",$notice);
						
					$msg = $GLOBALS['tmpl']->fetch("str:".$tmpl_content);
					$msg_data['dest'] = $user_info['email'];
					$msg_data['send_type'] = 1;
					$msg_data['title'] = "催款邮件通知";
					$msg_data['content'] = addslashes($msg);
					$msg_data['send_time'] = 0;
					$msg_data['is_send'] = 0;
					$msg_data['create_time'] = TIME_UTC;
					$msg_data['user_id'] = $user_info['id'];
					$msg_data['is_html'] = $tmpl['is_html'];
					$GLOBALS['db']->autoExecute(DB_PREFIX."deal_msg_list",$msg_data); //插入
				}
				
				//短信
				if(app_conf("SMS_ON")==1)
				{
					$tmpl = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."msg_template where name = 'TPL_DEAL_THREE_SMS'");				
					$tmpl_content = $tmpl['content'];
									
					$notice['user_name'] = $user_info["user_name"];
					$notice['deal_name'] = $v['name'];
					$notice['repay_time_y'] = to_date($v['repay_time'],"Y");
					$notice['repay_time_m'] = to_date($v['repay_time'],"m");
					$notice['repay_time_d'] = to_date($v['repay_time'],"d");
					$notice['site_name'] = app_conf("SHOP_TITLE");
					$notice['repay_money'] = round($repay_money,2);
					
					$GLOBALS['tmpl']->assign("notice",$notice);
						
					$msg = $GLOBALS['tmpl']->fetch("str:".$tmpl_content);
					
					$msg_data['dest'] = $user_info['mobile'];
					$msg_data['send_type'] = 0;
					$msg_data['title'] = "催款短信通知";
					$msg_data['content'] = addslashes($msg);;
					$msg_data['send_time'] = 0;
					$msg_data['is_send'] = 0;
					$msg_data['create_time'] = TIME_UTC;
					$msg_data['user_id'] = $user_info['id'];
					$msg_data['is_html'] = $tmpl['is_html'];
					$GLOBALS['db']->autoExecute(DB_PREFIX."deal_msg_list",$msg_data); //插入				
				}
				$GLOBALS['db']->autoExecute(DB_PREFIX."deal",array("send_three_msg_time"=>$v['repay_time']),"UPDATE","id=".$v['deal_id']); 
			}
		}
		
		//成功提示
		if($deal_ids){
			save_log(implode(",",$deal_ids)."发送催款提示",1);
		}
		$this->success("发送成功");
		
	}
	
	function repay_log(){
		$id = intval($_REQUEST['id']);
		$deal_repay = $GLOBALS['db']->getRow("SELECT dr.*,dr.l_key + 1 as l_key_index,d.name FROM  ".DB_PREFIX."deal_repay dr LEFT JOIN ".DB_PREFIX."deal d ON d.id=dr.deal_id WHERE  dr.id=".$id);
		if(!$deal_repay){
			$this->error("账单不存在");
		}
		
		$sql_count = " SELECT count(*) FROM ".DB_PREFIX."deal_repay_log WHERE repay_id= ".$id;
	
		$rs_count = $GLOBALS['db']->getOne($sql_count);
		$list = array();
		if($rs_count > 0){
			
			if (! empty ( $_REQUEST ['listRows'] )) {
				$listRows = $_REQUEST ['listRows'];
			} else {
				$listRows = '';
			}
			$p = new Page ( $rs_count, $listRows );
			
			$sql_list =  " SELECT * FROM ".DB_PREFIX."deal_repay_log WHERE repay_id= ".$id." ORDER BY id DESC LIMIT ".$p->firstRow . ',' . $p->listRows;
			$list = $GLOBALS['db']->getAll($sql_list);
			
			$page = $p->show();
			$this->assign ( "page", $page );
			
		}
		$this->assign("list",$list);
		$this->assign("deal_repay",$deal_repay);
		$this->display ();
		
	}
	
	function op_status(){
		$id = intval($_REQUEST['id']);
		$deal_repay = $GLOBALS['db']->getRow("SELECT dr.*,dr.l_key + 1 as l_key_index,d.name FROM  ".DB_PREFIX."deal_repay dr LEFT JOIN ".DB_PREFIX."deal d ON d.id=dr.deal_id WHERE  dr.id=".$id);
		if(!$deal_repay){
			$this->error("账单不存在");
		}
		if($deal_repay['has_repay']==1){
			switch($deal_repay['status']){
				case 0;
					$deal_repay['has_repay_status'] = "提前还款";
					break;
				case 1;
					$deal_repay['has_repay_status'] = "准时还款";
					break;
				case 2;
					$deal_repay['has_repay_status'] = "逾期还款";
					break;
				case 3;
					$deal_repay['has_repay_status'] = "严重逾期";
					break;
			}
		}
		else{
			$deal_repay['has_repay_status'] = "<span style='color:red'>未还</span>";
		}
		
		if($deal_repay['is_site_bad'] == 1){
			$deal_repay['site_bad_status'] = "<span style='color:red'>坏账</span>";
		}
		else{
			$deal_repay['site_bad_status'] = "正常";
		}
		
		$this->assign("deal_repay",$deal_repay);
		$this->display();
	}
	
	public function do_op_status(){
		$id = intval($_REQUEST['id']);
		
		$deal_repay = $GLOBALS['db']->getRow("SELECT dr.*,dr.l_key + 1 as l_key_index,d.name FROM  ".DB_PREFIX."deal_repay dr LEFT JOIN ".DB_PREFIX."deal d ON d.id=dr.deal_id WHERE  dr.id=".$id);
		if(!$deal_repay){
			$this->error("账单不存在");
		}
		if(intval($_REQUEST['is_site_bad'])==0){
			$this->success("操作完成");
			die();
		}
		$adm_session = es_session::get(md5(conf("AUTH_KEY")));
		$msg = strim($_REQUEST['desc']);
		$GLOBALS['db']->query("UPDATE ".DB_PREFIX."deal_repay SET is_site_bad=".(intval($_REQUEST['is_site_bad']) -1)." WHERE id=".$id);
		if($GLOBALS['db']->affected_rows()){
			
			save_log($deal_repay['name']." 第 ".($deal_repay['l_key_index'] + 1)."期，坏账操作",1);
			
			repay_log($id,$msg,0,$adm_session['adm_id']);
			$this->success("操作成功");
			
		}
		else{
			save_log($deal_repay['name']." 第 ".($deal_repay['l_key_index'] + 1)."期，坏账操作",0);
			
			$this->error("操作失败");
		}
	}
	
	/**
	 * 逾期账单
	 */
	public function yuqi()
	{
		$this->assign("main_title",L("DEAL_YUQI"));
		
		if (isset ( $_REQUEST ['_order'] )) {
			$sorder = $_REQUEST ['_order'];
		}
		else{
			$sorder = "id";
		}
		
		
		switch($sorder){
			case "name":
			case "cate_id":
					$order ="d.".$sorder;
				break;
			case "has_repay_status":
					$order ="dl.status";
				break;
			case "site_bad_status":
					$order ="dl.is_site_bad";
				break;
			case "is_has_send":
					$order ="d.send_three_msg_time";
				break;
			case "l_key_index":
					$order ="dl.l_key";
				break;
			default : 
				$order ="dl.".$sorder;
				break;
		}
		
	
		
		//排序方式默认按照倒序排列
		//接受 sost参数 0 表示倒序 非0都 表示正序
		if (isset ( $_REQUEST ['_sort'] )) {
			$sort = $_REQUEST ['_sort'] ? 'asc' : 'desc';
		}
		else{
			$sort = "desc";
		}
		
		//分类
		$cate_tree = M("DealCate")->where('is_delete = 0')->findAll();
		$cate_tree = D("DealCate")->toFormatTree($cate_tree,'name');
		$this->assign("cate_tree",$cate_tree);
		
		//开始加载搜索条件
		$condition .= "  (dl.repay_time + 24*3600 - 1) < ".TIME_UTC." AND dl.has_repay=0 ";	
		
		$begin_time  = !isset($_REQUEST['begin_time'])? 0 : to_timespan($_REQUEST['begin_time'],"Y-m-d");
		if($begin_time > 0){
			if($begin_time > TIME_UTC)
			{
				$this->error("不能超过当前时间");
			}
			$condition .= " and dl.repay_time >= $begin_time ";	
		}
		
		$deal_status = intval($_REQUEST['deal_status']);
		if($deal_status >0){
			$condition .= " AND dl.is_site_bad=".($deal_status-1);
		}
		
		
		if(trim($_REQUEST['name'])!='')
		{
			$condition .= " and d.name like '%".trim($_REQUEST['name'])."%'";
		}
		
		if(trim($_REQUEST['user_name'])!='')
		{
			$condition .= " and dl.user_id in (select id from  ".DB_PREFIX."user WHERE user_name='".trim($_REQUEST['user_name'])."')";			
		}

		if(intval($_REQUEST['cate_id'])>0)
		{
			require_once APP_ROOT_PATH."system/utils/child.php";
			$child = new Child("deal_cate");
			$cate_ids = $child->getChildIds(intval($_REQUEST['cate_id']));
			$cate_ids[] = intval($_REQUEST['cate_id']);
			$condition .=" and d.cate_id in (".implode(",",$cate_ids).") ";
		}
		
		$sql_count = " SELECT count(*) FROM ".DB_PREFIX."deal_repay dl LEFT JOIN ".DB_PREFIX."deal d ON d.id=dl.deal_id WHERE $condition ";
	
		$rs_count = $GLOBALS['db']->getOne($sql_count);
		$list = array();
		if($rs_count > 0){
			
			if (! empty ( $_REQUEST ['listRows'] )) {
				$listRows = $_REQUEST ['listRows'];
			} else {
				$listRows = '';
			}
			$p = new Page ( $rs_count, $listRows );
			
			$sql_list =  " SELECT dl.*,dl.l_key + 1 as l_key_index,d.name,d.cate_id,d.send_three_msg_time,u.user_name,u.mobile FROM ".DB_PREFIX."deal_repay dl LEFT JOIN ".DB_PREFIX."deal d  ON d.id=dl.deal_id left join ".DB_PREFIX."user u on u.id=dl.user_id WHERE $condition ORDER BY $order $sort LIMIT ".$p->firstRow . ',' . $p->listRows;
			$list = $GLOBALS['db']->getAll($sql_list);
			foreach($list as $k=>$v){
				$list[$k]['l_key_index'] = "第 ".$v['l_key_index']." 期";
				if($v['send_three_msg_time'] == $v['repay_time']){
					$list[$k]['is_has_send'] = 1;
				}
				else{
					$list[$k]['is_has_send'] = 0;
				}
				if($v['has_repay']==1){
					switch($v['status']){
						case 0;
							$list[$k]['has_repay_status'] = "提前还款";
							break;
						case 1;
							$list[$k]['has_repay_status'] = "准时还款";
							break;
						case 2;
							$list[$k]['has_repay_status'] = "逾期还款";
							break;
						case 3;
							$list[$k]['has_repay_status'] = "严重逾期";
							break;
					}
				}
				else{
					$list[$k]['has_repay_status'] = "<span style='color:red'>未还</span>";
				}
				
				if($v['is_site_bad'] == 1){
					$list[$k]['site_bad_status'] = "<span style='color:red'>坏账</span>";
				}
				else{
					$list[$k]['site_bad_status'] = "正常";
				}
			}
			
			$page = $p->show();
			$this->assign ( "page", $page );
			
		}
		
		$sortImg = $sort; //排序图标
		$sortAlt = $sort == 'desc' ? l("ASC_SORT") : l("DESC_SORT"); //排序提示
		$sort = $sort == 'desc' ? 1 : 0; //排序方式
		
		$this->assign ( 'sort', $sort );
		$this->assign ( 'order', $sorder );
		$this->assign ( 'sortImg', $sortImg );
		$this->assign ( 'sortType', $sortAlt );
		
		$this->assign("list",$list);
		$this->display ();
		return;
	}
	
	/**
	 * 垫付账单
	 */
	function generation_repay(){
		$this->assign("main_title","垫付账单");
		
		//分类
		$cate_tree = M("DealCate")->where('is_delete = 0')->findAll();
		$cate_tree = D("DealCate")->toFormatTree($cate_tree,'name');
		$this->assign("cate_tree",$cate_tree);
		
		if (isset ( $_REQUEST ['_order'] )) {
			$sorder = $_REQUEST ['_order'];
		}
		else{
			$sorder = "id";
		}
		
		switch($sorder){
			case "name":
			case "cate_id":
					$order ="d.".$sorder;
				break;
			case "site_bad_status":
					$order ="dr.is_site_bad";
				break;
			case "is_has_send":
					$order ="d.send_three_msg_time";
				break;
			case "l_key_index":
					$order ="dr.l_key";
				break;
			default : 
				$order ="gr.".$sorder;
				break;
		}
		
		//排序方式默认按照倒序排列
		//接受 sost参数 0 表示倒序 非0都 表示正序
		if (isset ( $_REQUEST ['_sort'] )) {
			$sort = $_REQUEST ['_sort'] ? 'asc' : 'desc';
		}
		else{
			$sort = "desc";
		}
		
		$condition = " 1= 1 ";
		
		if(isset($_REQUEST['status'])){
			$status = intval($_REQUEST['status']);
			if($status >0){
				$condition .= " AND gr.status=".($status-1);
			}
		}
		else{
			$condition .= " AND gr.status=0";
			$_REQUEST['status'] = 1;
		}
		
		$begin_time  = trim($_REQUEST['begin_time']) =="" ? 0 : to_timespan($_REQUEST['begin_time'],"Y-m-d");
		$end_time  = trim($_REQUEST['end_time']) =="" ? 0 : to_timespan($_REQUEST['end_time'],"Y-m-d");
		if($begin_time > 0 || $end_time > 0){
			if($end_time==0)
			{
				$condition .= " and dr.repay_time >= $begin_time ";	
			}
			else
				$condition .= " and dr.repay_time between  $begin_time and $end_time ";	
		}
		
		if($begin_time > 0)
			$_REQUEST['begin_time'] = to_date($begin_time ,"Y-m-d");
		if($end_time > 0)
			$_REQUEST['end_time'] = to_date($end_time ,"Y-m-d");
		
		
		$deal_status = intval($_REQUEST['deal_status']);
		if($deal_status >0){
			$condition .= " AND dr.is_site_bad=".($deal_status-1);
		}
		
		
		if(trim($_REQUEST['name'])!='')
		{
			$condition .= " and d.name like '%".trim($_REQUEST['name'])."%'";
		}
		
		if(trim($_REQUEST['user_name'])!='')
		{
			$condition .= " and dr.user_id in (select id from  ".DB_PREFIX."user WHERE user_name='".trim($_REQUEST['user_name'])."')";			
		}
		
		if(intval($_REQUEST['cate_id'])>0)
		{
			require_once APP_ROOT_PATH."system/utils/child.php";
			$child = new Child("deal_cate");
			$cate_ids = $child->getChildIds(intval($_REQUEST['cate_id']));
			$cate_ids[] = intval($_REQUEST['cate_id']);
			$condition .=" and d.cate_id in (".implode(",",$cate_ids).") ";
		}
		
		$sql_count = "SELECT count(*) FROM ".DB_PREFIX."generation_repay gr LEFT join ".DB_PREFIX."deal_repay dr ON dr.id=gr.repay_id LEFT JOIN ".DB_PREFIX."deal d ON d.id=gr.deal_id ";
		$rs_count = $GLOBALS['db']->getOne($sql_count);
		$list = array();
		if($rs_count > 0){
			if (! empty ( $_REQUEST ['listRows'] )) {
				$listRows = $_REQUEST ['listRows'];
			} else {
				$listRows = '';
			}
			$p = new Page ( $rs_count, $listRows );
			
			$sql_list =  " SELECT gr.*,dr.l_key + 1 as l_key_index,dr.l_key,d.name,d.cate_id,d.send_three_msg_time,dr.user_id,dr.repay_time,agc.name as agency_name,u.user_name,u.mobile FROM ".DB_PREFIX."generation_repay gr LEFT join ".DB_PREFIX."deal_repay dr ON dr.id=gr.repay_id LEFT JOIN ".DB_PREFIX."deal d  ON d.id=gr.deal_id LEFT JOIN ".DB_PREFIX."user agc ON agc.id=gr.agency_id left join ".DB_PREFIX."user u on u.id = d.user_id   WHERE $condition ORDER BY $order $sort LIMIT ".$p->firstRow . ',' . $p->listRows;
		
			$list = $GLOBALS['db']->getAll($sql_list);
			foreach($list as $k=>$v){
				$list[$k]['l_key_index'] = "第 ".$v['l_key_index']." 期";
				
				if($v['status'] == 0){
					$list[$k]['status_format'] = "垫付待收款";
				}
				else{
					$list[$k]['status_format'] = "垫付已收款";
				}
				
				if($v['is_site_bad'] == 1){
					$list[$k]['site_bad_status'] = "<span style='color:red'>坏账</span>";
				}
				else{
					$list[$k]['site_bad_status'] = "正常";
				}
				
				$list[$k]['total_money'] = $v['repay_money'] + $v['manage_money'] + $v['impose_money']+ $v['manage_impose_money'];
								
				$list[$k]['create_time_format'] = to_date($v['create_time'],"Y-m-d H:i");
			}
			
			$page = $p->show();
			$this->assign ( "page", $page );
			
		}
		
		$sortImg = $sort; //排序图标
		$sortAlt = $sort == 'desc' ? l("ASC_SORT") : l("DESC_SORT"); //排序提示
		$sort = $sort == 'desc' ? 1 : 0; //排序方式
		
		$this->assign ( 'sort', $sort );
		$this->assign ( 'order', $sorder );
		$this->assign ( 'sortImg', $sortImg );
		$this->assign ( 'sortType', $sortAlt );
		
		$this->assign ( 'list', $list );
		
		$this->display ();
	}
	
	
	/**
	 * 网站收益
	 */
	function site_money(){
		$this->assign("main_title","网站收益");
		$type_name = array(
				"9" => "提现手续费",
				"10" => "借款管理费",
				"12" => "逾期管理费",
				
				"13" => "人工操作",
				"14" => "借款服务费",
				"17" => "债权转让管理费",
				"18" => "开户奖励",
				"20" => "投标管理费",
				
				"23" => "邀请返利",
				"24" => "投标返利",
				"25" => "签到成功",
				
				"26" => "逾期罚金（垫付后）",
				"27" => "其他费用",
		);
		
		$this->assign('type_name',$type_name);
		
		
		
		if (isset ( $_REQUEST ['_order'] )) {
			$sorder = $_REQUEST ['_order'];
		}
		else{
			$sorder = "id";
		}
		
		switch($sorder){
			case "user_id":
				$order ="user_id";
				break;
			case "money":
				$order ="money";
				break;
			default :
				$order = $sorder;
				break;
		}
		
		//排序方式默认按照倒序排列
		//接受 sost参数 0 表示倒序 非0都 表示正序
		if (isset ( $_REQUEST ['_sort'] )) {
			$sort = $_REQUEST ['_sort'] ? 'asc' : 'desc';
		}
		else{
			$sort = "desc";
		}
		
		$condition = " 1= 1 ";
		
		$begin_time  = !isset($_REQUEST['begin_time'])? 0 : (trim($_REQUEST['begin_time']) =="" ? 0 : to_timespan($_REQUEST['begin_time'],"Y-m-d"));
		$end_time  = !isset($_REQUEST['end_time'])? 0 : (trim($_REQUEST['end_time']) =="" ? 0 : to_timespan($_REQUEST['end_time'],"Y-m-d"));
		$status =!isset($_REQUEST['status'])?0 : (trim($_REQUEST['status'])== ""? 0 : intval($_REQUEST['status'])   );
		
		if($status >0){
			$condition .= " AND type=".$status;
		}
		
		if($begin_time > 0 || $end_time > 0){
			if($end_time==0)
			{
				$condition .= " and s.create_time >= $begin_time ";
			}
			else
				$condition .= " and s.create_time between  $begin_time and $end_time ";
		}
		
		$_REQUEST['begin_time'] = to_date($begin_time ,"Y-m-d");
		$_REQUEST['end_time'] = to_date($end_time ,"Y-m-d");
		
	
		if(trim($_REQUEST['user_name'])!='')
		{
			$condition .= " and u.user_name like '%".trim($_REQUEST['user_name'])."%'";
		}
		$sql_count = " SELECT count(*) FROM ".DB_PREFIX."site_money_log s  left join ".DB_PREFIX."user u on u.id=s.user_id  WHERE $condition ";
		
		$rs_count = $GLOBALS['db']->getOne($sql_count);
		
		$list = array();
		if($rs_count > 0){
				
			if (! empty ( $_REQUEST ['listRows'] )) {
				$listRows = $_REQUEST ['listRows'];
			} else {
				$listRows = '';
			}
			$p = new Page ( $rs_count, $listRows );
				
			$sql_list =  " select s.* ,u.user_name from ".DB_PREFIX."site_money_log s left join ".DB_PREFIX."user u on u.id=s.user_id  WHERE $condition ORDER BY $order $sort LIMIT ".$p->firstRow . ',' . $p->listRows;
			
			$list = $GLOBALS['db']->getAll($sql_list);
			foreach($list as $k=>$v){
				$list[$k]['type_format'] = $type_name[$v['type']];
			}
			$page = $p->show();
			$this->assign ( "page", $page );
		}
		
		$sortImg = $sort; //排序图标
		$sortAlt = $sort == 'desc' ? l("ASC_SORT") : l("DESC_SORT"); //排序提示
		$sort = $sort == 'desc' ? 1 : 0; //排序方式
		
		$this->assign ( 'sort', $sort );
		$this->assign ( 'order', $sorder );
		$this->assign ( 'sortImg', $sortImg );
		$this->assign ( 'sortType', $sortAlt );
		
		$this->assign("list",$list);
		$this->display ();
		return; 
	}
	
	
	
	function op_generation_repay_status(){
		$id = intval($_REQUEST['id']);
		$deal_repay = $GLOBALS['db']->getRow("SELECT gr.*,dr.user_id,dr.l_key + 1 as l_key_index,dr.is_site_bad,d.name FROM ".DB_PREFIX."generation_repay gr LEFT JOIN  ".DB_PREFIX."deal_repay dr ON dr.id=gr.repay_id LEFT JOIN ".DB_PREFIX."deal d ON d.id=dr.deal_id WHERE  gr.id=".$id);
		if(!$deal_repay){
			$this->error("账单不存在");
		}
		
		switch($deal_repay['status']){
			case 0;
				$deal_repay['status_format'] = "垫付待收款";
				break;
			case 1;
				$deal_repay['status_format'] = "垫付已收款";
				break;
		}
		
		
		if($deal_repay['is_site_bad'] == 1){
			$deal_repay['site_bad_status'] = "<span style='color:red'>坏账</span>";
		}
		else{
			$deal_repay['site_bad_status'] = "正常";
		}
		
		$deal_repay['total_money'] = $deal_repay['repay_money'] + $deal_repay['manage_money'] + $deal_repay['impose_money']+ $deal_repay['manage_impose_money'];
		
		$this->assign("deal_repay",$deal_repay);
		$this->display();
	}
	
	function do_op_generation_repay_status(){
		B('FilterString');
		$data = M("GenerationRepay")->create ();
		
		$id= intval($_REQUEST['id']);
		$data['status'] =  intval($data['status']) - 1;
		if($data['status'] < 0){
			unset($data['status']);
		}
	
		// 更新数据
		$list=M("GenerationRepay")->save ($data);
		if (false !== $list) {
			//成功提示
			save_log($id.L("UPDATE_SUCCESS"),1);
			$this->success(L("UPDATE_SUCCESS"));
		} else {
			//错误提示
			save_log($id.L("UPDATE_FAILED"),0);
			$this->error(L("UPDATE_FAILED"),0,$id.L("UPDATE_FAILED"));
		}
	}
	
	
	public function trash()
	{
		$condition['is_delete'] = 1;
		$this->assign("default_map",$condition);
		parent::index();
	}
	public function add()
	{
		$this->assign("new_sort", M("Deal")->where("is_delete=0")->max("sort")+1);
		
		$deal_cate_tree = M("DealCate")->where('is_delete = 0')->findAll();
		$deal_cate_tree = D("DealCate")->toFormatTree($deal_cate_tree,'name');
		$this->assign("deal_cate_tree",$deal_cate_tree);
		
		$deal_sn = "MER".to_date(TIME_UTC,"Y")."".str_pad(D("Deal")->where()->max("id") + 1,7,0,STR_PAD_LEFT);
		
		$this->assign("deal_sn",$deal_sn);
		
		$citys = M("DealCity")->where('is_delete= 0 and is_effect=1 and user_type =2')->findAll();
		$this->assign ( 'citys', $citys );
		
		$deal_agency = M("User")->where('is_effect = 1')->order('sort DESC')->findAll();
		$this->assign("deal_agency",$deal_agency);
		
		$deal_type_tree = M("DealLoanType")->findAll();
		$deal_type_tree = D("DealLoanType")->toFormatTree($deal_type_tree,'name');
		$this->assign("deal_type_tree",$deal_type_tree);
		
		$loantype_list = load_auto_cache("loantype_list");
    	$this->assign("loantype_list",$loantype_list);

		$this->display();
	}
	
	public function insert() {
		B('FilterString');
		$ajax = intval($_REQUEST['ajax']);
		$data = M(MODULE_NAME)->create ();

		//开始验证有效性
		$this->assign("jumpUrl","javascript:history.back(-1);");
		
		if(!check_empty($data['name']))
		{
			$this->error(L("DEAL_NAME_EMPTY_TIP"));
		}	
		if(!check_empty($data['sub_name']))
		{
			$this->error(L("DEAL_SUB_NAME_EMPTY_TIP"));
		}	
		if($data['cate_id']==0)
		{
			$this->error(L("DEAL_CATE_EMPTY_TIP"));
		}
		if($data['type_id']==0)
		{
			$this->error(L("DEAL_TYPE_EMPTY_TIP"));
		}
		
		if(D("Deal")->where("deal_sn='".$data['deal_sn']."'")->count() > 0){
			$this->error("借款编号已存在");
		}
		
		
		$loantype_list = load_auto_cache("loantype_list");
		if(!in_array($data['repay_time_type'],$loantype_list[$data['loantype']]['repay_time_type'])){
			$this->error("还款方式不支持当前借款期限类型");
		}
		
		// 更新数据
		
		
		$log_info = $data['name'];
		$data['create_time'] = TIME_UTC;
		$data['update_time'] = TIME_UTC;
		$data['start_time'] = trim($data['start_time'])==''?0:to_timespan($data['start_time']);
		
		$list=M(MODULE_NAME)->add($data);
		if (false !== $list) {
			foreach($_REQUEST['city_id'] as $k=>$v){
				if(intval($v) > 0){
					$deal_city_link['deal_id'] =$list;
					$deal_city_link['city_id'] = intval($v);
					M("DealCityLink")->add ($deal_city_link);
				}
			
			}
			
			require_once(APP_ROOT_PATH."app/Lib/common.php");
			//成功提示
			syn_deal_status($list);
			syn_deal_match($list);
			save_log("编号：$list，".$log_info.L("INSERT_SUCCESS"),1);
			$this->assign("jumpUrl",u(MODULE_NAME."/add"));
			$this->success(L("INSERT_SUCCESS"));
		} else {
			//错误提示
			$dbErr = M()->getDbError();
			save_log($log_info.L("INSERT_FAILED").$dbErr,0);
			$this->error(L("INSERT_FAILED").$dbErr);
		}
	}	
	
	public function edit() {		
		$id = intval($_REQUEST ['id']);
		$condition['is_delete'] = 0;
		$condition['id'] = $id;		
		$vo = M(MODULE_NAME)->where($condition)->find();
		
		$vo['start_time'] = $vo['start_time']!=0?to_date($vo['start_time']):'';
		
		if($vo['deal_status'] ==0){
			$level_list = load_auto_cache("level");
			$u_level = M("User")->where("id=".$vo['user_id'])->getField("level_id");
			$vo['services_fee'] = $level_list['services_fee'][$u_level];
		}
		
		if($vo['deal_sn']==""){
			$deal_sn = "MER".to_date(TIME_UTC,"Y")."".str_pad($id,7,0,STR_PAD_LEFT);
			$this->assign ( 'deal_sn', $deal_sn );
		}
		
		$user_info = M("User") -> getById($vo['user_id']);
		$old_imgdata_str = unserialize($user_info['view_info']);
	
		foreach($old_imgdata_str as $k=>$v){
			$old_imgdata_str[$k]['key'] = $k;  /*+一个key*/
		}
		$this->assign("user_info",$user_info);
		$this->assign("old_imgdata_str",$old_imgdata_str);
		

		$vo['view_info'] = unserialize($vo['view_info']);
		
		if($vo['publish_wait'] == 1){
			$vo['manage_fee'] = app_conf("MANAGE_FEE");
			$vo['user_loan_manage_fee'] = app_conf("USER_LOAN_MANAGE_FEE");
			$vo['manage_impose_fee_day1'] = app_conf("MANAGE_IMPOSE_FEE_DAY1");
			$vo['manage_impose_fee_day2'] = app_conf("MANAGE_IMPOSE_FEE_DAY2");
			$vo['impose_fee_day1'] = app_conf("IMPOSE_FEE_DAY1");
			$vo['impose_fee_day2'] = app_conf("IMPOSE_FEE_DAY2");
			$vo['user_load_transfer_fee'] = app_conf("USER_LOAD_TRANSFER_FEE");
			$vo['compensate_fee'] = app_conf("COMPENSATE_FEE");
			$vo['user_bid_rebate'] = app_conf("USER_BID_REBATE");
			$vo['generation_position'] = 100;
		}
		
		$this->assign ( 'vo', $vo );
		
		$citys = M("DealCity")->where('is_delete= 0 and is_effect=1 ')->findAll();
		$citys_link = M("DealCityLink")->where("deal_id=".$id)->findAll();
		foreach($citys as $k=>$v){
			foreach($citys_link as $kk=>$vv){
				if($vv['city_id'] == $v['id'])
					$citys[$k]['is_selected'] = 1;
			}
		}
		
		$this->assign ( 'citys', $citys );
		
		if(trim($_REQUEST['type'])=="deal_status"){
			$this->display ("Deal:deal_status");
			exit();
		}
				
		$deal_cate_tree = M("DealCate")->where('is_delete = 0')->findAll();
		$deal_cate_tree = D("DealCate")->toFormatTree($deal_cate_tree,'name');
		$this->assign("deal_cate_tree",$deal_cate_tree);
		
		$deal_agency = M("User")->where('is_effect = 1 and user_type = 2 ')->order('sort DESC')->findAll();
		$this->assign("deal_agency",$deal_agency);
		
		$deal_type_tree = M("DealLoanType")->findAll();
		$deal_type_tree = D("DealLoanType")->toFormatTree($deal_type_tree,'name');
		$this->assign("deal_type_tree",$deal_type_tree);
		
		$loantype_list = load_auto_cache("loantype_list");
    	$this->assign("loantype_list",$loantype_list);
		$this->display ();
	}
	
	
	public function update() {
		B('FilterString');
		$data = M(MODULE_NAME)->create ();
		
		$log_info = M(MODULE_NAME)->where("id=".intval($data['id']))->getField("name");
		//开始验证有效性
		$this->assign("jumpUrl","javascript:history.back(-1);");
		if(!check_empty($data['name']))
		{
			$this->error(L("DEAL_NAME_EMPTY_TIP"));
		}	
		if(!check_empty($data['sub_name']))
		{
			$this->error(L("DEAL_SUB_NAME_EMPTY_TIP"));
		}		
		if($data['cate_id']==0)
		{
			$this->error(L("DEAL_CATE_EMPTY_TIP"));
		}
		if(D("Deal")->where("deal_sn='".$data['deal_sn']."' and id<>".$data['id'])->count() > 0){
			$this->error("借款编号已存在");
		}
		
		$loantype_list = load_auto_cache("loantype_list");
		if(!in_array($data['repay_time_type'],$loantype_list[$data['loantype']]['repay_time_type'])){
			$this->error("还款方式不支持当前借款期限类型");
		}
		
		$data['update_time'] = TIME_UTC;
		$data['publish_wait'] = 0;
		
		$data['start_time'] = trim($data['start_time'])==''?0:to_timespan($data['start_time']);
		
		$user_info = M("User") -> getById($data['user_id']);
		$old_imgdata_str = unserialize($user_info['view_info']);

		
		$data['view_info'] = array();
		foreach($_REQUEST['key'] as $k=>$v){
			if(isset($old_imgdata_str[$v])){
				$data['view_info'][$v] = $old_imgdata_str[$v];
			}
		}
		$data['view_info'] = serialize($data['view_info']);
		
		if($data['deal_status'] == 4){
			if($GLOBALS['db']->getOne("SELECT sum(money) FROM ".DB_PREFIX."deal_load where deal_id=".$data['id']) <floatval($data['borrow_amount'])){
				$this->error("未满标无法设置为还款状态!");
				exit();
			}
		}
		
		if($data['agency_id']!=M("Deal")->where("id=".$data['id'])->getField("agency_id")){
			$data['agency_status'] = 0;
		}
		
		// 更新数据
		$list=M(MODULE_NAME)->save($data);
		if (false !== $list) {
			
			M("DealCityLink")->where ("deal_id=".$data['id'])->delete();
			foreach($_REQUEST['city_id'] as $k=>$v){
				if(intval($v) > 0){
					$deal_city_link['deal_id'] = $data['id'];
					$deal_city_link['city_id'] = intval($v);
					M("DealCityLink")->add ($deal_city_link);
				}
				
			}
			
			require_once(APP_ROOT_PATH."app/Lib/common.php");
			if($data['is_delete']==3){
				//发送失败短信通知
				if(app_conf("SMS_ON")==1){
					$user_info  = D("User")->where("id=".$data['user_id'])->find();
					$deal_info = D("Deal")->where("id=".$data['id'])->find();
						
					$tmpl = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."msg_template where name = 'TPL_SMS_DEAL_DELETE'");				
					$tmpl_content = $tmpl['content'];
									
					$notice['user_name'] = $user_info["user_name"];
					$notice['deal_name'] = $data['name'];
					$notice['site_name'] = app_conf("SHOP_TITLE");
					$notice['delete_msg'] = $data['delete_msg'];
					$notice['deal_publish_time'] = to_date($deal_info['create_time'],"Y年m月d日");
					
					$GLOBALS['tmpl']->assign("notice",$notice);
					
					$msg = $GLOBALS['tmpl']->fetch("str:".$tmpl_content);
					
					$msg_data['dest'] = $user_info['mobile'];
					$msg_data['send_type'] = 0;
					$msg_data['title'] = "审核失败通知";
					$msg_data['content'] = addslashes($msg);;
					$msg_data['send_time'] = 0;
					$msg_data['is_send'] = 0;
					$msg_data['create_time'] = TIME_UTC;
					$msg_data['user_id'] = $user_info['id'];
					$msg_data['is_html'] = $tmpl['is_html'];
					$GLOBALS['db']->autoExecute(DB_PREFIX."deal_msg_list",$msg_data); //插入	
				}
			}
			else{
				//成功提示
				syn_deal_status($data['id']);
				syn_deal_match($data['id']);
				//发送电子协议邮件
				require_once(APP_ROOT_PATH."app/Lib/deal.php");
				send_deal_contract_email($data['id'],array(),$data['user_id']);
			}
			//成功提示
			save_log("编号：".$data['id']."，".$log_info.L("UPDATE_SUCCESS"),1);
			$this->assign("jumpUrl",u(MODULE_NAME."/edit",array("id"=>$data['id'])));
			$this->success(L("UPDATE_SUCCESS"));
		} else {
			//错误提示
			$dbErr = M()->getDbError();
			save_log("编号：".$data['id']."，".$log_info.L("UPDATE_FAILED").$dbErr,0);
			$this->error(L("UPDATE_FAILED").$dbErr,0);
		}
	}
	
	
	public function delete() {
		//删除指定记录
		$ajax = intval($_REQUEST['ajax']);
		$id = $_REQUEST ['id'];
		if (isset ( $id )) {
				$condition = array ('id' => array ('in', explode ( ',', $id ) ) , 'buy_count'=> array('eq',0) );
				$condition1 = array ('id' => array ('in', explode ( ',', $id ) ) , 'buy_count'=> array('gt',0) );
				//无法删除的
				$rel_data = M(MODULE_NAME)->where($condition1)->findAll();				
				foreach($rel_data as $data)
				{
					$info1[] = "编号：".$data['id']."-".$data['name'];	
				}
				if($info1) $info1_list = implode(",",$info1);
				//可以删除的
				$rel_data = M(MODULE_NAME)->where($condition)->findAll();				
				foreach($rel_data as $data)
				{
					$info[] = "编号：".$data['id']."-".$data['name'];	
				}
				if($info) $info_list = implode(",",$info);
				
				$list = M(MODULE_NAME)->where ( $condition )->setField ( 'is_delete', 1 );
				if ($list!==false) {
					M("Topic")->where(array ('fav_id' => array ('in', explode ( ',', $id ) ) ,"type"=>array('in',array("deal_message","message","deal_message_reply","message_reply","deal_collect","deal_bad"))))->setField("is_effect",0);
					save_log($info_list.l("DELETE_SUCCESS"),1);
					$this->success ("除".$info1_list."外，".l("DELETE_SUCCESS"),$ajax);
				} else {
					save_log($info_list.l("DELETE_FAILED"),0);
					$this->error (l("DELETE_FAILED"),$ajax);
				}
			} else {
				$this->error (l("INVALID_OPERATION"),$ajax);
		}		
	}
	
	
	/*
	 * 拆标
	 */
	function apart(){
		require_once(APP_ROOT_PATH."app/Lib/common.php");
		require_once(APP_ROOT_PATH."app/Lib/deal.php");
		$id = intval($_REQUEST['id']);
		$deal = get_deal($id,0);
		if(!$deal){
			$this->error ("借款不存在");
		}
		
		
		if($deal['is_effect']==1){
			$this->error ("请将借款设置为无效状态");
		}
		
		if($deal['ips_bill_no']!=""){
			$this->error ("第三方标无法拆分");
		}
		
		if($deal['deal_status']!=1){
			$this->error ("该借款当前状态不是进行中");
		}
		
		if($deal['load_money']==0){
			$this->error ("该借款还没人投标无法拆标");
		}
		
		$this->assign("deal",$deal);
		
		if(!in_array((int)to_date(TIME_UTC,"d"),array(29,30,31))){
			$NOW_TIME = to_date(TIME_UTC,"Y-m-d");
		}
		else{
			$NOW_TIME = to_date(next_replay_month(TIME_UTC),"Y-m")."-01";
		}
		
		$this->assign("NOW_TIME",$NOW_TIME);
		
		$html = $this->fetch();
		$this->success($html);
	}
	
	function do_apart(){
		require_once(APP_ROOT_PATH."app/Lib/common.php");
		require_once(APP_ROOT_PATH."app/Lib/deal.php");
		$id = intval($_REQUEST['id']);
		$deal = get_deal($id,0);
		
		if(!$deal){
			$this->error ("借款不存在");
		}
		
		
		if($deal['is_effect']==1){
			$this->error ("请将借款设置为无效状态");
		}
		
		if($deal['ips_bill_no']!=""){
			$this->error ("第三方标无法拆分");
		}
		
		if($deal['deal_status']!=1){
			$this->error ("该借款当前状态不是进行中");
		}
		
		if($deal['load_money']==0){
			$this->error ("该借款还没人投标无法拆标");
		}
		
		$data = $deal;
		
		//开始拆标
		$data['apart_borrow_amount'] = $deal['borrow_amount'];
		$data['borrow_amount'] = $deal['load_money'];
		$data['repay_start_time'] = TIME_UTC;
		$data['repay_start_date'] = to_date(TIME_UTC,"Y-m-d");
		$data['is_effect'] = 1;
		
		$GLOBALS['db']->autoExecute(DB_PREFIX."deal",$data,"UPDATE","id=".$id);
		if($GLOBALS['db']->affected_rows() == 0){
			$this->error("拆分失败");
			die();
		}
		
		syn_deal_status($id);
		
		$repay_start_time = strim($_REQUEST['repay_start_time']);
		
		$result = do_loans($id,$repay_start_time);
		
		if(($result['status'] == 2 || $result['status'] == 1) && (int)$_REQUEST['make_new'] == 1){
			$new_data = $deal;
			unset($new_data['id']);
			unset($new_data['deal_sn']);
			$new_data['is_effect'] = 0;
			$new_data['borrow_amount'] = $deal['borrow_amount']  -  $deal['load_money'];
			
			$GLOBALS['db']->autoExecute(DB_PREFIX."deal",$new_data,"INSERT");
			$new_id = $GLOBALS['db']->insert_id();
			if($new_id > 0){
				$deal_sn = "MER".to_date(TIME_UTC,"Y")."".str_pad($new_id,7,0,STR_PAD_LEFT);
				$GLOBALS['db']->query("update ".DB_PREFIX."deal SET deal_sn='".$deal_sn."' WHERE id=".$new_id);
				
				//更新城市
				$citys = M("DealCityLink")->where ("deal_id=".$id)->findAll();
				foreach($citys as $kk=>$vv){
					$new_city_link['deal_id'] = $new_id;
					$new_city_link['city_id'] = $vv['city_id'];
					$GLOBALS['db']->autoExecute(DB_PREFIX."deal_city_link",$new_city_link);
				}
			}
		}
		
		if($result['status'] == 2){
			ajax_return($result);
		}
		elseif($result['status'] == 1){
			$this->success("拆分成功");
		}
		else{
			$data['borrow_amount'] = $data['apart_borrow_amount'];
			$data['apart_borrow_amount'] = "";
			$data['repay_start_time'] = 0;
			$data['deal_status'] = 1;
			$data['is_effect'] = 0;
			
			$GLOBALS['db']->autoExecute(DB_PREFIX."deal",$deal,"UPDATE","id=".$id);
			syn_deal_status($id);
			
			$this->error($result['info']);
		}
	}
	
	public function restore() {
		//删除指定记录
		$ajax = intval($_REQUEST['ajax']);
		$id = $_REQUEST ['id'];
		if (isset ( $id )) {
				$condition = array ('id' => array ('in', explode ( ',', $id ) ) );
				$rel_data = M(MODULE_NAME)->where($condition)->findAll();				
				foreach($rel_data as $data)
				{
					$info[] = $data['name'];	
					rm_auto_cache("cache_deal_cart",array("id"=>$data['id']));					
				}
				if($info) $info = implode(",",$info);
				$list = M(MODULE_NAME)->where ( $condition )->setField ( 'is_delete', 0 );
				if ($list!==false) {
					
					M("Topic")->where(array ('fav_id' => array ('in', explode ( ',', $id ) ) ,"type"=>array('in',array("message","message_reply","deal_collect","deal_bad"))))->setField("is_effect",1);
										
					save_log($info.l("RESTORE_SUCCESS"),1);
					$this->success (l("RESTORE_SUCCESS"),$ajax);
				} else {
					save_log($info.l("RESTORE_FAILED"),0);
					$this->error (l("RESTORE_FAILED"),$ajax);
				}
			} else {
				$this->error (l("INVALID_OPERATION"),$ajax);
		}		
	}
	
	public function foreverdelete() {
		//彻底删除指定记录
		$ajax = intval($_REQUEST['ajax']);
		$id = $_REQUEST ['id'];
		if (isset ( $id )) {
				$condition = array ('id' => array ('in', explode ( ',', $id ) ) );
				//删除的验证
				if(M("DealOrder")->where(array ('deal_id' => array ('in', explode ( ',', $id ) ) ))->count()>0)
				{
					$this->error(l("DEAL_ORDER_NOT_EMPTY"),$ajax);
				}
				M("DealPayment")->where(array ('deal_id' => array ('in', explode ( ',', $id ) ) ))->delete();
				M("DealLoad")->where(array ('deal_id' => array ('in', explode ( ',', $id ) ) ))->delete();
				M("DealLoadRepay")->where(array ('deal_id' => array ('in', explode ( ',', $id ) ) ))->delete();
				M("DealRepay")->where(array ('deal_id' => array ('in', explode ( ',', $id ) ) ))->delete();
				M("DealCollect")->where(array ('deal_id' => array ('in', explode ( ',', $id ) ) ))->delete();
				M("Topic")->where(array ('fav_id' => array ('in', explode ( ',', $id ) ) ,"type"=>array('in',array("message","message_reply","deal_collect","deal_bad"))))->delete();
				M("DealCityLink")->where(array ('deal_id' => array ('in', explode ( ',', $id ) ) ))->delete();
				
				$rel_data = M(MODULE_NAME)->where($condition)->findAll();				
				foreach($rel_data as $data)
				{
					$info[] = $data['name'];	
				}
				if($info) $info = implode(",",$info);
				$list = M(MODULE_NAME)->where ( $condition )->delete();	
					
				if ($list!==false) {
					save_log($info.l("FOREVER_DELETE_SUCCESS"),1);
					$this->success (l("FOREVER_DELETE_SUCCESS"),$ajax);
				} else {
					save_log($info.l("FOREVER_DELETE_FAILED"),0);
					$this->error (l("FOREVER_DELETE_FAILED"),$ajax);
				}
			} else {
				$this->error (l("INVALID_OPERATION"),$ajax);
		}
	}
	
	
	public function set_sort()
	{
		$id = intval($_REQUEST['id']);
		$sort = intval($_REQUEST['sort']);
		$log_info = M(MODULE_NAME)->where("id=".$id)->getField('name');
		if(!check_sort($sort))
		{
			$this->error(l("SORT_FAILED"),1);
		}
		M(MODULE_NAME)->where("id=".$id)->setField("sort",$sort);
		rm_auto_cache("cache_deal_cart",array("id"=>$id));
		save_log($log_info.l("SORT_SUCCESS"),1);
		$this->success(l("SORT_SUCCESS"),1);
	}
	
	public function set_effect()
	{
		$id = intval($_REQUEST['id']);
		$ajax = intval($_REQUEST['ajax']);
		$info = M(MODULE_NAME)->where("id=".$id)->getField("name");
		$c_is_effect = M(MODULE_NAME)->where("id=".$id)->getField("is_effect");  //当前状态
		$n_is_effect = $c_is_effect == 0 ? 1 : 0; //需设置的状态
		M(MODULE_NAME)->where("id=".$id)->setField("is_effect",$n_is_effect);	
		M(MODULE_NAME)->where("id=".$id)->setField("update_time",TIME_UTC);	
		save_log($info.l("SET_EFFECT_".$n_is_effect),1);
		
		$this->ajaxReturn($n_is_effect,l("SET_EFFECT_".$n_is_effect),1)	;	
	}
	
	
	
	public function show_detail()
	{
		require_once(APP_ROOT_PATH."app/Lib/common.php");
		require_once(APP_ROOT_PATH."app/Lib/deal.php");
		$id = intval($_REQUEST['id']);
		syn_deal_status($id);
		$deal_info = M("Deal")->getById($id);
		$this->assign("deal_info",$deal_info);
		
		$true_repay_money  =  M("DealLoadRepay")->where("deal_id=".$id)->sum("repay_money");
		
		$this->assign("true_repay_money",floatval($true_repay_money) + 1);
		
		$count = D("DealLoad")->where('deal_id='.$id)->order("id ASC")->count();
		if (! empty ( $_REQUEST ['listRows'] )) {
			$listRows = $_REQUEST ['listRows'];
		} else {
			$listRows = '';
		}
		$p = new Page ( $count, $listRows );
		if($count>0){
			$loan_list = D("DealLoad")->where('deal_id='.$id)->order("id ASC")->limit($p->firstRow . ',' . $p->listRows)->findall();
			$this->assign("loan_list",$loan_list);
		}
		$page = $p->show();
		$this->assign ( "page", $page );
		
		$this->display();
	}
	
	public function filter_html()
	{
		$shop_cate_id = intval($_REQUEST['shop_cate_id']);
		$deal_id = intval($_REQUEST['deal_id']);
		$ids = $this->get_parent_ids($shop_cate_id);
		$filter_group = M("FilterGroup")->where(array("cate_id"=>array("in",$ids)))->findAll();
		foreach($filter_group as $k=>$v)
		{
			$filter_group[$k]['value'] = M("DealFilter")->where("filter_group_id = ".$v['id']." and deal_id = ".$deal_id)->getField("filter");
		}
		$this->assign("filter_group",$filter_group);
		$this->display();
	}
	
	//获取当前分类的所有父分类包含本分类的ID
	private $cate_ids = array();
	private function get_parent_ids($shop_cate_id)
	{
		$pid = $shop_cate_id;
		do{
			$pid = M("ShopCate")->where("id=".$pid)->getField("pid");
			if($pid>0)
			$this->cate_ids[] = $pid;
		}while($pid!=0);
		$this->cate_ids[] = $shop_cate_id;
		return $this->cate_ids;
	}
	
	
	public function load_user(){
		
		$return= array("status"=>0,"message"=>"");
		$id = intval($_REQUEST['id']);
		if($id==0){
			ajax_return($return);
			exit();
		}
		$user = $GLOBALS['db']->getRow("SELECT u.*,l.name,l.point as l_point,l.services_fee,u.view_info,enddate FROM ".DB_PREFIX."user u LEFT JOIN ".DB_PREFIX."user_level l ON u.level_id = l.id WHERE u.id=".$id);
		if(!$user){
			ajax_return($return);
			exit();
		}
		$user['old_imgdata_str'] = unserialize($user['view_info']);
		$return['status']=1;
		$return['user']=$user;
		ajax_return($return);
		exit();
	}

	
	/*
	 *回款计划
	 */
	public function repay_plan()
	{
		require_once(APP_ROOT_PATH."app/Lib/common.php");
		require_once(APP_ROOT_PATH."app/Lib/deal.php");
		$id = intval($_REQUEST['id']);
		
		if($id==0){
			$this->success("数据错误");
		}
		$deal_info = get_deal($id);
		
		if(!$deal_info){
			$this->success("借款不存在");
		}
	
		$this->assign("deal_info",$deal_info);
		$repay_list  = get_deal_load_list($deal_info);
		
		if(!$repay_list){
			$this->success("无还款信息");
		}
		
		foreach($repay_list as $k=>$v){
			$repay_list[$k]['idx'] = $k + 1;
		}
		$this->assign("repay_list",$repay_list);
		$this->assign("deal_id",$id);
		$this->assign("deal_info",$deal_info);
		$this->display();
	}
	
	

	function repay_plan_a(){
		$deal_id = intval($_REQUEST['deal_id']);
		$l_key = intval($_REQUEST['l_key']);
		$obj = strim($_REQUEST['obj']);
		
		if($deal_id==0){
			$this->error("数据错误");
		}
		require_once(APP_ROOT_PATH."app/Lib/common.php");
		require_once(APP_ROOT_PATH."app/Lib/deal.php");
		
		$deal_info = get_deal($deal_id);
	
		if(!$deal_info){
			$this->error("借款不存在");
		}
	
	
		//输出投标列表
		$page = intval($_REQUEST['p']);
		if($page==0)
			$page = 1;
		
		$page_size = 10;
		
		$limit = (($page-1)*$page_size).",".$page_size;
		
		$result = get_deal_user_load_list($deal_info,0,$l_key,-1,0,0,1,$limit);

		foreach ($result['item'] as $k=>$v)
		{
			$result['item'][$k]['interest_money']=format_price($v['interest_money']);
		}
		
		$rs_count = $result['count'];
		$page_all = ceil($rs_count/$page_size);
	
		$this->assign("load_user",$result['item']);
		$this->assign("l_key",$l_key);
		$this->assign("page_all",$page_all);
		$this->assign("rs_count",$rs_count);
		$this->assign("page",$page);
		$this->assign("deal_id",$deal_id);
		
		$this->assign("obj",$obj);
		$this->assign("page_prev",$page - 1);
		$this->assign("page_next",$page + 1);
		$html = $this->fetch();
		
		$this->success($html);
	}
	
	
	/**
	 * 代还款
	 */
	 
	 function do_site_repay($page=1){
	 	require_once(APP_ROOT_PATH."app/Lib/common.php");
		require_once(APP_ROOT_PATH."app/Lib/deal.php");
		$id = intval($_REQUEST['id']);
		$l_key = intval($_REQUEST['l_key']);
		
		$this->assign("jumpUrl",U("Deal/repay_plan",array("id"=>$id)));
		
		if($id==0){
			$this->success("数据错误");
		}
		$deal_info = get_deal($id);
		
		if(!$deal_info){
			$this->success("借款不存在");
		}
		
		if($deal_info['ips_bill_no'] !=""){
			$this->success("第三方同步暂无法代还款");
		}
		
		if($page==0)
			$page = 1;
		
		$page_size = 10;
		
		$limit = (($page-1)*$page_size).",".$page_size;
		
		$user_loan_list = get_deal_user_load_list($deal_info,  0 , $l_key , -1 , 0 , 0 , 1 , $limit);
		$rs_count = $user_loan_list['count'];
		
		$page_all = ceil($rs_count/$page_size);
		
		require_once(APP_ROOT_PATH."system/libs/user.php");
		foreach($user_loan_list['item'] as $kk=>$vv){
			if($vv['has_repay']==0 ){//借入者已还款，但是没打款到借出用户中心
				$user_load_data = array();

				$user_load_data['true_repay_time'] = TIME_UTC;
				$user_load_data['true_repay_date'] = to_date(TIME_UTC);
				$user_load_data['is_site_repay'] = 1;
				$user_load_data['status'] = 0;
				
				$user_load_data['true_self_money'] = (float)$vv['self_money'];
				$user_load_data['true_repay_money'] = (float)$vv['month_repay_money'];
				$user_load_data['true_interest_money'] = (float)$user_load_data['true_repay_money'] - (float)$user_load_data['true_self_money'];
				$user_load_data['true_manage_money'] = (float)$vv['month_manage_money'];
				$user_load_data['impose_money'] = (float)$vv['impose_money'];
				$user_load_data['repay_manage_impose_money'] = (float)$vv['repay_manage_impose_money'];
				
				if($vv['status']>0)
					$user_load_data['status'] = $vv['status'] - 1;
					
				$user_load_data['has_repay'] = 1;
				$GLOBALS['db']->autoExecute(DB_PREFIX."deal_load_repay",$user_load_data,"UPDATE","id=".$vv['id']." AND has_repay = 0 ","SILENT");
			
				if($GLOBALS['db']->affected_rows() > 0){
	
					//$content = "您好，您在".app_conf("SHOP_TITLE")."的投标 “<a href=\"".$deal_info['url']."\">".$deal_info['name']."</a>”成功还款".($user_load_data['true_repay_money']+$user_load_data['impose_money'])."元";
					$unext_loan = $user_loan_list[$vv['u_key']][$kk+1];
						
					if($unext_loan){
						//$content .= "，本笔投标的下个还款日为".to_date($unext_loan['repay_day'],"Y年m月d日")."，需还本息".number_format($unext_loan['month_repay_money'],2)."元。";
						$notices['content'] =",本笔投标的下个还款日为".to_date($unext_loan['repay_day'],"Y年m月d日")."，需还本息".number_format($unext_loan['month_repay_money'],2)."元。";
					}
					else{
						$load_repay_rs = $GLOBALS['db']->getOne("SELECT (sum(true_interest_money) + sum(impose_money)) as shouyi,sum(impose_money) as total_impose_money FROM ".DB_PREFIX."deal_load_repay WHERE deal_id=".$deal_info['id']." AND user_id=".$vv['user_id']);
						$all_shouyi_money= number_format($load_repay_rs['shouyi'],2);
						$all_impose_money = number_format($load_repay_rs['total_impose_money'],2);
						//$content .= "，本次投标共获得收益:".$all_shouyi_money."元,其中违约金为:".$all_impose_money."元,本次投标已回款完毕！";
						$notices['content'] =",本次投标共获得收益:".$all_shouyi_money."元,其中违约金为:".$all_impose_money."元,本次投标已回款完毕！";
					}
					if($user_load_data['impose_money'] !=0 || $user_load_data['true_manage_money'] !=0 || $user_load_data['true_repay_money']!=0){
						$in_user_id  = $vv['user_id'];
						//如果是转让债权那么将回款打入转让者的账户
						if((int)$vv['t_user_id'] == 0){
							$loan_user_info['user_name'] = $vv['user_name'];
							$loan_user_info['email'] = $vv['email'];
							$loan_user_info['mobile'] = $vv['mobile'];
						}
						else{
							$in_user_id = $vv['t_user_id'];
							$loan_user_info['user_name'] = $vv['t_user_name'];
							$loan_user_info['email'] = $vv['t_email'];
							$loan_user_info['mobile'] = $vv['t_mobile'];
						}
	
						//更新用户账户资金记录
						modify_account(array("money"=>$user_load_data['true_repay_money']),$in_user_id,"[<a href='".$deal_info['url']."' target='_blank'>".$deal_info['name']."</a>],第".($kk+1)."期,回报本息",5);
						
						modify_account(array("money"=>-$user_load_data['true_manage_money']),$in_user_id,"[<a href='".$deal_info['url']."' target='_blank'>".$deal_info['name']."</a>],第".($kk+1)."期,投标管理费",20);
						
						if($user_load_data['impose_money']!=0)
							modify_account(array("money"=>$user_load_data['impose_money']),$in_user_id,"[<a href='".$deal_info['url']."' target='_blank'>".$deal_info['name']."</a>],第".($kk+1)."期,逾期罚息",21);
						
						
						
						$msg_conf = get_user_msg_conf($in_user_id);
	
	
						//短信通知
						if(app_conf("SMS_ON")==1&&app_conf('SMS_REPAY_TOUSER_ON')==1){
							
							$tmpl = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."msg_template where name = 'TPL_DEAL_LOAD_REPAY_SMS'",false);
							$tmpl_content = $tmpl['content'];
								
							$notice['user_name'] = $loan_user_info['user_name'];
							$notice['deal_name'] = $deal_info['sub_name'];
							$notice['deal_url'] = $deal_info['url'];
							$notice['site_name'] = app_conf("SHOP_TITLE");
							$notice['repay_money'] = number_format(($user_load_data['true_repay_money']+$user_load_data['impose_money']),2);
							if($unext_loan){
								$notice['need_next_repay'] = $unext_loan;
								$notice['next_repay_time'] = to_date($unext_loan['repay_day'],"Y年m月d日");
								$notice['next_repay_money'] = number_format($unext_loan['month_repay_money'],2);
							}
							else{
								$notice['all_repay_money'] = $all_shouyi_money;
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
						$notices['shop_title'] = app_conf("SHOP_TITLE");
						$notices['url'] = "“<a href=\"".$deal_info['url']."\">".$deal_info['name']."</a>”";
						$notices['money'] = ($user_load_data['true_repay_money']+$user_load_data['impose_money']);
										
						$tmpl_content = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."msg_template where name = 'TPL_SITE_REPAY'",false);
						$GLOBALS['tmpl']->assign("notice",$notices);
						$content = $GLOBALS['tmpl']->fetch("str:".$tmpl_content['content']);
						
						if($msg_conf['sms_bidrepaid']==1)
							send_user_msg("",$content,0,$in_user_id,TIME_UTC,0,true,9);
						
						//邮件
						if($msg_conf['mail_bidrepaid']==1 && app_conf('MAIL_ON')==1){
							
							$tmpl = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."msg_template where name = 'TPL_DEAL_LOAD_REPAY_EMAIL'",false);
							$tmpl_content = $tmpl['content'];
								
							$notice['user_name'] = $loan_user_info['user_name'];
							$notice['deal_name'] = $deal_info['sub_name'];
							$notice['deal_url'] = $deal_info['url'];
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
								$notice['all_repay_money'] = $all_shouyi_money;
								$notice['impose_money'] = $all_impose_money;
							}
								
							$GLOBALS['tmpl']->assign("notice",$notice);
								
							$msg = $GLOBALS['tmpl']->fetch("str:".$tmpl_content);
							$msg_data['dest'] = $loan_user_info['email'];
							$msg_data['send_type'] = 1;
							$msg_data['title'] = "“".$deal_info['name']."”回款通知";
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
		
		if($page >= $page_all){
			$s_count =  $GLOBALS['db']->getOne("SELECT count(*) FROM  ".DB_PREFIX."deal_load_repay where deal_id=".$id." AND l_key=".$l_key." and has_repay = 0");
			$adm_session = es_session::get(md5(conf("AUTH_KEY")));
			if($s_count == 0){
				
				$rs_sum = $GLOBALS['db']->getRow("SELECT sum(true_repay_money) as total_repay_money,sum(true_self_money) as total_self_money,sum(true_interest_money) as total_interest_money,sum(true_manage_money) as total_manage_money,sum(impose_money) as total_impose_money,sum(repay_manage_impose_money) as total_repay_manage_impose_money FROM  ".DB_PREFIX."deal_load_repay where deal_id=".$id." AND l_key=".$l_key."  and has_repay = 1");
				
				$deal_load_list = get_deal_load_list($deal_info);
				
				//统计网站代还款
				$rs_site_sum = $GLOBALS['db']->getRow("SELECT sum(true_repay_money) as total_repay_money,sum(true_self_money) as total_self_money,sum(true_repay_manage_money) as total_manage_money,sum(impose_money) as total_impose_money,sum(repay_manage_impose_money) as total_repay_manage_impose_money FROM  ".DB_PREFIX."deal_load_repay where deal_id=".$id." AND l_key=".$l_key." and is_site_repay=1 and has_repay = 1");
				
				$repay_data['status'] = (int)$deal_load_list[$l_key]['status'] - 1;
				$repay_data['true_repay_time'] = TIME_UTC;
				$repay_data['true_repay_date'] = to_date(TIME_UTC);
				$repay_data['has_repay'] = 1;
				$repay_data['impose_money'] = floatval($rs_sum['total_impose_money']);
				$repay_data['true_self_money'] = floatval($rs_sum['total_self_money']);
				$repay_data['true_repay_money'] = floatval($rs_sum['total_repay_money']);
				$repay_data['true_manage_money'] = floatval($rs_sum['total_manage_money']);
				$repay_data['true_interest_money'] = floatval($rs_sum['total_interest_money']);
				$repay_data['manage_impose_money'] = floatval($rs_sum['total_repay_manage_impose_money']);
				
				
				$GLOBALS['db']->autoExecute("UPDATE ".DB_PREFIX."deal_repay",$repay_data,"UPDATE"," deal_id=".$id." AND l_key=".$l_key." and has_repay = 0 ");
				
				if($rs_site_sum){
					$r_msg = "网站代还款";
					if($rs_site_sum['total_repay_money'] > 0){
						$r_msg .=",本息：".format_price($rs_site_sum['total_repay_money']);
					}
					if($rs_site_sum['total_impose_money'] > 0){
						$r_msg .=",逾期费用：".format_price($rs_site_sum['total_impose_money']);
					}
					if($rs_site_sum['total_manage_money'] > 0){
						$r_msg .=",管理费：".format_price($rs_site_sum['total_manage_money']);
					}
					if($rs_site_sum['total_repay_manage_impose_money'] > 0){
						$r_msg .=",逾期管理费：".format_price($rs_site_sum['total_repay_manage_impose_money']);
					}
					repay_log($deal_load_list[$l_key]['repay_id'],$r_msg,0,$adm_session['adm_id']);
				}
				
				if($GLOBALS['db']->getOne("SELECT count(*) FROM ".DB_PREFIX."generation_repay WHERE deal_id=".$id." AND repay_id=".$deal_load_list[$l_key]['repay_id']."")==0){
					$generation_repay['deal_id'] = $id;
					$generation_repay['repay_id'] = $deal_load_list[$l_key]['repay_id'];
					
					$generation_repay['admin_id'] = $adm_session['adm_id'];
					$generation_repay['agency_id'] = $deal_info['agency_id'];
					$generation_repay['repay_money'] = $rs_site_sum['total_repay_money'];
					$generation_repay['self_money'] = $rs_site_sum['total_self_money'];
					$generation_repay['impose_money'] = $rs_site_sum['total_impose_money'];
					$generation_repay['manage_money'] = $rs_site_sum['total_manage_money'];
					$generation_repay['manage_impose_money'] = $rs_site_sum['total_repay_manage_impose_money'];
					$generation_repay['create_time'] = TIME_UTC;
					
					$GLOBALS['db']->autoExecute(DB_PREFIX."generation_repay",$generation_repay);
				}
				
			}
			
			$this->success("代还款执行完毕!");
		}
		else{
			register_shutdown_function(array(&$this, 'do_site_repay'), $page+1);
		}
	 }
	
	 
	 /**
	  * 待收款
	  */
	 function user_loads_repay(){
	 	$this->assign("main_title","收款信息");
		
		if (isset ( $_REQUEST ['_order'] )) {
			$sorder = $_REQUEST ['_order'];
		}
		else{
			$sorder = "id";
		}
		switch($sorder){
			case "name":
			case "user_name":
					$order ="dlr.user_id";
				break;
			case "l_key_index":
					$order ="dlr.l_key_index";
				break;
			case "repay_money_format":
					$order ="dlr.repay_money";
				break;
			case "yuqi_money":
					$order ="(dlr.repay_money + dlr.impose_money)";
				break;
			case "shiji_money":
					$order ="(dlr.repay_money + dlr.impose_money)";
				break;
			case "repay_time":
					$order ="dlr.repay_time";
				break;
			case "status_format":
				$order ="dlr.status";
				break;
		
			default : 
				$order =$sorder;
				break;
		}
		//排序方式默认按照倒序排列
		//接受 sost参数 0 表示倒序 非0都 表示正序
		if (isset ( $_REQUEST ['_sort'] )) {
			$sort = $_REQUEST ['_sort'] ? 'asc' : 'desc';
		}
		else{
			$sort = "desc";
		}
		
		$condition = " 1= 1 ";
		
		
		
		if(isset($_REQUEST['status'])){
			$status = intval($_REQUEST['status']);
			if($status >1){
				$condition .= " AND status=".($status-1);
			}else{
				if($status==1){
				$condition .= " AND status=0 and has_repay=0";}
				else {
					$condition .= " AND status=0";
				}
			}
		}
		
		
		
		$begin_time  = trim($_REQUEST['begin_time']) =="" ? 0 : to_timespan($_REQUEST['begin_time'],"Y-m-d");
		$end_time  = trim($_REQUEST['end_time']) =="" ? 0 : to_timespan($_REQUEST['end_time'],"Y-m-d");
		
		
		if($begin_time > 0 || $end_time > 0){
			if($end_time==0)
			{
				$condition .= " and dlr.repay_time >= $begin_time ";	
			}
			else
				$condition .= " and dlr.repay_time between  $begin_time and $end_time ";	
		}
		if(trim($_REQUEST['name'])!='')
		{
			$condition .= " and d.name like '%".trim($_REQUEST['name'])."%'";
		}
		
		$sql_count = "SELECT count(*) FROM ".DB_PREFIX."deal_load_repay dlr LEFT JOIN ".DB_PREFIX."deal d On d.id = dlr.deal_id where $extWhere $condition";
		$rs_count = $GLOBALS['db']->getOne($sql_count);
		
		$list = array();
		if($rs_count > 0){
			if (! empty ( $_REQUEST ['listRows'] )) {
				$listRows = $_REQUEST ['listRows'];
			} else {
				$listRows = '';
			}
			$p = new Page ( $rs_count, $listRows );
			
			$sql_list =  "SELECT dlr.*,dlr.l_key +1 as l_key_index ,d.name FROM ".DB_PREFIX."deal_load_repay dlr LEFT JOIN ".DB_PREFIX."deal d On d.id = dlr.deal_id WHERE $condition  ORDER BY $order $sort LIMIT ".$p->firstRow . ',' . $p->listRows;
		
			$list = $GLOBALS['db']->getAll($sql_list);
			
			foreach($list as $k=>$v){
				$list[$k]['l_key_index'] = "第 ".$v['l_key_index']." 期";
				$list[$k]['user_name'] = $GLOBALS['db']->getOne("select user_name from ".DB_PREFIX."user where id=".$v['user_id']);
				//状态
				if($v['has_repay'] == 0){
					$list[$k]['status_format'] = '待还';
				}elseif($v['status'] == 0){
					$list[$k]['status_format'] = '提前还款';
				}elseif($v['status'] == 1){
					$list[$k]['status_format'] = '准时还款';
				}elseif($v['status'] == 2){
					$list[$k]['status_format'] = '逾期还款';
				}elseif($v['status'] == 3){
					$list[$k]['status_format'] = '严重逾期';
				}
				$list[$k]['yuqi_money'] = format_price($v['repay_money']-$v['self_money']);
				if($list[$k]['has_repay'] == 1){
					$list[$k]['shiji_money'] = format_price($v['repay_money']-$v['self_money']+$v['impose_money']);
				}else {$list[$k]['shiji_money'] = 0;}
				$list[$k]['repay_money_format'] = format_price($v['repay_money']);
			}
			
			$page = $p->show();
			$this->assign ( "page", $page );
			
		}
		
		$sortImg = $sort; //排序图标
		$sortAlt = $sort == 'desc' ? l("ASC_SORT") : l("DESC_SORT"); //排序提示
		$sort = $sort == 'desc' ? 1 : 0; //排序方式
		
		$this->assign ( 'sort', $sort );
		$this->assign ( 'order', $sorder );
		$this->assign ( 'sortImg', $sortImg );
		$this->assign ( 'sortType', $sortAlt );
		
		$this->assign ( 'list', $list );
		
		$this->display ();
	 }
	 
	 
	/**
	 * 放款
	 */
	function do_loans(){
		$id = intval($_REQUEST['id']);
		$repay_start_time = strim($_REQUEST['repay_start_time']);
		
		require_once(APP_ROOT_PATH."app/Lib/common.php");
		$result = do_loans($id,$repay_start_time);
		
		if($result['status'] == 2){
			ajax_return($result);
		}
		elseif($result['status'] == 1){
			$this->success($result['info']);
		}
		else
			$this->error($result['info']);
	}
	
	/**
	 * 流标返还
	 */
	function do_received(){
		$id = intval($_REQUEST['id']);
		$bad_msg = strim($_REQUEST['bad_msg']);
		
		require_once(APP_ROOT_PATH."app/Lib/common.php");
		$result = do_received($id,0,$bad_msg);
		if($result['status'] == 2){
			ajax_return($result);
		}
		elseif($result['status']==1){
			$this->success($result['info']);
		}
		else{
			$this->error($result['info']);
		}
	}
	
	function do_export_load($page = 1)
	{	
		
		$id = intval($_REQUEST['id']);
		set_time_limit(0);
		$limit = (($page - 1)*intval(app_conf("BATCH_PAGE_SIZE"))).",".(intval(app_conf("BATCH_PAGE_SIZE")));
		$list = M("DealLoad")->limit($limit)->where('deal_id ='.$id)->findAll();
	
		if($list)
		{
			register_shutdown_function(array(&$this, 'do_export_load'), $page+1);
				
			$user_value = array('id'=>'""','user_name'=>'""','money'=>'""','create_time'=>'""','is_repay'=>'""','is_has_loans'=>'""','msg'=>'""');
			if($page == 1)
				$content = iconv("utf-8","gbk","编号,投标人,投标金额,投标时间,流标返还,是否转账,转账备注");
	
			if($page==1)
				$content = $content . "\n";
	
			foreach($list as $k=>$v)
			{
				$user_value = array();
				$user_value['id'] = iconv('utf-8','gbk','"' . $v['id'] . '"');
				$user_value['user_name'] = iconv('utf-8','gbk','"' . $v['user_name'] . '"');
				$user_value['money'] = iconv('utf-8','gbk','"' . $v['money'] . '"');
				$user_value['create_time'] = iconv('utf-8','gbk','"' . to_date($v['create_time']) . '"');
				
				$user_value['is_repay'] = iconv('utf-8','gbk','"'.($v['is_repay']==0 ? "否" : "是").'"');
							
				$user_value['is_has_loans'] = iconv('utf-8','gbk','"'.($v['is_has_loans']==0 ? "否" : "是").'"');
				$user_value['msg'] = iconv('utf-8','gbk','"' . $v['msg'] . '"');
	
	
				$content .= implode(",", $user_value) . "\n";
			}
				
				
			header("Content-Disposition: attachment; filename=user_deal_list.csv");
			echo $content;
		}
		else
		{
			if($page==1)
				$this->error(L("NO_RESULT"));
		}
	
	}
	

	function do_allrepay_plan_export_load($page = 1)
	{
		$id = intval($_REQUEST['id']);
		set_time_limit(0);
		$limit = (($page - 1)*intval(app_conf("BATCH_PAGE_SIZE"))).",".(intval(app_conf("BATCH_PAGE_SIZE")));
	
		require_once(APP_ROOT_PATH."app/Lib/common.php");
		require_once(APP_ROOT_PATH."app/Lib/deal.php");
		$deal_info = get_deal($id);
		$contents = "";
		if($page==1){
			$repay_list  = get_deal_load_list($deal_info);
			if($repay_list)
			{	register_shutdown_function(array(&$this, 'do_allrepay_plan_export_load'), $page+1);
				$repay_plan_value_s = array('l_key'=>'""','repay_day_format'=>'""','month_has_repay_money_all_format'=>'""','month_need_all_repay_money_format'=>'""','month_need_all_repay_money'=>'""','month_repay_money_format'=>'""','month_manage_money_format'=>'""','impose_money_format'=>'""','status_format');
				if($page==1)
					$contents = iconv("utf-8","gbk","借款期数,还款日,已还金额,待还金额,还需还金额,到期应还本息,到期应还本息管理费,逾期费用,还款情况");
		
				if($page==1)
					$contents = $contents . "\n";
				
				foreach($repay_list as $k=>$v)
				{
				$repay_plan_value_s = array();
				$repay_plan_value_s['l_key'] = iconv('utf-8','gbk','"' . ($v['l_key'] + 1) . '"');
				$repay_plan_value_s['repay_day_format'] = iconv('utf-8','gbk','"' . $v['repay_day_format'] . '"');
				$repay_plan_value_s['month_has_repay_money_all_format'] = iconv('utf-8','gbk','"' . $v['month_has_repay_money_all_format'] . '"');
				$repay_plan_value_s['month_need_all_repay_money_format'] = iconv('utf-8','gbk','"' . $v['month_need_all_repay_money_format'] . '"');
				$repay_plan_value_s['month_need_all_repay_money'] = iconv('utf-8','gbk','"'. $v['month_need_all_repay_money_format'] .'"');
				$repay_plan_value_s['month_repay_money_format'] = iconv('utf-8','gbk','"'. $v['month_repay_money_format'] .'"');
				$repay_plan_value_s['month_manage_money_format'] = iconv('utf-8','gbk','"' . $v['month_manage_money_format'] . '"');
				$repay_plan_value_s['impose_money_format'] = iconv('utf-8','gbk','"' . $v['impose_money_format'] . '"');
				$repay_plan_value_s['status_format'] = iconv('utf-8','gbk','"' . $v['status_format'] . '"');
				$contents .= implode(",", $repay_plan_value_s) . "\n";
				}
		
			}
		}
		else
		{
			if($page==1)
				$this->error(L("NO_RESULT"));
		}
		
		
		header("Content-Disposition: attachment; filename=repay_plan_list.csv");
		echo $contents;
				
	
	
	
	}
	
	function do_repay_plan_export_load($page = 1)
	{
		$pages=1;
		$id = intval($_REQUEST['id']);
		$l_key = intval($_REQUEST['l_key']);
		set_time_limit(0);
		$limit = (($page - 1)*intval(app_conf("BATCH_PAGE_SIZE"))).",".(intval(app_conf("BATCH_PAGE_SIZE")));
		
		require_once(APP_ROOT_PATH."app/Lib/common.php");
		require_once(APP_ROOT_PATH."app/Lib/deal.php");
		$deal_info = get_deal($id);
		$content = "";
		$contents = "";
		if($page==1){
			$repay_list  = get_deal_load_list($deal_info);
			
			if($repay_list)
			{
				$repay_plan_value_s = array('l_key'=>'""','repay_day_format'=>'""','month_has_repay_money_all_format'=>'""','month_need_all_repay_money_format'=>'""','month_need_all_repay_money'=>'""','month_repay_money_format'=>'""','month_manage_money_format'=>'""','impose_money_format'=>'""','status_format');
				if($page==1)
				$contents = iconv("utf-8","gbk","借款期数,还款日,已还金额,待还金额,还需还金额,到期应还本息,到期应还本息管理费,逾期费用,还款情况");
				
				if($page==1)
				$contents = $contents . "\n";
				$repay_plan_value_s = array();
				$repay_plan_value_s['l_key'] = iconv('utf-8','gbk','"' . ($repay_list[$l_key]['l_key'] + 1) . '"');
				$repay_plan_value_s['repay_day_format'] = iconv('utf-8','gbk','"' . $repay_list[$l_key]['repay_day_format'] . '"');
				$repay_plan_value_s['month_has_repay_money_all_format'] = iconv('utf-8','gbk','"' . $repay_list[$l_key]['month_has_repay_money_all_format'] . '"');
				$repay_plan_value_s['month_need_all_repay_money_format'] = iconv('utf-8','gbk','"' . $repay_list[$l_key]['month_need_all_repay_money_format'] . '"');
				
				$repay_plan_value_s['month_need_all_repay_money'] = iconv('utf-8','gbk','"' . $repay_list[$l_key]['month_need_all_repay_money_format'] . '"');
				$repay_plan_value_s['month_repay_money_format'] = iconv('utf-8','gbk','"' . $repay_list[$l_key]['month_repay_money_format'] . '"');
				$repay_plan_value_s['month_manage_money_format'] = iconv('utf-8','gbk','"' . $repay_list[$l_key]['month_manage_money_format'] . '"');
				$repay_plan_value_s['impose_money_format'] = iconv('utf-8','gbk','"' . $repay_list[$l_key]['impose_money_format'] . '"');
				
				$repay_plan_value_s['status_format'] = iconv('utf-8','gbk','"' . $repay_list[$l_key]['status_format'] . '"');
				$contents .= implode(",", $repay_plan_value_s) . "\n";
				
			}
		}
		
		
			
		$sqll = array(deal_id=>$id, l_key=>$l_key);
		
		$deal_info = get_deal($sqll['deal_id']);//($deal_info,0,$l_key,-1,0,0,1,$limit)
		$listss = get_deal_user_load_list($deal_info,0,$sqll['l_key'],-1,0,0,1,$limit);// get_deal_load_list($deal_info);
		
		foreach ($listss['item'] as $k=>$v)
		{
			$listss['item'][$k]['yuqi_money']=format_price($v['month_repay_money']-$v['self_money']);
		}
		$lists = $listss['item'];
	
		if($lists)
		{
			register_shutdown_function(array(&$this, 'do_repay_plan_export_load'), $page+1);
			$repay_plan_value = array('id'=>'""','user_name'=>'""','month_repay_money_format'=>'""','impose_money_format'=>'""','yuqi_money'=>'""','status_format'=>'""','site_repay_format'=>'""');
			if($page == 1)
			{
				$content = iconv("utf-8","gbk","借款编号,会员,还款金额,逾期罚息,预期收益,状态,还款人");}
	
			if($page==1)
				$content = $content . "\n";
	
			foreach($lists as $k=>$v)
			{
				$repay_plan_value = array();
				$repay_plan_value['id'] = iconv('utf-8','gbk','"' . $v['id'] . '"');
				$repay_plan_value['user_name'] = iconv('utf-8','gbk','"' . $v['user_name'] . '"');
				$repay_plan_value['month_repay_money_format'] = iconv('utf-8','gbk','"' . $v['month_repay_money_format'] . '"');
				$repay_plan_value['impose_money_format'] = iconv('utf-8','gbk','"' .$v['impose_money_format']. '"');
				$repay_plan_value['yuqi_money'] = iconv('utf-8','gbk','"'.$v['yuqi_money'].'"');
				$repay_plan_value['status_format'] = iconv('utf-8','gbk','"' .$v['status_format']. '"');
				$repay_plan_value['site_repay_format'] = iconv('utf-8','gbk','"' .$v['site_repay_format']. '"');
				$content .= implode(",", $repay_plan_value) . "\n";
			}
			
		}
		else
		{
			if($page==1)
				$this->error(L("NO_RESULT"));
		}
		
		
		header("Content-Disposition: attachment; filename=repay_plan_list_one.csv");
		echo $contents;
		echo $content;
		
	}
	
	public function export_csv_three($page = 1)
	{
		set_time_limit(0);
		$limit = (($page - 1)*intval(app_conf("BATCH_PAGE_SIZE"))).",".(intval(app_conf("BATCH_PAGE_SIZE")));
	
		if (isset ( $_REQUEST ['_order'] )) {
			$sorder = $_REQUEST ['_order'];
		}
		else{
			$sorder = "id";
		}
	
		switch($sorder){
			case "name":
			case "cate_id":
				$order ="d.".$sorder;
				break;
			case "has_repay_status":
				$order ="dl.status";
				break;
			case "site_bad_status":
				$order ="dl.is_site_bad";
				break;
			case "is_has_send":
				$order ="d.send_three_msg_time";
				break;
			case "l_key_index":
				$order ="dl.l_key";
				break;
			default :
				$order ="dl.".$sorder;
				break;
		}
	
		//排序方式默认按照倒序排列
		//接受 sost参数 0 表示倒序 非0都 表示正序
		if (isset ( $_REQUEST ['_sort'] )) {
			$sort = $_REQUEST ['_sort'] ? 'asc' : 'desc';
		}
		else{
			$sort = "ASC";
		}
	
		//开始加载搜索条件
		$condition =" 1=1 ";
	
		$status = intval($_REQUEST['status']);
	
		if($status >0){
			if(($status-1)==0)
				$condition .= " AND dl.has_repay=0 ";
			else
				$condition .= " AND dl.has_repay=1 and dl.status=".($status-2);
		}
		$deal_status = intval($_REQUEST['deal_status']);
		if($deal_status >0){
			$condition .= " AND dl.is_site_bad=".($deal_status-1);
		}
	
		$begin_time  = !isset($_REQUEST['begin_time'])? to_timespan(to_date(TIME_UTC ,"Y-m-d"),"Y-m-d")  : (trim($_REQUEST['begin_time']) =="" ? 0 : to_timespan($_REQUEST['begin_time'],"Y-m-d"));
		$end_time  = !isset($_REQUEST['end_time'])?to_timespan(to_date(TIME_UTC ,"Y-m-d"),"Y-m-d") + 3*24*3600: (trim($_REQUEST['end_time']) =="" ? 0 : to_timespan($_REQUEST['end_time'],"Y-m-d"));
		if($begin_time > 0 || $end_time > 0){
			if($end_time==0)
			{
				$condition .= " and dl.repay_time >= $begin_time ";
			}
			else
				$condition .= " and dl.repay_time between  $begin_time and $end_time ";
		}
	
		$_REQUEST['begin_time'] = to_date($begin_time ,"Y-m-d");
		$_REQUEST['end_time'] = to_date($end_time ,"Y-m-d");
	
		if(trim($_REQUEST['name'])!='')
		{
			$condition .= " and d.name like '%".trim($_REQUEST['name'])."%'";
		}
	
		if(trim($_REQUEST['user_name'])!='')
		{
			$condition .= " and dl.user_id in (select id from  ".DB_PREFIX."user WHERE user_name='".trim($_REQUEST['user_name'])."')";
		}
	
		if(intval($_REQUEST['cate_id'])>0)
		{
			require_once APP_ROOT_PATH."system/utils/child.php";
			$child = new Child("deal_cate");
			$cate_ids = $child->getChildIds(intval($_REQUEST['cate_id']));
			$cate_ids[] = intval($_REQUEST['cate_id']);
			$condition .=" and d.cate_id in (".implode(",",$cate_ids).") ";
		}
	
		$sql_list =  " SELECT dl.*,dl.l_key + 1 as l_key_index,d.name,d.cate_id,d.send_three_msg_time,u.user_name,u.mobile FROM ".DB_PREFIX."deal_repay dl LEFT JOIN ".DB_PREFIX."deal d  ON d.id=dl.deal_id left join ".DB_PREFIX."user u on u.id=dl.user_id WHERE $condition  ORDER BY $order $sort LIMIT ".$limit;
		$list = $GLOBALS['db']->getAll($sql_list);
			
		foreach($list as $k=>$v){
			$list[$k]['l_key_index'] = "第 ".$v['l_key_index']." 期";
			if($v['send_three_msg_time'] == $v['repay_time']){
				$list[$k]['is_has_send'] = "已发送";
			}
			else{
				$list[$k]['is_has_send'] = "未发送";
			}
			if($v['has_repay']==1){
				switch($v['status']){
					case 0;
					$list[$k]['has_repay_status'] = "提前还款";
					break;
					case 1;
					$list[$k]['has_repay_status'] = "准时还款";
					break;
					case 2;
					$list[$k]['has_repay_status'] = "逾期还款";
					break;
					case 3;
					$list[$k]['has_repay_status'] = "严重逾期";
					break;
				}
			}
			else{
				$list[$k]['has_repay_status'] = "未还";
			}
	
			if($v['is_site_bad'] == 1){
				$list[$k]['site_bad_status'] = "坏账";
			}
			else{
				$list[$k]['site_bad_status'] = "正常";
			}
		}
	
		if($list)
		{
			register_shutdown_function(array(&$this, 'export_csv_three'), $page+1);
	
			$three_value = array('id'=>'""','name'=>'""','l_key_index'=>'""','user_name'=>'""','mobile'=>'""','repay_money'=>'""','manage_money'=>'""','impose_money'=>'""','manage_impose_money'=>'""','repay_time'=>'""','cate_id'=>'""','has_repay_status'=>'""','site_bad_status'=>'""','is_has_send'=>'""');
			if($page == 1)
				$content = iconv("utf-8","gbk","编号,贷款名称,第几期,借款人,手机号码,还款金额,管理费,逾期费用,逾期管理费用,还款日,投标类型,还款状态 ,账单状态,发送提示");
	
			if($page==1)
				$content = $content . "\n";
	
			foreach($list as $k=>$v)
			{
				$three_value = array();
				$three_value['id'] = iconv('utf-8','gbk','"' . $v['id'] . '"');
				$three_value['name'] = iconv('utf-8','gbk','"' . $v['name'] . '"');
				$three_value['l_key_index'] = iconv('utf-8','gbk','"' . $v['l_key_index'] . '"');
				$three_value['user_name'] = iconv('utf-8','gbk','"' . $v['user_name'] . '"');
				$three_value['mobile'] = iconv('utf-8','gbk','"' . $v['mobile'] . '"');
				$three_value['repay_money'] = iconv('utf-8','gbk','"'.format_price( $v['repay_money'] ).'"');
				$three_value['manage_money'] = iconv('utf-8','gbk','"' . format_price($v['manage_money']) . '"');
				$three_value['impose_money'] = iconv('utf-8','gbk','"' . format_price($v['impose_money']) . '"');
				$three_value['manage_impose_money'] = iconv('utf-8','gbk','"'.format_price($v['manage_impose_money']).'"');
				$three_value['repay_time'] = iconv('utf-8','gbk','"' .  to_date($v['repay_time'],"Y-m-d"). '"');
				$three_value['cate_id'] = iconv('utf-8','gbk','"' . $v['cate_id'] . '"');
				$three_value['has_repay_status'] = iconv('utf-8','gbk','"' . $v['has_repay_status'] . '"');
				$three_value['site_bad_status'] = iconv('utf-8','gbk','"' . $v['site_bad_status'] . '"');
				$three_value['is_has_send'] = iconv('utf-8','gbk','"' . $v['is_has_send'] . '"');
				$content .= implode(",", $three_value) . "\n";
			}
			header("Content-Disposition: attachment; filename=repayment_bills_list.csv");
			echo $content;
		}
		else
		{
			if($page==1)
				$this->error(L("NO_RESULT"));
		}
	
	}
	
	
	public function export_csv_yuqi($page = 1)
	{
		set_time_limit(0);
		$limit = (($page - 1)*intval(app_conf("BATCH_PAGE_SIZE"))).",".(intval(app_conf("BATCH_PAGE_SIZE")));
		if (isset ( $_REQUEST ['_order'] )) {
			$sorder = $_REQUEST ['_order'];
		}
		else{
			$sorder = "id";
		}
		switch($sorder){
			case "name":
			case "cate_id":
				$order ="d.".$sorder;
				break;
			case "has_repay_status":
				$order ="dl.status";
				break;
			case "site_bad_status":
				$order ="dl.is_site_bad";
				break;
			case "is_has_send":
				$order ="d.send_three_msg_time";
				break;
			case "l_key_index":
				$order ="dl.l_key";
				break;
			default :
				$order ="dl.".$sorder;
				break;
		}
		//排序方式默认按照倒序排列
		//接受 sost参数 0 表示倒序 非0都 表示正序
		if (isset ( $_REQUEST ['_sort'] )) {
			$sort = $_REQUEST ['_sort'] ? 'asc' : 'desc';
		}
		else{
			$sort = "desc";
		}
	
		//开始加载搜索条件
		$condition .= "  (dl.repay_time + 24*3600 - 1) < ".TIME_UTC." AND dl.has_repay=0 ";
	
		$begin_time  = !isset($_REQUEST['begin_time'])? 0 : to_timespan($_REQUEST['begin_time'],"Y-m-d");
		if($begin_time > 0){
			if($begin_time > TIME_UTC)
			{
				$this->error("不能超过当前时间");
			}
			$condition .= " and dl.repay_time >= $begin_time ";
		}
	
		$deal_status = intval($_REQUEST['deal_status']);
		if($deal_status >0){
			$condition .= " AND dl.is_site_bad=".($deal_status-1);
		}
	
	
		if(trim($_REQUEST['name'])!='')
		{
			$condition .= " and d.name like '%".trim($_REQUEST['name'])."%'";
		}
	
		if(trim($_REQUEST['user_name'])!='')
		{
			$condition .= " and dl.user_id in (select id from  ".DB_PREFIX."user WHERE user_name='".trim($_REQUEST['user_name'])."')";
		}
	
		if(intval($_REQUEST['cate_id'])>0)
		{
			require_once APP_ROOT_PATH."system/utils/child.php";
			$child = new Child("deal_cate");
			$cate_ids = $child->getChildIds(intval($_REQUEST['cate_id']));
			$cate_ids[] = intval($_REQUEST['cate_id']);
			$condition .=" and d.cate_id in (".implode(",",$cate_ids).") ";
		}
		$list = array();
		$sql_list =  " SELECT dl.*,dl.l_key + 1 as l_key_index,d.name,d.cate_id,d.send_three_msg_time,u.user_name,u.mobile FROM ".DB_PREFIX."deal_repay dl LEFT JOIN ".DB_PREFIX."deal d  ON d.id=dl.deal_id left join ".DB_PREFIX."user u on dl.user_id=u.id WHERE $condition ORDER BY $order $sort  LIMIT ".$limit;
		$list = $GLOBALS['db']->getAll($sql_list);
		foreach($list as $k=>$v){
			$list[$k]['l_key_index'] = "第 ".$v['l_key_index']." 期";
			if($v['send_three_msg_time'] == $v['repay_time']){
				$list[$k]['is_has_send'] = "已发送";
			}
			else{
				$list[$k]['is_has_send'] = "未发送";
			}
			/*aaa*/
			$list[$k]['cate_id'] = $GLOBALS['db']->getOne("select name from ".DB_PREFIX."deal_cate where id=".$list[$k]['cate_id']);
			
			if($v['has_repay']==1){
				switch($v['status']){
					case 0;
					$list[$k]['has_repay_status'] = "提前还款";
					break;
					case 1;
					$list[$k]['has_repay_status'] = "准时还款";
					break;
					case 2;
					$list[$k]['has_repay_status'] = "逾期还款";
					break;
					case 3;
					$list[$k]['has_repay_status'] = "严重逾期";
					break;
				}
			}
			else{
				$list[$k]['has_repay_status'] = "未还";
			}
	
			if($v['is_site_bad'] == 1){
				$list[$k]['site_bad_status'] = "坏账";
			}
			else{
				$list[$k]['site_bad_status'] = "正常";
			}
		}
	
		if($list)
		{
			register_shutdown_function(array(&$this, 'export_csv_yuqi'), $page+1);
				
			$yuqi_value = array('id'=>'""','name'=>'""','l_key_index'=>'""','user_name'=>'""','mobile'=>'""','repay_money'=>'""','manage_money'=>'""','impose_money'=>'""','manage_impose_money'=>'""','repay_time'=>'""','cate_id'=>'""','has_repay_status'=>'""','site_bad_status'=>'""','is_has_send'=>'""');
			if($page == 1)
				$content = iconv("utf-8","gbk","编号,贷款名称,第几期,借款人,手机号码,还款金额,管理费,逾期费用,逾期管理费用,还款日,投标类型,还款状态 ,账单状态,发送提示");
	
			if($page==1)
				$content = $content . "\n";
	
			foreach($list as $k=>$v)
			{
				$yuqi_value = array();
				$yuqi_value['id'] = iconv('utf-8','gbk','"' . $v['id'] . '"');
				$yuqi_value['name'] = iconv('utf-8','gbk','"' . $v['name'] . '"');
				$yuqi_value['l_key_index'] = iconv('utf-8','gbk','"' . $v['l_key_index'] . '"');
				$yuqi_value['user_name'] = iconv('utf-8','gbk','"' . $v['user_name'] . '"');
				$yuqi_value['mobile'] = iconv('utf-8','gbk','"' . $v['mobile'] . '"');
				$yuqi_value['repay_money'] = iconv('utf-8','gbk','"'.format_price( $v['repay_money'] ).'"');
				$yuqi_value['manage_money'] = iconv('utf-8','gbk','"' . format_price($v['manage_money']) . '"');
				$yuqi_value['impose_money'] = iconv('utf-8','gbk','"' . format_price($v['impose_money']) . '"');
				$yuqi_value['manage_impose_money'] = iconv('utf-8','gbk','"'.format_price($v['manage_impose_money']).'"');
				$yuqi_value['repay_time'] = iconv('utf-8','gbk','"' .  to_date($v['repay_time'],"Y-m-d"). '"');
				$yuqi_value['cate_id'] = iconv('utf-8','gbk','"' . $v['cate_id'] . '"');
				$yuqi_value['has_repay_status'] = iconv('utf-8','gbk','"' . $v['has_repay_status'] . '"');
				$yuqi_value['site_bad_status'] = iconv('utf-8','gbk','"' . $v['site_bad_status'] . '"');
				$yuqi_value['is_has_send'] = iconv('utf-8','gbk','"' . $v['is_has_send'] . '"');
				$content .= implode(",", $yuqi_value) . "\n";
			}
			header("Content-Disposition: attachment; filename=yuqi_list.csv");
			echo $content;
		}
		else
		{
			if($page==1)
				$this->error(L("NO_RESULT"));
		}
	
	}
	
	public function export_csv_generation($page = 1)
	{
		set_time_limit(0);
		$limit = (($page - 1)*intval(app_conf("BATCH_PAGE_SIZE"))).",".(intval(app_conf("BATCH_PAGE_SIZE")));
	
		if (isset ( $_REQUEST ['_order'] )) {
			$sorder = $_REQUEST ['_order'];
		}
		else{
			$sorder = "id";
		}
	
		switch($sorder){
			case "name":
			case "cate_id":
				$order ="d.".$sorder;
				break;
			case "site_bad_status":
				$order ="dr.is_site_bad";
				break;
			case "is_has_send":
				$order ="d.send_three_msg_time";
				break;
			case "l_key_index":
				$order ="dr.l_key";
				break;
			default :
				$order ="gr.".$sorder;
				break;
		}
	
		//排序方式默认按照倒序排列
		//接受 sost参数 0 表示倒序 非0都 表示正序
		if (isset ( $_REQUEST ['_sort'] )) {
			$sort = $_REQUEST ['_sort'] ? 'asc' : 'desc';
		}
		else{
			$sort = "desc";
		}
	
		$condition = " 1= 1 ";
	
		if(isset($_REQUEST['status'])){
			$status = intval($_REQUEST['status']);
			if($status >0){
				$condition .= " AND gr.status=".($status-1);
			}
		}
		else{
			$condition .= " AND gr.status=0";
			$_REQUEST['status'] = 1;
		}
	
		$begin_time  = trim($_REQUEST['begin_time']) =="" ? 0 : to_timespan($_REQUEST['begin_time'],"Y-m-d");
		$end_time  = trim($_REQUEST['end_time']) =="" ? 0 : to_timespan($_REQUEST['end_time'],"Y-m-d");
		if($begin_time > 0 || $end_time > 0){
			if($end_time==0)
			{
				$condition .= " and dr.repay_time >= $begin_time ";
			}
			else
				$condition .= " and dr.repay_time between  $begin_time and $end_time ";
		}
	
		if($begin_time > 0)
			$_REQUEST['begin_time'] = to_date($begin_time ,"Y-m-d");
		if($end_time > 0)
			$_REQUEST['end_time'] = to_date($end_time ,"Y-m-d");
	
	
					$deal_status = intval($_REQUEST['deal_status']);
		if($deal_status >0){
			$condition .= " AND dr.is_site_bad=".($deal_status-1);
					}
	
	
					if(trim($_REQUEST['name'])!='')
					{
							$condition .= " and d.name like '%".trim($_REQUEST['name'])."%'";
		}
	
							if(trim($_REQUEST['user_name'])!='')
							{
								$condition .= " and dr.user_id in (select id from  ".DB_PREFIX."user WHERE user_name='".trim($_REQUEST['user_name'])."')";
							}
	
							if(intval($_REQUEST['cate_id'])>0)
							{
								require_once APP_ROOT_PATH."system/utils/child.php";
								$child = new Child("deal_cate");
								$cate_ids = $child->getChildIds(intval($_REQUEST['cate_id']));
								$cate_ids[] = intval($_REQUEST['cate_id']);
								$condition .=" and d.cate_id in (".implode(",",$cate_ids).") ";
							}
	
							$list = array();
							$sql_list =  " SELECT gr.*,dr.l_key + 1 as l_key_index,dr.l_key,d.name,d.cate_id,d.send_three_msg_time,dr.user_id,dr.repay_time,agc.name as agency_name,u.user_name,u.mobile FROM ".DB_PREFIX."generation_repay gr LEFT join ".DB_PREFIX."deal_repay dr ON dr.id=gr.repay_id LEFT JOIN ".DB_PREFIX."deal d  ON d.id=gr.deal_id LEFT JOIN ".DB_PREFIX."user agc ON agc.id=gr.agency_id left join ".DB_PREFIX."user u on u.id=d.user_id  WHERE $condition ORDER BY $order $sort LIMIT ".$limit;
	
							$list = $GLOBALS['db']->getAll($sql_list);
							foreach($list as $k=>$v){
								$list[$k]['l_key_index'] = "第 ".$v['l_key_index']." 期";
								$list[$k]['admin_id'] = $GLOBALS['db']->getOne("select adm_name from ".DB_PREFIX."admin where id=".$list[$k]['admin_id']);
								$list[$k]['cate_id'] = $GLOBALS['db']->getOne("select name from ".DB_PREFIX."deal_cate where id=".$list[$k]['cate_id']);
								if($v['status'] == 0){
									$list[$k]['status_format'] = "垫付待收款";
								}
								else{
									$list[$k]['status_format'] = "垫付已收款";
								}
									
								if($v['is_site_bad'] == 1){
									$list[$k]['site_bad_status'] = "坏账";
								}
								else{
									$list[$k]['site_bad_status'] = "正常";
								}
									
								$list[$k]['total_money'] = $v['repay_money'] + $v['manage_money'] + $v['impose_money']+ $v['manage_impose_money'];
									
								$list[$k]['create_time_format'] = to_date($v['create_time'],"Y-m-d H:i");
							}
							if($list)
							{
								register_shutdown_function(array(&$this, 'export_csv_generation'), $page+1);
									
								$generation_value = array('id'=>'""','name'=>'""','l_key_index'=>'""','cate_id'=>'""','user_name'=>'""','mobile'=>'""','repay_money'=>'""', 'manage_money'=>'""','impose_money'=>'""','manage_impose_money'=>'""','total_money'=>'""','repay_time'=>'""','create_time_format'=>'""','admin_id'=>'""','agency_name'=>'""','site_bad_status'=>'""','status_format'=>'""');
								if($page == 1)
									$content = iconv("utf-8","gbk","编号,贷款名称,第几期,投标类型,借款人,电话号码,金额[垫],管理费[垫],逾期费[垫],逾期管理费[垫],总垫付,还款日,垫付时间,操作管理员,操作机构,账单状态,收款状态");
	
								if($page==1)
									$content = $content . "\n";
	
								foreach($list as $k=>$v)
								{
									$generation_value = array();
									$generation_value['id'] = iconv('utf-8','gbk','"' . $v['id'] . '"');
									$generation_value['name'] = iconv('utf-8','gbk','"' . $v['name'] . '"');
									$generation_value['l_key_index'] = iconv('utf-8','gbk','"' . $v['l_key_index'] . '"');
									$generation_value['cate_id'] = iconv('utf-8','gbk','"' . $v['cate_id'] . '"');
									$generation_value['user_name'] = iconv('utf-8','gbk','"' . $v['user_name'] . '"');
									$generation_value['mobile'] = iconv('utf-8','gbk','"' . $v['mobile'] . '"');
									$generation_value['repay_money'] = iconv('utf-8','gbk','"'.format_price( $v['repay_money'] ).'"');
									$generation_value['manage_money'] = iconv('utf-8','gbk','"' . format_price($v['manage_money']) . '"');
									$generation_value['impose_money'] = iconv('utf-8','gbk','"' . format_price($v['impose_money']) . '"');
									$generation_value['manage_impose_money'] = iconv('utf-8','gbk','"'.format_price($v['manage_impose_money']).'"');
									$generation_value['total_money'] = iconv('utf-8','gbk','"'.format_price($v['total_money']).'"');
									$generation_value['repay_time'] = iconv('utf-8','gbk','"' .  to_date($v['repay_time'],"Y-m-d"). '"');
									$generation_value['create_time_format'] = iconv('utf-8','gbk','"' .  to_date($v['create_time_format'],"Y-m-d"). '"');
									$generation_value['admin_id'] = iconv('utf-8','gbk','"' . $v['admin_id'] . '"');
									$generation_value['agency_name'] = iconv('utf-8','gbk','"' . $v['agency_name'] . '"');
									$generation_value['site_bad_status'] = iconv('utf-8','gbk','"' . $v['site_bad_status'] . '"');
									$generation_value['status_format'] = iconv('utf-8','gbk','"' . $v['status_format'] . '"');
									$content .= implode(",", $generation_value) . "\n";
								}
								header("Content-Disposition: attachment; filename=generation_list.csv");
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