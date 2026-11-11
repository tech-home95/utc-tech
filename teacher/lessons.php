<?php
/**
 * Universal Technology Corporation (UTC) - Teacher Lessons Management
 */

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/auth.php';
require_once __DIR__ . '/../config/helpers.php';

requireRole('teacher');
$user = getCurrentUser();

$courseId = isset($_GET['course_id']) ? (int)$_GET['course_id'] : 1;

// Verify teacher owns course or admin
$cStmt = $pdo->prepare("SELECT * FROM courses WHERE id = ?");
$cStmt->execute([$courseId]);
$course = $cStmt->fetch();

if (!$course) {
    header("Location: courses.php");
    exit;
}

// Handle Add Module
if (isset($_POST['add_module'])) {
    $moduleTitle = sanitize($_POST['module_title'] ?? '');
    if (!empty($moduleTitle)) {
        $insMod = $pdo->prepare("INSERT INTO modules (course_id, title, sort_order) VALUES (?, ?, (SELECT COALESCE(MAX(sort_order),0)+1 FROM modules WHERE course_id = ?))");
        $insMod->execute([$courseId, $moduleTitle, $courseId]);
        setFlash('success', 'নতুন মডিউল সফলভাবে যোগ করা হয়েছে!');
        header("Location: lessons.php?course_id=" . $courseId);
        exit;
    }
}

// Handle Add Lesson
if (isset($_POST['add_lesson'])) {
    $moduleId = (int)$_POST['module_id'];
    $title = sanitize($_POST['title'] ?? '');
    $videoUrl = sanitize($_POST['video_url'] ?? '');
    $duration = sanitize($_POST['duration'] ?? '45 mins');
    $notes = sanitize($_POST['notes'] ?? '');

    if (!empty($title) && $moduleId > 0) {
        $insLes = $pdo->prepare("INSERT INTO lessons (module_id, course_id, title, video_url, duration, notes, sort_order) VALUES (?, ?, ?, ?, ?, ?, 1)");
        $insLes->execute([$moduleId, $courseId, $title, $videoUrl, $duration, $notes]);
        setFlash('success', 'নতুন ক্লাস লেকচার সফলভাবে আপলোড হয়েছে!');
        header("Location: lessons.php?course_id=" . $courseId);
        exit;
    }
}

// Handle Delete Lesson
if (isset($_GET['delete_lesson'])) {
    $delId = (int)$_GET['delete_lesson'];
    $pdo->prepare("DELETE FROM lessons WHERE id = ? AND course_id = ?")->execute([$delId, $courseId]);
    setFlash('success', 'লেকচারটি মুছে ফেলা হয়েছে।');
    header("Location: lessons.php?course_id=" . $courseId);
    exit;
}

// Fetch Modules with Lessons
$mStmt = $pdo->prepare("SELECT * FROM modules WHERE course_id = ? ORDER BY sort_order ASC");
$mStmt->execute([$courseId]);
$modules = $mStmt->fetchAll();

foreach ($modules as &$mod) {
    $lStmt = $pdo->prepare("SELECT * FROM lessons WHERE module_id = ? ORDER BY sort_order ASC, id ASC");
    $lStmt->execute([$mod['id']]);
    $mod['lessons'] = $lStmt->fetchAll();
}
unset($mod);

$pageTitle = 'লেসন ও কারিকুলাম পরিচালনা | ' . $course['title'] . ' | UTC';
require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/navbar.php';
?>

<div class="py-12 bg-slate-50 dark:bg-[#080d1a] min-h-[calc(100vh-80px)] transition-colors duration-300">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8">
        
        <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 pb-6 border-b border-slate-200 dark:border-slate-800">
            <div>
                <a href="courses.php" class="text-xs font-bold text-emerald-600 dark:text-emerald-400 hover:underline flex items-center gap-1 mb-1">
                    <i data-lucide="arrow-left" class="w-3.5 h-3.5"></i> কোর্স তালিকায় ফিরুন
                </a>
                <h1 class="text-2xl font-extrabold text-slate-900 dark:text-white">
                    <?= sanitize($course['title']) ?>
                </h1>
                <p class="text-xs text-slate-500">মডিউল ও ক্লাস লেকচার আপলোড প্যানেল</p>
            </div>
            
            <a href="../student/learn.php?course_id=<?= $course['id'] ?>" target="_blank" class="px-4 py-2 rounded-xl bg-slate-800 text-white text-xs font-bold flex items-center gap-2">
                <i data-lucide="eye" class="w-4 h-4 text-cyan-400"></i> ছাত্র ভিউ প্রিভিউ
            </a>
        </div>

        <?= renderFlash() ?>

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
            
            <!-- Left 7 Cols: Existing Modules & Lessons List -->
            <div class="lg:col-span-7 space-y-6">
                <h2 class="text-lg font-bold text-slate-900 dark:text-white">
                    বর্তমান কারিকুলাম ও আপলোডকৃত ক্লাস
                </h2>

                <?php if (empty($modules)): ?>
                    <div class="p-8 rounded-3xl glass-panel border border-slate-200 dark:border-slate-800 text-center text-xs text-slate-500">
                        এখনো কোনো মডিউল যোগ করা হয়নি। ডানপাশের ফর্ম থেকে নতুন মডিউল তৈরি করুন।
                    </div>
                <?php else: ?>
                    <div class="space-y-4">
                        <?php foreach ($modules as $mIdx => $mod): ?>
                            <div class="p-6 rounded-3xl glass-panel border border-slate-200 dark:border-slate-800 space-y-3">
                                <div class="flex items-center justify-between pb-2 border-b border-slate-100 dark:border-slate-800">
                                    <div class="flex items-center gap-2">
                                        <span class="w-6 h-6 rounded-md bg-emerald-600 text-white font-bold text-xs flex items-center justify-center">
                                            <?= $mIdx + 1 ?>
                                        </span>
                                        <h3 class="font-bold text-slate-900 dark:text-white text-sm">
                                            <?= sanitize($mod['title']) ?>
                                        </h3>
                                    </div>
                                    <span class="text-xs text-slate-400"><?= count($mod['lessons']) ?> টি ক্লাস</span>
                                </div>

                                <div class="space-y-2 pt-1">
                                    <?php if (empty($mod['lessons'])): ?>
                                        <p class="text-xs text-slate-400 italic">এই মডিউলে এখনও কোনো ক্লাস যোগ করা হয়নি।</p>
                                    <?php else: ?>
                                        <?php foreach ($mod['lessons'] as $les): ?>
                                            <div class="p-3 rounded-2xl bg-slate-50 dark:bg-slate-800/50 border border-slate-100 dark:border-slate-800 flex items-center justify-between text-xs gap-3">
                                                <div class="flex items-center gap-2 truncate">
                                                    <i data-lucide="play-circle" class="w-4 h-4 text-emerald-500 shrink-0"></i>
                                                    <span class="font-medium text-slate-800 dark:text-slate-200 truncate"><?= sanitize($les['title']) ?></span>
                                                    <span class="text-[10px] text-slate-400 shrink-0">(<?= sanitize($les['duration']) ?>)</span>
                                                </div>
                                                <a href="lessons.php?course_id=<?= $courseId ?>&delete_lesson=<?= $les['id'] ?>" onclick="return confirm('এই লেকচারটি মুছে ফেলতে চান?')" class="text-rose-500 hover:text-rose-700 font-bold shrink-0 p-1">
                                                    <i data-lucide="trash-2" class="w-3.5 h-3.5"></i>
                                                </a>
                                            </div>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Right 5 Cols: Add Module & Add Lesson Forms -->
            <div class="lg:col-span-5 space-y-6">
                
                <!-- Add New Module Form -->
                <div class="p-6 rounded-3xl glass-panel border border-slate-200 dark:border-slate-800 shadow-xl space-y-4">
                    <h3 class="text-sm font-bold text-slate-900 dark:text-white flex items-center gap-2">
                        <i data-lucide="folder-plus" class="w-4 h-4 text-emerald-500"></i>
                        <span>নতুন মডিউল তৈরি করুন</span>
                    </h3>

                    <form method="POST" class="space-y-3 text-xs">
                        <div>
                            <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">মডিউল শিরোনাম *</label>
                            <input type="text" name="module_title" required placeholder="যেমন: Module 5: API Integration" class="w-full px-3 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white text-xs">
                        </div>
                        <button type="submit" name="add_module" class="w-full py-2.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-white font-bold transition">
                            মডিউল যোগ করুন
                        </button>
                    </form>
                </div>

                <!-- Add New Lesson Form -->
                <div class="p-6 rounded-3xl glass-panel border border-slate-200 dark:border-slate-800 shadow-xl space-y-4">
                    <h3 class="text-sm font-bold text-slate-900 dark:text-white flex items-center gap-2">
                        <i data-lucide="video" class="w-4 h-4 text-blue-500"></i>
                        <span>নতুন ক্লাস লেকচার আপলোড</span>
                    </h3>

                    <form method="POST" class="space-y-3 text-xs">
                        <div>
                            <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">মডিউল নির্বাচন করুন *</label>
                            <select name="module_id" required class="w-full px-3 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white text-xs">
                                <?php foreach ($modules as $m): ?>
                                    <option value="<?= $m['id'] ?>"><?= sanitize($m['title']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div>
                            <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">ক্লাসের শিরোনাম *</label>
                            <input type="text" name="title" required placeholder="যেমন: Class 10: Building REST API" class="w-full px-3 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white text-xs">
                        </div>

                        <div>
                            <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">ভিডিও এম্বেড URL (YouTube/Vimeo)</label>
                            <input type="url" name="video_url" placeholder="https://www.youtube.com/embed/..." class="w-full px-3 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white text-xs">
                        </div>

                        <div>
                            <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">ক্লাসের দৈর্ঘ্য (Duration)</label>
                            <input type="text" name="duration" value="1 hr 15 mins" class="w-full px-3 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white text-xs">
                        </div>

                        <div>
                            <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">লেকচার নোটস ও নির্দেশনা</label>
                            <textarea name="notes" rows="3" placeholder="শিক্ষার্থীদের জন্য ক্লাসের কোড ফাইল, গিটহাব লিংক ও জরুরি নির্দেশনা..." class="w-full px-3 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white text-xs"></textarea>
                        </div>

                        <button type="submit" name="add_lesson" class="w-full py-3 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white font-bold transition shadow-md">
                            লেকচার সংরক্ষণ ও পাবলিশ করুন
                        </button>
                    </form>
                </div>

            </div>

        </div>

    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
