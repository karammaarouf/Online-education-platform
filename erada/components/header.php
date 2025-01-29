<!-- رأس الصفحة الرئيسية -->
<header class="header">
    <div class="logo-div">
        <a href="index.php" class="logo"><img src="images/logo.png" alt="لوغو">إرادة</a>
    </div>

    <div class="search-div">
        <form action="#" method="post" class="search-form">
            <input type="search" name="search-input" placeholder="ابحث هنا..." required maxlength="100">
            <button type="submit" class="search-btn btn" name="search-btn">بحث</button>
        </form>
    </div>



    <div class="icons">
        <div id="search-btn" class="search-btn" onclick="show_search_block()">بحث</div>
        <div id="user-btn" class="user-btn" onclick="show_login_block()">تسجيل الدخول</div>
        <button id="toggle-btn" class="toggle-btn" onclick="darkmode(this)">🌙</button>
    </div>


</header>

<!-- مربع البحث المخفي الذي يظهر عند تصغير الشاشة -->
<div class="search-div-flex">
    <form action="#" method="post" class="search-form">
        <input type="search" name="search-input" placeholder="ابحث هنا..." required maxlength="100">
        <button type="submit" class="search-btn" name="search-btn">بحث</button>
    </form>
</div>

<!-- فورم تسجيل الدخول المخفي يظهر عند ضغط الزر -->


<div class="form-container login-form hidden">
    <form action="" method="post" enctype="multipart/form-data" class="login">
        <h3>أهلا بعودتك</h3>
        <p>الأيميل <span>*</span></p>
        <input type="email" name="email" placeholder="enter your email" maxlength="50" required class="box">
        <p>كلمة المرور <span>*</span></p>
        <input type="password" name="pass" placeholder="enter your password" maxlength="20" required class="box">
        <p class="link"><a href="register.php">نسيت كلمة المرور</a></p>
        <input type="submit" name="submit" value="تسجيل الدخول" class="btn">
        <p class="link">ليس لديك حساب؟ <a href="register.php">تسجيل</a></p>
    </form>
</div>