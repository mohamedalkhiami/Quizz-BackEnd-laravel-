<?php

$user = \App\User::find($id);

?>

<form id="userEditForm">
    <div class="modal-body" id="formEditUser">

        {{csrf_field()}}

        <div class="form-group">
            <label for="user-name" class="col-form-label">Full Name:</label>
            <input type="text" value="{{$user->name}}" name="fullname" class="form-control validate[required]" id="user-name">
        </div>
        <div class="form-group">
            <label for="user-email" class="col-form-label">Email:</label>
            <input type="email" value="{{$user->email}}" name="email" class="form-control validate[required,custom[email]]" id="user-email">
        </div>
    </div>
    <div class="modal-footer">
        
        <button type="button" refreshURL="{{route('users.refresh')}}" id="updateUser" route="{{route('users.update', $id)}}" class="btn btn-primary">Update Changes</button>
    </div>
</form>