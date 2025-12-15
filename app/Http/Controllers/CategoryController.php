<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function store(Request $request, \App\Models\Quiz $quiz)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $quiz->categories()->create($validated);

        return redirect()->back()->with('success', 'Category created successfully.');
    }

    public function destroy(\App\Models\Category $category)
    {
        $category->questions()->delete();
        $category->delete();

        return redirect()->back()->with('success', 'Category deleted successfully.');
    }
}
