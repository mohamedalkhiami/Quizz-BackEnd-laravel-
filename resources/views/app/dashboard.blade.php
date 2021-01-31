@extends('layout')

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

@if(session()->has('error'))
<div class="alert alert-danger flush">
    <i class="fa fa-check"></i> {{session()->get('error')}}
</div>
<script src="{{url('js/jquery.min.js')}}"></script>
<script type="text/javascript">
    $('.flush').delay(5000).fadeOut();
</script>
@endif

@if(session()->has('success'))
<div class="alert alert-success flush">
    <i class="fa fa-check"></i> {{session()->get('success')}}
</div>
<script src="{{url('js/jquery.min.js')}}"></script>
<script type="text/javascript">
    $('.flush').delay(5000).fadeOut();
</script>
@endif


@if(auth()->user()->role_id == 2)
<div class="dashboard">
    <div class="row">
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


                function rankMe($quid) {
                    $r = 1;

                    foreach (\App\User::where('role_id', 2)->get() as $u) {
                        $rank = \DB::table('answers')->join('attempts', 'attempts.aid', '=', 'answers.id')
                            ->select('*')
                            ->where('attempts.quiz_id', $quid)->where('answers.correct', 1)
                            ->where('attempts.user_id', $u->id)
                            ->count();
                        $ranks[$u->id] = $r;    
                        $r++;   
                    }

                    arsort($ranks);

                    return $ranks[auth()->user()->id] . "/" . \App\User::where('role_id', 2)->count();
                }

                

            ?>
            @if(count($quizes))
            @foreach($quizes as $q)
            <?php

               // $c = \App\Quizfeedback::where('user_id', auth()->user()->id)->where('quiz_id', $q->id)->where('seen', 0)->get();
                $c = \App\Quizfeedback::where('quiz_id', $q->id)->where('published',1)->where('seen', 0)->count(); //\App\Quizfeedback::where('user_id', auth()->user()->id)->where('quiz_id', $q->id)->where('seen', 0)->get();

                $c1 = \App\Quizfeedback::where('quiz_id', $q->id)->where('published', 1)->where('user_id', auth()->user()->id)->count();

                $c2 = \App\Attempt::where('quiz_id', $q->id)->where('user_id', auth()->user()->id)->count(); 
            ?>
            @if($c2 == 0)
            <?php $i++; ?>
            <div class="col-md-4">
                <div class="card card-dashboard">
                    <div class="card-header d-flex justify-content-between">
                        <div class="card-icon flex-shrink-0">
                                <i class="fas fa-tasks fa-3x"></i>
                        </div>
                        <div class="card-details">
                            <p class="card-category m-0">{{$q->quiz_name}}</p>
                            <p class="card-category m-0">{{$q->description}}</p>
                            <p class="card-category m-0">Questions: [{{$q->questions_no}}]</p>
                        </div>
                    </div>
                    <div class="card-footer">
                            <i class="fas fa-tasks"></i>
                            <a href="{{route('quiz.start', $q->id)}}">Start Quiz</a>
                    </div>
                </div>
            </div>
            @endif
            @if($c > 0 )
            <?php $i++; ?>
            <div class="col-md-4 ">
                <div class="card card-dashboard alert alert-success">
                    <div class="card-header d-flex justify-content-between">
                        <div class="card-icon flex-shrink-0">
                                <i class="fas fa-trophy fa-3x"></i>
                        </div>
                        <div class="card-details">
                            <p class="card-category m-0">{{$q->quiz_name}}</p>
                            <hr/>
                            <p class="card-category m-0">{{$q->description}}</p>
                            <p class="card-category m-0">Questions: [{{$q->questions_no}}]</p>
                            <p class="card-category m-0 text-danger">Score: [{{myscore($q->id)}}/{{ansx($q->id)}}]</p>
                            <p class="card-category m-0 text-info">Rank: [{{ rankMe($q->id) }}]</p>
                        </div>
                    </div>
                    <div class="card-footer">
                            <i class="fas fa-tasks"></i>
                            <a  style="cursor: pointer;" data-toggle="modal" route="{{route('quiz.results.seen', $q->id)}}" data-target="#userModalQuizResults" class="viewResultx">View Results</a>
                    </div>
                </div>
            </div>
            @endif
            @endforeach
            @else
            <div class="col-md-12 alert alert-warning"><i class="fa fa-file"></i> You dont have any quiz right now!</div>
            @endif
            @if($i == 0) 
            <div class="col-md-12 alert alert-warning"><i class="fa fa-file"></i> Welcome to {{\App\HelperX::appName()}}!</div>
            @endif
    </div>
</div>
@endif

@if(auth()->user()->role_id == 1)
<div class="row">
    <div class="col-lg-3 col-md-6 col-sm-6">
        <div class="card card-dashboard">
            <div class="card-header d-flex justify-content-between">
                <div class="card-icon flex-shrink-0">
                    <i class="fas fa-users fa-3x"></i>
                </div>
                <div class="card-details">
                    <p class="card-category m-0">Total Users</p>
                    <h3 class="card-title mb-0">{{\App\User::where('role_id', '!=', 1)->where('active', 1)->count()}}</h3>
                </div>
            </div>
            <div class="card-footer">
                    <i class="fas fa-user"></i>
                    <a href="{{route('app.users')}}">View Users</a>
            </div>
        </div>
    </div>
    <div class="col-lg-3 col-md-6 col-sm-6">
        <div class="card card-dashboard">
            <div class="card-header d-flex justify-content-between">
                <div class="card-icon flex-shrink-0">
                    <i class="fas fa-newspaper fa-3x"></i>
                </div>
                <div class="card-details">
                    <p class="card-category m-0">Total Quiz</p>
                    <h3 class="card-title mb-0">{{\App\Quiz::count()}}</h3>
                </div>
            </div>
            <div class="card-footer">
                    <i class="fas fa-newspaper"></i>
                    <a href="{{route('app.quiz')}}">View Quiz</a>
            </div>
        </div>
    </div>
</div>
<br/>
<?php 
 $c = \App\User::where('role_id', '!=', 1)->where('verified', 0)->count();
?>
@if($c)
<div class="row">
    <div class="col-md-12">
        <h5 class="alert alert-info" style="display:flex; justify-content: space-between">
        <span>
        <button class="btn btn-danger" id="activateAll" style="display:none">
        <i class="fa fa-check"></i> Active All</button>
        </span>
        <!-- <span>
            <input type="text" class="form-control" style="width: 300px" placeholder="Search User!" />
        </span> -->
        <span>
        <i class="fa fa-users"></i>
        New Users Registered! Wait For Verification ({{\App\User::where('role_id', '!=', 1)->count()}})
        </span>
        
        </h5>
        <hr/>
        
        <table id="dataTable" class="table table-striped table-bordered">
				<thead>
					<tr>
						<th><input type="checkbox" id="actAll" value="all" /></th>
						<th>#</th>
						<th>Name</th>
						<th>Email</th>
                        <th>Status</th>
                        <th>Verified</th>
						<th>Registered At</th>
						<th>Manage</th>
					</tr>
				</thead>
				<tbody>
					<?php 

					$users = \App\User::where('role_id', '!=', 1)->where('active', 0)->orderBy('id','DESC')->get();
					$i = 1;

					?>
					@foreach($users as $u)
					<tr>
                        <td><input type="checkbox" class="checkitem" userid="{{$u->id}}" /></td>
						<td>{{$i}}</td>
						<td>{{$u->name}}</td>
						<td>{{$u->email}}</td>
						<td>{!! $u->active == 1 ? '<label class="label label-success">Active</label>' : '<label class="alert alert-danger">Blocked</label>' !!}</td>
                        <td>{!! $u->verified == 1 ? '<label class="label label-success">YES</label>' : '<label class="alert alert-danger">NO</label>' !!}</td>
                        <td>{{$u->created_at}}</td>
                        <td><button userid="{{$u->id}}" class="btn btn-danger btn-sm" id="activateSingle"><i class="fa fa-check"></i> Active User</button></td>
					</tr>
					<?php $i++; ?>
					@endforeach

				</tbody>
			</table>
    </div>
</div>
@endif
@endif



@endsection

@section('scripts')
<script>
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


    var items = [];

    $('body').on('change', '#actAll', function() {
        var checked = $(this).is(":checked");
        if(checked) {
            $('#activateAll').css('display', 'block');
            $('.checkitem').each(function(i, k) {
                $(this).prop('checked', true);
                var userid = $(this).attr('userid');
                items.push(userid);
            })
        }else{
            $('#activateAll').css('display', 'none')
            $('.checkitem').each(function(i, k) {
                $(this).prop('checked', false);
                var userid = $(this).attr('userid');
                items.pop(userid);
            })
        }
    });

    $('body').on('click', '#activateSingle', function() {
        var userid = $(this).attr('userid');
        swal({
            title: "Activate User Account",
            text: "This will activate the selected user!",
            type: "info",
            showCancelButton: true,
            closeOnConfirm: false,
            showLoaderOnConfirm: true
        }, function () {
            var data = {
                userid: userid,
                _token: '{{csrf_token()}}'
            }
            Biggo.talkToServer('{{route("app.users.activateSingle")}}', data).then(function(res){
                if (res.error) {
                    swal({
                        title: 'Error!',
                        text: res.msg,
                        type: 'error'
                    }, function() {
                        
                    });
                }else{
                    swal({
                        title: 'Account activated!',
                        text: 'Successfully created!',
                        type: 'success'
                    }, function() {
                        window.location = '';
                    });
                } 
            });
        });
    });

    $('body').on('click', '#activateAll', function() {
        swal({
            title: "Activate User Accounts",
            text: "This will activate the selected users!",
            type: "info",
            showCancelButton: true,
            closeOnConfirm: false,
            showLoaderOnConfirm: true
        }, function () {
            var data = {
                userids: items,
                _token: '{{csrf_token()}}'
            }
            Biggo.talkToServer('{{route("app.users.activateAll")}}', data).then(function(res){
                if (res.error) {
                    swal({
                        title: 'Error!',
                        text: res.msg,
                        type: 'error'
                    }, function() {
                        
                    });
                }else{
                    swal({
                        title: 'Accounts activated!',
                        text: 'Successfully created!',
                        type: 'success'
                    }, function() {
                        window.location = '{{route("app.users.activateAll.refresh")}}';
                    });
                } 
            });
        });
    })
})
</script>
@endsection