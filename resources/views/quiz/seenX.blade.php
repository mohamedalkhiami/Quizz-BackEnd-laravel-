<?php 



$quiz = \App\Quiz::find($id);

$questions = \App\Question::where('quiz_id', $id)->orderBy('qn_no', 'ASC')->get();

$i = 1;

function myscore($quid, $u) {
    return \DB::table('answers')->join('attempts', 'attempts.aid', '=', 'answers.id')
    ->select('*')
    ->where('attempts.quiz_id', $quid)->where('answers.correct', 1)
    ->where('attempts.user_id', $u)
    ->count();
}


function ansx($id) {
   return \DB::table('answers')->join('questions', 'questions.id', '=', 'answers.question_id')->select('*')->where('questions.quiz_id', $id)->where('answers.correct', 1)->count();
}

// $qf = \App\Quizfeedback::where('user_id', $uxid)->where('quiz_id', $id)->get();

// if(count($qf)) {

// 	foreach ($qf as $q) {
// 		$qff = \App\Quizfeedback::find($q->id);
// 		$qff->seen = 1;
// 		$qff->save();
// 	}

// }

?>

<div class="border-bottom px-3">
	<div>	
		<button type="button"  class="close" data-dismiss="modal" aria-label="Close">
			<span aria-hidden="true">&times;</span>
		</button>
	</div>
	<div class="d-flex justify-content-between align-items-center pt-5">
		<h5 class="quiz-name-title" id="userModalLabel"><i class="fa fa-list"></i> View Quiz Results</h5>
		<h5>Point: {{myscore($id, $uxid)}} out of {{ansx($id)}}</h5>
	</div>
</div>

<div class="modal-body">

	@foreach($questions as $q)
	<fieldset class="mb-3" id="qn{{$q->id}}">
		<h5 class="d-flex"><span class="question-number">{{$q->qn_no < 10 ? '0'.$q->qn_no : $q->qn_no}}.</span> <span>
			{{$q->question}}
		</span></h5>
		<div class="answers-option ml-5 ">
			<?php
				$answers = \App\Answer::where('question_id', $q->id)->get();
			?>
			@foreach($answers as $a)

			<?php 

			$attt = \DB::table('answers')->join('attempts', 'attempts.aid', '=', 'answers.id')
					->select('*')
					->where('attempts.quiz_id', $id)->where('answers.correct', 1)
					->where('attempts.user_id', $uxid)
					->where('answers.id', $a->id)
					->count();

			if($attt) {
				$checked = 'checked="true"';
			}else {
				$checked = "";
			}

			//$checked = $a->correct == 0 ? '' : 'checked="true"'; 

			?>

			@if($q->category == "single")

			<?php
				if($a->correct == 0) {
					$corrX = " <b>(WRONG)</b>";
				}else{
					$corrX = " <b>(CORRECT)</b>";
				}
			?>


			<div class="custom-control custom-checkbox">
				<input type="checkbox" id="question{{$q->id}}_answer_{{$a->id}}_single" {!! $checked !!} name="customRadio_{{$a->id}}" class="custom-control-input">
				<label class="custom-control-label" for="question{{$q->id}}_answer{{$a->id}}">{{$a->answer}} - {!!$corrX!!}</label>
			</div>
			@endif
			@if($q->category == "multiple")
			<?php
				if($a->correct == 0) {
					$corrX = " <b>(WRONG)</b>";
				}else{
					$corrX = " <b>(CORRECT)</b>";
				}
			?>
			<div class="custom-control custom-checkbox">
				<input type="checkbox" class="custom-control-input" {!! $checked !!} id="question{{$q->id}}_answer{{$a->id}}">
				<label class="custom-control-label" for="question{{$q->id}}_answer{{$a->id}}">{{$a->answer}} - {!!$corrX!!}</label>
			</div>
			@endif
			@endforeach

			@if($q->qn_photo_location != "")
				<br/>
				<img src="{{$q->qn_photo_location}}" style="width: 450px" />
			@endif
			
			
		
			
		</div>
	</fieldset>
	<hr>
	<br/>
	<?php $i++; ?>
	@endforeach

	
	
</div>

<script type="text/javascript">
	function seenQuiz() {
		window.location = '';
	}
</script>