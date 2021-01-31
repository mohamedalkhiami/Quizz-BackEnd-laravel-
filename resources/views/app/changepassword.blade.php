<?php

$user = \App\User::find($id);

?>

<form id="formEditUserPasswordForm">
    <div class="modal-body" id="formEditUserPassword">

        {{csrf_field()}}

        <div class="form-group">
            <label for="editpassword" class="col-form-label">New Password:</label>
            <input type="password" value="" name="editpassword" class="form-control validate[required]" id="editpassword"
            data-errormessage-value-missing="Password is required!" 
            />
        </div>
        <div class="form-group">
            <label for="editcpassword" class="col-form-label">Confirm Password:</label>
            <input type="password" value="" name="editcpassword" class="form-control validate[required,equals[editpassword]]" id="editcpassword"
            data-errormessage-value-missing="Confirm password is required!" 
			data-errormessage="Password mismatches!"
            />
        </div>
    </div>
    <div class="modal-footer">
        
        <button type="button" refreshURL="{{route('users.refresh')}}" id="changePasswordX" route="{{route('users.update.password', $id)}}" class="btn btn-primary">Update Password</button>
    </div>
</form>