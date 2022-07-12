<?php
require_once(__DIR__ . '/vendor/autoload.php');

$config = SendinBlue\Client\Configuration::getDefaultConfiguration()->setApiKey('api-key', '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');

$apiInstance = new SendinBlue\Client\Api\TransactionalSMSApi(
    new GuzzleHttp\Client(),
    $config
);
$sendTransacSms = new \SendinBlue\Client\Model\SendTransacSms();
$sendTransacSms['sender'] = 'Bello';
$sendTransacSms['recipient'] = '+33787056569';
$sendTransacSms['content'] = 'This is a transactional SMS';
$sendTransacSms['type'] = 'transactional';
$sendTransacSms['webUrl'] = 'https://www.monasso.org/';

try {
    $result = $apiInstance->sendTransacSms($sendTransacSms);
    print_r($result);
} catch (Exception $e) {
    echo 'Exception when calling TransactionalSMSApi->sendTransacSms: ', $e->getMessage(), PHP_EOL;
}
