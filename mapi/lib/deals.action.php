<?php
// +----------------------------------------------------------------------
// | Fanwe 方维p2p借贷系统
// +----------------------------------------------------------------------
// | Copyright (c) 2011 http://www.fanwe.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 云淡风轻(88522820@qq.com)
// +----------------------------------------------------------------------

require APP_ROOT_PATH.'app/Lib/deal.php';
class deals
{
	public function index(){
		//require APP_ROOT_PATH.'app/Lib/page.php';
		$page = intval($GLOBALS['request']['page']);
		if($page==0)
			$page = 1;
		
		$keywords = trim(htmlspecialchars($GLOBALS['request']['keywords']));
		$level = intval($GLOBALS['request']['level']);		
		$interest = intval($GLOBALS['request']['interest']);		
		$months = intval($GLOBALS['request']['months']);			
		$lefttime = intval($GLOBALS['request']['lefttime']);
		$deal_status = intval($GLOBALS['request']['deal_status']);
		
		$limit = (($page-1)*app_conf("DEAL_PAGE_SIZE")).",".app_conf("DEAL_PAGE_SIZE");
		$level_list = load_auto_cache("level");
		$cate_id = intval($GLOBALS['request']['cid']);
		
		
		$n_cate_id = 0;
		$condition = " publish_wait = 0 ";
		$orderby = "";
		if($cate_id > 0){
			$n_cate_id = $cate_id;
			//$condition .= "AND deal_status in(0,1)";		
			$orderby = "update_time DESC ,sort DESC,id DESC";			
		}else{
			$n_cate_id = 0;
			$orderby = "update_time DESC , sort DESC , id DESC";
		}
		
		if($keywords){
			$kw_unicode = str_to_unicode_string($keywords);
			$condition .=" and (match(name_match,deal_cate_match,tag_match,type_match) against('".$kw_unicode."' IN BOOLEAN MODE))";			
		}
		
		if($level > 0){
			$point  = $level_list['point'][$level];
			$condition .= " AND user_id in(SELECT u.id FROM ".DB_PREFIX."user u LEFT JOIN ".DB_PREFIX."user_level ul ON ul.id=u.level_id WHERE ul.point >= $point)";
		}
		
		if($interest > 0){
			$condition .= " AND rate >= ".$interest;
		}
		
		if($months > 0){
			if($months==12)
				$condition .= " AND repay_time <= ".$months;
			elseif($months==18)
				$condition .= " AND repay_time >= ".$months;
		}
		
		if($lefttime > 0){
			$condition .= " AND (start_time + enddate*24*3600 - ".TIME_UTC.") <= ".$lefttime*24*3600;
		}
		
		if ($deal_status > 0){
			$condition .= " AND deal_status = ".$deal_status;
		}
				
		$result = get_deal_list($limit,$n_cate_id,$condition,$orderby);														
		
		$root = array();
		$root['response_code'] = 1;
		$root['item'] = $result['list'];
		//$root['DEAL_PAGE_SIZE'] = app_conf("DEAL_PAGE_SIZE");
		//$root['count'] = $result['count'];
		$root['page'] = array("page"=>$page,"page_total"=>ceil($result['count']/app_conf("DEAL_PAGE_SIZE")),"page_size"=>app_conf("DEAL_PAGE_SIZE"));
		$root['program_title'] = "投资列表";
		output($root);		
	}
}
?>
