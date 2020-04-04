<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use Illuminate\Console\Command;
use App\Jobs\SendNotificationEmail;
use App\Mail\EmailBulkNotification;
use App\Models\NotificationsTriggered;

class SendNotificationsHourly extends Command
{
	/**
	 * The name and signature of the console command.
	 *
	 * @var string
	 */
	protected $signature = 'run:hourly_notifications';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Executes all notification every hour and inserts into email queue';

	/**
	 * Create a new command instance.
	 *
	 * @return void
	 */
	public function __construct()
	{
		parent::__construct();
	}

	/**
	 * Execute the console command.
	 *
	 * @return mixed
	 */
	public function handle()
	{
		$to = Carbon::now()->addMinutes(10);
		$from = Carbon::now()->subMinutes(10);
		$config = config('allita.notification');
		$hourley_notifications = NotificationsTriggered::with('communication')->whereBetween('deliver_time', [$from, $to])
			->with('to_user.person', 'from_user')
			->active()
			->get()
			->groupBy('to_id');
		foreach ($hourley_notifications as $hourley_notification) {
			$user = $hourley_notification->first()->to_user;
			$user_notifications = $hourley_notification->sortBy('updated_at')->groupBy('type_id');
			$token = generateToken();
			$data = [];
			foreach ($user_notifications as $key => $notification_types) {
				// if (1 == $notification_types->first()->type_id) {
				$data[$key]['notification_type'] = $config['type'][$notification_types->first()->type_id];
				foreach ($notification_types as $noti_key => $notification) {
					$data[$key]['type'][$noti_key]['heading'] = $notification->data['heading'];
					$data[$key]['type'][$noti_key]['link'] = $notification->data['base_url'] . $token;
					$data[$key]['type'][$noti_key]['message'] = $notification->data['message'];
					if (array_key_exists('project_details', $notification->data)) {
						$data[$key]['type'][$noti_key]['project_details'] = $notification->data['project_details'];
					} else {
						$data[$key]['type'][$noti_key]['project_details'] = '';
					}
					$data[$key]['type'][$noti_key]['from'] = $notification->from_user->name;
					$data[$key]['type'][$noti_key]['time'] = $notification->created_at;
					$notification->token = $token;
					$notification->active = 0;
					$notification->save();
				}
				//}
			}
			$email_notification = new EmailBulkNotification($data, $user);
			$queued_job = dispatch(new SendNotificationEmail($user, $email_notification));
		}
	}
}
