

//设置队列的检测定时器

function promote_sender_fun()
{
	window.clearInterval(promote_sender);
	$.ajax({
		url: "msg_send.php?act=promote_msg_list",
		success:function(data)
		{
			if(!isNaN(data)&&parseInt(data)>=1)
			{						
				$("#promote_msg").show();			
			}
			else
			{
				$("#promote_msg").hide();
			}
			promote_sender = window.setInterval("promote_sender_fun()",send_span);
		}
	});
}

function apns_sender_fun()
{
	window.clearInterval(apns_sender);
	$.ajax({
		url: "msg_send.php?act=apns_list",
		success:function(data)
		{
			if(!isNaN(data)&&parseInt(data)>=1)
			{						
				$("#apns_msg").show();			
			}
			else
			{
				$("#apns_msg").hide();
			}
			apns_sender = window.setInterval("apns_sender_fun()",send_span);
		}
	});
}

function deal_sender_fun()
{
	window.clearInterval(deal_sender);
	$.ajax({
		url: "msg_send.php?act=deal_msg_list",
		success:function(data)
		{
			if(!isNaN(data)&&parseInt(data)>=1)
			{						
				$("#deal_msg").show();			
			}
			else
			{
				$("#deal_msg").hide();
			}
			deal_sender = window.setInterval("deal_sender_fun()",send_span);
		}
	});
}

var promote_sender = window.setInterval("promote_sender_fun()",send_span);
var apns_sender = window.setInterval("apns_sender_fun()",send_span);
var deal_sender = window.setInterval("deal_sender_fun()",send_span);	