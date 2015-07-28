<!DOCTYPE html>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=2.0, user-scalable=0,minimum-scale=0.5">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black">
    <meta name="format-detection" content="telephone=no">
    <title><?php echo $this->_var['data']['program_title']; ?></title>
	<link rel="stylesheet" type="text/css" href="./css/font-awesome-4.2.0/css/font-awesome.min.css"><!--特殊字体处理包-->
    <script type="text/javascript" src="./js/jquery.js"></script><!--jquery文档-->
	<script type="text/javascript" src="./js/public.js"></script><!--共有jquery文档-->
	<script type="text/javascript" src="./js/touchScroll.js"></script><!--滑屏轮播插件包-->
	<script type="text/javascript" src="./js/touchslider.dev.js"></script><!--滑屏轮播插件包-->	
    <?php
			$this->_var['parent_pagecss'][] = $this->_var['TMPL_REAL']."/css/public.css";
	?>
	<link rel="stylesheet" type="text/css" href="<?php 
$k = array (
  'name' => 'parse_css',
  'v' => $this->_var['parent_pagecss'],
);
echo $k['name']($k['v']);
?>" />
    <script type="text/javascript">
		var APP_ROOT = '<?php echo $this->_var['APP_ROOT']; ?>';
		var WAP_PATH = '<?php echo $this->_var['WAP_ROOT']; ?>';
	</script> 	
</head>
<body id="top">
<div class="navbar Headerbackground_dark">
    <div class="nav-wrap-left">
    	<!--如果当前是首页就显示logo，else就显示箭号-->
		 <?php if ($this->_var['data']['act'] == 'init'): ?>
        <a class="logo"><!--左边文字logo-->
           <img src="./images/logo.png">
        </a>
		<?php else: ?>
		<a onclick="window.history.go(-1);" class="back"><!--箭号，返回上一页-->
        <i class="fa fa-chevron-left"></i>
        </a>
		<?php endif; ?>
    </div>
    <span><?php if ($this->_var['data']['act'] != 'init'): ?><?php echo $this->_var['data']['program_title']; ?><?php endif; ?></span><!--此处用于输出页面的位置信息，如注册，登录等-->
	    <?php if ($this->_var['data']['act'] == 'deal'): ?><!--关注功能-->
		    <?php if ($this->_var['is_login'] == 1): ?>
			    <a href="javascript:location.reload()" class="collect-but" <?php if ($this->_var['data']['is_faved']): ?>id="J-del-deal-collect-but" <?php else: ?>id="J-deal-collect-but"<?php endif; ?>  dataid="<?php echo $this->_var['data']['deal']['id']; ?>"><?php if ($this->_var['data']['is_faved']): ?>已<?php endif; ?>关注</a>
			<?php endif; ?>
		<?php endif; ?>
		 <?php if ($this->_var['data']['act'] == 'deals'): ?><!--我要投资的列表的搜索功能-->
		   <a href="<?php
echo parse_wap_url_tag("u:index|search|"."".""); 
?>" class="search_but"><i class="fa fa-search"></i></a>
		<?php endif; ?>
		 <?php if ($this->_var['data']['act'] == 'uc_collect'): ?>
		<div class="collect-but" id="uc_collect_editor">编辑</div>
		<?php endif; ?>
        
        <?php if ($this->_var['data']['act'] == 'uc_address'): ?>
		<button class="editor-address-but" id="submitt">保存地址</button>
		<?php endif; ?>
         

	<div class="nav-wrap-right">
		<a class="screen hide" id="screen" href="javascript:void(0);">
        <div class="lead_top"><i class="fa fa-list-ul"></i></div>
        <div class="lead_bottom">导航</div>
        </a>

    </div>


    
   
	<div class="public_menu hide_cont"><!--导航隐藏部分-->
        <div class="Angle"></div><!--小三角-->
	    <ul>
	        <li><a href="<?php
echo parse_wap_url_tag("u:index|init|"."".""); 
?>"><i class="fa fa-home"></i>首页</a></li>
			<li>
	        	<?php if ($this->_var['is_login'] == 1): ?>
	        	<a href="<?php
echo parse_wap_url_tag("u:index|uc_center|"."".""); 
?>">
	        	<?php else: ?>
				<a href="<?php
echo parse_wap_url_tag("u:index|login|"."".""); 
?>">
				<?php endif; ?>	
	        	<i class="fa fa-user"></i>会员中心</a>
			</li>
			<li>
				<?php if ($this->_var['is_login'] == 1): ?>
	        	<a href="<?php
echo parse_wap_url_tag("u:index|uc_invest|"."".""); 
?>">
	        	<?php else: ?>
				<a href="<?php
echo parse_wap_url_tag("u:index|login|"."".""); 
?>">
				<?php endif; ?>	
				<i class="fa fa-database"></i>我的投资</a>
			</li>
			<!--
			<li>
				
	        	<a href="<?php
echo parse_wap_url_tag("u:index|integral_mall|"."".""); 
?>">
	        	
				<i class="fa fa-database"></i>积分商城</a>
			</li>
			-->
		    </ul>
    </div>
</div>

<script>
$(document).ready(function(){
	$("#J-deal-collect-but").click(function(){
		var ajaxurl = '<?php
echo parse_wap_url_tag("u:index|uc_do_collect|"."".""); 
?>';
		var query = new Object();
		query.id =  $.trim($(this).attr("dataid"));
		var obj = $(this);
		$.ajax({ 
			url: ajaxurl,
			data:query,
			type: "POST",
			dataType: "json",
			success: function(result){
				if(result.status==1)
				{
					$(obj).html("已关注");
				}
				else
				{	
				}
			}
		});	
	});
		
				
	$("#J-del-deal-collect-but").click(function(){
		var ajaxurl = '<?php
echo parse_wap_url_tag("u:index|uc_del_collect|"."".""); 
?>';
		var query = new Object();
		query.id =  $.trim($(this).attr("dataid"));
		var obj = $(this);
		$.ajax({ 
			url: ajaxurl,
			data:query,
			type: "POST",
			dataType: "json",
			success: function(result){
				if(result.status==1)
				{
					$(obj).html("关注");
				}
				else
				{	
				}
			}
		});	
	});
});
</script>
 <?php if ($this->_var['data']['act'] == 'register' || $this->_var['data']['act'] == 'register_idno'): ?>
<div class="register_top clearfix">
	<ul class="info">
		<li class="<?php if ($this->_var['data']['act'] == 'register'): ?>current<?php endif; ?>">
			<span>1&nbsp;输入信息&nbsp;</span>
			<i class="fa fa-angle-right"></i>
		</li>
		<li class="<?php if ($this->_var['data']['act'] == 'register_idno'): ?>current<?php endif; ?>">
			<span>2&nbsp;身份验证&nbsp;</span>
			<i class="fa fa-angle-right"></i>
		</li>
		<li>
			<span>3&nbsp;注册成功&nbsp;</span>
			<i class="fa fa-angle-right"></i>
		</li>		
	</ul>
</div>
<?php endif; ?>
 <div class="page_total"><?php echo $this->_var['data']['page']['page_total']; ?></div>
<!--分页总数-->
