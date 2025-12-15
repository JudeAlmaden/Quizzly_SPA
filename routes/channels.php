<?php

use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

Broadcast::channel('quiz.{quizId}', function ($user, $quizId) {
    return \App\Models\Quiz::where('id', $quizId)
        ->where(function ($query) use ($user) {
            $query->where('creator_id', $user->id) // Allow creator
                  ->orWhereHas('participants', function ($q) use ($user) {
                      $q->where('user_id', $user->id); // Allow participants
                  });
        })->exists();
});
