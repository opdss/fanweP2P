<?php
// +----------------------------------------------------------------------
// | Fanwe 方维p2p借贷系统
// +----------------------------------------------------------------------
// | Copyright (c) 2011 http://www.fanwe.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 云淡风轻(88522820@qq.com)
// +----------------------------------------------------------------------

class UserLevelAction extends CommonAction{
	public function index()
	{
		$model = D ("UserLevel");
		if (! empty ( $model )) {
			$this->_list ( $model, array() );
		}
		
		$list = $this->get("list");
		foreach($list as $k=>$v){
			$r = "";
			$str_arr = explode("\n",$v['repaytime']);
			foreach($str_arr as $kk=>$vv){
				$v_arr = explode("|",$vv);
				$r.="期限:".$v_arr[0].((int)$v_arr[1] == 0 ? "天":"月").",最小利率:".$v_arr[2].",最大利率:".$v_arr[3]."<br>";
			}
			$list[$k]['repaytime'] = $r;
		}
		$this->assign ( 'list', $list );
		$this->display();
	}
	public function add()
	{
		$this->display();
	}
	public function edit() 
	{		
		$id = intval($_REQUEST ['id']);
		$condition['id'] = $id;		
		$vo = M(MODULE_NAME)->where($condition)->find();
		$vo_list = explode("\n",$vo['repaytime']);
		foreach($vo_list as $k=>$v){
			$vo_list[$k] = explode("|",$v);
		}
		$vo['repaytime_list'] = $vo_list;
		
		$this->assign ( 'vo', $vo );
		$this->display();
	}
		
	public function foreverdelete() {
		//彻底删除指定记录
		$ajax = intval($_REQUEST['ajax']);
		$id = $_REQUEST ['id'];
		if (isset ( $id )) {
				$condition = array ('id' => array ('in', explode ( ',', $id ) ) );
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
	
	public function insert() {
		B('FilterString');
		$ajax = intval($_REQUEST['ajax']);
		$data = M(MODULE_NAME)->create ();

		//开始验证有效性
		$this->assign("jumpUrl",u(MODULE_NAME."/add"));
		if(!check_empty($data['name']))
		{
			$this->error("等级名称不能为空");
		}	

		// 更新数据
		$log_info = $data['name'];
		$data['repaytime'] = $this->GroupRepyTime();
		$list=M(MODULE_NAME)->add($data);
		if (false !== $list) {
			//成功提示
			save_log($log_info.L("INSERT_SUCCESS"),1);
			$this->success(L("INSERT_SUCCESS"));
		} else {
			//错误提示
			$dbErr = M()->getDbError();
			save_log($log_info.L("INSERT_FAILED").$dbErr,0);
			$this->error(L("INSERT_FAILED").$dbErr);
		}
	}	
	
	public function update() {
		B('FilterString');
		$data = M(MODULE_NAME)->create ();
		
		$log_info = M(MODULE_NAME)->where("id=".intval($data['id']))->getField("name");
		//开始验证有效性
		$this->assign("jumpUrl",u(MODULE_NAME."/edit",array("id"=>$data['id'])));
		if(!check_empty($data['name']))
		{
			$this->error(L("等级名称不能为空"));
		}	
		$data['repaytime'] = $this->GroupRepyTime();
		// 更新数据
		$list=M(MODULE_NAME)->save ($data);
		if (false !== $list) {
			//成功提示
			save_log($log_info.L("UPDATE_SUCCESS"),1);
			$this->success(L("UPDATE_SUCCESS"));
		} else {
			//错误提示
			$dbErr = M()->getDbError();
			save_log($log_info.L("UPDATE_FAILED").$dbErr,0);
			$this->error(L("UPDATE_FAILED").$dbErr);
		}
	}
	
	private static function GroupRepyTime(){
		$repaytime = "";
		foreach($_REQUEST['repaytime'] as $k => $v){
			if((int)$v['time'] > 0){
				$repaytime .=(int)$v['time']."|".(int)$v['timetype']."|".(float)$v['minrate']."|".(float)$v['maxrate']."\n";
			}
		}
		$repaytime = substr($repaytime,0,strlen($repaytime)-1);
		return $repaytime;
	}


}
?>