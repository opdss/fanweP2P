<?php

class VipPrivilegeAction extends CommonAction {

     public function vip_user() {
    	if(intval($_REQUEST['is_effect'])!=-1 && isset($_REQUEST['is_effect']))
		{
			$map[DB_PREFIX.'user.is_effect'] = array('eq',intval($_REQUEST['is_effect']));
		}
		
		if(intval($_REQUEST['vip_state'])!=-1 && isset($_REQUEST['vip_state']))
		{
			$map[DB_PREFIX.'user.vip_state'] = array('eq',intval($_REQUEST['vip_state']));
		}
		
		
		$this->assign("vip_list",M("VipType")->where('is_effect = 1 and is_delete = 0 order by sort ')->findAll());
		
		$this->getUserList(0,0,$map);
		$this->display ();
    	
    }
    

	private function getUserList($user_type=0,$is_delete = 0,$map){
		
		//定义条件
		$map[DB_PREFIX.'user.is_delete'] = $is_delete;
				
		
		$this->assign("vip_list",M("VipType")->where('is_effect = 1 and is_delete = 0 order by sort ')->findAll());
		
		$sql_str="select id,user_name,vip_id,vip_state,money,byear,bmonth,bday,customer_id from ".DB_PREFIX."user where user_type!='3' ";
		
		if(trim($_REQUEST['user_name'])!='')
		{
			$user_name=trim($_REQUEST['user_name']);
			$sql_str="$sql_str and user_name like'%$user_name%'";
		}
		

		if(isset($_REQUEST['cnames']))
		{
			if(intval($_REQUEST['cnames'])==-2){
				$sql_str="$sql_str and customer_id =0 ";
			}elseif(intval($_REQUEST['cnames'])==-1){
				$sql_str="$sql_str and customer_id >0 ";
			}elseif(intval($_REQUEST['cnames'])==-3){
				$sql_str=$sql_str;
			}else{
				$sql_str="$sql_str and customer_id ='".$_REQUEST['cnames']."' ";
			}
			
		}
		
		if(isset($_REQUEST['vip_id']))
		{
			if(trim($_REQUEST['vip_id'])!=-1 && isset($_REQUEST['vip_id']) ){
				$vip_id=trim($_REQUEST['vip_id']);
				$sql_str="$sql_str and vip_id ='$vip_id'";
			}
			
		}
	
		if(intval($_REQUEST['vip_state'])!=-1 && isset($_REQUEST['vip_state']))
		{
			$vip_state=trim($_REQUEST['vip_state']);
			$sql_str="$sql_str and vip_state ='$vip_state'";
			
		}
		$begin_time  = trim($_REQUEST['begin_time']);
		$end_time  = trim($_REQUEST['end_time']);
		if($begin_time !='' || $end_time !=''){
			if($end_time=='')
			{	
				$begin_timeArray=explode("-",$begin_time);
				$smonth=$begin_timeArray[1];
				$sday=$begin_timeArray[2];
				$sql_str=" $sql_str and ((bmonth='$smonth' and bday>'$sday') or (bmonth>'$smonth')) ";
				
			}else{
				if (to_timespan(trim($_REQUEST['begin_time'])) > to_timespan(trim($_REQUEST['end_time']))){
					$this->error('开始时间不能大于结束时间');
					exit;
				}
				$begin_timeArray=explode("-",$begin_time);
				$smonth=$begin_timeArray[1];
				$sday=$begin_timeArray[2];
				$end_timeArray=explode("-",$end_time);
				$emonth=$end_timeArray[1];
				$eday=$end_timeArray[2];
				$sql_str=" $sql_str and ((bmonth='$smonth' and bday>'$sday') or (bmonth>'$smonth')) and ((bmonth='$emonth' and bday<'$eday') or (bmonth<'$emonth')) ";
			}
			
				
		}
		//排序方式默认按照倒序排列
		//接受 sost参数 0 表示倒序 非0都 表示正序
		if (isset ( $_REQUEST ['_sort'] )) {
			$sort = $_REQUEST ['_sort'] ? 'asc' : 'desc';
		}
		else{
			$sql_str="$sql_str order by id desc";
		}
		$customer_cate = $GLOBALS['db']->getAll("select id,name from ".DB_PREFIX."customer where is_effect = 1 and is_delete = 0");
		$this->assign ( 'customer_cate', $customer_cate );
		
		//echo $sql_str;
		$model = D();
		$voList = $this->_Sql_list($model, $sql_str, false);
		error_reporting(0); 
		$this->display();	
		
		
	}
	
	
	public function send_gift(){
		$today=to_date(TIME_UTC,"Y-m-d");
		$this_year=date("Y");
		$id = intval($_REQUEST ['id']);
		$condition['id'] = $id;	
		$vo = M("User")->where($condition)->find();
		$this->assign("vo",$vo);
		$userinfo = $GLOBALS['db']->getRow("select * FROM ".DB_PREFIX."user WHERE id='$id' ");
		$voty=M("VipType")->where("id='".$userinfo['vip_id']."'")->find();
		$this->assign("voty",$voty);
		
		$vog=M("GivenRecord")->where("user_id='$id' and given_name_type='1' and send_date like '%$this_year%' ")->find();
		$this->assign("vog",$vog);
		
		$this->display ();
		
	}
	
	//单个发送
	public function send_update_gift(){
		require_once(APP_ROOT_PATH."system/libs/user.php");
		if(trim($_REQUEST['send_state']==1)){
			$data['user_id'] = intval($_REQUEST['id']);
			$data['vip_id'] = trim($_REQUEST['vip_id']);
			$data['given_name_type'] = 1;
			$data['given_type'] = trim($_REQUEST['given_type']);
			
			$userinfo = $GLOBALS['db']->getRow("select * FROM ".DB_PREFIX."user WHERE id='".$data['user_id']."' ");
			if($data['given_type']==1){
				$data['given_value'] = trim($_REQUEST['guarantor_margin_amt']);
				modify_account(array('money'=>$data['given_value'],'nmc_amount'=>$data['given_value']),$data['user_id'],'生日发送红包',29);
			}elseif($data['given_type']==2){
				$data['given_value'] = trim($_REQUEST['guarantor_amt']);
				$score=$data['given_value'];
				modify_account(array('score'=>$score),$data['user_id'],'生日发送积分',29);
			}elseif($data['given_type']==0){
				$this->error("请选择礼品类型");
			}
			$data['given_num'] = trim($_REQUEST['given_num']);
			$data['send_date'] = to_date(TIME_UTC,"Y-m-d");
			$data['send_state'] = trim($_REQUEST['send_state']);
			$data['brief'] = trim($_REQUEST['brief']);
			
			$GLOBALS['db']->autoExecute(DB_PREFIX."given_record",$data,"INSERT");
			
			
			$this->success(L("礼品发放成功！")); 
		}else{
			$this->error(L("礼品发放失败！"));
		}
		
		
	}
	
	public function send_gift_all(){
		//发送礼品给指定记录
		require_once(APP_ROOT_PATH."system/libs/user.php");
		$ajax = intval($_REQUEST['ajax']);
		$id = $_REQUEST ['id'];
		if (isset ( $id )) {
				$condition = array ('id' => array ('in', explode ( ',', $id ) ) );
				$this_year=date("Y");
				$rel_data = M("User")->where($condition)->findAll();	
				//  根据VIP等级发送相关的 积分或红包 记录到获得礼金中 
				$send_date=to_date(TIME_UTC,"Y-m-d");	
				foreach($rel_data as $data)
				{
					if($data['vip_id']!=0&&$data['vip_state']==1){
						$user_id=$data['id'];
						$given_num=M("GivenRecord")->where("user_id='$user_id' and given_name_type='1' and send_date like '%$this_year%' ")->count();
						if($given_num==0){
							$info['user_id'] = $data['id'];
							$info['vip_id'] = $data['vip_id'];
							$userinfo = $GLOBALS['db']->getRow("select * FROM ".DB_PREFIX."user WHERE id='".$user_id."' ");
							
							$btype=M("VipSetting")->where("vip_id='".$data['vip_id']."' ")->getField("btype");
							$bgift=M("VipSetting")->where("vip_id='".$data['vip_id']."' ")->getField("bgift");
							if($btype==2){
								$info['given_type']= "1";
								modify_account(array('money'=>$bgift,'nmc_amount'=>$bgift),$GLOBALS['user_info']['id'],'生日发送红包',29);
							}else{
								$info['given_type']= "2";
								$score=$bgift;
								modify_account(array('score'=>$score),$data['user_id'],'生日发送积分',29);
							}
							$info['given_value']="$bgift";
							$info['given_name_type']= 1;
							$info['given_num']= "1";
							$info['send_date'] = to_date(TIME_UTC,"Y-m-d");
							$info['send_state'] = "1";
							$GLOBALS['db']->autoExecute(DB_PREFIX."given_record",$info,"INSERT");
						}
					}
					
				}
				
				$this->success(L("发放成功")); 
				
			} else {
				$this->error (l("发送失败"));
		}		
	}
	//VIP客服添加 
	public function add_sc_service(){
		
		$id = intval($_REQUEST ['id']);
		require_once APP_ROOT_PATH."app/Lib/common.php";
		$user_info = get_user( "*",$id);
		$this->assign ( 'user_info', $user_info );
		
		$voty=M("VipType")->where("id='".$user_info['vip_id']."'")->find();
		$this->assign("voty",$voty);
		//客服列表
		$customer_sql =  " SELECT * FROM ".DB_PREFIX."customer WHERE is_delete= 0 and is_effect=1";
		$customer_list = $GLOBALS['db']->getAll($customer_sql);
		$this->assign ( 'customers', $customer_list );
		$this->display ();
	}
	
	//VIP客服添加 
	public function insert_sc_service(){
		$id = intval($_REQUEST ['id']);
		$customer_id = intval($_REQUEST ['customer_id']);
		
		$user_info = array();
		$user_info['customer_id'] = $customer_id;
		$list = $GLOBALS['db']->autoExecute(DB_PREFIX."user",$user_info,"UPDATE","id=".$id);

		if (false !== $list) {
			//成功提示
			save_log($user_info.L("专享客服分配成功"),1);
			$this->success(L("专享客服分配成功"));
		} else {
			//错误提示
			save_log($user_info.L("专享客服分配失败"),0);
			$this->error(L("专享客服分配失败"));
		}
		

	}
	
	//客服查看
	public function sc_service(){
		
		$id = intval($_REQUEST ['id']);
		require_once APP_ROOT_PATH."app/Lib/common.php";
		$user_info = get_user( "*",$id);
		$this->assign ( 'user_info', $user_info );
		
		$voty=M("VipType")->where("id='".$user_info['vip_id']."'")->find();
		$this->assign("voty",$voty);
		
		$vocs=M("Customer")->where("id='".$user_info['customer_id']."'")->find();
		$this->assign("vocs",$vocs);
		//客服列表
		$customer_sql =  " SELECT * FROM ".DB_PREFIX."customer WHERE is_delete= 0 and is_effect=1";
		$customer_list = $GLOBALS['db']->getAll($customer_sql);
		$this->assign ( 'customers', $customer_list );
		$this->display ();
	}
	
	//VIP编辑 
	public function edit(){
		$id = intval($_REQUEST ['id']);
		$condition['id'] = $id;	
		$vo = M("ScService")->where($condition)->find();
		$this->assign("vo",$vo);
		
		$scinfo = $GLOBALS['db']->getRow("select * FROM ".DB_PREFIX."sc_service  WHERE id='$id' ");
		$voty=M("VipType")->where("id='".$scinfo['vip_id']."'")->find();
		$this->assign("voty",$voty);
		
		$this->display ();
	}
	
	public function update_sc_service() {
		//B('FilterString');
		$data = M("ScService")->create ();
		$log_info = M("ScService")->where("id=".intval($data['id']))->getField("title");
		//开始验证有效性
		$this->assign("jumpUrl",u("VipPrivilege"."/edit",array("id"=>$data['id'])));
		// 更新数据
		$list=M("ScService")->save ($data);
		if (false !== $list) {
			//成功提示
			save_log($log_info.L("UPDATE_SUCCESS"),1);
			rm_auto_cache("cache_sc_service");
			$this->success(L("UPDATE_SUCCESS"));
		} else {
			//错误提示
			save_log($log_info.L("UPDATE_FAILED"),0);
			$this->error(L("UPDATE_FAILED"),0,$log_info.L("UPDATE_FAILED"));
		}
	}
	
	public function sc_service_trash()
	{

		$map[DB_PREFIX.'sc_service.is_delete'] = array('eq',1);
		$model = D ("ScService");
		if (! empty ( $model )) {
			$this->_list ( $model, $map );
		}
		$this->display ();
	}
	
	public function delete() {
		//删除指定记录
		$ajax = intval($_REQUEST['ajax']);
		$id = $_REQUEST ['id'];
		if (isset ( $id )) {
				$condition = array ('id' => array ('in', explode ( ',', $id ) ) );
				
				$rel_data = M("ScService")->where($condition)->findAll();				
				foreach($rel_data as $data)
				{
					$info[] = $data['name'];	
				}
				if($info) $info = implode(",",$info);
				$list = M("ScService")->where ( $condition )->setField ( 'is_delete', 1 );
				if ($list!==false) {
					save_log($info.l("DELETE_SUCCESS"),1);
					rm_auto_cache("cache_sc_service");
					$this->success (l("DELETE_SUCCESS"),$ajax);
				} else {
					save_log($info.l("DELETE_FAILED"),0);
					$this->error (l("DELETE_FAILED"),$ajax);
				}
			} else {
				$this->error (l("INVALID_OPERATION"),$ajax);
		}		
	}
	public function restore() {
		//恢复指定记录
		$ajax = intval($_REQUEST['ajax']);
		$id = $_REQUEST ['id'];
		if (isset ( $id )) {
				$condition = array ('id' => array ('in', explode ( ',', $id ) ) );
				$rel_data = M("ScService")->where($condition)->findAll();				
				foreach($rel_data as $data)
				{
					$info[] = $data['name'];	
				}
				if($info) $info = implode(",",$info);
				$list = M("ScService")->where ( $condition )->setField ( 'is_delete', 0 );
				if ($list!==false) {
					save_log($info.l("RESTORE_SUCCESS"),1);
					rm_auto_cache("cache_sc_service");
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

				$rel_data = M("ScService")->where($condition)->findAll();				
				foreach($rel_data as $data)
				{
					$info[] = $data['name'];	
				}
				if($info) $info = implode(",",$info);
				$list = M("ScService")->where ( $condition )->delete();

				if ($list!==false) {
					save_log($info.l("FOREVER_DELETE_SUCCESS"),1);
					rm_auto_cache("cache_sc_service");
					$this->success (l("FOREVER_DELETE_SUCCESS"),$ajax);
				} else {
					save_log($info.l("FOREVER_DELETE_FAILED"),0);
					$this->error (l("FOREVER_DELETE_FAILED"),$ajax);
				}
			} else {
				$this->error (l("INVALID_OPERATION"),$ajax);
		}
	}
	
	//状态 变更
    public function set_effect()
	{
		$id = intval($_REQUEST['id']);
		$ajax = intval($_REQUEST['ajax']);
		$info = M("User")->where("id=".$id)->getField("name");
		$c_vip_state = M("User")->where("id=".$id)->getField("vip_state");  //当前状态
		$n_vip_state = $c_vip_state == 0 ? 1 : 0; //需设置的状态
		M("User")->where("id=".$id)->setField("vip_state",$n_vip_state);	
		save_log($info.l("SET_EFFECT_".$n_vip_state),1);
		rm_auto_cache("cache_vip_user");
		$this->ajaxReturn($n_vip_state,l("SET_EFFECT_".$n_vip_state),1)	;	
	}
	
	//VIP降级记录
	public function vip_demotion_record(){
		
		$this->assign("vip_list",M("VipType")->where('is_effect = 1 and is_delete = 0 order by sort ')->findAll());
		if(trim($_REQUEST['user_name'])!='')
		{
			$sql  ="select group_concat(id) from ".DB_PREFIX."user where user_name like '%".trim($_REQUEST['user_name'])."%'";
			$ids = $GLOBALS['db']->getOne($sql);
			$map[DB_PREFIX.'demotion_record.user_id'] = array("in",$ids);
		}
		
		$begin_time  = trim($_REQUEST['begin_time']);
		$end_time  = trim($_REQUEST['end_time']);
		if($begin_time !='' || $end_time !=''){
			if($end_time==0)
			{
				$map[DB_PREFIX.'demotion_record.start_date'] = array('egt',$begin_time);
			}
			else
				$map[DB_PREFIX.'demotion_record.start_date']= array("between",array($begin_time,$end_time));
			
				
		}
		
		$model = D ("DemotionRecord");
		
		if (! empty ( $model )) {
			$this->_list ( $model, $map );
		}
		$this->display ();
	}
	
	//VIP客服列表
	public function vip_sc_service(){
		
		$this->assign("vip_list",M("VipType")->where('is_effect = 1 and is_delete = 0 order by sort ')->findAll());
		if(trim($_REQUEST['user_name'])!='')
		{
			$map[DB_PREFIX.'sc_service.user_id'] = D("User")->where("user_name like '%".trim($_REQUEST['user_name'])."%'")->getField('id');
		}
		
		if(trim($_REQUEST['sc_name'])!='')
		{
			$map[DB_PREFIX.'sc_service.sc_name'] = array('like','%'.trim($_REQUEST['sc_name']).'%');
		}
		
		if(isset($_REQUEST['vip_id']))
		{
			if(trim($_REQUEST['vip_id'])!=-1 && isset($_REQUEST['vip_id']) ){
				$map[DB_PREFIX.'sc_service.vip_id'] = array('eq',trim($_REQUEST['vip_id']));
			}
			
		}
		
		$map[DB_PREFIX.'sc_service.is_delete'] = array('eq',0);
		$model = D ("ScService");
		
		if (! empty ( $model )) {
			$this->_list ( $model, $map );
		}
		$this->display ();
	}
	
	//VIP升级记录
	public function vip_upgrade_record(){
		
		$this->assign("vip_list",M("VipType")->where('is_effect = 1 and is_delete = 0 order by sort ')->findAll());
		if(trim($_REQUEST['user_name'])!='')
		{
			$sql  ="select group_concat(id) from ".DB_PREFIX."user where user_name like '%".trim($_REQUEST['user_name'])."%'";
			$ids = $GLOBALS['db']->getOne($sql);
			$map[DB_PREFIX.'vip_upgrade_record.user_id'] = array("in",$ids);
		}
		
		$begin_time  = trim($_REQUEST['begin_time']);
		$end_time  = trim($_REQUEST['end_time']);
		if($begin_time !='' || $end_time !=''){
			if($end_time==0)
			{
				$map[DB_PREFIX.'vip_upgrade_record.upgrade_date'] = array('egt',$begin_time);
			}
			else
				$map[DB_PREFIX.'vip_upgrade_record.upgrade_date']= array("between",array($begin_time,$end_time));
			
				
		}
		
		$model = D ("VipUpgradeRecord");
		
		if (! empty ( $model )) {
			$this->_list ( $model, $map );
		}
		$this->display ();
	}
	
}
?>