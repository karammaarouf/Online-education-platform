<?php include '../components/connect.php'; ?>
<?php session_start(); ?>
<?php if (isset($_GET['id'])) {
    $lecture_id = $_GET['id'];

    $_SESSION['lecture_id'] = $lecture_id;

    $sql = "UPDATE student_lecture SET status='yes' WHERE student_id='{$_SESSION['user_id']}' AND lecture_id='$lecture_id'";
    $conn->query($sql);
}

if (isset($_SESSION['lecture_id'])) {
    $lecture_id = $_SESSION['lecture_id'];
}
$sql = "SELECT * FROM comments WHERE lecture_id = '$lecture_id' ORDER BY `date` DESC";
$comments = $conn->query($sql);

$sql = "SELECT * FROM lectures WHERE id='$lecture_id'";
$lecture = $conn->query($sql)->fetch_assoc();

if (isset($_POST['comment_submit'])) {
    $comment = $_POST['comment'];
    $id = unique_id();
    $date = date("Y-m-d H:i:s");
    $student_id = $_SESSION['user_id'];
    $student_name = $_SESSION['username'];
    $sql = "INSERT INTO comments (id,student_id,lecture_id,student_name,comment,date) VALUES ('$id','$student_id','$lecture_id','$student_name','$comment','$date')";
    $conn->query($sql);
    header('location:' . $_SERVER['PHP_SELF']);
}

if (isset($_POST['delete_comment'])) {
    $comment_id = $_POST['comment'];
    $sql = "DELETE FROM comments WHERE id='$comment_id'";
    $conn->query($sql);
    header('location:' . $_SERVER['PHP_SELF']);
}




?>








<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>view_video</title>
    <link rel="stylesheet" href="../css/style.css">
    <!-- font awesome cdn link  -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css">

    <script defer src="../js/script.js"></script> <!-- تحميل السكربت بعد تحميل DOM -->
</head>

<body>
    <!-- هيدر المدرس -->
    <header class="header">
        <div class="logo-div">

            <a href="<?= ($_SESSION['type'] == 3 ? '../student/student_dashboard.php' : ($_SESSION['type'] == 2 ? '../teacher/teacher_dashboard.php' : ($_SESSION['type'] == 1 ? '../admin/admin_dashboard.php' : '../parent/parent_dashboard.php'))); ?>" class="logo"><img src="../images/logo.png" alt="لوغو المنصة">إرادة</a>
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
    :




    <div class="video-container">
        
            <div class="video-div">
                <div class="video-row">
                <center>
                    <video width="70%" controls loop muted poster="thumbnail.jpg">
                        <source src="<?= $lecture['video'] ?>" type="video/mp4">
                        المستعرض الخاص بك لا يدعم عرض الفيديو.
                    </video>
                    </center>
                </div>
                <div class="video-row">
                    <h2>
                        <?= $lecture['title'] ?>
                    </h2>
                    <h4 class="description"><?= $lecture['description'] ?></h4>
                    <a href='../components/download.php?&file=<?= $lecture['file'] ?>' download>تحميل الملفات <span class="green-icon"><i class="fa-solid fa-download"></i></span></a>
                </div>
        
        <br>

        <div class="video-row">
            <div class="comments-div">
                <div class="form-div">
                    <form action="" method="post">
                        <div class="form-row">
                            <div class="form-group">
                                <input class="comments" name="comment" placeholder="اكتب تعليقاً...">
                            </div>
                        </div>
                        <input type="submit" name="comment_submit" class="btn" value="تعليق">
                    </form>
                </div>
                <?php foreach ($comments as $comment): ?>
                    <div class="comments-label">
                        <div class="name_date">
                            <label for="comments"><?= $comment['student_name'] ?></label>
                            <label for=""><?= $comment['date'] ?></label>
                        </div>
                        <div class="comment">
                            <p><?= $comment['comment'] ?></p>
                        </div>
                        <form action="" method="post">
                            <input type="hidden" name="comment" value="<?= $comment['id'] ?>">
                            <?php if ($comment['student_id'] == $_SESSION['user_id'] || $_SESSION['type'] == 2) : ?>
                                <button type="submit" name="delete_comment" class="delete-btn"><span class="icon"><i class="fa-solid fa-trash"></i></span></button>
                            <?php endif ?>
                        </form>
                    </div>
                <?php endforeach ?>
            </div>
        </div>

    </div>
    </div>






    <a href="back.php" class="back-btn"><span class="icon"><i class="fa-solid fa-chevron-left"></i></span></a>
    <!-- الفوتر -->
    <?php include '../components/footer.php'; ?>

    <script src="../js/script.js"></script>
</body>

</html>