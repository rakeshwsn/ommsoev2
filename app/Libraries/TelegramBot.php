<?php

namespace App\Libraries;

use CodeIgniter\HTTP\CURLRequest;

class TelegramBot {

    private $apiToken;

    public function __construct()
    {
        $this->apiToken = getenv()['TELEGRAM_API_TOKEN'];
    }

    public function sendMessage($chatId, $text, $options = [])
    {
        $url = "https://api.telegram.org/bot{$this->apiToken}/sendMessage";

        $data = array_merge([
            'chat_id' => $chatId,
            'text' => $text
        ], $options);

        $client = \Config\Services::curlrequest();
        $response = $client->get($url,['debug' => true,'json' => $data]);

        return json_encode($response);
    }
}