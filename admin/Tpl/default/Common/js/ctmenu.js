function search_youhui_supplier()
	{
		var key = $("input[name='supplier_key']").val();
		if($.trim(key)=='')
		{
			alert(INPUT_KEY_PLEASE);
		}
		else
		{
			$.ajax({ 
				url: ROOT+"?"+VAR_MODULE+"=SupplierLocationMenu&"+VAR_ACTION+"=load_supplier_location&sid="+supplier_location_id, 
				data: "ajax=1&key="+key,
				type: "POST",
				success: function(obj){
					$("#supplier_list").html(obj);
				}
			});
		}
	}
function init_sub_cate()
{
	var cate_id = $("select[name='cate_id']").val();
	var menu_id = $("input[name='id']").val();
	
	if(cate_id>0)
	{		
		$.ajax({ 
			url: ROOT+"?"+VAR_MODULE+"="+MODULE_NAME+"&"+VAR_ACTION+"=load_sub_cate&cate_id="+cate_id+"&menu_id="+menu_id, 
			data: "ajax=1",
			dataType: "json",
			success: function(obj){
				if(obj.status)
				{
					$("#sub_cate_box").show();
					$("#sub_cate_box").find(".item_input").html(obj.data);
				}
				else
				{
					$("#sub_cate_box").hide();
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
		$("#sub_cate_box").hide();
		$("#sub_cate_box").find(".item_input").html("");
	}
}