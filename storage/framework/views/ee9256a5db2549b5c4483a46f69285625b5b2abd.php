<?php $__env->startSection('title', 'Start QUIZ'); ?>


<?php $__env->startSection('content'); ?>

<?php 

$quiz = \App\Quiz::find($id);

$questions = \App\Question::where('quiz_id', $id)->orderBy('qn_no', 'ASC')->get();

$i = 1;

?>

<?php if(session()->has('error')): ?>
<div class="alert alert-danger flush">
	<i class="fa fa-ban"></i> <?php echo e(session()->get('error')); ?>

</div>
<script src="<?php echo e(url('js/jquery.min.js')); ?>"></script>
<script type="text/javascript">
	$('.flush').delay(5000).fadeOut();
</script>
<?php endif; ?>

<div class="page-class ml-4">
	<h3>Quiz</h3>
	<hr/>
		<form id="quizAttemptForm">
				<?php $__currentLoopData = $questions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $q): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
				<fieldset class="mb-3">
					<h5 class="d-flex"><span class="question-number"><?php echo e($q->qn_no < 10 ? '0'.$q->qn_no : $q->qn_no); ?>.</span> <span><?php echo e($q->question); ?></span></h5>
					<div class="answers-option ml-5 ">
						<?php
							$answers = \App\Answer::where('question_id', $q->id)->get();
							$ch = $q->qn_no < 10 ? '0'.$q->qn_no : $q->qn_no;
						?>
						<?php $__currentLoopData = $answers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $a): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>

						<?php $checked = $a->correct == 0 ? '' : 'checked="true"'; ?>

						<?php if($q->category == "single"): ?>
						<div class="custom-control custom-radio">
							<input type="radio" id="question<?php echo e($ch); ?>_answer<?php echo e($a->id); ?>" name="customRadio<?php echo e($ch); ?>" class="custom-control-input answerAttempted" aid="<?php echo e($a->id); ?>" qid="<?php echo e($q->id); ?>" />
							<label class="custom-control-label" for="question<?php echo e($ch); ?>_answer<?php echo e($a->id); ?>"><?php echo e($a->answer); ?></label>
						</div>
						<?php endif; ?>
						<?php if($q->category == "multiple"): ?>
							<div class="custom-control custom-checkbox">
							<input type="checkbox" class="custom-control-input answerAttempted" aid="<?php echo e($a->id); ?>" qid="<?php echo e($q->id); ?>" id="question<?php echo e($ch); ?>_answer<?php echo e($a->id); ?>">
							<label class="custom-control-label" for="question<?php echo e($ch); ?>_answer<?php echo e($a->id); ?>"><?php echo e($a->answer); ?></label>
						</div>
						<?php endif; ?>
						<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
					</div>
					<?php if($q->qn_photo_location != ""): ?>
						<br/>
						<img src="<?php echo e($q->qn_photo_location); ?>" style="width: 480px" />
					<?php endif; ?>

				</fieldset>
				<br/>
				<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
				
				<button type="button" id="submitQuizAttempts" class="btn btn-primary"> Submit Quiz</button>

				<br/>
				<hr/>
		</form>
</div>

<?php $__env->stopSection(); ?>


<?php $__env->startSection('scripts'); ?>
<script>
$(function() {
	$('body').on('click', '#submitQuizAttempts', function() {

		var attempts = [];

		$('.answerAttempted').each(function(i, k) {
			
			var checked = $(this).is(':checked');
			
			if(checked) {
				var aid = $(this).attr('aid');
				var qid = $(this).attr('qid');
				var attempt = {
					"answer_id"   : aid,
					"question_id" : qid
				}
				attempts.push(attempt);
			}
				
		});

		

		swal({
			title: "You are about to submit quiz!",
			text: "Are you sure?",
			type: "info",
			confirmButtonText: 'Yes',
            cancelButtonText: "No",
			showCancelButton: true,
			closeOnConfirm: false,
			showLoaderOnConfirm: true
		}, function () {
			
			var data = {
				_token : '<?php echo e(csrf_token()); ?>',
				attempts: attempts,
				user_id: '<?php echo e(auth()->user()->id); ?>',
				quiz_id: '<?php echo e($id); ?>'				
			}

			$.post('<?php echo e(route("quiz.attempt")); ?>', data, function(res) {
				if(res.error) {
					swal({
					  title: "Error",
					  text: res.msg,
					  type: "error",
					  buttons: true
					}, function() {
						window.location = '<?php echo e(route("app.dashboard")); ?>';
					});
					
				}else {
					window.location = '<?php echo e(route("quiz.attempt.refresh")); ?>';
				}
				
			});
			
		});		

		
		
	});
});
</script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layout', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>