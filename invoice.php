<?php
/**
 * Universal Technology Corporation (UTC) - Official Invoice & Receipt
 */

require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/config/auth.php';
require_once __DIR__ . '/config/helpers.php';

$enrollmentId = isset($_GET['id']) ? (int)$_GET['id'] : 1;

$stmt = $pdo->prepare("SELECT e.*, c.title as course_title, c.original_price, c.discount_price, c.category,
                              u.name as student_name, u.email as student_email, u.phone as student_phone
                       FROM enrollments e
                       JOIN courses c ON e.course_id = c.id
                       JOIN users u ON e.student_id = u.id
                       WHERE e.id = ?");
$stmt->execute([$enrollmentId]);
$enrollment = $stmt->fetch();

if (!$enrollment) {
    header("Location: index.php");
    exit;
}

$pageTitle = 'মানি রিসিট ও ইনভয়েস #' . $enrollment['id'] . ' | UTC';
require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/navbar.php';
?>

<div class="py-12 bg-slate-100/60 dark:bg-[#070b16] transition-colors duration-300">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <!-- Action Bar (Print & Back) -->
        <div class="flex items-center justify-between mb-6 no-print">
            <a href="student/my-courses.php" class="px-4 py-2 rounded-xl bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-xs font-bold text-slate-700 dark:text-slate-300 hover:text-blue-600 flex items-center gap-2 transition">
                <i data-lucide="arrow-left" class="w-4 h-4"></i> আমার ক্লাসরুমে যান
            </a>
            <div class="flex items-center gap-3">
                <button onclick="window.print()" class="px-5 py-2 rounded-xl bg-blue-600 hover:bg-blue-500 text-white text-xs font-bold flex items-center gap-2 shadow-md transition">
                    <i data-lucide="printer" class="w-4 h-4"></i> রিসিট প্রিন্ট করুন
                </button>
            </div>
        </div>

        <?= renderFlash() ?>

        <!-- Printable Invoice Container -->
        <div class="p-8 sm:p-12 rounded-3xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 shadow-2xl space-y-8 print:border-none print:shadow-none print:p-0">
            
            <!-- Header Section -->
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-6 pb-8 border-b border-slate-200 dark:border-slate-800">
                <div class="space-y-1">
                    <div class="flex items-center gap-3">
                        <img src="<?= $appRoot ?>assets/images/utc-logo.png" alt="UTC Official Logo" class="w-14 h-14 rounded-full object-contain bg-white p-1 shadow-md border border-slate-200 shrink-0">
                        <div>
                            <h2 class="text-xl font-extrabold text-slate-900 dark:text-white">
                                Universal Technology Corporation
                            </h2>
                            <p class="text-xs text-blue-600 dark:text-cyan-400 font-bold uppercase tracking-wider">
                                Feni Leading IT Hub & Training Institute
                            </p>
                        </div>
                    </div>
                    <p class="text-xs text-slate-500 dark:text-slate-400 pt-2">
                        📍 287 (1st Floor), Doctor Para, Feni-3900<br>
                        📞 01812669757, 01817122483 | ✉️ utcsoftltd@gmail.com
                    </p>
                </div>

                <div class="text-left sm:text-right space-y-1">
                    <span class="inline-block px-3 py-1 rounded-full text-xs font-extrabold uppercase tracking-wider <?= $enrollment['payment_status'] === 'approved' ? 'bg-emerald-500/10 text-emerald-600 border border-emerald-500/30' : 'bg-amber-500/10 text-amber-600 border border-amber-500/30' ?>">
                        <?= $enrollment['payment_status'] === 'approved' ? 'PAID & APPROVED' : 'VERIFICATION PENDING' ?>
                    </span>
                    <h3 class="text-lg font-bold text-slate-900 dark:text-white font-mono pt-1">
                        INV-UTC-2026-<?= str_pad($enrollment['id'], 4, '0', STR_PAD_LEFT) ?>
                    </h3>
                    <p class="text-xs text-slate-500">তারিখ: <?= date('d M, Y - h:i A', strtotime($enrollment['enrolled_at'])) ?></p>
                </div>
            </div>

            <!-- Student & Payment Info Grid -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-8 text-xs">
                <div class="space-y-1.5 p-4 rounded-2xl bg-slate-50 dark:bg-slate-800/40 border border-slate-100 dark:border-slate-800">
                    <span class="font-bold text-slate-400 uppercase tracking-wider text-[10px]">শিক্ষার্থীর তথ্য (Billed To):</span>
                    <p class="font-bold text-slate-900 dark:text-white text-sm"><?= sanitize($enrollment['student_name']) ?></p>
                    <p class="text-slate-600 dark:text-slate-300">ইমেইল: <?= sanitize($enrollment['student_email']) ?></p>
                    <p class="text-slate-600 dark:text-slate-300">ফোন: <?= sanitize($enrollment['student_phone'] ?? $enrollment['sender_phone'] ?? 'N/A') ?></p>
                </div>

                <div class="space-y-1.5 p-4 rounded-2xl bg-slate-50 dark:bg-slate-800/40 border border-slate-100 dark:border-slate-800">
                    <span class="font-bold text-slate-400 uppercase tracking-wider text-[10px]">পেমেন্ট মেথড ও তথ্য:</span>
                    <p class="font-bold text-slate-900 dark:text-white text-sm">মেথড: <?= sanitize($enrollment['payment_method']) ?></p>
                    <p class="text-slate-600 dark:text-slate-300">TrxID: <span class="font-mono font-bold text-blue-600 dark:text-cyan-400"><?= sanitize($enrollment['transaction_id'] ?? 'N/A') ?></span></p>
                    <p class="text-slate-600 dark:text-slate-300">প্রেরক ফোন: <?= sanitize($enrollment['sender_phone'] ?? 'N/A') ?></p>
                </div>
            </div>

            <!-- Table of Fees -->
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead>
                        <tr class="border-b border-slate-200 dark:border-slate-800 text-slate-400 uppercase tracking-wider">
                            <th class="py-3 px-2">কোর্স বিবরণ</th>
                            <th class="py-3 px-2">ব্যাচ</th>
                            <th class="py-3 px-2 text-right">নিয়মিত ফি</th>
                            <th class="py-3 px-2 text-right">পরিশোধিত ফি</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-800 text-slate-700 dark:text-slate-300">
                        <tr>
                            <td class="py-4 px-2">
                                <p class="font-bold text-slate-900 dark:text-white text-sm"><?= sanitize($enrollment['course_title']) ?></p>
                                <span class="text-[11px] text-slate-500"><?= sanitize($enrollment['category']) ?></span>
                            </td>
                            <td class="py-4 px-2 font-semibold">
                                <?= sanitize($enrollment['batch_name']) ?>
                            </td>
                            <td class="py-4 px-2 text-right line-through text-slate-400">
                                <?= formatBDT($enrollment['original_price']) ?>
                            </td>
                            <td class="py-4 px-2 text-right font-black text-slate-900 dark:text-white text-sm">
                                <?= formatBDT($enrollment['amount']) ?>
                            </td>
                        </tr>
                    </tbody>
                    <tfoot>
                        <tr class="border-t-2 border-slate-200 dark:border-slate-700 text-sm font-extrabold text-slate-900 dark:text-white">
                            <td colspan="3" class="py-4 px-2 text-right">সর্বমোট পরিশোধিত (Total Paid):</td>
                            <td class="py-4 px-2 text-right text-blue-600 dark:text-cyan-400 text-base">
                                <?= formatBDT($enrollment['amount']) ?>
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </div>

            <!-- Notice / Seal -->
            <div class="pt-6 border-t border-slate-200 dark:border-slate-800 flex flex-col sm:flex-row items-center justify-between text-xs text-slate-500 gap-4">
                <div class="space-y-1 text-center sm:text-left">
                    <p class="font-bold text-slate-700 dark:text-slate-300">✨ Learn Today | Skill Up Tomorrow | Build Your Career with UTC ✨</p>
                    <p>যেকোনো জিজ্ঞাসা বা সাপোর্টের জন্য কল করুন: 01812669757, 01817122483</p>
                </div>
                <div class="text-center">
                    <div class="w-24 h-12 border-b-2 border-slate-300 dark:border-slate-600 flex items-end justify-center pb-1 font-mono text-[10px] text-slate-400">
                        Authorized Seal
                    </div>
                    <span class="text-[10px] font-bold text-slate-400">UTC Accounts Officer</span>
                </div>
            </div>

        </div>

    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
