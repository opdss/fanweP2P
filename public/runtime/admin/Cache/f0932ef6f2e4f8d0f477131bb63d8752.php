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
<script type="text/javascript" src="__TMPL__Common/js/calendar/calendar.php?lang=zh-cn" ></script>
<script type="text/javascript">
	var attr_cfg_json = <?php echo ($attr_cfg_json); ?>;
	var attr_stock_json = <?php echo ($attr_stock_json); ?>;
</script>
<link rel="stylesheet" type="text/css" href="__TMPL__Common/js/calendar/calendar.css" />
<script type="text/javascript" src="__TMPL__Common/js/calendar/calendar.js"></script>
<script type="text/javascript" src="__TMPL__Common/js/goods.js"></script>
<script type="text/javascript">
	window.onload = function()
	{
		init_dealform();
	}
</script>
<div class="main">
<div class="main_title"><?php echo ($vo["name"]); ?><?php echo L("EDIT");?>  <a href="<?php echo u("Goods/index");?>" class="back_list"><?php echo L("BACK_LIST");?></a></div>
<div class="blank5"></div>
<form name="edit" action="__APP__" method="post" enctype="multipart/form-data">
<div class="button_row">
	<input type="button" class="button conf_btn" rel="1" value="<?php echo L("SHOP_BASE_INFO");?>" />&nbsp;
	<input type="button" class="button conf_btn" rel="2" value="<?php echo L("ATTR_SETTING");?>" />&nbsp;	
	<input type="button" class="button conf_btn" rel="3" value="<?php echo L("SEO_CONFIG");?>" />&nbsp;	
</div>
<div class="blank5"></div>
<table class="form conf_tab" cellpadding=0 cellspacing=0 rel="1">
	<tr>
		<td colspan=2 class="topTd"></td>
	</tr>
	<tr>
		<td class="item_title"><?php echo L("GOODS_NAME");?>:</td>
		<td class="item_input"><input type="text" class="textbox require" name="name" style="width:500px;" value="<?php echo ($vo["name"]); ?>" /></td>
	</tr>
	<tr>
		<td class="item_title"><?php echo L("DEAL_SUB_NAME");?>:</td>
		<td class="item_input"><input type="text" class="textbox require" name="sub_name"  value="<?php echo ($vo["sub_name"]); ?>"/> <span class='tip_span'><?php echo L("GOODS_SUB_NAME_TIP");?></span></td>
	</tr>
	
	<tr>
		<td class="item_title"><?php echo L("GOODS_BRIEF");?>:</td>
		<td class="item_input"><textarea class="textarea" name="brief" ><?php echo ($vo["brief"]); ?></textarea></td>
	</tr>
	<tr>
		<td class="item_title"><?php echo L("CATE_TREE");?>:</td>
		<td class="item_input">		
		<!-- 分类   -->
		<select name="cate_id" class="require">
			<option value="0" <?php if($vo['cate_id'] == 0): ?>selected="selected"<?php endif; ?>>==<?php echo L("NO_SELECT_CATE");?>==</option>
			<?php if(is_array($cate)): foreach($cate as $key=>$cate_item): ?><option value="<?php echo ($cate_item["id"]); ?>" <?php if($vo['cate_id'] == $cate_item['id']): ?>selected="selected"<?php endif; ?>><?php echo ($cate_item["name"]); ?></option><?php endforeach; endif; ?>
		</select>
		</td>
	</tr>	
		
	<tr>
		<td class="item_title"><?php echo L("GOODS_ICON");?>:</td>
		<td class="item_input">
			<span>
        <div style='float:left; height:35px; padding-top:1px;'>
			<input type='hidden' value='<?php echo ($vo["img"]); ?>' name='img' id='keimg_h_img_i' />
			<div class='buttonActive' style='margin-right:5px;'>
				<div class='buttonContent'>
					<button type='button' class='keimg ke-icon-upload_image' rel='img'>选择图片</button>
				</div>
			</div>
		</div>
		 <a href='<?php if($vo["img"] == ''): ?>./admin/Tpl/default/Common/images/no_pic.gif<?php else: ?><?php echo ($vo["img"]); ?><?php endif; ?>' target='_blank' id='keimg_a_img' ><img src='<?php if($vo["img"] == ''): ?>./admin/Tpl/default/Common/images/no_pic.gif<?php else: ?><?php echo ($vo["img"]); ?><?php endif; ?>' id='keimg_m_img' width=35 height=35 style='float:left; border:#ccc solid 1px; margin-left:5px;' /></a>
		 <div style='float:left; height:35px; padding-top:1px;'>
			 <div class='buttonActive'>
				<div class='buttonContent'>
					<img src='/admin/Tpl/default/Common/images/del.gif' style='<?php if($vo["img"] == ''): ?>display:none<?php endif; ?>; margin-left:10px; float:left; border:#ccc solid 1px; width:35px; height:35px; cursor:pointer;' class='keimg_d' rel='img' title='删除'>
				</div>
			</div>
		</div>
		</span>
		</td>
	</tr>
	
	<tr>
		<td class="item_title">购买所需积分:</td>
		<td class="item_input"><input type="text" class="textbox require" name="score" value="<?php echo ($vo["user_max_bought"]); ?>" /></td>
	</tr>
	<tr>
		<td class="item_title">虚拟购买数:</td>
		<td class="item_input"><input type="text" class="textbox" name="invented_number" value="<?php echo ($vo["invented_number"]); ?>" /></td>
	</tr>
	<tr class="max_bought">
		<td class="item_title"><?php echo L("STOCK_NUM");?>:</td>
		<td class="item_input">
			<input type="text" class="textbox require" name="max_bought" value="<?php echo ($vo["max_bought"]); ?>" />
		</td>
	</tr>
	<tr>
		<td class="item_title"><?php echo L("DEAL_USER_MAX_BOUGHT");?>:</td>
		<td class="item_input"><input type="text" class="textbox require" name="user_max_bought" value="<?php echo ($vo["user_max_bought"]); ?>" /></td>
	</tr>
	<tr>
		<td class="item_title"><?php echo L("SORT");?>:</td>
		<td class="item_input"><input type="text" class="textbox require" name="sort" value="<?php echo ($vo["sort"]); ?>" /></td>
	</tr>
	<!-- 
	<tr>
		<td class="item_title"><?php echo L("IS_EFFECT");?>:</td>
		<td class="item_input">
			<lable><?php echo L("IS_EFFECT_1");?><input type="radio" name="is_effect" value="1" <?php if($vo['is_effect'] == 1): ?>checked="checked"<?php endif; ?> /></lable>
			<lable><?php echo L("IS_EFFECT_0");?><input type="radio" name="is_effect" value="0" <?php if($vo['is_effect'] == 0): ?>checked="checked"<?php endif; ?> /></lable>
		</td>
	</tr> -->
	<tr>
		<td class="item_title"><?php echo L("GOODS_DESCRIPTION");?>:</td>
		<td class="item_input">
			 <div  style='margin-bottom:5px; '><textarea id='description' name='description' class='ketext' style='width:750px; height:350px;' rel="true"><?php echo ($vo["description"]); ?></textarea> </div>
		</td>
	</tr>
	<tr>
		<td colspan=2 class="bottomTd"></td>
	</tr>
</table>



<table class="form conf_tab" cellpadding=0 cellspacing=0 rel="2">
	<tr>
		<td colspan=2 class="topTd"></td>
	</tr>
	
	<tr class="buy_type_0">
		<td class="item_title"><?php echo L("IS_HOT");?>:</td>
		<td class="item_input">
			<select name="is_hot">
				<option value="0" <?php if($vo['is_hot'] == 0): ?>selected="selected"<?php endif; ?>><?php echo L("IS_HOT_0");?></option>
				<option value="1" <?php if($vo['is_hot'] == 1): ?>selected="selected"<?php endif; ?>><?php echo L("IS_HOT_1");?></option>
			</select>
		</td>
	</tr>
	
	<tr class="buy_type_0">
		<td class="item_title">是否新品:</td>
		<td class="item_input">
			<select name="is_new">
				<option value="0" <?php if($vo['is_new'] == 0): ?>selected="selected"<?php endif; ?>><?php echo L("IS_BEST_0");?></option>
				<option value="1" <?php if($vo['is_new'] == 1): ?>selected="selected"<?php endif; ?>><?php echo L("IS_BEST_1");?></option>
			</select>
		</td>
	</tr>
	<tr class="buy_type_0">
		<td class="item_title">是否推荐:</td>
		<td class="item_input">
			<select name="is_recommend">
				<option value="0" <?php if($vo['is_recommend'] == 0): ?>selected="selected"<?php endif; ?>>否</option>
				<option value="1" <?php if($vo['is_recommend'] == 1): ?>selected="selected"<?php endif; ?>>是</option>
			</select>
		</td>
	</tr>
	<tr>
		<td class="item_title"><?php echo L("DEAL_IS_DELIVERY");?>:</td>
		<td class="item_input">
			<select name="is_delivery">
				<option value="0" <?php if($vo['is_delivery'] == 0): ?>selected="selected"<?php endif; ?>><?php echo L("IS_DELIVERY_0");?></option>
				<option value="1" <?php if($vo['is_delivery'] == 1): ?>selected="selected"<?php endif; ?>><?php echo L("IS_DELIVERY_1");?></option>
			</select>
			<span class='tip_span'>[<?php echo L("DEAL_IS_DELIVERY_TIP");?>]</span>
		</td>
	</tr>
	
	<tr id="filter_row"  class="buy_type_0">
		<td class="item_title"><?php echo L("FILTER_GROUP");?>:</td>
		<td class="item_input">
			<div id="filter"></div>
		</td>
	</tr>
	
	<tr class="buy_type_0">
		<td class="item_title"><?php echo L("GOODS_TYPE");?>:</td>
		<td class="item_input">
			<select name="goods_type_id">
			<option value="0" <?php if($vo['goods_type_id'] == 0): ?>selected="selected"<?php endif; ?>>==<?php echo L("NO_SELECT_GOODS_TYPE");?>==</option>
			<?php if(is_array($goods_type_list)): foreach($goods_type_list as $key=>$goods_type_item): ?><option value="<?php echo ($goods_type_item["id"]); ?>" <?php if($vo['goods_type_id'] == $goods_type_item['id']): ?>selected="selected"<?php endif; ?>><?php echo ($goods_type_item["name"]); ?></option><?php endforeach; endif; ?>
			</select>
		</td>
	</tr>
	<tr id="deal_attr_row">
		<td class="item_title"><?php echo L("GOODS_ATTR");?>:</td>
		<td class="item_input">
			<div id="deal_attr"></div>
		</td>
	</tr>
	<tr>
		<td colspan=2 class="bottomTd"></td>
	</tr>
</table>


<table class="form conf_tab" cellpadding=0 cellspacing=0 rel="3">
	<tr>
		<td colspan=2 class="topTd"></td>
	</tr>
	<tr>
		<td class="item_title"><?php echo L("GOODS_SEO_TITLE");?>:</td>
		<td class="item_input"><textarea class="textarea" name="seo_title" ><?php echo ($vo["seo_title"]); ?></textarea></td>
	</tr>
	<tr>
		<td class="item_title"><?php echo L("GOODS_SEO_KEYWORD");?>:</td>
		<td class="item_input"><textarea class="textarea" name="seo_keyword" ><?php echo ($vo["seo_keyword"]); ?></textarea></td>
	</tr>
	<tr>
		<td class="item_title"><?php echo L("GOODS_SEO_DESCRIPTION");?>:</td>
		<td class="item_input"><textarea class="textarea" name="seo_description" ><?php echo ($vo["seo_description"]); ?></textarea></td>
	</tr>
	<tr>
		<td colspan=2 class="bottomTd"></td>
	</tr>
</table>

<div class="blank5"></div>
	<table class="form" cellpadding=0 cellspacing=0>
		<tr>
			<td colspan=2 class="topTd"></td>
		</tr>
		<tr>
			<td class="item_title"></td>
			<td class="item_input">
			<!--隐藏元素-->
			<input type="hidden" name="id" value="<?php echo ($vo["id"]); ?>" />
			<input type="hidden" name="<?php echo conf("VAR_MODULE");?>" value="Goods" />
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
</div>
</body>
</html>


<script type="text/javascript">

load_attr_html();

/*
function set_params()
{
	$(".goods_attr_stock").each(
			function(){
				cosnole.log(this);
				load_attr_stock(item);
		});
}*/


</script>