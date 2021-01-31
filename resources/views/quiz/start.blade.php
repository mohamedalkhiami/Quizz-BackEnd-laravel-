@extends('layout')

@section('title', 'Start QUIZ')


@section('content')

<?php 

$quiz = \App\Quiz::find($id);

$questions = \App\Question::where('quiz_id', $id)->orderBy('qn_no', 'ASC')->get();

$i = 1;

?>

@if(session()->has('error'))
<div class="alert alert-danger flush">
	<i class="fa fa-ban"></i> {{session()->get('error')}}
</div>
<script src="{{url('js/jquery.min.js')}}"></script>
<script type="text/javascript">
	$('.flush').delay(5000).fadeOut();
</script>
@endif

<div class="page-class ml-4">
	<h3>Quiz</h3>
	<hr/>
		<form id="quizAttemptForm">
				@foreach($questions as $q)
				<fieldset class="mb-3">
					<h5 class="d-flex"><span class="question-number">{{$q->qn_no < 10 ? '0'.$q->qn_no : $q->qn_no}}.</span> <span>{{$q->question}}</span></h5>
					<div class="answers-option ml-5 ">
						<?php
							$answers = \App\Answer::where('question_id', $q->id)->get();
							$ch = $q->qn_no < 10 ? '0'.$q->qn_no : $q->qn_no;
						?>
						@foreach($answers as $a)

						<?php $checked = $a->correct == 0 ? '' : 'checked="true"'; ?>

						@if($q->category == "single")
						<div class="custom-control custom-radio">
							<input type="radio" id="question{{$ch}}_answer{{$a->id}}" name="customRadio{{$ch}}" class="custom-control-input answerAttempted" aid="{{$a->id}}" qid="{{$q->id}}" />
							<label class="custom-control-label" for="question{{$ch}}_answer{{$a->id}}">{{$a->answer}}</label>
						</div>
						@endif
						@if($q->category == "multiple")
							<div class="custom-control custom-checkbox">
							<input type="checkbox" class="custom-control-input answerAttempted" aid="{{$a->id}}" qid="{{$q->id}}" id="question{{$ch}}_answer{{$a->id}}">
							<label class="custom-control-label" for="question{{$ch}}_answer{{$a->id}}">{{$a->answer}}</label>
						</div>
						@endif
						@endforeach
					</div>
					@if($q->qn_photo_location != "")
						<br/>
						<img src="{{$q->qn_photo_location}}" style="width: 480px" />
					@endif

				</fieldset>
				<br/>
				@endforeach
				
				<button type="button" id="submitQuizAttempts" class="btn btn-primary"> Submit Quiz</button>

				<br/>
				<hr/>
		</form>
</div>

@endsection


@section('scripts')
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
				_token : '{{csrf_token()}}',
				attempts: attempts,
				user_id: '{{auth()->user()->id}}',
				quiz_id: '{{$id}}'				
			}

			$.post('{{route("quiz.attempt")}}', data, function(res) {
				if(res.error) {
					swal({
					  title: "Error",
					  text: res.msg,
					  type: "error",
					  buttons: true
					}, function() {
						window.location = '{{route("app.dashboard")}}';
					});
					
				}else {
					window.location = '{{route("quiz.attempt.refresh")}}';
				}
				
			});
			
		});		

		
		
	});
});
</script>
@endsection