<?php
// +----------------------------------------------------------------------
// | Fanwe 方维p2p借贷系统
// +----------------------------------------------------------------------
// | Copyright (c) 2011 http://www.fanwe.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 云淡风轻(88522820@qq.com)
// +----------------------------------------------------------------------

/* API的基本信息 */
if (isset($read_api) && $read_api == true)
{
    $api['info']    =  array(
    						array('name'=>'Hao123API[http://tuan.hao123.com/]','url'=>'hao123.php')    						
    				   );
    return $api;
}

require_once "api.php";
	
header('Content-type: text/xml; charset=utf-8');
		$now = get_gmtime();
		$sql = "SELECT d.id,d.discount,d.city_id,c.name as cate_name,d.name as goods_name,d.img,d.icon,d.current_price,d.origin_price,d.begin_time,d.end_time,d.brief as goodsbrief,dc.name as city_name,s.name as supplier_name,d.buy_count,sl.tel as sp_tel,sl.address as sp_address,sl.xpoint,sl.ypoint   ".
					'FROM '.DB_PREFIX.'deal as d '.
					'left join '.DB_PREFIX.'deal_city as dc on dc.id = d.city_id '.
					'left join '.DB_PREFIX.'supplier as s on s.id = d.supplier_id '.
					'left join '.DB_PREFIX.'supplier_location as sl on sl.supplier_id = s.id '.
					'left join '.DB_PREFIX.'deal_cate as c on c.id = d.cate_id '.
					"where d.is_effect = 1 and d.is_delete = 0 and d.is_shop = 0 and d.time_status = 1 and d.buy_status < 2  group by d.id order by d.sort desc,d.id desc";
		

	$list = $GLOBALS['db']->getAll($sql);
	
	$xml="<?xml version=\"1.0\" encoding=\"utf-8\"?>\r\n";
	$xml.="<urlset>\r\n";
		
		foreach($list as $item)
		{
			$xml.="<url>\r\n";
		
			$url = get_domain().url("tuan","deal",array("id"=>$item['id']));	
			//商品折扣
			if($item['discount']>0)
			{
				$rebate = number_format($item['discount'],1);
			}
			if ($item['origin_price'] > 0)
				$rebate = number_format($item['current_price']/$item['origin_price'] * 10, 1);
			else
				$rebate = 0;
				
			
			$begin_time = intval($item['begin_time'])>0?(intval($item['begin_time'])+(8*3600)):0; 
			$end_tiime = intval($item['end_time'])>0?(intval($item['end_time'])+(8*3600)):0; 
			
			$xml.="<loc>".convertUrl($url)."</loc>\r\n";
			$xml.="<data>\r\n";
			$xml.="<display>\r\n";
			$xml.="<website>".app_conf("SHOP_TITLE")."</website>\r\n";
			$xml.="<siteurl>".get_domain().APP_ROOT."</siteurl>\r\n";
			$xml.="<city>".$item['city_name']."</city>\r\n";
			$gcatename=$item['cate_name'];
			   if(!preg_match('/^((?!餐|美食|饮).)*$/is',$gcatename))
			{
				$class = 1;
			}
			else if(!preg_match('/^((?!休闲|娱乐).)*$/is',$gcatename))
			{
				$class = 2;
			}
			else if(!preg_match('/^((?!美容|化妆).)*$/is',$gcatename))
			{
				$class = 3;
			}
			else if(!preg_match('/^((?!网上|购物).)*$/is',$gcatename))
			{
				$class = 4;
			}
			else if(!preg_match('/^((?!运动|健身 ).)*$/is',$gcatename))
			{
				$class = 5;
			}
			
			$xml.="<category>".$class."</category>\r\n";
			$xml.="<dpshopid>".$item['xpoint'].",".$item['ypoint']."</dpshopid>\r\n";
			$xml.="<range>".$item['sp_address']."</range>\r\n";
			$xml.="<address>".$item['sp_address']."</address>\r\n";
			$xml.="<major>1</major>\r\n";
			$xml.="<title>".addslashes(emptyTag($item['goods_name']))."</title>\r\n";

			$domain = app_conf("PUBLIC_DOMAIN_ROOT")==''?get_domain().APP_ROOT:app_conf("PUBLIC_DOMAIN_ROOT");
	        $img = str_replace(APP_ROOT."./public/",$domain."/public/",$item['img']);	
	        $img = str_replace("./public/",$domain."/public/",$item['img']);
			
			$xml.="<image>".$img."</image>\r\n";
			$xml.="<startTime>".$begin_time."</startTime>\r\n";
			$xml.="<endTime>".$end_tiime."</endTime>\r\n";
			$xml.="<value>".round($item['origin_price'],2)."</value>\r\n";
			$xml.="<price>".round($item['current_price'],2)."</price>\r\n";
			$xml.="<rebate>".$rebate."</rebate>\r\n";
			$xml.="<bought>".$item['buy_count']."</bought>\r\n";
			
			
			$xml.="</display>\r\n";
			$xml.="</data>\r\n";
			$xml.="</url>\r\n";
		}
		
		$xml.="</urlset>\r\n";
		echo $xml;

?>