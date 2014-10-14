/*
*@namla
*/
$(document).ready(function(){
	$('#icon_upload').uploadify({
    	'formData'     : {
    		'folder'    : '/images/partner/icon',
    		'fileext'	: '*.jpg'
		},
	    'swf'  : '/js/uploadify/uploadify.swf',
	    'uploader'    : '/js/uploadify/uploadify.php',
	    'cancelImg' : '/js/adminbase/uploadify/cancel.png',
	    'onUploadSuccess' : function(file, data, response) {
			$("#icon").val(data);
			var image = "<img src='"+data+"' />";
			$("#show_icon").html(image);
		},
	    'auto'      : true,
	    'onError' : function(event, ID, fileObj, errorObj) {
	        alert(errorObj.type+"::"+errorObj.info);
	     }
	});
});
