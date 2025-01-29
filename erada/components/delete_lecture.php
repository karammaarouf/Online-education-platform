<?php include '../components/connect.php' ;?>
<?php
session_start();
$id=$_GET['id'];
$sql="DELETE FROM lectures WHERE id = '$id'";
$conn->query($sql);
echo "<script>alert('تم حذف المحاضرة بنجاح');</script>";

if($_SESSION['type']==2){
header('refresh:0.5;url=../teacher/teacher_dashboard.php');
}
else{
    header('refresh:0.5;url=../admin/admin_dashboard.php');
}
$conn->close();
?>