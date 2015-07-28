<?php
//商城的导航
class deal_city_auto_cache extends auto_cache{
	public function load($param)
	{
		$key = $this->build_key(__CLASS__,$param);
		$GLOBALS['cache']->set_dir(APP_ROOT_PATH."public/runtime/data/".__CLASS__."/");
		$city_list = $GLOBALS['cache']->get($key);
		if($city_list === false)
		{
			$temp_city_list = $GLOBALS['db']->getAll("select * from ".DB_PREFIX."deal_city where is_effect = 1 AND is_delete=0 order by sort desc");
			$city_list = array();
			foreach($temp_city_list as $k=>$v){
				if($v['pid'] == 0){
					$city_list[$v['id']] = $v;
				}
				
			}
			foreach($temp_city_list as $k=>$v){
				if($v['pid'] > 0){
					$city_list[$v['pid']]['child'][$v['id']] = $v;
				}
			}
			$GLOBALS['cache']->set_dir(APP_ROOT_PATH."public/runtime/data/".__CLASS__."/");
			$GLOBALS['cache']->set($key,$city_list);
		}
		return $city_list;
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