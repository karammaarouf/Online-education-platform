<?php 
include '../components/connect.php';

$id = $_GET['id'];

// التأكد من تنظيف المعرف لتجنب SQL Injection
$id = $conn->real_escape_string($id);

// حذف من جدول المواد
$sql = "DELETE FROM subjects WHERE id = '$id'";
$conn->query($sql);

// حذف من جدول المواد الخاصة بالمدرسين
$sql = "DELETE FROM teacher_subject WHERE subject_id = '$id'";
$conn->query($sql);

// حذف من جدول المواد الخاصة بالطلاب
$sql = "DELETE FROM student_subject WHERE subject_id = '$id'";
$conn->query($sql);

echo "<script>alert('".$id."تم حذف المادة بنجاح')</script>";
// إعادة التوجيه إلى لوحة تحكم المدير مع رسالة نجاح
header('refresh:0.5;URL=../admin/admin_dashboard.php?message=deleted');
$conn->close();
exit; // تأكد من إنهاء السكريبت بعد إعادة التوجيه
?>