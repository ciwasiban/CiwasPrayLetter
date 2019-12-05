<?php
// Import PHPMailer classes into the global namespace
// These must be at the top of your script, not inside a function
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

//Load composer's autoloader
require __DIR__ . '/vendor/autoload.php';
require __DIR__ . '/PrayLetter.php';

// 取得信件內容
$PrayLetter = new PrayLetter();
$PrayLetter->generate();

// 取得收件者清單
$recipientList = $PrayLetter->getRecipentList();

// 開始寄信 寫log
date_default_timezone_set('Asia/Taipei');
$start = date('Ymd-His', time());
echo $start_message = "start send mail..." . PHP_EOL;
error_log($start_message , 3, $start.".log");

// 寄信
$cnt = count($recipientList);
if ($cnt > 0) {
    $num = 0;
    foreach ($recipientList AS $recipient => $sendMail) {
        $message = $error = '';
        $num++;
        $body = str_replace(
            array('{%recipient%}'),
            array($recipient),
            $PrayLetter->body);

        // send mail
        $mail = new PHPMailer(true);                              // Passing `true` enables exceptions
        try {
            //Server settings
            $mail->SMTPDebug = 0;                                 // Enable verbose debug output
            $mail->isSMTP();                                      // Set mailer to use SMTP
            $mail->Host = 'smtp.gmail.com';  // Specify main and backup SMTP servers
            $mail->SMTPAuth = true;                               // Enable SMTP authentication
            $mail->Username = $PrayLetter->username;                 // SMTP username
            $mail->Password = $PrayLetter->password;                           // SMTP password
            $mail->SMTPSecure = 'ssl';                            // Enable TLS encryption, `ssl` also accepted
            $mail->Port = 465;                                    // TCP port to connect to
            $mail->CharSet = "UTF-8";

            //Recipients
            $mail->setFrom($PrayLetter->username, $PrayLetter->mailer);
            $mail->addAddress($sendMail, $recipient);     // Add a recipient , Name is optional

            //Content
            $mail->isHTML(true);                                  // Set email format to HTML
            $mail->Subject = sprintf('%s %s', $recipient, $PrayLetter->subject);
            $mail->Body    = $body;

            $mail->send();
            $message = sprintf('Message has been sent to %s. %s(%s) At %s' . PHP_EOL, $num, $recipient, $sendMail, date('Y-m-d H:i:s', time()));
            error_log($message, 3, $start.".log");
            echo $message;
        } catch (Exception $e) {
            $message = sprintf('Message could not be sent to %s. %s(%s) At %s' . PHP_EOL, $num, $recipient, $sendMail, date('Y-m-d H:i:s', time()));
            echo $error = 'Mailer Error: ' . $mail->ErrorInfo . PHP_EOL;
            error_log($message . $error, 3, $start.".log");
        }
    }
}

echo $end_message = sprintf("send mail end." . PHP_EOL . "(Total is %s)" . PHP_EOL, $cnt);
error_log($end_message , 3, $start.".log");
