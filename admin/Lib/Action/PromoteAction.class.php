<?php
// +----------------------------------------------------------------------
// | Fanwe 方维p2p借贷系统
// +----------------------------------------------------------------------
// | Copyright (c) 2011 http://www.fanwe.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 云淡风轻(88522820@qq.com)
// +----------------------------------------------------------------------

class PromoteAction extends CommonAction{
	private function read_modules()
	{
		$directory = APP_ROOT_PATH."system/promote/";
		$read_modules = true;
		$dir = @opendir($directory);
	    $modules     = array();
	
	    while (false !== ($file = @readdir($dir)))
	    {
	        if (preg_match("/^.*?\.php$/", $file))
	        {
	            $modules[] = require_once($directory.$file);
	        }
	    }
	    @closedir($dir);
	    unset($read_modules);
	
	    foreach ($modules AS $key => $value)
	    {
	        ksort($modules[$key]);
	    }
	    ksort($modules);
	
	    return $modules;
	}
	public function index()
	{
		$modules = $this->read_modules();
		$db_modules = $GLOBALS['db']->getAll("select * from ".DB_PREFIX."promote");
		foreach($modules as $k=>$v)
		{
			foreach($db_modules as $kk=>$vv)
			{
				if($v['class_name']==$vv['class_name'])
				{
					//已安装
					$modules[$k]['id'] = $vv['id'];
					$modules[$k]['installed'] = 1;
					$modules[$k]['sort'] = $vv['sort'];
					break;
				}
			}
			if($modules[$k]['installed'] != 1)
			$modules[$k]['installed'] = 0;			
			$modules[$k]['sort'] = intval($modules[$k]['sort']);
			
		}
		$this->assign("promote_list",$modules);
		$this->display();
	}
	
	public function install()
	{
		$class_name = $_REQUEST['class_name'];
		$directory = APP_ROOT_PATH."system/promote/";
		$read_modules = true;
		
		$file = $directory.$class_name."_promote.php";
		if(file_exists($file))
		{
			$module = require_once($file);
			$rs = M("Promote")->where("class_name = '".$class_name."'")->count();
			if($rs > 0)
			{
				$this->error(l("PAYMENT_INSTALLED"));
			}
		}
		else
		{
			$this->error(l("INVALID_OPERATION"));
		}
		
		$data['name'] = $module['name'];
		$data['class_name'] = $module['class_name'];
		$data['lang'] = $module['lang'];
		$data['config'] = $module['config'];
		$data['sort'] = (M("Promote")->max("sort") + 1);	

		$this->assign("data",$data);

		$this->display();
	}
	
	public function insert()
	{
		$data = M(MODULE_NAME)->create ();
		$data['config'] = serialize($_REQUEST['config']);
		// 更新数据
		$log_info = $data['class_name'];
		$list=M(MODULE_NAME)->add($data);
		$this->assign("jumpUrl",u(MODULE_NAME."/index"));
		if (false !== $list) {
			//成功提示
			save_log($log_info.L("INSTALL_SUCCESS"),1);
			$this->success(L("INSTALL_SUCCESS"));
		} else {
			//错误提示
			save_log($log_info.L("INSTALL_FAILED"),0);
			$this->error(L("INSTALL_FAILED"));
		}		
	}
	
	public function edit() {		
		$id = intval($_REQUEST ['id']);
		$condition['id'] = $id;		
		$vo = M(MODULE_NAME)->where($condition)->find();
		
		$directory = APP_ROOT_PATH."system/promote/";
		$read_modules = true;
		
		$file = $directory.$vo['class_name']."_promote.php";
		if(file_exists($file))
		{
			$module = require_once($file);
		}
		else
		{
			$this->error(l("INVALID_OPERATION"));
		}
		
		$vo['config'] = unserialize($vo['config']);
		
		$data['lang'] = $module['lang'];
		$data['config'] = $module['config'];
		$vo['name'] = $module['name'];
		$this->assign ( 'vo', $vo );
		$this->assign ( 'data', $data );
		$this->display ();
	}
	
	
	public function update()
	{
		$data = M(MODULE_NAME)->create ();
		$data['config'] = serialize($_REQUEST['config']);
		$log_info = M(MODULE_NAME)->where("id=".intval($data['id']))->getField("class_name");

		$this->assign("jumpUrl",u(MODULE_NAME."/edit",array("id"=>$data['id'])));
		
		// 更新数据
		$list=M(MODULE_NAME)->save ($data);
		if (false !== $list) {
			//成功提示
			save_log($log_info.L("UPDATE_SUCCESS"),1);
			$this->success(L("UPDATE_SUCCESS"));
		} else {
			//错误提示
			save_log($log_info.L("UPDATE_FAILED"),0);
			$this->error(L("UPDATE_FAILED"),0,$log_info.L("UPDATE_FAILED"));
		}
	}
	
	public function uninstall()
	{
		$ajax = intval($_REQUEST['ajax']);
		$id = intval($_REQUEST ['id']);
		$data = M(MODULE_NAME)->getById($id);
		if($data)
		{
			$info = $data['class_name'];
			$list = M(MODULE_NAME)->where ( array('id'=>$data['id']) )->delete();	
			if ($list!==false) {
					save_log($info.l("UNINSTALL_SUCCESS"),1);
					$this->success (l("UNINSTALL_SUCCESS"),$ajax);
				} else {
					save_log($info.l("UNINSTALL_FAILED"),0);
					$this->error (l("UNINSTALL_FAILED"),$ajax);
				}
		}
		else
		{
			$this->error (l("INVALID_OPERATION"),$ajax);
		}
	}

	public function set_sort()
	{
		$id = intval($_REQUEST['id']);
		$sort = intval($_REQUEST['sort']);
		$log_info = M("Promote")->where("id=".$id)->getField("class_name");
		if(!check_sort($sort))
		{
			$this->error(l("SORT_FAILED"),1);
		}
		M("Promote")->where("id=".$id)->setField("sort",$sort);
		save_log($log_info.l("SORT_SUCCESS"),1);
		$this->success(l("SORT_SUCCESS"),1);
	}
}
?>