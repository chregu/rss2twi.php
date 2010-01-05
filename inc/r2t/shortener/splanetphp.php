<?php

class r2t_shortener_splanetphp {

    // MAY NOT WORK FROM OUTSIDE MY NETWORK :)
    protected $apiurl = 'http://s.planet-php.org/api/txt140/';
    function __construct() {
        require_once ("HTTP/Request.php");

    }

    public function shorten($url,$text) {

        $req = new HTTP_Request($this->apiurl .'?url='. urlencode($url) ."&text=" . urlencode($text). "&maxchars=120");
        if (!PEAR::isError($req->sendRequest())) {
            return json_decode($req->getResponseBody(),true);
        }
        return "";
    }
}
