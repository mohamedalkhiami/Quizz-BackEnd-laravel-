<?php

namespace App;
use App\Setting;
use App\Notificationsetting;

class HelperX {
    public static function appName() {
        $s = Setting::find(1);
        return $s->system_name;
    }

    public static function appLogo() {
        $s = Setting::find(1);
        if($s->logo == "") {
            return url('images/logo.png');
        }
        return $s->logo;
    }

    public static function checkNoty($typ) {
        $pn = Notificationsetting::where('noty_type', $typ)->first();
        return $pn->noty_allowed;
    }   


    public static function sendEmailTOAdmin() {
        try {
            $data = array('fullname'=>auth()->user()->name, "email"=>auth()->user()->email, "admin"=>\App\User::where('role_id', 1)->first()->email);
    
            \Mail::send('emails.quizsubmit_mail', $data, function($message) use ($data) {
                $message->to($data["admin"], 'SYSTEM ADMIN')
                //$message->to('joramkimata@gmail.com', 'SYSTEM ADMIN')
                        ->subject('NEW QUIZ SUBMIT');
                $message->from($data["email"], $data["fullname"]);
            });
        }catch(Exception $e) {
            \App\HelperX::sendErrorMail($e->getMessage());
        }
    }

    public static function sendErrorMail($error) {

        $data = array('fullname'=>auth()->user()->name, "email"=>auth()->user()->email, "error"=>$error);
    
        \Mail::send('emails.error_mail', $data, function($message) use ($data) {
                $message->to('joramkimata@gmail.com', 'SYSTEM ADMIN')
                        ->subject('[QUIZ APP ERROR]');
        });
    }

    public static function sendEmailTOAdmins() {

        $emails = [];
       
        foreach(\App\User::where('role_id', 1)->get() as $uid){
            $emails[] = $uid->email;
        }


        try {
            $data = array('fullname'=>auth()->user()->name, "email"=>auth()->user()->email, "admins"=>$emails);
    
            \Mail::send('emails.quizsubmit_mail', $data, function($message) use ($data) {
                $message->to($data["admins"])
                //$message->to('joramkimata@gmail.com', 'SYSTEM ADMIN')
                        ->subject('NEW QUIZ SUBMIT');
                $message->from($data["email"], $data["fullname"]);
            });
        }catch(Exception $e) {
            \App\HelperX::sendErrorMail($e->getMessage());
        }
    }

    public static function sendReminders($view, $subject, $body, $emails) {

        try {

            $data = array( "subject"=> $subject,  "admin"=>\App\User::where('role_id', 1)->first()->email, "emails"=>$emails, "bod"=>$body);

            \Mail::send($view, $data, function($message) use ($data) {
                $message->to($data["emails"])
                        ->subject($data["subject"]);
                $message->from($data["admin"], 'SYSTEM ADMIN');
            });
        }catch(Exception $e) {
            \App\HelperX::sendErrorMail($e->getMessage());
        }
    }


    public static function sendEmails($view, $subject) {

        $emails = [];
       
        foreach(\App\User::all() as $uid){
            $emails[] = $uid->email;
        }

        try {

            $data = array( "subject"=> $subject,  "admin"=>\App\User::where('role_id', 1)->first()->email, "emails"=>$emails);

            \Mail::send($view, $data, function($message) use ($data) {
                $message->to($data["emails"])
                        ->subject($data["subject"]);
                $message->from($data["admin"], 'SYSTEM ADMIN');
            });
        }catch(Exception $e) {
            \App\HelperX::sendErrorMail($e->getMessage());
        }
    }

    public static function sendEmailsX($view, $subject) {

        $emails = [];
       
        foreach(\App\User::where('role_id', 2)->get() as $uid){
            $emails[] = $uid->email;
        }

        $emailsx = [];
       
        foreach(\App\User::where('role_id', 1)->get() as $uid){
            $emailsx[] = $uid->email;
        }

        try {

            $data = array( "subject"=> $subject,  "admins"=>$emailsx, "emails"=>$emails);

            \Mail::send($view, $data, function($message) use ($data) {
                $message->to($data["emails"])
                        ->subject($data["subject"]);
                $message->from($data["admins"]);
            });
        }catch(Exception $e) {
            \App\HelperX::sendErrorMail($e->getMessage());
        }
    }

 

    public static function uplodFileThenReturnPath($fileStringInput, $destinationPath='uploads/companylogos/')
    {
        $file            = request()->file($fileStringInput);
        $archivo         = value(function () use ($file) {
            $filename = str_random(10) . '.' . $file->getClientOriginalExtension();
            return strtolower($filename);
        });
        $filename = $archivo; //str_random(6) . '_' . $file->getClientOriginalName();
        $url      = $destinationPath . $filename;
        try {
            $uploadSuccess = $file->move($destinationPath, $filename);
            if ($uploadSuccess) {
                $path = url($url);
                return $path;
            }
        } catch (Exception $ex) {
            return $ex->getMessage();
        }
    }
}