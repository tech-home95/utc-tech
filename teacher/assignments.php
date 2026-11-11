<?php
/**
 * Universal Technology Corporation (UTC) - Teacher Assignments Management
 */

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/auth.php';
require_once __DIR__ . '/../config/helpers.php';

requireRole('teacher');
$user = getCurrentUser();

// Handle Create Assignment
if (($_SERVER['REQUEST_METHOD'] ?? '') === 'POST' && isset($_POST['create_assignment'])) {
    $courseId = (int)$_POST['course_id'];
    $title = sanitize($_POST['title'] ?? '');
    $description = sanitize($_POST['description'] ?? '');
    $maxMarks = (int)($_POST['max_marks'] ?? 100);
    $dueDate = sanitize($_POST['due_date'] ?? date('Y-m-d', strtotime('+7 days')));

    if (!empty($title) && $courseId > 0) {
        $ins = $pdo->prepare("INSERT INTO assignments (course_id, title, description, max_marks, due_date, created_by) VALUES (?, ?, ?, ?, ?, ?)");
        $ins->execute([$courseId, $title, $description, $maxMarks, $dueDate, $user['id']]);
        setFlash('success', 'নতুন অ্যাসাইনমেন্ট সফলভাবে তৈরি করা হয়েছে!');
        header("Location: assignments.php");
        exit;
    }
}

// Fetch teacher's courses
$cStmt = $pdo->prepare("SELECT id, title FROM courses WHERE teacher_id = ?");
$cStmt->execute([$user['id']]);
$courses = $cStmt->fetchAll();

// Fetch all assignments with submission count
$stmt = $pdo->prepare("SELECT a.*, c.title as course_title,
                              (SELECT COUNT(*) FROM submissions WHERE assignment_id = a.id) as total_submissions,
                              (SELECT COUNT(*) FROM submissions WHERE assignment_id = a.id AND marks_obtained IS NULL) as pending_reviews
                       FROM assignments a
                       JOIN courses c ON a.course_id = c.id
                       WHERE c.teacher_id = ?
                       ORDER BY a.id DESC");
$stmt->execute([$user['id']]);
$assignments = $stmt->fetchAll();

$pageTitle = 'অ্যাসাইনমেন্ট ও প্রজেক্ট মূল্যায়ন | Teacher Portal | UTC';
require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/navbar.php';
?>

<div class="py-12 bg-slate-50 dark:bg-[#080d1a] min-h-[calc(100vh-80px)] transition-colors duration-300">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8">
        
        <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 pb-6 border-b border-slate-200 dark:border-slate-800">
            <div>
                <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 dark:text-white">
                    কোর্স অ্যাসাইনমেন্ট ও মূল্যায়ন
                </h1>
                <p class="text-xs sm:text-sm text-slate-500 mt-1">
                    শিক্ষার্থীদের জন্য প্র্যাকটিক্যাল কোডিং টাস্ক দিন এবং জমা দেওয়া প্রজেক্টের নম্বর প্রদান করুন।
                </p>
            </div>
        </div>

        <?= renderFlash() ?>

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
            
            <!-- Left 7 Cols: Assignments List -->
            <div class="lg:col-span-7 space-y-4">
                <h2 class="text-lg font-bold text-slate-900 dark:text-white">
                    সক্রিয় অ্যাসাইনমেন্টসমূহ
                </h2>

                <?php if (empty($assignments)): ?>
                    <div class="p-8 rounded-3xl glass-panel border border-slate-200 dark:border-slate-800 text-center text-xs text-slate-500">
                        এখনো কোনো অ্যাসাইনমেন্ট তৈরি করা হয়নি। ডানপাশের ফর্ম থেকে নতুন অ্যাসাইনমেন্ট তৈরি করুন।
                    </div>
                <?php else: ?>
                    <div class="space-y-4">
                        <?php foreach ($assignments as $asg): ?>
                            <div class="p-6 rounded-3xl glass-panel border border-slate-200 dark:border-slate-800 space-y-3 hover:border-emerald-500/40 transition">
                                <div class="flex items-start justify-between gap-2">
                                    <div>
                                        <span class="px-2.5 py-0.5 rounded-md text-[10px] font-bold bg-blue-100 dark:bg-blue-900/60 text-blue-700 dark:text-blue-300">
                                            <?= sanitize($asg['course_title']) ?>
                                        </span>
                                        <h3 class="text-base font-bold text-slate-900 dark:text-white mt-1">
                                            <?= sanitize($asg['title']) ?>
                                        </h3>
                                        <p class="text-xs text-slate-500 mt-0.5"><?= sanitize($asg['description']) ?></p>
                                    </div>
                                    <span class="px-3 py-1 rounded-full text-xs font-bold bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 shrink-0">
                                        পূর্ণমান: <?= $asg['max_marks'] ?>
                                    </span>
                                </div>

                                <div class="pt-3 border-t border-slate-100 dark:border-slate-800 flex items-center justify-between text-xs">
                                    <div class="space-x-2 text-slate-500">
                                        <span>জমা পড়েছে: <strong><?= $asg['total_submissions'] ?> টি</strong></span>
                                        <span>•</span>
                                        <span class="text-amber-600 font-bold">পেন্ডিং রিভিউ: <?= $asg['pending_reviews'] ?> টি</span>
                                    </div>
                                    <a href="submissions.php?assignment_id=<?= $asg['id'] ?>" class="px-4 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white font-bold transition flex items-center gap-1.5 shadow-md">
                                        <i data-lucide="check-square" class="w-3.5 h-3.5"></i> সাবমিশন ও মূল্যায়ন (<?= $asg['total_submissions'] ?>)
                                    </a>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Right 5 Cols: Create Assignment Form -->
            <div class="lg:col-span-5">
                <div class="p-8 rounded-3xl glass-panel border border-slate-200 dark:border-slate-800 shadow-xl space-y-4">
                    <h3 class="text-base font-bold text-slate-900 dark:text-white flex items-center gap-2 pb-2 border-b border-slate-100 dark:border-slate-800">
                        <i data-lucide="plus-circle" class="w-5 h-5 text-emerald-500"></i>
                        <span>নতুন অ্যাসাইনমেন্ট প্রকাশ করুন</span>
                    </h3>

                    <form method="POST" class="space-y-4 text-xs">
                        <div>
                            <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">কোর্স নির্বাচন করুন *</label>
                            <select name="course_id" required class="w-full px-3 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white text-xs">
                                <?php foreach ($courses as $c): ?>
                                    <option value="<?= $c['id'] ?>"><?= sanitize($c['title']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div>
                            <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">অ্যাসাইনমেন্ট শিরোনাম *</label>
                            <input type="text" name="title" required placeholder="যেমন: Assignment 02: Build Responsive Portfolio" class="w-full px-3 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white text-xs">
                        </div>

                        <div>
                            <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">টাস্ক নির্দেশনা ও বিবরণ *</label>
                            <textarea name="description" rows="3" required placeholder="শিক্ষার্থীদের কী কোডিং করতে হবে, কোন ফ্রেমওয়ার্ক ব্যবহার করতে হবে এবং গিটহাব লিংক জমার নির্দেশাবলী..." class="w-full px-3 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white text-xs"></textarea>
                        </div>

                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">সর্বোচ্চ নম্বর (Marks)</label>
                                <input type="number" name="max_marks" value="100" class="w-full px-3 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white text-xs">
                            </div>
                            <div>
                                <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">জমা দেওয়ার শেষ তারিখ</label>
                                <input type="date" name="due_date" value="<?= date('Y-m-d', strtotime('+7 days')) ?>" class="w-full px-3 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white text-xs">
                            </div>
                        </div>

                        <button type="submit" name="create_assignment" class="w-full py-3.5 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white font-bold transition shadow-lg shadow-emerald-500/25">
                            অ্যাসাইনমেন্ট প্রকাশ করুন
                        </button>
                    </form>
                </div>
            </div>

        </div>

    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
