<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class QuizTimerStarted implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $quiz;
    public $question;
    public $duration;
    public $timerEndsAt;

    public function __construct($quiz, $question, $duration)
    {
        $this->quiz = $quiz;
        $this->question = $question;
        $this->duration = $duration;
        $this->timerEndsAt = now()->addSeconds($duration)->toIso8601String();
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
            'question' => $this->question,
            'duration' => $this->duration,
            'timer_ends_at' => $this->timerEndsAt,
        ];
    }

    public function broadcastAs(): string
    {
        return 'timer.started';
    }
}
