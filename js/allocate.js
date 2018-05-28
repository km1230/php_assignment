//assign value to checkbox on click
$(document).on('click', '.removeStaff', function(){
	if($(this).prop('checked')){
		$(this).val('1');
		$(this).parent().css('background-color', '#d02c2c')
	} else {
		$(this).val('0');
		$(this).parent().css('background-color', 'white')
	}
});

//finalize all checkbox before updating staff
$('#update').click(function(){
	$('.removeStaff').each(function(){
		$(this).prop('checked', true)
	})
});