<?php include '../components/connect.php' ;?>
<?php 
$id = $_GET['id']; // جلب معرف السجل من الرابط

// استعلام SQL لحذف السجل
$sql = "DELETE FROM user_accounts WHERE id='$id'";
$conn->query($sql);
$sql = "DELETE FROM teacher_subject WHERE teacher_id='$id'";
$conn->query($sql);
echo "<script>alert('تم الحذف بنجاح');</script>";
header('location: ../admin/admin_dashboard.php');
$conn->close();
?>