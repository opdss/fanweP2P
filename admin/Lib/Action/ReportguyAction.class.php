<?php
// +----------------------------------------------------------------------
// | Fanwe 方维p2p借贷系统
// +----------------------------------------------------------------------
// | Copyright (c) 2011 http://www.fanwe.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 云淡风轻(88522820@qq.com)
// +----------------------------------------------------------------------

class ReportguyAction extends CommonAction{
	public function index()
	{
		//开始加载搜索条件
		if(intval($_REQUEST['id'])>0)
		$map['id'] = intval($_REQUEST['id']);
		
		if(trim($_REQUEST['user_name'])!='')
		{
			$sql  ="select group_concat(id) from ".DB_PREFIX."user where user_name like '%".trim($_REQUEST['user_name'])."%'";
			
			$ids = $GLOBALS['db']->getOne($sql);
			$map['user_id'] = array("in",$ids);
		}
		
		if(trim($_REQUEST['r_user_name'])!='')
		{
			$sql  ="select group_concat(id) from ".DB_PREFIX."user where user_name like '%".trim($_REQUEST['r_user_name'])."%'";
			
			$ids = $GLOBALS['db']->getOne($sql);
			$map['r_user_id'] = array("in",$ids);
		}
		
		if(isset($_REQUEST['status']) && trim($_REQUEST['status']) != '' && trim($_REQUEST['status']) != 'all'){
			$map['status'] = array("eq",intval($_REQUEST['status']));
		}
		
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

	public function edit() {		
		$id = intval($_REQUEST ['id']);
		$condition['id'] = $id;		
		$vo = M(MODULE_NAME)->where($condition)->find();
		
		$this->assign ( 'vo', $vo );
		$this->display ();
	}
	
	public function update(){
		B('FilterString');
		$data = M(MODULE_NAME)->create ();
		//开始验证有效性
		$this->assign("jumpUrl",u(MODULE_NAME."/index",array("status"=>$_REQUEST['f_status'])));
		// 更新数据
		
		$list=M(MODULE_NAME)->save ($data);
		if (false !== $list) {
			//成功提示
			save_log($data['id'].L("UPDATE_SUCCESS"),1);
			
			$this->success(L("UPDATE_SUCCESS"));
		} else {
			//错误提示
			save_log($data['id'].L("UPDATE_FAILED"),0);
			$this->error(L("UPDATE_FAILED"),0,$data['id'].L("UPDATE_FAILED"));
		}
	}
	public function foreverdelete() {
		//彻底删除指定记录
		$ajax = intval($_REQUEST['ajax']);
		$id = $_REQUEST ['id'];
		if (isset ( $id )) {
				$condition = array ('id' => array ('in', explode ( ',', $id ) ) );
				
				$list = M(MODULE_NAME)->where ( $condition )->delete();	
				
				if ($list!==false) {
					save_log(l("FOREVER_DELETE_SUCCESS"),1);
					$this->success (l("FOREVER_DELETE_SUCCESS"),$ajax);
				} else {
					save_log(l("FOREVER_DELETE_FAILED"),0);
					$this->error (l("FOREVER_DELETE_FAILED"),$ajax);
				}
			} else {
				$this->error (l("INVALID_OPERATION"),$ajax);
		}
	}
}
?>