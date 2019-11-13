<?php

require_once (dirname(__FILE__) . '/twocheckout/TwocheckoutApi.php');

class Twocheckout {
    static function auth($account, $key, $sandbox, $params) {
        try {
            if ($sandbox) {
                TwocheckoutApi::setCredentials($account, $key, 'sandbox');
            } else {
                TwocheckoutApi::setCredentials($account, $key);
            }
            return Twocheckout_Charge::auth($params);

        } catch (Twocheckout_Error $e) {
            $error = array(
              "error" => $e->getMessage()
            );
	    return $error; 
        }
    }
};

