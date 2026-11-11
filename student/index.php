<?php
/**
 * Universal Technology Corporation (UTC) - Student Dashboard
 */

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/auth.php';
require_once __DIR__ . '/../config/helpers.php';

requireRole('student');
$user = getCurrentUser();

// Fetch Student Enrollments
$stmt = $pdo->prepare("SELECT e.*, c.title as course_title, c.category, c.thumbnail, c.total_classes, c.duration
                       FROM enrollments e
                       JOIN courses c ON e.course_id = c.id
                       WHERE e.student_id = ?
                       ORDER BY e.id DESC");
$stmt->execute([$user['id']]);
$enrollments = $stmt->fetchAll();

// Count Approved Enrollments
$activeCoursesCount = 0;
foreach ($enrollments as $en) {
    if ($en['payment_status'] === 'approved') {
        $activeCoursesCount++;
    }
}

// Fetch Pending Assignments
$asStmt = $pdo->prepare("SELECT a.*, c.title as course_title 
                         FROM assignments a
                         JOIN enrollments e ON a.course_id = e.course_id
                         JOIN courses c ON a.course_id = c.id
                         WHERE e.student_id = ? AND e.payment_status = 'approved'
                         ORDER BY a.id DESC LIMIT 4");
$asStmt->execute([$user['id']]);
$assignments = $asStmt->fetchAll();

// Notices
$notices = $pdo->query("SELECT * FROM notices WHERE target_role IN ('all', 'student') ORDER BY id DESC LIMIT 3")->fetchAll();

$pageTitle = 'Student Portal Dashboard | UTC';
require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/navbar.php';
?>

<div class="py-10 bg-slate-50 dark:bg-[#080d1a] min-h-[calc(100vh-80px)] transition-colors duration-300">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8">
        
        <!-- Welcome Greeting Banner -->
        <div class="p-8 rounded-3xl bg-gradient-to-r from-blue-900 via-indigo-900 to-slate-900 text-white relative overflow-hidden shadow-2xl">
            <div class="absolute -right-10 -bottom-10 w-64 h-64 bg-cyan-500/20 rounded-full blur-3xl pointer-events-none"></div>
            <div class="relative z-10 flex flex-col md:flex-row items-start md:items-center justify-between gap-6">
                <div class="space-y-2">
                    <span class="px-3 py-1 rounded-full text-xs font-bold bg-blue-500/30 text-cyan-300 border border-blue-400/30">
                        Student Learning Portal
                    </span>
                    <h1 class="text-2xl sm:text-3xl font-extrabold tracking-tight">
                        স্বাগতম, <?= sanitize($user['name']) ?>! 👋
                    </h1>
                    <p class="text-xs sm:text-sm text-slate-300">
                        Universal Technology Corporation (UTC) - এ আপনার আইটি স্কিল ডেভেলপমেন্ট জার্নি শুরু হোক।
                    </p>
                </div>
                <div class="flex items-center gap-3">
                    <a href="my-courses.php" class="px-5 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-500 text-white text-xs font-bold shadow-md transition flex items-center gap-2">
                        <i data-lucide="play-circle" class="w-4 h-4"></i> আমার ক্লাসরুমে যান
                    </a>
                    <a href="../courses.php" class="px-5 py-2.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-white border border-slate-700 text-xs font-bold transition">
                        নতুন কোর্স ব্রাউজ করুন
                    </a>
                </div>
            </div>
        </div>

        <?= renderFlash() ?>

        <!-- Quick Metrics Overview -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
            <div class="p-6 rounded-2xl glass-panel border border-slate-200 dark:border-slate-800 space-y-2">
                <div class="flex items-center justify-between text-blue-600 dark:text-cyan-400">
                    <span class="text-xs font-bold uppercase tracking-wider">এনরোলকৃত কোর্স</span>
                    <i data-lucide="book-open" class="w-5 h-5"></i>
                </div>
                <div class="text-3xl font-black text-slate-900 dark:text-white"><?= count($enrollments) ?></div>
                <p class="text-[11px] text-slate-500">অনুমোদিত: <?= $activeCoursesCount ?> টি কোর্স</p>
            </div>

            <div class="p-6 rounded-2xl glass-panel border border-slate-200 dark:border-slate-800 space-y-2">
                <div class="flex items-center justify-between text-emerald-600 dark:text-emerald-400">
                    <span class="text-xs font-bold uppercase tracking-wider">চলমান অ্যাসাইনমেন্ট</span>
                    <i data-lucide="file-check" class="w-5 h-5"></i>
                </div>
                <div class="text-3xl font-black text-slate-900 dark:text-white"><?= count($assignments) ?></div>
                <p class="text-[11px] text-slate-500">সাবমিশন ও গ্রেডিং ট্র্যাক করুন</p>
            </div>

            <div class="p-6 rounded-2xl glass-panel border border-slate-200 dark:border-slate-800 space-y-2">
                <div class="flex items-center justify-between text-purple-600 dark:text-purple-400">
                    <span class="text-xs font-bold uppercase tracking-wider">সার্টিফিকেট স্ট্যাটাস</span>
                    <i data-lucide="award" class="w-5 h-5"></i>
                </div>
                <div class="text-3xl font-black text-slate-900 dark:text-white"><?= $activeCoursesCount > 0 ? '1 Ready' : '0' ?></div>
                <p class="text-[11px] text-slate-500">কোর্স শেষে ডিজিটাল সনদ</p>
            </div>

            <div class="p-6 rounded-2xl glass-panel border border-slate-200 dark:border-slate-800 space-y-2">
                <div class="flex items-center justify-between text-amber-600 dark:text-amber-400">
                    <span class="text-xs font-bold uppercase tracking-wider">ক্যাম্পাস সাপোর্ট</span>
                    <i data-lucide="headphones" class="w-5 h-5"></i>
                </div>
                <div class="text-lg font-black text-slate-900 dark:text-white">২৪/৭ একটিভ</div>
                <p class="text-[11px] text-slate-500">হেল্পলাইন: 01812669757</p>
            </div>
        </div>

        <!-- Main Content Area: Enrolled Courses & Side Notices -->
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
            
            <!-- Left 8 Cols: Enrolled Courses -->
            <div class="lg:col-span-8 space-y-6">
                <div class="flex items-center justify-between">
                    <h2 class="text-xl font-bold text-slate-900 dark:text-white flex items-center gap-2">
                        <i data-lucide="graduation-cap" class="w-5 h-5 text-blue-600 dark:text-cyan-400"></i>
                        <span>আমার কোর্সসমূহ (My Enrolled Courses)</span>
                    </h2>
                    <a href="my-courses.php" class="text-xs font-bold text-blue-600 dark:text-cyan-400 hover:underline">
                        সবগুলো দেখুন &rarr;
                    </a>
                </div>

                <?php if (empty($enrollments)): ?>
                    <div class="p-8 rounded-3xl glass-panel border border-slate-200 dark:border-slate-800 text-center space-y-3">
                        <div class="w-12 h-12 rounded-full bg-slate-100 dark:bg-slate-800 text-slate-400 flex items-center justify-center mx-auto">
                            <i data-lucide="book-plus" class="w-6 h-6"></i>
                        </div>
                        <h3 class="text-base font-bold text-slate-800 dark:text-white">আপনি এখনো কোনো কোর্সে ভর্তি হননি</h3>
                        <p class="text-xs text-slate-500">আমাদের জনপ্রিয় প্রোগ্রামিং ও ওয়েব ডেভেলপমেন্ট কোর্সগুলো থেকে পছন্দের কোর্সটি বেছে নিন।</p>
                        <a href="../courses.php" class="inline-block px-5 py-2.5 rounded-xl bg-blue-600 text-white text-xs font-bold shadow-md">
                            কোর্সসমূহ দেখুন
                        </a>
                    </div>
                <?php else: ?>
                    <div class="space-y-4">
                        <?php foreach ($enrollments as $en): ?>
                            <div class="p-6 rounded-3xl glass-panel border border-slate-200 dark:border-slate-800 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-6 hover:border-blue-500/40 transition">
                                <div class="space-y-2">
                                    <div class="flex items-center gap-2">
                                        <span class="px-2.5 py-0.5 rounded-md text-[10px] font-bold bg-blue-100 dark:bg-blue-900/60 text-blue-700 dark:text-blue-300">
                                            <?= sanitize($en['category']) ?>
                                        </span>
                                        <span class="px-2.5 py-0.5 rounded-md text-[10px] font-bold <?= $en['payment_status'] === 'approved' ? 'bg-emerald-100 dark:bg-emerald-950/60 text-emerald-600' : 'bg-amber-100 dark:bg-amber-950/60 text-amber-600' ?>">
                                            <?= $en['payment_status'] === 'approved' ? 'অনুমোদিত (Active)' : 'ভেরিফিকেশন পেন্ডিং' ?>
                                        </span>
                                    </div>
                                    <h3 class="text-base font-bold text-slate-900 dark:text-white leading-snug">
                                        <?= sanitize($en['course_title']) ?>
                                    </h3>
                                    <p class="text-xs text-slate-500">
                                        ব্যাচ: <?= sanitize($en['batch_name']) ?> • মোট ক্লাস: <?= $en['total_classes'] ?> টি • পরিশোধিত: <?= formatBDT($en['amount']) ?>
                                    </p>
                                </div>

                                <div class="flex items-center gap-2 shrink-0">
                                    <?php if ($en['payment_status'] === 'approved'): ?>
                                        <a href="learn.php?course_id=<?= $en['course_id'] ?>" class="px-5 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-500 text-white text-xs font-bold shadow-md transition flex items-center gap-1.5">
                                            <i data-lucide="play" class="w-3.5 h-3.5"></i> ক্লাসরুমে যান
                                        </a>
                                        <a href="certificate.php?course_id=<?= $en['course_id'] ?>" class="px-3 py-2.5 rounded-xl border border-slate-300 dark:border-slate-700 hover:bg-slate-200 dark:hover:bg-slate-800 text-xs font-bold text-slate-700 dark:text-slate-300 transition" title="সার্টিফিকেট দেখুন">
                                            <i data-lucide="award" class="w-4 h-4 text-amber-500"></i>
                                        </a>
                                    <?php else: ?>
                                        <a href="../invoice.php?id=<?= $en['id'] ?>" class="px-4 py-2.5 rounded-xl bg-amber-500 text-slate-950 text-xs font-bold shadow-md transition flex items-center gap-1.5">
                                            <i data-lucide="receipt" class="w-3.5 h-3.5"></i> ইনভয়েস দেখুন
                                        </a>
                                    <?php endif; ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>

            </div>

            <!-- Right 4 Cols: Notices & Quick Shortcuts -->
            <div class="lg:col-span-4 space-y-6">
                
                <!-- Notice Board -->
                <div class="p-6 rounded-3xl glass-panel border border-slate-200 dark:border-slate-800 space-y-4">
                    <h3 class="text-base font-bold text-slate-900 dark:text-white flex items-center gap-2">
                        <i data-lucide="bell" class="w-4 h-4 text-amber-500"></i>
                        <span>অফিসিয়াল নোটিশ বোর্ড</span>
                    </h3>

                    <div class="space-y-3">
                        <?php foreach ($notices as $nt): ?>
                            <div class="p-3.5 rounded-2xl bg-slate-50 dark:bg-slate-800/50 border border-slate-100 dark:border-slate-800 space-y-1">
                                <h4 class="text-xs font-bold text-slate-900 dark:text-white">
                                    <?= sanitize($nt['title']) ?>
                                </h4>
                                <p class="text-[11px] text-slate-500 dark:text-slate-400 leading-relaxed">
                                    <?= sanitize($nt['content']) ?>
                                </p>
                                <span class="text-[10px] text-slate-400 block pt-1">
                                    <?= date('d M, Y', strtotime($nt['created_at'])) ?>
                                </span>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>

                <!-- Campus Help Card -->
                <div class="p-6 rounded-3xl bg-gradient-to-br from-blue-600/10 to-indigo-600/10 border border-blue-500/20 space-y-3">
                    <div class="w-10 h-10 rounded-xl bg-blue-600 text-white flex items-center justify-center font-bold">
                        <i data-lucide="message-square" class="w-5 h-5"></i>
                    </div>
                    <h4 class="font-bold text-slate-900 dark:text-white text-sm">সরাসরি ট্রেনার হেল্প</h4>
                    <p class="text-xs text-slate-500 dark:text-slate-400 leading-relaxed">
                        ল্যাব প্র্যাকটিসে যেকোনো সমস্যা হলে আমাদের ডক্টর পাড়া, ফেনী ক্যাম্পাসে চলে আসুন অথবা হেল্পলাইনে যোগাযোগ করুন।
                    </p>
                    <div class="text-xs font-bold text-blue-600 dark:text-cyan-400 pt-1">
                        📞 01812669757 | 01817122483
                    </div>
                </div>

            </div>

        </div>

    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
