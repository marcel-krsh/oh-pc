@extends('layouts.allita')
<?
/*
// ACCOUNTING SUPPORT DATA

     $entitiesData = array(
     	array(
            'entity_name'=>'Ohio Housing Finance Agency',
            'user_id'=>'2',
            'active'=>'1',
            'address1'=>'57 E Main Street',
            'address2'=>'',
            'city'=>'Columbus',
            'state_id'=>'36',
            'zip'=>'43215',
            'phone'=>'(614) 466-7970',
            'fax'=>'',
            'web_address'=>'http://www.ohiohome.org',
            'email_address'=>'savethedreamohio@ohiohome.org',
            'logo_link'=>'https://ohiohome.org/images/logo@2x.png'
     		)
     	);   
     DB::table('entities')->insert($entitiesData); 
       
     $accountsData = array(
 		array(
            'account_name'=>'OHFA',
            'user_id'=>'2',
            'entity_id'=>'1'
 			)
     	);
     DB::table('accounts')->insert($accountsData);
            
     $transactionCategoriesData = array(
     	array(
            'category_name'=>'Fund Award',
            'active'=>1
     		),
     	array(
            'category_name'=>'Landbank Remimbursement',
            'active'=>1
     		),
     	array(
            'category_name'=>'Disposition',
            'active'=>1
     		),
     	array(
            'category_name'=>'OHA Reimbursement',
            'active'=>1
     		),
     	array(
            'category_name'=>'Line Of Credit',
            'active'=>1
     		)

     	);  
     DB::table('transaction_categories')->insert($transactionCategoriesData);
    
     $transactionTypesData = array(
     	array(
            'type_name'=>'Invoice',
            'active'=>1
     		),
     	array(
            'type_name'=>'Purchase Order',
            'active'=>1
     		),
     	array(
            'type_name'=>'Reimbursement Request',
            'active'=>1
     		),
     	array(
            'type_name'=>'Application Award',
            'active'=>1
     		),
     	array(
            'type_name'=>'Batch Payment',
            'active'=>1
     		),
     	array(
            'type_name'=>'Line of Credit',
            'active'=>1
     		)
     	);
      DB::table('transaction_types')->insert($transactionTypesData);
      
      $transactionStatusData = array(
      		array(
      		'status_name'=>'Pending',
            'active'=> 1
      		),
      		array(
      		'status_name'=>'Cleared',
            'active'=> 1
      		),
      		array(
      		'status_name'=>'Insufficient',
            'active'=> 1
      		),
      		array(
      		'status_name'=>'Reserved',
            'active'=> 1
      		)
      	);  
           DB::table('transaction_statuses')->insert($transactionStatusData);
            



// PUT IN PARCEL SUPPORTING DROP DOWN DATA
$parcelTypeOptionsData = array(
	array(
  		'option_name'=>'1-4 Units',
        'active'=> 1
  		),
  		array(
  		'option_name'=>'5-8 Units',
        'active'=> 1
  		),
  		array(
  		'option_name'=>'9-12 Units',
        'active'=> 1
  		),
  		array(
  		'option_name'=>'13-16 Units',
        'active'=> 1
      
		),
		array(
  		'option_name'=>'17+ Units',
        'active'=> 1
		)
	);
DB::table('parcel_type_options')->insert($parcelTypeOptionsData); 
            

$acquiredOptionData = array(
    		array(
		            'option_name' => 'Fannie Mae'
		        ),
    		array(
		            'option_name' => 'Forfeited Land Sale'
		        ),
    		array(
		            'option_name' => 'Freddy'
		        ),
    		array(
		            'option_name' => 'HUD'
		        ),
    		array(
		            'option_name' => 'Other'
		        ),
    		array(
		            'option_name' => 'Private/Other Donation'
		        ),
    		array(
		            'option_name' => 'Quit Claim'
		        ),
    		array(
		            'option_name' => 'Sheriff Transfer'
		        ),
    		array(
		            'option_name' => 'Tax Foreclosure'
		        )
    		);
        
        DB::table('how_acquired_options')->insert($acquiredOptionData);

 $propertyStatusOptionData = array(
    		array(
		            'option_name' => 'Pending'
		        ),
    		array(
		            'option_name' => 'Approved'
		        ),
    		array(
		            'option_name' => 'Withdrawn'
		        ),
    		array(
		            'option_name' => 'Declined'
		        )
    		);
       
        DB::table('property_status_options')->insert($propertyStatusOptionData);

$targetAreaData = array(
    			array(
		            'target_area_name' => 'A-1 Ashtabula City North','county_id' => '4'
		        ),
		        array(
		            'target_area_name' => 'A-2 Ashtabula City South','county_id' => '4'
		        ),
		        array(
		            'target_area_name' => 'B Conneaut City','county_id' => '4'
		        ),
		        array(
		            'target_area_name' => 'C Geneva City','county_id' => '4'
		        ),
		        array(
		            'target_area_name' => 'Pymatuning','county_id' => '4'
		        ),
		        array(
		            'target_area_name' => '1 (Martins Ferry)','county_id' => '7'
		        ),
		        array(
		            'target_area_name' => '2 (Bridgeport)','county_id' => '7'
		        ),
		        array(
		            'target_area_name' => '3 (Bellaire)','county_id' => '7'
		        ),
		        array(
		            'target_area_name' => '1 (2nd Ward)','county_id' => '9'
		        ),
		        array(
		            'target_area_name' => '2 (Prospect Hill/Grandview)','county_id' => '9'
		        ),
		        array(
		            'target_area_name' => '3 (North End/Fordson Heights)','county_id' => '9'
		        ),
		        array(
		            'target_area_name' => '5 (4th Ward)','county_id' => '9'
		        ),
		        array(
		            'target_area_name' => '6 (Lindenwald)','county_id' => '9'
		        ),
		        array(
		            'target_area_name' => '7 (East Hamilton)','county_id' => '9'
		        ),
		        array(
		            'target_area_name' => '8 (Douglass)','county_id' => '9'
		        ),
		        array(
		            'target_area_name' => '9 (Prospect)','county_id' => '9'
		        ),
		        array(
		            'target_area_name' => '10 (Sherman)','county_id' => '9'
		        ),
		        array(
		            'target_area_name' => '11 (South)','county_id' => '9'
		        ),
		        array(
		            'target_area_name' => 'Church/Oakland','county_id' => '9'
		        ),
		        array(
		            'target_area_name' => 'Armondale','county_id' => '9'
		        ),
		        array(
		            'target_area_name' => 'A (Grand Avenue South/Promise Neighborhood)','county_id' => '12'
		        ),
		        array(
		            'target_area_name' => 'B (Highlands/Southgate Neighborhood)','county_id' => '12'
		        ),
		        array(
		            'target_area_name' => 'C (Lagonda Re-development Corridor)','county_id' => '12'
		        ),
		        array(
		            'target_area_name' => 'D (Sheridan/Kenton Corridor)','county_id' => '12'
		        ),
		        array(
		            'target_area_name' => 'E (Sodown+)','county_id' => '12'
		        ),
		        array(
		            'target_area_name' => 'F (South Yellow Springs Corridor)','county_id' => '12'
		        ),
		        array(
		            'target_area_name' => 'G (Western Edge)','county_id' => '12'
		        ),
		        array(
		            'target_area_name' => '1 (East Liverpool)','county_id' => '15'
		        ),
		        array(
		            'target_area_name' => '2 (Salem)','county_id' => '15'
		        ),
		        array(
		            'target_area_name' => '3 (Wellsville)','county_id' => '15'
		        ),
		        array(
		            'target_area_name' => '1 Bellaire-Puritas','county_id' => '18'
		        ),
		        array(
		            'target_area_name' => '2 Broadway-Slavic Village','county_id' => '18'
		        ),
		        array(
		            'target_area_name' => '3 Brooklyn Centre','county_id' => '18'
		        ),
		        array(
		            'target_area_name' => '4 Buckeye - Shaker Square','county_id' => '18'
		        ),
		        array(
		            'target_area_name' => '5 Buckeye-Woodhill','county_id' => '18'
		        ),
		        array(
		            'target_area_name' => '6 Central','county_id' => '18'
		        ),
		        array(
		            'target_area_name' => '7 Clark-Fulton','county_id' => '18'
		        ),
		        array(
		            'target_area_name' => '8 Collinwood-Nottingham','county_id' => '18'
		        ),
		        array(
		            'target_area_name' => '9 Cudell','county_id' => '18'
		        ),
		        array(
		            'target_area_name' => '10 Cuyahoga Valley','county_id' => '18'
		        ),
		        array(
		            'target_area_name' => '11 Detroit Shoreway','county_id' => '18'
		        ),
		        array(
		            'target_area_name' => '12 Downtown','county_id' => '18'
		        ),
		        array(
		            'target_area_name' => '13 Edgewater','county_id' => '18'
		        ),
		        array(
		            'target_area_name' => '14 Euclid-Green','county_id' => '18'
		        ),
		        array(
		            'target_area_name' => '15 Fairfax','county_id' => '18'
		        ),
		        array(
		            'target_area_name' => '16 Glenville','county_id' => '18'
		        ),
		        array(
		            'target_area_name' => '17 Goodrich-Kirtland Pk','county_id' => '18'
		        ),
		        array(
		            'target_area_name' => '18 Hopkins','county_id' => '18'
		        ),
		        array(
		            'target_area_name' => '19 Hough','county_id' => '18'
		        ),
		        array(
		            'target_area_name' => '20 Jefferson','county_id' => '18'
		        ),
		        array(
		            'target_area_name' => '21 Kamm\'s','county_id' => '18'
		        ),
		        array(
		            'target_area_name' => '22 Kinsman','county_id' => '18'
		        ),
		        array(
		            'target_area_name' => '23 Lee-Harvard','county_id' => '18'
		        ),
		        array(
		            'target_area_name' => '24 Lee-Seville','county_id' => '18'
		        ),
		        array(
		            'target_area_name' => '25 Mount Pleasant','county_id' => '18'
		        ),
		        array(
		            'target_area_name' => '26 North Shore Collinwood','county_id' => '18'
		        ),
		        array(
		            'target_area_name' => '27 Ohio City','county_id' => '18'
		        ),
		        array(
		            'target_area_name' => '28 Old Brooklyn','county_id' => '18'
		        ),
		        array(
		            'target_area_name' => '29 St.Clair-Superior','county_id' => '18'
		        ),
		        array(
		            'target_area_name' => '30 Stockyards','county_id' => '18'
		        ),
		        array(
		            'target_area_name' => '31 Tremont','county_id' => '18'
		        ),
		        array(
		            'target_area_name' => '32 Union-Miles','county_id' => '18'
		        ),
		        array(
		            'target_area_name' => '33 University','county_id' => '18'
		        ),
		        array(
		            'target_area_name' => '34 West Boulevard','county_id' => '18'
		        ),
		        array(
		            'target_area_name' => '35 Bratenahl','county_id' => '18'
		        ),
		        array(
		            'target_area_name' => '36 Cleveland Heights','county_id' => '18'
		        ),
		        array(
		            'target_area_name' => '37 Cuyahoga Heights','county_id' => '18'
		        ),
		        array(
		            'target_area_name' => '38 East Cleveland','county_id' => '18'
		        ),
		        array(
		            'target_area_name' => '39 Euclid','county_id' => '18'
		        ),
		        array(
		            'target_area_name' => '40 Garfield Heights','county_id' => '18'
		        ),
		        array(
		            'target_area_name' => '41 Maple Heights','county_id' => '18'
		        ),
		        array(
		            'target_area_name' => '42 Newburgh Heights','county_id' => '18'
		        ),
		        array(
		            'target_area_name' => '43 North Randall','county_id' => '18'
		        ),
		        array(
		            'target_area_name' => '44 Shaker Heights','county_id' => '18'
		        ),
		        array(
		            'target_area_name' => '45 South Euclid','county_id' => '18'
		        ),
		        array(
		            'target_area_name' => '46 Warrensville Heights','county_id' => '18'
		        ),
		        array(
		            'target_area_name' => '1 - Kilbourne','county_id' => '22'
		        ),
		        array(
		            'target_area_name' => '2 - Southside','county_id' => '22'
		        ),
		        array(
		            'target_area_name' => '3 - Firelands','county_id' => '22'
		        ),
		        array(
		            'target_area_name' => '4 - Eastside','county_id' => '22'
		        ),
		        array(
		            'target_area_name' => '5 - Homestead','county_id' => '22'
		        ),
		        array(
		            'target_area_name' => '6 - Searsville','county_id' => '22'
		        ),
		        array(
		            'target_area_name' => '7 - Homeville','county_id' => '22'
		        ),
		        array(
		            'target_area_name' => '8 - Crystal Rock','county_id' => '22'
		        ),
		        array(
		            'target_area_name' => '9 - Vermilion','county_id' => '22'
		        ),
		        array(
		            'target_area_name' => '1 Walnut Township - Roby Subdivision','county_id' => '23'
		        ),
		        array(
		            'target_area_name' => '2 Fairfield Beach','county_id' => '23'
		        ),
		        array(
		            'target_area_name' => '3 Walnut Township - Taylor Sandy Beach','county_id' => '23'
		        ),
		        array(
		            'target_area_name' => '4 Walnut Township New Salem','county_id' => '23'
		        ),
		        array(
		            'target_area_name' => '5 Columbus/Reynoldsburg','county_id' => '23'
		        ),
		        array(
		            'target_area_name' => '6 Pickerington','county_id' => '23'
		        ),
		        array(
		            'target_area_name' => '7 Lancaster East','county_id' => '23'
		        ),
		        array(
		            'target_area_name' => '8 Lancaster South','county_id' => '23'
		        ),
		        array(
		            'target_area_name' => '9 Lancaster West','county_id' => '23'
		        ),
		        array(
		            'target_area_name' => '1 – Whitehall','county_id' => '25'
		        ),
		        array(
		            'target_area_name' => '2 – Near Northeast','county_id' => '25'
		        ),
		        array(
		            'target_area_name' => '3 – Franklinton/Franklin Twp','county_id' => '25'
		        ),
		        array(
		            'target_area_name' => '4 – Southside','county_id' => '25'
		        ),
		        array(
		            'target_area_name' => 'A – East Side','county_id' => '31'
		        ),
		        array(
		            'target_area_name' => 'B – West Side','county_id' => '31'
		        ),
		        array(
		            'target_area_name' => 'C – North Side','county_id' => '31'
		        ),
		        array(
		            'target_area_name' => 'A.1 (Steubenville Central North)','county_id' => '41'
		        ),
		        array(
		            'target_area_name' => 'A.2 (Steubenville Central South)','county_id' => '41'
		        ),
		        array(
		            'target_area_name' => 'B (Toronto Area)','county_id' => '41'
		        ),
		        array(
		            'target_area_name' => 'C (Mingo Junction-Brilliant Area)','county_id' => '41'
		        ),
		        array(
		            'target_area_name' => 'D (Smithfield and Mount Pleasant Townships)','county_id' => '41'
		        ),
		        array(
		            'target_area_name' => '1 (Eastlake Area-wide)','county_id' => '43'
		        ),
		        array(
		            'target_area_name' => '2 (Madison Area - North & Lake)','county_id' => '43'
		        ),
		        array(
		            'target_area_name' => '3 (Mentor Area - North & Lake)','county_id' => '43'
		        ),
		        array(
		            'target_area_name' => '4 (Painesville - Area-wide)','county_id' => '43'
		        ),
		        array(
		            'target_area_name' => '5 (Painesville Township - Lake Park Area)','county_id' => '43'
		        ),
		        array(
		            'target_area_name' => '6 (Wickliffe - Southwestern Area)','county_id' => '43'
		        ),
		        array(
		            'target_area_name' => '7 (Willowick Area-wide)','county_id' => '43'
		        ),
		        array(
		            'target_area_name' => 'A.1 – Central Lorain','county_id' => '47'
		        ),
		        array(
		            'target_area_name' => 'A.2 –  South Lorain','county_id' => '47'
		        ),
		        array(
		            'target_area_name' => 'A.3 – East Side of Lorain','county_id' => '47'
		        ),
		        array(
		            'target_area_name' => 'B.1 – Sheffield Township','county_id' => '47'
		        ),
		        array(
		            'target_area_name' => 'C.1 – South Side of Elyria','county_id' => '47'
		        ),
		        array(
		            'target_area_name' => 'C.2 – East Side of Elyria','county_id' => '47'
		        ),
		        array(
		            'target_area_name' => 'C.3 – West Side of Elyria','county_id' => '47'
		        ),
		        array(
		            'target_area_name' => 'A – Toledo South End','county_id' => '48'
		        ),
		        array(
		            'target_area_name' => 'B – Old West/TOTCO','county_id' => '48'
		        ),
		        array(
		            'target_area_name' => 'C – Ottawa/BUMA','county_id' => '48'
		        ),
		        array(
		            'target_area_name' => 'D – ONXY/Englewood','county_id' => '48'
		        ),
		        array(
		            'target_area_name' => 'E – River East','county_id' => '48'
		        ),
		        array(
		            'target_area_name' => 'F – North River','county_id' => '48'
		        ),
		        array(
		            'target_area_name' => 'G – Library Village','county_id' => '48'
		        ),
		        array(
		            'target_area_name' => 'H – Arlington Scott Park','county_id' => '48'
		        ),
		        array(
		            'target_area_name' => 'Austintown','county_id' => '50'
		        ),
		        array(
		            'target_area_name' => 'Boardman','county_id' => '50'
		        ),
		        array(
		            'target_area_name' => 'Boulevard Park','county_id' => '50'
		        ),
		        array(
		            'target_area_name' => 'Buckeye/Lansingville','county_id' => '50'
		        ),
		        array(
		            'target_area_name' => 'Campbell','county_id' => '50'
		        ),
		        array(
		            'target_area_name' => 'Crandall Park North','county_id' => '50'
		        ),
		        array(
		            'target_area_name' => 'East Side','county_id' => '50'
		        ),
		        array(
		            'target_area_name' => 'Idora Garden District','county_id' => '50'
		        ),
		        array(
		            'target_area_name' => 'Kirkmere','county_id' => '50'
		        ),
		        array(
		            'target_area_name' => 'Struthers','county_id' => '50'
		        ),
		        array(
		            'target_area_name' => 'West Side A','county_id' => '50'
		        ),
		        array(
		            'target_area_name' => 'West Side B','county_id' => '50'
		        ),
		        array(
		            'target_area_name' => '1 – Wolf Creek','county_id' => '57'
		        ),
		        array(
		            'target_area_name' => '2 – Greater Dayton View','county_id' => '57'
		        ),
		        array(
		            'target_area_name' => '3 – FROC','county_id' => '57'
		        ),
		        array(
		            'target_area_name' => '4 – Old North Dayton','county_id' => '57'
		        ),
		        array(
		            'target_area_name' => '5 – East Dayton','county_id' => '57'
		        ),
		        array(
		            'target_area_name' => '6 – Belmont-Eastmont-Hearthstone','county_id' => '57'
		        ),
		        array(
		            'target_area_name' => '7 – Carillon','county_id' => '57'
		        ),
		        array(
		            'target_area_name' => '8 – Ridgewood Heights','county_id' => '57'
		        ),
		        array(
		            'target_area_name' => '9 –  Blairwood','county_id' => '57'
		        ),
		        array(
		            'target_area_name' => '10 – Salem Village/North Gate','county_id' => '57'
		        ),
		        array(
		            'target_area_name' => '11 – Olde Town','county_id' => '57'
		        ),
		        array(
		            'target_area_name' => '12 – Townview','county_id' => '57'
		        ),
		        array(
		            'target_area_name' => '13 – Drexel/Crown Point','county_id' => '57'
		        ),
		        array(
		            'target_area_name' => '14 – Meadowdale','county_id' => '57'
		        ),
		        array(
		            'target_area_name' => '15 – Markey','county_id' => '57'
		        ),
		        array(
		            'target_area_name' => '16 – South Shiloh','county_id' => '57'
		        ),
		        array(
		            'target_area_name' => '17 – Siebenthaler','county_id' => '57'
		        ),
		        array(
		            'target_area_name' => '18 – Northridge North','county_id' => '57'
		        ),
		        array(
		            'target_area_name' => '19 – Northridge Woodland Hills','county_id' => '57'
		        ),
		        array(
		            'target_area_name' => '20 – Northridge Traffic Circle','county_id' => '57'
		        ),
		        array(
		            'target_area_name' => 'South Shiloh - Central','county_id' => '57'
		        ),
		        array(
		            'target_area_name' => 'South Shiloh - Riverside','county_id' => '57'
		        ),
		        array(
		            'target_area_name' => 'Traffic Circle','county_id' => '57'
		        ),
		        array(
		            'target_area_name' => 'Edgemont','county_id' => '57'
		        ),
		        array(
		            'target_area_name' => 'Chautauqua Road Area','county_id' => '57'
		        ),
		        array(
		            'target_area_name' => 'Huber South','county_id' => '57'
		        ),
		        array(
		            'target_area_name' => 'Lynnhaven-Riverside','county_id' => '57'
		        ),
		        array(
		            'target_area_name' => 'Spinning Hills-Riverside','county_id' => '57'
		        ),
		        array(
		            'target_area_name' => 'Avondale-Riverside','county_id' => '57'
		        ),
		        array(
		            'target_area_name' => 'Wright Point-Riverside','county_id' => '57'
		        ),
		        array(
		            'target_area_name' => 'Olde Dowtown West-West Carrolton','county_id' => '57'
		        ),
		        array(
		            'target_area_name' => '1 (Kent)','county_id' => '67'
		        ),
		        array(
		            'target_area_name' => '2 (Ravenna)','county_id' => '67'
		        ),
		        array(
		            'target_area_name' => '3 (Ravenna Twp)','county_id' => '67'
		        ),
		        array(
		            'target_area_name' => '4 (Bal. of County)','county_id' => '67'
		        ),
		        array(
		            'target_area_name' => '1 – Woodville','county_id' => '70'
		        ),
		        array(
		            'target_area_name' => '2 – Appleseed','county_id' => '70'
		        ),
		        array(
		            'target_area_name' => '3 – North Lake/Middle Park','county_id' => '70'
		        ),
		        array(
		            'target_area_name' => '4 – Creveling','county_id' => '70'
		        ),
		        array(
		            'target_area_name' => '5 – Newman/Library Park','county_id' => '70'
		        ),
		        array(
		            'target_area_name' => '6 – Northern Downtown Mansfield','county_id' => '70'
		        ),
		        array(
		            'target_area_name' => '7 – Southern Mansfield','county_id' => '70'
		        ),
		        array(
		            'target_area_name' => '8 – Central Mansfield','county_id' => '70'
		        ),
		        array(
		            'target_area_name' => '9 – Madison Twp','county_id' => '70'
		        ),
		        array(
		            'target_area_name' => '10 – Shelby','county_id' => '70'
		        ),
		        array(
		            'target_area_name' => '11 – Plymouth','county_id' => '70'
		        ),
		        array(
		            'target_area_name' => 'Northeast','county_id' => '76'
		        ),
		        array(
		            'target_area_name' => 'Northwest','county_id' => '76'
		        ),
		        array(
		            'target_area_name' => 'Southeast','county_id' => '76'
		        ),
		        array(
		            'target_area_name' => 'Southwest','county_id' => '76'
		        ),
		        array(
		            'target_area_name' => '1 – Alliance1','county_id' => '76'
		        ),
		        array(
		            'target_area_name' => '3 – Alliance3','county_id' => '76'
		        ),
		        array(
		            'target_area_name' => '4 - Alliance','county_id' => '76'
		        ),
		        array(
		            'target_area_name' => '5 – Massillon','county_id' => '76'
		        ),
		        array(
		            'target_area_name' => '6 – Massillon','county_id' => '76'
		        ),
		        array(
		            'target_area_name' => 'TA1 – North Hill1','county_id' => '77'
		        ),
		        array(
		            'target_area_name' => 'TA2 – North Hill2','county_id' => '77'
		        ),
		        array(
		            'target_area_name' => 'TA3 – North Hill3','county_id' => '77'
		        ),
		        array(
		            'target_area_name' => 'TA4 – North Hill4','county_id' => '77'
		        ),
		        array(
		            'target_area_name' => 'TA5 – Sherbondy','county_id' => '77'
		        ),
		        array(
		            'target_area_name' => 'TA6 – Goosetown','county_id' => '77'
		        ),
		        array(
		            'target_area_name' => 'TA7 – North Hill7','county_id' => '77'
		        ),
		        array(
		            'target_area_name' => 'TA8 – Thomastown','county_id' => '77'
		        ),
		        array(
		            'target_area_name' => 'TA9 – Arlington','county_id' => '77'
		        ),
		        array(
		            'target_area_name' => 'TA10 – Barberton','county_id' => '77'
		        ),
		        array(
		            'target_area_name' => 'TA11 – Lakemore','county_id' => '77'
		        ),
		        array(
		            'target_area_name' => 'TA12 – Springfield','county_id' => '77'
		        ),
		        array(
		            'target_area_name' => '13-	Lakemore','county_id' => '77'
		        ),
		        array(
		            'target_area_name' => '14-	Bath','county_id' => '77'
		        ),
		        array(
		            'target_area_name' => '15-	Boston','county_id' => '77'
		        ),
		        array(
		            'target_area_name' => '16-	Boston heights','county_id' => '77'
		        ),
		        array(
		            'target_area_name' => '17-	Clinton','county_id' => '77'
		        ),
		        array(
		            'target_area_name' => '18-	Copley','county_id' => '77'
		        ),
		        array(
		            'target_area_name' => '19-	Coventry','county_id' => '77'
		        ),
		        array(
		            'target_area_name' => '20-	Cuyahoga Falls','county_id' => '77'
		        ),
		        array(
		            'target_area_name' => '21-	Fairlawn','county_id' => '77'
		        ),
		        array(
		            'target_area_name' => '22-	Green','county_id' => '77'
		        ),
		        array(
		            'target_area_name' => '23-	Hudson','county_id' => '77'
		        ),
		        array(
		            'target_area_name' => '24-	Macedonia','county_id' => '77'
		        ),
		        array(
		            'target_area_name' => '25-	Munroe Falls','county_id' => '77'
		        ),
		        array(
		            'target_area_name' => '26-	Mogadore','county_id' => '77'
		        ),
		        array(
		            'target_area_name' => '27-	New Franklin','county_id' => '77'
		        ),
		        array(
		            'target_area_name' => '28-	Norton','county_id' => '77'
		        ),
		        array(
		            'target_area_name' => '29-	Northfield','county_id' => '77'
		        ),
		        array(
		            'target_area_name' => '30-	Northfield Center','county_id' => '77'
		        ),
		        array(
		            'target_area_name' => '31-	Peninsula','county_id' => '77'
		        ),
		        array(
		            'target_area_name' => '32-	Richfield Village','county_id' => '77'
		        ),
		        array(
		            'target_area_name' => '33-	Richfield','county_id' => '77'
		        ),
		        array(
		            'target_area_name' => '34-	Reminderville','county_id' => '77'
		        ),
		        array(
		            'target_area_name' => '35-	Sagamore Hills','county_id' => '77'
		        ),
		        array(
		            'target_area_name' => '36-	Silver Lake','county_id' => '77'
		        ),
		        array(
		            'target_area_name' => '37-	Stow','county_id' => '77'
		        ),
		        array(
		            'target_area_name' => '38-	Twinsburg','county_id' => '77'
		        ),
		        array(
		            'target_area_name' => '39-	Twinsburg TWP','county_id' => '77'
		        ),
		        array(
		            'target_area_name' => '40-	Tallmadge','county_id' => '77'
		        ),
		        array(
		            'target_area_name' => '1-	Leavittsburg','county_id' => '78'
		        ),
		        array(
		            'target_area_name' => '2-	Brookfield','county_id' => '78'
		        ),
		        array(
		            'target_area_name' => '3-	Hubbard','county_id' => '78'
		        ),
		        array(
		            'target_area_name' => '4-	Masury','county_id' => '78'
		        ),
		        array(
		            'target_area_name' => '1 – Jefferson School','county_id' => '78'
		        ),
		        array(
		            'target_area_name' => '2 – Central City Garden','county_id' => '78'
		        ),
		        array(
		            'target_area_name' => '3 – Williard School','county_id' => '78'
		        ),
		        array(
		            'target_area_name' => '4 – AmVets','county_id' => '78'
		        ),
		        array(
		            'target_area_name' => '5 – Historic Perkins','county_id' => '78'
		        ),
		        array(
		            'target_area_name' => '6 – Near East/Trumbull Memorial Hospital','county_id' => '78'
		        ),
		        array(
		            'target_area_name' => '7 – Palmyra Hts','county_id' => '78'
		        ),
		        array(
		            'target_area_name' => '8 – Westlawn/Southwest','county_id' => '78'
		        ),
		        array(
		            'target_area_name' => '9 – North End','county_id' => '78'
		        ),
		        array(
		            'target_area_name' => '10 – Northwest','county_id' => '78'
		        ),
		        array(
		            'target_area_name' => '11 – Central Girard','county_id' => '78'
		        )




    		);
    	
DB::table('target_areas')->insert($targetAreaData);

// PUT IN STATE DATA
$stateData = array(
    			array(
		            'state_name' => 'Alabama',
		            'state_acronym' => 'AL'
		        ),
		        array(
				    'state_acronym' =>'AK',
				    'state_name' =>'Alaska'),
			    array(
				    'state_acronym' =>'AZ',
				    'state_name' =>'Arizona'),
			    array(
				    'state_acronym' =>'AR','state_name' =>'Arkansas'),
			    array(
				    'state_acronym' =>'CA','state_name' =>'California'),
			    array(
				    'state_acronym' =>'CO','state_name' =>'Colorado'),
			    array(
				    'state_acronym' =>'CT','state_name' =>'Connecticut'),
			    array(
				    'state_acronym' =>'DE','state_name' =>'Delaware'),
			    array(
				    'state_acronym' =>'DC','state_name' =>'District of Columbia'),
			    array(
				    'state_acronym' =>'FL','state_name' =>'Florida'),
			    array(
				    'state_acronym' =>'GA','state_name' =>'Georgia'),
			    array(
				    'state_acronym' =>'HI','state_name' =>'Hawaii'),
			    array(
				    'state_acronym' =>'ID','state_name' =>'Idaho'),
			    array(
				    'state_acronym' =>'IL','state_name' =>'Illinois'),
			    array(
				    'state_acronym' =>'IN','state_name' =>'Indiana'),
			    array(
				    'state_acronym' =>'IA','state_name' =>'Iowa'),
			    array(
				    'state_acronym' =>'KS','state_name' =>'Kansas'),
			    array(
				    'state_acronym' =>'KY','state_name' =>'Kentucky'),
			    array(
				    'state_acronym' =>'LA','state_name' =>'Louisiana'),
			    array(
				    'state_acronym' =>'ME','state_name' =>'Maine'),
			    array(
				    'state_acronym' =>'MD','state_name' =>'Maryland'),
			    array(
				    'state_acronym' =>'MA','state_name' =>'Massachusetts'),
			    array(
				    'state_acronym' =>'MI','state_name' =>'Michigan'),
			    array(
				    'state_acronym' =>'MN','state_name' =>'Minnesota'),
			    array(
				    'state_acronym' =>'MS','state_name' =>'Mississippi'),
			    array(
				    'state_acronym' =>'MO','state_name' >'Missouri'),
			    array(
				    'state_acronym' =>'MT','state_name' =>'Montana'),
			    array(
				    'state_acronym' =>'NE','state_name' =>'Nebraska'),
			    array(
				    'state_acronym' =>'NV','state_name' =>'Nevada'),
			    array(
				    'state_acronym' =>'NH','state_name' =>'New Hampshire'),
			    array(
				    'state_acronym' =>'NJ','state_name' =>'New Jersey'),
			    array(
				    'state_acronym' =>'NM','state_name' =>'New Mexico'),
			    array(
				    'state_acronym' =>'NY','state_name' =>'New York'),
			    array(
				    'state_acronym' =>'NC','state_name' =>'North Carolina'),
			    array(
				    'state_acronym' =>'ND','state_name' =>'North Dakota'),
			    array(
				    'state_acronym' =>'OH','state_name' =>'Ohio'),
			    array(
				    'state_acronym' =>'OK','state_name' =>'Oklahoma'),
			    array(
				    'state_acronym' =>'OR','state_name' =>'Oregon'),
			    array(
				    'state_acronym' =>'PA','state_name' =>'Pennsylvania'),
			    array(
				    'state_acronym' =>'RI','state_name' =>'Rhode Island'),
			    array(
				    'state_acronym' =>'SC','state_name' =>'South Carolina'),
			    array(
				    'state_acronym' =>'SD','state_name' =>'South Dakota'),
			    array(
				    'state_acronym' =>'TN','state_name' =>'Tennessee'),
			    array(
				    'state_acronym' =>'TX','state_name' =>'Texas'),
			    array(
				    'state_acronym' =>'UT','state_name' =>'Utah'),
			    array(
				    'state_acronym' =>'VT','state_name' =>'Vermont'),
			    array(
				    'state_acronym' =>'VA','state_name' =>'Virginia'),
			    array(
				    'state_acronym' =>'WA','state_name' =>'Washington'),
			    array(
				    'state_acronym' =>'WV','state_name' =>'West Virginia'),
			    array(
				    'state_acronym' =>'WI','state_name' =>'Wisconsin'),
			    array(
				    'state_acronym' =>'WY','state_name' =>'Wyoming'
		        ),
    		);
        DB::table('states')->insert($stateData);
// PUT IN COUNTY DATA
$countyData = array(
    		array(
		        	
		            'county_name' => 'Adams',
		            'state_id' => '36'
		        ),
		        array(
		        	
				    'state_id' =>'36','county_name' =>'Allen'),
			    array(
		        	
				    'state_id' =>'36','county_name' =>'Ashland'),
			    array(
		        	
				    'state_id' =>'36','county_name' =>'Ashtabula'),
			    array(
		        	
				    'state_id' =>'36','county_name' =>'Athens'),
			    array(
		        	
				    'state_id' =>'36','county_name' =>'Auglaize'),
			    array(
		        	
				    'state_id' =>'36','county_name' =>'Belmont'),
			    array(
		        	
				    'state_id' =>'36','county_name' =>'Brown'),
			    array(
		        	
				    'state_id' =>'36','county_name' =>'Butler'),
			    array(
		        	
				    'state_id' =>'36','county_name' =>'Carroll'),
			    array(
		        	
				    'state_id' =>'36','county_name' =>'Champaign'),
			    array(
		        	
				    'state_id' =>'36','county_name' =>'Clark'),
			    array(
				    'state_id' =>'36','county_name' =>'Clermont'),
			    array(
				    'state_id' =>'36','county_name' =>'Clinton'),
			    array(
				    'state_id' =>'36','county_name' =>'Columbiana'),
			    array(
				    'state_id' =>'36','county_name' =>'Coshocton'),
			    array(
				    'state_id' =>'36','county_name' =>'Crawford'),
			    array(
				    'state_id' =>'36','county_name' =>'Cuyahoga'),
			    array(
				    'state_id' =>'36','county_name' =>'Darke'),
			    array(
				    'state_id' =>'36','county_name' =>'Defiance'),
			    array(
				    'state_id' =>'36','county_name' =>'Delaware'),
			    array(
				    'state_id' =>'36','county_name' =>'Erie'),
			    array(
				    'state_id' =>'36','county_name' =>'Fairfield'),
			    array(
				    'state_id' =>'36','county_name' =>'Fayette'),
			    array(
				    'state_id' =>'36','county_name' =>'Franklin'),
			    array(
				    'state_id' =>'36','county_name' >'Fulton'),
			    array(
				    'state_id' =>'36','county_name' =>'Gallia'),
			    array(
				    'state_id' =>'36','county_name' =>'Geauga'),
			    array(
				    'state_id' =>'36','county_name' =>'Greene'),
			    array(
				    'state_id' =>'36','county_name' =>'Guernsey'),
			    array(
				    'state_id' =>'36','county_name' =>'Hamilton'),
			    array(
				    'state_id' =>'36','county_name' =>'Hancock'),
			    array(
				    'state_id' =>'36','county_name' =>'Hardin'),
			    array(
				    'state_id' =>'36','county_name' =>'Harrison'),
			    array(
				    'state_id' =>'36','county_name' =>'Henry'),
			    array(
				    'state_id' =>'36','county_name' =>'Highland'),
			    array(
				    'state_id' =>'36','county_name' =>'Hocking'),
			    array(
				    'state_id' =>'36','county_name' =>'Holmes'),
			    array(
				    'state_id' =>'36','county_name' =>'Huron'),
			    array(
				    'state_id' =>'36','county_name' =>'Jackson'),
			    array(
				    'state_id' =>'36','county_name' =>'Jefferson'),
			    array(
				    'state_id' =>'36','county_name' =>'Knox'),
			    array(
				    'state_id' =>'36','county_name' =>'Lake'),
			    array(
				    'state_id' =>'36','county_name' =>'Lawrence'),
			    array(
				    'state_id' =>'36','county_name' =>'Licking'),
			    array(
				    'state_id' =>'36','county_name' =>'Logan'),
			    array(
				    'state_id' =>'36','county_name' =>'Lorain'),
			    array(
				    'state_id' =>'36','county_name' =>'Lucas'),
			    array(
				    'state_id' =>'36','county_name' =>'Madison'),
			    array(
				    'state_id' =>'36','county_name' =>'Mahoning'),
			    array(
				    'state_id' =>'36','county_name' =>'Marion'
		        ),
		        array(
				    'state_id' =>'36','county_name' =>'Medina'
		        ),
		        array(
				    'state_id' =>'36','county_name' =>'Meigs'
		        ),
		        array(
				    'state_id' =>'36','county_name' =>'Mercer'
		        ),
		        array(
				    'state_id' =>'36','county_name' =>'Miami'
		        ),
		        array(
				    'state_id' =>'36','county_name' =>'Monroe'
		        ),
		        array(
				    'state_id' =>'36','county_name' =>'Montgomery'
		        ),
		        array(
				    'state_id' =>'36','county_name' =>'Morgan'
		        ),
		        array(
				    'state_id' =>'36','county_name' =>'Morrow'
		        ),
		        array(
				    'state_id' =>'36','county_name' =>'Muskingum'
		        ),
		        array(
				    'state_id' =>'36','county_name' =>'Noble'
		        ),
		        array(
				    'state_id' =>'36','county_name' =>'Ottawa'
		        ),
		        array(
				    'state_id' =>'36','county_name' =>'Paulding'
		        ),
		        array(
				    'state_id' =>'36','county_name' =>'Perry'
		        ),
		        array(
				    'state_id' =>'36','county_name' =>'Pickaway'
		        ),
		        array(
				    'state_id' =>'36','county_name' =>'Pike'
		        ),
		        array(
				    'state_id' =>'36','county_name' =>'Portage'
		        ),
		        array(
				    'state_id' =>'36','county_name' =>'Preble'
		        ),
		        array(
				    'state_id' =>'36','county_name' =>'Putnam'
		        ),
		        array(
				    'state_id' =>'36','county_name' =>'Richland'
		        ),
		        array(
				    'state_id' =>'36','county_name' =>'Ross'
		        ),
		        array(
				    'state_id' =>'36','county_name' =>'Sandusky'
		        ),
		        array(
				    'state_id' =>'36','county_name' =>'Scioto'
		        ),
		        array(
				    'state_id' =>'36','county_name' =>'Seneca'
		        ),
		        array(
				    'state_id' =>'36','county_name' =>'Shelby'
		        ),
		        array(
				    'state_id' =>'36','county_name' =>'Stark'
		        ),
		        array(
				    'state_id' =>'36','county_name' =>'Summit'
		        ),
		        array(
				    'state_id' =>'36','county_name' =>'Trumbull'
		        ),
		        array(
				    'state_id' =>'36','county_name' =>'Tuscarawas'
		        ),
		        array(
				    'state_id' =>'36','county_name' =>'Union'
		        ),
		        array(
				    'state_id' =>'36','county_name' =>'Van Wert'
		        ),
		        array(
				    'state_id' =>'36','county_name' =>'Vinton'
		        ),
		        array(
				    'state_id' =>'36','county_name' =>'Warren'
		        ),
		        array(
				    'state_id' =>'36','county_name' =>'Washington'
		        ),
		        array(
				    'state_id' =>'36','county_name' =>'Wayne'
		        ),
		        array(
				    'state_id' =>'36','county_name' =>'Williams'
		        ),
		        array(
				    'state_id' =>'36','county_name' =>'Wood'
		        ),
		        array(
				    'state_id' =>'36','county_name' =>'Wyandot'
		        ),
    		);
        DB::table('counties')->insert($countyData);

      	$rolesData = array(
      		array(
            'role_parent_id'=>1,
            'role_name'=>'OHFA NIP ROLES',
            'active'=>1
      		),
      		array(
            'role_parent_id'=>1,
            'role_name'=>'OHFA Admin',
            'active'=>1
      		),
      		array(
            'role_parent_id'=>1,
            'role_name'=>'OHFA Reviewer',
            'active'=>1
      		),
      		array(
            'role_parent_id'=>1,
            'role_name'=>'OHFA Approver',
            'active'=>1
      		),
      		array(
            'role_parent_id'=>1,
            'role_name'=>'Landbank Admin',
            'active'=>1
      		),
      		array(
            'role_parent_id'=>1,
            'role_name'=>'Landbank Owner',
            'active'=>1
      		),
      		array(
            'role_parent_id'=>1,
            'role_name'=>'Landbank Processor',
            'active'=>1
      		),
      		array(
            'role_parent_id'=>1,
            'role_name'=>'Landbank Approver',
            'active'=>1
      		) 
      	);
      	DB::table('roles')->insert($rolesData);
*/
       ?>
       @section('content')
       Successfully loaded in default base data. <Br />DO NOT REFRESH THIS SCREEN!
       @stop
       