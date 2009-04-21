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
 * Services_Twitter_DirectMessages
 *
 * @category Services
 * @package  Services_Twitter
 * @author   Joe Stump <joe@joestump.net> 
 * @license  http://www.opensource.org/licenses/bsd-license.php New BSD License
 * @link     http://twitter.com
 */
class Services_Twitter_DirectMessages extends Services_Twitter_Common
{
    /**
     * Group name
     *
     * @var string $name Overload name of API group
     * @see Services_Twitter_Common::$name
     */
    protected $name = 'direct_messages';

    /**
     * Delete/Destroy a direct message
     *
     * @param integer $id The direct message id to delete
     *
     * @return object Instance of SimpleXMLElement
     * @throws Services_Twitter_Exception
     * @see Services_Twitter_Common::sendRequest()
     */
    public function destroy($id)
    {
        if (!is_numeric($id)) {
            throw new Services_Twitter_Exception(
                $id . ' is not a valid numeric id'
            );
        }

        return $this->sendRequest(
            '/direct_messages/destroy/' . $id, array(), 'POST'
        );
    }

    /**
     * __call
     *
     * Evidently, PHP considers 'new' a reserved keyword. I really, really 
     * wanted to keep the interface the same across the board and Twitter uses
     * /direct_messages/new so Services_Twitter should use 
     * $foo->direct_messages->new(), which was causing parse errors. This is
     * an ugly hack to work around this issue.
     *
     * @param string $function The API endpoint to call
     * @param array  $args     The arguments for the API endpoint
     * 
     * @throws Services_Twitter_Exception
     * @return object Instance of SimpleXMLElement
     * @see Services_Twitter_DirectMessages::dmNew()
     * @see Services_Twitter_Common::__call()
     */
    public function __call($function, array $args = array())
    {
        switch ($function) {
        case 'new':
            if (!isset($args[0]) || !isset($args[1])) {
                throw new Services_Twitter_Exception(
                    'id and text are required when sending a direct message', 
                    Services_Twitter::ERROR_UNKNOWN
                );
            }

            return $this->dmNew($args[0], $args[1]);
        default:
            return parent::__call($function, $args);
        }
    }

    /**
     * Send a direct message
     *
     * @param string $user The id or username to send to
     * @param string $text The direct text to send
     * 
     * @return object Instance of SimpleXMLElement of new status
     * @throws Services_Twitter_Exception
     * @see Services_Twitter_Common::sendRequest()
     */
    protected function dmNew($user, $text)
    {
        if (preg_match('/[^a-z0-9_]/i', $user)) {
            throw new Services_Twitter_Exception(   
                'The user (' . $user . ') provided appears to be invalid', 
                Services_Twitter::ERROR_UNKNOWN
            );
        }

        if (!strlen($text)) {
            throw new Services_Twitter_Exception(
                'Statuses cannot be empty strings', 
                Services_Twitter::ERROR_UNKNOWN
            );
        }

        return $this->sendRequest('/direct_messages/new', array(
            'user' => $user,
            'text' => $text
        ), 'POST');
    }
}

?>
