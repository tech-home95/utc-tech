<?php
/**
 * Universal Technology Corporation (UTC) - Admin Dashboard
 */

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/auth.php';
require_once __DIR__ . '/../config/helpers.php';

requireRole('admin');
$user = getCurrentUser();

// Handle quick approve/reject from dashboard
if (isset($_GET['action']) && isset($_GET['enrollment_id'])) {
    $enId = (int)$_GET['enrollment_id'];
    $act = $_GET['action'];
    if ($act === 'approve') {
        $pdo->prepare("UPDATE enrollments SET payment_status = 'approved', approved_at = CURRENT_TIMESTAMP WHERE id = ?")->execute([$enId]);
        setFlash('success', "এনরোলমেন্ট #{$enId} সফলভাবে অনুমোদিত (Approved) হয়েছে!");
    } elseif ($act === 'reject') {
        $pdo->prepare("UPDATE enrollments SET payment_status = 'rejected' WHERE id = ?")->execute([$enId]);
        setFlash('warning', "এনরোলমেন্ট #{$enId} বাতিল (Rejected) করা হয়েছে।");
    }
    header("Location: index.php");
    exit;
}

// Calculate Statistics
$totalRevenue = $pdo->query("SELECT COALESCE(SUM(amount), 0) FROM enrollments WHERE payment_status = 'approved'")->fetchColumn();
$totalStudents = $pdo->query("SELECT COUNT(*) FROM users WHERE role = 'student'")->fetchColumn();
$totalCourses = $pdo->query("SELECT COUNT(*) FROM courses")->fetchColumn();
$pendingPayments = $pdo->query("SELECT COUNT(*) FROM enrollments WHERE payment_status = 'pending'")->fetchColumn();

// Recent Enrollments
$recentEnrollments = $pdo->query("SELECT e.*, c.title as course_title, u.name as student_name, u.email as student_email, u.phone as student_phone
                                  FROM enrollments e
                                  JOIN courses c ON e.course_id = c.id
                                  JOIN users u ON e.student_id = u.id
                                  ORDER BY e.id DESC LIMIT 8")->fetchAll();

$pageTitle = 'অ্যাডমিন ড্যাশবোর্ড ও কন্ট্রোল প্যানেল | UTC';
require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/navbar.php';
?>

<div class="py-10 bg-slate-50 dark:bg-[#080d1a] min-h-[calc(100vh-80px)] transition-colors duration-300">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8">
        
        <!-- Header Banner -->
        <div class="p-8 rounded-3xl bg-gradient-to-r from-purple-950 via-indigo-950 to-slate-900 text-white relative overflow-hidden shadow-2xl">
            <div class="absolute -right-10 -bottom-10 w-64 h-64 bg-purple-500/20 rounded-full blur-3xl pointer-events-none"></div>
            <div class="relative z-10 flex flex-col md:flex-row items-start md:items-center justify-between gap-6">
                <div class="space-y-2">
                    <span class="px-3 py-1 rounded-full text-xs font-bold bg-purple-500/30 text-purple-300 border border-purple-400/30">
                        Admin Master Control Panel
                    </span>
                    <h1 class="text-2xl sm:text-3xl font-extrabold tracking-tight">
                        UTC অ্যাডমিন ড্যাশবোর্ড
                    </h1>
                    <p class="text-xs sm:text-sm text-slate-300">
                        Universal Technology Corporation (UTC) - ফেনী প্রধান ক্যাম্পাস সার্বিক ব্যবস্থাপনা।
                    </p>
                </div>
                <div class="flex flex-wrap items-center gap-3">
                    <a href="courses.php?action=add" class="px-4 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-500 text-white text-xs font-bold shadow-md transition flex items-center gap-2">
                        <i data-lucide="plus-circle" class="w-4 h-4"></i> নতুন কোর্স যোগ করুন
                    </a>
                    <a href="enrollments.php" class="px-4 py-2.5 rounded-xl bg-purple-600 hover:bg-purple-500 text-white text-xs font-bold shadow-md transition flex items-center gap-2">
                        <i data-lucide="badge-dollar-sign" class="w-4 h-4"></i> পেমেন্ট ভেরিফাই করুন (<?= $pendingPayments ?>)
                    </a>
                </div>
            </div>
        </div>

        <?= renderFlash() ?>

        <!-- Key Financial & Operational Metric Cards -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
            
            <div class="p-6 rounded-3xl glass-panel border border-slate-200 dark:border-slate-800 space-y-2">
                <div class="flex items-center justify-between text-emerald-600 dark:text-emerald-400">
                    <span class="text-xs font-bold uppercase tracking-wider">মোট রাজস্ব (Revenue)</span>
                    <i data-lucide="wallet" class="w-5 h-5"></i>
                </div>
                <div class="text-3xl font-black text-slate-900 dark:text-white">
                    <?= formatBDT($totalRevenue) ?>
                </div>
                <p class="text-[11px] text-slate-500">অনুমোদিত ফি সংগ্রহ</p>
            </div>

            <div class="p-6 rounded-3xl glass-panel border border-slate-200 dark:border-slate-800 space-y-2">
                <div class="flex items-center justify-between text-blue-600 dark:text-cyan-400">
                    <span class="text-xs font-bold uppercase tracking-wider">মোট শিক্ষার্থী</span>
                    <i data-lucide="users" class="w-5 h-5"></i>
                </div>
                <div class="text-3xl font-black text-slate-900 dark:text-white">
                    <?= $totalStudents ?> জন
                </div>
                <p class="text-[11px] text-slate-500">নিবন্ধিত শিক্ষার্থী সংখ্যা</p>
            </div>

            <div class="p-6 rounded-3xl glass-panel border border-slate-200 dark:border-slate-800 space-y-2">
                <div class="flex items-center justify-between text-purple-600 dark:text-purple-400">
                    <span class="text-xs font-bold uppercase tracking-wider">সক্রিয় কোর্স</span>
                    <i data-lucide="book-open" class="w-5 h-5"></i>
                </div>
                <div class="text-3xl font-black text-slate-900 dark:text-white">
                    <?= $totalCourses ?> টি
                </div>
                <p class="text-[11px] text-slate-500">প্রফেশনাল আইটি কোর্স</p>
            </div>

            <div class="p-6 rounded-3xl glass-panel border border-slate-200 dark:border-slate-800 space-y-2">
                <div class="flex items-center justify-between text-amber-600 dark:text-amber-400">
                    <span class="text-xs font-bold uppercase tracking-wider">পেন্ডিং পেমেন্ট</span>
                    <i data-lucide="clock" class="w-5 h-5"></i>
                </div>
                <div class="text-3xl font-black text-amber-500">
                    <?= $pendingPayments ?> টি
                </div>
                <p class="text-[11px] text-slate-500">TrxID যাচাই প্রয়োজন</p>
            </div>

        </div>

        <!-- Recent Enrollments & Payment Verification Ledger -->
        <div class="glass-panel rounded-3xl border border-slate-200 dark:border-slate-800 overflow-hidden shadow-2xl space-y-4 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-lg font-bold text-slate-900 dark:text-white flex items-center gap-2">
                        <i data-lucide="badge-dollar-sign" class="w-5 h-5 text-emerald-500"></i>
                        <span>সাম্প্রতিক পেমেন্ট ও এনরোলমেন্ট রিকোয়েস্ট</span>
                    </h2>
                    <p class="text-xs text-slate-500">বিকাশ, নগদ, রকেট TrxID যাচাই করে অনুমোদন দিন।</p>
                </div>
                <a href="enrollments.php" class="text-xs font-bold text-purple-600 dark:text-purple-400 hover:underline">
                    সকল এনরোলমেন্ট দেখুন &rarr;
                </a>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead>
                        <tr class="bg-slate-100/70 dark:bg-slate-800/60 border-b border-slate-200 dark:border-slate-800 text-slate-500 uppercase tracking-wider">
                            <th class="py-3 px-4">শিক্ষার্থী</th>
                            <th class="py-3 px-4">কোর্স</th>
                            <th class="py-3 px-4">মেথড & TrxID</th>
                            <th class="py-3 px-4">প্রেরক ফোন</th>
                            <th class="py-3 px-4">ফি</th>
                            <th class="py-3 px-4">স্ট্যাটাস</th>
                            <th class="py-3 px-4 text-right">অ্যাকশন (Action)</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-800 text-slate-700 dark:text-slate-300">
                        <?php if (empty($recentEnrollments)): ?>
                            <tr>
                                <td colspan="7" class="py-6 text-center text-slate-500">কোন এনরোলমেন্ট রেকর্ড নেই।</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($recentEnrollments as $en): ?>
                                <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-800/30 transition">
                                    <td class="py-3 px-4">
                                        <p class="font-bold text-slate-900 dark:text-white"><?= sanitize($en['student_name']) ?></p>
                                        <p class="text-[10px] text-slate-400"><?= sanitize($en['student_email']) ?></p>
                                    </td>
                                    <td class="py-3 px-4 font-semibold max-w-[200px] truncate" title="<?= sanitize($en['course_title']) ?>">
                                        <?= sanitize($en['course_title']) ?>
                                    </td>
                                    <td class="py-3 px-4">
                                        <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-slate-200 dark:bg-slate-700 text-slate-800 dark:text-slate-200">
                                            <?= sanitize($en['payment_method']) ?>
                                        </span>
                                        <span class="block font-mono text-[11px] text-blue-600 dark:text-cyan-400 font-bold mt-0.5">
                                            <?= sanitize($en['transaction_id'] ?? 'N/A') ?>
                                        </span>
                                    </td>
                                    <td class="py-3 px-4 font-mono">
                                        <?= sanitize($en['sender_phone'] ?? $en['student_phone'] ?? 'N/A') ?>
                                    </td>
                                    <td class="py-3 px-4 font-bold text-slate-900 dark:text-white">
                                        <?= formatBDT($en['amount']) ?>
                                    </td>
                                    <td class="py-3 px-4">
                                        <span class="px-2.5 py-1 rounded-full text-[10px] font-extrabold uppercase <?= $en['payment_status'] === 'approved' ? 'bg-emerald-500/10 text-emerald-600' : ($en['payment_status'] === 'rejected' ? 'bg-rose-500/10 text-rose-600' : 'bg-amber-500/10 text-amber-600') ?>">
                                            <?= $en['payment_status'] === 'approved' ? 'অনুমোদিত' : ($en['payment_status'] === 'rejected' ? 'বাতিল' : 'যাচাই বাকি') ?>
                                        </span>
                                    </td>
                                    <td class="py-3 px-4 text-right space-x-1">
                                        <?php if ($en['payment_status'] !== 'approved'): ?>
                                            <a href="index.php?action=approve&enrollment_id=<?= $en['id'] ?>" class="px-2.5 py-1 rounded-lg bg-emerald-600 hover:bg-emerald-500 text-white font-bold text-[11px] transition inline-block">
                                                অনুমোদন ✓
                                            </a>
                                        <?php endif; ?>
                                        <?php if ($en['payment_status'] !== 'rejected'): ?>
                                            <a href="index.php?action=reject&enrollment_id=<?= $en['id'] ?>" onclick="return confirm('পেমেন্টটি বাতিল করতে চান?')" class="px-2 py-1 rounded-lg bg-rose-600/10 hover:bg-rose-600 hover:text-white text-rose-600 font-bold text-[11px] transition inline-block">
                                                বাতিল ✕
                                            </a>
                                        <?php endif; ?>
                                        <a href="../invoice.php?id=<?= $en['id'] ?>" target="_blank" class="px-2 py-1 rounded-lg bg-slate-200 dark:bg-slate-700 text-slate-700 dark:text-slate-200 font-bold text-[11px] transition inline-block">
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
