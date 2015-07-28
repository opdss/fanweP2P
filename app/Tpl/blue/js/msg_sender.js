var deal_sender;
function deal_sender_fun()
{
	window.clearInterval(deal_sender);
	if (to_send_msg == true) {
		$.ajax({
			url: APP_ROOT + "/msg_send.php?act=deal_msg_list",
			dataType:"json",
			success: function(data){
				
				if(data.DEAL_MSG_COUNT ==0)
				{
					to_send_msg = false;
				}
				else{
					to_send_msg = true;
				}
			}
		});
	}
	
	deal_sender = window.setInterval("deal_sender_fun()", send_span);
}

$(document).ready(function(){
	
	//关于队列群发检测
	deal_sender = window.setInterval("deal_sender_fun()",send_span);
});