<?php 

$quiz = \App\Quiz::find($id);

$questions = \App\Question::where('quiz_id', $id)->orderBy('qn_no', 'ASC')->get();

$i = 1;

function myscore($quid) {
    return \DB::table('answers')->join('attempts', 'attempts.aid', '=', 'answers.id')
    ->select('*')
    ->where('attempts.quiz_id', $quid)->where('answers.correct', 1)
    ->where('attempts.user_id', auth()->user()->id)
    ->count();
}


function ansx($id) {
   return \DB::table('answers')->join('questions', 'questions.id', '=', 'answers.question_id')->select('*')->where('questions.quiz_id', $id)->where('answers.correct', 1)->count();
}

$qf = \App\Quizfeedback::where('user_id', auth()->user()->id)->where('quiz_id', $id)->get();

if(count($qf)) {

	foreach ($qf as $q) {
		$qff = \App\Quizfeedback::find($q->id);
		$qff->seen = 1;
		$qff->save();
	}

}

?>

<div class="border-bottom px-3">
	<div>	
		<button type="button" onclick="seenQuiz()" class="close" data-dismiss="modal" aria-label="Close">
			<span aria-hidden="true">&times;</span>
		</button>
	</div>
	<div class="d-flex justify-content-between align-items-center pt-5">
		<h5 class="quiz-name-title" id="userModalLabel"><i class="fa fa-list"></i> View Quiz Results</h5>
		<h5>Point: <?php echo e(myscore($id)); ?> out of <?php echo e(ansx($id)); ?></h5>
	</div>
</div>

<div class="modal-body">

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

			<?php 

			$attt = \DB::table('answers')->join('attempts', 'attempts.aid', '=', 'answers.id')
					->select('*')
					->where('attempts.quiz_id', $id)->where('answers.correct', 1)
					->where('attempts.user_id', auth()->user()->id)
					->where('answers.id', $a->id)
					->count();

			if($attt) {
				$checked = 'checked="true"';
			}else {
				$checked = "";
			}

			//$checked = $a->correct == 0 ? '' : 'checked="true"'; 

			?>

			<?php if($q->category == "single"): ?>

			<?php
				if($a->correct == 0) {
					$corrX = " <b>(WRONG)</b>";
				}else{
					$corrX = " <b>(CORRECT)</b>";
				}
			?>


			<div class="custom-control custom-checkbox">
				<input type="checkbox" id="question<?php echo e($q->id); ?>_answer_<?php echo e($a->id); ?>_single" <?php echo $checked; ?> name="customRadio_<?php echo e($a->id); ?>" class="custom-control-input">
				<label class="custom-control-label" for="question<?php echo e($q->id); ?>_answer<?php echo e($a->id); ?>"><?php echo e($a->answer); ?> - <?php echo $corrX; ?></label>
			</div>
			<?php endif; ?>
			<?php if($q->category == "multiple"): ?>
			<?php
				if($a->correct == 0) {
					$corrX = " <b>(WRONG)</b>";
				}else{
					$corrX = " <b>(CORRECT)</b>";
				}
			?>
			<div class="custom-control custom-checkbox">
				<input type="checkbox" class="custom-control-input" <?php echo $checked; ?> id="question<?php echo e($q->id); ?>_answer<?php echo e($a->id); ?>">
				<label class="custom-control-label" for="question<?php echo e($q->id); ?>_answer<?php echo e($a->id); ?>"><?php echo e($a->answer); ?> - <?php echo $corrX; ?></label>
			</div>
			<?php endif; ?>
			<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

			<?php if($q->qn_photo_location != ""): ?>
				<br/>
				<img src="<?php echo e($q->qn_photo_location); ?>" style="width: 450px" />
			<?php endif; ?>
			
			
		
			
		</div>
	</fieldset>
	<hr>
	<br/>
	<?php $i++; ?>
	<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

	
	
</div>

<script type="text/javascript">
	function seenQuiz() {
		window.location = '';
	}
</script>