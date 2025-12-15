<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class QuizQuestionSelected implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $quiz;
    public $question;

    public function __construct($quiz, $question)
    {
        $this->quiz = $quiz;
        $this->question = $question;
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
        ];
    }

    public function broadcastAs(): string
    {
        return 'question.selected';
    }
}
