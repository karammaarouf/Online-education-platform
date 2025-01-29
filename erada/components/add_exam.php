<?php include 'connect.php'; ?>
<?php
//دالة اضافة سؤال الى جدول الامتحانات
function add_question($subject_id, $teacher_id, $subject_name, $teacher_name, $status, $question, $a, $b, $c, $d, $e, $answer, $id)
{
    global $conn;
    $sql = "INSERT INTO exam (subject_id,teacher_id,subject_name,teacher_name,status,question,a,b,c,d,e,answer,id) VALUES ('$subject_id','$teacher_id','$subject_name','$teacher_name','$status','$question','$a','$b','$c','$d','$e','$answer','$id') ";
    if ($conn->query($sql) === TRUE) {
        header('location:add_exam.php');
    }
}
//دالة لاحضار معلومات المادة حسب الايدي
//تعرض مباشرة
function get_subject_live($subject_id)
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
?>
<?php session_start();
if ($_SESSION['username']) {
    $teacher_id = $_SESSION['user_id'];
    $teacher_name = $_SESSION['username'];
    if (isset($_POST['add_exam'])) {
        $subject_id = $_POST['subject'];
        $_SESSION['subject_id'] = $subject_id;
    }
} else {
    header("location:../components/login.php");
    exit;
} ?>
<?php
if (isset($_POST['add_question'])) {
    $id = unique_id();
    $question = $_POST['question'];
    $a = $_POST['a'];
    $b = $_POST['b'];
    $c = $_POST['c'];
    $d = $_POST['d'];
    $e = $_POST['e'];
    $answer = $_POST['answer'];
    $status = 'active';
    $subject_id = $_SESSION['subject_id'];
    $subject_name = get_subject_live($_SESSION['subject_id'])['name'];
    $sql_check = "SELECT * FROM exam WHERE question = '$question' AND subject_id='$subject_id'";
    $result = $conn->query($sql_check);

    if ($result->num_rows == 0) {
        add_question($subject_id, $teacher_id, $subject_name, $teacher_name, $status, $question, $a, $b, $c, $d, $e, $answer, $id);
    } else {
        echo "<script>alert('هذا السؤال موجود بالفعل في نفس الورقة الامتحانية');</script>";
        header('refresh:0.5;URL=add_exam.php');
    }
}
if (isset($_POST['delete_question'])) {
    $id = $_POST['id'];
    $sql = "DELETE FROM exam WHERE id='$id'";
    $conn->query($sql);
    header('location:add_exam.php');
}

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>add_exam</title>
    <link rel="stylesheet" href="../css/style.css">
    <!-- font awesome cdn link  -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css">
    <script defer src="../js/script.js"></script> <!-- تحميل السكربت بعد تحميل DOM -->
</head>

<body>
    <!-- هيدر المدرس -->
    <header class="header">
        <div class="logo-div">
            <a href="../teacher/teacher_dashboard.php" class="logo"><img src="../images/logo.png" alt="لوغو المنصة"> اسم المنصة</a>
        </div>
        <div class="search-div">
            <form action="#" method="post" class="search-form">
                <input type="search" name="search-input" placeholder="ابحث هنا..." required maxlength="100">
                <button type="submit" class="search-btn btn" name="search-btn">بحث</button>
            </form>
        </div>
        <div class="icons">
            <!-- <div id="search-btn" class="search-btn" onclick="show_search_block()">بحث</div> -->
            <button id="toggle-btn" class="toggle-btn" onclick="darkmode(this)">🌙</button>
            <!-- <div id="language-btn" class="language-btn" onclick="translatePage()">الترجمة</div> -->
        </div>
    </header>

    <div class="form-div">
        <form action="" method="post">
            <h3 class="title">اسئلة <?= get_subject_live($_SESSION['subject_id'])['name']; ?></h3>
            <div class="form-row">
                <div class="form-group">
                    <label for="">السؤال: <span>*</span></label>
                    <input type="text" name='question' placeholder="اكتب السؤال هنا..." required>
                </div>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label for="">الإجابة a: <span>*</span></label>
                    <input type="text" name='a' placeholder="اكتب الخيار هنا..." required>
                </div>
                <div class="form-group">
                    <label for="">الإجابة b: <span>*</span></label>
                    <input type="text" name='b' placeholder="اكتب الخيار هنا..." required>
                </div>
                <div class="form-group">
                    <label for="">الإجابة c: <span>*</span></label>
                    <input type="text" name='c' placeholder="اكتب الخيار هنا..." required>
                </div>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label for="">الإجابة d: <span>*</span></label>
                    <input type="text" name='d' placeholder="اكتب الخيار هنا..." required>
                </div>
                <div class="form-group">
                    <label for="">الإجابة e: <span>*</span></label>
                    <input type="text" name='e' placeholder="اكتب الخيار هنا..." required>
                </div>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label for="">الاجابة الصحيحة: <span>*</span></label>
                    <input type="text" placeholder="اكتب الاجابة الصحيحة..." name="answer" required>
                </div>
            </div>
            <input type="submit" name="add_question" class="btn" value="حفظ السؤال">
        </form>
    </div>
    <div class="table-div">
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
                <?php foreach (exam_check($_SESSION['subject_id'], $_SESSION['user_id']) as $index => $q): ?>
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
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <a href="back.php" class="back-btn"><span class="icon"><i class="fa-solid fa-chevron-left"></i></span></a>

    <!-- الفوتر -->
    <?php include '../components/footer.php'; ?>
</body>

</html>