<?php

/**
 * System Message
 *
 * @param        $message
 * @param string $level
 */
function systemMessage($message, $level = 'info')
{
    session()->flash('systemMessage', $message);
    session()->flash('systemMessageLevel', $level);
}

/**
 * User Initials
 *
 * @param $name
 *
 * @return string
 */
function userInitials($name)
{
    if (strlen($name)>=3) {
        $first =  substr($name, 0, 1);
        $secondPosition = strpos($name, ' ')+1;
        $second = substr($name, $secondPosition, 1);
        $thirdPosition = strpos($name, ' ', $secondPosition)+1;
        if ($thirdPosition > ($secondPosition+1)) {
            $second .= substr($name, $thirdPosition, 1);
        }
    } else {
        $first='N';
        $second='A';
    }

    return $first.$second;
}

/**
 * Next Quarter
 *
 * @param $date
 *
 * @return false|string
 */
function nextQuarter($date)
{
    $currentQuarterStartMonth = ceil(date('n', strtotime($date)) / 3)*3;
    $year = date('Y', strtotime($date));
    $currentQuarterDate = $currentQuarterStartMonth.'/15/'.$year;
    $nextQuarterDate = date('m/d/Y', strtotime("+3 months", strtotime($currentQuarterDate)));

    return $nextQuarterDate;
}
