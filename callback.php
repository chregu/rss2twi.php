<?php


    
    
$_SERVER['HTTP_HOST'] = 'v1.chregu.tv';


define("R2T_PROJECT_DIR",dirname(__FILE__));
ini_set("include_path",R2T_PROJECT_DIR."/inc/:/var/www/zf-trunk/:".ini_get("include_path"));



require_once( "Zend/Feed/Pubsubhubbub/Storage/Filesystem.php");

$storage = new Zend_Feed_Pubsubhubbub_Storage_Filesystem;
$storage->setDirectory(R2T_PROJECT_DIR."/tmp");

require_once( "Zend/Feed/Pubsubhubbub/Subscriber/Callback.php");

$callback = new Zend_Feed_Pubsubhubbub_Subscriber_Callback;
$callback->setStorage($storage);

$token = substr($_SERVER['PATH_INFO'],1);


$callback->setSubscriptionKey($token);

$callback->handle();

if ($callback->hasFeedUpdate() || isset($_GET['doit']) ) {
    $data = $callback->getFeedUpdate();
    
    $key = md5($data);
    file_put_contents(R2T_PROJECT_DIR . '/tmp/updates/' . $key, $data);
    $command = "/usr/bin/php  /home/chregu/rss2twi.php/rss2twitter.php";
    $pcommand = $command . ' > /dev/null &';
    pclose(popen($pcommand, 'r'));
    /*  $this->_helper->getHelper('Spawn')
    ->setScriptPath(APPLICATION_ROOT . '/scripts/zfrun.php');
    $this->_helper->spawn(
    array('--key'=>$key), 'process', 'callback'
    );*/
}

$callback->sendResponse();
