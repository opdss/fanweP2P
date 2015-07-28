<?php
//用户等级
class links_auto_cache extends auto_cache{
	public function load($param)
	{
		$key = $this->build_key(__CLASS__,$param);
		$GLOBALS['cache']->set_dir(APP_ROOT_PATH."public/runtime/data/".__CLASS__."/");
		$f_link_group = $GLOBALS['cache']->get($key);
		if($f_link_group === false)
		{
			$f_link_group = $GLOBALS['db']->getAll("select * from ".DB_PREFIX."link_group where is_effect = 1 order by sort desc");
			foreach($f_link_group as $k=>$v)
			{
				$g_links = $GLOBALS['db']->getAll("select * from ".DB_PREFIX."link where is_effect = 1 and show_index = 1 and group_id = ".$v['id']." order by sort desc");
				if($g_links)
				{
					foreach($g_links as $kk=>$vv)
					{
						if(substr($vv['url'],0,7)=='http://')
						{
							$g_links[$kk]['url'] = str_replace("http://","",$vv['url']);
						}
					}
					$f_link_group[$k]['links'] = $g_links;
				}
				else
					unset($f_link_group[$k]);
			}
			$GLOBALS['cache']->set_dir(APP_ROOT_PATH."public/runtime/data/".__CLASS__."/");
			$GLOBALS['cache']->set($key,$f_link_group);
		}
		return $f_link_group;
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