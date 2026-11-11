<?php
/**
 * Universal Technology Corporation (UTC) - Contact Us
 */

$pageTitle = 'যোগাযোগ ও ক্যাম্পাস ঠিকানা | Universal Technology Corporation (UTC)';
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/config/auth.php';
require_once __DIR__ . '/config/helpers.php';

$sent = false;
if (($_SERVER['REQUEST_METHOD'] ?? '') === 'POST') {
    $sent = true;
    setFlash('success', 'আপনার বার্তাটি সফলভাবে পাঠানো হয়েছে! খুব শীঘ্রই আমাদের প্রতিনিধি আপনার সাথে যোগাযোগ করবেন।');
}

require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/navbar.php';
?>

<div class="py-14 bg-gradient-to-b from-blue-900 via-indigo-950 to-slate-950 text-white relative">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center space-y-3">
        <span class="px-3.5 py-1 rounded-full text-xs font-extrabold bg-blue-500/20 text-cyan-300 border border-blue-500/30 uppercase tracking-wider">
            Contact & Campus Location
        </span>
        <h1 class="text-3xl sm:text-4xl font-extrabold">যোগাযোগ করুন</h1>
        <p class="text-slate-300 text-sm max-w-xl mx-auto">
            ✨ Learn Today | Skill Up Tomorrow | Build Your Career with UTC ✨
        </p>
    </div>
</div>

<div class="py-16 bg-slate-50 dark:bg-[#080d1a] transition-colors duration-300">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <?= renderFlash() ?>

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-12 items-start">
            
            <!-- Contact Details Card -->
            <div class="lg:col-span-5 space-y-6">
                <div class="p-8 rounded-3xl glass-panel border border-slate-200 dark:border-slate-800 shadow-xl space-y-6">
                    <div>
                        <span class="text-xs font-bold text-blue-600 dark:text-cyan-400 uppercase tracking-wider">
                            প্রধান কার্যালয় ও একাডেমি
                        </span>
                        <h2 class="text-2xl font-extrabold text-slate-900 dark:text-white mt-1">
                            UTC – Feni
                        </h2>
                    </div>

                    <div class="space-y-4 text-xs sm:text-sm text-slate-600 dark:text-slate-300">
                        <div class="flex items-start gap-3">
                            <div class="w-10 h-10 rounded-xl bg-amber-500/10 text-amber-600 dark:text-amber-400 flex items-center justify-center shrink-0">
                                <i data-lucide="map-pin" class="w-5 h-5"></i>
                            </div>
                            <div>
                                <strong class="text-slate-900 dark:text-white block font-bold">ঠিকানা:</strong>
                                <span>287 (1st Floor), Doctor Para, Feni-3900</span>
                            </div>
                        </div>

                        <div class="flex items-start gap-3">
                            <div class="w-10 h-10 rounded-xl bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 flex items-center justify-center shrink-0">
                                <i data-lucide="phone" class="w-5 h-5"></i>
                            </div>
                            <div>
                                <strong class="text-slate-900 dark:text-white block font-bold">মোবাইল নম্বর:</strong>
                                <a href="tel:01812669757" class="hover:text-emerald-500 block font-bold text-blue-600 dark:text-cyan-400">01812669757</a>
                                <a href="tel:01817122483" class="hover:text-emerald-500 block font-bold text-blue-600 dark:text-cyan-400">01817122483</a>
                            </div>
                        </div>

                        <div class="flex items-start gap-3">
                            <div class="w-10 h-10 rounded-xl bg-cyan-500/10 text-cyan-600 dark:text-cyan-400 flex items-center justify-center shrink-0">
                                <i data-lucide="mail" class="w-5 h-5"></i>
                            </div>
                            <div>
                                <strong class="text-slate-900 dark:text-white block font-bold">অফিসিয়াল ইমেইল:</strong>
                                <a href="mailto:utcsoftltd@gmail.com" class="hover:underline font-bold text-blue-600 dark:text-cyan-400">utcsoftltd@gmail.com</a>
                            </div>
                        </div>

                        <div class="flex items-start gap-3">
                            <div class="w-10 h-10 rounded-xl bg-blue-500/10 text-blue-600 dark:text-cyan-400 flex items-center justify-center shrink-0">
                                <i data-lucide="clock" class="w-5 h-5"></i>
                            </div>
                            <div>
                                <strong class="text-slate-900 dark:text-white block font-bold">অফিস সময়সূচী:</strong>
                                <span>শনিবার - বৃহস্পতিবার (সকাল ৯:০০ - রাত ৮:০০)</span>
                            </div>
                        </div>
                    </div>

                    <!-- Campus Map Shortcut -->
                    <div class="pt-4 border-t border-slate-100 dark:border-slate-800">
                        <a href="https://maps.google.com/?q=Doctor+Para+Feni" target="_blank" class="w-full py-3 rounded-xl bg-slate-800 hover:bg-slate-700 text-white text-xs font-bold flex items-center justify-center gap-2 transition">
                            <i data-lucide="navigation" class="w-4 h-4 text-cyan-400"></i> Google Maps-এ অবস্থান দেখুন
                        </a>
                    </div>
                </div>
            </div>

            <!-- Inquiry Form -->
            <div class="lg:col-span-7">
                <div class="p-8 sm:p-10 rounded-3xl glass-panel border border-slate-200 dark:border-slate-800 shadow-xl space-y-6">
                    <div>
                        <h2 class="text-xl font-extrabold text-slate-900 dark:text-white">
                            সরাসরি বার্তা বা কাউন্সেলিং রিকোয়েস্ট পাঠান
                        </h2>
                        <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">
                            আপনার আগ্রহের কোর্স সম্পর্কে বিস্তারিত জানতে নিচের ফর্মটি পূরণ করুন।
                        </p>
                    </div>

                    <form method="POST" action="contact.php" class="space-y-4 text-xs">
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">আপনার নাম *</label>
                                <input type="text" name="name" required placeholder="পুরো নাম" class="w-full px-4 py-3 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm">
                            </div>
                            <div>
                                <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">মোবাইল নম্বর *</label>
                                <input type="tel" name="phone" required placeholder="018XXXXXXXX" class="w-full px-4 py-3 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm">
                            </div>
                        </div>

                        <div>
                            <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">ইমেইল এড্রেস</label>
                            <input type="email" name="email" placeholder="name@example.com" class="w-full px-4 py-3 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm">
                        </div>

                        <div>
                            <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">আগ্রহী কোর্স</label>
                            <select name="interested_course" class="w-full px-4 py-3 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm">
                                <option value="Programming Combo Pack">Programming Combo Pack (৫০% ডিসকাউন্ট)</option>
                                <option value="Advance Web Design">Advance Web Design (HTML, CSS, Sass, Tailwind, JS)</option>
                                <option value="Web Development Full-Stack">Web Development (PHP, JS, MySQL, SQLite, MongoDB)</option>
                                <option value="Laravel Full-Stack">Laravel Web Development & Freelancing</option>
                                <option value="MERN Stack">MERN Stack Web Development</option>
                                <option value="Python Django">Python Django Web Development with API</option>
                                <option value="CompTIA Networking">CompTIA A+ Hardware & Networking (CCNA & MTCNA)</option>
                                <option value="Flutter App">Flutter & Dart Mobile App (Android & iOS)</option>
                                <option value="Python BigData">Python Programming with BigData for Data Analysis</option>
                                <option value="Ethical Hacking">Certified Ethical Hacking (CEHv11)</option>
                            </select>
                        </div>

                        <div>
                            <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">আপনার প্রশ্ন বা বার্তা</label>
                            <textarea name="message" rows="4" placeholder="আপনার কোনো নির্দিষ্ট প্রশ্ন থাকলে লিখুন..." class="w-full px-4 py-3 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm"></textarea>
                        </div>

                        <button type="submit" class="w-full py-3.5 rounded-xl bg-blue-600 hover:bg-blue-500 text-white font-extrabold text-sm shadow-xl shadow-blue-500/25 transition">
                            বার্তা পাঠান
                        </button>
                    </form>
                </div>
            </div>

        </div>

    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
