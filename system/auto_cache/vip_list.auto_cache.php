<?php
//用户等级
class vip_list_auto_cache extends auto_cache{
	public function load($param)
	{
		$key = $this->build_key(__CLASS__,$param);
		$GLOBALS['cache']->set_dir(APP_ROOT_PATH."public/runtime/data/".__CLASS__."/");
		$vip_list = $GLOBALS['cache']->get($key);
		if($vip_list === false)
		{
			if(intval($param['id']) > 0){
				$ext = " WHERE id= ".intval($param['id']);
			}
			
			$temp_vip_list = $GLOBALS['db']->getAll("select * from ".DB_PREFIX."vip_type $ext order by sort ASC ");
			$vip_list = array();
			foreach($temp_vip_list as $k=>$v){
				$vip_list[$v['id']] = $v;
			}
			
			$GLOBALS['cache']->set_dir(APP_ROOT_PATH."public/runtime/data/".__CLASS__."/");
			$GLOBALS['cache']->set($key,$vip_list);
		}
		return $vip_list;
	}
	public function rm($param)
	{
		$key = $this->build_key(__CLASS__,$param);
		$GLOBALS['cache']->set_dir(APP_ROOT_PATH."public/runtime/data/".__CLASS__."/");
		$GLOBALS['cache']->rm($key);
	}
	public function clear_all()
	{
		$GLOBALS['cache']->set_dir(APP_ROOT_PATH."public/runtime/data/".__CLASS__."/");
		$GLOBALS['cache']->clear();
	}
}
?>