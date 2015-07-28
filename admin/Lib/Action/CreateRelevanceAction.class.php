<?php
// +----------------------------------------------------------------------
// | Fanwe 方维p2p借贷系统
// +----------------------------------------------------------------------
// | Copyright (c) 2011 http://www.fanwe.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 云淡风轻(88522820@qq.com)
// +----------------------------------------------------------------------

class CreateRelevanceAction extends CommonAction{
	public function index()
	{	
		$user_name  = strim($_REQUEST['user_name']);
		if (isset ( $_REQUEST ['_order'] )) {
			$sorder = $_REQUEST ['_order'];
		}
		else{
			$sorder = "id";
		}
		switch($sorder){
			case "user_name":
				$order ="u.".$sorder;
				break;
			case "email ":
				$order ="u.email";
				break;
			case "status":
				$order ="u.pid";
				break;
			case "humans":
				$order ="u.pid";
				break;
			case "referer_memo":
				$order ="u.referer_memo";
				break;
			case "referral_rate":
				$order ="u.referral_rate";
				break;
			default :
				$order ="u.".$sorder;
				break;
		}
		
		//排序方式默认按照倒序排列
		//接受 sost参数 0 表示倒序 非0都 表示正序
		if (isset ( $_REQUEST ['_sort'] )) {
			$sort = $_REQUEST ['_sort'] ? 'asc' : 'desc';
		}
		else{
			$sort = "desc";
		}
		
		$condition = " ( ru.user_type in (0,1) OR u.pid = 0 ) and u.user_type IN (0,1) ";
		if($user_name != "" )
		$condition.=" and u.user_name like '%".$user_name."%' ";
		
		$sql_count = "SELECT count(DISTINCT u.id) FROM ".DB_PREFIX."user u left join ".DB_PREFIX."user ru ON ru.id =u.pid  WHERE  $condition ";
		
		$count = $GLOBALS['db']->getOne($sql_count);
	
		if (! empty ( $_REQUEST ['listRows'] )) {
			$listRows = $_REQUEST ['listRows'];
		} else {
			$listRows = '';
		}
		
		$p = new Page ( $count, $listRows );
		if($count>0){
			$sql = "SELECT u.id,u.user_name,u.email,u.pid,u.referer_memo,u.referral_rate FROM ".DB_PREFIX."user u left join ".DB_PREFIX."user ru ON ru.id =u.pid WHERE  $condition  ORDER BY $order $sort LIMIT ".($p->firstRow . ',' . $p->listRows);
			$list = $GLOBALS['db']->getAll($sql);
			foreach($list as $k=>$v){
				if($list[$k]['pid'] == 0){
					$list[$k]['status']="未关联";
				}else{
					$list[$k]['status']="已关联";
				}
				$list[$k]['humans'] = get_user_name_real($list[$k]['pid']);  
			}
			$this->assign("list",$list);
		}
	
		$page = $p->show();
		
		$sortImg = $sort; //排序图标
		$sortAlt = $sort == 'desc' ? l("ASC_SORT") : l("DESC_SORT"); //排序提示
		$sort = $sort == 'desc' ? 1 : 0; //排序方式
		
		$this->assign ( 'sort', $sort );
		$this->assign ( "page", $page );
		$this->display();
	}
	
	
	
	public function edit()
	{
		$user_id = intval($_REQUEST['id']);
		$sql = "SELECT id,user_name,pid,referer_memo,referral_rate FROM ".DB_PREFIX."user WHERE id= ".$user_id ;
		$list = $GLOBALS['db']->getRow($sql);
		$rel_user_name = $GLOBALS['db']->getOne("select user_name from ".DB_PREFIX."user where id =".$list['pid']);
		$list['rel_user_name'] = $rel_user_name;
		$this->assign("list",$list);
		$this->display();
	}
	

	public function update()
	{	
		$id=intval($_REQUEST['id']);
		$referer_memo = strim($_REQUEST['referer_memo']);
		$referral_rate = floatval($_REQUEST['referral_rate']);
		$rel_user_name = strim($_REQUEST['rel_user_name']);
		$pid = M("User")->where("user_name='".$rel_user_name."'")->getField("id");
		
		if($rel_user_name=="")
		{
			$this->error(L("推荐人不能为空"));
		}
		if($id == $pid)
		{
			$this->error(L("推荐人不能是自己"));
		}
		if(isset($pid))
		{
			$data = array();
			$data['pid'] = $pid;
			$data['referer_memo'] = $referer_memo;
			$data['referral_rate'] = $referral_rate;
			
			$list = M('User')->where('id='.$id)->save($data);
			
			//成功提示
			save_log($id.L("UPDATE_SUCCESS"),1);
			$this->success(L("UPDATE_SUCCESS"));
		}else{
			$this->error(L("RELEVANCE_USER_NO"));
		}
	}
	
	//取消关联
	public function del_referrals()
	{
		$id=intval($_REQUEST['id']);
		$pid = M("User")->where("id=".$id)->getField("pid");
		if($pid != "" && $pid !=0)
		{
			$data = array();
			$data['pid'] = "";
			$data['referer_memo'] = "";
			$list = M('User')->where('id='.$id)->save($data);
			save_log($id.L("UPDATE_SUCCESS"),1);
			$this->success(L("UPDATE_SUCCESS"));
		}else{
			$this->error(L("无关联推广人"));
		}
		
	}
	
	

	public function export_csv($page = 1)
	{
		set_time_limit(0);
		$limit = (($page - 1)*intval(app_conf("BATCH_PAGE_SIZE"))).",".(intval(app_conf("BATCH_PAGE_SIZE")));
	
		//定义条件
		$user_name  = strim($_REQUEST['user_name']);
		$condition = " ( ru.user_type in (0,1) OR u.pid = 0 ) and u.user_type IN (0,1) ";
		if($user_name != "" )
		$condition.=" and u.user_name like '%".$user_name."%' ";
		
		$sql = "SELECT u.id,u.user_name,u.email,u.pid,u.referer_memo,u.referral_rate FROM ".DB_PREFIX."user u left join ".DB_PREFIX."user ru ON ru.id =u.pid WHERE  $condition LIMIT ".$limit;
		$list = $GLOBALS['db']->getAll($sql);
		foreach($list as $k=>$v){
			if($list[$k]['pid'] == 0){
				$list[$k]['status']="未关联";
			}else{
				$list[$k]['status']="已关联";
			}
			$list[$k]['humans'] = get_user_name_reals($list[$k]['pid']);  
		}
		if($list)
		{
			register_shutdown_function(array(&$this, 'export_csv'), $page+1);
			
			$user_value = array('id'=>'""','id'=>'""','email'=>'""','status'=>'""','humans'=>'""','referer_memo'=>'""','referral_rate'=>'""');
			if($page == 1)
				$content = iconv("utf-8","gbk","编号,用户名,邮箱,状态,推广人,备注,返利%");
			$content = $content . "\n";
			foreach($list as $k=>$v)
			{
				$create_relevance = array();
				$create_relevance['id'] = iconv('utf-8','gbk','"' . $v['id'] . '"');
				$create_relevance['user_name'] = iconv('utf-8','gbk','"' . get_user_name_reals($v['id']) . '"');
				$create_relevance['email'] = iconv('utf-8','gbk','"' . $v['email'] . '"');
				$create_relevance['status'] = iconv('utf-8','gbk','"' . $v['status'] . '"');
				$create_relevance['humans'] = iconv('utf-8','gbk','"' . $v['humans'] . '"');
				$create_relevance['referer_memo'] = iconv('utf-8','gbk','"' . $v['referer_memo'] . '"');
				$create_relevance['referral_rate'] = iconv('utf-8','gbk','"' . $v['referral_rate'] . '"');
				
				$content .= implode(",", $create_relevance) . "\n";
			}
			header("Content-Disposition: attachment; filename=create_relevance.csv");
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