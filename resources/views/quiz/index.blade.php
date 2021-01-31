@extends('layout')

@section('title', 'Manage Quiz')

@section('content')

@if(session()->has('success'))
<div class="alert alert-success flush">
    <i class="fa fa-check"></i> {{session()->get('success')}}
</div>
<script src="{{url('js/jquery.min.js')}}"></script>
<script type="text/javascript">
    $('.flush').delay(5000).fadeOut();
</script>
@endif

<div class="page-class">
	<button type="button" class="btn btn-primary" data-toggle="modal" data-target="#addQuizModal"><i class="fa fa-plus"></i> Add New Quiz</button>
	<div class="mt-3">
		<table id="dataTable_quiz" class="table table-striped table-bordered">
			<thead>
				<tr>
					<th>#</th>
					<th>Quiz Name</th>
					<th>No. of Questions</th>
					<th>No. of Questions Added</th>
                    <th>Creation Status</th>
                    <th>Quiz Published ?</th>
                    <th>Quiz Results Published ?</th>
					<th>Manage Quiz</th>
					<th>Quiz Questions</th>
					<th>Quiz Report</th>
				</tr>
			</thead>
			<tbody>
                <?php
                    $quizes = \App\Quiz::orderBy('id', 'DESC')->get();
                    $i = 1;
                ?>
                @foreach($quizes as $q)
				<tr>
					<td>{{$i}}</td>
					<td>{{$q->quiz_name}}</td>
					<td>{{$q->questions_no}}</td>
                    <td>{{\App\Question::where('quiz_id', $q->id)->count()}}</td>
                    <td>
                    	@if($q->questions_no == \App\Question::where('quiz_id', $q->id)->count())
                    		<span class=" alert-success">Completed</span>
                    	@else
                    		<span class=" alert-warning">In-Completed</span>
                    	@endif
                    	
                    </td>
					<td>{!!$q->status == 1 ? '<label class="alert-success">YES</label>' : '<label class="alert-danger">NO</label>'!!}</td>
					<td>
						@if(\App\Quizfeedback::where('quiz_id', $q->id)->where('published',1)->count() > 0)
							<label class="alert-success">YES</label>
						@elseif(\App\Quizfeedback::where('quiz_id', $q->id)->where('published',0)->count() > 0)
							<label class="alert-danger">NO</label>
						@else
							<label>--</label>
						@endif

					</td>
					<td>
						<button 
							class="btn btn-primary btn-sm editQuiz" 
                            data-toggle="modal" 
                            route="{{route('quiz.edit', $q->id)}}"
							data-target="#quizModal" 
							data-quiz_questions_number="5">
							<i class="fa fa-edit"></i> Edit Quiz
						</button>
					</td>
					<td>
						@if($q->questions_no == \App\Question::where('quiz_id', $q->id)->count())
							<!-- @if($q->status == 0)
                    		<button class="btn btn-success btn-sm">PUBLISH NOW</button>
                    		@else
                    		<button class="btn btn-danger btn-sm">UNPUBLISH NOW</button>
                    		@endif -->
                    	@else
                    		<button 
                            class="btn btn-primary btn-sm addQns" 
                            route="{{route('quiz.add.questions', $q->id)}}"
							data-toggle="modal" 
							data-target="#questionModal" 
							><i class="fa fa-plus"></i> Add Questions
						</button>
                    	@endif
						
						<button 
							class="btn btn-danger btn-sm previewQuiz" 
							data-toggle="modal" 
							route="{{route('quiz.preview.questions', $q->id)}}"
							data-target="#viewQuestionModal" 
							><i class="fa fa-list"></i> View Questions
						</button>
					</td>
					<td>
						<button 
							class="btn btn-primary btn-sm reportQuiz" 
							data-toggle="modal" 
							route="{{route('quiz.report', $q->id)}}"
							data-target="#reportModal" 
							>View Report
						</button>
					</td>
                </tr>
                <?php $i++; ?>
                @endforeach
			</tbody>
		</table>
	</div>
</div>
<!-- Add Quiz Modal -->
<article>
	<div class="modal fade" id="addQuizModal" tabindex="-1" role="dialog" aria-labelledby="addQuizModalLabel" aria-hidden="true">
		<div class="modal-dialog modal-dialog-centered " role="document">
			<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title text-center" id="addQuizModalLabel">Add New Quiz</h5>
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">&times;</span>
					</button>
				</div>
				<form action="{{route('quiz.store')}}" method="POST" id="addQuizForm" onsubmit="return processAddQuiz()">
					<div class="modal-body">
                        {{csrf_field()}}
						<div class="form-group">
							<label for="add-quiz-name" class="col-form-label">Quiz Name:</label>
                            <input type="text" name="quiz_name" class="validate[required] form-control" id="add-quiz-name"
                            data-errormessage-value-missing="Quiz name is required!"
                            />
						</div>
						<div class="form-group">
							<label for="add-quiz-questions-number" class="col-form-label">Number Of Questions:</label>
                            <input type="number" name="quiz_no_questions" class="validate[required,custom[integer]] form-control" id="add-quiz-questions-number"
                            data-errormessage-value-missing="Number of Questions is required!" 
	                        data-errormessage="Should be Number"
                            />
						</div>
						<!-- <div class="form-group">
							<label for="user-email" class="col-form-label d-block">Quiz Status:</label>
							<div class="custom-control custom-radio custom-control-inline">
								<input type="radio" id="add-quiz-active" name="add_quiz_status" value="1" class="custom-control-input">
								<label class="custom-control-label" for="add-quiz-active">Active</label>
							</div>
							<div class="custom-control custom-radio custom-control-inline">
								<input type="radio" id="add-quiz-inactive" name="add_quiz_status" value="0" class="custom-control-input" checked>
								<label class="custom-control-label" for="add-quiz-inactive">Inactive</label>
							</div>
						</div> -->
						<div class="form-group">
							<label for="add-quiz-description">Quiz Description</label>
							<textarea class="form-control" name="quiz_description" id="add-quiz-description" rows="3"></textarea>
						</div>
					</div>
					<div class="modal-footer">
						<button type="submit" class="btn btn-primary">Submit Quiz</button>
					</div>
				</form>
			</div>
		</div>
	</div>
</article>
<!-- Edit Quiz Modal -->
<article>
	<div class="modal fade" id="quizModal" tabindex="-1" role="dialog" aria-labelledby="quizModalLabel" aria-hidden="true">
		<div class="modal-dialog  modal-dialog-centered " role="document">
			<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title" id="quizModalLabel"><i class="fa fa-edit"></i> Edit Quiz Information</h5>
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">&times;</span>
					</button>
				</div>
				<div style="padding:10px">
					<center>
						<img src="{{url('images/loader.gif')}}" id="loader" />
					</center>
					<div id="quizEditor"></div>
				</div>
			</div>
		</div>
	</div>
</article>
<!-- View Question Modal -->
<article>
<div class="modal fade" id="viewQuestionModal" tabindex="-1" role="dialog" aria-labelledby="viewQuestionModalLabel"
 aria-hidden="true">
	<div class="modal-dialog modal-sm modal-dialog-centered modal-lg" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="viewQuestionModalLabel"><i class="fa fa-list"></i> View Quiz</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div style="padding:10px">
					<center>
						<img src="{{url('images/loader.gif')}}" id="loaderQuizV" />
					</center>
					<div id="quizPreview"></div>
				</div>
			
		</div>
	</div>
</div>
</article>
<!-- Add Question Modal -->
<article>
	<div class="modal fade" data-backdrop="static" data-keyboard="false" id="questionModal" tabindex="-1" role="dialog" aria-labelledby="questionModalLabel" aria-hidden="true">
		<div class="modal-dialog modal-dialog-centered " role="document">
			<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title" id="questionModalLabel"><i class="fa fa-plus"></i>  Add Quiz Questions</h5>
					<button type="button" onclick="window.location=''" class="close" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">&times;</span>
					</button>
				</div>
				
				<div style="padding:10px">
					<center>
						<img src="{{url('images/loader.gif')}}" id="loaderQns" />
					</center>
					<div id="qnsEditor"></div>
				</div>
			</div>
		</div>
	</div>
</article>
<!-- Report Modal -->
<article>
	<div class="modal fade" id="reportModal" tabindex="-1" role="dialog" aria-labelledby="reportModalLabel" aria-hidden="true">
		<div class="modal-dialog modal-dialog-centered modal-lg" role="document">
			<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title text-center" id="reportModalLabel"><i class="fa fa-file"></i> View Report</h5>
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">&times;</span>
					</button>
				</div>
				<div class="modal-body">
					<div style="padding:10px">
					<center>
						<img src="{{url('images/loader.gif')}}" id="loaderRep" />
					</center>
					<div id="repEditor"></div>
				</div>
				</div>
				
			</div>
		</div>
	</div>
</article>
@endsection


@section('scripts')

<script>
function processAddQuiz() {
    return $("#addQuizForm").validationEngine('validate');
}
</script>

<script src="{{url('js/jquery.dataTables.min.js')}}"></script>
<script src="{{url('js/dataTables.bootstrap4.min.js')}}"></script>
<script src="{{url('js/dataTables.buttons.min.js')}}"></script>
<script src="{{url('js/buttons.bootstrap4.min.js')}}"></script>
<script src="{{url('js/buttons.html5.min.js')}}"></script>
<script src="{{url('js/buttons.flash.min.js')}}"></script>
<script src="{{url('js/buttons.print.min.js')}}"></script>

<script>



	$(document).ready(function () {


		$('body').on('click', '#attachPhoto', function() {
			$("#attachedPhoto").click();
		});


		function getQNEditor(route, qn, quizid) {

			

			var data = {
				_token: '{{csrf_token()}}',
				quizid: quizid
			}

			$('#qn' + qn).css('opacity', 0.2).css('backgroundColor', '');

		  	Biggo.talkToServer(route, data).then(function(res){
				$('#qn' + qn).css('opacity', 1).css('backgroundColor', '#f5f5f5');
				$('#qn' + qn).html(res);
			});
		}

		$('body').on('click', '.deleteEditAnwr', function() {

			var aid    = $(this).attr('aid');
			var qn     = $(this).attr('qn');
			var quizid = $(this).attr('quizid');
			var route  = $(this).attr('route');
			var routeEd  = $(this).attr('routeEd');

			swal({
			  title: "You are about to delete question answer!",
			  text: "",
			  type: "info",
			  showCancelButton: true,
			  closeOnConfirm: true,
			  showLoaderOnConfirm: false
			}, function () {
				
				var data = {
					_token: '{{csrf_token()}}'
				}

				$('#answerDel' + aid).css('opacity', 0.2)
			  	Biggo.talkToServer(route, data).then(function(res){
					getQNEditor(routeEd, qn, quizid)
				});
			});
		});

		$('body').on('click', '.editRadioAnswer', function() {



		});

		$('body').on('click', '.reportQuiz', function() {
			var route = $(this).attr('route');
			$('#loaderRep').show();
			$('#repEditor').html('');
			$.get(route, function(res) {
				$('#loaderRep').hide();
				$('#repEditor').html(res);
			});
		});


		$('body').on('click', '.previewQuiz', function() {
			var route = $(this).attr('route');
			$('#loaderQuizV').show();
			$('#quizPreview').html('');
			$.get(route, function(res) {
				$('#loaderQuizV').hide();
				$('#quizPreview').html(res);
			});
		});

		$('body').on('click', '.updateQn', function() {
			var route     = $(this).attr('route');
			alert(route);
			var qn        = $(this).attr('qn');
			var quizid    = $(this).attr('quizid');
			var formdata  = $('#updateQnForm').serializeArray();
			alert(formdata)
			var data = {
				_token: '{{csrf_token()}}',
				quizid: quizid,
				qndata: formdata
			};

			$('#qn' + qn).css('opacity', 0.2).css('backgroundColor', '');

		  	Biggo.talkToServer(route, data).then(function(res){
				$('#qn' + qn).css('opacity', 1).css('backgroundColor', '#f5f5f5');
				$('#qn' + qn).html(res);
			});
		});

		$('body').on('click', '.editQn', function() {
			
			var route     = $(this).attr('route');

 			

			var qn        = $(this).attr('qn');
			var quizid    = $(this).attr('quizid');
			
			var data = {
				_token: '{{csrf_token()}}',
				quizid: quizid
			}

			$('#qn' + qn).css('opacity', 0.2).css('backgroundColor', '');

		  	Biggo.talkToServer(route, data).then(function(res){
				$('#qn' + qn).css('opacity', 1).css('backgroundColor', '#f5f5f5');
				$('#qn' + qn).html(res);
			});



		});


		$('body').on('click', '.cancelQn', function() {

			var route     = $(this).attr('route');
			var qn        = $(this).attr('qn');
			var quizid    = $(this).attr('quizid');
			

			var data = {
				_token: '{{csrf_token()}}',
				quizid: quizid
			}


			$('#qnx').css('opacity', 0.2);
			$('#qn' + qn).css('backgroundColor', '');

		  	Biggo.talkToServer(route, data).then(function(res){
				$('#qnx').css('opacity', 1).css('backgroundColor', '');
				$('#qn' + qn).html(res);
			});



		});


		$('body').on('click', '.deleteQn', function() {
			var route = $(this).attr('route');
			swal({
			  title: "You are about to delete question!",
			  text: "",
			  type: "info",
			  showCancelButton: true,
			  closeOnConfirm: false,
			  showLoaderOnConfirm: true
			}, function () {
				var data = {
					_token: '{{csrf_token()}}'
				}
			  	Biggo.talkToServer(route, data).then(function(res){
					alert(333)
				});
			});
		});

		$('body').on('click', '#publishUnQuiz', function() {
			var route = $(this).attr('route');
			swal({
			  title: "You are about to unpublish Quiz!",
			  text: "",
			  type: "info",
			  showCancelButton: true,
			  closeOnConfirm: false,
			  showLoaderOnConfirm: true
			}, function () {
				var data = {
					_token: '{{csrf_token()}}'
				}
			  	Biggo.talkToServer(route, data).then(function(res){
					  window.location = '{{route("quiz.refresh")}}';
				});
			});
		});

		$('body').on('click', '#publishQuiz', function() {
			var route = $(this).attr('route');
			swal({
			  title: "You are about to publish Quiz!",
			  text: "",
			  type: "info",
			  showCancelButton: true,
			  closeOnConfirm: false,
			  showLoaderOnConfirm: true
			}, function () {
				var data = {
					_token: '{{csrf_token()}}'
				}
			  	Biggo.talkToServer(route, data).then(function(res){
					  window.location = '{{route("quiz.refresh")}}';
				});
			});
		});

		var i = 0;

		$('body').on('change', '#answer-type', function() {
			var self = $(this).val();
			if(self !== "") {
				i = 0;
				$('#answersArea').html('')
			}
			$('#answersArea').html('')
		});

		$('body').on('dblclick', '.removeAnswer', function() {
			$(this).parent().remove();
		});


		$('body').on('click', '#addAnswer', function() {
			 var answerType = $('#answer-type').val();
			 if(answerType === ""){
			 	Biggo.showFeedBack(addQuestionForm, 'Please select answer type first', true);
			 	return;
			 }

			 var answerBody = $('#answerBody').val();
			 if(answerBody === "") {
			 	Biggo.showFeedBack(addQuestionForm, 'Please provide answer!', true);
			 	return;
			 }



			 if (answerType === "single") {
			 	i++;
			 	var add_answer_input = $('input.add-answer-input').val();
				$("#answersArea").append('<div  class="custom-control custom-radio"><input type="radio" id="customRadio'+i+'" name="customRadio" abody="'+add_answer_input+'" class="custom-control-input singleAnswerItem"><label class="custom-control-label removeAnswer" title="Double click to remove from list" style="cursor: pointer" for="customRadio'+i+'">'+add_answer_input+'</label></div><hr/>');
				$('input.add-answer-input').val('');
			 	
			 }else {
			 	i++;
				var add_answer_input = $('input.add-answer-input').val();
				$("#answersArea").append('<div  class="custom-control custom-checkbox"><input type="checkbox" abody="'+add_answer_input+'" class="custom-control-input multipleAnswerItem" id="customCheck'+i+'"><label class="custom-control-label removeAnswer" title="Double click to remove from list" style="cursor: pointer" for="customCheck'+i+'">'+add_answer_input+'</label></div><hr/>');
				$('input.add-answer-input').val('');
			 }

			 var antype = $('#question').val();

			 if(antype !== "") {
			 	$('#saveQn, #saveAndContQn').prop('disabled', false)
			 }
			 
		});

        $('body').on('click', '.addQns', function() {
            var route = $(this).attr('route');
            $('#loaderQns').show();
            $('#qnsEditor').html('');
            $.get(route, function(res) {
                $('#loaderQns').hide();
                $('#qnsEditor').html(res);
            });
        });

        $('body').on('click', '.editQuiz', function() {
            var route = $(this).attr('route');
            $('#loader').show();
            $('#quizEditor').html('');
            $.get(route, function(res) {
                $('#loader').hide();
                $('#quizEditor').html(res);
            });
        });

        $('body').on('click', '#saveAndContQn', function() {
        	var qn = $('#question').val();
        	if(qn === "") {
        		Biggo.showFeedBack(addQuestionForm, 'Please provide question body', true);
			 	return;
        	}
        	var answerType = $('#answer-type').val();
			 if(answerType === ""){
			 	Biggo.showFeedBack(addQuestionForm, 'Please select answer type first', true);
			 	return;
			 }else {
			 	if(answerType === "single") {
			 		var c = 0;
			 		var t = 1;
			 		var anyChecked = false;
			 		var answers = [];
			 		$('.singleAnswerItem').each(function(i, k) {
			 			var v = $('#customRadio' + t).prop('checked');
			 			if (v) {
			 				var answ = {
			 					body: $('#customRadio' + t).attr('abody'),
			 					correct: true
			 				};
			 				anyChecked = true;
			 				answers.push(answ)
			 			}else {
			 				var answ = {
			 					body: $('#customRadio' + t).attr('abody'),
			 					correct: false
			 				};
			 				answers.push(answ)
			 			}	
			 			c++;
			 			t++;
			 		});
			 		if( c <= 1) {
			 			Biggo.showFeedBack(addQuestionForm, 'Please add more answer', true);
			 			return;
			 		}
			 		if(anyChecked === false) {
			 			Biggo.showFeedBack(addQuestionForm, 'Please provide answer before saving!', true);
			 			return;
			 		}

			 		// Everything is fine here!

			 		$('#addQuestionFormControls').css('opacity', '0.2');
			 		$('#saveQn').prop('disabled', true);
			 		$('#saveAndContQn').css('cursor', 'wait');


			 		var isFileUpload = false;
		            var formdata;
		            
		            if(Biggo.isFileValueSetted(attachedPhoto) != undefined){
		                var arr  = Biggo.serializeData(addQuestionForm);
		                arr.push({name: "answers", value: JSON.stringify(answers)});
			 		    arr.push({name: "_token", value: '{{csrf_token()}}'})
		                var arr2 = ["attachedPhoto"];
		                isFileUpload = true;
		                formdata = Biggo.prepareFormData(arr, arr2);
		            }else{
		                formdata = Biggo.serializeData(addQuestionForm);
		                formdata.push({name: "_token", value: '{{csrf_token()}}'});
		                formdata.push({name: "answers", value: JSON.stringify(answers)});
		            }

			 		

			 		var route = $('#saveAndContQn').attr('route');

			 		


			 		var biggo = Biggo.talkToServer(route, formdata, isFileUpload);

			 		$('#loaderQns').show();
            		$('#qnsEditor').html('');
			 		biggo.then(function(res) {
			 			// if(res.refresh){
			 			// 	window.location = "";
			 			// }
			 			// $('#loaderQns').hide();
            			// $('#qnsEditor').html(res);
						window.location = "";
			 		});

			 	}else if(answerType === "multiple") {
			 		var c = 0;
			 		var t = 1;
			 		var anyChecked = false;
			 		var answers = [];
			 		$('.multipleAnswerItem').each(function(i, k) {
			 			var v = $('#customCheck' + t).prop('checked');
			 			if (v) {
			 				var answ = {
			 					body: $('#customCheck' + t).attr('abody'),
			 					correct: true
			 				};
			 				anyChecked = true;
			 				answers.push(answ)
			 			}else {
			 				var answ = {
			 					body: $('#customCheck' + t).attr('abody'),
			 					correct: false
			 				};
			 				answers.push(answ)
			 			}
			 			c++;
			 			t++;
			 		});
			 		if( c <= 1) {
			 			Biggo.showFeedBack(addQuestionForm, 'Please add more answer', true);
			 			return;
			 		}
			 		if(anyChecked === false) {
			 			Biggo.showFeedBack(addQuestionForm, 'Please provide answer before saving!', true);
			 			return;
			 		}

			 		// Everything is fine here!

			 		$('#addQuestionFormControls').css('opacity', '0.2');
			 		$('#saveQn').prop('disabled', true);
			 		$('#saveAndContQn').css('cursor', 'wait');


			 		var isFileUpload = false;
		            var formdata;
		            
		            if(Biggo.isFileValueSetted(attachedPhoto) != undefined){
		                var arr  = Biggo.serializeData(addQuestionForm);
		                arr.push({name: "answers", value: JSON.stringify(answers)});
			 		    arr.push({name: "_token", value: '{{csrf_token()}}'})
		                var arr2 = ["attachedPhoto"];
		                isFileUpload = true;
		                formdata = Biggo.prepareFormData(arr, arr2);
		            }else{
		                formdata = Biggo.serializeData(addQuestionForm);
		                formdata.push({name: "_token", value: '{{csrf_token()}}'});
		                formdata.push({name: "answers", value: JSON.stringify(answers)});
		            }

			 		var route = $('#saveAndContQn').attr('route');
			 		var biggo = Biggo.talkToServer(route, formdata, isFileUpload);

			 		$('#loaderQns').show();
            		$('#qnsEditor').html('');
			 		biggo.then(function(res) {
			 			// if(res.refresh){
			 			// 	window.location = "";
			 			// }
			 			// $('#loaderQns').hide();
            			// $('#qnsEditor').html(res);
						window.location = "";
			 		});
			 	}
			 }
        });

        $('body').on('click', '#saveQn', function() {

        	var qn = $('#question').val();
        	if(qn === "") {
        		Biggo.showFeedBack(addQuestionForm, 'Please provide question body', true);
			 	return;
        	}

        	var answerType = $('#answer-type').val();
			 if(answerType === ""){
			 	Biggo.showFeedBack(addQuestionForm, 'Please select answer type first', true);
			 	return;
			 }else {
			 	if(answerType === "single") {
			 		var c = 0;
			 		var t = 1;
			 		var anyChecked = false;
			 		var answers = [];
			 		$('.singleAnswerItem').each(function(i, k) {
			 			var v = $('#customRadio' + t).prop('checked');
			 			if (v) {
			 				var answ = {
			 					body: $('#customRadio' + t).attr('abody'),
			 					correct: true
			 				};
			 				anyChecked = true;
			 				answers.push(answ)
			 			}else {
			 				var answ = {
			 					body: $('#customRadio' + t).attr('abody'),
			 					correct: false
			 				};
			 				answers.push(answ)
			 			}	
			 			c++;
			 			t++;
			 		});
			 		if( c <= 1) {
			 			Biggo.showFeedBack(addQuestionForm, 'Please add more answer', true);
			 			return;
			 		}
			 		if(anyChecked === false) {
			 			Biggo.showFeedBack(addQuestionForm, 'Please provide answer before saving!', true);
			 			return;
			 		}

			 		// Everything is fine here!

			 		$('#addQuestionFormControls').css('opacity', '0.2');
			 		$('#saveAndContQn').prop('disabled', true);
			 		$('#saveQn').css('cursor', 'wait');


			 		var isFileUpload = false;
		            var formdata;
		            
		            if(Biggo.isFileValueSetted(attachedPhoto) != undefined){
		                var arr  = Biggo.serializeData(addQuestionForm);
		                arr.push({name: "answers", value: JSON.stringify(answers)});
			 		    arr.push({name: "_token", value: '{{csrf_token()}}'})
		                var arr2 = ["attachedPhoto"];
		                isFileUpload = true;
		                formdata = Biggo.prepareFormData(arr, arr2);
		            }else{
		                formdata = Biggo.serializeData(addQuestionForm);
		                formdata.push({name: "_token", value: '{{csrf_token()}}'});
		                formdata.push({name: "answers", value: JSON.stringify(answers)});
		            }

			 		var route = $('#saveQn').attr('route');
			 		var biggo = Biggo.talkToServer(route, formdata, isFileUpload);

			 		biggo.then(function(res) {
			 			if(!res.error){
			 				window.location = '{{route("quiz.refresh")}}';
			 			}
			 		});

			 	}else if(answerType === "multiple") {
			 		var c = 0;
			 		var t = 1;
			 		var anyChecked = false;
			 		var answers = [];
			 		$('.multipleAnswerItem').each(function(i, k) {
			 			var v = $('#customCheck' + t).prop('checked');
			 			if (v) {
			 				var answ = {
			 					body: $('#customCheck' + t).attr('abody'),
			 					correct: true
			 				};
			 				anyChecked = true;
			 				answers.push(answ)
			 			}else {
			 				var answ = {
			 					body: $('#customCheck' + t).attr('abody'),
			 					correct: false
			 				};
			 				answers.push(answ)
			 			}
			 			c++;
			 			t++;
			 		});
			 		if( c <= 1) {
			 			Biggo.showFeedBack(addQuestionForm, 'Please add more answer', true);
			 			return;
			 		}
			 		if(anyChecked === false) {
			 			Biggo.showFeedBack(addQuestionForm, 'Please provide answer before saving!', true);
			 			return;
			 		}

			 		// Everything is fine here!

			 		// Everything is fine here!

			 		$('#addQuestionFormControls').css('opacity', '0.2');
			 		$('#saveAndContQn').prop('disabled', true);
			 		$('#saveQn').css('cursor', 'wait');

			 		var isFileUpload = false;
		            var formdata;
		            
		            if(Biggo.isFileValueSetted(attachedPhoto) != undefined){
		                var arr  = Biggo.serializeData(addQuestionForm);
		                arr.push({name: "answers", value: JSON.stringify(answers)});
			 		    arr.push({name: "_token", value: '{{csrf_token()}}'})
		                var arr2 = ["attachedPhoto"];
		                isFileUpload = true;
		                formdata = Biggo.prepareFormData(arr, arr2);
		            }else{
		                formdata = Biggo.serializeData(addQuestionForm);
		                formdata.push({name: "_token", value: '{{csrf_token()}}'});
		                formdata.push({name: "answers", value: JSON.stringify(answers)});
		            }


			 		//console.log(formdata);

			 		var route = $('#saveQn').attr('route');
			 		var biggo = Biggo.talkToServer(route, formdata);

			 		biggo.then(function(res) {
			 			if(!res.error){
			 				window.location = '{{route("quiz.refresh")}}';
			 			}
			 		});
			 	}
			 }

			 
        })

        $('body').on('click', '#updateQuiz', function() {
            var valid = $('#editQuizForm').validationEngine('validate');
            var route = $(this).attr('route');

			var refreshURL = $(this).attr('refreshURL')
			if(valid) {
				
				$("#editQuiz").css('opacity', 0.2);
				$(this).prop('disabled', true);
				$(this).css('cursor', 'wait');
				var data = $("#editQuizForm").serializeArray();

				Biggo.talkToServer(route, data).then(function(res){

					$('#updateQuiz').prop('disabled', false);
					$('#updateQuiz').css('cursor', '');
					$("#editQuiz").css('opacity', 1);
					if(res.error) {
						Biggo.showFeedBack(userEditForm, res.msg, res.error);
					}

					window.location = refreshURL;
					
					
				});
				
			}
        });

		var table_quiz = $('#dataTable_quiz').DataTable();
		
		$('#reportModal').on('show.bs.modal', function (event) {
			var table_report = $('#dataTable_report').DataTable();
			
			$('#reportModal').on('hide.bs.modal', function () {
				table_report.destroy();
			});
		});
		
		// $('#reportModal').on('hide.bs.modal', function (event) {
		// 	table_report.destroy();
		// });


		/***************************************** Quiz Modal ************************************/
		$('#quizModal').on('show.bs.modal', function (event) {
			let button = $(event.relatedTarget) // Button that triggered the modal
			let quiz_name = button.data('quiz_name') // Extract info from data-* attributes
			let quiz_questions_number = button.data('quiz_questions_number') // Extract info from data-* attributes
			// If necessary, you could initiate an AJAX request here (and then do the updating in a callback).
			// Update the modal's content. We'll use jQuery here, but you could use a data binding library or other methods instead.
			let modal = $(this);
			modal.find('.modal-title').text(quiz_name);
			modal.find('.modal-body input#quiz-name').val(quiz_name);
			modal.find('.modal-body input#quiz-questions-number').val(quiz_questions_number);
		});

		/*****************************************MultiPhase Form************************************/
        $('.multiphase fieldset.setup-content:first-child').fadeIn('slow');
        let nextStep = $('button[type="button"].nextBtn'),
            prevStep = $('.prevBtn');

        // Continue to next step
        nextStep.on('click', function () {
            let parent_fieldset = $(this).parents('fieldset.setup-content'),
                next_step = true;
            if (next_step) {
                parent_fieldset.fadeOut(400, function () {
                    $(this).next().fadeIn();
                });
            }
        });

        // Back to previous Step
        prevStep.on('click', function () {
            $(this).parents('fieldset.setup-content').fadeOut(400, function () {
                $(this).prev().fadeIn();
            });
        });
		/***************************************** End MultiPhase Form ************************************/

		/************************************* Select Radio Or Checkbox *******************************/
		$('#questionModal').on('shown.bs.modal', function () {
			$('.answer-type').change(function () {
				let i = 0;
				let answer_type = '';
				answer_type = $(this).val();
				console.log(answer_type);
				if (answer_type == "single") {
					$('.add-answer-button').on('click', function () {
						i++;
						let add_answer_input = $('input.add-answer-input').val();
						$(".input-add").append('<div class="custom-control custom-radio"><input type="radio" id="customRadio'+i+'" name="customRadio" class="custom-control-input"><label class="custom-control-label" for="customRadio'+i+'">'+add_answer_input+'</label></div>');
					});
				} 
				else if (answer_type == "multiple") {
					$('.add-answer-button').on('click', function () {
						i++;
						let add_answer_input = $('input.add-answer-input').val();
						$(".input-add").append('<div class="custom-control custom-checkbox"><input type="checkbox" class="custom-control-input" id="customCheck'+i+'"><label class="custom-control-label" for="customCheck'+i+'">'+add_answer_input+'</label></div>');
					});
				}
			});
		});
		/************************************* End Select Radio Or Checkbox *******************************/
	});
</script>
@endsection