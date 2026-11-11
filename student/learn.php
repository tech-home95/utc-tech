<?php
/**
 * Universal Technology Corporation (UTC) - Interactive Student Classroom
 */

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/auth.php';
require_once __DIR__ . '/../config/helpers.php';

requireRole('student');
$user = getCurrentUser();

$courseId = isset($_GET['course_id']) ? (int)$_GET['course_id'] : 1;

// Verify approved enrollment
$enCheck = $pdo->prepare("SELECT * FROM enrollments WHERE student_id = ? AND course_id = ? AND payment_status = 'approved'");
$enCheck->execute([$user['id'], $courseId]);
$enrollment = $enCheck->fetch();

if (!$enrollment && !hasRole('admin')) {
    setFlash('error', 'এই কোর্সে আপনার কোনো সক্রিয় বা অনুমোদিত ভর্তি নেই। অনুগ্রহ করে পেমেন্ট সম্পূর্ণ করুন।');
    header("Location: my-courses.php");
    exit;
}

// Fetch course details
$stmt = $pdo->prepare("SELECT * FROM courses WHERE id = ?");
$stmt->execute([$courseId]);
$course = $stmt->fetch();

// Fetch modules & lessons
$mStmt = $pdo->prepare("SELECT * FROM modules WHERE course_id = ? ORDER BY sort_order ASC");
$mStmt->execute([$courseId]);
$modules = $mStmt->fetchAll();

$allLessons = [];
foreach ($modules as &$mod) {
    $lStmt = $pdo->prepare("SELECT * FROM lessons WHERE module_id = ? ORDER BY sort_order ASC");
    $lStmt->execute([$mod['id']]);
    $mod['lessons'] = $lStmt->fetchAll();
    foreach ($mod['lessons'] as $les) {
        $allLessons[] = $les;
    }
}
unset($mod);

// Determine active lesson
$activeLessonId = isset($_GET['lesson_id']) ? (int)$_GET['lesson_id'] : ($allLessons[0]['id'] ?? 0);
$activeLesson = null;
foreach ($allLessons as $les) {
    if ($les['id'] == $activeLessonId) {
        $activeLesson = $les;
        break;
    }
}
if (!$activeLesson && !empty($allLessons)) {
    $activeLesson = $allLessons[0];
}

// Handle Mark Completed Action
if (isset($_POST['mark_completed']) && $activeLesson) {
    $insProg = $pdo->prepare("INSERT OR REPLACE INTO course_progress (student_id, lesson_id, course_id, completed, updated_at) VALUES (?, ?, ?, 1, CURRENT_TIMESTAMP)");
    $insProg->execute([$user['id'], $activeLesson['id'], $courseId]);
    setFlash('success', 'অভিনন্দন! ক্লাসটি সম্পন্ন হিসেবে চিহ্নিত করা হয়েছে।');
    header("Location: learn.php?course_id={$courseId}&lesson_id={$activeLesson['id']}");
    exit;
}

// Handle Assignment Submission from Classroom
if (isset($_POST['submit_assignment'])) {
    $assignmentId = (int)$_POST['assignment_id'];
    $submissionText = sanitize($_POST['submission_text'] ?? '');
    
    $insSub = $pdo->prepare("INSERT INTO submissions (assignment_id, student_id, submission_text, submitted_at) VALUES (?, ?, ?, CURRENT_TIMESTAMP)");
    $insSub->execute([$assignmentId, $user['id'], $submissionText]);
    setFlash('success', 'অ্যাসাইনমেন্ট সফলভাবে জমা হয়েছে! শিক্ষক মূল্যায়নের পর মার্কস দেখতে পাবেন।');
    header("Location: learn.php?course_id={$courseId}&lesson_id={$activeLessonId}");
    exit;
}

// Fetch Completed Lessons for this student
$progStmt = $pdo->prepare("SELECT lesson_id FROM course_progress WHERE student_id = ? AND course_id = ? AND completed = 1");
$progStmt->execute([$user['id'], $courseId]);
$completedLessonIds = $progStmt->fetchAll(PDO::FETCH_COLUMN);

// Fetch Assignments for this course
$asgStmt = $pdo->prepare("SELECT a.*, s.marks_obtained, s.feedback, s.submitted_at 
                          FROM assignments a 
                          LEFT JOIN submissions s ON a.id = s.assignment_id AND s.student_id = ?
                          WHERE a.course_id = ?");
$asgStmt->execute([$user['id'], $courseId]);
$assignments = $asgStmt->fetchAll();

$pageTitle = $course['title'] . ' - Interactive Classroom | UTC';
require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/navbar.php';
?>

<div class="py-8 bg-slate-100/60 dark:bg-[#070b16] border-b border-slate-200 dark:border-slate-800">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-2">
                <a href="my-courses.php" class="text-xs font-bold text-blue-600 dark:text-cyan-400 hover:underline flex items-center gap-1">
                    <i data-lucide="arrow-left" class="w-3.5 h-3.5"></i> কোর্স ড্যাশবোর্ড
                </a>
                <span class="text-slate-400">•</span>
                <span class="text-xs text-slate-500 font-bold"><?= sanitize($course['category']) ?></span>
            </div>
            <h1 class="text-xl sm:text-2xl font-extrabold text-slate-900 dark:text-white mt-1">
                <?= sanitize($course['title']) ?>
            </h1>
        </div>
        <div class="flex items-center gap-3">
            <a href="certificate.php?course_id=<?= $course['id'] ?>" class="px-4 py-2 rounded-xl bg-amber-500 hover:bg-amber-400 text-slate-950 font-bold text-xs shadow-md transition flex items-center gap-1.5">
                <i data-lucide="award" class="w-4 h-4"></i> সনদপত্র দেখুন
            </a>
        </div>
    </div>
</div>

<div class="py-8 bg-slate-50 dark:bg-[#080d1a] min-h-[calc(100vh-140px)] transition-colors duration-300">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <?= renderFlash() ?>

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
            
            <!-- Left 8 Cols: Video Player & Tabs -->
            <div class="lg:col-span-8 space-y-6">
                
                <!-- Video Screen Container -->
                <div class="rounded-3xl overflow-hidden glass-panel border border-slate-200 dark:border-slate-800 shadow-2xl bg-black">
                    <div class="relative aspect-video w-full bg-slate-950 flex items-center justify-center">
                        <?php if ($activeLesson): ?>
                            <!-- Responsive Video Player Embed -->
                            <iframe class="w-full h-full" 
                                    src="<?= $activeLesson['video_url'] ?: 'https://www.youtube.com/embed/dQw4w9WgXcQ' ?>" 
                                    title="<?= sanitize($activeLesson['title']) ?>" 
                                    frameborder="0" 
                                    allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" 
                                    allowfullscreen>
                            </iframe>
                        <?php else: ?>
                            <div class="text-slate-400 text-sm">কোনো সক্রিয় ক্লাস পাওয়া যায়নি।</div>
                        <?php endif; ?>
                    </div>

                    <!-- Lesson Meta Bar below player -->
                    <div class="p-6 bg-white dark:bg-slate-900/90 border-t border-slate-200 dark:border-slate-800 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
                        <div class="space-y-1">
                            <h2 class="text-lg font-bold text-slate-900 dark:text-white">
                                <?= sanitize($activeLesson['title'] ?? 'ক্লাসের বিবরণ') ?>
                            </h2>
                            <p class="text-xs text-slate-500 flex items-center gap-2">
                                <span class="flex items-center gap-1"><i data-lucide="clock" class="w-3.5 h-3.5 text-blue-500"></i> <?= sanitize($activeLesson['duration'] ?? '45 mins') ?></span>
                                <span>•</span>
                                <span>Universal Technology Corporation (UTC) - Feni</span>
                            </p>
                        </div>

                        <?php if ($activeLesson): ?>
                            <form method="POST">
                                <button type="submit" name="mark_completed" class="px-5 py-2.5 rounded-xl <?= in_array($activeLesson['id'], $completedLessonIds) ? 'bg-emerald-600 text-white' : 'bg-blue-600 hover:bg-blue-500 text-white' ?> text-xs font-bold shadow-md transition flex items-center gap-2">
                                    <i data-lucide="check-circle" class="w-4 h-4"></i>
                                    <span><?= in_array($activeLesson['id'], $completedLessonIds) ? 'সম্পন্ন হয়েছে ✓' : 'ক্লাসটি সম্পন্ন হিসেবে চিহ্নিত করুন' ?></span>
                                </button>
                            </form>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Tabs: Notes, Resources, Assignments, Discussion -->
                <div class="p-8 rounded-3xl glass-panel border border-slate-200 dark:border-slate-800 shadow-xl space-y-6">
                    <div class="flex items-center gap-4 border-b border-slate-200 dark:border-slate-800 text-xs font-bold pb-2 overflow-x-auto">
                        <button type="button" onclick="switchClassTab('notes')" id="tab-btn-notes" class="px-4 py-2 rounded-xl bg-blue-600 text-white transition">
                            লেকচার নোটস ও বিবরণ
                        </button>
                        <button type="button" onclick="switchClassTab('assignments')" id="tab-btn-assignments" class="px-4 py-2 rounded-xl text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800 transition">
                            অ্যাসাইনমেন্ট ও টাস্ক (<?= count($assignments) ?>)
                        </button>
                        <button type="button" onclick="switchClassTab('qa')" id="tab-btn-qa" class="px-4 py-2 rounded-xl text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800 transition">
                            প্রশ্ন ও উত্তর (Q&A)
                        </button>
                    </div>

                    <!-- Tab 1: Lecture Notes -->
                    <div id="tab-content-notes" class="space-y-4 text-xs leading-relaxed text-slate-700 dark:text-slate-300">
                        <h3 class="text-sm font-bold text-slate-900 dark:text-white">ক্লাস ওভারভিউ ও শিক্ষক নির্দেশনা:</h3>
                        <div class="p-4 rounded-2xl bg-slate-50 dark:bg-slate-800/50 border border-slate-100 dark:border-slate-800 whitespace-pre-line font-bengali">
<?= sanitize($activeLesson['notes'] ?? 'ক্লাসের যাবতীয় কোড ফাইল, গিটহাব রিপোজিটরি ও রেফারেন্স লিঙ্ক নিচে সংযুক্ত করা হয়েছে। যেকোনো সমস্যায় ফোরামে পোস্ট করুন অথবা সরাসরি ডক্টর পাড়া, ফেনী ক্যাম্পাসে ট্রেনার সাপোর্ট নিন।') ?>
                        </div>

                        <!-- Resource Downloads -->
                        <div class="pt-4 border-t border-slate-100 dark:border-slate-800 space-y-2">
                            <h4 class="font-bold text-slate-900 dark:text-white">ডাউনলোডযোগ্য রিসোর্স ও লেকচার শিট:</h4>
                            <div class="flex flex-wrap gap-3">
                                <a href="#" class="px-3.5 py-2 rounded-xl bg-blue-50 dark:bg-blue-900/30 text-blue-600 dark:text-cyan-400 border border-blue-200 dark:border-blue-800 font-bold flex items-center gap-2 hover:bg-blue-100 transition">
                                    <i data-lucide="file-code" class="w-4 h-4"></i> Lecture-Source-Code.zip
                                </a>
                                <a href="#" class="px-3.5 py-2 rounded-xl bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 border border-slate-200 dark:border-slate-700 font-bold flex items-center gap-2 hover:bg-slate-200 transition">
                                    <i data-lucide="file-text" class="w-4 h-4 text-amber-500"></i> Course-Notes-Slide.pdf
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- Tab 2: Assignments -->
                    <div id="tab-content-assignments" class="hidden space-y-6">
                        <?php if (empty($assignments)): ?>
                            <p class="text-xs text-slate-500">বর্তমানে এই কোর্সে কোনো নির্ধারিত অ্যাসাইনমেন্ট নেই।</p>
                        <?php else: ?>
                            <?php foreach ($assignments as $asg): ?>
                                <div class="p-5 rounded-2xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900/70 space-y-3">
                                    <div class="flex items-start justify-between gap-2">
                                        <div>
                                            <h4 class="text-sm font-bold text-slate-900 dark:text-white"><?= sanitize($asg['title']) ?></h4>
                                            <p class="text-xs text-slate-500 mt-1"><?= sanitize($asg['description']) ?></p>
                                        </div>
                                        <span class="px-2.5 py-1 rounded-md text-[10px] font-bold bg-blue-100 dark:bg-blue-900/60 text-blue-600 dark:text-cyan-400 shrink-0">
                                            পূর্ণমান: <?= $asg['max_marks'] ?>
                                        </span>
                                    </div>

                                    <?php if ($asg['submitted_at']): ?>
                                        <div class="p-3 rounded-xl bg-emerald-50 dark:bg-emerald-950/40 border border-emerald-500/30 text-xs text-emerald-700 dark:text-emerald-300">
                                            <p class="font-bold">✓ আপনি অ্যাসাইনমেন্টটি জমা দিয়েছেন!</p>
                                            <p class="text-[11px] mt-0.5">জমা দেওয়ার তারিখ: <?= date('d M, Y', strtotime($asg['submitted_at'])) ?></p>
                                            <?php if ($asg['marks_obtained'] !== null): ?>
                                                <p class="font-bold mt-1 text-sm">প্রাপ্ত নম্বর: <?= $asg['marks_obtained'] ?> / <?= $asg['max_marks'] ?></p>
                                                <p class="text-slate-600 dark:text-slate-400 mt-0.5">শিক্ষকের মন্তব্য: <?= sanitize($asg['feedback'] ?? 'চমৎকার প্রজেক্ট!') ?></p>
                                            <?php else: ?>
                                                <p class="text-amber-600 dark:text-amber-400 mt-1">শিক্ষক কর্তৃক মূল্যায়ন প্রক্রিয়াধীন...</p>
                                            <?php endif; ?>
                                        </div>
                                    <?php else: ?>
                                        <form method="POST" class="space-y-3 pt-2 border-t border-slate-100 dark:border-slate-800">
                                            <input type="hidden" name="assignment_id" value="<?= $asg['id'] ?>">
                                            <label class="block text-xs font-bold text-slate-700 dark:text-slate-300">
                                                আপনার সমাধান লিংক (GitHub / Google Drive / Live URL):
                                            </label>
                                            <textarea name="submission_text" required rows="2" placeholder="যেমন: https://github.com/username/project-repo এবং স্ক্রিনশট লিংক..." class="w-full px-3 py-2 text-xs rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500"></textarea>
                                            <button type="submit" name="submit_assignment" class="px-4 py-2 bg-blue-600 hover:bg-blue-500 text-white text-xs font-bold rounded-xl transition">
                                                সাবমিট করুন
                                            </button>
                                        </form>
                                    <?php endif; ?>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>

                    <!-- Tab 3: Q&A -->
                    <div id="tab-content-qa" class="hidden space-y-4 text-xs">
                        <div class="p-4 rounded-2xl bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800/60 flex items-center justify-between">
                            <span class="text-slate-700 dark:text-slate-300">ক্লাস সংক্রান্ত প্রশ্ন ট্রেনারকে সরাসরি জিজ্ঞাসা করুন</span>
                            <span class="px-2.5 py-1 bg-emerald-500 text-white rounded-full text-[10px] font-bold">Live Support Active</span>
                        </div>
                        <div class="space-y-2">
                            <textarea rows="3" placeholder="আপনার প্রশ্নটি বিস্তারিত লিখুন..." class="w-full px-3 py-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white"></textarea>
                            <button type="button" onclick="showToast('আপনার প্রশ্নটি জমা হয়েছে! ট্রেনার খুব শীঘ্রই উত্তর দেবেন।', 'success')" class="px-4 py-2 bg-blue-600 text-white font-bold rounded-xl">প্রশ্ন পোস্ট করুন</button>
                        </div>
                    </div>
                </div>

            </div>

            <!-- Right 4 Cols: Curriculum Navigation Tree -->
            <div class="lg:col-span-4 space-y-6">
                
                <div class="p-6 rounded-3xl glass-panel border border-slate-200 dark:border-slate-800 shadow-xl space-y-4">
                    <div class="flex items-center justify-between pb-3 border-b border-slate-200 dark:border-slate-800">
                        <h3 class="text-sm font-bold text-slate-900 dark:text-white flex items-center gap-2">
                            <i data-lucide="list-video" class="w-4 h-4 text-blue-500"></i>
                            <span>কোর্স সূচিপত্র ও লেকচার</span>
                        </h3>
                        <span class="text-xs font-bold text-emerald-600 dark:text-emerald-400">
                            <?= count($completedLessonIds) ?> / <?= count($allLessons) ?> সম্পন্ন
                        </span>
                    </div>

                    <!-- Modules & Lessons List -->
                    <div class="space-y-4 max-h-[600px] overflow-y-auto pr-1">
                        <?php foreach ($modules as $mIdx => $mod): ?>
                            <div class="space-y-1.5">
                                <div class="text-[11px] font-bold uppercase tracking-wider text-slate-400 px-2">
                                    <?= sanitize($mod['title']) ?>
                                </div>
                                <div class="space-y-1">
                                    <?php foreach ($mod['lessons'] as $les): ?>
                                        <?php 
                                            $isActive = ($activeLesson && $activeLesson['id'] == $les['id']);
                                            $isDone = in_array($les['id'], $completedLessonIds);
                                        ?>
                                        <a href="learn.php?course_id=<?= $courseId ?>&lesson_id=<?= $les['id'] ?>" class="p-3 rounded-2xl flex items-center justify-between text-xs transition <?= $isActive ? 'bg-blue-600 text-white shadow-md' : 'hover:bg-slate-100 dark:hover:bg-slate-800 text-slate-700 dark:text-slate-300' ?>">
                                            <div class="flex items-center gap-2.5 truncate">
                                                <i data-lucide="<?= $isDone ? 'check-circle-2' : 'play-circle' ?>" class="w-4 h-4 <?= $isActive ? 'text-white' : ($isDone ? 'text-emerald-500' : 'text-slate-400') ?> shrink-0"></i>
                                                <span class="font-medium truncate"><?= sanitize($les['title']) ?></span>
                                            </div>
                                            <span class="text-[10px] <?= $isActive ? 'text-blue-100' : 'text-slate-400' ?> shrink-0 ml-2">
                                                <?= sanitize($les['duration']) ?>
                                            </span>
                                        </a>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>

                </div>

            </div>

        </div>

    </div>
</div>

<script>
function switchClassTab(tab) {
    const tabs = ['notes', 'assignments', 'qa'];
    tabs.forEach(t => {
        const btn = document.getElementById('tab-btn-' + t);
        const content = document.getElementById('tab-content-' + t);
        if (t === tab) {
            btn.className = 'px-4 py-2 rounded-xl bg-blue-600 text-white transition';
            content.classList.remove('hidden');
        } else {
            btn.className = 'px-4 py-2 rounded-xl text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800 transition';
            content.classList.add('hidden');
        }
    });
}
</script>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
