<?php

namespace App\Http\Controllers;
use App\User;

use Illuminate\Http\Request;

class AppController extends Controller
{
    public function index() {
    	return redirect()->to('/login');
    }

    public function getPasswordReset(Request $request) {
        if ($request->has('token')) {
            
            $token = $request->input('token');
            $time  = decrypt($token);

            $chek = \App\Pwdresett::where('token', $time)->where('requested', 1)->count();
            if($chek == 0) {
                return redirect()->to('/login')->with('error', 'Dont try to access through this link again!!');
            } else {
                $email = \App\Pwdresett::where('token', $time)->where('requested', 1)->first()->email;
                return redirect()->to('/reset-password')->with('password', $email);
            }
        }else{
            return redirect()->to('/login')->with('error', 'Dont try to access through this link again!!');
        }
    }

    public function doResetPassword() {
        $password = request('password');
        $email    = request('email');

        $user_id = User::where('email', $email)->first()->id;
        $user = User::find($user_id);
        $user->password = bcrypt($password);
        $user->save();

        $chek = \App\Pwdresett::where('email', $email)->count();
        if($chek > 0) {
            $pid  = \App\Pwdresett::where('email', $email)->first()->id;
            $pass = \App\Pwdresett::find($pid);
            $pass->requested = 0;
            $pass->save();
        }

        return redirect()->to('/login')->with('success', 'Now! you can login with new password!!');
    }

    public function resetPassword() {

        $input = session()->get('password');
        
        return view('app.password', compact('input'));

    }

    public function passwordReset() {
        $email = request('email');

        $c = \App\User::where('email', $email)->count();

        if($c == 0) {
            return response()->json([
                'msg'   => 'Email not found!',
                'error' => true
            ]);
        }

        $chek = \App\Pwdresett::where('email', $email)->where('requested', 1)->count();
        if($chek > 0) {
            return response()->json([
                'msg'   => 'Please check you email!! you have already requested!',
                'error' => true
            ]);
        }
        
        

        $time = \Carbon\Carbon::now();
        $url  = '<a href="' . url('/password-reset?token=' . encrypt($time)) . '">'.url('/password-reset?token=' . encrypt($time)).'</a>' ;


        $prs = new \App\Pwdresett;
        $prs->email = $email;
        $prs->token = $time;
        $prs->requested = 1;
        $prs->save();

        $subject = "[GOLDEN RULE - PASSWORD RESET]";

         // Send Email
        \App\HelperX::sendReminders('emails.password_reset', $subject, $url, $email);

        return response()->json([
                'msg'   => 'Successfully sent!. Please check your email!!',
                'error' => false
            ]);
    }

    public function login(){
        return view('app.login');
    }

    public function activated() {
        return redirect()->to('/login')->with('success', 'Successfully Account activated!, you can now login with the same credentials you used when create the account');
    }

    public function changepassword($id) {
        return view('app.changepassword', compact('id'));
    }

    

    public function dologin() {
    	$data = [
    		"email"     => request("email"),
    		"password"  => request("password"),
            "active"    => 1
    	];

    	$credtx = auth()->attempt($data);

    	if($credtx) {
    		return redirect()->intended('dashboard');
    	}else {
    		return redirect()->back()->with('error', 'Invalid User');
    	}
    }

    public function doRegisterRedirect() {
        return redirect()->to('/login')->with('success', 'Successfully Account activated!, you can now login with the same credentials you used when create the account');
        // return redirect()->to('login')->with('success', 'Please wait for verification!, you will receive email once the verification process is done...');
    }

    public function doRegister() {
        $fullname = request('register_name');
        $email    = trim(request('register_email'));
        $password = request('register_password_confirm');
        $check = User::where('email', $email)->count();
        if($check) {
            return response()->json([
               "error" => true,
               "msg"   => "Email already registered!"
            ]);
        }

        $u = new User;
        $u->name     = $fullname;
        $u->email    = $email;
        $u->password = bcrypt($password);
        $u->role_id  = 2;
        $u->save(); 

        // Send Email To Admin
        // try {
        //     $data = array('fullname'=>$fullname, "email"=>$email, "admin"=>\App\User::where('role_id', 1)->first()->email);
    
        //     \Mail::send('emails.register_mail', $data, function($message) use ($data) {
        //         $message->to($data["admin"], 'SYSTEM ADMIN')
        //         //$message->to('joramkimata@gmail.com', 'SYSTEM ADMIN')
        //                 ->subject('NEW USER REGISTERED');
        //         $message->from($data["email"], $data["fullname"]);
        //     });
        // }catch(Exception $e) {
        //     \App\HelperX::sendErrorMail($e->getMessage());
        // }
       

        return response()->json([
               "error" => false,
               "msg"   => "Successfully registered!"
        ]);
    }

    public function dashboard() {
        return view('app.dashboard');
    }

    public function logout() {
        auth()->logout();
        return redirect()->to('/login')->with('success', 'Successfully Logout');
    }
}
