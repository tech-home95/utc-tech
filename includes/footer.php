<?php
/**
 * Universal Technology Corporation (UTC) - Footer
 */
$appRoot = getAppRoot();
?>

<!-- Footer -->
<footer class="bg-slate-900 text-slate-300 border-t border-slate-800 relative z-20 mt-auto">
    <!-- Top Highlights Banner -->
    <div class="bg-gradient-to-r from-blue-900 via-indigo-950 to-slate-950 border-b border-slate-800/80 py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex flex-col md:flex-row items-center justify-between gap-6">
                <div>
                    <h3 class="text-xl sm:text-2xl font-extrabold text-white tracking-tight">
                        ✨ Learn Today | Skill Up Tomorrow | Build Your Career with UTC ✨
                    </h3>
                    <p class="text-slate-400 text-sm mt-1">
                        সঠিক গাইডলাইন + প্র্যাকটিক্যাল শিক্ষা = সফল আইটি প্রফেশনাল ও প্রোগ্রামার হওয়ার পথ
                    </p>
                </div>
                <div class="flex items-center gap-3">
                    <a href="<?= $appRoot ?>courses.php" class="px-6 py-3 rounded-xl bg-blue-600 hover:bg-blue-500 text-white font-bold text-sm shadow-lg shadow-blue-600/30 transition transform hover:-translate-y-0.5">
                        কোর্সসমূহে ভর্তি হন
                    </a>
                    <a href="tel:01812669757" class="px-5 py-3 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-200 border border-slate-700 font-bold text-sm transition flex items-center gap-2">
                        <i data-lucide="phone" class="w-4 h-4 text-emerald-400"></i> কল করুন
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Footer Body -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-10">
            
            <!-- Col 1: Institute Info -->
            <div class="space-y-4">
                <div class="flex items-center gap-3">
                    <img src="<?= $appRoot ?>assets/images/utc-logo.png" alt="UTC Logo" class="w-12 h-12 rounded-full object-contain bg-white p-0.5 shadow-lg border border-slate-700 shrink-0">
                    <span class="font-extrabold text-lg text-white tracking-tight">
                        Universal Technology Corporation
                    </span>
                </div>
                <p class="text-slate-400 text-sm leading-relaxed">
                    ইউআই ল্যাব ও লিড একাডেমির আদলে ফেনীর আধুনিকতম আইটি ইনস্টিটিউট। আন্তর্জাতিক মানের প্র্যাকটিক্যাল ট্রেনিং, লাইভ প্রজেক্ট ও আজীবন ক্যারিয়ার সাপোর্ট।
                </p>
                <div class="pt-2 flex items-center space-x-3 text-slate-400">
                    <a href="#" class="w-9 h-9 rounded-lg bg-slate-800 hover:bg-blue-600 hover:text-white transition flex items-center justify-center"><i data-lucide="facebook" class="w-4 h-4"></i></a>
                    <a href="#" class="w-9 h-9 rounded-lg bg-slate-800 hover:bg-rose-600 hover:text-white transition flex items-center justify-center"><i data-lucide="youtube" class="w-4 h-4"></i></a>
                    <a href="#" class="w-9 h-9 rounded-lg bg-slate-800 hover:bg-blue-700 hover:text-white transition flex items-center justify-center"><i data-lucide="linkedin" class="w-4 h-4"></i></a>
                    <a href="#" class="w-9 h-9 rounded-lg bg-slate-800 hover:bg-cyan-600 hover:text-white transition flex items-center justify-center"><i data-lucide="globe" class="w-4 h-4"></i></a>
                </div>
            </div>

            <!-- Col 2: Top Courses -->
            <div>
                <h4 class="text-white font-bold text-base mb-4 flex items-center gap-2">
                    <span class="w-2 h-2 rounded-full bg-blue-500"></span> জনপ্রিয় কোর্সসমূহ
                </h4>
                <ul class="space-y-2.5 text-sm text-slate-400">
                    <li><a href="<?= $appRoot ?>course-details.php?id=1" class="hover:text-cyan-400 transition flex items-center gap-1.5"><i data-lucide="chevron-right" class="w-3.5 h-3.5 text-blue-500"></i> Programming Combo Pack (50% Off)</a></li>
                    <li><a href="<?= $appRoot ?>course-details.php?id=2" class="hover:text-cyan-400 transition flex items-center gap-1.5"><i data-lucide="chevron-right" class="w-3.5 h-3.5 text-blue-500"></i> Advance Web Design (HTML, Tailwind, JS)</a></li>
                    <li><a href="<?= $appRoot ?>course-details.php?id=4" class="hover:text-cyan-400 transition flex items-center gap-1.5"><i data-lucide="chevron-right" class="w-3.5 h-3.5 text-blue-500"></i> Laravel Full-Stack Web Development</a></li>
                    <li><a href="<?= $appRoot ?>course-details.php?id=5" class="hover:text-cyan-400 transition flex items-center gap-1.5"><i data-lucide="chevron-right" class="w-3.5 h-3.5 text-blue-500"></i> MERN Stack Web Development</a></li>
                    <li><a href="<?= $appRoot ?>course-details.php?id=8" class="hover:text-cyan-400 transition flex items-center gap-1.5"><i data-lucide="chevron-right" class="w-3.5 h-3.5 text-blue-500"></i> Flutter & Dart Mobile App (Android/iOS)</a></li>
                    <li><a href="<?= $appRoot ?>course-details.php?id=10" class="hover:text-cyan-400 transition flex items-center gap-1.5"><i data-lucide="chevron-right" class="w-3.5 h-3.5 text-blue-500"></i> Certified Ethical Hacking (CEHv11)</a></li>
                </ul>
            </div>

            <!-- Col 3: Quick Links & Portals -->
            <div>
                <h4 class="text-white font-bold text-base mb-4 flex items-center gap-2">
                    <span class="w-2 h-2 rounded-full bg-cyan-500"></span> পোর্টাল ও সেবা
                </h4>
                <ul class="space-y-2.5 text-sm text-slate-400">
                    <li><a href="<?= $appRoot ?>student/index.php" class="hover:text-white transition flex items-center gap-1.5"><i data-lucide="layout" class="w-3.5 h-3.5 text-cyan-400"></i> Student Portal (লার্নিং ড্যাশবোর্ড)</a></li>
                    <li><a href="<?= $appRoot ?>teacher/index.php" class="hover:text-white transition flex items-center gap-1.5"><i data-lucide="presentation" class="w-4 h-4 text-emerald-400"></i> Teacher Portal (ক্লাস ও মূল্যায়ন)</a></li>
                    <li><a href="<?= $appRoot ?>admin/index.php" class="hover:text-white transition flex items-center gap-1.5"><i data-lucide="shield-alert" class="w-4 h-4 text-purple-400"></i> Admin Panel (অ্যাকাডেমিক কন্ট্রোল)</a></li>
                    <li><a href="<?= $appRoot ?>login.php" class="hover:text-white transition flex items-center gap-1.5"><i data-lucide="log-in" class="w-3.5 h-3.5 text-amber-400"></i> পোর্টাল লগইন</a></li>
                    <li><a href="<?= $appRoot ?>register.php" class="hover:text-white transition flex items-center gap-1.5"><i data-lucide="user-plus" class="w-3.5 h-3.5 text-blue-400"></i> নতুন রেজিস্ট্রেশন</a></li>
                </ul>
            </div>

            <!-- Col 4: Contact & Campus Address -->
            <div class="space-y-3">
                <h4 class="text-white font-bold text-base mb-4 flex items-center gap-2">
                    <span class="w-2 h-2 rounded-full bg-amber-500"></span> যোগাযোগ ও ক্যাম্পাস
                </h4>
                
                <div class="space-y-3 text-sm text-slate-400">
                    <div class="flex items-start gap-3">
                        <i data-lucide="map-pin" class="w-5 h-5 text-amber-400 shrink-0 mt-0.5"></i>
                        <div>
                            <strong class="text-white block">UTC – Feni</strong>
                            <span>287 (1st Floor), Doctor Para, Feni-3900</span>
                        </div>
                    </div>

                    <div class="flex items-center gap-3">
                        <i data-lucide="phone" class="w-5 h-5 text-emerald-400 shrink-0"></i>
                        <div>
                            <a href="tel:01812669757" class="hover:text-emerald-400 block font-semibold text-white">01812669757</a>
                            <a href="tel:01817122483" class="hover:text-emerald-400 block font-semibold text-white">01817122483</a>
                        </div>
                    </div>

                    <div class="flex items-center gap-3">
                        <i data-lucide="mail" class="w-5 h-5 text-cyan-400 shrink-0"></i>
                        <a href="mailto:utcsoftltd@gmail.com" class="hover:text-cyan-400 break-all">utcsoftltd@gmail.com</a>
                    </div>
                </div>

                <!-- Payment Methods Badges -->
                <div class="pt-4 border-t border-slate-800">
                    <p class="text-xs font-semibold text-slate-400 uppercase tracking-wider mb-2">সমর্থিত পেমেন্ট মেথড:</p>
                    <div class="flex flex-wrap gap-2 text-xs font-bold">
                        <span class="px-2.5 py-1 rounded bg-[#E2136E] text-white">bKash</span>
                        <span class="px-2.5 py-1 rounded bg-[#F7941D] text-white">Nagad</span>
                        <span class="px-2.5 py-1 rounded bg-[#8C3494] text-white">Rocket</span>
                        <span class="px-2.5 py-1 rounded bg-blue-800 text-white">Bank</span>
                    </div>
                </div>
            </div>

        </div>

        <!-- Bottom Copyright -->
        <div class="mt-12 pt-8 border-t border-slate-800 flex flex-col sm:flex-row items-center justify-between text-xs text-slate-500 gap-4">
            <p>© <?= date('Y') ?> Universal Technology Corporation (UTC). All Rights Reserved. Doctor Para, Feni.</p>
            <div class="flex items-center space-x-6">
                <a href="<?= $appRoot ?>about.php" class="hover:text-slate-300">About UTC</a>
                <a href="<?= $appRoot ?>contact.php" class="hover:text-slate-300">Terms of Service</a>
                <a href="<?= $appRoot ?>contact.php" class="hover:text-slate-300">Privacy Policy</a>
            </div>
        </div>
    </div>
</footer>

<!-- Initialize Lucide Icons -->
<script>
    lucide.createIcons();
</script>

<!-- Main JS -->
<script src="<?= $appRoot ?>assets/js/main.js"></script>

<!-- Three.js 3D Interactive Hero Canvas Script (If element present) -->
<script src="<?= $appRoot ?>assets/js/three-bg.js"></script>
</body>
</html>
