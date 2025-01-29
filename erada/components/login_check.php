<!-- الاتصال بقاعدة البيانات -->
<?php include 'connect.php' ?>
<!-- فحص حالة تسجيل الدخول -->
<?php
session_start(); //بدء جلسة جديدة لحفظ بيانات المستخدم

if (isset($_POST['submit'])) { //التحقق من ان زر الفورم تم الضغط عليه و استقبال بيانات
    $email = $_POST['email'];
    $pass = $_POST['pass'];

    $sql = "SELECT * FROM user_accounts WHERE email='$email'";
    $rs = $conn->query($sql); //للحصول على ناتج الاستعلام من قاعدة البيانات
    if ($rs->num_rows > 0) { // اذا لميكن الاستعلام فارغ و يحوي سطر واحد على الاقل
        $row = $rs->fetch_assoc(); //تحويل الاستعلام السابق الى مصفوفة كل سجل بسطره
        if (password_verify($pass, $row['password'])) {
            if ($row['status'] == 'active') { // اختبار اذا كان الحساب غير محظور
                //مدير
                if ($row['type'] == 1) { //فحص نوع هذا الحساب للتوجه الى الواجهة الموافقة له
                    $_SESSION['username'] = $row['name']; //تخزين اسم المستخدم في الجلسة للحفاظ على بيانات تسجيل الدخول اسثناء تصفح الواجهة
                    $_SESSION['user_id'] = $row['id'];
                    $_SESSION['email'] = $row['email'];
                    $_SESSION['password'] = $row['password'];
                    $_SESSION['photo'] = $row['photo'];
                    $_SESSION['type'] = $row['type'];
                    $conn->close(); //اغلاق الاتصال بقاعدة البيانات منعاً لحدوث الاخطاء
                    header("Location: ../admin/admin_dashboard.php"); // امر الانتقال الى واجهة المدير
                    exit();
                } //ايقاف تنفيذ باقي الكود اذا تحقق هذا الشرط
                //مدرس
                elseif ($row['type'] == 2) {
                    $_SESSION['username'] = $row['name'];
                    $_SESSION['email'] = $row['email'];
                    $_SESSION['user_id'] = $row['id'];
                    $_SESSION['password'] = $row['password'];
                    $_SESSION['photo'] = $row['photo'];
                    $_SESSION['type'] = $row['type'];
                    $conn->close();
                    header("Location: ../teacher/teacher_dashboard.php");
                    exit();
                }
                //طالب
                elseif ($row['type'] == 3) {
                    $_SESSION['username'] = $row['name'];
                    $_SESSION['email'] = $row['email'];
                    $_SESSION['user_id'] = $row['id'];
                    $_SESSION['password'] = $row['password'];
                    $_SESSION['photo'] = $row['photo'];
                    $_SESSION['type'] = $row['type'];
                    $conn->close();
                    header("Location: ../student/student_dashboard.php");
                    exit();
                }
                //ولي أمر
                else {

                    $_SESSION['username'] = $row['name'];
                    $_SESSION['email'] = $row['email'];
                    $_SESSION['user_id'] = $row['id'];
                    $_SESSION['password'] = $row['password'];
                    $_SESSION['photo'] = $row['photo'];
                    $_SESSION['type'] = $row['type'];
                    $conn->close();
                    header("Location: ../parent/parent_dashboard.php");
                    exit();
                }
            } else {
                echo '<script>alert("تم حظر حسابك!"); window.location.href="../index.php";</script>';
            }
        } else {
            echo '<script>alert("كلمة المرور خاطئة!"); window.location.href="login.php";</script>';
        }
    } else {
        echo '<script>alert("خطأفي البريد الالكتروني"); window.location.href="login.php";</script>'; //في حال لم تكن هناك بيانات اذا احتمال ان تكون كلمة السر خاطئو او الحساب خاطئ
    }
}
$conn->close(); //اغلاق الاتصال بقاعدة البيانات منعاً لحدوث الاخطاء
?>