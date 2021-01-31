
<?php 

$quiz = \App\Quiz::find($id);

$questions = \App\Question::where('quiz_id', $id)->orderBy('qn_no', 'ASC')->get();

$i = 1;

?>


<?php if(count($questions) == 0): ?>
<div class="alert alert-danger">
	No Questions published yet!
</div>
<?php else: ?>

<div class="modal-body">
<form>

	<?php if($quiz->questions_no == count($questions)): ?>
	<?php if($quiz->status == 0): ?>
	<div class="alert alert-warning"><i class="fa fa-exclamation"></i> Scoll down to Publish Quiz</div>
	<?php else: ?>
	<div class="alert alert-info"><i class="fa fa-tick"></i> Quiz Published Successfully!</div>
	<?php endif; ?>
	<?php endif; ?>

	<?php $__currentLoopData = $questions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $q): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
	<fieldset class="mb-3" id="qn<?php echo e($q->id); ?>">
		<h5 class="d-flex"><span class="question-number"><?php echo e($q->qn_no < 10 ? '0'.$q->qn_no : $q->qn_no); ?>.</span> <span>
			<?php echo e($q->question); ?>

		</span></h5>
		<div class="answers-option ml-5 ">
			<?php
				$answers = \App\Answer::where('question_id', $q->id)->get();
			?>

			<?php $__currentLoopData = $answers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $a): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>

			<?php $checked = $a->correct == 0 ? '' : 'checked="true"'; ?>

			<?php if($q->category == "single"): ?>
			<div class="custom-control custom-radio">
				<input type="radio" id="question<?php echo e($q->id); ?>_answer_<?php echo e($a->id); ?>_single" <?php echo $checked; ?> name="customRadio_<?php echo e($a->id); ?>" class="custom-control-input">
				<label class="custom-control-label <?php echo e($a->correct == 0 ? '' : 'text-success'); ?>" for="question<?php echo e($q->id); ?>_answer<?php echo e($a->id); ?>"><?php echo e($a->answer); ?></label>
			</div>
			<?php endif; ?>
			<?php if($q->category == "multiple"): ?>
			<div class="custom-control custom-checkbox">
				<input type="checkbox" class="custom-control-input" <?php echo $checked; ?> id="question<?php echo e($q->id); ?>_answer<?php echo e($a->id); ?>">
				<label class="custom-control-label <?php echo e($a->correct == 0 ? '' : 'text-success'); ?>" for="question<?php echo e($q->id); ?>_answer<?php echo e($a->id); ?>"><?php echo e($a->answer); ?></label>
			</div>
			<?php endif; ?>
			<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

			<?php if($q->qn_photo_location != ""): ?>
				<br/>
				<img src="<?php echo e($q->qn_photo_location); ?>" style="width: 450px" />
			<?php endif; ?>
			
			<?php if($quiz->status == 0): ?>
			<hr/>
			<p> <span style="cursor: pointer;" qn="<?php echo e($q->id); ?>" class="editQn" quizid="<?php echo e($id); ?>" route="<?php echo e(route('question.edit',$q->id)); ?>"><i class="fa fa-edit text-success"></i> Edit </span>  <!--  <span style="cursor: pointer;" disabled="true" class="deleteQn" route="<?php echo e(route('quiz.destroy',$q->id)); ?>"><i class="fa fa-trash text-danger"></i> Delete </span> --></p>
			<?php else: ?>
			
			<?php endif; ?>

			
		
			
		</div>
	</fieldset>
	<br/>
	<?php $i++; ?>
	<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

	<hr/>
	<?php if($quiz->questions_no == count($questions)): ?>
	<?php if($quiz->status == 0): ?>
	<button 
		type="button" 
		id="publishQuiz"
        class="btn btn-warning btn-sm" 
        route="<?php echo e(route('quiz.publish', $quiz->id)); ?>"
		><i class="fa fa-cog"></i> Publish Quiz Now
	</button>
	<?php else: ?>
	<button 
		type="button" 
		id="publishUnQuiz"
        class="btn btn-danger btn-sm" 
        route="<?php echo e(route('quiz.unpublish', $quiz->id)); ?>"
		><i class="fa fa-edit"></i>  UnPublish Quiz Now
	</button>
	<?php endif; ?>
	<?php endif; ?>
	
</form>
</div>

<?php endif; ?>