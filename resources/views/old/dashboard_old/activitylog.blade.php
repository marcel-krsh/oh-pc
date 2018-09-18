<script type='text/javascript'>
var recordstart = 0;
var recordcount = 250;
var recordtype = 'all';
var searchtype = '';
var searchstring = '';

$=jQuery;
$(document).ready(function() {
	$(".prevpage").hide();
	getactivitylog(recordstart,recordcount);
	$('#selectlogtype').change(function() {
		//alert($('#selectlogtype').val());
		getlogtype($('#selectlogtype').val());
	});
});

function getactivitylog(start, count) {
	$("#history-list").html("<center><h1>Please wait,  Loading results...</h1></center>");
    var url = '/viewlogjson/' +  recordtype + '/' + start + '/' + count;
    var querystring = '';
    var reqtype = 'GET';

    if (searchtype == '') {
        url = '/viewlogjson/' +  recordtype + '/' + start + '/' + count;
    } else {
        url = '/searchlogjson/' +  recordtype + '/' + start + '/' + count;
        querystring = 'searchselect=' + searchtype + '&searchtext=' + searchstring;
        reqtype = 'POST';
    }
	$.ajax({
        type: reqtype,
        url: url,
        data: querystring,
        dataType: 'json',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
        },
        success: function(data) {
                $("#history-list").html("");
                for (var i = 0; i <= data.length - 1; i++) {
                	var log = data[i];
                	var container = $('<div class="uk-width-1-1 history-list-item" id="history-0" data-grid-prepared="true" style=""></div>');
                	var subcontainer = $('<div class="uk-grid"></div>');
                	var historytype = $('<div class="uk-width-1-4@l uk-width-1-3@m history-type-and-who "></div>');
                	historytype.append('<span title="' +  log.staffName + '" class="no-print"><div class="user-badge user-badge-history-item user-badge-' +  log.staffBadgeColor + ' no-float">' + log.staffInitials + '</div></span>');
                	historytype.append('<span class="print-only">' +  log.staffName + '<br></span>');
                	historytype.append('<span class=" history-item-date-time">' +  log.dateTimeOfHistory + '</span>');
                	subcontainer.append(historytype);
                	var excerpt = $('<div class="uk-width-3-4@l uk-width-2-3@m history-item-excerpt"></div>');
                	excerpt.append(log.historyContent);
                	subcontainer.append(excerpt);
                	container.append(subcontainer);
                	$("#history-list").append(container);
                }
        },
        error: function() {
            $("#history-list").html("<center><h1>Unable to get search results</h1></center>");
        },
    });
}
function dosearch() {
    searchtype = $('#searchselect').val();
    searchstring = $('#searchtext').val();
    getactivitylog(recordstart, recordcount);
}
function getlogtype(logtype) {
	recordtype = logtype;
	recordcount = 250;
	recordstart = 0;
	getactivitylog(recordstart, recordcount);
}
function getnextpage() {
	recordstart = recordstart + recordcount;
	getactivitylog(recordstart,recordcount);
	if (recordstart > 200){
    	$(".prevpage").show();
    } else {
    	$(".prevpage").hide();
    }
}
function getprevpage() {
	recordstart = recordstart - recordcount;
	getactivitylog(recordstart,recordcount);
	if (recordstart > 200){
    	$(".prevpage").show();
    } else {
    	$(".prevpage").hide();
    }
}
</script>
<div class="uk-overflow-container uk-margin-top">
    <h4 class="uk-text-left">Activity logs: <small>
            <a onclick="getlogtype('all');" class="uk-button uk-button-default uk-button-small uk-align-right">
                <span class="">All logs</span>
            </a>
            <select id='selectlogtype'>
                <option value="all">All</option>
                <option value="user">User</option>
                <option value="parcel">parcel</option>
                <option value="account">Account</option>
                <option value="accounting">Accounting</option>
                <option value="program">Program</option>
                <option value="entity">Entity</option>
                <option value="vendor">Vendor</option>
                <option value="transaction">Transaction</option>
                <option value="dispositions">Dispositions</option>
                <option value="requests">Requests</option>
                <option value="programrule">Program Rule</option>
                <option value="communication">Communication</option>
                <option value="polist">POList</option>
                <option value="stats">Stats</option>
                <option value="import">Import</option>
                <option value="document">Document</option>
                <option value="documentcategory">Document Category</option>
                <option value="expensecategory">Expensecategory</option>
                <option value="reimbursement_purchase_orders">Reimbursement Purchase Orders</option>
                <option value="reimbursement_invoices">Reimbursement Invoices</option>
                <option value="parcels_to_reimbursement_requests">Parcels to Reimbursement Requests</option>
                <option value="parcels_to_purchase_orders">Parcels To Purchase Orders</option>
                <option value="disposition_invoices">Disposition Invoices</option>
                <option value="recapture_invoices">Recapture Invoices</option>
                <option value="parcels_to_reimbursement_invoices">Parcels To Reimbursement Invoices</option>

            </select>
        </small>
    </h4>
</div>
<div class="uk-overflow-container uk-margin-top">
    <h4 class="uk-text-left">Search: <small>
         Search for: <input id='searchtext' type='text' id='searchterm'>
         In Field:
         <select id='searchselect'>
         <option value='logdesc'>Log Description</option>
         <option value='eventname'>Event Name</option>
         <option value='email'>Email</option>
         <option value='logid'>Log ID</option>
         <option value='staffname'>Staff Name</option>
         <option value='history'>History option name</option>
         <option value='propertyname'>Property Name</option>
         <option value='any'>Any</option>
         </select>
         <a onclick="dosearch();" class="uk-button uk-button-default uk-button-small">
            <span class="dosearch">Search</span>
         </a>
        </small>
    </h4>
</div>

<div class="uk-grid uk-margin-top" style="position: relative;">
    <a class='prevpage' href="#" onclick="getprevpage();">Previus page</a>&nbsp;<a class='nextpage' href="#" onclick="getnextpage();">Next page</a>
</div>
<div class="uk-grid uk-margin-top" id="history-list" style="position: relative;">

</div>
<div class="uk-grid uk-margin-top" style="position: relative;">
    <a class='prevpage' href="#" onclick="getprevpage();">Previus page</a>&nbsp;<a class='nextpage' href="#" onclick="getnextpage();">Next page</a>
</div>
<div id="list-tab-bottom-bar" class="uk-flex-middle"  style="height:50px;">
<a  href="#top" uk-scroll="{offset: 90}" class="uk-button uk-button-default uk-button-small uk-align-right uk-margin-top uk-margin-right" style="margin-right:302px !important"><span class="a-arrow-small-up uk-text-small uk-vertical-align-middle"></span> SCROLL TO TOP</a> 

</div>