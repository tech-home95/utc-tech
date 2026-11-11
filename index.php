<?php
/**
 * Universal Technology Corporation (UTC) - Homepage
 * E-Learning Platform for IT Training (UY Lab / Lead Academy style)
 */

$pageTitle = 'Universal Technology Corporation (UTC) | Feni Top IT Institute & E-Learning';
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/config/auth.php';
require_once __DIR__ . '/config/helpers.php';

// Fetch courses
$stmt = $pdo->query("SELECT * FROM courses WHERE status = 'active' ORDER BY id ASC");
$courses = $stmt->fetchAll();

// Fetch notices
$noticeStmt = $pdo->query("SELECT * FROM notices WHERE target_role IN ('all', 'student') ORDER BY id DESC LIMIT 2");
$notices = $noticeStmt->fetchAll();

require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/navbar.php';
?>

<!-- Render Flash Alert if any -->
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-4">
    <?= renderFlash() ?>
</div>

<!-- ================= HERO SECTION WITH 3D CANVAS ================= -->
<section class="relative min-h-[640px] lg:min-h-[720px] flex items-center overflow-hidden bg-gradient-to-b from-blue-50/70 via-white to-slate-100 dark:from-[#091124] dark:via-[#080d1a] dark:to-[#050811]">
    
    <!-- Three.js 3D Interactive Canvas -->
    <div id="three-hero-canvas" class="absolute inset-0 opacity-40 dark:opacity-75 z-0 pointer-events-auto"></div>

    <!-- Gradient Glow overlays (non-blocking) -->
    <div class="absolute -top-32 -left-32 w-96 h-96 bg-blue-500/20 rounded-full blur-3xl pointer-events-none"></div>
    <div class="absolute top-1/2 -right-32 w-96 h-96 bg-cyan-500/15 rounded-full blur-3xl pointer-events-none"></div>
    <div class="absolute -bottom-32 left-1/3 w-96 h-96 bg-amber-500/10 rounded-full blur-3xl pointer-events-none"></div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-20 relative z-10 w-full">
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-12 items-center">
            
            <!-- Left Hero Content -->
            <div class="lg:col-span-7 space-y-6 text-center lg:text-left">
                
                <div class="inline-flex items-center gap-2 px-4 py-2 rounded-full glass-panel border border-blue-200 dark:border-blue-900/60 text-xs sm:text-sm font-bold text-blue-700 dark:text-cyan-400 shadow-sm animate-fade-in">
                    <span class="flex h-2 w-2 rounded-full bg-emerald-500 animate-ping"></span>
                    <span>📍 ডক্টর পাড়া, ফেনীর আধুনিকতম আইটি ইনস্টিটিউট</span>
                </div>

                <h1 class="text-3xl sm:text-4xl md:text-5xl lg:text-6xl font-extrabold tracking-tight text-slate-900 dark:text-white leading-[1.2]">
                    Universal Technology <br class="hidden sm:inline">
                    <span class="text-gradient-primary">Corporation (UTC)</span>
                </h1>

                <p class="text-base sm:text-lg lg:text-xl text-slate-600 dark:text-slate-300 font-medium max-w-2xl leading-relaxed">
                    ✨ <strong class="text-blue-600 dark:text-cyan-400">Learn Today | Skill Up Tomorrow | Build Your Career with UTC</strong> ✨<br>
                    আন্তর্জাতিক মানের প্রফেশনাল ট্রেনিং, হ্যান্ডস-অন ল্যাব প্রজেক্ট ও লাইফটাইম মেন্টরশিপ সাপোর্ট দিয়ে গড়ুন আপনার আইটি ক্যারিয়ার।
                </p>

                <!-- Urgent Promo Callout Box (Glassmorphic) -->
                <div class="p-4 sm:p-5 rounded-2xl glass-panel border border-amber-300/60 dark:border-amber-500/30 bg-amber-500/5 shadow-xl relative overflow-hidden text-left">
                    <div class="flex items-start gap-4">
                        <div class="w-12 h-12 rounded-xl bg-gradient-to-tr from-amber-500 to-red-500 text-white flex items-center justify-center font-bold text-2xl shrink-0 shadow-md">
                            🔥
                        </div>
                        <div>
                            <div class="flex flex-wrap items-center gap-2">
                                <h3 class="font-extrabold text-slate-900 dark:text-white text-base sm:text-lg">
                                    GOOD NEWS! Programming শেখার সেরা সুযোগ
                                </h3>
                                <span class="badge-discount px-2.5 py-0.5 rounded-full text-white text-xs font-black">
                                    ৫০% ডিসকাউন্ট
                                </span>
                            </div>
                            <p class="text-xs sm:text-sm text-slate-600 dark:text-slate-300 mt-1">
                                Programming Combo Pack ২০,০০০/- টাকার কোর্স এখন মাত্র <strong>১০,০০০/- টাকায়!</strong> অফারটি সীমিত সময়ের জন্য।
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Call to Action Buttons -->
                <div class="flex flex-wrap items-center justify-center lg:justify-start gap-4 pt-2">
                    <a href="#courses-section" class="px-7 py-3.5 rounded-xl bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white font-bold text-base shadow-xl shadow-blue-500/30 hover:shadow-blue-500/40 transition transform hover:-translate-y-0.5 flex items-center gap-2">
                        <i data-lucide="sparkles" class="w-5 h-5"></i> কোর্সসমূহ দেখুন
                    </a>
                    <a href="tel:01812669757" class="px-6 py-3.5 rounded-xl glass-panel border border-slate-300 dark:border-slate-700 hover:bg-slate-100 dark:hover:bg-slate-800 text-slate-800 dark:text-white font-bold text-base transition flex items-center gap-2">
                        <i data-lucide="phone-call" class="w-5 h-5 text-emerald-500"></i> কল করুন: 01812669757
                    </a>
                </div>

                <!-- Stats Counters -->
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 pt-6 border-t border-slate-200/80 dark:border-slate-800">
                    <div>
                        <div class="text-2xl sm:text-3xl font-extrabold text-blue-600 dark:text-cyan-400">১০+</div>
                        <div class="text-xs font-semibold text-slate-500 dark:text-slate-400">প্রফেশনাল কোর্স</div>
                    </div>
                    <div>
                        <div class="text-2xl sm:text-3xl font-extrabold text-amber-500">৫০%</div>
                        <div class="text-xs font-semibold text-slate-500 dark:text-slate-400">স্পেশাল ছাড়</div>
                    </div>
                    <div>
                        <div class="text-2xl sm:text-3xl font-extrabold text-emerald-500">২৪/৭</div>
                        <div class="text-xs font-semibold text-slate-500 dark:text-slate-400">সাপোর্ট সুবিধা</div>
                    </div>
                    <div>
                        <div class="text-2xl sm:text-3xl font-extrabold text-purple-500">১০০%</div>
                        <div class="text-xs font-semibold text-slate-500 dark:text-slate-400">প্র্যাকটিক্যাল ল্যাব</div>
                    </div>
                </div>

            </div>

            <!-- Right Hero Interactive 3D Showcase Card -->
            <div class="lg:col-span-5 relative perspective-container">
                <div class="tilt-card card-3d p-6 sm:p-8 rounded-3xl glass-panel border border-white/60 dark:border-slate-800 shadow-2xl relative overflow-hidden backdrop-blur-xl">
                    
                    <div class="absolute -top-12 -right-12 w-44 h-44 bg-gradient-to-br from-blue-600/30 to-indigo-600/30 rounded-full blur-2xl"></div>
                    
                    <!-- Floating Live Status -->
                    <div class="flex items-center justify-between pb-6 border-b border-slate-200/60 dark:border-slate-800">
                        <div class="flex items-center gap-3">
                            <div class="w-3 h-3 rounded-full bg-emerald-500 animate-ping"></div>
                            <span class="text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400">
                                Live Admissions Open
                            </span>
                        </div>
                        <span class="px-3 py-1 rounded-full text-xs font-bold bg-blue-100 dark:bg-blue-900/60 text-blue-700 dark:text-blue-300">
                            Batch 2026-A
                        </span>
                    </div>

                    <!-- Highlighted Course Card -->
                    <div class="py-6 space-y-4">
                        <span class="px-2.5 py-1 rounded-md text-xs font-bold bg-amber-500/20 text-amber-600 dark:text-amber-400">
                            ফিচার্ড কম্বো কোর্স
                        </span>
                        <h3 class="text-xl font-extrabold text-slate-900 dark:text-white leading-snug">
                            Programming Combo Pack
                        </h3>
                        <p class="text-xs text-slate-500 dark:text-slate-400">
                            Algorithm + C + Java + Python + JavaScript
                        </p>

                        <div class="space-y-2 pt-2">
                            <div class="flex items-center gap-2 text-xs font-semibold text-slate-700 dark:text-slate-300">
                                <i data-lucide="check-circle-2" class="w-4 h-4 text-emerald-500"></i>
                                <span>৪টি হাই-ডিমান্ড প্রোগ্রামিং ল্যাঙ্গুয়েজ</span>
                            </div>
                            <div class="flex items-center gap-2 text-xs font-semibold text-slate-700 dark:text-slate-300">
                                <i data-lucide="check-circle-2" class="w-4 h-4 text-emerald-500"></i>
                                <span>কোর্স ফি: ২০,০০০/- এর বদলে মাত্র ১০,০০০/-</span>
                            </div>
                            <div class="flex items-center gap-2 text-xs font-semibold text-slate-700 dark:text-slate-300">
                                <i data-lucide="check-circle-2" class="w-4 h-4 text-emerald-500"></i>
                                <span>লাইফটাইম রিপিট ও সার্টিফিকেট সুবিধা</span>
                            </div>
                        </div>

                        <!-- Price Tag -->
                        <div class="pt-4 flex items-baseline justify-between">
                            <div>
                                <span class="text-xs text-slate-400 line-through">৳২০,০০০</span>
                                <div class="text-2xl font-black text-blue-600 dark:text-cyan-400">৳১০,০০০</div>
                            </div>
                            <a href="checkout.php?course_id=1" class="px-5 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-500 text-white font-bold text-sm shadow-md transition">
                                এখনই ভর্তি হন
                            </a>
                        </div>
                    </div>

                    <!-- Campus Pill -->
                    <div class="pt-4 border-t border-slate-200/60 dark:border-slate-800 flex items-center justify-between text-xs text-slate-500 dark:text-slate-400">
                        <span class="flex items-center gap-1.5">
                            <i data-lucide="map-pin" class="w-4 h-4 text-amber-500"></i> ২৮৭ ডক্টর পাড়া, ১ম তলা, ফেনী
                        </span>
                        <span class="text-blue-600 dark:text-cyan-400 font-semibold">UTC Feni Hub</span>
                    </div>

                </div>
            </div>

        </div>
    </div>
</section>

<!-- ================= SPECIAL DISCOUNT PROMO BANNER ================= -->
<section class="py-12 bg-gradient-to-r from-blue-900 via-indigo-900 to-slate-900 text-white relative overflow-hidden">
    <div class="absolute inset-0 bg-[radial-gradient(#38bdf8_1px,transparent_1px)] [background-size:20px_20px] opacity-15"></div>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
        <div class="flex flex-col lg:flex-row items-center justify-between gap-8">
            <div class="space-y-3 text-center lg:text-left">
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-red-500/20 text-red-300 border border-red-500/30 text-xs font-bold uppercase tracking-wider">
                    🔥 ৫০% ও ৩০% স্পেশাল মেগা অফার
                </div>
                <h2 class="text-2xl sm:text-3xl font-extrabold tracking-tight">
                    প্রোগ্রামিং ও আইটি ক্যারিয়ারের শক্ত ভিত্তি গড়তে আজই যুক্ত হন UTC-তে!
                </h2>
                <p class="text-slate-300 text-sm max-w-2xl">
                    সফটওয়্যার ডেভেলপমেন্ট, ওয়েব ডেভেলপমেন্ট, সাইবার সিকিউরিটি কিংবা ফ্রিল্যান্সিং—আন্তর্জাতিক মানের ট্রেনারদের সাথে প্র্যাকটিক্যাল প্রজেক্টে দক্ষতা অর্জন করুন।
                </p>
            </div>
            <div class="flex flex-wrap items-center gap-4 shrink-0">
                <a href="courses.php" class="px-6 py-3.5 rounded-xl bg-amber-500 hover:bg-amber-400 text-slate-950 font-black text-sm shadow-xl shadow-amber-500/25 transition transform hover:-translate-y-0.5">
                    অফারযুক্ত কোর্সগুলো দেখুন
                </a>
                <a href="contact.php" class="px-6 py-3.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-white border border-slate-700 font-bold text-sm transition">
                    ফেনী অফিসে আসুন
                </a>
            </div>
        </div>
    </div>
</section>

<!-- ================= ALL COURSES SHOWCASE SECTION ================= -->
<section id="courses-section" class="py-20 bg-slate-50 dark:bg-[#080d1a] transition-colors duration-300">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <!-- Section Header -->
        <div class="flex flex-col md:flex-row md:items-end justify-between mb-12 gap-6">
            <div>
                <span class="text-xs font-bold tracking-wider text-blue-600 dark:text-cyan-400 uppercase">
                    Industry Standard Curriculum
                </span>
                <h2 class="text-2xl sm:text-3xl md:text-4xl font-extrabold text-slate-900 dark:text-white mt-1">
                    আমাদের বিশেষায়িত আইটি কোর্সসমূহ
                </h2>
                <p class="text-slate-600 dark:text-slate-400 text-sm mt-2 max-w-2xl">
                    ইউআই ল্যাব / লিড একাডেমির মত প্রফেশনাল মডিউলে সাজানো ১০টি ক্যারিয়ার ট্র্যাকিং কোর্স। ৫০% ও ৩০% ছাড়ে ভর্তি চলছে।
                </p>
            </div>
            <div class="flex items-center gap-2">
                <a href="courses.php" class="px-4 py-2 rounded-xl text-sm font-bold text-blue-600 dark:text-cyan-400 hover:bg-blue-50 dark:hover:bg-slate-800 transition flex items-center gap-1.5">
                    সব কোর্স দেখুন <i data-lucide="arrow-right" class="w-4 h-4"></i>
                </a>
            </div>
        </div>

        <!-- Courses Grid (10 Courses) -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            <?php foreach ($courses as $index => $course): ?>
                <?php 
                    $isTopCombo = ($course['id'] == 1);
                    $discountBadge = $course['discount_percent'] ? $course['discount_percent'] . '% OFF' : '30% OFF';
                ?>
                <div class="tilt-card card-3d rounded-3xl glass-panel border border-slate-200 dark:border-slate-800/80 overflow-hidden flex flex-col justify-between group hover:border-blue-500/50 transition-all duration-300 shadow-lg shadow-slate-200/50 dark:shadow-none">
                    
                    <!-- Course Card Top Header -->
                    <div class="p-6 space-y-4">
                        <div class="flex items-start justify-between gap-2">
                            <span class="px-3 py-1 rounded-full text-xs font-bold <?= $isTopCombo ? 'bg-red-500 text-white animate-pulse' : 'bg-blue-100 dark:bg-blue-900/60 text-blue-700 dark:text-blue-300' ?>">
                                <?= sanitize($course['category']) ?>
                            </span>
                            <span class="badge-discount px-3 py-1 rounded-full text-white text-xs font-black shrink-0">
                                <?= $discountBadge ?>
                            </span>
                        </div>

                        <!-- Course Title -->
                        <h3 class="text-lg font-bold text-slate-900 dark:text-white group-hover:text-blue-600 dark:group-hover:text-cyan-400 transition-colors line-clamp-2 leading-snug">
                            <a href="course-details.php?id=<?= $course['id'] ?>">
                                <?= sanitize($course['title']) ?>
                            </a>
                        </h3>

                        <!-- Short Description -->
                        <p class="text-xs text-slate-500 dark:text-slate-400 line-clamp-3 leading-relaxed">
                            <?= sanitize($course['short_desc']) ?>
                        </p>

                        <!-- Course Meta Stats -->
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

                    <!-- Course Card Bottom / Pricing & Action -->
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

    </div>
</section>

<!-- ================= "কেন UTC বেছে নেবেন?" (WHY CHOOSE UTC) ================= -->
<section id="why-utc" class="py-20 relative bg-white dark:bg-[#0b1329] transition-colors duration-300 overflow-hidden">
    <!-- Glow decorative backdrops -->
    <div class="absolute -top-40 right-0 w-80 h-80 bg-blue-600/10 rounded-full blur-3xl pointer-events-none"></div>
    <div class="absolute -bottom-40 left-0 w-80 h-80 bg-cyan-600/10 rounded-full blur-3xl pointer-events-none"></div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
        
        <div class="text-center max-w-3xl mx-auto mb-16 space-y-3">
            <span class="px-3.5 py-1 rounded-full text-xs font-extrabold bg-blue-500/10 text-blue-600 dark:text-cyan-400 border border-blue-500/20">
                UNIVERSAL TECHNOLOGY CORPORATION ADVANTAGE
            </span>
            <h2 class="text-3xl sm:text-4xl font-extrabold text-slate-900 dark:text-white tracking-tight">
                🌟 কেন UTC বেছে নেবেন?
            </h2>
            <p class="text-slate-600 dark:text-slate-400 text-base font-medium">
                সঠিক গাইডলাইন + প্র্যাকটিক্যাল শিক্ষা = সফল আইটি প্রফেশনাল ও প্রোগ্রামার হওয়ার পথ
            </p>
        </div>

        <!-- 8 Core Advantage Pillars (from user requirements) -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            
            <!-- 1 -->
            <div class="tilt-card card-3d p-6 rounded-2xl glass-panel border border-slate-200 dark:border-slate-800 space-y-3 hover:border-blue-500/40">
                <div class="w-12 h-12 rounded-xl bg-blue-500/10 text-blue-600 dark:text-cyan-400 flex items-center justify-center font-bold">
                    <i data-lucide="globe-2" class="w-6 h-6"></i>
                </div>
                <h3 class="text-base font-bold text-slate-900 dark:text-white">আন্তর্জাতিক মানের কোর্স মডিউল</h3>
                <p class="text-xs text-slate-500 dark:text-slate-400 leading-relaxed">
                    গ্লোবাল টেক ট্রেন্ড, ইন্ডাস্ট্রি স্ট্যান্ডার্ড এবং রিয়েল-লাইফ প্রজেক্ট ভিত্তিক আধুনিক সিলেবাস।
                </p>
            </div>

            <!-- 2 -->
            <div class="tilt-card card-3d p-6 rounded-2xl glass-panel border border-slate-200 dark:border-slate-800 space-y-3 hover:border-emerald-500/40">
                <div class="w-12 h-12 rounded-xl bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 flex items-center justify-center font-bold">
                    <i data-lucide="user-check" class="w-6 h-6"></i>
                </div>
                <h3 class="text-base font-bold text-slate-900 dark:text-white">দক্ষ ও প্রফেশনাল ট্রেনার</h3>
                <p class="text-xs text-slate-500 dark:text-slate-400 leading-relaxed">
                    সফটওয়্যার ইন্ডাস্ট্রি এবং ফ্রিল্যান্সিংয়ে কর্মরত অভিজ্ঞ ট্রেনারদের মাধ্যমে লাইভ ও অফলাইন ক্লাস।
                </p>
            </div>

            <!-- 3 -->
            <div class="tilt-card card-3d p-6 rounded-2xl glass-panel border border-slate-200 dark:border-slate-800 space-y-3 hover:border-purple-500/40">
                <div class="w-12 h-12 rounded-xl bg-purple-500/10 text-purple-600 dark:text-purple-400 flex items-center justify-center font-bold">
                    <i data-lucide="video" class="w-6 h-6"></i>
                </div>
                <h3 class="text-base font-bold text-slate-900 dark:text-white">ভিডিও রেকর্ড প্রদান</h3>
                <p class="text-xs text-slate-500 dark:text-slate-400 leading-relaxed">
                    প্রতিটি ক্লাসের ফুল এইচডি ভিডিও রেকর্ড ও স্টাডি মেটেরিয়ালস স্টুডেন্ট পোর্টালে সারাজীবন এক্সেস।
                </p>
            </div>

            <!-- 4 -->
            <div class="tilt-card card-3d p-6 rounded-2xl glass-panel border border-slate-200 dark:border-slate-800 space-y-3 hover:border-amber-500/40">
                <div class="w-12 h-12 rounded-xl bg-amber-500/10 text-amber-600 dark:text-amber-400 flex items-center justify-center font-bold">
                    <i data-lucide="building-2" class="w-6 h-6"></i>
                </div>
                <h3 class="text-base font-bold text-slate-900 dark:text-white">Industrial Training-এর সুযোগ</h3>
                <p class="text-xs text-slate-500 dark:text-slate-400 leading-relaxed">
                    কোর্স চলাকালীন সরাসরি বাস্তব ক্লায়েন্ট প্রজেক্ট ও ইন্ডাস্ট্রিয়াল কাজের অভিজ্ঞতা অর্জনের সুবিধা।
                </p>
            </div>

            <!-- 5 -->
            <div class="tilt-card card-3d p-6 rounded-2xl glass-panel border border-slate-200 dark:border-slate-800 space-y-3 hover:border-cyan-500/40">
                <div class="w-12 h-12 rounded-xl bg-cyan-500/10 text-cyan-600 dark:text-cyan-400 flex items-center justify-center font-bold">
                    <i data-lucide="headphones" class="w-6 h-6"></i>
                </div>
                <h3 class="text-base font-bold text-slate-900 dark:text-white">২৪ ঘণ্টা Online & Offline Support</h3>
                <p class="text-xs text-slate-500 dark:text-slate-400 leading-relaxed">
                    প্রোগ্রামিং বা ডিজাইনে আটকে গেলে সার্বক্ষণিক ডেডিকেটেড মেন্টর সাপোর্ট ও ক্যাম্পাসে বসে কোডিং হেল্প।
                </p>
            </div>

            <!-- 6 -->
            <div class="tilt-card card-3d p-6 rounded-2xl glass-panel border border-slate-200 dark:border-slate-800 space-y-3 hover:border-rose-500/40">
                <div class="w-12 h-12 rounded-xl bg-rose-500/10 text-rose-600 dark:text-rose-400 flex items-center justify-center font-bold">
                    <i data-lucide="repeat" class="w-6 h-6"></i>
                </div>
                <h3 class="text-base font-bold text-slate-900 dark:text-white">Lifetime Free Repeat Facility</h3>
                <p class="text-xs text-slate-500 dark:text-slate-400 leading-relaxed">
                    কোন ক্লাস বা টপিক বুঝতে ঘাটতি থাকলে পরবর্তী যেকোনো ব্যাচে সম্পূর্ণ ফ্রিতে রিপিট করার সুযোগ।
                </p>
            </div>

            <!-- 7 -->
            <div class="tilt-card card-3d p-6 rounded-2xl glass-panel border border-slate-200 dark:border-slate-800 space-y-3 hover:border-blue-500/40">
                <div class="w-12 h-12 rounded-xl bg-blue-500/10 text-blue-600 dark:text-blue-400 flex items-center justify-center font-bold">
                    <i data-lucide="badge-check" class="w-6 h-6"></i>
                </div>
                <h3 class="text-base font-bold text-slate-900 dark:text-white">Professional Certificate প্রদান</h3>
                <p class="text-xs text-slate-500 dark:text-slate-400 leading-relaxed">
                    কোর্স ও প্রজেক্ট সমাপ্তির পর দেশি-বিদেশি জবে গ্রহণযোগ্য ইউনিক কিউআর ভেরিফাইড প্রফেশনাল সার্টিফিকেট।
                </p>
            </div>

            <!-- 8 -->
            <div class="tilt-card card-3d p-6 rounded-2xl glass-panel border border-slate-200 dark:border-slate-800 space-y-3 hover:border-emerald-500/40">
                <div class="w-12 h-12 rounded-xl bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 flex items-center justify-center font-bold">
                    <i data-lucide="laptop" class="w-6 h-6"></i>
                </div>
                <h3 class="text-base font-bold text-slate-900 dark:text-white">Industry Practical Learning</h3>
                <p class="text-xs text-slate-500 dark:text-slate-400 leading-relaxed">
                    মুখস্থ বিদ্যা নয়, রিয়েল-ওয়ার্ল্ড প্রজেক্ট, গিটহাব রিপোজিটরি ও পোর্টফোলিও তৈরির মাধ্যমে হাতে-কলমে শিক্ষা।
                </p>
            </div>

        </div>

    </div>
</section>

<!-- ================= THREE PORTALS INTRO (Student, Teacher, Admin) ================= -->
<section class="py-20 bg-slate-100/60 dark:bg-[#070b16] transition-colors duration-300">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center max-w-2xl mx-auto mb-14">
            <h2 class="text-2xl sm:text-3xl font-extrabold text-slate-900 dark:text-white">
                সম্পূর্ণ ডিজিটাল ম্যানেজমেন্ট সিস্টেম
            </h2>
            <p class="text-slate-600 dark:text-slate-400 text-sm mt-2">
                শিক্ষার্থী, শিক্ষক ও প্রশাসনের জন্য আলাদা সুবিন্যস্ত পোর্টাল
            </p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            
            <!-- Student Portal Card -->
            <div class="p-8 rounded-3xl glass-panel border border-blue-500/20 text-center space-y-4 hover:shadow-2xl transition">
                <div class="w-16 h-16 rounded-2xl bg-blue-600 text-white flex items-center justify-center mx-auto shadow-lg shadow-blue-500/30">
                    <i data-lucide="user" class="w-8 h-8"></i>
                </div>
                <h3 class="text-xl font-bold text-slate-900 dark:text-white">Student Portal</h3>
                <p class="text-xs text-slate-500 dark:text-slate-400 leading-relaxed">
                    এনরোল করা কোর্স, ভিডিও লেকচার, অ্যাসাইনমেন্ট সাবমিশন, রিসোর্স ডাউনলোড, পেমেন্ট হিস্ট্রি ও ডিজিটাল সার্টিফিকেট।
                </p>
                <div class="pt-4">
                    <a href="student/index.php" class="px-5 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-500 text-white font-bold text-xs inline-flex items-center gap-2">
                        স্টুডেন্ট পোর্টালে যান <i data-lucide="arrow-right" class="w-4 h-4"></i>
                    </a>
                </div>
            </div>

            <!-- Teacher Portal Card -->
            <div class="p-8 rounded-3xl glass-panel border border-emerald-500/20 text-center space-y-4 hover:shadow-2xl transition">
                <div class="w-16 h-16 rounded-2xl bg-emerald-600 text-white flex items-center justify-center mx-auto shadow-lg shadow-emerald-500/30">
                    <i data-lucide="graduation-cap" class="w-8 h-8"></i>
                </div>
                <h3 class="text-xl font-bold text-slate-900 dark:text-white">Teacher Portal</h3>
                <p class="text-xs text-slate-500 dark:text-slate-400 leading-relaxed">
                    কোর্স ও লেসন আপলোড, লাইভ ক্লাসের শিডিউল, স্টুডেন্ট অ্যাসাইনমেন্ট মূল্যায়ন ও ফিডব্যাক প্রদান।
                </p>
                <div class="pt-4">
                    <a href="teacher/index.php" class="px-5 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white font-bold text-xs inline-flex items-center gap-2">
                        টিচার পোর্টালে যান <i data-lucide="arrow-right" class="w-4 h-4"></i>
                    </a>
                </div>
            </div>

            <!-- Admin Panel Card -->
            <div class="p-8 rounded-3xl glass-panel border border-purple-500/20 text-center space-y-4 hover:shadow-2xl transition">
                <div class="w-16 h-16 rounded-2xl bg-purple-600 text-white flex items-center justify-center mx-auto shadow-lg shadow-purple-500/30">
                    <i data-lucide="shield-check" class="w-8 h-8"></i>
                </div>
                <h3 class="text-xl font-bold text-slate-900 dark:text-white">Admin Panel</h3>
                <p class="text-xs text-slate-500 dark:text-slate-400 leading-relaxed">
                    কোর্স ম্যানেজমেন্ট, বিকাশ/নগদ পেমেন্ট অনুমোদন, ইউজার ও ট্রেনার ম্যানেজমেন্ট এবং রেভিনিউ রিপোর্ট।
                </p>
                <div class="pt-4">
                    <a href="admin/index.php" class="px-5 py-2.5 rounded-xl bg-purple-600 hover:bg-purple-500 text-white font-bold text-xs inline-flex items-center gap-2">
                        অ্যাডমিন প্যানেলে যান <i data-lucide="arrow-right" class="w-4 h-4"></i>
                    </a>
                </div>
            </div>

        </div>
    </div>
</section>

<!-- ================= CAMPUS CONTACT & LOCATION ================= -->
<section class="py-20 bg-white dark:bg-[#080d1a] border-t border-slate-200 dark:border-slate-800">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-12 items-center">
            
            <div class="lg:col-span-6 space-y-6">
                <span class="text-xs font-bold uppercase tracking-wider text-blue-600 dark:text-cyan-400">
                    📍 Head Office & Training Labs
                </span>
                <h2 class="text-3xl font-extrabold text-slate-900 dark:text-white tracking-tight">
                    সরাসরি আমাদের ফেনী ক্যাম্পাসে আসুন
                </h2>
                <p class="text-slate-600 dark:text-slate-400 text-sm leading-relaxed">
                    ফেনী শহরের প্রাণকেন্দ্রে অবস্থিত আমাদের শীতাতপ নিয়ন্ত্রিত আধুনিক হাই-স্পিড ইন্টারনেট ল্যাব, যেখানে আপনি ট্রেনারদের সাথে সরাসরি কথা বলে কোর্স বেছে নিতে পারেন।
                </p>

                <div class="space-y-4 pt-2">
                    <div class="flex items-start gap-3">
                        <div class="w-10 h-10 rounded-xl bg-amber-500/10 text-amber-600 dark:text-amber-400 flex items-center justify-center shrink-0">
                            <i data-lucide="map-pin" class="w-5 h-5"></i>
                        </div>
                        <div>
                            <h4 class="font-bold text-slate-900 dark:text-white text-sm">ঠিকানা:</h4>
                            <p class="text-xs text-slate-600 dark:text-slate-400">287 (1st Floor), Doctor Para, Feni-3900</p>
                        </div>
                    </div>

                    <div class="flex items-start gap-3">
                        <div class="w-10 h-10 rounded-xl bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 flex items-center justify-center shrink-0">
                            <i data-lucide="phone" class="w-5 h-5"></i>
                        </div>
                        <div>
                            <h4 class="font-bold text-slate-900 dark:text-white text-sm">যোগাযোগ নম্বর:</h4>
                            <p class="text-xs text-slate-600 dark:text-slate-400 font-semibold">01812669757, 01817122483</p>
                        </div>
                    </div>

                    <div class="flex items-start gap-3">
                        <div class="w-10 h-10 rounded-xl bg-cyan-500/10 text-cyan-600 dark:text-cyan-400 flex items-center justify-center shrink-0">
                            <i data-lucide="mail" class="w-5 h-5"></i>
                        </div>
                        <div>
                            <h4 class="font-bold text-slate-900 dark:text-white text-sm">ইমেইল:</h4>
                            <p class="text-xs text-slate-600 dark:text-slate-400">utcsoftltd@gmail.com</p>
                        </div>
                    </div>
                </div>

                <div class="pt-4 flex items-center gap-4">
                    <a href="contact.php" class="px-6 py-3 rounded-xl bg-blue-600 hover:bg-blue-500 text-white font-bold text-sm shadow-md transition">
                        অনলাইন কাউন্সেলিং বুক করুন
                    </a>
                    <a href="tel:01812669757" class="px-5 py-3 rounded-xl border border-slate-300 dark:border-slate-700 hover:bg-slate-100 dark:hover:bg-slate-800 text-slate-800 dark:text-white font-bold text-sm transition">
                        সরাসরি কল করুন
                    </a>
                </div>
            </div>

            <div class="lg:col-span-6">
                <!-- Location Map Embed / Tech Center visual -->
                <div class="rounded-3xl overflow-hidden shadow-2xl border border-slate-200 dark:border-slate-800 bg-slate-900 p-2">
                    <div class="p-6 rounded-2xl bg-gradient-to-br from-slate-900 to-slate-950 text-white space-y-4">
                        <div class="flex items-center justify-between">
                            <span class="text-xs font-bold text-cyan-400 tracking-wider uppercase">UTC Feni Campus</span>
                            <span class="px-2.5 py-1 rounded-md text-[10px] font-bold bg-emerald-500/20 text-emerald-300">Open 9:00 AM - 9:00 PM</span>
                        </div>
                        <h3 class="text-xl font-bold">Universal Technology Corporation (UTC)</h3>
                        <p class="text-xs text-slate-400">
                            ২৮৭ (১ম তলা), ডক্টর পাড়া, ফেনী-৩৯০০
                        </p>
                        <div class="h-48 rounded-xl bg-slate-800 flex flex-col items-center justify-center text-center p-4 border border-slate-700/60 relative overflow-hidden">
                            <div class="w-12 h-12 rounded-full bg-blue-600/30 text-blue-400 flex items-center justify-center mb-2">
                                <i data-lucide="map" class="w-6 h-6"></i>
                            </div>
                            <span class="text-sm font-bold">Doctor Para, Feni Map Locator</span>
                            <span class="text-xs text-slate-400 mt-1">ক্যাম্পাস ভিজিটের জন্য Google Maps বা স্থানীয় দিকনির্দেশনা দেখুন</span>
                            <a href="https://maps.google.com/?q=Doctor+Para+Feni" target="_blank" class="mt-3 px-4 py-1.5 rounded-lg bg-blue-600 hover:bg-blue-500 text-xs font-bold text-white transition flex items-center gap-1.5">
                                <i data-lucide="external-link" class="w-3.5 h-3.5"></i> Google Maps-এ খুলুন
                            </a>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
