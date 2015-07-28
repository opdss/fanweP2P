	$(document).ready(function(){
		load_exchange_row();
		$("select[name='send_type']").bind("change",function(){load_exchange_row();});
	});
	function load_exchange_row()
	{
		var send_type = $("select[name='send_type']").val();
		if(send_type==1)
		{
			$("#exchange_row").show();
		}
		else
		{
			$("input[name='exchange_score']").val("");
			$("input[name='exchange_limit']").val("");			
			$("#exchange_row").hide();
		}
	}