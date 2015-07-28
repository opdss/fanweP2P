function del_photo(photo_id,obj)
{
	if(confirm("确定要删除图片吗"))
	{
		$.ajax({
			url: ROOT + '?' + VAR_MODULE + '=' + CURR_MODULE + '&' + VAR_ACTION + '=removePhoto&photo_id='+photo_id,
			type:"POST",
			cache: false,
			dataType:"json",
			success: function(result){
				if(result.isErr==0)
				{					
					if($(obj).parent().parent().find(".img_list").length==1)
					{
						$(obj).parent().parent().parent().remove();
						$(obj).parent().remove();
					}
					else
					$(obj).parent().remove();
				}
				else
				$.ajaxError(result.content);
			}
		});
	}
}

function getMerchantByName(selid,keyid)
{
    var keywords = $(keyid).val();
    var jselect = $(selid);
    var oselect = jselect.get(0);
    var option;

	option = document.createElement('option');
    option.value = '';
    option.text = '搜索中，请稍候...';
    jselect.empty();
    oselect.options.add(option);

    var query = new Object();
    query.name = keywords;

	$.ajax({
      url: ROOT+'?'+VAR_MODULE+'=SupplierLocationImages&'+VAR_ACTION+'=getMerchantByName',
      cache: false,
      data:query,
      dataType:"json",
      success:function(result)
      {
        jselect.empty();
        if(result.length > 0)
        {
            var c = result.length;
            option = document.createElement('option');
            option.value = '';
            option.text = '搜索到 '+ c +' 个商户，请选择';
            oselect.options.add(option);

            var i = 0;
            for(i;i < c;i++)
            {
                option = document.createElement('option');
                option.value = result[i].id;
                option.text = result[i].name;
                oselect.options.add(option);
            }
			GetImagesGroup();
        }
        else
        {
            option = document.createElement('option');
            option.value = '';
            option.text = '未搜索到相关的商户';
            oselect.options.add(option);
        }
      }
    });
}

function GetImagesGroup(){
	$("#supplier_location_id").bind("change",function(){
		var query =new Object();
		query.supplier_location_id = $(this).val();
		query.id = 0;
		if($("input[name='id']").length >0){
			query.id = $("input[name='id']").val();
		}
		$.ajax({
		      url: ROOT+'?'+VAR_MODULE+'=SupplierLocationImages&'+VAR_ACTION+'=get_images_group',
		      cache: false,
		      data:query,
		      dataType:"text",
		      success:function(result)
		      {
			  	$("#images_group_id_area .item_input").html(result);
			  }
		});
	});
}
