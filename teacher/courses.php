<?php
/**
 * Universal Technology Corporation (UTC) - Teacher Courses
 */

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/auth.php';
require_once __DIR__ . '/../config/helpers.php';

requireRole('teacher');
$user = getCurrentUser();

$stmt = $pdo->prepare("SELECT c.*, 
                              (SELECT COUNT(*) FROM modules WHERE course_id = c.id) as total_modules,
                              (SELECT COUNT(*) FROM lessons WHERE course_id = c.id) as uploaded_lessons,
                              (SELECT COUNT(*) FROM enrollments WHERE course_id = c.id AND payment_status = 'approved') as enrolled_count
                       FROM courses c 
                       WHERE c.teacher_id = ? 
                       ORDER BY c.id ASC");
$stmt->execute([$user['id']]);
$courses = $stmt->fetchAll();

$pageTitle = 'কোর্স তালিকা ও সিলেবাস পরিচালনা | Teacher Portal | UTC';
require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/navbar.php';
?>

<div class="py-12 bg-slate-50 dark:bg-[#080d1a] min-h-[calc(100vh-80px)] transition-colors duration-300">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8">
        
        <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 pb-6 border-b border-slate-200 dark:border-slate-800">
            <div>
                <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 dark:text-white">
                    আমার পরিচালিত কোর্সসমূহ
                </h1>
                <p class="text-xs sm:text-sm text-slate-500 mt-1">
                    কোর্সের মডিউল তৈরি, নতুন ভিডিও লেকচার আপলোড এবং রিসোর্স ফাইল সংযুক্ত করুন।
                </p>
            </div>
            <a href="assignments.php" class="px-5 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white text-xs font-bold shadow-md transition flex items-center gap-2">
                <i data-lucide="clipboard-plus" class="w-4 h-4"></i> নতুন অ্যাসাইনমেন্ট তৈরি করুন
            </a>
        </div>

        <?= renderFlash() ?>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            <?php foreach ($courses as $c): ?>
                <div class="p-6 rounded-3xl glass-panel border border-slate-200 dark:border-slate-800 flex flex-col justify-between space-y-4 shadow-lg hover:border-emerald-500/40 transition">
                    <div class="space-y-3">
                        <div class="flex items-center justify-between">
                            <span class="px-2.5 py-0.5 rounded-md text-[10px] font-bold bg-blue-100 dark:bg-blue-900/60 text-blue-700 dark:text-blue-300">
                                <?= sanitize($c['category']) ?>
                            </span>
                            <span class="text-xs font-bold text-emerald-600">
                                <?= $c['enrolled_count'] ?> জন শিক্ষার্থী
                            </span>
                        </div>

                        <h3 class="text-base font-bold text-slate-900 dark:text-white leading-snug">
                            <?= sanitize($c['title']) ?>
                        </h3>

                        <div class="grid grid-cols-2 gap-2 pt-2 border-t border-slate-100 dark:border-slate-800 text-[11px] text-slate-500">
                            <div>মডিউল: <strong><?= $c['total_modules'] ?> টি</strong></div>
                            <div>আপলোডকৃত ক্লাস: <strong><?= $c['uploaded_lessons'] ?> টি</strong></div>
                            <div>সময়কাল: <strong><?= sanitize($c['duration']) ?></strong></div>
                            <div>কোর্স ফি: <strong><?= formatBDT($c['discount_price']) ?></strong></div>
                        </div>
                    </div>

                    <div class="pt-4 border-t border-slate-100 dark:border-slate-800 flex items-center gap-2">
                        <a href="lessons.php?course_id=<?= $c['id'] ?>" class="flex-1 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white text-xs font-bold text-center shadow-md transition flex items-center justify-center gap-1.5">
                            <i data-lucide="layers" class="w-3.5 h-3.5"></i> লেসন ও ক্লাস ম্যানেজ
                        </a>
                        <a href="../course-details.php?id=<?= $c['id'] ?>" target="_blank" class="px-3 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 hover:bg-slate-100 dark:hover:bg-slate-800 text-xs text-slate-600 dark:text-slate-300" title="ওয়েবসাইটে প্রিভিউ">
                            <i data-lucide="external-link" class="w-3.5 h-3.5"></i>
                        </a>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
