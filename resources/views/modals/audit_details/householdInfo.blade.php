
						<?php //dd($unit); ?>

			<div uk-grid>
				<div class="uk-width-1-1">
					<h2>Unit: {{$unit->unit_name}}</h2>
					<hr class="dashed-hr uk-width-1-1 uk-margin-bottom">
						<div id="unit-{{$unit->id}}" >
							<ul>
							@if($unit->household->head_of_household_name)
							<li>Tenant: <strong>{{$unit->household->head_of_household_name}}</strong> </li>
							@endIf
							@if($unit->most_recent_event())
							<li>Most Recent Event: <strong>{{date('n/d/Y',strtotime($unit->most_recent_event()->event_date))}} : {{$unit->most_recent_event()->type->event_type_description}} </strong></li>
							@endIf
							@if($unit->household->initial_move_in_date)
							 <li>Initial Move In Date: <strong>{{date('l n/j/Y',strtotime($unit->household->initial_move_in_date))}}</strong> </li>
							@endIf
							@if($unit->household->special_needs_id)
							<li>Special Needs: <strong>{{$unit->household->special_needs->special_needs_description}} ({{$unit->household->special_needs->special_needs_code}})</strong></li>
							@endIf
							@if(null !== $unit->household->household_income_move_in)
							<li>Household Move In Income: <strong>${{number_format($unit->household->household_income_move_in)}}</strong></li>
							@endIf
							@if($unit->most_recent_event())
							<li>Current Income: <strong>${{number_format($unit->most_recent_event()->current_income)}} </strong>
							</li>
							@endIf

							@if($unit->household->household_size_id)
							<li>Household Size: <strong>{{$unit->household->household_size->household_size_description}} </strong><br > &nbsp;(at move in: {{$unit->household->move_in_household_size->household_size_description}})</li>
							@endIf
							@if($unit->most_recent_event())
							<li>Household Count: <strong>{{number_format($unit->most_recent_event()->household_count)}} </strong></li>
							@endIf
							
							@if($unit->most_recent_event())
							<li>Tenant Rent Portion: <strong>${{number_format($unit->most_recent_event()->tenant_rent_portion)}} </strong>  </li>
							@endIf
							@if($unit->most_recent_event())
							<li>Rental Assistance Amount: <strong>${{number_format($unit->most_recent_event()->rent_assistance_amount)}} </strong>@if($unit->most_recent_event()->rental_assistance_type_id) ({{$unit->most_recent_event()->rent_assistance_type->rental_assistance_type_name}})@endIf</li>
							@endIf
							@if($unit->most_recent_event())
							<li>Utility Allowance: <strong>${{number_format($unit->most_recent_event()->utility_allowance)}} </strong></li>
							@endIf


							</ul>
						</div>
						