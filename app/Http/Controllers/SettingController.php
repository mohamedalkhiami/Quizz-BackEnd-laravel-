<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Http\Request;
use App\Notificationsetting;

class SettingController extends Controller
{
    public function index() {
        return view('app.settings');
    }

    public function notification() {
        if(request()->has('pushNotification')) {
            // Allow  
            $check = Notificationsetting::where('noty_type', 'pushNotification')->count();
            if($check) {
                $n = Notificationsetting::where('noty_type', 'pushNotification')->first();
                $n->noty_allowed = 1;
                $n->save();
            }  else {
                // add new
                $n = new Notificationsetting;
                $n->noty_type = 'pushNotification';
                $n->noty_allowed = 1;
                $n->save();
            }
        }else {
            // Disallow
            $check = Notificationsetting::where('noty_type', 'pushNotification')->count();
            if($check) {
                $n = Notificationsetting::where('noty_type', 'pushNotification')->first();
                $n->noty_allowed = 0;
                $n->save();
            }  else {
                // add new
                $n = new Notificationsetting;
                $n->noty_type = 'pushNotification';
                $n->noty_allowed = 0;
                $n->save();
            }
        }

        if(request()->has('emailNotification')) {
            // Allow
            $check = Notificationsetting::where('noty_type', 'emailNotification')->count();
            if($check) {
                $n = Notificationsetting::where('noty_type', 'emailNotification')->first();
                $n->noty_allowed = 1;
                $n->save();
            }  else {
                // add new
                $n = new Notificationsetting;
                $n->noty_type = 'emailNotification';
                $n->noty_allowed = 1;
                $n->save();
            }
        }else {
            // Disallow
            $check = Notificationsetting::where('noty_type', 'emailNotification')->count();
            if($check) {
                $n = Notificationsetting::where('noty_type', 'emailNotification')->first();
                $n->noty_allowed = 0;
                $n->save();
            }  else {
                // add new
                $n = new Notificationsetting;
                $n->noty_type = 'emailNotification';
                $n->noty_allowed = 0;
                $n->save();
            }
        }
        return redirect()->back()->with('success', 'Successfully Updated!'); 
    }

    public function changeapplogo(){
        $s = \App\Setting::find(1);
        
        if (request()->file('change_logo')) {
            $s->logo =  \App\HelperX::uplodFileThenReturnPath('change_logo');
            $s->save();
        }
        return redirect()->back()->with('success', 'Successfully Updated!'); 
    }

    public function changeappname() {
        $appname = request('appname');
        $s = \App\Setting::find(1);
        $s->system_name = $appname;
        $s->save();
        return redirect()->back()->with('success', 'Successfully Updated!'); 
    }

    public function changepassword(){
        $cnewPassword = request('cnewPassword');
        $user = User::find(auth()->user()->id);
        $user->password = bcrypt($cnewPassword);
        $user->save();
        return redirect()->back()->with('success', 'Successfully Updated!'); 
    }

    public function changeemail() {
        $email = request('email');
        $userid = auth()->user()->id;
        $check = User::where('email', $email)->where('id', '!=', $userid)->count();
        if($check) {
            return redirect()->back()->with('error', 'Email already used'); 
        }
        $user = User::find(auth()->user()->id);
        $user->email = $email;
        $user->save();
        return redirect()->back()->with('success', 'Successfully Updated!'); 
    }
}
