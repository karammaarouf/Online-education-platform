<!-- الاتصال بقاعدة البيانات -->
<?php include '../components/connect.php'; ?>
<!-- فحص حالة تسجيل الدخول -->
<?php
session_start(); //بدء جلسة جديدة لحفظ بيانات المستخدم
if (!isset($_SESSION['user_id']) || ($_SESSION['type'] != 1)) {
    header('Location: ../components/login.php');
    $conn->close();
    exit();
} else {
    //دالة الاستعلام عن كافة المواد
    function get_subjects()
    {
        global $conn;
        $sql = "SELECT * FROM subjects";
        $result = $conn->query($sql);
        return $result;
    }
    //دالة الاستعلام عن المدرسين 
    function get_teachers()
    {
        global $conn;
        $sql = "SELECT * FROM user_accounts WHERE type='2'";
        $result = $conn->query($sql);
        return $result;
    }
    //دالة الاستعلام عن الطلاب 
    function get_students()
    {
        global $conn;
        $sql = "SELECT * FROM user_accounts WHERE type='3'";
        $result = $conn->query($sql);
        return $result;
    }
    //دالة الاستعلام عن كافة المدرسين حسب المادة المحددة
    function get_teachers_subject($subject_id)
    {
        global $conn;
        $sql = "SELECT * FROM teacher_subject WHERE subject_id='$subject_id'";
        $result = $conn->query($sql);
        $teachers = [];
        if ($result->num_rows > 0) {
            foreach ($result as $teacher) {
                $id = $teacher['teacher_id'];
                $sql = "SELECT * FROM user_accounts WHERE id='$id'";
                $result = $conn->query($sql);
                $teachers[] = $result->fetch_assoc();
            }
        }
        return $teachers;
    }
    //دالة الاستعلام عن كافة المواد حسب المدرس المحدد
    function get_subjects_teacher($teacher_id)
    {
        global $conn;
        $sql = "SELECT * FROM teacher_subject WHERE teacher_id='$teacher_id'";
        $result = $conn->query($sql);
        $subjects = [];
        if ($result->num_rows > 0) {
            foreach ($result as $subject) {
                $id = $subject['subject_id'];
                $sql = "SELECT * FROM subjects WHERE id='$id'";
                $result = $conn->query($sql);
                $subjects[] = $result->fetch_assoc();
            }
        }
        return $subjects;
    }
    //استعلام عن الامتحانات المتوفرة
    function get_exams()
    {
        global $conn;

        $stmt = $conn->prepare("SELECT * FROM exam_start ORDER BY teacher_name");
        $stmt->execute();

        // الحصول على النتائج
        $result = $stmt->get_result();

        // التحقق من وجود نتائج
        if ($result->num_rows > 0) {
            $exams = $result->fetch_all(MYSQLI_ASSOC);
        } else {
            $exams = []; // مصفوفة فارغة في حالة عدم وجود امتحانات
        }

        // إغلاق الاتصال
        $stmt->close();

        return $exams;
    }
    //احضار بيانات الحساب حسب الايدي
    function get_user($user_id)
    {
        global $conn;
        $sql = "SELECT* FROM user_accounts WHERE id='$user_id'";
        return $conn->query($sql)->fetch_assoc();
    }
    //احضار بيانات الحساب حسب الايميل
    function get_user_email($email)
    {
        global $conn;
        $sql = "SELECT* FROM user_accounts WHERE email='$email'";
        if ($conn->query($sql)->num_rows > 0)
            return $conn->query($sql)->fetch_assoc();
        return FALSE;
    }
    //استعلام عن المراحل الدراسية
    function get_acad()
    {
        global $conn;
        $sql = "SELECT DISTINCT academic_year FROM subjects";
        return $conn->query($sql);
    }
    //استعلام عن رسائل اولياء الامور
    function get_parent_masseges()
    {
        global $conn;
        $sql = "SELECT * FROM masseges,user_accounts WHERE masseges.recipient_id='{$_SESSION['user_id']}' AND masseges.sender_id=user_accounts.id AND user_accounts.type='4'";
        $result = $conn->query($sql);
        return $result;
    }
    //استعلام عن رسائل المدرسيين 
    function get_teacher_masseges()
    {
        global $conn;
        $sql = "SELECT * FROM masseges,user_accounts WHERE masseges.recipient_id='{$_SESSION['user_id']}' AND masseges.sender_id=user_accounts.id AND user_accounts.type='2'";
        $result = $conn->query($sql);
        return $result;
    }

    //دالة لاحضار محاضرات مدرس معين بمادة معينة
    function get_lectures($teacher_id, $subject_id)
    {
        global $conn;
        $sql = "SELECT * FROM lectures WHERE teacher_id='$teacher_id' AND subject_id='$subject_id' ";
        $result = $conn->query($sql);
        return $result;
    }
    //استعلام عن مواد الطالب حسب الايدي
    function get_subject_student_by_id($student_id)
    {
        global $conn;
        $sql = "SELECT * FROM subjects,student_subject WHERE student_subject.subject_id=subjects.id AND student_subject.student_id='$student_id'";
        $result = $conn->query($sql);
        return $result;
    }
    //استعلام عن طلبات اضافة الابناء
    function get_parent_order()
    {
        global $conn;
        $sql = "SELECT * FROM parent_order";
        return $conn->query($sql);
    }
    //استعلام عن ايديات الطلاب حسبة المرحلة الدراسية
    function get_student_by_acad($acad)
    {
        global $conn;
        $sql = "SELECT * FROM student_info,user_accounts WHERE student_info.id=user_accounts.id AND student_info.academic_year LIKE '%$acad%'";
        $result = $conn->query($sql);
        return $result;
    }
    //استعلام عن ولي امر الطالب حسب ايدي الطالب
    function get_parent_by_student_id($student_id)
    {
        global $conn;
        $sql = "SELECT * FROM user_accounts,parent_student WHERE parent_student.parent_id=user_accounts.id AND parent_student.student_id='$student_id'";
        $result = $conn->query($sql);
        return $result;
    }
    //استعلام عن المرحلة الدراسية للطالب من الايدي
    function get_student_acad($student_id)
    {
        global $conn;
        $sql = "SELECT * FROM student_info WHERE id='$student_id'";
        $result = $conn->query($sql);
        return $result->fetch_assoc();
    }
    if (isset($_POST['subject'])) {
        $selected_subject = $_POST['subject'];
        $teachers_subject = get_teachers_subject($selected_subject);
    }
    //عرض المحاضرات عن المدير
    if (isset($_POST['lecture_submit'])) {
        $teacher_id = $_POST['teacher'];
        $subject_id = $_POST['subject'];
        $lectures = get_lectures($teacher_id, $subject_id);
    }
    //قبول الطلبات
    if (isset($_GET['accept_order'])) {
        $type = $_GET['type'];
        $id = $_GET['accept_order'];
        $parent_id = $_GET['parent_id'];
        $student_id = $_GET['student_id'];
        if ($type == 'اضافة ابناء') {
            if ($student_id) {
                try {
                    $sql = "INSERT INTO parent_student (parent_id,student_id) VALUES ('$parent_id','$student_id')";
                    $conn->query($sql);
                    $sql = "DELETE FROM parent_order WHERE id='$id'";
                    $conn->query($sql);
                } catch (PDOException $e) {
                    echo $e;
                }
            } else {
                echo "<script>alert('تم حذف الطلب لأن حساب الطالب غير موجود')</script>";
                $sql = "DELETE FROM parent_order WHERE id='$id'";
                $conn->query($sql);
            }
        } else if ($type == "تقرير") {
            $date = date("Y-m-d");
            $sql = "INSERT INTO report_result ( id,student_id,date,parent_id ) VALUES ('$id','$student_id','$date','$parent_id')";
            $conn->query($sql);
            $sql = "DELETE FROM parent_order WHERE id='$id'";
            $conn->query($sql);
        }
        header("refresh:0.5;URL=admin_dashboard.php");
    }
    //حذف الطلب
    if (isset($_GET['delete_order'])) {
        $id = $_GET['delete_order'];
        $sql = "DELETE FROM parent_order WHERE id='$id'";
        $conn->query($sql);
        header("refresh:0.5;URL=admin_dashboard.php");
    }
    //ارسال ملاحظات
    if (isset($_POST["send_massege"])) {
        $massege = $_POST["massege"];
        $id = unique_id();
        $date = date("Y-m-d H:i:s");
        $sender_id = $_SESSION["user_id"];
        $recipient_id = $_POST["recipient"];
        $sql = "INSERT INTO masseges (id,sender_id,recipient_id,massege,date) VALUES ('$id','$sender_id','$recipient_id','$massege','$date')";
        $conn->query($sql);
        echo "<script>alert('تم ارسال الرسالة بنجاح')</script>";
        header("refresh:0.5;URL=admin_dashboard.php");
    }
    //اصدار الشهادات
    if (isset($_POST["set_certificates"])) {
        $min = $_POST["min-mark"];
        set_certificates($min);
        echo "<script>alert('تم اصدار الشهادات بنجاح')</script>";
        header("refresh:0.5;URL=admin_dashboard.php");
    }
    // استقبال بيانات الحساب للمدرس و اضافتها للقاعدة
    if (isset($_POST['teacher_submit'])) {
        $id = unique_id();
        $name = $_POST['teacher_name'];
        $email = $_POST['teacher_email'];
        $pass = $_POST['teacher_password'];
        $c_pass = $_POST['teacher_c-password'];
        $birth = $_POST['teacher_birth'];
        $image = $_FILES['teacher_photo']['name'];
        $image = filter_var($image, FILTER_SANITIZE_STRING);
        $ext = pathinfo($image, PATHINFO_EXTENSION);
        $rename = unique_id() . '.' . $ext;
        $image_size = $_FILES['teacher_photo']['size'];
        $image_tmp_name = $_FILES['teacher_photo']['tmp_name'];
        $image_folder = '../uploaded_files/' . $rename;
        if (!empty($image)) {
            if ($image_size < 2000000) {
                move_uploaded_file($image_tmp_name, $image_folder);
            }
        }
        $subjects_id = $_POST['teacher_subjects'];
        $status = 'active'; //مفعل
        $type = '2'; //مدرس
        $sql = "SELECT * FROM user_accounts WHERE email = '$email'";
        if ($conn->query($sql)->num_rows > 0) {
            echo "<script>alert('هذا الحساب موجود بالفعل!');</script>";
        } else {
            if ($pass != $c_pass) {
                echo "<script>alert('كلمة المرور غير متطابقة');</script>";
            } else {
                $pass_hash = password_hash($pass, PASSWORD_DEFAULT);
                $sql = "INSERT INTO user_accounts VALUES ('$id','$name','$email','$pass_hash','$image_folder','$status','$type','$birth')";
                $conn->query($sql);
                foreach ($subjects_id as $subject_id) {
                    $sql = "INSERT INTO teacher_subject VALUES ('$id','$subject_id','$name','')";
                    $conn->query($sql);
                }
                echo "<script>alert('تم اضافة المدرس بنجاح');</script>";
            }
        }
        header('refresh:0.5;URL=../admin/admin_dashboard.php');
    }
    //استقبال بيانات الحساب للطالب و اضافتها للقاعدة
    if (isset($_POST['student_submit'])) {
        $id = unique_id();
        $name = $_POST['student_name'];
        $email = $_POST['student_email'];
        $pass = $_POST['student_password'];
        $c_pass = $_POST['student_c-password'];
        $birth = $_POST['student_birth'];
        $image = $_FILES['student_photo']['name'];
        $image = filter_var($image, FILTER_SANITIZE_STRING);
        $ext = pathinfo($image, PATHINFO_EXTENSION);
        $rename = unique_id() . '.' . $ext;
        $image_size = $_FILES['student_photo']['size'];
        $image_tmp_name = $_FILES['student_photo']['tmp_name'];
        $image_folder = '../uploaded_files/' . $rename;
        if (!empty($image)) {
            if ($image_size < 2000000) {
                move_uploaded_file($image_tmp_name, $image_folder);
            }
        }
        $subject = $_POST['student_subject']; //باقي نضيف جدول للمادة
        $status = 'active'; //مفعل
        $type = '3'; //طالب
        $sql = "SELECT * FROM user_accounts WHERE email = '$email'";
        if ($conn->query($sql)->num_rows > 0) {
            echo "<script>alert('هذا الحساب موجود بالفعل!');</script>";
        } else {
            if ($pass != $c_pass) {
                echo "<script>alert('كلمة المرور غير متطابقة');</script>";
            } else {
                $pass_hash = password_hash($pass, PASSWORD_DEFAULT);
                $sql = "INSERT INTO user_accounts VALUES ('$id','$name','$email','$pass_hash','$folder_photo','$status','$type','$birth')";
                $conn->query($sql);
                echo "<script>alert('تم اضافة الطالب بنجاح');</script>";
            }
        }
        header('refresh:0.5;URL=../admin/admin_dashboard.php');
    }
    //استقبال بيانات المواد لاضافتها للقاعدة
    if (isset($_POST['subject_submit'])) {
        $id = unique_id();
        $name = $_POST['subject_name'];
        $academic_year = $_POST['subject_academic-year'];
        $sql = "INSERT INTO subjects VALUES('$id','$name','$academic_year')";
        if ($conn->query($sql) === TRUE) {
            echo "<script>alert('تم إضافة المادة بنجاح');</script>";
        } else {
            echo "<script>alert('هذه المادة موجودة بالفعل');</script>";
        }
        header('refresh:0.5;URL=../admin/admin_dashboard.php');
    }
    //حذف امتحان
    if (isset($_POST['delete_exam'])) {
        $exam_id = $_POST['exam_id'];
        $sql = "DELETE FROM exam_start WHERE id='$exam_id'";
        $conn->query($sql);
        $sql = "DELETE FROM student_exam WHERE exam_id='$exam_id'";
        $conn->query($sql);
        header("location:admin_dashboard.php");
    }
} ?>
<?php
//استعلام عن علامات النشاطات حسب ايدي المادة
function get_student_homewarke($student_id, $subject_id)
{
    global $conn;
    $sql = "SELECT * FROM upload_homewarke WHERE student_id='$student_id' AND subject_id='$subject_id'";
    $result = $conn->query($sql);
    $sum = 0;
    foreach ($result as $row) {
        $sum += $row["mark"];
    }

    $sql = "SELECT * FROM homewarke WHERE subject_id='$subject_id'";
    $result = $conn->query($sql);
    $count = $result->num_rows;
    if ($count > 0) {
        $avg = $sum / $count;
    } else {
        $avg = 0;
    }
    return floor($avg * 10) / 10;
}
//استعلام عن نتائج الامتحامانات
function get_student_exam($student_id, $subject_id)
{
    global $conn;
    $sql = "SELECT * FROM exam_result WHERE student_id='$student_id' AND subject_id='$subject_id'";
    $result = $conn->query($sql);
    $sum = 0;
    foreach ($result as $row) {
        $sum += $row["mark"];
    }

    $sql = "SELECT * FROM exam_start WHERE subject_id='$subject_id'";
    $result = $conn->query($sql);
    $count = $result->num_rows;

    if ($count > 0) {
        $avg = $sum / $count;
    } else {
        $avg = 0;
    }
    return floor($avg * 10) / 10;
}
//استعلام عن مواد الطالب من الايدي
function get_student_subject($student_id)
{
    global $conn;
    $sql = "SELECT * FROM subjects,student_subject WHERE student_subject.subject_id=subjects.id AND student_subject.student_id='$student_id'";
    $marks = array();
    $result = $conn->query($sql);
    foreach ($result as $row) {
        $marks['subject_name'][] = $row["name"];
        $marks['homewarke_avg'][] = get_student_homewarke($student_id, $row['id']);
        $marks['exam_avg'][] = get_student_exam($student_id, $row['id']);
    }
    return $marks;
}
//المجموع الكلي 
function get_total_avg($student_id)
{
    global $conn;
    $total_subject = count(get_student_subject($student_id)['subject_name']);
    $homewarke_avg = 0;
    foreach (get_student_subject($student_id)['homewarke_avg'] as $havg) {
        $homewarke_avg += $havg;
    }
    $exam_avg = 0;
    foreach (get_student_subject($student_id)['exam_avg'] as $eavg) {
        $exam_avg += $eavg;
    }

    $avg = (($homewarke_avg / $total_subject) + ($exam_avg / $total_subject)) / 2;

    return floor($avg * 10) / 10;
}
//اصدار شهادات للناجحين فقط
function set_certificates($min)
{
    global $conn;
    $id = unique_id();
    $date = date('Y-m-d');
    $status = 'success';
    $students = get_students();
    foreach ($students as $student) {
        $student_id = $student['id'];
        if (get_total_avg($student_id) >= $min) {
            $sql = "INSERT INTO certificates (id,student_id,status,date) VALUES ('$id','$student_id','$status','$date')";
            $conn->query($sql);
        }
    }
}
//الاستعلام عن كل اولياء الامور
function get_parents()
{
    global $conn;
    $sql = "SELECT* FROM user_accounts WHERE type=4";
    $result = $conn->query($sql);
    return $result;
}
//استعلام عن اسماء الابناء حسب ايدي ولي الامر
function get_student_parent($parent_id)
{
    global $conn;
    $sql = "SELECT * FROM user_accounts,parent_student WHERE parent_student.student_id=user_accounts.id AND parent_student.parent_id='$parent_id'";
    $result = $conn->query($sql);
    return $result;
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
    <script defer src="../js/script.js"></script> <!-- تحميل السكربت بعد تحميل DOM -->
</head>

<body>
    <!-- هيدر المدير -->
    <header class="header">
        <div class="logo-div">
            <a href="../admin/admin_dashboard.php" class="logo"><img src="../images/logo.png" alt="لوغو المنصة">إرادة</a>
        </div>
        <div class="search-div">
            <form action="#" method="post" class="search-form">
                <input type="search" name="search-input" placeholder="ابحث هنا..." required maxlength="100">
                <button type="submit" class="search-btn btn" name="search-btn">بحث</button>
            </form>
        </div>
        <div class="icons">
            <button id="toggle-btn" class="toggle-btn" onclick="darkmode(this)">🌙</button>
        </div>
    </header>

    <section class="section">
        <!-- القسم الخاص بحالات استخدام المدير-->
        <div class="side-bar">
            <div class="img-div">
                <img src="<?php echo $_SESSION['photo']; ?>" alt="صورة شخصية">
                <h2>المدير</h2>
                <p><?php echo $_SESSION['username']; ?></p>
            </div>
            <div class="btn-div">
                <a class="btn update-btn" href="../components/update.php">الملف الشخصي <span class="icon"><i class="fa-solid fa-address-card"></i></span></a>
                <a class="btn logout-btn" href="../components/logout.php">تسجيل الخروج <span class="icon"><i class="fa-solid fa-right-from-bracket"></i></span></a>
            </div>
            <div class="usecase">
                <a href="#" class="usecase-btn" onclick="show_usecase('usecase_1',this,'default_show_1')">المدرسين <span class="icon"><i class="fas fa-user-tie"></i></span></a>
                <a href="#" class="usecase-btn" onclick="show_usecase('usecase_2',this,'default_show_2')">الطلاب <span class="icon"><i class="fas fa-user-graduate"></i></span></a>
                <a href="#" class="usecase-btn" onclick="show_usecase('usecase_3',this,'default_show_3')">المواد <span class="icon"><i class="fa-solid fa-book"></i></span></a>
                <a href="#" class="usecase-btn" onclick="show_usecase('usecase_4',this,'default_show_4')">المحاضرات <span class="icon"><i class="fa-solid fa-book-open"></i></span></a>
                <a href="#" class="usecase-btn" onclick="show_usecase('usecase_5',this,'default_show_5')">الامتحانات <span class="icon"><i class="fa-solid fa-file-lines"></i></span></a>
                <a href="#" class="usecase-btn" onclick="show_usecase('usecase_6',this,'default_show_6')">اولياء الأمور <span class="icon"><i class="fas fa-user-friends"></i></span></a>
                <a href="#" class="usecase-btn" onclick="show_usecase('usecase_7',this,'default_show_7')">التقارير <span class="icon"><i class="fa-solid fa-file-contract"></i></span></a>
            </div>
        </div>
        <!-- ################################################################################################################### -->
        <!-- #################################################[ادارة المدرسين]################################################# -->
        <!-- ################################################################################################################### -->
        <div class="inside-div" id="usecase_1">
            <h1 class="title">إدارة المدرسين</h1>
            <div class="nav-bar">
                <button class="section-btn" id="default_show_1" onclick="show_div('statu_1',this)">عرض</button>
                <button class="section-btn" onclick="show_div('statu_2',this)">إضافة</button>
                <button class="section-btn" onclick="show_div('statu_3',this)">عرض الملاحظات</button>
                <button class="section-btn" onclick="show_div('statu_4',this)">ارسال ملاحظة</button>
            </div>
            <!-- ####################################################[عرض]###################################################### -->
            <?php if (isset($_POST['search_teacher'])) {
                $search = $_POST['search'];
                $sql = "SELECT * FROM user_accounts WHERE name LIKE '%$search%' AND type='2'";
                $result = $conn->query($sql);
            } ?>
            <div class="informations" id="statu_1">
                <div class="form-div" mithud>
                    <form action="" method="POST" enctype="multipart/form-data">
                        <div class="form-row">
                            <div class="form-group">
                                <label for="search">الاسم:</label>
                                <input type="search" name="search" id="search" value="<?php if (isset($search)) {
                                                                                            echo $search;
                                                                                        }  ?>">
                            </div>
                        </div>
                        <input type="submit" name="search_teacher" class="btn" value="بحث">
                    </form>
                </div>
                <div class="table-div">
                    <h1>جدول بيانات المدرسين</h1>
                    <table>
                        <thead>
                            <tr>
                                <th>الرقم</th>
                                <th>الاسم</th>
                                <th>الإيميل</th>
                                <th>تاريخ الميلاد</th>
                                <th>المواد</th>
                                <th>الحالة</th>
                                <th>الإجراءات</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $sql = "SELECT * FROM user_accounts WHERE type='2' ORDER  BY name ASC"; //امر الاستعلام عن كافة المدرسين
                            $rs = $conn->query($sql); //تخزين نتيجة الاستعلام بمتغير
                            if (isset($result) && $result) {
                                $rs = $result;
                            } ?>
                            <!-- التأكد من أن الاستعلام غير فارغ -->
                            <?php if ($rs->num_rows > 0) : ?>
                                <?php $index = 0; ?>
                                <!-- تحويل كل الاستعلامات لبيانات على شكل مصفوفة -->
                                <?php while ($row = $rs->fetch_assoc()) : ?>
                                    <tr><?php $index += 1; ?>
                                        <td><?= $index ?></td>
                                        <td><?= $row['name'] ?></td>
                                        <td><?= $row['email'] ?></td>
                                        <td><?= $row['birth'] ?></td>
                                        <td><?php foreach (get_subjects_teacher($row['id']) as $subject) {
                                                echo $subject['name'] . "<br>";
                                            } ?></td>
                                        <td><?= $row['status'] == 'active' ? 'مفعل' : 'محظور' ?></td>
                                        <td class='actions'>
                                            <a href='../components/edit_teacher.php?id=<?= $row['id'] ?>'><span class="green-icon"><i class="fa-solid fa-gear"></i></span></a>
                                            <a href='../components/delete_user.php?id=<?= $row['id'] ?>'><span class="red-icon"><i class="fa-solid fa-trash"></i></span></a>
                                            <a href="../components/block_unblock_user.php?id=<?= $row['id'] . "&status=" . $row['status'] ?>"><?= ($row['status'] == 'active' ? '<span class="orange-icon"><i class="fa-solid fa-lock-open"></i></span>' : '<span class="orange-icon"><i class="fa-solid fa-lock"></i></span>') ?></a>
                                        </td>
                                    </tr>
                                <?php endwhile ?>
                            <?php endif ?>
                        </tbody>
                    </table>
                </div>
            </div>
            <!-- ####################################################[اضافة]###################################################### -->
            <div class="informations" id="statu_2">
                <div class="form-div">
                    <form action="" method="POST" enctype="multipart/form-data">
                        <h1>إضافة مدرسين</h1>

                        <div class="form-row">
                            <div class="form-group">
                                <label for="name">الاسم:</label>
                                <input type="text" id="name" name="teacher_name" required>
                            </div>
                            <div class="form-group">
                                <label for="email">الإيميل:</label>
                                <input type="email" id="email" name="teacher_email" required>
                            </div>
                            <div class="form-group">
                                <label for="password">كلمة المرور:</label>
                                <input type="password" id="password" name="teacher_password" required>
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group">
                                <label for="c-password">تأكيد كلمة المرور:</label>
                                <input type="password" id="c-password" name="teacher_c-password" required>
                            </div>
                            <div class="form-group">
                                <label for="birthdate">تاريخ الميلاد:</label>
                                <input type="date" id="teacher_birth" name="teacher_birth" required>
                            </div>
                            <div class="form-group">
                                <label for="image">الصورة:</label>
                                <input type="file" id="photo" name="teacher_photo" accept="image/*" required>
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group check_input">
                                <label for="subject" class="title">مواد الابتدائي:</label>
                                <?php $sql = "SELECT * FROM subjects WHERE academic_year='primary-school'";
                                $rs = $conn->query($sql);
                                if ($rs) {
                                    while ($row = $rs->fetch_assoc()) {
                                        echo "<input type='checkbox' id='" . $row['name'] . "' name='teacher_subjects[]' value='" . $row['id'] . "'>
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
                                        echo "<input type='checkbox' id='" . $row['name'] . "' name='teacher_subjects[]' value='" . $row['id'] . "'>
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
                                        echo "<input type='checkbox' id='" . $row['name'] . "' name='teacher_subjects[]' value='" . $row['id'] . "'>
                                            <label for='" . $row['name'] . "'>" . $row['name'] . "</label><br>";
                                    }
                                } ?>
                            </div>
                        </div>
                        <button type="submit" name="teacher_submit">إضافة مدرس</button>
                    </form>
                </div>
            </div>
            <!-- ####################################################[عرض ملاحظات المدرسيين]###################################################### -->
            <div class="informations" id="statu_3">
                <div class="massege-div">
                    <?php foreach (get_teacher_masseges() as $massege): ?>
                        <div class="massege-row">
                            <div class="massege-group">
                                <div class="massege-info">
                                    <label for=""><?= $massege['name'] ?></label>
                                    <label for=""><?= $massege['date']  ?></label>
                                </div>
                                <div class="massege">
                                    <label for=""><?= $massege['massege'] ?></label>
                                </div>
                            </div>
                        </div>
                    <?php endforeach ?>
                </div>
            </div>
            <!-- ####################################################[ارسال ملاحظة لمدرس]###################################################### -->
            <div class="informations" id="statu_4">
                <div class="form-div">
                    <form action="" method="post">
                        <div class="form-row">
                            <div class="form-group">
                                <label for="">اكتب رسالة</label>
                                <input type="text" name="massege" placeholder="اكتب نص الرسالة...">
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group">
                                <label for="">المرسل اليه:</label>
                                <select name="recipient" id="">
                                    <?php foreach (get_teachers() as $teacher): ?>
                                        <option value="<?= $teacher['id'] ?>"><?= $teacher['name'] ?></option>
                                    <?php endforeach ?>
                                </select>
                            </div>
                        </div>
                        <input type="submit" name="send_massege" value="ارسال الرسالة" class="btn">
                    </form>
                </div>
            </div>
        </div>
        <!-- ################################################################################################################### -->
        <!-- #################################################[ادارة الطلاب]#################################################### -->
        <!-- ################################################################################################################### -->
        <div class="inside-div" id="usecase_2">
            <h1 class="title">إدارة الطلاب</h1>
            <div class="nav-bar">
                <button class="section-btn" id="default_show_2" onclick="show_div('statu_5',this)">عرض</button>
            </div>
            <!-- ####################################################[عرض]###################################################### -->
            <div class="informations" id="statu_5">
                <div class="table-div">
                    <h1>جدول بيانات الطلاب</h1>
                    <table>
                        <thead>
                            <tr>
                                <th>الرقم</th>
                                <th>الاسم</th>
                                <th>الإيميل</th>
                                <th>تاريخ الميلاد</th>
                                <th>المادة</th>
                                <th>الحالة</th>
                                <th>الإجراءات</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $sql = "SELECT * FROM user_accounts WHERE type='3'"; //امر الاستعلام عن كافة الطلاب
                            //تخزين نتيجة الاستعلام بمتغير
                            $rs = $conn->query($sql); ?>
                            <!-- //التأكد من أن الاستعلام غير فارغ -->
                            <?php if ($rs->num_rows > 0) : ?>
                                <?php $index = 0; ?>
                                <!-- //تحويل كل الاستعلامات لبيانات على شكل مصفوفة -->
                                <?php while ($row = $rs->fetch_assoc()) : ?>
                                    <tr><?php $index += 1; ?>
                                        <td><?= $index ?></td>
                                        <td><?= $row['name'] ?></td>
                                        <td><?= $row['email'] ?></td>
                                        <td><?= $row['birth'] ?></td>
                                        <td><?php foreach (get_subject_student_by_id($row['id']) as $subject) {
                                                echo $subject['name'] . "<br>";
                                            } ?></td>
                                        <td><?= $row['status'] == 'active' ? 'مفعل' : 'محظور' ?></td>
                                        <td class='actions'>
                                            <a href='../components/delete_user.php?id=<?= $row['id'] ?>'><span class="red-icon"><i class="fa-solid fa-trash"></i></span></a>
                                            <a href='../components/block_unblock_user.php?id=<?= $row['id'] . "&status=" . $row['status'] ?>'><?= ($row['status'] == 'active' ? "<span class='orange-icon'><i class='fa-solid fa-lock-open'></i></span>" : "<span class='orange-icon'><i class='fa-solid fa-lock'></i></span>") ?></a>
                                        </td>
                                    </tr>
                                <?php endwhile ?>
                            <?php endif ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <!-- ################################################################################################################### -->
        <!-- #################################################[ادارة المواد]################################################### -->
        <!-- ################################################################################################################### -->
        <div class="inside-div" id="usecase_3">
            <h1 class="title">إدارة المواد</h1>
            <div class="nav-bar">
                <button class="section-btn" id="default_show_3" onclick="show_div('statu_6',this)">عرض</button>
                <button class="section-btn" onclick="show_div('statu_7',this)">إضافة</button>
            </div>
            <!-- ####################################################[عرض]###################################################### -->
            <div class="informations" id="statu_6">
                <div class="table-div">
                    <h1>جدول بيانات المواد</h1>
                    <table>
                        <thead>
                            <tr>
                                <th>الرقم</th>
                                <th>الاسم</th>
                                <th>السنة الدراسية</th>
                                <th>الإجراءات</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $sql = "SELECT * FROM subjects ORDER BY academic_year"; //امر الاستعلام عن كافة المواد
                            //تخزين نتيجة الاستعلام بمتغير
                            $rs = $conn->query($sql); ?>
                            <!-- //التأكد من أن الاستعلام غير فارغ -->
                            <?php if ($rs->num_rows > 0) : ?>
                                <?php $index = 0; ?>
                                <!-- //تحويل كل الاستعلامات لبيانات على شكل مصفوفة -->
                                <?php while ($row = $rs->fetch_assoc()) : ?>
                                    <tr><?php $index += 1; ?>
                                        <td><?= $index ?></td>
                                        <td><?= $row['name'] ?></td>
                                        <td><?= ($row['academic_year'] == 'high-school' ? "المرحلة الثانوية" : ($row['academic_year'] == 'primary-school' ? 'المرحلة الابتدائية' : 'المرعلة الاعدادية')) ?></td>
                                        <td class='actions'>
                                            <a href='../components/edit_subject.php?id=<?= $row['id'] ?>'><span class="green-icon"><i class="fa-solid fa-gear"></i></span></a>
                                            <a href='../components/delete_subject.php?id=<?= $row['id'] ?>'><span class="red-icon"><i class="fa-solid fa-trash"></i></span></a>
                                        </td>
                                    </tr>
                                <?php endwhile ?>
                            <?php endif ?>
                        </tbody>
                    </table>
                </div>
            </div>
            <!-- ####################################################[اضافة]###################################################### -->
            <div class="informations" id="statu_7">
                <div class="form-div">
                    <form action="" method="POST" enctype="multipart/form-data">
                        <h1>إضافة المواد</h1>
                        <div class="form-row">
                            <div class="form-group">
                                <label for="name">الاسم:</label>
                                <input type="text" id="name" name="subject_name" required>
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group" style="flex: 1 1 33%;">
                                <label for="subject">المادة:</label>
                                <select id="subject" name="subject_academic-year" required>
                                    <option value="">اختر السنة الدراسية</option>
                                    <option value="primary-school">الابتدائي</option>
                                    <option value="middle-school">الاعدادي</option>
                                    <option value="high-school">الثانوي</option>
                                </select>
                            </div>
                        </div>
                        <button type="submit" name="subject_submit">إضافة مادة</button>
                    </form>
                </div>
            </div>
        </div>
        <!-- ################################################################################################################### -->
        <!-- #################################################[ادارة المحاضرات]################################################ -->
        <!-- ################################################################################################################### -->
        <div class="inside-div" id="usecase_4">
            <h1 class="title">إدارة المحاضرات</h1>
            <div class="nav-bar">
                <button class="section-btn" id="default_show_4" onclick="show_div('statu_8',this)">عرض</button>
            </div>
            <!-- ####################################################[عرض]###################################################### -->
            <div class="informations" id="statu_8">
                <div class="form-div">
                    <form action="" method="post">
                        <div class="form-row">
                            <div class="form-group">
                                <select name="subject" id="" onchange="this.form.submit()">
                                    <option value="">--اختر المادة--</option>
                                    <?php foreach (get_subjects() as $subject) { ?>
                                        <option value="<?= $subject['id'] ?>" <?= (isset($selected_subject) && $selected_subject == $subject['id'] ? ' selected' : ''); ?>><?= $subject['name'] ?></option>
                                    <?php } ?>
                                </select>
                            </div>
                            <div class="form-group">
                                <select name="teacher" id="">
                                    <option value="">--اختر المدرس--</option>
                                    <?php if (isset($teachers_subject)) : ?>
                                        <?php foreach ($teachers_subject as $teacher) : ?>
                                            <option value="<?= $teacher['id'] ?>"><?= $teacher['name'] ?></option>
                                        <?php endforeach ?>
                                    <?php endif  ?>
                                </select>
                            </div>
                        </div>
                        <button type="submit" name='lecture_submit'>استعلام</button>
                    </form>
                </div>
                <div class="card-container">
                    <?php if (isset($lectures)) : ?>
                        <?php foreach ($lectures as $lecture) : ?>
                            <div class='card-div'>
                                <div class='card-row'>
                                    <div class='card-img'>
                                        <img src="<?= $lecture['photo'] ?> ">
                                    </div>
                                </div>
                                <div class='card-row'>
                                    <div class='card-text'>
                                        <h2> <?= $lecture['title'] ?> </h2>
                                        <p><?= $lecture['description'] ?> </p>
                                    </div>
                                </div>
                                <div class='card-row'>
                                    <div class='card-option'>
                                        <a href='../components/view_video.php?&id=<?= $lecture['id'] ?>' class='show-btn'>عرض <i class="fa-solid fa-eye"></i></a> <!---->
                                        <a href='../components/delete_lecture.php?&id=<?= $lecture['id'] ?>' class='delete-btn'>حذف <i class="fa-solid fa-trash"></i></a><!---->
                                    </div>
                                </div>
                            </div>
                        <?php endforeach ?>
                    <?php endif ?>
                </div>
            </div>
        </div>
        <!-- ################################################################################################################### -->
        <!-- #################################################[ادارة الامتحانات]################################################ -->
        <!-- ################################################################################################################### -->
        <div class="inside-div" id="usecase_5">
            <h1 class="title">إدارة الامتحانات</h1>
            <div class="nav-bar">
                <button class="section-btn" id="default_show_5" onclick="show_div('statu_9',this)">عرض</button>
            </div>
            <!-- ####################################################[عرض]###################################################### -->
            <div class="informations" id="statu_9">
                <div class="table-div">
                    <h1>جدول بيانات الامتحانات</h1>
                    <table>
                        <thead>
                            <tr>
                                <th>الرقم</th>
                                <th>المادة</th>
                                <th>المدرس</th>
                                <th>عدد الاسئلة</th>
                                <th>المدة</th>
                                <th>التاريخ</th>
                                <th>الاجراءات</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach (get_exams() as $index => $exam): ?>
                                <tr>
                                    <td><?= $index + 1 ?></td>
                                    <td><?= $exam['subject_name'] ?></td>
                                    <td><?= $exam['teacher_name'] ?></td>
                                    <td><?= $exam['count'] ?></td>
                                    <td><?= $exam['time'] ?></td>
                                    <td><?= $exam['date'] ?></td>
                                    <td>
                                        <form action="" method="post">
                                            <input type="hidden" name="exam_id" value="<?= $exam['id'] ?>">
                                            <button type="submit" name="delete_exam"><span class="red-icon"><i class="fa-solid fa-trash"></i></span></button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endforeach ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <!-- ################################################################################################################### -->
        <!-- #################################################[ادارة اولياء الامور]############################################# -->
        <!-- ################################################################################################################### -->
        <div class="inside-div" id="usecase_6">
            <h1 class="title">الطلبات</h1>
            <div class="nav-bar">
                <button class="section-btn" id="default_show_6" onclick="show_div('statu_10',this)">عرض أولياء الامور</button>
                <button class="section-btn" onclick="show_div('statu_11',this)">عرض الطلبات</button>
                <button class="section-btn" onclick="show_div('statu_12',this)">الملاحظات</button>
                <button class="section-btn" onclick="show_div('statu_13',this)">ارسال ملاحظة</button>
            </div>
            <!-- ###############################################[عرض اولياء الامور]################################################ -->
            <div class="informations" id="statu_10">
                <table>
                    <thead>
                        <tr>
                            <th>الرقم</th>
                            <th>الاسم</th>
                            <th>البريد الالكتروني</th>
                            <th>الأبناء</th>
                            <th>الاجراءات</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach (get_parents() as $index => $parent): ?>
                            <tr>
                                <td><?= $index + 1 ?></td>
                                <td><?= $parent['name'] ?></td>
                                <td><?= $parent['email'] ?></td>
                                <td><?php foreach (get_student_parent($parent['id']) as $student) {
                                        echo $student['name'] . "<br>";
                                    } ?></td>
                                <td class='actions'>
                                    <a href='../components/delete_user.php?id=<?= $parent['id'] ?>'><span class="red-icon"><i class="fa-solid fa-trash"></i></span></a>
                                    <a href='../components/block_unblock_user.php?id=<?= $parent['id'] . "&status=" . $parent['status'] ?>'><?= ($parent['status'] == 'active' ? '<span class="orange-icon"><i class="fa-solid fa-lock-open"></i></span>' : '<span class="orange-icon"><i class="fa-solid fa-lock"></i></span>') ?></a>
                                </td>
                            </tr>
                        <?php endforeach ?>
                    </tbody>
                </table>
            </div>
            <!-- ####################################################[عرض الطلبات]#################################################### -->
            <div class="informations" id="statu_11">
                <table>
                    <thead>
                        <tr>
                            <th>الرقم</th>
                            <th>اسم ولي الامر</th>
                            <th>اسم الطالب</th>
                            <th>نوع الطلب</th>
                            <th>الاجراءات</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach (get_parent_order() as $index => $order): ?>
                            <tr>
                                <td><?= $index + 1 ?></td>
                                <td><?= get_user($order['parent_id'])['name'] ?></td>
                                <td><?php if (get_user_email($order['student_email']) != FALSE) {
                                        echo get_user_email($order['student_email'])['name'];
                                    } else {
                                        echo "تم اضافة حساب غير موجود";
                                    }
                                    ?></td>
                                <td><?= $order['type'] ?></td>
                                <td>
                                    <a href='admin_dashboard.php?accept_order=<?= $order['id'] ?>&parent_id=<?= $order['parent_id'] ?>&student_id=<?= get_user_email($order['student_email'])['id'] ?>&type=<?= $order['type'] ?> ' class="edit-btn">قبول</a><!---->
                                    <a href='admin_dashboard.php?delete_order=<?= $order['id'] ?>' class="delete-btn">رفض</a><!---->
                                </td>
                            </tr>
                        <?php endforeach ?>
                    </tbody>
                </table>
            </div>
            <!-- ####################################################[عرض الملاحظات]################################################### -->
            <div class="informations" id="statu_12">
                <div class="massege-div">
                    <?php foreach (get_parent_masseges() as $massege): ?>
                        <div class="massege-row">
                            <div class="massege-group">
                                <div class="massege-info">
                                    <label for=""><?= $massege['name'] ?></label>
                                    <label for=""><?= $massege['date']  ?></label>
                                </div>
                                <div class="massege">
                                    <label for=""><?= $massege['massege'] ?></label>
                                </div>
                            </div>
                        </div>
                    <?php endforeach ?>
                </div>
            </div>
            <!-- ####################################################[ارسال ملاحظة]###################################################### -->
            <div class="informations" id="statu_13">
                <div class="form-div">
                    <form action="" method="post">
                        <div class="form-row">
                            <div class="form-group">
                                <label for="">اكتب ملاحظة</label>
                                <input type="text" name="massege" placeholder="اكتب نص الرسالة...">
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group">
                                <label for="">المرسل اليه:</label>
                                <select name="recipient" id="">
                                    <?php $sql = 'SELECT * FROM user_accounts WHERE type = 4 ';
                                    $result = $conn->query($sql);
                                    ?>
                                    <?php foreach ($result as $admin): ?>
                                        <option value="<?= $admin['id'] ?>"><?= $admin['name'] ?></option>
                                    <?php endforeach ?>
                                </select>
                            </div>
                        </div>
                        <input type="submit" name="send_massege" value="ارسال الملاحظة" class="btn">
                    </form>
                </div>
            </div>
        </div>
        <!-- ################################################################################################################### -->
        <!-- #################################################[ادارة التقارير]################################################# -->
        <!-- ################################################################################################################### -->
        <div class="inside-div" id="usecase_7">
            <h1 class="title">إدارة التقارير</h1>
            <div class="nav-bar">
                <button class="section-btn" id="default_show_7" onclick="show_div('statu_14',this)">المدرسين</button>
                <button class="section-btn" onclick="show_div('statu_15',this)">الطلاب</button>
                <button class="section-btn" onclick="show_div('statu_16',this)">الشهادات</button>
            </div>
            <!-- ##################################################[المدرسيين]################################################### -->
            <div class="informations" id="statu_14">
                <div class="form-div">
                    <form action="../components/report_form2.php" method="post">
                        <div class="form-row">
                            <div class="form-group">
                                <label for="">المدرس:</label>
                                <select name="teacher" id="">
                                    <?php foreach (get_teachers() as $teacher): ?>
                                        <option value="<?= $teacher['id'] ?>"><?= $teacher['name'] ?></option>
                                    <?php endforeach ?>
                                </select>
                            </div>
                        </div>
                        <input type="submit" name="teacher_report" value="إنشاء تقرير" class="btn">
                    </form>
                </div>
            </div>
            <!-- ####################################################[الطلاب]###################################################### -->
            <div class="informations" id="statu_15">
                <div class="form-div">
                    <form action="../components/report_form.php" method="post">
                        <div class="form-row">
                            <div class="form-group">
                                <label for="">الطالب:</label>
                                <select name="student" id="">
                                    <?php foreach (get_students() as $student): ?>
                                        <option value="<?= $student['id'] ?>"><?= $student['name'] ?></option>
                                    <?php endforeach ?>
                                </select>
                            </div>
                        </div>
                        <input type="submit" name="student_report" value="إنشاء تقرير" class="btn">
                    </form>
                </div>
            </div>
            <!-- ####################################################[الشهادات]###################################################### -->
            <div class="informations" id="statu_16">
                <div class="form-div">
                    <form action="" method="post">
                        <div class="form-row">
                            <div class="form-group">
                                <label for="">المرحلة الدراسية:</label>
                                <select name="acad" id="">
                                    <option value="">--اختر المرحلة الدراسية--</option>
                                    <?php foreach (get_acad() as $acad): ?>
                                        <option value="<?= $acad['academic_year'] ?>"><?= ($acad['academic_year'] == 'middle-school' ? 'المرحلة الاعدادية' : ($acad['academic_year'] == 'high-school' ? 'المرحلة الثانوية' : 'المرحلة الابتدائية')) ?></option>
                                    <?php endforeach ?>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="">الحد الأدنى للعلامات:</label>
                                <input type="text" name="min_mark">
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group">
                                <input type="submit" class="btn" name="select_acad" value="عرض">
                            </div>
                            <div class="form-group">
                                <input type="submit" name="set_certificates" class="btn block" value="إصدار الشهادات">
                            </div>
                        </div>
                    </form>
                </div>
                <table>
                    <thead>
                        <tr>
                            <th>الترتيب</th>
                            <th>الاسم</th>
                            <th>اسم ولي الامر</th>
                            <th>السنة الدراسية</th>
                            <th>المعدل</th>
                            <th>اجراءات</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (isset($_POST['select_acad'])) {
                            $acad = $_POST['acad'];
                            $min = $_POST['min_mark'];
                        } else {
                            $acad = '-';
                            $min = 0;
                        } ?>
                        <?php foreach (get_student_by_acad($acad) as $index => $student_id): ?>
                            <?php if (get_total_avg($student_id['id']) >= $min) : ?>
                                <tr>
                                    <td><?= $index + 1 ?></td>
                                    <td><?= get_user($student_id['id'])['name'] ?></td>
                                    <td><?php foreach (get_parent_by_student_id($student_id['id']) as $parent) {
                                            echo $parent['name'] . "<br>";
                                        } ?></td>
                                    <td><?= (get_student_acad($student_id['id'])['academic_year'] == 'high-school' ? 'المرحلة الثانوية' : (get_student_acad($student_id['id'])['academic_year'] == 'middle-school' ? 'المرحلة الاعدادية' : 'المرحلة الابتدائية')) ?></td>

                                    <td><?php echo get_total_avg($student_id['id']) . " %"; ?></td>

                                    <td><a href="../components/shahade.php?student_id=<?= $student_id['id'] ?>"><span class="green-icon"><i class="fa-solid fa-eye"></i></span></a></td>
                                </tr>
                            <?php endif ?>
                        <?php endforeach ?>
                    </tbody>
                </table>
            </div>
        </div>
    </section>



    <button class="floating-btn" onclick="show_side_bar()">get</button>


    <!-- الفوتر -->
    <?php include '../components/footer.php'; ?>

    <script src="../js/script.js"></script>
</body>

</html>