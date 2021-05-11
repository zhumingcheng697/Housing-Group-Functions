<?php

require __DIR__ . '/mail-helper.php';

use Aws\SecretsManager\SecretsManagerClient;
use Aws\Exception\AwsException;

if (!file_exists(__DIR__ . '/email.json')) {
    echo "Unable to load \"email.json\". Please configure it if you have not done so already.\n";
} else {
    $client = new SecretsManagerClient([
        'profile' => 'default',
        'version' => '2017-10-17',
        'region' => 'us-east-1'
    ]);

    $secretName = 'nyu-it-dgcom-fhd-email';
    $secret = file_get_contents(__DIR__ . '/email.json');
    $description = 'Email credentials';

    try {
        $result = $client->putSecretValue([
            'SecretId' => $secretName,
            'SecretString' => $secret,
        ]);
        echo "Secret updated successfully\n";
    } catch (AwsException $e) {
        try {
            $result = $client->createSecret([
                'Description' => $description,
                'Name' => $secretName,
                'SecretString' => $secret,
            ]);
            echo "Secret created successfully\n";
        } catch (AwsException $err) {
            // output error message if fails
            echo $err->getMessage();
            echo "\n";
        }
    }
}