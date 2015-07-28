<?php
//指定文章父分类下子分类树状格式化后的结果
class cache_shop_acate_tree_auto_cache extends auto_cache{
	public function load($param)
	{
		$key = $this->build_key(__CLASS__,$param);
		$GLOBALS['cache']->set_dir(APP_ROOT_PATH."public/runtime/data/".__CLASS__."/");
		$cate_list = $GLOBALS['cache']->get($key);
		if($cate_list===false)
		{
			$pid = intval($param['pid']);
			require_once APP_ROOT_PATH."system/utils/child.php";
			require_once APP_ROOT_PATH."system/utils/tree.php";
			$ids_util = new child("article_cate");
			$ids = $ids_util->getChildIds($pid);
			$ids[] = $pid;
			$cate_list = $GLOBALS['db']->getAll("select * from ".DB_PREFIX."article_cate where is_effect = 1 and is_delete = 0 and id in (".implode(",",$ids).") order by `sort` desc, id desc");
			foreach($cate_list as $k=>$v)
			{
				if($v['uname']!='')
				$curl = url("index","acate",array("id"=>$v['uname']));
				else
				$curl = url("index","acate",array("id"=>$v['id']));
				$cate_list[$k]['url'] = $curl;
			}	
			$tree_util = new tree();
			$cate_list = $tree_util->toFormatTree($cate_list,'name');	
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