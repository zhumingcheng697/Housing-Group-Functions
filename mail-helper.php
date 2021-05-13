<?php

require __DIR__ . '/vendor/autoload.php';

use Aws\SecretsManager\SecretsManagerClient;
use Aws\Exception\AwsException;

function get_email_config($callback) {
    $client = new SecretsManagerClient([
        'version' => '2017-10-17',
        'region' => 'us-east-1',
    ]);

    $secretName = 'nyu-it-dgcom-fhd-email';

    try {
        $result = $client->getSecretValue([
            'SecretId' => $secretName,
        ]);

        if (isset($result['SecretString'])) {
            $callback(json_decode($result['SecretString'], true));
        } else if (isset($result['SecretBinary'])) {
            $callback(json_decode(base64_decode($result['SecretBinary']), true));
        } else {
            echo "Unable to get email credentials from AWS Secrets Manager. Please run \"upload-email-credential.php\" if you have not done so already.\n";
            get_local_email_config($callback);
        }
    } catch (AwsException $e) {
        echo "Unable to get email credentials from AWS Secrets Manager. Please run \"upload-email-credential.php\" if you have not done so already.\n" . $e->getMessage() . "\n" . $e->getAwsErrorMessage() . "\n" . $e->getAwsErrorCode() . "\n";
        get_local_email_config($callback);
    } catch (Exception $e) {
        echo "Unable to get email credentials from AWS Secrets Manager. Please run \"upload-email-credential.php\" if you have not done so already.\n" . $e->getMessage() . "\n";
        get_local_email_config($callback);
    }
}

function get_local_email_config($callback) {
    if (!file_exists(__DIR__ . '/email.json')) {
        echo "Unable to load \"email.json\" as fallback. Email failed to send.\n";
    } else {
        $callback(json_decode(file_get_contents(__DIR__ . '/email.json'), true));
    }
}

function send_email($sender_name, $sender_email, $recipient, $subject, $body, $email_config) {
    try {
        $transport = (new Swift_SmtpTransport($email_config["host"], $email_config["port"], ($email_config["encryption"] ?? null) ? $email_config["encryption"] : null))
            ->setUsername($email_config["username"])
            ->setPassword($email_config["password"]);

        $mailer = new Swift_Mailer($transport);

        $message = (new Swift_Message($subject))
            ->setFrom([$email_config["username"] => $sender_name ?: "PHP Email Lambda Test"])
            ->setTo([$recipient])
            ->setBody($body, "text/html");

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
        echo "Email failed to send:\n" . $e->getMessage() . "\n";
    }
}

function mail_helper($overloaded_sender_name = null, $overloaded_sender_email = null, $overloaded_recipient = null, $overloaded_subject = null, $overloaded_body = null, $fallback_sender_name = null, $fallback_sender_email = null, $fallback_recipient = null, $fallback_subject = null, $fallback_body = null) {
    $props = array();
    parse_str($_SERVER['QUERY_STRING'], $props);

    $sender_name = $overloaded_sender_name ?: (($props["name"] ?? null) ? $props["name"] : (($props["sender"] ?? null) ? $props["sender"] : ($fallback_sender_name ?: "PHP Email Lambda Test")));
    $sender_email = $overloaded_sender_email ?: (($props["email"] ?? null) ? $props["email"] : (($props["from"] ?? null) ? $props["from"] : ($fallback_sender_email ?: "nyu-dining-test@outlook.com")));
    $recipient = $overloaded_recipient ?: (($props["to"] ?? null) ? $props["to"] : (($props["mailto"] ?? null) ? $props["mailto"] : ($fallback_recipient ?: "nyu-dining-test@outlook.com")));
    $subject = $overloaded_subject ?: (($props["subject"] ?? null) ? $props["subject"] : ($fallback_subject ?: "PHP Email Lambda Test (" . date(DATE_RFC2822) . ")"));
    $body = $overloaded_body ?: (($props["body"] ?? null) ? $props["body"] : (($props["msg"] ?? null) ? $props["msg"] : (($props["message"] ?? null) ? $props["message"] : ($fallback_body ?: "This is an automatic email sent using PHP Lambda."))));

    get_email_config(function ($email_config) use ($sender_name, $sender_email, $recipient, $subject, $body) {
        send_email($sender_name, $sender_email, $recipient, $subject, $body, $email_config);
    });
}

?>
