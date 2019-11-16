<div uk-grid>
	<div class="uk-width-2-3">
		<ul class="leaders" style="margin-right:30px;">
			<li><span>Total Buildings</span> <span>{{ $details->total_building }}</span></li>
			<li style="display:none"><span class="indented">Total Building Common Areas</span> <span>{{ $details->total_building_common_areas }}</span></li>
			<li style="display:none"><span class="indented">Total Building Systems</span> <span>{{ $details->total_building_systems }}</span></li>
			<li style="display:none"><span class="indented">Total Building Exteriors</span> <span>{{ $details->total_building_exteriors }}</span></li>
			<li style="display:none"><span>Total Project Common Areas</span> <span></span></li>
			<li><a  onclick="$('#units-summary-header').scrollView();" class="uk-link-mute" ><span>Total Units</span> <span>{{ $details->total_units }}</span></a></li>
			<li><span class="indented">• Market Rate Units</span> <span>{{ $details->market_rate }}</span></li>
			<li><span class="indented">• Program Units</span> <span>{{ $details->subsidized }}</span></li>
			<li><span>Total Programs</span> <span></span></li>
			@foreach(json_decode($details->programs, true) as $program)
				<li><span class="indented">• {{ $program['name'] }}</span> <span>{{ $program['units'] }}</span></li>
			@endforeach
		</ul>
	</div>
	<div class="uk-width-1-3">
		<h5 class="uk-margin-remove"><strong>OWNER: {{ $details->owner_name }}</strong></h5>
		<div class="address" style="margin-bottom:20px;">
			<i class="a-avatar"></i> {{ $details->owner_poc }}<br />
			<i class="a-phone-5"></i> {{ $details->owner_phone }} @if($details->owner_fax != '')<i class="a-fax-2" style="margin-left:10px"></i> {{ $details->owner_fax }} @endif<br />
			<i class="a-mail-send"></i> {{ $details->owner_email }}<br />
			@if($details->owner_address)<i class="a-mailbox"></i> {{ $details->owner_address }} @endif
		</div>
		<h5 class="uk-margin-remove"><strong>Managed By: {{ $details->manager_name }}</strong></h5>
		<div class="address">
			<i class="a-avatar"></i> {{ $details->manager_poc }}<br />
			@if($details->manager_phone)<i class="a-phone-5"></i> {{ $details->manager_phone }} @if($details->manager_fax != '')<i class="a-fax-2" style="margin-left:10px"></i> {{ $details->manager_fax }} @endif<br />@endif
			@if($details->manager_email)<i class="a-mail-send"></i> {{ $details->manager_email }}<br /> @endif
			@if($details->manager_address)<i class="a-mailbox"></i> {{ $details->manager_address ? $details->manager_address . ', ' : '' }} {{ $details->manager_address2 ? $details->manager_address2 . ', ' : '' }} {{ $details->manager_city ? $details->manager_city . ', ' : '' }} {{ $details->manager_state ? $details->manager_state : '' }} {{ $details->manager_zip ? ' - ' . $details->manager_zip : '' }} @endif
		</div>
	</div>
</div>