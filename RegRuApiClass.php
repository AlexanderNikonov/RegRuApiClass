<?php

/**
 * Reg.ru API class
 * API Documentation: https://www.reg.ru/support/help/API-version2
 * Class Documentation: https://github.com/AlexanderNikonov/RegRuApiClass
 * 
 * @author Alexander Nikonov <alexanderphp@ya.ru>
 * @version 0.1
 * @since 23.02.2014
 * @access public
 * @license http://www.apache.org/licenses/LICENSE-2.0.txt Apache License Version 2.0
 * @example example.php
 * @filesource
 */
@error_reporting(E_ALL);
@ini_set('display_errors', true);

class RegRuApiClass {

    /**
     * The API base URL
     */
    const API_URL = 'https://api.reg.ru/api/regru2/';

    /**
     * API Access Login
     * @var string $_apiLogin
     */
    protected $_apiLogin = null;

    /**
     * API Access Password
     * @var string $_apiPassword
     */
    protected $_apiPassword = null;

    /**
     * API Access Signature
     * @var string $_apiSig
     */
    protected $_apiSig = null;

    /**
     * API available methods
     * @var array $avalibleMethods
     */
    protected $avalibleMethods = array(
        'domain' => array(
            'nop',
            'get_prices',
            'get_suggest',
            'get_premium',
            'get_deleted',
            'check',
            'create',
            'transfer',
            'get_rereg_data',
            'set_rereg_bids',
            'get_user_rereg_bids',
            'get_docs_upload_uri',
            'update_contacts',
            'update_private_person_flag',
            'register_ns',
            'delete_ns',
            'get_nss',
            'update_nss',
            'delegate',
            'undelegate',
            'transfer_to_another_account',
            'look_at_entering_list',
            'accept_or_refuse_entering_list',
            'cancel_transfer',
            'request_to_transfer',
        ),
    );

    /**
     * Configuration API
     * @param array $config The array is specified login password and signature
     * @example example.php
     * @throws Exception
     */
    public function __construct(array $config) {
        if (is_array($config)) {
            $this->setApiLogin($config['apiLogin']);
            $this->setApiPassword($config['apiPassword']);
            $this->setApiSig($config['apiSig']);
        } else {
            throw new Exception('Error: __construct() - Configuration data is missing.');
        }
    }

    protected function setApiLogin($param) {
        if (is_string($param)) {
            $this->_apiLogin = $param;
        } else {
            throw new Exception('Error: _apiLogin is not string');
        }
        return true;
    }

    protected function setApiPassword($param) {
        if (is_string($param)) {
            $this->_apiPassword = $param;
        } else {
            throw new Exception('Error: _apiPassword is not string');
        }
        return true;
    }

    protected function setApiSig($param) {
        if (is_string($param) || $param === null) {
            $this->_apiSig = $param;
        } else {
            throw new Exception('Error: _apiSig is not string');
        }
        return true;
    }

    protected function isValidMethod($function, $method) {
        if (!in_array($method, $this->avalibleMethods[$function])) {
            return false;
        }
        return true;
    }

    /**
     * Getting prices for registration / renewal of domains in all accessible areas.
     * @param array $array Avalible methods: show_renew_data, show_update_data, currency
     * @return object
     */
    public function domainGetPrices(array $array = array('show_renew_data', 'show_update_data', 'currency')) {
        return $this->_makeCall('domain', 'get_prices', $array);
    }

    /**
     * The function prepares and sends the request to the API URL
     * @param string $function API cagegory (domain, tests_fn, user, bill, service, zone, hosting, folder)
     * @param string $method function Method
     * @param array $params function parameters
     * @return array
     * @throws Exception
     */
    protected function _makeCall($function, $method, array $params) {
        if (!$this->isValidMethod($function, $method)) {
            throw new Exception('Error: isValidMethod ' . '[' . $function . '/' . $method . ']');
        }
        $paramString = json_encode($params);

        $apiCall = self::API_URL . $function .
                '/' . $method .
                '?username=' . $this->_apiLogin .
                '&password=' . $this->_apiPassword .
                '&output_content_type=json' .
                '&sig=' . $this->_apiSig .
                (count($params) ? '&input_format=json' : '') .
                (count($params) ? '&input_data=' . $paramString : '');

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $apiCall);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Accept: application/json'));
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

        $jsonData = curl_exec($ch);
        if (!$jsonData) {
            throw new Exception('Error: _makeCall() - cURL error: ' . curl_error($ch));
        }
        curl_close($ch);

        return json_decode($jsonData);
    }

}

$RegRuApi = new RegRuApiClass(array(
    'apiLogin' => 'AlexanderPHP', //Your Reg.Ru Login
    'apiPassword' => '1234', // API or profile password
    'apiSig' => null,
        ));
$response = $RegRuApi->domainGetPrices(array());
