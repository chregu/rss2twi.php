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
 * Services_Twitter_Account
 *
 * @category Services
 * @package  Services_Twitter
 * @author   Joe Stump <joe@joestump.net> 
 * @license  http://www.opensource.org/licenses/bsd-license.php New BSD License
 * @link     http://twitter.com
 */
class Services_Twitter_Account extends Services_Twitter_Common
{
    /**
     * Verify a user's credentials
     *
     * @return boolean
     * @see Services_Twitter_Common::sendRequest()
     */
    public function verify_credentials()
    {
        $res = $this->sendRequest('/account/verify_credentials');
        return ((string)$res === 'true');
    }

    /**
     * End a person's session
     *
     * Insert long rant about Twitter's API being inconsistent. This endpoint
     * returns 'Logged out.' in plain text so we overload it and check the
     * response string from the exception inevitably thrown when it can't
     * parse the XML.
     *
     * @return boolean
     * @see Services_Twitter_Common::sendRequest()
     * @see Services_Twitter_Exception::getResponse()
     */
    public function end_session()
    {
        try {
            $res = $this->sendRequest('/account/end_session', array(), 'POST');
            return (trim(strval($res->error)) == 'Logged out.');
        } catch (Services_Twitter_Exception $e) {
            return false;
        }
    }

    /**
     * Update a user's location
     *
     * @param string $location The user's new location
     *
     * @return boolean True if the location was updated successfully
     * @see Services_Twitter_Common::sendRequest()
     * @link http://apiwiki.twitter.com/REST+API+Documentation#updatelocationnbsp
     */
    public function update_location($location)
    {
        try {
            $res = $this->sendRequest(
                '/account/update_location', array(
                    'location' => $location
                ), 'POST'
            );

            return (strval($res->location) == $location);
        } catch (Services_Twitter_Exception $e) {
            return false;
        }
    }

    /**
     * Update a user's delivery device
     *
     * @param string $device The new device (im, sms or none)
     *
     * @return object Instance of SimpleXmlElement returned from API
     * @see Services_Twitter_Common::sendRequest()
     * @link http://apiwiki.twitter.com/REST+API+Documentation#updatedeliverydevice
     */
    public function update_delivery_device($device)
    {
        if (!in_array($device, array('im', 'sms', 'none'))) {
            throw new Services_Twitter_Exception('Invalid device: ' . $device);
        }

        return $this->sendRequest(
            '/account/update_delivery_device', array(
                    'device' => $device
            ), 'POST'
        );
    }
}

?>
