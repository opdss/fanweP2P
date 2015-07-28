<?php
// +----------------------------------------------------------------------
// | Fanwe 方维p2p借贷系统
// +----------------------------------------------------------------------
// | Copyright (c) 2011 http://www.fanwe.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 云淡风轻(88522820@qq.com)
// +----------------------------------------------------------------------

class ExpressAction extends CommonAction{
	private function read_modules()
	{
		$directory = APP_ROOT_PATH."system/express/";
		$read_modules = true;
		$dir = @opendir($directory);
	    $modules     = array();
	
	    while (false !== ($file = @readdir($dir)))
	    {
	        if (preg_match("/^.*?\.php$/", $file))
	        {
	            $modules[] = require_once($directory .$file);
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
		$db_modules = $GLOBALS['db']->getAll("select * from ".DB_PREFIX."express");
		foreach($modules as $k=>$v)
		{
			foreach($db_modules as $kk=>$vv)
			{
				if($v['class_name']==$vv['class_name'])
				{
					//已安装
					$modules[$k]['id'] = $vv['id'];
					$modules[$k]['installed'] = 1;
					$modules[$k]['is_effect'] = $vv['is_effect'];
					break;
				}
			}
			
			if($modules[$k]['installed'] != 1)
			$modules[$k]['installed'] = 0;
		
		}
		$this->assign("express_list",$modules);
		$this->display();
	}
	
	public function install()
	{
		$class_name = $_REQUEST['class_name'];
		$directory = APP_ROOT_PATH."system/express/";
		$read_modules = true;
		
		$file = $directory.$class_name."_express.php";
		if(file_exists($file))
		{
			$module = require_once($file);
			$rs = M("Express")->where("class_name = '".$class_name."'")->count();
			if($rs > 0)
			{
				$this->error(l("EXPRESS_INSTALLED"));
			}
		}
		else
		{
			$this->error(l("INVALID_OPERATION"));
		}
		
		//开始插入数据
		$data['name'] = $module['name'];
		$data['class_name'] = $module['class_name'];
		$data['lang'] = $module['lang'];
		$data['config'] = $module['config'];
		$data['print_tmpl'] = $GLOBALS['tmpl']->fetch("str:".file_get_contents(APP_ROOT_PATH."system/express/".$module['class_name']."_express.html"));

		$this->assign("data",$data);

		$this->display();
		
	}
	
	public function insert()
	{
		$data = M(MODULE_NAME)->create ();
		$data['config'] = serialize($_REQUEST['config']);
		// 更新数据
		$log_info = $data['name'];
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
		
		$directory = APP_ROOT_PATH."system/express/";
		$read_modules = true;
		
		$file = $directory.$vo['class_name']."_express.php";
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

		
		$this->assign ( 'vo', $vo );
		$this->assign ( 'data', $data );
		$this->display ();
	}
	
	public function update()
	{
		$data = M(MODULE_NAME)->create ();
		$data['config'] = serialize($_REQUEST['config']);
		$log_info = M(MODULE_NAME)->where("id=".intval($data['id']))->getField("name");

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
			$info = $data['name'];
			$list = M(MODULE_NAME)->where ( array('id'=>$data['id']) )->delete();	
			M("DeliveryNotice")->where("express_id=".$data['id'])->setField("express_id",0);
			$this->success (l("UNINSTALL_SUCCESS"),$ajax);
		}
		else
		{
			$this->error (l("INVALID_OPERATION"),$ajax);
		}
	}
	
	//快递单打印
	public function eprint()
	{
		$order_ids = $_REQUEST['order_id'];		
		$order_ids = explode(",",$order_ids);

		$express_id = intval($_REQUEST['express_id']);
		$express_sn = trim($_REQUEST['express_sn']);
		
		$express = M("Express")->where("is_effect = 1 and id = ".$express_id)->find();
		if($express)
		{
			$directory = APP_ROOT_PATH."system/express/";
			$file = $directory.$express['class_name']."_express.php";			
			require_once $file;
			$class_name = $express['class_name']."_express";
			$express_module = new $class_name;
			$html = "<body style='margin:0px; padding:0px;'>";
			foreach($order_ids as $order_id)
			{
				if($express_sn=='')
				{
					$delivery_notices = $GLOBALS['db']->getAll("select * from ".DB_PREFIX."delivery_notice as dn left join ".DB_PREFIX."deal_order_item as doi on dn.order_item_id = doi.id where doi.order_id = ".$order_id." and dn.express_id = ".$express_id." group by dn.notice_sn order by dn.delivery_time desc");
					
					$prev_item = array();
					foreach($delivery_notices as $item)
					{					
						if(intval($item['order_item_id']) != intval($prev_item['order_item_id']))	
						$html .= $express_module->get_express_form($order_id,$item['notice_sn']);						
						$prev_item = $item;
					}					
				}
				else
				$html .= $express_module->get_express_form($order_id,$express_sn);
			}
		
			$html.="</div>";
			$this->assign("html",$html);
			$this->display();
		}
		else
		{
			$this->error (l("INVALID_OPERATION"));
		}
	}
	
	
	
}
?>