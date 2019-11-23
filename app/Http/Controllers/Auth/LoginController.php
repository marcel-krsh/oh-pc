<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Mail\EmailApproveUserAccess;
use App\Models\Role;
use App\Models\Token;
use App\Models\User;
use App\Models\UserRole;
use Auth;
use Cookie;
use GeoIP;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Session;
use Agent;

class LoginController extends Controller
{
  /*
  |--------------------------------------------------------------------------
  | Login Controller
  |--------------------------------------------------------------------------
  |
  | This controller handles authenticating users for the application and
  | redirecting them to your home screen. The controller uses a trait
  | to conveniently provide its functionality to your applications.
  |
   */

  use AuthenticatesUsers;

  /**
   * Where to redirect users after login.
   *
   * @var string
   */
  protected $redirectTo = '/';
  protected $verification_number;

  /**
   * Create a new controller instance.
   *
   * @return void
   */
  public function __construct()
  {
    $this->middleware('guest', ['except' => ['logout', 'getApproveAccess', 'postApproveAccess']]);
    $this->verification_number = 1299;
  }

  /**
   * Handle an authentication attempt.
   *
   * @param  \Illuminate\Http\Request $request
   *
   * @return Response
   */
  public function authenticate(Request $request)
  {
    $credentials = $request->only('user_id', 'token');

    if (Auth::attempt($credentials)) {
      // Authentication passed...
      Auth::user()->auto_login_token = Str::random(255);
      Auth::user()->save();
      return redirect()->intended('dashboard');
    } else {
      // return "oops";
      return redirect()->intended('login');
    }
  }

  public function login(Request $request)
  {
    $validator = \Validator::make($request->all(), [
      'email'    => 'required|email',
      'password' => 'required',
    ]);
    if ($validator->fails()) {
      $errors = $validator->errors()->all();
      return redirect()->back()->withInput($request->except('password'))->withErrors($validator);
    }
    if (Auth::validate($request->only('email', 'password'))) {
      $user   = User::where('email', $request->email)->first();
      $device = Cookie::get('device_' . $user->id);
      if (!$user->isActive()) {
        $validator->getMessageBag()->add('error', 'User is not active');
        return redirect()->back()->withInput($request->except('password'))->withErrors($validator);
      }
      // check device already registered
      $is_device_verifed = Token::where('user_id', $user->id)
        ->where('used', 1)
        ->where('device', $device)
        ->first();
      //if true login with id & return to intended url else create toke
      if ($is_device_verifed) {
        session(["user_id" => $user->id]);
        if (count($user->roles) == 0) {
          return redirect('request-access');
        }
        $user           = Auth::loginUsingId($user->id, 1);
        $token          = new Token;
        $token->user_id = $user->id;
        $token->save();
        $this->saveToken($token, 'login');
        //flash('Login successful')->success();
        if (count($user->roles) > 0) {
          return redirect()->intended('/');
        } else {
          return redirect('request-access');
        }
      } else {
        session(["user_id" => $user->id]);
        session(["remember" => $request->get('remember')]);
        session(["login_success" => true]);
        session(["user_id_validation" => $user->id + $this->verification_number]);
        //flash('Select channel communication')->success();
        return redirect("code");
      }
      $token->delete(); // delete token because veriication code cannot be sent
      return redirect('/login')->withErrors([
        "Unable to send verification code",
      ]);
    }
    $validator->getMessageBag()->add('error', 'Login credentials didn\'t match our records');
    return redirect()->back()->withInput($request->except('password'))->withErrors($validator); /*->withInputs()
  ->withErrors([
  'email' => Lang::get('auth.failed'),
  ]);*/
  }

  public function getCode(Request $request)
  {
    $user_id = intval(session()->get('user_id'));
    $user    = User::with('person.allita_phone')->find($user_id);
    if (is_null($user) || !session()->has('login_success')) {
    	$error   = "Looks like the user doesn't exist with the provided information. ".$user_id.' '.session('user_id');
	    abort(403, $error);
      // $message = "<h2>USER NOT FOUND!</h2><p>No user information has been found</p>";
      // $error   = "Looks like the user doesn't exist with the provided information";
      // $type    = "danger";
      return view('errors.error', compact('error', 'message', 'type'));
    } else {
      if ($user->person->allita_phone) {
        $phone_number     = $user->person->allita_phone->number;
        $mask_phonenumber = mask_phone_number($phone_number);
      } else {
        $mask_phonenumber = null;
      }
      $mask_email = mask_email($user->email);
      return view('auth.code', compact('user', 'user_id', 'phone_number', 'mask_phonenumber', 'mask_email'));
    }
  }

  public function getVerification()
  {
    $user_id = session()->get('user_id');
    $user    = User::with('person.allita_phone')->find($user_id);
    if ($user && session()->get('user_id_validation') == $user_id + $this->verification_number && session()->has('code_sent')) {
      return view('auth.verification', compact('user', 'user_id'));
    } else {
    	$error   = "Looks like the user doesn't exist with the provided information";
	    abort(403, $error);
      // $message = "<h2>USER NOT FOUND!</h2><p>No user information has been found</p>";
      // $error   = "Looks like the user doesn't exist with the provided information";
      // $type    = "danger";
      // return view('errors.error', compact('error', 'message', 'type'));
    }
  }

  public function getRequestAccess(Request $request)
  {
    $user_id = session()->get('user_id');
    $user    = User::with('person.allita_phone')->find($user_id);
    if (is_null($user)) {
    	$error   = "Looks like the user doesn't exist with the provided information";
	    abort(403, $error);
      // $message = "<h2>USER NOT FOUND!</h2><p>No user information has been found</p>";
      // $error   = "Looks like the user doesn't exist with the provided information";
      // $type    = "danger";
      // return view('errors.error', compact('error', 'message', 'type'));
    } else {
      if (count($user->roles) > 0) {
        return redirect()->intended('/');
      }
      return view('auth.request-access', compact('user', 'user_id'));
    }
  }

  public function postVerification(Request $request)
  {
    $validator = \Validator::make($request->all(), [
      'verification_code' => 'required|exists:tokens,code',
    ], [
    	'verification_code.exists' => 'The verification code entered was not valid. Please check your code and try again.',
    ]);
    if ($validator->fails()) {
      $errors = $validator->errors()->all();
      return redirect()->back()->withErrors($validator);
    }
    $user_id = session()->get('user_id');
    $token   = Token::where('code', $request->verification_code)
      ->where('user_id', $user_id)
      ->orderBy('id', 'DESC')
      ->first();
    $user = User::find($user_id);
    if ($token->isValid()) {
      if (count($user->roles) == 0) {
        return redirect('request-access');
      }
      Auth::loginUsingId($user_id, 1);
      $token = $this->saveToken($token);
      //if (!empty($request->device)) {
      //$token->device = $request->device;
      //Issue here
      //  if user with no role is verified, still need to redirect to request access
      //  If a user with tole is verified, need to redirect to homepage
      $user = Auth::user();
      if (0 == $user->verified) {
        //$user->activate();
        $user->verify();
        $user->save();
      }
      $cookie = Cookie::forever('device_' . $user_id, $token->device);
      // }
      $token->used = 1;
      if ($request->has('device_name')) {
        $token->user_device_name = $request->device_name;
      }

      $token->save();
      return redirect()->intended('/')->withCookie($cookie);
    } else {
    	$validator->getMessageBag()->add('error', 'The verification code entered was expired or already used. Please login again to receive new code.');
      return redirect('login')->withErrors($validator);
    }
    return redirect()->back();
  }

  public function saveToken($token, $trigger = 'code')
  {
    $ip       = $this->getUserIpAddr();
    $location = GeoIP::getLocation($ip);
    $agent    = Agent::all();
    if ('login' == $trigger) {
      $token->used = 2;
      $token->code = '000-000-0000';
    }
    $token->ip        = $ip;
    $token->isMobile  = $agent->device->isMobile;
    $token->isTablet  = $agent->device->isTablet;
    $token->isDesktop = $agent->device->isDesktop;
    $token->isBot     = $agent->device->isBot;
    $token->browser   = $agent->browser->name;
    $token->platform  = $agent->platform->name;
    $token->device    = $agent->device->family . $token->browser . $token->platform;

    $token->iso_code    = $location->iso_code;
    $token->country     = $location->country;
    $token->city        = $location->city;
    $token->state       = $location->state;
    $token->state_name  = $location->state_name;
    $token->postal_code = $location->postal_code;
    $token->lat         = $location->lat;
    $token->lon         = $location->lon;
    $token->timezone    = $location->timezone;
    $token->continent   = $location->continent;
    $token->currency    = $location->currency;
    $token->save();
    return $token;
  }

  public function postCode(Request $request)
  {
    $validator = \Validator::make($request->all(), [
      'delivery_method' => 'required',
    ]);
    if ($validator->fails()) {
      $errors = $validator->errors()->all();
      return redirect()->back()->withInput($request->except('password'))->withErrors($validator);
    }
    $user_id = session()->get('user_id');
    $user    = User::with('person.allita_phone')->find($user_id);
    if ($user->person->allita_phone) {
      $phone_number = $user->person->allita_phone->number;
    } else {
      $phone_number = null;
    }
    if ($user && session()->get('user_id_validation') == $user_id + $this->verification_number) {
      $token = Token::create([
        'user_id' => $user->id,
      ]);
      $status = false;
      if (2 == $request->delivery_method) {
        // send voice request
        $status = $token->sendCode("voice", $phone_number);
      } elseif (1 == $request->delivery_method) {
        // send sms request
        $status = $token->sendCode("sms", $phone_number);
      } else {
        // send email request
        $status = $token->sendCode("email");
      }
      if ($status) {
        session(["code_sent" => session()->pull('login_success')]);
        return redirect()->to("verification");
      } else {
        $validator->getMessageBag()->add('error', 'Something went wrong, please contact admin');
        return redirect()->back()->withErrors($validator);
      }
    } else {
      $validator->getMessageBag()->add('error', 'Something went wrong, please contact admin');
      return redirect()->back()->withErrors($validator);
    }
  }

  public function postRequestAccess(Request $request)
  {
    $user_id      = session()->get('user_id');
    $current_user = User::with('person.allita_phone')->find($user_id);
    //Send email to all admins
    $admin_ids = UserRole::where('role_id', 4)->pluck('user_id');
    $admins    = User::whereIn('id', $admin_ids)->get();
    if (count($admins) == 0) {
      return 'Something went wrong. Try again later or contact Technical Team';
    }
    foreach ($admins as $key => $admin) {
      $email_notification_to_admins = new EmailApproveUserAccess($admin, $current_user);
      \Mail::to($admin->email)->send($email_notification_to_admins); //enable this in live
    }
    return 1;
  }

  public function getApproveAccess($user_id)
  {
    $current_user = Auth::user();
    if (!is_null($current_user) && $current_user->can('access_admin')) {
      // Check if user is logged in, taken care by middleware
      // Check if the person accessing this page is admin
      // Get the roles based on hierarchy
      // get user and show details

      //$current_user = Auth::onceUsingId(env('USER_ID_IMPERSONATION'));
      $user = User::find($user_id);
      if (!$user) {
      	$error   = "Looks like the user doesn't exist with the provided information";
	    	abort(403, $error);
        // $message = "<h2>USER NOT FOUND!</h2><p>No user information has been found</p>";
        // $error   = "Looks like the user doesn't exist with the provided information";
        // $type    = "danger";
        // return view('errors.error', compact('error', 'message', 'type'));
      } elseif (count($user->roles) > 0) {
      	$error   = "Looks like the user has been already given access";
	    	abort(403, $error);
        // $message = "<h2>ACCESS GRANTED!</h2><p>This user has been already given access</p>";
        // $error   = "Looks like the user has been already given access";
        // $type    = "message";
        // return view('errors.error', compact('error', 'message', 'type'));
      }
      session(["user_id" => $user->id]);
      $current_user_role_id = $current_user->roles->first()->role_id;
      $roles                = Role::where('id', '<', $current_user_role_id)->active()->orderBy('role_name', 'ASC')->get();
      if ($current_user->admin_access()) {
        return view('auth.approve-access', compact('user', 'user_id', 'roles'));
      }
    } else {
      if (is_null($current_user)) {
        if (!session()->has('url.intended')) {
          session(['url.intended' => url()->previous()]);
        }
        return view('auth.login');
      } else {
        return 'Sorry, you do not have sufficient priveledges to access this page.';
      }
    }
  }

  public function postApproveAccess(Request $request)
  {
    $current_user = Auth::user();
    if (!is_null($current_user) && $current_user->can('access_admin')) {
      $validator = \Validator::make($request->all(), [
        'role' => 'required',
      ]);
      if ($validator->fails()) {
        return 2;
      }
      $user_id      = session()->get('user_id');
      $current_user = Auth::user();
      //$current_user = Auth::onceUsingId(env('USER_ID_IMPERSONATION'));
      $user = User::find($user_id);
      if (!$user) {
      	$error   = "Looks like the user doesn't exist with the provided information";
	    	abort(403, $error);
        // $message = "<h2>USER NOT FOUND!</h2><p>No user information has been found</p>";
        // $error   = "Looks like the user doesn't exist with the provided information";
        // $type    = "danger";
        // return view('errors.error', compact('error', 'message', 'type'));
      } elseif (count($user->roles) > 0) {
      	$error   = "Looks like the user has been already given access";
	    	abort(403, $error);
        // $message = "<h2>ACCESS GRANTED!</h2><p>This user has been already given access</p>";
        // $error   = "Looks like the user has been already given access";
        // $type    = "message";
        // return view('errors.error', compact('error', 'message', 'type'));
      }
      $current_user_role_id = $current_user->roles->first()->role_id;
      if ($request->role >= $current_user_role_id) {
        return 'Something went wrong';
      }
      $user_role          = new UserRole;
      $user_role->role_id = $request->role;
      $user_role->user_id = $user->id;
      $user_role->save();
      return 1;
    } else {
      if (is_null($current_user)) {
        if (!session()->has('url.intended')) {
          session(['url.intended' => url()->previous()]);
        }
        return view('auth.login');
      } else {
        return 'Sorry, you do not have sufficient priveledges to access this page.';
      }
    }
  }

  protected function extraCheckErrors($validator)
  {
    $validator->getMessageBag()->add('error', 'Something went wrong. Try again later or contact Technical Team');
    return response()->json(['errors' => $validator->errors()->all()]);
  }

  public function getUserIpAddr()
  {
    if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
      //ip from share internet
      $ip = $_SERVER['HTTP_CLIENT_IP'];
    } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
      //ip pass from proxy
      $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
    } else {
      $ip = $_SERVER['REMOTE_ADDR'];
    }
    return $ip;
  }
}
