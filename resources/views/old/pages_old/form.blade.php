@extends('layouts.allita')

@section('content')
<div class="uk-grid">
	
	<?php /// MAKE THIS PAGE PRODUCE ANY FORM WE NEED!! :) /// ?>
	
	@if($step == 1)
	<div class="uk-width-1-1 uk-width-1-3@m uk-push-1-3" data-uk-scrollspy="{cls:'uk-animation-slide-bottom',delay:900}">	
		<h1 class="uk-margin-top">@if($formIcon)<span uk-icon="{{$formIcon}}" class="form-title-icon"></span>@else<img src="/apple-icon-57x57.png" >@endIf {{$formTitle}}</h1>
		<hr />
		<form action="{{$action}}" method="post">
			<h4 class="uk-margin-bottom">{{$formDescription}}<br /><br /></h4>
			@forEach($formRows as $formRow)
				@if($formRow->type == 'text')
				<div class="uk-form-row">
					<div class="uk-grid">
						<label for="{{$formRow->fieldName}}" class="uk-width-1-1 uk-width-1-2@m">{{$fieldLabel}}: </label>
						<input type="text" name="{{$formRow->fieldName}}" value="{{$formRow->fieldValue}}" placeholder="{{$formRow->placeholder}}" class="uk-input uk-width-1-1 uk-width-1-2@m" @if($formRow->required) REQUIRED @endIf>
					</div>
				</div>
				@elseif($formRow->type == 'password')
				<div class="uk-form-row">
					<div class="uk-grid">
						<label for="{{$formRow->fieldName}}" class="uk-width-1-1 uk-width-1-2@m">{{$fieldLabel}}: </label>
						<input type="password" name="{{$formRow->fieldName}}" value="{{$formRow->fieldValue}}" placeholder="{{$formRow->placeholder}}" class="uk-input uk-width-1-1 uk-width-1-2@m" @if($formRow->required) REQUIRED @endIf>
					</div>
				</div>
				@elseif($formRow->type == 'select')
				<div class="uk-form-row">
					<div class="uk-grid">
						<label for="{{$formRow->fieldName}}" class="uk-width-1-1 uk-width-1-2@m">{{$fieldLabel}}: </label>
						<select type="text" name="{{$formRow->fieldName}}"  placeholder="{{$formRow->placeholder}}" class="uk-select uk-width-1-1 uk-width-1-2@m" @if($formRow->required) REQUIRED @endIf>
							<option>{{$formRow->optionDisplay}}</option>
							@forEach($formRow->options as $option)
								<option value="{{$option->value}}" @if($formRow->value == $option->value) SELECTED @endIf>{{$option->optionDisplay}}</option>
							@endForEach
						</select>
					</div>
				</div>
				@elseif($formRow->type == 'checkbox')
				<div class="uk-form-row">
					<div class="uk-grid">
						<label for="{{$formRow->fieldName}}" class="uk-width-1-1 uk-width-1-2@m uk-push-1-2"><input type="checkbox" name="{{$formRow->fieldName}}" class="uk-checkbox"
								@if($formRow->required) REQUIRED @endIf @if($formRow->value == $formRow->fieldValue) CHECKED @endIf>&nbsp;
						{{$fieldLabel}}</label>

					</div>
				</div>
				@elseif($formRow->type == 'radio')
				@elseif($formRow->type == 'file')
				@elseif($formRow->type == 'textbox')
				@endif
			@endForEach
			<div class="uk-form-row">
				<div class="uk-grid">
					<button class="uk-button uk-button-default uk-width-1-1 uk-width-1-2@m uk-push-1-2" type="submit"><span uk-icon="{{$buttonIcon}}"></span> {{$buttonLabel}}</button>
				</div>
			</div>
			{{ csrf_field() }}
		</form>
		<hr class="uk-margin-large-top" />
	</div>
	@endIf


</div>
@if(strlen($message)>0)
		<script>
			UIkit.modal.alert('{{$message}}');
		</script>
@endif
@stop