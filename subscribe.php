<?php
$_SERVER['HTTP_HOST'] = 'v1.chregu.tv';


define("R2T_PROJECT_DIR",dirname(__FILE__));
ini_set("include_path",R2T_PROJECT_DIR."/inc/:/var/www/zf-trunk/:".ini_get("include_path"));

require_once 'Zend/Feed/Reader.php';


//$topic = 'http://chregu.tv/blog/atom.xml';
$topic = 'http://www.planet-php.net/atom/';
$feed = Zend_Feed_Reader::import($topic);

$feedTopicUri = $feed->getFeedLink();

/**
* The feed may advertise one or more Hub Endpoints we can use.
* We may subscribe to the Topic using one or more of the Hub
* Endpoints advertised (good idea in case a Hub goes down).
*/
$feedHubs = $feed->getHubs();

/**
* Carry out subscription operation...
*/

require_once( "Zend/Feed/Pubsubhubbub/Storage/Filesystem.php");
$storage = new Zend_Feed_Pubsubhubbub_Storage_Filesystem;
$storage->setDirectory(R2T_PROJECT_DIR."/tmp");
$options = array(
'topicUrl' => $feedTopicUri,
'hubUrls' => $feedHubs,
'storage' => $storage,
'callbackUrl' => 'http://'.$_SERVER['HTTP_HOST'].'/rss2twi/callback.php',
'usePathParameter' => true,
'authentications' => array(
)
);
require_once( "Zend/Feed/Pubsubhubbub/Subscriber.php");

$subscriber = new Zend_Feed_Pubsubhubbub_Subscriber($options);
$subscriber->subscribeAll();
/**
* Do some checking for errors...
*/
if (!$subscriber->isSuccess()) {
    var_dump($subscriber->getErrors()); exit;
print "<br/>ERROR";
} else {

	error_log("SEEMS TO BE OK");
}

