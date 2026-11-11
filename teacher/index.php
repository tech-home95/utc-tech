<?php
/**
 * Universal Technology Corporation (UTC) - Teacher Portal Dashboard
 */

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/auth.php';
require_once __DIR__ . '/../config/helpers.php';

requireRole('teacher');
$user = getCurrentUser();

// Fetch Teacher Courses
$stmt = $pdo->prepare("SELECT * FROM courses WHERE teacher_id = ? ORDER BY id ASC");
$stmt->execute([$user['id']]);
$courses = $stmt->fetchAll();

$courseIds = array_column($courses, 'id');
$inQuery = !empty($courseIds) ? implode(',', array_map('intval', $courseIds)) : '0';

// Total enrolled students in teacher's courses
$enrStmt = $pdo->query("SELECT COUNT(*) FROM enrollments WHERE course_id IN ($inQuery) AND payment_status = 'approved'");
$totalStudents = $enrStmt->fetchColumn();

// Pending submissions to review
$subStmt = $pdo->query("SELECT s.*, a.title as assignment_title, u.name as student_name, c.title as course_title
                        FROM submissions s
                        JOIN assignments a ON s.assignment_id = a.id
                        JOIN courses c ON a.course_id = c.id
                        JOIN users u ON s.student_id = u.id
                        WHERE a.course_id IN ($inQuery) AND s.marks_obtained IS NULL
                        ORDER BY s.id DESC LIMIT 5");
$pendingSubmissions = $subStmt->fetchAll();

$pageTitle = 'Teacher Portal Dashboard | UTC';
require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/navbar.php';
?>

<div class="py-10 bg-slate-50 dark:bg-[#080d1a] min-h-[calc(100vh-80px)] transition-colors duration-300">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8">
        
        <!-- Welcome Banner -->
        <div class="p-8 rounded-3xl bg-gradient-to-r from-emerald-900 via-teal-950 to-slate-900 text-white relative overflow-hidden shadow-2xl">
            <div class="absolute -right-10 -bottom-10 w-64 h-64 bg-emerald-500/20 rounded-full blur-3xl pointer-events-none"></div>
            <div class="relative z-10 flex flex-col md:flex-row items-start md:items-center justify-between gap-6">
                <div class="space-y-2">
                    <span class="px-3 py-1 rounded-full text-xs font-bold bg-emerald-500/30 text-emerald-300 border border-emerald-400/30">
                        Instructor & Academic Portal
                    </span>
                    <h1 class="text-2xl sm:text-3xl font-extrabold tracking-tight">
                        স্বাগতম, <?= sanitize($user['name']) ?>! 🎓
                    </h1>
                    <p class="text-xs sm:text-sm text-slate-300">
                        Universal Technology Corporation (UTC) ট্রেনার ড্যাশবোর্ডে আপনার ক্লাস ও শিক্ষার্থীদের অগ্রগতি পর্যালোচনা করুন।
                    </p>
                </div>
                <div class="flex items-center gap-3">
                    <a href="assignments.php" class="px-5 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white text-xs font-bold shadow-md transition flex items-center gap-2">
                        <i data-lucide="plus-circle" class="w-4 h-4"></i> নতুন অ্যাসাইনমেন্ট
                    </a>
                </div>
            </div>
        </div>

        <?= renderFlash() ?>

        <!-- Key Metrics -->
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-6">
            <div class="p-6 rounded-2xl glass-panel border border-slate-200 dark:border-slate-800 space-y-2">
                <div class="flex items-center justify-between text-blue-600 dark:text-cyan-400">
                    <span class="text-xs font-bold uppercase tracking-wider">অ্যাসাইন করা কোর্স</span>
                    <i data-lucide="book-open" class="w-5 h-5"></i>
                </div>
                <div class="text-3xl font-black text-slate-900 dark:text-white"><?= count($courses) ?></div>
                <p class="text-[11px] text-slate-500">আপনার তত্ত্বাবধানে পরিচালিত কোর্স</p>
            </div>

            <div class="p-6 rounded-2xl glass-panel border border-slate-200 dark:border-slate-800 space-y-2">
                <div class="flex items-center justify-between text-emerald-600 dark:text-emerald-400">
                    <span class="text-xs font-bold uppercase tracking-wider">মোট শিক্ষার্থী</span>
                    <i data-lucide="users" class="w-5 h-5"></i>
                </div>
                <div class="text-3xl font-black text-slate-900 dark:text-white"><?= $totalStudents ?></div>
                <p class="text-[11px] text-slate-500">অনুমোদিত অ্যাক্টিভ শিক্ষার্থী</p>
            </div>

            <div class="p-6 rounded-2xl glass-panel border border-slate-200 dark:border-slate-800 space-y-2">
                <div class="flex items-center justify-between text-amber-600 dark:text-amber-400">
                    <span class="text-xs font-bold uppercase tracking-wider">মূল্যায়ন অপেক্ষমাণ সাবমিশন</span>
                    <i data-lucide="clipboard-list" class="w-5 h-5"></i>
                </div>
                <div class="text-3xl font-black text-slate-900 dark:text-white"><?= count($pendingSubmissions) ?></div>
                <p class="text-[11px] text-slate-500">মার্কস ও ফিডব্যাক প্রদান করুন</p>
            </div>
        </div>

        <!-- Courses & Pending Submissions Grid -->
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
            
            <!-- Left 7 Cols: My Courses -->
            <div class="lg:col-span-7 space-y-4">
                <div class="flex items-center justify-between">
                    <h2 class="text-lg font-bold text-slate-900 dark:text-white flex items-center gap-2">
                        <i data-lucide="book-marked" class="w-5 h-5 text-emerald-500"></i>
                        <span>আমার পাঠদানকৃত কোর্সসমূহ</span>
                    </h2>
                    <a href="courses.php" class="text-xs font-bold text-emerald-600 dark:text-emerald-400 hover:underline">
                        সব কোর্স &rarr;
                    </a>
                </div>

                <div class="space-y-3">
                    <?php foreach ($courses as $c): ?>
                        <div class="p-5 rounded-2xl glass-panel border border-slate-200 dark:border-slate-800 flex items-center justify-between gap-4">
                            <div>
                                <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-blue-100 dark:bg-blue-900/60 text-blue-700 dark:text-blue-300">
                                    <?= sanitize($c['category']) ?>
                                </span>
                                <h3 class="text-sm font-bold text-slate-900 dark:text-white mt-1"><?= sanitize($c['title']) ?></h3>
                                <p class="text-xs text-slate-500 mt-0.5"><?= $c['total_classes'] ?> টি ক্লাস • ফি: <?= formatBDT($c['discount_price']) ?></p>
                            </div>

                            <div class="flex items-center gap-2 shrink-0">
                                <a href="lessons.php?course_id=<?= $c['id'] ?>" class="px-3.5 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white text-xs font-bold transition flex items-center gap-1.5">
                                    <i data-lucide="layers" class="w-3.5 h-3.5"></i> লেসন ম্যানেজ
                                </a>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- Right 5 Cols: Pending Submissions -->
            <div class="lg:col-span-5 space-y-4">
                <h2 class="text-lg font-bold text-slate-900 dark:text-white flex items-center gap-2">
                    <i data-lucide="clock" class="w-5 h-5 text-amber-500"></i>
                    <span>পেন্ডিং অ্যাসাইনমেন্ট রিভিউ</span>
                </h2>

                <div class="p-6 rounded-3xl glass-panel border border-slate-200 dark:border-slate-800 space-y-3">
                    <?php if (empty($pendingSubmissions)): ?>
                        <p class="text-xs text-slate-500 text-center py-6">কোনো পেন্ডিং সাবমিশন নেই! সব কাজ মূল্যায়িত হয়েছে।</p>
                    <?php else: ?>
                        <?php foreach ($pendingSubmissions as $sub): ?>
                            <div class="p-3.5 rounded-2xl bg-slate-50 dark:bg-slate-800/60 border border-slate-100 dark:border-slate-800 text-xs space-y-2">
                                <div class="flex items-center justify-between">
                                    <span class="font-bold text-slate-900 dark:text-white"><?= sanitize($sub['student_name']) ?></span>
                                    <span class="text-[10px] text-slate-400"><?= date('d M, Y', strtotime($sub['submitted_at'])) ?></span>
                                </div>
                                <p class="text-[11px] text-blue-600 dark:text-cyan-400 font-medium truncate"><?= sanitize($sub['assignment_title']) ?></p>
                                <a href="submissions.php?assignment_id=<?= $sub['assignment_id'] ?>" class="text-[11px] font-bold text-emerald-600 hover:underline block pt-1">
                                    গ্রেডিং ও ফিডব্যাক দিন &rarr;
                                </a>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>

        </div>

    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
