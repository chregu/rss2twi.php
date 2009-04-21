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
 * Services_Twitter_Statuses
 *
 * @category Services
 * @package  Services_Twitter
 * @author   Joe Stump <joe@joestump.net> 
 * @license  http://www.opensource.org/licenses/bsd-license.php New BSD License
 * @link     http://twitter.com
 */
class Services_Twitter_Statuses extends Services_Twitter_Common
{
    /**
     * Fetch a specific status message
     *
     * @param integer $id The unique numeric status ID
     *
     * @return object Instance of SimpleXMLElement of new status
     * @throws Services_Twitter_Exception
     * @see Services_Twitter_Common::sendRequest()
     */
    public function show($id)
    {
        return $this->sendRequest('/statuses/show/' . (int)$id);
    }

    /**
     * Destroy a specific status message
     *
     * @param integer $id The unique numeric status ID
     * 
     * @return object Instance of SimpleXMLElement of new status
     * @throws Services_Twitter_Exception
     * @see Services_Twitter_Common::sendRequest()
     */
    public function destroy($id)
    {
        return $this->sendRequest(
            '/statuses/destroy/' . (int)$id, array(), 'POST'
        );
    }

    /**
     * Update the Twitter status
     *
     * @param string  $status    New Twitter status
     * @param integer $inReplyTo Status ID being replied to
     * 
     * @return object Instance of SimpleXMLElement of new status
     * @throws Services_Twitter_Exception
     * @see Services_Twitter_Common::sendRequest()
     */
    public function update($status, $inReplyTo = 0)
    {
        if (!strlen($status)) {
            throw new Services_Twitter_Exception(
                'Statuses cannot be empty strings'
            );
        }

        $params = array(
            'status' => $status
        );

        if ((int)$inReplyTo > 0) {
            $params['in_reply_to_status_id'] = (int)$inReplyTo;
        }

        return $this->sendRequest('/statuses/update', $params, 'POST'); 
    }

    /**
     * Get the user's timeline
     *
     * This method was overridden from the normal 
     * Services_Twitter_Common::__call() because it can optionally take an $id
     * of another user. If the key 'id' exists in the $params array then it
     * alters the behavior of what's returned. Also, any argument other than
     * 'id', 'since' and 'page' are currently ignored.
     *
     * @param array $params Parameters array
     * 
     * @return object Instance of SimpleXMLElement of new status
     * @throws Services_Twitter_Exception
     * @see Services_Twitter_Common::sendRequest()
     */
    public function user_timeline(array $params = array())
    {
        $allowed = array('id', 'since', 'since_id', 'page');
        $tmp     = array();
        foreach ($params as $key => $val) {
            if (in_array($key, $allowed)) {
                $tmp[$key] = $val;
            }
        }

        $endPoint = '/statuses/user_timeline';
        if (isset($tmp['id'])) {
            $endPoint .= '/' . $tmp['id'];
            unset($tmp['id']);
        }

        $res = $this->sendRequest($endPoint, $tmp);
        if (!isset($res->status) || 
            (is_array($res->status) && !count($res->status))) {
            throw new Services_Twitter_Exception(
                $this->user . " has no status updates" 
            );
        }

        return $res;
    }

    /**
     * Returns up to 100 of the user's friends 
     *
     * @param array $params Parameters array
     * 
     * @return object Instance of SimpleXMLElement of new status
     * @throws Services_Twitter_Exception
     * @see Services_Twitter_Common::sendRequest()
     */
    public function friends(array $params = array())
    {
        $allowed = array('id', 'lite', 'page');
        $tmp     = array();
        foreach ($params as $key => $val) {
            if (in_array($key, $allowed)) {
                $tmp[$key] = $val;
            }
        }

        $tmp['lite'] = (isset($tmp['lite']) && 
                        $tmp['lite'] === true) ? 'true' : 'false';

        $endPoint = '/statuses/friends';
        if (isset($tmp['id']) && strlen($tmp['id'])) {
            $endPoint .= '/' . $tmp['id'];
            unset($tmp['id']);
        }

        return $this->sendRequest($endPoint, $tmp);
    }

    /**
     * Returns up to 100 of the user's followers 
     *
     * @param array $params Parameters array
     * 
     * @return object Instance of SimpleXMLElement of new status
     * @throws {@link Services_Twitter_Exception} on request problems
     * @see Services_Twitter_Common::sendRequest()
     */
    public function followers(array $params = array(), $screenName = null)
    {
        $allowed = array('lite', 'page');
        $tmp     = array();
        foreach ($params as $key => $val) {
            if (in_array($key, $allowed)) {
                $tmp[$key] = $val;
            }
        }

        $tmp['lite'] = (isset($tmp['lite']) && 
                        $tmp['lite'] === true) ? 'true' : 'false';

        if ($screenName !== null) {
            return $this->sendRequest('/statuses/followers/' . $screenName, $tmp);
        }

        return $this->sendRequest('/statuses/followers', $tmp);
    }
}

?>
