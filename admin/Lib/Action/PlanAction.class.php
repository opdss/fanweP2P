<?php
// +----------------------------------------------------------------------
// | easethink 方维借贷系统
// +----------------------------------------------------------------------
// | Copyright (c) 2011 http://www.fanwe.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 云淡风轻(88522820@qq.com)
// +----------------------------------------------------------------------

class PlanAction extends CommonAction{
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
		$model = D ("Plan");
		if (! empty ( $model )) {
			$this->_list ( $model, $map );
		}
		$list = $this->get("list");
		foreach($list as $k=>$v)
		{
			$list[$k]['plan_cate'] = M("PlanCate")->where("id=".$list[$k]['plan_cate_id'])->getField("name");
			$list[$k]['plan_money'] = format_price($list[$k]['plan_money']);
			$list[$k]['rate'] = $list[$k]['rate']."%";
			$list[$k]['repay_time'] = to_date($list[$k]['repay_time'],"Y-m-d");
			//$list[$k]['repay_time'] = $list[$k]['repay_time']."个月";
			if($list[$k]['warrant'] == 0){
				$list[$k]['warrant'] = "无";
			}elseif ($list[$k]['warrant'] == 1){
				$list[$k]['warrant'] = "本金";
			}elseif ($list[$k]['warrant'] == 2){
				$list[$k]['warrant'] = "本金和利息";
			}
			$list[$k]['min_load_money'] = format_price($list[$k]['min_load_money']);
			$list[$k]['max_load_money'] = format_price($list[$k]['max_load_money']);
			$list[$k]['advance'] = $list[$k]['advance'].'%';
			$list[$k]['start_time'] = to_date($list[$k]['start_time'],"Y-m-d");
			$list[$k]['end_time'] = to_date($list[$k]['end_time'],"Y-m-d");
			$list[$k]['pay_end_time'] = to_date($list[$k]['pay_end_time'],"Y-m-d");
			if($list[$k]['pay_expire_type'] == 0){
				$list[$k]['pay_expire_type'] = "不扣除";
			}elseif ($list[$k]['pay_expire_type'] == 1){
				$list[$k]['pay_expire_type'] = "全额扣除";
			}elseif ($list[$k]['pay_expire_type'] == 2){
				$list[$k]['pay_expire_type'] = "百分比扣除";
			}
			if($list[$k]['repay_status'] == 0){
				$list[$k]['repay_status'] = "还款自动返还";
			}elseif ($list[$k]['repay_status'] == 1){
				$list[$k]['repay_status'] = "转让还返";
			}elseif ($list[$k]['repay_status'] == 2){
				$list[$k]['repay_status'] = "到期返还";
			}
			if($list[$k]['status'] == 1){
				$list[$k]['status'] = "预约中";
			}elseif ($list[$k]['status'] == 2){
				$list[$k]['status'] = "计划满额";
			}elseif ($list[$k]['status'] == 3){
				$list[$k]['status'] = "收益中";
			}else{
				$list[$k]['status'] = "失败";
			}
		}
		//dump($result);exit;
		$this->assign("main_title","计划列表");//L(MODULE_NAME."_INDEX")
		$this->assign("list",$list);
		$this->display ();
		return;
	}
	
	public function add() {
		$plan_cate = M("PlanCate")->where("is_effect=1")->findAll();
		$this->assign("plan_cate",$plan_cate);
		$this->display ();
	}
	
	public function get_repay_time() {
		$plan_id = intval($_REQUEST['plan_cate_id']);
		$repay_time = $GLOBALS['db']->getOne("SELECT repay_time FROM ".DB_PREFIX."plan_cate WHERE is_effect=1 and id=".$plan_id);
		
		if($repay_time !=""){
			$result['repay_time'] = $repay_time;
			$result['status'] = 1;
			ajax_return($result);
		}
		else{
			$result['status'] = 0;
			ajax_return($result);
		}
	}
	
	public function insert() {
		$data = M("Plan")->create ();
		$data['start_time']  = to_timespan($_REQUEST['start_time'],"Y-m-d");
		$data['end_time']  = to_timespan($_REQUEST['end_time'],"Y-m-d");
		$data['pay_end_time']  = to_timespan($_REQUEST['pay_end_time'],"Y-m-d");
		// 更新数据
		$list = M("Plan")->add ($data);
		if (false !== $list){
			//错误提示
			$dbErr = M()->getDbError();
			$this->success ("添加成功");
		}else{
			$this->error(L("INSERT_FAILED"));
		}
	}
	
	public function edit() {
		$plan_cate = M("PlanCate")->where("is_effect=1")->findAll();
		$this->assign("plan_cate",$plan_cate);
		$id = intval($_REQUEST ['id']);
		$condition['id'] = $id;
		$vo = M("Plan")->where($condition)->find();
		$_REQUEST['start_time'] = to_date($vo['start_time'],"Y-m-d H:i:s") ;
		$_REQUEST['end_time'] = to_date($vo['end_time'],"Y-m-d H:i:s") ;
		$_REQUEST['pay_end_time'] = to_date($vo['pay_end_time'],"Y-m-d H:i:s") ;
		$this->assign ( 'vo', $vo );
		$this->display ();
	}
	
	public function update()
	{
		$data = M("Plan")->create ();
		$data['start_time']  = to_timespan($_REQUEST['start_time'],"Y-m-d");
		$data['end_time']  = to_timespan($_REQUEST['end_time'],"Y-m-d");
		$data['pay_end_time']  = to_timespan($_REQUEST['pay_end_time'],"Y-m-d");
		// 更新数据
		$list=M("Plan")->save($data);
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
	
	public function show_plan(){
		$id = intval($_REQUEST ['id']);
		
		$plan_sql = "SELECT * FROM ".DB_PREFIX."plan	where id = ".$id;
		$plan_list = $GLOBALS['db']->getRow($plan_sql);
		if($plan_list['advance']!="")
		$plan_list['advance'] =$plan_list['advance']."%";
		$plan_list['plan_money'] = format_price($plan_list['plan_money']);
		if($plan_list['warrant'] == 0)
		{	$plan_list['warrant'] = "无";
		} elseif ($plan_list['warrant'] == 1){
			$plan_list['warrant'] = "本金";
		}else{
			$plan_list['warrant'] = "本金和利息";
		}
		$plan_list['repay_time'] = to_date($plan_list['repay_time'],"Y-m-d H:i:s");
		$this->assign ( 'plan_list', $plan_list );
		
		
		$sql = "SELECT pl.*,u.user_name,p.name
				FROM ".DB_PREFIX."plan_load pl LEFT JOIN ".DB_PREFIX."plan p on pl.plan_id = p.id 
				LEFT JOIN ".DB_PREFIX."user u on pl.user_id = u.id
				where p.id = ".$id;
		$sql_count = "SELECT count(*) FROM ".DB_PREFIX."plan_load where p.id = ".$id;
		
		$count = $GLOBALS['db']->getAll($sql_count);
		
		//开始list
		if (isset ( $_REQUEST ['_order'] )) {
			$order = $_REQUEST ['_order'];
		} else {
			$order = ! empty ( $sortBy ) ? $sortBy : "id";
		}
		//排序方式默认按照倒序排列
		//接受 sost参数 0 表示倒序 非0都 表示正序
		if (isset ( $_REQUEST ['_sort'] )) {
			$sort = $_REQUEST ['_sort'] ? 'asc' : 'desc';
		} else {
			$sort = $asc ? 'asc' : 'desc';
		}
		//取得满足条件的记录数
		$count = count($count);
		if ($count > 0) {
			//创建分页对象
			if (! empty ( $_REQUEST ['listRows'] )) {
				$listRows = $_REQUEST ['listRows'];
			} else {
				$listRows = '';
			}
			$p = new Page ( $count, $listRows );
			//分页查询数据
			$sql .= " order by `" . $order . "` " . $sort;
			$sql .= " limit ".$p->firstRow . ',' . $p->listRows;
		
			$plan_load = $GLOBALS['db']->getAll($sql);
				
			//			echo $model->getlastsql();
			//分页跳转的时候保证查询条件
			foreach ( $map as $key => $val ) {
				if (! is_array ( $val )) {
					$p->parameter .= "$key=" . urlencode ( $val ) . "&";
				}
			}
			//分页显示
		
			$page = $p->show ();
			//列表排序显示
			$sortImg = $sort; //排序图标
			$sortAlt = $sort == 'desc' ? l("ASC_SORT") : l("DESC_SORT"); //排序提示
			$sort = $sort == 'desc' ? 1 : 0; //排序方式
			//模板赋值显示
			
			foreach ( $plan_load as $k=> $v ){
				$plan_load[$k]['deal_cate_ids'] = $GLOBALS['db']->getOne("select name from fanwe_plan_cate where id = ".$plan_load[$k]['deal_cate_ids']);
			}
			
			$this->assign ( 'plan_load_list', $plan_load );
			$this->assign ( 'sort', $sort );
			$this->assign ( 'order', $order );
			$this->assign ( 'sortImg', $sortImg );
			$this->assign ( 'sortType', $sortAlt );
			$this->assign ( "page", $page );
			$this->assign ( "nowPage",$p->nowPage);
		}
		$this->display ();
		
	}
	
	// u-计划 还款
	function plan_load_repay()
	{
		require_once(APP_ROOT_PATH."app/Lib/common.php");
		require_once(APP_ROOT_PATH."app/Lib/plan_func.php");
		$id = intval($_REQUEST['id']);
		
		if($id==0){
			$this->success("数据错误");
		}
		
		$plan_info = get_plan($id);
		
		if(!$plan_info){
			$this->success("该计划不存在");
		}
	
		$this->assign("plan_info",$plan_info);
		$repay_list  = get_plan_load_list($plan_info);
		
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
	
}
?>