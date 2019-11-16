<?php

namespace App\Mail;

use App\Models\HistoricEmail;
use App\Models\ScheduleDays;
use App\Models\ScheduleTime;
use App\Models\User;
use Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

/**
 * EmailScheduleInvitation.
 *
 * @category Mail
 * @license  Proprietary and confidential
 */
class EmailScheduleInvitation extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * @var mixed Owner
     */
    public $owner;

    /**
     * @var mixed ScheduleTime
     */
    public $schedule_time;

    /**
     * @var mixed ics_link
     */
    public $ics_link;

    /**
     * EmailNotification constructor.
     *
     * @param int  $recipient_id
     * @param null $message_id
     */
    public function __construct($recipient_id, $schedule_time, $ics_link = '')
    {
        $this->schedule_time = $schedule_time;
        $this->owner = User::where('id', '=', $recipient_id)->first();
        $this->user = $this->owner;
        $this->ics_link = $ics_link;
        $this->subject = '[OHFA Allita PC] You have a new assignment';
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $schedule_time = $this->schedule_time;
        $cached_audit = $schedule_time->cached_audit;
        $day = $schedule_time->day->date;

        $owner = $this->owner;
        $greeting = 'A new assignment has been scheduled.';

        $date = Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $day)->format('F d, Y');
        $start_time = Carbon\Carbon::createFromFormat('H:i:s', $schedule_time->start_time)->format('h:i A');
        $end_time = Carbon\Carbon::createFromFormat('H:i:s', $schedule_time->end_time)->format('h:i A');
        $audit_id = $cached_audit->audit_id;
        $address = $cached_audit->formatted_address('simple');

        $introLines[] = 'You are receiving this notification because you have a new assignment for '.$date.', from '.$start_time.' to '.$end_time.'.';

        $introLines[] = 'This assignment is for audit '.$audit_id.' :';
        $introLines[] = $cached_audit->title;
        $introLines[] = $address;

        //header("text/calendar");
        $filename = 'invite.ics';
        // file_put_contents($filename, $this->ics_link);

        $actionText = '';
        $actionUrl = '';
        // $this->ics_link;
        // data:text/calendar;charset=utf8,BEGIN:VCALENDAR%0d%0aVERSION:2.0%0d%0aBEGIN:VEVENT%0d%0aUID:0fb5ca321f6a2703720c9358033f9d18%0d%0aSUMMARY:Assignment%20Abigail%20Apartments%0d%0aDTSTART;TZID=20190212EST094500%0d%0aDTEND;TZID=20190212EST180000%0d%0aDESCRIPTION:Assignment%20for%20audit%20#%20:%206410%5C,%20Abigail%20Apartments%20starting%20on%20February%2012%5C,%202019%20from%2009:45%20AM%20to%2006:00%20PM%0d%0aLOCATION:945%20Findlay%20Street%20Cincinnati%20OH%2045214%0d%0aEND:VEVENT%0d%0aEND:VCALENDAR
        $level = 'success';
        $level2 = 'error';
        $outroLines = [];

        //clear session vars.
        // session(['ownerId'=>"",'newUserId' => ""]);

        // save in database
        if ($owner) {
            $body = \view('emails.send_communication', compact('greeting', 'introLines', 'actionUrl', 'actionText', 'level', 'outroLines', 'actionText2', 'actionUrl2', 'level2'));
            $email_saved_in_db = new  HistoricEmail([
                'user_id' => $owner->id,
                'type' => 'schedule_time',
                'type_id' => $schedule_time->id,
                'subject' => $this->subject,
                'body' => $body,
            ]);
            $email_saved_in_db->save();
        }

        return $this->view('emails.send_communication', compact('greeting', 'introLines', 'actionUrl', 'actionText', 'level', 'outroLines', 'actionText2', 'actionUrl2', 'level2'))
                    ->attachData($this->ics_link, $filename, [
                        'mime' => 'text/calendar',
                    ]);
    }
}
