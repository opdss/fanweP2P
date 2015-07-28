<?php
//指定父分类下子分类树状格式化后的结果
class cache_deal_cate_auto_cache extends auto_cache{
	public function load()
	{
		$key = $this->build_key(__CLASS__);
		$GLOBALS['cache']->set_dir(APP_ROOT_PATH."public/runtime/data/".__CLASS__."/");
		$cate_list = $GLOBALS['cache']->get($key);
		if($cate_list===false)
		{
			$cate_list = $GLOBALS['db']->getAll("select * from ".DB_PREFIX."deal_cate where is_effect = 1 and is_delete = 0 ");
			
			$GLOBALS['cache']->set_dir(APP_ROOT_PATH."public/runtime/data/".__CLASS__."/");
			$GLOBALS['cache']->set($key,$cate_list);
		}
		return $cate_list;
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