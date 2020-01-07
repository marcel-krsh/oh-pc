<script>
	UIkit.sticky('.uk-sticky', {});
</script>
<div uk-grid class="uk-margin-top ">


	<div class="uk-grid uk-align-center">
		<div class="uk-width-1-1">
			<ul class="uk-subnav uk-subnav-pill"  uk-sticky="offset: 55;background:#f9f9f9" uk-switcher="animation: uk-animation-fade" style="background-color: aliceblue; padding-bottom: 10px">

				<li id="amenities-tab" class="uk-active"><a>Amenities</a></li>
				<li id="hud-tab"><a>HUD Areas</a></li>
				<li id="findingtype-tab"><a>Finding Types</a></li>
				<li id="default-followups-tab"><a>Follow Ups</a></li>
				<li id="boilerplates-tab"><a>Boilerplates</a></li>
				<li id="organizations-tab" ><a>Organizations</a></li>
				<li id="users-tab" ><a>Users</a></li>
        <!-- <li id="programs-tab"><a>Programs</a></li>
        <li id="document-categories-tab"><a>Doc Categories</a></li>
        <li id="counties-tab"><a>Counties</a></li> -->
        <li id="emails-tab"><a>Email History</a></li>
      </ul>

      <ul class="uk-switcher uk-margin">

      	<li id="amenities-tab-content" class="uk-active">
      		<script type="text/javascript">
      			$('#amenities-tab').on('click',function(){
      				$('#amenities-tab-content').load('/tabs/amenity');
      			});
      		</script>
      	</li>
      	<li id="hud-tab-content">
      		<script type="text/javascript">
      			$('#hud-tab').on('click',function(){
      				$('#hud-tab-content').load('/tabs/hud');
      			});
      		</script>
      	</li>
      	<li id="findingtype-tab-content">
      		<script type="text/javascript">
      			$('#findingtype-tab').on('click',function(){
      				$('#findingtype-tab-content').load('/tabs/findingtype');
      			});
      		</script>
      	</li>
      	<li id="default-followups-tab-content">
      		<script type="text/javascript">
      			$('#default-followups-tab').on('click',function(){
      				$('#default-followups-tab-content').load('/tabs/defaultfollowup');
      			});
      		</script>
      	</li>
      	<li id="boilerplates-tab-content">
      		<script type="text/javascript">
      			$('#boilerplates-tab').on('click',function(){
      				$('#boilerplates-tab-content').load('/tabs/boilerplate');
      			});
      		</script>
      	</li>
			  <!-- <li id="programs-tab-content">
			      <script type="text/javascript">
			          $('#programs-tab').on('click',function(){
			              $('#programs-tab-content').load('/tabs/program');
			          });
			      </script>
			  </li>
			  <li id="document-tab-content">
			      <script type="text/javascript">
			          $('#document-categories-tab').on('click', function(){
			              $('#document-tab-content').load('/tabs/document_category');
			          });

			      </script>
			  </li>
			  <li id="counties-content">
			      <script type="text/javascript">
			          $('#counties-tab').on('click', function(){
			              $('#counties-content').load('/tabs/county');
			          });
			      </script>
			    </li> -->
			    <li  id="organizations-tab-content">
			    	<script type="text/javascript">
			    		$('#organizations-tab').on('click',function(){
			    			$('#organizations-tab-content').load('/tabs/organization');
			    		});
			    	</script>
			    </li>
			    <li id="users-content">
			    	<script type="text/javascript">
			    		$('#users-tab').on('click', function(){
			    			$('#users-content').load('/tabs/users');
			    		});
			    	</script>
			    </li>
			    <li id="emails-content">
			    	<script type="text/javascript">
			    		$('#emails-tab').on('click', function(){
			    			$('#emails-content').load('/tabs/emails');
			    		});
			    	</script>
			    </li>
			  </ul>

			</div>
		</div>
	</div>
	<script type="text/javascript">$('#amenities-tab-content').load('/tabs/amenity');
</script>
<?php // keep this script at the bottom of page to ensure the tabs behave appropriately ?>
<script>
	window.adminLoaded = 1;


</script>
<?php // end script keep ?>
