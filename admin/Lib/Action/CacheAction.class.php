<?php
// +----------------------------------------------------------------------
// | Fanwe 方维p2p借贷系统
// +----------------------------------------------------------------------
// | Copyright (c) 2011 http://www.fanwe.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 云淡风轻(88522820@qq.com)
// +----------------------------------------------------------------------

class CacheAction extends CommonAction{

	public function clear_admin()
	{
		set_time_limit(0);
		es_session::close();
		clear_dir_file(get_real_path()."public/runtime/admin/Cache/");	
		clear_dir_file(get_real_path()."public/runtime/admin/Data/_fields/");		
		clear_dir_file(get_real_path()."public/runtime/admin/Temp/");	
		clear_dir_file(get_real_path()."public/runtime/admin/Logs/");	
		@unlink(get_real_path()."public/runtime/admin/~app.php");
		@unlink(get_real_path()."public/runtime/admin/~runtime.php");
		@unlink(get_real_path()."public/runtime/admin/lang.js");
		@unlink(get_real_path()."public/runtime/app/config_cache.php");	
		
		header("Content-Type:text/html; charset=utf-8");
       	exit("<div style='line-height:50px; text-align:center; color:#f30;'>".L('CLEAR_SUCCESS')."</div><div style='text-align:center;'><input type='button' onclick='$.weeboxs.close();' class='button' value='关闭' /></div>");
	}
	
	public function clear_parse_file()
	{
		set_time_limit(0);
		es_session::close();
		clear_dir_file(get_real_path()."public/runtime/statics/");	
		clear_dir_file(get_real_path()."public/runtime/app/tpl_caches/");		
		clear_dir_file(get_real_path()."public/runtime/app/tpl_compiled/");
		
		clear_dir_file(get_real_path()."public/runtime/wap/tpl_caches/");		
		clear_dir_file(get_real_path()."public/runtime/wap/tpl_compiled/");
		clear_dir_file(get_real_path()."public/runtime/wap/statics/");
		
		header("Content-Type:text/html; charset=utf-8");
       	exit("<div style='line-height:50px; text-align:center; color:#f30;'>".L('CLEAR_SUCCESS')."</div><div style='text-align:center;'><input type='button' onclick='$.weeboxs.close();' class='button' value='关闭' /></div>");
	}
	
	public function clear_data()
	{
		set_time_limit(0);
		es_session::close();
		@unlink(get_real_path()."public/runtime/app/deal_cate_conf.js");	
		clear_dir_file(get_real_path()."public/runtime/app/deal_region_conf/");
		if(intval($_REQUEST['is_all'])==0)
		{
			//数据缓存
			clear_dir_file(get_real_path()."public/runtime/app/data_caches/");				
			clear_dir_file(get_real_path()."public/runtime/app/db_caches/");
			$GLOBALS['cache']->clear();
			clear_dir_file(get_real_path()."public/runtime/app/tpl_caches/");		
			clear_dir_file(get_real_path()."public/runtime/app/tpl_compiled/");
			@unlink(get_real_path()."public/runtime/app/lang.js");				
			
			//删除相关未自动清空的数据缓存
			clear_auto_cache("page_image");
			clear_auto_cache("recommend_hot_sale_list");
			clear_auto_cache("recommend_uc_topic");
			clear_auto_cache("youhui_page_recommend_youhui_list");
		}
		else
		{

			clear_dir_file(get_real_path()."public/runtime/data/");	
			clear_dir_file(get_real_path()."public/runtime/app/data_caches/");				
			clear_dir_file(get_real_path()."public/runtime/app/db_caches/");
			clear_dir_file(get_real_path()."public/runtime/app/tpl_caches/");		
			clear_dir_file(get_real_path()."public/runtime/app/tpl_compiled/");
			clear_dir_file(get_real_path()."public/runtime/statics/");	
			
			$GLOBALS['cache']->clear();
			clear_dir_file(get_real_path()."public/runtime/app/tpl_caches/");		
			clear_dir_file(get_real_path()."public/runtime/app/tpl_compiled/");
			@unlink(get_real_path()."public/runtime/app/lang.js");	
			
			//后台
			clear_dir_file(get_real_path()."public/runtime/admin/Cache/");	
			clear_dir_file(get_real_path()."public/runtime/admin/Data/_fields/");		
			clear_dir_file(get_real_path()."public/runtime/admin/Temp/");	
			clear_dir_file(get_real_path()."public/runtime/admin/Logs/");	
			@unlink(get_real_path()."public/runtime/admin/~app.php");
			@unlink(get_real_path()."public/runtime/admin/~runtime.php");
			@unlink(get_real_path()."public/runtime/admin/lang.js");
			@unlink(get_real_path()."public/runtime/app/config_cache.php");	
			
		}
		header("Content-Type:text/html; charset=utf-8");
       	exit("<div style='line-height:50px; text-align:center; color:#f30;'>".L('CLEAR_SUCCESS')."</div><div style='text-align:center;'><input type='button' onclick='$.weeboxs.close();' class='button' value='关闭' /></div>");
	}

	
	public function syn_data()
	{
		set_time_limit(0);
		es_session::close();
		//同步，supplier_location表, deal表, youhui表, event表 , supplier 表
		//总数
		$page = intval($_REQUEST['p'])==0?1:intval($_REQUEST['p']);
		if($page==1)
		syn_dealing();
		$page_size = 5;		
		$location_total = M("SupplierLocation")->count();
		$deal_total = M("Deal")->count();
		$youhui_total = M("Youhui")->count();
		$event_total = M("Event")->count();
		$supplier_total = M("Supplier")->count();
		$count = max(array($location_total,$deal_total,$youhui_total,$event_total,$supplier_total));
		
		$limit = ($page-1)*$page_size.",".$page_size;
		$location_list = M("SupplierLocation")->limit($limit)->findAll();
		foreach($location_list as $v)
		{
			syn_supplier_location_match($v['id']);
		}
		$supplier_list = M("Supplier")->limit($limit)->findAll();
		foreach($supplier_list as $v)
		{
			syn_supplier_match($v['id']);
		}
		$deal_list = M("Deal")->limit($limit)->findAll();
		foreach($deal_list as $v)
		{
			syn_deal_match($v['id']);
		}
		$youhui_list = M("Youhui")->limit($limit)->findAll();
		foreach($youhui_list as $v)
		{
			syn_youhui_match($v['id']);
		}	
		$event_list = M("Event")->limit($limit)->findAll();
		foreach($youhui_list as $v)
		{
			syn_event_match($v['id']);
		}		
		
		if($page*$page_size>=$count)
		{
			$this->assign("jumpUrl",U("Cache/index"));
			$ajax = intval($_REQUEST['ajax']);
			clear_auto_cache("cache_deal_cart");
       		$data['status'] = 1;
       		$data['info'] = "<div style='line-height:50px; text-align:center; color:#f30;'>同步成功</div><div style='text-align:center;'><input type='button' onclick='$.weeboxs.close();' class='button' value='关闭' /></div>";
			header("Content-Type:text/html; charset=utf-8");
            exit(json_encode($data));
		}
		else 
		{
			$total_page = ceil($count/$page_size);       		
       		$data['status'] = 0;
       		$data['info'] = "共".$total_page."页，当前第".$page."页,等待更新下一页记录";
       		$data['url'] = U("Cache/syn_data",array("p"=>$page+1));
       		header("Content-Type:text/html; charset=utf-8");
            exit(json_encode($data));
		}		
	}
	
	public function clear_image()
	{
		set_time_limit(0);
		es_session::close();
		$path  = APP_ROOT_PATH."public/attachment/";
		$this->clear_image_file($path);
		$path  = APP_ROOT_PATH."public/images/";
		$this->clear_image_file($path);
		$path  = APP_ROOT_PATH."public/view_info/";
		$this->clear_image_file($path);
	
		clear_dir_file(get_real_path()."public/runtime/app/tpl_caches/");		
		clear_dir_file(get_real_path()."public/runtime/app/tpl_compiled/");
		
		header("Content-Type:text/html; charset=utf-8");
       	exit("<div style='line-height:50px; text-align:center; color:#f30;'>".L('CLEAR_SUCCESS')."</div><div style='text-align:center;'><input type='button' onclick='$.weeboxs.close();' class='button' value='关闭' /></div>");
	}
	
	private function clear_image_file($path)
	{
	   if ( $dir = opendir( $path ) )
	   {
	            while ( $file = readdir( $dir ) )
	            {
	                $check = is_dir( $path. $file );
	                if ( !$check )
	                {
	                	if(preg_match("/_(\d+)x(\d+)/i",$file,$matches))
	                    @unlink ( $path . $file);                       
	                }
	                else 
	                {
	                 	if($file!='.'&&$file!='..')
	                 	{
	                 		$this->clear_image_file($path.$file."/");              			       		
	                 	} 
	                 }           
	            }
	            closedir( $dir );
	            return true;
	   }
	}
}
?>