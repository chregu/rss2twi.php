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

/**
 * Services_Twitter_Search
 *
 * @category Services
 * @package  Services_Twitter
 * @author   Joe Stump <joe@joestump.net> 
 * @license  http://www.opensource.org/licenses/bsd-license.php New BSD License
 * @link     http://twitter.com
 * @link     http://apiwiki.twitter.com/Search+API+Documentation
 */
class Services_Twitter_Search extends Services_Twitter_Common
{
    /**
     * The search API's URI
     *
     * @var string $uri The search API's URI
     * @link http://apiwiki.twitter.com/Search+API+Documentation
     */
    static private $uri = 'http://search.twitter.com';

    /**
     * Constructor
     *
     * @see Services_Twitter_Common::sendRequest()
     * @see Services_Twitter_Common::__construct()
     * @return void
     */
    public function __construct()
    {
        // Disables authentication in sendRequest()
        $this->user = $this->pass = null;
    }

    /**
     * Get search trends
     *
     * @return object The top ten trends on Twitter
     * @see Services_Twitter_Search::sendRequest()
     * @throws {@link Services_Twitter_Exception} on API/HTTP errors
     */
    public function trends()
    {
        return $this->sendRequest('/trends');
    }

    /**
     * Query the search API
     *
     * @param string $query The full query to send
     * 
     * @throws {@link Services_Twitter_Exception} on API/HTTP errors
     * @return object The results of the query according to the API
     * @see Services_Twitter_Search::sendRequest()
     */
    public function query($query)
    {
        return $this->sendRequest('/search', array('q' => $query));
    }

    /**
     * Send a special request to Search API
     *
     * @param string $endPoint The search API endpoint minus extension
     * @param array  $params   An array of parameters
     *
     * @throws {@link Services_Twitter_Exception} on API/HTTP errors
     * @return object Instance of stdClass of JSON response
     * @see Services_Common::sendRequest()
     */
    protected function sendRequest($endPoint, array $params = array())
    {
        $endPoint = self::$uri . $endPoint . '.' . 
                    Services_Twitter::OUTPUT_JSON;
        
        return parent::sendRequest(
                $endPoint, $params, 'GET', Services_Twitter::OUTPUT_JSON
        );
    }
}

?>
