
<div class="uk-grid">
	<div class="uk-width-1-1 uk-margin-top ">
	<h4 class="uk-text-center"> TOTAL USERS</h4>
	<hr>
		<div class="uk-overflow-container" class="margin:4px;">
		<table class="uk-table uk-table-hover uk-table-striped">
		<thead >
			<tr >
				<th width="300">Name</th><th width="300">Email</th><th width="300">Role(s)</th><th width="300">Entity</th>
			</tr>
		</thead>
		<tbody>
    <?php /* @foreach ($myUsers as $user ) 
    	<tr>
         <td width="300"><div class="user-badge user-badge-{{ $user->badge_color }} {{ strtolower($user->name) }}"> {{ userInitials($user->name) }} </div><a onClick="dynamicModalLoad('user/{{ $user->id }}');" target="_blank">{{ $user->name }}</a></td>
         <td width="300"><a herf="mailto:{{ $user->email }}"><i class="uk-icon-envelope"></i> {{ $user->email }}</a></td>
         <td width="300"><i class="uk-icon-user"></i>: {{ $user->badge_color }}</a></td>
         <td width="300">{{ $user->entity_name }}</td>
         </tr>
    @endforeach
    */ ?>
		</tbody>
		</table>
		</div>
	</div>
</div>

