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
require_once 'Validate.php';

/**
 * Services_Twitter_Common
 *
 * @category Services
 * @package  Services_Twitter
 * @author   Joe Stump <joe@joestump.net> 
 * @license  http://www.opensource.org/licenses/bsd-license.php New BSD License
 * @link     http://twitter.com
 */
abstract class Services_Twitter_Common
{
    /**
     * Name of call group
     *
     * @var string $name Used to overload class name for groupings
     * @see Services_Twitter_Common::sendRequest()
     */
    protected $name = null;

    /**
     * Username of Twitter user
     *
     * @var string $user Twitter username
     */
    protected $user = '';

    /**
     * Password of Twitter user
     *
     * @var string $pass User's password for Twitter
     */
    protected $pass = '';

    /**
     * Options for HTTP requests and misc. 
     *
     * - timetout 
     *
     * @access protected
     * @var array $options An array of various options
     */
    protected $options = array(
        'timeout' => 30,
        'userAgent' => 'Services_Twitter 0.2.0'
    );

    /**
     * Constructor
     *
     * @param string $user Twitter username
     * @param string $pass Twitter password
     *
     * @return void
     */
    public function __construct($user, $pass)
    {
        $this->user = $user;
        $this->pass = $pass;
    }

    /**
     * Set an option in {@link Services_Twitter_Common::$options}
     *
     * If a function exists named _set$option (e.g. _setUserAgent()) then that
     * method will be used instead. Otherwise, the value is set directly into
     * the options array.
     *
     * @param string $option Name of option
     * @param mixed  $value  Value of option
     *
     * @throws InvalidArgumentException on invalid option names
     * @see Services_Twitter_Common::$options
     * @return void
     */
    public function setOption($option, $value)
    {
        if (!is_string($option)) {
            throw new InvalidArgumentException('Option names must be strings');
        }

        $func = '_set' . ucfirst($option);
        if (method_exists($this, $func)) {
            $this->$func($value);
        } else {
            $this->options[$option] = $value;
        } 
    }

    /**
     * Set a number of options at once
     *
     * @param array $options The options to set
     *
     * @return void
     * @see Services_Twitter_Common::setOption()
     */
    public function setOptions(array $options)
    {
        foreach ($options as $option => $value) {
            $this->setOption($option, $value);
        }
    }

    /**
     * Send a request to the Twitter API
     *
     * @param string $endPoint The API endpoint WITHOUT the extension
     * @param array  $params   The API endpoint arguments to pass
     * @param string $method   Whether to use GET or POST 
     *
     * @throws Services_Twitter_Exception
     * @return object Instance of SimpleXMLElement 
     */
    protected function sendRequest($endPoint, 
                                   array $params = array(),
                                   $method = 'GET', 
                                   $output = Services_Twitter::OUTPUT_XML)
    {
        // If the $endPoint is a valid URI then we use that instead of using
        // the base URI.
        if (Validate::uri($endPoint, 
                          array('allowed_schemes' => array('http')))) {
            $uri = $endPoint;
        } else {
            $uri = Services_Twitter::$uri . $endPoint . '.xml';    
        } 

        if ($method != 'GET' && $method != 'POST') {
            throw new Services_Twitter_Exception(
                'Unsupported method: ' . $method
            );
        }

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_USERAGENT, $this->options['userAgent']);
        curl_setopt($ch, CURLOPT_HEADER, false);

        // If user and pass are not set then we are using endpoints that do
        // not require authentication.
        if (isset($this->user) && isset($this->pass)) {
            curl_setopt($ch, CURLOPT_USERPWD, $this->user . ':' . $this->pass);
        }

        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $this->options['timeout']);

        // You can set a source in $params for most requests or via the
        // setOption() method.
        if (!isset($params['source'])) {
            if (isset($this->options['source'])) {
                $params['source'] = $this->options['source'];
            }
        }

        $sets = array();
        foreach ($params as $key => $val) {
            $sets[] = $key . '=' . urlencode($val);
        }

        if ($method == 'POST') {
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, implode('&', $sets));
        } else {
            if (count($sets)) {
                $uri .= '?' . implode('&', $sets);
            }
        }

        curl_setopt($ch, CURLOPT_URL, $uri);
        $res = trim(curl_exec($ch));

        $err = curl_errno($ch);
        if ($err !== CURLE_OK) {
            throw new Services_Twitter_Exception(curl_error($ch), $err, $uri);
        }

        $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        if (substr($code, 0, 1) != '2') {
            $xml = @simplexml_load_string($res);
            if ($xml instanceof SimpleXMLElement && isset($xml->error)) {
                throw new Services_Twitter_Exception(
                    (string)$xml->error, Services_Twitter::ERROR_UNKNOWN, $uri
                );
            }

            throw new Services_Twitter_Exception(
                'Unexpected HTTP status returned from API', $code, $uri
            );
        }

        curl_close($ch);

        if (!strlen($res)) {
            throw new Services_Twitter_Exception(
                'Empty response was received from the API', 
                Services_Twitter::ERROR_UNKNOWN, $uri
            );
        }

        switch ($output) {
        case Services_Twitter::OUTPUT_XML:
            $response = @simplexml_load_string($res);
            if (!$response instanceof SimpleXMLElement) {
                throw new Services_Twitter_Exception(
                    'Could not parse XML response received by the API', 
                    Services_Twitter::ERROR_UNKNOWN, $uri, $res
                );
            }
            break;
        case Services_Twitter::OUTPUT_JSON:
            $response = @json_decode($res);
            if (!$response instanceof stdClass) {
                throw new Services_Twitter_Exception(
                    'Could not parse JSON response received by the API', 
                    Services_Twitter::ERROR_UNKNOWN, $uri, $res
                );
            }
            break;
        } 

        return $response;
    }

    /**
     * Overloaded call for API passthrough
     *
     * Takes the function called and magically creates an API endpoint based
     * on the class name / grouping name and the function name. For instance,
     * a call to $twitter->statuses->followers() would call the API endpoint
     * '/statuses/followers.xml' and return the results.
     * 
     * @param string $function API endpoint being called
     * @param array  $args     $args[0] is an array of GET/POST arguments
     * 
     * @return object Instance of SimpleXMLElement
     * @see Services_Twitter_Common::sendRequest()
     * @see Services_Twitter_Common::$name
     */
    public function __call($function, array $args = array())
    {
        if (isset($args[0]) && is_array($args[0]) && count($args[0])) {
            $params = $args[0];
        } else {
            $params = array();
        }

        if (!is_null($this->name)) {
            $endPoint = '/' . $this->name . '/' . $function;
        } elseif (get_class($this) != 'Services_Twitter') {
            $name     = strtolower(array_pop(explode('_', get_class($this))));
            $endPoint = '/' . $name . '/' . $function;
        } else {
            $endPoint = '/' . $function;
        }

        return $this->sendRequest($endPoint, $params);
    }
}

?>
