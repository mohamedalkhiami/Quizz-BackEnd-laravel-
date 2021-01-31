<?php $q = \App\Question::find($id); ?>

<div style="padding: 10px" id="qnx{{$id}}">
<h5 class="d-flex"><span class="question-number">{{$q->qn_no < 10 ? '0'.$q->qn_no : $q->qn_no}}.</span> <span>
			
		</span></h5>
		<textarea class="form-control" style="width: 100%" rows="4">{{$q->question}}</textarea><br/>
		<div class="answers-option ml-5 ">
			<?php
				$answers = \App\Answer::where('question_id', $q->id)->get();
			?>
			@foreach($answers as $a)

			<?php $checked = $a->correct == 0 ? '' : 'checked="true"'; ?>

			@if($q->category == "single")
			<div class="" style="display: flex; justify-content: space-between;" id="answerDel{{$a->id}}">
				<input type="radio"  {!! $checked !!} name="customRadio_{{$q->id}}" class="editRadioAnswer"> 
				<label style="width: 100%" class="{{ $a->correct == 0 ? '' : 'text-success' }}" for="question{{$q->id}}_answer{{$a->id}}">
					<textarea class="form-control"  rows="2">{{$a->answer}}</textarea><br/>
				</label>
				<span class="deleteEditAnwr" routeEd="{{route('question.edit', $id)}}" qn={{$id}} aid="{{$a->id}}" quizid="{{$quizid}}" route="{{route('question.answer.delete',$a->id)}}"> <i  style="margin-left: 12px;cursor: pointer;" class="fa fa-trash text-danger"></i></span>
			</div>
			@endif
			@if($q->category == "multiple")
			<div class="" style="display: flex; justify-content: space-between;">
				<input type="checkbox" class="" {!! $checked !!} id="question{{$q->id}}_answer{{$a->id}}">
				<label style="width: 100%" class="{{ $a->correct == 0 ? '' : 'text-success' }}" for="question{{$q->id}}_answer{{$a->id}}">
					<textarea class="form-control"  rows="2">{{$a->answer}}</textarea><br/>
				</label>
				<span class="deleteEditAnwr" routeEd="{{route('question.edit', $id)}}" qn={{$id}} aid="{{$a->id}}" quizid="{{$quizid}}" route="{{route('question.answer.delete',$a->id)}}"> <i  style="margin-left: 12px;cursor: pointer;" class="fa fa-trash text-danger"></i></span> 	
			</div>
			@endif
			@endforeach

			<div id="answersAreaX"></div>
			
			<div class="form-group">
				<div class="input-group mb-3">
					<input type="text" id="answerBody" class="form-control add-answer-input">
					<div class="input-group-append">
						<button class="btn btn-success add-answer-button" type="button" id="addAnswerX"><i class="fa fa-plus"></i> Add Answer Option</button>
					</div>
				</div>
			</div>
			
			<hr/>
			<p class="well"> <span style="cursor: pointer;" qn="{{$q->id}}" quizid="{{$quizid}}" class="updateQn" route="{{route('question.update',$q->id)}}"><i class="fa fa-save text-info"></i> Update </span> |  <span style="cursor: pointer;" quizid="{{$quizid}}" qn="{{$q->id}}" class="cancelQn" route="{{route('quiz.cancel',$q->id)}}"><i class="fa fa-undo text-danger"></i> Cancel </span></p>
			
		</div>
</div>

