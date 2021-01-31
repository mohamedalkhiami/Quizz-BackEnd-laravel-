<form action="#" class="" id="addQuestionForm">
	<fieldset class="row setup-content">
		<div class="modal-body" id="addQuestionFormControls">
			<div class="form-group">
				<label for="questions_01" class="col-form-label">Question <?php echo e($qnno); ?>:</label>
				<textarea class="form-control" rows="4"  name="question" id="question"></textarea>
				<input type="hidden" name="qnno" value="<?php echo e($qnno); ?>" />
			</div>
			<div class="input-group mb-3">
				<div class="input-group-prepend">
					<label class="input-group-text" for="answer-type">Answer Type</label>
				</div>
				<select class="custom-select answer-type" name="category" id="answer-type">
					<option value="">Select Answer Type</option>
					<option value="single">Single Answer </option>
					<option value="multiple">Multiple Answer </option>
				</select>
			</div>

			<div id="answersArea"></div>
			
			<div class="form-group">
				<div class="input-group mb-3">
					<input type="text" id="answerBody" class="form-control add-answer-input">
					<div class="input-group-append">
						<button class="btn btn-primary add-answer-button" type="button" id="addAnswer">Add Answer Option</button>
					</div>
				</div>
			</div>

			<div class="form-group">
				<div class="input-group">
					<button id="attachPhoto" type="button" class="btn btn-success"><i class="fa fa-paperclip"></i> Attach Photo</button>
					<input id="attachedPhoto" type="file" name="attachedPhoto" hidden />
					
				</div>
			</div>

			<hr/>

			<div id="photoAttachment"></div>

		</div>
		<div class="modal-footer d-flex justify-content-between">
			<button id="saveQn" route="<?php echo e(route('quiz.store.questions', $id)); ?>" disabled="true" type="button" class="btn btn-primary btn-action">Save and Quit</button>
			<button id="saveAndContQn" route="<?php echo e(route('quiz.storeAndContinue.questions', $id)); ?>" disabled="true" class="btn btn-action nextBtn btn-success" type="button">Save and Continue</button>
		</div>
	</fieldset>
</form>

<script src="<?php echo e(url('js/biggo.js')); ?>"></script>
<script type="text/javascript">
	$(function() {
		Biggo.imageUploadDisplay('attachedPhoto', 'photoAttachment', 240, 240)
	});
</script>

