<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Quiz;
use App\Models\Category;
use App\Models\Question;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\QuizParticipant;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Create Users
        $admin = User::firstOrCreate(
            ['email' => 'judealmaden2045@gmail.com'],
            [
                'name' => 'Jude Almaden',
                'password' => Hash::make('password123'),
            ]
        );

        $user2 = User::firstOrCreate(
            ['email' => 'player1@gmail.com'],
            [
                'name' => 'Player One',
                'password' => Hash::make('password123'),
            ]
        );
        
        // 2. Create the Quiz
        $quiz = Quiz::create([
            'name' => 'General Knowledge Quiz',
            'description' => 'A comprehensive quiz testing your knowledge across various difficulty levels.',
            'creator_id' => $admin->id,
            'quiz_code' => 'GENKP',
        ]);

        // 3. Create Categories
        $easy = Category::create(['name' => 'Easy', 'quiz_id' => $quiz->id]);
        $medium = Category::create(['name' => 'Medium', 'quiz_id' => $quiz->id]);
        $hard = Category::create(['name' => 'Hard', 'quiz_id' => $quiz->id]);

        // 4. Easy Questions (True/False) - 10 Items
        $easyQuestions = [
            ['q' => 'The sun rises in the east.', 'a' => true],
            ['q' => 'Water is dry.', 'a' => false],
            ['q' => 'Fish live in water.', 'a' => true],
            ['q' => 'Humans need oxygen to survive.', 'a' => true],
            ['q' => 'The moon is made of cheese.', 'a' => false],
            ['q' => 'Ice is hot.', 'a' => false],
            ['q' => 'Dogs are mammals.', 'a' => true],
            ['q' => 'Penguins can fly.', 'a' => false],
            ['q' => 'Red is a color.', 'a' => true],
            ['q' => '2 + 2 = 5.', 'a' => false],
        ];

        foreach ($easyQuestions as $item) {
            Question::create([
                'category_id' => $easy->id,
                'quiz_id' => $quiz->id,
                'question_data' => [
                    'type' => 'TrueOrFalse',
                    'question_text' => $item['q'],
                    'choices' => [
                        ['id' => 1, 'text' => 'True', 'is_correct' => $item['a']],
                        ['id' => 2, 'text' => 'False', 'is_correct' => !$item['a']],
                    ],
                ],
                'points' => 5,
            ]);
        }

        // 5. Medium Questions (MCQ) - 10 Items
        $mediumQuestions = [
            [
                'q' => 'What is the capital of France?',
                'choices' => [
                    ['id' => 1, 'text' => 'Paris', 'is_correct' => true],
                    ['id' => 2, 'text' => 'London', 'is_correct' => false],
                    ['id' => 3, 'text' => 'Berlin', 'is_correct' => false],
                    ['id' => 4, 'text' => 'Rome', 'is_correct' => false],
                ]
            ],
            [
                'q' => 'Which planet is known as the Red Planet?',
                'choices' => [
                    ['id' => 1, 'text' => 'Venus', 'is_correct' => false],
                    ['id' => 2, 'text' => 'Mars', 'is_correct' => true],
                    ['id' => 3, 'text' => 'Jupiter', 'is_correct' => false],
                    ['id' => 4, 'text' => 'Saturn', 'is_correct' => false],
                ]
            ],
            [
                'q' => 'What is the chemical symbol for Oxygen?',
                'choices' => [
                    ['id' => 1, 'text' => 'O', 'is_correct' => true],
                    ['id' => 2, 'text' => 'Ox', 'is_correct' => false],
                    ['id' => 3, 'text' => 'Om', 'is_correct' => false],
                    ['id' => 4, 'text' => 'On', 'is_correct' => false],
                ]
            ],
            [
                'q' => 'How many continents are there?',
                'choices' => [
                    ['id' => 1, 'text' => '5', 'is_correct' => false],
                    ['id' => 2, 'text' => '6', 'is_correct' => false],
                    ['id' => 3, 'text' => '7', 'is_correct' => true],
                    ['id' => 4, 'text' => '8', 'is_correct' => false],
                ]
            ],
            [
                'q' => 'Who wrote "Hamlet"?',
                'choices' => [
                    ['id' => 1, 'text' => 'Charles Dickens', 'is_correct' => false],
                    ['id' => 2, 'text' => 'William Shakespeare', 'is_correct' => true],
                    ['id' => 3, 'text' => 'Mark Twain', 'is_correct' => false],
                    ['id' => 4, 'text' => 'J.K. Rowling', 'is_correct' => false],
                ]
            ],
            [
                'q' => 'What is the square root of 64?',
                'choices' => [
                    ['id' => 1, 'text' => '6', 'is_correct' => false],
                    ['id' => 2, 'text' => '7', 'is_correct' => false],
                    ['id' => 3, 'text' => '8', 'is_correct' => true],
                    ['id' => 4, 'text' => '9', 'is_correct' => false],
                ]
            ],
            [
                'q' => 'Which is the largest ocean?',
                'choices' => [
                    ['id' => 1, 'text' => 'Atlantic', 'is_correct' => false],
                    ['id' => 2, 'text' => 'Indian', 'is_correct' => false],
                    ['id' => 3, 'text' => 'Pacific', 'is_correct' => true],
                    ['id' => 4, 'text' => 'Arctic', 'is_correct' => false],
                ]
            ],
            [
                'q' => 'What boils at 100 degrees Celsius?',
                'choices' => [
                    ['id' => 1, 'text' => 'Oil', 'is_correct' => false],
                    ['id' => 2, 'text' => 'Water', 'is_correct' => true],
                    ['id' => 3, 'text' => 'Milk', 'is_correct' => false],
                    ['id' => 4, 'text' => 'Alcohol', 'is_correct' => false],
                ]
            ],
            [
                'q' => 'Which animal is the fastest land animal?',
                'choices' => [
                    ['id' => 1, 'text' => 'Lion', 'is_correct' => false],
                    ['id' => 2, 'text' => 'Cheetah', 'is_correct' => true],
                    ['id' => 3, 'text' => 'Horse', 'is_correct' => false],
                    ['id' => 4, 'text' => 'Tiger', 'is_correct' => false],
                ]
            ],
            [
                'q' => 'What is the currency of Japan?',
                'choices' => [
                    ['id' => 1, 'text' => 'Dollar', 'is_correct' => false],
                    ['id' => 2, 'text' => 'Euro', 'is_correct' => false],
                    ['id' => 3, 'text' => 'Yen', 'is_correct' => true],
                    ['id' => 4, 'text' => 'Won', 'is_correct' => false],
                ]
            ],
        ];

        foreach ($mediumQuestions as $item) {
            Question::create([
                'category_id' => $medium->id,
                'quiz_id' => $quiz->id,
                'question_data' => [
                    'type' => 'MCQ',
                    'question_text' => $item['q'],
                    'choices' => $item['choices'],
                ],
                'points' => 10,
            ]);
        }

        // 6. Hard Questions (Identification) - 10 Items
        $hardQuestions = [
            ['q' => 'Who was the first President of the United States?', 'a' => 'George Washington'],
            ['q' => 'What is the capital of Australia?', 'a' => 'Canberra'],
            ['q' => 'What is the powerhouse of the cell?', 'a' => 'Mitochondria'],
            ['q' => 'Which element has the chemical symbol "Fe"?', 'a' => 'Iron'],
            ['q' => 'Who painted the Mona Lisa?', 'a' => 'Leonardo da Vinci'],
            ['q' => 'In which year did the Titanic sink?', 'a' => '1912'],
            ['q' => 'What is the longest river in the world?', 'a' => 'Nile'],
            ['q' => 'Who invented the telephone?', 'a' => 'Alexander Graham Bell'],
            ['q' => 'Which planet is closest to the sun?', 'a' => 'Mercury'],
            ['q' => 'What allows plants to make food from sunlight?', 'a' => 'Photosynthesis'],
        ];

        foreach ($hardQuestions as $item) {
            Question::create([
                'category_id' => $hard->id,
                'quiz_id' => $quiz->id,
                'question_data' => [
                    'type' => 'Identification',
                    'question_text' => $item['q'],
                    'correct_answer' => $item['a'],
                ],
                'points' => 15,
                'bonus_points' => 5,
            ]);
        }

        // 7. Register Participants
        QuizParticipant::create([
            'quiz_id' => $quiz->id,
            'user_id' => $user2->id,
            'status' => 'joined',
        ]);
    }
}
