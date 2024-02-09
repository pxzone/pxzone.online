<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Api_auth {

    public function authKeys() {
        $auth = array(
            'username' => 'pxzbot',
            'password' => 'password',
            'okLink_key' => 'api_key',
        );
        return $auth;
    }
}
