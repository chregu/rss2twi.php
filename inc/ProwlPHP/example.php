<?php

include('ProwlPHP.php');

$prowl = new Prowl('8021a193fb3d6286eb11173a549c7da47468cd1f');
$prowl->push(array(
                'application'=>'rss2twi.php',
                'event'=>'New Post',
                'description'=>'Test message! \n Sent at ' . date('H:i:s'),
                'priority'=>0,
                //'apikey'=>'APIKEY'	// Not required if already set during object construction.
                //'providerkey'=>"PROVIDERKEY'
            ),true);

var_dump($prowl->getError());	// Optional
var_dump($prowl->getRemaining()); // Optional
var_dump(date('d m Y h:i:s', $prowl->getResetdate()));	// Optional

?>
