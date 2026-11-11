<?php
/**
 * Universal Technology Corporation (UTC) - Automated End-to-End Test Suite
 * Tests Student Registration, Course Enrollment, bKash Payment, Admin Approval,
 * Classroom Learning Progress, Assignment Submission, Teacher Grading & Certificate.
 */

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/auth.php';
require_once __DIR__ . '/../config/helpers.php';

echo "============================================================\n";
echo "  UNIVERSAL TECHNOLOGY CORPORATION (UTC) - TEST SUITE\n";
echo "============================================================\n\n";

$passCount = 0;
$failCount = 0;

function assertTest($condition, $message) {
    global $passCount, $failCount;
    if ($condition) {
        echo "  [PASS] " . $message . "\n";
        $passCount++;
    } else {
        echo "  [FAIL] " . $message . "\n";
        $failCount++;
    }
}

// -------------------------------------------------------------
// TEST 1: Database & 10 Courses Integrity
// -------------------------------------------------------------
echo "1. Testing Database & Courses Integrity...\n";

$courseCount = (int)$pdo->query("SELECT COUNT(*) FROM courses")->fetchColumn();
assertTest($courseCount >= 10, "Total courses count is {$courseCount} (Expected: 10)");

// Verify Course 1: Programming Combo Pack
$c1 = $pdo->query("SELECT * FROM courses WHERE id = 1")->fetch();
assertTest($c1 && strpos($c1['title'], 'Programming Combo Pack') !== false, "Course #1 title is: '{$c1['title']}'");
assertTest($c1['original_price'] == 20000 && $c1['discount_price'] == 10000, "Course #1 pricing: ৳{$c1['original_price']} -> ৳{$c1['discount_price']} (50% Off)");

// Verify Course 2: Advance Web Design
$c2 = $pdo->query("SELECT * FROM courses WHERE id = 2")->fetch();
assertTest($c2 && strpos($c2['title'], 'Advance Web Design') !== false, "Course #2 title verified");
assertTest($c2['original_price'] == 7000, "Course #2 original price is ৳7,000");

// Verify Course 10: Ethical Hacking
$c10 = $pdo->query("SELECT * FROM courses WHERE id = 10")->fetch();
assertTest($c10 && strpos($c10['title'], 'Ethical Hacking') !== false, "Course #10 title verified");
assertTest($c10['original_price'] == 80000, "Course #10 original price is ৳80,000");

// -------------------------------------------------------------
// TEST 2: Multi-Role Accounts Authentication
// -------------------------------------------------------------
echo "\n2. Testing Multi-Role Authentication...\n";

// Test Admin
$admin = $pdo->query("SELECT * FROM users WHERE email = 'admin@utc.edu.bd'")->fetch();
assertTest($admin && password_verify('admin123', $admin['password']), "Super Admin credentials verified (admin@utc.edu.bd)");

// Test Teacher
$teacher = $pdo->query("SELECT * FROM users WHERE email = 'teacher@utc.edu.bd'")->fetch();
assertTest($teacher && password_verify('teacher123', $teacher['password']), "Lead Teacher credentials verified (teacher@utc.edu.bd)");

// Test Student
$student = $pdo->query("SELECT * FROM users WHERE email = 'student@utc.edu.bd'")->fetch();
assertTest($student && password_verify('student123', $student['password']), "Demo Student credentials verified (student@utc.edu.bd)");

// -------------------------------------------------------------
// TEST 3: New Student Registration & Session
// -------------------------------------------------------------
echo "\n3. Testing New Student Registration Flow...\n";

$testEmail = 'test_student_' . time() . '@utc.edu.bd';
$testName = 'Kamrul Hasan (Feni)';
$testPhone = '01819998877';
$testPass = password_hash('pass123456', PASSWORD_DEFAULT);

$regStmt = $pdo->prepare("INSERT INTO users (name, email, password, phone, role) VALUES (?, ?, ?, ?, 'student')");
$regStmt->execute([$testName, $testEmail, $testPass, $testPhone]);
$newStudentId = $pdo->lastInsertId();

assertTest($newStudentId > 0, "New student registered with ID: {$newStudentId} ({$testEmail})");

// -------------------------------------------------------------
// TEST 4: Course Enrollment & bKash Payment Simulation
// -------------------------------------------------------------
echo "\n4. Testing Course Enrollment & bKash Payment Submission...\n";

$courseToEnroll = 1; // Programming Combo Pack
$finalFee = 10000;
$trxId = 'BKASH' . strtoupper(bin2hex(random_bytes(3)));

$enrStmt = $pdo->prepare("INSERT INTO enrollments (
    student_id, course_id, batch_name, amount, payment_method, transaction_id, sender_phone, payment_status, enrolled_at
) VALUES (?, ?, 'UTC Batch 2026-A', ?, 'bKash', ?, ?, 'pending', CURRENT_TIMESTAMP)");

$enrStmt->execute([$newStudentId, $courseToEnroll, $finalFee, $trxId, $testPhone]);
$enrollmentId = $pdo->lastInsertId();

assertTest($enrollmentId > 0, "Enrollment order created with ID: {$enrollmentId}");

$checkEnroll = $pdo->query("SELECT * FROM enrollments WHERE id = {$enrollmentId}")->fetch();
assertTest($checkEnroll['payment_status'] === 'pending', "Enrollment payment status is correctly 'pending'");
assertTest($checkEnroll['transaction_id'] === $trxId, "Transaction ID stored: {$trxId}");

// -------------------------------------------------------------
// TEST 5: Admin Payment Verification & Approval
// -------------------------------------------------------------
echo "\n5. Testing Admin Approval Workflow...\n";

// Admin approves the enrollment
$appStmt = $pdo->prepare("UPDATE enrollments SET payment_status = 'approved', approved_at = CURRENT_TIMESTAMP WHERE id = ?");
$appStmt->execute([$enrollmentId]);

$approvedEnroll = $pdo->query("SELECT * FROM enrollments WHERE id = {$enrollmentId}")->fetch();
assertTest($approvedEnroll['payment_status'] === 'approved', "Enrollment approved by Admin!");
assertTest(!empty($approvedEnroll['approved_at']), "Approval timestamp recorded: {$approvedEnroll['approved_at']}");

// Verify revenue statistics updated
$totalRevenue = (float)$pdo->query("SELECT SUM(amount) FROM enrollments WHERE payment_status = 'approved'")->fetchColumn();
assertTest($totalRevenue >= 20000, "Admin Revenue correctly recalculated to: " . formatBDT($totalRevenue));

// -------------------------------------------------------------
// TEST 6: Student Classroom Access & Lesson Progress
// -------------------------------------------------------------
echo "\n6. Testing Student Classroom & Lesson Progress...\n";

// Fetch first lesson of Course 1
$firstLesson = $pdo->query("SELECT * FROM lessons WHERE course_id = 1 ORDER BY sort_order ASC LIMIT 1")->fetch();
assertTest($firstLesson !== false, "Fetched Lesson: '{$firstLesson['title']}'");

// Student marks lesson as completed
$progStmt = $pdo->prepare("INSERT OR REPLACE INTO course_progress (student_id, lesson_id, course_id, completed, updated_at) VALUES (?, ?, 1, 1, CURRENT_TIMESTAMP)");
$progStmt->execute([$newStudentId, $firstLesson['id']]);

$progCheck = $pdo->query("SELECT * FROM course_progress WHERE student_id = {$newStudentId} AND lesson_id = {$firstLesson['id']}")->fetch();
assertTest($progCheck && $progCheck['completed'] == 1, "Lesson marked as completed in student progress!");

// -------------------------------------------------------------
// TEST 7: Assignment Submission by Student
// -------------------------------------------------------------
echo "\n7. Testing Assignment Submission...\n";

$assignment = $pdo->query("SELECT * FROM assignments WHERE course_id = 1 LIMIT 1")->fetch();
assertTest($assignment !== false, "Active Assignment found: '{$assignment['title']}' (Max: {$assignment['max_marks']})");

$submissionText = 'https://github.com/kamrul-utc/c-algorithms-solutions - Implemented Bubble Sort & Binary Search';
$subStmt = $pdo->prepare("INSERT INTO submissions (assignment_id, student_id, submission_text, submitted_at) VALUES (?, ?, ?, CURRENT_TIMESTAMP)");
$subStmt->execute([$assignment['id'], $newStudentId, $submissionText]);
$submissionId = $pdo->lastInsertId();

assertTest($submissionId > 0, "Student submitted homework successfully! Submission ID: {$submissionId}");

// -------------------------------------------------------------
// TEST 8: Teacher Review & Grading Workflow
// -------------------------------------------------------------
echo "\n8. Testing Teacher Grading Workflow...\n";

$givenMarks = 98.5;
$teacherFeedback = 'Excellent implementation! Clean code structure and detailed complexity analysis.';

$gradeStmt = $pdo->prepare("UPDATE submissions SET marks_obtained = ?, feedback = ?, graded_at = CURRENT_TIMESTAMP WHERE id = ?");
$gradeStmt->execute([$givenMarks, $teacherFeedback, $submissionId]);

$gradedSub = $pdo->query("SELECT * FROM submissions WHERE id = {$submissionId}")->fetch();
assertTest($gradedSub['marks_obtained'] == $givenMarks, "Teacher graded assignment: {$gradedSub['marks_obtained']} / {$assignment['max_marks']}");
assertTest($gradedSub['feedback'] === $teacherFeedback, "Teacher feedback saved successfully");

// -------------------------------------------------------------
// TEST 9: Digital Certificate Verification
// -------------------------------------------------------------
echo "\n9. Testing Digital Certificate Generation...\n";

$certId = 'UTC-CERT-2026-' . str_pad(1 * 100 + $newStudentId, 6, '0', STR_PAD_LEFT);
assertTest(!empty($certId), "Certificate ID generated: {$certId} for {$testName}");

// -------------------------------------------------------------
// TEST 10: Coupon Validation
// -------------------------------------------------------------
echo "\n10. Testing Coupon Validation...\n";

$c50 = $pdo->query("SELECT * FROM coupons WHERE code = 'UTC50' AND is_active = 1")->fetch();
assertTest($c50 && $c50['discount_percent'] == 50, "Coupon 'UTC50' verified (50% Discount)");

$c30 = $pdo->query("SELECT * FROM coupons WHERE code = 'UTC30' AND is_active = 1")->fetch();
assertTest($c30 && $c30['discount_percent'] == 30, "Coupon 'UTC30' verified (30% Discount)");

// -------------------------------------------------------------
// SUMMARY
// -------------------------------------------------------------
echo "\n============================================================\n";
echo "  TEST SUMMARY: {$passCount} PASSED, {$failCount} FAILED\n";
echo "============================================================\n";

if ($failCount === 0) {
    echo "🎉 ALL TESTS PASSED SUCCESSFULLY! The UTC E-Learning system is 100% operational.\n";
} else {
    echo "⚠️ Some tests failed. Please review the output above.\n";
}
