<?php

namespace App\Http\Controllers\POC;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Device;
use Auth;
use Session;
use App\LogConverter;

class TwoFAController extends Controller
{
	public function __construct()
    {
        // $this->middleware('auth');
    	Auth::onceUsingId(1); // TEST BRIAN
    }

    public function index($resend = 0)
    {
    	// session(["new_device_check['54321']" => '0']); // reset tries for testing

    	// display an info page and a form field when user is trying to login and device isn't known yet

    	// we should already know who is trying to login
    	$user = Auth::user();

    	if($user === null){
    		// something isn't right, user should be known and alert admin
    		system_message('User unknown when trying to SMS device registration code.', null, null, null);
    		return 0; // go somewhere
    	}

    	// insert test to see if we already know the device here
    	$device_known = $this->isDeviceKnown(); // TODO

    	if(!$device_known){
    		$error = null;
    		$status = null;
    		$newly_added_device = null;

    		// fetch device record
    		// $device = Device::where('device_id' == 'notsurehowtogettheid')->first();

    		// if(!$device){
    			// if there is no record for this device id, create a new device in the database
    			// $device = new Device([
	            //     'user_id' => $user->id
	            // ]);
	            // $device->save();

    			// because it is the first time we are on this page for this device, we send the code
    			// $newly_added_device = 1; //TEST UNCOMMENT TO TEST FIRST LOAD
    		// }
    		
            // $device_id = $device->id;
            $device_id = '54321'; // TEST

         	// $lc = new LogConverter('Device', 'new device');
        	// $lc->setFrom(Auth::user())->setTo($device)->setDesc(Auth::user()->email . ' is adding a device.')->save();

    		// manage tries?
    		$tries = $this->checkDeviceTries($device_id);
    		if($tries >= 5){
    			// too many tries for this device, display an error and alert someone?
	         	// $lc = new LogConverter('Device', 'failed verification');
	        	// $lc->setFrom(Auth::user())->setTo($device)->setDesc(Auth::user()->email . ' tried to verify a device too many times.')->save();
	        	$error = 'You tried to verify the code too many times. Please contact support.';

    		}else{
    			if($newly_added_device || $resend){
    				// send code on that first load
    				// generate a code
		    		$code = rand(10000, 99999);

		    		// save the code in database
		    		// devices table should be updated to account for generated codes and tries
					// $device->updade(['some_field'=>$code, 'tries'=>$tries]); 

					// add a try
		    		$tries = $this->newDeviceTry($device_id);

		    		// get user's cell phone number
		    		// $phone = $user->getPhone();

		    		$phone = null; // TEST

		    		// send code via Twilio
		    		$sms_sent = $this->sendSMSCode($code, $phone);
		    		if($sms_sent == 'Queued'){
		    			$status = 'Message queued';
		    		}else{
		    			$status = $sms_sent;
		    		}
    			}else{
    				$status = 'Code already sent';
    			}	
    		}

    		return \view('poc.twilio.index', compact('user', 'status', 'error', 'tries'));
    	}else{
    		// device is known, skip SMS verification
    		return 1; // go somewhere else
    	}
    }

    public function isDeviceKnown()
    {
    	// TBD
    	return 0;
    }

    // that next function should be placed in a helper
    public function sendSMSCode($message=null, $phone=null)
    {
    	if($message === null || $message == ''){

    		$message = "Pink Elephants and Happy Rainbows"; // TEST

   			// $status = 'I cannot send an empty SMS';
			// return $status;
    	}
    	if($phone === null){
			$phone = env('TWILIO_TEST_NUMBER', '');
    	}

    	// Twilio initialization
    	$accountId = env('TWILIO_SID', '');
    	$token = env('TWILIO_TOKEN', '');
    	$fromNumber = env('TWILIO_FROM', '');
		$twilio = new \Aloha\Twilio\Twilio($accountId, $token, $fromNumber);

		// Send message to phone number
		$message_sent = $twilio->message($phone, $message);

		if($message_sent->status != 'queued'){
			$status = "Something is wrong: ".$message_sent->status;
		}else{
			$status = 'Queued';
		}

		return $status;
    }

    public function validateSMSCode(Request $request)
    {
		// Get code
		$code = $request->get('code');

		$user = Auth::user(); // user should be logged in
		if($user === null){
    		// something isn't right, user should be known and alert admin
    		system_message('User unknown when trying to validate SMS device registration code.', null, null, null);
    		$data['message'] = "User unknown";
			$data['error'] = 1;
			return $data;
    	}

    	// $check = $user->validateSMSCode($code, $device_id);
    	if($code == '12345'){ // TEST
    		$check = 1;
    	}else{
  			$tries = $this->newDeviceTry('54321'); // TEST
    		$check = null;
    	}

		if($check){ // if it works
			$data['message'] = $code;
			$data['error'] = 0;
		}else{
			if($tries >= 5){
				$data['message'] = "You tried to verify the code too many times. Please contact support.";
				$data['error'] = 2;
			}else{
				$data['message'] = "This code did not work, please try again.";
				$data['error'] = 1;
			}
		}
		
		return $data;
    }

    public function newDeviceTry($id=null)
    {
    	if($id){
    		if(!session("new_device_check['".$id."']")){
    			session(["new_device_check['".$id."']" => '1']);
    		}else{
    			session(["new_device_check['".$id."']" => session("new_device_check['".$id."']") + 1]);
    		}
    		return session("new_device_check['".$id."']");
    	}else{
    		return 0;
    	}
    }

    public function checkDeviceTries($id=null)
    {
    	return session("new_device_check['".$id."']");
    }

    public function getsms(Request $request)
    {
        $accountId = env('TWILIO_SID', 'AC6f27ac04da4d08cae26b464c669d0c5e');
        $token = env('TWILIO_TOKEN', '09b2f0946def7460369f645c9eb55a43');
        $fromNumber = env('TWILIO_FROM', '+14014000016');
        $twilio = new \Aloha\Twilio\Twilio($accountId, $token, $fromNumber);

        // get the phone information if it exists
        $from = $request['From'];
        if(empty($from)){
            exit;
        }

        $body = $request['Body'];
        if(empty($body)){
            exit;
        }

        $response = "This number is not monitored.";

        header("content-type: text/xml");
        echo "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n";
        echo "<Response>";
        echo "<Message>".$response."</Message>";
        echo "</Response>";
    }

}
