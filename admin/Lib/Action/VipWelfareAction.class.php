<?php

class VipWelfareAction extends CommonAction {

    public function given_record() {
    	
    	$this->assign("vip_list",M("VipType")->where('is_effect = 1 and is_delete = 0 order by sort ')->findAll());
    	
    	if(trim($_REQUEST['user_name'])!='')
		{
			$sqlid  ="select group_concat(id) from ".DB_PREFIX."user where user_name like '%".trim($_REQUEST['user_name'])."%'";
			$ids = $GLOBALS['db']->getOne($sqlid);
			$sql = "and gr.user_id in ($ids)";
		}
		
		
		if(isset($_REQUEST['given_name_type']))
		{
			if($_REQUEST['given_name_type']>0){
				$sql = "$sql and gr.given_name_type ='".$_REQUEST['given_name_type']."'  ";
			}else{
				$sql =$sql;
			}
		}
		
		if(isset($_REQUEST['given_type']))
		{
			if($_REQUEST['given_type']>0){
				$sql = "$sql and gr.given_type ='".$_REQUEST['given_type']."'  ";
			}else{
				$sql =$sql;
			}
		}
		
		if(isset($_REQUEST['vip_id']))
		{
			if(trim($_REQUEST['vip_id'])!=-1 && isset($_REQUEST['vip_id']) ){
				$sql = "$sql and gr.vip_id ='".trim($_REQUEST['vip_id'])."'";
			}
		}
		
		$begin_time  = trim($_REQUEST['begin_time']);
		$end_time  = trim($_REQUEST['end_time']);
		if($begin_time !='' || $end_time !=''){
			if($end_time==0)
			{
				$sql = "$sql and gr.send_date >='$begin_time' ";
			}
			else
				$sql = "$sql and gr.send_date between '$begin_time' and '$end_time' ";
		}
		
		$sql=" select gr.*,vf.name as given_name from ".DB_PREFIX."given_record gr " .
			 "left join ".DB_PREFIX."vip_gift vg on vg.id = gr.gift_id  " .
			 "left join ".DB_PREFIX."vip_festivals vf on vf.id = gr.gift_id  " .
			 " where 1=1 $sql ";
		//接受 sost参数 0 表示倒序 非0都 表示正序
		if (isset ( $_REQUEST ['_sort'] )) {
			$sort = $_REQUEST ['_sort'] ? 'asc' : 'desc';
		}
		else{
			$sql="$sql order by id desc";
		}
		
		$model = D();
		$voList = $this->_Sql_list($model, $sql, false);
		//error_reporting(0); 
		$this->display();	
    }
    
    public function score_exchange() {
    	
    	$this->assign("vip_list",M("VipType")->where('is_effect = 1 and is_delete = 0 order by sort ')->findAll());
    	
    	if(trim($_REQUEST['user_name'])!='')
		{
			$sql  ="select group_concat(id) from ".DB_PREFIX."user where user_name like '%".trim($_REQUEST['user_name'])."%'";
			$ids = $GLOBALS['db']->getOne($sql);
			$map[DB_PREFIX.'score_exchange_record.user_id'] = array("in",$ids);
		}
		
		$begin_time  = trim($_REQUEST['begin_time']);
		$end_time  = trim($_REQUEST['end_time']);
		if($begin_time !='' || $end_time !=''){
			if($end_time==0)
			{
				$map[DB_PREFIX.'score_exchange_record.exchange_date'] = array('egt',$begin_time);
			}
			else
				$map[DB_PREFIX.'score_exchange_record.exchange_date']= array("between",array($begin_time,$end_time));
				
		}
		
		$model = D ("ScoreExchangeRecord");
		
		if (! empty ( $model )) {
			$this->_list ( $model, $map );
		}
		$this->display ();
    }
    
public function export_csv($page = 1)
    {
    	set_time_limit(0);
    	$limit = (($page - 1)*intval(app_conf("BATCH_PAGE_SIZE"))).",".(intval(app_conf("BATCH_PAGE_SIZE")));
    
    	//定义条件
   		 if(trim($_REQUEST['user_name'])!='')
		{
			$sqlid  ="select group_concat(id) from ".DB_PREFIX."user where user_name like '%".trim($_REQUEST['user_name'])."%'";
			$ids = $GLOBALS['db']->getOne($sqlid);
			$sql = "and gr.user_id in ($ids)";
		}
		
		
		if(isset($_REQUEST['given_name_type']))
		{
			if($_REQUEST['given_name_type']>0){
				$sql = "$sql and gr.given_name_type ='".$_REQUEST['given_name_type']."'  ";
			}else{
				$sql =$sql;
			}
		}
		
		if(isset($_REQUEST['given_type']))
		{
			if($_REQUEST['given_type']>0){
				$sql = "$sql and gr.given_type ='".$_REQUEST['given_type']."'  ";
			}else{
				$sql =$sql;
			}
		}
		
		if(isset($_REQUEST['vip_id']))
		{
			if(trim($_REQUEST['vip_id'])!=-1 && isset($_REQUEST['vip_id']) ){
				$sql = "$sql and gr.vip_id ='".trim($_REQUEST['vip_id'])."'";
			}
		}
		
		$begin_time  = trim($_REQUEST['begin_time']);
		$end_time  = trim($_REQUEST['end_time']);
		if($begin_time !='' || $end_time !=''){
			if($end_time==0)
			{
				$sql = "$sql and gr.send_date >='$begin_time' ";
			}
			else
				$sql = "$sql and gr.send_date between '$begin_time' and '$end_time' ";
		}
		
		$sql=" select gr.*,vf.name as given_name from ".DB_PREFIX."given_record gr " .
				"left join ".DB_PREFIX."vip_gift vg on vg.id = gr.gift_id  " .
				"left join ".DB_PREFIX."vip_festivals vf on vf.id = gr.gift_id  " .
				" where 1=1 $sql ";
    
    	$list = $GLOBALS['db']->getAll($sql);;
    	
    	if($list)
    	{
    		register_shutdown_function(array(&$this, 'export_csvs'), $page+1);
    		$welfare_value = array('id'=>'""','user_id'=>'""','vip_id'=>'""','given_name'=>'""','given_value'=>'""','given_num'=>'""','send_date'=>'""','send_state'=>'""');
    		if($page == 1)
    			$content = iconv("utf-8","gbk","编号,VIP会员,VIP等级,礼品名称,礼品值,数量,发放日期,发放状态");
    		if($page==1) $content = $content . "\n";
    
    		foreach($list as $k=>$v)
    		{
    			
    			$list[$k]['vip_id'] = M("VipType")->where(" id=".$list[$k]['vip_id'])->getField("vip_grade");
    			if($list[$k]['given_name_type']==1){
    				$list[$k]['given_name'] =  "生日礼品";
    			}
    			elseif($list[$k]['given_name_type']==2){
    				if($list[$k]['gift_id']>0){
    					$list[$k]['given_name'] =  "节日礼品"."(".$list[$k]['given_name'].")";
    				}else{
    					$list[$k]['given_name'] =  "节日礼品";
    				}
    					
    			}
    			
    			if($list[$k]['given_type']==1){
    				$list[$k]['given_value'] = $list[$k]['given_value']." 元现金红包";
    			}else{
    				$list[$k]['given_value'] = $list[$k]['given_value']." 积分";
    			}
    			
    				
    			if($list[$k]['send_state']){
    				$list[$k]['send_state'] = "已发放";
    			}else{	
    				$list[$k]['send_state'] = "未发放";
    			}
    				
    			$welfare_value = array();
    			$welfare_value['id'] = iconv('utf-8','gbk','"' . $v['id'] . '"');
    			$welfare_value['user_id'] = iconv('utf-8','gbk','"' . get_user_name_reals($v['user_id']) . '"');
    			$welfare_value['vip_id'] = iconv('utf-8','gbk','"' . $list[$k]['vip_id']. '"');
    			$welfare_value['given_name'] = iconv('utf-8','gbk','"' . $v['given_name'] . '"');
    			$welfare_value['given_value'] = iconv('utf-8','gbk','"' . $v['given_value'] . '"');
    			$welfare_value['given_num'] = iconv('utf-8','gbk','"' . $v['given_num'] . '"');
    			$welfare_value['send_date'] = iconv('utf-8','gbk','"' . $v['send_date'] . '"');
    			$welfare_value['send_state'] = iconv('utf-8','gbk','"' .$list[$k]['send_state'] . '"');
    
    			$content .= implode(",", $welfare_value) . "\n";
    		}
    			
    			
    		header("Content-Disposition: attachment; filename=welfare_value_list.csv");
    		echo $content;
    	}
    	else
    	{
    		if($page==1)
    			$this->error(L("NO_RESULT"));
    	}
    
    }
    
    
    public function score_export_csv($page = 1)
    {
    	set_time_limit(0);
    	$limit = (($page - 1)*intval(app_conf("BATCH_PAGE_SIZE"))).",".(intval(app_conf("BATCH_PAGE_SIZE")));
    
    	//定义条件
    	if(trim($_REQUEST['user_name'])!='')
		{
			$sql  ="select group_concat(id) from ".DB_PREFIX."user where user_name like '%".trim($_REQUEST['user_name'])."%'";
			$ids = $GLOBALS['db']->getOne($sql);
			$map[DB_PREFIX.'score_exchange_record.user_id'] = array("in",$ids);
		}
		
		$begin_time  = trim($_REQUEST['begin_time']);
		$end_time  = trim($_REQUEST['end_time']);
		if($begin_time !='' || $end_time !=''){
			if($end_time==0)
			{
				$map[DB_PREFIX.'score_exchange_record.exchange_date'] = array('egt',$begin_time);
			}
			else
				$map[DB_PREFIX.'score_exchange_record.exchange_date']= array("between",array($begin_time,$end_time));
				
		}
    
    	$list = M("ScoreExchangeRecord")
		->where($map)
		->limit($limit)->findAll();
	
    	 
    	if($list)
    	{
    		register_shutdown_function(array(&$this, 'export_csv_score'), $page+1);
    		$score_list = array('id'=>'""','user_id'=>'""','vip_id'=>'""','integral'=>'""','cash'=>'""','exchange_date'=>'""');
    		if($page == 1)
    			$content = iconv("utf-8","gbk","编号,VIP会员,VIP等级,兑现积分,兑现金额,兑现日期");
    		
    		if($page==1) $content = $content . "\n";
    
    		foreach($list as $k=>$v)
    		{	
    			$list[$k]['integral'] = $list[$k]['integral']." 积分";
    			$list[$k]['vip_id'] =  M("VipType")->where(" id=".$list[$k]['vip_id'])->getField("vip_grade");
    			$score_list = array();
    			$score_list['id'] = iconv('utf-8','gbk','"' . $list[$k]['id'] . '"');
    			$score_list['user_id'] = iconv('utf-8','gbk','"' . get_user_name_reals($v['user_id']) . '"');
    			$score_list['vip_id'] = iconv('utf-8','gbk','"' . $list[$k]['vip_id']. '"');
    			$score_list['integral'] = iconv('utf-8','gbk','"' . $list[$k]['integral'] . '"');
    			$score_list['cash'] = iconv('utf-8','gbk','"' . format_price($v['cash']) . '"');
    			$score_list['exchange_date'] = iconv('utf-8','gbk','"' . $v['exchange_date'] . '"');
    
    			$content .= implode(",", $welfare_value) . "\n";
    		}
    		 
    		header("Content-Disposition: attachment; filename=score_list.csv");
    		echo $content;
    	}
    	else
    	{
    		if($page==1)
    			$this->error(L("NO_RESULT"));
    	}
    
    }
    
    
}
?>