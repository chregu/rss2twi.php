<?php

class r2t {

    public $debug = true;
    protected $feeds = array();
    /**
     *
     */
    function __construct() {
        $this->init();
    }

    protected function init() {
        include_once ("sfYaml/sfYaml.class.php");
        define('R2T_TEMP_DIR', R2T_PROJECT_DIR . "/tmp/");
        if (!file_Exists(R2T_TEMP_DIR)) {
            if (!mkdir(R2T_TEMP_DIR)) {
                die("Could not create " . R2T_TEMP_DIR);
            }
        }
        $yaml = file_get_contents(R2T_PROJECT_DIR . '/conf/defaults.yml');
        $yaml .= file_get_contents(R2T_PROJECT_DIR . '/conf/feeds.yml');
        $f = sfYAML::Load($yaml);
        if ($f['feeds']) {
            $this->feeds = $f['feeds'];
        }
        $this->defaults = $f['defaults'];
    }

    public function process() {

        foreach ($this->feeds as $feedname => $options) {
            $options = $this->mergeOptionsWithDefaults($options);

            $newentries = $this->getNewEntries($feedname, $options['url']);
            $cnt = 1;
            foreach ($newentries as $guid => $e) {

                $options = $this->twit($e, $options);
                $cnt++;
                if ($cnt > $options['maxposts']) {
                    break;
                }
            }
        }
    }

    protected function mergeOptionsWithDefaults($options) {
        foreach ($this->defaults as $name => $value) {
            if (!isset($options[$name])) {
                $options[$name] = $value;
            }
        }
        return $options;

    }
    protected function twit($entry, $options) {
        include_once 'Services/Twitter.php';

        try {
            $service = new Services_Twitter($options['twitter']['user'], $options['twitter']['pass']);

            if (isset($options['shortener']) && $options['shortener'] && strlen($entry['link']) > $options['maxurllength']) {
                if (!isset($options['shortenerObject'])) {
                    $this->debug("create " . $options['shortener'] . " class");
                    include_once("r2t/shortener/".$options['shortener'].".php");
                    $classname = "r2t_shortener_".$options['shortener'];
                    $options['shortenerObject'] = new $classname();
                }
                $this->debug("shorten " . $entry['link'] ." to ");
                $entry['link'] = $options['shortenerObject']->shorten($entry['link']);
                $this->debug( "    " . $entry['link']);
            }

            $msg = $entry['title'] . " " . $entry['link'];
            if (isset($options['prefix'])) {
                $msg = $options['prefix'] . " " . $msg;
            }
            $msg = trim($msg);
            $this->debug("twit " . $msg);
            $service->statuses->update($msg);
        } catch (Exception $e) {
            print "Couldn't post " . $entry['title'] . " " . $entry['link'] . " due to " . $e->getMessage();
        }

        return $options;

    }

    protected function getNewEntries($feedname, $url) {
        $oldentries = $this->getOldEntries($feedname);
        $onlineentries = $this->getOnlineEntries($url);
        if (count($onlineentries) > 0) {
            file_put_contents(R2T_TEMP_DIR . "/$feedname", sfYaml::dump($onlineentries));
        }
        $newentries = $onlineentries;
        foreach ($onlineentries as $guid => $a) {
            if (isset($oldentries[$guid])) {
                unset($newentries[$guid]);
            } else {
                $this->debug("   New Entry: " . $a['link'] . " " . $a['title']);
            }

        }
        return $newentries;
    }

    protected function getOldEntries($feed) {
        $file = R2T_TEMP_DIR."/$feed";
        $oldentries = array();
        if (file_exists($file)) {
            $oldentries = sfYAML::Load($file);
        }
        return $oldentries;

    }

    protected function getOnlineEntries($url) {
        $feed = $this->readFeed($url);
        $this->debug("Loop through entries");
        $entries = array();
        foreach ($feed as $entry) {
            $this->debug("  " . $entry->link . ": " . $entry->guid . " " . $entry->title);
            if (isset($entry->guid)) {
                $entry->guid = $entry->link;
            }
            $e = array(
                    "link" => $entry->link,
                    "title" => $entry->title
            );
            $entries[$entry->guid] = $e;
        }
        return $entries;
    }

    protected function readFeed($url) {
        require_once ("XML/Feed/Parser.php");

        $this->debug("readFeed for $url");
        $body = $this->httpRequest($url);
        if ($body) {
            $this->debug("parse Feed");
            return new XML_Feed_Parser($body);

        } else {
            $this->debug("Feed for $url was empty");
            return array();
        }

    }

    protected function httpRequest($url) {
        require_once ("HTTP/Request.php");
        $this->debug("httpRequest for $url");

        $req = new HTTP_Request($url);
        if (!PEAR::isError($req->sendRequest())) {
            return $req->getResponseBody();
        }
        return null;

    }
    protected function debug($msg) {

        if ($this->debug) {
            if (is_string($msg)) {
                print $msg . "\n";
            } else {
                var_dump($msg);
            }
        }
    }
}
