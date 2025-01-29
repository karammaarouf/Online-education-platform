<?php include '../components/connect.php' ?>

    <?php 
    session_start();
    if(isset($_GET['id'])){
        $id=$_GET['id'];
        $active_account="UPDATE user_accounts SET status='active' WHERE id = '$id'";
        $block_account="UPDATE user_accounts SET status='block' WHERE id = '$id'";
        $_GET['status'] == 'active'?$conn->query($block_account):$conn->query($active_account);
        if($_SESSION['type']==1){
        header("Location: ../admin/admin_dashboard.php");}
        else{

            header("Location: ../teacher/teacher_dashboard.php");
        }
    }

    $conn->close();
    ?>