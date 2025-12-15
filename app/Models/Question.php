<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Question extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'question_data' => 'array',
        'is_selected' => 'boolean',
        'is_available' => 'boolean',
        'accepting_answers' => 'boolean',
        'is_revealed' => 'boolean',
        'timer_started_at' => 'datetime',
    ];

    public function answerHistories()
    {
        return $this->hasMany(AnswerHistory::class);
    }

    public function quiz()
    {
        return $this->belongsTo(Quiz::class);
    }
    //
}
