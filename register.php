<?php
/**
 * Universal Technology Corporation (UTC) - Student Registration
 */

require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/config/auth.php';
require_once __DIR__ . '/config/helpers.php';

if (isLoggedIn()) {
    header("Location: student/index.php");
    exit;
}

$error = '';

if (($_SERVER['REQUEST_METHOD'] ?? '') === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $password = trim($_POST['password'] ?? '');
    $role = in_array($_POST['role'] ?? '', ['student', 'teacher']) ? $_POST['role'] : 'student';

    if (empty($name) || empty($email) || empty($password)) {
        $error = 'অনুগ্রহ করে সকল প্রয়োজনীয় তথ্য পূরণ করুন।';
    } elseif (strlen($password) < 6) {
        $error = 'পাসওয়ার্ড ন্যূনতম ৬ অক্ষরের হতে হবে।';
    } else {
        // Check if email already registered
        $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->execute([$email]);
        if ($stmt->fetch()) {
            $error = 'এই ইমেইলটি ইতিপূর্বে নিবন্ধিত হয়েছে! অন্য ইমেইল ব্যবহার করুন বা লগইন করুন।';
        } else {
            $hashed = password_hash($password, PASSWORD_DEFAULT);
            $ins = $pdo->prepare("INSERT INTO users (name, email, password, phone, role) VALUES (?, ?, ?, ?, ?)");
            $ins->execute([$name, $email, $hashed, $phone, $role]);
            
            $userId = $pdo->lastInsertId();
            $user = ['id' => $userId, 'name' => $name, 'email' => $email, 'role' => $role];
            loginUser($user);

            setFlash('success', 'রেজিস্ট্রেশন সফল হয়েছে! ইউনিভার্সাল টেকনোলজি কর্পোরেশনে স্বাগতম।');
            
            if ($role === 'teacher') {
                header("Location: teacher/index.php");
            } else {
                header("Location: student/index.php");
            }
            exit;
        }
    }
}

$pageTitle = 'নতুন একাউন্ট রেজিস্ট্রেশন | UTC';
require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/navbar.php';
?>

<div class="py-16 bg-slate-50 dark:bg-[#080d1a] min-h-[calc(100vh-80px)] flex items-center justify-center transition-colors duration-300">
    <div class="max-w-md w-full mx-4 space-y-6">
        
        <div class="text-center space-y-2">
            <img src="<?= $appRoot ?>assets/images/utc-logo.png" alt="UTC Logo" class="w-16 h-16 rounded-full object-contain mx-auto shadow-2xl bg-white p-1 border-2 border-blue-500/30">
            <h1 class="text-2xl font-extrabold text-slate-900 dark:text-white">
                নতুন একাউন্ট তৈরি করুন
            </h1>
            <p class="text-xs text-slate-500 dark:text-slate-400">
                আন্তর্জাতিক মানের আইটি স্কিল অর্জন করতে যুক্ত হন
            </p>
        </div>

        <?php if ($error): ?>
            <div class="p-4 rounded-xl bg-rose-500/10 border border-rose-500 text-rose-600 dark:text-rose-400 text-xs font-bold flex items-center gap-2">
                <i data-lucide="alert-circle" class="w-4 h-4 shrink-0"></i>
                <span><?= sanitize($error) ?></span>
            </div>
        <?php endif; ?>

        <!-- Registration Card -->
        <div class="p-8 rounded-3xl glass-panel border border-slate-200 dark:border-slate-800 shadow-2xl space-y-6">
            <form method="POST" action="register.php" class="space-y-4 text-xs">
                <div>
                    <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">পূর্ণ নাম *</label>
                    <input type="text" name="name" required placeholder="আপনার পুরো নাম" class="w-full px-4 py-3 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm">
                </div>

                <div>
                    <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">ইমেইল এড্রেস *</label>
                    <input type="email" name="email" required placeholder="name@example.com" class="w-full px-4 py-3 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm">
                </div>

                <div>
                    <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">মোবাইল নম্বর *</label>
                    <input type="tel" name="phone" required placeholder="018XXXXXXXX" class="w-full px-4 py-3 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm">
                </div>

                <div>
                    <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">পাসওয়ার্ড * (ন্যূনতম ৬ অক্ষর)</label>
                    <input type="password" name="password" required placeholder="••••••••" class="w-full px-4 py-3 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm">
                </div>

                <div>
                    <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">ভূমিকা (Role)</label>
                    <select name="role" class="w-full px-4 py-3 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm">
                        <option value="student">শিক্ষার্থী (Student)</option>
                        <option value="teacher">ইন্সট্রাক্টর / ট্রেনার (Teacher)</option>
                    </select>
                </div>

                <div class="pt-2">
                    <button type="submit" class="w-full py-3.5 rounded-xl bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white font-extrabold text-sm shadow-xl shadow-blue-500/25 transition transform hover:-translate-y-0.5">
                        অ্যাকাউন্ট তৈরি করুন
                    </button>
                </div>
            </form>

            <div class="pt-4 border-t border-slate-100 dark:border-slate-800 text-center text-xs text-slate-500">
                ইতিমধ্যে একাউন্ট আছে? <a href="login.php" class="font-bold text-blue-600 dark:text-cyan-400 hover:underline">লগইন করুন</a>
            </div>
        </div>

    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
