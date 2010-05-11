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
class Zend_Feed_Pubsubhubbub_Storage_Filesystem implements Zend_Feed_Pubsubhubbub_StorageInterface
{

    /**
     * The directory to which values will be stored. If left unset, will attempt
     * to detect and use a valid writable temporary directory.
     *
     * @var string
     */
    protected $_directory = null;

    /**
     * Set the directory to which values will be stored.
     *
     * @param string $directory
     */
    public function setDirectory($directory)
    {
        if (!file_exists($directory) || !is_writable($directory)) {
            require_once 'Zend/Feed/Pubsubhubbub/Exception.php';
            throw new Zend_Feed_Pubsubhubbub_Exception('The directory "'
            . $directory . '" is not writable or does not exist and therefore'
            . ' cannot be used');
        }
        $this->_directory = rtrim($directory, '/\\');
    }

    /**
     * Get the directory to which values will be stored.
     *
     * @return string
     */
    public function getDirectory()
    {
        if ($this->_directory === null) {
            $this->_directory = sys_get_temp_dir();
        }
        return $this->_directory;
    }

    public function setSubscription($key, array $data)
    {
        if (empty($key) || !is_string($key)) {
            require_once 'Zend/Feed/Pubsubhubbub/Exception.php';
            throw new Zend_Feed_Pubsubhubbub_Exception('Invalid parameter "key"'
                .' of "' . $key . '" must be a non-empty string');
        }
        $filename = $this->_getFilename($key);
        $path = $this->getDirectory() . '/' . $filename;
        file_put_contents($path, serialize($data));
    }

    public function getSubscription($key)
    {
        if (empty($key) || !is_string($key)) {
            require_once 'Zend/Feed/Pubsubhubbub/Exception.php';
            throw new Zend_Feed_Pubsubhubbub_Exception('Invalid parameter "data"'
                .' of "' . $data . '" must be a non-empty string');
        }
        $filename = $this->_getFilename($key);
        $path = $this->getDirectory() . '/' . $filename;
        if (!file_exists($path) || !is_readable($path)) {
            return false;
        }
        $serialized = file_get_contents($path);
        if (empty($serialized)) {
            return false;
        }
        $data = unserialize($serialized);
        return $data;
    }

    public function hasSubscription($key)
    {
        if (empty($key) || !is_string($key)) {
            require_once 'Zend/Feed/Pubsubhubbub/Exception.php';
            throw new Zend_Feed_Pubsubhubbub_Exception('Invalid parameter "data"'
                .' of "' . $data . '" must be a non-empty string');
        }
        $filename = $this->_getFilename($key);
        $path = $this->getDirectory() . '/' . $filename;
        if (!file_exists($path) || !is_readable($path)) {
            return false;
        }
        return true;
    }

    public function removeSubscription($key)
    {
        if (empty($key) || !is_string($key)) {
            require_once 'Zend/Feed/Pubsubhubbub/Exception.php';
            throw new Zend_Feed_Pubsubhubbub_Exception('Invalid parameter "data"'
                .' of "' . $data . '" must be a non-empty string');
        }
        $filename = $this->_getFilename($key);
        $path = $this->getDirectory() . '/' . $filename;
        if (!file_exists($path) || !is_readable($path)) {
            return;
        }
        unlink($path);
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
    public function setToken($key, $token)
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
        $filename = $this->_getFilename($key);
        $path = $this->getDirectory() . '/' . $filename;
        file_put_contents($path, $token);
    }

    /**
     * Get data associated with the given key
     *
     * @param string $key
     * @return string
     */
    public function getToken($key)
    {
        if (empty($key) || !is_string($key)) {
            require_once 'Zend/Feed/Pubsubhubbub/Exception.php';
            throw new Zend_Feed_Pubsubhubbub_Exception('Invalid parameter "data"'
                .' of "' . $data . '" must be a non-empty string');
        }
        $filename = $this->_getFilename($key);
        $path = $this->getDirectory() . '/' . $filename;
        if (!file_exists($path) || !is_readable($path)) {
            return false;
        }
        return file_get_contents($path);
    }

    /**
     * Checks for the existence of a record agreeing with the given key
     *
     * @param string $key
     * @return bool
     */
    public function hasToken($key)
    {
        if (empty($key) || !is_string($key)) {
            require_once 'Zend/Feed/Pubsubhubbub/Exception.php';
            throw new Zend_Feed_Pubsubhubbub_Exception('Invalid parameter "data"'
                .' of "' . $data . '" must be a non-empty string');
        }
        $filename = $this->_getFilename($key);
        $path = $this->getDirectory() . '/' . $filename;
        if (!file_exists($path) || !is_readable($path)) {
            return false;
        }
        return true;
    }

    /**
     * Deletes a record with the given key
     *
     * @param string $key
     */
    public function removeToken($key)
    {
        if (empty($key) || !is_string($key)) {
            require_once 'Zend/Feed/Pubsubhubbub/Exception.php';
            throw new Zend_Feed_Pubsubhubbub_Exception('Invalid parameter "data"'
                .' of "' . $data . '" must be a non-empty string');
        }
        $filename = $this->_getFilename($key);
        $path = $this->getDirectory() . '/' . $filename;
        if (!file_exists($path) || !is_readable($path)) {
            return;
        }
        unlink($path);
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
     * Based on parameters, generate a valid one-way hashed filename for a
     * store entry
     *
     * @param string $hubUrl The Hub Server URL
     * @param string $topicUrl The Topic (RSS or Atom feed) URL
     * @param string $type
     * @return string
     */
    protected function _getFilename($key)
    {
        return preg_replace(array("/\+/", "/\//", "/\=/"),
            array('_', '.', ''), base64_encode(sha1($key)));
    }

}
