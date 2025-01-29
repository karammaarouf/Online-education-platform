<?php include("connect.php");  ?>
<?php session_start();


if(isset($_GET["email"])){
    $email = $_GET["email"];
}


if(isset($_POST["reset_password"])){
    $pass= $_POST["pass"];
    $cpass=$_POST['c_pass'];
    $email=$_SESSION['email'];
    if ($pass == $cpass) {
        if (strlen($pass >= 8)) {
            if(preg_match('/[a-zA-Z]/', $pass)&& preg_match('/\d/', $pass)){
            $pass_hash = password_hash($pass, PASSWORD_DEFAULT);
            $sql = "UPDATE user_accounts SET password='$pass_hash' WHERE email='$email'";
            if ($conn->query($sql)) {

                echo '<script>alert("تمت إعادة تعيين كلمة المرور بنجاح, يرجى إعادة تسجيل الدخول");</script>';
                header('refresh:0.5;URL=login.php');
                exit();
            }
        }else{
            echo '<script>alert("كلمة المرور ضعيفة للغاية , استخدم مزيجاً من الأحرف و الأرقام."); window.location.href="reset_password.php";</script>';
        }
        } else {
            echo '<script>alert("كلمة المرور قصيرة جداً"); window.location.href="reset_password.php";</script>';
        }
    } else {
        echo '<script>alert("يرجى التأكد من  كلمة المرور!"); window.location.href="reset_password.php";</script>'; //في حال كانت كلمة المرور غير متطابقة  

    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>login</title>
    <link rel="stylesheet" href="../css/style.css">
               <!-- font awesome cdn link  -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css">
</head>
<body class="body_bg">

<!-- الهيدر -->
    <header class="header">
        <div class="logo-div">
            <a href="../index.php" class="logo"><img src="../images/logo.png" alt="لوغو المنصة">إرادة</a>
        </div>
        <div class="icons">
            <button id="toggle-btn" class="toggle-btn" onclick="darkmode(this)">🌙</button>
        </div>
    </header>


<!-- فورم اعادة تعيين كلمة المرور -->
<section class="form-section">
    <div class="login-div">
        <form action="" method="post"  class="login">
        <h3>إعادة تعيين كلمة الرور</h3>
        <p>كلمة المرور الجديدة<span>*</span></p>
        <input type="password" name="pass" placeholder="اكتب كلمة المرور الجديدة..." maxlength="50" required class="box">
        <p>تأكيد كلمة المرور<span>*</span></p>
        <input type="password" name="c_pass" placeholder="أعد كتابة كلمة المرور..." maxlength="50" required class="box">
        <input type="submit" name="reset_password" value="تغيير كلمة المرور" class="btn">
        </form>
    </div>
</section>


 <!-- الفوتر -->
 <?php include 'footer.php'; ?>
<script src="../js/script.js"></script>
</body>
</html>