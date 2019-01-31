//global functions here
function showMessage(id, message, type){
	$('#'+id+'_message').html(message);
	if(type == 'error')
		$('#'+id).removeClass('alert-success').addClass('alert-danger');
	else
		$('#'+id).removeClass('alert-danger').addClass('alert-success');
	$('#'+id).removeClass('hide').show();
	setTimeout('hideMessage("'+id+'")',5000);
}
function hideMessage(id){
	$('#'+id).fadeOut(3000);
	//$('#'+id+'_message').html('');
}
function parseJSON(json){
	try{
		return jQuery.parseJSON(json);
	}
	catch(e){
		if(debug)
			showMessage('errors','<b>JSON Error:</b><ul>'+e.message.replace('<br />','')+
				'</ul>Actual JSON:<ul>'+json.replace('<br />','')+'</ul>', 'error');
		else
			showMessage('errors','There was an error processing this request', 'error');
		return false;
	}
}