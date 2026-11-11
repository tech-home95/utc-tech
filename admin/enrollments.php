<?php
/**
 * Universal Technology Corporation (UTC) - Admin Enrollments & Payment Verification
 */

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/auth.php';
require_once __DIR__ . '/../config/helpers.php';

requireRole('admin');
$user = getCurrentUser();

// Handle Status Updates
if (isset($_GET['action']) && isset($_GET['id'])) {
    $enId = (int)$_GET['id'];
    $act = $_GET['action'];
    if ($act === 'approve') {
        $pdo->prepare("UPDATE enrollments SET payment_status = 'approved', approved_at = CURRENT_TIMESTAMP WHERE id = ?")->execute([$enId]);
        setFlash('success', "এনরোলমেন্ট #{$enId} সফলভাবে অনুমোদিত হয়েছে!");
    } elseif ($act === 'reject') {
        $pdo->prepare("UPDATE enrollments SET payment_status = 'rejected' WHERE id = ?")->execute([$enId]);
        setFlash('warning', "এনরোলমেন্ট #{$enId} বাতিল করা হয়েছে।");
    } elseif ($act === 'delete') {
        $pdo->prepare("DELETE FROM enrollments WHERE id = ?")->execute([$enId]);
        setFlash('success', "এনরোলমেন্ট রেকর্ড মুছে ফেলা হয়েছে।");
    }
    header("Location: enrollments.php");
    exit;
}

$statusFilter = isset($_GET['status']) ? trim($_GET['status']) : '';
$sql = "SELECT e.*, c.title as course_title, u.name as student_name, u.email as student_email, u.phone as student_phone
        FROM enrollments e
        JOIN courses c ON e.course_id = c.id
        JOIN users u ON e.student_id = u.id";
$params = [];

if (!empty($statusFilter)) {
    $sql .= " WHERE e.payment_status = ?";
    $params[] = $statusFilter;
}

$sql .= " ORDER BY e.id DESC";
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$enrollments = $stmt->fetchAll();

$pageTitle = 'পেমেন্ট ও এনরোলমেন্ট অনুমোদন | Admin Panel | UTC';
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
                    পেমেন্ট ও ভর্তি অনুমোদন ব্যবস্থাপনা (Payment Approval)
                </h1>
                <p class="text-xs sm:text-sm text-slate-500 mt-1">
                    বিকাশ, নগদ, রকেটের Transaction ID (TrxID) মিলিয়ে শিক্ষার্থীদের কোর্স অ্যাক্সেস দিন।
                </p>
            </div>

            <!-- Filter Buttons -->
            <div class="flex items-center gap-2 text-xs font-bold">
                <a href="enrollments.php" class="px-3.5 py-2 rounded-xl transition <?= empty($statusFilter) ? 'bg-purple-600 text-white' : 'glass-panel text-slate-600 dark:text-slate-300' ?>">
                    সকল
                </a>
                <a href="enrollments.php?status=pending" class="px-3.5 py-2 rounded-xl transition <?= ($statusFilter === 'pending') ? 'bg-amber-500 text-slate-950' : 'glass-panel text-slate-600 dark:text-slate-300' ?>">
                    পেন্ডিং
                </a>
                <a href="enrollments.php?status=approved" class="px-3.5 py-2 rounded-xl transition <?= ($statusFilter === 'approved') ? 'bg-emerald-600 text-white' : 'glass-panel text-slate-600 dark:text-slate-300' ?>">
                    অনুমোদিত
                </a>
                <a href="enrollments.php?status=rejected" class="px-3.5 py-2 rounded-xl transition <?= ($statusFilter === 'rejected') ? 'bg-rose-600 text-white' : 'glass-panel text-slate-600 dark:text-slate-300' ?>">
                    বাতিল
                </a>
            </div>
        </div>

        <?= renderFlash() ?>

        <div class="glass-panel rounded-3xl border border-slate-200 dark:border-slate-800 overflow-hidden shadow-2xl">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead>
                        <tr class="bg-slate-100/70 dark:bg-slate-800/60 border-b border-slate-200 dark:border-slate-800 text-slate-500 uppercase tracking-wider">
                            <th class="py-4 px-6">ইনভয়েস #</th>
                            <th class="py-4 px-6">শিক্ষার্থী</th>
                            <th class="py-4 px-6">কোর্সের নাম</th>
                            <th class="py-4 px-6">মেথড & TrxID</th>
                            <th class="py-4 px-6">প্রেরক ফোন</th>
                            <th class="py-4 px-6">টাকার পরিমাণ</th>
                            <th class="py-4 px-6">স্ট্যাটাস</th>
                            <th class="py-4 px-6 text-right">অনুমোদন বা অ্যাকশন</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-800 text-slate-700 dark:text-slate-300">
                        <?php if (empty($enrollments)): ?>
                            <tr>
                                <td colspan="8" class="py-8 text-center text-slate-500">কোনো এনরোলমেন্ট পাওয়া যায়নি।</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($enrollments as $en): ?>
                                <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-800/30 transition">
                                    <td class="py-4 px-6 font-mono font-bold text-slate-900 dark:text-white">
                                        #<?= $en['id'] ?>
                                    </td>
                                    <td class="py-4 px-6">
                                        <p class="font-bold text-slate-900 dark:text-white"><?= sanitize($en['student_name']) ?></p>
                                        <p class="text-[10px] text-slate-400"><?= sanitize($en['student_email']) ?></p>
                                    </td>
                                    <td class="py-4 px-6 font-semibold max-w-[220px]">
                                        <?= sanitize($en['course_title']) ?>
                                    </td>
                                    <td class="py-4 px-6">
                                        <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-slate-200 dark:bg-slate-700 text-slate-800 dark:text-slate-200">
                                            <?= sanitize($en['payment_method']) ?>
                                        </span>
                                        <span class="block font-mono text-[11px] text-blue-600 dark:text-cyan-400 font-bold mt-0.5 select-all">
                                            <?= sanitize($en['transaction_id'] ?? 'N/A') ?>
                                        </span>
                                    </td>
                                    <td class="py-4 px-6 font-mono">
                                        <?= sanitize($en['sender_phone'] ?? $en['student_phone'] ?? 'N/A') ?>
                                    </td>
                                    <td class="py-4 px-6 font-black text-slate-900 dark:text-white text-sm">
                                        <?= formatBDT($en['amount']) ?>
                                    </td>
                                    <td class="py-4 px-6">
                                        <span class="px-2.5 py-1 rounded-full text-[10px] font-extrabold uppercase <?= $en['payment_status'] === 'approved' ? 'bg-emerald-500/10 text-emerald-600 border border-emerald-500/30' : ($en['payment_status'] === 'rejected' ? 'bg-rose-500/10 text-rose-600 border border-rose-500/30' : 'bg-amber-500/10 text-amber-600 border border-amber-500/30') ?>">
                                            <?= $en['payment_status'] === 'approved' ? 'অনুমোদিত' : ($en['payment_status'] === 'rejected' ? 'বাতিল' : 'যাচাই চলছে') ?>
                                        </span>
                                    </td>
                                    <td class="py-4 px-6 text-right space-x-1 whitespace-nowrap">
                                        <?php if ($en['payment_status'] !== 'approved'): ?>
                                            <a href="enrollments.php?action=approve&id=<?= $en['id'] ?>" class="px-2.5 py-1.5 rounded-lg bg-emerald-600 hover:bg-emerald-500 text-white font-bold text-xs transition inline-block">
                                                অনুমোদন করুন ✓
                                            </a>
                                        <?php endif; ?>
                                        <?php if ($en['payment_status'] !== 'rejected'): ?>
                                            <a href="enrollments.php?action=reject&id=<?= $en['id'] ?>" onclick="return confirm('পেমেন্টটি বাতিল করতে চান?')" class="px-2 py-1.5 rounded-lg bg-rose-500/10 hover:bg-rose-600 hover:text-white text-rose-600 font-bold text-xs transition inline-block">
                                                বাতিল
                                            </a>
                                        <?php endif; ?>
                                        <a href="../invoice.php?id=<?= $en['id'] ?>" target="_blank" class="px-2.5 py-1.5 rounded-lg bg-slate-200 dark:bg-slate-700 text-slate-700 dark:text-slate-200 font-bold text-xs transition inline-block">
                                            রিসিট
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
