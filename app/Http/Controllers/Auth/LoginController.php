<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Token;
use App\Models\User;
use Auth;
use Cookie;
use GeoIP;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Session;
use Soumen\Agent\Agent;

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
    $this->middleware('guest', ['except' => 'logout']);
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
      // check device already registered
      $is_device_verifed = Token::where('user_id', $user->id)
        ->where('used', 1)
        ->where('device', $device)
        ->first();
      //if true login with id & return to intended url else create toke
      if ($is_device_verifed) {
        Auth::loginUsingId($user->id, 1);
        $token          = new Token;
        $token->user_id = $user->id;
        $token->save();
        $this->saveToken($token, 'login');
        //flash('Login successful')->success();
        return redirect()->intended('/');
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
    $user_id = session()->get('user_id');
    $user    = User::with('person.allita_phone')->find($user_id);
    if (is_null($user) || !session()->has('login_success')) {
      $message = "<h2>USER NOT FOUND!</h2><p>No user information has been found</p>";
      $error   = "Looks like the user doesn't exist with the provided information";
      $type    = "danger";
      return view('errors.error', compact('error', 'message', 'type'));
    } else {
      $phone_number     = $user->person->allita_phone->area_code . $user->person->allita_phone->phone_number;
      $mask_phonenumber = mask_phone_number($phone_number);
      $mask_email       = mask_email($user->email);
      return view('auth.code', compact('user', 'user_id', 'phone_number', 'mask_phonenumber', 'mask_email'));
    }
  }

  public function postVerification(Request $request)
  {
    $validator = \Validator::make($request->all(), [
      'verification_code' => 'required|exists:tokens,code',
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
    if ($token->isValid()) {
      Auth::loginUsingId($user_id, 1);
      $token = $this->saveToken($token);
      //if (!empty($request->device)) {
      //$token->device = $request->device;
      $user = Auth::user();
      if (0 == $user->verified) {
        $user->activate();
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
    }
    flash('Token Expired')->error();
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

  public function getVerification()
  {
    $user_id = session()->get('user_id');
    $user    = User::with('person.allita_phone')->find($user_id);
    if ($user && session()->get('user_id_validation') == $user_id + $this->verification_number && session()->has('code_sent')) {
      return view('auth.verification', compact('user', 'user_id'));
    } else {
      $message = "<h2>USER NOT FOUND!</h2><p>No user information has been found</p>";
      $error   = "Looks like the user doesn't exist with the provided information";
      $type    = "danger";
      return view('errors.error', compact('error', 'message', 'type'));
    }
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
    $user_id      = session()->get('user_id');
    $user         = User::with('person.allita_phone')->find($user_id);
    $phone_number = $user->person->allita_phone->area_code . $user->person->allita_phone->phone_number;
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
