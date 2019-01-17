<?php
// General helpers

use App\Mail\EmailSystemAdmin;
use App\DocumentCategory;


function formatDate($date, $format="F d, Y", $from_format="Y-m-d H:i:s")
{
    return Carbon\Carbon::createFromFormat($from_format , $date)->format($format);
}

function formatTime($time, $format="g:i A", $from_format="H:i:s")
{
    return Carbon\Carbon::createFromFormat($from_format , $time)->format($format);
}

function timeToSlot($time) 
{
    $hour =  \Carbon\Carbon::createFromFormat('H:i:s', $time)->format('H');
    $min =  \Carbon\Carbon::createFromFormat('H:i:s', $time)->format('i');
    return ($hour - 6)*4 + 1 + $min / 15;
}

function slotToTime($slot) 
{
    $hours = sprintf("%02d",  floor(($slot - 1) * 15 / 60) + 6);
    $minutes = sprintf("%02d", ($slot - 1) * 15 % 60);
    return formatTime($hours.':'.$minutes.':00');
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
    } elseif ($bytes == 1) {
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
    $ext  = end($tmp);
    $ext  = strtoupper($ext);
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
    $admins = ['jotassin@gmail.com','brian@greenwood360.com'];

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
