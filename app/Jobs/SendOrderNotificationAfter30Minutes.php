<?php

namespace App\Jobs;

use App\Events\Order30MinutesAgoEvent;
use App\Http\Resources\OrderResource;
use App\Models\Order;
use App\Models\SubOrder;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SendOrderNotificationAfter30Minutes implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public $suborder;
    public function __construct($suborder)
    {
        $this->suborder = $suborder;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $parent = Order::where('id' , $this->suborder[0]->order_id)->first();
        if ($parent['in_progress']) {
            event(new Order30MinutesAgoEvent($this->suborder));
        }
    }
}
