$(document).ready(function(){
	$("select[name='input_type']").bind("change",function(){
		load_field_form();
	});
	load_field_form();
});

function load_field_form()
{
	input_type = $("select[name='input_type']").val();
	if(input_type == 0)
	{		
		$("#scope_input_row").hide();
		
	}
	else
	{
		$("#scope_input_row").show();
	}
}
