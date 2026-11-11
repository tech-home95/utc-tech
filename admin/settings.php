<?php
/**
 * Universal Technology Corporation (UTC) - Admin Institute Settings
 */

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/auth.php';
require_once __DIR__ . '/../config/helpers.php';

requireRole('admin');
$user = getCurrentUser();

if (($_SERVER['REQUEST_METHOD'] ?? '') === 'POST' && isset($_POST['save_settings'])) {
    $keys = [
        'institute_name', 'address', 'phone_primary', 'phone_secondary', 'email',
        'bkash_number', 'nagad_number', 'rocket_number', 'bank_info', 'tagline'
    ];

    $upd = $pdo->prepare("INSERT OR REPLACE INTO settings (setting_key, setting_value) VALUES (?, ?)");
    foreach ($keys as $k) {
        $val = sanitize($_POST[$k] ?? '');
        $upd->execute([$k, $val]);
    }
    setFlash('success', 'প্রতিষ্ঠানের তথ্য ও পেমেন্ট সেটিংস সফলভাবে আপডেট হয়েছে!');
    header("Location: settings.php");
    exit;
}

$pageTitle = 'ওয়েবসাইট সেটিংস ও ক্যাম্পাস কনফিগারেশন | Admin Panel | UTC';
require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/navbar.php';
?>

<div class="py-12 bg-slate-50 dark:bg-[#080d1a] min-h-[calc(100vh-80px)] transition-colors duration-300">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8">
        
        <div class="flex items-center justify-between pb-6 border-b border-slate-200 dark:border-slate-800">
            <div>
                <a href="index.php" class="text-xs font-bold text-purple-600 dark:text-purple-400 hover:underline flex items-center gap-1 mb-1">
                    <i data-lucide="arrow-left" class="w-3.5 h-3.5"></i> অ্যাডমিন ড্যাশবোর্ড
                </a>
                <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 dark:text-white">
                    ক্যাম্পাস ও পেমেন্ট গেটওয়ে সেটিংস
                </h1>
                <p class="text-xs sm:text-sm text-slate-500 mt-1">
                    প্রতিষ্ঠান সংক্রান্ত তথ্য, মোবাইল ব্যাংকিং নম্বর এবং হেল্পলাইন পরিবর্তন করুন।
                </p>
            </div>
        </div>

        <?= renderFlash() ?>

        <form method="POST" class="p-8 rounded-3xl glass-panel border border-slate-200 dark:border-slate-800 shadow-2xl space-y-6 text-xs">
            
            <!-- Institute Info -->
            <div class="space-y-4">
                <h3 class="text-base font-bold text-slate-900 dark:text-white pb-2 border-b border-slate-100 dark:border-slate-800">
                    প্রতিষ্ঠান পরিচিতি ও ঠিকানা
                </h3>

                <div>
                    <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">প্রতিষ্ঠানের নাম</label>
                    <input type="text" name="institute_name" value="<?= sanitize(getSetting('institute_name', 'Universal Technology Corporation (UTC)')) ?>" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white font-bold text-sm">
                </div>

                <div>
                    <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">ঠিকানা (Address)</label>
                    <input type="text" name="address" value="<?= sanitize(getSetting('address', '287 (1st Floor), Doctor Para, Feni-3900')) ?>" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white">
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">হেল্পলাইন ১ (Phone Primary)</label>
                        <input type="text" name="phone_primary" value="<?= sanitize(getSetting('phone_primary', '01812669757')) ?>" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white font-bold">
                    </div>
                    <div>
                        <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">হেল্পলাইন ২ (Phone Secondary)</label>
                        <input type="text" name="phone_secondary" value="<?= sanitize(getSetting('phone_secondary', '01817122483')) ?>" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white font-bold">
                    </div>
                </div>

                <div>
                    <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">অফিসিয়াল ইমেইল</label>
                    <input type="email" name="email" value="<?= sanitize(getSetting('email', 'utcsoftltd@gmail.com')) ?>" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white">
                </div>

                <div>
                    <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">ট্যাগলাইন (Slogan)</label>
                    <input type="text" name="tagline" value="<?= sanitize(getSetting('tagline', 'Learn Today | Skill Up Tomorrow | Build Your Career with UTC')) ?>" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white">
                </div>
            </div>

            <!-- Payment Accounts Info -->
            <div class="space-y-4 pt-4 border-t border-slate-100 dark:border-slate-800">
                <h3 class="text-base font-bold text-slate-900 dark:text-white pb-2 border-b border-slate-100 dark:border-slate-800">
                    মোবাইল ফিনান্সিয়াল ও ব্যাংক পেমেন্ট একাউন্ট
                </h3>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <div>
                        <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">বিকাশ (bKash Number)</label>
                        <input type="text" name="bkash_number" value="<?= sanitize(getSetting('bkash_number', '01812669757')) ?>" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white font-mono">
                    </div>
                    <div>
                        <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">নগদ (Nagad Number)</label>
                        <input type="text" name="nagad_number" value="<?= sanitize(getSetting('nagad_number', '01817122483')) ?>" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white font-mono">
                    </div>
                    <div>
                        <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">রকেট (Rocket Number)</label>
                        <input type="text" name="rocket_number" value="<?= sanitize(getSetting('rocket_number', '01812669757-9')) ?>" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white font-mono">
                    </div>
                </div>

                <div>
                    <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">ব্যাংক একাউন্ট বিবরণ (Bank Transfer Info)</label>
                    <textarea name="bank_info" rows="2" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white"><?= sanitize(getSetting('bank_info', 'Islami Bank Bangladesh Ltd, Feni Branch, A/C: 20501234567890, A/C Name: Universal Technology Corporation')) ?></textarea>
                </div>
            </div>

            <div class="pt-4 border-t border-slate-100 dark:border-slate-800 flex justify-end">
                <button type="submit" name="save_settings" class="px-6 py-3 rounded-xl bg-purple-600 hover:bg-purple-500 text-white font-bold transition shadow-lg shadow-purple-500/25">
                    সেটিংস সংরক্ষণ করুন
                </button>
            </div>

        </form>

    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
