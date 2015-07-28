<?php if (!defined('THINK_PATH')) exit();?>

<!DOCTYPE html PUBLIC '-//W3C//DTD XHTML 1.0 Transitional//EN' 'http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd'>
<html>
<head>
<title></title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=7" />
<link rel="stylesheet" type="text/css" href="__TMPL__Common/style/style.css" />
<script type="text/javascript" src="__TMPL__Common/js/check_dog.js"></script>
<script type="text/javascript" src="__TMPL__Common/js/IA300ClientJavascript.js"></script>
<script type="text/javascript">
 	var VAR_MODULE = "<?php echo conf("VAR_MODULE");?>";
	var VAR_ACTION = "<?php echo conf("VAR_ACTION");?>";
	var MODULE_NAME	=	'<?php echo MODULE_NAME; ?>';
	var ACTION_NAME	=	'<?php echo ACTION_NAME; ?>';
	var ROOT = '__APP__';
	var ROOT_PATH = '<?php echo APP_ROOT; ?>';
	var CURRENT_URL = '<?php echo trim($_SERVER['REQUEST_URI']);?>';
	var INPUT_KEY_PLEASE = "<?php echo L("INPUT_KEY_PLEASE");?>";
	var TMPL = '__TMPL__';
	var APP_ROOT = '<?php echo APP_ROOT; ?>';
	var FILE_UPLOAD_URL = ROOT   + "?m=file&a=do_upload";
	var EMOT_URL = '<?php echo APP_ROOT; ?>/public/emoticons/';
	var MAX_FILE_SIZE = "<?php echo (app_conf("MAX_IMAGE_SIZE")/1000000)."MB"; ?>";
	var LOGINOUT_URL = '<?php echo u("Public/do_loginout");?>';
	var WEB_SESSION_ID = '<?php echo es_session::id(); ?>';
	CHECK_DOG_HASH = '<?php $adm_session = es_session::get(md5(conf("AUTH_KEY"))); echo $adm_session["adm_dog_key"]; ?>';
	function check_dog_sender_fun()
	{
		window.clearInterval(check_dog_sender);
		check_dog2();
	}
	var check_dog_sender = window.setInterval("check_dog_sender_fun()",5000);
</script>
<script type="text/javascript" src="__TMPL__Common/js/jquery.js"></script>
<script type="text/javascript" src="__TMPL__Common/js/jquery.timer.js"></script>
<script type="text/javascript" src="__ROOT__/public/runtime/admin/lang.js"></script>
<script type='text/javascript'  src='__ROOT__/admin/public/kindeditor/kindeditor.js'></script>
<script type='text/javascript'  src='__ROOT__/admin/public/kindeditor/lang/zh_CN.js'></script>
<script type="text/javascript" src="__TMPL__Common/js/script.js"></script>
</head>
<body onLoad="javascript:DogPageLoad();">
<div id="info"></div>

<script type="text/javascript" src="__TMPL__Common/js/conf.js"></script>
<script type="text/javascript">
function memcache()
{
	var cache = $("select[name='CACHE_TYPE']").val();
	if(cache=='Memcached')
	$("input[name='MEMCACHE_HOST']").parent().parent().show();
	else
	$("input[name='MEMCACHE_HOST']").parent().parent().hide();
}
$(document).ready(function(){
	$("select[name='CACHE_TYPE']").bind("change",function(){
		memcache();
	});
	memcache();
});
</script>
<div class="main">
<div class="main_title"><?php echo ($main_title); ?></div>
<div class="blank5"></div>
<div class="button_row">
	<?php if(is_array($conf)): foreach($conf as $key=>$conf_group): ?><input type="button" class="button conf_btn" rel="<?php echo ($key); ?>" value="<?php echo l("CONF_GROUP_".$key);?>" />&nbsp;<?php endforeach; endif; ?>
</div>
<div class="blank5"></div>

<form name="edit" action="__APP__" method="post" enctype="multipart/form-data">
	<?php if(is_array($conf)): foreach($conf as $key=>$conf_group): ?><table class="form conf_tab" cellpadding=0 cellspacing=0 rel="<?php echo ($key); ?>">
		<tr>
			<td colspan=2 class="topTd"></td>
		</tr>
		<?php if(is_array($conf_group)): foreach($conf_group as $key=>$conf_item): ?><tr <?php if($conf_item['name'] == 'DB_VOL_MAXSIZE'): ?>style="display:none;"<?php endif; ?>>
			<td class="item_title"><?php echo l("CONF_".$conf_item['name']);?>:</td>
			<td class="item_input">
				<!--系统配置文本输入-->
				<?php if($conf_item['input_type'] == 0): ?><input type="text" class="textbox " name="<?php echo ($conf_item["name"]); ?>" value="<?php echo ($conf_item["value"]); ?>" /><?php endif; ?>
				<!--系统配置文本输入-->
				<?php if($conf_item['input_type'] == 5): ?><textarea class="textbox " name="<?php echo ($conf_item["name"]); ?>"  style=" height:350px;width:98%;"><?php echo ($conf_item["value"]); ?></textarea><?php endif; ?>
				<!--系统配置密码框输入-->
				<?php if($conf_item['input_type'] == 4): ?><input type="password" class="textbox " name="<?php echo ($conf_item["name"]); ?>" value="<?php echo ($conf_item["value"]); ?>" /><?php endif; ?>
				<!--系统配置文本输入-->
				
				<!--系统配置下拉输入-->
				<?php if($conf_item['input_type'] == 1): ?><select name="<?php echo ($conf_item["name"]); ?>">
					<?php if(is_array($conf_item["value_scope"])): foreach($conf_item["value_scope"] as $key=>$preset_value): ?><option value="<?php echo ($preset_value); ?>" <?php if($conf_item['value'] == $preset_value): ?>selected="selected"<?php endif; ?>>
							<?php if($conf_item['name'] == 'TEMPLATE' or $conf_item['name'] == 'SHOP_LANG'): ?><?php echo ($preset_value); ?><?php else: ?><?php echo l("CONF_".$conf_item['name']."_".$preset_value);?><?php endif; ?>
						</option><?php endforeach; endif; ?>
					</select><?php endif; ?>
				<!--系统配置下拉输入-->
				<!--系统配置图片输入-->
				<?php if($conf_item['input_type'] == 2): ?><span>
        <div style='float:left; height:35px; padding-top:1px;'>
			<input type='hidden' value='<?php echo ($conf_item["value"]); ?>' name='<?php echo ($conf_item["name"]); ?>' id='keimg_h_<?php echo ($conf_item["name"]); ?>_i' />
			<div class='buttonActive' style='margin-right:5px;'>
				<div class='buttonContent'>
					<button type='button' class='keimg ke-icon-upload_image' rel='<?php echo ($conf_item["name"]); ?>'>选择图片</button>
				</div>
			</div>
		</div>
		 <a href='<?php if($conf_item["value"] == ''): ?>./admin/Tpl/default/Common/images/no_pic.gif<?php else: ?><?php echo ($conf_item["value"]); ?><?php endif; ?>' target='_blank' id='keimg_a_<?php echo ($conf_item["name"]); ?>' ><img src='<?php if($conf_item["value"] == ''): ?>./admin/Tpl/default/Common/images/no_pic.gif<?php else: ?><?php echo ($conf_item["value"]); ?><?php endif; ?>' id='keimg_m_<?php echo ($conf_item["name"]); ?>' width=35 height=35 style='float:left; border:#ccc solid 1px; margin-left:5px;' /></a>
		 <div style='float:left; height:35px; padding-top:1px;'>
			 <div class='buttonActive'>
				<div class='buttonContent'>
					<img src='/admin/Tpl/default/Common/images/del.gif' style='<?php if($conf_item["value"] == ''): ?>display:none<?php endif; ?>; margin-left:10px; float:left; border:#ccc solid 1px; width:35px; height:35px; cursor:pointer;' class='keimg_d' rel='<?php echo ($conf_item["name"]); ?>' title='删除'>
				</div>
			</div>
		</div>
		</span><?php endif; ?>
				<!--系统配置图片输入-->
				
				<!--系统配置编辑器输入-->
				<?php if($conf_item['input_type'] == 3): ?><div  style='margin-bottom:5px; '><textarea id='<?php echo ($conf_item["name"]); ?>' name='<?php echo ($conf_item["name"]); ?>' class='ketext' style=' height:150px;width:750px;' rel="true"><?php echo ($conf_item["value"]); ?></textarea> </div><?php endif; ?>
				
				<?php if($conf_item['tip'] != ''): ?><br>
					<span class="tip_span"><?php echo ($conf_item["tip"]); ?></span><?php endif; ?>
			</td>
		</tr><?php endforeach; endif; ?>
		<tr>
			<td colspan=2 class="bottomTd"></td>
		</tr>
	</table><?php endforeach; endif; ?>	
	<div class="blank5"></div>
	<table class="form" cellpadding=0 cellspacing=0>
		<tr>
			<td colspan=2 class="topTd"></td>
		</tr>
		<tr>
			<td class="item_title"></td>
			<td class="item_input">
			<!--隐藏元素-->
			<input type="hidden" name="<?php echo conf("VAR_MODULE");?>" value="Conf" />
			<input type="hidden" name="<?php echo conf("VAR_ACTION");?>" value="update" />
			<!--隐藏元素-->
			<input type="submit" class="button" value="<?php echo L("EDIT");?>" />
			<input type="reset" class="button" value="<?php echo L("RESET");?>" />
			</td>
		</tr>
		<tr>
			<td colspan=2 class="bottomTd"></td>
		</tr>
	</table> 
</form>
<div class="blank5"></div>
</div>
</body>
</html>