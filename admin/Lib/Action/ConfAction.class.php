<?php
// +----------------------------------------------------------------------
// | Fanwe 方维p2p借贷系统
// +----------------------------------------------------------------------
// | Copyright (c) 2011 http://www.fanwe.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 云淡风轻(88522820@qq.com)
// +----------------------------------------------------------------------

class ConfAction extends CommonAction{
	public function index()
	{
		$conf_res = M("Conf")->where("is_effect = 1 and is_conf = 1")->order("group_id asc,sort asc")->findAll();
		foreach($conf_res as $k=>$v)
		{
			$v['value'] = htmlspecialchars($v['value']);
			if($v['name']=='TEMPLATE')
			{
				
				//输出现有模板文件夹
				$directory = APP_ROOT_PATH."app/Tpl/";
				$dir = @opendir($directory);
			    $tmpls     = array();
			
			    while (false !== ($file = @readdir($dir)))
			    {
			    	if($file!='.'&&$file!='..')
			        $tmpls[] = $file;
			    }
			    @closedir($dir);
				//end
				
				$v['input_type'] = 1;
				$v['value_scope'] = $tmpls;
			}
			elseif($v['name']=='SHOP_LANG')
			{
				//输出现有语言包文件夹
				$directory = APP_ROOT_PATH."app/Lang/";
				$dir = @opendir($directory);
			    $tmpls     = array();
			
			    while (false !== ($file = @readdir($dir)))
			    {
			    	if($file!='.'&&$file!='..')
			        $tmpls[] = $file;
			    }
			    @closedir($dir);
				//end
				
				$v['input_type'] = 1;
				$v['value_scope'] = $tmpls;
			}
			else
			$v['value_scope'] = explode(",",$v['value_scope']);
			$conf[$v['group_id']][] = $v;
		}
		$this->assign("conf",$conf);
		$this->display();
	}
	
	public function money_index()
	{
		$conf_res = M("Conf")->where("is_effect = 1 and `name` in ('OPEN_IPS','IPS_MERCODE','IPS_KEY','IPS_3DES_KEY','IPS_3DES_IV','IPS_FEE_TYPE')")->order("group_id asc,sort asc")->findAll();

		foreach($conf_res as $k=>$v)
		{
			$v['value'] = htmlspecialchars($v['value']);
			if($v['name']=='TEMPLATE')
			{
				
				//输出现有模板文件夹
				$directory = APP_ROOT_PATH."app/Tpl/";
				$dir = @opendir($directory);
			    $tmpls     = array();
			
			    while (false !== ($file = @readdir($dir)))
			    {
			    	if($file!='.'&&$file!='..')
			        $tmpls[] = $file;
			    }
			    @closedir($dir);
				//end
				
				$v['input_type'] = 1;
				$v['value_scope'] = $tmpls;
			}
			elseif($v['name']=='SHOP_LANG')
			{
				//输出现有语言包文件夹
				$directory = APP_ROOT_PATH."app/Lang/";
				$dir = @opendir($directory);
			    $tmpls     = array();
			
			    while (false !== ($file = @readdir($dir)))
			    {
			    	if($file!='.'&&$file!='..')
			        $tmpls[] = $file;
			    }
			    @closedir($dir);
				//end
				
				$v['input_type'] = 1;
				$v['value_scope'] = $tmpls;
			}
			else
			$v['value_scope'] = explode(",",$v['value_scope']);
			$conf[$v['group_id']][] = $v;
		}
		$this->assign("conf",$conf);
		$this->assign("main_title","资金托管配置");
		$this->display();
	}
	
	public function update_money()
	{
		$conf_res = M("Conf")->where("is_effect = 1 and name in ('OPEN_IPS','IPS_MERCODE','IPS_KEY','IPS_3DES_KEY','IPS_3DES_IV','IPS_FEE_TYPE')")->findAll();
		foreach($conf_res as $k=>$v)
		{
			conf($v['name'],$_REQUEST[$v['name']]);
			if($v['name']=='URL_MODEL'&&$v['value']!=$_REQUEST[$v['name']])
			{
				clear_auto_cache("byouhui_filter_nav_cache");
				clear_auto_cache("cache_shop_acate_tree");
				clear_auto_cache("cache_shop_cate_tree");
				clear_auto_cache("cache_youhui_cate_tree");
				clear_auto_cache("city_list_result");
				clear_auto_cache("fyouhui_filter_nav_cache");
				clear_auto_cache("get_help_cache");
				clear_auto_cache("page_image");
				clear_auto_cache("tuan_filter_nav_cache");
				clear_auto_cache("youhui_page_recommend_youhui_list");
				clear_auto_cache("ytuan_filter_nav_cache");
				clear_auto_cache("store_filter_nav_cache");
				clear_dir_file(get_real_path()."public/runtime/app/data_caches/");	
				clear_dir_file(get_real_path()."public/runtime/app/tpl_caches/");	
				clear_dir_file(get_real_path()."public/runtime/app/tpl_compiled/");	
				
				clear_dir_file(get_real_path()."public/runtime/app/data_caches/");	
				clear_dir_file(get_real_path()."public/runtime/data/page_static_cache/");
				clear_dir_file(get_real_path()."public/runtime/data/dynamic_avatar_cache/");	
			}
		}
		
		//开始写入配置文件
		$sys_configs = M("Conf")->findAll();
		$config_str = "<?php\n";
		$config_str .= "return array(\n";
		foreach($sys_configs as $k=>$v)
		{
			$config_str.="'".$v['name']."'=>'".addslashes($v['value'])."',\n";
		}
		$config_str.=");\n ?>";
		$filename = get_real_path()."public/sys_config.php";
		
	    if (!$handle = fopen($filename, 'w')) {
		     $this->error(l("OPEN_FILE_ERROR").$filename);
		}
		
		    
		if (fwrite($handle, $config_str) === FALSE) {
		     $this->error(l("WRITE_FILE_ERROR").$filename);
		}
			
	    fclose($handle);
			

			
		save_log(l("CONF_UPDATED"),1);		
		//clear_cache();
		write_timezone();
		$this->success(L("UPDATE_SUCCESS"));
	}
	
	
	
	public function referrals()
	{
		$conf_res = M("Conf")->where("is_effect = 1 and `name` in ('INVITE_REFERRALS','INVITE_REFERRALS_MIN','INVITE_REFERRALS_MAX','INVITE_REFERRALS_RATE','INVITE_REFERRALS_AUTO','INVITE_REFERRALS_DATE','REFERRAL_IP_LIMIT','INVITE_REFERRALS_TYPE')")->order("group_id asc,sort asc")->findAll();

		foreach($conf_res as $k=>$v)
		{
			$v['value'] = htmlspecialchars($v['value']);
			if($v['name']=='TEMPLATE')
			{
				
				//输出现有模板文件夹
				$directory = APP_ROOT_PATH."app/Tpl/";
				$dir = @opendir($directory);
			    $tmpls     = array();
			
			    while (false !== ($file = @readdir($dir)))
			    {
			    	if($file!='.'&&$file!='..')
			        $tmpls[] = $file;
			    }
			    @closedir($dir);
				//end
				
				$v['input_type'] = 1;
				$v['value_scope'] = $tmpls;
			}
			elseif($v['name']=='SHOP_LANG')
			{
				//输出现有语言包文件夹
				$directory = APP_ROOT_PATH."app/Lang/";
				$dir = @opendir($directory);
			    $tmpls     = array();
			
			    while (false !== ($file = @readdir($dir)))
			    {
			    	if($file!='.'&&$file!='..')
			        $tmpls[] = $file;
			    }
			    @closedir($dir);
				//end
				
				$v['input_type'] = 1;
				$v['value_scope'] = $tmpls;
			}
			else
			$v['value_scope'] = explode(",",$v['value_scope']);
			$conf[$v['group_id']][] = $v;
		}
		$this->assign("conf",$conf);
		$this->assign("main_title","邀请返利配置");
		$this->display();
	}
	
	public function update_referrals(){
		$conf_res = M("Conf")->where("is_effect = 1 and name in ('INVITE_REFERRALS','INVITE_REFERRALS_MIN','INVITE_REFERRALS_MAX','INVITE_REFERRALS_RATE','INVITE_REFERRALS_AUTO','INVITE_REFERRALS_DATE','REFERRAL_IP_LIMIT','INVITE_REFERRALS_TYPE')")->findAll();
		
		foreach($conf_res as $k=>$v)
		{
			conf($v['name'],$_REQUEST[$v['name']]);
			if($v['name']=='URL_MODEL'&&$v['value']!=$_REQUEST[$v['name']])
			{
				clear_auto_cache("byouhui_filter_nav_cache");
				clear_auto_cache("cache_shop_acate_tree");
				clear_auto_cache("cache_shop_cate_tree");
				clear_auto_cache("cache_youhui_cate_tree");
				clear_auto_cache("city_list_result");
				clear_auto_cache("fyouhui_filter_nav_cache");
				clear_auto_cache("get_help_cache");
				clear_auto_cache("page_image");
				clear_auto_cache("tuan_filter_nav_cache");
				clear_auto_cache("youhui_page_recommend_youhui_list");
				clear_auto_cache("ytuan_filter_nav_cache");
				clear_auto_cache("store_filter_nav_cache");
				clear_dir_file(get_real_path()."public/runtime/app/data_caches/");	
				clear_dir_file(get_real_path()."public/runtime/app/tpl_caches/");	
				clear_dir_file(get_real_path()."public/runtime/app/tpl_compiled/");	
				
				clear_dir_file(get_real_path()."public/runtime/app/data_caches/");	
				clear_dir_file(get_real_path()."public/runtime/data/page_static_cache/");
				clear_dir_file(get_real_path()."public/runtime/data/dynamic_avatar_cache/");	
			}
		}
		
		//开始写入配置文件
		$sys_configs = M("Conf")->findAll();
		$config_str = "<?php\n";
		$config_str .= "return array(\n";
		foreach($sys_configs as $k=>$v)
		{
			$config_str.="'".$v['name']."'=>'".addslashes($v['value'])."',\n";
		}
		$config_str.=");\n ?>";
		$filename = get_real_path()."public/sys_config.php";
		
	    if (!$handle = fopen($filename, 'w')) {
		     $this->error(l("OPEN_FILE_ERROR").$filename);
		}
		
		    
		if (fwrite($handle, $config_str) === FALSE) {
		     $this->error(l("WRITE_FILE_ERROR").$filename);
		}
			
	    fclose($handle);
			

			
		save_log(l("CONF_UPDATED"),1);		
		//clear_cache();
		write_timezone();
		$this->success(L("UPDATE_SUCCESS"));
	}
	
	public function qq()
	{
		$conf = M("Conf")->where("is_effect = 1 and `name` = 'ONLINE_QQ'")->order("group_id asc,sort asc")->getField("value");

		if($conf!=""){
			$list = unserialize($conf);
			$this->assign("list",$list);
		}
		
		$this->assign("main_title","QQ客服配置");
		$this->display();
	}
	
	public function update_qq(){
		$conf_res = M("Conf")->where("is_effect = 1 and name = ''ONLINE_QQ'")->find();
		$config_data = array();
		$ids = $_REQUEST['config']['id'];
		$names = $_REQUEST['config']['name'];
		$qqs = $_REQUEST['config']['qq'];
		foreach($ids as $k=>$v){
			if($names[$k]!="" || $qqs[$k]!=""){
				$sv = array();
				$sv['name'] = trim($names[$k]);
				$sv['qq'] = floatval($qqs[$k]);
				$config_data[] = $sv;
			}
		}
		
		$names = $_REQUEST['aconfig']['name'];
		$qqs = $_REQUEST['aconfig']['qq'];
		
		foreach($names as $k=>$v){
			if($v!="" || $qqs[$k]!=""){
				$sv = array();
				$sv['name'] = trim($v);
				$sv['qq'] = floatval($qqs[$k]);
				$config_data[] = $sv;
			}
		}
		conf("ONLINE_QQ",serialize($config_data));
		
		
		//开始写入配置文件
		$sys_configs = M("Conf")->findAll();
		$config_str = "<?php\n";
		$config_str .= "return array(\n";
		foreach($sys_configs as $k=>$v)
		{
			$config_str.="'".$v['name']."'=>'".addslashes($v['value'])."',\n";
		}
		$config_str.=");\n ?>";
		$filename = get_real_path()."public/sys_config.php";
		
	    if (!$handle = fopen($filename, 'w')) {
		     $this->error(l("OPEN_FILE_ERROR").$filename);
		}
		
		    
		if (fwrite($handle, $config_str) === FALSE) {
		     $this->error(l("WRITE_FILE_ERROR").$filename);
		}
			
	    fclose($handle);
			

			
		save_log(l("CONF_UPDATED"),1);		
		//clear_cache();
		write_timezone();
		$this->success(L("UPDATE_SUCCESS"));
	}

	public function update()
	{
		$conf_res = M("Conf")->where("is_effect = 1 and is_conf = 1")->findAll();
		foreach($conf_res as $k=>$v)
		{
			conf($v['name'],$_REQUEST[$v['name']]);
			if($v['name']=='URL_MODEL'&&$v['value']!=$_REQUEST[$v['name']])
			{
				clear_auto_cache("byouhui_filter_nav_cache");
				clear_auto_cache("cache_shop_acate_tree");
				clear_auto_cache("cache_shop_cate_tree");
				clear_auto_cache("cache_youhui_cate_tree");
				clear_auto_cache("city_list_result");
				clear_auto_cache("fyouhui_filter_nav_cache");
				clear_auto_cache("get_help_cache");
				clear_auto_cache("page_image");
				clear_auto_cache("tuan_filter_nav_cache");
				clear_auto_cache("youhui_page_recommend_youhui_list");
				clear_auto_cache("ytuan_filter_nav_cache");
				clear_auto_cache("store_filter_nav_cache");
				clear_dir_file(get_real_path()."public/runtime/app/data_caches/");	
				clear_dir_file(get_real_path()."public/runtime/app/tpl_caches/");	
				clear_dir_file(get_real_path()."public/runtime/app/tpl_compiled/");	
				
				clear_dir_file(get_real_path()."public/runtime/app/data_caches/");	
				clear_dir_file(get_real_path()."public/runtime/data/page_static_cache/");
				clear_dir_file(get_real_path()."public/runtime/data/dynamic_avatar_cache/");	
			}
		}
		
			//开始写入配置文件
			$sys_configs = M("Conf")->findAll();
			$config_str = "<?php\n";
			$config_str .= "return array(\n";
			foreach($sys_configs as $k=>$v)
			{
				$config_str.="'".$v['name']."'=>'".addslashes($v['value'])."',\n";
			}
			$config_str.=");\n ?>";
			$filename = get_real_path()."public/sys_config.php";
			
		    if (!$handle = fopen($filename, 'w')) {
			     $this->error(l("OPEN_FILE_ERROR").$filename);
			}
			
			    
			if (fwrite($handle, $config_str) === FALSE) {
			     $this->error(l("WRITE_FILE_ERROR").$filename);
			}
			
	    fclose($handle);
			

			
		save_log(l("CONF_UPDATED"),1);		
		//clear_cache();
		write_timezone();
		$this->success(L("UPDATE_SUCCESS"));
	}
	
	public function mobile()
	{
		$config = M("MConfig")->order("sort asc")->findAll();
		$this->assign("config",$config);
		$this->display();
	}
	
	public function savemobile()
	{
		foreach($_POST as $k=>$v)
		{
			M("MConfig")->where("code='".$k."'")->setField("val",$v);
		}
		$this->success("保存成功");
	}
	
	public function insertnews()
	{
			//B('FilterString');
		$name="MConfigList";
		$model = D ($name);
		if (false ===$data= $model->create ()) {
			$this->error ( $model->getError () );
		}
		$data['is_verify'] = 1;
		$data['group'] = 4;
		//保存当前数据对象
		$list=$model->add ($data);
		if ($list!==false) { //保存成功
			//$this->saveLog(1,$list);
			$this->success (L('INSERT_SUCCESS'));
		} else {
			//失败提示
			//$this->saveLog(0,$list);
			$this->error (L('INSERT_FAILED'));
		}
	}
	function edit() {
		$name = "MConfigList";
		$model = D($name);
		
		$id = $_REQUEST [$model->getPk ()];
		$vo = $model->getById($id);
		$this->assign ( 'vo', $vo );
		$this->display ();
	}
	public function news()
	{
		//列表过滤器，生成查询Map对象
		$map = $this->_search ();
		if (method_exists ( $this, '_filter' )) {
			$this->_filter ( $map );
		}
		$map['group'] = 4;
		$name=$this->getActionName();
		$model = D ("MConfigList");
		if (! empty ( $model )) {
			$this->_list ( $model, $map );
		}
		$this->display ();
		return;
	}
	
	function updatenews() {
		//B('FilterString');
		$name="MConfigList";
		$model = D ( $name );
		if (false === $data = $model->create ()) {
			$this->error ( $model->getError () );
		}
		// 更新数据
		$list=$model->save ($data);
		$id = $data[$model->getPk()];
		if (false !== $list) {
			//成功提示
			//$this->saveLog(1,$id);
			$this->success (L('UPDATE_SUCCESS'));
		} else {
			//错误提示
			//$this->saveLog(0,$id);
			$this->error (L('UPDATE_FAILED'));
		}
	}
	
	public function foreverdelete() {
		//删除指定记录
		$result = array('isErr'=>0,'content'=>'');
		$id = $_REQUEST['id'];
		if(!empty($id))
		{
			$name="MConfigList";
			$model = D($name);
			$pk = $model->getPk ();
			$condition = array ($pk => array ('in', explode ( ',', $id ) ) );
			if(false !== $model->where ( $condition )->delete ())
			{
				//$this->saveLog(1,$id);
			}
			else
			{
				//$this->saveLog(0,$id);
				$result['isErr'] = 1;
				$result['content'] = L('FOREVER_DELETE_SUCCESS');
			}
		}
		else
		{
			$result['isErr'] = 1;
			$result['content'] = L('FOREVER_DELETE_FAILED');
		}
		
		die(json_encode($result));
	}
	

	public function toogle_status()
	{
		$id = intval($_REQUEST['id']);
		$ajax = intval($_REQUEST['ajax']);
		$field = $_REQUEST['field'];
		$info = $id."_".$field;
		$c_is_effect = M("MConfigList")->where("id=".$id)->getField($field);  //当前状态

		$n_is_effect = $c_is_effect == 0 ? 1 : 0; //需设置的状态
		M("MConfigList")->where("id=".$id)->setField($field,$n_is_effect);
		
		save_log($info.l("SET_EFFECT_".$n_is_effect),1);
		$this->ajaxReturn($n_is_effect,l("SET_EFFECT_".$n_is_effect),1)	;	
	}
	public function commossion()
	{
		$conf_res = M("Conf")->where("is_effect = 1 and `name` in ('INVESTORS_COMMISSION_RATIO','BORROWER_COMMISSION_RATIO')")->order("group_id asc,sort asc")->findAll();

		foreach($conf_res as $k=>$v)
		{
			$v['value'] = htmlspecialchars($v['value']);
			if($v['name']=='TEMPLATE')
			{
				
				//输出现有模板文件夹
				$directory = APP_ROOT_PATH."app/Tpl/";
				$dir = @opendir($directory);
			    $tmpls     = array();
			
			    while (false !== ($file = @readdir($dir)))
			    {
			    	if($file!='.'&&$file!='..')
			        $tmpls[] = $file;
			    }
			    @closedir($dir);
				//end
				
				$v['input_type'] = 1;
				$v['value_scope'] = $tmpls;
			}
			elseif($v['name']=='SHOP_LANG')
			{
				//输出现有语言包文件夹
				$directory = APP_ROOT_PATH."app/Lang/";
				$dir = @opendir($directory);
			    $tmpls     = array();
			
			    while (false !== ($file = @readdir($dir)))
			    {
			    	if($file!='.'&&$file!='..')
			        $tmpls[] = $file;
			    }
			    @closedir($dir);
				//end
				
				$v['input_type'] = 1;
				$v['value_scope'] = $tmpls;
			}
			else
			$v['value_scope'] = explode(",",$v['value_scope']);
			$conf[$v['group_id']][] = $v;
		}
		$this->assign("conf",$conf);
		$this->assign("main_title","授权服务机构返佣设置");
		$this->display();
	}
	
	public function update_commossion(){
		$conf_res = M("Conf")->where("is_effect = 1 and name in ('INVESTORS_COMMISSION_RATIO','BORROWER_COMMISSION_RATIO')")->findAll();
		foreach($conf_res as $k=>$v)
		{
			conf($v['name'],floatval(strim($_REQUEST[$v['name']])));
			if($v['name']=='URL_MODEL'&&$v['value']!=$_REQUEST[$v['name']])
			{
				clear_auto_cache("byouhui_filter_nav_cache");
				clear_auto_cache("cache_shop_acate_tree");
				clear_auto_cache("cache_shop_cate_tree");
				clear_auto_cache("cache_youhui_cate_tree");
				clear_auto_cache("city_list_result");
				clear_auto_cache("fyouhui_filter_nav_cache");
				clear_auto_cache("get_help_cache");
				clear_auto_cache("page_image");
				clear_auto_cache("tuan_filter_nav_cache");
				clear_auto_cache("youhui_page_recommend_youhui_list");
				clear_auto_cache("ytuan_filter_nav_cache");
				clear_auto_cache("store_filter_nav_cache");
				clear_dir_file(get_real_path()."public/runtime/app/data_caches/");	
				clear_dir_file(get_real_path()."public/runtime/app/tpl_caches/");	
				clear_dir_file(get_real_path()."public/runtime/app/tpl_compiled/");	
				
				clear_dir_file(get_real_path()."public/runtime/app/data_caches/");	
				clear_dir_file(get_real_path()."public/runtime/data/page_static_cache/");
				clear_dir_file(get_real_path()."public/runtime/data/dynamic_avatar_cache/");	
			}
		}
		
		//开始写入配置文件
		$sys_configs = M("Conf")->findAll();
		$config_str = "<?php\n";
		$config_str .= "return array(\n";
		foreach($sys_configs as $k=>$v)
		{
			$config_str.="'".$v['name']."'=>'".addslashes($v['value'])."',\n";
		}
		$config_str.=");\n ?>";
		$filename = get_real_path()."public/sys_config.php";
		
	    if (!$handle = fopen($filename, 'w')) {
		     $this->error(l("OPEN_FILE_ERROR").$filename);
		}
		
		    
		if (fwrite($handle, $config_str) === FALSE) {
		     $this->error(l("WRITE_FILE_ERROR").$filename);
		}
			
	    fclose($handle);
			

			
		save_log(l("CONF_UPDATED"),1);		
		//clear_cache();
		write_timezone();
		$this->success(L("UPDATE_SUCCESS"));
	}
}
?>