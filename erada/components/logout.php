<!-- تسجيل الخروج -->
<?php
// بدء الجلسة
session_start();

// إنهاء الجلسة
session_unset(); // إزالة جميع متغيرات الجلسة
session_destroy(); // تدمير الجلسة

// إعادة توجيه المستخدم الى صفحة البداية
header("Location: ../index.php");
exit();
?>
