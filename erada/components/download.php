<?php
// تأكد من أن هناك اسم ملف يتم تمريره كمعلمة في الرابط
if (isset($_GET['file'])) {
    $filename = $_GET['file'];

    // تحديد المسار الكامل للملف
    $filepath =$filename;

    // التحقق من وجود الملف
    if (file_exists($filepath)) {
        // إعدادات رأس التحميل
        header('Content-Description: File Transfer');
        header('Content-Type: application/pdf');
        header('Content-Disposition: attachment; filename=' . basename($filepath));
        header('Expires: 0');
        header('Cache-Control: must-revalidate');
        header('Pragma: public');
        header('Content-Length: ' . filesize($filepath));

        // قراءة الملف وإرساله إلى المتصفح
        readfile($filepath);
        exit;
    } else {
        echo "الملف غير موجود.";
    }
} else {
    echo "لم يتم تحديد ملف.";
}
?>
