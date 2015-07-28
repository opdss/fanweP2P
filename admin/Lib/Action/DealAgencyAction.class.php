<?php
// +----------------------------------------------------------------------
// | Fanwe 方维p2p借贷系统
// +----------------------------------------------------------------------
// | Copyright (c) 2011 http://www.fanwe.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 云淡风轻(88522820@qq.com)
// +----------------------------------------------------------------------

class DealAgencyAction extends CommonAction{
	public function __construct()
	{	
		parent::__construct();
		require_once APP_ROOT_PATH."/system/libs/user.php";
	}
	public function index()
	{
		
		//列表过滤器，生成查询Map对象
		/*$map = $this->_search ();
		if(strim($_REQUEST['user_name'])!=""){
			$map['user_name'] =  array("like","%".strim($_REQUEST['name'])."%");
		}
		
		if(strim($_REQUEST['mobile'])!=""){
			$map['mobile'] =  array("eq",strim($_REQUEST['mobile']));
		}
		
		if(strim($_REQUEST['email'])!=""){
			$map['email'] =  array("eq",strim($_REQUEST['email']));
		}
		*/
		if (method_exists ( $this, '_filter' )) {
			$this->_filter ( $map );
		}
		if(intval($_REQUEST['is_effect'])!=-1 && isset($_REQUEST['is_effect']))
		{
			$map[DB_PREFIX.'user.is_effect'] = array('eq',intval($_REQUEST['is_effect']));
		}
		$this->getUserList(2,0,$map);
		$this->display ();

		return;
	}
	
	public function passed(){
		$user_id = intval($_REQUEST['id']);
		$user_info = M("User")->getById($user_id);
		
		$field_array = array(
			"credit_identificationscanning"=>"idcardpassed",
		);
		
		
		$this->assign("user_info",$user_info);
		
		
		
		$t_credit_file = M("UserCreditFile")->where("user_id=".$user_id)->findAll();
		foreach($t_credit_file as $k=>$v){
    		$file_list = array();
    		if($v['file'])
    			$file_list = unserialize($v['file']);
    		
    		if(is_array($file_list)) 
    			$v['file_list']= $file_list;
    		
    		$credit_file[$v['type']] = $v;
    	}
    	
    	
    	$loantype = intval($_REQUEST['loantype']);
    	$needs_credits = array();
		if($loantype > 0){
			$loantypeinfo = M("DealLoanType")->getById($loantype);
			if($loantypeinfo['credits']!=""){
				$needs_credits = unserialize($loantypeinfo['credits']);
			}
		}
    	
    	$credit_type= load_auto_cache("credit_type");
    	$credit_list = array();
    	foreach($credit_type['list'] as $k=>$v){
    		if($v["type"]=="credit_identificationscanning")
			{
				if($v['must']==1 || $loantype == 0 || (count($needs_credits)>0 && in_array($v['type'],$needs_credits))){
					$credit_list[$v['type']] = $credit_type['list'][$v['type']];
					$credit_list[$v['type']]['credit'] = $credit_file[$v['type']];
					
					//User表里面的数据
					if($user_info[$field_array[$v['type']]]){
						$credit_list[$v['type']]['credit']['passed'] = $user_info[$field_array[$v['type']]];
					}
				}
			}
    	}
		
		
		$this->assign("credits",$credit_list);
		
		$this->display ();
		return;
	}
	/*
	 * $user_type  0普通会员 1企业会员
	 */
	private function getUserList($user_type=0,$is_delete = 0,$map){
		
		//$group_list = M("UserGroup")->findAll();
		//$this->assign("group_list",$group_list);
		
		$map[DB_PREFIX.'user.user_type'] = $user_type;
		//定义条件
		$map[DB_PREFIX.'user.is_delete'] = $is_delete;

		/*if(intval($_REQUEST['group_id'])>0)
		{
			$map[DB_PREFIX.'user.group_id'] = intval($_REQUEST['group_id']);
		}*/
		
		if(trim($_REQUEST['user_name'])!='')
		{
			$map[DB_PREFIX.'user.user_name'] = array('like','%'.trim($_REQUEST['user_name']).'%');
		}
		if(trim($_REQUEST['email'])!='')
		{
			$map[DB_PREFIX.'user.email'] = array('like','%'.trim($_REQUEST['email']).'%');
		}
		if(trim($_REQUEST['mobile'])!='')
		{
			$map[DB_PREFIX.'user.mobile'] = array('like','%'.trim($_REQUEST['mobile']).'%');
		}
		/*if(trim($_REQUEST['pid_name'])!='')
		{
			$pid = M("User")->where("user_name='".trim($_REQUEST['pid_name'])."'")->getField("id");
			$map[DB_PREFIX.'user.pid'] = $pid;
		}
		*/
		
		$begin_time  = trim($_REQUEST['begin_time'])==''?0:to_timespan($_REQUEST['begin_time']);
		$end_time  = trim($_REQUEST['end_time'])==''?0:to_timespan($_REQUEST['end_time']);
		/*if($begin_time > 0 || $end_time > 0){
			if($end_time==0)
			{
				$map[DB_PREFIX.'user.create_time'] = array('egt',$begin_time);
			}
			else
				$map[DB_PREFIX.'user.create_time']= array("between",array($begin_time,$end_time));
		}*/
		
		if (method_exists ( $this, '_filter' )) {
			$this->_filter ( $map );
		}
		$name= 'user';

		$model = D ($name);
		if (! empty ( $model )) {
			$this->_list ( $model, $map );
		}
		
	}
	
	
	public function add()
	{
		$this->display();
	}
	
	public function insert() {
		B('FilterString');
		if(strim($_REQUEST['user_pwd'])!=""){
			if(strim($_REQUEST['user_pwd'])!=strim($_REQUEST['cfguser_pwd'])){
				$this->error("确认密码错误");
			}
		}
		$data = M("user")->create ();
		//开始验证有效性
		$this->assign("jumpUrl",u(MODULE_NAME."/add"));
		if(!check_empty($data['user_name']))
		{
			$this->error(L("DEALAGENCY_NAME_EMPTY_TIP"));
		}	
		if($data['user_pwd']!=""){
			$data['user_pwd'] = $data['user_pwd'];
		}
		// 更新数据
		$log_info = $data['user_name'];
		$data["user_type"] = 2;
		$list=M("user")->add($data);
		if (false !== $list) {
			
			//成功提示
			save_log($log_info.L("INSERT_SUCCESS"),1);
			
			$this->success(L("INSERT_SUCCESS"));
		} else {
			//错误提示
			save_log($log_info.L("INSERT_FAILED"),0);
			$this->error(L("INSERT_FAILED"));
		}
	}
	
	public function edit() {		
		$id = intval($_REQUEST ['id']);
		
		$condition['id'] = $id;		
		$vo = M("user")->where($condition)->find();
		$this->assign ( 'vo', $vo );		
		
		$this->display ();
	}

	
    public function set_effect()
	{
		$id = intval($_REQUEST['id']);
		$ajax = intval($_REQUEST['ajax']);
		$info = M("user")->where("id=".$id)->getField("name");
		$c_is_effect = M("user")->where("id=".$id)->getField("is_effect");  //当前状态
		$n_is_effect = $c_is_effect == 0 ? 1 : 0; //需设置的状态
		M("user")->where("id=".$id)->setField("is_effect",$n_is_effect);	
		save_log($info.l("SET_EFFECT_".$n_is_effect),1);
		
		$this->ajaxReturn($n_is_effect,l("SET_EFFECT_".$n_is_effect),1)	;	
	}
	
	public function set_sort()
	{
		$id = intval($_REQUEST['id']);
		$sort = intval($_REQUEST['sort']);
		$log_info = M("user")->where("id=".$id)->getField("name");
		if(!check_sort($sort))
		{
			$this->error(l("SORT_FAILED"),1);
		}
		M("user")->where("id=".$id)->setField("sort",$sort);
		save_log($log_info.l("SORT_SUCCESS"),1);
		
		$this->success(l("SORT_SUCCESS"),1);
	}
	
	public function update() {
		B('FilterString');
		if(strim($_REQUEST['user_pwd'])!=""){
			if(strim($_REQUEST['user_pwd'])!=strim($_REQUEST['cfguser_pwd'])){
				$this->error("确认密码错误");
			}
		}
		$data = M("user")->create ();
		if(!check_empty($data['user_name']))
		{
			$this->error(L("DEALAGENCY_NAME_EMPTY_TIP"));
		}	

		if($data['user_pwd']!=""){
			//$data['user_pwd'] = md5($data['user_pwd']);
		}
		else{
			unset($data['user_pwd']);
		}
		$log_info = M("user")->where("id=".intval($data['id']))->getField("user_name");
		//开始验证有效性
		$this->assign("jumpUrl",u(MODULE_NAME."/edit",array("id"=>$data['id'])));
		// 更新数据
		$data["user_type"] = 2;
		$res = save_user($data,'UPDATE');
		if($res['status']==0)
		{
			$error_field = $res['data'];
			if($error_field['error'] == EMPTY_ERROR)
			{
				if($error_field['field_name'] == 'user_name')
				{
					$this->error(L("USER_NAME_EMPTY_TIP"));
				}
				elseif($error_field['field_name'] == 'email')
				{
					$this->error(L("USER_EMAIL_EMPTY_TIP"));
				}
				else
				{
					$this->error(sprintf(L("USER_EMPTY_ERROR"),$error_field['field_show_name']));
				}
			}
			if($error_field['error'] == FORMAT_ERROR)
			{
				if($error_field['field_name'] == 'email')
				{
					$this->error(L("USER_EMAIL_FORMAT_TIP"));
				}
				if($error_field['field_name'] == 'mobile')
				{
					$this->error(L("USER_MOBILE_FORMAT_TIP"));
				}
				if($error_field['field_name'] == 'idno')
				{
					$this->error(L("USER_IDNO_FORMAT_TIP"));
				}
			}
			
			if($error_field['error'] == EXIST_ERROR)
			{
				if($error_field['field_name'] == 'user_name')
				{
					$this->error(L("USER_NAME_EXIST_TIP"));
				}
				if($error_field['field_name'] == 'email')
				{
					$this->error(L("USER_EMAIL_EXIST_TIP"));
				}
				if($error_field['field_name'] == 'mobile')
				{
					$this->error(L("USER_MOBILE_EXIST_TIP"));
				}
				if($error_field['field_name'] == 'idno')
				{
					$this->error(L("USER_IDNO_EXIST_TIP"));
				}
			}
			save_log($log_info.L("UPDATE_FAILED"),0);
		}
		else
		{
			save_log($log_info.L("UPDATE_SUCCESS"),1);
			
			$this->success(L("UPDATE_SUCCESS"));
		}
	}
	
	public function delete() {
		//删除指定记录
		$ajax = intval($_REQUEST['ajax']);
		$id = $_REQUEST ['id'];
		if (isset ( $id )) {
				$deal_condition = array ('user_id' => array ('in', explode ( ',', $id ) ) );
				if(M("Deal")->where($deal_condition)->count() > 0){
					$this->error ("删除的会员有借款记录",$ajax);
				}
				//删除验证
				if(M("DealOrder")->where(array ('user_id' => array ('in', explode ( ',', $id ) ) ))->count()>0)
				{
					$this->error (l("ORDER_EXIST_DELETE_FAILED"),$ajax);
				}
				$condition = array ('id' => array ('in', explode ( ',', $id ) ) );
				$rel_data = M('user')->where($condition)->findAll();				
				foreach($rel_data as $data)
				{
					$info[] = $data['user_name'];	
				}
				if($info) $info = implode(",",$info);
				$list = M('user')->where ( $condition )->setField ( 'is_delete', 1 );
				if ($list!==false) {
					//把信息屏蔽
					M("Topic")->where("user_id in (".$id.")")->setField("is_effect",0);
					M("TopicReply")->where("user_id in (".$id.")")->setField("is_effect",0);
					M("Message")->where("user_id in (".$id.")")->setField("is_effect",0);
					save_log($info.l("DELETE_SUCCESS"),1);
					$this->success (l("DELETE_SUCCESS"),$ajax);
				} else {
					save_log($info.l("DELETE_FAILED"),0);
					$this->error (l("DELETE_FAILED"),$ajax);
				}
			} else {
				$this->error (l("INVALID_OPERATION"),$ajax);
		}		
	}
	

	function view_info(){
		$agency_id = intval($_REQUEST['id']);
		$deal_agency = M("user")->getById($agency_id);
		$old_imgdata_str = unserialize($deal_agency['view_info']);
		$this->assign("deal_agency",$deal_agency);
		$this->assign("old_imgdata_str",$old_imgdata_str);
		$this->display();
	}
	
	function modify_view_info(){
		
		if(intval($_REQUEST['id'])==0){
			$this->error("机构不存在！");
			exit();
		}
		
		$view_down_data = array();
		foreach($_FILES['img_data']['name'] as $k=>$v){
			$file = pathinfo($v);
			
			if($file['error'] == 0){
				if(!file_exists(APP_ROOT_PATH."/public/gview_info"))
					@mkdir(APP_ROOT_PATH."/public/gview_info",0777);
			
				$time = to_date(TIME_UTC,"Ym");
				if(!file_exists(APP_ROOT_PATH."/public/gview_info/".$time))
					@mkdir(APP_ROOT_PATH."/public/gview_info/".$time,0777);
			
				$file_name = md5(TIME_UTC.$_REQUEST['id'].$v.$k).".".$file['extension'];
				
				move_uploaded_file($_FILES['img_data']['tmp_name'][$k],APP_ROOT_PATH."/public/gview_info/".$time."/".$file_name);
				
				if(file_exists(APP_ROOT_PATH."/public/gview_info/".$time."/".$file_name)){
					$view_down_data[$k]['img'] = "./public/gview_info/".$time."/".$file_name;
					$view_down_data[$k]['name'] = strim($_REQUEST['file_name'][$k]);
				}
			
			}
			
		}
		
		$new_view_info_arr= array();
		$old_view_info = M("user")->where("id=".intval($_REQUEST['id']))->getField("view_info");
		if($old_view_info !=""){
			$old_view_info_arr = unserialize($old_view_info);
			
			foreach($old_view_info_arr as $k=>$v){
				$new_view_info_arr[$k] = $v;
			}
		}
		
		foreach($view_down_data as $k=>$v){
			$new_view_info_arr[] = $v;
		}
	
		
		$data['view_info'] = serialize($new_view_info_arr);
		
	
		if($GLOBALS['db']->autoExecute(DB_PREFIX."user",$data,"UPDATE","id=".$_REQUEST['id'])){
			$this->success("上传资料成功！");
		}
		else{
			$this->error("上传资料失败！");
		}
	
	}
	
	function view_info_del_img(){
		if(intval($_REQUEST['id'])==0){
			$this->error("机构不存在！");
			exit();
		}
		
		if(strim($_REQUEST['src'])==""){
			$this->error("删除的文件不存在！");
			exit();
		}
		
		$old_view_info = M("user")->where("id=".intval($_REQUEST['id']))->getField("view_info");
		if($old_view_info !=""){
			$old_view_info_arr = unserialize($old_view_info);
			foreach($old_view_info_arr as $k=>$v){
				if($v['img'] == strim($_REQUEST['src'])){
					@unlink(APP_ROOT_PATH.$v['img']);
					unset($old_view_info_arr[$k]);
				}
			}
		}
		$data['view_info'] = serialize($old_view_info_arr);
		
		if($GLOBALS['db']->autoExecute(DB_PREFIX."user",$data,"UPDATE","id=".$_REQUEST['id'])){
			$this->success("删除成功！");
		}
		else{
			$this->error("删除失败！");
		}
	}
	public function trash()
	{
		$this->getUserList(2,1,array());
		$this->display ();
	}
	public function restore() {
		//删除指定记录
		$ajax = intval($_REQUEST['ajax']);
		$id = $_REQUEST ['id'];
		if (isset ( $id )) {
				$condition = array ('id' => array ('in', explode ( ',', $id ) ) );
				$rel_data = M("user")->where($condition)->findAll();				
				foreach($rel_data as $data)
				{
					$info[] = $data['user_name'];						
				}
				if($info) $info = implode(",",$info);
				$list = M("user")->where ( $condition )->setField ( 'is_delete', 0 );
				if ($list!==false) {
					//把信息屏蔽
					M("Topic")->where("user_id in (".$id.")")->setField("is_effect",1);
					M("TopicReply")->where("user_id in (".$id.")")->setField("is_effect",1);
					M("Message")->where("user_id in (".$id.")")->setField("is_effect",1);
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
				$rel_data = M("user")->where($condition)->findAll();				
				foreach($rel_data as $data)
				{
					$info[] = $data['user_name'];	
				}
				if($info) $info = implode(",",$info);
				$ids = explode ( ',', $id );
				foreach($ids as $uid)
				{
					delete_user($uid);
				}
				save_log($info.l("FOREVER_DELETE_SUCCESS"),1);
				clear_auto_cache("consignee_info");
				$this->success (l("FOREVER_DELETE_SUCCESS"),$ajax);
				
			} else {
				$this->error (l("INVALID_OPERATION"),$ajax);
		}
	}
	
}
?>