<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class QuizNextQuestion implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $quiz;

    public function __construct($quiz)
    {
        $this->quiz = $quiz;
    }

    public function broadcastOn(): array
    {
        return [
            new Channel('quiz.' . $this->quiz->id),
        ];
    }

    public function broadcastWith(): array
    {
        return [
            'quiz_id' => $this->quiz->id,
        ];
    }

    public function broadcastAs(): string
    {
        return 'next.question';
    }
}
