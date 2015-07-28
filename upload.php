<?php
require './system/common.php';
require './app/Lib/app_init.php';
	    if($GLOBALS['user_info']['id']==0)
		{
			$data['error'] = 1;  //未登录
			$data['message'] = $GLOBALS['lang']['PLEASE_LOGIN_FIRST'];
			ajax_return($data);
		}
		//上传处理
		//创建comment目录
		if (!is_dir(APP_ROOT_PATH."public/comment")) { 
	             @mkdir(APP_ROOT_PATH."public/comment");
	             @chmod(APP_ROOT_PATH."public/comment", 0777);
	        }
		
	    $dir = to_date(TIME_UTC,"Ym");
	    if (!is_dir(APP_ROOT_PATH."public/comment/".$dir)) { 
	             @mkdir(APP_ROOT_PATH."public/comment/".$dir);
	             @chmod(APP_ROOT_PATH."public/comment/".$dir, 0777);
	        }
	        
	    $dir = $dir."/".to_date(TIME_UTC,"d");
	    if (!is_dir(APP_ROOT_PATH."public/comment/".$dir)) { 
	             @mkdir(APP_ROOT_PATH."public/comment/".$dir);
	             @chmod(APP_ROOT_PATH."public/comment/".$dir, 0777);
	        }
	     
	    $dir = $dir."/".to_date(TIME_UTC,"H");
	    if (!is_dir(APP_ROOT_PATH."public/comment/".$dir)) { 
	             @mkdir(APP_ROOT_PATH."public/comment/".$dir);
	             @chmod(APP_ROOT_PATH."public/comment/".$dir, 0777);
	        }
	        
	    if(app_conf("IS_WATER_MARK")==1)
	    $img_result = save_image_upload($_FILES,"topic_image","comment/".$dir,$whs=array('thumb'=>array(100,100,1,0)),1,1);
	    else
		$img_result = save_image_upload($_FILES,"topic_image","comment/".$dir,$whs=array('thumb'=>array(100,100,1,0)),0,1);	
		if(intval($img_result['error'])!=0)	
		{
			ajax_return($img_result);
		}
		else 
		{
			if(app_conf("PUBLIC_DOMAIN_ROOT")!='')
        	{
        		$paths = pathinfo($img_result['topic_image']['url']);
        		$path = str_replace("./","",$paths['dirname']);
        		$filename = $paths['basename'];
        		$pathwithoupublic = str_replace("public/","",$path);
	        	$syn_url = app_conf("PUBLIC_DOMAIN_ROOT")."/es_file.php?username=".app_conf("IMAGE_USERNAME")."&password=".app_conf("IMAGE_PASSWORD")."&file=".SITE_DOMAIN.APP_ROOT."/".$path."/".$filename."&path=".$pathwithoupublic."/&name=".$filename."&act=0";
	        	@file_get_contents($syn_url);
        	}
			
		}
		$data['error'] = 0; 
		$data['message'] = $img_result['topic_image']['thumb']['thumb']['url'];
		$data['name'] = valid_str($_FILES['topic_image']['name']);
		
		require_once APP_ROOT_PATH."system/utils/es_imagecls.php";
		$image = new es_imagecls();
		$info = $image->getImageInfo($img_result['topic_image']['path']);
		
		$image_data['width'] = intval($info[0]);
		$image_data['height'] = intval($info[1]);
		$image_data['name'] = valid_str($_FILES['topic_image']['name']);
		$image_data['filesize'] = filesize($img_result['topic_image']['path']);
		$image_data['create_time'] = TIME_UTC;
		$image_data['user_id'] = intval($GLOBALS['user_info']['id']);
		$image_data['user_name'] = addslashes($GLOBALS['user_info']['user_name']);
		$image_data['path'] = $img_result['topic_image']['thumb']['thumb']['url'];
		$image_data['o_path'] = $img_result['topic_image']['url'];
		$GLOBALS['db']->autoExecute(DB_PREFIX."topic_image",$image_data);	
		
		$data['id'] = intval($GLOBALS['db']->insert_id());
		

		ajax_return($data);
?>