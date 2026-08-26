<?php

namespace Tests\Unit\Services;

use App\Models\Answer;
use App\Models\Question;
use App\Models\Quiz;
use App\Models\User;
use App\Models\UserQuizAttempt;
use App\Services\QuizService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;

class QuizServiceTest extends TestCase
{
    use RefreshDatabase;

    private QuizService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new QuizService();
    }

    private function makeQuizWithQuestions(int $passPercentage = 60): Quiz
    {
        $quiz = Quiz::create([
            'title'           => 'Test Quiz',
            'type'            => 'topic',
            'total_points'    => 20,
            'pass_percentage' => $passPercentage,
            'created_by'      => User::factory()->admin()->create()->id,
        ]);

        $q1 = Question::create([
            'quiz_id'       => $quiz->id,
            'question_text' => 'Question 1',
            'points'        => 10,
        ]);
        $correct1   = Answer::create(['question_id' => $q1->id, 'answer_text' => 'Correct A', 'is_correct' => true]);
        $incorrect1 = Answer::create(['question_id' => $q1->id, 'answer_text' => 'Wrong A', 'is_correct' => false]);

        $q2 = Question::create([
            'quiz_id'       => $quiz->id,
            'question_text' => 'Question 2',
            'points'        => 10,
        ]);
        $correct2   = Answer::create(['question_id' => $q2->id, 'answer_text' => 'Correct B', 'is_correct' => true]);
        $incorrect2 = Answer::create(['question_id' => $q2->id, 'answer_text' => 'Wrong B', 'is_correct' => false]);

        $quiz->load('questions.answers');

        $quiz->_testData = [
            'q1_id'      => $q1->id,
            'q2_id'      => $q2->id,
            'correct1'   => $correct1->id,
            'incorrect1' => $incorrect1->id,
            'correct2'   => $correct2->id,
            'incorrect2' => $incorrect2->id,
        ];

        return $quiz;
    }

    #[Test]
    public function it_scores_zero_for_all_wrong_answers(): void
    {
        $user = User::factory()->create();
        $quiz = $this->makeQuizWithQuestions();
        $d    = $quiz->_testData;

        $attempt = $this->service->submitQuiz($user, $quiz, [
            $d['q1_id'] => $d['incorrect1'],
            $d['q2_id'] => $d['incorrect2'],
        ]);

        $this->assertEquals(0, $attempt->score);
        $this->assertFalse($attempt->passed);
    }

    #[Test]
    public function it_scores_full_for_all_correct_answers(): void
    {
        $user = User::factory()->create();
        $quiz = $this->makeQuizWithQuestions();
        $d    = $quiz->_testData;

        $attempt = $this->service->submitQuiz($user, $quiz, [
            $d['q1_id'] => $d['correct1'],
            $d['q2_id'] => $d['correct2'],
        ]);

        $this->assertEquals(20, $attempt->score);
        $this->assertTrue($attempt->passed);
    }

    #[Test]
    public function it_passes_when_above_pass_percentage(): void
    {
        $user = User::factory()->create();
        $quiz = $this->makeQuizWithQuestions(passPercentage: 50);
        $d    = $quiz->_testData;

        // 10/20 = 50% — exactly at threshold
        $attempt = $this->service->submitQuiz($user, $quiz, [
            $d['q1_id'] => $d['correct1'],
            $d['q2_id'] => $d['incorrect2'],
        ]);

        $this->assertEquals(10, $attempt->score);
        $this->assertTrue($attempt->passed);
    }

    #[Test]
    public function it_fails_when_below_pass_percentage(): void
    {
        $user = User::factory()->create();
        $quiz = $this->makeQuizWithQuestions(passPercentage: 80);
        $d    = $quiz->_testData;

        // 10/20 = 50% — below 80% threshold
        $attempt = $this->service->submitQuiz($user, $quiz, [
            $d['q1_id'] => $d['correct1'],
            $d['q2_id'] => $d['incorrect2'],
        ]);

        $this->assertFalse($attempt->passed);
        $this->assertNotNull($attempt->can_retry_at);
    }

    #[Test]
    public function it_sets_retry_cooldown_on_failure(): void
    {
        $user = User::factory()->create();
        $quiz = $this->makeQuizWithQuestions();
        $d    = $quiz->_testData;

        $attempt = $this->service->submitQuiz($user, $quiz, [
            $d['q1_id'] => $d['incorrect1'],
        ]);

        $this->assertFalse($attempt->passed);
        $this->assertNotNull($attempt->can_retry_at);
        $this->assertTrue($attempt->can_retry_at->isFuture());
    }

    #[Test]
    public function it_prevents_retaking_a_passed_quiz(): void
    {
        $user = User::factory()->create();
        $quiz = $this->makeQuizWithQuestions();
        $d    = $quiz->_testData;

        // First attempt — pass
        $this->service->submitQuiz($user, $quiz, [
            $d['q1_id'] => $d['correct1'],
            $d['q2_id'] => $d['correct2'],
        ]);

        $canTake = $this->service->canTakeQuiz($user, $quiz);
        $this->assertFalse($canTake);
    }

    #[Test]
    public function it_prevents_immediate_retry_after_failure(): void
    {
        $user = User::factory()->create();
        $quiz = $this->makeQuizWithQuestions();
        $d    = $quiz->_testData;

        // First attempt — fail
        $this->service->submitQuiz($user, $quiz, [
            $d['q1_id'] => $d['incorrect1'],
        ]);

        $canTake = $this->service->canTakeQuiz($user, $quiz);
        $this->assertFalse($canTake);
    }

    #[Test]
    public function it_ignores_answers_with_no_matching_question(): void
    {
        $user = User::factory()->create();
        $quiz = $this->makeQuizWithQuestions();

        // Submit with non-existent question IDs
        $attempt = $this->service->submitQuiz($user, $quiz, [
            99999 => 88888,
        ]);

        $this->assertEquals(0, $attempt->score);
        $this->assertFalse($attempt->passed);
    }
}