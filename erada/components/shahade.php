<?php include("../components/connect.php"); ?>
<?php
session_start();
//استعلام عن بيانات الحساب الاساسية من الايدي
function get_user($student_id)
{
    global $conn;
    $sql = "SELECT*FROM user_accounts WHERE id='$student_id'";
    $result = $conn->query($sql);
    $row = $result->fetch_assoc();
    return $row;
}
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
    return floor($avg * 10) / 10;;
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
//المكموع الكلي 
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


if (isset($_GET["student_id"])) {
    $student_id = $_GET["student_id"];
}

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>report</title>
    <link rel="stylesheet" href="../css/style.css">
    <!-- font awesome cdn link  -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css">
    <style>
        @page {
            size: A4 landscape;
            /* تحديد حجم الصفحة كـ A4 بالعرض */
            margin: 0;
            /* ضبط الهامش حسب الرغبة */
        }
    </style>
</head>

<body style="background-image: none; background-color: white; direction:rtl; ">
    <div class="head_shahade1">
        <div class="head_shahade">
            <div class="title_shahade2">
                <p> مناطق الشمال المحرر</p>
                <p>جامعة حلب الحرة</p>
                <p>منصة إرادة</p>

            </div>


            <div class="title_shahade">
                <img src="../images/logo.png" alt="">
                <h6> إرادة</h6>

            </div>
        </div>
        <div class="title_shahade3">
            <h1>شَهَادَةُ إِمْتِيَاز
            </h1>
        </div>
        <div class="text_first">
            <p> تــقــدم مــنــصــة إرادة الــتــعــلــيــمــيــة بـالـشــكــر والــتــقــديــر للــطــالــب/ة</p>
        </div>

        <div class="student_name">
            <div>
                <label for=""> >>>><?= get_user($student_id)['name'] ?><<<< </label>
            </div>
        </div>
        <div class="text_second">
            <p>
                على تفوقه/ا وتميزه/ا الدراسي في الفصل الدراسي الثاني لعام <?= date('Y') ?>م



                كما نتقدم بالشكر الجزيل لأسرته/ا على الاهتمام وحسن المتابعة
            </p>
            <hr>
            <p>
                سائلين الله له/ا مزيداً من التفوق والنجاح ،،
            </p>
        </div>

        <div class="table_shahade">
            <table>
                <thead>
                    <tr>
                        <th>المواد</th>
                        <?php foreach (get_student_subject($student_id)['subject_name'] as $index => $subject): ?>
                            <td><?= $subject ?></td>
                        <?php endforeach; ?>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <th>درجات النشاطات</th>
                        <?php foreach (get_student_subject($student_id)['homewarke_avg'] as $index => $subject): ?>
                            <td><?= $subject . " %" ?></td>
                        <?php endforeach; ?>
                    </tr>
                    <tr>
                        <th>درجات الإمتحانات</th>
                        <?php foreach (get_student_subject($student_id)['exam_avg'] as $index => $subject): ?>
                            <td><?= $subject . " %" ?></td>
                        <?php endforeach; ?>
                    </tr>

                </tbody>
            </table>
        </div>
        <div class="tutal">
            <p>◆ المجموع العام <?= get_total_avg($student_id) . " %" ?>.</p>
        </div>

        <div class="copy">
            <h1>توقيع المدير</h1>
            <button class="btn" onclick="printReport()">طباعة <span class="icon"><i class="fa-solid fa-print"></i></span></button>
        </div>

        <a href="back.php" class="back-btn"><span class="icon"><i class="fa-solid fa-chevron-left"></i></span></a>

        <script>
            function printReport() {
                window.print();
            }
        </script>
    </div>

    <script src="js/script.js"></script>

</body>

</html>