<?php
namespace App\Models;

use App\Mail\EmailVerificationCode;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Mail;
use Twilio;

class Token extends Model
{
  const EXPIRATION_TIME = 15; // minutes

  protected $fillable = [
    'code',
    'user_id',
    'used',
  ];

  public function __construct(array $attributes = [])
  {
    if (!isset($attributes['code'])) {
      $attributes['code'] = $this->generateCode();
    }
    parent::__construct($attributes);
  }

  /**
   * Generate a six digits code
   *
   * @param int $codeLength
   * @return string
   */
  public function generateCode($codeLength = 9)
  {
    $num  = mt_rand(100, 999);
    $code = $num;
    $num  = mt_rand(100, 999);
    $code = $code . '-' . $num;
    $num  = mt_rand(100, 999);
    $code = $code . '-' . $num;
    return $code;
  }

  /**
   * User tokens relation
   *
   * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
   */
  public function user()
  {
    return $this->belongsTo(User::class);
  }

  /**
   * True if the token is not used nor expired
   *
   * @return bool
   */
  public function isValid()
  {
    return (!$this->isUsed() && !$this->isExpired());
  }

  /**
   * Is the current token used
   *
   * @return bool
   */
  public function isUsed()
  {
    return $this->used;
  }

  public function scopeNotused($query)
  {
    return $query->where('used', 0);
  }

  /**
   * Is the current token expired
   *
   * @return bool
   */
  public function isExpired()
  {
    return $this->created_at->diffInMinutes(Carbon::now()) > static::EXPIRATION_TIME;
  }

  public function sendCode($mode_of_communication = "email", $phone_number = null)
  {
    if (!$this->user) {
      throw new \Exception("No user attached to this token.");
    }
    if (!$this->code) {
      $this->code = $this->generateCode();
    }
    if ("email" == $mode_of_communication) {
      return $this->sendCodeByEmail($this->user, $this->code);
    } elseif ("sms" == $mode_of_communication) {
      return $this->sendCodeBySms($phone_number, $this->code);
    } elseif ("voice" == $mode_of_communication) {
      return $this->sendCodeByVoice($phone_number, $this->code);
    }
  }

  public function sendCodeByEmail($user, $code)
  {
    $data['email'] = $user->email;
    $data['code']  = $code;
    try {
      $email_template = new EmailVerificationCode($user, $code);
      \Mail::to($user->email)->send($email_template);
      return true;
    } catch (\Exception $ex) {
      return false; //enable to send email }
    }
  }

  public function sendCodeByVoice($to_number, $code, $loop = 3)
  {
    //slow down the voice for number
    $code_text       = (string) (str_replace('-', '', $code)); // convert into a string
    $code_text       = str_split($code_text, "1"); // break string in 3 character sets
    $code_voice_text = implode(". ", $code_text);
    $message         = "Your verification code is: " . $code_voice_text . '. ';
    try {
      Twilio::call($to_number, function ($msg) use ($message, $loop) {
        $msg->say($message, ['loop' => $loop]);
      });
      return true;
    } catch (\Exception $ex) {
      return false; //enable to send sms
    }
  }

  public function sendCodeBySms($to_number, $code)
  {
    $message = "Your verification code is: " . $code;
    try {
      Twilio::message($to_number, $message);
      return true;
    } catch (\Exception $ex) {
      return false; //enable to send sms
    }
  }
}
