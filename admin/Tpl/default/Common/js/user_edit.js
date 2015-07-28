function toogle_merchant_status()
{
	var is_merchant = $("select[name='is_merchant']").val();
	if(is_merchant==1)
	{
		$("#merchant_name").show();
	}
	else
	{
		$("#merchant_name").find("input[name='merchant_name']").val("");
		$("#merchant_name").hide();
	}
}
function toogle_daren_status()
{
	var is_daren = $("select[name='is_daren']").val();
	if(is_daren==1)
	{
		$("#daren_title").show();
		$("#daren_cate").show();
	}
	else
	{
		$("#daren_title").find("input[name='daren_title']").val("");
		$("#daren_cate").find("input[name='cate_id[]']").attr("checked",false);
		$("#daren_cate").hide();
		$("#daren_title").hide();
	}
}
function check_merchant_name()
{
	var merchant_name = $("input[name='merchant_name']").val();
	if(merchant_name!='')
	$.ajax({ 
		url: ROOT+"?"+VAR_MODULE+"="+MODULE_NAME+"&"+VAR_ACTION+"=check_merchant_name", 
		data: "merchant_name="+merchant_name+"&ajax=1",
		type:"post",
		dataType: "json",
		success: function(obj){
			if(obj.status==0)
			{
				alert(obj.info);
				$("input[name='merchant_name']").val("");
			}
		}
	});
}

$(document).ready(function(){
	$("select[name='is_merchant']").bind("change",function(){
		toogle_merchant_status();
	});
	toogle_merchant_status();
	$("select[name='is_daren']").bind("change",function(){
		toogle_daren_status();
	});
	toogle_daren_status();
	$("input[name='merchant_name']").bind("blur",function(){
		check_merchant_name();
	});
	$("select[name='province_id']").bind("change",function(){
		load_city($("select[name='province_id']"),$("select[name='city_id']"));
	});
	$("select[name='n_province_id']").bind("change",function(){
		load_city($("select[name='n_province_id']"),$("select[name='n_city_id']"));
	});
});

function load_city(pname,cname)
{
	var id = pname.val();
	var evalStr="regionConf.r"+id+".c";

	if(id==0)
	{
		var html = "<option value='0'>="+LANG['PLEASE_SELECT']+"=</option>";
	}
	else
	{
		var regionConfs=eval(evalStr);
		evalStr+=".";
		var html = "<option value='0'>="+LANG['PLEASE_SELECT']+"=</option>";
		for(var key in regionConfs)
		{
			html+="<option value='"+eval(evalStr+key+".i")+"'>"+eval(evalStr+key+".n")+"</option>";
		}
	}
	cname.html(html);
}