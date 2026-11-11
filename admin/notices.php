<?php
/**
 * Universal Technology Corporation (UTC) - Admin Notices
 */

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/auth.php';
require_once __DIR__ . '/../config/helpers.php';

requireRole('admin');
$user = getCurrentUser();

if (($_SERVER['REQUEST_METHOD'] ?? '') === 'POST' && isset($_POST['post_notice'])) {
    $title = sanitize($_POST['title'] ?? '');
    $content = sanitize($_POST['content'] ?? '');
    $targetRole = sanitize($_POST['target_role'] ?? 'all');

    if (!empty($title) && !empty($content)) {
        $ins = $pdo->prepare("INSERT INTO notices (title, content, target_role) VALUES (?, ?, ?)");
        $ins->execute([$title, $content, $targetRole]);
        setFlash('success', 'নতুন নোটিশ সফলভাবে প্রকাশ করা হয়েছে!');
        header("Location: notices.php");
        exit;
    }
}

if (isset($_GET['delete_notice'])) {
    $delId = (int)$_GET['delete_notice'];
    $pdo->prepare("DELETE FROM notices WHERE id = ?")->execute([$delId]);
    setFlash('success', 'নোটিশটি মুছে ফেলা হয়েছে।');
    header("Location: notices.php");
    exit;
}

$notices = $pdo->query("SELECT * FROM notices ORDER BY id DESC")->fetchAll();

$pageTitle = 'নোটিশ বোর্ড পরিচালনা | Admin Panel | UTC';
require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/navbar.php';
?>

<div class="py-12 bg-slate-50 dark:bg-[#080d1a] min-h-[calc(100vh-80px)] transition-colors duration-300">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8">
        
        <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 pb-6 border-b border-slate-200 dark:border-slate-800">
            <div>
                <a href="index.php" class="text-xs font-bold text-purple-600 dark:text-purple-400 hover:underline flex items-center gap-1 mb-1">
                    <i data-lucide="arrow-left" class="w-3.5 h-3.5"></i> অ্যাডমিন ড্যাশবোর্ড
                </a>
                <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 dark:text-white">
                    অফিসিয়াল নোটিশ বোর্ড পরিচালনা
                </h1>
                <p class="text-xs sm:text-sm text-slate-500 mt-1">
                    শিক্ষার্থী এবং শিক্ষকদের উদ্দেশ্যে গুরুত্বপূর্ণ আপডেট ও বার্তা পোস্ট করুন।
                </p>
            </div>
        </div>

        <?= renderFlash() ?>

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
            
            <!-- Left 7 Cols: Notices List -->
            <div class="lg:col-span-7 space-y-4">
                <h2 class="text-lg font-bold text-slate-900 dark:text-white">
                    প্রকাশিত নোটিশসমূহ
                </h2>

                <div class="space-y-4">
                    <?php foreach ($notices as $nt): ?>
                        <div class="p-6 rounded-3xl glass-panel border border-slate-200 dark:border-slate-800 space-y-2">
                            <div class="flex items-center justify-between">
                                <span class="px-2.5 py-0.5 rounded text-[10px] font-bold uppercase <?= $nt['target_role'] === 'student' ? 'bg-blue-500/10 text-blue-600' : 'bg-purple-500/10 text-purple-600' ?>">
                                    প্রাপক: <?= $nt['target_role'] ?>
                                </span>
                                <a href="notices.php?delete_notice=<?= $nt['id'] ?>" onclick="return confirm('নোটিশটি মুছে ফেলতে চান?')" class="text-rose-500 hover:text-rose-700 text-xs font-bold">
                                    মুছুন
                                </a>
                            </div>
                            <h3 class="text-base font-bold text-slate-900 dark:text-white"><?= sanitize($nt['title']) ?></h3>
                            <p class="text-xs text-slate-600 dark:text-slate-300 leading-relaxed"><?= sanitize($nt['content']) ?></p>
                            <span class="text-[10px] text-slate-400 block pt-1"><?= date('d M, Y - h:i A', strtotime($nt['created_at'])) ?></span>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- Right 5 Cols: Create Notice Form -->
            <div class="lg:col-span-5">
                <div class="p-8 rounded-3xl glass-panel border border-slate-200 dark:border-slate-800 shadow-xl space-y-4 text-xs">
                    <h3 class="text-base font-bold text-slate-900 dark:text-white flex items-center gap-2 pb-2 border-b border-slate-100 dark:border-slate-800">
                        <i data-lucide="send" class="w-5 h-5 text-purple-600"></i>
                        <span>নতুন নোটিশ তৈরি করুন</span>
                    </h3>

                    <form method="POST" class="space-y-4">
                        <div>
                            <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">নোটিশ শিরোনাম *</label>
                            <input type="text" name="title" required placeholder="শিরোনাম লিখুন" class="w-full px-3 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white">
                        </div>

                        <div>
                            <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">প্রাপক নির্বাচন (Target Role)</label>
                            <select name="target_role" class="w-full px-3 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white">
                                <option value="all">সকলের জন্য (All Students & Teachers)</option>
                                <option value="student">শুধু শিক্ষার্থী (Students Only)</option>
                                <option value="teacher">শুধু শিক্ষক (Teachers Only)</option>
                            </select>
                        </div>

                        <div>
                            <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">বার্তা বা বিবরণ *</label>
                            <textarea name="content" rows="4" required placeholder="নোটিশের বিস্তারিত বিবরণ..." class="w-full px-3 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white"></textarea>
                        </div>

                        <button type="submit" name="post_notice" class="w-full py-3 rounded-xl bg-purple-600 hover:bg-purple-500 text-white font-bold transition shadow-lg shadow-purple-500/25">
                            নোটিশ প্রকাশ করুন
                        </button>
                    </form>
                </div>
            </div>

        </div>

    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
