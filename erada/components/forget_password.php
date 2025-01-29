<?php include "connect.php"; ?>
<?php include "send_email.php"; ?>
<?php session_start();
function get_user($email){
    global $conn;
    $sql="SELECT * FROM user_accounts WHERE email='$email'";
    $result=mysqli_query($conn,$sql);
    return $result;
}
    if(isset($_POST['submit'])){
        $email=$_POST['email'];
        if(get_user( $email )->num_rows > 0){
        $code=unique_id() ;
        $_SESSION['code']= $code ;
        $_SESSION['email']= $email;
        send_email($email,'reset password code',"<h1>رمز اعادة تعيين كلمة المرور هو</h1><br><h2>$code</h2>");
        echo "<script>alert('تم إرسال كود إعادة التعيين إلى البريد الالكتروني".$email."')</script>";
        }else{
            echo"<script>alert('البريد الالكتروني غير صحيح')</script>";
            header("refresh:0.5;URL=forget_password.php");
        }
    }
    if(isset($_POST["v_submit"])){
        $code=$_SESSION["code"];
        $email=$_SESSION['email'];
        if($_POST["code"]== $code){
            header("refresh:0.5;URL=reset_password.php?email=$email");
        }
        else{

            echo "<script>alert('الكود الذي ادخلته غير صحيح')</script>";
            header("refresh:0.5;URL=forget_password.php");
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
<section class="form-section" <?php if(isset($code)) echo 'hidden'; ?>>
    <div class="login-div">
        <form action="" method="post"  class="login">
        <h3>إعادة تعيين كلمة الرور</h3>
        <p>البريد الالكتروني <span>*</span></p>
        <input type="email" name="email" placeholder="اكتب البريد الالكتروني..." maxlength="50" required class="box">
        <input type="submit" name="submit" value="ارسال رمز إعادة التعيين" class="btn">
        </form>
    </div>
</section>
<section class="form-section" <?php if(!isset($code)) echo 'hidden'; ?>>
    <div class="login-div">
        <form action="" method="post"  class="login">
        <h3>إعادة تعيين كلمة الرور</h3>
        <p>الكود<span>*</span></p>
        <input type="text" name="code" placeholder="اكتب كود إعادة التعيين..." maxlength="50" required class="box">
        <input type="submit" name="v_submit" value="التحقق" class="btn">
        </form>
    </div>
</section>

<a href="back.php" class="back-btn"><span class="icon"><i class="fa-solid fa-chevron-left"></i></span></a>
 <!-- الفوتر -->
 <?php include 'footer.php'; ?>
<script src="../js/script.js"></script>
</body>
</html>