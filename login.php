<?php
/**
 * Universal Technology Corporation (UTC) - Login Page
 */

require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/config/auth.php';
require_once __DIR__ . '/config/helpers.php';

if (isLoggedIn()) {
    $role = $_SESSION['user_role'] ?? 'student';
    if ($role === 'admin') header("Location: admin/index.php");
    elseif ($role === 'teacher') header("Location: teacher/index.php");
    else header("Location: student/index.php");
    exit;
}

$error = '';

if (($_SERVER['REQUEST_METHOD'] ?? '') === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = trim($_POST['password'] ?? '');

    if (empty($email) || empty($password)) {
        $error = 'ইমেইল এবং পাসওয়ার্ড প্রদান করুন।';
    } else {
        $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password'])) {
            loginUser($user);
            setFlash('success', "স্বাগতম {$user['name']}! সফলভাবে লগইন হয়েছে।");

            if ($user['role'] === 'admin') {
                header("Location: admin/index.php");
            } elseif ($user['role'] === 'teacher') {
                header("Location: teacher/index.php");
            } else {
                header("Location: student/index.php");
            }
            exit;
        } else {
            $error = 'ভুল ইমেইল অথবা পাসওয়ার্ড!';
        }
    }
}

$pageTitle = 'লগইন (Login) | Universal Technology Corporation (UTC)';
require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/navbar.php';
?>

<div class="py-16 bg-slate-50 dark:bg-[#080d1a] min-h-[calc(100vh-80px)] flex items-center justify-center transition-colors duration-300">
    <div class="max-w-md w-full mx-4 space-y-6">
        
        <div class="text-center space-y-2">
            <img src="<?= $appRoot ?>assets/images/utc-logo.png" alt="UTC Logo" class="w-16 h-16 rounded-full object-contain mx-auto shadow-2xl bg-white p-1 border-2 border-blue-500/30">
            <h1 class="text-2xl font-extrabold text-slate-900 dark:text-white">
                পোর্টাল লগইন
            </h1>
            <p class="text-xs text-slate-500 dark:text-slate-400">
                Universal Technology Corporation লার্নিং প্ল্যাটফর্মে স্বাগতম
            </p>
        </div>

        <?= renderFlash() ?>

        <?php if ($error): ?>
            <div class="p-4 rounded-xl bg-rose-500/10 border border-rose-500 text-rose-600 dark:text-rose-400 text-xs font-bold flex items-center gap-2">
                <i data-lucide="alert-circle" class="w-4 h-4 shrink-0"></i>
                <span><?= sanitize($error) ?></span>
            </div>
        <?php endif; ?>

        <!-- Quick Demo Accounts Auto-Fill Pills -->
        <div class="p-4 rounded-2xl glass-panel border border-blue-500/20 text-xs space-y-2.5">
            <span class="font-bold text-slate-700 dark:text-slate-300 block">⚡ দ্রুত টেস্ট করার জন্য ডেমো একাউন্ট নির্বাচন করুন:</span>
            <div class="grid grid-cols-3 gap-2">
                <button type="button" onclick="fillDemo('admin@utc.edu.bd', 'admin123')" class="px-2.5 py-1.5 rounded-lg bg-purple-500/10 text-purple-600 dark:text-purple-400 border border-purple-500/30 hover:bg-purple-600 hover:text-white text-[11px] font-bold transition">
                    👑 Admin
                </button>
                <button type="button" onclick="fillDemo('teacher@utc.edu.bd', 'teacher123')" class="px-2.5 py-1.5 rounded-lg bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 border border-emerald-500/30 hover:bg-emerald-600 hover:text-white text-[11px] font-bold transition">
                    🎓 Teacher
                </button>
                <button type="button" onclick="fillDemo('student@utc.edu.bd', 'student123')" class="px-2.5 py-1.5 rounded-lg bg-blue-500/10 text-blue-600 dark:text-blue-400 border border-blue-500/30 hover:bg-blue-600 hover:text-white text-[11px] font-bold transition">
                    💻 Student
                </button>
            </div>
        </div>

        <!-- Login Form -->
        <div class="p-8 rounded-3xl glass-panel border border-slate-200 dark:border-slate-800 shadow-2xl space-y-6">
            <form method="POST" action="login.php" class="space-y-4 text-xs">
                <div>
                    <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">ইমেইল এড্রেস</label>
                    <div class="relative">
                        <i data-lucide="mail" class="w-4 h-4 absolute left-3.5 top-3.5 text-slate-400"></i>
                        <input type="email" id="login-email" name="email" required placeholder="name@example.com" class="w-full pl-10 pr-4 py-3 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm">
                    </div>
                </div>

                <div>
                    <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">পাসওয়ার্ড</label>
                    <div class="relative">
                        <i data-lucide="lock" class="w-4 h-4 absolute left-3.5 top-3.5 text-slate-400"></i>
                        <input type="password" id="login-pass" name="password" required placeholder="••••••••" class="w-full pl-10 pr-4 py-3 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm">
                    </div>
                </div>

                <div class="flex items-center justify-between text-[11px]">
                    <label class="flex items-center gap-2 cursor-pointer text-slate-600 dark:text-slate-400">
                        <input type="checkbox" checked class="rounded border-slate-300 text-blue-600 focus:ring-blue-500">
                        <span>মনে রাখুন (Remember me)</span>
                    </label>
                    <a href="contact.php" class="text-blue-600 dark:text-cyan-400 hover:underline">পাসওয়ার্ড ভুলে গেছেন?</a>
                </div>

                <button type="submit" class="w-full py-3.5 rounded-xl bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white font-extrabold text-sm shadow-xl shadow-blue-500/25 transition transform hover:-translate-y-0.5">
                    লগইন করুন
                </button>
            </form>

            <div class="pt-4 border-t border-slate-100 dark:border-slate-800 text-center text-xs text-slate-500">
                নতুন শিক্ষার্থী? <a href="register.php" class="font-bold text-blue-600 dark:text-cyan-400 hover:underline">নতুন একাউন্ট তৈরি করুন</a>
            </div>
        </div>

    </div>
</div>

<script>
function fillDemo(email, pass) {
    document.getElementById('login-email').value = email;
    document.getElementById('login-pass').value = pass;
    showToast('ডেমো তথ্য পূরণ করা হয়েছে! লগইন করুন চাপুন।', 'info');
}
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
