<?php
$req_url = 'http://twitter.com/oauth/request_token';
$acc_url = 'http://twitter.com/oauth/access_token';
$authurl = 'http://twitter.com/oauth/authorize';
$api_url = 'http://twitter.com/statuses/update.json';
$conskey = 'DyhAb4DLlFmc5Wn29QvL9g';
$conssec = 'wgaBiC9YJx38sqBLklUqpkWB1Cq1ztAemp5lkfwQ';
try {
    session_start();
    
    $oauth = new OAuth($conskey,$conssec,OAUTH_SIG_METHOD_HMACSHA1,OAUTH_AUTH_TYPE_URI);
    $oauth->disableSSLChecks();
    $oauth->enableDebug();
    $request_token_info = $oauth->getRequestToken($req_url);
    
    
    $token = $request_token_info["oauth_token"];
    $secret = $request_token_info["oauth_token_secret"];
    $oauth->setToken($request_token_info["oauth_token"],$request_token_info["oauth_token_secret"]);
    
    printf("I think I got a valid request token, navigate your www client to:\n\n%s?oauth_token=%s\n\nOnce you finish authorizing, hit ENTER or INTERRUPT to exit\n\n", $authurl, $request_token_info["oauth_token"]);
    
    $in = fopen("php://stdin", "r");
    $foo =    fgets($in, 255);
    
    $access_token_info = $oauth->getAccessToken($acc_url,null,trim($foo));
    
    print "Add the following to your conf/feeds.yml\n";
    print "*****\n";
    print "            token: ".$access_token_info['oauth_token'] ."\n";
    print "            secret: ". $access_token_info['oauth_token_secret'] ."\n";
    print "*****\n";
    
    
  
} catch(OAuthException $E) {
    print_r($E);
}
?>
