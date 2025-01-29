<?php include "../components/connect.php" ?>
<?php session_start();
if (!isset($_SESSION['username']) || ($_SESSION['type'] != 4))
    header("location:../index.php");
?>
<?php
//استعلام عن مدرسين الطلاب تعييد ايدي المدرسيين
function get_teachers_ids()
{
    global $conn;
    $students = get_students();
    $teachers = array();
    foreach ($students as $student) {
        $student_id = $student['id'];
        $sql = "SELECT * FROM student_subject,teacher_subject WHERE student_subject.subject_id=teacher_subject.subject_id AND student_subject.student_id='$student_id'";
        $result = $conn->query($sql);
        while ($row = $result->fetch_assoc()) {
            $teachers[] = $row;
        }
    }
    return $teachers;
}
//احضار ايديات الابناء
function get_students_id()
{
    global $conn;
    $sql = "SELECT * FROM parent_student WHERE parent_id='{$_SESSION['user_id']}'";
    $result = $conn->query($sql);
    return $result;
}
//استعلام عن الابناء من خلال الايدي
function get_student($student_id)
{
    global $conn;
    $sql = "SELECT * FROM user_accounts WHERE id='$student_id'";
    return $conn->query($sql)->fetch_assoc();
}
//استعلام عن بيانات الحساب للابناء
function get_students()
{
    global $conn;
    $student_ids = get_students_id();
    $students = [];
    foreach ($student_ids as $student_id) {
        $id = $student_id['student_id'];
        $sql = "SELECT * FROM user_accounts where id='$id'";
        $students[] = $conn->query($sql)->fetch_assoc();
    }
    return $students;
}
//استعلام عن السنة الدراسية حسب ايدي الطالب
function get_acad($student_id)
{
    global $conn;
    $sql = "SELECT DISTINCT subjects.academic_year  FROM student_subject,subjects WHERE subjects.id=student_subject.subject_id AND student_subject.student_id='$student_id' ";
    $result = $conn->query($sql)->fetch_assoc();
    return $result;
}
//استعلام عن مواد الطالب  حسب ايدي الطالب
function get_subjects($student_id)
{
    global $conn;

    // إعداد الاستعلام باستخدام المعاملات المحضرة
    $stmt = $conn->prepare("
        SELECT subjects.*
        FROM student_subject
        INNER JOIN subjects ON subjects.id = student_subject.subject_id
        WHERE student_subject.student_id = ?
    ");

    // ربط المعاملات
    $stmt->bind_param("s", $student_id);

    // تنفيذ الاستعلام
    $stmt->execute();

    // الحصول على النتيجة
    $result = $stmt->get_result();

    // استخراج البيانات كأريج من المصفوفات
    $subjects = [];
    while ($row = $result->fetch_assoc()) {
        $subjects[] = $row;
    }

    // إغلاق البيان
    $stmt->close();

    return $subjects; // يعيد الأريج الذي يحتوي على المواد الدراسية
}

//استعلام عن واجبات الابناء جسب ايدي الطالب
function get_homework($student_id)
{
    global $conn;

    // إعداد الاستعلام باستخدام المعاملات المحضرة
    $stmt = $conn->prepare("
        SELECT homewarke.*
        FROM student_homewarke
        INNER JOIN homewarke ON student_homewarke.homewarke_id = homewarke.id
        WHERE student_homewarke.student_id = ?");

    // ربط المعاملات
    $stmt->bind_param("s", $student_id);

    // تنفيذ الاستعلام
    $stmt->execute();

    // الحصول على النتيجة
    $result = $stmt->get_result();

    // استخراج البيانات كأريج من المصفوفات
    $homeworks = [];
    while ($row = $result->fetch_assoc()) {
        $homeworks[] = $row;
    }

    // إغلاق البيان
    $stmt->close();

    return $homeworks; // يعيد الأريج الذي يحتوي على المهام المنزلية
}
//دالة لاحضار معلومات المادة حسب الايدي الخاص بالمادة
//تعرض مباشرة
function get_subject_info($subject_id)
{
    global $conn;

    $sql = "SELECT * FROM subjects WHERE id='$subject_id'";
    $result = $conn->query($sql);
    return $result->fetch_assoc();
}
//استعلام عن امتحانات الابناء حسب ايدي الابن
function get_exam($student_id)
{
    global $conn;

    // إعداد الاستعلام باستخدام المعاملات المحضرة
    $stmt = $conn->prepare("
        SELECT exam_start.*
        FROM student_exam
        INNER JOIN exam_start ON student_exam.exam_id = exam_start.id
        WHERE student_exam.student_id = ?");

    // ربط المعاملات
    $stmt->bind_param("s", $student_id);

    // تنفيذ الاستعلام
    $stmt->execute();

    // الحصول على النتيجة
    $result = $stmt->get_result();

    // استخراج البيانات كأريج من المصفوفات
    $homeworks = [];
    while ($row = $result->fetch_assoc()) {
        $homeworks[] = $row;
    }

    // إغلاق البيان
    $stmt->close();

    return $homeworks; // يعيد الأريج الذي يحتوي على المهام المنزلية
}
//دالة الاستعلام عن كافة المدرسين حسب المادة المحددة بالايدي
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
//استعلام عن المحاضرات حسب ايدي المادة
function get_lectures($subject_id)
{
    global $conn;
    $sql = "SELECT * FROM lectures WHERE subject_id='$subject_id'";
    return $conn->query($sql);
}
//استعلام عن علامات الطالب حسب ايدي الطالب بامتحان معين
function get_exam_result($student_id, $exam_id)
{
    global $conn;
    $sql = "SELECT * FROM exam_result WHERE student_id='$student_id' AND `exam_id` = '$exam_id'";
    $result = $conn->query($sql);
    return $result->fetch_assoc();
}
//استعلام عن علامات الطالب حسب ايدي الطالب بنشاط معين
function get_homewarke_result($student_id, $homewarke_id)
{
    global $conn;
    $sql = "SELECT * FROM upload_homewarke WHERE student_id='$student_id' AND homewarke_id='$homewarke_id'";
    $result = $conn->query($sql);
    return $result->fetch_assoc();
}
//استعلام عن الامتحانات حسب المادة
function get_exam_by_subject_id($subject_id)
{
    global $conn;
    $sql = "SELECT * FROM exam_start WHERE subject_id='$subject_id'";
    $result = $conn->query($sql);
    return $result;
}
//استعلام عن الانشطة حسب المادة
function get_homewarke_by_subject_id($subject_id)
{
    global $conn;
    $sql = "SELECT * FROM homewarke WHERE subject_id='$subject_id'";
    $result = $conn->query($sql);
    return $result;
}
//استعلام عن المحاضرات حسب ايدي الطالب
function get_student_lectures($student_id)
{
    global $conn;
    $sql = "SELECT * from student_lecture,lectures WHERE student_lecture.lecture_id=lectures.id AND student_lecture.student_id='$student_id'";
    $result = $conn->query($sql);
    return $result;
}
//الاستعلام عن المدرس حسب المحاضرة
function get_teacher_by_lecture_id($lecture_id)
{
    global $conn;
    $sql = "SELECT * FROM user_accounts,lectures WHERE user_accounts.id=lectures.teacher_id AND lectures.id='lecture_id'";
    $result = $conn->query($sql);
    return $result->fetch_assoc();
}
//استعلام عن الملاحظات
function get_masseges()
{
    global $conn;
    $sql = "SELECT * FROM masseges WHERE recipient_id='{$_SESSION['user_id']}'";
    $result = $conn->query($sql);
    return $result;
}
//استعلام عن التقارير المقبولة
function get_report()
{
    global $conn;
    $sql = "SELECT * FROM report_result WHERE parent_id='{$_SESSION['user_id']}'";
    $result = $conn->query($sql);
    return $result;
}
//احضار الشهادات المعروضة عن كل الابناء
function get_certificates()
{
    global $conn;
    $students = get_students_id();
    $certificates = [];
    foreach ($students as $student) {
        $student_id = $student['student_id'];
        $sql = "SELECT * FROM certificates WHERE student_id='$student_id' AND status='success'";
        $result = $conn->query($sql);
        if ($result->num_rows > 0) {
            $certificates[] = $result->fetch_assoc();
        }
    }
    return $certificates;
}
?>
<?php
//ارسال طلب اضافة طالب لولي الامر
if (isset($_POST['report_student'])) {
    $status = $_POST['status'];
    $email = $_POST['email'];
    $id = unique_id();
    $sql = "INSERT INTO parent_order (id,student_email,parent_id,type) VALUES ('$id','$email','{$_SESSION['user_id']}','$status')";
    $conn->query($sql);
    header("refresh:0.5;URL=parent_dashboard.php");
    echo "<script>alert('تم ارسال الطلب الى المدير')</script>";
}
//ارسال الرسالة
if (isset($_POST["send_massege"])) {
    $massege = $_POST["massege"];
    $id = unique_id();
    $date = date("Y-m-d H:i:s");
    $sender_id = $_SESSION["user_id"];
    $recipient_id = $_POST["recipient"];
    $sql = "INSERT INTO masseges (id,sender_id,recipient_id,massege,date) VALUES ('$id','$sender_id','$recipient_id','$massege','$date')";
    $conn->query($sql);
    echo "<script>alert('تم ارسال الرسالة بنجاح')</script>";
    header("refresh:0.5;URL=parent_dashboard.php");
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>parent</title>
    <link rel="stylesheet" href="../css/style.css">
           <!-- font awesome cdn link  -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css">
    <script defer src="../js/script.js"></script> <!-- تحميل السكربت بعد تحميل DOM -->
</head>

<body>
    <header class="header">
        <div class="logo-div">
            <a href="../parent/parent_dashboard.php" class="logo"><img src="../images/logo.png"
                    alt="لوغو المنصة">إرادة</a>
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
        <!-- القسم الخاص بحالات استخدام ولي الامر-->
        <div class="side-bar">
            <div class="img-div">
                <img src="<?php echo $_SESSION['photo']; ?>" alt="صورة شخصية">
                <h2>ولي أمر</h2>
                <p><?php echo $_SESSION['username']; ?></p>
            </div>
            <div class="btn-div">
                <a class="btn update-btn" href="../components/update.php">الملف الشخصي <span class="icon"><i class="fa-solid fa-address-card"></i></span></a>
                <a class="btn logout-btn" href="../components/logout.php">تسجيل الخروج <span class="icon"><i class="fa-solid fa-right-from-bracket"></i></span></a>
            </div>
            <div class="usecase">
                <a href="#" class="usecase-btn" onclick="show_usecase('usecase_1',this,'default_show_1')">الأبناء <span class="icon"><i class="fa-solid fa-user-graduate"></i></span></a>
                <a href="#" class="usecase-btn" onclick="show_usecase('usecase_2',this,'default_show_2')">الأنشطة <span class="icon"><i class="fa-solid fa-file-invoice"></i></span></a>
                <a href="#" class="usecase-btn" onclick="show_usecase('usecase_3',this,'default_show_3')">الامتحانات <span class="icon"><i class="fa-solid fa-file-lines"></i></span></a>
                <a href="#" class="usecase-btn" onclick="show_usecase('usecase_4',this,'default_show_4')">المواد <span class="icon"><i class="fa-solid fa-book"></i></span></a>
                <a href="#" class="usecase-btn" onclick="show_usecase('usecase_5',this,'default_show_5')">الملاحظات <span class="icon"><i class="fa-solid fa-envelope-open-text"></i></span></a>
                <a href="#" class="usecase-btn" onclick="show_usecase('usecase_6',this,'default_show_6')">التقارير <span class="icon"><i class="fa-solid fa-file-contract"></i></span></a>
            </div>
        </div>
        <!-- ################################################################################################################### -->
        <!-- ###################################################[عرض الابناء]################################################### -->
        <!-- ################################################################################################################### -->
        <div class="inside-div" id="usecase_1">
            <h1 class="title">الابناء المسجلين</h1>
            <div class="nav-bar">
                <button class="section-btn" id="default_show_1" onclick="show_div('statu_1',this)">عرض</button>
                <button class="section-btn" onclick="show_div('statu_2',this)">إضافة</button>
            </div>
            <!-- ####################################################[عرض]###################################################### -->
            <div class="informations" id="statu_1">
                <h1>بيانات الابناء</h1>
                <table>
                    <thead>
                        <tr>
                            <th>الرقم</th>
                            <th>الاسم</th>
                            <th>البريد الالكتروني</th>
                            <th>تاريخ الميلاد</th>
                            <th>السنة الدراسية</th>
                            <th>المواد الدراسية</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach (get_students() as $index => $student): ?>
                            <tr>
                                <td><?= $index + 1 ?></td>
                                <td><?= $student['name'] ?></td>
                                <td><?= $student['email'] ?></td>
                                <td><?= $student['birth'] ?></td>
                                <td><?= get_acad($student['id'])['academic_year'] ?></td>
                                <td><?php foreach (get_subjects($student['id']) as $key => $value) {
                                        echo $value['name'] . "<br>";
                                    } ?></td>
                            </tr>
                        <?php endforeach ?>
                    </tbody>
                </table>
            </div>
            <!-- ####################################################[اضافة]###################################################### -->
            <div class="informations" id="statu_2">
                <h1>اضافة ابناء</h1>
                <div class="form-div">
                    <form action="" method="post">
                        <div class="form-row">
                            <div class="form-group">
                                <label for="">البريد الالكتروني: <span>*</span></label>
                                <input type="email" name="email" placeholder="اكتب البريد الالكتروني الخاص بالطالب...">
                            </div>
                        </div>
                        <input type="submit" name="report_student" value="ارسال طلب الاضافة" class="btn">
                        <input type="hidden" name="status" value="اضافة ابناء">
                    </form>
                </div>
            </div>
        </div>
        <!-- ################################################################################################################### -->
        <!-- ###################################################[الانشطة]####################################################### -->
        <!-- ################################################################################################################### -->
        <div class="inside-div" id="usecase_2">
            <h1 class="title">نشاطات الابناء</h1>
            <div class="nav-bar">
                <button class="section-btn" id="default_show_2" onclick="show_div('statu_3',this)">عرض</button>
                <button class="section-btn" onclick="show_div('statu_4',this)">نتائج النشاطات</button>
            </div>
            <!-- ####################################################[عرض]###################################################### -->
            <div class="informations" id="statu_3">
                <table>
                    <thead>
                        <tr>
                            <th>الرقم</th>
                            <th>الاسم</th>
                            <th>المادة</th>
                            <th>المدرسين</th>
                            <th>عدد الانشطة</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- الفور الاولى تمر على الايديات الخاصة بالابناء -->
                        <?php foreach (get_students_id() as $student): ?>
                            <!-- الفور الثانية تمر علة جميع مواد كل ابن -->
                            <?php foreach (get_subjects($student['student_id']) as $index => $subject): ?>
                                <tr>
                                    <td><?= $index + 1 ?></td>
                                    <td><?= get_student($student['student_id'])['name'] ?></td>
                                    <td><?= get_subject_info($subject['id'])['name'] ?></td>
                                    <td><?php foreach (get_teachers_subject($subject['id']) as $key => $teacher) {
                                            echo $teacher['name'] . "<br>";
                                        } ?></td>
                                    <td><?= get_homewarke_by_subject_id($subject['id'])->num_rows ?></td>
                                </tr>
                            <?php endforeach ?>
                        <?php endforeach ?>
                    </tbody>
                </table>
            </div>
            <!-- ####################################################[نتائج النشاطات]###################################################### -->
            <div class="informations" id="statu_4">
                <table>
                    <thead>
                        <tr>
                            <th>الرقم</th>
                            <th>الاسم</th>
                            <th>المادة</th>
                            <th>نوع النشاط</th>
                            <th>التاريخ</th>
                            <th>العلامة</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- الفور الاولى تمر على الايديات الخاصة بالابناء -->
                        <?php foreach (get_students_id() as $student): ?>
                            <!-- الفور الثانية تمر علة جميع واجبات كل ابن -->
                            <?php foreach (get_homework($student['student_id']) as $index => $homewarke): ?>
                                <tr>
                                    <td><?= $index + 1 ?></td>
                                    <td><?= get_student($student['student_id'])['name'] ?></td>
                                    <td><?= get_subject_info($homewarke['subject_id'])['name'] ?></td>
                                    <td><?= $homewarke['type'] ?></td>
                                    <td><?= $homewarke['date'] ?></td>
                                    <td><?php if (get_homewarke_result($student['student_id'], $homewarke['id'])) {
                                            if (get_homewarke_result($student['student_id'], $homewarke['id'])['show'] == 'hidden') {
                                                echo "محجوبة";
                                            } else {
                                                echo get_homewarke_result($student['student_id'], $homewarke['id'])['mark'];
                                            }
                                        } else {
                                            echo "لم يتم التقديم";
                                        } ?></td>
                                </tr>
                            <?php endforeach ?>
                        <?php endforeach ?>
                    </tbody>
                </table>
            </div>
        </div>
        <!-- ################################################################################################################### -->
        <!-- ###################################################[الامتحانات]#################################################### -->
        <!-- ################################################################################################################### -->
        <div class="inside-div" id="usecase_3">
            <h1 class="title">امتحانات الابناء</h1>
            <div class="nav-bar">
                <button class="section-btn" id="default_show_3" onclick="show_div('statu_5',this)">عرض</button>
                <button class="section-btn" onclick="show_div('statu_6',this)">نتائج الامتحانات</button>
            </div>
            <!-- ####################################################[عرض]###################################################### -->
            <div class="informations" id="statu_5">
                <table>
                    <thead>
                        <tr>
                            <th>الرقم</th>
                            <th>الاسم</th>
                            <th>المادة</th>
                            <th>المدرسين</th>
                            <th>عدد الامتحانات</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- الفور الاولى تمر على الايديات الخاصة بالابناء -->
                        <?php foreach (get_students_id() as $student): ?>
                            <!-- الفور الثانية تمر علة جميع مواد كل ابن -->
                            <?php foreach (get_subjects($student['student_id']) as $index => $subject): ?>
                                <tr>
                                    <td><?= $index + 1 ?></td>
                                    <td><?= get_student($student['student_id'])['name'] ?></td>
                                    <td><?= get_subject_info($subject['id'])['name'] ?></td>
                                    <td><?php foreach (get_teachers_subject($subject['id']) as $key => $teacher) {
                                            echo $teacher['name'] . "<br>";
                                        } ?></td>
                                    <td><?= get_exam_by_subject_id($subject['id'])->num_rows ?></td>
                                </tr>
                            <?php endforeach ?>
                        <?php endforeach ?>
                    </tbody>
                </table>
            </div>
            <!-- ####################################################[نتائج الامتحانات]################################################### -->
            <div class="informations" id="statu_6">
                <table>
                    <thead>
                        <tr>
                            <th>الرقم</th>
                            <th>الاسم</th>
                            <th>المادة</th>
                            <th>عددالاسئلة</th>
                            <th>التاريخ</th>
                            <th>العلامة</th>
                            <th>الحالة</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- الفور الاولى تمر على الايديات الخاصة بالابناء -->
                        <?php foreach (get_students_id() as $student): ?>
                            <!-- الفور الثانية تمر علة جميع واجبات كل ابن -->
                            <?php foreach (get_exam($student['student_id']) as $index => $exam): ?>
                                <tr>
                                    <td><?= $index + 1 ?></td>
                                    <td><?= get_student($student['student_id'])['name'] ?></td>
                                    <td><?= get_subject_info($exam['subject_id'])['name'] ?></td>
                                    <td><?= $exam['count'] ?></td>
                                    <td><?= $exam['date'] ?></td>
                                    <td><?php if (get_exam_result($student['student_id'], $exam['id'])) {
                                            if (get_exam_result($student['student_id'], $exam['id'])['show'] == 'hidden') {
                                                echo "محجوبة";
                                            } else {
                                                echo get_exam_result($student['student_id'], $exam['id'])['mark'];
                                            }
                                        } else {
                                            echo "لم يتم التقديم";
                                        } ?></td>
                                    <td><?php if (get_exam_result($student['student_id'], $exam['id'])) {
                                            if (get_exam_result($student['student_id'], $exam['id'])['show'] == 'hidden') {
                                                echo "محجوبة";
                                            } else {
                                                echo get_exam_result($student['student_id'], $exam['id'])['status'];
                                            }
                                        } else {
                                            echo "لم يتم التقديم";
                                        } ?></td>
                                </tr>
                            <?php endforeach ?>
                        <?php endforeach ?>
                    </tbody>
                </table>
            </div>
        </div>
        <!-- ################################################################################################################### -->
        <!-- ###################################################[المواد]####################################################### -->
        <!-- ################################################################################################################### -->
        <div class="inside-div" id="usecase_4">
            <h1 class="title">مواد الابناء</h1>
            <div class="nav-bar">
                <button class="section-btn" id="default_show_4" onclick="show_div('statu_7',this)">عرض</button>
                <button class="section-btn" onclick="show_div('statu_8',this)">الحضور</button>
            </div>
            <!-- ####################################################[عرض]###################################################### -->
            <div class="informations" id="statu_7">
                <table>
                    <thead>
                        <tr>
                            <th>الرقم</th>
                            <th>الاسم</th>
                            <th>المادة</th>
                            <th>اسماء المدرسين</th>
                            <th>عدد المحاضرات</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- الفور الاولى تمر على الايديات الخاصة بالابناء -->
                        <?php foreach (get_students_id() as $student): ?>
                            <!-- الفور الثانية تمر علة جميع المواد كل ابن -->
                            <?php foreach (get_subjects($student['student_id']) as $index => $subject): ?>
                                <tr>
                                    <td><?= $index + 1 ?></td>
                                    <td><?= get_student($student['student_id'])['name'] ?></td>
                                    <td><?= get_subject_info($subject['id'])['name'] ?></td>
                                    <td><?php foreach (get_teachers_subject($subject['id']) as $key => $teacher) {
                                            echo $teacher['name'] . "<br>";
                                        } ?></td>
                                    <td><?= get_lectures($subject['id'])->num_rows ?></td>
                                </tr>
                            <?php endforeach ?>
                        <?php endforeach ?>
                    </tbody>
                </table>
            </div>
            <!-- ####################################################[الحضور]###################################################### -->
            <div class="informations" id="statu_8">
                <table>
                    <thead>
                        <tr>
                            <th>الرقم</th>
                            <th>الاسم</th>
                            <th>المادة</th>
                            <th>العنوان</th>
                            <th>طبيعة المحاضرة</th>
                            <th>الحضور</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- الفور الاولى تمر على الايديات الخاصة بالابناء -->
                        <?php foreach (get_students_id() as $student): ?>
                            <!-- الفور الثانية تمر علة جميع المحاضرات كل ابن -->
                            <?php foreach (get_student_lectures($student['student_id']) as $index => $lecture): ?>
                                <tr>
                                    <td><?= $index + 1 ?></td>
                                    <td><?= get_student($student['student_id'])['name'] ?></td>
                                    <td><?= get_subject_info($lecture['subject_id'])['name'] ?></td>
                                    <td><?= $lecture['title'] ?></td>
                                    <td><?= $lecture['type'] == 'recorded' ? 'مسجلة' : 'اونلاين' ?></td>
                                    <td><?= $lecture['status'] == 'yes' ? 'حاضر' : 'غائب' ?></td>
                                </tr>
                            <?php endforeach ?>
                        <?php endforeach ?>
                    </tbody>
                </table>
            </div>
        </div>
        <!-- ################################################################################################################### -->
        <!-- #################################################[الملاحظات]####################################################### -->
        <!-- ################################################################################################################### -->
        <div class="inside-div" id="usecase_5">
            <h1 class="title">الملاحظات</h1>
            <div class="nav-bar">
                <button class="section-btn" id="default_show_5" onclick="show_div('statu_9',this)">عرض الملاحظات</button>
                <button class="section-btn" onclick="show_div('statu_10',this)">ارسال ملاحظة</button>
            </div>
            <!-- ####################################################[عرض]###################################################### -->
            <div class="informations" id="statu_9">
                <div class="massege-div">
                    <?php foreach (get_masseges() as $massege): ?>
                        <div class="massege-row">
                            <div class="massege-group">
                                <div class="massege-info">
                                    <label for=""><?= get_student($massege['sender_id'])['name']  ?></label>
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
            <div class="informations" id="statu_10">
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
                                    <?php $sql = 'SELECT * FROM user_accounts WHERE type = 1 ';
                                    $result = $conn->query($sql);
                                    ?>
                                    <?php foreach ($result as $admin): ?>
                                        <option value="<?= $admin['id'] ?>"><?= $admin['name'] . "........(مدير)" ?></option>
                                    <?php endforeach ?>
                                    <?php foreach (get_teachers_ids() as $teacher): ?>
                                        <option value="<?= $teacher['teacher_id'] ?>"><?= get_student($teacher['teacher_id'])['name'] . "..........(مدرس)" ?></option>
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
        <!-- #################################################[التقارير]####################################################### -->
        <!-- ################################################################################################################### -->
        <div class="inside-div" id="usecase_6">
            <h1 class="title">التقارير</h1>
            <div class="nav-bar">
                <button class="section-btn" id="default_show_6" onclick="show_div('statu_11',this)">طلب تقرير</button>
                <button class="section-btn" onclick="show_div('statu_12',this)">عرض التقارير</button>
                <button class="section-btn" onclick="show_div('statu_13',this)">الشهادات</button>
            </div>
            <!-- ####################################################[طلب تقرير]###################################################### -->
            <div class="informations" id="statu_11">
                <div class="form-div">
                    <form action="" method="post">
                        <div class="form-row">
                            <div class="form-group">
                                <label for="">اختر طالب:</label>
                                <select name="email" id="">
                                    <?php foreach (get_students() as $index => $student): ?>
                                        <option value="<?= $student['email'] ?>"><?= $student['name'] ?></option>
                                    <?php endforeach ?>
                                </select>
                            </div>
                        </div>
                        <input type="submit" name="report_student" value="إرسال الطلب" class="btn">
                        <input type="hidden" name="status" value="تقرير">
                    </form>
                </div>
            </div>
            <!-- ####################################################[عرض التقارير]###################################################### -->
            <div class="informations" id="statu_12">
                <table>
                    <thead>
                        <tr>
                            <th>الرقم</th>
                            <th>الاسم</th>
                            <th>المرحلة الدراسية</th>
                            <th>التاريخ</th>
                            <th>الاجراءات</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach (get_report() as $index => $report): ?>
                            <tr>
                                <td><?= $index + 1 ?></td>
                                <td><?= get_student($report['student_id'])['name'] ?></td>
                                <td><?= get_acad($report['student_id'])['academic_year'] ?></td>
                                <td><?= $report['date'] ?></td>
                                <td>
                                    <a href="../components/report_form.php?student_id=<?= $report['student_id'] ?>"><span class="green-icon"><i class="fa-solid fa-file-lines"></i></span></a>
                                </td>
                            </tr>
                        <?php endforeach ?>
                    </tbody>
                </table>
            </div>
            <!-- ####################################################[الشهادات]###################################################### -->
            <div class="informations" id="statu_13">
                <table>
                    <thead>
                        <tr>
                            <th>الرقم</th>
                            <th>الاسم</th>
                            <th>المرحلة الدراسية</th>
                            <th>التاريخ</th>
                            <th>الاجراءات</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach (get_certificates() as $index => $certificate): ?>
                            <tr>
                                <td><?= $index + 1 ?></td>
                                <td><?= get_student($certificate['student_id'])['name'] ?></td>
                                <td><?= get_acad($certificate['student_id'])['academic_year'] ?></td>
                                <td><?= $certificate['date'] ?></td>
                                <td><a href="../components/shahade.php?student_id=<?= $certificate['student_id'] ?>"><span class="green-icon"><i class="fa-solid fa-graduation-cap"></i></span></a></td>
                            </tr>
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