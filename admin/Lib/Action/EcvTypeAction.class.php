<?php
// +----------------------------------------------------------------------
// | Fanwe 方维o2o商业系统
// +----------------------------------------------------------------------
// | Copyright (c) 2011 http://www.fanwe.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 云淡风轻(88522820@qq.com)
// +----------------------------------------------------------------------

class EcvTypeAction extends CommonAction{
	public function index()
	{
		parent::index();
	}
	public function add()
	{
		$this->display();
	}
	public function insert() {
		B('FilterString');
		$ajax = intval($_REQUEST['ajax']);
		$data = M(MODULE_NAME)->create ();
		//开始验证有效性
		$this->assign("jumpUrl",u(MODULE_NAME."/add"));
		if(!check_empty($data['name']))
		{
			$this->error(L("VOUCHER_NAME_EMPTY_TIP"));
		}	
		if(doubleval($data['money'])<=0)
		{
			$this->error(L("VOUCHER_MONEY_ERROR_TIP"));
		}	
	
		$data['begin_time'] = trim($data['begin_time'])==''?0:to_timespan($data['begin_time']);
		$data['end_time'] = trim($data['end_time'])==''?0:to_timespan($data['end_time']);
		// 更新数据
		$log_info = $data['name'];
		$list=M(MODULE_NAME)->add($data);
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
		$vo = M(MODULE_NAME)->where($condition)->find();
		$this->assign ( 'vo', $vo );
		$this->display ();
	}
	
	public function update() {
		B('FilterString');
		$data = M(MODULE_NAME)->create ();
				
		$log_info = M(MODULE_NAME)->where("id=".intval($data['id']))->getField("name");
		//开始验证有效性
		$this->assign("jumpUrl",u(MODULE_NAME."/edit",array("id"=>$data['id'])));
		if(!check_empty($data['name']))
		{
			$this->error(L("VOUCHER_NAME_EMPTY_TIP"));
		}	
		if(doubleval($data['money'])<=0)
		{
			$this->error(L("VOUCHER_MONEY_ERROR_TIP"));
		}	
	
		$data['begin_time'] = trim($data['begin_time'])==''?0:to_timespan($data['begin_time']);
		$data['end_time'] = trim($data['end_time'])==''?0:to_timespan($data['end_time']);
		// 更新数据
		$list=M(MODULE_NAME)->save ($data);
		if (false !== $list) {
			//成功提示
			M("Ecv")->where("ecv_type_id=".$data['id'])->setField("use_limit",$data['use_limit']);  //同步可用次数
			M("Ecv")->where("ecv_type_id=".$data['id'])->setField("begin_time",$data['begin_time']);
			M("Ecv")->where("ecv_type_id=".$data['id'])->setField("end_time",$data['end_time']);
			M("Ecv")->where("ecv_type_id=".$data['id'])->setField("money",$data['money']);
			save_log($log_info.L("UPDATE_SUCCESS"),1);
			$this->success(L("UPDATE_SUCCESS"));
		} else {
			//错误提示
			save_log($log_info.L("UPDATE_FAILED"),0);
			$this->error(L("UPDATE_FAILED"),0,$log_info.L("UPDATE_FAILED"));
		}
	}
	
	
	public function foreverdelete() {
		//彻底删除指定记录
		$ajax = intval($_REQUEST['ajax']);
		$id = $_REQUEST ['id'];
		if (isset ( $id )) {
				$condition = array ('id' => array ('in', explode ( ',', $id ) ) );
				if(M("Ecv")->where(array ('ecv_type_id' => array ('in', explode ( ',', $id ) ) ))->count()>0)
				{
					$this->error(l("VOUCHER_EXIST"),$ajax);
				}
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
	
	public function send()
	{
		$id = intval($_REQUEST['id']);
		$ecv_type = M("EcvType")->getById($id);
		if(!$ecv_type)
		{
			$this->error(l("INVALID_ECV_TYPE"));
		}
		$user_group = M("UserGroup")->findAll();
		$this->assign("user_group",$user_group);
		$this->assign("ecv_type",$ecv_type);
		$this->display();
	}	
	
	public function doSend()
	{
		require_once APP_ROOT_PATH."system/libs/voucher.php";
		$ecv_type_id = intval($_REQUEST['ecv_type_id']);
		$need_password = intval($_REQUEST['need_password']);
		$send_type = intval($_REQUEST['send_type']);
		$user_group = intval($_REQUEST['user_group']);
		$user_ids = trim($_REQUEST['user_id']);
		$gen_count = intval($_REQUEST['gen_count']);
		$page = intval($_REQUEST['page'])==0?1:intval($_REQUEST['page']);  //大数据量时的载入的页数
		$page_size = app_conf("BATCH_PAGE_SIZE"); //每次运行的次数， 开发时可根据实际环境改变此大小
		$page_limit = ($page-1)*$page_size.",".$page_size;
		switch($send_type)
		{
			case 0:
				//按会员组
				$user_list = M("User")->where("group_id=".$user_group)->order("id asc")->limit($page_limit)->findAll();
				if($user_list)
				{
					foreach($user_list as $v)
					{
						send_voucher($ecv_type_id,$v['id'],$need_password);
					}
					$this->assign("jumpUrl",u("EcvType/doSend",array("ecv_type_id"=>$ecv_type_id,'need_password'=>$need_password,'send_type'=>$send_type,'user_group'=>$user_group,'user_id'=>$user_ids,'gen_count'=>$gen_count,'page'=>($page+1))));
					$msg = sprintf(l("SEND_VOUCHER_PAGE_SUCCESS"),($page-1)*$page_size,$page*$page_size);
					$this->success($msg);
				}
				else
				{
					save_log("ID".$ecv_type_id.l("VOUCHER_SEND_SUCCESS"),1);
					$this->assign("jumpUrl",u("EcvType/index"));
					$this->success(l("VOUCHER_SEND_SUCCESS"));
				}
				break;
			case 1:
				//按会员ID
				$user_list = M("User")->where("id in(".$user_ids.")")->order("id asc")->limit($page_limit)->findAll();
				if($user_list)
				{
					foreach($user_list as $v)
					{
						send_voucher($ecv_type_id,$v['id'],$need_password);
					}
					$this->assign("jumpUrl",u("EcvType/doSend",array("ecv_type_id"=>$ecv_type_id,'need_password'=>$need_password,'send_type'=>$send_type,'user_group'=>$user_group,'user_id'=>$user_ids,'gen_count'=>$gen_count,'page'=>($page+1))));
					$msg = sprintf(l("SEND_VOUCHER_PAGE_SUCCESS"),($page-1)*$page_size,$page*$page_size);
					$this->success($msg);
				}
				else
				{
					save_log("ID".$ecv_type_id.l("VOUCHER_SEND_SUCCESS"),1);
					$this->assign("jumpUrl",u("EcvType/index"));
					$this->success(l("VOUCHER_SEND_SUCCESS"));
				}
				break;
			case 2:
				//线下
				for($i=0;$i<$page_size;$i++)
				{					
					if(($page-1)*$page_size+$i==$gen_count)
					{
						save_log("ID".$ecv_type_id.l("VOUCHER_SEND_SUCCESS"),1);
						$this->assign("jumpUrl",u("EcvType/index"));
						$this->success(l("VOUCHER_SEND_SUCCESS"));
						break;
					}
					send_voucher($ecv_type_id,0,$need_password);	
				}
				$this->assign("jumpUrl",u("EcvType/doSend",array("ecv_type_id"=>$ecv_type_id,'need_password'=>$need_password,'send_type'=>$send_type,'user_group'=>$user_group,'user_id'=>$user_ids,'gen_count'=>$gen_count,'page'=>($page+1))));
				$msg = sprintf(l("SEND_VOUCHER_PAGE_SUCCESS"),($page-1)*$page_size,$page*$page_size);
				$this->success($msg);
				break;
		}
		
	}
	
	
	
	
}
?>