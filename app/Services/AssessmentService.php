<?php

namespace App\Services;

use App\Models\User;
use App\Models\Track;
use App\Models\AssessmentResult;

class AssessmentService
{
    /**
     * Assessment questions configuration
     * Each question has options that map to different tracks
     */
    private function getQuestions(): array
    {
        return [
            [
                'id' => 1,
                'question' => 'What type of projects interest you most?',
                'options' => [
                    [
                        'id' => 'A',
                        'text' => 'Building websites and web applications',
                        'tracks' => ['web' => 3],
                    ],
                    [
                        'id' => 'B',
                        'text' => 'Creating mobile apps',
                        'tracks' => ['mobile' => 3],
                    ],
                    [
                        'id' => 'C',
                        'text' => 'Data analysis and AI',
                        'tracks' => ['data' => 3],
                    ],
                    [
                        'id' => 'D',
                        'text' => 'Game development',
                        'tracks' => ['game' => 3],
                    ],
                ]
            ],
            [
                'id' => 2,
                'question' => 'Which programming language sounds most interesting to you?',
                'options' => [
                    [
                        'id' => 'A',
                        'text' => 'JavaScript/TypeScript',
                        'tracks' => ['web' => 2, 'mobile' => 1],
                    ],
                    [
                        'id' => 'B',
                        'text' => 'Python',
                        'tracks' => ['data' => 3, 'web' => 1],
                    ],
                    [
                        'id' => 'C',
                        'text' => 'Java/Kotlin',
                        'tracks' => ['mobile' => 2],
                    ],
                    [
                        'id' => 'D',
                        'text' => 'C#/C++',
                        'tracks' => ['game' => 2],
                    ],
                ]
            ],
            [
                'id' => 3,
                'question' => 'What do you enjoy working with?',
                'options' => [
                    [
                        'id' => 'A',
                        'text' => 'User interfaces and design',
                        'tracks' => ['web' => 2, 'mobile' => 2],
                    ],
                    [
                        'id' => 'B',
                        'text' => 'Data and algorithms',
                        'tracks' => ['data' => 3],
                    ],
                    [
                        'id' => 'C',
                        'text' => 'Databases and APIs',
                        'tracks' => ['web' => 2],
                    ],
                    [
                        'id' => 'D',
                        'text' => 'Graphics and animations',
                        'tracks' => ['game' => 3, 'mobile' => 1],
                    ],
                ]
            ],
            [
                'id' => 4,
                'question' => 'What is your learning goal?',
                'options' => [
                    [
                        'id' => 'A',
                        'text' => 'Get a job as a web developer',
                        'tracks' => ['web' => 3],
                    ],
                    [
                        'id' => 'B',
                        'text' => 'Create my own mobile app',
                        'tracks' => ['mobile' => 3],
                    ],
                    [
                        'id' => 'C',
                        'text' => 'Work with data and AI',
                        'tracks' => ['data' => 3],
                    ],
                    [
                        'id' => 'D',
                        'text' => 'Develop games',
                        'tracks' => ['game' => 3],
                    ],
                ]
            ],
            [
                'id' => 5,
                'question' => 'Which platform do you prefer?',
                'options' => [
                    [
                        'id' => 'A',
                        'text' => 'Web browsers',
                        'tracks' => ['web' => 3],
                    ],
                    [
                        'id' => 'B',
                        'text' => 'Mobile devices (iOS/Android)',
                        'tracks' => ['mobile' => 3],
                    ],
                    [
                        'id' => 'C',
                        'text' => 'Cloud and servers',
                        'tracks' => ['data' => 2, 'web' => 1],
                    ],
                    [
                        'id' => 'D',
                        'text' => 'Gaming consoles/PC',
                        'tracks' => ['game' => 3],
                    ],
                ]
            ],
        ];
    }

    /**
     * Get assessment questions (without track mappings for client)
     */
    public function getAssessmentQuestions(): array
    {
        $questions = $this->getQuestions();
        
        // Remove track mappings from options (client shouldn't see this)
        return array_map(function($question) {
            $question['options'] = array_map(function($option) {
                return [
                    'id' => $option['id'],
                    'text' => $option['text'],
                ];
            }, $question['options']);
            
            return $question;
        }, $questions);
    }

    /**
     * Calculate scores and recommend track
     */
    public function evaluateAssessment(User $user, array $answers): AssessmentResult
    {
        $questions = $this->getQuestions();
        $trackScores = [
            'web' => 0,
            'mobile' => 0,
            'data' => 0,
            'game' => 0,
        ];

        // Calculate scores based on answers
        foreach ($answers as $questionId => $selectedOption) {
            $question = collect($questions)->firstWhere('id', (int)$questionId);
            
            if (!$question) continue;
            
            $option = collect($question['options'])->firstWhere('id', $selectedOption);
            
            if (!$option) continue;
            
            // Add scores for each track
            foreach ($option['tracks'] as $trackType => $points) {
                $trackScores[$trackType] += $points;
            }
        }

        // Find track with highest score
        arsort($trackScores);
        $topTrackType = array_key_first($trackScores);

        // Map track type to actual track
        $trackMapping = [
            'web' => 'Web Development',
            'mobile' => 'Mobile Development',
            'data' => 'Data Science',
            'game' => 'Game Development',
        ];

        // Find the track in database
        $recommendedTrack = Track::where('title', 'LIKE', '%' . $trackMapping[$topTrackType] . '%')
            ->first();

        // If no exact match, get first available track
        if (!$recommendedTrack) {
            $recommendedTrack = Track::first();
        }

        // Save assessment result
        $result = AssessmentResult::create([
            'user_id' => $user->id,
            'recommended_track_id' => $recommendedTrack?->id,
            'answers' => $answers,
            'scores' => $trackScores,
            'analysis' => $this->generateAnalysis($trackScores, $topTrackType),
        ]);

        return $result;
    }

    /**
     * Generate analysis text
     */
    private function generateAnalysis(array $scores, string $topTrack): string
    {
        $trackDescriptions = [
            'web' => 'You have a strong interest in web development! You enjoy creating websites and web applications, working with modern frameworks, and bringing designs to life in the browser.',
            'mobile' => 'Mobile development is your calling! You\'re interested in creating apps for smartphones and tablets, learning iOS and Android development, and building user-friendly mobile experiences.',
            'data' => 'Data science and AI fascinate you! You\'re drawn to working with data, building machine learning models, and using analytics to solve real-world problems.',
            'game' => 'Game development excites you! You love the idea of creating interactive experiences, working with graphics and physics engines, and bringing game ideas to life.',
        ];

        $analysis = $trackDescriptions[$topTrack] ?? 'You have diverse interests in technology!';
        
        // Add score breakdown
        $analysis .= "\n\nYour scores:\n";
        foreach ($scores as $track => $score) {
            $percentage = round(($score / array_sum($scores)) * 100);
            $analysis .= "- " . ucfirst($track) . ": {$percentage}%\n";
        }

        return $analysis;
    }

    /**
     * Get user's assessment result
     */
    public function getUserAssessment(User $user): ?AssessmentResult
    {
        return AssessmentResult::with('recommendedTrack')
            ->where('user_id', $user->id)
            ->latest()
            ->first();
    }
}