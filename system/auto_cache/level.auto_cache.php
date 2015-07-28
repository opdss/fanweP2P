<?php
//用户等级
class level_auto_cache extends auto_cache{
	public function load($param)
	{
		$key = $this->build_key(__CLASS__,$param);
		$GLOBALS['cache']->set_dir(APP_ROOT_PATH."public/runtime/data/".__CLASS__."/");
		$level_list = $GLOBALS['cache']->get($key);
		if($level_list === false)
		{
			$level_list['list'] = $GLOBALS['db']->getAll("select * from ".DB_PREFIX."user_level order by point desc");
			foreach($level_list['list'] as $k=>$v){
				$level_list['point'][$v['id']] = $v['point'];
				$level_list['services_fee'][$v['id']] = $v['services_fee'];
				$level_list['enddate'][$v['id']] = $v['enddate'];
				$level_list['enddate_list'][$v['id']] = explode(",",$v['enddate']);
				$level_list['repaytime'][$v['id']] = $v['repaytime'];
				if($v['repaytime']){
					$repaytime_list = explode("\n",$v['repaytime']);
					foreach($repaytime_list as $kkkk=>$vvv)
					{
						if(explode("|",$vvv))
							$level_list['repaytime_list'][$v['id']][] =explode("|",str_replace("\r","",$vvv));
					}
					unset($repaytime_list);
				}
			}
			$GLOBALS['cache']->set_dir(APP_ROOT_PATH."public/runtime/data/".__CLASS__."/");
			$GLOBALS['cache']->set($key,$level_list);
		}
		return $level_list;
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