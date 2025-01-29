
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

<!-- فورم تسجيل الدخول-->
<section class="form-section">
    <div class="login-div">
        <form action="login_check.php" method="post" enctype="multipart/form-data" class="login">
        <h3>أهلا بعودتك</h3>
        <p>الأيميل <span>*</span></p>
        <input type="email" name="email" placeholder="enter your email" maxlength="50" required class="box">
        <p>كلمة المرور <span>*</span></p>
        <input type="password" name="pass" placeholder="enter your password" maxlength="20" required class="box">
        <p class="link"><a href="forget_password.php">نسيت كلمة المرور</a></p>
        <input type="submit" name="submit" value="تسجيل الدخول" class="btn">
        <p class="link">ليس لديك حساب؟ <a href="register.php">تسجيل</a></p>
        </form>
    </div>
</section>
<a href="back.php" class="back-btn"><span class="icon"><i class="fa-solid fa-chevron-left"></i></span></a>

 <!-- الفوتر -->
 <?php include 'footer.php'; ?>
<script src="../js/script.js"></script>
</body>
</html>