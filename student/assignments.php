<?php
/**
 * Universal Technology Corporation (UTC) - Student Assignments
 */

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/auth.php';
require_once __DIR__ . '/../config/helpers.php';

requireRole('student');
$user = getCurrentUser();

// Fetch assignments for student's enrolled courses
$stmt = $pdo->prepare("SELECT a.*, c.title as course_title, s.id as submission_id, s.submission_text, s.marks_obtained, s.feedback, s.submitted_at
                       FROM assignments a
                       JOIN enrollments e ON a.course_id = e.course_id
                       JOIN courses c ON a.course_id = c.id
                       LEFT JOIN submissions s ON a.id = s.assignment_id AND s.student_id = ?
                       WHERE e.student_id = ? AND e.payment_status = 'approved'
                       ORDER BY a.id DESC");
$stmt->execute([$user['id'], $user['id']]);
$assignments = $stmt->fetchAll();

$pageTitle = 'আমার অ্যাসাইনমেন্ট ও মূল্যায়ন | Student Portal | UTC';
require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/navbar.php';
?>

<div class="py-12 bg-slate-50 dark:bg-[#080d1a] min-h-[calc(100vh-80px)] transition-colors duration-300">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8">
        
        <div class="pb-6 border-b border-slate-200 dark:border-slate-800">
            <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 dark:text-white">
                কোর্স অ্যাসাইনমেন্ট ও প্রজেক্ট মূল্যায়ন
            </h1>
            <p class="text-xs sm:text-sm text-slate-500 mt-1">
                বাস্তব প্রজেক্ট কোডিং সম্পন্ন করে গিটহাব রিপোজিটরি লিংক জমা দিন এবং ট্রেনারের গ্রেডিং দেখুন।
            </p>
        </div>

        <?= renderFlash() ?>

        <?php if (empty($assignments)): ?>
            <div class="p-12 rounded-3xl glass-panel border border-slate-200 dark:border-slate-800 text-center space-y-3">
                <div class="w-14 h-14 rounded-2xl bg-slate-100 dark:bg-slate-800 text-slate-400 flex items-center justify-center mx-auto">
                    <i data-lucide="clipboard-check" class="w-7 h-7"></i>
                </div>
                <h3 class="text-base font-bold text-slate-800 dark:text-white">বর্তমানে কোনো অ্যাসাইনমেন্ট নেই</h3>
                <p class="text-xs text-slate-500">আপনার নথিভুক্ত কোর্সের ক্লাস চলাকালীন নতুন অ্যাসাইনমেন্ট এখানে যুক্ত হবে।</p>
            </div>
        <?php else: ?>
            <div class="space-y-4">
                <?php foreach ($assignments as $asg): ?>
                    <div class="p-6 rounded-3xl glass-panel border border-slate-200 dark:border-slate-800 shadow-md space-y-4">
                        <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
                            <div>
                                <span class="px-2.5 py-0.5 rounded-md text-[10px] font-bold bg-blue-100 dark:bg-blue-900/60 text-blue-700 dark:text-blue-300">
                                    <?= sanitize($asg['course_title']) ?>
                                </span>
                                <h3 class="text-base font-bold text-slate-900 dark:text-white mt-1">
                                    <?= sanitize($asg['title']) ?>
                                </h3>
                                <p class="text-xs text-slate-500 mt-1">
                                    <?= sanitize($asg['description']) ?>
                                </p>
                            </div>
                            <div class="flex items-center gap-3 shrink-0">
                                <span class="px-3 py-1 rounded-full text-xs font-bold bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 border border-slate-200 dark:border-slate-700">
                                    পূর্ণমান: <?= $asg['max_marks'] ?>
                                </span>
                                <span class="px-3 py-1 rounded-full text-xs font-bold <?= $asg['submission_id'] ? 'bg-emerald-500/10 text-emerald-600 border border-emerald-500/30' : 'bg-amber-500/10 text-amber-600 border border-amber-500/30' ?>">
                                    <?= $asg['submission_id'] ? 'সাবমিট করা হয়েছে ✓' : 'পেন্ডিং' ?>
                                </span>
                            </div>
                        </div>

                        <!-- Submission details or submit action -->
                        <div class="pt-4 border-t border-slate-100 dark:border-slate-800 text-xs">
                            <?php if ($asg['submission_id']): ?>
                                <div class="p-4 rounded-2xl bg-slate-50 dark:bg-slate-800/40 border border-slate-100 dark:border-slate-800 space-y-2">
                                    <p class="text-slate-700 dark:text-slate-300"><strong>জমা দেওয়া লিংক:</strong> <span class="font-mono text-blue-600 dark:text-cyan-400 break-all"><?= sanitize($asg['submission_text']) ?></span></p>
                                    <div class="flex items-center justify-between pt-1">
                                        <span class="text-slate-400 text-[11px]">জমা দেওয়ার তারিখ: <?= date('d M, Y - h:i A', strtotime($asg['submitted_at'])) ?></span>
                                        <?php if ($asg['marks_obtained'] !== null): ?>
                                            <span class="font-bold text-sm text-emerald-600 dark:text-emerald-400">
                                                প্রাপ্ত মার্কস: <?= $asg['marks_obtained'] ?> / <?= $asg['max_marks'] ?>
                                            </span>
                                        <?php else: ?>
                                            <span class="text-amber-500 font-semibold">মূল্যায়ন চলছে</span>
                                        <?php endif; ?>
                                    </div>
                                    <?php if ($asg['feedback']): ?>
                                        <p class="text-[11px] text-slate-600 dark:text-slate-400 pt-1 border-t border-slate-200 dark:border-slate-700">
                                            <strong>শিক্ষকের ফিডব্যাক:</strong> <?= sanitize($asg['feedback']) ?>
                                        </p>
                                    <?php endif; ?>
                                </div>
                            <?php else: ?>
                                <div class="flex items-center justify-between">
                                    <span class="text-slate-500">শেষ সময়: <?= $asg['due_date'] ? date('d M, Y', strtotime($asg['due_date'])) : 'উন্মুক্ত' ?></span>
                                    <a href="learn.php?course_id=<?= $asg['course_id'] ?>" class="px-4 py-2 rounded-xl bg-blue-600 text-white font-bold text-xs hover:bg-blue-500 transition">
                                        ক্লাসরুমে সাবমিট করুন &rarr;
                                    </a>
                                </div>
                            <?php endif; ?>
                        </div>

                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
