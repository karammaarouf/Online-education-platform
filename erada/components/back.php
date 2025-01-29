<?php include("connect.php");
session_start();
if (isset($_SESSION["type"])) {
if ($_SESSION["type"] == 1) {
    header("location:../admin/admin_dashboard.php");
}
elseif ($_SESSION["type"] == 2) {
    header("location:../teacher/teacher_dashboard.php");
}
elseif ($_SESSION["type"] == 3) {
    header("location:../student/student_dashboard.php");
}
else{
    header("location:../parent/parent_dashboard.php");
}
}
else {
    header("location:../index.php");
}

?>