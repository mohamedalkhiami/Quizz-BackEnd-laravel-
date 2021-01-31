'use strict'


// Process Login 
function processLogin() {
	return $("#loginForm").validationEngine('validate');
}

// Process Register

$(function() {
	$('body').on('click', '#doRegister', function() {
			var valid = $("#registerForm").validationEngine('validate');
			var route = $(this).attr('route');
			var redirect = $(this).attr('red');
			
			if(valid) {
				var data = Biggo.serializeData(registerForm);
				swal({
				  title: "Create Account",
				  text: "New User will created",
				  type: "info",
				  showCancelButton: true,
				  closeOnConfirm: false,
				  showLoaderOnConfirm: true
				}, function () {
				  	Biggo.talkToServer(route, data).then(function(res){

				  		if (res.error) {
				  			swal({
				                title: 'Error!',
				                text: res.msg,
				                type: 'error'
				            }, function() {
				                
				            });
				  		}else{
				  			swal({
				                title: 'Account Created!',
				                text: 'Successfully created!',
				                type: 'success'
				            }, function() {
				                window.location = $('#doRegister').attr('red');
				            });
				  		}
						
						
					});
				});

			}
	});
})