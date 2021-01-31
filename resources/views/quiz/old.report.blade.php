<?php

$attempts = \App\Attempt::where('quiz_id', $id)->get();

$answers    = \DB::table('answers')->join('questions', 'questions.id', '=', 'answers.question_id')->select('*')->where('questions.quiz_id', $id)->where('answers.correct', 1)->count();

$ranks = [];

foreach (\App\User::all()->where('role_id', 2) as $u) {
	$rank = \DB::table('answers')
	->join('attempts', 'attempts.aid', '=', 'answers.id')
		// ->select('*')		
		->where('attempts.user_id', $u->id)
		->where('attempts.quiz_id', $id)
		->where('answers.correct', 1)
		->count();
	$ranks[$rank] = $u->id;		
}

asort($ranks);



?>

<article>
<div class="modal fade" id="userModalQuizResults" data-backdrop="static" data-keyboard="false"  tabindex="-1" role="dialog" aria-labelledby="userModalLabel" aria-hidden="true">
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

@if(count($attempts))

<table id="dataTable_reportx" class="table table-striped table-bordered">
<thead>
	<tr>
		<th>Rank</th>
		<th>Name</th>
		<th>Score</th>
		<th>View Results</th>
	</tr>
</thead>
<tbody>
	<?php $c = 1; ?>
	@foreach($ranks as $k => $v)
	<?php $ur = "quiz/results/seenX/".$id."/" . $v; ?>
	<tr>
		<td>{{$c}}</td>
		<td>{{\App\User::find($v)->name}} ( {{\App\User::find($v)->email}} )</td>
		<td>{{$k}} / {{$answers}} </td>
		<td><button class="btn btn-primary btn-sm viewResultx" data-toggle="modal" route="{{url($ur)}}" data-target="#userModalQuizResults">View Results</button></td>
	</tr>
	<?php $c++; ?>
	@endforeach
	
</tbody>
</table>

<hr/>

<?php

$qf = \App\Quizfeedback::where('quiz_id', $id)->where('published',0)->count();

?>
@if($qf == 0)
<button route="{{route('quiz.publish.results', $id)}}" id="publishResx" class="btn btn-success btn-sm"><i class="fa fa-check"></i> Publish Results Now</button>
@else
<div class="alert alert-warning" ><i class="fa fa-tick"></i> Results were published already </div>
@endif

@else

<div class="alert alert-danger" ><i class="fa fa-ban"></i> No one attempt on this quiz </div>

@endif


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

<script type="text/javascript">
$(function() {

	$('body').on('click', '.viewResultx', function() {
			        var route = $(this).attr('route');
			        $('#loaderRex').show();
			        $('#quizResxEditor').html('');
			        $.get(route, function(res) {
			            $('#loaderRex').hide();
			            $('#quizResxEditor').html(res);
			        });
			    });

$('body').on('click', '#publishResx', function() {
	var route = $(this).attr('route');
	swal({
	  title: "You are about to publish quiz results!",
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

$('#dataTable_reportx').DataTable();
});	 
</script>