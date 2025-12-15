<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Quiz;
use App\Models\Category;
use App\Models\Question;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Create Users
        $user1 = User::factory()->create([
            'name' => 'Jude Almaden 2045',
            'email' => 'judealmaden2045@gmail.com',
            'password' => bcrypt('password123'),
        ]);

        $user2 = User::factory()->create([
            'name' => 'Jude Almaden 2046',
            'email' => 'judealmaden2046@gmail.com',
            'password' => bcrypt('password123'),
        ]);

        $user3 = User::factory()->create([
            'name' => 'Random User',
            'email' => 'random@example.com',
            'password' => bcrypt('password123'),
        ]);

        // 2. Create Sample Quiz
        $quiz = Quiz::create([
            'name' => 'General Knowledge Quiz',
            'description' => 'A fun quiz to test your general knowledge.',
            'creator_id' => $user1->id,
            'quiz_code' => 'ABCDE',
        ]);

        // 3. Create Categories
        $scienceCategory = Category::create([
            'name' => 'Science',
            'quiz_id' => $quiz->id,
        ]);

        $historyCategory = Category::create([
            'name' => 'History',
            'quiz_id' => $quiz->id,
        ]);

        // 4. Create Questions for Science
        Question::create([
            'category_id' => $scienceCategory->id,
            'quiz_id' => $quiz->id,
            'question_data' => [
                'type' => 'MCQ',
                'question_text' => 'What is the chemical symbol for Gold?',
                'choices' => [
                    ['id' => 1, 'text' => 'Au', 'is_correct' => true],
                    ['id' => 2, 'text' => 'Ag', 'is_correct' => false],
                    ['id' => 3, 'text' => 'Fe', 'is_correct' => false],
                    ['id' => 4, 'text' => 'Pb', 'is_correct' => false],
                ],
            ],
            'points' => 10,
        ]);
        
        Question::create([
            'category_id' => $scienceCategory->id,
            'quiz_id' => $quiz->id,
            'question_data' => [
                'type' => 'TrueOrFalse',
                'question_text' => 'Water boils at 100 degrees Celsius.',
                'choices' => [
                    ['id' => 1, 'text' => 'True', 'is_correct' => true],
                    ['id' => 2, 'text' => 'False', 'is_correct' => false],
                ],
            ],
            'points' => 5,
        ]);

        // 5. Create Questions for History
        Question::create([
            'category_id' => $historyCategory->id,
            'quiz_id' => $quiz->id,
            'question_data' => [
                'type' => 'Identification',
                'question_text' => 'Who was the first President of the United States?',
                'choices' => [], // Identification type might not have choices or handled differently
                'correct_answer' => 'George Washington',
            ],
            'points' => 15,
            'bonus_points' => 5,
        ]);

        // 6. Register Participants (optional, but good for testing)
        // Let's say user 2 is a participant
        \App\Models\QuizParticipant::create([
            'quiz_id' => $quiz->id,
            'user_id' => $user2->id,
            'status' => 'joined', // or 'active'
        ]);
    }
}
