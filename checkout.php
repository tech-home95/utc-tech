<?php
/**
 * Universal Technology Corporation (UTC) - Checkout & Payment Gateway
 */

require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/config/auth.php';
require_once __DIR__ . '/config/helpers.php';

$courseId = isset($_GET['course_id']) ? (int)$_GET['course_id'] : 1;
$stmt = $pdo->prepare("SELECT * FROM courses WHERE id = ?");
$stmt->execute([$courseId]);
$course = $stmt->fetch();

if (!$course) {
    header("Location: courses.php");
    exit;
}

$currentUser = getCurrentUser();
$finalAmount = $course['discount_price'];
$appliedCoupon = null;
$discountAmount = 0;

// Handle Coupon Validation
if (isset($_POST['apply_coupon'])) {
    $couponCode = strtoupper(trim($_POST['coupon_code'] ?? ''));
    $cStmt = $pdo->prepare("SELECT * FROM coupons WHERE code = ? AND is_active = 1");
    $cStmt->execute([$couponCode]);
    $coupon = $cStmt->fetch();

    if ($coupon) {
        $appliedCoupon = $coupon;
        $discountAmount = ($course['original_price'] * $coupon['discount_percent']) / 100;
        $finalAmount = max(0, $course['original_price'] - $discountAmount);
        setFlash('success', "কুপন কোড '{$couponCode}' সফলভাবে যুক্ত হয়েছে! {$coupon['discount_percent']}% ছাড় প্রয়োগ করা হয়েছে।");
    } else {
        setFlash('error', "অবৈধ বা মেয়াদোত্তীর্ণ কুপন কোড!");
    }
}

// Handle Order & Payment Submission
if (($_SERVER['REQUEST_METHOD'] ?? '') === 'POST' && isset($_POST['submit_payment'])) {
    $paymentMethod = sanitize($_POST['payment_method'] ?? 'bKash');
    $senderPhone = sanitize($_POST['sender_phone'] ?? '');
    $transactionId = sanitize($_POST['transaction_id'] ?? '');
    $batchName = 'UTC Batch 2026-A';
    $amountPaid = (float)($_POST['final_amount'] ?? $finalAmount);
    
    // Auto-login or register if not logged in
    $studentId = $currentUser ? $currentUser['id'] : null;

    if (!$studentId) {
        $name = sanitize($_POST['name'] ?? '');
        $email = sanitize($_POST['email'] ?? '');
        $phone = sanitize($_POST['phone'] ?? $senderPhone);
        $password = trim($_POST['password'] ?? '123456');

        if (empty($name) || empty($email)) {
            setFlash('error', 'দয়া করে নাম এবং ইমেইল প্রদান করুন।');
        } else {
            // Check if user exists
            $uStmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
            $uStmt->execute([$email]);
            $existing = $uStmt->fetch();

            if ($existing) {
                $studentId = $existing['id'];
                loginUser($existing);
            } else {
                $hashed = password_hash($password, PASSWORD_DEFAULT);
                $insUser = $pdo->prepare("INSERT INTO users (name, email, password, phone, role) VALUES (?, ?, ?, ?, 'student')");
                $insUser->execute([$name, $email, $hashed, $phone]);
                $studentId = $pdo->lastInsertId();

                $newUser = ['id' => $studentId, 'name' => $name, 'email' => $email, 'role' => 'student'];
                loginUser($newUser);
            }
        }
    }

    if ($studentId) {
        // If demo express sandbox payment, auto-approve
        $isInstantSandbox = ($paymentMethod === 'Instant_Sandbox');
        $paymentStatus = $isInstantSandbox ? 'approved' : 'pending';
        $approvedAt = $isInstantSandbox ? date('Y-m-d H:i:s') : null;
        $trxCode = $isInstantSandbox ? 'UTC-SBX-' . strtoupper(bin2hex(random_bytes(4))) : $transactionId;

        // Insert Enrollment
        $insEnroll = $pdo->prepare("INSERT INTO enrollments (
            student_id, course_id, batch_name, amount, payment_method, transaction_id, sender_phone, payment_status, enrolled_at, approved_at
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, CURRENT_TIMESTAMP, ?)");
        
        $insEnroll->execute([
            $studentId,
            $courseId,
            $batchName,
            $amountPaid,
            $paymentMethod,
            $trxCode,
            $senderPhone,
            $paymentStatus,
            $approvedAt
        ]);

        $enrollmentId = $pdo->lastInsertId();

        if ($isInstantSandbox) {
            setFlash('success', 'পেমেন্ট সফল হয়েছে! আপনার কোর্স রেজিস্ট্রেশন তাৎক্ষণিকভাবে সম্পন্ন হয়েছে।');
        } else {
            setFlash('info', 'আপনার পেমেন্ট রিকোয়েস্ট জমা হয়েছে! অ্যাডমিন ট্রানজেকশন আইডি যাচাইয়ের পর কোর্স অ্যাক্সেস এক্টিভ হবে।');
        }

        header("Location: invoice.php?id=" . $enrollmentId);
        exit;
    }
}

$pageTitle = 'কোর্স ফি পেমেন্ট ও ভর্তি | UTC';
require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/navbar.php';
?>

<div class="py-12 bg-slate-50 dark:bg-[#080d1a] transition-colors duration-300">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <div class="text-center max-w-2xl mx-auto mb-10 space-y-2">
            <span class="px-3 py-1 rounded-full text-xs font-extrabold bg-blue-500/10 text-blue-600 dark:text-cyan-400 border border-blue-500/20 uppercase tracking-wider">
                Secure Checkout & Enrollment
            </span>
            <h1 class="text-3xl font-extrabold text-slate-900 dark:text-white">
                অনলাইন ভর্তি ও পেমেন্ট
            </h1>
            <p class="text-slate-600 dark:text-slate-400 text-sm">
                bKash, Nagad, Rocket কিংবা ব্যাংক ট্রান্সফারের মাধ্যমে ঘরে বসেই ভর্তি কনফার্ম করুন।
            </p>
        </div>

        <?= renderFlash() ?>

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
            
            <!-- Left 7 Cols: Student Info & Payment Methods -->
            <div class="lg:col-span-7 space-y-6">
                
                <form method="POST" action="checkout.php?course_id=<?= $course['id'] ?>" class="space-y-6">
                    <input type="hidden" name="final_amount" value="<?= $finalAmount ?>">
                    
                    <!-- Student Identity -->
                    <div class="p-6 rounded-3xl glass-panel border border-slate-200 dark:border-slate-800 shadow-md space-y-4">
                        <div class="flex items-center gap-3 pb-3 border-b border-slate-200 dark:border-slate-800">
                            <div class="w-8 h-8 rounded-lg bg-blue-600 text-white flex items-center justify-center font-bold text-sm">
                                1
                            </div>
                            <h2 class="text-base font-bold text-slate-900 dark:text-white">
                                শিক্ষার্থীর তথ্য (Student Details)
                            </h2>
                        </div>

                        <?php if ($currentUser): ?>
                            <div class="p-4 rounded-xl bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800/60 flex items-center justify-between">
                                <div>
                                    <p class="text-sm font-bold text-slate-900 dark:text-white"><?= sanitize($currentUser['name']) ?></p>
                                    <p class="text-xs text-slate-500 dark:text-slate-400"><?= sanitize($currentUser['email']) ?> • <?= sanitize($currentUser['phone'] ?? 'Phone not set') ?></p>
                                </div>
                                <span class="px-3 py-1 rounded-full text-xs font-bold bg-blue-600 text-white">লগইনকৃত</span>
                            </div>
                        <?php else: ?>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs">
                                <div>
                                    <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">পূর্ণ নাম *</label>
                                    <input type="text" name="name" required placeholder="আপনার পুরো নাম লিখুন" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500">
                                </div>
                                <div>
                                    <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">ইমেইল এড্রেস *</label>
                                    <input type="email" name="email" required placeholder="name@example.com" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500">
                                </div>
                                <div>
                                    <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">মোবাইল নম্বর *</label>
                                    <input type="tel" name="phone" required placeholder="018XXXXXXXX" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500">
                                </div>
                                <div>
                                    <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">পাসওয়ার্ড (নতুন একাউন্টের জন্য) *</label>
                                    <input type="password" name="password" required value="123456" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500">
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>

                    <!-- Payment Method Selection -->
                    <div class="p-6 rounded-3xl glass-panel border border-slate-200 dark:border-slate-800 shadow-md space-y-4">
                        <div class="flex items-center gap-3 pb-3 border-b border-slate-200 dark:border-slate-800">
                            <div class="w-8 h-8 rounded-lg bg-emerald-600 text-white flex items-center justify-center font-bold text-sm">
                                2
                            </div>
                            <h2 class="text-base font-bold text-slate-900 dark:text-white">
                                পেমেন্ট মাধ্যম নির্বাচন করুন (Payment Method)
                            </h2>
                        </div>

                        <!-- Method Selector Radio Buttons -->
                        <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 text-xs">
                            <label class="cursor-pointer border-2 border-slate-200 dark:border-slate-700 rounded-2xl p-3 flex flex-col items-center justify-center text-center space-y-1.5 hover:border-pink-500 transition has-[:checked]:border-pink-600 has-[:checked]:bg-pink-500/10">
                                <input type="radio" name="payment_method" value="bKash" checked class="hidden" onchange="switchPayTab('bKash')">
                                <span class="px-2 py-0.5 rounded bg-[#E2136E] text-white font-extrabold text-[11px]">bKash</span>
                                <span class="font-bold text-slate-800 dark:text-white">বিকাশ</span>
                            </label>

                            <label class="cursor-pointer border-2 border-slate-200 dark:border-slate-700 rounded-2xl p-3 flex flex-col items-center justify-center text-center space-y-1.5 hover:border-amber-500 transition has-[:checked]:border-amber-500 has-[:checked]:bg-amber-500/10">
                                <input type="radio" name="payment_method" value="Nagad" class="hidden" onchange="switchPayTab('Nagad')">
                                <span class="px-2 py-0.5 rounded bg-[#F7941D] text-white font-extrabold text-[11px]">Nagad</span>
                                <span class="font-bold text-slate-800 dark:text-white">নগদ</span>
                            </label>

                            <label class="cursor-pointer border-2 border-slate-200 dark:border-slate-700 rounded-2xl p-3 flex flex-col items-center justify-center text-center space-y-1.5 hover:border-purple-500 transition has-[:checked]:border-purple-600 has-[:checked]:bg-purple-500/10">
                                <input type="radio" name="payment_method" value="Rocket" class="hidden" onchange="switchPayTab('Rocket')">
                                <span class="px-2 py-0.5 rounded bg-[#8C3494] text-white font-extrabold text-[11px]">Rocket</span>
                                <span class="font-bold text-slate-800 dark:text-white">রকেট</span>
                            </label>

                            <label class="cursor-pointer border-2 border-slate-200 dark:border-slate-700 rounded-2xl p-3 flex flex-col items-center justify-center text-center space-y-1.5 hover:border-emerald-500 transition has-[:checked]:border-emerald-600 has-[:checked]:bg-emerald-500/10">
                                <input type="radio" name="payment_method" value="Instant_Sandbox" class="hidden" onchange="switchPayTab('Sandbox')">
                                <span class="px-2 py-0.5 rounded bg-emerald-600 text-white font-extrabold text-[10px]">Instant Demo</span>
                                <span class="font-bold text-slate-800 dark:text-white">তাৎক্ষণিক টেস্ট</span>
                            </label>
                        </div>

                        <!-- Payment Instructions Box -->
                        <div id="pay-instruction-box" class="p-4 rounded-2xl bg-slate-100 dark:bg-slate-800/80 border border-slate-200 dark:border-slate-700 text-xs space-y-2">
                            <p class="font-bold text-slate-900 dark:text-white">
                                বিকাশ সেন্ড মানি নির্দেশনা:
                            </p>
                            <p class="text-slate-600 dark:text-slate-300 leading-relaxed">
                                ১. আপনার বিকাশ অ্যাপ থেকে "Send Money" অপশনে যান।<br>
                                ২. প্রাপক নম্বর: <strong class="text-pink-600 dark:text-pink-400 font-mono text-sm">01812669757</strong> (ক্লিক করে কপি করুন)<br>
                                ৩. টাকার পরিমাণ: <strong class="text-blue-600 dark:text-cyan-400 font-mono text-sm"><?= formatBDT($finalAmount) ?></strong> টাকা দিন।<br>
                                ৪. রেফারেন্স হিসেবে আপনার নাম দিন। ট্রানজেকশন সফল হলে TrxID ও প্রেরক নম্বর নিচে লিখুন।
                            </p>
                        </div>

                        <!-- TrxID and Sender Phone Form Inputs -->
                        <div id="trx-inputs" class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs pt-2">
                            <div>
                                <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">প্রেরকের বিকাশ/নগদ নম্বর *</label>
                                <input type="tel" name="sender_phone" placeholder="01XXXXXXXXX" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500">
                            </div>
                            <div>
                                <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">Transaction ID (TrxID) *</label>
                                <input type="text" name="transaction_id" placeholder="যেমন: TRX998877" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white font-mono uppercase focus:outline-none focus:ring-2 focus:ring-blue-500">
                            </div>
                        </div>

                    </div>

                    <!-- Submit Button -->
                    <button type="submit" name="submit_payment" class="w-full py-4 rounded-2xl bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white font-extrabold text-base shadow-xl shadow-blue-500/30 transition transform hover:-translate-y-0.5 flex items-center justify-center gap-2">
                        <i data-lucide="shield-check" class="w-5 h-5"></i>
                        <span>ভর্তি নিশ্চিত করুন ও ইনভয়েস সংগ্রহ করুন (<?= formatBDT($finalAmount) ?>)</span>
                    </button>
                </form>

            </div>

            <!-- Right 5 Cols: Order Summary & Coupon Box -->
            <div class="lg:col-span-5 space-y-6">
                
                <div class="p-6 sm:p-8 rounded-3xl glass-panel border border-slate-200 dark:border-slate-800 shadow-xl space-y-6">
                    <h3 class="text-lg font-bold text-slate-900 dark:text-white pb-3 border-b border-slate-200 dark:border-slate-800">
                        অর্ডার সামারি (Order Summary)
                    </h3>

                    <!-- Course Snapshot -->
                    <div class="space-y-2">
                        <span class="px-2.5 py-0.5 rounded-md text-[10px] font-bold bg-blue-100 dark:bg-blue-900/60 text-blue-700 dark:text-blue-300">
                            <?= sanitize($course['category']) ?>
                        </span>
                        <h4 class="text-sm font-bold text-slate-900 dark:text-white leading-snug">
                            <?= sanitize($course['title']) ?>
                        </h4>
                        <p class="text-xs text-slate-500 dark:text-slate-400">
                            ব্যাচ: UTC Batch 2026-A • <?= $course['total_classes'] ?> টি ক্লাস
                        </p>
                    </div>

                    <!-- Price Calculations -->
                    <div class="space-y-3 pt-3 border-t border-slate-100 dark:border-slate-800 text-xs">
                        <div class="flex justify-between text-slate-600 dark:text-slate-400">
                            <span>কোর্স ফি (নিয়মিত):</span>
                            <span><?= formatBDT($course['original_price']) ?></span>
                        </div>
                        <div class="flex justify-between text-emerald-600 dark:text-emerald-400 font-bold">
                            <span>স্পেশাল অফার ডিসকাউন্ট:</span>
                            <span>- <?= formatBDT($course['original_price'] - $course['discount_price']) ?></span>
                        </div>
                        <?php if ($appliedCoupon): ?>
                            <div class="flex justify-between text-amber-600 dark:text-amber-400 font-bold">
                                <span>কুপন ডিসকাউন্ট (<?= $appliedCoupon['code'] ?>):</span>
                                <span>- <?= formatBDT($discountAmount) ?></span>
                            </div>
                        <?php endif; ?>
                        
                        <div class="pt-3 border-t border-slate-200 dark:border-slate-700 flex justify-between text-base font-extrabold text-slate-900 dark:text-white">
                            <span>সর্বমোট প্রদেয় ফি:</span>
                            <span class="text-blue-600 dark:text-cyan-400"><?= formatBDT($finalAmount) ?></span>
                        </div>
                    </div>

                    <!-- Coupon Code Apply Box -->
                    <form method="POST" action="checkout.php?course_id=<?= $course['id'] ?>" class="pt-3 border-t border-slate-100 dark:border-slate-800">
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">
                            ডিসকাউন্ট কুপন কোড থাকলে লিখুন:
                        </label>
                        <div class="flex gap-2">
                            <input type="text" name="coupon_code" placeholder="যেমন: UTC50 অথবা UTC30" class="flex-1 px-3 py-2 text-xs rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white uppercase font-mono focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <button type="submit" name="apply_coupon" class="px-4 py-2 bg-slate-800 dark:bg-slate-700 hover:bg-slate-900 text-white text-xs font-bold rounded-xl transition">
                                প্রয়োগ
                            </button>
                        </div>
                        <p class="text-[11px] text-slate-400 mt-1">প্রোগ্রামিং কম্বো কোর্সে ৫০% ডিসকাউন্টের জন্য <code>UTC50</code> বা <code>UTC30</code> ট্রাই করুন।</p>
                    </form>

                    <!-- Trust Badges -->
                    <div class="pt-4 border-t border-slate-100 dark:border-slate-800 text-[11px] text-slate-500 space-y-1.5">
                        <div class="flex items-center gap-1.5 text-emerald-600 dark:text-emerald-400">
                            <i data-lucide="check-circle" class="w-3.5 h-3.5"></i>
                            <span>১০০% নিরাপদ অফিসিয়াল পেমেন্ট গেটওয়ে</span>
                        </div>
                        <div class="flex items-center gap-1.5 text-blue-600 dark:text-cyan-400">
                            <i data-lucide="shield" class="w-3.5 h-3.5"></i>
                            <span>Universal Technology Corporation (UTC) - Feni</span>
                        </div>
                    </div>

                </div>

            </div>

        </div>

    </div>
</div>

<script>
function switchPayTab(method) {
    const box = document.getElementById('pay-instruction-box');
    const inputs = document.getElementById('trx-inputs');
    
    if (method === 'bKash') {
        box.innerHTML = `
            <p class="font-bold text-slate-900 dark:text-white">বিকাশ সেন্ড মানি নির্দেশনা:</p>
            <p class="text-slate-600 dark:text-slate-300 leading-relaxed">
                ১. আপনার বিকাশ অ্যাপ থেকে "Send Money" অপশনে যান।<br>
                ২. প্রাপক নম্বর: <strong class="text-pink-600 dark:text-pink-400 font-mono text-sm">01812669757</strong> (ক্লিক করে কপি করুন)<br>
                ৩. টাকার পরিমাণ দিন এবং রেফারেন্স দিন। ট্রানজেকশন আইডি নিচে লিখুন।
            </p>
        `;
        inputs.style.display = 'grid';
    } else if (method === 'Nagad') {
        box.innerHTML = `
            <p class="font-bold text-slate-900 dark:text-white">নগদ সেন্ড মানি নির্দেশনা:</p>
            <p class="text-slate-600 dark:text-slate-300 leading-relaxed">
                ১. আপনার নগদ অ্যাপ বা USSD (*167#) থেকে "Send Money" করুন।<br>
                ২. নগদ নম্বর: <strong class="text-amber-500 font-mono text-sm">01817122483</strong><br>
                ৩. সফল পেমেন্টের পর প্রাপ্ত ৮ সংখ্যার TrxID নিচে লিখুন।
            </p>
        `;
        inputs.style.display = 'grid';
    } else if (method === 'Rocket') {
        box.innerHTML = `
            <p class="font-bold text-slate-900 dark:text-white">রকেট সেন্ড মানি নির্দেশনা:</p>
            <p class="text-slate-600 dark:text-slate-300 leading-relaxed">
                ১. আপনার রকেট একাউন্ট থেকে "Send Money" অপশনে যান।<br>
                ২. রকেট নম্বর: <strong class="text-purple-500 font-mono text-sm">01812669757-9</strong><br>
                ৩. সফল ট্রানজেকশন আইডি নিচে প্রদান করুন।
            </p>
        `;
        inputs.style.display = 'grid';
    } else if (method === 'Sandbox') {
        box.innerHTML = `
            <p class="font-bold text-emerald-600 dark:text-emerald-400">⚡ Instant Demo Sandbox Mode:</p>
            <p class="text-slate-600 dark:text-slate-300 leading-relaxed">
                টেস্টিং মোড চালু রয়েছে! এই মোডে ক্লিক করলেই কোনো বিকাশ/নগদ টাকা পাঠানো ছাড়াই সরাসরি আপনার পেমেন্ট তাৎক্ষণিক অ্যাপ্রুভ হয়ে যাবে এবং কোর্স এক্সেস ওপেন হবে।
            </p>
        `;
        inputs.style.display = 'none';
    }
}
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
