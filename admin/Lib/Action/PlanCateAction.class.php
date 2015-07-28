<?php
// +----------------------------------------------------------------------
// | easethink 方维借贷系统
// +----------------------------------------------------------------------
// | Copyright (c) 2011 http://www.fanwe.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 云淡风轻(88522820@qq.com)
// +----------------------------------------------------------------------

class PlanCateAction extends CommonAction{
	public function index()
	{	
		$this->assign("default_map",$condition);
		
		//列表过滤器，生成查询Map对象
		$map = $this->_search ();
		//追加默认参数
		if($this->get("default_map"))
		$map = array_merge($map,$this->get("default_map"));
		
		if (method_exists ( $this, '_filter' )) {
			$this->_filter ( $map );
		}
		
		$model = D ("PlanCate");
		
		if (! empty ( $model )) {
			$this->_list ( $model, $map );
		}
		$list = $this->get("list");
		
		foreach($list as $k=>$v)
		{
			$list[$k]['rate'] = $list[$k]['rate'].'%'; 
			$list[$k]['repay_time'] = to_date($list[$k]['repay_time'],"Y-m-d H:i:s"); 
		
		}
		$this->assign("main_title","计划分类");//L(MODULE_NAME."_INDEX")
		$this->assign("list",$list);
		$this->display ();
		return;
	}
	
	public function add() {
		$this->display ();
	}
	
	public function insert()
	{
		$data = M("PlanCate")->create ();
		
		$data['repay_time']  = to_timespan($_REQUEST['repay_time'],"Y-m-d");
		// 更新数据
		$list = M("PlanCate")->add ($data);
		$plancate_id = $list;
		if (false !== $list){
			//错误提示
			$dbErr = M()->getDbError();
			$this->success ("添加成功");
		}else{
			$this->error(L("INSERT_FAILED"));
		}
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
	
	
	public function edit()
	{
		$id = intval($_REQUEST ['id']);
		$condition['id'] = $id;
		$vo = M("PlanCate")->where($condition)->find();
		$_REQUEST['repay_time'] = to_date($vo['repay_time'],"Y-m-d H:i:s") ;
		$this->assign ( 'vo', $vo );
		$this->display ();
	}
	
	public function update()
	{
		$data = M(MODULE_NAME)->create ();
		if(intval( $data['rate']) <= 0 ){
			$this->error("预期收益率为大于0正数");
		}
		$data['repay_time'] = to_timespan($data['repay_time'],"Y-m-d");
		// 更新数据
		$list=M(MODULE_NAME)->save($data);
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
			$list = M(MODULE_NAME)->where ($condition)->delete();
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
}
?>