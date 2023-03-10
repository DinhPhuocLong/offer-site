<?php
namespace App\Events;
use Illuminate\Queue\SerializesModels;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class NewNotify implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $notify;

    public function __construct($notify)
    {
        $this->notify = $notify;
    }

    public function broadcastOn()
    {
        return ['site-channel'];
    }

    public function broadcastAs()
    {
        return 'site-event';
    }
}
