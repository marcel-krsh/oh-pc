<?php

namespace App\Http\Controllers;

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
        //Auth::onceUsingId(286); // TEST BRIAN
        Auth::onceUsingId(env('USER_ID_IMPERSONATION'));
    }

    public function index($resend = 0)
    {
        // session(["new_device_check['54321']" => '0']); // reset tries for testing

        // display an info page and a form field when user is trying to login and device isn't known yet

        // we should already know who is trying to login
        $user = Auth::user();

        if ($user === null) {
            // something isn't right, user should be known and alert admin
            system_message('User unknown when trying to SMS device registration code.', null, null, null);
            return 0; // go somewhere
        }

        // insert test to see if we already know the device here
        $device_known = $this->isDeviceKnown(); // TODO

        if (!$device_known) {
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

            // what is user's preferred 2FA method
            // $method = $user->tfa_method();  // sms, email, voice, fax
            $method = 'sms';

            // manage tries?
            $tries = $this->checkDeviceTries($device_id);
            if ($tries >= 5) {
                // too many tries for this device, display an error and alert someone?
                // $lc = new LogConverter('Device', 'failed verification');
                // $lc->setFrom(Auth::user())->setTo($device)->setDesc(Auth::user()->email . ' tried to verify a device too many times.')->save();
                $error = 'You tried to verify the code too many times. Please contact support.';
            } else {
                if ($newly_added_device || $resend) {
                    // send code on that first load
                    // generate a code
                    $code = rand(100, 999)." ".rand(100, 999)." ".rand(100, 999);

                    // save the code in database
                    // devices table should be updated to account for generated codes and tries
                    // $device->updade(['some_field'=>$code, 'tries'=>$tries]);

                    // add a try
                    $tries = $this->newDeviceTry($device_id);

                    // get user's cell phone number
                    // $phone = $user->getPhone();

                    $phone = null; // TEST

                    $message = 'Hello. This is your security code.';
                    $message_code = $code;

                    if ($method == 'sms') {
                        // send code via Twilio SMS
                        $sms_sent = $this->sendSMSCode($message, $message_code, $phone);
                        if ($sms_sent == 'Queued') {
                            $status = 'Message queued';
                        } else {
                            $status = $sms_sent;
                        }
                    } elseif ($method == 'voice') {
                        $voice = $this->makeVoiceCall($message, $message_code, $phone);
                        $status = 'Call initiated';
                    } elseif ($method == 'fax') {
                        $code_no_space = str_replace(' ', '', $message_code);
                        $fax_sent = $this->sendFax($code_no_space, $message_code, $phone);
                        $status = 'Fax sent';
                    } elseif ($method == 'email') {
                    }
                } else {
                    $status = 'Code already sent';
                }
            }

            return \view('poc.twilio.index', compact('user', 'status', 'error', 'tries'));
        } else {
            // device is known, skip SMS verification
            return 1; // go somewhere else
        }
    }

    public function isDeviceKnown()
    {
        // TBD
        return 0;
    }

    public function makeVoiceCall($message = null, $message_code = null, $phone = null)
    {
        if ($message === null || $message == '') {
            $message = "Pink Elephants and Happy Rainbows"; // TEST

            // $status = 'I cannot send an empty SMS';
            // return $status;
        }
        if ($phone === null) {
            $phone = env('TWILIO_TEST_NUMBER', '');
        }

        $accountId = env('TWILIO_SID', 'AC6f27ac04da4d08cae26b464c669d0c5e');
        $token = env('TWILIO_TOKEN', '09b2f0946def7460369f645c9eb55a43');
        $fromNumber = env('TWILIO_FROM', '+14014000016');
        $twilio = new \Aloha\Twilio\Twilio($accountId, $token, $fromNumber);

        $message_code = implode(', ', str_split($message_code));

        $call_made = $twilio->call($phone, function ($call) use ($message, $message_code) {
                        $call->pause(['length' => 1]);
                        $call->say($message.$message_code, ['voice' => 'woman','loop' => 3]);
        });
        return $call_made;
    }

    // show menu response
    public function getvoiceresponse(Request $request)
    {
        $selectedOption = $request->input('Digits');

        switch ($selectedOption) {
            case 1:
                return $this->_getReturnInstructions();
            case 2:
                return $this->_getPlanetsMenu();
        }

        $response = new Twiml();
        $response->say(
            'Returning to the main menu',
            ['voice' => 'Alice', 'language' => 'en-GB']
        );
        $response->redirect(route('welcome', [], false));

        return $response;
    }

    // that next function should be placed in a helper
    public function sendSMSCode($message = null, $message_code = null, $phone = null)
    {
        if ($message === null || $message == '') {
            $message = "Pink Elephants and Happy Rainbows"; // TEST

            // $status = 'I cannot send an empty SMS';
            // return $status;
        }
        if ($phone === null) {
            $phone = env('TWILIO_TEST_NUMBER', '');
        }

        // Twilio initialization
        $accountId = env('TWILIO_SID', '');
        $token = env('TWILIO_TOKEN', '');
        $fromNumber = env('TWILIO_FROM', '');
        $twilio = new \Aloha\Twilio\Twilio($accountId, $token, $fromNumber);

        // Send message to phone number
        $message_sent = $twilio->message($phone, $message." ".$message_code);

        if ($message_sent->status != 'queued') {
            $status = "Something is wrong: ".$message_sent->status;
        } else {
            $status = 'Queued';
        }

        return $status;
    }

    public function validateSMSCode(Request $request)
    {
        // Get code
        $code = $request->get('code');

        $user = Auth::user(); // user should be logged in
        if ($user === null) {
            // something isn't right, user should be known and alert admin
            system_message('User unknown when trying to validate SMS device registration code.', null, null, null);
            $data['message'] = "User unknown";
            $data['error'] = 1;
            return $data;
        }

        // $check = $user->validateSMSCode($code, $device_id);
        if ($code == '12345') { // TEST
            $check = 1;
        } else {
            $tries = $this->newDeviceTry('54321'); // TEST
            $check = null;
        }

        if ($check) { // if it works
            $data['message'] = $code;
            $data['error'] = 0;
        } else {
            if ($tries >= 5) {
                $data['message'] = "You tried to verify the code too many times. Please contact support.";
                $data['error'] = 2;
            } else {
                $data['message'] = "This code did not work, please try again.";
                $data['error'] = 1;
            }
        }
        
        return $data;
    }

    public function newDeviceTry($id = null)
    {
        if ($id) {
            if (!session("new_device_check['".$id."']")) {
                session(["new_device_check['".$id."']" => '1']);
            } else {
                session(["new_device_check['".$id."']" => session("new_device_check['".$id."']") + 1]);
            }
            return session("new_device_check['".$id."']");
        } else {
            return 0;
        }
    }

    public function checkDeviceTries($id = null)
    {
        return session("new_device_check['".$id."']");
    }

    public function getsms(Request $request)
    {
        app('debugbar')->disable();

        $accountId = env('TWILIO_SID', 'AC6f27ac04da4d08cae26b464c669d0c5e');
        $token = env('TWILIO_TOKEN', '09b2f0946def7460369f645c9eb55a43');
        $fromNumber = env('TWILIO_FROM', '+14014000016');
        $twilio = new \Aloha\Twilio\Twilio($accountId, $token, $fromNumber);

        // get the phone information if it exists
        $from = $request['From'];
        if (empty($from)) {
            exit;
        }

        $body = $request['Body'];
        if (empty($body)) {
            exit;
        }

        $response = "This number is not monitored.";

        header("content-type: text/xml");
        echo "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n";
        echo "<Response>";
        echo "<Message>".$response."</Message>";
        echo "</Response>";
    }

    public function getsmsfailed(Request $request)
    {
        // do something
    }

    public function getvoice(Request $request)
    {
    }

    public function getvoicefailed(Request $request)
    {
        // do something
    }

    public function sendFax($code_no_space = null, $message_code = null, $phone = null)
    {
        if ($phone === null) {
            $phone = '+18442660833'; // using http://www.faxburner.com/ to test, phone changes every couple of days
        }

        // Twilio initialization
        $accountId = env('TWILIO_SID', '');
        $token = env('TWILIO_TOKEN', '');
        $fromNumber = env('TWILIO_FROM', '');
        $twilio = new \Aloha\Twilio\Twilio($accountId, $token, $fromNumber);

        // get the pdf url
        // $pdfurl = route('device.create.fax.pdf', ['code' => $message_code]);
        $pdfurl = "http://65a6d568.ngrok.io/poc/tfa/faxpdf/".$code_no_space; // for testing only, won't work on localhost

        $sdk = $twilio->getTwilio();
        $fax = $sdk->fax->v1->faxes
                   ->create(
                       $phone, // to
                       $pdfurl,
                       //"https://www.twilio.com/docs/documents/25/justthefaxmaam.pdf", // mediaUrl
                        ["from" => $fromNumber]
                   );

        return $fax->sid;
    }

    public function generateFaxPdf($code = null)
    {
        // generate a PDF to be sent by fax

        // make sure that the user is currently logged in and that the code matches that user
        // to prevent possible direct access

        $code = chunk_split($code, 3, ' '); // add spaces

        $pdf = \PDF::loadView('poc.twilio.faxpdf', compact('code'));
        return $pdf->download('allita_compliance.pdf');
    }
}
