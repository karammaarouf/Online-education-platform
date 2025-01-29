<?php include("connect.php");?>
<?php  

$teacher_id='QNvObrKTGS';
$subject_id='1rpsxqDadb';
    function get_stu_sub($teacher_id){
        global $conn;
        $sql = "SELECT*FROM subjects,teacher_subject WHERE teacher_subject.subject_id=subjects.id AND teacher_subject.teacher_id='$teacher_id'";
        $subs= $conn->query($sql);
        $result = [];
        if ($subs->num_rows > 0) {
            foreach ($subs as $row) {
                $sub_id = $row["id"];
                $sql="SELECT * FROM user_accounts,student_subject WHERE student_subject.student_id=user_accounts.id AND student_subject.subject_id='$sub_id'";
                $res=$conn->query($sql);
                if ($res->num_rows > 0) {
                    while ($row1 = $res->fetch_assoc()) {
                        $result[] = $row1;
                    }
                }
               
            }
        }
        return $result;

    }
    function get_stu_exam_sub($subject_id){
        global $conn;
        $sql = "SELECT * FROM user_accounts,student_subject WHERE student_subject.student_id=user_accounts.id AND student_subject.subject_id='$subject_id'";
        $students = $conn->query($sql);
        return $students;
    }

foreach (get_stu_exam_sub($subject_id) as $row) {
    print($row['name']);
}






?>