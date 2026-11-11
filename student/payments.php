<?php
/**
 * Universal Technology Corporation (UTC) - Student Payment History
 */

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/auth.php';
require_once __DIR__ . '/../config/helpers.php';

requireRole('student');
$user = getCurrentUser();

$stmt = $pdo->prepare("SELECT e.*, c.title as course_title, c.original_price
                       FROM enrollments e
                       JOIN courses c ON e.course_id = c.id
                       WHERE e.student_id = ?
                       ORDER BY e.id DESC");
$stmt->execute([$user['id']]);
$payments = $stmt->fetchAll();

$pageTitle = 'পেমেন্ট ও লেনদেন হিস্ট্রি | UTC';
require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/navbar.php';
?>

<div class="py-12 bg-slate-50 dark:bg-[#080d1a] min-h-[calc(100vh-80px)] transition-colors duration-300">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8">
        
        <div class="pb-6 border-b border-slate-200 dark:border-slate-800">
            <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 dark:text-white">
                পেমেন্ট ও ফি পরিশোধের ইতিহাস
            </h1>
            <p class="text-xs sm:text-sm text-slate-500 mt-1">
                আপনার সকল কোর্স ফি পেমেন্ট ট্রানজেকশন ও অফিশিয়াল মানি রিসিট ডাউনলোড করুন।
            </p>
        </div>

        <?= renderFlash() ?>

        <?php if (empty($payments)): ?>
            <div class="p-12 rounded-3xl glass-panel border border-slate-200 dark:border-slate-800 text-center space-y-3">
                <div class="w-14 h-14 rounded-2xl bg-slate-100 dark:bg-slate-800 text-slate-400 flex items-center justify-center mx-auto">
                    <i data-lucide="receipt" class="w-7 h-7"></i>
                </div>
                <h3 class="text-base font-bold text-slate-800 dark:text-white">কোন পেমেন্ট হিস্ট্রি নেই</h3>
                <p class="text-xs text-slate-500">আপনি কোনো কোর্সে ভর্তি হয়ে পেমেন্ট করলে তা এখানে প্রদর্শিত হবে।</p>
            </div>
        <?php else: ?>
            <div class="glass-panel rounded-3xl border border-slate-200 dark:border-slate-800 overflow-hidden shadow-xl">
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs">
                        <thead>
                            <tr class="bg-slate-100/70 dark:bg-slate-800/60 border-b border-slate-200 dark:border-slate-800 text-slate-500 uppercase tracking-wider">
                                <th class="py-4 px-6">ইনভয়েস #</th>
                                <th class="py-4 px-6">কোর্সের নাম</th>
                                <th class="py-4 px-6">পেমেন্ট মেথড</th>
                                <th class="py-4 px-6">TrxID</th>
                                <th class="py-4 px-6">পরিশোধিত ফি</th>
                                <th class="py-4 px-6">স্ট্যাটাস</th>
                                <th class="py-4 px-6 text-right">রিসিট</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 dark:divide-slate-800 text-slate-700 dark:text-slate-300">
                            <?php foreach ($payments as $p): ?>
                                <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-800/30 transition">
                                    <td class="py-4 px-6 font-mono font-bold text-slate-900 dark:text-white">
                                        INV-UTC-2026-<?= str_pad($p['id'], 4, '0', STR_PAD_LEFT) ?>
                                    </td>
                                    <td class="py-4 px-6 font-semibold">
                                        <?= sanitize($p['course_title']) ?>
                                    </td>
                                    <td class="py-4 px-6">
                                        <span class="px-2.5 py-1 rounded-md text-[11px] font-bold bg-slate-100 dark:bg-slate-800 text-slate-800 dark:text-slate-200">
                                            <?= sanitize($p['payment_method']) ?>
                                        </span>
                                    </td>
                                    <td class="py-4 px-6 font-mono text-[11px] text-blue-600 dark:text-cyan-400">
                                        <?= sanitize($p['transaction_id'] ?? 'N/A') ?>
                                    </td>
                                    <td class="py-4 px-6 font-extrabold text-slate-900 dark:text-white text-sm">
                                        <?= formatBDT($p['amount']) ?>
                                    </td>
                                    <td class="py-4 px-6">
                                        <span class="px-2.5 py-1 rounded-full text-[10px] font-extrabold uppercase <?= $p['payment_status'] === 'approved' ? 'bg-emerald-500/10 text-emerald-600 border border-emerald-500/30' : ($p['payment_status'] === 'rejected' ? 'bg-rose-500/10 text-rose-600 border border-rose-500/30' : 'bg-amber-500/10 text-amber-600 border border-amber-500/30') ?>">
                                            <?= $p['payment_status'] === 'approved' ? 'অনুমোদিত' : ($p['payment_status'] === 'rejected' ? 'বাতিল' : 'যাচাই চলছে') ?>
                                        </span>
                                    </td>
                                    <td class="py-4 px-6 text-right">
                                        <a href="../invoice.php?id=<?= $p['id'] ?>" class="px-3 py-1.5 rounded-lg bg-blue-600 text-white font-bold text-xs hover:bg-blue-500 transition inline-flex items-center gap-1.5">
                                            <i data-lucide="printer" class="w-3.5 h-3.5"></i> রিসিট
                                        </a>
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
