<?php
/**
 * Universal Technology Corporation (UTC) - Teacher Student Roster
 */

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/auth.php';
require_once __DIR__ . '/../config/helpers.php';

requireRole('teacher');
$user = getCurrentUser();

$stmt = $pdo->prepare("SELECT e.*, c.title as course_title, u.name as student_name, u.email as student_email, u.phone as student_phone
                       FROM enrollments e
                       JOIN courses c ON e.course_id = c.id
                       JOIN users u ON e.student_id = u.id
                       WHERE c.teacher_id = ?
                       ORDER BY e.id DESC");
$stmt->execute([$user['id']]);
$students = $stmt->fetchAll();

$pageTitle = 'শিক্ষার্থী তালিকা | Teacher Portal | UTC';
require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/navbar.php';
?>

<div class="py-12 bg-slate-50 dark:bg-[#080d1a] min-h-[calc(100vh-80px)] transition-colors duration-300">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8">
        
        <div class="pb-6 border-b border-slate-200 dark:border-slate-800">
            <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 dark:text-white">
                কোর্স শিক্ষার্থীদের তালিকা (Student Roster)
            </h1>
            <p class="text-xs sm:text-sm text-slate-500 mt-1">
                আপনার পরিচালিত সকল কোর্সের শিক্ষার্থীদের তালিকা ও যোগাযোগ তথ্য।
            </p>
        </div>

        <?= renderFlash() ?>

        <?php if (empty($students)): ?>
            <div class="p-12 rounded-3xl glass-panel border border-slate-200 dark:border-slate-800 text-center text-xs text-slate-500">
                এখনো কোনো শিক্ষার্থী তালিকাভুক্ত হয়নি।
            </div>
        <?php else: ?>
            <div class="glass-panel rounded-3xl border border-slate-200 dark:border-slate-800 overflow-hidden shadow-xl">
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs">
                        <thead>
                            <tr class="bg-slate-100/70 dark:bg-slate-800/60 border-b border-slate-200 dark:border-slate-800 text-slate-500 uppercase tracking-wider">
                                <th class="py-4 px-6">শিক্ষার্থীর নাম</th>
                                <th class="py-4 px-6">ইমেইল ও ফোন</th>
                                <th class="py-4 px-6">কোর্সের নাম</th>
                                <th class="py-4 px-6">ব্যাচ</th>
                                <th class="py-4 px-6">স্ট্যাটাস</th>
                                <th class="py-4 px-6">ভর্তির তারিখ</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 dark:divide-slate-800 text-slate-700 dark:text-slate-300">
                            <?php foreach ($students as $st): ?>
                                <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-800/30 transition">
                                    <td class="py-4 px-6 font-bold text-slate-900 dark:text-white text-sm">
                                        <?= sanitize($st['student_name']) ?>
                                    </td>
                                    <td class="py-4 px-6">
                                        <p class="font-medium"><?= sanitize($st['student_email']) ?></p>
                                        <p class="text-slate-400 text-[11px]"><?= sanitize($st['student_phone'] ?? 'N/A') ?></p>
                                    </td>
                                    <td class="py-4 px-6 font-semibold">
                                        <?= sanitize($st['course_title']) ?>
                                    </td>
                                    <td class="py-4 px-6">
                                        <?= sanitize($st['batch_name']) ?>
                                    </td>
                                    <td class="py-4 px-6">
                                        <span class="px-2.5 py-1 rounded-full text-[10px] font-bold <?= $st['payment_status'] === 'approved' ? 'bg-emerald-500/10 text-emerald-600' : 'bg-amber-500/10 text-amber-600' ?>">
                                            <?= $st['payment_status'] === 'approved' ? 'অনুমোদিত' : 'পেন্ডিং' ?>
                                        </span>
                                    </td>
                                    <td class="py-4 px-6 text-slate-400">
                                        <?= date('d M, Y', strtotime($st['enrolled_at'])) ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        <?php endif; ?>

    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
