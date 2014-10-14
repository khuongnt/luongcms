$(document).ready(function() {
	setInterval(function(){ hideMessage()},5000);
	$('.btnDeleteRow').click(function(){
		var path = $(this).attr('rel');
		if (confirm("Bạn có chắc chắn muốn xóa bản ghi này?")) {
			document.location = path;
	    } 
	});
	
	
	$('#category').change(function(){
		var category = $('#category').val();
		window.location.href="/admin/news/index/category/"+category;
	});
	$('#category_partner').change(function(){
		var category = $('#category_partner').val();
		window.location.href="/admin/partner/index/category/"+category;
	});
	
	function isNumberKey(evt){
		var charCode = (evt.which) ? evt.which : event.keyCode
		if (charCode > 31 && (charCode < 48 || charCode > 57))
		return false;
		return true;
	}
	
	//Tooltips
	$(".tip_trigger").hover(function(){
		tip = $(this).find('.tip');
		tip.show(); //Show tooltip
	}, function() {
		tip.hide(); //Hide tooltip		  
	}).mousemove(function(e) {
		var mousex = e.pageX + 20; //Get X coodrinates
		var mousey = e.pageY + 20; //Get Y coordinates
		var tipWidth = tip.width(); //Find width of tooltip
		var tipHeight = tip.height(); //Find height of tooltip
		//Distance of element from the right edge of viewport
		var tipVisX = $(window).width() - (mousex + tipWidth);
		//Distance of element from the bottom of viewport
		var tipVisY = $(window).height() - (mousey + tipHeight);
		if ( tipVisX < 20 ) { //If tooltip exceeds the X coordinate of viewport
			mousex = e.pageX - tipWidth - 20;
		} if ( tipVisY < 20 ) { //If tooltip exceeds the Y coordinate of viewport
			mousey = e.pageY - tipHeight - 20;
		} 
		tip.css({  top: mousey, left: mousex });
	});
});


jQuery(function($) {
    $('.autoNumeric').autoNumeric('init');
});

function hideMessage() {
	$('.alert-success').hide();
}
function change(id) {
	var order = $('#order'+id).val();
    var formData = {id:id, order:order }; //Array 
    $.ajax({
        url : "/admin/category/change-order",
        type: "POST",
        data : formData,
        success: function(data, textStatus, jqXHR){
            //data - response from server
        },
        error: function (jqXHR, textStatus, errorThrown){
        }
    });
}

function change(id, url) {
	var order = $('#order'+id).val();
    var formData = {id:id, order:order }; //Array 
    $.ajax({
        url : url,
        type: "POST",
        data : formData,
        success: function(data, textStatus, jqXHR){
            //data - response from server
        },
        error: function (jqXHR, textStatus, errorThrown){
        }
    });
}

$(document).ready(function(){
    var check_features=0;
    var check_additional=0;
    $("#features").click(function(){
        if(check_features==0)
        {
            $(this).html("<span class='span1 btn btn-info'>Bỏ chọn</span>");
            $(".features").each(function(){
                this.checked =true;
                check_features=1;
            })
        }
        else
        {
            $(this).html("<span class='span1 btn btn-info'>Chọn hết</span>");
            $(".features").each(function(){
                this.checked =false;
                check_features=0;
            })
        }
    });
    
    $("#additional").click(function(){
        if(check_additional==0)
        {
        	$(this).html("<span class='span1 btn btn-info'>Bỏ chọn</span>");
            $(".additional").each(function(){
                this.checked =true;
                check_additional=1;
            })
        }
        else
        {
        	$(this).html("<span class='span1 btn btn-info'>Chọn hết</span>");
            $(".additional").each(function(){
                this.checked =false;
                check_additional=0;
            })
        }
    });
});

function check_moto_rental() {
	var lease_term        = $('[name=lease_term]').val();
	var seats             = $('[name=seats]').val();
	var weight            = $('[name=weight]').val();
	var year_manufactured = $('[name=year_manufactured]').val();
	var model             = $('[name=model]').val();
	var price             = $('[name=price]').val();
	
	var name_vi           = $('[name=name_vi]').val();
	var name_en           = $('[name=name_en]').val();
	var name_jp           = $('[name=name_jp]').val();
	
	var furnishing_vi     = $('[name=furnishing_vi]').val();
	var furnishing_en     = $('[name=furnishing_en]').val();
	var furnishing_jp     = $('[name=furnishing_jp]').val();
	
	var description_vi    = CKEDITOR.instances['description_vi'].getData();
	var description_en    = CKEDITOR.instances['description_en'].getData();
	var description_jp    = CKEDITOR.instances['description_jp'].getData();
	
	if(lease_term == '')
		alert('Lease term không được để trống');
	else if(seats == '')
		alert('Seats không được để trống');
	else if(weight == '')
		alert('Weight không được để trống');
	else if(year_manufactured == '')
		alert('Year Manufactured không được để trống');
	else if(model == '')
		alert('Model không được để trống');
	else if(price == '')
		alert('Price không được để trống');
	else if(name_vi == '')
		alert('Name tiếng việt không được để trống');
	else if(name_en == '')
		alert('Name tiếng anh không được để trống');
	else if(name_jp == '')
		alert('Name tiếng nhật không được để trống');
	else if(furnishing_vi == '')
		alert('Furnishing tiếng việt không được để trống');
	else if(furnishing_en == '')
		alert('Furnishing tiếng anh không được để trống');
	else if(furnishing_jp == '')
		alert('Furnishing tiếng nhật không được để trống');
	else if(description_vi == '')
		alert('Description VI không được để trống');
	else if(description_en == '')
		alert('Description EN không được để trống');
	else if(description_jp == '')
		alert('Description JP không được để trống');
	else 
		frm_moto.submit();
}

function check_house() {
	var using_space       = $('[name=using_space]').val();
	var bedrooms          = $('[name=bedrooms]').val();
	var bathrooms         = $('[name=bathrooms]').val();
	var year_build        = $('[name=year_build]').val();
	var price             = $('[name=price]').val();
	var location          = $('[name=location]').val();
	var district_id       = $('[name=district_id]').val();
	var project_id        = $('[name=project_id]').val();
	
	var parking_vi           	= $('[name=parking_vi]').val();
	var parking_en           	= $('[name=parking_en]').val();
	var parking_jp           	= $('[name=parking_jp]').val();
	
	var furnishing_vi           = $('[name=furnishing_vi]').val();
	var furnishing_en           = $('[name=furnishing_en]').val();
	var furnishing_jp           = $('[name=furnishing_jp]').val();
	
	var highlighted_feature_vi  = $('[name=highlighted_feature_vi]').val();
	var highlighted_feature_en  = $('[name=highlighted_feature_en]').val();
	var highlighted_feature_jp  = $('[name=highlighted_feature_jp]').val();
	
	var name_vi           = $('[name=name_vi]').val();
	var name_en           = $('[name=name_en]').val();
	var name_jp           = $('[name=name_jp]').val();
	
	var description_vi    = CKEDITOR.instances['description_vi'].getData();
	var description_en    = CKEDITOR.instances['description_en'].getData();
	var description_jp    = CKEDITOR.instances['description_jp'].getData();
	if(using_space == '')
		alert('Using space không được để trống');
	else if(bedrooms == '')
		alert('Bedrooms không được để trống');
	else if(bathrooms == '')
		alert('Bathrooms không được để trống');
	else if(year_build == '')
		alert('Year build không được để trống');
	else if(location == '')
		alert('Location không được để trống');
	else if(district_id == '')
		alert('Vui lòng chọn quận');
	
	else if(name_vi == '')
		alert('Name tiếng việt không được để trống');
	else if(name_en == '')
		alert('Name tiếng anh không được để trống');
	else if(name_jp == '')
		alert('Name tiếng nhật không được để trống');
	else if(parking_vi == '')
		alert('Parking tiếng việt không được để trống');
	else if(parking_en == '')
		alert('Parking tiếng anh không được để trống');
	else if(parking_jp == '')
		alert('Parking tiếng nhật không được để trống');
	else if(furnishing_vi == '')
		alert('Furnishing tiếng việt không được để trống');
	else if(furnishing_en == '')
		alert('Furnishing tiếng anh không được để trống');
	else if(furnishing_jp == '')
		alert('Furnishing tiếng nhật không được để trống');
	else if(highlighted_feature_vi == '')
		alert('Highlighted feature tiếng việt không được để trống');
	else if(highlighted_feature_en == '')
		alert('Highlighted feature tiếng anh không được để trống');
	else if(highlighted_feature_jp == '')
		alert('Highlighted feature tiếng nhật không được để trống');
	else if(description_vi == '')
		alert('Description VI không được để trống');
	else if(description_en == '')
		alert('Description EN không được để trống');
	else if(description_jp == '')
		alert('Description JP không được để trống');
	else 
		frm_house.submit();
}

/*Check chỉ cho nhập số*/
function isNumberKey(evt){
	var charCode = (evt.which) ? evt.which : event.keyCode
	if (charCode > 31 && (charCode < 48 || charCode > 57))
	return false;
	return true;
}