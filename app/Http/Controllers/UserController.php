<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Address;
use Auth;
use Session;
use App\LogConverter;
use Carbon;
use App\Events\AuditorAddressEvent;

class UserController extends Controller
{
    public function __construct()
    {
        // $this->middleware('auth');
        if (env('APP_DEBUG_NO_DEVCO') == 'true') {
            Auth::onceUsingId(286); // TEST BRIAN
        }
    }

    public function saveAuditorAddress(Request $request, $user){
        $forminputs = $request->get('inputs');
        parse_str($forminputs, $forminputs);

        $address = new Address([
                'line_1' => $forminputs['address1'],
                'line_2' => $forminputs['address2'],
                'city' => $forminputs['city'],
                'state' => $forminputs['state'],
                'zip' => $forminputs['zip'],
                'user_id' => $user
            ]);
        $address->save();

        $formatted_address = $forminputs['address1'];
        if($forminputs['address2']){ $formatted_address = $formatted_address.", ".$forminputs['address2']; }
        if($forminputs['city']){ $formatted_address = $formatted_address.", ".$forminputs['city']; }
        if($forminputs['state']){ $formatted_address = $formatted_address.", ".$forminputs['state']; }
        if($forminputs['zip']){ $formatted_address = $formatted_address." ".$forminputs['zip']; }

        broadcast(new AuditorAddressEvent(Auth::user(), $address->id, $formatted_address));

        return 1;
    }

    public function deleteAuditorAddress(Request $request, $address_id){
        // check if current user can delete this record and if the record exists.
        
        $address = Address::where('id', '=', $address_id)->first();

        if($address && $address->user_id == Auth::user()->id){
            $address->delete();
            return 1;
        }else{
            return 0;
        }
    }

    public function preferences($id)
    {

        if ($id != Auth::user()->id) {
            $output['message'] = 'You can only edit your own preferences.';
            return $output;
        }

        $user = Auth::user();

        $phone_number = '';
        if($user->person){
            if($user->person->phone){
                if($user->person->phone->number()){
                    $phone_number = $user->person->phone->number();
                }
            }
        }

        //dd($user->organization_details->address->line_1);


        $org_name = $user->organization;
        $org_address1 = '';
        $org_address2 = '';
        $org_city = '';
        $org_state = '';
        $org_zip = '';
        if($user->organization_details){
            if($user->organization_details->address){
                $org_address1 = $user->organization_details->address->line_1;
                $org_address2 = $user->organization_details->address->line_2;
                $org_city = $user->organization_details->address->city;
                $org_state = $user->organization_details->address->state;
                $org_zip = $user->organization_details->address->zip;
            }
        }

        $addresses = [];
        foreach($user->auditor_addresses as $address){

            $formatted_address = $address->line_1;
            if($address->line_2){
                $formatted_address = $formatted_address.", ".$address->line_2;
            }
            if($address->city){
                $formatted_address = $formatted_address.", ".$address->city;
            }
            if($address->state){
                $formatted_address = $formatted_address.", ".$address->state;
            }
            if($address->zip){
                $formatted_address = $formatted_address." ".$address->zip;
            }

            $addresses[] = [
                'address_id' => $address->id,
                'address' => $formatted_address
            ];
        }

        $data = collect([
            "summary" => [
                "id" => $id,
                "name" => $user->name,
                'initials' => $user->initials(),
                'active' => $user->active,
                'email' => $user->email,
                'phone' => $user->initials(),
                'color' => $user->badge_color,
                'phone' => $phone_number,
                'organization' => [
                    "name" => $org_name,
                    "address1" => $org_address1,
                    "address2" => $org_address2,
                    "city" => $org_city,
                    "state" => $org_state,
                    "zip" => $org_zip,
                ],
                'availability_max_hours' => $user->availability_max_hours,
                'availability_lunch' => $user->availability_lunch,
                'availability_max_driving' => $user->availability_max_driving,
                'addresses' => $addresses,
                'date' => 'DECEMBER 21, 2018',
                'ref' => '20181221',
                'date-previous' => 'DECEMBER 14, 2018',
                'ref-previous' => '20181214',
                'date-next' => 'DECEMBER 28, 2018',
                'ref-next' => '20181228'
            ],
            "calendar" => [
                "header" => ["12/18", "12/19", "12/20", "12/21", "12/22", "12/23", "12/24"],
                "content" => [
                    [
                        "id" => 111,
                        "date" => "12/18",
                        "no_availability" => 0,
                        "start_time" => "08:00 AM",
                        "end_time" => "05:30 PM",
                        "before_time_start" => "1",
                        "before_time_span" => "8",
                        "after_time_start" => "46",
                        "after_time_span" => "15",
                        "events" => [
                            [
                                "id" => 112,
                                "status" => "action-required",
                                "start" => "9",
                                "span" =>  "24",
                                "icon" => "a-mobile-not",
                                "lead" => 2,
                                "class" => "",
                                "modal_type" => ""
                            ],
                            [
                                "id" => 113,
                                "status" => "breaktime",
                                "start" => "33",
                                "span" =>  "2",
                                "icon" => "",
                                "lead" => 1,
                                "class" => "",
                                "modal_type" => ""
                            ],
                            [
                                "id" => 114,
                                "status" => "",
                                "start" => "35",
                                "span" =>  "11",
                                "icon" => "a-mobile-checked",
                                "lead" => 1,
                                "class" => "no-border-bottom",
                                "modal_type" => ""
                            ]
                        ]
                    ],
                    [
                        "id" => 112,
                        "date" => "12/19",
                        "no_availability" => 0,
                        "start_time" => "08:00 AM",
                        "end_time" => "05:30 PM",
                        "before_time_start" => "1",
                        "before_time_span" => "8",
                        "after_time_start" => "46",
                        "after_time_span" => "15",
                        "events" => [
                            [
                                "id" => 112,
                                "status" => "",
                                "start" => "9",
                                "span" =>  "12",
                                "icon" => "a-mobile-not",
                                "lead" => 2,
                                "class" => "",
                                "modal_type" => ""
                            ],
                            [
                                "id" => 113,
                                "status" => "breaktime",
                                "start" => "21",
                                "span" =>  "1",
                                "icon" => "",
                                "lead" => 1,
                                "class" => "",
                                "modal_type" => ""
                            ],
                            [
                                "id" => 114,
                                "status" => "",
                                "start" => "22",
                                "span" =>  "24",
                                "icon" => "a-circle-plus",
                                "lead" => 1,
                                "class" => "available no-border-top no-border-bottom",
                                "modal_type" => "choose-filing"
                            ]
                        ]
                    ],
                    [
                        "id" => 113,
                        "date" => "12/20",
                        "no_availability" => 0,
                        "start_time" => "08:00 AM",
                        "end_time" => "05:30 PM",
                        "before_time_start" => "1",
                        "before_time_span" => "8",
                        "after_time_start" => "46",
                        "after_time_span" => "15",
                        "events" => [
                            [
                                "id" => 112,
                                "status" => "action-required",
                                "start" => "9",
                                "span" =>  "12",
                                "icon" => "a-mobile-not",
                                "lead" => 1,
                                "class" => "",
                                "modal_type" => ""
                            ],
                            [
                                "id" => 113,
                                "status" => "breaktime",
                                "start" => "21",
                                "span" =>  "4",
                                "icon" => "",
                                "lead" => 1,
                                "class" => "",
                                "modal_type" => ""
                            ],
                            [
                                "id" => 114,
                                "status" => "",
                                "start" => "25",
                                "span" =>  "21",
                                "icon" => "a-circle-plus",
                                "lead" => 1,
                                "class" => "available no-border-top no-border-bottom",
                                "modal_type" => "choose-filing"
                            ]
                        ]
                    ],
                    [
                        "id" => 115,
                        "date" => "12/21",
                        "no_availability" => 0,
                        "start_time" => "08:00 AM",
                        "end_time" => "05:30 PM",
                        "before_time_start" => "1",
                        "before_time_span" => "8",
                        "after_time_start" => "46",
                        "after_time_span" => "15",
                        "events" => [
                            [
                                "id" => 112,
                                "status" => "",
                                "start" => "9",
                                "span" =>  "16",
                                "icon" => "a-circle-plus",
                                "lead" => 1,
                                "class" => "available no-border-top",
                                "modal_type" => "choose-filing"
                            ],
                            [
                                "id" => 113,
                                "status" => "",
                                "start" => "30",
                                "span" =>  "16",
                                "icon" => "a-circle-plus",
                                "lead" => 1,
                                "class" => "available no-border-bottom",
                                "modal_type" => "choose-filing"
                            ]
                        ]
                    ],
                    [
                        "id" => 116,
                        "date" => "12/22",
                        "no_availability" => 0,
                        "start_time" => "08:00 AM",
                        "end_time" => "05:30 PM",
                        "before_time_start" => "1",
                        "before_time_span" => "8",
                        "after_time_start" => "46",
                        "after_time_span" => "15",
                        "events" => [
                            [
                                "id" => 112,
                                "status" => "in-progress",
                                "start" => "9",
                                "span" =>  "16",
                                "icon" => "a-mobile-checked",
                                "lead" => 1,
                                "class" => "",
                                "modal_type" => "change-date"
                            ],
                            [
                                "id" => 113,
                                "status" => "breaktime",
                                "start" => "25",
                                "span" =>  "1",
                                "icon" => "",
                                "lead" => 1,
                                "class" => "",
                                "modal_type" => ""
                            ],
                            [
                                "id" => 113,
                                "status" => "",
                                "start" => "26",
                                "span" =>  "12",
                                "icon" => "a-folder",
                                "lead" => 2,
                                "class" => "",
                                "modal_type" => ""
                            ],
                            [
                                "id" => 113,
                                "status" => "",
                                "start" => "38",
                                "span" =>  "8",
                                "icon" => "a-folder",
                                "lead" => 1,
                                "class" => "no-border-bottom",
                                "modal_type" => ""
                            ]
                        ]
                    ],
                    [
                        "id" => 114,
                        "date" => "12/23",
                        "no_availability" => 1
                    ],
                    [
                        "id" => 115,
                        "date" => "12/24",
                        "no_availability" => 1
                    ]
                ],
                "footer" => [
                    "previous" => "DECEMBER 14, 2018",
                    'ref-previous' => '20181214',
                    "today" => "DECEMBER 21, 2018",
                    "next" => "DECEMBER 28, 2018",
                    'ref-next' => '20181228'
                ]
            ],
            "calendar-previous" => [
                "header" => ["12/11", "12/12", "12/13", "12/14", "12/15", "12/16", "12/17"],
                "content" => [
                    [
                        "id" => 113,
                        "date" => "12/11",
                        "no_availability" => 0,
                        "start_time" => "08:00 AM",
                        "end_time" => "05:30 PM",
                        "before_time_start" => "1",
                        "before_time_span" => "8",
                        "after_time_start" => "46",
                        "after_time_span" => "15",
                        "events" => [
                            [
                                "id" => 112,
                                "status" => "action-required",
                                "start" => "9",
                                "span" =>  "12",
                                "icon" => "a-mobile-not",
                                "lead" => 1,
                                "class" => "",
                                "modal_type" => ""
                            ],
                            [
                                "id" => 113,
                                "status" => "breaktime",
                                "start" => "21",
                                "span" =>  "4",
                                "icon" => "",
                                "lead" => 1,
                                "class" => "",
                                "modal_type" => ""
                            ],
                            [
                                "id" => 114,
                                "status" => "",
                                "start" => "25",
                                "span" =>  "21",
                                "icon" => "a-circle-plus",
                                "lead" => 1,
                                "class" => "available no-border-top no-border-bottom",
                                "modal_type" => "choose-filing"
                            ]
                        ]
                    ],
                    [
                        "id" => 115,
                        "date" => "12/12",
                        "no_availability" => 0,
                        "start_time" => "08:00 AM",
                        "end_time" => "05:30 PM",
                        "before_time_start" => "1",
                        "before_time_span" => "8",
                        "after_time_start" => "46",
                        "after_time_span" => "15",
                        "events" => [
                            [
                                "id" => 112,
                                "status" => "",
                                "start" => "9",
                                "span" =>  "16",
                                "icon" => "a-circle-plus",
                                "lead" => 1,
                                "class" => "available no-border-top",
                                "modal_type" => "choose-filing"
                            ],
                            [
                                "id" => 113,
                                "status" => "",
                                "start" => "30",
                                "span" =>  "16",
                                "icon" => "a-circle-plus",
                                "lead" => 1,
                                "class" => "available no-border-bottom",
                                "modal_type" => "choose-filing"
                            ]
                        ]
                    ],
                    [
                        "id" => 116,
                        "date" => "12/13",
                        "no_availability" => 0,
                        "start_time" => "08:00 AM",
                        "end_time" => "05:30 PM",
                        "before_time_start" => "1",
                        "before_time_span" => "8",
                        "after_time_start" => "46",
                        "after_time_span" => "15",
                        "events" => [
                            [
                                "id" => 112,
                                "status" => "in-progress",
                                "start" => "9",
                                "span" =>  "16",
                                "icon" => "a-mobile-checked",
                                "lead" => 1,
                                "class" => "",
                                "modal_type" => "change-date"
                            ],
                            [
                                "id" => 113,
                                "status" => "breaktime",
                                "start" => "25",
                                "span" =>  "1",
                                "icon" => "",
                                "lead" => 1,
                                "class" => "",
                                "modal_type" => ""
                            ],
                            [
                                "id" => 113,
                                "status" => "",
                                "start" => "26",
                                "span" =>  "12",
                                "icon" => "a-folder",
                                "lead" => 2,
                                "class" => "",
                                "modal_type" => ""
                            ],
                            [
                                "id" => 113,
                                "status" => "",
                                "start" => "38",
                                "span" =>  "8",
                                "icon" => "a-folder",
                                "lead" => 1,
                                "class" => "no-border-bottom",
                                "modal_type" => ""
                            ]
                        ]
                    ],
                    [
                        "id" => 116,
                        "date" => "12/14",
                        "no_availability" => 0,
                        "start_time" => "08:00 AM",
                        "end_time" => "05:30 PM",
                        "before_time_start" => "1",
                        "before_time_span" => "8",
                        "after_time_start" => "46",
                        "after_time_span" => "15",
                        "events" => [
                            [
                                "id" => 112,
                                "status" => "in-progress",
                                "start" => "9",
                                "span" =>  "16",
                                "icon" => "a-mobile-checked",
                                "lead" => 1,
                                "class" => "",
                                "modal_type" => "change-date"
                            ],
                            [
                                "id" => 113,
                                "status" => "breaktime",
                                "start" => "25",
                                "span" =>  "1",
                                "icon" => "",
                                "lead" => 1,
                                "class" => "",
                                "modal_type" => ""
                            ],
                            [
                                "id" => 113,
                                "status" => "",
                                "start" => "26",
                                "span" =>  "12",
                                "icon" => "a-folder",
                                "lead" => 2,
                                "class" => "",
                                "modal_type" => ""
                            ],
                            [
                                "id" => 113,
                                "status" => "",
                                "start" => "38",
                                "span" =>  "8",
                                "icon" => "a-folder",
                                "lead" => 1,
                                "class" => "no-border-bottom",
                                "modal_type" => ""
                            ]
                        ]
                    ],
                    [
                        "id" => 116,
                        "date" => "12/15",
                        "no_availability" => 0,
                        "start_time" => "08:00 AM",
                        "end_time" => "05:30 PM",
                        "before_time_start" => "1",
                        "before_time_span" => "8",
                        "after_time_start" => "46",
                        "after_time_span" => "15",
                        "events" => [
                            [
                                "id" => 112,
                                "status" => "in-progress",
                                "start" => "9",
                                "span" =>  "16",
                                "icon" => "a-mobile-checked",
                                "lead" => 1,
                                "class" => "",
                                "modal_type" => "change-date"
                            ],
                            [
                                "id" => 113,
                                "status" => "breaktime",
                                "start" => "25",
                                "span" =>  "1",
                                "icon" => "",
                                "lead" => 1,
                                "class" => "",
                                "modal_type" => ""
                            ],
                            [
                                "id" => 113,
                                "status" => "",
                                "start" => "26",
                                "span" =>  "12",
                                "icon" => "a-folder",
                                "lead" => 2,
                                "class" => "",
                                "modal_type" => ""
                            ],
                            [
                                "id" => 113,
                                "status" => "",
                                "start" => "38",
                                "span" =>  "8",
                                "icon" => "a-folder",
                                "lead" => 1,
                                "class" => "no-border-bottom",
                                "modal_type" => ""
                            ]
                        ]
                    ],
                    [
                        "id" => 116,
                        "date" => "12/16",
                        "no_availability" => 0,
                        "start_time" => "08:00 AM",
                        "end_time" => "05:30 PM",
                        "before_time_start" => "1",
                        "before_time_span" => "8",
                        "after_time_start" => "46",
                        "after_time_span" => "15",
                        "events" => [
                            [
                                "id" => 112,
                                "status" => "in-progress",
                                "start" => "9",
                                "span" =>  "16",
                                "icon" => "a-mobile-checked",
                                "lead" => 1,
                                "class" => "",
                                "modal_type" => "change-date"
                            ],
                            [
                                "id" => 113,
                                "status" => "breaktime",
                                "start" => "25",
                                "span" =>  "1",
                                "icon" => "",
                                "lead" => 1,
                                "class" => "",
                                "modal_type" => ""
                            ],
                            [
                                "id" => 113,
                                "status" => "",
                                "start" => "26",
                                "span" =>  "12",
                                "icon" => "a-folder",
                                "lead" => 2,
                                "class" => "",
                                "modal_type" => ""
                            ],
                            [
                                "id" => 113,
                                "status" => "",
                                "start" => "38",
                                "span" =>  "8",
                                "icon" => "a-folder",
                                "lead" => 1,
                                "class" => "no-border-bottom",
                                "modal_type" => ""
                            ]
                        ]
                    ],
                    [
                        "id" => 116,
                        "date" => "12/17",
                        "no_availability" => 0,
                        "start_time" => "08:00 AM",
                        "end_time" => "05:30 PM",
                        "before_time_start" => "1",
                        "before_time_span" => "8",
                        "after_time_start" => "46",
                        "after_time_span" => "15",
                        "events" => [
                            [
                                "id" => 112,
                                "status" => "in-progress",
                                "start" => "9",
                                "span" =>  "16",
                                "icon" => "a-mobile-checked",
                                "lead" => 1,
                                "class" => "",
                                "modal_type" => "change-date"
                            ],
                            [
                                "id" => 113,
                                "status" => "breaktime",
                                "start" => "25",
                                "span" =>  "1",
                                "icon" => "",
                                "lead" => 1,
                                "class" => "",
                                "modal_type" => ""
                            ],
                            [
                                "id" => 113,
                                "status" => "",
                                "start" => "26",
                                "span" =>  "12",
                                "icon" => "a-folder",
                                "lead" => 2,
                                "class" => "",
                                "modal_type" => ""
                            ],
                            [
                                "id" => 113,
                                "status" => "",
                                "start" => "38",
                                "span" =>  "8",
                                "icon" => "a-folder",
                                "lead" => 1,
                                "class" => "no-border-bottom",
                                "modal_type" => ""
                            ]
                        ]
                    ]
                ],
                "footer" => [
                    "previous" => "DECEMBER 07, 2018",
                    'ref-previous' => '20181207',
                    "today" => "DECEMBER 14, 2018",
                    "next" => "DECEMBER 21, 2018",
                    'ref-next' => '20181221'
                ]
            ],
            "calendar-next" => [
                "header" => ["12/25", "12/26", "12/27", "12/28", "12/29", "12/30", "12/31"],
                "content" => [
                    [
                        "id" => 113,
                        "date" => "12/11",
                        "no_availability" => 0,
                        "start_time" => "08:00 AM",
                        "end_time" => "05:30 PM",
                        "before_time_start" => "1",
                        "before_time_span" => "8",
                        "after_time_start" => "46",
                        "after_time_span" => "15",
                        "events" => [
                            [
                                "id" => 112,
                                "status" => "action-required",
                                "start" => "9",
                                "span" =>  "12",
                                "icon" => "a-mobile-not",
                                "lead" => 1,
                                "class" => "",
                                "modal_type" => ""
                            ],
                            [
                                "id" => 113,
                                "status" => "breaktime",
                                "start" => "21",
                                "span" =>  "4",
                                "icon" => "",
                                "lead" => 1,
                                "class" => "",
                                "modal_type" => ""
                            ],
                            [
                                "id" => 114,
                                "status" => "",
                                "start" => "25",
                                "span" =>  "21",
                                "icon" => "a-circle-plus",
                                "lead" => 1,
                                "class" => "available no-border-top no-border-bottom",
                                "modal_type" => "choose-filing"
                            ]
                        ]
                    ],
                    [
                        "id" => 115,
                        "date" => "12/12",
                        "no_availability" => 0,
                        "start_time" => "08:00 AM",
                        "end_time" => "05:30 PM",
                        "before_time_start" => "1",
                        "before_time_span" => "8",
                        "after_time_start" => "46",
                        "after_time_span" => "15",
                        "events" => [
                            [
                                "id" => 112,
                                "status" => "",
                                "start" => "9",
                                "span" =>  "16",
                                "icon" => "a-circle-plus",
                                "lead" => 1,
                                "class" => "available no-border-top",
                                "modal_type" => "choose-filing"
                            ],
                            [
                                "id" => 113,
                                "status" => "",
                                "start" => "30",
                                "span" =>  "16",
                                "icon" => "a-circle-plus",
                                "lead" => 1,
                                "class" => "available no-border-bottom",
                                "modal_type" => "choose-filing"
                            ]
                        ]
                    ],
                    [
                        "id" => 116,
                        "date" => "12/13",
                        "no_availability" => 0,
                        "start_time" => "08:00 AM",
                        "end_time" => "05:30 PM",
                        "before_time_start" => "1",
                        "before_time_span" => "8",
                        "after_time_start" => "46",
                        "after_time_span" => "15",
                        "events" => [
                            [
                                "id" => 112,
                                "status" => "in-progress",
                                "start" => "9",
                                "span" =>  "16",
                                "icon" => "a-mobile-checked",
                                "lead" => 1,
                                "class" => "",
                                "modal_type" => "change-date"
                            ],
                            [
                                "id" => 113,
                                "status" => "breaktime",
                                "start" => "25",
                                "span" =>  "1",
                                "icon" => "",
                                "lead" => 1,
                                "class" => "",
                                "modal_type" => ""
                            ],
                            [
                                "id" => 113,
                                "status" => "",
                                "start" => "26",
                                "span" =>  "12",
                                "icon" => "a-folder",
                                "lead" => 2,
                                "class" => "",
                                "modal_type" => ""
                            ],
                            [
                                "id" => 113,
                                "status" => "",
                                "start" => "38",
                                "span" =>  "8",
                                "icon" => "a-folder",
                                "lead" => 1,
                                "class" => "no-border-bottom",
                                "modal_type" => ""
                            ]
                        ]
                    ],
                    [
                        "id" => 116,
                        "date" => "12/14",
                        "no_availability" => 0,
                        "start_time" => "08:00 AM",
                        "end_time" => "05:30 PM",
                        "before_time_start" => "1",
                        "before_time_span" => "8",
                        "after_time_start" => "46",
                        "after_time_span" => "15",
                        "events" => [
                            [
                                "id" => 112,
                                "status" => "in-progress",
                                "start" => "9",
                                "span" =>  "16",
                                "icon" => "a-mobile-checked",
                                "lead" => 1,
                                "class" => "",
                                "modal_type" => "change-date"
                            ],
                            [
                                "id" => 113,
                                "status" => "breaktime",
                                "start" => "25",
                                "span" =>  "1",
                                "icon" => "",
                                "lead" => 1,
                                "class" => "",
                                "modal_type" => ""
                            ],
                            [
                                "id" => 113,
                                "status" => "",
                                "start" => "26",
                                "span" =>  "12",
                                "icon" => "a-folder",
                                "lead" => 2,
                                "class" => "",
                                "modal_type" => ""
                            ],
                            [
                                "id" => 113,
                                "status" => "",
                                "start" => "38",
                                "span" =>  "8",
                                "icon" => "a-folder",
                                "lead" => 1,
                                "class" => "no-border-bottom",
                                "modal_type" => ""
                            ]
                        ]
                    ],
                    [
                        "id" => 116,
                        "date" => "12/15/18",
                        "no_availability" => 0,
                        "start_time" => "08:00 AM",
                        "end_time" => "05:30 PM",
                        "before_time_start" => "1",
                        "before_time_span" => "8",
                        "after_time_start" => "46",
                        "after_time_span" => "15",
                        "events" => [
                            [
                                "id" => 112,
                                "status" => "in-progress",
                                "start" => "9",
                                "span" =>  "16",
                                "icon" => "a-mobile-checked",
                                "lead" => 1,
                                "class" => "",
                                "modal_type" => "change-date"
                            ],
                            [
                                "id" => 113,
                                "status" => "breaktime",
                                "start" => "25",
                                "span" =>  "1",
                                "icon" => "",
                                "lead" => 1,
                                "class" => "",
                                "modal_type" => ""
                            ],
                            [
                                "id" => 113,
                                "status" => "",
                                "start" => "26",
                                "span" =>  "12",
                                "icon" => "a-folder",
                                "lead" => 2,
                                "class" => "",
                                "modal_type" => ""
                            ],
                            [
                                "id" => 113,
                                "status" => "",
                                "start" => "38",
                                "span" =>  "8",
                                "icon" => "a-folder",
                                "lead" => 1,
                                "class" => "no-border-bottom",
                                "modal_type" => ""
                            ]
                        ]
                    ],
                    [
                        "id" => 116,
                        "date" => "12/16",
                        "no_availability" => 0,
                        "start_time" => "08:00 AM",
                        "end_time" => "05:30 PM",
                        "before_time_start" => "1",
                        "before_time_span" => "8",
                        "after_time_start" => "46",
                        "after_time_span" => "15",
                        "events" => [
                            [
                                "id" => 112,
                                "status" => "in-progress",
                                "start" => "9",
                                "span" =>  "16",
                                "icon" => "a-mobile-checked",
                                "lead" => 1,
                                "class" => "",
                                "modal_type" => "change-date"
                            ],
                            [
                                "id" => 113,
                                "status" => "breaktime",
                                "start" => "25",
                                "span" =>  "1",
                                "icon" => "",
                                "lead" => 1,
                                "class" => "",
                                "modal_type" => ""
                            ],
                            [
                                "id" => 113,
                                "status" => "",
                                "start" => "26",
                                "span" =>  "12",
                                "icon" => "a-folder",
                                "lead" => 2,
                                "class" => "",
                                "modal_type" => ""
                            ],
                            [
                                "id" => 113,
                                "status" => "",
                                "start" => "38",
                                "span" =>  "8",
                                "icon" => "a-folder",
                                "lead" => 1,
                                "class" => "no-border-bottom",
                                "modal_type" => ""
                            ]
                        ]
                    ],
                    [
                        "id" => 116,
                        "date" => "12/17",
                        "no_availability" => 0,
                        "start_time" => "08:00 AM",
                        "end_time" => "05:30 PM",
                        "before_time_start" => "1",
                        "before_time_span" => "8",
                        "after_time_start" => "46",
                        "after_time_span" => "15",
                        "events" => [
                            [
                                "id" => 112,
                                "status" => "in-progress",
                                "start" => "9",
                                "span" =>  "16",
                                "icon" => "a-mobile-checked",
                                "lead" => 1,
                                "class" => "",
                                "modal_type" => "change-date"
                            ],
                            [
                                "id" => 113,
                                "status" => "breaktime",
                                "start" => "25",
                                "span" =>  "1",
                                "icon" => "",
                                "lead" => 1,
                                "class" => "",
                                "modal_type" => ""
                            ],
                            [
                                "id" => 113,
                                "status" => "",
                                "start" => "26",
                                "span" =>  "12",
                                "icon" => "a-folder",
                                "lead" => 2,
                                "class" => "",
                                "modal_type" => ""
                            ],
                            [
                                "id" => 113,
                                "status" => "",
                                "start" => "38",
                                "span" =>  "8",
                                "icon" => "a-folder",
                                "lead" => 1,
                                "class" => "no-border-bottom",
                                "modal_type" => ""
                            ]
                        ]
                    ]
                ],
                "footer" => [
                    "previous" => "DECEMBER 21, 2018",
                    'ref-previous' => '20181221',
                    "today" => "DECEMBER 28, 2018",
                    "next" => "JANUARY 04, 2019",
                    'ref-next' => '20190104',
                ]
            ]
        ]);


        return view('modals.user-preferences', compact('data'));
    }

    public function getUserAvailabilityCalendar($userid, $currentdate, $beforeafter)
    {

        if ($userid != Auth::user()->id) {
            $output['message'] = 'You can only edit your own preferences.';
            return $output;
        }

        // from the current date and beforeafter, calculate new target date
        $created = Carbon\Carbon::createFromFormat('Ymd', $currentdate);
        if ($beforeafter == "before") {
            $newdate = $created->subDays(9);

            $newdate_previous = Carbon\Carbon::createFromFormat('Ymd', $currentdate)->subDays(18)->format('F d, Y');
            $newdate_ref_previous = Carbon\Carbon::createFromFormat('Ymd', $currentdate)->subDays(18)->format('Ymd');
            $newdate_next = Carbon\Carbon::createFromFormat('Ymd', $currentdate)->format('F d, Y');
            $newdate_ref_next = Carbon\Carbon::createFromFormat('Ymd', $currentdate)->format('Ymd');

            $newdateref = $newdate->format('Ymd');
            $newdateformatted = $newdate->format('F d, Y');

            $header_dates = [];
            $header_dates[] = $newdate->subDays(4)->format('m/d');
            $header_dates[] = $newdate->addDays(1)->format('m/d');
            $header_dates[] = $newdate->addDays(1)->format('m/d');
            $header_dates[] = $newdate->addDays(1)->format('m/d');
            $header_dates[] = $newdate->addDays(1)->format('m/d');
            $header_dates[] = $newdate->addDays(1)->format('m/d');
            $header_dates[] = $newdate->addDays(1)->format('m/d');
            $header_dates[] = $newdate->addDays(1)->format('m/d');
            $header_dates[] = $newdate->addDays(1)->format('m/d');
        } else {
            $newdate = $created->addDays(9);

            $newdate_previous = Carbon\Carbon::createFromFormat('Ymd', $currentdate)->format('F d, Y');
            $newdate_ref_previous = Carbon\Carbon::createFromFormat('Ymd', $currentdate)->format('Ymd');
            $newdate_next = Carbon\Carbon::createFromFormat('Ymd', $currentdate)->addDays(18)->format('F d, Y');
            $newdate_ref_next = Carbon\Carbon::createFromFormat('Ymd', $currentdate)->addDays(18)->format('Ymd');

            $newdateref = $newdate->format('Ymd');
            $newdateformatted = $newdate->format('F d, Y');

            $header_dates = [];
            $header_dates[] = $newdate->subDays(4)->format('m/d');
            $header_dates[] = $newdate->addDays(1)->format('m/d');
            $header_dates[] = $newdate->addDays(1)->format('m/d');
            $header_dates[] = $newdate->addDays(1)->format('m/d');
            $header_dates[] = $newdate->addDays(1)->format('m/d');
            $header_dates[] = $newdate->addDays(1)->format('m/d');
            $header_dates[] = $newdate->addDays(1)->format('m/d');
            $header_dates[] = $newdate->addDays(1)->format('m/d');
            $header_dates[] = $newdate->addDays(1)->format('m/d');
        }
        // dd($header_dates);
        // dd($currentdate." - ".$created." - ".$newdate." - ".$newdateformatted." - ".$newdateref);
        $data = collect([
            "summary" => [
                "id" => $userid,
                "name" => "Jane Doe",
                'initials' => 'JD',
                'color' => 'blue',
                'date' => $newdateformatted,
                'ref' => $newdateref
            ],
            "calendar" => [
                "header" => $header_dates,
                "content" => [
                    [
                        "id" => 111,
                        "date" => "12/18",
                        "no_availability" => 0,
                        "start_time" => "08:00 AM",
                        "end_time" => "05:30 PM",
                        "before_time_start" => "1",
                        "before_time_span" => "8",
                        "after_time_start" => "46",
                        "after_time_span" => "15",
                        "events" => [
                            [
                                "id" => 112,
                                "status" => "action-required",
                                "start" => "9",
                                "span" =>  "24",
                                "icon" => "a-mobile-not",
                                "lead" => 2,
                                "class" => "",
                                "modal_type" => ""
                            ],
                            [
                                "id" => 113,
                                "status" => "breaktime",
                                "start" => "33",
                                "span" =>  "2",
                                "icon" => "",
                                "lead" => 1,
                                "class" => "",
                                "modal_type" => ""
                            ],
                            [
                                "id" => 114,
                                "status" => "",
                                "start" => "35",
                                "span" =>  "11",
                                "icon" => "a-mobile-checked",
                                "lead" => 1,
                                "class" => "no-border-bottom",
                                "modal_type" => ""
                            ]
                        ]
                    ],
                    [
                        "id" => 112,
                        "date" => "12/19",
                        "no_availability" => 0,
                        "start_time" => "08:00 AM",
                        "end_time" => "05:30 PM",
                        "before_time_start" => "1",
                        "before_time_span" => "8",
                        "after_time_start" => "46",
                        "after_time_span" => "15",
                        "events" => [
                            [
                                "id" => 112,
                                "status" => "",
                                "start" => "9",
                                "span" =>  "12",
                                "icon" => "a-mobile-not",
                                "lead" => 2,
                                "class" => "",
                                "modal_type" => ""
                            ],
                            [
                                "id" => 113,
                                "status" => "breaktime",
                                "start" => "21",
                                "span" =>  "1",
                                "icon" => "",
                                "lead" => 1,
                                "class" => "",
                                "modal_type" => ""
                            ],
                            [
                                "id" => 114,
                                "status" => "",
                                "start" => "22",
                                "span" =>  "24",
                                "icon" => "a-circle-plus",
                                "lead" => 1,
                                "class" => "available no-border-top no-border-bottom",
                                "modal_type" => "choose-filing"
                            ]
                        ]
                    ],
                    [
                        "id" => 113,
                        "date" => "12/20",
                        "no_availability" => 0,
                        "start_time" => "08:00 AM",
                        "end_time" => "05:30 PM",
                        "before_time_start" => "1",
                        "before_time_span" => "8",
                        "after_time_start" => "46",
                        "after_time_span" => "15",
                        "events" => [
                            [
                                "id" => 112,
                                "status" => "action-required",
                                "start" => "9",
                                "span" =>  "12",
                                "icon" => "a-mobile-not",
                                "lead" => 1,
                                "class" => "",
                                "modal_type" => ""
                            ],
                            [
                                "id" => 113,
                                "status" => "breaktime",
                                "start" => "21",
                                "span" =>  "4",
                                "icon" => "",
                                "lead" => 1,
                                "class" => "",
                                "modal_type" => ""
                            ],
                            [
                                "id" => 114,
                                "status" => "",
                                "start" => "25",
                                "span" =>  "21",
                                "icon" => "a-circle-plus",
                                "lead" => 1,
                                "class" => "available no-border-top no-border-bottom",
                                "modal_type" => "choose-filing"
                            ]
                        ]
                    ],
                    [
                        "id" => 115,
                        "date" => "12/21",
                        "no_availability" => 0,
                        "start_time" => "08:00 AM",
                        "end_time" => "05:30 PM",
                        "before_time_start" => "1",
                        "before_time_span" => "8",
                        "after_time_start" => "46",
                        "after_time_span" => "15",
                        "events" => [
                            [
                                "id" => 112,
                                "status" => "",
                                "start" => "9",
                                "span" =>  "16",
                                "icon" => "a-circle-plus",
                                "lead" => 1,
                                "class" => "available no-border-top",
                                "modal_type" => "choose-filing"
                            ],
                            [
                                "id" => 113,
                                "status" => "",
                                "start" => "30",
                                "span" =>  "16",
                                "icon" => "a-circle-plus",
                                "lead" => 1,
                                "class" => "available no-border-bottom",
                                "modal_type" => "choose-filing"
                            ]
                        ]
                    ],
                    [
                        "id" => 116,
                        "date" => "12/22",
                        "no_availability" => 0,
                        "start_time" => "08:00 AM",
                        "end_time" => "05:30 PM",
                        "before_time_start" => "1",
                        "before_time_span" => "8",
                        "after_time_start" => "46",
                        "after_time_span" => "15",
                        "events" => [
                            [
                                "id" => 112,
                                "status" => "in-progress",
                                "start" => "9",
                                "span" =>  "16",
                                "icon" => "a-mobile-checked",
                                "lead" => 1,
                                "class" => "",
                                "modal_type" => "change-date"
                            ],
                            [
                                "id" => 113,
                                "status" => "breaktime",
                                "start" => "25",
                                "span" =>  "1",
                                "icon" => "",
                                "lead" => 1,
                                "class" => "",
                                "modal_type" => ""
                            ],
                            [
                                "id" => 113,
                                "status" => "",
                                "start" => "26",
                                "span" =>  "12",
                                "icon" => "a-folder",
                                "lead" => 2,
                                "class" => "",
                                "modal_type" => ""
                            ],
                            [
                                "id" => 113,
                                "status" => "",
                                "start" => "38",
                                "span" =>  "8",
                                "icon" => "a-folder",
                                "lead" => 1,
                                "class" => "no-border-bottom",
                                "modal_type" => ""
                            ]
                        ]
                    ],
                    [
                        "id" => 114,
                        "date" => "12/23",
                        "no_availability" => 1
                    ],
                    [
                        "id" => 114,
                        "date" => "12/24",
                        "no_availability" => 1
                    ],
                    [
                        "id" => 114,
                        "date" => "12/25",
                        "no_availability" => 1
                    ],
                    [
                        "id" => 114,
                        "date" => "12/26",
                        "no_availability" => 1
                    ]
                ],
                "footer" => [
                    "previous" => $newdate_previous,
                    'ref-previous' => $newdate_ref_previous,
                    "today" => $newdateformatted,
                    "next" => $newdate_next,
                    'ref-next' => $newdate_ref_next
                ]
            ]
        ]);

        return view('auditors.partials.auditor-availability-calendar', compact('data'));
    }
}
