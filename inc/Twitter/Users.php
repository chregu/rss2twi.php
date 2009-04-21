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
 * Services_Twitter_Exception
 *
 * @category Services
 * @package  Services_Twitter
 * @author   Joe Stump <joe@joestump.net> 
 * @license  http://www.opensource.org/licenses/bsd-license.php New BSD License
 * @link     http://twitter.com
 */
class Services_Twitter_Users extends Services_Twitter_Common
{
    /**
     * Show extended information for a given user
     *
     * This will return the extended information of a given user. This 
     * information includes design settings, so third party developers can 
     * theme their widgets according to a given user's preferences.
     *
     * The parameter passed can be the user ID (integer) of the user, their
     * username (string) or the user's email address (string). NOTE: Strict
     * validation on email addresses is NOT done in this package.
     *
     * @param mixed $id_or_email Integer user ID, username or email address 
     * 
     * @return object Instance of SimpleXMLElement
     * @throws Services_Twitter_Exception
     * @see Services_Twitter_Common::sendRequest()
     */
    public function show($id_or_email)
    {
        $params = array();

        /** 
         * This is by far not the most robust email checker here, but the 
         * API will return an error if it's invalid and I don't want to base
         * this package on Validate, which isn't PHP5.
         */
        if (strpos($id_or_email, '@')) {
            $endPoint        = '/users/show';
            $params['email'] = $id_or_email;
        } else {
            $endPoint = '/users/show/' . $id_or_email;
        }

        return $this->sendRequest($endPoint, $params);
    }
}

?>
