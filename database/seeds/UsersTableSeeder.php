<?php

use Illuminate\Database\Seeder;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {



        $breakoutItemsStatusData = [
            [
            'breakout_item_status_name'=>'Pending'
            //1
            ],
            [
            'breakout_item_status_name'=>'Approved'
            //2
            ],
            [
            'breakout_item_status_name'=>'Declined'
            //3
            ],
            [
            'breakout_item_status_name'=>'Corrections Requested'
            //4
            ],
            [
            'breakout_item_status_name'=>'Corrections Made'
            //5
            ],
            [
            'breakout_item_status_name'=>'Cancelled'
            //6
            ],
            [
            'breakout_item_status_name'=>'Paid'
            //7
            ],
            [
            'breakout_item_status_name'=>'Invoiced'
            //7
            ]
        ];
        DB::table('breakout_items_statuses')->insert($breakoutItemsStatusData);

        // $expenseCategoriesData = array(
        //     array(
        //     'expense_category_name'=>'Expense Categories',
        //     'parent_id'=>0,
        //     ),
        //     array(
        //     'expense_category_name'=>'Acquisition',
        //     'parent_id'=>1
        //     ),
        //     array(
        //     'expense_category_name'=>'Pre-Demo',
        //     'parent_id'=>1
        //     ),
        //     array(
        //     'expense_category_name'=>'Demolition',
        //     'parent_id'=>1
        //     ),
        //     array(
        //     'expense_category_name'=>'Greening',
        //     'parent_id'=>1
        //     ),
        //     array(
        //     'expense_category_name'=>'Maintenance',
        //     'parent_id'=>1
        //     ),
        //     array(
        //     'expense_category_name'=>'Administration',
        //     'parent_id'=>1
        //     ),
        //     array(
        //     'expense_category_name'=>'Other',
        //     'parent_id'=>1
        //     ),
        //     array(
        //     'expense_category_name'=>'NIP Loan Payoff',
        //     'parent_id'=>1
        //     ),
        //     array(
        //     'expense_category_name'=>'Required Documents',
        //     'parent_id'=>1
        //     ),
        // );
        // DB::table('expense_categories')->insert($expenseCategoriesData);

        $breakOutTypesData = [
            [
            'breakout_type_name'=>'Landbank Reimbursement'
            ],
            [
            'breakout_type_name'=>'HFA Reimbursement'
            ],
            [
            'breakout_type_name'=>'Landbank Advance'
            ],
            [
            'breakout_type_name'=>'Landbank LOC'
            ]
            
        ];
        DB::table('break_out_types')->insert($breakOutTypesData);



        DB::table('vendors')->insert([
            'vendor_name'=>'Legacy Vendor',
            'vendor_email'=>null,
            'vendor_phone'=>null,
            'vendor_mobile_phone'=>null,
            'vendor_fax'=>null,
            'vendor_street_address'=>null,
            'vendor_street_address2'=>null,
            'vendor_city'=>null,
            'vendor_state_id'=>36,
            'vendor_zip'=>null,
            'vendor_duns'=>null,
            'passed_sam_gov'=>null,
            'active'=>1,
            'vendor_notes'=>'This is a place holder vendor for legacy parcels prior to the ability to specify vendors for break out costs.'

        ]);

        DB::table('users')->insert([
            'name' => 'Brian Greenwood',
            'email' => 'brian@greenwood360.com',
            'password' => bcrypt('M0therBoard4247'),
            'entity_type'=>'hfa',
            'badge_color' => 'blue',
            'entity_id' => '1',
            'active' => '1',
            'verified' => '1',
        ]);

        // Users Seed
        // DO NOT ADD USERS TO THE TOP - THE Entities use the cardinality of the users entered. Add to the bottom.
        DB::table('users')->insert([
            'name' => 'Holly Swisher',
            'email' => 'hswisher@ohiohome.org',
            'password' => bcrypt('Allita12'),
            'badge_color' => 'pink',
            'entity_id' => '1',
            'entity_type'=>'hfa',
            'active' => '1',
            'verified' => '1',

        ]);

        $countyUsers = [
                [
                'name'=>'Ashtabula County Land Reutilization Corporation',
                'email'=>'Landbank@ashtabulacounty.us',
                'password'=>bcrypt('7aster!'),
                'badge_color' => 'blue',
                'entity_id' => '2','entity_type'=>'landbank',
                'active' => '1',
                'verified' => '1',
                ],
                [
                    'name'=>'Belmont County Land Reutilization Corporation',
                    'email'=>'ginny.favede@co.belmont.oh.us',
                    'password'=>bcrypt('cav$dsh3') ,
                    'badge_color' => 'blue',
                    'entity_id' => '3','entity_type'=>'landbank',
                    'active' => '1',
                    'verified' => '1',
                    ],
                [
                    'name'=>'Butler County Land Reutilization Corporation',
                    'email'=>'nixn@butlercountyohio.org',
                    'password'=>bcrypt('@mu#1py') ,
                    'badge_color' => 'blue',
                    'entity_id' => '4','entity_type'=>'landbank',
                    'active' => '1',
                    'verified' => '1',
                    ],
                [
                    'name'=>'Central Ohio Community Improvement Corporation',
                    'email'=>'jcrosenberger@cocic.org',
                    'password'=>bcrypt('#osu1&&') ,
                    'badge_color' => 'blue',
                    'entity_id' => '5','entity_type'=>'landbank',
                    'active' => '1',
                    'verified' => '1',

                    ],
                [
                    'name'=>'Clark County Land Reutilization Corporation',
                    'email'=>'dfleck@clarkcountyohio.gov',
                    'password'=>bcrypt('lu&clk7') ,
                    'badge_color' => 'blue',
                    'entity_id' => '6','entity_type'=>'landbank',
                    'active' => '1',
                    'verified' => '1',

                    ],
                [
                    'name'=>'Columbiana County Land Reutilization Corp',
                    'email'=>'therold@columbianacodev.org',
                    'password'=>bcrypt('ana@poly9') ,
                    'badge_color' => 'blue',
                    'entity_id' => '7','entity_type'=>'landbank',
                    'active' => '1',
                    'verified' => '1',

                    ],
                [
                    'name'=>'Cuyahoga County Land Reutilization Corporation',
                    'email'=>'bwhitney@cuyahogalandbank.org',
                    'password'=>bcrypt('cavs1$') ,
                    'badge_color' => 'blue',
                    'entity_id' => '8','entity_type'=>'landbank',
                    'active' => '1',
                    'verified' => '1',

                    ],
                [
                    'name'=>'Erie County Land Reutilization Corp',
                    'email'=>'sschell@eriecounty.oh.gov',
                    'password'=>bcrypt('$rommel7') ,
                    'badge_color' => 'blue',
                    'entity_id' => '9','entity_type'=>'landbank',
                    'active' => '1',
                    'verified' => '1',

                    ],
                [
                    'name'=>'Fairfield County Land Reutilization Corp',
                    'email'=>'jnbahnsen@co.fairfield.oh.us',
                    'password'=>bcrypt('gen*sher!') ,
                    'badge_color' => 'blue',
                    'entity_id' => '10','entity_type'=>'landbank',
                    'active' => '1',
                    'verified' => '1',

                    ],
                [
                    'name'=>'Hamilton County Land Reutilization Corporation',
                    'email'=>'crecht@cincinnatiport.org',
                    'password'=>bcrypt('@@loop88') ,
                    'badge_color' => 'blue',
                    'entity_id' => '11','entity_type'=>'landbank',
                    'active' => '1',
                    'verified' => '1',

                    ],
                [
                    'name'=>'Jefferson County Land Reutilization Corporation',
                    'email'=>'rfenderrpc@jeffersoncountyoh.com',
                    'password'=>bcrypt('ck#gable') ,
                    'badge_color' => 'blue',
                    'entity_id' => '12','entity_type'=>'landbank',
                    'active' => '1',
                    'verified' => '1',

                    ],
                [
                    'name'=>'Lake County Land Reutilization Corp',
                    'email'=>'jmrogers@lakecountylandbank.org',
                    'password'=>bcrypt('&simco35') ,
                    'badge_color' => 'blue',
                    'entity_id' => '13','entity_type'=>'landbank',
                    'active' => '1',
                    'verified' => '1',

                    ],
                [
                    'name'=>'Lorain County Port Authority',
                    'email'=>'pmetzger@loraincounty.us',
                    'password'=>bcrypt('77erie@') ,
                    'badge_color' => 'blue',
                    'entity_id' => '14','entity_type'=>'landbank',
                    'active' => '1',
                    'verified' => '1',

                    ],
                [
                    'name'=>'Lucas County Land Reutilization Corporation',
                    'email'=>'dmann@co.lucas.oh.us',
                    'password'=>bcrypt('$sac4ft') ,
                    'badge_color' => 'blue',
                    'entity_id' => '15','entity_type'=>'landbank',
                    'active' => '1',
                    'verified' => '1',

                    ],
                [
                    'name'=>'Mahoning County Land Reutilization Corp',
                    'email'=>'dflora@mahoninglandbank.com',
                    'password'=>bcrypt('upown@2') ,
                    'badge_color' => 'blue',
                    'entity_id' => '16','entity_type'=>'landbank',
                    'active' => '1',
                    'verified' => '1',

                    ],
                [
                    'name'=>'Montgomery County Land Reutilization Corp',
                    'email'=>'mikeg@mclandbank.com',
                    'password'=>bcrypt('go#west6!') ,
                    'badge_color' => 'blue',
                    'entity_id' => '17','entity_type'=>'landbank',
                    'active' => '1',
                    'verified' => '1',

                    ],
                [
                    'name'=>'Portage County Land Reutilization Corporation',
                    'email'=>'morgantid@kent-ohio.org',
                    'password'=>bcrypt('nxt@qu!') ,
                    'badge_color' => 'blue',
                    'entity_id' => '18','entity_type'=>'landbank',
                    'active' => '1',
                    'verified' => '1',

                    ],
                [
                    'name'=>'Richland County Land Reutilization Corp',
                    'email'=>'ahamrick@richlandcountyoh.us',
                    'password'=>bcrypt('5ny@yank') ,
                    'badge_color' => 'blue',
                    'entity_id' => '19','entity_type'=>'landbank',
                    'active' => '1',
                    'verified' => '1',

                    ],
                [
                    'name'=>'Stark County Land Reutilization Corp',
                    'email'=>'aazumbar@starkcountyohio.gov',
                    'password'=>bcrypt('stk!7ohmy') ,
                    'badge_color' => 'blue',
                    'entity_id' => '20','entity_type'=>'landbank',
                    'active' => '1',
                    'verified' => '1',

                    ],
                [
                    'name'=>'Summit County Land Reutilization Corp',
                    'email'=>'pbravo@summitlandbank.org',
                    'password'=>bcrypt('red!grn@4') ,
                    'badge_color' => 'blue',
                    'entity_id' => '21','entity_type'=>'landbank',
                    'active' => '1',
                    'verified' => '1',

                    ],
                [
                    'name'=>'Trumbull County Land Reutilization Corp',
                    'email'=>'lisa@tnpwarren.org',
                    'password'=>bcrypt('yt*oh@@') ,
                    'badge_color' => 'blue',
                    'entity_id' => '22','entity_type'=>'landbank',
                    'active' => '1',
                    'verified' => '1',

                    ],
                [
                    'name'=>'Carlie Boos',
                    'email'=>'cboos@ohiohome.org',
                    'password'=>bcrypt('Allita12') ,
                    'badge_color' => 'blue',
                    'entity_id' => '1','entity_type'=>'hfa',
                    'active' => '1',
                    'verified' => '1',

                    ],
                [
                    'name'=>'Eric Tooney',
                    'email'=>'etooney@ohiohome.org',
                    'password'=>bcrypt('Allita12') ,
                    'badge_color' => 'blue',
                    'entity_id' => '1','entity_type'=>'hfa',
                    'active' => '1',
                    'verified' => '1',

                    ],
                [
                    'name'=>'Jilvonda Burston',
                    'email'=>'jburston@ohiohome.org',
                    'password'=>bcrypt('Allita12') ,
                    'badge_color' => 'orange',
                    'entity_id' => '1','entity_type'=>'hfa',
                    'active' => '1',
                    'verified' => '1',

                    ],
                [
                    'name'=>'William Steele',
                    'email'=>'wsteele@ohiohome.org',
                    'password'=>bcrypt('Allita12') ,
                    'badge_color' => 'blue',
                    'entity_id' => '1','entity_type'=>'hfa',
                    'active' => '1',
                    'verified' => '1',

                    ],
                [
                    'name'=>'Marc Gardner',
                    'email'=>'mgardnere@ohiohome.org',
                    'password'=>bcrypt('Allita12') ,
                    'badge_color' => 'blue',
                    'entity_id' => '1','entity_type'=>'hfa',
                    'active' => '1',
                    'verified' => '1',

                    ],
                [
                    'name'=>'Eric Corthell',
                    'email'=>'ecorthell@ohiohome.org',
                    'password'=>bcrypt('Allita12') ,
                    'badge_color' => 'green',
                    'entity_id' => '1','entity_type'=>'hfa',
                    'active' => '1',
                    'verified' => '1',

                    ],
                /// 30
                [
                    'name'=>'Allen County',
                    'email'=>'allencounty@allita.org',
                    'password'=>bcrypt('Allita12') ,
                    'badge_color' => 'green',
                    'entity_id' => '23','entity_type'=>'landbank',
                    'active' => '1',
                    'verified' => '1',

                    ],
                /// 31
                [
                    'name'=>'Clinton County',
                    'email'=>'clintoncounty@allita.org',
                    'password'=>bcrypt('Allita12') ,
                    'badge_color' => 'green',
                    'entity_id' => '24','entity_type'=>'landbank',
                    'active' => '1',
                    'verified' => '1',

                    ],
                /// 32
                [
                    'name'=>'Crawford County',
                    'email'=>'crawfordcounty@allita.org',
                    'password'=>bcrypt('Allita12') ,
                    'badge_color' => 'green',
                    'entity_id' => '25','entity_type'=>'landbank',
                    'active' => '1',
                    'verified' => '1',

                    ],
                /// 33
                [
                    'name'=>'Lawrence County',
                    'email'=>'lawrencecounty@allita.org',
                    'password'=>bcrypt('Allita12') ,
                    'badge_color' => 'green',
                    'entity_id' => '26','entity_type'=>'landbank',
                    'active' => '1',
                    'verified' => '1',

                    ],
                /// 34
                [
                    'name'=>'Licking County',
                    'email'=>'lickingcounty@allita.org',
                    'password'=>bcrypt('Allita12') ,
                    'badge_color' => 'green',
                    'entity_id' => '27','entity_type'=>'landbank',
                    'active' => '1',
                    'verified' => '1',

                    ],
                /// 35
                [
                    'name'=>'Marion County',
                    'email'=>'marioncounty@allita.org',
                    'password'=>bcrypt('Allita12') ,
                    'badge_color' => 'green',
                    'entity_id' => '28','entity_type'=>'landbank',
                    'active' => '1',
                    'verified' => '1',

                    ],
                /// 36
                [
                    'name'=>'Morrow County',
                    'email'=>'morrowcounty@allita.org',
                    'password'=>bcrypt('Allita12') ,
                    'badge_color' => 'green',
                    'entity_id' => '29','entity_type'=>'landbank',
                    'active' => '1',
                    'verified' => '1',

                    ],
                /// 37
                [
                    'name'=>'Ottawa County',
                    'email'=>'ottawacounty@allita.org',
                    'password'=>bcrypt('Allita12') ,
                    'badge_color' => 'green',
                    'entity_id' => '30','entity_type'=>'landbank',
                    'active' => '1',
                    'verified' => '1',

                    ],
                /// 38
                [
                    'name'=>'Perry County',
                    'email'=>'perrycounty@allita.org',
                    'password'=>bcrypt('Allita12') ,
                    'badge_color' => 'green',
                    'entity_id' => '31','entity_type'=>'landbank',
                    'active' => '1',
                    'verified' => '1',

                    ],
                /// 39
                [
                    'name'=>'Ross County',
                    'email'=>'rosscounty@allita.org',
                    'password'=>bcrypt('Allita12') ,
                    'badge_color' => 'green',
                    'entity_id' => '32','entity_type'=>'landbank',
                    'active' => '1',
                    'verified' => '1',

                    ],
                /// 40
                [
                    'name'=>'Sandusky County',
                    'email'=>'sanduskycounty@allita.org',
                    'password'=>bcrypt('Allita12') ,
                    'badge_color' => 'green',
                    'entity_id' => '33','entity_type'=>'landbank',
                    'active' => '1',
                    'verified' => '1',

                    ],
                /// 41
                [
                    'name'=>'Scioto County',
                    'email'=>'sciotocounty@allita.org',
                    'password'=>bcrypt('Allita12') ,
                    'badge_color' => 'green',
                    'entity_id' => '34','entity_type'=>'landbank',
                    'active' => '1',
                    'verified' => '1',

                    ],
                /// 42
                [
                    'name'=>'Seneca County',
                    'email'=>'senecacounty@allita.org',
                    'password'=>bcrypt('Allita12') ,
                    'badge_color' => 'green',
                    'entity_id' => '35','entity_type'=>'landbank',
                    'active' => '1',
                    'verified' => '1',

                    ],
                /// 43
                [
                    'name'=>'Shelby County',
                    'email'=>'county@allita.org',
                    'password'=>bcrypt('Allita12') ,
                    'badge_color' => 'green',
                    'entity_id' => '36','entity_type'=>'landbank',
                    'active' => '1',
                    'verified' => '1',

                    ],
                /// 44
                [
                    'name'=>'Van Wert County',
                    'email'=>'vanwertcounty@allita.org',
                    'password'=>bcrypt('Allita12') ,
                    'badge_color' => 'green',
                    'entity_id' => '37','entity_type'=>'landbank',
                    'active' => '1',
                    'verified' => '1',

                    ],
                /// 45
                [
                    'name'=>'Williams County',
                    'email'=>'williamscounty@allita.org',
                    'password'=>bcrypt('Allita12') ,
                    'badge_color' => 'green',
                    'entity_id' => '38','entity_type'=>'landbank',
                    'active' => '1',
                    'verified' => '1',

                    ],
                /// 46
                [
                    'name'=>'Historic Agency',
                    'email'=>'historic@allita.org',
                    'password'=>bcrypt('Allita12') ,
                    'badge_color' => 'orange',
                    'entity_id' => '39','entity_type'=>'historic agency',
                    'active' => '1',
                    'verified' => '1',

                    ]
               ];
        DB::table('users')->insert($countyUsers);

        DB::table('users')->insert([
            'name' => 'Liza Gaines',
            'email' => 'lgaines@ohiohome.org',
            'password' => bcrypt('Allita12'),
            'badge_color' => 'green',
            'entity_id' => '1',
            'entity_type'=>'hfa',
            'active' => '1',
            'verified' => '1',
        ]);
    }
}
