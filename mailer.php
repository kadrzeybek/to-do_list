<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php';

function sendResetMail($toEmail, $resetLink) {
    $mail = new PHPMailer(true);

    try {
        $mail->isSMTP();
        $mail->Host = 'sandbox.smtp.mailtrap.io';
        $mail->SMTPAuth = true;
        $mail->Username = '79b5ffcb06724f'; 
        $mail->Password = '254f0d4e718ca0'; 
        $mail->Port = 2525;

        $mail->setFrom('support@yourapp.com', 'ToDo App');
        $mail->addAddress($toEmail);

        $mail->CharSet = 'UTF-8';
    $mail->Encoding = 'base64';


        $mail->isHTML(true);
        $mail->Subject = 'Şifre Sıfırlama Bağlantısı';
        $mail->Body = "Merhaba, <br><br>Şifrenizi sıfırlamak için aşağıdaki bağlantıya tıklayın:<br><a href='$resetLink'>$resetLink</a><br><br>Bu bağlantı sadece 10 dakika geçerlidir.";

        $mail->send();
    } catch (Exception $e) {
        echo "Mail gönderilemedi. Hata: {$mail->ErrorInfo}";
    }
}

?>
