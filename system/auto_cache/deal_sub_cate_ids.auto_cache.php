<?php
//生活服务分类父类下的所有子类ID,不包含父分类
class deal_sub_cate_ids_auto_cache extends auto_cache{
	public function load($param)
	{
		$key = $this->build_key(__CLASS__,$param);
		$GLOBALS['cache']->set_dir(APP_ROOT_PATH."public/runtime/data/".__CLASS__."/");
		$ids = $GLOBALS['cache']->get($key);
			if($ids === false)
			{
				$cate_id = intval($param['cate_id']);
				$ids_util = new ChildIds("deal_cate");
				$ids = $ids_util->getChildIds($cate_id);
				$ids[] = $cate_id;
				$GLOBALS['cache']->set_dir(APP_ROOT_PATH."public/runtime/data/".__CLASS__."/");
				$GLOBALS['cache']->set($key,$ids);
			}
			return $ids;
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