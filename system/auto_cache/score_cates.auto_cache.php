<?php
//商城的导航
class score_cates_auto_cache extends auto_cache{
	public function load($param)
	{
		$key = $this->build_key(__CLASS__,$param);
		$GLOBALS['cache']->set_dir(APP_ROOT_PATH."public/runtime/data/".__CLASS__."/");
		$cates_list = $GLOBALS['cache']->get($key);
		if($cates_list === false)
		{
			$cates_list = $GLOBALS['db']->getAll("SELECT id,name FROM ".DB_PREFIX."goods_cate WHERE is_effect=1 and is_delete = 0 and pid= 0 order by sort asc");
			/*
			$temp_city_list = $GLOBALS['db']->getAll("select * from ".DB_PREFIX."deal_city where is_effect = 1 AND is_delete=0 order by sort desc");
			
			$cates_list = array();

			foreach($temp_city_list as $k=>$v){
				if($v['pid'] == 0){
					$cates_list[$v['id']] = $v;
				}
				
			}
			foreach($temp_city_list as $k=>$v){
				if($v['pid'] > 0){
					$cates_list[$v['pid']]['child'][$v['id']] = $v;
				}
			}*/
			$GLOBALS['cache']->set_dir(APP_ROOT_PATH."public/runtime/data/".__CLASS__."/");
			$GLOBALS['cache']->set($key,$cates_list);
		}
		return $cates_list;
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