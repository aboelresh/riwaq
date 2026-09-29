# 🎓 دليل تعلم Backend - من الصفر للاحتراف

## 📖 المقدمة

مرحباً! هتتعلم معايا إزاي تبني **Backend API احترافي** بـ Laravel خطوة بخطوة.
هنشرح كل سطر كود ونفهم ليه كتبناه بالظبط.

---

## 🗂️ جدول المحتويات

1. [فهم بنية المشروع](#1-فهم-بنية-المشروع)
2. [قاعدة البيانات والعلاقات](#2-قاعدة-البيانات-والعلاقات)
3. [Models وكيف تشتغل](#3-models-وكيف-تشتغل)
4. [Services - منطق العمل](#4-services---منطق-العمل)
5. [Controllers - معالجة الطلبات](#5-controllers---معالجة-الطلبات)
6. [Authentication & Security](#6-authentication--security)
7. [Routes - ربط كل حاجة](#7-routes---ربط-كل-حاجة)
8. [API Flow - كيف يعمل الطلب](#8-api-flow---كيف-يعمل-الطلب)

---

## 1. فهم بنية المشروع

### 📂 البنية الأساسية:
```
backend/
├── app/
│   ├── Http/
│   │   ├── Controllers/     ← هنا بنستقبل الطلبات
│   │   └── Middleware/      ← هنا بنتحقق من الصلاحيات
│   ├── Models/              ← هنا بنتعامل مع الجداول
│   └── Services/            ← هنا المنطق المعقد
├── database/
│   ├── migrations/          ← هنا بنعمل الجداول
│   └── seeders/             ← هنا بنملا بيانات تجريبية
├── routes/
│   └── api.php              ← هنا بنحدد URLs
└── storage/                 ← هنا بنحفظ الملفات
```

### 🤔 ليه البنية دي؟

**Laravel بيتبع نمط MVC:**
- **M**odel: بيتعامل مع قاعدة البيانات
- **V**iew: الواجهات (مش محتاجينها لأننا API)
- **C**ontroller: بيستقبل الطلبات ويرد عليها

**احنا ضفنا Services عشان:**
- نفصل المنطق المعقد عن Controllers
- نخلي الكود أسهل في الصيانة والاختبار
- نقدر نعيد استخدام نفس المنطق في أماكن مختلفة

---

## 2. قاعدة البيانات والعلاقات

### 🗄️ الجداول الأساسية:

#### 1️⃣ **جدول Users (المستخدمين)**
```php
Schema::create('users', function (Blueprint $table) {
    $table->id();                          // رقم تعريف تلقائي
    $table->string('name');                // اسم المستخدم
    $table->string('email')->unique();     // البريد (فريد)
    $table->string('password');            // كلمة السر (مشفرة)
    $table->enum('role', ['learner', 'admin'])->default('learner');
    $table->timestamps();                  // created_at & updated_at
});
```

**الشرح:**
- `id()`: رقم تعريفي يزيد تلقائياً (1, 2, 3...)
- `string('name')`: نص عادي للاسم (255 حرف كحد أقصى)
- `unique()`: لازم يكون فريد (مفيش 2 users بنفس الإيميل)
- `enum()`: قيم محددة فقط ('learner' أو 'admin')
- `timestamps()`: Laravel يحط التاريخ والوقت تلقائياً

#### 2️⃣ **جدول Tracks (المسارات)**
```php
Schema::create('tracks', function (Blueprint $table) {
    $table->id();
    $table->string('title');
    $table->text('description')->nullable();
    $table->foreignId('created_by')->constrained('users')->onDelete('cascade');
    $table->timestamps();
});
```

**الشرح:**
- `text()`: نص طويل (أكثر من 255 حرف)
- `nullable()`: ممكن يكون فاضي
- `foreignId()`: مفتاح خارجي (يربط بجدول تاني)
- `constrained('users')`: يربط بـ `users` table
- `onDelete('cascade')`: لو المستخدم اتمسح، كل tracks بتاعته تتمسح

#### 3️⃣ **جدول UserCourseProgress (تقدم المستخدم)**
```php
Schema::create('user_course_progress', function (Blueprint $table) {
    $table->id();
    $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
    $table->foreignId('course_id')->constrained('courses')->onDelete('cascade');
    $table->boolean('is_unlocked')->default(false);
    $table->integer('total_score')->default(0);
    $table->integer('max_possible_score')->default(0);
    $table->boolean('is_completed')->default(false);
    $table->timestamps();
});
```

**الشرح:**
- `boolean()`: صح (1) أو غلط (0)
- `default(false)`: القيمة الافتراضية
- `integer()`: رقم صحيح

---

### 🔗 العلاقات بين الجداول:

#### **One-to-Many (واحد لكثير):**
```php
// في User Model:
public function tracks()
{
    return $this->hasMany(Track::class, 'created_by');
}
```

**معناها:** 
- مستخدم واحد يقدر يعمل tracks كتير
- زي: أدمن واحد عمل 10 tracks

#### **Many-to-Many (كثير لكثير):**
```php
// في Track Model:
public function courses()
{
    return $this->belongsToMany(Course::class, 'track_courses')
                ->withPivot('order')
                ->orderBy('order');
}
```

**معناها:**
- Track واحد فيه courses كتير
- Course واحد ممكن يكون في tracks كتير
- محتاجين جدول وسيط: `track_courses`
- `withPivot('order')`: نقدر نوصل لعمود `order` في الجدول الوسيط

---

### 🧮 مثال عملي على العلاقات:
```php
// عايز كل الكورسات في Track رقم 1:
$track = Track::find(1);
$courses = $track->courses;  // Laravel بيجيب الكورسات تلقائياً!

// عايز التراكات اللي فيها Course رقم 2:
$course = Course::find(2);
$tracks = $course->tracks;   // Laravel بيجيب التراكات تلقائياً!
```

**السحر هنا:** Laravel بيفهم العلاقات ويعمل SQL Query لوحده!

---

## 3. Models وكيف تشتغل

### 🎯 إيه هو الـ Model؟

**Model = البوابة بين الكود وقاعدة البيانات**

### 📝 مثال: User Model
```php
<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Tymon\JWTAuth\Contracts\JWTSubject;

class User extends Authenticatable implements JWTSubject
{
    // 1️⃣ الحقول اللي نقدر نملاها بـ Mass Assignment
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
    ];

    // 2️⃣ الحقول اللي مينفعش نرجعها في الـ JSON
    protected $hidden = [
        'password',
        'remember_token',
    ];

    // 3️⃣ تحويل أنواع البيانات
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',  // Laravel يشفر كلمة السر تلقائياً
        ];
    }

    // 4️⃣ دوال JWT (للـ Authentication)
    public function getJWTIdentifier()
    {
        return $this->getKey();  // بيرجع الـ ID
    }

    public function getJWTCustomClaims()
    {
        return [];  // بيانات إضافية في الـ Token (مش محتاجين دلوقتي)
    }

    // 5️⃣ Helper Methods (دوال مساعدة)
    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isLearner(): bool
    {
        return $this->role === 'learner';
    }

    // 6️⃣ العلاقات
    public function tracks()
    {
        return $this->belongsToMany(Track::class, 'user_tracks')
                    ->withTimestamps();
    }

    public function quizAttempts()
    {
        return $this->hasMany(UserQuizAttempt::class);
    }
}
```

### 🔍 شرح كل جزء:

#### **1. Fillable (الحقول القابلة للملء):**
```php
protected $fillable = ['name', 'email', 'password'];
```

**ليه محتاجينها؟**
- Laravel بيحمينا من **Mass Assignment Vulnerability**
- لو حد بعت `is_admin: true` في الـ Request، Laravel مش هيقبلها
- **فقط** الحقول في `$fillable` اللي نقدر نملاها

**مثال:**
```php
// ✅ دا هيشتغل:
User::create([
    'name' => 'Ahmed',
    'email' => 'ahmed@test.com',
    'password' => bcrypt('password')
]);

// ❌ دا مش هيشتغل (role مش في fillable):
User::create([
    'name' => 'Ahmed',
    'role' => 'admin'  // ← Laravel هيتجاهل دي!
]);
```

#### **2. Hidden (حقول مخفية):**
```php
protected $hidden = ['password', 'remember_token'];
```

**ليه؟**
- لما نرجع User في JSON، مينفعش نرجع كلمة السر!
- Laravel بيشيلهم تلقائياً

**مثال:**
```php
$user = User::find(1);
return response()->json($user);

// الـ Output:
{
    "id": 1,
    "name": "Ahmed",
    "email": "ahmed@test.com"
    // ❌ password مش موجودة!
}
```

#### **3. Casts (تحويل الأنواع):**
```php
protected function casts(): array
{
    return [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];
}
```

**ليه؟**
- Laravel يحول البيانات تلقائياً
- `datetime`: يحول string لـ Carbon object (عشان نقدر نعمل عمليات على التاريخ)
- `hashed`: يشفر كلمة السر بـ bcrypt

#### **4. Helper Methods:**
```php
public function isAdmin(): bool
{
    return $this->role === 'admin';
}
```

**ليه نعملها؟**
- أسهل في القراءة
- لو عايزين نغير المنطق، نغيره في مكان واحد

**مثال استخدام:**
```php
// ❌ مش واضح:
if ($user->role === 'admin') {
    // ...
}

// ✅ واضح وأسهل:
if ($user->isAdmin()) {
    // ...
}
```

#### **5. العلاقات:**
```php
public function quizAttempts()
{
    return $this->hasMany(UserQuizAttempt::class);
}
```

**الاستخدام:**
```php
$user = User::find(1);

// جيب كل محاولات الكويزات:
$attempts = $user->quizAttempts;

// عد المحاولات الناجحة:
$passed = $user->quizAttempts()->where('passed', true)->count();

// جيب آخر محاولة:
$last = $user->quizAttempts()->latest()->first();
```

---

### 🎯 مثال: Quiz Model مع Methods معقدة
```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Quiz extends Model
{
    protected $fillable = [
        'title',
        'type',
        'topic_id',
        'course_id',
        'total_points',
        'pass_percentage',
        'created_by',
    ];

    protected $casts = [
        'total_points' => 'integer',
        'pass_percentage' => 'integer',
    ];

    // ========== العلاقات ==========
    
    public function questions()
    {
        return $this->hasMany(Question::class);
    }

    public function attempts()
    {
        return $this->hasMany(UserQuizAttempt::class);
    }

    public function topic()
    {
        return $this->belongsTo(Topic::class);
    }

    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    // ========== Helper Methods ==========
    
    public function isAssessment(): bool
    {
        return $this->type === 'assessment';
    }

    public function isTopicQuiz(): bool
    {
        return $this->type === 'topic';
    }

    public function isCourseQuiz(): bool
    {
        return $this->type === 'course';
    }
}
```

**الفايدة:**
- كود نظيف وواضح
- سهل الصيانة
- نقدر نختبر كل جزء لوحده

---

## 4. Services - منطق العمل

### 🎯 إيه هو الـ Service؟

**Service = ملف فيه الـ Business Logic المعقد**

### ❓ ليه نفصل Services عن Controllers؟

**❌ بدون Services:**
```php
// في TrackController:
public function enroll($trackId)
{
    $user = auth()->user();
    $track = Track::findOrFail($trackId);
    
    // ✋ كود كتير ومعقد في الـ Controller!
    $userTrack = UserTrack::create([...]);
    $firstCourse = $track->courses()->orderBy('order')->first();
    $progress = UserCourseProgress::create([...]);
    // ... 50 سطر تاني!
}
```

**✅ مع Services:**
```php
// في TrackController:
public function enroll($trackId)
{
    $user = auth()->user();
    $track = Track::findOrFail($trackId);
    
    // 🎯 واضح ونظيف!
    $this->unlockService->unlockFirstCourseInTrack($user, $track);
    
    return response()->json(['success' => true]);
}
```

---

### 📝 مثال كامل: UnlockService
```php
<?php

namespace App\Services;

use App\Models\User;
use App\Models\Track;
use App\Models\Course;
use App\Models\Topic;

class UnlockService
{
    // Constructor Injection (حقن الاعتماديات)
    public function __construct(
        private ProgressService $progressService,
        private QuizService $quizService
    ) {}

    /**
     * فتح أول كورس في المسار
     * 
     * @param User $user المستخدم
     * @param Track $track المسار
     * @return void
     */
    public function unlockFirstCourseInTrack(User $user, Track $track): void
    {
        // 1️⃣ جيب أول كورس في المسار
        $firstCourse = $track->courses()->orderBy('order')->first();
        
        // 2️⃣ لو مفيش كورسات، وقف
        if (!$firstCourse) {
            return;
        }
        
        // 3️⃣ جيب أو اعمل Progress Record للكورس
        $progress = $this->progressService->initializeCourseProgress($user, $firstCourse);
        
        // 4️⃣ لو الكورس مقفول، افتحه
        if (!$progress->is_unlocked) {
            $progress->update([
                'is_unlocked' => true,
                'started_at' => now(),
            ]);
            
            // 5️⃣ افتح أول موضوع في الكورس
            $this->unlockFirstTopicInCourse($user, $firstCourse);
        }
    }

    /**
     * فتح أول موضوع في الكورس
     */
    public function unlockFirstTopicInCourse(User $user, Course $course): void
    {
        // 1️⃣ جيب أول موضوع
        $firstTopic = $course->topics()->orderBy('order')->first();
        
        if (!$firstTopic) {
            return;
        }
        
        // 2️⃣ جيب Progress Record
        $progress = $this->progressService->initializeTopicProgress($user, $firstTopic);
        
        // 3️⃣ افتح الموضوع
        if (!$progress->is_unlocked) {
            $progress->update(['is_unlocked' => true]);
        }
    }

    /**
     * فتح الموضوع التالي في الكورس
     * 
     * @return Topic|null الموضوع التالي أو null لو مفيش
     */
    public function unlockNextTopicInCourse(User $user, Course $course, Topic $currentTopic): ?Topic
    {
        // 1️⃣ جيب كل الموضوعات مرتبة
        $topics = $course->topics()->orderBy('order')->get();
        
        // 2️⃣ لاقي الموضوع الحالي
        $currentIndex = $topics->search(fn($t) => $t->id === $currentTopic->id);
        
        // 3️⃣ لو مش موجود أو آخر موضوع، وقف
        if ($currentIndex === false || $currentIndex >= $topics->count() - 1) {
            return null;
        }
        
        // 4️⃣ جيب الموضوع التالي
        $nextTopic = $topics[$currentIndex + 1];
        
        // 5️⃣ تأكد إن المستخدم نجح في كويز الموضوع الحالي
        $topicQuiz = $currentTopic->quiz;
        
        if (!$topicQuiz) {
            return null;
        }
        
        $lastAttempt = $this->quizService->getLastAttempt($user, $topicQuiz);
        
        // 6️⃣ لو مش ناجح، مينفعش يفتح الموضوع التالي
        if (!$lastAttempt || !$lastAttempt->passed) {
            return null;
        }
        
        // 7️⃣ افتح الموضوع التالي
        $progress = $this->progressService->initializeTopicProgress($user, $nextTopic);
        
        if (!$progress->is_unlocked) {
            $progress->update(['is_unlocked' => true]);
        }
        
        return $nextTopic;
    }
}
```

### 🔍 شرح خطوة بخطوة:

#### **1. Constructor Injection:**
```php
public function __construct(
    private ProgressService $progressService,
    private QuizService $quizService
) {}
```

**معناها:**
- `UnlockService` محتاج `ProgressService` و `QuizService` عشان يشتغل
- Laravel بيعمل Instance منهم تلقائياً (Dependency Injection)
- `private` معناها الـ properties دي بس لـ UnlockService

#### **2. Type Hinting:**
```php
public function unlockFirstCourseInTrack(User $user, Track $track): void
```

**معناها:**
- `User $user`: لازم يكون User object
- `Track $track`: لازم يكون Track object
- `: void`: الدالة مش بترجع حاجة

**الفايدة:**
- لو بعتنا نوع غلط، PHP بيرمي Exception
- الكود أكثر أماناً

#### **3. Early Return:**
```php
if (!$firstCourse) {
    return;
}
```

**معناها:**
- لو مفيش كورس، وقف فوراً
- بدل ما نعمل `if` جوا `if` جوا `if`

#### **4. Service Interaction:**
```php
$progress = $this->progressService->initializeCourseProgress($user, $firstCourse);
```

**معناها:**
- `UnlockService` بيستخدم `ProgressService`
- كل Service عنده مسئولية واحدة محددة

---

### 🎯 مثال: QuizService
```php
<?php

namespace App\Services;

use App\Models\User;
use App\Models\Quiz;
use App\Models\UserQuizAttempt;
use Carbon\Carbon;

class QuizService
{
    /**
     * تقديم الكويز وحساب النتيجة
     * 
     * @param User $user المستخدم
     * @param Quiz $quiz الكويز
     * @param array $answers إجابات المستخدم
     * @return UserQuizAttempt المحاولة
     * @throws \Exception لو المستخدم مينفعش يعيد المحاولة دلوقتي
     */
    public function submitQuiz(User $user, Quiz $quiz, array $answers): UserQuizAttempt
    {
        // 1️⃣ تحقق من آخر محاولة
        $lastAttempt = $this->getLastAttempt($user, $quiz);
        
        // 2️⃣ لو المستخدم مينفعش يعيد، ارمي Exception
        if ($lastAttempt && !$lastAttempt->canRetryNow()) {
            throw new \Exception('You must wait before retrying this quiz.');
        }
        
        // 3️⃣ ابدأ حساب النتيجة
        $score = 0;
        $maxScore = 0;
        $userAnswers = [];
        
        // 4️⃣ لف على كل الأسئلة
        foreach ($quiz->questions as $question) {
            $maxScore += $question->points;
            
            // جيب إجابة المستخدم لهذا السؤال
            $answerId = $answers[$question->id] ?? null;
            
            // لو مفيش إجابة، كمّل
            if (!$answerId) {
                continue;
            }
            
            // جيب الإجابة من الـ Database
            $answer = $question->answers()->find($answerId);
            
            if (!$answer) {
                continue;
            }
            
            // 5️⃣ تحقق لو الإجابة صح
            $isCorrect = $answer->is_correct;
            
            // 6️⃣ لو صح، زود النقاط
            if ($isCorrect) {
                $score += $question->points;
            }
            
            // 7️⃣ احفظ الإجابة
            $userAnswers[] = [
                'question_id' => $question->id,
                'answer_id' => $answerId,
                'is_correct' => $isCorrect,
            ];
        }
        
        // 8️⃣ احسب النسبة المئوية
        $passPercentage = $quiz->pass_percentage;
        $achievedPercentage = ($maxScore > 0) ? ($score / $maxScore) * 100 : 0;
        $passed = $achievedPercentage >= $passPercentage;
        
        // 9️⃣ احفظ المحاولة في الـ Database
        $attempt = UserQuizAttempt::create([
            'user_id' => $user->id,
            'quiz_id' => $quiz->id,
            'score' => $score,
            'max_score' => $maxScore,
            'passed' => $passed,
            'can_retry_at' => !$passed ? Carbon::now()->addHour() : null,
            'attempted_at' => now(),
        ]);
        
        // 🔟 احفظ كل إجابة
        foreach ($userAnswers as $userAnswer) {
            UserQuizAnswer::create([
                'attempt_id' => $attempt->id,
                'question_id' => $userAnswer['question_id'],
                'answer_id' => $userAnswer['answer_id'],
                'is_correct' => $userAnswer['is_correct'],
            ]);
        }
        
        return $attempt;
    }

    /**
     * جيب آخر محاولة للمستخدم في هذا الكويز
     */
    public function getLastAttempt(User $user, Quiz $quiz): ?UserQuizAttempt
    {
        return UserQuizAttempt::where('user_id', $user->id)
            ->where('quiz_id', $quiz->id)
            ->latest('attempted_at')
            ->first();
    }

    /**
     * تحقق لو المستخدم يقدر ياخد الكويز
     */
    public function canTakeQuiz(User $user, Quiz $quiz): bool
    {
        $lastAttempt = $this->getLastAttempt($user, $quiz);
        
        // لو مفيش محاولات قبل كدا، يقدر ياخد
        if (!$lastAttempt) {
            return true;
        }
        
        // لو نجح قبل كدا، مينفعش يعيد
        if ($lastAttempt->passed) {
            return false;
        }
        
        // تحقق لو فات ساعة من آخر محاولة
        return $lastAttempt->canRetryNow();
    }
}
```

### 🔍 نقاط مهمة:

#### **1. Exception Handling:**
```php
if ($lastAttempt && !$lastAttempt->canRetryNow()) {
    throw new \Exception('You must wait before retrying this quiz.');
}
```

**ليه نرمي Exception؟**
- Controller بيمسك الـ Exception ويرجع رسالة للمستخدم
- أحسن من return false (مش واضح إيه المشكلة)

#### **2. Null Coalescing Operator:**
```php
$answerId = $answers[$question->id] ?? null;
```

**معناها:**
- لو `$answers[$question->id]` موجود، استخدمه
- لو مش موجود، استخدم `null`
- بدل `isset()` و `if`

#### **3. Carbon للتعامل مع التواريخ:**
```php
'can_retry_at' => Carbon::now()->addHour()
```

**معناها:**
- الوقت الحالي + ساعة
- Carbon بيسهل التعامل مع التواريخ

---

## 5. Controllers - معالجة الطلبات

### 🎯 إيه دور الـ Controller؟

**Controller = بيستقبل الطلب ← بيعالجه ← بيرجع رد**

### 📝 بنية الـ Controller:
```php
<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Quiz;
use App\Services\QuizService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class QuizController extends Controller
{
    // 1️⃣ Constructor Injection
    public function __construct(
        private QuizService $quizService
    ) {}

    // 2️⃣ Show Quiz (GET /api/quizzes/{id})
    public function show($id)
    {
        $user = auth()->user();
        $quiz = Quiz::with('questions.answers')->findOrFail($id);

        // تحقق لو المستخدم يقدر ياخد الكويز
        if (!$this->quizService->canTakeQuiz($user, $quiz)) {
            $lastAttempt = $this->quizService->getLastAttempt($user, $quiz);
            
            return response()->json([
                'success' => false,
                'message' => 'Cannot take quiz at this time',
                'can_retry_at' => $lastAttempt?->can_retry_at
            ], 403);
        }

        // اخفي الإجابات الصحيحة
        $questions = $quiz->questions->map(function($question) {
            return [
                'id' => $question->id,
                'question_text' => $question->question_text,
                'points' => $question->points,
                'answers' => $question->answers->map(function($answer) {
                    return [
                        'id' => $answer->id,
                        'answer_text' => $answer->answer_text,
                        // ❌ مش بنرجع is_correct!
                    ];
                })
            ];
        });

        return response()->json([
            'success' => true,
            'data' => [
                'quiz' => $quiz,
                'questions' => $questions,
            ]
        ]);
    }

    // 3️⃣ Submit Quiz (POST /api/quizzes/{id}/submit)
    public function submit(Request $request, $id)
    {
        // Validation (تحقق من البيانات)
        $validator = Validator::make($request->all(), [
            'answers' => 'required|array',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $user = auth()->user();
        $quiz = Quiz::findOrFail($id);

        try {
            // استخدم Service لحساب النتيجة
            $attempt = $this->quizService->submitQuiz($user, $quiz, $request->answers);

            // لو نجح في Topic Quiz، افتح الموضوع التالي
            if ($quiz->isTopicQuiz() && $attempt->passed) {
                $topic = $quiz->topic;
                $course = $topic->courses()->first();
                
                if ($course) {
                    $this->unlockService->unlockNextTopicInCourse($user, $course, $topic);
                }
            }

            return response()->json([
                'success' => true,
                'message' => 'Quiz submitted successfully',
                'data' => $attempt
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        }
    }
}
```

### 🔍 شرح كل جزء:

#### **1. Route Parameters:**
```php
public function show($id)
```

**معناها:**
- الـ `$id` بييجي من الـ URL
- مثلاً: `/api/quizzes/5` ← `$id = 5`

#### **2. Eager Loading:**
```php
$quiz = Quiz::with('questions.answers')->findOrFail($id);
```

**ليه؟**
- بدل ما نعمل 100 query للـ Database
- نجيب الكويز + الأسئلة + الإجابات في query واحد
- أسرع بكتير!

#### **3. Auth Helper:**
```php
$user = auth()->user();
```

**معناها:**
- Laravel بيجيب المستخدم من الـ Token
- Middleware بيتأكد إن الـ Token صحيح قبل ما نوصل هنا

#### **4. HTTP Status Codes:**
```php
return response()->json([...], 403);
```

**الأكواد المهمة:**
- `200`: نجح (OK)
- `201`: اتعمل حاجة جديدة (Created)
- `400`: طلب غلط (Bad Request)
- `401`: مش مسموح (Unauthorized)
- `403`: ممنوع (Forbidden)
- `404`: مش موجود (Not Found)
- `422`: بيانات غلط (Validation Error)
- `500`: مشكلة في السيرفر (Server Error)

#### **5. Validation:**
```php
$validator = Validator::make($request->all(), [
    'answers' => 'required|array',
]);
```

**Rules مهمة:**
- `required`: لازم يكون موجود
- `array`: لازم يكون array
- `string`: لازم يكون text
- `email`: لازم يكون email صحيح
- `min:8`: على الأقل 8 characters
- `unique:users`: لازم يكون فريد في جدول users

#### **6. Try-Catch:**
```php
try {
    $attempt = $this->quizService->submitQuiz(...);
} catch (\Exception $e) {
    return response()->json([...], 400);
}
```

**ليه؟**
- لو حصل خطأ في Service، مش هينهار الـ App
- بنمسك الخطأ ونرجع رسالة واضحة

---

### 🎯 مثال: Admin Controller
```php
<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Track;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class TrackController extends Controller
{
    /**
     * GET /api/admin/tracks
     * جيب كل المسارات
     */
    public function index()
    {
        $tracks = Track::with(['creator', 'courses'])->get();

        return response()->json([
            'success' => true,
            'data' => $tracks
        ]);
    }

    /**
     * POST /api/admin/tracks
     * إنشاء مسار جديد
     */
    public function store(Request $request)
    {
        // 1️⃣ Validation
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'course_ids' => 'nullable|array',
            'course_ids.*' => 'exists:courses,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        // 2️⃣ إنشاء المسار
        $track = Track::create([
            'title' => $request->title,
            'description' => $request->description,
            'created_by' => auth()->id(),
        ]);

        // 3️⃣ ربط الكورسات بالمسار
        if ($request->has('course_ids')) {
            $courses = [];
            foreach ($request->course_ids as $index => $courseId) {
                $courses[$courseId] = ['order' => $index + 1];
            }
            $track->courses()->attach($courses);
        }

        // 4️⃣ رجّع المسار مع الكورسات
        return response()->json([
            'success' => true,
            'message' => 'Track created successfully',
            'data' => $track->load('courses')
        ], 201);
    }

    /**
     * PUT /api/admin/tracks/{id}
     * تحديث مسار
     */
    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'course_ids' => 'nullable|array',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $track = Track::findOrFail($id);

        // تحديث البيانات الأساسية
        $track->update([
            'title' => $request->title,
            'description' => $request->description,
        ]);

        // تحديث الكورسات (sync = مسح القديم وحط الجديد)
        if ($request->has('course_ids')) {
            $courses = [];
            foreach ($request->course_ids as $index => $courseId) {
                $courses[$courseId] = ['order' => $index + 1];
            }
            $track->courses()->sync($courses);
        }

        return response()->json([
            'success' => true,
            'message' => 'Track updated successfully',
            'data' => $track->load('courses')
        ]);
    }

    /**
     * DELETE /api/admin/tracks/{id}
     * حذف مسار
     */
    public function destroy($id)
    {
        $track = Track::findOrFail($id);
        $track->delete();  // Laravel بيمسح العلاقات تلقائياً (cascade)

        return response()->json([
            'success' => true,
            'message' => 'Track deleted successfully'
        ]);
    }
}
```

### 🔍 نقاط مهمة:

#### **1. Validation Rules:**
```php
'course_ids.*' => 'exists:courses,id'
```

**معناها:**
- كل عنصر في `course_ids` لازم يكون موجود في جدول `courses`
- `*` معناها "كل العناصر"

#### **2. Attach vs Sync:**
```php
$track->courses()->attach($courses);  // إضافة
$track->courses()->sync($courses);    // مسح + إضافة
```

**الفرق:**
- `attach`: يضيف على اللي موجود
- `sync`: يمسح القديم ويحط الجديد (أفضل للـ Update)

#### **3. Load Method:**
```php
$track->load('courses')
```

**معناها:**
- جيب الكورسات بعد ما خلصنا التعديلات
- عشان نرجعهم في الـ Response

---

## 6. Authentication & Security

### 🔐 JWT Authentication

#### **إيه هو JWT؟**

**JWT = JSON Web Token**

**شكله:**
```
eyJ0eXAiOiJKV1QiLCJhbGc...  ← Header
eyJzdWIiOiIxMjM0NTY3ODk...  ← Payload
SflKxwRJSMeKKF2QT4fwpM...  ← Signature
```

**كيف يشتغل؟**

1. المستخدم يعمل Login
2. السيرفر يرجع Token
3. الـ Frontend يحفظ الـ Token
4. في كل طلب، الـ Frontend يبعت الـ Token في Header
5. السيرفر يتحقق من الـ Token

---

### 📝 Login Flow:
```php
// LoginController
public function login(Request $request)
{
    // 1️⃣ Validation
    $validator = Validator::make($request->all(), [
        'email' => 'required|email',
        'password' => 'required|string',
    ]);

    if ($validator->fails()) {
        return response()->json([
            'success' => false,
            'errors' => $validator->errors()
        ], 422);
    }

    // 2️⃣ جيب الـ credentials
    $credentials = $request->only('email', 'password');

    // 3️⃣ حاول تعمل Login
    if (!$token = auth('api')->attempt($credentials)) {
        return response()->json([
            'success' => false,
            'message' => 'Invalid credentials'
        ], 401);
    }

    // 4️⃣ رجّع Token + User
    return response()->json([
        'success' => true,
        'message' => 'Login successful',
        'data' => [
            'user' => auth('api')->user(),
            'token' => $token,
            'token_type' => 'bearer',
        ]
    ]);
}
```

### 🔍 شرح:

#### **1. auth('api')->attempt():**
```php
auth('api')->attempt($credentials)
```

**معناها:**
- Laravel بيدور على الإيميل في الـ Database
- بيتحقق إن الباسورد صح (bcrypt)
- لو صح، بيولد Token ويرجعه

#### **2. Token في الـ Frontend:**
```javascript
// لما المستخدم يعمل Login:
localStorage.setItem('token', response.data.token);

// في كل طلب:
headers: {
    'Authorization': `Bearer ${token}`
}
```

---

### 🛡️ Middleware - حماية الـ Routes

#### **إيه هو Middleware؟**

**Middleware = حارس بيتحقق من الصلاحيات قبل ما يوصل للـ Controller**
```php
<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class IsAdmin
{
    public function handle(Request $request, Closure $next)
    {
        // 1️⃣ تحقق لو المستخدم مسجل دخول
        if (!auth()->check()) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthenticated.'
            ], 401);
        }

        // 2️⃣ تحقق لو المستخدم Admin
        if (!auth()->user()->isAdmin()) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized. Admin access required.'
            ], 403);
        }

        // 3️⃣ كمّل للـ Controller
        return $next($request);
    }
}
```

### 🔍 كيف يشتغل؟
```
Request → Middleware → Controller → Response
          ↓
          لو مش Admin → رجع 403
```

#### **استخدام Middleware في Routes:**
```php
// في routes/api.php:
Route::middleware('admin')->group(function () {
    Route::get('/admin/users', [AdminUserController::class, 'index']);
    Route::post('/admin/tracks', [AdminTrackController::class, 'store']);
});
```

---

### 🔒 Security Best Practices

#### **1. Password Hashing:**
```php
// ❌ خطأ فادح:
'password' => $request->password

// ✅ صح:
'password' => Hash::make($request->password)
```

**ليه؟**
- لو حد hack الـ Database، مش هيقدر يشوف Passwords
- bcrypt = one-way encryption (مينفعش يترجع)

#### **2. Mass Assignment Protection:**
```php
// في Model:
protected $fillable = ['name', 'email'];

// ❌ لو حد بعت:
{
    "name": "Ahmed",
    "role": "admin"  ← Laravel هيتجاهلها!
}
```

#### **3. SQL Injection Protection:**
```php
// ❌ خطر جداً:
$users = DB::select("SELECT * FROM users WHERE email = '$email'");

// ✅ آمن (Laravel Eloquent):
$user = User::where('email', $email)->first();
```

**ليه Laravel آمن؟**
- بيستخدم Prepared Statements
- بيهرب من Special Characters تلقائياً

#### **4. Rate Limiting:**
```php
// في routes/api.php:
Route::middleware('throttle:60,1')->group(function () {
    // 60 requests كل دقيقة
});
```

#### **5. CORS Configuration:**
```php
// في config/cors.php:
'allowed_origins' => ['http://localhost:3000'],
```

**ليه؟**
- بس المواقع المحددة تقدر تكلم الـ API
- حماية من CSRF attacks

---

## 7. Routes - ربط كل حاجة

### 🛣️ إيه هو الـ Route؟

**Route = يربط URL بـ Controller Method**
```php
Route::get('/tracks', [TrackController::class, 'index']);
       ↑         ↑                    ↑              ↑
    Method     URL              Controller        Method
```

---

### 📝 أنواع Routes:
```php
// GET - جيب بيانات
Route::get('/users', [UserController::class, 'index']);

// POST - اعمل حاجة جديدة
Route::post('/users', [UserController::class, 'store']);

// PUT/PATCH - حدّث حاجة موجودة
Route::put('/users/{id}', [UserController::class, 'update']);

// DELETE - امسح حاجة
Route::delete('/users/{id}', [UserController::class, 'destroy']);
```

---

### 🎯 Route Groups:
```php
// كل الـ Routes دي محتاجة Authentication
Route::middleware('auth:api')->group(function () {
    
    // Tracks Routes
    Route::prefix('tracks')->group(function () {
        Route::get('/', [TrackController::class, 'index']);
        Route::get('/{id}', [TrackController::class, 'show']);
        Route::post('/{id}/enroll', [TrackController::class, 'enroll']);
    });
    
    // Admin Routes
    Route::prefix('admin')->middleware('admin')->group(function () {
        Route::get('/users', [AdminUserController::class, 'index']);
    });
});
```

### 🔍 شرح:

#### **1. Middleware:**
```php
Route::middleware('auth:api')->group(...)
```

**معناها:**
- كل الـ Routes جوا الـ group دي محتاجة Token
- لو مفيش Token → 401 Unauthorized

#### **2. Prefix:**
```php
Route::prefix('tracks')->group(...)
```

**معناها:**
- كل الـ Routes جوا هيبدأوا بـ `/tracks`
- بدل ما نكرر `/tracks` في كل Route

#### **3. Multiple Middlewares:**
```php
Route::middleware(['auth:api', 'admin'])->group(...)
```

**معناها:**
- لازم يكون مسجل دخول
- **و** لازم يكون Admin

---

### 🗺️ Route Parameters:
```php
Route::get('/topics/{id}', [TopicController::class, 'show']);
```

**في Controller:**
```php
public function show($id)
{
    $topic = Topic::findOrFail($id);
    // ...
}
```

---

### 📋 Route List الكامل:
```php
<?php

use Illuminate\Support\Facades\Route;

// ========== Public Routes ==========
Route::get('/', function () {
    return response()->json([
        'success' => true,
        'message' => ' Riwaq API',
        'version' => '1.0.0'
    ]);
});

// ========== Auth Routes ==========
Route::prefix('auth')->group(function () {
    Route::post('register', [RegisterController::class, 'register']);
    Route::post('login', [LoginController::class, 'login']);
    
    Route::middleware('auth:api')->group(function () {
        Route::post('logout', [LogoutController::class, 'logout']);
    });
});

// ========== Protected Routes ==========
Route::middleware('auth:api')->group(function () {
    
    // Tracks
    Route::prefix('tracks')->group(function () {
        Route::get('/', [TrackController::class, 'index']);
        Route::get('/my-tracks', [TrackController::class, 'myTracks']);
        Route::get('/{id}', [TrackController::class, 'show']);
        Route::post('/{id}/enroll', [TrackController::class, 'enroll']);
    });

    // Courses
    Route::get('/courses/{id}', [CourseController::class, 'show