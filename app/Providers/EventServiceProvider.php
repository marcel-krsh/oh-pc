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
        'App\Events\SomeEvent' => [
            'App\Listeners\EventListener',
        ],
        'transactions.created' => [
            'App\Events\TransactionsEvent@transactionCreated'
        ],
        'transactions.updated' => [
            'App\Events\TransactionsEvent@transactionUpdated'
        ],
        'transactions.deleted' => [
            'App\Events\TransactionsEvent@transactionDeleted'
        ],
        'invoice_items.created' => [
            'App\Events\InvoiceItemsEvent@reimbursementItemCreated'
        ],
        'invoice_items.updated' => [
            'App\Events\InvoiceItemsEvent@reimbursementItemUpdated'
        ],
        'invoice_items.deleted' => [
            'App\Events\InvoiceItemsEvent@reimbursementItemDeleted'
        ],
        'disposition_items.created' => [
            'App\Events\InvoiceItemsEvent@dispositionItemCreated'
        ],
        'disposition_items.updated' => [
            'App\Events\InvoiceItemsEvent@dispositionItemUpdated'
        ],
        'disposition_items.deleted' => [
            'App\Events\InvoiceItemsEvent@dispositionItemDeleted'
        ],
        'recapture_items.created' => [
            'App\Events\InvoiceItemsEvent@recaptureItemCreated'
        ],
        'recapture_items.updated' => [
            'App\Events\InvoiceItemsEvent@recaptureItemUpdated'
        ],
        'recapture_items.deleted' => [
            'App\Events\InvoiceItemsEvent@recaptureItemDeleted'
        ]
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
