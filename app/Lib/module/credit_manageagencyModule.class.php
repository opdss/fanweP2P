<?php
class credit_manageagencyModule extends SiteBaseModule {
	private $is_ajax ; 
	public function __construct(){
		$this->is_ajax = intval($_REQUEST['is_ajax']);
		$manageagency_info = es_session::get("manageagency_info");
		if(!$manageagency_info){
			set_gopreview();
			showErr($GLOBALS['lang']['PLEASE_LOGIN_FIRST'],$this->is_ajax,url("index","authorized#login"));
		}
	}
    function index() {
		$manageagency_info = es_session::get("manageagency_info");
    	$type=strim($_REQUEST['type']);
    	$credit_type= load_auto_cache("credit_type");
    	if(!isset($credit_type['list'][$type])){
    		showErr('认证类型不存在',$this->is_ajax);
    	}
    	$credit_type['list'][$type]['description_format'] = $GLOBALS['tmpl']->fetch("str:".$credit_type['list'][$type]['description']);
    	$GLOBALS['tmpl']->assign("credit",$credit_type['list'][$type]);
    	
    	if($type=="credit_contact" || $type=="credit_residence"){
    		//地区列表
				$work =  $GLOBALS['db']->getRow("SELECT * FROM ".DB_PREFIX."user_work where user_id =".$manageagency_info['id']);
				$GLOBALS['tmpl']->assign("work",$work);
				
				$region_lv2 = $GLOBALS['db']->getAll("select * from ".DB_PREFIX."region_conf where region_level = 2");  //二级地址
				foreach($region_lv2 as $k=>$v)
				{
					if($v['id'] == intval($work['province_id']))
					{
						$region_lv2[$k]['selected'] = 1;
						break;
					}
				}
				$GLOBALS['tmpl']->assign("region_lv2",$region_lv2);
				
				$region_lv3 = $GLOBALS['db']->getAll("select * from ".DB_PREFIX."region_conf where pid = ".intval($work['province_id']));  //三级地址
				foreach($region_lv3 as $k=>$v)
				{
					if($v['id'] == intval($work['city_id']))
					{
						$region_lv3[$k]['selected'] = 1;
						break;
					}
				}
				$GLOBALS['tmpl']->assign("region_lv3",$region_lv3);
    	}
    	
    	$GLOBALS['tmpl']->assign("is_ajax",$this->is_ajax);
    	
    	if($this->is_ajax==1)
    		showSuccess($GLOBALS['tmpl']->fetch("manageagency/credit_box.html"),$this->is_ajax);
    	else
    		$GLOBALS['tmpl']->display("manageagency/credit_box.html");
    }
    
    function credit_save(){
		$manageagency_info = es_session::get("manageagency_info");
    	$type=strim($_REQUEST['type']);
    	$credit_type= load_auto_cache("credit_type");
    	if(!isset($credit_type['list'][$type])){
    		showErr('认证类型不存在',$this->is_ajax);
    	}
    	
    	$field_array = array(
			"credit_identificationscanning"=>"idcardpassed",
			"credit_contact"=>"workpassed",
			"credit_credit"=>"creditpassed",
			"credit_incomeduty"=>"incomepassed",
			"credit_house"=>"housepassed",
			"credit_car"=>"carpassed",
			"credit_marriage"=>"marrypassed",
			"credit_titles"=>"skillpassed",
			"credit_videoauth"=>"videopassed",
			"credit_mobilereceipt"=>"mobiletruepassed",
			"credit_residence"=>"residencepassed",
			"credit_seal"=>"sealpassed",
		);
		$u_c_data[$field_array[$type]] = 0;
    	//身份认证
    	if($type == "credit_identificationscanning"){
    		$u_c_data['real_name'] = strim($_REQUEST['real_name']);
    		$u_c_data['idno'] = strim($_REQUEST['idno']);
    		if(getIDCardInfo($u_c_data['idno'])==0){
    			showErr("提交失败,身份证号码错误！",$this->is_ajax);
    		}
    		if($GLOBALS['db']->getOne("select count(*) from ".DB_PREFIX."user where idno = '".$u_c_data['idno']."' and id <> ".intval($manageagency_info['id']))>0){
    			showErr("提交失败,身份证号码已使用！",$this->is_ajax);
    		}
    		$u_c_data['sex'] = intval($_REQUEST['sex']);
    		$u_c_data['byear'] = intval($_REQUEST['byear']);
    		$u_c_data['bmonth'] = intval($_REQUEST['bmonth']);
    		$u_c_data['bday'] = intval($_REQUEST['bday']);
    		$u_c_data['bday'] = intval($_REQUEST['bday']);
    		
    	}
    	//汽车认证
    	if($type == "credit_car"){
    		$u_c_data['car_brand'] = strim($_REQUEST['carbrand']);
    		$u_c_data['car_year'] = intval($_REQUEST['caryear']);
    		$u_c_data['car_number'] = strim($_REQUEST['carnumber']);
    		$u_c_data['carloan'] = intval($_REQUEST['carloan']);
    		
    	}
    	//房产认证
    	if($type == "credit_house"){
    		$u_c_data['houseloan'] = intval($_REQUEST['houseloan']);
    		
    	}
    	//结婚认证
    	if($type == "credit_marriage"){
    		$u_c_data['haschild'] = intval($_REQUEST['haschild']);
    		
    	}
    	//学历认证
    	if($type == "credit_graducation"){
    		$u_c_data['edu_validcode'] = strim($_REQUEST['validcode']);
    		$u_c_data['graduation'] = strim($_REQUEST['graduation']);
    		$u_c_data['university'] = strim($_REQUEST['university']);
    		$u_c_data['graduatedyear'] = intval($_REQUEST['graduatedyear']);
    	}
    	
    	//视频认证
    	if($type == "credit_videoauth"){
    		$u_c_data['has_send_video'] = intval($_REQUEST['usemail']);
    	}
    	
    	//居住地证明
    	if($type == "credit_residence"){
    		$u_w_data['province_id'] = intval($_REQUEST['province_id']);
    		$u_w_data['city_id'] = intval($_REQUEST['city_id']);
    		
    		if($GLOBALS['db']->getOne("SELECT count(*) FROM ".DB_PREFIX."user_work where user_id=".$manageagency_info['id']) > 0){
    			$u_w_data['user_id'] = $manageagency_info['id'];
    			$GLOBALS['db']->autoExecute(DB_PREFIX."user_work",$u_w_data,"INSERT");
    		}
    		else
    			$GLOBALS['db']->autoExecute(DB_PREFIX."user_work",$u_w_data,"UPDATE","user_id=".$manageagency_info['id']);
    		
    		$u_c_data['address'] = htmlspecialchars($_REQUEST['address']);
    		$u_c_data['phone'] = htmlspecialchars($_REQUEST['phone']);
    		$u_c_data['postcode'] = htmlspecialchars($_REQUEST['postcode']);
    		
    	}
    	
    	$GLOBALS['db']->autoExecute(DB_PREFIX."user",$u_c_data,"UPDATE","id=".$manageagency_info['id']);
    	
    	$file=array();
    	if($credit_type['list'][$type]['file_count'] > 0){
	    	for($i=1;$i<=$credit_type['list'][$type]['file_count'];$i++){
	    		if(trim($_REQUEST['file'.$i])!=""){
	    			$file[] = replace_public(strim($_REQUEST['file'.$i]));
	    		}
	    	}
	    	if(count($file)==0){
	    		exit();
	    	}
    	}
    	
    	$mode = "INSERT";
    	$condition = "";
    	
    	$temp_info = $GLOBALS['db']->getRow("SELECT user_id,`type`,`file` FROM ".DB_PREFIX."user_credit_file WHERE user_id=".$manageagency_info['id']." AND type='".$type."'");
    	if($temp_info){
    		$file_list = unserialize($temp_info['file']);
    		
    		//认证是否过期
			$time = TIME_UTC;
			$expire_time = $credit_type['list'][$type]['expire']*30*24*3600;
			
    		switch($type){
    			case "credit_contact" :
    				if($manageagency_info['workpassed']==1){
			    		if(($time - $manageagency_info['workpassed_time']) > $expire_time){
			    			
			    			$manageagency_info['workpassed'] = 0;
			    			$GLOBALS['db']->query("update ".DB_PREFIX."user set workpassed=0 WHERE id=".$manageagency_info['id']);
			    			es_session::set('user_info',$manageagency_info);
			    		}
			    	}
    			break;
    			case "credit_credit" :
    				if($manageagency_info['creditpassed']==1){
			    		if(($time - $manageagency_info['creditpassed_time']) > $expire_time){
			    			
			    			$manageagency_info['creditpassed'] = 0;
			    			$GLOBALS['db']->query("update ".DB_PREFIX."user set creditpassed=0 WHERE id=".$manageagency_info['id']);
			    			es_session::set('user_info',$manageagency_info);
			    		}
			    	}
    			break;
    			case "credit_incomeduty" :
    				if($manageagency_info['incomepassed']==1){
			    		if(($time - $manageagency_info['incomepassed_time']) > $expire_time){
			    			
			    			$manageagency_info['incomepassed'] = 0;
			    			$GLOBALS['db']->query("update ".DB_PREFIX."user set incomepassed=0 WHERE id=".$manageagency_info['id']);
			    			es_session::set('user_info',$manageagency_info);
			    		}
			    	}
    			break;
    			case "credit_residence" :
    				if($manageagency_info['residencepassed']==1){
			    		if(($time - $manageagency_info['residencepassed_time']) > $expire_time){
			    			
			    			$manageagency_info['residencepassed'] = 0;
			    			$GLOBALS['db']->query("update ".DB_PREFIX."user set residencepassed=0 WHERE id=".$manageagency_info['id']);
			    			es_session::set('user_info',$manageagency_info);
			    		}
			    	}
	    		break;
	    		
	    		case "credit_seal" :
			    	foreach($file_list as $k=>$v){
						@unlink(APP_ROOT_PATH.$v);
					}
					$file_list = array();
	    			$manageagency_info['sealpassed'] = 0;
	    			$GLOBALS['db']->query("update ".DB_PREFIX."user set sealpassed=0 WHERE id=".$manageagency_info['id']);
	    			es_session::set('user_info',$manageagency_info);
			    	
    			break;
    		}
    		
    		$mode = "UPDATE";
    		$condition = "user_id=".$manageagency_info['id']." AND type='".$type."'";
    	}
    	
		if($file){
			foreach($file as $v){
				$file_list[] = $v;
			}
		}

    	$data['user_id'] = $manageagency_info['id'];
    	$data['type'] = $type;
    	$data['status'] = 0;
    	$data['file'] = serialize($file);
    	$data['create_time'] = TIME_UTC;
    	$data['passed'] = 0;
    	
    	$GLOBALS['db']->autoExecute(DB_PREFIX."user_credit_file",$data,$mode,$condition);
    	
    	if($this->is_ajax==1)
    		showSuccess("提交成功,请等待管理员审核！",$this->is_ajax);
    	else
    		$GLOBALS['tmpl']->display("inc/credit/upload_result_tip.html");
    }
}
?>