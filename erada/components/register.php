<?php include 'connect.php'; ?>
<?php
session_start();
if (isset($_POST['submit'])) {

    $name  = $_POST['name'];
    $email  = $_POST['email'];
    $sql = "SELECT * FROM user_accounts WHERE email='$email'";
    $result = $conn->query($sql);
    if ($result->num_rows > 0) {
        echo "<script>alert('هذا الحساب مستخدم بالفعل')</script>";
        header("refresh:0.5;URL=login.php");
        exit();
    }




    $birth = $_POST['birth'];

    $pass = $_POST['pass'];
    $cpass = $_POST['c_pass'];

    $academic_year = $_POST['academic-year'];

    $image = $_FILES['image']['name'];
    $image = filter_var($image, FILTER_SANITIZE_STRING);
    $ext = pathinfo($image, PATHINFO_EXTENSION);
    $rename = unique_id() . '.' . $ext;
    $image_size = $_FILES['image']['size'];
    $image_tmp_name = $_FILES['image']['tmp_name'];
    $image_folder = '../uploaded_files/' . $rename;
    if (!empty($image)) {
        if ($image_size < 2000000) {
            move_uploaded_file($image_tmp_name, $image_folder);
        }
    }



    if ($pass == $cpass) {
        if (strlen($pass >= 8)) {
            if (preg_match('/[a-zA-Z]/', $pass) && preg_match('/\d/', $pass)) {
                $id  = unique_id();
                $pass_hash = password_hash($pass, PASSWORD_DEFAULT);
                $sql = "INSERT INTO user_accounts (id,name,email,password,photo,status,type,birth) VALUES('$id','$name','$email','$pass_hash','$image_folder','active','3','$birth')";
                if ($conn->query($sql)) {
                    $sql = "INSERT INTO student_info (id,academic_year)VALUES('$id','$academic_year')";
                    $conn->query($sql);
                    $_SESSION['username'] = $name;
                    $_SESSION['user_id'] = $id;
                    $_SESSION['email'] = $email;
                    $_SESSION['photo'] = $image_folder;
                    $_SESSION['academic-year'] = $academic_year;

                    move_uploaded_file($image_tmp_name, $image_folder);
                    if ($prev_image != '' and $prev_image != $rename) {
                        unlink('uploaded_files/' . $prev_image);
                    }

                    header('Location: select_subjects.php');
                    exit();
                }
            } else {
                echo '<script>alert("كلمة المرور ضعيفة للغاية , استخدم مزيجاً من الأحرف و الأرقام."); window.location.href="register.php";</script>';
            }
        } else {
            echo '<script>alert("كلمة المرور قصيرة جداً"); window.location.href="register.php";</script>';
        }
    } else {
        echo '<script>alert("يرجى التأكد من  كلمة المرور!"); window.location.href="register.php";</script>'; //في حال كانت كلمة المرور غير متطابقة  

    }
}
?>
<?php
//دالة تعيد المواد
function get_subject()
{
    global $conn;
    $sql = 'SELECT distinct academic_year FROM subjects order by academic_year';
    $result = $conn->query($sql);
    return $result;
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>register</title>
    <link rel="stylesheet" href="../css/style.css">
    <!-- font awesome cdn link  -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css">
</head>

<body class="body_bg">
    <header class="header">
        <div class="logo-div">
            <a href="../index.php" class="logo"><img src="../images/logo.png" alt="لوغو المنصة">إرادة</a>
        </div>


        <div class="icons">
            <button id="toggle-btn" class="toggle-btn" onclick="darkmode(this)">🌙</button>
        </div>


    </header>

    <!-- فورم تسجيل الاشتراك-->

    <div class="form-div">
        <form action="" method="post" enctype="multipart/form-data">
            <h1>بيانات حساب الطالب</h1>
            <div class="form-row">
                <div class="form-group">
                    <label for="name">الاسم الثلاثي: <span>*</span></label>
                    <input type="text" name='name' placeholder="ادخل الاسم الثلاثي..." required>
                </div>
                <div class="form-group">
                    <label for="email">البريد الالكتروني: <span>*</span></label>
                    <input type="email" name='email' placeholder="ادخل بريدك الالكتروني..." required>
                </div>
                <div class="form-group">
                    <label for="birth">تاريخ الميلاد: <span>*</span></label>
                    <input type="date" name='birth' required>
                </div>
            </div>



            <div class="form-row">
                <div class="form-group">
                    <label for="pass">كلمة المرور: <span>*</span></label>
                    <input type="password" name='pass' placeholder="كلمة المرور ينبغي ان تكون قوية..." required>
                </div>
                <div class="form-group">
                    <label for="c_pass">تأكيد كلمة المرور: <span>*</span></label>
                    <input type="password" name='c_pass' placeholder="تأكد من تطابق كلمة المرور..." required>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="image">الصورة الشخصية: <span>*</span></label>
                    <input type="file" name='image' accept="image/*" required>
                </div>
                <div class="form-group">
                    <label for="academy">السنة الدراسية: <span>*</span></label>
                    <select name="academic-year" id="" required>
                        <option value="">--احتر المرحلة--</option>
                        <?php $acad = get_subject();
                        foreach ($acad as $ac) : ?>
                            <option value="<?= $ac['academic_year'] ?>"><?php if ($ac['academic_year'] == 'primary-school')
                                                                            echo 'المرحلة الابتدائية';
                                                                        if ($ac['academic_year'] == 'middle-school')
                                                                            echo 'المرحلة الاعدادية';
                                                                        if ($ac['academic_year'] == 'high-school')
                                                                            echo 'المرحلة الثانوية';
                                                                        ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            <input type="submit" name="submit" value="تسجيل الحساب" class="btn">
    </div>
    </form>
    </div>
    <a href="back.php" class="back-btn"><span class="icon"><i class="fa-solid fa-chevron-left"></i></span></a>
    <!-- الفوتر -->
    <?php include 'footer.php'; ?>
    <script src="../js/script.js"></script>
</body>

</html>