<?php 

$q = \App\Question::find($id); 

$qz = \App\Quiz::find($quizid);

?>
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
	
	@if($qz->status == 0)
	<hr/>
	<p> <span style="cursor: pointer;" qn="{{$q->id}}" quizid="{{$quizid}}" class="editQn" route="{{route('question.edit',$q->id)}}"><i class="fa fa-edit text-success"></i> Edit </span> <!--  <span style="cursor: pointer;" class="deleteQn" route="{{route('quiz.destroy',$q->id)}}"><i class="fa fa-trash text-danger"></i> Delete </span> --></p>
	@else
	
	@endif

	
</div>