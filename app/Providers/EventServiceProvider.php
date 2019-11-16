<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Event;

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
            'App\Events\CommunicationsEvent@communicationCreated',
        ],
        'communication.recipient.created' => [
            'App\Events\CommunicationsEvent@communicationRecipientCreated',
        ],
        'audit.created' => [
            'App\Events\AuditsEvent@auditCreated',
        ],
        'audit.updated' => [
            'App\Events\AuditsEvent@auditUpdated',
        ],
        'audit.cache' => [
            'App\Events\AuditsEvent@createNewCachedAudit',
        ],
        'cachedaudit.created' => [
            'App\Events\CachedAuditsEvent@cachedAuditCreated',
        ],
        \App\Events\AuditorAddressEvent::class => [
            \App\Listeners\AddAuditorAddress::class,
        ],
        \App\Events\ChatEvent::class => [
            \App\Listeners\ChatListener::class,
        ],

        \App\Events\UpdateEvent::class => [
            \App\Listeners\UpdateListener::class,
        ],

        'finding.created' => [
            'App\Events\FindingsEvent@findingCreated',
        ],

        'scheduletime.created' => [
            'App\Events\SchedulesEvent@scheduleTimeCreated',
        ],

        'communication.created' => [
            'App\Events\CommunicationReceipientEvent@communicationCreated',
        ],
        'amenity.created' => [
            'App\Events\AmenityEvent@amenityUpdated',
        ],
        'amenity.deleted' => [
            'App\Events\AmenityEvent@amenityUpdated',
        ],
        'amenity.created' => [
            'App\Events\AmenityEvent@amenityUpdated',
        ],
        'amenity.updated' => [
            'App\Events\AmenityEvent@amenityUpdated',
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
