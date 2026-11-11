<?php
/**
 * Universal Technology Corporation (UTC) - Admin User Management
 */

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/auth.php';
require_once __DIR__ . '/../config/helpers.php';

requireRole('admin');
$user = getCurrentUser();

// Handle Create Teacher / User
if (($_SERVER['REQUEST_METHOD'] ?? '') === 'POST' && isset($_POST['add_user'])) {
    $name = sanitize($_POST['name'] ?? '');
    $email = sanitize($_POST['email'] ?? '');
    $phone = sanitize($_POST['phone'] ?? '');
    $role = sanitize($_POST['role'] ?? 'teacher');
    $password = trim($_POST['password'] ?? '123456');
    $bio = sanitize($_POST['bio'] ?? '');

    $chk = $pdo->prepare("SELECT id FROM users WHERE email = ?");
    $chk->execute([$email]);
    if ($chk->fetch()) {
        setFlash('error', 'এই ইমেইলটি ইতিপূর্বে নিবন্ধিত হয়েছে!');
    } else {
        $hashed = password_hash($password, PASSWORD_DEFAULT);
        $ins = $pdo->prepare("INSERT INTO users (name, email, password, phone, role, bio) VALUES (?, ?, ?, ?, ?, ?)");
        $ins->execute([$name, $email, $hashed, $phone, $role, $bio]);
        setFlash('success', "নতুন ব্যবহারকারী/শিক্ষক ({$name}) সফলভাবে তৈরি হয়েছে!");
        header("Location: users.php");
        exit;
    }
}

// Handle Delete User
if (isset($_GET['delete_user'])) {
    $delId = (int)$_GET['delete_user'];
    if ($delId != $user['id']) { // Cannot delete self
        $pdo->prepare("DELETE FROM users WHERE id = ?")->execute([$delId]);
        setFlash('success', 'ব্যবহারকারী সফলভাবে মুছে ফেলা হয়েছে।');
    } else {
        setFlash('error', 'আপনি নিজের অ্যাডমিন অ্যাকাউন্ট মুছে ফেলতে পারবেন না!');
    }
    header("Location: users.php");
    exit;
}

// Fetch all users
$stmt = $pdo->query("SELECT * FROM users ORDER BY id DESC");
$allUsers = $stmt->fetchAll();

$pageTitle = 'ব্যবহারকারী ও শিক্ষক ব্যবস্থাপনা | Admin Panel | UTC';
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
                    ব্যবহারকারী ও ট্রেনার ব্যবস্থাপনা
                </h1>
                <p class="text-xs sm:text-sm text-slate-500 mt-1">
                    শিক্ষার্থী, শিক্ষক ও অ্যাডমিনদের একাউন্ট কন্ট্রোল ও নতুন শিক্ষক নিয়োগ।
                </p>
            </div>
        </div>

        <?= renderFlash() ?>

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
            
            <!-- Left 8 Cols: Users List -->
            <div class="lg:col-span-8 space-y-4">
                <div class="glass-panel rounded-3xl border border-slate-200 dark:border-slate-800 overflow-hidden shadow-2xl">
                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-xs">
                            <thead>
                                <tr class="bg-slate-100/70 dark:bg-slate-800/60 border-b border-slate-200 dark:border-slate-800 text-slate-500 uppercase tracking-wider">
                                    <th class="py-4 px-6">নাম ও ভূমিকা</th>
                                    <th class="py-4 px-6">ইমেইল</th>
                                    <th class="py-4 px-6">ফোন</th>
                                    <th class="py-4 px-6">নিবন্ধন তারিখ</th>
                                    <th class="py-4 px-6 text-right">অ্যাকশন</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 dark:divide-slate-800 text-slate-700 dark:text-slate-300">
                                <?php foreach ($allUsers as $u): ?>
                                    <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-800/30 transition">
                                        <td class="py-4 px-6">
                                            <p class="font-bold text-slate-900 dark:text-white text-sm"><?= sanitize($u['name']) ?></p>
                                            <span class="inline-block mt-1 px-2 py-0.5 rounded text-[10px] font-bold uppercase <?= $u['role'] === 'admin' ? 'bg-purple-500/10 text-purple-600' : ($u['role'] === 'teacher' ? 'bg-emerald-500/10 text-emerald-600' : 'bg-blue-500/10 text-blue-600') ?>">
                                                <?= $u['role'] ?>
                                            </span>
                                        </td>
                                        <td class="py-4 px-6 font-medium">
                                            <?= sanitize($u['email']) ?>
                                        </td>
                                        <td class="py-4 px-6 font-mono text-[11px]">
                                            <?= sanitize($u['phone'] ?? 'N/A') ?>
                                        </td>
                                        <td class="py-4 px-6 text-slate-400">
                                            <?= date('d M, Y', strtotime($u['created_at'])) ?>
                                        </td>
                                        <td class="py-4 px-6 text-right">
                                            <?php if ($u['id'] != $user['id']): ?>
                                                <a href="users.php?delete_user=<?= $u['id'] ?>" onclick="return confirm('এই অ্যাকাউন্টটি মুছে ফেলতে চান?')" class="px-2.5 py-1 rounded-lg bg-rose-500/10 hover:bg-rose-600 hover:text-white text-rose-600 font-bold text-xs transition">
                                                    মুছুন
                                                </a>
                                            <?php else: ?>
                                                <span class="text-[10px] text-slate-400 font-bold">You (Admin)</span>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Right 4 Cols: Add New Teacher/Admin Form -->
            <div class="lg:col-span-4">
                <div class="p-8 rounded-3xl glass-panel border border-slate-200 dark:border-slate-800 shadow-xl space-y-4 text-xs">
                    <h3 class="text-base font-bold text-slate-900 dark:text-white flex items-center gap-2 pb-2 border-b border-slate-100 dark:border-slate-800">
                        <i data-lucide="user-plus" class="w-5 h-5 text-purple-600 dark:text-purple-400"></i>
                        <span>নতুন শিক্ষক বা কর্মকর্তা যুক্ত করুন</span>
                    </h3>

                    <form method="POST" class="space-y-4">
                        <div>
                            <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">পূর্ণ নাম *</label>
                            <input type="text" name="name" required placeholder="ট্রেনারের নাম" class="w-full px-3 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white">
                        </div>

                        <div>
                            <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">ইমেইল এড্রেস *</label>
                            <input type="email" name="email" required placeholder="trainer@utc.edu.bd" class="w-full px-3 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white">
                        </div>

                        <div>
                            <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">মোবাইল নম্বর</label>
                            <input type="tel" name="phone" placeholder="018XXXXXXXX" class="w-full px-3 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white">
                        </div>

                        <div>
                            <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">পাসওয়ার্ড</label>
                            <input type="password" name="password" value="teacher123" required class="w-full px-3 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white">
                        </div>

                        <div>
                            <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">ভূমিকা (Role)</label>
                            <select name="role" class="w-full px-3 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white">
                                <option value="teacher">শিক্ষক / ট্রেনার (Teacher)</option>
                                <option value="admin">সুপার অ্যাডমিন (Admin)</option>
                                <option value="student">শিক্ষার্থী (Student)</option>
                            </select>
                        </div>

                        <div>
                            <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">বায়ো / অভিজ্ঞতা</label>
                            <textarea name="bio" rows="2" placeholder="ইন্ডাস্ট্রি ও টেকনোলজি অভিজ্ঞতা..." class="w-full px-3 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white"></textarea>
                        </div>

                        <button type="submit" name="add_user" class="w-full py-3 rounded-xl bg-purple-600 hover:bg-purple-500 text-white font-bold transition shadow-lg shadow-purple-500/25">
                            ব্যবহারকারী যোগ করুন
                        </button>
                    </form>
                </div>
            </div>

        </div>

    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
