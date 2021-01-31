<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ReminderController extends Controller
{
    //
    public function index() {
    	return view('reminders.index');
    }

    public function save() {
    	$subject    = request('reminder_subject');
    	$bod       = request('reminder_body');
    	$recipients = request('reminder_recipients');
    	$view       = 'emails.reminder_mail';


    	$r = new \App\Reminder;
    	$r->reminder_subject    = $subject;
    	$r->reminder_body       = $bod;
    	$r->reminder_recipients = implode( ", ", $recipients);
    	$r->save();
 
    	 // Send Email
        \App\HelperX::sendReminders($view, $subject, $bod, $recipients);

        return response()->json([
                "error" => true,
                "msg"   => "Successfully added!"
            ]);
    }

    public function refresh() {
    	return redirect()->back()->with('success', 'Successfully Updated!');
    }
}
