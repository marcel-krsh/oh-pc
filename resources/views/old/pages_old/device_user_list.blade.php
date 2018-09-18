	<br /><h2>Users with lists on Device Id "{{$deviceId}}"</h2>
	<hr />
	<p><ul>
	@if(count($users) > 0)
	    @foreach ($users as $user ) 
	    <li>{{$user->name}} : <a href="mailto:{{$user->email}}"><span uk-icon="envelope"></span></a> | <a onclick="dynamicModalLoad('user/{{$user->id}}');"><span uk-icon="edit"></span></a></li>

	    @endforeach
	@else
		<li>None of the users on the device have created a visit list yet.</li>
	@endif
	</ul>
</p>
	<hr>
	<small>NOTE: If you want to revoke a user's access, simply edit their profile above and check the box to generate a new API key. Their access to all devices will be revoked until they update their API key.</small>