<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class QuizController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $quizzes = \App\Models\Quiz::where('creator_id', $user->id)
            ->orWhereHas('participants', function ($query) use ($user) {
                $query->where('user_id', $user->id);
            })
            ->with(['participants' => function($query) use ($user) {
                $query->where('user_id', $user->id);
            }])
            ->latest()
            ->get();

        return \Inertia\Inertia::render('Dashboard', [
            'quizzes' => $quizzes,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        $quiz = \App\Models\Quiz::create([
            ...$validated,
            'creator_id' => auth()->id(),
        ]);

        return redirect()->back()->with('success', 'Quiz created successfully!');
    }

    public function join(Request $request)
    {
        $validated = $request->validate([
            'code' => 'required|string|size:5|exists:quizzes,quiz_code',
        ]);

        $quiz = \App\Models\Quiz::where('quiz_code', $validated['code'])->firstOrFail();
        $user = auth()->user();

        // Check if already a participant or creator
        if ($quiz->creator_id !== $user->id && !$quiz->participants()->where('user_id', $user->id)->exists()) {
             $quiz->participants()->create([
                'quiz_id' => $quiz->id,
                'user_id' => $user->id,
                'status' => 'pending',
            ]);
        }

        return redirect()->back()->with('success', 'Join request sent! Waiting for approval.');
    }

    public function play(\App\Models\Quiz $quiz)
    {
        $isAdmin = auth()->id() === $quiz->creator_id;
        
        // Admins go to Quiz Dashboard
        if ($isAdmin) {
            return \Inertia\Inertia::render('QuizAdmin/QuizDashboard', [
                'quiz' => $quiz,
            ]);
        }
        
        // Participants go to Game view
        $selectedQuestion = \App\Models\Question::where('quiz_id', $quiz->id)
            ->where('is_selected', true)
            ->first();
        
        $remainingTime = null;
        if ($selectedQuestion && $selectedQuestion->timer_started_at && $selectedQuestion->timer_duration) {
            $elapsed = now()->diffInSeconds($selectedQuestion->timer_started_at, false);
            $remainingTime = max(0, $selectedQuestion->timer_duration - abs($elapsed));
            
            // Auto-close if time is up
            if ($remainingTime <= 0 && $selectedQuestion->accepting_answers) {
                $selectedQuestion->update(['accepting_answers' => false]);
            }
        }
            
        $hasAnswered = false;
        $userAnswer = null;
        if ($selectedQuestion) {
             $history = \App\Models\AnswerHistory::where('question_id', $selectedQuestion->id)
                ->where('user_id', auth()->id())
                ->first();
             if ($history) {
                 $hasAnswered = true;
                 $userAnswer = $history->answer;
             }
        }

        return \Inertia\Inertia::render('Game', [
            'quiz' => $quiz,
            'selectedQuestion' => $selectedQuestion,
            'categories' => [],
            'remainingTime' => $remainingTime,
            'isAdmin' => false,
            'hasAnswered' => $hasAnswered,
            'userAnswer' => $userAnswer,
        ]);
    }

    public function rankings(\App\Models\Quiz $quiz)
    {
        // Get all answer histories for this quiz, grouped by user
        $rankings = \App\Models\AnswerHistory::where('quiz_id', $quiz->id)
            ->with('user')
            ->selectRaw('user_id, SUM(points) as total_points, COUNT(*) as answer_count')
            ->groupBy('user_id')
            ->orderByDesc('total_points')
            ->get()
            ->map(function ($ranking) {
                return [
                    'user' => $ranking->user,
                    'total_points' => $ranking->total_points,
                    'answer_count' => $ranking->answer_count,
                ];
            });

        return \Inertia\Inertia::render('QuizAdmin/Rankings', [
            'quiz' => $quiz,
            'rankings' => $rankings,
        ]);
    }

    public function questions(\App\Models\Quiz $quiz)
    {
        $categories = $quiz->categories()->with('questions')->get();
        
        return \Inertia\Inertia::render('QuizAdmin/Questions', [
            'quiz' => $quiz,
            'categories' => $categories,
        ]);
    }

    public function game(\App\Models\Quiz $quiz)
    {
        $selectedQuestion = \App\Models\Question::where('quiz_id', $quiz->id)
            ->where('is_selected', true)
            ->first();
        
        $categories = $quiz->categories()->withCount(['questions' => function($query) {
            $query->where('is_available', false); // Count unasked questions
        }])->get();
        
        $remainingTime = null;
        if ($selectedQuestion && $selectedQuestion->timer_started_at && $selectedQuestion->timer_duration) {
            $elapsed = now()->diffInSeconds($selectedQuestion->timer_started_at, false);
            $remainingTime = max(0, $selectedQuestion->timer_duration - abs($elapsed));
            
            // Auto-close if time is up
            if ($remainingTime <= 0 && $selectedQuestion->accepting_answers) {
                $selectedQuestion->update(['accepting_answers' => false]);
            }
        }
        
        $hasAnswered = false;
        $userAnswer = null;
        if ($selectedQuestion) {
             $history = \App\Models\AnswerHistory::where('question_id', $selectedQuestion->id)
                ->where('user_id', auth()->id())
                ->first();
             if ($history) {
                 $hasAnswered = true;
                 $userAnswer = $history->answer;
             }
        }
            
        return \Inertia\Inertia::render('Game', [
            'quiz' => $quiz,
            'selectedQuestion' => $selectedQuestion,
            'categories' => $categories,
            'remainingTime' => $remainingTime,
            'isAdmin' => auth()->id() === $quiz->creator_id,
            'hasAnswered' => $hasAnswered,
            'userAnswer' => $userAnswer,
        ]);
    }

    public function history(\App\Models\Quiz $quiz)
    {
        $questions = $quiz->questions()
            ->where('is_available', true)
            ->withCount('answerHistories')
            ->orderBy('updated_at', 'desc')
            ->get();

        return \Inertia\Inertia::render('QuizAdmin/History', [
            'quiz' => $quiz,
            'questions' => $questions,
        ]);
    }

    public function historyQuestion(\App\Models\Quiz $quiz, \App\Models\Question $question)
    {
        $history = $question->answerHistories()
            ->with('user')
            ->orderBy('created_at', 'asc')
            ->get();

        return \Inertia\Inertia::render('QuizAdmin/HistoryQuestion', [
            'quiz' => $quiz,
            'question' => $question,
            'history' => $history,
        ]);
    }

    public function toggleAnswerStatus(\App\Models\AnswerHistory $answer)
    {
        // Toggle correctness
        $answer->is_correct = !$answer->is_correct;
        $answer->save();

        // Recalculate points for all answers to this question
        $this->recalculatePoints($answer->question);

        return redirect()->back();
    }

    private function recalculatePoints($question)
    {
        // Get all correct answers sorted by submission time
        $correctAnswers = $question->answerHistories()
            ->where('is_correct', true)
            ->orderBy('created_at', 'asc')
            ->get();
        
        // Assign points with bonus for first correct answer
        foreach ($correctAnswers as $index => $history) {
            $points = $question->points;
            if ($index === 0 && $question->bonus_points > 0) {
                $points += $question->bonus_points;
            }
            $history->update(['points' => $points]);
        }

        // Set incorrect answers to 0 points
        $question->answerHistories()
            ->where('is_correct', false)
            ->update(['points' => 0]);
    }

    public function participants(\App\Models\Quiz $quiz)
    {
        $participants = $quiz->participants()->with('user')->get();
        
        return \Inertia\Inertia::render('QuizAdmin/Participants', [
            'quiz' => $quiz,
            'participants' => $participants,
        ]);
    }

    public function updateParticipantStatus(\App\Models\QuizParticipant $participant, Request $request)
    {
        $validated = $request->validate([
            'status' => 'required|in:joined,pending,rejected',
        ]);

        $participant->update(['status' => $validated['status']]);

        return redirect()->back()->with('success', 'Participant status updated.');
    }

    public function selectQuestion(Request $request, \App\Models\Quiz $quiz)
    {
        $validated = $request->validate([
            'category_id' => 'required|exists:categories,id',
        ]);

        // Unselect all questions first
        \App\Models\Question::where('quiz_id', $quiz->id)->update([
            'is_selected' => false,
            'accepting_answers' => false,
            'is_revealed' => false,
            'timer_started_at' => null,
            'timer_duration' => null,
        ]);

        // Select a random unasked question from the category
        $question = \App\Models\Question::where('quiz_id', $quiz->id)
            ->where('category_id', $validated['category_id'])
            ->where('is_available', false) // Not yet asked
            ->inRandomOrder()
            ->first();

        if ($question) {
            $question->update([
                'is_selected' => true,
                'is_available' => true, // Mark as asked
            ]);

            broadcast(new \App\Events\QuizQuestionSelected($quiz, $question))->toOthers();

            return redirect()->back()->with('success', 'Question selected!');
        }

        return redirect()->back()->with('error', 'No available questions in this category.');
    }

    public function startTimer(Request $request, \App\Models\Question $question)
    {
        $validated = $request->validate([
            'duration' => 'required|integer|min:5|max:300',
        ]);

        $question->update([
            'timer_started_at' => now(),
            'timer_duration' => $validated['duration'],
            'accepting_answers' => true,
        ]);

        $quiz = \App\Models\Quiz::find($question->quiz_id);
        broadcast(new \App\Events\QuizTimerStarted($quiz, $question, $validated['duration']));

        return redirect()->back()->with('success', 'Timer started!');
    }

    public function revealAnswer(\App\Models\Question $question)
    {
        $question->update([
            'is_revealed' => true,
            'accepting_answers' => false,
        ]);

        $quiz = \App\Models\Quiz::find($question->quiz_id);
        broadcast(new \App\Events\QuizAnswerRevealed($quiz, $question));

        return redirect()->back()->with('success', 'Answer revealed!');
    }

    public function nextQuestion(\App\Models\Quiz $quiz)
    {
        // Unselect current question
        \App\Models\Question::where('quiz_id', $quiz->id)
            ->where('is_selected', true)
            ->update([
                'is_selected' => false,
                'accepting_answers' => false,
                'timer_started_at' => null,
                'timer_duration' => null,
            ]);

        broadcast(new \App\Events\QuizNextQuestion($quiz));

        return redirect()->back()->with('success', 'Ready for next question!');
    }

    public function submitAnswer(Request $request, \App\Models\Question $question)
    {
        // Basic validation
        $validated = $request->validate([
            'answer' => 'required',
        ]);

        $user = auth()->user();
        if (!$question->accepting_answers || $question->is_revealed) {
             return redirect()->back()->with('error', 'Time is up or answering is closed.');
        }

        // Logic to store answer and calculate score
        // We need to assume a 'responses' table/model exists
        // Check if user already answered
        $existing = \App\Models\AnswerHistory::where('question_id', $question->id)
            ->where('user_id', $user->id)
            ->first();

        if ($existing) {
             return redirect()->back()->with('message', 'Answer already submitted.');
        }
        
        $isCorrect = false;
        $points = 0;

        if ($question->question_data['type'] === 'MCQ' || $question->question_data['type'] === 'TrueOrFalse') {
             // Find choice
             $choices = collect($question->question_data['choices']);
             $selectedChoice = $choices->where('id', $validated['answer'])->first();
             if ($selectedChoice && isset($selectedChoice['is_correct']) && $selectedChoice['is_correct']) {
                 $isCorrect = true;
             }
        } elseif ($question->question_data['type'] === 'Identification') {
             // Compare text (case insensitive)
             if (strcasecmp(trim($validated['answer']), trim($question->question_data['correct_answer'])) === 0) {
                 $isCorrect = true;
             }
        }

        if ($isCorrect) {
            $points = $question->points;
        }

        \App\Models\AnswerHistory::create([
            'quiz_id' => $question->quiz_id,
            'question_id' => $question->id,
            'user_id' => $user->id,
            'answer' => $validated['answer'],
            'is_correct' => $isCorrect,
            'points' => $points,
        ]);

        return redirect()->back()->with('success', 'Answer submitted!');
    }
}
