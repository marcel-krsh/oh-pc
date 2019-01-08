<?php

namespace App\Providers;

use Illuminate\Support\Facades\Event;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        \App\Events\SomeEvent::class => [
            \App\Listeners\EventListener::class,
        ],
        'communications.created' => [
            'App\Events\CommunicationsEvent@communicationCreated'
        ],
        'communication.recipient.created' => [
            'App\Events\CommunicationsEvent@communicationRecipientCreated'
        ],
        'audit.created' => [
            'App\Events\AuditsEvent@auditCreated'
        ],
        'audit.updated' => [
            'App\Events\AuditsEvent@auditUpdated'
        ],
        'cachedaudit.created' => [
            'App\Events\CachedAuditsEvent@cachedAuditCreated'
        ],
        'App\Events\MessageSent' => [
            'App\Listeners\SendChatMessage',
        ],
        'App\Events\ChatEvent' => [
            'App\Listeners\ChatListener',
        ],
        'App\Events\AuditBroadcast' => [
            'App\Listeners\AuditBroadcastListener',
        ],
        'App\Events\CommunicationBroadcastEvent' => [
            'App\Listeners\CommunicationBroadcastListener',
        ],
        'App\Events\ReportBroadcastEvent' => [
            'App\Listeners\ReportBroadcastListener',
        ],

    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        parent::boot();

        //
    }
}
