<?php
//用户扩展字段
class user_field_list_auto_cache extends auto_cache{
	public function load($param)
	{
		$key = $this->build_key(__CLASS__,$param);
		$GLOBALS['cache']->set_dir(APP_ROOT_PATH."public/runtime/data/".__CLASS__."/");
		$field_list = $GLOBALS['cache']->get($key);
		if($field_list === false)
		{
			$field_list = $GLOBALS['db']->getAll("select * from ".DB_PREFIX."user_field order by sort desc");
			
			foreach($field_list as $k=>$v)
			{
				$field_list[$k]['value_scope'] = preg_split("/[ ,]/i",$v['value_scope']);
			}
			$GLOBALS['cache']->set_dir(APP_ROOT_PATH."public/runtime/data/".__CLASS__."/");
			$GLOBALS['cache']->set($key,$field_list);
		}
		return $field_list;
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