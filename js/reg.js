$(document).ready(function(){
	//check ID
	$('#sid').keyup(function(){
		if($('#sid').val().length<6 || $('#sid').val().length>6){
			$('#sid_error').text('Please enter 6 chars')
		} else if((/[\W\s]/).test($('#sid').val())) {
			$('#sid_error').text('Please enter alphabets and digits only')
		} else if(!(/^[a-zA-Z]{2}/).test($('#sid').val()) || !(/[0-9]{4}$/).test($('#sid').val())) {
			$('#sid_error').text('Please enter 2 alphabets followed by 4 digits')
		} else {
			$('#sid_error').text('')
		};
	});

	//check duplicated user id
	$('#sid').focusout(function(){
		if($('#sid_error').text() == ''){
			$.get("checkduplicated.php", {sid: $('#sid').val()}, function(data){
				$('#sid_error').text(data)
			});
		};
	});

	//check name
	$('#name').keyup(function(){
		if(!(/^[A-Z]/).test($('#name').val())) {
			$('#name_error').text('Please start with an uppercase')
		} else if((/[\s]{2,}/).test($('#name').val())){
			$('#name_error').text('Consecutive whitespaces entered')
		} else if(!(/[a-z]$/).test($('#name').val())){
			$('#name_error').text('Please end with a lowercase')	
		} else {
			$('#name_error').text('')
		}
	});

	//check password and calculate password strength	
	$('#password').keyup(function(){
		let pw = $('#password').val();
		let l = pw.length;
		let char = pw.split('');
		let lowerscore = 0, upperscore = 0, digitscore = 0, symscore = 0, middle = 0, requirement = 0, strength = 0;

		char.forEach(c=>{
			if((/[a-z]/).test(c)){
				lowerscore++;
			} else if((/[A-Z]/).test(c)){
				upperscore++;
			} else if((/[0-9]/).test(c)){
				digitscore++;
				if(pw.lastIndexOf(c)>0){
					middle++
				};
			} else if((/[\W]/).test(c)){
				symscore++;
				if(pw.lastIndexOf(c)>0){
					middle++
				}
			};
		});

		let scorearray = [lowerscore, upperscore, digitscore, symscore, middle];
		scorearray.forEach(s=>{
			if(s>0){
				requirement++
			};
		});

		//password strength
		strength = l*4 +(l-lowerscore)*2 + (l-upperscore)*2 + digitscore*4 + symscore*6 + middle*2;
		if(requirement>4){
			strength += requirement*2
		};

		$('#pw_meter').val(strength);

		if(pw.length<6){
			$('#pw_error').text('Please enter 6-12 chars')
		} else if(pw.length>12){
			$('#pw_error').text('Please enter 6-12 chars')
		} else if(!(/[\W]/).test(pw)){
			$('#pw_error').text('Please enter at least 1 symbol')
		} else if(!(/[A-Z]/).test(pw)){
			$('#pw_error').text('Please enter at least 1 uppercase')
		} else if(!(/[a-z]/).test(pw)){
			$('#pw_error').text('Please enter at least 1 lowercase')
		} else if(!(/[0-9]/).test(pw)){
			$('#pw_error').text('Please enter at least 1 digit')
		} else if((/\s/).test(pw)){
			$('#pw_error').text('No whitespace is allowed in password')
		} else if((/\\/).test(pw)){
			$('#pw_error').text('No blackslash is allowed in password')	
		} else {
			$('#pw_error').text('')
		};
	});

	//check password confirmation
	$('#password2').keyup(function(){
		if($('#password2').val() != $('#password').val()){
			$('#pw2_error').text('Passwords do not match')
		} else {
			$('#pw2_error').text('')
		}
	});

	//check email
	$('#email').keyup(function(){
		if(!(/[.\S]+\@[\w]+\.[\w]+/).test($('#email').val())){
			$('#email_error').text('Please enter valid email address')
		} else {
			$('#email_error').text('')
		}
	});

	//check duplicated email address
	$('#email').focusout(function(){
		if($('#email_error').text() == ''){
			$.get("checkduplicated.php", {email: $('#email').val()}, function(data){
				$('#email_error').text(data)
			});
		};
	});

	//check updateEmail (MyAccount)
	$('#updateEmail').keyup(function(){
		if(!(/[.\S]+\@[\w]+\.[\w]+/).test($('#updateEmail').val())){
			$('#email_error').text('Please enter valid email address')
		} else {
			$('#email_error').text('')
		}
	});

	//check if email duplicated with other users (MyAccount)
	$('#updateEmail').focusout(function(){
		if($('#email_error').text() == ''){
			$.get("checkduplicated.php", {updateEmail: $('#updateEmail').val()}, function(data){
				$('#email_error').text(data)
			});
		};
	});

	//check phone number
	$('#phone').keyup(function(){
		if(!(/[0-9]/).test($('#phone').val())){
			$('#phone_error').text('Please enter valid phone number')
		} else if($('#phone').val().length != 10){
			$('#phone_error').text('Please enter valid phone number')
		} else {
			$('#phone_error').text('')
		}
	});

	//check credit card number
	$('#card').keyup(function(){
		if($('#card').val().length != 16){
			$('#card_error').text('Please enter valid credit card number')
		} else if($('#card').val()[0] != 4 && $('#card').val()[0] != 5 && $('#card').val()[0] != 6){
			$('#card_error').text('Please enter valid credit card number')
		} else if($('#card').val().search('-')>=0){
			$('#card_error').text('No need to enter "-"')
		} else {
			$('#card_error').text('')
		}		
	});

	//lock sign up button until for any wrong information in 1second interval
	setInterval(function(){
		if($('#sid_error').text() != '' || $('#pw_error').text() != '' || $('#pw2_error').text() != '' ||
		$('#email_error').text() != '' || $('#phone_error').text() != '' || $('#card_error').text() != '' ||
		$('#name_error').text() != ''){
			$('#submit').prop('disabled', true);
			$('#submit').removeClass('btn-info');
			$('#submit').addClass('btn-disabled')
		} else {
			$('#submit').prop('disabled', false);
			$('#submit').removeClass('btn-disabled');
			$('#submit').addClass('btn-info')
		}
	},500);

	//page reload button
	$('.btn-secondary').click(function(){
		location.reload();
	});
});