<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require_once __DIR__ . '/vendor/autoload.php';

function sendResetMail($toEmail, $body, $subject = 'Şifre Sıfırlama') {
    $mail = new PHPMailer(true);
    try {
        $mail->isSMTP();
        $mail->Host       = 'smtp.mailtrap.io';
        $mail->SMTPAuth   = true;
        $mail->Username   = '79b5ffcb06724f';
        $mail->Password   = '254f0d4e718ca0';
        $mail->Port       = 2525;

        $mail->setFrom('test@todo.local', 'ToDoList Uygulama');
        $mail->addAddress($toEmail);

        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->Body    = $body;

        $mail->send();
        return true;
    } catch (Exception $e) {
        return "Mail gönderilemedi: " . $mail->ErrorInfo;
    }
}

?>