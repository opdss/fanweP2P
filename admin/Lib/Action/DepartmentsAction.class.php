<?php
// +----------------------------------------------------------------------
// | easethink 方维借贷系统
// +----------------------------------------------------------------------
// | Copyright (c) 2011 http://www.fanwe.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 云淡风轻(88522820@qq.com)
// +----------------------------------------------------------------------

class DepartmentsAction extends CommonAction{
	public function index()
	{	
		$condition['is_delete'] = 0;
		$condition['is_department'] = 1;
		$this->assign("default_map",$condition);
		
		//列表过滤器，生成查询Map对象
		$map = $this->_search ();
		//追加默认参数
		if($this->get("default_map"))
		$map = array_merge($map,$this->get("default_map"));
		
		if (method_exists ( $this, '_filter' )) {
			$this->_filter ( $map );
		}
		
		$model = D ("Admin");
		if (! empty ( $model )) {
			$this->_list ( $model, $map );
		}
		$list = $this->get("list");
		
		$result = array();
		$row = 0;
		foreach($list as $k=>$v)
		{
			$v['role_id_format'] = M("Role")->where("id=".$v['role_id'])->getField("name");
		//所属部门	$v['pid_format'] =  M("Admin")->where("id=".$v['pid'])->getField("adm_name");
			$result[$row] = $v;
			$row++;
		}
		$this->assign("list",$result);
		$this->display ();
		return;
	}
	
	public function trash()
	{
		$department = M("Admin")->where('is_delete = 1')->findAll();
		
		$this->assign("department",$department);
		$this->display ();
	}
	
	public function edit()
	{
		$id = intval($_REQUEST ['id']);
		$condition['id'] = $id;
		$vo = M("Admin")->where($condition)->find();
		$this->assign ( 'vo', $vo );
		/*  所属部门
		$sql= " SELECT id,adm_name FROM ".DB_PREFIX."admin WHERE is_department=1 and is_delete=0 and is_effect=1 and id <> ".$id;
		$departs = $GLOBALS['db']->getAll($sql);
		$this->assign ( 'departs', $departs );
		*/
		$role = M("Role")  -> findAll();
		$this->assign ( 'role', $role );
		
		$this->display ();
		
	}
	
	public function update()
	{
		$data = M("Admin")->create ();
		
		if(!check_empty($data['adm_password']))
		{
			unset($data['adm_password']);  //不更新密码
		}
		else
		{
			$data['adm_password'] = md5(trim($data['adm_password']));
		}
		
		if($data['role_id']==0){
			$this->error("请选择部门角色");
		}
		
		// 更新数据
		$list=M("Admin")->save ($data);
		
		
		if (false !== $list) {
			//成功提示
			save_log($data['name'].L("UPDATE_SUCCESS"),1);
			$this->success(L("UPDATE_SUCCESS"));
		} else {
			//错误提示
			save_log($data['name'].L("UPDATE_FAILED"),0);
			$this->error(L("UPDATE_FAILED"),0,$data['name'].L("UPDATE_FAILED"));
		}
	}
	
	public function delete() {
		//删除指定记录
		$ajax = intval($_REQUEST['ajax']);
		$id = $_REQUEST ['id'];
		if (isset ( $id )) {

			$condition = array ();
			$condition['id'] = array ('in', explode ( ',', $id ) );
			$condition['is_delete'] = 0;
			
			$rel_data = M("Admin")->where($condition)->findAll();
			foreach($rel_data as $k=>$v){
				$info[] =$v['name'];
			}
			if($info) $info = implode(",",$info);
			$list = M("Admin")->where ( $condition )->setField ( 'is_delete', 1 );
			if ($list!==false) {
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
	
	public function restore(){
		$ajax = intval($_REQUEST['ajax']);
		$id = $_REQUEST ['id'];
		if (isset ( $id )) {
		
			$condition = array ();
			$condition['id'] = array ('in', explode ( ',', $id ) );
			$condition['is_delete'] = 1;
				
			$rel_data = M("Admin")->where($condition)->findAll();
			foreach($rel_data as $k=>$v){
				$info[] =$v['adm_name'];
			}
			if($info) $info = implode(",",$info);
			$list = M("Admin")->where ( $condition )->setField ( 'is_delete', 0);
			if ($list!==false) {
				
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
	
	public function foreverdelete(){
		//彻底删除指定记录
		$ajax = intval($_REQUEST['ajax']);
		$id = $_REQUEST ['id'];
		if (isset ( $id )) {
			$condition = array ('id' => array ('in', explode ( ',', $id ) ) );
			//删除的验证

			$list = M("Admin")->where ( $condition )->delete();
				
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
	
	public function add() {
		/*
		$sql= " SELECT id,adm_name FROM ".DB_PREFIX."admin WHERE is_department=1 and is_delete=0 and is_effect=1 ";
		$departs = $GLOBALS['db']->getAll($sql);
		$this->assign ( 'departs', $departs );
		*/
		$list = M("Role")  -> findAll();
		$this->assign ( 'list', $list );
		$this->display ();
		
	}
	
	
	public function insert()
	{
		B('FilterString');
		$data = M("Admin")->create ();
		//开始验证有效性
		$this->assign("jumpUrl",u(MODULE_NAME."/add"));
		if(!check_empty($data['adm_name']))
		{
			$this->error(L("ADM_NAME_EMPTY_TIP"));
		}	
		if(!check_empty($data['adm_password']))
		{
			$this->error(L("ADM_PASSWORD_EMPTY_TIP"));
		}
		if($data['role_id']==0)
		{
			$this->error(L("ROLE_EMPTY_TIP"));
		}
		if(M("Admin")->where("adm_name='".$data['adm_name']."'")->count()>0)
		{
			$this->error(L("ADMIN_EXIST_TIP"));
		}
		// 更新数据
		$log_info = $data['adm_name'];
		$data['adm_password'] = md5(trim($data['adm_password']));
		$data['is_department'] = 1;
		$list=M("Admin")->add($data);
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
	
	public function set_effect()
	{
		$id = intval($_REQUEST['id']);
		$ajax = intval($_REQUEST['ajax']);
		$info = M("Admin")->where("id=".$id)->getField("name");
		$c_is_effect = M("Admin")->where("id=".$id)->getField("is_effect");  //当前状态
		$n_is_effect = $c_is_effect == 0 ? 1 : 0; //需设置的状态
		M("Admin")->where("id=".$id)->setField("is_effect",$n_is_effect);	
		save_log($info.l("SET_EFFECT_".$n_is_effect),1);
		
		$this->ajaxReturn($n_is_effect,l("SET_EFFECT_".$n_is_effect),1)	;	
	}
	
	
}
?>