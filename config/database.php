<?php
/**
 * Universal Technology Corporation (UTC) - Database Connection & Auto-Setup
 * Uses SQLite via PDO for zero-configuration, robust persistence.
 */

if (session_status() === PHP_SESSION_NONE) {
    $sessionDir = __DIR__ . '/../sessions';
    if (!is_dir($sessionDir)) {
        @mkdir($sessionDir, 0777, true);
    }
    @ini_set('session.save_path', realpath($sessionDir) ?: $sessionDir);
    @session_start();
}

$dbDir = __DIR__ . '/../db';
if (!is_dir($dbDir)) {
    mkdir($dbDir, 0777, true);
}

$dbPath = $dbDir . '/utc_elearning.sqlite';

try {
    $pdo = new PDO("sqlite:" . $dbPath);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    $pdo->exec("PRAGMA foreign_keys = ON;");

    // Initialize Schema
    initDatabase($pdo);
} catch (PDOException $e) {
    die("Database Connection Error: " . $e->getMessage());
}

/**
 * Initializes database tables and populates default courses and accounts if empty
 */
function initDatabase($pdo) {
    // 1. Users Table
    $pdo->exec("CREATE TABLE IF NOT EXISTS users (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        name TEXT NOT NULL,
        email TEXT UNIQUE NOT NULL,
        password TEXT NOT NULL,
        phone TEXT,
        role TEXT NOT NULL DEFAULT 'student', -- 'admin', 'teacher', 'student'
        avatar TEXT,
        bio TEXT,
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP
    )");

    // 2. Courses Table
    $pdo->exec("CREATE TABLE IF NOT EXISTS courses (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        title TEXT NOT NULL,
        slug TEXT UNIQUE NOT NULL,
        category TEXT NOT NULL,
        original_price REAL NOT NULL,
        discount_price REAL NOT NULL,
        discount_percent INTEGER DEFAULT 0,
        short_desc TEXT,
        full_desc TEXT,
        why_choose_utc TEXT,
        thumbnail TEXT,
        duration TEXT DEFAULT '4 Months',
        total_classes INTEGER DEFAULT 36,
        level TEXT DEFAULT 'Beginner to Advanced',
        teacher_id INTEGER,
        status TEXT DEFAULT 'active',
        featured INTEGER DEFAULT 1,
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (teacher_id) REFERENCES users(id) ON DELETE SET NULL
    )");

    // 3. Course Modules
    $pdo->exec("CREATE TABLE IF NOT EXISTS modules (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        course_id INTEGER NOT NULL,
        title TEXT NOT NULL,
        sort_order INTEGER DEFAULT 1,
        FOREIGN KEY (course_id) REFERENCES courses(id) ON DELETE CASCADE
    )");

    // 4. Module Lessons
    $pdo->exec("CREATE TABLE IF NOT EXISTS lessons (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        module_id INTEGER NOT NULL,
        course_id INTEGER NOT NULL,
        title TEXT NOT NULL,
        video_url TEXT,
        duration TEXT DEFAULT '45 mins',
        notes TEXT,
        resource_file TEXT,
        sort_order INTEGER DEFAULT 1,
        FOREIGN KEY (module_id) REFERENCES modules(id) ON DELETE CASCADE,
        FOREIGN KEY (course_id) REFERENCES courses(id) ON DELETE CASCADE
    )");

    // 5. Enrollments Table
    $pdo->exec("CREATE TABLE IF NOT EXISTS enrollments (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        student_id INTEGER NOT NULL,
        course_id INTEGER NOT NULL,
        batch_name TEXT DEFAULT 'UTC Batch 2026-A',
        amount REAL NOT NULL,
        payment_method TEXT NOT NULL, -- 'bKash', 'Nagad', 'Rocket', 'Bank'
        transaction_id TEXT,
        sender_phone TEXT,
        payment_status TEXT DEFAULT 'pending', -- 'pending', 'approved', 'rejected'
        enrolled_at DATETIME DEFAULT CURRENT_TIMESTAMP,
        approved_at DATETIME,
        FOREIGN KEY (student_id) REFERENCES users(id) ON DELETE CASCADE,
        FOREIGN KEY (course_id) REFERENCES courses(id) ON DELETE CASCADE
    )");

    // 6. Assignments
    $pdo->exec("CREATE TABLE IF NOT EXISTS assignments (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        course_id INTEGER NOT NULL,
        title TEXT NOT NULL,
        description TEXT,
        max_marks INTEGER DEFAULT 100,
        due_date DATE,
        created_by INTEGER,
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (course_id) REFERENCES courses(id) ON DELETE CASCADE,
        FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE SET NULL
    )");

    // 7. Submissions
    $pdo->exec("CREATE TABLE IF NOT EXISTS submissions (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        assignment_id INTEGER NOT NULL,
        student_id INTEGER NOT NULL,
        submission_text TEXT,
        file_path TEXT,
        marks_obtained REAL,
        feedback TEXT,
        submitted_at DATETIME DEFAULT CURRENT_TIMESTAMP,
        graded_at DATETIME,
        FOREIGN KEY (assignment_id) REFERENCES assignments(id) ON DELETE CASCADE,
        FOREIGN KEY (student_id) REFERENCES users(id) ON DELETE CASCADE
    )");

    // 8. Progress
    $pdo->exec("CREATE TABLE IF NOT EXISTS course_progress (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        student_id INTEGER NOT NULL,
        lesson_id INTEGER NOT NULL,
        course_id INTEGER NOT NULL,
        completed INTEGER DEFAULT 1,
        updated_at DATETIME DEFAULT CURRENT_TIMESTAMP,
        UNIQUE(student_id, lesson_id),
        FOREIGN KEY (student_id) REFERENCES users(id) ON DELETE CASCADE,
        FOREIGN KEY (lesson_id) REFERENCES lessons(id) ON DELETE CASCADE
    )");

    // 9. Notices
    $pdo->exec("CREATE TABLE IF NOT EXISTS notices (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        title TEXT NOT NULL,
        content TEXT NOT NULL,
        target_role TEXT DEFAULT 'all', -- 'all', 'student', 'teacher'
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP
    )");

    // 10. Coupons
    $pdo->exec("CREATE TABLE IF NOT EXISTS coupons (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        code TEXT UNIQUE NOT NULL,
        discount_percent INTEGER NOT NULL,
        valid_until DATE,
        is_active INTEGER DEFAULT 1
    )");

    // 11. Settings
    $pdo->exec("CREATE TABLE IF NOT EXISTS settings (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        setting_key TEXT UNIQUE NOT NULL,
        setting_value TEXT
    )");

    // Seed default data if users table is empty
    $checkUsers = $pdo->query("SELECT COUNT(*) FROM users")->fetchColumn();
    if ($checkUsers == 0) {
        seedInitialData($pdo);
    }
}

/**
 * Seeds initial users, 10 official courses, modules, lessons, and notices
 */
function seedInitialData($pdo) {
    // Seed Users
    $password = password_hash('123456', PASSWORD_DEFAULT);
    $adminPassword = password_hash('admin123', PASSWORD_DEFAULT);
    $teacherPassword = password_hash('teacher123', PASSWORD_DEFAULT);
    $studentPassword = password_hash('student123', PASSWORD_DEFAULT);

    $stmt = $pdo->prepare("INSERT INTO users (name, email, password, phone, role, bio) VALUES (?, ?, ?, ?, ?, ?)");
    
    // 1. Admin
    $stmt->execute([
        'UTC Super Admin',
        'admin@utc.edu.bd',
        $adminPassword,
        '01812669757',
        'admin',
        'Head Administrator & Academic Director at Universal Technology Corporation (UTC).'
    ]);
    $adminId = $pdo->lastInsertId();

    // 2. Teachers
    $stmt->execute([
        'Engr. Tanvir Ahmed',
        'teacher@utc.edu.bd',
        $teacherPassword,
        '01817122483',
        'teacher',
        'Senior Software Engineer & Lead Instructor with 8+ years experience in Full-stack Web, Python & Mobile App Development.'
    ]);
    $teacher1Id = $pdo->lastInsertId();

    $stmt->execute([
        'Sarwar Hossain (CEH)',
        'sarwar@utc.edu.bd',
        $teacherPassword,
        '01812669757',
        'teacher',
        'Cybersecurity Consultant & Certified Ethical Hacker (CEH v11) with expertise in Network Security and Cloud Infrastructure.'
    ]);
    $teacher2Id = $pdo->lastInsertId();

    // 3. Demo Student
    $stmt->execute([
        'Rahim Chowdhury',
        'student@utc.edu.bd',
        $studentPassword,
        '01899123456',
        'student',
        'Aspiring Full-Stack Software Developer studying Computer Science at Feni.'
    ]);
    $studentId = $pdo->lastInsertId();

    // Common "কেন UTC বেছে নেবেন?" Description for all courses
    $whyUtc = "🌟 কেন UTC বেছে নেবেন?
✔️ আন্তর্জাতিক মানের কোর্স মডিউল
✔️ দক্ষ ও প্রফেশনাল ট্রেনারের মাধ্যমে ক্লাস
✔️ প্রতিটি ক্লাসের ভিডিও রেকর্ড প্রদান
✔️ Industrial Training-এর সুযোগ
✔️ ২৪ ঘণ্টা Online & Offline Support
✔️ Lifetime Free Repeat Facility
✔️ কোর্স শেষে Professional Certificate প্রদান
✔️ Industry-oriented Practical Learning
সঠিক গাইডলাইন + প্র্যাকটিক্যাল শিক্ষা = সফল প্রোগ্রামার হওয়ার পথ
✨ Learn Today | Skill Up Tomorrow | Build Your Career with UTC ✨";

    // 10 Courses Specified by the User
    $courses = [
        [
            'title' => 'Programming Combo Pack (Algorithm, Programming Language C, Java, Python, JavaScript)',
            'slug' => 'programming-combo-pack',
            'category' => 'Software & Programming',
            'original_price' => 20000,
            'discount_price' => 10000,
            'discount_percent' => 50,
            'short_desc' => 'Algorithm এবং চারটি শীর্ষ প্রোগ্রামিং ল্যাঙ্গুয়েজ (C, Java, Python, JS) একটি অনন্য কম্বো প্যাকে শিখুন ৫০% ছাড়ে!',
            'full_desc' => "GOOD NEWS! GOOD NEWS! GOOD NEWS! 🔥
Programming শেখার সেরা সুযোগ – এখন ৫০% ডিসকাউন্টে!

💻 প্রোগ্রামিং ক্যারিয়ারের শক্ত ভিত্তি গড়তে চান?
একটি কোর্সেই শিখে নিন Algorithm এবং চারটি জনপ্রিয় প্রোগ্রামিং ল্যাঙ্গুয়েজ। সফটওয়্যার ডেভেলপমেন্ট, ওয়েব ডেভেলপমেন্ট, Freelancing কিংবা চাকরির প্রস্তুতি, সবকিছুর জন্য প্রোগ্রামিং-এর বেসিক শক্তিশালী হওয়া জরুরি।

🚀 UTC নিয়ে এসেছে Programming Combo Pack
📘 Course Name: Programming Combo Pack
✅ Algorithm & Problem Solving
✅ Programming Language C
✅ Java OOP & Core Concepts
✅ Python for Real-World Development
✅ JavaScript Modern ES6+ & DOM

💰 Course Fee: ২০,০০০/- টাকা
🎁 ৫০% স্পেশাল ডিসকাউন্ট
👉 এখন আপনাকে দিতে হবে মাত্র ১০,০০০/- টাকা!

" . $whyUtc . "

⏰ ৫০% ডিসকাউন্টের এই অফার সীমিত সময়ের জন্য!
তাই দেরি না করে আজই যোগাযোগ করুন এবং আপনার সিট নিশ্চিত করুন।",
            'duration' => '5 Months',
            'total_classes' => 45,
            'level' => 'Beginner to Advanced',
            'teacher_id' => $teacher1Id,
            'thumbnail' => 'course_programming_combo.jpg'
        ],
        [
            'title' => 'Advance Web Design Using Html5, Css3, Sass, Tailwind Css & VanillaJS with project & web Technology',
            'slug' => 'advance-web-design-html-css-sass-tailwind-js',
            'category' => 'Web Design & Frontend',
            'original_price' => 7000,
            'discount_price' => 4900,
            'discount_percent' => 30,
            'short_desc' => 'HTML5, CSS3, SASS, Tailwind CSS এবং Vanilla JavaScript দিয়ে আধুনিক, রেসপনসিভ ও ইন্টারঅ্যাক্টিভ ওয়েব ডিজাইন শিখুন।',
            'full_desc' => "GOOD NEWS! GOOD NEWS! GOOD NEWS! 🔥
আধুনিক ওয়েব ডিজাইন শেখার সেরা সুযোগ – এখন ৩০% ডিসকাউন্টে!

💻 ফ্রন্টএন্ড ওয়েব ডিজাইনার হিসেবে ক্যারিয়ার গড়তে চান?
আজকের টেক ইন্ডাস্ট্রিতে সুন্দর, দ্রুত ও রেসপনসিভ ওয়েবসাইটের চাহিদা তুঙ্গে। এই কোর্সে আপনি বেসিক থেকে শুরু করে আধুনিক ফ্রন্টএন্ড ফ্রেমওয়ার্ক ও টুলস নিয়ে প্র্যাকটিক্যাল প্রজেক্টসহ কাজ শিখবেন।

🚀 Course Name: Advance Web Design Using Html5, Css3, Sass, Tailwind Css & VanillaJS with project & web Technology
✅ HTML5 Semantic Structure & SEO Best Practices
✅ CSS3 Modern Layouts (Flexbox, Grid, Animations, 3D Transforms)
✅ SASS / SCSS Preprocessor for Professional Workflow
✅ Tailwind CSS Utility-First Responsive Styling
✅ Vanilla JavaScript DOM Manipulation, ES6+, Async & Events
✅ 5+ Real-world Live Landing Pages & Dashboard Projects

💰 Course Fee: ৭,০০০/- টাকা
🎁 ৩০% স্পেশাল ডিসকাউন্ট
👉 এখন আপনাকে দিতে হবে মাত্র ৪,৯০০/- টাকা!

" . $whyUtc . "

⏰ ৩০% ডিসকাউন্টের এই অফার সীমিত সময়ের জন্য!
তাই দেরি না করে আজই যোগাযোগ করুন এবং আপনার সিট নিশ্চিত করুন।",
            'duration' => '3.5 Months',
            'total_classes' => 32,
            'level' => 'Beginner to Intermediate',
            'teacher_id' => $teacher1Id,
            'thumbnail' => 'course_web_design.jpg'
        ],
        [
            'title' => 'Algorithm, Web Design, Development (PHP & JS), Databases (MySql, PDO, SQLite, MongoDB) with project, APIs & web Technology with Freelancing',
            'slug' => 'web-development-php-js-databases-freelancing',
            'category' => 'Full-Stack Web Development',
            'original_price' => 24000,
            'discount_price' => 16800,
            'discount_percent' => 30,
            'short_desc' => 'PHP, JavaScript, ৪টি ডেটাবেস (MySQL, PDO, SQLite, MongoDB), REST API ও ফ্রিল্যান্সিং গাইডলাইনসহ পূর্ণাঙ্গ কোর্স।',
            'full_desc' => "GOOD NEWS! GOOD NEWS! GOOD NEWS! 🔥
ফুলস্ট্যাক ওয়েব ডেভেলপমেন্ট ও ফ্রিল্যান্সিং মাস্টারক্লাস – এখন ৩০% ডিসকাউন্টে!

💻 প্রফেশনাল ওয়েব ডেভেলপার হয়ে মার্কেটপ্লেস ও লোকাল ইন্ডাস্ট্রিতে নিজের ক্যারিয়ার গড়ুন!
ওয়েব অ্যাপ্লিকেশন তৈরি, মাল্টি-ডেটাবেজ আর্কিটেকচার, এপিআই ইন্টিগ্রেশন এবং ফ্রিল্যান্সিং সাকসেস সিক্রেটস শিখুন একটি মাত্র কোর্সে।

🚀 Course Highlights:
✅ Core Algorithm & Logic Building
✅ Responsive Web Design (Tailwind & Modern CSS)
✅ Server-side Development with PHP & Modern Vanilla JS
✅ 4 Databases in Depth: MySQL, PDO, SQLite & MongoDB NoSQL
✅ RESTful API Development, JSON & Ajax
✅ Complete E-Commerce & Management System Live Projects
✅ Freelancing Masterclass (Upwork, Fiverr, International Client Handling)

💰 Course Fee: ২৪,০০০/- টাকা
🎁 ৩০% স্পেশাল ডিসকাউন্ট
👉 এখন আপনাকে দিতে হবে মাত্র ১৬,৮০০/- টাকা!

" . $whyUtc . "

⏰ ৩০% ডিসকাউন্টের এই অফার সীমিত সময়ের জন্য!
তাই দেরি না করে আজই যোগাযোগ করুন এবং আপনার সিট নিশ্চিত করুন।",
            'duration' => '5 Months',
            'total_classes' => 45,
            'level' => 'Intermediate to Advanced',
            'teacher_id' => $teacher1Id,
            'thumbnail' => 'course_fullstack_php.jpg'
        ],
        [
            'title' => 'Algorithm, Web Design & Development (PHP, JS & Laravel Framework), Web Technology with Freelancing',
            'slug' => 'laravel-framework-web-development-freelancing',
            'category' => 'Full-Stack Web Development',
            'original_price' => 30000,
            'discount_price' => 21000,
            'discount_percent' => 30,
            'short_desc' => 'Laravel ফ্রেমওয়ার্ক দিয়ে এন্টারপ্রাইজ গ্রেড ওয়েব অ্যাপ্লিকেশন ডেভেলপমেন্ট, REST API ও ফ্রিল্যান্সিং শিখুন।',
            'full_desc' => "GOOD NEWS! GOOD NEWS! GOOD NEWS! 🔥
Laravel ফ্রেমওয়ার্ক মাস্টারক্লাস – এখন ৩০% ডিসকাউন্টে!

💻 PHP-এর সবচেয়ে জনপ্রিয় ফ্রেমওয়ার্ক Laravel দিয়ে হাই-স্যালারি জব বা ফ্রিল্যান্সিং করতে চান?
লারাবেলের MVC আর্কিটেকচার, Eloquent ORM, Authentication, Payment Gateway Integration ও রিয়েল প্রজেক্ট নিয়ে সম্পূর্ণ প্র্যাকটিক্যাল গাইড।

🚀 Course Highlights:
✅ Data Structure, Algorithm & Clean Code Principles
✅ Advanced PHP OOP Concepts
✅ Laravel Framework (Routing, Controllers, Blade, Eloquent, Migrations)
✅ Multi-auth, Roles & Permissions (Spatie)
✅ Payment Gateway (bKash, Nagad, Stripe, SSLCommerz)
✅ REST API for Mobile/Frontend Applications
✅ Freelancing Guidelines & Portfolio Building

💰 Course Fee: ৩০,০০০/- টাকা
🎁 ৩০% স্পেশাল ডিসকাউন্ট
👉 এখন আপনাকে দিতে হবে মাত্র ২১,০০০/- টাকা!

" . $whyUtc . "

⏰ ৩০% ডিসকাউন্টের এই অফার সীমিত সময়ের জন্য!
তাই দেরি না করে আজই যোগাযোগ করুন এবং আপনার সিট নিশ্চিত করুন।",
            'duration' => '6 Months',
            'total_classes' => 52,
            'level' => 'Intermediate to Advanced',
            'teacher_id' => $teacher1Id,
            'thumbnail' => 'course_laravel.jpg'
        ],
        [
            'title' => 'Algorithm, Web Design & Development (JS & MERN Stack ), Web Technology with Freelancing',
            'slug' => 'mern-stack-web-development-freelancing',
            'category' => 'Full-Stack Web Development',
            'original_price' => 30000,
            'discount_price' => 21000,
            'discount_percent' => 30,
            'short_desc' => 'MongoDB, Express.js, React.js এবং Node.js দিয়ে আধুনিক MERN স্ট্যাক ওয়েব অ্যাপ্লিকেশন ডেভেলপমেন্ট।',
            'full_desc' => "GOOD NEWS! GOOD NEWS! GOOD NEWS! 🔥
MERN Stack স্পেশালাইজেশন কোর্স – এখন ৩০% ডিসকাউন্টে!

💻 জাভাস্ক্রিপ্ট প্রেমীদের জন্য সেরা ক্যারিয়ার চয়েস MERN স্ট্যাক!
একটি মাত্র ল্যাঙ্গুয়েজ দিয়ে ফ্রন্টএন্ড এবং ব্যাকএন্ড উভয় সাইডেই পারদর্শী হয়ে উঠুন। গ্লোবাল মার্কেটে সবচেয়ে চাহিদাসম্পন্ন স্ট্যাক।

🚀 Course Highlights:
✅ Advanced JavaScript (ES6+, Closures, Async/Await, Prototypes)
✅ React.js (Hooks, State Management, Redux Toolkit, Context API)
✅ Node.js & Express.js Backend Server Architecture
✅ MongoDB Database Design with Mongoose
✅ JWT Authentication, Security & Deployment (Vercel, Render)
✅ Full-stack Projects: Social Media & Real-time Chat App
✅ Upwork, Fiverr & Remote Job Preparation

💰 Course Fee: ৩০,০০০/- টাকা
🎁 ৩০% স্পেশাল ডিসকাউন্ট
👉 এখন আপনাকে দিতে হবে মাত্র ২১,০০০/- টাকা!

" . $whyUtc . "

⏰ ৩০% ডিসকাউন্টের এই অফার সীমিত সময়ের জন্য!
তাই দেরি না করে আজই যোগাযোগ করুন এবং আপনার সিট নিশ্চিত করুন।",
            'duration' => '6 Months',
            'total_classes' => 50,
            'level' => 'Intermediate to Advanced',
            'teacher_id' => $teacher1Id,
            'thumbnail' => 'course_mern_stack.jpg'
        ],
        [
            'title' => 'Algorithm, Web Design & Development Using python Django framework With API & Web Technology',
            'slug' => 'python-django-web-development-api',
            'category' => 'Python & Web Development',
            'original_price' => 30000,
            'discount_price' => 21000,
            'discount_percent' => 30,
            'short_desc' => 'Python ও Django ফ্রেমওয়ার্ক দিয়ে সিকিউর এবং স্কেলেবল ওয়েব অ্যাপ্লিকেশন ও REST API তৈরি শিখুন।',
            'full_desc' => "GOOD NEWS! GOOD NEWS! GOOD NEWS! 🔥
পাইথন ও জ্যাঙ্গো ফ্রেমওয়ার্ক ডেভেলপমেন্ট – এখন ৩০% ডিসকাউন্টে!

💻 পাইথনের ক্ষমতা ও জ্যাঙ্গোর সিকিউরিটি দিয়ে রোবাস্ট ব্যাকএন্ড তৈরি করতে চান?
জ্যাঙ্গোর বিল্ট-ইন অ্যাডমিন, ওআরএম এবং জ্যাঙ্গো রেস্ট ফ্রেমওয়ার্ক (DRF) দিয়ে এন্টারপ্রাইজ ব্যাকএন্ড আর্কিটেকচার শিখুন।

🚀 Course Highlights:
✅ Core Python & OOP Concepts
✅ Algorithms & Data Structures in Python
✅ Django Framework Fundamentals & MTV Architecture
✅ Django ORM, Models, Migrations & Querysets
✅ Django REST Framework (DRF) for Full API Creation
✅ Token Authentication, Permissions & Middleware
✅ Scalable E-commerce Project & Cloud Deployment

💰 Course Fee: ৩০,০০০/- টাকা
🎁 ৩০% স্পেশাল ডিসকাউন্ট
👉 এখন আপনাকে দিতে হবে মাত্র ২১,০০০/- টাকা!

" . $whyUtc . "

⏰ ৩০% ডিসকাউন্টের এই অফার সীমিত সময়ের জন্য!
তাই দেরি না করে আজই যোগাযোগ করুন এবং আপনার সিট নিশ্চিত করুন।",
            'duration' => '5.5 Months',
            'total_classes' => 48,
            'level' => 'Intermediate to Advanced',
            'teacher_id' => $teacher1Id,
            'thumbnail' => 'course_python_django.jpg'
        ],
        [
            'title' => 'CompTIA A+ Hardware & Networking with CCNA & MTCNA',
            'slug' => 'comptia-a-plus-hardware-networking-ccna-mtcna',
            'category' => 'Hardware & Networking',
            'original_price' => 15000,
            'discount_price' => 10500,
            'discount_percent' => 30,
            'short_desc' => 'কম্পিউটার হার্ডওয়্যার ট্রাবলশুটিং, সিসকো নেটওয়ার্কিং (CCNA) ও মিক্রোটিক (MTCNA) এর বাস্তবমুখী কোর্স।',
            'full_desc' => "GOOD NEWS! GOOD NEWS! GOOD NEWS! 🔥
হার্ডওয়্যার, সিসকো ও মিক্রোটিক নেটওয়ার্কিং – এখন ৩০% ডিসকাউন্টে!

💻 নেটওয়ার্ক ইঞ্জিনিয়ার ও সিস্টেম অ্যাডমিনিস্ট্রেটর হিসেবে সফল ক্যারিয়ার তৈরি করুন!
আইটি সাপোর্ট, সিসকো রাউটিং/সুইচিং এবং মিক্রোটিক কনফিগারেশনের পূর্ণাঙ্গ হ্যান্ডস-অন ল্যাব প্র্যাকটিস।

🚀 Course Highlights:
✅ CompTIA A+ PC Hardware, Assembly, Troubleshooting & OS
✅ Computer Networking Fundamentals (OSI, TCP/IP, Subnetting)
✅ Cisco CCNA (Routing, Switching, VLAN, NAT, Access Control Lists)
✅ MikroTik MTCNA RouterOS Setup, Bandwidth Management & Queues
✅ Firewall Configuration, VPN Setup & Network Troubleshooting
✅ Hands-on Hardware & Real Router Lab Facilities in UTC Feni

💰 Course Fee: ১৫,০০০/- টাকা
🎁 ৩০% স্পেশাল ডিসকাউন্ট
👉 এখন আপনাকে দিতে হবে মাত্র ১০,৫০০/- টাকা!

" . $whyUtc . "

⏰ ৩০% ডিসকাউন্টের এই অফার সীমিত সময়ের জন্য!
তাই দেরি না করে আজই যোগাযোগ করুন এবং আপনার সিট নিশ্চিত করুন।",
            'duration' => '4 Months',
            'total_classes' => 36,
            'level' => 'Beginner to Advanced',
            'teacher_id' => $teacher2Id,
            'thumbnail' => 'course_networking_ccna.jpg'
        ],
        [
            'title' => 'Algorithm, Advanced Dart, Flutter framework android & ios app with Web Development & Technology',
            'slug' => 'flutter-dart-android-ios-mobile-app-development',
            'category' => 'Mobile App Development',
            'original_price' => 30000,
            'discount_price' => 21000,
            'discount_percent' => 30,
            'short_desc' => 'গুগলের ফ্লাটার ও ডার্ট দিয়ে একটি কোডবেইজ থেকে অ্যান্ড্রয়েড ও আইওএস উভয় প্ল্যাটফর্মের প্রফেশনাল অ্যাপ বানান।',
            'full_desc' => "GOOD NEWS! GOOD NEWS! GOOD NEWS! 🔥
ফ্লাটার ক্রস-প্ল্যাটফর্ম মোবাইল অ্যাপ ডেভেলপমেন্ট – এখন ৩০% ডিসকাউন্টে!

💻 মোবাইল অ্যাপ ইন্ডাস্ট্রিতে ক্যারিয়ার গড়ার সবচেয়ে ট্রেন্ডি মাধ্যম Google Flutter!
ডার্ট ল্যাঙ্গুয়েজ, স্টেট ম্যানেজমেন্ট, ফায়ারবেস ও রেস্ট এপিআই ইন্টিগ্রেশন দিয়ে প্লে স্টোর ও অ্যাপ স্টোর রেডি মোবাইল অ্যাপ তৈরি করুন।

🚀 Course Highlights:
✅ Logic & Algorithms with Dart Programming
✅ Flutter Widget Tree, UI Design, Animations
✅ State Management (Bloc / Provider / Riverpod)
✅ Firebase Auth, Firestore Database & Push Notifications
✅ REST API Integration with Dio / Http
✅ Google Maps, Camera, Local Storage (Hive/SQLite)
✅ Play Store & App Store Publishing Guidelines

💰 Course Fee: ৩০,০০০/- টাকা
🎁 ৩০% স্পেশাল ডিসকাউন্ট
👉 এখন আপনাকে দিতে হবে মাত্র ২১,০০০/- টাকা!

" . $whyUtc . "

⏰ ৩০% ডিসকাউন্টের এই অফার সীমিত সময়ের জন্য!
তাই দেরি না করে আজই যোগাযোগ করুন এবং আপনার সিট নিশ্চিত করুন।",
            'duration' => '6 Months',
            'total_classes' => 50,
            'level' => 'Intermediate to Advanced',
            'teacher_id' => $teacher1Id,
            'thumbnail' => 'course_flutter_app.jpg'
        ],
        [
            'title' => 'Algorithm, Core & Advanced python Programming with BigData for Data Analysis',
            'slug' => 'python-bigdata-data-analysis',
            'category' => 'Data Science & Big Data',
            'original_price' => 30000,
            'discount_price' => 21000,
            'discount_percent' => 30,
            'short_desc' => 'পাইথন প্রোগ্রামিং, বিগ ডেটা প্রসেসিং, Pandas, NumPy, Matplotlib এবং ডেটা অ্যানালাইসিসের সম্পূর্ণ পথ।',
            'full_desc' => "GOOD NEWS! GOOD NEWS! GOOD NEWS! 🔥
পাইথন ও বিগ ডেটা অ্যানালাইসিস মাস্টারক্লাস – এখন ৩০% ডিসকাউন্টে!

💻 ডেটা অ্যানালিস্ট বা ডেটা সায়েন্টিস্ট হয়ে ভবিষ্যতের সবচেয়ে লাভজনক সেক্টরে পদার্পণ করুন!
বিশাল ডেটাসেট বিশ্লেষণ, ভিজ্যুয়ালাইজেশন, ইনসাইটস এক্সট্র্যাকশন ও মেশিন লার্নিং প্রি-প্রসেসিং শিখুন।

🚀 Course Highlights:
✅ Algorithms, Math for Data & Advanced Python
✅ Data Wrangling with Pandas & NumPy
✅ Data Visualization with Matplotlib & Seaborn
✅ SQL for Data Analysis & Business Intelligence
✅ Introduction to Big Data concepts, PySpark & Hadoop ecosystem
✅ Real-world Business Data Analysis Case Studies
✅ Interactive Dashboards with Streamlit

💰 Course Fee: ৩০,০০০/- টাকা
🎁 ৩০% স্পেশাল ডিসকাউন্ট
👉 এখন আপনাকে দিতে হবে মাত্র ২১,০০০/- টাকা!

" . $whyUtc . "

⏰ ৩০% ডিসকাউন্টের এই অফার সীমিত সময়ের জন্য!
তাই দেরি না করে আজই যোগাযোগ করুন এবং আপনার সিট নিশ্চিত করুন।",
            'duration' => '5.5 Months',
            'total_classes' => 48,
            'level' => 'Intermediate to Advanced',
            'teacher_id' => $teacher1Id,
            'thumbnail' => 'course_python_bigdata.jpg'
        ],
        [
            'title' => 'Certification Ethical Hacking (CEHv11)',
            'slug' => 'certification-ethical-hacking-ceh-v11',
            'category' => 'Cyber Security & Ethical Hacking',
            'original_price' => 80000,
            'discount_price' => 56000,
            'discount_percent' => 30,
            'short_desc' => 'আন্তর্জাতিক মানের সাইবার সিকিউরিটি ও সার্টিফাইড এথিক্যাল হ্যাকার (CEHv11) পরীক্ষার পূর্ণাঙ্গ প্রস্তুতি ও হ্যান্ডস-অন ল্যাব।',
            'full_desc' => "GOOD NEWS! GOOD NEWS! GOOD NEWS! 🔥
আন্তর্জাতিক সার্টিফাইড এথিক্যাল হ্যাকিং (CEHv11) – এখন ৩০% ডিসকাউন্টে!

💻 সাইবার সিকিউরিটি এক্সপার্ট হয়ে ব্যাংক, ফাইন্যান্সিয়াল প্রতিষ্ঠান ও আন্তর্জাতিক সংস্থায় সিকিউরিটি অ্যানালিস্ট হিসেবে কাজ করুন!
পেনিট্রেশন টেস্টিং, সিস্টেম হ্যাকিং, ম্যালওয়্যার অ্যানালাইসিস, ওয়েব অ্যাপ সিকিউরিটি ও লাইভ ল্যাব অ্যাটাক-ডিফেন্স সিমুলেশন।

🚀 Course Highlights:
✅ Introduction to Ethical Hacking & Cyber Threat Landscape
✅ Footprinting & Reconnaissance Techniques
✅ Scanning Networks, Vulnerability Analysis (Nessus, Nmap)
✅ System Hacking, Privilege Escalation & Steganography
✅ Malware Threats, Trojans, Viruses & Worms
✅ Web Application Hacking & SQL Injection (OWASP Top 10)
✅ Wireless Network Security, Cloud Computing Hacking & Cryptography
✅ Live Capture The Flag (CTF) Practice & CEH Exam Preparation

💰 Course Fee: ৮০,০০০/- টাকা
🎁 ৩০% স্পেশাল ডিসকাউন্ট
👉 এখন আপনাকে দিতে হবে মাত্র ৫৬,০০০/- টাকা!

" . $whyUtc . "

⏰ ৩০% ডিসকাউন্টের এই অফার সীমিত সময়ের জন্য!
তাই দেরি না করে আজই যোগাযোগ করুন এবং আপনার সিট নিশ্চিত করুন।",
            'duration' => '6 Months',
            'total_classes' => 55,
            'level' => 'Professional / Advanced',
            'teacher_id' => $teacher2Id,
            'thumbnail' => 'course_ethical_hacking.jpg'
        ]
    ];

    $insertCourse = $pdo->prepare("INSERT INTO courses (
        title, slug, category, original_price, discount_price, discount_percent, short_desc, full_desc, why_choose_utc, duration, total_classes, level, teacher_id, status, featured, thumbnail
    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'active', 1, ?)");

    $insertModule = $pdo->prepare("INSERT INTO modules (course_id, title, sort_order) VALUES (?, ?, ?)");
    $insertLesson = $pdo->prepare("INSERT INTO lessons (module_id, course_id, title, video_url, duration, notes, sort_order) VALUES (?, ?, ?, ?, ?, ?, ?)");

    foreach ($courses as $c) {
        $insertCourse->execute([
            $c['title'],
            $c['slug'],
            $c['category'],
            $c['original_price'],
            $c['discount_price'],
            $c['discount_percent'],
            $c['short_desc'],
            $c['full_desc'],
            $whyUtc,
            $c['duration'],
            $c['total_classes'],
            $c['level'],
            $c['teacher_id'],
            $c['thumbnail']
        ]);
        $courseId = $pdo->lastInsertId();

        // Seed Sample Modules & Lessons for this course
        $modulesData = [
            'Module 1: Orientation & Fundamentals',
            'Module 2: Core Concepts & Practice Exercises',
            'Module 3: Advanced Architectures & Real-world Project',
            'Module 4: Industry Standards, Testing & Freelancing'
        ];

        foreach ($modulesData as $idx => $modTitle) {
            $insertModule->execute([$courseId, $modTitle, $idx + 1]);
            $modId = $pdo->lastInsertId();

            // Insert 2 lessons per module
            $insertLesson->execute([
                $modId,
                $courseId,
                "Class " . (($idx * 2) + 1) . ": Introduction & Setup for " . substr($c['title'], 0, 30),
                "https://www.youtube.com/embed/dQw4w9WgXcQ", // Demo embed
                "1 hr 15 mins",
                "স্বাগতম ক্লাসে! আজকের ক্লাসে বেসিক পরিবেশ সেটআপ এবং কনসেপ্ট আলোচনা করা হয়েছে। প্রয়োজনীয় কোড এবং রিসোর্স নিচে দেওয়া হলো।",
                1
            ]);

            $insertLesson->execute([
                $modId,
                $courseId,
                "Class " . (($idx * 2) + 2) . ": Practical Implementation & Exercise",
                "https://www.youtube.com/embed/dQw4w9WgXcQ",
                "1 hr 30 mins",
                "বাস্তব প্রজেক্ট কোডিং এবং সমস্যা সমাধান। গিটহাব রিপোজিটরিতে কোড পুশ করা হয়েছে।",
                2
            ]);
        }
    }

    // Seed Sample Enrollment for demo student
    $pdo->prepare("INSERT INTO enrollments (
        student_id, course_id, batch_name, amount, payment_method, transaction_id, sender_phone, payment_status, enrolled_at, approved_at
    ) VALUES (
        ?, 1, 'UTC Batch 2026-A', 10000, 'bKash', 'TRX99887766BK', '01899123456', 'approved', CURRENT_TIMESTAMP, CURRENT_TIMESTAMP
    )")->execute([$studentId]);

    // Seed Sample Assignment
    $pdo->prepare("INSERT INTO assignments (
        course_id, title, description, max_marks, due_date, created_by
    ) VALUES (
        1, 'Assignment 01: Algorithm & Problem Solving in C', 'Implement Bubble Sort and Binary Search algorithm in C language with time complexity analysis. Submit your GitHub link and screenshots.', 100, date('now', '+7 days'), ?
    )")->execute([$teacher1Id]);

    // Seed Sample Notices
    $noticeStmt = $pdo->prepare("INSERT INTO notices (title, content, target_role) VALUES (?, ?, ?)");
    $noticeStmt->execute([
        '🎉 নতুন ব্যাচে ভর্তি চলছে - বিশেষ ৫০% ও ৩০% ডিসকাউন্ট!',
        'ইউনিভার্সাল টেকনোলজি কর্পোরেশন (UTC) এর সকল প্রফেশনাল ও আইটি কোর্সে সীমিত সময়ের জন্য স্পেশাল অফার চলছে। দ্রুত আপনার সিট কনফার্ম করুন। যোগাযোগ: 01812669757, 01817122483।',
        'all'
    ]);
    $noticeStmt->execute([
        '📢 আগামী শুক্রবার লাইভ ক্যারিয়ার সেমিনার',
        'আইটি ও সফটওয়্যার ইন্ডাস্ট্রিতে সফল ক্যারিয়ার গড়ার বিষয়ে ডক্টর পাড়া, ফেনী ক্যাম্পাসে সরাসরি ফ্রি সেমিনার অনুষ্ঠিত হবে।',
        'student'
    ]);

    // Seed Coupons
    $couponStmt = $pdo->prepare("INSERT INTO coupons (code, discount_percent, valid_until, is_active) VALUES (?, ?, ?, 1)");
    $couponStmt->execute(['UTC50', 50, date('Y-12-31')]);
    $couponStmt->execute(['UTC30', 30, date('Y-12-31')]);

    // Seed Default Settings
    $settings = [
        'institute_name' => 'Universal Technology Corporation (UTC)',
        'institute_short' => 'UTC',
        'address' => '287 (1st Floor), Doctor Para, Feni-3900',
        'phone_primary' => '01812669757',
        'phone_secondary' => '01817122483',
        'email' => 'utcsoftltd@gmail.com',
        'bkash_number' => '01812669757 (Merchant/Personal)',
        'nagad_number' => '01817122483 (Personal)',
        'rocket_number' => '01812669757-9',
        'bank_info' => 'Islami Bank Bangladesh Ltd, Feni Branch, A/C: 20501234567890, A/C Name: Universal Technology Corporation',
        'tagline' => 'Learn Today | Skill Up Tomorrow | Build Your Career with UTC',
        'hero_title' => 'দক্ষতা অর্জন করুন বিশ্বমানের আইটি প্রতিষ্ঠানে',
        'hero_subtitle' => 'Universal Technology Corporation (UTC) নিয়ে এলো ইন্ডাস্ট্রিয়াল প্রফেশনাল ট্রেনিং ও লাইভ প্রজেক্ট বেসড ক্যারিয়ার ট্র্যাক।'
    ];

    $setStmt = $pdo->prepare("INSERT INTO settings (setting_key, setting_value) VALUES (?, ?)");
    foreach ($settings as $k => $v) {
        $setStmt->execute([$k, $v]);
    }
}
