<?php
// +----------------------------------------------------------------------
// | Fanwe 方维p2p借贷系统
// +----------------------------------------------------------------------
// | Copyright (c) 2011 http://www.fanwe.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 云淡风轻(88522820@qq.com)
// +----------------------------------------------------------------------

require APP_ROOT_PATH.'app/Lib/deal.php';
class search
{
	public function index(){
		$root = array();
		//利率
		$interest_url = array(
				array(
						"interest"=>0,
						"name" => "不限",
				),
				array(
						"interest"=>10,
						"name" => "10%",
				),
				array(
						"interest"=>12,
						"name" => "12%",
				),
				array(
						"interest"=>15,
						"name" => "15%",
				),
				array(
						"interest"=>18,
						"name" => "18",
				),
		);
		foreach($interest_url as $k=>$v){
			$tmp_args = $page_args;
			$tmp_args['interest'] = $v['interest'];
			$interest_url[$k]['url'] = url("index","deals#index",$tmp_args);
		}
		$root['interest_url'] = $interest_url;
			
			
		//几天内
		$lefttime_url = array(
				array(
						"lefttime"=>0,
						"name" => "不限",
				),
				array(
						"lefttime"=>1,
						"name" => "1天",
				),
				array(
						"lefttime"=>3,
						"name" => "3天",
				),
				array(
						"lefttime"=>6,
						"name" => "6天",
				),
				array(
						"lefttime"=>9,
						"name" => "9天",
				),
				array(
						"lefttime"=>12,
						"name" => "12天",
				),
		);
		foreach($lefttime_url as $k=>$v){
			$tmp_args = $page_args;
			$tmp_args['lefttime'] = $v['lefttime'];
			$lefttime_url[$k]['url'] = url("index","deals#index",$tmp_args);
		}
		$root['lefttime_url'] = $lefttime_url;
			
		
		//借款期限
		$months_type_url = array(
				array(
						"name" => "不限",
				),
				array(
						"name" => "3 个月以下",
				),
				array(
						"name" => "3-6 个月",
				),
				array(
						"name" => "6-9 个月",
				),
				array(
						"name" => "9-12 个月",
				),
				array(
						"name" => "12 个月以上",
				),
		);
		foreach($months_type_url as $k=>$v){
			$tmp_args = $page_args;
			$tmp_args['months_type'] = $k;
			$months_type_url[$k]['url'] = url("index","deals#index",$tmp_args);
		}
		$root['months_type_url'] = $months_type_url;
			
			
		//标状态
		$deal_status_url = array(
				array(
						"name" => "不限",
				),
				array(
						"name" => "进行中",
				),
				array(
						"name" => "满标",
				),
				array(
						"name" => "流标",
				),
				array(
						"name" => "还款中",
				),
				array(
						"name" => "已还清",
				),
		);
		foreach($deal_status_url as $k=>$v){
			$tmp_args = $page_args;
			$tmp_args['deal_status'] = $k;
			$deal_status_url[$k]['url'] = url("index","deals#index",$tmp_args);
		}
		$root['deal_status_url'] = $deal_status_url;
			
			
		//会员等级
		$level_list_url = array();
		$tmp_args = $page_args;
		$tmp_args['level'] = 0;
		$level_list_url[0]['url'] = url("index","deals#index",$tmp_args);
		$level_list_url[0]['name'] = "不限";
		foreach($level_list['list'] as $k=>$v){
			$tmp_args = $page_args;
			$tmp_args['level'] = $v['id'];
			$level_list_url[$k+1] = $v;
			$level_list_url[$k+1]['url'] = url("index","deals#index",$tmp_args);
		}
		$root['level_list_url'] = $level_list_url;
		
		
		
		$root['program_title'] = "收索";
		output($root);		
	}
}
?>
