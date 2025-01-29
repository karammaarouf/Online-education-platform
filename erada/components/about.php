<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/style.css">
               <!-- font awesome cdn link  -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css">
    <title>about</title>
</head>

<body>
    <header class="header">
        <div class="logo-div">
            <a href="../admin/admin_dashboard.php" class="logo"><img src="../images/logo.png" alt="لوغو المنصة">إرادة</a>
        </div>
        <div class="search-div">
            <form action="#" method="post" class="search-form">
                <input type="search" name="search-input" placeholder="ابحث هنا..." required maxlength="100">
                <button type="submit" class="search-btn btn" name="search-btn">بحث</button>
            </form>
        </div>
        <div class="icons">
            <!-- <div id="search-btn" class="search-btn" onclick="show_search_block()">بحث</div> -->
            <button id="toggle-btn" class="toggle-btn" onclick="darkmode(this)">🌙</button>
            <!-- <div id="language-btn" class="language-btn" onclick="translatePage()">الترجمة</div> -->
        </div>
    </header>


    <div class="about">
        <div class="row1">

            <div class="image">
                <img src="../images/4.svg" alt="">
            </div>
            <div class="why1">
                <h1>لماذا تختارنا ؟</h1>
                <p> نقدم في هذه المنصة جميع الكورسات الرائعة والتدريبات المبتكرة لتنمية مهارات الطلاب وكل هذا نقدمه بشكل مجاني مع نخبة من المدرسين المختصين </p>
                <a href="register.php"><button class="btn">إنضم إلينا</button></a>
            </div>
        </div>

        <div class="why">
            <h2>نبذة عن المنصة</h2>
            <p>منصتنا التعليمية تأسست في عام [سنة التأسيس] بهدف تقديم تعليم عالي الجودة عبر الإنترنت.<br> نقدم مجموعة متنوعة من الدورات التي تغطي مواضيع متعددة وتستهدف جميع المستويات التعليمية.</p>
        </div>

        <!-- <div class="why">
            <h2>رؤيتنا ورسالتنا</h2>
            <p>رؤيتنا هي [الرؤية]. رسالتنا هي [الرسالة]. نحن ملتزمون بتوفير تعليم متميز ومتاح للجميع.</p>
        </div> -->
        <div class="row2">

            <div class="image">
                <img src="../images/1.svg" alt="">
            </div>
            <div class="why">
                <h2>الفريق المؤسس</h2>
                <ul>
                    <li>
                        كرم معروف
                    </li>
                    <li>
                        حسن شحادي
                    </li>

                </ul>
            </div>
        </div>

        <div class="why">
            <h2>شهادات وتجارب المستخدمين</h2>
            <p>إليك بعض التعليقات من طلابنا السابقين: [شهادات وتجارب المستخدمين]</p>
        </div>
        <div class="why">
            <h2>الإحصائيات والإنجازات</h2>
            <p>حتى الآن، قمنا بتدريب [عدد الطلاب] طلاب وأصدرنا [عدد الشهادات] شهادات. حصلنا على [الجوائز والتقديرات] تقديرات وجوائز.</p>
        </div>
        <div class="why">
            <h2>التكنولوجيا المستخدمة</h2>
            <p>نستخدم أحدث التقنيات في التعليم عبر الإنترنت لضمان تجربة تعليمية سلسة وممتعة.</p>
        </div>
        <div class="why">
            <h2> للتواصل معنا</h2>
        </div>
        <div class="icoon">
            <a href=""> <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"><!--!Font Awesome Free 6.6.0 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2024 Fonticons, Inc.-->
                    <path d="M280 0C408.1 0 512 103.9 512 232c0 13.3-10.7 24-24 24s-24-10.7-24-24c0-101.6-82.4-184-184-184c-13.3 0-24-10.7-24-24s10.7-24 24-24zm8 192a32 32 0 1 1 0 64 32 32 0 1 1 0-64zm-32-72c0-13.3 10.7-24 24-24c75.1 0 136 60.9 136 136c0 13.3-10.7 24-24 24s-24-10.7-24-24c0-48.6-39.4-88-88-88c-13.3 0-24-10.7-24-24zM117.5 1.4c19.4-5.3 39.7 4.6 47.4 23.2l40 96c6.8 16.3 2.1 35.2-11.6 46.3L144 207.3c33.3 70.4 90.3 127.4 160.7 160.7L345 318.7c11.2-13.7 30-18.4 46.3-11.6l96 40c18.6 7.7 28.5 28 23.2 47.4l-24 88C481.8 499.9 466 512 448 512C200.6 512 0 311.4 0 64C0 46 12.1 30.2 29.5 25.4l88-24z" />
                </svg></a>
            <a href=""><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"><!--!Font Awesome Free 6.6.0 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2024 Fonticons, Inc.-->
                    <path d="M512 256C512 114.6 397.4 0 256 0S0 114.6 0 256C0 376 82.7 476.8 194.2 504.5V334.2H141.4V256h52.8V222.3c0-87.1 39.4-127.5 125-127.5c16.2 0 44.2 3.2 55.7 6.4V172c-6-.6-16.5-1-29.6-1c-42 0-58.2 15.9-58.2 57.2V256h83.6l-14.4 78.2H287V510.1C413.8 494.8 512 386.9 512 256h0z" />
                </svg></a>
            <a href=""><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512"><!--!Font Awesome Free 6.6.0 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2024 Fonticons, Inc.-->
                    <path d="M92.1 254.6c0 24.9 7 49.2 20.2 70.1l3.1 5-13.3 48.6L152 365.2l4.8 2.9c20.2 12 43.4 18.4 67.1 18.4h.1c72.6 0 133.3-59.1 133.3-131.8c0-35.2-15.2-68.3-40.1-93.2c-25-25-58-38.7-93.2-38.7c-72.7 0-131.8 59.1-131.9 131.8zM274.8 330c-12.6 1.9-22.4 .9-47.5-9.9c-36.8-15.9-61.8-51.5-66.9-58.7c-.4-.6-.7-.9-.8-1.1c-2-2.6-16.2-21.5-16.2-41c0-18.4 9-27.9 13.2-32.3c.3-.3 .5-.5 .7-.8c3.6-4 7.9-5 10.6-5c2.6 0 5.3 0 7.6 .1c.3 0 .5 0 .8 0c2.3 0 5.2 0 8.1 6.8c1.2 2.9 3 7.3 4.9 11.8c3.3 8 6.7 16.3 7.3 17.6c1 2 1.7 4.3 .3 6.9c-3.4 6.8-6.9 10.4-9.3 13c-3.1 3.2-4.5 4.7-2.3 8.6c15.3 26.3 30.6 35.4 53.9 47.1c4 2 6.3 1.7 8.6-1c2.3-2.6 9.9-11.6 12.5-15.5c2.6-4 5.3-3.3 8.9-2s23.1 10.9 27.1 12.9c.8 .4 1.5 .7 2.1 1c2.8 1.4 4.7 2.3 5.5 3.6c.9 1.9 .9 9.9-2.4 19.1c-3.3 9.3-19.1 17.7-26.7 18.8zM448 96c0-35.3-28.7-64-64-64H64C28.7 32 0 60.7 0 96V416c0 35.3 28.7 64 64 64H384c35.3 0 64-28.7 64-64V96zM148.1 393.9L64 416l22.5-82.2c-13.9-24-21.2-51.3-21.2-79.3C65.4 167.1 136.5 96 223.9 96c42.4 0 82.2 16.5 112.2 46.5c29.9 30 47.9 69.8 47.9 112.2c0 87.4-72.7 158.5-160.1 158.5c-26.6 0-52.7-6.7-75.8-19.3z" />
                </svg></a>
            <a href=""><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"><!--!Font Awesome Free 6.6.0 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2024 Fonticons, Inc.-->
                    <path d="M48 64C21.5 64 0 85.5 0 112c0 15.1 7.1 29.3 19.2 38.4L236.8 313.6c11.4 8.5 27 8.5 38.4 0L492.8 150.4c12.1-9.1 19.2-23.3 19.2-38.4c0-26.5-21.5-48-48-48L48 64zM0 176L0 384c0 35.3 28.7 64 64 64l384 0c35.3 0 64-28.7 64-64l0-208L294.4 339.2c-22.8 17.1-54 17.1-76.8 0L0 176z" />
                </svg></a>
        </div>
    </div>
    <a href="back.php" class="back-btn"><span class="icon"><i class="fa-solid fa-chevron-left"></i></span></a>
    <!-- الفوتر -->
    <?php include 'footer.php'; ?>
    <script src="../js/script.js"></script>
</body>

</html>