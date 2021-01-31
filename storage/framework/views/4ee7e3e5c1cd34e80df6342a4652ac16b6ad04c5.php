<?php $__env->startSection('title', 'Manage Reminders'); ?>

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



<div class="row">
	<div class="col-md-12" style="background-color: white; padding: 12px">
		<form id="reminderForm">
			<h5><i class="fa fa-bell"></i> Create New Reminder</h5>
			<hr/>
			<div class="form-group">
            <label for="reminder_subject" class="col-form-label">Reminder Subject:</label>
            <textarea name="reminder_subject" class="validate[required] form-control" id="reminder_subject"
            data-errormessage-value-missing="Subject is required!"
            ></textarea>
        </div>
        <div class="form-group">
            <label for="reminder_body" class="col-form-label">Reminder Body:</label>
            <textarea rows="4" name="reminder_body" class="validate[required] form-control" id="reminder_body"
            data-errormessage-value-missing="Body is required!"
            ></textarea>
        </div>
        <div class="form-group">
        	<label for="reminder_recipients" class="col-form-label">Recipients:</label><br/>
        	<hr/>
        	<?php 
				$users = \App\User::where('role_id', '!=', 1)->get();
				$d = 1;
			?>
			<table>
				<tr>
			<?php $__currentLoopData = $users; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $u): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
				<td style="padding:10px">
					<input type="checkbox" id="reminder_recipients" name="reminder_recipients[]"  value="<?php echo e($u->email); ?>" /> <?php echo e($u->email); ?>	
				</td>	
				<?php 
					if($d == 6) {
						echo '</tr>';
						echo  '<tr>';
						$d = 1;
					}else {
						$d++;
					}
				?>	
			<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
			</table>
        </div>
        <hr/><br/>
        <div class="form-group">
            <button type="button" id="createReminder" redirectUrl="<?php echo e(route("app.reminders.refresh")); ?>" route=<?php echo e(route("app.reminders")); ?> class="btn btn-primary"><i class="fa fa-bell"></i> Remind Now</button>
        </div>
		</form>
	</div>
	
</div>

<hr/>

<div class="row">
	<div class="col-md-12" style="background-color: white; padding: 12px">
		<table id="dataTable" class="table table-striped table-bordered">
				<thead>
					<tr>
						<th>#</th>
						<th>Subject</th>
						<th>Body</th>
						<th>Created At</th>
						<th>Recipients</th>
					</tr>
				</thead>
				<tbody>
					<?php 

					$rsx = \App\Reminder::all();
					$i = 1;

					?>
					<?php $__currentLoopData = $rsx; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $r): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
					<tr>
						<td><?php echo e($i); ?></td>
						<td><?php echo e($r->reminder_subject); ?></td>
						<td><?php echo e($r->reminder_body); ?></td>
						<td><?php echo e($r->created_at); ?></td>
						<td><?php echo e($r->reminder_recipients); ?></td>
					</tr>
					<?php $i++; ?>
					<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

				</tbody>
			</table>
	</div>
</div>

<?php $__env->stopSection(); ?>

<?php $__env->startSection('scripts'); ?>

	<script src="<?php echo e(url('js/jquery.dataTables.min.js')); ?>"></script>
	<script src="<?php echo e(url('js/dataTables.bootstrap4.min.js')); ?>"></script>
	<script src="<?php echo e(url('js/dataTables.buttons.min.js')); ?>"></script>
	<script src="<?php echo e(url('js/buttons.bootstrap4.min.js')); ?>"></script>
	<script src="<?php echo e(url('js/buttons.html5.min.js')); ?>"></script>
	<script src="<?php echo e(url('js/buttons.flash.min.js')); ?>"></script>
	<script src="<?php echo e(url('js/buttons.print.min.js')); ?>"></script>

	<script type="text/javascript">

		$(function() {

			var emails = []
			
			$('body').on('click', '#createReminder', function() {

				var route  = $(this).attr('route');
				var redirectUrl = $(this).attr('redirectUrl');

				$("input[name='reminder_recipients[]']:checked").each(function () {
				    emails.push(($(this).val()));
				});

				if(emails.length == 0) {
					Biggo.showFeedBack(reminderForm, 'Please select at least one email', true);
					return;
				}

				var data = $('#reminderForm').serializeArray();
				
				var valid = $("#reminderForm").validationEngine('validate');
				if(valid) {

					$("#reminderForm").css('opacity', 0.2);
				    $(this).prop('disabled', true);
				    $(this).css('cursor', 'wait');

				    
				    
				    data.push({
				    	"name": "_token",
				    	"value": '<?php echo e(csrf_token()); ?>'
				    });


				    Biggo.talkToServer(route, data).then(function(res){

				    	

						$('#createReminder').prop('disabled', false);
						$('#createReminder').css('cursor', '');
						$("#reminderForm").css('opacity', 1);


						window.location = redirectUrl;
						// if(res.error) {
						// 	Biggo.showFeedBack(userEditForm, res.msg, res.error);
						// }
						
						
						
						
					});
				}
			});
		});

		var table = $('#dataTable').DataTable({
			"bInfo": false,
			"pageLength": 15,
			"lengthMenu": [[10, 15 , 25, 50, -1], [10, 15, 25, 50, "All"]],
				dom: "Blfrtip",
				buttons: [
					{
						extend: "excel",
						className: "btn-sm btn-success px-3 py-2",
						title: 'Users'
					},
					{
						extend: "pdf",
						className: "btn-sm btn-danger px-3 py-2",
						title: 'Users'
					},
					{
						extend: "print",
						className: "btn-sm px-3 py-2",
						title: 'Users'
					}
				]
		});
	</script>

<?php $__env->stopSection(); ?>	
<?php echo $__env->make('layout', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>