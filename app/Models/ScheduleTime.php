<?php

namespace App\Models;

use Carbon;
use Event;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class ScheduleTime extends Model
{
    public $timestamps = true;
    //protected $dateFormat = 'Y-m-d G:i:s.u';

    protected $guarded = ['id'];

    public static function boot()
    {
        parent::boot();

        static::created(function ($scheduleTime) {
            Event::listen('scheduletime.created', $scheduleTime);
        });
    }

    public function auditor() : HasOne
    {
        return $this->hasOne(\App\Models\User::class, 'id', 'auditor_id');
    }

    public function audit() : HasOne
    {
        return $this->hasOne(\App\Models\Audit::class, 'id', 'audit_id');
    }

    public function day() : HasOne
    {
        return $this->hasOne(\App\Models\ScheduleDay::class, 'id', 'day_id');
    }

    public function cached_audit() : HasOne
    {
        return $this->hasOne(\App\Models\CachedAudit::class, 'audit_id', 'audit_id');
    }

    public function start_date()
    {
        // formats and combines date and time
        $date = Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $this->day->date)->format('Y-m-d');

        return $date.' '.$this->start_time;
    }

    public function end_date()
    {
        // formats and combines date and time
        $date = Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $this->day->date)->format('Y-m-d');

        return $date.' '.$this->end_time;
    }

    public function ics_link()
    {
        $day = $this->day->date;
        $cached_audit = $this->cached_audit;
        $date = Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $day)->format('F d, Y');
        $start = Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $this->start_date())->format('YmdTHis');
        $end = Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $this->end_date())->format('YmdTHis');

        $start_time = Carbon\Carbon::createFromFormat('H:i:s', $this->start_time)->format('h:i A');
        $end_time = Carbon\Carbon::createFromFormat('H:i:s', $this->end_time)->format('h:i A');

        $audit_id = $cached_audit->audit_id;
        $address = $cached_audit->address.' '.$cached_audit->city.' '.$cached_audit->state.' '.$cached_audit->zip;

        // https://tools.ietf.org/html/rfc5545#section-3.8.4.7
        $uid = md5($this->auditor_id.$this->day->date.$this->id.$cached_audit->title.$address.'@ohiohome.org');

        $mail[0] = 'BEGIN:VCALENDAR';
        $mail[1] = 'PRODID:-//Google Inc//Google Calendar 70.9054//EN';
        $mail[2] = 'VERSION:2.0';
        $mail[3] = 'CALSCALE:GREGORIAN';
        $mail[4] = 'METHOD:REQUEST';
        $mail[5] = 'BEGIN:VEVENT';
        $mail[6] = 'DTSTART;TZID=America/Sao_Paulo:'.gmdate('Ymd\THis\Z', strtotime($this->start_date()));
        $mail[7] = 'DTEND;TZID=America/Sao_Paulo:'.gmdate('Ymd\THis\Z', strtotime($this->end_date()));
        $mail[8] = 'DTSTAMP;TZID=America/Sao_Paulo:'.gmdate('Ymd\THis\Z');
        $mail[9] = 'UID:'.$uid;
        $mail[10] = 'ORGANIZER;';
        $mail[11] = 'CREATED:'.gmdate('Ymd\THis\Z');
        $mail[12] = 'DESCRIPTION:'.$this->escapeString('Assignment for audit '.$audit_id.', '.$cached_audit->title.' starting on '.$date.' from '.$start_time.' to '.$end_time);
        $mail[13] = 'LAST-MODIFIED:'.gmdate('Ymd\THis\Z');
        $mail[14] = 'LOCATION:'.$this->escapeString($address);
        $mail[15] = 'SEQUENCE:0';
        $mail[16] = 'STATUS:CONFIRMED';
        $mail[17] = 'SUMMARY:'.$this->escapeString('Assignment for audit '.$audit_id.', '.$cached_audit->title.' starting on '.$date.' from '.$start_time.' to '.$end_time);
        $mail[18] = 'TRANSP:OPAQUE';
        $mail[19] = 'END:VEVENT';
        $mail[20] = 'END:VCALENDAR';

        $mail = implode("\r\n", $mail);

        return $mail;
        /*
                $url = [
                    'BEGIN:VCALENDAR',
                    'VERSION:2.0',
                    'BEGIN:VEVENT',
                    'UID:'.$uid,
                    'SUMMARY:Assignment '.$this->escapeString($cached_audit->title),
                ];
                $url[] = 'DTSTART;TZID='.$start;
                $url[] = 'DTEND;TZID='.$end;

                $url[] = 'DESCRIPTION:'.$this->escapeString("Assignment for audit ".$audit_id.", ".$cached_audit->title." starting on ".$date." from ".$start_time." to ".$end_time);

                $url[] = 'LOCATION:'.$this->escapeString($address);

                $url[] = 'END:VEVENT';
                $url[] = 'END:VCALENDAR';
        */
        //$redirectLink = implode('%0d%0a', $url);
        //return 'data:text/calendar;charset=utf8,'.$redirectLink;
    }

    protected function escapeString(string $field): string
    {
        return addcslashes($field, "\n,;");
    }
}
