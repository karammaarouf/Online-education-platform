<!--الاتصال بقاعدة البيانات -->
<?php include '../components/connect.php'; ?>
<?PHP include '../components/send_email.php'; ?>
<?php session_start(); //بدء الجلسة
if (!isset($_SESSION['user_id']) || ($_SESSION['type'] != 2)) { //التحقق من ان المستخدم قد سجل الدخول
    // إعادة توجيه المستخدم إلى صفحة تسجيل الدخول
    header('Location: ../components/login.php');
    $conn->close();
    exit();
} else {
    $teacher_id = $_SESSION['user_id'];
    // دالة تعيد كل معلومات حساب المدرس
    function get_user_accounts()
    {
        global $teacher_id, $conn;
        $sql = "SELECT * FROM user_accounts WHERE id = ?";
        $stmt = $conn->prepare($sql); //نستخدم هذه الطريقة للاستعلام من الجدول عندما يكون ناتج الاستعلام المتوقع هو سطر واحد فقط
        $stmt->bind_param("i", $teacher_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $user = $result->fetch_assoc();
        $stmt->close();
        return $user;
    }
    //دالة احضار معلومات عن الطلاب الذين  سجلو على مواد تخص المدرس حسب ايدي المدرس
    function get_students_subjects($teacher_id)
    {
        global $conn;
        $result = []; // مصفوفة فارغة لتخزين النتائج

        // استخدام عبارة مُجهزة لجلب المواد التي يدرسها المدرس
        $stmt = $conn->prepare("SELECT subject_id FROM teacher_subject WHERE teacher_id = ?");
        $stmt->bind_param("s", $teacher_id); // استخدام "s" لأن المعرف نص
        $stmt->execute();
        $subjects = $stmt->get_result();

        if ($subjects->num_rows > 0) {
            $subject_ids = [];
            while ($row = $subjects->fetch_assoc()) {
                $subject_ids[] = "'" . $conn->real_escape_string($row['subject_id']) . "'";
            }

            if (!empty($subject_ids)) {
                // جلب الطلاب المسجلين في هذه المواد
                $subject_ids_str = implode(",", $subject_ids);
                $sql = "SELECT DISTINCT student_id FROM student_subject WHERE subject_id IN ($subject_ids_str)";
                $students = $conn->query($sql);

                if ($students->num_rows > 0) {
                    $student_ids = [];
                    while ($row = $students->fetch_assoc()) {
                        $student_ids[] = "'" . $conn->real_escape_string($row['student_id']) . "'";
                    }

                    if (!empty($student_ids)) {
                        // جلب معلومات الطلاب من جدول user_accounts
                        $student_ids_str = implode(",", $student_ids);
                        $sql = "SELECT * FROM user_accounts WHERE id IN ($student_ids_str)";
                        $user_accounts = $conn->query($sql);

                        if ($user_accounts->num_rows > 0) {
                            $result = $user_accounts->fetch_all(MYSQLI_ASSOC);
                        }
                    }
                }
            }
        }

        // إغلاق البيان
        $stmt->close();

        return $result;
    }
    //تحديد الطلاب حسب مادة معينة من اجل ارسال الامتحان الى الطلاب المعننيين فقط
    function get_stu_exam_sub($subject_id){
        global $conn;
        $sql = "SELECT * FROM user_accounts,student_subject WHERE student_subject.student_id=user_accounts.id AND student_subject.subject_id='$subject_id'";
        $students = $conn->query($sql);
        return $students;
    }
         //دالة تستعلم عن اولياء امور الطلاب الخاصيين بالمدرس
    function get_parent_student()
    {
        global $conn;
        $students = (get_students_subjects($_SESSION['user_id']));
        $result = [];
        foreach ($students as $row) {
            $student_id = $row["id"];
            $sql = "SELECT * FROM user_accounts,parent_student WHERE user_accounts.id=parent_student.parent_id AND parent_student.student_id='$student_id'";
            $result[] = $conn->query($sql)->fetch_assoc();
        }
        return $result;
    }
    //دالة تعيد ايدي المواد الخاصة بهذا المدرس
    function get_teacher_subjects_ids()
    {
        global  $conn;
        $teacher_id = $_SESSION['user_id'];
        $sql = "SELECT subject_id FROM teacher_subject WHERE teacher_id ='$teacher_id'";
        $result = $conn->query($sql); //نستخدم هذا الاستعلام عندما يكون ناتج الاستعلام المتوقع به اكثر من سطر
        $users = [];

        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $users[] = $row['subject_id'];
            }
        }

        return $users;
    }
    //دالة لاحضار معلومات المادة حسب الايدي
    //تعرض مباشرة
    function get_subject_info($subject_id)
    {
        global $conn;

        $sql = "SELECT * FROM subjects WHERE id='$subject_id'";
        $result = $conn->query($sql);
        return $result->fetch_assoc();
    }
    //الاستعلام عن ورقة امتحانية
    function exam_check($subject_id, $teacher_id)
    {
        global $conn;
        $sql_check = "SELECT * FROM exam WHERE subject_id = '$subject_id' AND teacher_id='$teacher_id'";
        $result = $conn->query($sql_check);
        return $result;
    }
    //دالة تعيد اسماء المواد الخاصة بهذا المدرس
    function get_teacher_subjects()
    {
        global $conn;
        $subject_ids_list = get_teacher_subjects_ids();
        $subject_names_list = [];
        foreach ($subject_ids_list as $id) {
            $sql = "SELECT * FROM subjects WHERE id = '$id'";
            $stmt = $conn->query($sql);
            $subject_name = $stmt->fetch_assoc();
            $subject_names_list[] = $subject_name;
        }
        $stmt->close();
        return $subject_names_list;
    }
    //احضار كافة الانشطة
    function get_homewarke()
    {
        global $conn;
        $homewarks = [];
        foreach (get_teacher_subjects_ids() as $subject_id) {
            $sql = "SELECT * FROM homewarke WHERE subject_id='$subject_id'";
            $result = $conn->query($sql);

            if ($result->num_rows > 0) {

                while ($row = $result->fetch_assoc()) {
                    $homewarks[] = $row;
                }
            }
        }
        return $homewarks;
    }
    //دالة للاستعلام عن المحاضرات
    function get_lectures()
    {
        global $conn, $selectedOption, $teacher_id;
        $sql = "SELECT * FROM lectures WHERE subject_id='$selectedOption' AND teacher_id='$teacher_id'";
        $result = $conn->query($sql);
        $lectures = [];
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $lectures[] = $row;
            }
        }

        return $lectures;
    }
    //احضار الطلاب حسب ايدي المادة 
    function get_students($subject_id)
    {
        global $conn;
        $sql = "SELECT student_id FROM student_subject WHERE subject_id='$subject_id'";
        //نحضر جميع ايديهات الطلاب الذين سجلوا على نفس المادة المختارة
        $result = $conn->query($sql);
        if ($result->num_rows > 0) {
            $students = [];
            while ($row = $result->fetch_assoc()) {
                $id = $row['student_id'];
                $sql = "SELECT * FROM user_accounts WHERE id='$id'";
                $students[] = $conn->query($sql)->fetch_assoc();
            }
        }
        return $students;
    }
    //دالة احضار معلومات عن برنامج الامتحان حسب ايدي المدرس
    function get_exam_info($teacher_id)
    {
        global $conn;
        $sql = "SELECT * FROM exam_start WHERE teacher_id='$teacher_id'";
        $result = $conn->query($sql);
        return $result;
    }
    //نتائج الامتحانات
    function get_exam_result()
    {
        global $conn;
        $teacher_subjects = get_teacher_subjects_ids();
        $exam = [];
        foreach ($teacher_subjects as $subject) {
            $sql = "SELECT * FROM exam_result WHERE subject_id='$subject'";
            $result = $conn->query($sql);

            if ($result->num_rows > 0) {

                while ($row = $result->fetch_assoc()) {
                    $exam[] = $row;
                }
            }
        }
        return $exam;
    }
    //الاستعلام عن النشاطات
    function get_homewarke_result()
    {
        global $conn;
        $teacher_subjects = get_teacher_subjects_ids();
        $homewarkes = [];
        foreach ($teacher_subjects as $subject) {
            $sql = "SELECT * FROM upload_homewarke WHERE subject_id='$subject'";
            $result = $conn->query($sql);

            if ($result->num_rows > 0) {

                while ($row = $result->fetch_assoc()) {
                    $homewarkes[] = $row;
                }
            }
        }
        return $homewarkes;
    }
    //دالة احضار بيانات الطالب حسب الايدي
    function get_student_info($student_id)
    {
        global $conn;
        $sql = "SELECT * FROM user_accounts WHERE id = '$student_id'";
        $result = $conn->query($sql);
        return $result->fetch_assoc();
    }
    //دالة احضار نشاط معين حسب الايدي
    function get_homewarke_info($homewarke_id)
    {
        global $conn;
        $sql = "SELECT * FROM homewarke WHERE id ='$homewarke_id'";
        return $conn->query($sql)->fetch_assoc();
    }
    //احضار الرسائل
    function get_masseges()
    {
        global $conn;
        $sql = "SELECT * FROM masseges WHERE recipient_id='{$_SESSION['user_id']}'";
        $result = $conn->query($sql);
        return $result;
    }
    //حجب و اظهار العلامات للطلاب
    if (isset($_POST['show_submit'])) {
        $subject_id = $_POST['subject'];
        if ($_POST['show_submit'] === "حجب العلامات") {
            $sql = "UPDATE exam_result SET `show`='hidden' WHERE subject_id='$subject_id'";
            $conn->query($sql);
        } else {
            $sql = "UPDATE exam_result SET `show`='block' WHERE subject_id='$subject_id'";
            $conn->query($sql);
        }
        header("location:teacher_dashboard.php");
    }
    //حجب و اظهار العلامات للطلاب على النشاط
    if (isset($_POST['show_homewarke'])) {
        $subject_id = $_POST['subject'];
        if ($_POST['homewarke_submit'] === "حجب العلامات") {
            $sql = "UPDATE upload_homewarke SET `show`='hidden' WHERE subject_id='$subject_id'";
            $conn->query($sql);
        } else {
            $sql = "UPDATE upload_homewarke SET `show`='block' WHERE subject_id='$subject_id'";
            $conn->query($sql);
        }
        header("location:teacher_dashboard.php");
    }
    //تصحيح الانشطة
    if (isset($_POST['mark_result'])) {
        $homewarke_id = $_POST['homewarke_id'];
        $mark = $_POST['mark'];
        $sql = "UPDATE upload_homewarke SET mark='$mark' WHERE id = '$homewarke_id'";
        $conn->query($sql);
        header("location:teacher_dashboard.php");
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
        header("refresh:0.5;URL=teacher_dashboard.php");
    }
    //حذف سؤال
    if (isset($_POST['delete_question'])) {
        $id = $_POST['id'];
        $sql = "DELETE FROM exam WHERE id='$id'";
        if ($conn->query($sql) === TRUE) {
            header("refresh:0.5;URL=teacher_dashboard.php");
        }
    }
    //بدأ سؤال او تفعيله
    if (isset($_POST['active_question'])) {
        $id = $_POST['id'];
        $sql = "UPDATE exam SET status='active' WHERE id='$id'";
        if ($conn->query($sql) === TRUE) {
            header("refresh:0.5;URL=teacher_dashboard.php");
        }
    }
    //الغاء تفعيل سؤال او اخفاءه
    if (isset($_POST['block_question'])) {
        $id = $_POST['id'];
        $sql = "UPDATE exam SET status='block' WHERE id='$id'";
        if ($conn->query($sql) === TRUE) {
            header("refresh:0.5;URL=teacher_dashboard.php");
        }
    }
    //لبدء الامتحان
    if (isset($_POST['send_exam'])) {
        $id = unique_id();
        $teacher_id = $_SESSION['user_id'];
        $teacher_name = $_SESSION['username'];
        $subject_id = $_POST['subject'];
        $subject_name = get_subject_info($subject_id)['name'];
        // يمثل عدد الاسئلة
        $count = $_POST['count'];
        //تمثل مدة الامتحان بالدقائق
        $time = $_POST['time'];
        //تمثل تاريخ بدء الامتحان
        $date = $_POST['date'];
        //اختبار شرط ان عدد الاسئلة المحددة موجود في بنك الاسئلة
        if (exam_check($subject_id, $_SESSION['user_id'])->num_rows < $count) {
            echo "<script>alert('عدد الاسئلة غير كافي في بنك الاسئلة, لديك " . exam_check($subject_id, $_SESSION['user_id'])->num_rows . "سؤال في بنك الاسئلة لمادة " . $subject_name . "')</script>";
            header("refresh:0.5;URL=teacher_dashboard.php");
        } else {
            $sql = "INSERT INTO exam_start (id,subject_id,subject_name,teacher_id,teacher_name,count,time,date) VALUES ('$id','$subject_id','$subject_name','$teacher_id','$teacher_name','$count','$time','$date')";
            if ($conn->query($sql) === TRUE) {
                foreach (get_stu_exam_sub($subject_id) as $student) {
                    $student_id = $student['id'];
                    $sql = "INSERT INTO student_exam (student_id,exam_id) VALUES ('$student_id','$id')";
                    $conn->query($sql);
                    $student_email = $student['email'];
                    send_email($student_email, "exam", "<h1>لديك امتحان جديد تم اضافته حديثاً, لا تنسى الاطلاع عليه</h1>");
                }
                echo "<script>alert('تم ارسال الامتحان بنجاح')</script>";
                header("refresh:0.5;URL=teacher_dashboard.php");
            }
        }
    }
    //ارسال بيانات النشاط الى القاعدة
    if (isset($_POST['homewarke_submit'])) {
        $id = unique_id();
        $type = $_POST['homewarke_type'];
        $subject = $_POST['subject'];
        $teacher_id = $_SESSION['user_id'];
        $question = $_POST['question'];
        $count = ($type == 'عادي' ? 1 : $_POST['count']);
        $time = $_POST['time'];
        $date = $_POST['date'];
        // استخدام عبارات محضرة (Prepared Statements) لتحسين الأمان ومنع هجمات SQL Injection
        $stmt = $conn->prepare("INSERT INTO homewarke (id, subject_id, teacher_id, question, time, date, count, type) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssssisis", $id, $subject, $teacher_id, $question, $time, $date, $count, $type);

        // تنفيذ الاستعلام والتحقق من نجاح العملية
        if ($stmt->execute()) {
            foreach (get_stu_exam_sub($subject) as $student) {
                $student_id = $student['id'];
                $sql = "INSERT INTO student_homewarke(student_id,homewarke_id) VALUES ('$student_id','$id')";
                $conn->query($sql);
                $student_email = $student['email'];
                send_email($student_email, "homewarke", "<h1>لديك نشاط جديد تم اضافته حديثاً</h1>");
            }
            echo "<script>alert('تم ارسال النشاط الى الطلاب');</script>";

            header("refresh:0.5;URL=teacher_dashboard.php");
        } else {
            echo "<script>alert('حدث خطأ أثناء إرسال النشاط.');</script>";
        }

        // إغلاق البيان
        $stmt->close();
    }
    // اذا انهينا البث المباشر
    if (isset($_POST['end_live'])) {
        if (isset($_SESSION['live_id'])) {
            $live_id = $_SESSION['live_id'];
            $sql = "UPDATE live SET status='ended' WHERE id='$live_id'";
            if ($conn->query($sql)) {
                $_SESSION['timer'] = FALSE;
                echo "<script>alert('انتهت الجلسة بنجاح')</script>";
                header("refresh:0.5;URL=../teacher/teacher_dashboard.php");
            }
        }
    }
    //لحفظ قيمة الاختيار للمحافظة عليه بعد تحميل الصفحة
    if (isset($_POST['option'])) {
        $selectedOption = isset($_POST['option']) ? $_POST['option'] : '';
    }
    if (isset($_POST['show_exam'])) {
        $selectedOption1 = isset($_POST['subject']) ? $_POST['subject'] : '';
        $subject_id = $_POST['subject'];
    }
    //حذف نشاط
    if (isset($_POST['delete_homewarke'])) {
        $homewarke_id= $_POST['homewarke_id'];
        $sql = "DELETE FROM homewarke WHERE id='$homewarke_id'";
        $conn->query($sql);
        $sql="DELETE FROM student_homewarke WHERE homewarke_id='$homewarke_id'";
        $conn->query($sql);
        header("location:teacher_dashboard.php");
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>teacher</title>
    <link rel="stylesheet" href="../css/style.css">
    <!-- font awesome cdn link  -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css">
    <script defer src="../js/script.js"></script> <!-- تحميل السكربت بعد تحميل DOM -->
</head>

<body>
    <!-- هيدر المدرس -->
    <header class="header">
        <div class="logo-div">
            <a href="../teacher/teacher_dashboard.php" class="logo"><img src="../images/logo.png" alt="لوغو المنصة">إرادة</a>
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
        <!-- القسم الخاص بحالات استخدام المدرس-->
        <div class="side-bar">
            <div class="img-div">
                <img src="<?php echo  $_SESSION['photo']; ?>" alt="صورة شخصية">
                <h2>مدرس</h2>
                <p><?php echo $_SESSION['username']; ?></p>
            </div>
            <div class="btn-div">
                <a class="btn update-btn" href="../components/update.php">الملف الشخصي <span class="icon"><i class="fa-solid fa-address-card"></i></span></a>
                <a class="btn logout-btn" href="../components/logout.php">تسجيل الخروج <span class="icon"><i class="fa-solid fa-right-from-bracket"></i></span></a>
            </div>
            <div class="usecase">
                <a href="#" class="usecase-btn" onclick="show_usecase('usecase_1',this,'default_show_1')">المحاضرات المسجلة <span class="icon"><i class="fa-solid fa-sheet-plastic"></i></span></a>
                <a href="#" class="usecase-btn" onclick="show_usecase('usecase_2',this,'default_show_2')">محاضرات اونلاين <span class="icon"><i class="fa-solid fa-satellite-dish"></i></span></a>
                <a href="#" class="usecase-btn" onclick="show_usecase('usecase_3',this,'default_show_3')">الطلاب <span class="icon"><i class="fa-solid fa-user-graduate"></i></span></a>
                <a href="#" class="usecase-btn" onclick="show_usecase('usecase_4',this,'default_show_4')">الأنشطة <span class="icon"><i class="fa-solid fa-file-export"></i></span></a>
                <a href="#" class="usecase-btn" onclick="show_usecase('usecase_5',this,'default_show_5')">الامتحانات <span class="icon"><i class="fa-solid fa-file-lines"></i></span></a>
                <a href="#" class="usecase-btn" onclick="show_usecase('usecase_6',this,'default_show_6')">الملاحظات <span class="icon"><i class="fa-solid fa-envelope-open-text"></i></span></a>
            </div>
        </div>
        <!-- ################################################################################################################### -->
        <!-- #################################################[ادارة المحاضرات]################################################ -->
        <!-- ################################################################################################################### -->
        <div class="inside-div" id="usecase_1">
            <h1 class="title">إدارة المحاضرات</h1>
            <div class="nav-bar">
                <button class="section-btn" id="default_show_1" onclick="show_div('statu_1',this)">عرض</button>
                <button class="section-btn" onclick="show_div('statu_2',this)">إضافة</button>
            </div>
            <!-- ####################################################[عرض]###################################################### -->
            <div class="informations" id="statu_1">
                <div class="form-div">
                    <form id="selectForm" action="" method="post">
                        <div class="form-row">
                            <div class="form-group">
                                <select name="option" onchange="document.getElementById('selectForm').submit()">
                                    <option value="">--اختر المادة--</option>
                                    <?php foreach (get_teacher_subjects() as $subject) {
                                        echo "<option value='" . $subject['id'] . "'" . ($selectedOption == $subject['id'] ? ' selected' : '') . ">" . $subject['name'] . "</option>";
                                    } ?>
                                </select>
                            </div>
                        </div>
                    </form>
                </div>
                <div class='card-container'>
                    <?php foreach (get_lectures() as $lecture) : ?>
                        <div class='card-div'>
                            <div class='card-row'>
                                <div class='card-img'>
                                    <img src='<?= $lecture['photo'] ?>'>
                                </div>
                            </div>
                            <div class='card-row'>
                                <div class='card-text'>
                                    <h2><?= $lecture['title'] ?></h2>
                                    <p><?= $lecture['description'] ?></p>
                                </div>
                            </div>
                            <div class='card-row'>
                                <div class='card-option'>
                                    <a href='../components/edit_lecture.php?&id=<?= $lecture['id'] ?>' class='edit-btn'>تعديل <i class="fa-solid fa-gear"></i></a>
                                    <a href='../components/view_video.php?&id=<?= $lecture['id'] ?>' class='show-btn'>عرض <i class="fa-solid fa-eye"></i></a>
                                    <a href='../components/delete_lecture.php?&id=<?= $lecture['id'] ?>' class='delete-btn'>حذف <i class="fa-solid fa-trash"></i></a>
                                </div>
                            </div>
                        </div>
                    <?php endforeach  ?>
                </div>
            </div>
            <!-- ####################################################[اضافة]###################################################### -->
            <div class="informations" id="statu_2">
                <div class="form-div">
                    <form action="../components/add_lecture.php" method="post" enctype="multipart/form-data">
                        <h1>إضافة محاضرة</h1>
                        <div class="form-row">
                            <div class="form-group">
                                <label for="">المادة: <span>*</span></label>
                                <select name="subject_id" id="" required>
                                    <option value="">اختر المادة..</option>
                                    <?php foreach (get_teacher_subjects() as $subject) {
                                        $id = $subject['id'];
                                        $name = $subject['name'];
                                        echo "<option value='$id'>$name</option>";
                                    } ?>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="">العنوان: <span>*</span></label>
                                <input type="text" name="title" placeholder="أدخل العنوان هنا..." required>
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group">
                                <label for="">الوصف: <span>*</span></label>
                                <input id="description" name="description" placeholder="أدخل الوصف هنا..." required>
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group">
                                <label for="">الملف: <span>*</span></label>
                                <input type="file" name="file" required>
                            </div>
                            <div class="form-group">
                                <label for="">الغلاف: <span>*</span></label>
                                <input type="file" name="photo" accept="image/*" required>
                            </div>
                            <div class="form-group">
                                <label for="">الفيديو: <span>*</span></label>
                                <input type="file" name="video" accept="video/*" required>
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group check_input">
                                <label for="status">محاضرة مسجلة</label>
                                <input type="hidden" name="type" value="recorded" required>
                            </div>
                        </div>
                        <button type="submit" name="submit">إضافة محاضرة</button>
                    </form>
                </div>
            </div>
        </div>
        <!-- ################################################################################################################### -->
        <!-- #################################################[ادارة البث المباشر]############################################# -->
        <!-- ################################################################################################################### -->
        <div class="inside-div" id="usecase_2">
            <h1 class="title">محاضرة أونلاين</h1>
            <div class="nav-bar">
                <button class="section-btn" id="default_show_2" onclick="show_div('statu_3',this)">جلسة جديدة</button>
            </div>
            <!-- ################################################[جلسات مباشرة]################################################### -->
            <div class="informations" id="statu_3">
                <div class="form-div">
                    <form action="../components/add_lecture.php" method="post" enctype="multipart/form-data">
                        <h1>بدء محاضرة اونلاين</h1>
                        <div class="form-row">
                            <div class="form-group">
                                <label for="">المادة: <span>*</span></label>
                                <select name="subject_id" id="" required>
                                    <option value="">اختر المادة..</option>
                                    <?php foreach (get_teacher_subjects() as $subject) {
                                        $id = $subject['id'];
                                        $name = $subject['name'];
                                        echo "<option value='$id'>$name</option>";
                                    } ?>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="">العنوان: <span>*</span></label>
                                <input type="text" name="title" placeholder="أدخل العنوان هنا..." required>
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group">
                                <label for="">الوصف: <span>*</span></label>
                                <input id="description" name="description" placeholder="أدخل الوصف هنا..." required>
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group">
                                <label for="">الملف: <span>*</span></label>
                                <input type="file" name="file" required>
                            </div>
                            <div class="form-group">
                                <label for="">الغلاف: <span>*</span></label>
                                <input type="file" name="photo" accept="image/*" required>
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group check_input">
                                <label for="status">محاضرة اونلاين</label>
                                <input type="hidden" name="type" value="online" required>
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group">
                                <label for="">افتح جلسة:</label>
                                <a href="https://meet.google.com/new" class="btn" target="_blank">اضغط لبدأ البث المباشر</a>
                            </div>
                            <div class="form-group">
                                <label>رابط الجلسة:</label>
                                <input name='link' type="text" placeholder="ضع رابط الجلسة هنا..." required>
                            </div>
                        </div>
                        <button type="submit" name="live_submit"> ابدأ الجلسة الآن</button>
                    </form>
                </div>
                <div class="hidden_div hidden" id="timeDiv">
                    <div class="live_timer" id="time_div">
                        <div class="form-div">
                            <form action="" method="post">
                                <div class="form-row">
                                    <label for="" onclick="min_div()" id="min">تصغير</label>
                                </div>
                                <div class="form-row">
                                    <div class="form-group">
                                        <h1>وقت الجلسة</h1>
                                        <label id="timeCounter">00:00:00</label>
                                    </div>
                                </div>
                                <input type="submit" onclick="toggel_hidden()" name='end_live' value="إنهاء الجلسة">
                            </form>
                        </div>
                    </div>
                    <div onclick="min_div()" class="max">Live</div>
                </div>
            </div>
        </div>
        <!-- ################################################################################################################### -->
        <!-- #################################################[ادارة الطلاب]#################################################### -->
        <!-- ################################################################################################################### -->
        <div class="inside-div" id="usecase_3">
            <h1 class="title">إدارة الطلاب</h1>
            <div class="nav-bar">
                <button class="section-btn" id="default_show_3" onclick="show_div('statu_4',this)">عرض</button>
                <button class="section-btn" onclick="show_div('statu_5',this)">نتائج الامتحانات</button>
                <button class="section-btn" onclick="show_div('statu_6',this)">نتائج النشاطات</button>
            </div>
            <!-- ####################################################[عرض الامتحانات]###################################################### -->
            <div class="informations" id="statu_4">
                <?php if (isset($_POST['search_student'])) {
                    $search = $_POST['search'];
                    $sql = "SELECT * FROM user_accounts WHERE name LIKE '%$search%' AND type='3'";
                    $result = $conn->query($sql);
                } ?>
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
                        <input type="submit" name="search_student" class="btn" value="بحث">
                    </form>
                </div>
                <div class="table-div">
                    <h1>جدول البيانات</h1>
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
                            <?php $sql = "SELECT * FROM user_accounts WHERE type='3' ORDER  BY name ASC"; //امر الاستعلام عن كافة الطلاب
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
                                        <td>
                                            <?php $sql = "SELECT subject_id from student_subject WHERE student_id='{$row['id']}'";
                                            $subject_ids = $conn->query($sql);
                                            foreach ($subject_ids as $subject_id) {
                                                echo get_subject_info($subject_id['subject_id'])['name'] . "<br>";
                                            } ?>
                                        </td>
                                        <td><?= $row['status'] ?></td>
                                        <td class='actions'>
                                            <a href="../components/block_unblock_user.php?id=<?= $row['id'] . "&status=" . $row['status'] ?>"><?= ($row['status'] == 'active' ? '<span class="orange-icon"><i class="fa-solid fa-lock-open"></i></span>' : '<span class="orange-icon"><i class="fa-solid fa-lock"></i></span>') ?></a>
                                        </td>
                                    </tr>
                                <?php endwhile ?>
                            <?php endif ?>
                        </tbody>
                    </table>
                </div>
            </div>
            <!-- #################################################[نتائج الامتحانات]################################################# -->
            <div class="informations" id="statu_5">
                <div class="form-div">
                    <form action="" method="post">
                        <div class="form-row">
                            <div class="form-group">
                                <label for="">اختر المادة: <span>*</span></label>
                                <select name="subject" id="" required>
                                    <option value="">--اختر المادة--</option>
                                    <?php foreach (get_teacher_subjects_ids() as $subject_id): ?>
                                        <?php $subject = get_subject_info($subject_id) ?>
                                        <option value="<?= $subject['id'] ?>"><?= $subject['name'] ?></option>
                                    <?php endforeach ?>
                                </select>
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group"><input type="submit" name="show_submit" value="حجب العلامات" class="btn block"></div>
                            <div class="form-group"><input type="submit" name="show_submit" value="إظهار العلامات" class="btn"></div>
                        </div>
                    </form>
                </div>
                <div class="table-div">
                    <h1>جدول نتائج الامتحانات</h1>
                    <table>
                        <thead>
                            <tr>
                                <th>الرقم</th>
                                <th>اسم الطالب</th>
                                <th>اسم المادة</th>
                                <th>العلامة</th>
                                <th>الحالة</th>
                                <th>الاجراءات</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach (get_exam_result() as $index => $exam) : ?>
                                <tr>
                                    <td><?= $index + 1 ?></td>
                                    <td><?= $exam['student_name'] ?></td>
                                    <td><?= $exam['subject_name'] ?></td>
                                    <td><?= $exam['mark'] ?></td>
                                    <td><?= $exam['status'] ?></td>
                                    <td><?= ($exam['show'] === 'hidden' ? "محجوبة" : "ظاهرة") ?></td>
                                </tr>
                            <?php endforeach ?>
                        </tbody>
                    </table>
                </div>
            </div>
            <!-- ####################################################[نتائج الانشطة]################################################### -->
            <div class="informations" id="statu_6">
                <div class="form-div">
                    <form action="" method="post">
                        <div class="form-row">
                            <div class="form-group">
                                <label for="">اختر المادة: <span>*</span></label>
                                <select name="subject" id="" required>
                                    <option value="">--اختر المادة--</option>
                                    <?php foreach (get_teacher_subjects_ids() as $subject_id): ?>
                                        <?php $subject = get_subject_info($subject_id) ?>
                                        <option value="<?= $subject['id'] ?>"><?= $subject['name'] ?></option>
                                    <?php endforeach ?>
                                </select>
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group"><input type="submit" name="show_homewarke" value="حجب العلامات" class="btn block"></div>
                            <div class="form-group"><input type="submit" name="show_homewarke" value="إظهار العلامات" class="btn"></div>
                        </div>
                    </form>
                </div>
                <div class="table-div">
                    <h1>جدول نتائج النشاطات</h1>
                    <table>
                        <thead>
                            <tr>
                                <th>الرقم</th>
                                <th>اسم الطالب</th>
                                <th>اسم المادة</th>
                                <th>النشاط</th>
                                <th>العلامة</th>
                                <th>الاجراءات</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach (get_homewarke_result() as $index => $homewarke) : ?>
                                <tr>
                                    <td><?= $index + 1 ?></td>
                                    <td><?= get_student_info($homewarke['student_id'])['name'] ?></td>
                                    <td><?= get_subject_info($homewarke['subject_id'])['name']  ?></td>
                                    <td><?= $homewarke['type'] ?></td>
                                    <td><?= $homewarke['mark'] ?></td>
                                    <td><?= ($homewarke['show'] === 'hidden' ? "محجوبة" : "ظاهرة") ?></td>
                                </tr>
                            <?php endforeach ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <!-- ################################################################################################################### -->
        <!-- #################################################[ادارة الانشطة]################################################### -->
        <!-- ################################################################################################################### -->
        <div class="inside-div" id="usecase_4">
            <h1 class="title">إدارة الانشطة</h1>
            <div class="nav-bar">
                <button class="section-btn" id="default_show_4" onclick="show_div('statu_7',this)">عرض</button>
                <button class="section-btn" onclick="show_div('statu_8',this)">إضافة</button>
                <button class="section-btn" onclick="show_div('statu_9',this)">تصحيح</button>
            </div>
            <!-- ####################################################[جدول النشاطات]###################################################### -->
            <div class="informations" id="statu_7">
                <div class="table-div">
                    <h1>جدول بيانات الانشطة</h1>
                    <table>
                        <thead>
                            <tr>
                                <th>الرقم</th>
                                <th>المادة</th>
                                <th>النوع</th>
                                <th>عدد الاسئلة</th>
                                <th>المدة</th>
                                <th>التاريخ</th>
                                <th>الاجراءات</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach (get_homewarke() as $index => $homewarke): ?>
                                <tr>
                                    <?php $subject_name = get_subject_info($homewarke['subject_id'])['name'] ?>
                                    <td><?= $index + 1 ?></td>
                                    <td><?= $subject_name ?></td>
                                    <td><?= $homewarke['type'] ?></td>
                                    <td><?= $homewarke['count'] ?></td>
                                    <td><?= $homewarke['time'] ?></td>
                                    <td><?= $homewarke['date'] ?></td>
                                    <td><form action="" method="post">
                                        <input type="hidden" name="homewarke_id" value="<?= $homewarke['id'] ?>">
                                        <button type="submit" name="delete_homewarke"><span class="red-icon"><i class="fa-solid fa-trash"></i></span></button>
                                    </form></td>
                                </tr>
                            <?php endforeach ?>
                        </tbody>
                    </table>
                </div>
            </div>
            <!-- ####################################################[اضافة]###################################################### -->
            <div class="informations" id="statu_8">
                <div class="form-div">
                    <form action="" method="post">
                        <div class="form-row">
                            <div class="form-group">
                                <label for="">اختر المادة:</label>
                                <select name="subject">
                                    <?php foreach (get_teacher_subjects_ids() as $subject_id): ?>
                                        <?php $subject = get_subject_info($subject_id)  ?>
                                        <option value="<?= $subject['id'] ?>"><?= $subject['name'] ?></option>
                                    <?php endforeach ?>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="">نوع النشاط:</label>
                                <select name="homewarke_type" id="" onchange="toggle_homewarke(this)">
                                    <option value="عادي" selected>عادي</option>
                                    <option value="مؤتمت">مؤتمت</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="">تاريخ البداية:</label>
                                <input type="date" name="date" required>
                            </div>
                        </div>
                        <!-- النشاط العادي -->
                        <div class="form-row" id="normal_homewarke">
                            <div class="form-group">
                                <label for="">نص السؤال:</label>
                                <input type="text" name="question">
                            </div>
                        </div>
                        <!-- النشاط المؤتمت -->
                        <div class="form-row" id="options_homewarke" style="display: none;">
                            <div class="form-group">
                                <label for="">عدد الاسئلة:</label>
                                <input type="text" name="count" placeholder="اكتب رقماً صحيحاً...">
                            </div>
                            <div class="form-group">
                                <label for="">المدة بالدقائق:</label>
                                <input type="text" name="time" placeholder="اكتب رقماً صحيحاً...">
                            </div>
                        </div>
                        <input type="submit" name="homewarke_submit" class="btn" value="ارسال">
                    </form>
                </div>
            </div>
            <!-- ####################################################[تصحيح]###################################################### -->
            <div class="informations" id="statu_9">
                <div class="form-div">
                    <form id="examForm" action="" method="post">
                        <div class="form-row">
                            <div class="form-group">
                                <label for="subject">اختر المادة: <span>*</span></label>
                                <select name="subject" required>
                                    <option value="">--اختر المادة--</option>
                                    <?php foreach (get_teacher_subjects() as $subject) {
                                        echo "<option value='" . $subject['id'] . "'>" . $subject['name'] . "</option>";
                                    } ?>
                                </select>
                            </div>
                        </div>
                        <input type="submit" name="homewarke_result" value="عرض الانشطة" class="btn">
                    </form>
                </div>
                <table>
                    <thead>
                        <tr>
                            <th>الرقم</th>
                            <th>الاسم</th>
                            <th>المادة</th>
                            <th>السؤال</th>
                            <th>ملف الإجابة</th>
                            <th>تصحيح</th>
                            <th>العلامة</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (isset($_POST['homewarke_result'])) {
                            $sql = "SELECT * FROM  upload_homewarke WHERE subject_id = '{$_POST['subject']}' AND type='عادي' ";
                            $result = $conn->query($sql);
                        } ?>
                        <?php if (isset($result)): ?>
                            <?php foreach ($result as $index => $homewarke): ?>
                                <tr>
                                    <td><?= $index + 1 ?></td>
                                    <td><?= get_student_info($homewarke['student_id'])['name'] ?></td>
                                    <td><?= get_subject_info($homewarke['subject_id'])['name'] ?></td>
                                    <td><?= get_homewarke_info($homewarke['homewarke_id'])['question'] ?></td>
                                    <td><a href="../components/download.php?file=<?= $homewarke['file'] ?>" download class="edit-btn btn">تنزيل الملف <i class="fa-solid fa-download"></i></a></td>
                                    <td>
                                        <form action="" method="post" style="display:flex;justify-content:space-around;">
                                            <input type="text" placeholder="اكتب العلامة..." name="mark" style="width:10rem;font-size:1rem;">
                                            <input type="hidden" name="homewarke_id" value="<?= $homewarke['id'] ?>">
                                            <button type="submit" class="edit-btn btn" name="mark_result" style="float:left;">تصحيح <i class="fa-solid fa-pen-clip"></i></button>
                                        </form>
                                    </td>
                                    <td><?= $homewarke['mark'] ?></td>
                                </tr>
                            <?php endforeach ?>
                        <?php endif ?>
                    </tbody>
                </table>
            </div>
        </div>
        <!-- ################################################################################################################### -->
        <!-- #################################################[ادارة الامتحانات]################################################ -->
        <!-- ################################################################################################################### -->
        <div class="inside-div" id="usecase_5">
            <h1 class="title">إدارة الامتحانات</h1>
            <div class="nav-bar">
                <button class="section-btn" id="default_show_5" onclick="show_div('statu_10',this)">عرض</button>
                <button class="section-btn" onclick="show_div('statu_11',this)">إضافة اسئلة</button>
                <button class="section-btn" onclick="show_div('statu_12',this)">بدء امتحان</button>
                <button class="section-btn" onclick="show_div('statu_13',this)">بنك الاسئلة</button>
            </div>
            <!-- ####################################################[عرض]###################################################### -->
            <div class="informations" id="statu_10">
                <div class="table-div">
                    <h1>جدول بيانات الامتحانات</h1>
                    <table>
                        <thead>
                            <tr>
                                <th>الرقم</th>
                                <th>المادة</th>
                                <th>عدد الاسئلة</th>
                                <th>المدة</th>
                                <th>تاريخ البدء</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach (get_exam_info($_SESSION['user_id']) as $index => $exam) : ?>
                                <tr>
                                    <td><?= $index + 1 ?></td>
                                    <td><?= $exam['subject_name'] ?></td>
                                    <td><?= $exam['count'] ?></td>
                                    <td><?= $exam['time'] ?></td>
                                    <td><?= $exam['date'] ?></td>
                                </tr>
                            <?php endforeach ?>
                        </tbody>
                    </table>
                </div>
            </div>
            <!-- ####################################################[اضافة اسئلة]###################################################### -->
            <div class="informations" id="statu_11">
                <div class="form-div">
                    <form id="examForm" action="../components/add_exam.php" method="post">
                        <div class="form-row">
                            <div class="form-group">
                                <label for="subject">اختر المادة: <span>*</span></label>
                                <select name="subject" required>
                                    <option value="">--اختر المادة--</option>
                                    <?php foreach (get_teacher_subjects() as $subject) {
                                        echo "<option value='" . $subject['id'] . "'>" . $subject['name'] . "</option>";
                                    } ?>
                                </select>
                            </div>
                        </div>
                        <input type="submit" name="add_exam" value="كتابة اسئلة" class="btn">
                    </form>
                </div>
            </div>
            <!-- ####################################################[بدء الامتحان]###################################################### -->
            <div class="informations" id="statu_12">
                <div class="form-div">
                    <form action="" method="post">
                        <div class="form-row">
                            <div class="form-group">
                                <label for="subject">اختر المادة: <span>*</span></label>
                                <select name="subject" required>
                                    <option value="">--اختر المادة--</option>
                                    <?php foreach (get_teacher_subjects() as $subject) {
                                        echo "<option value='" . $subject['id'] . "'>" . $subject['name'] . "</option>";
                                    } ?>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="count">عدد الاسئلة: <span>*</span></label>
                                <input type="text" name="count" value="50" required>
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group">
                                <label for="subject">مدة الامتحان بالدقائق: <span>*</span></label>
                                <input type="text" name="time" value="60">
                            </div>
                            <div class="form-group">
                                <label for="count">تاريخ البدء: <span>*</span></label>
                                <input type="date" name="date" required>
                            </div>
                        </div>
                        <input type="submit" name="send_exam" value="بدء الامتحان" class="btn">
                    </form>
                </div>
            </div>
            <!-- ####################################################[بنك الاسئلة]###################################################### -->
            <div class="informations" id="statu_13">
                <div class="form-div">
                    <form action="" method="post">
                        <div class="form-row">
                            <div class="form-group">
                                <label for="subject">اختر المادة</label>
                                <select name="subject" required>
                                    <option value="">--اختر المادة--</option>
                                    <!-- استعلام عن اسم المادة -->
                                    <?php foreach (get_teacher_subjects() as $subject) {
                                        echo "<option value='" . $subject['id'] . "' " . ($selectedOption1 == $subject['id'] ? ' selected' : '') . " >" . $subject['name'] . "</option>";
                                    } ?>
                                </select>
                            </div>
                        </div>
                        <input type="submit" name="show_exam" value="عرض الاسئلة" class="btn">
                    </form>
                </div>
                <div class="table-div">
                    <?php if (isset($_POST['show_exam'])) : $subject_id = $_POST['subject'] ?>
                        <h1><?= "عدد الاسئلة : " . exam_check($subject_id, $_SESSION['user_id'])->num_rows ?></h1>
                    <?php endif ?>
                    <table>
                        <thead>
                            <tr>
                                <th>الرقم</th>
                                <th>السؤال</th>
                                <th>الاجابة الصحيحة</th>
                                <th>الخيارات</th>
                                <th>الحالة</th>
                                <th>الإجراءات</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (isset($_POST['show_exam'])): ?>
                                <?php foreach (exam_check($_POST['subject'], $_SESSION['user_id']) as $index => $q): ?>
                                    <tr>
                                        <td><?= $index + 1 ?></td>
                                        <td><?= $q['question'] ?></td>
                                        <td><?= $q['answer'] ?></td>
                                        <td><?php echo "a)" . $q['a'] . "<br>" . "b)" . $q['b'] . "<br>" . "c)" . $q['c'] . "<br>" . "d)" . $q['d'] . "<br>" . "e)" . $q['e']; ?></td>
                                        <td><?= $q['status'] ?></td>
                                        <td class='actions'>
                                            <form action="" method="post">
                                                <input type="text" name="id" value="<?= $q['id'] ?>" hidden>
                                                <button type="submit" name='delete_question'><span class="red-icon"><i class="fa-solid fa-trash"></i></span></button>
                                                <button type="submit" name='<?= ($q['status'] == 'active' ? 'block_question' : 'active_question') ?>'><?= ($q['status'] == 'active' ? '<span class="orange-icon"><i class="fa-solid fa-lock-open"></i></span>' : '<span class="orange-icon"><i class="fa-solid fa-lock"></i></span>') ?></button>
                                            </form>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <!-- ################################################################################################################### -->
        <!-- #################################################[ادارة الملاحظات]################################################# -->
        <!-- ################################################################################################################### -->
        <div class="inside-div" id="usecase_6">
            <h1 class="title">الملاحظات</h1>
            <div class="nav-bar">
                <button class="section-btn" id="default_show_6" onclick="show_div('statu_14',this)">عرض الملاحظات</button>
                <button class="section-btn" onclick="show_div('statu_15',this)">ارسال ملاحظة</button>
            </div>
            <!-- ####################################################[عرض]###################################################### -->
            <div class="informations" id="statu_14">
                <div class="massege-div">
                    <?php foreach (get_masseges() as $massege): ?>
                        <div class="massege-row">
                            <div class="massege-group">
                                <div class="massege-info">
                                    <label for=""><?= get_student_info($massege['sender_id'])['name']  ?></label>
                                    <label for=""><?= $massege['date'] ?></label>
                                </div>
                                <div class="massege">
                                    <label for=""><?= $massege['massege'] ?></label>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
            <!-- ####################################################[ارسال]###################################################### -->
            <div class="informations" id="statu_15">
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
                                    <?php $sql = "SELECT * FROM user_accounts WHERE type=1";
                                    $result = $conn->query($sql);
                                    foreach ($result as $admin) : ?>
                                        <option value="<?= $admin['id'] ?>"><?= $admin['name'] . "..........(مدير)" ?></option>
                                    <?php endforeach; ?>
                                    <?php foreach (get_parent_student() as $parent) : ?>
                                        <option value="<?= $parent['id'] ?>"><?= $parent['name'] . "..........(ولي أمر)" ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        <input type="submit" name="send_massege" value="ارسال الملاحظة" class="btn">
                    </form>
                </div>
            </div>
        </div>
        <!-- ####################################################################################################### -->
        <!-- ####################################################################################################### -->
        <!-- اذا تم عمل جلسة مباشر ينفذ امر الجافا -->
        <?php if (isset($_SESSION['timer']) && $_SESSION['timer'] == TRUE) : ?>
            <script>
                // اظهار ديف الوقت
                let $hidden_div = document.getElementById('timeDiv');
                $hidden_div.classList.toggle('hidden');

                // عداد جلسة البث المباشر

                var startTime = 0;

                function formatTime(seconds) {
                    let hours = Math.floor(seconds / 3600);
                    let minutes = Math.floor((seconds % 3600) / 60);
                    let secs = seconds % 60;
                    let time = hours.toString().padStart(2, "0") + ":" + minutes.toString().padStart(2, "0") + ":" + secs.toString().padStart(2, "0");
                    return time;
                }

                function startCounter() {
                    let counter = document.getElementById("timeCounter");
                    setInterval(function() {
                        startTime++;
                        counter.textContent = formatTime(startTime);
                    }, 1000);
                };
                startCounter();
            </script>
        <?php endif ?>

    </section>
    <button class="floating-btn" onclick="show_side_bar()">get</button>
    <!-- الفوتر -->
    <?php include '../components/footer.php'; ?>

</body>

</html>