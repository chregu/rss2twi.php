<?php

class r2t_shortener_liipto140 {

    protected $apiurl = 'http://liip.to/api/txt140/';
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
