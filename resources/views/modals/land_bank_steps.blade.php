@extends('modals.howtocontainer')
<?php setlocale(LC_MONETARY, 'en_US'); ?>
@section('content')

				<style type="text/css">
					.steps {
						    font-size: 6rem;
						    font-weight: 100;
						    line-height: 5rem;
						    color: #738c88;
						    float: right;
					}
					.steps-heading {
						font-weight: 200;
						text-align: center;
						font-size: 3.25rem;
						padding-top: 20px;
						padding-bottom: 20px;
						color: white;
					}

				</style>
	
			<div class="uk-align-center uk-margin-large-top uk-margin-large-bottom uk-border-rounded" style="background: #005186;">
                <h1 class="steps-heading uk-margin-large-top uk-margin-large-bottom">8 Steps to Reimbursement</h1>
			</div>
				
			<div class="uk-flex-center uk-text-left" uk-grid>
				<div uk-grid class="uk-width-3-5@m ">
					<div class="uk-width-1-6">
						<span class="steps">1</span>
					</div>
					<div class="uk-width-2-3">
						<h2>IMPORT YOUR PARCELS</h2>
						<p>Import up to 5,000 parcels using a handy excel template.</p>
						<div class="uk-grid uk-grid-collapse uk-margin-large-bottom">
							<a href="/import_parcels?showHowTo=1" class="uk-button uk-button-default uk-button-large uk-width-9-10"><i  class="a-higher"></i> IMPORT</a>
							<div class="uk-width-1-10"></div>
						</div>
					</div>
				</div>
			</div>

			<div class="uk-flex-center uk-text-left" uk-grid>
				<div uk-grid class="uk-width-3-5@m">
					<div class="uk-width-1-6">
						<span class="steps">2</span>
					</div>
					<div class="uk-width-2-3">
						<h2>VALIDATE YOUR PARCELS</h2>
						<p>Check your parcels against your HFA's data.</p>
						<div class="uk-grid uk-grid-collapse uk-margin-large-bottom">
							<a href="/validate_parcels?showHowTo=1" class="uk-button uk-button-default uk-button-large uk-width-9-10 "><span class="a-check"></span> START VALIDATION</a>
						</div>		
					</div>
				</div>
			</div>
			
			<div class="uk-flex-center uk-text-left" uk-grid>
				<div uk-grid class="uk-width-3-5@m">
					<div class="uk-width-1-6">
						<span class="steps">3</span>
					</div>
					<div class="uk-width-2-3">
						<h2>ENTER COSTS &amp; REQUEST AMOUNTS</h2>
						<p>Open the parcel you want to enter your expenses for, then click the button to enter them by category and their amounts. Once you have them all in, click on the save button.</p>
						<p>Then in your breakouts tab at the bottom of your parcel detail page, adjust the requested amount for each cost and click save.<br /><img src="/request_amount_save.png" class="uk-width-1-2 uk-align-center"></p>
						<p>TIP: Click on the parcel's tab title to refresh your expense charts.<img src="/parcel_tab.png" class="uk-width-1-2"></p>
						<div class="uk-grid uk-grid-collapse uk-margin-large-bottom">
							<a href="/dashboard?tab=5&parcelsListFilter=46" class="uk-button uk-button-default uk-button-large uk-width-9-10"><span class="a-circle-plus"></span> VIEW PARCELS READY FOR COSTS</a>
						</div>
					</div>
				</div>
			</div>
			
			<div class="uk-flex-center uk-text-left" uk-grid>
				<div uk-grid class="uk-width-3-5@m">
					<div class="uk-width-1-6">
						<span class="steps">4</span>
					</div>
					<div class="uk-width-2-3">
						<h2>UPLOAD YOUR DOCUMENTS</h2>
						<p>You can do this step immediately following step three, by clicking on the "Supporting Documents" tab of your pacel. There, you can upload your supporting documents.</p>
						<div class="uk-grid uk-grid-collapse uk-margin-large-bottom">
							<a href="/dashboard?tab=5&parcelsListFilter=45" class="uk-button uk-button-default uk-button-large uk-width-9-10 "><span class="a-files-layout"></span> VIEW PARCELS MISSING DOCUMENTS</a>
						</div>
					</div>
				</div>
			</div>
			
			<div class="uk-flex-center uk-text-left" uk-grid>
				<div uk-grid class="uk-width-3-5@m">
					<div class="uk-width-1-6">
						<span class="steps">5</span>
					</div>
					<div class="uk-width-2-3">
						<h2>ADD PARCELS TO A REQUEST</h2>
						<p>If you have been assigned the "Parcel Approver" role - Open parcels that are ready for submission, and under their breakouts tab, click on the "Checks &amp; Actions" button - and select "Add this parcel to request".</p>
						<p><img src="/how_to_add_parcel.png"></p>
						<div class="uk-grid uk-grid-collapse uk-margin-large-bottom">
							<a href="/dashboard?tab=5&parcelsListFilter=7" class="uk-button uk-button-default uk-button-large uk-width-9-10"><span class="a-location-pin"></span> VIEW PARCELS</a>
						</div>
					</div>
				</div>
			</div>
			
			<div class="uk-flex-center uk-text-left" uk-grid>
				<div uk-grid class="uk-width-3-5@m">
					<div class="uk-width-1-6">
						<span class="steps">6</span>
					</div>
					<div class="uk-width-2-3">
						<h2>SUBMIT FOR REIMBURSEMENT</h2>
						<p>After adding all your parcels to a request, users with the "Request Approver" role can approve the request and send it to your HFA.</p>
						<p>Just open the request by clicking on the <span uk-icon="copy"></span> icon in the action column. Review the request, and its amount. Click on the approve button. If all approvals have been given, you can click on the "Send Request to HFA" button to send the request.</p>
						<div class="uk-grid uk-grid-collapse uk-margin-large-bottom">
							<a href="/dashboard?tab=4" class="uk-button uk-button-default uk-button-large uk-width-9-10"><span class="a-envelope-4"></span> VIEW REQUESTS</a>
						</div>
					</div>
				</div>
			</div>
			
			<div class="uk-flex-center uk-text-left uk-margin-large-bottom" uk-grid>
				<div uk-grid class="uk-width-3-5@m">
					<div class="uk-width-1-6">
						<span class="steps">7</span>
					</div>
					<div class="uk-width-2-3">
						<h2>MAKE ANY NEEDED CORRECTIONS</h2>
						<p>Review corrections requested by your approver, or by your HFA.</p>
						<div uk-grid class="uk-child-width-1-1 uk-child-width-1-2@m">
							<div>
								<a href="/dashboard?tab=5&parcelsListFilter=47" class="uk-button uk-button-default uk-button-large uk-width-1-1"><span class="a-home-2"></span> INTERNAL</a>
							</div>
							<div>
								<a href="/dashboard?tab=5&parcelsListFilter=9" class="uk-button uk-button-default uk-button-large uk-width-1-1"><span class="a-institution"></span> HFA</a>
							</div>
						</div>
					</div>
				</div>
			</div>
			
			<div class="uk-flex-center uk-text-left" uk-grid style="margin-bottom:100px;">
				<div uk-grid class="uk-width-3-5@m">
					<div class="uk-width-1-6">
						<span class="steps">8</span>
					</div>
					<div class="uk-width-2-3">
						<h2>INVOICE PURCHASE ORDERS</h2>
						<p>Review PO (approved request) summaries and invoice them.</p>
						<div uk-grid class="uk-child-width-1-1 uk-child-width-1-2@m uk-text-center">
							<div>
								<a href="/dashboard?tab=3&posStatusFilter=2" class="uk-button uk-button-default uk-button-large uk-width-1-1"><span class="a-envelope-dollar_1"></span> SEND INVOICES</a>
							</div>
							<div>
								<a href="/dashboard?tab=5&parcelsListFilter=10" class="uk-button uk-button-default uk-button-large uk-width-1-1"><span class="a-location-pin"></span> VIEW PARCELS</a>
							</div>
						</div>
						<div uk-grid class="uk-child-width-1-1 uk-child-width-1-2@m uk-text-center">
							<div>
								<a href="/dashboard?tab=4&requestsStatusFilter=3" class="uk-button uk-button-default uk-button-small uk-width-1-1"><span class="a-circle-cross"></span> DECLINED REQUESTS</a>
							</div>
							<div>
								<a href="/dashboard?tab=5&parcelsListFilter=11" class="uk-button uk-button-default uk-button-small uk-width-1-1"><span class="a-circle-cross"></span> DECLINED PARCELS</a>
							</div>
						</div>
					</div>
				</div>
			</div>
		
	
				
			
@stop