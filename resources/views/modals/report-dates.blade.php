<script>
		resizeModal(65);
</script>
<div class="modal-report-dates">
	<h2 class="uk-text-uppercase uk-text-emphasis">Change Report Dates</h2>
	<hr class="dashed-hr uk-column-span uk-margin-bottom uk-margin-top">
	<div class="alert alert-danger uk-text-danger" style="display:none"></div>
	<form id="reportDatesForm" action="{{ route('admin.createuser') }}" method="post" role="userForm">
		<input type="hidden" name="_token" value="{{ csrf_token() }}">
		<div class="uk-grid">
			<div class="uk-width-1-1 uk-margin-top">
				<label for="name">Date of Letter<span class="uk-text-danger uk-text-bold">*</span> :</label>
				<div class="uk-width-1-2">
					<input id="date-of-letter" value="{{ !is_null($report->letter_date) ? Carbon\Carbon::parse(strtotime($report->letter_date))->format('F j, Y') : '' }}" type="text" class="uk-input" name="letter_date" placeholder="Select Date">
				</div>
			</div>
			<div class="uk-width-1-1 uk-margin-top">
				<label for="name">Date of Review<span class="uk-text-danger uk-text-bold">*</span> :</label>
				<div class="uk-width-1-2">
					<input id="date-of-inspection" value="{{ !is_null($report->review_date) ? Carbon\Carbon::parse(strtotime($report->review_date))->format('F j, Y') : '' }}" type="text" class="uk-input" name="review_date" placeholder="Select Date">
				</div>
			</div>
			<div class="uk-width-1-1 uk-margin-top">
				<label for="name">Response Due Date :</label>
				<div class="uk-width-1-2">
					<input id="response-date" value="{{ !is_null($report->response_due_date) ? Carbon\Carbon::parse(strtotime($report->response_due_date))->format('F j, Y') : '' }}" type="text" class="uk-input" name="response_due_date" placeholder="Select Date">
				</div>
			</div>
		</div>
		<div class="uk-grid" uk-grid="">
			<div class="uk-width-1-4 uk-padding-remove-left uk-first-column">
				<a class="uk-button uk-button-default uk-width-1-1" onclick="dynamicModalClose()"><span uk-icon="times-circle" class="uk-icon"></span> CANCEL</a>
			</div>
			<div class="uk-width-1-4 ">
				<a id="dates_save_button" class="uk-button uk-width-1-1 uk-button uk-button-success" onclick="submitUserInfoForm()"><span uk-icon="save" class="uk-icon"></span> SAVE</a>
			</div>
		</div>
	</form>

	<script>
		// flatpickr.defaultConfig.animate = window.navigator.userAgent.indexOf('MSIE') === -1;
		flatpickr('#date-of-letter', {
			altFormat: "F j, Y",
			dateFormat: "F j, Y",
			// "minDate": new Date().fp_incr(1),
			"locale": {
        "firstDayOfWeek": 1 // start week on Monday
      },
    });

		flatpickr('#date-of-inspection', {
			altFormat: "F j, Y",
			dateFormat: "F j, Y",
			"locale": {
        "firstDayOfWeek": 1 // start week on Monday
      },
    });

		flatpickr('#response-date', {
			altFormat: "F j, Y",
			dateFormat: "F j, Y",
			"locale": {
        "firstDayOfWeek": 1 // start week on Monday
      },
    });


  </script>
</div>
