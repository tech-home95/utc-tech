<?php
/**
 * Universal Technology Corporation (UTC) - Teacher Grade Submissions
 */

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/auth.php';
require_once __DIR__ . '/../config/helpers.php';

requireRole('teacher');
$user = getCurrentUser();

$assignmentId = isset($_GET['assignment_id']) ? (int)$_GET['assignment_id'] : 1;

// Fetch assignment details
$aStmt = $pdo->prepare("SELECT a.*, c.title as course_title FROM assignments a JOIN courses c ON a.course_id = c.id WHERE a.id = ?");
$aStmt->execute([$assignmentId]);
$assignment = $aStmt->fetch();

if (!$assignment) {
    header("Location: assignments.php");
    exit;
}

// Handle Grading Submission
if (($_SERVER['REQUEST_METHOD'] ?? '') === 'POST' && isset($_POST['grade_student'])) {
    $subId = (int)$_POST['submission_id'];
    $marks = (float)$_POST['marks_obtained'];
    $feedback = sanitize($_POST['feedback'] ?? '');

    $upd = $pdo->prepare("UPDATE submissions SET marks_obtained = ?, feedback = ?, graded_at = CURRENT_TIMESTAMP WHERE id = ?");
    $upd->execute([$marks, $feedback, $subId]);
    setFlash('success', 'শিক্ষার্থীর গ্রেডিং ও ফিডব্যাক সফলভাবে সংরক্ষণ করা হয়েছে!');
    header("Location: submissions.php?assignment_id=" . $assignmentId);
    exit;
}

// Fetch all submissions for this assignment
$sStmt = $pdo->prepare("SELECT s.*, u.name as student_name, u.email as student_email, u.phone as student_phone
                        FROM submissions s
                        JOIN users u ON s.student_id = u.id
                        WHERE s.assignment_id = ?
                        ORDER BY s.submitted_at DESC");
$sStmt->execute([$assignmentId]);
$submissions = $sStmt->fetchAll();

$pageTitle = 'অ্যাসাইনমেন্ট সাবমিশন গ্রেডিং | UTC';
require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/navbar.php';
?>

<div class="py-12 bg-slate-50 dark:bg-[#080d1a] min-h-[calc(100vh-80px)] transition-colors duration-300">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8">
        
        <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 pb-6 border-b border-slate-200 dark:border-slate-800">
            <div>
                <a href="assignments.php" class="text-xs font-bold text-emerald-600 dark:text-emerald-400 hover:underline flex items-center gap-1 mb-1">
                    <i data-lucide="arrow-left" class="w-3.5 h-3.5"></i> সকল অ্যাসাইনমেন্টে ফিরুন
                </a>
                <h1 class="text-2xl font-extrabold text-slate-900 dark:text-white">
                    <?= sanitize($assignment['title']) ?>
                </h1>
                <p class="text-xs text-slate-500">
                    কোর্স: <?= sanitize($assignment['course_title']) ?> • পূর্ণমান: <?= $assignment['max_marks'] ?>
                </p>
            </div>
            <span class="px-3.5 py-1.5 rounded-full text-xs font-bold bg-blue-100 dark:bg-blue-900/60 text-blue-700 dark:text-blue-300">
                মোট সাবমিশন: <?= count($submissions) ?> টি
            </span>
        </div>

        <?= renderFlash() ?>

        <?php if (empty($submissions)): ?>
            <div class="p-12 rounded-3xl glass-panel border border-slate-200 dark:border-slate-800 text-center space-y-2">
                <p class="text-sm text-slate-500">এখনো কোনো শিক্ষার্থী এই অ্যাসাইনমেন্টটি জমা দেয়নি।</p>
            </div>
        <?php else: ?>
            <div class="space-y-6">
                <?php foreach ($submissions as $sub): ?>
                    <div class="p-6 rounded-3xl glass-panel border border-slate-200 dark:border-slate-800 space-y-4">
                        <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-2 pb-3 border-b border-slate-100 dark:border-slate-800">
                            <div>
                                <h3 class="text-sm font-bold text-slate-900 dark:text-white"><?= sanitize($sub['student_name']) ?></h3>
                                <p class="text-xs text-slate-500"><?= sanitize($sub['student_email']) ?> • <?= sanitize($sub['student_phone'] ?? 'N/A') ?></p>
                            </div>
                            <span class="text-xs text-slate-400">
                                জমা দেওয়ার সময়: <?= date('d M, Y - h:i A', strtotime($sub['submitted_at'])) ?>
                            </span>
                        </div>

                        <!-- Submitted Work content -->
                        <div class="p-4 rounded-2xl bg-slate-50 dark:bg-slate-800/50 border border-slate-100 dark:border-slate-800 text-xs">
                            <span class="font-bold text-slate-400 uppercase text-[10px] block mb-1">শিক্ষার্থীর সাবমিট করা কাজ:</span>
                            <div class="font-mono text-blue-600 dark:text-cyan-400 break-all select-all">
                                <?= sanitize($sub['submission_text']) ?>
                            </div>
                        </div>

                        <!-- Grading Form -->
                        <form method="POST" class="pt-2 grid grid-cols-1 sm:grid-cols-12 gap-3 items-end text-xs">
                            <input type="hidden" name="submission_id" value="<?= $sub['id'] ?>">
                            
                            <div class="sm:col-span-3">
                                <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">প্রাপ্ত নম্বর (Max: <?= $assignment['max_marks'] ?>)</label>
                                <input type="number" step="0.5" max="<?= $assignment['max_marks'] ?>" name="marks_obtained" value="<?= $sub['marks_obtained'] ?? '' ?>" required placeholder="নম্বর লিখুন" class="w-full px-3 py-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white text-xs">
                            </div>

                            <div class="sm:col-span-6">
                                <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">শিক্ষকের মন্তব্য / ফিডব্যাক</label>
                                <input type="text" name="feedback" value="<?= sanitize($sub['feedback'] ?? '') ?>" placeholder="যেমন: চমৎকার কোড স্ট্রাকচার! ক্লিন কোড বজায় রাখুন।" class="w-full px-3 py-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white text-xs">
                            </div>

                            <div class="sm:col-span-3">
                                <button type="submit" name="grade_student" class="w-full py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white font-bold transition shadow-md">
                                    <?= $sub['marks_obtained'] !== null ? 'গ্রেড আপডেট করুন' : 'মার্কস প্রদান করুন' ?>
                                </button>
                            </div>
                        </form>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
