<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Quiz;
use App\Question;
use App\Answer;
use App\HelperX;

class QuizController extends Controller
{
    public function index(){
        return view('quiz.index');
    }

    public function attemptRefresh(){
        return redirect()->to('dashboard')->with('success', 'Successfully submitted!');
    }

    public function startQuiz($id) {
        $quiz = Quiz::find($id);
        if($quiz){
             if($quiz->status == 1){
                return view('quiz.start', compact('id'));
             }
             return redirect()->back()->with('error', 'Quiz is not yet published!');
        }
        return redirect()->back()->with('error', 'No qiuz Found');
    }

    public function unpublishResults($id) {
        //sleep(1);
        $qf = \App\Quizfeedback::where('quiz_id', $id)->count();
        if($qf > 0) {
            \App\Quizfeedback::where('quiz_id', $id)->delete();
            foreach (\App\User::where('role_id', 2)->get() as $u) {
                $qfx = new \App\Quizfeedback;
                $qfx->published = 0;
                $qfx->user_id   = $u->id; 
                $qfx->quiz_id   = $id;
                $qfx->save();
            }
        }

        // Send Email
        if(HelperX::checkNoty('emailNotification') == 1) {
             \App\HelperX::sendEmails('emails.quizresults_mail', 'QUIZ RESULTS ARE OUT!');
        }
       
    }   

    public function publishResults($id) {
        //sleep(1);
        $qf = \App\Quizfeedback::where('quiz_id', $id)->count();
        if($qf > 0) {

            \App\Quizfeedback::where('quiz_id', $id)->delete();
            foreach (\App\User::where('role_id', 2)->get() as $u) {
                $qfx = new \App\Quizfeedback;
                $qfx->published = 1;
                $qfx->user_id   = $u->id; 
                $qfx->quiz_id   = $id;
                $qfx->save();
            }

	   $qiz = \App\Quiz::find($id);
	   $qiz->completed = 1;
           $qiz->save();

        }

        // Send Email
        if(HelperX::checkNoty('emailNotification') == 1) {
            \App\HelperX::sendEmails('emails.quizresults_mail', 'QUIZ RESULTS ARE OUT!');
        }
       // \App\HelperX::sendEmails('emails.quizresults_mail', 'QUIZ RESULTS ARE OUT!');
        
       // Push Notification
       if(HelperX::checkNoty('pushNotification') == 1) {
            \App\FcmHelper::sendPushNotificationQuizResPub();
        }
       // \App\FcmHelper::sendPushNotificationQuizResPub();
    }   

    public function seenResults($id) {
        return view('quiz.seen', compact('id'));
    }

    public function seenXResults($id, $uxid) {
        
        return view('quiz.seenX', compact('id', 'uxid'));
    }

    public function attempt() {

        $user_id = request('user_id');
        $quiz_id = request('quiz_id');

        $check = \App\Attempt::where('quiz_id', $quiz_id)->where('user_id', $user_id)->count();

        if($check) {
            return response()->json([
                "error" => true,
                "msg"   => "You already done this quiz"
            ]);
        }

        $answers = (request('attempts'));

        if($answers) {
            foreach ($answers as $a) {
                $aid    = $a["answer_id"];   
                $qid    = $a["question_id"]; 
                $att    = new \App\Attempt;
                $att->aid = $aid;
                $att->qid = $qid;
                $att->user_id = $user_id;
                $att->quiz_id = $quiz_id;
                $att->save();   
            }
        }else {
            $att    = new \App\Attempt;
            $att->user_id = $user_id;
            $att->quiz_id = $quiz_id;
            $att->save();   
        }

        if(HelperX::checkNoty('emailNotification') == 1) {
            \App\HelperX::sendEmailTOAdmins();
        }
        // Send Email to admins
        //\App\HelperX::sendEmailTOAdmins();
        
    }

    

    public function edit($id) {
        return view('quiz.edit', compact('id'));
    }

    public function preview($id) {
        return view('quiz.preview', compact('id'));
    }

    public function staff() {
        return view('quiz.staff');
    }

    public function report($id) {
        return view('quiz.report', compact('id'));
    }

    public function unpublish($id) {
        $quiz = Quiz::find($id);
        $quiz->status = 0;
        $quiz->save();
    }

    public function publish($id) {
        $quiz = Quiz::find($id);
        $quiz->status = 1;
        $quiz->save();
        // send Email here to every one
        if(HelperX::checkNoty('emailNotification') == 1) {
            \App\HelperX::sendEmails('emails.quizpublished_mail', 'NEW QUIZ PUBLISHED!');
        }
        //\App\HelperX::sendEmails('emails.quizpublished_mail', 'NEW QUIZ PUBLISHED!');

        // Push Notification
        if(HelperX::checkNoty('pushNotification') == 1) {
            \App\FcmHelper::sendPushNotificationQuizPub();
        }
        
        //\App\FcmHelper::sendPushNotificationQuizPub();

        // fcm()
        //     ->to(\App\FcmHelper::getFCMTokens()) // $recipients must an array
        //     ->data([
        //         'title' => 'Test FCM',
        //         'body' => 'This is a test of FCM',
        //     ])
        //     ->send();
    }

    public function cancel($id) {
        $quizid = request('quizid');
        return view('quiz.questionsEditX', compact('id', 'quizid'));
    }

    public function destroy($id) {
        $q = Question::find($id);
        $q->delete();
        $a = Answer::where('question_id', $id)->delete();
    }

    public function update($id){
        $quiz_name         = request('quiz_name');
        $quiz_no_questions = request('quiz_no_questions');
        $quiz_description  = request('quiz_description');

        $qns = Question::where('quiz_id', $id)->count();

        $q  = Quiz::find($id);

       

        if ($quiz_no_questions >= $qns) {
            $q->quiz_name = $quiz_name;
            $q->questions_no = $quiz_no_questions;
            $q->description = $quiz_description;
            $q->save();
        }

       
    }

    public function refresh() {
        return redirect()->back()->with('success', 'Successfully Updated!');
    }

    public function store() {
        $quiz_name         = request('quiz_name');
        $quiz_no_questions = request('quiz_no_questions');
        $quiz_description  = request('quiz_description');

        $q = new Quiz;
        $q->quiz_name = $quiz_name;
        $q->questions_no = $quiz_no_questions;
        $q->description = $quiz_description;
        $nq = $q->save();

	$qf = \App\Quizfeedback::where('quiz_id', $q->id)->count();
        if($qf == 0) {
            \App\Quizfeedback::where('quiz_id', $q->id)->delete();
            foreach (\App\User::where('role_id', 2)->get() as $u) {
                $qfx = new \App\Quizfeedback;
                $qfx->published = 0;
                $qfx->user_id   = $u->id; 
                $qfx->quiz_id   = $q->id;
                $qfx->save();
            }
        }


        return redirect()->back()->with('success', 'Successfully Added!');

    }
}

