<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class QuestionController extends Controller
{
    public function store(Request $request, \App\Models\Category $category)
    {
        $validated = $request->validate([
            'type' => 'required|in:MCQ,TrueOrFalse,Identification',
            'question_text' => 'required|string',
            'choices' => 'nullable|array',
            'correct_answer' => 'nullable|string',
            'points' => 'integer|min:0',
            'bonus_points' => 'integer|min:0',
        ]);

        $questionData = [
            'type' => $validated['type'],
            'question_text' => $validated['question_text'],
            'choices' => $validated['choices'] ?? [],
        ];

        if (isset($validated['correct_answer'])) {
            $questionData['correct_answer'] = $validated['correct_answer'];
        }

        $category->questions()->create([
            'quiz_id' => $category->quiz_id,
            'question_data' => $questionData,
            'points' => $validated['points'] ?? 10,
            'bonus_points' => $validated['bonus_points'] ?? 0,
        ]);

        return redirect()->back()->with('success', 'Question created successfully.');
    }

    public function update(Request $request, \App\Models\Question $question)
    {
        $validated = $request->validate([
            'type' => 'required|in:MCQ,TrueOrFalse,Identification',
            'question_text' => 'required|string',
            'choices' => 'nullable|array',
            'correct_answer' => 'nullable|string',
            'points' => 'integer|min:0',
            'bonus_points' => 'integer|min:0',
        ]);

        $questionData = [
            'type' => $validated['type'],
            'question_text' => $validated['question_text'],
            'choices' => $validated['choices'] ?? [],
        ];

        if (isset($validated['correct_answer'])) {
            $questionData['correct_answer'] = $validated['correct_answer'];
        }

        $question->update([
            'question_data' => $questionData,
            'points' => $validated['points'] ?? 10,
            'bonus_points' => $validated['bonus_points'] ?? 0,
        ]);

        return redirect()->back()->with('success', 'Question updated successfully.');
    }

    public function destroy(\App\Models\Question $question)
    {
        $question->delete();

        return redirect()->back()->with('success', 'Question deleted successfully.');
    }
}
