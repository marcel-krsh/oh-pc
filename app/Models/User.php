<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

/**
 * User Model
 *
 * @category Models
 * @license  Proprietary and confidential
 */
class User extends Authenticatable
{
  use Notifiable;

  /**
   * The attributes that are mass assignable.
   *
   * @var array
   */
  protected $fillable = [
    'name',
    'email',
    'password',
    'organization_id',
    'badge_color',
    'email_token',
    'active',
    'validate_all',
    'entity_type',
    'api_token',
    'devco_key',
    'last_accessed',
    'socket_id',
    'person_key',
    'person_id',
    'last_edited',
    'user_status_key',
    'organization_id',
    'organization_key',
    'organization',
    'availability_max_hours',
    'availability_lunch',
    'availability_max_driving',
    'default_address_id',
    'allowed_tablet',
  ];

  protected static $logAttributes = [
    'name',
    'email',
    'organization_id',
    'badge_color',
  ];

  /**
   * The attributes that should be hidden for arrays.
   *
   * @var array
   */
  protected $hidden = [
    'password',
    'remember_token',
  ];

  /**
   * A user can have many messages
   *
   * @return \Illuminate\Database\Eloquent\Relations\HasMany
   */
  public function messages()
  {
    return $this->hasMany(Message::class);
  }

  /**
   * Roles
   *
   * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
   */
  public function roles(): HasMany
  {
    return $this->hasMany(UserRole::class);
  }

  public function role(): HasOne
  {
    return $this->hasOne(UserRole::class);
  }

  public function roles_list()
  {
    $roles  = $this->roles()->get();
    $output = '';
    foreach ($roles as $role) {
      if ($output == '') {
        $output = $output . $role->role->name;
      } else {
        $output = $output . ', ' . $role->role->name;
      }
    }
    if ($output == '') {
      $output = 'NO ACCESS';
    }
    return $output;
  }

  /**
   * Person
   *
   * @return \Illuminate\Database\Eloquent\Relations\HasOne
   */
  public function person(): HasOne
  {
    return $this->hasOne(People::class, 'id', 'person_id');
  }

  /**
   * Availabilities
   *
   * @return \Illuminate\Database\Eloquent\Relations\HasMany
   */
  public function availabilities(): HasMany
  {
    return $this->hasMany(Availability::class, 'user_id', $this->id);
  }

  /**
   * Organization
   *
   * @return \Illuminate\Database\Eloquent\Relations\HasOne
   */
  public function has_organization(): int
  {
    if (is_null($this->organization_id)) {
      return false;
    } else {
      return true;
    }
  }

  public function has_address(): int
  {
    if (is_null($this->organization_id) || is_null($this->organization_details->address)) {
      return false;
    } else {
      return true;
    }
  }

  public function organization_details(): HasOne
  {
    return $this->hasOne(Organization::class, 'id', 'organization_id');
  }

  public function auditor_addresses(): HasMany
  {
    return $this->HasMany(Address::class, 'user_id', 'id');
  }

  public function addresses(): HasMany
  {
    return $this->HasMany(Address::class, 'user_id', 'id');
  }

  public function initials(): string
  {

    $person = $this->person;
    //dd($person, $this->id, $this->person_id);
    if ($this->person) {
      $initials = substr($person->first_name, 0, 1);
      $initials .= substr($person->last_name, 0, 1);
      return strtoupper($initials);
    } else {
      return 'NA';
    }
  }

  public function full_name(): string
  {
    $fullName = "NA";
    if ($this->person) {
      $fullName = $this->person->first_name . " " . $this->person->last_name;
    }
    return $fullName;
  }

  /**
   * Is From Organization
   *
   * @param $id
   *
   * @return int
   */
  public function isFromOrganization($id): int
  {
    if ($id == $this->organization_id) {
      return 1;
    }
    return 0;
  }

  public function isFromEntity($id): int
  {
    if ($id == $this->entity_id) {
      return 1;
    }
    return 0;
  }

  public function isOhfa(): bool
  {
    $ohfa_id = SystemSetting::get('ohfa_organization_id');
    if ($ohfa_id == $this->organization_id) {
      return 1;
    }
    return 0;
  }

  public function hasRole($role_id): bool
  {
    foreach ($this->roles()->get() as $role) {
      if ($role->role_id == $role_id) {
        return true;
      }
    }
    return false;
  }

  /**
   * Has PM level access
   *
   * @return bool
   */
  public function pm_access(): bool
  {
    foreach ($this->roles()->get() as $role) {
      if ($this->hasRole(1) || $this->hasRole(2) || $this->hasRole(3) || $this->hasRole(4) || $this->hasRole(5)) {
        return true;
      }
    }
    return false;
  }

  /**
   * Has Auditor level access
   *
   * @return bool
   */
  public function auditor_access(): bool
  {
    foreach ($this->roles()->get() as $role) {
      if ($this->hasRole(2) || $this->hasRole(3) || $this->hasRole(4) || $this->hasRole(5)) {
        return true;
      }
    }
    return false;
  }

  /**
   * Has Manager level access
   *
   * @return bool
   */
  public function manager_access(): bool
  {
    foreach ($this->roles()->get() as $role) {
      if ($this->hasRole(3) || $this->hasRole(4) || $this->hasRole(5)) {
        return true;
      }
    }
    return false;
  }

  /**
   * Has Admin level access
   *
   * @return bool
   */
  public function admin_access(): bool
  {
    foreach ($this->roles()->get() as $role) {
      if ($this->hasRole(4) || $this->hasRole(5)) {
        return true;
      }
    }
    return false;
  }

  /**
   * Has Root level access
   *
   * @return bool
   */
  public function root_access(): bool
  {
    foreach ($this->roles()->get() as $role) {
      if ($this->hasRole(5)) {
        return true;
      }
    }
    return false;
  }

  /**
   * Get Perms
   *
   * @return array
   */
  public function getPerms()
  {
    $permnames = [];
    foreach ($this->roles as $userrole) {
      foreach ($userrole->permissions as $perm) {
        array_push($permnames, $perm->permission_name);
      }
    }
    return $permnames;
  }

  /**
   * Verify
   */
  public function verify()
  {
    $this->verified    = 1;
    $this->email_token = null;
    $this->save();
  }

  /**
   * Activate
   */
  public function activate()
  {
    $this->active = 1;
    $this->save();
  }

  /**
   * Increment Tries
   */
  public function incrementTries()
  {
    $this->tries = $this->tries + 1;
    $this->save();
  }

  /**
   * Reset Tries
   */
  public function resetTries()
  {
    $this->tries = 0;
    $this->save();
  }

  /**
   * Deactivate
   */
  public function deactivate()
  {
    $this->active = 0;
    $this->save();
  }

  /**
   * Is Active
   *
   * @return mixed
   */
  public function isActive()
  {
    //proxy return value
    return $this->active;
  }

  public function isScheduled($audit_id, $day_id): int
  {
    // audit_id is the id of Audit, not CachedAudit
    if (count(ScheduleTime::where('auditor_id', '=', $this->id)->where('audit_id', '=', $audit_id)->where('day_id', '=', $day_id)->get())) {
      return 1;
    } else {
      return 0;
    }
  }

  public function isAuditorOnAudit($audit_id): int
  {
    // audit_id is the id of Audit, not CachedAudit
    if (count(AuditAuditor::where('audit_id', '=', $audit_id)->where('user_id', '=', $this->id)->get())) {
      return 1;
    } else {
      return 0;
    }
  }

  // returns the times and slots of earliest and latest availability
  public function availabilityOnDay($day_id)
  {
    // availability without taking into account scheduled time
    $day            = ScheduleDay::where('id', '=', $day_id)->first();
    $date           = formatDate($day->date, 'Y-m-d', 'Y-m-d H:i:s');
    $availabilities = Availability::where('user_id', '=', $this->id)->where('date', '=', $date)->get();

    if (count($availabilities)) {
      // $start_slot = null; // 1 06:00
      // $end = null; // 58 20:00
      $start_slot = null;
      $end_slot   = null;
      foreach ($availabilities as $a) {
        $a_start_slot = timeToSlot($a->start_time);
        $a_end_slot   = timeToSlot($a->end_time);

        // initial values
        if (!$start_slot) {
          $start_slot = $a_start_slot;
        }

        if (!$end_slot) {
          $start_slot = $a_start_slot;
        }

        // compare and save the earliest and latest times
        if ($a_start_slot < $start_slot) {
          $start_slot = $a_start_slot;
        }

        if ($a_end_slot > $end_slot) {
          $end_slot = $a_end_slot;
        }
      }
      return [slotToTime($start_slot), slotToTime($end_slot), $start_slot, $end_slot];
    }

    return null;
  }

  public function scheduledOnDay($day_id, $audit_id)
  {
    // scheduled times
    $schedules = ScheduleTime::where('day_id', '=', $day_id)->where('audit_id', '=', $audit_id)->where('user_id', '=', $this->id)->get();
  }

  public function timeAvailableOnDay($day_id)
  {
    // availability after taking into account the schedules
    $availabilityOnDay = $this->availabilityOnDay($day_id);
    if ($availabilityOnDay) {
      $span = $availabilityOnDay[3] - $availabilityOnDay[2];

      $hours   = sprintf("%02d", floor(($span) * 15 / 60));
      $minutes = sprintf("%02d", ($span) * 15 % 60);

      return $hours . ':' . $minutes;
    } else {
      return null;
    }
  }

  public function default_address()
  {
    // if there is a default address, pick it, otherwise choose the organization address
    $default_address_id = $this->default_address_id;

    $address = Address::where('id', '=', $default_address_id)->first();
    if ($address) {
      return $address->formatted_address();
    } else {
      if ($this->organization_details) {
        return $this->organization_details->address->formatted_address();
      } else {
        return '';
      }
    }
  }

  public function distanceAndTime($audit_id)
  {
    $address = $this->default_address();
    if ($address != '') {
      $audit = Audit::where('id', '=', $audit_id)->first();
      if ($audit->project->address) {
        $project_address = $audit->project->address->formatted_address();
      } else {
        $project_address = 'No Address';
      }

      $address = urlencode($address);

      //$googleAPI = 'AIzaSyCIXStIobkIUMeRCH-nwikznhFc39pAf9Q'; //env('GOOGLE_API_KEY');
      $googleAPI       = SystemSetting::get('google_api_key');
      $project_address = urlencode($project_address);
      $url             = "https://maps.googleapis.com/maps/api/distancematrix/json?units=imperial&origins=" . $address . "&destinations=" . $project_address . "&key=" . $googleAPI;
      $ch              = curl_init();
      $timeout         = 0;
      curl_setopt($ch, CURLOPT_URL, $url);
      curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
      curl_setopt($ch, CURLOPT_HEADER, 0);
      curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
      $data = curl_exec($ch);
      // send request and wait for response
      $response = json_decode($data, true);
      curl_close($ch);
      //dd($response,env('GOOGLE_API_KEY'),$googleAPI,env('OHIOGEOCODE_LOGIN'),env('APP_URL'));
      if (count($response)) {
        return [$response['rows'][0]['elements'][0]['distance']['text'], $response['rows'][0]['elements'][0]['duration']['text'], $response['rows'][0]['elements'][0]['duration']['value']]; // array with 10 miles, 10 hours 36 mins, and the value in seconds
      } else {
        return 0;
      }
    } else {
      return null;
    }
  }

  public function notification_preference(): HasOne
  {
    return $this->hasOne(UserNotificationPreferences::class, 'user_id', 'id');
  }

  public static function allManagers()
  {
    $user_ids = UserRole::whereIn('role_id', [3, 4, 5])->get()->pluck('user_id');
    $users    = User::whereIn('id', $user_ids)->get();
    return $users;
  }

  public function user_organizations()
  {
    return $this->hasMany(UserOrganization::class, 'user_id', 'id');
  }

  public function report_access()
  {
    return $this->hasMany(ReportAccess::class, 'user_id', 'id');
  }

  public function user_addresses()
  {
    return $this->hasMany(UserAddresses::class, 'user_id', 'id');
  }

  public function user_phone_numbers()
  {
    return $this->hasMany(UserPhoneNumber::class, 'user_id', 'id');
  }

  public function user_emails()
  {
    return $this->hasMany(UserEmail::class, 'user_id', 'id');
  }

  public function email_address()
  {
    return $this->hasOne(EmailAddress::class, 'email_address', 'email');
  }
}
