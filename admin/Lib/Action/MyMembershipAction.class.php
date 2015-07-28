<?php
// +----------------------------------------------------------------------
// | easethink 方维借贷系统
// +----------------------------------------------------------------------
// | Copyright (c) 2011 http://www.fanwe.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 云淡风轻(88522820@qq.com)
// +----------------------------------------------------------------------

class MyMembershipAction extends CommonAction{
	public function index()
	{	
		$adm_session = es_session::get(md5(conf("AUTH_KEY")));
		$adm_name = $adm_session['adm_name'];
		$adm_id = intval($adm_session['adm_id']);
		
		$condition['is_delete'] = 0;
		$condition['is_effect'] = 1;
		//is_department  0:管理员  1：部门
		$is_department = M("Admin")->where("id=".$adm_id)->getField("is_department");
		
		if( $is_department == 0){
			$condition['admin_id'] = $adm_id;
		}elseif($is_department == 1)
		{
			$id = $GLOBALS['db']->getAll("SELECT id FROM  ".DB_PREFIX."admin WHERE  pid = ".$adm_id);
			$flatmap = array_map("array_pop",$id);
			$id=implode(',',$flatmap);
			$condition['admin_id'] = array("exp","in (".$adm_id.",".$id.")");
		}
		$this->assign("default_map",$condition);
		
		//列表过滤器，生成查询Map对象
		$map = $this->_search ();
		//追加默认参数
		if($this->get("default_map"))
		$map = array_merge($map,$this->get("default_map"));
		
		if (method_exists ( $this, '_filter' )) {
			$this->_filter ( $map );
		}
		
		$model = D ("User");
		if (! empty ( $model )) {
			$this->_list ( $model, $map );
		}
		
		$list = $this->get("list");
		$this->assign("list",$list);
		$this->display ();
		return;
	}
	
	public function delete() {
		//删除指定记录
		$ajax = intval($_REQUEST['ajax']);
		$id = $_REQUEST ['id'];
		if (isset ( $id )) {

			$condition = array ();
			$condition['id'] = array ('in', explode ( ',', $id ) );
		
			$list = M("User")->where ( $condition )->setField ( 'admin_id', 0 );
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
	
	
	
	public function set_effect()
	{
		$id = intval($_REQUEST['id']);
		$ajax = intval($_REQUEST['ajax']);
		$info = M("DealCity")->where("id=".$id)->getField("name");
		$c_is_effect = M("DealCity")->where("id=".$id)->getField("is_effect");  //当前状态
		$n_is_effect = $c_is_effect == 0 ? 1 : 0; //需设置的状态
		M("DealCity")->where("id=".$id)->setField("is_effect",$n_is_effect);	
		save_log($info.l("SET_EFFECT_".$n_is_effect),1);
		
		$this->ajaxReturn($n_is_effect,l("SET_EFFECT_".$n_is_effect),1)	;	
	}
	
	
}
?>