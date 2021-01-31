@extends('layout')

@section('title', 'Quiz Staff')

@section('content')


<article>
<div class="modal fade" id="userModalQuizResults" data-backdrop="static" data-keyboard="false" tabindex="-1" role="dialog" aria-labelledby="userModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
        <div class="modal-content">
            <div style="padding:10px">
                    <center>
                        <img src="{{url('images/loader.gif')}}" id="loaderRex" />
                    </center>
                    <div id="quizResxEditor"></div>
                </div>
        </div>
    </div>
</div>
</article>


                <!-- CONTENT BLOCK HERE -->
                

					<div class="page-class">
						<div class="row">
							<div class="col-md-7 mx-auto">
								<table id="dataTable" class="table table-striped table-bordered">
									<thead>
										<tr>
											<th>Quiz Name</th>
											<th>Score</th>
											<th>Ranked</th>
											<th>View Qestions</th>
										</tr>
									</thead>
									<tbody>
										 <?php 
							                $quizes = \App\Quiz::where('status', 1)->get();
							                $i = 0;

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

							               

							            ?>
							            @if(count($quizes))
            							@foreach($quizes as $q)

            							<?php

							                $c = \App\Quizfeedback::where('user_id', auth()->user()->id)->where('quiz_id', $q->id)->where('seen', 1)->get();
							            
							            ?>

            							<?php
 											$r = 1;

							                foreach (\App\User::where('role_id', 2)->get() as $u) {
												$rank = \DB::table('answers')->join('attempts', 'attempts.aid', '=', 'answers.id')
													->select('*')
													->where('attempts.quiz_id', $q->id)->where('answers.correct', 1)
													->where('attempts.user_id', $u->id)
													->count();
												$ranks[$u->id] = $r;	
												$r++;	
											}

											arsort($ranks);

            							?>
            							@if(count($c))
										<tr>
											<td>{{$q->quiz_name}}</td>
											<td>{{myscore($q->id)}} out of {{ansx($q->id)}}</td>
											<td>{{$ranks[auth()->user()->id]}}/{{\App\User::where('role_id', 2)->count()}}</td>
											<td><button class="btn btn-primary btn-sm viewResultx" data-toggle="modal" route="{{route('quiz.results.seen', $q->id)}}" data-target="#userModalQuizResults">View Results</button></td>
										</tr>
										@endif
										@endforeach
										@endif
									</tbody>
								</table>
							</div>
						</div>
					</div>

            

@endsection

@section('scripts')
        <script src="{{url('js/jquery.min.js')}}"></script>
        <script src="{{url('js/bootstrap.bundle.min.js')}}"></script>
        <script src="{{url('js/matchHeight.min.js')}}"></script>
        <script src="{{url('js/nprogress.min.js')}}"></script>
        <script src="{{url('js/custom.min.js')}}"></script>
        

		<script src="{{url('js/jquery.dataTables.min.js')}}"></script>
		<script src="{{url('js/dataTables.bootstrap4.min.js')}}"></script>
		<script src="{{url('js/dataTables.buttons.min.js')}}"></script>
		<script src="{{url('js/buttons.bootstrap4.min.js')}}"></script>
		<script src="{{url('js/buttons.html5.min.js')}}"></script>
		<script src="{{url('js/buttons.flash.min.js')}}"></script>
		<script src="{{url('js/buttons.print.min.js')}}"></script>
		<script>
			$(document).ready(function () {

				$('body').on('click', '.viewResultx', function() {
			        var route = $(this).attr('route');
			        $('#loaderRex').show();
			        $('#quizResxEditor').html('');
			        $.get(route, function(res) {
			            $('#loaderRex').hide();
			            $('#quizResxEditor').html(res);
			        });
			    });


				
				var table = $('#dataTable').DataTable();


				$('#userModal').on('show.bs.modal', function (event) {
					var button = $(event.relatedTarget) // Button that triggered the modal
					var user_name = button.data('user_name') // Extract info from data-* attributes
					var user_email = button.data('user_email') // Extract info from data-* attributes
					// If necessary, you could initiate an AJAX request here (and then do the updating in a callback).
					// Update the modal's content. We'll use jQuery here, but you could use a data binding library or other methods instead.
					var modal = $(this)
					modal.find('.quiz-name-title').text(user_name)
					modal.find('.modal-body input#user-name').val(user_name)
					modal.find('.modal-body input#user-email').val(user_email)
				});
			});
		</script>
@endsection