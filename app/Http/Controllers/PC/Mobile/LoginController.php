<?php

namespace App\Http\Controllers\PC\Mobile;

use App\Http\Controllers\Controller;
use Illuminate\Support\Str;
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
use Soumen\Agent\Agent;
use Twilio;

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
    // $this->middleware('guest', ['except' => ['logout', 'getApproveAccess', 'postApproveAccess']]);
    $this->verification_number = 1299;
    $this->allitapc();
  }

  public function requestAutoLogin(Request $request){
    //dd(Auth::user());
    if(Auth::check()){
      $user = Auth::user();
      //generate a new autologin token
      $user->auto_login_token = Str::random(255);
      $user->save();
      // disable any open codes
      Token::where('user_id',$user->id)->update(['used' => 2]);
      // create a confirmation pin
      $token = new Token;
      //$token->code = strtoupper(Str::random(3)).'-'.strtoupper(Str::random(3)).'-'.strtoupper(Str::random(3));
      $token->user_id = $user->id;
      $token->save();
      $to_number = $request->number;
      $to_number =str_replace('.', '',str_replace('-', '',str_replace(')', '',str_replace('(', '',str_replace(' ', '', $to_number)))));
      //dd($to_number);
      $message = "Your ".env('APP_NAME')." Login Link: " . url('/mobile/auto_login').'?token='.$user->auto_login_token.'&user_id='.$user->id;
      try {
        Twilio::message($to_number, $message);
        return '<h2>Check Your Phone!</h2><h3>I Sent a Text Message With Your Auto-login Link to ('.substr($to_number, 0,3).') '.substr($to_number, 3,3).'-'.substr($to_number,6,4).'. Tap the Link and Enter The Verification Code:</p><hr class="dashed-hr uk-margin-bottom"><h1 class="uk-align-center"> '.$token->code.'</h1><hr class="dashed-hr uk-margin-bottom">';
      } catch (\Exception $ex) {
        return 1;
      }


    }else{
      return 'NOT LOGGED IN!';//redirect('/login');
    }
  }
  public function autoLogin(Request $request){
        $user = User::find(intval($request->user_id));
        if(null !== $user && $request->token == $user->auto_login_token){
            // user exists and token is a match
            if($user->auto_login_token !== null && strlen($user->auto_login_token) == 255 ){
              // the stored token is a valid token.
              // generate a new token.
              $user->auto_login_token = Str::random(255);
              $user->save();
              session(['user_id'=>$user->id]);
              session(['login_success'=>1]);
              session(['code_sent'=>1]);
              session(['user_id_validation' => $user->id + $this->verification_number]);
              
              return redirect('/verification?user_id='.$user->id);

              
            } else {
              //token length is invalid - make it valid and take them to login:
              $user->auto_login_token = Str::random(255);
              $user->save();
              return '<h2>The token provided was not a valid, but we fixed it! You will need to send yourself a new link. Please go back to your desktop or tablet and try again.</h2>';
              //return redirect('/login');
            }
            
        } elseif($user !== null) {
            //token did not match... to avoid a hack attempt change it.
            $user->auto_login_token = Str::random(255);
            $user->save();
            return redirect('/login');
        } else {
           return redirect('/login');
        }
    }
  
}
