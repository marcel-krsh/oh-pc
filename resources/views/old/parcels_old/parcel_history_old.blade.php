<div id="detail-subtabs-content">
    <script>
        // disable infinite scroll:
        window.getContentForListId = 0;
    </script>

    <!-- Begin history --> 
    <style>
        .black-button{
            background-color:black;
            
        }
        .user-badge-black {
            background-color: #000000 !important;
        }
    </style>
    
<!-- begin output templates -->
<template id="filter-by-staff-template" class="uk-hidden">
    <a class="uk-button uk-button-default no-text-shadow user-badge-!!badgeColor!! uk-dark uk-light" uk-tooltip="pos:top-left;title:!!staffName!!" data-uk-filter="staff-!!staffId!!">
        !!staffInitials!!
    </a>
</template>
<!-- END TEMPLATTES -->


<!-- Begin Tools and Filters --> 
<div class="uk-container uk-margin-top no-print">
    <div class="uk-grid uk-grid-collapse uk-margin-top ">
        <div class="uk-width-1-5@l uk-width-2-5@m uk-margin-small-right">
            <a class="uk-button uk-button-primary blue-button uk-width-1-1 uk-padding-remove" onclick="openWindow('history')" >
                <span uk-icon="list-ul"></span> <span class="uk-text-small">OPEN FILE HISTORY IN NEW WINDOW</span></a>
            
        </div>
        <div class="uk-width-1-5@l uk-width-2-5@m uk-margin-small-right">
            
            
        </div>
        
        <div class="uk-width-1-1 uk-margin-remove"></div>
        <div class="uk-width-1-1 uk-margin-small-top uk-button-group" id="message-filters" data-uk-button-radio>
            
            <div class="uk-button uk-button-default uk-width-1-6 ">
                
                 <div class="field-box no-border uk-padding-remove uk-margin-remove">
                        <div class="uk-grid uk-grid-collapse">
                            
                            <div class="uk-width-1-1">
                                <div class="uk-form-select uk-active" data-uk-form-select="">
                                        <span class=""></span>
                                                  
                                            <select name="historyType" id="program-replaceWithProgramId-switcher" class="uk-select" onchange="">
                                                <option value="" selected="">All History Types</option>
                                                <!-- TIM PUT IN DYNAMIC LIST OF TYPES HERE -->
                                                <option value="" >User/View Logs</option>
                                                <option value="">Applicant Information</option>
                                                <option value="">Counseling Agency</option>
                                                <option value="">Payments</option>
                                                <option value="">CDF</option>
                                                <option value="">Document</option>
                                                <option value="">Communication</option>
                                                <option value="">Note</option>
                                                <option value="">Outcome</option>
                                                <option value="">Status Change</option>
                                                
                                                
                                            </select>
                                </div>
                            </div>
                        </div>
                        
                    </div>
                            
                   
            </div>
            <div class="uk-button uk-button-default uk-width-1-4 ">
                
                        
                                <input id="histories-search" type="text" value="" class="uk-width-1-1 no-background no-border" placeholder="Search Within History">
                            
                   
            </div>
            <a class="uk-button uk-button-default" data-uk-filter="">
                ALL
            </a>
            
            
            <!-- display current info - read only -->
            
            
            
        
            
        </div>
        
        <!-- End Tool and Filters -->
        
    </div>
</div>
    
<!-- start comm list -->
<div class="uk-container uk-container-center">
    <div class="uk-grid uk-margin-top" id="history-list" data-uk-grid="{controls: '#message-filters', animation: false}">
        <!-- history list is loaded in place using loadCommuincations() -> printhistoriesList(); using the json input from histories/file-id.json where file-id is the file-id of the current file. -->
        
    </div>
</div>
<div id="detail-tab-bottom-bar" class="uk-vertical-align no-print">
    <table class="action-bar">
        <tbody>
            <tr>
            <td width="3.5%"></td>
            <td width="18.6%">
            </td>
            <td width="18.6%">
            </td>
            <td width="18.6%">
                
            </td>
            <td width="18.6%">
                <a class="uk-button uk-button-primary blue-button uk-width-1-1 uk-padding-remove" onclick="openWindow('print-history')">
                <span uk-icon="print"></span> <span class="uk-text-small">PRINT HISTORY</span></a>            
            </td>
            
            <td width="3.5%"></td>
        </tr>
    </tbody></table>
</div>
<!-- end comm list -->
<script>
    loadHistories({{$parcel->id}});
</script>

</div>

