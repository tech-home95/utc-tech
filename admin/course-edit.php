<?php
/**
 * Universal Technology Corporation (UTC) - Admin Course Edit / Create
 */

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/auth.php';
require_once __DIR__ . '/../config/helpers.php';

requireRole('admin');
$user = getCurrentUser();

$courseId = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$course = null;

if ($courseId > 0) {
    $cStmt = $pdo->prepare("SELECT * FROM courses WHERE id = ?");
    $cStmt->execute([$courseId]);
    $course = $cStmt->fetch();
}

// Fetch Teachers for dropdown
$tStmt = $pdo->query("SELECT id, name FROM users WHERE role = 'teacher' ORDER BY name ASC");
$teachers = $tStmt->fetchAll();

// Handle Save / Update
if (($_SERVER['REQUEST_METHOD'] ?? '') === 'POST') {
    $title = sanitize($_POST['title'] ?? '');
    $slug = sanitize($_POST['slug'] ?? strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $title))));
    $category = sanitize($_POST['category'] ?? '');
    $originalPrice = (float)($_POST['original_price'] ?? 0);
    $discountPrice = (float)($_POST['discount_price'] ?? 0);
    $discountPercent = (int)($_POST['discount_percent'] ?? 0);
    $duration = sanitize($_POST['duration'] ?? '4 Months');
    $totalClasses = (int)($_POST['total_classes'] ?? 36);
    $level = sanitize($_POST['level'] ?? 'Beginner to Advanced');
    $teacherId = !empty($_POST['teacher_id']) ? (int)$_POST['teacher_id'] : null;
    $shortDesc = sanitize($_POST['short_desc'] ?? '');
    $fullDesc = trim($_POST['full_desc'] ?? '');
    $whyUtc = trim($_POST['why_choose_utc'] ?? '');

    if ($courseId > 0) {
        $upd = $pdo->prepare("UPDATE courses SET 
            title = ?, slug = ?, category = ?, original_price = ?, discount_price = ?, discount_percent = ?,
            duration = ?, total_classes = ?, level = ?, teacher_id = ?, short_desc = ?, full_desc = ?, why_choose_utc = ?
            WHERE id = ?");
        $upd->execute([
            $title, $slug, $category, $originalPrice, $discountPrice, $discountPercent,
            $duration, $totalClasses, $level, $teacherId, $shortDesc, $fullDesc, $whyUtc, $courseId
        ]);
        setFlash('success', 'কোর্স তথ্য সফলভাবে আপডেট হয়েছে!');
    } else {
        $ins = $pdo->prepare("INSERT INTO courses (
            title, slug, category, original_price, discount_price, discount_percent,
            duration, total_classes, level, teacher_id, short_desc, full_desc, why_choose_utc, status
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'active')");
        $ins->execute([
            $title, $slug, $category, $originalPrice, $discountPrice, $discountPercent,
            $duration, $totalClasses, $level, $teacherId, $shortDesc, $fullDesc, $whyUtc
        ]);
        $courseId = $pdo->lastInsertId();
        setFlash('success', 'নতুন কোর্স সফলভাবে তৈরি হয়েছে!');
    }

    header("Location: courses.php");
    exit;
}

$pageTitle = ($course ? 'কোর্স এডিট: ' . $course['title'] : 'নতুন কোর্স তৈরি') . ' | Admin | UTC';
require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/navbar.php';
?>

<div class="py-12 bg-slate-50 dark:bg-[#080d1a] min-h-[calc(100vh-80px)] transition-colors duration-300">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8">
        
        <div class="flex items-center justify-between pb-6 border-b border-slate-200 dark:border-slate-800">
            <div>
                <a href="courses.php" class="text-xs font-bold text-purple-600 dark:text-purple-400 hover:underline flex items-center gap-1 mb-1">
                    <i data-lucide="arrow-left" class="w-3.5 h-3.5"></i> কোর্স তালিকায় ফিরুন
                </a>
                <h1 class="text-2xl font-extrabold text-slate-900 dark:text-white">
                    <?= $course ? 'কোর্স এডিট ও কনফিগারেশন' : 'নতুন আইটি কোর্স তৈরি করুন' ?>
                </h1>
            </div>
        </div>

        <form method="POST" class="p-8 rounded-3xl glass-panel border border-slate-200 dark:border-slate-800 shadow-2xl space-y-6 text-xs">
            
            <div class="space-y-4">
                <div>
                    <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">কোর্সের সম্পূর্ণ শিরোনাম *</label>
                    <input type="text" name="title" value="<?= sanitize($course['title'] ?? '') ?>" required placeholder="Course Name" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white text-sm">
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">ক্যাটাগরি *</label>
                        <input type="text" name="category" value="<?= sanitize($course['category'] ?? 'Software & Programming') ?>" required class="w-full px-4 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white">
                    </div>
                    <div>
                        <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">ইউআরএল স্লাগ (Slug)</label>
                        <input type="text" name="slug" value="<?= sanitize($course['slug'] ?? '') ?>" placeholder="course-slug-name" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white font-mono">
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <div>
                        <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">নিয়মিত ফি (Original Price) *</label>
                        <input type="number" name="original_price" value="<?= $course['original_price'] ?? 20000 ?>" required class="w-full px-4 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white font-bold">
                    </div>
                    <div>
                        <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">অফার ফি (Discount Price) *</label>
                        <input type="number" name="discount_price" value="<?= $course['discount_price'] ?? 10000 ?>" required class="w-full px-4 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white font-bold text-blue-600 dark:text-cyan-400">
                    </div>
                    <div>
                        <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">ডিসকাউন্ট শতকরা (%)</label>
                        <input type="number" name="discount_percent" value="<?= $course['discount_percent'] ?? 50 ?>" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white">
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <div>
                        <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">সময়কাল (Duration)</label>
                        <input type="text" name="duration" value="<?= sanitize($course['duration'] ?? '5 Months') ?>" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white">
                    </div>
                    <div>
                        <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">মোট ক্লাস সংখ্যা</label>
                        <input type="number" name="total_classes" value="<?= $course['total_classes'] ?? 45 ?>" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white">
                    </div>
                    <div>
                        <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">এসাইনকৃত ট্রেনার</label>
                        <select name="teacher_id" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white">
                            <option value="">নির্বাচন করুন (Not Assigned)</option>
                            <?php foreach ($teachers as $t): ?>
                                <option value="<?= $t['id'] ?>" <?= (isset($course['teacher_id']) && $course['teacher_id'] == $t['id']) ? 'selected' : '' ?>>
                                    <?= sanitize($t['name']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <div>
                    <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">সংক্ষিপ্ত বিবরণ (Short Description)</label>
                    <textarea name="short_desc" rows="2" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white"><?= sanitize($course['short_desc'] ?? '') ?></textarea>
                </div>

                <div>
                    <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">সম্পূর্ণ প্রমোশনাল বিবরণ ও টেক্সট (Full Bengali Promotional Text)</label>
                    <textarea name="full_desc" rows="8" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white font-bengali"><?= htmlspecialchars($course['full_desc'] ?? '') ?></textarea>
                </div>

                <div>
                    <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">কেন UTC বেছে নেবেন? (Why Choose UTC)</label>
                    <textarea name="why_choose_utc" rows="6" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white font-bengali"><?= htmlspecialchars($course['why_choose_utc'] ?? '') ?></textarea>
                </div>
            </div>

            <div class="pt-4 border-t border-slate-100 dark:border-slate-800 flex items-center justify-end gap-3">
                <a href="courses.php" class="px-5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 text-slate-600 dark:text-slate-300 font-bold hover:bg-slate-100 transition">
                    বাতিল
                </a>
                <button type="submit" class="px-6 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-500 text-white font-bold shadow-lg transition">
                    সংরক্ষণ করুন
                </button>
            </div>

        </form>

    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
