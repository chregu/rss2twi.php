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
 * NOTE: Interface requires the setting of sufficient data to create a tuple
 * to uniquely identify each entry in a filename. The type is used as a postfix
 * depending on what context the Storage class implementation is being used,
 * e.g. subscription, unsubscription, etc. At a later date, if feasible, can
 * migrate to using Zend_Cache as an alternative - but this interface will
 * remain enforced.
 */

/**
 * @category   Zend
 * @package    Zend_Feed_Pubsubhubbub
 * @copyright  Copyright (c) 2009 Padraic Brady
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
interface Zend_Feed_Pubsubhubbub_StorageInterface
{

    /**
     * Store data which is associated with a Subscription. The key is the
     * unique tuple (Topic URL, Callback URL) concatenated and hashed using MD5.
     *
     * @param string $key
     * @param string $token
     */
    public function setSubscription($key, array $data);

    /**
     * Get data associated with the given key
     *
     * @param string $key
     * @return string
     */
    public function getSubscription($key);

    /**
     * Checks for the existence of a record agreeing with the given key
     *
     * @param string $key
     * @return bool
     */
    public function hasSubscription($key);

    /**
     * Deletes a record with the given key
     *
     * @param string $key
     */
    public function removeSubscription($key);

    /**
     * If implemented: deletes all records
     *
     * @param string $type
     */
    public function cleanup($type);

}
