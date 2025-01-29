<?php include("connect.php");  ?>
<?php session_start();
function get_teacher_info($teacher_id)
{
    global $conn;
    $sql = "SELECT * FROM user_accounts WHERE id='$teacher_id'";
    $result = $conn->query($sql);
    $row = $result->fetch_assoc();
    return $row;
}
function get_subjects($teacher_id)
{
    global $conn;
    $sql = "SELECT * FROM subjects,teacher_subject WHERE teacher_subject.subject_id=subjects.id AND teacher_subject.teacher_id = '$teacher_id'";
    $result = $conn->query($sql);
    return $result;
}
function get_lectures($subject_id, $teacher_id)
{
    global $conn;
    $sql = "SELECT * FROM lectures WHERE subject_id='$subject_id' AND teacher_id='$teacher_id'";
    $result = $conn->query($sql);
    return $result;
}
function get_live_lectures($subject_id, $teacher_id)
{
    global $conn;
    $sql = "SELECT * FROM live WHERE subject_id='$subject_id' AND teacher_id='$teacher_id'";
    $result = $conn->query($sql);
    return $result;
}
function get_exam_start($subject_id, $teacher_id)
{
    global $conn;
    $sql = "SELECT * FROM exam_start WHERE subject_id='$subject_id' AND teacher_id='$teacher_id'";
    $result = $conn->query($sql);
    return $result;
}
function get_homewarke($subject_id, $teacher_id)
{
    global $conn;
    $sql = "SELECT * FROM homewarke WHERE subject_id='$subject_id' AND teacher_id='$teacher_id'";
    $result = $conn->query($sql);
    return $result;
}

if (isset($_POST['teacher_report'])) {
    $teacher_id = $_POST['teacher'];
} ?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>report</title>
    <link rel="stylesheet" href="../css/style.css">
               <!-- font awesome cdn link  -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css">
</head>

<body style="background-image: none; background-color: white; direction:rtl; ">
    <div class="head_report1">
        <div class="head_report">
            <div class="title_report">
                <img src="../images/logo.png" alt="">
                <h6>إرادة</h6>
            </div>
            <div class="title_report">
                <h1>|تقرير المدرس|</h1>
                <h2>|<?= get_teacher_info($teacher_id)['name'] ?>|</h2>
            </div>
            <div class="title_report">
                <p>الجمهورية العربية السورية</p>
                <p>وزارة التعليم</p>
                <p> مناطق الشمال المحرر</p>
                <p>جامعة حلب الحرة</p>
                <p>منصة إرادة</p>
            </div>
        </div>


        <div class="table_report">
            <table>
                <thead>
                    <tr>
                        <th> التاريخ</th>
                        <th> نوع التقرير</th>
                        <th> السنة</th>
                        <th> الوكيل</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td><?= date("Y-m-d") ?></td>
                        <td>عام</td>
                        <td><?= date("Y") ?></td>
                        <td><?= get_teacher_info($teacher_id)['name'] ?></td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div class="table_report2">
            <table>
                <thead>
                    <tr>
                        <th>المواد</th>
                        <th>عدد المحاضرات</th>
                        <th>الجلسات الاونلاين</th>
                        <th>الامتحانات</th>
                        <th>الانشطة</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach (get_subjects($teacher_id) as $subject) : ?>
                        <tr>
                            <td><?= $subject['name'] ?></td>
                            <td><?= get_lectures($subject['id'], $teacher_id)->num_rows ?></td>
                            <td><?= get_live_lectures($subject['id'], $teacher_id)->num_rows ?></td>
                            <td><?= get_exam_start($subject['id'], $teacher_id)->num_rows ?></td>
                            <td><?= get_homewarke($subject['id'], $teacher_id)->num_rows ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>


        <div class="copy">
            <h1>توقيع المدير</h1>
            <button class="btn" onclick="printReport()">طباعة <span class="icon"><i class="fa-solid fa-print"></i></span></button>

        </div>

        <script>
            function printReport() {
                window.print();
            }
        </script>
    </div>

    <a href="back.php" class="back-btn"><span class="icon"><i class="fa-solid fa-chevron-left"></i></span></a>

    <script src="../js/script.js"></script>

</body>

</html>