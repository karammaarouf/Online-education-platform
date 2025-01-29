<?php include "connect.php" ?>
<?php session_start();
if (isset($_POST["student_report"])) {
    $student_id = $_POST['student'];

}
if (isset($_GET["student_id"])) {
    $student_id = $_GET['student_id'];

}

function get_student_info($student_id)
{
    global $conn;
    $sql = "SELECT * FROM user_accounts WHERE id = '$student_id'";
    $resulr = $conn->query($sql);
    $row = $resulr->fetch_assoc();
    return $row;
}
function get_parent($student_id)
{
    global $conn;
    $sql = "SELECT * FROM user_accounts,parent_student WHERE parent_student.parent_id=user_accounts.id AND parent_student.student_id='$student_id'";
    $resulr = $conn->query($sql);
    $row = $resulr->fetch_assoc();
    return $row;
}
function get_subjects($student_id)
{
    global $conn;
    $sql = "SELECT * FROM subjects,student_subject WHERE student_subject.subject_id=subjects.id AND student_subject.student_id='$student_id'";
    $result = $conn->query($sql);
    return $result;
}
function get_lectures($student_id, $subject_id)
{
    global $conn;
    $sql = "SELECT * FROM lectures,student_lecture WHERE student_lecture.lecture_id=lectures.id AND student_lecture.student_id='$student_id' AND lectures.subject_id='$subject_id'";
    $result = $conn->query($sql);
    return $result;
}
function get_lectures_status($student_id, $subject_id)
{
    global $conn;
    $sql = "SELECT * FROM lectures,student_lecture WHERE student_lecture.lecture_id=lectures.id AND student_lecture.student_id='$student_id' AND lectures.subject_id='$subject_id' AND student_lecture.status='yes'";
    $result = $conn->query($sql);
    return $result;
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
</head>

<body style="background-image: none; background-color: white; direction:rtl; ">
    <div class="head_report1">
        <div class="head_report">
            <div class="title_report">
                <img src="../images/logo.png" alt="">
                <h6>إرادة</h6>
            </div>
            <div class="title_report">
                <h1>|تقرير الطالب|</h1>
                <h2>|<?= get_student_info($student_id)['name'] ?>|</h2>
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
                        <td>وضع الطالب</td>
                        <td><?= date("Y") ?></td>
                        <td><?= get_parent($student_id)['name'] ?></td>
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
                        <th>الحضور</th>
                        <th>التقييم</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach (get_subjects($student_id) as $subject) : ?>
                        <tr>
                            <td><?= $subject['name'] ?></td>
                            <td><?= get_lectures($student_id, $subject['id'])->num_rows ?></td>
                            <td><?= get_lectures_status($student_id, $subject['id'])->num_rows ?></td>
                            <td><?php
                                $lec = get_lectures($student_id, $subject['id'])->num_rows;
                                $yes = get_lectures_status($student_id, $subject['id'])->num_rows;
                                $avg = $lec != 0 ?  $yes / $lec * 100 : "";
                                if ($avg >= 90) {
                                    echo "ممتاز";
                                } else if ($avg >= 80) {
                                    echo "جيد جداً";
                                } else if ($avg >= 60) {
                                    echo "جيد";
                                } else if ($avg >= 0) {
                                    echo "ضعيف";
                                } else {
                                    echo "لايوجد";
                                }

                                ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <div class="div_pos">
            <div class="text_pos">
                <p> ⁂ أبرز الإيجابيات:</p>
                <div>
                    <label for="">*********</label>
                </div>
            </div>
        </div>

        <div class="div_neg">
            <div class="text_neg">
                <p> ⁂ أبرز السلبيات:</p>
                <div>
                    <label for="">*********</label>
                </div>
            </div>
        </div>

        <div class="div_not">
            <div class="text_not">
                <p> ⁂ ملاحظات المدرس:</p>
                <div>
                    <label for="">*********</label>
                </div>
            </div>
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

    <!-- <script src="../js/script.js"></script> -->

</body>

</html>