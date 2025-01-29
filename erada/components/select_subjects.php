<?php include 'connect.php'; ?>
<?php
session_start();
if (isset($_SESSION['academic-year'])) {
    $acad = $_SESSION['academic-year'];
    $user_id = $_SESSION['user_id'];

    if (isset($_POST['submit'])) {
        $subjects = $_POST['subjects'];

        foreach ($subjects as $subject) {
            $sql = "INSERT INTO student_subject (student_id,subject_id) VALUES ('$user_id','$subject')";
            $conn->query($sql);
        }
        header("location:parent_register.php");
        exit();
    }
} else {
    // header('location:register.php');
}

?>
<?php
function  get_subjects()
{
    global $conn, $acad;
    $sql = "SELECT * FROM subjects WHERE academic_year='$acad'";
    $result = $conn->query($sql);
    return $result;
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>select_subjects</title>
    <link rel="stylesheet" href="../css/style.css">
    <!-- font awesome cdn link  -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css">
</head>

<body class="body_bg">
    <header class="header">
        <div class="logo-div">
            <a href="../index.php" class="logo"><img src="../images/logo.png" alt="لوغو المنصة"> اسم المنصة</a>
        </div>
        <div class="icons">
            <button id="toggle-btn" class="toggle-btn" onclick="darkmode(this)">🌙</button>
        </div>
    </header>


    <div class="form-div">
        <form action="" method="post">
            <h1>اختر المواد</h1>
            <div class="form-row">
                <div class="form-group check_input">
                    <?php $subjects = get_subjects(); ?>
                    <label for="" class="title"><?php if ($acad == 'primary-school')
                                                    echo 'مواد المرحلة الابتدائية';
                                                if ($acad == 'middle-school')
                                                    echo 'مواد المرحلة الاعدادية';
                                                if ($acad == 'high-school')
                                                    echo 'مواد المرحلة الثانوية'; ?></label>
                    <?php foreach ($subjects as $subject): ?>
                        <input type='checkbox' name='subjects[]' value=<?= $subject['id'] ?>>
                        <label><?= $subject['name'] ?></label><br>
                    <?php endforeach; ?>
                </div>
            </div>
            <input type="submit" name="submit" class="btn" value="تسجيل المواد">
        </form>
    </div>
    <a href="parent_register.php" class="skip">تخطي</a>

    <!-- الفوتر -->
    <?php include 'footer.php'; ?>
    <script src="../js/script.js"></script>
</body>

</html>