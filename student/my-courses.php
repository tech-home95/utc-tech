<?php
/**
 * Universal Technology Corporation (UTC) - Student My Courses
 */

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/auth.php';
require_once __DIR__ . '/../config/helpers.php';

requireRole('student');
$user = getCurrentUser();

$stmt = $pdo->prepare("SELECT e.*, c.title as course_title, c.category, c.thumbnail, c.total_classes, c.duration,
                              c.short_desc
                       FROM enrollments e
                       JOIN courses c ON e.course_id = c.id
                       WHERE e.student_id = ?
                       ORDER BY e.id DESC");
$stmt->execute([$user['id']]);
$enrollments = $stmt->fetchAll();

$pageTitle = 'আমার কোর্সসমূহ | Student Portal | UTC';
require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/navbar.php';
?>

<div class="py-12 bg-slate-50 dark:bg-[#080d1a] min-h-[calc(100vh-80px)] transition-colors duration-300">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8">
        
        <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 pb-6 border-b border-slate-200 dark:border-slate-800">
            <div>
                <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 dark:text-white">
                    আমার নথিভুক্ত কোর্সসমূহ
                </h1>
                <p class="text-xs sm:text-sm text-slate-500 mt-1">
                    Universal Technology Corporation (UTC) ক্লাসরুমে সকল লেকচার, মেটেরিয়াল ও প্রজেক্ট দেখুন।
                </p>
            </div>
            <a href="../courses.php" class="px-5 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-500 text-white text-xs font-bold shadow-md transition flex items-center gap-2">
                <i data-lucide="plus" class="w-4 h-4"></i> নতুন কোর্সে ভর্তি হন
            </a>
        </div>

        <?= renderFlash() ?>

        <?php if (empty($enrollments)): ?>
            <div class="text-center py-20 glass-panel rounded-3xl border border-slate-200 dark:border-slate-800 space-y-4">
                <div class="w-16 h-16 rounded-2xl bg-blue-500/10 text-blue-600 dark:text-cyan-400 flex items-center justify-center mx-auto">
                    <i data-lucide="book-open" class="w-8 h-8"></i>
                </div>
                <h3 class="text-lg font-bold text-slate-800 dark:text-white">কোন কোর্স পাওয়া যায়নি</h3>
                <p class="text-xs text-slate-500 max-w-sm mx-auto">আপনি এখনো কোনো কোর্সে ভর্তি হননি। আজই ৫০% ও ৩০% ডিসকাউন্টে আপনার পছন্দের আইটি কোর্স বেছে নিন।</p>
                <a href="../courses.php" class="inline-block px-6 py-3 rounded-xl bg-blue-600 text-white text-xs font-bold shadow-lg">কোর্স ব্রাউজ করুন</a>
            </div>
        <?php else: ?>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                <?php foreach ($enrollments as $en): ?>
                    <div class="tilt-card card-3d rounded-3xl glass-panel border border-slate-200 dark:border-slate-800 overflow-hidden flex flex-col justify-between shadow-lg hover:border-blue-500/40 transition">
                        
                        <div class="p-6 space-y-4">
                            <div class="flex items-center justify-between">
                                <span class="px-2.5 py-0.5 rounded-md text-[10px] font-bold bg-blue-100 dark:bg-blue-900/60 text-blue-700 dark:text-blue-300">
                                    <?= sanitize($en['category']) ?>
                                </span>
                                <span class="px-2.5 py-0.5 rounded-md text-[10px] font-bold <?= $en['payment_status'] === 'approved' ? 'bg-emerald-500/10 text-emerald-600 border border-emerald-500/20' : 'bg-amber-500/10 text-amber-600 border border-amber-500/20' ?>">
                                    <?= $en['payment_status'] === 'approved' ? '✓ অ্যাক্টিভ' : 'ভেরিফিকেশন চলছে' ?>
                                </span>
                            </div>

                            <h3 class="text-base font-bold text-slate-900 dark:text-white leading-snug">
                                <?= sanitize($en['course_title']) ?>
                            </h3>

                            <p class="text-xs text-slate-500 line-clamp-2">
                                <?= sanitize($en['short_desc']) ?>
                            </p>

                            <div class="pt-2 border-t border-slate-100 dark:border-slate-800 grid grid-cols-2 gap-2 text-[11px] text-slate-500">
                                <div class="flex items-center gap-1.5">
                                    <i data-lucide="video" class="w-3.5 h-3.5 text-blue-500"></i>
                                    <span><?= $en['total_classes'] ?> টি ক্লাস</span>
                                </div>
                                <div class="flex items-center gap-1.5">
                                    <i data-lucide="clock" class="w-3.5 h-3.5 text-emerald-500"></i>
                                    <span><?= sanitize($en['duration']) ?></span>
                                </div>
                            </div>
                        </div>

                        <div class="p-6 bg-slate-50/80 dark:bg-slate-900/50 border-t border-slate-100 dark:border-slate-800 flex items-center justify-between">
                            <span class="text-xs font-semibold text-slate-500">
                                ব্যাচ: <?= sanitize($en['batch_name']) ?>
                            </span>

                            <div class="flex items-center gap-2">
                                <?php if ($en['payment_status'] === 'approved'): ?>
                                    <a href="learn.php?course_id=<?= $en['course_id'] ?>" class="px-4 py-2 rounded-xl bg-blue-600 hover:bg-blue-500 text-white text-xs font-bold shadow-md transition flex items-center gap-1.5">
                                        <i data-lucide="play" class="w-3.5 h-3.5"></i> ক্লাসে ঢুকুন
                                    </a>
                                <?php else: ?>
                                    <a href="../invoice.php?id=<?= $en['id'] ?>" class="px-3 py-2 rounded-xl bg-amber-500 text-slate-950 text-xs font-bold transition">
                                        ইনভয়েস
                                    </a>
                                <?php endif; ?>
                            </div>
                        </div>

                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
