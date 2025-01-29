<?php include '../components/connect.php'; ?>
<?php if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $sql = "SELECT * FROM user_accounts WHERE id='$id'";
    $rs = $conn->query($sql);
    if ($rs->num_rows > 0) {
        $rs = $rs->fetch_assoc();
        $name = $rs['name'];
        $email = $rs['email'];
        $pass = $rs['password'];
        $birth = $rs['birth'];
        $status = $rs['status'];
        $photo = $rs['photo'];
    }
    // استعلام عن اسم المواد المرتبط باسم الاستاذ بيان مشترك بين جدولين
    $sql_ts = "SELECT subject_id FROM teacher_subject WHERE teacher_id='$id'";
    $rs_ts = $conn->query($sql_ts);
    $teacherSubjects = [];
    while ($row = $rs_ts->fetch_assoc()) {
        $teacherSubjects[] = $row['subject_id'];
    }
}
?>
<?php if (isset($_POST['teacher_submit'])) {
    $name = $_POST['teacher_name'];
    $email = $_POST['teacher_email'];
    $pass = $_POST['teacher_password'];
    $birth = $_POST['teacher_birth'];
    $status = $_POST['teacher_status'];

    $image = $_FILES['teacher_photo']['name'];
    if (isset($_FILES['teacher_photo']) && $_FILES['teacher_photo']['error'] === UPLOAD_ERR_OK) {
        $image = filter_var($image, FILTER_SANITIZE_STRING);
        $ext = pathinfo($image, PATHINFO_EXTENSION);
        $rename = unique_id() . '.' . $ext;
        $image_size = $_FILES['teacher_photo']['size'];
        $image_tmp_name = $_FILES['teacher_photo']['tmp_name'];
        $image_folder = '../uploaded_files/' . $rename;
        if ($image_size < 2000000) {
            move_uploaded_file($image_tmp_name, $image_folder);
            $photo = $image_folder;
        }
    }

    $subjects_id = $_POST['teacher_subjects'];
    $sql = "UPDATE user_accounts SET name='$name',email='$email',password='$pass',status='$status',photo='$photo' WHERE id='$id'";
    $conn->query($sql);

    //حذف المواد من المدرس لاعادة تحديثها
    $sql = "DELETE FROM  teacher_subject WHERE teacher_id='$id' ";
    $conn->query($sql);
    //اضافة المواد بعد التعديل الة المدرس
    foreach ($subjects_id as $subject_id) {
        $sql = "INSERT INTO teacher_subject (teacher_id,subject_id,teacher_name,subject_name) VALUES ('$id','$subject_id','$name','')";
        $conn->query($sql);
    }
    $conn->close();
    header("location: ../admin/admin_dashboard.php");
    $conn->close();
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
            <h1>تحديث بيانات المدرس</h1>
            <div class="form-row">
                <div class="form-group">
                    <label for="image">الصورة:</label>
                    <img src="<?= $photo ?>" alt="Current Photo" style="max-width: 200px; max-height: 200px;">
                    <br>
                    <input type="file" id="photo" name="teacher_photo" accept="image/*">
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="name">الاسم:</label>
                    <input type="text" id="name" name="teacher_name" value="<?= $name ?>">
                </div>
                <div class="form-group">
                    <label for="email">الإيميل:</label>
                    <input type="email" id="email" name="teacher_email" value="<?= $email ?>">
                </div>
                <div class="form-group">
                    <label for="password">كلمة المرور:</label>
                    <input type="password" id="password" name="teacher_password" value="<?= $pass ?>">
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="birthdate">تاريخ الميلاد:</label>
                    <input type="date" id="teacher_birth" name="teacher_birth" value="<?= $birth ?>">
                </div>
                <div class="form-group">
                    <label for="teacher_status">الحالة:</label>
                    <select name="teacher_status" id="teacher_status">
                        <option value="<?= ($status == 'active' ? 'active' : 'block') ?>"><?= ($status == 'active' ? 'مفعل' : 'محظور') ?></option>
                        <option value="<?= ($status == 'active' ? 'block' : 'active') ?>"><?= ($status == 'active' ? 'محظور' : 'مفعل') ?></option>
                    </select>
                </div>
            </div>
            <div class="form-row">
                <div class="form-group check_input">
                    <label for="subject" class="title">مواد الابتدائي:</label>
                    <?php $sql = "SELECT * FROM subjects WHERE academic_year='primary-school'";
                    $rs = $conn->query($sql);
                    if ($rs) {
                        while ($row = $rs->fetch_assoc()) {

                            $checked = in_array($row['id'], $teacherSubjects) ? 'checked' : '';
                            echo "<input type='checkbox' id='" . $row['name'] . "' name='teacher_subjects[]' value='" . $row['id'] . "' $checked>
                                            <label for='" . $row['name'] . "'>" . $row['name'] . "</label><br>";
                        }
                    } ?>
                </div>
                <div class="form-group check_input">
                    <label for="subject" class="title">مواد الاعدادي:</label>
                    <?php $sql = "SELECT * FROM subjects WHERE academic_year='middle-school'";
                    $rs = $conn->query($sql);
                    if ($rs) {
                        while ($row = $rs->fetch_assoc()) {
                            $checked = in_array($row['id'], $teacherSubjects) ? 'checked' : '';
                            echo "<input type='checkbox' id='" . $row['name'] . "' name='teacher_subjects[]' value='" . $row['id'] . "' $checked>
                                            <label for='" . $row['name'] . "'>" . $row['name'] . "</label><br>";
                        }
                    } ?>
                </div>
                <div class="form-group check_input">
                    <label for="subject" class="title">مواد الثانوي:</label>
                    <?php $sql = "SELECT * FROM subjects WHERE academic_year='high-school'";
                    $rs = $conn->query($sql);
                    if ($rs) {
                        while ($row = $rs->fetch_assoc()) {
                            $checked = in_array($row['id'], $teacherSubjects) ? 'checked' : '';
                            echo "<input class='check_input' type='checkbox' id='" . $row['name'] . "' name='teacher_subjects[]' value='" . $row['id'] . "' $checked>
                                            <label for='" . $row['name'] . "'>" . $row['name'] . "</label><br>";
                        }
                    } ?>
                </div>
            </div>
            <button type="submit" name="teacher_submit">تحديث البيانات</button>
        </form>
    </div>
    <a href="back.php" class="back-btn"><span class="icon"><i class="fa-solid fa-chevron-left"></i></span></a>
</body>

</html>