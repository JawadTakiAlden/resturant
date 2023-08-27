<?php

namespace App\Listeners;

use App\Events\ReadyToDeliverEvent;
use App\Jobs\SendOrderNotificationAfter30Minutes;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class ReadyOrderListener implements ShouldQueue
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
    }

    /**
     * Handle the event.
     */
    public function handle(ReadyToDeliverEvent $event): void
    {
        SendOrderNotificationAfter30Minutes::dispatch($event->order)
            ->delay(now()->addMinutes(30))->onQueue('default');
    }
}
