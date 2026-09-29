<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Track;
use App\Models\Course;
use App\Models\Topic;
use App\Models\Quiz;
use App\Models\Question;
use App\Models\Answer;
use Illuminate\Support\Facades\Hash;

class ExtraSeeder extends Seeder
{
    public function run(): void
    {
        echo " Running ExtraSeeder...\n\n";

        $admin = User::firstOrCreate(
            ['email' => 'Admin@Riwaq.com'],
            [
                'name'              => 'Admin',
                'password'          => Hash::make('password'),
                'role'              => 'admin',
                'email_verified_at' => now(),
            ]
        );

        // =============================================================
        // TRACK 1 – Android with Kotlin
        // =============================================================
        echo " Creating Track 1: Android with Kotlin...\n";
        $androidTrack = Track::create([
            'title'       => 'تطوير Android باستخدام Kotlin',
            'description' => 'تعلم بناء تطبيقات Android احترافية بلغة Kotlin من الصفر حتى النشر.',
            'created_by'  => $admin->id,
        ]);

        // Topic 1.1
        $at1 = Topic::create(['title' => 'مقدمة إلى Kotlin', 'type' => 'article', 'created_by' => $admin->id,
            'content' => '<h1>ما هي Kotlin؟</h1>
<p>Kotlin هي لغة برمجة حديثة <strong>تم تطويرها من قبل شركة JetBrains</strong> (نفس الشركة التي طورت IntelliJ IDEA)، وقد اعتمدتها Google رسمياً كاللغة الأولى لتطوير تطبيقات Android منذ عام 2019. تعمل على JVM (Java Virtual Machine) وهي متوافقة 100% مع Java.</p>

<h2>📊 مقارنة بين Kotlin و Java:</h2>
<table border="1" cellpadding="10" style="width:100%">
<tr><th>الميزة</th><th>Kotlin</th><th>Java</th></tr>
<tr><td>الأمان من NullPointerException</td><td>✅ محمية بشكل افتراضي</td><td>❌ عرضة للأخطاء</td></tr>
<tr><td>طول الكود</td><td>✅ أقصر وأنيق (40% أقل)</td><td>❌ طويل وممل</td></tr>
<tr><td>سهولة التعلم</td><td>✅ سهلة نسبياً</td><td>❌ أكثر تعقيداً</td></tr>
<tr><td>التوافق مع Java</td><td>✅ متوافقة 100%</td><td>✅ الأصلية</td></tr>
<tr><td>Null Safety</td><td>✅ مدمجة في اللغة</td><td>❌ يحتاج Optional</td></tr>
</table>

<h2>📝 مثال أول: Hello World</h2>
<pre><code>fun main() {
    val name = "أحمد"
    println("مرحباً يا $name!")  // النتيجة: مرحباً يا أحمد!
}</code></pre>
<p><strong>شرح الكود خطوة بخطوة:</strong></p>
<ul>
<li><strong><code>fun</code></strong>: كلمة حجوزة لتعريف دالة function</li>
<li><strong><code>main()</code></strong>: الدالة الرئيسية - تُعتبر نقطة بداية تنفيذ أي برنامج Kotlin</li>
<li><strong><code>val</code></strong>: متغير ثابت immutable - لا يمكن تغيير قيمته بعد التعريف</li>
<li><strong><code>"أحمد"</code></strong>: قيمة نصية من نوع String</li>
<li><strong><code>$name</code></strong>: استيفاء النصوص String Interpolation - إدراج قيمة المتغير داخل النص</li>
<li><strong><code>println()</code></strong>: طباعة النص مع إضافة سطر جديد في النهاية</li>
</ul>

<h2>🔀 الفرق بين val و var - مهم جداً!</h2>
<p><strong>val = Value (قيمة ثابتة):</strong></p>
<pre><code>val age = 25      // تُعرّف مرة واحدة فقط
age = 26          // ❌ خطأ! لا يمكن تغييره - سيحدث compile error</code></pre>

<p><strong>var = Variable (متغير):</strong></p>
<pre><code>var score = 100   // يمكن تغييره في أي وقت
score = 95        // ✅ صحيح تماماً
score = 85        // ✅ يمكن تغييره مرة أخرى</code></pre>

<p><strong>النصيحة الذهبية:</strong> استخدم <code>val</code> في البداية دائماً، واستخدم <code>var</code> فقط عند الحاجة الفعلية. هذا يجعل الكود أكثر أماناً وسهولة للقراءة.</p>

<h2>📦 الأنواع البيانية الأساسية</h2>
<pre><code>val number: Int = 42              // عدد صحيح
val price: Double = 19.99         // عدد عشري
val count: Float = 3.14f          // عدد عشري (أقل دقة)
val name: String = "محمد"          // نص
val isActive: Boolean = true      // صواب/خطأ
val character: Char = \'A\'       // حرف واحد فقط
val largeNumber: Long = 1000000L  // عدد كبير

// Kotlin يتعرّف على النوع تلقائياً (Type Inference)
val score = 95           // تُعرّف تلقائياً كـ Int
val message = "مرحباً"   // تُعرّف تلقائياً كـ String
</code></pre>

<h2>🎯 مميزات Kotlin الرئيسية</h2>

<h3>1️⃣ Null Safety - الحماية من NullPointerException</h3>
<p>أخطر أخطاء Java هي <code>NullPointerException</code>. Kotlin تحل هذه المشكلة بشكل مدمج في اللغة:</p>
<pre><code>// لا يقبل null - آمن تماماً
var name: String = "محمد"
name = null  // ❌ خطأ في وقت الكتابة!

// يقبل null - اختياري
var optional: String? = null    // ✅ صحيح
optional = "علي"                // ✅ صحيح

// Safe Call Operator (?.) - آمن في الاستخدام
val length = optional?.length   // إذا كان null، النتيجة null، وإلا النتيجة العدد
</code></pre>

<h3>2️⃣ Type Inference - تعرّف تلقائي على نوع المتغير</h3>
<pre><code>val score = 95              // Kotlin تعرّف تلقائياً أنه Int
val name = "أحمد"          // Kotlin تعرّف تلقائياً أنه String
val active = true           // Kotlin تعرّف تلقائياً أنه Boolean

// بدلاً من Java الممل:
int score = 95;
String name = "أحمد";
boolean active = true;
</code></pre>

<h3>3️⃣ Data Classes - فئات متخصصة للبيانات</h3>
<pre><code>data class User(val id: Int, val name: String, val email: String)

val user = User(1, "محمد", "mohamad@example.com")
// ينتج تلقائياً:
// - equals()   : مقارنة الكائنات
// - hashCode() : للاستخدام في الخرائط
// - toString() : "User(id=1, name=محمد, email=mohamad@example.com)"
// - copy()     : نسخ مع تغيير بعض البيانات

val user2 = user.copy(id = 2)  // نسخة جديدة بـ id مختلف
</code></pre>

<h3>4️⃣ Extension Functions - إضافة دوال لفئات موجودة</h3>
<pre><code>// إضافة دالة جديدة للـ String بدون توريث
fun String.isValidEmail(): Boolean {
    return this.contains("@") && this.contains(".")
}

// الاستخدام:
val email = "user@example.com"
if (email.isValidEmail()) {
    println("البريد صحيح")
}
</code></pre>

<h3>5️⃣ Lambda Expressions - دوال مجهولة وقوية</h3>
<pre><code>val numbers = listOf(1, 2, 3, 4, 5)

// Filter: تصفية الأرقام الأكبر من 2
numbers.filter { it > 2 }           // [3, 4, 5]

// Map: تحويل كل عنصر (الضرب × 2)
numbers.map { it * 2 }              // [2, 4, 6, 8, 10]

// Sum: جمع كل الأرقام
numbers.sum()                       // 15

// forEach: تنفيذ عملية على كل عنصر
numbers.forEach { println(it) }     // طباعة 1، 2، 3، 4، 5
</code></pre>

<h3>6️⃣ Coroutines - البرمجة غير المتزامنة</h3>
<pre><code>// تحميل البيانات من الإنترنت بدون تجميد الواجهة
GlobalScope.launch {
    val data = fetchDataFromServer()  // قد تستغرق وقتاً
    updateUI(data)                    // تحديث الواجهة بعد انتهاء التحميل
}
</code></pre>

<h2>🚀 لماذا تختار Kotlin لـ Android؟</h2>
<ol>
<li><strong>الأمان:</strong> تقليل الأخطاء بشكل جذري بفضل Null Safety المدمجة</li>
<li><strong>الإنتاجية:</strong> كود أقل بـ 40% مقارنة بـ Java مع وظائف أكثر</li>
<li><strong>الأداء:</strong> أداء مماثل لـ Java تماماً (تُترجم إلى نفس bytecode)</li>
<li><strong>الدعم الرسمي:</strong> Google تدعمها رسمياً وتوفر مكتبات Jetpack كاملة لها</li>
<li><strong>الصيانة:</strong> سهولة الصيانة والقراءة والتطوير المستقبلي</li>
<li><strong>التعلم:</strong> قوس تعلم أقل من Java مع مرونة أكثر</li>
</ol>

<h2>📚 الخطوات التالية</h2>
<p>بعد فهم أساسيات Kotlin، ستتعلم:</p>
<ul>
<li>Activity و Lifecycle</li>
<li>RecyclerView للقوائم الطويلة</li>
<li>Jetpack Compose لبناء الواجهات الحديثة</li>
<li>قواعد البيانات مع Room</li>
<li>الشبكات والـ APIs</li>
<li>تطبيقات حقيقية متكاملة</li>
</ul>']);

        $aq = Quiz::create(['title' => 'اختبار مقدمة Kotlin', 'type' => 'topic', 'topic_id' => $at1->id, 'total_points' => 50, 'pass_percentage' => 60, 'created_by' => $admin->id]);
        $q = Question::create(['quiz_id' => $aq->id, 'question_text' => 'ما هي اللغة الرسمية لتطوير Android؟', 'points' => 10]);
        Answer::create(['question_id' => $q->id, 'answer_text' => 'Kotlin', 'is_correct' => true]);
        Answer::create(['question_id' => $q->id, 'answer_text' => 'Java', 'is_correct' => false]);
        Answer::create(['question_id' => $q->id, 'answer_text' => 'Swift', 'is_correct' => false]);
        Answer::create(['question_id' => $q->id, 'answer_text' => 'Dart', 'is_correct' => false]);

        $q = Question::create(['quiz_id' => $aq->id, 'question_text' => 'ما الفرق بين val و var في Kotlin؟', 'points' => 10]);
        Answer::create(['question_id' => $q->id, 'answer_text' => 'val ثابت لا يتغير، var قابل للتغيير', 'is_correct' => true]);
        Answer::create(['question_id' => $q->id, 'answer_text' => 'val قابل للتغيير، var ثابت', 'is_correct' => false]);
        Answer::create(['question_id' => $q->id, 'answer_text' => 'لا فرق بينهما', 'is_correct' => false]);

        $q = Question::create(['quiz_id' => $aq->id, 'question_text' => 'ما هي بيئة التشغيل التي تعمل عليها Kotlin؟', 'points' => 10]);
        Answer::create(['question_id' => $q->id, 'answer_text' => 'JVM', 'is_correct' => true]);
        Answer::create(['question_id' => $q->id, 'answer_text' => 'CLR', 'is_correct' => false]);
        Answer::create(['question_id' => $q->id, 'answer_text' => 'Node.js', 'is_correct' => false]);

        $q = Question::create(['quiz_id' => $aq->id, 'question_text' => 'أي مما يلي صحيح لطباعة نص في Kotlin؟', 'points' => 10]);
        Answer::create(['question_id' => $q->id, 'answer_text' => 'println("Hello")', 'is_correct' => true]);
        Answer::create(['question_id' => $q->id, 'answer_text' => 'print.out("Hello")', 'is_correct' => false]);
        Answer::create(['question_id' => $q->id, 'answer_text' => 'echo "Hello"', 'is_correct' => false]);

        $q = Question::create(['quiz_id' => $aq->id, 'question_text' => 'كيف تعرّف دالة في Kotlin؟', 'points' => 10]);
        Answer::create(['question_id' => $q->id, 'answer_text' => 'fun myFunction() {}', 'is_correct' => true]);
        Answer::create(['question_id' => $q->id, 'answer_text' => 'function myFunction() {}', 'is_correct' => false]);
        Answer::create(['question_id' => $q->id, 'answer_text' => 'def myFunction() {}', 'is_correct' => false]);

        $q = Question::create(['quiz_id' => $aq->id, 'question_text' => 'ما الشركة التي طوّرت لغة Kotlin؟', 'points' => 10]);
        Answer::create(['question_id' => $q->id, 'answer_text' => 'JetBrains', 'is_correct' => true]);
        Answer::create(['question_id' => $q->id, 'answer_text' => 'Google', 'is_correct' => false]);
        Answer::create(['question_id' => $q->id, 'answer_text' => 'Oracle', 'is_correct' => false]);
        Answer::create(['question_id' => $q->id, 'answer_text' => 'Apple', 'is_correct' => false]);

        $q = Question::create(['quiz_id' => $aq->id, 'question_text' => 'ما معنى Null Safety في Kotlin؟', 'points' => 10]);
        Answer::create(['question_id' => $q->id, 'answer_text' => 'حماية الكود من NullPointerException في وقت الترجمة', 'is_correct' => true]);
        Answer::create(['question_id' => $q->id, 'answer_text' => 'حذف القيم الفارغة تلقائياً', 'is_correct' => false]);
        Answer::create(['question_id' => $q->id, 'answer_text' => 'منع استخدام null كلياً', 'is_correct' => false]);

        $q = Question::create(['quiz_id' => $aq->id, 'question_text' => 'كيف تُعرّف متغيراً يقبل null في Kotlin؟', 'points' => 10]);
        Answer::create(['question_id' => $q->id, 'answer_text' => 'var name: String? = null', 'is_correct' => true]);
        Answer::create(['question_id' => $q->id, 'answer_text' => 'var name: String = null', 'is_correct' => false]);
        Answer::create(['question_id' => $q->id, 'answer_text' => 'nullable var name: String', 'is_correct' => false]);

        $q = Question::create(['quiz_id' => $aq->id, 'question_text' => 'ما عامل Safe Call في Kotlin؟', 'points' => 10]);
        Answer::create(['question_id' => $q->id, 'answer_text' => '?. (مثال: name?.length)', 'is_correct' => true]);
        Answer::create(['question_id' => $q->id, 'answer_text' => '!! (مثال: name!!.length)', 'is_correct' => false]);
        Answer::create(['question_id' => $q->id, 'answer_text' => '?: (Elvis operator)', 'is_correct' => false]);

        $q = Question::create(['quiz_id' => $aq->id, 'question_text' => 'ما عامل Elvis في Kotlin؟', 'points' => 10]);
        Answer::create(['question_id' => $q->id, 'answer_text' => '?: يُعيد قيمة افتراضية إذا كان التعبير null', 'is_correct' => true]);
        Answer::create(['question_id' => $q->id, 'answer_text' => '?. للاستدعاء الآمن', 'is_correct' => false]);
        Answer::create(['question_id' => $q->id, 'answer_text' => '!! لإجبار القيمة غير الفارغة', 'is_correct' => false]);

        $q = Question::create(['quiz_id' => $aq->id, 'question_text' => 'ما الـ data class في Kotlin؟', 'points' => 10]);
        Answer::create(['question_id' => $q->id, 'answer_text' => 'كلاس يُولّد تلقائياً equals وhashCode وcopy وtoString', 'is_correct' => true]);
        Answer::create(['question_id' => $q->id, 'answer_text' => 'كلاس لتخزين البيانات في قاعدة بيانات', 'is_correct' => false]);
        Answer::create(['question_id' => $q->id, 'answer_text' => 'كلاس لا يملك دوال', 'is_correct' => false]);

        $q = Question::create(['quiz_id' => $aq->id, 'question_text' => 'ما الـ object keyword في Kotlin؟', 'points' => 10]);
        Answer::create(['question_id' => $q->id, 'answer_text' => 'إنشاء Singleton أو anonymous object', 'is_correct' => true]);
        Answer::create(['question_id' => $q->id, 'answer_text' => 'إنشاء كلاس عادي', 'is_correct' => false]);
        Answer::create(['question_id' => $q->id, 'answer_text' => 'تعريف interface', 'is_correct' => false]);

        $q = Question::create(['quiz_id' => $aq->id, 'question_text' => 'ما الـ companion object في Kotlin؟', 'points' => 10]);
        Answer::create(['question_id' => $q->id, 'answer_text' => 'يُستخدم كـ static members داخل الكلاس', 'is_correct' => true]);
        Answer::create(['question_id' => $q->id, 'answer_text' => 'كلاس مساعد خارجي', 'is_correct' => false]);
        Answer::create(['question_id' => $q->id, 'answer_text' => 'نوع من Singleton العام', 'is_correct' => false]);

        $q = Question::create(['quiz_id' => $aq->id, 'question_text' => 'ما الـ extension function في Kotlin؟', 'points' => 10]);
        Answer::create(['question_id' => $q->id, 'answer_text' => 'إضافة دوال جديدة لكلاس موجود بدون وراثة', 'is_correct' => true]);
        Answer::create(['question_id' => $q->id, 'answer_text' => 'وراثة كلاس وإضافة دوال', 'is_correct' => false]);
        Answer::create(['question_id' => $q->id, 'answer_text' => 'تعديل الكلاس الأصلي مباشرة', 'is_correct' => false]);

        $q = Question::create(['quiz_id' => $aq->id, 'question_text' => 'ما الـ lambda في Kotlin؟', 'points' => 10]);
        Answer::create(['question_id' => $q->id, 'answer_text' => 'دالة مجهولة تُكتب بين أقواس معقوفة { }', 'is_correct' => true]);
        Answer::create(['question_id' => $q->id, 'answer_text' => 'نوع من المتغيرات', 'is_correct' => false]);
        Answer::create(['question_id' => $q->id, 'answer_text' => 'كلاس مجهول', 'is_correct' => false]);

        // Topic 1.2
        $at2 = Topic::create(['title' => 'Activities و Lifecycle', 'type' => 'article', 'created_by' => $admin->id,
            'content' => '<h1>🎬 Android Activity Lifecycle - دورة حياة النشاط</h1>

<p><strong>ما هي الـ Activity؟</strong></p>
<p>الـ Activity تمثل <strong>شاشة واحدة</strong> في تطبيق Android. كل شاشة تراها في التطبيق (مثل شاشة تسجيل الدخول أو الصفحة الرئيسية) تُعتبر Activity واحدة. كل Activity لها دورة حياة محددة تبدأ من الإنشاء وتنتهي بالحذف.</p>

<h2>📊 مراحل دورة حياة Activity</h2>

<p><strong>يوجد 6 مراحل أساسية:</strong></p>

<h3>1️⃣ onCreate() - الإنشاء</h3>
<pre><code>override fun onCreate(savedInstanceState: Bundle?) {
    super.onCreate(savedInstanceState)
    setContentView(R.layout.activity_main)
    
    // المنطق هنا:
    // - تعريف المتغيرات
    // - ربط الـ Views
    // - تحميل البيانات الأولية
}
</code></pre>
<p><strong>متى تُستدعى؟</strong> عند إنشاء الـ Activity لأول مرة.</p>
<p><strong>الاستخدام:</strong></p>
<ul>
<li>تعيين الـ layout XML</li>
<li>تهيئة المتغيرات والـ Views</li>
<li>تحميل البيانات الثابتة</li>
<li>استعادة البيانات المحفوظة إذا كانت موجودة</li>
</ul>

<h3>2️⃣ onStart() - البدء</h3>
<pre><code>override fun onStart() {
    super.onStart()
    // Activity أصبحت مرئية للمستخدم
    // لكن قد لا تكون في المقدمة بعد
}
</code></pre>
<p><strong>متى تُستدعى؟</strong> عندما تصبح الـ Activity مرئية للمستخدم (لكن قد تكون خلف Activity أخرى).</p>
<p><strong>الاستخدام:</strong></p>
<ul>
<li>بدء تسجيل مستشعرات الجهاز</li>
<li>تشغيل الرسوميات أو الأنيميشن</li>
<li>تحديث الواجهة حسب الحالة الحالية</li>
</ul>

<h3>3️⃣ onResume() - الاستئناف</h3>
<pre><code>override fun onResume() {
    super.onResume()
    // Activity في المقدمة والمستخدم يتفاعل معها
    startCamera()      // مثال: بدء كاميرا
    startListening()   // مثال: بدء الاستماع
}
</code></pre>
<p><strong>متى تُستدعى؟</strong> عندما تصبح الـ Activity في المقدمة والمستخدم يتفاعل معها مباشرة.</p>
<p><strong>الاستخدام:</strong></p>
<ul>
<li>بدء الكاميرا أو الميكروفون</li>
<li>بدء الرسوميات الثقيلة</li>
<li>تحديث الواجهة بالبيانات الحية</li>
<li>التحقق من الأذونات</li>
</ul>

<h3>4️⃣ onPause() - الإيقاف</h3>
<pre><code>override fun onPause() {
    super.onPause()
    // Activity فقدت التركيز جزئياً
    // قد تكون Activity أخرى ظهرت فوقها (مثل dialog)
    stopCamera()       // إيقاف الموارد الثقيلة
    pauseMusic()       // إيقاف الموسيقى
}
</code></pre>
<p><strong>متى تُستدعى؟</strong> عندما تفقد الـ Activity التركيز جزئياً (مثل ظهور dialog أو شاشة أخرى فوقها).</p>
<p><strong>الاستخدام:</strong></p>
<ul>
<li>إيقاف الموارد الثقيلة (كاميرا، ميكروفون)</li>
<li>حفظ البيانات تلقائياً</li>
<li>إيقاف الرسوميات والأنيميشن</li>
<li>التوقف عن الاستماع للمستشعرات</li>
</ul>

<h3>5️⃣ onStop() - التوقف</h3>
<pre><code>override fun onStop() {
    super.onStop()
    // Activity غير مرئية نهائياً
    // قد تكون محذوفة من الذاكرة لاحقاً
    cancelNetworkRequests()  // إلغاء طلبات الإنترنت
}
</code></pre>
<p><strong>متى تُستدعى؟</strong> عندما لا تكون الـ Activity مرئية للمستخدم (مثل الضغط على الزر back أو فتح تطبيق آخر).</p>
<p><strong>الاستخدام:</strong></p>
<ul>
<li>إلغاء طلبات الشبكة</li>
<li>حفظ البيانات المهمة في قاعدة البيانات</li>
<li>تحرير الموارد</li>
</ul>

<h3>6️⃣ onDestroy() - الحذف</h3>
<pre><code>override fun onDestroy() {
    super.onDestroy()
    // Activity محذوفة وستُزال من الذاكرة
    cleanupResources()  // تنظيف جميع الموارد
}
</code></pre>
<p><strong>متى تُستدعى؟</strong> عند حذف الـ Activity نهائياً من الذاكرة.</p>
<p><strong>الاستخدام:</strong></p>
<ul>
<li>تحرير الموارد النهائية</li>
<li>إغلاق الاتصالات</li>
<li>حذف الـ listeners والـ callbacks</li>
</ul>

<h2>📈 رسم بياني لدورة الحياة</h2>
<pre><code>
┌─────────────────────────────────────┐
│         onCreate()                  │  ← الإنشاء
└─────────────────────────────────────┘
                  ↓
┌─────────────────────────────────────┐
│         onStart()                   │  ← الظهور
└─────────────────────────────────────┘
                  ↓
┌─────────────────────────────────────┐
│         onResume()                  │  ← التركيز
└─────────────────────────────────────┘
                  ↓
            ↙━━━━━┛
         (Activity مرئية ومركزة)
            ↖━━━━━┓
                  ↓
┌─────────────────────────────────────┐
│         onPause()                   │  ← فقدان التركيز
└─────────────────────────────────────┘
                  ↓
┌─────────────────────────────────────┐
│         onStop()                    │  ← الاختفاء
└─────────────────────────────────────┘
                  ↓
┌─────────────────────────────────────┐
│         onDestroy()                 │  ← الحذف
└─────────────────────────────────────┘
</code></pre>

<h2>💾 حفظ واستعادة البيانات</h2>

<p><strong>onSaveInstanceState():</strong> تُستدعى قبل حذف الـ Activity لحفظ البيانات المهمة (مثل عند تدوير الهاتف)</p>
<pre><code>override fun onSaveInstanceState(outState: Bundle) {
    super.onSaveInstanceState(outState)
    
    // حفظ البيانات المهمة في Bundle
    outState.putString("user_name", userName)
    outState.putInt("score", score)
    outState.putBoolean("is_logged_in", isLoggedIn)
}
</code></pre>

<p><strong>onRestoreInstanceState():</strong> تُستدعى عند إعادة إنشاء الـ Activity لاستعادة البيانات المحفوظة</p>
<pre><code>override fun onRestoreInstanceState(savedInstanceState: Bundle?) {
    super.onRestoreInstanceState(savedInstanceState)
    
    if (savedInstanceState != null) {
        val userName = savedInstanceState.getString("user_name", "")
        val score = savedInstanceState.getInt("score", 0)
        val isLoggedIn = savedInstanceState.getBoolean("is_logged_in", false)
    }
}
</code></pre>

<h2>🎯 مثال عملي كامل</h2>
<pre><code>class MainActivity : AppCompatActivity() {
    
    override fun onCreate(savedInstanceState: Bundle?) {
        super.onCreate(savedInstanceState)
        setContentView(R.layout.activity_main)
        Log.d("Lifecycle", "onCreate")
    }
    
    override fun onStart() {
        super.onStart()
        Log.d("Lifecycle", "onStart")
    }
    
    override fun onResume() {
        super.onResume()
        Log.d("Lifecycle", "onResume - Activity في المقدمة")
    }
    
    override fun onPause() {
        super.onPause()
        Log.d("Lifecycle", "onPause - Activity فقدت التركيز")
    }
    
    override fun onStop() {
        super.onStop()
        Log.d("Lifecycle", "onStop - Activity غير مرئية")
    }
    
    override fun onDestroy() {
        super.onDestroy()
        Log.d("Lifecycle", "onDestroy - Activity محذوفة")
    }
}
</code></pre>

<p><strong>النتيجة عند التشغيل:</strong></p>
<pre><code>onCreate
onStart
onResume
</code></pre>

<p><strong>عند الضغط على الزر back:</strong></p>
<pre><code>onPause
onStop
onDestroy
</code></pre>

<h2>⚠️ نصائح مهمة</h2>
<ol>
<li><strong>استخدم onResume() و onPause():</strong> للموارد الثقيلة (كاميرا، ميكروفون، موسيقى)</li>
<li><strong>استخدم onDestroy():</strong> لتنظيف الموارد الأخيرة فقط</li>
<li><strong>تجنب العمليات الثقيلة في onCreate():</strong> قد تؤدي لتأخير ظهور الشاشة</li>
<li><strong>حفظ البيانات تدريجياً:</strong> بدلاً من حفظها كلها في onPause()</li>
<li><strong>تذكر super.</strong> قبل كل استدعاء دالة في الـ Activity</li>
</ol>']);

        $aq = Quiz::create(['title' => 'اختبار Activity Lifecycle', 'type' => 'topic', 'topic_id' => $at2->id, 'total_points' => 50, 'pass_percentage' => 60, 'created_by' => $admin->id]);
        $q = Question::create(['quiz_id' => $aq->id, 'question_text' => 'أول دالة تُستدعى عند إنشاء Activity؟', 'points' => 10]);
        Answer::create(['question_id' => $q->id, 'answer_text' => 'onCreate()', 'is_correct' => true]);
        Answer::create(['question_id' => $q->id, 'answer_text' => 'onStart()', 'is_correct' => false]);
        Answer::create(['question_id' => $q->id, 'answer_text' => 'onResume()', 'is_correct' => false]);

        $q = Question::create(['quiz_id' => $aq->id, 'question_text' => 'ما الدالة التي تُستدعى عندما تصبح Activity مرئية للمستخدم؟', 'points' => 10]);
        Answer::create(['question_id' => $q->id, 'answer_text' => 'onStart()', 'is_correct' => true]);
        Answer::create(['question_id' => $q->id, 'answer_text' => 'onCreate()', 'is_correct' => false]);
        Answer::create(['question_id' => $q->id, 'answer_text' => 'onResume()', 'is_correct' => false]);

        $q = Question::create(['quiz_id' => $aq->id, 'question_text' => 'ما الدالة التي تُستدعى عند إغلاق Activity نهائياً؟', 'points' => 10]);
        Answer::create(['question_id' => $q->id, 'answer_text' => 'onDestroy()', 'is_correct' => true]);
        Answer::create(['question_id' => $q->id, 'answer_text' => 'onStop()', 'is_correct' => false]);
        Answer::create(['question_id' => $q->id, 'answer_text' => 'onPause()', 'is_correct' => false]);

        $q = Question::create(['quiz_id' => $aq->id, 'question_text' => 'متى تُستدعى onPause()؟', 'points' => 10]);
        Answer::create(['question_id' => $q->id, 'answer_text' => 'عندما تفقد Activity التركيز جزئياً', 'is_correct' => true]);
        Answer::create(['question_id' => $q->id, 'answer_text' => 'عند إنشاء الـ Activity', 'is_correct' => false]);
        Answer::create(['question_id' => $q->id, 'answer_text' => 'عند حذف الـ Activity', 'is_correct' => false]);

        $q = Question::create(['quiz_id' => $aq->id, 'question_text' => 'ما البيان المستخدم لتعريف Activity في AndroidManifest؟', 'points' => 10]);
        Answer::create(['question_id' => $q->id, 'answer_text' => '<activity>', 'is_correct' => true]);
        Answer::create(['question_id' => $q->id, 'answer_text' => '<screen>', 'is_correct' => false]);
        Answer::create(['question_id' => $q->id, 'answer_text' => '<view>', 'is_correct' => false]);

        $q = Question::create(['quiz_id' => $aq->id, 'question_text' => 'ما الحالة التي تُستدعى فيها onResume()؟', 'points' => 10]);
        Answer::create(['question_id' => $q->id, 'answer_text' => 'عندما تصبح Activity في المقدمة وتكتسب تركيز المستخدم', 'is_correct' => true]);
        Answer::create(['question_id' => $q->id, 'answer_text' => 'عند إنشاء Activity لأول مرة', 'is_correct' => false]);
        Answer::create(['question_id' => $q->id, 'answer_text' => 'عند إغلاق Activity', 'is_correct' => false]);

        $q = Question::create(['quiz_id' => $aq->id, 'question_text' => 'ما الدالة التي تُحفظ فيها البيانات قبل إغلاق Activity؟', 'points' => 10]);
        Answer::create(['question_id' => $q->id, 'answer_text' => 'onSaveInstanceState()', 'is_correct' => true]);
        Answer::create(['question_id' => $q->id, 'answer_text' => 'onPause()', 'is_correct' => false]);
        Answer::create(['question_id' => $q->id, 'answer_text' => 'onStop()', 'is_correct' => false]);

        $q = Question::create(['quiz_id' => $aq->id, 'question_text' => 'ما الدالة لاستعادة البيانات المحفوظة في Bundle؟', 'points' => 10]);
        Answer::create(['question_id' => $q->id, 'answer_text' => 'onRestoreInstanceState()', 'is_correct' => true]);
        Answer::create(['question_id' => $q->id, 'answer_text' => 'onResume()', 'is_correct' => false]);
        Answer::create(['question_id' => $q->id, 'answer_text' => 'onStart()', 'is_correct' => false]);

        $q = Question::create(['quiz_id' => $aq->id, 'question_text' => 'ما الفرق بين onStop() وonDestroy()؟', 'points' => 10]);
        Answer::create(['question_id' => $q->id, 'answer_text' => 'onStop تجعل الـ Activity غير مرئية، onDestroy تُنهيها تماماً', 'is_correct' => true]);
        Answer::create(['question_id' => $q->id, 'answer_text' => 'لا فرق بينهما', 'is_correct' => false]);
        Answer::create(['question_id' => $q->id, 'answer_text' => 'onDestroy تُوقف الـ Activity مؤقتاً', 'is_correct' => false]);

        $q = Question::create(['quiz_id' => $aq->id, 'question_text' => 'ما الـ Back Stack في Android؟', 'points' => 10]);
        Answer::create(['question_id' => $q->id, 'answer_text' => 'مكدس يتتبع Activities المفتوحة للتنقل للخلف', 'is_correct' => true]);
        Answer::create(['question_id' => $q->id, 'answer_text' => 'نوع من التخزين المؤقت', 'is_correct' => false]);
        Answer::create(['question_id' => $q->id, 'answer_text' => 'قائمة التطبيقات المثبتة', 'is_correct' => false]);

        $q = Question::create(['quiz_id' => $aq->id, 'question_text' => 'ما الـ launchMode للـ Activity؟', 'points' => 10]);
        Answer::create(['question_id' => $q->id, 'answer_text' => 'يُحدد كيفية إنشاء نسخ Activity في الـ Back Stack', 'is_correct' => true]);
        Answer::create(['question_id' => $q->id, 'answer_text' => 'سرعة تشغيل الـ Activity', 'is_correct' => false]);
        Answer::create(['question_id' => $q->id, 'answer_text' => 'نمط التصميم', 'is_correct' => false]);

        $q = Question::create(['quiz_id' => $aq->id, 'question_text' => 'ما الـ Fragment في Android؟', 'points' => 10]);
        Answer::create(['question_id' => $q->id, 'answer_text' => 'جزء من واجهة المستخدم يمكن إعادة استخدامه داخل Activity', 'is_correct' => true]);
        Answer::create(['question_id' => $q->id, 'answer_text' => 'Activity صغيرة مستقلة', 'is_correct' => false]);
        Answer::create(['question_id' => $q->id, 'answer_text' => 'نوع من الـ Service', 'is_correct' => false]);

        $q = Question::create(['quiz_id' => $aq->id, 'question_text' => 'ما الـ Context في Android؟', 'points' => 10]);
        Answer::create(['question_id' => $q->id, 'answer_text' => 'كائن يوفر وصولاً لموارد التطبيق والنظام', 'is_correct' => true]);
        Answer::create(['question_id' => $q->id, 'answer_text' => 'نوع من الـ View', 'is_correct' => false]);
        Answer::create(['question_id' => $q->id, 'answer_text' => 'قاعدة بيانات محلية', 'is_correct' => false]);

        $q = Question::create(['quiz_id' => $aq->id, 'question_text' => 'ما التطبيق الأفضل لـ Multi-Window في Android؟', 'points' => 10]);
        Answer::create(['question_id' => $q->id, 'answer_text' => 'استخدام onMultiWindowModeChanged للتعامل مع حالة المشاركة', 'is_correct' => true]);
        Answer::create(['question_id' => $q->id, 'answer_text' => 'منع الـ Multi-Window كلياً', 'is_correct' => false]);
        Answer::create(['question_id' => $q->id, 'answer_text' => 'إنشاء Activity جديدة لكل نافذة', 'is_correct' => false]);

        // Topic 1.3
        $at3 = Topic::create(['title' => 'RecyclerView والقوائم', 'type' => 'article', 'created_by' => $admin->id,
            'content' => '<h1>📋 RecyclerView – عرض القوائم في Android باحترافية</h1>

<p>RecyclerView هو المكون الأساسي في Android لعرض <strong>قوائم طويلة من البيانات بكفاءة عالية</strong>. بدلاً من إنشاء View لكل عنصر (كما كان يفعل ListView القديم)، يُعيد RecyclerView استخدام Views التي خرجت من الشاشة لعرض العناصر الجديدة – ومن هنا جاء اسمه "Recycler".</p>

<h2>🔄 لماذا RecyclerView أفضل من ListView؟</h2>
<table border="1" cellpadding="10" style="width:100%">
<tr><th>الميزة</th><th>RecyclerView</th><th>ListView</th></tr>
<tr><td>إعادة استخدام Views</td><td>✅ إلزامي بالـ ViewHolder</td><td>❌ اختياري</td></tr>
<tr><td>اتجاه العرض</td><td>✅ عمودي، أفقي، شبكة</td><td>❌ عمودي فقط</td></tr>
<tr><td>الأنيميشن</td><td>✅ مدمجة (ItemAnimator)</td><td>❌ يدوي</td></tr>
<tr><td>التخصيص</td><td>✅ مرن جداً</td><td>❌ محدود</td></tr>
<tr><td>الأداء</td><td>✅ ممتاز مع آلاف العناصر</td><td>❌ بطيء مع قوائم كبيرة</td></tr>
</table>

<h2>🏗️ المكونات الأساسية لـ RecyclerView</h2>
<p>يتكون RecyclerView من 3 أجزاء رئيسية:</p>
<ol>
<li><strong>Adapter:</strong> يربط البيانات بالـ Views – هو الوسيط بين البيانات والواجهة</li>
<li><strong>ViewHolder:</strong> يحفظ مرجعاً لعناصر الواجهة – يمنع استدعاء <code>findViewById()</code> المتكرر</li>
<li><strong>LayoutManager:</strong> يُحدد طريقة ترتيب العناصر (عمودي، أفقي، شبكي)</li>
</ol>

<h2>📝 مثال عملي كامل: قائمة طلاب</h2>

<h3>الخطوة 1: تعريف ملف الـ Layout لكل عنصر (item_student.xml)</h3>
<pre><code>&lt;LinearLayout
    android:layout_width="match_parent"
    android:layout_height="wrap_content"
    android:orientation="horizontal"
    android:padding="16dp"&gt;

    &lt;ImageView
        android:id="@+id/imgAvatar"
        android:layout_width="48dp"
        android:layout_height="48dp" /&gt;

    &lt;LinearLayout
        android:orientation="vertical"
        android:layout_marginStart="12dp"&gt;

        &lt;TextView
            android:id="@+id/tvName"
            android:textSize="18sp"
            android:textStyle="bold" /&gt;

        &lt;TextView
            android:id="@+id/tvGrade"
            android:textSize="14sp"
            android:textColor="#888" /&gt;
    &lt;/LinearLayout&gt;
&lt;/LinearLayout&gt;</code></pre>

<h3>الخطوة 2: إنشاء الـ Adapter مع ViewHolder</h3>
<pre><code>data class Student(val name: String, val grade: String)

class StudentAdapter(
    private val students: List&lt;Student&gt;,
    private val onItemClick: (Student) -&gt; Unit
) : RecyclerView.Adapter&lt;StudentAdapter.StudentViewHolder&gt;() {

    // ViewHolder: يحفظ مراجع الـ Views لتجنب البحث المتكرر
    class StudentViewHolder(view: View) : RecyclerView.ViewHolder(view) {
        val tvName: TextView = view.findViewById(R.id.tvName)
        val tvGrade: TextView = view.findViewById(R.id.tvGrade)
    }

    // تُستدعى عند الحاجة لإنشاء ViewHolder جديد
    override fun onCreateViewHolder(parent: ViewGroup, viewType: Int): StudentViewHolder {
        val view = LayoutInflater.from(parent.context)
            .inflate(R.layout.item_student, parent, false)
        return StudentViewHolder(view)
    }

    // تُستدعى لربط البيانات بكل عنصر
    override fun onBindViewHolder(holder: StudentViewHolder, position: Int) {
        val student = students[position]
        holder.tvName.text = student.name
        holder.tvGrade.text = student.grade

        // التعامل مع النقر
        holder.itemView.setOnClickListener {
            onItemClick(student)
        }
    }

    // تُعيد عدد العناصر في القائمة
    override fun getItemCount() = students.size
}</code></pre>

<h3>الخطوة 3: استخدام RecyclerView في الـ Activity</h3>
<pre><code>class MainActivity : AppCompatActivity() {
    override fun onCreate(savedInstanceState: Bundle?) {
        super.onCreate(savedInstanceState)
        setContentView(R.layout.activity_main)

        val students = listOf(
            Student("أحمد محمد", "الصف الأول"),
            Student("فاطمة علي", "الصف الثاني"),
            Student("محمد خالد", "الصف الثالث")
        )

        val recyclerView = findViewById&lt;RecyclerView&gt;(R.id.rvStudents)

        // 1. تعيين LayoutManager
        recyclerView.layoutManager = LinearLayoutManager(this)

        // 2. تعيين Adapter
        recyclerView.adapter = StudentAdapter(students) { student -&gt;
            Toast.makeText(this, "اخترت: ${student.name}", Toast.LENGTH_SHORT).show()
        }
    }
}</code></pre>

<h2>📐 أنواع LayoutManager</h2>
<table border="1" cellpadding="10" style="width:100%">
<tr><th>النوع</th><th>العرض</th><th>الاستخدام</th></tr>
<tr><td><code>LinearLayoutManager</code></td><td>قائمة عمودية أو أفقية</td><td>قوائم عادية، رسائل</td></tr>
<tr><td><code>GridLayoutManager</code></td><td>شبكة بأعمدة محددة</td><td>معرض صور، منتجات</td></tr>
<tr><td><code>StaggeredGridLayoutManager</code></td><td>شبكة بأحجام مختلفة</td><td>Pinterest شبيه</td></tr>
</table>

<pre><code>// قائمة أفقية
recyclerView.layoutManager = LinearLayoutManager(this, LinearLayoutManager.HORIZONTAL, false)

// شبكة من 2 عمود
recyclerView.layoutManager = GridLayoutManager(this, 2)

// شبكة متدرجة 3 أعمدة
recyclerView.layoutManager = StaggeredGridLayoutManager(3, StaggeredGridLayoutManager.VERTICAL)</code></pre>

<h2>⚡ DiffUtil – تحديث القائمة بكفاءة</h2>
<p>بدلاً من استدعاء <code>notifyDataSetChanged()</code> الذي يُعيد رسم <strong>كل</strong> العناصر، استخدم <code>DiffUtil</code> لحساب الفروق فقط:</p>
<pre><code>class StudentDiffCallback(
    private val oldList: List&lt;Student&gt;,
    private val newList: List&lt;Student&gt;
) : DiffUtil.Callback() {
    override fun getOldListSize() = oldList.size
    override fun getNewListSize() = newList.size
    override fun areItemsTheSame(oldPos: Int, newPos: Int) =
        oldList[oldPos].name == newList[newPos].name
    override fun areContentsTheSame(oldPos: Int, newPos: Int) =
        oldList[oldPos] == newList[newPos]
}

// الاستخدام:
val diffResult = DiffUtil.calculateDiff(StudentDiffCallback(oldList, newList))
diffResult.dispatchUpdatesTo(adapter)</code></pre>

<h2>🎨 إضافة فواصل بين العناصر (ItemDecoration)</h2>
<pre><code>// إضافة خط فاصل بين العناصر
val divider = DividerItemDecoration(this, DividerItemDecoration.VERTICAL)
recyclerView.addItemDecoration(divider)</code></pre>

<h2>⚠️ نصائح مهمة</h2>
<ol>
<li><strong>استخدم ViewHolder دائماً:</strong> RecyclerView يُجبرك على ذلك – لا تتجاهل الـ Pattern</li>
<li><strong>استخدم DiffUtil أو ListAdapter:</strong> بدلاً من <code>notifyDataSetChanged()</code></li>
<li><strong>لا تُجرِ عمليات ثقيلة في onBindViewHolder():</strong> لأنها تُستدعى عند كل تمرير</li>
<li><strong>استخدم setHasFixedSize(true):</strong> إذا كان حجم RecyclerView لا يتغير</li>
<li><strong>أضف RecycledViewPool:</strong> لمشاركة Views بين RecyclerViews متعددة</li>
</ol>']);

        $aq = Quiz::create(['title' => 'اختبار RecyclerView', 'type' => 'topic', 'topic_id' => $at3->id, 'total_points' => 50, 'pass_percentage' => 60, 'created_by' => $admin->id]);
        foreach ([
            ['ما الكلاس المطلوب لربط RecyclerView بالبيانات؟', 'Adapter', 'Layout', 'Fragment'],
            ['ما الدالة التي تُعيد عدد عناصر القائمة؟', 'getItemCount()', 'getSize()', 'count()'],
            ['ما الـ LayoutManager المناسب للقوائم العمودية؟', 'LinearLayoutManager', 'GridLayoutManager', 'StaggeredLayoutManager'],
            ['ما معنى "Recycler" في RecyclerView؟', 'إعادة استخدام الـ Views القديمة', 'حذف البيانات القديمة', 'تحديث البيانات تلقائياً'],
            ['ما الدالة التي تُستدعى لربط البيانات بكل عنصر؟', 'onBindViewHolder()', 'onCreateViewHolder()', 'getItemCount()'],
            ['ما الـ DiffUtil في RecyclerView؟', 'أداة لحساب الفروق بين قائمتين وتحديث الـ RecyclerView بكفاءة', 'نوع من الـ Adapter', 'أداة للأنيميشن'],
            ['ما الـ ItemDecoration في RecyclerView؟', 'إضافة زخرفة بصرية مثل الفواصل للعناصر', 'تأثير النقر على العنصر', 'نوع من الـ LayoutManager'],
            ['ما GridLayoutManager في RecyclerView؟', 'يعرض العناصر في شبكة بعدد أعمدة محدد', 'يعرض العناصر أفقياً فقط', 'يرتب العناصر عشوائياً'],
            ['كيف تُحدّث عنصراً واحداً في RecyclerView بكفاءة؟', 'notifyItemChanged(position)', 'notifyDataSetChanged()', 'refreshItem()'],
            ['ما الـ ViewType في RecyclerView Adapter؟', 'يُتيح عرض أنواع مختلفة من العناصر في نفس القائمة', 'تحديد نوع البيانات', 'نوع الـ Layout'],
        ] as $i => [$text, $correct, $wrong1, $wrong2]) {
            $q = Question::create(['quiz_id' => $aq->id, 'question_text' => $text, 'points' => 10]);
            Answer::create(['question_id' => $q->id, 'answer_text' => $correct, 'is_correct' => true]);
            Answer::create(['question_id' => $q->id, 'answer_text' => $wrong1, 'is_correct' => false]);
            Answer::create(['question_id' => $q->id, 'answer_text' => $wrong2, 'is_correct' => false]);
        }

        // Topic 1.4
        $at4 = Topic::create(['title' => 'Jetpack Compose', 'type' => 'article', 'created_by' => $admin->id,
            'content' => '<h1>🎨 Jetpack Compose – بناء واجهات Android الحديثة</h1>

<p>Jetpack Compose هو <strong>إطار عمل تصريحي (Declarative)</strong> من Google لبناء واجهات Android. بدلاً من XML وfindViewById، تكتب واجهتك ككود Kotlin مباشرة، وCompose يتولى تحديث الشاشة تلقائياً عند تغيير البيانات.</p>

<h2>🔄 الفرق بين البرمجة الإجرائية والتصريحية</h2>
<table border="1" cellpadding="10" style="width:100%">
<tr><th>المقاربة</th><th>الإجرائية (XML)</th><th>التصريحية (Compose)</th></tr>
<tr><td>الطريقة</td><td>أخبر النظام كيف يعمل خطوة بخطوة</td><td>صِف النتيجة المطلوبة والنظام ينفذها</td></tr>
<tr><td>التحديث</td><td>يدوي: <code>textView.text = "..."</code></td><td>تلقائي: عند تغيير State</td></tr>
<tr><td>الملفات</td><td>XML + Kotlin منفصلين</td><td>كل شيء في Kotlin</td></tr>
</table>

<h2>📝 أول Composable: Hello World</h2>
<pre><code>@Composable
fun Greeting(name: String) {
    Text(
        text = "مرحباً يا $name!",
        fontSize = 24.sp,
        color = Color.Blue,
        fontWeight = FontWeight.Bold
    )
}

// معاينة في Android Studio بدون تشغيل التطبيق
@Preview(showBackground = true)
@Composable
fun PreviewGreeting() {
    Greeting("أحمد")
}</code></pre>

<h2>📐 الـ Layouts: ترتيب العناصر</h2>

<h3>Column – ترتيب عمودي</h3>
<pre><code>@Composable
fun UserProfile() {
    Column(
        modifier = Modifier.padding(16.dp),
        horizontalAlignment = Alignment.CenterHorizontally
    ) {
        Image(painter = painterResource(R.drawable.avatar), contentDescription = "صورة")
        Spacer(modifier = Modifier.height(8.dp))
        Text("أحمد محمد", fontSize = 20.sp, fontWeight = FontWeight.Bold)
        Text("مطور Android", color = Color.Gray)
    }
}</code></pre>

<h3>Row – ترتيب أفقي</h3>
<pre><code>@Composable
fun ActionButtons() {
    Row(
        modifier = Modifier.fillMaxWidth(),
        horizontalArrangement = Arrangement.SpaceEvenly
    ) {
        Button(onClick = { }) { Text("متابعة") }
        Button(onClick = { }) { Text("رسالة") }
    }
}</code></pre>

<h3>Box – تراكب العناصر</h3>
<pre><code>@Composable
fun ImageWithBadge() {
    Box {
        Image(painter = painterResource(R.drawable.photo), contentDescription = null)
        Badge(modifier = Modifier.align(Alignment.TopEnd)) {
            Text("3")
        }
    }
}</code></pre>

<h2>🎯 Modifier – تخصيص المظهر</h2>
<p>الـ Modifier هو الأداة الأساسية لتعديل حجم ولون وسلوك أي Composable:</p>
<pre><code>Text(
    text = "نص منسّق",
    modifier = Modifier
        .fillMaxWidth()           // عرض كامل
        .padding(16.dp)           // هوامش داخلية
        .background(Color.LightGray, RoundedCornerShape(8.dp))  // خلفية مدوّرة
        .clickable { /* عمل عند النقر */ }
        .border(1.dp, Color.Gray, RoundedCornerShape(8.dp))    // إطار
)</code></pre>

<h2>🔄 State – إدارة الحالة</h2>
<p>الـ State هو ما يجعل Compose يُعيد رسم الشاشة عند تغيير البيانات:</p>
<pre><code>@Composable
fun Counter() {
    // remember: يحفظ القيمة عبر إعادة التكوين
    // mutableStateOf: يُخطر Compose بالتغييرات
    var count by remember { mutableStateOf(0) }

    Column(horizontalAlignment = Alignment.CenterHorizontally) {
        Text("العدد: $count", fontSize = 32.sp)
        Spacer(modifier = Modifier.height(8.dp))
        Row {
            Button(onClick = { count-- }) { Text("-") }
            Spacer(modifier = Modifier.width(16.dp))
            Button(onClick = { count++ }) { Text("+") }
        }
    }
}</code></pre>

<h2>📋 LazyColumn – القوائم الطويلة (مكافئ RecyclerView)</h2>
<pre><code>@Composable
fun StudentList(students: List&lt;Student&gt;) {
    LazyColumn {
        items(students) { student -&gt;
            Card(
                modifier = Modifier
                    .fillMaxWidth()
                    .padding(8.dp),
                elevation = CardDefaults.cardElevation(4.dp)
            ) {
                Row(modifier = Modifier.padding(16.dp)) {
                    Text(student.name, fontWeight = FontWeight.Bold)
                    Spacer(modifier = Modifier.weight(1f))
                    Text(student.grade, color = Color.Gray)
                }
            }
        }
    }
}</code></pre>

<h2>⬆️ State Hoisting – رفع الحالة</h2>
<p>أفضل ممارسة: اجعل الـ Composable لا يملك حالته بل يستقبلها من الأعلى:</p>
<pre><code>// Composable بدون حالة خاصة (Stateless) – قابل لإعادة الاستخدام
@Composable
fun CustomTextField(
    value: String,
    onValueChange: (String) -&gt; Unit,
    label: String
) {
    OutlinedTextField(
        value = value,
        onValueChange = onValueChange,
        label = { Text(label) }
    )
}

// الـ parent يملك الحالة
@Composable
fun LoginScreen() {
    var email by remember { mutableStateOf("") }
    var password by remember { mutableStateOf("") }

    Column {
        CustomTextField(email, { email = it }, "البريد")
        CustomTextField(password, { password = it }, "كلمة المرور")
        Button(onClick = { login(email, password) }) { Text("دخول") }
    }
}</code></pre>

<h2>⚠️ نصائح ذهبية</h2>
<ol>
<li><strong>استخدم remember:</strong> لحفظ القيم عند إعادة التكوين (Recomposition)</li>
<li><strong>استخدم rememberSaveable:</strong> لحفظ القيم حتى عند تدوير الشاشة</li>
<li><strong>تجنب الحسابات الثقيلة في Composable:</strong> استخدم LaunchedEffect أو derivedStateOf</li>
<li><strong>استخدم State Hoisting:</strong> لفصل المنطق عن الواجهة</li>
<li><strong>الـ Modifier مهم:</strong> تعلمه جيداً فهو أقوى أدواتك في Compose</li>
</ol>']);

        $aq = Quiz::create(['title' => 'اختبار Jetpack Compose', 'type' => 'topic', 'topic_id' => $at4->id, 'total_points' => 50, 'pass_percentage' => 60, 'created_by' => $admin->id]);
        foreach ([
            ['ما الـ annotation المستخدم لتعريف دالة Composable؟', '@Composable', '@Function', '@UI'],
            ['ما الـ annotation لمعاينة الـ UI في Android Studio؟', '@Preview', '@Show', '@Render'],
            ['ما نوع البرمجة الذي يعتمده Jetpack Compose؟', 'تصريحي (Declarative)', 'إجرائي (Imperative)', 'وظيفي (Functional)'],
            ['ما أداة الـ state management الأساسية في Compose؟', 'remember { mutableStateOf() }', 'setState()', 'LiveData only'],
            ['ما مكافئ TextView في Compose؟', 'Text()', 'Label()', 'TextView()'],
            ['ما مكافئ Button في Compose؟', 'Button(onClick = {}) { Text(...) }', 'Btn()', 'ClickButton()'],
            ['ما الـ Column في Jetpack Compose؟', 'ترتيب العناصر عمودياً', 'ترتيب العناصر أفقياً', 'تراكب العناصر'],
            ['ما الـ Row في Jetpack Compose؟', 'ترتيب العناصر أفقياً', 'ترتيب العناصر عمودياً', 'تراكب العناصر'],
            ['ما الـ Box في Jetpack Compose؟', 'تراكب العناصر فوق بعضها', 'ترتيب أفقي', 'ترتيب عمودي'],
            ['ما الـ LazyColumn في Compose؟', 'مكافئ RecyclerView للقوائم الطويلة في Compose', 'عمود عادي', 'قائمة أفقية'],
            ['ما الـ Modifier في Compose؟', 'يُعدّل مظهر وسلوك الـ Composable مثل الحجم واللون', 'نوع من الـ State', 'annotation خاص'],
            ['ما hoistState في Compose؟', 'رفع الـ State للأعلى لجعل الـ Composables أكثر قابلية لإعادة الاستخدام', 'نوع من الـ remember', 'أسلوب تصميم'],
        ] as [$text, $correct, $wrong1, $wrong2]) {
            $q = Question::create(['quiz_id' => $aq->id, 'question_text' => $text, 'points' => 10]);
            Answer::create(['question_id' => $q->id, 'answer_text' => $correct, 'is_correct' => true]);
            Answer::create(['question_id' => $q->id, 'answer_text' => $wrong1, 'is_correct' => false]);
            Answer::create(['question_id' => $q->id, 'answer_text' => $wrong2, 'is_correct' => false]);
        }

        // Topic 1.5
        $at5 = Topic::create(['title' => 'Room Database', 'type' => 'article', 'created_by' => $admin->id,
            'content' => '<h1>🗄️ Room Database – قواعد البيانات المحلية في Android</h1>

<p>Room هي <strong>مكتبة ORM رسمية من Android Jetpack</strong> تُسهّل التعامل مع SQLite. تُوفر طبقة تجريدية فوق SQLite مع التحقق من صحة الاستعلامات في وقت الترجمة.</p>

<h2>🏗️ المكونات الثلاثة لـ Room</h2>
<table border="1" cellpadding="10" style="width:100%">
<tr><th>المكون</th><th>الدور</th><th>الـ Annotation</th></tr>
<tr><td><strong>Entity</strong></td><td>يمثل جدول في قاعدة البيانات</td><td><code>@Entity</code></td></tr>
<tr><td><strong>DAO</strong></td><td>يحتوي دوال الوصول للبيانات</td><td><code>@Dao</code></td></tr>
<tr><td><strong>Database</strong></td><td>نقطة الوصول الرئيسية لقاعدة البيانات</td><td><code>@Database</code></td></tr>
</table>

<h2>📝 مثال عملي كامل: تطبيق ملاحظات</h2>

<h3>الخطوة 1: تعريف الـ Entity (الجدول)</h3>
<pre><code>@Entity(tableName = "notes")
data class Note(
    @PrimaryKey(autoGenerate = true)
    val id: Int = 0,

    @ColumnInfo(name = "title")
    val title: String,

    @ColumnInfo(name = "content")
    val content: String,

    @ColumnInfo(name = "created_at")
    val createdAt: Long = System.currentTimeMillis(),

    @ColumnInfo(name = "is_important")
    val isImportant: Boolean = false
)</code></pre>

<h3>الخطوة 2: تعريف الـ DAO (دوال الوصول)</h3>
<pre><code>@Dao
interface NoteDao {
    // إدراج ملاحظة جديدة
    @Insert(onConflict = OnConflictStrategy.REPLACE)
    suspend fun insert(note: Note)

    // تحديث ملاحظة
    @Update
    suspend fun update(note: Note)

    // حذف ملاحظة
    @Delete
    suspend fun delete(note: Note)

    // جلب كل الملاحظات (مرتبة بالتاريخ)
    @Query("SELECT * FROM notes ORDER BY created_at DESC")
    fun getAllNotes(): Flow&lt;List&lt;Note&gt;&gt;

    // البحث في الملاحظات
    @Query("SELECT * FROM notes WHERE title LIKE :query OR content LIKE :query")
    fun searchNotes(query: String): Flow&lt;List&lt;Note&gt;&gt;

    // جلب الملاحظات المهمة فقط
    @Query("SELECT * FROM notes WHERE is_important = 1")
    fun getImportantNotes(): Flow&lt;List&lt;Note&gt;&gt;

    // حذف كل الملاحظات
    @Query("DELETE FROM notes")
    suspend fun deleteAll()
}</code></pre>

<h3>الخطوة 3: إنشاء الـ Database</h3>
<pre><code>@Database(
    entities = [Note::class],
    version = 1,
    exportSchema = false
)
abstract class AppDatabase : RoomDatabase() {
    abstract fun noteDao(): NoteDao

    companion object {
        @Volatile
        private var INSTANCE: AppDatabase? = null

        fun getDatabase(context: Context): AppDatabase {
            return INSTANCE ?: synchronized(this) {
                val instance = Room.databaseBuilder(
                    context.applicationContext,
                    AppDatabase::class.java,
                    "notes_database"
                ).build()
                INSTANCE = instance
                instance
            }
        }
    }
}</code></pre>

<h3>الخطوة 4: الاستخدام في ViewModel</h3>
<pre><code>class NoteViewModel(application: Application) : AndroidViewModel(application) {
    private val noteDao = AppDatabase.getDatabase(application).noteDao()
    val allNotes: Flow&lt;List&lt;Note&gt;&gt; = noteDao.getAllNotes()

    fun addNote(title: String, content: String) {
        viewModelScope.launch {
            noteDao.insert(Note(title = title, content = content))
        }
    }

    fun deleteNote(note: Note) {
        viewModelScope.launch {
            noteDao.delete(note)
        }
    }
}</code></pre>

<h2>🔗 العلاقات بين الجداول</h2>
<pre><code>// علاقة One-to-Many: مستخدم لديه عدة ملاحظات
@Entity
data class User(
    @PrimaryKey val userId: Int,
    val name: String
)

@Entity(
    foreignKeys = [ForeignKey(
        entity = User::class,
        parentColumns = ["userId"],
        childColumns = ["authorId"],
        onDelete = ForeignKey.CASCADE  // حذف الملاحظات عند حذف المستخدم
    )]
)
data class Note(
    @PrimaryKey val noteId: Int,
    val authorId: Int,   // المفتاح الأجنبي
    val title: String
)

// Data class للعلاقة
data class UserWithNotes(
    @Embedded val user: User,
    @Relation(parentColumn = "userId", entityColumn = "authorId")
    val notes: List&lt;Note&gt;
)

// DAO
@Transaction
@Query("SELECT * FROM User WHERE userId = :userId")
fun getUserWithNotes(userId: Int): Flow&lt;UserWithNotes&gt;</code></pre>

<h2>📦 Migration – تحديث هيكل قاعدة البيانات</h2>
<pre><code>// عند إضافة عمود جديد
val MIGRATION_1_2 = object : Migration(1, 2) {
    override fun migrate(database: SupportSQLiteDatabase) {
        database.execSQL("ALTER TABLE notes ADD COLUMN color INTEGER NOT NULL DEFAULT 0")
    }
}

// تطبيق الـ Migration
Room.databaseBuilder(context, AppDatabase::class.java, "notes_db")
    .addMigrations(MIGRATION_1_2)
    .build()</code></pre>

<h2>🔧 Type Converters – أنواع بيانات مخصصة</h2>
<pre><code>class Converters {
    @TypeConverter
    fun fromTimestamp(value: Long?): Date? = value?.let { Date(it) }

    @TypeConverter
    fun dateToTimestamp(date: Date?): Long? = date?.time
}

@Database(entities = [Note::class], version = 1)
@TypeConverters(Converters::class)
abstract class AppDatabase : RoomDatabase() { ... }</code></pre>

<h2>⚠️ نصائح مهمة</h2>
<ol>
<li><strong>استخدم Flow أو LiveData:</strong> لمراقبة تغييرات البيانات تلقائياً</li>
<li><strong>استخدم suspend:</strong> لجعل عمليات الكتابة غير متزامنة</li>
<li><strong>لا تنسَ Migration:</strong> عند تغيير هيكل الجداول</li>
<li><strong>استخدم @Transaction:</strong> للعمليات التي تشمل عدة جداول</li>
<li><strong>اختبر DAO:</strong> Room يدعم الاختبار بقاعدة بيانات في الذاكرة</li>
</ol>']);

        $aq = Quiz::create(['title' => 'اختبار Room Database', 'type' => 'topic', 'topic_id' => $at5->id, 'total_points' => 50, 'pass_percentage' => 60, 'created_by' => $admin->id]);
        foreach ([
            ['ما الـ annotation لتعريف جدول في Room؟', '@Entity', '@Table', '@Model'],
            ['ما الـ annotation لتعريف الـ DAO؟', '@Dao', '@Database', '@Query'],
            ['ما الـ annotation لاستعلام SQL في Room؟', '@Query', '@Select', '@Fetch'],
            ['ما قاعدة البيانات التي تعتمد عليها Room؟', 'SQLite', 'MySQL', 'Firebase'],
            ['ما الـ annotation لتعريف المفتاح الأساسي؟', '@PrimaryKey', '@Id', '@Key'],
            ['ما الـ annotation لتعريف الـ Database class؟', '@Database', '@RoomDB', '@AppDatabase'],
            ['ما فائدة @Insert في Room DAO؟', 'إدراج بيانات في قاعدة البيانات', 'قراءة البيانات', 'حذف البيانات'],
            ['ما فائدة @Delete في Room DAO؟', 'حذف سجل من قاعدة البيانات', 'إدراج بيانات', 'تحديث بيانات'],
            ['ما فائدة @Update في Room DAO؟', 'تحديث سجل موجود في قاعدة البيانات', 'إدراج سجل جديد', 'حذف سجل'],
            ['ما الـ Migration في Room؟', 'التعامل مع تغييرات هيكل قاعدة البيانات عند تحديث الإصدار', 'نقل البيانات بين أجهزة', 'نوع من الاستعلام'],
            ['ما الـ ForeignKey في Room؟', 'ربط جدولين معاً عبر مفتاح خارجي', 'مفتاح أساسي إضافي', 'نوع استعلام'],
        ] as [$text, $correct, $wrong1, $wrong2]) {
            $q = Question::create(['quiz_id' => $aq->id, 'question_text' => $text, 'points' => 10]);
            Answer::create(['question_id' => $q->id, 'answer_text' => $correct, 'is_correct' => true]);
            Answer::create(['question_id' => $q->id, 'answer_text' => $wrong1, 'is_correct' => false]);
            Answer::create(['question_id' => $q->id, 'answer_text' => $wrong2, 'is_correct' => false]);
        }

        // Topics 1.6–1.10
        $androidTopicsExtra = [
            ['Intents والتنقل بين الشاشات', '<h2>🔗 ما هو الـ Intent؟</h2>
<p>Intent هو <strong>كائن رسالة</strong> يُستخدم للتواصل بين مكونات Android المختلفة. يُمكنك من خلاله الانتقال بين الشاشات (Activities)، تمرير البيانات، وتشغيل خدمات أو تطبيقات أخرى.</p>

<h2>📊 نوعان من Intent</h2>
<table border="1" cellpadding="10" style="width:100%">
<tr><th>النوع</th><th>الوصف</th><th>الاستخدام</th></tr>
<tr><td><strong>Explicit Intent</strong></td><td>يُحدد الـ Activity المطلوب بالاسم</td><td>التنقل داخل تطبيقك</td></tr>
<tr><td><strong>Implicit Intent</strong></td><td>يُحدد الفعل المطلوب فقط</td><td>مشاركة، فتح رابط، اتصال</td></tr>
</table>

<h3>Explicit Intent – التنقل بين شاشات تطبيقك</h3>
<pre><code>// الانتقال من MainActivity إلى ProfileActivity
val intent = Intent(this, ProfileActivity::class.java)
// تمرير بيانات مع Intent
intent.putExtra("user_name", "أحمد")
intent.putExtra("user_age", 25)
intent.putExtra("is_premium", true)
startActivity(intent)

// استقبال البيانات في ProfileActivity
override fun onCreate(savedInstanceState: Bundle?) {
    super.onCreate(savedInstanceState)
    val name = intent.getStringExtra("user_name") ?: "زائر"
    val age = intent.getIntExtra("user_age", 0)
    val isPremium = intent.getBooleanExtra("is_premium", false)
}</code></pre>

<h3>Implicit Intent – التفاعل مع تطبيقات أخرى</h3>
<pre><code>// فتح رابط في المتصفح
val browserIntent = Intent(Intent.ACTION_VIEW, Uri.parse("https://www.google.com"))
startActivity(browserIntent)

// مشاركة نص
val shareIntent = Intent(Intent.ACTION_SEND).apply {
    type = "text/plain"
    putExtra(Intent.EXTRA_TEXT, "مرحباً! شاهد هذا التطبيق الرائع")
}
startActivity(Intent.createChooser(shareIntent, "شارك عبر"))

// الاتصال بـ رقم
val callIntent = Intent(Intent.ACTION_DIAL, Uri.parse("tel:+201234567890"))
startActivity(callIntent)</code></pre>

<h3>Activity Result API – استلام نتيجة من شاشة أخرى</h3>
<pre><code>// الطريقة الحديثة (بدلاً من startActivityForResult)
val launcher = registerForActivityResult(ActivityResultContracts.StartActivityForResult()) { result ->
    if (result.resultCode == RESULT_OK) {
        val data = result.data?.getStringExtra("selected_item")
        // استخدم البيانات
    }
}

// الإطلاق
launcher.launch(Intent(this, SelectItemActivity::class.java))</code></pre>',
                [
                    ['ما استخدام Intent في Android؟', 'التنقل بين Screens وتمرير البيانات', 'تخزين البيانات', 'رسم الواجهات'],
                    ['كيف تمرر بيانات مع Intent؟', 'putExtra()', 'addData()', 'sendData()'],
                    ['ما الفرق بين Explicit وImplicit Intent؟', 'Explicit يحدد الـ Activity، Implicit يحدد الفعل', 'لا فرق', 'Implicit أسرع'],
                    ['ما الدالة لاستقبال Intent في Activity الثانية؟', 'getIntent()', 'receiveIntent()', 'fetchIntent()'],
                    ['ما طريقة الحصول على قيمة ممررة مع Intent؟', 'intent.getStringExtra("key")', 'intent.get("key")', 'intent.value("key")'],
                ]],
            ['ViewModel وLiveData', '<h2>🏗️ ما هو ViewModel؟</h2>
<p>ViewModel هو كلاس من <strong>Android Jetpack</strong> يحتفظ بالبيانات وحالة الواجهة حتى عند تغيير الـ Configuration (مثل تدوير الشاشة). بدلاً من فقدان البيانات وإعادة تحميلها، يبقى ViewModel حياً.</p>

<h2>📊 دورة حياة ViewModel مقارنة بالـ Activity</h2>
<pre><code>
Activity: onCreate → onDestroy (تدوير) → onCreate → onDestroy (إغلاق)
ViewModel: ────────── حي طوال الوقت ─────────── → onCleared()
</code></pre>

<h3>إنشاء ViewModel بسيط</h3>
<pre><code>class CounterViewModel : ViewModel() {
    // MutableLiveData: قابل للتعديل (داخلي)
    private val _count = MutableLiveData(0)
    // LiveData: للقراءة فقط (خارجي)
    val count: LiveData&lt;Int&gt; = _count

    private val _userName = MutableLiveData&lt;String&gt;()
    val userName: LiveData&lt;String&gt; = _userName

    fun increment() { _count.value = (_count.value ?: 0) + 1 }
    fun decrement() { _count.value = (_count.value ?: 0) - 1 }
    fun setName(name: String) { _userName.value = name }
}</code></pre>

<h3>استخدام ViewModel في Activity</h3>
<pre><code>class MainActivity : AppCompatActivity() {
    // لا تُنشئ ViewModel مباشرةً! استخدم ViewModelProvider
    private val viewModel: CounterViewModel by viewModels()

    override fun onCreate(savedInstanceState: Bundle?) {
        super.onCreate(savedInstanceState)
        setContentView(R.layout.activity_main)

        // مراقبة LiveData – يتحدث الـ UI تلقائياً عند التغيير
        viewModel.count.observe(this) { count ->
            tvCounter.text = "العدد: $count"
        }

        btnIncrement.setOnClickListener { viewModel.increment() }
        btnDecrement.setOnClickListener { viewModel.decrement() }
    }
}</code></pre>

<h2>🔄 الفرق بين LiveData وMutableLiveData</h2>
<table border="1" cellpadding="10" style="width:100%">
<tr><th>الميزة</th><th>LiveData</th><th>MutableLiveData</th></tr>
<tr><td>القراءة</td><td>✅ نعم</td><td>✅ نعم</td></tr>
<tr><td>الكتابة</td><td>❌ لا</td><td>✅ نعم</td></tr>
<tr><td>الاستخدام</td><td>للعرض في الـ UI</td><td>داخل ViewModel للتعديل</td></tr>
</table>

<h2>💡 StateFlow مع Compose (البديل الحديث)</h2>
<pre><code>class ModernViewModel : ViewModel() {
    private val _uiState = MutableStateFlow(UiState())
    val uiState: StateFlow&lt;UiState&gt; = _uiState.asStateFlow()

    fun updateName(name: String) {
        _uiState.update { it.copy(userName = name) }
    }
}

data class UiState(val userName: String = "", val isLoading: Boolean = false)</code></pre>',
                [
                    ['ما فائدة ViewModel؟', 'الاحتفاظ بالبيانات عند تدوير الشاشة', 'رسم الواجهات', 'إدارة قاعدة البيانات'],
                    ['ما الفرق بين LiveData وMutableLiveData؟', 'MutableLiveData قابل للتعديل، LiveData للقراءة فقط', 'لا فرق', 'LiveData أحدث'],
                    ['كيف تُراقب LiveData في Fragment؟', 'viewModel.data.observe(viewLifecycleOwner) { }', 'viewModel.data.watch { }', 'viewModel.data.listen { }'],
                    ['ما الـ Architecture Pattern الذي يستخدم ViewModel؟', 'MVVM', 'MVC', 'MVP'],
                    ['من أي Jetpack library تأتي ViewModel؟', 'lifecycle-viewmodel', 'room', 'navigation'],
                ]],
            ['Coroutines وAsync Programming', '<h2>⚡ ما هي Coroutines؟</h2>
<p>Coroutines هي طريقة Kotlin الحديثة للتعامل مع <strong>البرمجة غير المتزامنة (Asynchronous)</strong>. تُمكنك من كتابة كود يبدو متسلسلاً (Sequential) لكنه ينفذ عمليات طويلة في الخلفية بدون تجميد واجهة المستخدم.</p>

<h2>🚫 المشكلة بدون Coroutines</h2>
<pre><code>// ❌ هذا سيُجمّد الشاشة!
fun loadData() {
    val result = api.fetchUsers()  // عملية طويلة على Main Thread
    textView.text = result         // الشاشة متجمدة حتى تنتهي العملية
}

// ✅ الحل مع Coroutines
fun loadData() {
    viewModelScope.launch {
        val result = withContext(Dispatchers.IO) {
            api.fetchUsers()       // تعمل في خيط الـ IO
        }
        textView.text = result     // تعود للـ Main Thread تلقائياً
    }
}</code></pre>

<h2>📊 أنواع الـ Dispatchers</h2>
<table border="1" cellpadding="10" style="width:100%">
<tr><th>Dispatcher</th><th>الاستخدام</th><th>أمثلة</th></tr>
<tr><td><code>Dispatchers.Main</code></td><td>الخيط الرئيسي (UI)</td><td>تحديث الواجهة</td></tr>
<tr><td><code>Dispatchers.IO</code></td><td>عمليات الإدخال/الإخراج</td><td>قاعدة البيانات، API</td></tr>
<tr><td><code>Dispatchers.Default</code></td><td>عمليات حسابية ثقيلة</td><td>فرز بيانات، معالجة صور</td></tr>
</table>

<h3>suspend – الكلمة السحرية</h3>
<pre><code>// suspend = يمكن إيقافها مؤقتاً واستئنافها لاحقاً
suspend fun fetchUserData(): User {
    val profile = api.getProfile()       // ينتظر نتيجة 1
    val posts = api.getUserPosts()        // ينتظر نتيجة 2
    return User(profile, posts)
}

// تشغيل متوازي مع async
suspend fun loadDashboard() {
    coroutineScope {
        val profile = async { api.getProfile() }   // يبدأ فوراً
        val posts = async { api.getPosts() }        // يبدأ فوراً (متوازي)
        val notifications = async { api.getNotifications() }

        // ينتظر كل النتائج
        updateUI(profile.await(), posts.await(), notifications.await())
    }
}</code></pre>

<h3>الـ Scopes في Android</h3>
<pre><code>// في ViewModel – تُلغى تلقائياً عند حذف ViewModel
viewModelScope.launch { ... }

// في Activity/Fragment – تُلغى عند onDestroy
lifecycleScope.launch { ... }

// أساسي – يجب إلغاؤه يدوياً
val job = CoroutineScope(Dispatchers.IO).launch { ... }
job.cancel()  // إلغاء يدوي</code></pre>',
                [
                    ['ما الكلمة المفتاحية لتعليق coroutine مؤقتاً؟', 'suspend', 'async', 'await'],
                    ['ما الـ scope المناسب لـ coroutines داخل ViewModel؟', 'viewModelScope', 'globalScope', 'mainScope'],
                    ['ما فائدة Dispatchers.IO؟', 'تشغيل العمليات على خيط للإدخال/إخراج', 'تشغيل على الـ Main thread', 'إيقاف الـ coroutine'],
                    ['ما الفرق بين launch وasync في Coroutines؟', 'async يُعيد نتيجة، launch لا يُعيد', 'launch أسرع', 'لا فرق'],
                    ['ما مكتبة الشبكة التي تدعم Coroutines بشكل مدمج؟', 'Retrofit', 'OkHttp', 'Volley'],
                ]],
            ['Navigation Component', '<h2>🧭 ما هو Navigation Component؟</h2>
<p>Navigation Component هو جزء من <strong>Android Jetpack</strong> يُدير التنقل بين Fragments بشكل مرئي وآمن. يُوفر:</p>
<ul>
<li>تصميم مرئي للتنقل في Android Studio</li>
<li>إدارة تلقائية لـ Back Stack</li>
<li>تمرير بيانات آمن بـ Safe Args</li>
<li>Deep Links</li>
<li>دعم Bottom Navigation و Drawer</li>
</ul>

<h2>🏗️ المكونات الثلاثة</h2>
<table border="1" cellpadding="10" style="width:100%">
<tr><th>المكون</th><th>الدور</th></tr>
<tr><td><strong>NavGraph</strong></td><td>خريطة XML تصف كل الوجهات والمسارات</td></tr>
<tr><td><strong>NavHostFragment</strong></td><td>حاوية تعرض الـ Fragment الحالي</td></tr>
<tr><td><strong>NavController</strong></td><td>يُدير التنقل الفعلي بين الوجهات</td></tr>
</table>

<h3>nav_graph.xml – تعريف المسارات</h3>
<pre><code>&lt;navigation xmlns:android="http://schemas.android.com/apk/res/android"
    app:startDestination="@id/homeFragment"&gt;

    &lt;fragment
        android:id="@+id/homeFragment"
        android:name=".HomeFragment"&gt;
        &lt;action
            android:id="@+id/action_home_to_detail"
            app:destination="@id/detailFragment" /&gt;
    &lt;/fragment&gt;

    &lt;fragment
        android:id="@+id/detailFragment"
        android:name=".DetailFragment"&gt;
        &lt;argument
            android:name="itemId"
            app:argType="integer" /&gt;
    &lt;/fragment&gt;
&lt;/navigation&gt;</code></pre>

<h3>التنقل في الكود</h3>
<pre><code>// الانتقال بسيط
findNavController().navigate(R.id.action_home_to_detail)

// مع Safe Args (آمن وبتحقق في وقت الترجمة)
val action = HomeFragmentDirections.actionHomeToDetail(itemId = 42)
findNavController().navigate(action)

// استقبال البيانات في الوجهة
val args: DetailFragmentArgs by navArgs()
val itemId = args.itemId</code></pre>',
                [
                    ['ما ملف تعريف الـ Navigation Graph؟', 'nav_graph.xml', 'routes.xml', 'navigation.xml'],
                    ['ما الـ Fragment الذي يستضيف Navigation Component؟', 'NavHostFragment', 'MainFragment', 'ContainerFragment'],
                    ['كيف تنتقل بين وجهتين في Navigation؟', 'findNavController().navigate(R.id.action)', 'navigate(fragment)', 'switchFragment()'],
                    ['ما فائدة Safe Args في Navigation؟', 'تمرير بيانات بشكل آمن بين Fragments', 'تسريع التنقل', 'تصميم الواجهات'],
                    ['من يُدير الـ Back Stack في Navigation Component؟', 'NavController', 'BackStack', 'FragmentManager'],
                ]],
            ['Firebase مع Android', '<h2>🔥 ما هو Firebase؟</h2>
<p>Firebase هي <strong>منصة من Google</strong> تُوفر خدمات متكاملة للتطبيقات تشمل قواعد بيانات في الوقت الفعلي، مصادقة المستخدمين، إشعارات push، تخزين ملفات، و analytics – كل ذلك بدون الحاجة لبناء خادم خلفي.</p>

<h2>📊 أهم خدمات Firebase</h2>
<table border="1" cellpadding="10" style="width:100%">
<tr><th>الخدمة</th><th>الوصف</th><th>الاستخدام</th></tr>
<tr><td><strong>Authentication</strong></td><td>مصادقة المستخدمين</td><td>تسجيل دخول بالبريد، Google، Facebook</td></tr>
<tr><td><strong>Firestore</strong></td><td>قاعدة بيانات NoSQL مرنة</td><td>تخزين بيانات التطبيق</td></tr>
<tr><td><strong>Realtime Database</strong></td><td>بيانات متزامنة في الوقت الفعلي</td><td>دردشة، لعبة متعددين</td></tr>
<tr><td><strong>Storage</strong></td><td>تخزين ملفات (صور، فيديو)</td><td>رفع صور الملف الشخصي</td></tr>
<tr><td><strong>FCM</strong></td><td>إشعارات Push</td><td>تنبيهات وإشعارات</td></tr>
<tr><td><strong>Analytics</strong></td><td>تتبع سلوك المستخدمين</td><td>فهم استخدام التطبيق</td></tr>
</table>

<h3>Firebase Authentication – تسجيل الدخول</h3>
<pre><code>// تسجيل مستخدم جديد بالبريد
FirebaseAuth.getInstance()
    .createUserWithEmailAndPassword(email, password)
    .addOnCompleteListener { task ->
        if (task.isSuccessful) {
            val user = task.result?.user
            Log.d("Auth", "تم التسجيل: ${user?.uid}")
        } else {
            Log.e("Auth", "فشل: ${task.exception?.message}")
        }
    }

// تسجيل الدخول
FirebaseAuth.getInstance()
    .signInWithEmailAndPassword(email, password)
    .addOnSuccessListener { result ->
        val user = result.user
    }

// الحصول على المستخدم الحالي
val currentUser = FirebaseAuth.getInstance().currentUser</code></pre>

<h3>Firestore – قاعدة البيانات</h3>
<pre><code>val db = FirebaseFirestore.getInstance()

// إضافة بيانات
val student = hashMapOf(
    "name" to "أحمد",
    "age" to 20,
    "grade" to "ممتاز"
)
db.collection("students").add(student)
    .addOnSuccessListener { doc -> Log.d("Firestore", "تم الإضافة: ${doc.id}") }

// قراءة البيانات
db.collection("students").get()
    .addOnSuccessListener { result ->
        for (document in result) {
            Log.d("Firestore", "${document.id} => ${document.data}")
        }
    }

// الاستماع للتحديثات في الوقت الفعلي
db.collection("students").addSnapshotListener { snapshots, e ->
    if (e != null) return@addSnapshotListener
    for (doc in snapshots!!.documentChanges) {
        when (doc.type) {
            DocumentChange.Type.ADDED -> Log.d("New", doc.document.data.toString())
            DocumentChange.Type.MODIFIED -> Log.d("Modified", doc.document.data.toString())
            DocumentChange.Type.REMOVED -> Log.d("Removed", doc.document.data.toString())
        }
    }
}</code></pre>

<h3>الإعداد – google-services.json</h3>
<p>يجب تحميل ملف <code>google-services.json</code> من Firebase Console ووضعه في مجلد <code>app/</code> ثم إضافة الـ plugins في <code>build.gradle</code>.</p>',
                [
                    ['ما قاعدة بيانات Firebase الوقت الفعلي؟', 'Firebase Realtime Database', 'Firebase SQL', 'Firebase Store'],
                    ['ما خدمة Firebase للمصادقة؟', 'Firebase Authentication', 'Firebase Auth Manager', 'Firebase Login'],
                    ['ما خدمة Firebase لقراءة الأحداث؟', 'Firebase Analytics', 'Firebase Logger', 'Firebase Debug'],
                    ['ما ملف الإعداد المطلوب لـ Firebase في Android؟', 'google-services.json', 'firebase.json', 'config.json'],
                    ['ما خدمة Firebase للإشعارات؟', 'FCM (Firebase Cloud Messaging)', 'Firebase Push', 'Firebase Notify'],
                ]],
        ];

        $androidTopicModels = [$at1, $at2, $at3, $at4, $at5];
        foreach ($androidTopicsExtra as $i => $topicData) {
            [$title, $content, $questions] = $topicData;
            $topic = Topic::create(['title' => $title, 'type' => 'article', 'created_by' => $admin->id, 'content' => "<h1>$title</h1><p>$content</p>"]);
            $androidTopicModels[] = $topic;
            $quiz = Quiz::create(['title' => "اختبار $title", 'type' => 'topic', 'topic_id' => $topic->id, 'total_points' => 50, 'pass_percentage' => 60, 'created_by' => $admin->id]);
            foreach ($questions as [$qText, $correct, $wrong1, $wrong2]) {
                $q = Question::create(['quiz_id' => $quiz->id, 'question_text' => $qText, 'points' => 10]);
                Answer::create(['question_id' => $q->id, 'answer_text' => $correct, 'is_correct' => true]);
                Answer::create(['question_id' => $q->id, 'answer_text' => $wrong1, 'is_correct' => false]);
                Answer::create(['question_id' => $q->id, 'answer_text' => $wrong2, 'is_correct' => false]);
            }
        }

        // ── Android: 3 كورسات (موضوعات 0-2 | 3-5 | 6-9) ──
        $androidCourseDefs = [
            ['title' => 'Android بـ Kotlin – المستوى المبتدئ',  'suffix' => 'Beginner',     'slice' => [0, 3]],
            ['title' => 'Android بـ Kotlin – المستوى المتوسط', 'suffix' => 'Intermediate', 'slice' => [3, 3]],
            ['title' => 'Android بـ Kotlin – المستوى المتقدم', 'suffix' => 'Advanced',     'slice' => [6, 4]],
        ];
        $androidCourseOrder = 1;
        foreach ($androidCourseDefs as $cDef) {
            [$start, $len] = $cDef['slice'];
            $slice = array_slice($androidTopicModels, $start, $len);
            $course = Course::create(['title' => $cDef['title'], 'description' => 'تطوير Android الاحترافي بـ Kotlin', 'created_by' => $admin->id]);
            $attach = [];
            foreach ($slice as $idx => $t) { $attach[$t->id] = ['order' => $idx + 1]; }
            $course->topics()->attach($attach);
            Quiz::create(['title' => "Android {$cDef['suffix']} Final Exam", 'type' => 'course', 'course_id' => $course->id, 'total_points' => 30, 'pass_percentage' => 60, 'created_by' => $admin->id]);
            $androidTrack->courses()->attach([$course->id => ['order' => $androidCourseOrder++]]);
        }

        // =============================================================
        // TRACK 2 – iOS Development with Swift
        // =============================================================
        echo " Creating Track 2: iOS with Swift...\n";
        $iosTrack = Track::create(['title' => 'تطوير iOS باستخدام Swift', 'description' => 'تعلم بناء تطبيقات iPhone وiPad بلغة Swift وSwiftUI.', 'created_by' => $admin->id]);

        $iosTopicsData = [
            ['مقدمة إلى Swift', 'Swift لغة من Apple لبناء تطبيقات iOS/macOS.',
                [['من طورت لغة Swift؟', 'Apple', 'Google', 'Microsoft'],
                 ['ما الـ IDE الرسمي لتطوير iOS؟', 'Xcode', 'Android Studio', 'VS Code'],
                 ['ما الكلمة المفتاحية للثوابت في Swift؟', 'let', 'const', 'val'],
                 ['ما الكلمة المفتاحية للمتغيرات في Swift؟', 'var', 'let', 'mut'],
                 ['ما الامتداد الافتراضي لملفات Swift؟', '.swift', '.ios', '.apple'],
                 ['ما الـ Optional في Swift؟', 'نوع يمكن أن يحتوي قيمة أو nil', 'قيمة افتراضية', 'نوع خاص بالأرقام'],
                 ['كيف تُفكك Optional بشكل آمن في Swift؟', 'if let أو guard let', 'force unwrap !', 'try/catch'],
                 ['ما الـ struct في Swift؟', 'نوع قيمة (Value Type) يُنسخ عند التمرير', 'مثل class تماماً', 'نوع مرجعي'],
                 ['ما الـ enum في Swift؟', 'نوع بيانات يُعرّف مجموعة من القيم المحددة', 'مصفوفة ثابتة', 'نوع من الـ class'],
                 ['ما الـ Protocol في Swift؟', 'واجهة تُعرّف متطلبات يجب تنفيذها', 'كلاس مجرد', 'نوع من الـ struct']]],
            ['SwiftUI Basics', 'SwiftUI إطار تصريحي لبناء واجهات iOS.',
                [['ما نوع البرمجة في SwiftUI؟', 'تصريحي (Declarative)', 'إجرائي', 'Object-Oriented only'],
                 ['ما مكافئ TextView في SwiftUI؟', 'Text("")', 'Label("")', 'TextView("")'],
                 ['ما الـ property wrapper للحالة في SwiftUI؟', '@State', '@Binding', '@Published'],
                 ['ما الـ container لترتيب العناصر أفقياً؟', 'HStack', 'VStack', 'ZStack'],
                 ['ما الـ container لترتيب العناصر عمودياً؟', 'VStack', 'HStack', 'ZStack'],
                 ['ما الـ @Binding في SwiftUI؟', 'ربط قيمة بين View أب وView ابن', 'نوع من @State', 'قيمة ثابتة'],
                 ['ما الـ @ObservedObject؟', 'مراقبة object خارجي ينفذ ObservableObject', 'نوع @State', 'نوع @Binding'],
                 ['ما الـ @EnvironmentObject؟', 'مشاركة object عبر شجرة الـ Views', 'نوع @State', 'نوع @Binding'],
                 ['ما List في SwiftUI؟', 'مكافئ UITableView لعرض قوائم', 'نوع مصفوفة', 'نوع بيانات'],
                 ['ما NavigationView في SwiftUI؟', 'حاوية للتنقل بين الـ Views', 'نوع View عادي', 'شريط التنقل فقط']]],
            ['UIKit Fundamentals', 'UIKit الإطار القديم لواجهات iOS يُستخدم في المشاريع الكبيرة.',
                [['ما كلاس الشاشة الأساسي في UIKit؟', 'UIViewController', 'UIView', 'UIScreen'],
                 ['ما الـ component لعرض القوائم في UIKit؟', 'UITableView', 'UIList', 'RecyclerView'],
                 ['ما الـ Storyboard؟', 'ملف مرئي لتصميم واجهات UIKit', 'قاعدة بيانات', 'ملف إعدادات'],
                 ['ما الـ delegate في UITableView؟', 'UITableViewDelegate', 'UITableViewManager', 'TableDelegate'],
                 ['ما الـ dataSource في UITableView؟', 'UITableViewDataSource', 'UITableViewData', 'TableSource']]],
            ['Core Data', 'Core Data إطار Apple لتخزين البيانات محلياً.',
                [['ما فائدة Core Data؟', 'تخزين البيانات محلياً', 'الاتصال بالإنترنت', 'رسم الواجهات'],
                 ['ما الكلاس الأساسي في Core Data؟', 'NSManagedObject', 'NSObject', 'DataModel'],
                 ['ما الـ context في Core Data؟', 'NSManagedObjectContext', 'DataContext', 'StoreContext'],
                 ['كيف تحفظ التغييرات في Core Data؟', 'context.save()', 'context.commit()', 'context.flush()'],
                 ['ما الملف الذي يحتوي على نموذج البيانات؟', '.xcdatamodeld', '.data', '.model']]],
            ['Networking مع URLSession', 'URLSession هي أداة Apple للطلبات الشبكية.',
                [['ما الكلاس المستخدم للطلبات الشبكية في iOS؟', 'URLSession', 'HttpClient', 'NetworkManager'],
                 ['ما الدالة لبدء طلب شبكي؟', 'dataTask(with:)', 'request()', 'fetch()'],
                 ['ما تنسيق البيانات الأكثر شيوعاً في APIs؟', 'JSON', 'XML', 'CSV'],
                 ['ما الـ protocol لفك تشفير JSON في Swift؟', 'Codable', 'Decodable only', 'Serializable'],
                 ['ما الخيط الذي يجب تحديث الـ UI منه؟', 'Main Thread', 'Background Thread', 'Any Thread']]],
            ['Combine Framework', 'Combine إطار Apple للبرمجة التفاعلية.',
                [['ما الـ protocol الأساسي في Combine؟', 'Publisher', 'Observable', 'Stream'],
                 ['ما الـ property wrapper لـ Combine في SwiftUI؟', '@Published', '@State', '@Binding'],
                 ['ما الكلاس لتخزين Subscriptions؟', 'AnyCancellable', 'Subscription', 'Sink'],
                 ['ما الـ operator لتحويل البيانات في Combine؟', 'map', 'transform', 'convert'],
                 ['ما الـ operator لتصفية البيانات؟', 'filter', 'where', 'select']]],
            ['App Store والنشر', 'خطوات نشر تطبيقك على App Store.',
                [['ما المتطلب الأول لنشر تطبيق على App Store؟', 'Apple Developer Account', 'Google Account', 'GitHub Account'],
                 ['ما الأداة لأرشفة التطبيق قبل النشر؟', 'Xcode Organizer', 'Transporter', 'iTunes'],
                 ['ما صيغة ملف التطبيق المنشور؟', '.ipa', '.apk', '.app'],
                 ['ما مدة مراجعة Apple للتطبيقات عادةً؟', '1-3 أيام', 'فوراً', 'أسبوع كامل'],
                 ['ما عمولة Apple من مبيعات التطبيقات؟', '30%', '15%', '50%']]],
            ['TestFlight والاختبار', 'TestFlight أداة Apple للاختبار قبل النشر.',
                [['ما فائدة TestFlight؟', 'توزيع التطبيق على مختبرين قبل النشر', 'نشر التطبيق مباشرة', 'تصحيح الأخطاء'],
                 ['كم المختبر الأقصى في TestFlight؟', '10,000 مختبر', '100 مختبر', '1,000 مختبر'],
                 ['ما مدة صلاحية الـ build في TestFlight؟', '90 يوماً', '30 يوماً', '1 سنة'],
                 ['من أين يُحمّل المختبرون تطبيق TestFlight؟', 'App Store', 'مباشرةً من Xcode', 'رابط ويب'],
                 ['ما نوع الـ Certificate المطلوب لـ TestFlight؟', 'Distribution Certificate', 'Development Certificate', 'Any Certificate']]],
            ['مشاريع عملية iOS', 'تطبيق ما تعلمته في مشاريع حقيقية.',
                [['ما أول خطوة لبناء مشروع iOS جديد؟', 'إنشاء مشروع جديد في Xcode', 'كتابة الكود', 'تصميم الـ UI'],
                 ['ما الـ pattern المعتمد في iOS؟', 'MVC', 'MVVM', 'MVP'],
                 ['ما أداة إدارة الـ packages في iOS؟', 'Swift Package Manager', 'CocoaPods only', 'npm'],
                 ['ما فائدة CocoaPods؟', 'إدارة مكتبات الطرف الثالث', 'تصميم الواجهات', 'نشر التطبيق'],
                 ['ما الـ simulator في Xcode؟', 'محاكي للأجهزة الحقيقية', 'قاعدة بيانات', 'أداة تصميم']]],
            ['الأمان وحماية البيانات', 'حماية بيانات المستخدم في iOS.',
                [['أين تُخزّن البيانات الحساسة في iOS؟', 'Keychain', 'UserDefaults', 'CoreData'],
                 ['ما الـ framework لتشفير البيانات؟', 'CryptoKit', 'SecurityKit', 'EncryptKit'],
                 ['ما بروتوكول الاتصال الآمن في iOS؟', 'HTTPS / TLS', 'HTTP', 'FTP'],
                 ['ما App Transport Security؟', 'إجبار الاتصالات الآمنة HTTPS', 'تشفير قاعدة البيانات', 'حماية الواجهة'],
                 ['ما الـ framework لبصمة الإصبع ووجه ID؟', 'LocalAuthentication', 'FaceKit', 'BiometricKit']]],
        ];

        $iosTopicModels = [];
        foreach ($iosTopicsData as $topicData) {
            [$title, $desc, $questions] = $topicData;
            $topic = Topic::create(['title' => $title, 'type' => 'article', 'created_by' => $admin->id, 'content' => "<h1>$title</h1><p>$desc</p>"]);
            $iosTopicModels[] = $topic;
            $quiz = Quiz::create(['title' => "اختبار $title", 'type' => 'topic', 'topic_id' => $topic->id, 'total_points' => 50, 'pass_percentage' => 60, 'created_by' => $admin->id]);
            foreach ($questions as [$qText, $correct, $wrong1, $wrong2]) {
                $q = Question::create(['quiz_id' => $quiz->id, 'question_text' => $qText, 'points' => 10]);
                Answer::create(['question_id' => $q->id, 'answer_text' => $correct, 'is_correct' => true]);
                Answer::create(['question_id' => $q->id, 'answer_text' => $wrong1, 'is_correct' => false]);
                Answer::create(['question_id' => $q->id, 'answer_text' => $wrong2, 'is_correct' => false]);
            }
        }
        // ── iOS: 3 كورسات (موضوعات 0-2 | 3-5 | 6-9) ──
        $iosCourseDefs = [
            ['title' => 'iOS بـ Swift – المستوى المبتدئ',  'suffix' => 'Beginner',     'slice' => [0, 3]],
            ['title' => 'iOS بـ Swift – المستوى المتوسط', 'suffix' => 'Intermediate', 'slice' => [3, 3]],
            ['title' => 'iOS بـ Swift – المستوى المتقدم', 'suffix' => 'Advanced',     'slice' => [6, 4]],
        ];
        $iosCourseOrder = 1;
        foreach ($iosCourseDefs as $cDef) {
            [$start, $len] = $cDef['slice'];
            $slice = array_slice($iosTopicModels, $start, $len);
            $course = Course::create(['title' => $cDef['title'], 'description' => 'تطوير iOS الاحترافي بـ Swift وSwiftUI', 'created_by' => $admin->id]);
            $attach = [];
            foreach ($slice as $idx => $t) { $attach[$t->id] = ['order' => $idx + 1]; }
            $course->topics()->attach($attach);
            Quiz::create(['title' => "iOS {$cDef['suffix']} Final Exam", 'type' => 'course', 'course_id' => $course->id, 'total_points' => 30, 'pass_percentage' => 60, 'created_by' => $admin->id]);
            $iosTrack->courses()->attach([$course->id => ['order' => $iosCourseOrder++]]);
        }

        // =============================================================
        // TRACK 3 – Flutter & Dart
        // =============================================================
        echo " Creating Track 3: Flutter & Dart...\n";
        $flutterTrack = Track::create(['title' => 'Flutter وDart للمبتدئين والمحترفين', 'description' => 'بناء تطبيقات متعددة المنصات بإطار Flutter من Google.', 'created_by' => $admin->id]);

        $flutterTopicsData = [
            ['أساسيات لغة Dart', '<h1>🎯 أساسيات لغة Dart – محرك Flutter</h1>
<p>لغة Dart هي لغة برمجة محسنة للعميل (Client-optimized) لتطبيقات سريعة على أي منصة. تم تطويرها بواسطة Google وهي اللغة الأساسية لإطار Flutter.</p>

<h2>🔹 لماذا Dart؟</h2>
<ul>
<li><strong>AOT (Ahead-of-Time):</strong> تحويل الكود إلى لغة الآلة لأداء فائق.</li>
<li><strong>JIT (Just-in-Time):</strong> لدعم خاصية Hot Reload البرمجية.</li>
<li><strong>Strongly Typed:</strong> لغة صارمة في أنواع البيانات مما يقلل الأخطاء.</li>
</ul>

<h3>📝 المتغيرات وأنواع البيانات</h3>
<pre><code>void main() {
  String name = "Flutter"; // نص
  int version = 3;       // رقم صحيح
  bool isAwesome = true; // قيمة منطقية
  
  // الفرق بين final و const
  final currentTime = DateTime.now(); // قيمة ثابتة تحدد وقت التشغيل
  const pi = 3.14;                   // قيمة ثابتة تحدد وقت البرمجة
}</code></pre>

<h3>🚀 البرمجة غير المتزامنة (Asynchronous)</h3>
<p>تستخدم Dart الـ <code>Future</code> و <code>async/await</code> للتعامل مع العمليات الطويلة كجلب البيانات من الإنترنت:</p>
<pre><code>Future&lt;String&gt; fetchData() async {
  await Future.delayed(Duration(seconds: 2));
  return "تم جلب البيانات بنجاح!";
}</code></pre>',
                [['من طورت لغة Dart؟', 'Google', 'Apple', 'Facebook'],
                 ['ما نوع Dart من حيث الكتابة؟', 'Strongly Typed', 'Weakly Typed', 'Dynamic only'],
                 ['ما الدالة الرئيسية في Dart؟', 'main()', 'start()', 'run()'],
                 ['ما الكلمة للطباعة في Dart؟', 'print()', 'println()', 'console.log()'],
                 ['ما الفرق بين final وconst في Dart؟', 'const وقت الترجمة، final وقت التشغيل', 'لا فرق', 'final أسرع']]],

            ['Widgets في Flutter', '<h1>🏗️ كل شيء هو Widget</h1>
<p>في Flutter، الواجهة بأكملها عبارة عن شجرة من الـ Widgets. من الزر البسيط إلى الشاشة كاملة، كل شيء يتم تمثيله بـ Widget.</p>

<h2>📊 أنواع الـ Widgets الأساسية</h2>
<table border="1" cellpadding="10" style="width:100%">
<tr><th>النوع</th><th>الوصف</th><th>المثال</th></tr>
<tr><td><strong>Stateless</strong></td><td>ثابت لا يتغير بعد بنائه</td><td>نص، أيقونة</td></tr>
<tr><td><strong>Stateful</strong></td><td>ديناميكي يمكن تحديثه</td><td>نموذج إدخال،عداد</td></tr>
</table>

<h3>🧱 الـ Widgets الهيكلية (Layout)</h3>
<ul>
<li><strong>Scaffold:</strong> يوفر الهيكل الأساسي للصفحة (AppBar, Body, FloatingActionButton).</li>
<li><strong>Column:</strong> لترتيب العناصر عمودياً.</li>
<li><strong>Row:</strong> لترتيب العناصر أفقياً.</li>
<li><strong>Container:</strong> صندوق مرن للتنسيق (التلوين، الهوامش، الحدود).</li>
</ul>

<h3>📝 كود بسيط لواجهة Flutter</h3>
<pre><code>class MyWidget extends StatelessWidget {
  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(title: Text("مرحباً بك")),
      body: Center(
        child: Column(
          children: [
            Text("أهلاً بك في فلاتر"),
            ElevatedButton(onPressed: () {}, child: Text("اضغط هنا"))
          ],
        ),
      ),
    );
  }
}</code></pre>',
                [['ما أنواع الـ Widgets في Flutter؟', 'Stateless وStateful', 'Static وDynamic', 'Fixed وFlex'],
                 ['ما الـ Widget الذي لا يتغير حالته؟', 'StatelessWidget', 'StatefulWidget', 'ImmutableWidget'],
                 ['ما الـ Widget الذي يملك حالة داخلية؟', 'StatefulWidget', 'StatelessWidget', 'DynamicWidget'],
                 ['ما الـ Widget الأساسي لتطبيق Flutter؟', 'MaterialApp', 'FlutterApp', 'AppWidget'],
                 ['ما الـ Widget لعرض نص؟', 'Text()', 'Label()', 'TextWidget()']]],

            ['State Management بـ Provider', '<h1>🔄 إدارة الحالة (State Management)</h1>
<p>إدارة الحالة هي عملية التحكم في منطق التطبيق وتحديث الواجهة عند تغير البيانات. يعد <strong>Provider</strong> الحل الرسمي الموصى به من قبل فريق Flutter للبدء.</p>

<h2>💡 لماذا نحتاج لإدارة الحالة؟</h2>
<p>عندما يكبر التطبيق، يصبح تمرير البيانات بين الـ Widgets (Prop Drilling) صعباً جداً. الـ Provider يحل هذه المشكلة بتوفير البيانات في شجرة الـ Widgets لسهولة الوصول إليها.</p>

<h3>🛠️ خطوات استخدام Provider</h3>
<ol>
<li><strong>إنشاء الكلاس:</strong> يجب أن يرث من <code>ChangeNotifier</code>.</li>
<li><strong>استدعاء notifyListeners:</strong> لتحديث الواجهة.</li>
<li><strong>التغليف بـ ChangeNotifierProvider:</strong> في أعلى شجرة الـ Widgets.</li>
</ol>

<h3>📝 مثال عملي: عداد النقاط</h3>
<pre><code>class CounterProvider with ChangeNotifier {
  int _count = 0;
  int get count => _count;

  void increment() {
    _count++;
    notifyListeners(); // أهم خطوة للتحديث
  }
}

// في الواجهة
Text(context.watch&lt;CounterProvider&gt;().count.toString());
</code></pre>',
                [['ما الـ package الأشهر لإدارة الحالة في Flutter؟', 'provider', 'redux', 'mobx'],
                 ['ما الـ class الذي يُوفر البيانات في Provider؟', 'ChangeNotifier', 'StateManager', 'DataProvider'],
                 ['كيف تُخطر Provider بتغيير البيانات؟', 'notifyListeners()', 'setState()', 'update()'],
                 ['كيف تقرأ قيمة من Provider؟', 'context.watch<T>()', 'Provider.get<T>()', 'context.read<T>()'],
                 ['ما فائدة Consumer في Provider؟', 'إعادة بناء الـ Widget عند تغيير البيانات', 'قراءة البيانات فقط', 'حفظ البيانات']]],

            ['Flutter Navigation', '<h1>🧭 التنقل في Flutter</h1>
<p>يتم إدارة التنقل بين الصفحات في فلاتر باستخدام كلاس <strong>Navigator</strong> الذي يعمل بنظام الـ Stack (آخر من يدخل هو أول من يخرج).</p>

<h2>🛤️ طرق التنقل</h2>
<ol>
<li><strong>Basic Navigation:</strong> الانتقال المباشر باستخدام روت مؤقت.</li>
<li><strong>Named Routes:</strong> تعريف أسماء ثابتة لكل صفحة في التطبيق.</li>
<li><strong>GoRouter:</strong> حزمة حديثة تدعم الروابط العميقة (Deep Linking) المعقدة.</li>
</ol>

<h3>📝 أمثلة كود التنقل</h3>
<pre><code>// 1. الانتقال لصفحة جديدة
Navigator.push(
  context,
  MaterialPageRoute(builder: (context) => SecondScreen()),
);

// 2. العودة للخلف
Navigator.pop(context);

// 3. التنقل بالأسماء
Navigator.pushNamed(context, "/details");
</code></pre>',
                [['ما الكلاس المستخدم للتنقل في Flutter؟', 'Navigator', 'Router', 'NavController'],
                 ['ما الدالة للذهاب إلى شاشة جديدة؟', 'Navigator.push()', 'Navigator.go()', 'Navigator.open()'],
                 ['ما الدالة للعودة للشاشة السابقة؟', 'Navigator.pop()', 'Navigator.back()', 'Navigator.return()'],
                 ['ما الـ package للـ navigation المتقدم؟', 'go_router', 'flutter_nav', 'nav_plus'],
                 ['ما Named Routes؟', 'تعريف مسارات بأسماء بدلاً من Widgets', 'مسارات مشفرة', 'مسارات ثابتة']]],

            ['HTTP والـ APIs في Flutter', '<h1>🌐 الاتصال بالإنترنت وجلب البيانات</h1>
<p>التواصل مع الـ Web Service هو جزء أساسي من أي تطبيق موبايل. الحزمة الأكثر استخداماً هي <code>http</code>.</p>

<h2>🔄 دورة جلب البيانات</h2>
<ol>
<li>إرسال طلب (Request) مثل GET أو POST.</li>
<li>تحويل الاستجابة (Response) من خام إلى JSON.</li>
<li>تحويل الـ JSON إلى كلاس Dart (Model) لاستخدامه في الواجهة.</li>
</ol>

<h3>📝 كود جلب بيانات مستخدم</h3>
<pre><code>import \'package:http/http.dart\' as http;
import \'dart:convert\';

Future&lt;void&gt; fetchUsers() async {
  final url = Uri.parse(\'https://api.example.com/users\');
  final response = await http.get(url);

  if (response.statusCode == 200) {
    var data = jsonDecode(response.body);
    print(data[\'name\']);
  }
}</code></pre>
<p>ينصح دائماً باستخدام <strong>FutureBuilder</strong> لعرض البيانات في الواجهة أثناء جلبها.</p>',
                [['ما الـ package الأشهر لطلبات HTTP في Flutter؟', 'http', 'dio', 'retrofit'],
                 ['ما دالة GET request في الـ http package؟', 'http.get()', 'http.fetch()', 'http.request()'],
                 ['كيف تُحول استجابة JSON إلى Map في Dart؟', 'jsonDecode(response.body)', 'JSON.parse()', 'json.decode()'],
                 ['ما الـ model class في Flutter؟', 'كلاس Dart يمثل بيانات الـ API', 'ملف JSON', 'قاعدة بيانات'],
                 ['ما تقنية التحويل من JSON إلى Object؟', 'fromJson()', 'parseJson()', 'toObject()']]],

            ['Flutter Animations', '<h1>🎨 الرسوم المتحركة (Animations)</h1>
<p>تعتبر الرسوم المتحركة ما يميز تطبيقات Flutter عن غيرها بفضل سلاسة الوصول إلى 60 إطار في الثانية.</p>

<h2>🔧 أنواع الأنيميشن</h2>
<ul>
<li><strong>Implicit Animations:</strong> أسهل الأنواع، تغطي التغييرات البسيطة مثل <code>AnimatedContainer</code>.</li>
<li><strong>Explicit Animations:</strong> تحكم كامل في الحركة باستخدام <code>AnimationController</code>.</li>
<li><strong>Hero Animations:</strong> حركة انتقال العناصر بين الشاشات.</li>
</ul>

<h3>📝 مثال AnimatedContainer</h3>
<pre><code>AnimatedContainer(
  duration: Duration(seconds: 1),
  color: isSelected ? Colors.red : Colors.blue,
  width: isSelected ? 200 : 100,
  child: FlutterLogo(),
)</code></pre>',
                [['ما الكلاس الأساسي للـ animations في Flutter؟', 'AnimationController', 'Animator', 'FlutterAnimation'],
                 ['ما الـ Widget لتحريك عنصر عند ظهوره؟', 'AnimatedOpacity', 'FadeWidget', 'AppearWidget'],
                 ['ما الـ Widget لتحريك موضع عنصر؟', 'AnimatedPositioned', 'MovingWidget', 'SlideWidget'],
                 ['ما الـ Curve المستخدم لحركة طبيعية؟', 'Curves.easeInOut', 'Curves.linear', 'Curves.fast'],
                 ['ما الـ Widget لأبسط animations في Flutter؟', 'AnimatedContainer', 'SimpleAnimation', 'EasyAnimation']]],

            ['Firebase مع Flutter', '<h1>🔥 قوة Firebase مع Flutter</h1>
<p>Firebase توفر بنية تحتية سحابية قوية لتطبيقك بدون الحاجة لبناء سيرفر خاص.</p>

<h2>⭐ الخدمات الأساسية</h2>
<table border="1" cellpadding="10" style="width:100%">
<tr><th>الخدمة</th><th>الوصف في Flutter</th></tr>
<tr><td><strong>Authentication</strong></td><td>تسجيل مستخدمين (Google, Email)</td></tr>
<tr><td><strong>Firestore</strong></td><td>قاعدة بيانات حية (Real-time NoSQL)</td></tr>
<tr><td><strong>Storage</strong></td><td>تخزين ملفات وصور المستخدمين</td></tr>
</table>

<h3>📝 تسجيل مستخدم جديد</h3>
<pre><code>final FirebaseAuth _auth = FirebaseAuth.instance;

Future&lt;void&gt; register(email, password) async {
  await _auth.createUserWithEmailAndPassword(
    email: email, 
    password: password
  );
}</code></pre>',
                [['ما الـ package لـ Firebase Auth في Flutter؟', 'firebase_auth', 'flutter_auth', 'auth_manager'],
                 ['ما الـ package لـ Firestore في Flutter؟', 'cloud_firestore', 'firebase_firestore', 'flutter_firestore'],
                 ['كيف تحصل على المستخدم الحالي في Firebase Auth؟', 'FirebaseAuth.instance.currentUser', 'Auth.user', 'Firebase.getUser()'],
                 ['ما الـ Stream لمراقبة حالة تسجيل الدخول؟', 'authStateChanges()', 'userStream()', 'loginStream()'],
                 ['ما نوع قاعدة بيانات Firestore؟', 'NoSQL Document-based', 'SQL Relational', 'Key-Value only']]],

            ['Flutter Testing', '<h1>🧪 اختبار جودة الأداء (Testing)</h1>
<p>لضمان استقرار تطبيقك، يجب إجراء اختبارات آلية تغطي كافة أجزائه.</p>

<h2>📑 أنواع الاختبارات</h2>
<ol>
<li><strong>Unit Testing:</strong> اختبار دالة أو كلاس واحد فقط.</li>
<li><strong>Widget Testing:</strong> التأكد من أن الواجهة تظهر وتتفاعل بشكل صحيح.</li>
<li><strong>Integration Testing:</strong> اختبار سيناريو كامل كعملية تسجيل الدخول في جهاز حقيقي.</li>
</ol>

<h3>📝 مثال Unit Test</h3>
<pre><code>void main() {
  test(\'يجب أن يزيد العداد\', () {
    final counter = Counter();
    counter.increment();
    expect(counter.value, 1);
  });
}</code></pre>',
                [['ما أنواع الاختبار في Flutter؟', 'Unit, Widget, Integration', 'Manual, Auto', 'Debug, Release'],
                 ['ما الـ package الأساسي للاختبار في Dart؟', 'test', 'flutter_test', 'mockito'],
                 ['ما الدالة لاختبار Widgets؟', 'testWidgets()', 'widgetTest()', 'testUI()'],
                 ['ما الكلاس للتفاعل مع Widgets في الاختبار؟', 'WidgetTester', 'TestRunner', 'UITester'],
                 ['ما مفهوم Mock في الاختبار؟', 'محاكاة كائنات وهمية بدل الحقيقية', 'اختبار يدوي', 'اختبار أداء']]],

            ['Flutter للويب وسطح المكتب', '<h1>💻 ما وراء الموبايل</h1>
<p>فلاتر تسمح لك باستخدام نفس الكود لبناء تطبيقات للويب، ويندوز، ماك، ولينكس.</p>

<h2>🌐 Flutter Web</h2>
<p>يتم تحويل الكود إلى HTML/CSS و CanvasKit للحصول على أفضل أداء في المتصفح.</p>

<h2>🖥️ Desktop Support</h2>
<p>يوفر فلاتر واجهات سطح مكتب أصلية مع دعم كامل للفأرة ولوحة المفاتيح.</p>

<pre><code># لتفعيل دعم الويندوز
flutter config --enable-windows-desktop
# لبناء نسخة الويب
flutter build web</code></pre>',
                [['كم منصة يدعمها Flutter رسمياً؟', '6 منصات', '2 منصات', '4 منصات'],
                 ['ما أمر تشغيل Flutter على الويب؟', 'flutter run -d chrome', 'flutter web', 'flutter run --web'],
                 ['ما قيود Flutter على الويب مقارنة بالموبايل؟', 'حجم الـ bundle وأداء الرسوم', 'لا توجد قيود', 'عدم دعم الـ API'],
                 ['ما المنصات المكتبية التي يدعمها Flutter؟', 'Windows, macOS, Linux', 'Windows only', 'macOS only'],
                 ['ما الأمر لبناء Flutter للـ Desktop؟', 'flutter build windows/macos/linux', 'flutter desktop build', 'flutter compile']]],

            ['نشر تطبيق Flutter', '<h1>🚀 من الكود إلى عالم المستخدمين</h1>
<p>مرحلة النشر هي المرحلة النهائية التي تتطلب ضبط إعدادات كل نظام تشغيل.</p>

<h2>📦 خطوات التحضير</h2>
<ul>
<li><strong>Android:</strong> ضبط الـ App Bundle، الشهادة الرقمية (Signing Key)، وتحديث ملف proguard.</li>
<li><strong>iOS:</strong> استخدام Xcode لضبط الـ Bundle ID، وتجهيز الـ TestFlight.</li>
</ul>

<pre><code># بناء نسخة الأندرويد النهائية
flutter build appbundle
# تنظيف المشروع قبل البناء
flutter clean
</code></pre>',
                [['ما الأمر لبناء APK في Flutter؟', 'flutter build apk', 'flutter compile android', 'flutter release'],
                 ['ما الأمر لبناء IPA لـ iOS؟', 'flutter build ios', 'flutter compile ios', 'flutter release ios'],
                 ['ما الملف الذي يحتوي على إعدادات التطبيق في Flutter؟', 'pubspec.yaml', 'config.yaml', 'app.yaml'],
                 ['أين تُعرّف الـ packages التي يعتمد عليها Flutter؟', 'dependencies في pubspec.yaml', 'package.json', 'requirements.txt'],
                 ['ما الأمر لتحديث الـ packages في Flutter؟', 'flutter pub get', 'flutter install', 'flutter update']]],
        ];

        $flutterTopicModels = [];
        foreach ($flutterTopicsData as $topicData) {
            [$title, $content, $questions] = $topicData;
            $topic = Topic::create(['title' => $title, 'type' => 'article', 'created_by' => $admin->id, 'content' => $content]);
            $flutterTopicModels[] = $topic;
            $quiz = Quiz::create(['title' => "اختبار $title", 'type' => 'topic', 'topic_id' => $topic->id, 'total_points' => 50, 'pass_percentage' => 60, 'created_by' => $admin->id]);
            foreach ($questions as [$qText, $correct, $wrong1, $wrong2]) {
                $q = Question::create(['quiz_id' => $quiz->id, 'question_text' => $qText, 'points' => 10]);
                Answer::create(['question_id' => $q->id, 'answer_text' => $correct, 'is_correct' => true]);
                Answer::create(['question_id' => $q->id, 'answer_text' => $wrong1, 'is_correct' => false]);
                Answer::create(['question_id' => $q->id, 'answer_text' => $wrong2, 'is_correct' => false]);
            }
        }
        // ── Flutter: 3 كورسات (موضوعات 0-2 | 3-5 | 6-9) ──
        $flutterCourseDefs = [
            ['title' => 'Flutter وDart – المستوى المبتدئ',  'suffix' => 'Beginner',     'slice' => [0, 3]],
            ['title' => 'Flutter وDart – المستوى المتوسط', 'suffix' => 'Intermediate', 'slice' => [3, 3]],
            ['title' => 'Flutter وDart – المستوى المتقدم', 'suffix' => 'Advanced',     'slice' => [6, 4]],
        ];
        $flutterCourseOrder = 1;
        foreach ($flutterCourseDefs as $cDef) {
            [$start, $len] = $cDef['slice'];
            $slice = array_slice($flutterTopicModels, $start, $len);
            $course = Course::create(['title' => $cDef['title'], 'description' => 'تطوير تطبيقات متعددة المنصات بـ Flutter وDart', 'created_by' => $admin->id]);
            $attach = [];
            foreach ($slice as $idx => $t) { $attach[$t->id] = ['order' => $idx + 1]; }
            $course->topics()->attach($attach);
            Quiz::create(['title' => "Flutter {$cDef['suffix']} Final Exam", 'type' => 'course', 'course_id' => $course->id, 'total_points' => 30, 'pass_percentage' => 60, 'created_by' => $admin->id]);
            $flutterTrack->courses()->attach([$course->id => ['order' => $flutterCourseOrder++]]);
        }

        // =============================================================
        // HELPER: build track from structured array
        // =============================================================
        $buildTrack = function (string $title, string $desc, array $topicsData) use ($admin): Track {
            $track = Track::create(['title' => $title, 'description' => $desc, 'created_by' => $admin->id]);
            $topicModels = [];
            foreach ($topicsData as $topicData) {
                [$tTitle, $tDesc, $questions] = $topicData;
                $topic = Topic::create(['title' => $tTitle, 'type' => 'article', 'created_by' => $admin->id, 'content' => "<h1>$tTitle</h1><p>$tDesc</p>"]);
                $topicModels[] = $topic;
                $quiz = Quiz::create(['title' => "اختبار $tTitle", 'type' => 'topic', 'topic_id' => $topic->id, 'total_points' => 50, 'pass_percentage' => 60, 'created_by' => $admin->id]);
                foreach ($questions as [$qText, $correct, $wrong1, $wrong2]) {
                    $q = Question::create(['quiz_id' => $quiz->id, 'question_text' => $qText, 'points' => 10]);
                    Answer::create(['question_id' => $q->id, 'answer_text' => $correct, 'is_correct' => true]);
                    Answer::create(['question_id' => $q->id, 'answer_text' => $wrong1, 'is_correct' => false]);
                    Answer::create(['question_id' => $q->id, 'answer_text' => $wrong2, 'is_correct' => false]);
                }
            }

            // ── تقسيم الـ 10 موضوعات على 3 كورسات: (3 | 3 | 4) ──
            $courses = [
                ['title' => "$title – المستوى المبتدئ",    'suffix' => 'Beginner',     'slice' => [0, 3]],
                ['title' => "$title – المستوى المتوسط",    'suffix' => 'Intermediate', 'slice' => [3, 3]],
                ['title' => "$title – المستوى المتقدم",    'suffix' => 'Advanced',     'slice' => [6, 4]],
            ];
            $courseOrder = 1;
            foreach ($courses as $cDef) {
                [$start, $len] = $cDef['slice'];
                $slice = array_slice($topicModels, $start, $len);
                if (empty($slice)) continue;
                $course = Course::create([
                    'title'       => $cDef['title'],
                    'description' => $desc,
                    'created_by'  => $admin->id,
                ]);
                $attach = [];
                foreach ($slice as $idx => $t) { $attach[$t->id] = ['order' => $idx + 1]; }
                $course->topics()->attach($attach);
                Quiz::create([
                    'title'           => "{$title} {$cDef['suffix']} Final Exam",
                    'type'            => 'course',
                    'course_id'       => $course->id,
                    'total_points'    => 30,
                    'pass_percentage' => 60,
                    'created_by'      => $admin->id,
                ]);
                $track->courses()->attach([$course->id => ['order' => $courseOrder++]]);
            }
            return $track;
        };

        // =============================================================
        // TRACK 4 – React.js
        // =============================================================
        echo " Creating Track 4: React.js...\n";
        $buildTrack('تطوير الواجهات بـ React.js', 'إطار JavaScript لبناء واجهات مستخدم تفاعلية وديناميكية.', [
            ['مقدمة إلى React', '<h1>🚀 مرحباً بك في عالم React</h1>
<p>React هي مكتبة JavaScript لبناء واجهات المستخدم، تركز على مفهوم المكونات (Components).</p>
<h2>핵 المبادئ الأساسية:</h2>
<ul>
<li><strong>JSX:</strong> دمج HTML مع JavaScript.</li>
<li><strong>Components:</strong> تقسيم الواجهة لقطع صغيرة قابلة لإعادة الاستخدام.</li>
<li><strong>Virtual DOM:</strong> تحديث الواجهة بكفاءة عالية.</li>
</ul>
<h3>📝 كود بسيط:</h3>
<pre><code>function Welcome() {
  return &lt;h1&gt;مرحباً بالعالم!&lt;/h1&gt;;
}</code></pre>', [
                ['من طورت React؟', 'Facebook (Meta)', 'Google', 'Microsoft', 'Twitter'],
                ['ما الوحدة الأساسية في React؟', 'Component', 'Module', 'Class', 'Tag'],
            ]],

            ['React Hooks', '<h1>⚓ التعامل مع Hooks</h1>
<p>الـ Hooks تسمح لك باستخدام الحالة (State) ومميزات React الأخرى في المكونات الوظيفية (Function Components).</p>
<h2>🛠️ أشهر الـ Hooks:</h2>
<ul>
<li><strong>useState:</strong> لإضافة حالة للمكون.</li>
<li><strong>useEffect:</strong> للتعامل مع الآثار الجانبية (مثل جلب البيانات).</li>
</ul>
<pre><code>const [count, setCount] = useState(0);
useEffect(() => {
  document.title = `لقد نقرت ${count} مرات`;
}, [count]);</code></pre>', [
                ['ما الـ Hook للحالة في React؟', 'useState', 'useData', 'useStatus', 'useCurrent'],
                ['ما الـ Hook للآثار الجانبية؟', 'useEffect', 'useSideEffect', 'useLifecycle', 'useAction'],
            ]],

            ['إدارة الحالة بـ Redux', '<h1>📦 إدارة الحالة المركزية مع Redux</h1>
<p>عندما يكبر التطبيق، نحتاج لمكان واحد لتخزين الحالة (State) يمكن الوصول إليه من أي مكان.</p>
<h2>🏗️ الهيكل الأساسي:</h2>
<ul>
<li><strong>Store:</strong> المخزن الرئيسي.</li>
<li><strong>Actions:</strong> وصف لما سيحدث.</li>
<li><strong>Reducers:</strong> تنفيذ التغيير الفعلي.</li>
</ul>
<p>ينصح حالياً باستخدام <strong>Redux Toolkit</strong> لسهولتها.</p>', [
                ['ما الـ concept الأساسي في Redux؟', 'Store, Action, Reducer', 'State, Props, Context', 'Model, View, Controller', 'Input, Process, Output'],
                ['ما الـ Store في Redux؟', 'مكان مركزي لتخزين حالة التطبيق', 'قاعدة بيانات', 'ملف إعدادات', 'واجهة المستخدم'],
            ]],

            ['React Router', '<h1>🧭 التنقل بين الصفحات</h1>
<p>React Router هي المكتبة القياسية للتنقل بين الصفحات في تطبيقات React (Single Page Applications).</p>
<pre><code>&lt;Routes&gt;
  &lt;Route path="/" element={&lt;Home /&gt;} /&gt;
  &lt;Route path="/about" element={&lt;About /&gt;} /&gt;
&lt;/Routes&gt;</code></pre>
<p>استخدم <code>Link</code> بدلاً من <code>&lt;a&gt;</code> لمنع إعادة تحميل الصفحة بالكامل.</p>', [
                ['ما الـ package للـ routing في React؟', 'react-router-dom', 'react-navigation', 'next-router', 'web-navigation'],
                ['ما الـ component لتعريف مسار؟', '<Route>', '<Page>', '<Path>', '<Screen>'],
            ]],

            ['TypeScript مع React', '<h1>🛡️ حماية الكود بـ TypeScript</h1>
<p>استخدام TypeScript مع React يساعدك على اكتشاف الأخطاء قبل تشغيل التطبيق عن طريق تحديد أنواع البيانات (Types).</p>
<pre><code>interface Props {
  title: string;
  count?: number;
}
const Welcome: React.FC&lt;Props&gt; = ({ title }) => &lt;h1&gt;{title}&lt;/h1&gt;;</code></pre>', [
                ['ما فائدة TypeScript؟', 'اكتشاف الأخطاء وقت الكتابة', 'تسريع التطبيق', 'تصميم الواجهات', 'ضغط الكود'],
                ['ما امتداد ملفات React TypeScript؟', '.tsx', '.ts', '.jsx', '.native'],
            ]],

            ['Next.js الأساسيات', '<h1>🖼️ إطار عمل Next.js</h1>
<p>Next.js هو إطار عمل مبني فوق React يوفر ميزات مثل Server-Side Rendering (SSR) وتحسين محركات البحث (SEO).</p>
<ul>
<li><strong>Routing:</strong> يعتمد على هيكل المجلدات (app directory).</li>
<li><strong>Performance:</strong> تحسين تلقائي للصور والروابط.</li>
</ul>', [
                ['ما الـ rendering الافتراضي في Next.js؟', 'Server-Side Rendering', 'Client-Side Rendering', 'Static Only', 'Canvas Rendering'],
                ['ما اصطلاح الـ routing في Next.js؟', 'File-based routing', 'Config-based routing', 'Component routing', 'Action routing'],
            ]],

            ['Component Lifecycle', '<h1>🔄 دورة حياة المكون</h1>
<p>تمر مكونات React بثلاث مراحل رئيسية: Mounting (البناء)، Updating (التحديث)، و Unmounting (الإزالة).</p>
<p>في المكونات الوظيفية، يدير <code>useEffect</code> هذه المراحل ببراعة.</p>', [
                ['أي مرحلة تُنفذ عند إضافة المكون للـ DOM؟', 'Mounting', 'Updating', 'Unmounting', 'Rendering'],
                ['أي دالة في Class Component تعمل عند حذف المكون؟', 'componentWillUnmount', 'componentDidDelete', 'stopComponent', 'exit'],
            ]],

            ['Forms و Validation', '<h1>📝 التعامل مع النماذج</h1>
<p>يمكن إدارة النماذج في React يدوياً باستخدام <code>useState</code> أو باستخدام مكتبات مثل <strong>React Hook Form</strong> للحصول على أداء أفضل وتحقق (Validation) أسهل.</p>', [
                ['ما المكتبة الشهيرة للتعامل مع النماذج في React؟', 'React Hook Form', 'React Input', 'React Data', 'Form Manager'],
                ['أي سمة في الإدخال تربط قيمته بالـ State؟', 'value', 'data', 'content', 'state'],
            ]],

            ['API Handling', '<h1>🌐 طلب البيانات من السيرفر</h1>
<p>نستخدم عادة <code>fetch</code> أو مكتبة <code>axios</code> داخل <code>useEffect</code> لجلب البيانات.</p>
<pre><code>useEffect(() => {
  axios.get("/api/data").then(res => setData(res.data));
}, []);</code></pre>', [
                ['ما المكتبة الشهيرة لطلبات HTTP؟', 'Axios', 'Fetch', 'Ajax', 'Requests'],
                ['أين يوضع كود جلب البيانات عادة؟', 'useEffect', 'useState', 'render', 'constructor'],
            ]],

            ['نشر تطبيق React', '<h1>🚀 النشر والاستضافة</h1>
<p>يمكن نشر تطبيقات React على منصات مثل <strong>Vercel</strong> أو <strong>Netlify</strong> أو <strong>Firebase Hosting</strong> بخطوات بسيطة بعد تنفيذ أمر البناء.</p>
<pre><code>npm run build</code></pre>', [
                ['ما أمر بناء النسخة النهائية؟', 'npm run build', 'npm start', 'npm create-version', 'npm compile'],
                ['ما المنصة التي طورت Next.js وتعتبر الأفضل لنشر React؟', 'Vercel', 'AWS', 'Heroku', 'Netlify'],
            ]],
            ['React Performance', 'تحسين أداء تطبيقات React.', [
                ['ما الـ Hook لحفظ نتيجة عملية مكلفة؟', 'useMemo', 'useCache', 'useOptimize'],
                ['ما الـ Hook لحفظ مرجع دالة؟', 'useCallback', 'useFn', 'useMethod'],
                ['ما الـ HOC لمنع إعادة render غير الضروري؟', 'React.memo', 'React.pure', 'React.optimize'],
                ['ما أداة قياس أداء React؟', 'React DevTools Profiler', 'Chrome Performance', 'React Bench'],
                ['ما Code Splitting في React؟', 'تقسيم الكود لتحميل أسرع', 'تقسيم المكونات', 'فصل CSS'],
                ['ما Lazy Loading في React؟', 'تحميل المكون عند الحاجة فقط', 'تحميل الصور ببطء', 'تأخير التحميل'],
                ['ما React.lazy()؟', 'تحميل Component بشكل غير متزامن', 'Component خاص', 'Hook للتحميل'],
                ['ما Suspense في React؟', 'يعرض fallback أثناء تحميل Component كسول', 'نوع error boundary', 'حالة تحميل'],
                ['ما Reconciliation في React؟', 'خوارزمية مقارنة Virtual DOM لتحديث الفعلي', 'عملية render', 'عملية mount'],
            ]],
            ['React Testing', 'اختبار مكونات React.', [
                ['ما المكتبة الأشهر لاختبار React؟', 'React Testing Library', 'Jest only', 'Mocha'],
                ['ما الدالة لتصيير component في الاختبار؟', 'render()', 'mount()', 'show()'],
                ['ما مفهوم "getByText" في Testing Library؟', 'البحث عن عنصر بنصه', 'البحث عن عنصر بـ ID', 'اختبار النص'],
                ['ما دالة المحاكاة للـ events في الاختبار؟', 'fireEvent', 'triggerEvent', 'simulateEvent'],
                ['ما مفهوم snapshot testing؟', 'حفظ صورة للـ UI والمقارنة لاحقاً', 'لقطة شاشة', 'اختبار بصري'],
                ['ما مفهوم "queryByText"؟', 'يبحث عن عنصر ويُعيد null إن لم يجد', 'يبحث كـ getByText', 'يُلقي خطأ إن لم يجد'],
                ['ما screen في React Testing Library؟', 'كائن يُوفّر dومethods للبحث في rendered output', 'شاشة المستخدم', 'نوع render'],
                ['ما waitFor في React Testing Library؟', 'ينتظر تغييراً غير متزامن في الـ DOM', 'يُوقف الاختبار', 'دالة timeout'],
                ['ما مفهوم Test Coverage؟', 'نسبة الكود المُغطى بالاختبارات', 'عدد الاختبارات', 'جودة الاختبارات'],
            ]],
            ['React Forms', 'إدارة النماذج في React.', [
                ['ما مفهوم Controlled Component؟', 'الـ input قيمته مرتبطة بـ State', 'input بلا state', 'input ثابت'],
                ['ما مفهوم Uncontrolled Component؟', 'الـ input يُدار بـ ref بدل state', 'input بدون onChange', 'input غير موجود'],
                ['ما المكتبة الأشهر لإدارة النماذج؟', 'React Hook Form', 'Formik', 'كلاهما شائعان'],
                ['ما مكتبة التحقق الأشهر مع React Hook Form؟', 'Zod أو Yup', 'validator.js', 'Joi'],
                ['ما الـ event للتعامل مع submit النموذج؟', 'onSubmit', 'onSend', 'onForm'],
                ['ما register في React Hook Form؟', 'تسجيل حقل الـ input في النموذج', 'نوع validation', 'دالة إرسال'],
                ['ما handleSubmit في React Hook Form؟', 'دالة تُعالج submit النموذج بعد validation', 'دالة إعادة ضبط', 'دالة تسجيل'],
                ['ما formState.errors؟', 'كائن يحتوي أخطاء validation لكل حقل', 'حالة النموذج الكاملة', 'قائمة الحقول'],
                ['ما reset() في React Hook Form؟', 'دالة لإعادة النموذج لقيمه الابتدائية', 'دالة حذف النموذج', 'دالة إلغاء validation'],
            ]],
            ['Tailwind CSS مع React', 'تنسيق React بـ Tailwind CSS.', [
                ['ما نوع Tailwind CSS؟', 'Utility-First CSS Framework', 'Component Framework', 'CSS Preprocessor'],
                ['كيف تُضيف Tailwind لـ React؟', 'npm install tailwindcss', 'npm install bootstrap', 'npm install css'],
                ['ما الكلاس لجعل العنصر flex؟', 'flex', 'd-flex', 'display-flex'],
                ['ما الكلاس لجعل النص أزرق في Tailwind؟', 'text-blue-500', 'color-blue', 'text-color-blue'],
                ['ما الكلاس لإضافة padding من كل الجهات؟', 'p-4', 'padding-4', 'pad-4'],
                ['ما الكلاس لجعل الخلفية حمراء في Tailwind؟', 'bg-red-500', 'background-red', 'bg-color-red'],
                ['ما الـ responsive prefix في Tailwind؟', 'md: lg: xl: للشاشات المختلفة', 'responsive:', 'screen:'],
                ['ما الـ dark mode class في Tailwind؟', 'dark: prefix مع dark mode config', 'night:', 'mode-dark:'],
                ['ما الـ hover state في Tailwind؟', 'hover: prefix (e.g. hover:bg-blue-600)', 'on-hover:', ':hover'],
                ['ما الـ JIT في Tailwind CSS؟', 'Just-In-Time: توليد CSS عند الحاجة فقط', 'JavaScript Integration Tool', 'Java Interface Type'],
            ]],
        ]);

        // =====================================================================
        // =============================================================
        echo " Creating Track 5: Node.js Backend...\n";
        $buildTrack('تطوير الـ Backend بـ Node.js', 'بناء APIs وخوادم قوية باستخدام Node.js وExpress.', [
            ['مقدمة إلى Node.js', '<h1>🌐 ما هو Node.js؟</h1>
<p>Node.js هي بيئة تشغيل (Runtime) تسمح لك بتشغيل JavaScript خارج المتصفح، وتحديداً على السيرفر.</p>
<ul>
<li><strong>V8 Engine:</strong> المحرك السريع الذي طورته Google ويدير Node.js.</li>
<li><strong>Event Driven:</strong> يعتمد كلياً على نظام الأحداث والتعامل مع الطلبات بشكل غير متزامن.</li>
<li><strong>Non-blocking I/O:</strong> يسمح بمعالجة آلاف الطلبات المتزامنة دون توقف البرنامج.</li>
</ul>', [
                ['ما بيئة تشغيل Node.js؟', 'V8 Engine', 'SpiderMonkey', 'Chakra', 'Gecko'],
                ['ما طبيعة Node.js في معالجة الطلبات؟', 'Non-blocking I/O', 'Blocking I/O', 'Sync only', 'Direct I/O'],
                ['ما مدير الحزم الافتراضي في Node.js؟', 'npm', 'yarn', 'pip', 'apt'],
                ['ما الأمر لتشغيل ملف Node.js؟', 'node app.js', 'run app.js', 'start app.js', 'exec app.js'],
                ['ما ملف إعدادات مشروع Node.js؟', 'package.json', 'config.json', 'node.json', 'app.settings'],
            ]],
            ['Express.js Framework', '<h1>🚀 إطار عمل Express.js</h1>
<p>Express هو الإطار القياسي لـ Node.js، يوفر بنية مرنة لبناء تطبيقات الويب والـ APIs.</p>
<pre><code>const express = require(\'express\');
const app = express();

app.get(\'/\', (req, res) => {
  res.send(\'مرحباً بك في السيرفر!\');
});

app.listen(3000);</code></pre>', [
                ['ما الـ Middleware في Express؟', 'دالة تُنفّذ بين الطلب والاستجابة', 'قاعدة بيانات', 'ملف إعدادات', 'واجهة المستخدم'],
                ['ما الدالة لإرسال JSON في Express؟', 'res.json()', 'res.send()', 'res.data()', 'res.write()'],
                ['ما الـ object الذي يمثل الطلب في Express؟', 'req', 'request', 'ctx', 'query'],
                ['ما الـ object الذي يمثل الاستجابة؟', 'res', 'response', 'reply', 'result'],
                ['ما الأمر لتثبيت Express؟', 'npm install express', 'npm install expressjs', 'npm get express', 'npm add-express'],
            ]],

            ['REST API Design', '<h1>📡 تصميم REST APIs</h1>
<p>REST هي معايير دولية لتنظيم التواصل بين الـ Client والـ Server باستخدام بروتوكول HTTP.</p>
<table border="1" style="width:100%">
<tr><th>الطريقة</th><th>العملية</th></tr>
<tr><td>GET</td><td>جلب بيانات</td></tr>
<tr><td>POST</td><td>إضافة بيانات جديدة</td></tr>
<tr><td>PUT/PATCH</td><td>تعديل بيانات</td></tr>
<tr><td>DELETE</td><td>حذف بيانات</td></tr>
</table>', [
                ['ما HTTP method لإنشاء بيانات جديدة؟', 'POST', 'GET', 'CREATE', 'PUT'],
                ['ما معني الكود 404؟', 'المورد غير موجود', 'نجاح العملية', 'خطأ في السيرفر', 'غير مصرح'],
                ['ما HTTP status للنجاح "OK"؟', '200', '201', '404', '500'],
                ['ما الفرق بين PUT و PATCH؟', 'PUT للتعديل الكامل، PATCH للتعديل الجزئي', 'لا فرق', 'PATCH أقدم', 'PUT أسرع'],
                ['ما HTTP Status 201؟', 'Created: تم إنشاء المورد بنجاح', 'OK', 'Accepted', 'No Content'],
            ]],

            ['قواعد بيانات MongoDB', '<h1>🍃 MongoDB مع Mongoose</h1>
<p>MongoDB هي قاعدة بيانات NoSQL مثالية لـ Node.js لأنها تخزن البيانات كوثائق شبيهة بالـ JSON.</p>
<p>نستخدم <strong>Mongoose</strong> لتعريف هياكل البيانات (Schemas) داخل الكود.</p>
<pre><code>const userSchema = new mongoose.Schema({
  name: String,
  email: { type: String, required: true }
});</code></pre>', [
                ['ما نوع MongoDB؟', 'NoSQL Document Database', 'SQL Relational', 'Key-Value Store', 'Graph Database'],
                ['ما الـ Schema في Mongoose؟', 'هيكل بيانات الـ Collection', 'الـ Database نفسها', 'ملف الإعدادات', 'الواجهة'],
                ['ما الـ ORM المستخدم مع MongoDB في Node.js؟', 'Mongoose', 'Sequelize', 'Prisma', 'TypeORM'],
                ['ما وحدة البيانات في MongoDB؟', 'Document', 'Row', 'Record', 'Table'],
                ['ما دالة البحث عن وثيقة بـ ID؟', 'findById()', 'findOne()', 'getById()', 'fetch()'],
            ]],

            ['Authentication وJWT', '<h1>🔐 الأمان بـ JWT</h1>
<p>JSON Web Token (JWT) هي وسيلة آمنة لتسجيل دخول المستخدمين ونقل هويتهم بين المتصفح والسيرفر.</p>
<ul>
<li><strong>Header:</strong> نوع التشفير.</li>
<li><strong>Payload:</strong> بيانات المستخدم (مثل ID).</li>
<li><strong>Signature:</strong> التوقيع للتأكد من عدم التلاعب.</li>
</ul>', [
                ['ما اختصار JWT؟', 'JSON Web Token', 'Java Web Token', 'JavaScript Web Type', 'Joint Token'],
                ['أين يُخزّن JWT عادةً في الـ client؟', 'localStorage أو httpOnly cookie', 'قاعدة البيانات', 'الخادم', 'ملف النص'],
                ['ما أجزاء JWT؟', 'Header, Payload, Signature', 'Key, Value, Hash', 'User, Pass, Salt', 'Token, Secret, ID'],
                ['ما مكتبة التشفير الأشهر في Node.js؟', 'bcrypt', 'crypto', 'hash', 'security'],
                ['ما صلاحية JWT الافتراضية المُوصى بها؟', 'قصيرة (ساعة أو أقل)', 'سنة كاملة', 'لا تنتهي', 'يوم كامل'],
            ]],
            ['Socket.io والوقت الفعلي', '<h1>⚡ التواصل الفوري (Real-time)</h1>
<p>Socket.io تفتح اتصالاً دائماً بين السيرفر والمستخدم، مما يسمح بإرسال البيانات في اللحظة الفعلية.</p>
<pre><code>io.on(\'connection\', (socket) => {
  socket.emit(\'message\', \'أهلاً بك!\');
});</code></pre>', [
                ['ما فائدة Socket.io؟', 'الاتصال في الوقت الفعلي', 'تخزين الصور', 'تشفير كلمات المرور', 'تحليل البيانات'],
                ['ما نوع الاتصال في WebSockets؟', 'ثنائي الاتجاه (Bi-directional)', 'أحادي الاتجاه', 'متقطع', 'بطيء'],
                ['ما مفهوم "Room" في Socket.io؟', 'قناة خاصة لمجموعة من المستخدمين', 'غرفة في الواقع', 'ملف إعدادات', 'اسم مستعار'],
                ['ما دالة الانضمام لـ Room؟', 'socket.join(room)', 'socket.enter(room)', 'socket.add(room)', 'socket.to(room)'],
                ['ما حدث الاتصال في Socket.io على الخادم؟', 'connection', 'connect', 'open', 'init'],
            ]],

            ['Docker وDeployment', '<h1>🐳 توزيع ونشر التطبيقات</h1>
<p>Docker يسمح بتغليف تطبيقك في بيئة معزولة (Container) تضمن عمله بنفس الطريقة على أي سيرفر.</p>', [
                ['ما فائدة Docker؟', 'تغليف التطبيق في container يعمل في أي بيئة', 'قاعدة بيانات', 'إطار عمل', 'تصميم واجهة'],
                ['ما الملف الذي يصف Docker image؟', 'Dockerfile', 'docker.yml', 'container.json', 'image.config'],
                ['ما المنصة السحابية الأشهر لـ Node.js؟', 'Heroku أو Railway أو Render', 'GitHub', 'GitLab', 'Adobe'],
                ['ما Process Manager الأشهر لـ Node.js؟', 'PM2', 'Nodemon', 'Forever', 'Docker'],
                ['ما الفرق بين dev dependencies وdependencies؟', 'dev للتطوير فقط، dependencies للإنتاج', 'لا فرق', 'dev أسرع', 'لا شيء'],
            ]],

            ['Testing في Node.js', '<h1>🧪 الاختبارات الآلية (Testing)</h1>
<p>لضمان جودة الكود، نستخدم مكتبات مثل <strong>Jest</strong> لاختبار كل جزء من أجزاء التطبيق آلياً.</p>', [
                ['ما الـ framework الأشهر لاختبار Node.js؟', 'Jest', 'Mocha', 'Jasmine', 'Socket.io'],
                ['ما مفهوم Unit Testing؟', 'اختبار وحدة صغيرة من الكود', 'اختبار التطبيق كاملاً', 'اختبار الـ UI', 'اختبار يدوي'],
                ['ما مفهوم Integration Testing؟', 'اختبار تفاعل الوحدات مع بعضها', 'اختبار وحدة واحدة', 'اختبار الأداء', 'اختبار السمة'],
                ['ما المكتبة لاختبار HTTP requests؟', 'Supertest', 'Axios', 'Fetch', 'Request'],
                ['ما الـ assertion library الأشهر مع Jest؟', 'expect()', 'assert()', 'should()', 'check()'],
            ]],

            ['TypeScript مع Node.js', '<h1>🛡️ حماية الكود بـ TypeScript</h1>
<p>إضافة TypeScript للسيرفر تساعد على تجنب الكثير من الأخطاء المنطقية وتجعل الكود أسهل في الصيانة.</p>', [
                ['ما فائدة TypeScript في الـ Backend؟', 'أمان الأنواع واكتشاف الأخطاء مبكراً', 'أداء أسرع', 'سهولة الكتابة', 'تصغير الكود'],
                ['ما الـ framework الشهير لـ Node.js TypeScript؟', 'NestJS', 'Next.js', 'Nuxt.js', 'ExpresTS'],
                ['ما ملف إعدادات TypeScript؟', 'tsconfig.json', 'typescript.json', 'ts.config', 'settings.ts'],
                ['ما الأمر لتشغيل TypeScript مباشرةً؟', 'ts-node app.ts', 'node app.ts', 'tsc app.ts', 'run ts'],
                ['ما الـ decorator في NestJS؟', '@Controller()', '@Route()', '@Api()', '@Init()'],
            ]],

            ['Security في Node.js', '<h1>🔐 تأمين السيرفر</h1>
<p>حماية السيرفر من الهجمات (مثل SQL Injection أو XSS) هو جزء لا يتجزأ من عمل مطور الـ Backend.</p>', [
                ['ما هجوم SQL Injection؟', 'حقن كود SQL ضار في الاستعلامات', 'سرقة الـ JWT', 'هجوم DDOS', 'تخمين كلمة المرور'],
                ['ما الـ middleware لحماية Express؟', 'helmet', 'cors', 'morgan', 'body-parser'],
                ['ما CORS؟', 'سياسة مشاركة الموارد عبر الأصول', 'تشفير البيانات', 'بروتوكول اتصال', 'نوع قاعدة بيانات'],
                ['ما هجوم XSS؟', 'حقن JavaScript في صفحات الويب', 'سرقة قاعدة البيانات', 'هجوم الشبكة', 'تشفير خاطئ'],
                ['ما Rate Limiting؟', 'تحديد عدد الطلبات في وقت معين', 'تسريع الاستجابة', 'تشفير البيانات', 'ضغط الملفات'],
            ]],
        ]);

        // =============================================================
        // TRACK 6 – Python
        // =============================================================
        echo " Creating Track 6: Python...\n";
        $buildTrack('برمجة Python من الصفر', 'تعلم Python الأكثر شعبية في العالم للبيانات والويب والذكاء الاصطناعي.', [
            ['أساسيات Python', '<h1>🐍 لغة البرمجة Python</h1>
<p>Python هي اللغة الأكثر شعبية حالياً لبساطتها وقوتها في مجالات الذكاء الاصطناعي، الويب، والأتمتة.</p>
<pre><code># مثال بسيط للطباعة والمدخلات
name = input("ما اسمك؟")
print(f"أهلاً {name}")</code></pre>
<ul>
<li><strong>Easy to Read:</strong> تعتمد على المسافات (Indentation) بدلاً من الأقواس.</li>
<li><strong>Interpreted:</strong> يتم تنفيذ الكود سطر بسطر.</li>
</ul>', [
                ['من أنشأ Python؟', 'Guido van Rossum', 'James Gosling', 'Bjarne Stroustrup', 'Dennis Ritchie'],
                ['ما طريقة تعريف المتغيرات في Python؟', 'name = "value"', 'var name = "value"', 'String name = "value"', 'let name = "value"'],
                ['ما الـ indentation في Python؟', 'المسافات الإلزامية لتحديد الكتل البرمجية', 'نوع من التعليقات', 'تنسيق اختياري للجمال', 'أداة للبحث'],
                ['كيف تُعرّف دالة في Python؟', 'def my_function():', 'function my_function():', 'fun my_function():', 'void my_function():'],
                ['ما امتداد ملفات Python؟', '.py', '.python', '.pyt', '.pyc'],
            ]],

            ['هياكل البيانات في Python', '<h1>📊 القوائم والمجموعات</h1>
<p>توفر Python أدوات قوية لتخزين البيانات والتعامل معها بكفاءة:</p>
<ul>
<li><strong>List:</strong> مرتبة وقابلة للتعديل <code>[1, 2, 3]</code>.</li>
<li><strong>Tuple:</strong> مرتبة وغير قابلة للتعديل <code>(1, 2, 3)</code>.</li>
<li><strong>Dictionary:</strong> مفتاح وقيمة <code>{"key": "value"}</code>.</li>
</ul>', [
                ['ما الفرق بين List وTuple؟', 'List قابل للتعديل، Tuple ثابت', 'Tuple أطول', 'لا فرق بينهما', 'List أسرع من Tuple'],
                ['كيف تُنشئ Dictionary في Python؟', "{'key': 'value'}", "['key', 'value']", "(key, value)", "<key, value>"],
                ['ما الـ Set في Python؟', 'مجموعة عناصر فريدة بدون ترتيب', 'قائمة مرتبة', 'قاموس مفاتيح', 'نوع من البيانات الثنائية'],
                ['كيف تُضيف عنصر لـ List؟', 'list.append()', 'list.add()', 'list.push()', 'list.insert_end()'],
                ['ما نتيجة [1,2] + [3,4]؟', '[1,2,3,4]', '5', 'خطأ في الجمع', '[4,6]'],
            ]],

            ['OOP في Python', '<h1>🏗️ البرمجة الكائنية (OOP)</h1>
<p>تسمح لك البرمجة الكائنية بتنظيم الكود في شكل كلاسات (Classes) وكائنات (Objects) تحاكي الواقع.</p>
<pre><code>class Dog:
    def __init__(self, name):
        self.name = name

my_dog = Dog("Rex")</code></pre>', [
                ['ما __init__ في Python؟', 'دالة البناء (Constructor)', 'دالة الحماية', 'دالة النهاية', 'متغير عام'],
                ['ما self في Python؟', 'مرجع للكائن الحالي (Instance)', 'كلمة للحماية', 'متغير داخل النظام', 'دالة طباعة'],
                ['كيف تُعرّف كلاس يرث من كلاس آخر؟', 'class Child(Parent):', 'class Child extends Parent:', 'class Child : Parent:', 'class Child < Parent:'],
                ['ما مفهوم الـ Encapsulation؟', 'إخفاء التفاصيل الداخلية للكلاس وحمايتها', 'الوراثة المتعددة', 'تكرار الكود', 'تحويل الكود إلى ملف'],
                ['ما دالة `super()`؟', 'استدعاء دوال الكلاس الأب', 'دالة لحذف الكائن', 'دالة لطباعة الكلاس', 'دالة لنسخ الكود'],
            ]],

            ['Python للويب مع Django', '<h1>🌐 إطار العمل Django</h1>
<p>Django هو إطار ويب متكامل وشامل (Batteries Included) يركز على التطوير السريع والأمان العالي.</p>
<ul>
<li><strong>ORM:</strong> للتعامل مع قاعدة البيانات بدون SQL.</li>
<li><strong>Admin Panel:</strong> لوحة تحكم جاهزة للإدارة.</li>
</ul>', [
                ['ما فلسفة Django الأساسية؟', 'Don\'t Repeat Yourself (DRY)', 'Code is simple', 'Minimalist', 'Async first'],
                ['ما الـ MTV في Django؟', 'Model, Template, View', 'Model, Type, View', 'Main, Template, View', 'Module, Tool, View'],
                ['ما أمر بدء مشروع جديد؟', 'django-admin startproject', 'python create django', 'django init', 'npm init django'],
                ['ما الـ Migrations في Django؟', 'تطبيق تعديلات الـ Model على قاعدة البيانات', 'نقل الموقع لسيرفر آخر', 'حذف البيانات القديمة', 'تغيير لغة الموقع'],
                ['كيف يتم جلب كل المستخدمين بـ ORM؟', 'User.objects.all()', 'User.getAll()', 'SELECT * FROM User', 'User.fetch()'],
            ]],

            ['Python للـ API مع FastAPI', '<h1>⚡ FastAPI الحديث</h1>
<p>FastAPI هو إطار عمل سريع جداً لبناء APIs يعتمد على Python Type Hints ويوفر توثيقاً تلقائياً.</p>
<pre><code>from fastapi import FastAPI
app = FastAPI()

@app.get("/")
def read_root(): return {"Hello": "World"}</code></pre>', [
                ['بماذا يتميز FastAPI؟', 'السرعة العالية والتوثيق التلقائي', 'أنه الأقدم', 'دعم قواعد البيانات فقط', 'بساطة القوالب'],
                ['ما أداة التوثيق التلقائي في FastAPI؟', 'Swagger UI (docs)', 'Postman', 'Console', 'Excel'],
                ['ما مكتبة التحقق من البيانات (Validation)؟', 'Pydantic', 'NumPy', 'Django', 'Requests'],
                ['ما الكلمة المستخدمة لتعريف الدوال غير المتزامنة؟', 'async def', 'deferred', 'thread', 'await'],
                ['كيف يتم تشغيل تطبيق FastAPI؟', 'uvicorn main:app --reload', 'python run fastapi', 'node server.js', 'fastapi start'],
            ]],
            ['Data Analysis بـ Pandas', '<h1>📊 تحليل البيانات بـ Pandas</h1>
<p>Pandas هي المكتبة الأهم للتعامل مع البيانات الجدولية (مثل ملفات Excel). الهيكل الأساسي فيها هو <strong>DataFrame</strong>.</p>
<pre><code>import pandas as pd
df = pd.read_csv("data.csv")
print(df.head()) # عرض أول 5 صفوف</code></pre>', [
                ['ما الهيكل الأساسي في Pandas؟', 'DataFrame', 'Matrix', 'List', 'Array'],
                ['كيف تقرأ ملف CSV في Pandas؟', 'pd.read_csv()', 'pd.open_csv()', 'pd.get_csv()', 'pd.load()'],
                ['كيف تختار عموداً معيناً؟', 'df["column_name"]', 'df.get(column)', 'df.select(column)', 'df[0]'],
                ['ما الدالة للإحصاءات الأساسية؟', 'df.describe()', 'df.summary()', 'df.info()', 'df.stats()'],
                ['كيف تحذف القيم المفقودة؟', 'df.dropna()', 'df.remove_null()', 'df.clean()', 'df.delete_na()'],
            ]],

            ['Machine Learning بـ Scikit-learn', '<h1>🤖 تعلم الآلة (ML)</h1>
<p>تستخدم مكتبة Scikit-learn لبناء نماذج ذكاء اصطناعي يمكنها التنبؤ بالنتائج بناءً على البيانات التاريخية.</p>
<ul>
<li><strong>Supervised Learning:</strong> التعلم بوجود إجابات معروفة.</li>
<li><strong>Unsupervised Learning:</strong> اكتشاف الأنماط تلقائياً.</li>
</ul>', [
                ['ما الخطوة الأولى قبل تدريب النموذج؟', 'تقسيم البيانات train/test', 'حذف البيانات', 'طباعة النتائج', 'رفع الموقع'],
                ['ما الدالة لتدريب النموذج في sklearn؟', 'model.fit()', 'model.train()', 'model.run()', 'model.start()'],
                ['ما الـ overfitting؟', 'حفظ النموذج للبيانات بدلاً من فهمها', 'سرعة النموذج العالية', 'حجم الملف الكبير', 'نوع من الخوارزميات'],
                ['ما مكتبة sklearn؟', 'Scikit-learn لتعلم الآلة', 'Sky-learn للفضاء', 'Simple-learn للتعليم', 'Script-learn'],
                ['ما دالة التنبؤ؟', 'model.predict()', 'model.guess()', 'model.forecast()', 'model.check()'],
            ]],

            ['Data Visualization بـ Matplotlib', '<h1>📈 تمثيل البيانات بصرياً</h1>
<p>البشر يفهمون الصور أفضل من الأرقام، لذا نستخدم Matplotlib وSeaborn لتحويل الجداول إلى رسوم بيانية جذابة.</p>', [
                ['ما أشهر مكتبة للرسم البياني في Python؟', 'Matplotlib', 'Requests', 'Django', 'Pandas'],
                ['كيف ترسم خطاً بيانياً بسيطاً؟', 'plt.plot(x, y)', 'plt.draw(x, y)', 'plt.line(x, y)', 'plt.show()'],
                ['ما فائدة Seaborn؟', 'واجهة أجمل ورسوم إحصائية أسهل', 'تسريع بناء الـ API', 'تخزين البيانات', 'تأمين الموقع'],
                ['كيف تظهر الرسم على الشاشة؟', 'plt.show()', 'plt.display()', 'plt.render()', 'plt.print()'],
                ['كيف تضيف عنواناً للرسم؟', 'plt.title()', 'plt.label()', 'plt.header()', 'plt.text()'],
            ]],

            ['Automation وScraping', '<h1>🕷️ الزواحف الرقمية والأتمتة</h1>
<p>تعلم كيف تجمع البيانات من المواقع تلقائياً (Web Scraping) وأتمتة المهام المتكررة لتوفير الوقت.</p>', [
                ['ما المكتبة الأشهر لسحب بيانات HTML؟', 'BeautifulSoup', 'Flask', 'Pandas', 'NumPy'],
                ['ما المكتبة المستخدمة لإرسال طلبات الـ HTTP؟', 'requests', 'urllib', 'http_lib', 'web_get'],
                ['ما أداة أتمتة المتصفح (المحاكاة)؟', 'Selenium', 'Chrome', 'Firefox', 'HTML'],
                ['ما Scrapy؟', 'إطار عمل متكامل وقوي للـ Scraping', 'مكتبة صور', 'قاعدة بيانات', 'لغة برمجة'],
                ['ما الـ Bot؟', 'برنامج يقوم بمهام تلقائية', 'شخص حقيقي', 'نوع من ملفات الـ HTML', 'اسم نظام تشغيل'],
            ]],

            ['Python المتقدم', '<h1>🚀 مفاهيم بايثون المتقدمة</h1>
<p>تعلم خصائص اللغة القوية مثل Decorators لتحسين الكود، وGenerators لتوفير الذاكرة، وContext Managers لإدارة الموارد.</p>', [
                ['ما الـ Decorator في Python؟', 'دالة تغلف وتعدل سلوك دالة أخرى', 'قائمة بيانات', 'نوع من أنواع الكلاسات', 'زخرفة للواجهة'],
                ['ما الكلمة المستخدمة في الـ Generator؟', 'yield', 'return', 'give', 'output'],
                ['ما ميزة الـ Generator؟', 'توفير الذاكرة عند التعامل مع بيانات ضخمة', 'زيادة سرعة المعالج', 'تشفير الكود', 'تصغير حجم الملف'],
                ['ماLambda في Python؟', 'دالة سريعة مجهولة الاسم في سطر واحد', 'خوارزمية ذكاء اصطناعي', 'نوع متغير ثنائي', 'نوع من الروابط'],
                ['ما الـ Collections module؟', 'مكتبة توفر أنواع بيانات متقدمة', 'نوع من الـ lists', 'أداة للرسم', 'قاعدة بيانات'],
            ]],
        ]);

        // =============================================================
        // TRACK 7 – Cybersecurity
        // =============================================================
        echo " Creating Track 7: Cybersecurity...\n";
        $buildTrack('أمن المعلومات والأمن السيبراني', 'حماية الأنظمة والشبكات من الهجمات الإلكترونية.', [
            ['مقدمة في أمن المعلومات', '<h1>🛡️ حجر الأساس في الأمن السيبراني</h1>
<p>أمن المعلومات ليس مجرد تثبيت برامج مضادة للفيروسات، بل هو استراتيجية متكاملة تهدف إلى حماية الأصول الرقمية (البيانات، الأنظمة، والشبكات) من الوصول غير المصرح به أو التدمير أو التعديل.</p>

<h3>1. مُثلث التهديدات (CIA Triad)</h3>
<p>أي نظام أمني في العالم يُقاس بمدى تحقيقه للثلاثية التالية:</p>
<ul>
    <li><strong>Confidentiality (السرية):</strong> ضمان أن البيانات لا يراها إلا الأشخاص المصرح لهم فقط. (على سبيل المثال، تشفير الملفات).</li>
    <li><strong>Integrity (النزاهة):</strong> التأكد من أن البيانات لم يتم تعديلها أو التلاعب بها أثناء النقل أو التخزين. (استخدام الـ Hashing).</li>
    <li><strong>Availability (التوافر):</strong> ضمان أن الأنظمة والبيانات متاحة للمستخدمين المصرح لهم عند الحاجة إليها. (الحماية من هجمات DDoS).</li>
</ul>

<h3>2. أنواع المخترقين (Hackers Types)</h3>
<p>ينقسم المخترقون إلى فئات بناءً على نواياهم:</p>
<table border="1" style="width:100%; border-collapse: collapse; text-align: center;">
    <thead>
        <tr style="background-color: #333; color: white;">
            <th>الفئة</th>
            <th>الوصف</th>
            <th>الشرعية</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td><strong>White Hat</strong></td>
            <td>خبراء أمنيون يعملون لاكتشاف الثغرات وإصلاحها.</td>
            <td>قانوني/أخلاقي</td>
        </tr>
        <tr>
            <td><strong>Black Hat</strong></td>
            <td>مجرمون يهدفون للسرقة أو التخريب الشخصي.</td>
            <td>غير قانوني</td>
        </tr>
        <tr>
            <td><strong>Grey Hat</strong></td>
            <td>يخترقون الأنظمة دون إذن ولكن دون نية تخريبية غالباً.</td>
            <td>منطقة رمادية</td>
        </tr>
    </tbody>
</table>

<h3>3. مفاهيم أساسية يجب معرفتها</h3>
<ul>
    <li><strong>Vulnerability (الثغرة):</strong> هي نقطة ضعف في الكود أو الإعدادات يمكن استغلالها.</li>
    <li><strong>Exploit (الاستغلال):</strong> هو الكود أو الأداة التي تستعمل الثغرة لاختراق النظام.</li>
    <li><strong>Threat (التهديد):</strong> أي فعل محتمل قد يؤدي إلى ضرر (مثل المخترقين أو الفيروسات).</li>
    <li><strong>Risk (المخاطرة):</strong> احتمال وقوع التهديد مضروباً في حجم الضرر الناتج.</li>
</ul>

<h3>4. مراحل الهجوم الأخلاقي (Ethical Hacking Phases)</h3>
<p>المخترق المحترف لا يبدأ بالهجوم مباشرة، بل يتبع الخطوات التالية:</p>
<ol>
    <li><strong>Reconnaissance:</strong> جمع المعلومات عن الهدف (Passive or Active).</li>
    <li><strong>Scanning:</strong> فحص المنافذ والخدمات المفتوحة باستخدام أدوات مثل Nmap.</li>
    <li><strong>Gaining Access:</strong> استغلال الثغرات للوصول للنظام.</li>
    <li><strong>Maintaining Access:</strong> زرع "أبواب خلفية" للبقاء في النظام.</li>
    <li><strong>Clearing Tracks:</strong> مسح السجلات (Logs) لإخفاء أثر الاختراق.</li>
</ol>

<p align="center"><i>"الأمن هو عملية مستمرة وليس منتجاً تشتريه مرة واحدة." - بروس شناير</i></p>', [
                ['ما هما الركنان الأساسيان بجانب التوافر في CIA؟', 'السرية والنزاهة', 'السرعة والدقة', 'التشفير والنسخ احتياطي', 'الهوية والوصول'],
                ['ماذا يسمى المخترق الأخلاقي؟', 'White Hat Hacker', 'Black Hat Hacker', 'Grey Hat Hacker', 'Script Kiddie'],
                ['ما المقصود بـ Integrity (النزاهة)؟', 'عدم التعديل على البيانات بطريقة غير مصرح بها', 'تشفير البيانات بكلمة مرور', 'جعل الموقع يعمل 24 ساعة', 'سرعة استجابة السيرفر'],
                ['ما الفرق بين Vulnerability و Exploit؟', 'الثغرة هي نقطة الضعف، والاستغلال هو الأداة التي تخترقها', 'لا فرق بينهما', 'الاستغلال هو نقطة الضعف', 'الثغرة تكون في الهاردوير فقط'],
                ['ما المرحلة الأولى في عملية الاختراق الأخلاقي؟', 'جمع المعلومات (Reconnaissance)', 'استغلال الثغرات', 'مسح الآثار', 'زرع الفيروسات'],
                ['ما هو هجوم الـ DDoS باختصار؟', 'إغراق السيرفر بطلبات وهمية لتعطيله', 'سرقة حسابات فيسبوك', 'تخمين كلمة مرور الواي فاي', 'تشفير ملفات الجهاز'],
                ['أين تكمن خطورة الـ Social Engineering (الهندسة الاجتماعية)؟', 'في التلاعب بالعقول البشرية للحصول على أسرار', 'في كسر تشفير الشبكات', 'في برمجيات التجسس المتطورة', 'في سرقة كابلات الإنترنت'],
                ['ما مبدأ "Least Privilege"؟', 'إعطاء المستخدم أقل صلاحيات ممكنة لأداء عمله فقط', 'إعطاء كل الموظفين صلاحية المدير', 'عدم إعطاء أي صلاحيات لأحد', 'تغيير كلمة المرور كل يوم'],
                ['ما الفرق بين المخترق الرمادي (Grey Hat) والأسود؟', 'الرمادي يخترق بلا نية شريرة غالباً لكن بلا إذن', 'الأسود يحمي الشركات', 'لا يوجد فرق حقيقي', 'الرمادي لا يستخدم الكمبيوتر'],
                ['ما دور الـ Hashing في النزاهة؟', 'للتأكد أن الملف لم يتغير (مثل بصمة الإصبع)', 'لتصغير حجم الملفات', 'لتشفير الصور', 'لتسريع نقل البيانات عبر الإنترنت'],
            ]],
            ['أنواع الهجمات الإلكترونية', '<h1>☣️ خريطة الهجمات الإلكترونية المعاصرة</h1>
<p>لكي تتمكن من حماية الأنظمة، عليك أولاً أن تفهم كيف يفكر المهاجمون وما هي الأدوات التي يستخدمونها. الهجمات الإلكترونية تتطور يومياً لتصبح أكثر ذكاءً وتعقيداً.</p>

<h3>1. البرمجيات الخبيثة (Malware)</h3>
<p>ليست كل الفيروسات متشابهة، إليك التفصيل التقني لأهم أنواعها:</p>
<ul>
    <li><strong>Viruses:</strong> برامج تُلحق نفسها بملفات شرعية وتنتشر عند تشغيل تلك الملفات.</li>
    <li><strong>Worms (الديدان):</strong> برامج مستقلة تنتشر عبر الشبكة تلقائياً دون تدخل بشري، مستغلة الثغرات الأمنية.</li>
    <li><strong>Trojan Horse (حصان طروادة):</strong> برمجية تبدو مفيدة (مثل لعبة أو برنامج مجاني) لكنها تحتوي على كود خبيث بالداخل.</li>
    <li><strong>Ransomware (فيروس الفدية):</strong> يقوم بتشفير ملفات الضحية بالكامل ويطلب فدية ماليّة (غالباً بالعملات المشفرة) مقابل مفتاح فك التشفير.</li>
</ul>

<h3>2. هجمات حجب الخدمة (DoS & DDoS)</h3>
<p>الهدف من هذه الهجمات ليس السرقة، بل التعطيل:</p>
<ul>
    <li><strong>DoS (Denial of Service):</strong> هجوم من مصدر واحد لإغراق السيرفر.</li>
    <li><strong>DDoS (Distributed DoS):</strong> هجوم موزع من آلاف الأجهزة المخترقة (تسمى Botnets) في وقت واحد، مما يجعل من الصعب جداً حظره.</li>
</ul>

<h3>3. الهندسة الاجتماعية (Social Engineering)</h3>
<p>هذا النوع من الهجمات لا يستهدف الكود، بل يستهدف "الثغرة البشرية".</p>
<table border="1" style="width:100%; border-collapse: collapse;">
    <tr style="background-color: #f2f2f2;">
        <th>النوع</th>
        <th>طريقة الهجوم</th>
    </tr>
    <tr>
        <td><strong>Phishing (التصيد)</strong></td>
        <td>إرسال رسائل بريد إلكتروني وهمية تبدو كأنها من بنك أو شركة رسمية لسرقة البيانات.</td>
    </tr>
    <tr>
        <td><strong>Spear Phishing</strong></td>
        <td>تصيد مستهدف لشخص معين أو شركة معينة بناءً على معلومات جمعت عنه مسبقاً.</td>
    </tr>
    <tr>
        <td><strong>Vishing</strong></td>
        <td>التصيد عبر المكالمات الصوتية (Voice Phishing).</td>
    </tr>
</table>

<h3>4. هجمات تقنية متقدمة</h3>
<ul>
    <li><strong>Man-in-the-Middle (MitM):</strong> حيث يقوم المهاجم باعتراض الاتصال بين الضحية والسيرفر (مثل التجسس على شبكة واي فاي عامة).</li>
    <li><strong>Zero-Day Attack:</strong> هجوم يستغل ثغرة أمنية لم يكتشفها المطورون بعد، وبالتالي لا يوجد لها "تحديث أمني" (Patch).</li>
    <li><strong>SQL Injection:</strong> إدخال أوامر SQL خبيثة في حقول الإدخال للوصول لقاعدة البيانات (سنتناولها بالتفصيل في أمن التطبيقات).</li>
</ul>

<blockquote style="border-left: 5px solid #ccc; padding-left: 10px;">
    <i>"الشركات تنقسم لنوعين: شركات تعرضت للاختراق، وشركات لا تعرف أنها تعرضت للاختراق بعد." - جيمس كومي</i>
</blockquote>', [
                ['ما الفرق الجوهري بين الـ Virus والـ Worm؟', 'الـ Worm ينتشر تلقائياً عبر الشبكة، الفيروس يحتاج لتشغيل ملف', 'الفيروس أسرع', 'الـ Worm لا يضر الجهاز', 'الفيروس يعمل على الموبايل فقط'],
                ['ما هو الـ Trojan Horse (حصان طروادة)؟', 'برمجية خبيثة تتخفى في شكل برنامج مفيد', 'برنامج تشفير ملفات', 'هجوم لتعطيل الموقع', 'جهاز حماية من الاختراق'],
                ['ما الهدف من هجوم الـ DDoS؟', 'إخراج الموقع أو الخدمة عن الخدمة (تعطيله)', 'سرقة بيانات البطاقات الائتمانية', 'تغيير شكل الموقع', 'تشفير قاعدة البيانات'],
                ['ماذا يسمى هجوم التصيد الذي يستهدف شخصية هامة (مثل مدير شركة)؟', 'Whaling (صيد الحيتان)', 'Phishing عادي', 'Vishing', 'Smishing'],
                ['ما المقصود بـ Zero-Day Attack؟', 'هجوم يستغل ثغرة غير مكتشفة ولا يوجد لها علاج حالياً', 'هجوم يحدث في اليوم الأول من الشهر', 'هجوم يستمر لمدة 24 ساعة فقط', 'أضعف أنواع الهجمات'],
                ['أين تكمن خطورة هجوم الـ Man-in-the-Middle؟', 'في قدرة المهاجم على قراءة وتعديل البيانات المنقولة سراً', 'في حذف ملفات نظام التشغيل', 'في حرق المعالج (CPU)', 'في سرقة كابلات الألياف الضوئية'],
                ['ما هو الـ Botnet؟', 'شبكة من الأجهزة المخترقة تُستخدم لشن هجمات DDoS', 'برنامج دردشة ذكي', 'نوع من أنواع الـ Firewall', 'متصفح إنترنت آمن'],
                ['كيف يعمل الـ Ransomware (فيروس الفدية)؟', 'يشفر البيانات ويطلب مالاً لفكها', 'يسرق كلمة مرور الـ WiFi', 'يسرع الجهاز بشكل وهمي', 'يمسح سجل البحث'],
                ['ما هو الـ Vishing؟', 'التصيد الاحتيالي عن طريق المكالمات الهاتفية', 'التصيد عبر رسائل الـ SMS', 'التصيد عبر البريد الإلكتروني', 'التصيد داخل الألعاب'],
                ['ما هي أكثر ثغرة يتم استغلالها في الهندسة الاجتماعية؟', 'ثقة المستخدم الفطرية أو قلة وعيه الأمني', 'ثغرة في نظام ويندوز', 'ثغرة في أجهزة الرويتر', 'ثغرة في لغة الجافا سكريبت'],
            ]],
            ['التشفير وحماية البيانات', '<h1>🔐 علم التشفير: حماية الأسرار الرقمية</h1>
<p>التشفير (Cryptography) هو العلم الذي يحول البيانات من شكلها المفهوم (Plaintext) إلى شكل غير مفهوم (Ciphertext) لحمايتها من المتطفلين.</p>

<h3>1. التشفير المتماثل (Symmetric Encryption)</h3>
<p>في هذا النوع، يتم استخدام <strong>مفتاح واحد فقط</strong> لعمليتي التشفير وفك التشفير. يشبه ذلك قفل الباب الذي يفتح ويغلق بنفس المفتاح.</p>
<ul>
    <li><strong>المميزات:</strong> سريع جداً ومناسب للبيانات الضخمة.</li>
    <li><strong>العيوب:</strong> صعوبة تبادل المفتاح بأمان؛ فإذا حصل المهاجم على المفتاح، يمكنه فك تشفير كل شيء.</li>
    <li><strong>أشهر الخوارزميات:</strong> AES (Advanced Encryption Standard).</li>
</ul>

<h3>2. التشفير غير المتماثل (Asymmetric Encryption)</h3>
<p>يستخدم هذا النوع <strong>زوجاً من المفاتيح</strong>:</p>
<ul>
    <li><strong>Public Key (المفتاح العام):</strong> يمكن توزيعه على الجميع لتشفير البيانات المرسلة إليك.</li>
    <li><strong>Private Key (المفتاح الخاص):</strong> يبقى معك وحدك لفك تشفير ما تم إرساله.</li>
</ul>
<p>هذا النوع يحل مشكلة تبادل المفاتيح، ولكنه أبطأ من التشفير المتماثل. أشهر خوارزمياته: <strong>RSA</strong>.</p>

<h3>3. الـ Hashing (بصمة البيانات)</h3>
<p>الـ Hashing يختلف عن التشفير في أنه <strong>طريق واحد (One-way)</strong>، أي لا يمكن إعادة البيانات لأصلها بعد تحويلها. يستخدم للتأكد من نزاهة الملفات (Integrity).</p>
<pre><code># مثال لعملية Hashing بـ SHA-256
Input: "Hello" -> Hash: 2cf24dba5f...
Input: "hello" -> Hash: 2cf24dba5f... (أي تغيير بسيط يغير الـ Hash تماماً)</code></pre>

<h3>4. الشهادات الرقمية وبروتوكول HTTPS</h3>
<p>عندما تزور موقعاً يبدأ بـ HTTPS، فهذا يعني أن بياناتك مشفرة أثناء انتقالها بين المتصفح والسيرفر باستخدام بروتوكولات <strong>TLS/SSL</strong>، مما يمنع هجمات التجسس.</p>

<table border="1" style="width:100%; border-collapse: collapse;">
    <thead>
        <tr style="background-color: #e6f3ff;">
            <th>الميزة</th>
            <th>التشفير (Encryption)</th>
            <th>الهاش (Hashing)</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td>الاتجاه</td>
            <td>طريقان (تشفير وفك تشفير)</td>
            <td>طريق واحد (لا يمكن فكه)</td>
        </tr>
        <tr>
            <td>الاستخدام</td>
            <td>السرية (Confidentiality)</td>
            <td>النزاهة (Integrity)</td>
        </tr>
    </tbody>
</table>', [
                ['ما الفرق الرئيسي بين التشفير المتماثل وغير المتماثل؟', 'المتماثل يستخدم مفتاحاً واحداً، غير المتماثل يستخدم مفتاحين', 'المتماثل أحدث', 'غير المتماثل يستخدم في تشفير الصور فقط', 'لا فرق بينهما'],
                ['ما هي الخوارزمية القياسية المستخدمة عالمياً للتشفير المتماثل حالياً؟', 'AES', 'RSA', 'MD5', 'DES'],
                ['في التشفير غير المتماثل، أي مفتاح يستخدمه الآخرون لتشفير رسائلهم إليك؟', 'المفتاح العام (Public Key)', 'المفتاح الخاص (Private Key)', 'مفتاح الـ WiFi', 'مفتاح مشترك'],
                ['لماذا يستخدم الـ Hashing لكلمات المرور بدلاً من التشفير العادي؟', 'لأنه طريق واحد، فحتى لو سُرقت قاعدة البيانات لن يظهر الرقم السري الحقيقي', 'لأنه يجعل كلمة المرور أقصر', 'لأنه يغير لغة كلمة المرور', 'لأنه يحذف كلمة المرور بعد الاستخدام'],
                ['ما هو بروتوكول الأمان الذي حل محل SSL وما زال الناس يطلقون عليه نفس الاسم؟', 'TLS', 'HTTP', 'FTP', 'SSH'],
                ['ما ميزة خوارزميات التشفير غير المتماثل مثل RSA؟', 'حل مشكلة توزيع المفاتيح بشكل آمن', 'السرعة الفائقة في تشفير الملفات الضخمة', 'صغر حجم البيانات بعد التشفير', 'أنها لا تحتاج لمعالجات قوية'],
                ['ماذا يحدث للـ Hash إذا قمت بتغيير حرف واحد في ملف حجمه 10 جيجابايت؟', 'يتغير الـ Hash بالكامل وبشكل جذري', 'يتغير حرف واحد فقط في الـ Hash', 'لا يتغير الـ Hash أبداً', 'يتم مسح الملف تلقائياً'],
                ['أي من الخوارزميات التالية تعتبر ضعيفة وغير آمنة حالياً للـ Hashing؟', 'MD5', 'SHA-256', 'SHA-3', 'AES'],
                ['ما هي الوظيفة الأساسية لشهادة الـ SSL/TLS في المواقع؟', 'تشفير البيانات والتحقق من هوية صاحب الموقع', 'زيادة سرعة تحميل الصفحة', 'تغيير شكل التصميم', 'منع الإعلانات المنبثقة'],
                ['ما هو رمز القفل الذي يظهر في المتصفح بجانب رابط الموقع؟', 'يدل على أن الاتصال مشفر وآمن عبر HTTPS', 'يدل على أن الجهاز مصاب بفيروس', 'يدل على أن الموقع مغلق للصيانة', 'يدل على أن المتصفح يحتاج تحديث'],
            ]],
            ['أمن الشبكات', '<h1>🌐 حماية حدود الشبكة (Network Security)</h1>
<p>الشبكة هي الممر الذي تسلكه البيانات، وحمايتها تعني منع المهاجمين من اعتراض هذه البيانات أو الوصول إلى الأجهزة المتصلة.</p>

<h3>1. جدران الحماية (Firewalls)</h3>
<p>يعمل الجدار الناري كحارس بوابة (Gatekeeper) يراقب حركة المرور الواردة والصادرة بناءً على قواعد أمنية محددة.</p>
<ul>
    <li><strong>Packet Filtering:</strong> فحص كل حزمة بيانات بناءً على الـ IP والمنفذ (Port).</li>
    <li><strong>Next-Generation Firewall (NGFW):</strong> جدران ذكية تفحص محتوى البيانات نفسه (Deep Packet Inspection).</li>
</ul>

<h3>2. أنظمة كشف ومنع الاختراق (IDS & IPS)</h3>
<p>هذه الأنظمة تعمل ككاميرات مراقبة ذكية داخل الشبكة:</p>
<ul>
    <li><strong>IDS (Intrusion Detection System):</strong> يراقب الشبكة ويرسل تنبيهات عند اكتشاف نشاط مشبوه (نظام سلبي).</li>
    <li><strong>IPS (Intrusion Prevention System):</strong> لا يكتفي بالتنبيه، بل يقوم باتخاذ إجراء فوري لحظر الهجوم (نظام إيجابي).</li>
</ul>

<h3>3. الشبكات الخاصة الافتراضية (VPN)</h3>
<p>تنشئ الـ VPN نفقاً مشفراً (Encrypted Tunnel) عبر الإنترنت العام، مما يسمح للمستخدم بنقل البيانات بأمان كما لو كان داخل شبكة الشركة الخاصة.</p>

<h3>4. المنافذ والبروتوكولات (Ports & Protocols)</h3>
<p>كل خدمة على السيرفر تعمل عبر "منفذ" محدد. إغلاق المنافذ غير المستخدمة هو أحد أهم خطوات تأمين السيرفر.</p>
<table border="1" style="width:100%; border-collapse: collapse; text-align: center;">
    <tr style="background-color: #f9f9f9;">
        <th>المنفذ</th>
        <th>الخدمة</th>
        <th>الوصف</th>
    </tr>
    <tr>
        <td>22</td>
        <td>SSH</td>
        <td>التحكم بالسيرفر عن بعد بأمان.</td>
    </tr>
    <tr>
        <td>80</td>
        <td>HTTP</td>
        <td>تصفح الويب (غير مشفر).</td>
    </tr>
    <tr>
        <td>443</td>
        <td>HTTPS</td>
        <td>تصفح الويب الآمن (المشفر).</td>
    </tr>
    <tr>
        <td>21</td>
        <td>FTP</td>
        <td>نقل الملفات (قديم وغير آمن).</td>
    </tr>
</table>', [
                ['ما هي الوظيفة الأساسية لـ Firewall (الجدار الناري)؟', 'مراقبة وتصفية حركة مرور الشبكة بناءً على قواعد محددة', 'تسريع سرعة الإنترنت', 'تخزين المواقع المفضلة', 'تغيير شكل أيقونة الشبكة'],
                ['ما الفرق بين IDS و IPS؟', 'IDS يكتشف الهجوم فقط، بينما IPS يكتشفه ويمنعه فوراً', 'IPS أقدم من IDS', 'IDS يستخدم في المنازل فقط', 'لا يوجد فرق بينهما'],
                ['لماذا يستخدم الـ VPN في بيئات العمل؟', 'لإنشاء اتصال مشفر وآمن للوصول لموارد الشركة عبر الإنترنت', 'لزيادة سرعة تحميل الملفات', 'لتغيير خلفية سطح المكتب', 'لحجب الإعلانات فقط'],
                ['ما هو المنفذ (Port) الافتراضي لتصفح الويب المشفر HTTPS؟', '443', '80', '22', '21'],
                ['أي بروتوكول يستخدم للتحكم في السيرفرات عن بعد بشكل مشفر؟', 'SSH (Port 22)', 'Telnet', 'HTTP', 'SMTP'],
                ['ماذا يسمى الهجوم الذي يحاول فيه المهاجم فحص كل المنافذ المفتوحة في السيرفر؟', 'Port Scanning', 'Phishing', 'DDoS', 'SQL Injection'],
                ['ما هو الـ DMZ في أمن الشبكات؟', 'منطقة عازلة توضع فيها السيرفرات المتاحة للجمهور لحماية الشبكة الداخلية', 'برنامج مضاد فيروسات', 'نوع من أنواع الكابلات', 'منطقة لا يوجد بها إنترنت'],
                ['ما خطورة ترك بروتوكول Telnet مفتوحاً في الشبكة؟', 'لأنه يرسل البيانات وكلمات المرور بنص واضح (غير مشفر)', 'لأنه بطيء جداً', 'لأنه يستهلك البطارية', 'لأنه يعمل على ويندوز فقط'],
                ['أي طبقة من طبقات OSI يعمل فيها الجدار الناري التقليدي غالباً؟', 'الطبقة الثالثة والرابعة (Network & Transport)', 'الطبقة الأولى', 'الطبقة الثانية فقط', 'الطبقة السابعة فقط'],
                ['ما هو الـ Honeypot في أمن الشبكات؟', 'نظام وهمي يُوضع كفخ لجذب المهاجمين ودراسة أساليبهم', 'جهاز لتقوية إشارة الواي فاي', 'نوع من ملفات الكوكيز', 'كلمة مرور قوية جداً'],
            ]],
            ['أمن التطبيقات OWASP', '<h1>💻 أمان تطبيقات الويب (OWASP Top 10)</h1>
<p>أغلب عمليات الاختراق الكبرى تتم عبر ثغرات في كود الموقع نفسه. منظمة <strong>OWASP</strong> تصدر دورياً قائمة بأخطر 10 ثغرات أمنية يجب على كل مطور معرفتها وتجنبها.</p>

<h3>1. ثغرة حقن الأوامر (SQL Injection)</h3>
<p>تحدث عندما يقوم المهاجم بإدخال أوامر SQL خبيثة في حقول الإدخال (مثل حقل تسجيل الدخول) لتغيير استعلام قاعدة البيانات.</p>
<ul>
    <li><strong>الخطر:</strong> تسريب البيانات، حذف الجداول، أو تخطي عملية تسجيل الدخول.</li>
    <li><strong>الحل:</strong> استخدام <strong>Prepared Statements</strong> (الاستعلامات المُجهزة) وعدم دمج مدخلات المستخدم مباشرة في النص.</li>
</ul>

<h3>2. ثغرة البرمجة العبارة للمواقع (XSS)</h3>
<p>حقن كود JavaScript ضار في صفحة يراها مستخدمون آخرون.</p>
<ul>
    <li><strong>Stored XSS:</strong> الكود يُخزن في قاعدة البيانات (مثل تعليق) ويُنفذ عند كل من يقرأ التعليق.</li>
    <li><strong>Reflected XSS:</strong> الكود يُرسل عبر رابط (URL) ويُنفذ فوراً.</li>
    <li><strong>الحل:</strong> تعقيم المدخلات (Output Encoding) واستخدام Content Security Policy (CSP).</li>
</ul>

<h3>3. تزوير الطلبات عبر المواقع (CSRF)</h3>
<p>إجبار المتصفح على إرسال طلب غير مرغوب فيه إلى موقع آخر يكون المستخدم مسجلاً للدخول فيه بالفعل (مثل تغيير كلمة المرور دون علم المستخدم).</p>
<ul>
    <li><strong>الحل:</strong> استخدام <strong>Anti-CSRF Tokens</strong> في كل فورم برمجية.</li>
</ul>

<h3>4. ثغرة Broken Access Control</h3>
<p>فشل الموقع في التحقق من صلاحيات المستخدم؛ مثلاً أن يتمكن مستخدم عادي من الوصول لصفحة المدير عن طريق تخمين الرابط <code>/admin</code>.</p>

<table border="1" style="width:100%; border-collapse: collapse;">
    <tr style="background-color: #ffe6e6;">
        <th>الثغرة</th>
        <th>المبدأ الأمني المنتهك</th>
        <th>حل سريع</th>
    </tr>
    <tr>
        <td>SQL Injection</td>
        <td>نزاهة وسرية البيانات</td>
        <td>Parametrized Queries</td>
    </tr>
    <tr>
        <td>XSS</td>
        <td>ثقة المستخدم</td>
        <td>Htmlspecialchars / Sanitize</td>
    </tr>
    <tr>
        <td>Insecure Deserialization</td>
        <td>تكامل البيانات</td>
        <td>تجنب استقبال كائنات من المستخدم</td>
    </tr>
</table>', [
                ['ما هو اختصار منظمة OWASP؟', 'Open Web Application Security Project', 'Online Website Access Security Protocol', 'Official Web Application Security Panel', 'Open World Security Platform'],
                ['كيف تحمي الكود الخاص بك من ثغرة SQL Injection بشكل نهائي؟', 'استخدام الـ Prepared Statements والـ ORMs', 'تشفير قاعدة البيانات بالكامل', 'استخدام كلمات مرور طويلة للمستخدمين', 'إخفاء كود الـ PHP عن المستخدم'],
                ['ما هو الهدف الرئيسي لهجوم الـ Cross-Site Scripting (XSS)؟', 'تنفيذ كود JavaScript خبيث في متصفح الضحية لسرقة الـ Cookies', 'حذف ملفات السيرفر بالكامل', 'إيقاف قاعدة البيانات عن العمل', 'تغيير شكل أيقونات الموقع'],
                ['ثغرة الـ CSRF تعتمد بشكل أساسي على استغلال ماذا؟', 'ثقة المواقع في المتصفحات التي تخزن ملفات الكوكيز (Sessions)', 'ضعف خوارزمية التشفير في السيرفر', 'بطء اتصال الإنترنت لدى الضحية', 'استخدام لغة جافا قديمة'],
                ['ما هي ثغرة الـ Sensitive Data Exposure؟', 'عدم تشفير البيانات الحساسة مثل كلمات المرور والبطاقات البنكية', 'ظهور اسم المبرمج في الكود', 'استخدام خطوط غير واضحة في التصميم', 'بطء الموقع عند تحميل الصور'],
                ['ما المقصود بـ Broken Access Control؟', 'قدرة المستخدم على الوصول لموارد لا يملك صلاحية لها', 'تعطل لوحة التحكم في الموقع', 'نسيان كلمة مرور المدير', 'غلق الموقع للصيانة'],
                ['كيف تمنع هجمات الـ XSS في لغة PHP مثلاً؟', 'باستخدام دالة htmlspecialchars() لتعقيم مخرجات المستخدم', 'باستخدام دالة md5() لكل النصوص', 'بزيادة حجم الرامات في السيرفر', 'بمنع المستخدم من كتابة أي نص'],
                ['ما هو الـ Security Misconfiguration؟', 'ترك إعدادات السيرفر الافتراضية أو كشف تفاصيل الأخطاء (Debug info) للجمهور', 'استخدام ألوان غير متناسقة', 'عدم تحديث كارت الشاشة', 'كتابة تعليقات كثيرة في الكود'],
                ['ما هي ثغرة الـ XML External Entities (XXE)؟', 'استغلال معالج الـ XML لقراءة ملفات السيرفر المحلية', 'نوع من أنواع الفيروسات', 'هجوم لزيادة عدد زوار الموقع', 'ثغرة في ملفات الـ CSS'],
                ['لماذا يعتبر استخدام مكتبات (Libraries) قديمة خطراً أمنياً؟', 'لأنها قد تحتوي على ثغرات معروفة ومنشورة عالمياً (CVEs)', 'لأنها تجعل الموقع يبدو قديماً', 'لأنها لا تدعم اللغة العربية', 'لأنها تزيد من حجم الموقع'],
            ]],
            ['Linux للأمن السيبراني', '<h1>🐧 لينكس: نظام تشغيل الهاكرز</h1>
<p>لا يمكن احتراف الأمن السيبراني دون فهم عميق لنظام <strong>Linux</strong>. أغلب السيرفرات في العالم وأغلب أدوات الاختراق تعمل على لينكس لصلاحياته الواسعة ومرونته العالية.</p>

<h3>1. توزيعات اختبار الاختراق</h3>
<ul>
    <li><strong>Kali Linux:</strong> التوزيعة الأشهر عالمياً، تأتي محملة مسبقاً بمئات الأدوات الجاهزة للاختراق.</li>
    <li><strong>Parrot Security OS:</strong> بديل خفيف وأنيق لـ Kali، يركز على الخصوصية والتطوير.</li>
</ul>

<h3>2. أوامر التيرمينال الأساسية (Essential Commands)</h3>
<p>الهاكر المحترف يقضي 90% من وقته داخل الشاشة السوداء (Terminal):</p>
<pre><code># فحص الشبكة ومعرفة الأجهزة المتصلة
sudo nmap -sP 192.168.1.0/24

# البحث عن ملف يحتوي كلمة معينة
grep -r "password" /etc/configs

# تغيير صلاحيات ملف ليكون قابلاً للتنفيذ
chmod +x exploit.py</code></pre>

<h3>3. أهم الأدوات المدمجة في لينكس</h3>
<table border="1" style="width:100%; border-collapse: collapse;">
    <tr style="background-color: #333; color: white;">
        <th>الأداة</th>
        <th>الوظيفة</th>
    </tr>
    <tr>
        <td><strong>Nmap</strong></td>
        <td>فحص المنافذ واكتشاف الخدمات والأنظمة.</td>
    </tr>
    <tr>
        <td><strong>Wireshark</strong></td>
        <td>تحليل حزم البيانات التي تمر عبر الشبكة.</td>
    </tr>
    <tr>
        <td><strong>Metasploit</strong></td>
        <td>إطار عمل شامل لاستغلال الثغرات وتطويرها.</td>
    </tr>
    <tr>
        <td><strong>John the Ripper</strong></td>
        <td>أداة قوية لكسر كلمات المرور (Cracking).</td>
    </tr>
</table>

<h3>4. نظام الملفات والصلاحيات</h3>
<p>في لينكس، كل شيء هو ملف. فهم الـ <strong>Root</strong> وصلاحيات الـ <strong>Sudo</strong> هو المفتاح للسيطرة الكاملة على السيرفر (Privilege Escalation).</p>', [
                ['ما هي توزيعة Linux الأكثر شهرة واستخداماً في اختبار الاختراق؟', 'Kali Linux', 'Ubuntu', 'Windows Server', 'Mint'],
                ['ما هو الأمر الذي يمنح المستخدم "صلاحيات المدير" مؤقتاً في Linux؟', 'sudo', 'run-as-admin', 'root-me', 'please'],
                ['أداة Nmap تستخدم بشكل أساسي لـ؟', 'فحص الشبكة واكتشاف المنافذ المفتوحة والخدمات', 'تصفح الإنترنت بشكل خفي', 'تعديل الصور', 'كتابة الأكواد البرمجية'],
                ['ما هي وظيفة أداة Wireshark؟', 'التقاط وتحليل حزم البيانات (Packets) في الشبكة', 'تخمين كلمات مرور فيسبوك', 'تشفير القرص الصلب', 'تثبيت البرامج'],
                ['ما هو الـ Shell في Linux؟', 'الواجهة البرمجية التي تستقبل الأوامر وتنفذها', 'شاشة عرض الفيديو', 'نوع من أنواع الفيروسات', 'ملف نصي'],
                ['ما معنى الصلاحية 777 لملف في Linux؟', 'يمكن لأي شخص قراءة وكتابة وتنفيذ الملف', 'الملف للقراءة فقط للمدير', 'الملف مخفي ولا يمكن فتحه', 'الملف تالف'],
                ['أداة Metasploit تعتبر؟', 'إطار عمل (Framework) متكامل لاستغلال الثغرات', 'متصفح إنترنت سريع', 'لعبة فيديو للهاكرز', 'مضاد فيروسات'],
                ['ما الملف الذي يخزن كلمات المرور المشفرة (Hashes) للمستخدمين في Linux؟', '/etc/shadow', '/etc/passwords', '/home/user/secrets', '/root/data'],
                ['ما الأمر المستخدم في Linux لمعرفة الـ IP الخاص بالجهاز؟', 'ifconfig (أو ip a)', 'get-ip', 'whoami', 'netstat'],
                ['ما المقصود بـ Privilege Escalation؟', 'عملية رفع صلاحيات المخترق من مستخدم عادي إلى Root', 'تحميل الألعاب بشكل مجاني', 'زيادة سرعة المعالج', 'حذف السجلات'],
            ]],
            ['إدارة الهوية والوصول', '<h1>🔑 إدارة الهوية والوصول (IAM)</h1>
<p>IAM هي المعايير والتقنيات التي تضمن أن الأشخاص المناسبين لديهم الصلاحيات المناسبة للوصول إلى الموارد المناسبة في الوقت المناسب.</p>

<h3>1. مبدأ الامتيازات الأقل (Least Privilege)</h3>
<p>هو أهم قاعدة في الأمان: لا تعطي المستخدم صلاحيات أكثر مما يحتاج فعلياً لأداء وظيفته. هذا يقلل من حجم الضرر في حال سُرقت حسابات الموظفين.</p>

<h3>2. المصادقة متعدة العوامل (MFA)</h3>
<p>كلمة المرور وحدها لم تعد كافية. الـ MFA تطلب طريقتين أو أكثر لإثبات الهوية:</p>
<ul>
    <li>شيء تعرفه (Password).</li>
    <li>شيء تملكه (Phone, Token).</li>
    <li>شيء فيك (Biometrics - بصمة الإصبع أو الوجه).</li>
</ul>

<h3>3. تسجيل الدخول الموحد (SSO) والـ OAuth</h3>
<p><strong>SSO:</strong> يسمح للمستخدم بتسجيل الدخول مرة واحدة للوصول لعدة تطبيقات برمجية.</p>
<p><strong>OAuth:</strong> هو البروتوكول الذي تستخدمه عندما تسجل الدخول لموقع ما باستخدام حسابك في Google أو Facebook دون كشف كلمة مرورك للموقع الجديد.</p>', [
                ['ما هو اختصار IAM؟', 'Identity and Access Management', 'Identity and Account Module', 'Internal Access Manager', 'Internet Authentication Mode'],
                ['ما هو مفهوم مبدأ "Least Privilege"؟', 'منح المستخدم الحد الأدنى فقط من الصلاحيات اللازمة لعمله', 'إلغاء كل صلاحيات المستخدمين', 'منح المدير صلاحيات أقل من الموظف', 'منع الموظفين من استخدام الإنترنت'],
                ['المصادقة باستخدام بصمة الإصبع بجانب كلمة المرور تعتبر مثالاً على؟', 'Multi-Factor Authentication (MFA)', 'Single Factor Auth', 'No Auth', 'Social Engineering'],
                ['ما هي الـ Biometrics؟', 'الخصائص الحيوية مثل بصمة الوجه والعين', 'برامج الحماية من الفيروسات', 'اتصال إنترنت سريع', 'فحص ملفات النظام'],
                ['ماذا يسمى تسجيل الدخول لمرة واحدة للوصول لعدة خدمات؟', 'Single Sign-On (SSO)', 'Multi Sign-In', 'Google Login', 'Double Auth'],
                ['ما هو بروتوكول OAuth 2.0؟', 'بروتوكول يسمح لتطبيق بالوصول لموارد تطبيق آخر دون كشف كلمة المرور', 'بروتوكول لنقل الملفات', 'بروتوكول لزيادة سرعة السيرفر', 'نوع من أنواع التشفير'],
                ['ما الفرق بين المصادقة (Authentication) والترخيص (Authorization)؟', 'الأولى تتحقق من هويتك، والثانية تتحقق مما مسموح لك بفعله', 'لا يوجد فرق', 'الأولى خاصة بالدخول، والثانية خاصة بالخروج', 'الأولى للمديرين والثانية للموظفين'],
                ['ما هو الـ Brute Force Attack؟', 'هجوم يحاول تخمين كل احتمالات كلمة المرور حتى ينجح', 'هجوم لتعطيل السيرفر', 'هجوم عبر الهندسة الاجتماعية', 'تشفير ملفات الجهاز'],
                ['كيف يمكن حماية الموقع من هجمات تخمين كلمة المرور (Brute Force)؟', 'تطبيق سياسة Account Lockout (قفل الحساب بعد عدد محاولات خطأ)', 'تغيير ألوان الموقع', 'حذف حسابات المستخدمين', 'استخدام صور بدلاً من الكلمات'],
                ['ما هي قوة كلمة المرور المثالية؟', 'طويلة، تحتوي حروف كبيرة وصغيرة وأرقام ورموز', 'اسم المستخدم مكرر مرتين', 'تاريخ الميلاد فقط', 'كلمة "password123"'],
            ]],

            ['Ethical Hacking', '<h1>🎯 مراحل اختبار الاختراق (Penetration Testing)</h1>
<p>المخترق الأخلاقي يتبع منهجية علمية لاكتشاف الثغرات وتوثيقها قبل أن يستغلها المخترقون الأشرار.</p>

<h3>1. الاستطلاع وجمع المعلومات (Reconnaissance)</h3>
<p>هي أهم وأطول مرحلة، حيث يجمع المخترق كل معلومة ممكنة عن الهدف (عناوين IP، موظفين، تكنولوجيا مستخدمة).</p>
<ul>
    <li><strong>Passive:</strong> جمع معلومات من الإنترنت دون تواصل مباشر مع الهدف.</li>
    <li><strong>Active:</strong> استخدام أدوات للتفاعل مع أنظمة الهدف.</li>
</ul>

<h3>2. المسح والفحص (Scanning)</h3>
<p>استخدام أدوات مثل <strong>Nmap</strong> أو <strong>Nessus</strong> لاكتشاف الثغرات والخدمات المفتوحة.</p>

<h3>3. الاستغلال (Exploitation)</h3>
<p>محاولة الدخول للنظام فعلياً باستخدام "ثغرة" تم اكتشافها في المرحلة السابقة.</p>

<h3>4. إعداد التقارير (Reporting)</h3>
<p>المرحلة التي تميز المخترق الأخلاقي؛ حيث يقدم للشركة تقريراً مفصلاً يحتوي على:</p>
<ul>
    <li>الثغرات المكتشفة.</li>
    <li>درجة خطورتها.</li>
    <li>طرق إصلاحها (Remediation).</li>
</ul>', [
                ['ما الفرق بين الهاكر الأخلاقي (Ethical Hacker) والهاكر المجرّم؟', 'الأخلاقي يملك إذناً رسمياً ويهدف للإصلاح والتوثيق', 'الأخلاقي يستخدم ويندوز فقط', 'لا يوجد فرق تقني', 'المجرّم أقوى دائماً'],
                ['ما هي المرحلة الأولى والأكثر أهمية في عملية اختبار الاختراق؟', 'جمع المعلومات (Reconnaissance)', 'اختراق قاعدة البيانات', 'مسح السجلات', 'تثبيت الفيروسات'],
                ['ما الفرق بين الـ Active والـ Passive Reconnaissance؟', 'الـ Active يتضمن تفاعلاً مباشراً مع النظام (كالفحص)، الـ Passive لا', 'لا يوجد فرق', 'الـ Passive أخطر', 'الـ Active أبطأ'],
                ['أداة Nessus تستخدم بشكل أساسي لـ؟', 'فحص وكشف الثغرات بشكل آلي (Vulnerability Scanning)', 'تخمين كلمات المرور', 'رسم تصاميم المواقع', 'تحليل الصور'],
                ['ماذا يسمى اختبار الاختراق الذي لا يملك فيه المخترق أي معلومات مسبقة عن الهدف؟', 'Black Box Testing', 'White Box Testing', 'Grey Box Testing', 'No Box Testing'],
                ['ما هو الـ White Box Testing؟', 'اختبار يملك فيه المخترق وصولاً كاملاً للكود والبيانات مسبقاً', 'اختبار سطحي للموقع', 'اختبار الشبكة فقط', 'اختبار في ضوء النهار'],
                ['ما هو الـ Privilege Escalation في مرحلة الاستغلال؟', 'محاولة الحصول على صلاحيات المدير (Root) بعد الدخول كمستخدم عادي', 'محاولة زيادة سرعة التحميل', 'تجميل شكل الأيقونات', 'حذف النسخ الاحتياطية'],
                ['ما أهمية التقرير النهائي للشركة؟', 'يساعدهم على فهم الثغرات وإغلاقها لحماية أنفسهم', 'مجرد روتين غير مهم', 'لمطالبة الشركة بمبالغ مالية كبيرة', 'لنشر الثغرات على الإنترنت'],
                ['ما هو الـ Bug Bounty؟', 'برنامج تقدم فيه الشركات مكافآت لمن يكتشف ثغرات في أنظمتها ويبلغ عنها', 'فيروس يصيب الحشرات', 'نوع من أنواع الألعاب', 'اشتراك شهري'],
                ['أي أداة تستخدم لاعتراض وتحليل طلبات الويب (Proxy) في اختبار الاختراق؟', 'Burp Suite', 'Nmap', 'Excel', 'VLC Player'],
            ]],
            ['الاستجابة للحوادث', '<h1>🚨 الاستجابة للحوادث والأدلة الجنائية</h1>
<p>السؤال ليس "هل سأتعرض للاختراق؟" بل "متى سأتعرض للاختراق وكيف سأتعامل معه؟". الاستجابة للحوادث (Incident Response) هي الخطة التي تتبعها الشركات لتقليل الضرر واستعادة الأنظمة.</p>

<h3>1. مراحل الاستجابة للحوادث (PDCERF)</h3>
<ol>
    <li><strong>Preparation:</strong> إعداد الفريق والأدوات قبل وقوع أي هجوم.</li>
    <li><strong>Detection:</strong> اكتشاف الهجوم فور وقوعه من خلال أنظمة المراقبة.</li>
    <li><strong>Containment:</strong> عزل الأنظمة المصابة لمنع انتشار الهجوم (مثل فصل السيرفر عن الشبكة).</li>
    <li><strong>Eradication:</strong> حذف جذور الهجوم (مسح الفيروسات أو سد الثغرة).</li>
    <li><strong>Recovery:</strong> استعادة الأنظمة من النسخ الاحتياطية والتأكد من سلامتها.</li>
    <li><strong>Lessons Learned:</strong> تحليل ما حدث لتجنب تكراره مستقبلاً.</li>
</ol>

<h3>2. الأدلة الجنائية الرقمية (Digital Forensics)</h3>
<p>هي عملية جمع وتحليل الأدلة الرقمية بطريقة مقبولة قانونياً لتقديمها للعدالة.</p>
<ul>
    <li><strong>Chain of Custody:</strong> سجل يوثق من تعامل مع الدليل الرقمي ومن نقله لضمان عدم التلاعب به.</li>
    <li><strong>Volatile Data:</strong> البيانات التي تضيع بمجرد إطفاء الجهاز (مثل الذاكرة العشوائية RAM).</li>
</ul>', [
                ['ما هي الخطوة الأولى التي يجب اتخاذها فور اكتشاف اختراق نشط لسيرفر؟', 'Containment (العزل) لمنع انتشار الاختراق', 'إبلاغ الشرطة فوراً', 'تغيير ألوان واجهة الموقع', 'عمل نسخة احتياطية للبيانات المصابة'],
                ['ما المقصود بـ Digital Forensics؟', 'تحليل الأدلة الرقمية لمعرفة كيف وماذا حدث بعد وقوع هجوم', 'بناء أجهزة كمبيوتر جديدة', 'تصميم برامج مضادة للفيروسات', 'حماية الشبكات من هجمات DDoS'],
                ['ما أهمية سجلات الـ Logs أثناء الاستجابة للحوادث؟', 'تعمل كالبريد الوارد والصادر لمعرفة تحركات المهاجم', 'تزيد من سرعة السيرفر', 'ليس لها أهمية حقيقية', 'تستخدم لتجميل تقرير الأداء'],
                ['ما هي الـ SIEM؟', 'أنظمة لإدارة وتحليل الأحداث الأمنية مركزياً وتنبيه الفريق', 'نوع من أنواع الـ Firewall', 'متصفح ويب آمن', 'وحدة معالجة مركزية'],
                ['في الأدلة الجنائية، ماذا تعني الـ Volatile Data؟', 'البيانات التي تُمحى بمجرد انقطاع الطاقة مثل الرامات (RAM)', 'البيانات المخزنة على الأقراص الصلبة', 'البيانات المطبوعة على الورق', 'البيانات المشفرة'],
                ['ما هو الـ SOC؟', 'Security Operations Center - مركز العمليات الأمنية', 'اسم شركة برمجيات', 'نظام تشغيل قديم', 'نوع من أنواع الفيروسات'],
                ['لماذا تعتبر مرحلة "Lessons Learned" مهمة؟', 'لتطوير النظام الأمني وتجنب وقوع نفس الهجوم مستقبلاً', 'لتقديم استقالة فريق الأمن', 'لنشر أسرار الشركة', 'لا فائدة منها'],
                ['ماذا يسمى الشخص الذي يقوم بتحليل البرمجيات الخبيثة لمعرفة طريقة عملها؟', 'Malware Analyst', 'Web Designer', 'Database Admin', 'Sales Manager'],
                ['ما المقصود بـ Chain of Custody؟', 'توثيق مسار الدليل الرقمي لضمان عدم التلاعب به في المحكمة', 'سلسلة من كلمات المرور', 'سياسة استخدام الإنترنت في الشركة', 'طريقة لتشفير الملفات'],
                ['ماذا تفعل إذا طلبت منك جهة التحقيق "صورة" (Image) من القرص الصلب؟', 'أخذ نسخة طبق الأصل بت (Bit-for-bit) من القرص بالكامل', 'تصوير القرص بالكاميرا', 'نسخ ملفات الـ PDF فقط', 'إرسال القرص بالبريد'],
            ]],

            ['شهادات الأمن السيبراني', '<h1>🎓 مسارك المهني في الأمن السيبراني</h1>
<p>الأمن السيبراني مجال واسع، والشهادات المهنية هي طريقك لإثبات كفاءتك للشركات الكبرى. إليك أهم الشهادات حسب المستوى:</p>

<table border="1" style="width:100%; border-collapse: collapse;">
    <tr style="background-color: #f2f2f2;">
        <th>المستوى</th>
        <th>الشهادة</th>
        <th>جهة الإصدار</th>
    </tr>
    <tr>
        <td>مبتدئ</td>
        <td><strong>CompTIA Security+</strong></td>
        <td>CompTIA</td>
    </tr>
    <tr>
        <td>متوسط</td>
        <td><strong>CEH (Ethical Hacker)</strong></td>
        <td>EC-Council</td>
    </tr>
    <tr>
        <td>متقدم (عملي)</td>
        <td><strong>OSCP</strong></td>
        <td>Offensive Security</td>
    </tr>
    <tr>
        <td>إداري وخبير</td>
        <td><strong>CISSP</strong></td>
        <td>(ISC)²</td>
    </tr>
</table>

<h3>نصائح للبدء في المجال</h3>
<ul>
    <li>تعلم الشبكات (Network+) أولاً.</li>
    <li>أتقن نظام Linux وتيل التعامل مع الـ Terminal.</li>
    <li>مارس مهاراتك في منصات مثل TryHackMe أو HackTheBox.</li>
    <li>ابقَ مطلعاً على أحدث الأخبار (The Hacker News).</li>
</ul>', [
                ['ما هي الشهادة التي تعتبر "البداية المثالية" والمفضلة عالمياً للمبتدئين؟', 'Security+', 'CCNP', 'CISSP', 'OSCP'],
                ['شهادة الـ CEH تركز بشكل أساسي على؟', 'أدوات ومنهجيات الهاكر الأخلاقي', 'إدارة الشبكات المنزلية', 'تصميم واجهات المستخدم', 'برمجة تطبيقات الأندرويد'],
                ['ما الذي يميز شهادة OSCP عن غيرها؟', 'أنها شهادة عملية بنسبة 100% تتطلب اختراق عدة أجهزة في 24 ساعة', 'أنها شهادة نظريّة سهلة', 'أنها مجانية تماماً', 'أنها خاصة بمنتجات مايكروسوفت فقط'],
                ['شهادة الـ CISSP مخصصة لمن؟', 'الخبراء والمديرين الذين يملكون خبرة سنوات في المجال', 'الطلاب في السنة الأولى', 'المصممين الهواة', 'موظفي الاستقبال'],
                ['أي منصة تعتبر الأفضل للممارسة العملية لمهارات الاختراق في بيئة قانونية؟', 'TryHackMe أو HackTheBox', 'Facebook', 'YouTube', 'Wikipedia'],
                ['ما هو الـ CVE؟', 'قاعدة بيانات عالمية للثغرات الأمنية المكتشفة', 'نوع من أنواع التشفير', 'نظام تشغيل صيني', 'شهادة أمنية'],
                ['ما هي المنظمة المسؤولة عن شهادة الـ CEH؟', 'EC-Council', 'SANS', 'Microsoft', 'Google'],
                ['لماذا ينصح بتعلم الشبكات (Networking) قبل الأمن السيبراني؟', 'لأنك لا تستطيع حماية ما لا تفهم كيف يعمل في الأساس', 'لأن الشبكات أسهل بكثير', 'لأن وظائف الشبكات أكثر', 'ليس من الضروري تعلم الشبكات'],
                ['ما الفرق بين شهادات CompTIA وشهادات GIAC؟', 'CompTIA عامة ومحايدة، GIAC متخصصة جداً ومكلفة', 'لا يوجد فرق', 'GIAC للمبتدئين فقط', 'CompTIA خاصة بآبل'],
                ['ما هي أفضل طريقة للبقاء مطلعاً على ثغرات الـ Zero-Day الجديدة؟', 'متابعة النشرات الأمنية والمواقع التقنية المتخصصة', 'قراءة الجرائد اليومية', 'مشاهدة الأفلام', 'انتظار وصول رسائل البريد'],
            ]],
        ]);

        // =============================================================
        // TRACK 8 – UI/UX Design
        // =============================================================
        echo " Creating Track 8: UI/UX Design...\n";
        $buildTrack('تصميم UI/UX الاحترافي', 'تعلم تصميم تجارب مستخدم جذابة وسهلة الاستخدام باستخدام Figma.', [
            ['مقدمة في UX Design', '<h1>✨ فن تجربة المستخدم (User Experience)</h1>
<p>تصميم تجارب المستخدم (UX) ليس مجرد جعل الأشياء تبدو جميلة، بل هو علم يهتم بكيفية تفاعل المستخدم مع المنتج ومدى سهولة وفائدة هذا التفاعل.</p>

<h3>1. الفرق بين UI و UX</h3>
<p>غالباً ما يتم الخلط بينهما، لكن الفرق جوهري:</p>
<ul>
    <li><strong>UX (User Experience):</strong> تركز على رحلة المستخدم، حل المشكلات، والمنطق خلف التصميم. (ماذا يحدث؟)</li>
    <li><strong>UI (User Interface):</strong> تركز على الجماليات، الألوان، الأزرار، والخطوط. (كيف يبدو؟)</li>
</ul>

<h3>2. التفكير التصميمي (Design Thinking)</h3>
<p>هو منهجية عالمية لحل المشكلات المعقدة، وتتكون من 5 مراحل:</p>
<ol>
    <li><strong>Empathize:</strong> فهم المستخدم واحتياجاته من خلال التعاطف.</li>
    <li><strong>Define:</strong> تحديد المشكلة الحقيقية التي نحاول حلها.</li>
    <li><strong>Ideate:</strong> توليد أكبر قدر ممكن من الأفكار والحلول.</li>
    <li><strong>Prototype:</strong> بناء نماذج أولية سريعة للأفكار.</li>
    <li><strong>Test:</strong> اختبار النماذج مع مستخدمين حقيقيين.</li>
</ol>

<h3>3. أبحاث المستخدم (UX Research)</h3>
<p>المصمم المحترف لا يخمن، بل يبحث. الأدوات تشمل:</p>
<ul>
    <li><strong>User Personas:</strong> بناء شخصيات وهمية تمثل فئات المستخدمين الحقيقيين.</li>
    <li><strong>User Journey Maps:</strong> رسم خريطة لكل خطوة يخطوها المستخدم من بداية استخدامه للمنتج حتى النهاية.</li>
</ul>', [
                ['ما هو الهدف الرئيسي من تصميم تجربة المستخدم (UX)؟', 'جعل المنتج سهلاً وفعالاً ومرضياً للمستخدم', 'رسم صور ملونة جميلة', 'كتابة كود برمجي سريع', 'بيع المنتج بأغلى سعر'],
                ['ما الفرق الجوهري بين الـ UI والـ UX؟', 'الـ UX هو المنطق والرحلة، والـ UI هو الشكل والجماليات', 'لا يوجد فرق حقيقي', 'الـ UI للموبايل والـ UX للويب', 'الـ UX هو الكود والـ UI هو التصميم'],
                ['في أي مرحلة من مراحل Design Thinking يتم فهم احتياجات المستخدم؟', 'Empathize (التعاطف)', 'Testing', 'Prototyping', 'Ideate'],
                ['ما هي الـ User Persona؟', 'شخصية خيالية تمثل شريحة من المستخدمين الحقيقيين لاستهداف احتياجاتهم', 'صورة الملف الشخصي للمستخدم', 'اسم المبرمج الذي صمم التطبيق', 'شخصية مشهورة تعلن عن التطبيق'],
                ['ماذا يسمى "رسم خريطة لخطوات المستخدم داخل التطبيق"؟', 'User Journey Map', 'Sitemap', 'Code Structure', 'Wireframe'],
                ['ما المبدأ الذي ينص على أن "المستخدمين يقضون معظم وقتهم على مواقع أخرى، لذا يتوقعون أن يعمل موقعك بنفس الطريقة"؟', 'Jakob\'s Law', 'Hick\'s Law', 'Fitts\'s Law', 'Moore\'s Law'],
                ['ما المقصود بـ Accessibility في التصميم؟', 'جعل المنتج قابلاً للاستخدام من الجميع بما في ذلك ذوي الاحتياجات الخاصة', 'سرعة تحميل الصفحة', 'إمكانية فتح الموقع من أي متصفح', 'استخدام ألوان زاهية'],
                ['لماذا يعتبر "اختبار المستخدم" (Testing) خطوة ضرورية؟', 'لاكتشاف المشاكل التي لم يلحظها المصمم أثناء العمل', 'لأنه شرط قانوني', 'لزيادة سعر الخدمة', 'لجعل التصميم يبدو احترافياً فقط'],
                ['ما هو الـ Wireframe باختصار؟', 'هيكل بسيط للتصميم يركز على توزيع العناصر دون ألوان أو تفاصيل', 'تصميم نهائي جاهز للبرمجة', 'كود HTML للصفحة', 'شعار الشركة'],
                ['ما هي الـ Pain Points في الـ UX؟', 'المشاكل والعقبات التي تواجه المستخدم وتجعله يشعر بالإحباط', 'نقاط قوة التصميم', 'ألوان غير متناسقة', 'خطوط صغيرة جداً'],
            ]],

            ['Figma من الصفر', '<h1>🎨 احتراف Figma: أداة المصممين الأولى</h1>
<p>فيجما (Figma) ليست مجرد أداة رسم، بل هي منصة تعاونية تسمح للمصممين والمطورين بالعمل معاً في نفس الوقت على نفس المشروع.</p>

<h3>1. المكونات (Components)</h3>
<p>هي عناصر تصميم قابلة لإعادة الاستخدام (مثل الأزرار أو القوائم). إذا قمت بتغيير "الماستر"، يتغير التصميم في كل مكان استخدمت فيه هذا المكون.</p>

<h3>2. الـ Auto Layout (قوة التنظيم)</h3>
<p>أهم ميزة في Figma، تسمح بتصميم واجهات مرنة (Responsive) تتمدد وتتقلص تلقائياً مع المحتوى، تماماً مثل الـ Flexbox في البرمجة.</p>

<h3>3. النماذج التفاعلية (Prototyping)</h3>
<p>تحويل الصور الثابتة إلى تطبيق حي يمكن الضغط عليه. يمكنك تحديد كيف تتنقل الشاشات وما هي الأنميشنز (Animations) التي تظهر بينها.</p>

<table border="1" style="width:100%; border-collapse: collapse;">
    <tr style="background-color: #f0f0f0;">
        <th>الميزة</th>
        <th>الوصف الفني</th>
    </tr>
    <tr>
        <td>Variables</td>
        <td>تخزين قيم الألوان والخطوط والمسافات لاستخدامها كمتغيرات.</td>
    </tr>
    <tr>
        <td>Dev Mode</td>
        <td>وضع خاص للمطورين لرؤية المسافات والأكواد (CSS) بسهولة.</td>
    </tr>
    <tr>
        <td>Plugins</td>
        <td>إضافات برمجية تسرع العمل (مثل توليد نصوص وهمية أو أيقونات).</td>
    </tr>
</table>', [
                ['ما الذي يميز Figma عن برامج مثل Photoshop في تصميم الـ UI؟', 'أنها أداة Vector وتعتمد على السحابة (Cloud) والتعاون اللحظي', 'أنها مجانية بالكامل للأفراد', 'أنها تدعم تعديل الصور بشكل أقوى', 'أنها تعمل على الموبايل فقط'],
                ['في Figma، ماذا يحدث عند تعديل "Main Component"؟', 'تتحدث جميع النسخ المرتبطة به تلقائياً', 'يتم حذف التصميم', 'يتغير لونه فقط', 'لا يحدث شيء للنسخ الأخرى'],
                ['ما هي وظيفة ميزة Auto Layout؟', 'تنسيق العناصر تلقائياً وجعلها متجاوبة مع المحتوى', 'تلوين الأزرار بشكل آلي', 'تحسين جودة الصور', 'كتابة نصوص وهمية'],
                ['ماذا يسمى وضع Figma الذي يساعد المطورين على استخراج الأكواد؟', 'Dev Mode', 'Design Mode', 'Admin Mode', 'Build Mode'],
                ['كيف يمكنك إنشاء "نموذج تفاعلي" (Clickable Prototype)؟', 'عبر توصيل الشاشات في تبويب Prototyping', 'بكتابة كود JavaScript', 'بحفظ الملف كـ PDF', 'برسم أسهم باليد'],
                ['ما المقصود بـ "Constraints" في Figma؟', 'تحديد كيفية تفاعل العناصر عند تغيير حجم الشاشة (Frame)', 'فرض كلمة مرور على الملف', 'تقليل عدد الألوان المتاحة', 'تحديد وقت العمل'],
                ['ما هو الـ Frame في فيجما؟', 'حاوية تمثل شاشة الجهاز أو مساحة عمل مستقلة', 'مجرد صورة خلفية', 'اسم الخط المستخدم', 'أداة للرسم الحر'],
                ['أي اختصار بلوحة المفاتيح يستخدم لإنشاء Component سريعاً؟', 'Ctrl + Alt + K (أو Cmd + Option + K)', 'Ctrl + S', 'Ctrl + C', 'Ctrl + N'],
                ['ما هو الـ Smart Animate؟', 'خاصية في الـ Prototyping تحرك العناصر بسلاسة بين الشاشات المتشابهة', 'ذكاء اصطناعي يرسم واجهات كاملة', 'فلتر لتحسين الألوان', 'أداة لضغط الملفات'],
                ['ما هي الـ Plugins؟', 'إضافات خارجية تزيد من قدرات Figma (مثل Iconify أو Content Reel)', 'مشاكل تقنية في البرنامج', 'نوع من أنواع الخطوط', 'خلفيات جاهزة لسطح المكتب'],
            ]],

            ['Color Theory وTypography', '<h1>🎨 سيكولوجية الألوان وفن الخطوط</h1>
<p>التصميم السيء بـألوان جيدة قد ينجح، لكن التصميم الجيد بألوان سيئة سيفشل حتماً. الألوان والخطوط هي لغة التواصل الصامتة مع المستخدم.</p>

<h3>1. عجلة الألوان (Color Wheel)</h3>
<ul>
    <li><strong>Complementary:</strong> ألوان متقابلة (مثل الأزرق والبرتقالي)، تعطي تبايناً عالياً جداً.</li>
    <li><strong>Analogous:</strong> ألوان متجاورة (مثل الأزرق والأخضر)، تعطي شعوراً بالانسجام والهدوء.</li>
</ul>

<h3>2. سيكولوجية الألوان</h3>
<ul>
    <li><strong>الأزرق:</strong> الثقة، الأمان، والهدوء (لذا تستخدمه البنوك وتطبيقات التواصل).</li>
    <li><strong>الأحمر:</strong> الإثارة، الشغف، أو التحذير والخطورة.</li>
    <li><strong>الأخضر:</strong> الطبيعة، النمو، والنجاح (أو الحالة الإيجابية Success).</li>
</ul>

<h3>3. فن الخطوط (Typography)</h3>
<p>يجب اختيار الخطوط بناءً على سهولة القراءة (Readability) وليس الشكل فقط:</p>
<ul>
    <li><strong>Sans-serif:</strong> خطوط ناعمة بلا حواف بارزة (الأفضل للشاشات الرقمية).</li>
    <li><strong>Hierarchy:</strong> استخدام أحجام مختلفة للخطوط (H1, H2, Body) لتمكين المستخدم من "قراءة" الصفحة بالنظر السريع (Scanning).</li>
</ul>', [
                ['أي من أنواع الخطوط هو الأنسب للقراءة على الشاشات الرقمية؟', 'Sans-serif (بدون زوائد)', 'Serif (بزوائد)', 'Script (خط اليد)', 'Blackletter'],
                ['ماذا يسمى المبدأ الذي يحدد أهمية العناصر من خلال تباين الحجم واللون؟', 'Visual Hierarchy (التسلسل البصري)', 'Flat Design', 'Color Blindness', 'Grid System'],
                ['ما هي "الألوان التكميلية" (Complementary Colors)؟', 'ألوان متقابلة على عجلة الألوان وتوفر تبايناً حاداً', 'ألوان متشابهة جداً', 'تدرجات الرمادي فقط', 'ألوان الباستيل'],
                ['ما هو اللون الذي يُنصح باستخدامه لإعطاء شعور بالثقة والأمان؟', 'الأزرق', 'الأصفر', 'الأرجواني', 'الأحمر'],
                ['ما هو الـ Contrast Ratio الموصى به للمحتوى النصي لضمان سهولة الوصول (Accessibility)؟', '4.5:1 على الأقل', '1:1', '2:1', '100:1'],
                ['ماذا يسمى الفراغ الأبيض بين العناصر في التصميم؟', 'Whitespace (أو Negative Space)', 'Dead Zone', 'Empty Gap', 'Missing Content'],
                ['ما المقصود بـ Line Height؟', 'المسافة الرأسية بين سطور النص لضمان سهولة القراءة', 'طول السطر الواحد', 'حجم الخط', 'نوع الخط'],
            ]],

            ['Design Systems', '<h1>🏗️ أنظمة التصميم (Design Systems)</h1>
<p>نظام التصميم هو مجموعة من القواعد والمكونات الموحدة التي تسمح للفرق الكبيرة ببناء منتجات متناسقة بسرعة مذهلة. إنه ليس مجرد "مكتبة واجهات"، بل هو لغة مشتركة.</p>

<h3>1. التصميم الذري (Atomic Design)</h3>
<p>منهجية براد فروست التي تبني الواجهات كأنها مواد كيميائية:</p>
<ul>
    <li><strong>Atoms:</strong> العناصر الأساسية كالأزرار والحقول.</li>
    <li><strong>Molecules:</strong> مجموعة عناصر تعمل معاً (مثل محرك البحث بشريط ونص وزر).</li>
    <li><strong>Organisms:</strong> أقسام كاملة كالهيدر (Header).</li>
    <li><strong>Pages:</strong> الواجهة النهائية المكتملة.</li>
</ul>

<h3>2. الـ Design Tokens</h3>
<p>هي أصغر ذرات النظام (مثل رقم اللون <code>#FF0000</code> أو حجم الخط <code>16px</code>) تُخزن كمتغيرات (Variables) لضمان توافقها بين التصميم والبرمجة بالكامل.</p>', [
                ['ما هو الـ Design System باختصار؟', 'مجموعة من المعايير والمكونات الموحدة لضمان التناسق والسرعة', 'ملف يحتوي على صور التطبيق فقط', 'برنامج جديد للتصميم', 'قائمة بأسماء الموظفين'],
                ['من هو صاحب ومنشئ منهجية "Atomic Design"؟', 'Brad Frost', 'Steve Jobs', 'Jakob Nielsen', 'Elon Musk'],
                ['في الـ Atomic Design، ماذا يمثل الزر (Button) الواحد؟', 'Atom (ذرة)', 'Molecule (جزيء)', 'Organism (كائن)', 'Page'],
                ['ما هي فائدة الـ Design Tokens؟', 'توحيد قيم الألوان والمسافات بين التصميم والبرمجة بسهولة', 'تشفير ملفات التصميم', 'تحميل الصور بسرعة', 'ترجمة التطبيق'],
                ['أي نظام تصميم (Design System) تتبعه شركة Google؟', 'Material Design', 'Human Interface Guidelines', 'Ant Design', 'Fluent Design'],
                ['ما ميزة الـ Documentation (التوثيق) في نظام التصميم؟', 'شرح كيفية ومتى يجب استخدام كل عنصر والمواصفات التقنية له', 'إعطاء مظهر جميل للنظام', 'لا فائدة منها', 'تقليل حجم الملفات'],
                ['ما هو الـ Grid System؟', 'نظام شبكي يساعد على توزيع العناصر بتوازن وتناسق على الشاشة', 'نوع من أنواع الألوان', 'أداة للرسم ثلاثي الأبعاد', 'بروتوكول لنقل البيانات'],
                ['ما فائدة استخدام الـ Symbols أو Components في النظام؟', 'السرعة في التعديل الشامل وتجنب تكرار العمل', 'تغيير لغة التصميم', 'إخفاء الأخطاء', 'زيادة مساحة الملف'],
                ['ماذا يسمى النظام الذي تتبعه شركة Apple؟', 'Human Interface Guidelines (HIG)', 'Carbon', 'Polaris', 'Material Pro'],
                ['لماذا نحتاج "نسخة للمطورين" في الـ Design System؟', 'لضمان أن ما تم تصميمه يتم برمجته بدقة 100%', 'لجعل المطورين يشعرون بالسعادة فقط', 'لأن المطور لا يحب الألوان', 'لا نحتاجها، المطور يقرأ الصور'],
            ]],

            ['Wireframing وPrototyping', '<h1>📐 من الفكرة إلى الواقع التفاعلي</h1>
<p>قبل رسم الألوان والخطوط، يجب بناء الهيكل العظمي للتطبيق (Wireframe) ثم تحويله لنسخة تفاعلية (Prototype) لاختبار المنطق والوظائف.</p>

<h3>1. الـ Wireframes (المخططات الهيكلية)</h3>
<ul>
    <li><strong>Low-fidelity:</strong> رسومات سريعة (بالقلم أو أشكال بسيطة) تركز على مكان العناصر.</li>
    <li><strong>High-fidelity:</strong> مخططات أكثر دقة قريبة من الشكل النهائي لكن بدون الصور الحقيقية.</li>
</ul>

<h3>2. النماذج التفاعلية (Prototyping)</h3>
<p>الهدف هو رؤية "سريان المستخدم" (User Flow). هل الروابط تعمل؟ هل التنقل سهل؟</p>
<p><strong>Micro-interactions:</strong> هي الأنميشنز الصغيرة التي تظهر عند الضغط على زر أو تمرير القائمة، وهي ما تجعل التطبيق يبدو "حياً" وراقياً.</p>', [
                ['ما هو الفرق الأساسي بين الـ Wireframe والـ Prototype؟', 'الـ Wireframe هيكل ثابت، والـ Prototype نموذج تفاعلي يمكن الضغط عليه', 'لا يوجد فرق', 'الـ Wireframe للموبايل فقط', 'الـ Prototype يحتاج لكود برمجى'],
                ['ما الفائدة من عمل Low-fidelity Wireframes في البداية؟', 'توفير الوقت والتركيز على حل المشكلات والمنطق قبل تضييع الجهد في الجماليات', 'لأنها تبدو أجمل', 'لإبهار العميل بسرعة', 'لأن المصمم لا يحب الألوان في البداية'],
                ['ما المقصود بـ Micro-interactions في الـ UI؟', 'تفاعلات صغيرة مثل حركة أيقونة القلب عند الضغط عليها أو أنميشن الـ Loading', 'حوارات طويلة مع المستخدم', 'تصميمات كبيرة جداً', 'رسائل البريد الإلكتروني'],
                ['ما هو الـ User Flow؟', 'المسار الكلي الذي يسلكه المستخدم لإتمام مهمة معينة (مثل شراء منتج)', 'سرعة تدفق البيانات', 'كمية الضغط على الأزرار في الدقيقة', 'شكل الأيقونات في واجهة المستخدم'],
                ['أي أداة تستخدم لعمل Wireframes سريعة جداً وكأنها مرسومة باليد؟', 'Balsamiq', 'Photoshop', 'Excel', 'Visual Studio'],
                ['لماذا نقوم بعمل High-fidelity Prototypes قبل البرمجة الفعلية؟', 'لاختبار تجربة المستخدم بشكل واقعي وتوفير تكاليف التعديل بعد البرمجة', 'لزيادة حجم الملف فقط', 'لأن المطورين يطلبون ذلك دائماً', 'للحصول على جوائز تصميم'],
                ['ما هو الـ Feedback في الـ Prototype؟', 'رد فعل النظام عند قيام المستخدم بفعل معين (مثل ظهور رسالة نجاح)', 'تعليقات المدير على التصميم', 'اسم المصمم الذي قام بالعمل', 'صوت النقر على الماوس'],
            ]],
            ['Usability Testing', '<h1>🧪 اختبار قابلية الاستخدام (Usability Testing)</h1>
<p>التصميم الجميل لا يعني دائماً تصميماً ناجحاً. اختبار قابلية الاستخدام هو الطريقة الوحيدة للتأكد من أن المستخدمين يستطيعون فعلاً استخدام ما صممته دون إحباط.</p>

<h3>1. متى ولماذا نختبر؟</h3>
<p>نختبر في كل مراحل التصميم لتوفير الوقت والتكلفة. "اختبار 5 مستخدمين فقط يمكنه كشف 85% من مشاكل الاستخدام الكبرى" - (مبدأ نيلسن).</p>

<h3>2. أنواع طرق الاختبار</h3>
<ul>
    <li><strong>Moderated:</strong> يكون المصمم موجوداً لتوجيه المستخدم ومراقبته.</li>
    <li><strong>A/B Testing:</strong> عرض نسختين مختلفتين من نفس الصفحة على مستخدمين مختلفين لمعرفة أيهما تحقق نتائج أفضل.</li>
    <li><strong>Heatmaps:</strong> خرائط حرارية تظهر المناطق التي يضغط عليها المستخدمون بكثرة (مثل أداة Hotjar).</li>
</ul>

<h3>3. بروتوكول التفكير بصوت عالٍ (Think Aloud)</h3>
<p>نطلب من المستخدم التعليق بصوت عالٍ على كل ما يدور في ذهنه أثناء استخدام التطبيق، مما يساعدنا على فهم "لماذا" يفعل ما يفعله وليس فقط "ماذا" يفعل.</p>', [
                ['كم عدد المستخدمين الكافي لاكتشاف أغلب مشاكل الاستخدام الكبرى حسب دراسات Jakob Nielsen؟', '5 مستخدمين', '100 مستخدم', '1000 مستخدم', 'واحد فقط لا يكفي'],
                ['ما هو الـ A/B Testing في التصميم؟', 'مقارنة نسختين من التصميم (A و B) لمعرفة أيهما يعطي نتائج أفضل', 'اختبار التطبيق على أجهزة آبل فقط', 'تلوين التصميم بلونين فقط', 'اختبار الكود البرمجي'],
                ['ماذا تقيس "الخرائط الحرارية" (Heatmaps)؟', 'المناطق التي يتفاعل معها المستخدمون بكشره على الشاشة', 'درجة حرارة هاتف المستخدم', 'سرعة الإنترنت لدى المستخدم', 'عدد الألوان المستخدمة'],
                ['ما الهدف من "Think Aloud Protocol"؟', 'فهم أفكار ومبررات المستخدم أثناء استخدامه للتطبيق بصوت مسموع', 'حث المستخدم على الغناء', 'اختبار ميكروفون الجهاز', 'محاكاة الأوامر الصوتية'],
                ['ما الفرق بين Qualitative و Quantitative Testing؟', 'الأول يركز على "لماذا" والأسلوب، والثاني يركز على الأرقام والإحصائيات', 'لا يوجد فرق', 'الأول أسرع دائماً', 'الثاني للمصممين فقط'],
                ['ما هي الـ Eye Tracking في اختبار المستخدم؟', 'تقنية لتتبع حركة عين المستخدم ومعرفة أين ينظر بالضبط', 'فحص نظر المستخدم', 'أداة لتكبير الخط', 'نوع من أنواع الكاميرات'],
                ['متى يجب البدء في اختبار قابلية الاستخدام؟', 'في أقرب وقت ممكن (حتى في مرحلة الـ Wireframes السطحية)', 'بعد انتهاء البرمجة بالكامل', 'يوم إطلاق التطبيق للمتجر', 'لا نحتاجه إذا كان التصميم جميلاً'],
                ['ما هو الـ Task Completion Rate؟', 'نسبة المستخدمين الذين نجحوا في إتمام مهمة معينة بالتطبيق', 'سرعة كتابة الكود', 'عدد مرات تحميل التطبيق', 'الوقت المستغرق في التصميم'],
                ['ما المقصود بـ "System Usability Scale" (SUS)؟', 'استبيان معياري لقياس مدى سهولة استخدام النظام من وجهة نظر المستخدم', 'جهاز لقياس وزن الهاتف', 'برمجية لقياس استخدام الرامات', 'مقياس لجمال الألوان'],
                ['لماذا نفضل اختبار مستخدمين حقيقيين بدلاً من موظفي الشركة؟', 'لأن الموظفين لديهم معرفة مسبقة بالمنتج وقد يكونون متحيزين', 'لأن الموظفين لا يحبون الاختبار', 'لأن المستخدم الحقيقي مجاني', 'لا فرق بينهما'],
            ]],

            ['Accessibility في التصميم', '<h1>🌍 التصميم الشامل (Accessibility)</h1>
<p>التصميم من أجل الوصول (Accessibility) يعني التأكد من أن منتجك "قابل للاستخدام من قبل الجميع"، بما في ذلك الأشخاص الذين يعانون من إعاقات بصرية أو سمعية أو حركية.</p>

<h3>1. معايير WCAG</h3>
<p>هي المعايير العالمية لسهولة الوصول للويب، وتصنف إلى مستويات (A, AA, AAA). المستوى <strong>AA</strong> هو المعيار المقبول لمعظم الشركات.</p>

<h3>2. التباين والألوان (Color Contrast)</h3>
<p>يجب أن يكون تباين النص مع الخلفية واضحاً جداً (بنسبة 4.5:1 على الأقل) لتمكين ضعاف البصر من القراءة.</p>

<h3>3. القارئات الشاشية و Alt Text</h3>
<p>المكفوفون يستخدمون برامج تقرأ الشاشة. لذا يجب إضافة "نص بديل" (Alt Text) لكل صورة يشرح محتواها، لكي يتمكن البرنامج من قراءته لهم.</p>', [
                ['ما هو اختصار معايير سهولة الوصول العالمية للويب؟', 'WCAG', 'HTML', 'IEEE', 'W3C'],
                ['ما هو أقل معدل تباين (Contrast Ratio) مقبول للنص العادي حسب معايير AA؟', '4.5:1', '1:1', '2:1', '100:1'],
                ['ما هي وظيفة الـ Alt Text في الصور؟', 'وصف محتوى الصورة للأشخاص الذين يستخدمون قارئات الشاشة', 'تحسين جودة الصورة', 'تغيير حجم الصورة', 'تشفير الصورة'],
                ['لماذا لا يجب الاعتماد على "اللون فقط" لإيصال معلومة (مثل الخطأ باللون الأحمر)؟', 'لأن الأشخاص المصابين بعمى الألوان قد لا يلاحظون الفرق', 'لأن الألوان باهظة الثمن', 'لأن التصميم يبدو قديماً', 'لأن المتصفحات لا تدعم الألوان'],
                ['ما هو الـ Screen Reader؟', 'برنامج يحول النص والعناصر الظاهرة على الشاشة إلى صوت لمساعدة المكفوفين', 'جهاز لتكبير الشاشة', 'نوع من أنواع الشاشات الحديثة', 'تطبيق لتصوير الشاشة'],
                ['ما المقصود بـ "Keyboard Navigation" في الـ Accessibility؟', 'القدرة على تصفح واستخدام الموقع بالكامل عبر لوحة المفاتيح فقط', 'تغيير شكل أزرار الكيبورد', 'سرعة الكتابة على الكيبورد', 'استخدام الماوس فقط'],
                ['أي من المستويات التالية في معايير WCAG هو الأكثر صرامة وأصعبها تحقيقاً؟', 'AAA', 'A', 'AA', 'B'],
                ['ما فائدة استخدام الـ Semantic HTML (مثل <nav>, <header>) لسهولة الوصول؟', 'يساعد المتصفحات وقارئات الشاشة على فهم هيكل الصفحة ومعناها بوضوح', 'يجعل الكود ملوناً', 'يقلل من حجم الصفحة', 'يسرع تحميل الصور'],
                ['ما هو الـ Focus State في التصميم؟', 'مؤشر مرئي (مثل برواز حول الزر) يظهر عند التنقل بالكيبورد لمعرفة مكانك الحالي', 'حالة التركيز أثناء العمل', 'نوع من أنواع الخطوط', 'خلفية الصفحة'],
                ['تصميم "Dark Mode" يساعد بشكل أساسي في؟', 'تقليل إجهاد العين وتوفير البطارية وراحة بعض المستخدمين', 'زيادة سرعة الموقع', 'إخفاء العيوب في التصميم', 'تقليل عدد الصور'],
            ]],

            ['Mobile-First Design', '<h1>📱 التصميم للموبايل أولاً (Mobile-First)</h1>
<p>أغلب تصفح الإنترنت اليوم يتم عبر الموبايل. استراتيجية "الموبايل أولاً" تعني أن تبدأ بتصميم الشاشة الصغيرة وتحديد الأولويات القصوى، ثم التوسع للشاشات الأكبر.</p>

<h3>1. مبدأ "المنطقة الذهبية" (Thumb Zone)</h3>
<p>يجب وضع الأزرار التفاعلية الهامة في أسفل الشاشة حيث يسهل لمسها بإبهام اليد الواحدة دون مجهود.</p>

<h3>2. التصميم المتجاوب (Responsive Design)</h3>
<p>التصميم الذي يغير شكله وترتيب عناصره تلقائياً بناءً على حجم الشاشة (باستخدام Grids مرنة و Breakpoints).</p>

<h3>3. أهداف اللمس (Touch Targets)</h3>
<p>يجب أن تكون الأزرار كبيرة كفاية (44x44 بكسل على الأقل) لسهولة الضغط بالأصبع وتجنب الأخطاء.</p>', [
                ['ماذا تعني استراتيجية "Mobile-First Design"؟', 'البدء بتصميم نسخة الموبايل أولاً لتحديد الأولويات ثم التوسع للشاشات الكبيرة', 'إلغاء نسخة الكمبيوتر تماماً', 'صنع تطبيق أندرويد فقط', 'تصميم واجهة تشبه الهاتف'],
                ['ما هو الحجم الأدنى الموصى به لزر اللمس (Touch Target) لسهولة الضغط بالأصبع؟', '44x44 بكسل', '10x10 بكسل', '100x100 بكسل', 'غير مهم'],
                ['ما هي الـ Thumb Zone؟', 'المنطقة التي يسهل الوصول إليها بإبهام اليد الواحدة على شاشة الموبايل', 'منطقة بصمة الإصبع', 'الجزء العلوي من الشاشة', 'لوحة المفاتيح'],
                ['ما هو الفرق بين Responsive و Adaptive Design؟', 'الـ Responsive مرن ويتغير مع كل حجم، والـ Adaptive ينتقل بين أحجام ثابتة محددة', 'لا فرق بينهما', 'الـ Responsive للموبايل فقط', 'الـ Adaptive أسرع'],
                ['ماذا تسمى القائمة التي تختفي خلف أيقونة (≡) في الموبايل؟', 'Hamburger Menu', 'Pizza Menu', 'Breadcrumb', 'Slider'],
                ['لماذا نفضل وضع القوائم الرئيسية (Tab Bar) في أسفل شاشة الموبايل؟', 'لسهولة الوصول إليها بالإبهام (Thumb-friendly)', 'لأنها تبدو أجمل هناك', 'لأنها تخفي الجزء السفلي من الصور', 'لأن آيفون يفرض ذلك'],
                ['ما المقصود بـ "Gesture-based Navigation"؟', 'التنقل عبر السحب (Swipe) أو النقر المزدوج بدلاً من الأزرار التقليدية', 'التحدث للموبايل', 'استخدام الماوس مع الموبايل', 'هز الهاتف لتغيير الصفحة'],
                ['ما هي الـ Safe Area في تصميم الموبايل؟', 'المنطقة التي لا يغطيها الـ Notch أو منحنيات الشاشة في الهواتف الحديثة', 'مكان حفظ الصور', 'منطقة كلمات المرور', 'حقيبة الهاتف'],
                ['ما فائدة الـ Skeleton Screens أثناء التحميل؟', 'توفير انطباع بالسرعة عبر عرض هيكل الصفحة قبل تحميل المحتوى الفعلي', 'إخفاء الأخطاء البرمجية', 'تصغير حجم الصور', 'نوع من أنواع الخطوط'],
                ['لماذا تبتعد التصميمات الحديثة عن استخدام الـ Hover في الموبايل؟', 'لأن شاشات اللمس لا تدعم الوقوف بالماوس (No Hover state)', 'لأنها بطيئة', 'لأنها تستهلك طاقة', 'لأن الألوان لا تظهر فيها'],
            ]],

            ['Portfolio وPresentation', '<h1>💼 بناء الـ Portfolio الاحترافي</h1>
<p>أعمالك تتحدث عنك. المصمم الناجح ليس من لديه صور جميلة، بل من يستطيع شرح "كيف" وصل لهذا الحل.</p>

<h3>1. دراسة الحالة (Case Study)</h3>
<p>لا تضع صوراً فقط! اشرح مشروعك كقصة:</p>
<ul>
    <li>المشكلة (The Problem).</li>
    <li>الأبحاث (User Research).</li>
    <li>الحل (The Solution).</li>
    <li>النتائج (The Results).</li>
</ul>

<h3>2. التقديم (Presentation)</h3>
<p>استخدم موك آب (Mockups) واقعية لعرض تصميمك داخل هاتف أو لابتوب، فهذا يجعل العميل يتخيل المنتج في الواقع.</p>

<h3>3. التسليم (Design Handoff)</h3>
<p>هي عملية نقل التصميم للمطورين. يجب أن يكون ملفك منظماً، مع تسمية الطبقات (Layers) بوضوح وتوضيح حالات الأزرار (Hover, Active, Disabled).</p>', [
                ['ما هي المنصة الأكثر شهرة لعرض أعمال المصممين (Portfolio) عالمياً؟', 'Behance أو Dribbble', 'GitHub', 'Stack Overflow', 'Substack'],
                ['ما هو المحتوى الأهم في "دراسة الحالة" (Case Study) في الـ UX؟', 'شرح رحلة حل المشكلة والقرارات التصميمية وليس فقط الصور النهائية', 'كم عدد الساعات التي استغرقها العمل', 'قائمة بأسماء الأدوات المستخدمة', 'شكر وتقدير للأصدقاء'],
                ['ماذا يسمى عرض التصميم داخل صورة جهاز واقعي (مثل آيفون)؟', 'Mockup', 'Wireframe', 'Sitemap', 'Code'],
                ['ما المقصود بـ Design Handoff؟', 'عملية تسليم ملفات التصميم للمطورين لبرمجتها', 'ترك مهنة التصميم', 'الرسم باليد', 'تغيير ألوان شعار الشركة'],
                ['لماذا يفضل المطورون استخدام Figma للتسليم (Handoff)؟', 'لأنه يوفر لهم قياسات دقيقة وأكواد CSS وأصول الصور بسهولة', 'لأنه برنامج مجاني', 'لأنه يحتوي على ألعاب', 'لأنه يعمل بدون إنترنت'],
                ['ما هي الـ Iteration في عملية التصميم؟', 'تكرار وتحسين التصميم بناءً على التغذية الراجعة (Feedback)', 'سرقة تصميم من موقع آخر', 'تغيير اسم المشروع', 'حذف الملف والبدء من جديد'],
                ['ما أهمية الـ Storytelling في عرض أعمالك؟', 'تجعل العميل ينجذب لفهم منطقك وقدرتك على حل المشكلات كقصة مشوقة', 'لتضيع وقت المقابلة', 'لأن المصمم يجب أن يكون مؤلفاً', 'لا أهمية لها'],
                ['ماذا نضع في جزء "النتائج" (Results) في دراسة الحالة؟', 'بيانات توضح تحسن أداء المنتج (مثل زيادة المبيعات أو سهولة الاستخدام)', 'صور للمكتب الذي عملت فيه', 'قائمة بالخطوط المستخدمة', 'تاريخ انتهاء المشروع'],
                ['أي من هؤلاء هو الأهم عند بناء بورتفوليو للمبتدئ؟', 'التركيز على 2-3 مشاريع قوية ومشروحة بالتفصيل', 'وضع 100 صورة صغيرة بدون شرح', 'تغيير شكل الموقع كل يوم', 'استخدام ألوان كثيرة جداً'],
                ['ما هو الـ Personal Brand للمصمم؟', 'هويتك البصرية وأسلوبك الفريد الذي يميزك في سوق العمل', 'اسم شركتك الخاصة', 'ماركة ملابسك المفضلة', 'نوع الجهاز الذي تستخدمه'],
            ]],

            ['AI وتصميم المستقبل', '<h1>🤖 الذكاء الاصطناعي في عالم التصميم</h1>
<p>الذكاء الاصطناعي ليس تهديداً للمصمم، بل هو "مساعد طيار" (Co-pilot) يحررك من المهام التكرارية لتركز على الإبداع الاستراتيجي.</p>

<h3>1. الذكاء الاصطناعي التوليدي (Generative UI)</h3>
<p>أدوات يمكنها توليد واجهات كاملة بناءً على وصف نصي، أو تحويل Wireframe مرسوم باليد إلى تصميم ملون جاهز (مثل Figma AI).</p>

<h3>2. أبحاث المستخدم بالذكاء الاصطناعي</h3>
<p>استخدام AI لتحليل مئات المقابلات مع المستخدمين واستخراج الأنماط (Patterns) والمشاكل الشائعة في دقائق بدلاً من أسابيع.</p>

<h3>3. مستقبل الوظائف</h3>
<p>الوظائف لن تختفي، لكن المصمم الذي يستخدم الذكاء الاصطناعي سيتفوق بمراحل على المصمم التقليدي في السرعة والجودة.</p>', [
                ['كيف يمكن للذكاء الاصطناعي مساعدة المصممين في Figma؟', 'توليد مكونات جاهزة، تسمية الطبقات تلقائياً، وإنشاء نماذج سريعة', 'كتابة الكود البرمجي بالكامل وحذف المصمم', 'تغيير سرعة الإنترنت', 'لا علاقة لـ AI بـ Figma'],
                ['ما المقصود بـ "Generative UI"؟', 'واجهات مستخدم يتم توليدها ديناميكياً بواسطة الذكاء الاصطناعي', 'تصميم واجهة واحدة للأبد', 'نوع قديم من أنواع التصميم', 'تصميم المطبوعات'],
                ['أي أداة AI تشتهر بتوليد صور خيالية يمكن استلهام التصميم منها؟', 'Midjourney', 'Excel', 'Adobe Reader', 'WhatsApp'],
                ['كيف يساعد AI في أبحاث تجربة المستخدم (UX Research)؟', 'تحليل كميات كبيرة من آراء المستخدمين واستخراج النتائج بسرعة', 'كتابة السيرة الذاتية للمصمم', 'تغيير ألوان الصور', 'تخمين كلمات المرور'],
                ['ما هي ميزة "Magic Rename" في إضافات Figma؟', 'تسمية الطبقات (Layers) بأسماء منطقية تلقائياً باستخدام AI', 'تغيير اسم الملف إلى "Magic"', 'حذف الطبقات غير المستخدمة', 'تلوين الطبقات'],
                ['هل سيستبدل الذكاء الاصطناعي المصممين البشر تماماً؟', 'لا، ولكنه سيغير دورهم ليركزوا أكثر على الاستراتيجية والتفكير الإبداعي', 'نعم، خلال أشهر قليلة', 'الذكاء الاصطناعي لا يمكنه التصميم أبداً', 'فقط في تصميم الشعارات'],
                ['ما فائدة الذكاء الاصطناعي في الـ Usability Testing؟', 'محاكاة سلوك المستخدمين وتوقع المشاكل قبل الاختبار الحقيقي', 'تصليح أخطاء الهاردوير', 'توفير الكهرباء', 'زيادة جرافيك الألعاب'],
                ['ما هو الـ Prompt Engineering للمصممين؟', 'فن كتابة الأوامر النصية بدقة للحصول على أفضل النتائج من أدوات الـ AI', 'تغيير شاشة الكمبيوتر', 'نوع من أنواع الهندسة المعمارية', 'تصميم الملابس'],
                ['أي شركة تمتلك أداة "Firefly" للذكاء الاصطناعي المدمجة في الفوتوشوب؟', 'Adobe', 'Google', 'Microsoft', 'Figma'],
                ['ما هي النصيحة الأهم للمصممين في عصر الذكاء الاصطناعي؟', 'تعلم كيفية دمج أدوات AI في سير عملك لزيادة إنتاجيتك وإبداعك', 'التوقف عن تعلم التصميم', 'الاعتماد الكلي على AI لعمل كل شيء', 'تجنب استخدام AI تماماً'],
            ]],
        ]);

        // =============================================================
        // TRACK 9 – DevOps & Cloud
        // =============================================================
        echo " Creating Track 9: DevOps & Cloud...\n";
        $buildTrack('DevOps & Cloud', 'تعلم مفاهيم وإدارة البنية التحتية، الأتمتة، والنشر السحابي باستخدام أدوات DevOps والخدمات السحابية.', [
            ['مقدمة DevOps', '<h1>🚀 ثقافة الـ DevOps والأتمتة</h1>
<p>الـ DevOps ليس مجرد وظيفة أو أداة، بل هو "ثقافة" تجمع بين مبرمجي التطبيقات (Development) وخبراء إدارة الأنظمة (Operations) لضمان تسليم البرمجيات بسرعة وجودة عالية.</p>

<h3>1. دورة حياة DevOps</h3>
<p>تتكون من حلقة مستمرة تشمل:</p>
<ul>
    <li><strong>Plan:</strong> التخطيط للميزات.</li>
    <li><strong>Code & Build:</strong> كتابة الكود وتجميعه.</li>
    <li><strong>Continuous Testing:</strong> اختبار الكود آلياً عند كل تغيير.</li>
    <li><strong>Release & Deploy:</strong> نشر التطبيق في بيئة الإنتاج.</li>
    <li><strong>Monitor:</strong> مراقبة الأداء وتتبع الأخطاء لحظياً.</li>
</ul>

<h3>2. الـ CI/CD (القلب النابض للـ DevOps)</h3>
<ul>
    <li><strong>CI (Continuous Integration):</strong> دمج الكود بشكل مستمر في المستودع الرئيسي مع تشغيل اختبارات آلية لمنع تعارض الأكواد.</li>
    <li><strong>CD (Continuous Delivery/Deployment):</strong> أتمتة عملية النشر بحيث يصل الكود للعميل بمجرد نجاح الاختبارات دون تدخل يدوي.</li>
</ul>

<h3>3. البنية التحتية ككود (Infrastructure as Code)</h3>
<p>بدلاً من إعداد السيرفرات يدوياً، نكتب "أكواد" تصف شكل السيرفر المطلوب (مثل Terraform)، مما يجعل عملية إنشاء البيئات سريعة وقابلة للتكرار بدقة 100%.</p>', [
                ['ما هو الهدف الأساسي من منهجية الـ DevOps؟', 'كسر الحواجز بين فرق التطوير والعمليات لتسريع نشر البرمجيات وتحسين جودتها', 'تقليل عدد الموظفين في الشركة', 'إجبار المطورين على إدارة السيرفرات بأنفسهم', 'حذف مرحلة الاختبار'],
                ['ماذا يرمز اختصار CI في DevOps؟', 'Continuous Integration (التكامل المستمر)', 'Code Indexing', 'Central Intelligence', 'Cloud Infrastructure'],
                ['ما الفارق الأساسي بين Continuous Delivery و Continuous Deployment؟', 'في الـ Deployment يتم النشر للإنتاج آلياً فوراً، أما في الـ Delivery فقد يتطلب موافقة يدوية نهائية', 'لا يوجد فرق', 'الـ Delivery للأجهزة المحمولة فقط', 'الـ Deployment يحتاج لكود أكثر'],
                ['ما المقصود بـ Infrastructure as Code (IaC)؟', 'إدارة وإعداد البنية التحتية والسيرفرات من خلال ملفات كود بدلاً من الإعداد اليدوي', 'كتابة كود المواقع بالداخل', 'بناء خوادم خشبية', 'تشفير السيرفرات'],
                ['ما هي ميزة الـ Automation في DevOps؟', 'تقليل الأخطاء البشرية، توفير الوقت، وضمان اتساق البيئات', 'تغيير شكل الكود', 'زيادة كمية الكود', 'جعل التطبيق ملوناً'],
                ['في حلقة الـ DevOps، ماذا نستخدم لمراقبة أداء التطبيق بعد النشر؟', 'Monitoring Tools (مثل Prometheus أو Grafana)', 'أدوات التصميم', 'محررات الكود', 'برامج الرسم'],
                ['ما المقصود بـ "Shift Left" في الـ DevOps؟', 'البدء بعمليات الاختبار والأمان في مرحلة مبكرة جداً من التطوير', 'تحريك الأزرار لليسار في التصميم', 'تغيير اتجاه الكتابة', 'تأجيل الاختبار لنهاية المشروع'],
                ['ما هو الـ SRE؟', 'Site Reliability Engineering: تطبيق مفاهيم البرمجيات على إدارة العمليات', 'نوع من أنواع البرمجة', 'أداة لتصميم المواقع', 'بروتوكول أمان'],
                ['لماذا يعتبر الـ DevOps هاماً للشركات الكبرى؟', 'لأنه يتيح لهم إطلاق مئات التحديثات يومياً دون تعطل النظام', 'لأنه يقلل من فاتورة الكهرباء', 'لأنه يجعل المطور يكتب كوداً أقصر', 'لا فائدة منه للشركات الكبرى'],
                ['ما هو الـ Pipeline في CI/CD؟', 'سلسلة من الخطوات الآلية التي يمر بها الكود من البناء إلى الاختبار ثم النشر', 'أنبوب مياه حقيقي', 'نوع من أنواع قواعد البيانات', 'أداة رسم'],
            ]],

            ['Git وGitHub للفرق', '<h1>📂 إدارة الإصدارات والتعاون البرمجي</h1>
<p>نظام Git هو الأداة التي تسمح لمئات المطورين بالعمل على نفس "ملف الكود" في نفس الوقت دون أن يمسح أحدهم عمل الآخر.</p>

<h3>1. فروع العمل (Branching Strategy)</h3>
<ul>
    <li><strong>Main/Master Branch:</strong> الفرع الذي يحتوي على الكود "المستقر" والمنشور للجمهور.</li>
    <li><strong>Feature Branch:</strong> فرع جانبي يُنشئه المطور لإضافة ميزة جديدة، ثم يقوم بدمجه لاحقاً.</li>
</ul>

<h3>2. الـ Pull Requests و Code Review</h3>
<p>قبل دمج أي كود في المشروع، يفتح المطور "Pull Request" حيث يقوم زملاؤه بمراجعة الكود، كتابة ملاحظات، والتأكد من عدم وجود أخطاء قبل الموافقة على دمج الكود.</p>

<h3>3. أوامر Git المتقدمة</h3>
<ul>
    <li><code>git stash</code>: حفظ التغييرات الحالية مؤقتاً لتنظيف بيئة العمل دون عمل Commit.</li>
    <li><code>git rebase</code>: إعادة كتابة تاريخ الالتزامات لجعل السجل البرمجي أكثر تنظيماً.</li>
</ul>', [
                ['ما هو الفرق بين Git و GitHub؟', 'Git هو برنامج محلي لإدارة الإصدارات، وGitHub منصة سحابية لاستضافتها', 'لا فرق بينهما', 'GitHub هو لغة برمجة وGit محرر نصوص', 'Git أحدث من GitHub'],
                ['أي أمر يستخدم لحفظ التغييرات "مؤقتاً" دون تسجيلها كـ Commit؟', 'git stash', 'git save', 'git pause', 'git clean'],
                ['ما هو الـ Pull Request (أو Merge Request)؟', 'طلب لمراجعة كود في فرع معين تمهيداً لدمجه في فرع آخر', 'أمر لحذف الكود', 'طلب لزيادة الراتب', 'رسالة خطأ في Git'],
                ['ما الفائدة من الـ .gitignore؟', 'تحديد ملفات (مثل ملفات الإعدادات السرية) لا يرغب Git في تتبعها أو رفعها', 'تسريع عملية الـ Pull', 'تلوين الكود', 'حماية الملفات بكلمة سر'],
                ['ماذا يفعل الأمر git checkout -b feature-test؟', 'ينشئ فرعاً جديداً باسم feature-test وينتقل إليه فوراً', 'يحذف فرعاً قديماً', 'يدمج فرعين', 'يعرض قائمة بالفروع'],
                ['ما المقصود بـ Conflict في Git؟', 'تعارض بين تعديلين مختلفين تم إجراؤهما على نفس السطر في نفس الملف', 'مشاجرة بين المبرمجين', 'خطأ في الاتصال بالسيرفر', 'نوع من أنواع الفيروسات'],
                ['أي أمر يستخدم لجلب آخر التغييرات من السيرفر ودمجها مع كودك المحلي؟', 'git pull', 'git push', 'git update', 'git fetch only'],
                ['ماذا يسمى السجل الذي يحتوي على تاريخ جميع الـ Commits؟', 'Git Log', 'Git History', 'Git Record', 'Git Diary'],
                ['ما هو الـ Fork في GitHub؟', 'أخذ نسخة كاملة من مشروع شخص آخر لوضعه في حسابك والتعديل عليه بشكل مستقل', 'حذف المشروع', 'تغيير اسم المشروع', 'أداة للاختبار آلياً'],
                ['ما الفائدة من استخدام SSH Key مع GitHub؟', 'تسهيل الاتصال والرفع دون الحاجة لكتابة اسم المستخدم وكلمة السر في كل مرة وبأمان عالٍ', 'زيادة سرعة الإنترنت', 'تشفير ملفات البرمجة', 'منع المطورين من التعديل'],
            ]],

            ['Docker وContainerization', '<h1>🐳 دوكر (Docker): تغليف التطبيقات</h1>
<p>دوكر حل مشكلة "الكود يعمل في جهازي ولا يعمل في جهازك". من خلال Docker، نقوم بتغليف التطبيق مع كل احتياجاته (قاعدة بيانات، مكتبات، إعدادات) داخل "حاوية" (Container) تعمل بنفس الطريقة في أي مكان في العالم.</p>

<h3>1. الـ Image والـ Container</h3>
<ul>
    <li><strong>Image:</strong> هي "الخطة" أو القالب الساكن الذي يحتوي على تعليمات بناء الحاوية.</li>
    <li><strong>Container:</strong> هي النسخة الحية والفعالة التي تعمل بناءً على الـ Image.</li>
</ul>

<h3>2. ملف الـ Dockerfile</h3>
<p>هو ملف نصي نكتب فيه تعليمات بناء الـ Image. مثال:</p>
<pre><code>FROM node:18
WORKDIR /app
COPY . .
RUN npm install
CMD ["npm", "start"]</code></pre>

<h3>3. Docker Compose</h3>
<p>أداة تسمح بتشغيل عدة حاويات (مثل تطبيق ويب + قاعدة بيانات + كاش) معاً عبر ملف <code>yaml</code> واحد بكل سهولة.</p>', [
                ['ما هي المشكلة الأساسية التي حلها Docker؟', 'تضارب البيئات وعدم اتساق تشغيل التطبيقات بين الأجهزة المختلفة', 'بطء لغات البرمجية', 'صعوبة كتابة الكود', 'قلة مساحة القرص الصلب'],
                ['ما الفرق بين الـ Image والـ Container في دوكر؟', 'الـ Image هي القالب (الخطة)، والـ Container هو التطبيق الفعلي الذي يعمل حالياً', 'لا يوجد فرق', 'الـ Container هو صورة والـ Image هي فيديو', 'الـ Image أسرع'],
                ['ما هو الـ Docker Hub؟', 'مستودع سحابي لمشاركة وتحميل صور (Images) دوكر الجاهزة', 'برنامج لكتابة الكود', 'شركة استضافة', 'موقع أخبار برمجية'],
                ['أي أداة تستخدم لتشغيل عدة Container معاً بتنسيق واحد؟', 'Docker Compose', 'Docker Play', 'Docker Multi', 'Docker Run'],
                ['ما هو الـ Docker Image Layer؟', 'كل أمر في الـ Dockerfile ينشئ طبقة جديدة، مما يساعد في التخزين المؤقت (Caching) والسرعة', 'خلفية الشاشة', 'نوع من أنواع التشفير', 'طبقة حماية'],
                ['ما وظيفة الأمر RUN في ملف Dockerfile؟', 'تنفيذ أمر معين داخل الحاوية أثناء عملية بناء الـ Image (مثل تثبيت مكتبة)', 'تشغيل التطبيق النهائي', 'فتح متصفح الإنترنت', 'حذف الصور'],
                ['كيف يتواصل Container مع Container آخر؟', 'عبر إنشاء شبكة افتراضية (Docker Network) تربط بينهما', 'عبر البلوتوث', 'لا يمكنهما التواصل', 'عبر كابل USB'],
                ['ما هو الـ Volume في دوكر؟', 'مكان لتخزين البيانات بشكل دائم خارج الحاوية لضمان عدم فقدانها عند حذف الحاوية', 'قوة صوت السيرفر', 'حجم الذاكرة المستهلكة', 'مقياس لسرعة التطبيق'],
                ['ما المقصود بـ "Containerization"؟', 'عزل التطبيقات عن بعضها وعن نظام التشغيل لضمان الخفة والأمان', 'تحميل التطبيق في شاحنة', 'البرمجة داخل صندوق', 'نوع من أنواع التشفير'],
                ['أي أمر يستخدم لعرض جميع الحاويات (Containers) التي تعمل حالياً؟', 'docker ps', 'docker show', 'docker list', 'docker status'],
            ]],

            ['Kubernetes', '<h1>☸️ كوبرنيتيس (Kubernetes): قائد الأسطول</h1>
<p>إذا كان Docker يمنحك السفينة (Container)، فإن Kubernetes (أو K8s) هو القبطان الذي يدير أسطولاً من آلاف السفن. هو نظام "أوركسترا" لإدارة الحاويات آلياً.</p>

<h3>1. المفاهيم الأساسية</h3>
<ul>
    <li><strong>Cluster:</strong> مجموعة من الأجهزة (Nodes) تعمل معاً كقوة واحدة لإدارة الحاويات.</li>
    <li><strong>Pod:</strong> أصغر وحدة في Kubernetes، وهي حاوية واحدة أو مجموعة صغيرة من الحاويات تعمل معاً.</li>
    <li><strong>Deployment:</strong> يحدد عدد الـ Pods المطلوبة، ويقوم بتبديلها تلقائياً إذا فشلت.</li>
</ul>

<h3>2. ميزة Scaling و Self-healing</h3>
<p>إذا زاد الضغط على الموقع، يقوم Kubernetes بزيادة عدد الحاويات تلقائياً (Scaling). وإذا توقفت حاوية عن العمل، يقوم بقتلها وتشغيل واحدة جديدة بدلاً منها فوراً (Self-healing).</p>', [
                ['ما هي الوظيفة الأساسية لـ Kubernetes (K8s)؟', 'إدارة وتنسيق وتشغيل الحاويات (Containers) بشكل آلي واسع النطاق', 'تصميم واجهات المستخدم', 'برمجة قواعد البيانات', 'تأجير سيرفرات'],
                ['ما هو الـ Pod في عالم Kubernetes؟', 'أصغر وحدة قابلة للنشر وتحتوي على Container واحد أو أكثر', 'جهاز كمبيوتر فيزيائي', 'ملف كود', 'أداة رسم'],
                ['ماذا يسمى الجهاز (فيزيائي أو افتراضي) الذي يعمل داخل Cluster الكوبرنيتيس؟', 'Node (عقدة)', 'Pod', 'Service', 'Deployment'],
                ['ما المقصود بـ Self-healing في Kubernetes؟', 'القدرة على إعادة تشغيل الحاويات الفاشلة تلقائياً لضمان استمرارية الخدمة', 'شفاء الكود من الفيروسات', 'حذف الكود المعطوب', 'تصحيح الأخطاء اللغوية'],
                ['ما هي الـ Service في Kubernetes؟', 'طريقة لتعريف عنوان ثابت (IP/DNS) للوصول لمجموعة من الـ Pods', 'وظيفة برمجية', 'شركة تقنية', 'نوع من قواعد البيانات'],
                ['ما هو الـ Kubeclt؟', 'أداة سطر الأوامر المستخدمة للتحكم وإصدار الأوامر لـ Kubernetes Cluster', 'لغة برمجة', 'اسم المتصفح', 'أداة تصميم'],
                ['ماذا يفعل الـ Load Balancer داخل كوبرنيتيس؟', 'توزيع طلبات المستخدمين بالتساوي على الحاويات المتاحة لمنع الضغط عن واحدة دون غيرها', 'ضغط الصور', 'تلوين الأزرار', 'حفظ كلمات السر'],
                ['ما هو الـ Namespace؟', 'طريقة لتقسيم موارد الـ Cluster الواحد إلى أقسام افتراضية لمشاريع مختلفة', 'اسم المستخدم', 'اسم المتصفح', 'نوع خط'],
                ['ما المقصود بـ Horizontal Pod Autoscaling (HPA)؟', 'زيادة عدد الـ Pods تلقائياً عندما يزداد استهلاك المعالج (CPU) أو الذاكرة', 'تغيير شكل الموقع', 'حذف البيانات القديمة', 'تحديث النسخة يدوياً'],
                ['أين يتم تخزين إعدادات Kubernetes عادةً؟', 'في ملفات YAML', 'في ملفات Word', 'داخل قاعدة بيانات SQL', 'لا يتم تخزينها'],
            ]],

            ['AWS Fundamentals', '<h1>☁️ أساسيات الحوسبة السحابية (AWS)</h1>
<p>Amazon Web Services هي المنصة السحابية الأكبر في العالم. فبدلاً من شراء سيرفرات حقيقية ووضعها في مكتبك، تقوم بتأجيرها من أمازون والدفع فقط مقابل ما تستهلكه.</p>

<h3>1. الخدمات الأساسية</h3>
<ul>
    <li><strong>EC2 (Elastic Compute Cloud):</strong> سيرفرات افتراضية يمكنك تثبيت أي نظام تشغيل عليها.</li>
    <li><strong>S3 (Simple Storage Service):</strong> مخزن ضخم للملفات (صور، فيديوهات، نسخ احتياطية) بمدى توفر عالٍ جداً.</li>
    <li><strong>RDS (Relational Database Service):</strong> قواعد بيانات مُدارة بالكامل (SQL) تهتم أمازون بعمل التحديثات والنسخ الاحتياطي لها.</li>
</ul>

<h3>2. مفهوم Serverless (Lambda)</h3>
<p>خدمة تسمح لك بتشغيل "كود" فقط عند الحاجة دون وجود سيرفر يعمل 24 ساعة. تدفع فقط مقابل أجزاء من الثانية التي عمل فيها الكود!</p>', [
                ['ما الذي يميز الحوسبة السحابية (Cloud Computing) عن الاستضافة التقليدية؟', 'المرونة التامة والدفع مقابل الاستخدام الفعلي (Pay-as-you-go) والقدرة على التوسع الهائل', 'أنها مجانية بالكامل', 'أنها لا تحتاج لإنترنت', 'أنها مخصصة للصور فقط'],
                ['ما هي خدمة EC2 في AWS؟', 'سيرفرات افتراضية (Virtual Machines) لتشغيل التطبيقات', 'خدمة بريد إلكتروني', 'خدمة تخزين صور', 'قاعدة بيانات'],
                ['لأي غرض تستخدم خدمة Amazon S3 في الغالب؟', 'تخزين الملفات والصور والبيانات غير المنظمة بشكل سحابي وآمن', 'تشغيل كود Python', 'إرسال رسائل نصية', 'برمجة المواقع'],
                ['ما هي ميزة خدمة AWS Lambda؟', 'تشغيل الكود كخدمة (Serverless) دون الحاجة لإدارة أي سيرفرات', 'إنشاء مواقع ويب ثابتة', 'تخزين كلمات السر', 'تحرير الفيديوهات'],
                ['ما هو الـ Region والـ Availability Zone (AZ) في AWS؟', 'الـ Region منطقة جغرافية، والـ AZ هو مركز بيانات (Data Center) داخلها', 'أنواع حسابات مستخدمين', 'أنواع خطط دفع', 'أسماء لغات البرمجة'],
                ['ما هي خدمة RDS؟', 'خدمة قواعد بيانات علائقية مُدارة (مثل MySQL, PostgreSQL)', 'خدمة توصيل طلبات', 'برنامج للرسم', 'شبكة تواصل اجتماعي'],
                ['ما وظيفة خدمة Amazon CloudFront؟', 'شبكة توزيع محتوى (CDN) لتسريع وصول المحتوى للمستخدمين حول العالم', 'حماية السيرفر من الحريق', 'تغيير لغة الموقع تلقائياً', 'تخزين ملفات PDF'],
                ['ما هو الـ IAM في AWS؟', 'نظام لإدارة الهويات والوصول وتحديد من يسمح له بفعل ماذا داخل الحساب', 'لغة برمجة', 'نوع سيرفر', 'أداة للذكاء الاصطناعي'],
                ['ما المقصود بـ Auto Scaling في AWS؟', 'زيادة أو تقليل عدد السيرفرات (EC2) تلقائياً بناءً على حجم حركة المرور', 'تغيير لغة الموقع آلياً', 'حذف البيانات القديمة', 'تحديث السيرفر يدوياً'],
                ['لماذا تختار الشركات خدمة Serverless بدلاً من السيرفرات التقليدية أحياناً؟', 'لأنها تلغي تكاليف السيرفرات في أوقات الخمول وتوفر مجهود الإدارة التقنية', 'لأنها أبطأ وأكثر تعقيداً', 'لأنها تعمل بدون كهرباء', 'لا يوجد سبب واضح'],
            ]],
        ]);

        // =============================================================
        // TRACK 10 – Database Design
        // =============================================================
        echo " Creating Track 10: Database Design...\n";
        $buildTrack('تصميم قواعد البيانات والـ SQL', 'من التصميم المنطقي ERD إلى احتراف الاستعلامات المعقدة وإدارة الأداء.', [
            ['مقدمة وعالم البيانات', '<h1>📊 قواعد البيانات: مستودع ذكاء التطبيقات</h1>
<p>قاعدة البيانات ليست مجرد "جدول إكسل"، بل هي محرك معقد يضمن سلامة البيانات وسرعة استرجاعها تحت أصعب الظروف.</p>

<h3>1. SQL vs NoSQL</h3>
<ul>
    <li><strong>SQL (العلائقية):</strong> تعتمد على الجداول والعلاقات الصارمة (مثل PostgreSQL, MySQL). ممتازة للبيانات المالية والمسجلة بدقة.</li>
    <li><strong>NoSQL (غير العلائقية):</strong> تعتمد على الوثائق (JSON) أو المفاتيح. ممتازة للبيانات الضخمة والمتغيرة بسرعة (مثل MongoDB, Redis).</li>
</ul>

<h3>2. المعرفات والعلاقات</h3>
<ul>
    <li><strong>Primary Key:</strong> الرقم الفريد الذي لا يتكرر أبداً داخل الجدول (مثل الرقم القومي).</li>
    <li><strong>Foreign Key:</strong> حقل يربط جدولاً بجدول آخر (مثل ربط "الطلب" بـ "رقم المستخدم").</li>
</ul>', [
                ['ما هو الفرق الجوهري بين SQL و NoSQL؟', 'الـ SQL تعتمد على الجداول والمخطط الثابت، والـ NoSQL مرنة وتعتمد غالباً على الوثائق', 'الـ SQL للويب والـ NoSQL للموبايل', 'الـ NoSQL مجانية والـ SQL مدفوعة', 'لا يوجد فرق حقيقي'],
                ['ما هو الـ Primary Key؟', 'معرّف فريد لكل صف في الجدول يمنع تكرار البيانات', 'الاسم الأول للمستخدم', 'تاريخ إنشاء الجدول', 'نوع قاعدة البيانات'],
                ['أي من أنظمة قواعد البيانات التالية يعتبر NoSQL؟', 'MongoDB', 'PostgreSQL', 'MySQL', 'Oracle'],
                ['ما هي وظيفة الـ DBMS؟', 'نظام برمجي لإدارة وإنشاء والتحكم في قواعد البيانات', 'لغة برمجة لتصميم المواقع', 'جهاز كمبيوتر قوي', 'نوع من الشاشات'],
                ['ماذا يرمز اختصار ACID في قواعد البيانات؟', 'مجموعة خصائص تضمن موثوقية المعاملات (Atomicity, Consistency, Isolation, Durability)', 'أحماض كيميائية', 'نوع من التشفير', 'سرعة المعالجة'],
                ['ما المقصود بـ Data Redundancy؟', 'تكرار البيانات غير الضروري الذي يؤدي لهدر المساحة وصعوبة التحديث', 'سرعة استرجاع البيانات', 'تأمين البيانات', 'نسخ البيانات احتياطياً'],
                ['ماذا يعني "مخطط قاعدة البيانات" (Schema)؟', 'الهيكل التنظيمي الذي يصف الجداول والحقول والعلاقات بينها', 'كلمة سر قاعدة البيانات', 'واجهة المستخدم', 'الخادم الذي تعمل عليه'],
                ['ما هي العلاقة من نوع (One-to-Many)؟', 'علاقة حيث يمكن لواحد في الجدول (أ) أن يرتبط بعدة عناصر في الجدول (ب)', 'علاقة صداقة', 'علاقة برمجية معقدة', 'لا توجد هكذا علاقة'],
                ['ما فائدة الـ Foreign Key؟', 'الحفاظ على "التكامل المرجعي" (Referential Integrity) وربط الجداول ببعضها بذكاء', 'تلوين الجداول', 'تسريع البحث فقط', 'تشفير البيانات'],
                ['أي قاعدة بيانات تشتهر بكونها "Key-Value store" وتعمل في الذاكرة (In-Memory)؟', 'Redis', 'SQL Server', 'SQLite', 'MariaDB'],
            ]],

            ['لغة SQL من الصفر', '<h1>🔍 لغة SQL: كيف تتحدث مع البيانات؟</h1>
<p>SQL هي اللغة العالمية الموحدة للتخاطب مع أغلب قواعد البيانات في العالم. تعلمها هو المهارة الأهم لأي مطور باك-إند.</p>

<h3>1. عمليات الـ CRUD</h3>
<p>هي العمليات الأربع الأساسية التي نقوم بها على أي بيانات:</p>
<ol>
    <li><strong>Create (INSERT):</strong> إضافة بيانات جديدة.</li>
    <li><strong>Read (SELECT):</strong> قراءة واسترجاع البيانات.</li>
    <li><strong>Update (UPDATE):</strong> تعديل بيانات موجودة.</li>
    <li><strong>Delete (DELETE):</strong> حذف البيانات.</li>
</ol>

<h3>2. التصفية والترتيب</h3>
<p>نستخدم <code>WHERE</code> لتحدي شروط معينة، و <code>ORDER BY</code> لترتيب النتائج أبجدياً أو رقمياً.</p>', [
                ['أي كلمة مفتاحية تستخدم لاسترجاع البيانات من الجدول؟', 'SELECT', 'GET', 'EXTRACT', 'QUERY'],
                ['كيف يمكنك اختيار المستخدمين الذين تزيد أعمارهم عن 20 عاماً؟', 'SELECT * FROM users WHERE age > 20', 'GET age > 20', 'FIND users age 20', 'FILTER age > 20'],
                ['ما هو الأمر المستخدم لإضافة صف جديد للجدول؟', 'INSERT INTO', 'ADD ROW', 'CREATE DATA', 'NEW RECORD'],
                ['أي جملة تستخدم لتعديل بيانات مسجلة مسبقاً؟', 'UPDATE', 'CHANGE', 'MODIFY', 'SET'],
                ['ماذا يفعل الأمر DELETE FROM users (بدون WHERE)؟', 'يحذف جميع الصفوف داخل جدول المستخدمين (خطر جداً!)', 'يحذف الجدول نفسه', 'يحذف المستخدم الأول فقط', 'يعطي رسالة خطأ'],
                ['كيف ترتب النتائج من الأحدث إلى الأقدم؟', 'ORDER BY date DESC', 'ORDER BY date ASC', 'SORT date', 'ARRANGE date'],
                ['ما فائدة الكلمة المفتاحية DISTINCT؟', 'إعادة القيم الفريدة فقط ومنع تكرار الصفوف المتشابهة في النتائج', 'حذف البيانات', 'تغيير اسم العمود', 'جمع الأرقام'],
                ['كيف تبحث عن نص يبدأ بحرف "A"؟', "WHERE name LIKE 'A%'", 'WHERE name = A', 'WHERE name contains A', 'WHERE name STARTS WITH A'],
                ['ما وظيفة الدالة COUNT(*)؟', 'حساب عدد الصفوف الإجمالي التي تطابق الاستعلام', 'جمع القيم المالية', 'إيجاد أكبر قيمة', 'إيجاد أقل قيمة'],
                ['ما الفرق بين DELETE و TRUNCATE؟', 'DELETE يحذف صفوفاً محددة (مع سجل)، TRUNCATE يفرغ الجدول بالكامل (بدون سجل) وبسرعة أكبر', 'لا فرق بينهما', 'TRUNCATE للموبايل فقط', 'DELETE يحذف الأعمدة'],
            ]],

            ['SQL المتقدم والربط (JOINs)', '<h1>🔗 قوة العلاقات: الربط والتحليل</h1>
<p>القوة الحقيقية لـ SQL تكمن في قدرتها على دمج البيانات من جداول مختلفة في استعلام واحد لتوليد تقارير ذكية.</p>

<h3>1. أنواع الـ JOINs</h3>
<ul>
    <li><strong>Inner Join:</strong> يعيد البيانات الموجودة في الجدولين فقط (التقاطع).</li>
    <li><strong>Left Join:</strong> يعيد كل بيانات الجدول "الأيسر"، وما يطابقها فقط من "الأيمن".</li>
</ul>

<h3>2. العمليات التجميعية (Aggregation)</h3>
<p>استخدام <code>GROUP BY</code> مع دوال مثل <code>SUM</code> أو <code>AVG</code> لحساب إجمالي المبيعات للموظف أو متوسط أعمار الطلاب.</p>', [
                ['ماذا يفعل الـ INNER JOIN؟', 'يعيد فقط الصفوف التي لها قيم مطابقة في كلا الجدولين', 'يعيد كل شيء في الجدولين', 'يعيد الجدول الأيمن فقط', 'يجمع الجدولين فوق بعضهما'],
                ['إذا أردت قائمة بكل الطلاب حتى لو لم يسجلوا في أي كورس، ماذا تستخدم؟', 'LEFT JOIN (باعتبار الطلاب في الجدول الأيسر)', 'INNER JOIN', 'RIGHT JOIN', 'CROSS JOIN'],
                ['ما وظيفة GROUP BY في SQL؟', 'تجميع الصفوف التي لها نفس القيم في أعمدة محددة لإجراء عمليات حسابية عليها', 'ترتيب النتائج', 'حذف التكرار', 'تغيير شكل الجداول'],
                ['أي دالة تستخدم لحساب "متوسط" القيم في عمود معين؟', 'AVG()', 'SUM()', 'MEAN()', 'TOTAL()'],
                ['ما الفرق بين WHERE و HAVING؟', 'WHERE تصفي الصفوف "قبل" التجميع، و HAVING تصفي المجموعات "بعد" التجميع', 'لا فرق حقيقي', 'HAVING أسرع دائماً', 'WHERE تستخدم مع التجميع فقط'],
                ['ما هو الـ Subquery؟', 'استعلام (SELECT) موضوع داخل استعلام آخر', 'نوع من أنواع التشفير', 'نظام نسخ احتياطي', 'لغة برمجة مصغرة'],
                ['ماذا تفعل الدالة MAX()؟', 'إيجاد أكبر قيمة في عمود معين', 'إيجاد أقل قيمة', 'حساب العدد', 'دمج النصوص'],
                ['أي نوع من الـ JOIN يعيد "كل الاحتمالات الممكنة" بين كل صف في A وكل صف في B؟', 'CROSS JOIN', 'INNER JOIN', 'LEFT JOIN', 'FULL JOIN'],
                ['ما المقصود بـ Aliasing (باستخدام AS)؟', 'إعطاء اسم مؤقت للجدول أو العمود لتسهيل قراءة الاستعلام أو دمجه', 'تغيير اسم العمود للأبد', 'تشفير اسم الجدول', 'نوع من أنواع الحماية'],
                ['متى تستخدم UNION في SQL؟', 'لدمج نتائج استعلامين (أو أكثر) في مجموعة نتائج واحدة بشرط تساوي عدد الأعمدة', 'لربط الجداول بالعرض', 'لحذف البيانات', 'لإجراء عمليات طرح بين الجداول'],
            ]],

            ['تصميم وهيكلة البيانات', '<h1>🏗️ هندسة البيانات: من الفوضى إلى النظام</h1>
<p>قبل كتابة كود SQL واحد، يجب تصميم "مخطط" قاعدة البيانات. التصميم السيء يؤدي لتطبيقات بطيئة وصعبة التحديث.</p>

<h3>1. الـ ERD (Entity Relationship Diagram)</h3>
<p>رسم بياني يوضح "الكائنات" (Entities) مثل (المستخدم، المنتج، الطلب) وكيف يرتبط كل منهم بالآخر.</p>

<h3>2. التطبيع (Normalization)</h3>
<p>هي عملية تنظيم الجداول لتقليل تكرار البيانات (Redundancy) وضمان أن كل معلومة مخزنة في مكان واحد فقط.
<ul>
    <li><strong>1NF:</strong> التأكد من أن كل خلية تحتوي قيمة واحدة فقط.</li>
    <li><strong>2NF:</strong> التأكد من أن كل عمود يعتمد بالكامل على المفتاح الأساسي.</li>
</ul>', [
                ['ما الهدف الأساسي من عملية الـ Normalization؟', 'تقليل تكرار البيانات وضمان سلامتها وتسهيل تحديثها', 'جعل قاعدة البيانات أكبر مساحة', 'تلوين الجداول', 'إخفاء البيانات'],
                ['ماذا يمثل الـ ERD؟', 'مخطط يظهر الكائنات والعلاقات بينها قبل بناء قاعدة البيانات فعلياً', 'كود SQL معقد', 'شعار قاعدة البيانات', 'واجهة المستخدم'],
                ['في أي درجة تطبيع (NF) يجب التأكد من "عدم وجود قيم متعددة في خلية واحدة"؟', '1NF (أول درجة)', '3NF', 'BCNF', '10NF'],
                ['ما هي العلاقة من نوع Many-to-Many؟', 'علاقة حيث يمكن لعدة عناصر في A الارتباط بعدة عناصر في B (مثل الطلاب والكورسات)', 'علاقة مستحيلة برمجياً', 'علاقة غير شرعية', 'علاقة الربط المباشر'],
                ['كيف نحل مشكلة العلاقة Many-to-Many تقنياً؟', 'بإنشاء "جدول وسيط" (Pivot/Junction Table) يربط المفتاحين الأساسيين بكلا الجدولين', 'بوضع كل البيانات في جدول واحد', 'بحذف أحد الجداول', 'باستخدام NoSQL فقط'],
                ['ما هو التكامل المرجعي (Referential Integrity)؟', 'قاعدة تضمن أن العلاقات بين الجداول تظل متسقة دائماً (مثل عدم حذف مستخدم لديه طلبات نشطة)', 'تكامل الألوان', 'سرعة المعالجة', 'تشفير الجداول'],
                ['ماذا يعني Column Indexing؟', 'إنشاء "فهرس" للعمود لتسريع عمليات البحث بشكل هائل (مثل فهرس الكتاب)', 'ترقيم الأعمدة', 'تغيير أسماء الأعمدة', 'تقليل حجم البيانات'],
                ['ما هو الملح المُر (Indexing Drawback)؟', 'الفهارس تسرع القراءة لكنها تبطئ عمليات الإضافة والتعديل (Write) وتستهلك مساحة إضافية', 'لا يوجد عيوب', 'تجعل البيانات تختفي', 'تحتاج لكود معقد'],
                ['ما المقصود بـ Composite Key؟', 'مفتاح أساسي يتكون من عمودين أو أكثر معاً لضمان التفرد', 'مفتاح مصنوع من البلاستيك', 'مفتاح مشفر', 'مكون من حرف واحد'],
                ['لماذا يفضل استخدام الأرقام (ID) كمفاتيح أساسية بدلاً من الأسماء؟', 'لأن الأرقام أسرع في المعالجة ولا تتغير وفريدة دائماً بعكس الأسماء', 'لأن الكمبيوتر لا يفهم الحروف', 'لأنها تبدو احترافية', 'لا يهم، كلاهما واحد'],
            ]],

            ['PostgreSQL المتقدم والأداء', '<h1>🐘 احتراف PostgreSQL: وحش قواعد البيانات</h1>
<p>بوستجري (PostgreSQL) يعتبر أقوى قاعدة بيانات مفتوحة المصدر في العالم، بفضل ميزاتها المتقدمة التي تنافس الحلول التجارية الباهظة.</p>

<h3>1. الـ JSONB والبيانات المرنة</h3>
<p>تسمح PostgreSQL بتخزين بيانات JSON بصيغة ثنائية (Binary) سريعة جداً، مما يمنحك قوة الـ NoSQL داخل نظام SQL رصين.</p>

<h3>2. تحسين الاستعلامات (Performance Tuning)</h3>
<ul>
    <li><strong>EXPLAIN:</strong> أمر سحري يخبرك كيف سيقوم محرك قاعدة البيانات بتنفيذ استعلامك وأين توجد نقاط البطء.</li>
    <li><strong>Common Table Expressions (CTE):</strong> استخدام <code>WITH</code> لكتابة استعلامات معقدة بطريقة منظمة وقابلة للقراءة.</li>
</ul>', [
                ['ما الذي يميز PostgreSQL عن MySQL بشكل أساسي؟', 'دعم متقدم للـ JSON والعمليات المعقدة والالتزام الصارم بمعايير SQL مع قابلية توسع هائلة', 'أنها لـ "الماك" فقط', 'أنها تعمل بدون إنترنت', 'أنها لا تدعم الجداول'],
                ['ما الفرق بين JSON و JSONB في PostgreSQL؟', 'الـ JSONB يخزن البيانات بصيغة ثنائية وهو أسرع بكثير في المعالجة ويدعم الفهرسة', 'لا يوجد فرق', 'الـ JSONB للصور فقط', 'الـ JSON حجمه أصغر'],
                ['أي أمر يستخدم لمعرفة كيف سيتم تنفيذ الاستعلام واكتشاف البطء؟', 'EXPLAIN ANALYZE', 'HOW TO RUN', 'CHECK SPEED', 'DEBUG SQL'],
                ['ما هو الـ CTE (Common Table Expression)؟', 'طريقة لكتابة استعلامات مؤقتة ومنظمة باستخدام كلمة WITH لتسهيل القراءة', 'نوع من أنواع التشفير', 'نظام نسخ احتياطي', 'لغة برمجة'],
                ['ما وظيفة الـ Full Text Search في PostgreSQL؟', 'البحث المتقدم داخل النصوص الطويلة والمقالات بسرعة فائقة وبنتائج ذكية', 'البحث عن ملفات الصور', 'تلوين الكلمات', 'ترجمة النصوص'],
                ['ما هي الجداول "الافتراضية" (Views)؟', 'استعلام محفوظ يمكنك التعامل معه كأنه جدول حقيقي لتبسيط الوصول للبيانات المعقدة', 'جداول لا وجود لها', 'جداول للمسودات', 'جداول للصور'],
                ['ما المقصود بـ Stored Procedures؟', 'كود برمجى (مثل SQL أو PL/pgSQL) يُحفظ داخل قاعدة البيانات ويُنفذ على السيرفر مباشرة', 'طريقة لحفظ الكود في ملفات', 'أداة للرسم', 'قاعدة بيانات للصور'],
                ['ما هو الـ WAL (Write Ahead Logging) في PostgreSQL؟', 'نظام يضمن عدم فقدان البيانات حتى لو انقطعت الكهرباء فجأة عن السيرفر', 'نظام لتسجيل دخول المبرمجين', 'نوع من أنواع التشفير', 'بروتوكول شبكة'],
                ['ما هي الـ Window Functions (مثل ROW_NUMBER)؟', 'دوال تقوم بحسابات عبر مجموعة من الصفوف المرتبطة بالصف الحالي دون دمجها', 'دوال لفتح نوافذ بالمتصفح', 'دوال برمجية عادية', 'أداة للتصميم'],
                ['لماذا تعتبر PostgreSQL الخيار الأول لشركات الـ Fintech؟', 'بفضل موثوقيتها العالية جداً في التعامل مع البيانات المالية والعمليات الحساسة (Transactions)', 'لأن شعارها "فيل"', 'لأنها أسرع في تحميل الصور', 'لأن المبرمجين يحبونها فقط'],
            ]],
            ['NoSQL - MongoDB', 'قواعد البيانات الوثائقية.', [
                ['ما الوحدة الأساسية في MongoDB؟', 'Document (وثيقة)', 'Row', 'Record'],
                ['ما المجموعة في MongoDB؟', 'Collection: مجموعة وثائق', 'Table', 'Database'],
                ['ما الأمر للبحث في MongoDB؟', 'find()', 'search()', 'query()'],
                ['ما الـ Aggregation Pipeline؟', 'سلسلة عمليات لمعالجة البيانات', 'نوع استعلام', 'أداة نسخ احتياطي'],
                ['ما الـ Index في MongoDB؟', 'يُسرّع البحث في الوثائق', 'ضغط البيانات', 'تشفير الوثائق'],
            ]],
            ['Redis - قاعدة بيانات الكاش', 'التخزين المؤقت السريع.', [
                ['ما Redis؟', 'قاعدة بيانات Key-Value في الذاكرة', 'قاعدة SQL', 'قاعدة وثائق'],
                ['ما أشهر استخدامات Redis؟', 'Caching, Sessions, Pub/Sub', 'تخزين الصور', 'إدارة المستخدمين'],
                ['ما الـ TTL في Redis؟', 'Time To Live: مدة بقاء المفتاح', 'Time To Load', 'Total Time Limit'],
                ['ما الأمر لحفظ قيمة في Redis؟', 'SET key value', 'INSERT key value', 'ADD key value'],
                ['ما الأمر لقراءة قيمة من Redis؟', 'GET key', 'READ key', 'FETCH key'],
            ]],
            ['Database Performance', 'تحسين أداء قواعد البيانات.', [
                ['ما الـ Index في SQL؟', 'هيكل بيانات يُسرّع البحث', 'ضغط البيانات', 'تشفير الجدول'],
                ['ما Query Optimization؟', 'تحسين الاستعلام للأداء الأفضل', 'تبسيط الاستعلام', 'تصغير الجدول'],
                ['ما Connection Pooling؟', 'إعادة استخدام اتصالات قاعدة البيانات', 'تعدد قواعد البيانات', 'نسخ احتياطي'],
                ['ما Sharding في قواعد البيانات؟', 'توزيع البيانات على عدة خوادم', 'نسخ البيانات', 'ضغط البيانات'],
                ['ما Replication؟', 'نسخ البيانات على خوادم متعددة للاحتياط', 'تقسيم البيانات', 'تشفير البيانات'],
            ]],
            ['Data Backup والاسترداد', 'حماية البيانات من الضياع.', [
                ['ما Full Backup؟', 'نسخ احتياطي كامل لكل البيانات', 'نسخ للتغييرات فقط', 'نسخ دورية صغيرة'],
                ['ما Incremental Backup؟', 'نسخ التغييرات منذ آخر نسخة احتياطية', 'نسخ كامل', 'نسخ أسبوعية'],
                ['ما RTO؟', 'Recovery Time Objective: وقت الاسترداد المستهدف', 'Real Time Output', 'Restore Timeout Option'],
                ['ما RPO؟', 'Recovery Point Objective: أقصى بيانات مقبول فقدانها', 'Restore Point Option', 'Replication Point Only'],
                ['ما أمر النسخ الاحتياطي في PostgreSQL؟', 'pg_dump', 'pg_backup', 'db_export'],
            ]],
            ['NewSQL وقواعد البيانات الحديثة', 'مستقبل قواعد البيانات.', [
                ['ما NewSQL؟', 'قواعد بيانات تجمع ACID وقابلية التوسع', 'نسخة جديدة من SQL', 'قاعدة NoSQL'],
                ['ما CockroachDB؟', 'قاعدة بيانات SQL موزعة', 'قاعدة NoSQL', 'قاعدة بيانات رسومية'],
                ['ما PlanetScale؟', 'قاعدة بيانات MySQL serverless', 'قاعدة NoSQL', 'خدمة AWS'],
                ['ما Graph Database؟', 'قاعدة بيانات للعلاقات المعقدة (Neo4j)', 'قاعدة صور', 'قاعدة بيانات SQL'],
                ['ما Time-Series Database؟', 'قاعدة لبيانات مرتبطة بالزمن (مقاييس، تتبع)', 'قاعدة أرقام عامة', 'قاعدة تواريخ'],
            ]],
        ]);

        // =============================================================
        // TRACKS 11-20 (Condensed but complete)
        // =============================================================
        $allTracks = [
            // Track 11
            ['تطوير الألعاب بـ Unity وC#', 'بناء ألعاب 2D و3D احترافية باستخدام لغة C# ومحرك Unity.', [
                ['أساسيات Unity وC#', '<h1>🎮 مقدمة في محرك Unity ولغة C#</h1>
<p>محرك Unity هو الخيار الأول للمطورين المستقلين والشركات الكبرى لبناء ألعاب تعمل على كافة المنصات.</p>
<h3>1. المكونات الأساسية</h3>
<ul>
    <li><strong>GameObject:</strong> الحاوية الأساسية لكل شيء تراه في اللعبة.</li>
    <li><strong>Component:</strong> "وظيفة" تضاف للكائن (مثل المبرمج، الصوت، أو الجاذبية).</li>
</ul>
<h3>2. البرمجة بـ C#</h3>
<p>تعمل Unity بنظام الـ Scripting. الدالة <code>Start()</code> تنفذ مرة واحدة، بينما <code>Update()</code> تنفذ في كل إطار (Frame) للحركة والتفاعل.</p>', [
                    ['ما هي لغة البرمجة الأساسية في Unity؟', 'C#', 'C++', 'Python', 'Java'],
                    ['ما هو الـ GameObject؟', 'الكائن الأساسي الذي يحتوي على كافة المكونات في اللعبة', 'ملف صوتي', 'نوع من أنواع الصور', 'أداة للرسم'],
                    ['ما وظيفة الدالة Update()؟', 'تنفيذ الكود في كل إطار (Frame) طوال فترة تشغيل اللعبة', 'تشغيل الكود مرة واحدة عند البداية', 'حفظ اللعبة', 'إصلاح الأخطاء تلقائياً'],
                    ['أين يتم تعديل خصائص الـ GameObject بصرياً؟', 'لوحة الـ Inspector', 'لوحة الـ Console', 'لوحة الـ Project', 'لوحة الـ Hierarchy'],
                    ['ما هو الـ Component في Unity؟', 'وحدة وظيفية تضاف للكائن لمنحه ميزات معينة (مثل سكريبت أو فيزياء)', 'اسم اللعبة', 'نوع من أنواع الشخصيات', 'موقع إنترنت'],
                    ['ماذا تسمى لوحة العمل التي ترتب فيها الكائنات؟', 'The Scene View', 'The Game View', 'The Asset Store', 'The Warehouse'],
                    ['كيف تجعل متغيراً في الكود يظهر في الـ Inspector؟', 'عبر تعريفه كـ public أو استخدام [SerializeField]', 'عبر تشفيره', 'لا يمكن ذلك', 'عبر كتابته في ملف خاروي'],
                    ['ما هو الـ Prefab؟', 'قالب لكائن تم إعداده مسبقاً لإعادة استخدامه في اللعبة عدة مرات', 'خطأ في الكود', 'صورة خلفية', 'اسم محرك الألعاب'],
                    ['ما وظيفة Debug.Log()؟', 'طباعة رسائل في الـ Console لتصحيح وفهم سير الكود', 'إغلاق اللعبة', 'تسريع المعالج', 'تلوين الأزرار'],
                    ['ما الفرق بين Start و Awake؟', 'Awake تنفذ قبل Start وهي مفيدة لإعداد المتغيرات قبل بدء اللعب', 'لا فرق', 'Start أسرع', 'Awake للصور فقط'],
                ]],
                ['الفيزياء و Rigidbody', '<h1>⚖️ عالم الفيزياء في Unity</h1>
<p>نظام الفيزياء يمنح كائناتك وزناً وقدرة على التصادم.</p>
<ul>
    <li><strong>Rigidbody:</strong> يضيف الجاذبية والقوى للكائن.</li>
    <li><strong>Colliders:</strong> يحدد حدود الجسم لكي لا يمر عبر الجدران.</li>
</ul>', [
                    ['أي مكون يضيف "الجاذبية" للكائن؟', 'Rigidbody', 'Mesh Renderer', 'Audio Source', 'Light'],
                    ['ما وظيفة الـ Collider؟', 'تحديد "شكل" الكائن لأغراض الاصطدام الفيزيائي', 'تلوين الكائن', 'جعل الكائن يتحدث', 'حفظ بيانات اللاعب'],
                    ['ما المقصود بـ Is Trigger؟', 'خيار يجعل الكائن يمر عبر الأشياء لكنه يطلق حدثاً برمجياً عند الاصطدام', 'خيار لانفجار الكائن', 'خيار لتسريع الكائن', 'خيار لحذف الكائن'],
                    ['أي دالة تستخدم لاكتشاف الاصطدام الفيزيائي العادي؟', 'OnCollisionEnter', 'OnTriggerEnter', 'Update', 'FixedUpdate'],
                    ['لماذا نستخدم FixedUpdate للفيزياء؟', 'لأنها تعمل بمعدل زمني ثابت مما يضمن دقة الحسابات الفيزيائية', 'لأنها أحدث', 'لأنها أبطأ', 'لأنها للرسوم فقط'],
                    ['كيف تجعل كائناً "كتلة واحدة" لا يتأثر بالجاذبية؟', 'إلغاء تفعيل Use Gravity في Rigidbody', 'حذف الـ Rigidbody', 'تغيير لونه', 'تكبير حجمه'],
                    ['ما هو الـ Physic Material؟', 'ملف يحدد "الاحتكاك" و"الارتداد" لسطح الكائن', 'نوع من أنواع الكود', 'صورة للمادة', 'أداة للرسم'],
                    ['كيف تطبق قوة دفع مفاجئة على كائن؟', 'AddForce()', 'Move()', 'Rotate()', 'Fly()'],
                    ['ما هو الـ Raycasting؟', 'إرسال شعاع وهمي لاكتشاف الكائنات في مسار معين (مثل الليزر)', 'نوع صوت', 'طريقة رسم', 'تشفير بيانات'],
                    ['ما وظيفة دالة Rigidbody.velocity؟', 'التحكم المباشر في سرعة واتجاه الكائن', 'تغيير حجم الكائن', 'حساب الوقت', 'تلوين الظلال'],
                ]],
                ['الرسوم المتحركة والتحكم', '<h1>🏃 نظام التحريك (Animator)</h1>
<p>نظام Mecanim في Unity يتيح لك إدارة الحركات المعقدة (Idle, Walk, Jump) عبر مخطط حالات (State Machine).</p>', [
                    ['ما هو الـ Animator Controller؟', 'مخطط لإدارة الانتقال بين الحركات المختلفة للشخصية', 'أداة لتسجيل الصوت', 'محرك الرسم', 'اسم الشخصية'],
                    ['ما المقصود بـ Transition في التحريك؟', 'منطقة الانتقال من حركة إلى أخرى (مثل من الوقوف للمشي)', 'ترجمة النص', 'حذف الحركة', 'تغيير اللون'],
                    ['ما هو الـ Blend Tree؟', 'أداة لدمج عدة حركات بناءً على متغيرات (مثل دمج المشي والجري)', 'شجرة حقيقية', 'نوع Collider', 'كود حفظ'],
                    ['كيف تغير حركة الشخصية برمجياً؟', 'عبر تحديث Parameters في الـ Animator (مثل SetFloat)', 'عبر كتابة اسم الملف', 'لا يمكن ذلك', 'بإعادة تشغيل اللعبة'],
                    ['ما هو الـ IK (Inverse Kinematics)؟', 'نظام لتحريك الأطراف طبيعياً لتلمس أهدافاً معينة (مثل اليد على المقبض)', 'نوع كاميرا', 'تشفير فيديو', 'أداة رسم'],
                    ['ما وظيفة الـ Any State في الـ Animator؟', 'حالة تسمح بالانتقال لأي حركة من أي مكان (مثل حالة الموت)', 'حالة مجهولة', 'حالة للصور', 'بداية اللعبة'],
                    ['ماذا يفعل الـ Root Motion؟', 'يجعل حركة الشخصية الفيزيائية تتبع حركة الأنيميشن نفسه', 'يحرك الكاميرا', 'يحرك الأرض', 'يحذف الصوت'],
                    ['ما هو الـ Avatar في Unity؟', 'نظام لربط هيكل العظام الخاص بالموديل بنظام Unity لتحريكه', 'صورة الملف الشخصي', 'قاعدة بيانات', 'نوع سلاح'],
                    ['ما فائدة الـ Animation Window؟', 'إنشاء وتعديل الحركات (Keys) يدوياً داخل Unity', 'مشاهدة التلفاز', 'تحرير الفيديوهات', 'تثبيت البرامج'],
                    ['ما هو الـ Sprite Animator؟', 'نظام مخصص لتحريك الصور 2D إطاراً بإطار', 'برنامج للرسم', 'محرك فيزياء', 'أداة إضاءة'],
                ]],
            ]],
            // Track 12
            ['تطوير الذكاء الاصطناعي وتعلم الآلة', 'من الرياضيات الأساسية ومكتبة NumPy إلى بناء النماذج اللغوية العملاقة (LLMs).', [
                ['NumPy والحساب العلمي', '<h1>🔢 أساسيات NumPy</h1>
<p>الذكاء الاصطناعي هو "رياضيات مصفوفات". NumPy هي المكتبة الأهم في بايثون لمعالجة هذه المصفوفات بسرعة فائقة.</p>', [
                    ['لماذا نستخدم NumPy بدلاً من الـ Lists في بايثون؟', 'لأنها أسرع بكثير وتتعامل بكفاءة مع المصفوفات الضخمة', 'لأن لونها أفضل', 'لأنها مخصصة للإنترنت', 'لا فرق بينهما'],
                    ['ماذا يمثل الـ ndarray في NumPy؟', 'مصفوفة متعددة الأبعاد (ن صعبة الحساب يدوياً)', 'قاعدة بيانات', 'نوع من الصور', 'ملف نصي'],
                    ['كيف تنشئ مصفوفة من الأصفار؟', 'np.zeros()', 'np.null()', 'np.empty()', 'np.void()'],
                    ['ما هو الـ Broadcasting؟', 'قدرة NumPy على إجراء عمليات حساب مصفوفات ذات أحجام مختلفة', 'بث تلفزيوني', 'نشر الملفات', 'نوع تشفير'],
                    ['ما وظيفة np.reshape()؟', 'إعادة تشكيل مصفوفة لأبعاد جديدة دون تغيير بياناتها', 'حذف المصفوفة', 'تلوين المصفوفة', 'دمج الصور'],
                    ['ما هي الـ Matrix Multiplication؟', 'ضرب المصفوفات، وهي العملية الأساسية في تدريب نماذج الـ AI', 'جمع الأرقام', 'طرح الأرقام', 'تلوين الكود'],
                    ['كيف تجد القيمة العظمى في مصفوفة؟', 'np.max()', 'np.high()', 'np.top()', 'np.greatest()'],
                    ['ما هو الـ Axis في NumPy؟', 'يمثل البعد (مثلاً Axis 0 هو الصفوف)', 'محور الأرض', 'اسم مغير', 'نوع بيانات'],
                    ['ما وظيفة np.linspace()؟', 'إنشاء مصفوفة من الأرقام الموزعة بالتساوي بين قيمتين', 'رسم خط', 'حذف البيانات', 'تشفير البيانات'],
                    ['ما هي ميزة الـ Vectorization؟', 'كتابة كود يعمل على المصفوفة كاملة دفعة واحدة دون حلقات Loops', 'تلوين الرسومات', 'إبطاء الكود', 'زيادة المساحة'],
                ]],
                ['تعلم الآلة Scikit-learn', '<h1>🤖 الخوارزميات الكلاسيكية</h1>
<p>تعلم الآلة (ML) هو تدريب النموذج على البيانات لتوقع المستقبل أو تصنيف الحاضر.</p>', [
                    ['ما الفرق بين Supervised و Unsupervised Learning؟', 'Supervised يحتاج بيانات مُصنفة (إجابات)، Unsupervised يكتشف الأنماط وحده', 'لا فرق', 'الـ Supervised أقدم', 'الـ Unsupervised للصور فقط'],
                    ['ما هو الانحدار الخطي (Linear Regression)؟', 'خوارزمية لتوقع قيمة رقمية مستمرة (مثل سعر السكن)', 'خوارزمية لتصنيف الصور', 'أداة للرسم', 'نوع بريد'],
                    ['ما هي الـ Random Forest؟', 'مجموعة من أشجار القرار (Decision Trees) تعمل معاً لتحقيق دقة أعلى', 'غابة حقيقية', 'خوارزمية صوت', 'فيروس كمبيوتر'],
                    ['ما هو الـ Overfitting؟', 'عندما يفهم النموذج بيانات التدريب بدقة شديدة لدرجة الفشل في بيانات جديدة', 'زيادة سرعة التدريب', 'تحسين الدقة', 'حذف البيانات'],
                    ['ما وظيفة train_test_split؟', 'تقسيم البيانات لجزء للتدريب وجزء آخر منفصل للاختبار', 'دمج الملفات', 'حذف المكرر', 'تلوين الجداول'],
                    ['ما هو الـ KNN؟', 'تصنيف البيانات بناءً على أقرب جيرانها لها في المساحة', 'نوع من أنواع التشفير', 'برنامج دردشة', 'أداة تحميل'],
                    ['ما المقصود بـ Clustering؟', 'تجميع البيانات المتشابهة في مجموعات بدون علم مسبق بالفئات', 'حذف البيانات', 'تلوين البيانات', 'تشفير البيانات'],
                    ['ما هي الـ SVM؟', 'خوارزمية تبحث عن أفضل فاصل (Hyperplane) بين مجموعات البيانات', 'ماكينة حقيقية', 'أداة تحميل', 'بروتوكول شبكة'],
                    ['ما هي ميزة الـ Grid Search؟', 'البحث التلقائي عن أفضل إعدادات (Hyperparameters) للنموذج', 'البحث في جوجل', 'تلوين الصور', 'حذف الملفات'],
                    ['ما هو الـ Accuracy؟', 'مقياس يحدد نسبة التوقعات الصحيحة للنموذج', 'ساعة زمنية', 'اسم النموذج', 'حجم الذاكرة'],
                ]],
                ['التعلم العميق و TensorFlow', '<h1>🧠 الشبكات العصبية</h1>
<p>Deep Learning يحاكي دماغ الإنسان عبر طبقات من الخلايا العصبية الاصطناعية لحل المشكلات المعقدة للغاية.</p>', [
                    ['ما هي المكتبة التي طورتها جوجل للتعلم العميق؟', 'TensorFlow', 'C++', 'Excel', 'Django'],
                    ['ما هي الـ Keras؟', 'واجهة برمجية سهلة لبناء الشبكات العصبية تعمل فوق TensorFlow', 'لغة برمجة', 'قاعدة بيانات', 'نظام تشغيل'],
                    ['ماذا تمثل الطبقة (Layer) في الشبكة العصبية؟', 'مجموعة من الخلايا التي تعالج البيانات وتمررها للطبقة التالية', 'خلفية شاشة', 'ملف صوتي', 'نوع ذاكرة'],
                    ['ما هو الـ Epoch؟', 'مرور كامل للنموذج على كافة بيانات التدريب لمرة واحدة', 'ساعة حائط', 'نسخة البرنامج', 'حجم الصور'],
                    ['ما وظيفة الـ Activation Function؟', 'تحديد ما إذا كانت الخلية العصبية يجب أن تنشط وتضيف غير خطية للكود', 'تلوين الصور', 'حذف البيانات', 'تسريع الإنترنت'],
                    ['ما هي الـ Weights؟', 'قيم تحدد قوة وتأثير كل مدخل على النيجة النهائية للشبكة', 'وزن الحاسوب', 'عدد الصور', 'سرعة التحميل'],
                    ['ما هو الـ Optimizer؟', 'الخوارزمية التي تعدل الأوزان لتقليل نسبة الخطأ (مثل Adam)', 'برنامج تنظيف', 'محرك رسوم', 'نوع تخزين'],
                    ['ما هو الـ Loss Function؟', 'مقياس لمدى خطأ توقعات النموذج مقارنة بالواقع', 'دالة ضياع ملفات', 'أداة رسم', 'طريقة حفظ'],
                    ['ماذا يعني التعلم العميق (Deep Learning)؟', 'استخدام شبكات عصبية ذات طبقات مخفية كثيرة جداً', 'التعلم في أعماق البحار', 'استخدام كتب كثيرة', 'نوع من الرياضيات'],
                    ['ما هو الـ Batch Size؟', 'عدد العينات التي تعالجها الشبكة قبل تحديث الأوزان داخلياً', 'حجم الشاشة', 'عدد المبرمجين', 'مساحة القرص'],
                ]],
                ['النماذج اللغوية الكبيرة (LLMs)', '<h1>🗣️ ثورة الـ GPT</h1>
<p>النماذج اللغوية (مثل GPT) تفهم وتولد النصوص البشرية بدقة مذهلة عبر تقنية الـ Transformers.</p>', [
                    ['ماذا يرمز اختصار LLM؟', 'Large Language Model', 'Long Learning Machine', 'List Logic Model', 'Live Level Management'],
                    ['ما هو الـ Transformer؟', 'البنية التقنية الحديثة التي تعتمد عليها نماذج الذكاء الاصطناعي التوليدي', 'سيارة متحولة', 'نوع من الكابلات', 'برنامج تصميم'],
                    ['ما المقصود بـ Hallucination؟', 'قيام النموذج بتأليف معلومات خاطئة والادعاء بأنها حقيقة', 'توقف البرنامج', 'بطء الاستجابة', 'تكرار الكلام'],
                    ['ما هو الـ Prompt Engineering؟', 'فن كتابة التعليمات البرمجية للنموذج للحصول على أدق إجابة', 'برمجة محركات البحث', 'تصميم واجهات', 'إصلاح الكمبيوتر'],
                    ['ما هي تقنية الـ RAG؟', 'ربط نموذج الـ AI بمصادر بيانات خارجية لتقليل الأخطاء وزيادة المعرفة', 'نوع من الرسوم', 'لغة برمجة', 'محرك ألعاب'],
                    ['ماذا يفعل الـ Fine-tuning؟', 'تدريب نموذج ضخم جاهز على بيانات متخصصة صغرى ليصبح خبيراً فيها', 'مسح البيانات', 'تغيير الاسم', 'تسريع الإنترنت'],
                    ['ما هي شركة OpenAI؟', 'الشركة المطورة لـ ChatGPT', 'شركة هواتف', 'شركة تواصل اجتماعي', 'متجر إلكتروني'],
                    ['ما هو الـ Token في معالجة اللغات؟', 'أصغر وحدة نصية يفهمها النموذج (قد تكون كلمة أو جزء من كلمة)', 'عملة رقمية', 'برنامج حماية', 'ملف صورة'],
                    ['ما هو الـ Context Window؟', 'كمية المعلومات التي يستطيع النموذج تذكرها في المحادثة الواحدة', 'نافذة بالمتصفح', 'ذاكرة رام', 'حجم شاشة'],
                    ['أي نموذج يعتبر منافساً لـ GPT ومفتوح المصدر؟', 'Llama', 'Siri', 'Alexa', 'Google Search'],
                ]],
            ]],
            // Track 13 – Blockchain
            ['تطوير Blockchain وWeb3', 'العملات الرقمية، الـ DeFi والـ Smart Contracts.', [
                ['مقدمة في Blockchain', '<h1>🔗 ما هي الثورة التي أحدثها الـ Blockchain؟</h1>
<p>البلوكشين هو سجل رقمي موزع لا مركزي. تخيل كتاباً للحسابات يمتلك كل شخص في العالم نسخة منه، وعند كتابة أي عملية، تظهر في كل النسخ فوراً ولا يمكن مسحها.</p>
<h3>1. البنية التقنية</h3>
<ul>
    <li><strong>Block:</strong> الحاوية التي تحمل البيانات.</li>
    <li><strong>Hash:</strong> البصمة الرقمية التي تربط الكتلة بما قبلها (سر الأمان).</li>
</ul>', [
                    ['ما هو الـ Blockchain باختصار؟', 'سجل رقمي موزع ولامركزي وغير قابل للتعديل', 'قاعدة بيانات مركزية', 'برنامج دردشة', 'نظام تشغيل'],
                    ['ما هو الـ Hash في البلوكشين؟', 'بصمة رقمية فريدة تضمن عدم التلاعب بالبيانات', 'اسم المستخدم', 'نوع عملة', 'سرعة الإنترنت'],
                    ['ما المقصود بـ Decentralization (اللامركزية)؟', 'عدم وجود سلطة مركزية تتحكم في البيانات، بل هي موزعة على الجميع', 'تحكم بنك واحد', 'تعطل النظام', 'تشفير البيانات'],
                    ['ماذا يحدث إذا حاولت تغيير بيانات في كتلة قديمة؟', 'سينكسر تسلسل الـ Hash ويفشل النظام في قبول الكتلة', 'سيتم التعديل ببساطة', 'سيصبح النظام أسرع', 'لا شيء'],
                    ['ما هو الـ Node (العقدة)؟', 'جهاز حاسوب مشارك في شبكة البلوكشين ويحتفظ بنسخة من السجل', 'سلك إنترنت', 'نوع شاشة', 'برنامج رسم'],
                    ['ما هو الـ Genesis Block؟', 'أول كتلة تم إنشاؤها في السلسلة (الكتلة رقم 0)', 'آخر كتلة', 'كتلة محذوفة', 'كتلة مشفرة للبيع'],
                    ['ما المقصود بـ Immutable؟', 'غير قابل للتغيير أو المسح بمجرد التسجيل', 'بطيء جداً', 'سهل الاختراق', 'مجاني'],
                    ['ما هو الـ Consensus Mechanism؟', 'بروتوكول لاتفاق جميع العقد على صحة المعاملة', 'نوع عملة', 'برنامج محادثة', 'إضاءة الشاشة'],
                    ['ما الفرق بين البلوكشين العام والخاص؟', 'العام متاح للجميع (مثل بيتكوين)، الخاص لمؤسسة معينة', 'لا فرق', 'العام أسرع', 'الخاص مجاني'],
                    ['ما هي تقنية الـ Distributed Ledger؟', 'السجل الموزع الذي يضمن مطابقة البيانات عند كل المشاركين', 'ملف إكسل واحد', 'صورة رقمية', 'برنامج حماية'],
                ]],
                ['Bitcoin وأساسيات العملات', '<h1>🪙 بيتكوين: الذهب الرقمي</h1>
<p>بيتكوين هي أول عملة رقمية ناجحة، تعتمد على خوارزمية Proof of Work لتأمين الشبكة.</p>', [
                    ['من هو منشئ البيتكوين؟', 'Satoshi Nakamoto (هوية مجهولة)', 'Bill Gates', 'Elon Musk', 'Mark Zuckerberg'],
                    ['ما هي آلية الـ Proof of Work؟', 'إثبات العمل عبر حل مسائل رياضية معقدة لتأمين الشبكة', 'إثبات الهوية بالصور', 'دفع رسوم بالفيزا', 'التصويت بالهاتف'],
                    ['ما هو الـ Mining (التعدين)؟', 'عملية التحقق من المعاملات وإنتاج عملات جديدة عبر قوة المعالجة', 'شراء العملات من المتجر', 'تقليل سرعة الشبكة', 'حذف البيانات القديمة'],
                    ['ما هو الحد الأقصى لعدد عملات البيتكوين؟', '21 مليون عملة فقط', '100 مليون', 'مليار', 'غير محدود'],
                    ['ما هو الـ Halving في بيتكوين؟', 'حدث يقع كل 4 سنوات يقلل مكافأة التعدين للنصف للسيطرة على التضخم', 'إيقاف الشبكة', 'تغيير اسم العملة', 'زيادة سرعة الإنترنت'],
                    ['ما هي الـ Seed Phrase؟', 'مجموعة كلمات (12-24) تستخدم لاستعادة المحفظة في حال ضياعها', 'كلمة سر المتصفح', 'عنوان المحفظة', 'كود الخصم'],
                    ['ما هو الـ Public Key؟', 'عنوانك الذي تعطه للناس ليرسلوا لك الأموال', 'كلمة سرك الخاصة', 'رقم هاتفك', 'عنوان منزلك'],
                    ['ماذا يحدث إذا فقدت الـ Private Key الخاص بك؟', 'ستفقد الوصول لأموالك للأبد ولا يمكن استردادها', 'يمكنك الاتصال بالدعم', 'ستصلك رسالة بريد', 'سيتم تغيير القفل تلقائياً'],
                    ['ما هي الـ Cold Wallet؟', 'محفظة غير متصلة بالإنترنت (أكثر أماناً)', 'محفظة هاتف', 'بورصة عملات', 'حساب بنكي'],
                    ['ما المقصود بـ Satoshi؟', 'أصغر وحدة في البيتكوين (واحد من مئة مليون)', 'اسم مدير البنك', 'نوع معالج', 'اسم عملة منافسة'],
                ]],
                ['Ethereum وSmart Contracts', '<h1>📜 العقود الذكية وإيثيريوم</h1>
<p>إيثيريوم ليست مجرد عملة، بل هي "كمبيوتر عالمي" يتيح تشغيل برامج لا مركزية (Smart Contracts).</p>', [
                    ['ما هي الميزة الأساسية لـ Ethereum؟', 'دعم العقود الذكية والتطبيقات اللامركزية (DApps)', 'أنها مجانية تماماً', 'أنها أقدم من بيتكوين', 'أنها تستخدم في الصور فقط'],
                    ['ما هو العقد الذكي (Smart Contract)؟', 'كود برمجي ينفذ شروط الاتفاق تلقائياً بدون وسيط عند تحقق الشروط', 'ورقة قانونية', 'رسالة بريد', 'اتفاق شفهي'],
                    ['ما هي لغة البرمجة الأساسية في Ethereum؟', 'Solidity', 'Python', 'C++', 'Swift'],
                    ['ما هو الـ Gas Fee؟', 'تكلفة طاقة المعالجة لإتمام معاملة على شبكة إيثيريوم', 'بنزين السيارة', 'إيجار السيرفر', 'مجاني دائماً'],
                    ['ماذا يمثل الـ EVM (Ethereum Virtual Machine)؟', 'البيئة التي يتم فيها تنفيذ الكود الخاص بالعقود الذكية', 'محرك ألعاب', 'برنامج دردشة', 'نظام تشغيل ويندوز'],
                    ['ما هو معيار ERC-20؟', 'المعيار الموحد لإنشاء العملات (Tokens) على شبكة إيثيريوم', 'معيار للصور', 'معيار للصوت', 'معيار للأمان'],
                    ['من هو المؤسس الرئيسي لـ Ethereum؟', 'Vitalik Buterin', 'Steve Jobs', 'Jeff Bezos', 'Sundar Pichai'],
                    ['ما هو الـ Mainnet؟', 'الشبكة الأساسية الحقيقية التي تتم عليها المعاملات الفعلية', 'شبكة تجريبية', 'شبكة واي فاي', 'برنامج تحميل'],
                    ['ما الفائدة من الـ Testnet؟', 'اختبار العقود البرمجية بـ "عملات وهمية" قبل نشرها على الشبكة الحقيقية', 'شراء العملات', 'تعدين البيتكوين', 'لا فائدة منها'],
                    ['ما هو الـ Gwei؟', 'وحدة صغيرة جداً من الإيثر تستخدم لحساب سعر الـ Gas', 'نوع عملة جديدة', 'اسم محفظة', 'سرعة المعالج'],
                ]],
                ['عالم الـ DeFi وNFTs', '<h1>🏦 المالية اللامركزية والرموز غير القابلة للاستبدال</h1>
<p>DeFi تعيد اختراع البنوك بدون موظفين، وNFTs تعيد اختراع الفن والملكية الرقمية.</p>', [
                    ['ماذا يعني اختصار DeFi؟', 'المالية اللامركزية (Decentralized Finance)', 'التطبيقات الرقمية', 'التشفير المالي', 'تعريف البيانات'],
                    ['ما هو الـ NFT؟', 'رمز غير قابل للاستبدال يمثل ملكية فريدة لشيء رقمي', 'عملة مثل البيتكوين', 'برنامج رسم', 'رابط موقع'],
                    ['ما هي الـ DEX (Decentralized Exchange)؟', 'منصة تبادل عملات لا مركزية تدار بالعقود الذكية', 'بنك مركزي', 'محل صرافة', 'متجر إلكتروني'],
                    ['ما هو الـ Yield Farming؟', 'استثمار العملات في بروتوكولات DeFi للحصول على عوائد', 'زراعة حقيقية', 'شراء عملات رخيصة', 'تعدين البيتكوين'],
                    ['ما هو الـ Stablecoin؟', 'عملة رقمية قيمتها مرتبطة بأصل ثابت مثل الدولار (مثل USDT)', 'عملة سريعة التقلب', 'عملة قديمة', 'عملة ورقية'],
                    ['ما المقصود بـ Liquidity Pool؟', 'مجمع من العملات يوفره المستخدمون لتسهيل عمليات التداول في الـ DEX', 'حمام سباحة', 'قاعدة بيانات', 'خادم إنترنت'],
                    ['ما هو الـ DAO؟', 'منظمة لا مركزية تدار بالكود وتصويت الأعضاء (Decentralized Autonomous Organization)', 'شركة حكومية', 'جمعية خيرية', 'تطبيق دردشة'],
                    ['ما هي منصة OpenSea؟', 'أكبر سوق لبيع وشراء الـ NFTs', 'محرك بحث', 'موقع أخبار', 'منصة تداول أسهم'],
                    ['ما هو الـ Crypto Wallet؟', 'تطبيق أو جهاز يخزن مفاتيحك الخاصة للوصول لعملاتك', 'محفظة جلدية', 'حساب بنكي', 'خزنة حديدية'],
                    ['ما هو الـ Minting في عالم الـ NFT؟', 'عملية تحويل العمل الفني إلى رمز على البلوكشين', 'مسح الصورة', 'طباعة الورق', 'تغيير الألوان'],
                ]],
            ]],
            // Track 14 – Digital Marketing
            ['التسويق الرقمي وصناعة المحتوى', 'من الصفر إلى مؤثر في عالم التسويق الرقمي.', [
                ['أساسيات التسويق الرقمي', '<h1>📈 قوة التسويق الرقمي</h1>
<p>التسويق لم يعد مجرد إعلانات، بل هو فن فهم الجمهور وتقديم قيمة حقيقية في الوقت المناسب.</p>', [
                    ['ما هو الـ KPI في التسويق؟', 'مؤشر قياس الأداء الرئيسي (Key Performance Indicator)', 'نوع من أنواع الكاميرات', 'شركة تسويق', 'اسم نموذج إعلان'],
                    ['ماذا يعني اختصار SEO؟', 'تحسين محركات البحث (Search Engine Optimization)', 'نظام الملفات', 'سرعة الموقع', 'اسم منصة'],
                    ['ما هو الـ ROI؟', 'العائد على الاستثمار (Return On Investment)', 'عدد المتابعين', 'سرعة التحميل', 'تلوين الصور'],
                    ['ما المقصود بـ Funnel (قمع التسويق)؟', 'رحلة العميل من التعرف على المنتج حتى عملية الشراء', 'أداة للرسم', 'تشفير بيانات', 'سرعة الموقع'],
                    ['ما هو الـ CRM؟', 'نظام إدارة علاقات العملاء', 'نظام تشغيل', 'نوع إعلان', 'محرك بحث'],
                    ['ما الفرق بين التسويق الداخلي والخارجي (Inbound vs Outbound)؟', 'Inbound يجذب العميل بالمحتوى، Outbound يصل للعميل بالإعلانات المزعجة', 'لا فرق', 'الخارجي أحدث', 'الداخلي للصور فقط'],
                    ['ما هو الـ Buyer Persona؟', 'شخصية خيالية تمثل عميلك المثالي بناءً على بيانات حقيقية', 'شخص حقيقي', 'اسم برنامج', 'طريقة دفع'],
                    ['ما المقصود بـ B2B؟', 'التسويق من شركة إلى شركة أخرى (Business to Business)', 'من شركة لعميل فرد', 'من فرد لفرد', 'بيع الصور'],
                    ['ما هو الـ Landing Page؟', 'صفحة هبوط مخصصة لإقناع الزائر باتخاذ فعل محدد (مثل الشراء)', 'الصفحة الرئيسية للموقع', 'صفحة البحث', 'صفحة الاتصال'],
                    ['ما المقصود بـ CTA؟', 'طلب اتخاذ إجراء (مثل: اشترِ الآن أو اشترك)', 'نوع من الصور', 'اختصار لشركة', 'سرعة السيرفر'],
                ]],
                ['SEO - تحسين محركات البحث', '<h1>🔍 كيف تتصدر نتائج بحث Google؟</h1>
<p>تحسين محركات البحث هو عملية جعل موقعك يظهر في النتائج الأولى مجاناً.</p>', [
                    ['ما هو الـ On-Page SEO؟', 'تحسين العناصر داخل موقعك (الكلمات، العناوين، السرعة)', 'بناء الروابط مع مواقع أخرى', 'الإعلانات المدفوعة', 'تغيير الاستضافة'],
                    ['ما هو الـ Backlink؟', 'رابط من موقع خارجي يشير إلى موقعك (يزيد من ثقة جوجل فيك)', 'رابط داخلي في موقعك', 'كود برمجي', 'صورة للموقع'],
                    ['ما هي الـ Keywords؟', 'الكلمات التي يبحث عنها الناس في محرك البحث', 'كلمات السر', 'أسماء المبرمجين', 'أنواع الخطوط'],
                    ['ما وظيفة ملف robots.txt؟', 'توجيه عناكب محركات البحث حول الصفحات التي يجب أو لا يجب أرشفتها', 'تلوين الموقع', 'حماية الصور', 'تسريع الفيديو'],
                    ['ما المقصود بـ Domain Authority؟', 'مقياس لقوة وموثوقية نطاق موقعك لدى محركات البحث', 'سعر الدومين', 'اسم المالك', 'سرعة الاستضافة'],
                    ['لماذا تعتبر سرعة الموقع مهمة للـ SEO؟', 'لأن جوجل يفضل المواقع السريعة لتحسين تجربة المستخدم', 'لأنها تزيد الألوان', 'لا أهمية لها', 'لزيادة عدد الكلمات'],
                    ['ما هو الـ Alt Text للصور؟', 'نص وصفي للصورة يساعد محركات البحث والمكفوفين على فهمها', 'اسم المصور', 'لون الصورة', 'حجم الصورة'],
                    ['ما هي الـ Search Intent؟', 'الهدف الحقيقي للمستخدم من وراء عملية البحث (شراء، معلومة، إلخ)', 'سرعة الإنترنت', 'جودة الشاشة', 'نوع المتصفح'],
                    ['ما هو الـ Sitemap؟', 'ملف XML يحتوي على قائمة بكل صفحات الموقع ليسهل على جوجل فهم هيكلته', 'خريطة جوجل ماب', 'صورة خلفية', 'واجهة الموقع'],
                    ['ماذا يعني الـ "بلاك هات" SEO؟', 'استخدام تقنيات غير قانونية أو مخادعة للتلاعب بنتائج البحث (وقد تؤدي للحظر)', 'تصميم موقع أسود', 'استخدام صور مظلمة', 'سرعة عالية جداً'],
                ]],
                ['Google Ads والإعلانات المدفوعة', '<h1>💰 الإعلانات الممولة</h1>
<p>جوجل أدز تتيح لك الظهور أمام عميل يبحث "الآن" عن خدمتك.</p>', [
                    ['ماذا يعني اختصار PPC؟', 'الدفع مقابل كل نقرة (Pay Per Click)', 'شركة إنتاج برامج', 'قوة الصور الرقمية', 'دفع شهري ثابت'],
                    ['ما هو الـ Quality Score في Google Ads؟', 'مقياس جودة إعلانك ومدى صلته بالكلمة البحثية وصفحة الهبوط', 'سعر الإعلان', 'عدد الموظفين في جوجل', 'عمر الحساب'],
                    ['ما المقصود بـ Impression؟', 'عدد مرات ظهور إعلانك على الشاشة بغض النظر عن النقر', 'عدد المبيعات', 'عدد التحميلات', 'طول الإعلان'],
                    ['ما هو الـ CTR؟', 'نسبة النقر للظهور (Click Through Rate)', 'سرعة الإنترنت', 'عدد الصور', 'تكلفة الإعلان الكلية'],
                    ['ما هو الـ Remarketing (إعادة الاستهداف)؟', 'إظهار إعلاناتك للأشخاص الذين زاروا موقعك سابقاً ولم يشتروا', 'البحث عن عملاء جدد', 'حذف الإعلانات القديمة', 'تغيير شكل الموقع'],
                    ['ما هو الـ Bid في المزاد؟', 'أقصى سعر أنت مستعد لدفعه مقابل النقرة الواحدة', 'اسم الحملة', 'وقت الإعلان', 'نوع الخط'],
                    ['أين تظهر إعلانات Google Search؟', 'في مقدمة ومؤخرة صفحة نتائج بحث جوجل', 'في فيسبوك فقط', 'في البريد الورقي', 'لا تظهر'],
                    ['ما أهمية الـ Negative Keywords؟', 'منع ظهور إعلانك عند البحث عن كلمات غير متعلقة بخدمتك', 'كلمات تزيد السعر', 'كلمات محظورة سياسياً', 'لا فائدة منها'],
                    ['ما هو الـ Conversion في الإعلانات؟', 'تحقيق الهدف المطلوب (مثل عملية شراء أو تعبئة نموذج بيانات)', 'تغيير العملة', 'تحويل ملف فيديو', 'تغيير لون الإعلان'],
                    ['ما المقصود بـ Ad Extension؟', 'معلومات إضافية تضاف للإعلان (مثل رقم الهاتف أو روابط إضافية)', 'تكبير حجم الإعلان', 'ترجمة الإعلان', 'إيقاف الإعلان'],
                ]],
                ['التسويق عبر البريد الإلكتروني', '<h1>📧 قوة الـ Email Marketing</h1>
<p>البريد الإلكتروني هو القناة الوحيدة التي تمتلكها بالكامل، ولا تعتمد على خوارزميات المنصات.</p>', [
                    ['ما هو الـ Open Rate؟', 'نسبة الأشخاص الذين فتحوا الرسالة من إجمالي المستلمين', 'سرعة فتح الإيميل', 'عدد الرسائل المرسلة', 'اسم البرنامج'],
                    ['ماذا يعني الـ Spam؟', 'رسائل مزعجة وغير مرغوب فيها ترسل بشكل جماعي', 'رسائل مهمة جداً', 'نوع من أنواع الملفات', 'اسم شركة بريد'],
                    ['ما هو الـ Automation (الأتمتة) في البريد؟', 'إرسال رسائل تلقائية بناءً على فعل المستخدم (مثل رسالة ترحيب)', 'كتابة الرسائل يدوياً', 'حذف الإيميلات', 'تغيير العنوان'],
                    ['ما أهمية الـ Subject Line؟', 'هو العامل الأول الذي يحدد ما إذا كان المستخدم سيفتح الرسالة أم لا', 'لون الرسالة', 'اسم المبرمج', 'نوع الخط'],
                    ['ما هو الـ Newsletter؟', 'نشرة بريدية دورية تحتوي على معلومات وقيمة للجمهور', 'رسالة إعلانية واحدة', 'كلمة سر', 'رابط تحميل'],
                    ['ما المقصود بـ Segmentation؟', 'تقسيم قائمة البريد لمجموعات بناءً على الاهتمامات أو السلوك', 'حذف القائمة', 'تلوين الأسماء', 'زيادة عدد المشتركين'],
                    ['ما هي منصة Mailchimp؟', 'واحدة من أشهر المنصات لإدارة حملات البريد الإلكتروني', 'موقع للألعاب', 'متصفح ويب', 'نظام تشغيل'],
                    ['ما هو الـ Bounce Rate في البريد؟', 'نسبة الرسائل التي لم تصل لصندوق الوارد لأسباب تقنية أو خطأ في العنوان', 'سرعة الرد', 'عدد الصور', 'تكلفة الرسالة'],
                    ['ما وظيفة الـ Unsubscribe Link؟', 'رابط إلزامي قانونياً يسمح للمستخدم بالتوقف عن استقبال رسائلك', 'رابط للدفع', 'رابط للصور', 'رابط للشكاوى'],
                    ['ما هو الـ Opt-in؟', 'إجراء يقوم به المستخدم للموافقة على الانضمام لقائمتك البريدية', 'حظر المستخدم', 'إرسال رسالة عشوائية', 'تسجيل الدخول'],
                ]],
            ]],
            // Track 15 – WordPress & Web Development (No-Code)
            ['تطوير المواقع بـ WordPress', 'إنشاء وإدارة مواقع احترافية بدون خبرة برمجية.', [
                ['WordPress أساسيات', '<h1>🌐 ما هو ووردبريس؟</h1>
<p>ووردبريس هو نظام إدارة المحتوى (CMS) الأكثر شهرة في العالم، حيث يشغل أكثر من 40% من مواقع الإنترنت.</p>', [
                    ['ما هو الـ CMS؟', 'نظام إدارة المحتوى (Content Management System)', 'نظام تشغيل للحاسوب', 'قاعدة بيانات للصور', 'لغة برمجة جديدة'],
                    ['ما الفرق الجوهري بين WordPress.org و WordPress.com؟', '.org يمنحك كامل التحكم والاستضافة الذاتية، .com خدمة استضافة محدودة', 'لا فرق بينهما', '.com أسرع دائماً', '.org للصور فقط'],
                    ['ما هو الـ Dashboard في ووردبريس؟', 'لوحة التحكم الرئيسية لإدارة الموقع والمحتوى', 'واجهة الموقع للزوار', 'محرك البحث', 'اسم قاعدة البيانات'],
                    ['ما المقصود بـ Open Source (مفتوح المصدر) في ووردبريس؟', 'أن الكود المصدري متاح للجميع للتعديل والتطوير مجاناً', 'أنه يعمل فقط في النهار', 'أنه لا يحتاج كلمة سر', 'أنه مخترق'],
                    ['ما هو الـ Post (المقال) في ووردبريس؟', 'محتوى متجدد يتم ترتيبه زمنياً (مثل أخبار المدونة)', 'صفحة ثابتة مثل "اتصل بنا"', 'اسم القالب', 'نوع من أنواع الصور'],
                    ['ما هي الـ Page (الصفحة) في ووردبريس؟', 'محتوى ثابت لا يعتمد على الزمن (مثل صفحة "من نحن")', 'مقال إخباري', 'تعليق من مستخدم', 'ملف فيديو'],
                    ['كيف تدخل للوحة تحكم ووردبريس عادةً؟', 'عبر إضافة /wp-admin لرابط الموقع', 'عبر إرسال بريد إلكتروني', 'عبر ضغط زر مفتاح الويندوز', 'عبر كتابة كلمة "هكر"'],
                    ['ما هي الـ Plugins (الإضافات)؟', 'برامج صغيرة تضاف للموقع لزيادة وظائفه (مثل متجر أو منتدى)', 'نوع من أنواع الخطوط', 'صور خلفية', 'أسماء المستخدمين'],
                    ['ما هي الـ Themes (القوالب)؟', 'ملفات تحدد المظهر الخارجي وتصميم الموقع', 'قواعد البيانات', 'برامج حماية', 'أنواع السيرفرات'],
                    ['ما هي الـ Widgets؟', 'كتل برمجية صغيرة تضاف غالباً في الجوانب أو التذييل (Sidebars)', 'أزرار ضخمة', 'فيروسات برمجية', 'رسائل ترحيب'],
                ]],
                ['Themes والتصميم', '<h1>🎨 تصميم المواقع في ووردبريس</h1>
<p>القالب هو روح الموقع، واختيار القالب الصحيح يوفر عليك شهوراً من العمل البرمجي.</p>', [
                    ['ما هو الـ Theme؟', 'الملف الذي يتحكم في شكل وتنسيق وألوان الموقع', 'إضافة لإرسال البريد', 'محرك بحث داخلي', 'نوع من ملفات الوسائط'],
                    ['ما هو الـ Child Theme ولماذا نستخدمه؟', 'قالب تابع يستخدم للحفاظ على تعديلاتك عند تحديث القالب الأصلي', 'قالب مخصص للأطفال', 'قالب صغير الحجم', 'قالب للتجربة فقط'],
                    ['ما هو الـ Customizer؟', 'أداة تتيح لك معاينة وتغيير إعدادات القالب بشكل مباشر وبصري', 'برنامج للفوتوشوب', 'محرك لقواعد البيانات', 'أداة لحذف الموقع'],
                    ['ماذا يعني Responsiveness في القوالب؟', 'أن التصميم يتغير ويتناسب تلقائياً مع أحجام الشاشات المختلفة (جوال، تابلت..)', 'أن الموقع سريع الرد على الرسائل', 'أن الموقع يتحدث عدة لغات', 'أن الألوان زاهية'],
                    ['ما هو أشهر Page Builder (باني صفحات) في ووردبريس؟', 'Elementor', 'Photoshop', 'Excel', 'VLC'],
                    ['ما وظيفة الـ Drag and Drop في تصميم الصفحات؟', 'بناء واجهات الموقع بسحب العناصر وإفلاتها بدون كتابة كود', 'سحب الصور من جوجل', 'حذف الملفات نهائياً', 'تسريع الإنترنت'],
                    ['ما هي الـ Google Fonts في ووردبريس؟', 'مكتبة خطوط مجانية يمكن دمجها لتغيير شكل نصوص الموقع', 'محرك بحث للخطوط', 'بريد إلكتروني', 'خريطة للموقع'],
                    ['ماذا تفعل ميزة Demo Import في القوالب الاحترافية؟', 'تحميل بيانات وموقع كامل جاهز بضغطة زر لتبدأ التعديل عليه', 'حذف كل بيانات الموقع', 'زيادة سرعة السيرفر', 'تشفير الكود'],
                    ['ما هو الـ Typography؟', 'فن تنسيق الخطوط وأحجامها ومسافاتها في الموقع', 'نوع من أنواع الجرافيك', 'تصوير فوتوغرافي', 'برمجة خلفية'],
                    ['ما أهمية اختيار قالب خفيف (Lightweight)؟', 'لزيادة سرعة تحميل الموقع وتحسين أداء الـ SEO', 'لأن سعره أرخص', 'لأنه يحتوي على صور أقل', 'ليعمل على الجوالات القديمة فقط'],
                ]],
                ['Plugins والإضافات', '<h1>🔌 توسيع قدرات موقعك</h1>
<p>الإضافات هي ما يجعل ووردبريس قوياً جداً؛ يمكنك تحويل مدونة بسيطة إلى متجر عالمي أو أكاديمية تعليمية.</p>', [
                    ['ما هي الوظيفة الأساسية للـ Plugin؟', 'إضافة ميزات ووظائف جديدة لم تكن موجودة في ووردبريس الأساسي', 'تغيير لغة الويندوز', 'تنظيف شاشة الكمبيوتر', 'تحسين جودة الكاميرا'],
                    ['ما هو أشهر Plugin لتحويل ووردبريس لمتجر إلكتروني؟', 'WooCommerce', 'Contact Form 7', 'Yoast SEO', 'Jetpack'],
                    ['ما هي إضافة Yoast SEO؟', 'إضافة تساعدك في تحسين موقعك للظهور في محركات البحث', 'إضافة لتشغيل الألعاب', 'إضافة لحماية الموقع من الاختراق', 'إضافة لإرسال النشرات البريدية'],
                    ['ما هي إضافة Wordfence؟', 'إضافة متخصصة في حماية الموقع من الهجمات والاختراقات', 'إضافة لتغيير الألوان', 'إضافة لتعديل الصور', 'إضافة لزيادة المتابعين'],
                    ['لماذا يجب الحذر من تنصيب عدد كبير جداً من الإضافات؟', 'لأنها قد تؤدي لبطء الموقع أو حدوث تعارضات تقنية', 'لأن جوجل سيحظر الموقع', 'لأن مساحة الهاردسك ستنتهي', 'لأنها تزيد من أرباح الموقع'],
                    ['ما هو الـ Shortcode؟', 'كود صغير بين قوسين [ ] يستخدم لعرض وظيفة الإضافة داخل المقالات', 'كلمة سر قصيرة', 'كود لتشفير البيانات', 'رابط مختصر'],
                    ['ما وظيفة إضافات الـ Caching (مثل WP Rocket)؟', 'تسريع الموقع عبر تخزين نسخ ثابتة من الصفحات وتقليل الضغط على السيرفر', 'إرسال بريد سريع', 'تغيير شكل الأزرار', 'حذف التعليقات المزعجة'],
                    ['ما هي إضافة Contact Form 7؟', 'إضافة لإنشاء نماذج اتصال ليتمكن الزوار من مراسلتك', 'إضافة للدردشة الحية', 'إضافة لنشر الفيديو', 'إضافة لإدارة الملفات'],
                    ['ما معنى Plugin Conflict؟', 'تعارض بين إضافتين أو مع القالب يؤدي لتعطل وظيفة في الموقع', 'اتفاق بين المبرمجين', 'سرعة عالية في الموقع', 'تحديث تلقائي'],
                    ['كيف يتم تحديث الإضافات في ووردبريس؟', 'بضغطة زر واحدة من لوحة التحكم (Plugins > Update)', 'عبر الاتصال بالشركة', 'بإعادة تشغيل الكمبيوتر', 'لا يمكن تحديثها'],
                ]],
            ]],
        ];

        foreach ($allTracks as $i => $trackData) {
            echo " Creating Track " . ($i + 11) . ": " . $trackData[0] . "...\n";
            $buildTrack($trackData[0], $trackData[1], $trackData[2]);
        }

        // =============================================================
        // TRACKS 16-20 (Quick build with full data)
        // =============================================================
        $quickTracks = [
            // Track 16 – Laravel
            ['تطوير الويب بـ Laravel', 'إطار PHP الأكثر شعبية لبناء تطبيقات ويب قوية.', [
                ['مقدمة Laravel', '<h1>🏗️ لماذا لارافل؟</h1>
<p>لارافل هو إطار عمل PHP للمحترفين الذين يعشقون الكود الجميل والأنظمة القوية.</p>', [
                    ['من هو مؤسس إطار عمل Laravel؟', 'Taylor Otwell', 'Rasmus Lerdorf', 'Mark Zuckerberg', 'Bill Gates'],
                    ['ما هو نمط التصميم الذي يتبعه Laravel؟', 'MVC (Model-View-Controller)', 'Singleton', 'Factory', 'Observer'],
                    ['ما هي أداة سطر الأوامر الخاصة بـ Laravel؟', 'Artisan', 'Composer', 'NPM', 'Git'],
                    ['ما هو مدير الحزم الأساسي في PHP وLaravel؟', 'Composer', 'NPM', 'Yarn', 'Pip'],
                    ['أين يتم تخزين إعدادات البيئة (مثل بيانات الدايتابيز)؟', 'في ملف .env', 'في ملف config.php', 'في قاعدة البيانات نفسها', 'في ملف index.php'],
                    ['ما وظيفة المجلد /routes؟', 'يحتوي على كافة مسارات (عناوين) الموقع', 'يحتوي على السكربتات البرمجية', 'تخزين الصور', 'تخزين ملفات الـ CSS'],
                    ['ما هو المحرك الذي يستخدمه لارافل لكتابة القوالب؟', 'Blade', 'Twig', 'Smarty', 'Mustache'],
                    ['كيف نقوم بتثبيت مشروع لارافل جديد عبر الكومبوزر؟', 'composer create-project laravel/laravel project-name', 'npm install laravel', 'laravel start project', 'git clone laravel'],
                    ['ما هو الـ Middleware في لارافل؟', 'فلتر لفحص الطلبات (مثل فحص هل المستخدم مسجل دخول أم لا)', 'نوع من أنواع القواعد', 'محرك قوالب', 'أداة لضغط الصور'],
                    ['ما وظيفة المجلد /public؟', 'نقطة الدخول للموقع ويحتوي على ملف index.php والملفات العامة (CSS, JS)', 'تخزين ملفات البرمجة الحساسة', 'تخزين الصور المحذوفة', 'لا وظيفة له'],
                ]],
                ['Routing في Laravel', '<h1>🛤️ توجيه المسارات</h1>
<p>الروتينج هو خريطة موقعك، هو ما يربط الرابط بالوظيفة البرمجية.</p>', [
                    ['كيف نكتب Route بسيط يعرض كلمة "Hello"؟', "Route::get('/', function() { return 'Hello'; });", "Route::post('/', 'Hello');", "URL::add('/', 'Hello');", "Link::to('/', 'Hello');"],
                    ['ما الفرق بين Route::get و Route::post؟', 'get لجلب البيانات، post لإرسال البيانات (مثل الفورم)', 'لا فرق بينهما', 'get أسرع', 'post للصور فقط'],
                    ['كيف نمرر متغير (ID) في الرابط؟', "Route::get('/user/{id}', ...)", "Route::get('/user/id', ...)", "Route::get('/user?id=', ...)", "Route::get('/id/user', ...)"],
                    ['ما المقصود بـ Named Routes؟', 'إعطاء اسم للمسار ليسهل استدعاؤه في الكود لاحقاً عبر دالة route()', 'تسمية الرابط باللغة العربية', 'رابط مشفر', 'رابط لا يعمل'],
                    ['ما هو الـ Route Group؟', 'تجميع مجموعة مسارات لتطبيق خصائص مشتركة (مثل Middleware) عليها دفعة واحدة', 'دليل للمسارات', 'حذف المسارات القديمة', 'لا يوجد شيء بهذا الاسم'],
                    ['كيف نحمي مجموعة مسارات ليدخلها "الأعضاء فقط"؟', "باستخدام Middleware 'auth'", "باستخدام Middleware 'guest'", "باستخدام Middleware 'web'", "لا يمكن حمايتها"],
                    ['ما وظيفة دالة Route::redirect؟', 'تحويل الرابط من عنوان برمجي إلى عنوان آخر تلقائياً', 'حذف الرابط', 'تغيير شكل الرابط', 'تشفير الرابط'],
                    ['أين نكتب مسارات الـ API في لارافل؟', 'في ملف api.php', 'في ملف web.php', 'في ملف console.php', 'في ملف channels.php'],
                    ['لماذا نستخدم Controller بدلاً من كتابة الكود داخل الـ Route مباشرة؟', 'لتنظيم الكود وفصله عن ملف المسارات لجعل المشروع قابلاً للصيانة', 'لأن الكنترولر أسرع في التنفيذ', 'لأن المسارات لا تدعم الكود الطويل', 'لأن الكنترولر مجاني'],
                    ['ما الأمر الذي يعرض لك كافة المسارات المسجلة في مشروعك؟', 'php artisan route:list', 'php artisan list:routes', 'php artisan show:routes', 'php artisan routes'],
                ]],
                ['Eloquent ORM', '<h1>🗃️ التعامل مع قواعد البيانات بسلاسة</h1>
<p>إيلوكويت هو سحر لارافل، يتيح لك التعامل مع قاعدة البيانات كأنها كائنات برمجية (Objects) بدون كتابة SQL معقد.</p>', [
                    ['ما هو الـ Model في لارافل؟', 'كلاس يمثل جدولاً في قاعدة البيانات ويتعامل معه', 'نوع من أنواع التصاميم', 'ملف لعرض النتائج للزوار', 'أداة للتحكم في الألوان'],
                    ['ما هي الـ Migrations؟', 'نظام "إصدارات" لقاعدة البيانات يتيح بناء الجداول وتعديلها عبر الكود', 'عملية نقل الموقع من سيرفر لآخر', 'تصغير حجم الصور', 'تشفير كلمات السر'],
                    ['كيف نجلب كافة السجلات من جدول Users باستخدام Eloquent؟', 'User::all()', 'User::get_all()', 'SELECT * FROM users', 'User::fetch()'],
                    ['ما وظيفة الخاصية $fillable داخل الموديل؟', 'تحديد الحقول التي يسمح بتعبئتها دفعة واحدة (Mass Assignment)', 'تحديد الحقول التي تظهر في المتصفح', 'تنسيق الخطوط في قاعدة البيانات', 'لا وظيفة لها'],
                    ['ما هي علاقة One To Many (واحد إلى كثير)؟', 'مثل: قسم واحد يحتوي على عدة مقالات', 'مثل: مستخدم واحد له بروفايل واحد', 'مثل: طالب له عدة أساتذة والأستاذ له عدة طلاب', 'لا توجد هكذا علاقة'],
                    ['ما الأمر الذي يقوم بإنشاء ملف Migration جديد؟', 'php artisan make:migration name', 'php artisan create:table name', 'php artisan db:table name', 'new table name'],
                    ['كيف نقوم بتنفيذ المهاجرشن (بناء الجداول فعلياً في القاعدة)؟', 'php artisan migrate', 'php artisan run:sql', 'php artisan db:seed', 'php artisan build'],
                    ['ما هو الـ Seeder؟', 'كلاس يستخدم لتعبئة قاعدة البيانات ببيانات أولية أو تجريبية', 'برنامج لحماية قاعدة البيانات', 'أداة لحذف السجلات القديمة', 'محرك بحث'],
                    ['ما هي الـ Query Builder؟', 'طريقة للتعامل مع قاعدة البيانات بكود PHP مرن دون الحاجة لاستخدام الموديل دائماً', 'أداة لتصميم المواقع', 'برنامج للفوتوشوب', 'نوع محرك بحث'],
                    ['ماذا تفعل دالة create() في الموديل؟', 'تقوم بإنشاء سجل جديد وحفظه في قاعدة البيانات فوراً', 'تقوم بمسح سجل', 'تقوم بتعديل سجل', 'تقوم بعرض سجل'],
                ]],
            ]],
            // Track 17 – Computer Science Fundamentals
            ['أساسيات علوم الحاسب', 'الخوارزميات وهياكل البيانات ونظرية الحوسبة.', [
                ['مقدمة في الخوارزميات', '<h1>🧠 التفكير الخوارزمي</h1>
<p>الخوارزمية ليست كوداً برمجياً فحسب، بل هي منطق حل المشكلات خطوة بخطوة.</p>', [
                    ['ما هو التعريف الدقيق للخوارزمية؟', 'مجموعة من الخطوات المتسلسلة والمنطقية لحل مشكلة محددة', 'لغة برمجة مثل جاوا', 'جهاز حاسوب فائق السرعة', 'نوع من أنواع قواعد البيانات'],
                    ['ما المقصود بـ Time Complexity (تعقيد الوقت)؟', 'مقياس لمدى زيادة وقت تنفيذ الخوارزمية مع زيادة حجم البيانات المدخلة', 'الوقت الذي يستغرقه المبرمج في كتابة الكود', 'سرعة المعالج بالجيجاهرتز', 'تاريخ انتهاء البرنامج'],
                    ['ما هو الـ Big O Notation؟', 'تمثيل رياضي لوصف أداء الخوارزمية في أسوأ الحالات', 'اسم شركة تكنولوجية', 'لغة برمجة قديمة', 'طريقة لتسمية المتغيرات'],
                    ['أيهما أسرع: خوارزمية بـ O(1) أم O(n)؟', 'O(1) أسرع لأن وقتها ثابت لا يعتمد على حجم البيانات', 'O(n) أسرع دائماً', 'لهما نفس السرعة', 'يعتمد على لون الشاشة'],
                    ['ما هو الـ Binary Search (البحث الثنائي)؟', 'خوارزمية بحث سريعة تعمل فقط على البيانات المرتبة بتقسيمها لنصفين في كل خطوة', 'بحث عشوائي عن البيانات', 'طريقة للبحث في جوجل', 'البحث في قائمة غير مرتبة'],
                    ['ما هو الـ Recursion (التكرار المتداخل)؟', 'أن تقوم الدالة باستدعاء نفسها لحل مشكلة أصغر', 'تكرار الكود يدوياً', 'حلقة loop عادية', 'خطأ برمي يؤدي للتوقف'],
                    ['ما هي خوارزمية الـ Sorting (الترتيب)؟', 'عملية تنظيم البيانات بترتيب معين (مثل من الأصغر للأكبر)', 'عملية حذف البيانات المكررة', 'عملية تشفير البيانات', 'عملية طباعة البيانات'],
                    ['ماذا يعني O(n^2)؟', 'أن الوقت يزداد بشكل تربيعي (غالباً وجود حلقتين متداخلتين Nested Loops)', 'أن الوقت ثابت', 'أن الخوارزمية ذكية جداً', 'أن البيانات مشفرة'],
                    ['ما هي الـ Greedy Algorithms؟', 'خوارزميات تتخذ "القرار الأفضل حالياً" في كل خطوة أملاً في الوصول للحل النهائي', 'خوارزميات تستهلك الكثير من الرام', 'خوارزميات بطيئة جداً', 'خوارزميات مخترقة'],
                    ['ما المقصود بـ Space Complexity؟', 'مقدار الذاكرة (RAM) التي تستهلكها الخوارزمية أثناء التنفيذ', 'مساحة الهاردسك المطلوبة للبرنامج', 'حجم الشاشة المطلوب', 'بعد السيرفر عن المستخدم'],
                ]],
                ['هياكل البيانات الأساسية', '<h1>📊 تنظيم البيانات في الذاكرة</h1>
<p>بنية البيانات الصحيحة تجعل الكود أسرع بآلاف المرات. اختر أداتك بعناية.</p>', [
                    ['ما هي الـ Array (المصفوفة)؟', 'بنية تخزن عناصر من نفس النوع في أماكن متجاورة في الذاكرة', 'مجموعة من الصور', 'ملف نصي', 'رابط موقع'],
                    ['ما هو الـ Stack (المكدس)؟', 'بنية بيانات تتبع مبدأ LIFO (الأخير دخولاً هو الأول خروجاً)', 'بنية تتبع مبدأ FIFO', 'قاعدة بيانات عملاقة', 'نوع من أنواع الذاكرة المؤقتة'],
                    ['ما هي الـ Queue (الطابور)؟', 'بنية بيانات تتبع مبدأ FIFO (الأول دخولاً هو الأول خروجاً)', 'مجموعة من الملفات', 'بنية تتبع مبدأ LIFO', 'طريقة لتشفير البيانات'],
                    ['ما هي الـ Linked List (القائمة المرتبطة)؟', 'عناصر مشتتة في الذاكرة يربط بينها "مؤشرات" (Pointers)', 'قائمة تسوق', 'مصفوفة ثابتة الحجم', 'ملف إكسل'],
                    ['ما هو الـ Pointer (المؤشر)؟', 'متغير يخزن عنوان مكان في الذاكرة بدلاً من القيمة نفسها', 'سهم يظهر على الشاشة', 'نوع من ملفات الوسائط', 'كلمة سر'],
                    ['ما هي الـ Hash Table؟', 'بنية بيانات تسمح بالوصول السريع جداً للعناصر باستخدام "مفتاح" (Key)', 'جدول بيانات عادي', 'رسم بياني للفوركس', 'طريقة لضغط الملفات'],
                    ['ما المقصود بـ Null في هياكل البيانات؟', 'قيمة تمثل "لا شيء" أو نهاية القائمة', 'رقم صفر', 'خطأ في النظام', 'أكبر قيمة ممكنة'],
                    ['ما هي الـ Tree (الشجرة) في علوم الحاسب؟', 'بنية بيانات هرمية تتكون من عقد (Nodes) مرتبطة بعلاقة أب وابن', 'صورة لشجرة', 'ملف نظام لويندوز', 'نوع من أنواع الخطوط'],
                    ['ما هي العقدة (Node)؟', 'الوحدة الأساسية التي تتكون منها هياكل البيانات مثل القائمة المرتبطة والشجرة', 'نقطة اتصال إنترنت', 'اسم مستخدم', 'سرعة المعالج'],
                    ['ما الفرق بين البيانات الخطية والغير خطية؟', 'الخطية مرتبة بتسلسل واحد (مثل المصفوفة)، الغير خطية متشعبة (مثل الشجرة)', 'الخطية أسرع دائماً', 'الغير خطية للصور فقط', 'لا فرق'],
                ]],
                ['الأشجار والرسوم البيانية', '<h1>🌲 الأشجار والرسوم البيانية (Graphs)</h1>
<p>كيف تمثل خرائط جوجل الطرق؟ وكيف تنظم الملفات في جهازك؟ الإجابة هي الجراف والأشجار.</p>', [
                    ['ما هي الـ Binary Tree (الشجرة الثنائية)؟', 'شجرة لا تملك فيها كل عقدة أكثر من ابنين فقط', 'شجرة بلونين فقط', 'شجرة تعمل بالنظام الثنائي 0 و 1', 'نوع من أنواع الذاكرة'],
                    ['ما هو الـ Graph (الرسم البياني)؟', 'مجموعة من العقد (Vertices) تربط بينها حواف (Edges)', 'صورة توضيحية في إكسل', 'خريطة ورقية', 'نوع من أنواع الخطوط'],
                    ['ما المقصود بـ BST (Binary Search Tree)؟', 'شجرة ثنائية مرتبة: الابن الأصغر على اليسار والأكبر على اليمين لتسهيل البحث', 'شجرة بحث عشوائي', 'اختصار لشركة تقنية', 'نوع من أنواع قواعد البيانات'],
                    ['ما هو الـ Root في الشجرة؟', 'العقدة العليا التي تبدأ منها الشجرة ولا أب لها', 'جذر النبات', 'كلمة سر النظام', 'آخر عقدة في الشجرة'],
                    ['ما هي الـ Leaf Node (العقدة الورقية)؟', 'العقدة التي لا تملك أي أبناء وتكون في نهاية الفروع', 'عقدة خضراء اللون', 'أول عقدة في الشجرة', 'عقدة محذوفة'],
                    ['ما هو الـ BFS (Breadth-First Search)؟', 'خوارزمية للبحث في الجراف تبحث مستوى بمستوى (بالعرض)', 'البحث السريع جداً', 'البحث في الملفات فقط', 'البحث بالعمق أولاً'],
                    ['ما هو الـ DFS (Depth-First Search)؟', 'خوارزمية للبحث في الجراف تذهب لأعمق نقطة في الفرع قبل الانتقال للفرع التالي', 'البحث السطحي', 'البحث عن الصور', 'نوع من أنواع القواعد'],
                    ['ماذا يمثل الـ Edge في الجراف؟', 'الرابط أو العلاقة بين عقدتين', 'حافة الشاشة', 'نهاية قاعدة البيانات', 'اسم المستخدم'],
                    ['ما المقصود بـ Weighted Graph؟', 'جراف يكون فيه للحوائف قيم (أوزان) مثل "المسافة" بين مدينتين', 'جراف ثقيل الحجم', 'جراف للصور الثقيلة', 'جراف معقد برمجياً'],
                    ['ما هي الـ Cycle في الجراف؟', 'مسار يبدأ من عقدة وينتهي إليها (دائرة مغلقة)', 'عملية إعادة تشغيل البرنامج', 'خطأ يؤدي لمسح البيانات', 'تكرار الكود'],
                ]],
            ]],
            // Track 18 – Freelancing
            ['العمل الحر والـ Freelancing', 'كيف تبني مسيرة ناجحة كمستقل في مجال التقنية.', [
                ['مقدمة في العمل الحر', '<h1>🚀 طريقك للاستقلال المهني</h1>
<p>العمل الحر ليس مجرد مهارة تقنية، بل هو "بيزنس" متكامل يتطلب إدارة، تسويقاً، وصبراً.</p>', [
                    ['ما هو تعريف الـ Freelancer (المستقل)؟', 'شخص يعمل لحسابه الخاص ويقدم خدمات لعدة عملاء بدلاً من الالتزام بشركة واحدة', 'موظف في شركة حكومية', 'طالب يدرس البرمجة', 'صاحب شركة ضخمة'],
                    ['ما هي أهم ميزة في العمل الحر؟', 'المرونة في الوقت والمكان والتحكم في نوعية المشاريع التي تعمل عليها', 'الحصول على راتب ثابت شهرياً', 'عدم الحاجة للتفكير في التسويق', 'التأمين الطبي المجاني'],
                    ['ما هو الـ Portfolio؟', 'معرض أعمال يحتوي على أفضل المشاريع التي أنجزتها لإثبات مهارتك للعملاء', 'سيرتك الذاتية المكتوبة فقط', 'محفظة نقود حقيقية', 'اسم برنامج برمجي'],
                    ['ما المقصود بـ Soft Skills؟', 'مهارات التواصل، إدارة الوقت، والتفاوض مع العملاء', 'مهارات كتابة الكود المعقد', 'مهارات إصلاح الحواسيب', 'برامج الجرافيك السهلة'],
                    ['لماذا التخصص (Niche) مهم للمستقل؟', 'لأنه يجعلك خبيراً في مجال محدد مما يسهل جذب العملاء ورفع سعرك', 'لأنه يجعلك تتعلم كل لغات البرمجة', 'لأنه يقلل المنافسة للأبد', 'ليس له أهمية'],
                    ['ما هو العائق الأكبر الذي يواجه المبتدئين في العمل الحر؟', 'الحصول على أول عميل وبناء الثقة', 'غلاء أسعار الحواسيب', 'اللغة العربية', 'عدم توفر إنترنت'],
                    ['ما هي منصة Upwork؟', 'واحدة من أكبر المنصات العالمية للعمل الحر', 'برنامج لتصميم المنازل', 'لغة برمجة جديدة', 'موقع للتواصل الاجتماعي'],
                    ['ماذا يعني "العمل مقابل ساعة" (Hourly Rate)؟', 'أن يتقاضى المستقل مبلغاً محدداً مقابل كل ساعة عمل ينجزها', 'العمل ساعة واحدة فقط في اليوم', 'دفع قيمة الساعة للعميل', 'دفع فاتورة الكهرباء'],
                    ['أيهما أفضل للمبتدئين: المنصات العربية أم العالمية؟', 'يفضل البدء بما يتناسب مع لغتك ومهارتك، فكلاهما يوفر فرصاً قوية', 'العالمية فقط', 'العربية فقط', 'لا يوجد فرق'],
                    ['ما أهمية الـ Personal Branding؟', 'بناء اسم وسمعة احترافية تجعل العملاء يبحثون عنك بالاسم', 'تلوين الموقع الشخصي', 'اختيار ملابس أنيقة', 'تغيير صورة البروفايل باستمرار'],
                ]],
                ['منصات العمل الحر', '<h1>🏢 اختيار "سوق" العمل المناسب</h1>
<p>هناك منصات لكل نوع من الخدمات، اختر المنصة التي يتواجد فيها عملاؤك المثاليون.</p>', [
                    ['ما الفرق الجوهري بين Upwork و Fiverr؟', 'Upwork يعتمد على تقديم عروض للمشاريع، Fiverr يعتمد على عرض خدماتك كـ "منتجات"', 'لا فرق بينهما', 'Upwork مجاني تماماً', 'Fiverr للبرمجة فقط'],
                    ['ما هي منصة "مستقل"؟', 'أكبر منصة عربية للعمل الحر تابعة لشركة حسوب', 'منصة لبيع الكتب', 'منصة لتعليم الطبخ', 'موقع إخباري للتقنية'],
                    ['ماذا يعني اختصار "Gig" على موقع Fiverr؟', 'الخدمة المصغرة التي يعرضها المستقل للبيع', 'سعة الذاكرة', 'اسم مبرمج شهير', 'كود خصم'],
                    ['ما هو الـ Proposal (العرض)؟', 'الرسالة التي ترسلها للعميل لتقنعه باختيارك لتنفيذ مشروعه', 'طلب زيادة الراتب', 'رسالة شكر', 'اسم شركة شحن'],
                    ['لماذا يجب قراءة وصف المشروع (Job Description) بعناية؟', 'ليفهم العميل أنك منتبه للتفاصيل ولتكتب عرضاً مخصصاً يحل مشكلته', 'لأن جوجل يطلب ذلك', 'لزيادة سرعة القراءة', 'لا داعي لقراءته'],
                    ['ما هو نظام الـ Escrow في المنصات؟', 'نظام يحجز أموال العميل لدى المنصة لضمان حق المستقل عند تسليم العمل', 'نظام لتشفير البيانات', 'اسم خادم فائق السرعة', 'نوع من أنواع الضرائب'],
                    ['ما هي منصة Freelancer.com؟', 'منصة عالمية قديمة وشاملة لكل أنواع العمل الحر', 'موقع للألعاب', 'تطبيق للمواعدة', 'موقع لتحميل الأفلام'],
                    ['ما أهمية التقييمات (Reviews) في المنصات؟', 'هي "العملة" الحقيقية للمستقل، فهي تبني الثقة وتجذب العملاء الجدد', 'لتزيين الصفحة فقط', 'للحصول على خصومات من الموقع', 'لا تهم العميل'],
                    ['كيف تجعل الـ Profile الخاص بك جذاباً؟', 'باستخدام صورة احترافية، عنوان واضح، ووصف يركز على الفائدة التي ستقدمها للعميل', 'بوضع صور كرتونية', 'بترك الوصف فارغاً', 'بكتابة "ابحث عن عمل" فقط'],
                    ['ما هي منصة PeoplePerHour؟', 'منصة عمل حر عالمية تركز أكثر على السوق الأوروبي', 'موقع لبيع الساعات', 'تطبيق تتبع وقت', 'لغة برمجة'],
                ]],
                ['التسعير والعقود', '<h1>💰 كيف تتقاضى ما تستحقه فعلاً؟</h1>
<p>التسعير فن يجمع بين تقدير قيمة عملك، تكاليفك، وميزانية العميل.</p>', [
                    ['ما المقصود بـ Fixed Price (السعر الثابت) للمشروع؟', 'الاتفاق على مبلغ محدد لكامل المشروع مهما استغرق من وقت', 'تثبيت سعر العملة', 'دفع راتب شهري', 'السعر الذي لا يمكن تغييره أبداً لمشاريع أخرى'],
                    ['كيف تحسب "سعر الساعة" الخاص بك (Hourly Rate)؟', 'بتقسيم دخلك المستهدف + مصاريفك على ساعات العمل الفعلية', 'باختيار رقم عشوائي', 'بسؤال الجيران', 'بمضاعفة رقم عمرك'],
                    ['ما هو الـ Milestone (المرحلة)؟', 'تقسيم المشروع الكبير لمراحل صغيرة، يتم الدفع عند انتهاء كل منها', 'نهاية المشروع فقط', 'بداية المشروع', 'نوع من أنواع العقود الورقية'],
                    ['لماذا يعتبر السعر المنخفض جداً (Too Low Price) خطراً عليك؟', 'لأنه قد يعطي انطباعاً بضعف جودة عملك ويؤدي للإرهاق السريع', 'لأنه سيجذب عملاء كثيرين', 'لأنه سيسعد المنصة', 'لا خطر فيه'],
                    ['ما المقصود بـ Scope Creep؟', 'توسع متطلبات المشروع وزيادة المهام دون زيادة في الأجر المتفق عليه', 'سرعة إنجاز المشروع', 'تشفير ملفات المشروع', 'اسم أداة برمجية'],
                    ['ما فائدة طلب "دفعة مقدمة" (Down Payment)؟', 'ضمان جدية العميل وتأمين جزء من دخلك قبل البدء بالعمل', 'شراء أدوات جديدة', 'للاحتفاظ بها كذكرى', 'لا تطلب أبداً'],
                    ['ما هو الـ Retainer Contract؟', 'عقد طويل الأمد يدفع فيه العميل مبلغاً ثابتاً كل شهر مقابل عدد ساعات أو خدمات معينة', 'عقد لبيع الأجهزة', 'عقد إيجار مكتب', 'عقد توظيف بدوام كامل'],
                    ['ما هي القيمة المضافة (Value-Based Pricing)؟', 'تسعير المشروع بناءً على "الأرباح أو الفائدة" التي سيحققها العميل من عملك', 'التسعير حسب وزن الملفات', 'التسعير حسب عدد الكلمات', 'التسعير المجاني'],
                    ['ماذا تفعل إذا طلب العميل خصماً (Discount) كبيراً؟', 'حاول تقليل نطاق العمل (Scope) مقابل تقليل السعر', 'ارفض العمل فوراً', 'وافق بدون تفكير لترضي العميل', 'اترك له المشروع مجاناً'],
                    ['لماذا يجب توثيق كل شيء "كتابياً"؟', 'لضمان حقوق الطرفين ومرجعاً عند حدوث أي سوء تفاهم حول المشروع', 'لأن الذاكرة ضعيفة فقط', 'لأن القانون يمنع الكلام', 'لأنه يشغل مساحة'],
                ]],
            ]],
            // Track 19 – Embedded Systems & IoT
            ['الأنظمة المدمجة وإنترنت الأشياء', 'برمجة المتحكمات الدقيقة وبناء مشاريع IoT.', [
                ['مقدمة في الأنظمة المدمجة', '<h1>📟 ماذا نعني بالأنظمة المدمجة؟</h1>
<p>النظام المدمج هو جهاز كمبيوتر مخصص لأداء وظيفة واحدة أو وظائف محدودة داخل نظام أكبر (مثل معالج الغسالة أو نظام الفرامل في السيارة).</p>', [
                    ['ما هو الفرق الجوهري بين الحاسوب الشخصي والنظام المدمج؟', 'الحاسوب الشخصي عام الغرض، النظام المدمج مخصص لوظيفة محددة', 'لا فرق بينهما في الهاردوير', 'النظام المدمج أسرع دائماً', 'النظام المدمج لا يحتوي على معالج'],
                    ['ما هو الـ Microcontroller (المتحكم الدقيق)؟', 'شريحة تحتوي على المعالج والذاكرة والمداخل والمخارج في قطعة واحدة', 'شاشة صغيرة', 'نوع من أنواع البطاريات', 'سكربت برمجي'],
                    ['ما هي لغة البرمجة الأكثر استخداماً في هذا المجال؟', 'لغة C و C++', 'لغة Python فقط', 'لغة HTML', 'لغة Swift'],
                    ['ما المقصود بـ Real-Time Operating System (RTOS)؟', 'نظام تشغيل يضمن تنفيذ المهام في أوقات زمنية محددة وصارمة', 'نظام تشغيل سريع فقط', 'نظام تشغيل للألعاب', 'نظام تشغيل لا يحتاج كهرباء'],
                    ['أين نجد الأنظمة المدمجة في حياتنا؟', 'في السيارات، الأجهزة المنزلية، الطائرات، والمعدات الطبية', 'في الكتب الورقية فقط', 'في برامج الأوفيس فقط', 'لا توجد في حياتنا اليومية'],
                    ['ما هي الـ GPIO Pins؟', 'أطراف التوصيل في المتحكم التي تسمح باستقبال وإرسال الإشارات الرقمية', 'أزرار الكيبورد', 'أماكن وضع البطارية', 'نقاط الواي فاي'],
                    ['ما هو الـ Microprocessor مقارنة بالـ Microcontroller؟', 'المعالج (Processor) يحتاج لذاكرة ومكونات خارجية ليعمل، بينما المتحكم مدمج فيه كل شيء', 'لا فرق بينهما', 'المتحكم أقوى من المعالج', 'المعالج يعمل بدون كهرباء'],
                    ['ماذا يعني Low Power Consumption في هذا المجال؟', 'استهلاك طاقة قليل جداً ليتمكن الجهاز من العمل لسنوات ببطارية صغيرة', 'أن الجهاز بطيء', 'أن الجهاز رخيص الثمن', 'أن الجهاز لا يسخن'],
                    ['ما وظيفة الـ Firmware؟', 'الكود البرمجي الذي يتم كتابته مباشرة على الهاردوير للتحكم في وظائف الجهاز', 'اسم شركة تصنيع', 'برنامج لعرض الفيديوهات', 'قاعدة بيانات للصور'],
                    ['ما هي الـ Embedded C؟', 'نسخة من لغة C مخصصة للتعامل مع موارد الهاردوير المحدودة', 'لغة برمجة جديدة كلياً', 'لغة تستخدم لتصميم المواقع', 'لغة للذكاء الاصطناعي'],
                ]],
                ['Arduino للمبتدئين', '<h1>🤖 ابدأ عالم الإلكترونيات مع أردوينو</h1>
<p>أردوينو هو بوابة الهواة والمحترفين لدخول عالم الهاردوير بفضل سهولة برمجته وتوفر قطعه.</p>', [
                    ['ما هو الـ Arduino؟', 'منصة إلكترونية مفتوحة المصدر تعتمد على هاردوير وسوفتوير سهل الاستخدام', 'نوع من أنواع الروبوتات الضخمة', 'محرك بحث', 'لغة برمجة مثل بايثون'],
                    ['ماذا تسمى البيئة البرمجية التي نكتب فيها كود الأردوينو؟', 'Arduino IDE', 'Visual Studio', 'Notepad', 'Excel'],
                    ['ما هي الدالة التي تنفذ "مرة واحدة فقط" عند تشغيل الأردوينو؟', 'void setup()', 'void loop()', 'void start()', 'void main()'],
                    ['ما هي الدالة التي تتكرر باستمرار طالما الجهاز يعمل؟', 'void loop()', 'void setup()', 'void run()', 'void repeat()'],
                    ['كيف نقوم بتعريف طرف (Pin) كمخرج في الأردوينو؟', "pinMode(pin, OUTPUT);", "setPin(pin, OUT);", "digitalWrite(pin, HIGH);", "makePin(out);"],
                    ['ماذا تفعل دالة digitalWrite(pin, HIGH)؟', 'ترسل تياراً كهربائياً (5 فولت غالباً) لهذا الطرف لتشغيل المكون المتصل به', 'تقرأ قيمة الحساس', 'تطفي الجهاز', 'تغير لون الليد'],
                    ['ما فائدة دالة delay(1000)؟', 'إيقاف تنفيذ الكود لمدة ثانية واحدة (1000 ميلي ثانية)', 'إيقاف الجهاز للأبد', 'زيادة سرعة الكود', 'مسح الذاكرة'],
                    ['ما هو الـ Serial Monitor؟', 'أداة لعرض البيانات المرسلة من الأردوينو للكمبيوتر لغرض الاختبار (Debugging)', 'شاشة متصلة بالأردوينو', 'لوحة مفاتيح', 'برنامج لتعديل الصور'],
                    ['ما هو الـ Breadboard (لوحة التجارب)؟', 'لوحة بلاستيكية بها ثقوب لتوصيل المكونات الإلكترونية ببعضها بدون لحام', 'لوحة التحكم الرئيسية', 'نوع من أنواع الحساسات', 'أسلاك التوصيل'],
                    ['ماذا يحدث إذا عكسنا أقطاب الـ LED (الموجب والسالب)؟', 'لن يضيء الليد وقد يتضرر إذا كان الجهد عالياً', 'سيضيء بلون مختلف', 'سينفجر الأردوينو', 'سيشتغل بشكل طبيعي'],
                ]],
                ['Raspberry Pi', '<h1>🥧 حاسوب بحجم بطاقة الائتمان</h1>
<p>راسبيري باي ليس مجرد متحكم، بل هو حاسوب كامل يعمل بنظام لينكس، قادر على تشغيل برامج معقدة.</p>', [
                    ['ما الفرق الجوهري بين Raspberry Pi و Arduino؟', 'الراسبيري باي حاسوب كامل (SBC)، أما الأردوينو فمتحكم دقيق (Microcontroller)', 'الأردوينو أسرع', 'الراسبيري باي أرخص', 'لا فرق بينهما'],
                    ['ما هو نظام التشغيل الأشهر لـ Raspberry Pi؟', 'Raspberry Pi OS (المبني على دبيان لينكس)', 'Windows 11', 'Android', 'macOS'],
                    ['أين يتم تخزين نظام التشغيل والملفات في الراسبيري باي؟', 'على بطاقة ذاكرة MicroSD', 'على القرص الصلب الداخلي', 'في الرام', 'في السحابة'],
                    ['ماذا يعني اختصار GPIO؟', 'أطراف الإدخال والإخراج العامة (General Purpose Input Output)', 'معالج الرسوميات', 'مدخل الطاقة', 'نوع من أنواع الذاكرة'],
                    ['ما هي اللغة المفضلة لبرمجة الراسبيري باي للتحكم في الـ GPIO؟', 'Python', 'C++', 'Java', 'Assembly'],
                    ['ماذا نحتاج لتشغيل الراسبيري باي كحاسوب مكتب? ', 'شاشة، لوحة مفاتيح، فأرة، ومصدر طاقة (USB-C غالباً)', 'بطارية قلم فقط', 'محرك أقراص CD', 'لا شيء'],
                    ['ما هو الـ SSH؟', 'بروتوكول للتحكم في الراسبيري باي عن بعد عبر شبكة الواي فاي أو الكابل', 'نوع من أنواع الأسلاك', 'اسم المعالج', 'مدخل الصوت'],
                    ['ما وظيفة مدخل HDMI في الراسبيري باي؟', 'لتوصيل الشاشات وعرض الصورة والصوت', 'لشحن الجهاز', 'لتوصيل الحساسات', 'لتوصيل الإنترنت'],
                    ['ما هو الـ Headless Mode؟', 'تشغيل الراسبيري باي بدون توصيل شاشة أو لوحة مفاتيح والتحكم فيه عن بعد', 'وضع توفير الطاقة', 'وضع تحطم النظام', 'وضع الألعاب'],
                    ['لماذا يستخدم الراسبيري باي في مشاريع الذكاء الاصطناعي البسيطة؟', 'لأنه يمتلك قوة معالجة ورام كافية لتشغيل مكتبات مثل OpenCV', 'لأنه صغير الحجم فقط', 'لأنه ملون', 'لأنه لا يسخن'],
                ]],
            ]],
            // Track 20 – Soft Skills & Career
            ['المهارات الناعمة والتطور المهني', 'مهارات القرن الحادي والعشرين للنجاح في مجال التقنية.', [
                ['التواصل الفعّال', '<h1>🗣️ فن التواصل في البيئة التقنية</h1>
<p>قدرتك على شرح فكرة معقدة لزميلك أو لعميل غير تقني هي نصف نجاحك المهني.</p>', [
                    ['ما هو "الاستماع النشط" (Active Listening)؟', 'التركيز التام مع المتحدث وإظهار فهمك عبر ردود فعل ملائمة وليس فقط سماع صوته', 'سماع الموسيقى أثناء العمل', 'مقاطعة المتحدث لتصحيح أخطائه', 'تسجيل الكلام لسماعه لاحقاً'],
                    ['كيف تشرح مفهوماً تقنياً لعميل غير تقني؟', 'باستخدام التشبيهات من الحياة اليومية والابتعاد عن المصطلحات المعقدة', 'باستخدام كود برمجية', 'بالحديث بسرعة لإنهاء الوقت', 'بإخباره أنه لا يحتاج للفهم'],
                    ['ما أهمية الـ Body Language (لغة الجسد) في المقابلات؟', 'تعطي انطباعاً عن ثقتك بنفسك وصدقك وتفاعلك مع الطرف الآخر', 'لا أهمية لها في المهن التقنية', 'تستخدم فقط لمصممي الجرافيك', 'مهمة فقط إذا كنت تعمل كممثل'],
                    ['ما معنى الـ Constructive Criticism (النقد البناء)؟', 'تقديم ملاحظات تهدف لتحسين العمل والأداء بدون تجريح الشخص', 'البحث عن أخطاء الزملاء للسخرية منهم', 'الموافقة على كل شيء دون إبداء رأي', 'انتقاد الملابس والمظهر'],
                    ['كيف تتعامل مع رسائل البريد الإلكتروني المهنية؟', 'بكتابة عنوان واضح، تحية رسمية، والدخول في صلب الموضوع باختصار', 'بالرد بكلمة واحدة دائماً', 'بتجاهل الرسائل غير المهمة', 'بإرسال صور ترفيهية'],
                    ['ما هي الـ Non-verbal communication؟', 'التواصل عبر تعابير الوجه، نبرة الصوت، والمظهر (بدون كلمات)', 'التحدث بلغة الإشارة فقط', 'إرسال الرسائل النصية', 'البرمجة'],
                    ['لماذا يفضل "وضوح الهدف" قبل بداية أي اجتماع؟', 'لتوفير الوقت والتأكد من خروج الجميع بنتيجة محددة ومنتجة', 'ليعرف الموظفون من هو المدير', 'لطلب الطعام في الوقت المناسب', 'لا فائدة من الوضوح'],
                    ['كيف ترفض طلباً في العمل بطريقة احترافية؟', 'بشرح السبب (مثل ضيق الوقت) وتقديم بديل أو موعد آخر إن أمكن', 'بقول كلمة "لا" وإغلاق الباب', 'بتجاهل الطلب تماماً', 'بالبدء في الصراخ'],
                    ['ما المقصود بـ Empathy (التعاطف) في العمل الجماعي؟', 'قدرتك على فهم مشاعر وظروف زملائك ووضع نفسك مكانهم', 'أن تبكي معهم', 'أن تشتري لهم الهدايا', 'أن تترك عملك لتقوم بعملهم'],
                    ['ما هو الـ Elevator Pitch؟', 'تعريف موجز جداً عن نفسك ومهاراتك في وقت لا يتجاوز 30 ثانية', 'لعبة يتم لعبها في المصعد', 'طريقة لإصلاح المصاعد', 'إعلان مدفوع في برج طويل'],
                ]],
                ['التفكير النقدي وحل المشكلات', '<h1>🧩 المبرمج هو "حلال مشاكل" بامتياز</h1>
<p>البرمجة هي مجرد وسيلة، والهدف الحقيقي هو حل المشكلات بطريقة إبداعية وفعالة.</p>', [
                    ['ما المقصود بـ Critical Thinking (التفكير النقدي)؟', 'تحليل المعلومات بموضوعية واتخاذ قرارات مدروسة بناءً على حقائق بدلاً من العواطف', 'انتقاد كل شيء يفعله الآخرون', 'التفكير بسرعة كبيرة جداً', 'حفظ الكود عن ظهر قلب'],
                    ['ما هي استراتيجية "فرق تسد" (Divide and Conquer) في حل المشكلات؟', 'تقسيم المشكلة الكبيرة لعدة مشاكل صغيرة يسهل حل كل منها على حدة', 'التفريق بين زملاء العمل ليضعفوا', 'العمل بمفردك دائماً', 'حذف المشكلة وتجاهلها'],
                    ['ماذا تفعل عندما تواجه "Bugs" (أخطاء) صعبة في الكود؟', 'تحلل الخطوات التي أدت للخطأ، تقرأ رسائل الخطأ، وتجرب الحلول بشكل منظم', 'تعيد كتابة البرنامج كله من الصفر', 'تترك البرمجة وتبحث عن مهنة أخرى', 'تلوم جهاز الكمبيوتر'],
                    ['ما هي أهمية الـ First Principles Thinking؟', 'تفكيك المشكلة إلى حقائقها الأساسية وإعادة بنائها من الصفر بدلاً من التقليد', 'قراءة القواعد الأساسية للشركة', 'التفكير في المركز الأول دائماً', 'الاعتماد على آراء الآخرين فقط'],
                    ['ماذا يعني الـ Brainstorming (العصف الذهني)؟', 'جلسة جماعية لإنتاج أكبر عدد ممكن من الأفكار دون نقدها في البداية', 'إرهاق العقل بالتفكير الزائد', 'مرض يصيب المبرمجين', 'نوع من أنواع التنبؤ بالطقس'],
                    ['ما هو الـ Root Cause Analysis؟', 'البحث عن "السبب الحقيقي" للمشكلة بدلاً من معالجة الأعراض السطحية فقط', 'تحليل جذور الأشجار', 'فحص الفيروسات', 'حساب تكلفة المشروع'],
                    ['لماذا يجب "توثيق" الحلول بعد حل المشكلة؟', 'لمنع تكرار نفس المشكلة وتكون مرجعاً للآخرين في المستقبل', 'لزيادة حجم الملفات', 'لسد وقت الفراغ', 'لا يجب التوثيق'],
                    ['ما معنى الـ Trial and Error (التجربة والخطأ)؟', 'استراتيجية تجربة عدة حلول مختلفة حتى تجد الحل الصحيح، وهي مفيدة أحياناً', 'ارتكاب الأخطاء المتعمدة', 'حذف الكود عشوائياً', 'الانتظار حتى تحل المشكلة نفسها'],
                    ['كيف يساعد "الهدوء" في حل المشكلات المعقدة؟', 'يسمح للدماغ بالتفكير المنطقي والابتعاد عن التوتر الذي يشل التفكير', 'يجعل الوقت يمر أبطأ', 'يجعل الكود يعمل تلقائياً', 'لا علاقة للهدوء بالحل'],
                    ['ما هو الـ Flowchart (المخطط الانسيابي)؟', 'تمثيل بصري للخطوات المنطقية لحل مشكلة أو سير عملية ما', 'رسم فني للزينة', 'خريطة لتدفق المياه', 'نوع من أنواع الصور الشخصية'],
                ]],
                ['إدارة الوقت والإنتاجية', '<h1>⏱️ استثمر وقتك، لا تضيعه</h1>
<p>الوقت هو أصلك الأغلى كمطور. تعلم كيف تديره لتنجز أكثر بجهد أقل.</p>', [
                    ['ما هي تقنية الـ Pomodoro؟', 'العمل بتركيز لمدة 25 دقيقة ثم أخذ استراحة لمدة 5 دقائق', 'نوع من أنواع البيتزا الإيطالية', 'برنامج لكتابة الكود', 'طريقة لطبخ الطماطم'],
                    ['ما هي "مصفوفة أيزنهاور" (Eisenhower Matrix)؟', 'أداة لترتيب المهام حسب "الأهمية" و "الاستعجال"', 'جدول لضرب الأرقام', 'خريطة للعلاقات الدولية', 'نوع من أنواع قواعد البيانات'],
                    ['ما المقصود بـ Deep Work (العمل العميق)؟', 'فترة من التركيز التام بدون أي تشتيت (بدون إشعارات أو هاتف) لإنجاز مهام معقدة', 'العمل تحت الماء', 'العمل في أوقات متأخرة بالليل', 'العمل على تفاصيل صغيرة جداً'],
                    ['لماذا يعتبر الـ Multitasking (تعدد المهام) خرافة تضر بالإنتاجية؟', 'لأن العقل يستغرق وقتاً طويلاً للانتقال بين المهام مما يقلل التركيز والجودة', 'لأنه يجعل المبرمج بطلاً خارقاً', 'لأنه يوفر الكثير من الوقت', 'لأنه يحسن جودة الكود'],
                    ['ما هو الـ Time Blocking؟', 'تخصيص "بلوكات" زمنية محددة في جدولك لكل مهمة بدلاً من قائمة مهام مفتوحة', 'منع الوقت من المرور', 'حظر الأشخاص المزعجين', 'توقف الساعة عن العمل'],
                    ['ما هي قاعدة الـ 2 دقيقة؟', 'إذا كانت المهمة تستغرق أقل من دقيقتين، قم بها فوراً ولا تؤجلها', 'مدة أكل التفاحة', 'سرعة كتابة جملة واحدة', 'وقت استراحة المبرمج'],
                    ['ما أهمية الـ Deadlines (المواعيد النهائية)؟', 'تخلق شعوراً بالمسؤولية وتساعد في ترتيب الأولويات لإنهاء العمل في وقته', 'تسبب التوتر غير المفيد فقط', 'لا أهمية لها في العمل الحر', 'لإجبار الموظفين على السهر'],
                    ['ما معنى الـ Procrastination (التسويف)؟', 'تأجيل المهام المهمة والقيام بأشياء تافهة بدلاً منها', 'السرعة في الإنجاز', 'تنظيم الملفات', 'التحدث مع العملاء'],
                    ['كيف تساعد "قوائم المهام" (To-Do Lists)؟', 'تفرغ عقلك من تذكر كل شيء وتجعلك تركز على ما يجب فعله الآن', 'تزيد من القلق', 'تستخدم لزينة المكتب', 'لا تساعد في شيء'],
                    ['ما هو الـ Burnout (الاحتراق الوظيفي)؟', 'حالة من الإرهاق الشديد ناتجة عن ضغط العمل المستمر بدون راحة كافية', 'احتراق جهاز الكمبيوتر', 'زيادة الراتب بشكل مفاجئ', 'العمل في الصيف والشمس'],
                ]],
            ]],
        ];

        foreach ($quickTracks as $i => $trackData) {
            echo " Creating Track " . ($i + 16) . ": " . $trackData[0] . "...\n";
            $buildTrack($trackData[0], $trackData[1], $trackData[2]);
        }

        echo "\n✅ ExtraSeeder completed successfully!\n";
        echo "📊 Total Tracks created : 20\n";
        echo "📚 Total Topics per track: 10\n";
        echo "❓ Total Questions per topic: 10+\n";
        echo "🔢 Grand Total Questions  : ~1000\n";
    }
}
