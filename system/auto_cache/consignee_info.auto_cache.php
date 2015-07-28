<?php
//配送方式记录
class consignee_info_auto_cache extends auto_cache{
	public function load($param)
	{
		$key = $this->build_key(__CLASS__,$param);
		$consignee_id = intval($param['consignee_id']);
		$GLOBALS['cache']->set_dir(APP_ROOT_PATH."public/runtime/data/".__CLASS__."/");
		$consignee_data = $GLOBALS['cache']->get($key);
			if($consignee_data === false)
			{
				$consignee_info =  $consignee_data['consignee_info'] = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."user_consignee where id = ".$consignee_id);
				$region_lv1 = $GLOBALS['db']->getAll("select * from ".DB_PREFIX."delivery_region where pid = 0");  //一级地址
				foreach($region_lv1 as $k=>$v)
				{
					if($v['id'] == $consignee_info['region_lv1'])
					{
						$region_lv1[$k]['selected'] = 1;
						break;
					}
				}
				$consignee_data['region_lv1'] = $region_lv1;
				
				$region_lv2 = $GLOBALS['db']->getAll("select * from ".DB_PREFIX."delivery_region where pid = ".$consignee_info['region_lv1']);  //二级地址
				foreach($region_lv2 as $k=>$v)
				{
					if($v['id'] == $consignee_info['region_lv2'])
					{
						$region_lv2[$k]['selected'] = 1;
						break;
					}
				}
				$consignee_data['region_lv2'] = $region_lv2;
				
				$region_lv3 = $GLOBALS['db']->getAll("select * from ".DB_PREFIX."delivery_region where pid = ".$consignee_info['region_lv2']);  //三级地址
				foreach($region_lv3 as $k=>$v)
				{
					if($v['id'] == $consignee_info['region_lv3'])
					{
						$region_lv3[$k]['selected'] = 1;
						break;
					}
				}
				$consignee_data['region_lv3'] = $region_lv3;
				
				$region_lv4 = $GLOBALS['db']->getAll("select * from ".DB_PREFIX."delivery_region where pid = ".$consignee_info['region_lv3']);  //四级地址
				foreach($region_lv4 as $k=>$v)
				{
					if($v['id'] == $consignee_info['region_lv4'])
					{
						$region_lv4[$k]['selected'] = 1;
						break;
					}
				}
				$consignee_data['region_lv4'] = $region_lv4;	
				$GLOBALS['cache']->set_dir(APP_ROOT_PATH."public/runtime/data/".__CLASS__."/");			
				$GLOBALS['cache']->set($key,$consignee_data);
			}
		return $consignee_data;	
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