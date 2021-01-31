<?php

/**
 * @SWG\Swagger(
 *   basePath="/part_one/public/api",
 *   @SWG\Info(
 *     title="QuizApp API",
 *     version="1.0.0"
 *   )
 * )
 */


namespace App\Http\Controllers;
// Useful Link: https://github.com/zircote/swagger-php/tree/2.0.9/Examples/petstore.swagger.io
use Illuminate\Http\Request;
use App\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

use JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;

use App\Quiz;


class ApiController extends Controller
{


/**
 * @SWG\Post(
 *   path="/auth",
 *   summary="Authentication Users!",
 *   operationId="authenticate",
 *  @SWG\Parameter(
 *         name="body",
 *         in="body",
 *         description="User object",
 *         required=true,
 *         @SWG\Schema(ref="#/definitions/User"),
 *     ),
 *   @SWG\Response(response=200, description="successful operation"),
 *   @SWG\Response(response=406, description="not acceptable"),
 *  @SWG\Response(response=404, description="not found"),
 *   @SWG\Response(response=500, description="internal server error")
 * )
 *
 */

    public function authenticate(Request $request)
    {
        $credentials = $request->only('email', 'password');

        try {
            if (! $token = JWTAuth::attempt($credentials)) {
                return response()->json(['error' => 'invalid_credentials'], 400);
            }
            
        } catch (JWTException $e) {
            return response()->json(['error' => 'could_not_create_token'], 500);
        }

        $user = auth()->user();

        return response()->json(compact('token' ,'user'));
    }

   
  /**
 * @SWG\Get(
 *   path="/me",
 *   summary="Get Auth User Details",
 * @SWG\Parameter(
 *          name="token",
 *          description="Token id",
 *          required=true,
 *          type="string",
 *          in="query"
 *      ),
 *   operationId="getAuthenticatedUser",
 *   @SWG\Response(response=200, description="successful operation"),
 *   @SWG\Response(response=406, description="not acceptable"),
 *  @SWG\Response(response=404, description="not found"),
 *   @SWG\Response(response=500, description="internal server error"),
 * )
 * 
 */

	public function getAuthenticatedUser()
    {
            try {

                    if (! $user = JWTAuth::parseToken()->authenticate()) {
                            return response()->json(['user_not_found'], 404);
                    }

            } catch (Tymon\JWTAuth\Exceptions\TokenExpiredException $e) {

                    return response()->json(['token_expired'], $e->getStatusCode());

            } catch (Tymon\JWTAuth\Exceptions\TokenInvalidException $e) {

                    return response()->json(['token_invalid'], $e->getStatusCode());

            } catch (Tymon\JWTAuth\Exceptions\JWTException $e) {

                    return response()->json(['token_absent'], $e->getStatusCode());

            }

            return response()->json(compact('user'));
    }


    /**
 * @SWG\Post(
 *   path="/register",
 *   summary="Register Users!",
 *   operationId="register",
 *  @SWG\Parameter(
 *         name="body",
 *         in="body",
 *         description="User object",
 *         required=true,
 *         @SWG\Schema(ref="#/definitions/User"),
 *     ),
 *   @SWG\Response(response=200, description="successful operation"),
 *   @SWG\Response(response=406, description="not acceptable"),
 *  @SWG\Response(response=404, description="not found"),
 *   @SWG\Response(response=500, description="internal server error"),
 * )
 *
 */
    public function register(Request $request)
    {

   			// name: Test Man
			// email: test@email.com
			// password: secret
			// password_confirmation: secret

            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:255',
                'email' => 'required|string|email|max:255|unique:users',
                'password' => 'required|string|min:6|confirmed',
            ]);

            if($validator->fails()){
                    return response()->json($validator->errors(), 400);
            }

            $user = new User;
            $user->name  = $request->get('name');
            $user->email = $request->get('email');
            $user->password = Hash::make($request->get('password'));
            $user->role_id = 2;
            $user->save();

            $token = JWTAuth::fromUser($user);

            return response()->json(compact('user','token'),201);
    }

/**
 * @SWG\Post(
 *   path="/logout",
 *   summary="Logout User",
 *   operationId="logout",
 *   @SWG\Response(response=200, description="successful operation"),
 *   @SWG\Response(response=406, description="not acceptable"),
 *  @SWG\Response(response=404, description="not found"),
 *   @SWG\Response(response=500, description="internal server error"),
 * )
 *
 */
    public function logout(Request $request)
    {
        $this->validate($request, [
            'token' => 'required'
        ]);

 
        try {
            
            JWTAuth::parseToken()->invalidate($request->token);
 
            return response()->json([
                'success' => true,
                'message' => 'User logged out successfully'
            ]);
            
        } catch (JWTException $exception) {
            return response()->json([
                'success' => false,
                'message' => 'Sorry, the user cannot be logged out '
            ], 500);
        }
    }

    private function myscore($quid) {
        return \DB::table('answers')->join('attempts', 'attempts.aid', '=', 'answers.id')
        ->select('*')
        ->where('attempts.quiz_id', $quid)->where('answers.correct', 1)
        ->where('attempts.user_id', auth()->user()->id)
        ->count();
    }
    private function ansx($id) {
       return \DB::table('answers')->join('questions', 'questions.id', '=', 'answers.question_id')->select('*')->where('questions.quiz_id', $id)->where('answers.correct', 1)->count();
    }
    private function rankMe($quid) {
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

      /**
 * @SWG\Get(
 *   path="/dashboard",
 *   summary="Get User Dashboard",
 *   operationId="dashboard",
 * @SWG\Parameter(
 *          name="token",
 *          description="Token id",
 *          required=true,
 *          type="string",
 *          in="query"
 *      ),
 *   @SWG\Response(response=200, description="successful operation"),
 *   @SWG\Response(response=406, description="not acceptable"),
 *   @SWG\Response(response=500, description="internal server error"),
 *  @SWG\Response(response=404, description="not found"),
 * )
 * */
    public function dashboard() {
        $quizes = \App\Quiz::where('status', 1)->orderBy('id', 'DESC')->get();
        if(count($quizes)) {
            
            $dataX = [];
	
            foreach ($quizes as $q) {
                $c2 = \App\Attempt::where('quiz_id', $q->id)->where('user_id', auth()->user()->id)->count(); 
                if($c2 == 0) {
                    $data = [];
                    $data["quiz_id"]      = $q->id;
                    $data["quiz_name"]    = $q->quiz_name;
                    $data["description"]  = $q->description;
                    $data["questions_no"] = $q->questions_no;
                    $data["result"]       = (object)[];
                    $dataX[] = $data;
                } else {
                    $c = \App\Quizfeedback::where('user_id', auth()->user()->id)->where('quiz_id', $q->id)->where('published', 1)->where('seen', 0)->get();
                    if(count($c) > 0) {
          	       $data = [];
                       $data["quiz_id"]      = $q->id;
                       $data["quiz_name"]    = $q->quiz_name;
                       $data["description"]  = $q->description;
                       $data["questions_no"] = $q->questions_no;
                       $data["result"]       = ["score" => $this->myscore($q->id) . '/' . $this->ansx($q->id) , "rank" =>  $this->rankMe($q->id) ];
                       $dataX[] = $data;
		    }	
		}
            }
	    
            if(count($dataX) == 0) {
                return response()->json([
               "data" => ["quizes" => []]
            ], 200);
            }
            return response()->json([
               "data" => ["quizes" => $dataX]
            ], 200);
        }else {
            return response()->json([
               "data" => ["quizes" => []]
            ], 200);
        }
    }

    // Start Quiz App
  /**
 * @SWG\Get(
 *   path="/quiz/start/{id}",
 *   summary="Start new Quiz",
 *   operationId="startQuiz",
 * @SWG\Parameter(
 *          name="id",
 *          description="Quiz id",
 *          required=true,
 *          type="integer",
 *          in="path"
 *      ),
 * @SWG\Parameter(
 *          name="token",
 *          description="Token id",
 *          required=true,
 *          type="string",
 *          in="query"
 *      ),
 *   @SWG\Response(response=200, description="successful operation"),
 *   @SWG\Response(response=406, description="not acceptable"),
 *   @SWG\Response(response=500, description="internal server error"),
 *  @SWG\Response(response=404, description="not found"),
 * )
 * */
    public function startQuiz($id) {
        $quiz = Quiz::find($id);
        if($quiz){
             if($quiz->status == 1){
                $questions = \App\Question::where('quiz_id', $quiz->id)->orderBy('qn_no', 'ASC')->get();
                $questions_ = [];
                foreach($questions as $q) {
                    $answers = \App\Answer::where('question_id', $q->id)->get();
                    $data = [];
                    $data["question_id"]         = $q->id;
                    $data["question_number"]     = $q->qn_no; 
                    $data["question_body"]       = $q->question; 
                    $data["question_category"]   = $q->category; 
                    $data["question_photo_location"]   = $q->qn_photo_location; 
                   
                    $questions_answers = [];
                    foreach($answers as $a) {
                      
                        $attt = \DB::table('answers')->join('attempts', 'attempts.aid', '=', 'answers.id')
                                ->select('*')
                                ->where('attempts.quiz_id', $quiz->id)->where('answers.correct', 1)
                                ->where('attempts.user_id', auth()->user()->id)
                                ->where('answers.id', $a->id)
                                ->count();
                        if($attt) {
                            $checked = true;
                        }else {
                            $checked = false;
                        }
                        $datax = [];
                        $datax["answer_id"]        = $a->id;
                        $datax["my_answer"]         = $checked;
                        $datax["answer"]           = $a->answer;
                        $datax["correct"]          = $a->correct == 0 ? false : true;
                        $questions_answers[]       = $datax;
                        $data["questions_answers"] = $questions_answers;
                    }
                    $questions_[] = $data;
                }
                
                return response()->json([
                   "data" => [
                        "quiz" => ["quiz"=>$quiz, "detail"=>$questions_],
                        "message" => null,
                        "error" => false  
                   ]
                ], 200);
             }
            
            return response()->json([
               "data" => [
                    "quiz" => null,
                    "message" => "Quiz is not yet published!",
                    "error" => true 
               ]
            ], 400);
            
        }
        
        return response()->json([
               "data" => [
                    "quiz" => $quiz,
                    "message" => "No Quiz found",
                    "error" => true  
               ]
            ], 404);
    }


     /**
 * @SWG\Get(
 *   path="/quiz/myquizes",
 *   summary="Get User Quizes",
 *   operationId="myQuizes",
 * @SWG\Parameter(
 *          name="token",
 *          description="Token id",
 *          required=true,
 *          type="string",
 *          in="query"
 *      ),
 *   @SWG\Response(response=200, description="successful operation"),
 *   @SWG\Response(response=406, description="not acceptable"),
 *  @SWG\Response(response=404, description="not found"),
 *   @SWG\Response(response=500, description="internal server error")
 * )
 * */
    // My Quizes
    public function myQuizes() {
        $quizes = \DB::table('quiz')->join('attempts', 'quiz.id', '=', 'attempts.quiz_id')->select('attempts.*', 'quiz.*')->where('attempts.user_id', auth()->user()->id)->where('quiz.completed', 1)->orderBy('quiz.id', 'DESC')->get();
        $quiz_ids = [];
        $qz = [];
        foreach($quizes as $q) {
            $qid = $q->quiz_id;
            $data = [];
            if(!in_array($qid, $quiz_ids)) {
                $quiz_ids[] = $qid;
                $data["quiz_name"] = $q->quiz_name;
                $data["quiz_id"] = $q->quiz_id;
                $data["questions_no"] = $q->questions_no;
     		$data["description"] = $q->description;
                $data["result"] = ["score" =>  $this->myscore($q->id) . '/' .  $this->ansx($q->id) , "rank" =>  $this->rankMe($q->id) ];
                $qz[] = $data;
            }
        }
        //arsort($qz);
        //return ["data" => $qz ];

        return response()->json(["data" => $qz ], 200);
    }

    // Attempt Quiz
    public function attempt($qid) {
        $user_id = auth()->user()->id;
        $quiz_id = $qid;
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
        return response()->json([
            "error" => false,
            "msg"   => "Successfully submitted"
        ]);
    }

    // Get My Attempts
    public function getAttempts($id){
        $user_id = auth()->user()->id;
        $quiz_id = $qid;
        return \App\Attempt::where('quiz_id', $quiz_id)->where('user_id', $user_id)->get();   
    }


   /**
 * @SWG\Post(
 *   path="/settings/changepassword",
 *   summary="Change User Password",
 *   operationId="changepassword",
 * @SWG\Parameter(
 *          name="token",
 *          description="Token id",
 *          required=true,
 *          type="string",
 *          in="query"
 *      ),
  *  @SWG\Parameter(
 *         name="body",
 *         in="body",
 *         description="User object",
 *         required=true,
 *         @SWG\Schema(ref="#/definitions/User"),
 *     ),
 *   @SWG\Response(response=200, description="successful operation"),
 *   @SWG\Response(response=406, description="not acceptable"),
 *  @SWG\Response(response=404, description="not found"),
 *   @SWG\Response(response=500, description="internal server error")
 * )
 *
 */

    // Change Password
    public function changepassword(){
        $cnewPassword = request('password');
        $user = User::find(auth()->user()->id);
        $user->password = bcrypt($cnewPassword);
        $user->save();
        return response()->json(["message"=>"Password was changed successfully"], 200); 
    }



    // Full Quiz
    /**
 * @SWG\Get(
 *   path="/quiz/full/{id}",
 *   summary="Full Quiz Detail",
 *   operationId="fullQuiz",
 * @SWG\Parameter(
 *          name="id",
 *          description="Quiz id",
 *          required=true,
 *          type="integer",
 *          in="path"
 *      ),
 * @SWG\Parameter(
 *          name="token",
 *          description="Token id",
 *          required=true,
 *          type="string",
 *          in="query"
 *      ),
 *   @SWG\Response(response=200, description="successful operation"),
 *   @SWG\Response(response=406, description="not acceptable"),
 *   @SWG\Response(response=404, description="not found"),
 *   @SWG\Response(response=500, description="internal server error")
 * )
 * */
    public function fullQuiz($id) {
        $quiz = Quiz::find($id);
        if($quiz){
             if($quiz->status == 1){
                $questions = \App\Question::where('quiz_id', $quiz->id)->orderBy('qn_no', 'ASC')->get();
                $questions_ = [];
                foreach($questions as $q) {
                    $answers = \App\Answer::where('question_id', $q->id)->get();
                    $data = [];
                    $data["question_id"]         = $q->id;
                    $data["question_number"]     = $q->qn_no; 
                    $data["question_body"]       = $q->question; 
                    $data["question_category"]   = $q->category; 
                    $data["question_photo_location"]   = $q->qn_photo_location; 
                   
                    $questions_answers = [];
                    foreach($answers as $a) {
                        $attt = \DB::table('answers')->join('attempts', 'attempts.aid', '=', 'answers.id')
                                ->select('*')
                                ->where('attempts.quiz_id', $quiz->id)->where('answers.correct', 1)
                                ->where('attempts.user_id', auth()->user()->id)
                                ->where('answers.id', $a->id)
                                ->count();
                        if($attt) {
                            $checked = true;
                        }else {
                            $checked = false;
                        }
                        $datax = [];
                        $datax["answer_id"]        = $a->id;
                        $datax["my_answer"]         = $checked;
                        $datax["answer"]           = $a->answer;
                        $datax["correct"]          = $a->correct == 0 ? false : true;
                        $questions_answers[]       = $datax;
                        $data["questions_answers"] = $questions_answers;
                        
                    }
                    $questions_[] = $data;
                }
                
                return response()->json([
                   "data" => [
                        "quiz" => ["quiz"=>$quiz, "detail"=>$questions_],
                        "message" => null,
                        "error" => false  
                   ]
                ], 200);
             }
            
            return response()->json([
               "data" => [
                    "quiz" => null,
                    "message" => "Quiz is not yet published!",
                    "error" => true 
               ]
            ], 400);
            
        }
        
        return response()->json([
               "data" => [
                    "quiz" => $quiz,
                    "message" => "No Quiz found",
                    "error" => true  
               ]
            ], 404);
    }

    // Seen Quiz
    public function seenResults($id) {
        $qf = \App\Quizfeedback::where('user_id', auth()->user()->id)->where('quiz_id', $id)->get();
        if(count($qf)) {
            foreach ($qf as $q) {
                $qff = \App\Quizfeedback::find($q->id);
                $qff->seen = 1;
                $qff->save();
            }
        }
        return response()->json([
            "error" => false,
            "msg"   => "Successfully Updated"
        ]);
    }


    // Save token
    public function saveFCMToken() {
        $token = request('fcm_token');
        // check existence of token
        $check = \App\Fcmtoken::where('fcm_token', $token)->count();
        if($check == 0){
            // save it 
            $fcmToken = new \App\Fcmtoken;
            $fcmToken->fcm_token = $token;
            $fcmToken->save();
        }
        return response()->json(["msg"=>"Token save successfully!"], 200); 
    }
        
    
}
