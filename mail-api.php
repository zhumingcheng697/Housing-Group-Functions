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
            ->setTo([$recipient])
            ->setBody($body)
        ;

        if ($sender_email) {
            if ($sender_name) {
                $message->setFrom([$sender_email => $sender_name]);
            } else {
                $message->setFrom([$sender_email]);
            }
        } else {
            $message->setFrom([$email_config["username"] => "PHP Email Lambda Test"]);
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

    $sender_name = $props["name"] ?? null;
    $sender_email = $props["email"] ?? null;
    $recipient = $props["to"] ?? $props["mailto"] ?? "nyu-dining-test@outlook.com";
    $subject = $props["subject"] ?? "PHP Email Lambda Test (" . date(DATE_RFC2822) . ")";
    $body = $props["body"] ?? $props["msg"] ?? $props["message"] ?? "This is an automatic email sent using PHP Lambda.";

    send_email($sender_name, $sender_email, $recipient, $subject, $body);
})();

?>
