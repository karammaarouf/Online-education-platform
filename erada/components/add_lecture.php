<?php include '../components/connect.php' ?>
<?php include '../components/send_email.php'?>
<?php session_start(); //بدء الجلسة
 if(!isset($_SESSION['username'])){
    header("location: ../components/login.php");
    $conn->close();
    exit;
}
else{
    //تعود معلومات الطلاب الاساسية و تعرض بال foreach
    function get_students($subject_id){
        global $conn;
        $sql="SELECT student_id FROM student_subject WHERE subject_id='$subject_id'";
        //نحضر جميع ايديهات الطلاب الذين سجلوا على نفس المادة المختارة
        $result=$conn->query($sql);
        if($result->num_rows>0){
            $students=[];
        while($row=$result->fetch_assoc()){
            $id=$row['student_id'];
            $sql="SELECT * FROM user_accounts WHERE id='$id'";
            $students[]=$conn->query($sql)->fetch_assoc();
        }
    }
    return $students;
    }
    //لاحضار اسم المادة حسب الايدي فقط
    function get_subject_name($subject_id){
        global $conn;
        $sql="SELECT name FROM subjects WHERE id ='$subject_id'";
        $result=$conn->query($sql);
        return $result->fetch_assoc()['name'];
    }
    // تعبة جدول الحضور باسماء الطلاب المشتركين بنفس المادة
    function set_table_lecture($student_id,$student_name,$lecture_id,$subject_id,$subject_name,$status){
        global $conn;
        $sql="INSERT INTO student_lecture (student_id,lecture_id,student_name,subject_id,subject_name,status) VALUES ('$student_id','$lecture_id','$student_name','$subject_id','$subject_name','$status')";
        $conn->query($sql);
    }

//دالة للاستعلام عن بيانات الطالب حسب الايدي
function get_student($student_id){
    global $conn;
    $sql= "SELECT * FROM user_accounts WHERE id='$student_id'";
    $result=$conn->query($sql);
    return $result->fetch_assoc();
}










    // في حال تم رفع محاضرة مسجلة
    if(isset($_POST['submit'])){
        $id=unique_id();
        $title=$_POST['title'];
        $description=$_POST['description'];
        $type=$_POST['type'];

        // معلومات الملف
        $file = $_FILES['file']['name'];//اسم الملف
        $file = filter_var($file, FILTER_SANITIZE_STRING);//اذالة الرموز غير المرغوب بها من اسم الملف
        $ext = pathinfo($file, PATHINFO_EXTENSION);//الحصول على امتداد الملف
        $rename = unique_id() . '.' . $ext;//نقوم بإنشاء مسمى جديد و فريد للملف و ندمجمه مع الامتداد الاصلي للملف
        $file_size = $_FILES['file']['size'];//حجم الملف
        $file_tmp_name = $_FILES['file']['tmp_name'];//المكان المؤقت الذي يتم فيه حفظ الملف
        $file_folder = '../uploaded_files/' . $rename;//المسار مع اسم الملف الجديد
        if (!empty($file)) {
            if ($file_size < 20000000) {//20ميغابايت
                move_uploaded_file($file_tmp_name, $file_folder);//نقل الملف من المسار المؤقت الى المسار النهائي
            }
        }

        //معلومات الصورة
        $photo=$_FILES['photo']['name'];//اسم الصورة
        $photo = filter_var($photo, FILTER_SANITIZE_STRING);//اذالة الرموز غير المرغوب بها من اسم الصورة
        $ext = pathinfo($photo, PATHINFO_EXTENSION);//الحصول على امتداد الصورة
        $rename = unique_id() . '.' . $ext;//نقوم بإنشاء مسمى جديد و فريد للصورة و ندمجمه مع الامتداد الاصلي للصورة
        $photo_size = $_FILES['photo']['size'];//حجم الصورة
        $photo_tmp_name = $_FILES['photo']['tmp_name'];//المكان المؤقت الذي يتم فيه حفظ الصورة
        $photo_folder = '../uploaded_files/' . $rename;//المسار مع اسم الصورة الجديد
        if (!empty($photo)) {
            if ($photo_size < 2000000) {//2ميغابايت
                move_uploaded_file($photo_tmp_name, $photo_folder);//نقل الصورة من المسار المؤقت الى المسار النهائي
            }
        }

        //معلومات الفيديو
        $video=$_FILES['video']['name']; //اسم الفيديو
        $video = filter_var($video, FILTER_SANITIZE_STRING);//اذالة الرموز غير المرغوب بها من اسم الفيديو
        $ext = pathinfo($video, PATHINFO_EXTENSION);//الحصول على امتداد الفيديو
        $rename = unique_id() . '.' . $ext;//نقوم بإنشاء مسمى جديد و فريد للفيديو و ندمجمه مع الامتداد الاصلي للفيديو
        $video_size = $_FILES['video']['size'];//حجم الفيديو
        $video_tmp_name = $_FILES['video']['tmp_name'];//المكان المؤقت الذي يتم فيه حفظ الفيديو
        $video_folder = '../uploaded_files/' . $rename;//المسار مع اسم الفيديو الجديد
        if (!empty($video)) {
            if ($video_size < 200000000) {//200ميغابايت
                move_uploaded_file($video_tmp_name, $video_folder);//نقل الفيديو من المسار المؤقت الى المسار النهائي
            }
        }
        
        $subject_id=$_POST['subject_id'];
        $subject_name=get_subject_name($subject_id);
        $teacher_id=$_SESSION['user_id'];
        //جدول المحاضرة
        $sql="INSERT INTO lectures ( id,title,description,file,photo,video,subject_id,teacher_id,type ) VALUES('$id','$title','$description','$file_folder','$photo_folder','$video_folder','$subject_id','$teacher_id','$type')";
        $conn->query($sql);
        //جدوب الحضور
        foreach(get_students($subject_id) as $std){
            $student_id= $std['id'];
            $student_name=$std['name'];
            $status='no';
            set_table_lecture($student_id,$student_name,$id,$subject_id,$subject_name,$status);
            
        }
        $conn->close();
        echo "<script>alert('تم اضافة المحاضرة بنجاح');</script>";
        header("refresh:0.5;URL=../teacher/teacher_dashboard.php");
    }


/**###################################################################################################### */

    // في حال تم رفع محاضرة اونلاين
    if(isset($_POST['live_submit'])){
        $id=unique_id();
        $title=$_POST['title'];
        $description=$_POST['description'];
        $type=$_POST['type'];
        

        // معلومات الملف
        $file = $_FILES['file']['name'];//اسم الملف
        $file = filter_var($file, FILTER_SANITIZE_STRING);//اذالة الرموز غير المرغوب بها من اسم الملف
        $ext = pathinfo($file, PATHINFO_EXTENSION);//الحصول على امتداد الملف
        $rename = unique_id() . '.' . $ext;//نقوم بإنشاء مسمى جديد و فريد للملف و ندمجمه مع الامتداد الاصلي للملف
        $file_size = $_FILES['file']['size'];//حجم الملف
        $file_tmp_name = $_FILES['file']['tmp_name'];//المكان المؤقت الذي يتم فيه حفظ الملف
        $file_folder = '../uploaded_files/' . $rename;//المسار مع اسم الملف الجديد
        if (!empty($file)) {
            if ($file_size < 20000000) {//20ميغابايت
                move_uploaded_file($file_tmp_name, $file_folder);//نقل الملف من المسار المؤقت الى المسار النهائي
            }
        }

        //معلومات الصورة
        $photo=$_FILES['photo']['name'];//اسم الصورة
        $photo = filter_var($photo, FILTER_SANITIZE_STRING);//اذالة الرموز غير المرغوب بها من اسم الصورة
        $ext = pathinfo($photo, PATHINFO_EXTENSION);//الحصول على امتداد الصورة
        $rename = unique_id() . '.' . $ext;//نقوم بإنشاء مسمى جديد و فريد للصورة و ندمجمه مع الامتداد الاصلي للصورة
        $photo_size = $_FILES['photo']['size'];//حجم الصورة
        $photo_tmp_name = $_FILES['photo']['tmp_name'];//المكان المؤقت الذي يتم فيه حفظ الصورة
        $photo_folder = '../uploaded_files/' . $rename;//المسار مع اسم الصورة الجديد
        if (!empty($photo)) {
            if ($photo_size < 10000000) {//10ميغابايت
                move_uploaded_file($photo_tmp_name, $photo_folder);//نقل الصورة من المسار المؤقت الى المسار النهائي
            }
        }

        $video_folder='../video/uploading.mp4';



        $subject_id=$_POST['subject_id'];

        $teacher_id=$_SESSION['user_id'];

        $sql="INSERT INTO lectures ( id,title,description,file,photo,video,subject_id,teacher_id,type ) VALUES('$id','$title','$description','$file_folder','$photo_folder','$video_folder','$subject_id','$teacher_id','$type')";
        $conn->query($sql);

        $link=$_POST['link'];

        // ضبط المنطقة الزمنية
        date_default_timezone_set('Asia/Damascus');
        // الحصول على التاريخ الحالي (السنة، الشهر، اليوم)
        $date = date("Y-m-d H:i:s");

        $subject_name=get_subject_name($subject_id);

        // اضافة بيانات البث المباشر ملاحظة: الحالة هي مفعل
        $sql="INSERT INTO live (link,id,subject_id,teacher_id,date) VALUES('$link','$id','$subject_id','$teacher_id','$date')";
        $conn->query($sql);
               //جدوب الحضور
               foreach(get_students($subject_id) as $std){
                $student_id= $std['id'];
                $student_name=$std['name'];
                $status='no';
                set_table_lecture($student_id,$student_name,$id,$subject_id,$subject_name,$status);
                $student_email=get_student($student_id)['email'];
                send_email($student_id,'جلسة اونلاين','<h1>هناك جلسة اونلاين نشطة الآن</h1>');
                
            }
        $conn->close();
        $_SESSION['timer']=TRUE;
        $_SESSION['live_id']=$id;
        echo "<script>alert('تم ارسال اشعار البث المباشر للطلاب');</script>";
        header("refresh:0.5;URL=../teacher/teacher_dashboard.php");
    }
}



?>  
