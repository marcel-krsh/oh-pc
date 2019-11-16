<div  id="contacts-content">
	<div class="uk-width-1-1 uk-align-center	" style="width: 90%">
		<a name="organizationtop"></a>
		<div class="uk-overflow-container uk-margin-top">
			<div uk-grid class="uk-margin-remove">
				<h4 class="uk-text-left uk-width-2-3" style="padding-top: 8px;">{{ number_format($users->total(), 0) }} TOTAL USERS</h4>
			</div>
			<hr>
			{{ $users->links() }} <a onClick="dynamicModalLoad('{{ $project->id }}/add-user-to-project');" class="uk-badge uk-margin-top uk-badge-success"><i class="a-circle-plus"></i> ADD USER TO PROJECT</a>

			<div class="uk-margin-top">
				<div class="uk-background-muted uk-padding uk-panel">
					<p><strong>NOTE:</strong> Please select the radio button next to the contact information of any contact that should be used on reports.<br> The default selection is applied to the primary manager designated in DEV|CO. <br>However you can choose any combination of User, Organization, and Address across users.
					</p>
				</div>
			</div>
			<table class="uk-table uk-table-hover uk-table-striped uk-overflow-container small-table-text">
				<thead>
					<tr>
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
					</tr>
				</thead>
				<tbody>
					<tr class="">
						<td>
							<input type="radio" uk-tooltip="" title="" aria-expanded="false"> Robert Hellmuth <i class="a-pencil" uk-tooltip="" title="" aria-expanded="false"></i><br>
							<small>PROPERTY MANAGER</small>
						</td>
						<td>
							<input name="org" type="radio" uk-tooltip="" title="" aria-expanded="false">
							<small>Sawmill Road Management Company, LLC <i class="a-pencil" uk-tooltip="" title="" aria-expanded="false"></i>
							</small> <hr class="dashed-hr  uk-margin-small-bottom">
							<input type="radio" uk-tooltip="" title="" aria-expanded="false" name="org">
							<small>Another Management Company, LLC <i class="a-pencil" uk-tooltip="" title="" aria-expanded="false"></i>
							</small><br><hr class="dashed-hr  uk-margin-small-bottom">
							<small><i class="a-circle-plus" uk-tooltip="" title="" aria-expanded="false"></i> ADD ANOTHER ORGANIZATION</small>
						</td>
						<td>
							<small><span target="_blank" href="https://www.google.com/maps?q=1990A+Kingsgate+Road+Springfield+OH+45502" class="uk-text-muted uk-align-left"><input type="radio" uk-tooltip="" title="" aria-expanded="false"></span>
								<div class="uk-align-left">
									1990A Kingsgate Road<br>Springfield OH 45502 <i class="a-pencil" uk-tooltip="" title="" aria-expanded="false"></i>
								</div>
							</small><br><hr class="dashed-hr  uk-margin-small-bottom">
							<small><span target="_blank" href="https://www.google.com/maps?q=1990A+Kingsgate+Road+Springfield+OH+45502" class="uk-text-muted uk-align-left"><input type="radio" uk-tooltip="" title="" aria-expanded="false"></span>
								<div class="uk-align-left">
									123 Sesame Street<br>New York NY 12345 <i class="a-pencil" uk-tooltip="" title="" aria-expanded="false"></i>
								</div>
								<br><hr class="dashed-hr uk-margin-small-bottom"><i class="a-circle-plus" uk-tooltip="" title="" style="
								" aria-expanded="false"></i> ADD ANOTHER ADDRESS
							</small>
						</td>
						<td>
							<small> (937) 342-9071
							</small>
						</td>
						<td><small><a class="" href="mailto:RHellmuth6236@allita.org">RHellmuth6236@allita.org</a></small></td>
						<td>
							<span data-uk-tooltip="" title="" aria-expanded="false"><i class="a-file-gear_1"></i> | <i class="a-file-approve"></i>
							</span>
						</td>
					</tr>
					<tr class="">
						<td><input type="radio" uk-tooltip="" title="" aria-expanded="false"> Brian Greenwood <i class="a-pencil" uk-tooltip="" title="" aria-expanded="false"></i><br>
							<small>PROPERTY MANAGER</small>
						</td>
						<td>
							<input name="org" type="radio" uk-tooltip="" title="" aria-expanded="false"><small> Greenwood 360, LLC <i class="a-pencil" uk-tooltip="" title="" aria-expanded="false"></i></small> <hr class="dashed-hr uk-margin-small-bottom"> <input name="org" type="radio" uk-tooltip="" title="" aria-expanded="false"><small> Allita 360 <i class="a-pencil" uk-tooltip="" title="" aria-expanded="false"></i></small><br><hr class="dashed-hr uk-margin-small-bottom"><small><i class="a-circle-plus" uk-tooltip="" title="" aria-expanded="false"></i> ADD ANOTHER ORGANIZATION</small>
						</td>
						<td>
							<small> <span target="_blank" href="https://www.google.com/maps?q=1990A+Kingsgate+Road+Springfield+OH+45502" class="uk-text-muted uk-align-left"><input type="radio" uk-tooltip="" title="" aria-expanded="false"></span>
								<div class="uk-align-left">
									300 Marconi Blvd<br>Columbus OH 43215 <i class="a-pencil" uk-tooltip="" title="" aria-expanded="false"></i>
								</div>
							</small>
							<br><hr class="dashed-hr uk-margin-small-bottom">
							<small><span target="_blank" href="https://www.google.com/maps?q=1990A+Kingsgate+Road+Springfield+OH+45502" class="uk-text-muted uk-align-left"><input type="radio" uk-tooltip="" title="" aria-expanded="false"></span>
								<div class="uk-align-left">
									321 Sesame Street<br>New York NY 12345 <i class="a-pencil" uk-tooltip="" title="" aria-expanded="false"></i>
								</div>
								<br><hr class="dashed-hr uk-margin-small-bottom"><i class="a-circle-plus" uk-tooltip="" title="" style=" " aria-expanded="false"></i> ADD ANOTHER ADDRESS
							</small>
						</td>
						<td>
							<small> (937) 342-9071
							</small>
						</td>
						<td><small><a class="" href="mailto:RHellmuth6236@allita.org">BrianGreenwood@allita.org</a></small></td>

						<td>
							<span data-uk-tooltip="" title="" aria-expanded="false"><i class="a-file-gear_1" style="color:rgba(0,0,0,.3)"></i> | <i class="a-file-approve"></i>
							</span>
						</td>
					</tr>
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
