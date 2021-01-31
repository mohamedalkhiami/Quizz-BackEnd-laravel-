<?php

use App\Notificationsetting;

$check = Notificationsetting::where('noty_type', 'emailNotification')->count();
if($check == 0) {
	$n = new Notificationsetting;
	$n->noty_type = 'pushNotification';
	$n->noty_allowed = 0;
	$n->save();
} 

$check_ = Notificationsetting::where('noty_type', 'emailNotification')->count();
if($check_ == 0) {
	$n = new Notificationsetting;
	$n->noty_type = 'emailNotification';
	$n->noty_allowed = 0;
	$n->save();
} 

?>




<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="description" content="some page description here">
        <meta name="keywords" content="page keywords here">  
        <link rel="shortcut icon" href="<?php echo e(url('images/favicon.ico')); ?>" type="image/x-icon" />
        <link rel="stylesheet" href="<?php echo e(url('css/master.min.css')); ?>">
        <title>Login</title>
       	<link rel="stylesheet" type="text/css" href="<?php echo e(url('vendors/fa/css/all.css')); ?>">
       	<link rel="stylesheet" type="text/css" href="<?php echo e(url('vendors/sa/sweetalert.css')); ?>">
       	<link rel="stylesheet" type="text/css" href="<?php echo e(url('vendors/ve/css/validationEngine.jquery.css')); ?>">
    </head>

    <body class="home">
		<div class="home-form" style="margin-top: 100px">
			<img class="home_logo img-fluid d-block mx-auto" src="<?php echo e(\App\HelperX::appLogo()); ?>" alt="Logo">
			<h2 class="text-center system_name"><?php echo e(\App\HelperX::appName()); ?></h2>
			<?php if(session()->has('error')): ?>
			<div class="alert alert-danger flush">
				<i class="fa fa-ban"></i> <?php echo e(session()->get('error')); ?>

			</div>
			<script src="<?php echo e(url('js/jquery.min.js')); ?>"></script>
			<script type="text/javascript">
				$('.flush').delay(5000).fadeOut();
			</script>
			<?php endif; ?>

			<?php if(session()->has('success')): ?>
			<div class="alert alert-success flush">
				<i class="fa fa-check"></i> <?php echo e(session()->get('success')); ?>

			</div>
			<script src="<?php echo e(url('js/jquery.min.js')); ?>"></script>
			<script type="text/javascript">
				$('.flush').delay(5000).fadeOut();
			</script>
			<?php endif; ?>
			<div class="tab-content" id="nav-tabContent">
				<div class="tab-pane fade show active jsHeightLogin" id="nav-login" role="tabpanel" aria-labelledby="nav-login-tab">

					<div class="tab-content" id="nav-tabContent" >
						<div class="tab-pane fade show active" id="nav-password" role="tabpanel" aria-labelledby="nav-password-tab" >

							

							<form id="loginForm" action="<?php echo e(route('app.dologin')); ?>" method="POST" onsubmit="return processLogin()">

								<?php echo e(csrf_field()); ?>	

								<div class="form-group">
									<label for="login_email">Email address</label>
									<input type="text"  name="email" class="validate[required,custom[email]] form-control" id="login_email">
								</div>
								<div class="form-group">
									<label for="login_password">Password</label>
									<input type="password" name="password" class="validate[required] form-control" id="login_password">
								</div>
								<div class="form-group mt-4">
									<button type="submit" class="btn btn-primary btn-block px-5 py-2">Login</button>
								</div>
								<br/>
								<a href="#" style="color: white; text-decoration: underline;" data-toggle="modal" data-target="#passwordResetDialog">Forget Password??</a>
							</form>
						</div>
						<div class="tab-pane fade" id="nav-nopassword" role="tabpanel" aria-labelledby="nav-nopassword-tab">
							
						<form action="index.html">
							<div class="form-group">
								<label for="new-user-email">Email address</label>
								<input type="email" class="form-control" id="new-user-email">
							</div>
							<div class="form-group mt-4">
								<button type="submit" class="btn btn-primary btn-block px-5 py-2">Send</button>
							</div>
						</form>
						</div>
					</div>
					<nav>
						<div class="nav nav-tabs nav-fill border-0" id="nav-tab" role="tablist">
							<a class="nav-item nav-link text-right active" id="nav-password-tab" data-toggle="tab" href="#nav-password" role="tab" aria-controls="nav-password" aria-selected="true">Back to Login</a>
							<!-- <a class="nav-item nav-link text-right" id="nav-nopassword-tab" data-toggle="tab" href="#nav-nopassword" role="tab" aria-controls="nav-nopassword" aria-selected="false">Forget Password?</a> -->
						</div>
					</nav>
				</div>
				<div class="tab-pane fade jsHeightLogin jsHeightReg" id="nav-register" role="tabpanel" aria-labelledby="nav-register-tab">
					<form id="registerForm" action="<?php echo e(route('app.doRegister')); ?>" method="POST">

						<?php echo e(csrf_field()); ?>


						<div class="form-group">
							<label for="register_name">Full Name</label>
							<input type="text" class="validate[required] form-control" id="register_name" name="register_name"
							data-errormessage-value-missing="Fullname is required!"  />
						</div>
						<div class="form-group">
							<label for="register_email">Email address</label>
							<input type="email" class="validate[required,custom[email]] form-control" id="register_email" name="register_email"
							data-errormessage-value-missing="Email is required!" 
							 />
						</div>
						<div class="form-group">
							<label for="register_password">Password</label>
							<input type="password" class="validate[required] form-control" id="register_password" name="register_password"
							data-errormessage-value-missing="Password is required!" 
							 />
						</div>
						<div class="form-group">
							<label for="register_password_confirm">Confirm Password</label>
							<input type="password" class="validate[required,equals[register_password]] form-control" id="register_password_confirm" name="register_password_confirm" 
							data-errormessage-value-missing="Confirm password is required!" 
							data-errormessage="Password mismatches!" />
						</div>
						<button type="button" red="<?php echo e(route('app.doRegister.redirect')); ?>" route="<?php echo e(route('app.doRegister')); ?>" id="doRegister" class="btn btn-primary btn-block px-5 py-2 mt-4">CREATE ACCOUNT!</button>
					</form>
				</div>
			</div>
			
			<nav class="mt-5" style="padding-bottom: 40px">
				<div class="nav nav-tabs nav-fill" id="nav-tab" role="tablist">
					<a class="nav-item nav-link active" id="nav-login-tab" data-toggle="tab" href="#nav-login" role="tab" aria-controls="nav-login"
					aria-selected="true">I have an account</a>
					<a class="nav-item nav-link" id="nav-register-tab" data-toggle="tab" href="#nav-register" role="tab" aria-controls="nav-register"
					aria-selected="false">Create New Account</a>
				</div>
			</nav>
		</div>
		<article>
			<div class="modal fade" id="passwordResetDialog" tabindex="-1" role="dialog" aria-labelledby="passwordResetDialog" aria-hidden="true">
				<div class="modal-dialog modal-dialog-centered " role="document">
					<div class="modal-content">
						<div class="modal-header">
							<h5 class="modal-title text-center" id="passwordResetDialogLabel">Password Reset</h5>
							<button type="button" class="close" data-dismiss="modal" aria-label="Close">
								<span aria-hidden="true">&times;</span>
							</button>
						</div>
						<form id="passwordResetDialogForm">
							<div class="modal-body">
		                        <?php echo e(csrf_field()); ?>

								<div class="form-group">
									<label for="email" class="col-form-label">Enter Email:</label>
		                            <input type="text" name="email" class="validate[required,custom[email]] form-control" id="email"
		                            data-errormessage-value-missing="Email is required!"
		                            />
								</div>
							</div>
							<div class="modal-footer">
								<button type="button" route="<?php echo e(route('app.password.reset')); ?>" id="resetBtn" class="btn btn-primary">Reset Password</button>
							</div>
						</form>
					</div>
				</div>
			</div>
		</article>
        <script src="<?php echo e(url('js/jquery.min.js')); ?>"></script>
        <script src="<?php echo e(url('js/bootstrap.bundle.min.js')); ?>"></script>
        <script src="<?php echo e(url('js/matchHeight.min.js')); ?>"></script>
        <script src="<?php echo e(url('js/nprogress.min.js')); ?>"></script>
        <script src="<?php echo e(url('js/custom.min.js')); ?>"></script>
        <script type="text/javascript" src="<?php echo e(url('vendors/sa/sweetalert.min.js')); ?>"></script>
        <script type="text/javascript" src="<?php echo e(url('vendors/ve/js/languages/jquery.validationEngine-en.js')); ?>"></script>
        <script type="text/javascript" src="<?php echo e(url('vendors/ve/js/jquery.validationEngine.js')); ?>"></script>
        <script type="text/javascript" src="<?php echo e(url('vendors/bjs/biggo.js')); ?>"></script>
        <script type="text/javascript" src="<?php echo e(url('app/login.js')); ?>"></script>
        <script type="text/javascript">
        	$(function() {
        		$('body').on('click', '#resetBtn', function() {
        			var route  = $(this).attr('route');
        			var valid = $('#passwordResetDialogForm').validationEngine('validate');
	        		if(valid) {
	        			var data  = Biggo.serializeData(passwordResetDialogForm);
	        			$("#passwordResetDialogForm").css('opacity', 0.2);
				        $(this).prop('disabled', true);
				        $(this).css('cursor', 'wait');
				        Biggo.talkToServer(route, data).then(function(res){

				        	$('#resetBtn').prop('disabled', false);
							$('#resetBtn').css('cursor', '');
							$("#passwordResetDialogForm").css('opacity', 1);

				        	if(res.error) {
				        		Biggo.showFeedBack(passwordResetDialogForm, res.msg, res.error);
				        	}else {
				        		Biggo.showFeedBack(passwordResetDialogForm, res.msg, res.error);
				        		setTimeout(function(){
				        			window.location = "";
				        		}, 3000);
				        	}

							

							//window.location = "";

						});
	        		}
        		});
        	});
        </script>

    </body>

</html>

