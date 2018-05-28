//initialize checkbox on rendering
$('.day').each(function(){
	if($(this).val() == '1'){
		$(this).prop('checked', true)
	};
});
$('.manSelect').each(function(){
	if($(this).val() == '1'){
		$(this).prop('checked', true);
		$(this).parent().css('background-color', '#28a745')
	} else {
		$(this).prop('checked', false);
		$(this).parent().css('background-color', 'white')
	}
});

//set value to checkbox
$(document).on('click', '.day', function(){
	if($(this).prop('checked')){
		$(this).val('1')
	} else {
		$(this).val('0')
	}
});
$(document).on('click', '.manSelect', function(){
	if($(this).prop('checked')){
		$(this).val('1');
		$(this).parent().css('background-color', '#28a745')
	} else {
		$(this).val('0');
		$(this).parent().css('background-color', 'white')
	}
});
$(document).on('click', '.complete', function(){
    if($(this).prop('checked')){
        $(this).val('1');
        $(this).parent().css('background-color', '#28a745')
    } else {
        $(this).val('0');
        $(this).parent().css('background-color', 'white')
    }
});
$(document).on('click', '.remove', function(){
    if($(this).prop('checked')){
        $(this).val('1');
        $(this).parent().css('background-color', '#d02c2c')
    } else {
        $(this).val('0')
        $(this).parent().css('background-color', 'white')
    }
});

//finalize day checkbox before submitting manager selected items
$('#update').click(function(){
    //master page
	$('.day').each(function(){
		if(!$(this).prop('checked')){
			$(this).prop('checked', true)
		}
	});
    //manager page
	$('.manSelect').each(function(){
		if(!$(this).prop('checked')){
			$(this).prop('checked', true)
		}
	});
    //order history page by staff
    $('.completed').each(function(){
		if(!$(this).prop('checked')){
			$(this).prop('checked', true)
		}
	});
	//checkbox with className '.remove' across pages
	$('.remove').each(function(){
		if(!$(this).prop('checked')){
			$(this).prop('checked', true)
		}
	})
});

//reset button
$('.btn-secondary').click(function(){
	location.reload()
});