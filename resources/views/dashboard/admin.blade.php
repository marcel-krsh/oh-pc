<div uk-grid>
    <div class="uk-width-1-1 uk-width-1-2@s uk-width-3-5@m uk-push-1-5 uk-margin-top uk-margin-large-bottom">
        <div class="uk-panel uk-panel-box">
            <h3 class="uk-panel-title">
                ADMIN TOOLS
            </h3>
            <hr class="dashed-hr" class="uk-margin-bottom">
            <br/>
            <ul style="list-style-type: none; padding:0">
                <li>
                <a onclick="dynamicModalLoad('admin/document_category/create')" class="uk-button uk-button-default uk-width-2-5@m">CREATE NEW DOCUMENT CATEGORY</a>
                <a onclick="dynamicModalLoad('admin/boilerplate/create')" class="uk-button uk-button-default uk-width-2-5@m uk-float-right">CREATE NEW BOILERPLATE</a>
                <hr class="dashed-hr uk-margin-bottom">
                </li>
                <li>
                <a onclick="dynamicModalLoad('admin/finding_type/create')" class="uk-button uk-button-default uk-width-2-5@m">CREATE FINDING TYPE</a>
                <a onclick="dynamicModalLoad('admin/document_category/create')" class="uk-button uk-button-default uk-width-2-5@m  uk-float-right">CREATE HUD AREA</a>
                <hr class="dashed-hr uk-margin-bottom">
                </li>
                <li>
                <a onclick="dynamicModalLoad('auditors/{{Auth::user()->id}}/preferences',0,0,1);" class="uk-button uk-button-default uk-width-2-5@m uk-float-right">EDIT PREFERENCES</a>
                <hr class="dashed-hr uk-margin-bottom">
                </li>
            </ul>




        </div>
    </div>

    <div class="uk-grid">
        <div class="uk-width-1-1 ">
            <ul class="uk-subnav uk-subnav-pill" uk-switcher="animation: uk-animation-fade">
                <li id="organizations-tab" class="uk-active"><a>Organizations</a></li>
                <li id="amenities-tab"><a>Amenities</a></li>
                <li id="hud-tab"><a>HUD Areas</a></li>
                <li id="findingtype-tab"><a>Finding Types</a></li>
                <li id="default-followups-tab"><a>Follow Ups</a></li>
                <li id="boilerplates-tab"><a>Boilerplates</a></li>
                <li id="programs-tab"><a>Programs</a></li>
                <li id="document-categories-tab"><a>Doc Categories</a></li>
                <li id="counties-tab"><a>Counties</a></li>
                <li id="emails-tab"><a>Email History</a></li>
            </ul>

            <ul class="uk-switcher uk-margin">
                <li class="uk-active" id="organizations-tab-content">
                    <script type="text/javascript">
                         $('#organizations-tab').on('click',function(){
                            $('#organizations-tab-content').load('/tabs/organization');
                        });
                    </script>
                </li>
                <li id="amenities-tab-content">
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
                <li id="programs-tab-content">
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
<script type="text/javascript">$('#organizations-tab-content').load('/tabs/organization');
                    </script>

