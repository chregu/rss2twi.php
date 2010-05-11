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
 * NOTE: Mainly for testing; will add a other versions at some point using
 * Zend_Db and Zend_Cache. This is largely a simple keypair store anyway.
 * If stuck, use the interface to roll your own...
 */

/**
 * @see Zend_Feed_Pubsubhubbub_StorageInterface
 */
require_once 'Zend/Feed/Pubsubhubbub/StorageInterface.php';

/**
 * @see Zend_Uri
 */
require_once 'Zend/Uri.php';

/**
 * @category   Zend
 * @package    Zend_Feed_Pubsubhubbub
 * @copyright  Copyright (c) 2009 Padraic Brady
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Feed_Pubsubhubbub_Storage_Memory implements Zend_Feed_Pubsubhubbub_StorageInterface
{

    /**
     * Constructor; checks that apc has been loaded
     */
    public function __construct()
    {
        if (!extension_loaded('apc')) {
            require_once 'Zend/Feed/Pubsubhubbub/Exception.php';
            throw new Zend_Feed_Pubsubhubbub_Exception('The apc extension must be'
            . 'loaded to use this Storage medium');
        }
    }

    /**
     * Store data which is associated with the given Hub Server URL and Topic
     * URL and where that data relates to the given Type. The Types supported
     * include: "subscription", "unsubscription". These Type strings may also
     * be referenced by constants on the Zend_Feed_Pubsubhubbub class.
     *
     * @param string $key
     * @param string $token
     */
    public function setVerifyToken($key, $token)
    {
        if (empty($key) || !is_string($key)) {
            require_once 'Zend/Feed/Pubsubhubbub/Exception.php';
            throw new Zend_Feed_Pubsubhubbub_Exception('Invalid parameter "key"'
                .' of "' . $key . '" must be a non-empty string');
        }
        if (empty($token) || !is_string($token)) {
            require_once 'Zend/Feed/Pubsubhubbub/Exception.php';
            throw new Zend_Feed_Pubsubhubbub_Exception('Invalid parameter "token"'
                . ' must be a non-empty string');
        }
        $key = $this->_getSecureKey($key);
        apc_store($key, $token);
    }

    /**
     * Get data associated with the given key
     *
     * @param string $key
     * @return string
     */
    public function getVerifyToken($key)
    {
        if (empty($key) || !is_string($key)) {
            require_once 'Zend/Feed/Pubsubhubbub/Exception.php';
            throw new Zend_Feed_Pubsubhubbub_Exception('Invalid parameter "data"'
                .' of "' . $data . '" must be a non-empty string');
        }
        $key = $this->_getSecureKey($key);
        return apc_fetch($key);
    }

    /**
     * Checks for the existence of a record agreeing with the given key
     *
     * @param string $key
     * @return bool
     */
    public function hasVerifyToken($key)
    {
        if (empty($key) || !is_string($key)) {
            require_once 'Zend/Feed/Pubsubhubbub/Exception.php';
            throw new Zend_Feed_Pubsubhubbub_Exception('Invalid parameter "data"'
                .' of "' . $data . '" must be a non-empty string');
        }
        $key = $this->_getSecureKey($key);
        if (apc_fetch($key)) {
            return true;
        }
        return false;
    }

    /**
     * Deletes a record with the given key
     *
     * @param string $key
     */
    public function removeVerifyToken($key)
    {
        if (empty($key) || !is_string($key)) {
            require_once 'Zend/Feed/Pubsubhubbub/Exception.php';
            throw new Zend_Feed_Pubsubhubbub_Exception('Invalid parameter "data"'
                .' of "' . $data . '" must be a non-empty string');
        }
        $key = $this->_getSecureKey($key);
        apc_delete($key);
    }

    /**
     * When/If implemented: deletes all records for any given valid Type
     *
     * @param string $type
     */
    public function cleanup($type)
    {
        require_once 'Zend/Feed/Pubsubhubbub/Exception.php';
        throw new Zend_Feed_Pubsubhubbub_Exception('Not Implemented');
    }

    /**
     * Based on parameters, generate a valid one-way hashed key for a
     * store entry
     *
     * @param string $key
     * @return string
     */
    protected function _getSecureKey($key)
    {
        return preg_replace(array("/+/", "/\//", "/=/"),
            array('_', '.', ''), base64_encode(sha1($key)));
    }

}
