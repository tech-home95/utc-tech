<?php
/**
 * Universal Technology Corporation (UTC) - Student Profile
 */

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/auth.php';
require_once __DIR__ . '/../config/helpers.php';

requireRole('student');
$user = getCurrentUser();

if (($_SERVER['REQUEST_METHOD'] ?? '') === 'POST') {
    if (isset($_POST['update_profile'])) {
        $name = sanitize($_POST['name'] ?? '');
        $phone = sanitize($_POST['phone'] ?? '');
        $bio = sanitize($_POST['bio'] ?? '');

        $stmt = $pdo->prepare("UPDATE users SET name = ?, phone = ?, bio = ? WHERE id = ?");
        $stmt->execute([$name, $phone, $bio, $user['id']]);
        $_SESSION['user_name'] = $name;
        setFlash('success', 'আপনার প্রোফাইল তথ্য সফলভাবে আপডেট করা হয়েছে!');
        header("Location: profile.php");
        exit;
    }

    if (isset($_POST['change_password'])) {
        $currentPass = trim($_POST['current_password'] ?? '');
        $newPass = trim($_POST['new_password'] ?? '');

        $uStmt = $pdo->prepare("SELECT password FROM users WHERE id = ?");
        $uStmt->execute([$user['id']]);
        $currHash = $uStmt->fetchColumn();

        if (password_verify($currentPass, $currHash)) {
            if (strlen($newPass) >= 6) {
                $newHash = password_hash($newPass, PASSWORD_DEFAULT);
                $pdo->prepare("UPDATE users SET password = ? WHERE id = ?")->execute([$newHash, $user['id']]);
                setFlash('success', 'পাসওয়ার্ড সফলভাবে পরিবর্তন করা হয়েছে!');
            } else {
                setFlash('error', 'নতুন পাসওয়ার্ড ন্যূনতম ৬ অক্ষরের হতে হবে।');
            }
        } else {
            setFlash('error', 'বর্তমান পাসওয়ার্ড সঠিক নয়!');
        }
        header("Location: profile.php");
        exit;
    }
}

$pageTitle = 'প্রোফাইল সেটিংস | Student Portal | UTC';
require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/navbar.php';
?>

<div class="py-12 bg-slate-50 dark:bg-[#080d1a] min-h-[calc(100vh-80px)] transition-colors duration-300">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8">
        
        <div class="pb-6 border-b border-slate-200 dark:border-slate-800">
            <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 dark:text-white">
                প্রোফাইল ও একাউন্ট সেটিংস
            </h1>
            <p class="text-xs sm:text-sm text-slate-500 mt-1">
                আপনার ব্যক্তিগত তথ্য এবং নিরাপত্তা সেটিংস পরিচালনা করুন।
            </p>
        </div>

        <?= renderFlash() ?>

        <div class="grid grid-cols-1 md:grid-cols-12 gap-8 items-start">
            
            <!-- Left Profile Form -->
            <div class="md:col-span-7 space-y-6">
                <div class="p-8 rounded-3xl glass-panel border border-slate-200 dark:border-slate-800 shadow-xl space-y-6">
                    <h3 class="text-base font-bold text-slate-900 dark:text-white pb-3 border-b border-slate-100 dark:border-slate-800">
                        ব্যক্তিগত তথ্য
                    </h3>

                    <form method="POST" class="space-y-4 text-xs">
                        <div>
                            <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">পূর্ণ নাম</label>
                            <input type="text" name="name" value="<?= sanitize($user['name']) ?>" required class="w-full px-4 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>

                        <div>
                            <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">ইমেইল এড্রেস (পরিবর্তনযোগ্য নয়)</label>
                            <input type="email" value="<?= sanitize($user['email']) ?>" disabled class="w-full px-4 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-100 dark:bg-slate-900 text-slate-500 cursor-not-allowed">
                        </div>

                        <div>
                            <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">মোবাইল নম্বর</label>
                            <input type="tel" name="phone" value="<?= sanitize($user['phone'] ?? '') ?>" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>

                        <div>
                            <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">সংক্ষিপ্ত বায়ো / পড়াশোনার বিবরণ</label>
                            <textarea name="bio" rows="3" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500"><?= sanitize($user['bio'] ?? '') ?></textarea>
                        </div>

                        <button type="submit" name="update_profile" class="px-5 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-500 text-white font-bold text-xs shadow-md transition">
                            তথ্য সংরক্ষণ করুন
                        </button>
                    </form>
                </div>
            </div>

            <!-- Right Password Change Form -->
            <div class="md:col-span-5 space-y-6">
                <div class="p-8 rounded-3xl glass-panel border border-slate-200 dark:border-slate-800 shadow-xl space-y-6">
                    <h3 class="text-base font-bold text-slate-900 dark:text-white pb-3 border-b border-slate-100 dark:border-slate-800">
                        পাসওয়ার্ড পরিবর্তন
                    </h3>

                    <form method="POST" class="space-y-4 text-xs">
                        <div>
                            <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">বর্তমান পাসওয়ার্ড</label>
                            <input type="password" name="current_password" required placeholder="••••••••" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>

                        <div>
                            <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">নতুন পাসওয়ার্ড (ন্যূনতম ৬ অক্ষর)</label>
                            <input type="password" name="new_password" required placeholder="••••••••" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>

                        <button type="submit" name="change_password" class="w-full py-2.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-white font-bold text-xs transition">
                            পাসওয়ার্ড আপডেট করুন
                        </button>
                    </form>
                </div>
            </div>

        </div>

    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
