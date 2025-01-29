<?php include '../components/connect.php'; ?>
<?php if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $sql = "SELECT * FROM subjects WHERE id='$id'";
    $rs = $conn->query($sql);
    if ($rs->num_rows > 0) {
        $rs = $rs->fetch_assoc();
        $name = $rs['name'];
        $academic_year = $rs['academic_year'];
    }
}
?>
<?php
if (isset($_POST['subject_submit'])) {
    $name = $_POST['subject_name'];
    $academic_year = $_POST['acad'];
    $sql = "UPDATE subjects SET name='$name',academic_year='$academic_year' WHERE id='$id'";
    $conn->query($sql);
    $conn->close();
    header("location: ../admin/admin_dashboard.php");
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>update</title>
    <link rel="stylesheet" href="../css/style.css">
               <!-- font awesome cdn link  -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css">
</head>

<body>
    <div class="form-div">
        <form action="" method="POST" enctype="multipart/form-data">
            <h1>تحديث بيانات المادة</h1>
            <div class="form-row">
                <div class="form-group">
                    <label for="">الاسم:</label>
                    <input type="text" name='subject_name' value="<?php echo $name ?>">
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="">السنة الدراسية:</label>
                    <select name="acad" id="">
                        <option value="<?= $academic_year ?>"><?= $academic_year ?></option>
                        <option value="primary-school">الابتدائي</option>
                        <option value="middle-school">الاعدادي</option>
                        <option value="high-school">الثانوي</option>
                    </select>
                </div>
            </div>
            <button type="submit" name="subject_submit">تحديث البيانات</button>
    </div>

    <a href="back.php" class="back-btn"><span class="icon"><i class="fa-solid fa-chevron-left"></i></span></a>
</body>

</html>