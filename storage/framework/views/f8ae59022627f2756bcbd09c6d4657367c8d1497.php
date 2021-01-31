<?php $__env->startSection('title', 'Settings'); ?>

<?php $__env->startSection('content'); ?>

<?php if(session()->has('success')): ?>
<div class="alert alert-success flush">
    <i class="fa fa-check"></i> <?php echo e(session()->get('success')); ?>

</div>
<script src="<?php echo e(url('js/jquery.min.js')); ?>"></script>
<script type="text/javascript">
    $('.flush').delay(5000).fadeOut();
</script>
<?php endif; ?>

<?php if(session()->has('error')): ?>
<div class="alert alert-danger flush">
    <i class="fa fa-ban"></i> <?php echo e(session()->get('error')); ?>

</div>
<script src="<?php echo e(url('js/jquery.min.js')); ?>"></script>
<script type="text/javascript">
    $('.flush').delay(5000).fadeOut();
</script>
<?php endif; ?>

<?php
	$pn = App\Notificationsetting::where('noty_type', 'pushNotification')->first();
	$en = App\Notificationsetting::where('noty_type', 'emailNotification')->first();
?>

<div class="setting">
	<div class="row">
		<div class="col-md-5">
			<div class="card card-dashboard">
				<div class="card-header d-flex justify-content-between">
					<div class="card-icon flex-shrink-0">
						<i class="fas fa-key fa-3x"></i>
					</div>
					<div class="card-details">
						<p class="card-category m-0">Change your account password</p>
					</div>
				</div>
				<div class="card-footer">
					<form action="<?php echo e(route('app.settings.changepassword')); ?>" method="POST" onsubmit="return settingChangePass()" id="settingChangePassForm">
                        <?php echo e(csrf_field()); ?>

                        <div class="form-group">
							<label for="newPassword">New Password</label>
							<input type="password" class="validate[required] form-control" data-errormessage-value-missing="Password is required!"  id="newPassword" name="newPassword" />
						</div>
						<div class="form-group">
							<label for="cnewPassword">Confirm Password</label>
							<input type="password" class="validate[required,equals[newPassword]] form-control" id="cnewPassword" data-errormessage-value-missing="Confirm password is required!" 
							data-errormessage="Password mismatches!" name="cnewPassword">
						</div>
						<button type="submit" class="btn btn-primary btn-block px-5 py-2 mt-4">Submit</button>
					</form>
				</div>
			</div>
		</div>
		<!-- <div class="col-lg-3 col-md-6 col-sm-6">
			<div class="card card-dashboard">
				<div class="card-header d-flex justify-content-between">
					<div class="card-icon flex-shrink-0 p-0">
						<img class="img-fluid change-picture" src="<?php echo e(url('images/profile.jpg')); ?>" alt="Profile picture">
					</div>
					<div class="card-details">
						<p class="card-category m-0">Change your profile picture</p>
					</div>
				</div>
				<div class="card-footer">
					<form action="">
						<div class="input-group mb-3">
							<div class="custom-file">
								<input type="file" class="custom-file-input" id="change_profile_picture" aria-describedby="inputGroupFileAddon01">
								<label class="custom-file-label" for="change_profile_picture">Choose file</label>
							</div>
						</div>
						<button type="submit" class="btn btn-primary btn-block px-5 py-2 mt-4">Submit</button>
					</form>
				</div>
			</div>
		</div> -->
		<?php if(auth()->user()->role_id == 1): ?>
		<div class="col-md-5">
			<div class="card card-dashboard">
				<div class="card-header d-flex justify-content-between">
					<div class="card-icon flex-shrink-0 p-0">
						<img class="img-fluid change-picture" src="<?php echo e(\App\HelperX::appLogo()); ?>" alt="Profile picture">
					</div>
					<div class="card-details">
						<p class="card-category m-0">Change system logo</p>
					</div>
				</div>
				<div class="card-footer">
					<form action="<?php echo e(route('app.settings.changeapplogo')); ?>" method="POST" enctype="multipart/form-data" onsubmit="return settingChangeAppLogo()" id="settingChangeAppLogoForm">
						<div class="input-group mb-3">
                            <?php echo e(csrf_field()); ?>

							<div class="custom-file">
								<input type="file" class="validate[required] custom-file-input" id="change_logo" name="change_logo" aria-describedby="inputGroupFileAddon01">
								<label class="custom-file-label" for="change_logo">Choose file</label>
							</div>
						</div>
						<button type="submit" class="btn btn-primary btn-block px-5 py-2 mt-4">Submit</button>
					</form>
				</div>
			</div>
		</div>
		<?php endif; ?>
    </div>
    <br/><br/>
    <?php if(auth()->user()->role_id == 1): ?>
    <div class="row">
		<div class="col-md-5">
			<div class="card card-dashboard">
				<div class="card-header d-flex justify-content-between">
					<div class="card-icon flex-shrink-0">
						<i class="fas fa-envelope fa-3x"></i>
					</div>
					<div class="card-details">
						<p class="card-category m-0">Change your account email</p>
					</div>
				</div>
				<div class="card-footer">
					<form action="<?php echo e(route('app.settings.changeemail')); ?>" method="POST" onsubmit="return settingChangeEmail()" id="settingChangeEmailForm">
                        <?php echo e(csrf_field()); ?>

                        <div class="form-group">
							<label for="email">Your Email</label>
							<input type="text" value="<?php echo e(auth()->user()->email); ?>" class="validate[required,custom[email]] form-control" data-errormessage-value-missing="Email is required!"  id="email" name="email" />
						</div>
						<button type="submit" class="btn btn-primary btn-block px-5 py-2 mt-4">Submit</button>
					</form>
				</div>
			</div>
        </div>
        <div class="col-md-5">
			<div class="card card-dashboard">
				<div class="card-header d-flex justify-content-between">
					<div class="card-icon flex-shrink-0">
						<i class="fa fa-university fa-3x"></i>
					</div>
					<div class="card-details">
						<p class="card-category m-0">Change System Name</p>
					</div>
				</div>
				<div class="card-footer">
					<form action="<?php echo e(route('app.settings.changeappname')); ?>" method="POST" onsubmit="return settingChangeAppName()" id="settingChangeAppNameForm">
                        <?php echo e(csrf_field()); ?>

                        <div class="form-group">
							<label for="email">System Name</label>
							<input type="text" value="<?php echo e(\App\HelperX::appName()); ?>" class="validate[required] form-control" data-errormessage-value-missing="App Name is required!"  id="appname" name="appname" />
						</div>
						<button type="submit" class="btn btn-primary btn-block px-5 py-2 mt-4">Submit</button>
					</form>
				</div>
			</div>
		</div>
	</div>
	<?php endif; ?>
	<br/><br/>
	<?php if(auth()->user()->role_id == 1): ?>
	
    <div class="row">
		<div class="col-md-5">
			<div class="card card-dashboard">
				<div class="card-header d-flex justify-content-between">
					<div class="card-icon flex-shrink-0">
						<i class="fas fa-bell fa-3x"></i>
					</div>
					<div class="card-details">
						<p class="card-category m-0">Notifications</p>
					</div>
				</div>
				<div class="card-footer">
					<form action="<?php echo e(route('app.settings.notification')); ?>" method="POST" onsubmit="return settingChangeEmail()" id="settingChangeEmailForm">
                        <?php echo e(csrf_field()); ?>

                        <div class="">
							<input type="checkbox" name="pushNotification" <?php 
								if($pn->noty_allowed == 1) { echo "checked"; } ?>  />
							<label for="pushNotification">Allow Push Notification</label>
						</div>
						<div class="">
							<input type="checkbox" name="emailNotification" <?php 
								if($en->noty_allowed == 1) { echo "checked"; } ?>   />
							<label for="emailNotification">Allow Email Notification</label>
						</div>
						<button type="submit" class="btn btn-primary btn-block px-5 py-2 mt-4">Submit</button>
					</form>
				</div>
			</div>
        </div>
	</div>
	<?php endif; ?>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('scripts'); ?>
<script>
function settingChangePass() {
    return $("#settingChangePassForm").validationEngine('validate');
}
function settingChangeEmail() {
    return $("#settingChangeEmailForm").validationEngine('validate');
}
function settingChangeAppName() {
    return $("#settingChangeAppNameForm").validationEngine('validate');
}
function settingChangeAppLogo() {
    return $("#settingChangeAppLogoForm").validationEngine('validate');
}
</script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layout', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>