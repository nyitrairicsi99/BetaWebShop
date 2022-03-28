<?php
    namespace Controller;
    
    use PDO;
    use PHPMailer\PHPMailer\PHPMailer;
    use PHPMailer\PHPMailer\SMTP;
    use PHPMailer\PHPMailer\Exception;

    class MailController {
        private static $instance = null;
        private static $host = null;
        private static $username = null;
        private static $password = null;
        
        private function __construct()
        {
        }

        public static function getInstance() {
            if (self::$instance == null)
            {
                self::$instance = new MailController();
                SettingsController::getInstance();
                self::$host = SettingsController::$smtphost;
                self::$username = SettingsController::$smtpuser;
                self::$password = SettingsController::$smtppass;
            }     
          return self::$instance;
        }

        public static function sendMail($address,$subject,$body) {
            if (
                self::$host!=null &&
                self::$username!=null &&
                self::$password!=null
            ) {
                $mail = new PHPMailer(true);
                try {
                    //Server settings
                    $mail->SMTPDebug = SMTP::DEBUG_SERVER; 
                    $mail->isSMTP();       
                    $mail->Mailer = "smtp";  
                    $mail->Host = self::$host; 
                    $mail->SMTPAuth = true;
                    $mail->Username = self::$username;
                    $mail->Password = self::$password;
                    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                    $mail->Port = 587;
    
                    //Recipients
                    $mail->setFrom(self::$username, 'Webshop');
                    $mail->addAddress($address);
    
                    //Content
                    $mail->isHTML(true);
                    $mail->Subject = $subject;
                    $mail->Body = $body;
                    $mail->AltBody = $body;
    
                    $mail->send();
                    //echo 'Message has been sent';
                } catch (Exception $e) {
                    //echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
                }
            }

            
        }
    }