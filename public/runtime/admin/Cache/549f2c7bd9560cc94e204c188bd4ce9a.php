<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title><?php echo conf("APP_NAME");?><?php echo l("ADMIN_PLATFORM");?></title>
<script type="text/javascript" src="__ROOT__/public/runtime/admin/lang.js"></script>
<script type="text/javascript">
	var version = '<?php echo app_conf("DB_VERSION");?>';
	var VAR_MODULE = "<?php echo conf("VAR_MODULE");?>";
	var VAR_ACTION = "<?php echo conf("VAR_ACTION");?>";
	var ROOT = '__APP__';
	var ROOT_PATH = '<?php echo APP_ROOT; ?>';
</script>
<link rel="stylesheet" type="text/css" href="__TMPL__Common/style/style.css" />
<link rel="stylesheet" type="text/css" href="__TMPL__Common/style/main.css" />
<script type="text/javascript" src="__TMPL__Common/js/jquery.js"></script>
</head>

<body>
	<div class="main">
	<div class="main_title"><?php echo conf("APP_NAME");?><?php echo l("ADMIN_PLATFORM");?> <?php echo L("HOME");?>	</div>
	<div class="blank5"></div>
	<table class="form" cellpadding=0 cellspacing=0>
		<tr>
			<td colspan=2 class="topTd"></td>
		</tr>
		<tr>
			<td class="item_title" style="width:200px;">
				快捷导航
			</td>
			<td class="item_input">				
				<select name="nav" id="J_nav">
					<?php if(is_array($navs)): foreach($navs as $key=>$nav): ?><option value="<?php echo ($nav["key"]); ?>"><?php echo ($nav["name"]); ?></option><?php endforeach; endif; ?>
				</select>
				<select name="m" id="J_m">
					
				</select>
				<select name="a" id="J_a">
					
				</select>
			</td>
		</tr>
		<tr>
			<td class="item_title" style="width:200px;">
				<?php echo L("TOTAL_REG_USER_COUNT");?>
			</td>
			<td class="item_input">				
				<?php echo sprintf(L("TOTAL_USER_COUNT_FORMAT"),$total_user,$total_verify_user); ?>
			</td>
		</tr>
		<tr>
			<td class="item_title" style="width:200px;">
				注册待验证
			</td>
			<td class="item_input">				
				<a href="<?php echo u("User/register");?>" <?php if($register_count > 0): ?>style="color:#f60;"<?php endif; ?>><?php echo ($register_count); ?>待审核的普通会员</a>
				&nbsp;
				<a href="<?php echo u("User/company_register");?>" <?php if($company_register_count > 0): ?>style="color:#f60;"<?php endif; ?>><?php echo ($company_register_count); ?>待审核的企业会员</a>
			</td>
		</tr>
		<tr>
			<td class="item_title" style="width:200px;">
				待审核的借款
			</td>
			<td class="item_input">				
				<a href="<?php echo u("Deal/publish");?>" <?php if($wait_deal_count > 0): ?>style="color:#f60;"<?php endif; ?>><?php echo ($wait_deal_count); ?>待审核的借款</a>
			</td>
		</tr>
		
		<tr>
			<td class="item_title" style="width:200px;">
				等待材料的借款
			</td>
			<td class="item_input">				
				<a href="<?php echo u("Deal/wait");?>" <?php if($info_deal_count > 0): ?>style="color:#f60;"<?php endif; ?>><?php echo ($info_deal_count); ?>等待材料的借款</a>
			</td>
		</tr>
		
		
		<tr>
			<td class="item_title" style="width:200px;">
				满标的借款
			</td>
			<td class="item_input">				
				<a href="<?php echo u("Deal/full");?>" <?php if($suc_deal_count > 0): ?>style="color:#f60;"<?php endif; ?>><?php echo ($suc_deal_count); ?>满标的借款</a>
			</td>
		</tr>	
		
		
		<tr>
			<td class="item_title" style="width:200px;">
				三日内需还款的借款
			</td>
			<td class="item_input">				
				<a href="<?php echo u("Deal/three",array('status'=>1));?>" <?php if($threeday_repay_count > 0): ?>style="color:#f60;"<?php endif; ?>><?php echo ($threeday_repay_count); ?>期需还款的借款</a>
			</td>
		</tr>
		<tr>
			<td class="item_title" style="width:200px;">
				逾期未还的借款
			</td>
			<td class="item_input">				
				<a href="<?php echo u("Deal/yuqi");?>" <?php if($yq_repay_count > 0): ?>style="color:#f60;"<?php endif; ?>><?php echo ($yq_repay_count); ?>笔逾期</a>
			</td>
		</tr>
		
		<tr>
			<td class="item_title" style="width:200px;">
				待审核的认证
			</td>
			<td class="item_input">		
				<a href="<?php echo u("Credit/user_wait");?>" <?php if($auth_count > 0): ?>style="color:#f60;"<?php endif; ?>><?php echo ($auth_count); ?>待审核的认证</a>
			</td>
		</tr>
		
			<tr>
			<td class="item_title" style="width:200px;">
				待处理的授信额度申请
			</td>
			<td class="item_input">		
				<a href="<?php echo u("DealQuotaSubmit/index",array("status"=>0));?>" <?php if($deal_quota_count > 0): ?>style="color:#f60;"<?php endif; ?>><?php echo ($deal_quota_count); ?>待处理的授信额度申请</a>
			</td>
		</tr>
		
		<tr>
			<td class="item_title" style="width:200px;">
				待处理的额度申请
			</td>
			<td class="item_input">		
				<a href="<?php echo u("QuotaSubmit/index",array("status"=>0));?>" <?php if($quota_count > 0): ?>style="color:#f60;"<?php endif; ?>><?php echo ($quota_count); ?>待处理的额度申请</a>
			</td>
		</tr>
		
	
		
		<tr>
			<td class="item_title" style="width:200px;">
				提现申请
			</td>
			<td class="item_input">
				<a href="<?php echo u("UserCarry/index",array("status"=>0));?>" <?php if($carry_count > 0): ?>style="color:#f60;"<?php endif; ?>><?php echo ($carry_count); ?>提现申请</a>
			</td>
		</tr>
		
		<tr>
			<td class="item_title" style="width:200px;">
				待处理续约申请
			</td>
			<td class="item_input">
				<a href="<?php echo u("GenerationRepaySubmit/index");?>" <?php if($generation_repay_submit > 0): ?>style="color:#f60;"<?php endif; ?>><?php echo ($generation_repay_submit); ?>待处理续约申请</a>
			</td>
		</tr>
		<tr>
			<td class="item_title" style="width:200px;">
				待处理举报
			</td>
			<td class="item_input">
				<a href="<?php echo u("Reportguy/index",array("status"=>0));?>" <?php if($reportguy_count > 0): ?>style="color:#f60;"<?php endif; ?>><?php echo ($reportguy_count); ?>待处理举报</a>
			</td>
		</tr>
		<tr>
			<td class="item_title" style="width:200px;">
				订单统计
			</td>
			<td class="item_input">
				充值成交<?php echo ($incharge_order_buy_count); ?>
				
				<?php if($reminder['incharge_count'] > 0): ?>(<a href="<?php echo u("PaymentNotice/index");?>" style="color:#f60;"><?php echo ($reminder["incharge_count"]); ?>新充值单</a>)<?php endif; ?>
			</td>
		</tr>
		
		
		<tr>
			<td class="item_title" style="width:200px;">
				网站数据统计
			</td>
			<td class="item_input">
				<a href="<?php echo u("Index/statistics");?>">查看</a>
			</td>
		</tr>
		<tr>
			<td colspan=2 class="bottomTd"></td>
		</tr>
		<tr>
			<td class="item_title" style="width:200px;">
				<?php echo L("CURRENT_VERSION");?>
			</td>
			<td class="item_input">
				<?php echo L("APP_VERSION");?>:<?php echo conf("DB_VERSION");?>
			</td>
		</tr>
		
		<tr>
			<td class="item_title" style="width:200px;">
				<?php echo L("TIME_INFORMATION");?>
			</td>
			<td class="item_input">
				<?php echo L("CURRENT_TIME");?>：<?php echo to_date(get_gmtime()); ?>
			</td>
		</tr>
		<tr>
			<td class="item_title" style="width:200px;">
				<?php echo L("GET_MORE_INFO");?>
			</td>
			<td class="item_input">
				请访问 <a href="http://www.fanwe.com" target="_blank" title="方维贷款商业系统">http://p2p.fanwe.com</a>
			</td>
		</tr>
		<tr>
			<td colspan=2 class="bottomTd"></td>
		</tr>
	</table>	
	</div>
<script type="text/javascript">
	var nav_json_data = <?php echo json_encode($navs); ?>;
	loadModule();
	$("#J_nav").change(function(){
		loadModule();
	});
	$("#J_m").change(function(){
		loadAction();
	});
	function loadModule(){
		var nav =$("#J_nav").val();
		var html = "";
		$.each(nav_json_data,function(i,v){
			if(i==nav){
				$.each(v.groups,function(ii,vv){
					html += '<option value="'+ii+'">'+vv.name+'</option>';
				});
			}
		});
		
		$("#J_m").html(html);
		loadAction();
	}
	
	function loadAction(){
		var nav =$("#J_nav").val();
		var m =  $("#J_m").val();
		var a_html = '<option value="">请选择</option>';
		$.each(nav_json_data,function(i,v){
			if(i==nav){
				$.each(v.groups,function(ii,vv){
					if(ii==m){
						$.each(vv.nodes,function(iii,vvv){
							a_html += '<option value="'+vvv.action+'" module="'+vvv.module+'">'+vvv.name+'</option>';
						});
					}
				});
			}
		});
	
		$("#J_a").html(a_html);
	}
	
	$("#J_a").change(function(){
		if($.trim($(this).val())!=""){
			location.href = ROOT + '?m='+$(this).find("option:selected").attr("module")+'&a='+$(this).val();
		}
	})
</script>
</body>
</html>