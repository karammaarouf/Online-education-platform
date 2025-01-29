<!-- الاتصال بقاعدة البيانات -->
<?php include '../components/connect.php'; ?>
<!-- فحص حالة تسجيل الدخول -->
<?php
session_start(); //بدء جلسة جديدة لحفظ بيانات المستخدم
if (isset($_SESSION['user_id'])) {
    $error = FALSE;
    $id = $_SESSION['user_id'];
    if (isset($_POST['submit'])) {
        $name = $_POST['name'];
        $email = $_POST['email'];
        $old_pass = $_POST['old_pass'];
        $new_pass = $_POST['new_pass'];
        $c_pass = $_POST['c_pass'];
        $image = $_FILES['photo']['name'];
        $image = filter_var($image, FILTER_SANITIZE_STRING);
        $ext = pathinfo($image, PATHINFO_EXTENSION);
        $rename = unique_id() . '.' . $ext;
        $image_size = $_FILES['photo']['size'];
        $image_tmp_name = $_FILES['photo']['tmp_name'];
        $image_folder = '../uploaded_files/' . $rename;
        if (!empty($image)) {
            if ($image_size < 2000000) {
                move_uploaded_file($image_tmp_name, $image_folder);
                $sql = "UPDATE user_accountS SET photo='$image_folder' WHERE id='$id'";
                if ($conn->query($sql)) {
                    $_SESSION['photo'] = $image_folder;
                }
            }
        }
        if (!empty($name)) {
            $sql = "UPDATE user_accounts SET name='$name' WHERE id='$id'";
            $conn->query($sql);
        }
        if (!empty($email)) {
            $sql = "UPDATE user_accounts SET email='$email' WHERE id='$id'";
            try {
                if ($conn->query($sql) === TRUE) {
                    echo "تم تحديث السجل بنجاح";
                } else {
                    $error = TRUE;
                    throw new Exception("خطأ في الاستعلام: " . $conn->error);
                }
            } catch (Exception $e) {
                echo '<script>alert("هذا الحساب مستخدم مسبقاً");</script>';
                $error = TRUE;
            }
        }
        if (!empty($old_pass)) {
            if (password_verify($old_pass, $_SESSION['password'])) {
                if ($new_pass == $c_pass) {
                    if (strlen($pass >= 8)) {
                        if (preg_match('/[a-zA-Z]/', $pass) && preg_match('/\d/', $pass)) {
                            $pass_hash = password_hash($new_pass, PASSWORD_DEFAULT);
                            $sql = "UPDATE user_accounts SET password='$pass_hash' WHERE id='$id'";
                            $conn->query($sql);
                        } else {
                            echo '<script>alert("كلمة المرور ضعيفة للغاية , استخدم مزيجاً من الأحرف و الأرقام."); window.location.href="register.php";</script>';
                        }
                    } else {
                        echo '<script>alert("كلمة المرور قصيرة جداً"); window.location.href="register.php";</script>';
                    }
                } else {
                    echo '<script>alert("كلمة المرور  غير متطابقة");</script>';
                    $error = TRUE;
                }
            } else {
                echo '<script>alert("كلمة المرور القديمة غير صحيحة");</script>';
                $error = TRUE;
            }
        }

        if ($error) {
            echo '<script>alert("حدث خطأ اثناء التحديث");</script>';
            header('refresh:1;url=update.php');
        } else {
            echo '<script>alert("تم تحديث البيانات بنجاح");</script>';
            switch ($_SESSION['type']) {
                case 1:
                    header('refresh:1;url=../admin/admin_dashboard.php');
                    break;
                case 2:
                    header('refresh:1;url=../teacher/teacher_dashboard.php');
                    break;
                case 3:
                    header('refresh:1;url=../student/student_dashboard.php');
                    break;
                case 4:
                    header('refresh:1;url=../parent/parent_dashboard.php');
                    break;
            }
        }
    }
    $conn->close();
} else {
    header('location: ../index.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>admin</title>
    <link rel="stylesheet" href="../css/style.css">
    <!-- font awesome cdn link  -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css">
</head>

<body>

    <div class="form-div">
        <form action="" method="post" enctype="multipart/form-data">
            <h1>تحديث البيانات</h1>
            <div class="form-row">
                <div class="form-group">
                    <img src="<?= $_SESSION['photo'] ?>" alt="الصورة الشخصية" style="max-width: 200px; max-height: 200px;">
                </div>
            </div>

            <br>
            <div class="form-row">
                <div class="form-group">
                    <label>تحديث الصورة</label>
                    <input type="file" accept="image/*" name="photo" class="box">
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label>الأسم</label>
                    <input type="text" name="name" placeholder="<?= $_SESSION['username']; ?>" maxlength="50" class="box">
                </div>
                <div class="form-group">
                    <label>الأيميل</label>
                    <input type="email" name="email" placeholder="<?= $_SESSION['email']; ?>" maxlength="100" class="box">
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label>كلمة المرور القديمة</label>
                    <input type="password" name="old_pass" placeholder="اكتب كلمة المرور القديمة" maxlength="20" class="box">
                </div>
                <div class="form-group">
                    <label>كلمة المرور الجديدة</label>
                    <input type="password" name="new_pass" placeholder="اكتب كلمة المرور الجديدة" maxlength="20" class="box">
                </div>
                <div class="form-group">
                    <label>تأكيد كلمة المرور</label>
                    <input type="password" name="c_pass" placeholder="أعد كتابة كلمة المرور الجديدة" maxlength="20" class="box">
                </div>
            </div>

            <button type="submit" name="submit" class="btn">تحديث البيانات</button>
        </form>
    </div>


    <a href="back.php" class="back-btn"><span class="icon"><i class="fa-solid fa-chevron-left"></i></span></a>



    <!-- الفوتر -->
    <?php include '../components/footer.php'; ?>

    <script src="../js/script.js"></script>
</body>

</html>