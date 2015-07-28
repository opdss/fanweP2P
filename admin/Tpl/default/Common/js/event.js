function add_submit_row(event_id)
{
	$.ajax({ 
		url: ROOT+"?"+VAR_MODULE+"="+MODULE_NAME+"&"+VAR_ACTION+"=add_submit_item&event_id="+event_id, 
		data: "ajax=1",
		success: function(obj){
			$("#submit_row").append(obj);
		}
	});	
}
function remove_row(obj,event_id)
{
	if(event_id>0)
	{
		if(confirm("删除该配置有可能影响已报名的数据，确定删除吗？"))
		{
			$(obj).parent().remove();
		}
	}
	else
	$(obj).parent().remove();
}
function change_type(obj)
{
	if($(obj).val()>0)
	{
		$(obj).parent().find("span").show();
	}
	else
	{
		$(obj).parent().find("span").hide();
	}
}

function set_area()
{
	var city_id =$("select[name='city_id']").val();
	var event_id = $("input[name='id']").val();
	$.ajax({ 
		url: ROOT+"?"+VAR_MODULE+"="+MODULE_NAME+"&"+VAR_ACTION+"=area_list&city_id="+city_id+"&id="+event_id, 
		data: "ajax=1",
		success: function(obj){
			$("#area_list").html(obj);
		}
	});	
}


function init_supplier_location()
{
	var supplier_id = $("select[name='supplier_id']").val();
	var event_id = $("input[name='id']").val();	
	if(supplier_id>0)
	{		
		$.ajax({ 
			url: ROOT+"?"+VAR_MODULE+"="+MODULE_NAME+"&"+VAR_ACTION+"=load_supplier_location&supplier_id="+supplier_id+"&event_id="+event_id, 
			data: "ajax=1",
			dataType: "json",
			success: function(obj){
				if(obj.status)
				{
					$("#supplier_location").show();
					$("#supplier_location").find(".item_input").html(obj.data);
				}
				else
				{
					$("#supplier_location").hide();
				}
				
			},
			error:function(ajaxobj)
			{
				if(ajaxobj.responseText!='')
				alert(ajaxobj.responseText);
			}
		
		});
	}
	else
	{
		$("#supplier_location").hide();
		$("#supplier_location").find(".item_input").html("");
	}
}


function search_event_supplier()
{
	var key = $("input[name='supplier_key']").val();
	if($.trim(key)=='')
	{
		alert(INPUT_KEY_PLEASE);
	}
	else
	{
		$.ajax({ 
			url: ROOT+"?"+VAR_MODULE+"=SupplierLocation&"+VAR_ACTION+"=search_supplier", 
			data: "ajax=1&key="+key,
			type: "POST",
			success: function(obj){
				$("#supplier_list").html(obj);
				$("select[name='supplier_id']").bind("change",function(){
					init_supplier_location();
				});
			}
		});
	}
}

$(document).ready(function(){
	$("select[name='city_id']").bind("change",function(){
		set_area();
	});
	set_area();
	$("input[name='supplier_key_btn']").bind("click",function(){
		search_event_supplier();
		
	});
	$("select[name='supplier_id']").bind("change",function(){
		init_supplier_location();
	});
	init_supplier_location();
});