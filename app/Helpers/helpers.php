<?php
// General helpers

use Carbon\Carbon;
use App\DocumentCategory;
use Illuminate\Support\Str;
use App\Mail\EmailSystemAdmin;

function formatDate($date, $format = "F d, Y", $from_format = "Y-m-d H:i:s")
{
	return Carbon::createFromFormat($from_format, $date)->format($format);
}

function formatTime($time, $format = "g:i A", $from_format = "H:i:s")
{
	return Carbon::createFromFormat($from_format, $time)->format($format);
}

function timeToSlot($time)
{
	$hour = Carbon::createFromFormat('H:i:s', $time)->format('H');
	$min = Carbon::createFromFormat('H:i:s', $time)->format('i');
	return ($hour - 6) * 4 + 1 + $min / 15;
}

function slotToTime($slot)
{
	$hours = sprintf("%02d", floor(($slot - 1) * 15 / 60) + 6);
	$minutes = sprintf("%02d", ($slot - 1) * 15 % 60);
	return formatTime($hours . ':' . $minutes . ':00');
}

/**
 * Format Size Units
 *
 * @param $bytes
 *
 * @return string
 */
function formatSizeUnits($bytes)
{
	if ($bytes >= 1073741824) {
		$bytes = number_format($bytes / 1073741824, 2) . ' GB';
	} elseif ($bytes >= 1048576) {
		$bytes = number_format($bytes / 1048576, 2) . ' MB';
	} elseif ($bytes >= 1024) {
		$bytes = number_format($bytes / 1024, 2) . ' KB';
	} elseif ($bytes > 1) {
		$bytes = $bytes . ' bytes';
	} elseif (1 == $bytes) {
		$bytes = $bytes . ' byte';
	} else {
		$bytes = '0 bytes';
	}

	return $bytes;
}

/**
 * Check File Name
 *
 * @param $fn
 *
 * @return bool
 */
function check_file_name($fn)
{
	// ALLOWABLE CHARACTERS PASS THE REGULAR EXPRESSION
	$new = preg_replace('#[^A-Z0-9_\-.]#i', null, $fn);
	if ($new != $fn) {
		return false;
	}

	// ALLOWABLE EXTENSIONS
	$exts = ['PNG', 'GIF', 'JPG', 'JPEG', 'XLS', 'XLSX'];
	$tmp = explode('.', $fn);
	$ext = end($tmp);
	$ext = strtoupper($ext);
	if (!in_array($ext, $exts)) {
		return false;
	}

	return true;
}

/**
 * Email Admins
 *
 * email only a few admins to help monitor and debug
 *
 * @param string $message
 * @param string $where
 *
 * @return bool
 */
function email_admins($message = '', $where = '')
{
	$admins = ['jotassin@gmail.com', 'brian@greenwood360.com'];

	$emailNotification = new EmailSystemAdmin($message, $where);
	\Mail::to($admins)->send($emailNotification);

	return true;
}

/**
 * Document Category Name
 *
 * get document category name from id
 *
 * @param null $id
 *
 * @return null
 */
function document_category_name($id = null)
{
	$category = DocumentCategory::where('id', '=', $id)->first();
	if ($category) {
		return $category->document_category_name;
	} else {
		return null;
	}
}

/**
 * System messages
 *
 * Send users system messages for various reasons
 *
 * @param string $message
 * @param string $action_link
 * @param string $action_text
 * @param integer $recipient_id
 *
 * @return bool
 */
function system_message($message, $action_link = null, $action_text = null, $recipient_id)
{
	// $admins = ['jotassin@gmail.com','brian@greenwood360.com'];

	// $emailNotification = new EmailSystemAdmin($message, $where);
	// \Mail::to($admins)->send($emailNotification);

	return true;
}

function camelCase($str, array $noStrip = [])
{
	// non-alpha and non-numeric characters become spaces
	$str = preg_replace('/[^a-z0-9' . implode("", $noStrip) . ']+/i', ' ', $str);
	$str = trim($str);
	// uppercase the first character of each word
	$str = ucwords(strtolower($str));
	$str = str_replace(" ", "", $str);
	$str = lcfirst($str);

	return $str;
}

function alpha_numeric_random($length = 16)
{
	$pool = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
	return substr(str_shuffle(str_repeat($pool, $length)), 0, $length);
}

function mask_email($email)
{
	$mail_segments = explode("@", $email);
	$mail_segments[0] = substr($mail_segments[0], 0, 1) . str_repeat("*", strlen($mail_segments[0]) - 2) . substr($mail_segments[0], -1);
	$pos = strpos($mail_segments[1], '.');
	$mail_segments[1] = substr($mail_segments[1], 0, 1) . str_repeat("*", strlen($mail_segments[1]) - $pos + 1) . substr($mail_segments[1], $pos - 1);
	return implode("@", $mail_segments);
}

function mask_phone_number($phone)
{
	return substr($phone, 0, 2) . '******' . substr($phone, -2);
}

function generateToken($characters = 128)
{
	return Str::random($characters); // str_random($characters);
}

function closestNextHour($timestamp = null)
{
	if (is_null($timestamp)) {
		$timestamp = Carbon::now();
	}
	$next_hour = Carbon::parse($timestamp)->addHour();
	$start_of_hour = $next_hour->startOfHour();
	return $start_of_hour;
}

function notificationDeliverTime($deliver_hour = '17:00:00')
{
	$now = Carbon::now();
	if ($now->toTimeString() >= $deliver_hour) {
		//nextday
		$next_day = $now->addDay();
		$next_date = $next_day->toDateString();
		$delivert_time = $next_date . ' ' . $deliver_hour;
	} else {
		//today
		$today = $now->toDateString();
		$delivert_time = $today . ' ' . $deliver_hour;
	}
	return $delivert_time;
}

function local()
{
	if (env('APP_ENV') == 'local') {
		return true;
	}
	return false;
}

function asset_version()
{
	$version = '1.0';
	if (env('ASSET_VERSION')) {
		$version = env('ASSET_VERSION');
	}
	return '?v=' . $version;
}

function modal_confirm($request)
{
	$hide_confirm_modal = $request->hide_confirm_modal;
	if ($hide_confirm_modal == 'true') {
		$request->session()->put('hide_confirm_modal', true);
		\Session::save();
		//session(['hide_confirm_modal' => true]);
	}
}

function milliseconds_mutator($value)
{
	try {
		if (!is_null($value)) {
			return Carbon::createFromFormat('Y-m-d H:i:s.u', $value);
		} else {
			return null;
		}
	} catch (\Exception $e) {
		return $value;
	}
}

function snake_case($string)
{
	Str::snake($string);
}
