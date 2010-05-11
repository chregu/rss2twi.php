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
 * @category   Zend
 * @package    Zend_Feed_Pubsubhubbub
 * @copyright  Copyright (c) 2009 Padraic Brady
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Feed_Pubsubhubbub_HubServer
{

    /**
     * The Callback URL to be used by Publishers and Subscribers
     *
     * @var string
     */
    protected $_callbackUrl = '';

    /**
     * Constructor; accepts an array or Zend_Config instance to preset
     * options for the Hub Server without calling all supported setter
     * methods in turn.
     *
     * @param array|Zend_Config $options Options array or Zend_Config instance
     */
    public function __construct($config = null)
    {
        if (!is_null($config)) {
            $this->setConfig($config);
        }
    }

    /**
     * Process any injected configuration options
     *
     * @param array|Zend_Config $options Options array or Zend_Config instance
     */
    public function setConfig($config)
    {
        if ($config instanceof Zend_Config) {
            $config = $config->toArray();
        } elseif (!is_array($config)) {
            require_once 'Zend/Feed/Pubsubhubbub/Exception.php';
            throw new Zend_Feed_Pubsubhubbub_Exception('Array or Zend_Config object'
            . 'expected, got ' . gettype($config));
        }
        if (array_key_exists('callbackUrl', $config)) {
            $this->setCallbackUrl($config['callbackUrl']);
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

}
