jQuery(function(){
	get_deal_catetype_city();
	$("input[name='cate_id[]']").bind("click",function(){
		get_deal_catetype_city();
	});
});
function get_deal_catetype_city(){
	var cate_ids= "0";
	var id = 0;
		
	if($("input[name='cate_id[]']:checked").length > 0){
		$("input[name='cate_id[]']:checked").each(function(){
			cate_ids += ","+$(this).val();
		});
	}
	
	if($("input[name='id']").length > 0)
	{
		id = $("input[name='id']").val();
	}
	
	$.ajax({
		url: ROOT+"?"+VAR_MODULE+"="+MODULE_NAME+"&"+VAR_ACTION+"=load_city_link&cate_ids="+cate_ids+"&id="+id, 
		dataType: "text",
		success:function(result){
			$("#deal_city_link .item_input").html(result);
		}
	});
}
