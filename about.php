<?php
/**
 * Universal Technology Corporation (UTC) - About Us
 */

$pageTitle = 'আমাদের সম্পর্কে | Universal Technology Corporation (UTC)';
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/config/auth.php';
require_once __DIR__ . '/config/helpers.php';
require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/navbar.php';
?>

<div class="py-16 bg-gradient-to-b from-blue-900 via-indigo-950 to-slate-950 text-white relative overflow-hidden">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10 text-center space-y-4">
        <span class="px-3.5 py-1 rounded-full text-xs font-extrabold bg-blue-500/20 text-cyan-300 border border-blue-500/30 uppercase tracking-wider">
            About Universal Technology Corporation
        </span>
        <h1 class="text-3xl sm:text-5xl font-extrabold tracking-tight">
            দক্ষতা ও প্রযুক্তিতে এগিয়ে চলার বিশ্বস্ত প্রতিষ্ঠান
        </h1>
        <p class="text-slate-300 text-sm sm:text-base max-w-3xl mx-auto leading-relaxed">
            ✨ Learn Today | Skill Up Tomorrow | Build Your Career with UTC ✨
        </p>
    </div>
</div>

<div class="py-16 bg-slate-50 dark:bg-[#080d1a] transition-colors duration-300">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-16">
        
        <!-- Story & Mission -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
            <div class="space-y-4">
                <span class="text-xs font-bold text-blue-600 dark:text-cyan-400 uppercase tracking-wider">আমাদের মিশন ও রূপকল্প</span>
                <h2 class="text-2xl sm:text-3xl font-extrabold text-slate-900 dark:text-white">
                    আন্তর্জাতিক মানের আইটি শিক্ষার প্রতিশ্রুতি
                </h2>
                <p class="text-slate-600 dark:text-slate-300 text-sm leading-relaxed">
                    Universal Technology Corporation (UTC) দেশের শীর্ষস্থানীয় আইটি প্রতিষ্ঠান যেমন UY Lab ও Lead Academy-এর আদলে গঠিত একটি অত্যাধুনিক প্রশিক্ষণ কেন্দ্র। ফেনী শহরের কেন্দ্রস্থল ডক্টর পাড়ায় আমাদের সুসজ্জিত ল্যাব ও প্রফেশনাল ইন্সট্রাক্টরদের মাধ্যমে শিক্ষার্থীদের বিশ্বমানের প্র্যাকটিক্যাল কোডিং ও ফ্রিল্যান্সিং স্কিল প্রদান করা হয়।
                </p>
                <p class="text-slate-600 dark:text-slate-300 text-sm leading-relaxed">
                    আমরা বিশ্বাস করি শুধু থিওরিটিক্যাল জ্ঞান নয়, বাস্তব প্রজেক্ট ও সমস্যার সমাধানই একজন শিক্ষার্থীকে সফল সফটওয়্যার ইঞ্জিনিয়ার, ওয়েব ডেভেলপার কিংবা সাইবার সিকিউরিটি প্রফেশনাল হিসেবে গড়ে তোলে।
                </p>
            </div>

            <div class="p-8 rounded-3xl glass-panel border border-slate-200 dark:border-slate-800 space-y-4 shadow-xl">
                <h3 class="text-lg font-bold text-slate-900 dark:text-white flex items-center gap-2">
                    <i data-lucide="award" class="w-5 h-5 text-amber-500"></i> UTC এর মূল প্রতিশ্রুতিসমূহ
                </h3>
                <ul class="space-y-3 text-xs sm:text-sm text-slate-600 dark:text-slate-300">
                    <li class="flex items-center gap-2">✔️ প্রতিটি ক্লাসের এইচডি ভিডিও রেকর্ড প্রদান</li>
                    <li class="flex items-center gap-2">✔️ Industrial Training ও সরাসরি ক্লায়েন্ট প্রজেক্টের অভিজ্ঞতা</li>
                    <li class="flex items-center gap-2">✔️ ২৪ ঘণ্টা অনলাইন ও অফলাইন ক্যাম্পাস সাপোর্ট</li>
                    <li class="flex items-center gap-2">✔️ Lifetime Free Repeat Facility (আজীবন রিভিশনের সুযোগ)</li>
                    <li class="flex items-center gap-2">✔️ কোর্স শেষে ভেরিফায়েড প্রফেশনাল সার্টিফিকেট প্রদান</li>
                </ul>
            </div>
        </div>

        <!-- Campus Facilities -->
        <div class="p-8 sm:p-12 rounded-3xl glass-panel border border-slate-200 dark:border-slate-800 space-y-6">
            <div class="text-center max-w-2xl mx-auto space-y-2">
                <h2 class="text-2xl font-extrabold text-slate-900 dark:text-white">ফেনী ক্যাম্পাস সুবিধাসমূহ</h2>
                <p class="text-xs text-slate-500">২৮৭ ডক্টর পাড়া, ১ম তলা, ফেনী-৩৯০০</p>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-6 text-center text-xs">
                <div class="p-6 rounded-2xl bg-white dark:bg-slate-900/60 border border-slate-200 dark:border-slate-800 space-y-2">
                    <div class="w-12 h-12 rounded-xl bg-blue-500/10 text-blue-600 dark:text-cyan-400 flex items-center justify-center mx-auto">
                        <i data-lucide="wifi" class="w-6 h-6"></i>
                    </div>
                    <h4 class="font-bold text-slate-900 dark:text-white text-sm">হাই-স্পিড ফাইবার নেটওয়ার্ক</h4>
                    <p class="text-slate-500">নিরবচ্ছিন্ন ব্রডব্যান্ড ইন্টারনেট এবং আধুনিক কম্পিউটার ল্যাব।</p>
                </div>

                <div class="p-6 rounded-2xl bg-white dark:bg-slate-900/60 border border-slate-200 dark:border-slate-800 space-y-2">
                    <div class="w-12 h-12 rounded-xl bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 flex items-center justify-center mx-auto">
                        <i data-lucide="wind" class="w-6 h-6"></i>
                    </div>
                    <h4 class="font-bold text-slate-900 dark:text-white text-sm">শীতাতপ নিয়ন্ত্রিত ক্লাসরুম</h4>
                    <p class="text-slate-500">মনোরম ও শান্ত পরিবেশে প্রতিটি শিক্ষার্থীর জন্য আলাদা ওয়ার্কস্টেশন।</p>
                </div>

                <div class="p-6 rounded-2xl bg-white dark:bg-slate-900/60 border border-slate-200 dark:border-slate-800 space-y-2">
                    <div class="w-12 h-12 rounded-xl bg-purple-500/10 text-purple-600 dark:text-purple-400 flex items-center justify-center mx-auto">
                        <i data-lucide="briefcase" class="w-6 h-6"></i>
                    </div>
                    <h4 class="font-bold text-slate-900 dark:text-white text-sm">প্লেসমেন্ট ও ফ্রিল্যান্সিং সেল</h4>
                    <p class="text-slate-500">Upwork, Fiverr এবং স্থানীয় আইটি কোম্পানিতে জব রিকমেন্ডেশন সাপোর্ট।</p>
                </div>
            </div>
        </div>

    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
