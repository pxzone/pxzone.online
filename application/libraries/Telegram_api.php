<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Telegram_api {

    public function authKeys() {
        $auth = array(
            'api_key' => 'api_key',
            'api_key_test' => 'api_key',
        );
        return $auth;
    }
}
