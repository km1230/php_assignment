$(document).ready(function(){
	//menu active highlight
	$('#menu > a').removeClass('active');
	var loc = $(location).attr('href');	
	if(loc.search('home') >=0){
		$('#home').addClass('active');
	};
	if(loc.search('registration')>=0){
		$('#reg').addClass('active');
	};
	if(loc.search('lazenbys')>=0){
		$('#lazenbys').addClass('active');
	};
	if(loc.search('ref')>=0){
		$('#ref').addClass('active');
	};
	if(loc.search('trade')>=0){
		$('#trade').addClass('active');
	};
	if(loc.search('account')>=0){
		$('#account').addClass('active');
	};
	if(loc.search('cart')>=0){
		$('#cart').addClass('active');
	};
	if(loc.search('allocate')>=0){
		$('#allocate').addClass('active');
	};

	//responsive menu bar
	$(window).resize(function(){
		if($(window).width()<=992){
			$('.nav-link').css('display', 'none')
		} else {
			$('.nav-link').css('display', 'block')
		};
	});
	$('#menuToggle').click(function(){
		if($('.nav-link').css('display') == 'none'){
			$('.nav-link').css('display', 'block');
		} else {
			$('.nav-link').css('display', 'none')
		}
	});
});