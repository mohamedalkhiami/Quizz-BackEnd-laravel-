<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Question;
use App\Answer;

class QuestionController extends Controller
{
    public function add($id) {

    	$qn = Question::where('quiz_id', $id)->count();

    	$quiz_qn_no = \App\Quiz::find($id)->questions_no;

    	if($qn == $quiz_qn_no) {
    		return "<div class='alert alert-danger'>You can not add more questions</div>";
    	}

    	$qnno = 1;

    	if($qn) {
    		$lastQnAdded = Question::where('quiz_id', $id)->latest()->first()->qn_no;
    		$qnno = $qnno + $lastQnAdded;
    	}

        return view('quiz.questions', compact('id', 'qnno'));
    }

    public function deleteAnswer($id){
        Answer::find($id)->delete();
    }


    public function edit($id) {
        $quizid = request('quizid');
    	return view('quiz.questionsEdit', compact('id', 'quizid'));
    }

    public function storeAndContinue($id) {

    	$question = request('question');
    	$quiz_id  = $id;
    	$qnno     = request('qnno');
    	$category = request('category');

    	$q = new Question;

        if(request()->hasFile('attachedPhoto')) {
            //let us upload photo
            $q->qn_photo_location =  \App\HelperX::uplodFileThenReturnPath('attachedPhoto');
        }

    	$q->quiz_id  = $quiz_id;
    	$q->qn_no    = $qnno;
    	$q->category = $category;
    	$q->question = $question;
    	$q->save(); 

    	$answers = json_decode(request('answers'), true);
    	foreach ($answers as $a) {
    		$answer_body    = $a["body"];   
    		$answer_correct = $a["correct"]; 
    		$anw = new Answer;
    		$anw->question_id = $q->id;
    		$anw->answer = $answer_body;
    		$anw->correct = $answer_correct;
    		$anw->save(); 	
    	}
    	

    	$qn = Question::where('quiz_id', $id)->count();

    	$quiz_qn_no = \App\Quiz::find($id)->questions_no;

    	if($qn == $quiz_qn_no) {
    		return response()->json(["refresh"=>true]);
    	}

    	$qnno = 1;

    	if($qn) {
    		$lastQnAdded = Question::where('quiz_id', $id)->latest()->first()->qn_no;
    		$qnno = $qnno + $lastQnAdded;
    	}

        return view('quiz.questions', compact('id', 'qnno'));

    }

    public function store($id) {

    	$question = request('question');
    	$quiz_id  = $id;
    	$qnno     = request('qnno');
    	$category = request('category');

    	$q = new Question;

        if(request()->hasFile('attachedPhoto')) {
            //let us upload photo
            $q->qn_photo_location =  \App\HelperX::uplodFileThenReturnPath('attachedPhoto');
        }


    	$q->quiz_id  = $quiz_id;
    	$q->qn_no    = $qnno;
    	$q->category = $category;
    	$q->question = $question;
    	$q->save(); 

    	$answers = json_decode(request('answers'), true);
    	foreach ($answers as $a) {
    		$answer_body    = $a["body"];   
    		$answer_correct = $a["correct"]; 
    		$anw = new Answer;
    		$anw->question_id = $q->id;
    		$anw->answer = $answer_body;
    		$anw->correct = $answer_correct;
    		$anw->save(); 	
    	}
    	return response()->json(["error"=>false, "msg"=>"Successfully added!"]);
    }

    public function updateQn($id) {
        return dd(request()->all());
    }

}
