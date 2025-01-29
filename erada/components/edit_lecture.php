<?php include '../components/connect.php'; ?>
<?php
session_start();
$teacher_id = $_SESSION['user_id'];
$lecture_id = $_GET['id'];
//دالة الاستعلام عن كافة المواد حسب المدرس المحدد
function get_teacher_subjects($teacher_id)
{
    global $conn;
    $sql = "SELECT * FROM teacher_subject WHERE teacher_id='$teacher_id'";
    $result = $conn->query($sql);
    $subjects = [];
    if ($result->num_rows > 0) {
        foreach ($result as $subject) {
            $id = $subject['subject_id'];
            $sql = "SELECT * FROM subjects WHERE id='$id'";
            $result = $conn->query($sql);
            $subjects[] = $result->fetch_assoc();
        }
    }
    return $subjects;
}
//دالة للاستعلام عن الحاضرة المحددة
function get_lecture($lecture_id)
{
    global $conn;
    $sql = "SELECT * FROM lectures WHERE id='$lecture_id' ";
    $result = $conn->query($sql)->fetch_assoc();
    return $result;
}
$lecture = get_lecture($lecture_id);

?>
<?php

if (isset($_POST['submit'])) {
    $id = $lecture['id'];


    $subject_id = $_POST['subject_id'];
    $sql = "UPDATE lectures SET subject_id='$subject_id' WHERE id='$id'";
    $conn->query($sql);


    $title = $_POST['title'];
    $sql = "UPDATE lectures SET title='$title' WHERE id='$id'";
    $conn->query($sql);


    $description = $_POST['description'];
    $sql = "UPDATE lectures SET description='$description' WHERE id='$id'";
    $conn->query($sql);


    $file = $_FILES['file']['name'];
    $file = filter_var($file, FILTER_SANITIZE_STRING);
    $ext = pathinfo($file, PATHINFO_EXTENSION);
    $rename = unique_id() . '.' . $ext;
    $file_size = $_FILES['file']['size'];
    $file_tmp_name = $_FILES['file']['tmp_name'];
    $file_folder = '../uploaded_files/' . $rename;
    if (!empty($file)) {
        if ($file_size < 20000000) {
            move_uploaded_file($file_tmp_name, $file_folder);
            $sql = "UPDATE lectures SET file='$file_folder' WHERE id='$id'";
            $conn->query($sql);
        }
    }

    $image = $_FILES['photo']['name'];
    $image = filter_var($image, FILTER_SANITIZE_STRING);
    $ext = pathinfo($image, PATHINFO_EXTENSION);
    $rename = unique_id() . '.' . $ext;
    $image_size = $_FILES['photo']['size'];
    $image_tmp_name = $_FILES['photo']['tmp_name'];
    $image_folder = '../uploaded_files/' . $rename;
    if (!empty($image)) {
        if ($image_size < 2000000) {
            move_uploaded_file($image_tmp_name, $image_folder);
            $sql = "UPDATE lectures SET photo='$image_folder' WHERE id='$id'";
            $conn->query($sql);
        }
    }


    $video = $_FILES['video']['name'];
    $video = filter_var($video, FILTER_SANITIZE_STRING);
    $ext = pathinfo($video, PATHINFO_EXTENSION);
    $rename = unique_id() . '.' . $ext;
    $video_size = $_FILES['video']['size'];
    $video_tmp_name = $_FILES['video']['tmp_name'];
    $video_folder = '../uploaded_files/' . $rename;
    if (!empty($video)) {
        if ($video_size < 200000000) {
            move_uploaded_file($video_tmp_name, $video_folder);
            $sql = "UPDATE lectures SET video='$video_folder' WHERE id='$id'";
            $conn->query($sql);
        }
    }


    $conn->close();
    echo '<script>alert("تم تحديث بيانات المحاضرة بنجاح");</script>';
    header('refresh:0.5;url=../teacher/teacher_dashboard.php');
}


?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>update</title>
    <link rel="stylesheet" href="../css/style.css">
               <!-- font awesome cdn link  -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css">
</head>

<body>
    <div class="form-div">
        <form action="" method="post" enctype="multipart/form-data">
            <h1>تعديل محاضرة</h1>
            <div class="form-row">
                <div class="form-group">
                    <label for="">المادة: <span>*</span></label>
                    <select name="subject_id" id="">
                        <?php foreach (get_teacher_subjects($teacher_id) as $subject) : ?>
                            <option value=<?= $subject['id'] ?> <?= $lecture['subject_id'] == $subject['id'] ? ' selected' : '' ?>><?= $subject['name'] ?></option>
                        <?php endforeach ?>
                    </select>
                </div>
                <div class="form-group">
                    <label for="">العنوان: <span>*</span></label>
                    <input type="text" name="title" value="<?= $lecture['title'] ?>">
                </div>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label for="">الوصف: <span>*</span></label>
                    <input id="description" name="description" value="<?= $lecture['description'] ?>">
                </div>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label for="">الغلاف: <span>*</span></label>
                    <img src="<?= $lecture['photo'] ?>" alt="" style="width:50rem; height:30rem;border-radius: 0.5rem;">
                    <input type="file" name="photo" accept="image/*">
                </div>
                <div class="form-group">
                    <label for="">الفيديو: <span>*</span></label>
                    <center> <video width="505" controls loop muted poster="<?= $lecture['photo'] ?>">
                            <source src="<?= $lecture['video'] ?>" type="video/mp4">
                            المستعرض الخاص بك لا يدعم عرض الفيديو.
                        </video>
                    </center>
                    <input type="file" name="video" accept="video/*">
                </div>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label for="">الملف: <span>*</span></label>
                    <h2><?= $lecture['file'] ?></h2>
                    <input type="file" name="file">
                </div>
            </div>
            <button type="submit" name="submit">تعديل محاضرة</button>
        </form>
    </div>

    <a href="back.php" class="back-btn"><span class="icon"><i class="fa-solid fa-chevron-left"></i></span></a>
    <script src="../js/script.js"></script>
</body>

</html>