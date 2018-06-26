<?php setlocale(LC_MONETARY, 'en_US'); ?>
<div id="dynamic-modal-content" class="">
	<script>
	resizeModal(60);
	</script>
	<div class="uk-form-row">
		<div class="uk-button uk-button-default uk-form-select uk-width-1-1 uk-button-large" data-uk-form-select>
			<span>{{$categoryname}}</span>
			<i uk-icon="caret-down"></i>
		    <select class="uk-select" id="category-select-id" onchange="updateCategoryDetails($('#category-select-id').val())">
		    	@foreach($expense_categories as $expense_category)
				<option name="categoryid" value="{{$expense_category->id}}" @if($expense_category->id==$categoryid) selected @endif>{{$expense_category->expense_category_name}}</option>
				@endforeach
		    </select>
		</div>
	</div>
	<div class="uk-form-row">
        <label>INCLUDING ZERO VALUES <input class="uk-checkbox" type="checkbox" name="zero_values" id="zero_values" value="1"  onclick="updateCategoryDetails($('#category-select-id').val())"></label>
    </div>

	<h3>Averages <small>({{$source}})</small></h3>
	<table class="uk-table uk-table-hover uk-table-striped">
		<thead>
			<tr>
				@if($parcelid)
				<th>
					This Parcel
				</th>
				@endif
				<th>
					This Program
				</th>
				<th>
					Overall
				</th>
			</tr>
		</thead>
		<tbody>
			<tr>
				@if($parcelid)
				<td id="av_pa">{{$amount_for_parcel}}</td>
				@endif
				<td id="av_pr">{{$amount_for_program_average}}</td>
				<td id="av_ov">{{$amount_for_entity_average}}</td>
			</tr>
		</tbody>
	</table>

	<h3>Medians</h3>
	<table class="uk-table uk-table-hover uk-table-striped">
		<thead>
			<tr>
				@if($parcelid)
				<th>
					This Parcel
				</th>
				@endif
				<th>
					This Program
				</th>
				<th>
					Overall
				</th>
			</tr>
		</thead>
		<tbody>
			<tr>
				@if($parcelid)
				<td id="me_pa">{{$amount_for_parcel}}</td>
				@endif
				<td id="me_pr">{{$amount_for_program_median}}</td>
				<td id="me_ov">{{$amount_for_entity_median}}</td>
			</tr>
		</tbody>
	</table>
</div>	
<script type="text/javascript">
	function updateCategoryDetails(categoryId){
		var zero_values = 0;
		if($( "input[name='zero_values']:checked" ).val() == 1) zero_values = 1;
		$.get('/modals/expense-categories-details/1/'+categoryId+'/{{$programid}}/{{$parcelid}}/'+zero_values,
                function( data ) {
	                $("#av_pa").html(data['amount_for_parcel']);
	                $("#av_pr").html(data['amount_for_program_average']);
	                $("#av_ov").html(data['amount_for_entity_average']);
	                $("#me_pa").html(data['amount_for_parcel']);
	                $("#me_pr").html(data['amount_for_program_median']);
	                $("#me_ov").html(data['amount_for_entity_median']);
	               
		} );
	}

</script>