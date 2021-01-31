
<?php 

$quiz = \App\Quiz::find($id);

$questions = \App\Question::where('quiz_id', $id)->orderBy('qn_no', 'ASC')->get();

$i = 1;

?>


@if(count($questions) == 0)
<div class="alert alert-danger">
	No Questions published yet!
</div>
@else

<div class="modal-body">
<form>

	@if($quiz->questions_no == count($questions))
	@if($quiz->status == 0)
	<div class="alert alert-warning"><i class="fa fa-exclamation"></i> Scoll down to Publish Quiz</div>
	@else
	<div class="alert alert-info"><i class="fa fa-tick"></i> Quiz Published Successfully!</div>
	@endif
	@endif

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

			<?php $checked = $a->correct == 0 ? '' : 'checked="true"'; ?>

			@if($q->category == "single")
			<div class="custom-control custom-radio">
				<input type="radio" id="question{{$q->id}}_answer_{{$a->id}}_single" {!! $checked !!} name="customRadio_{{$a->id}}" class="custom-control-input">
				<label class="custom-control-label {{ $a->correct == 0 ? '' : 'text-success' }}" for="question{{$q->id}}_answer{{$a->id}}">{{$a->answer}}</label>
			</div>
			@endif
			@if($q->category == "multiple")
			<div class="custom-control custom-checkbox">
				<input type="checkbox" class="custom-control-input" {!! $checked !!} id="question{{$q->id}}_answer{{$a->id}}">
				<label class="custom-control-label {{ $a->correct == 0 ? '' : 'text-success' }}" for="question{{$q->id}}_answer{{$a->id}}">{{$a->answer}}</label>
			</div>
			@endif
			@endforeach

			@if($q->qn_photo_location != "")
				<br/>
				<img src="{{$q->qn_photo_location}}" style="width: 450px" />
			@endif
			
			@if($quiz->status == 0)
			<hr/>
			<p> <span style="cursor: pointer;" qn="{{$q->id}}" class="editQn" quizid="{{$id}}" route="{{route('question.edit',$q->id)}}"><i class="fa fa-edit text-success"></i> Edit </span>  <!--  <span style="cursor: pointer;" disabled="true" class="deleteQn" route="{{route('quiz.destroy',$q->id)}}"><i class="fa fa-trash text-danger"></i> Delete </span> --></p>
			@else
			
			@endif

			
		
			
		</div>
	</fieldset>
	<br/>
	<?php $i++; ?>
	@endforeach

	<hr/>
	@if($quiz->questions_no == count($questions))
	@if($quiz->status == 0)
	<button 
		type="button" 
		id="publishQuiz"
        class="btn btn-warning btn-sm" 
        route="{{route('quiz.publish', $quiz->id)}}"
		><i class="fa fa-cog"></i> Publish Quiz Now
	</button>
	@else
	<button 
		type="button" 
		id="publishUnQuiz"
        class="btn btn-danger btn-sm" 
        route="{{route('quiz.unpublish', $quiz->id)}}"
		><i class="fa fa-edit"></i>  UnPublish Quiz Now
	</button>
	@endif
	@endif
	
</form>
</div>

@endif