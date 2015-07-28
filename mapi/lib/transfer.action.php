<?php
// +----------------------------------------------------------------------
// | Fanwe 方维p2p借贷系统
// +----------------------------------------------------------------------
// | Copyright (c) 2011 http://www.fanwe.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 云淡风轻(88522820@qq.com)
// +----------------------------------------------------------------------
require APP_ROOT_PATH.'app/Lib/deal.php';
class transfer
{
	public function index(){
		
		$root = array();
		
		$page = intval($GLOBALS['request']['page']);
		if($page==0)
			$page = 1;
		
		
		$keywords = trim(htmlspecialchars($GLOBALS['request']['keywords']));
				
		$level = intval($GLOBALS['request']['level']);		
		$interest = intval($GLOBALS['request']['interest']);		
		$months = intval($GLOBALS['request']['months']);			
		$lefttime = intval($GLOBALS['request']['lefttime']);
		$cate_id = intval($GLOBALS['request']['cid']);
		
		$limit = (($page-1)*app_conf("DEAL_PAGE_SIZE")).",".app_conf("DEAL_PAGE_SIZE");
		$level_list = load_auto_cache("level");
		
		
		$page_args =array();
		
		$condition = "";
		if($cate_id > 0){
			$condition .= "AND d.deal_status >=4 and cate_id=".$cate_id;
			$orderby = "d.update_time DESC ,d.sort DESC,d.id DESC";
		}
		elseif ($cate_id == 0){
			$orderby = " d.create_time DESC , dlt.id DESC";			
		}
		
		if($keywords){
			$kw_unicode = str_to_unicode_string($keywords);
			$condition .=" and (match(d.name_match,d.deal_cate_match,d.tag_match,d.type_match) against('".$kw_unicode."' IN BOOLEAN MODE))";
		}
		
		if($level > 0){
			$point  = $level_list['point'][$level];
			$condition .= " AND d.user_id in(SELECT u.id FROM ".DB_PREFIX."user u LEFT JOIN ".DB_PREFIX."user_level ul ON ul.id=u.level_id WHERE ul.point >= $point)";
		}
		
		if($interest > 0){
			$condition .= " AND d.rate >= ".$interest;
		}
		
		if($months > 0){
			if($months==12)
				$condition .= " AND d.repay_time <= ".$months;
			elseif($months==18)
			$condition .= " AND d.repay_time >= ".$months;
		}
		
		if($lefttime > 0){
			$condition .= " AND (d.next_repay_time + 24*3600 - 1 - ".TIME_UTC.") <= ".$lefttime*24*3600;
		}
		
		$extfield = "";
		$union_sql = "";
		
		
		$result = get_transfer_list($limit,$condition,$extfield,$union_sql,$orderby);

		$root = array();
		$root['response_code'] = 1;
		$root['item'] = $result['list'];
		//$root['DEAL_PAGE_SIZE'] = app_conf("DEAL_PAGE_SIZE");
		//$root['count'] = $result['count'];
		$root['page'] = array("page"=>$page,"page_total"=>ceil($result['rs_count']/app_conf("DEAL_PAGE_SIZE")),"page_size"=>app_conf("DEAL_PAGE_SIZE"));
			
		$root['program_title'] = "债权转让";
		output($root);		
	}
}
?>
