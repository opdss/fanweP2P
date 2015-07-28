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
    						array('name'=>'腾讯搜搜API[http://tuan.soso.com/]','url'=>'soso.php')    						
    				   );
    return $api;
}
require_once "api.php";


		header('Content-type: text/xml; charset=GBK');
		$sql = "SELECT d.id,d.supplier_id,d.discount,c.name as cate_name,d.city_id,d.name as goods_name,d.current_price,d.origin_price,d.begin_time,d.sub_name,d.img,d.end_time,d.brief as goodsbrief,dc.name as city_name,s.name as sp_name,d.buy_count,d.user_count,s.content,sl.tel as sp_tel,sl.address as sp_address ".
					'FROM '.DB_PREFIX.'deal as d '.
					'left join '.DB_PREFIX.'deal_city as dc on dc.id = d.city_id '.
					'left join '.DB_PREFIX.'supplier as s on s.id = d.supplier_id '.
					'left join '.DB_PREFIX.'deal_cate as c on c.id = d.cate_id '.
					'left join '.DB_PREFIX.'supplier_location as sl on sl.supplier_id = s.id '.
					"where d.is_effect = 1 and d.is_delete = 0 and d.is_shop = 0 and d.time_status = 1 and d.buy_status < 2  group by d.id order by d.sort desc,d.id desc";
		mysql_query("set names gb2312");
		$list = $GLOBALS['db']->getAll($sql);
		$xml="<?xml version=\"1.0\" encoding=\"GBK\"?>\r\n";
		$xml.="<sdd>\r\n";
		$xml.="<provider>".iconv('utf-8','gbk',app_conf("SHOP_TITLE"))."</provider>\r\n";
		$xml.="<version>1.0</version>\r\n";
		$xml.="<dataServiceId>1_1</dataServiceId>\r\n";
		//$xml.="<updatemethod>all</updatemethod>\r\n";
		$xml.="<datalist>\r\n";
		foreach($list as $item)
		{
			$xml.="<item>\r\n";
			
			$url = get_domain().url("tuan","deal",array("id"=>$item['id']));	
				
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
			
			$item_brief = $item['goodsbrief']==''?$item['goods_name']:$item['goodsbrief'];
			$domain = app_conf("PUBLIC_DOMAIN_ROOT")==''?get_domain().APP_ROOT:app_conf("PUBLIC_DOMAIN_ROOT");
			
			$xml.="<keyword><![CDATA[".emptyTag($item['goods_name'])."]]></keyword>\r\n";
			$xml.="<url>".convertUrl($url)."</url>\r\n";
			$xml.="<creator>".$domain."</creator>\r\n";
			$xml.="<title>".emptyTag($item['goods_name'])."</title>\r\n";
			$xml.="<publishdate>".date('Y-m-d',$begin_time)."</publishdate>\r\n";
			$img = str_replace(APP_ROOT."./public/",$domain."/public/",$item['img']);
	        $img = str_replace("./public/",$domain."/public/",$item['img']);
			$xml.="<imageaddress1>".$img."</imageaddress1>\r\n";
			$xml.="<imagealt1><![CDATA[".$item['goods_name']."]]></imagealt1>\r\n";
			$xml.="<imagelink1>".convertUrl($url)."</imagelink1>\r\n";
			$xml.="<content1><![CDATA[".emptyTag($item['sub_name'])."]]></content1>\r\n";
			$xml.="<linktext1><![CDATA[".emptyTag($item['sub_name'])."]]></linktext1>\r\n";
			$xml.="<linktarget1>".convertUrl($url)."</linktarget1>\r\n";
			
			//$xml.="<content2>".emptyTag($item['goods_name'])."</content2>\r\n";
			$xml.="<content2>".round($item['origin_price'],2)."</content2>\r\n";
			$xml.="<content3>".round($item['current_price'],2)."</content3>\r\n";
			$xml.="<content4>".$rebate."</content4>\r\n";
			$xml.="<content5>".emptyTag($item['cate_name'])."</content5>\r\n";
			$xml.="<content6>".$item['city_name']."</content6>\r\n";
			$xml.="<content7>".$item['id']."</content7>\r\n";
			
			$xml.="<linktext2>".iconv('utf-8','gbk',app_conf("SHOP_TITLE"))."</linktext2>\r\n";
			$xml.="<linktarget2>".get_domain().APP_ROOT."</linktarget2>\r\n";
			$xml.="<content8>".date('Y-m-d H:m:s',$begin_time)."</content8>\r\n";
			$xml.="<content9>".date('Y-m-d H:m:s',$end_tiime)."</content9>\r\n";
			$xml.="<valid>1</valid>\r\n";
			$xml.="</item>\r\n";
		}
		$xml.="</datalist>\r\n";
		$xml.="</sdd>\r\n";
		echo $xml;
?>