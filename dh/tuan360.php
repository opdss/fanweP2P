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
    						array('name'=>'360团购导航[http://tuan.360.cn/]','url'=>'tuan360.php')    						
    				   );
    return $api;
}
require_once "api.php";

	header('Content-type: text/xml; charset=utf-8');
		$sql = "SELECT d.id,d.supplier_id,d.discount,c.name as cate_name,d.icon,d.city_id,d.name as goods_name,d.img,d.current_price,d.origin_price,d.begin_time,d.end_time,d.brief as goodsbrief,dc.name as city_name,s.name as supplier_name,d.buy_count ".
					'FROM '.DB_PREFIX.'deal as d '.
					'left join '.DB_PREFIX.'deal_city as dc on dc.id = d.city_id '.
					'left join '.DB_PREFIX.'supplier as s on s.id = d.supplier_id '.
					'left join '.DB_PREFIX.'deal_cate as c on c.id = d.cate_id '.
					"where d.is_effect = 1 and d.is_shop = 0 and d.is_delete = 0 and d.time_status = 1 and d.buy_status < 2  group by d.id order by d.sort desc,d.id desc";
		
		$list = $GLOBALS['db']->getAll($sql);

		$xml="<?xml version=\"1.0\" encoding=\"utf-8\"?>\r\n";
		$xml.="<data>\r\n";
		$xml.="<site_name>".app_conf("SHOP_TITLE")."</site_name> \r\n";
		$xml.="<goodsdata>\r\n";
		$index = 0;
		
		foreach($list as $item)
		{
			$index++;
			
			$xml.="<goods id=\"$index\">\r\n";
				
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
				
			$supplier = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."supplier_location where supplier_id = ".$item['supplier_id']." and is_main = 1");
			
			$address = "";
			if($supplier)
			{
				$address = emptyTag($supplier['address']);
				$map = convertUrl("http://ditu.google.cn/maps?f=q&source=s_q&hl=zh-CN&geocode=&q=".$supplier['api_address']);
			}
			
			$xml.="<city_name>".$item['city_name']."</city_name>\r\n";
			$xml.="<site_url>".get_domain().APP_ROOT."</site_url>\r\n";
			$xml.="<title>".mb_substr(emptyTag($item['goods_name']),0,10,'utf-8')."</title>\r\n";
			$xml.="<goods_url>".convertUrl($url)."</goods_url>\r\n";
			$xml.="<desc>".emptyTag($item['goods_name'])."</desc>\r\n";
			if((strstr($item['cate_name'],'餐饮')!=false)||strstr($item['cate_name'],'美食')!=false){
			$cate_name='餐饮美食';
			}
			elseif((strstr($item['cate_name'],'休闲')!=false)||strstr($item['cate_name'],'娱乐')!=false){
			$cate_name='休闲娱乐';
			}
			elseif((strstr($item['cate_name'],'美容')!=false)||strstr($item['cate_name'],'保健')!=false){
			$cate_name='美容保健';
			}
			elseif((strstr($item['cate_name'],'精品')!=false)||strstr($item['cate_name'],'购物')!=false){
			$cate_name='精品购物';
			}
			elseif((strstr($item['cate_name'],'优惠')!=false)||strstr($item['cate_name'],'券票')!=false){
			$cate_name='优惠券票';
		    }
			else
				$cate_name='其他';

			$xml.="<class>".$cate_name."</class>\r\n";
			
			$domain = app_conf("PUBLIC_DOMAIN_ROOT")==''?get_domain().APP_ROOT:app_conf("PUBLIC_DOMAIN_ROOT");
	        $img = str_replace(APP_ROOT."./public/",$domain."/public/",$item['img']);	
	        $img = str_replace("./public/",$domain."/public/",$item['img']);
	        
			$xml.="<img_url>".$img."</img_url>\r\n";
			$xml.="<original_price>".number_format(round($item['origin_price'],2), 2, '.', '')."</original_price>\r\n";
			$xml.="<sale_price>".number_format(round($item['current_price'],2), 2, '.', '')."</sale_price>\r\n";
			$xml.="<sale_rate>".$rebate."</sale_rate>\r\n";
			$xml.="<sales_num>".$item['buy_count']."</sales_num>\r\n";
			$xml.="<start_time>".to_date($begin_time,"YmdHis")."</start_time>\r\n";
			$xml.="<close_time>".to_date($end_tiime,"YmdHis")."</close_time>\r\n";
			$xml.="<address>$address</address>\r\n";
			$xml.="<map>$map</map>\r\n";
			$xml.="<coupon_start_time></coupon_start_time>\r\n";
			$xml.="<coupon_close_time></coupon_close_time>\r\n";
			$xml.="</goods>\r\n";
		}
		
		$xml.="</goodsdata>\r\n";
		$xml.="</data>\r\n";
		echo $xml;
?>