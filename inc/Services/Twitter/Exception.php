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

require_once 'PEAR/Exception.php';

/**
 * Services_Twitter_Exception
 *
 * @category Services
 * @package  Services_Twitter
 * @author   Joe Stump <joe@joestump.net> 
 * @license  http://www.opensource.org/licenses/bsd-license.php New BSD License
 * @link     http://twitter.com
 */
class Services_Twitter_Exception extends PEAR_Exception
{
    /**
     * Call to the API that created the error
     *
     * @var string $call 
     */
    protected $call = '';

    /**
     * The raw response returned by the API
     *
     * @var string $response
     */
    protected $response = '';

    /**
     * Constructor
     *
     * @param string  $message  Error message
     * @param integer $code     Error code
     * @param string  $call     API call that generated error
     * @param string  $response The raw response that produced the erorr
     *
     * @see Services_Twitter_Exception::$call
     * @link http://php.net/exceptions
     */
    public function __construct($message = null, 
                                $code = 0, 
                                $call = '',
                                $response = '') 
    {
        parent::__construct($message, $code);
        $this->call     = $call;
        $this->response = $response;
    }

    /**
     * Return API call
     *
     * @return string
     * @see Services_Twitter_Exception::$call
     */
    public function getCall()
    {
        return $this->call;
    }

    /**
     * Get the raw API response that died   
     *
     * @return string
     * @see Services_Twitter_Exception::$response
     */
    public function getResponse()
    {
        return $this->response;
    }

    /**
     * __toString
     *
     * Overload PEAR_Exception's horrible __toString implementation.
     *
     * @return      string
     */
    public function __toString()
    {
        return $this->message . ' (Code: ' . $this->code . ', Call: ' . 
               $this->call . ')';
    }
}

?>
