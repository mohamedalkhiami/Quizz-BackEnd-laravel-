<?php

$quiz = \App\Quiz::find($id);

?>

<form method="POST" id="editQuizForm">
    <div class="modal-body" id="editQuiz">
        {{csrf_field()}}
        <div class="form-group">
            <label for="add-quiz-name" class="col-form-label">Quiz Name:</label>
            <input type="text" value="{{$quiz->quiz_name}}" name="quiz_name" class="validate[required] form-control" id="add-quiz-name"
            data-errormessage-value-missing="Quiz name is required!"
            />
        </div>
        <div class="form-group">
            <label for="add-quiz-questions-number" class="col-form-label">Number Of Questions:</label>
            <input type="number" value="{{$quiz->questions_no}}" name="quiz_no_questions" class="validate[required,custom[integer]] form-control" id="add-quiz-questions-number"
            data-errormessage-value-missing="Number of Questions is required!" 
            data-errormessage="Should be Number"
            />
        </div>
        <div class="form-group">
            <label for="add-quiz-description">Quiz Description</label>
            <textarea class="form-control" name="quiz_description" id="add-quiz-description" rows="3">{{$quiz->description}}</textarea>
        </div>
    </div>
    <div class="modal-footer">
        <button type="button" id="updateQuiz" route="{{route('quiz.update', $id)}}" refreshURL="{{route('quiz.refresh')}}" class="btn btn-primary">Update Quiz</button>
    </div>
</form>