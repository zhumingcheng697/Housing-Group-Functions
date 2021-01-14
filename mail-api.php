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
            ->setFrom([$email_config["username"] => ($sender_name ?? null) ? $sender_name : "PHP Email Lambda Test"])
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

(function() {
    $props = array();
    parse_str($_SERVER['QUERY_STRING'], $props);

    $sender_name = ($props["name"] ?? null) ? $props["name"] : "PHP Email Lambda Test";
    $sender_email = ($props["email"] ?? null) ? $props["email"] : "nyu-dining-test@outlook.com";
    $recipient = ($props["to"] ?? null) ? $props["to"] : (($props["mailto"] ?? null) ? $props["mailto"] : "nyu-dining-test@outlook.com");
    $subject = ($props["subject"] ?? null) ? $props["subject"] : "PHP Email Lambda Test (" . date(DATE_RFC2822) . ")";
    $body = ($props["body"] ?? null) ? $props["body"] : (($props["msg"] ?? null) ? $props["msg"] : (($props["message"] ?? null) ? $props["message"] : "This is an automatic email sent using PHP Lambda."));

    send_email($sender_name, $sender_email, $recipient, $subject, $body);

    echo "Sending through default mail\n";

    ini_set("sendmail_from", $sender_email);

    if (mail($recipient, $subject, $body, "From: " . $sender_name . " <" . $sender_email . ">")) {
        echo "Email sent successfully to \"" . $recipient . "\"\n";
    } else {
        echo "Email failed to send\n";
    }

    ini_restore("sendmail_from");
})();

?>
