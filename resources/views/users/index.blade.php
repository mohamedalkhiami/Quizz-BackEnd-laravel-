@extends('layout')

@section('title', 'Manage Users')


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
	<div class="row">
		<div class="col-md-9">
			<table id="dataTable" class="table table-striped table-bordered">
				<thead>
					<tr>
						<th>#</th>
						<th>Name</th>
						<th>Email</th>
						<th>Status</th>
						<th>Manage</th>
					</tr>
				</thead>
				<tbody>
					<?php 

					$users = \App\User::where('role_id', '!=', 1)->get();
					$i = 1;

					?>
					@foreach($users as $u)
					<tr>
						<td>{{$i}}</td>
						<td>{{$u->name}}</td>
						<td>{{$u->email}}</td>
						<td>{!! $u->active == 1 ? '<label class="alert alert-success">Active</label>' : '<label class="alert alert-danger">Blocked</label>' !!}</td>
						<td>
							<button class="btn btn-primary btn-sm editUser" route="{{route('users.edit', $u->id)}}" userid="{{$u->id}}"  data-toggle="modal" data-target="#userModal" ><i class="fa fa-edit"></i> Edit User</button>
							@if($u->active == 1)
							<button class="btn btn-danger btn-sm deactivate" userid="{{$u->id}}"><i class="fa fa-lock"></i> De-activate</button>
							@else
							<button class="btn btn-success btn-sm activate" userid="{{$u->id}}"><i class="fa fa-unlock"></i> Activate</button>
							@endif
							<button class="btn btn-warning btn-sm changepassword" userid="{{$u->id}}" route="{{route('app.changepassword', $u->id)}}" data-toggle="modal" data-target="#changePassword"><i class="fa fa-key"></i> Change Password</button>
						</td>
					</tr>
					<?php $i++; ?>
					@endforeach

				</tbody>
			</table>
		</div>
	</div>
</div>
<article>
	<div class="modal fade" id="userModal" tabindex="-1" role="dialog" aria-labelledby="userModalLabel" aria-hidden="true">
		<div class="modal-dialog modal-dialog-centered " role="document">
			<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title" id="userModalLabel"><i class="fa fa-edit"></i> Edit User Information</h5>
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">&times;</span>
					</button>
				</div>
				<div style="padding:10px">
					<center>
						<img src="{{url('images/loader.gif')}}" id="loader" />
					</center>
					<div id="userEditor"></div>
				</div>
			</div>
		</div>
	</div>
</article>

<article>
	<div class="modal fade" id="changePassword" tabindex="-1" role="dialog" aria-labelledby="userModalLabel" aria-hidden="true">
		<div class="modal-dialog modal-dialog-centered " role="document">
			<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title" id="userModalLabel"><i class="fa fa-key"></i> Change Password</h5>
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">&times;</span>
					</button>
				</div>
				<div style="padding:10px">
					<center>
						<img src="{{url('images/loader.gif')}}" id="loaderKey" />
					</center>
					<div id="changePassEditor"></div>
				</div>
			</div>
		</div>
	</div>
</article>

@endsection


@section('scripts')

	<script src="{{url('js/jquery.dataTables.min.js')}}"></script>
	<script src="{{url('js/dataTables.bootstrap4.min.js')}}"></script>
	<script src="{{url('js/dataTables.buttons.min.js')}}"></script>
	<script src="{{url('js/buttons.bootstrap4.min.js')}}"></script>
	<script src="{{url('js/buttons.html5.min.js')}}"></script>
	<script src="{{url('js/buttons.flash.min.js')}}"></script>
	<script src="{{url('js/buttons.print.min.js')}}"></script>
<script>
	$(document).ready(function () {

		$('body').on('click', '.activate', function() {
			var userid = $(this).attr('userid');
			swal({
				  title: "Activate User Account",
				  text: "",
				  type: "info",
				  showCancelButton: true,
				  closeOnConfirm: false,
				  showLoaderOnConfirm: true
				}, function () {
					var data = {
						userid: userid,
						_token: '{{csrf_token()}}'
					}
				  	Biggo.talkToServer('{{route("users.activate")}}', data).then(function(res){
						  window.location = '{{route("users.refresh")}}';
					});
				});
		});

		$('body').on('click', '.changepassword', function() {
			var userid = $(this).attr('userid');
			var route  = $(this).attr('route');

			$('#changePassEditor').html('');
			$('#loaderKey').show();
			$.get(route, function(res) {
				$('#changePassEditor').html(res);
				$('#loaderKey').hide();
			});
		});

		$('body').on('click', '#changePasswordX', function() {
		
			var valid = $("#formEditUserPasswordForm").validationEngine('validate');
			var route = $(this).attr('route');
			var refreshURL = $(this).attr('refreshURL')
			if(valid) {
				$("#formEditUserPassword").css('opacity', 0.2);
				$(this).prop('disabled', true);
				$(this).css('cursor', 'wait');
				var data = $("#formEditUserPasswordForm").serializeArray();

				Biggo.talkToServer(route, data).then(function(res){

					$('#changePasswordX').prop('disabled', false);
					$('#changePasswordX').css('cursor', '');
					$("#formEditUserPassword").css('opacity', 1);
					if(res.error) {
						Biggo.showFeedBack(userEditForm, res.msg, res.error);
					}

					window.location = refreshURL;
					
					
				});
			}
		});

		$('body').on('click', '.deactivate', function() {
			var userid = $(this).attr('userid');
			swal({
				  title: "De-activare User Account",
				  text: "",
				  type: "info",
				  showCancelButton: true,
				  closeOnConfirm: false,
				  showLoaderOnConfirm: true
				}, function () {
					var data = {
						userid: userid,
						_token: '{{csrf_token()}}'
					}
				  	Biggo.talkToServer('{{route("users.deactivate")}}', data).then(function(res){
						  window.location = '{{route("users.refresh")}}';
					});
				});
		});

		$('body').on('click', '#updateUser', function() {
			var valid = $("#userEditForm").validationEngine('validate');
			var route = $(this).attr('route');
			var refreshURL = $(this).attr('refreshURL')
			if(valid) {
				
				$("#formEditUser").css('opacity', 0.2);
				$(this).prop('disabled', true);
				$(this).css('cursor', 'wait');
				var data = $("#userEditForm").serializeArray();

				Biggo.talkToServer(route, data).then(function(res){

					$('#updateUser').prop('disabled', false);
					$('#updateUser').css('cursor', '');
					$("#formEditUser").css('opacity', 1);
					if(res.error) {
						Biggo.showFeedBack(userEditForm, res.msg, res.error);
					}

					window.location = refreshURL;
					
					
				});
				
			}
		})

		$('body').on('click', '.editUser', function() {
			var userid = $(this).attr('userid');
			var route = $(this).attr('route');
			$('#loader').show();
			$('#userEditor').html('');
			$.get(route, function(res) {
				$('#loader').hide();
				$('#userEditor').html(res);
			})
		});

		var table = $('#dataTable').DataTable({
			"bInfo": false,
			"pageLength": 15,
			"lengthMenu": [[10, 15 , 25, 50, -1], [10, 15, 25, 50, "All"]],
				dom: "Blfrtip",
				buttons: [
					{
						extend: "excel",
						className: "btn-sm btn-success px-3 py-2",
						title: 'Users'
					},
					{
						extend: "pdf",
						className: "btn-sm btn-danger px-3 py-2",
						title: 'Users'
					},
					{
						extend: "print",
						className: "btn-sm px-3 py-2",
						title: 'Users'
					}
				]
		});


		$('#userModal').on('show.bs.modal', function (event) {
			var button = $(event.relatedTarget) // Button that triggered the modal
			var user_name = button.data('user_name') // Extract info from data-* attributes
			var user_email = button.data('user_email') // Extract info from data-* attributes
			// If necessary, you could initiate an AJAX request here (and then do the updating in a callback).
			// Update the modal's content. We'll use jQuery here, but you could use a data binding library or other methods instead.
			var modal = $(this)
			modal.find('.modal-title').text(user_name)
			modal.find('.modal-body input#user-name').val(user_name)
			modal.find('.modal-body input#user-email').val(user_email)
		});
	});
</script>

@endsection