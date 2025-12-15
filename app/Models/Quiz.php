<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Quiz extends Model
{
    use \Illuminate\Database\Eloquent\Factories\HasFactory;

    protected $guarded = [];

    protected static function booted()
    {
        static::creating(function ($quiz) {
            $quiz->quiz_code = $quiz->quiz_code ?? \Illuminate\Support\Str::upper(\Illuminate\Support\Str::random(5));
        });
    }

    public function participants()
    {
        return $this->hasMany(QuizParticipant::class);
    }

    public function categories()
    {
        return $this->hasMany(Category::class);
    }

    public function questions()
    {
        return $this->hasMany(Question::class);
    }
}
