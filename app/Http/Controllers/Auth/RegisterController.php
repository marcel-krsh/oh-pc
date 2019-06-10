<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\EmailAddress;
use App\Models\EmailAddressType;
use App\Models\People;
use App\Models\PhoneNumber;
use App\Models\PhoneNumberType;
use App\Models\Role;
use App\Models\User;
use DB;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class RegisterController extends Controller
{
  /*
  |--------------------------------------------------------------------------
  | Register Controller
  |--------------------------------------------------------------------------
  |
  | This controller handles the registration of new users as well as their
  | validation and creation. By default this controller uses a trait to
  | provide this functionality without requiring any additional code.
  |
   */

  use RegistersUsers;

  /**
   * Where to redirect users after registration.
   *
   * @var string
   */
  protected $redirectTo = '/';

  /**
   * Create a new controller instance.
   *
   * @return void
   */
  public function __construct()
  {
    $this->middleware('guest');
  }

  /**
   * Get a validator for an incoming registration request.
   *
   * @param  array  $data
   * @return \Illuminate\Contracts\Validation\Validator
   */
  protected function validator(array $data)
  {
    return Validator::make($data, [
      'first_name'   => 'required|max:255',
      'last_name'    => 'required|max:255',
      'email'        => 'required|string|email|max:255|unique:users',
      'password'     => 'required|string|min:8|confirmed',
      'phone_number' => 'required|min:12',
    ], [
    	'email.unique' => 'This email has already been registered'
    ]);
  }

  public function postRegister(Request $request)
  {
    $this->validator($request->all())->validate();
    DB::beginTransaction();
    try {
      //Phone numbers table
      $input_phone_number                 = $request->phone_number;
      $split_number                       = explode('-', $input_phone_number);
      $phone_number_type                  = PhoneNumberType::where('phone_number_type_name', 'Business')->first();
      $phone_number                       = new PhoneNumber;
      $phone_number->phone_number_type_id = $phone_number_type->id;
      $phone_number->area_code            = $split_number[0];
      $phone_number->phone_number         = $split_number[1] . $split_number[2];
      $phone_number->save();

      // Email address table
      $email_address_type                   = EmailAddressType::where('email_address_type_name', 'Work')->first();
      $email_address                        = new EmailAddress;
      $email_address->email_address         = $request->email;
      $email_address->email_address_type_id = $email_address_type->id;
      $email_address->save();

      // People table
      $people                           = new People;
      $people->last_name                = $request->last_name;
      $people->first_name               = $request->first_name;
      $people->default_phone_number_id  = $phone_number->id;
      $people->default_email_address_id = $email_address->id;
      $people->is_active                = 1;
      $people->save();

      // User table
      $user        = new User;
      $user->name  = $people->first_name . ' ' . $people->last_name;
      $user->email = $email_address->email_address;
      //$user->active        = 1;
      $user->password    = bcrypt($request->password);
      $selected_role     = Role::where('role_name', 'Auditor')->active()->first();
      $user->email_token = alpha_numeric_random(60);
      $user->person_id   = $people->id;
      $user->save();
      $user->activate();

      // User role table
      // $user_role          = new UserRole;
      // $user_role->role_id = $selected_role->id;
      // $user_role->user_id = $user->id;
      // $user_role->save();
      DB::commit();
      return redirect()->to('login');
    } catch (\Exception $e) {
      DB::rollBack();
      $data_insert_error = $e->getMessage();
    }
    $validator->getMessageBag()->add('error', 'Something went wrong. Try again later or contact Technical Team');
    return response()->json(['errors' => $validator->errors()->all()]);

  }
}
