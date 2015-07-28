<?php
// +----------------------------------------------------------------------
// | easethink 方维借贷系统
// +----------------------------------------------------------------------
// | Copyright (c) 2011 http://www.fanwe.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 云淡风轻(88522820@qq.com)
// +----------------------------------------------------------------------

class GoodsTypeAttrAction extends CommonAction{
	public function index()
	{	
		$goods_type_id = intval($_REQUEST ['goods_type_id']);
		$this->assign("default_map",$condition);
		//列表过滤器，生成查询Map对象
		$map = $this->_search ();
		//追加默认参数
		if($this->get("default_map"))
		$map = array_merge($map,$this->get("default_map"));
		
		if (method_exists ( $this, '_filter' )) {
			$this->_filter ( $map );
		}
		
		$model = D ("GoodsTypeAttr");
		
		if (! empty ( $model )) {
			$this->_list ( $model, $map );
		}
		$list = $this->get("list");
		$this->assign("goods_type_id",$goods_type_id);
		$this->assign("list",$list);
		$this->display ();
		return;
	}
	
	
	public function edit()
	{
		$id = intval($_REQUEST ['id']);
		
		$condition['id'] = $id;
		$vo = M("GoodsTypeAttr")->where($condition)->find();
		$this->assign ( 'vo', $vo );
		
		$this->display ();
		
	}
	
	public function update()
	{
		$data = M("GoodsTypeAttr")->create ();
		
		// 更新数据
		$list=M("GoodsTypeAttr")->save ($data);
		
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
	
	
	public function delete(){
		//彻底删除指定记录
		$ajax = intval($_REQUEST['ajax']);
		$id = $_REQUEST ['id'];
		if (isset ( $id )) {
			$condition = array ('id' => array ('in', explode ( ',', $id ) ) );
			//删除的验证
			$list = M("GoodsTypeAttr")->where ( $condition )->delete();
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
	
	public function adds() {
		$goods_type_id = intval($_REQUEST ['goods_type_id']);
		$this->assign("goods_type_id",$goods_type_id);
		$this->display ();
	}
	
	public function insert()
	{
		$data = M("GoodsTypeAttr")->create ();
		// 更新数据
		
		$list=M("GoodsTypeAttr")->add ($data);
	
		if (false !== $list) {
			
			save_log($data['name'].L("INSERT_SUCCESS"),1);
			$this->assign("jumpUrl",u("GoodsType/index"));
			$this->success(L("INSERT_SUCCESS"));
		} else {
			//错误提示
			$dbErr = M()->getDbError();
			save_log($data['name'].L("INSERT_FAILED").$dbErr,0);
			$this->error(L("INSERT_FAILED").$dbErr);
		}
		
	}
	
}
?>