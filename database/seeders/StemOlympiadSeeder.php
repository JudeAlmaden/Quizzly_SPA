<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Quiz;
use App\Models\Category;
use App\Models\Question;
use App\Models\QuizParticipant;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class StemOlympiadSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        // Retrieve Admin
        $admin = User::where('email', 'judealmaden2045@gmail.com')->first();

        // Retrieve Players
        $players = User::whereIn('email', [
            'player1@gmail.com',
            'player2@gmail.com',
            'player3@gmail.com',
            'player4@gmail.com',
            'player5@gmail.com',
            'player6@gmail.com',
        ])->get();

        if (!$admin) {
            // Fallback if seeded out of order, though AccountsSeeder should run first
             $admin = User::create([
                'name' => 'Jude Almaden',
                'email' => 'judealmaden2045@gmail.com',
                'password' => Hash::make('password123'),
            ]);
        }

        // 1. Create Quiz
        $quiz = Quiz::firstOrCreate(
            ['name' => 'SHS STEM OLYMPIAD 2026'],
            [
                'description' => 'A comprehensive quiz testing your knowledge across various difficulty levels.',
                'creator_id' => $admin->id,
                'quiz_code' => 'STM26',
            ]
        );

        // 2. Attach Players to Quiz
        foreach ($players as $player) {
            QuizParticipant::firstOrCreate(
                [
                    'quiz_id' => $quiz->id,
                    'user_id' => $player->id,
                ],
                [
                    'status' => 'joined'
                ]
            );
        }

        // 3. Create Categories
        $easy = Category::firstOrCreate(['name' => 'Easy', 'quiz_id' => $quiz->id]);
        $average = Category::firstOrCreate(['name' => 'Average', 'quiz_id' => $quiz->id]);
        $difficult = Category::firstOrCreate(['name' => 'Difficult', 'quiz_id' => $quiz->id]);
$tieBreaker = Category::firstOrCreate(['name' => 'Tie Breaker', 'quiz_id' => $quiz->id]);

        // 4. Create Easy Questions (15 seconds timer)
        $easyQuestions = [
            // Identification
            // [
            //     'type' => 'Identification',
            //     'q' => 'Calculate the quantity of heat required to raise the temperature of 11 kg of water from 10°C to 100°C. Assume the specific heat capacity of water is 4200 J/(kg·°C). Express your answer in MJ.',
            //     'a' => '4.158 MJ',
            // ],
            // [
            //     'type' => 'Identification',
            //     'q' => 'The wavelength of light in glass (n = 1.5) is 450 mm. What is the wavelength of this light in diamond (n = 2.42)?',
            //     'a' => '278.93 mm',
            // ],
            // [
            //     'type' => 'Identification',
            //     'q' => 'Light strikes an air (n = 1)–water boundary with an angle of incidence of 30°. Calculate the refracted angle in water (n = 1.333).',
            //     'a' => '22°',
            // ],
            [
                'type' => 'Identification',
                'q' => 'A 2 kg object accelerates at 3 m/s². Find the force.',
                'a' => '6 N',
            ],
            [
                'type' => 'Identification',
                'q' => 'What is the frequency of a wave with a period of 0.5 s?',
                'a' => '2 Hz',
            ],
            [
                'type' => 'Identification',
                'q' => 'The complex number 5 – 3i is divided by 2 – i. Find the quotient.',
                'a' => '(13 - i)/5', // Normalized answer
            ],
            [
                'type' => 'Identification',
                'q' => 'Solve for x from the following equations: xy = 12, yz = 20, xz = 15',
                'a' => '+-3',
            ],
            [
                'type' => 'Identification',
                'q' => 'Susie’s mother is 35 years old. Three years ago, she was four times as old as Susie was then. How old is Susie?',
                'a' => '11',
            ],
            [
                'type' => 'Identification',
                'q' => 'Four years ago, the sum of the ages of A and B was 45. Six years from now, twice B’s age will be 28 years more than A’s age by then. How old is A now?',
                'a' => '28',
            ],
            // MCQ
            // [
            //     'type' => 'MCQ',
            //     'q' => 'In A triangle BCD, BC = 25, and CD =10. The perimeter of the triangle may be? ',
            //     'choices' => [
            //         ['id' => 1, 'text' => '70', 'is_correct' => false],
            //         ['id' => 2, 'text' => '71', 'is_correct' => false],
            //         ['id' => 3, 'text' => '69', 'is_correct' => true],
            //         ['id' => 4, 'text' => '89', 'is_correct' => false],
            //     ]
            // ],
            [
                'type' => 'MCQ',
                'q' => 'What is the World’s Tallest Free – Standing Structure?',
                'choices' => [
                    ['id' => 1, 'text' => 'Merdeka 118', 'is_correct' => false],
                    ['id' => 2, 'text' => 'Tokyo Skytree', 'is_correct' => false],
                    ['id' => 3, 'text' => 'Burj Khalifa', 'is_correct' => true],
                    ['id' => 4, 'text' => 'Abraj Al-Bait Clock Tower', 'is_correct' => false],
                ]
            ],
            // [
            //     'type' => 'MCQ',
            //     'q' => 'What is the force produced by two moving surfaces in contact called?',
            //     'choices' => [
            //         ['id' => 1, 'text' => 'Tension', 'is_correct' => false],
            //         ['id' => 2, 'text' => 'Compression', 'is_correct' => false],
            //         ['id' => 3, 'text' => 'Friction', 'is_correct' => true],
            //         ['id' => 4, 'text' => 'None of the Above', 'is_correct' => false],
            //     ]
            // ],
            [
                'type' => 'MCQ',
                'q' => 'A process or set of rules to be followed in calculations or other problem solving operations, especially by a computer.',
                'choices' => [
                    ['id' => 1, 'text' => 'Law', 'is_correct' => false],
                    ['id' => 2, 'text' => 'Program', 'is_correct' => false],
                    ['id' => 3, 'text' => 'Algorithm', 'is_correct' => true],
                    ['id' => 4, 'text' => 'None of the Above', 'is_correct' => false],
                ]
            ],
            // [
            //     'type' => 'MCQ',
            //     'q' => 'In civil engineering, what is the purpose of a retaining wall?',
            //     'choices' => [
            //         ['id' => 1, 'text' => 'Flood Prevention', 'is_correct' => false],
            //         ['id' => 2, 'text' => 'Noise Reduction', 'is_correct' => false],
            //         ['id' => 3, 'text' => 'Slope Stabilization', 'is_correct' => true],
            //         ['id' => 4, 'text' => 'Foundation Support', 'is_correct' => false],
            //     ]
            // ],
            [
                'type' => 'MCQ',
                'q' => 'A characteristic of measurement that indicates the closeness of two or more measurements to each other, regardless of whether or not they are accurate.',
                'choices' => [
                    ['id' => 1, 'text' => 'Definite', 'is_correct' => false],
                    ['id' => 2, 'text' => 'Exact', 'is_correct' => false],
                    ['id' => 3, 'text' => 'Precision', 'is_correct' => true],
                    ['id' => 4, 'text' => 'Correct', 'is_correct' => false],
                ]
            ],
            [
                'type' => 'MCQ',
                'q' => 'Who is known as the Father of Geometry?',
                'choices' => [
                    ['id' => 1, 'text' => 'Pythagoras', 'is_correct' => false],
                    ['id' => 2, 'text' => 'Archimedes', 'is_correct' => false],
                    ['id' => 3, 'text' => 'Euclid', 'is_correct' => true],
                    ['id' => 4, 'text' => 'Aristotle', 'is_correct' => false],
                ]
            ],
            //  [
            //     'type' => 'MCQ',
            //     'q' => 'What is the solution of an equation called?',
            //     'choices' => [
            //         ['id' => 1, 'text' => 'Variable', 'is_correct' => false],
            //         ['id' => 2, 'text' => 'Constant', 'is_correct' => false],
            //         ['id' => 3, 'text' => 'Root', 'is_correct' => true],
            //         ['id' => 4, 'text' => 'Coefficient', 'is_correct' => false],
            //     ]
            // ],
            // [
            //     'type' => 'MCQ',
            //     'q' => 'What branch of math deals with shapes and space?',
            //     'choices' => [
            //         ['id' => 1, 'text' => 'Algebra', 'is_correct' => false],
            //         ['id' => 2, 'text' => 'Statistics', 'is_correct' => false],
            //         ['id' => 3, 'text' => 'Calculus', 'is_correct' => false],
            //         ['id' => 4, 'text' => 'Geometry', 'is_correct' => true],
            //     ]
            // ],
            //  [
            //     'type' => 'MCQ',
            //     'q' => ' Who introduced coordinate geometry?',
            //     'choices' => [
            //         ['id' => 1, 'text' => 'Pascal', 'is_correct' => false],
            //         ['id' => 2, 'text' => 'Fermat', 'is_correct' => false],
            //         ['id' => 3, 'text' => 'Leibniz', 'is_correct' => false],
            //         ['id' => 4, 'text' => 'René Descartes', 'is_correct' => true],
            //     ]
            // ],
            //  [
            //     'type' => 'MCQ',
            //     'q' => 'What is the value of 0! (zero factorial)? ',
            //     'choices' => [
            //         ['id' => 1, 'text' => '0', 'is_correct' => false],
            //         ['id' => 2, 'text' => 'Undefined', 'is_correct' => false],
            //         ['id' => 3, 'text' => '-1', 'is_correct' => false],
            //         ['id' => 4, 'text' => '1', 'is_correct' => true],
            //     ]
            // ],
        ];

        // 5. Create Average Questions (30 seconds timer)
        $averageQuestions = [
            // MCQ
            // [
            //     'type' => 'MCQ',
            //     'q' => 'What is the slope of a horizontal line?',
            //     'choices' => [
            //         ['id' => 1, 'text' => '1', 'is_correct' => false],
            //         ['id' => 2, 'text' => 'Undefined', 'is_correct' => false],
            //         ['id' => 3, 'text' => '-1', 'is_correct' => false],
            //         ['id' => 4, 'text' => '0', 'is_correct' => true],
            //     ]
            // ],
            // [
            //     'type' => 'MCQ',
            //     'q' => 'The formula (a^2 + b^2 = c^2) applies to: ',
            //     'choices' => [
            //         ['id' => 1, 'text' => 'Any triangle ', 'is_correct' => false],
            //         ['id' => 2, 'text' => 'Isoceles triangle ', 'is_correct' => false],
            //         ['id' => 3, 'text' => 'Equilateral triangle ', 'is_correct' => false],
            //         ['id' => 4, 'text' => 'Right triangle ', 'is_correct' => true],
            //     ]
            // ],
            // [
            //     'type' => 'MCQ',
            //     'q' => 'Who is the father of calculus',
            //     'choices' => [
            //         ['id' => 1, 'text' => 'Newton', 'is_correct' => false],
            //         ['id' => 2, 'text' => 'Descartes', 'is_correct' => false],
            //         ['id' => 3, 'text' => 'Leibniz', 'is_correct' => false],
            //         ['id' => 4, 'text' => 'Newton and Leibniz', 'is_correct' => true],
            //     ]
            // ],
            // [
            //     'type' => 'MCQ',
            //     'q' => 'What does the term "PID" stand for in controls systems engineering?',
            //     'choices' => [
            //         ['id' => 1, 'text' => 'Proportional, Integral, Differential', 'is_correct' => true],
            //         ['id' => 2, 'text' => 'Power Input Device', 'is_correct' => false],
            //         ['id' => 3, 'text' => 'Process Identification Data', 'is_correct' => false],
            //         ['id' => 4, 'text' => 'Programmable Interface Device', 'is_correct' => false],
            //     ]
            // ],
            // [
            //     'type' => 'MCQ',
            //     'q' => 'What "A" Word is a watercourse engineered to carry a water from a source to a distribution point? It was most famously used in Ancient Rome.',
            //     'choices' => [
            //         ['id' => 1, 'text' => 'Aquifer', 'is_correct' => false],
            //         ['id' => 2, 'text' => 'Artesian Well', 'is_correct' => false],
            //         ['id' => 3, 'text' => 'Aquatic Canal', 'is_correct' => false],
            //         ['id' => 4, 'text' => 'Aqueduct', 'is_correct' => true],
            //     ]
            // ],
            [
                'type' => 'MCQ',
                'q' => 'What is the determinant of a 2×2 matrix?',
                'choices' => [
                    ['id' => 1, 'text' => 'Sum of elements', 'is_correct' => false],
                    ['id' => 2, 'text' => 'Product of diagonals only', 'is_correct' => false],
                    ['id' => 3, 'text' => 'ad − bc', 'is_correct' => true],
                    ['id' => 4, 'text' => 'a + d − b − c', 'is_correct' => false],
                ]
            ],
            [
                'type' => 'MCQ',
                'q' => 'What is the sum of interior angles of a pentagon?',
                'choices' => [
                    ['id' => 1, 'text' => '360°', 'is_correct' => false],
                    ['id' => 2, 'text' => '450°', 'is_correct' => false],
                    ['id' => 3, 'text' => '540°', 'is_correct' => true],
                    ['id' => 4, 'text' => '720°', 'is_correct' => false],
                ]
            ],
            [
                'type' => 'MCQ',
                'q' => 'Which material is commonly used as a piezoelectric element in sensors and actuators?',
                'choices' => [
                    ['id' => 1, 'text' => 'Quartz', 'is_correct' => true],
                    ['id' => 2, 'text' => 'Aluminum', 'is_correct' => false],
                    ['id' => 3, 'text' => 'Silicon', 'is_correct' => false],
                    ['id' => 4, 'text' => 'Copper', 'is_correct' => false],
                ]
            ],
            // [
            //     'type' => 'MCQ',
            //     'q' => 'What sub-field of civil engineering safeguards against misuse of resources?',
            //     'choices' => [
            //         ['id' => 1, 'text' => 'Traffic Engineering', 'is_correct' => false],
            //         ['id' => 2, 'text' => 'Environmental Engineering', 'is_correct' => true],
            //         ['id' => 3, 'text' => 'Mining Engineering', 'is_correct' => false],
            //         ['id' => 4, 'text' => 'Water Resource Engineering', 'is_correct' => false],
            //     ]
            // ],
            [
                'type' => 'MCQ',
                'q' => 'Which principle explains buoyant force equal to the weight of displaced fluid?',
                'choices' => [
                    ['id' => 1, 'text' => 'Principle of Force and Motion', 'is_correct' => false],
                    ['id' => 2, 'text' => "Archimedes' Principle", 'is_correct' => true],
                    ['id' => 3, 'text' => 'Laws of Nature', 'is_correct' => false],
                    ['id' => 4, 'text' => "Saint-Venant’s Principle", 'is_correct' => false],
                ]
            ],

            // Identification
            // [
            //     'type' => 'Identification',
            //     'q' => 'A projectile is launched at 20 m/s at 30°. Find range. Use g = 9.81m/s2',
            //     'a' => '35.3 m',
            // ],
            [
                'type' => 'Identification',
                'q' => 'On the Genotype AaBb × AaBb. What is the Probability of Aabb?',
                'a' => '1/8',
            ],
            [
                'type' => 'Identification',
                'q' => 'A body moves such that its velocity is given by v(t) = 4t + 2. Find the displacement from t = 1 s to t = 4 s.',
                'a' => '36 m',
            ],
            // [
            //     'type' => 'Identification',
            //     'q' => 'How many grams of NaCl are formed when 0.5 mol of Na reacts completely? (Molar mass of NaCl = 58.5 g/mol) 2Na + Cl2 → 2NaCl',
            //     'a' => '29.25 g',
            // ],
            [
                'type' => 'Identification',
                'q' => 'A solid disk rolls without slipping. Find the ratio of translational kinetic energy to rotational kinetic energy.',
                'a' => '2:1',
            ],
            [
                'type' => 'Identification',
                'q' => 'A projectile is fired at 25 m/s at 37°. Find time of flight.',
                'a' => '3.06 s',
            ],
            [
                'type' => 'Identification',
                'q' => 'Suppose you receive x dollars in January and $100 more each month until April. Write the factored polynomial for the total.',
                'a' => '4(x + 150)',
            ],
            // [
            //     'type' => 'Identification',
            //     'q' => 'Find A: 2x+3/(x+4)(x−5) = A/(x+4) + B/(x−5)',
            //     'a' => '5/9',
            // ],
            [
                'type' => 'Identification',
                'q' => 'How many children are there given the age conditions described?',
                'a' => '6',
            ],
            // [
            //     'type' => 'Identification',
            //     'q' => "Ramir can do a job in 1hr, Paul can do it in 2hrs, and Dona can do it in 3hrs. How 
            //     long will it take them to the job by working together",
            //     'a' => '0.55 hrs',
            // ],
        ];

        // 6. Create Hard Questions (60 seconds timer)
        $difficultQuestions = [

            // Identification
            // [
            //     'type' => 'Identification',
            //     'q' => 'Monique is 6ft tall. When her shadow is 9ft long, the tree’s shadow is 45ft. How tall is the tree?',
            //     'a' => '30 ft',
            // ],
            [
                'type' => 'Identification',
                'q' => 'How many circular arrangements can be made from 10 objects all taken at a time?',
                'a' => '362880',
            ],
            [
                'type' => 'Identification',
                'q' => 'A sector with area 96π and radius 24m has a small circle inscribed tangent to all sides. Find its radius.',
                'a' => '8 m',
            ],
            [
                'type' => 'Identification',
                'q' => 'Out of 25 generals, 14 served in Korea, 12 in Vietnam, and 10 in Japan. 4 served in both Korea and Vietnam, 3 in both Vietnam and Japan, 2 in both Korea and Japan, and 1 in all three wars. How many served in none?',
                'a' => '3',
            ],
            // [
            //     'type' => 'Identification',
            //     'q' => 'A skater has a moment of inertia of 3 kg m2 when her arms are outstretched and 1 kg m2 
            //     when her arms are brought in close to her sides. She starts to spin at the rate if 1 rev/s 
            //     when her arms are outstretched and then pulls her arms to her sides. What is her final 
            //     angular speed?',
            //     'a' => '3 rev/s',
            // ],
            // [
            //     'type' => 'Identification',
            //     'q' => 'Find pH of buffer with [HA] = 0.2, [A−] = 0.05, pKa = 4.8.',
            //     'a' => '4.2',
            // ],
            // [
            //     'type' => 'Identification',
            //     'q' => 'Find the angular momentum of a 0.210kg ball moving in a circle of radius 1.10m at 5.25m/s.',
            //     'a' => '1.21 kg m^2/s',
            // ],
            [
                'type' => 'Identification',
                'q' => 'A 1000 W heater heats 1 kg water. How long to raise temp by 50°C? (c=4190).',
                'a' => '209.5s',
            ],
            [
                'type' => 'Identification',
                'q' => 'A 10N force is applied 0.5m from a pivot at 90°. What is the torque?',
                'a' => '5 Nm',
            ],
            // [
            //     'type' => 'Identification',
            //     'q' => 'A muscle cell produces 36 ATP aerobically and 2 ATP anaerobically. What is the percentage decrease?',
            //     'a' => '94.4%',
            // ],

            // MCQ
            // [
            //     'type' => 'MCQ',
            //     'q' => 'What does a zero determinant imply?',
            //     'choices' => [
            //         ['id' => 1, 'text' => 'Matrix is invertible', 'is_correct' => false],
            //         ['id' => 2, 'text' => 'Matrix is singular', 'is_correct' => true],
            //         ['id' => 3, 'text' => 'Matrix is diagonal', 'is_correct' => false],
            //         ['id' => 4, 'text' => 'Matrix is orthogonal', 'is_correct' => false],
            //     ]
            // ],
            [
                'type' => 'MCQ',
                'q' => 'What is the rank of a matrix? ',
                'choices' => [
                    ['id' => 1, 'text' => ' Number of rows ', 'is_correct' => false],
                    ['id' => 2, 'text' => ' Number of columns ', 'is_correct' => false],
                    ['id' => 3, 'text' => ' Maximum number of linearly independent rows or columns ', 'is_correct' => true],
                    ['id' => 4, 'text' => 'Determinant Value', 'is_correct' => false],
                ]
            ],
            // [
            //     'type' => 'MCQ',
            //     'q' => 'Riemann Hypothesis is related to: ',
            //     'choices' => [
            //         ['id' => 1, 'text' => 'Geometry', 'is_correct' => false],
            //         ['id' => 2, 'text' => 'Calculus', 'is_correct' => false],
            //         ['id' => 3, 'text' => 'Prime Numbers', 'is_correct' => true],
            //         ['id' => 4, 'text' => 'Statistics', 'is_correct' => false],
            //     ]
            // ],
            [
                'type' => 'MCQ',
                'q' => 'What is the Laplace transform mainly used for?',
                'choices' => [
                    ['id' => 1, 'text' => 'Geometry Proofs', 'is_correct' => false],
                    ['id' => 2, 'text' => 'Statistics Sampling', 'is_correct' => false],
                    ['id' => 3, 'text' => 'Solving differential Equations', 'is_correct' => true],
                    ['id' => 4, 'text' => 'Matrix Inversion', 'is_correct' => false],
                ]
            ],
            [
                'type' => 'MCQ',
                'q' => 'The point at which a material begins to demonstrate plastic behavior and 
                    irreversibly deform under stress or strain, exceeding the elastic capacity that 
                    allowed it to deform but still return to its original shape',
                'choices' => [
                    ['id' => 1, 'text' => 'Rupture', 'is_correct' => false],
                    ['id' => 2, 'text' => 'Yield/Yielding State', 'is_correct' => true],
                    ['id' => 3, 'text' => 'Bending', 'is_correct' => false],
                    ['id' => 4, 'text' => 'Rotation', 'is_correct' => false],
                ]
            ],
            [
                'type' => 'MCQ',
                'q' => '.  What is the primary characteristic that distinguishes a rigid body from a 
                    deformable body?  ',
                'choices' => [
                    ['id' => 1, 'text' => 'Rigid bodies maintain their shape under load', 'is_correct' => false],
                    ['id' => 2, 'text' => 'Rigid bodies cannot move.', 'is_correct' => true],
                    ['id' => 3, 'text' => 'Deformable bodies cannot be compressed', 'is_correct' => false],
                    ['id' => 4, 'text' => ' Deformable bodies have fixed dimensions. ', 'is_correct' => false],
                ]
            ],
            // [
            //     'type' => 'MCQ',
            //     'q' => 'Which Windows password recovery tool is named after the first two sons of Adam and Eve?',
            //     'choices' => [
            //         ['id' => 1, 'text' => 'Cain and Simon', 'is_correct' => false],
            //         ['id' => 2, 'text' => 'Abel and Simon', 'is_correct' => false],
            //         ['id' => 3, 'text' => 'Cain and Abel', 'is_correct' => true],
            //         ['id' => 4, 'text' => 'Topaz and Gin', 'is_correct' => false],
            //     ]
            // ],

            [
                'type' => 'MCQ',
                'q' => ' What is the name of the spacecraft launched in 1989 to observe Jupiter 
            that is named for the Italian astronomer who is regarded as the “Father of the 
            Scientific Method”? ',
                'choices' => [
                    ['id' => 1, 'text' => 'Apollo', 'is_correct' => false],
                    ['id' => 2, 'text' => 'Galileo', 'is_correct' => true],
                    ['id' => 3, 'text' => 'Space Shuttle Fleet', 'is_correct' => false],
                    ['id' => 4, 'text' => 'Voyager', 'is_correct' => false],
                ]
            ],
            // [
            //     'type' => 'MCQ',
            //     'q' => 'Which of the following is an example of a non-concurrent force system?',
            //     'choices' => [
            //         ['id' => 1, 'text' => 'The weight of an object resting on a table.', 'is_correct' => false],
            //         ['id' => 2, 'text' => 'Two people pushing a car in the same direction.', 'is_correct' => false],
            //         ['id' => 3, 'text' => 'A bookshelf hanging on a wall.', 'is_correct' => false],
            //         ['id' => 4, 'text' => 'A ladder leaning against a wall.', 'is_correct' => true],
            //     ]
            // ],
            // [
            //     'type' => 'Identification',
            //     'q' => 'In a Lucena City, all seven digit telephone numbers begin with 350. How many telephone 
            //     numbers may be assigned to that city if the last four digits should not begin or end in 
            //     zero? '   ,             
            //     'a' => '8100',
            // ],
            // [
            //     'type' => 'Identification',
            //     'q' => 'Find the derivative of y with respect to x: x = (y^3/3)+y'    ,            
            //     'a' => 'dx/dy = 1/(1+y^2)',
            // ],
        ];

        $tieBreakerQuestions = [

            // Easy Questions (previously commented)
            [
                'type' => 'Identification',
                'q' => 'Calculate the quantity of heat required to raise the temperature of 11 kg of water from 10°C to 100°C. Assume the specific heat capacity of water is 4200 J/(kg·°C). Express your answer in MJ.',
                'a' => '4.158 MJ',
            ],
            [
                'type' => 'Identification',
                'q' => 'The wavelength of light in glass (n = 1.5) is 450 mm. What is the wavelength of this light in diamond (n = 2.42)?',
                'a' => '278.93 mm',
            ],
            [
                'type' => 'Identification',
                'q' => 'Light strikes an air (n = 1)–water boundary with an angle of incidence of 30°. Calculate the refracted angle in water (n = 1.333).',
                'a' => '22°',
            ],
            // MCQ commented out from Easy
            [
                'type' => 'MCQ',
                'q' => 'In A triangle BCD, BC = 25, and CD =10. The perimeter of the triangle may be?',
                'choices' => [
                    ['id' => 1, 'text' => '70', 'is_correct' => false],
                    ['id' => 2, 'text' => '71', 'is_correct' => false],
                    ['id' => 3, 'text' => '69', 'is_correct' => true],
                    ['id' => 4, 'text' => '89', 'is_correct' => false],
                ]
            ],
            [
                'type' => 'MCQ',
                'q' => 'What is the force produced by two moving surfaces in contact called?',
                'choices' => [
                    ['id' => 1, 'text' => 'Tension', 'is_correct' => false],
                    ['id' => 2, 'text' => 'Compression', 'is_correct' => false],
                    ['id' => 3, 'text' => 'Friction', 'is_correct' => true],
                    ['id' => 4, 'text' => 'None of the Above', 'is_correct' => false],
                ]
            ],
            [
                'type' => 'MCQ',
                'q' => 'In civil engineering, what is the purpose of a retaining wall?',
                'choices' => [
                    ['id' => 1, 'text' => 'Flood Prevention', 'is_correct' => false],
                    ['id' => 2, 'text' => 'Noise Reduction', 'is_correct' => false],
                    ['id' => 3, 'text' => 'Slope Stabilization', 'is_correct' => true],
                    ['id' => 4, 'text' => 'Foundation Support', 'is_correct' => false],
                ]
            ],
            [
                'type' => 'MCQ',
                'q' => 'What "A" Word is a watercourse engineered to carry water from a source to a distribution point? It was most famously used in Ancient Rome.',
                'choices' => [
                    ['id' => 1, 'text' => 'Aquifer', 'is_correct' => false],
                    ['id' => 2, 'text' => 'Artesian Well', 'is_correct' => false],
                    ['id' => 3, 'text' => 'Aquatic Canal', 'is_correct' => false],
                    ['id' => 4, 'text' => 'Aqueduct', 'is_correct' => true],
                ]
            ],
            [
                'type' => 'MCQ',
                'q' => 'What is the solution of an equation called?',
                'choices' => [
                    ['id' => 1, 'text' => 'Variable', 'is_correct' => false],
                    ['id' => 2, 'text' => 'Constant', 'is_correct' => false],
                    ['id' => 3, 'text' => 'Root', 'is_correct' => true],
                    ['id' => 4, 'text' => 'Coefficient', 'is_correct' => false],
                ]
            ],
            [
                'type' => 'MCQ',
                'q' => 'What branch of math deals with shapes and space?',
                'choices' => [
                    ['id' => 1, 'text' => 'Algebra', 'is_correct' => false],
                    ['id' => 2, 'text' => 'Statistics', 'is_correct' => false],
                    ['id' => 3, 'text' => 'Calculus', 'is_correct' => false],
                    ['id' => 4, 'text' => 'Geometry', 'is_correct' => true],
                ]
            ],
            [
                'type' => 'MCQ',
                'q' => 'Who introduced coordinate geometry?',
                'choices' => [
                    ['id' => 1, 'text' => 'Pascal', 'is_correct' => false],
                    ['id' => 2, 'text' => 'Fermat', 'is_correct' => false],
                    ['id' => 3, 'text' => 'Leibniz', 'is_correct' => false],
                    ['id' => 4, 'text' => 'René Descartes', 'is_correct' => true],
                ]
            ],
            [
                'type' => 'MCQ',
                'q' => 'What is the value of 0! (zero factorial)?',
                'choices' => [
                    ['id' => 1, 'text' => '0', 'is_correct' => false],
                    ['id' => 2, 'text' => 'Undefined', 'is_correct' => false],
                    ['id' => 3, 'text' => '-1', 'is_correct' => false],
                    ['id' => 4, 'text' => '1', 'is_correct' => true],
                ]
            ],

            // Difficult Questions (commented out)
            [
                'type' => 'Identification',
                'q' => 'Monique is 6ft tall. When her shadow is 9ft long, the tree’s shadow is 45ft. How tall is the tree?',
                'a' => '30 ft',
            ],
            [
                'type' => 'Identification',
                'q' => 'A skater has a moment of inertia of 3 kg m2 when her arms are outstretched and 1 kg m2 when her arms are brought in close to her sides. She starts to spin at the rate if 1 rev/s when her arms are outstretched and then pulls her arms to her sides. What is her final angular speed?',
                'a' => '3 rev/s',
            ],
            [
                'type' => 'Identification',
                'q' => 'Find pH of buffer with [HA] = 0.2, [A−] = 0.05, pKa = 4.8.',
                'a' => '4.2',
            ],
            [
                'type' => 'Identification',
                'q' => 'Find the angular momentum of a 0.210kg ball moving in a circle of radius 1.10m at 5.25m/s.',
                'a' => '1.21 kg m^2/s',
            ],
            [
                'type' => 'MCQ',
                'q' => 'What does a zero determinant imply?',
                'choices' => [
                    ['id' => 1, 'text' => 'Matrix is invertible', 'is_correct' => false],
                    ['id' => 2, 'text' => 'Matrix is singular', 'is_correct' => true],
                    ['id' => 3, 'text' => 'Matrix is diagonal', 'is_correct' => false],
                    ['id' => 4, 'text' => 'Matrix is orthogonal', 'is_correct' => false],
                ]
            ],
            [
                'type' => 'MCQ',
                'q' => 'Riemann Hypothesis is related to:',
                'choices' => [
                    ['id' => 1, 'text' => 'Geometry', 'is_correct' => false],
                    ['id' => 2, 'text' => 'Calculus', 'is_correct' => false],
                    ['id' => 3, 'text' => 'Prime Numbers', 'is_correct' => true],
                    ['id' => 4, 'text' => 'Statistics', 'is_correct' => false],
                ]
            ],
            [
                'type' => 'Identification',
                'q' => 'In a Lucena City, all seven digit telephone numbers begin with 350. How many telephone numbers may be assigned to that city if the last four digits should not begin or end in zero?',
                'a' => '8100',
            ],
            [
                'type' => 'Identification',
                'q' => 'Find the derivative of y with respect to x: x = (y^3/3)+y',
                'a' => 'dx/dy = 1/(1+y^2)',
            ],
        ];
            

        //Attach questions to quiz
        foreach ($easyQuestions as $item) {
            // Check if question already exists to avoid duplicates
            $exists = Question::where('quiz_id', $quiz->id)
                ->where('category_id', $easy->id)
                ->where('question_data->question_text', $item['q'])
                ->exists();

            if ($exists) {
                continue;
            }

            $questionData = [
                'type' => $item['type'],
                'question_text' => $item['q'],
                'timer_seconds' => 15, // 15 seconds timer
            ];

            if ($item['type'] === 'Identification') {
                $questionData['correct_answer'] = $item['a'];
            } elseif ($item['type'] === 'MCQ') {
                $questionData['choices'] = $item['choices'];
            }

            Question::create([
                'category_id' => $easy->id,
                'quiz_id' => $quiz->id,
                'question_data' => $questionData,
                'points' => 10, // Default points for Easy
            ]);
        }
        foreach ($averageQuestions as $item) {
            $questionData = [
                'type' => $item['type'],
                'question_text' => $item['q'],
                'timer_seconds' => 30,
            ];

            if ($item['type'] === 'Identification') {
                $questionData['correct_answer'] = $item['a'];
            } else {
                $questionData['choices'] = $item['choices'];
            }

            Question::create([
                'category_id' => $average->id,
                'quiz_id' => $quiz->id,
                'question_data' => $questionData,
                'points' => 20, // Higher than Easy
            ]);
        }
        foreach ($difficultQuestions as $item) {
            $questionData = [
                'type' => $item['type'],
                'question_text' => $item['q'],
                'timer_seconds' => 60,
            ];

            if ($item['type'] === 'Identification') {
                $questionData['correct_answer'] = $item['a'];
            } else {
                $questionData['choices'] = $item['choices'];
            }

            Question::create([
                'category_id' => $difficult->id,
                'quiz_id' => $quiz->id,
                'question_data' => $questionData,
                'points' => 30, // Hard points
            ]);
        }
        foreach ($tieBreakerQuestions as $item) {
            $questionData = [
                'type' => $item['type'],
                'question_text' => $item['q'],
                'timer_seconds' => 30,
            ];

            if ($item['type'] === 'Identification') {
                $questionData['correct_answer'] = $item['a'];
            } else {
                $questionData['choices'] = $item['choices'];
            }

            Question::create([
                'category_id' => $tieBreaker->id,
                'quiz_id' => $quiz->id,
                'question_data' => $questionData,
                'points' => 1, // Hard points
            ]);
        }

    }
}
