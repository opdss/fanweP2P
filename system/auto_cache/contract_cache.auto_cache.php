<?php
//配送方式记录
class contract_cache_auto_cache extends auto_cache{
	public function load($param)
	{
		$key = $this->build_key(__CLASS__,$param);
		$consignee_id = intval($param['consignee_id']);
		$GLOBALS['cache']->set_dir(APP_ROOT_PATH."public/runtime/data/".__CLASS__."/");
		$contract_data = $GLOBALS['cache']->get($key);
			if($contract_data === false)
			{
				$temp_contract_data =  $GLOBALS['db']->getAll("select * from ".DB_PREFIX."contract where is_effect = 1 and is_delete=0 ");
				$contract_data = array();
				foreach($temp_contract_data as $k=>$v){
					$contract_data[$v['id']] = $v;
				}
				
				$GLOBALS['cache']->set_dir(APP_ROOT_PATH."public/runtime/data/".__CLASS__."/");			
				$GLOBALS['cache']->set($key,$contract_data);
			}
		return $contract_data;	
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