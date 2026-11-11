<?php
/**
 * Universal Technology Corporation (UTC) - All Courses Page
 */

$pageTitle = 'সকল আইটি কোর্সসমূহ | Universal Technology Corporation (UTC)';
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/config/auth.php';
require_once __DIR__ . '/config/helpers.php';

// Filter & Search
$category = isset($_GET['category']) ? trim($_GET['category']) : '';
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$sort = isset($_GET['sort']) ? trim($_GET['sort']) : '';

$sql = "SELECT * FROM courses WHERE status = 'active'";
$params = [];

if ($category) {
    $sql .= " AND category = ?";
    $params[] = $category;
}

if ($search) {
    $sql .= " AND (title LIKE ? OR short_desc LIKE ? OR full_desc LIKE ?)";
    $params[] = "%$search%";
    $params[] = "%$search%";
    $params[] = "%$search%";
}

if ($sort === 'price_low') {
    $sql .= " ORDER BY discount_price ASC";
} elseif ($sort === 'price_high') {
    $sql .= " ORDER BY discount_price DESC";
} else {
    $sql .= " ORDER BY id ASC";
}

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$courses = $stmt->fetchAll();

// Get unique categories for filter pills
$catStmt = $pdo->query("SELECT DISTINCT category FROM courses WHERE status = 'active'");
$categories = $catStmt->fetchAll(PDO::FETCH_COLUMN);

require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/navbar.php';
?>

<div class="py-12 bg-slate-100/60 dark:bg-[#070b16] border-b border-slate-200 dark:border-slate-800 transition-colors">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center space-y-4">
        <span class="px-3.5 py-1 rounded-full text-xs font-extrabold bg-blue-500/10 text-blue-600 dark:text-cyan-400 border border-blue-500/20 uppercase tracking-wider">
            UTC Career Ready Programs
        </span>
        <h1 class="text-3xl sm:text-4xl font-extrabold text-slate-900 dark:text-white">
            প্রফেশনাল আইটি ও প্রোগ্রামিং কোর্সসমূহ
        </h1>
        <p class="text-slate-600 dark:text-slate-400 text-sm max-w-2xl mx-auto">
            বেসিক থেকে অ্যাডভান্সড লেভেলের ১০টি ক্যারিয়ার ওরিয়েন্টেড কোর্স। আন্তর্জাতিক মানের সিলেবাস ও ৫০% - ৩০% স্পেশাল ডিসকাউন্ট।
        </p>

        <!-- Search & Filter Form -->
        <form method="GET" action="courses.php" class="max-w-3xl mx-auto pt-4 flex flex-col sm:flex-row gap-3">
            <div class="relative flex-1">
                <i data-lucide="search" class="w-5 h-5 absolute left-3.5 top-3.5 text-slate-400"></i>
                <input type="text" name="search" value="<?= sanitize($search) ?>" placeholder="কোর্সের নাম বা বিষয় দিয়ে সার্চ করুন..." class="w-full pl-11 pr-4 py-3 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>
            
            <select name="sort" class="px-4 py-3 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                <option value="">সাজান (Default)</option>
                <option value="price_low" <?= $sort === 'price_low' ? 'selected' : '' ?>>ফি: কম থেকে বেশি</option>
                <option value="price_high" <?= $sort === 'price_high' ? 'selected' : '' ?>>ফি: বেশি থেকে কম</option>
            </select>

            <button type="submit" class="px-6 py-3 rounded-xl bg-blue-600 hover:bg-blue-500 text-white font-bold text-sm shadow-md transition">
                ফিল্টার করুন
            </button>
        </form>

        <!-- Category Pills -->
        <div class="flex flex-wrap items-center justify-center gap-2 pt-4">
            <a href="courses.php" class="px-3.5 py-1.5 rounded-full text-xs font-bold transition <?= empty($category) ? 'bg-blue-600 text-white' : 'bg-white dark:bg-slate-800 text-slate-700 dark:text-slate-300 border border-slate-200 dark:border-slate-700 hover:border-blue-500' ?>">
                সকল কোর্স
            </a>
            <?php foreach ($categories as $cat): ?>
                <a href="courses.php?category=<?= urlencode($cat) ?>" class="px-3.5 py-1.5 rounded-full text-xs font-bold transition <?= ($category === $cat) ? 'bg-blue-600 text-white' : 'bg-white dark:bg-slate-800 text-slate-700 dark:text-slate-300 border border-slate-200 dark:border-slate-700 hover:border-blue-500' ?>">
                    <?= sanitize($cat) ?>
                </a>
            <?php endforeach; ?>
        </div>
    </div>
</div>

<div class="py-16 bg-slate-50 dark:bg-[#080d1a] transition-colors">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <?php if (empty($courses)): ?>
            <div class="text-center py-16 space-y-4">
                <div class="w-16 h-16 rounded-full bg-slate-200 dark:bg-slate-800 text-slate-500 flex items-center justify-center mx-auto">
                    <i data-lucide="book-x" class="w-8 h-8"></i>
                </div>
                <h3 class="text-xl font-bold text-slate-800 dark:text-white">কোন কোর্স পাওয়া যায়নি</h3>
                <p class="text-slate-500 text-sm">অন্য কি-ওয়ার্ড দিয়ে সার্চ করুন অথবা সব কোর্স ফিল্টারে ক্লিক করুন।</p>
                <a href="courses.php" class="inline-block px-5 py-2.5 rounded-xl bg-blue-600 text-white text-sm font-bold">সকল কোর্স দেখুন</a>
            </div>
        <?php else: ?>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                <?php foreach ($courses as $course): ?>
                    <?php 
                        $isTopCombo = ($course['id'] == 1);
                        $discountBadge = $course['discount_percent'] ? $course['discount_percent'] . '% OFF' : '30% OFF';
                    ?>
                    <div class="tilt-card card-3d rounded-3xl glass-panel border border-slate-200 dark:border-slate-800/80 overflow-hidden flex flex-col justify-between group hover:border-blue-500/50 transition-all duration-300 shadow-lg shadow-slate-200/50 dark:shadow-none">
                        
                        <div class="p-6 space-y-4">
                            <div class="flex items-start justify-between gap-2">
                                <span class="px-3 py-1 rounded-full text-xs font-bold <?= $isTopCombo ? 'bg-red-500 text-white animate-pulse' : 'bg-blue-100 dark:bg-blue-900/60 text-blue-700 dark:text-blue-300' ?>">
                                    <?= sanitize($course['category']) ?>
                                </span>
                                <span class="badge-discount px-3 py-1 rounded-full text-white text-xs font-black shrink-0">
                                    <?= $discountBadge ?>
                                </span>
                            </div>

                            <h3 class="text-lg font-bold text-slate-900 dark:text-white group-hover:text-blue-600 dark:group-hover:text-cyan-400 transition-colors line-clamp-2 leading-snug">
                                <a href="course-details.php?id=<?= $course['id'] ?>">
                                    <?= sanitize($course['title']) ?>
                                </a>
                            </h3>

                            <p class="text-xs text-slate-500 dark:text-slate-400 line-clamp-3 leading-relaxed">
                                <?= sanitize($course['short_desc']) ?>
                            </p>

                            <div class="grid grid-cols-2 gap-2 pt-2 border-t border-slate-100 dark:border-slate-800 text-xs text-slate-600 dark:text-slate-400">
                                <div class="flex items-center gap-1.5">
                                    <i data-lucide="clock" class="w-4 h-4 text-blue-500"></i>
                                    <span><?= sanitize($course['duration']) ?></span>
                                </div>
                                <div class="flex items-center gap-1.5">
                                    <i data-lucide="video" class="w-4 h-4 text-emerald-500"></i>
                                    <span><?= $course['total_classes'] ?> টি লাইভ ক্লাস</span>
                                </div>
                                <div class="flex items-center gap-1.5">
                                    <i data-lucide="bar-chart" class="w-4 h-4 text-amber-500"></i>
                                    <span><?= sanitize($course['level']) ?></span>
                                </div>
                                <div class="flex items-center gap-1.5">
                                    <i data-lucide="award" class="w-4 h-4 text-purple-500"></i>
                                    <span>সার্টিফিকেট ও প্রজেক্ট</span>
                                </div>
                            </div>
                        </div>

                        <div class="p-6 bg-slate-50/80 dark:bg-slate-900/40 border-t border-slate-100 dark:border-slate-800/80 flex items-center justify-between">
                            <div>
                                <span class="text-xs text-slate-400 line-through block font-medium">
                                    <?= formatBDT($course['original_price']) ?>
                                </span>
                                <div class="text-xl font-black text-slate-900 dark:text-white">
                                    <span class="text-blue-600 dark:text-cyan-400"><?= formatBDT($course['discount_price']) ?></span>
                                </div>
                            </div>
                            <div class="flex items-center gap-2">
                                <a href="course-details.php?id=<?= $course['id'] ?>" class="px-3 py-2 rounded-xl border border-slate-200 dark:border-slate-700 hover:bg-slate-200 dark:hover:bg-slate-800 text-xs font-bold text-slate-700 dark:text-slate-200 transition">
                                    বিস্তারিত
                                </a>
                                <a href="checkout.php?course_id=<?= $course['id'] ?>" class="px-4 py-2 rounded-xl bg-blue-600 hover:bg-blue-500 text-white text-xs font-bold shadow-md shadow-blue-500/25 transition">
                                    ভর্তি হন
                                </a>
                            </div>
                        </div>

                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
