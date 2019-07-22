
<div  id="contacts-content">
	<div class="uk-width-1-1 uk-align-center	" style="width: 90%">
		<a name="organizationtop"></a>
		<div class="uk-overflow-container uk-margin-top">
			<div uk-grid class="uk-margin-remove">
				<h4 class="uk-text-left uk-width-2-3" style="padding-top: 8px;">{{ number_format($users->total(), 0) }} TOTAL USERS</h4>
			</div>
			<hr>
			{{ $users->links() }} <a onClick="dynamicModalLoad('{{ $project->id }}/add-user-to-project');" class="uk-badge uk-margin-top uk-badge-success"><i class="a-circle-plus"></i> ADD USER TO PROJECT</a>
			<table class="uk-table uk-table-hover uk-table-striped uk-overflow-container small-table-text">
				<thead>
					<th>
						<small>NAME</small>
					</th>
					<th>
						<small>ORGANIZATION</small>
					</th>
					<th>
						<small>ADDRESS</small>
					</th>
					<th>
						<small>PHONE</small>
					</th>
					<th>
						<small>EMAIL</small>
					</th>
					<th>
						<small>REPORT ACCESS</small>
					</th>
				</thead>
				<tbody>
					@foreach($users as $user)
					<tr class="{{ !$user->active ? 'uk-text-muted' : '' }}">
						<td>{{ $user->first_name }} {{ $user->last_name }}<br /><small>
							@if($user->role){{strtoupper($user->role->role->role_name)}}@else NO ACCESS @endIf</small>
						</td>
						<td><small>@if($user->organization_name)
							{{ $user->organization_details->organization_name }}@else NA @endif</small>
						</td>
						<td>
							<small>@if($user->has_address())
								<a target="_blank" href="https://www.google.com/maps?q={{ urlencode(str_replace('<br />',' ',$user->organization_details->address->formatted_address())) }}" class="uk-text-muted uk-align-left"><i class="a-marker-basic uk-text-muted uk-link"></i></a>
								<div class="uk-align-left">
									{!! $user->organization_details->address->formatted_address() !!}
								</div>
								@else <i class="a-marker-basic uk-link uk-align-left"></i><div class="uk-text-muted uk-align-left">NA</div> @endif
							</small>
						</td>
						<td>
							<small>@if($user->has_organization())
								@if($user->area_code && $user->phone_number)
								{{ $user->organization_details->phone_number_formatted() }}
								@endif
								@endif
							</small>
						</td>
						<td><small><a class="{{ !$user->active ? 'uk-text-muted' : '' }}" href="mailto:{{ $user->email }}">{{ $user->email }}</a></small></td>
						@php
						$pm_access = $user->pm_access();
						@endphp
						@if(in_array($user->id, $project_user_ids))
						@php
						$project_roles = \App\Models\ProjectContactRole::with('projectRole')->where('person_id', $user->person_id)->where('project_id', $project->id)->get();
						$roles = [];
						foreach ($project_roles as $key => $pj) {
							if($pj->projectRole) {
								array_push($roles, $pj->projectRole->role_name);
							}
						}
						$user_roles = implode(',', $roles);
						// dd($project_roles->pluck('projectRole')->pluck('role_name'));
						@endphp
						<td>
							<span><i class="use-hand-cursor a-file-gear_1" data-uk-tooltip title="{{ strtoupper($user_roles)}}"></i>  | </span>
							<span class="use-hand-cursor" data-uk-tooltip title="{{ $pm_access ? 'USER HAS ACCESS TO REPORTS' : 'USER HAS NO ACCESS TO REPORTS' }}"> <i class="{{ $pm_access ? 'a-file-approve' : 'a-file-fail' }}"></i>
							</span>
						</td>
						@else
						<td>
							<span><i class="uk-text-muted a-file-gear_1"></i> | </span>
							<span class="use-hand-cursor" data-uk-tooltip title="{{ $pm_access ? 'CLICK TO REMOVE REPORTS ACCESS' : 'USER HAS NO ACCESS TO REPORTS' }}" onclick="editUser({{ $user->id }})"><i class="{{ $pm_access ? 'a-file-approve' : 'a-file-fail' }}"></i>
							</span>
						</td>
						@endif
					</tr>
					@endforeach
				</tbody>
			</table>
			{{-- <a name="userbottom"></a> --}}
			{{ $users->links() }}
		</div>
	</div>
</div>
<script>
	$(document).ready(function(){
		$('.page-link').click(function(){
			$('#contacts-content').load($(this).attr('href'));
			return false;
		});
	});

	function editUser(id) {
		dynamicModalLoad('remove-user-from-project/{{ $project->id }}/'+id);
	}

</script>
