<?php
//商城的导航
class loantype_list_auto_cache extends auto_cache{
	public function load($param)
	{
		$key = $this->build_key(__CLASS__,$param);
		$GLOBALS['cache']->set_dir(APP_ROOT_PATH."public/runtime/data/".__CLASS__."/");
		$loantype_list = $GLOBALS['cache']->get($key);
		if($loantype_list === false)
		{
			$loantype_list = array();
			$directory = APP_ROOT_PATH."system/loantype/";
			$read_modules = true;
			$dir = @opendir($directory);
		    $modules     = array();
		
		    while (false !== ($file = @readdir($dir)))
		    {
		        if (preg_match("/^.*?\.php$/", $file))
		        {
		            $modules[] = require_once($directory .$file);
		        }
		    }
		    
		    @closedir($dir);
		    unset($read_modules);
		
		    foreach ($modules AS $key => $value)
		    {
		        $loantype_list[$value['key']] = $value;
		        $loantype_list[$value['key']]['repay_time_type_str'] = implode(",",$value['repay_time_type']);
		    }
		    unset($modules);
		    ksort($loantype_list);
			$GLOBALS['cache']->set_dir(APP_ROOT_PATH."public/runtime/data/".__CLASS__."/");
			$GLOBALS['cache']->set($key,$loantype_list);
		}
		return $loantype_list;
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