<?php

/**
 * An interface for Twitter's HTTP API
 *
 * PHP version 5.1.0+
 *
 * Copyright (c) 2007, The PEAR Group
 * 
 * All rights reserved.
 * 
 * Redistribution and use in source and binary forms, with or without 
 * modification, are permitted provided that the following conditions are met:
 *
 *  - Redistributions of source code must retain the above copyright notice,
 *    this list of conditions and the following disclaimer.
 *  - Redistributions in binary form must reproduce the above copyright notice,
 *    this list of conditions and the following disclaimer in the documentation
 *    and/or other materials provided with the distribution.
 *  - Neither the name of the The PEAR Group nor the names of its contributors 
 *    may be used to endorse or promote products derived from this software 
 *    without specific prior written permission.
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS" 
 * AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE 
 * IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE 
 * ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT OWNER OR CONTRIBUTORS BE 
 * LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR 
 * CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF 
 * SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS 
 * INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN 
 * CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE) 
 * ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE 
 * POSSIBILITY OF SUCH DAMAGE.
 *
 * @category  Services
 * @package   Services_Twitter
 * @author    Joe Stump <joe@joestump.net> 
 * @copyright 1997-2007 Joe Stump <joe@joestump.net> 
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD License
 * @version   Release: 0.2.0
 * @link      http://twitter.com/help/api
 * @link      http://twitter.com
 */

require_once 'Services/Twitter/Exception.php';
require_once 'Services/Twitter/Common.php';

/**
 * Services_Twitter
 *
 * <code>
 * <?php
 * require_once 'Services/Twitter.php';
 *
 * $username = 'You_Username';
 * $password = 'Your_Password';
 *
 * try {
 *     $twitter = new Services_Twitter($username, $password);
 *     $msg = $twitter->statuses->update("I'm coding with PEAR right now!");
 *     print_r($msg); // Should be a SimpleXMLElement structure
 * } catch (Services_Twitter_Exception $e) {
 *     echo $e->getMessage(); 
 * }
 * ?>
 * </code>
 *
 * @category Services
 * @package  Services_Twitter
 * @author   Joe Stump <joe@joestump.net> 
 * @license  http://www.opensource.org/licenses/bsd-license.php New BSD License
 * @link     http://twitter.com
 */
class Services_Twitter extends Services_Twitter_Common
{
    /**
     * Twitter API error codes
     *
     * @global integer ERROR_UNKNOWN     An unknown error occurred
     * @global integer ERROR_REQUEST     Bad request sent
     * @global integer ERROR_AUTH        Not authorized to do action
     * @global integer ERROR_FORBIDDEN   Forbidden from doing action
     * @global integer ERROR_NOT_FOUND   Item requested not found
     * @global integer ERROR_INTERNAL    Internal Twitter error
     * @global integer ERROR_DOWN        Twitter is down
     * @global integer ERROR_UNAVAILABLE API is overloaded
     */
    const ERROR_UNKNOWN     = 1;
    const ERROR_REQUEST     = 400;
    const ERROR_AUTH        = 401;
    const ERROR_FORBIDDEN   = 403;
    const ERROR_NOT_FOUND   = 404;
    const ERROR_INTERNAL    = 500;
    const ERROR_DOWN        = 502;
    const ERROR_UNAVAILABLE = 503;

    /**
     * Twitter API output parsing options
     *
     * @global string OUTPUT_XML  The response is expected to be XML
     * @global string OUTPUT_JSON The response is expected to be JSON
     */
    const OUTPUT_XML  = 'xml';
    const OUTPUT_JSON = 'json';

    /**
     * Public URI of Twitter's API
     *
     * @var         string      $uri        URI of Twitter API
     * @see         Services_Twitter_Common::sendRequest()
     */
    static public $uri = 'http://twitter.com';

    /**
     * Supported areas / methods of Twitter's API
     *
     * @var         array       $methods
     * @see         Services_Twitter::__get()
     */
    static protected $methods = array(
        'account'         => 'Account',
        'direct_messages' => 'DirectMessages',
        'favorites'       => 'Favorites',
        'friendships'     => 'Friendships',
        'notifications'   => 'Notifications',
        'statuses'        => 'Statuses',
        'users'           => 'Users',
        'search'          => 'Search'
    );

    /**
     * Instances of Twitter methods
     *
     * @var         array       $instances
     * @see         Services_Twitter::__get(), Services_Twitter::$methods
     */
    protected $instances = array();

    /**
     * Lazily load Twitter API drivers
     *
     * @param string $var Method to load
     * 
     * @throws Services_Twitter_Exception
     * @return object Instance of API driver
     * @see Services_Twitter::factory()
     */
    public function __get($var)
    {
        if (!isset(self::$methods[$var])) {
            throw new Services_Twitter_Exception(
                'Method (' . $var . ') is not implemented'
            );
        }

        return $this->factory(self::$methods[$var]);
    }

    /**
     * Instantiate API driver
     *
     * @param string $method API driver to load
     * 
     * @return object Instance of API driver
     */
    protected function factory($method)
    {
        if (isset($this->instances[$method])) {
            return $this->instances[$method];
        }

        $file = 'Services/Twitter/' . $method . '.php';
        include_once $file;
        
        $class = 'Services_Twitter_' . $method;
        $this->instances[$method] = new $class($this->user, $this->pass);
        return $this->instances[$method];
    }
}

?>
