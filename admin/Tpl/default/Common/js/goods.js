function init_dealform()
{
	//绑定副标题20个字数的限制
	$("input[name='sub_name']").bind("keyup change",function(){
		if($(this).val().length>20)
		{
			$(this).val($(this).val().substr(0,20));
		}		
	});
	
	//绑定团购商品类型，显示属性
	$("select[name='goods_type_id']").bind("change",function(){
		load_attr_html();
	});
}


function load_attr_html()
{
		deal_goods_type = $("select[name='goods_type_id']").val();
		deal_id = $("input[name='id']").val();
		if(deal_goods_type>0)
		{
			$("#deal_attr_row").show();
			$.ajax({ 
				url: ROOT+"?"+VAR_MODULE+"="+MODULE_NAME+"&"+VAR_ACTION+"=attr_html&goods_type_id="+deal_goods_type+"&goods_id="+deal_id, 
				data: "ajax=1",
				success: function(obj){
					$("#deal_attr").html(obj);
					//set_params();
								
				}
			});
		}
		else
		{
			$("#deal_attr_row").hide();
			$("#deal_attr").html("");
		}
}


//加载属性库存表
function load_attr_stock(obj)
{
	if(obj)
	{
		 attr_cfg_json = '';
		 attr_stock_json = '';
	}

	if($(".goods_attr_stock:checked").length>0)
	{
			$(".max_bought_row").find("input[name='max_bought']").val("");
			$(".max_bought_row").hide();
	}
	else
	{
			$(".max_bought_row").show();
	}
	//初始化deal_attr_stock_hd
	var goods_attr_stock_box = $(".goods_attr_stock");
	for(i=0;i<goods_attr_stock_box.length;i++)
	{
		var v = $(goods_attr_stock_box[i]).attr("checked")?1:0;
		$(goods_attr_stock_box[i]).parent().find(".goods_attr_stock_hd").val(v);
	}
	var box = $(".goods_attr_stock:checked");
	if(!box.length>0)
	{
		$("#stock_table").html("");
		return;
	}
	
	var x = 1; //行数
	var y = 0; //列数
	var attr_id = 0;
	var attr_item_count = 0; //每组属性的个数
	var attr_arr = new Array();
	for(i=0;i<box.length;i++)
	{
		if($(box[i]).attr("rel")!=attr_id)
		{
			y++;
			attr_id = $(box[i]).attr("rel");
			attr_arr.push(attr_id);
		}
		else
		{
			attr_item_count++;
		}
	}

	//开始计算行数
	for(i=0;i<attr_arr.length;i++)
	{
		x = x * parseInt($("input[name='goods_attr_stock["+attr_arr[i]+"][]']:checked").length);
	}	
	var html = "<table width='100%' style='border-left: solid #ccc 1px; border-top: solid #ccc 1px;'>";	
	html += "<tr>";
	for(j=0;j<attr_arr.length;j++)
	{
		html+="<th>"+$("#title_"+attr_arr[j]).html()+"</th>";
	}
	html+="<th>"+LANG['DEAL_MAX_BOUGHT_TIP']+"</th>";
	html +="</tr>";
	
	for(i=0;i<x;i++)
	{
		html += "<tr>";
		for(j=0;j<attr_arr.length;j++)
		{
			html+="<td><select name='stock_attr["+attr_arr[j]+"][]' class='attr_select_box' onchange='check_same(this);'><option value=''>"+LANG['EMPTY_SELECT']+"</option>";
			
			//开始获取相应的选取值
			var cbo = $("input[name='goods_attr_stock["+attr_arr[j]+"][]']:checked");
			for(k=0;k<cbo.length;k++)
			{
				var cnt = $(cbo[k]).parent().find("*[name='goods_attr["+attr_arr[j]+"][]']").val();				
				html =  html + "<option value='"+cnt+"'";
				if(attr_cfg_json!=''&&attr_cfg_json[i][attr_arr[j]]==cnt)
				html = html + " selected='selected' ";
				html = html + ">"+cnt+"</option>";
			}
			
			html+="</select></td>";
		}
		html+="<td><input type='text' class='textbox' style='width: 50px;' name='stock_cfg_num[]' value='";
		if(attr_stock_json!='')
		html = html + attr_stock_json[i]['stock_cfg'];		
		html=html+"' /> <input type='hidden' name='stock_cfg[]' value='";
		if(attr_stock_json!='')
		html+=attr_stock_json[i]['attr_str'];
		html+="' /> </td>";
		html +="</tr>";
	}	
	html += "</table>";
	$("#stock_table").html(html);
}


//检测当前行的配置
function check_same(obj)
{
	var selectbox = $(obj).parent().parent().find("select");
	var row_value = '';
	for(i=0;i<selectbox.length;i++)
	{
		if($(selectbox[i]).val()!='')
			row_value += $(selectbox[i]).val();
		else
		{
			$(obj).parent().parent().find("input[name='stock_cfg[]']").val("");
			return;
		}
	}
	//开始检测是否存在该配置
	var stock_cfg = $("input[name='stock_cfg[]']");
	for(i=0;i<stock_cfg.length;i++)
	{
		if(row_value==$(stock_cfg[i]).val()&&row_value!=''&&stock_cfg[i]!=obj)
		{
			alert(LANG['SPEC_EXIST']);
			$(obj).parent().parent().find("input[name='stock_cfg[]']").val("");
			$(obj).val("");
			return;
		}
	}
	$(obj).parent().parent().find("input[name='stock_cfg[]']").val(row_value);
}