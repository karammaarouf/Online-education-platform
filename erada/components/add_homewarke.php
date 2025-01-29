<!-- تقديم المذاكرة للطالب -->
<?php include "connect.php" ?>
<?php session_start();
if (isset($_SESSION['user_id'])) {
    if (isset($_GET['id'])) {
        $sql = "SELECT * FROM homewarke WHERE id='{$_GET['id']}'";
        $homewarke = $conn->query($sql)->fetch_assoc();
        $_SESSION['homewarke_id'] = $homewarke['id'] ?? $_SESSION['homewarke_id'] ?? null;
        $_SESSION['subject_id'] = $homewarke['subject_id'] ?? $_SESSION['subject_id'] ?? null;
        $_SESSION['teacher_id'] = $homewarke['teacher_id'] ?? $_SESSION['teacher_id'] ?? null;
        $_SESSION['time'] = $homewarke['time'] ?? $_SESSION['time'] ?? null;
        $_SESSION['date'] = $homewarke['date'] ?? $_SESSION['date'] ?? null;
        $_SESSION['count'] = $homewarke['count'] ?? $_SESSION['count'] ?? null;
        $_SESSION['done'][] = $_SESSION['homewarke_id'];
    }
    // جمع القيم من الجلسة
    $homewarke_id = $_SESSION['homewarke_id'];
    $subject_id = $_SESSION['subject_id'];
    $teacher_id = $_SESSION['teacher_id'];
    $time = $_SESSION['time'];
    $date = $_SESSION['date'];
    $count = $_SESSION['count'];
    $student_id = $_SESSION['user_id'];
    $student_name = $_SESSION['username'];
    //احضار الاسئلة مرة واحدة فقط
    if (!isset($_SESSION['homewarke_questions'])) {
        $sql = "SELECT * FROM exam WHERE subject_id='$subject_id' AND teacher_id='$teacher_id' ORDER BY  RAND() LIMIT  $count ";
        $questions = $conn->query($sql);
        if ($questions->num_rows > 0) {
            $questions = $questions->fetch_all(MYSQLI_ASSOC);
            $_SESSION['homewarke_questions'] = $questions;
        }
    } else {
        $questions = $_SESSION['homewarke_questions'];
    }

    // مجموع العلامات
    $sum = 0;

    // حساب العلامات
    function get_answer($question_id, $student_answer)
    {
        global $conn;
        $sql = "SELECT * FROM exam WHERE id=?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $question_id);
        $stmt->execute();
        $answer = $stmt->get_result()->fetch_assoc();
        $stmt->close();

        if ($answer && $answer['answer'] === $student_answer) {
            return TRUE;
        } else {
            return FALSE;
        }
    }

    // تسليم الورقة
    if (isset($_POST['submit_homewarke'])) {
        if (isset($_POST['questions']) && is_array($_POST['questions'])) {
            $studentAnswers = $_POST['questions']; // جمع الإجابات المرسلة من النموذج

            foreach ($studentAnswers as $question) {
                if (isset($question['id']) && isset($question['answers']) && is_array($question['answers'])) {
                    $questionId = $question['id']; // جمع معرف السؤال
                    $answers = $question['answers']; // جمع الإجابات المختارة من الطالب

                    // اختبار اجابة الطالب وجمع العلامة
                    $sum += (get_answer($questionId, $answers[0]) ? 1 : 0);
                }
            }
            $avg = $sum / $count * 100;
            $type = 'مؤتمت';

            $id = unique_id();

            // الاستعلام لإدخال البيانات في قاعدة البيانات
            $sql = "INSERT INTO upload_homewarke (id,subject_id,student_id, teacher_id, mark, type, homewarke_id) 
                        VALUES (?, ?, ?, ?, ?, ?, ?)";

            // تأكد من مطابقة عدد الأنواع مع عدد المتغيرات
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ssssiss", $id, $subject_id, $student_id, $teacher_id, $avg, $type, $homewarke_id);
            if ($stmt->execute()) {
                // التحقق مما إذا كانت $_SESSION['done'] موجودة وتكون مصفوفة
                if (!isset($_SESSION['done-homewarke']) || !is_array($_SESSION['done-homewarke'])) {
                    $_SESSION['done-homewarke'] = array(); // تهيئة $_SESSION['done'] كمصفوفة إذا لم تكن موجودة
                }
                $_SESSION['done-homewarke'][] = $exam_id;

                echo "<script>alert('تم تسليم ورقة المذاكرة بنجاح');</script>";
                unset($_SESSION['homewarke_questions']);
                unset($_SESSION['exam_questions']);
                unset($_SESSION['homewarke_id']);
                unset($_SESSION['subject_id']);
                unset($_SESSION['teacher_id']);
                unset($_SESSION['teacher_name']);
                unset($_SESSION['time']);
                unset($_SESSION['date']);
                unset($_SESSION['count']);
                header("refresh:0.5;URL=../student/student_dashboard.php");
            } else {
                echo "Error: " . $stmt->error; // عرض الخطأ إن وجد
            }

            $stmt->close();
            $conn->close();
        } else {
            echo "Invalid questions data.";
        }
    } else {
        echo "No exam submission detected.";
    }
} else {
    header("location:../index.php");
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>exam</title>
    <link rel="stylesheet" href="../css/style.css">
               <!-- font awesome cdn link  -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css">
    <script>
        // إعداد المؤقت
        var totalTime = <?= $time ?>*60; // 30 دقيقة (بالثواني)
        var timeLeft;

        // استعادة الوقت المتبقي من localStorage أو البدء من جديد
        if (localStorage.getItem('timeLeft')) {
            timeLeft = localStorage.getItem('timeLeft');
        } else {
            timeLeft = totalTime;
        }

        // دالة لتحديث المؤقت
        function updateTimer() {
            var minutes = Math.floor(timeLeft / 60);
            var seconds = timeLeft % 60;
            document.getElementById("timer").innerHTML = minutes + ":" + (seconds < 10 ? "0" : "") + seconds;

            timeLeft--;
            localStorage.setItem('timeLeft', timeLeft); // حفظ الوقت المتبقي في localStorage

            // إذا انتهى الوقت
            if (timeLeft < 0) {
                clearInterval(timerInterval);
                localStorage.removeItem('timeLeft'); // إزالة الوقت المتبقي من localStorage
                alert('إنتهى وقت المذاكرة المحدد , سيتم احتساب علامات الاسئلة التي تمت الإجابة عليها فقط')

                document.getElementById("exam_btn").click(); // إرسال الامتحان تلقائيًا
            }
        }

        // تحديث المؤقت كل ثانية
        var timerInterval = setInterval(updateTimer, 1000);

        // إزالة الوقت المتبقي عند إرسال الامتحان يدويًا
        function submitExam() {
            localStorage.removeItem('timeLeft');
        }
    </script>
</head>

<body style="background-image: none; background-color: white; direction:rtl; ">
<h2 class="exam_timer">  <span id="timer">الوقت المتبقي</span></h2>
    <div class="head_exam1">
        <div class="head_exam">
            <div class="title_exam">
                <img src="../images/logo.png" alt="">
                <h6>إرادة</h6>
            </div>
            <div class="title_exam">
                <h1>|مذاكرة|</h1>
                <h2>| اسم الطالب : <?= $_SESSION['username'] ?>|</h2>
                <h2>مدة الامتحان : <?= $time ?> دقيقة</h2>
                <h2>العلامة : 100 </h2>
            </div>
            <div class="title_exam">
                <p>الجمهورية العربية السورية</p>
                <p>وزارة التعليم</p>
                <p> مناطق الشمال المحرر</p>
                <p>جامعة حلب الحرة</p>
                <p>منصة إرادة</p>
            </div>
        </div>

        <div class="form-exam">
            <form action="" method="post" id="examForm"> <!-- action تحدد الملف الذي ستذهب إليه البيانات بعد الضغط على Submit -->
                <?php foreach ($questions as $index => $question): ?> <!-- $index يمثل رقم السؤال في المصفوفة $questions -->
                    <div class="exam-row">
                        <div class="exam-group">
                            <div class="q-div">
                                <label style="color:gray"><?= $index + 1 ?>)</label> <!-- نص السؤال -->
                                <label><?= $question['question'] ?></label> <!-- نص السؤال -->

                                <input type="hidden" name="questions[<?= $index ?>][id]" value="<?= $question['id'] ?>"> <!-- معرف السؤال (مخفي) -->
                            </div>
                            <div class="a-div">
                                <!-- كل خيار له اسم مرتبط بالسؤال -->
                                <div>
                                    <label><?= $question['a'] ?></label>
                                    <input type="radio" name="questions[<?= $index ?>][answers][]" value="<?= $question['a'] ?>">
                                </div>
                                <div>
                                    <label><?= $question['b'] ?></label>
                                    <input type="radio" name="questions[<?= $index ?>][answers][]" value="<?= $question['b'] ?>">
                                </div>
                                <div>
                                    <label><?= $question['c'] ?></label>
                                    <input type="radio" name="questions[<?= $index ?>][answers][]" value="<?= $question['c'] ?>">
                                </div>
                                <div>
                                    <label><?= $question['d'] ?></label>
                                    <input type="radio" name="questions[<?= $index ?>][answers][]" value="<?= $question['d'] ?>">
                                </div>
                                <div>
                                    <label><?= $question['e'] ?></label>
                                    <input type="radio" name="questions[<?= $index ?>][answers][]" value="<?= $question['e'] ?>">
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach ?>
                <center> <button type="submit" name="submit_homewarke" onclick="submitExam()" id="exam_btn" class="submit_btn">تسليم الورقة</button> </center> <!-- زر الإرسال -->
            </form>
        </div>
    </div>
    
</body>

</html>