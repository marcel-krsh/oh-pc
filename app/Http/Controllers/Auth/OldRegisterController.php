<?php

namespace App\Http\Controllers\Auth;

use App\Models\User;
use App\Models\Entity;
use App\Models\Program;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Foundation\Auth\RegistersUsers;
use App\Models\Mail\EmailActivation;
use App\Models\Mail\EmailEntityActivation;
use Illuminate\Support\Facades\DB;

class OldRegisterController extends Controller
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
     * Where to redirect users after login / registration.
     *
     * @var string
     */
    protected $redirectTo = '/home';

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
            'name' => 'required|max:255',
            'email' => 'required|email|max:255|unique:users',
            'email-confirm' => 'required|same:email|max:255',
            'password' => 'required|min:6|confirmed',
            'g-recaptcha-response' => 'required|captcha',
            'entity_id' => 'required_without_all:newlandbank',
            'program_name' => 'required_if:newlandbank,"TRUE"',
            'county_id' => 'required_if:newlandbank,"TRUE"',
            'entity_name' => 'required_if:newlandbank,"TRUE"',
            'address1' => 'required_if:newlandbank,"TRUE"',
            'city' => 'required_if:newlandbank,"TRUE"',
            'state_id' => 'required_if:newlandbank,"TRUE"',
            'zip' => 'required_if:newlandbank,"TRUE"',
            'phone' => 'required_if:newlandbank,"TRUE"',
        ]);
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return User
     */
    protected function create(array $data)
    {
        // return User::create([
        //     'name' => $data['name'],
        //     'email' => $data['email'],
        //     'password' => bcrypt($data['password']),
        // ]);
        $newuser = ""; //define new user,  will be overwritten below
        if (isset($data['newlandbank'])) {
            // $m = "inside";
            // dd($m,$data);
            //we have a new landbank,  create both user and landbank
            $newuser = User::create([
                'name' => $data['name'],
                'email' => $data['email'],
                'validate_all' => 1,
                'password' => bcrypt($data['password']),
                'entity_id' => 99999, //give temporary id
                'email_token' => str_random(60),
                
                'active'=> 0, // make sure they are set to an active of 0 until they are verified.
            ]);
            $newEntity= Entity::insertGetId([
                'entity_name' => $data['entity_name'],
                'address1' => $data['address1'],
                'address2' => $data['address2'],
                'city' => $data['city'],
                'state_id'=>$data['state_id'],
                'zip' => $data['zip'],
                'phone' => $data['phone'],
                'fax' => $data['fax'],
                'web_address' => $data['web_address'],
                'email_address' => $data['email_address'],
                'logo_link' => $data['logo_link'],
                'owner_type' => 'user',
                'owner_id' => $newuser->id,
                'user_id' => $newuser->id,
                'datatran_user' => "a",
                'datatran_password' => "a",
                'active' => 0, // make sure entity is not active so it does not show up in list of active orgs until approved.
            ]);
            $newuser->entity_id = $newEntity;
            $newuser->validate_all = 1;
            $newuser->save();
            /// New Landbank Program
            $newProgram = Program::insertGetId([
                'hfa'=>1,
                'owner_id'=>$newEntity,
                'entity_id'=>$newEntity,
                'program_name'=>$data['program_name'],
                'county_id'=>$data['county_id'],
                'active'=>0 // make sure program is not active and does not show up in list on front as available program.
                ]);
            /// Store ids into session.
            session(['newUserId'=>$newuser->id,'newEntityId'=>$newEntity,'newProgramId'=>$newProgram]);

            $approvers = DB::table('users')->join('users_roles', 'users_roles.user_id', '=', 'users.id')->select('users.id', 'users.email')->where('users_roles.role_id', 5)->get()->all();
            //dd($approvers);
            foreach ($approvers as $approver) {
                /// Send an approval email to each person in the role.
                session(['approverId'=>$approver->id]);
                $emailEntityActivation = new EmailEntityActivation(User::find($approver->id));
                \Mail::to($approver->email)->send($emailEntityActivation);
            }
            /// Remove from session.
            session(['newUserId'=>'','newEntityId'=>'','newProgramId'=>'']);
            return $newuser;
        } else {
            //no new Entity,  create user with existing landbank
            $newuser = User::create([
                'name' => $data['name'],
                'email' => $data['email'],
                'password' => bcrypt($data['password']),
                'entity_id' => $data['entity_id'],
                'email_token' => str_random(254)
            ]);
            //send mail to Entity owner
            $entity = Entity::find($newuser->entity_id);
            $owner = User::find($entity->owner_id);
            session(['ownerId' => $owner->id,'newUserId'=>$newuser->id]);
            $emailactivation = new EmailActivation($owner);
            \Mail::to($owner->email)->send($emailactivation);
            return $newuser;
        }
        // //send mail to new user
        // $email = new EmailVerification($newuser);
        // \Mail::to($newuser->email)->send($email);
    }
    public function verify($token)
    {
        User::where('email_token', $token)->firstOrFail()->verify();
        return redirect('/login');
    }
}
