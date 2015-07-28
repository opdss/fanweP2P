<?php
// +----------------------------------------------------------------------
// | easethink 方维借贷系统
// +----------------------------------------------------------------------
// | Copyright (c) 2011 http://www.fanwe.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 云淡风轻(88522820@qq.com)
// +----------------------------------------------------------------------

class GoodsOrderAction extends CommonAction{
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
		
		$model = D ("GoodsOrder");
		if (! empty ( $model )) {
			$this->_list ( $model, $map );
		}
		$list = $this->get("list");
		
		$result = array();
		$row = 0;
		foreach($list as $k=>$v)
		{
			if($list[$k]['is_delivery'] == 0)
			{	$list[$k]['is_delivery_format'] = "否";}
			else{
				$list[$k]['is_delivery_format'] = "是";
			}
			if($list[$k]['order_status'] == 0){
				$list[$k]['order_status_format'] = "未发货";
			}elseif($list[$k]['order_status'] == 1){
				$list[$k]['order_status_format'] = "已发货";
			}elseif($list[$k]['order_status'] == 2){
				$list[$k]['order_status_format'] = "无效订单";
			}elseif($list[$k]['order_status'] == 3){
				$list[$k]['order_status_format'] = "用户删除";
			}
			$list[$k]['user_name'] = get_user_name($list[$k]['user_id']);
		}
		$this->assign("list",$list);
		$this->display ();
		return;
	}
	
	
	public function view_order()
	{
		$id = intval($_REQUEST ['id']);
		$list = M("GoodsOrder")->where("id =".$id)->find();
	
		if($list['is_delivery'] == 0)
		{	$list['is_delivery_format'] = "否";}
		else{
			$list['is_delivery_format'] = "是";
		}
		if($list['order_status'] == 0){
			$list['order_status_format'] = "未发货";
		}elseif($list['order_status'] == 1){
			$list['order_status_format'] = "已发货";
		}elseif($list['order_status'] == 2){
			$list['order_status_format'] = "无效订单";
		}elseif($list['order_status'] == 3){
			$list['order_status_format'] = "用户删除";
		}
		
		$list['ex_time'] = to_date( $list['ex_time'],"Y-m-d H:i:s");
		$list['delivery_time'] = to_date( $list['delivery_time'],"Y-m-d H:i:s");
		$list['user_name'] = get_user_name($list['user_id']);
		
		$list['attr_format'] = unserialize($list['attr']);
		foreach($list['attr_format'] as $kk=>$vv){
			$attr_str .= $GLOBALS['db']->getOne("select name from ".DB_PREFIX."goods_type_attr where id =".$kk );
			$attr_str .=":";
			$attr_str .= $GLOBALS['db']->getOne("select name from ".DB_PREFIX."goods_attr where id =".$vv );
			$attr_str .="  ";
		}
		$list['attr_str'] = $attr_str;
		
		$this->assign ( 'list', $list );
		$this->display ();
	}
	
	public function update()
	{
		$data = M("GoodsOrder")->create();
		
		if($data['delivery_sn']=="" && $data['is_delivery'] == 1){
			$this->error("请填写快递单号",0,$data['name'].L("UPDATE_FAILED"));
		}
		if($data['delivery_addr']=="" && $data['is_delivery'] == 1){
			$this->error("配送地址不能为空",0,$data['name'].L("UPDATE_FAILED"));
		}
		$data['order_status'] = 1;
		$data['delivery_time'] = TIME_UTC;
		$data['delivery_date'] =  to_date(TIME_UTC,"Y-m-d");
		
		$delivery_addr = strim($_REQUEST ['delivery_addr']);
		$delivery_sn = strim($_REQUEST ['delivery_sn']);
		
		// 更新数据
		$list=M("GoodsOrder")->save($data);
		
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
	
	//取消订单
	public function quxiao()
	{
		$id = intval($_REQUEST ['id']);
		$user_id = intval($_REQUEST ['user_id']);
		$total_score = intval($_REQUEST ['total_score']);
		$data = M("GoodsOrder")->create();
		$data['order_status'] = 2;
		$data['id'] = $id;
		// 更新数据
		$list=M("GoodsOrder")->save($data);
		if (false !== $list) {
			$return['info'] = "订单取消成功";
			$return['status'] = 1;
			require_once APP_ROOT_PATH."system/libs/user.php";
			modify_account(array('score'=>$total_score),$user_id,"取消订单积分返还",22);
			$this->success(L("UPDATE_SUCCESS"));
		} else {
			$return['info'] = "订单取消失败";
			$return['status'] = 0;
		}
		ajax_return($return);
	}
	
	public function del_order()
	{
		//删除指定记录
		$ajax = intval($_REQUEST['ajax']);
		$id = intval($_REQUEST ['id']);
		if (isset ( $id )) {
			$condition = array ('id' => array ('in', explode ( ',', $id ) ) );
			//删除的验证
			
			$rel_data = M("GoodsOrder")->where($condition)->findAll();
			foreach($rel_data as $data)
			{
				$info[] = $data['order_sn'];
			}
			if($info) $info = implode(",",$info);
			$list = M("GoodsOrder")->where ( $condition )->delete();
				
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
	
	public function export_csv($page = 1)
	{
		set_time_limit(0);
		$limit = (($page - 1)*intval(app_conf("BATCH_PAGE_SIZE"))).",".(intval(app_conf("BATCH_PAGE_SIZE")));
	
		//定义条件
		
		
		
		$list = M("GoodsOrder")
		->where($map)
		->limit($limit)->findAll();
		
		foreach($list as $k=>$v)
		{
			if($list[$k]['is_delivery'] == 0)
			{	$list[$k]['is_delivery_format'] = "否";}
			else{
				$list[$k]['is_delivery_format'] = "是";
			}
			if($list[$k]['order_status'] == 0){
				$list[$k]['order_status_format'] = "未发货";
			}elseif($list[$k]['order_status'] == 1){
				$list[$k]['order_status_format'] = "已发货";
			}elseif($list[$k]['order_status'] == 2){
				$list[$k]['order_status_format'] = "无效订单";
			}elseif($list[$k]['order_status'] == 3){
				$list[$k]['order_status_format'] = "用户删除";
			}
			$list[$k]['user_name'] = get_user_name_reals($list[$k]['user_id']);
		}
		
		if($list)
		{
			register_shutdown_function(array(&$this, 'export_csv'), $page+1);
				
			$order_list = array('id'=>'""','order_sn'=>'""','goods_name'=>'""','user_name'=>'""','total_score'=>'""','ex_time'=>'""','delivery_time'=>'""','order_status_format'=>'""','is_delivery_format'=>'""');
			if($page == 1)
				$content = iconv("utf-8","gbk","编号,订单号,商品名称,会员名,所需积分,兑换时间,发货时间,订单状态,是否配送");
	
			if($page==1)
				$content = $content . "\n";
	
			foreach($list as $k=>$v)
			{
				$order_list = array();
				$order_list['id'] = iconv('utf-8','gbk','"' . $v['id'] . '"');
				$order_list['order_sn'] = iconv('utf-8','gbk','"' . $v['order_sn'] . '"');
				$order_list['goods_name'] = iconv('utf-8','gbk','"' . $v['goods_name'] . '"');
				$order_list['user_name'] = iconv('utf-8','gbk','"' . $v['user_name'] . '"');
				$order_list['total_score'] = iconv('utf-8','gbk','"' . $v['total_score'] . '"');
				$order_list['ex_time'] = iconv('utf-8','gbk','"' . to_date($v['ex_time']) . '"');
				$order_list['delivery_time'] = iconv('utf-8','gbk','"' . to_date($v['delivery_time']) . '"');
				$order_list['order_status_format'] = iconv('utf-8','gbk','"' . $v['order_status_format'] . '"');
				$order_list['is_delivery_format'] = iconv('utf-8','gbk','"' . $v['is_delivery_format'] . '"');
	
				
				$content .= implode(",", $order_list) . "\n";
			}
				
				
			header("Content-Disposition: attachment; filename=order_list.csv");
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