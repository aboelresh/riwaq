<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Topic;
use App\Models\UserTopicProgress;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;

class AiChatController extends Controller
{
    /**
     * POST /api/ai/chat
     * Send a message to the AI study assistant
     */
    public function chat(Request $request)
    {
        $validated = $request->validate([
            'message' => 'required|string|max:1000',
            'topic_id' => 'nullable|integer',
            'history' => 'nullable|array|max:10',
            'history.*.role' => 'in:user,assistant',
            'history.*.content' => 'string|max:2000',
        ]);

        $user = auth()->user();

        // Rate limiting: 30 messages per hour per user
        $cacheKey = "ai_chat_limit:{$user->id}";
        $count = Cache::get($cacheKey, 0);
        if ($count >= 30) {
            return response()->json([
                'success' => false,
                'message' => 'Rate limit exceeded. Try again later.',
            ], 429);
        }
        Cache::put($cacheKey, $count + 1, 3600);

        // Build context
        $topicContext = '';
        if ($validated['topic_id']) {
            $topic = Topic::find($validated['topic_id']);
            if ($topic) {
                $topicContext = "\n\nCurrent Topic: {$topic->title}\nType: {$topic->type}\nContent (summary): " . mb_substr(strip_tags($topic->content ?? ''), 0, 2000);
            }
        }

        // Get user progress for context
        $progress = UserTopicProgress::where('user_id', $user->id)
            ->where('is_viewed', true)
            ->count();

        $systemPrompt = $this->buildSystemPrompt($user, $topicContext, $progress);

        // Try configured AI provider
        $provider = config('services.ai.provider', env('AI_PROVIDER', 'gemini'));

        try {
            $reply = match ($provider) {
                'openai' => $this->callOpenAI($systemPrompt, $validated),
                'claude' => $this->callClaude($systemPrompt, $validated),
                'gemini' => $this->callGemini($systemPrompt, $validated),
                'groq' => $this->callGroq($systemPrompt, $validated),
                default => $this->callGroq($systemPrompt, $validated),
            };

            return response()->json([
                'success' => true,
                'data' => ['reply' => $reply],
            ]);
        } catch (\Exception $e) {
            \Log::error('AI Chat Error: ' . $e->getMessage());

            // Fallback: smart pre-built responses
            $reply = $this->fallbackResponse($validated['message'], $topicContext);

            return response()->json([
                'success' => true,
                'data' => ['reply' => $reply, 'fallback' => true, 'error' => $e->getMessage()],
            ]);
        }
    }

    private function buildSystemPrompt($user, $topicContext, $viewedTopics)
    {
        return "You are 'Rafiq' (رفيق) — a helpful, friendly AI study companion for the Code Master platform. Your name means 'companion' in Arabic. You are the student's shadow in their learning journey — always there, always understanding. You introduce yourself as 'رفيق' in Arabic or 'Rafiq' in English.

Platform: Code Master — an interactive learning platform for programming and technology.

Student: {$user->name}
Topics completed: {$viewedTopics}
{$topicContext}

## PLATFORM KNOWLEDGE (use when student asks about the platform):

**What is Code Master?**
An interactive learning platform with 4 tracks: Web Development, Mobile Apps, Data Science, Game Development. Each track has sequential courses with topics and quizzes.

**How to start:**
1. Take the 5-question assessment quiz to find your best track
2. Set up your profile (photo, bio, goals)
3. Enroll in a track and start studying

**Study Modes:**
- Theory Mode: Read articles, then pass a quiz to unlock the next topic
- Video Mode: Watch video lessons. 80% watched = topic completed. Progress saves automatically.

**Teams:**
- Create or join teams with invite codes, links, or username invites
- Teams have sections (General + custom like Frontend, Backend)
- Mission Board: Kanban with TODO → IN PROGRESS → REVIEW → DONE
- Notes: Private (leader only) or shared notes per member
- 11 achievement badges per member
- Leaderboard + Activity Feed
- Real-time WebSocket chat (team, section, and DM levels)
- Weekly challenges: Leaders set goals (topics viewed, quizzes passed, XP earned, tasks completed)

**Workstation:** Personal productivity tools — Pomodoro timer, tasks, sticky notes, mood tracker, habits, quotes.

**Progress:** Full performance report with heatmap, skills radar, track progress, achievements, and printable report.

**Streak:** Consecutive days of learning. View a topic or pass a quiz to maintain it.

**XP Points:** Earned from quizzes and topics. Shown everywhere.

**Shortcuts:** Ctrl+K for quick search. Esc to close modals.

## RULES:
- Be concise and clear. Use short paragraphs.
- Explain concepts simply, as if teaching a beginner.
- If the student asks about a topic, use the provided topic content for context.
- If the student asks about the platform, use the PLATFORM KNOWLEDGE above.
- Support both Arabic and English — reply in the same language the student uses.
- Use code examples when helpful (wrap in ```language blocks).
- Encourage the student and be positive.
- If you don't know something, say so honestly.
- Keep responses under 300 words unless the student asks for detailed explanation.
- You can use emojis sparingly to be friendly.";
    }

    private function callGemini($systemPrompt, $validated)
    {
        $apiKey = env('GEMINI_API_KEY', '');
        if (!$apiKey) {
            throw new \Exception('Gemini API key not configured');
        }

        $messages = [];
        foreach ($validated['history'] ?? [] as $msg) {
            $messages[] = [
                'role' => $msg['role'] === 'user' ? 'user' : 'model',
                'parts' => [['text' => $msg['content']]],
            ];
        }
        $messages[] = [
            'role' => 'user',
            'parts' => [['text' => $validated['message']]],
        ];

        $response = Http::timeout(30)->post(
            "https://generativelanguage.googleapis.com/v1beta/models/gemini-2.0-flash:generateContent?key={$apiKey}",
            [
                'system_instruction' => ['parts' => [['text' => $systemPrompt]]],
                'contents' => $messages,
                'generationConfig' => [
                    'temperature' => 0.7,
                    'maxOutputTokens' => 1024,
                ],
            ]
        );

        if (!$response->ok()) {
            throw new \Exception('Gemini API error: ' . $response->status());
        }

        return $response->json('candidates.0.content.parts.0.text', 'Sorry, I could not generate a response.');
    }

    private function callOpenAI($systemPrompt, $validated)
    {
        $apiKey = env('OPENAI_API_KEY', '');
        if (!$apiKey) throw new \Exception('OpenAI API key not configured');

        $messages = [['role' => 'system', 'content' => $systemPrompt]];
        foreach ($validated['history'] ?? [] as $msg) {
            $messages[] = ['role' => $msg['role'], 'content' => $msg['content']];
        }
        $messages[] = ['role' => 'user', 'content' => $validated['message']];

        $response = Http::timeout(30)
            ->withHeaders(['Authorization' => "Bearer {$apiKey}"])
            ->post('https://api.openai.com/v1/chat/completions', [
                'model' => env('OPENAI_MODEL', 'gpt-4o-mini'),
                'messages' => $messages,
                'max_tokens' => 1024,
                'temperature' => 0.7,
            ]);

        if (!$response->ok()) throw new \Exception('OpenAI API error: ' . $response->status());
        return $response->json('choices.0.message.content', 'Sorry, I could not generate a response.');
    }

    private function callClaude($systemPrompt, $validated)
    {
        $apiKey = env('ANTHROPIC_API_KEY', '');
        if (!$apiKey) throw new \Exception('Claude API key not configured');

        $messages = [];
        foreach ($validated['history'] ?? [] as $msg) {
            $messages[] = ['role' => $msg['role'], 'content' => $msg['content']];
        }
        $messages[] = ['role' => 'user', 'content' => $validated['message']];

        $response = Http::timeout(30)
            ->withHeaders([
                'x-api-key' => $apiKey,
                'anthropic-version' => '2023-06-01',
                'content-type' => 'application/json',
            ])
            ->post('https://api.anthropic.com/v1/messages', [
                'model' => env('ANTHROPIC_MODEL', 'claude-sonnet-4-20250514'),
                'max_tokens' => 1024,
                'system' => $systemPrompt,
                'messages' => $messages,
            ]);

        if (!$response->ok()) throw new \Exception('Claude API error: ' . $response->status());
        return $response->json('content.0.text', 'Sorry, I could not generate a response.');
    }

    private function callGroq($systemPrompt, $validated)
    {
        $apiKey = env('GROQ_API_KEY', '');
        if (!$apiKey) throw new \Exception('Groq API key not configured');

        $messages = [['role' => 'system', 'content' => $systemPrompt]];
        foreach ($validated['history'] ?? [] as $msg) {
            $messages[] = ['role' => $msg['role'], 'content' => $msg['content']];
        }
        $messages[] = ['role' => 'user', 'content' => $validated['message']];

        $response = Http::timeout(30)
            ->withHeaders(['Authorization' => "Bearer {$apiKey}"])
            ->post('https://api.groq.com/openai/v1/chat/completions', [
                'model' => env('GROQ_MODEL', 'llama-3.3-70b-versatile'),
                'messages' => $messages,
                'max_tokens' => 1024,
                'temperature' => 0.7,
            ]);

        if (!$response->ok()) throw new \Exception('Groq API error: ' . $response->status());
        return $response->json('choices.0.message.content', 'Sorry, I could not generate a response.');
    }

    private function fallbackResponse($message, $topicContext)
    {
        $msg = mb_strtolower($message);
        $isAr = preg_match('/[\x{0600}-\x{06FF}]/u', $message);

        if (str_contains($msg, 'شرح') || str_contains($msg, 'explain') || str_contains($msg, 'وضح')) {
            return $isAr
                ? 'عشان أقدر أشرحلك بشكل أفضل، محتاج تحدد الجزء اللي مش فاهمه. اقرأ الموضوع وقولي أنهي نقطة محتاج توضيح فيها.'
                : 'To explain better, I need you to specify which part you don\'t understand. Read the topic and tell me which point needs clarification.';
        }
        if (str_contains($msg, 'كويز') || str_contains($msg, 'quiz') || str_contains($msg, 'اختبار')) {
            return $isAr
                ? 'الكويز بيكون في نهاية كل موضوع. اقرأ المحتوى كويس وبعدين اضغط "ابدأ الكويز". لو مجاوبتش صح، تقدر تعيده تاني!'
                : 'The quiz is at the end of each topic. Read the content well then click "Start Quiz". If you don\'t pass, you can retry!';
        }
        if (str_contains($msg, 'صعب') || str_contains($msg, 'hard') || str_contains($msg, 'مش فاهم')) {
            return $isAr
                ? 'متقلقش! كل حاجة بتبقى صعبة في الأول. حاول تقرأ الموضوع أكتر من مرة، وركز على الأمثلة العملية. لو في جزء معين مش فاهمه، قولي وهحاول أبسطهولك.'
                : 'Don\'t worry! Everything is hard at first. Try reading the topic multiple times, focus on practical examples. If there\'s a specific part you don\'t get, tell me and I\'ll try to simplify it.';
        }

        return $isAr
            ? 'أنا مساعد الدراسة بتاعك! اسألني عن أي حاجة في الموضوع الحالي وهحاول أساعدك. ممكن تسألني أشرحلك مفهوم معين أو أساعدك تحل مشكلة.'
            : 'I\'m your study assistant! Ask me about anything in the current topic and I\'ll try to help. You can ask me to explain a concept or help you solve a problem.';
    }
}
