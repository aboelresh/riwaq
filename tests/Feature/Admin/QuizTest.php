<?php

namespace Tests\Feature\Admin;

use App\Models\Course;
use App\Models\Topic;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class QuizTest extends TestCase
{
    use RefreshDatabase;

    private User  $admin;
    private User  $learner;
    private Topic $topic;

    protected function setUp(): void
    {
        parent::setUp();
        $this->admin   = User::factory()->admin()->create();
        $this->learner = User::factory()->learner()->create();

        $course = Course::create([
            'title'      => 'Test Course',
            'created_by' => $this->admin->id,
        ]);

        $this->topic = Topic::create([
            'title'      => 'Test Topic',
            'type'       => 'article',
            'content'    => 'Test content',
            'created_by' => $this->admin->id,
        ]);

        $this->topic->courses()->attach($course->id);
    }

    private function validQuizPayload(array $override = []): array
    {
        return array_merge([
            'title'           => 'Test Quiz',
            'type'            => 'topic',
            'topic_id'        => $this->topic->id,
            'total_points'    => 10,
            'pass_percentage' => 60,
            'questions'       => [[
                'question_text' => 'What is PHP?',
                'points'        => 10,
                'answers'       => [
                    ['answer_text' => 'A language', 'is_correct' => true],
                    ['answer_text' => 'A database', 'is_correct' => false],
                ],
            ]],
        ], $override);
    }

    #[Test]
    public function quiz_with_no_correct_answer_is_rejected_bug015(): void
    {
        $payload = $this->validQuizPayload(['questions' => [[
            'question_text' => 'Test?',
            'points'        => 10,
            'answers'       => [
                ['answer_text' => 'Wrong A', 'is_correct' => false],
                ['answer_text' => 'Wrong B', 'is_correct' => false],
            ],
        ]]]);

        $response = $this->postJson('/api/v1/admin/quizzes', $payload, $this->authHeaders($this->admin));

        $response->assertStatus(422);
        $this->assertStringContainsString('correct answer', json_encode($response->json('errors')));
    }

    #[Test]
    public function quiz_with_single_answer_is_rejected(): void
    {
        $payload = $this->validQuizPayload(['questions' => [[
            'question_text' => 'Test?',
            'points'        => 10,
            'answers'       => [['answer_text' => 'Only one', 'is_correct' => true]],
        ]]]);

        $this->postJson('/api/v1/admin/quizzes', $payload, $this->authHeaders($this->admin))
             ->assertStatus(422);
    }

    #[Test]
    public function admin_can_create_valid_quiz(): void
    {
        $response = $this->postJson('/api/v1/admin/quizzes',
            $this->validQuizPayload(),
            $this->authHeaders($this->admin)
        );

        $response->assertStatus(201)
                 ->assertJsonPath('data.title', 'Test Quiz')
                 ->assertJsonCount(1, 'data.questions');

        $this->assertArrayHasKey('is_correct', $response->json('data.questions.0.answers.0'));
    }

    #[Test]
    public function learner_cannot_manage_quizzes(): void
    {
        $this->postJson('/api/v1/admin/quizzes', $this->validQuizPayload(), $this->authHeaders($this->learner))
             ->assertStatus(403);
    }

    #[Test]
    public function quiz_uses_db_transaction_on_create(): void
    {
        $response = $this->postJson('/api/v1/admin/quizzes',
            $this->validQuizPayload(),
            $this->authHeaders($this->admin)
        );

        $response->assertStatus(201);
        $quizId = $response->json('data.id');

        $this->assertDatabaseHas('quizzes',   ['id' => $quizId]);
        $this->assertDatabaseHas('questions', ['quiz_id' => $quizId]);
        $this->assertDatabaseHas('answers',   ['is_correct' => true]);
    }
}