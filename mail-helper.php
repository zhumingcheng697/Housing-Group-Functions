<?php

require __DIR__ . '/vendor/autoload.php';

function send_email($sender_name, $sender_email, $recipient, $subject, $body) {
    try {
        $email_config = json_decode(file_get_contents(__DIR__ . '/email.json'), true);

        $transport = (new Swift_SmtpTransport($email_config["host"], $email_config["port"], ($email_config["encryption"] ?? null) ? $email_config["encryption"] : null))
            ->setUsername($email_config["username"])
            ->setPassword($email_config["password"])
        ;

        $mailer = new Swift_Mailer($transport);

        $message = (new Swift_Message($subject))
            ->setFrom([$email_config["username"] => $sender_name ? $sender_name : "PHP Email Lambda Test"])
            ->setTo([$recipient])
            ->setBody($body)
        ;

        if ($sender_email) {
            if ($sender_name) {
                $message->setReplyTo([$sender_email => $sender_name]);
            } else {
                $message->setReplyTo([$sender_email]);
            }
        }

        if ($mailer->send($message)) {
            echo "Email sent successfully to \"" . $recipient . "\"\n";
        } else {
            echo "Email failed to send\n";
        }
    } catch (Exception $e) {
        echo "Email failed to send:\n" .  $e->getMessage() . "\n";
    }
}

function mail_helper($default_sender_name, $default_sender_email, $default_recipient, $default_subject, $default_body) {
    $props = array();
    parse_str($_SERVER['QUERY_STRING'], $props);

    $sender_name = $default_sender_name ? $default_sender_name : (($props["name"] ?? null) ? $props["name"] : (($props["sender"] ?? null) ? $props["sender"] : "PHP Email Lambda Test"));
    $sender_email = $default_sender_email ? $default_sender_email : (($props["email"] ?? null) ? $props["email"] : (($props["from"] ?? null) ? $props["from"] : "nyu-dining-test@outlook.com"));
    $recipient = $default_recipient ? $default_recipient : (($props["to"] ?? null) ? $props["to"] : (($props["mailto"] ?? null) ? $props["mailto"] : "nyu-dining-test@outlook.com"));
    $subject = $default_subject ? $default_subject : (($props["subject"] ?? null) ? $props["subject"] : "PHP Email Lambda Test (" . date(DATE_RFC2822) . ")");
    $body = $default_body ? $default_body : (($props["body"] ?? null) ? $props["body"] : (($props["msg"] ?? null) ? $props["msg"] : (($props["message"] ?? null) ? $props["message"] : "This is an automatic email sent using PHP Lambda.")));

    send_email($sender_name, $sender_email, $recipient, $subject, $body);
}

?>
