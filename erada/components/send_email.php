
<?php 
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;



require 'C:/xampp/htdocs/erada/PHPMailer/src/Exception.php';
require 'C:/xampp/htdocs/erada/PHPMailer/src/PHPMailer.php';
require 'C:/xampp/htdocs/erada/PHPMailer/src/SMTP.php';

function send_email($toEmail, $subject, $body) {
    $mail = new PHPMailer(true);

    try {
        // إعدادات الخادم
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';    // خادم SMTP
        $mail->SMTPAuth   = true;                 // تفعيل مصادقة SMTP
        $mail->Username   = 'eradaplatform@gmail.com';  // حساب Gmail
        $mail->Password   = 'wqwz spkq oojn nstl';         // كلمة المرور
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS; // تغيير إلى SMTPS
        $mail->Port       = 465;

        // إعدادات المرسل
        $mail->setFrom('eradaplatform@gmail.com', 'Erada PlatForm');
        $mail->addAddress($toEmail);             

        // المحتوى
        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->Body    = $body;
        $mail->send();
        
    } catch (Exception $e) {
        
    }
}

?>
