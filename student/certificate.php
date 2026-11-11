<?php
/**
 * Universal Technology Corporation (UTC) - Digital Certificate of Completion
 */

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/auth.php';
require_once __DIR__ . '/../config/helpers.php';

requireRole('student');
$user = getCurrentUser();

$courseId = isset($_GET['course_id']) ? (int)$_GET['course_id'] : 1;

$cStmt = $pdo->prepare("SELECT * FROM courses WHERE id = ?");
$cStmt->execute([$courseId]);
$course = $cStmt->fetch();

if (!$course) {
    header("Location: my-courses.php");
    exit;
}

$pageTitle = 'সার্টিফিকেট | ' . $course['title'] . ' | UTC';
require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/navbar.php';
?>

<div class="py-10 bg-slate-100/60 dark:bg-[#070b16] transition-colors duration-300">
    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
        
        <div class="flex items-center justify-between no-print">
            <a href="learn.php?course_id=<?= $course['id'] ?>" class="px-4 py-2 rounded-xl bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-xs font-bold text-slate-700 dark:text-slate-300 flex items-center gap-2">
                <i data-lucide="arrow-left" class="w-4 h-4"></i> ক্লাসরুমে ফিরুন
            </a>
            <button onclick="window.print()" class="px-5 py-2.5 rounded-xl bg-amber-500 hover:bg-amber-400 text-slate-950 font-black text-xs shadow-md transition flex items-center gap-2">
                <i data-lucide="download" class="w-4 h-4"></i> সনদপত্র ডাউনলোড / প্রিন্ট করুন
            </button>
        </div>

        <!-- Official Certificate Graphic Card -->
        <div class="p-8 sm:p-14 rounded-3xl bg-white dark:bg-slate-900 border-8 border-double border-amber-500/40 shadow-2xl relative overflow-hidden text-center space-y-8 print:border-8 print:p-8">
            
            <!-- Corner Decorative Watermarks -->
            <div class="absolute -top-16 -left-16 w-36 h-36 bg-amber-500/10 rounded-full blur-2xl pointer-events-none"></div>
            <div class="absolute -bottom-16 -right-16 w-36 h-36 bg-blue-500/10 rounded-full blur-2xl pointer-events-none"></div>

            <!-- Top Header -->
            <div class="space-y-2">
                <img src="<?= $appRoot ?>assets/images/utc-logo.png" alt="UTC Official Seal" class="w-20 h-20 rounded-full object-contain mx-auto shadow-2xl bg-white p-1 border-4 border-amber-500/60">
                <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 dark:text-white tracking-wider uppercase font-serif">
                    Universal Technology Corporation
                </h1>
                <p class="text-xs font-bold text-blue-600 dark:text-cyan-400 tracking-widest uppercase">
                    Center for Excellence in Information Technology • Feni, Bangladesh
                </p>
                <div class="w-24 h-1 bg-amber-500 mx-auto rounded-full mt-2"></div>
            </div>

            <!-- Certificate Title -->
            <div class="space-y-1">
                <p class="text-xs uppercase tracking-widest text-slate-400 font-bold">Certificate of Completion</p>
                <h2 class="text-xl sm:text-2xl font-serif text-slate-800 dark:text-slate-100 italic">
                    This is proudly presented to
                </h2>
            </div>

            <!-- Student Name -->
            <div class="py-2">
                <span class="text-3xl sm:text-4xl font-extrabold text-blue-700 dark:text-cyan-400 font-serif border-b-2 border-slate-300 dark:border-slate-700 pb-2 px-8 inline-block">
                    <?= sanitize($user['name']) ?>
                </span>
            </div>

            <!-- Details paragraph -->
            <p class="text-xs sm:text-sm text-slate-600 dark:text-slate-300 max-w-2xl mx-auto leading-relaxed">
                for successfully completing the professional training and hands-on industrial project on
                <strong class="text-slate-900 dark:text-white block text-base sm:text-lg mt-1 font-sans">
                    <?= sanitize($course['title']) ?>
                </strong>
                with outstanding performance and dedication at UTC Feni Campus.
            </p>

            <!-- Verification ID & Date -->
            <div class="pt-4 flex flex-wrap items-center justify-between text-xs text-slate-500 border-t border-slate-200 dark:border-slate-800 max-w-2xl mx-auto">
                <div>
                    <span class="block font-bold text-slate-400 uppercase text-[10px]">Certificate ID</span>
                    <span class="font-mono font-bold text-slate-900 dark:text-white">UTC-CERT-2026-<?= str_pad($course['id'] * 100 + $user['id'], 6, '0', STR_PAD_LEFT) ?></span>
                </div>
                <div>
                    <span class="block font-bold text-slate-400 uppercase text-[10px]">Issue Date</span>
                    <span class="font-bold text-slate-900 dark:text-white"><?= date('F d, Y') ?></span>
                </div>
                <div>
                    <span class="block font-bold text-slate-400 uppercase text-[10px]">Verification Status</span>
                    <span class="font-bold text-emerald-600">Verified & Authentic ✓</span>
                </div>
            </div>

            <!-- Signatures -->
            <div class="pt-8 grid grid-cols-2 gap-8 max-w-xl mx-auto text-xs">
                <div class="space-y-1">
                    <div class="h-12 border-b border-slate-400 flex items-end justify-center pb-1 font-serif italic text-sm text-slate-700 dark:text-slate-300">
                        Engr. Tanvir Ahmed
                    </div>
                    <span class="font-bold text-slate-900 dark:text-white block">Lead Instructor & Mentor</span>
                    <span class="text-[10px] text-slate-400">Universal Technology Corporation</span>
                </div>

                <div class="space-y-1">
                    <div class="h-12 border-b border-slate-400 flex items-end justify-center pb-1 font-serif italic text-sm text-slate-700 dark:text-slate-300">
                        Director of Academics
                    </div>
                    <span class="font-bold text-slate-900 dark:text-white block">Academic Director</span>
                    <span class="text-[10px] text-slate-400">UTC – Doctor Para, Feni</span>
                </div>
            </div>

        </div>

    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
