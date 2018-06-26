@extends('modals.container')
<?php setlocale(LC_MONETARY, 'en_US'); ?>
@section('content')
<form action="/modals/rules/edit/" method="post">
				{{csrf_field()}}

				
	        	<div class="uk-grid">
					<div class="uk-width-1-1">
						
                        <small>EDITING:</small>
                        <input type="text" name="rules_name" class="uk-input uk-width-1-1 uk-text-large" value="{{$rule->rules_name}}">
                        <hr class="uk-margin-top-remove">

                        
					</div>
				</div>
				<div class="uk-grid">
					<div class="uk-width-3-10">
						Aquisition Advance
					</div>
					<div class="uk-width-7-10">
						<div class="uk-grid">
							<div class="uk-width-1-3">
							
								<input class="uk-checkbox" class="uk-checkbox" type="checkbox" name="acquisition_advance" @if($rule->acquisition_advance == 1) checked @endIf 
							onchange="$(this).siblings('span').fadeToggle();$(this).closest('.uk-grid').children('.show-hide-me').fadeToggle();">
								<span @if($rule->acquisition_advance != 1) style="display:none;" @endIf id="acquisition_advance_display">
									<label for="acquisition_max_advance"> Maximum</label>
								</span>
							</div>
							<div class="uk-width-2-3 show-hide-me" @if($rule->demolition_advance != 1) style="display:none;" @endIf >
								<span>
										<div class="uk-inline">
											<span uk-icon="dollar" class="uk-form-icon"></span>
											<input type="number" name="acquisition_max_advance" value="{{$rule->acquisition_max_advance}}" pattern="[0-9]+(\.[0-9]{0,2})" class="uk-input uk-form-small">
										</div>
								 	<span uk-icon="question-circle" uk-tooltip="(Enter Zero to use the Balance of Funds Available)"></span>
								</span>
							</div>
							
						</div>
						
					</div>
				</div>
				<hr class="uk-grid-divider uk-margin-small-top uk-margin-small-bottom">
				<div class="uk-grid">
					<div class="uk-width-3-10">
						NIP Loan Payoff Advance
					</div>
					<div class="uk-width-7-10">
						<div class="uk-grid">
							<div class="uk-width-1-3">
							
								<input class="uk-checkbox" type="checkbox" name="nip_loan_payoff_advance" @if($rule->nip_loan_payoff_advance == 1) checked @endIf 
							onchange="$(this).siblings('span').fadeToggle();$(this).closest('.uk-grid').children('.show-hide-me').fadeToggle();">
								<span @if($rule->nip_loan_payoff_advance != 1) style="display:none;" @endIf id="nip_loan_payoff_advance_display">
									<label for="nip_loan_payoff_max_advance"> Maximum</label>
								</span>
							</div>
							<div class="uk-width-2-3 show-hide-me" @if($rule->nip_loan_payoff_advance != 1) style="display:none;" @endIf >
								<span >
										<div class="uk-inline">
											<span uk-icon="dollar" class="uk-form-icon"></span>
											<input type="number" name="nip_loan_payoff_max_advance" value="{{$rule->nip_loan_payoff_max_advance}}" pattern="[0-9]+(\.[0-9]{0,2})" class="uk-input uk-form-small">
										</div>
								 	<span uk-icon="question-circle" class=""  uk-tooltip="(Enter Zero to use the Balance of Funds Available)"></span>
								</span>
							</div>
							
						</div>
					</div>
				</div>
				<hr class="uk-grid-divider uk-margin-small-top uk-margin-small-bottom">
				<div class="uk-grid">
					<div class="uk-width-3-10">
						Pre-Demo Advance
					</div>
					<div class="uk-width-7-10">
						<div class="uk-grid">
							<div class="uk-width-1-3">
							
								<input type="checkbox" class="uk-checkbox" name="pre_demo_advance" @if($rule->pre_demo_advance == 1) checked @endIf 
							onchange="$(this).siblings('span').fadeToggle();$(this).closest('.uk-grid').children('.show-hide-me').fadeToggle();">
								<span  @if($rule->pre_demo_advance != 1) style="display:none;" @endIf>
									<label for="pre_demo_max_advance"> Maximum</label>
								</span>
							</div>
							<div class="uk-width-2-3 show-hide-me" @if($rule->pre_demo_advance != 1) style="display:none;" @endIf >
								<span>
										<div class="uk-inline">
											<span uk-icon="dollar" class="uk-form-icon"></span>
											<input type="number" name="pre_demo_max_advance" value="{{$rule->pre_demo_max_advance}}" pattern="[0-9]+(\.[0-9]{0,2})" class="uk-input uk-form-small">
										</div>
								 	<span uk-icon="question-circle" uk-tooltip="(Enter Zero to use the Balance of Funds Available)"></span>
								</span>

							</div>
						</div>
					</div>
				</div>
				<hr class="uk-grid-divider uk-margin-small-top uk-margin-small-bottom">
				<div class="uk-grid">
					<div class="uk-width-3-10">
						Demolition Advance
					</div>
					<div class="uk-width-7-10">
						<div class="uk-grid">
							<div class="uk-width-1-3">
							
								<input type="checkbox" class="uk-checkbox" name="demolition_advance" @if($rule->demolition_advance == 1) checked @endIf 
							onchange="$(this).siblings('span').fadeToggle(); $(this).closest('.uk-grid').children('.show-hide-me').fadeToggle();">
								<span @if($rule->demolition_advance != 1) style="display:none;" @endIf id="demolition_advance_display">
									<label for="demolition_max_advance"> Maximum</label>
								</span>
							</div>
							<div class="uk-width-2-3 show-hide-me" @if($rule->demolition_advance != 1) style="display:none;" @endIf >
								
										<div class="uk-inline">
											<span uk-icon="dollar" class="uk-form-icon"></span>
											<input type="number" name="demolition_max_advance" value="{{$rule->demolition_max_advance}}" pattern="[0-9]+(\.[0-9]{0,2})" class="uk-input uk-form-small">
										</div>
								 	<span uk-icon="question-circle" uk-tooltip="(Enter Zero to use the Balance of Funds Available)"></span>
								
							</div>
							
						</div>
					</div>
				</div>
				<hr class="uk-grid-divider uk-margin-small-top uk-margin-small-bottom">
				<div class="uk-grid">
					<div class="uk-width-3-10">
						Greening Advance
					</div>
					<div class="uk-width-7-10">
						<div class="uk-grid">
							<div class="uk-width-1-3">
							
								<input type="checkbox" class="uk-checkbox" name="greening_advance" @if($rule->greening_advance == 1) checked @endIf 
							onchange="$(this).siblings('span').fadeToggle();$(this).closest('.uk-grid').children('.show-hide-me').fadeToggle();">
								<span @if($rule->greening_advance != 1) style="display:none;" @endIf id="greening_advance_display">
									<label for="greening_max_advance"> Maximum</label>
								</span>
							</div>
							<div class="uk-width-2-3 show-hide-me" @if($rule->greening_advance != 1) style="display:none;" @endIf >
								<span >
										<div class="uk-inline">
											<span uk-icon="dollar" class="uk-form-icon"></span>
											<input type="number" name="greening_max_advance" value="{{$rule->greening_max_advance}}" pattern="[0-9]+(\.[0-9]{0,2})" class="uk-input uk-form-small">
										</div>
								 	<span uk-icon="question-circle"  uk-tooltip="(Enter Zero to use the Balance of Funds Available)"></span>
								</span>
							</div>
							
						</div>
					</div>
				</div>
				<hr class="uk-grid-divider uk-margin-small-top uk-margin-small-bottom">
				<div class="uk-grid">
					<div class="uk-width-3-10">
						Maintenance Advance
					</div>
					<div class="uk-width-7-10">
						<div class="uk-grid">
							<div class="uk-width-1-3">
							
								<input type="checkbox" class="uk-checkbox" name="maintenance_advance" @if($rule->maintenance_advance == 1) checked @endIf 
							onchange="$(this).siblings('span').fadeToggle();$(this).closest('.uk-grid').children('.show-hide-me').fadeToggle();">
								<span @if($rule->maintenance_advance != 1) style="display:none;" @endIf id="maintenance_advance_display">
									<label for="maintenance_max_advance"> Maximum</label>
									</span>
							</div>
							<div class="uk-width-2-3 show-hide-me" @if($rule->maintenance_advance != 1) style="display:none;" @endIf >
								<span >
										<div class="uk-inline">
											<span uk-icon="dollar" class="uk-form-icon"></span>
											<input type="number" name="maintenance_max_advance" value="{{$rule->maintenance_max_advance}}" pattern="[0-9]+(\.[0-9]{0,2})" class="uk-input uk-form-small">
										</div>
								 	<span uk-icon="question-circle"  uk-tooltip="(Enter Zero to use the Balance of Funds Available)"></span>
								</span>
							</div>
							
						</div>
					</div>
				</div>
				<hr class="uk-grid-divider uk-margin-small-top uk-margin-small-bottom">
				<div class="uk-grid">
					<div class="uk-width-3-10">
						Admin Advance
					</div>
					<div class="uk-width-7-10">
						<div class="uk-grid">
							<div class="uk-width-1-3">
							
								<input type="checkbox" class="uk-checkbox" name="admin_advance" @if($rule->admin_advance == 1) checked @endIf 
							onchange="$(this).siblings('span').fadeToggle();$(this).closest('.uk-grid').children('.show-hide-me').fadeToggle();">
								<span @if($rule->admin_advance != 1) style="display:none;" @endIf id="admin_advance_display">
									<label for="admin_max_advance"> Maximum</label>
								</span>
							</div>
							<div class="uk-width-2-3 show-hide-me" @if($rule->admin_advance != 1) style="display:none;" @endIf >
								<span >
										<div class="uk-inline">
											<span uk-icon="dollar" class=" uk-form-icon"></span>
											<input type="number" name="admin_max_advance" value="{{$rule->admin_max_advance}}" pattern="[0-9]+(\.[0-9]{0,2})" class="uk-input uk-form-small">
										</div>
								 	<span uk-icon="question-circle" uk-tooltip="(Enter Zero to use the Balance of Funds Available)"></span>
								</span>
							</div>
							
						</div>
					</div>
				</div>
				<hr class="uk-grid-divider uk-margin-small-top uk-margin-small-bottom">
				<div class="uk-grid">
					<div class="uk-width-3-10">
						Other Advance
					</div>
					<div class="uk-width-7-10">
						<div class="uk-grid">
							<div class="uk-width-1-3">
							
								<input type="checkbox" class="uk-checkbox" name="other_advance" @if($rule->other_advance == 1) checked @endIf 
							onchange="$(this).siblings('span').fadeToggle();$(this).closest('.uk-grid').children('.show-hide-me').fadeToggle();">
								<span @if($rule->other_advance != 1) style="display:none;" @endIf id="other_advance_display">
									<label for="other_max_advance"> Maximum</label>
								</span>
							</div>
							<div class="uk-width-2-3 show-hide-me" @if($rule->other_advance != 1) style="display:none;" @endIf >
								<span >
										<div class="uk-inline">
											<span uk-icon="dollar" class="uk-form-icon"></span>
											<input type="number" name="other_max_advance" value="{{$rule->other_max_advance}}" pattern="[0-9]+(\.[0-9]{0,2})" class="uk-input uk-form-small">
										</div>
								 	<span uk-icon="question-circle"  uk-tooltip="(Enter Zero to use the Balance of Funds Available)"></span>
								</span>
							</div>
							
						</div>
					</div>
				</div>
				<hr class="uk-grid-divider uk-margin-small-top uk-margin-small-bottom">
				<div class="uk-grid">
					<div class="uk-width-3-10">
						Acquisition
					</div>
					<div class="uk-width-7-10">
						<div class="uk-grid">
							<div class="uk-width-1-3">
								<span uk-tooltip="This cannot be disabled."><input type="checkbox" checked="true" disabled="true" class="uk-checkbox" ></span> <label for="acquisition_max"> Maximum </label>
							</div>
							<div class="uk-width-2-3">
										<div class="uk-inline">
											<span uk-icon="dollar" class="uk-form-icon"></span>
											<input type="number" name="acquisition_max" value="{{$rule->acquisition_max}}" pattern="[0-9]+(\.[0-9]{0,2})" class="uk-input uk-form-small">
										</div>
								 	<span uk-icon="question-circle" uk-tooltip="(Enter Zero to use the Balance of Funds Available)"></span>
							</div>
							<div class="uk-width-1-3">
								<span uk-tooltip="This cannot be disabled."><input type="checkbox" checked="true" disabled="true" class="uk-checkbox"></span> <label for="acquisition_min"> Minimum </label>
							</div>
							<div class="uk-width-2-3">
										<div class="uk-inline">
											<span uk-icon="dollar" class="uk-form-icon"></span>
											<input type="number" name="acquisition_min" value="{{$rule->acquisition_min}}" pattern="[0-9]+(\.[0-9]{0,2})" class="uk-input uk-form-small">
										</div>
								 	<span uk-icon="question-circle"  uk-tooltip="(A Zero Here Means There is No Minimum.)"></span>
							</div>
							<div class="uk-width-1-3">	
								<span uk-tooltip="This cannot be disabled."><input type="checkbox" checked="true" disabled="true" class="uk-checkbox" ></span> <label for="acquisition_document_req_min"> Document @ </label>
							</div>
							<div class="uk-width-2-3">
										<div class="uk-inline">
											<span uk-icon="dollar" class="uk-form-icon"></span>
											<input type="number" name="acquisition_document_req_min" value="{{$rule->acquisition_document_req_min}}" pattern="[0-9]+(\.[0-9]{0,2})" class="uk-input uk-form-small">
										</div>
								 	<span uk-icon="question-circle" uk-tooltip="(You can prevent documents from being required by entering a very high number - like 9999999)"></span>
							</div>
						</div>
					</div>
				</div>
				<hr class="uk-grid-divider uk-margin-small-top uk-margin-small-bottom">


				<div class="uk-grid">
					<div class="uk-width-3-10">
						NIP Loan Payoff
					</div>
					<div class="uk-width-7-10">
						<div class="uk-grid">
							<div class="uk-width-1-3">
								<span uk-tooltip="This cannot be disabled."><input type="checkbox" checked="true" disabled="true" ></span> <label for="nip_loan_payoff_max"> Maximum </label>
							</div>
							<div class="uk-width-2-3">
										<div class="uk-inline">
											<span uk-icon="dollar" class="uk-form-icon"></span>
											<input type="number" name="nip_loan_payoff_max" value="{{$rule->nip_loan_payoff_max}}" pattern="[0-9]+(\.[0-9]{0,2})" class="uk-input uk-form-small">
										</div>
								 	<span uk-icon="question-circle"  uk-tooltip="(Enter Zero to use the Balance of Funds Available)"></span>
							</div>
							<div class="uk-width-1-3">
								<span uk-tooltip="This cannot be disabled."><input type="checkbox" checked="true" disabled="true" class="uk-checkbox" ></span> <label for="nip_loan_payoff_min"> Minimum </label>
							</div>
							<div class="uk-width-2-3">
										<div class="uk-inline">
											<span uk-icon="dollar" class="uk-form-icon"></span>
											<input type="number" name="nip_loan_payoff_min" value="{{$rule->nip_loan_payoff_min}}" pattern="[0-9]+(\.[0-9]{0,2})" class="uk-input uk-form-small">
										</div>
								 	<span uk-icon="question-circle"  uk-tooltip="(A Zero Here Means There is No Minimum.)"></span>
							</div>
							<div class="uk-width-1-3">	
								<span uk-tooltip="This cannot be disabled."><input type="checkbox" checked="true" disabled="true" class="uk-checkbox" ></span> <label for="nip_loan_payoff_document_req_min"> Document @ </label>
							</div>
							<div class="uk-width-2-3">
										<div class="uk-inline">
											<span uk-icon="dollar" class="uk-form-icon"></span>
											<input type="number" name="nip_loan_payoff_document_req_min" value="{{$rule->nip_loan_payoff_document_req_min}}" pattern="[0-9]+(\.[0-9]{0,2})" class="uk-input uk-form-small">
										</div>
								 	<span uk-icon="question-circle"  uk-tooltip="(You can prevent documents from being required by entering a very high number - like 9999999)"></span>
							</div>
						</div>
					</div>
				</div>
				<hr class="uk-grid-divider uk-margin-small-top uk-margin-small-bottom">


				<div class="uk-grid">
					<div class="uk-width-3-10">
						Pre-Demo
					</div>
					<div class="uk-width-7-10">
						<div class="uk-grid">
							<div class="uk-width-1-3">
								<span uk-tooltip="This cannot be disabled."><input type="checkbox" checked="true" disabled="true" ></span> <label for="pre_demo_max"> Maximum </label>
							</div>
							<div class="uk-width-2-3">
										<div class="uk-inline">
											<span uk-icon="dollar" class="uk-form-icon"></span>
											<input type="number" name="pre_demo_max" value="{{$rule->pre_demo_max}}" pattern="[0-9]+(\.[0-9]{0,2})" class="uk-input uk-form-small">
										</div>
								 	<span uk-icon="question-circle"  uk-tooltip="(Enter Zero to use the Balance of Funds Available)"></span>
							</div>
							<div class="uk-width-1-3">
								<span uk-tooltip="This cannot be disabled."><input type="checkbox" checked="true" disabled="true" class="uk-checkbox" ></span> <label for="pre_demo_min"> Minimum </label>
							</div>
							<div class="uk-width-2-3">
										<div class="uk-inline">
											<span uk-icon="dollar" class="uk-form-icon"></span>
											<input type="number" name="pre_demo_min" value="{{$rule->pre_demo_min}}" pattern="[0-9]+(\.[0-9]{0,2})" class="uk-input uk-form-small">
										</div>
								 	<span uk-icon="question-circle"  uk-tooltip="(A Zero Here Means There is No Minimum.)"></span>
							</div>
							<div class="uk-width-1-3">	
								<span uk-tooltip="This cannot be disabled."><input type="checkbox" checked="true" disabled="true" ></span> <label for="pre_demo_document_req_min"> Document @ </label>
							</div>
							<div class="uk-width-2-3">
										<div class="uk-inline">
											<span uk-icon="dollar" class="uk-form-icon"></span>
											<input type="number" name="pre_demo_document_req_min" value="{{$rule->pre_demo_document_req_min}}" pattern="[0-9]+(\.[0-9]{0,2})" class="uk-input uk-form-small">
										</div>
								 	<span uk-icon="question-circle"  uk-tooltip="(You can prevent documents from being required by entering a very high number - like 9999999)"></span>
							</div>
						</div>
					</div>
				</div>
				<hr class="uk-grid-divider uk-margin-small-top uk-margin-small-bottom">


				<div class="uk-grid">
					<div class="uk-width-3-10">
						Demolition
					</div>
					<div class="uk-width-7-10">
						<div class="uk-grid">
							<div class="uk-width-1-3">
								<span uk-tooltip="This cannot be disabled."><input type="checkbox" class="uk-checkbox" checked="true" disabled="true" ></span> <label for="demolition_max"> Maximum </label>
							</div>
							<div class="uk-width-2-3">
										<div class="uk-inline">
											<span uk-icon="dollar" class="uk-form-icon"></span>
											<input type="number" name="demolition_max" value="{{$rule->demolition_max}}" pattern="[0-9]+(\.[0-9]{0,2})" class="uk-input uk-form-small">
										</div>
								 	<span uk-icon="question-circle"  uk-tooltip="(Enter Zero to use the Balance of Funds Available)"></span>
							</div>
							<div class="uk-width-1-3">
								<span uk-tooltip="This cannot be disabled."><input type="checkbox" checked="true" disabled="true" ></span> <label for="demolition_min"> Minimum </label>
							</div>
							<div class="uk-width-2-3">
										<div class="uk-inline">
											<span uk-icon="dollar" class="uk-form-icon"></span>
											<input type="number" name="demolition_min" value="{{$rule->demolition_min}}" pattern="[0-9]+(\.[0-9]{0,2})" class="uk-input uk-form-small">
										</div>
								 	<span uk-icon="question-circle"  uk-tooltip="(A Zero Here Means There is No Minimum.)"></span>
							</div>
							<div class="uk-width-1-3">	
								<span uk-tooltip="This cannot be disabled."><input type="checkbox" checked="true" disabled="true" ></span> <label for="demolition_document_req_min"> Document @ </label>
							</div>
							<div class="uk-width-2-3">
										<div class="uk-inline">
											<span uk-icon="dollar" class="uk-form-icon"></span>
											<input type="number" name="demolition_document_req_min" value="{{$rule->demolition_document_req_min}}" pattern="[0-9]+(\.[0-9]{0,2})" class="uk-input uk-form-small">
										</div>
								 	<span uk-icon="question-circle"  uk-tooltip="(You can prevent documents from being required by entering a very high number - like 9999999)"></span>
							</div>
						</div>
					</div>
				</div>
				<hr class="uk-grid-divider uk-margin-small-top uk-margin-small-bottom">


				<div class="uk-grid">
					<div class="uk-width-3-10">
						Greening
					</div>
					<div class="uk-width-7-10">
						<div class="uk-grid">
							<div class="uk-width-1-3">
								<span uk-tooltip="This cannot be disabled."><input type="checkbox" class="uk-checkbox" checked="true" disabled="true" ></span> <label for="greening_max"> Maximum </label>
							</div>
							<div class="uk-width-2-3">
										<div class="uk-inline">
											<span uk-icon="dollar" class="uk-form-icon"></span>
											<input type="number" name="greening_max" value="{{$rule->greening_max}}" pattern="[0-9]+(\.[0-9]{0,2})" class="uk-input uk-form-small">
										</div>
								 	<span uk-icon="question-circle"  uk-tooltip="(Enter Zero to use the Balance of Funds Available)"></span>
							</div>
							<div class="uk-width-1-3">
								<span uk-tooltip="This cannot be disabled."><input type="checkbox" checked="true" disabled="true" ></span> <label for="greening_min"> Minimum </label>
							</div>
							<div class="uk-width-2-3">
										<div class="uk-inline">
											<span uk-icon="dollar" class="uk-form-icon"></span>
											<input type="number" name="greening_min" value="{{$rule->greening_min}}" pattern="[0-9]+(\.[0-9]{0,2})" class="uk-input uk-form-small">
										</div>
								 	<span uk-icon="question-circle"  uk-tooltip="(A Zero Here Means There is No Minimum.)"></span>
							</div>
							<div class="uk-width-1-3">	
								<span uk-tooltip="This cannot be disabled."><input type="checkbox" class="uk-checkbox" checked="true" disabled="true" ></span> <label for="greening_document_req_min"> Document @ </label>
							</div>
							<div class="uk-width-2-3">
										<div class="uk-inline">
											<span uk-icon="dollar" class="uk-form-icon"></span>
											<input type="number" name="greening_document_req_min" value="{{$rule->greening_document_req_min}}" pattern="[0-9]+(\.[0-9]{0,2})" class="uk-input uk-form-small">
										</div>
								 	<span uk-icon="question-circle"  uk-tooltip="(You can prevent documents from being required by entering a very high number - like 9999999)"></span>
							</div>
						</div>
					</div>
				</div>
				<hr class="uk-grid-divider uk-margin-small-top uk-margin-small-bottom">


				<div class="uk-grid">
					<div class="uk-width-3-10">
						Maintenance
					</div>
					<div class="uk-width-7-10">
						<div class="uk-grid">
							<div class="uk-width-1-3">
								<span uk-tooltip="This cannot be disabled."><input type="checkbox" class="uk-checkbox" checked="true" disabled="true" ></span> <label for="maintenance_max"> Maximum </label>
							</div>
							<div class="uk-width-2-3">
										<div class="uk-inline">
											<span uk-icon="dollar" class="uk-form-icon"></span>
											<input type="number" name="maintenance_max" value="{{$rule->maintenance_max}}" pattern="[0-9]+(\.[0-9]{0,2})" class="uk-input uk-form-small">
										</div>
								 	<span uk-icon="question-circle"  uk-tooltip="(Enter Zero to use the Balance of Funds Available)"></span>
							</div>
							<div class="uk-width-1-3">
								<span uk-tooltip="This cannot be disabled."><input type="checkbox" checked="true" disabled="true" ></span> <label for="maintenance_min"> Minimum </label>
							</div>
							<div class="uk-width-2-3">
										<div class="uk-inline">
											<span uk-icon="dollar" class="uk-form-icon"></span>
											<input type="number" name="maintenance_min" value="{{$rule->maintenance_min}}" pattern="[0-9]+(\.[0-9]{0,2})" class="uk-input uk-form-small">
										</div>
								 	<span uk-icon="question-circle"  uk-tooltip="(A Zero Here Means There is No Minimum.)"></span>
							</div>
							<div class="uk-width-1-3">	
								<span uk-tooltip="This cannot be disabled."><input type="checkbox" class="uk-checkbox" checked="true" disabled="true" ></span> <label for="maintenance_document_req_min"> Document @ </label>
							</div>
							<div class="uk-width-2-3">
										<div class="uk-inline">
											<span uk-icon="dollar" class="uk-form-icon"></span>
											<input type="number" name="maintenance_document_req_min" value="{{$rule->maintenance_document_req_min}}" pattern="[0-9]+(\.[0-9]{0,2})" class="uk-input uk-form-small">
										</div>
								 	<span uk-icon="question-circle"  uk-tooltip="(You can prevent documents from being required by entering a very high number - like 9999999)"></span>
							</div>
						</div>
					</div>
				</div>
				<hr class="uk-grid-divider uk-margin-small-top uk-margin-small-bottom">


				<div class="uk-grid">
					<div class="uk-width-3-10">
						Admin
					</div>
					<div class="uk-width-7-10">
						<div class="uk-grid">
							<div class="uk-width-1-3">
								<span uk-tooltip="This cannot be disabled."><input type="checkbox" class="uk-checkbox" checked="true" disabled="true" ></span> <label for="admin_max_percent"> Maximum </label>
							</div>
							<div class="uk-width-2-3">
										<div class="uk-inline">
											<span uk-icon="percent" class=" uk-form-icon"></span>
											<input type="number" name="admin_max_percent" value="{{$rule->admin_max_percent *100}}" pattern="[0-9]+(\.[0-9]{0,2})" class="uk-input uk-form-small">
										</div>
								 	<span uk-icon="question-circle"  uk-tooltip="(Enter Zero to use the Balance of Funds Available)"></span>
							</div>
							<div class="uk-width-1-3">
								<span uk-tooltip="This cannot be disabled."><input type="checkbox" checked="true" disabled="true" ></span> <label for="admin_min"> Minimum </label>
							</div>
							<div class="uk-width-2-3">
										<div class="uk-inline">
											<span uk-icon="dollar" class="uk-form-icon"></span>
											<input type="number" name="admin_min" value="{{$rule->admin_min}}" pattern="[0-9]+(\.[0-9]{0,2})" class="uk-input uk-form-small">
										</div>
								 	<span uk-icon="question-circle"  uk-tooltip="(A Zero Here Means There is No Minimum.)"></span>
							</div>
							<div class="uk-width-1-3">	
								<span uk-tooltip="This cannot be disabled."><input type="checkbox" class="uk-checkbox" checked="true" disabled="true" ></span> <label for="admin_document_req_min"> Document @ </label>
							</div>
							<div class="uk-width-2-3">
										<div class="uk-inline">
											<span uk-icon="dollar" class="uk-form-icon"></span>
											<input type="number" name="admin_document_req_min" value="{{$rule->admin_document_req_min}}" pattern="[0-9]+(\.[0-9]{0,2})" class="uk-input uk-form-small">
										</div>
								 	<span uk-icon="question-circle"  uk-tooltip="(You can prevent documents from being required by entering a very high number - like 9999999)"></span>
							</div>
						</div>
					</div>
				</div>
				<hr class="uk-grid-divider uk-margin-small-top uk-margin-small-bottom">



				<div class="uk-grid">
					<div class="uk-width-3-10">
						Other
					</div>
					<div class="uk-width-7-10">
						<div class="uk-grid">
							<div class="uk-width-1-3">
								<span uk-tooltip="This cannot be disabled."><input type="checkbox" class="uk-checkbox" checked="true" disabled="true" ></span> <label for="other_max"> Maximum </label>
							</div>
							<div class="uk-width-2-3">
										<div class="uk-inline">
											<span uk-icon="dollar" class="uk-form-icon"></span>
											<input type="number" name="other_max" value="{{$rule->other_max}}" pattern="[0-9]+(\.[0-9]{0,2})" class="uk-input uk-form-small">
										</div>
								 	<span uk-icon="question-circle"  uk-tooltip="(Enter Zero to use the Balance of Funds Available)"></span>
							</div>
							<div class="uk-width-1-3">
								<span uk-tooltip="This cannot be disabled."><input type="checkbox" class="uk-checkbox" checked="true" disabled="true" ></span> <label for="other_min"> Minimum </label>
							</div>
							<div class="uk-width-2-3">
										<div class="uk-inline">
											<span uk-icon="dollar" class="uk-form-icon"></span>
											<input type="number" name="other_min" value="{{$rule->other_min}}" pattern="[0-9]+(\.[0-9]{0,2})" class="uk-input uk-form-small">
										</div>
								 	<span uk-icon="question-circle"  uk-tooltip="(A Zero Here Means There is No Minimum.)"></span>
							</div>
							<div class="uk-width-1-3">	
								<span uk-tooltip="This cannot be disabled."><input type="checkbox" class="uk-checkbox" checked="true" disabled="true" ></span> <label for="other_document_req_min"> Document @ </label>
							</div>
							<div class="uk-width-2-3">
										<div class="uk-inline">
											<span uk-icon="dollar" class="uk-form-icon"></span>
											<input type="number" name="other_document_req_min" value="{{$rule->other_document_req_min}}" pattern="[0-9]+(\.[0-9]{0,2})" class="uk-input uk-form-small">
										</div>
								 	<span uk-icon="question-circle"  uk-tooltip="(You can prevent documents from being required by entering a very high number - like 9999999)"></span>
							</div>
						</div>
					</div>
				</div>
				<hr class="uk-grid-divider uk-margin-small-top uk-margin-small-bottom">


				<div class="uk-grid">
					<div class="uk-width-3-10">
						Total Reimbursement
					</div>
					<div class="uk-width-7-10">
						<div class="uk-grid">
							<div class="uk-width-1-3">
								<span uk-tooltip="This cannot be disabled."><input type="checkbox" class="uk-checkbox" checked="true" disabled="true" ></span> <label for="parcel_total_max"> Maximum </label>
							</div>
							<div class="uk-width-2-3">
										<div class="uk-inline">
											<span uk-icon="dollar" class="uk-form-icon"></span>
											<input type="number" name="parcel_total_max" value="{{$rule->parcel_total_max}}" pattern="[0-9]+(\.[0-9]{0,2})" class="uk-input uk-form-small">
										</div>
								 	<span uk-icon="question-circle"  uk-tooltip="(This is the Total Amount for All Expenses on a Parcel."></span>
							</div>
						</div>
					</div>
				</div>
				<hr class="uk-grid-divider uk-margin-small-top uk-margin-small-bottom">
				<div class="uk-grid">
					<div class="uk-width-3-10">
						Maintenance Recapture Prorate
					</div>
					<div class="uk-width-7-10">
						<div class="uk-grid">
							<div class="uk-width-1-3">
								<span uk-tooltip="This cannot be disabled."><input type="checkbox" class="uk-checkbox" checked="true" disabled="true" ></span> <label for="maintenance_recap_pro_rate"> Per Month </label>
							</div>
							<div class="uk-width-2-3">
										<div class="uk-inline">
											<span uk-icon="percent" class=" uk-form-icon"></span>
											<input type="number" name="maintenance_recap_pro_rate" value="{{$rule->maintenance_recap_pro_rate}}" pattern="[0-9]+(\.[0-9]{0,2})" class="uk-input uk-form-small">
										</div>
								 	<span uk-icon="question-circle"  uk-tooltip="(This is the maintenance recapture rate for unused months when a parcel is disposed."></span>
							</div>
						</div>
						
							
						
					</div>
				</div>
				<hr class="uk-grid-divider uk-margin-small-top uk-margin-small-bottom">
				<div class="uk-grid">
					<div class="uk-width-3-10">
						Imputed Cost (Dispositions)
					</div>
					<div class="uk-width-7-10">
						<div class="uk-grid">
							<div class="uk-width-1-3">
								<span uk-tooltip="This cannot be disabled."><input type="checkbox" class="uk-checkbox" checked="true" disabled="true" ></span> <label for="imputed_cost_per_parcel"> Per Parcel </label>
							</div>
							<div class="uk-width-2-3">
										<div class="uk-inline">
											<span uk-icon="dollar" class="uk-form-icon"></span>
											<input type="number" name="imputed_cost_per_parcel" value="{{$rule->imputed_cost_per_parcel}}" pattern="[0-9]+(\.[0-9]{0,2})" class="uk-input uk-form-small">
										</div>
								 	<span uk-icon="question-circle"  uk-tooltip="(This is the amount landbanks keep by default when a parcel is disposed."></span>
							</div>
						</div>
						
					</div>
				</div>
				<hr class="uk-grid-divider uk-margin-small-top uk-margin-small-bottom">
				@if(strlen($rule->required_document_categories)>0)
				<div class="uk-grid">
					<div class="uk-width-3-10">
						Required Documents
					</div>
					<div class="uk-width-7-10">
						SCRIPT TO PROCESS DOCUMENT CATEGORIES!
							
						
					</div>
				</div>
				<hr class="uk-grid-divider uk-margin-small-top uk-margin-small-bottom">
				@endIf
				@if(strlen($rule->notes)>0)
				<div class="uk-grid">
					<div class="uk-width-3-10">
						Notes About These Rules
					</div>
					<div class="uk-width-7-10">
						{{$rule->notes}}
							
						
					</div>
				</div>
				<hr class="uk-grid-divider uk-margin-small-top uk-margin-small-bottom">
				@endIf
				
			</form>
@stop