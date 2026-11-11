<?php
/**
 * Universal Technology Corporation (UTC) - Course Details
 */

require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/config/auth.php';
require_once __DIR__ . '/config/helpers.php';

$courseId = isset($_GET['id']) ? (int)$_GET['id'] : 1;

$stmt = $pdo->prepare("SELECT c.*, u.name as teacher_name, u.bio as teacher_bio 
                       FROM courses c 
                       LEFT JOIN users u ON c.teacher_id = u.id 
                       WHERE c.id = ?");
$stmt->execute([$courseId]);
$course = $stmt->fetch();

if (!$course) {
    header("Location: courses.php");
    exit;
}

// Fetch modules & lessons
$modStmt = $pdo->prepare("SELECT * FROM modules WHERE course_id = ? ORDER BY sort_order ASC");
$modStmt->execute([$courseId]);
$modules = $modStmt->fetchAll();

foreach ($modules as &$mod) {
    $lesStmt = $pdo->prepare("SELECT * FROM lessons WHERE module_id = ? ORDER BY sort_order ASC");
    $lesStmt->execute([$mod['id']]);
    $mod['lessons'] = $lesStmt->fetchAll();
}
unset($mod);

$pageTitle = $course['title'] . ' | UTC';
require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/navbar.php';
?>

<!-- Course Header Banner -->
<div class="py-14 bg-gradient-to-b from-blue-900 via-indigo-950 to-slate-950 text-white relative overflow-hidden">
    <div class="absolute inset-0 bg-[radial-gradient(#38bdf8_1px,transparent_1px)] [background-size:24px_24px] opacity-20"></div>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
        
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
            
            <div class="lg:col-span-8 space-y-4">
                <div class="flex flex-wrap items-center gap-2">
                    <span class="px-3 py-1 rounded-full text-xs font-bold bg-blue-500/20 text-blue-300 border border-blue-500/30">
                        <?= sanitize($course['category']) ?>
                    </span>
                    <span class="badge-discount px-3 py-1 rounded-full text-white text-xs font-black">
                        <?= $course['discount_percent'] ? $course['discount_percent'] . '% OFF' : '30% OFF' ?>
                    </span>
                    <span class="px-3 py-1 rounded-full text-xs font-semibold bg-emerald-500/20 text-emerald-300">
                        ব্যাচে সিট সীমিত
                    </span>
                </div>

                <h1 class="text-2xl sm:text-3xl lg:text-4xl font-extrabold tracking-tight leading-snug">
                    <?= sanitize($course['title']) ?>
                </h1>

                <p class="text-slate-300 text-sm sm:text-base leading-relaxed">
                    <?= sanitize($course['short_desc']) ?>
                </p>

                <!-- Quick Metadata Bar -->
                <div class="flex flex-wrap items-center gap-6 pt-4 text-xs sm:text-sm text-slate-300 border-t border-slate-800">
                    <div class="flex items-center gap-2">
                        <i data-lucide="clock" class="w-4 h-4 text-cyan-400"></i>
                        <span>সময়কাল: <strong><?= sanitize($course['duration']) ?></strong></span>
                    </div>
                    <div class="flex items-center gap-2">
                        <i data-lucide="video" class="w-4 h-4 text-emerald-400"></i>
                        <span>মোট ক্লাস: <strong><?= $course['total_classes'] ?> টি</strong></span>
                    </div>
                    <div class="flex items-center gap-2">
                        <i data-lucide="layers" class="w-4 h-4 text-amber-400"></i>
                        <span>লেভেল: <strong><?= sanitize($course['level']) ?></strong></span>
                    </div>
                    <div class="flex items-center gap-2">
                        <i data-lucide="map-pin" class="w-4 h-4 text-rose-400"></i>
                        <span>ক্যাম্পাস: <strong>UTC Feni</strong></span>
                    </div>
                </div>

            </div>

        </div>

    </div>
</div>

<!-- Main Body: Curriculum, Bengali Copy & Sticky Enrollment Sidebar -->
<div class="py-14 bg-slate-50 dark:bg-[#080d1a] transition-colors duration-300">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-10 items-start">
            
            <!-- Left 8 Columns: Full Description & Curriculum -->
            <div class="lg:col-span-8 space-y-10">
                
                <!-- Full Course Description (Bengali Copy as required) -->
                <div class="p-8 rounded-3xl glass-panel border border-slate-200 dark:border-slate-800 shadow-md space-y-6">
                    <div class="flex items-center gap-3 pb-4 border-b border-slate-200 dark:border-slate-800">
                        <div class="w-10 h-10 rounded-xl bg-amber-500/10 text-amber-500 flex items-center justify-center font-bold">
                            <i data-lucide="sparkles" class="w-5 h-5"></i>
                        </div>
                        <h2 class="text-xl font-extrabold text-slate-900 dark:text-white">
                            কোর্স বিস্তারিত ও বিশেষ সুবিধা
                        </h2>
                    </div>

                    <div class="text-slate-700 dark:text-slate-300 text-sm leading-relaxed whitespace-pre-line font-bengali">
<?= sanitize($course['full_desc']) ?>
                    </div>
                </div>

                <!-- Curriculum & Modules Accordion -->
                <div class="p-8 rounded-3xl glass-panel border border-slate-200 dark:border-slate-800 shadow-md space-y-6">
                    <div class="flex items-center justify-between pb-4 border-b border-slate-200 dark:border-slate-800">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-xl bg-blue-500/10 text-blue-600 dark:text-cyan-400 flex items-center justify-center font-bold">
                                <i data-lucide="book-open" class="w-5 h-5"></i>
                            </div>
                            <div>
                                <h2 class="text-xl font-extrabold text-slate-900 dark:text-white">
                                    কোর্স কারিকুলাম ও মডিউল
                                </h2>
                                <p class="text-xs text-slate-500 dark:text-slate-400">প্রতিটি মডিউলে থাকছে প্র্যাকটিক্যাল কোডিং ও অ্যাসাইনমেন্ট</p>
                            </div>
                        </div>
                        <span class="text-xs font-bold text-blue-600 dark:text-cyan-400">
                            <?= count($modules) ?> টি মডিউল
                        </span>
                    </div>

                    <div class="space-y-4">
                        <?php foreach ($modules as $mIndex => $module): ?>
                            <div class="border border-slate-200 dark:border-slate-800 rounded-2xl overflow-hidden bg-white dark:bg-slate-900/60 transition">
                                <div class="p-4 bg-slate-50 dark:bg-slate-800/50 flex items-center justify-between cursor-pointer" onclick="this.nextElementSibling.classList.toggle('hidden')">
                                    <div class="flex items-center gap-3">
                                        <span class="w-7 h-7 rounded-lg bg-blue-600 text-white font-bold text-xs flex items-center justify-center">
                                            0<?= $mIndex + 1 ?>
                                        </span>
                                        <h3 class="font-bold text-slate-900 dark:text-white text-sm">
                                            <?= sanitize($module['title']) ?>
                                        </h3>
                                    </div>
                                    <div class="flex items-center gap-2 text-xs text-slate-500">
                                        <span><?= count($module['lessons']) ?> টি ক্লাস</span>
                                        <i data-lucide="chevron-down" class="w-4 h-4"></i>
                                    </div>
                                </div>

                                <div class="p-4 divide-y divide-slate-100 dark:divide-slate-800 space-y-2">
                                    <?php foreach ($module['lessons'] as $lIndex => $lesson): ?>
                                        <div class="pt-2 flex items-center justify-between text-xs text-slate-600 dark:text-slate-300">
                                            <div class="flex items-center gap-2">
                                                <i data-lucide="play-circle" class="w-4 h-4 text-blue-500"></i>
                                                <span class="font-medium"><?= sanitize($lesson['title']) ?></span>
                                            </div>
                                            <span class="text-slate-400"><?= sanitize($lesson['duration']) ?></span>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>

                <!-- Instructor Card -->
                <div class="p-8 rounded-3xl glass-panel border border-slate-200 dark:border-slate-800 shadow-md flex flex-col sm:flex-row items-center sm:items-start gap-6">
                    <div class="w-20 h-20 rounded-2xl bg-gradient-to-tr from-blue-600 to-cyan-500 text-white flex items-center justify-center font-black text-2xl shadow-xl shrink-0">
                        <?= strtoupper(substr($course['teacher_name'] ?? 'U', 0, 1)) ?>
                    </div>
                    <div class="space-y-2 text-center sm:text-left">
                        <span class="text-xs font-bold text-blue-600 dark:text-cyan-400 uppercase tracking-wider">
                            কোর্স ইন্সট্রাক্টর
                        </span>
                        <h3 class="text-xl font-bold text-slate-900 dark:text-white">
                            <?= sanitize($course['teacher_name'] ?? 'Senior Industry Expert') ?>
                        </h3>
                        <p class="text-xs text-slate-600 dark:text-slate-400 leading-relaxed">
                            <?= sanitize($course['teacher_bio'] ?? 'Universal Technology Corporation (UTC) জ্যেষ্ঠ প্রশিক্ষক ও সফটওয়্যার আর্কিটেক্ট।') ?>
                        </p>
                    </div>
                </div>

            </div>

            <!-- Right 4 Columns: Sticky Checkout & Pricing Card -->
            <div class="lg:col-span-4 sticky top-28 space-y-6">
                
                <div class="tilt-card card-3d p-6 sm:p-8 rounded-3xl glass-panel border border-blue-500/30 shadow-2xl space-y-6 bg-white dark:bg-slate-900/90 backdrop-blur-xl">
                    
                    <div class="space-y-2 pb-4 border-b border-slate-200 dark:border-slate-800">
                        <span class="text-xs font-bold text-slate-400 uppercase tracking-wider">স্পেশাল অফার ফি</span>
                        <div class="flex items-baseline gap-3">
                            <span class="text-3xl font-extrabold text-blue-600 dark:text-cyan-400">
                                <?= formatBDT($course['discount_price']) ?>
                            </span>
                            <span class="text-sm font-semibold text-slate-400 line-through">
                                <?= formatBDT($course['original_price']) ?>
                            </span>
                        </div>
                        <p class="text-xs text-emerald-600 dark:text-emerald-400 font-bold">
                            🎉 আপনি সাশ্রয় করছেন <?= formatBDT($course['original_price'] - $course['discount_price']) ?> টাকা!
                        </p>
                    </div>

                    <!-- Direct Enroll CTA Button -->
                    <a href="checkout.php?course_id=<?= $course['id'] ?>" class="w-full py-4 rounded-xl bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white font-extrabold text-center block text-sm shadow-xl shadow-blue-500/30 hover:shadow-blue-500/40 transition transform hover:-translate-y-0.5">
                        অনলাইনে ভর্তি নিশ্চিত করুন <i data-lucide="arrow-right" class="w-4 h-4 inline ml-1"></i>
                    </a>

                    <!-- Key Course Highlights Checklist -->
                    <div class="space-y-3 pt-2 text-xs font-semibold text-slate-700 dark:text-slate-300">
                        <div class="flex items-center gap-2">
                            <i data-lucide="check" class="w-4 h-4 text-emerald-500"></i>
                            <span><?= $course['total_classes'] ?> টি লাইভ ও প্র্যাকটিক্যাল ল্যাব ক্লাস</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <i data-lucide="check" class="w-4 h-4 text-emerald-500"></i>
                            <span>প্রতিটি ক্লাসের এইচডি ভিডিও রেকর্ড</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <i data-lucide="check" class="w-4 h-4 text-emerald-500"></i>
                            <span>২৪ ঘণ্টা অনলাইন ও অফলাইন ক্যাম্পাস সাপোর্ট</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <i data-lucide="check" class="w-4 h-4 text-emerald-500"></i>
                            <span>Lifetime Free Repeat Facility</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <i data-lucide="check" class="w-4 h-4 text-emerald-500"></i>
                            <span>কোর্স শেষে Professional Certificate</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <i data-lucide="check" class="w-4 h-4 text-emerald-500"></i>
                            <span>Industrial Training & মার্কেটপ্লেস গাইডলাইন</span>
                        </div>
                    </div>

                    <!-- Direct Helpline Box -->
                    <div class="p-4 rounded-2xl bg-slate-100 dark:bg-slate-800/80 border border-slate-200 dark:border-slate-700/60 text-xs space-y-2">
                        <div class="font-bold text-slate-900 dark:text-white flex items-center gap-1.5">
                            <i data-lucide="phone-call" class="w-4 h-4 text-emerald-500"></i>
                            <span>ভর্তি হেল্পলাইন (সরাসরি কথা বলুন):</span>
                        </div>
                        <div class="space-y-1 font-semibold text-blue-600 dark:text-cyan-400">
                            <a href="tel:01812669757" class="block hover:underline">📞 01812669757</a>
                            <a href="tel:01817122483" class="block hover:underline">📞 01817122483</a>
                        </div>
                        <p class="text-[11px] text-slate-500 pt-1">
                            📍 287 (1st Floor), Doctor Para, Feni-3900
                        </p>
                    </div>

                </div>

            </div>

        </div>

    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
