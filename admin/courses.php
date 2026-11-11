<?php
/**
 * Universal Technology Corporation (UTC) - Admin Courses Management
 */

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/auth.php';
require_once __DIR__ . '/../config/helpers.php';

requireRole('admin');
$user = getCurrentUser();

// Handle Delete Course
if (isset($_GET['delete_course'])) {
    $delId = (int)$_GET['delete_course'];
    $pdo->prepare("DELETE FROM courses WHERE id = ?")->execute([$delId]);
    setFlash('success', 'কোর্সটি সফলভাবে মুছে ফেলা হয়েছে।');
    header("Location: courses.php");
    exit;
}

// Fetch all courses with teacher name & student count
$stmt = $pdo->query("SELECT c.*, u.name as teacher_name,
                            (SELECT COUNT(*) FROM enrollments WHERE course_id = c.id AND payment_status = 'approved') as student_count,
                            (SELECT COUNT(*) FROM lessons WHERE course_id = c.id) as lesson_count
                     FROM courses c
                     LEFT JOIN users u ON c.teacher_id = u.id
                     ORDER BY c.id ASC");
$courses = $stmt->fetchAll();

$pageTitle = 'কোর্স ম্যানেজমেন্ট | Admin Panel | UTC';
require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/navbar.php';
?>

<div class="py-12 bg-slate-50 dark:bg-[#080d1a] min-h-[calc(100vh-80px)] transition-colors duration-300">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8">
        
        <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 pb-6 border-b border-slate-200 dark:border-slate-800">
            <div>
                <a href="index.php" class="text-xs font-bold text-purple-600 dark:text-purple-400 hover:underline flex items-center gap-1 mb-1">
                    <i data-lucide="arrow-left" class="w-3.5 h-3.5"></i> অ্যাডমিন ড্যাশবোর্ড
                </a>
                <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 dark:text-white">
                    সকল আইটি কোর্স ব্যবস্থাপনা (<?= count($courses) ?> টি কোর্স)
                </h1>
                <p class="text-xs sm:text-sm text-slate-500 mt-1">
                    কোর্সের শিরোনাম, রেগুলার ফি, ডিসকাউন্ট ফি, ট্রেনার অ্যাসাইনমেন্ট ও কারিকুলাম কন্ট্রোল।
                </p>
            </div>
            <a href="course-edit.php?action=create" class="px-5 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-500 text-white text-xs font-bold shadow-md transition flex items-center gap-2">
                <i data-lucide="plus-circle" class="w-4 h-4"></i> নতুন কোর্স যোগ করুন
            </a>
        </div>

        <?= renderFlash() ?>

        <div class="glass-panel rounded-3xl border border-slate-200 dark:border-slate-800 overflow-hidden shadow-2xl">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead>
                        <tr class="bg-slate-100/70 dark:bg-slate-800/60 border-b border-slate-200 dark:border-slate-800 text-slate-500 uppercase tracking-wider">
                            <th class="py-4 px-6">আইডি</th>
                            <th class="py-4 px-6">কোর্সের শিরোনাম ও ক্যাটাগরি</th>
                            <th class="py-4 px-6">নিয়মিত ফি</th>
                            <th class="py-4 px-6">অফার ফি</th>
                            <th class="py-4 px-6">ট্রেনার</th>
                            <th class="py-4 px-6">শিক্ষার্থী</th>
                            <th class="py-4 px-6">ক্লাস সংখ্যা</th>
                            <th class="py-4 px-6 text-right">অ্যাকশন</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-800 text-slate-700 dark:text-slate-300">
                        <?php foreach ($courses as $c): ?>
                            <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-800/30 transition">
                                <td class="py-4 px-6 font-mono font-bold text-slate-400">
                                    #<?= $c['id'] ?>
                                </td>
                                <td class="py-4 px-6 max-w-xs">
                                    <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-blue-100 dark:bg-blue-900/60 text-blue-700 dark:text-blue-300">
                                        <?= sanitize($c['category']) ?>
                                    </span>
                                    <h3 class="font-bold text-slate-900 dark:text-white mt-1 leading-snug">
                                        <a href="../course-details.php?id=<?= $c['id'] ?>" target="_blank" class="hover:text-blue-600 dark:hover:text-cyan-400">
                                            <?= sanitize($c['title']) ?>
                                        </a>
                                    </h3>
                                </td>
                                <td class="py-4 px-6 text-slate-400 line-through">
                                    <?= formatBDT($c['original_price']) ?>
                                </td>
                                <td class="py-4 px-6 font-black text-blue-600 dark:text-cyan-400 text-sm">
                                    <?= formatBDT($c['discount_price']) ?>
                                </td>
                                <td class="py-4 px-6 font-medium">
                                    <?= sanitize($c['teacher_name'] ?? 'Not Assigned') ?>
                                </td>
                                <td class="py-4 px-6 font-bold text-slate-900 dark:text-white">
                                    <?= $c['student_count'] ?> জন
                                </td>
                                <td class="py-4 px-6">
                                    <?= $c['lesson_count'] ?> / <?= $c['total_classes'] ?> টি
                                </td>
                                <td class="py-4 px-6 text-right space-x-1">
                                    <a href="course-edit.php?id=<?= $c['id'] ?>" class="px-2.5 py-1.5 rounded-lg bg-slate-200 dark:bg-slate-700 hover:bg-blue-600 hover:text-white text-slate-700 dark:text-slate-200 font-bold transition inline-block">
                                        এডিট
                                    </a>
                                    <a href="courses.php?delete_course=<?= $c['id'] ?>" onclick="return confirm('এই কোর্সটি মুছে ফেলতে চান?')" class="px-2 py-1.5 rounded-lg bg-rose-500/10 hover:bg-rose-600 hover:text-white text-rose-600 font-bold transition inline-block">
                                        মুছুন
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>

    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
