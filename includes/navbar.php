<?php
/**
 * Universal Technology Corporation (UTC) - Navbar
 * Enhanced with Official UTC Logo, Rich Courses Mega Dropdown, and Smooth Unbreakable Hover Transitions
 */
$currentUser = getCurrentUser();
$appRoot = getAppRoot();
$currentScript = $_SERVER['SCRIPT_NAME'] ?? '';
?>

<!-- Announcement Strip -->
<div class="bg-gradient-to-r from-blue-700 via-indigo-700 to-cyan-600 text-white text-xs sm:text-sm py-2 px-4 shadow-sm relative z-50">
    <div class="max-w-7xl mx-auto flex flex-wrap justify-between items-center gap-2">
        <div class="flex items-center space-x-2 font-medium">
            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold bg-amber-400 text-slate-900 animate-pulse">
                🔥 স্পেশাল অফার
            </span>
            <span>GOOD NEWS! আইটি ও প্রোগ্রামিং কোর্সে ৫০% ও ৩০% স্পেশাল ডিসকাউন্ট!</span>
        </div>
        <div class="flex items-center space-x-4 text-xs font-medium">
            <a href="tel:01812669757" class="hover:text-amber-300 transition flex items-center gap-1">
                <i data-lucide="phone-call" class="w-3.5 h-3.5"></i> 01812669757
            </a>
            <span class="hidden md:inline opacity-60">|</span>
            <a href="mailto:utcsoftltd@gmail.com" class="hidden md:flex hover:text-amber-300 transition items-center gap-1">
                <i data-lucide="mail" class="w-3.5 h-3.5"></i> utcsoftltd@gmail.com
            </a>
            <span class="hidden lg:inline opacity-60">|</span>
            <span class="hidden lg:inline text-amber-200">📍 ডক্টর পাড়া, ফেনী</span>
        </div>
    </div>
</div>

<!-- Main Navigation -->
<header class="sticky top-0 z-50 glass-nav transition-colors duration-300">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between h-20">
            
            <!-- Official Brand Logo -->
            <a href="<?= $appRoot ?>index.php" class="flex items-center gap-3 group shrink-0">
                <div class="relative">
                    <img src="<?= $appRoot ?>assets/images/utc-logo.png" 
                         alt="Universal Technology Corporation Logo" 
                         class="w-12 h-12 rounded-full object-contain bg-white shadow-md border-2 border-blue-500/30 group-hover:border-blue-600 group-hover:scale-105 transition-all duration-300 p-0.5">
                    <span class="absolute -top-0.5 -right-0.5 w-3.5 h-3.5 bg-emerald-500 rounded-full border-2 border-white dark:border-slate-900 animate-pulse"></span>
                </div>
                <div class="flex flex-col">
                    <span class="font-extrabold text-lg sm:text-xl tracking-tight text-slate-900 dark:text-white leading-none group-hover:text-blue-600 dark:group-hover:text-cyan-400 transition-colors">
                        Universal Technology
                    </span>
                    <span class="text-[11px] font-bold text-blue-600 dark:text-cyan-400 tracking-wider uppercase mt-1">
                        Corporation • Feni IT Hub
                    </span>
                </div>
            </a>

            <!-- Desktop Navigation Links -->
            <nav class="hidden md:flex items-center space-x-1 lg:space-x-2 text-sm font-semibold">
                
                <a href="<?= $appRoot ?>index.php" class="nav-item-link <?= strpos($currentScript, 'index.php') !== false ? 'active text-blue-600 dark:text-cyan-400' : '' ?>">
                    <i data-lucide="home" class="w-4 h-4 text-blue-500"></i>
                    <span>Home</span>
                </a>

                <!-- Courses Dropdown with Unbreakable Hover Bridge -->
                <div class="relative group">
                    <a href="<?= $appRoot ?>courses.php" class="nav-item-link flex items-center gap-1.5 <?= strpos($currentScript, 'courses.php') !== false ? 'active text-blue-600 dark:text-cyan-400' : '' ?>">
                        <i data-lucide="book-open" class="w-4 h-4 text-emerald-500"></i>
                        <span>কোর্সসমূহ</span>
                        <span class="px-1.5 py-0.5 text-[10px] bg-red-500 text-white rounded-full font-bold">১০টি কোর্স</span>
                        <i data-lucide="chevron-down" class="w-3.5 h-3.5 text-slate-400 group-hover:rotate-180 transition-transform duration-200"></i>
                    </a>

                    <!-- Hover Mega Dropdown Menu -->
                    <div class="dropdown-hover-bridge left-0 w-[640px] opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-200 transform translate-y-1 group-hover:translate-y-0 pointer-events-none group-hover:pointer-events-auto">
                        <div class="rounded-3xl glass-panel shadow-2xl p-6 border border-slate-200 dark:border-slate-800 backdrop-blur-xl space-y-4">
                            
                            <div class="flex items-center justify-between pb-3 border-b border-slate-200 dark:border-slate-800">
                                <div class="flex items-center gap-2">
                                    <span class="w-2.5 h-2.5 rounded-full bg-blue-600 animate-ping"></span>
                                    <h4 class="font-extrabold text-sm text-slate-900 dark:text-white uppercase tracking-wider">
                                        UTC প্রফেশনাল আইটি কোর্স ক্যাটালগ
                                    </h4>
                                </div>
                                <span class="text-xs font-bold text-amber-500">🔥 ৫০% ও ৩০% স্পেশাল ছাড়</span>
                            </div>

                            <div class="grid grid-cols-2 gap-4 text-xs">
                                
                                <!-- Col 1: Software & Web Development -->
                                <div class="space-y-2">
                                    <span class="font-bold text-[11px] uppercase tracking-wider text-slate-400 block px-2">
                                        💻 সফটওয়্যার ও ওয়েব ডেভেলপমেন্ট
                                    </span>
                                    
                                    <a href="<?= $appRoot ?>course-details.php?id=1" class="p-2.5 rounded-xl hover:bg-blue-50 dark:hover:bg-slate-800/80 transition flex items-start gap-2.5 group/item">
                                        <div class="w-8 h-8 rounded-lg bg-blue-600/10 text-blue-600 dark:text-cyan-400 flex items-center justify-center font-bold shrink-0 mt-0.5 group-hover/item:bg-blue-600 group-hover/item:text-white transition">
                                            01
                                        </div>
                                        <div>
                                            <div class="font-bold text-slate-900 dark:text-white group-hover/item:text-blue-600 dark:group-hover/item:text-cyan-400 leading-snug">
                                                Programming Combo Pack
                                            </div>
                                            <div class="text-[10px] text-slate-500 flex items-center gap-2 mt-0.5">
                                                <span class="text-red-500 font-bold">৫০% ছাড়</span>
                                                <span>৳১০,০০০</span>
                                            </div>
                                        </div>
                                    </a>

                                    <a href="<?= $appRoot ?>course-details.php?id=2" class="p-2.5 rounded-xl hover:bg-blue-50 dark:hover:bg-slate-800/80 transition flex items-start gap-2.5 group/item">
                                        <div class="w-8 h-8 rounded-lg bg-emerald-600/10 text-emerald-600 dark:text-emerald-400 flex items-center justify-center font-bold shrink-0 mt-0.5 group-hover/item:bg-emerald-600 group-hover/item:text-white transition">
                                            02
                                        </div>
                                        <div>
                                            <div class="font-bold text-slate-900 dark:text-white group-hover/item:text-blue-600 dark:group-hover/item:text-cyan-400 leading-snug">
                                                Advance Web Design (HTML, CSS, Tailwind)
                                            </div>
                                            <div class="text-[10px] text-slate-500 flex items-center gap-2 mt-0.5">
                                                <span class="text-amber-500 font-bold">৩০% ছাড়</span>
                                                <span>৳৪,৯০০</span>
                                            </div>
                                        </div>
                                    </a>

                                    <a href="<?= $appRoot ?>course-details.php?id=3" class="p-2.5 rounded-xl hover:bg-blue-50 dark:hover:bg-slate-800/80 transition flex items-start gap-2.5 group/item">
                                        <div class="w-8 h-8 rounded-lg bg-purple-600/10 text-purple-600 dark:text-purple-400 flex items-center justify-center font-bold shrink-0 mt-0.5 group-hover/item:bg-purple-600 group-hover/item:text-white transition">
                                            03
                                        </div>
                                        <div>
                                            <div class="font-bold text-slate-900 dark:text-white group-hover/item:text-blue-600 dark:group-hover/item:text-cyan-400 leading-snug">
                                                Web Development (PHP, JS, 4 DBs)
                                            </div>
                                            <div class="text-[10px] text-slate-500 flex items-center gap-2 mt-0.5">
                                                <span class="text-amber-500 font-bold">৩০% ছাড়</span>
                                                <span>৳১৬,৮০০</span>
                                            </div>
                                        </div>
                                    </a>

                                    <a href="<?= $appRoot ?>course-details.php?id=4" class="p-2.5 rounded-xl hover:bg-blue-50 dark:hover:bg-slate-800/80 transition flex items-start gap-2.5 group/item">
                                        <div class="w-8 h-8 rounded-lg bg-red-600/10 text-red-600 dark:text-red-400 flex items-center justify-center font-bold shrink-0 mt-0.5 group-hover/item:bg-red-600 group-hover/item:text-white transition">
                                            04
                                        </div>
                                        <div>
                                            <div class="font-bold text-slate-900 dark:text-white group-hover/item:text-blue-600 dark:group-hover/item:text-cyan-400 leading-snug">
                                                Laravel Framework & Freelancing
                                            </div>
                                            <div class="text-[10px] text-slate-500 flex items-center gap-2 mt-0.5">
                                                <span class="text-amber-500 font-bold">৩০% ছাড়</span>
                                                <span>৳২১,০০০</span>
                                            </div>
                                        </div>
                                    </a>

                                    <a href="<?= $appRoot ?>course-details.php?id=5" class="p-2.5 rounded-xl hover:bg-blue-50 dark:hover:bg-slate-800/80 transition flex items-start gap-2.5 group/item">
                                        <div class="w-8 h-8 rounded-lg bg-cyan-600/10 text-cyan-600 dark:text-cyan-400 flex items-center justify-center font-bold shrink-0 mt-0.5 group-hover/item:bg-cyan-600 group-hover/item:text-white transition">
                                            05
                                        </div>
                                        <div>
                                            <div class="font-bold text-slate-900 dark:text-white group-hover/item:text-blue-600 dark:group-hover/item:text-cyan-400 leading-snug">
                                                MERN Stack Web Development
                                            </div>
                                            <div class="text-[10px] text-slate-500 flex items-center gap-2 mt-0.5">
                                                <span class="text-amber-500 font-bold">৩০% ছাড়</span>
                                                <span>৳২১,০০০</span>
                                            </div>
                                        </div>
                                    </a>
                                </div>

                                <!-- Col 2: Advanced Tech, App & Security -->
                                <div class="space-y-2">
                                    <span class="font-bold text-[11px] uppercase tracking-wider text-slate-400 block px-2">
                                        🚀 মোবাইল অ্যাপ, ডেটা ও সিকিউরিটি
                                    </span>

                                    <a href="<?= $appRoot ?>course-details.php?id=8" class="p-2.5 rounded-xl hover:bg-blue-50 dark:hover:bg-slate-800/80 transition flex items-start gap-2.5 group/item">
                                        <div class="w-8 h-8 rounded-lg bg-blue-500/10 text-blue-500 flex items-center justify-center font-bold shrink-0 mt-0.5 group-hover/item:bg-blue-500 group-hover/item:text-white transition">
                                            08
                                        </div>
                                        <div>
                                            <div class="font-bold text-slate-900 dark:text-white group-hover/item:text-blue-600 dark:group-hover/item:text-cyan-400 leading-snug">
                                                Flutter & Dart Mobile App (Android/iOS)
                                            </div>
                                            <div class="text-[10px] text-slate-500 flex items-center gap-2 mt-0.5">
                                                <span class="text-amber-500 font-bold">৩০% ছাড়</span>
                                                <span>৳২১,০০০</span>
                                            </div>
                                        </div>
                                    </a>

                                    <a href="<?= $appRoot ?>course-details.php?id=10" class="p-2.5 rounded-xl hover:bg-blue-50 dark:hover:bg-slate-800/80 transition flex items-start gap-2.5 group/item">
                                        <div class="w-8 h-8 rounded-lg bg-rose-600/10 text-rose-600 dark:text-rose-400 flex items-center justify-center font-bold shrink-0 mt-0.5 group-hover/item:bg-rose-600 group-hover/item:text-white transition">
                                            10
                                        </div>
                                        <div>
                                            <div class="font-bold text-slate-900 dark:text-white group-hover/item:text-blue-600 dark:group-hover/item:text-cyan-400 leading-snug">
                                                Certification Ethical Hacking (CEHv11)
                                            </div>
                                            <div class="text-[10px] text-slate-500 flex items-center gap-2 mt-0.5">
                                                <span class="text-amber-500 font-bold">৩০% ছাড়</span>
                                                <span>৳৫৬,০০০</span>
                                            </div>
                                        </div>
                                    </a>

                                    <a href="<?= $appRoot ?>course-details.php?id=7" class="p-2.5 rounded-xl hover:bg-blue-50 dark:hover:bg-slate-800/80 transition flex items-start gap-2.5 group/item">
                                        <div class="w-8 h-8 rounded-lg bg-amber-600/10 text-amber-600 dark:text-amber-400 flex items-center justify-center font-bold shrink-0 mt-0.5 group-hover/item:bg-amber-600 group-hover/item:text-white transition">
                                            07
                                        </div>
                                        <div>
                                            <div class="font-bold text-slate-900 dark:text-white group-hover/item:text-blue-600 dark:group-hover/item:text-cyan-400 leading-snug">
                                                CompTIA A+ & CCNA Networking
                                            </div>
                                            <div class="text-[10px] text-slate-500 flex items-center gap-2 mt-0.5">
                                                <span class="text-amber-500 font-bold">৩০% ছাড়</span>
                                                <span>৳১০,৫০০</span>
                                            </div>
                                        </div>
                                    </a>

                                    <a href="<?= $appRoot ?>course-details.php?id=9" class="p-2.5 rounded-xl hover:bg-blue-50 dark:hover:bg-slate-800/80 transition flex items-start gap-2.5 group/item">
                                        <div class="w-8 h-8 rounded-lg bg-teal-600/10 text-teal-600 dark:text-teal-400 flex items-center justify-center font-bold shrink-0 mt-0.5 group-hover/item:bg-teal-600 group-hover/item:text-white transition">
                                            09
                                        </div>
                                        <div>
                                            <div class="font-bold text-slate-900 dark:text-white group-hover/item:text-blue-600 dark:group-hover/item:text-cyan-400 leading-snug">
                                                Python Programming with BigData
                                            </div>
                                            <div class="text-[10px] text-slate-500 flex items-center gap-2 mt-0.5">
                                                <span class="text-amber-500 font-bold">৩০% ছাড়</span>
                                                <span>৳২১,০০০</span>
                                            </div>
                                        </div>
                                    </a>

                                    <a href="<?= $appRoot ?>course-details.php?id=6" class="p-2.5 rounded-xl hover:bg-blue-50 dark:hover:bg-slate-800/80 transition flex items-start gap-2.5 group/item">
                                        <div class="w-8 h-8 rounded-lg bg-indigo-600/10 text-indigo-600 dark:text-indigo-400 flex items-center justify-center font-bold shrink-0 mt-0.5 group-hover/item:bg-indigo-600 group-hover/item:text-white transition">
                                            06
                                        </div>
                                        <div>
                                            <div class="font-bold text-slate-900 dark:text-white group-hover/item:text-blue-600 dark:group-hover/item:text-cyan-400 leading-snug">
                                                Python Django Web Development
                                            </div>
                                            <div class="text-[10px] text-slate-500 flex items-center gap-2 mt-0.5">
                                                <span class="text-amber-500 font-bold">৩০% ছাড়</span>
                                                <span>৳২১,০০০</span>
                                            </div>
                                        </div>
                                    </a>
                                </div>

                            </div>

                            <!-- Mega Dropdown Footer -->
                            <div class="pt-3 border-t border-slate-200 dark:border-slate-800 flex items-center justify-between">
                                <span class="text-[11px] text-slate-500">📍 UTC ডক্টর পাড়া, ফেনী ক্যাম্পাসে সরাসরি এসে ভর্তির সুযোগ</span>
                                <a href="<?= $appRoot ?>courses.php" class="px-4 py-1.5 rounded-xl bg-blue-600 hover:bg-blue-500 text-white font-bold text-xs transition flex items-center gap-1.5 shadow-md">
                                    <span>সবগুলো কোর্স দেখুন</span>
                                    <i data-lucide="arrow-right" class="w-3.5 h-3.5"></i>
                                </a>
                            </div>

                        </div>
                    </div>
                </div>

                <a href="<?= $appRoot ?>index.php#why-utc" class="nav-item-link">
                    <i data-lucide="sparkles" class="w-4 h-4 text-amber-500"></i>
                    <span>কেন UTC?</span>
                </a>
                
                <a href="<?= $appRoot ?>about.php" class="nav-item-link <?= strpos($currentScript, 'about.php') !== false ? 'active text-blue-600 dark:text-cyan-400' : '' ?>">
                    <span>আমাদের সম্পর্কে</span>
                </a>
                
                <a href="<?= $appRoot ?>contact.php" class="nav-item-link <?= strpos($currentScript, 'contact.php') !== false ? 'active text-blue-600 dark:text-cyan-400' : '' ?>">
                    <span>যোগাযোগ</span>
                </a>

                <?php if ($currentUser): ?>
                    <?php if ($currentUser['role'] === 'admin'): ?>
                        <a href="<?= $appRoot ?>admin/index.php" class="px-3.5 py-1.5 rounded-xl bg-purple-600/10 text-purple-600 dark:text-purple-400 font-bold border border-purple-500/20 hover:bg-purple-600 hover:text-white transition-all duration-200 flex items-center gap-1.5 shadow-sm">
                            <i data-lucide="shield-check" class="w-4 h-4"></i> Admin Panel
                        </a>
                    <?php elseif ($currentUser['role'] === 'teacher'): ?>
                        <a href="<?= $appRoot ?>teacher/index.php" class="px-3.5 py-1.5 rounded-xl bg-emerald-600/10 text-emerald-600 dark:text-emerald-400 font-bold border border-emerald-500/20 hover:bg-emerald-600 hover:text-white transition-all duration-200 flex items-center gap-1.5 shadow-sm">
                            <i data-lucide="graduation-cap" class="w-4 h-4"></i> Teacher Portal
                        </a>
                    <?php else: ?>
                        <a href="<?= $appRoot ?>student/index.php" class="px-3.5 py-1.5 rounded-xl bg-blue-600/10 text-blue-600 dark:text-blue-400 font-bold border border-blue-500/20 hover:bg-blue-600 hover:text-white transition-all duration-200 flex items-center gap-1.5 shadow-sm">
                            <i data-lucide="layout-dashboard" class="w-4 h-4"></i> Student Portal
                        </a>
                    <?php endif; ?>
                <?php endif; ?>
            </nav>

            <!-- Right Controls: Dark/Light Mode + Auth -->
            <div class="flex items-center space-x-3">
                
                <!-- Theme Toggle Button -->
                <button type="button" class="theme-toggle-btn p-2.5 rounded-xl text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800 hover:text-amber-500 dark:hover:text-amber-400 focus:outline-none transition-all duration-200 border border-slate-200 dark:border-slate-700/60 shadow-sm" aria-label="Toggle Theme">
                    <!-- Sun Icon (shown in dark mode) -->
                    <svg class="theme-icon-sun w-5 h-5 text-amber-400 hidden" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 2a1 1 0 011 1v1a1 1 0 11-2 0V3a1 1 0 011-1zm4 8a4 4 0 11-8 0 4 4 0 018 0zm-.464 4.95l.707.707a1 1 0 001.414-1.414l-.707-.707a1 1 0 00-1.414 1.414zm2.12-10.607a1 1 0 010 1.414l-.706.707a1 1 0 11-1.414-1.414l.707-.707a1 1 0 011.414 0zM17 11a1 1 0 100-2h-1a1 1 0 100 2h1zm-7 4a1 1 0 011 1v1a1 1 0 11-2 0v-1a1 1 0 011-1zM5.05 6.464A1 1 0 106.465 5.05l-.708-.707a1 1 0 00-1.414 1.414l.707.707zm1.414 8.486l-.707.707a1 1 0 01-1.414-1.414l.707-.707a1 1 0 011.414 1.414zM4 11a1 1 0 100-2H3a1 1 0 000 2h1z" clip-rule="evenodd"></path>
                    </svg>
                    <!-- Moon Icon (shown in light mode) -->
                    <svg class="theme-icon-moon w-5 h-5 text-slate-700" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M17.293 13.293A8 8 0 016.707 2.707a8.001 8.001 0 1010.586 10.586z"></path>
                    </svg>
                </button>

                <?php if ($currentUser): ?>
                    <!-- User Profile Dropdown with Unbreakable Hover Bridge -->
                    <div class="relative group">
                        <button type="button" class="flex items-center gap-2 p-1.5 rounded-xl border border-slate-200 dark:border-slate-700/60 bg-white dark:bg-slate-800 text-left focus:outline-none group-hover:border-blue-500/50 group-hover:ring-2 group-hover:ring-blue-500/20 transition-all duration-200">
                            <div class="w-8 h-8 rounded-lg bg-gradient-to-tr from-blue-600 to-cyan-500 text-white font-bold text-sm flex items-center justify-center shadow-sm">
                                <?= strtoupper(substr($currentUser['name'], 0, 1)) ?>
                            </div>
                            <div class="hidden sm:block pr-1">
                                <p class="text-xs font-bold text-slate-900 dark:text-white leading-none"><?= sanitize(explode(' ', $currentUser['name'])[0]) ?></p>
                                <p class="text-[10px] text-blue-600 dark:text-cyan-400 capitalize"><?= $currentUser['role'] ?></p>
                            </div>
                            <i data-lucide="chevron-down" class="w-4 h-4 text-slate-400 group-hover:rotate-180 transition-transform duration-200"></i>
                        </button>
                        
                        <!-- Hover Container with padding bridge (zero gap bug!) -->
                        <div class="dropdown-hover-bridge right-0 w-60 opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-200 transform translate-y-1 group-hover:translate-y-0 pointer-events-none group-hover:pointer-events-auto">
                            <div class="rounded-2xl glass-panel shadow-2xl py-2 border border-slate-200 dark:border-slate-800 backdrop-blur-xl">
                                <div class="px-4 py-3 border-b border-slate-100 dark:border-slate-800">
                                    <p class="text-xs font-semibold text-slate-500 dark:text-slate-400">Signed in as</p>
                                    <p class="text-sm font-bold text-slate-900 dark:text-white truncate"><?= sanitize($currentUser['email']) ?></p>
                                </div>
                                
                                <?php if ($currentUser['role'] === 'student'): ?>
                                    <a href="<?= $appRoot ?>student/index.php" class="flex items-center gap-2.5 px-4 py-2.5 text-xs font-bold text-slate-700 dark:text-slate-200 hover:bg-blue-50 dark:hover:bg-slate-800/80 hover:text-blue-600 transition">
                                        <i data-lucide="layout-dashboard" class="w-4 h-4 text-blue-500"></i> Student Dashboard
                                    </a>
                                    <a href="<?= $appRoot ?>student/my-courses.php" class="flex items-center gap-2.5 px-4 py-2.5 text-xs font-bold text-slate-700 dark:text-slate-200 hover:bg-blue-50 dark:hover:bg-slate-800/80 hover:text-blue-600 transition">
                                        <i data-lucide="book-open" class="w-4 h-4 text-emerald-500"></i> আমার কোর্সসমূহ
                                    </a>
                                    <a href="<?= $appRoot ?>student/payments.php" class="flex items-center gap-2.5 px-4 py-2.5 text-xs font-bold text-slate-700 dark:text-slate-200 hover:bg-blue-50 dark:hover:bg-slate-800/80 hover:text-blue-600 transition">
                                        <i data-lucide="credit-card" class="w-4 h-4 text-purple-500"></i> পেমেন্ট হিস্ট্রি
                                    </a>
                                <?php elseif ($currentUser['role'] === 'teacher'): ?>
                                    <a href="<?= $appRoot ?>teacher/index.php" class="flex items-center gap-2.5 px-4 py-2.5 text-xs font-bold text-slate-700 dark:text-slate-200 hover:bg-emerald-50 dark:hover:bg-slate-800/80 hover:text-emerald-600 transition">
                                        <i data-lucide="layout-dashboard" class="w-4 h-4 text-emerald-500"></i> Teacher Dashboard
                                    </a>
                                    <a href="<?= $appRoot ?>teacher/courses.php" class="flex items-center gap-2.5 px-4 py-2.5 text-xs font-bold text-slate-700 dark:text-slate-200 hover:bg-emerald-50 dark:hover:bg-slate-800/80 hover:text-emerald-600 transition">
                                        <i data-lucide="book-marked" class="w-4 h-4 text-blue-500"></i> Manage Lessons
                                    </a>
                                    <a href="<?= $appRoot ?>teacher/assignments.php" class="flex items-center gap-2.5 px-4 py-2.5 text-xs font-bold text-slate-700 dark:text-slate-200 hover:bg-emerald-50 dark:hover:bg-slate-800/80 hover:text-emerald-600 transition">
                                        <i data-lucide="clipboard-check" class="w-4 h-4 text-amber-500"></i> Submissions
                                    </a>
                                <?php elseif ($currentUser['role'] === 'admin'): ?>
                                    <a href="<?= $appRoot ?>admin/index.php" class="flex items-center gap-2.5 px-4 py-2.5 text-xs font-bold text-slate-700 dark:text-slate-200 hover:bg-purple-50 dark:hover:bg-slate-800/80 hover:text-purple-600 transition">
                                        <i data-lucide="shield" class="w-4 h-4 text-purple-500"></i> Admin Panel
                                    </a>
                                    <a href="<?= $appRoot ?>admin/courses.php" class="flex items-center gap-2.5 px-4 py-2.5 text-xs font-bold text-slate-700 dark:text-slate-200 hover:bg-purple-50 dark:hover:bg-slate-800/80 hover:text-purple-600 transition">
                                        <i data-lucide="book-plus" class="w-4 h-4 text-blue-500"></i> Manage Courses
                                    </a>
                                    <a href="<?= $appRoot ?>admin/enrollments.php" class="flex items-center gap-2.5 px-4 py-2.5 text-xs font-bold text-slate-700 dark:text-slate-200 hover:bg-purple-50 dark:hover:bg-slate-800/80 hover:text-purple-600 transition">
                                        <i data-lucide="badge-dollar-sign" class="w-4 h-4 text-emerald-500"></i> Approve Payments
                                    </a>
                                <?php endif; ?>

                                <div class="border-t border-slate-100 dark:border-slate-800 my-1"></div>
                                <a href="<?= $appRoot ?>logout.php" class="flex items-center gap-2.5 px-4 py-2 text-xs font-bold text-rose-600 hover:bg-rose-50 dark:hover:bg-rose-950/30 transition">
                                    <i data-lucide="log-out" class="w-4 h-4"></i> লগআউট (Logout)
                                </a>
                            </div>
                        </div>
                    </div>
                <?php else: ?>
                    <a href="<?= $appRoot ?>login.php" class="hidden sm:inline-flex items-center px-4 py-2 text-sm font-bold rounded-xl text-slate-700 dark:text-slate-200 hover:text-blue-600 dark:hover:text-cyan-400 hover:bg-blue-50/80 dark:hover:bg-slate-800 transition-all duration-200">
                        লগইন
                    </a>
                    <a href="<?= $appRoot ?>register.php" class="inline-flex items-center px-5 py-2.5 text-sm font-bold rounded-xl text-white bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 shadow-md shadow-blue-500/25 hover:shadow-lg hover:shadow-blue-500/35 transition-all duration-200 transform hover:-translate-y-0.5">
                        রেজিস্ট্রেশন
                    </a>
                <?php endif; ?>

                <!-- Mobile Menu Button -->
                <button type="button" id="mobile-menu-btn" class="md:hidden p-2.5 rounded-xl text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800 focus:outline-none transition-colors" aria-label="Open Mobile Menu">
                    <i data-lucide="menu" class="w-6 h-6"></i>
                </button>
            </div>
        </div>
    </div>

    <!-- Mobile Drawer Navigation -->
    <div id="mobile-menu" class="hidden md:hidden border-t border-slate-200 dark:border-slate-800 bg-white/95 dark:bg-slate-900/95 backdrop-blur-md px-4 pt-3 pb-6 space-y-2">
        <a href="<?= $appRoot ?>index.php" class="block px-3 py-2 rounded-xl text-base font-bold text-slate-800 dark:text-white hover:bg-blue-50 dark:hover:bg-slate-800 hover:text-blue-600 transition">
            হোমপেজ (Home)
        </a>
        <a href="<?= $appRoot ?>courses.php" class="block px-3 py-2 rounded-xl text-base font-bold text-slate-800 dark:text-white hover:bg-blue-50 dark:hover:bg-slate-800 hover:text-blue-600 transition">
            সকল কোর্সসমূহ (১০টি কোর্স)
        </a>
        <a href="<?= $appRoot ?>index.php#why-utc" class="block px-3 py-2 rounded-xl text-base font-bold text-slate-800 dark:text-white hover:bg-blue-50 dark:hover:bg-slate-800 hover:text-blue-600 transition">
            কেন UTC বেছে নেবেন?
        </a>
        <a href="<?= $appRoot ?>about.php" class="block px-3 py-2 rounded-xl text-base font-bold text-slate-800 dark:text-white hover:bg-blue-50 dark:hover:bg-slate-800 hover:text-blue-600 transition">
            আমাদের সম্পর্কে
        </a>
        <a href="<?= $appRoot ?>contact.php" class="block px-3 py-2 rounded-xl text-base font-bold text-slate-800 dark:text-white hover:bg-blue-50 dark:hover:bg-slate-800 hover:text-blue-600 transition">
            যোগাযোগ ও ঠিকানা
        </a>

        <div class="border-t border-slate-200 dark:border-slate-800 pt-3">
            <?php if ($currentUser): ?>
                <div class="px-3 py-2 bg-slate-100 dark:bg-slate-800 rounded-xl mb-2">
                    <p class="text-sm font-bold text-slate-900 dark:text-white"><?= sanitize($currentUser['name']) ?></p>
                    <p class="text-xs text-blue-600 dark:text-cyan-400 capitalize"><?= $currentUser['role'] ?> Portal</p>
                </div>
                <?php if ($currentUser['role'] === 'admin'): ?>
                    <a href="<?= $appRoot ?>admin/index.php" class="block px-3 py-2 text-purple-600 font-bold hover:bg-purple-50 dark:hover:bg-slate-800 rounded-xl">Admin Panel</a>
                <?php elseif ($currentUser['role'] === 'teacher'): ?>
                    <a href="<?= $appRoot ?>teacher/index.php" class="block px-3 py-2 text-emerald-600 font-bold hover:bg-emerald-50 dark:hover:bg-slate-800 rounded-xl">Teacher Portal</a>
                <?php else: ?>
                    <a href="<?= $appRoot ?>student/index.php" class="block px-3 py-2 text-blue-600 font-bold hover:bg-blue-50 dark:hover:bg-slate-800 rounded-xl">Student Portal</a>
                <?php endif; ?>
                <a href="<?= $appRoot ?>logout.php" class="block px-3 py-2 text-rose-600 font-semibold hover:bg-rose-50 dark:hover:bg-slate-800 rounded-xl">লগআউট</a>
            <?php else: ?>
                <div class="grid grid-cols-2 gap-3 pt-2">
                    <a href="<?= $appRoot ?>login.php" class="text-center py-2.5 rounded-xl border border-slate-300 dark:border-slate-700 font-bold text-slate-800 dark:text-white hover:border-blue-500">লগইন</a>
                    <a href="<?= $appRoot ?>register.php" class="text-center py-2.5 rounded-xl bg-blue-600 text-white font-bold hover:bg-blue-500 shadow-md">রেজিস্ট্রেশন</a>
                </div>
            <?php endif; ?>
        </div>
    </div>
</header>
