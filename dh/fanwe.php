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
    						array('name'=>'API城市列表','url'=>'fanwe.php?act=citys'),
    						array('name'=>'API团购列表','url'=>'fanwe.php?city={CITY_ID}')
    				   );
    return $api;
}

	require_once "api.php";

	if($_REQUEST['act']=='citys')
	{
		header('Content-type: text/xml; charset=utf-8');
		$now = get_gmtime();
		$sql = 'SELECT id,name from '.DB_PREFIX.'deal_city where is_effect = 1 and is_delete = 0 and is_open = 1';
	
	
		$list = $GLOBALS['db']->getAll($sql);
			
		$xml="<?xml version=\"1.0\" encoding=\"utf-8\"?>\r\n";
		$xml.="<response date=\"".to_date($now,"r")."\">\r\n";
		$xml.="<citys>\r\n";
			
		foreach($list as $item)
		{
			$xml.="<city><id>".$item['id']."</id><name>".addslashes(emptyTag($item['name']))."</name></city>\r\n";
		}
		$xml.="</citys>\r\n";
		$xml.="</response>\r\n";
		echo $xml;
	}	
	else
	{
		header('Content-type: text/xml; charset=utf-8');
		$cityID = intval($_REQUEST['city']);
		$now = get_gmtime();
		if($cityID > 0)
				$where = " and d.city_id = $cityID";
		else
				$where = "";
			
		$sql = "SELECT d.id,d.discount,d.city_id,d.name as goods_name,d.img,d.icon,d.current_price,d.origin_price,d.begin_time,d.end_time,d.brief as goodsbrief,dc.name as city_name,s.name as supplier_name,d.buy_count ".
						'FROM '.DB_PREFIX.'deal as d '.
						'left join '.DB_PREFIX.'deal_city as dc on dc.id = d.city_id '.
						'left join '.DB_PREFIX.'supplier as s on s.id = d.supplier_id '.
						"where d.is_effect = 1 and d.is_delete = 0 and d.is_shop=0 and d.time_status = 1 and d.buy_status < 2  $where group by d.id order by d.sort desc,d.id desc";
			
	
		$list = $GLOBALS['db']->getAll($sql);
			
		$xml="<?xml version=\"1.0\" encoding=\"utf-8\"?>\r\n";
		$xml.="<response date=\"".to_date($now,"r")."\">\r\n";
	
		foreach($list as $item)
		{
				$url = get_domain().url("tuan","deal",array("id"=>$item['id']));	
				
				$xml.="<goods>\r\n";
				$xml.="<cityid>".$item['city_id']."</cityid>\r\n";
				$xml.="<cityname>".$item['city_name']."</cityname>\r\n";
				$xml.="<id>".$item['id']."</id>\r\n";
				$xml.="<title>".emptyTag($item['goods_name'])."</title>\r\n";
				$xml.="<brief><![CDATA[".$item['goodsbrief']."]]></brief>\r\n";
				$xml.="<url>".convertUrl($url)."</url>\r\n";
				$xml.="<groupprice>".floatval($item['current_price'])."</groupprice>\r\n";
				$xml.="<marketprice>".floatval($item['origin_price'])."</marketprice>\r\n";
				$xml.="<begintime>".to_date($item['begin_time'],"r")."</begintime>\r\n";
				$xml.="<endtime>".to_date($item['end_time'],"r")."</endtime>\r\n";
				
				//对图片路径的修复
				$domain = app_conf("PUBLIC_DOMAIN_ROOT")==''?get_domain().APP_ROOT:app_conf("PUBLIC_DOMAIN_ROOT");
		        $icon = str_replace(APP_ROOT."./public/",$domain."/public/",$item['icon']);	
		        $icon = str_replace("./public/",$domain."/public/",$item['icon']);
		        $img = str_replace(APP_ROOT."./public/",$domain."/public/",$item['img']);	
		        $img = str_replace("./public/",$domain."/public/",$item['img']);	
				
				$xml.="<smallimg>".$icon."</smallimg>\r\n";
				$xml.="<bigimg>".$img."</bigimg>\r\n";
				$xml.="<suppliers>".emptyTag($item['supplier_name'])."</suppliers>\r\n";
				$xml.="<buycount>".$item['buy_count']."</buycount>\r\n";
				$xml.="</goods>\r\n";
		}
		$xml.="</response>\r\n";
		echo $xml;
	}
?>