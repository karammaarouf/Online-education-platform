<?php include "connect.php" ?>
<?php include "send_email.php"?>
<?php session_start();
function get_parent(){
    global $conn;
    $sql="SELECT * FROM user_accounts,parent_student WHERE parent_student.parent_id=user_accounts.id AND parent_student.student_id='{$_SESSION['user_id']}'";
    $result=$conn->query($sql);
    return $result;
}
if (isset($_SESSION['user_id'])) {
    if (isset($_POST['start_exam'])) {
        $_SESSION['exam_id'] = $_POST['exam_id'] ?? $_SESSION['exam_id'] ?? null;
        $_SESSION['subject_id'] = $_POST['subject_id'] ?? $_SESSION['subject_id'] ?? null;
        $_SESSION['subject_name'] = $_POST['subject_name'] ?? $_SESSION['subject_name'] ?? null;
        $_SESSION['teacher_id'] = $_POST['teacher_id'] ?? $_SESSION['teacher_id'] ?? null;
        $_SESSION['teacher_name'] = $_POST['teacher_name'] ?? $_SESSION['teacher_name'] ?? null;
        $_SESSION['time'] = $_POST['time'] ?? $_SESSION['time'] ?? null;
        $_SESSION['date'] = $_POST['date'] ?? $_SESSION['date'] ?? null;
        $_SESSION['count'] = $_POST['count'] ?? $_SESSION['count'] ?? null;
        $_SESSION['done'][] = $_SESSION['exam_id'];
        if(!isset($_SESSION['exam_send']) || $_SESSION['exam_send']!='ok'){
        foreach (get_parent()as $parent){
            send_email($parent['email'],'EXAM_notifacation',"<h1>الطالب {$_SESSION['username']} يقوم بتقديم إمتحان الآن بمادة {$_SESSION['subject_name']} , يرجى متابعة وضع الطالب  </h1>");
        }
        $_SESSION['exam_send']='ok';
    }
    }
    // جمع القيم من الجلسة
    $exam_id = $_SESSION['exam_id'];
    $subject_id = $_SESSION['subject_id'];
    $subject_name = $_SESSION['subject_name'];
    $teacher_id = $_SESSION['teacher_id'];
    $teacher_name = $_SESSION['teacher_name'];
    $time = $_SESSION['time'];
    $date = $_SESSION['date'];
    $count = $_SESSION['count'];
    $student_id = $_SESSION['user_id'];
    $student_name = $_SESSION['username'];
    //احضار الاسئلة مرة واحدة فقط
    if (!isset($_SESSION['exam_questions'])) {
        $sql = "SELECT * FROM exam WHERE subject_id='$subject_id' AND teacher_id='$teacher_id' ORDER BY  RAND() LIMIT  $count ";
        $questions = $conn->query($sql);
        if ($questions->num_rows > 0) {
            $questions = $questions->fetch_all(MYSQLI_ASSOC);
            $_SESSION['exam_questions'] = $questions;
        }
    } else {
        $questions = $_SESSION['exam_questions'];
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
    if (isset($_POST['submit_exam'])) {
        if (isset($_POST['questions']) && is_array($_POST['questions'])) {
            $studentAnswers = $_POST['questions']; // جمع الإجابات المرسلة من النموذج

            foreach ($studentAnswers as $question) {
                if (isset($question['id']) && isset($question['answers']) && is_array($question['answers'])) {
                    $questionId = $question['id']; // جمع معرف السؤال
                    $answers = $question['answers']; // جمع الإجابات المختارة من الطالب

                    // اختبار اجابة الطالب وجمع العلامة
                    $sum += (get_answer($questionId, $answers[0]) ? 1 : 0 );
                }
            }
            $avg=$sum/$count*100;
            // تحديد الحالة بناءً على العلامات
            $status = $avg >=50  ? 'ناجح' : 'راسب';
            
            $id = unique_id();

            // الاستعلام لإدخال البيانات في قاعدة البيانات
            $sql = "INSERT INTO exam_result (id, subject_id, student_id, teacher_id, student_name, subject_name, teacher_name, mark, status, exam_id) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

            // تأكد من مطابقة عدد الأنواع مع عدد المتغيرات
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("sssssssiss", $id, $subject_id, $student_id, $teacher_id, $student_name, $subject_name, $teacher_name, $avg,$status,$exam_id);
            if ($stmt->execute()) {
                // التحقق مما إذا كانت $_SESSION['done'] موجودة وتكون مصفوفة
                if (!isset($_SESSION['done']) || !is_array($_SESSION['done'])) {
                    $_SESSION['done'] = array(); // تهيئة $_SESSION['done'] كمصفوفة إذا لم تكن موجودة
                }
                $_SESSION['done'][] = $exam_id;

                echo "<script>alert('تم تسليم الورقة بنجاح');</script>";
                unset($_SESSION['exam_questions']);
                unset($_SESSION['send_exam']);
                unset($_SESSION['exam_id']);
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
                alert('إنتهى وقت الامتحان المحدد , سيتم احتساب علامات الاسئلة التي تمت الإجابة عليها فقط')
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
                    <h1>|امتحان مادة <?= $subject_name ?>|</h1>
                    <h2>| اسم الطالب : <?= $_SESSION['username'] ?>|</h2>
                    <h2>| مدرس المادة : <?= $teacher_name ?>|</h2>
                    <h2>مدة الامتحان : <span><?= $time ?></span> دقيقة</h2>
                    
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
                    <center> <button type="submit" name="submit_exam" onclick="submitExam()" id="exam_btn" class="submit_btn">تسليم الورقة</button> </center> <!-- زر الإرسال -->
                </form>
            </div>

        </div>
       
    </body>

</html>