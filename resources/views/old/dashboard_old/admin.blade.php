<div class="uk-width-1-1 uk-width-1-2@s uk-width-3-5@m uk-push-1-5 uk-margin-top uk-margin-large-bottom">
    <div class="uk-panel uk-panel-box">
        <h3 class="uk-panel-title">
            ADMIN TOOLS
        </h3>
        <hr class="dashed-hr" class="uk-margin-bottom">
        <br/>
        <ul style="list-style-type: none;">
            <li >
            <a onclick="dynamicModalLoad('admin/entity/create')" class="uk-button uk-button-default uk-width-2-5@m" >CREATE NEW ENTITY</a>
            <a onclick="dynamicModalLoad('admin/program/create')" class="uk-button uk-button-default uk-width-2-5@m uk-float-right">CREATE NEW PROGRAM</a>
            <hr class="dashed-hr uk-margin-bottom">
            <a onclick="dynamicModalLoad('admin/rule/create')" class="uk-button uk-button-default uk-width-2-5@m">CREATE NEW RULE</a>
            <a onclick="dynamicModalLoad('admin/account/create')" class="uk-button uk-button-default uk-width-2-5@m uk-float-right">CREATE NEW ACCOUNT</a>
            <hr class="dashed-hr uk-margin-bottom">
            <a onclick="dynamicModalLoad('admin/vendor/create')" class="uk-button uk-button-default uk-width-2-5@m">CREATE NEW VENDOR</a>
            <a onclick="dynamicModalLoad('admin/target_area/create')" class="uk-button uk-button-default uk-width-2-5@m  uk-float-right">CREATE NEW TARGET AREA</a>
            <hr class="dashed-hr uk-margin-bottom">
            <a onclick="dynamicModalLoad('admin/document_category/create')" class="uk-button uk-button-default uk-width-2-5@m">CREATE NEW DOCUMENT CATEGORY</a>
            <a onclick="dynamicModalLoad('admin/expense_category/create')" class="uk-button uk-button-default uk-width-2-5@m  uk-float-right">CREATE NEW EXPENSE CATEGORY</a>

            </li>
        </ul>




    </div>
</div>

<div class="uk-grid">
    <div class="uk-width-1-1 ">
        <ul class="uk-subnav uk-subnav-pill" uk-switcher="animation: uk-animation-fade">
            <li id="entities-tab" class="uk-active"><a>Entities</a></li>
            <li id="programs-tab"><a>Programs</a></li>
            <li id="rules-tab"><a>Rules</a></li>
            <li id="accounts-tab"><a>Accounts</a></li>
            <li id="vendors-tab"><a>Vendors</a></li>
            <li id="target-areas-tab"><a>Target Areas</a></li>
            <li id="document-categories-tab"><a>Document Categories</a></li>
            <li id="expense-categories-tab"><a>Expense Categories</a></li>
            <li id="counties-tab"><a>Counties</a></li>
            <li id="emails-tab"><a>Email History</a></li>
        </ul>

        <ul class="uk-switcher uk-margin">
            <li class="uk-active" id="entities-tab-content">
                <script type="text/javascript">
                     $('#entities-tab').on('click',function(){
                        $('#entities-tab-content').load('/tabs/entity');
                    });
                </script>
            </li>
            <li id="programs-tab-content">
                <script type="text/javascript">
                    $('#programs-tab').on('click',function(){
                        $('#programs-tab-content').load('/tabs/program');
                    });
                </script>
            </li>
            <li id="rules-tab-content">
                <script type="text/javascript">
                    $('#rules-tab').on('click',function(){
                        $('#rules-tab-content').load('/tabs/rule');
                    });
                </script>
            </li>
            <li id="accounts-tab-content">
                <script type="text/javascript">
                    $('#accounts-tab').on('click', function(){
                        $('#accounts-tab-content').load('/tabs/account');
                    });
                </script>
            </li>
            <li id="vendor-tab-content">
                <script type="text/javascript">
                    $('#vendors-tab').on('click', function(){
                        $('#vendor-tab-content').load('/tabs/vendor');
                    });
                </script>
            </li>
            <li id="target-tab-content">
                <script type="text/javascript">
                    $('#target-areas-tab').on('click', function(){
                        $('#target-tab-content').load('/tabs/target_area');
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
            <li id="expense-tab-content">
                <script type="text/javascript">
                    $('#expense-categories-tab').on('click', function(){
                        $('#expense-tab-content').load('/tabs/expense_category');
                    });
                </script>
            </li>
            <li id="counties-content">
                <script type="text/javascript">
                    $('#counties-tab').on('click', function(){
                        $('#counties-content').load('/tabs/county');
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
<script type="text/javascript">$('#entities-tab-content').load('/tabs/entity');
                    </script>

