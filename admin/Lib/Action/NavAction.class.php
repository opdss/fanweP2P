<?php
// +----------------------------------------------------------------------
// | Fanwe 方维p2p借贷系统
// +----------------------------------------------------------------------
// | Copyright (c) 2011 http://www.fanwe.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 云淡风轻(88522820@qq.com)
// +----------------------------------------------------------------------

class NavAction extends CommonAction{
	private $navs;

	public function __construct()
	{
		parent::__construct();
		$this->navs = array(
			'index' => array(
				'name'	=>	l('NAV_INDEX_MODULE'),  //首页
				'app_index'	=>	'index'
			),
			'deal' => array(
				'app_index'	=>	'index',
				'name'	=>	l('NAV_TUAN_MODULE')  //今日借款
			),
			'transfer' => array(
				'app_index'	=>	'index',
				'name'	=>	l('NAV_TRANSFER_MODULE'),  //债权转让
				'acts'	=> array(
					'index'		=>	"转让首页",
					'detail'	=>	'转让详情',
				),
			),
			'score'	=>	array(
				'app_index'	=>	'index',
				'name'	=>	'积分商城'
			),
			'goods'	=>	array(
				'app_index'	=>	'index',
				'name'	=>	'积分商品'
			),
			'aboutp2p' => array(
				'app_index'	=>	'index',
				'name'	=>	"平台原理"  //平台原理
			),
			'aboutlaws' => array(
				'app_index'	=>	'index',
				'name'	=>	"政策法规"  //政策法规
			),
			'aboutfee' => array(
				'app_index'	=>	'index',
				'name'	=>	"借贷费率"  //借贷费率
			),
			'tool' => array(
				'app_index'	=>	'index',
				'name'	=>	"工具箱"  //平台原理
			),
			'belender' => array(
				'app_index'	=>	'index',
				'name'	=>	"成为借出者"  //成为借出者
			),
			'article' => array(
				'app_index'	=>	'index',
				'name'	=>	l('NAV_ARTICLE_MODULE') //文章
			),
			'acate' => array(
				'app_index'	=>	'index',
				'name'	=>	l('NAV_ARTICLE_CATE_MODULE') //文章分类
			),
			'borrow' => array(
				'app_index'	=>	'index',
				'name'	=>	l('NAV_BORROW_MODULE'),  //我要贷款
				'acts'	=> array(
					'index'		=>	"贷款首页",
					'aboutborrow'	=>	'贷款说明',
					'creditswitch' =>'信用认证',
				),
			),
			'uc_center' => array(
				'app_index'	=>	'index',
				'name'	=>	l('NAV_UC_CENTER_MODULE'),  //产品分类
			),
			'uc_deal' => array(
				'app_index'	=>	'index',
				'name'	=>	"贷款管理",  //贷款管理
				'acts'	=> array(
					'refund'		=>	"偿还贷款",
				),
			),
			'uc_invest' => array(
				'app_index'	=>	'index',
				'name'	=>	"我的投标",  //我的投标
			),
			'uc_account' => array(
				'app_index'	=>	'index',
				'name'	=>	"帐户设置",  //帐户设置
			),
			'deals' => array(  //团购列表
				'app_index'	=>	'index',
				'name'	=>	l('NAV_DEALS_MODULE'),
				'acts'	=> array(
					'index'		=>	l('NAV_DEALS_MODULE_INDEX'),
					'about'	=>	'如何理财',
				),
			),
			
			'guarantee' => array(  //团购列表
				'app_index'	=>	'index',
				'name'	=>	"安全保障",
				'acts'	=> array(
					'index'		=>	"首页",
					'detail'	=>	'列表页',
				),
			),
			
			'msg'	=>	array(
				'app_index'	=>	'index',
				'name'	=>	l('NAV_MESSAGE_MODULE')
			),
			
			'guide'	=>	array(
				'app_index'	=>	'index',
				'name'	=>	'新手指引'
			),
		);
	}
	public function index()
	{
		$condition['pid'] = 0;
		$this->assign("default_map",$condition);
		
		//列表过滤器，生成查询Map对象
		$map = $this->_search ();
		//追加默认参数
		if($this->get("default_map"))
		$map = array_merge($map,$this->get("default_map"));
		
		if (method_exists ( $this, '_filter' )) {
			$this->_filter ( $map );
		}
		$name=$this->getActionName();
		$model = D ($name);
		if (! empty ( $model )) {
			$this->_list ( $model, $map );
		}
		$list = $this->get("list");
		
		$result = array();
		$row = 0;
		foreach($list as $k=>$v)
		{
			$v['level'] = -1;
			$result[$row] = $v;
			$row++;
			$sub_nav = M(MODULE_NAME)->where(array("id"=>array("in",D(MODULE_NAME)->getChildIds($v['id']))))->findAll();
			$sub_nav = D(MODULE_NAME)->toFormatTree($sub_nav,'name');
			foreach($sub_nav as $kk=>$vv)
			{
				$vv['name']	=	$vv['title_show'];
				$result[$row] = $vv;
				$row++;
			}
		}
		//dump($result);exit;
		$this->assign("list",$result);
		$this->display ();
		return;
	}

	public function add() {		
		//定义菜单的部份可选项		
		
		$this->assign("navs",$this->navs);
		
		$parent_nav_tree = M("Nav")->where("pid = 0")->findAll();
		$parent_nav_tree = D("Nav")->toFormatTree($parent_nav_tree,'name');
		$this->assign("parent_nav_tree",$parent_nav_tree);
		
		$this->display ();
	}
	public function edit() {		
		//定义菜单的部份可选项		
		$id = intval($_REQUEST ['id']);		
		$condition['id'] = $id;		
		$vo = M(MODULE_NAME)->where($condition)->find();
		$this->assign ( 'vo', $vo );
		$this->assign("navs",$this->navs);
		
		$parent_nav_tree = M("Nav")->where("pid = 0 AND id<>".$id)->findAll();
		$parent_nav_tree = D("Nav")->toFormatTree($parent_nav_tree,'name');
		$this->assign("parent_nav_tree",$parent_nav_tree);
		
		$this->display ();
	}
	
	public function load_module()
	{
		$id = intval($_REQUEST['id']);
		$module = trim($_REQUEST['module']);
		$act = M(MODULE_NAME)->where("id=".$id)->getField("u_action");
		$this->ajaxReturn($this->navs[$module]['acts'],$act);
	}	
	
	public function update() {
		B('FilterString');
		$data = M(MODULE_NAME)->create ();
		
		$log_info = M(MODULE_NAME)->where("id=".intval($data['id']))->getField("name");
		//开始验证有效性
		$this->assign("jumpUrl",u(MODULE_NAME."/edit",array("id"=>$data['id'])));
		if(!check_empty($data['name']))
		{
			$this->error(L("NAV_NAME_EMPTY_TIP"));
		}	
		if(!check_empty($data['url'])&&$_REQUEST['u_module']=='')
		{
			$this->error(L("NAV_URL_EMPTY_TIP"));
		}		
		
		if($_REQUEST['u_module']!='')
		{
			$data['app_index'] = $this->navs[$data['u_module']]['app_index'];
			$data['url'] = '';
		}
		if($data['url']!='')
		{
			$data['u_module'] = '';
			$data['u_action'] = '';
			$data['u_id'] = '';
			$data['u_param'] = '';
		}
		if(!isset($_REQUEST['u_action']))
		$data['u_action'] = '';
		// 更新数据
		
		$data['u_param'] = trim($data['u_param']);
		$list=M(MODULE_NAME)->save ($data);
		if (false !== $list) {
			//成功提示
			save_log($log_info.L("UPDATE_SUCCESS"),1);
			rm_auto_cache("cache_nav_list_shop");
			rm_auto_cache("cache_nav_list_tuan");
			rm_auto_cache("cache_nav_list_youhui");
			$this->success(L("UPDATE_SUCCESS"));
		} else {
			//错误提示
			$dbErr = M()->getDbError();
			save_log($log_info.L("UPDATE_FAILED").$dbErr,0);
			$this->error(L("UPDATE_FAILED").$dbErr);
		}
	}
	
	
	public function insert() {
		B('FilterString');
		$data = M(MODULE_NAME)->create ();

		//开始验证有效性
		$this->assign("jumpUrl",u(MODULE_NAME."/add"));
		if(!check_empty($data['name']))
		{
			$this->error(L("NAV_NAME_EMPTY_TIP"));
		}	
		if(!check_empty($data['url'])&&$_REQUEST['u_module']=='')
		{
			$this->error(L("NAV_URL_EMPTY_TIP"));
		}		
		
		if($_REQUEST['u_module']!='')
		{
			$data['app_index'] = $this->navs[$data['u_module']]['app_index'];
			$data['url'] = '';
		}
		if($data['url']!='')
		{
			$data['u_module'] = '';
			$data['u_action'] = '';
			$data['u_id'] = '';
			$data['u_param'] = '';
		}
		if(!isset($_REQUEST['u_action']))
		$data['u_action'] = '';
		// 更新数据

		$data['u_param'] = trim($data['u_param']);
		$list=M(MODULE_NAME)->add ($data);
		$log_info = $data['name'];
		if (false !== $list) {
			//成功提示
			save_log($log_info.L("INSERT_SUCCESS"),1);
			rm_auto_cache("cache_nav_list_shop");
			rm_auto_cache("cache_nav_list_tuan");
			rm_auto_cache("cache_nav_list_youhui");
			$this->success(L("INSERT_SUCCESS"));
		} else {
			//错误提示
			$dbErr = M()->getDbError();
			save_log($log_info.L("INSERT_FAILED").$dbErr,0);
			$this->error(L("INSERT_FAILED").$dbErr);
		}
	}
	public function set_sort()
	{
		$id = intval($_REQUEST['id']);
		$sort = intval($_REQUEST['sort']);
		$log_info = M("Nav")->where("id=".$id)->getField("name");
		if(!check_sort($sort))
		{
			$this->error(l("SORT_FAILED"),1);
		}
		M("Nav")->where("id=".$id)->setField("sort",$sort);
		rm_auto_cache("cache_nav_list_shop");
		rm_auto_cache("cache_nav_list_tuan");
		rm_auto_cache("cache_nav_list_youhui");
		save_log($log_info.l("SORT_SUCCESS"),1);
		$this->success(l("SORT_SUCCESS"),1);
	}
	
	public function set_effect()
	{
		$id = intval($_REQUEST['id']);
		$ajax = intval($_REQUEST['ajax']);
		$info = M(MODULE_NAME)->where("id=".$id)->getField("name");
		$c_is_effect = M(MODULE_NAME)->where("id=".$id)->getField("is_effect");  //当前状态
		$n_is_effect = $c_is_effect == 0 ? 1 : 0; //需设置的状态
		M(MODULE_NAME)->where("id=".$id)->setField("is_effect",$n_is_effect);	
		save_log($info.l("SET_EFFECT_".$n_is_effect),1);
		rm_auto_cache("cache_nav_list_shop");
		rm_auto_cache("cache_nav_list_tuan");
		rm_auto_cache("cache_nav_list_youhui");
		$this->ajaxReturn($n_is_effect,l("SET_EFFECT_".$n_is_effect),1)	;	
	}
	
	public function foreverdelete() {
		//彻底删除指定记录
		$ajax = intval($_REQUEST['ajax']);
		$id = $_REQUEST ['id'];
		if (isset ( $id )) {
				$condition = array ('id' => array ('in', explode ( ',', $id ) ) );
				
				$rel_data = M(MODULE_NAME)->where($condition)->findAll();				
				foreach($rel_data as $data)
				{
					$info[] = $data['name'];	
				}
				if($info) $info = implode(",",$info);
				$list = M(MODULE_NAME)->where ( $condition )->delete();	
					
				if ($list!==false) {
					save_log($info.l("FOREVER_DELETE_SUCCESS"),1);
					rm_auto_cache("cache_nav_list_shop");
					rm_auto_cache("cache_nav_list_tuan");
					rm_auto_cache("cache_nav_list_youhui");
					$this->success (l("FOREVER_DELETE_SUCCESS"),$ajax);
				} else {
					save_log($info.l("FOREVER_DELETE_FAILED"),0);
					$this->error (l("FOREVER_DELETE_FAILED"),$ajax);
				}
			} else {
				$this->error (l("INVALID_OPERATION"),$ajax);
		}
	}
}
?>