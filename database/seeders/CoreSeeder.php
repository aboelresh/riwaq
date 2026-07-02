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

class CoreSeeder extends Seeder
{
    public function run(): void
    {
        echo " Seeding database...\n\n";

        $admin = User::firstOrCreate(
            ['email' => 'Admin@codemaster.com'],
            [
                'name'              => 'Admin',
                'password'          => Hash::make('password'),
                'role'              => 'admin',
                'email_verified_at' => now(),
            ]
        );

        
        User::firstOrCreate(
            ['email' => 'learner@codemaster.com'],
            [
                'name'              => 'Learner Test',
                'password'          => Hash::make('password123'),
                'role'              => 'learner',
                'email_verified_at' => now(),
            ]
        );

        echo " Creating topics...\n";
        $topic1 = Topic::create([
            'title' => 'Introduction to HTML',
            'type' => 'article',
            'content' => '<h1>What is HTML?</h1><p>HTML stands for HyperText Markup Language. It is the standard markup language for creating web pages.</p><p>HTML consists of a series of elements that tell the browser how to display content.</p>',
            'created_by' => $admin->id,
        ]);

        $topic2 = Topic::create([
            'title' => 'HTML Tags Explained',
            'type' => 'article',
            'content' => '<h1>HTML Tags</h1><p>HTML tags are the building blocks of HTML. Common tags include div, p, h1, a, img, etc.</p><p>Tags usually come in pairs: opening and closing tags.</p>',
            'created_by' => $admin->id,
        ]);

        $topic3 = Topic::create([
            'title' => 'HTML Forms Tutorial',
            'type' => 'article',
            'content' => '<h1>HTML Forms</h1><p>Forms allow users to input data. They use input, textarea, select, and button elements.</p>',
            'created_by' => $admin->id,
        ]);

        $topic4 = Topic::create([
            'title' => 'Introduction to CSS',
            'type' => 'article',
            'content' => '<h1>What is CSS?</h1><p>CSS stands for Cascading Style Sheets. It is used to style HTML elements.</p>',
            'created_by' => $admin->id,
        ]);

        $topic5 = Topic::create([
            'title' => 'CSS Selectors',
            'type' => 'article',
            'content' => '<h1>CSS Selectors</h1><p>Selectors are used to target HTML elements. Examples: class, id, element, attribute selectors.</p>',
            'created_by' => $admin->id,
        ]);

        echo " Creating quizzes...\n";
        $quiz1 = Quiz::create([
            'title' => 'HTML Basics Quiz',
            'type' => 'topic',
            'topic_id' => $topic1->id,
            'total_points' => 10,
            'pass_percentage' => 50,
            'created_by' => $admin->id,
        ]);

        $q1 = Question::create([
            'quiz_id' => $quiz1->id,
            'question_text' => 'What does HTML stand for?',
            'points' => 5,
        ]);

        Answer::create(['question_id' => $q1->id, 'answer_text' => 'HyperText Markup Language', 'is_correct' => true]);
        Answer::create(['question_id' => $q1->id, 'answer_text' => 'High Tech Modern Language', 'is_correct' => false]);
        Answer::create(['question_id' => $q1->id, 'answer_text' => 'Home Tool Markup Language', 'is_correct' => false]);

        $q2 = Question::create([
            'quiz_id' => $quiz1->id,
            'question_text' => 'Which tag is used for paragraphs?',
            'points' => 5,
        ]);

        Answer::create(['question_id' => $q2->id, 'answer_text' => '<p>', 'is_correct' => true]);
        Answer::create(['question_id' => $q2->id, 'answer_text' => '<para>', 'is_correct' => false]);
        Answer::create(['question_id' => $q2->id, 'answer_text' => '<paragraph>', 'is_correct' => false]);

        $quiz2 = Quiz::create([
            'title' => 'HTML Tags Quiz',
            'type' => 'topic',
            'topic_id' => $topic2->id,
            'total_points' => 10,
            'pass_percentage' => 50,
            'created_by' => $admin->id,
        ]);

        $q3 = Question::create([
            'quiz_id' => $quiz2->id,
            'question_text' => 'Which tag creates a hyperlink?',
            'points' => 10,
        ]);

        Answer::create(['question_id' => $q3->id, 'answer_text' => '<a>', 'is_correct' => true]);
        Answer::create(['question_id' => $q3->id, 'answer_text' => '<link>', 'is_correct' => false]);
        Answer::create(['question_id' => $q3->id, 'answer_text' => '<href>', 'is_correct' => false]);

        $quiz3 = Quiz::create([
            'title' => 'HTML Forms Quiz',
            'type' => 'topic',
            'topic_id' => $topic3->id,
            'total_points' => 10,
            'pass_percentage' => 50,
            'created_by' => $admin->id,
        ]);

        $q4 = Question::create([
            'quiz_id' => $quiz3->id,
            'question_text' => 'Which tag is used for text input?',
            'points' => 10,
        ]);

        Answer::create(['question_id' => $q4->id, 'answer_text' => '<input type="text">', 'is_correct' => true]);
        Answer::create(['question_id' => $q4->id, 'answer_text' => '<text>', 'is_correct' => false]);
        Answer::create(['question_id' => $q4->id, 'answer_text' => '<textbox>', 'is_correct' => false]);

        $quiz4 = Quiz::create([
            'title' => 'CSS Basics Quiz',
            'type' => 'topic',
            'topic_id' => $topic4->id,
            'total_points' => 10,
            'pass_percentage' => 50,
            'created_by' => $admin->id,
        ]);

        $q8 = Question::create([
            'quiz_id' => $quiz4->id,
            'question_text' => 'What does CSS stand for?',
            'points' => 10,
        ]);

        Answer::create(['question_id' => $q8->id, 'answer_text' => 'Cascading Style Sheets', 'is_correct' => true]);
        Answer::create(['question_id' => $q8->id, 'answer_text' => 'Computer Style Sheets', 'is_correct' => false]);

        $quiz5 = Quiz::create([
            'title' => 'CSS Selectors Quiz',
            'type' => 'topic',
            'topic_id' => $topic5->id,
            'total_points' => 10,
            'pass_percentage' => 50,
            'created_by' => $admin->id,
        ]);

        $q9 = Question::create([
            'quiz_id' => $quiz5->id,
            'question_text' => 'Which symbol is used for class selectors?',
            'points' => 10,
        ]);

        Answer::create(['question_id' => $q9->id, 'answer_text' => '.', 'is_correct' => true]);
        Answer::create(['question_id' => $q9->id, 'answer_text' => '#', 'is_correct' => false]);

        echo " Creating courses...\n";
        $course1 = Course::create([
            'title' => 'HTML Fundamentals',
            'description' => 'Learn HTML from scratch',
            'created_by' => $admin->id,
        ]);

        $course1->topics()->attach([
            $topic1->id => ['order' => 1],
            $topic2->id => ['order' => 2],
            $topic3->id => ['order' => 3],
        ]);

        $courseFinalQuiz = Quiz::create([
            'title' => 'HTML Course Final Exam',
            'type' => 'course',
            'course_id' => $course1->id,
            'total_points' => 30,
            'pass_percentage' => 50,
            'created_by' => $admin->id,
        ]);

        $q5 = Question::create([
            'quiz_id' => $courseFinalQuiz->id,
            'question_text' => 'What is the correct HTML for creating a hyperlink?',
            'points' => 10,
        ]);

        Answer::create(['question_id' => $q5->id, 'answer_text' => '<a href="url">text</a>', 'is_correct' => true]);
        Answer::create(['question_id' => $q5->id, 'answer_text' => '<link>url</link>', 'is_correct' => false]);

        $q6 = Question::create([
            'quiz_id' => $courseFinalQuiz->id,
            'question_text' => 'Which HTML element defines the title of a document?',
            'points' => 10,
        ]);

        Answer::create(['question_id' => $q6->id, 'answer_text' => '<title>', 'is_correct' => true]);
        Answer::create(['question_id' => $q6->id, 'answer_text' => '<head>', 'is_correct' => false]);

        $q7 = Question::create([
            'quiz_id' => $courseFinalQuiz->id,
            'question_text' => 'Which HTML tag is used to define an internal style sheet?',
            'points' => 10,
        ]);

        Answer::create(['question_id' => $q7->id, 'answer_text' => '<style>', 'is_correct' => true]);
        Answer::create(['question_id' => $q7->id, 'answer_text' => '<css>', 'is_correct' => false]);

        $course2 = Course::create([
            'title' => 'CSS Fundamentals',
            'description' => 'Learn CSS styling',
            'created_by' => $admin->id,
        ]);

        $course2->topics()->attach([
            $topic4->id => ['order' => 1],
            $topic5->id => ['order' => 2],
        ]);

        $course2FinalQuiz = Quiz::create([
            'title' => 'CSS Course Final Exam',
            'type' => 'course',
            'course_id' => $course2->id,
            'total_points' => 20,
            'pass_percentage' => 50,
            'created_by' => $admin->id,
        ]);

        $q10 = Question::create([
            'quiz_id' => $course2FinalQuiz->id,
            'question_text' => 'How do you add a background color in CSS?',
            'points' => 10,
        ]);

        Answer::create(['question_id' => $q10->id, 'answer_text' => 'background-color: red;', 'is_correct' => true]);
        Answer::create(['question_id' => $q10->id, 'answer_text' => 'bg-color: red;', 'is_correct' => false]);

        $q11 = Question::create([
            'quiz_id' => $course2FinalQuiz->id,
            'question_text' => 'Which property is used to change font size?',
            'points' => 10,
        ]);

        Answer::create(['question_id' => $q11->id, 'answer_text' => 'font-size', 'is_correct' => true]);
        Answer::create(['question_id' => $q11->id, 'answer_text' => 'text-size', 'is_correct' => false]);

        // ── Mobile Development Topics ──
        $mobileTopic1 = Topic::create([
            'title' => 'Introduction to Mobile Development',
            'type' => 'article',
            'content' => '<h1>Mobile Development</h1><p>Mobile development involves creating applications for smartphones and tablets. The two major platforms are iOS (Swift) and Android (Kotlin/Java).</p><p>Cross-platform frameworks like React Native and Flutter allow you to build for both platforms from a single codebase.</p>',
            'created_by' => $admin->id,
        ]);

        $mobileTopic2 = Topic::create([
            'title' => 'React Native Basics',
            'type' => 'article',
            'content' => '<h1>React Native</h1><p>React Native lets you build mobile apps using JavaScript and React. Components render to native platform UI elements.</p><p>Key concepts: Views, Text, StyleSheet, FlatList, and Navigation.</p>',
            'created_by' => $admin->id,
        ]);

        $mobileQuiz1 = Quiz::create([
            'title' => 'Mobile Dev Basics Quiz',
            'type' => 'topic',
            'topic_id' => $mobileTopic1->id,
            'total_points' => 10,
            'pass_percentage' => 50,
            'created_by' => $admin->id,
        ]);

        $mq1 = Question::create(['quiz_id' => $mobileQuiz1->id, 'question_text' => 'Which language is used for native Android development?', 'points' => 10]);
        Answer::create(['question_id' => $mq1->id, 'answer_text' => 'Kotlin', 'is_correct' => true]);
        Answer::create(['question_id' => $mq1->id, 'answer_text' => 'Python', 'is_correct' => false]);
        Answer::create(['question_id' => $mq1->id, 'answer_text' => 'Ruby', 'is_correct' => false]);

        $mobileQuiz2 = Quiz::create([
            'title' => 'React Native Quiz',
            'type' => 'topic',
            'topic_id' => $mobileTopic2->id,
            'total_points' => 10,
            'pass_percentage' => 50,
            'created_by' => $admin->id,
        ]);

        $mq2 = Question::create(['quiz_id' => $mobileQuiz2->id, 'question_text' => 'React Native renders to what?', 'points' => 10]);
        Answer::create(['question_id' => $mq2->id, 'answer_text' => 'Native platform UI', 'is_correct' => true]);
        Answer::create(['question_id' => $mq2->id, 'answer_text' => 'WebView HTML', 'is_correct' => false]);

        $mobileCourse = Course::create([
            'title' => 'Mobile App Fundamentals',
            'description' => 'Introduction to mobile app development',
            'created_by' => $admin->id,
        ]);

        $mobileCourse->topics()->attach([
            $mobileTopic1->id => ['order' => 1],
            $mobileTopic2->id => ['order' => 2],
        ]);

        $mobileCourseQuiz = Quiz::create([
            'title' => 'Mobile Course Final Exam',
            'type' => 'course',
            'course_id' => $mobileCourse->id,
            'total_points' => 10,
            'pass_percentage' => 50,
            'created_by' => $admin->id,
        ]);

        $mq3 = Question::create(['quiz_id' => $mobileCourseQuiz->id, 'question_text' => 'Which framework allows cross-platform mobile development with JavaScript?', 'points' => 10]);
        Answer::create(['question_id' => $mq3->id, 'answer_text' => 'React Native', 'is_correct' => true]);
        Answer::create(['question_id' => $mq3->id, 'answer_text' => 'Django', 'is_correct' => false]);

        // ── Data Science Topics ──
        $dataTopic1 = Topic::create([
            'title' => 'Introduction to Data Science',
            'type' => 'article',
            'content' => '<h1>Data Science</h1><p>Data science combines statistics, programming, and domain expertise to extract insights from data.</p><p>Key tools: Python, Pandas, NumPy, Matplotlib, and Scikit-learn.</p>',
            'created_by' => $admin->id,
        ]);

        $dataTopic2 = Topic::create([
            'title' => 'Python for Data Analysis',
            'type' => 'article',
            'content' => '<h1>Python & Pandas</h1><p>Pandas is the go-to library for data manipulation. DataFrames let you work with tabular data efficiently.</p><p>Common operations: filtering, grouping, merging, and aggregation.</p>',
            'created_by' => $admin->id,
        ]);

        $dataQuiz1 = Quiz::create([
            'title' => 'Data Science Basics Quiz',
            'type' => 'topic',
            'topic_id' => $dataTopic1->id,
            'total_points' => 10,
            'pass_percentage' => 50,
            'created_by' => $admin->id,
        ]);

        $dq1 = Question::create(['quiz_id' => $dataQuiz1->id, 'question_text' => 'Which language is most popular for data science?', 'points' => 10]);
        Answer::create(['question_id' => $dq1->id, 'answer_text' => 'Python', 'is_correct' => true]);
        Answer::create(['question_id' => $dq1->id, 'answer_text' => 'PHP', 'is_correct' => false]);
        Answer::create(['question_id' => $dq1->id, 'answer_text' => 'Ruby', 'is_correct' => false]);

        $dataQuiz2 = Quiz::create([
            'title' => 'Python Data Analysis Quiz',
            'type' => 'topic',
            'topic_id' => $dataTopic2->id,
            'total_points' => 10,
            'pass_percentage' => 50,
            'created_by' => $admin->id,
        ]);

        $dq2 = Question::create(['quiz_id' => $dataQuiz2->id, 'question_text' => 'What is the main data structure in Pandas?', 'points' => 10]);
        Answer::create(['question_id' => $dq2->id, 'answer_text' => 'DataFrame', 'is_correct' => true]);
        Answer::create(['question_id' => $dq2->id, 'answer_text' => 'Array', 'is_correct' => false]);

        $dataCourse = Course::create([
            'title' => 'Data Science Fundamentals',
            'description' => 'Introduction to data science and analysis',
            'created_by' => $admin->id,
        ]);

        $dataCourse->topics()->attach([
            $dataTopic1->id => ['order' => 1],
            $dataTopic2->id => ['order' => 2],
        ]);

        $dataCourseQuiz = Quiz::create([
            'title' => 'Data Science Course Final Exam',
            'type' => 'course',
            'course_id' => $dataCourse->id,
            'total_points' => 10,
            'pass_percentage' => 50,
            'created_by' => $admin->id,
        ]);

        $dq3 = Question::create(['quiz_id' => $dataCourseQuiz->id, 'question_text' => 'What does AI stand for?', 'points' => 10]);
        Answer::create(['question_id' => $dq3->id, 'answer_text' => 'Artificial Intelligence', 'is_correct' => true]);
        Answer::create(['question_id' => $dq3->id, 'answer_text' => 'Automated Integration', 'is_correct' => false]);

        // ── Game Development Topics ──
        $gameTopic1 = Topic::create([
            'title' => 'Introduction to Game Development',
            'type' => 'article',
            'content' => '<h1>Game Development</h1><p>Game development combines programming, art, and design to create interactive experiences.</p><p>Popular engines: Unity (C#), Unreal Engine (C++), and Godot (GDScript).</p>',
            'created_by' => $admin->id,
        ]);

        $gameTopic2 = Topic::create([
            'title' => 'Game Physics Basics',
            'type' => 'article',
            'content' => '<h1>Game Physics</h1><p>Physics engines simulate real-world behavior: gravity, collisions, and forces.</p><p>Key concepts: rigidbodies, colliders, raycasting, and velocity.</p>',
            'created_by' => $admin->id,
        ]);

        $gameQuiz1 = Quiz::create([
            'title' => 'Game Dev Basics Quiz',
            'type' => 'topic',
            'topic_id' => $gameTopic1->id,
            'total_points' => 10,
            'pass_percentage' => 50,
            'created_by' => $admin->id,
        ]);

        $gq1 = Question::create(['quiz_id' => $gameQuiz1->id, 'question_text' => 'Which programming language does Unity use?', 'points' => 10]);
        Answer::create(['question_id' => $gq1->id, 'answer_text' => 'C#', 'is_correct' => true]);
        Answer::create(['question_id' => $gq1->id, 'answer_text' => 'JavaScript', 'is_correct' => false]);
        Answer::create(['question_id' => $gq1->id, 'answer_text' => 'Python', 'is_correct' => false]);

        $gameQuiz2 = Quiz::create([
            'title' => 'Game Physics Quiz',
            'type' => 'topic',
            'topic_id' => $gameTopic2->id,
            'total_points' => 10,
            'pass_percentage' => 50,
            'created_by' => $admin->id,
        ]);

        $gq2 = Question::create(['quiz_id' => $gameQuiz2->id, 'question_text' => 'What simulates gravity and collisions in games?', 'points' => 10]);
        Answer::create(['question_id' => $gq2->id, 'answer_text' => 'Physics engine', 'is_correct' => true]);
        Answer::create(['question_id' => $gq2->id, 'answer_text' => 'Rendering engine', 'is_correct' => false]);

        $gameCourse = Course::create([
            'title' => 'Game Development Fundamentals',
            'description' => 'Introduction to game development concepts',
            'created_by' => $admin->id,
        ]);

        $gameCourse->topics()->attach([
            $gameTopic1->id => ['order' => 1],
            $gameTopic2->id => ['order' => 2],
        ]);

        $gameCourseQuiz = Quiz::create([
            'title' => 'Game Dev Course Final Exam',
            'type' => 'course',
            'course_id' => $gameCourse->id,
            'total_points' => 10,
            'pass_percentage' => 50,
            'created_by' => $admin->id,
        ]);

        $gq3 = Question::create(['quiz_id' => $gameCourseQuiz->id, 'question_text' => 'Which engine uses C++ and is known for AAA games?', 'points' => 10]);
        Answer::create(['question_id' => $gq3->id, 'answer_text' => 'Unreal Engine', 'is_correct' => true]);
        Answer::create(['question_id' => $gq3->id, 'answer_text' => 'Unity', 'is_correct' => false]);

        // ── Create all 4 Tracks ──
        echo " Creating tracks...\n";

        $webTrack = Track::create([
            'title' => 'Web Development Track',
            'description' => 'Learn web development from scratch — HTML, CSS, JavaScript, and modern frameworks',
            'created_by' => $admin->id,
        ]);
        // ── Extra Web Course: JavaScript Basics ──
        $jsTopic1 = Topic::create(['title' => 'Introduction to JavaScript', 'type' => 'article', 'content' => '<h1>JavaScript</h1><p>JavaScript is the programming language of the web. It adds interactivity to websites.</p><h2>Variables</h2><p>Use <code>let</code>, <code>const</code>, and <code>var</code> to declare variables.</p><pre><code>let name = "Ahmed";\nconst age = 25;\nconsole.log(name, age);</code></pre><h2>Data Types</h2><p>JavaScript has several data types: String, Number, Boolean, Array, Object, null, undefined.</p>', 'created_by' => $admin->id]);
        $jsTopic2 = Topic::create(['title' => 'Functions & Scope', 'type' => 'article', 'content' => '<h1>Functions</h1><p>Functions are reusable blocks of code.</p><pre><code>function greet(name) {\n  return `Hello, ${name}!`;\n}\n\nconst greetArrow = (name) => `Hello, ${name}!`;</code></pre><h2>Scope</h2><p><strong>Global scope</strong>: accessible everywhere. <strong>Local scope</strong>: accessible only within the function. <strong>Block scope</strong>: <code>let</code> and <code>const</code> are block-scoped.</p>', 'created_by' => $admin->id]);
        $jsTopic3 = Topic::create(['title' => 'DOM Manipulation', 'type' => 'article', 'content' => '<h1>The DOM</h1><p>The Document Object Model represents the page structure as a tree of nodes.</p><pre><code>document.getElementById("app")\ndocument.querySelector(".card")\nelement.innerHTML = "Hello"</code></pre><h2>Event Listeners</h2><pre><code>button.addEventListener("click", () => {\n  alert("Clicked!");\n});</code></pre>', 'created_by' => $admin->id]);

        $jsQuiz1 = Quiz::create(['title' => 'JS Basics Quiz', 'type' => 'topic', 'topic_id' => $jsTopic1->id, 'total_points' => 10, 'pass_percentage' => 50, 'created_by' => $admin->id]);
        $jsq1 = Question::create(['quiz_id' => $jsQuiz1->id, 'question_text' => 'Which keyword declares a constant in JavaScript?', 'points' => 5]);
        Answer::create(['question_id' => $jsq1->id, 'answer_text' => 'const', 'is_correct' => true]);
        Answer::create(['question_id' => $jsq1->id, 'answer_text' => 'var', 'is_correct' => false]);
        Answer::create(['question_id' => $jsq1->id, 'answer_text' => 'static', 'is_correct' => false]);
        $jsq2 = Question::create(['quiz_id' => $jsQuiz1->id, 'question_text' => 'JavaScript runs in the...', 'points' => 5]);
        Answer::create(['question_id' => $jsq2->id, 'answer_text' => 'Browser', 'is_correct' => true]);
        Answer::create(['question_id' => $jsq2->id, 'answer_text' => 'Database', 'is_correct' => false]);

        $jsQuiz2 = Quiz::create(['title' => 'Functions Quiz', 'type' => 'topic', 'topic_id' => $jsTopic2->id, 'total_points' => 10, 'pass_percentage' => 50, 'created_by' => $admin->id]);
        $jsq3 = Question::create(['quiz_id' => $jsQuiz2->id, 'question_text' => 'Arrow functions use which syntax?', 'points' => 10]);
        Answer::create(['question_id' => $jsq3->id, 'answer_text' => '=>', 'is_correct' => true]);
        Answer::create(['question_id' => $jsq3->id, 'answer_text' => '->', 'is_correct' => false]);

        $jsQuiz3 = Quiz::create(['title' => 'DOM Quiz', 'type' => 'topic', 'topic_id' => $jsTopic3->id, 'total_points' => 10, 'pass_percentage' => 50, 'created_by' => $admin->id]);
        $jsq4 = Question::create(['quiz_id' => $jsQuiz3->id, 'question_text' => 'Which method selects an element by ID?', 'points' => 10]);
        Answer::create(['question_id' => $jsq4->id, 'answer_text' => 'getElementById', 'is_correct' => true]);
        Answer::create(['question_id' => $jsq4->id, 'answer_text' => 'getElementByClass', 'is_correct' => false]);

        $jsCourse = Course::create(['title' => 'JavaScript Essentials', 'description' => 'Learn JavaScript from variables to DOM manipulation', 'created_by' => $admin->id]);
        $jsCourse->topics()->attach([$jsTopic1->id => ['order' => 1], $jsTopic2->id => ['order' => 2], $jsTopic3->id => ['order' => 3]]);
        Quiz::create(['title' => 'JavaScript Final Exam', 'type' => 'course', 'course_id' => $jsCourse->id, 'total_points' => 20, 'pass_percentage' => 50, 'created_by' => $admin->id]);

        $webTrack->courses()->attach([
            $course1->id => ['order' => 1],
            $course2->id => ['order' => 2],
            $jsCourse->id => ['order' => 3],
        ]);

        $mobileTrack = Track::create([
            'title' => 'Mobile Development Track',
            'description' => 'Build native and cross-platform mobile applications for iOS and Android',
            'created_by' => $admin->id,
        ]);
        // ── Extra Mobile Course: Flutter ──
        $flutterTopic1 = Topic::create(['title' => 'Flutter Overview', 'type' => 'article', 'content' => '<h1>What is Flutter?</h1><p>Flutter is Google\'s UI toolkit for building natively compiled applications for mobile, web, and desktop from a single codebase.</p><h2>Why Flutter?</h2><ul><li>Hot Reload — see changes instantly</li><li>Rich widgets library</li><li>Single codebase for iOS & Android</li><li>Dart programming language</li></ul>', 'created_by' => $admin->id]);
        $flutterTopic2 = Topic::create(['title' => 'Widgets & Layouts', 'type' => 'article', 'content' => '<h1>Widgets</h1><p>Everything in Flutter is a widget. Widgets describe what the UI should look like.</p><pre><code>Container(\n  child: Column(\n    children: [\n      Text("Hello"),\n      ElevatedButton(\n        onPressed: () {},\n        child: Text("Click me"),\n      ),\n    ],\n  ),\n)</code></pre><h2>Layout Widgets</h2><p>Row, Column, Stack, Expanded, Padding, Center.</p>', 'created_by' => $admin->id]);

        $fQuiz1 = Quiz::create(['title' => 'Flutter Basics Quiz', 'type' => 'topic', 'topic_id' => $flutterTopic1->id, 'total_points' => 10, 'pass_percentage' => 50, 'created_by' => $admin->id]);
        $fq1 = Question::create(['quiz_id' => $fQuiz1->id, 'question_text' => 'What language does Flutter use?', 'points' => 10]);
        Answer::create(['question_id' => $fq1->id, 'answer_text' => 'Dart', 'is_correct' => true]);
        Answer::create(['question_id' => $fq1->id, 'answer_text' => 'Swift', 'is_correct' => false]);
        Answer::create(['question_id' => $fq1->id, 'answer_text' => 'Java', 'is_correct' => false]);

        $fQuiz2 = Quiz::create(['title' => 'Widgets Quiz', 'type' => 'topic', 'topic_id' => $flutterTopic2->id, 'total_points' => 10, 'pass_percentage' => 50, 'created_by' => $admin->id]);
        $fq2 = Question::create(['quiz_id' => $fQuiz2->id, 'question_text' => 'In Flutter, everything is a...', 'points' => 10]);
        Answer::create(['question_id' => $fq2->id, 'answer_text' => 'Widget', 'is_correct' => true]);
        Answer::create(['question_id' => $fq2->id, 'answer_text' => 'Component', 'is_correct' => false]);

        $flutterCourse = Course::create(['title' => 'Flutter Development', 'description' => 'Build beautiful cross-platform apps with Flutter', 'created_by' => $admin->id]);
        $flutterCourse->topics()->attach([$flutterTopic1->id => ['order' => 1], $flutterTopic2->id => ['order' => 2]]);
        Quiz::create(['title' => 'Flutter Final Exam', 'type' => 'course', 'course_id' => $flutterCourse->id, 'total_points' => 10, 'pass_percentage' => 50, 'created_by' => $admin->id]);

        $mobileTrack->courses()->attach([
            $mobileCourse->id => ['order' => 1],
            $flutterCourse->id => ['order' => 2],
        ]);

        $dataTrack = Track::create([
            'title' => 'Data Science Track',
            'description' => 'Master data analysis, machine learning, and AI with Python',
            'created_by' => $admin->id,
        ]);
        // ── Extra Data Course: Machine Learning ──
        $mlTopic1 = Topic::create(['title' => 'What is Machine Learning?', 'type' => 'article', 'content' => '<h1>Machine Learning</h1><p>ML is a subset of AI where systems learn from data without being explicitly programmed.</p><h2>Types of ML</h2><ul><li><strong>Supervised Learning</strong>: labeled data (classification, regression)</li><li><strong>Unsupervised Learning</strong>: no labels (clustering, dimensionality reduction)</li><li><strong>Reinforcement Learning</strong>: learning through rewards</li></ul>', 'created_by' => $admin->id]);
        $mlTopic2 = Topic::create(['title' => 'Building Your First Model', 'type' => 'article', 'content' => '<h1>Your First ML Model</h1><p>Using scikit-learn to build a simple classifier:</p><pre><code>from sklearn.tree import DecisionTreeClassifier\nfrom sklearn.model_selection import train_test_split\n\nX_train, X_test, y_train, y_test = train_test_split(X, y)\nmodel = DecisionTreeClassifier()\nmodel.fit(X_train, y_train)\naccuracy = model.score(X_test, y_test)</code></pre><p>Key steps: prepare data → split → train → evaluate → deploy.</p>', 'created_by' => $admin->id]);

        $mlQuiz1 = Quiz::create(['title' => 'ML Basics Quiz', 'type' => 'topic', 'topic_id' => $mlTopic1->id, 'total_points' => 10, 'pass_percentage' => 50, 'created_by' => $admin->id]);
        $mlq1 = Question::create(['quiz_id' => $mlQuiz1->id, 'question_text' => 'Supervised learning uses what kind of data?', 'points' => 10]);
        Answer::create(['question_id' => $mlq1->id, 'answer_text' => 'Labeled data', 'is_correct' => true]);
        Answer::create(['question_id' => $mlq1->id, 'answer_text' => 'Random data', 'is_correct' => false]);

        $mlQuiz2 = Quiz::create(['title' => 'First Model Quiz', 'type' => 'topic', 'topic_id' => $mlTopic2->id, 'total_points' => 10, 'pass_percentage' => 50, 'created_by' => $admin->id]);
        $mlq2 = Question::create(['quiz_id' => $mlQuiz2->id, 'question_text' => 'What is the first step in building an ML model?', 'points' => 10]);
        Answer::create(['question_id' => $mlq2->id, 'answer_text' => 'Prepare data', 'is_correct' => true]);
        Answer::create(['question_id' => $mlq2->id, 'answer_text' => 'Deploy', 'is_correct' => false]);

        $mlCourse = Course::create(['title' => 'Machine Learning Basics', 'description' => 'Introduction to ML concepts and building models', 'created_by' => $admin->id]);
        $mlCourse->topics()->attach([$mlTopic1->id => ['order' => 1], $mlTopic2->id => ['order' => 2]]);
        Quiz::create(['title' => 'ML Final Exam', 'type' => 'course', 'course_id' => $mlCourse->id, 'total_points' => 10, 'pass_percentage' => 50, 'created_by' => $admin->id]);

        $dataTrack->courses()->attach([
            $dataCourse->id => ['order' => 1],
            $mlCourse->id => ['order' => 2],
        ]);

        $gameTrack = Track::create([
            'title' => 'Game Development Track',
            'description' => 'Create games using Unity, Unreal Engine, and game design principles',
            'created_by' => $admin->id,
        ]);
        // ── Extra Game Course: Unity 2D ──
        $u2dTopic1 = Topic::create(['title' => 'Unity 2D Setup', 'type' => 'article', 'content' => '<h1>Getting Started with Unity 2D</h1><p>Unity supports both 2D and 3D game development. For 2D games:</p><ol><li>Create a new 2D project</li><li>Set up your sprites and tilemaps</li><li>Configure the camera for 2D view</li></ol><h2>The Unity Editor</h2><p>Key panels: <strong>Scene</strong> (design), <strong>Game</strong> (preview), <strong>Inspector</strong> (properties), <strong>Hierarchy</strong> (objects tree).</p>', 'created_by' => $admin->id]);
        $u2dTopic2 = Topic::create(['title' => 'Player Movement', 'type' => 'article', 'content' => '<h1>Character Movement in Unity</h1><pre><code>public class PlayerController : MonoBehaviour\n{\n    public float speed = 5f;\n\n    void Update()\n    {\n        float h = Input.GetAxis("Horizontal");\n        float v = Input.GetAxis("Vertical");\n        transform.Translate(new Vector2(h, v) * speed * Time.deltaTime);\n    }\n}</code></pre><h2>Key Concepts</h2><ul><li><code>Time.deltaTime</code> — frame-rate independent movement</li><li><code>Input.GetAxis</code> — smooth input handling</li><li><code>Rigidbody2D</code> — physics-based movement</li></ul>', 'created_by' => $admin->id]);

        $u2dQuiz1 = Quiz::create(['title' => 'Unity 2D Setup Quiz', 'type' => 'topic', 'topic_id' => $u2dTopic1->id, 'total_points' => 10, 'pass_percentage' => 50, 'created_by' => $admin->id]);
        $uq1 = Question::create(['quiz_id' => $u2dQuiz1->id, 'question_text' => 'Which panel shows the game preview in Unity?', 'points' => 10]);
        Answer::create(['question_id' => $uq1->id, 'answer_text' => 'Game panel', 'is_correct' => true]);
        Answer::create(['question_id' => $uq1->id, 'answer_text' => 'Scene panel', 'is_correct' => false]);

        $u2dQuiz2 = Quiz::create(['title' => 'Player Movement Quiz', 'type' => 'topic', 'topic_id' => $u2dTopic2->id, 'total_points' => 10, 'pass_percentage' => 50, 'created_by' => $admin->id]);
        $uq2 = Question::create(['quiz_id' => $u2dQuiz2->id, 'question_text' => 'Time.deltaTime ensures movement is...', 'points' => 10]);
        Answer::create(['question_id' => $uq2->id, 'answer_text' => 'Frame-rate independent', 'is_correct' => true]);
        Answer::create(['question_id' => $uq2->id, 'answer_text' => 'Faster', 'is_correct' => false]);

        $u2dCourse = Course::create(['title' => 'Unity 2D Game Development', 'description' => 'Build your first 2D game from scratch', 'created_by' => $admin->id]);
        $u2dCourse->topics()->attach([$u2dTopic1->id => ['order' => 1], $u2dTopic2->id => ['order' => 2]]);
        Quiz::create(['title' => 'Unity 2D Final Exam', 'type' => 'course', 'course_id' => $u2dCourse->id, 'total_points' => 10, 'pass_percentage' => 50, 'created_by' => $admin->id]);

        $gameTrack->courses()->attach([
            $gameCourse->id => ['order' => 1],
            $u2dCourse->id => ['order' => 2],
        ]);

        echo "\n Database seeded successfully!\n\n";

        $this->command->info(' Admin and Learner accounts created successfully!');
        $this->command->info(' Admin: admin@codemaster.com / password');
        $this->command->info(' Learner: learner@codemaster.com / password123');

        echo " Created:\n";
        echo "   - 4 Tracks (Web, Mobile, Data Science, Game Dev)\n";
        echo "   - 9 Courses\n";
        echo "   - 19 Topics\n";
        echo "   - 28 Quizzes\n";
        echo "   - 32+ Questions\n\n";
    }
}
