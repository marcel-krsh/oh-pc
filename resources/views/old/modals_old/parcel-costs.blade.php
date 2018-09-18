
	<div id="dynamic-modal-content">
		<script>
		resizeModal(90);
		</script>
		<style>
		.hot-container {
		    overflow: hidden;
		}
		</style>
			<div class="uk-container uk-container-center">

				<div class="uk-grid uk-grid-small">
					<div class="uk-width-8-10">
						<h3>Add Costs for Parcel {{$parcel->parcel_id}}</h3>
					</div>
				</div>
				<div uk-grid class="uk-grid-collapse">
	
					<div class="uk-width-1-1">
						<div uk-grid class="uk-grid-collapse">
							<div class="uk-width-1-1 uk-margin-bottom">
								<div class="uk-width-1-1">
								  
							      <div id="console-alert" class="uk-alert" hidden><div id="costConsole" class="console"></div></div>

							      <div id="costEntry" style="margin-left:20px; height:500px; overflow:hidden;"></div>
							      <div uk-grid class="uk-grid-collapse">
						            <div class="uk-width-1-1">
						            	<hr class="hr-dashed" />
						            	<p>Knowingly submitting incorrect documentation, request for reimbursements for expenses not incurred or those expenses where payment was received from another source, constitutes fraud and will be prosecuted to the fullest extent of the law.</p><hr class="hr-dashed" />
							            <button class="uk-button uk-button-success uk-width-1-3  uk-margin-top" style="margin-left:20px;" name="save">Save</button>
							        </div>
							      </div>
							      
							      <script>
							        function isEmptyRow(instance, row) {
									    var rowData = instance.getData()[row];

									    for (var i = 0, ilen = rowData.length; i < ilen; i++) {
									      if (rowData[i] !== null) {
									        return false;
									      }
									    }

									    return true;
									}

							        var categories = [
							        	@foreach($expense_categories as $cat)
							        	"{{$cat->expense_category_name}}",
							        	@endforeach
							        ];

							        var expenses = [
							        	
							        ];

							        var vendors = [
							        	"",
							        	@foreach($vendors as $vendor)
							        	"{!!$vendor->vendor_name!!}",
							        	@endforeach
							        ];

						            var
						              $container = $("#costEntry"),
						              $console = $("#costConsole"),
						              $parent = $container.parent(),
						              autosaveNotification,
						              hot;

						            hot = new Handsontable($container[0], {
						              columnSorting: true,
						              startRows: 8,
						              startCols: 6,
						              rowHeaders: true,
						              autoWrapRow: true,
									  autoWrapCol: true,
						              colHeaders: ['Category', 'Description', 'Advance', 'Vendor', 'Cost', 'Notes'],
						              columns: [
								        
							            {
							              data: 'category',
							              type: 'dropdown',
							              source: categories
							            },
							            {
							              data: 'expense',
							              type: 'dropdown',
							              source: expenses,
							              renderer: dropRenderer,
							              validator: null
							            },
								        {
								          data: 'advance',
									      type: 'checkbox',
									      renderer: checkRenderer,
									      className: 'htCenter'
								        },
							            {
							              data: 'vendor',
							              type: 'dropdown',
							              source: vendors,
							              validator: null
							            },
							            {
							            	data: 'numeric',
            								type: 'text'
							            },
							            {}
							          ],
						              minSpareCols: 0,
						              minSpareRows: 1,
						              rowHeaders: false,
    								  colWidths: [100, 200, 50, 200, 80, 130],
						              contextMenu: false,
						              stretchH: 'all',
								    beforeChange: function (changes) {
								      var instance = hot,
								        ilen = changes.length,
								        clen = instance.colCount,
								        rowColumnSeen = {},
								        rowsToFill = {},
								        i,
								        c;

								      for (i = 0; i < ilen; i++) {
								        // if oldVal is empty
								        if (changes[i][2] === null && changes[i][3] !== null) {
								          if (isEmptyRow(instance, changes[i][0])) {
								            rowColumnSeen[changes[i][0] + '/' + changes[i][1]] = true;
								            rowsToFill[changes[i][0]] = true;
								          }
								        }
								      }
								      for (var r in rowsToFill) {
								        if (rowsToFill.hasOwnProperty(r)) {
								          for (c = 0; c < clen; c++) {
								            if (!rowColumnSeen[r + '/' + c]) {
								              changes.push([r, c, null, tpl[c]]);
								            }
								          }
								        }
								      }

								    },
						              afterChange: function (change, source) {
						                var data;

						                if (!$parent.find('input[name=autosave]').is(':checked')) {
						                  return;
						                }
						                data = change[0];

						                // transform sorted row to original row
						                data[0] = hot.sortIndex[data[0]] ? hot.sortIndex[data[0]][0] : data[0];						               
						              }
						            });

						            $parent.find('button[name=save]').click(function () {
						            	var test = {data: hot.getData(), '_token': '{{ csrf_token() }}'};

						              $.ajax({
						                url: '{{ URL::route("parcelcosts.save", [$parcel]) }}',
						                data: {data: hot.getData(), '_token': '{{ csrf_token() }}'}, // returns all cells' data
						                dataType: 'json',
						                type: 'POST',
						                success: function (res) {
						                	console.log(res);
						                  if (res == 1) {
						                    $console.text('Data saved');
						                    $("#console-alert").removeAttr("hidden");
						                    loadParcelSubTab('detail',{{$parcel->id}});
											dynamicModalClose();
						                  }
						                  else {
						                    $console.text('There was a problem, your data was not saved.');
						                    $("#console-alert").removeAttr("hidden");
						                  }
						                },
						                error: function () {
						                  $console.text('There was a problem, your data was not saved.');
						                    $("#console-alert").removeAttr("hidden");
						                }
						              });
						            });
	

									function dropRenderer(instance, td, row, col, prop, value, cellProperties) {
									 	var category_value = instance.getDataAtCell(row,0);
									 	if (category_value) {
									      if(category_value === 'NIP Loan Payoff'){
												expenseArray = ['Payoff of Existing Loan'];
											}else if(category_value === 'Acquisition'){
												expenseArray = ['Acquisition of real estate',
													'Purchase and litigation of Tax Lien Certificates for Vacant property',
													'Real Estate Agent Fees',
													'Court Costs Related to Foreclosure and/or BOR Process',
													'Title Searches',
													'Closing Costs'];
											}else if(category_value === 'Pre-Demo'){
												expenseArray = ['Environmental assessments',
													'Asbestos surveys',
													'Contract preparation and review by third-parties',
													'Board-Up/Security',
													'Architectural/engineering fees, including cost estimates, bid specifications and job progress inspections',
													'Legal/bid advertisements',
													'Other third-party expenses approved on a case-by-case basis by OHFA'];
											}else if(category_value === 'Demolition'){
												expenseArray = ['Demolition of buildings',
													'Removal of asbestos',
													'Removal of other hazardous materials',
													'Clearance of structures (poles, fences, walls, driveways, service walks, etc.)',
													'Removal of underground storage tanks and utility services',
													'Removal and/or filling/capping of septic systems and wells',
													'Clearance of debris and garbage (illegal dumping, junk vehicles, etc.)',
													'Regulatory permit and inspection fees',
													'Other expenses approved on a case-by-case basis by OHFA'];
											}else if(category_value === 'Greening (Post Demo)'){
												expenseArray = ['Grading, Seeding, and/or Basic Site Restoration',
													'Additional greening or improvements beyond basic site restoration'];
											}else if(category_value === 'Maintenance'){
												expenseArray = ['Current and future maintenance up to $400 annually for up to 3 years'];
											}else if(category_value === 'Administration'){
												expenseArray = ['General management and oversight',
													'Technical support services',
													'Monitoring and evaluation',
													'Preparation of Reimbursement/Disbursement Requests',
													'Performance Report preparation',
													'Local historic review/Assessments (OHPO clearance is not required)',
													'Audit costs',
													'Contract preparation and review by internal counsel or staffers',
													'NIP Loan closing expenses by internal staff or third party',
													'Mortgage recording',
													'Other expenses approved on a case-by-case basis by OHFA'];
											}else{
												expenseArray = [''];
											}
										    cellProperties.source = expenseArray;
											  Handsontable.renderers.AutocompleteRenderer.apply(this, arguments);
									    }

									 	
									  };

									function checkRenderer(instance, td, row, col, prop, value, cellProperties) {
										var category_value = instance.getDataAtCell(row,0);
									 	if (category_value) {
											var rules = [];
											@foreach($advance_rules as $rule)
											rules['{{$rule["name"]}}'] = {{$rule['advance']}};
											@endforeach

										 	if(category_value === 'NIP Loan Payoff'){
										 		if(rules[category_value] == 1)
												cellProperties.readOnly = false;
											}else if(category_value === 'Acquisition'){
										 		if(rules[category_value] == 1)
												cellProperties.readOnly = false;
											}else if(category_value === 'Pre-Demo'){
										 		if(rules[category_value] == 1)
												cellProperties.readOnly = false;
											}else if(category_value === 'Demolition'){
										 		if(rules[category_value] == 1)
												cellProperties.readOnly = false;
											}else if(category_value === 'Greening (Post Demo)'){
										 		if(rules[category_value] == 1)
												cellProperties.readOnly = false;
											}else if(category_value === 'Administration'){
										 		if(rules[category_value] == 1)
												cellProperties.readOnly = false;
											}else{
												cellProperties.readOnly = true;
												cellProperties.source = false;
											}
											Handsontable.renderers.CheckboxRenderer.apply(this, arguments);
										}else{
											cellProperties.readOnly = true;
											cellProperties.source = false;
											Handsontable.renderers.CheckboxRenderer.apply(this, arguments);
										}
									  };
									 
						          </script>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
	</div>	
	