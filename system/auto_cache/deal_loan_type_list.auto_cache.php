<?php
//商城的导航
class deal_loan_type_list_auto_cache extends auto_cache{
	public function load($param)
	{
		$key = $this->build_key(__CLASS__,$param);
		$GLOBALS['cache']->set_dir(APP_ROOT_PATH."public/runtime/data/".__CLASS__."/");
		$loan_type_list = $GLOBALS['cache']->get($key);
		if($loan_type_list === false)
		{
			$ext = "";
			if($param['id'] > 0)
			{
				$ext = " and id=".$param['id'];
			}
			$t_loan_type_list = $GLOBALS['db']->getAll("select * from ".DB_PREFIX."deal_loan_type where is_effect = 1 and is_delete =0 $ext order by sort desc");
			$loan_type_list = array();
			foreach($t_loan_type_list as $k=>$v){
				$loan_type_list[$v['id']] = $v;
			}
			$GLOBALS['cache']->set_dir(APP_ROOT_PATH."public/runtime/data/".__CLASS__."/");
			$GLOBALS['cache']->set($key,$loan_type_list);
		}
		return $loan_type_list;
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