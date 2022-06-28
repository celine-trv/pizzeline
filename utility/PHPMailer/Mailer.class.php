<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

require_once('Exception.php');
require_once('PHPMailer.php');
require_once('SMTP.php');


class Mailer extends PHPMailer
{
    const _HOST     = 'host';
    const _USERNAME = 'noreply@mail.fr';
    const _PASSWORD = '************';


    /**
     * sending email
     * 
     * @param   array       $recipients :   if name (optionnal) -> have to be the key(s) / value(s) = email address
     * @param   string      $subject    :   subject of email
     * @param   string      $body       :   content of email
     * @param   array       $bcc        :   blind copy : if name (optionnal) -> have to be the key(s) / value(s) = email address
     * @return  <bool>                  :   true if email sent / false if not
     */
    public static function sendEmail($recipients, $subject, $body, $bcc = []) {
        $validate = false;
        $mailer = new PHPMailer();      // (true) to enable Exceptions

        try {
            //To load French version
            $mailer->setLanguage('fr', '/language/');
            
            //Server settings
            $mailer->isSMTP();                                                    //Send using SMTP
            $mailer->SMTPAuth   = true;                                           //Enable SMTP authentication
            $mailer->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;                    //`PHPMailer::ENCRYPTION_STARTTLS` Enable TLS encryption; `PHPMailer::ENCRYPTION_SMTPS` encouraged
            $mailer->SMTPOptions = array('ssl' => array('verify_peer' => false,'verify_peer_name' => false,'allow_self_signed' => true)); // ignore certificate error
            // $mailer->SMTPDebug = SMTP::DEBUG_SERVER;                           //Enable verbose debug output
            $mailer->Port       = 465;                                            //TCP port 587 to connect to, use 465 for `PHPMailer::ENCRYPTION_SMTPS` above
            $mailer->Host       = self::_HOST;                                    //Set the SMTP server to send through
            $mailer->Username   = self::_USERNAME;                                //SMTP username
            $mailer->Password   = self::_PASSWORD;                                //SMTP password


            //Recipients
            $mailer->setFrom(self::_USERNAME, 'Pizzeline');
            foreach($recipients as $recipient_name => $recipient_address) {       //Add recipients
                if(is_string($recipient_name))
                    $mailer->addAddress($recipient_address, $recipient_name);
                else
                    $mailer->addAddress($recipient_address);
            }
            // $mailer->From = self::_USERNAME;
            // $mailer->FromName = "Pizzeline";     // name displayed
            // $mailer->addAddress('ellen@example.com', 'john@exemple.com');         //Name is optional
            // $mailer->addReplyTo('john@exemple.com', 'John');
            // $mailer->addCC('cc@example.com');
            if(!empty($bcc)) {                                                    //Add BCC
                foreach($bcc as $bcc_name => $bcc_address) {
                    if(is_string($bcc_name))
                        $mailer->addBCC($bcc_address, $bcc_name);
                    else
                        $mailer->addBCC($bcc_address);
                }
            }

            //Attachments
            // $mailer->addAttachment('/var/tmp/file.tar.gz');                    //Add attachments
            // $mailer->addAttachment('/img/test.jpg', 'new.jpg');                //Optional name

            //Content
            $mailer->isHTML(true);                                                //Set email format to HTML
            $mailer->CharSet = "utf-8";
            $mailer->Subject = $subject;
            $mailer->Body    = $body;                                             // html message
            // $mailer->AltBody = 'This is the body in plain text for non-HTML mail clients';        // plain text message
            // $mailer->WordWrap = 70;                                            // 70 recommended

            if($mailer->send()) $validate = true;

            unset($mailer);
        } 
        catch (Exception $e) {
            echo "Message could not be sent. <br>Mailer Error: {$mailer->ErrorInfo}";
        }
        return $validate;
    }
}
