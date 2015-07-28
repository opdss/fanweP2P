$(function(){
	if($("#divFloatToolsView").outerHeight() < 250)
		$("#floatTools").height(250);
	else
		$("#floatTools").height($("#divFloatToolsView").outerHeight());
	$("#aFloatTools_Show").click(function(){
		$('#divFloatToolsView').animate({width:'show',opacity:'show'},100,function(){$('#divFloatToolsView').show();});
		$('#aFloatTools_Show').hide();
		$('#aFloatTools_Hide').show();				
	});
	$("#aFloatTools_Hide").click(function(){
		$('#divFloatToolsView').animate({width:'hide', opacity:'hide'},100,function(){$('#divFloatToolsView').hide();});
		$('#aFloatTools_Show').show();
		$('#aFloatTools_Hide').hide();	
	});
});