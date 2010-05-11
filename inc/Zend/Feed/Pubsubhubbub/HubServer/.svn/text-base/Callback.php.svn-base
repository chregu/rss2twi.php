<?php
/**
 * Zend Framework
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://framework.zend.com/license/new-bsd
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to padraic dot brady at yahoo dot com so we can send you a copy immediately.
 *
 * @category   Zend
 * @package    Zend_Feed_Pubsubhubbub
 * @copyright  Copyright (c) 2009 Padraic Brady
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/**
 * @see Zend_Feed_Pubsubhubbub
 */
require_once 'Zend/Feed/Pubsubhubbub.php';

/**
 * @see Zend_Feed_Pubsubhubbub
 */
require_once 'Zend/Feed/Pubsubhubbub/CallbackAbstract.php';

/**
 * @see Zend_Feed_Reader
 */
require_once 'Zend/Feed/Reader.php';

/**
 * @category   Zend
 * @package    Zend_Feed_Pubsubhubbub
 * @copyright  Copyright (c) 2009 Padraic Brady
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Feed_Pubsubhubbub_HubServer_Callback
    extends Zend_Feed_Pubsubhubbub_CallbackAbstract
{

    /**
     * The URL Hub Servers must use when communicating with this Subscriber
     *
     * @var string
     */
    protected $_callbackUrl = '';

    /**
     * The number of seconds which this Hub defaults to as lease seconds.
     *
     * @var int
     */
    protected $_leaseSeconds = 2592000;

    /**
     * The POST payload as a parameter array. Where multiple values are
     * attached to an identical key, the parameter is an array of those
     * values in the order in which they were presented in the payload.
     *
     * @var array
     */
    protected $_postData = array();

    /**
     * The preferred verification mode (sync or async). By default, this
     * Hub Server prefers synchronous verification, but will support
     * asynchronous in the future.
     *
     * @var string
     */
    protected $_preferredVerificationMode
        = Zend_Feed_Pubsubhubbub::VERIFICATION_MODE_SYNC;

    /**
     * An array of any errors including keys for 'response', 'hubUrl'.
     * The response is the actual Zend_Http_Response object.
     *
     * @var array
     */
    protected $_errors = array();

    /**
     * Handle any callback related to a subscription, unsubscription or
     * publisher notification of new feed updates. [PUBLISHER OUTSTANDING]
     *
     * @param array $httpGetData GET data if available (NOT USED BY HUB)
     * @param bool $sendResponseNow Whether to send response now or when asked
     */
    public function handle(array $httpGetData = null, $sendResponseNow = false)
    {
        $this->_postData = $this->_parseParameters();
        if (strtolower($_SERVER['REQUEST_METHOD']) !== 'post') {
            $this->getHttpResponse()->setHttpResponseCode(404);
        } elseif ($this->isValidSubscription()) {
            $this->_handleSubscription('subscribe');
            if ($this->isSuccess()) {
                $this->getHttpResponse()->setHttpResponseCode(204);
            } else {
                $this->getHttpResponse()->setHttpResponseCode(404);
            }
        } elseif ($this->isValidUnsubscription()) {
            $this->_handleSubscription('unsubscribe');
            if ($this->isSuccess()) {
                $this->getHttpResponse()->setHttpResponseCode(204);
            } else {
                $this->getHttpResponse()->setHttpResponseCode(404);
            }
        } else {
            $this->getHttpResponse()->setHttpResponseCode(404);
        }
        if ($sendResponseNow) {
            $this->sendResponse();
        }
    }

    /**
     * Set the callback URL to be used by Publishers or Subscribers when
     * communication with the Hub Server
     *
     * @param string $url
     */
    public function setCallbackUrl($url)
    {
        if (empty($url) || !is_string($url) || !Zend_Uri::check($url)) {
            require_once 'Zend/Feed/Pubsubhubbub/Exception.php';
            throw new Zend_Feed_Pubsubhubbub_Exception('Invalid parameter "url"'
                .' of "' . $url . '" must be a non-empty string and a valid'
                .'URL');
        }
        $this->_callbackUrl = $url;
    }

    /**
     * Get the callback URL to be used by Publishers or Subscribers when
     * communication with the Hub Server
     *
     * @return string
     */
    public function getCallbackUrl()
    {
        if (empty($this->_callbackUrl)) {
            require_once 'Zend/Feed/Pubsubhubbub/Exception.php';
            throw new Zend_Feed_Pubsubhubbub_Exception('A valid Callback URL MUST be'
            . ' set before attempting any operation');
        }
        return $this->_callbackUrl;
    }

    /**
     * Set the number of seconds for which any subscription will remain valid
     *
     * @param int $seconds
     */
    public function setLeaseSeconds($seconds)
    {
        $seconds = intval($seconds);
        if ($seconds <= 0) {
            require_once 'Zend/Feed/Pubsubhubbub/Exception.php';
            throw new Zend_Feed_Pubsubhubbub_Exception('Expected lease seconds'
            . ' must be an integer greater than zero');
        }
        $this->_leaseSeconds = $seconds;
    }

    /**
     * Get the number of lease seconds on subscriptions
     *
     * @return int
     */
    public function getLeaseSeconds()
    {
        return $this->_leaseSeconds;
    }

    /**
     * Set preferred verification mode (sync or async). By default, this
     * Hub Server prefers synchronous verification, but will support
     * asynchronous in the future.
     *
     * @param string $mode Should be 'sync' or 'async'
     */
    public function setPreferredVerificationMode($mode)
    {
        if ($mode !== Zend_Feed_Pubsubhubbub::VERIFICATION_MODE_SYNC
        && $mode !== Zend_Feed_Pubsubhubbub::VERIFICATION_MODE_ASYNC) {
            require_once 'Zend/Feed/Pubsubhubbub/Exception.php';
            throw new Zend_Feed_Pubsubhubbub_Exception('Invalid preferred'
            . ' mode specified: "' . $mode . '" but should be one of'
            . ' Zend_Feed_Pubsubhubbub::VERIFICATION_MODE_SYNC or'
            . ' Zend_Feed_Pubsubhubbub::VERIFICATION_MODE_ASYNC');
        }
        $this->_preferredVerificationMode = $mode;
    }

    /**
     * Get preferred verification mode (sync or async).
     *
     * @return string
     */
    public function getPreferredVerificationMode()
    {
        return $this->_preferredVerificationMode;
    }

    /**
     * Checks validity of the request simply by making a quick pass and
     * confirming the presence of all REQUIRED parameters.
     *
     * @param array $httpGetData
     * @return bool
     */
    public function isValidSubscription()
    {
        if (isset($this->_postData['hub.mode'])
        && $this->_postData['hub.mode'] !== 'subscribe') {
            return false;
        }
        if (!$this->_hasValidSubscriptionOpParameters()) {
            return false;
        }
        if (array_key_exists('hub.lease_seconds', $this->_postData)) {
            if (intval($this->_postData['hub.lease_seconds']) <= 0) { // can we do this?
                return false;
            }
        }
        return true;
    }

    /**
     * Checks validity of the request simply by making a quick pass and
     * confirming the presence of all REQUIRED parameters.
     *
     * @param array $httpGetData
     * @return bool
     */
    public function isValidUnsubscription()
    {
        if (isset($this->_postData['hub.mode'])
        && $this->_postData['hub.mode'] !== 'unsubscribe') {
            return false;
        }
        if (!$this->_hasValidSubscriptionOpParameters()) {
            return false;
        }
        if (!$this->getStorage()->hasSubscription($this->_getTokenKey(
            $this->_postData['hub.callback'], $this->_postData['hub.topic'], 'subscription'
        ))) {
            return false;
        }
        return true;
    }

    /**
     * Returns a boolean indicator of whether the notifications to a
     * Subscriber was successful. If it failed, FALSE is returned.
     *
     * @return bool
     */
    public function isSuccess()
    {
        if (count($this->_errors) > 0) {
            return false;
        }
        return true;
    }

    /**
     * Return an array of errors met from any failures, including keys:
     * 'response' => the Zend_Http_Response object from the failure
     * 'callbackUrl' => the URL of the Subscriber whose confirmation failed
     *
     * @return array
     */
    public function getErrors()
    {
        return $this->_errors;
    }

    /**
     * Handle a (Un)subscription request (currently synchronous only)
     *
     * @return void
     */
    protected function _handleSubscription($type)
    {
        $client = $this->_getHttpClient($type);
        $client->setUri($this->_postData['hub.callback']);
        $client->setRawData($this->_getRequestParameters($type));
        $response = $client->request();
        $subscriptionKey = $this->_getTokenKey(
            $this->_postData['hub.callback'], $this->_postData['hub.topic'], 'subscription'
        );
        $tokenKey = $this->_getTokenKey(
            $this->_postData['hub.callback'], $this->_postData['hub.topic'], 'challenge'
        );
        if ($response->getStatus() < 200 || $response->getStatus() > 299
        || $response->getBody() !== $this->getStorage()->getToken($tokenKey)) {
            $this->_errors[] = array(
                'response' => $response,
                'callback' => $this->_postData['hub.callback'],
                'topic' => $this->_postData['hub.topic']
            );
        } elseif ($type == 'subscribe') {
            $data = array(
                'callback' => $this->_postData['hub.callback'],
                'topic' => $this->_postData['hub.topic'],
                'created_date' => time(),
                'modified_date' => time(),
                'lease_seconds' => $this->getLeaseSeconds(), // for now :P
                'expiration_date' => time() + $this->getLeaseSeconds(),
                'subscription_state' => 'verified'
            );
            if ($this->getStorage()->hasSubscription($subscriptionKey)) {
                $origData = $this->getStorage()->getSubscription($subscriptionKey);
                $data['created_date'] = $origData['created_date'];
            }
            $this->getStorage()->setSubscription($subscriptionKey, $data);
        } elseif ($type == 'unsubscribe') {
            $this->getStorage()->removeSubscription($subscriptionKey);
        }
    }

    /**
     * Get a basic prepared HTTP client for use
     *
     * @param string $mode Must be "subscribe" or "unsubscribe"
     * @return Zend_Http_Client
     */
    protected function _getHttpClient()
    {
        $client = Zend_Feed_Pubsubhubbub::getHttpClient();
        $client->setMethod(Zend_Http_Client::GET);
        $client->setConfig(array('useragent' => 'Zend_Feed_Pubsubhubbub_HubServer/'
            . Zend_Version::VERSION));
        return $client;
    }

    /**
     * Return a list of standard protocol/optional parameters for addition to
     * client's POST body that are specific to the current Hub Server URL
     *
     * @param string $hubUrl
     */
    protected function _getRequestParameters($mode)
    {
        if (!in_array($mode, array('subscribe', 'unsubscribe'))) {
            require_once 'Zend/Feed/Pubsubhubbub/Exception.php';
            throw new Zend_Feed_Pubsubhubbub_Exception('Invalid mode specified: "'
            . $mode . '" which should have been "subscribe" or "unsubscribe"');
        }
        $params = array();
        $params['hub.callback'] = $this->getCallbackUrl();
        $params['hub.mode'] = $mode;
        $params['hub.topic'] = $this->_postData['hub.topic'];
        if (isset($this->_postData['hub.verify_token'])) {
            $params['hub.verify_token'] = $this->_postData['hub.verify_token'];
        }
        /**
         * Establish a persistent Hub challenge and add to parameters
         */
        $key = $this->_getTokenKey(
            $this->_postData['hub.callback'], $this->_postData['hub.topic'], 'challenge'
        );
        $token = $this->_getToken();
        $this->getStorage()->setToken($key, hash('sha256', $token));
        $params['hub.challenge'] = $token;
        if ($mode == 'subscribe') {
            $params['hub.lease_seconds'] = $this->getLeaseSeconds(); //for now! :P
        }
        return $this->_toByteValueOrderedString(
            $this->_urlEncode($params)
        );
    }

    /**
     * Check validity of request omitting the hub.mode for a subscription or
     * unsubscription POST request
     *
     */
    protected function _hasValidSubscriptionOpParameters()
    {
        $required = array('hub.mode', 'hub.callback',
            'hub.topic', 'hub.verify');
        foreach ($required as $key) {
            if (!array_key_exists($key, $this->_postData)) {
                return false;
            }
        }
        if (!Zend_Uri::check($this->_postData['hub.topic'])) {
            return false;
        }
        if (!Zend_Uri::check($this->_postData['hub.callback'])) {
            return false;
        }
        return true;
    }

    /**
     * Build an array of POST parameters from the raw body (this prevents)
     * the overwrites of keys in $_POST for repeated keyed parameters
     *
     * @return array|void
     */
    protected function _parseParameters()
    {
        $params = array();
        $body = $this->_getRawBody();
        if (empty($body)) {
            return array();
        }
        $parts = explode('&', $body);
        foreach ($parts as $kvpair) {
            $pair = explode('=', $kvpair);
            $key = rawurldecode($pair[0]);
            $value = rawurldecode($pair[1]);
            if (isset($params[$key])) {
                if (is_array($params[$key])) {
                    $params[$key][] = $value;
                } else {
                    $params[$key] = array($params[$key], $value);
                }
            } else {
                $params[$key] = $value;
            }
        }
        return $params;
    }

    /**
     * Simple helper to generate a verification token used in (un)subscribe
     * requests to a Hub Server. Follows no particular method, which means
     * it might be improved/changed in future.
     *
     * @param string $hubUrl The Hub Server URL for which this token will apply
     * @return string
     */
    protected function _getToken()
    {
        if (!empty($this->_testStaticToken)) {
            return $this->_testStaticToken;
        }
        return uniqid(rand(), true) . time();
    }

    /**
     * Simple helper to generate a verification token used in (un)subscribe
     * requests to a Hub Server.
     *
     * @param string $hubUrl The Hub Server URL for which this token will apply
     * @return string
     */
    protected function _getTokenKey($subscriberUrl, $topicUrl, $type = null)
    {
        $keyBase = $subscriberUrl . $topicUrl . $type;
        $key = sha1($keyBase);
        return $key;
    }

    /**
     * URL Encode an array of parameters
     *
     * @param array $params
     * @return array
     */
    protected function _urlEncode(array $params)
    {
        $encoded = array();
        foreach ($params as $key => $value) {
            if (is_array($value)) {
                $ekey = urlencode($key);
                $encoded[$ekey] = array();
                foreach ($value as $duplicateKey) {
                    $encoded[$ekey][] = urlencode($duplicateKey);
                }
            } else {
                $encoded[urlencode($key)] = urlencode($value);
            }
        }
        return $encoded;
    }

    /**
     * Order outgoing parameters
     *
     * @param array $params
     * @return array
     */
    protected function _toByteValueOrderedString(array $params)
    {
        $return = array();
        uksort($params, 'strnatcmp');
        foreach ($params as $key => $value) {
            if (is_array($value)) {
                /**
                 * We skip sorting values simply because per spec, the order
                 * of these values imposes an order of preference we should
                 * not interfere with.
                 */
                //natsort($value);
                foreach ($value as $keyduplicate) {
                    $return[] = $key . '=' . $keyduplicate;
                }
            } else {
                $return[] = $key . '=' . $value;
            }
        }
        return implode('&', $return);
    }

    /**
     * This STRICTLY for testing purposes only...
     */
    protected $_testStaticToken = null;
    final public function setTestStaticToken($token)
    {
        $this->_testStaticToken = (string) $token;
    }

}
