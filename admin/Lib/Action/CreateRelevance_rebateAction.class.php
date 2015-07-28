<?php
// +----------------------------------------------------------------------
// | Fanwe 方维p2p借贷系统
// +----------------------------------------------------------------------
// | Copyright (c) 2011 http://www.fanwe.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 云淡风轻(88522820@qq.com)
// +----------------------------------------------------------------------

class CreateRelevance_rebateAction extends CommonAction{
	public function index()
	{	
		$user_name  = trim($_REQUEST['user_name']);
		$authorized_name  = trim($_REQUEST['authorized_name']);
		$status  = trim($_REQUEST['status']);
		
		if (isset ( $_REQUEST ['_order'] )) {
			$sorder = $_REQUEST ['_order'];
		}
		else{
			$sorder = "id";
		}
		switch($sorder){
			case "user_name":
				$order =$sorder;
				break;
			case "email ":
				$order ="email";
				break;
			case "status":
				$order ="pid";
				break;
			case "humans":
				$order ="pid";
				break;
			case "referer_memo":
				$order ="referer_memo";
				break;
			default :
				$order =$sorder;
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
		
		
		
		
		$condition = " u_p.user_type =3 ";
		
		if($user_name!=="" && $user_name!==0)
			$condition.=" and u.user_name like '%".$user_name."%' ";
		
		if($authorized_name!=="" && $authorized_name!==0)
			$condition.=" and u_p.user_name like '%".$authorized_name."%' ";
		if($status!=="" && $status!="all")
		{
			if($status == 0)
			{
				$condition.=" and u.pid = 0  ";
			}
			else
			{
				$condition.=" and (u.pid <> 0 && u.referer_memo <>0) ";
			}
		}
			
		$sql_count = "SELECT count(*) FROM ".DB_PREFIX."user u left join ".DB_PREFIX."user u_p on u.referer_memo = u_p.id WHERE $condition ";

		$count = $GLOBALS['db']->getOne($sql_count);
	
		if (! empty ( $_REQUEST ['listRows'] )) {
			$listRows = $_REQUEST ['listRows'];
		} else {
			$listRows = '';
		}
		
		$p = new Page ( $count, $listRows );
		if($count>0){
			$sql = "SELECT u.id,u.user_name,u.email,u.pid,u.referer_memo FROM ".DB_PREFIX."user u left join ".DB_PREFIX."user u_p on u.referer_memo = u_p.id WHERE  $condition  ORDER BY $order $sort LIMIT ".($p->firstRow . ',' . $p->listRows);
		
			$list = $GLOBALS['db']->getAll($sql);
			foreach($list as $k=>$v){
				if($list[$k]['pid'] == 0){
					$list[$k]['status']="未关联";
				}else{
					$list[$k]['status']="已关联";
				}
				$list[$k]['humans'] = $user_name =  M("User")->where("id=".$list[$k]['referer_memo']." and is_delete = 0")->getField("user_name");;  
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
		$sql = "SELECT id,user_name,pid,referer_memo FROM ".DB_PREFIX."user WHERE id= ".$user_id ;
		$list = $GLOBALS['db']->getRow($sql);
		$rel_user_name = $GLOBALS['db']->getOne("select user_name from ".DB_PREFIX."user where id =".$list['referer_memo']);
		$list['rel_user_name'] = $rel_user_name;
		$this->assign("list",$list);
		$this->display();
	}
	

	public function update()
	{	
		$id=intval($_REQUEST['id']);
		//$referer_memo = strim($_REQUEST['referer_memo']);
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
			$data['referer_memo'] = $pid;
			
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
			$data['referer_memo'] = $pid;
			$list = M('User')->where('id='.$id)->save($data);
			save_log($id.L("UPDATE_SUCCESS"),1);
			$this->success(L("UPDATE_SUCCESS"));
		}else{
			$this->error(L("无关联推广人"));
		}
		
	}
		
}
?>