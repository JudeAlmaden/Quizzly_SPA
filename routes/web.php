<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

Route::get('/dashboard', [\App\Http\Controllers\QuizController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::post('/quizzes', [\App\Http\Controllers\QuizController::class, 'store'])->name('quiz.store');
    Route::post('/quizzes/join', [\App\Http\Controllers\QuizController::class, 'join'])->name('quiz.join');
    Route::get('/quizzes/{quiz}/play', [\App\Http\Controllers\QuizController::class, 'play'])->name('quiz.play'); // This is the Dashboard
    
    // Quiz Admin Routes
    Route::prefix('/quizzes/{quiz}')->group(function () {
        Route::get('/rankings', [\App\Http\Controllers\QuizController::class, 'rankings'])->name('quiz.rankings');
        Route::get('/questions', [\App\Http\Controllers\QuizController::class, 'questions'])->name('quiz.questions');
        Route::get('/game', [\App\Http\Controllers\QuizController::class, 'game'])->name('quiz.game');
        Route::get('/history', [\App\Http\Controllers\QuizController::class, 'history'])->name('quiz.history');
        Route::get('/history/{question}', [\App\Http\Controllers\QuizController::class, 'historyQuestion'])->name('quiz.history.question');
        Route::get('/participants', [\App\Http\Controllers\QuizController::class, 'participants'])->name('quiz.participants');
        
        // Category routes
        Route::post('/categories', [\App\Http\Controllers\CategoryController::class, 'store'])->name('categories.store');
    });
    
    Route::delete('/categories/{category}', [\App\Http\Controllers\CategoryController::class, 'destroy'])->name('categories.destroy');
    
    // Question routes
    Route::post('/categories/{category}/questions', [\App\Http\Controllers\QuestionController::class, 'store'])->name('questions.store');
    Route::put('/questions/{question}', [\App\Http\Controllers\QuestionController::class, 'update'])->name('questions.update');
    Route::delete('/questions/{question}', [\App\Http\Controllers\QuestionController::class, 'destroy'])->name('questions.destroy');
    
    // Participant management
    Route::put('/participants/{participant}/status', [\App\Http\Controllers\QuizController::class, 'updateParticipantStatus'])->name('participants.updateStatus');
    
    // Game control routes
    Route::post('/quizzes/{quiz}/select-question', [\App\Http\Controllers\QuizController::class, 'selectQuestion'])->name('game.selectQuestion');
    Route::post('/questions/{question}/start-timer', [\App\Http\Controllers\QuizController::class, 'startTimer'])->name('game.startTimer');
    Route::post('/questions/{question}/submit', [\App\Http\Controllers\QuizController::class, 'submitAnswer'])->name('game.submitAnswer');
    Route::post('/questions/{question}/reveal', [\App\Http\Controllers\QuizController::class, 'revealAnswer'])->name('game.revealAnswer');
    Route::post('/quizzes/{quiz}/next-question', [\App\Http\Controllers\QuizController::class, 'nextQuestion'])->name('game.nextQuestion');
    Route::post('/answer/{answer}/toggle', [\App\Http\Controllers\QuizController::class, 'toggleAnswerStatus'])->name('answer.toggle');
});

require __DIR__.'/auth.php';
